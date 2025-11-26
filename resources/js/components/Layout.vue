<template>
  <div class="min-h-screen bg-gray-50">
    <div class="flex relative">
      <!-- Sidebar -->
      <Sidebar id="main-sidebar" class="fixed lg:relative z-40 transition-all duration-300" @sidebar-collapsed="handleSidebarCollapse" />

      <!-- Mobile Sidebar Overlay -->
      <div
        v-if="showMobileSidebar"
        @click="closeMobileSidebar"
        class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden"
      ></div>

      <!-- Main Content -->
      <div class="flex-1 flex flex-col min-w-0 transition-all duration-300">
        <!-- Header -->
        <Header @toggle-mobile-sidebar="toggleMobileSidebar" />

        <!-- Main Content Area -->
        <main class="flex-1 overflow-x-hidden bg-gray-50" :class="isSidebarCollapsed ? 'lg:ml-0' : 'lg:ml-4'">
          <div class="max-w-full">
            <slot />
          </div>
        </main>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import Sidebar from './Sidebar.vue';
import Header from './Header.vue';

const showMobileSidebar = ref(false);
const isSidebarCollapsed = ref(false);

const toggleMobileSidebar = () => {
  showMobileSidebar.value = !showMobileSidebar.value;
};

const closeMobileSidebar = () => {
  showMobileSidebar.value = false;
};

const handleSidebarCollapse = (collapsed) => {
  isSidebarCollapsed.value = collapsed;
};

const handleEscape = (event) => {
  if (event.key === 'Escape') {
    closeMobileSidebar();
  }
};

onMounted(() => {
  document.addEventListener('keydown', handleEscape);
});

onUnmounted(() => {
  document.removeEventListener('keydown', handleEscape);
});
</script>