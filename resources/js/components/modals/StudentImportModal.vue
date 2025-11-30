<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 transition-opacity" aria-hidden="true">
        <div class="absolute inset-0 bg-gray-500 opacity-75" @click="$emit('close')"></div>
      </div>

      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div class="w-full">
              <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                Import Data Mahasiswa
              </h3>

              <!-- Step 1: Download Template -->
              <div v-if="step === 1" class="space-y-6">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                  <div class="flex items-start">
                    <svg class="h-6 w-6 text-blue-600 mt-1 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                      <h4 class="text-lg font-semibold text-blue-900 mb-2">Panduan Import Data Mahasiswa</h4>
                      <p class="text-blue-700 text-sm">
                        Untuk memastikan data mahasiswa berhasil diimport dengan benar, ikuti langkah-langkah berikut:
                      </p>
                      <ol class="mt-3 text-sm text-blue-700 space-y-1 list-decimal list-inside">
                        <li>Download template Excel yang telah disediakan</li>
                        <li>Isi data mahasiswa sesuai format template</li>
                        <li>Perhatikan kolom dengan tanda (*) wajib diisi</li>
                        <li>Upload file yang sudah diisi</li>
                        <li>Review hasil import dan konfirmasi</li>
                      </ol>
                    </div>
                  </div>
                </div>

                <div class="flex justify-end">
                  <button
                    @click="step = 2"
                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium"
                  >
                    Lanjutkan Upload
                  </button>
                </div>
              </div>

              <!-- Step 2: Upload File -->
              <div v-if="step === 2" class="space-y-6">
                <div
                  @dragover.prevent="dragOver = true"
                  @dragleave.prevent="dragOver = false"
                  @drop.prevent="handleFileDrop"
                  class="border-2 border-dashed rounded-lg p-8 text-center transition-colors"
                  :class="[
                    dragOver ? 'border-green-500 bg-green-50' : 'border-gray-300',
                    file ? 'border-blue-500 bg-blue-50' : ''
                  ]"
                >
                  <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                  </svg>
                  <div class="mt-4">
                    <label for="file-upload" class="cursor-pointer">
                      <span class="mt-2 block text-sm font-medium text-gray-900">
                        Klik untuk upload atau drag and drop file disini
                      </span>
                      <input
                        id="file-upload"
                        type="file"
                        @change="handleFileChange"
                        accept=".xlsx,.xls,.csv"
                        class="sr-only"
                      >
                    </label>
                    <p class="mt-1 text-xs text-gray-500">
                      Format file: XLSX, XLS, atau CSV (Maks. 10MB)
                    </p>
                  </div>

                  <div v-if="file" class="mt-4">
                    <div class="flex items-center justify-between bg-white p-3 rounded-lg border">
                      <div class="flex items-center">
                        <svg class="h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm font-medium text-gray-900">{{ file.name }}</span>
                      </div>
                      <button
                        @click="removeFile"
                        class="text-red-500 hover:text-red-700"
                      >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>

                <div class="flex justify-between">
                  <button
                    @click="step = 1"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
                  >
                    Kembali
                  </button>
                  <button
                    @click="uploadFile"
                    :disabled="!file || uploading"
                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    {{ uploading ? 'Mengupload...' : 'Import Data' }}
                  </button>
                </div>
              </div>

              <!-- Step 3: Results -->
              <div v-if="step === 3" class="space-y-6">
                <div :class="[
                  'rounded-lg p-6',
                  importResults.success ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200'
                ]">
                  <div class="flex items-center">
                    <svg :class="[
                      'h-6 w-6 mr-3',
                      importResults.success ? 'text-green-600' : 'text-red-600'
                    ]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path v-if="importResults.success" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                      <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                      <h4 :class="[
                        'text-lg font-semibold',
                        importResults.success ? 'text-green-900' : 'text-red-900'
                      ]">
                        {{ importResults.success ? 'Import Berhasil!' : 'Import Gagal!' }}
                      </h4>
                      <p :class="[
                        'text-sm mt-1',
                        importResults.success ? 'text-green-700' : 'text-red-700'
                      ]">
                        {{ importResults.message }}
                      </p>
                    </div>
                  </div>

                  <div v-if="importResults.success" class="mt-4 text-sm text-green-700">
                    <p><strong>Data berhasil diimport:</strong> {{ importResults.data?.imported_count || 0 }} mahasiswa</p>
                  </div>

                  <div v-if="importResults.errors && importResults.errors.length > 0" class="mt-4">
                    <h5 class="text-sm font-medium text-red-900 mb-2">Error:</h5>
                    <ul class="text-sm text-red-700 space-y-1">
                      <li v-for="(error, index) in importResults.errors" :key="index">{{ error }}</li>
                    </ul>
                  </div>
                </div>

                <div class="flex justify-end">
                  <button
                    @click="finishImport"
                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium"
                  >
                    Selesai
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer Actions -->
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button
            @click="handleClose"
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-gray-600 text-base font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:ml-3 sm:w-auto sm:text-sm"
          >
            Tutup
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useToastStore } from '@/stores/toast'
import studentService from '@/services/studentService'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close', 'import-success'])

const toastStore = useToastStore()

const step = ref(1)
const file = ref(null)
const dragOver = ref(false)
const downloading = ref(false)
const uploading = ref(false)
const importResults = ref({
  success: false,
  message: '',
  data: null,
  errors: []
})

const downloadTemplate = async () => {
  try {
    downloading.value = true
    const response = await studentService.downloadTemplate()

    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', 'students_template.xlsx')
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    window.URL.revokeObjectURL(url)

    toastStore.success('Berhasil', 'Template berhasil diunduh')
  } catch (error) {
    console.error('Error downloading template:', error)
    toastStore.error('Error', 'Gagal mengunduh template')
  } finally {
    downloading.value = false
  }
}

const handleFileChange = (event) => {
  const selectedFile = event.target.files[0]
  if (selectedFile) {
    file.value = selectedFile
  }
}

const handleFileDrop = (event) => {
  const droppedFile = event.dataTransfer.files[0]
  if (droppedFile) {
    file.value = droppedFile
  }
  dragOver.value = false
}

const removeFile = () => {
  file.value = null
}

const uploadFile = async () => {
  if (!file.value) return

  const formData = new FormData()
  formData.append('file', file.value)

  try {
    uploading.value = true
    const response = await studentService.import(formData)

    importResults.value = {
      success: true,
      message: response.message || 'Data berhasil diimport',
      data: response.data || null,
      errors: []
    }

    step.value = 3
    emit('import-success')

  } catch (error) {
    console.error('Error importing file:', error)
    importResults.value = {
      success: false,
      message: error.response?.data?.message || 'Gagal mengimport data',
      data: null,
      errors: error.response?.data?.errors ? Object.values(error.response.data.errors).flat() : [error.message]
    }

    step.value = 3
  } finally {
    uploading.value = false
  }
}

const finishImport = () => {
  handleClose()
}

const handleClose = () => {
  step.value = 1
  file.value = null
  importResults.value = {
    success: false,
    message: '',
    data: null,
    errors: []
  }
  emit('close')
}
</script>