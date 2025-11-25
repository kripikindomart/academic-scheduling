<template>
  <Layout>
    <div class="max-w-4xl mx-auto">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-2xl font-semibold text-gray-900">Profil Saya</h1>
        <p class="text-sm text-gray-600">Kelola informasi profil Anda</p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Card -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex flex-col items-center">
              <div class="h-24 w-24 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center text-white font-bold text-2xl mb-4">
                {{ userInitial }}
              </div>
              <h3 class="text-xl font-semibold text-gray-900">{{ authStore.user?.name || 'User' }}</h3>
              <p class="text-gray-600">{{ authStore.userRole?.charAt(0).toUpperCase() + authStore.userRole?.slice(1) || 'User' }}</p>
              <p class="text-sm text-gray-500 mt-1">{{ authStore.user?.email }}</p>

              <div class="mt-6 w-full border-t border-gray-200 pt-6">
                <div class="space-y-2 text-sm">
                  <div class="flex justify-between">
                    <span class="text-gray-600">Status</span>
                    <span class="font-medium text-green-600">Aktif</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">Bergabung Sejak</span>
                    <span class="font-medium">Nov 2024</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Profile Form -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Personal Information -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
              <h2 class="text-xl font-semibold text-gray-900">Informasi Pribadi</h2>
            </div>
            <div class="p-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                  <input type="text" v-model="profile.name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                  <input type="email" v-model="profile.email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" readonly>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon</label>
                  <input type="tel" v-model="profile.phone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir</label>
                  <input type="date" v-model="profile.birthDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
              </div>
              <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Bio</label>
                <textarea v-model="profile.bio" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Ceritakan tentang diri Anda..."></textarea>
              </div>
            </div>
          </div>

          <!-- Change Password -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
              <h2 class="text-xl font-semibold text-gray-900">Ubah Password</h2>
            </div>
            <div class="p-6">
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Password Saat Ini</label>
                  <input type="password" v-model="passwordForm.current" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
                  <input type="password" v-model="passwordForm.new" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password Baru</label>
                  <input type="password" v-model="passwordForm.confirm" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div class="flex items-center text-sm text-gray-600">
                  <svg class="h-5 w-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  Password harus minimal 8 karakter dengan kombinasi huruf dan angka
                </div>
                <button @click="changePassword" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                  Ubah Password
                </button>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex justify-end space-x-4">
            <button class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
              Batal
            </button>
            <button @click="saveProfile" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
              Simpan Perubahan
            </button>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref, computed } from 'vue';
import Layout from '@/components/Layout.vue';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();

const userInitial = computed(() => {
  const name = authStore.user?.name || 'U';
  return name.charAt(0).toUpperCase();
});

const profile = ref({
  name: authStore.user?.name || '',
  email: authStore.user?.email || '',
  phone: '',
  birthDate: '',
  bio: ''
});

const passwordForm = ref({
  current: '',
  new: '',
  confirm: ''
});

const saveProfile = async () => {
  try {
    console.log('Saving profile:', profile.value);
    // Save to API
    alert('Profil berhasil diperbarui!');
  } catch (error) {
    console.error('Error saving profile:', error);
    alert('Gagal memperbarui profil');
  }
};

const changePassword = async () => {
  if (passwordForm.value.new !== passwordForm.value.confirm) {
    alert('Password baru dan konfirmasi tidak cocok!');
    return;
  }

  if (passwordForm.value.new.length < 8) {
    alert('Password minimal 8 karakter!');
    return;
  }

  try {
    console.log('Changing password');
    // Send to API
    alert('Password berhasil diubah!');

    // Clear form
    passwordForm.value = {
      current: '',
      new: '',
      confirm: ''
    };
  } catch (error) {
    console.error('Error changing password:', error);
    alert('Gagal mengubah password');
  }
};
</script>