<template>
  <Layout>
    <div class="max-w-4xl mx-auto">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-2xl font-semibold text-gray-900">Pengaturan</h1>
        <p class="text-sm text-gray-600">Kelola preferensi dan konfigurasi sistem</p>
      </div>

      <!-- Settings Sections -->
      <div class="space-y-6">
        <!-- Profile Settings -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
          <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Profil Pengguna</h2>
          </div>
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                <input type="text" v-model="settings.name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input type="email" v-model="settings.email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon</label>
                <input type="tel" v-model="settings.phone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Bio</label>
                <textarea v-model="settings.bio" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
              </div>
            </div>
          </div>
        </div>

        <!-- Notification Settings -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
          <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Notifikasi</h2>
          </div>
          <div class="p-6 space-y-4">
            <div class="flex items-center justify-between">
              <div>
                <p class="font-medium text-gray-900">Email Notifications</p>
                <p class="text-sm text-gray-500">Terima notifikasi melalui email</p>
              </div>
              <button @click="toggleSetting('emailNotifications')" :class="settings.emailNotifications ? 'bg-green-600' : 'bg-gray-200'" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors">
                <span :class="settings.emailNotifications ? 'translate-x-6' : 'translate-x-1'" class="inline-block h-4 w-4 transform bg-white rounded-full transition-transform"></span>
              </button>
            </div>
            <div class="flex items-center justify-between">
              <div>
                <p class="font-medium text-gray-900">Push Notifications</p>
                <p class="text-sm text-gray-500">Terima notifikasi di browser</p>
              </div>
              <button @click="toggleSetting('pushNotifications')" :class="settings.pushNotifications ? 'bg-green-600' : 'bg-gray-200'" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors">
                <span :class="settings.pushNotifications ? 'translate-x-6' : 'translate-x-1'" class="inline-block h-4 w-4 transform bg-white rounded-full transition-transform"></span>
              </button>
            </div>
            <div class="flex items-center justify-between">
              <div>
                <p class="font-medium text-gray-900">Jadwal Reminder</p>
                <p class="text-sm text-gray-500">Pengingat jadwal 15 menit sebelum mulai</p>
              </div>
              <button @click="toggleSetting('scheduleReminder')" :class="settings.scheduleReminder ? 'bg-green-600' : 'bg-gray-200'" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors">
                <span :class="settings.scheduleReminder ? 'translate-x-6' : 'translate-x-1'" class="inline-block h-4 w-4 transform bg-white rounded-full transition-transform"></span>
              </button>
            </div>
          </div>
        </div>

        <!-- Appearance Settings -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
          <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Tampilan</h2>
          </div>
          <div class="p-6 space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Theme</label>
              <select v-model="settings.theme" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                <option value="light">Light</option>
                <option value="dark">Dark</option>
                <option value="auto">Auto (System)</option>
              </select>
            </div>
            <div class="flex items-center justify-between">
              <div>
                <p class="font-medium text-gray-900">Compact Mode</p>
                <p class="text-sm text-gray-500">Tampilan yang lebih ringkas</p>
              </div>
              <button @click="toggleSetting('compactMode')" :class="settings.compactMode ? 'bg-green-600' : 'bg-gray-200'" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors">
                <span :class="settings.compactMode ? 'translate-x-6' : 'translate-x-1'" class="inline-block h-4 w-4 transform bg-white rounded-full transition-transform"></span>
              </button>
            </div>
          </div>
        </div>

        <!-- Privacy Settings -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
          <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Privasi</h2>
          </div>
          <div class="p-6 space-y-4">
            <div class="flex items-center justify-between">
              <div>
                <p class="font-medium text-gray-900">Profile Visibility</p>
                <p class="text-sm text-gray-500">Tampilkan profil ke pengguna lain</p>
              </div>
              <button @click="toggleSetting('profileVisibility')" :class="settings.profileVisibility ? 'bg-green-600' : 'bg-gray-200'" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors">
                <span :class="settings.profileVisibility ? 'translate-x-6' : 'translate-x-1'" class="inline-block h-4 w-4 transform bg-white rounded-full transition-transform"></span>
              </button>
            </div>
            <div class="flex items-center justify-between">
              <div>
                <p class="font-medium text-gray-900">Status Online</p>
                <p class="text-sm text-gray-500">Tampilkan status online Anda</p>
              </div>
              <button @click="toggleSetting('onlineStatus')" :class="settings.onlineStatus ? 'bg-green-600' : 'bg-gray-200'" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors">
                <span :class="settings.onlineStatus ? 'translate-x-6' : 'translate-x-1'" class="inline-block h-4 w-4 transform bg-white rounded-full transition-transform"></span>
              </button>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-4">
          <button class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
            Batal
          </button>
          <button @click="saveSettings" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
            Simpan Pengaturan
          </button>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref } from 'vue';
import Layout from '@/components/Layout.vue';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();

const settings = ref({
  name: authStore.user?.name || '',
  email: authStore.user?.email || '',
  phone: '',
  bio: '',
  emailNotifications: true,
  pushNotifications: true,
  scheduleReminder: true,
  theme: 'light',
  compactMode: false,
  profileVisibility: true,
  onlineStatus: true
});

const toggleSetting = (setting) => {
  settings.value[setting] = !settings.value[setting];
};

const saveSettings = async () => {
  try {
    // Save settings to API
    console.log('Saving settings:', settings.value);
    // Show success message
    alert('Pengaturan berhasil disimpan!');
  } catch (error) {
    console.error('Error saving settings:', error);
    alert('Gagal menyimpan pengaturan');
  }
};
</script>