<template>
  <Layout @sidebar-collapsed="handleSidebarCollapse">
    <div class="min-h-screen bg-gray-50">
      <!-- Toast Component -->
      <Toast
        v-if="toast.show"
        :title="toast.title"
        :description="toast.description"
        :type="toast.type"
        :duration="toast.duration"
        @close="hideToast"
      />

      <!-- Statistics Cards -->
      <div class="px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Jadwal Kelas</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ stats.total_class_schedules || 0 }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Jadwal Aktif</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ stats.active_class_schedules || 0 }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Pertemuan</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ stats.total_schedules || 0 }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Hari Ini</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ stats.today_schedules || 0 }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="flex flex-col lg:flex-row gap-6 px-4 sm:px-6 lg:px-8 pb-8">
        <!-- Left Menu -->
        <div :class="['transition-all duration-300', sidebarCollapsed ? 'lg:w-20' : 'lg:w-64']">
          <div class="bg-white rounded-lg shadow">
            <div class="p-4 border-b border-gray-200">
              <h2 class="text-lg font-semibold text-gray-900" :class="sidebarCollapsed ? 'text-center' : ''">
                <span v-if="!sidebarCollapsed">Menu</span>
                <svg v-else class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
              </h2>
            </div>
            <nav class="p-2">
              <button
                v-for="menuItem in menuItems"
                :key="menuItem.key"
                @click="activeMenu = menuItem.key"
                :class="[
                  'w-full flex items-center px-3 py-2 mb-1 rounded-md text-sm font-medium transition-colors',
                  activeMenu === menuItem.key
                    ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-700'
                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                ]"
              >
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path v-if="menuItem.icon === 'class-schedule'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                  <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span v-if="!sidebarCollapsed" class="ml-3">{{ menuItem.name }}</span>
              </button>
            </nav>
          </div>
        </div>

        <!-- Main Data Area -->
        <div class="flex-1 flex flex-col min-w-0">
          <div class="bg-white rounded-lg shadow flex flex-col overflow-hidden">
            <!-- Action Buttons Above Table -->
            <div class="p-4 border-b border-gray-200">
              <div class="flex flex-col space-y-4">
                <!-- Title and Data Count -->
                <div class="flex items-center space-x-4">
                  <h3 class="text-lg font-semibold text-gray-900">
                    {{ activeMenu === 'schedules' ? 'Jadwal Kelas' : 'List Jadwal' }}
                  </h3>
                  <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">
                    {{ activeMenu === 'schedules' ? classes.length : filteredClassSchedules.length }} data
                  </span>
                </div>

                <!-- Action Buttons -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2">
                  <button
                    @click="refreshData"
                    :disabled="loading"
                    class="flex items-center justify-center px-3 py-2 text-sm bg-white border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
                  >
                    <svg class="w-4 h-4 animate-spin" v-if="loading" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <svg class="w-4 h-4" v-else fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span class="ml-2">Refresh</span>
                  </button>

                  <template v-if="activeMenu === 'schedules'">
                    <button
                      @click="openBatchScheduleModal"
                      class="flex items-center justify-center px-3 py-2 text-sm bg-blue-600 border border-transparent rounded-md text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                      </svg>
                      <span class="ml-2">Buat Jadwal Kelas</span>
                    </button>
                  </template>

                  <template v-if="activeMenu === 'class-schedules'">
                    <button
                      @click="exportSchedules"
                      class="flex items-center justify-center px-3 py-2 text-sm bg-purple-600 border border-transparent rounded-md text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                      </svg>
                      <span class="ml-2">Export</span>
                    </button>
                  </template>
                </div>
              </div>
            </div>

            <!-- Classes List Content (Jadwal Kelas) -->
            <div v-if="activeMenu === 'schedules'" class="flex-1 flex flex-col">
              <!-- Search and Filters -->
              <div class="p-4 border-b border-gray-200 bg-gray-50">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
                  <!-- Search -->
                  <div class="relative">
                    <input
                      v-model="searchQuery"
                      type="text"
                      placeholder="Cari kelas..."
                      class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm"
                    >
                    <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                  </div>

                  <!-- Program Study Filter -->
                  <select
                    v-model="filterProgramStudy"
                    class="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm"
                  >
                    <option value="">Semua Program Studi</option>
                    <option v-for="ps in programStudies" :key="ps.id" :value="ps.id">
                      {{ ps.name }}
                    </option>
                  </select>

                  <!-- Academic Year Filter -->
                  <select
                    v-model="filterAcademicYear"
                    class="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm"
                  >
                    <option value="">Semua Tahun Akademik</option>
                    <option v-for="year in academicYears" :key="year.id" :value="year.id">
                      {{ year.year }}
                    </option>
                  </select>

                  <!-- Status Filter -->
                  <select
                    v-model="filterStatus"
                    class="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm"
                  >
                    <option value="">Semua Status</option>
                    <option value="draft">Draft</option>
                    <option value="active">Aktif</option>
                    <option value="completed">Selesai</option>
                  </select>
                </div>
              </div>

              <!-- Class Schedules Table -->
              <div class="flex-1 overflow-auto">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Nama Kelas
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Kode Kelas
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Program Studi
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Level
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Kapasitas
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                      </th>
                      <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Aksi
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-if="loading">
                      <td colspan="7" class="px-6 py-12 text-center">
                        <div class="flex justify-center">
                          <svg class="animate-spin h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                          </svg>
                        </div>
                      </td>
                    </tr>
                    <tr v-else-if="filteredClasses.length === 0">
                      <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        Tidak ada data kelas
                      </td>
                    </tr>
                    <tr v-else v-for="classItem in paginatedClasses" :key="classItem.id" class="hover:bg-gray-50">
                      <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ classItem.name }}</div>
                        <div class="text-sm text-gray-500">{{ classItem.description || '-' }}</div>
                      </td>
                      <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">{{ classItem.code || '-' }}</div>
                      </td>
                      <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">{{ classItem.program_study?.name || '-' }}</div>
                      </td>
                      <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">{{ classItem.class_level || '-' }}</div>
                      </td>
                      <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">{{ classItem.capacity || '-' }}</div>
                      </td>
                      <td class="px-6 py-4">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                          Aktif
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button
                          @click="viewClassDetails(classItem)"
                          class="text-blue-600 hover:text-blue-900 mr-3"
                          title="Lihat Detail"
                        >
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                          </svg>
                        </button>
                        <button
                          @click="editClass(classItem)"
                          class="text-indigo-600 hover:text-indigo-900 mr-3"
                          title="Edit"
                        >
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                          </svg>
                        </button>
                        <button
                          @click="deleteClass(classItem)"
                          class="text-red-600 hover:text-red-900"
                          title="Hapus"
                        >
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                          </svg>
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Class Schedules List Content (List Jadwal) -->
            <div v-else class="flex-1 flex flex-col">
              <!-- Search and Filters -->
              <div class="p-4 border-b border-gray-200 bg-gray-50">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3">
                  <!-- Search -->
                  <div class="relative">
                    <input
                      v-model="scheduleSearchQuery"
                      type="text"
                      placeholder="Cari jadwal..."
                      class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm"
                    >
                    <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                  </div>

                  <!-- Date Filter -->
                  <input
                    v-model="dateFilter"
                    type="date"
                    class="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm"
                  >

                  <!-- Status Filter -->
                  <select
                    v-model="scheduleStatusFilter"
                    class="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm"
                  >
                    <option value="">Semua Status</option>
                    <option value="draft">Draft</option>
                    <option value="approved">Disetujui</option>
                    <option value="rejected">Ditolak</option>
                    <option value="cancelled">Dibatalkan</option>
                  </select>

                  <!-- Sort -->
                  <select
                    v-model="sortBy"
                    class="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm"
                  >
                    <option value="date">Urut: Tanggal</option>
                    <option value="start_time">Urut: Jam Mulai</option>
                    <option value="title">Urut: Judul</option>
                    <option value="status">Urut: Status</option>
                  </select>

                  <!-- Sort Order -->
                  <select
                    v-model="sortOrder"
                    class="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm"
                  >
                    <option value="asc">A-Z</option>
                    <option value="desc">Z-A</option>
                  </select>
                </div>
              </div>

              <!-- Schedules Table -->
              <div class="flex-1 overflow-auto">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-6 py-3 text-left">
                        <input
                          type="checkbox"
                          :checked="selectedSchedules.length === currentSchedules.length && currentSchedules.length > 0"
                          :indeterminate="selectedSchedules.length > 0 && selectedSchedules.length < currentSchedules.length"
                          @change="toggleSelectAllSchedules"
                          class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        >
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Informasi Jadwal
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Tanggal & Waktu
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Ruangan
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Dosen
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                      </th>
                      <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Aksi
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-if="scheduleLoading">
                      <td colspan="7" class="px-6 py-12 text-center">
                        <div class="flex justify-center">
                          <svg class="animate-spin h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                          </svg>
                        </div>
                      </td>
                    </tr>
                    <tr v-else-if="filteredSchedules.length === 0">
                      <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        Tidak ada data jadwal
                      </td>
                    </tr>
                    <tr v-else v-for="schedule in paginatedSchedules" :key="schedule.id" class="hover:bg-gray-50">
                      <td class="px-6 py-4 whitespace-nowrap">
                        <input
                          type="checkbox"
                          :value="schedule.id"
                          v-model="selectedSchedules"
                          class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        >
                      </td>
                      <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ schedule.title }}</div>
                        <div class="text-sm text-gray-500">{{ schedule.course?.course_code }} - {{ schedule.course?.course_name }}</div>
                        <div class="text-sm text-gray-500">{{ schedule.schedule_code }}</div>
                      </td>
                      <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">{{ formatDate(schedule.date) }}</div>
                        <div class="text-sm text-gray-500">{{ formatTime(schedule.start_time) }} - {{ formatTime(schedule.end_time) }}</div>
                      </td>
                      <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">{{ schedule.room?.room_code || 'Online' }}</div>
                        <div class="text-sm text-gray-500">{{ schedule.room?.name || 'Online Meeting' }}</div>
                      </td>
                      <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">{{ schedule.lecturer?.name || '-' }}</div>
                      </td>
                      <td class="px-6 py-4">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                          :class="getScheduleStatusColor(schedule.status)">
                          {{ getScheduleStatusText(schedule.status) }}
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button
                          @click="viewSchedule(schedule)"
                          class="text-blue-600 hover:text-blue-900 mr-3"
                          title="Lihat Detail"
                        >
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                          </svg>
                        </button>
                        <button
                          @click="editSchedule(schedule)"
                          class="text-indigo-600 hover:text-indigo-900 mr-3"
                          title="Edit"
                        >
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                          </svg>
                        </button>
                        <button
                          @click="deleteSchedule(schedule)"
                          class="text-red-600 hover:text-red-900"
                          title="Hapus"
                        >
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                          </svg>
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
              <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                  Menampilkan {{ startIndex + 1 }} sampai {{ endIndex }} dari {{ totalItems }} hasil
                </div>
                <div class="flex gap-2">
                  <button
                    @click="previousPage"
                    :disabled="currentPage === 1"
                    class="px-3 py-1 text-sm bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    Sebelumnya
                  </button>
                  <button
                    @click="nextPage"
                    :disabled="currentPage === totalPages"
                    class="px-3 py-1 text-sm bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    Selanjutnya
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Class Schedule Form Modal -->
    <ClassScheduleFormModal
      v-if="showClassScheduleModal"
      :show="showClassScheduleModal"
      :class-schedule="editingClassSchedule"
      @close="closeClassScheduleModal"
      @success="handleClassScheduleSuccess"
    />

    <!-- Batch Schedule Modal -->
    <BatchScheduleModal
      v-if="showBatchScheduleModal"
      :show="showBatchScheduleModal"
      @close="closeBatchScheduleModal"
      @success="handleBatchScheduleSuccess"
    />
  </Layout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import Layout from '@/components/Layout.vue';
import Toast from '@/components/Toast.vue';
import ClassScheduleFormModal from '@/components/modals/ClassScheduleFormModal.vue';
import BatchScheduleModal from '@/components/modals/BatchScheduleModal.vue';

// State management
const loading = ref(false);
const scheduleLoading = ref(false);
const classes = ref([]);
const classSchedules = ref([]);
const schedules = ref([]);
const stats = ref({});
const programStudies = ref([]);
const academicYears = ref([]);
const sidebarCollapsed = ref(false);

// Modal state
const showClassScheduleModal = ref(false);
const showBatchScheduleModal = ref(false);
const editingClassSchedule = ref(null);

// Menu and tabs
const activeMenu = ref('class-schedules');

// Search and filters for class schedules
const searchQuery = ref('');
const filterProgramStudy = ref('');
const filterAcademicYear = ref('');
const filterStatus = ref('');

// Search and filters for schedules
const scheduleSearchQuery = ref('');
const dateFilter = ref('');
const scheduleStatusFilter = ref('');
const sortBy = ref('date');
const sortOrder = ref('asc');

// Pagination
const currentPage = ref(1);
const itemsPerPage = ref(10);

// Bulk selection for schedules
const selectedSchedules = ref([]);

// Toast
const toast = ref({
  show: false,
  title: '',
  description: '',
  type: 'success',
  duration: 3000
});

const menuItems = [
  { key: 'schedules', name: 'Jadwal Kelas', icon: 'class-schedule' },
  { key: 'class-schedules', name: 'List Jadwal', icon: 'schedule' },
];

// Computed properties for classes
const filteredClasses = computed(() => {
  let filtered = Array.isArray(classes.value) ? classes.value : [];

  // Search
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(item =>
      item.name?.toLowerCase().includes(query) ||
      item.code?.toLowerCase().includes(query) ||
      item.program_study?.name?.toLowerCase().includes(query)
    );
  }

  // Program Study Filter
  if (filterProgramStudy.value) {
    filtered = filtered.filter(item => item.program_study_id === filterProgramStudy.value);
  }

  return filtered;
});

const paginatedClasses = computed(() => {
  return filteredClasses.value;
});

// Computed properties for class schedules
const filteredClassSchedules = computed(() => {
  let filtered = Array.isArray(classSchedules.value) ? classSchedules.value : [];

  // Search
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(item =>
      item.title?.toLowerCase().includes(query) ||
      item.schedule_code?.toLowerCase().includes(query) ||
      item.program_study?.name?.toLowerCase().includes(query)
    );
  }

  // Filters
  if (filterProgramStudy.value) {
    filtered = filtered.filter(item => item.program_study_id == filterProgramStudy.value);
  }

  if (filterAcademicYear.value) {
    filtered = filtered.filter(item => item.academic_year_id == filterAcademicYear.value);
  }

  if (filterStatus.value) {
    filtered = filtered.filter(item => item.status === filterStatus.value);
  }

  return filtered;
});

// Computed properties for schedules
const filteredSchedules = computed(() => {
  let filtered = Array.isArray(schedules.value) ? schedules.value : [];

  // Search
  if (scheduleSearchQuery.value) {
    const query = scheduleSearchQuery.value.toLowerCase();
    filtered = filtered.filter(item =>
      item.title?.toLowerCase().includes(query) ||
      item.schedule_code?.toLowerCase().includes(query) ||
      item.course?.course_name?.toLowerCase().includes(query) ||
      item.course?.course_code?.toLowerCase().includes(query)
    );
  }

  // Date filter
  if (dateFilter.value) {
    filtered = filtered.filter(item => item.date === dateFilter.value);
  }

  // Status filter
  if (scheduleStatusFilter.value) {
    filtered = filtered.filter(item => item.status === scheduleStatusFilter.value);
  }

  // Sort
  filtered.sort((a, b) => {
    let aValue, bValue;

    switch (sortBy.value) {
      case 'date':
        aValue = new Date(a.date);
        bValue = new Date(b.date);
        break;
      case 'start_time':
        aValue = a.start_time;
        bValue = b.start_time;
        break;
      case 'title':
        aValue = a.title || '';
        bValue = b.title || '';
        break;
      case 'status':
        aValue = a.status || '';
        bValue = b.status || '';
        break;
      default:
        aValue = new Date(a.date);
        bValue = new Date(b.date);
    }

    if (sortOrder.value === 'asc') {
      return aValue > bValue ? 1 : -1;
    } else {
      return aValue < bValue ? 1 : -1;
    }
  });

  return filtered;
});

// Pagination computed
const currentItems = computed(() => {
  return activeMenu.value === 'class-schedules' ? filteredClassSchedules.value : filteredSchedules.value;
});

const paginatedClassSchedules = computed(() => {
  if (activeMenu.value !== 'class-schedules') return [];
  const start = (currentPage.value - 1) * itemsPerPage.value;
  const end = start + itemsPerPage.value;
  return filteredClassSchedules.value.slice(start, end);
});

const paginatedSchedules = computed(() => {
  if (activeMenu.value !== 'schedules') return [];
  const start = (currentPage.value - 1) * itemsPerPage.value;
  const end = start + itemsPerPage.value;
  return filteredSchedules.value.slice(start, end);
});

const totalPages = computed(() => {
  return Math.ceil(currentItems.value.length / itemsPerPage.value);
});

const startIndex = computed(() => {
  return (currentPage.value - 1) * itemsPerPage.value;
});

const endIndex = computed(() => {
  return Math.min(startIndex.value + itemsPerPage.value, currentItems.value.length);
});

const totalItems = computed(() => {
  return currentItems.value.length;
});

const currentSchedules = computed(() => {
  return filteredSchedules.value;
});

// Helper functions
const formatDate = (dateString) => {
  if (!dateString) return '-';
  const date = new Date(dateString);
  return date.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  });
};

const formatTime = (timeString) => {
  if (!timeString) return '-';
  return timeString.substring(0, 5);
};

const getStatusColor = (status) => {
  switch (status) {
    case 'draft': return 'bg-gray-100 text-gray-800';
    case 'active': return 'bg-green-100 text-green-800';
    case 'completed': return 'bg-blue-100 text-blue-800';
    case 'cancelled': return 'bg-red-100 text-red-800';
    default: return 'bg-gray-100 text-gray-800';
  }
};

const getStatusText = (status) => {
  switch (status) {
    case 'draft': return 'Draft';
    case 'active': return 'Aktif';
    case 'completed': return 'Selesai';
    case 'cancelled': return 'Dibatalkan';
    default: return status || '-';
  }
};

const getScheduleStatusColor = (status) => {
  switch (status) {
    case 'draft': return 'bg-gray-100 text-gray-800';
    case 'approved': return 'bg-green-100 text-green-800';
    case 'rejected': return 'bg-red-100 text-red-800';
    case 'cancelled': return 'bg-orange-100 text-orange-800';
    default: return 'bg-gray-100 text-gray-800';
  }
};

const getScheduleStatusText = (status) => {
  switch (status) {
    case 'draft': return 'Draft';
    case 'approved': return 'Disetujui';
    case 'rejected': return 'Ditolak';
    case 'cancelled': return 'Dibatalkan';
    default: return status || '-';
  }
};

// API calls
const loadClassSchedules = async () => {
  loading.value = true;
  try {
    const response = await fetch('/api/class-schedules', {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'application/json'
      }
    });
    const data = await response.json();
    classSchedules.value = data.data || [];
  } catch (error) {
    console.error('Error loading class schedules:', error);
    showToast('Error', 'Gagal memuat data jadwal kelas', 'error');
  } finally {
    loading.value = false;
  }
};

const loadSchedules = async () => {
  scheduleLoading.value = true;
  try {
    const response = await fetch('/api/schedules', {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'application/json'
      }
    });
    const data = await response.json();
    schedules.value = data.data || [];
  } catch (error) {
    console.error('Error loading schedules:', error);
    showToast('Error', 'Gagal memuat data jadwal', 'error');
  } finally {
    scheduleLoading.value = false;
  }
};

const loadStatistics = async () => {
  try {
    const response = await fetch('/api/schedules/statistics', {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'application/json'
      }
    });
    const data = await response.json();
    stats.value = data.data || {};
  } catch (error) {
    console.error('Error loading statistics:', error);
  }
};

const loadProgramStudies = async () => {
  try {
    const response = await fetch('/api/program-studies', {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'application/json'
      }
    });
    const data = await response.json();
    programStudies.value = data.data || [];
  } catch (error) {
    console.error('Error loading program studies:', error);
  }
};

const loadAcademicYears = async () => {
  try {
    const response = await fetch('/api/academic-years', {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'application/json'
      }
    });
    const data = await response.json();
    academicYears.value = data.data || [];
  } catch (error) {
    console.error('Error loading academic years:', error);
  }
};

const refreshData = async () => {
  await Promise.all([
    loadClassSchedules(),
    loadSchedules(),
    loadStatistics()
  ]);
};

const showToast = (title, description, type = 'success') => {
  toast.value = {
    show: true,
    title,
    description,
    type,
    duration: 3000
  };
};

const hideToast = () => {
  toast.value.show = false;
};

// Methods
const openCreateClassScheduleModal = () => {
  editingClassSchedule.value = null;
  showClassScheduleModal.value = true;
};

const openBatchScheduleModal = () => {
  showBatchScheduleModal.value = true;
};

const closeClassScheduleModal = () => {
  showClassScheduleModal.value = false;
  editingClassSchedule.value = null;
};

const closeBatchScheduleModal = () => {
  showBatchScheduleModal.value = false;
};

const handleClassScheduleSuccess = async () => {
  closeClassScheduleModal();
  await refreshData();
  showToast('Success', 'Jadwal kelas berhasil disimpan', 'success');
};

const handleBatchScheduleSuccess = async () => {
  closeBatchScheduleModal();
  await refreshData();
  showToast('Success', 'Jadwal berhasil dibuat', 'success');
};

const viewClassScheduleDetails = (classSchedule) => {
  // TODO: Navigate to class schedule details
  console.log('View class schedule details:', classSchedule);
};

const editClassSchedule = (classSchedule) => {
  editingClassSchedule.value = classSchedule;
  showClassScheduleModal.value = true;
};

const deleteClassSchedule = (classSchedule) => {
  // TODO: Implement delete class schedule
  console.log('Delete class schedule:', classSchedule);
};

const viewSchedule = (schedule) => {
  // TODO: Navigate to schedule details
  console.log('View schedule:', schedule);
};

const editSchedule = (schedule) => {
  // TODO: Navigate to edit schedule
  console.log('Edit schedule:', schedule);
};

const deleteSchedule = (schedule) => {
  // TODO: Implement delete schedule
  console.log('Delete schedule:', schedule);
};

const exportSchedules = () => {
  // TODO: Implement export schedules
  console.log('Export schedules');
};

const toggleSelectAllSchedules = () => {
  if (selectedSchedules.value.length === currentSchedules.value.length) {
    selectedSchedules.value = [];
  } else {
    selectedSchedules.value = currentSchedules.value.map(item => item.id);
  }
};

const previousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--;
  }
};

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++;
  }
};

// Handle sidebar collapse event from Layout component
const handleSidebarCollapse = (collapsed) => {
  sidebarCollapsed.value = collapsed;
};

// Lifecycle
onMounted(async () => {
  // Load sidebar state from localStorage
  const saved = localStorage.getItem('sidebar-collapsed');
  if (saved !== null) {
    sidebarCollapsed.value = saved === 'true';
  }

  await Promise.all([
    loadClassSchedules(),
    loadSchedules(),
    loadStatistics(),
    loadProgramStudies(),
    loadAcademicYears()
  ]);
});

// Watch for menu changes to reset pagination
watch(activeMenu, () => {
  currentPage.value = 1;
  selectedSchedules.value = [];
});
</script>

<style scoped>
.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}
</style>