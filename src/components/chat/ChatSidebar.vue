<template>
    <v-list dense>
        <v-list-item v-for="chat in chats" :key="chat.id" :class="{ 'bg-orange lighten-5': chat.id === selectedChatId }"
            @click="$emit('selectChat', chat.id)">
            <v-list-item-content>
                <v-list-item-title class="text-subtitle-1">{{ chat.name }}</v-list-item-title>
                <v-list-item-subtitle class="grey--text text-caption">{{ chat.lastMessage }}</v-list-item-subtitle>
            </v-list-item-content>

            <v-list-item-action>
                <v-badge v-if="chat.unread > 0" color="red" content="{{ chat.unread }}" dot />
            </v-list-item-action>
        </v-list-item>
    </v-list>
</template>

<script setup>
defineProps({
    chats: Array,
    selectedChatId: [String, Number, null]
});
</script>
