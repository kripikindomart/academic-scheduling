<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 transition-opacity" aria-hidden="true">
        <div class="absolute inset-0 bg-gray-500 opacity-75" @click="$emit('close')"></div>
      </div>

      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
              <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
              </svg>
            </div>
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
              <h3 class="text-lg leading-6 font-medium text-gray-900">
                Impor Data {{ getModuleName() }}
              </h3>
              <div class="mt-2">
                <p class="text-sm text-gray-500">
                  Upload file Excel/CSV untuk mengimpor data {{ getModuleName() }} secara massal.
                </p>
              </div>

              <!-- File Upload -->
              <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih File</label>
                <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors">
                  <div class="space-y-1 text-center">
                    <svg v-if="!selectedFile" class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                      <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <div v-else class="mx-auto h-12 w-12 text-green-400 flex items-center justify-center">
                      <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                    </div>
                    <div class="flex text-sm text-gray-600">
                      <label
                        for="file-upload"
                        class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500"
                      >
                        <span>Upload file</span>
                        <input id="file-upload" name="file-upload" type="file" class="sr-only" @change="handleFileChange" accept=".xlsx,.xls,.csv">
                      </label>
                      <p class="pl-1">atau drag and drop</p>
                    </div>
                    <p class="text-xs text-gray-500">XLSX, XLS, CSV hingga 10MB</p>
                    <div v-if="selectedFile" class="mt-2 text-sm text-green-600">
                      File terpilih: {{ selectedFile.name }}
                    </div>
                  </div>
                </div>
              </div>

              <!-- Template Download -->
              <div class="mt-4 p-3 bg-gray-50 rounded-md">
                <p class="text-sm text-gray-600 mb-2">Download template untuk format yang benar:</p>
                <button
                  @click="downloadTemplate"
                  type="button"
                  class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                  Download Template
                </button>
              </div>

              <!-- Import Options -->
              <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Opsi Impor</label>
                <div class="space-y-2">
                  <label class="flex items-center">
                    <input
                      v-model="importOptions.skipDuplicates"
                      type="checkbox"
                      class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    >
                    <span class="ml-2 text-sm text-gray-700">Lewati data duplikat</span>
                  </label>
                  <label class="flex items-center">
                    <input
                      v-model="importOptions.updateExisting"
                      type="checkbox"
                      class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    >
                    <span class="ml-2 text-sm text-gray-700">Update data yang sudah ada</span>
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button
            type="button"
            @click="handleImport"
            :disabled="loading || !selectedFile"
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
          >
            <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Impor Data
          </button>
          <button
            type="button"
            @click="$emit('close')"
            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
          >
            Batal
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useToastStore } from '@/stores/toast'

const props = defineProps({
  show: {
    type: Boolean,
    required: true
  },
  module: {
    type: String,
    required: true
  }
})

const emit = defineEmits(['close', 'imported'])

const toastStore = useToastStore()
const loading = ref(false)
const selectedFile = ref(null)

const importOptions = ref({
  skipDuplicates: true,
  updateExisting: false
})

const getModuleName = () => {
  const moduleNames = {
    'lecturers': 'Dosen',
    'students': 'Mahasiswa',
    'courses': 'Mata Kuliah',
    'rooms': 'Ruangan',
    'buildings': 'Gedung',
    'program-studies': 'Program Studi'
  }
  return moduleNames[props.module] || props.module
}

const handleFileChange = (event) => {
  const file = event.target.files[0]
  if (file) {
    // Check file size (10MB max)
    if (file.size > 10 * 1024 * 1024) {
      toastStore.error('Error', 'Ukuran file maksimal 10MB')
      event.target.value = ''
      return
    }

    // Check file type
    const allowedTypes = [
      'application/vnd.ms-excel',
      'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
      'text/csv'
    ]

    if (!allowedTypes.includes(file.type) && !file.name.match(/\.(xlsx|xls|csv)$/i)) {
      toastStore.error('Error', 'File harus berformat Excel (XLS, XLSX) atau CSV')
      event.target.value = ''
      return
    }

    selectedFile.value = file
  }
}

const downloadTemplate = () => {
  // Create a simple template file URL
  const templateUrl = `/templates/${props.module}-template.xlsx`

  // Create a temporary link element to download the template
  const link = document.createElement('a')
  link.href = templateUrl
  link.download = `${props.module}-template.xlsx`
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)

  toastStore.success('Berhasil', 'Template berhasil diunduh')
}

const handleImport = async () => {
  if (!selectedFile.value) {
    toastStore.error('Error', 'Pilih file terlebih dahulu')
    return
  }

  loading.value = true

  try {
    const formData = new FormData()
    formData.append('file', selectedFile.value)
    formData.append('skip_duplicates', importOptions.value.skipDuplicates)
    formData.append('update_existing', importOptions.value.updateExisting)

    // Dynamically import the appropriate service
    let service
    switch (props.module) {
      case 'lecturers':
        service = (await import('@/services/lecturerService')).default
        break
      case 'rooms':
        service = (await import('@/services/roomService')).default
        break
      case 'buildings':
        service = (await import('@/services/buildingService')).default
        break
      case 'program-studies':
        service = (await import('@/services/programStudyService')).default
        break
      default:
        throw new Error('Module not supported yet')
    }

    const response = await service.import(formData)

    toastStore.success(
      'Berhasil',
      `${response.imported || 0} data berhasil diimpor${response.errors ? ` (${response.errors} error)` : ''}`
    )

    emit('imported', response)
  } catch (error) {
    toastStore.handleError(error, 'Impor data')
  } finally {
    loading.value = false
  }
}
</script>