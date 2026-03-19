<template>

    <div class="chat-window-content">
        <!-- Messages area -->
        <div class="messages" ref="messagesContainer">
            <div v-for="msg in chat.messages" :key="msg.id"
                :class="['message', msg.senderId === currentUser.id ? 'sent' : 'received']">
                {{ msg.text }}
            </div>
        </div>

        <!-- Input area -->
        <div class="chat-input">
            <v-text-field v-model="messageText" label="Type a message..." outlined dense hide-details
                @keyup.enter="handleSend" class="flex-grow-1" />
            <v-checkbox v-if="currentUser.type === 'employer'" v-model="localBroadcastMode" label="Broadcast to all"
                class="mt-1" dense hide-details />
            <v-btn color="orange" @click="handleSend" class="mt-2">
                Send
            </v-btn>
        </div>
    </div>
</template>

<script setup>
import { ref, nextTick, watch } from 'vue'


const props = defineProps({
    chat: Object,
    currentUser: Object,
    broadcastMode: Boolean
})
const emit = defineEmits(['sendMessage', 'sendBroadcast'])

const messageText = ref('')
const localBroadcastMode = ref(props.broadcastMode)
const messagesContainer = ref(null)

// Scroll to bottom
function scrollToBottom() {
    if (messagesContainer.value) {
        messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
    }
}

function handleSend() {
    if (!messageText.value.trim()) return

    if (props.currentUser.type === 'employer' && localBroadcastMode.value) {
        emit('sendBroadcast', messageText.value)
    } else {
        emit('sendMessage', messageText.value)
    }

    messageText.value = ''

    nextTick(() => scrollToBottom())
}

// Automatically scroll to bottom when messages change
watch(
  () => props.chat.messages,
  async () => {
    await nextTick();
    scrollToBottom();
  },
  { deep: true }
);
</script>

<style scoped>
.chat-window-content {
    display: flex;
    flex-direction: column;
    height: 100%;
}

.messages {
    flex-grow: 1;
    overflow-y: auto;
    padding: 10px;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    /* start messages from bottom */
}

.message {
    margin-bottom: 8px;
    padding: 6px 10px;
    border-radius: 12px;
    max-width: 70%;
    word-break: break-word;
}

.sent {
    background-color: #dcf8c6;
    align-self: flex-end;
}

.received {
    background-color: #ffffff;
    align-self: flex-start;
}

.chat-input {
    display: flex;
    flex-direction: column;
    padding: 8px;
    border-top: 1px solid #ddd;
    background-color: #f5f5f5;
}
</style>
