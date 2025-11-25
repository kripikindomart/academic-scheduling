<template>
  <div class="min-h-screen bg-gray-50">
    <div class="flex h-screen">
      <!-- Sidebar -->
      <div class="w-64 bg-white shadow-lg">
        <div class="p-6">
          <div class="flex items-center">
            <div class="h-8 w-8 gradient-primary rounded-lg flex items-center justify-center">
              <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              </svg>
            </div>
            <span class="ml-2 text-xl font-semibold text-gray-900">Mata Kuliah</span>
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

          <router-link to="/courses" class="flex items-center px-6 py-3 bg-green-50 text-green-600 border-r-4 border-green-600">
            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            Mata Kuliah
          </router-link>

          <router-link to="/students" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-100 hover:text-green-600 transition-colors">
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
                <h1 class="text-2xl font-semibold text-gray-900">Mata Kuliah</h1>
                <p class="text-sm text-gray-600">Kelola data mata kuliah</p>
              </div>
              <div class="flex items-center space-x-4">
                <button class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors flex items-center">
                  <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                  </svg>
                  Tambah Mata Kuliah
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
                <input type="text" v-model="searchQuery" placeholder="Cari mata kuliah..."
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Semester</label>
                <select v-model="filterSemester" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                  <option value="">Semua Semester</option>
                  <option value="1">Semester 1</option>
                  <option value="2">Semester 2</option>
                  <option value="3">Semester 3</option>
                  <option value="4">Semester 4</option>
                  <option value="5">Semester 5</option>
                  <option value="6">Semester 6</option>
                  <option value="7">Semester 7</option>
                  <option value="8">Semester 8</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipe</label>
                <select v-model="filterType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                  <option value="">Semua Tipe</option>
                  <option value="mandatory">Wajib</option>
                  <option value="elective">Pilihan</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Courses Table -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
              <h2 class="text-xl font-semibold text-gray-900">Daftar Mata Kuliah</h2>
            </div>
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead class="bg-gray-50">
                  <tr class="text-left text-gray-600 border-b border-gray-200">
                    <th class="px-6 py-3 font-medium">Kode</th>
                    <th class="px-6 py-3 font-medium">Nama Mata Kuliah</th>
                    <th class="px-6 py-3 font-medium">SKS</th>
                    <th class="px-6 py-3 font-medium">Semester</th>
                    <th class="px-6 py-3 font-medium">Tipe</th>
                    <th class="px-6 py-3 font-medium">Kapasitas</th>
                    <th class="px-6 py-3 font-medium">Status</th>
                    <th class="px-6 py-3 font-medium">Aksi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                  <tr v-for="course in filteredCourses" :key="course.id" class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-gray-900">{{ course.code }}</td>
                    <td class="px-6 py-4 text-gray-900">{{ course.name }}</td>
                    <td class="px-6 py-4 text-gray-900">{{ course.credits }}</td>
                    <td class="px-6 py-4 text-gray-900">{{ course.semester }}</td>
                    <td class="px-6 py-4">
                      <span :class="course.type === 'mandatory' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'"
                        class="px-2 py-1 rounded-full text-xs font-medium">
                        {{ course.type === 'mandatory' ? 'Wajib' : 'Pilihan' }}
                      </span>
                    </td>
                    <td class="px-6 py-4 text-gray-900">{{ course.capacity }}</td>
                    <td class="px-6 py-4">
                      <span :class="course.isActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                        class="px-2 py-1 rounded-full text-xs font-medium">
                        {{ course.isActive ? 'Aktif' : 'Tidak Aktif' }}
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
const filterSemester = ref('');
const filterType = ref('');
const courses = ref([]);
const loading = ref(false);

const filteredCourses = computed(() => {
  let filtered = courses.value;

  if (searchQuery.value) {
    filtered = filtered.filter(course =>
      course.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      course.code.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
  }

  if (filterSemester.value) {
    filtered = filtered.filter(course => course.semester === filterSemester.value);
  }

  if (filterType.value) {
    filtered = filtered.filter(course => course.type === filterType.value);
  }

  return filtered;
});

const fetchCourses = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/courses');
    courses.value = response.data.data || response.data;
  } catch (error) {
    console.error('Error fetching courses:', error);
    // Mock data for now
    courses.value = [
      { id: 1, code: 'IF001', name: 'Pemrograman Web', credits: 3, semester: 3, type: 'mandatory', capacity: 50, isActive: true },
      { id: 2, code: 'IF002', name: 'Basis Data', credits: 4, semester: 2, type: 'mandatory', capacity: 40, isActive: true },
      { id: 3, code: 'IF003', name: 'Kecerdasan Buatan', credits: 3, semester: 5, type: 'elective', capacity: 30, isActive: true },
      { id: 4, code: 'IF004', name: 'Jaringan Komputer', credits: 3, semester: 4, type: 'mandatory', capacity: 45, isActive: false },
      { id: 5, code: 'IF005', name: 'Algoritma Struktur Data', credits: 4, semester: 2, type: 'mandatory', capacity: 50, isActive: true },
    ];
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchCourses();
});
</script>