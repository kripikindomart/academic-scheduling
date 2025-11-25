<template>
  <Layout>
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-2xl font-semibold text-gray-900">Program Studi</h1>
        <p class="text-sm text-gray-600">Kelola data program studi</p>
      </div>
      <button @click="openCreateModal" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors flex items-center">
        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        Tambah Program Studi
      </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
      <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
        <div class="flex items-center">
          <div class="p-3 bg-blue-100 rounded-lg">
            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Total Program Studi</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.total }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
        <div class="flex items-center">
          <div class="p-3 bg-green-100 rounded-lg">
            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Aktif</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.active }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
        <div class="flex items-center">
          <div class="p-3 bg-yellow-100 rounded-lg">
            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Tidak Aktif</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.inactive }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
        <div class="flex items-center">
          <div class="p-3 bg-purple-100 rounded-lg">
            <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Fakultas</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.faculties }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
          <input
            type="text"
            v-model="filters.search"
            placeholder="Cari nama program studi..."
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
          >
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Fakultas</label>
          <select v-model="filters.faculty" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            <option value="">Semua Fakultas</option>
            <option value="Fakultas Ilmu Komputer">Fakultas Ilmu Komputer</option>
            <option value="Fakultas Teknik">Fakultas Teknik</option>
            <option value="Fakultas Ekonomi">Fakultas Ekonomi</option>
            <option value="Fakultas Hukum">Fakultas Hukum</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
          <select v-model="filters.status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            <option value="">Semua Status</option>
            <option value="active">Aktif</option>
            <option value="inactive">Tidak Aktif</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Jenjang</label>
          <select v-model="filters.level" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            <option value="">Semua Jenjang</option>
            <option value="S1">S1</option>
            <option value="S2">S2</option>
            <option value="S3">S3</option>
          </select>
        </div>
      </div>
      <div class="mt-4 flex justify-end space-x-2">
        <button @click="resetFilters" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
          Reset
        </button>
        <button @click="applyFilters" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
          Terapkan Filter
        </button>
      </div>
    </div>

    <!-- Bulk Actions -->
    <div v-if="selectedItems.length > 0" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
      <div class="flex justify-between items-center">
        <div class="flex items-center">
          <span class="text-sm font-medium text-yellow-800">
            {{ selectedItems.length }} item terpilih
          </span>
          <div class="ml-4 flex space-x-2">
            <button @click="bulkDelete" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition-colors text-sm">
              Hapus
            </button>
            <button @click="bulkActivate" class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 transition-colors text-sm">
              Aktifkan
            </button>
            <button @click="bulkDeactivate" class="px-3 py-1 bg-gray-600 text-white rounded hover:bg-gray-700 transition-colors text-sm">
              Nonaktifkan
            </button>
            <button @click="clearSelection" class="px-3 py-1 bg-yellow-600 text-white rounded hover:bg-yellow-700 transition-colors text-sm">
              Batal Pilih
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
      <div class="p-6 border-b border-gray-200">
        <div class="flex justify-between items-center">
          <h2 class="text-xl font-semibold text-gray-900">Daftar Program Studi</h2>
          <div class="flex items-center space-x-2">
            <span class="text-sm text-gray-600">Show</span>
            <select v-model="perPage" @change="fetchData" class="px-2 py-1 border border-gray-300 rounded text-sm">
              <option value="10">10</option>
              <option value="25">25</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select>
            <span class="text-sm text-gray-600">entries</span>
          </div>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr class="text-left text-gray-600 border-b border-gray-200">
              <th class="px-6 py-3 font-medium">
                <input
                  type="checkbox"
                  :checked="allSelected"
                  @change="toggleSelectAll"
                  class="rounded border-gray-300 text-green-600 focus:ring-green-500"
                >
              </th>
              <th class="px-6 py-3 font-medium">Kode</th>
              <th class="px-6 py-3 font-medium">Nama Program Studi</th>
              <th class="px-6 py-3 font-medium">Fakultas</th>
              <th class="px-6 py-3 font-medium">Jenjang</th>
              <th class="px-6 py-3 font-medium">Ketua Program Studi</th>
              <th class="px-6 py-3 font-medium">Status</th>
              <th class="px-6 py-3 font-medium">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="program in programs" :key="program.id" class="hover:bg-gray-50">
              <td class="px-6 py-4">
                <input
                  type="checkbox"
                  :checked="selectedItems.includes(program.id)"
                  @change="toggleSelect(program.id)"
                  class="rounded border-gray-300 text-green-600 focus:ring-green-500"
                >
              </td>
              <td class="px-6 py-4 font-medium text-gray-900">{{ program.code }}</td>
              <td class="px-6 py-4 text-gray-900">{{ program.name }}</td>
              <td class="px-6 py-4 text-gray-900">{{ program.faculty }}</td>
              <td class="px-6 py-4">
                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                  {{ program.level }}
                </span>
              </td>
              <td class="px-6 py-4 text-gray-900">{{ program.head_of_program }}</td>
              <td class="px-6 py-4">
                <span :class="program.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                      class="px-2 py-1 rounded-full text-xs font-medium">
                  {{ program.status === 'active' ? 'Aktif' : 'Tidak Aktif' }}
                </span>
              </td>
              <td class="px-6 py-4">
                <div class="flex space-x-2">
                  <button @click="editProgram(program)" class="text-blue-600 hover:text-blue-900">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </button>
                  <button @click="deleteProgram(program.id)" class="text-red-600 hover:text-red-900">
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

      <!-- Pagination -->
      <div class="p-6 border-t border-gray-200">
        <div class="flex justify-between items-center">
          <div class="text-sm text-gray-700">
            Showing {{ (currentPage - 1) * perPage + 1 }} to {{ Math.min(currentPage * perPage, total) }} of {{ total }} results
          </div>
          <div class="flex space-x-1">
            <button
              @click="currentPage > 1 && changePage(currentPage - 1)"
              :disabled="currentPage <= 1"
              class="px-3 py-1 border border-gray-300 rounded-l hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Previous
            </button>
            <button
              v-for="page in visiblePages"
              :key="page"
              @click="changePage(page)"
              :class="[
                'px-3 py-1 border-t border-b',
                page === currentPage
                  ? 'bg-green-600 text-white border-green-600'
                  : 'border-gray-300 hover:bg-gray-50'
              ]"
            >
              {{ page }}
            </button>
            <button
              @click="currentPage < totalPages && changePage(currentPage + 1)"
              :disabled="currentPage >= totalPages"
              class="px-3 py-1 border border-gray-300 rounded-r hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Next
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" @click="closeModal">
          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="w-full">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                  {{ editingProgram ? 'Edit Program Studi' : 'Tambah Program Studi' }}
                </h3>
                <form @submit.prevent="saveProgram">
                  <div class="space-y-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Kode Program Studi</label>
                      <input
                        type="text"
                        v-model="form.code"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                        placeholder="Contoh: TI"
                      >
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Nama Program Studi</label>
                      <input
                        type="text"
                        v-model="form.name"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                        placeholder="Contoh: Teknik Informatika"
                      >
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Fakultas</label>
                      <select v-model="form.faculty" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Pilih Fakultas</option>
                        <option value="Fakultas Ilmu Komputer">Fakultas Ilmu Komputer</option>
                        <option value="Fakultas Teknik">Fakultas Teknik</option>
                        <option value="Fakultas Ekonomi">Fakultas Ekonomi</option>
                        <option value="Fakultas Hukum">Fakultas Hukum</option>
                      </select>
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Jenjang</label>
                      <select v-model="form.level" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Pilih Jenjang</option>
                        <option value="S1">S1</option>
                        <option value="S2">S2</option>
                        <option value="S3">S3</option>
                      </select>
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Ketua Program Studi</label>
                      <input
                        type="text"
                        v-model="form.head_of_program"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                        placeholder="Nama Ketua Program Studi"
                      >
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                      <select v-model="form.status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="active">Aktif</option>
                        <option value="inactive">Tidak Aktif</option>
                      </select>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button @click="saveProgram" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
              {{ editingProgram ? 'Update' : 'Simpan' }}
            </button>
            <button @click="closeModal" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
              Batal
            </button>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import Layout from '@/components/Layout.vue';

// State
const programs = ref([]);
const loading = ref(false);
const showModal = ref(false);
const editingProgram = ref(null);
const selectedItems = ref([]);
const perPage = ref(10);
const currentPage = ref(1);
const total = ref(0);

// Filters
const filters = ref({
  search: '',
  faculty: '',
  status: '',
  level: ''
});

// Form
const form = ref({
  code: '',
  name: '',
  faculty: '',
  level: '',
  head_of_program: '',
  status: 'active'
});

// Stats
const stats = ref({
  total: 0,
  active: 0,
  inactive: 0,
  faculties: 0
});

// Computed
const allSelected = computed(() => {
  return programs.value.length > 0 && programs.value.every(program => selectedItems.value.includes(program.id));
});

const totalPages = computed(() => {
  return Math.ceil(total.value / perPage.value);
});

const visiblePages = computed(() => {
  const pages = [];
  const start = Math.max(1, currentPage.value - 2);
  const end = Math.min(totalPages.value, currentPage.value + 2);

  for (let i = start; i <= end; i++) {
    pages.push(i);
  }

  return pages;
});

// Methods
const fetchData = async () => {
  loading.value = true;
  try {
    // Simulate API call
    const mockData = [
      { id: 1, code: 'TI', name: 'Teknik Informatika', faculty: 'Fakultas Teknik', level: 'S1', head_of_program: 'Dr. Ahmad, M.Kom.', status: 'active' },
      { id: 2, code: 'SI', name: 'Sistem Informasi', faculty: 'Fakultas Ilmu Komputer', level: 'S1', head_of_program: 'Dr. Budi, M.T.', status: 'active' },
      { id: 3, code: 'MI', name: 'Manajemen Informatika', faculty: 'Fakultas Ilmu Komputer', level: 'D3', head_of_program: 'Ir. Siti, M.Kom.', status: 'active' },
      { id: 4, code: 'KA', name: 'Keperawatan', faculty: 'Fakultas Kesehatan', level: 'D3', head_of_program: 'Ns. Dewi, S.Kep., M.Kep.', status: 'active' },
      { id: 5, code: 'TE', name: 'Teknik Elektro', faculty: 'Fakultas Teknik', level: 'S1', head_of_program: 'Dr. Eko, M.T.', status: 'inactive' },
      { id: 6, code: 'TS', name: 'Teknik Sipil', faculty: 'Fakultas Teknik', level: 'S1', head_of_program: 'Ir. Rudi, M.T.', status: 'active' },
      { id: 7, code: 'AK', name: 'Akuntansi', faculty: 'Fakultas Ekonomi', level: 'S1', head_of_program: 'Dr. Rina, M.Ak., Ph.D.', status: 'active' },
      { id: 8, code: 'MN', name: 'Manajemen', faculty: 'Fakultas Ekonomi', level: 'S1', head_of_program: 'Dr. Hendra, M.M.', status: 'active' },
      { id: 9, code: 'HU', name: 'Hukum', faculty: 'Fakultas Hukum', level: 'S1', head_of_program: 'Dr. Ani, S.H., M.H.', status: 'active' },
      { id: 10, code: 'TI-Magister', name: 'Teknik Informatika', faculty: 'Fakultas Teknik', level: 'S2', head_of_program: 'Dr. Tono, M.T., Ph.D.', status: 'active' },
      { id: 11, code: 'MG', name: 'Magister Manajemen', faculty: 'Fakultas Ekonomi', level: 'S2', head_of_program: 'Dr. Budi Santoso, M.M.', status: 'active' },
      { id: 12, code: 'HI', name: 'Hubungan Internasional', faculty: 'Fakultas Ilmu Sosial', level: 'S1', head_of_program: 'Dr. Susi, S.IP., M.Si.', status: 'active' }
    ];

    // Apply filters
    let filteredData = mockData;
    if (filters.value.search) {
      filteredData = filteredData.filter(program =>
        program.name.toLowerCase().includes(filters.value.search.toLowerCase()) ||
        program.code.toLowerCase().includes(filters.value.search.toLowerCase())
      );
    }
    if (filters.value.faculty) {
      filteredData = filteredData.filter(program => program.faculty === filters.value.faculty);
    }
    if (filters.value.status) {
      filteredData = filteredData.filter(program => program.status === filters.value.status);
    }
    if (filters.value.level) {
      filteredData = filteredData.filter(program => program.level === filters.value.level);
    }

    // Pagination
    total.value = filteredData.length;
    const startIndex = (currentPage.value - 1) * perPage.value;
    const endIndex = startIndex + perPage.value;
    programs.value = filteredData.slice(startIndex, endIndex);

    // Update stats
    stats.value = {
      total: mockData.length,
      active: mockData.filter(p => p.status === 'active').length,
      inactive: mockData.filter(p => p.status === 'inactive').length,
      faculties: [...new Set(mockData.map(p => p.faculty))].length
    };
  } catch (error) {
    console.error('Error fetching data:', error);
  } finally {
    loading.value = false;
  }
};

const openCreateModal = () => {
  editingProgram.value = null;
  form.value = {
    code: '',
    name: '',
    faculty: '',
    level: '',
    head_of_program: '',
    status: 'active'
  };
  showModal.value = true;
};

const editProgram = (program) => {
  editingProgram.value = program;
  form.value = { ...program };
  showModal.value = true;
};

const saveProgram = async () => {
  try {
    if (editingProgram.value) {
      // Update existing program
      const index = programs.value.findIndex(p => p.id === editingProgram.value.id);
      if (index !== -1) {
        programs.value[index] = { ...form.value, id: editingProgram.value.id };
      }
      alert('Program studi berhasil diperbarui!');
    } else {
      // Create new program
      const newProgram = {
        ...form.value,
        id: programs.value.length + 1
      };
      programs.value.push(newProgram);
      alert('Program studi berhasil ditambahkan!');
    }
    closeModal();
    fetchData();
  } catch (error) {
    console.error('Error saving program:', error);
    alert('Gagal menyimpan program studi');
  }
};

const deleteProgram = (id) => {
  if (confirm('Apakah Anda yakin ingin menghapus program studi ini?')) {
    programs.value = programs.value.filter(p => p.id !== id);
    alert('Program studi berhasil dihapus!');
    fetchData();
  }
};

const closeModal = () => {
  showModal.value = false;
  editingProgram.value = null;
};

const toggleSelect = (id) => {
  const index = selectedItems.value.indexOf(id);
  if (index > -1) {
    selectedItems.value.splice(index, 1);
  } else {
    selectedItems.value.push(id);
  }
};

const toggleSelectAll = () => {
  if (allSelected.value) {
    selectedItems.value = [];
  } else {
    selectedItems.value = programs.value.map(p => p.id);
  }
};

const clearSelection = () => {
  selectedItems.value = [];
};

const bulkDelete = () => {
  if (confirm(`Apakah Anda yakin ingin menghapus ${selectedItems.value.length} program studi?`)) {
    programs.value = programs.value.filter(p => !selectedItems.value.includes(p.id));
    alert(`${selectedItems.value.length} program studi berhasil dihapus!`);
    clearSelection();
    fetchData();
  }
};

const bulkActivate = () => {
  programs.value.forEach(program => {
    if (selectedItems.value.includes(program.id)) {
      program.status = 'active';
    }
  });
  alert(`${selectedItems.value.length} program studi berhasil diaktifkan!`);
  clearSelection();
  fetchData();
};

const bulkDeactivate = () => {
  programs.value.forEach(program => {
    if (selectedItems.value.includes(program.id)) {
      program.status = 'inactive';
    }
  });
  alert(`${selectedItems.value.length} program studi berhasil dinonaktifkan!`);
  clearSelection();
  fetchData();
};

const resetFilters = () => {
  filters.value = {
    search: '',
    faculty: '',
    status: '',
    level: ''
  };
  currentPage.value = 1;
  fetchData();
};

const applyFilters = () => {
  currentPage.value = 1;
  fetchData();
};

const changePage = (page) => {
  currentPage.value = page;
  fetchData();
};

onMounted(() => {
  fetchData();
});
</script>