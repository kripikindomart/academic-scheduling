<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <!-- Background overlay -->
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="closeModal"></div>

      <!-- Modal panel -->
      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-white">
              {{ isEdit ? 'Edit Mata Kuliah' : 'Tambah Mata Kuliah' }}
            </h3>
            <button
              @click="closeModal"
              class="text-white hover:text-gray-200 transition-colors"
            >
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Form Content -->
        <form @submit.prevent="handleSubmit" class="px-6 py-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Course Name -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Nama Mata Kuliah <span class="text-red-500">*</span>
              </label>
              <input
                v-model="form.course_name"
                type="text"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                placeholder="Masukkan nama mata kuliah"
              />
            </div>

            <!-- Course Code -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Kode Mata Kuliah
              </label>
              <input
                v-model="form.course_code"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                placeholder="Masukkan kode mata kuliah"
              />
            </div>

            <!-- Lecturer -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Dosen Pengampu <span class="text-red-500">*</span>
              </label>
              <select
                v-model="form.lecturer_id"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
              >
                <option value="">Pilih Dosen</option>
                <option v-for="lecturer in lecturers" :key="lecturer.id" :value="lecturer.id">
                  {{ lecturer.name }}
                </option>
              </select>
            </div>

            <!-- Credits -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                SKS <span class="text-red-500">*</span>
              </label>
              <input
                v-model.number="form.credits"
                type="number"
                min="1"
                max="6"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                placeholder="Jumlah SKS"
              />
            </div>

            <!-- Category -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Kategori <span class="text-red-500">*</span>
              </label>
              <select
                v-model="form.category"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
              >
                <option value="">Pilih Kategori</option>
                <option value="Kuliah">Kuliah</option>
                <option value="UTS">UTS</option>
                <option value="UAS">UAS</option>
              </select>
            </div>

            <!-- Session Type -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Jenis Sesi <span class="text-red-500">*</span>
              </label>
              <select
                v-model="form.session_type"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
              >
                <option value="">Pilih Jenis Sesi</option>
                <option value="Teori">Teori</option>
                <option value="Praktikum">Praktikum</option>
                <option value="Seminar">Seminar</option>
                <option value="Studio">Studio</option>
              </select>
            </div>

            <!-- Description -->
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Deskripsi
              </label>
              <textarea
                v-model="form.description"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                placeholder="Deskripsi mata kuliah"
              ></textarea>
            </div>
          </div>

          <!-- Category Auto-Fill Info -->
          <div v-if="form.category" class="mt-4 p-3 bg-purple-50 rounded-lg">
            <div class="flex items-center">
              <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <div class="text-sm text-purple-800">
                <span v-if="form.category === 'Kuliah'">
                  <strong>Kuliah Regular:</strong> Sesi akan dijadwalkan sesuai jadwal reguler (16x pertemuan)
                </span>
                <span v-else-if="form.category === 'UTS'">
                  <strong>UTS:</strong> Sesi akan dijadwalkan di tengah semester dengan durasi khusus
                </span>
                <span v-else-if="form.category === 'UAS'">
                  <strong>UAS:</strong> Sesi akan dijadwalkan di akhir semester dengan durasi khusus
                </span>
              </div>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="mt-6 flex justify-end space-x-3">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="loading"
              class="px-4 py-2 bg-gradient-to-r from-purple-500 to-purple-600 text-white font-medium rounded-lg hover:from-purple-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="loading" class="flex items-center">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Menyimpan...
              </span>
              <span v-else>
                {{ isEdit ? 'Update' : 'Tambah' }} Mata Kuliah
              </span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, watch, onMounted } from 'vue'
import { useToastStore } from '@/stores/toast'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  course: {
    type: Object,
    default: null
  },
  isEdit: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close', 'saved'])

const toastStore = useToastStore()

// State
const loading = ref(false)
const lecturers = ref([])

// Form data
const form = reactive({
  course_name: '',
  course_code: '',
  lecturer_id: '',
  credits: 3,
  category: 'Kuliah',
  session_type: 'Teori',
  description: ''
})

// Methods
const resetForm = () => {
  Object.assign(form, {
    course_name: '',
    course_code: '',
    lecturer_id: '',
    credits: 3,
    category: 'Kuliah',
    session_type: 'Teori',
    description: ''
  })
}

const populateForm = () => {
  if (props.course) {
    Object.assign(form, {
      course_name: props.course.course_name || '',
      course_code: props.course.course_code || '',
      lecturer_id: props.course.lecturer_id || '',
      credits: props.course.credits || 3,
      category: props.course.category || 'Kuliah',
      session_type: props.course.session_type || 'Teori',
      description: props.course.description || ''
    })
  }
}

const fetchLecturers = async () => {
  try {
    const response = await fetch('/api/lecturers/for-scheduling', {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    })

    // Check if response is actually JSON before trying to parse
    const contentType = response.headers.get('content-type')
    if (!contentType || !contentType.includes('application/json')) {
      throw new Error('Response is not JSON - likely authentication error')
    }

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }

    const data = await response.json()
    lecturers.value = data.data || data
  } catch (error) {
    console.error('Error fetching lecturers:', error)

    // Provide fallback data on authentication errors
    lecturers.value = [
      { id: 1, name: 'Dr. Ahmad Wijaya', code: 'AW001' },
      { id: 2, name: 'Prof. Siti Nurhaliza', code: 'SN002' },
      { id: 3, name: 'Dr. Budi Santoso', code: 'BS003' },
      { id: 4, name: 'Prof. Diana Putri', code: 'DP004' }
    ]

    toastStore.addToast({
      type: 'warning',
      title: 'Warning',
      message: 'Menggunakan data dosen default - periksa koneksi API'
    })
  }
}

const validateForm = () => {
  const errors = []

  if (!form.course_name.trim()) {
    errors.push('Nama mata kuliah wajib diisi')
  }

  if (!form.lecturer_id) {
    errors.push('Dosen wajib dipilih')
  }

  if (!form.credits || form.credits < 1 || form.credits > 6) {
    errors.push('SKS harus antara 1-6')
  }

  if (!form.category) {
    errors.push('Kategori wajib dipilih')
  }

  if (!form.session_type) {
    errors.push('Jenis sesi wajib dipilih')
  }

  return errors
}

const handleSubmit = async () => {
  const errors = validateForm()
  if (errors.length > 0) {
    errors.forEach(error => {
      toastStore.addToast({
        type: 'error',
        title: 'Validation Error',
        message: error
      })
    })
    return
  }

  loading.value = true

  try {
    const courseData = {
      ...form,
      lecturer_name: lecturers.value.find(l => l.id === form.lecturer_id)?.name || ''
    }

    emit('saved', courseData)
  } catch (error) {
    console.error('Error saving course:', error)
    toastStore.handleError(error, props.isEdit ? 'updateCourse' : 'createCourse')
  } finally {
    loading.value = false
  }
}

const closeModal = () => {
  emit('close')
}

// Watchers
watch(() => props.show, (newShow) => {
  if (newShow) {
    if (props.isEdit) {
      populateForm()
    } else {
      resetForm()
    }
    fetchLecturers()
  }
})

// Lifecycle
onMounted(() => {
  if (props.show) {
    if (props.isEdit) {
      populateForm()
    }
    fetchLecturers()
  }
})
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