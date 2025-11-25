<template>
  <div :class="['bg-white shadow-lg transition-all duration-300', isCollapsed ? 'w-20' : 'w-64']">
    <!-- Logo Section -->
    <div class="p-6">
      <div class="flex items-center">
        <div class="h-8 w-8 gradient-primary rounded-lg flex items-center justify-center flex-shrink-0">
          <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
        </div>
        <transition name="fade">
          <span v-if="!isCollapsed" class="ml-2 text-xl font-semibold text-gray-900">Scheduler</span>
        </transition>
      </div>
    </div>

    <!-- Toggle Button -->
    <div class="px-4 pb-4">
      <button
        @click="toggleSidebar"
        class="w-full flex items-center justify-center p-2 text-gray-600 hover:text-green-600 hover:bg-gray-50 rounded-lg transition-colors"
        :title="isCollapsed ? 'Expand sidebar' : 'Collapse sidebar'"
      >
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path v-if="!isCollapsed" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
          <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
        </svg>
        <span v-if="!isCollapsed" class="ml-2 text-sm">{{ isCollapsed ? 'Expand' : 'Collapse' }}</span>
      </button>
    </div>

    <!-- Navigation Menu -->
    <nav class="px-4">
      <router-link
        v-for="item in menuItems"
        :key="item.path"
        :to="item.path"
        :class="[
          'flex items-center px-3 py-3 mb-1 rounded-lg transition-all duration-200 group',
          isCurrentRoute(item.path)
            ? 'bg-green-50 text-green-600 border-r-4 border-green-600'
            : 'text-gray-700 hover:bg-gray-100 hover:text-green-600'
        ]"
        :title="isCollapsed ? item.name : ''"
      >
        <div class="flex-shrink-0 w-5 h-5 flex items-center justify-center">
          <!-- Home icon -->
          <svg v-if="item.icon === 'home'" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
          </svg>
          <!-- Calendar icon -->
          <svg v-else-if="item.icon === 'calendar'" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
          <!-- Book icon -->
          <svg v-else-if="item.icon === 'book'" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
          </svg>
          <!-- Users icon -->
          <svg v-else-if="item.icon === 'users'" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
          <!-- Building icon -->
          <svg v-else-if="item.icon === 'building'" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
          </svg>
          <!-- Settings icon -->
          <svg v-else-if="item.icon === 'settings'" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
        </div>
        <transition name="fade">
          <div v-if="!isCollapsed" class="ml-3">
            <span class="font-medium">{{ item.name }}</span>
            <span v-if="item.badge" class="ml-2 px-2 py-1 text-xs bg-red-500 text-white rounded-full">{{ item.badge }}</span>
          </div>
        </transition>

        <!-- Tooltip for collapsed mode -->
        <div
          v-if="isCollapsed"
          class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-sm rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap pointer-events-none z-50"
        >
          {{ item.name }}
          <div class="absolute -left-1 top-1/2 transform -translate-y-1/2 w-2 h-2 bg-gray-900 rotate-45"></div>
        </div>
      </router-link>
    </nav>

    <!-- User Profile Section -->
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200">
      <div class="flex items-center">
        <div class="relative">
          <div class="h-8 w-8 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center text-white font-semibold">
            {{ userInitial }}
          </div>
          <div class="absolute bottom-0 right-0 h-2 w-2 bg-green-500 rounded-full border-2 border-white"></div>
        </div>
        <transition name="fade">
          <div v-if="!isCollapsed" class="ml-3">
            <p class="text-sm font-medium text-gray-900 truncate">{{ userName }}</p>
            <p class="text-xs text-gray-500 truncate">{{ userRole }}</p>
          </div>
        </transition>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useRoute } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const route = useRoute();
const authStore = useAuthStore();

const isCollapsed = ref(false);

// Icon functions - simplified approach

const menuItems = [
  { path: '/dashboard', name: 'Dashboard', icon: 'home' },
  { path: '/schedule', name: 'Jadwal', icon: 'calendar', badge: '3' },
  { path: '/courses', name: 'Mata Kuliah', icon: 'book' },
  { path: '/program-studies', name: 'Program Studi', icon: 'building' },
  { path: '/students', name: 'Mahasiswa', icon: 'users' },
  { path: '/users', name: 'Pengguna', icon: 'users', adminOnly: true },
  { path: '/settings', name: 'Pengaturan', icon: 'settings' },
];

const userName = computed(() => authStore.user?.name || 'User');
const userRole = computed(() => authStore.userRole || 'User');
const userInitial = computed(() => {
  const name = authStore.user?.name || 'U';
  return name.charAt(0).toUpperCase();
});

const toggleSidebar = () => {
  isCollapsed.value = !isCollapsed.value;
  localStorage.setItem('sidebar-collapsed', isCollapsed.value.toString());
};

const isCurrentRoute = (path) => {
  return route.path === path || route.path.startsWith(path + '/');
};

// Load sidebar state from localStorage
const loadSidebarState = () => {
  const saved = localStorage.getItem('sidebar-collapsed');
  if (saved !== null) {
    isCollapsed.value = saved === 'true';
  }
};

loadSidebarState();

// Filter menu items based on user role
const filteredMenuItems = computed(() => {
  if (userRole.value === 'admin' || userRole.value === 'super admin') {
    return menuItems;
  }
  return menuItems.filter(item => !item.adminOnly);
});
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>