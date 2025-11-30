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
          <p class="text-gray-600 mb-6">Please login first to access the academic year management system.</p>
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
          <!-- Total Academic Years Card -->
          <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 opacity-10">
              <svg class="h-24 w-24 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/>
              </svg>
            </div>
            <div class="relative p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-12 h-12 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dt class="text-sm font-medium text-blue-100">Total Tahun Akademik</dt>
                  <dd class="text-3xl font-bold text-white">{{ academicYearStats.total_academic_years || 0 }}</dd>
                  <div class="flex items-center mt-1">
                    <svg class="w-4 h-4 text-blue-200 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-xs text-blue-100">Semua tahun akademik</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Active Academic Years Card -->
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
                  <dt class="text-sm font-medium text-green-100">Aktif</dt>
                  <dd class="text-3xl font-bold text-white">{{ academicYearStats.by_status?.active || 0 }}</dd>
                  <div class="flex items-center mt-1">
                    <svg class="w-4 h-4 text-green-200 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-xs text-green-100">Sedang berlangsung</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Planning Card -->
          <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 opacity-10">
              <svg class="h-24 w-24 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
              </svg>
            </div>
            <div class="relative p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-12 h-12 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dt class="text-sm font-medium text-amber-100">Perencanaan</dt>
                  <dd class="text-3xl font-bold text-white">{{ academicYearStats.by_status?.planning || 0 }}</dd>
                  <div class="flex items-center mt-1">
                    <svg class="w-4 h-4 text-amber-200 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-xs text-amber-100">Dalam perencanaan</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Completed Card -->
          <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 opacity-10">
              <svg class="h-24 w-24 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"/>
              </svg>
            </div>
            <div class="relative p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-12 h-12 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dt class="text-sm font-medium text-purple-100">Selesai</dt>
                  <dd class="text-3xl font-bold text-white">{{ academicYearStats.by_status?.completed || 0 }}</dd>
                  <div class="flex items-center mt-1">
                    <svg class="w-4 h-4 text-purple-200 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-xs text-purple-100">Telah selesai</span>
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
            <div class="flex flex-col xl:flex-row gap-6">
              <!-- Enhanced Search -->
              <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Pencarian Tahun Akademik</label>
                <div class="relative group">
                  <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                  </div>
                  <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Cari nama, kode, atau deskripsi tahun akademik..."
                    class="block w-full pl-12 pr-12 py-2.5 border-2 border-gray-200 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all duration-200 hover:border-gray-300"
                  />
                  <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <div v-if="searchQuery" class="flex items-center">
                      <span class="text-xs text-gray-500 mr-2">{{ academicYears.length }} hasil</span>
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
              <div class="xl:w-96">
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter</label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                  <div class="relative">
                    <select
                      v-model="selectedStatus"
                      class="appearance-none w-full px-3 py-2.5 pr-8 border-2 border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white hover:border-gray-300 transition-all duration-200"
                    >
                      <option value="">Semua Status</option>
                      <option value="upcoming">Akan Datang</option>
                      <option value="active">Aktif</option>
                      <option value="completed">Selesai</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                      <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                      </svg>
                    </div>
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
          <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-t border-gray-200">
            <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
              <div class="flex flex-wrap gap-2">
                <button
                  @click="showAddModal = true"
                  class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-medium rounded-lg hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 text-sm"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                  </svg>
                  Tambah Tahun Akademik
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
          <!-- Table Header -->
          <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                </div>
                <div>
                  <h2 class="text-xl font-bold text-gray-900">Data Tahun Akademik</h2>
                  <p class="text-sm text-gray-500">Kelola informasi tahun akademik dan pengaturan terkait</p>
                </div>
              </div>

              <!-- Selected Items Counter -->
              <div v-if="selectedItems.length > 0" class="flex items-center space-x-3">
                <span class="text-sm font-medium text-gray-700">
                  {{ selectedItems.length }} tahun akademik dipilih
                </span>
                <div class="h-6 w-px bg-gray-300"></div>
                <div class="flex items-center space-x-2">
                  <button
                    @click="confirmBulkDelete"
                    class="px-3 py-1 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors text-sm font-medium"
                  >
                    Hapus
                  </button>
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
            <span class="mt-4 text-gray-600 font-medium">Memuat data tahun akademik...</span>
            <div class="text-xs text-gray-500 mt-2">Debug: {{ academicYears.length }} items loaded</div>
          </div>

          <!-- Empty State -->
          <div v-else-if="!loading && academicYears.length === 0" class="text-center py-16">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"/>
              </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada data tahun akademik</h3>
            <p class="text-gray-500 mb-6 max-w-sm mx-auto">
              Mulai dengan menambahkan tahun akademik baru untuk mengelola data akademik
            </p>
            <button
              @click="showAddModal = true"
              class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-medium rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 text-sm"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
              </svg>
              Tambah Tahun Akademik
            </button>
          </div>

          <!-- Modern Data Table -->
          <div v-show="!loading && academicYears.length > 0" class="w-full">
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
                  Showing {{ pagination.from || 1 }} to {{ pagination.to || academicYears.length }} of {{ pagination.total }} results
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
                  <tr>
                    <th scope="col" class="px-6 py-4 text-left">
                      <input
                        type="checkbox"
                        @change="toggleSelectAll"
                        :checked="allItemsSelected"
                        class="h-5 w-5 text-blue-600 border-gray-300 rounded-lg focus:ring-blue-500 focus:ring-2"
                      />
                    </th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                      Informasi Tahun Akademik
                    </th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                      Periode
                    </th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                      Status
                    </th>
                    <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">
                      Aksi
                    </th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                  <tr
                    v-for="(academicYear, index) in academicYears.filter(ay => ay && ay.id)"
                    :key="academicYear.id"
                    :class="[
                      'hover:bg-blue-50 transition-colors duration-150',
                      index % 2 === 0 ? 'bg-white' : 'bg-gray-50/30'
                    ]"
                  >
                    <td class="px-6 py-4">
                      <input
                        type="checkbox"
                        v-model="selectedItems"
                        :value="academicYear.id"
                        class="h-5 w-5 text-blue-600 border-gray-300 rounded-lg focus:ring-blue-500 focus:ring-2"
                      />
                    </td>

                    <!-- Academic Year Info -->
                    <td class="px-6 py-4">
                      <div class="flex items-start space-x-4">
                        <!-- Icon -->
                        <div class="flex-shrink-0">
                          <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-semibold text-base shadow-md">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                          </div>
                        </div>

                        <!-- Academic Year Details -->
                        <div class="flex-1 min-w-0">
                          <!-- Name -->
                          <h3 class="text-base font-semibold text-gray-900 truncate hover:text-blue-600 transition-colors">
                            {{ academicYear.name }}
                          </h3>

                          <!-- Code -->
                          <div class="flex items-center mt-1 text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            {{ academicYear.code }}
                          </div>

                          <!-- Description -->
                          <div v-if="academicYear.description" class="flex items-center mt-2 text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span class="line-clamp-2">{{ academicYear.description }}</span>
                          </div>
                        </div>
                      </div>
                    </td>

                    <!-- Period -->
                    <td class="px-6 py-4">
                      <div class="space-y-1">
                        <div class="flex items-center">
                          <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                          </svg>
                          <div>
                            <div class="text-sm font-medium text-gray-900">
                              {{ formatDate(academicYear.start_date) }} - {{ formatDate(academicYear.end_date) }}
                            </div>
                            <div class="text-xs text-gray-500">
                              {{ academicYear.semesters || 2 }} Semester
                            </div>
                          </div>
                        </div>
                      </div>
                    </td>

                    <!-- Status -->
                    <td class="px-6 py-4">
                      <div class="space-y-2">
                        <span
                          :class="[
                            'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium',
                            academicYear.is_active ? 'bg-gradient-to-r from-green-100 to-green-200 text-green-800 border border-green-300' :
                            academicYear.status === 'planning' ? 'bg-gradient-to-r from-amber-100 to-amber-200 text-amber-800 border border-amber-300' :
                            academicYear.status === 'completed' ? 'bg-gradient-to-r from-purple-100 to-purple-200 text-purple-800 border border-purple-300' :
                            'bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 border border-gray-300'
                          ]"
                        >
                          <span class="w-2 h-2 mr-1.5 rounded-full"
                                :class="[
                                  'inline-block',
                                  academicYear.is_active ? 'bg-green-500' :
                                  academicYear.status === 'planning' ? 'bg-amber-500' :
                                  academicYear.status === 'completed' ? 'bg-purple-500' :
                                  'bg-gray-500'
                                ]">
                          </span>
                          {{ academicYear.is_active ? 'Aktif' :
                             academicYear.status === 'planning' ? 'Perencanaan' :
                             academicYear.status === 'completed' ? 'Selesai' :
                             academicYear.status || 'Unknown' }}
                        </span>
                      </div>
                    </td>

                    <!-- Actions -->
                    <td class="px-6 py-4">
                      <div class="flex justify-end space-x-1">
                        <!-- Detail Button -->
                        <button
                          @click="viewAcademicYearDetail(academicYear)"
                          class="p-2 text-green-600 hover:bg-green-100 rounded-lg transition-colors duration-200 group"
                          title="Lihat detail tahun akademik"
                        >
                          <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                          </svg>
                        </button>

                        <!-- Edit Button -->
                        <button
                          @click="editAcademicYear(academicYear)"
                          class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors duration-200 group"
                          title="Edit tahun akademik"
                        >
                          <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                          </svg>
                        </button>

                        <!-- Duplicate Button -->
                        <button
                          @click="duplicateAcademicYear(academicYear)"
                          class="p-2 text-purple-600 hover:bg-purple-100 rounded-lg transition-colors duration-200 group"
                          title="Duplikat tahun akademik"
                        >
                          <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                          </svg>
                        </button>

                        <!-- Toggle Active Button -->
                        <button
                          @click="toggleActiveStatus(academicYear)"
                          :class="[
                            'p-2 rounded-lg transition-colors duration-200 group',
                            academicYear.is_active
                              ? 'text-amber-600 hover:bg-amber-100'
                              : 'text-green-600 hover:bg-green-100'
                          ]"
                          :title="academicYear.is_active ? 'Nonaktifkan tahun akademik' : 'Aktifkan tahun akademik'"
                        >
                          <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path v-if="academicYear.is_active" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                          </svg>
                        </button>

                        <!-- Delete Button -->
                        <button
                          @click="confirmDelete(academicYear)"
                          class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition-colors duration-200 group"
                          title="Hapus tahun akademik"
                        >
                          <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                          </svg>
                        </button>
                      </div>
                    </td>
                  </tr>
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
      <!-- Simple form modal for now - can be enhanced later -->
      <div v-if="showAddModal || showEditModal" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
          <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
          </div>

          <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form @submit.prevent="saveAcademicYear">
              <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="mb-4">
                  <h3 class="text-lg font-medium text-gray-900 mb-2">
                    {{ showEditModal ? 'Edit Tahun Akademik' : 'Tambah Tahun Akademik Baru' }}
                  </h3>
                </div>

                <div class="space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Nama Tahun Akademik</label>
                    <input
                      v-model="currentAcademicYear.name"
                      type="text"
                      required
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm px-3 py-2 border"
                      placeholder="contoh: Tahun Akademik 2025/2026"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700">Kode</label>
                    <input
                      v-model="currentAcademicYear.code"
                      type="text"
                      required
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm px-3 py-2 border"
                      placeholder="contoh: 2025"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700">Tahun Kalender</label>
                    <input
                      v-model="currentAcademicYear.academic_calendar_year"
                      type="text"
                      required
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm px-3 py-2 border"
                      placeholder="contoh: 2025-2026"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                    <textarea
                      v-model="currentAcademicYear.description"
                      rows="3"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm px-3 py-2 border"
                      placeholder="Deskripsi tahun akademik"
                    ></textarea>
                  </div>

                  <div class="grid grid-cols-2 gap-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                      <input
                        v-model="currentAcademicYear.start_date"
                        type="date"
                        required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm px-3 py-2 border"
                      />
                    </div>

                    <div>
                      <label class="block text-sm font-medium text-gray-700">Tanggal Selesai</label>
                      <input
                        v-model="currentAcademicYear.end_date"
                        type="date"
                        required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm px-3 py-2 border"
                      />
                    </div>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700">Periode Penerimaan</label>
                    <select
                      v-model="currentAcademicYear.admission_period"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm px-3 py-2 border"
                    >
                      <option value="ganjil">Ganjil (September - Februari)</option>
                      <option value="genap">Genap (Maret - Agustus)</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Pilih periode penerimaan mahasiswa S2</p>
                  </div>

                  <!-- Admission Period -->
                  <div class="border-t pt-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Periode Penerimaan & Registrasi</h4>
                    <div class="grid grid-cols-2 gap-4">
                      <div>
                        <label class="block text-sm font-medium text-gray-700">Mulai Penerimaan</label>
                        <input
                          v-model="currentAcademicYear.admission_start_date"
                          type="date"
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm px-3 py-2 border"
                        />
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700">Selesai Penerimaan</label>
                        <input
                          v-model="currentAcademicYear.admission_end_date"
                          type="date"
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm px-3 py-2 border"
                        />
                      </div>
                    </div>
                  </div>

                  <!-- Academic Calendar -->
                  <div class="border-t pt-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Kalender Akademik</h4>
                    <div class="grid grid-cols-2 gap-4">
                      <div>
                        <label class="block text-sm font-medium text-gray-700">Mulai Perkuliahan</label>
                        <input
                          v-model="currentAcademicYear.class_start_date"
                          type="date"
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm px-3 py-2 border"
                        />
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700">Selesai Perkuliahan</label>
                        <input
                          v-model="currentAcademicYear.class_end_date"
                          type="date"
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm px-3 py-2 border"
                        />
                      </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                      <div>
                        <label class="block text-sm font-medium text-gray-700">Ujian Tengah Semester</label>
                        <div class="grid grid-cols-2 gap-2">
                          <input
                            v-model="currentAcademicYear.mid_exam_start_date"
                            type="date"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm px-3 py-2 border"
                            placeholder="Mulai"
                          />
                          <input
                            v-model="currentAcademicYear.mid_exam_end_date"
                            type="date"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm px-3 py-2 border"
                            placeholder="Selesai"
                          />
                        </div>
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700">Ujian Akhir Semester</label>
                        <div class="grid grid-cols-2 gap-2">
                          <input
                            v-model="currentAcademicYear.final_exam_start_date"
                            type="date"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm px-3 py-2 border"
                            placeholder="Mulai"
                          />
                          <input
                            v-model="currentAcademicYear.final_exam_end_date"
                            type="date"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm px-3 py-2 border"
                            placeholder="Selesai"
                          />
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- S2 Specific Deadlines -->
                  <div class="border-t pt-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Deadline S2</h4>
                    <div class="grid grid-cols-2 gap-4">
                      <div>
                        <label class="block text-sm font-medium text-gray-700">Batas Pengumpulan Tesis</label>
                        <input
                          v-model="currentAcademicYear.thesis_deadline"
                          type="date"
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm px-3 py-2 border"
                        />
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700">Tanggal Yudisium</label>
                        <input
                          v-model="currentAcademicYear.yudisium_date"
                          type="date"
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm px-3 py-2 border"
                        />
                      </div>
                    </div>
                  </div>

                  <!-- Settings & Limits -->
                  <div class="border-t pt-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Pengaturan & Batasan</h4>
                    <div class="grid grid-cols-2 gap-4">
                      <div>
                        <label class="block text-sm font-medium text-gray-700">Maks SKS per Semester</label>
                        <input
                          v-model.number="currentAcademicYear.max_credit_per_semester"
                          type="number"
                          required
                          min="6"
                          max="24"
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm px-3 py-2 border"
                          placeholder="contoh: 12"
                        />
                        <p class="text-xs text-gray-500 mt-1">S2 biasanya 12-15 SKS</p>
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select
                          v-model="currentAcademicYear.status"
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm px-3 py-2 border"
                        >
                          <option value="upcoming">Akan Datang</option>
                          <option value="active">Aktif</option>
                          <option value="completed">Selesai</option>
                        </select>
                      </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                      <div>
                        <label class="block text-sm font-medium text-gray-700">Biaya Kuliah per Semester (Rp)</label>
                        <input
                          v-model.number="currentAcademicYear.tuition_fee"
                          type="number"
                          min="0"
                          step="0.01"
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm px-3 py-2 border"
                          placeholder="contoh: 15000000"
                        />
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700">Biaya Pendaftaran (Rp)</label>
                        <input
                          v-model.number="currentAcademicYear.registration_fee"
                          type="number"
                          min="0"
                          step="0.01"
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm px-3 py-2 border"
                          placeholder="contoh: 500000"
                        />
                      </div>
                    </div>
                  </div>

                  <!-- Additional Settings -->
                  <div class="border-t pt-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Pengaturan Tambahan</h4>
                    <div class="space-y-2">
                      <label class="flex items-center">
                        <input
                          v-model="currentAcademicYear.is_visible_to_students"
                          type="checkbox"
                          class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        />
                        <span class="ml-2 text-sm text-gray-700">Tampilkan ke mahasiswa</span>
                      </label>
                      <label class="flex items-center">
                        <input
                          v-model="currentAcademicYear.allow_course_registration"
                          type="checkbox"
                          class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        />
                        <span class="ml-2 text-sm text-gray-700">Izinkan registrasi matakuliah</span>
                      </label>
                      <label class="flex items-center">
                        <input
                          v-model="currentAcademicYear.allow_schedule_changes"
                          type="checkbox"
                          class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        />
                        <span class="ml-2 text-sm text-gray-700">Izinkan perubahan jadwal</span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>

              <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button
                  type="submit"
                  :disabled="loading"
                  class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
                >
                  {{ showEditModal ? 'Update' : 'Simpan' }}
                </button>
                <button
                  type="button"
                  @click="closeModal"
                  class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                >
                  Batal
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Detail Modal -->
      <div v-if="showDetailModal" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
          <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
          </div>

          <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <div class="mb-4">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Detail Tahun Akademik</h3>
              </div>

              <div v-if="selectedAcademicYearForDetail" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Nama</label>
                    <p class="mt-1 text-sm text-gray-900">{{ selectedAcademicYearForDetail.name }}</p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Kode</label>
                    <p class="mt-1 text-sm text-gray-900">{{ selectedAcademicYearForDetail.code }}</p>
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                  <p class="mt-1 text-sm text-gray-900">{{ selectedAcademicYearForDetail.description || '-' }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                    <p class="mt-1 text-sm text-gray-900">{{ formatDate(selectedAcademicYearForDetail.start_date) }}</p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal Selesai</label>
                    <p class="mt-1 text-sm text-gray-900">{{ formatDate(selectedAcademicYearForDetail.end_date) }}</p>
                  </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <p class="mt-1 text-sm text-gray-900">{{ selectedAcademicYearForDetail.status }}</p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Aktif</label>
                    <p class="mt-1 text-sm text-gray-900">{{ selectedAcademicYearForDetail.is_active ? 'Ya' : 'Tidak' }}</p>
                  </div>
                </div>
              </div>
            </div>

            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
              <button
                type="button"
                @click="closeModal"
                class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm"
              >
                Tutup
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Confirm Delete Modal -->
      <ConfirmModal
        v-if="showDeleteConfirm"
        :show="showDeleteConfirm"
        title="Hapus Tahun Akademik"
        :message="`Apakah Anda yakin ingin menghapus tahun akademik ${currentAcademicYear?.name}?`"
        @confirm="deleteAcademicYear"
        @cancel="showDeleteConfirm = false"
      />

      <ConfirmModal
        v-if="showBulkDeleteConfirm"
        :show="showBulkDeleteConfirm"
        title="Hapus Tahun Akademik Terpilih"
        :message="`Apakah Anda yakin ingin menghapus ${selectedItems.length} tahun akademik yang dipilih?`"
        @confirm="bulkDelete"
        @cancel="showBulkDeleteConfirm = false"
      />
    </div>
  </Layout>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useToastStore } from '@/stores/toast'
import academicYearService from '@/services/academicYearService'
import Layout from '@/components/Layout.vue'
import ConfirmModal from '@/components/modals/ConfirmModal.vue'
import Toast from '@/components/Toast.vue'

const router = useRouter()
const toastStore = useToastStore()

// Auth state
const isAuthenticated = computed(() => {
  const token = localStorage.getItem('token');
  return token !== null && token !== undefined && token !== '';
});

// State
const loading = ref(false)
const academicYears = ref([])
const academicYearStats = ref({})
const selectedItems = ref([])

// Filters
const searchQuery = ref('')
const selectedStatus = ref('')

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
const showDeleteConfirm = ref(false)
const showBulkDeleteConfirm = ref(false)
const perPageOptions = [5, 10, 20, 50, 100]
const currentAcademicYear = ref({})
const selectedAcademicYearForDetail = ref(null)

// Computed
const allItemsSelected = computed(() => {
  return academicYears.value.length > 0 && selectedItems.value.length === academicYears.value.length
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
const formatDate = (dateString) => {
  if (!dateString) return '-'
  const date = new Date(dateString)
  return date.toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const fetchAcademicYears = async (showLoading = true) => {
  try {
    if (showLoading) {
      loading.value = true
    }
    selectedItems.value = []

    const params = {
      page: pagination.current_page,
      per_page: pagination.per_page
    }

    // Add filters if they have values
    if (searchQuery.value) {
      params.search = searchQuery.value
    }

    if (selectedStatus.value) {
      params.status = selectedStatus.value
    }

    console.log('📋 Fetching academic years with params:', params)
    const response = await academicYearService.getAll(params)
    console.log('✅ Response received:', response)

    // Ensure we have proper data structure
    if (response && response.data) {
      // API returns nested structure: data.data contains academic years array
      const responseData = response.data;

      console.log('🔍 Parsing response data:', responseData);

      // Handle both possible response structures
      if (responseData.data && Array.isArray(responseData.data)) {
        // New API format: { data: { data: [...], total: ..., current_page: ... }, meta: {...} }
        academicYears.value = responseData.data;
        pagination.current_page = responseData.meta?.current_page || responseData.current_page || 1;
        pagination.last_page = responseData.meta?.last_page || responseData.last_page || 1;
        pagination.per_page = responseData.meta?.per_page || responseData.per_page || 10;
        pagination.total = responseData.meta?.total || responseData.total || academicYears.value.length;
        pagination.from = responseData.meta?.from || responseData.from || 1;
        pagination.to = responseData.meta?.to || responseData.to || academicYears.value.length;
      } else if (Array.isArray(responseData)) {
        // Simple array format
        academicYears.value = responseData;
        pagination.current_page = 1;
        pagination.last_page = 1;
        pagination.per_page = academicYears.value.length;
        pagination.total = academicYears.value.length;
        pagination.from = 1;
        pagination.to = academicYears.value.length;
      } else {
        console.warn('Unexpected response structure:', responseData);
        academicYears.value = [];
      }

      console.log('✅ Academic years data set:', academicYears.value.length, 'items')
      console.log('✅ Pagination set:', pagination)

      // Force Vue reactivity update
      academicYears.value = [...academicYears.value];

    } else {
      console.warn('Invalid response structure:', response)
      academicYears.value = []
    }
  } catch (error) {
    console.error('Error fetching academic years:', error)

    // Handle authentication errors specifically
    if (error.response?.status === 401) {
      console.warn('Authentication failed - token invalid or expired')
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
      console.warn('Access forbidden - insufficient permissions')
      toastStore.addToast({
        type: 'error',
        title: 'Access Denied',
        message: 'You do not have permission to access this resource.'
      })
      return
    }

    academicYears.value = []
    toastStore.addToast({
      type: 'error',
      title: 'Error',
      message: error.response?.data?.message || 'Gagal memuat data tahun akademik'
    })
  } finally {
    if (showLoading) {
      loading.value = false
    }
    console.log('Loading completed, academic years count:', academicYears.value.length)
  }
}

const changePerPage = (value) => {
  pagination.per_page = parseInt(value)
  pagination.current_page = 1
  fetchAcademicYears(false)
}

const fetchStats = async () => {
  try {
    const response = await academicYearService.getStatistics()
    academicYearStats.value = response.data || {}
  } catch (error) {
    console.error('Error fetching stats:', error)

    // Handle authentication errors specifically
    if (error.response?.status === 401) {
      console.warn('Stats API authentication failed - token invalid or expired')
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
      console.warn('Stats API access forbidden - insufficient permissions')
      return
    }

    // Don't show toast for stats errors to avoid spamming the user
    console.warn('Failed to fetch academic year statistics')
  }
}

const refreshData = () => {
  fetchAcademicYears()
  fetchStats()
}

const toggleSelectAll = () => {
  if (allItemsSelected.value) {
    selectedItems.value = []
  } else {
    selectedItems.value = academicYears.value.map(ay => ay.id)
  }
}

const saveAcademicYear = async () => {
  try {
    loading.value = true

    // Set default values for required fields
    const academicYearData = {
      ...currentAcademicYear.value,
      academic_calendar_year: currentAcademicYear.value.academic_calendar_year || currentAcademicYear.value.code || '',
      max_credit_per_semester: currentAcademicYear.value.max_credit_per_semester || 12,
      admission_period: currentAcademicYear.value.admission_period || 'ganjil',
      is_visible_to_students: currentAcademicYear.value.is_visible_to_students !== undefined ? currentAcademicYear.value.is_visible_to_students : true,
      allow_course_registration: currentAcademicYear.value.allow_course_registration !== undefined ? currentAcademicYear.value.allow_course_registration : false,
      allow_schedule_changes: currentAcademicYear.value.allow_schedule_changes !== undefined ? currentAcademicYear.value.allow_schedule_changes : true,
    }

    let response
    if (showEditModal.value) {
      response = await academicYearService.update(currentAcademicYear.value.id, academicYearData)
      toastStore.success('Berhasil', 'Tahun akademik berhasil diperbarui')
    } else {
      response = await academicYearService.create(academicYearData)
      toastStore.success('Berhasil', 'Tahun akademik berhasil ditambahkan')
    }

    closeModal()
    fetchAcademicYears()
    fetchStats()
  } catch (error) {
    console.error('Error saving academic year:', error)
    const message = error.response?.data?.message || error.message || 'Gagal menyimpan tahun akademik'
    toastStore.error('Error', message)
  } finally {
    loading.value = false
  }
}

const editAcademicYear = async (academicYear) => {
  try {
    // Open modal first (will show empty form)
    showEditModal.value = true

    // Set academic year data
    currentAcademicYear.value = { ...academicYear }

    // Then fetch complete academic year data from backend
    const response = await academicYearService.getById(academicYear.id)
    console.log('📋 EditAcademicYear - API Response:', response.data)

    // Set academic year data (this should trigger the watcher in modal)
    currentAcademicYear.value = response.data
    console.log('📋 EditAcademicYear - CurrentAcademicYear set:', currentAcademicYear.value)

  } catch (error) {
    console.error('Error fetching academic year data:', error)
    toastStore.handleError(error, 'Ambil data tahun akademik')

    // Fallback to basic data if fetch fails
    currentAcademicYear.value = { ...academicYear }
    console.log('📋 EditAcademicYear - Fallback data set:', currentAcademicYear.value)
  }
}

const confirmDelete = (academicYear) => {
  currentAcademicYear.value = academicYear
  showDeleteConfirm.value = true
}

const deleteAcademicYear = async () => {
  try {
    await academicYearService.delete(currentAcademicYear.value.id)
    toastStore.success('Berhasil', 'Tahun akademik berhasil dihapus')
    showDeleteConfirm.value = false
    currentAcademicYear.value = null

    // Refresh data from server to ensure consistency and update pagination info
    fetchAcademicYears(false)
  } catch (error) {
    console.error('Error deleting academic year:', error)
    toastStore.error('Error', 'Gagal menghapus tahun akademik')
  }
}

const confirmBulkDelete = () => {
  showBulkDeleteConfirm.value = true
}

const bulkDelete = async () => {
  try {
    const promises = selectedItems.value.map(id => academicYearService.delete(id))
    await Promise.all(promises)

    toastStore.success('Berhasil', `${selectedItems.value.length} tahun akademik berhasil dihapus`)
    showBulkDeleteConfirm.value = false
    selectedItems.value = []

    // Refresh data from server to ensure consistency and update pagination info
    fetchAcademicYears(false)
  } catch (error) {
    console.error('Error bulk deleting academic years:', error)
    toastStore.error('Error', 'Gagal menghapus tahun akademik terpilih')
  }
}

const duplicateAcademicYear = async (academicYear) => {
  try {
    const response = await academicYearService.duplicate(academicYear.id)
    toastStore.success('Berhasil', `Tahun akademik "${response.data.name}" berhasil diduplikasi`)

    // Refresh data to show the duplicated item
    fetchAcademicYears()
    fetchStats()
  } catch (error) {
    console.error('Error duplicating academic year:', error)
    toastStore.error('Error', error.response?.data?.message || 'Gagal menduplikasi tahun akademik')
  }
}

const toggleActiveStatus = async (academicYear) => {
  try {
    const response = await academicYearService.toggleActive(academicYear.id)
    const message = academicYear.is_active
      ? 'Tahun akademik berhasil dinonaktifkan'
      : 'Tahun akademik berhasil diaktifkan'

    toastStore.success('Berhasil', message)

    // Update the academic year in the local array for immediate UI update
    const index = academicYears.value.findIndex(ay => ay.id === academicYear.id)
    if (index > -1) {
      academicYears.value[index] = response.data
    }

    // Also refresh stats to reflect the change
    fetchStats()
  } catch (error) {
    console.error('Error toggling active status:', error)
    toastStore.error('Error', error.response?.data?.message || 'Gagal mengubah status tahun akademik')
  }
}

const viewAcademicYearDetail = (academicYear) => {
  selectedAcademicYearForDetail.value = academicYear
  showDetailModal.value = true
}

const closeModal = () => {
  showAddModal.value = false
  showEditModal.value = false
  showDetailModal.value = false
  currentAcademicYear.value = {}
  selectedAcademicYearForDetail.value = null
}

const exportData = async () => {
  try {
    const blob = await academicYearService.export({
      search: searchQuery.value,
      status: selectedStatus.value
    })

    const url = window.URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = `academic_years_${new Date().toISOString().split('T')[0]}.xlsx`
    document.body.appendChild(a)
    a.click()
    document.body.removeChild(a)
    window.URL.revokeObjectURL(url)

    toastStore.success('Berhasil', 'Data berhasil diekspor')
  } catch (error) {
    console.error('Error exporting data:', error)
    toastStore.error('Error', 'Gagal mengekspor data')
  }
}

// Pagination methods
const prevPage = () => {
  if (pagination.current_page > 1) {
    pagination.current_page--
    fetchAcademicYears(false)
  }
}

const nextPage = () => {
  if (pagination.current_page < pagination.last_page) {
    pagination.current_page++
    fetchAcademicYears(false)
  }
}

const goToPage = (page) => {
  if (page !== '...' && page !== pagination.current_page) {
    pagination.current_page = page
    fetchAcademicYears(false)
  }
}

// Watchers
watch([searchQuery, selectedStatus], () => {
  pagination.current_page = 1
  fetchAcademicYears(true)
}, { debounce: 150 })

// For development - set token if not exists
const setDevToken = () => {
  const token = localStorage.getItem('token');
  if (!token) {
    // Use the fresh token from login
    const devToken = '5|MEZycBtM16vrde0w6IpFYZ8KVyUaJXJ27j7OorNI19d91d94';
    localStorage.setItem('token', devToken);
    console.log('🔑 Development token set for testing');
    return devToken;
  }
  return token;
};

// Lifecycle
onMounted(() => {
  console.log('Component mounted, checking auth...')

  // For development: set token if not exists
  const token = setDevToken();

  if (!token) {
    console.warn('No authentication token found, redirecting to login...')
    window.location.href = '/login'
    return
  }

  console.log('Authentication token found, fetching initial data...')
  fetchAcademicYears()
  fetchStats()
})
</script>

<style scoped>
.truncate {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
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