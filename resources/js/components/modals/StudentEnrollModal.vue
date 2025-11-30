<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition-all duration-300"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-all duration-300"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="show"
        class="fixed inset-0 z-[9999] overflow-y-auto"
        @click="handleBackdropClick"
      >
        <!-- Backdrop -->
        <div class="flex min-h-full items-center justify-center p-4">
          <div class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>

          <!-- Dialog -->
          <Transition
            enter-active-class="transition-all duration-300"
            enter-from-class="opacity-0 scale-95 translate-y-4"
            enter-to-class="opacity-100 scale-100 translate-y-0"
            leave-active-class="transition-all duration-300"
            leave-from-class="opacity-100 scale-100 translate-y-0"
            leave-to-class="opacity-0 scale-95 translate-y-4"
          >
            <div
              v-if="show"
              class="relative mx-auto max-w-6xl w-full transform rounded-2xl shadow-2xl transition-all"
              @click.stop
            >
              <!-- Modal Content -->
              <div class="relative overflow-hidden rounded-2xl bg-white shadow-xl">
                <!-- Header -->
                <div class="relative bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-6">
                  <!-- Close Button -->
                  <button
                    @click="handleClose"
                    class="absolute top-4 right-4 text-white/80 hover:text-white transition-colors"
                    :disabled="loading"
                  >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>

                  <!-- Title and Icon -->
                  <div class="flex items-center space-x-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20 backdrop-blur-sm">
                      <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                      </svg>
                    </div>
                    <div>
                      <h3 class="text-2xl font-bold text-white">
                        Enroll Mahasiswa ke Kelas
                      </h3>
                      <p class="mt-1 text-blue-100">
                        Pilih mahasiswa untuk didaftarkan ke kelas yang dipilih
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Class Information -->
                <div v-if="selectedClass" class="bg-blue-50 border-b border-blue-100 px-6 py-4">
                  <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                      <div class="bg-blue-100 rounded-lg p-3">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                      </div>
                      <div>
                        <h4 class="font-semibold text-gray-900">{{ selectedClass.name }}</h4>
                        <p class="text-sm text-gray-600">{{ selectedClass.code }} • {{ selectedClass.program_study?.name }}</p>
                        <div class="flex items-center space-x-4 mt-1">
                          <span class="text-xs text-gray-500">
                            Angkatan: {{ selectedClass.batch_year }}
                          </span>
                          <span class="text-xs text-gray-500">
                            {{ selectedClass.semester }} • {{ selectedClass.academic_year }}
                          </span>
                          <span class="text-xs font-medium" :class="getCapacityTextColor()">
                            Kapasitas: {{ selectedClass.current_students || 0 }}/{{ selectedClass.capacity }}
                          </span>
                        </div>
                      </div>
                    </div>
                    <div class="text-right">
                      <div class="text-sm font-medium text-gray-900">
                        {{ selectedClass.available_slots || (selectedClass.capacity - (selectedClass.current_students || 0)) }} slot tersedia
                      </div>
                      <div class="text-xs text-gray-500">
                        {{ getCapacityPercentage() }}% terisi
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Main Content -->
                <div class="px-6 py-6">
                  <!-- Search and Filters -->
                  <div class="mb-6 space-y-4">
                    <!-- Search Bar -->
                    <div class="relative">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                      </div>
                      <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Cari mahasiswa berdasarkan nama atau NIM..."
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        :disabled="loading"
                      >
                    </div>

                    <!-- Filters & Controls -->
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                      <!-- Program Study Filter -->
                      <select
                        v-model="filterProgramStudy"
                        class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        :disabled="loading"
                      >
                        <option value="">Semua Program Studi</option>
                        <option v-for="ps in programStudies" :key="ps.id" :value="ps.id">
                          {{ ps.name }}
                        </option>
                      </select>

                      <!-- Batch Year Filter -->
                      <select
                        v-model="filterBatchYear"
                        class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        :disabled="loading"
                      >
                        <option value="">Semua Angkatan</option>
                        <option v-for="year in batchYears" :key="year" :value="year">
                          {{ year }}
                        </option>
                      </select>

                      <!-- Per Page Selector -->
                      <select
                        v-model="perPage"
                        @change="changePerPage"
                        class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        :disabled="loading"
                      >
                        <option value="10">10 data</option>
                        <option value="20">20 data</option>
                        <option value="50">50 data</option>
                        <option value="100">100 data</option>
                      </select>

                      <!-- Search -->
                      <div class="relative">
                        <input
                          v-model="searchQuery"
                          type="text"
                          placeholder="Cari mahasiswa..."
                          class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          :disabled="loading"
                        >
                        <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                      </div>

                      <!-- Clear Filters -->
                      <button
                        @click="clearFilters"
                        class="px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                        :disabled="loading"
                      >
                        Reset Filter
                      </button>
                    </div>
                  </div>

                  <!-- Selection Summary -->
                  <div v-if="selectedStudents.length > 0" class="mb-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                      <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-medium text-blue-900">
                          {{ selectedStudents.length }} mahasiswa dipilih
                        </span>
                      </div>
                      <div class="flex items-center space-x-2">
                        <span class="text-sm text-blue-700">
                          Kapasitas tersisa: {{ availableSlotsAfterSelection }}
                        </span>
                        <button
                          v-if="availableSlotsAfterSelection < 0"
                          @click="showCapacityWarning = true"
                          class="text-sm text-red-600 hover:text-red-700 underline"
                        >
                          Melebihi kapasitas!
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- View Mode Toggle -->
                  <div class="flex justify-end mb-4">
                    <div class="inline-flex rounded-md shadow-sm" role="group">
                      <button
                        @click="viewMode = 'table'"
                        :class="[
                          'relative inline-flex items-center px-3 py-2 text-sm font-medium border',
                          viewMode === 'table'
                            ? 'bg-blue-600 text-white border-blue-600 z-10'
                            : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                        ]"
                      >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m0 0l8-4m-8 4l-8-4" />
                        </svg>
                        Tabel
                      </button>
                      <button
                        @click="viewMode = 'grid'"
                        :class="[
                          'relative inline-flex items-center px-3 py-2 text-sm font-medium border -ml-px',
                          viewMode === 'grid'
                            ? 'bg-blue-600 text-white border-blue-600 z-10'
                            : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                        ]"
                      >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h2zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6a2 2 0 012-2h2zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2a2 2 0 012-2h2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2a2 2 0 012-2h2z" />
                        </svg>
                        Grid
                      </button>
                    </div>
                  </div>

                  <!-- Students List -->
                  <div class="space-y-4">
                    <!-- Loading State -->
                    <div v-if="loading" class="text-center py-12">
                      <div class="inline-flex items-center space-x-3">
                        <div class="w-6 h-6 animate-spin rounded-full border-2 border-blue-600 border-t-transparent"></div>
                        <span class="text-gray-600">Memuat data mahasiswa...</span>
                      </div>
                    </div>

                    <!-- Empty State -->
                    <div v-else-if="filteredStudents.length === 0" class="text-center py-12">
                      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                      </svg>
                      <h3 class="mt-4 text-lg font-medium text-gray-900">Tidak ada mahasiswa</h3>
                      <p class="mt-2 text-gray-500">Tidak ada mahasiswa yang sesuai dengan kriteria pencarian</p>
                    </div>

                    <!-- Table View -->
                    <div v-else-if="viewMode === 'table'">
                      <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-300">
                          <thead class="bg-gray-50">
                            <tr>
                              <th scope="col" class="w-12 px-6 py-3 text-left">
                                <input
                                  type="checkbox"
                                  :checked="isAllSelected"
                                  @change="toggleSelectAll"
                                  :disabled="loading"
                                  class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                >
                              </th>
                              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                NIM
                              </th>
                              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nama
                              </th>
                              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Program Studi
                              </th>
                              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Angkatan
                              </th>
                              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                              </th>
                            </tr>
                          </thead>
                          <tbody class="bg-white divide-y divide-gray-200">
                            <tr
                              v-for="student in filteredStudents"
                              :key="student.id"
                              :class="selectedStudents.includes(student.id) ? 'bg-blue-50' : ''"
                            >
                              <td class="w-12 px-6 py-4 whitespace-nowrap">
                                <input
                                  type="checkbox"
                                  :value="student.id"
                                  v-model="selectedStudents"
                                  :disabled="loading || isAlreadyEnrolled(student.id)"
                                  class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                >
                              </td>
                              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ student.student_number }}
                              </td>
                              <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ student.name }}</div>
                              </td>
                              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ student.program_study?.name || '-' }}
                              </td>
                              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ student.batch_year || '-' }}
                              </td>
                              <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                  class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                  :class="student.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                                >
                                  {{ student.status === 'active' ? 'Aktif' : student.status }}
                                </span>
                                <span
                                  v-if="isAlreadyEnrolled(student.id)"
                                  class="ml-2 inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800"
                                >
                                  Terdaftar
                                </span>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>

                      <!-- Pagination -->
                      <div class="flex items-center justify-between px-4 py-3 bg-white border-t border-gray-200 sm:px-6">
                        <div class="flex-1 flex justify-between sm:hidden">
                          <button
                            @click="previousPage"
                            :disabled="currentPage <= 1 || loading"
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
                          >
                            Previous
                          </button>
                          <button
                            @click="nextPage"
                            :disabled="currentPage >= lastPage || loading"
                            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
                          >
                            Next
                          </button>
                        </div>
                        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                          <div>
                            <p class="text-sm text-gray-700">
                              Menampilkan
                              <span class="font-medium">{{ (currentPage - 1) * perPage + 1 }}</span>
                              hingga
                              <span class="font-medium">{{ Math.min(currentPage * perPage, totalStudents) }}</span>
                              dari
                              <span class="font-medium">{{ totalStudents }}</span>
                              hasil
                            </p>
                          </div>
                          <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                              <button
                                @click="previousPage"
                                :disabled="currentPage <= 1 || loading"
                                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50"
                              >
                                Previous
                              </button>
                              <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                                {{ currentPage }}
                              </span>
                              <button
                                @click="nextPage"
                                :disabled="currentPage >= lastPage || loading"
                                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50"
                              >
                                Next
                              </button>
                            </nav>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Grid View -->
                    <div v-else class="grid grid-cols-1 lg:grid-cols-2 gap-4 max-h-96 overflow-y-auto">
                      <div
                        v-for="student in filteredStudents"
                        :key="student.id"
                        class="relative border rounded-lg hover:bg-gray-50 transition-colors"
                        :class="[
                          selectedStudents.includes(student.id) ? 'border-blue-500 bg-blue-50' : 'border-gray-200'
                        ]"
                      >
                        <!-- Checkbox -->
                        <div class="absolute top-3 left-3">
                          <input
                            type="checkbox"
                            :value="student.id"
                            v-model="selectedStudents"
                            :disabled="loading || isAlreadyEnrolled(student.id)"
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                          >
                        </div>

                        <!-- Student Card -->
                        <div class="p-4 pl-12">
                          <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                              <h4 class="font-medium text-gray-900 truncate">
                                {{ student.name }}
                              </h4>
                              <p class="text-sm text-gray-500">
                                {{ student.student_number }}
                              </p>

                              <!-- Student Details -->
                              <div class="mt-2 flex items-center space-x-4 text-xs text-gray-500">
                                <span v-if="student.program_study?.name" class="flex items-center">
                                  <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                  </svg>
                                  {{ student.program_study.name }}
                                </span>
                                <span v-if="student.batch_year" class="flex items-center">
                                  <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                  </svg>
                                  {{ student.batch_year }}
                                </span>
                                <span class="flex items-center">
                                  <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                  </svg>
                                  {{ student.current_semester }} semester
                                </span>
                              </div>

                              <!-- Status Badge -->
                              <div class="mt-2">
                                <span
                                  class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                                  :class="student.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                                >
                                  {{ student.status === 'active' ? 'Aktif' : student.status }}
                                </span>
                                <span
                                  v-if="isAlreadyEnrolled(student.id)"
                                  class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800"
                                >
                                  Sudah terdaftar
                                </span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Actions -->
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                  <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                      <span v-if="selectedStudents.length > 0">
                        {{ selectedStudents.length }} mahasiswa akan di-enroll
                        <span v-if="availableSlotsAfterSelection < 0" class="text-red-600 font-medium">
                          (Melebihi kapasitas!)
                        </span>
                      </span>
                      <span v-else>
                        Pilih mahasiswa yang ingin di-enroll
                      </span>
                    </div>

                    <div class="flex space-x-3">
                      <!-- Cancel Button -->
                      <button
                        @click="handleClose"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                        :disabled="loading"
                      >
                        Batal
                      </button>

                      <!-- Enroll Button -->
                      <button
                        @click="handleEnroll"
                        :disabled="loading || selectedStudents.length === 0 || availableSlotsAfterSelection < 0"
                        class="px-4 py-2 bg-blue-600 border border-transparent rounded-lg text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                      >
                        <span class="flex items-center">
                          <svg v-if="loading" class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                          </svg>
                          <svg v-else class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                          </svg>
                          {{ loading ? 'Meng-enroll...' : `Enroll ${selectedStudents.length} Mahasiswa` }}
                        </span>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </Transition>
        </div>
      </div>
    </Transition>

    <!-- Capacity Warning Modal -->
    <div
      v-if="showCapacityWarning"
      class="fixed inset-0 z-[10000] overflow-y-auto"
      @click="showCapacityWarning = false"
    >
      <div class="flex min-h-full items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>
        <div class="relative max-w-md w-full bg-white rounded-lg shadow-xl p-6">
          <div class="flex items-start space-x-3">
            <div class="flex-shrink-0">
              <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
              </svg>
            </div>
            <div class="flex-1">
              <h3 class="text-lg font-medium text-gray-900">
                Peringatan Kapasitas
              </h3>
              <p class="mt-2 text-sm text-gray-500">
                Jumlah mahasiswa yang dipilih melebihi kapasitas kelas. Kapasitas tersedia: {{ selectedClass?.available_slots || 0 }} mahasiswa.
              </p>
              <div class="mt-4 flex space-x-3">
                <button
                  @click="showCapacityWarning = false"
                  class="px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                >
                  Ok
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import studentService from '@/services/studentService'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  selectedClass: {
    type: Object,
    default: null
  },
  loading: {
    type: Boolean,
    default: false
  },
  enrolledStudents: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['close', 'enroll'])

// State
const searchQuery = ref('')
const selectedStudents = ref([])
const students = ref([])
const programStudies = ref([])
const batchYears = ref([])
const loading = ref(false)
const showCapacityWarning = ref(false)

// View mode
const viewMode = ref('table') // 'table' or 'grid'

// Pagination
const currentPage = ref(1)
const perPage = ref(10)
const totalStudents = ref(0)
const lastPage = ref(1)

// Filters
const filterProgramStudy = ref('')
const filterBatchYear = ref('')

// Computed properties
const filteredStudents = computed(() => {
  // Filter out already enrolled students from the display
  return students.value.filter(student => !isAlreadyEnrolled(student.id))
})

const availableSlotsAfterSelection = computed(() => {
  if (!props.selectedClass) return 0
  const currentStudents = props.selectedClass.current_students || 0
  const capacity = props.selectedClass.capacity || 0
  return capacity - currentStudents - selectedStudents.value.length
})

const isAllSelected = computed(() => {
  return filteredStudents.value.length > 0 && filteredStudents.value.every(student => selectedStudents.value.includes(student.id))
})

// Methods
const loadStudents = async () => {
  console.log('=== loadStudents function called ===')
  loading.value = true
  try {
    console.log('Loading students...')
    console.log('Calling studentService.getActive() with pagination...')
    console.log('Current page:', currentPage.value, 'Per page:', perPage.value)

    // Build parameters for API call
    const params = {
      page: currentPage.value,
      per_page: perPage.value,
      status: 'active'
    }

    // Add filters if present
    if (filterProgramStudy.value) {
      params.program_study_id = filterProgramStudy.value
    }
    if (filterBatchYear.value) {
      params.batch_year = filterBatchYear.value
    }
    if (searchQuery.value) {
      params.search = searchQuery.value
    }

    console.log('API params:', params)

    // Use studentService to get active students with pagination
    const result = await studentService.getActive(params)

    console.log('Students loaded successfully:', result)

    // Handle Laravel pagination format
    if (result.data && result.data.data && Array.isArray(result.data.data)) {
      // Laravel pagination format: { data: { data: [...], current_page: 1, last_page: 1, total: 50, ... }, meta: {...} }
      students.value = result.data.data
      currentPage.value = result.data.current_page || 1
      lastPage.value = result.data.last_page || 1
      totalStudents.value = result.data.total || result.data.data.length

      console.log('Using server pagination - Current page:', currentPage.value, 'Last page:', lastPage.value, 'Total:', totalStudents.value)
      console.log('Students on this page:', result.data.data.length)
    } else if (result.data && Array.isArray(result.data)) {
      // Direct array format (fallback for client-side pagination)
      students.value = result.data
      totalStudents.value = result.data.length
      lastPage.value = Math.ceil(totalStudents.value / perPage.value)

      console.log('Using client-side pagination - Total students:', totalStudents.value, 'Last page:', lastPage.value)
    } else if (Array.isArray(result)) {
      // Array format directly (fallback)
      students.value = result
      totalStudents.value = result.length
      lastPage.value = Math.ceil(totalStudents.value / perPage.value)

      console.log('Using direct array format - Total students:', totalStudents.value)
    } else {
      students.value = []
      totalStudents.value = 0
      lastPage.value = 1
      console.log('No students found, unexpected response format:', result)
    }

    console.log('Processed students count:', students.value.length)

    // Extract unique program studies and batch years (only if not filtered)
    if (!filterProgramStudy.value && students.value.length > 0) {
      const uniqueProgramStudies = [...new Set(students.value
        .map(s => s.program_study)
        .filter(Boolean)
        .map(ps => ({ id: ps.id, name: ps.name })))
      ]
      programStudies.value = uniqueProgramStudies
    }

    if (!filterBatchYear.value && students.value.length > 0) {
      const uniqueBatchYears = [...new Set(students.value
        .filter(s => s.batch_year)
        .map(s => s.batch_year)
      )].sort()
      batchYears.value = uniqueBatchYears
    }

    console.log('Program studies loaded:', programStudies.value.length, 'items')
    console.log('Batch years loaded:', batchYears.value.length, 'items')
  } catch (error) {
    console.error('Error loading students:', error)
    console.error('Error details:', error.response?.data || error.message)
    // Show error notification to user if needed
    if (typeof showToast !== 'undefined') {
      showToast('Error', 'Gagal memuat data mahasiswa', 'error')
    }
    students.value = []
    totalStudents.value = 0
    lastPage.value = 1
  } finally {
    loading.value = false
    console.log('=== loadStudents function completed ===')
  }
}

const loadProgramStudies = async () => {
  console.log('=== loadProgramStudies function called ===')
  try {
    // If we already have program studies from student data, use them
    if (programStudies.value.length > 0) {
      console.log('Program studies already loaded:', programStudies.value.length)
      return
    }

    // Otherwise, we could load them separately if needed
    console.log('Loading program studies separately...')
    // This could call a separate API if needed
  } catch (error) {
    console.error('Error loading program studies:', error)
  }
}

const clearFilters = () => {
  searchQuery.value = ''
  filterProgramStudy.value = ''
  filterBatchYear.value = ''
  currentPage.value = 1
}

const toggleSelectAll = () => {
  const availableStudents = filteredStudents.value

  if (isAllSelected.value) {
    // Unselect all
    selectedStudents.value = selectedStudents.value.filter(studentId =>
      !availableStudents.some(student => student.id === studentId)
    )
  } else {
    // Select all available students
    const newSelections = availableStudents
      .filter(student => !selectedStudents.value.includes(student.id))
      .map(student => student.id)
    selectedStudents.value = [...selectedStudents.value, ...newSelections]
  }
}

const changePerPage = () => {
  currentPage.value = 1
  loadStudents() // Reload data with new per_page value
}

const previousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
    loadStudents() // Load data for previous page
  }
}

const nextPage = () => {
  if (currentPage.value < lastPage.value) {
    currentPage.value++
    loadStudents() // Load data for next page
  }
}

const isAlreadyEnrolled = (studentId) => {
  return props.enrolledStudents.some(enrolled => enrolled.id === studentId)
}

const getCapacityPercentage = () => {
  if (!props.selectedClass || !props.selectedClass.capacity) return 0
  return Math.round(((props.selectedClass.current_students || 0) / props.selectedClass.capacity) * 100)
}

const getCapacityTextColor = () => {
  const percentage = getCapacityPercentage()
  if (percentage >= 100) return 'text-red-600'
  if (percentage >= 80) return 'text-yellow-600'
  return 'text-green-600'
}

const handleBackdropClick = () => {
  if (!props.loading) {
    handleClose()
  }
}

const handleClose = () => {
  if (!props.loading) {
    clearFilters()
    selectedStudents.value = []
    emit('close')
  }
}

const handleEnroll = () => {
  if (selectedStudents.value.length === 0) return

  // Validate capacity
  if (availableSlotsAfterSelection.value < 0) {
    showCapacityWarning.value = true
    return
  }

  emit('enroll', {
    studentIds: selectedStudents.value,
    enrollmentDate: new Date().toISOString().split('T')[0], // Today's date in YYYY-MM-DD format
    notes: `Enrolled ${selectedStudents.value.length} students to ${props.selectedClass?.name || 'Class'}`
  })
}

// Watch for modal open/close
watch(() => props.show, (newShow) => {
  console.log('StudentEnrollModal show changed to:', newShow)
  if (newShow) {
    console.log('Modal is opening, calling loadStudents...')
    loadStudents()
    loadProgramStudies()
    clearFilters()
    selectedStudents.value = []
  }
}, { immediate: true })

// Handle escape key
const handleEscapeKey = (event) => {
  if (event.key === 'Escape' && props.show && !props.loading) {
    handleClose()
  }
}

onMounted(() => {
  console.log('=== StudentEnrollModal mounted ===')
  console.log('Props show:', props.show)
  console.log('Props selectedClass:', props.selectedClass)
  document.addEventListener('keydown', handleEscapeKey)
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleEscapeKey)
})

// Watch for filter changes and reload data
watch([filterProgramStudy, filterBatchYear], () => {
  console.log('Filters changed, reloading data...')
  currentPage.value = 1
  loadStudents()
})

// Watch for search with debounce
let searchTimeout
watch(searchQuery, () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    console.log('Search query changed:', searchQuery.value)
    currentPage.value = 1
    loadStudents()
  }, 500) // 500ms debounce
})

// Prevent body scroll when modal is open
watch(() => props.show, (newShow) => {
  if (newShow) {
    document.body.style.overflow = 'hidden'
    document.documentElement.style.overflow = 'hidden'
  } else {
    // Ensure scroll is restored with fallback
    setTimeout(() => {
      document.body.style.overflow = ''
      document.documentElement.style.overflow = ''
      document.body.style.removeProperty('overflow')
      document.documentElement.style.removeProperty('overflow')
    }, 50)
  }
}, { immediate: true })

// Cleanup function to ensure scroll is always restored
onUnmounted(() => {
  document.body.style.overflow = ''
  document.documentElement.style.overflow = ''
  document.body.style.removeProperty('overflow')
  document.documentElement.style.removeProperty('overflow')
})

// Also add a global cleanup function for emergencies
const restoreScroll = () => {
  document.body.style.overflow = ''
  document.documentElement.style.overflow = ''
  document.body.style.removeProperty('overflow')
  document.documentElement.style.removeProperty('overflow')
}

// Expose cleanup function globally for emergency cases
window.restoreScroll = restoreScroll
</script>

<style scoped>
/* Custom scrollbar for students list */
.max-h-96::-webkit-scrollbar {
  width: 6px;
}

.max-h-96::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

.max-h-96::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

.max-h-96::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}
</style>