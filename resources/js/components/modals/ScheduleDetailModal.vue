<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="$emit('close')"></div>

      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl sm:w-full">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
          <div class="flex items-center justify-between">
            <h3 class="text-xl font-bold text-white">Detail Jadwal Kelas</h3>
            <button
              @click="$emit('close')"
              class="text-white hover:text-gray-200 transition-colors"
            >
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Content -->
        <div class="bg-white px-6 py-5 max-h-[80vh] overflow-y-auto">
          <div v-if="schedule" class="space-y-6">
            <!-- Basic Information Section -->
            <div class="bg-gray-50 rounded-lg p-5">
              <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2v0a2 2 0 012-2h-2a2 2 0 01-2-2V0z"/>
                </svg>
                Informasi Dasar
              </h4>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <p class="text-sm font-medium text-gray-500">Kode Jadwal</p>
                  <p class="text-sm text-gray-900 font-mono mt-1">{{ schedule.schedule_code || '-' }}</p>
                </div>

                <div>
                  <p class="text-sm font-medium text-gray-500">Judul Jadwal</p>
                  <p class="text-sm text-gray-900 mt-1">{{ schedule.title || '-' }}</p>
                </div>

                <div>
                  <p class="text-sm font-medium text-gray-500">Program Studi</p>
                  <p class="text-sm text-gray-900 mt-1">{{ schedule.program_study?.name || '-' }}</p>
                </div>

                <div>
                  <p class="text-sm font-medium text-gray-500">Kelas</p>
                  <p class="text-sm text-gray-900 mt-1">{{ schedule.school_class?.name || '-' }} ({{ schedule.school_class?.batch_year || '-' }})</p>
                </div>

                <div>
                  <p class="text-sm font-medium text-gray-500">Tahun Akademik</p>
                  <p class="text-sm text-gray-900 mt-1">
                    {{ schedule.academic_year?.academic_calendar_year || '-' }} - {{ schedule.academic_year?.admission_period || '-' }}
                  </p>
                </div>

                <div>
                  <p class="text-sm font-medium text-gray-500">Status</p>
                  <span
                    :class="[
                      'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium mt-1',
                      schedule.status === 'active' ? 'bg-green-100 text-green-800' :
                      schedule.status === 'completed' ? 'bg-blue-100 text-blue-800' :
                      schedule.status === 'cancelled' ? 'bg-red-100 text-red-800' :
                      schedule.status === 'draft' ? 'bg-yellow-100 text-yellow-800' :
                      'bg-gray-100 text-gray-800'
                    ]"
                  >
                    {{ getStatusLabel(schedule.status) }}
                  </span>
                </div>

                <div class="md:col-span-2">
                  <p class="text-sm font-medium text-gray-500">Deskripsi</p>
                  <p class="text-sm text-gray-900 mt-1">{{ schedule.description || '-' }}</p>
                </div>
              </div>
            </div>

            <!-- Online/Offline Distribution -->
            <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg p-5">
              <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Distribusi Pembelajaran
              </h4>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-center">
                  <div class="flex-1">
                    <p class="text-sm font-medium text-gray-700">Persentase Online</p>
                    <div class="mt-2">
                      <div class="w-full bg-gray-200 rounded-full h-4">
                        <div
                          class="bg-blue-500 h-4 rounded-full"
                          :style="{ width: (schedule.online_percentage || 0) + '%' }"
                        ></div>
                      </div>
                      <p class="text-lg font-semibold mt-1">{{ schedule.online_percentage || 0 }}%</p>
                    </div>
                  </div>
                </div>

                <div class="flex items-center">
                  <div class="flex-1">
                    <p class="text-sm font-medium text-gray-700">Persentase Offline</p>
                    <div class="mt-2">
                      <div class="w-full bg-gray-200 rounded-full h-4">
                        <div
                          class="bg-green-500 h-4 rounded-full"
                          :style="{ width: (schedule.offline_percentage || 0) + '%' }"
                        ></div>
                      </div>
                      <p class="text-lg font-semibold mt-1">{{ schedule.offline_percentage || 0 }}%</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Course Details Section -->
            <div class="bg-gray-50 rounded-lg p-5">
              <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                Daftar Mata Kuliah ({{ schedule.details?.length || 0 }})
              </h4>

              <div v-if="schedule.details && schedule.details.length > 0" class="space-y-4">
                <div
                  v-for="(detail, index) in schedule.details"
                  :key="detail.id"
                  class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow"
                >
                  <!-- Course Header -->
                  <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-4 py-3 border-b border-gray-200">
                    <div class="flex justify-between items-start">
                      <div class="flex-1">
                        <h5 class="font-semibold text-gray-900 text-lg">
                          {{ detail.course?.course_name || '-' }}
                          <span class="text-sm text-gray-500 ml-2">({{ detail.course?.course_code || '-' }})</span>
                        </h5>
                        <div class="flex items-center mt-1 space-x-3">
                          <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            {{ detail.course?.credits || detail.credits || 0 }} SKS
                          </span>
                          <span class="text-sm text-gray-600">
                            {{ detail.course?.course_type === 'mandatory' ? 'Wajib' : 'Pilihan' }} • {{ detail.course?.level === 'undergraduate' ? 'S1' : detail.course?.level === 'graduate' ? 'S2' : 'S3' }}
                          </span>
                          <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                :class="detail.is_online ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'">
                            {{ detail.is_online ? 'Online' : 'Offline' }}
                          </span>
                        </div>
                      </div>
                      <div class="text-right">
                        <p class="text-sm text-gray-500">Kelas</p>
                        <p class="font-semibold text-gray-900">{{ detail.class_name || '-' }}</p>
                      </div>
                    </div>
                  </div>

                  <!-- Course Details -->
                  <div class="p-4">
                    <!-- Schedule Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                      <div class="bg-gray-50 rounded-lg p-3">
                        <h6 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                          <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                          </svg>
                          Jadwal
                        </h6>
                        <div class="space-y-1 text-sm">
                          <div class="flex justify-between">
                            <span class="text-gray-600">Hari:</span>
                            <span class="font-medium text-gray-900 capitalize">{{ detail.day_of_week || '-' }}</span>
                          </div>
                          <div class="flex justify-between">
                            <span class="text-gray-600">Waktu:</span>
                            <span class="font-medium text-gray-900">{{ detail.start_time || '-' }} - {{ detail.end_time || '-' }}</span>
                          </div>
                          <div class="flex justify-between">
                            <span class="text-gray-600">Ruang:</span>
                            <span class="font-medium text-gray-900">{{ detail.room?.name || detail.room?.room_code || (detail.is_online ? 'Online' : '-') }}</span>
                          </div>
                        </div>
                      </div>

                      <div class="bg-gray-50 rounded-lg p-3">
                        <h6 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                          <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                          </svg>
                          Informasi
                        </h6>
                        <div class="space-y-1 text-sm">
                          <div class="flex justify-between">
                            <span class="text-gray-600">Jumlah Pertemuan:</span>
                            <span class="font-medium text-gray-900">{{ detail.total_meetings || 0 }} kali</span>
                          </div>
                          <div class="flex justify-between">
                            <span class="text-gray-600">Sesi/Pertemuan:</span>
                            <span class="font-medium text-gray-900">{{ detail.sessions_per_meeting || 1 }} sesi</span>
                          </div>
                          <div class="flex justify-between">
                            <span class="text-gray-600">Periode:</span>
                            <span class="font-medium text-gray-900">{{ formatDate(detail.start_date) }} - {{ formatDate(detail.end_date) }}</span>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Lecturer Information -->
                    <div class="bg-yellow-50 rounded-lg p-3 mb-4">
                      <h6 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Dosen Pengampu
                      </h6>
                      <div v-if="detail.lecturer" class="flex items-center">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                          <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                          </svg>
                        </div>
                        <div class="flex-1">
                          <p class="font-semibold text-gray-900 text-base">
                            {{ detail.lecturer.name || 'Tidak ada dosen' }}
                          </p>
                          <p class="text-sm text-gray-600">
                            NIP: {{ detail.lecturer.employee_number || detail.lecturer.code || '-' }}
                          </p>
                          <p v-if="detail.lecturer.email" class="text-sm text-gray-600">
                            Email: {{ detail.lecturer.email }}
                          </p>
                          <p v-if="detail.lecturer.rank || detail.lecturer.position" class="text-sm text-gray-600">
                            {{ detail.lecturer.rank || detail.lecturer.position }}
                          </p>
                        </div>
                      </div>
                      <div v-else class="text-sm text-gray-500 italic">
                        Belum ada dosen pengampu yang ditetapkan
                      </div>
                    </div>

                    <!-- Course Description (if available) -->
                    <div v-if="detail.course?.description" class="bg-blue-50 rounded-lg p-3 mb-4">
                      <h6 class="text-sm font-semibold text-gray-700 mb-1">Deskripsi Mata Kuliah</h6>
                      <p class="text-sm text-gray-600">{{ detail.course.description }}</p>
                    </div>

                    <!-- Notes -->
                    <div v-if="detail.notes" class="bg-gray-50 rounded-lg p-3">
                      <h6 class="text-sm font-semibold text-gray-700 mb-1">Catatan</h6>
                      <p class="text-sm text-gray-600">{{ detail.notes }}</p>
                    </div>
                  </div>
                </div>
              </div>

              <div v-else class="text-center py-8 text-gray-500">
                <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002 2v0a2 2 0 012-2h-2a2 2 0 01-2-2V0z"/>
                </svg>
                <p>Belum ada mata kuliah yang ditambahkan</p>
              </div>
            </div>

            <!-- Statistics Section -->
            <div class="bg-blue-50 rounded-lg p-5">
              <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002 2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Statistik Jadwal
              </h4>

              <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg p-4 text-center">
                  <p class="text-2xl font-bold text-blue-600">{{ schedule.details?.length || 0 }}</p>
                  <p class="text-xs text-gray-600 mt-1">Total Mata Kuliah</p>
                </div>
                <div class="bg-white rounded-lg p-4 text-center">
                  <p class="text-2xl font-bold text-green-600">{{ getTotalOnlineCourses() }}</p>
                  <p class="text-xs text-gray-600 mt-1">Kelas Online</p>
                </div>
                <div class="bg-white rounded-lg p-4 text-center">
                  <p class="text-2xl font-bold text-purple-600">{{ getTotalOfflineCourses() }}</p>
                  <p class="text-xs text-gray-600 mt-1">Kelas Offline</p>
                </div>
                <div class="bg-white rounded-lg p-4 text-center">
                  <p class="text-2xl font-bold text-orange-600">{{ getTotalCredits() }}</p>
                  <p class="text-xs text-gray-600 mt-1">Total SKS</p>
                </div>
              </div>
            </div>

            <!-- Time Information -->
            <div class="bg-gray-50 rounded-lg p-5">
              <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Informasi Waktu
              </h4>

              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <p class="text-sm font-medium text-gray-500">Dibuat pada</p>
                  <p class="text-sm text-gray-900 mt-1">{{ formatDate(schedule.created_at) }}</p>
                </div>

                <div>
                  <p class="text-sm font-medium text-gray-500">Diperbarui pada</p>
                  <p class="text-sm text-gray-900 mt-1">{{ formatDate(schedule.updated_at) }}</p>
                </div>

                <div>
                  <p class="text-sm font-medium text-gray-500">Tanggal Mulai - Selesai</p>
                  <p class="text-sm text-gray-900 mt-1">
                    {{ getScheduleDateRange() }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Generated Schedules Section -->
            <div v-if="schedule.schedules && schedule.schedules.length > 0" class="bg-purple-50 rounded-lg p-5">
              <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 0l8 0v4m0 6l8 0m0-6l-8 0v6m0 6V7m0 10v-4m0 0l8 0"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7V3m0 0l-8 0m0 4l8 0"/>
                </svg>
                Jadwal Ter-generate ({{ schedule.schedules.length }})
              </h4>

              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-purple-100">
                    <tr>
                      <th class="px-4 py-2 text-left text-xs font-medium text-gray-700">Tanggal</th>
                      <th class="px-4 py-2 text-left text-xs font-medium text-gray-700">Hari</th>
                      <th class="px-4 py-2 text-left text-xs font-medium text-gray-700">Waktu</th>
                      <th class="px-4 py-2 text-left text-xs font-medium text-gray-700">Mata Kuliah</th>
                      <th class="px-4 py-2 text-left text-xs font-medium text-gray-700">Ruang</th>
                      <th class="px-4 py-2 text-left text-xs font-medium text-gray-700">Status</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr
                      v-for="(schedule, index) in schedule.schedules"
                      :key="schedule.id"
                      class="hover:bg-gray-50"
                    >
                      <td class="px-4 py-2 text-sm text-gray-900">{{ formatDate(schedule.date) }}</td>
                      <td class="px-4 py-2 text-sm text-gray-900 capitalize">{{ schedule.day_of_week }}</td>
                      <td class="px-4 py-2 text-sm text-gray-900">{{ schedule.start_time }} - {{ schedule.end_time }}</td>
                      <td class="px-4 py-2 text-sm text-gray-900">
                        {{ schedule.title || '-' }}
                      </td>
                      <td class="px-4 py-2 text-sm text-gray-900">
                        {{ schedule.rooms && schedule.rooms.length > 0 ? schedule.rooms.map(r => r.name).join(', ') : (schedule.is_online ? 'Online' : '-') }}
                      </td>
                      <td class="px-4 py-2 text-sm">
                        <span
                          :class="[
                            'px-2 py-1 text-xs rounded-full',
                            schedule.status === 'approved' ? 'bg-green-100 text-green-800' :
                            schedule.status === 'submitted' ? 'bg-yellow-100 text-yellow-800' :
                            schedule.status === 'rejected' ? 'bg-red-100 text-red-800' :
                            'bg-gray-100 text-gray-800'
                          ]"
                        >
                          {{ getScheduleStatusLabel(schedule.status) }}
                        </span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div v-else class="text-center py-8">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002 2v0a2 2 0 012-2h-2a2 2 0 01-2-2V0z"/>
              </svg>
            </div>
            <p class="text-gray-500">Tidak ada data jadwal</p>
          </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse">
          <button
            @click="$emit('generate', schedule)"
            class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm"
          >
            Generate Jadwal
          </button>
          <button
            @click="$emit('close')"
            class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
          >
            Tutup
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits, computed } from 'vue'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  schedule: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['close', 'edit', 'generate'])

// Computed properties
const getTotalOnlineCourses = () => {
  if (!props.schedule?.details) return 0
  return props.schedule.details.filter(detail => detail.is_online).length
}

const getTotalOfflineCourses = () => {
  if (!props.schedule?.details) return 0
  return props.schedule.details.filter(detail => !detail.is_online).length
}

const getTotalCredits = () => {
  if (!props.schedule?.details) return 0
  return props.schedule.details.reduce((total, detail) => total + (detail.course?.credits || 0), 0)
}

const getScheduleDateRange = () => {
  if (!props.schedule?.details || props.schedule.details.length === 0) return '-'

  const dates = props.schedule.details
    .map(detail => ({ start: detail.start_date, end: detail.end_date }))
    .sort((a, b) => new Date(a.start) - new Date(b.start))

  if (dates.length === 0) return '-'

  const startDate = new Date(dates[0].start).toLocaleDateString('id-ID')
  const endDate = new Date(dates[dates.length - 1].end).toLocaleDateString('id-ID')

  return `${startDate} - ${endDate}`
}

// Methods
const getStatusLabel = (status) => {
  const labels = {
    'draft': 'Draft',
    'active': 'Aktif',
    'completed': 'Selesai',
    'cancelled': 'Dibatalkan'
  }
  return labels[status] || status
}

const getScheduleStatusLabel = (status) => {
  const labels = {
    'draft': 'Draft',
    'submitted': 'Menung Approval',
    'approved': 'Disetujui',
    'rejected': 'Ditolak',
    'cancelled': 'Dibatalkan'
  }
  return labels[status] || status
}

const formatDate = (dateString) => {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}
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