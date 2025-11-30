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
                <div class="relative bg-gradient-to-r from-blue-600 to-blue-800 px-6 py-6">
                  <!-- Title with Class Info -->
                  <div class="flex items-center justify-between">
                    <div>
                      <h3 class="text-2xl font-bold text-white">Daftar Mahasiswa</h3>
                      <p class="mt-1 text-blue-100">
                        Kelas: {{ selectedClass?.name }} - {{ selectedClass?.program_study?.name }}
                      </p>
                      <p class="text-blue-200 text-sm">
                        {{ selectedClass?.academic_year }} | Kapasitas: {{ currentEnrolled }}/{{ selectedClass?.capacity }}
                      </p>
                    </div>

                    <!-- Close Button -->
                    <button
                      @click="handleClose"
                      class="rounded-full bg-white/20 p-2 text-white hover:bg-white/30 transition-colors"
                      :disabled="loading"
                    >
                      <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                      </svg>
                    </button>
                  </div>
                </div>

                <!-- Body -->
                <div class="px-6 py-6">
                  <!-- Search and Actions -->
                  <div class="mb-6 flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                    <div class="flex-1 max-w-md">
                      <div class="relative">
                        <input
                          v-model="searchQuery"
                          type="text"
                          placeholder="Cari mahasiswa (NIM, Nama, Email)..."
                          class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          :disabled="loading"
                        >
                        <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                      </div>
                    </div>

                    <div class="flex items-center gap-3">
                      <button
                        @click="loadStudents"
                        :disabled="loading"
                        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                      >
                        <svg v-if="loading" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <svg v-else class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span class="ml-2">Refresh</span>
                      </button>

                      <button
                        @click="openEnrollModal"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
                      >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        <span class="ml-2">Tambah Mahasiswa</span>
                      </button>

                      <button
                        v-if="hasSelectedStudents"
                        @click="handleBulkRemove"
                        :disabled="bulkDeleting"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
                      >
                        <svg v-if="!bulkDeleting" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <svg v-else class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="ml-2">{{ bulkDeleting ? 'Menghapus...' : `Hapus ${selectedStudents.length} Mahasiswa` }}</span>
                      </button>
                    </div>
                  </div>

                  <!-- Students Table -->
                  <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                      <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                          <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                              <input
                                type="checkbox"
                                :checked="isAllSelected"
                                :indeterminate="isIndeterminate"
                                @change="toggleSelectAll"
                                class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                              />
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                              Nama Lengkap
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                              Email
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                              Angkatan
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                              Tanggal Enrollment
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                              Aksi
                            </th>
                          </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                          <!-- Loading State -->
                          <tr v-if="loading && students.length === 0">
                            <td colspan="6" class="px-6 py-12 text-center">
                              <div class="flex flex-col items-center">
                                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mb-4"></div>
                                <span class="text-gray-500">Memuat data mahasiswa...</span>
                              </div>
                            </td>
                          </tr>

                          <!-- Empty State -->
                          <tr v-else-if="filteredStudents.length === 0">
                            <td colspan="6" class="px-6 py-12 text-center">
                              <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Mahasiswa</h3>
                                <p class="text-gray-500 mb-4">
                                  {{ searchQuery ? 'Tidak ada mahasiswa yang cocok dengan pencarian' : 'Kelas ini belum memiliki mahasiswa yang terdaftar' }}
                                </p>
                                <button
                                  v-if="!searchQuery"
                                  @click="openEnrollModal"
                                  class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
                                >
                                  Tambah Mahasiswa Pertama
                                </button>
                              </div>
                            </td>
                          </tr>

                          <!-- Students Data -->
                          <tr v-for="student in paginatedStudents" :key="student.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                              <input
                                type="checkbox"
                                :checked="selectedStudents.includes(student.id)"
                                @change="toggleStudentSelection(student.id)"
                                class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                              />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                              <div>
                                <div class="text-sm font-medium text-gray-900">
                                  {{ student.name }}
                                  <span class="text-xs text-gray-500 ml-2">({{ student.student_number }})</span>
                                </div>
                                <div class="text-sm text-gray-500">{{ student.phone || '-' }}</div>
                              </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                              {{ student.email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                              {{ student.batch_year || '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                              {{ formatDate(student.pivot?.enrollment_date) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                              <div class="flex items-center justify-end space-x-2">
                                <!-- Remove Student -->
                                <button
                                  @click="confirmRemoveStudent(student)"
                                  class="text-red-600 hover:text-red-900"
                                  title="Hapus dari Kelas"
                                >
                                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                  </svg>
                                </button>
                              </div>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>

                  <!-- Pagination -->
                  <div v-if="totalPages > 1" class="mt-6 flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                      Menampilkan {{ startIndex + 1 }} sampai {{ endIndex }} dari {{ filteredStudents.length }} mahasiswa
                    </div>
                    <div class="flex items-center space-x-2">
                      <button
                        @click="previousPage"
                        :disabled="currentPage === 1"
                        class="px-3 py-1 text-sm bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                      >
                        Sebelumnya
                      </button>
                      <span class="px-3 py-1 text-sm text-gray-700">
                        Halaman {{ currentPage }} dari {{ totalPages }}
                      </span>
                      <button
                        @click="nextPage"
                        :disabled="currentPage === totalPages"
                        class="px-3 py-1 text-sm bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                      >
                        Selanjutnya
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Loading Overlay -->
              <div
                v-if="loading"
                class="absolute inset-0 flex items-center justify-center rounded-2xl bg-white/80 backdrop-blur-sm"
              >
                <div class="flex items-center space-x-3">
                  <div class="h-8 w-8 animate-spin rounded-full border-2 border-blue-600 border-t-transparent"></div>
                  <span class="text-gray-700">Memproses...</span>
                </div>
              </div>
            </div>
          </Transition>
        </div>
      </div>
    </Transition>
  </Teleport>

  <!-- Confirm Remove Modal -->
  <ConfirmDialogModal
    v-if="showRemoveConfirm"
    :show="showRemoveConfirm"
    :title="'Hapus Mahasiswa dari Kelas'"
    :message="`Apakah Anda yakin ingin menghapus ${studentToRemove?.name} (${studentToRemove?.student_number}) dari kelas ${selectedClass?.name}?`"
    :confirm-text="'Hapus'"
    :cancel-text="'Batal'"
    :type="'danger'"
    @close="showRemoveConfirm = false"
    @confirm="handleRemoveStudent"
  />
</template>

<script setup>
import { ref, computed, onMounted, watch, onUnmounted } from 'vue'
import classService from '@/services/classService'
import ConfirmDialogModal from './ConfirmDialogModal.vue'
import { showToast } from '@/stores/toast'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  selectedClass: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['close', 'open-enroll-modal', 'refresh'])

// State
const loading = ref(false)
const students = ref([])
const searchQuery = ref('')
const currentPage = ref(1)
const itemsPerPage = ref(10)
const showRemoveConfirm = ref(false)
const studentToRemove = ref(null)
const selectedStudents = ref([])
const selectAll = ref(false)
const bulkDeleting = ref(false)

// Computed
const filteredStudents = computed(() => {
  if (!students.value || !Array.isArray(students.value)) return []
  if (!searchQuery.value) return students.value

  const query = searchQuery.value.toLowerCase()
  return students.value.filter(student =>
    student.student_number?.toLowerCase().includes(query) ||
    student.name?.toLowerCase().includes(query) ||
    student.email?.toLowerCase().includes(query)
  )
})

const totalPages = computed(() => {
  return Math.ceil(filteredStudents.value.length / itemsPerPage.value)
})

const startIndex = computed(() => {
  return (currentPage.value - 1) * itemsPerPage.value
})

const endIndex = computed(() => {
  return Math.min(startIndex.value + itemsPerPage.value, filteredStudents.value.length)
})

const paginatedStudents = computed(() => {
  const start = startIndex.value
  const end = endIndex.value
  return filteredStudents.value.slice(start, end)
})

const currentEnrolled = computed(() => {
  if (!students.value || !Array.isArray(students.value)) return 0
  return students.value.filter(student => student.pivot?.status === 'active').length
})

const hasSelectedStudents = computed(() => {
  return selectedStudents.value.length > 0
})

const isAllSelected = computed(() => {
  return paginatedStudents.value.length > 0 && selectedStudents.value.length === paginatedStudents.value.length
})

const isIndeterminate = computed(() => {
  return selectedStudents.value.length > 0 && selectedStudents.value.length < paginatedStudents.value.length
})

// Methods
const loadStudents = async () => {
  if (!props.selectedClass?.id) return

  loading.value = true
  try {
    const response = await classService.getClassStudents(props.selectedClass.id, {
      per_page: 1000, // Load all students for client-side filtering
      status: 'active'
    })
    // Handle different response structures
    const studentsData = response.data?.data || response.data || []
    students.value = Array.isArray(studentsData) ? studentsData : []
  } catch (error) {
    console.error('Error loading students:', error)
    showToast('Error', 'Gagal memuat data mahasiswa', 'error')
    students.value = [] // Ensure students is always an array
  } finally {
    loading.value = false
  }
}

// Custom debounce function
const debounce = (func, delay) => {
  let timeoutId
  return function (...args) {
    clearTimeout(timeoutId)
    timeoutId = setTimeout(() => func.apply(this, args), delay)
  }
}

const handleClose = () => {
  if (!loading.value) {
    emit('close')
  }
}

const handleBackdropClick = () => {
  handleClose()
}

const openEnrollModal = () => {
  emit('open-enroll-modal')
}

const confirmRemoveStudent = (student) => {
  studentToRemove.value = student
  showRemoveConfirm.value = true
}

const handleRemoveStudent = async () => {
  if (!props.selectedClass?.id || !studentToRemove.value?.id) return

  try {
    await classService.removeStudent(props.selectedClass.id, studentToRemove.value.id)

    // Refresh students list
    await loadStudents()

    // Emit refresh event to update parent component
    emit('refresh')

    showToast('Success', 'Mahasiswa berhasil dihapus dari kelas', 'success')
  } catch (error) {
    console.error('Error removing student:', error)
    showToast('Error', error.response?.data?.message || 'Gagal menghapus mahasiswa', 'error')
  } finally {
    showRemoveConfirm.value = false
    studentToRemove.value = null
  }
}

const toggleSelectAll = () => {
  if (isAllSelected.value) {
    // Deselect all
    selectedStudents.value = []
  } else {
    // Select all on current page
    selectedStudents.value = paginatedStudents.value.map(student => student.id)
  }
}

const toggleStudentSelection = (studentId) => {
  const index = selectedStudents.value.indexOf(studentId)
  if (index > -1) {
    selectedStudents.value.splice(index, 1)
  } else {
    selectedStudents.value.push(studentId)
  }
}

const handleBulkRemove = async () => {
  if (!props.selectedClass?.id || selectedStudents.value.length === 0) return

  bulkDeleting.value = true

  try {
    showToast('Info', `Sedang menghapus ${selectedStudents.value.length} mahasiswa...`, 'info')

    const removePromises = selectedStudents.value.map(studentId =>
      classService.removeStudent(props.selectedClass.id, studentId, 'Dihapus secara massal')
    )

    await Promise.all(removePromises)

    // Refresh students list
    await loadStudents()

    // Emit refresh event to update parent component
    emit('refresh')

    // Clear selection
    const deletedCount = selectedStudents.value.length
    selectedStudents.value = []

    showToast('Success', `${deletedCount} mahasiswa berhasil dihapus dari kelas`, 'success')
  } catch (error) {
    console.error('Error removing students:', error)
    showToast('Error', error.response?.data?.message || 'Gagal menghapus mahasiswa', 'error')
  } finally {
    bulkDeleting.value = false
  }
}

const getStudentStatusClass = (status) => {
  const statusClasses = {
    'active': 'bg-green-100 text-green-800',
    'inactive': 'bg-yellow-100 text-yellow-800',
    'transferred': 'bg-red-100 text-red-800',
    'dropped': 'bg-gray-100 text-gray-800'
  }
  return statusClasses[status] || 'bg-gray-100 text-gray-800'
}

const getStudentStatusText = (status) => {
  const statusTexts = {
    'active': 'Aktif',
    'inactive': 'Tidak Aktif',
    'transferred': 'Dipindahkan',
    'dropped': 'Keluar'
  }
  return statusTexts[status] || 'Tidak Diketahui'
}

const formatDate = (dateString) => {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'long',
    year: 'numeric'
  })
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
  }
}

const previousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
  }
}

// Watch for class changes
watch(() => props.selectedClass, (newClass) => {
  if (newClass && props.show) {
    loadStudents()
  }
}, { immediate: true })

// Watch for modal open
watch(() => props.show, (newShow) => {
  if (newShow && props.selectedClass) {
    loadStudents()
    currentPage.value = 1
    searchQuery.value = ''
  }
})

// Prevent body scroll when modal is open
watch(() => props.show, (newShow) => {
  if (newShow) {
    document.body.style.overflow = 'hidden'
    document.documentElement.style.overflow = 'hidden'
  } else {
    setTimeout(() => {
      document.body.style.overflow = ''
      document.documentElement.style.overflow = ''
      document.body.style.removeProperty('overflow')
      document.documentElement.style.removeProperty('overflow')
    }, 50)
  }
}, { immediate: true })

// Cleanup
onUnmounted(() => {
  document.body.style.overflow = ''
  document.documentElement.style.overflow = ''
  document.body.style.removeProperty('overflow')
  document.documentElement.style.removeProperty('overflow')
})

// Debounce search
watch(searchQuery, debounce(() => {
  currentPage.value = 1
}, 300))
</script>

<style scoped>
/* Custom scrollbar */
.overflow-x-auto::-webkit-scrollbar {
  height: 6px;
}

.overflow-x-auto::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}

/* Ensure proper z-index stacking */
.z-\[9999\] {
  z-index: 9999;
}
</style>