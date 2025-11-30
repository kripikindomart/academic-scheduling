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
                <div class="flex items-center">
                  <div class="p-2 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg mr-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                  </div>
                  <h3 class="text-2xl leading-6 font-bold text-gray-900">
                    Import Data Mata Kuliah
                  </h3>
                </div>
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
                        'flex items-center justify-center w-12 h-12 rounded-full text-sm font-semibold transition-all duration-300',
                        currentStep >= 1 ? 'bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-lg' : 'bg-gray-200 text-gray-600'
                      ]"
                    >
                      <svg v-if="currentStep > 1" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                      </svg>
                      <span v-else>1</span>
                    </div>
                    <span class="ml-3 text-sm font-medium">Upload File</span>
                  </div>
                  <div class="flex-1 h-1 mx-4 rounded-full transition-all duration-300" :class="currentStep >= 2 ? 'bg-gradient-to-r from-blue-500 to-indigo-600' : 'bg-gray-200'"></div>
                  <div class="flex items-center">
                    <div
                      :class="[
                        'flex items-center justify-center w-12 h-12 rounded-full text-sm font-semibold transition-all duration-300',
                        currentStep >= 2 ? 'bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-lg' : 'bg-gray-200 text-gray-600'
                      ]"
                    >
                      <svg v-if="currentStep > 2" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                      </svg>
                      <span v-else>2</span>
                    </div>
                    <span class="ml-3 text-sm font-medium">Validasi & Edit</span>
                  </div>
                  <div class="flex-1 h-1 mx-4 rounded-full transition-all duration-300" :class="currentStep >= 3 ? 'bg-gradient-to-r from-blue-500 to-indigo-600' : 'bg-gray-200'"></div>
                  <div class="flex items-center">
                    <div
                      :class="[
                        'flex items-center justify-center w-12 h-12 rounded-full text-sm font-semibold transition-all duration-300',
                        currentStep >= 3 ? 'bg-gradient-to-br from-green-500 to-emerald-600 text-white shadow-lg' : 'bg-gray-200 text-gray-600'
                      ]"
                    >
                      <svg v-if="currentStep > 3" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                      </svg>
                      <span v-else>3</span>
                    </div>
                    <span class="ml-3 text-sm font-medium">Import Data</span>
                  </div>
                </div>
              </div>

              <!-- Step 1: File Upload -->
              <div v-if="currentStep === 1" class="space-y-6">
                <!-- Template Download -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-6 shadow-sm">
                  <div class="flex items-center">
                    <div class="p-3 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg mr-4">
                      <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                      </svg>
                    </div>
                    <div class="flex-1">
                      <h4 class="text-lg font-bold text-gray-900">Template Excel</h4>
                      <p class="text-sm text-gray-600 mt-1">Download template format import mata kuliah yang sudah disediakan</p>
                      <div class="mt-3 flex items-center gap-3">
                        <button
                          @click="downloadTemplate"
                          :disabled="downloadingTemplate"
                          class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-semibold rounded-lg hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 shadow-lg transition-all duration-200 transform hover:scale-105"
                        >
                          <svg v-if="downloadingTemplate" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                          </svg>
                          <svg v-else class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                          </svg>
                          {{ downloadingTemplate ? 'Mengunduh...' : 'Download Template' }}
                        </button>
                        <div class="text-xs text-gray-500">
                          <span class="inline-flex items-center">
                            <svg class="w-4 h-4 mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Format: XLSX, XLS, CSV (Maks. 10MB)
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- File Upload Area -->
                <div class="relative">
                  <div
                    class="border-3 border-dashed border-gray-300 rounded-2xl p-8 text-center transition-all duration-300"
                    :class="{
                      'border-blue-400 bg-gradient-to-br from-blue-50 to-indigo-50 shadow-lg': isDragOver,
                      'hover:border-gray-400 hover:bg-gray-50': !isDragOver
                    }"
                    @dragover.prevent="isDragOver = true"
                    @dragleave.prevent="isDragOver = false"
                    @drop.prevent="handleFileDrop">

                    <div v-if="!selectedFile" class="space-y-4">
                      <div class="mx-auto w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                      </div>
                      <div>
                        <label for="file-upload" class="cursor-pointer">
                          <span class="text-lg font-semibold text-gray-900 block">
                            Upload File Excel Mata Kuliah
                          </span>
                          <span class="text-sm text-gray-500 block mt-1">
                            Drag & drop file di sini atau klik untuk browse
                          </span>
                          <input id="file-upload" name="file-upload" type="file" class="sr-only" accept=".xlsx,.xls,.csv" @change="handleFileSelect">
                        </label>
                      </div>
                    </div>

                    <div v-if="selectedFile" class="space-y-4">
                      <div class="mx-auto w-20 h-20 bg-gradient-to-br from-green-100 to-emerald-100 rounded-full flex items-center justify-center">
                        <svg class="w-10 h-10 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                      </div>
                      <div class="text-center">
                        <p class="text-lg font-semibold text-gray-900">{{ selectedFile.name }}</p>
                        <p class="text-sm text-gray-500">{{ formatFileSize(selectedFile.size) }}</p>
                        <button @click="removeFile" class="mt-3 text-sm text-red-600 hover:text-red-800 font-medium">
                          Hapus File
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Import Options -->
                <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl p-6 shadow-sm">
                  <h4 class="text-lg font-bold text-gray-900 mb-4">Pengaturan Import</h4>
                  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <label class="flex items-start space-x-3 p-3 bg-white rounded-lg hover:shadow-md transition-shadow cursor-pointer">
                      <input
                        v-model="importOptions.skipDuplicates"
                        type="checkbox"
                        class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                      >
                      <div>
                        <span class="text-sm font-medium text-gray-900 block">Lewati Duplikat</span>
                        <span class="text-xs text-gray-500">Tidak import data yang sudah ada</span>
                      </div>
                    </label>

                    <label class="flex items-start space-x-3 p-3 bg-white rounded-lg hover:shadow-md transition-shadow cursor-pointer">
                      <input
                        v-model="importOptions.updateExisting"
                        type="checkbox"
                        class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                      >
                      <div>
                        <span class="text-sm font-medium text-gray-900 block">Update Data</span>
                        <span class="text-xs text-gray-500">Perbarui data yang sudah ada</span>
                      </div>
                    </label>

                    <label class="flex items-start space-x-3 p-3 bg-white rounded-lg hover:shadow-md transition-shadow cursor-pointer">
                      <input
                        v-model="importOptions.validateOnly"
                        type="checkbox"
                        class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                      >
                      <div>
                        <span class="text-sm font-medium text-gray-900 block">Validasi Saja</span>
                        <span class="text-xs text-gray-500">Hanya validasi, tidak import</span>
                      </div>
                    </label>
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3 pt-6 border-t">
                  <button
                    @click="$emit('close')"
                    type="button"
                    class="px-6 py-3 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                  >
                    Batal
                  </button>
                  <button
                    @click="validateFile"
                    :disabled="!selectedFile || validating"
                    type="button"
                    class="px-6 py-3 border border-transparent rounded-xl shadow-lg text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 transform hover:scale-105"
                  >
                    <svg v-if="validating" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ validating ? 'Memvalidasi...' : 'Validasi File' }}
                  </button>
                </div>
              </div>

              <!-- Step 2: Validation Results -->
              <div v-if="currentStep === 2" class="space-y-6">
                <!-- Summary Dashboard -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                  <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-5 shadow-lg">
                    <div class="flex items-center">
                      <div class="p-3 bg-blue-500 rounded-xl">
                        <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                      </div>
                      <div class="ml-4">
                        <p class="text-sm font-medium text-blue-900">Total Data</p>
                        <p class="text-3xl font-bold text-blue-600">{{ validationResults.total_rows || 0 }}</p>
                      </div>
                    </div>
                  </div>

                  <div class="bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-xl p-5 shadow-lg">
                    <div class="flex items-center">
                      <div class="p-3 bg-green-500 rounded-xl">
                        <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                      </div>
                      <div class="ml-4">
                        <p class="text-sm font-medium text-green-900">Valid</p>
                        <p class="text-3xl font-bold text-green-600">{{ validationResults.valid_rows || 0 }}</p>
                      </div>
                    </div>
                  </div>

                  <div class="bg-gradient-to-br from-red-50 to-red-100 border border-red-200 rounded-xl p-5 shadow-lg">
                    <div class="flex items-center">
                      <div class="p-3 bg-red-500 rounded-xl">
                        <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                      </div>
                      <div class="ml-4">
                        <p class="text-sm font-medium text-red-900">Error</p>
                        <p class="text-3xl font-bold text-red-600">{{ validationResults.invalid_rows || 0 }}</p>
                      </div>
                    </div>
                  </div>

                  <div class="bg-gradient-to-br from-yellow-50 to-orange-100 border border-yellow-200 rounded-xl p-5 shadow-lg">
                    <div class="flex items-center">
                      <div class="p-3 bg-yellow-500 rounded-xl">
                        <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.314 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                      </div>
                      <div class="ml-4">
                        <p class="text-sm font-medium text-yellow-900">Peringatan</p>
                        <p class="text-3xl font-bold text-yellow-600">{{ validationResults.warning_rows || 0 }}</p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Quick Fix Tools -->
                <div v-if="validationResults.all_data && validationResults.all_data.length > 0"
                     class="bg-white rounded-xl shadow-sm border border-gray-200">
                  <!-- Header -->
                  <div class="px-6 py-5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-t-xl">
                    <div class="flex items-center justify-between">
                      <div class="flex items-center">
                        <div class="p-3 bg-white/20 rounded-xl mr-4">
                          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                          </svg>
                        </div>
                        <div>
                          <h4 class="text-base font-bold">Quick Fix Tools</h4>
                          <p class="text-sm text-white/90">Perbaiki data dengan cepat dan otomatis</p>
                        </div>
                      </div>
                      <div class="text-right">
                        <span class="text-2xl font-bold">{{ validationResults.all_data.length }}</span>
                        <span class="text-sm block text-white/80">data perlu diperiksa</span>
                      </div>
                    </div>
                  </div>

                  <!-- Tools Grid -->
                  <div class="p-6 space-y-4">
                    <!-- Program Study Fix -->
                    <div class="flex items-center gap-4 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-200">
                      <div class="flex-shrink-0">
                        <div class="p-3 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl">
                          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                          </svg>
                        </div>
                      </div>
                      <div class="flex-1">
                        <h5 class="text-base font-bold text-gray-900">Program Studi</h5>
                        <p class="text-sm text-gray-600">Auto-fill program studi yang kosong</p>
                      </div>
                      <div class="flex items-center gap-3">
                        <select v-model="selectedProgramStudy"
                                class="text-sm px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white shadow-sm">
                          <option value="">Pilih Program Studi</option>
                          <option v-for="ps in programStudies" :key="ps.id" :value="ps.name">
                            {{ ps.name }}
                          </option>
                        </select>
                        <button @click="applyProgramStudyToAll"
                                :disabled="!selectedProgramStudy"
                                class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-bold rounded-lg hover:from-blue-700 hover:to-indigo-700 disabled:from-gray-300 disabled:to-gray-400 disabled:cursor-not-allowed transition-all duration-200 shadow-lg">
                          Apply All
                        </button>
                      </div>
                    </div>

                    <!-- Course Type Fix -->
                    <div class="flex items-center gap-4 p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl border border-purple-200">
                      <div class="flex-shrink-0">
                        <div class="p-3 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl">
                          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                          </svg>
                        </div>
                      </div>
                      <div class="flex-1">
                        <h5 class="text-base font-bold text-gray-900">Tipe Mata Kuliah</h5>
                        <p class="text-sm text-gray-600">Perbaiki field tipe mata kuliah</p>
                      </div>
                      <div class="flex gap-2">
                        <button @click="fixAllCourseTypes"
                                class="px-5 py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 text-white text-sm font-bold rounded-lg hover:from-purple-700 hover:to-pink-700 transition-all duration-200 shadow-lg">
                          Fix All Types
                        </button>
                      </div>
                    </div>

                    <!-- Semester Fix -->
                    <div class="flex items-center gap-4 p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border border-green-200">
                      <div class="flex-shrink-0">
                        <div class="p-3 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl">
                          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                          </svg>
                        </div>
                      </div>
                      <div class="flex-1">
                        <h5 class="text-base font-bold text-gray-900">Semester</h5>
                        <p class="text-sm text-gray-600">Perbaiki format semester</p>
                      </div>
                      <div class="flex gap-2">
                        <button @click="fixAllSemesters"
                                class="px-5 py-2.5 bg-gradient-to-r from-green-600 to-emerald-600 text-white text-sm font-bold rounded-lg hover:from-green-700 hover:to-emerald-700 transition-all duration-200 shadow-lg">
                          Fix Semesters
                        </button>
                      </div>
                    </div>

                    <!-- Auto Fill Tools -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                      <button @click="autoFillDefaults"
                              class="flex items-center justify-center gap-3 p-4 bg-gradient-to-r from-orange-50 to-yellow-50 hover:from-orange-100 hover:to-yellow-100 border border-orange-200 rounded-xl transition-all duration-200">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <div class="text-left">
                          <span class="text-sm font-bold text-orange-800 block">Auto Fill</span>
                          <span class="text-xs text-orange-600">Isi data kosong</span>
                        </div>
                      </button>

                      <button @click="fixAllCredits"
                              class="flex items-center justify-center gap-3 p-4 bg-gradient-to-r from-cyan-50 to-blue-50 hover:from-cyan-100 hover:to-blue-100 border border-cyan-200 rounded-xl transition-all duration-200">
                        <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08.402-2.599 1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="text-left">
                          <span class="text-sm font-bold text-cyan-800 block">SKS Default</span>
                          <span class="text-xs text-cyan-600">Set SKS = 3</span>
                        </div>
                      </button>

                      <button @click="fixAllActiveStatus"
                              class="flex items-center justify-center gap-3 p-4 bg-gradient-to-r from-teal-50 to-green-50 hover:from-teal-100 hover:to-green-100 border border-teal-200 rounded-xl transition-all duration-200">
                        <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="text-left">
                          <span class="text-sm font-bold text-teal-800 block">Status Aktif</span>
                          <span class="text-xs text-teal-600">Set semua aktif</span>
                        </div>
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Data Table -->
                <div v-if="validationResults.all_data && validationResults.all_data.length > 0" class="space-y-4">
                  <!-- Table Header -->
                  <div class="bg-gray-100 px-5 py-3 border border-gray-300 rounded-t-lg">
                    <div class="flex items-center justify-between">
                      <div class="flex items-center space-x-3">
                        <input
                          v-model="selectAllChecked"
                          @change="toggleSelectAll"
                          type="checkbox"
                          class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                          :indeterminate="selectedRowsCount > 0 && selectedRowsCount < validationResults.all_data.length"
                        >
                        <span class="text-sm text-gray-600 font-medium">
                          {{ selectedRowsCount }} dari {{ validationResults.all_data.length }} dipilih
                        </span>
                      </div>
                      <div class="flex items-center gap-2">
                        <button v-if="selectedRowsCount > 0"
                                @click="bulkDeleteSelected"
                                class="text-sm px-3 py-1.5 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors">
                          🗑️ Hapus {{ selectedRowsCount }}
                        </button>
                        <button @click="exportToCSV" class="text-sm px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200">
                          📊 Export CSV
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Table -->
                  <div class="border border-gray-300 rounded-b-lg overflow-hidden">
                    <div class="overflow-auto" style="height: 500px; width: 100%;">
                      <table class="w-full" style="min-width: 2000px; border-collapse: collapse;">
                        <!-- Header -->
                        <thead class="sticky top-0 z-10 bg-gray-50">
                          <tr>
                            <th class="sticky left-0 z-20 px-3 py-3 text-xs font-bold text-gray-700 text-center border-r border-b border-gray-300 bg-gray-100 w-16">
                              #
                            </th>
                            <th class="sticky left-0 z-20 px-3 py-3 text-xs font-bold text-gray-700 border-r border-b border-gray-300 bg-gray-100 w-24">
                              Status
                            </th>
                            <th v-for="(column, index) in columns" :key="index"
                                class="px-3 py-3 text-xs font-bold text-gray-700 border-r border-b border-gray-300"
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
                            <td class="sticky left-0 z-10 px-3 py-2 text-xs text-gray-600 text-center border-r border-b border-gray-300 bg-gray-50">
                              {{ item.row_number }}
                            </td>

                            <td class="sticky left-0 z-10 px-2 py-2 border-r border-b border-gray-300 bg-gray-50">
                              <span v-if="item.is_valid"
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                ✓ Valid
                              </span>
                              <span v-else-if="item.is_duplicate"
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800">
                                ⚠ Dup
                              </span>
                              <span v-else
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">
                                ✗ Error
                              </span>
                            </td>

                            <!-- Editable Cells -->
                            <td v-for="(column, colIndex) in columns" :key="colIndex"
                                class="px-2 py-2 border-r border-b border-gray-300"
                                :style="getColumnWidth(colIndex)">
                              <!-- Dropdown for select fields -->
                              <select v-if="column.type === 'select'"
                                      v-model="item.mapped_data[column.field]"
                                      @change="markAsChanged(item)"
                                      class="w-full px-2 py-1.5 text-xs border rounded-lg focus:ring-2 focus:ring-blue-500 focus:bg-white transition-colors"
                                      :class="getCellClass(item, column)">
                                <option value="">Pilih {{ column.name }}</option>
                                <option v-for="option in column.options" :key="option" :value="option">
                                  {{ option }}
                                </option>
                              </select>

                              <!-- Number input for numeric fields -->
                              <input v-else-if="column.type === 'number'"
                                     v-model="item.mapped_data[column.field]"
                                     type="number"
                                     min="0"
                                     @change="markAsChanged(item)"
                                     class="w-full px-2 py-1.5 text-xs border rounded-lg focus:ring-2 focus:ring-blue-500 focus:bg-white transition-colors"
                                     :class="getCellClass(item, column)">

                              <!-- Text input for other fields -->
                              <input v-else
                                     v-model="item.mapped_data[column.field]"
                                     type="text"
                                     @change="markAsChanged(item)"
                                     class="w-full px-2 py-1.5 text-xs border rounded-lg focus:ring-2 focus:ring-blue-500 focus:bg-white transition-colors"
                                     :class="getCellClass(item, column)"
                                     :placeholder="getPlaceholder(column)">
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-between pt-6 border-t">
                  <button
                    @click="currentStep = 1"
                    type="button"
                    class="px-6 py-3 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                  >
                    ← Kembali
                  </button>
                  <div class="flex space-x-3">
                    <button
                      @click="revalidateEditedData"
                      :disabled="validating"
                      type="button"
                      class="px-5 py-3 border border-orange-300 rounded-xl text-sm font-medium text-orange-700 bg-orange-50 hover:bg-orange-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 disabled:opacity-50 transition-colors"
                    >
                      <svg v-if="validating" class="animate-spin -ml-1 mr-2 h-5 w-5 text-orange-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                      {{ validating ? 'Merevalidasi...' : 'Validasi Ulang' }}
                    </button>
                    <button
                      v-if="validationResults.invalid_rows === 0"
                      @click="currentStep = 3"
                      type="button"
                      class="px-6 py-3 border border-transparent rounded-xl shadow-lg text-sm font-medium text-white bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 transform hover:scale-105"
                    >
                      Lanjut ke Import →
                    </button>
                  </div>
                </div>
              </div>

              <!-- Step 3: Confirmation -->
              <div v-if="currentStep === 3" class="space-y-6">
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl p-8">
                  <div class="flex items-center">
                    <div class="p-4 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full mr-6">
                      <svg class="h-12 w-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                    </div>
                    <div class="flex-1">
                      <h4 class="text-2xl font-bold text-green-900">Data Siap Diimport!</h4>
                      <p class="text-lg text-green-700 mt-2">
                        {{ validationResults.valid_rows }} data valid dan siap untuk diimport ke sistem.
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Import Settings Summary -->
                <div class="bg-gray-50 rounded-xl p-6">
                  <h4 class="text-lg font-bold text-gray-900 mb-4">Ringkasan Import</h4>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-3">
                      <div class="flex justify-between items-center p-3 bg-white rounded-lg">
                        <span class="text-sm text-gray-600">Data yang akan diimport:</span>
                        <span class="text-lg font-bold text-blue-600">{{ validationResults.valid_rows }}</span>
                      </div>
                      <div class="flex justify-between items-center p-3 bg-white rounded-lg">
                        <span class="text-sm text-gray-600">Duplikat akan dilewati:</span>
                        <span class="text-lg font-bold text-yellow-600">{{ validationResults.duplicate_rows || 0 }}</span>
                      </div>
                    </div>
                    <div class="space-y-3">
                      <div class="flex justify-between items-center p-3 bg-white rounded-lg">
                        <span class="text-sm text-gray-600">Update data yang ada:</span>
                        <span class="text-lg font-bold text-purple-600">{{ importOptions.updateExisting ? 'Ya' : 'Tidak' }}</span>
                      </div>
                      <div class="flex justify-between items-center p-3 bg-white rounded-lg">
                        <span class="text-sm text-gray-600">Mode validasi:</span>
                        <span class="text-lg font-bold text-green-600">{{ importOptions.validateOnly ? 'Validasi Saja' : 'Import Langsung' }}</span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-between pt-6 border-t">
                  <button
                    @click="currentStep = 2"
                    type="button"
                    class="px-6 py-3 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                  >
                    ← Kembali
                  </button>
                  <div class="flex space-x-3">
                    <button
                      @click="$emit('close')"
                      type="button"
                      class="px-6 py-3 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                    >
                      Batal
                    </button>
                    <button
                      v-if="!importOptions.validateOnly"
                      @click="processImport"
                      :disabled="importing"
                      type="button"
                      class="px-8 py-3 border border-transparent rounded-xl shadow-lg text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 transition-all duration-200 transform hover:scale-105"
                    >
                      <svg v-if="importing" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                      {{ importing ? 'Mengimport...' : 'Import Data' }}
                    </button>
                    <button
                      v-else
                      @click="validateOnlyComplete"
                      class="px-8 py-3 border border-transparent rounded-xl shadow-lg text-sm font-medium text-white bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 transform hover:scale-105"
                    >
                      Selesai Validasi
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
                  <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-gradient-to-br from-green-400 to-emerald-500 sm:mx-0 sm:h-10 sm:w-10">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                  </div>
                  <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                      Import Berhasil!
                    </h3>
                    <div class="mt-2">
                      <p class="text-sm text-gray-500">
                        Data mata kuliah berhasil diimport ke sistem.
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
                  class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-base font-medium text-white hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
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
  validateOnly: false
})

// Column definitions with dropdown options for course import
const columns = ref([
  { name: 'Kode MK', field: 'course_code', type: 'text', required: true, width: '120px' },
  { name: 'Nama MK', field: 'course_name', type: 'text', required: true, width: '200px' },
  { name: 'Deskripsi', field: 'description', type: 'text', required: false, width: '250px' },
  { name: 'SKS', field: 'credits', type: 'number', required: true, width: '80px' },
  { name: 'Semester', field: 'semester', type: 'text', required: true, width: '100px' },
  { name: 'Tahun Akademik', field: 'academic_year', type: 'text', required: false, width: '120px' },
  { name: 'Tipe', field: 'course_type', type: 'select', required: true, width: '120px', options: ['mandatory', 'elective'] },
  { name: 'Level', field: 'level', type: 'select', required: false, width: '100px', options: ['S1', 'S2', 'S3'] },
  { name: 'Kapasitas', field: 'capacity', type: 'number', required: false, width: '100px' },
  { name: 'Program Studi', field: 'program_study_name', type: 'select', required: true, width: '180px', options: [] },
  { name: 'Status Aktif', field: 'is_active', type: 'select', required: false, width: '100px', options: ['true', 'false'] }
])

// Load program studies on mount
onMounted(async () => {
  await loadProgramStudies()
})

// Methods
const loadProgramStudies = async () => {
  try {
    const response = await axios.get('/api/program-studies')
    if (response.data.success) {
      programStudies.value = response.data.data
      const programStudyColumn = columns.value.find(col => col.field === 'program_study_name')
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
    const response = await axios.get('/api/courses/template-download', {
      responseType: 'blob'
    })

    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `template-import-mata-kuliah-${new Date().toISOString().split('T')[0]}.xlsx`)
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
    formData.append('validate_only', importOptions.validateOnly ? '1' : '0')

    const response = await axios.post('/api/courses/import/validate', formData, {
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

    const response = await axios.post('/api/courses/import/revalidate', {
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

    const response = await axios.post('/api/courses/import/process', {
      corrected_data: correctedData,
      skip_duplicates: importOptions.skipDuplicates,
      update_existing: importOptions.updateExisting
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

const validateOnlyComplete = () => {
  showSuccessModal.value = true
  emit('import-success', {
    validated: validationResults.value.valid_rows || 0,
    invalid: validationResults.value.invalid_rows || 0
  })
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
    if (!item.mapped_data.program_study_name || item.mapped_data.program_study_name.trim() === '') {
      item.mapped_data.program_study_name = selectedProgramStudy.value
      updatedCount++
      markAsChanged(item)
    }
  })

  toastStore.success(`${updatedCount} program studi kosong telah diisi`)
  setTimeout(() => revalidateEditedData(), 500)
}

const fixAllCourseTypes = () => {
  let updatedCount = 0

  validationResults.value.all_data.forEach(item => {
    if (item.mapped_data.course_type) {
      const type = item.mapped_data.course_type.toLowerCase().trim()
      if (type.includes('wajib') || type.includes('mandatory') || type.includes('required')) {
        item.mapped_data.course_type = 'mandatory'
        updatedCount++
      } else if (type.includes('pilihan') || type.includes('elective') || type.includes('opsional')) {
        item.mapped_data.course_type = 'elective'
        updatedCount++
      }
    }
  })

  toastStore.success(`${updatedCount} tipe mata kuliah telah diperbaiki`)
  setTimeout(() => revalidateEditedData(), 500)
}

const fixAllSemesters = () => {
  let updatedCount = 0

  validationResults.value.all_data.forEach(item => {
    if (item.mapped_data.semester) {
      const semester = item.mapped_data.semester.toString().toLowerCase().trim()
      if (semester.includes('ganjil') || semester.includes('odd')) {
        item.mapped_data.semester = 'ganjil'
        updatedCount++
      } else if (semester.includes('genap') || semester.includes('even')) {
        item.mapped_data.semester = 'genap'
        updatedCount++
      } else if (!['ganjil', 'genap'].includes(semester)) {
        // Default to ganjil if not recognized
        item.mapped_data.semester = 'ganjil'
        updatedCount++
      }
    }
  })

  toastStore.success(`${updatedCount} format semester telah diperbaiki`)
  setTimeout(() => revalidateEditedData(), 500)
}

const autoFillDefaults = () => {
  let updatedCount = 0

  validationResults.value.all_data.forEach(item => {
    // Fill default credits if empty
    if (!item.mapped_data.credits || item.mapped_data.credits === '' || item.mapped_data.credits === 0) {
      item.mapped_data.credits = 3
      updatedCount++
    }

    // Fill default capacity if empty
    if (!item.mapped_data.capacity || item.mapped_data.capacity === '' || item.mapped_data.capacity === 0) {
      item.mapped_data.capacity = 50
      updatedCount++
    }

    // Fill default academic year if empty
    if (!item.mapped_data.academic_year || item.mapped_data.academic_year === '') {
      const currentYear = new Date().getFullYear()
      item.mapped_data.academic_year = `${currentYear}/${currentYear + 1}`
      updatedCount++
    }

    // Fill default level if empty
    if (!item.mapped_data.level || item.mapped_data.level === '') {
      item.mapped_data.level = 'S2'
      updatedCount++
    }

    markAsChanged(item)
  })

  toastStore.success(`${updatedCount} field default telah diisi`)
  setTimeout(() => revalidateEditedData(), 500)
}

const fixAllCredits = () => {
  let updatedCount = 0

  validationResults.value.all_data.forEach(item => {
    if (item.mapped_data.credits) {
      const credits = parseInt(item.mapped_data.credits)
      if (isNaN(credits) || credits < 1 || credits > 6) {
        item.mapped_data.credits = 3
        updatedCount++
        markAsChanged(item)
      }
    }
  })

  toastStore.success(`${updatedCount} SKS telah dinormalisasi ke 3`)
  setTimeout(() => revalidateEditedData(), 500)
}

const fixAllActiveStatus = () => {
  let updatedCount = 0

  validationResults.value.all_data.forEach(item => {
    const currentValue = item.mapped_data.is_active?.toString().toLowerCase().trim()
    if (!currentValue || currentValue === '') {
      item.mapped_data.is_active = 'true'
      updatedCount++
      markAsChanged(item)
    }
  })

  toastStore.success(`${updatedCount} status aktif telah diset ke 'Aktif'`)
  setTimeout(() => revalidateEditedData(), 500)
}

// Helper Functions
const getColumnWidth = (index) => {
  return `width: ${columns.value[index]?.width || '120px'}; min-width: ${columns.value[index]?.width || '120px'};`
}

const getPlaceholder = (column) => {
  const placeholders = {
    'course_code': 'KODE123',
    'course_name': 'Nama Mata Kuliah',
    'credits': '3',
    'semester': 'ganjil/genap',
    'academic_year': '2024/2025',
    'capacity': '50'
  }
  return placeholders[column.field] || ''
}

const getCellClass = (item, column) => {
  const hasErrors = item.errors_array && item.errors_array.length > 0

  if (hasErrors) {
    const hasFieldError = item.errors_array.some(error => {
      const lowerError = error.toLowerCase()
      const lowerField = column.field.toLowerCase()
      const lowerName = column.name.toLowerCase()

      if (lowerError.includes(`field ${lowerField}`) ||
          lowerError.includes(lowerName) ||
          lowerError.includes(lowerField.replace('_', ' '))) {
        return true
      }

      return false
    })

    if (hasFieldError) {
      return 'bg-red-50 border-red-300 text-red-900 ring-1 ring-red-200'
    }
  }

  if (column.required && (!item.mapped_data[column.field] || item.mapped_data[column.field] === '')) {
    return 'bg-yellow-50 border-yellow-300 text-yellow-900 ring-1 ring-yellow-200'
  }

  return 'bg-white border-gray-200 text-gray-900 hover:bg-gray-50'
}

const getRowClass = (item) => {
  if (item.is_valid) {
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
  link.download = `validation-data-mata-kuliah-${new Date().toISOString().split('T')[0]}.csv`
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
  window.URL.revokeObjectURL(url)

  toastStore.success('Data berhasil diexport ke CSV')
}
</script>

<style scoped>
/* Custom scrollbar styling */
.overflow-auto::-webkit-scrollbar {
  height: 10px;
  width: 10px;
}

.overflow-auto::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 6px;
}

.overflow-auto::-webkit-scrollbar-thumb {
  background: linear-gradient(to bottom, #c1c1c1, #a8a8a8);
  border-radius: 6px;
}

.overflow-auto::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(to bottom, #a8a8a8, #959595);
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
  transition-duration: 200ms;
}

/* Indeterminate checkbox state */
input[type="checkbox"]:indeterminate {
  background-color: #3b82f6;
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 16 16'%3e%3cpath stroke='%23fff' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M4 8h8'/%3e%3c/svg%3e");
  background-position: center;
  background-repeat: no-repeat;
}

/* Loading spinner animation */
@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}
</style>