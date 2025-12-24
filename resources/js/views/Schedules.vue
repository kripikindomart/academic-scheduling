<template>
  <Layout>
    <div class="min-h-screen bg-gray-50">
      <!-- Toast Component -->
      <div
        v-for="toast in toastStore.toasts"
        :key="toast.id"
        class="fixed bottom-4 right-4 z-50 animate-pulse"
      >
        <Toast
          :title="toast.title"
          :message="toast.message"
          :type="toast.type"
          :duration="toast.duration"
          @close="toastStore.removeToast(toast.id)"
        />
      </div>

      <!-- Auth Error State -->
      <div v-if="!isAuthenticated" class="flex items-center justify-center min-h-screen">
        <div class="text-center p-8 bg-white rounded-2xl shadow-xl max-w-md">
          <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <h2 class="text-xl font-bold text-gray-900 mb-2">Authentication Required</h2>
          <p class="text-gray-600 mb-6">Please login first to access the schedule management system.</p>
          <button
            @click="$router.push('/login')"
            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            Go to Login
          </button>
        </div>
      </div>

      <!-- Content only show if authenticated -->
      <div v-else class="px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <!-- Total Schedules Card -->
          <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 opacity-10">
              <svg class="h-24 w-24 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
              </svg>
            </div>
            <div class="relative p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-12 h-12 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dt class="text-sm font-medium text-blue-100">Total Jadwal</dt>
                  <dd class="text-3xl font-bold text-white">{{ scheduleStats.total_schedules || 0 }}</dd>
                  <div class="flex items-center mt-1">
                    <svg class="w-4 h-4 text-blue-200 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-xs text-blue-100">Semua Jadwal</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Active Schedules Card -->
          <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-green-500 to-green-600 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 opacity-10">
              <svg class="h-24 w-24 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="relative p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-12 h-12 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dt class="text-sm font-medium text-green-100">Jadwal Aktif</dt>
                  <dd class="text-3xl font-bold text-white">{{ scheduleStats.by_status?.Aktif || 0 }}</dd>
                  <div class="flex items-center mt-1">
                    <svg class="w-4 h-4 text-green-200 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-xs text-green-100">Sedang berjalan</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- On Leave Card -->
          <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 opacity-10">
              <svg class="h-24 w-24 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="relative p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-12 h-12 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dt class="text-sm font-medium text-amber-100">Jadwal Draft</dt>
                  <dd class="text-3xl font-bold text-white">{{ scheduleStats.by_status?.draft || 0 }}</dd>
                  <div class="flex items-center mt-1">
                    <svg class="w-4 h-4 text-amber-200 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-xs text-amber-100">Menunggu persetujuan</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Inactive Card -->
          <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-red-500 to-red-600 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 opacity-10">
              <svg class="h-24 w-24 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="relative p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-12 h-12 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dt class="text-sm font-medium text-red-100">Jadwal Selesai</dt>
                  <dd class="text-3xl font-bold text-white">{{ scheduleStats.by_status?.completed || 0 }}</dd>
                  <div class="flex items-center mt-1">
                    <svg class="w-4 h-4 text-red-200 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-xs text-red-100">Selesai diproses</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="px-4 sm:px-6 lg:px-8 pb-8">
        <!-- Modern Search and Filters -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 mb-6 overflow-hidden">
          <div class="p-6">
            <div class="flex flex-col gap-6">
              <!-- Enhanced Search -->
              <div class="w-full">
                <label class="block text-sm font-medium text-gray-700 mb-2">Pencarian Jadwal</label>
                <div class="relative group">
                  <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                  </div>
                  <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Cari judul, kode, kelas, atau program studi..."
                    class="block w-full pl-12 pr-12 py-2.5 border-2 border-gray-200 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all duration-200 hover:border-gray-300"
                  />
                  <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <div v-if="searchQuery" class="flex items-center">
                      <span class="text-xs text-gray-500 mr-2">{{ schedules.length }} hasil</span>
                      <button
                        @click="searchQuery = ''"
                        class="text-gray-400 hover:text-gray-600 transition-colors"
                      >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Enhanced Filters -->
              <div class="w-full">
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter</label>
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
                  <div class="relative">
                     <select v-model="selectedDepartment" class="appearance-none w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">Semua Jurusan</option>
                        <option v-for="dept in departments" :key="dept" :value="dept">{{ dept }}</option>
                     </select>
                  </div>

                  <div class="relative">
                     <SearchableSelect
                       v-model="selectedAcademicYear"
                       :options="academicYears"
                       label=""
                       placeholder="Semua Tahun Akademik"
                     />
                  </div>



                  <div class="relative">
                     <select v-model="selectedStatus" class="appearance-none w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">Semua Status</option>
                        <option value="draft">Draft</option>
                        <option value="active">Aktif</option>
                        <option value="completed">Selesai</option>
                        <option value="cancelled">Dibatalkan</option>
                     </select>
                  </div>

                  <div class="relative">
                     <select v-model="selectedCourse" class="appearance-none w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">Semua Mata Kuliah</option>
                        <option v-for="course in courses" :key="course.id" :value="course.id">{{ course.course_name }}</option>
                     </select>
                  </div>

                  <div class="relative">
                     <select v-model="selectedClass" class="appearance-none w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">Semua Kelas</option>
                        <option v-for="cls in classes" :key="cls.id" :value="cls.id">{{ cls.name }}</option>
                     </select>
                  </div>

                  <div class="relative">
                     <input type="date" v-model="dateFrom" class="appearance-none w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Dari Tanggal">
                  </div>

                  <div class="relative">
                     <input type="date" v-model="dateTo" class="appearance-none w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Sampai Tanggal">
                  </div>

                  <button
                    @click="refreshData"
                    :disabled="loading"
                    class="w-full px-3 py-2 bg-gradient-to-r from-gray-50 to-gray-100 text-gray-700 rounded-lg hover:from-gray-100 hover:to-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 disabled:opacity-50 font-medium text-sm transition-all duration-200 border-2 border-gray-200 hover:border-gray-300 flex items-center justify-center"
                  >
                    <svg v-if="loading" class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <svg v-else class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Refresh
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div v-if="activeTab === 'active'" class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-t border-gray-200">
            <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
              <div class="flex flex-wrap gap-2">
                <button
                  @click="showAddModal = true"
                  class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-medium rounded-lg hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 text-sm"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                  </svg>
                  Tambah Jadwal Kelas
                </button>

                <button
                  @click="enrollCourse(null)"
                  :disabled="schedules.length === 0"
                  class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white font-medium rounded-lg hover:from-green-600 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                  </svg>
                  Enroll Matakuliah
                </button>

                <button
                  @click="autoSchedule(null)"
                  :disabled="schedules.length === 0"
                  class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white font-medium rounded-lg hover:from-indigo-600 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                  </svg>
                  Generate Data
                </button>

                <button
                  @click="showImportModal = true"
                  class="inline-flex items-center px-4 py-2 bg-white text-gray-700 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200 border-2 border-gray-200 hover:border-gray-300 text-sm"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                  </svg>
                  Import
                </button>

                <button
                  @click="exportData"
                  class="inline-flex items-center px-4 py-2 bg-white text-gray-700 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200 border-2 border-gray-200 hover:border-gray-300 text-sm"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                  </svg>
                  Export
                </button>
              </div>

              <!-- Quick Stats -->
              <div class="flex items-center space-x-4 text-sm text-gray-600">
                <div class="flex items-center">
                  <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                  <span>{{ pagination.total }} total</span>
                </div>
                <div class="flex items-center">
                  <div class="w-2 h-2 bg-blue-500 rounded-full mr-2"></div>
                  <span>{{ pagination.current_page }}/{{ pagination.last_page }} hal</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Modern Table Container with proper overflow handling -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
          <!-- Tab Navigation -->
          <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
              <button
                @click="switchTab('active')"
                :class="[
                  'py-3 px-6 text-sm font-medium border-b-2 transition-all duration-200',
                  activeTab === 'active'
                    ? 'border-blue-500 text-blue-600 bg-blue-50'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                ]"
              >
                <div class="flex items-center">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                  </svg>
                  Jadwal Kelas
                  <span v-if="activeTab === 'active'" class="ml-2 px-2 py-0.5 text-xs bg-blue-100 text-blue-600 rounded-full">
                    {{ pagination.total }}
                  </span>
                </div>
              </button>

              <button
                @click="switchTab('meetings')"
                :class="[
                  'py-3 px-6 text-sm font-medium border-b-2 transition-all duration-200',
                  activeTab === 'meetings'
                    ? 'border-purple-500 text-purple-600 bg-purple-50'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                ]"
              >
                <div class="flex items-center">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                  Jadwal Perkuliahan
                  <span v-if="activeTab === 'meetings'" class="ml-2 px-2 py-0.5 text-xs bg-purple-100 text-purple-600 rounded-full">
                    {{ pagination.total }}
                  </span>
                </div>
              </button>

              <button
                @click="switchTab('trash')"
                :class="[
                  'py-3 px-6 text-sm font-medium border-b-2 transition-all duration-200',
                  activeTab === 'trash'
                    ? 'border-red-500 text-red-600 bg-red-50'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                ]"
              >
                <div class="flex items-center">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                  </svg>
                  Sampah
                  <span v-if="trashCount > 0" class="ml-2 px-2 py-0.5 text-xs bg-red-100 text-red-600 rounded-full">
                    {{ trashCount }}
                  </span>
                </div>
              </button>
            </nav>
          </div>

          <!-- Table Header -->
          <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10"
                     :class="[
                       'rounded-lg flex items-center justify-center',
                       activeTab === 'active' ? 'bg-blue-500' : 'bg-red-500'
                     ]"
                >
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path v-if="activeTab === 'active'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 2v6m4-6v6m5 5H2m0 0v7m0-13a2 2 0 012-2h10a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V4z"/>
                  </svg>
                </div>
                <div>
                  <h2 class="text-xl font-bold text-gray-900">
                    {{ activeTab === 'active' ? 'Data Jadwal Kelas' : (activeTab === 'meetings' ? 'Jadwal Perkuliahan' : 'Sampah Jadwal') }}
                  </h2>
                  <p class="text-sm text-gray-500">
                    {{ activeTab === 'active' ? 'Kelola jadwal kelas dan konfigurasi' : (activeTab === 'meetings' ? 'Kelola pertemuan kuliah dan ruang kelas' : 'Data jadwal yang telah dihapus') }}
                  </p>
                </div>
              </div>

              <!-- Selected Items Counter -->
              <div v-if="selectedItems.length > 0" class="flex items-center space-x-3">
                <span class="text-sm font-medium text-gray-700">
                  {{ selectedItems.length }} jadwal dipilih
                </span>
                <div class="h-6 w-px bg-gray-300"></div>
                <div class="flex items-center space-x-2">
                  <!-- Actions for Active Tab -->
                  <template v-if="activeTab === 'active'">
                    <button
                      @click="bulkCreateUserAccounts"
                      class="px-3 py-1 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors text-sm font-medium"
                    >
                      Buat Akun Pengguna
                    </button>
                    <button
                      @click="confirmBulkDelete"
                      class="px-3 py-1 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors text-sm font-medium"
                    >
                      Hapus
                    </button>
                    <button
                      @click="bulkToggleStatus"
                      class="px-3 py-1 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors text-sm font-medium"
                    >
                      Toggle Status
                    </button>
                  </template>

                  <!-- Actions for Meetings Tab -->
                  <template v-else-if="activeTab === 'meetings'">
                    <button
                      @click="confirmBulkDeleteMeetings"
                      class="px-3 py-1 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors text-sm font-medium"
                    >
                      Hapus Pertemuan
                    </button>
                  </template>

                  <!-- Actions for Trash Tab -->
                  <template v-else-if="activeTab === 'trash'">
                    <button
                      @click="confirmBulkRestore"
                      class="px-3 py-1 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors text-sm font-medium"
                    >
                      Pulihkan
                    </button>
                    <button
                      @click="confirmBulkForceDelete"
                      class="px-3 py-1 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors text-sm font-medium"
                    >
                      Hapus Permanen
                    </button>
                  </template>

                  <button
                    @click="selectedItems = []"
                    class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium"
                  >
                    Batal
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Loading State -->
          <div v-if="loading" class="flex flex-col items-center justify-center py-16">
            <div class="relative">
              <svg class="animate-spin h-12 w-12 text-blue-500" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <div class="absolute inset-0 rounded-full bg-blue-100 opacity-20 animate-ping"></div>
            </div>
            <span class="mt-4 text-gray-600 font-medium">Memuat data jadwal...</span>
            <div class="text-xs text-gray-500 mt-2">Debug: {{ schedules.length }} items loaded</div>
          </div>

          <!-- Empty State -->
          <div v-else-if="!loading && schedules.length === 0" class="text-center py-16">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
              </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">
              {{ activeTab === 'active' ? 'Belum ada data jadwal' : 'Tidak ada data sampah' }}
            </h3>
            <p class="text-gray-500 mb-6 max-w-sm mx-auto">
              {{ activeTab === 'active' ? 'Mulai dengan menambahkan jadwal kelas baru' : 'Tidak ada jadwal yang telah dihapus' }}
            </p>
            <button
              v-if="activeTab === 'active'"
              @click="showAddModal = true"
              class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-medium rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 text-sm"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
              </svg>
              Tambah Jadwal
            </button>
          </div>

          <!-- Modern Data Table -->
          <div v-show="!loading && schedules.length > 0" class="w-full">
            <!-- Pagination Controls Top -->
            <div class="flex justify-between items-center mb-4 px-2">
              <!-- Show entries dropdown -->
              <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-600">Show</span>
                <select
                  v-model="pagination.per_page"
                  @change="changePerPage($event.target.value)"
                  class="px-3 py-1 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                  <option v-for="option in perPageOptions" :key="option" :value="option">
                    {{ option }}
                  </option>
                </select>
                <span class="text-sm text-gray-600">entries</span>
              </div>

              <!-- Showing info -->
              <div class="text-sm text-gray-600">
                <span v-if="pagination.total > 0">
                  Showing {{ pagination.from || 1 }} to {{ pagination.to || schedules.length }} of {{ pagination.total }} results
                </span>
                <span v-else>
                  Showing 0 to 0 of 0 results
                </span>
              </div>
            </div>

            <!-- Table container with modern scrolling -->
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                  <!-- Header for Meetings Tab -->
                  <!-- Header for Meetings & Conflicts Tab -->
                  <tr v-if="activeTab === 'meetings' || activeTab === 'conflicts'">
                    <th scope="col" class="px-6 py-4 text-left">
                      <input
                        type="checkbox"
                        @change="toggleSelectAll"
                        :checked="allItemsSelected"
                        class="h-5 w-5 text-blue-600 border-gray-300 rounded-lg focus:ring-blue-500 focus:ring-2"
                      />
                    </th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                      Waktu
                    </th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                      Mata Kuliah
                    </th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                      Dosen & Ruang
                    </th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                      Tipe
                    </th>
                    <th
                      v-if="activeTab === 'conflicts'"
                      scope="col"
                      class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider"
                    >
                      Detail Bentrok
                    </th>
                    <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">
                      Aksi
                    </th>
                  </tr>

                  <!-- Header for Class Schedule Tab -->
                  <tr v-else>
                    <th scope="col" class="px-6 py-4 text-left">
                      <input
                        type="checkbox"
                        @change="toggleSelectAll"
                        :checked="allItemsSelected"
                        class="h-5 w-5 text-blue-600 border-gray-300 rounded-lg focus:ring-blue-500 focus:ring-2"
                      />
                    </th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                      Informasi Jadwal
                    </th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                      Program Studi
                    </th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                      Tipe
                    </th>
                    <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">
                      {{ activeTab === 'active' ? 'Aksi' : 'Aksi Sampah' }}
                    </th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                  <!-- Meetings Rows -->
                  <!-- Meetings & Conflicts Rows -->
                  <template v-if="activeTab === 'meetings' || activeTab === 'conflicts'">
                    <tr
                      v-for="(meeting, index) in schedules"
                      :key="meeting.id"
                      :class="[
                        'hover:bg-purple-50 transition-colors duration-150',
                        index % 2 === 0 ? 'bg-white' : 'bg-gray-50/30'
                      ]"
                    >
                      <td class="px-6 py-4">
                        <input
                          type="checkbox"
                          v-model="selectedItems"
                          :value="meeting.id"
                          class="h-5 w-5 text-purple-600 border-gray-300 rounded-lg focus:ring-purple-500 focus:ring-2"
                        />
                      </td>
                      <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">
                          {{ new Date(meeting.date).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric' }) }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                          {{ meeting.start_time }} - {{ meeting.end_time }}
                        </div>
                      </td>
                      <td class="px-6 py-4">
                         <div class="flex items-center">
                           <span v-if="meeting.meeting_number" class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-purple-100 text-purple-700 text-xs font-bold mr-2">
                             {{ meeting.meeting_number }}
                           </span>
                           <div>
                             <div class="text-sm font-medium text-gray-900">{{ meeting.title }}</div>
                             <div class="text-xs text-gray-500">{{ meeting.schedule_code }}</div>
                           </div>
                         </div>
                      </td>
                      <td class="px-6 py-4">
                        <div class="space-y-2">
                          <!-- Lecturers -->
                          <div class="flex items-start">
                             <svg class="w-4 h-4 mr-1.5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <div class="text-sm text-gray-900">
                              <div v-if="meeting.lecturers && meeting.lecturers.length > 0">
                                <div v-for="lecturer in meeting.lecturers" :key="lecturer.id">
                                  {{ lecturer.name }} <span v-if="lecturer.pivot?.is_primary" class="text-xs text-blue-600">(Utama)</span>
                                </div>
                              </div>
                              <span v-else class="text-gray-400 italic">Belum ada dosen</span>
                            </div>
                          </div>

                          <!-- Room -->
                          <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <span class="text-sm text-gray-900">
                              <template v-if="meeting.is_online">Online</template>
                              <template v-else>
                                {{ meeting.rooms && meeting.rooms.length > 0 ? meeting.rooms.map(r => r.name).join(', ') : '-' }}
                              </template>
                            </span>
                          </div>
                        </div>
                      </td>
                      <td class="px-6 py-4">
                        <div class="space-y-2">
                          <!-- Session Type (Clickable) -->
                          <button
                            @click="openQuickSessionType(meeting)"
                            :class="[
                              'px-2 py-1 text-xs rounded-full font-medium cursor-pointer transition-colors',
                              meeting.session_type === 'uts' ? 'bg-orange-100 text-orange-800 hover:bg-orange-200' :
                              meeting.session_type === 'uas' ? 'bg-red-100 text-red-800 hover:bg-red-200' :
                              'bg-blue-100 text-blue-800 hover:bg-blue-200'
                            ]"
                            title="Klik untuk ubah tipe"
                          >
                            {{ meeting.session_type === 'uts' ? 'UTS' : meeting.session_type === 'uas' ? 'UAS' : 'Kuliah' }}
                          </button>
                          <!-- Status Jadwal (Clickable) -->
                          <div class="flex items-center space-x-2">
                            <button
                              v-if="!meeting.rescheduled_from"
                              @click="openQuickReschedule(meeting)"
                              class="inline-flex items-center px-2 py-1 text-xs rounded-full font-medium bg-green-100 text-green-800 hover:bg-green-200 transition-colors cursor-pointer"
                              title="Klik untuk reschedule"
                            >
                              <span class="w-1.5 h-1.5 mr-1 rounded-full bg-green-500"></span>
                              Terjadwal
                            </button>
                            <button
                              v-else
                              @click="openQuickReschedule(meeting)"
                              class="inline-flex items-center px-2 py-1 text-xs rounded-full font-medium bg-orange-100 text-orange-800 hover:bg-orange-200 transition-colors cursor-pointer"
                              title="Klik untuk edit reschedule"
                            >
                              <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                              </svg>
                              Reschedule
                            </button>
                          </div>
                        </div>
                      </td>
                  <td v-if="activeTab === 'conflicts'" class="px-6 py-4">
                    <div class="text-sm text-red-600">
                        <ul class="list-disc list-inside">
                            <li v-for="(detail, idx) in (meeting.conflict_details || [])" :key="idx">
                                {{ detail }}
                            </li>
                            <li v-if="!meeting.conflict_details || meeting.conflict_details.length === 0">
                                {{ meeting.conflict_status !== 'none' ? meeting.conflict_status : 'Tanpa detail' }}
                            </li>
                        </ul>
                    </div>
                  </td>
                      <td class="px-6 py-4 text-right text-sm font-medium">
                        <div class="flex items-center justify-end space-x-2">
                             <!-- Detail -->
                           <button
                              @click="openMeetingDetail(meeting)"
                              class="text-green-600 hover:text-green-900 p-1 rounded-full hover:bg-green-50"
                              title="Lihat Detail"
                            >
                              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                              </svg>
                            </button>
                            <!-- Edit -->
                            <button
                              @click="openEditMeeting(meeting)"
                              class="text-blue-600 hover:text-blue-900 p-1 rounded-full hover:bg-blue-50"
                              title="Edit"
                            >
                              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                              </svg>
                            </button>
                             <button
                              @click="deleteMeeting(meeting)"
                              class="text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-50"
                              title="Hapus"
                            >
                              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                              </svg>
                            </button>
                        </div>
                      </td>
                    </tr>
                  </template>

                  <!-- Class Schedule Rows (Active & Trash) -->
                  <template v-else>
                    <tr
                      v-for="(schedule, index) in schedules.filter(l => l && l.id)"
                      :key="schedule.id"
                      :class="[
                        'hover:bg-blue-50 transition-colors duration-150',
                        index % 2 === 0 ? 'bg-white' : 'bg-gray-50/30'
                      ]"
                    >
                      <td class="px-6 py-4">
                        <input
                          type="checkbox"
                          v-model="selectedItems"
                          :value="schedule.id"
                          class="h-5 w-5 text-blue-600 border-gray-300 rounded-lg focus:ring-blue-500 focus:ring-2"
                        />
                      </td>
  
                      <!-- Schedule Info -->
                      <td class="px-6 py-4">
                        <div class="space-y-3">
                          <!-- Title and Code -->
                          <div>
                            <h3 class="text-base font-semibold text-gray-900 truncate hover:text-blue-600 transition-colors">
                              {{ schedule.title || 'Jadwal Tanpa Judul' }}
                            </h3>
                            <p class="text-sm text-gray-500 mt-1">
                              Kode: {{ schedule.schedule_code }}
                            </p>
                          </div>
  
                          <!-- Class Info -->
                          <div class="flex items-center space-x-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                              {{ schedule.school_class?.name || 'Kelas' }}
                            </span>
                            <span v-if="schedule.school_class?.batch_year" class="text-xs text-gray-500">
                                  • {{ schedule.school_class.batch_year }}
                                </span>
                          </div>
  
                          <!-- Percentages -->
                          <div class="flex items-center space-x-3 text-xs text-gray-600">
                            <span class="flex items-center">
                              <svg class="w-3 h-3 mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"/>
                                <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"/>
                              </svg>
                              Online: {{ schedule.online_percentage || 0 }}%
                            </span>
                            <span class="flex items-center">
                              <svg class="w-3 h-3 mr-1 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"/>
                              </svg>
                              Offline: {{ schedule.offline_percentage || 100 }}%
                            </span>
                          </div>
                        </div>
                      </td>
  
                      <!-- Department -->
                      <td class="px-6 py-4">
                        <div class="space-y-1">
                          <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <div>
                              <div class="text-sm font-medium text-gray-900 truncate" :title="schedule.program_study?.name">
                                {{ schedule.program_study?.name || 'Belum ditugaskan' }}
                              </div>
                              <div v-if="schedule.academic_year" class="text-xs text-gray-500 truncate" :title="schedule.academic_year?.academic_calendar_year">
                                {{ schedule.academic_year?.academic_calendar_year }} - {{ schedule.academic_year?.admission_period }}
                              </div>
                            </div>
                          </div>
                        </div>
                      </td>
  
                      <!-- Tipe -->
                      <td class="px-6 py-4">
                        <div class="space-y-1">
                          <!-- Session Type -->
                          <span
                            :class="[
                              'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium',
                              schedule.session_type === 'uts' ? 'bg-orange-100 text-orange-800 border border-orange-300' :
                              schedule.session_type === 'uas' ? 'bg-red-100 text-red-800 border border-red-300' :
                              'bg-blue-100 text-blue-800 border border-blue-300'
                            ]"
                          >
                            {{ schedule.session_type === 'uts' ? 'UTS' : schedule.session_type === 'uas' ? 'UAS' : 'Kuliah' }}
                          </span>
                          <!-- Reschedule Indicator -->
                          <div v-if="schedule.rescheduled_from" class="flex items-center text-xs text-orange-600 mt-1">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Reschedule
                          </div>
                          <!-- Description (if available) -->
                          <div v-if="schedule.description" class="text-xs text-gray-600 mt-1 line-clamp-2">
                            {{ schedule.description }}
                          </div>
                        </div>
                      </td>
  
                      <!-- Actions -->
                      <td class="px-6 py-4">
                        <div class="flex justify-end space-x-1">
                          <template v-if="activeTab === 'active'">
                            <!-- Detail Button -->
                            <button
                              @click="viewScheduleDetail(schedule)"
                              class="p-2 text-green-600 hover:bg-green-100 rounded-lg transition-colors duration-200 group"
                              title="Lihat detail dosen"
                            >
                              <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                              </svg>
                            </button>
  
                            <!-- Edit Button -->
                            <button
                              @click="editSchedule(schedule)"
                              class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors duration-200 group"
                              title="Edit jadwal"
                            >
                              <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                              </svg>
                            </button>
  
                            <!-- Enroll Course Button -->
                            <button
                              @click="enrollCourse(schedule)"
                              class="p-2 text-green-600 hover:bg-green-100 rounded-lg transition-colors duration-200 group"
                              title="Enroll matakuliah"
                            >
                              <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                              </svg>
                            </button>
  
                            <!-- Duplicate Button -->
                            <button
                              @click="duplicateSchedule(schedule)"
                              class="p-2 text-purple-600 hover:bg-purple-100 rounded-lg transition-colors duration-200 group"
                              title="Duplikat jadwal"
                            >
                              <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                              </svg>
                            </button>
  
                            <!-- Generate Data Button -->
                            <button
                              @click="autoSchedule(schedule)"
                              class="p-2 text-indigo-600 hover:bg-indigo-100 rounded-lg transition-colors duration-200 group"
                              title="Generate Data: Buat jadwal berdasarkan persentase online/offline dan rotasi ruangan"
                            >
                              <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                              </svg>
                            </button>
  
                            <!-- Delete Button -->
                            <button
                              @click="confirmDelete(schedule)"
                              class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition-colors duration-200 group"
                              title="Hapus jadwal"
                            >
                              <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                              </svg>
                            </button>
                          </template>
  
                          <template v-else-if="activeTab === 'trash'">
                            <!-- Restore Button -->
                            <button
                              @click="restoreSchedule(schedule.id)"
                              class="p-2 text-green-600 hover:bg-green-100 rounded-lg transition-colors duration-200 group"
                              title="Pulihkan jadwal"
                            >
                              <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                              </svg>
                            </button>
  
                            <!-- Force Delete Button -->
                            <button
                              @click="confirmForceDeleteSchedule(schedule)"
                              class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition-colors duration-200 group"
                              title="Hapus permanen"
                            >
                              <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                              </svg>
                            </button>
                          </template>
                        </div>
                      </td>
                    </tr>
                  </template>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
              <div class="flex-1 flex justify-between sm:hidden">
                <button
                  @click="prevPage"
                  :disabled="pagination.current_page === 1"
                  class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                >
                  Previous
                </button>
                <button
                  @click="nextPage"
                  :disabled="pagination.current_page === pagination.last_page"
                  class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                >
                  Next
                </button>
              </div>
              <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                  <p class="text-sm text-gray-700">
                    Showing
                    <span class="font-medium">{{ pagination.from }}</span>
                    to
                    <span class="font-medium">{{ pagination.to }}</span>
                    of
                    <span class="font-medium">{{ pagination.total }}</span>
                    results
                  </p>
                </div>
                <div>
                  <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                    <button
                      @click="prevPage"
                      :disabled="pagination.current_page === 1"
                      class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                    >
                      <span class="sr-only">Previous</span>
                      <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                      </svg>
                    </button>

                    <!-- Page numbers -->
                    <button
                      v-for="page in displayedPages"
                      :key="page"
                      @click="goToPage(page)"
                      :class="[
                        'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                        page === pagination.current_page
                          ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                          : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                      ]"
                    >
                      {{ page }}
                    </button>

                    <button
                      @click="nextPage"
                      :disabled="pagination.current_page === pagination.last_page"
                      class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                    >
                      <span class="sr-only">Next</span>
                      <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                      </svg>
                    </button>
                  </nav>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Modals -->
      <CreateClassScheduleModal
        v-if="showAddModal"
        :show="showAddModal"
        @close="closeModal"
        @created="fetchSchedules"
      />

      <ScheduleDetailModal
        v-if="showDetailModal"
        :show="showDetailModal"
        :schedule="selectedScheduleForDetail"
        @close="closeModal"
        @edit="editScheduleFromDetail"
      />

      <ScheduleImportModal
        v-if="showImportModal"
        :show="showImportModal"
        @close="showImportModal = false"
        @import-success="onImported"
      />


      <ConfirmModal
        v-if="showDeleteConfirm"
        :show="showDeleteConfirm"
        title="Hapus Jadwal"
        :message="`Apakah Anda yakin ingin menghapus jadwal ${currentSchedule?.title}?`"
        type="danger"
        :loading="loading"
        confirm-text="Ya, Hapus"
        @confirm="deleteSchedule"
        @cancel="showDeleteConfirm = false"
      />

      <ConfirmModal
        v-if="showBulkDeleteConfirm"
        :show="showBulkDeleteConfirm"
        title="Hapus Jadwal Terpilih"
        :message="`Apakah Anda yakin ingin menghapus ${selectedItems.length} jadwal yang dipilih?`"
        type="danger"
        :loading="loading"
        confirm-text="Ya, Hapus Semua"
        @confirm="bulkDelete"
        @cancel="showBulkDeleteConfirm = false"
      />

      <ConfirmModal
        v-if="showBulkRestoreConfirm"
        :show="showBulkRestoreConfirm"
        title="Pulihkan Jadwal Terpilih"
        :message="`Apakah Anda yakin ingin memulihkan ${selectedItems.length} jadwal yang dipilih?`"
        type="success"
        :loading="loading"
        confirm-text="Ya, Pulihkan"
        @confirm="bulkRestore"
        @cancel="showBulkRestoreConfirm = false"
      />

      <ConfirmModal
        v-if="showBulkForceDeleteConfirm"
        :show="showBulkForceDeleteConfirm"
        title="Hapus Permanen Jadwal Terpilih"
        :message="`Apakah Anda yakin ingin menghapus permanen ${selectedItems.length} jadwal yang dipilih? Tindakan ini tidak dapat dibatalkan.`"
        type="danger"
        :loading="loading"
        confirm-text="Hapus Permanen"
        cancel-text="Batal"
        @confirm="bulkForceDelete"
        @cancel="showBulkForceDeleteConfirm = false"
      />

      <ConfirmModal
        v-if="showForceDeleteConfirm"
        :show="showForceDeleteConfirm"
        title="Hapus Permanen Jadwal"
        :message="`Apakah Anda yakin ingin menghapus permanen jadwal ${forceDeleteTarget?.title}? Tindakan ini tidak dapat dibatalkan.`"
        type="danger"
        :loading="loading"
        confirm-text="Hapus Permanen"
        cancel-text="Batal"
        @confirm="forceDeleteSchedule"
        @cancel="showForceDeleteConfirm = false"
      />

      <!-- Auto Schedule Confirmation Modal -->
      <ConfirmModal
        v-if="showAutoScheduleConfirm"
        :show="showAutoScheduleConfirm"
        title="Generate Jadwal Otomatis"
        :message="autoScheduleMessage"
        type="info"
        :loading="loading"
        confirm-text="Ya, Generate"
        cancel-text="Batal"
        @confirm="executeAutoSchedule"
        @cancel="showAutoScheduleConfirm = false"
      />

      <!-- Delete Meeting Confirmation Modal -->
      <ConfirmModal
        v-if="showDeleteMeetingConfirm"
        :show="showDeleteMeetingConfirm"
        title="Hapus Pertemuan"
        :message="`Apakah Anda yakin ingin menghapus pertemuan ${meetingToDelete?.title || ''}?`"
        type="danger"
        :loading="loading"
        confirm-text="Ya, Hapus"
        @confirm="executeDeleteMeeting"
        @cancel="showDeleteMeetingConfirm = false"
      />

      <!-- Bulk Delete Meetings Confirmation Modal -->
      <ConfirmModal
        v-if="showBulkDeleteMeetingsConfirm"
        :show="showBulkDeleteMeetingsConfirm"
        title="Hapus Pertemuan Terpilih"
        :message="`Apakah Anda yakin ingin menghapus ${selectedItems.length} pertemuan yang dipilih?`"
        type="danger"
        :loading="loading"
        confirm-text="Ya, Hapus Semua"
        @confirm="executeBulkDeleteMeetings"
        @cancel="showBulkDeleteMeetingsConfirm = false"
      />

      <!-- Edit Meeting Modal -->
      <EditScheduleModal
        v-if="showEditMeetingModal"
        v-model="showEditMeetingModal"
        :schedule="selectedMeetingForEdit"
        :readonly="isMeetingReadonly"
        :lecturers="lecturers"
        :rooms="rooms"
        @save="saveMeeting"
      />

      <!-- Quick Reschedule Modal -->
      <div v-if="showQuickRescheduleModal" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
          <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="showQuickRescheduleModal = false"></div>
          <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
              {{ quickRescheduleForm.is_rescheduled ? 'Edit Reschedule' : 'Atur Jadwal' }}
            </h3>
            
            <!-- Status Toggle -->
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Status Jadwal</label>
              <div class="flex items-center space-x-4">
                <label class="inline-flex items-center cursor-pointer">
                  <input type="radio" v-model="quickRescheduleForm.is_rescheduled" :value="false" class="form-radio text-green-600">
                  <span class="ml-2 text-sm text-gray-700">Terjadwal</span>
                </label>
                <label class="inline-flex items-center cursor-pointer">
                  <input type="radio" v-model="quickRescheduleForm.is_rescheduled" :value="true" class="form-radio text-orange-600">
                  <span class="ml-2 text-sm text-orange-700">Reschedule</span>
                </label>
              </div>
            </div>

            <!-- Date (if reschedule) -->
            <div v-if="quickRescheduleForm.is_rescheduled" class="mb-4 p-3 bg-orange-50 border border-orange-200 rounded-md">
              <label class="block text-sm font-medium text-orange-700 mb-1">Tanggal Baru</label>
              <input type="date" v-model="quickRescheduleForm.date" class="w-full border-orange-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
            </div>

            <!-- Mode Perkuliahan -->
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Mode Perkuliahan</label>
              <div class="flex items-center space-x-4">
                <label class="inline-flex items-center cursor-pointer">
                  <input type="radio" v-model="quickRescheduleForm.is_online" :value="false" class="form-radio text-blue-600">
                  <span class="ml-2 text-sm text-gray-700">Offline</span>
                </label>
                <label class="inline-flex items-center cursor-pointer">
                  <input type="radio" v-model="quickRescheduleForm.is_online" :value="true" class="form-radio text-blue-600">
                  <span class="ml-2 text-sm text-gray-700">Online</span>
                </label>
              </div>
            </div>

            <!-- Room Selection (Offline only) -->
            <div v-if="!quickRescheduleForm.is_online" class="mb-4">
              <SearchableSelect
                label="Ruangan"
                v-model="quickRescheduleForm.room_id"
                :options="formattedRoomsForQuickModal"
                placeholder="Cari ruangan..."
                empty-message="Ruangan tidak ditemukan"
              />
            </div>

            <!-- Lecturer Selection (Team Teaching) -->
            <div v-if="conflictWarnings.team_teaching_lecturers?.length > 0" class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Ganti Dosen (Team Teaching)</label>
              <select v-model="quickRescheduleForm.lecturer_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <option v-for="l in conflictWarnings.team_teaching_lecturers" :key="l.id" :value="l.id">
                  {{ l.name }}
                </option>
              </select>
              <p class="mt-1 text-xs text-gray-500">Pilih dosen lain dari team teaching jika diperlukan.</p>
            </div>

            <!-- Conflict Check Loading -->
            <div v-if="conflictCheckLoading" class="mb-4 p-3 bg-gray-50 border border-gray-200 rounded-md">
              <div class="flex items-center text-sm text-gray-600">
                <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Mengecek konflik jadwal...
              </div>
            </div>

            <!-- Conflict Warnings -->
            <div v-if="conflictWarnings.has_conflicts && !conflictCheckLoading" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-md">
              <div class="flex items-start">
                <svg class="w-5 h-5 text-red-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div class="flex-1">
                  <h4 class="text-sm font-semibold text-red-800 mb-2">⚠️ Peringatan Konflik!</h4>
                  
                  <!-- Same Course Schedules -->
                  <div v-if="conflictWarnings.same_course_schedules?.length" class="text-sm text-red-700 mb-2 p-2 bg-red-100 rounded">
                    <strong>Jadwal mata kuliah yang sama di tanggal ini:</strong>
                    <ul class="list-disc list-inside mt-1">
                      <li v-for="c in conflictWarnings.same_course_schedules" :key="c.id">
                        {{ c.title || c.schedule_code }} ({{ c.start_time }} - {{ c.end_time }})
                        <span class="text-xs text-red-600">- {{ c.lecturer }}, {{ c.room }}</span>
                      </li>
                    </ul>
                  </div>
                  
                  <!-- Room Conflicts -->
                  <div v-if="conflictWarnings.room_conflicts?.length" class="text-sm text-red-700 mb-2">
                    <strong>Ruangan bentrok:</strong>
                    <ul class="list-disc list-inside mt-1">
                      <li v-for="c in conflictWarnings.room_conflicts" :key="c.id">
                        {{ c.title || c.schedule_code }} ({{ c.start_time }} - {{ c.end_time }})
                      </li>
                    </ul>
                  </div>
                  
                  <!-- Lecturer Conflicts -->
                  <div v-if="conflictWarnings.lecturer_conflicts?.length" class="text-sm text-red-700">
                    <strong>Dosen bentrok:</strong>
                    <ul class="list-disc list-inside mt-1">
                      <li v-for="c in conflictWarnings.lecturer_conflicts" :key="c.id">
                        {{ c.title || c.schedule_code }} ({{ c.start_time }} - {{ c.end_time }})
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>

            <!-- No Conflict Message -->
            <div v-if="!conflictWarnings.has_conflicts && !conflictCheckLoading && quickRescheduleForm.is_rescheduled" class="mb-4 p-3 bg-green-50 border border-green-200 rounded-md">
              <div class="flex items-center text-sm text-green-700">
                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                ✓ Tidak ada konflik jadwal ditemukan
              </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-3 mt-6">
              <button @click="showQuickRescheduleModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                Batal
              </button>
              <button @click="saveQuickReschedule" :disabled="quickRescheduleSaving" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 disabled:opacity-50">
                {{ quickRescheduleSaving ? 'Menyimpan...' : 'Simpan' }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Session Type Modal -->
      <div v-if="showQuickSessionTypeModal" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
          <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="showQuickSessionTypeModal = false"></div>
          <div class="relative bg-white rounded-lg shadow-xl max-w-sm w-full p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
              Ubah Tipe Perkuliahan
            </h3>
            
            <div class="space-y-3">
              <label class="flex items-center p-3 rounded-lg border cursor-pointer transition-colors"
                     :class="quickSessionTypeForm.session_type === 'kuliah' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:bg-gray-50'">
                <input type="radio" v-model="quickSessionTypeForm.session_type" value="kuliah" class="form-radio text-blue-600">
                <span class="ml-3 text-sm font-medium text-gray-700">Kuliah</span>
              </label>
              <label class="flex items-center p-3 rounded-lg border cursor-pointer transition-colors"
                     :class="quickSessionTypeForm.session_type === 'uts' ? 'border-orange-500 bg-orange-50' : 'border-gray-200 hover:bg-gray-50'">
                <input type="radio" v-model="quickSessionTypeForm.session_type" value="uts" class="form-radio text-orange-600">
                <span class="ml-3 text-sm font-medium text-orange-700">UTS (Ujian Tengah Semester)</span>
              </label>
              <label class="flex items-center p-3 rounded-lg border cursor-pointer transition-colors"
                     :class="quickSessionTypeForm.session_type === 'uas' ? 'border-red-500 bg-red-50' : 'border-gray-200 hover:bg-gray-50'">
                <input type="radio" v-model="quickSessionTypeForm.session_type" value="uas" class="form-radio text-red-600">
                <span class="ml-3 text-sm font-medium text-red-700">UAS (Ujian Akhir Semester)</span>
              </label>
            </div>

            <div class="flex justify-end space-x-3 mt-6">
              <button @click="showQuickSessionTypeModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                Batal
              </button>
              <button @click="saveQuickSessionType" :disabled="quickSessionTypeSaving" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 disabled:opacity-50">
                {{ quickSessionTypeSaving ? 'Menyimpan...' : 'Simpan' }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <EnrollCourseModal
        v-if="showEnrollModal"
        :show="showEnrollModal"
        :class-schedule="selectedScheduleForEnroll"
        @close="showEnrollModal = false"
        @enrolled="onCourseEnrolled"
        @refresh="onCoursesRefresh"
      />

      <AutoScheduleModal
        v-if="showAutoScheduleModal"
        :show="showAutoScheduleModal"
        :class-schedule="selectedScheduleForAuto"
        @close="showAutoScheduleModal = false"
        @generated="onAutoScheduleGenerated"
      />

      <ScheduleDetailModal
        v-if="showDetailModal"
        :show="showDetailModal"
        :schedule="selectedScheduleForDetail"
        @close="closeModal"
        @generate="handleGenerateFromDetail"
      />


    </div>
  </Layout>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useToastStore } from '@/stores/toast'
import scheduleService from '@/services/scheduleService'
import meetingService from '@/services/meetingService'
import programStudyService from '@/services/programStudyService'
import classService from '@/services/classService'
import courseService from '@/services/courseService'
import lecturerService from '@/services/lecturerService'
import roomService from '@/services/roomService'
import academicYearService from '@/services/academicYearService'
import Layout from '@/components/Layout.vue'
import CreateClassScheduleModal from '@/components/modals/CreateClassScheduleModal.vue'
import ScheduleDetailModal from '@/components/modals/ScheduleDetailModal.vue'
import ScheduleImportModal from '@/components/modals/ImportModal.vue'
import EnrollCourseModal from '@/components/modals/EnrollCourseModal.vue'
import AutoScheduleModal from '@/components/modals/AutoScheduleModal.vue'
import ConfirmModal from '@/components/modals/ConfirmModal.vue'
import EditScheduleModal from '@/components/modals/EditScheduleModal.vue'
import SearchableSelect from '@/components/SearchableSelect.vue'
import Toast from '@/components/Toast.vue'

const router = useRouter()
const toastStore = useToastStore()

// Modal State for Edit Meeting
const showEditMeetingModal = ref(false)
const selectedMeetingForEdit = ref(null)
const isMeetingReadonly = ref(false)

// Auth state
const isAuthenticated = computed(() => {
  const token = localStorage.getItem('token');
  return token !== null && token !== undefined && token !== '';
});

// State
const loading = ref(false)
const schedules = ref([])
const scheduleStats = ref({})
const selectedItems = ref([])

// Filters
const searchQuery = ref('')
const selectedDepartment = ref('')
const selectedStatus = ref('')
const departments = ref([])
const programStudies = ref([])
const courses = ref([])
const classes = ref([])
const lecturers = ref([])
const rooms = ref([])
const selectedCourse = ref('')
const selectedClass = ref('')
const dateFrom = ref('')
const dateTo = ref('')
const selectedAcademicYear = ref('')
const academicYears = ref([])
const isInitialized = ref(false)

// Tab Management
const activeTab = ref('active')
const trashCount = ref(0)

// Pagination
const pagination = reactive({
  current_page: 1,
  last_page: 1,
  per_page: 10,
  total: 0,
  from: 0,
  to: 0
})

// Modals
const showAddModal = ref(false)
const showEditModal = ref(false)
const showDetailModal = ref(false)
const showImportModal = ref(false)
const showEnrollModal = ref(false)
const showAutoScheduleModal = ref(false)
const showDeleteConfirm = ref(false)
const showForceDeleteConfirm = ref(false)
const forceDeleteTarget = ref(null)
const showBulkDeleteConfirm = ref(false)

// Meeting delete confirmations
const showDeleteMeetingConfirm = ref(false)
const showBulkDeleteMeetingsConfirm = ref(false)
const meetingToDelete = ref(null)

// Auto Schedule Confirm
const showAutoScheduleConfirm = ref(false)
const autoScheduleData = ref(null)
const autoScheduleMessage = ref('')

const perPageOptions = [5, 10, 20, 50, 100]
const showBulkRestoreConfirm = ref(false)
const showBulkForceDeleteConfirm = ref(false)
const currentSchedule = ref(null)
const selectedScheduleForDetail = ref(null)
const selectedScheduleForEnroll = ref(null)
const selectedScheduleForAuto = ref(null)

// Quick Reschedule Modal
const showQuickRescheduleModal = ref(false)
const quickRescheduleSaving = ref(false)
const quickRescheduleTarget = ref(null)
const quickRescheduleForm = reactive({
  is_rescheduled: false,
  date: '',
  is_online: false,
  room_id: '',
  lecturer_id: '',
  session_type: 'kuliah'
})
const conflictCheckLoading = ref(false)
const conflictWarnings = reactive({
  has_conflicts: false,
  room_conflicts: [],
  lecturer_conflicts: [],
  same_course_schedules: [],
  team_teaching_lecturers: [],
  current_lecturer_id: null
})

// Quick Session Type Modal
const showQuickSessionTypeModal = ref(false)
const quickSessionTypeSaving = ref(false)
const quickSessionTypeTarget = ref(null)
const quickSessionTypeForm = reactive({
  session_type: 'kuliah'
})

// Computed
const formattedRoomsForQuickModal = computed(() => {
  return rooms.value.map(r => ({
    id: r.id,
    name: `${r.name} (${r.building || 'Gedung'})`
  }))
})

const allItemsSelected = computed(() => {
  return schedules.value.length > 0 && selectedItems.value.length === schedules.value.length
})

const displayedPages = computed(() => {
  const current = pagination.current_page
  const last = pagination.last_page
  const delta = 2
  const range = []
  const rangeWithDots = []

  for (let i = Math.max(2, current - delta); i <= Math.min(last - 1, current + delta); i++) {
    range.push(i)
  }

  if (current - delta > 2) {
    rangeWithDots.push(1, '...')
  } else {
    rangeWithDots.push(1)
  }

  rangeWithDots.push(...range)

  if (current + delta < last - 1) {
    rangeWithDots.push('...', last)
  } else {
    rangeWithDots.push(last)
  }

  return rangeWithDots.filter((item, index, arr) => arr.indexOf(item) === index)
})

// Methods
const fetchSchedules = async (loadingAnimation = true) => {
  if (!isInitialized.value) return // Block fetch if filters not loaded
  if (loadingAnimation) loading.value = true
  try {
    selectedItems.value = []

    if (activeTab.value === 'trash') {
      await fetchTrashSchedules()
      return
    }

    const params = {
      page: pagination.current_page,
      per_page: pagination.per_page,
      search: searchQuery.value,
      department: selectedDepartment.value,
      status: selectedStatus.value,
      course_id: selectedCourse.value,
      class_id: selectedClass.value,
      date_from: dateFrom.value,
      date_to: dateTo.value,
      academic_year_id: selectedAcademicYear.value
    }

    let response;
    if (activeTab.value === 'meetings') {
         response = await meetingService.getAll(params)
    } else if (activeTab.value === 'conflicts') {
         params.conflict_status = 'has_conflict'
         params.is_active = true
         response = await scheduleService.getAll(params)
    } else {
         params.is_active = true
         response = await scheduleService.getAll(params)
    }

    if (response && response.data) {
      // API returns: { success: true, data: [...], meta: {...} }
      const responseData = response.data;

      // Handle ResponseService format
      if (Array.isArray(responseData)) {
        // Direct array format
        schedules.value = responseData;
      } else if (responseData.data && Array.isArray(responseData.data)) {
        // Nested data format
        schedules.value = responseData.data;
      } else if (responseData.success !== undefined) {
        // ResponseService format with data property
        if (Array.isArray(responseData.data)) {
          schedules.value = responseData.data;
        } else {
          schedules.value = [];
        }
      } else {
        schedules.value = [];
      }

      // Handle pagination from meta if available
      const meta = response.meta || (response.data && response.data.meta);
      
      if (meta) {
        pagination.current_page = meta.current_page || 1;
        pagination.last_page = meta.last_page || 1;
        pagination.per_page = meta.per_page || 10;
        pagination.total = meta.total || 0;
        pagination.from = meta.from || 0;
        pagination.to = meta.to || 0;
      }

    } else {
      schedules.value = [];
    }
  } catch (error) {
  
    // Handle authentication errors specifically
    if (error.response?.status === 401) {
        localStorage.removeItem('token')
      toastStore.addToast({
        type: 'error',
        title: 'Authentication Error',
        message: 'Session expired. Please login again.'
      })
      // Redirect to login after a short delay
      setTimeout(() => {
        window.location.href = '/login'
      }, 2000)
      return
    }

    if (error.response?.status === 403) {
      toastStore.addToast({
        type: 'error',
        title: 'Access Denied',
        message: 'You do not have permission to access this resource.'
      })
      return
    }

    schedules.value = []
    toastStore.addToast({
      type: 'error',
      title: 'Error',
      message: error.response?.data?.message || 'Gagal memuat data dosen'
    })
  } finally {
    if (loadingAnimation) {
      loading.value = false
    }
  }
}

const fetchTrashSchedules = async () => {
  try {
    const params = {
      page: pagination.current_page,
      per_page: pagination.per_page,
      search: searchQuery.value,
      department: selectedDepartment.value,
      only_trashed: true
    }

    const response = await scheduleService.getTrash(params)

    if (response && response.data) {
      const responseData = response.data

      if (responseData.data && Array.isArray(responseData.data)) {
        schedules.value = responseData.data
        pagination.current_page = responseData.current_page || 1
        pagination.last_page = responseData.last_page || 1
        pagination.per_page = responseData.per_page || 10
        pagination.total = responseData.total || 0
        pagination.from = responseData.from || 0
        pagination.to = responseData.to || 0
      } else if (Array.isArray(responseData)) {
        schedules.value = responseData
        pagination.current_page = 1
        pagination.last_page = 1
        pagination.per_page = schedules.value.length
        pagination.total = schedules.value.length
        pagination.from = 1
        pagination.to = schedules.value.length
      } else {
        schedules.value = []
      }

    } else {
      schedules.value = []
    }
  } catch (error) {
    schedules.value = []
    toastStore.addToast({
      type: 'error',
      title: 'Error',
      message: error.response?.data?.message || 'Gagal memuat data sampah'
    })
  }
}

// Watchers for real-time filtering
watch([selectedDepartment, selectedStatus, selectedCourse, selectedClass, selectedAcademicYear, dateFrom, dateTo], () => {
    // Reset to first page when filter changes
    pagination.current_page = 1
    fetchSchedules()
})

watch(activeTab, (newTab) => {
    selectedItems.value = []
    pagination.current_page = 1
    fetchSchedules()
})

const switchTab = (tab) => {
  activeTab.value = tab
  // fetchSchedules is handled by the watcher
}

const changePerPage = (value) => {
  pagination.per_page = parseInt(value)
  pagination.current_page = 1
  fetchSchedules(false) // No loading animation for changing entries per page
}

const restoreSchedule = async (id) => {
  try {
    const response = await scheduleService.restore(id)
    toastStore.success('Berhasil', 'Data jadwal berhasil dipulihkan')
    // Remove from local array for immediate UI update (item moved from trash to active)
    const index = schedules.value.findIndex(l => l.id === id)
    if (index > -1) {
      schedules.value.splice(index, 1)
    }
  } catch (error) {
    toastStore.error('Error', error.response?.data?.message || 'Gagal memulihkan data dosen')
  }
}

const confirmForceDeleteSchedule = (schedule) => {
  forceDeleteTarget.value = schedule
  showForceDeleteConfirm.value = true
}

const forceDeleteSchedule = async () => {
  if (!forceDeleteTarget.value) return

  try {
    loading.value = true
    await scheduleService.forceDelete(forceDeleteTarget.value.id)
    toastStore.success('Berhasil', 'Data jadwal berhasil dihapus secara permanen')
    showForceDeleteConfirm.value = false
    forceDeleteTarget.value = null
    // Refresh table data after successful deletion
    fetchSchedules(false)
  } catch (error) {
    toastStore.error('Error', error.response?.data?.message || 'Gagal menghapus data dosen')
    showForceDeleteConfirm.value = false
    forceDeleteTarget.value = null
  } finally {
    loading.value = false
  }
}

const duplicateSchedule = async (schedule) => {
  try {
    await scheduleService.duplicate(schedule.id)

    toastStore.success('Berhasil', `Data jadwal "${schedule.title}" berhasil diduplikasi`)

    fetchSchedules(false) // No loading animation for duplicate
  } catch (error) {
    toastStore.error('Error', error.response?.data?.message || 'Gagal menduplikasi data jadwal')
  }
}

const enrollCourse = async (schedule) => {
  // If schedule is null (from toolbar button), use the first schedule or show a message
  if (!schedule) {
    if (schedules.value.length === 0) {
      toastStore.warning('Perhatian', 'Tidak ada jadwal kelas yang tersedia. Silakan buat jadwal terlebih dahulu.')
      return
    }
    schedule = schedules.value[0]
  }
  selectedScheduleForEnroll.value = schedule
  showEnrollModal.value = true
}

const onCourseEnrolled = (data) => {
  showEnrollModal.value = false
  selectedScheduleForEnroll.value = null
  fetchSchedules(false) // Refresh to show enrolled courses
}

const onCoursesRefresh = () => {
  fetchSchedules(false) // Refresh schedules without closing modal
}

const autoSchedule = async (schedule) => {
  // If schedule is null (from toolbar button), use the first schedule or show a message
  if (!schedule) {
    if (schedules.value.length === 0) {
      toastStore.warning('Perhatian', 'Tidak ada jadwal kelas yang tersedia. Silakan buat jadwal terlebih dahulu.')
      return
    }
    schedule = schedules.value[0]
  }
  
  try {
    loading.value = true
    
    // Fetch full schedule data with details
    const fullScheduleResponse = await scheduleService.getById(schedule.id)
    const fullSchedule = fullScheduleResponse.data || fullScheduleResponse
    
    // Check if schedule has enrolled courses
    if (!fullSchedule.details || fullSchedule.details.length === 0) {
      toastStore.warning('Perhatian', 'Tidak ada matakuliah yang di-enroll. Silakan enroll matakuliah terlebih dahulu.')
      loading.value = false
      return
    }
    
    loading.value = false
    
    // Prepare data for confirmation
    autoScheduleData.value = fullSchedule
    autoScheduleMessage.value = `Generate Data Jadwal untuk "${fullSchedule.title}"?\n\n` +
      `• Online: ${fullSchedule.online_percentage || 0}%\n` +
      `• Offline: ${fullSchedule.offline_percentage || 100}%\n` +
      `• Total Matakuliah: ${fullSchedule.details?.length || 0}\n\n` +
      `Data akan di-generate berdasarkan persentase online/offline dengan rotasi ruangan.`
    
    showAutoScheduleConfirm.value = true
    
  } catch (error) {
    toastStore.error('Error', error.response?.data?.message || error.message || 'Gagal menyiapkan data jadwal')
    loading.value = false
  }
}

const executeAutoSchedule = async () => {
    if (!autoScheduleData.value) return
    
    showAutoScheduleConfirm.value = false
    loading.value = true
    toastStore.info('Proses', 'Sedang men-generate jadwal...')
    
    try {
        const response = await scheduleService.autoGenerate(autoScheduleData.value.id)
        
        if (response.success) {
          const data = response.data || {}
          toastStore.success(
            'Berhasil', 
            `Generated ${data.total_generated || 0} jadwal ` +
            `(Online: ${data.online_count || 0}, Offline: ${data.offline_count || 0})`
          )
          fetchSchedules(false) // Refresh to show generated schedules
        } else {
          toastStore.error('Error', response.message || 'Gagal men-generate jadwal')
        }
    } catch (error) {
        toastStore.error('Error', error.response?.data?.message || error.message || 'Gagal men-generate jadwal')
    } finally {
        loading.value = false
        autoScheduleData.value = null
    }
}

const onAutoScheduleGenerated = (data) => {
  showAutoScheduleModal.value = false
  selectedScheduleForAuto.value = null
  fetchSchedules(false) // Refresh to show generated schedules
}

const createUserAccount = async (schedule) => {
  // Check if schedule already has user account
  if (schedule.user_id || schedule.user) {
    toastStore.warning('Perhatian', `Dosen "${schedule.name}" sudah memiliki akun pengguna`)
    return
  }

  try {
    const loadingToast = toastStore.addToast({
      type: 'info',
      title: 'Memproses',
      message: 'Membuat akun pengguna...',
      timeout: 0
    })

    // Call API to create user account
    const response = await scheduleService.createUserAccount(schedule.id)

    // Remove loading toast
    toastStore.removeToast(loadingToast)

    if (response.success) {
      const defaultPassword = response.data.default_password

      toastStore.success('Berhasil',
        `Akun pengguna untuk "${schedule.name}" berhasil dibuat. Default password: ${defaultPassword}`
      )

      // Refresh schedule data to show user account
      fetchSchedules()
    } else {
      toastStore.error('Error', response.message || 'Gagal membuat akun pengguna')
    }
  } catch (error) {
    toastStore.handleError(error, 'createUserAccount')
  }
}

const bulkCreateUserAccounts = async () => {
  if (selectedItems.value.length === 0) {
    toastStore.warning('Perhatian', 'Pilih minimal satu dosen untuk dibuatkan akun pengguna')
    return
  }

  // Filter out null values and extract schedule IDs
  const scheduleIds = selectedItems.value
    .filter(id => id !== null && id !== undefined && id !== '')
    .map(id => parseInt(id))
    .filter(id => !isNaN(id) && id > 0)

  // Check if we have any valid IDs after filtering
  if (scheduleIds.length === 0) {
    toastStore.warning('Perhatian', 'Tidak ada jadwal valid yang dipilih. Silakan pilih kembali.')
    return
  }

  try {
    const loadingToast = toastStore.addToast({
      type: 'info',
      title: 'Memproses',
      message: `Membuat akun pengguna untuk ${scheduleIds.length} dosen...`,
      timeout: 0
    })

    // Call bulk API
    const response = await scheduleService.bulkCreateUserAccounts(scheduleIds)

    // Remove loading toast
    toastStore.removeToast(loadingToast)

    if (response.success) {
      const { success_count, error_count, results } = response.data

      // Show success message
      if (success_count > 0) {
        toastStore.success(
          'Berhasil',
          `${success_count} akun pengguna berhasil dibuat. Password default: nama tanpa spasi + @123`
        )
      }

      // Show errors if any
      if (error_count > 0) {
        const errorResults = results.filter(r => !r.success)
        const errorMessages = errorResults.slice(0, 3).map(r => `${r.schedule_name}: ${r.message}`)
        const errorMessage = errorMessages.join('; ') + (error_count > 3 ? '...' : '')

        toastStore.error('Error', `${error_count} akun gagal dibuat: ${errorMessage}`)
      }

      // Log detailed results for debugging
    } else {
      toastStore.error('Error', response.message || 'Gagal membuat akun pengguna secara bulk')
    }

    // Clear selection and refresh data
    selectedItems.value = []
    fetchSchedules()
  } catch (error) {
    toastStore.handleError(error, 'bulkCreateUserAccounts')
  }
}

const fetchStats = async () => {
  try {
    const includeTrash = activeTab.value === 'trash'
    const response = await scheduleService.getStatistics(includeTrash)
    scheduleStats.value = response.data || {}
  } catch (error) {

    // Handle authentication errors specifically
    if (error.response?.status === 401) {
      localStorage.removeItem('token')
      toastStore.addToast({
        type: 'error',
        title: 'Authentication Error',
        message: 'Session expired. Please login again.'
      })
      // Redirect to login after a short delay
      setTimeout(() => {
        window.location.href = '/login'
      }, 2000)
      return
    }

    if (error.response?.status === 403) {
      return
    }

    // Don't show toast for stats errors to avoid spamming the user
  }
}

const fetchProgramStudies = async () => {
  try {
    const response = await programStudyService.getAll({
      per_page: 100 // Get all program studies
    })

    if (response && response.data) {
      const responseData = response.data

      if (responseData.data && Array.isArray(responseData.data)) {
        programStudies.value = responseData.data
        // Update departments array for compatibility with existing filter
        departments.value = responseData.data.map(ps => ps.name)
      } else if (Array.isArray(responseData)) {
        programStudies.value = responseData
        departments.value = responseData.map(ps => ps.name)
      } else if (responseData && typeof responseData === 'object') {
        // Handle object with nested data
        const programStudiesArray = Object.values(responseData)
        programStudies.value = programStudiesArray
        departments.value = programStudiesArray.map(ps => ps.name)
      }

    }
  } catch (error) {
    // Fallback to default options if API fails
    departments.value = ['Teknik Informatika', 'Sistem Informasi', 'Teknik Elektro', 'Teknik Sipil', 'Manajemen']
    toastStore.addToast({
      type: 'error',
      title: 'Error',
      message: 'Gagal memuat data program studi'
    })
  }
}

const fetchClasses = async (department = null) => {
  try {
      const params = { per_page: 200, status: 'Active' }
      if (department) {
          params.department = department
      }
      const response = await classService.getAll(params)
      if (response.data) {
          classes.value = Array.isArray(response.data) ? response.data : (response.data.data || [])
      }
  } catch (error) {
      console.error('Failed to fetch classes', error)
      classes.value = []
  }
}

const fetchFiltersData = async () => {
   try {
     const [coursesRes, lecturersRes, roomsRes, programsRes, academicYearsRes, activeYearRes] = await Promise.all([
       courseService.getAll(),
       lecturerService.getAll(),
       roomService.getAll(),
       programStudyService.getAll(),
       academicYearService.getAll(),
       academicYearService.getActive()
     ])
 
     // Assign directly to existing refs
     courses.value = coursesRes.data || coursesRes
     lecturers.value = lecturersRes.data || lecturersRes
     rooms.value = roomsRes.data || roomsRes
     programStudies.value = programsRes.data || programsRes
     academicYears.value = academicYearsRes.data || academicYearsRes
 
     // Set active academic year as default filter if active year data exists
     if (activeYearRes.success && activeYearRes.data) {
        selectedAcademicYear.value = activeYearRes.data.id
     }

     await fetchClasses(selectedDepartment.value)
     
   } catch (error) {
     console.error('Failed to fetch filter options', error)
     toastStore.error('Error', 'Gagal memuat filter')
   }
}

const refreshData = async () => {
    const previousYear = selectedAcademicYear.value
    // Fetch filters FIRST to get default active academic year
    await fetchFiltersData()
    fetchStats()
    fetchProgramStudies()
    // Only fetch manually if the watcher wasn't triggered (year didn't change)
    isInitialized.value = true // Allow fetches now
    if (selectedAcademicYear.value === previousYear) {
        fetchSchedules()
    }
}

const toggleSelectAll = () => {
  if (allItemsSelected.value) {
    selectedItems.value = []
  } else {
    selectedItems.value = schedules.value.map(l => l.id)
  }
}

const editSchedule = async (schedule) => {
  try {
    // Clear current schedule first to reset form
    currentSchedule.value = null

    // Open modal first (will show empty form)
    showEditModal.value = true

    // Then fetch complete schedule data from backend
    const response = await scheduleService.getById(schedule.id)

    // Set schedule data (this should trigger the watcher in modal)
    currentSchedule.value = response.data

  } catch (error) {
    toastStore.handleError(error, 'Ambil data dosen')

    // Fallback to basic data if fetch fails
    currentSchedule.value = { ...schedule }
  }
}

const confirmDelete = (schedule) => {
  currentSchedule.value = schedule
  showDeleteConfirm.value = true
}

const deleteSchedule = async () => {
  try {
    loading.value = true
    // Use different methods based on active tab
    if (activeTab.value === 'trash') {
      // For trash tab, permanently delete
      await scheduleService.forceDelete(currentSchedule.value.id)
      toastStore.success('Berhasil', 'Dosen berhasil dihapus permanen')
    } else {
      // For active tab, soft delete
      await scheduleService.delete(currentSchedule.value.id)
      toastStore.success('Berhasil', 'Dosen berhasil dihapus')
    }

    showDeleteConfirm.value = false
    currentSchedule.value = null

    // Refresh data from server to ensure consistency and update pagination info
    fetchSchedules(false)
  } catch (error) {
    toastStore.error('Error', 'Gagal menghapus jadwal')
  } finally {
    loading.value = false
  }
}

// Confirmation functions for bulk actions
const confirmBulkDelete = () => {
  showBulkDeleteConfirm.value = true
}

const confirmBulkRestore = () => {
  showBulkRestoreConfirm.value = true
}

const confirmBulkForceDelete = () => {
  showBulkForceDeleteConfirm.value = true
}

const bulkDelete = async () => {
  try {
    loading.value = true
    // Use different methods based on active tab
    if (activeTab.value === 'trash') {
      // For trash tab, permanently delete
      await scheduleService.bulkForceDelete({ ids: selectedItems.value })
      toastStore.success('Berhasil', `${selectedItems.value.length} jadwal berhasil dihapus permanen`)
    } else {
      // For active tab, soft delete
      await scheduleService.bulkDelete({ class_schedule_ids: selectedItems.value })
      toastStore.success('Berhasil', `${selectedItems.value.length} jadwal berhasil dihapus`)
    }

    showBulkDeleteConfirm.value = false
    selectedItems.value = []

    // Refresh data from server to ensure consistency and update pagination info
    fetchSchedules(false)
  } catch (error) {
    toastStore.error('Error', 'Gagal menghapus jadwal terpilih')
  } finally {
    loading.value = false
  }
}

const bulkToggleStatus = async () => {
  try {
    await scheduleService.bulkToggleStatus({ ids: selectedItems.value })
    toastStore.success('Berhasil', 'Status jadwal berhasil diperbarui')
    selectedItems.value = []
    // Remove fetchSchedules() to prevent loading state on bulk operations
  } catch (error) {
    toastStore.error('Error', 'Gagal memperbarui status jadwal')
  }
}

const exportData = async () => {
  try {
    const blob = await scheduleService.export({
      search: searchQuery.value,
      department: selectedDepartment.value,
      status: selectedStatus.value
    })

    const url = window.URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = `schedules_${new Date().toISOString().split('T')[0]}.xlsx`
    document.body.appendChild(a)
    a.click()
    document.body.removeChild(a)
    window.URL.revokeObjectURL(url)

    toastStore.success('Berhasil', 'Data berhasil diekspor')
  } catch (error) {
    toastStore.error('Error', 'Gagal mengekspor data')
  }
}




const bulkRestore = async () => {
  try {
    loading.value = true
    const restoredCount = await scheduleService.bulkRestore(selectedItems.value)
    toastStore.success('Berhasil', `${selectedItems.value.length} jadwal berhasil dipulihkan`)

    showBulkRestoreConfirm.value = false
    selectedItems.value = []

    // Refresh data from server to ensure consistency and update pagination info
    fetchSchedules(false)
  } catch (error) {
    toastStore.error('Error', 'Gagal memulihkan jadwal terpilih')
  } finally {
    loading.value = false
  }
}

const bulkForceDelete = async () => {
  try {
    loading.value = true
    await scheduleService.bulkForceDelete({ ids: selectedItems.value })
    toastStore.success('Berhasil', `${selectedItems.value.length} jadwal berhasil dihapus permanen`)

    showBulkForceDeleteConfirm.value = false
    selectedItems.value = []

    // Refresh data from server to ensure consistency and update pagination info
    fetchSchedules(false)
  } catch (error) {
    toastStore.error('Error', 'Gagal menghapus permanen jadwal terpilih')
  } finally {
    loading.value = false
  }
}

// Helper functions
const getEmploymentTypeLabel = (type) => {
  const labels = {
    'permanent': 'Tetap',
    'contract': 'Kontrak',
    'part_time': 'Paruh Waktu',
    'guest': 'Tamu'
  }
  return labels[type] || type
}

const viewScheduleDetail = async (schedule) => {
  try {
    loading.value = true
    // Fetch complete data including generated schedules
    const response = await scheduleService.getById(schedule.id)
    if (response) { // Response service might return data directly or wrapped
        const data = response.data || response // Try to get data property
        selectedScheduleForDetail.value = data
        showDetailModal.value = true
    } else {
      toastStore.error("Error", "Gagal mengambil detail jadwal")
    }
  } catch (error) {
    console.error(error)
    toastStore.error("Error", "Terjadi kesalahan sistem")
  } finally {
    loading.value = false
  }
}

const deleteMeeting = (meeting) => {
    meetingToDelete.value = meeting
    showDeleteMeetingConfirm.value = true
}

const executeDeleteMeeting = async () => {
    if (!meetingToDelete.value) return
    try {
        await meetingService.delete(meetingToDelete.value.id)
        toastStore.success('Berhasil', 'Pertemuan berhasil dihapus')
        showDeleteMeetingConfirm.value = false
        meetingToDelete.value = null
        fetchSchedules(false)
    } catch (error) {
        toastStore.error('Gagal', 'Gagal menghapus pertemuan')
    }
}

const confirmBulkDeleteMeetings = () => {
    if (selectedItems.value.length === 0) {
        toastStore.error('Error', 'Pilih minimal satu pertemuan untuk dihapus')
        return
    }
    showBulkDeleteMeetingsConfirm.value = true
}

const executeBulkDeleteMeetings = async () => {
    try {
        loading.value = true
        for (const id of selectedItems.value) {
            await meetingService.delete(id)
        }
        toastStore.success('Berhasil', `${selectedItems.value.length} pertemuan berhasil dihapus`)
        showBulkDeleteMeetingsConfirm.value = false
        selectedItems.value = []
        fetchSchedules(false)
    } catch (error) {
        toastStore.error('Gagal', 'Gagal menghapus pertemuan')
    } finally {
        loading.value = false
    }
}

const handleGenerateFromDetail = (schedule) => {
    // Close detail modal
    showDetailModal.value = false
    // Trigger auto schedule logic
    autoSchedule(schedule)
}

const openEditMeeting = (meeting) => {
  isMeetingReadonly.value = false
  selectedMeetingForEdit.value = meeting
  showEditMeetingModal.value = true
}

const openMeetingDetail = (meeting) => {
  isMeetingReadonly.value = true
  selectedMeetingForEdit.value = meeting
  showEditMeetingModal.value = true
}

const saveMeeting = async (data) => {
  try {
    // Call update API
    await meetingService.update(data.id, data)
    
    toastStore.success('Berhasil', 'Jadwal berhasil diperbarui')
    showEditMeetingModal.value = false
    
    // Refresh list
    fetchSchedules(false)
  } catch (error) {
    console.error('Update failed:', error)
    toastStore.error('Gagal', 'Gagal memperbarui jadwal')
  }
}

// Quick Reschedule Functions
const openQuickReschedule = (meeting) => {
  quickRescheduleTarget.value = meeting
  
  // Parse date
  let parsedDate = meeting.date
  if (parsedDate && parsedDate.includes('T')) {
    parsedDate = parsedDate.split('T')[0]
  } else if (parsedDate && parsedDate.length > 10) {
    parsedDate = parsedDate.substring(0, 10)
  }
  
  quickRescheduleForm.is_rescheduled = !!meeting.rescheduled_from
  quickRescheduleForm.date = parsedDate
  quickRescheduleForm.is_online = !!meeting.is_online
  quickRescheduleForm.room_id = meeting.room_id || (meeting.rooms && meeting.rooms.length > 0 ? meeting.rooms[0].id : '')
  quickRescheduleForm.lecturer_id = meeting.lecturer_id || (meeting.lecturers && meeting.lecturers.length > 0 ? meeting.lecturers[0].id : '')
  quickRescheduleForm.session_type = meeting.session_type || 'kuliah'
  
  // Reset conflict warnings
  conflictWarnings.has_conflicts = false
  conflictWarnings.room_conflicts = []
  conflictWarnings.lecturer_conflicts = []
  conflictWarnings.same_course_schedules = []
  conflictWarnings.team_teaching_lecturers = []
  conflictWarnings.current_lecturer_id = null
  
  showQuickRescheduleModal.value = true
  
  // Always check conflicts to get team teaching lecturers
  checkQuickRescheduleConflicts()
}

// Check conflicts for quick reschedule
const checkQuickRescheduleConflicts = async () => {
  if (!quickRescheduleTarget.value || !quickRescheduleForm.date) return
  
  conflictCheckLoading.value = true
  
  try {
    const response = await scheduleService.quickCheckConflicts({
      schedule_id: quickRescheduleTarget.value.id,
      date: quickRescheduleForm.date,
      room_id: quickRescheduleForm.is_online ? null : quickRescheduleForm.room_id,
      lecturer_id: quickRescheduleForm.lecturer_id || null
    })
    
    if (response.data) {
      conflictWarnings.has_conflicts = response.data.has_conflicts || false
      conflictWarnings.room_conflicts = response.data.room_conflicts || []
      conflictWarnings.lecturer_conflicts = response.data.lecturer_conflicts || []
      conflictWarnings.same_course_schedules = response.data.same_course_schedules || []
      conflictWarnings.team_teaching_lecturers = response.data.team_teaching_lecturers || []
      conflictWarnings.current_lecturer_id = response.data.current_lecturer_id
      
      // Set lecturer_id from response if not already set
      if (!quickRescheduleForm.lecturer_id && response.data.current_lecturer_id) {
        quickRescheduleForm.lecturer_id = response.data.current_lecturer_id
      }
    }
  } catch (error) {
    console.error('Conflict check failed:', error)
  } finally {
    conflictCheckLoading.value = false
  }
}

const saveQuickReschedule = async () => {
  if (!quickRescheduleTarget.value) return
  
  quickRescheduleSaving.value = true
  
  try {
    const updateData = {
      is_online: quickRescheduleForm.is_online
    }
    
    // If reschedule, set the new date and mark as rescheduled
    if (quickRescheduleForm.is_rescheduled) {
      updateData.date = quickRescheduleForm.date
      updateData.rescheduled_from = quickRescheduleTarget.value.id
    } else {
      // If reverting to normal, clear rescheduled_from
      updateData.rescheduled_from = null
    }
    
    // Handle room based on online/offline mode
    if (quickRescheduleForm.is_online) {
      updateData.room_id = null
    } else {
      updateData.room_id = quickRescheduleForm.room_id || null
    }
    
    // Include lecturer_id if changed
    if (quickRescheduleForm.lecturer_id) {
      updateData.lecturer_id = quickRescheduleForm.lecturer_id
    }
    
    await meetingService.update(quickRescheduleTarget.value.id, updateData)
    
    toastStore.success('Berhasil', 'Status jadwal berhasil diperbarui')
    showQuickRescheduleModal.value = false
    
    // Refresh list
    fetchSchedules(false)
  } catch (error) {
    console.error('Quick reschedule failed:', error)
    toastStore.error('Gagal', 'Gagal memperbarui status jadwal')
  } finally {
    quickRescheduleSaving.value = false
  }
}

// Quick Session Type Functions
const openQuickSessionType = (meeting) => {
  quickSessionTypeTarget.value = meeting
  quickSessionTypeForm.session_type = meeting.session_type || 'kuliah'
  showQuickSessionTypeModal.value = true
}

const saveQuickSessionType = async () => {
  if (!quickSessionTypeTarget.value) return
  
  quickSessionTypeSaving.value = true
  
  try {
    await meetingService.update(quickSessionTypeTarget.value.id, {
      session_type: quickSessionTypeForm.session_type
    })
    
    toastStore.success('Berhasil', 'Tipe perkuliahan berhasil diubah')
    showQuickSessionTypeModal.value = false
    
    // Refresh list
    fetchSchedules(false)
  } catch (error) {
    console.error('Quick session type update failed:', error)
    toastStore.error('Gagal', 'Gagal mengubah tipe perkuliahan')
  } finally {
    quickSessionTypeSaving.value = false
  }
}

const editScheduleFromDetail = async (schedule) => {
  try {
    // Clear current schedule first to reset form
    currentSchedule.value = null

    // Close detail modal and open edit modal
    showDetailModal.value = false
    showEditModal.value = true

    // Then fetch complete schedule data from backend
    const response = await scheduleService.getById(schedule.id)

    // Set schedule data (this should trigger the watcher in modal)
    currentSchedule.value = response.data

  } catch (error) {
    toastStore.handleError(error, 'Ambil data dosen')

    // Fallback to basic data if fetch fails
    currentSchedule.value = { ...schedule }
    showEditModal.value = true
    showDetailModal.value = false
  }
}

const closeModal = () => {
  showAddModal.value = false
  showEditModal.value = false
  showDetailModal.value = false
  currentSchedule.value = null
  selectedScheduleForDetail.value = null
}

const onScheduleSaved = () => {
  closeModal()
  fetchSchedules()
  fetchStats()
  toastStore.success('Berhasil', showEditModal.value ? 'Dosen berhasil diperbarui' : 'Dosen berhasil ditambahkan')
}

const onImported = () => {
  showImportModal.value = false
  fetchSchedules()
  fetchStats()
  toastStore.success('Berhasil', 'Data berhasil diimpor')
}

// Pagination methods
const prevPage = () => {
  if (pagination.current_page > 1) {
    pagination.current_page--
    fetchSchedules(false) // No loading animation for page navigation
  }
}

const nextPage = () => {
  if (pagination.current_page < pagination.last_page) {
    pagination.current_page++
    fetchSchedules(false) // No loading animation for page navigation
  }
}

const goToPage = (page) => {
  if (page !== '...' && page !== pagination.current_page) {
    pagination.current_page = page
    fetchSchedules(false) // No loading animation for page navigation
  }
}

// Watchers
// Watchers
watch(searchQuery, () => {
  pagination.current_page = 1
  fetchSchedules(true)
}, { debounce: 300 })

// Watch department to update classes
watch(selectedDepartment, (newVal) => {
    selectedClass.value = '' // Reset selected class
    fetchClasses(newVal)
})

watch([selectedStatus, selectedCourse, selectedClass, dateFrom, dateTo, activeTab], () => {
  pagination.current_page = 1
  fetchSchedules(true)
})

// Watch quick reschedule form for conflict checking
watch(
  () => [quickRescheduleForm.date, quickRescheduleForm.room_id, quickRescheduleForm.lecturer_id, quickRescheduleForm.is_online],
  () => {
    if (showQuickRescheduleModal.value && quickRescheduleForm.date) {
      checkQuickRescheduleConflicts()
    }
  }
)

// For development - set token if not exists
const setDevToken = () => {
  const token = localStorage.getItem('token');
  if (!token) {
    // Use the token we got from curl test
    const devToken = '5|knJKNnhuBhHKvBTD5QWV513ET8EM3kPK14FlmgAC4ffb5bf4';
    localStorage.setItem('token', devToken);
    return devToken;
  }
  return token;
};

// Lifecycle
// Lifecycle
onMounted(async () => {
    // Check local storage for token first
    const token = localStorage.getItem('token');
    if (!token) {
        // Try to set dev token
        setDevToken();
    }
    
    // Initial fetch
    await refreshData()
})
</script>

<style scoped>
.truncate {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

/* Custom scrollbar for table */
.overflow-x-auto::-webkit-scrollbar {
  height: 6px;
}

.overflow-x-auto::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}
</style>