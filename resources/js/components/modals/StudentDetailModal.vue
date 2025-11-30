<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 transition-opacity" aria-hidden="true">
        <div class="absolute inset-0 bg-gray-500 opacity-75" @click="$emit('close')"></div>
      </div>

      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-6xl sm:w-full">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
          <!-- Header -->
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-2xl font-bold text-gray-900">Detail Mahasiswa</h3>
            <button
              @click="$emit('close')"
              class="text-gray-400 hover:text-gray-500 focus:outline-none"
            >
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Student Info Card -->
          <div v-if="student" class="bg-gradient-to-r from-green-50 to-blue-50 rounded-xl p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:space-x-6">
              <!-- Avatar -->
              <div class="flex-shrink-0 mb-4 md:mb-0">
                <div class="h-24 w-24 rounded-full bg-gradient-to-br from-green-400 to-blue-500 flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                  {{ getInitials(student.name) }}
                </div>
              </div>

              <!-- Basic Info -->
              <div class="flex-1">
                <h4 class="text-2xl font-bold text-gray-900 mb-2">{{ student.name }}</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div class="flex items-center text-gray-700">
                    <svg class="h-5 w-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                    </svg>
                    <span class="font-medium">NIM:</span>
                    <span class="ml-2">{{ student.student_number }}</span>
                  </div>
                  <div class="flex items-center text-gray-700">
                    <svg class="h-5 w-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span class="font-medium">Email:</span>
                    <span class="ml-2">{{ student.email }}</span>
                  </div>
                  <div class="flex items-center text-gray-700">
                    <svg class="h-5 w-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                    <span class="font-medium">No. HP:</span>
                    <span class="ml-2">{{ student.phone }}</span>
                  </div>
                  <div class="flex items-center text-gray-700">
                    <svg class="h-5 w-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span class="font-medium">Program Studi:</span>
                    <span class="ml-2">{{ student.program_study?.name || '-' }}</span>
                  </div>
                </div>

                <!-- Status Badges -->
                <div class="flex flex-wrap gap-2 mt-4">
                  <span :class="getStatusColor(student.status)" class="px-3 py-1 rounded-full text-sm font-medium">
                    {{ getStatusLabel(student.status) }}
                  </span>
                  <span :class="student.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'" class="px-3 py-1 rounded-full text-sm font-medium">
                    {{ student.is_active ? 'Aktif' : 'Tidak Aktif' }}
                  </span>
                  <span :class="student.is_regular ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800'" class="px-3 py-1 rounded-full text-sm font-medium">
                    {{ student.is_regular ? 'Regular' : 'Non-Regular' }}
                  </span>
                  <span v-if="student.hasUserAccount" class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm font-medium">
                    Punya Akun User
                  </span>
                </div>
              </div>

              <!-- Actions -->
              <div class="flex-shrink-0 ml-6">
                <div class="flex flex-col space-y-2">
                  <button
                    @click="editStudent"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center"
                  >
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit
                  </button>
                  <button
                    @click="printStudentCard"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center"
                  >
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak Kartu
                  </button>
                  <button
                    v-if="!student.hasUserAccount"
                    @click="createUserAccount"
                    class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center justify-center"
                  >
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    Buat Akun
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Tab Navigation -->
          <div class="border-b border-gray-200 mb-6">
            <nav class="-mb-px flex space-x-8">
              <button
                v-for="tab in tabs"
                :key="tab.key"
                @click="activeTab = tab.key"
                :class="[
                  activeTab === tab.key
                    ? 'border-green-500 text-green-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                  'whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm'
                ]"
              >
                {{ tab.label }}
              </button>
            </nav>
          </div>

          <!-- Tab Content -->
          <div class="min-h-[400px]">
            <!-- Personal Info Tab -->
            <div v-if="activeTab === 'personal'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-4">
                <h5 class="font-semibold text-gray-900 mb-3">Data Pribadi</h5>

                <div class="flex justify-between py-2 border-b">
                  <span class="text-gray-600">No. KTP</span>
                  <span class="font-medium">{{ student.id_card_number || '-' }}</span>
                </div>

                <div class="flex justify-between py-2 border-b">
                  <span class="text-gray-600">Jenis Kelamin</span>
                  <span class="font-medium">{{ getGenderLabel(student.gender) }}</span>
                </div>

                <div class="flex justify-between py-2 border-b">
                  <span class="text-gray-600">Tempat Lahir</span>
                  <span class="font-medium">{{ student.birth_place || '-' }}</span>
                </div>

                <div class="flex justify-between py-2 border-b">
                  <span class="text-gray-600">Tanggal Lahir</span>
                  <span class="font-medium">{{ formatDate(student.birth_date) }}</span>
                </div>

                <div class="flex justify-between py-2 border-b">
                  <span class="text-gray-600">Usia</span>
                  <span class="font-medium">{{ calculateAge(student.birth_date) }} tahun</span>
                </div>

                <div class="flex justify-between py-2 border-b">
                  <span class="text-gray-600">Agama</span>
                  <span class="font-medium">{{ student.religion || '-' }}</span>
                </div>

                <div class="flex justify-between py-2 border-b">
                  <span class="text-gray-600">Golongan Darah</span>
                  <span class="font-medium">{{ student.blood_type || '-' }}</span>
                </div>

                <div class="flex justify-between py-2 border-b">
                  <span class="text-gray-600">Kewarganegaraan</span>
                  <span class="font-medium">{{ student.nationality || '-' }}</span>
                </div>
              </div>

              <div class="space-y-4">
                <h5 class="font-semibold text-gray-900 mb-3">Alamat</h5>

                <div class="flex justify-between py-2 border-b">
                  <span class="text-gray-600">Alamat Lengkap</span>
                  <span class="font-medium text-right max-w-xs">{{ student.address || '-' }}</span>
                </div>

                <div class="flex justify-between py-2 border-b">
                  <span class="text-gray-600">Kota</span>
                  <span class="font-medium">{{ student.city || '-' }}</span>
                </div>

                <div class="flex justify-between py-2 border-b">
                  <span class="text-gray-600">Provinsi</span>
                  <span class="font-medium">{{ student.province || '-' }}</span>
                </div>

                <div class="flex justify-between py-2 border-b">
                  <span class="text-gray-600">Kode Pos</span>
                  <span class="font-medium">{{ student.postal_code || '-' }}</span>
                </div>
              </div>
            </div>

            <!-- Academic Info Tab -->
            <div v-if="activeTab === 'academic'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-4">
                <h5 class="font-semibold text-gray-900 mb-3">Informasi Akademik</h5>

                <div class="flex justify-between py-2 border-b">
                  <span class="text-gray-600">Angkatan</span>
                  <span class="font-medium">{{ student.batch_year || '-' }}</span>
                </div>

                <div class="flex justify-between py-2 border-b">
                  <span class="text-gray-600">Semester Saat Ini</span>
                  <span class="font-medium">{{ student.current_semester || '-' }}</span>
                </div>

                <div class="flex justify-between py-2 border-b">
                  <span class="text-gray-600">Tahun Ajar Saat Ini</span>
                  <span class="font-medium">{{ student.current_year || '-' }}</span>
                </div>

                <div class="flex justify-between py-2 border-b">
                  <span class="text-gray-600">IPK</span>
                  <span class="font-medium">{{ student.gpa ? student.gpa.toFixed(2) : '-' }}</span>
                </div>

                <div class="flex justify-between py-2 border-b">
                  <span class="text-gray-600">Prestasi Akademik</span>
                  <span class="font-medium">{{ getAcademicStanding(student.gpa) }}</span>
                </div>

                <div class="flex justify-between py-2 border-b">
                  <span class="text-gray-600">Kelas</span>
                  <span class="font-medium">{{ student.class || '-' }}</span>
                </div>

                <div class="flex justify-between py-2 border-b">
                  <span class="text-gray-600">Tanggal Masuk</span>
                  <span class="font-medium">{{ formatDate(student.enrollment_date) }}</span>
                </div>

                <div class="flex justify-between py-2 border-b">
                  <span class="text-gray-600">Lama Studi</span>
                  <span class="font-medium">{{ calculateStudyDuration(student.enrollment_date) }} tahun</span>
                </div>

                <div v-if="student.graduation_date" class="flex justify-between py-2 border-b">
                  <span class="text-gray-600">Tanggal Lulus</span>
                  <span class="font-medium">{{ formatDate(student.graduation_date) }}</span>
                </div>
              </div>

              <div class="space-y-4">
                <h5 class="font-semibold text-gray-900 mb-3">Progress Akademik</h5>

                <!-- GPA Progress Bar -->
                <div class="bg-gray-50 p-4 rounded-lg">
                  <div class="flex justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Progress IPK</span>
                    <span class="text-sm text-gray-500">{{ student.gpa ? student.gpa.toFixed(2) : '0.00' }}/4.00</span>
                  </div>
                  <div class="w-full bg-gray-200 rounded-full h-2">
                    <div
                      class="bg-gradient-to-r from-green-400 to-green-600 h-2 rounded-full"
                      :style="{ width: `${(student.gpa || 0) * 25}%` }"
                    ></div>
                  </div>
                </div>

                <!-- Study Duration Progress -->
                <div class="bg-gray-50 p-4 rounded-lg">
                  <div class="flex justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Progress Studi</span>
                    <span class="text-sm text-gray-500">{{ calculateStudyProgress(student) }}%</span>
                  </div>
                  <div class="w-full bg-gray-200 rounded-full h-2">
                    <div
                      class="bg-gradient-to-r from-blue-400 to-blue-600 h-2 rounded-full"
                      :style="{ width: `${calculateStudyProgress(student)}%` }"
                    ></div>
                  </div>
                </div>

                <!-- Quick Stats -->
                <div class="grid grid-cols-2 gap-4">
                  <div class="bg-green-50 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-green-600">{{ student.current_semester || 0 }}</div>
                    <div class="text-sm text-gray-600">Semester</div>
                  </div>
                  <div class="bg-blue-50 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ student.credits_completed || 0 }}</div>
                    <div class="text-sm text-gray-600">SKS Selesai</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Parent Info Tab -->
            <div v-if="activeTab === 'parent'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-4">
                <h5 class="font-semibold text-gray-900 mb-3">Data Ayah</h5>

                <div class="flex justify-between py-2 border-b">
                  <span class="text-gray-600">Nama Ayah</span>
                  <span class="font-medium">{{ student.father_name || '-' }}</span>
                </div>

                <div class="flex justify-between py-2 border-b">
                  <span class="text-gray-600">No. HP</span>
                  <span class="font-medium">{{ student.parent_phone || '-' }}</span>
                </div>

                <div class="flex justify-between py-2 border-b">
                  <span class="text-gray-600">Email</span>
                  <span class="font-medium">{{ student.parent_email || '-' }}</span>
                </div>
              </div>

              <div class="space-y-4">
                <h5 class="font-semibold text-gray-900 mb-3">Data Ibu</h5>

                <div class="flex justify-between py-2 border-b">
                  <span class="text-gray-600">Nama Ibu</span>
                  <span class="font-medium">{{ student.mother_name || '-' }}</span>
                </div>

                <div class="flex justify-between py-2 border-b">
                  <span class="text-gray-600">Alamat Orang Tua</span>
                  <span class="font-medium text-right max-w-xs">{{ student.parent_address || '-' }}</span>
                </div>
              </div>

              <div v-if="student.notes" class="md:col-span-2">
                <h5 class="font-semibold text-gray-900 mb-3">Catatan</h5>
                <div class="bg-yellow-50 p-4 rounded-lg">
                  <p class="text-gray-700">{{ student.notes }}</p>
                </div>
              </div>
            </div>

            <!-- History Tab -->
            <div v-if="activeTab === 'history'">
              <div class="space-y-6">
                <!-- Academic Progress Timeline -->
                <div>
                  <h5 class="font-semibold text-gray-900 mb-4">Riwayat Akademik</h5>
                  <div class="space-y-4">
                    <div class="flex items-start space-x-3">
                      <div class="flex-shrink-0 h-8 w-8 bg-green-500 rounded-full flex items-center justify-center">
                        <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                      </div>
                      <div>
                        <p class="font-medium">Pendaftaran</p>
                        <p class="text-sm text-gray-500">{{ formatDate(student.enrollment_date) }}</p>
                      </div>
                    </div>

                    <div v-if="student.graduation_date" class="flex items-start space-x-3">
                      <div class="flex-shrink-0 h-8 w-8 bg-blue-500 rounded-full flex items-center justify-center">
                        <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                      </div>
                      <div>
                        <p class="font-medium">Kelulusan</p>
                        <p class="text-sm text-gray-500">{{ formatDate(student.graduation_date) }}</p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- User Account Info -->
                <div v-if="student.hasUserAccount">
                  <h5 class="font-semibold text-gray-900 mb-4">Informasi Akun User</h5>
                  <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start space-x-3">
                      <svg class="h-6 w-6 text-blue-600 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                      </svg>
                      <div>
                        <p class="font-medium text-blue-900">Akun Portal Mahasiswa Aktif</p>
                        <p class="text-sm text-blue-700 mt-1">
                          Username: {{ student.student_number }}<br>
                          Email: {{ student.email }}<br>
                          Role: Mahasiswa
                        </p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- System Info -->
                <div>
                  <h5 class="font-semibold text-gray-900 mb-4">Informasi Sistem</h5>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex justify-between py-2 border-b">
                      <span class="text-gray-600">Dibuat Oleh</span>
                      <span class="font-medium">{{ student.creator?.name || 'System' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b">
                      <span class="text-gray-600">Tanggal Dibuat</span>
                      <span class="font-medium">{{ formatDateTime(student.created_at) }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b">
                      <span class="text-gray-600">Diupdate Oleh</span>
                      <span class="font-medium">{{ student.updater?.name || 'System' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b">
                      <span class="text-gray-600">Terakhir Diupdate</span>
                      <span class="font-medium">{{ formatDateTime(student.updated_at) }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer Actions -->
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button
            @click="$emit('close')"
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm"
          >
            Tutup
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed } from 'vue';
import studentService from '@/services/studentService';

export default {
  name: 'StudentDetailModal',
  props: {
    show: {
      type: Boolean,
      default: false
    },
    student: {
      type: Object,
      default: null
    }
  },
  emits: ['close', 'edit', 'accountCreated'],
  setup(props, { emit }) {
    const activeTab = ref('personal');

    const tabs = [
      { key: 'personal', label: 'Data Pribadi' },
      { key: 'academic', label: 'Akademik' },
      { key: 'parent', label: 'Orang Tua' },
      { key: 'history', label: 'Riwayat' }
    ];

    const getInitials = (name) => {
      if (!name) return 'M';
      return name.split(' ').map(word => word[0]).join('').toUpperCase().slice(0, 2);
    };

    const getStatusColor = (status) => {
      const colors = {
        'active': 'bg-green-100 text-green-800',
        'inactive': 'bg-gray-100 text-gray-800',
        'graduated': 'bg-blue-100 text-blue-800',
        'dropped_out': 'bg-red-100 text-red-800',
        'on_leave': 'bg-yellow-100 text-yellow-800'
      };
      return colors[status] || 'bg-gray-100 text-gray-800';
    };

    const getStatusLabel = (status) => {
      const labels = {
        'active': 'Aktif',
        'inactive': 'Tidak Aktif',
        'graduated': 'Lulus',
        'dropped_out': 'Drop Out',
        'on_leave': 'Cuti'
      };
      return labels[status] || status;
    };

    const getGenderLabel = (gender) => {
      const labels = {
        'L': 'Laki-laki',
        'P': 'Perempuan'
      };
      return labels[gender] || gender;
    };

    const formatDate = (date) => {
      if (!date) return '-';
      return new Date(date).toLocaleDateString('id-ID');
    };

    const formatDateTime = (date) => {
      if (!date) return '-';
      return new Date(date).toLocaleString('id-ID');
    };

    const calculateAge = (birthDate) => {
      if (!birthDate) return '-';
      const today = new Date();
      const birth = new Date(birthDate);
      let age = today.getFullYear() - birth.getFullYear();
      const monthDiff = today.getMonth() - birth.getMonth();
      if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
        age--;
      }
      return age;
    };

    const calculateStudyDuration = (enrollmentDate) => {
      if (!enrollmentDate) return 0;
      const today = new Date();
      const enrollment = new Date(enrollmentDate);
      let years = today.getFullYear() - enrollment.getFullYear();
      const monthDiff = today.getMonth() - enrollment.getMonth();
      if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < enrollment.getDate())) {
        years--;
      }
      return years;
    };

    const calculateStudyProgress = (student) => {
      if (!student.enrollment_date || !student.program_study?.duration_years) return 0;
      const currentDuration = calculateStudyDuration(student.enrollment_date);
      const expectedYears = student.program_study.duration_years;
      return Math.min((currentDuration / expectedYears) * 100, 100);
    };

    const getAcademicStanding = (gpa) => {
      if (!gpa) return '-';
      if (gpa >= 3.5) return 'Cum Laude';
      if (gpa >= 3.0) return 'Sangat Memuaskan';
      if (gpa >= 2.5) return 'Memuaskan';
      if (gpa >= 2.0) return 'Cukup';
      return 'Kurang';
    };

    const editStudent = () => {
      emit('edit', props.student);
    };

    const printStudentCard = async () => {
      try {
        const blob = await studentService.printStudentCard(props.student.id);
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = `kartu-mahasiswa-${props.student.student_number}.pdf`;
        link.click();
        window.URL.revokeObjectURL(url);
      } catch (error) {
        console.error('Error printing student card:', error);
        alert('Terjadi kesalahan saat mencetak kartu mahasiswa');
      }
    };

    const createUserAccount = async () => {
      if (!confirm('Apakah Anda yakin ingin membuat akun user untuk mahasiswa ini?')) {
        return;
      }

      try {
        await studentService.createUserAccount(props.student.id);
        emit('accountCreated');
        alert('Akun user berhasil dibuat dengan password default NIM');
      } catch (error) {
        console.error('Error creating user account:', error);
        alert('Terjadi kesalahan saat membuat akun user');
      }
    };

    return {
      activeTab,
      tabs,
      getInitials,
      getStatusColor,
      getStatusLabel,
      getGenderLabel,
      formatDate,
      formatDateTime,
      calculateAge,
      calculateStudyDuration,
      calculateStudyProgress,
      getAcademicStanding,
      editStudent,
      printStudentCard,
      createUserAccount
    };
  }
};
</script>