<template>

    <!-- Top AppBar -->
    <BackButtonAppBar />

    <!-- Main content -->

    <v-container fluid class="pa-0" style="height: calc(100vh - 64px - 56px);">
        <!-- 64px for AppBar, 56px for bottom nav (adjust if different) -->
        <v-row style="height: 100%; overflow: hidden;">
            <!-- Sidebar -->
            <v-col cols="4" :class="['chat-sidebar', 'pa-2', 'chat-sidebar-col', { 'hide-on-mobile': !!activeChat }]"
                style="border-right:1px solid #ddd; overflow-y: auto;">
                <chat-sidebar :chats="chats" :selectedChatId="selectedChatId" @selectChat="openChat" />
            </v-col>

            <!-- Chat Window -->
            <v-col cols="8" class="chat-window pa-0 chat-window-col"
                style="display: flex; flex-direction: column; height: 100%;">
                <template v-if="activeChat">
                    <div class="chat-header">
                        <v-toolbar density="compact" color="orange-lighten-5" flat>
                            <v-btn variant="text" class="d-sm-none" @click="closeChatOnMobile">
                                <v-icon>mdi-arrow-left</v-icon>
                            </v-btn>
                            <v-toolbar-title class="text-subtitle-1 font-weight-medium">
                                {{ activeChat.name }}
                            </v-toolbar-title>
                            <v-spacer />
                        </v-toolbar>
                    </div>

                    <div class="chat-body">
                        <chat-window :chat="activeChat" :currentUser="currentUser" @sendMessage="sendMessage"
                            :broadcastMode="broadcastMode" @sendBroadcast="sendBroadcast" />
                    </div>
                </template>

                <div v-else class="text-center grey--text mt-5">
                    No chat loaded.
                </div>
            </v-col>
        </v-row>
    </v-container>

</template>

<script setup>
import { ref, computed, watch, onUnmounted } from 'vue';
import BackButtonAppBar from '@/components/header/BackButtonAppBar.vue';
import ChatSidebar from '@/components/chat/ChatSidebar.vue';
import ChatWindow from '@/components/chat/ChatWindow.vue';
import { useRoute } from 'vue-router';
import { collection, doc, onSnapshot, orderBy, query } from "firebase/firestore";
import { db } from "@/services/firebase.js";
import { ensureThread, makeThreadId, sendThreadMessage } from "@/services/chatService.js";
import { listenToMessages } from "@/services/chatService";

const route = useRoute();

const chatId = ref(route.params.id || null);
const chatName = ref(route.query.name || null);
const currentUser = ref(getCurrentUser());
const currentUserKey = computed(() => `${currentUser.value.type}:${currentUser.value.id}`);

// Chat list should come from API. For now we only create/open the chat passed via route.
const chats = ref([]);

const selectedChatId = ref(null);
const activeChat = computed(() =>
    chats.value.find(c => String(c.id) === String(selectedChatId.value))
);
const broadcastMode = ref(false);
const activeThreadId = ref(null);
let unsubscribeMessages = null;

function getCurrentUser() {
    try {
        const raw = localStorage.getItem('labour_currentUser');
        const userData = raw ? JSON.parse(raw) : null;
        const worker = userData?.worker;
        const profile = userData?.profile;
        const employer = userData?.employer;
        if (worker?.id) return { id: worker.id, type: 'worker', name: profile?.name || 'Worker' };
        if (employer?.id) return { id: employer.id, type: 'employer', name: profile?.name || 'Employer' };
    } catch (e) {
        // ignore
    }
    return { id: 1, type: 'worker', name: 'Worker' };
}

function ensureChatExistsAndSelect(id, name) {
    if (!id) return;
    const normalizedId = String(id);

    let existing = chats.value.find(c => String(c.id) === normalizedId);
    if (!existing) {
        existing = { id: normalizedId, name: name || 'Chat', lastMessage: '', unread: 0, messages: [] };
        chats.value.unshift(existing);
    } else if (name && existing.name !== name) {
        existing.name = name;
    }

    openChat(normalizedId);
}

function openChat(chatId) {
    selectedChatId.value = chatId;
    broadcastMode.value = false;
}

function closeChatOnMobile() {
    selectedChatId.value = null;
}

async function sendMessage(message) {
    if (!activeChat.value || !activeThreadId.value) return;

    try {
        await sendThreadMessage({
            threadId: activeThreadId.value,
            text: message,
            senderId: currentUserKey.value,
        });

        // 🔥 Trigger push
        const receiverId = activeChat.value.id;

        await fetch(`${import.meta.env.VITE_API_BASE_URL}/send-chat-push-notification`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                receiverId: receiverId,
                text: message,
                senderId: currentUserKey.value,
                senderName: currentUser.value.name,
            }),
        });  
    } catch (err) {
        console.error("Error sending push notification:", err);
    }
}

function sendBroadcast(message) {
    chats.value.forEach(chat => {
        chat.messages.push({
            id: Date.now() + Math.random(),
            text: message,
            senderId: currentUser.value.id,
            timestamp: new Date().toISOString(),
        });
        chat.lastMessage = message;
    });
}

watch(
    () => [route.params.id, route.query.name],
    ([newId, newName]) => {
        chatId.value = newId || null;
        chatName.value = newName || null;
        ensureChatExistsAndSelect(chatId.value, chatName.value);
    },
    { immediate: true }
);

// If user lands on /chat without id, don't show the misleading "select user" state on mobile.
// (Once chat list is wired to API, sidebar selection can be enabled again.)

watch(
    () => activeChat.value?.id,
    async (otherUserId) => {
        if (unsubscribeMessages) {
            unsubscribeMessages();
            unsubscribeMessages = null;
        }
        activeThreadId.value = null;

        if (!otherUserId) return;

        let otherUserKey = String(otherUserId);

        // 🔥 Normalize: only add prefix if missing
        if (!otherUserKey.includes(":")) {
            otherUserKey = currentUser.value.type === "worker"
                ? `employer:${otherUserKey}`
                : `worker:${otherUserKey}`;
        }
        if (otherUserKey === currentUserKey.value) {
            console.warn("⚠️ Same user chat prevented");
            return;
        }

        const threadId = makeThreadId(
            currentUserKey.value, 
            otherUserKey,
        );
        activeThreadId.value = threadId;

        await ensureThread({
            threadId,
            participants: [currentUserKey.value, otherUserKey],
            participantNames: {
                [currentUserKey.value]: currentUser.value.name,
                [otherUserKey]: activeChat.value?.name || "Chat",
            },
        });

        const threadRef = doc(db, "threads", threadId);
        const msgsQ = query(collection(threadRef, "messages"), orderBy("createdAt", "asc"));

        unsubscribeMessages = listenToMessages(threadId, (msgs) => {
            const formatted = msgs.map(m => ({
                id: m.id,
                text: m.text || "",
                senderId: m.senderId,
                timestamp: m.createdAt?.toDate
                    ? m.createdAt.toDate().toISOString()
                    : new Date().toISOString(),
            }));

            if (activeChat.value) activeChat.value.messages = formatted;
        });
    },
    { immediate: true }
);

onUnmounted(() => {
    if (unsubscribeMessages) unsubscribeMessages();
});
</script>

<style scoped>
.chat-sidebar {
    background-color: #f9f9f9;
}

.chat-window {
    display: flex;
    flex-direction: column;
}

.chat-window-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow-y: auto;
    padding-bottom: 8px;
    /* make room for input field */
}

.chat-header {
    border-bottom: 1px solid #eee;
}

.chat-body {
    flex: 1;
    min-height: 0;
    display: flex;
}

.v-main {

    padding-bottom: 0px !important;
}

/* PWA/mobile: hide sidebar when a chat is open */
@media (max-width: 600px) {
    .chat-sidebar-col { display: block; }
    .chat-window-col {
        flex: 0 0 100%;
        max-width: 100%;
    }

    .chat-sidebar-col.hide-on-mobile {
        display: none;
    }
}
</style>
