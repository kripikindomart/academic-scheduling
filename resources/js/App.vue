<template>
  <div id="app" class="min-h-screen bg-background">
    <RouterView />

    <!-- Global Toast Container -->
    <div class="fixed bottom-4 right-4 z-50 space-y-2">
      <Toast
        v-for="toast in toastStore.toasts"
        :key="toast.id"
        :title="toast.title"
        :description="toast.description"
        :type="toast.type"
        :duration="toast.duration"
        @close="toastStore.removeToast(toast.id)"
      />
    </div>
  </div>
</template>

<script setup>
import { RouterView } from 'vue-router';
import { onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useToastStore } from './stores/toast';
import Toast from './components/Toast.vue';

const authStore = useAuthStore();
const toastStore = useToastStore();

onMounted(() => {
  // Check for existing auth token on app load
  authStore.checkAuth();
});
</script>
