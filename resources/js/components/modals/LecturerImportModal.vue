<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 transition-opacity" aria-hidden="true">
        <div class="absolute inset-0 bg-gray-500 opacity-75" @click="$emit('close')"></div>
      </div>

      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-7xl sm:w-full">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div class="w-full">
              <!-- Header -->
              <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl leading-6 font-bold text-gray-900">
                  Import Data Dosen
                </h3>
                <button
                  @click="$emit('close')"
                  class="text-gray-400 hover:text-gray-600 transition-colors"
                >
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                </button>
              </div>

              <!-- Step Indicator -->
              <div class="mb-8">
                <div class="flex items-center justify-between">
                  <div class="flex items-center">
                    <div
                      :class="[
                        'flex items-center justify-center w-10 h-10 rounded-full text-sm font-medium',
                        currentStep >= 1 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600'
                      ]"
                    >
                      1
                    </div>
                    <span class="ml-3 text-sm font-medium">Pilih File</span>
                  </div>
                  <div class="flex-1 h-0.5 mx-4" :class="currentStep >= 2 ? 'bg-blue-600' : 'bg-gray-200'"></div>
                  <div class="flex items-center">
                    <div
                      :class="[
                        'flex items-center justify-center w-10 h-10 rounded-full text-sm font-medium',
                        currentStep >= 2 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600'
                      ]"
                    >
                      2
                    </div>
                    <span class="ml-3 text-sm font-medium">Validasi</span>
                  </div>
                  <div class="flex-1 h-0.5 mx-4" :class="currentStep >= 3 ? 'bg-blue-600' : 'bg-gray-200'"></div>
                  <div class="flex items-center">
                    <div
                      :class="[
                        'flex items-center justify-center w-10 h-10 rounded-full text-sm font-medium',
                        currentStep >= 3 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600'
                      ]"
                    >
                      3
                    </div>
                    <span class="ml-3 text-sm font-medium">Konfirmasi</span>
                  </div>
                </div>
              </div>

              <!-- Step 1: File Upload -->
              <div v-if="currentStep === 1" class="space-y-6">
                <!-- Template Download -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                  <div class="flex items-center">
                    <svg class="w-6 h-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <div>
                      <h4 class="text-sm font-semibold text-blue-900">Download Template</h4>
                      <p class="text-xs text-blue-700 mt-1">Unduh template Excel untuk format yang benar</p>
                      <div class="mt-2">
                        <button
                          @click="downloadTemplate"
                          :disabled="downloadingTemplate"
                          class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50"
                        >
                          <svg v-if="downloadingTemplate" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                          </svg>
                          <svg v-else class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                          </svg>
                          {{ downloadingTemplate ? 'Mengunduh...' : 'Download Template Excel' }}
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- File Upload Area -->
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors"
                     :class="{ 'border-blue-400 bg-blue-50': isDragOver }"
                     @dragover.prevent="isDragOver = true"
                     @dragleave.prevent="isDragOver = false"
                     @drop.prevent="handleFileDrop">
                  <svg v-if="!selectedFile" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                  </svg>
                  <div v-if="!selectedFile" class="mt-4">
                    <label for="file-upload" class="cursor-pointer">
                      <span class="mt-2 block text-sm font-medium text-gray-900">
                        Klik untuk upload atau drag and drop
                      </span>
                      <span class="mt-1 block text-xs text-gray-500">
                        Excel files (.xlsx, .xls, .csv) hingga 10MB
                      </span>
                      <input id="file-upload" name="file-upload" type="file" class="sr-only" accept=".xlsx,.xls,.csv" @change="handleFileSelect">
                    </label>
                  </div>

                  <div v-if="selectedFile" class="mt-4">
                    <div class="flex items-center justify-center space-x-2">
                      <svg class="h-8 w-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                      <div class="text-sm text-gray-900">
                        <span class="font-medium">{{ selectedFile.name }}</span>
                        <span class="text-gray-500">({{ formatFileSize(selectedFile.size) }})</span>
                      </div>
                    </div>
                    <button @click="removeFile" class="mt-2 text-xs text-red-600 hover:text-red-800">
                      Hapus file
                    </button>
                  </div>
                </div>

                <!-- Import Options -->
                <div class="bg-gray-50 rounded-lg p-4">
                  <h4 class="text-sm font-medium text-gray-900 mb-3">Opsi Import</h4>
                  <div class="space-y-2">
                    <label class="flex items-center space-x-3">
                      <input
                        v-model="importOptions.skipDuplicates"
                        type="checkbox"
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                      >
                      <span class="text-sm text-gray-700">Lewati duplikat</span>
                    </label>

                    <label class="flex items-center space-x-3">
                      <input
                        v-model="importOptions.updateExisting"
                        type="checkbox"
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                      >
                      <span class="text-sm text-gray-700">Update data yang ada</span>
                    </label>

                    <label class="flex items-center space-x-3">
                      <input
                        v-model="importOptions.forceImportInvalid"
                        type="checkbox"
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                      >
                      <span class="text-sm text-gray-700">Import data tidak valid (jika mungkin)</span>
                    </label>
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3 pt-4">
                  <button
                    @click="$emit('close')"
                    type="button"
                    class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                  >
                    Batal
                  </button>
                  <button
                    @click="validateFile"
                    :disabled="!selectedFile || validating"
                    type="button"
                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
                  >
                    <svg v-if="validating" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ validating ? 'Memvalidasi...' : 'Validasi File' }}
                  </button>
                </div>
              </div>

              <!-- Step 2: Excel-like Validation Results -->
              <div v-if="currentStep === 2" class="space-y-6">
                <!-- Summary Dashboard -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                  <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center">
                      <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                      </div>
                      <div class="ml-3">
                        <p class="text-sm font-medium text-blue-900">Total Data</p>
                        <p class="text-2xl font-bold text-blue-600">{{ validationResults.total_rows || 0 }}</p>
                      </div>
                    </div>
                  </div>

                  <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                      <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                      </div>
                      <div class="ml-3">
                        <p class="text-sm font-medium text-green-900">Valid</p>
                        <p class="text-2xl font-bold text-green-600">{{ validationResults.valid_rows || 0 }}</p>
                      </div>
                    </div>
                  </div>

                  <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center">
                      <div class="p-2 bg-red-100 rounded-lg">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                      </div>
                      <div class="ml-3">
                        <p class="text-sm font-medium text-red-900">Error</p>
                        <p class="text-2xl font-bold text-red-600">{{ validationResults.invalid_rows || 0 }}</p>
                      </div>
                    </div>
                  </div>

                  <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-center">
                      <div class="p-2 bg-yellow-100 rounded-lg">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.314 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                      </div>
                      <div class="ml-3">
                        <p class="text-sm font-medium text-yellow-900">Duplikat</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ validationResults.duplicate_rows || 0 }}</p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Enhanced Quick Fix Bar -->
                <div v-if="(validationResults.invalid_data && validationResults.invalid_data.length > 0) || (validationResults.all_data && validationResults.all_data.length > 0)"
                     class="mb-6 bg-white rounded-xl shadow-sm border border-gray-200">
                  <!-- Header -->
                  <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-t-xl">
                    <div class="flex items-center justify-between">
                      <div class="flex items-center">
                        <div class="p-2 bg-white/20 rounded-lg mr-3">
                          <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                          </svg>
                        </div>
                        <div>
                          <h4 class="text-sm font-semibold">Quick Fix Tools</h4>
                          <p class="text-xs text-white/80">Perbaiki data dengan cepat dan otomatis</p>
                        </div>
                      </div>
                      <div class="text-right">
                        <span class="text-lg font-bold">{{ (validationResults.invalid_data?.length || 0) + (validationResults.all_data?.length || 0) }}</span>
                        <span class="text-xs block text-white/80">items perlu diperbaiki</span>
                      </div>
                    </div>
                  </div>

                  <!-- Tools Grid -->
                  <div class="p-6 space-y-4">
                    <!-- Program Study Fix -->
                    <div class="flex items-center gap-4 p-3 bg-blue-50 rounded-lg">
                      <div class="flex-shrink-0">
                        <div class="p-2 bg-blue-100 rounded-lg">
                          <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                          </svg>
                        </div>
                      </div>
                      <div class="flex-1">
                        <h5 class="text-sm font-medium text-gray-900">Program Studi</h5>
                        <p class="text-xs text-gray-600">Auto-fill program studi yang kosong</p>
                      </div>
                      <div class="flex items-center gap-2">
                        <select v-model="selectedProgramStudy"
                                class="text-sm px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                          <option value="">Pilih Program Studi</option>
                          <option v-for="ps in programStudies" :key="ps.id" :value="ps.name">
                            {{ ps.name }}
                          </option>
                        </select>
                        <button @click="applyProgramStudyToAll"
                                :disabled="!selectedProgramStudy"
                                class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors">
                          Apply All
                        </button>
                      </div>
                    </div>

                    <!-- Enum Fields Fix -->
                    <div class="flex items-center gap-4 p-3 bg-purple-50 rounded-lg">
                      <div class="flex-shrink-0">
                        <div class="p-2 bg-purple-100 rounded-lg">
                          <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                          </svg>
                        </div>
                      </div>
                      <div class="flex-1">
                        <h5 class="text-sm font-medium text-gray-900">Field Enum</h5>
                        <p class="text-xs text-gray-600">Perbaiki field enum (gender, status, dll)</p>
                      </div>
                      <div class="flex gap-2">
                        <button @click="fixAllEnumFields"
                                class="px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors">
                          Fix All Enums
                        </button>
                      </div>
                    </div>

                    <!-- Duplicates Management -->
                    <div class="flex items-center gap-4 p-3 bg-red-50 rounded-lg">
                      <div class="flex-shrink-0">
                        <div class="p-2 bg-red-100 rounded-lg">
                          <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                          </svg>
                        </div>
                      </div>
                      <div class="flex-1">
                        <h5 class="text-sm font-medium text-gray-900">Data Duplikat</h5>
                        <p class="text-xs text-gray-600">Hapus semua data duplikat dengan satu klik</p>
                      </div>
                      <div class="flex gap-2">
                        <div class="text-right">
                          <span class="text-lg font-bold text-red-600">{{ validationResults.duplicate_rows || 0 }}</span>
                          <span class="text-xs block text-gray-600">duplikat</span>
                        </div>
                        <button @click="deleteAllDuplicates"
                                :disabled="(validationResults.duplicate_rows || 0) === 0"
                                class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors">
                          🗑️ Hapus Semua
                        </button>
                      </div>
                    </div>

                    <!-- Format Fix Tools -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                      <button @click="fixAllDates"
                              class="flex items-center justify-center gap-2 p-3 bg-green-50 hover:bg-green-100 border border-green-200 rounded-lg transition-colors">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <div class="text-left">
                          <span class="text-xs font-medium text-green-800 block">Format Tanggal</span>
                          <span class="text-xs text-green-600">Auto-fix dates</span>
                        </div>
                      </button>

                      <button @click="fixEmails"
                              class="flex items-center justify-center gap-2 p-3 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 rounded-lg transition-colors">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <div class="text-left">
                          <span class="text-xs font-medium text-indigo-800 block">Format Email</span>
                          <span class="text-xs text-indigo-600">Auto-fix emails</span>
                        </div>
                      </button>

                      <button @click="fixPhones"
                              class="flex items-center justify-center gap-2 p-3 bg-orange-50 hover:bg-orange-100 border border-orange-200 rounded-lg transition-colors">
                        <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        <div class="text-left">
                          <span class="text-xs font-medium text-orange-800 block">Format No. HP</span>
                          <span class="text-xs text-orange-600">Auto-fix phones</span>
                        </div>
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Excel-like Data Table -->
                <div v-if="validationResults.all_data && validationResults.all_data.length > 0" class="space-y-4">
                  <!-- Excel Header Bar with Bulk Actions -->
                  <div class="bg-gray-100 px-4 py-3 border border-gray-300">
                    <div class="flex items-center justify-between">
                      <div class="flex items-center space-x-3">
                        <input
                          v-model="selectAllChecked"
                          @change="toggleSelectAll"
                          type="checkbox"
                          class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                          :indeterminate="selectedRowsCount > 0 && selectedRowsCount < validationResults.all_data.length"
                        >
                        <span class="text-xs text-gray-600 font-medium">
                          {{ selectedRowsCount }} dari {{ validationResults.all_data.length }} dipilih
                        </span>
                        <span class="text-xs text-gray-500">• A1:{{ getColumnLetter(columns.length - 1) }}{{ validationResults.all_data.length + 1 }}</span>
                      </div>
                      <div class="flex items-center gap-2">
                        <button
                          v-if="selectedRowsCount > 0"
                          @click="bulkDeleteSelected"
                          class="text-xs px-2 py-1 bg-red-100 text-red-700 rounded hover:bg-red-200 transition-colors"
                        >
                          🗑️ Hapus {{ selectedRowsCount }}
                        </button>
                        <button @click="exportToCSV" class="text-xs px-2 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200">
                          📊 Export CSV
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Excel Table with Horizontal Scroll -->
                  <div class="border border-gray-300 rounded-lg overflow-hidden">
                    <!-- Scrollable Table Container -->
                    <div class="overflow-auto" style="height: 500px; width: 100%;">
                      <!-- Table -->
                      <table class="w-full" style="min-width: 2800px; border-collapse: collapse;">
                        <!-- Fixed Header Row -->
                        <thead class="sticky top-0 z-10 bg-gray-50">
                          <tr>
                            <!-- Select All Checkbox -->
                            <th class="sticky left-0 z-20 px-2 py-2 text-xs font-medium text-gray-700 text-center border-r border-b border-gray-300 bg-gray-100 w-12">
                              <input
                                v-model="selectAllChecked"
                                @change="toggleSelectAll"
                                type="checkbox"
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                :indeterminate="selectedRowsCount > 0 && selectedRowsCount < validationResults.all_data.length"
                              >
                            </th>

                            <!-- Row Number -->
                            <th class="sticky left-0 z-20 px-2 py-2 text-xs font-medium text-gray-700 text-center border-r border-b border-gray-300 bg-gray-100 w-12">
                              #
                            </th>

                            <!-- Status Column -->
                            <th class="sticky left-0 z-20 px-2 py-2 text-xs font-medium text-gray-700 border-r border-b border-gray-300 bg-gray-100 w-24">
                              Status
                            </th>

                            <!-- Dynamic Column Headers -->
                            <th v-for="(column, index) in columns" :key="index"
                                class="px-2 py-2 text-xs font-medium text-gray-700 border-r border-b border-gray-300"
                                :style="getColumnWidth(index)">
                              {{ column.name }}
                              <span v-if="column.required" class="text-red-500">*</span>
                            </th>
                          </tr>
                        </thead>

                        <!-- Data Rows -->
                        <tbody>
                          <tr v-for="(item, rowIndex) in validationResults.all_data" :key="rowIndex"
                              class="hover:bg-blue-50 transition-colors"
                              :class="getRowClass(item)">
                            <!-- Row Checkbox -->
                            <td class="sticky left-0 z-10 px-2 py-1 text-xs text-center border-r border-b border-gray-300 bg-gray-50">
                              <input
                                v-model="item.selected"
                                @change="updateSelectedCount"
                                type="checkbox"
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                              >
                            </td>

                            <!-- Row Number -->
                            <td class="sticky left-0 z-10 px-2 py-1 text-xs text-gray-600 text-center border-r border-b border-gray-300 bg-gray-50">
                              {{ item.row_number }}
                            </td>

                            <!-- Status -->
                            <td class="sticky left-0 z-10 px-1 py-1 border-r border-b border-gray-300 bg-gray-50">
                              <span v-if="item.is_valid && !item.is_duplicate"
                                    class="inline-flex items-center px-1 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                ✓ Valid
                              </span>
                              <span v-else-if="item.is_duplicate"
                                    class="inline-flex items-center px-1 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                ⚠ Dup
                              </span>
                              <span v-else
                                    class="inline-flex items-center px-1 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                ✗ Error
                              </span>
                            </td>

                            <!-- Editable Data Cells -->
                            <td v-for="(column, colIndex) in columns" :key="colIndex"
                                class="px-1 py-1 border-r border-b border-gray-300"
                                :style="getColumnWidth(colIndex)">
                              <!-- Dropdown for enum fields -->
                              <select v-if="column.type === 'select'"
                                      v-model="item.mapped_data[column.field]"
                                      @change="markAsChanged(item)"
                                      class="w-full px-1 py-0.5 text-xs border-0 rounded focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                      :class="getCellClass(item, column)">
                                <option value="">Pilih {{ column.name }}</option>
                                <option v-for="option in column.options" :key="option" :value="option">
                                  {{ option }}
                                </option>
                              </select>
                              <!-- Date input for date fields -->
                              <input v-else-if="column.type === 'date'"
                                     v-model="item.mapped_data[column.field]"
                                     type="date"
                                     @change="markAsChanged(item)"
                                     class="w-full px-1 py-0.5 text-xs border-0 rounded focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                     :class="getCellClass(item, column)">
                              <!-- Text input for other fields -->
                              <input v-else
                                     v-model="item.mapped_data[column.field]"
                                     type="text"
                                     @change="markAsChanged(item)"
                                     class="w-full px-1 py-0.5 text-xs border-0 rounded focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                     :class="getCellClass(item, column)"
                                     :placeholder="getPlaceholder(column)">
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>

                  <!-- Excel Footer Bar -->
                  <div class="bg-gray-100 px-4 py-2 border-t border-gray-300">
                    <div class="flex items-center justify-between">
                      <div class="text-xs text-gray-600">
                        💡 <span class="font-medium">Tips:</span> Klik sel untuk edit, gunakan <kbd>Tab</kbd> untuk navigasi, <kbd>Ctrl+Z</kbd> untuk undo
                      </div>
                      <div class="text-xs text-gray-600">
                        Scroll → untuk lihat semua kolom
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-between pt-4">
                  <button
                    @click="currentStep = 1"
                    type="button"
                    class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                  >
                    ← Kembali
                  </button>
                  <div class="flex space-x-3">
                    <button
                      @click="revalidateEditedData"
                      :disabled="validating"
                      type="button"
                      class="px-4 py-2 border border-orange-300 rounded-md shadow-sm text-sm font-medium text-orange-700 bg-orange-50 hover:bg-orange-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 disabled:opacity-50"
                    >
                      <svg v-if="validating" class="animate-spin -ml-1 mr-2 h-4 w-4 text-orange-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                      {{ validating ? 'Merevalidasi...' : 'Validasi Ulang' }}
                    </button>
                    <button
                      v-if="validationResults.invalid_rows === 0"
                      @click="currentStep = 3"
                      type="button"
                      class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                    >
                      Lanjut ke Import →
                    </button>
                  </div>
                </div>
              </div>

              <!-- Step 3: Confirmation -->
              <div v-if="currentStep === 3" class="space-y-6">
                <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                  <div class="flex items-center">
                    <svg class="h-8 w-8 text-green-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                      <h4 class="text-lg font-semibold text-green-900">Data Siap Diimport!</h4>
                      <p class="text-sm text-green-700 mt-1">
                        {{ validationResults.valid_rows }} data valid dan siap untuk diimport ke sistem.
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Final Import Settings -->
                <div class="bg-gray-50 rounded-lg p-4">
                  <h4 class="text-sm font-medium text-gray-900 mb-3">Konfirmasi Import</h4>
                  <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                      <span class="text-gray-600">Data yang akan diimport:</span>
                      <span class="font-medium text-gray-900 ml-2">{{ validationResults.valid_rows }}</span>
                    </div>
                    <div>
                      <span class="text-gray-600">Duplikat akan dilewati:</span>
                      <span class="font-medium text-gray-900 ml-2">{{ validationResults.duplicate_rows }}</span>
                    </div>
                    <div>
                      <span class="text-gray-600">Update data ada:</span>
                      <span class="font-medium text-gray-900 ml-2">{{ importOptions.updateExisting ? 'Ya' : 'Tidak' }}</span>
                    </div>
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-between pt-4">
                  <button
                    @click="currentStep = 2"
                    type="button"
                    class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                  >
                    ← Kembali
                  </button>
                  <div class="flex space-x-3">
                    <button
                      @click="$emit('close')"
                      type="button"
                      class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                      Batal
                    </button>
                    <button
                      @click="processImport"
                      :disabled="importing"
                      type="button"
                      class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
                    >
                      <svg v-if="importing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                      {{ importing ? 'Mengimport...' : 'Import Data' }}
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Success Modal -->
        <div v-if="showSuccessModal" class="fixed inset-0 z-50 overflow-y-auto">
          <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
              <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
              <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                  <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                    <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                  </div>
                  <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                      Import Berhasil!
                    </h3>
                    <div class="mt-2">
                      <p class="text-sm text-gray-500">
                        Data dosen berhasil diimport ke sistem.
                      </p>
                      <div v-if="importResults" class="mt-3 text-sm">
                        <p><span class="font-medium">{{ importResults.imported || 0 }}</span> data diimport</p>
                        <p><span class="font-medium">{{ importResults.updated || 0 }}</span> data diupdate</p>
                        <p><span class="font-medium">{{ importResults.skipped || 0 }}</span> data dilewati</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button
                  @click="closeSuccessModal"
                  type="button"
                  class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
                >
                  Selesai
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useToastStore } from '@/stores/toast'
import axios from 'axios'
import { useDateFormat } from '@vueuse/core'

const toastStore = useToastStore()

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close', 'import-success'])

// State
const currentStep = ref(1)
const selectedFile = ref(null)
const isDragOver = ref(false)
const validating = ref(false)
const importing = ref(false)
const downloadingTemplate = ref(false)
const validationResults = ref({})
const importResults = ref(null)
const showSuccessModal = ref(false)
const programStudies = ref([])
const selectedProgramStudy = ref('')
const selectedRowsCount = ref(0)
const selectAllChecked = ref(false)

const importOptions = reactive({
  skipDuplicates: true,
  updateExisting: false,
  forceImportInvalid: false
})

// Column definitions with dropdown options
const columns = ref([
  { name: 'Nama', field: 'nama_wajib', type: 'text', required: true, width: '180px' },
  { name: 'Email', field: 'email_wajib', type: 'text', required: true, width: '200px' },
  { name: 'NIP/NIDN', field: 'nip_nidn_wajib', type: 'text', required: true, width: '120px' },
  { name: 'No HP', field: 'no_hp_wajib', type: 'text', required: true, width: '120px' },
  { name: 'No KTP', field: 'no_ktp', type: 'text', required: false, width: '120px' },
  { name: 'Jenis Kelamin', field: 'jenis_kelamin', type: 'select', required: false, width: '100px', options: ['L', 'P'] },
  { name: 'Tempat Lahir', field: 'tempat_lahir', type: 'text', required: false, width: '120px' },
  { name: 'Tanggal Lahir', field: 'tanggal_lahir', type: 'date', required: false, width: '120px' },
  { name: 'Alamat', field: 'alamat', type: 'text', required: false, width: '200px' },
  { name: 'Kota', field: 'kota', type: 'text', required: false, width: '120px' },
  { name: 'Provinsi', field: 'provinsi', type: 'text', required: false, width: '120px' },
  { name: 'Kode Pos', field: 'kode_pos', type: 'text', required: false, width: '80px' },
  { name: 'Kebangsaan', field: 'kebangsaan', type: 'text', required: false, width: '120px' },
  { name: 'Agama', field: 'agama', type: 'select', required: false, width: '100px', options: ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Budha', 'Konghucu', 'Lainnya'] },
  { name: 'Gol Darah', field: 'golongan_darah', type: 'select', required: false, width: '80px', options: ['A', 'B', 'AB', 'O'] },
  { name: 'Status Pegawai', field: 'status_kepegawaian_wajib', type: 'select', required: true, width: '120px', options: ['permanent', 'contract', 'part_time', 'guest'] },
  { name: 'Jenis Pegawai', field: 'jenis_pegawai_wajib', type: 'select', required: true, width: '120px', options: ['Tetap', 'Kontrak', 'Paruh', 'Tamu'] },
  { name: 'Status Dosen', field: 'status_dosen_wajib', type: 'select', required: true, width: '100px', options: ['Aktif', 'Cuti', 'Tidak Aktif'] },
  { name: 'Tanggal Masuk', field: 'tanggal_masuk', type: 'date', required: false, width: '120px' },
  { name: 'Jabatan', field: 'jabatan', type: 'text', required: false, width: '150px' },
  { name: 'Pangkat Akademik', field: 'gelar', type: 'select', required: false, width: '120px', options: ['Asisten Ahli', 'Lektor', 'Lektor Kepala', 'Profesor'] },
  { name: 'Bidang Keahlian', field: 'bidang_keahlian', type: 'text', required: false, width: '180px' },
  { name: 'Program Studi', field: 'departemen', type: 'select', required: false, width: '150px', options: [] },
  { name: 'Fakultas', field: 'fakultas', type: 'select', required: false, width: '120px', options: ['Sekolah Pascasarjana'] },
  { name: 'Pendidikan', field: 'pendidikan_tertinggi', type: 'select', required: false, width: '100px', options: ['S1', 'S2', 'S3'] },
  { name: 'Institusi', field: 'institusi_pendidikan', type: 'text', required: false, width: '150px' },
  { name: 'Jurusan', field: 'jurusan_pendidikan', type: 'text', required: false, width: '120px' },
  { name: 'Tahun Lulus', field: 'tahun_lulus', type: 'text', required: false, width: '80px' },
  { name: 'Ruang Kantor', field: 'no_ruang_kantor', type: 'text', required: false, width: '100px' },
  { name: 'Catatan', field: 'catatan', type: 'text', required: false, width: '150px' }
])

// Load program studies on mount
onMounted(async () => {
  await loadProgramStudies()
})

// Methods
const loadProgramStudies = async () => {
  try {
    const response = await axios.get('/api/lecturers/import/program-studies')
    if (response.data.success) {
      programStudies.value = response.data.data
      const programStudyColumn = columns.value.find(col => col.field === 'departemen')
      if (programStudyColumn) {
        programStudyColumn.options = programStudies.value.map(ps => ps.name)
      }
    }
  } catch (error) {
    console.error('Failed to load program studies:', error)
  }
}

const downloadTemplate = async () => {
  try {
    downloadingTemplate.value = true
    const response = await axios.get('/api/lecturers/import/template-download', {
      responseType: 'blob'
    })

    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `template-import-dosen-${new Date().toISOString().split('T')[0]}.xlsx`)
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    window.URL.revokeObjectURL(url)

    toastStore.success('Template berhasil diunduh')
  } catch (error) {
    toastStore.handleError(error, 'download template')
  } finally {
    downloadingTemplate.value = false
  }
}

const handleFileSelect = (event) => {
  const file = event.target.files[0]
  if (file) {
    selectedFile.value = file
    isDragOver.value = false
  }
}

const handleFileDrop = (event) => {
  const file = event.dataTransfer.files[0]
  if (file && (file.type === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' ||
              file.type === 'application/vnd.ms-excel' ||
              file.type === 'text/csv')) {
    selectedFile.value = file
  } else {
    toastStore.error('File tidak valid. Silakan upload file Excel atau CSV.')
  }
  isDragOver.value = false
}

const removeFile = () => {
  selectedFile.value = null
}

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const validateFile = async () => {
  if (!selectedFile.value) return

  try {
    validating.value = true

    const formData = new FormData()
    formData.append('file', selectedFile.value)
    formData.append('skip_duplicates', importOptions.skipDuplicates ? '1' : '0')
    formData.append('update_existing', importOptions.updateExisting ? '1' : '0')
    formData.append('force_import_invalid', importOptions.forceImportInvalid ? '1' : '0')

    const response = await axios.post('/api/lecturers/import/validate', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })

    if (response.data.success) {
      validationResults.value = response.data.data
      // Initialize selection state
      validationResults.value.all_data = validationResults.value.all_data.map(item => ({
        ...item,
        selected: false
      }))
      currentStep.value = 2
      toastStore.success('File berhasil divalidasi')
    } else {
      toastStore.error('Validasi gagal', response.data.message)
    }
  } catch (error) {
    toastStore.handleError(error, 'validasi file')
  } finally {
    validating.value = false
  }
}

// Select/Unselect functionality
const toggleSelectAll = () => {
  const newValue = selectAllChecked.value
  validationResults.value.all_data.forEach(item => {
    item.selected = newValue
  })
  updateSelectedCount()
}

const updateSelectedCount = () => {
  selectedRowsCount.value = validationResults.value.all_data?.filter(item => item.selected).length || 0
  selectAllChecked.value = selectedRowsCount.value === validationResults.value.all_data?.length
}

const bulkDeleteSelected = () => {
  const selectedItems = validationResults.value.all_data.filter(item => item.selected)
  const count = selectedItems.length

  if (count === 0) return

  // Remove selected items from array
  validationResults.value.all_data = validationResults.value.all_data.filter(item => !item.selected)

  // Update counts
  validationResults.value.total_rows = validationResults.value.all_data.length
  validationResults.value.invalid_rows = validationResults.value.all_data.filter(item => !item.is_valid && !item.is_duplicate).length
  validationResults.value.valid_rows = validationResults.value.all_data.filter(item => item.is_valid && !item.is_duplicate).length
  validationResults.value.duplicate_rows = validationResults.value.all_data.filter(item => item.is_duplicate).length

  updateSelectedCount()
  toastStore.success(`${count} data berhasil dihapus`)
}

const revalidateEditedData = async () => {
  if (!validationResults.value.all_data || validationResults.value.all_data.length === 0) {
    toastStore.error('Tidak ada data untuk divalidasi ulang')
    return
  }

  try {
    validating.value = true

    const correctedData = validationResults.value.all_data.map(item => ({
      row_number: item.row_number,
      mapped_data: item.mapped_data
    }))

    const response = await axios.post('/api/lecturers/import/revalidate', {
      corrected_data: correctedData
    }, {
      headers: {
        'Content-Type': 'application/json'
      }
    })

    if (response.data.success) {
      const revalidationResults = response.data.data

      if (revalidationResults.invalid_data && revalidationResults.invalid_data.length > 0) {
        revalidationResults.invalid_data.forEach(revalidatedItem => {
          const originalIndex = validationResults.value.all_data.findIndex(item => item.row_number === revalidatedItem.row_number)
          if (originalIndex !== -1) {
            validationResults.value.all_data[originalIndex].is_valid = revalidatedItem.is_valid
            validationResults.value.all_data[originalIndex].errors = revalidatedItem.errors
            validationResults.value.all_data[originalIndex].errors_array = revalidatedItem.errors_array
            validationResults.value.all_data[originalIndex].mapped_data = revalidatedItem.mapped_data
          }
        })
      }

      validationResults.value.all_data.forEach(item => {
        const wasValidated = revalidationResults.invalid_data?.find(invalid => invalid.row_number === item.row_number)
        if (!wasValidated) {
          item.is_valid = true
          item.errors = null
          item.errors_array = []
        }
      })

      validationResults.value.invalid_rows = revalidationResults.invalid_rows || 0
      validationResults.value.valid_rows = revalidationResults.valid_rows || (validationResults.value.total_rows - validationResults.value.invalid_rows)

      if (revalidationResults.invalid_rows === 0) {
        toastStore.success('Semua data telah diperbaiki dan valid!')
        setTimeout(() => {
          currentStep.value = 3
        }, 1000)
      } else {
        toastStore.warning(`${revalidationResults.invalid_rows} data masih perlu diperbaiki`)
      }

    } else {
      toastStore.error('Revalidasi gagal', response.data.message)
    }
  } catch (error) {
    toastStore.handleError(error, 'revalidasi data')
  } finally {
    validating.value = false
  }
}

const processImport = async () => {
  try {
    importing.value = true

    const correctedData = validationResults.value.all_data.map(item => item.mapped_data)

    const response = await axios.post('/api/lecturers/import/process', {
      corrected_data: correctedData,
      skip_duplicates: importOptions.skipDuplicates,
      update_existing: importOptions.updateExisting,
      force_import_invalid: importOptions.forceImportInvalid
    }, {
      headers: {
        'Content-Type': 'application/json'
      }
    })

    if (response.data.success) {
      importResults.value = response.data.data
      showSuccessModal.value = true
      emit('import-success', importResults.value)

      toastStore.success()
    } else {
      toastStore.error('Import gagal', response.data.message)
    }
  } catch (error) {
    toastStore.handleError(error, 'import data')
  } finally {
    importing.value = false
  }
}

const closeSuccessModal = () => {
  showSuccessModal.value = false
  emit('close')
  emit('import-success', importResults.value)
}

// Quick Fix Functions
const applyProgramStudyToAll = () => {
  if (!selectedProgramStudy.value) return

  let updatedCount = 0
  validationResults.value.all_data.forEach(item => {
    // Always update departemen with selected program study (overwrite existing data)
    item.mapped_data.departemen = selectedProgramStudy.value
    updatedCount++

    // Always set fakultas to "Sekolah Pascasarjana" (overwrite existing data)
    item.mapped_data.fakultas = 'Sekolah Pascasarjana'
    updatedCount++
  })

  toastStore.success(
    'Program Studi & Fakultas Updated',
    `Departemen diisi dengan "${selectedProgramStudy.value}" dan Fakultas diisi dengan "Sekolah Pascasarjana" untuk ${validationResults.value.all_data.length} data`
  )

  // Don't call revalidateEditedData immediately to avoid date validation issues
  // revalidateEditedData()
}

const fixAllEnumFields = () => {
  let updatedCount = 0

  validationResults.value.all_data.forEach(item => {
    // Fix gender
    if (item.mapped_data.jenis_kelamin) {
      const gender = item.mapped_data.jenis_kelamin.toLowerCase().trim()
      if (gender === 'laki-laki' || gender === 'pria' || gender === 'male') {
        item.mapped_data.jenis_kelamin = 'L'
        updatedCount++
      } else if (gender === 'perempuan' || gender === 'wanita' || gender === 'female') {
        item.mapped_data.jenis_kelamin = 'P'
        updatedCount++
      }
    }

    // Fix status
    if (item.mapped_data.status_dosen_wajib) {
      const status = item.mapped_data.status_dosen_wajib.toLowerCase().trim()
      if (status === 'aktif' || status === 'active') {
        item.mapped_data.status_dosen_wajib = 'Aktif'
        updatedCount++
      } else if (status === 'cuti' || status === 'on_leave') {
        item.mapped_data.status_dosen_wajib = 'Cuti'
        updatedCount++
      } else if (status === 'tidak aktif' || status === 'non-aktif' || status === 'inactive') {
        item.mapped_data.status_dosen_wajib = 'Tidak'
        updatedCount++
      }
    }

    // Fix employment type
    if (item.mapped_data.jenis_pegawai_wajib) {
      const type = item.mapped_data.jenis_pegawai_wajib.toLowerCase().trim()
      if (type.includes('tetap') || type.includes('pns') || type.includes('permanent')) {
        item.mapped_data.jenis_pegawai_wajib = 'Tetap'
        updatedCount++
      } else if (type.includes('kontrak') || type.includes('contract')) {
        item.mapped_data.jenis_pegawai_wajib = 'Kontrak'
        updatedCount++
      } else if (type.includes('paruh') || type.includes('part-time')) {
        item.mapped_data.jenis_pegawai_wajib = 'Paruh'
        updatedCount++
      } else if (type.includes('tamu') || type.includes('guest')) {
        item.mapped_data.jenis_pegawai_wajib = 'Tamu'
        updatedCount++
      }
    }

    // Fix blood type
    if (item.mapped_data.golongan_darah) {
      const blood = item.mapped_data.golongan_darah.toUpperCase().trim()
      if (['A', 'B', 'AB', 'O'].includes(blood)) {
        item.mapped_data.golongan_darah = blood
        updatedCount++
      }
    }
  })

  toastStore.success(
    'Enum Fields Fixed',
    `${updatedCount} enum fields corrected`
  )
  revalidateEditedData()
}

const deleteAllDuplicates = () => {
  const duplicateCount = validationResults.value.duplicate_rows || 0
  if (duplicateCount === 0) return

  // Filter out duplicate rows
  validationResults.value.all_data = validationResults.value.all_data.filter(item => !item.is_duplicate)

  // Update counts
  validationResults.value.duplicate_rows = 0
  validationResults.value.total_rows = validationResults.value.all_data.length

  toastStore.success(
    'Duplicates Deleted',
    `${duplicateCount} duplicate rows removed`
  )
}

// Helper Functions
const getColumnWidth = (index) => {
  return `width: ${columns.value[index]?.width || '120px'}; min-width: ${columns.value[index]?.width || '120px'};`
}

const getPlaceholder = (column) => {
  const placeholders = {
    'nama_wajib': 'Nama lengkap dosen',
    'email_wajib': 'email@universitas.ac.id',
    'nip_nidn_wajib': '1234567890123456',
    'no_hp_wajib': '081234567890'
  }
  return placeholders[column.field] || ''
}

const getCellClass = (item, column) => {
  // First check if this item has any errors
  const hasErrors = item.errors_array && item.errors_array.length > 0

  if (hasErrors) {
    const hasFieldError = item.errors_array.some(error => {
      const lowerError = error.toLowerCase()
      const lowerField = column.field.toLowerCase()
      const lowerName = column.name.toLowerCase()

      // Check for "wajib diisi" (required field) errors
      if (lowerError.includes('wajib diisi') && column.field.includes('_wajib')) {
        return true
      }

      // Check for field-specific required errors
      if (lowerError.includes(`field ${lowerField} wajib diisi`)) {
        return true
      }

      // Check for specific error types by field
      if (column.field === 'email_wajib' && lowerError.includes('email')) {
        return true
      }
      if (column.field === 'nip_nidn_wajib' && (lowerError.includes('nip') || lowerError.includes('duplicate'))) {
        return true
      }
      if (column.field === 'no_hp_wajib' && lowerError.includes('hp')) {
        return true
      }

      // Check if the error mentions any part of the field name
      if (lowerError.includes(lowerField.replace('_wajib', '')) ||
          lowerError.includes(lowerName.replace(' (wajib)', ''))) {
        return true
      }

      return false
    })

    if (hasFieldError) {
      return 'bg-red-50 border-red-300 text-red-900 ring-1 ring-red-200'
    }
  }

  // Check for empty required fields (yellow highlight)
  if (column.required && (!item.mapped_data[column.field] || item.mapped_data[column.field] === '')) {
    return 'bg-yellow-50 border-yellow-300 text-yellow-900 ring-1 ring-yellow-200'
  }

  // Normal state
  return 'bg-white border-gray-200 text-gray-900 hover:bg-gray-50'
}

const getRowClass = (item) => {
  if (item.is_valid && !item.is_duplicate) {
    return 'bg-green-50'
  }
  if (item.is_duplicate) {
    return 'bg-yellow-50'
  }
  return 'bg-red-50'
}

const markAsChanged = (item) => {
  item.has_changed = true
}

const getColumnLetter = (index) => {
  let result = ''
  let i = index
  while (i >= 0) {
    result = String.fromCharCode(65 + (i % 26)) + result
    i = Math.floor(i / 26) - 1
  }
  return result || 'A'
}

const exportToCSV = () => {
  if (!validationResults.value.all_data) return

  const headers = columns.value.map(col => col.name).join(',')
  const rows = validationResults.value.all_data.map(item =>
    columns.value.map(col => `"${item.mapped_data[col.field] || ''}"`).join(',')
  )

  const csv = [headers, ...rows].join('\n')

  const blob = new Blob([csv], { type: 'text/csv' })
  const url = window.URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.href = url
  link.download = `validation-data-${new Date().toISOString().split('T')[0]}.csv`
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
  window.URL.revokeObjectURL(url)

  toastStore.success('Data berhasil diexport ke CSV')
}

// Quick Fix Functions
const autoFillProgramStudy = () => {
  if (!validationResults.value.all_data || programStudies.value.length === 0) return

  const defaultProgramStudy = programStudies.value[0]?.name || 'Teknik Informatika'
  let filledCount = 0

  validationResults.value.all_data.forEach(item => {
    if (!item.mapped_data.departemen || item.mapped_data.departemen.trim() === '') {
      item.mapped_data.departemen = defaultProgramStudy
      filledCount++
      markAsChanged(item)
    }
  })

  toastStore.success(`${filledCount} program studi kosong telah diisi`)
  setTimeout(() => revalidateEditedData(), 500)
}

const fixAllDates = () => {
  if (!validationResults.value.all_data) return

  let fixedCount = 0

  validationResults.value.all_data.forEach(item => {
    ['tanggal_lahir', 'tanggal_masuk'].forEach(field => {
      if (item.mapped_data[field]) {
        const fixedDate = fixDateFormat(item.mapped_data[field])
        if (fixedDate !== item.mapped_data[field]) {
          item.mapped_data[field] = fixedDate
          fixedCount++
          markAsChanged(item)
        }
      }
    })
  })

  toastStore.success(`${fixedCount} format tanggal telah diperbaiki`)
  setTimeout(() => revalidateEditedData(), 500)
}

const fixEmails = () => {
  if (!validationResults.value.all_data) return

  let fixedCount = 0

  validationResults.value.all_data.forEach(item => {
    if (item.mapped_data.email_wajib) {
      const fixedEmail = fixEmailFormat(item.mapped_data.email_wajib)
      if (fixedEmail !== item.mapped_data.email_wajib) {
        item.mapped_data.email_wajib = fixedEmail
        fixedCount++
        markAsChanged(item)
      }
    }
  })

  toastStore.success(`${fixedCount} format email telah diperbaiki`)
  setTimeout(() => revalidateEditedData(), 500)
}

const fixPhones = () => {
  if (!validationResults.value.all_data) return

  let fixedCount = 0

  validationResults.value.all_data.forEach(item => {
    if (item.mapped_data.no_hp_wajib) {
      const fixedPhone = fixPhoneNumber(item.mapped_data.no_hp_wajib)
      if (fixedPhone !== item.mapped_data.no_hp_wajib) {
        item.mapped_data.no_hp_wajib = fixedPhone
        fixedCount++
        markAsChanged(item)
      }
    }
  })

  toastStore.success(`${fixedCount} format nomor HP telah diperbaiki`)
  setTimeout(() => revalidateEditedData(), 500)
}

// Helper functions for fixing data
const fixDateFormat = (dateString) => {
  if (!dateString) return dateString

  // Handle Excel serial numbers
  if (/^\d{5}$/.test(dateString)) {
    const excelDate = parseInt(dateString)
    if (excelDate > 25000 && excelDate < 100000) {
      const adjustedDate = excelDate > 60 ? excelDate - 1 : excelDate
      const baseDate = new Date(1900, 0, 1)
      const resultDate = new Date(baseDate.getTime() + adjustedDate * 24 * 60 * 60 * 1000)
      return resultDate.toISOString().split('T')[0]
    }
  }

  return dateString
}

const fixEmailFormat = (email) => {
  if (!email) return email
  let fixedEmail = email.trim().toLowerCase()
  if (fixedEmail && !fixedEmail.includes('@')) {
    fixedEmail += '@example.com'
  }
  return fixedEmail
}

const fixPhoneNumber = (phone) => {
  if (!phone) return phone
  let fixedPhone = phone.replace(/[^\d+]/g, '')
  if (fixedPhone && !fixedPhone.startsWith('+62') && !fixedPhone.startsWith('0')) {
    fixedPhone = '+62' + fixedPhone
  } else if (fixedPhone && fixedPhone.startsWith('0')) {
    fixedPhone = '+62' + fixedPhone.substring(1)
  }
  return fixedPhone
}
</script>

<style scoped>
/* Custom scrollbar styling */
.overflow-auto::-webkit-scrollbar {
  height: 8px;
  width: 8px;
}

.overflow-auto::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 4px;
}

.overflow-auto::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 4px;
}

.overflow-auto::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}

/* Excel-like cell focus */
input:focus,
select:focus {
  outline: 2px solid #3b82f6;
  outline-offset: -1px;
}

/* Smooth transitions */
.transition-colors {
  transition-property: background-color, border-color, color, fill, stroke;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
}

/* Indeterminate checkbox state */
input[type="checkbox"]:indeterminate {
  background-color: #3b82f6;
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 16 16'%3e%3cpath stroke='%23fff' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M4 8h8'/%3e%3c/svg%3e");
  background-position: center;
  background-repeat: no-repeat;
}
</style>
