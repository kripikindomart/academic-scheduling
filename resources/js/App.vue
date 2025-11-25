<template>
  <div id="app" class="min-h-screen bg-background">
    <RouterView />
  </div>
</template>

<script setup>
import { RouterView } from 'vue-router';
import { onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();

onMounted(() => {
  // Check for existing auth token on app load
  authStore.checkAuth();
});
</script>
