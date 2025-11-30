<template>
  <Layout>
    <div class="min-h-screen bg-gray-50">
      <!-- Toast Component (Temporarily Disabled) -->
      <!--
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
      -->

      <!-- Auth Error State -->
      <div v-if="!isAuthenticated" class="flex items-center justify-center min-h-screen">
        <div class="text-center p-8 bg-white rounded-2xl shadow-xl max-w-md">
          <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <h2 class="text-xl font-bold text-gray-900 mb-2">Authentication Required</h2>
          <p class="text-gray-600 mb-6">Please login first to access the course management system.</p>
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
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <!-- Total Courses Card -->
          <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 opacity-10">
              <svg class="h-24 w-24 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
              </svg>
            </div>
            <div class="relative p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-12 h-12 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dt class="text-sm font-medium text-blue-100">Total Mata Kuliah</dt>
                  <dd class="text-3xl font-bold text-white">{{ courseStats.total_courses || 0 }}</dd>
                  <div class="flex items-center mt-1">
                    <svg class="w-4 h-4 text-blue-200 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-xs text-blue-100">Semua mata kuliah</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Active Courses Card -->
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
                  <dt class="text-sm font-medium text-green-100">Mata Kuliah Aktif</dt>
                  <dd class="text-3xl font-bold text-white">{{ courseStats.active_courses || 0 }}</dd>
                  <div class="flex items-center mt-1">
                    <svg class="w-4 h-4 text-green-200 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-xs text-green-100">Sedang aktif</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Inactive Courses Card -->
          <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-yellow-500 to-yellow-600 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 opacity-10">
              <svg class="h-24 w-24 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6z"/>
              </svg>
            </div>
            <div class="relative p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-12 h-12 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6z"/>
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dt class="text-sm font-medium text-yellow-100">Mata Kuliah Non-Aktif</dt>
                  <dd class="text-3xl font-bold text-white">{{ courseStats.inactive_courses || 0 }}</dd>
                  <div class="flex items-center mt-1">
                    <svg class="w-4 h-4 text-yellow-200 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-xs text-yellow-100">Tidak aktif</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Total Capacity Card -->
          <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
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
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dt class="text-sm font-medium text-purple-100">Total Kapasitas</dt>
                  <dd class="text-3xl font-bold text-white">{{ formatNumber(courseStats.total_capacity) }}</dd>
                  <div class="flex items-center mt-1">
                    <svg class="w-4 h-4 text-purple-200 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-xs text-purple-100">Kapasitas semua MK</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Actions and Filters -->
        <div class="mt-8 bg-white rounded-xl shadow-lg p-6">
          <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0">
            <div>
              <h2 class="text-2xl font-bold text-gray-900">Manajemen Mata Kuliah</h2>
              <p class="text-gray-600 mt-1">Kelola data mata kuliah program studi</p>
            </div>
            <div class="flex flex-wrap gap-3">
              <button
                @click="openCreateModal"
                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-600 to-green-700 text-white font-medium rounded-lg hover:from-green-700 hover:to-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 shadow-lg transform transition-all duration-200 hover:scale-105"
              >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Tambah Mata Kuliah
              </button>

              <button
                @click="openImportModal"
                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium rounded-lg hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-lg transform transition-all duration-200 hover:scale-105"
              >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                Import Excel
              </button>

              <button
                @click="handleExport"
                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-600 to-purple-700 text-white font-medium rounded-lg hover:from-purple-700 hover:to-purple-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 shadow-lg transform transition-all duration-200 hover:scale-105"
              >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export
              </button>
            </div>
          </div>

          <!-- Filters -->
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
              <div class="relative">
                <input
                  v-model="filters.search"
                  @input="debouncedSearch"
                  type="text"
                  placeholder="Cari kode atau nama..."
                  class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                  </svg>
                </div>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Program Studi</label>
              <select
                v-model="filters.program_study_id"
                @change="fetchData"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option value="">Semua Program Studi</option>
                <option v-for="program in programStudies" :key="program.id" :value="program.id">
                  {{ program.name }}
                </option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Semester</label>
              <select
                v-model="filters.semester"
                @change="fetchData"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option value="">Semua Semester</option>
                <option value="ganjil">Ganjil</option>
                <option value="genap">Genap</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
              <select
                v-model="filters.is_active"
                @change="fetchData"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option value="">Semua Status</option>
                <option :value="true">Aktif</option>
                <option :value="false">Non-Aktif</option>
              </select>
            </div>
          </div>

          <div class="mt-4 flex justify-between items-center">
            <div class="text-sm text-gray-600">
              Menampilkan <span class="font-semibold">{{ currentPage * perPage - perPage + 1 }}</span> hingga
              <span class="font-semibold">{{ Math.min(currentPage * perPage, total) }}</span> dari
              <span class="font-semibold">{{ total }}</span> data
            </div>
            <button
              @click="resetFilters"
              class="inline-flex items-center px-3 py-1 text-sm text-gray-600 hover:text-gray-800 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors"
            >
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
              </svg>
              Reset Filter
            </button>
          </div>
        </div>

        <!-- Bulk Actions -->
        <div v-if="selectedItems.length > 0" class="mt-4 bg-yellow-50 border border-yellow-200 rounded-xl p-4">
          <div class="flex justify-between items-center">
            <div class="flex items-center">
              <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.932-3l.524-8.969c.237-.719.322-1.564.184-2.382C15.769 2.569 13.758 2 12.002 2 10.247 2 8.236 2.569 7.384 3.807c-.138.818-.053 1.663.184 2.382l.524 8.969c.57 1.333 1.393 3 1.932 3z"/>
              </svg>
              <span class="text-sm font-medium text-yellow-800">
                {{ selectedItems.length }} mata kuliah terpilih
              </span>
            </div>
            <div class="flex space-x-2">
              <!-- Actions for Active Tab -->
              <template v-if="activeTab === 'active'">
                <button @click="bulkActivate" class="px-3 py-1 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                  Aktifkan
                </button>
                <button @click="bulkDeactivate" class="px-3 py-1 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm">
                  Non-Aktifkan
                </button>
                <button @click="bulkDelete" class="px-3 py-1 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">
                  Hapus
                </button>
              </template>

              <!-- Actions for Trash Tab -->
              <template v-else-if="activeTab === 'trash'">
                <button @click="bulkRestore" class="px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                  Restore
                </button>
                <button @click="confirmBulkForceDelete" class="px-3 py-1 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">
                  Hapus Permanen
                </button>
              </template>

              <button @click="clearSelection" class="px-3 py-1 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 text-sm">
                Batal
              </button>
            </div>
          </div>
        </div>

        <!-- Courses Table -->
        <div class="mt-6 bg-white rounded-xl shadow-lg overflow-hidden">
          <!-- Tab Navigation -->
          <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
              <button
                @click="switchTab('active')"
                :class="[
                  'py-4 px-6 text-sm font-medium border-b-2 transition-colors duration-200',
                  activeTab === 'active'
                    ? 'border-blue-500 text-blue-600 bg-blue-50'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                ]"
              >
                <div class="flex items-center space-x-2">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                  </svg>
                  <span>Data Mata Kuliah</span>
                </div>
              </button>

              <button
                @click="switchTab('trash')"
                :class="[
                  'py-4 px-6 text-sm font-medium border-b-2 transition-colors duration-200',
                  activeTab === 'trash'
                    ? 'border-red-500 text-red-600 bg-red-50'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                ]"
              >
                <div class="flex items-center space-x-2">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                  </svg>
                  <span>Sampah</span>
                  <span v-if="trashCount > 0" class="ml-2 px-2 py-0.5 text-xs bg-red-100 text-red-600 rounded-full">
                    {{ trashCount }}
                  </span>
                </div>
              </button>
            </nav>
          </div>

          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead :class="activeTab === 'active' ? 'bg-gradient-to-r from-blue-600 to-blue-700' : 'bg-gradient-to-r from-red-600 to-red-700'">
                <tr>
                  <th class="px-6 py-3 text-left">
                    <input
                      type="checkbox"
                      :checked="allSelected"
                      @change="toggleSelectAll"
                      class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    >
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Kode</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Nama Mata Kuliah</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">SKS</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Semester</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Tipe</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Program Studi</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">{{ activeTab === 'active' ? 'Aksi' : 'Aksi Sampah' }}</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <!-- Loading State -->
                <tr v-if="loading">
                  <td colspan="9" class="px-6 py-12 text-center">
                    <div class="flex justify-center">
                      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                    </div>
                    <p class="mt-4 text-gray-500">Memuat data mata kuliah...</p>
                  </td>
                </tr>

                <!-- Empty State -->
                <tr v-else-if="courses.length === 0">
                  <td colspan="9" class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada data mata kuliah</h3>
                    <p class="mt-1 text-sm text-gray-500">Belum ada mata kuliah yang terdaftar.</p>
                    <div class="mt-6">
                      <button @click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Tambah Mata Kuliah Pertama
                      </button>
                    </div>
                  </td>
                </tr>

                <!-- Data Rows -->
                <tr v-else v-for="course in courses" :key="course.id" class="hover:bg-gray-50 transition-colors">
                  <td class="px-6 py-4">
                    <input
                      type="checkbox"
                      :checked="selectedItems.includes(course.id)"
                      @change="toggleSelect(course.id)"
                      class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    >
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ course.course_code }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm text-gray-900">{{ course.course_name }}</div>
                    <div v-if="course.program_study" class="text-xs text-gray-500">{{ course.program_study.name }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ course.credits }} SKS</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                      {{ course.semester }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="course.course_type === 'mandatory' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'" class="px-2 py-1 text-xs font-medium rounded-full">
                      {{ course.course_type === 'mandatory' ? 'Wajib' : 'Pilihan' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">
                      {{ course.current_enrollment }}/{{ course.capacity }}
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
                      <div
                        :class="getEnrollmentBarColor(course.current_enrollment, course.capacity)"
                        :style="{ width: getEnrollmentPercentage(course.current_enrollment, course.capacity) + '%' }"
                        class="h-1.5 rounded-full"
                      ></div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <!-- Status Display (no toggle in trash) -->
                    <div v-if="activeTab === 'active'" class="flex items-center">
                      <!-- Toggle Switch for Active Tab -->
                      <button
                        @click="toggleStatus(course)"
                        :disabled="togglingStatus === course.id"
                        :class="[
                          'relative inline-flex h-6 w-11 items-center rounded-full transition-colors',
                          course.is_active ? 'bg-green-600' : 'bg-gray-300',
                          togglingStatus === course.id ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'
                        ]"
                      >
                        <span
                          :class="[
                            'inline-block h-4 w-4 transform rounded-full bg-white transition-transform',
                            course.is_active ? 'translate-x-6' : 'translate-x-1'
                          ]"
                        />
                      </button>
                      <span :class="course.is_active ? 'text-green-600' : 'text-gray-500'" class="ml-2 text-xs">
                        {{ course.is_active ? 'Aktif' : 'Non-Aktif' }}
                      </span>
                    </div>
                    <div v-else class="text-red-600 text-xs font-medium">
                      <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                      Dihapus
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2">
                      <!-- Actions for Active Tab -->
                      <template v-if="activeTab === 'active'">
                        <!-- Edit Button -->
                        <button
                          @click="editCourse(course)"
                          class="text-blue-600 hover:text-blue-900 p-1 rounded hover:bg-blue-50 transition-colors"
                          title="Edit"
                        >
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                          </svg>
                        </button>

                        <!-- Duplicate Button -->
                        <button
                          @click="duplicateCourse(course)"
                          :disabled="duplicatingCourse === course.id"
                          class="text-purple-600 hover:text-purple-900 p-1 rounded hover:bg-purple-50 transition-colors"
                          title="Duplikat"
                        >
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h9m0-9V4a2 2 0 00-2-2H5a2 2 0 00-2 2v3m-3 3V4a2 2 0 012-2h9a2 2 0 012 2v3"/>
                          </svg>
                        </button>

                        <!-- Delete Button -->
                        <button
                          @click="confirmDeleteCourse(course)"
                          class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50 transition-colors"
                          title="Hapus"
                        >
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                          </svg>
                        </button>
                      </template>

                      <!-- Actions for Trash Tab -->
                      <template v-else-if="activeTab === 'trash'">
                        <!-- Restore Button -->
                        <button
                          @click="restoreCourse(course)"
                          class="text-blue-600 hover:text-blue-900 p-1 rounded hover:bg-blue-50 transition-colors"
                          title="Restore"
                        >
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                          </svg>
                        </button>

                        <!-- Force Delete Button -->
                        <button
                          @click="confirmForceDeleteCourse(course)"
                          class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50 transition-colors"
                          title="Hapus Permanen"
                        >
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                          </svg>
                        </button>
                      </template>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="bg-gray-50 px-6 py-3 flex items-center justify-between border-t border-gray-200">
            <div class="flex-1 flex justify-between sm:hidden">
              <button
                @click="prevPage"
                :disabled="currentPage <= 1"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
              >
                Previous
              </button>
              <button
                @click="nextPage"
                :disabled="currentPage >= totalPages"
                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
              >
                Next
              </button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
              <div>
                <p class="text-sm text-gray-700">
                  Halaman <span class="font-medium">{{ currentPage }}</span> dari
                  <span class="font-medium">{{ totalPages }}</span>
                </p>
              </div>
              <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                  <button
                    @click="prevPage"
                    :disabled="currentPage <= 1"
                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50"
                  >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                  </button>

                  <template v-for="page in visiblePages" :key="page">
                    <button
                      v-if="page !== '...'"
                      @click="goToPage(page)"
                      :class="[
                        'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                        page === currentPage
                          ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                          : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                      ]"
                    >
                      {{ page }}
                    </button>
                    <span
                      v-else
                      class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700"
                    >
                      ...
                    </span>
                  </template>

                  <button
                    @click="nextPage"
                    :disabled="currentPage >= totalPages"
                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50"
                  >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                  </button>
                </nav>
              </div>
            </div>
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

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="w-full">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                  {{ editingCourse ? 'Edit Mata Kuliah' : 'Tambah Mata Kuliah' }}
                </h3>

                <form @submit.prevent="saveCourse">
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Kode Mata Kuliah -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Kode Mata Kuliah</label>
                      <input
                        v-model="form.course_code"
                        type="text"
                        required
                        :class="['w-full px-3 py-2 rounded-lg focus:outline-none focus:ring-2', formErrors.course_code ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500']"
                        placeholder="Contoh: IF001"
                      >
                      <p v-if="formErrors.course_code" class="mt-1 text-sm text-red-600">
                        {{ Array.isArray(formErrors.course_code) ? formErrors.course_code[0] : formErrors.course_code }}
                      </p>
                    </div>

                    <!-- SKS -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">SKS</label>
                      <input
                        v-model.number="form.credits"
                        type="number"
                        min="1"
                        max="12"
                        required
                        :class="['w-full px-3 py-2 rounded-lg focus:outline-none focus:ring-2', formErrors.credits ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500']"
                        placeholder="Contoh: 3"
                      >
                      <p v-if="formErrors.credits" class="mt-1 text-sm text-red-600">
                        {{ Array.isArray(formErrors.credits) ? formErrors.credits[0] : formErrors.credits }}
                      </p>
                    </div>

                    <!-- Nama Mata Kuliah -->
                    <div class="sm:col-span-2">
                      <label class="block text-sm font-medium text-gray-700 mb-1">Nama Mata Kuliah</label>
                      <input
                        v-model="form.course_name"
                        type="text"
                        required
                        :class="['w-full px-3 py-2 rounded-lg focus:outline-none focus:ring-2', formErrors.course_name ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500']"
                        placeholder="Contoh: Pemrograman Web"
                      >
                      <p v-if="formErrors.course_name" class="mt-1 text-sm text-red-600">
                        {{ Array.isArray(formErrors.course_name) ? formErrors.course_name[0] : formErrors.course_name }}
                      </p>
                    </div>

                    <!-- Program Studi -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Program Studi</label>
                      <select
                        v-model="form.program_study_id"
                        required
                        :class="['w-full px-3 py-2 rounded-lg focus:outline-none focus:ring-2', formErrors.program_study_id ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500']"
                      >
                        <option value="">Pilih Program Studi</option>
                        <option v-for="program in programStudies" :key="program.id" :value="program.id">
                          {{ program.name }}
                        </option>
                      </select>
                      <p v-if="formErrors.program_study_id" class="mt-1 text-sm text-red-600">
                        {{ Array.isArray(formErrors.program_study_id) ? formErrors.program_study_id[0] : formErrors.program_study_id }}
                      </p>
                    </div>

                    <!-- Semester -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
                      <select
                        v-model="form.semester"
                        required
                        :class="['w-full px-3 py-2 rounded-lg focus:outline-none focus:ring-2', formErrors.semester ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500']"
                      >
                        <option value="">Pilih Semester</option>
                        <option value="ganjil">Ganjil</option>
                        <option value="genap">Genap</option>
                      </select>
                      <p v-if="formErrors.semester" class="mt-1 text-sm text-red-600">
                        {{ Array.isArray(formErrors.semester) ? formErrors.semester[0] : formErrors.semester }}
                      </p>
                    </div>

  
                    <!-- Tipe Mata Kuliah -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Mata Kuliah</label>
                      <select
                        v-model="form.course_type"
                        required
                        :class="['w-full px-3 py-2 rounded-lg focus:outline-none focus:ring-2', formErrors.course_type ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500']"
                      >
                        <option value="">Pilih Tipe</option>
                        <option value="mandatory">Wajib</option>
                        <option value="elective">Pilihan</option>
                      </select>
                      <p v-if="formErrors.course_type" class="mt-1 text-sm text-red-600">
                        {{ Array.isArray(formErrors.course_type) ? formErrors.course_type[0] : formErrors.course_type }}
                      </p>
                    </div>

  
  
                    <!-- Status -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                      <select
                        v-model="form.is_active"
                        :class="['w-full px-3 py-2 rounded-lg focus:outline-none focus:ring-2', formErrors.is_active ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500']"
                      >
                        <option :value="true">Aktif</option>
                        <option :value="false">Non-Aktif</option>
                      </select>
                      <p v-if="formErrors.is_active" class="mt-1 text-sm text-red-600">
                        {{ Array.isArray(formErrors.is_active) ? formErrors.is_active[0] : formErrors.is_active }}
                      </p>
                    </div>

                    <!-- Deskripsi -->
                    <div class="sm:col-span-2">
                      <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                      <textarea
                        v-model="form.description"
                        rows="3"
                        :class="['w-full px-3 py-2 rounded-lg focus:outline-none focus:ring-2', formErrors.description ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500']"
                        placeholder="Deskripsi mata kuliah (opsional)"
                      ></textarea>
                      <p v-if="formErrors.description" class="mt-1 text-sm text-red-600">
                        {{ Array.isArray(formErrors.description) ? formErrors.description[0] : formErrors.description }}
                      </p>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button @click="saveCourse" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
              {{ editingCourse ? 'Update' : 'Simpan' }}
            </button>
            <button @click="closeModal" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
              Batal
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Confirmation Modal -->
    <div v-if="confirmationModal.show" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeConfirmationModal"></div>

        <!-- Modal panel -->
        <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <!-- Modal header with icon -->
          <div :class="[
            'px-4 pt-5 pb-4 sm:p-6 sm:pb-4',
            confirmationModal.type === 'delete' ? 'bg-red-50' : 'bg-yellow-50'
          ]">
            <div class="flex items-center">
              <div :class="[
                'mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full',
                confirmationModal.type === 'delete' ? 'bg-red-100' : 'bg-yellow-100'
              ]">
                <!-- Delete icon -->
                <svg v-if="confirmationModal.type === 'delete'" class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.932-3l.524-8.969c.237-.719.322-1.564.184-2.382C15.769 2.569 13.758 2 12.002 2 10.247 2 8.236 2.569 7.384 3.807c-.138.818-.053 1.663.184 2.382l.524 8.969c.57 1.333 1.393 3 1.932 3z"/>
                </svg>
                <!-- Warning icon -->
                <svg v-else class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.932-3l.524-8.969c.237-.719.322-1.564.184-2.382C15.769 2.569 13.758 2 12.002 2 10.247 2 8.236 2.569 7.384 3.807c-.138.818-.053 1.663.184 2.382l.524 8.969c.57 1.333 1.393 3 1.932 3z"/>
                </svg>
              </div>
              <div class="ml-4 text-left">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                  {{ confirmationModal.title }}
                </h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-700">
                    {{ confirmationModal.message }}
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Modal content (details) -->
          <div v-if="confirmationModal.details" class="px-4 pb-4 sm:p-6 sm:pb-4">
            <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
              <div class="flex justify-between items-center text-sm">
                <span class="font-medium text-gray-700">Item yang akan dihapus:</span>
                <span class="text-gray-600">{{ confirmationModal.details }}</span>
              </div>
            </div>
          </div>

          <!-- Modal actions -->
          <div class="px-4 py-3 bg-gray-50 px-6 py-3 sm:flex sm:flex-row-reverse">
            <button
              type="button"
              @click="confirmAction"
              :class="[
                'inline-flex w-full justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm',
                confirmationModal.type === 'delete'
                  ? 'bg-red-600 hover:bg-red-700 focus:ring-red-500'
                  : 'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500'
              ]"
            >
              {{ confirmationModal.confirmText || 'Lanjutkan' }}
            </button>
            <button
              type="button"
              @click="closeConfirmationModal"
              class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Batal
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Course Import Modal -->
    <CourseImportModal
      :show="showImportModal"
      @close="showImportModal = false"
      @import-success="handleImportSuccess"
    />
  </Layout>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useToastStore } from '@/stores/toast'
import { useAuthStore } from '@/stores/auth'
import Layout from '@/components/Layout.vue'
// import Toast from '@/components/Toast.vue' // Temporarily disabled
import CourseImportModal from '@/components/modals/CourseImportModal.vue'
import courseService from '@/services/courseService'
import programStudyService from '@/services/programStudyService'

const router = useRouter()
const toastStore = useToastStore()
const authStore = useAuthStore()

// State
const loading = ref(false)
const courses = ref([])
const programStudies = ref([])

// Tab Management
const activeTab = ref('active')
const trashCount = ref(0)
const courseStats = reactive({
  total_courses: 0,
  active_courses: 0,
  inactive_courses: 0,
  total_capacity: 0,
  total_enrollment: 0
})

const filters = reactive({
  search: '',
  program_study_id: '',
  semester: '',
  is_active: ''
})

// Pagination
const currentPage = ref(1)
const perPage = ref(20)
const total = ref(0)

// Modal state
const showModal = ref(false)
const editingCourse = ref(null)
const showImportModal = ref(false)
const form = reactive({
  course_code: '',
  course_name: '',
  description: '',
  credits: 3,
  semester: 'ganjil',
    course_type: 'mandatory',
    capacity: 50,
  current_enrollment: 0,
  is_active: true,
  program_study_id: ''
})
const formErrors = ref({})

// Bulk actions
const selectedItems = ref([])

// Loading states
const togglingStatus = ref(null)
const duplicatingCourse = ref(null)

// Confirmation modal
const confirmationModal = reactive({
  show: false,
  type: 'delete',
  title: '',
  message: '',
  details: '',
  confirmText: 'Hapus',
  action: null
})


// Force delete tracking
const bulkForceDeleteType = ref('bulk')
const courseToDelete = ref(null)

// Bulk action modal
const showBulkActionModal = ref(false)

// Computed
const isAuthenticated = computed(() => authStore.isAuthenticated)

const allSelected = computed(() => {
  return courses.value.length > 0 && selectedItems.value.length === courses.value.length
})

const totalPages = computed(() => {
  return Math.ceil(total.value / perPage.value)
})

const visiblePages = computed(() => {
  const pages = []
  const startPage = Math.max(1, currentPage.value - 2)
  const endPage = Math.min(totalPages.value, startPage + 4)

  for (let i = startPage; i <= endPage; i++) {
    pages.push(i)
  }

  return pages
})

// Debounced search
let searchTimeout = null
const debouncedSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    currentPage.value = 1
    fetchData()
  }, 500)
}

// Methods
const fetchActiveCourses = async () => {
  try {
    const params = {
      page: currentPage.value,
      per_page: perPage.value,
      ...filters
    }

    // Remove empty filters
    Object.keys(params).forEach(key => {
      if (params[key] === '') {
        delete params[key]
      }
    })

    const response = await courseService.getAll(params)
    courses.value = response.data || []
    total.value = response.meta?.pagination?.total || 0

    // Update selected items if needed
    selectedItems.value = selectedItems.value.filter(id =>
      courses.value.some(course => course.id === id)
    )
  } catch (error) {
    console.error('Failed to fetch active courses:', error)
    toastStore.addToast({
      type: 'error',
      title: 'Error',
      message: 'Gagal memuat data mata kuliah aktif'
    })
  }
}

const fetchTrashCourses = async () => {
  try {
    const params = {
      page: currentPage.value,
      per_page: perPage.value,
      ...filters
    }

    // Remove empty filters
    Object.keys(params).forEach(key => {
      if (params[key] === '') {
        delete params[key]
      }
    })

    const response = await courseService.getTrash(params)
    courses.value = response.data || []
    total.value = response.meta?.pagination?.total || 0
    trashCount.value = response.meta?.pagination?.total || 0

    // Update selected items if needed
    selectedItems.value = selectedItems.value.filter(id =>
      courses.value.some(course => course.id === id)
    )
  } catch (error) {
    console.error('Failed to fetch trash courses:', error)
    toastStore.addToast({
      type: 'error',
      title: 'Error',
      message: 'Gagal memuat data sampah mata kuliah'
    })
  }
}

const fetchData = async () => {
  try {
    loading.value = true

    if (activeTab.value === 'trash') {
      await fetchTrashCourses()
    } else {
      await fetchActiveCourses()
    }
  } catch (error) {
    console.error('Failed to fetch courses:', error)
    toastStore.addToast({
      type: 'error',
      title: 'Error',
      message: 'Gagal memuat data mata kuliah'
    })
  } finally {
    loading.value = false
  }
}

const fetchStats = async () => {
  try {
    const response = await courseService.getStatistics()
    Object.assign(courseStats, response)
  } catch (error) {
    console.error('Failed to fetch stats:', error)
  }
}

const fetchProgramStudies = async () => {
  try {
    const response = await programStudyService.getAll()
    programStudies.value = response.data || []
  } catch (error) {
    console.error('Failed to fetch program studies:', error)
  }
}

const switchTab = (tab) => {
  if (activeTab.value !== tab) {
    activeTab.value = tab
    currentPage.value = 1 // Reset to first page
    selectedItems.value = [] // Clear selection
    fetchData()
  }
}

const refreshData = async () => {
  await Promise.all([
    fetchData(),
    fetchStats(),
    fetchProgramStudies()
  ])
}

const resetFilters = () => {
  Object.assign(filters, {
    search: '',
    program_study_id: '',
    semester: '',
    is_active: ''
  })
  currentPage.value = 1
  fetchData()
}

// Pagination
const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
    fetchData()
  }
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
    fetchData()
  }
}

const goToPage = (page) => {
  currentPage.value = page
  fetchData()
}

// Bulk selection methods
const toggleSelectAll = () => {
  if (allSelected.value) {
    selectedItems.value = []
  } else {
    selectedItems.value = courses.value.map(course => course.id)
  }
}

const toggleSelect = (id) => {
  const index = selectedItems.value.indexOf(id)
  if (index > -1) {
    selectedItems.value.splice(index, 1)
  } else {
    selectedItems.value.push(id)
  }
}

const clearSelection = () => {
  selectedItems.value = []
}

// Bulk actions
const bulkDelete = async () => {
  if (selectedItems.value.length === 0) return

  showConfirmationModal({
    type: 'delete',
    title: 'Hapus Mata Kuliah',
    message: 'Apakah Anda yakin ingin menghapus mata kuliah yang dipilih?',
    details: `${selectedItems.value.length} mata kuliah`,
    action: async () => {
      try {
        await courseService.bulkDelete(selectedItems.value)
        selectedItems.value = []
        await refreshData()
        toastStore.addToast({
          type: 'success',
          title: 'Berhasil',
          message: 'Mata kuliah berhasil dihapus'
        })
      } catch (error) {
        console.error('Failed to bulk delete:', error)
        toastStore.addToast({
          type: 'error',
          title: 'Error',
          message: 'Gagal menghapus mata kuliah'
        })
      }
    }
  })
}

const bulkActivate = async () => {
  if (selectedItems.value.length === 0) return

  try {
    await courseService.bulkToggleStatus(selectedItems.value, true)
    selectedItems.value = []
    await refreshData()
    toastStore.addToast({
      type: 'success',
      title: 'Berhasil',
      message: 'Mata kuliah berhasil diaktifkan'
    })
  } catch (error) {
    console.error('Failed to bulk activate:', error)
    toastStore.addToast({
      type: 'error',
      title: 'Error',
      message: 'Gagal mengaktifkan mata kuliah'
    })
  }
}

const bulkDeactivate = async () => {
  if (selectedItems.value.length === 0) return

  try {
    await courseService.bulkToggleStatus(selectedItems.value, false)
    selectedItems.value = []
    await refreshData()
    toastStore.addToast({
      type: 'success',
      title: 'Berhasil',
      message: 'Mata kuliah berhasil dinon-aktifkan'
    })
  } catch (error) {
    console.error('Failed to bulk deactivate:', error)
    toastStore.addToast({
      type: 'error',
      title: 'Error',
      message: 'Gagal menon-aktifkan mata kuliah'
    })
  }
}

// CRUD operations
const openCreateModal = () => {
  resetForm()
  editingCourse.value = null
  showModal.value = true
}

const editCourse = (course) => {
  resetForm()
  editingCourse.value = course

  Object.assign(form, {
    course_code: course.course_code,
    course_name: course.course_name,
    description: course.description || '',
    credits: course.credits,
    semester: course.semester,
        course_type: course.course_type,
            is_active: course.is_active,
    program_study_id: course.program_study_id
  })

  showModal.value = true
}

const saveCourse = async () => {
  try {
    formErrors.value = {}

    if (editingCourse.value) {
      await courseService.update(editingCourse.value.id, form)
      toastStore.addToast({
        type: 'success',
        title: 'Berhasil',
        message: 'Mata kuliah berhasil diperbarui'
      })
    } else {
      await courseService.create(form)
      toastStore.addToast({
        type: 'success',
        title: 'Berhasil',
        message: 'Mata kuliah berhasil ditambahkan'
      })
    }

    closeModal()
    await refreshData()
  } catch (error) {
    if (error.response?.data?.errors) {
      formErrors.value = error.response.data.errors
    } else {
      console.error('Failed to save course:', error)
      toastStore.addToast({
        type: 'error',
        title: 'Error',
        message: 'Gagal menyimpan mata kuliah'
      })
    }
  }
}

const confirmDeleteCourse = (course) => {
  showConfirmationModal({
    type: 'delete',
    title: 'Hapus Mata Kuliah',
    message: 'Apakah Anda yakin ingin menghapus mata kuliah ini?',
    details: `${course.course_name} (${course.course_code})`,
    action: async () => {
      try {
        await courseService.delete(course.id)
        await refreshData()
        toastStore.addToast({
          type: 'success',
          title: 'Berhasil',
          message: 'Mata kuliah berhasil dihapus'
        })
      } catch (error) {
        console.error('Failed to delete course:', error)
        toastStore.addToast({
          type: 'error',
          title: 'Error',
          message: 'Gagal menghapus mata kuliah'
        })
      }
    }
  })
}

const duplicateCourse = async (course) => {
  try {
    duplicatingCourse.value = course.id
    await courseService.duplicate(course.id)
    await refreshData()
    toastStore.addToast({
      type: 'success',
      title: 'Berhasil',
      message: 'Mata kuliah berhasil diduplikasi'
    })
  } catch (error) {
    console.error('Failed to duplicate course:', error)
    toastStore.addToast({
      type: 'error',
      title: 'Error',
      message: 'Gagal menduplikasi mata kuliah'
    })
  } finally {
    duplicatingCourse.value = null
  }
}

const toggleStatus = async (course) => {
  try {
    togglingStatus.value = course.id
    const newStatus = !course.is_active
    await courseService.update(course.id, { is_active: newStatus })

    course.is_active = newStatus

    // Update stats
    if (newStatus) {
      courseStats.active_courses++
      courseStats.inactive_courses--
    } else {
      courseStats.active_courses--
      courseStats.inactive_courses++
    }

    toastStore.addToast({
      type: 'success',
      title: 'Berhasil',
      message: `Mata kuliah berhasil ${newStatus ? 'diaktifkan' : 'dinon-aktifkan'}`
    })
  } catch (error) {
    console.error('Failed to toggle status:', error)
    toastStore.addToast({
      type: 'error',
      title: 'Error',
      message: 'Gagal mengubah status mata kuliah'
    })
  } finally {
    togglingStatus.value = null
  }
}

// Import/Export
const openImportModal = () => {
  console.log('Opening import modal...')
  showImportModal.value = true
  console.log('showImportModal.value:', showImportModal.value)
}

const handleImportSuccess = async (result) => {
  showImportModal.value = false

  if (result.errors && result.errors.length > 0) {
    toastStore.addToast({
      type: 'warning',
      title: 'Import Selesai dengan Warning',
      message: `${result.imported} berhasil, ${result.errors.length} gagal`
    })
  } else {
    toastStore.addToast({
      type: 'success',
      title: 'Import Berhasil',
      message: `${result.imported} mata kuliah berhasil diimport`
    })
  }

  await refreshData()
}

const handleFileImport = async (event) => {
  const file = event.target.files[0]
  if (!file) return

  try {
    const response = await courseService.import(file)

    if (response.errors && response.errors.length > 0) {
      toastStore.addToast({
        type: 'warning',
        title: 'Import Selesai dengan Warning',
        message: `${response.imported_count} berhasil, ${response.errors.length} gagal`
      })
    } else {
      toastStore.addToast({
        type: 'success',
        title: 'Import Berhasil',
        message: `${response.imported_count} mata kuliah berhasil diimport`
      })
    }

    await refreshData()
  } catch (error) {
    console.error('Failed to import:', error)
    toastStore.addToast({
      type: 'error',
      title: 'Error',
      message: 'Gagal mengimport data mata kuliah'
    })
  }

  // Reset file input
  event.target.value = ''
}

const handleExport = async () => {
  try {
    const blob = await courseService.export(filters)
    const url = window.URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = url
    link.download = `courses_${new Date().toISOString().split('T')[0]}.xlsx`
    link.click()
    window.URL.revokeObjectURL(url)

    toastStore.addToast({
      type: 'success',
      title: 'Export Berhasil',
      message: 'Data mata kuliah berhasil diekspor'
    })
  } catch (error) {
    console.error('Failed to export:', error)
    toastStore.addToast({
      type: 'error',
      title: 'Error',
      message: 'Gagal mengekspor data mata kuliah'
    })
  }
}

// Modal methods
const closeModal = () => {
  showModal.value = false
  resetForm()
}

const resetForm = () => {
  Object.assign(form, {
    course_code: '',
    course_name: '',
    description: '',
    credits: 3,
    semester: 'ganjil',
        course_type: 'mandatory',
        capacity: 50,
    is_active: true,
    program_study_id: ''
  })
  formErrors.value = {}
}

// Confirmation modal methods
const showConfirmationModal = (options) => {
  Object.assign(confirmationModal, {
    show: true,
    type: 'delete',
    title: '',
    message: '',
    details: '',
    confirmText: 'Hapus',
    action: null,
    ...options
  })
}

const confirmAction = async () => {
  if (confirmationModal.action) {
    await confirmationModal.action()
  }
  closeConfirmationModal()
}

const closeConfirmationModal = () => {
  Object.assign(confirmationModal, {
    show: false,
    type: 'delete',
    title: '',
    message: '',
    details: '',
    confirmText: 'Hapus',
    action: null
  })
}

// Utility methods
const formatNumber = (num) => {
  return new Intl.NumberFormat('id-ID').format(num || 0)
}

const getEnrollmentPercentage = (current, capacity) => {
  return capacity > 0 ? Math.round((current / capacity) * 100) : 0
}

const getEnrollmentBarColor = (current, capacity) => {
  const percentage = getEnrollmentPercentage(current, capacity)
  if (percentage >= 90) return 'bg-red-500'
  if (percentage >= 75) return 'bg-yellow-500'
  return 'bg-green-500'
}

// Authentication check
const checkAuth = () => {
  if (!authStore.isAuthenticated) {
    router.push('/login')
    return false
  }
  return true
}

// Trash Operations
const restoreCourse = async (course) => {
  try {
    await courseService.restore(course.id)
    await refreshData()
    toastStore.addToast({
      type: 'success',
      title: 'Berhasil',
      message: 'Mata kuliah berhasil dipulihkan'
    })
  } catch (error) {
    console.error('Failed to restore course:', error)
    toastStore.addToast({
      type: 'error',
      title: 'Error',
      message: 'Gagal memulihkan mata kuliah'
    })
  }
}

const confirmForceDeleteCourse = (course) => {
  selectedItems.value = [course.id]
  bulkForceDeleteType.value = 'single'
  courseToDelete.value = course

  showConfirmationModal({
    type: 'delete',
    title: 'Hapus Permanen Mata Kuliah',
    message: `Apakah Anda yakin ingin menghapus permanen mata kuliah "${course.course_name}"? Tindakan ini tidak dapat dibatalkan.`,
    confirmText: 'Hapus Permanen',
    action: bulkForceDelete
  })
}

const confirmBulkForceDelete = () => {
  if (selectedItems.value.length === 0) return

  bulkForceDeleteType.value = 'bulk'
  courseToDelete.value = null

  showConfirmationModal({
    type: 'delete',
    title: 'Hapus Permanen Mata Kuliah Terpilih',
    message: `Apakah Anda yakin ingin menghapus permanen ${selectedItems.value.length} mata kuliah terpilih? Tindakan ini tidak dapat dibatalkan.`,
    confirmText: 'Hapus Permanen',
    action: bulkForceDelete
  })
}

const bulkRestore = async () => {
  if (selectedItems.value.length === 0) return

  const count = selectedItems.value.length
  try {
    const promises = selectedItems.value.map(id =>
      courseService.restore(id)
    )

    await Promise.all(promises)

    await refreshData()
    selectedItems.value = []
    showBulkActionModal.value = false

    toastStore.addToast({
      type: 'success',
      title: 'Berhasil',
      message: `${count} mata kuliah berhasil dipulihkan`
    })
  } catch (error) {
    console.error('Failed to bulk restore courses:', error)
    toastStore.addToast({
      type: 'error',
      title: 'Error',
      message: 'Gagal memulihkan beberapa mata kuliah'
    })
  }
}

const bulkForceDelete = async () => {
  try {
    if (bulkForceDeleteType.value === 'single' && courseToDelete.value) {
      // Single course permanent delete
      await courseService.forceDelete(courseToDelete.value.id)

      toastStore.addToast({
        type: 'success',
        title: 'Berhasil',
        message: 'Mata kuliah berhasil dihapus permanen'
      })
    } else if (bulkForceDeleteType.value === 'bulk' && selectedItems.value.length > 0) {
      const count = selectedItems.value.length
      // Bulk permanent delete
      const promises = selectedItems.value.map(id =>
        courseService.forceDelete(id)
      )

      await Promise.all(promises)

      toastStore.addToast({
        type: 'success',
        title: 'Berhasil',
        message: `${count} mata kuliah berhasil dihapus permanen`
      })
    }

    // Close modal and reset
    selectedItems.value = []
    courseToDelete.value = null
    bulkForceDeleteType.value = 'bulk'

    await refreshData()
  } catch (error) {
    console.error('Failed to permanently delete courses:', error)
    toastStore.addToast({
      type: 'error',
      title: 'Error',
      message: 'Gagal menghapus permanen mata kuliah'
    })
  }
}

// Initialize
onMounted(async () => {
  if (checkAuth()) {
    await refreshData()
  }
})
</script>