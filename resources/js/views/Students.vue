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
          <p class="text-gray-600 mb-6">Please login first to access the student management system.</p>
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
          <!-- Total Students Card -->
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
                  <dt class="text-sm font-medium text-blue-100">Total Mahasiswa</dt>
                  <dd class="text-3xl font-bold text-white">{{ studentStats.total_students || 0 }}</dd>
                  <div class="flex items-center mt-1">
                    <svg class="w-4 h-4 text-blue-200 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-xs text-blue-100">Semua mahasiswa</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Active Students Card -->
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
                  <dt class="text-sm font-medium text-green-100">Mahasiswa Aktif</dt>
                  <dd class="text-3xl font-bold text-white">{{ studentStats.by_status?.active || 0 }}</dd>
                  <div class="flex items-center mt-1">
                    <svg class="w-4 h-4 text-green-200 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-xs text-green-100">Sedang studi</span>
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
                  <dt class="text-sm font-medium text-amber-100">Cuti</dt>
                  <dd class="text-3xl font-bold text-white">{{ studentStats.by_status?.on_leave || 0 }}</dd>
                  <div class="flex items-center mt-1">
                    <svg class="w-4 h-4 text-amber-200 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-xs text-amber-100">Sedang cuti</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Graduated Card -->
          <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 opacity-10">
              <svg class="h-24 w-24 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M5 13l4 4L19 7"/>
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
                  <dt class="text-sm font-medium text-purple-100">Lulus</dt>
                  <dd class="text-3xl font-bold text-white">{{ studentStats.by_status?.graduated || 0 }}</dd>
                  <div class="flex items-center mt-1">
                    <svg class="w-4 h-4 text-purple-200 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-xs text-purple-100">Telah lulus</span>
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
                <label class="block text-sm font-medium text-gray-700 mb-2">Pencarian Mahasiswa</label>
                <div class="relative group">
                  <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                  </div>
                  <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Cari nama, NIM, email, atau program studi..."
                    class="block w-full pl-12 pr-12 py-2.5 border-2 border-gray-200 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all duration-200 hover:border-gray-300"
                  />
                  <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <div v-if="searchQuery" class="flex items-center">
                      <span class="text-xs text-gray-500 mr-2">{{ students.length }} hasil</span>
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
                      v-model="selectedProgramStudy"
                      class="appearance-none w-full px-3 py-2.5 pr-8 border-2 border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white hover:border-gray-300 transition-all duration-200"
                    >
                      <option value="">Semua Program Studi</option>
                      <option v-for="ps in programStudies" :key="ps.id" :value="ps.id">{{ ps.name }}</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                      <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                      </svg>
                    </div>
                  </div>

                  <div class="relative">
                    <select
                      v-model="selectedStatus"
                      class="appearance-none w-full px-3 py-2.5 pr-8 border-2 border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white hover:border-gray-300 transition-all duration-200"
                    >
                      <option value="">Semua Status</option>
                      <option value="active">Aktif</option>
                      <option value="on_leave">Cuti</option>
                      <option value="graduated">Lulus</option>
                      <option value="dropped_out">Putus</option>
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
                  Tambah Mahasiswa
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
                  Data Aktif
                  <span class="ml-2 px-2 py-0.5 text-xs bg-blue-100 text-blue-600 rounded-full">
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
                    {{ activeTab === 'active' ? 'Data Mahasiswa' : 'Sampah Mahasiswa' }}
                  </h2>
                  <p class="text-sm text-gray-500">
                    {{ activeTab === 'active' ? 'Kelola informasi mahasiswa dan data akademik' : 'Data mahasiswa yang telah dihapus' }}
                  </p>
                </div>
              </div>

              <!-- Selected Items Counter -->
              <div v-if="selectedItems.length > 0" class="flex items-center space-x-3">
                <span class="text-sm font-medium text-gray-700">
                  {{ selectedItems.length }} mahasiswa dipilih
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
            <span class="mt-4 text-gray-600 font-medium">Memuat data mahasiswa...</span>
            <div class="text-xs text-gray-500 mt-2">Debug: {{ students.length }} items loaded</div>
          </div>

          <!-- Empty State -->
          <div v-else-if="!loading && students.length === 0" class="text-center py-16">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
              </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">
              {{ activeTab === 'active' ? 'Belum ada data mahasiswa' : 'Tidak ada data sampah' }}
            </h3>
            <p class="text-gray-500 mb-6 max-w-sm mx-auto">
              {{ activeTab === 'active' ? 'Mulai dengan menambahkan mahasiswa baru untuk mengelola data akademik' : 'Tidak ada mahasiswa yang telah dihapus' }}
            </p>
            <button
              v-if="activeTab === 'active'"
              @click="showAddModal = true"
              class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-medium rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 text-sm"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
              </svg>
              Tambah Mahasiswa
            </button>
          </div>

          <!-- Modern Data Table -->
          <div v-show="!loading && students.length > 0" class="w-full">
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
                  Showing {{ pagination.from || 1 }} to {{ pagination.to || students.length }} of {{ pagination.total }} results
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
                      Informasi Mahasiswa
                    </th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                      Program Studi
                    </th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                      Status
                    </th>
                    <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">
                      {{ activeTab === 'active' ? 'Aksi' : 'Aksi Sampah' }}
                    </th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                  <tr
                    v-for="(student, index) in students.filter(s => s && s.id)"
                    :key="student.id"
                    :class="[
                      'hover:bg-blue-50 transition-colors duration-150',
                      index % 2 === 0 ? 'bg-white' : 'bg-gray-50/30'
                    ]"
                  >
                    <td class="px-6 py-4">
                      <input
                        type="checkbox"
                        v-model="selectedItems"
                        :value="student.id"
                        class="h-5 w-5 text-blue-600 border-gray-300 rounded-lg focus:ring-blue-500 focus:ring-2"
                      />
                    </td>

                    <!-- Combined Student Info: Photo + Name + NIM + Email -->
                    <td class="px-6 py-4">
                      <div class="flex items-start space-x-4">
                        <!-- Photo -->
                        <div class="flex-shrink-0">
                          <div class="relative group">
                            <img
                              v-if="student.photo"
                              :src="`/storage/${student.photo}`"
                              :alt="student.name"
                              class="h-14 w-14 rounded-xl object-cover border-2 border-white shadow-md group-hover:shadow-lg transition-all duration-200"
                              @error="$event.target.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(student.name || '?')}&background=3B82F6&color=fff&size=56`"
                            />
                            <div
                              v-else
                              class="h-14 w-14 rounded-xl bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-semibold text-base shadow-md group-hover:shadow-lg transition-all duration-200"
                            >
                              {{ student.name ? student.name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase() : '??' }}
                            </div>
                            <!-- Status Indicator -->
                            <div
                              :class="[
                                'absolute -bottom-1 -right-1 w-4 h-4 rounded-full border-2 border-white',
                                student.status === 'active' ? 'bg-green-500' :
                                student.status === 'on_leave' ? 'bg-yellow-500' :
                                student.status === 'graduated' ? 'bg-purple-500' :
                                'bg-gray-400'
                              ]"
                            ></div>
                          </div>
                        </div>

                        <!-- Student Details -->
                        <div class="flex-1 min-w-0">
                          <!-- Name -->
                          <h3 class="text-base font-semibold text-gray-900 truncate hover:text-blue-600 transition-colors">
                            {{ student.name }}
                          </h3>

                          <!-- NIM -->
                          <div class="flex items-center mt-1 text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                            </svg>
                            {{ student.nim || student.student_number }}
                          </div>

                          <!-- Email -->
                          <div class="flex items-center mt-2 text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            {{ student.email }}
                          </div>

                          <!-- Batch Year and GPA (if available) -->
                          <div class="flex items-center space-x-4 mt-2">
                            <span v-if="student.batch_year || student.enrollment_year" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                              {{ student.batch_year || student.enrollment_year }}
                            </span>
                            <span v-if="student.gpa" class="text-xs text-gray-500">
                              GPA: {{ student.gpa }}
                            </span>
                          </div>
                        </div>
                      </div>
                    </td>

                    <!-- Program Study -->
                    <td class="px-6 py-4">
                      <div class="space-y-1">
                        <div class="flex items-center">
                          <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                          </svg>
                          <div>
                            <div class="text-sm font-medium text-gray-900 truncate" :title="student.program_study?.name || student.program_study">
                              {{ student.program_study?.name || student.program_study || 'Belum ditugaskan' }}
                            </div>
                            <div v-if="student.class_name" class="text-xs text-gray-500 truncate" :title="student.class_name">
                              Kelas: {{ student.class_name }}
                            </div>
                          </div>
                        </div>
                      </div>
                    </td>

                    <!-- Status -->
                    <td class="px-6 py-4">
                      <div class="space-y-2">
                        <!-- Academic Status -->
                        <span
                          :class="[
                            'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium',
                            student.status === 'active' ? 'bg-gradient-to-r from-green-100 to-green-200 text-green-800 border border-green-300' :
                            student.status === 'on_leave' ? 'bg-gradient-to-r from-yellow-100 to-yellow-200 text-yellow-800 border border-yellow-300' :
                            student.status === 'graduated' ? 'bg-gradient-to-r from-purple-100 to-purple-200 text-purple-800 border border-purple-300' :
                            student.status === 'dropped_out' ? 'bg-gradient-to-r from-red-100 to-red-200 text-red-800 border border-red-300' :
                            'bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 border border-gray-300'
                          ]"
                        >
                          <span class="w-2 h-2 mr-1.5 rounded-full"
                                :class="[
                                  'inline-block',
                                  student.status === 'active' ? 'bg-green-500' :
                                  student.status === 'on_leave' ? 'bg-yellow-500' :
                                  student.status === 'graduated' ? 'bg-purple-500' :
                                  student.status === 'dropped_out' ? 'bg-red-500' :
                                  'bg-gray-500'
                                ]">
                          </span>
                          {{ student.status === 'active' ? 'Aktif' :
                             student.status === 'on_leave' ? 'Cuti' :
                             student.status === 'graduated' ? 'Lulus' :
                             student.status === 'dropped_out' ? 'Putus' :
                             student.status || 'Unknown' }}
                        </span>

                        <!-- Credits Taken (if active) -->
                        <div v-if="student.status === 'active' && student.credits_taken" class="text-xs text-gray-600">
                          SKS: {{ student.credits_taken }}
                        </div>
                      </div>
                    </td>

                    <!-- Actions -->
                    <td class="px-6 py-4">
                      <div class="flex justify-end space-x-1">
                        <template v-if="activeTab === 'active'">
                          <!-- Detail Button -->
                          <button
                            @click="viewStudentDetail(student)"
                            class="p-2 text-green-600 hover:bg-green-100 rounded-lg transition-colors duration-200 group"
                            title="Lihat detail mahasiswa"
                          >
                            <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                          </button>

                          <!-- Edit Button -->
                          <button
                            @click="editStudent(student)"
                            class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors duration-200 group"
                            title="Edit mahasiswa"
                          >
                            <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                          </button>

                          <!-- Duplicate Button -->
                          <button
                            @click="duplicateStudent(student)"
                            class="p-2 text-purple-600 hover:bg-purple-100 rounded-lg transition-colors duration-200 group"
                            title="Duplikat mahasiswa"
                          >
                            <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                          </button>

                          <!-- Create User Account Button -->
                          <button
                            v-if="!student.user_id && !student.user"
                            @click="createUserAccount(student)"
                            class="p-2 text-green-600 hover:bg-green-100 rounded-lg transition-colors duration-200 group"
                            title="Buat akun pengguna"
                          >
                            <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                          </button>

                          <!-- User Account Status Indicator -->
                          <div
                            v-if="student.user_id || student.user"
                            class="p-2 text-gray-400 rounded-lg"
                            :title="`Akun pengguna sudah ada: ${student.user?.email || student.email}`"
                          >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                          </div>

                          <!-- Delete Button -->
                          <button
                            @click="confirmDelete(student)"
                            class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition-colors duration-200 group"
                            title="Hapus mahasiswa"
                          >
                            <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                          </button>
                        </template>

                        <template v-else-if="activeTab === 'trash'">
                          <!-- Restore Button -->
                          <button
                            @click="restoreStudent(student.id)"
                            class="p-2 text-green-600 hover:bg-green-100 rounded-lg transition-colors duration-200 group"
                            title="Pulihkan mahasiswa"
                          >
                            <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                            </svg>
                          </button>

                          <!-- Force Delete Button -->
                          <button
                            @click="confirmForceDeleteStudent(student)"
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
      <StudentFormModal
        v-if="showAddModal || showEditModal"
        :show="showAddModal || showEditModal"
        :student="currentStudent"
        :is-edit="showEditModal"
        @close="closeModal"
        @saved="onStudentSaved"
      />

      <StudentDetailModal
        v-if="showDetailModal"
        :show="showDetailModal"
        :student="selectedStudentForDetail"
        @close="closeModal"
        @edit="editStudentFromDetail"
      />

      <StudentDuplicateModal
        v-if="selectedStudentForDuplicate"
        :show="!!selectedStudentForDuplicate"
        :student="selectedStudentForDuplicate"
        @close="closeDuplicateModal"
        @duplicated="onStudentDuplicated"
      />

      <StudentImportModal
        v-if="showImportModal"
        :show="showImportModal"
        @close="showImportModal = false"
        @import-success="onImported"
      />

      <ConfirmModal
        v-if="showDeleteConfirm"
        :show="showDeleteConfirm"
        title="Hapus Mahasiswa"
        :message="`Apakah Anda yakin ingin menghapus mahasiswa ${currentStudent?.name}?`"
        @confirm="deleteStudent"
        @cancel="showDeleteConfirm = false"
      />

      <ConfirmModal
        v-if="showBulkDeleteConfirm"
        :show="showBulkDeleteConfirm"
        title="Hapus Mahasiswa Terpilih"
        :message="`Apakah Anda yakin ingin menghapus ${selectedItems.length} mahasiswa yang dipilih?`"
        @confirm="bulkDelete"
        @cancel="showBulkDeleteConfirm = false"
      />

      <ConfirmModal
        v-if="showBulkRestoreConfirm"
        :show="showBulkRestoreConfirm"
        title="Pulihkan Mahasiswa Terpilih"
        :message="`Apakah Anda yakin ingin memulihkan ${selectedItems.length} mahasiswa yang dipilih?`"
        @confirm="bulkRestore"
        @cancel="showBulkRestoreConfirm = false"
      />

      <ConfirmModal
        v-if="showBulkForceDeleteConfirm"
        :show="showBulkForceDeleteConfirm"
        title="Hapus Permanen Mahasiswa Terpilih"
        :message="`Apakah Anda yakin ingin menghapus permanen ${selectedItems.length} mahasiswa yang dipilih? Tindakan ini tidak dapat dibatalkan.`"
        confirm-text="Hapus Permanen"
        cancel-text="Batal"
        @confirm="bulkForceDelete"
        @cancel="showBulkForceDeleteConfirm = false"
      />

      <ConfirmModal
        v-if="showForceDeleteConfirm"
        :show="showForceDeleteConfirm"
        title="Hapus Permanen Mahasiswa"
        :message="`Apakah Anda yakin ingin menghapus permanen mahasiswa ${forceDeleteTarget?.name}? Tindakan ini tidak dapat dibatalkan.`"
        confirm-text="Hapus Permanen"
        cancel-text="Batal"
        @confirm="forceDeleteStudent"
        @cancel="showForceDeleteConfirm = false"
      />
    </div>
  </Layout>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useToastStore } from '@/stores/toast'
import studentService from '@/services/studentService'
import programStudyService from '@/services/programStudyService'
import Layout from '@/components/Layout.vue'
import StudentFormModal from '@/components/modals/StudentFormModal.vue'
import StudentDetailModal from '@/components/modals/StudentDetailModal.vue'
import StudentDuplicateModal from '@/components/modals/StudentDuplicateModal.vue'
import StudentImportModal from '@/components/modals/StudentImportModal.vue'
import ImportModal from '@/components/modals/ImportModal.vue'
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
const students = ref([])
const studentStats = ref({})
const selectedItems = ref([])

// Filters
const searchQuery = ref('')
const selectedProgramStudy = ref('')
const selectedStatus = ref('')
const programStudies = ref([])

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
const showDeleteConfirm = ref(false)
const showForceDeleteConfirm = ref(false)
const forceDeleteTarget = ref(null)
const showBulkDeleteConfirm = ref(false)
const perPageOptions = [5, 10, 20, 50, 100]
const showBulkRestoreConfirm = ref(false)
const showBulkForceDeleteConfirm = ref(false)
const currentStudent = ref(null)
const selectedStudentForDetail = ref(null)
const selectedStudentForDuplicate = ref(null)

// Computed
const allItemsSelected = computed(() => {
  return students.value.length > 0 && selectedItems.value.length === students.value.length
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
// Function to reset all filters
const resetFilters = () => {
  searchQuery.value = ''
  selectedProgramStudy.value = ''
  selectedStatus.value = ''
  pagination.current_page = 1
  pagination.per_page = 20
}

const fetchStudents = async (showLoading = true) => {
  try {
    if (showLoading) {
      loading.value = true
    }
    selectedItems.value = []

    if (activeTab.value === 'trash') {
      await fetchTrashStudents()
      return
    }

    const params = {
      page: pagination.current_page,
      per_page: pagination.per_page
    }

    console.log('📋 Fetching students with params:', params)
    console.log('🔍 Filters cleared:', {
      search: searchQuery.value,
      programStudy: selectedProgramStudy.value,
      status: selectedStatus.value,
      currentPage: pagination.current_page,
      perPage: pagination.per_page
    })
    const response = await studentService.getAll(params)
    console.log('✅ Response received:', response)

    // Ensure we have proper data structure
    if (response && response.data) {
      // API returns nested structure: data.data contains students array
      const responseData = response.data;

      console.log('🔍 Parsing response data:', responseData);

      // Handle both possible response structures
      if (responseData.data && Array.isArray(responseData.data)) {
        // New API format: { data: { data: [...], total: ..., current_page: ... }, meta: {...} }
        students.value = responseData.data;
        pagination.current_page = responseData.meta?.current_page || responseData.current_page || 1;
        pagination.last_page = responseData.meta?.last_page || responseData.last_page || 1;
        pagination.per_page = responseData.meta?.per_page || responseData.per_page || 10;
        pagination.total = responseData.meta?.total || responseData.total || students.value.length;
        pagination.from = responseData.meta?.from || responseData.from || 1;
        pagination.to = responseData.meta?.to || responseData.to || students.value.length;
      } else if (Array.isArray(responseData)) {
        // Simple array format
        students.value = responseData;
        pagination.current_page = 1;
        pagination.last_page = 1;
        pagination.per_page = students.value.length;
        pagination.total = students.value.length;
        pagination.from = 1;
        pagination.to = students.value.length;
      } else {
        console.warn('Unexpected response structure:', responseData);
        students.value = [];
      }

      console.log('✅ Students data set:', students.value.length, 'items')
      console.log('✅ Pagination set:', pagination)

      // Force Vue reactivity update
      students.value = [...students.value];

    } else {
      console.warn('Invalid response structure:', response)
      students.value = []
    }
  } catch (error) {
    console.error('Error fetching students:', error)

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

    students.value = []
    toastStore.addToast({
      type: 'error',
      title: 'Error',
      message: error.response?.data?.message || 'Gagal memuat data mahasiswa'
    })
  } finally {
    if (showLoading) {
      loading.value = false
    }
    console.log('Loading completed, students count:', students.value.length)
  }
}

const fetchTrashStudents = async () => {
  try {
    const params = {
      page: pagination.current_page,
      per_page: pagination.per_page,
      search: searchQuery.value,
      program_study: selectedProgramStudy.value,
      only_trashed: true
    }

    console.log('Fetching trash students with params:', params)
    const response = await studentService.getTrash(params)
    console.log('Trash response received:', response)

    if (response && response.data) {
      const responseData = response.data

      console.log('🗑️ Parsing trash response data:', responseData);

      if (responseData.data && Array.isArray(responseData.data)) {
        students.value = responseData.data
        pagination.current_page = responseData.meta?.current_page || responseData.current_page || 1
        pagination.last_page = responseData.meta?.last_page || responseData.last_page || 1
        pagination.per_page = responseData.meta?.per_page || responseData.per_page || 10
        pagination.total = responseData.meta?.total || responseData.total || students.value.length
        pagination.from = responseData.meta?.from || responseData.from || 1
        pagination.to = responseData.meta?.to || responseData.to || students.value.length
      } else if (Array.isArray(responseData)) {
        students.value = responseData
        pagination.current_page = 1
        pagination.last_page = 1
        pagination.per_page = students.value.length
        pagination.total = students.value.length
        pagination.from = 1
        pagination.to = students.value.length
      } else {
        console.warn('Unexpected trash response structure:', responseData)
        students.value = []
      }

      console.log('✅ Trash students data set:', students.value.length, 'items')

      // Force Vue reactivity update
      students.value = [...students.value];

    } else {
      console.warn('Invalid trash response structure:', response)
      students.value = []
    }
  } catch (error) {
    console.error('Error fetching trash students:', error)
    students.value = []
    toastStore.addToast({
      type: 'error',
      title: 'Error',
      message: error.response?.data?.message || 'Gagal memuat data sampah'
    })
  }
}

const switchTab = (tab) => {
  if (activeTab.value !== tab) {
    activeTab.value = tab
    pagination.current_page = 1
    fetchStudents(true) // Show loading for tab switch
  }
}

const changePerPage = (value) => {
  pagination.per_page = parseInt(value)
  pagination.current_page = 1
  fetchStudents(false) // No loading animation for changing entries per page
}

const restoreStudent = async (id) => {
  try {
    const response = await studentService.restore(id)
    toastStore.success('Berhasil', 'Data mahasiswa berhasil dipulihkan')
    // Remove from local array for immediate UI update (item moved from trash to active)
    const index = students.value.findIndex(s => s.id === id)
    if (index > -1) {
      students.value.splice(index, 1)
    }
  } catch (error) {
    console.error('Error restoring student:', error)
    toastStore.error('Error', error.response?.data?.message || 'Gagal memulihkan data mahasiswa')
  }
}

const confirmForceDeleteStudent = (student) => {
  forceDeleteTarget.value = student
  showForceDeleteConfirm.value = true
}

const forceDeleteStudent = async () => {
  if (!forceDeleteTarget.value) return

  try {
    await studentService.forceDelete(forceDeleteTarget.value.id)
    toastStore.success('Berhasil', 'Data mahasiswa berhasil dihapus secara permanen')
    showForceDeleteConfirm.value = false
    forceDeleteTarget.value = null
    // Refresh table data after successful deletion (no loading animation)
    fetchStudents(false)
  } catch (error) {
    console.error('Error force deleting student:', error)
    toastStore.error('Error', error.response?.data?.message || 'Gagal menghapus data mahasiswa')
    showForceDeleteConfirm.value = false
    forceDeleteTarget.value = null
  }
}

const duplicateStudent = (student) => {
  console.log('duplicateStudent called with:', student)
  selectedStudentForDuplicate.value = student
  console.log('selectedStudentForDuplicate set to:', selectedStudentForDuplicate.value)
}

const createUserAccount = async (student) => {
  // Check if student already has user account
  if (student.user_id || student.user) {
    toastStore.warning('Perhatian', `Mahasiswa "${student.name}" sudah memiliki akun pengguna`)
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
    const response = await studentService.createUserAccount(student.id)

    // Remove loading toast
    toastStore.removeToast(loadingToast)

    if (response.success) {
      // Extract password from response message
      const message = response.message || ''
      const defaultPassword = message.includes('Default password:')
        ? message.split('Default password:')[1].trim()
        : 'Tidak diketahui'

      toastStore.success('Berhasil',
        `Akun pengguna untuk "${student.name}" berhasil dibuat. Default password: ${defaultPassword}`
      )

      // Refresh student data to show user account
      fetchStudents()
    } else {
      toastStore.error('Error', response.message || 'Gagal membuat akun pengguna')
    }
  } catch (error) {
    console.error('Error creating user account:', error)
    toastStore.handleError(error, 'createUserAccount')
  }
}

const bulkCreateUserAccounts = async () => {
  if (selectedItems.value.length === 0) {
    toastStore.warning('Perhatian', 'Pilih minimal satu mahasiswa untuk dibuatkan akun pengguna')
    return
  }

  // Filter out null values and extract student IDs
  const studentIds = selectedItems.value
    .filter(id => id !== null && id !== undefined && id !== '')
    .map(id => parseInt(id))
    .filter(id => !isNaN(id) && id > 0)

  // Check if we have any valid IDs after filtering
  if (studentIds.length === 0) {
    toastStore.warning('Perhatian', 'Tidak ada mahasiswa valid yang dipilih. Silakan pilih kembali.')
    return
  }

  try {
    const loadingToast = toastStore.addToast({
      type: 'info',
      title: 'Memproses',
      message: `Membuat akun pengguna untuk ${studentIds.length} mahasiswa...`,
      timeout: 0
    })

    // Call bulk API
    const response = await studentService.bulkCreateUserAccounts(studentIds)

    // Remove loading toast
    toastStore.removeToast(loadingToast)

    if (response.success) {
      const { success_count, error_count, results } = response.data

      // Show success message
      if (success_count > 0) {
        toastStore.success(
          'Berhasil',
          `${success_count} akun pengguna berhasil dibuat. Password default: nomor induk mahasiswa`
        )
      }

      // Show errors if any
      if (error_count > 0) {
        const errorResults = results.filter(r => !r.success)
        const errorMessages = errorResults.slice(0, 3).map(r => `${r.student_name}: ${r.message}`)
        const errorMessage = errorMessages.join('; ') + (error_count > 3 ? '...' : '')

        toastStore.error('Error', `${error_count} akun gagal dibuat: ${errorMessage}`)
      }

      // Log detailed results for debugging
      console.log('Bulk create user accounts results:', results)
    } else {
      toastStore.error('Error', response.message || 'Gagal membuat akun pengguna secara bulk')
    }

    // Clear selection and refresh data
    selectedItems.value = []
    fetchStudents()
  } catch (error) {
    console.error('Error in bulk create user accounts:', error)
    toastStore.handleError(error, 'bulkCreateUserAccounts')
  }
}

const fetchStats = async () => {
  try {
    const includeTrash = activeTab.value === 'trash'
    const response = await studentService.getStatistics(includeTrash)
    studentStats.value = response.data || {}
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
    console.warn('Failed to fetch student statistics')
  }
}

const fetchProgramStudies = async () => {
  try {
    const response = await programStudyService.getAll({
      is_active: true,
      per_page: 100 // Get all active program studies
    })

    if (response && response.data) {
      const responseData = response.data

      if (responseData.data && Array.isArray(responseData.data)) {
        programStudies.value = responseData.data
      } else if (Array.isArray(responseData)) {
        programStudies.value = responseData
      }

      console.log('Program studies loaded:', programStudies.value.length, 'items')
    }
  } catch (error) {
    console.error('Error fetching program studies:', error)
    // Fallback to default options if API fails
    programStudies.value = [
      { id: 1, name: 'Teknik Informatika' },
      { id: 2, name: 'Sistem Informasi' },
      { id: 3, name: 'Manajemen' },
      { id: 4, name: 'Akuntansi' }
    ]
    toastStore.addToast({
      type: 'error',
      title: 'Error',
      message: 'Gagal memuat data program studi'
    })
  }
}

const refreshData = () => {
  fetchStudents()
  fetchStats()
  fetchProgramStudies()
}

const toggleSelectAll = () => {
  if (allItemsSelected.value) {
    selectedItems.value = []
  } else {
    selectedItems.value = students.value.map(s => s.id)
  }
}

const editStudent = async (student) => {
  try {
    // Clear current student first to reset form
    currentStudent.value = null

    // Open modal first (will show empty form)
    showEditModal.value = true

    // Then fetch complete student data from backend
    const response = await studentService.getById(student.id)
    console.log('📋 EditStudent - API Response:', response.data)

    // Set student data (this should trigger the watcher in modal)
    currentStudent.value = response.data
    console.log('📋 EditStudent - CurrentStudent set:', currentStudent.value)

  } catch (error) {
    console.error('Error fetching student data:', error)
    toastStore.handleError(error, 'Ambil data mahasiswa')

    // Fallback to basic data if fetch fails
    currentStudent.value = { ...student }
    console.log('📋 EditStudent - Fallback data set:', currentStudent.value)
  }
}

const confirmDelete = (student) => {
  currentStudent.value = student
  showDeleteConfirm.value = true
}

const deleteStudent = async () => {
  try {
    // Use different methods based on active tab
    if (activeTab.value === 'trash') {
      // For trash tab, permanently delete
      await studentService.forceDelete(currentStudent.value.id)
      toastStore.success('Berhasil', 'Mahasiswa berhasil dihapus permanen')
    } else {
      // For active tab, soft delete
      await studentService.delete(currentStudent.value.id)
      toastStore.success('Berhasil', 'Mahasiswa berhasil dihapus')
    }

    showDeleteConfirm.value = false
    currentStudent.value = null

    // Refresh data from server to ensure consistency and update pagination info
    fetchStudents(false) // No loading animation for delete operations
  } catch (error) {
    console.error('Error deleting student:', error)
    toastStore.error('Error', 'Gagal menghapus mahasiswa')
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
    // Use different methods based on active tab
    if (activeTab.value === 'trash') {
      // For trash tab, permanently delete
      await studentService.bulkForceDelete({ ids: selectedItems.value })
      toastStore.success('Berhasil', `${selectedItems.value.length} mahasiswa berhasil dihapus permanen`)
    } else {
      // For active tab, soft delete
      await studentService.bulkDelete({ student_ids: selectedItems.value })
      toastStore.success('Berhasil', `${selectedItems.value.length} mahasiswa berhasil dihapus`)
    }

    showBulkDeleteConfirm.value = false
    selectedItems.value = []

    // Refresh data from server to ensure consistency and update pagination info
    fetchStudents(false) // No loading animation for bulk delete operations
  } catch (error) {
    console.error('Error bulk deleting students:', error)
    toastStore.error('Error', 'Gagal menghapus mahasiswa terpilih')
  }
}

const bulkToggleStatus = async () => {
  try {
    await studentService.bulkToggleStatus({ ids: selectedItems.value })
    toastStore.success('Berhasil', 'Status mahasiswa berhasil diperbarui')
    selectedItems.value = []
    // Remove fetchStudents() to prevent loading state on bulk operations
  } catch (error) {
    console.error('Error toggling status:', error)
    toastStore.error('Error', 'Gagal memperbarui status mahasiswa')
  }
}

const exportData = async () => {
  try {
    const blob = await studentService.export({
      search: searchQuery.value,
      program_study: selectedProgramStudy.value,
      status: selectedStatus.value
    })

    const url = window.URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = `students_${new Date().toISOString().split('T')[0]}.xlsx`
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

const bulkRestore = async () => {
  try {
    await studentService.bulkRestore({ ids: selectedItems.value })
    toastStore.success('Berhasil', `${selectedItems.value.length} mahasiswa berhasil dipulihkan`)

    showBulkRestoreConfirm.value = false
    selectedItems.value = []

    // Refresh data from server to ensure consistency and update pagination info
    fetchStudents(false) // No loading animation for bulk restore operations
  } catch (error) {
    console.error('Error bulk restoring students:', error)
    toastStore.error('Error', 'Gagal memulihkan mahasiswa terpilih')
  }
}

const bulkForceDelete = async () => {
  try {
    await studentService.bulkForceDelete({ ids: selectedItems.value })
    toastStore.success('Berhasil', `${selectedItems.value.length} mahasiswa berhasil dihapus permanen`)

    showBulkForceDeleteConfirm.value = false
    selectedItems.value = []

    // Refresh data from server to ensure consistency and update pagination info
    fetchStudents(false) // No loading animation for bulk force delete operations
  } catch (error) {
    console.error('Error bulk force deleting students:', error)
    toastStore.error('Error', 'Gagal menghapus permanen mahasiswa terpilih')
  }
}

const viewStudentDetail = (student) => {
  selectedStudentForDetail.value = student
  showDetailModal.value = true
}

const editStudentFromDetail = async (student) => {
  try {
    // Clear current student first to reset form
    currentStudent.value = null

    // Close detail modal and open edit modal
    showDetailModal.value = false
    showEditModal.value = true

    // Then fetch complete student data from backend
    const response = await studentService.getById(student.id)
    console.log('📋 EditStudentFromDetail - API Response:', response.data)

    // Set student data (this should trigger the watcher in modal)
    currentStudent.value = response.data
    console.log('📋 EditStudentFromDetail - CurrentStudent set:', currentStudent.value)

  } catch (error) {
    console.error('Error fetching student data:', error)
    toastStore.handleError(error, 'Ambil data mahasiswa')

    // Fallback to basic data if fetch fails
    currentStudent.value = { ...student }
    console.log('📋 EditStudentFromDetail - Fallback data set:', currentStudent.value)
    showEditModal.value = true
    showDetailModal.value = false
  }
}

const closeModal = () => {
  showAddModal.value = false
  showEditModal.value = false
  showDetailModal.value = false
  currentStudent.value = null
  selectedStudentForDetail.value = null
}

const closeDuplicateModal = () => {
  selectedStudentForDuplicate.value = null
}

const onStudentDuplicated = (duplicatedStudent) => {
  closeDuplicateModal()
  fetchStudents()
  fetchStats()
  toastStore.success('Berhasil', `Mahasiswa "${duplicatedStudent.name}" berhasil diduplikasi`)
}

const onStudentSaved = (eventData) => {
  // Handle both old format (direct student object) and new format (event object with isEditing)
  const isEditing = eventData?.isEditing !== undefined ? eventData.isEditing : showEditModal.value

  closeModal()

  // For new students, reset all filters to ensure data visibility
  if (!isEditing) {
    resetFilters()
    pagination.per_page = 100 // Override default for new data visibility
  }

  // Force fetch students with fresh data
  fetchStudents(true) // Show loading to ensure fresh data
  fetchStats()

  // Show success notification based on operation type
  const message = isEditing ? 'Mahasiswa berhasil diperbarui' : 'Mahasiswa berhasil ditambahkan'
  toastStore.success('Berhasil', message)
}

const onImported = () => {
  showImportModal.value = false
  fetchStudents()
  fetchStats()
  toastStore.success('Berhasil', 'Data berhasil diimpor')
}

// Pagination methods
const prevPage = () => {
  if (pagination.current_page > 1) {
    pagination.current_page--
    fetchStudents(false) // No loading animation for page navigation
  }
}

const nextPage = () => {
  if (pagination.current_page < pagination.last_page) {
    pagination.current_page++
    fetchStudents(false) // No loading animation for page navigation
  }
}

const goToPage = (page) => {
  if (page !== '...' && page !== pagination.current_page) {
    pagination.current_page = page
    fetchStudents(false) // No loading animation for page navigation
  }
}

// Watchers
watch([searchQuery, selectedProgramStudy, selectedStatus, activeTab], () => {
  pagination.current_page = 1
  fetchStudents(true) // Show loading for search/filter operations
}, { debounce: 150 }) // Reduced debounce from 300 to 150ms

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
  fetchProgramStudies() // Fetch program studies first
  fetchStudents()
  fetchStats()
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