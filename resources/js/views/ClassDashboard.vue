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
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Kelas</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ stats.total_classes || 0 }}</dd>
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
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Mahasiswa</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ stats.total_students || 0 }}</dd>
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
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Kapasitas</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ stats.total_capacity || 0 }}</dd>
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
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Utilisasi</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ stats.utilization_rate || 0 }}%</dd>
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
                  <path v-if="menuItem.icon === 'class'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253M12 10.5h.01M8 10.5h.01m8 0h.01" />
                  <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
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
                    {{ activeMenu === 'classes' ? 'Data Kelas' : 'Mahasiswa Per Kelas' }}
                  </h3>
                  <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">
                    {{ activeMenu === 'classes' ? filteredClasses.length : totalStudents }} data
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

                  <template v-if="activeMenu === 'classes'">
                    <button
                      @click="openAddModal"
                      class="flex items-center justify-center px-3 py-2 text-sm bg-blue-600 border border-transparent rounded-md text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                      </svg>
                      <span class="ml-2">Tambah Kelas</span>
                    </button>

                    <button
                      @click="autoGenerateClasses"
                      class="flex items-center justify-center px-3 py-2 text-sm bg-green-600 border border-transparent rounded-md text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                      </svg>
                      <span class="ml-2">Auto Generate</span>
                    </button>

                    <button
                      @click="autoEnrollStudents"
                      class="flex items-center justify-center px-3 py-2 text-sm bg-purple-600 border border-transparent rounded-md text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                      </svg>
                      <span class="ml-2">Auto Enroll</span>
                    </button>
                  </template>
                </div>
              </div>
            </div>

            <!-- Tabs -->
            <div class="border-b border-gray-200">
              <nav class="flex -mb-px">
                <button
                  @click="activeTab = 'active'"
                  :class="[
                    'py-2 px-4 text-sm font-medium border-b-2 transition-colors',
                    activeTab === 'active'
                      ? 'border-blue-500 text-blue-600'
                      : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                  ]"
                >
                  Aktif
                  <span class="ml-2 px-2 py-0.5 text-xs bg-blue-100 text-blue-600 rounded-full">
                    {{ activeMenu === 'classes' ? stats.active_classes : stats.active_enrollments || 0 }}
                  </span>
                </button>
                <button
                  @click="activeTab = 'trash'"
                  :class="[
                    'py-2 px-4 text-sm font-medium border-b-2 transition-colors',
                    activeTab === 'trash'
                      ? 'border-blue-500 text-blue-600'
                      : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                  ]"
                >
                  Trash
                  <span class="ml-2 px-2 py-0.5 text-xs bg-red-100 text-red-600 rounded-full">
                    {{ stats.trashed_classes || 0 }}
                  </span>
                </button>
              </nav>
            </div>

            <!-- Search and Filters -->
            <div class="p-4 border-b border-gray-200 bg-gray-50">
              <!-- Bulk Actions on Top -->
              <div v-if="selectedItems.length > 0" class="mb-4">
                <div class="flex items-center justify-between">
                  <span class="px-3 py-1.5 text-sm bg-blue-100 text-blue-800 rounded-md">
                    {{ selectedItems.length }} dipilih
                  </span>
                  <div class="relative inline-block text-left">
                    <button
                      @click="showBulkActions = !showBulkActions"
                      class="px-3 py-1.5 text-sm bg-gray-600 border border-transparent rounded-md text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                    >
                      Aksi Bulk
                      <svg class="w-4 h-4 ml-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                      </svg>
                    </button>
                    <div v-if="showBulkActions" class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                      <div class="py-1">
                        <template v-if="activeTab === 'active'">
                          <button
                            @click="bulkActivate"
                            class="flex items-center px-4 py-2 text-sm text-green-700 hover:bg-green-50 w-full text-left"
                          >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Aktifkan
                          </button>
                          <button
                            @click="bulkDeactivate"
                            class="flex items-center px-4 py-2 text-sm text-yellow-700 hover:bg-yellow-50 w-full text-left"
                          >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L5.636 5.636" />
                            </svg>
                            Nonaktifkan
                          </button>
                          <button
                            @click="bulkDelete"
                            class="flex items-center px-4 py-2 text-sm text-red-700 hover:bg-red-50 w-full text-left"
                          >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Hapus
                          </button>
                        </template>
                        <template v-else>
                          <button
                            @click="bulkRestore"
                            class="flex items-center px-4 py-2 text-sm text-green-700 hover:bg-green-50 w-full text-left"
                          >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                            </svg>
                            Pulihkan
                          </button>
                          <button
                            @click="bulkDeletePermanent"
                            class="flex items-center px-4 py-2 text-sm text-red-700 hover:bg-red-50 w-full text-left"
                          >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Hapus Permanen
                          </button>
                        </template>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Search and Filters -->
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

                <!-- Sort -->
                <select
                  v-model="sortBy"
                  class="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm"
                >
                  <option value="name">Urut: Nama</option>
                  <option value="code">Urut: Kode</option>
                  <option value="program_study">Urut: Program Studi</option>
                  <option value="batch_year">Urut: Angkatan</option>
                  <option value="capacity">Urut: Kapasitas</option>
                </select>

                <select
                  v-model="sortOrder"
                  class="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm"
                >
                  <option value="asc">A-Z</option>
                  <option value="desc">Z-A</option>
                </select>

                <!-- Filters -->
                <select
                  v-model="filterProgramStudy"
                  class="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm"
                >
                  <option value="">Semua Program Studi</option>
                  <option v-for="ps in programStudies" :key="ps.id" :value="ps.id">
                    {{ ps.name }}
                  </option>
                </select>

                <select
                  v-model="filterBatchYear"
                  class="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm"
                >
                  <option value="">Semua Angkatan</option>
                  <option v-for="year in batchYears" :key="year" :value="year">
                    {{ year }}
                  </option>
                </select>

                <select
                  v-model="filterSemester"
                  class="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm"
                >
                  <option value="">Semua Semester</option>
                  <option value="ganjil">Ganjil</option>
                  <option value="genap">Genap</option>
                </select>
              </div>
            </div>

            <!-- Table -->
            <div class="flex-1 overflow-auto">
              <!-- Active Classes Table -->
              <table v-if="activeTab === 'active'" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left">
                      <input
                        type="checkbox"
                        :checked="selectedItems.length === currentItems.length && currentItems.length > 0"
                        :indeterminate="selectedItems.length > 0 && selectedItems.length < currentItems.length"
                        @change="toggleSelectAll"
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                      >
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Informasi Kelas
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Program Studi
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Angkatan
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Semester
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
                    <td :colspan="activeMenu === 'classes' ? 8 : 6" class="px-6 py-12 text-center">
                      <div class="flex justify-center">
                        <svg class="animate-spin h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                      </div>
                    </td>
                  </tr>
                  <tr v-else-if="filteredClasses.length === 0">
                    <td :colspan="activeMenu === 'classes' ? 8 : 6" class="px-6 py-12 text-center text-gray-500">
                      Tidak ada data kelas
                    </td>
                  </tr>
                  <tr v-else v-for="kelas in paginatedClasses" :key="kelas.id" class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <input
                        type="checkbox"
                        :value="kelas.id"
                        v-model="selectedItems"
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                      >
                    </td>
                    <td class="px-6 py-4">
                      <div class="text-sm font-medium text-gray-900">{{ kelas.name }}</div>
                      <div class="text-sm text-gray-500">{{ kelas.code }}</div>
                    </td>
                    <td class="px-6 py-4">
                      <div class="text-sm text-gray-900">{{ kelas.program_study?.name || '-' }}</div>
                    </td>
                    <td class="px-6 py-4">
                      <div class="text-sm text-gray-900">{{ kelas.batch_year || '-' }}</div>
                    </td>
                    <td class="px-6 py-4">
                      <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                        :class="kelas.semester === 'ganjil' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'">
                        {{ kelas.semester || '-' }}
                      </span>
                    </td>
                    <td class="px-6 py-4">
                      <div class="text-sm text-gray-900">
                        {{ kelas.current_students || 0 }} / {{ kelas.capacity || 0 }}
                      </div>
                      <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                        <div class="bg-blue-600 h-2 rounded-full"
                          :style="{ width: Math.min(((kelas.current_students || 0) / (kelas.capacity || 1)) * 100, 100) + '%' }">
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4">
                      <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                        :class="kelas.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                        {{ kelas.is_active ? 'Aktif' : 'Nonaktif' }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                      <button
                        @click="viewClassStudents(kelas)"
                        class="text-blue-600 hover:text-blue-900 mr-3"
                        title="Lihat Mahasiswa"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                      </button>
                      <button
                        @click="openStudentEnrollModal(kelas)"
                        class="text-green-600 hover:text-green-900 mr-3"
                        title="Enroll Mahasiswa"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                      </button>
                      <button
                        @click="openEditModal(kelas)"
                        class="text-indigo-600 hover:text-indigo-900 mr-3"
                        title="Edit"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                      </button>
                      <button
                        @click="deleteClass(kelas)"
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

              <!-- Trash Classes Table -->
              <table v-else-if="activeTab === 'trash'" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left">
                      <input
                        type="checkbox"
                        :checked="selectedItems.length === trashedClasses.length && trashedClasses.length > 0"
                        :indeterminate="selectedItems.length > 0 && selectedItems.length < trashedClasses.length"
                        @change="toggleSelectAll"
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                      >
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Informasi Kelas
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Program Studi
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Angkatan
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Semester
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Dihapus Pada
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Aksi
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-if="trashLoading">
                    <td colspan="7" class="px-6 py-12 text-center">
                      <div class="flex justify-center">
                        <svg class="animate-spin h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                      </div>
                    </td>
                  </tr>
                  <tr v-else-if="trashedClasses.length === 0">
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                      Tidak ada data kelas yang dihapus
                    </td>
                  </tr>
                  <tr v-else v-for="kelas in trashedClasses" :key="kelas.id" class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <input
                        type="checkbox"
                        :value="kelas.id"
                        v-model="selectedItems"
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                      >
                    </td>
                    <td class="px-6 py-4">
                      <div class="text-sm font-medium text-gray-900">{{ kelas.name }}</div>
                      <div class="text-sm text-gray-500">{{ kelas.code }}</div>
                    </td>
                    <td class="px-6 py-4">
                      <div class="text-sm text-gray-900">{{ kelas.program_study?.name || '-' }}</div>
                    </td>
                    <td class="px-6 py-4">
                      <div class="text-sm text-gray-900">{{ kelas.batch_year || '-' }}</div>
                    </td>
                    <td class="px-6 py-4">
                      <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                        :class="kelas.semester === 'ganjil' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'">
                        {{ kelas.semester || '-' }}
                      </span>
                    </td>
                    <td class="px-6 py-4">
                      <div class="text-sm text-gray-900">
                        {{ formatDate(kelas.deleted_at) }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                      <button
                        @click="restoreClass(kelas)"
                        class="text-green-600 hover:text-green-900 mr-3"
                        title="Pulihkan"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                        </svg>
                      </button>
                      <button
                        @click="forceDeleteClass(kelas)"
                        class="text-red-600 hover:text-red-900"
                        title="Hapus Permanen"
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

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
              <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                  Menampilkan {{ startIndex + 1 }} sampai {{ endIndex }} dari {{ (activeTab === 'trash' ? trashedClasses.length : filteredClasses.length) }} hasil
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

    <!-- Class Form Modal -->
    <ClassFormModal
      v-if="showModal"
      :show="showModal"
      :kelas="editingClass"
      @close="closeModal"
      @success="(isEdit) => handleFormSuccess(isEdit)"
    />

    <!-- Delete Confirmation Dialog -->
    <ConfirmDialogModal
      v-if="showDeleteConfirm"
      :show="showDeleteConfirm"
      :loading="deleteLoading"
      title="Hapus Kelas"
      :message="`Apakah Anda yakin ingin menghapus kelas ${classToDelete?.name || ''}?`"
      :details="`Kode: ${classToDelete?.class_code || ''} • ${classToDelete?.study_program?.name || ''} • ${classToDelete?.academic_year || ''}`"
      @close="closeDeleteConfirm"
      @confirm="confirmDelete"
    />

    <!-- Bulk Delete Confirmation Dialog -->
    <BulkDeleteDialogModal
      v-if="showBulkDeleteConfirm"
      :show="showBulkDeleteConfirm"
      :loading="bulkDeleteLoading"
      :item-count="selectedItems.length"
      :item-list="selectedItems.map(id => {
        const item = classes.find(c => c.id === id) || trashedClasses.find(c => c.id === id);
        return item || { id, name: `Kelas ${id}` };
      })"
      :has-active-students="false"
      @close="closeBulkDeleteConfirm"
      @confirm="confirmBulkDelete"
    />

    <!-- Hard Delete Confirmation Dialog -->
    <HardDeleteDialogModal
      v-if="showHardDeleteConfirm"
      :show="showHardDeleteConfirm"
      :loading="hardDeleteLoading"
      :item-type="itemToHardDelete?.type || 'Kelas'"
      :item-name="itemToHardDelete?.item?.name || (itemToHardDelete?.isBulk ? `${itemToHardDelete?.count} kelas` : '')"
      :item-code="itemToHardDelete?.item?.code || ''"
      :has-related-data="true"
      :require-confirmation="itemToHardDelete?.isBulk || false"
      :confirmation-text="itemToHardDelete?.isBulk ? 'HAPUS PERMANEN' : 'HAPUS PERMANEN'"
      @close="closeHardDeleteConfirm"
      @confirm="confirmHardDelete"
    />

    <!-- Student Enrollment Modal -->
    <StudentEnrollModal
      v-if="showStudentEnrollModal"
      :show="showStudentEnrollModal"
      :selected-class="selectedClassForEnrollment"
      :loading="enrollmentLoading"
      @close="closeStudentEnrollModal"
      @enroll="handleStudentEnrollment"
    />

    <!-- View Students Modal -->
    <ViewStudentsModal
      v-if="showViewStudentsModal"
      :show="showViewStudentsModal"
      :selected-class="selectedClassForView"
      @close="closeViewStudentsModal"
      @open-enroll-modal="handleOpenEnrollModalFromView"
      @refresh="handleRefreshFromViewModal"
    />
  </Layout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import Layout from '@/components/Layout.vue';
import Toast from '@/components/Toast.vue';
import ClassFormModal from '@/components/modals/ClassFormModal.vue';
import ConfirmDialogModal from '@/components/modals/ConfirmDialogModal.vue';
import BulkDeleteDialogModal from '@/components/modals/BulkDeleteDialogModal.vue';
import HardDeleteDialogModal from '@/components/modals/HardDeleteDialogModal.vue';
import StudentEnrollModal from '@/components/modals/StudentEnrollModal.vue';
import ViewStudentsModal from '@/components/modals/ViewStudentsModal.vue';
import classService from '@/services/classService';
import studentService from '@/services/studentService';

// State management
const loading = ref(false);
const classes = ref([]);
const stats = ref({});
const programStudies = ref([]);
const batchYears = ref([]);
const sidebarCollapsed = ref(false);

// Modal state
const showModal = ref(false);
const editingClass = ref(null);

// Delete confirmation state
const showDeleteConfirm = ref(false);
const deleteLoading = ref(false);
const classToDelete = ref(null);

// Bulk Delete Dialog State
const showBulkDeleteConfirm = ref(false);
const bulkDeleteLoading = ref(false);

// Hard Delete Dialog State
const showHardDeleteConfirm = ref(false);
const hardDeleteLoading = ref(false);
const itemToHardDelete = ref(null);

// Trash state
const trashedClasses = ref([]);
const trashLoading = ref(false);

// Menu and tabs
const activeMenu = ref('classes');
const activeTab = ref('active');

// Class selection for student view
const selectedClass = ref(null);

// Student Enrollment Modal State
const showStudentEnrollModal = ref(false);
const selectedClassForEnrollment = ref(null);
const enrollmentLoading = ref(false);

// View Students Modal State
const showViewStudentsModal = ref(false);
const selectedClassForView = ref(null);
const menuItems = [
  { key: 'classes', name: 'Data Kelas', icon: 'class' },
  { key: 'students', name: 'Mahasiswa Per Kelas', icon: 'users' },
];

// Search and filters
const searchQuery = ref('');
const sortBy = ref('name');
const sortOrder = ref('asc');
const filterProgramStudy = ref('');
const filterBatchYear = ref('');
const filterSemester = ref('');

// Pagination
const currentPage = ref(1);
const itemsPerPage = ref(10);

// Bulk actions
const selectedItems = ref([]);
const showBulkActions = ref(false);

// Toast
const toast = ref({
  show: false,
  title: '',
  description: '',
  type: 'success',
  duration: 3000
});

// Computed properties
const filteredClasses = computed(() => {
  // Ensure classes.value is an array
  let filtered = Array.isArray(classes.value) ? classes.value : [];

  // Filter by tab
  if (activeTab.value === 'active') {
    filtered = filtered.filter(item => !item.deleted_at);
  } else {
    filtered = filtered.filter(item => item.deleted_at);
  }

  // Search
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(item =>
      item.name?.toLowerCase().includes(query) ||
      item.code?.toLowerCase().includes(query) ||
      item.program_study?.name?.toLowerCase().includes(query)
    );
  }

  // Filters
  if (filterProgramStudy.value) {
    filtered = filtered.filter(item => item.program_study_id == filterProgramStudy.value);
  }

  if (filterBatchYear.value) {
    filtered = filtered.filter(item => item.batch_year == filterBatchYear.value);
  }

  if (filterSemester.value) {
    filtered = filtered.filter(item => item.semester === filterSemester.value);
  }

  // Sort
  filtered.sort((a, b) => {
    let aValue, bValue;

    switch (sortBy.value) {
      case 'name':
        aValue = a.name || '';
        bValue = b.name || '';
        break;
      case 'code':
        aValue = a.code || '';
        bValue = b.code || '';
        break;
      case 'program_study':
        aValue = a.program_study?.name || '';
        bValue = b.program_study?.name || '';
        break;
      case 'batch_year':
        aValue = a.batch_year || '';
        bValue = b.batch_year || '';
        break;
      case 'capacity':
        aValue = a.capacity || 0;
        bValue = b.capacity || 0;
        break;
      default:
        aValue = a.name || '';
        bValue = b.name || '';
    }

    if (typeof aValue === 'string') {
      return sortOrder.value === 'asc'
        ? aValue.localeCompare(bValue)
        : bValue.localeCompare(aValue);
    } else {
      return sortOrder.value === 'asc' ? aValue - bValue : bValue - aValue;
    }
  });

  return filtered;
});

const currentItems = computed(() => {
  return activeTab.value === 'trash' ? trashedClasses.value : filteredClasses.value;
});

const paginatedClasses = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  const end = start + itemsPerPage.value;
  const items = activeTab.value === 'trash' ? trashedClasses.value : filteredClasses.value;
  return items.slice(start, end);
});

const totalPages = computed(() => {
  const items = activeTab.value === 'trash' ? trashedClasses.value : filteredClasses.value;
  return Math.ceil(items.length / itemsPerPage.value);
});

const startIndex = computed(() => {
  return (currentPage.value - 1) * itemsPerPage.value;
});

const endIndex = computed(() => {
  const items = activeTab.value === 'trash' ? trashedClasses.value : filteredClasses.value;
  return Math.min(startIndex.value + itemsPerPage.value, items.length);
});

const totalStudents = computed(() => {
  return filteredClasses.value.reduce((total, item) => total + (item.current_students || 0), 0);
});

// Helper functions
const formatDate = (dateString) => {
  if (!dateString) return '-';
  const date = new Date(dateString);
  return date.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

// Methods
const loadClasses = async () => {
  loading.value = true;
  try {
    const response = await classService.getAll();
    console.log('Classes response:', response);

    // Handle different response structures
    if (response.data && Array.isArray(response.data.data)) {
      classes.value = response.data.data;
    } else if (response.data && Array.isArray(response.data)) {
      classes.value = response.data;
    } else if (Array.isArray(response)) {
      classes.value = response;
    } else {
      classes.value = response.data?.data || response.data || [];
    }

    console.log('Classes loaded:', classes.value);
  } catch (error) {
    console.error('Error loading classes:', error);
    showToast('Error', 'Gagal memuat data kelas', 'error');
  } finally {
    loading.value = false;
  }
};

const loadTrashedClasses = async () => {
  trashLoading.value = true;
  try {
    const response = await classService.getTrashed();
    console.log('Trashed classes response:', response);

    // Handle different response structures
    if (response.data && Array.isArray(response.data.data)) {
      trashedClasses.value = response.data.data;
    } else if (response.data && Array.isArray(response.data)) {
      trashedClasses.value = response.data;
    } else if (Array.isArray(response)) {
      trashedClasses.value = response;
    } else {
      trashedClasses.value = response.data?.data || response.data || [];
    }

    console.log('Trashed classes loaded:', trashedClasses.value);
  } catch (error) {
    console.error('Error loading trashed classes:', error);
    showToast('Error', 'Gagal memuat data kelas yang dihapus', 'error');
  } finally {
    trashLoading.value = false;
  }
};

const loadStatistics = async () => {
  try {
    const response = await classService.getStatistics();
    stats.value = response.data || response;
  } catch (error) {
    console.error('Error loading statistics:', error);
  }
};

const loadProgramStudies = async () => {
  try {
    const response = await fetch('/api/program-studies', {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    });
    const data = await response.json();
    programStudies.value = data.data || data;
  } catch (error) {
    console.error('Error loading program studies:', error);
  }
};

const loadBatchYears = async () => {
  try {
    // Generate batch years from current data or create default range
    const currentYear = new Date().getFullYear();
    const years = [];
    for (let year = currentYear - 5; year <= currentYear + 2; year++) {
      years.push({ value: year, label: `Angkatan ${year}` });
    }
    batchYears.value = years;
  } catch (error) {
    console.error('Error loading batch years:', error);
  }
};

const refreshData = async () => {
  await Promise.all([
    loadClasses(),
    loadTrashedClasses(),
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

const toggleSelectAll = () => {
  if (selectedItems.value.length === currentItems.value.length) {
    selectedItems.value = [];
  } else {
    selectedItems.value = currentItems.value.map(item => item.id);
  }
};



const deleteClass = async (kelas) => {
  classToDelete.value = kelas;
  showDeleteConfirm.value = true;
};

const viewClassStudents = (kelas) => {
  selectedClassForView.value = kelas;
  showViewStudentsModal.value = true;
};

const autoGenerateClasses = () => {
  // TODO: Implement auto generate modal
  showToast('Info', 'Fitur auto generate akan segera hadir', 'info');
};

const autoEnrollStudents = () => {
  // TODO: Implement auto enroll modal
  showToast('Info', 'Fitur auto enroll akan segera hadir', 'info');
};

// Student Enrollment Functions
const openStudentEnrollModal = (kelas) => {
  console.log('=== openStudentEnrollModal called ===')
  console.log('Class data:', kelas)
  selectedClassForEnrollment.value = kelas;
  console.log('selectedClassForEnrollment.value:', selectedClassForEnrollment.value)
  showStudentEnrollModal.value = true;
  console.log('showStudentEnrollModal.value set to:', showStudentEnrollModal.value)
  console.log('=== openStudentEnrollModal completed ===')
};

const closeStudentEnrollModal = () => {
  showStudentEnrollModal.value = false;
  selectedClassForEnrollment.value = null;
};

const closeViewStudentsModal = () => {
  showViewStudentsModal.value = false;
  selectedClassForView.value = null;
};

const handleOpenEnrollModalFromView = () => {
  // Preserve the class data before clearing the modal
  const classData = selectedClassForView.value;
  closeViewStudentsModal();
  openStudentEnrollModal(classData);
};

const handleRefreshFromViewModal = async () => {
  try {
    await loadClasses(); // Refresh classes data to update capacity
    await loadStatistics(); // Refresh statistics
  } catch (error) {
    console.error('Error refreshing data:', error);
  }
};

const handleStudentEnrollment = async (enrollmentData) => {
  enrollmentLoading.value = true;

  try {
    // Safety check to ensure selectedClassForEnrollment exists
    if (!selectedClassForEnrollment.value?.id) {
      showToast('Error', 'Kelas tidak dipilih atau data kelas tidak valid', 'error');
      return;
    }

    const result = await classService.enrollStudents(
      selectedClassForEnrollment.value.id,
      {
        student_ids: enrollmentData.studentIds,
        enrollment_date: enrollmentData.enrollmentDate,
        notes: enrollmentData.notes
      }
    );

    showToast(
      'Sukses',
      result.message,
      'success'
    );

    // Update class data with the response from backend
    if (result.data?.updated_class) {
      // Find and update the class in the classes array
      const classIndex = classes.value.findIndex(c => c.id === result.data.updated_class.id);
      if (classIndex !== -1) {
        classes.value[classIndex] = {
          ...classes.value[classIndex],
          current_students: result.data.updated_class.current_students
        };
      }

      // Update selected class if it's the same class
      if (selectedClassForEnrollment.value?.id === result.data.updated_class.id) {
        selectedClassForEnrollment.value = {
          ...selectedClassForEnrollment.value,
          current_students: result.data.updated_class.current_students
        };
      }
    }

    closeStudentEnrollModal();

    // Ensure body scroll is restored after modal closes
    document.body.style.overflow = '';
    document.documentElement.style.overflow = '';

    // Force reflow and scroll restoration
    setTimeout(() => {
      window.scrollTo({
        top: 0,
        left: 0,
        behavior: 'smooth'
      });
    }, 100);

    await loadClasses(); // Refresh classes data
    await loadStatistics(); // Refresh statistics

  } catch (error) {
    console.error('Enrollment error:', error);
    showToast(
      'Error',
      error.response?.data?.message || 'Gagal mengenroll mahasiswa',
      'error'
    );
  } finally {
    enrollmentLoading.value = false;
  }
};

const bulkActivate = async () => {
  try {
    await classService.bulkUpdate({
      class_ids: selectedItems.value,
      is_active: true
    });
    showToast('Success', 'Kelas berhasil diaktifkan', 'success');
    selectedItems.value = [];
    await refreshData();
  } catch (error) {
    console.error('Error bulk activating:', error);
    showToast('Error', error.userMessage || 'Gagal mengaktifkan kelas', 'error');
  }
};

const bulkDeactivate = async () => {
  try {
    await classService.bulkUpdate({
      class_ids: selectedItems.value,
      is_active: false
    });
    showToast('Success', 'Kelas berhasil dinonaktifkan', 'success');
    selectedItems.value = [];
    await refreshData();
  } catch (error) {
    console.error('Error bulk deactivating:', error);
    showToast('Error', error.userMessage || 'Gagal menonaktifkan kelas', 'error');
  }
};

const bulkDelete = () => {
  if (selectedItems.value.length === 0) {
    showToast('Error', 'Pilih minimal satu kelas untuk dihapus', 'error');
    return;
  }
  showBulkDeleteConfirm.value = true;
};

// Bulk Delete Dialog Handlers
const confirmBulkDelete = async () => {
  bulkDeleteLoading.value = true;
  try {
    await classService.bulkDelete({
      class_ids: selectedItems.value
    });
    showToast('Success', 'Kelas berhasil dihapus', 'success');
    selectedItems.value = [];
    await refreshData();
    showBulkDeleteConfirm.value = false;
  } catch (error) {
    console.error('Error bulk deleting:', error);
    showToast('Error', error.userMessage || 'Gagal menghapus kelas', 'error');
  } finally {
    bulkDeleteLoading.value = false;
  }
};

const closeBulkDeleteConfirm = () => {
  showBulkDeleteConfirm.value = false;
};

const restoreClass = async (kelas) => {
  try {
    await classService.restore(kelas.id);
    showToast('success', 'Kelas berhasil dipulihkan!', `Kelas ${kelas.name} telah berhasil dipulihkan.`);
    await Promise.all([
      loadClasses(),
      loadTrashedClasses(),
      loadStatistics()
    ]);
  } catch (error) {
    console.error('Error restoring class:', error);
    showToast('error', 'Gagal memulihkan kelas', error.userMessage || 'Terjadi kesalahan saat memulihkan kelas.');
  }
};

const forceDeleteClass = async (kelas) => {
  try {
    await classService.forceDelete(kelas.id);
    showToast('success', 'Kelas berhasil dihapus permanen!', `Kelas ${kelas.name} telah dihapus permanen dari sistem.`);
    await Promise.all([
      loadTrashedClasses(),
      loadStatistics()
    ]);
  } catch (error) {
    console.error('Error force deleting class:', error);
    showToast('error', 'Gagal menghapus permanen kelas', error.userMessage || 'Terjadi kesalahan saat menghapus permanen kelas.');
  }
};

const bulkRestore = async () => {
  try {
    await classService.bulkRestore({
      class_ids: selectedItems.value
    });
    showToast('success', 'Kelas berhasil dipulihkan!', `${selectedItems.value.length} kelas telah berhasil dipulihkan.`);
    selectedItems.value = [];
    await Promise.all([
      loadClasses(),
      loadTrashedClasses(),
      loadStatistics()
    ]);
  } catch (error) {
    console.error('Error bulk restoring:', error);
    showToast('error', 'Gagal memulihkan kelas', error.userMessage || 'Terjadi kesalahan saat memulihkan kelas.');
  }
};

const bulkDeletePermanent = () => {
  if (selectedItems.value.length === 0) {
    showToast('Error', 'Pilih minimal satu kelas untuk dihapus permanen', 'error');
    return;
  }
  itemToHardDelete.value = {
    type: 'Kelas',
    count: selectedItems.value.length,
    isBulk: true
  };
  showHardDeleteConfirm.value = true;
};

// Hard Delete Dialog Handlers
const confirmHardDelete = async () => {
  hardDeleteLoading.value = true;
  try {
    if (itemToHardDelete.value.isBulk) {
      // Bulk hard delete
      await classService.bulkForceDelete({
        class_ids: selectedItems.value
      });
      showToast('success', 'Kelas berhasil dihapus permanen!', `${selectedItems.value.length} kelas telah dihapus permanen dari sistem.`);
      selectedItems.value = [];
      await Promise.all([
        loadTrashedClasses(),
        loadStatistics()
      ]);
    } else {
      // Single hard delete
      await classService.forceDelete(itemToHardDelete.value.item.id);
      showToast('success', 'Kelas berhasil dihapus permanen!', `${itemToHardDelete.value.item.name} telah dihapus permanen dari sistem.`);
      await Promise.all([
        loadTrashedClasses(),
        loadStatistics()
      ]);
    }
    showHardDeleteConfirm.value = false;
    itemToHardDelete.value = null;
  } catch (error) {
    console.error('Error hard deleting:', error);
    showToast('error', 'Gagal menghapus permanen kelas', error.userMessage || 'Terjadi kesalahan saat menghapus permanen kelas.');
  } finally {
    hardDeleteLoading.value = false;
  }
};

const closeHardDeleteConfirm = () => {
  showHardDeleteConfirm.value = false;
  itemToHardDelete.value = null;
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

// Modal functions
const openAddModal = () => {
  editingClass.value = null;
  showModal.value = true;
};

const openEditModal = (kelas) => {
  editingClass.value = kelas;
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  editingClass.value = null;
};

const handleFormSuccess = async (isEdit = false) => {
  closeModal();
  await loadClasses();
  await loadStatistics();

  if (isEdit) {
    showToast('success', 'Kelas berhasil diperbarui!', 'Data kelas telah berhasil diperbarui.');
  } else {
    showToast('success', 'Kelas berhasil ditambahkan!', 'Kelas baru telah berhasil ditambahkan.');
  }
};

// Lifecycle
onMounted(async () => {
  // Load sidebar state from localStorage
  const saved = localStorage.getItem('sidebar-collapsed');
  if (saved !== null) {
    sidebarCollapsed.value = saved === 'true';
  }

  // Listen for sidebar collapse events
  window.addEventListener('sidebar-collapsed', (event) => {
    sidebarCollapsed.value = event.detail.collapsed;
  });

  await Promise.all([
    loadClasses(),
    loadStatistics(),
    loadProgramStudies(),
    loadBatchYears()
  ]);
});

// Watch for tab changes to reset pagination and load trashed classes
watch(activeTab, (newTab) => {
  currentPage.value = 1;
  selectedItems.value = [];

  if (newTab === 'trash') {
    loadTrashedClasses();
  }
});

// Delete confirmation dialog handlers
const closeDeleteConfirm = () => {
  showDeleteConfirm.value = false;
  classToDelete.value = null;
  deleteLoading.value = false;
};

const confirmDelete = async () => {
  if (!classToDelete.value) return;

  deleteLoading.value = true;

  try {
    await classService.delete(classToDelete.value.id);
    showToast('success', 'Kelas berhasil dihapus!', `Kelas ${classToDelete.value.name} telah berhasil dihapus.`);
    await refreshData();
    closeDeleteConfirm();
  } catch (error) {
    console.error('Error deleting class:', error);
    showToast('error', 'Gagal menghapus kelas', error.userMessage || 'Terjadi kesalahan saat menghapus kelas.');
    deleteLoading.value = false;
  }
};
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