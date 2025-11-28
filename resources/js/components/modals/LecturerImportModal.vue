<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 transition-opacity" aria-hidden="true">
        <div class="absolute inset-0 bg-gray-500 opacity-75" @click="$emit('close')"></div>
      </div>

      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-6xl sm:w-full">
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
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Pilih File Excel/CSV
                    <span class="text-red-500">*</span>
                  </label>
                  <div
                    @click="$refs.fileInput.click()"
                    :class="[
                      'mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed rounded-lg cursor-pointer transition-all duration-200',
                      isDragOver ? 'border-blue-400 bg-blue-50' : 'border-gray-300 hover:border-gray-400'
                    ]"
                    @dragover.prevent="isDragOver = true"
                    @dragleave.prevent="isDragOver = false"
                    @drop.prevent="handleFileDrop"
                  >
                    <div class="space-y-1 text-center">
                      <svg
                        class="mx-auto h-12 w-12 text-gray-400"
                        stroke="currentColor"
                        fill="none"
                        viewBox="0 0 48 48"
                        aria-hidden="true"
                      >
                        <path
                          d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                          stroke-width="2"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                        />
                      </svg>
                      <div class="flex text-sm text-gray-600">
                        <label class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                          <span>Pilih file</span>
                          <input
                            ref="fileInput"
                            type="file"
                            class="sr-only"
                            accept=".xlsx,.xls,.csv"
                            @change="handleFileSelect"
                          >
                        </label>
                        <p class="pl-1">atau drag and drop</p>
                      </div>
                      <p class="text-xs text-gray-500">Format: XLSX, XLS, CSV (Maks 10MB)</p>
                    </div>
                  </div>
                  <input
                    ref="fileInput"
                    type="file"
                    class="sr-only"
                    accept=".xlsx,.xls,.csv"
                    @change="handleFileSelect"
                  >
                </div>

                <!-- File Preview -->
                <div v-if="selectedFile" class="bg-green-50 border border-green-200 rounded-lg p-4">
                  <div class="flex items-center justify-between">
                    <div class="flex items-center">
                      <svg class="w-8 h-8 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                      </svg>
                      <div>
                        <h4 class="text-sm font-semibold text-green-900">{{ selectedFile.name }}</h4>
                        <p class="text-xs text-green-700">{{ formatFileSize(selectedFile.size) }}</p>
                      </div>
                    </div>
                    <button
                      @click="removeFile"
                      class="text-red-600 hover:text-red-800 transition-colors"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                      </svg>
                    </button>
                  </div>
                </div>

                <!-- Import Options -->
                <div class="space-y-4">
                  <h4 class="text-sm font-semibold text-gray-900">Opsi Import</h4>

                  <label class="flex items-center space-x-3">
                    <input
                      v-model="importOptions.skipDuplicates"
                      type="checkbox"
                      class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    >
                    <span class="text-sm text-gray-700">Lewati data duplikat</span>
                  </label>

                  <label class="flex items-center space-x-3">
                    <input
                      v-model="importOptions.updateExisting"
                      type="checkbox"
                      class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    >
                    <span class="text-sm text-gray-700">Update data yang sudah ada</span>
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

              <!-- Step 2: Validation Results -->
              <div v-if="currentStep === 2" class="space-y-6">
                <!-- Summary -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                  <h4 class="text-sm font-semibold text-blue-900 mb-3">Hasil Validasi</h4>
                  <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center">
                      <div class="text-2xl font-bold text-blue-600">{{ validationResults.total_rows }}</div>
                      <div class="text-xs text-gray-600">Total Baris</div>
                    </div>
                    <div class="text-center">
                      <div class="text-2xl font-bold text-green-600">{{ validationResults.valid_rows }}</div>
                      <div class="text-xs text-gray-600">Valid</div>
                    </div>
                    <div class="text-center">
                      <div class="text-2xl font-bold text-red-600">{{ validationResults.invalid_rows }}</div>
                      <div class="text-xs text-gray-600">Invalid</div>
                    </div>
                    <div class="text-center">
                      <div class="text-2xl font-bold text-yellow-600">{{ validationResults.duplicate_rows }}</div>
                      <div class="text-xs text-gray-600">Duplikat</div>
                    </div>
                  </div>
                </div>

                <!-- Invalid Data Details -->
                <div v-if="validationResults.invalid_rows > 0" class="space-y-4">
                  <h4 class="text-sm font-semibold text-red-900">Data Invalid ({{ validationResults.invalid_rows }} baris)</h4>

                  <div class="bg-red-50 border border-red-200 rounded-lg max-h-60 overflow-y-auto">
                    <table class="min-w-full divide-y divide-red-200">
                      <thead class="bg-red-100">
                        <tr>
                          <th class="px-3 py-2 text-left text-xs font-medium text-red-900">Baris</th>
                          <th class="px-3 py-2 text-left text-xs font-medium text-red-900">Error</th>
                          <th class="px-3 py-2 text-left text-xs font-medium text-red-900">Data</th>
                        </tr>
                      </thead>
                      <tbody class="bg-white divide-y divide-red-200">
                        <tr v-for="(item, index) in validationResults.invalid_data.slice(0, 10)" :key="index">
                          <td class="px-3 py-2 text-sm text-gray-900">{{ item.row_number }}</td>
                          <td class="px-3 py-2 text-sm text-red-600">{{ item.errors }}</td>
                          <td class="px-3 py-2 text-xs text-gray-500 truncate">{{ JSON.stringify(item.data) }}</td>
                        </tr>
                      </tbody>
                    </table>

                    <div v-if="validationResults.invalid_data.length > 10" class="px-3 py-2 text-center text-sm text-gray-600 bg-red-50">
                      ... dan {{ validationResults.invalid_data.length - 10 }} baris lainnya
                    </div>
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3 pt-4">
                  <button
                    @click="currentStep = 1"
                    type="button"
                    class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                  >
                    Kembali
                  </button>
                  <button
                    @click="currentStep = 3"
                    type="button"
                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                  >
                    Lanjutkan
                  </button>
                </div>
              </div>

              <!-- Step 3: Confirmation -->
              <div v-if="currentStep === 3" class="space-y-6">
                <!-- Final Summary -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                  <h4 class="text-sm font-semibold text-yellow-900 mb-3">Konfirmasi Import</h4>
                  <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                      <span class="text-gray-600">Total data akan diimport:</span>
                      <span class="font-medium">{{ validationResults.valid_rows }} baris</span>
                    </div>
                    <div class="flex justify-between text-sm">
                      <span class="text-gray-600">Data duplikat akan dilewati:</span>
                      <span class="font-medium">{{ validationResults.duplicate_rows }} baris</span>
                    </div>
                    <div v-if="importOptions.updateExisting" class="flex justify-between text-sm">
                      <span class="text-gray-600">Data akan diupdate:</span>
                      <span class="font-medium">{{ validationResults.duplicate_rows }} baris</span>
                    </div>
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3 pt-4">
                  <button
                    @click="currentStep = 2"
                    type="button"
                    class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                  >
                    Kembali
                  </button>
                  <button
                    @click="processImport"
                    :disabled="importing"
                    type="button"
                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50"
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
              <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                  <div v-if="importResults" class="mt-3 space-y-1">
                    <div class="flex justify-between text-sm">
                      <span>Diimport:</span>
                      <span class="font-medium">{{ importResults.imported }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                      <span>Diupdate:</span>
                      <span class="font-medium">{{ importResults.updated }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                      <span>Dilewati:</span>
                      <span class="font-medium">{{ importResults.skipped }}</span>
                    </div>
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
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useToastStore } from '@/stores/toast'
import axios from 'axios'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close', 'import-success'])

const toastStore = useToastStore()

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

const importOptions = ref({
  skipDuplicates: true,
  updateExisting: false,
  forceImportInvalid: false
})

// Methods
const downloadTemplate = async () => {
  try {
    downloadingTemplate.value = true
    const response = await axios.get('/api/lecturers/import/template', {
      responseType: 'blob'
    })

    // Create download link
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
  }
}

const handleFileDrop = (event) => {
  isDragOver.value = false
  const file = event.dataTransfer.files[0]
  if (file && isValidFile(file)) {
    selectedFile.value = file
  }
}

const isValidFile = (file) => {
  const validTypes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel', 'text/csv']
  const validExtensions = ['.xlsx', '.xls', '.csv']

  return validTypes.includes(file.type) ||
         validExtensions.some(ext => file.name.toLowerCase().endsWith(ext))
}

const removeFile = () => {
  selectedFile.value = null
  if (document.getElementById('fileInput')) {
    document.getElementById('fileInput').value = ''
  }
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

    const response = await axios.post('/api/lecturers/import/validate', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })

    if (response.data.success) {
      validationResults.value = response.data.data
      currentStep.value = 2

      if (validationResults.value.invalid_rows === 0) {
        toastStore.success('File valid, dapat diimport')
      } else {
        toastStore.warning('File valid dengan beberapa data invalid')
      }
    } else {
      toastStore.error('Validasi gagal', response.data.message)
    }
  } catch (error) {
    toastStore.handleError(error, 'validasi file')
  } finally {
    validating.value = false
  }
}

const processImport = async () => {
  try {
    importing.value = true

    const formData = new FormData()
    formData.append('file', selectedFile.value)
    formData.append('skip_duplicates', importOptions.value.skipDuplicates ? '1' : '0')
    formData.append('update_existing', importOptions.value.updateExisting ? '1' : '0')
    formData.append('force_import_invalid', importOptions.value.forceImportInvalid ? '1' : '0')

    const response = await axios.post('/api/lecturers/import/process', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })

    if (response.data.success) {
      importResults.value = response.data.data
      showSuccessModal.value = true
      emit('import-success', importResults.value)

      toastStore.success(`Import berhasil! ${importResults.value.imported} data diimport, ${importResults.value.updated} data diupdate`)
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
}
</script>