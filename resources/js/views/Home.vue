<template>
  <div class="min-h-screen bg-white">
    <!-- Navigation -->
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50 animate-fade-in">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
          <div class="flex items-center space-x-3">
            <div class="h-10 w-10 gradient-primary rounded-xl flex items-center justify-center animate-scale-in">
              <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
            </div>
            <span class="text-xl font-bold text-gradient">Academic Scheduler</span>
          </div>
          <div class="flex items-center space-x-4">
            <router-link to="/login" class="text-gray-600 hover:text-green-600 transition-colors">
              Login
            </router-link>
            <router-link to="/schedule" class="text-green-600 hover:text-green-700 font-medium">
              Lihat Jadwal
            </router-link>
          </div>
        </div>
      </div>
    </nav>

    <!-- Quick Access Section - Langsung ke fitur -->
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
      <!-- Quick Links -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <router-link
          to="/login"
          class="bg-white rounded-xl p-8 text-center hover-lift shadow-md border border-gray-200 animate-scale-in"
        >
          <div class="w-16 h-16 gradient-primary rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
            </svg>
          </div>
          <h3 class="text-xl font-bold text-gray-900 mb-2">Login Dosen</h3>
          <p class="text-gray-600">Kelola jadwal dan kelas Anda</p>
        </router-link>

        <router-link
          to="/schedule"
          class="bg-white rounded-xl p-8 text-center hover-lift shadow-md border border-gray-200 animate-scale-in"
          style="animation-delay: 0.1s"
        >
          <div class="w-16 h-16 gradient-secondary rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
          </div>
          <h3 class="text-xl font-bold text-gray-900 mb-2">Jadwal Lengkap</h3>
          <p class="text-gray-600">Lihat semua jadwal perkuliahan</p>
        </router-link>

        <a
          href="mailto:admin@kampus.ac.id"
          class="bg-white rounded-xl p-8 text-center hover-lift shadow-md border border-gray-200 animate-scale-in"
          style="animation-delay: 0.2s"
        >
          <div class="w-16 h-16 bg-gradient-to-r from-orange-500 to-red-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
          </div>
          <h3 class="text-xl font-bold text-gray-900 mb-2">Bantuan</h3>
          <p class="text-gray-600">Hubungi admin untuk bantuan</p>
        </a>
      </div>

      <!-- Schedule Section - Langsung ke fitur -->
      <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-8">
        <div class="text-center mb-8">
          <h2 class="text-3xl font-bold text-gradient mb-4">Jadwal Perkuliahan</h2>
          <p class="text-gray-600">Semester Ganjil 2024/2025</p>
        </div>

        <!-- Quick Filters -->
        <div class="flex flex-wrap justify-center gap-3 mb-8">
          <button
            v-for="day in days"
            :key="day.key"
            @click="selectedDay = day.key"
            :class="[
              'px-6 py-2 rounded-full font-medium transition-all hover-lift',
              selectedDay === day.key
                ? 'gradient-primary text-white shadow-lg'
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
            ]"
          >
            {{ day.name }}
          </button>
        </div>

        <!-- Schedule Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="schedule in filteredSchedules"
            :key="schedule.id"
            class="bg-gray-50 rounded-xl p-6 hover-lift border border-gray-200 animate-scale-in"
          >
            <div class="flex items-start justify-between mb-4">
              <div class="flex-1">
                <h3 class="font-bold text-lg text-gray-900 mb-1">{{ schedule.subject }}</h3>
                <p class="text-green-600 font-medium">{{ schedule.time }}</p>
              </div>
              <div class="w-12 h-12 gradient-secondary rounded-lg flex items-center justify-center">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
            </div>
            <div class="space-y-2">
              <div class="flex items-center text-sm text-gray-600">
                <svg class="h-4 w-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                {{ schedule.lecturer }}
              </div>
              <div class="flex items-center text-sm text-gray-600">
                <svg class="h-4 w-4 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                {{ schedule.room }}
              </div>
              <div class="flex items-center justify-between">
                <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                  {{ schedule.class }}
                </span>
                <span class="px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                  {{ schedule.credits }} SKS
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- View All Button -->
        <div class="text-center mt-8">
          <router-link
            to="/schedule"
            class="gradient-primary text-white px-8 py-4 rounded-xl font-semibold hover-lift text-lg shadow-lg inline-flex items-center"
          >
            Lihat Semua Jadwal
            <svg class="h-5 w-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
            </svg>
          </router-link>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
          <div class="flex items-center justify-center space-x-3 mb-4">
            <div class="h-8 w-8 gradient-primary rounded-lg flex items-center justify-center">
              <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
            </div>
            <span class="text-xl font-bold">Academic Scheduler</span>
          </div>
          <p class="text-gray-400">© 2024 Sistem Penjadwalan Akademik Kampus</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

const selectedDay = ref('all');

const days = [
  { key: 'all', name: 'Semua Hari' },
  { key: 'senin', name: 'Senin' },
  { key: 'selasa', name: 'Selasa' },
  { key: 'rabu', name: 'Rabu' },
  { key: 'kamis', name: 'Kamis' },
  { key: 'jumat', name: 'Jumat' },
];

const allSchedules = [
  { id: 1, day: 'senin', dayKey: 'senin', time: '08:00-10:00', subject: 'Pemrograman Web', lecturer: 'Dr. Ahmad, S.Kom., M.Kom.', room: 'Lab. Komputer 1', class: 'A', credits: 3 },
  { id: 2, day: 'senin', dayKey: 'senin', time: '10:00-12:00', subject: 'Basis Data', lecturer: 'Prof. Siti, S.Kom., M.T.', room: 'Ruang 301', class: 'B', credits: 3 },
  { id: 3, day: 'selasa', dayKey: 'selasa', time: '08:00-10:00', subject: 'Algoritma Struktur Data', lecturer: 'Dr. Budi, S.Kom., M.Cs.', room: 'Ruang 201', class: 'A', credits: 4 },
  { id: 4, day: 'selasa', dayKey: 'selasa', time: '13:00-15:00', subject: 'Jaringan Komputer', lecturer: 'Ir. Diana, M.T.', room: 'Lab. Jaringan', class: 'C', credits: 3 },
  { id: 5, day: 'rabu', dayKey: 'rabu', time: '08:00-10:00', subject: 'Sistem Operasi', lecturer: 'Dr. Eko, S.Kom., M.Kom.', room: 'Lab. Komputer 2', class: 'B', credits: 3 },
  { id: 6, day: 'rabu', dayKey: 'rabu', time: '10:00-12:00', subject: 'Kecerdasan Buatan', lecturer: 'Prof. Fajar, S.Kom., Ph.D.', room: 'Ruang 401', class: 'A', credits: 3 },
  { id: 7, day: 'kamis', dayKey: 'kamis', time: '08:00-10:00', subject: 'Pemrograman Mobile', lecturer: 'Dr. Grace, S.Kom., M.T.', room: 'Lab. Mobile', class: 'C', credits: 3 },
  { id: 8, day: 'kamis', dayKey: 'kamis', time: '13:00-15:00', subject: 'Keamanan Informasi', lecturer: 'Ir. Hendra, M.Kom.', room: 'Ruang 302', class: 'B', credits: 3 },
  { id: 9, day: 'jumat', dayKey: 'jumat', time: '08:00-10:00', subject: 'Rekayasa Perangkat Lunak', lecturer: 'Dr. Indah, S.Kom., M.Cs.', room: 'Ruang 401', class: 'A', credits: 3 },
  { id: 10, day: 'jumat', dayKey: 'jumat', time: '10:00-12:00', subject: 'Data Mining', lecturer: 'Prof. Joko, S.Kom., Ph.D.', room: 'Lab. Komputer 1', class: 'C', credits: 3 },
];

const filteredSchedules = computed(() => {
  if (selectedDay.value === 'all') {
    return allSchedules.slice(0, 6); // Show only 6 cards on home page
  }
  return allSchedules.filter(schedule => schedule.dayKey === selectedDay.value);
});

onMounted(() => {
  // Add staggered animation delays
  const elements = document.querySelectorAll('.animate-scale-in');
  elements.forEach((el, index) => {
    el.style.animationDelay = `${index * 0.1}s`;
  });
});
</script>