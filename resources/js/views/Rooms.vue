<template>
  <Layout>
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-2xl font-semibold text-gray-900">Manajemen Ruangan</h1>
        <p class="text-sm text-gray-600">Kelola data ruangan kampus</p>
      </div>
      <button @click="openCreateModal" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors flex items-center">
        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        Tambah Ruangan
      </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
      <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
        <div class="flex items-center">
          <div class="p-3 bg-blue-100 rounded-lg">
            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Total Ruangan</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.total }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
        <div class="flex items-center">
          <div class="p-3 bg-green-100 rounded-lg">
            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Aktif</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.active }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
        <div class="flex items-center">
          <div class="p-3 bg-yellow-100 rounded-lg">
            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Tidak Aktif</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.inactive }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
        <div class="flex items-center">
          <div class="p-3 bg-purple-100 rounded-lg">
            <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Gedung</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.buildings }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
          <input
            type="text"
            v-model="filters.search"
            placeholder="Cari nama atau kode ruangan..."
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
          >
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
          <select v-model="filters.status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            <option value="">Semua Status</option>
            <option value="active">Aktif</option>
            <option value="inactive">Tidak Aktif</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Gedung</label>
          <select v-model="filters.building" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            <option value="">Semua Gedung</option>
            <option value="A">Gedung A</option>
            <option value="B">Gedung B</option>
            <option value="C">Gedung C</option>
            <option value="D">Gedung D</option>
            <option value="Lab">Laboratorium</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Ruangan</label>
          <select v-model="filters.room_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            <option value="">Semua Tipe</option>
            <option value="classroom">Ruang Kelas</option>
            <option value="laboratory">Laboratorium</option>
            <option value="seminar_room">Ruang Seminar</option>
            <option value="auditorium">Auditorium</option>
            <option value="meeting_room">Ruang Rapat</option>
          </select>
        </div>
      </div>
      <div class="mt-4 flex justify-between items-center">
        <button @click="refreshData" class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
          <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          Refresh Data
        </button>
        <div class="flex space-x-2">
          <button @click="resetFilters" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
            Reset
          </button>
          <button @click="applyFilters" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
            Terapkan Filter
          </button>
        </div>
      </div>
    </div>

    <!-- Bulk Actions -->
    <div v-if="selectedItems.length > 0" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
      <div class="flex justify-between items-center">
        <div class="flex items-center">
          <span class="text-sm font-medium text-yellow-800">
            {{ selectedItems.length }} item terpilih
          </span>
          <div class="ml-4 flex space-x-2">
            <!-- Active tab actions -->
            <template v-if="activeTab === 'active'">
              <button @click="bulkDelete" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition-colors text-sm">
                Hapus
              </button>
              <button @click="bulkActivate" class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 transition-colors text-sm">
                Aktifkan
              </button>
              <button @click="bulkDeactivate" class="px-3 py-1 bg-gray-600 text-white rounded hover:bg-gray-700 transition-colors text-sm">
                Nonaktifkan
              </button>
            </template>

            <!-- Trash tab actions -->
            <template v-else>
              <button @click="bulkRestore" class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 transition-colors text-sm">
                Restore
              </button>
              <button @click="bulkDeletePermanent" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition-colors text-sm">
                Hapus Permanen
              </button>
            </template>
          <button @click="clearSelection" class="ml-4 px-3 py-1 bg-yellow-600 text-white rounded hover:bg-yellow-700 transition-colors text-sm">
            Batal Pilih
          </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Table Container with Tabs -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
      <!-- Tabs at top of table -->
      <div class="border-b border-gray-200">
        <nav class="flex -mb-px">
          <button
            @click="activeTab = 'active'"
            :class="[
              'px-6 py-3 text-sm font-medium transition-colors border-b-2',
              activeTab === 'active'
                ? 'border-green-500 text-green-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            ]"
          >
            <div class="flex items-center">
              <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
              </svg>
              Data Aktif
              <span class="ml-2 bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">
                {{ stats.active }}
              </span>
            </div>
          </button>
          <button
            @click="activeTab = 'trash'"
            :class="[
              'px-6 py-3 text-sm font-medium transition-colors border-b-2',
              activeTab === 'trash'
                ? 'border-red-500 text-red-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            ]"
          >
            <div class="flex items-center">
              <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
              Data Terhapus
              <span class="ml-2 bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-medium">
                {{ stats.trashed }}
              </span>
            </div>
          </button>
        </nav>
      </div>

      <div class="p-6 border-b border-gray-200">
        <div class="flex justify-between items-center">
          <h2 class="text-xl font-semibold text-gray-900">
            {{ activeTab === 'active' ? 'Daftar Ruangan' : 'Data Terhapus' }}
          </h2>
          <div class="flex items-center space-x-2">
            <span class="text-sm text-gray-600">Show</span>
            <select v-model="perPage" @change="fetchData" class="px-2 py-1 border border-gray-300 rounded text-sm">
              <option value="10">10</option>
              <option value="25">25</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select>
            <span class="text-sm text-gray-600">entries</span>
          </div>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr class="text-left text-gray-600 border-b border-gray-200">
              <th class="px-6 py-3 font-medium">
                <input
                  type="checkbox"
                  :checked="allSelected"
                  @change="toggleSelectAll"
                  class="rounded border-gray-300 text-green-600 focus:ring-green-500"
                >
              </th>
              <th class="px-6 py-3 font-medium">Kode</th>
              <th class="px-6 py-3 font-medium">Nama Ruangan</th>
              <th class="px-6 py-3 font-medium">Gedung</th>
              <th class="px-6 py-3 font-medium">Tipe</th>
              <th class="px-6 py-3 font-medium">Kapasitas</th>
              <th class="px-6 py-3 font-medium">Status</th>
              <th class="px-6 py-3 font-medium">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-if="rooms.length === 0" class="hover:bg-gray-50">
              <td colspan="8" class="px-6 py-12 text-center">
                <div class="flex flex-col items-center justify-center">
                  <svg class="h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                  </svg>
                  <h3 class="text-lg font-medium text-gray-900 mb-2">
                    {{ activeTab === 'trash' ? 'Tidak Ada Data Terhapus' : 'Tidak Ada Data Ruangan' }}
                  </h3>
                  <p class="text-gray-500 mb-4">
                    {{ activeTab === 'trash' ? 'Belum ada ruangan yang dihapus.' : 'Belum ada ruangan yang terdaftar dalam sistem.' }}
                  </p>
                  <button v-if="activeTab === 'active'" @click="openCreateModal" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors flex items-center">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Tambah Ruangan Pertama
                  </button>
                </div>
              </td>
            </tr>
            <tr v-else v-for="room in rooms" :key="room.id" class="hover:bg-gray-50">
              <td class="px-6 py-4">
                <input
                  type="checkbox"
                  :checked="selectedItems.includes(room.id)"
                  @change="toggleSelect(room.id)"
                  class="rounded border-gray-300 text-green-600 focus:ring-green-500"
                >
              </td>
              <td class="px-6 py-4 font-medium text-gray-900">{{ room.room_code }}</td>
              <td class="px-6 py-4 text-gray-900">{{ room.name }}</td>
              <td class="px-6 py-4 text-gray-900">{{ room.building }}</td>
              <td class="px-6 py-4">
                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                  {{ getRoomTypeLabel(room.room_type) }}
                </span>
              </td>
              <td class="px-6 py-4 text-gray-900">{{ room.capacity }} Orang</td>
              <td class="px-6 py-4">
                <!-- Slide Toggle for Status -->
                <div class="flex items-center space-x-3">
                  <label class="text-sm text-gray-700 whitespace-nowrap">Status:</label>
                  <button
                    @click="toggleStatus(room)"
                    :disabled="togglingStatus === room.id"
                    :class="[
                      'relative inline-flex h-6 w-11 items-center rounded-full transition-colors',
                      room.is_active ? 'bg-green-600' : 'bg-gray-300',
                      togglingStatus === room.id ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'
                    ]"
                  >
                    <span
                      :class="[
                        'inline-block h-4 w-4 transform rounded-full bg-white transition-transform',
                        room.is_active ? 'translate-x-6' : 'translate-x-1'
                      ]"
                    />
                  </button>
                  <span :class="room.is_active ? 'text-green-600 font-medium' : 'text-gray-500'" class="text-sm">
                    {{ room.is_active ? 'Aktif' : 'Tidak Aktif' }}
                  </span>
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="flex space-x-2">
                  <!-- Edit button -->
                  <button @click="editRoom(room)"
                          class="text-blue-600 hover:text-blue-900 p-1 rounded"
                          title="Edit">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </button>

                  <!-- Duplicate button (only for active tab) -->
                  <button v-if="activeTab === 'active'"
                          @click="duplicateRoom(room)"
                          class="text-purple-600 hover:text-purple-900 p-1 rounded"
                          :disabled="duplicatingRoom === room.id"
                          title="Duplicate">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h9m0-9V4a2 2 0 00-2-2H5a2 2 0 00-2 2v3m-3 3V4a2 2 0 012-2h9a2 2 0 012 2v3" />
                    </svg>
                  </button>

                  <!-- Delete button (only for active tab) -->
                  <button v-if="activeTab === 'active'"
                          @click="confirmDeleteRoom(room)"
                          class="text-red-600 hover:text-red-900 p-1 rounded"
                          title="Delete">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>

                  <!-- Restore button (only for trash tab) -->
                  <button v-if="activeTab === 'trash'"
                          @click="restoreRoom(room)"
                          class="text-green-600 hover:text-green-900 p-1 rounded"
                          :disabled="restoringRoom === room.id"
                          title="Restore">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6 6" />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="p-6 border-t border-gray-200">
        <div class="flex justify-between items-center">
          <div class="text-sm text-gray-700">
            Showing {{ (currentPage - 1) * perPage + 1 }} to {{ Math.min(currentPage * perPage, total) }} of {{ total }} results
          </div>
          <div class="flex space-x-1">
            <button
              @click="currentPage > 1 && changePage(currentPage - 1)"
              :disabled="currentPage <= 1"
              class="px-3 py-1 border border-gray-300 rounded-l hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Previous
            </button>
            <button
              v-for="page in visiblePages"
              :key="page"
              @click="changePage(page)"
              :class="[
                'px-3 py-1 border-t border-b',
                page === currentPage
                  ? 'bg-green-600 text-white border-green-600'
                  : 'border-gray-300 hover:bg-gray-50'
              ]"
            >
              {{ page }}
            </button>
            <button
              @click="currentPage < totalPages && changePage(currentPage + 1)"
              :disabled="currentPage >= totalPages"
              class="px-3 py-1 border border-gray-300 rounded-r hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Next
            </button>
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

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="w-full">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                  {{ editingRoom ? 'Edit Ruangan' : 'Tambah Ruangan' }}
                </h3>
                <form @submit.prevent="saveRoom">
                  <div class="space-y-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Kode Ruangan</label>
                      <input
                        type="text"
                        v-model="form.room_code"
                        required
                        :class="['w-full px-3 py-2 rounded-lg focus:outline-none focus:ring-2', formErrors.room_code ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-green-500']"
                        placeholder="Contoh: A-101"
                      >
                      <p v-if="formErrors.room_code" class="mt-1 text-sm text-red-600">
                        {{ Array.isArray(formErrors.room_code) ? formErrors.room_code[0] : formErrors.room_code }}
                      </p>
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Nama Ruangan</label>
                      <input
                        type="text"
                        v-model="form.name"
                        required
                        :class="['w-full px-3 py-2 rounded-lg focus:outline-none focus:ring-2', formErrors.name ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-green-500']"
                        placeholder="Contoh: Ruang Kelas Teknik Informatika"
                      >
                      <p v-if="formErrors.name" class="mt-1 text-sm text-red-600">
                        {{ Array.isArray(formErrors.name) ? formErrors.name[0] : formErrors.name }}
                      </p>
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Gedung</label>
                      <select v-model="form.building" required :class="['w-full px-3 py-2 rounded-lg focus:outline-none focus:ring-2', formErrors.building ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-green-500']">
                        <option value="">Pilih Gedung</option>
                        <option value="A">Gedung A</option>
                        <option value="B">Gedung B</option>
                        <option value="C">Gedung C</option>
                        <option value="D">Gedung D</option>
                        <option value="Lab">Laboratorium</option>
                      </select>
                      <p v-if="formErrors.building" class="mt-1 text-sm text-red-600">
                        {{ Array.isArray(formErrors.building) ? formErrors.building[0] : formErrors.building }}
                      </p>
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Ruangan</label>
                      <select v-model="form.room_type" required :class="['w-full px-3 py-2 rounded-lg focus:outline-none focus:ring-2', formErrors.room_type ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-green-500']">
                        <option value="">Pilih Tipe Ruangan</option>
                        <option value="classroom">Ruang Kelas</option>
                        <option value="laboratory">Laboratorium</option>
                        <option value="seminar_room">Ruang Seminar</option>
                        <option value="auditorium">Auditorium</option>
                        <option value="meeting_room">Ruang Rapat</option>
                      </select>
                      <p v-if="formErrors.room_type" class="mt-1 text-sm text-red-600">
                        {{ Array.isArray(formErrors.room_type) ? formErrors.room_type[0] : formErrors.room_type }}
                      </p>
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Lantai</label>
                      <input
                        type="number"
                        v-model="form.floor"
                        required
                        :class="['w-full px-3 py-2 rounded-lg focus:outline-none focus:ring-2', formErrors.floor ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-green-500']"
                        placeholder="Contoh: 1"
                      >
                      <p v-if="formErrors.floor" class="mt-1 text-sm text-red-600">
                        {{ Array.isArray(formErrors.floor) ? formErrors.floor[0] : formErrors.floor }}
                      </p>
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Kapasitas</label>
                      <input
                        type="number"
                        v-model="form.capacity"
                        required
                        :class="['w-full px-3 py-2 rounded-lg focus:outline-none focus:ring-2', formErrors.capacity ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-green-500']"
                        placeholder="Contoh: 30"
                      >
                      <p v-if="formErrors.capacity" class="mt-1 text-sm text-red-600">
                        {{ Array.isArray(formErrors.capacity) ? formErrors.capacity[0] : formErrors.capacity }}
                      </p>
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                      <select v-model="form.is_active" :class="['w-full px-3 py-2 rounded-lg focus:outline-none focus:ring-2', formErrors.is_active ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-green-500']">
                        <option :value="true">Aktif</option>
                        <option :value="false">Tidak Aktif</option>
                      </select>
                      <p v-if="formErrors.is_active" class="mt-1 text-sm text-red-600">
                        {{ Array.isArray(formErrors.is_active) ? formErrors.is_active[0] : formErrors.is_active }}
                      </p>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button @click="saveRoom" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
              {{ editingRoom ? 'Update' : 'Simpan' }}
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
            confirmationModal.type === 'delete' ? 'bg-red-50' : confirmationModal.type === 'warning' ? 'bg-yellow-50' : 'bg-blue-50'
          ]">
            <div class="flex items-center">
              <div :class="[
                'mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full',
                confirmationModal.type === 'delete' ? 'bg-red-100' : confirmationModal.type === 'warning' ? 'bg-yellow-100' : 'bg-blue-100'
              ]">
                <!-- Delete icon -->
                <svg v-if="confirmationModal.type === 'delete'" class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.932-3l.524-8.969c.237-.719.322-1.564.184-2.382C15.769 2.569 13.758 2 12.002 2 10.247 2 8.236 2.569 7.384 3.807c-.138.818-.053 1.663.184 2.382l.524 8.969c.57 1.333 1.393 3 1.932 3z" />
                </svg>
                <!-- Warning icon -->
                <svg v-else-if="confirmationModal.type === 'warning'" class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.932-3l.524-8.969c.237-.719.322-1.564.184-2.382C15.769 2.569 13.758 2 12.002 2 10.247 2 8.236 2.569 7.384 3.807c-.138.818-.053 1.663.184 2.382l.524 8.969c.57 1.333 1.393 3 1.932 3z" />
                </svg>
                <!-- Info icon -->
                <svg v-else class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div class="ml-4 text-left">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
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
          <div class="px-6 py-3 bg-gray-50 sm:flex sm:flex-row-reverse">
            <button
              type="button"
              @click="confirmAction"
              :class="[
                'inline-flex w-full justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm',
                confirmationModal.type === 'delete'
                  ? 'bg-red-600 hover:bg-red-700 focus:ring-red-500'
                  : confirmationModal.type === 'warning'
                    ? 'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500'
                    : 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500'
              ]"
              :disabled="confirmationModal.processing"
            >
              <svg v-if="confirmationModal.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V4a1 1 0 00-2 0v8a8 8 0 018 8v4a1 1 0 002 0v-8z"></path>
              </svg>
              {{ confirmationModal.processing ? 'Processing...' : confirmationModal.confirmButtonText }}
            </button>
            <button
              type="button"
              @click="closeConfirmationModal"
              class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
              :disabled="confirmationModal.processing"
            >
              Batal
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container fixed top-4 right-4 z-50 space-y-2">
      <Toast
        v-for="(toast, index) in toasts"
        :key="index"
        :title="toast.title"
        :description="toast.description"
        :type="toast.type"
        :duration="toast.duration"
        @close="removeToast(index)"
        ref="toastComponent"
      />
    </div>
  </Layout>
</template>

<script setup>
import { ref, computed, onMounted, ref as vueRef, watch } from 'vue';
import Layout from '@/components/Layout.vue';
import roomService from '@/services/roomService';
import Toast from '@/components/Toast.vue';

// State
const rooms = ref([]);
const loading = ref(false);
const showModal = ref(false);
const editingRoom = ref(null);
const selectedItems = ref([]);
const perPage = ref(10);
const currentPage = ref(1);
const total = ref(0);
const toasts = ref([]);
const toastComponent = ref(null);

// Tab management
const activeTab = ref('active');

// Loading states for async operations
const togglingStatus = ref(null);
const restoringRoom = ref(null);
const duplicatingRoom = ref(null);
const formErrors = ref({});

// Confirmation Modal
const confirmationModal = ref({
  show: false,
  type: 'delete', // delete, warning, info
  title: '',
  message: '',
  confirmButtonText: 'Hapus',
  details: null,
  processing: false,
  onConfirm: null
});

// Filters
const filters = ref({
  search: '',
  status: '',
  level: ''
});

// Form
const form = ref({
  room_code: '',
  name: '',
  building: '',
  floor: '',
  room_type: '',
  capacity: '',
  description: '',
  is_active: true
});

// Stats
const stats = ref({
  total: 0,
  active: 0,
  inactive: 0,
  buildings: 0,
  trashed: 0
});

// Computed
const allSelected = computed(() => {
  return rooms.value.length > 0 && rooms.value.every(room => selectedItems.value.includes(room.id));
});

const totalPages = computed(() => {
  return Math.ceil(total.value / perPage.value);
});

const visiblePages = computed(() => {
  const pages = [];
  const start = Math.max(1, currentPage.value - 2);
  const end = Math.min(totalPages.value, currentPage.value + 2);

  for (let i = start; i <= end; i++) {
    pages.push(i);
  }

  return pages;
});

// XSS Protection - Enhanced Sanitize input
const sanitizeInput = (input) => {
  if (typeof input !== 'string') return input;

  return input
    // Remove script tags and content
    .replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '')
    // Remove all HTML tags
    .replace(/<[^>]*>/g, '')
    // Remove JavaScript protocol
    .replace(/javascript:/gi, '')
    // Remove all event handlers (onclick, onload, etc.)
    .replace(/on\w+\s*=/gi, '')
    // Remove potentially harmful CSS expressions
    .replace(/expression\s*\(/gi, '')
    // Remove data and object attributes
    .replace(/data-[^=]*=['"][^'"]*['"]/gi, '')
    // Remove dangerous characters
    .replace(/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/g, '')
    // Limit length
    .substring(0, 1000)
    .trim();
};

// Sanitize form data before sending to backend
const sanitizeFormData = (data) => {
  const sanitized = {};
  const numericFields = ['capacity', 'floor', 'current_occupancy', 'area'];

  Object.keys(data).forEach(key => {
    if (numericFields.includes(key)) {
      // Keep numeric fields as numbers
      sanitized[key] = data[key];
    } else if (typeof data[key] === 'string') {
      // Sanitize string fields only
      sanitized[key] = sanitizeInput(data[key]);
    } else {
      // Keep other fields as-is
      sanitized[key] = data[key];
    }
  });
  return sanitized;
};

// Toast helper functions
const showToast = (title, description = '', type = 'info', duration = 5000) => {
  const toast = { title, description, type, duration };
  toasts.value.push(toast);

  // Auto remove after duration
  if (duration > 0) {
    setTimeout(() => {
      removeToast(toasts.value.indexOf(toast));
    }, duration);
  }
};

const removeToast = (index) => {
  if (index > -1) {
    toasts.value.splice(index, 1);
  }
};

// Methods
const fetchData = async () => {
  loading.value = true;
  try {
    // Map frontend level values to backend format
    const levelMapping = {
      'S1': 'undergraduate',
      'S2': 'graduate',
      'S3': 'doctoral'
    };

    const params = {
      search: filters.value.search,
      status: filters.value.status,
      level: levelMapping[filters.value.level] || filters.value.level,
      per_page: perPage.value,
      page: currentPage.value
    };

    let response;
    if (activeTab.value === 'trash') {
      response = await roomService.getTrash(params);
    } else {
      response = await roomService.getAll(params);
    }

    if (response.success) {
      rooms.value = response.data.data || response.data;
      total.value = response.meta?.total || rooms.value.length;

      // Load statistics
      await loadStatistics();
    } else {
      throw new Error(response.message || 'Failed to fetch data');
    }
  } catch (error) {
    rooms.value = [];
    total.value = 0;

    // Handle different error types
    if (error.errorType === 'PERMISSION_ERROR') {
      // Show permission error with helpful message
      showPermissionError();
    } else if (error.errorType === 'VALIDATION_ERROR') {
      showToast('Validasi Gagal', error.userMessage, 'error');
    } else {
      // General error
      showToast('Gagal Memuat Data', error.userMessage, 'error');
    }
  } finally {
    loading.value = false;
  }
};

const showPermissionError = () => {
  const shouldShowPermissionMessage = localStorage.getItem('permission_error_shown');
  if (!shouldShowPermissionMessage) {
    localStorage.setItem('permission_error_shown', 'true');
    showToast(
      'Akses Ditolak',
      'Anda tidak memiliki izin untuk mengakses halaman Ruangan. Silakan hubungi administrator sistem.',
      'error',
      8000
    );
  }
};


const loadStatistics = async () => {
  try {
    const response = await roomService.getStatistics();
    if (response.success) {
      stats.value = {
        total: response.data.total_rooms || 0,
        active: response.data.active_rooms || 0,
        inactive: response.data.inactive_rooms || 0,
        trashed: response.data.trashed_rooms || 0,
        buildings: response.data.buildings || 1
      };
    }
  } catch (error) {
    // Don't show error for statistics, just set defaults
    stats.value = {
      total: 0,
      active: 0,
      inactive: 0,
      trashed: 0,
      faculties: 1 // Sekolah Pascasarjana
    };
  }
};

const openCreateModal = () => {
  editingRoom.value = null;
  form.value = {
    room_code: '',
    name: '',
    building: '',
    floor: 1,
    room_type: '',
    capacity: 1,
    department: '',
    faculty: '',
    is_active: true
  };
  showModal.value = true;
};

const editRoom = (room) => {
  editingRoom.value = room;

  form.value = {
    id: room.id,
    room_code: room.room_code,
    name: room.name,
    building: room.building,
    floor: room.floor,
    room_type: room.room_type,
    capacity: room.capacity,
    department: room.department || '',
    faculty: room.faculty || '',
    is_active: room.is_active !== undefined ? room.is_active : (room.status === 'active')
  };
  showModal.value = true;
};

const saveRoom = async () => {
  // Clear previous errors
  formErrors.value = {};

  // Frontend validation
  if (!form.value.room_code.trim()) {
    formErrors.value.room_code = ['Kode ruangan wajib diisi'];
    return;
  }

  if (!form.value.name.trim()) {
    formErrors.value.name = ['Nama ruangan wajib diisi'];
    return;
  }

  if (!form.value.building) {
    formErrors.value.building = ['Gedung wajib dipilih'];
    return;
  }

  if (!form.value.room_type) {
    formErrors.value.room_type = ['Tipe ruangan wajib dipilih'];
    return;
  }

  // Validate capacity (must be positive integer)
  const capacity = parseInt(form.value.capacity);
  if (isNaN(capacity) || capacity < 1) {
    formErrors.value.capacity = ['Kapasitas harus berupa angka dan minimal 1'];
    return;
  }

  // Validate floor (must be positive integer)
  const floor = parseInt(form.value.floor);
  if (isNaN(floor) || floor < 1) {
    formErrors.value.floor = ['Lantai harus berupa angka dan minimal 1'];
    return;
  }

  // Validate optional numeric fields
  if (form.value.current_occupancy) {
    const currentOccupancy = parseInt(form.value.current_occupancy);
    if (isNaN(currentOccupancy) || currentOccupancy < 0) {
      formErrors.value.current_occupancy = ['Occupancy harus berupa angka dan minimal 0'];
      return;
    }
  }

  if (form.value.area) {
    const area = parseFloat(form.value.area);
    if (isNaN(area) || area < 1) {
      formErrors.value.area = ['Luas harus berupa angka dan minimal 1'];
      return;
    }
  }

  try {
    // Sanitize form data before sending
    const sanitizedData = sanitizeFormData(form.value);

    // Use validated numeric values
    sanitizedData.capacity = capacity;
    sanitizedData.floor = floor;

    // Optional numeric fields (if present)
    if (form.value.current_occupancy) {
      sanitizedData.current_occupancy = parseInt(form.value.current_occupancy);
    }
    if (form.value.area) {
      sanitizedData.area = parseFloat(form.value.area);
    }

    if (editingRoom.value) {
      // Update existing program
      const response = await roomService.update(editingRoom.value.id, sanitizedData);
      if (response.success) {
        showToast('Berhasil', 'Ruangan berhasil diperbarui!', 'success');
        closeModal();
        fetchData();
      } else {
        throw new Error(response.message || 'Failed to update room');
      }
    } else {
      // Create new room
      const response = await roomService.create(sanitizedData);
      if (response.success) {
        showToast('Berhasil', 'Ruangan berhasil ditambahkan!', 'success');
        closeModal();
        fetchData();
      } else {
        throw new Error(response.message || 'Failed to create room');
      }
    }
  } catch (error) {
    
    if (error.errorType === 'PERMISSION_ERROR') {
      showToast('Akses Ditolak', error.userMessage, 'error');
    } else if (error.errorType === 'VALIDATION_ERROR') {
      // Set form errors for field-specific error display
      formErrors.value = error.validationErrors || {};

      // Show general toast message
      const errorMessages = Object.values(error.validationErrors || {}).flat();
      showToast('Validasi Gagal', errorMessages.join(', '), 'error');
    } else {
      showToast('Gagal Menyimpan', error.userMessage, 'error');
    }
  }
};

const confirmDeleteRoom = async (room) => {
  showConfirmationModal(
    'delete',
    'Konfirmasi Hapus',
    `Apakah Anda yakin ingin menghapus ruangan "${sanitizeInput(room.name)}"? Data yang dihapus dapat dipulihkan kembali.`,
    `${sanitizeInput(room.room_code)} - ${sanitizeInput(room.name)}`,
    'Hapus',
    async () => {
      const response = await roomService.delete(room.id);
      if (response.success) {
        showToast('Berhasil', 'Ruangan berhasil dihapus!', 'success');
        fetchData();
      } else {
        throw new Error(response.message || 'Failed to delete program');
      }
    }
  );
};

const deleteRoom = async (id) => {
  try {
    const response = await roomService.delete(id);
    if (response.success) {
      showToast('Berhasil', 'Ruangan berhasil dihapus!', 'success');
      fetchData();
    } else {
      throw new Error(response.message || 'Failed to delete program');
    }
  } catch (error) {
        showToast('Gagal Menghapus', error.userMessage || error.message, 'error');
  }
};

const closeModal = () => {
  showModal.value = false;
  editingRoom.value = null;
  formErrors.value = {};
};

const toggleSelect = (id) => {
  const index = selectedItems.value.indexOf(id);
  if (index > -1) {
    selectedItems.value.splice(index, 1);
  } else {
    selectedItems.value.push(id);
  }
};

const toggleSelectAll = () => {
  if (allSelected.value) {
    selectedItems.value = [];
  } else {
    selectedItems.value = rooms.value.map(p => p.id);
  }
};

const clearSelection = () => {
  selectedItems.value = [];
};

const bulkDelete = async () => {
  showConfirmationModal(
    'delete',
    'Konfirmasi Hapus Massal',
    `Apakah Anda yakin ingin menghapus ${selectedItems.value.length} ruangan yang dipilih? Data yang dihapus dapat dipulihkan kembali.`,
    `${selectedItems.value.length} ruangan`,
    'Hapus Semua',
    async () => {
      const response = await roomService.bulkDelete({
        room_ids: selectedItems.value
      });
      if (response.success) {
        showToast('Berhasil', `${selectedItems.value.length} ruangan berhasil dihapus!`, 'success');
        clearSelection();
        fetchData();
      } else {
        throw new Error(response.message || 'Failed to bulk delete rooms');
      }
    }
  );
};

const bulkActivate = async () => {
  try {
    const response = await roomService.bulkToggleStatus({
      room_ids: selectedItems.value,
      is_active: true
    });
    if (response.success) {
      showToast('Berhasil', `${selectedItems.value.length} ruangan berhasil diaktifkan!`, 'success');
      clearSelection();
      fetchData();
    } else {
      throw new Error(response.message || 'Failed to bulk activate programs');
    }
  } catch (error) {
      showToast('Gagal Mengaktifkan', error.userMessage || error.message || error.response?.data?.message || 'Terjadi kesalahan', 'error');
  }
};

const bulkDeactivate = async () => {
  try {
    const response = await roomService.bulkToggleStatus({
      room_ids: selectedItems.value,
      is_active: false
    });
    if (response.success) {
      showToast('Berhasil', `${selectedItems.value.length} ruangan berhasil dinonaktifkan!`, 'success');
      clearSelection();
      fetchData();
    } else {
      throw new Error(response.message || 'Failed to bulk deactivate programs');
    }
  } catch (error) {
        showToast('Gagal Menonaktifkan', error.userMessage || error.message, 'error');
  }
};

const resetFilters = () => {
  filters.value = {
    search: '',
    status: '',
    level: ''
  };
  currentPage.value = 1;
  fetchData();
};

const applyFilters = () => {
  currentPage.value = 1;
  fetchData();
};

// Watch filters for auto-filtering
watch([() => filters.value.search, () => filters.value.status, () => filters.value.level], () => {
  currentPage.value = 1;
  fetchData();
}, { deep: true });

const changePage = (page) => {
  currentPage.value = page;
  fetchData();
};

// Helper function to format level for display
const getRoomTypeLabel = (roomType) => {
  const roomTypeMapping = {
    'classroom': 'Ruang Kelas',
    'laboratory': 'Laboratorium',
    'seminar_room': 'Ruang Seminar',
    'auditorium': 'Auditorium',
    'meeting_room': 'Ruang Rapat',
    'workshop': 'Workshop',
    'library': 'Perpustakaan',
    'office': 'Kantor',
    'multipurpose': 'Multi Purpose'
  };

  return roomTypeMapping[roomType] || roomType;
};


// Watch for tab changes
watch(activeTab, () => {
  clearSelection();
  fetchData();
});

// Confirmation Modal Methods
const showConfirmationModal = (type, title, message, details = null, confirmButtonText = 'Ya', onConfirm = null) => {
  confirmationModal.value = {
    show: true,
    type,
    title,
    message,
    confirmButtonText,
    details,
    processing: false,
    onConfirm
  };
};

const closeConfirmationModal = () => {
  confirmationModal.value.show = false;
  confirmationModal.value.processing = false;
  confirmationModal.value.onConfirm = null;
};

const confirmAction = async () => {
  if (confirmationModal.value.onConfirm && !confirmationModal.value.processing) {
    confirmationModal.value.processing = true;
    try {
      await confirmationModal.value.onConfirm();
      closeConfirmationModal();
    } catch (error) {
            confirmationModal.value.processing = false;
    }
  }
};

// New Methods for enhanced functionality
const toggleStatus = async (room) => {
  try {
    togglingStatus.value = room.id;

    const response = await roomService.toggleStatus(room.id, !room.is_active);

    if (response.success) {
      // Update room data in the local array
      const index = rooms.value.findIndex(r => r.id === room.id);
      if (index !== -1) {
        rooms.value[index] = { ...rooms.value[index], ...response.data };
      }

      showToast('Berhasil', `Status ${response.data.name} berhasil diubah menjadi ${response.data.is_active ? 'Aktif' : 'Tidak Aktif'}`, 'success');
    } else {
      throw new Error(response.message || 'Failed to toggle status');
    }
  } catch (error) {
    showToast('Gagal Mengubah Status', error.userMessage || error.message || error.response?.data?.message || 'Terjadi kesalahan', 'error');
  } finally {
    togglingStatus.value = null;
  }
};

const duplicateRoom = async (room) => {
  showConfirmationModal(
    'info',
    'Konfirmasi Duplikasi',
    `Apakah Anda yakin ingin menduplikasi ruangan "${room.name}"?`,
    `${room.room_code} - ${room.name}`,
    'Duplikasi',
    async () => {
      duplicatingRoom.value = room.id;

      const response = await roomService.duplicate(room.id);

      if (response.success) {
        showToast('Berhasil', `Ruangan "${response.data.name}" berhasil diduplikasi!`, 'success');
        fetchData();
      } else {
        throw new Error(response.message || 'Failed to duplicate room');
      }
    }
  );
};

const restoreRoom = async (room) => {
  showConfirmationModal(
    'info',
    'Konfirmasi Pemulihan',
    `Apakah Anda yakin ingin memulihkan ruangan "${room.name}"?`,
    `${room.room_code} - ${room.name}`,
    'Pulihkan',
    async () => {
      restoringRoom.value = room.id;

      const response = await roomService.restore(room.id);

      if (response.success) {
        showToast('Berhasil', `Ruangan "${response.data.name}" berhasil dipulihkan!`, 'success');
        fetchData();
      } else {
        throw new Error(response.message || 'Failed to restore room');
      }
    }
  );
};

// Bulk Restore
const bulkRestore = async () => {
  showConfirmationModal(
    'info',
    'Konfirmasi Pemulihan Massal',
    `Apakah Anda yakin ingin memulihkan ${selectedItems.value.length} ruangan yang dipilih?`,
    `${selectedItems.value.length} ruangan`,
    'Pulihkan Semua',
    async () => {
      const promises = selectedItems.value.map(id =>
        roomService.restore(id)
      );

      const results = await Promise.allSettled(promises);
      const successful = results.filter(r => r.status === 'fulfilled' && r.value.success).length;

      if (successful > 0) {
        showToast('Berhasil', `${successful} ruangan berhasil dipulihkan!`, 'success');
        clearSelection();
        fetchData();
      } else {
        showToast('Gagal', 'Tidak ada ruangan yang berhasil dipulihkan', 'error');
      }
    }
  );
};

// Bulk Delete Permanent
const bulkDeletePermanent = async () => {
  showConfirmationModal(
    'delete',
    'Konfirmasi Hapus Permanen',
    `⚠️ PERINGATAN: Apakah Anda yakin ingin menghapus permanen ${selectedItems.value.length} ruangan? Tindakan ini tidak dapat dibatalkan!`,
    `${selectedItems.value.length} ruangan akan dihapus permanen`,
    'Hapus Permanen',
    async () => {
      const promises = selectedItems.value.map(id =>
        roomService.forceDelete(id).catch(error => {
                    return { success: false, error: error.message || 'Failed to force delete' };
        })
      );

      const results = await Promise.allSettled(promises);
      const successful = results.filter(r => r.status === 'fulfilled' && r.value.success).length;

      if (successful > 0) {
        showToast('Berhasil', `${successful} ruangan berhasil dihapus permanen!`, 'success');
        clearSelection();
        fetchData();
      } else {
        showToast('Gagal', 'Tidak ada ruangan yang berhasil dihapus permanen', 'error');
      }
    }
  );
};

// Refresh Data
const refreshData = async () => {
  try {
    clearSelection();
    await fetchData();
    showToast('Data Segar', 'Data berhasil dimuat ulang!', 'success', 3000);
  } catch (error) {
    showToast('Gagal Refresh', error.userMessage || error.message, 'error');
  }
};

onMounted(() => {
  fetchData();
});
</script>