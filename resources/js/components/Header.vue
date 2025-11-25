<template>
  <header class="bg-white shadow-sm border-b border-gray-200">
    <div class="px-6 py-4">
      <div class="flex justify-between items-center">
        <!-- Left: Page Title and Breadcrumb -->
        <div>
          <div class="flex items-center space-x-4">
            <!-- Mobile Menu Toggle -->
            <button
              @click="$emit('toggle-mobile-sidebar')"
              class="lg:hidden p-2 text-gray-600 hover:text-green-600 hover:bg-gray-50 rounded-lg"
            >
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
            </button>

            <div>
              <h1 class="text-2xl font-semibold text-gray-900">{{ pageTitle }}</h1>
              <nav class="text-sm text-gray-500">
                <ol class="flex items-center space-x-2">
                  <li v-for="(item, index) in breadcrumbs" :key="index">
                    <template v-if="index < breadcrumbs.length - 1">
                      <router-link
                        v-if="item.link"
                        :to="item.link"
                        class="hover:text-green-600 transition-colors"
                      >
                        {{ item.name }}
                      </router-link>
                      <span v-else>{{ item.name }}</span>
                      <svg class="inline-block w-4 h-4 mx-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                      </svg>
                    </template>
                    <span v-else class="text-gray-900 font-medium">{{ item.name }}</span>
                  </li>
                </ol>
              </nav>
            </div>
          </div>
        </div>

        <!-- Right: Actions and User Menu -->
        <div class="flex items-center space-x-4">
          <!-- Search Bar -->
          <div class="hidden md:block">
            <div class="relative">
              <input
                type="text"
                v-model="searchQuery"
                placeholder="Cari..."
                class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                @keyup.enter="handleSearch"
              />
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </div>
            </div>
          </div>

          <!-- Notifications -->
          <div class="relative">
            <button
              @click="toggleNotifications"
              class="relative p-2 text-gray-600 hover:text-green-600 hover:bg-gray-50 rounded-lg transition-colors"
            >
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
              </svg>
              <span v-if="unreadNotifications > 0" class="absolute top-0 right-0 h-4 w-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">
                {{ unreadNotifications > 9 ? '9+' : unreadNotifications }}
              </span>
            </button>

            <!-- Notifications Dropdown -->
            <div
              v-if="showNotifications"
              v-click-outside="closeNotifications"
              class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50"
            >
              <div class="p-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-900">Notifikasi</h3>
              </div>
              <div class="max-h-96 overflow-y-auto">
                <div v-for="notification in notifications" :key="notification.id" class="p-4 hover:bg-gray-50 border-b border-gray-100 last:border-b-0">
                  <div class="flex items-start">
                    <div class="flex-shrink-0">
                      <div :class="getNotificationIconClass(notification.type)" class="p-2 rounded-lg">
                        <svg v-if="notification.type === 'info'" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <svg v-else-if="notification.type === 'success'" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <svg v-else-if="notification.type === 'warning'" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                      </div>
                    </div>
                    <div class="ml-3 flex-1">
                      <p class="text-sm text-gray-900">{{ notification.message }}</p>
                      <p class="text-xs text-gray-500 mt-1">{{ formatTime(notification.createdAt) }}</p>
                    </div>
                  </div>
                </div>
              </div>
              <div class="p-4 border-t border-gray-200">
                <button class="w-full text-center text-sm text-green-600 hover:text-green-700 font-medium">
                  Lihat Semua Notifikasi
                </button>
              </div>
            </div>
          </div>

          <!-- User Menu -->
          <div class="relative">
            <button
              @click="toggleUserMenu"
              class="flex items-center space-x-3 p-2 text-gray-600 hover:text-green-600 hover:bg-gray-50 rounded-lg transition-colors"
            >
              <div class="relative">
                <div class="h-8 w-8 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center text-white font-semibold">
                  {{ userInitial }}
                </div>
                <div class="absolute bottom-0 right-0 h-2 w-2 bg-green-500 rounded-full border-2 border-white"></div>
              </div>
              <div class="hidden md:block text-left">
                <p class="text-sm font-medium text-gray-900">{{ userName }}</p>
                <p class="text-xs text-gray-500">{{ userRole }}</p>
              </div>
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>

            <!-- User Menu Dropdown -->
            <div
              v-if="showUserMenu"
              v-click-outside="closeUserMenu"
              class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-50"
            >
              <div class="p-4 border-b border-gray-200">
                <p class="text-sm font-medium text-gray-900">{{ userName }}</p>
                <p class="text-xs text-gray-500">{{ userEmail }}</p>
              </div>
              <div class="py-1">
                <router-link to="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-green-600">
                  <div class="flex items-center">
                    <svg class="h-4 w-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Profil
                  </div>
                </router-link>
                <router-link to="/settings" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-green-600">
                  <div class="flex items-center">
                    <svg class="h-4 w-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Pengaturan
                  </div>
                </router-link>
                <hr class="my-1" />
                <button @click="handleLogout" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 hover:text-red-700">
                  <div class="flex items-center">
                    <svg class="h-4 w-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Logout
                  </div>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const emit = defineEmits(['toggle-mobile-sidebar']);
const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();

const searchQuery = ref('');
const showNotifications = ref(false);
const showUserMenu = ref(false);
const unreadNotifications = ref(3);

const userName = computed(() => authStore.user?.name || 'User');
const userEmail = computed(() => authStore.user?.email || '');
const userRole = computed(() => authStore.userRole || 'User');
const userInitial = computed(() => {
  const name = authStore.user?.name || 'U';
  return name.charAt(0).toUpperCase();
});

// Page title and breadcrumbs based on current route
const pageTitle = computed(() => {
  const titles = {
    '/dashboard': 'Dashboard',
    '/schedule': 'Jadwal',
    '/courses': 'Mata Kuliah',
    '/students': 'Mahasiswa',
    '/users': 'Pengguna',
    '/settings': 'Pengaturan',
  };
  return titles[route.path] || 'Dashboard';
});

const breadcrumbs = computed(() => {
  const paths = route.path.split('/').filter(Boolean);
  const breadcrumbItems = [{ name: 'Beranda', link: '/' }];

  if (paths.length > 0) {
    let currentPath = '';
    paths.forEach(path => {
      currentPath += `/${path}`;
      const name = pageTitle.value;
      if (name && name !== 'Beranda') {
        breadcrumbItems.push({ name, link: currentPath });
      }
    });
  }

  return breadcrumbItems;
});

const notifications = ref([
  {
    id: 1,
    type: 'info',
    message: 'Jadwal mata kuliah Pemrograman Web telah diperbarui',
    createdAt: new Date(Date.now() - 1000 * 60 * 5), // 5 menit lalu
    read: false
  },
  {
    id: 2,
    type: 'success',
    message: 'Data mahasiswa baru berhasil ditambahkan',
    createdAt: new Date(Date.now() - 1000 * 60 * 30), // 30 menit lalu
    read: false
  },
  {
    id: 3,
    type: 'warning',
    message: 'Ruangan Lab Komputer 1 tidak tersedia hari ini',
    createdAt: new Date(Date.now() - 1000 * 60 * 60), // 1 jam lalu
    read: true
  }
]);

// Notification icons - simplified approach
const getNotificationIcon = (type) => {
  return type; // Just return the type, we'll handle it in template
};

const getNotificationIconClass = (type) => {
  const classes = {
    info: 'bg-blue-100 text-blue-600',
    success: 'bg-green-100 text-green-600',
    warning: 'bg-yellow-100 text-yellow-600'
  };
  return classes[type] || classes.info;
};

const formatTime = (date) => {
  const now = new Date();
  const diff = now - date;
  const minutes = Math.floor(diff / 60000);
  const hours = Math.floor(diff / 3600000);
  const days = Math.floor(diff / 86400000);

  if (minutes < 1) return 'Baru saja';
  if (minutes < 60) return `${minutes} menit lalu`;
  if (hours < 24) return `${hours} jam lalu`;
  return `${days} hari lalu`;
};

const toggleNotifications = () => {
  showNotifications.value = !showNotifications.value;
  showUserMenu.value = false;
};

const toggleUserMenu = () => {
  showUserMenu.value = !showUserMenu.value;
  showNotifications.value = false;
};

const closeNotifications = () => {
  showNotifications.value = false;
};

const closeUserMenu = () => {
  showUserMenu.value = false;
};

const handleSearch = () => {
  if (searchQuery.value.trim()) {
    // Implement search functionality
    console.log('Searching for:', searchQuery.value);
  }
};

const handleLogout = async () => {
  await authStore.logout();
  router.push('/login');
};

// Click outside directive
const vClickOutside = {
  beforeMount(el, binding) {
    el.clickOutsideEvent = (event) => {
      if (!(el === event.target || el.contains(event.target))) {
        binding.value();
      }
    };
    document.addEventListener('click', el.clickOutsideEvent);
  },
  unmounted(el) {
    document.removeEventListener('click', el.clickOutsideEvent);
  }
};

onMounted(() => {
  // Calculate unread notifications
  unreadNotifications.value = notifications.value.filter(n => !n.read).length;
});

onUnmounted(() => {
  closeNotifications();
  closeUserMenu();
});
</script>