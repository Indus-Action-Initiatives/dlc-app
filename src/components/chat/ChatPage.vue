<template>

    <!-- Top AppBar -->
    <BackButtonAppBar />

    <!-- Main content -->

    <v-container fluid class="pa-0" style="height: calc(100vh - 64px - 56px);">
        <!-- 64px for AppBar, 56px for bottom nav (adjust if different) -->
        <v-row style="height: 100%; overflow: hidden;">
            <!-- Sidebar -->
            <v-col cols="4" class="chat-sidebar pa-2" style="border-right:1px solid #ddd; overflow-y: auto;">
                <chat-sidebar :chats="chats" :selectedChatId="selectedChatId" @selectChat="openChat" />
            </v-col>

            <!-- Chat Window -->
            <v-col cols="8" class="chat-window pa-2" style="display: flex; flex-direction: column; height: 100%;">
                <chat-window v-if="activeChat" :chat="activeChat" :currentUser="currentUser" @sendMessage="sendMessage"
                    :broadcastMode="broadcastMode" @sendBroadcast="sendBroadcast" />
                <div v-else class="text-center grey--text mt-5">
                    Select User to start messaging
                </div>
            </v-col>
        </v-row>
    </v-container>

</template>

<script setup>
import { ref, computed } from 'vue';
import BackButtonAppBar from '@/components/header/BackButtonAppBar.vue';
import ChatSidebar from '@/components/chat/ChatSidebar.vue';
import ChatWindow from '@/components/chat/ChatWindow.vue';
import { useRoute } from 'vue-router';

const route = useRoute();

const chatId = ref(route.params.id || null);

const currentUser = ref({ id: 1, type: 'worker', name: 'Worker A' });

const chats = ref([
    { id: 1, name: 'Employer 1', lastMessage: 'Hello!', unread: 2, messages: [] },
    { id: 2, name: 'Employer 2', lastMessage: 'Please apply', unread: 0, messages: [] },
]);

const selectedChatId = ref(null);
const activeChat = computed(() => chats.value.find(c => c.id === selectedChatId.value));
const broadcastMode = ref(false);

function openChat(chatId) {
    selectedChatId.value = chatId;
    broadcastMode.value = false;
}

function sendMessage(message) {
    if (activeChat.value) {
        activeChat.value.messages.push({
            id: Date.now(),
            text: message,
            senderId: currentUser.value.id,
            timestamp: new Date().toISOString(),
        });
        activeChat.value.lastMessage = message;
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

.v-main {

    padding-bottom: 0px !important;
}
</style>
