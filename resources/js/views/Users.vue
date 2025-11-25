<template>
  <Layout>
    <!-- Header Actions -->
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-2xl font-semibold text-gray-900">Pengguna</h1>
        <p class="text-sm text-gray-600">Kelola data pengguna sistem</p>
      </div>
      <button class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors flex items-center">
        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        Tambah Pengguna
      </button>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
          <input type="text" v-model="searchQuery" placeholder="Cari nama atau email..."
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
          <select v-model="filterRole" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            <option value="">Semua Role</option>
            <option value="super admin">Super Admin</option>
            <option value="admin">Admin</option>
            <option value="dosen">Dosen</option>
            <option value="kaprodi">Kaprodi</option>
            <option value="staff">Staff</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
          <select v-model="filterStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            <option value="">Semua Status</option>
            <option value="active">Aktif</option>
            <option value="inactive">Tidak Aktif</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
      <div class="p-6 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-900">Daftar Pengguna</h2>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr class="text-left text-gray-600 border-b border-gray-200">
              <th class="px-6 py-3 font-medium">Nama</th>
              <th class="px-6 py-3 font-medium">Email</th>
              <th class="px-6 py-3 font-medium">Role</th>
              <th class="px-6 py-3 font-medium">Status</th>
              <th class="px-6 py-3 font-medium">Terakhir Login</th>
              <th class="px-6 py-3 font-medium">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="user in filteredUsers" :key="user.id" class="hover:bg-gray-50">
              <td class="px-6 py-4">
                <div class="flex items-center">
                  <div class="h-8 w-8 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center text-white font-semibold text-sm mr-3">
                    {{ user.name.charAt(0).toUpperCase() }}
                  </div>
                  <div>
                    <p class="font-medium text-gray-900">{{ user.name }}</p>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 text-gray-900">{{ user.email }}</td>
              <td class="px-6 py-4">
                <span :class="getRoleClass(user.role)" class="px-2 py-1 rounded-full text-xs font-medium">
                  {{ user.role }}
                </span>
              </td>
              <td class="px-6 py-4">
                <span :class="user.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                  class="px-2 py-1 rounded-full text-xs font-medium">
                  {{ user.status === 'active' ? 'Aktif' : 'Tidak Aktif' }}
                </span>
              </td>
              <td class="px-6 py-4 text-gray-900">{{ formatLastLogin(user.lastLogin) }}</td>
              <td class="px-6 py-4">
                <div class="flex space-x-2">
                  <button class="text-blue-600 hover:text-blue-900">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </button>
                  <button class="text-red-600 hover:text-red-900">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import Layout from '@/components/Layout.vue';

const searchQuery = ref('');
const filterRole = ref('');
const filterStatus = ref('');
const users = ref([]);
const loading = ref(false);

const filteredUsers = computed(() => {
  let filtered = users.value;

  if (searchQuery.value) {
    filtered = filtered.filter(user =>
      user.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      user.email.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
  }

  if (filterRole.value) {
    filtered = filtered.filter(user => user.role === filterRole.value);
  }

  if (filterStatus.value) {
    filtered = filtered.filter(user => user.status === filterStatus.value);
  }

  return filtered;
});

const getRoleClass = (role) => {
  const classes = {
    'super admin': 'bg-purple-100 text-purple-800',
    'admin': 'bg-red-100 text-red-800',
    'dosen': 'bg-blue-100 text-blue-800',
    'kaprodi': 'bg-green-100 text-green-800',
    'staff': 'bg-yellow-100 text-yellow-800'
  };
  return classes[role] || 'bg-gray-100 text-gray-800';
};

const formatLastLogin = (lastLogin) => {
  if (!lastLogin) return 'Belum pernah';

  const now = new Date();
  const loginDate = new Date(lastLogin);
  const diff = now - loginDate;
  const minutes = Math.floor(diff / 60000);
  const hours = Math.floor(diff / 3600000);
  const days = Math.floor(diff / 86400000);

  if (minutes < 60) return `${minutes} menit lalu`;
  if (hours < 24) return `${hours} jam lalu`;
  if (days < 7) return `${days} hari lalu`;
  return loginDate.toLocaleDateString('id-ID');
};

const fetchUsers = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/users');
    users.value = response.data.data || response.data;
  } catch (error) {
    console.error('Error fetching users:', error);
    // Mock data for now
    users.value = [
      { id: 1, name: 'Super Admin', email: 'admin@jadwal-app.com', role: 'super admin', status: 'active', lastLogin: new Date(Date.now() - 1000 * 60 * 30) },
      { id: 2, name: 'Admin User', email: 'admin2@jadwal-app.com', role: 'admin', status: 'active', lastLogin: new Date(Date.now() - 1000 * 60 * 60 * 2) },
      { id: 3, name: 'Dr. Ahmad', email: 'ahmad@jadwal-app.com', role: 'dosen', status: 'active', lastLogin: new Date(Date.now() - 1000 * 60 * 60 * 5) },
      { id: 4, name: 'Prof. Siti', email: 'siti@jadwal-app.com', role: 'kaprodi', status: 'active', lastLogin: new Date(Date.now() - 1000 * 60 * 60 * 24) },
      { id: 5, name: 'Staff Akademik', email: 'staff@jadwal-app.com', role: 'staff', status: 'inactive', lastLogin: null },
    ];
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchUsers();
});
</script>