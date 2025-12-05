<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <!-- Background overlay -->
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="closeModal"></div>

      <!-- Modal panel -->
      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
        <!-- Modal Header with Steps -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-white">Buat Jadwal Baru</h3>
            <button
              @click="closeModal"
              class="text-white hover:text-gray-200 transition-colors"
            >
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <!-- Progress Steps -->
          <div class="flex items-center justify-between">
            <div v-for="(step, index) in steps" :key="index" class="flex items-center">
              <div
                :class="[
                  'flex items-center justify-center w-8 h-8 rounded-full text-sm font-medium transition-colors',
                  currentStep > index ? 'bg-green-400 text-white' :
                  currentStep === index + 1 ? 'bg-white text-green-600' :
                  'bg-green-300 text-white'
                ]"
              >
                <svg v-if="currentStep > index + 1" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span v-else>{{ index + 1 }}</span>
              </div>
              <span
                :class="[
                  'ml-2 text-sm font-medium',
                  currentStep === index + 1 ? 'text-white' : 'text-green-200'
                ]"
              >
                {{ step.title }}
              </span>
              <div v-if="index < steps.length - 1" class="w-12 h-0.5 bg-green-400 mx-4"></div>
            </div>
          </div>
        </div>

        <!-- Form Content -->
        <form @submit.prevent="handleStepSubmit" class="px-6 py-4">
          <!-- Step 1: Program & Class -->
          <div v-if="currentStep === 1" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Program Studi <span class="text-red-500">*</span>
                </label>
                <select
                  v-model="form.program_study_id"
                  required
                  @change="fetchClasses"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                >
                  <option value="">Pilih Program Studi</option>
                  <option v-for="program in programStudies" :key="program.id" :value="program.id">
                    {{ program.name }}
                  </option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Kelas <span class="text-red-500">*</span>
                </label>
                <select
                  v-model="form.class_id"
                  required
                  :disabled="!form.program_study_id"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 disabled:bg-gray-100"
                >
                  <option value="">Pilih Kelas</option>
                  <option v-for="classItem in classes" :key="classItem.id" :value="classItem.id">
                    {{ classItem.name }}
                  </option>
                </select>
              </div>
            </div>
          </div>

          <!-- Step 2: Academic Info -->
          <div v-if="currentStep === 2" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Tahun Akademik <span class="text-red-500">*</span>
                </label>
                <select
                  v-model="form.academic_year_id"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                >
                  <option value="">Pilih Tahun Akademik</option>
                  <option v-for="year in academicYears" :key="year.id" :value="year.id">
                    {{ year.name }}
                  </option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Semester <span class="text-red-500">*</span>
                </label>
                <select
                  v-model="form.semester"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                >
                  <option value="">Pilih Semester</option>
                  <option value="Ganjil">Ganjil</option>
                  <option value="Genap">Genap</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Hari <span class="text-red-500">*</span>
                </label>
                <select
                  v-model="form.day_of_week"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                >
                  <option value="">Pilih Hari</option>
                  <option value="Senin">Senin</option>
                  <option value="Selasa">Selasa</option>
                  <option value="Rabu">Rabu</option>
                  <option value="Kamis">Kamis</option>
                  <option value="Jumat">Jumat</option>
                  <option value="Sabtu">Sabtu</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Jam Mulai <span class="text-red-500">*</span>
                </label>
                <input
                  v-model="form.start_time"
                  type="time"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                >
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Jam Selesai <span class="text-red-500">*</span>
                </label>
                <input
                  v-model="form.end_time"
                  type="time"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                >
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Ruangan <span class="text-red-500">*</span>
                </label>
                <select
                  v-model="form.room_id"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                >
                  <option value="">Pilih Ruangan</option>
                  <option v-for="room in rooms" :key="room.id" :value="room.id">
                    {{ room.name }}
                  </option>
                </select>
              </div>
            </div>
          </div>

          <!-- Step 3: Course & Lecturer -->
          <div v-if="currentStep === 3" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Mata Kuliah <span class="text-red-500">*</span>
                </label>
                <select
                  v-model="form.course_id"
                  required
                  @change="onCourseChange"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                >
                  <option value="">Pilih Mata Kuliah</option>
                  <option v-for="course in courses" :key="course.id" :value="course.id">
                    {{ course.course_name }} ({{ course.course_code }})
                  </option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Dosen <span class="text-red-500">*</span>
                </label>
                <select
                  v-model="form.lecturer_id"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                >
                  <option value="">Pilih Dosen</option>
                  <option v-for="lecturer in lecturers" :key="lecturer.id" :value="lecturer.id">
                    {{ lecturer.name }}
                  </option>
                </select>
              </div>
            </div>

            <!-- Course Info Panel -->
            <div v-if="selectedCourse" class="p-4 bg-green-50 rounded-lg border border-green-200">
              <h4 class="font-semibold text-green-900 mb-3">Detail Mata Kuliah</h4>
              <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                  <span class="font-medium text-green-700">Kode:</span>
                  <span class="text-green-900 ml-1">{{ selectedCourse.course_code }}</span>
                </div>
                <div>
                  <span class="font-medium text-green-700">SKS:</span>
                  <span class="text-green-900 ml-1">{{ selectedCourse.credits }}</span>
                </div>
                <div>
                  <span class="font-medium text-green-700">Jenis:</span>
                  <span class="text-green-900 ml-1">{{ selectedCourse.session_type || '-' }}</span>
                </div>
                <div>
                  <span class="font-medium text-green-700">Kategori:</span>
                  <span class="text-green-900 ml-1">{{ selectedCourse.category || '-' }}</span>
                </div>
              </div>
              <div v-if="selectedCourse.description" class="mt-2 text-sm text-green-800">
                <span class="font-medium">Deskripsi:</span> {{ selectedCourse.description }}
              </div>
            </div>
          </div>

          <!-- Step 4: Review & Submit -->
          <div v-if="currentStep === 4" class="space-y-6">
            <div class="bg-gray-50 rounded-lg p-6">
              <h4 class="font-semibold text-gray-900 mb-4">Ringkasan Jadwal</h4>
              <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                  <span class="font-medium text-gray-600">Program Studi:</span>
                  <span class="text-gray-900 ml-2">{{ getSelectedProgramName() }}</span>
                </div>
                <div>
                  <span class="font-medium text-gray-600">Kelas:</span>
                  <span class="text-gray-900 ml-2">{{ getSelectedClassName() }}</span>
                </div>
                <div>
                  <span class="font-medium text-gray-600">Mata Kuliah:</span>
                  <span class="text-gray-900 ml-2">{{ getSelectedCourseName() }}</span>
                </div>
                <div>
                  <span class="font-medium text-gray-600">Dosen:</span>
                  <span class="text-gray-900 ml-2">{{ getSelectedLecturerName() }}</span>
                </div>
                <div>
                  <span class="font-medium text-gray-600">Hari:</span>
                  <span class="text-gray-900 ml-2">{{ form.day_of_week }}</span>
                </div>
                <div>
                  <span class="font-medium text-gray-600">Waktu:</span>
                  <span class="text-gray-900 ml-2">{{ form.start_time }} - {{ form.end_time }}</span>
                </div>
                <div>
                  <span class="font-medium text-gray-600">Ruangan:</span>
                  <span class="text-gray-900 ml-2">{{ getSelectedRoomName() }}</span>
                </div>
                <div>
                  <span class="font-medium text-gray-600">Semester:</span>
                  <span class="text-gray-900 ml-2">{{ getSelectedAcademicYearName() }} - {{ form.semester }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Navigation Buttons -->
          <div class="mt-8 flex justify-between">
            <button
              v-if="currentStep > 1"
              type="button"
              @click="previousStep"
              class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
            >
              ← Sebelumnya
            </button>
            <div v-else></div>

            <div class="flex space-x-3">
              <button
                type="button"
                @click="closeModal"
                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
              >
                Batal
              </button>
              <button
                v-if="currentStep < steps.length"
                type="button"
                @click="nextStep"
                :disabled="!isCurrentStepValid()"
                class="px-6 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Selanjutnya →
              </button>
              <button
                v-else
                type="submit"
                :disabled="loading"
                class="px-6 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white font-medium rounded-lg hover:from-green-600 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <span v-if="loading" class="flex items-center">
                  <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Menyimpan...
                </span>
                <span v-else>
                  Buat Jadwal
                </span>
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch } from 'vue'
import { useToastStore } from '@/stores/toast'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close', 'saved'])

const toastStore = useToastStore()

// State
const loading = ref(false)
const currentStep = ref(1)
const programStudies = ref([])
const classes = ref([])
const courses = ref([])
const lecturers = ref([])
const rooms = ref([])
const academicYears = ref([])
const selectedCourse = ref(null)

// Steps configuration
const steps = ref([
  { title: 'Program & Kelas' },
  { title: 'Info Akademik' },
  { title: 'Mata Kuliah' },
  { title: 'Konfirmasi' }
])

// Form data
const form = reactive({
  program_study_id: '',
  class_id: '',
  course_id: '',
  lecturer_id: '',
  day_of_week: '',
  start_time: '',
  end_time: '',
  room_id: '',
  academic_year_id: '',
  semester: ''
})

// Methods
const resetForm = () => {
  Object.assign(form, {
    program_study_id: '',
    class_id: '',
    course_id: '',
    lecturer_id: '',
    day_of_week: '',
    start_time: '',
    end_time: '',
    room_id: '',
    academic_year_id: '',
    semester: ''
  })
  selectedCourse.value = null
  currentStep.value = 1
  classes.value = []
}

const nextStep = () => {
  if (currentStep.value < steps.value.length) {
    currentStep.value++
  }
}

const previousStep = () => {
  if (currentStep.value > 1) {
    currentStep.value--
  }
}

const isCurrentStepValid = () => {
  switch (currentStep.value) {
    case 1:
      return form.program_study_id && form.class_id
    case 2:
      return form.academic_year_id && form.semester && form.day_of_week &&
             form.start_time && form.end_time && form.room_id
    case 3:
      return form.course_id && form.lecturer_id
    case 4:
      return true
    default:
      return false
  }
}

// Helper functions for summary
const getSelectedProgramName = () => {
  const program = programStudies.value.find(p => p.id === form.program_study_id)
  return program ? program.name : '-'
}

const getSelectedClassName = () => {
  const classItem = classes.value.find(c => c.id === form.class_id)
  return classItem ? classItem.name : '-'
}

const getSelectedCourseName = () => {
  const course = courses.value.find(c => c.id === form.course_id)
  return course ? course.course_name : '-'
}

const getSelectedLecturerName = () => {
  const lecturer = lecturers.value.find(l => l.id === form.lecturer_id)
  return lecturer ? lecturer.name : '-'
}

const getSelectedRoomName = () => {
  const room = rooms.value.find(r => r.id === form.room_id)
  return room ? room.name : '-'
}

const getSelectedAcademicYearName = () => {
  const year = academicYears.value.find(y => y.id === form.academic_year_id)
  return year ? year.name : '-'
}

const fetchProgramStudies = async () => {
  try {
    const response = await fetch('/api/program-studies', {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    })

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }

    const data = await response.json()
    programStudies.value = data.data || data
  } catch (error) {
    console.error('Error fetching program studies:', error)
    toastStore.addToast({
      type: 'error',
      title: 'Error',
      message: 'Gagal memuat data program studi'
    })
  }
}

const fetchClasses = async () => {
  if (!form.program_study_id) {
    classes.value = []
    return
  }

  try {
    const response = await fetch(`/api/classes/program-study/${form.program_study_id}`, {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    })

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }

    const data = await response.json()
    classes.value = data.data || data
  } catch (error) {
    console.error('Error fetching classes:', error)
    toastStore.addToast({
      type: 'error',
      title: 'Error',
      message: 'Gagal memuat data kelas'
    })
  }
}

const fetchCourses = async () => {
  try {
    const response = await fetch('/api/courses', {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    })

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }

    const data = await response.json()
    courses.value = data.data || data
  } catch (error) {
    console.error('Error fetching courses:', error)
    toastStore.addToast({
      type: 'error',
      title: 'Error',
      message: 'Gagal memuat data mata kuliah'
    })
  }
}

const fetchLecturers = async () => {
  // Only fetch lecturers if day and time are selected
  if (!form.day_of_week || !form.start_time) {
    lecturers.value = []
    return
  }

  try {
    const params = new URLSearchParams({
      day: form.day_of_week,
      time: form.start_time
    })

    const response = await fetch(`/api/lecturers/for-scheduling?${params}`, {
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

const fetchRooms = async () => {
  try {
    const response = await fetch('/api/rooms', {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    })

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }

    const data = await response.json()
    rooms.value = data.data || data
  } catch (error) {
    console.error('Error fetching rooms:', error)
    toastStore.addToast({
      type: 'error',
      title: 'Error',
      message: 'Gagal memuat data ruangan'
    })
  }
}

const fetchAcademicYears = async () => {
  try {
    const response = await fetch('/api/academic-years', {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    })

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }

    const data = await response.json()
    academicYears.value = data.data || data
  } catch (error) {
    console.error('Error fetching academic years:', error)
    toastStore.addToast({
      type: 'error',
      title: 'Error',
      message: 'Gagal memuat data tahun akademik'
    })
  }
}

const onCourseChange = () => {
  selectedCourse.value = courses.value.find(c => c.id === form.course_id) || null
}

const validateForm = () => {
  const errors = []

  if (!form.program_study_id) errors.push('Program studi wajib dipilih')
  if (!form.class_id) errors.push('Kelas wajib dipilih')
  if (!form.course_id) errors.push('Mata kuliah wajib dipilih')
  if (!form.lecturer_id) errors.push('Dosen wajib dipilih')
  if (!form.day_of_week) errors.push('Hari wajib dipilih')
  if (!form.start_time) errors.push('Jam mulai wajib diisi')
  if (!form.end_time) errors.push('Jam selesai wajib diisi')
  if (!form.room_id) errors.push('Ruangan wajib dipilih')
  if (!form.academic_year_id) errors.push('Tahun akademik wajib dipilih')
  if (!form.semester) errors.push('Semester wajib dipilih')

  // Time validation
  if (form.start_time && form.end_time && form.start_time >= form.end_time) {
    errors.push('Jam selesai harus setelah jam mulai')
  }

  return errors
}

const handleStepSubmit = async () => {
  // Only submit on final step
  if (currentStep.value < steps.value.length) {
    return
  }
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
    const response = await fetch('/api/schedules', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(form)
    })

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }

    const data = await response.json()
    toastStore.addToast({
      type: 'success',
      title: 'Berhasil',
      message: 'Jadwal berhasil dibuat'
    })

    emit('saved', data)
    closeModal()
  } catch (error) {
    console.error('Error creating schedule:', error)
    toastStore.addToast({
      type: 'error',
      title: 'Error',
      message: 'Gagal membuat jadwal'
    })
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
    resetForm()
    fetchProgramStudies()
    fetchCourses()
    fetchRooms()
    fetchAcademicYears()
  }
})

// Watch day and time changes to fetch lecturers
watch(() => [form.day_of_week, form.start_time], () => {
  if (form.day_of_week && form.start_time) {
    fetchLecturers()
  }
})

// Lifecycle
onMounted(() => {
  if (props.show) {
    fetchProgramStudies()
    fetchCourses()
    fetchRooms()
    fetchAcademicYears()
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