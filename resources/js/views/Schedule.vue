<template>
  <div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-blue-50">
    <!-- Navigation -->
    <nav class="bg-white/80 backdrop-blur-md border-b border-orange-100 sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
          <div class="flex items-center space-x-3">
            <div class="h-10 w-10 gradient-primary rounded-xl flex items-center justify-center">
              <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
            </div>
            <span class="text-xl font-bold text-gradient">Academic Scheduler</span>
          </div>
          <div class="flex items-center space-x-4">
            <router-link to="/" class="text-gray-600 hover:text-orange-600 transition-colors">
              Beranda
            </router-link>
            <router-link to="/login" class="gradient-primary text-white px-6 py-2 rounded-lg font-medium hover-lift">
              Login
            </router-link>
          </div>
        </div>
      </div>
    </nav>

    <!-- Schedule Content -->
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gradient mb-4">Jadwal Perkuliahan</h1>
        <p class="text-gray-600 text-lg">Semester Ganjil 2024/2025</p>
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-xl shadow-lg p-6 mb-8 border border-orange-100">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Program Studi</label>
            <select v-model="filters.program" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
              <option value="all">Semua Program</option>
              <option value="ti">Teknik Informatika</option>
              <option value="si">Sistem Informasi</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Hari</label>
            <select v-model="filters.day" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
              <option value="all">Semua Hari</option>
              <option value="senin">Senin</option>
              <option value="selasa">Selasa</option>
              <option value="rabu">Rabu</option>
              <option value="kamis">Kamis</option>
              <option value="jumat">Jumat</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Ruang</label>
            <select v-model="filters.room" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
              <option value="all">Semua Ruang</option>
              <option value="lab-komputer">Lab. Komputer</option>
              <option value="lab-jaringan">Lab. Jaringan</option>
              <option value="regular">Ruang Regular</option>
            </select>
          </div>
          <div class="flex items-end">
            <button @click="applyFilters" class="gradient-primary text-white px-6 py-2 rounded-lg font-medium hover-lift w-full">
              Terapkan Filter
            </button>
          </div>
        </div>
      </div>

      <!-- Schedule Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-12">
        <div v-for="day in days" :key="day.key" class="lg:col-span-1">
          <div class="bg-white rounded-xl shadow-lg border border-orange-100 overflow-hidden">
            <div class="gradient-primary p-4">
              <h3 class="text-lg font-semibold text-white">{{ day.name }}</h3>
            </div>
            <div class="p-4 space-y-3 max-h-96 overflow-y-auto">
              <div
                v-for="schedule in getSchedulesForDay(day.key)"
                :key="schedule.id"
                class="border-l-4 border-orange-500 bg-orange-50 p-3 rounded-r-lg"
              >
                <div class="font-semibold text-sm text-gray-900">{{ schedule.subject }}</div>
                <div class="text-xs text-gray-600 mt-1">{{ schedule.time }}</div>
                <div class="text-xs text-gray-500">{{ schedule.room }}</div>
                <div class="text-xs text-gray-500">{{ schedule.class }}</div>
              </div>
              <div v-if="getSchedulesForDay(day.key).length === 0" class="text-center text-gray-400 text-sm py-4">
                Tidak ada jadwal
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Schedule Table -->
      <div class="bg-white rounded-xl shadow-lg border border-orange-100">
        <div class="p-6 border-b border-orange-100">
          <h2 class="text-xl font-semibold text-gray-900">Detail Jadwal</h2>
        </div>
        <div class="p-6">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="text-left text-gray-600 border-b border-gray-200">
                  <th class="pb-3 font-medium">Hari</th>
                  <th class="pb-3 font-medium">Jam</th>
                  <th class="pb-3 font-medium">Mata Kuliah</th>
                  <th class="pb-3 font-medium">Dosen</th>
                  <th class="pb-3 font-medium">Ruang</th>
                  <th class="pb-3 font-medium">Kelas</th>
                  <th class="pb-3 font-medium">SKS</th>
                </tr>
              </thead>
              <tbody class="text-gray-900">
                <tr v-for="schedule in displayedSchedules" :key="schedule.id" class="border-b border-gray-100 hover:bg-gray-50">
                  <td class="py-3 font-medium">{{ schedule.day }}</td>
                  <td class="py-3">{{ schedule.time }}</td>
                  <td class="py-3 font-semibold">{{ schedule.subject }}</td>
                  <td class="py-3">{{ schedule.lecturer }}</td>
                  <td class="py-3">{{ schedule.room }}</td>
                  <td class="py-3">
                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                      {{ schedule.class }}
                    </span>
                  </td>
                  <td class="py-3">{{ schedule.credits }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';

const filters = reactive({
  program: 'all',
  day: 'all',
  room: 'all',
});

const days = [
  { key: 'senin', name: 'Senin' },
  { key: 'selasa', name: 'Selasa' },
  { key: 'rabu', name: 'Rabu' },
  { key: 'kamis', name: 'Kamis' },
  { key: 'jumat', name: 'Jumat' },
];

const allSchedules = [
  { id: 1, day: 'Senin', dayKey: 'senin', time: '08:00-10:00', subject: 'Pemrograman Web', lecturer: 'Dr. Ahmad, S.Kom., M.Kom.', room: 'Lab. Komputer 1', class: 'A', credits: 3, roomType: 'lab-komputer' },
  { id: 2, day: 'Senin', dayKey: 'senin', time: '10:00-12:00', subject: 'Basis Data', lecturer: 'Prof. Siti, S.Kom., M.T.', room: 'Ruang 301', class: 'B', credits: 3, roomType: 'regular' },
  { id: 3, day: 'Selasa', dayKey: 'selasa', time: '08:00-10:00', subject: 'Algoritma Struktur Data', lecturer: 'Dr. Budi, S.Kom., M.Cs.', room: 'Ruang 201', class: 'A', credits: 4, roomType: 'regular' },
  { id: 4, day: 'Selasa', dayKey: 'selasa', time: '13:00-15:00', subject: 'Jaringan Komputer', lecturer: 'Ir. Diana, M.T.', room: 'Lab. Jaringan', class: 'C', credits: 3, roomType: 'lab-jaringan' },
  { id: 5, day: 'Rabu', dayKey: 'rabu', time: '08:00-10:00', subject: 'Sistem Operasi', lecturer: 'Dr. Eko, S.Kom., M.Kom.', room: 'Lab. Komputer 2', class: 'B', credits: 3, roomType: 'lab-komputer' },
  { id: 6, day: 'Rabu', dayKey: 'rabu', time: '10:00-12:00', subject: 'Kecerdasan Buatan', lecturer: 'Prof. Fajar, S.Kom., Ph.D.', room: 'Ruang 401', class: 'A', credits: 3, roomType: 'regular' },
  { id: 7, day: 'Kamis', dayKey: 'kamis', time: '08:00-10:00', subject: 'Pemrograman Mobile', lecturer: 'Dr. Grace, S.Kom., M.T.', room: 'Lab. Mobile', class: 'C', credits: 3, roomType: 'lab-komputer' },
  { id: 8, day: 'Kamis', dayKey: 'kamis', time: '13:00-15:00', subject: 'Keamanan Informasi', lecturer: 'Ir. Hendra, M.Kom.', room: 'Ruang 302', class: 'B', credits: 3, roomType: 'regular' },
  { id: 9, day: 'Jumat', dayKey: 'jumat', time: '08:00-10:00', subject: 'Rekayasa Perangkat Lunak', lecturer: 'Dr. Indah, S.Kom., M.Cs.', room: 'Ruang 401', class: 'A', credits: 3, roomType: 'regular' },
  { id: 10, day: 'Jumat', dayKey: 'jumat', time: '10:00-12:00', subject: 'Data Mining', lecturer: 'Prof. Joko, S.Kom., Ph.D.', room: 'Lab. Komputer 1', class: 'C', credits: 3, roomType: 'lab-komputer' },
];

const displayedSchedules = computed(() => {
  let filtered = [...allSchedules];

  if (filters.day !== 'all') {
    filtered = filtered.filter(schedule => schedule.dayKey === filters.day);
  }

  if (filters.room !== 'all') {
    filtered = filtered.filter(schedule => schedule.roomType === filters.room);
  }

  if (filters.program !== 'all') {
    // Simple filter for demo
    filtered = filtered.filter(schedule => schedule.class === filters.program.charAt(0).toUpperCase());
  }

  return filtered;
});

const getSchedulesForDay = (dayKey) => {
  return displayedSchedules.value.filter(schedule => schedule.dayKey === dayKey);
};

const applyFilters = () => {
  // Filters are reactive, so this just triggers a re-evaluation
  console.log('Filters applied:', filters);
};
</script>