<script setup>
import { onMounted } from 'vue'
import { listenForPush } from '@/services/pushNotifications'

onMounted(() => {
  // Start listening for notifications globally on app load
  listenForPush();
});
</script>

<template>
  <router-view />
</template>