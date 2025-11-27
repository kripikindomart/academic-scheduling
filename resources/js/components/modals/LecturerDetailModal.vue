<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 transition-opacity" aria-hidden="true">
        <div class="absolute inset-0 bg-gray-500 opacity-75" @click="$emit('close')"></div>
      </div>

      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl sm:w-full">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
          <div class="flex items-center justify-between">
            <h3 class="text-xl font-semibold text-white flex items-center">
              <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h-1m-1 4v-4m-2 0h4m0 0v4m0-4v4m8-1V6a2 2 0 00-2-2H6a2 2 0 00-2 2v8a2 2 0 002 2h2m-2 0h4" />
              </svg>
              Detail Dosen
            </h3>
            <button
              @click="$emit('close')"
              class="text-white hover:text-gray-200 transition-colors duration-200"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>

        <div class="bg-white px-6 py-4">
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Profile Photo Section -->
            <div class="lg:col-span-1">
              <div class="text-center">
                <!-- Profile Photo -->
                <div class="relative inline-block">
                  <img
                    v-if="lecturer.photo"
                    :src="`/storage/${lecturer.photo}`"
                    :alt="lecturer.name"
                    class="h-48 w-48 rounded-2xl object-cover border-4 border-white shadow-xl mx-auto"
                    @error="$event.target.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(lecturer.name || '?')}&background=3B82F6&color=fff&size=200`"
                  >
                  <div
                    v-else
                    class="h-48 w-48 rounded-2xl bg-gradient-to-br from-blue-400 to-indigo-600 flex items-center justify-center text-white shadow-xl mx-auto"
                  >
                    <div class="text-center">
                      <svg class="h-24 w-24 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                      </svg>
                      <p class="text-lg font-semibold">{{ lecturer.name }}</p>
                    </div>
                  </div>
                </div>

                <!-- Status Badge -->
                <div class="mt-4">
                  <span
                    :class="[
                      'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium',
                      lecturer.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                    ]"
                  >
                    <svg class="w-4 h-4 mr-1" :class="lecturer.is_active ? 'text-green-500' : 'text-gray-500'" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414 1 1 0 00-1.414 1.414l4 4a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    {{ lecturer.is_active ? 'Aktif' : 'Tidak Aktif' }}
                  </span>
                </div>

                <!-- Quick Info -->
                <div class="mt-4 space-y-2">
                  <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-sm font-medium text-gray-700">NIP</p>
                    <p class="text-sm text-gray-900">{{ lecturer.employee_number || '-' }}</p>
                  </div>
                  <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-sm font-medium text-gray-700">Email</p>
                    <p class="text-sm text-gray-900 break-all">{{ lecturer.email || '-' }}</p>
                  </div>
                  <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-sm font-medium text-gray-700">Telepon</p>
                    <p class="text-sm text-gray-900">{{ lecturer.phone || '-' }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Detailed Information -->
            <div class="lg:col-span-2 space-y-6">
              <!-- Personal Information -->
              <div>
                <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                  <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                  Informasi Personal
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <p class="text-sm font-medium text-gray-600">Tempat Lahir</p>
                    <p class="text-sm text-gray-900">{{ lecturer.birth_place || '-' }}, {{ formatDate(lecturer.birth_date) }}</p>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-600">Jenis Kelamin</p>
                    <p class="text-sm text-gray-900">{{ getGenderLabel(lecturer.gender) }}</p>
                  </div>
                  <div class="md:col-span-2">
                    <p class="text-sm font-medium text-gray-600">Alamat</p>
                    <p class="text-sm text-gray-900">{{ lecturer.address || '-' }}</p>
                  </div>
                </div>
              </div>

              <!-- Academic Information -->
              <div>
                <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                  <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                  </svg>
                  Informasi Akademik
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <p class="text-sm font-medium text-gray-600">Program Studi</p>
                    <p class="text-sm text-gray-900">{{ lecturer.department || '-' }}</p>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-600">Fakultas</p>
                    <p class="text-sm text-gray-900">{{ lecturer.faculty || '-' }}</p>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-600">Pangkat Akademik</p>
                    <p class="text-sm text-gray-900">{{ lecturer.rank || lecturer.position || '-' }}</p>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-600">Jenis Pegawai</p>
                    <p class="text-sm text-gray-900">{{ getEmploymentTypeLabel(lecturer.employment_type) }}</p>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-600">Status Kerja</p>
                    <p class="text-sm text-gray-900">{{ getStatusLabel(lecturer.status) }}</p>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-600">TMT Masuk</p>
                    <p class="text-sm text-gray-900">{{ formatDate(lecturer.hire_date) }}</p>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-600">Beban Mengajar</p>
                    <p class="text-sm text-gray-900">{{ lecturer.academic_load || 0 }} SKS</p>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-600">Ruangan</p>
                    <p class="text-sm text-gray-900">{{ lecturer.office_room || '-' }}</p>
                  </div>
                </div>
              </div>

              <!-- Specialization -->
              <div>
                <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                  <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                  </svg>
                  Bidang Keahlian
                </h4>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                  <p class="text-sm text-gray-900">{{ lecturer.specialization || 'Tidak ada informasi keahlian' }}</p>
                </div>
              </div>

              <!-- Education -->
              <div v-if="lecturer.highest_education">
                <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                  <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5v12z" />
                  </svg>
                  Pendidikan
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div>
                    <p class="text-sm font-medium text-gray-600">Pendidikan Tertinggi</p>
                    <p class="text-sm text-gray-900">{{ lecturer.highest_education }}</p>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-600">Institusi</p>
                    <p class="text-sm text-gray-900">{{ lecturer.education_institution || '-' }}</p>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-600">Jurusan</p>
                    <p class="text-sm text-gray-900">{{ lecturer.education_major || '-' }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="bg-gray-50 px-6 py-4">
            <div class="flex justify-end space-x-3">
              <button
                @click="$emit('close')"
                class="px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200"
              >
                Tutup
              </button>
              <button
                @click="editLecturer"
                class="px-4 py-2 bg-blue-600 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  show: {
    type: Boolean,
    required: true
  },
  lecturer: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['close', 'edit'])

const formatDate = (dateString) => {
  if (!dateString) return '-'
  try {
    const date = new Date(dateString)
    return date.toLocaleDateString('id-ID', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    })
  } catch (error) {
    return dateString
  }
}

const getGenderLabel = (gender) => {
  const labels = {
    male: 'Laki-laki',
    female: 'Perempuan'
  }
  return labels[gender] || gender
}

const getEmploymentTypeLabel = (type) => {
  const labels = {
    permanent: 'Permanent',
    contract: 'Kontrak',
    part_time: 'Part Time',
    guest: 'Tamu'
  }
  return labels[type] || type
}

const getStatusLabel = (status) => {
  const labels = {
    active: 'Aktif',
    inactive: 'Tidak Aktif',
    on_leave: 'Cuti',
    terminated: 'Berhenti',
    retired: 'Pensiun'
  }
  return labels[status] || status
}

const editLecturer = () => {
  emit('edit', props.lecturer)
}
</script>

<style scoped>
/* Custom scrollbar for modal content */
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: #f1f1f1;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #555;
}
</style>