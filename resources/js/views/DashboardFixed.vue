<template>
  <Layout>
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 hover-lift">
        <div class="flex items-center">
          <div class="p-3 bg-green-100 rounded-lg">
            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Total Jadwal</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.totalSchedules }}</p>
            <p class="text-xs text-green-600 mt-1">+12% dari bulan lalu</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 hover-lift">
        <div class="flex items-center">
          <div class="p-3 bg-green-100 rounded-lg">
            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Mata Kuliah</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.totalCourses }}</p>
            <p class="text-xs text-green-600 mt-1">+3 mata kuliah baru</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 hover-lift">
        <div class="flex items-center">
          <div class="p-3 bg-green-100 rounded-lg">
            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Mahasiswa</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.totalStudents }}</p>
            <p class="text-xs text-green-600 mt-1">234 aktif</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 hover-lift">
        <div class="flex items-center">
          <div class="p-3 bg-green-100 rounded-lg">
            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Ruangan</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.totalRooms }}</p>
            <p class="text-xs text-green-600 mt-1">85% terisi</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Aksi Cepat</h3>
        <div class="space-y-3">
          <button class="w-full text-left p-3 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors flex items-center">
            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Tambah Jadwal Baru
          </button>
          <button class="w-full text-left p-3 bg-gray-50 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors flex items-center">
            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            Tambah Mahasiswa
          </button>
          <button class="w-full text-left p-3 bg-gray-50 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors flex items-center">
            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002 2m-6 0a2 2 0 002 2v-10a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2m-6 0a1 1 0 001-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            Lihat Laporan
          </button>
        </div>
      </div>

      <!-- Upcoming Schedule -->
      <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 md:col-span-2">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Jadwal Hari Ini</h3>
        <div class="space-y-3">
          <div v-for="schedule in todaySchedule" :key="schedule.id" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
            <div class="flex items-center space-x-4">
              <div class="text-center">
                <p class="text-2xl font-bold text-green-600">{{ schedule.time.split(':')[0] }}</p>
                <p class="text-xs text-gray-500">{{ schedule.time.split(':')[1] }}</p>
              </div>
              <div>
                <p class="font-medium text-gray-900">{{ schedule.course }}</p>
                <p class="text-sm text-gray-600">{{ schedule.lecturer }}</p>
              </div>
            </div>
            <div class="text-right">
              <p class="text-sm font-medium text-gray-900">{{ schedule.room }}</p>
              <span :class="schedule.status === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'" class="px-2 py-1 rounded-full text-xs font-medium">
                {{ schedule.status === 'confirmed' ? 'Aktif' : 'Pending' }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
      <div class="p-6 border-b border-gray-200">
        <div class="flex justify-between items-center">
          <h2 class="text-xl font-semibold text-gray-900">Aktivitas Terbaru</h2>
          <button class="text-sm text-green-600 hover:text-green-700 font-medium">
            Lihat Semua
          </button>
        </div>
      </div>
      <div class="p-6">
        <div class="space-y-4">
          <div v-for="activity in recentActivities" :key="activity.id" class="flex items-start space-x-3">
            <div :class="getActivityIconClass(activity.type)" class="p-2 rounded-lg flex-shrink-0">
              <svg v-if="activity.type === 'schedule'" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <svg v-else-if="activity.type === 'student'" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              <svg v-else-if="activity.type === 'course'" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              </svg>
              <svg v-else-if="activity.type === 'room'" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
              <svg v-else-if="activity.type === 'user'" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM12 14a7 7 0 00-7 7h14a7 7 0 007-7z" />
              </svg>
            </div>
            <div class="flex-1">
              <p class="text-sm text-gray-900">{{ activity.description }}</p>
              <p class="text-xs text-gray-500 mt-1">{{ formatTime(activity.createdAt) }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';
import Layout from '@/components/Layout.vue';

const authStore = useAuthStore();

const stats = ref({
  totalSchedules: 156,
  totalCourses: 42,
  totalStudents: 256,
  totalRooms: 15
});

const todaySchedule = ref([
  {
    id: 1,
    time: '08:00',
    course: 'Pemrograman Web',
    lecturer: 'Dr. Ahmad, S.Kom., M.Kom.',
    room: 'Lab. Komputer 1',
    status: 'confirmed'
  },
  {
    id: 2,
    time: '10:00',
    course: 'Basis Data',
    lecturer: 'Prof. Siti, S.Kom., M.T.',
    room: 'Ruang 301',
    status: 'confirmed'
  },
  {
    id: 3,
    time: '13:00',
    course: 'Algoritma Struktur Data',
    lecturer: 'Dr. Budi, S.Kom., M.Cs.',
    room: 'Ruang 201',
    status: 'pending'
  },
  {
    id: 4,
    time: '15:00',
    course: 'Jaringan Komputer',
    lecturer: 'Ir. Diana, M.T.',
    room: 'Lab. Jaringan',
    status: 'confirmed'
  }
]);

const recentActivities = ref([
  {
    id: 1,
    type: 'schedule',
    description: 'Jadwal mata kuliah Pemrograman Web berhasil ditambahkan',
    createdAt: new Date(Date.now() - 1000 * 60 * 5)
  },
  {
    id: 2,
    type: 'student',
    description: 'Data mahasiswa baru berhasil ditambahkan: Ahmad Rizki',
    createdAt: new Date(Date.now() - 1000 * 60 * 30)
  },
  {
    id: 3,
    type: 'course',
    description: 'Mata kuliah Kecerdasan Buatan berhasil diupdate',
    createdAt: new Date(Date.now() - 1000 * 60 * 60)
  },
  {
    id: 4,
    type: 'room',
    description: 'Ruangan Lab Komputer 2 telah dibooking',
    createdAt: new Date(Date.now() - 1000 * 60 * 60 * 2)
  },
  {
    id: 5,
    type: 'user',
    description: 'User baru dengan role Dosen telah dibuat',
    createdAt: new Date(Date.now() - 1000 * 60 * 60 * 3)
  }
]);

const getActivityIcon = (type) => {
  if (type === 'schedule') {
    return 'calendar';
  } else if (type === 'student') {
    return 'users';
  } else if (type === 'course') {
    return 'book';
  } else if (type === 'room') {
    return 'home';
  } else if (type === 'user') {
    return 'user';
  }
  return 'calendar';
};

const getActivityIconClass = (type) => {
  if (type === 'schedule') {
    return 'bg-green-100 text-green-600';
  } else if (type === 'student') {
    return 'bg-blue-100 text-blue-600';
  } else if (type === 'course') {
    return 'bg-purple-100 text-purple-600';
  } else if (type === 'room') {
    return 'bg-yellow-100 text-yellow-600';
  } else if (type === 'user') {
    return 'bg-indigo-100 text-indigo-600';
  }
  return 'bg-green-100 text-green-600';
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

onMounted(async () => {
  // Load dashboard data from API
  try {
    // Fetch dashboard statistics
    // const response = await axios.get('/api/dashboard');
    // stats.value = response.data;
  } catch (error) {
    console.error('Error loading dashboard data:', error);
  }
});
</script>