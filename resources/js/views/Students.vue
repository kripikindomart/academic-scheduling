<template>
  <div class="min-h-screen bg-gray-50">
    <div class="flex h-screen">
      <!-- Sidebar -->
      <div class="w-64 bg-white shadow-lg">
        <div class="p-6">
          <div class="flex items-center">
            <div class="h-8 w-8 gradient-primary rounded-lg flex items-center justify-center">
              <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
            </div>
            <span class="ml-2 text-xl font-semibold text-gray-900">Mahasiswa</span>
          </div>
        </div>

        <nav class="mt-6">
          <router-link to="/dashboard" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-100 hover:text-green-600 transition-colors">
            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            Dashboard
          </router-link>

          <router-link to="/schedule" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-100 hover:text-green-600 transition-colors">
            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            Jadwal
          </router-link>

          <router-link to="/courses" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-100 hover:text-green-600 transition-colors">
            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            Mata Kuliah
          </router-link>

          <router-link to="/students" class="flex items-center px-6 py-3 bg-green-50 text-green-600 border-r-4 border-green-600">
            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            Mahasiswa
          </router-link>
        </nav>
      </div>

      <!-- Main Content -->
      <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
          <div class="px-6 py-4">
            <div class="flex justify-between items-center">
              <div>
                <h1 class="text-2xl font-semibold text-gray-900">Mahasiswa</h1>
                <p class="text-sm text-gray-600">Kelola data mahasiswa</p>
              </div>
              <div class="flex items-center space-x-4">
                <button class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors flex items-center">
                  <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                  </svg>
                  Tambah Mahasiswa
                </button>
              </div>
            </div>
          </div>
        </header>

        <!-- Content -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6">
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
        </main>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

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