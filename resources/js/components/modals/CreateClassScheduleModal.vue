<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 transition-opacity" aria-hidden="true">
        <div class="absolute inset-0 bg-gray-500 opacity-75" @click="$emit('close')"></div>
      </div>

      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
        <form @submit.prevent="handleSubmit">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="w-full">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-6">
                  Buat Kelas Jadwal Baru
                </h3>

                <!-- Basic Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Program Studi <span class="text-red-500">*</span>
                    </label>
                    <select
                      v-model="form.program_study_id"
                      @change="onProgramStudyChange"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                      required
                    >
                      <option value="">Pilih Program Studi</option>
                      <option v-for="ps in programStudies" :key="ps.id" :value="ps.id">
                        {{ ps.name }}
                      </option>
                    </select>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Kelas <span class="text-red-500">*</span>
                    </label>
                    <select
                      v-model="form.class_id"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                      required
                      :disabled="!form.program_study_id"
                    >
                      <option value="">Pilih Kelas</option>
                      <option v-for="cls in classes" :key="cls.id" :value="cls.id">
                        {{ cls.name }} ({{ cls.batch_year }})
                      </option>
                    </select>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Tahun Akademik <span class="text-red-500">*</span>
                    </label>
                    <select
                      v-model="form.academic_year_id"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                      required
                    >
                      <option value="">Pilih Tahun Akademik</option>
                      <option v-for="ay in academicYears" :key="ay.id" :value="ay.id">
                        {{ ay.academic_calendar_year }} - {{ ay.admission_period }}
                      </option>
                    </select>
                  </div>
                </div>

                <!-- Schedule Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Persentase Online (%)
                    </label>
                    <select
                      v-model.number="form.online_percentage"
                      @change="onOnlinePercentageChange"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                      required
                    >
                      <option value="">Pilih Persentase Online</option>
                      <option v-for="percentage in onlinePercentages" :key="percentage" :value="percentage">
                        {{ percentage }}%
                      </option>
                    </select>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Persentase Offline (%)
                    </label>
                    <input
                      v-model.number="form.offline_percentage"
                      type="number"
                      min="0"
                      max="100"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100"
                      readonly
                    >
                  </div>
                </div>

                <div class="mb-6">
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Judul Kelas Jadwal
                  </label>
                  <input
                    v-model="form.title"
                    type="text"
                    placeholder="Contoh: Jadwal Kelas A Angkatan 2023"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                  >
                </div>

                <div class="mb-6">
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Deskripsi
                  </label>
                  <textarea
                    v-model="form.description"
                    rows="3"
                    placeholder="Deskripsi kelas jadwal..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                  ></textarea>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              type="submit"
              :disabled="loading"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
            >
              <span v-if="loading">Menyimpan...</span>
              <span v-else>Buat Kelas Jadwal</span>
            </button>
            <button
              type="button"
              @click="$emit('close')"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Batal
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
    required: true
  }
})

const emit = defineEmits(['close', 'created'])

const toastStore = useToastStore()
const loading = ref(false)

// Form data
const form = reactive({
  program_study_id: '',
  class_id: '',
  academic_year_id: '',
  title: '',
  description: '',
  online_percentage: '',
  offline_percentage: 100
})

// Dropdown data
const programStudies = ref([])
const classes = ref([])
const academicYears = ref([])

// Online percentage options
const onlinePercentages = ref([10, 20, 30, 40, 50, 60, 70, 80, 90, 100])

// Fetch functions
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
    toastStore.addToast({
      type: 'error',
      title: 'Error',
      message: 'Gagal memuat data kelas'
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
    toastStore.addToast({
      type: 'error',
      title: 'Error',
      message: 'Gagal memuat data tahun akademik'
    })
  }
}

// Event handlers
const onProgramStudyChange = () => {
  form.class_id = ''
  fetchClasses()
}

const onOnlinePercentageChange = () => {
  const online = parseInt(form.online_percentage) || 0
  form.offline_percentage = 100 - online
}

const handleSubmit = async () => {
  if (loading.value) return

  // Validation
  if (!form.program_study_id || !form.class_id || !form.academic_year_id || !form.online_percentage) {
    toastStore.addToast({
      type: 'error',
      title: 'Error',
      message: 'Semua field wajib harus diisi'
    })
    return
  }

  loading.value = true

  try {
    const response = await fetch('/api/class-schedules', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(form)
    })

    if (!response.ok) {
      const errorData = await response.json()
      throw new Error(errorData.message || `HTTP error! status: ${response.status}`)
    }

    const data = await response.json()

    toastStore.addToast({
      type: 'success',
      title: 'Success',
      message: 'Kelas jadwal berhasil dibuat!'
    })

    emit('created', data.data)
    emit('close')

    // Reset form
    Object.assign(form, {
      program_study_id: '',
      class_id: '',
      academic_year_id: '',
      title: '',
      description: '',
      online_percentage: '',
      offline_percentage: 100
    })

  } catch (error) {
    toastStore.addToast({
      type: 'error',
      title: 'Error',
      message: error.message || 'Gagal membuat kelas jadwal'
    })
  } finally {
    loading.value = false
  }
}

// Watchers
watch(() => props.show, (newValue, oldValue) => {
  if (newValue) {
    fetchProgramStudies()
    fetchAcademicYears()
  }
}, { immediate: true })

// Also try mounted hook
onMounted(() => {
  if (props.show) {
    fetchProgramStudies()
    fetchAcademicYears()
  }
})

// Auto-generate title when class is selected
watch(() => form.class_id, (newClassId) => {
  if (newClassId) {
    const selectedClass = classes.value.find(c => c.id === newClassId)
    if (selectedClass && !form.title) {
      form.title = `Jadwal ${selectedClass.name} - ${selectedClass.batch_year}`
    }
  }
})
</script>