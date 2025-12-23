<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <!-- Background overlay -->
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="closeModal"></div>

      <!-- Modal panel -->
      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-white">
              Generate Jadwal Otomatis
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

        <form @submit.prevent="handleSubmit" class="px-6 py-4">
          <!-- Date Configuration -->
          <div class="mb-6 p-4 bg-blue-50 rounded-lg">
            <h4 class="text-sm font-semibold text-blue-800 mb-3 flex items-center">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
              Konfigurasi Tanggal
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Tanggal Mulai Kuliah <span class="text-red-500">*</span>
                </label>
                <input
                  v-model="form.start_date"
                  type="date"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Tanggal Akhir Kuliah <span class="text-red-500">*</span>
                </label>
                <input
                  v-model="form.end_date"
                  type="date"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                />
              </div>
            </div>
          </div>

          <!-- Study Days Selection -->
          <div class="mb-6 p-4 bg-green-50 rounded-lg">
            <h4 class="text-sm font-semibold text-green-800 mb-3 flex items-center">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              Hari Perkuliahan
            </h4>
            <div class="grid grid-cols-3 md:grid-cols-6 gap-2">
              <label
                v-for="day in studyDays"
                :key="day.value"
                class="flex items-center p-2 border rounded-lg cursor-pointer transition-colors"
                :class="form.study_days.includes(day.value)
                  ? 'bg-green-100 border-green-500 text-green-700'
                  : 'bg-white border-gray-300 hover:border-gray-400'"
              >
                <input
                  v-model="form.study_days"
                  type="checkbox"
                  :value="day.value"
                  class="sr-only"
                />
                <span class="text-sm font-medium text-center w-full">{{ day.label }}</span>
              </label>
            </div>
            <p class="mt-2 text-xs text-green-600">
              Pilih hari-hari dimana perkuliahan akan dijadwalkan
            </p>
          </div>

          <!-- Enrolled Courses -->
          <div class="mb-6 p-4 bg-purple-50 rounded-lg">
            <h4 class="text-sm font-semibold text-purple-800 mb-3 flex items-center">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
              </svg>
              Mata Kuliah Terdaftar
            </h4>

            <div v-if="enrolledCourses.length > 0" class="space-y-2">
              <div
                v-for="(course, index) in enrolledCourses"
                :key="course.id"
                class="flex items-center justify-between p-3 bg-white border rounded-lg"
              >
                <div class="flex-1">
                  <div>
                    <span class="font-medium">{{ course.course?.course_name || 'Unknown Course' }}</span>
                    <span class="text-sm text-gray-500 ml-2">({{ course.course?.course_code || 'N/A' }})</span>
                    <div class="text-sm text-gray-600">
                      {{ course.course?.credits || 0 }} SKS •
                      {{ course.lecturer?.name || 'No Lecturer' }}
                    </div>
                  </div>
                </div>
                <div class="text-xs text-gray-500">
                  <span class="text-green-600 font-medium">
                    ✓ Akan di-generate
                  </span>
                </div>
              </div>
            </div>

            <div v-else class="text-center py-4 text-gray-500">
              <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002 2v0a2 2 0 012-2h-2a2 2 0 01-2-2V0z"/>
              </svg>
              <p>Belum ada mata kuliah yang terdaftar</p>
              <p class="text-xs mt-1">Silakan tambahkan mata kuliah terlebih dahulu</p>
            </div>
          </div>

          <!-- Preview Summary -->
          <div v-if="form.study_days.length > 0 && enrolledCourses.length > 0" class="mb-6 p-4 bg-gray-50 rounded-lg">
            <h4 class="text-sm font-semibold text-gray-700 mb-2">Ringkasan Jadwal</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
              <div>
                <span class="text-gray-500">Total Minggu:</span>
                <span class="ml-2 font-medium">{{ totalWeeks }} minggu</span>
              </div>
              <div>
                <span class="text-gray-500">Total Hari Kuliah:</span>
                <span class="ml-2 font-medium">{{ totalStudyDays }} hari</span>
              </div>
              <div>
                <span class="text-gray-500">Total SKS:</span>
                <span class="ml-2 font-medium">{{ totalCredits }} SKS</span>
              </div>
            </div>
            <div class="mt-3">
              <span class="text-gray-500">Distribusi Online/Offline:</span>
              <div class="mt-1 flex items-center space-x-4">
                <div class="flex items-center">
                  <div class="w-24 bg-gray-200 rounded-full h-2">
                    <div
                      class="bg-blue-500 h-2 rounded-full"
                      :style="{ width: (classSchedule?.online_percentage || 0) + '%' }"
                    ></div>
                  </div>
                  <span class="ml-2 text-sm">{{ classSchedule?.online_percentage || 0 }}% Online</span>
                </div>
                <div class="flex items-center">
                  <div class="w-24 bg-gray-200 rounded-full h-2">
                    <div
                      class="bg-green-500 h-2 rounded-full"
                      :style="{ width: (classSchedule?.offline_percentage || 100) + '%' }"
                    ></div>
                  </div>
                  <span class="ml-2 text-sm">{{ classSchedule?.offline_percentage || 100 }}% Offline</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Helper message when form is incomplete -->
          <div v-if="!canSubmit" class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-lg">
            <div class="flex items-start">
              <svg class="w-5 h-5 text-amber-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <div class="text-sm text-amber-800">
                <p class="font-medium">Mohon lengkapi data berikut:</p>
                <ul class="mt-1 ml-4 list-disc space-y-1">
                  <li v-if="!form.start_date">Tanggal mulai kuliah</li>
                  <li v-if="!form.end_date">Tanggal akhir kuliah</li>
                  <li v-if="form.study_days.length === 0">Hari perkuliahan (minimal satu hari)</li>
                  <li v-if="enrolledCourses.length === 0">Mata kuliah (belum ada mata kuliah yang terdaftar)</li>
                </ul>
              </div>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="flex justify-end space-x-3">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
            >
              Batal
            </button>
            <!-- Only show Generate button when form is complete -->
            <button
              v-if="canSubmit"
              type="submit"
              :disabled="loading"
              class="px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-medium rounded-lg hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="loading" class="flex items-center">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Mengenerate Jadwal...
              </span>
              <span v-else>Generate Jadwal</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, watch, onMounted } from 'vue'
import { useToastStore } from '@/stores/toast'
import scheduleService from '@/services/scheduleService'
import courseService from '@/services/courseService'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  classSchedule: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['close', 'generated'])

const toastStore = useToastStore()

// State
const loading = ref(false)
const enrolledCourses = ref([])

// Form data
const form = reactive({
  start_date: '',
  end_date: '',
  study_days: []
})

// Study days options
const studyDays = ref([
  { value: 'senin', label: 'Senin' },
  { value: 'selasa', label: 'Selasa' },
  { value: 'rabu', label: 'Rabu' },
  { value: 'kamis', label: 'Kamis' },
  { value: 'jumat', label: 'Jumat' },
  { value: 'sabtu', label: 'Sabtu' }
])

// Computed properties
const canSubmit = computed(() => {
  return form.start_date &&
         form.end_date &&
         form.study_days.length > 0 &&
         enrolledCourses.value.length > 0
})

const totalWeeks = computed(() => {
  if (!form.start_date || !form.end_date) return 0
  const start = new Date(form.start_date)
  const end = new Date(form.end_date)
  const weeks = Math.ceil((end - start) / (7 * 24 * 60 * 60 * 1000))
  return weeks || 0
})

const totalStudyDays = computed(() => {
  return totalWeeks.value * form.study_days.length
})

const totalCredits = computed(() => {
  return enrolledCourses.value
    .reduce((total, course) => total + (course.course?.credits || 0), 0)
})

// Methods
const resetForm = () => {
  Object.assign(form, {
    start_date: '',
    end_date: '',
    study_days: []
  })
}

const fetchEnrolledCourses = async () => {
  if (!props.classSchedule?.id) return

  try {
    // Get class schedule details with enrolled courses
    const response = await fetch(`/api/class-schedules/${props.classSchedule.id}`, {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Accept': 'application/json'
      }
    })

    if (!response.ok) throw new Error('Failed to fetch class schedule')

    const data = await response.json()
    enrolledCourses.value = data.data?.details || []
  } catch (error) {
    console.error('Error fetching enrolled courses:', error)
    toastStore.addToast({
      type: 'error',
      title: 'Error',
      message: 'Gagal memuat data mata kuliah terdaftar'
    })
  }
}

const handleSubmit = async () => {
  if (!canSubmit.value || loading.value) return

  loading.value = true

  try {
    const payload = {
      start_date: form.start_date,
      end_date: form.end_date,
      study_days: form.study_days,
      courses: enrolledCourses.value.map(c => ({
        course_id: c.course_id,
        credits: c.course?.credits || 0,
        meetings_per_week: 1
      }))
    }

    const response = await scheduleService.autoGenerate(props.classSchedule.id, payload)

    // Handle response safely with default values
    const responseData = response || {}
    const totalGenerated = responseData.total_generated || 0
    const conflicts = responseData.conflicts || []

    toastStore.addToast({
      type: 'success',
      title: 'Berhasil',
      message: `Berhasil generate ${totalGenerated} detail jadwal`
    })

    if (conflicts.length > 0) {
      toastStore.addToast({
        type: 'warning',
        title: 'Konflik Terdeteksi',
        message: `${conflicts.length} jadwal memiliki konflik`
      })
    }

    emit('generated', responseData)
    closeModal()
  } catch (error) {
    console.error('Error generating schedule:', error)
    toastStore.addToast({
      type: 'error',
      title: 'Error',
      message: error.message || 'Gagal generate jadwal otomatis'
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
  if (newShow && props.classSchedule) {
    resetForm()
    fetchAvailableCourses()
  }
})

// Lifecycle
onMounted(() => {
  if (props.show && props.classSchedule) {
    fetchAvailableCourses()
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