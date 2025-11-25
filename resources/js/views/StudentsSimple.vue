<template>
  <Layout>
    <!-- Header Actions -->
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-2xl font-semibold text-gray-900">Mahasiswa</h1>
        <p class="text-sm text-gray-600">Kelola data mahasiswa</p>
      </div>
      <button class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors flex items-center">
        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        Tambah Mahasiswa
      </button>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
          <input type="text" v-model="searchQuery" placeholder="Cari nama atau NIM..."
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Program Studi</label>
          <select v-model="filterProgram" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            <option value="">Semua Program Studi</option>
            <option value="Teknik Informatika">Teknik Informatika</option>
            <option value="Sistem Informasi">Sistem Informasi</option>
            <option value="Manajemen">Manajemen</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
          <select v-model="filterStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            <option value="">Semua Status</option>
            <option value="active">Aktif</option>
            <option value="inactive">Tidak Aktif</option>
            <option value="graduated">Lulus</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
      <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
        <div class="flex items-center">
          <div class="p-3 bg-green-100 rounded-lg">
            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Total Mahasiswa</p>
            <p class="text-2xl font-semibold text-gray-900">256</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
        <div class="flex items-center">
          <div class="p-3 bg-blue-100 rounded-lg">
            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Aktif</p>
            <p class="text-2xl font-semibold text-gray-900">234</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
        <div class="flex items-center">
          <div class="p-3 bg-yellow-100 rounded-lg">
            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Tidak Aktif</p>
            <p class="text-2xl font-semibold text-gray-900">12</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
        <div class="flex items-center">
          <div class="p-3 bg-purple-100 rounded-lg">
            <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Lulus</p>
            <p class="text-2xl font-semibold text-gray-900">10</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Students Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
      <div class="p-6 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-900">Daftar Mahasiswa</h2>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr class="text-left text-gray-600 border-b border-gray-200">
              <th class="px-6 py-3 font-medium">NIM</th>
              <th class="px-6 py-3 font-medium">Nama</th>
              <th class="px-6 py-3 font-medium">Email</th>
              <th class="px-6 py-3 font-medium">Program Studi</th>
              <th class="px-6 py-3 font-medium">Angkatan</th>
              <th class="px-6 py-3 font-medium">Status</th>
              <th class="px-6 py-3 font-medium">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="student in filteredStudents" :key="student.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 font-medium text-gray-900">{{ student.nim }}</td>
              <td class="px-6 py-4 text-gray-900">{{ student.name }}</td>
              <td class="px-6 py-4 text-gray-900">{{ student.email }}</td>
              <td class="px-6 py-4 text-gray-900">{{ student.program }}</td>
              <td class="px-6 py-4 text-gray-900">{{ student.batch }}</td>
              <td class="px-6 py-4">
                <span :class="getStatusClass(student.status)" class="px-2 py-1 rounded-full text-xs font-medium">
                  {{ getStatusText(student.status) }}
                </span>
              </td>
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
const filterProgram = ref('');
const filterStatus = ref('');
const students = ref([]);
const loading = ref(false);

const filteredStudents = computed(() => {
  let filtered = students.value;

  if (searchQuery.value) {
    filtered = filtered.filter(student =>
      student.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      student.nim.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
  }

  if (filterProgram.value) {
    filtered = filtered.filter(student => student.program === filterProgram.value);
  }

  if (filterStatus.value) {
    filtered = filtered.filter(student => student.status === filterStatus.value);
  }

  return filtered;
});

const getStatusClass = (status) => {
  const classes = {
    active: 'bg-green-100 text-green-800',
    inactive: 'bg-red-100 text-red-800',
    graduated: 'bg-purple-100 text-purple-800'
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
};

const getStatusText = (status) => {
  const texts = {
    active: 'Aktif',
    inactive: 'Tidak Aktif',
    graduated: 'Lulus'
  };
  return texts[status] || 'Unknown';
};

const fetchStudents = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/students');
    students.value = response.data.data || response.data;
  } catch (error) {
    console.error('Error fetching students:', error);
    // Mock data for now
    students.value = [
      { id: 1, nim: '2021001001', name: 'Ahmad Rizki', email: 'ahmad@university.ac.id', program: 'Teknik Informatika', batch: '2021', status: 'active' },
      { id: 2, nim: '2021001002', name: 'Siti Nurhaliza', email: 'siti@university.ac.id', program: 'Sistem Informasi', batch: '2021', status: 'active' },
      { id: 3, nim: '2020002001', name: 'Budi Santoso', email: 'budi@university.ac.id', program: 'Teknik Informatika', batch: '2020', status: 'active' },
      { id: 4, nim: '2019003001', name: 'Dewi Lestari', email: 'dewi@university.ac.id', program: 'Manajemen', batch: '2019', status: 'graduated' },
      { id: 5, nim: '2021001003', name: 'Eko Prasetyo', email: 'eko@university.ac.id', program: 'Teknik Informatika', batch: '2021', status: 'inactive' },
    ];
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchStudents();
});
</script>