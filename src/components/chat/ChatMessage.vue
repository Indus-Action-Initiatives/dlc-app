<template>
    <div :class="[
        'd-flex mb-2',
        message.senderId === currentUser.id ? 'justify-end' : 'justify-start'
    ]">
        <v-chip :color="message.senderId === currentUser.id ? 'orange lighten-2' : 'grey lighten-3'"
            :text-color="message.senderId === currentUser.id ? 'black' : 'black'" class="ma-0" style="max-width: 70%">
            {{ message.text }}
            <div class="text-caption grey--text float-right">{{ formatTime(message.timestamp) }}</div>
        </v-chip>
    </div>
</template>

<script setup>
const props = defineProps({
    message: Object,
    currentUser: Object
});

function formatTime(ts) {
    const d = new Date(ts);
    return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}
</script>
