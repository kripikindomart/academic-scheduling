<template>
  <Layout>
    <div class="min-h-screen bg-gray-50 pb-8">
      <!-- Header -->
      <div class="px-4 py-6 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-semibold text-gray-900">Program Studi Pascasarjana</h1>
        <p class="text-sm text-gray-600">Kelola data program studi S2 dan S3</p>
      </div>
      <button @click="openCreateModal" class="flex items-center px-4 py-2 text-white transition-colors bg-green-600 rounded-lg hover:bg-green-700">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        Tambah Program Studi
      </button>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-4">
      <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
        <div class="flex items-center">
          <div class="p-3 bg-blue-100 rounded-lg">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Total Program Studi</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.total }}</p>
          </div>
        </div>
      </div>
      <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
        <div class="flex items-center">
          <div class="p-3 bg-green-100 rounded-lg">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Aktif</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.active }}</p>
          </div>
        </div>
      </div>
      <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
        <div class="flex items-center">
          <div class="p-3 bg-yellow-100 rounded-lg">
            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Tidak Aktif</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.inactive }}</p>
          </div>
        </div>
      </div>
      <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
        <div class="flex items-center">
          <div class="p-3 bg-purple-100 rounded-lg">
            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Fakultas</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.faculties }}</p>
          </div>
        </div>
      </div>
        </div>
      </div>

      <!-- Filters and Search -->
      <div class="px-4 sm:px-6 lg:px-8">
        <div class="p-6 mb-6 bg-white border border-gray-200 rounded-lg shadow-sm">
      <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div>
          <label class="block mb-2 text-sm font-medium text-gray-700">Search</label>
          <input
            type="text"
            v-model="filters.search"
            placeholder="Cari nama program studi..."
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
          >
        </div>
        <div>
          <label class="block mb-2 text-sm font-medium text-gray-700">Status</label>
          <select v-model="filters.status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            <option value="">Semua Status</option>
            <option value="active">Aktif</option>
            <option value="inactive">Tidak Aktif</option>
          </select>
        </div>
        <div>
          <label class="block mb-2 text-sm font-medium text-gray-700">Jenjang</label>
          <select v-model="filters.level" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            <option value="">Semua Jenjang</option>
            <option value="S2">S2 - Magister</option>
            <option value="S3">S3 - Doktor</option>
          </select>
        </div>
      </div>
      <div class="flex items-center justify-between mt-4">
        <button @click="refreshData" class="flex items-center px-4 py-2 text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          Refresh Data
        </button>
        <div class="flex space-x-2">
          <button @click="resetFilters" class="px-4 py-2 text-gray-700 transition-colors border border-gray-300 rounded-lg hover:bg-gray-50">
            Reset
          </button>
          <button @click="applyFilters" class="px-4 py-2 text-white transition-colors bg-green-600 rounded-lg hover:bg-green-700">
            Terapkan Filter
          </button>
        </div>
      </div>
    </div>

    <!-- Bulk Actions -->
    <div v-if="selectedItems.length > 0" class="p-4 mb-6 border border-yellow-200 rounded-lg bg-yellow-50">
      <div class="flex items-center justify-between">
        <div class="flex items-center">
          <span class="text-sm font-medium text-yellow-800">
            {{ selectedItems.length }} item terpilih
          </span>
          <div class="flex ml-4 space-x-2">
            <!-- Active tab actions -->
            <template v-if="activeTab === 'active'">
              <button @click="bulkDelete" class="px-3 py-1 text-sm text-white transition-colors bg-red-600 rounded hover:bg-red-700">
                Hapus
              </button>
              <button @click="bulkActivate" class="px-3 py-1 text-sm text-white transition-colors bg-green-600 rounded hover:bg-green-700">
                Aktifkan
              </button>
              <button @click="bulkDeactivate" class="px-3 py-1 text-sm text-white transition-colors bg-gray-600 rounded hover:bg-gray-700">
                Nonaktifkan
              </button>
            </template>

            <!-- Trash tab actions -->
            <template v-else>
              <button @click="bulkRestore" class="px-3 py-1 text-sm text-white transition-colors bg-green-600 rounded hover:bg-green-700">
                Restore
              </button>
              <button @click="bulkDeletePermanent" class="px-3 py-1 text-sm text-white transition-colors bg-red-600 rounded hover:bg-red-700">
                Hapus Permanen
              </button>
            </template>
          <button @click="clearSelection" class="px-3 py-1 ml-4 text-sm text-white transition-colors bg-yellow-600 rounded hover:bg-yellow-700">
            Batal Pilih
          </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Table Container with Tabs -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
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
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
              </svg>
              Data Aktif
              <span class="px-2 py-1 ml-2 text-xs font-medium text-blue-800 bg-blue-100 rounded-full">
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
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
              Data Terhapus
              <span class="px-2 py-1 ml-2 text-xs font-medium text-red-800 bg-red-100 rounded-full">
                {{ stats.trashed }}
              </span>
            </div>
          </button>
        </nav>
      </div>

      <div class="p-6 border-b border-gray-200">
        <div class="flex items-center justify-between">
          <h2 class="text-xl font-semibold text-gray-900">
            {{ activeTab === 'active' ? 'Daftar Program Studi' : 'Data Terhapus' }}
          </h2>
          <div class="flex items-center space-x-2">
            <span class="text-sm text-gray-600">Show</span>
            <select v-model="perPage" @change="fetchData" class="px-2 py-1 text-sm border border-gray-300 rounded">
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
                  class="text-green-600 border-gray-300 rounded focus:ring-green-500"
                >
              </th>
              <th class="px-6 py-3 font-medium">Kode</th>
              <th class="px-6 py-3 font-medium">Nama Program Studi</th>
              <th class="px-6 py-3 font-medium">Jenjang</th>
              <th class="px-6 py-3 font-medium">Ketua Program Studi</th>
              <th class="px-6 py-3 font-medium">Status</th>
              <th class="px-6 py-3 font-medium">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-if="programs.length === 0" class="hover:bg-gray-50">
              <td colspan="7" class="px-6 py-12 text-center">
                <div class="flex flex-col items-center justify-center">
                  <svg class="w-12 h-12 mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                  </svg>
                  <h3 class="mb-2 text-lg font-medium text-gray-900">
                    {{ activeTab === 'trash' ? 'Tidak Ada Data Terhapus' : 'Tidak Ada Data Program Studi' }}
                  </h3>
                  <p class="mb-4 text-gray-500">
                    {{ activeTab === 'trash' ? 'Belum ada program studi yang dihapus.' : 'Belum ada program studi yang terdaftar dalam sistem.' }}
                  </p>
                  <button v-if="activeTab === 'active'" @click="openCreateModal" class="flex items-center px-4 py-2 text-white transition-colors bg-green-600 rounded-lg hover:bg-green-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Tambah Program Studi Pertama
                  </button>
                </div>
              </td>
            </tr>
            <tr v-else v-for="program in programs" :key="program.id" class="hover:bg-gray-50">
              <td class="px-6 py-4">
                <input
                  type="checkbox"
                  :checked="selectedItems.includes(program.id)"
                  @change="toggleSelect(program.id)"
                  class="text-green-600 border-gray-300 rounded focus:ring-green-500"
                >
              </td>
              <td class="px-6 py-4 font-medium text-gray-900">{{ program.code }}</td>
              <td class="px-6 py-4 text-gray-900">{{ program.name }}</td>
              <td class="px-6 py-4">
                <span class="px-2 py-1 text-xs font-medium text-blue-800 bg-blue-100 rounded-full">
                  {{ formatLevelForDisplay(program.level) }}
                </span>
              </td>
              <td class="px-6 py-4 text-gray-900">{{ program.head_of_program }}</td>
              <td class="px-6 py-4">
                <!-- Slide Toggle for Status -->
                <div class="flex items-center space-x-3">
                  <label class="text-sm text-gray-700 whitespace-nowrap">Status:</label>
                  <button
                    @click="toggleStatus(program)"
                    :disabled="togglingStatus === program.id"
                    :class="[
                      'relative inline-flex h-6 w-11 items-center rounded-full transition-colors',
                      program.is_active ? 'bg-green-600' : 'bg-gray-300',
                      togglingStatus === program.id ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'
                    ]"
                  >
                    <span
                      :class="[
                        'inline-block h-4 w-4 transform rounded-full bg-white transition-transform',
                        program.is_active ? 'translate-x-6' : 'translate-x-1'
                      ]"
                    />
                  </button>
                  <span :class="program.is_active ? 'text-green-600 font-medium' : 'text-gray-500'" class="text-sm">
                    {{ program.is_active ? 'Aktif' : 'Tidak Aktif' }}
                  </span>
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="flex space-x-2">
                  <!-- Edit button (only for active tab) -->
                  <button v-if="activeTab === 'active'"
                          @click="editProgram(program)"
                          class="p-1 text-blue-600 rounded hover:text-blue-900"
                          title="Edit">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </button>

                  <!-- Duplicate button (only for active tab) -->
                  <button v-if="activeTab === 'active'"
                          @click="duplicateProgram(program)"
                          class="p-1 text-purple-600 rounded hover:text-purple-900"
                          :disabled="duplicatingProgram === program.id"
                          title="Duplicate">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h9m0-9V4a2 2 0 00-2-2H5a2 2 0 00-2 2v3m-3 3V4a2 2 0 012-2h9a2 2 0 012 2v3" />
                    </svg>
                  </button>

                  <!-- Delete button (only for active tab) -->
                  <button v-if="activeTab === 'active'"
                          @click="confirmDeleteProgram(program)"
                          class="p-1 text-red-600 rounded hover:text-red-900"
                          title="Delete">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>

                  <!-- Restore button (only for trash tab) -->
                  <button v-if="activeTab === 'trash'"
                          @click="restoreProgram(program)"
                          class="p-1 text-green-600 rounded hover:text-green-900"
                          :disabled="restoringProgram === program.id"
                          title="Restore">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6 6" />
                    </svg>
                  </button>

                  <!-- Delete Permanent button (only for trash tab) -->
                  <button v-if="activeTab === 'trash'"
                          @click="confirmDeletePermanent(program)"
                          class="p-1 text-red-600 rounded hover:text-red-900"
                          title="Delete Permanently">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
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
        <div class="flex items-center justify-between">
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
      <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" @click="closeModal">
          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="w-full">
                <h3 class="mb-4 text-lg font-medium leading-6 text-gray-900">
                  {{ editingProgram ? 'Edit Program Studi' : 'Tambah Program Studi' }}
                </h3>
                <form @submit.prevent="saveProgram">
                  <div class="space-y-4">
                    <div>
                      <label class="block mb-1 text-sm font-medium text-gray-700">Kode Program Studi</label>
                      <input
                        type="text"
                        v-model="form.code"
                        required
                        :class="['w-full px-3 py-2 rounded-lg focus:outline-none focus:ring-2', formErrors.code ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-green-500']"
                        placeholder="Contoh: TI"
                      >
                      <p v-if="formErrors.code" class="mt-1 text-sm text-red-600">
                        {{ Array.isArray(formErrors.code) ? formErrors.code[0] : formErrors.code }}
                      </p>
                    </div>
                    <div>
                      <label class="block mb-1 text-sm font-medium text-gray-700">Nama Program Studi</label>
                      <input
                        type="text"
                        v-model="form.name"
                        required
                        :class="['w-full px-3 py-2 rounded-lg focus:outline-none focus:ring-2', formErrors.name ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-green-500']"
                        placeholder="Contoh: Teknik Informatika"
                      >
                      <p v-if="formErrors.name" class="mt-1 text-sm text-red-600">
                        {{ Array.isArray(formErrors.name) ? formErrors.name[0] : formErrors.name }}
                      </p>
                    </div>
                    <div>
                      <label class="block mb-1 text-sm font-medium text-gray-700">Jenjang</label>
                      <select v-model="form.level" required :class="['w-full px-3 py-2 rounded-lg focus:outline-none focus:ring-2', formErrors.level ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-green-500']">
                        <option value="">Pilih Jenjang</option>
                        <option value="S2">S2 - Magister</option>
                        <option value="S3">S3 - Doktor</option>
                      </select>
                      <p v-if="formErrors.level" class="mt-1 text-sm text-red-600">
                        {{ Array.isArray(formErrors.level) ? formErrors.level[0] : formErrors.level }}
                      </p>
                    </div>
                    <div>
                      <label class="block mb-1 text-sm font-medium text-gray-700">Ketua Program Studi</label>
                      <input
                        type="text"
                        v-model="form.head_of_program"
                        required
                        :class="['w-full px-3 py-2 rounded-lg focus:outline-none focus:ring-2', formErrors.head_of_program ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-green-500']"
                        placeholder="Nama Ketua Program Studi"
                      >
                      <p v-if="formErrors.head_of_program" class="mt-1 text-sm text-red-600">
                        {{ Array.isArray(formErrors.head_of_program) ? formErrors.head_of_program[0] : formErrors.head_of_program }}
                      </p>
                    </div>
                    <div>
                      <label class="block mb-1 text-sm font-medium text-gray-700">Status</label>
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
          <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
            <button @click="saveProgram" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
              {{ editingProgram ? 'Update' : 'Simpan' }}
            </button>
            <button @click="closeModal" type="button" class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
              Batal
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Confirmation Modal -->
    <div v-if="confirmationModal.show" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="closeConfirmationModal"></div>

        <!-- Modal panel -->
        <div class="relative inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
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
                <svg v-if="confirmationModal.type === 'delete'" class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.932-3l.524-8.969c.237-.719.322-1.564.184-2.382C15.769 2.569 13.758 2 12.002 2 10.247 2 8.236 2.569 7.384 3.807c-.138.818-.053 1.663.184 2.382l.524 8.969c.57 1.333 1.393 3 1.932 3z" />
                </svg>
                <!-- Warning icon -->
                <svg v-else-if="confirmationModal.type === 'warning'" class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.932-3l.524-8.969c.237-.719.322-1.564.184-2.382C15.769 2.569 13.758 2 12.002 2 10.247 2 8.236 2.569 7.384 3.807c-.138.818-.053 1.663.184 2.382l.524 8.969c.57 1.333 1.393 3 1.932 3z" />
                </svg>
                <!-- Info icon -->
                <svg v-else class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div class="ml-4 text-left">
                <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
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
            <div class="p-3 border border-gray-200 rounded-lg bg-gray-50">
              <div class="flex items-center justify-between text-sm">
                <span class="font-medium text-gray-700">Item yang akan dihapus:</span>
                <span class="text-gray-600">{{ confirmationModal.details }}</span>
              </div>
            </div>
          </div>

          <!-- Modal actions -->
          <div class="px-4 px-6 py-3 bg-gray-50 sm:flex sm:flex-row-reverse">
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
              <svg v-if="confirmationModal.processing" class="w-5 h-5 mr-3 -ml-1 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V4a1 1 0 00-2 0v8a8 8 0 018 8v4a1 1 0 002 0v-8z"></path>
              </svg>
              {{ confirmationModal.processing ? 'Processing...' : confirmationModal.confirmButtonText }}
            </button>
            <button
              type="button"
              @click="closeConfirmationModal"
              class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
              :disabled="confirmationModal.processing"
            >
              Batal
            </button>
          </div>
        </div>
      </div>
    </div>

        </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref, computed, onMounted, ref as vueRef, watch } from 'vue';
import Layout from '@/components/Layout.vue';
import programStudyService from '@/services/programStudyService';
import { useToastStore } from '../stores/toast';

// State
const programs = ref([]);
const loading = ref(false);
const showModal = ref(false);
const editingProgram = ref(null);
const selectedItems = ref([]);
const perPage = ref(10);
const currentPage = ref(1);
const total = ref(0);

// Global toast store
const toastStore = useToastStore();

// Tab management
const activeTab = ref('active');

// Loading states for async operations
const togglingStatus = ref(null);
const restoringProgram = ref(null);
const duplicatingProgram = ref(null);
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
  code: '',
  name: '',
  level: '',
  head_of_program: '',
  is_active: true
});

// Stats
const stats = ref({
  total: 0,
  active: 0,
  inactive: 0,
  faculties: 0
});

// Computed
const allSelected = computed(() => {
  return programs.value.length > 0 && programs.value.every(program => selectedItems.value.includes(program.id));
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

// XSS Protection - Sanitize input
const sanitizeInput = (input) => {
  if (typeof input !== 'string') return input;

  // Remove HTML tags and potential script content
  return input
    .replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '') // Remove script tags
    .replace(/<[^>]*>/g, '') // Remove HTML tags
    .replace(/javascript:/gi, '') // Remove javascript: protocol
    .replace(/on\w+\s*=/gi, '') // Remove event handlers
    .trim();
};

// Sanitize form data before sending to backend
const sanitizeFormData = (data) => {
  const sanitized = {};
  Object.keys(data).forEach(key => {
    if (typeof data[key] === 'string') {
      sanitized[key] = sanitizeInput(data[key]);
    } else {
      sanitized[key] = data[key];
    }
  });
  return sanitized;
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
      response = await programStudyService.getTrash(params);
    } else {
      response = await programStudyService.getAll(params);
    }

    if (response.success) {
      programs.value = response.data.data || response.data;
      total.value = response.meta?.total || programs.value.length;

      // Load statistics
      await loadStatistics();
    } else {
      throw new Error(response.message || 'Failed to fetch data');
    }
  } catch (error) {
    programs.value = [];
    total.value = 0;

    // Handle different error types
    if (error.errorType === 'PERMISSION_ERROR') {
      // Show permission error with helpful message
      showPermissionError();
    } else if (error.errorType === 'VALIDATION_ERROR') {
      toastStore.error('Validasi Gagal', error.userMessage);
    } else {
      // General error
      toastStore.error('Gagal Memuat Data', error.userMessage);
    }
  } finally {
    loading.value = false;
  }
};

const showPermissionError = () => {
  const shouldShowPermissionMessage = localStorage.getItem('permission_error_shown');
  if (!shouldShowPermissionMessage) {
    localStorage.setItem('permission_error_shown', 'true');
    toastStore(
      'Akses Ditolak',
      'Anda tidak memiliki izin untuk mengakses halaman Program Studi. Silakan hubungi administrator sistem.',
      'error',
      8000
    );
  }
};


const loadStatistics = async () => {
  try {
    const response = await programStudyService.getStatistics();
    if (response.success) {
      stats.value = {
        total: response.data.total_programs || 0,
        active: response.data.active_programs || 0,
        inactive: response.data.inactive_programs || 0,
        trashed: response.data.trashed_programs || 0,
        faculties: response.data.faculties || 1
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
  editingProgram.value = null;
  form.value = {
    code: '',
    name: '',
    faculty: 'Sekolah Pascasarjana',
    level: '',
    head_of_program: '',
    is_active: true
  };
  showModal.value = true;
};

const editProgram = (program) => {
  editingProgram.value = program;

  // Map backend level format to frontend format
  const levelMapping = {
    'undergraduate': 'S1',
    'graduate': 'S2',
    'doctoral': 'S3'
  };

  const mappedLevel = levelMapping[program.level] || program.level;

  form.value = {
    id: program.id,
    code: program.code,
    name: program.name,
    faculty: program.faculty,
    level: mappedLevel, // Use mapped level for frontend
    head_of_program: program.head_of_program,
    is_active: program.is_active !== undefined ? program.is_active : (program.status === 'active')
  };
  showModal.value = true;
};

const saveProgram = async () => {
  // Clear previous errors
  formErrors.value = {};

  try {
    // Sanitize form data before sending
    const sanitizedData = sanitizeFormData(form.value);

    if (editingProgram.value) {
      // Update existing program
      const response = await programStudyService.update(editingProgram.value.id, sanitizedData);
      if (response.success) {
        toastStore.success('Berhasil', 'Program studi berhasil diperbarui!');
        closeModal();
        fetchData();
      } else {
        throw new Error(response.message || 'Failed to update program');
      }
    } else {
      // Create new program
      const response = await programStudyService.create(sanitizedData);
      if (response.success) {
        toastStore.success('Berhasil', 'Program studi berhasil ditambahkan!');
        closeModal();
        fetchData();
      } else {
        throw new Error(response.message || 'Failed to create program');
      }
    }
  } catch (error) {
    
    if (error.errorType === 'PERMISSION_ERROR') {
      toastStore('Akses Ditolak', error.userMessage, 'error');
    } else if (error.errorType === 'VALIDATION_ERROR') {
      // Set form errors for field-specific error display
      formErrors.value = error.validationErrors || {};

      // Show general toast message
      const errorMessages = Object.values(error.validationErrors || {}).flat();
      toastStore.error('Validasi Gagal', errorMessages.join(', '));
    } else {
      toastStore('Gagal Menyimpan', error.userMessage, 'error');
    }
  }
};

const confirmDeleteProgram = async (program) => {
  showConfirmationModal(
    'delete',
    'Konfirmasi Hapus',
    `Apakah Anda yakin ingin menghapus program studi "${sanitizeInput(program.name)}"? Data yang dihapus dapat dipulihkan kembali.`,
    `${sanitizeInput(program.code)} - ${sanitizeInput(program.name)}`,
    'Hapus',
    async () => {
      try {
        const response = await programStudyService.delete(program.id);

        // Check for success: false in response
        if (response.success === false) {
          const errorMessage = response.message || 'Failed to delete program study';
          toastStore.error('Gagal Hapus', errorMessage);
          return;
        }

        // Success case
        toastStore.success('Berhasil', 'Program studi berhasil dihapus!');
        fetchData();

      } catch (error) {
        const errorMessage = error.response?.data?.message || error.message || 'Failed to delete program study';
        toastStore.error('Gagal Hapus', errorMessage);
      }
    }
  );
};

// Confirm Delete Permanent (single item)
const confirmDeletePermanent = async (program) => {
  showConfirmationModal(
    'delete',
    'Konfirmasi Hapus Permanen',
    `⚠️ PERINGATAN: Apakah Anda yakin ingin menghapus permanen program studi "${sanitizeInput(program.name)}"? Tindakan ini tidak dapat dibatalkan!`,
    `${sanitizeInput(program.code)} - ${sanitizeInput(program.name)}`,
    'Hapus Permanen',
    async () => {
      try {
        const response = await programStudyService.forceDelete(program.id);

        if (response.success === false) {
          const errorMessage = response.message || 'Failed to permanently delete program study';
          toastStore.error('Gagal Hapus Permanen', errorMessage);
          return;
        }

        toastStore.success('Berhasil', 'Program studi berhasil dihapus permanen!');
        fetchData();

      } catch (error) {
        const errorMessage = error.response?.data?.message || error.message || 'Failed to permanently delete program study';
        toastStore.error('Gagal Hapus Permanen', errorMessage);
      }
    }
  );
};


const closeModal = () => {
  showModal.value = false;
  editingProgram.value = null;
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
    selectedItems.value = programs.value.map(p => p.id);
  }
};

const clearSelection = () => {
  selectedItems.value = [];
};

const bulkDelete = async () => {
  showConfirmationModal(
    'delete',
    'Konfirmasi Hapus Massal',
    `Apakah Anda yakin ingin menghapus ${selectedItems.value.length} program studi yang dipilih? Data yang dihapus dapat dipulihkan kembali.`,
    `${selectedItems.value.length} program studi`,
    'Hapus Semua',
    async () => {
      try {
        const response = await programStudyService.bulkDelete({
          program_study_ids: selectedItems.value
        });

        if (response.success) {
          toastStore.success('Berhasil', `${selectedItems.value.length} program studi berhasil dihapus!`);
          clearSelection();
          fetchData();
        } else {
          throw new Error(response.message || 'Failed to bulk delete programs');
        }
      } catch (error) {
        const errorMessage = error.response?.data?.message || error.message || 'Failed to bulk delete programs';
        toastStore.error('Gagal Hapus Massal', errorMessage);
      }
    }
  );
};

const bulkActivate = async () => {
  try {
    const response = await programStudyService.bulkToggleStatus({
      program_study_ids: selectedItems.value,
      is_active: true
    });
    if (response.success) {
      toastStore('Berhasil', `${selectedItems.value.length} program studi berhasil diaktifkan!`, 'success');
      clearSelection();
      fetchData();
    } else {
      throw new Error(response.message || 'Failed to bulk activate programs');
    }
  } catch (error) {
      toastStore.error('Gagal Mengaktifkan', error.userMessage || error.message || error.response?.data?.message || 'Terjadi kesalahan');
  }
};

const bulkDeactivate = async () => {
  try {
    const response = await programStudyService.bulkToggleStatus({
      program_study_ids: selectedItems.value,
      is_active: false
    });
    if (response.success) {
      toastStore('Berhasil', `${selectedItems.value.length} program studi berhasil dinonaktifkan!`, 'success');
      clearSelection();
      fetchData();
    } else {
      throw new Error(response.message || 'Failed to bulk deactivate programs');
    }
  } catch (error) {
        toastStore('Gagal Menonaktifkan', error.userMessage || error.message, 'error');
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
const formatLevelForDisplay = (level) => {
  const levelMapping = {
    'undergraduate': 'S1',
    'graduate': 'S2',
    'doctoral': 'S3'
  };

  return levelMapping[level] || level;
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
const toggleStatus = async (program) => {
  try {
    togglingStatus.value = program.id;

    const response = await programStudyService.toggleStatus(program.id, !program.is_active);

    if (response.success) {
      // Update program data in the local array
      const index = programs.value.findIndex(p => p.id === program.id);
      if (index !== -1) {
        programs.value[index] = { ...programs.value[index], ...response.data };
      }

      toastStore('Berhasil', `Status ${response.data.name} berhasil diubah menjadi ${response.data.is_active ? 'Aktif' : 'Tidak Aktif'}`, 'success');
    } else {
      throw new Error(response.message || 'Failed to toggle status');
    }
  } catch (error) {
    toastStore.error('Gagal Mengubah Status', error.userMessage || error.message || error.response?.data?.message || 'Terjadi kesalahan');
  } finally {
    togglingStatus.value = null;
  }
};

const duplicateProgram = async (program) => {
  showConfirmationModal(
    'info',
    'Konfirmasi Duplikasi',
    `Apakah Anda yakin ingin menduplikasi program studi "${program.name}"?`,
    `${program.code} - ${program.name}`,
    'Duplikasi',
    async () => {
      try {
        duplicatingProgram.value = program.id;

        const response = await programStudyService.duplicate(program.id);

        if (response.success) {
          toastStore.success('Berhasil', `Program studi "${response.data.name}" berhasil diduplikasi!`);
          fetchData();
        } else {
          throw new Error(response.message || 'Failed to duplicate program');
        }
      } catch (error) {
        const errorMessage = error.response?.data?.message || error.message || 'Failed to duplicate program';
        toastStore.error('Gagal Duplikasi', errorMessage);
      } finally {
        duplicatingProgram.value = null;
      }
    }
  );
};

const restoreProgram = async (program) => {
  showConfirmationModal(
    'info',
    'Konfirmasi Pemulihan',
    `Apakah Anda yakin ingin memulihkan program studi "${program.name}"?`,
    `${program.code} - ${program.name}`,
    'Pulihkan',
    async () => {
      restoringProgram.value = program.id;

      const response = await programStudyService.restore(program.id);

      if (response.success) {
        toastStore('Berhasil', `Program studi "${response.data.name}" berhasil dipulihkan!`, 'success');
        fetchData();
      } else {
        throw new Error(response.message || 'Failed to restore program');
      }
    }
  );
};

// Bulk Restore
const bulkRestore = async () => {
  showConfirmationModal(
    'info',
    'Konfirmasi Pemulihan Massal',
    `Apakah Anda yakin ingin memulihkan ${selectedItems.value.length} program studi yang dipilih?`,
    `${selectedItems.value.length} program studi`,
    'Pulihkan Semua',
    async () => {
      const promises = selectedItems.value.map(id =>
        programStudyService.restore(id)
      );

      const results = await Promise.allSettled(promises);
      const successful = results.filter(r => r.status === 'fulfilled' && r.value.success).length;

      if (successful > 0) {
        toastStore('Berhasil', `${successful} program studi berhasil dipulihkan!`, 'success');
        clearSelection();
        fetchData();
      } else {
        toastStore.error('Gagal', 'Tidak ada program studi yang berhasil dipulihkan');
      }
    }
  );
};

// Bulk Delete Permanent
const bulkDeletePermanent = async () => {
  showConfirmationModal(
    'delete',
    'Konfirmasi Hapus Permanen',
    `⚠️ PERINGATAN: Apakah Anda yakin ingin menghapus permanen ${selectedItems.value.length} program studi? Tindakan ini tidak dapat dibatalkan!`,
    `${selectedItems.value.length} program studi akan dihapus permanen`,
    'Hapus Permanen',
    async () => {
      const promises = selectedItems.value.map(id =>
        programStudyService.forceDelete(id).catch(error => {
          return { success: false, error: error.message || 'Failed to force delete' };
        })
      );

      const results = await Promise.allSettled(promises);
      const successful = results.filter(r => r.status === 'fulfilled' && r.value.success).length;
      const failed = results.filter(r => r.status === 'fulfilled' && !r.value.success).length;

      // Collect error messages
      const errorMessages = [];
      results.forEach(result => {
        if (result.status === 'fulfilled' && !result.value.success && result.value.error) {
          errorMessages.push(result.value.error);
        }
      });

      if (successful > 0) {
        toastStore.success('Berhasil', `${successful} program studi berhasil dihapus permanen!`);
      }

      if (failed > 0) {
        const errorMessage = errorMessages.length > 0 ? errorMessages[0] : 'Gagal menghapus beberapa program studi';
        toastStore.error('Gagal Hapus Permanen', errorMessage);
      }

      if (successful > 0 || failed > 0) {
        clearSelection();
        fetchData();
      }
    }
  );
};

// Refresh Data
const refreshData = async () => {
  try {
    clearSelection();
    await fetchData();
    toastStore.success('Data Segar', 'Data berhasil dimuat ulang!');
  } catch (error) {
    toastStore.error('Gagal Refresh', error.userMessage || error.message);
  }
};

onMounted(() => {
  fetchData();
});
</script>