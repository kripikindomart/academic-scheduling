<template>
  <Layout>
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
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Gedung</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ stats.total_buildings || 0 }}</dd>
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
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2M8 7a2 2 0 002-2h.5" />
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Ruangan</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ stats.total_rooms || 0 }}</dd>
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
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
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
      <div class="flex flex-col lg:flex-row gap-6 px-4 sm:px-6 lg:px-8 pb-8 flex-1">
        <!-- Left Menu -->
        <div class="lg:w-64 flex-shrink-0">
          <div class="bg-white rounded-lg shadow">
            <div class="p-4 border-b border-gray-200">
              <h2 class="text-lg font-semibold text-gray-900">Menu</h2>
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
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path v-if="menuItem.icon === 'building'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                  <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2M8 7a2 2 0 002-2h.5" />
                </svg>
                {{ menuItem.name }}
              </button>
            </nav>
          </div>
        </div>

        <!-- Main Data Area -->
        <div class="flex-1 flex flex-col">
          <div class="bg-white rounded-lg shadow flex flex-col min-w-0">
            <!-- Action Buttons Above Table -->
            <div class="p-4 border-b border-gray-200">
              <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="flex items-center space-x-4">
                  <h3 class="text-lg font-semibold text-gray-900">
                    {{ activeMenu === 'gedung' ? 'Data Gedung' : 'Data Ruangan' }}
                  </h3>
                  <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">
                    {{ activeMenu === 'gedung' ? filteredBuildings.length : filteredRooms.length }} data
                  </span>
                </div>
                <div class="flex flex-wrap gap-2">
                  <button
                    @click="refreshData"
                    :disabled="loading"
                    class="px-3 py-1.5 text-sm bg-white border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
                  >
                    <svg class="w-4 h-4 animate-spin" v-if="loading" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <svg class="w-4 h-4" v-else fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Refresh
                  </button>

                  <button
                    @click="exportData"
                    class="px-3 py-1.5 text-sm bg-white border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                  >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Export
                  </button>
                  <button
                    @click="importData"
                    class="px-3 py-1.5 text-sm bg-white border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                  >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Import
                  </button>
                  <button
                    @click="openAddModal"
                    class="px-3 py-1.5 text-sm bg-blue-600 border border-transparent rounded-md text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                  >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah {{ activeMenu === 'gedung' ? 'Gedung' : 'Ruangan' }}
                  </button>
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
                    {{ activeMenu === 'gedung' ? stats.active_buildings : stats.active_rooms || 0 }}
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
                    {{ activeMenu === 'gedung' ? stats.trashed_buildings : stats.trashed_rooms || 0 }}
                  </span>
                </button>
              </nav>
            </div>

            <!-- Search and Filters -->
            <div class="p-4 border-b border-gray-200 bg-gray-50">
              <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                <!-- Bulk Actions on Left -->
                <div class="flex items-center space-x-2">
                  <template v-if="selectedItems.length > 0">
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
                      <div v-if="showBulkActions" class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
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
                              @click="bulkDelete"
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
                  </template>
                </div>

                <!-- Search and Sort on Right -->
                <div class="flex items-center space-x-2 w-full lg:w-auto">
                  <div class="flex-1 lg:flex-initial">
                    <div class="relative">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                      </div>
                      <input
                        v-model="searchQuery"
                        type="text"
                        :placeholder="`Cari ${activeMenu === 'gedung' ? 'gedung' : 'ruangan'}...`"
                        class="pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full lg:w-64"
                      />
                    </div>
                  </div>
                  <div class="flex items-center space-x-2">
                    <label class="text-sm text-gray-700 whitespace-normal">Urutkan:</label>
                    <select
                      v-model="sortBy"
                      class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                      <option value="name">Nama</option>
                      <option value="code" v-if="activeMenu === 'gedung'">Kode</option>
                      <option value="created_at">Tanggal Dibuat</option>
                      <option value="capacity" v-if="activeMenu === 'ruangan'">Kapasitas</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>

  
            <!-- Data Table -->
            <div class="w-full border border-gray-200">
              <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
                <table class="w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr v-if="activeMenu === 'gedung'">
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">
                        <input
                          type="checkbox"
                          @change="toggleSelectAll"
                          :checked="allSelected"
                          class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        />
                      </th>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Nama Gedung
                      </th>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Kode
                      </th>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Lantai
                      </th>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
        Ruangan
                      </th>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                      </th>
                      <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Aksi
                      </th>
                    </tr>
                    <tr v-else>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">
                        <input
                          type="checkbox"
                          @change="toggleSelectAll"
                          :checked="allSelected"
                          class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        />
                      </th>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Ruangan
                      </th>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Gedung
                      </th>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Lantai
                      </th>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Kapasitas
                      </th>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                      </th>
                      <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Aksi
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-if="loading">
                      <td :colspan="activeMenu === 'gedung' ? 6 : 6" class="px-4 py-3 text-center text-gray-500">
                        <div class="flex items-center justify-center">
                          <svg class="animate-spin h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                          </svg>
                          Memuat data...
                        </div>
                      </td>
                    </tr>
                    <tr v-else-if="currentData.length === 0">
                      <td :colspan="activeMenu === 'gedung' ? 6 : 6" class="px-4 py-3 text-center text-gray-500">
                        <div class="text-center">
                          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                          </svg>
                          <p class="mt-2 text-sm text-gray-600">Tidak ada data {{ activeMenu === 'gedung' ? 'gedung' : 'ruangan' }} yang ditemukan</p>
                        </div>
                      </td>
                    </tr>
                    <template v-else>
                      <tr v-for="item in currentData" :key="item.id" class="hover:bg-gray-50">
                        <template v-if="activeMenu === 'gedung'">
                          <td class="px-4 py-3 whitespace-normal">
                            <input
                              type="checkbox"
                              :value="item.id"
                              v-model="selectedItems"
                              class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                            />
                          </td>
                          <td class="px-4 py-3 whitespace-normal">
                            <div class="text-sm font-medium text-gray-900">{{ item.name }}</div>
                            <div class="text-sm text-gray-500">{{ item.description }}</div>
                          </td>
                          <td class="px-4 py-3 whitespace-normal">
                            <div class="text-sm text-gray-900">{{ item.code }}</div>
                          </td>
                          <td class="px-4 py-3 whitespace-normal">
                            <div class="text-sm text-gray-900">{{ item.floor_count }}</div>
                          </td>
                          <td class="px-4 py-3 whitespace-normal">
                            <div class="text-sm text-gray-900">{{ item.total_rooms }}</div>
                          </td>
                          <td class="px-4 py-3 whitespace-normal">
                            <label class="flex items-center cursor-pointer">
                              <input
                                type="checkbox"
                                :checked="item.is_active"
                                @change="toggleBuildingStatus(item)"
                                class="sr-only"
                              />
                              <div class="relative">
                                <div class="block bg-gray-300 w-10 h-6 rounded-full transition-colors" :class="item.is_active ? 'bg-green-500' : 'bg-gray-300'"></div>
                                <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform" :class="item.is_active ? 'translate-x-4' : 'translate-x-0'"></div>
                              </div>
                              <span class="ml-2 text-sm" :class="item.is_active ? 'text-green-600' : 'text-gray-500'">
                                {{ item.is_active ? 'Aktif' : 'Nonaktif' }}
                              </span>
                            </label>
                          </td>
                          <td class="px-4 py-3 whitespace-normal text-sm text-gray-500">
                            <div class="flex items-center space-x-2">
                              <template v-if="activeTab === 'active'">
                                <button
                                  @click="duplicateBuilding(item)"
                                  class="text-purple-600 hover:text-purple-900"
                                  title="Duplikat"
                                >
                                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                  </svg>
                                </button>
                                <button
                                  @click="editBuilding(item)"
                                  class="text-blue-600 hover:text-blue-900"
                                  title="Edit"
                                >
                                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                  </svg>
                                </button>
                                <button
                                  @click="deleteBuilding(item)"
                                  class="text-red-600 hover:text-red-900"
                                  title="Hapus"
                                >
                                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                  </svg>
                                </button>
                              </template>
                              <template v-else>
                                <button
                                  @click="restoreItem(item)"
                                  class="text-green-600 hover:text-green-900"
                                  title="Pulihkan"
                                >
                                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                  </svg>
                                </button>
                                <button
                                  @click="deleteBuilding(item)"
                                  class="text-red-600 hover:text-red-900"
                                  title="Hapus Permanen"
                                >
                                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                  </svg>
                                </button>
                              </template>
                            </div>
                          </td>
                        </template>
                        <template v-else>
                          <td class="px-4 py-3 whitespace-normal">
                            <input
                              type="checkbox"
                              :value="item.id"
                              v-model="selectedItems"
                              class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                            />
                          </td>
                          <td class="px-4 py-3 whitespace-normal">
                            <div class="text-sm font-medium text-gray-900">{{ item.name }}</div>
                            <div class="text-sm text-gray-500">{{ item.room_type }}</div>
                          </td>
                          <td class="px-4 py-3 whitespace-normal">
                            <div class="text-sm text-gray-900">{{ item.building }}</div>
                          </td>
                          <td class="px-4 py-3 whitespace-normal">
                            <div class="text-sm text-gray-900">{{ item.floor }}</div>
                          </td>
                          <td class="px-4 py-3 whitespace-normal">
                            <div class="text-sm text-gray-900">{{ item.capacity }}</div>
                          </td>
                          <td class="px-4 py-3 whitespace-normal">
                            <label class="flex items-center cursor-pointer">
                              <input
                                type="checkbox"
                                :checked="item.is_active"
                                @change="toggleRoomStatus(item)"
                                class="sr-only"
                              />
                              <div class="relative">
                                <div class="block bg-gray-300 w-10 h-6 rounded-full transition-colors" :class="item.is_active ? 'bg-green-500' : 'bg-gray-300'"></div>
                                <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform" :class="item.is_active ? 'translate-x-4' : 'translate-x-0'"></div>
                              </div>
                              <span class="ml-2 text-sm" :class="item.is_active ? 'text-green-600' : 'text-gray-500'">
                                {{ item.is_active ? 'Aktif' : 'Nonaktif' }}
                              </span>
                            </label>
                          </td>
                          <td class="px-4 py-3 whitespace-normal text-sm text-gray-500">
                            <div class="flex items-center space-x-2">
                              <template v-if="activeTab === 'active'">
                                <button
                                  @click="duplicateRoom(item)"
                                  class="text-purple-600 hover:text-purple-900"
                                  title="Duplikat"
                                >
                                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                  </svg>
                                </button>
                                <button
                                  @click="editRoom(item)"
                                  class="text-blue-600 hover:text-blue-900"
                                  title="Edit"
                                >
                                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                  </svg>
                                </button>
                                <button
                                  @click="deleteRoom(item)"
                                  class="text-red-600 hover:text-red-900"
                                  title="Hapus"
                                >
                                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                  </svg>
                                </button>
                              </template>
                              <template v-else>
                                <button
                                  @click="restoreItem(item)"
                                  class="text-green-600 hover:text-green-900"
                                  title="Pulihkan"
                                >
                                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                  </svg>
                                </button>
                                <button
                                  @click="deleteRoom(item)"
                                  class="text-red-600 hover:text-red-900"
                                  title="Hapus Permanen"
                                >
                                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                  </svg>
                                </button>
                              </template>
                            </div>
                          </td>
                        </template>
                      </tr>
                    </template>
                  </tbody>
                </table>
              </div>
            </div>
            </div>

            <!-- Pagination -->
            <div class="bg-white px-3 sm:px-4 py-3 border-t border-gray-200 overflow-x-auto">
              <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 min-w-max">
                <!-- Show entries dropdown and pagination info -->
                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                  <!-- Show entries dropdown -->
                  <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-700">Show</label>
                    <select
                      v-model="entriesPerPage"
                      @change="onEntriesPerPageChange($event.target.value)"
                      class="px-3 py-1 text-sm border border-gray-300 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                      <option value="5">5</option>
                      <option value="10">10</option>
                      <option value="25">25</option>
                      <option value="50">50</option>
                      <option value="100">100</option>
                    </select>
                    <label class="text-sm text-gray-700">entries</label>
                  </div>

                  <!-- Pagination info -->
                  <div v-if="pagination && pagination.total > 0" class="text-sm text-gray-700">
                    Showing {{ pagination.from || 0 }} to {{ pagination.to || 0 }} of {{ pagination.total || 0 }} results
                  </div>
                </div>

                <!-- Numbered Pagination -->
                <div v-if="pagination && pagination.total > 0" class="flex items-center gap-1">
                  <!-- Previous button -->
                  <button
                    @click="onPageChange(pagination.current_page - 1)"
                    :disabled="pagination.current_page <= 1"
                    class="px-3 py-1 text-sm border border-gray-300 rounded-md bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    Previous
                  </button>

                  <!-- Page numbers -->
                  <template v-for="page in generatePageNumbers(pagination.current_page, pagination.last_page)" :key="page">
                    <span v-if="page === '...'" class="px-3 py-1 text-sm text-gray-500">...</span>
                    <button
                      v-else
                      @click="onPageChange(page)"
                      :class="[
                        'px-3 py-1 text-sm border rounded-md',
                        page === pagination.current_page
                          ? 'bg-blue-500 text-white border-blue-500'
                          : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                      ]"
                    >
                      {{ page }}
                    </button>
                  </template>

                  <!-- Next button -->
                  <button
                    @click="onPageChange(pagination.current_page + 1)"
                    :disabled="pagination.current_page >= pagination.last_page"
                    class="px-3 py-1 text-sm border border-gray-300 rounded-md bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    Next
                  </button>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>

    <!-- Add/Edit Modal -->
    <div v-if="showAddModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="mb-4">
              <h3 class="text-lg font-medium text-gray-900">
                {{ editingBuilding ? 'Edit Gedung' : editingRoom ? 'Edit Ruangan' : `Tambah ${activeMenu === 'gedung' ? 'Gedung' : 'Ruangan'}` }}
              </h3>
            </div>
            <!-- Building Form -->
            <form v-if="activeMenu === 'gedung'" @submit.prevent="saveBuilding">
              <div class="grid grid-cols-1 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700">Nama Gedung *</label>
                  <input
                    v-model="buildingForm.name"
                    type="text"
                    required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Kode *</label>
                  <input
                    v-model="buildingForm.code"
                    type="text"
                    required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                  <textarea
                    v-model="buildingForm.description"
                    rows="3"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                  ></textarea>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Alamat</label>
                  <textarea
                    v-model="buildingForm.address"
                    rows="2"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                  ></textarea>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Jumlah Lantai</label>
                    <input
                      v-model.number="buildingForm.floor_count"
                      type="number"
                      min="1"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Jumlah Ruangan</label>
                    <input
                      v-model.number="buildingForm.total_rooms"
                      type="number"
                      min="0"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    />
                  </div>
                </div>
              </div>
            </form>
            <!-- Room Form -->
            <form v-else @submit.prevent="saveRoom">
              <div class="grid grid-cols-1 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700">Nama Ruangan *</label>
                  <input
                    v-model="roomForm.name"
                    type="text"
                    required
                    :class="['mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm', formErrors.name ? 'border-red-500' : '']"
                  />
                  <p v-if="formErrors.name" class="mt-1 text-sm text-red-600">
                    {{ Array.isArray(formErrors.name) ? formErrors.name[0] : formErrors.name }}
                  </p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Gedung *</label>
                  <select
                    v-model="roomForm.building"
                    required
                    :class="['mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm', formErrors.building ? 'border-red-500' : '']"
                    @change="updateRoomCodePreview"
                  >
                    <option value="">Pilih Gedung</option>
                    <option v-for="building in buildings" :key="building.id" :value="building.name">
                      {{ building.name }}
                    </option>
                  </select>
                  <p v-if="formErrors.building" class="mt-1 text-sm text-red-600">
                    {{ Array.isArray(formErrors.building) ? formErrors.building[0] : formErrors.building }}
                  </p>
                </div>
                  <div>
                  <label class="block text-sm font-medium text-gray-700">Kode Ruangan (Auto)</label>
                  <div class="mt-1 flex items-center space-x-2">
                    <div class="flex-1 bg-gray-100 border border-gray-300 rounded-md px-3 py-2 text-sm">
                      <span class="font-mono text-gray-700">
                        {{ roomCodePreview || 'Format: KODESP-KODEGEDUNG-KODERUANGAN' }}
                      </span>
                    </div>
                  </div>
                  <p class="mt-1 text-xs text-gray-500">Kode akan dibuat otomatis saat menyimpan</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Lantai *</label>
                    <select
                      v-model.number="roomForm.floor"
                      required
                      :disabled="!roomForm.building"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm disabled:bg-gray-100 disabled:cursor-not-allowed"
                      @change="updateRoomCodePreview"
                    >
                      <option value="">Pilih Lantai</option>
                      <option v-for="floor in floorOptions" :key="floor" :value="floor">
                        Lantai {{ floor }}
                      </option>
                    </select>
                    <p v-if="!roomForm.building" class="mt-1 text-xs text-gray-500">Pilih gedung terlebih dahulu</p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Kapasitas</label>
                    <input
                      v-model.number="roomForm.capacity"
                      type="number"
                      min="1"
                      required
                      :class="['mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm', formErrors.capacity ? 'border-red-500' : '']"
                    />
                    <p v-if="formErrors.capacity" class="mt-1 text-sm text-red-600">
                      {{ Array.isArray(formErrors.capacity) ? formErrors.capacity[0] : formErrors.capacity }}
                    </p>
                  </div>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Tipe Ruangan</label>
                  <select
                    v-model="roomForm.room_type"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                  >
                    <option value="classroom">Ruang Kelas</option>
                    <option value="laboratory">Laboratorium</option>
                    <option value="seminar_room">Ruang Seminar</option>
                    <option value="auditorium">Auditorium</option>
                    <option value="workshop">Bengkel</option>
                    <option value="library">Perpustakaan</option>
                    <option value="office">Kantor</option>
                    <option value="meeting_room">Ruang Rapat</option>
                    <option value="multipurpose">Ruang Serbaguna</option>
                  </select>
                </div>
              </div>
            </form>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              type="submit"
              @click="activeMenu === 'gedung' ? saveBuilding() : saveRoom()"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
            >
              {{ editingBuilding || editingRoom ? 'Perbarui' : 'Simpan' }}
            </button>
            <button
              type="button"
              @click="closeModal"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Batal
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Dialog -->
    <div v-if="showDeleteDialog" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <!-- Center modal -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <!-- Modal Header -->
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
              </div>
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                  Konfirmasi Hapus {{ deleteType }}
                </h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-500">
                    Apakah Anda yakin ingin menghapus {{ deleteType }} "{{ itemName }}"?
                  </p>
                  <div v-if="activeTab === 'active'" class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                    <p class="text-sm text-yellow-800">
                      <strong>Informasi:</strong> Data akan dipindahkan ke trash dan dapat dipulihkan kembali.
                    </p>
                  </div>
                  <div v-else class="mt-3 p-3 bg-red-50 border border-red-200 rounded-md">
                    <p class="text-sm text-red-800">
                      <strong>Peringatan:</strong> Ini akan menghapus data secara permanen dan tidak dapat dipulihkan!
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Modal Footer -->
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              type="button"
              @click="confirmDelete"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
              {{ activeTab === 'active' ? 'Hapus' : 'Hapus Permanen' }}
            </button>
            <button
              type="button"
              @click="showDeleteDialog = false"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors"
            >
              Batal
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Bulk Action Confirmation Dialog -->
    <div v-if="showBulkConfirmDialog" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <!-- Center modal -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <!-- Modal Header -->
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full"
                :class="getBulkActionIconClass()">
                <svg class="h-6 w-6"
                  :class="getBulkActionIconTextColor()"
                  fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    :d="getBulkActionIconPath()" />
                </svg>
              </div>
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                  Konfirmasi Operasi Bulk
                </h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-500">
                    {{ bulkActionMessage }}
                  </p>
                  <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-md">
                    <p class="text-sm text-blue-800">
                      <strong>Informasi:</strong>
                      <span v-if="bulkAction === 'activate'">Data akan diaktifkan dan dapat digunakan kembali.</span>
                      <span v-else-if="bulkAction === 'deactivate'">Data akan dinonaktifkan dan tidak dapat digunakan sementara.</span>
                      <span v-else-if="bulkAction === 'delete' && activeTab === 'active'">Data akan dipindahkan ke trash dan dapat dipulihkan kembali.</span>
                      <span v-else-if="bulkAction === 'delete' && activeTab === 'trash'">Ini akan menghapus data secara permanen dan tidak dapat dipulihkan!</span>
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Modal Footer -->
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              type="button"
              @click="executeBulkAction"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm transition-colors"
              :class="getBulkActionButtonClass()"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  :d="getBulkActionButtonIcon()" />
              </svg>
              {{ bulkAction === 'activate' ? 'Aktifkan' : bulkAction === 'deactivate' ? 'Nonaktifkan' : 'Hapus' }}
            </button>
            <button
              type="button"
              @click="showBulkConfirmDialog = false; bulkAction = ''; bulkActionMessage = ''"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors"
            >
              Batal
            </button>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';

// Debounce helper
const debounce = (func, delay) => {
  let timeoutId;
  return function (...args) {
    clearTimeout(timeoutId);
    timeoutId = setTimeout(() => func.apply(this, args), delay);
  };
};
import Layout from '@/components/Layout.vue';
import Toast from '@/components/Toast.vue';
import roomService from '@/services/roomService';
import buildingService from '@/services/buildingService';
// import LaravelVuePagination from 'vue-laravel-pagination';

// Toast state
const toast = ref({
  show: false,
  title: '',
  description: '',
  type: 'info',
  duration: 5000
});

// Toast functions
const showToast = (title, description = '', type = 'info', duration = 5000) => {
  toast.value = {
    show: true,
    title,
    description,
    type,
    duration
  };
};

const hideToast = () => {
  toast.value.show = false;
};

// Data
const loading = ref(false);
const activeMenu = ref('gedung');
const activeTab = ref('active');
const searchQuery = ref('');
const sortBy = ref('name');
const entriesPerPage = ref(10);
const showAddModal = ref(false);
const editingBuilding = ref(null);
const editingRoom = ref(null);
const selectedItems = ref([]);
const showBulkActions = ref(false);

// Laravel Vue Pagination data
const roomsPagination = ref({
  data: [],
  current_page: 1,
  last_page: 1,
  per_page: 10,
  total: 0,
  from: 0,
  to: 0
});

const buildingsPagination = ref({
  data: [],
  current_page: 1,
  last_page: 1,
  per_page: 10,
  total: 0,
  from: 0,
  to: 0
});

// Computed pagination for template
const pagination = computed(() => {
  return activeMenu.value === 'gedung' ? buildingsPagination.value : roomsPagination.value;
});

// Current data based on active menu and tab
const currentData = computed(() => {
  if (activeMenu.value === 'gedung') {
    return buildings.value || [];
  } else {
    return rooms.value || [];
  }
});

// Dialog states
const showDeleteDialog = ref(false);
const showBulkConfirmDialog = ref(false);
const showDuplicateDialog = ref(false);
const itemToDelete = ref(null);
const itemName = ref('');
const deleteType = ref('');
const bulkAction = ref('');
const bulkActionMessage = ref('');
const duplicateData = ref({});
const originalRoomData = ref({});
const formErrors = ref({});

// Buildings
const buildings = ref([]);
const filteredBuildings = computed(() => {
  return buildings.value;
});

// Floor options based on selected building
const floorOptions = computed(() => {
  if (!roomForm.value.building) return [];

  const selectedBuilding = buildings.value.find(b => b.name === roomForm.value.building);
  if (!selectedBuilding || !selectedBuilding.floor_count || selectedBuilding.floor_count <= 0) {
    // Default floor options if building has no floor_count data
    return Array.from({ length: 5 }, (_, i) => i + 1); // 1-5 as default
  }

  return Array.from({ length: selectedBuilding.floor_count }, (_, i) => i + 1);
});

// Rooms
const rooms = ref([]);
const filteredRooms = computed(() => {
  return rooms.value;
});




// Stats
const stats = ref({
  total_buildings: 0,
  active_buildings: 0,
  trashed_buildings: 0,
  total_rooms: 0,
  active_rooms: 0,
  trashed_rooms: 0,
  total_capacity: 0,
  utilization_rate: 0
});

// Forms
const buildingForm = ref({
  name: '',
  code: '',
  description: '',
  address: '',
  floor_count: 1,
  total_rooms: 0
});

const roomForm = ref({
  name: '',
  building: '',
  room_code: '',
  department: '',
  faculty: '',
  floor: 1,
  capacity: 0,
  room_type: 'classroom'
});

const roomCodePreview = ref('');

// Menu items
const menuItems = [
  { key: 'gedung', name: 'Gedung', icon: 'building' },
  { key: 'ruangan', name: 'Ruangan', icon: 'room' }
];

// Helper methods for bulk action modal
const getBulkActionIconClass = () => {
  return bulkAction.value === 'delete' ? 'bg-red-100' : 'bg-yellow-100';
};

const getBulkActionIconTextColor = () => {
  return bulkAction.value === 'delete' ? 'text-red-600' : 'text-yellow-600';
};

const getBulkActionIconPath = () => {
  return bulkAction.value === 'delete'
    ? 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'
    : 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
};

const getBulkActionButtonClass = () => {
  return bulkAction.value === 'delete'
    ? 'bg-red-600 hover:bg-red-700 focus:ring-red-500'
    : 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500';
};

const getBulkActionButtonIcon = () => {
  return bulkAction.value === 'delete'
    ? 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16'
    : 'M5 13l4 4L19 7';
};

// Methods
const loadData = async () => {
  loading.value = true;

  // Minimal loading time untuk UX (cukup berkedip)
  const minLoadingTime = 150; // 150ms
  const startTime = Date.now();
  try {
    // Prepare query parameters for server-side filtering and pagination
    const params = {
      page: activeMenu.value === 'gedung' ? buildingsPagination.value.current_page : roomsPagination.value.current_page,
      per_page: entriesPerPage.value,
    };

    // Add search if exists
    if (searchQuery.value) {
      params.search = searchQuery.value;
    }

    // Add sort parameter in the correct format: sort_field:direction
    if (sortBy.value) {
      params.sort = sortBy.value;
    }

    if (activeMenu.value === 'gedung') {
      if (activeTab.value === 'trash') {
        const response = await buildingService.getTrash(params);
        buildingsPagination.value = response.meta?.pagination || response;
        buildings.value = response.data || [];
      } else {
        const response = await buildingService.getAll(params);
        buildingsPagination.value = response.meta?.pagination || response;
        buildings.value = response.data || [];
      }
    } else {
      if (activeTab.value === 'trash') {
        const response = await roomService.getTrash(params);
        roomsPagination.value = response.meta?.pagination || response;
        rooms.value = response.data || [];
      } else {
        try {
          const response = await roomService.getAll(params);
          roomsPagination.value = response.meta?.pagination || response;
          rooms.value = response.data || [];
        } catch (error) {
          // Set dummy room data with pagination structure if API fails
          const dummyRooms = [
            {
              id: 1,
              name: 'Ruang Kelas A-101',
              building: 'Gedung A',
              room_code: 'SPS-GA1-KLS01',
              floor: 1,
              capacity: 30,
              room_type: 'classroom',
              is_active: true,
              created_at: '2024-01-01'
            },
            {
              id: 2,
              name: 'Laboratorium Komputer',
              building: 'Gedung B',
              room_code: 'SPS-GB2-LAB01',
              floor: 2,
              capacity: 25,
              room_type: 'laboratory',
              is_active: true,
              created_at: '2024-01-02'
            },
            {
              id: 3,
              name: 'Ruang Seminar',
              building: 'Gedung C',
              room_code: 'SPS-GC3-SMR01',
              floor: 1,
              capacity: 50,
              room_type: 'seminar_room',
              is_active: false,
              created_at: '2024-01-03'
            },
            {
              id: 4,
              name: 'Ruang Dosen A',
              building: 'Gedung A',
              room_code: 'SPS-GA1-DSN01',
              floor: 2,
              capacity: 15,
              room_type: 'office',
              is_active: true,
              created_at: '2024-01-04'
            },
            {
              id: 5,
              name: 'Lab Fisika',
              building: 'Gedung B',
              room_code: 'SPS-GB2-LAB02',
              floor: 1,
              capacity: 20,
              room_type: 'laboratory',
              is_active: true,
              created_at: '2024-01-05'
            },
            {
              id: 6,
              name: 'Ruang Teori B',
              building: 'Gedung A',
              room_code: 'SPS-GA1-THR02',
              floor: 3,
              capacity: 40,
              room_type: 'classroom',
              is_active: true,
              created_at: '2024-01-06'
            },
            {
              id: 7,
              name: 'Ruang Multimedia',
              building: 'Gedung C',
              room_code: 'SPS-GC3-MMM01',
              floor: 2,
              capacity: 60,
              room_type: 'multimedia_room',
              is_active: false,
              created_at: '2024-01-07'
            },
            {
              id: 8,
              name: 'Ruang Workshop',
              building: 'Gedung D',
              room_code: 'SPS-GD4-WRK01',
              floor: 1,
              capacity: 35,
              room_type: 'workshop',
              is_active: true,
              created_at: '2024-01-08'
            },
            {
              id: 9,
              name: 'Lab Bahasa',
              building: 'Gedung B',
              room_code: 'SPS-GB2-LAB03',
              floor: 3,
              capacity: 18,
              room_type: 'language_lab',
              is_active: true,
              created_at: '2024-01-09'
            },
            {
              id: 10,
              name: 'Ruang Rapat A',
              building: 'Gedung C',
              room_code: 'SPS-GC3-RPT01',
              floor: 4,
              capacity: 25,
              room_type: 'meeting_room',
              is_active: true,
              created_at: '2024-01-10'
            },
            {
              id: 11,
              name: 'Ruang Kuliah C',
              building: 'Gedung A',
              room_code: 'SPS-GA1-KLS03',
              floor: 1,
              capacity: 45,
              room_type: 'classroom',
              is_active: true,
              created_at: '2024-01-11'
            }
          ];

          // Set pagination structure
          roomsPagination.value = {
            data: dummyRooms,
            current_page: 1,
            last_page: 2,
            per_page: 10,
            total: 11,
            from: 1,
            to: 10
          };
          rooms.value = dummyRooms;
        }
      }
    }
    await loadStats();
    showToast('Sukses', 'Data berhasil dimuat ulang', 'success');
  } catch (error) {
    showToast('Error', error.userMessage || 'Gagal memuat data', 'error');
  } finally {
    // Pastikan loading minimal 150ms untuk UX yang halus
    const elapsed = Date.now() - startTime;
    const remainingTime = Math.max(0, minLoadingTime - elapsed);

    if (remainingTime > 0) {
      setTimeout(() => {
        loading.value = false;
      }, remainingTime);
    } else {
      loading.value = false;
    }
  }
};

// updatePaginationData function removed - no longer needed

const loadStats = async () => {
  try {
    const [buildingStats, roomStats] = await Promise.all([
      buildingService.getStatistics(),
      roomService.getStatistics()
    ]);

    // Merge building and room statistics properly
    stats.value = {
      // Building stats
      total_buildings: buildingStats.data?.total_buildings || 0,
      active_buildings: buildingStats.data?.active_buildings || 0,
      trashed_buildings: buildingStats.data?.trashed_buildings || 0,
      // Room stats
      total_rooms: roomStats.data?.total_rooms || 0,
      active_rooms: roomStats.data?.active_rooms || 0,
      trashed_rooms: roomStats.data?.trashed_rooms || 0,
      total_capacity: roomStats.data?.capacity_statistics?.total_capacity || 0,
      utilization_rate: roomStats.data?.utilization_rate || 0
    };
  } catch (error) {
    console.error('Failed to load stats:', error);
    // Set default values on error
    stats.value = {
      total_buildings: 0,
      active_buildings: 0,
      trashed_buildings: 0,
      total_rooms: 0,
      active_rooms: 0,
      trashed_rooms: 0,
      total_capacity: 0,
      utilization_rate: 0
    };
  }
};

const refreshData = () => {
  roomsPagination.value.current_page = 1;
  loadData();
};

// Laravel Vue Pagination event handler
const onPageChange = (page) => {
  if (activeMenu.value === 'gedung') {
    buildingsPagination.value.current_page = page;
  } else {
    roomsPagination.value.current_page = page;
  }
  loadData();
};

const onEntriesPerPageChange = (value) => {
  entriesPerPage.value = parseInt(value);
  roomsPagination.value.current_page = 1; // Reset to first page
  buildingsPagination.value.current_page = 1; // Reset to first page
  loadData();
};

const generatePageNumbers = (currentPage, lastPage) => {
  const pages = [];
  const delta = 2; // Number of pages to show on each side of current page

  // Always show first page
  pages.push(1);

  // Calculate range around current page
  const left = Math.max(2, currentPage - delta);
  const right = Math.min(lastPage - 1, currentPage + delta);

  // Add ellipsis if needed
  if (left > 2) {
    pages.push('...');
  }

  // Add pages in range
  for (let i = left; i <= right; i++) {
    if (i !== 1 && i !== lastPage) {
      pages.push(i);
    }
  }

  // Add ellipsis if needed
  if (right < lastPage - 1) {
    pages.push('...');
  }

  // Always show last page if it's different from first
  if (lastPage > 1) {
    pages.push(lastPage);
  }

  // Remove duplicates
  return [...new Set(pages)];
};


const closeModal = () => {
  showAddModal.value = false;
  editingBuilding.value = null;
  editingRoom.value = null;
  roomCodePreview.value = '';
  formErrors.value = {};
  buildingForm.value = {
    name: '',
    code: '',
    description: '',
    address: '',
    floor_count: 1,
    total_rooms: 0
  };
  roomForm.value = {
    name: '',
    building: '',
    room_code: '',
    floor: 1,
    capacity: 0,
    room_type: 'classroom'
  };
};

// Building methods
const editBuilding = (building) => {
  editingBuilding.value = building;
  buildingForm.value = { ...building };
  showAddModal.value = true;
};

const saveBuilding = async () => {
  try {
    if (editingBuilding.value) {
      await buildingService.update(editingBuilding.value.id, buildingForm.value);
      showToast('Sukses', 'Gedung berhasil diperbarui', 'success');
    } else {
      await buildingService.create(buildingForm.value);
      showToast('Sukses', 'Gedung berhasil ditambahkan', 'success');
    }
    closeModal();
    await loadData();
  } catch (error) {
    showToast('Error', error.userMessage || 'Gagal menyimpan gedung', 'error');
  }
};

const deleteBuilding = (building) => {
  itemToDelete.value = building;
  itemName.value = building.name;
  deleteType.value = 'Gedung';
  showDeleteDialog.value = true;
};

const openAddModal = () => {
  // Clear previous form errors
  formErrors.value = {};

  editingBuilding.value = null;
  editingRoom.value = null;
  showAddModal.value = true;
};

// Room methods
const editRoom = (room) => {
  // Clear previous form errors
  formErrors.value = {};

  editingRoom.value = room;
  roomForm.value = {
    ...room,
    capacity: Number(room.capacity) || 0,
    floor: Number(room.floor) || 1
  };
  showAddModal.value = true;
};

const saveRoom = async () => {
  try {
    // Ensure numeric fields are properly converted
    const formData = {
      ...roomForm.value,
      capacity: Number(roomForm.value.capacity) || 0,
      floor: Number(roomForm.value.floor) || 1
    };

    if (editingRoom.value) {
      await roomService.update(editingRoom.value.id, formData);
      showToast('Sukses', 'Ruangan berhasil diperbarui', 'success');
    } else {
      await roomService.create(formData);
      showToast('Sukses', 'Ruangan berhasil ditambahkan', 'success');
    }
    closeModal();
    await loadData();
  } catch (error) {
    // Clear previous errors
    formErrors.value = {};

    if (error.errorType === 'VALIDATION_ERROR' && error.validationErrors) {
      // Set field-specific errors
      formErrors.value = error.validationErrors;
      showToast('Error', 'Validasi gagal. Silakan periksa kembali input Anda.', 'error');
    } else {
      showToast('Error', error.userMessage || 'Gagal menyimpan ruangan', 'error');
    }
  }
};

const deleteRoom = (room) => {
  itemToDelete.value = room;
  itemName.value = room.name;
  deleteType.value = 'Ruangan';
  showDeleteDialog.value = true;
};

const restoreItem = async (item) => {
  const itemType = activeMenu.value === 'gedung' ? 'gedung' : 'ruangan';
  const service = activeMenu.value === 'gedung' ? buildingService : roomService;

  try {
    await service.restore(item.id);
    showToast('Sukses', `${itemType.charAt(0).toUpperCase() + itemType.slice(1)} berhasil dipulihkan`, 'success');
    await loadData();
  } catch (error) {
    showToast('Error', error.userMessage || `Gagal memulihkan ${itemType}`, 'error');
  }
};

const confirmDelete = async () => {
  if (!itemToDelete.value) return;

  try {
    const itemType = activeMenu.value === 'gedung' ? 'gedung' : 'ruangan';
    const service = activeMenu.value === 'gedung' ? buildingService : roomService;

    if (activeTab.value === 'active') {
      // Soft delete - move to trash
      await service.delete(itemToDelete.value.id);
      showToast('Sukses', `${deleteType.value} berhasil dipindahkan ke trash`, 'success');
    } else {
      // Hard delete - permanent deletion from trash
      await service.forceDelete(itemToDelete.value.id);
      showToast('Sukses', `${deleteType.value} berhasil dihapus permanen`, 'success');
    }

    showDeleteDialog.value = false;
    itemToDelete.value = null;
    itemName.value = '';
    deleteType.value = '';
    await loadData();
  } catch (error) {
    showToast('Error', error.userMessage || `Gagal menghapus ${deleteType.value.toLowerCase()}`, 'error');
  }
};

// Duplicate functions
const duplicateBuilding = async (building) => {
  try {
    await buildingService.duplicate(building.id);
    showToast('Sukses', 'Gedung berhasil diduplikasi', 'success');
    await loadData();
  } catch (error) {
    showToast('Error', error.userMessage || 'Gagal menduplikasi gedung', 'error');
  }
};

const duplicateRoom = async (room) => {
  try {
    await roomService.duplicate(room.id);
    showToast('Sukses', 'Ruangan berhasil diduplikasi', 'success');
    await loadData();
  } catch (error) {
    showToast('Error', error.userMessage || 'Gagal menduplikasi ruangan', 'error');
  }
};

// Toggle status methods
const toggleBuildingStatus = async (building) => {
  const newStatus = !building.is_active;
  const action = newStatus ? 'mengaktifkan' : 'menonaktifkan';

  try {
    await buildingService.toggleStatus(building.id, { is_active: Boolean(newStatus) });
    building.is_active = newStatus;
    showToast('Sukses', `Gedung berhasil ${action}`, 'success');
    // Don't reload all data, just update the UI
  } catch (error) {
    // Revert the status on error
    building.is_active = !newStatus;
    showToast('Error', error.userMessage || `Gagal ${action} gedung`, 'error');
  }
};

const toggleRoomStatus = async (room) => {
  const newStatus = !room.is_active;
  const action = newStatus ? 'mengaktifkan' : 'menonaktifkan';

  try {
    await roomService.toggleStatus(room.id, { is_active: Boolean(newStatus) });
    room.is_active = newStatus;
    showToast('Sukses', `Ruangan berhasil ${action}`, 'success');
    // Don't reload all data, just update the UI
  } catch (error) {
    // Revert the status on error
    room.is_active = !newStatus;
    showToast('Error', error.userMessage || `Gagal ${action} ruangan`, 'error');
  }
};

// Bulk action methods
const allSelected = computed(() => {
  return currentData.value.length > 0 && selectedItems.value.length === currentData.value.length;
});

const toggleSelectAll = () => {
  if (allSelected.value) {
    selectedItems.value = [];
  } else {
    selectedItems.value = currentData.value.map(item => item.id);
  }
};

const bulkActivate = () => {
  if (selectedItems.value.length === 0) {
    showToast('Info', 'Pilih data terlebih dahulu', 'info');
    return;
  }

  bulkAction.value = 'activate';
  bulkActionMessage.value = `Apakah Anda yakin ingin mengaktifkan ${selectedItems.value.length} data yang dipilih?`;
  showBulkConfirmDialog.value = true;
};

const bulkDeactivate = () => {
  if (selectedItems.value.length === 0) {
    showToast('Info', 'Pilih data terlebih dahulu', 'info');
    return;
  }

  bulkAction.value = 'deactivate';
  bulkActionMessage.value = `Apakah Anda yakin ingin menonaktifkan ${selectedItems.value.length} data yang dipilih?`;
  showBulkConfirmDialog.value = true;
};

const bulkDelete = () => {
  if (selectedItems.value.length === 0) {
    showToast('Info', 'Pilih data terlebih dahulu', 'info');
    return;
  }

  bulkAction.value = 'delete';
  const action = activeTab.value === 'active' ? 'memindahkan ke trash' : 'menghapus permanen';
  bulkActionMessage.value = `Apakah Anda yakin ingin ${action} ${selectedItems.value.length} data yang dipilih?`;
  showBulkConfirmDialog.value = true;
};

const bulkRestore = () => {
  if (selectedItems.value.length === 0) {
    showToast('Info', 'Pilih data terlebih dahulu', 'info');
    return;
  }

  bulkAction.value = 'restore';
  bulkActionMessage.value = `Apakah Anda yakin ingin memulihkan ${selectedItems.value.length} data yang dipilih?`;
  showBulkConfirmDialog.value = true;
};

const executeBulkAction = async () => {
  try {
    const service = activeMenu.value === 'gedung' ? buildingService : roomService;
    const itemType = activeMenu.value === 'gedung' ? 'Gedung' : 'Ruangan';
    const idField = activeMenu.value === 'gedung' ? 'building_ids' : 'room_ids';

    switch (bulkAction.value) {
      case 'activate':
        await service.bulkToggleStatus({ [idField]: selectedItems.value, is_active: true });
        showToast('Sukses', `${selectedItems.value.length} ${itemType.toLowerCase()} berhasil diaktifkan`, 'success');
        break;
      case 'deactivate':
        await service.bulkToggleStatus({ [idField]: selectedItems.value, is_active: false });
        showToast('Sukses', `${selectedItems.value.length} ${itemType.toLowerCase()} berhasil dinonaktifkan`, 'success');
        break;
      case 'restore':
        // Handle bulk restore - need to implement this
        for (const id of selectedItems.value) {
          await service.restore(id);
        }
        showToast('Sukses', `${selectedItems.value.length} ${itemType.toLowerCase()} berhasil dipulihkan`, 'success');
        break;
      case 'delete':
        if (activeTab.value === 'active') {
          await service.bulkDelete({ [idField]: selectedItems.value });
          showToast('Sukses', `${selectedItems.value.length} ${itemType.toLowerCase()} berhasil dipindahkan ke trash`, 'success');
        } else {
          await service.bulkForceDelete({ [idField]: selectedItems.value });
          showToast('Sukses', `${selectedItems.value.length} ${itemType.toLowerCase()} berhasil dihapus permanen`, 'success');
        }
        break;
    }

    selectedItems.value = [];
    showBulkConfirmDialog.value = false;
    bulkAction.value = '';
    bulkActionMessage.value = '';
    await loadData();
  } catch (error) {
    // Close modal first, then show error toast
    showBulkConfirmDialog.value = false;
    showToast('Error', error.response?.data?.message || error.userMessage || 'Gagal melakukan operasi bulk', 'error');
  }
};

const exportData = () => {
  showToast('Info', 'Fitur export akan segera tersedia', 'info');
};

const updateRoomCodePreview = () => {
  if (roomForm.value.building && roomForm.value.room_type) {
    // Generate preview code similar to backend logic
    const building = roomForm.value.building;
    const roomType = roomForm.value.room_type;

    // Use SP (Study Program) code - default to SPS
    const spCode = 'SPS';

    // Extract building code from building name
    const words = building.toUpperCase().split(' ');

    // Filter out common faculty/building indicators and get main building name
    const filteredWords = words.filter(word =>
      !['FAKULTAS', 'FK', 'SEKOLAH', 'SCHOOL', 'FACULTY'].includes(word)
    );

    const buildingCode = filteredWords.length > 0 ? filteredWords[0].substring(0, 3) : 'GEN';

    // Room type code
    const roomTypeCodes = {
      'classroom': 'KLS',
      'laboratory': 'LAB',
      'seminar_room': 'SMR',
      'auditorium': 'AUD',
      'workshop': 'WSH',
      'library': 'LIB',
      'office': 'OFC',
      'meeting_room': 'MTR',
      'multipurpose': 'MPL'
    };

    const roomTypeCode = roomTypeCodes[roomType] || 'RUM';

    roomCodePreview.value = `${spCode}-${buildingCode}-${roomTypeCode}01`;
  } else {
    roomCodePreview.value = '';
  }
};

const importData = () => {
  showToast('Info', 'Fitur import akan segera tersedia', 'info');
};

// Watch for menu changes
watch(activeMenu, () => {
  searchQuery.value = '';
  sortBy.value = 'name';
  activeTab.value = 'active';
  selectedItems.value = [];
  roomsPagination.value.current_page = 1;
  loadData();
});

// Watch for tab changes
watch(activeTab, () => {
  selectedItems.value = [];
  roomsPagination.value.current_page = 1;
  loadData();
});

// Watch for search and sort changes (with debounce)
const debouncedLoad = debounce(() => {
  roomsPagination.value.current_page = 1;
  loadData();
}, 500);

watch([searchQuery, sortBy], () => {
  debouncedLoad();
});

// Watch for room form changes to update preview
watch(() => roomForm.value.building, (newBuilding, oldBuilding) => {
  if (newBuilding !== oldBuilding) {
    // Reset floor when building changes
    roomForm.value.floor = 1;
  }
  updateRoomCodePreview();
});

watch(() => roomForm.value.room_type, () => {
  updateRoomCodePreview();
});

// Check for permission errors and show notifications
const checkPermissionErrors = () => {
  const permissionError = localStorage.getItem('permissionError');
  if (permissionError) {
    const errorData = JSON.parse(permissionError);
    const now = Date.now();

    // Show error if within 5 seconds
    if (now - errorData.timestamp < 5000) {
      showToast('Akses Ditolak', errorData.message, 'error');
    }

    // Clear stored error
    localStorage.removeItem('permissionError');
  }
};

// Load initial data
onMounted(() => {
  // Check for permission errors from previous requests
  checkPermissionErrors();

  loadData();
});
</script>