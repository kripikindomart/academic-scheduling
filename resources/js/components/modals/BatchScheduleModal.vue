<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <!-- Background overlay -->
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="$emit('close')"></div>

      <!-- Modal panel -->
      <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-6xl sm:w-full border border-gray-100">
        <!-- Modern gradient header -->
        <div class="relative bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-600 px-6 py-8 sm:px-8">
          <!-- Decorative background pattern -->
          <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0 bg-black opacity-5"></div>
            <svg class="absolute inset-0 w-full h-full" xmlns="http://www.w3.org/2000/svg">
              <defs>
                <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                  <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="1"/>
                </pattern>
              </defs>
              <rect width="100%" height="100%" fill="url(#grid)" />
            </svg>
          </div>

          <!-- Header content -->
          <div class="relative z-10 flex items-center justify-between">
            <div class="flex-1">
              <h3 class="text-3xl font-bold text-white mb-2 bg-clip-text text-transparent bg-gradient-to-r from-white to-blue-100">
                Template Jadwal Kelas
              </h3>
              <p class="text-blue-100 text-sm">Buat template daftar mata kuliah untuk satu semester</p>
            </div>
            <button
              @click="$emit('close')"
              class="ml-4 bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white p-3 rounded-xl transition-all duration-200 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-white/50"
            >
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>

        <!-- Progress indicator -->
        <div class="relative bg-gradient-to-r from-blue-50 via-purple-50 to-indigo-50 px-6 py-4 border-b border-gray-100">
          <div class="flex items-center justify-between mb-3">
            <div class="flex items-center space-x-2">
              <div class="flex items-center justify-center w-8 h-8 bg-white rounded-lg shadow-sm border border-gray-200">
                <span class="text-sm font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">{{ currentStep }}</span>
              </div>
              <span class="text-sm font-medium text-gray-700">Progress</span>
            </div>
            <span class="text-sm text-gray-500 bg-white px-3 py-1 rounded-full border border-gray-200">{{ currentStep }} dari 3</span>
          </div>
          <div class="relative">
            <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
              <div
                class="bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-600 h-3 rounded-full transition-all duration-500 ease-out relative"
                :style="{ width: (currentStep / 3 * 100) + '%' }"
              >
                <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
              </div>
            </div>
            <!-- Step indicators -->
            <div class="absolute inset-0 flex items-center justify-between px-1">
              <div v-for="step in 3" :key="step"
                   class="w-5 h-5 rounded-full border-2 transition-all duration-300"
                   :class="step <= currentStep ? 'bg-white border-blue-600' : 'bg-gray-300 border-gray-400'">
              </div>
            </div>
          </div>
        </div>

        <!-- Content area -->
        <div class="bg-white px-6 py-6 sm:px-8 sm:py-8">

          <!-- Step 1: Configuration -->
          <div v-if="currentStep === 1" class="space-y-6">
            <div class="relative bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-5 mb-6 shadow-sm">
              <div class="flex">
                <div class="flex-shrink-0">
                  <div class="flex items-center justify-center w-10 h-10 bg-blue-100 rounded-lg">
                    <svg class="h-6 w-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                  </div>
                </div>
                <div class="ml-4 flex-1">
                  <h4 class="text-sm font-semibold text-blue-900 mb-1">Langkah 1: Konfigurasi Awal</h4>
                  <p class="text-sm text-blue-700 leading-relaxed">
                    Pilih kelas dan konfigurasi dasar untuk jadwal batch. Semua jadwal akan menggunakan tahun akademik yang aktif.
                  </p>
                </div>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700">Program Studi <span class="text-red-500">*</span></label>
                <select v-model="batchConfig.program_study_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                  <option value="">Pilih Program Studi</option>
                  <option v-for="program in programStudies" :key="program.id" :value="program.id">
                    {{ program.name }}
                  </option>
                </select>
                <div v-if="programStudies.length === 0" class="mt-1 text-xs text-red-500">
                  Tidak ada data program studi
                </div>
                <div v-else class="mt-1 text-xs text-gray-500">
                  {{ programStudies.length }} program studi tersedia
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Kelas <span class="text-red-500">*</span></label>
                <select v-model="batchConfig.school_class_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                  <option value="">Pilih Kelas</option>
                  <option v-for="classItem in (classes || [])" :key="classItem?.id || 'unknown'" :value="classItem?.id">
                    {{ classItem?.name || 'Unknown Class' }} ({{ classItem?.code || 'N/A' }})
                  </option>
                </select>
                <div v-if="classes.length === 0" class="mt-1 text-xs text-orange-500">
                  Pilih program studi terlebih dahulu
                </div>
                <div v-else class="mt-1 text-xs text-gray-500">
                  {{ classes.length }} kelas tersedia
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Tahun Akademik <span class="text-red-500">*</span></label>
                <select v-model="batchConfig.academic_year" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                  <option value="">Pilih Tahun Akademik</option>
                  <option v-for="year in academicYears" :key="year.id" :value="getAcademicYearValue(year)">
                    {{ getAcademicYearDisplay(year) }}
                  </option>
                </select>
                <div v-if="academicYears.length === 0" class="mt-1 text-xs text-red-500">
                  Tidak ada data tahun akademik
                </div>
                <div v-else class="mt-1 text-xs text-gray-500">
                  {{ academicYears.length }} tahun akademik tersedia
                </div>
              </div>
            </div>

            </div>

          <!-- Step 2: Schedules -->
          <div v-if="currentStep === 2" class="space-y-6">
            <div class="relative bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-200 rounded-xl p-5 mb-6 shadow-sm">
              <div class="flex">
                <div class="flex-shrink-0">
                  <div class="flex items-center justify-center w-10 h-10 bg-purple-100 rounded-lg">
                    <svg class="h-6 w-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                  </div>
                </div>
                <div class="ml-4 flex-1">
                  <h4 class="text-sm font-semibold text-purple-900 mb-1">Langkah 2: Konfigurasi Jadwal Kuliah</h4>
                  <p class="text-sm text-purple-700 leading-relaxed">
                    Tambahkan mata kuliah yang akan dijadwalkan. Setiap mata kuliah dapat memiliki jadwal yang berbeda meskipun dalam kelas yang sama.
                  </p>
                </div>
              </div>
            </div>

            <div class="flex justify-between items-center mb-6">
              <h4 class="text-lg font-medium text-gray-900">Daftar Jadwal</h4>
            </div>

            <div v-if="batchSchedules.length === 0" class="text-center py-16 bg-gradient-to-br from-gray-50 to-blue-50 rounded-2xl border border-gray-200">
              <div class="mx-auto w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                <svg class="h-10 w-10 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
              </div>
              <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Jadwal Kuliah</h3>
              <p class="text-sm text-gray-600 mb-4">Tambahkan jadwal mata kuliah untuk memulai konfigurasi batch</p>
              <button
                @click="addScheduleEntry"
                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Jadwal Pertama
              </button>
            </div>

            <div v-else class="space-y-4">
              <!-- Sticky Add Button for non-empty schedules -->
              <div class="sticky bottom-0 pt-2 pb-4 bg-gradient-to-b from-transparent via-white to-white z-10">
                <div class="flex justify-center">
                  <button
                    @click="addScheduleEntry"
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-medium rounded-xl shadow-lg transform transition-all duration-200 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                  >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Jadwal
                  </button>
                </div>
              </div>
              <div v-for="(schedule, index) in batchSchedules" :key="index" class="bg-gradient-to-br from-white to-gray-50 border border-gray-200 rounded-xl shadow-md overflow-hidden">
                <!-- Schedule Header with Toggle -->
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 px-4 py-3">
                  <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                      <!-- Collapse/Expand Toggle -->
                      <button
                        @click="schedule.expanded = !schedule.expanded"
                        class="w-6 h-6 bg-white/20 rounded-lg flex items-center justify-center text-white hover:bg-white/30 transition-all duration-200"
                      >
                        <svg
                          class="w-4 h-4 transition-transform duration-200"
                          :class="schedule.expanded ? 'rotate-180' : ''"
                          fill="none"
                          stroke="currentColor"
                          viewBox="0 0 24 24"
                        >
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                      </button>
                      <!-- Schedule Number -->
                      <div class="w-6 h-6 bg-white/20 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-xs">{{ index + 1 }}</span>
                      </div>
                      <!-- Course Name -->
                      <h5 class="text-sm font-semibold text-white">
                        {{ getCourseName(schedule.course_id) || 'Jadwal #' + (index + 1) }}
                      </h5>
                      <!-- Schedule Summary (when collapsed) -->
                      <span v-if="!schedule.expanded" class="text-xs text-blue-100 ml-2">
                        {{ schedule.day || '-' }}, {{ schedule.start_time || '-' }}-{{ schedule.end_time || '-' }}
                      </span>
                    </div>
                    <!-- Actions -->
                    <div class="flex items-center space-x-1">
                      <button
                        @click="duplicateScheduleEntry(index)"
                        class="bg-blue-500 hover:bg-blue-600 text-white p-1 rounded transition-colors duration-200"
                        title="Duplikat Jadwal"
                      >
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8"></path>
                        </svg>
                      </button>
                      <button
                        @click="removeScheduleEntry(index)"
                        class="bg-red-500 hover:bg-red-600 text-white p-1 rounded transition-colors duration-200"
                        title="Hapus Jadwal"
                      >
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Collapsible Content -->
                <div v-show="schedule.expanded !== false" class="p-4 space-y-4">
                  <!-- Content di dalam sini akan diperkecil -->
                  <!-- Course Information -->
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                      <label class="block text-sm font-semibold text-gray-700 mb-2">Mata Kuliah <span class="text-red-500">*</span></label>
                      <select v-model="schedule.course_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        <option value="">Pilih Mata Kuliah</option>
                        <option v-for="course in courses.filter(c => c && c.id)" :key="course.id" :value="course.id">
                          {{ course.course_code }} - {{ course.course_name }}
                        </option>
                      </select>
                    </div>

                    <div>
                      <label class="block text-sm font-semibold text-gray-700 mb-2">Hari <span class="text-red-500">*</span></label>
                      <select v-model="schedule.day" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        <option value="">Pilih Hari</option>
                        <option value="senin">Senin</option>
                        <option value="selasa">Selasa</option>
                        <option value="rabu">Rabu</option>
                        <option value="kamis">Kamis</option>
                        <option value="jumat">Jumat</option>
                        <option value="sabtu">Sabtu</option>
                      </select>
                    </div>
                  </div>

                  <!-- Time Information -->
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                      <label class="block text-sm font-semibold text-gray-700 mb-2">Jam Mulai <span class="text-red-500">*</span></label>
                      <input
                        v-model="schedule.start_time"
                        type="time"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                      >
                    </div>

                    <div>
                      <label class="block text-sm font-semibold text-gray-700 mb-2">Jam Selesai <span class="text-red-500">*</span></label>
                      <input
                        v-model="schedule.end_time"
                        type="time"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                      >
                    </div>
                  </div>

                  <!-- Mode and Lecturers -->
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                      <label class="block text-sm font-semibold text-gray-700 mb-2">Mode Perkuliahan <span class="text-red-500">*</span></label>
                      <select
                        v-model="schedule.mode"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                        @change="handleModeChange(schedule)"
                      >
                        <option value="offline">Offline</option>
                        <option value="online">Online</option>
                        <option value="hybrid">Hybrid</option>
                      </select>
                    </div>

                    <div v-if="schedule.mode === 'hybrid'">
                      <label class="block text-sm font-semibold text-gray-700 mb-2">Persentase Offline (%) <span class="text-red-500">*</span></label>
                      <input
                        v-model.number="schedule.offline_percentage"
                        type="number"
                        min="1"
                        max="99"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                        placeholder="Contoh: 70"
                      >
                      <p class="mt-1 text-xs text-gray-500">{{ schedule.offline_percentage }}% offline, {{ 100 - schedule.offline_percentage }}% online</p>
                    </div>
                  </div>

                  <!-- Room Selection (only for offline and hybrid modes) -->
                  <div v-if="schedule.mode === 'offline' || schedule.mode === 'hybrid'">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Ruang <span class="text-red-500">*</span></label>
                    <select v-model="schedule.room_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                      <option value="">Pilih Ruang</option>
                      <option v-for="room in (rooms || [])" :key="room?.id || 'unknown'" :value="room?.id">
                        {{ room?.room_code || 'N/A' }} - {{ room?.name || 'Unknown Room' }} (Kapasitas: {{ room?.capacity || 'N/A' }})
                      </option>
                    </select>
                  </div>

                  <!-- Team Teaching -->
                  <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Dosen Pengajar <span class="text-red-500">*</span></label>
                    <div class="space-y-3">
                      <div v-for="(lecturerId, lecturerIndex) in schedule.lecturer_ids" :key="lecturerIndex" class="flex items-center space-x-2">
                        <select
                          v-model="schedule.lecturer_ids[lecturerIndex]"
                          class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                          :disabled="isLecturerSelected(schedule, lecturerId, lecturerIndex)"
                        >
                          <option value="">Pilih Dosen</option>
                          <option
                            v-for="lecturer in (Array.isArray(lecturers) ? lecturers.filter(l => l && l.id) : [])"
                            :key="lecturer.id"
                            :value="lecturer.id"
                            :disabled="isLecturerSelected(schedule, lecturer.id, lecturerIndex)"
                          >
                            {{ lecturer.name }} - {{ lecturer.employee_number || lecturer.nip || 'N/A' }}
                          </option>
                        </select>
                        <button
                          v-if="schedule.lecturer_ids.length > 1"
                          @click="removeLecturer(schedule, lecturerIndex)"
                          class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-lg transition-colors duration-200"
                        >
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                          </svg>
                        </button>
                      </div>
                      <button
                        @click="addLecturer(schedule)"
                        :disabled="!canAddMoreLecturers(schedule)"
                        class="bg-green-500 hover:bg-green-600 disabled:bg-gray-400 disabled:cursor-not-allowed text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center space-x-1"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Tambah Dosen (Team Teaching)</span>
                      </button>
                    </div>
                  </div>

                  <!-- Date Range and Sessions -->
                  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                      <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Mulai Kuliah <span class="text-red-500">*</span></label>
                      <input
                        v-model="schedule.start_date"
                        type="date"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                      >
                    </div>

                    <div>
                      <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Selesai Kuliah <span class="text-red-500">*</span></label>
                      <input
                        v-model="schedule.end_date"
                        type="date"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                      >
                    </div>

                    <div>
                      <label class="block text-sm font-semibold text-gray-700 mb-2">Sesi per Pertemuan <span class="text-red-500">*</span></label>
                      <input
                        v-model.number="schedule.session_count"
                        type="number"
                        min="1"
                        max="4"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all duration-200"
                        placeholder="1"
                      >
                      <p class="mt-1 text-xs text-gray-600">
                        <span v-if="schedule.session_count">
                          {{ schedule.session_count === 1 ? '16 pertemuan total' : schedule.session_count === 2 ? '8 pertemuan total' : schedule.session_count === 3 ? '6 pertemuan total' : '4 pertemuan total' }}
                        </span>
                        <span v-else>16 pertemuan total</span>
                      </p>
                    </div>
                  </div>

                  <!-- Notes -->
                  <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan</label>
                    <textarea
                      v-model="schedule.notes"
                      rows="2"
                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                      placeholder="Catatan opsional untuk jadwal ini..."
                    ></textarea>
                  </div>

                  <!-- Validation Messages -->
                  <div v-if="getScheduleValidation(schedule)" class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                    <div class="flex items-start space-x-2">
                      <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                      </svg>
                      <div>
                        <p class="text-sm text-yellow-800 font-medium">Peringatan Validasi</p>
                        <p class="text-sm text-yellow-700">{{ getScheduleValidation(schedule) }}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Step 3: Review & Confirm -->
          <div v-if="currentStep === 3" class="space-y-6">
            <div class="relative bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl p-5 mb-6 shadow-sm">
              <div class="flex">
                <div class="flex-shrink-0">
                  <div class="flex items-center justify-center w-10 h-10 bg-green-100 rounded-lg">
                    <svg class="h-6 w-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                  </div>
                </div>
                <div class="ml-4 flex-1">
                  <h4 class="text-sm font-semibold text-green-900 mb-1">Langkah 3: Konfirmasi & Generate Jadwal</h4>
                  <p class="text-sm text-green-700 leading-relaxed">
                    Periksa kembali konfigurasi jadwal yang akan dibuat. Sistem akan mengizinkan bentrok sementara untuk proses batch dan akan memvalidasi setiap jadwal.
                  </p>
                </div>
              </div>
            </div>

            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-2xl p-6 shadow-sm">
              <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                  <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"></path>
                  </svg>
                </div>
                Konfigurasi Batch
              </h4>
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl p-4 border border-gray-200">
                  <p class="text-xs font-medium text-gray-500 mb-1">Program Studi</p>
                  <p class="text-sm font-semibold text-gray-900">{{ getProgramName(batchConfig.program_study_id) }}</p>
                </div>
                <div class="bg-white rounded-xl p-4 border border-gray-200">
                  <p class="text-xs font-medium text-gray-500 mb-1">Kelas</p>
                  <p class="text-sm font-semibold text-gray-900">{{ getClassName(batchConfig.school_class_id) }}</p>
                </div>
                <div class="bg-white rounded-xl p-4 border border-gray-200">
                  <p class="text-xs font-medium text-gray-500 mb-1">Semester</p>
                  <p class="text-sm font-semibold text-gray-900">{{ batchConfig.semester }}</p>
                </div>
                <div class="bg-white rounded-xl p-4 border border-gray-200">
                  <p class="text-xs font-medium text-gray-500 mb-1">Tahun Akademik</p>
                  <p class="text-sm font-semibold text-gray-900">{{ batchConfig.academic_year }}</p>
                </div>
              </div>
            </div>

            <div>
              <h4 class="text-lg font-medium text-gray-900 mb-3">Ringkasan Jadwal ({{ batchSchedules.length }} jadwal)</h4>
              <div class="overflow-x-auto">
                <table class="w-full text-sm">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Mata Kuliah</th>
                      <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Hari</th>
                      <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jam</th>
                      <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ruang</th>
                      <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Dosen</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="(schedule, index) in batchSchedules" :key="index">
                      <td class="px-3 py-2">{{ getCourseName(schedule.course_id) }}</td>
                      <td class="px-3 py-2 capitalize">{{ schedule.day }}</td>
                      <td class="px-3 py-2">{{ schedule.start_time }} - {{ schedule.end_time }}</td>
                      <td class="px-3 py-2">{{ getRoomName(schedule.room_id) }}</td>
                      <td class="px-3 py-2">{{ getLecturerName(schedule.lecturer_ids) }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Warnings -->
            <div v-if="hasConflicts" class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
              <div class="flex">
                <div class="flex-shrink-0">
                  <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                  </svg>
                </div>
                <div class="ml-3">
                  <p class="text-sm text-yellow-700">
                    <strong>Perhatian:</strong> Terdapat {{ conflictCount }} kemungkinan bentrok jadwal. Sistem akan tetap membuat jadwal namun harap periksa kembali.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal actions -->
        <div class="bg-gradient-to-r from-gray-50 to-blue-50 px-6 py-6 border-t border-gray-200">
          <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-4 sm:space-y-0">
            <!-- Step indicators -->
            <div class="flex items-center space-x-2">
              <div v-for="step in 3" :key="step"
                   class="flex items-center">
                <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-semibold transition-all duration-300"
                     :class="step < currentStep ? 'bg-green-500 text-white' : step === currentStep ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-500'">
                  <svg v-if="step < currentStep" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                  <span v-else>{{ step }}</span>
                </div>
                <div v-if="step < 3" class="w-8 h-1 bg-gray-300 rounded-full mx-2"
                     :class="step < currentStep ? 'bg-green-500' : ''"></div>
              </div>
            </div>

            <!-- Action buttons -->
            <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3">
              <button
                v-if="currentStep > 1"
                @click="previousStep"
                class="order-2 sm:order-1 px-6 py-3 bg-white border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-all duration-200"
              >
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
              </button>

              <button
                @click="$emit('close')"
                class="order-1 sm:order-2 px-6 py-3 bg-white border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-all duration-200"
              >
                Batal
              </button>

              <button
                v-if="currentStep < 3"
                @click="nextStep"
                :disabled="!isCurrentStepValid"
                class="order-3 sm:order-3 px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-medium rounded-xl hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 transform hover:scale-105"
              >
                Lanjutkan
                <svg class="w-4 h-4 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                </svg>
              </button>

              <button
                v-if="currentStep === 3"
                @click="createBatchSchedules"
                :disabled="isSubmitting || batchSchedules.length === 0"
                class="order-3 sm:order-3 px-8 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-medium rounded-xl hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 transform hover:scale-105"
              >
                <svg v-if="isSubmitting" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white inline" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg v-else class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ isSubmitting ? 'Menyimpan Template...' : 'Simpan Template Jadwal Kelas' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, watch, onMounted } from 'vue';
import { useToastStore } from '@/stores/toast';

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['close', 'success']);

const toastStore = useToastStore();

const currentStep = ref(1);
const isSubmitting = ref(false);

const batchConfig = reactive({
  program_study_id: '',
  school_class_id: '',
  academic_year: ''
});

const batchSchedules = ref([]);

// New reactive data for bulk course selection
const selectedCourses = ref([]);
const courseAssignments = ref([]);
const scheduleGenerationMode = ref('manual'); // 'manual' or 'auto'
const autoGenerateInterval = ref(1); // weeks between sessions
const skipHolidays = ref(true);

const programStudies = ref([]);
const classes = ref([]);
const courses = ref([]);
const rooms = ref([]);
const lecturers = ref([]);
const academicYears = ref([]);

const isCurrentStepValid = computed(() => {
  if (currentStep.value === 1) {
    return batchConfig.program_study_id &&
           batchConfig.school_class_id &&
           batchConfig.academic_year;
  }
  if (currentStep.value === 2) {
    return batchSchedules.value.length > 0;
  }
  return true;
});

const hasConflicts = computed(() => {
  const conflicts = [];
  for (let i = 0; i < batchSchedules.value.length; i++) {
    for (let j = i + 1; j < batchSchedules.value.length; j++) {
      if (hasTimeConflict(batchSchedules.value[i], batchSchedules.value[j])) {
        conflicts.push({ i, j });
      }
    }
  }
  return conflicts.length > 0;
});

const conflictCount = computed(() => {
  const conflicts = [];
  for (let i = 0; i < batchSchedules.value.length; i++) {
    for (let j = i + 1; j < batchSchedules.value.length; j++) {
      if (hasTimeConflict(batchSchedules.value[i], batchSchedules.value[j])) {
        conflicts.push({ i, j });
      }
    }
  }
  return conflicts.length;
});

const hasTimeConflict = (schedule1, schedule2) => {
  if (schedule1.day !== schedule2.day) return false;
  if (schedule1.room_id !== schedule2.room_id) return false;

  const start1 = new Date(`2000-01-01 ${schedule1.start_time}`);
  const end1 = new Date(`2000-01-01 ${schedule1.end_time}`);
  const start2 = new Date(`2000-01-01 ${schedule2.start_time}`);
  const end2 = new Date(`2000-01-01 ${schedule2.end_time}`);

  return (start1 < end2) && (start2 < end1);
};

const addScheduleEntry = () => {
  batchSchedules.value.push({
    course_id: '',
    day: '',
    start_time: '',
    end_time: '',
    room_id: '',
    lecturer_ids: [''], // Support multiple lecturers for team teaching
    mode: 'offline', // online, offline, hybrid
    offline_percentage: 100, // percentage for offline sessions
    start_date: '',
    end_date: '',
    session_count: 1, // sesi per pertemuan (1 = 16 pertemuan, 2 = 8 pertemuan)
    notes: '',
    expanded: true // untuk collapsible functionality
  });
};

const duplicateScheduleEntry = (index) => {
  const originalSchedule = batchSchedules.value[index];
  const duplicatedSchedule = { ...originalSchedule };

  // Create new lecturers array copy
  duplicatedSchedule.lecturer_ids = [...originalSchedule.lecturer_ids];

  // Insert the duplicate right after the original
  batchSchedules.value.splice(index + 1, 0, duplicatedSchedule);

  // Show success message
  const courseName = getCourseName(duplicatedSchedule.course_id);
  toastStore.success('Berhasil', `Jadwal "${courseName}" berhasil diduplikasi`);
  console.log('✅ Schedule duplicated:', courseName);
};

const removeScheduleEntry = (index) => {
  batchSchedules.value.splice(index, 1);
};

const addLecturer = (schedule) => {
  schedule.lecturer_ids.push('');
};

const removeLecturer = (schedule, index) => {
  schedule.lecturer_ids.splice(index, 1);
};

// Helper functions for preventing duplicate lecturer selections
const isLecturerSelected = (schedule, lecturerId, currentIndex) => {
  if (!lecturerId) return false; // Empty selection is not disabled

  // Check if this lecturer is already selected in other positions
  return schedule.lecturer_ids.some((id, index) =>
    id === lecturerId && index !== currentIndex
  );
};

const canAddMoreLecturers = (schedule) => {
  // Check if there are available lecturers that haven't been selected yet
  const selectedLecturerIds = schedule.lecturer_ids.filter(id => id);
  const availableLecturers = lecturers.value.filter(l => l && l.id);

  // Can add more if there are unselected lecturers available
  return availableLecturers.length > selectedLecturerIds.length;
};

const handleModeChange = (schedule) => {
  // Reset room selection when switching to online mode
  if (schedule.mode === 'online') {
    schedule.room_id = '';
  }
  // Set default offline percentage for hybrid mode
  if (schedule.mode === 'hybrid' && !schedule.offline_percentage) {
    schedule.offline_percentage = 70;
  }
};

const getScheduleValidation = (schedule) => {
  const validations = [];

  // Check required fields
  if (!schedule.course_id) validations.push('Mata kuliah harus dipilih');
  if (!schedule.day) validations.push('Hari harus dipilih');
  if (!schedule.start_time) validations.push('Jam mulai harus diisi');
  if (!schedule.end_time) validations.push('Jam selesai harus diisi');

  // Check time logic
  if (schedule.start_time && schedule.end_time) {
    const start = new Date(`1970-01-01T${schedule.start_time}`);
    const end = new Date(`1970-01-01T${schedule.end_time}`);

    if (end <= start) {
      validations.push('Jam selesai harus setelah jam mulai');
    }
  }

  // Check room requirement for offline and hybrid modes
  if ((schedule.mode === 'offline' || schedule.mode === 'hybrid') && !schedule.room_id) {
    validations.push('Ruang harus dipilih untuk mode offline/hybrid');
  }

  // Check lecturers
  const validLecturers = schedule.lecturer_ids?.filter(id => id) || [];
  if (validLecturers.length === 0) validations.push('Minimal satu dosen harus dipilih');

  // Check date range
  if (!schedule.start_date) validations.push('Tanggal mulai harus diisi');
  if (!schedule.end_date) validations.push('Tanggal selesai harus diisi');
  if (schedule.start_date && schedule.end_date && schedule.start_date > schedule.end_date) {
    validations.push('Tanggal mulai tidak boleh lebih besar dari tanggal selesai');
  }

  // Check session count (sesi per pertemuan)
  if (!schedule.session_count || schedule.session_count < 1) {
    validations.push('Jumlah sesi per pertemuan minimal 1');
  }
  if (schedule.session_count && schedule.session_count > 4) {
    validations.push('Jumlah sesi per pertemuan maksimal 4');
  }

  // Calculate total meetings
  const totalMeetings = schedule.session_count ? 16 / schedule.session_count : 16;
  if (schedule.session_count && totalMeetings > 32) {
    validations.push(`Dengan ${schedule.session_count} sesi per pertemuan, total pertemuan ${Math.round(totalMeetings)} terlalu banyak. Maksimal 32 pertemuan.`);
  }

  // Check date range capacity for total meetings
  if (schedule.start_date && schedule.end_date && schedule.session_count) {
    const startDate = new Date(schedule.start_date);
    const endDate = new Date(schedule.end_date);
    const weeksDiff = Math.ceil((endDate - startDate) / (7 * 24 * 60 * 60 * 1000));

    if (totalMeetings > weeksDiff) {
      validations.push(`Rentang tanggal hanya ${weeksDiff} minggu, tidak cukup untuk ${Math.round(totalMeetings)} pertemuan (${schedule.session_count} sesi per pertemuan).`);
    }
  }

  // Check time logic
  if (schedule.start_time && schedule.end_time && schedule.start_time >= schedule.end_time) {
    validations.push('Jam selesai harus lebih besar dari jam mulai');
  }

  return validations.length > 0 ? validations[0] : null;
};

const nextStep = () => {
  if (currentStep.value < 3) {
    currentStep.value++;
  }
};

const previousStep = () => {
  if (currentStep.value > 1) {
    currentStep.value--;
  }
};

const getProgramName = (id) => {
  const program = programStudies.value.find(p => p.id === id);
  return program ? program.name : '-';
};

const getClassName = (id) => {
  const classItem = classes.value.find(c => c.id === id);
  return classItem ? classItem.name : '-';
};

const getCourseName = (id) => {
  const course = courses.value.find(c => c.id === id);
  return course ? `${course.course_code} - ${course.course_name}` : '-';
};

const getRoomName = (id) => {
  const room = rooms.value.find(r => r.id === id);
  return room ? `${room.room_code} - ${room.name}` : '-';
};

const getLecturerName = (ids) => {
  if (!Array.isArray(ids)) {
    // Handle old single lecturer format
    const lecturer = lecturers.value.find(l => l.id === ids);
    return lecturer ? `${lecturer.name} - ${lecturer.employee_number || lecturer.nip || 'N/A'}` : 'Belum dipilih';
  }

  // Handle multiple lecturers (team teaching)
  const validLecturers = ids.filter(id => id).map(id => {
    const lecturer = lecturers.value.find(l => l.id === id);
    return lecturer ? `${lecturer.name} - ${lecturer.employee_number || lecturer.nip || 'N/A'}` : null;
  }).filter(Boolean);

  return validLecturers.length > 0 ? validLecturers.join(', ') : 'Belum dipilih';
};

const getAcademicYearDisplay = (year) => {
  if (!year) return '';

  let display = '';

  // Handle different data structures
  if (year.academic_calendar_year) {
    display = year.academic_calendar_year;
  } else if (year.year) {
    display = year.year;
  } else if (year.name) {
    // Remove "Ganjil" or "Genap" from display if it already contains academic year
    const cleanName = year.name.replace(/^(Ganjil|Genap)\s+/i, '');
    display = cleanName || year.name;
  } else if (typeof year === 'string') {
    display = year;
  } else {
    return 'Unknown Year';
  }

  // Add active status with better formatting
  if (year.is_active) {
    display = `${display} (Aktif)`;
  }

  return display;
};

const getAcademicYearValue = (year) => {
  // Ensure we return the correct value for the dropdown
  let value = '';
  if (year.academic_calendar_year) {
    value = year.academic_calendar_year;
  } else if (year.year) {
    value = year.year;
  } else if (year.name) {
    const cleanName = year.name.replace(/^(Ganjil|Genap)\s+/i, '');
    value = cleanName || year.name;
  } else if (typeof year === 'string') {
    value = year;
  }

  return value;
};

const createBatchSchedules = async () => {
  try {
    isSubmitting.value = true;

    // Extract academic year info from selected academic year
    const selectedAcademicYear = academicYears.value.find(ay => getAcademicYearValue(ay) === batchConfig.academic_year);
    const academicYearId = selectedAcademicYear?.id;

    // Create Class Schedule template data
    const classScheduleData = {
      title: `Jadwal Kelas ${classes.value.find(c => c.id === batchConfig.school_class_id)?.name || ''} - ${batchConfig.academic_year}`,
      program_study_id: batchConfig.program_study_id,
      class_id: batchConfig.school_class_id,
      academic_year_id: academicYearId,
      semester: batchConfig.academic_year.includes('Ganjil') ? 'Ganjil' : 'Genap',
      online_percentage: 0,
      offline_percentage: 100,
      description: `Template jadwal kelas untuk semester ${batchConfig.academic_year}`,
      status: 'draft'
    };

    console.log('🚀 Creating Class Schedule:', classScheduleData);

    // Check if Class Schedule already exists for this class and academic year
    const existingSchedulesResponse = await fetch(`/api/class-schedules?class_id=${batchConfig.school_class_id}&program_study_id=${batchConfig.program_study_id}`, {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Accept': 'application/json'
      }
    });

    let classScheduleId;

    if (existingSchedulesResponse.ok) {
      const existingResult = await existingSchedulesResponse.json();
      const existingSchedules = existingResult.data || [];

      // Use existing schedule if found
      if (existingSchedules.length > 0) {
        classScheduleId = existingSchedules[0].id;
        console.log('✅ Using existing Class Schedule:', classScheduleId);
        toastStore.success('Berhasil', `Menambah ${batchSchedules.value.length} mata kuliah ke template jadwal kelas yang sudah ada`);
      } else {
        // Create new Class Schedule if none exists
        const classScheduleResponse = await fetch('/api/class-schedules', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${localStorage.getItem('token')}`,
            'Accept': 'application/json'
          },
          body: JSON.stringify(classScheduleData)
        });

        if (!classScheduleResponse.ok) {
          const errorText = await classScheduleResponse.text();
          console.error('❌ Class Schedule Creation Error:', errorText);
          toastStore.error('Error', 'Gagal membuat template jadwal kelas');
          return;
        }

        const classScheduleResult = await classScheduleResponse.json();
        classScheduleId = classScheduleResult.data?.id;
        console.log('✅ New Class Schedule Created:', classScheduleId);
      }
    } else {
      // Error checking existing schedules, create new one
      const classScheduleResponse = await fetch('/api/class-schedules', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Accept': 'application/json'
        },
        body: JSON.stringify(classScheduleData)
      });

      if (!classScheduleResponse.ok) {
        const errorText = await classScheduleResponse.text();
        console.error('❌ Class Schedule Creation Error:', errorText);
        toastStore.error('Error', 'Gagal membuat template jadwal kelas');
        return;
      }

      const classScheduleResult = await classScheduleResponse.json();
      classScheduleId = classScheduleResult.data?.id;
      console.log('✅ Class Schedule Created:', classScheduleId);
    }

    if (!classScheduleId) {
      toastStore.error('Error', 'Gagal membuat template jadwal kelas - tidak ada ID');
      return;
    }

    console.log('✅ Class Schedule Created:', classScheduleId);

    // Now add courses as Class Schedule Details
    let successCount = 0;
    for (const schedule of batchSchedules.value) {
      try {
        const course = courses.value.find(c => c.id === schedule.course_id);
        const firstLecturer = schedule.lecturer_ids.find(id => id);

        // Calculate total meetings based on session count
        const totalMeetings = schedule.session_count ? 16 / schedule.session_count : 16;

        const detailData = {
          course_id: schedule.course_id,
          lecturer_id: firstLecturer,
          room_id: schedule.mode === 'online' ? null : schedule.room_id,
          day_of_week: schedule.day,
          start_time: schedule.start_time,
          end_time: schedule.end_time,
          start_date: schedule.start_date,
          end_date: schedule.end_date,
          sessions_per_meeting: schedule.session_count || 1,
          total_meetings: Math.round(totalMeetings),
          meeting_type: schedule.mode === 'online' ? 'online' : 'offline',
          is_online: schedule.mode === 'online',
          notes: schedule.notes || ''
        };

        const detailResponse = await fetch(`/api/class-schedules/${classScheduleId}/add-course`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${localStorage.getItem('token')}`,
            'Accept': 'application/json'
          },
          body: JSON.stringify(detailData)
        });

        if (detailResponse.ok) {
          successCount++;
          console.log(`✅ Course ${course?.course_name} added to class schedule`);
        } else {
          console.error(`❌ Failed to add course ${course?.course_name}`);
        }
      } catch (error) {
        console.error('Error adding course to class schedule:', error);
      }
    }

    if (successCount > 0) {
      toastStore.success('Berhasil', `Template jadwal kelas berhasil dibuat dengan ${successCount} mata kuliah`);
      emit('success');
      emit('close');
      resetForm();
    } else {
      toastStore.error('Error', 'Gagal menambahkan mata kuliah ke template jadwal');
    }
  } catch (error) {
    console.error('Error creating class schedule template:', error);
    toastStore.error('Error', 'Terjadi kesalahan saat membuat template jadwal kelas');
  } finally {
    isSubmitting.value = false;
  }
};


const resetForm = () => {
  currentStep.value = 1;
  Object.assign(batchConfig, {
    program_study_id: '',
    school_class_id: '',
    academic_year: ''
  });
  batchSchedules.value = [];
  selectedCourses.value = [];
  courseAssignments.value = [];
};

// Watch for program study changes to filter classes
watch(() => batchConfig.program_study_id, async (newProgramId) => {
  console.log('🔄 Program study changed to:', newProgramId);
  if (newProgramId) {
    await loadClassesForBatch(newProgramId);
  } else {
    classes.value = [];
    batchConfig.school_class_id = '';
  }
});


// Helper functions for lecturer management
const addLecturerToCourse = (course, lecturerId) => {
  if (!course.lecturer_ids) course.lecturer_ids = [];
  if (!course.lecturer_ids.includes(lecturerId)) {
    course.lecturer_ids.push(lecturerId);
  }
};

const removeLecturerFromCourse = (course, lecturerId) => {
  if (course.lecturer_ids) {
    course.lecturer_ids = course.lecturer_ids.filter(id => id !== lecturerId);
  }
};

const getSessionTypeLabel = (type) => {
  const types = {
    'lecture': 'Kuliah',
    'uts': 'UTS',
    'uas': 'UAS'
  };
  return types[type] || type;
};

const isScheduleConfigurationValid = computed(() => {
  return batchSchedules.value.length > 0 && batchSchedules.value.every(schedule =>
    schedule.course_id &&
    schedule.day &&
    schedule.start_time &&
    schedule.end_time &&
    schedule.room_id &&
    schedule.lecturer_ids &&
    schedule.lecturer_ids.length > 0 &&
    schedule.start_date &&
    schedule.end_date &&
    schedule.session_count > 0
  );
});

const getAcademicYearEndDate = () => {
  const selectedYear = academicYears.value.find(year => getAcademicYearValue(year) === batchConfig.academic_year);
  if (selectedYear && selectedYear.end_date) {
    return new Date(selectedYear.end_date);
  }
  // Fallback to current date + 6 months if no end date specified
  const fallback = new Date();
  fallback.setMonth(fallback.getMonth() + 6);
  return fallback;
};

const validateAndGenerateSchedules = () => {
  if (!isScheduleConfigurationValid.value) {
    alert('Mohon lengkapi semua pengaturan jadwal terlebih dahulu!');
    return false;
  }

  const academicYearEnd = getAcademicYearEndDate();
  const finalSchedules = [];
  const warnings = [];

  // Validate each schedule configuration
  for (let i = 0; i < batchSchedules.value.length; i++) {
    const schedule = batchSchedules.value[i];
    const course = courses.value.find(c => c.id === schedule.course_id);

    if (!course) {
      warnings.push(`Jadwal #${i + 1}: Matakuliah tidak ditemukan`);
      continue;
    }

    const startDate = new Date(schedule.start_date);
    const endDate = new Date(schedule.end_date);
    const academicEndDate = new Date(Math.min(endDate, academicYearEnd));

    // Calculate available weeks
    const availableWeeks = Math.ceil((academicEndDate - startDate) / (1000 * 60 * 60 * 24 * 7));
    const maxPossibleSessions = availableWeeks;

    if (schedule.session_count > maxPossibleSessions) {
      warnings.push(`Jadwal ${course.course_name}: Hanya dapat membuat ${maxPossibleSessions} sesi dari ${schedule.session_count} yang diminta (${availableWeeks} minggu tersedia)`);
      return false;
    }

    const dayMap = {
      'senin': 1,
      'selasa': 2,
      'rabu': 3,
      'kamis': 4,
      'jumat': 5,
      'sabtu': 6
    };

    const targetDay = dayMap[schedule.day];
    let currentDate = new Date(startDate);
    let sessionsCreated = 0;

    // Find first occurrence of the target day
    while (currentDate.getDay() !== targetDay && currentDate <= academicEndDate) {
      currentDate.setDate(currentDate.getDate() + 1);
    }

    // Generate sessions
    while (currentDate <= academicEndDate && sessionsCreated < schedule.session_count) {
      if (currentDate.getDay() === targetDay) {
        // Skip weekends if needed
        if (!skipHolidays.value || (currentDate.getDay() !== 0 && currentDate.getDay() !== 6)) {
          // Create schedule for each lecturer (team teaching)
          schedule.lecturer_ids.forEach(lecturerId => {
            finalSchedules.push({
              ...batchConfig, // Include program_study_id, school_class_id, academic_year
              course_id: schedule.course_id,
              date: currentDate.toISOString().split('T')[0],
              day: schedule.day,
              start_time: schedule.start_time,
              end_time: schedule.end_time,
              room_id: schedule.room_id,
              lecturer_id: lecturerId,
              mode: schedule.mode,
              offline_percentage: schedule.offline_percentage,
              notes: `${course.course_name} - Sesi ${sessionsCreated + 1}/${schedule.session_count} (${schedule.mode})`
            });
          });
          sessionsCreated++;
        }
      }

      // Move to next week
      currentDate.setDate(currentDate.getDate() + 7);
    }
  }

  if (warnings.length > 0) {
    const warningMessage = warnings.join('\n');
    if (!confirm(`Peringatan:\n${warningMessage}\n\nLanjutkan generate jadwal?`)) {
      return false;
    }
  }

  // Replace batchSchedules with generated ones
  batchSchedules.value = finalSchedules;
  return true;
};

const loadProgramStudiesForBatch = async () => {
  try {
    const token = localStorage.getItem('token');
    if (!token) {
      console.warn('No authentication token found for program studies');
      programStudies.value = [];
      return;
    }

    const response = await fetch('/api/program-studies', {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    });

    if (!response.ok) {
      console.error('Program studies API error:', response.status, response.statusText);
      programStudies.value = [];
      return;
    }

    const data = await response.json();

    // Handle different response formats
    if (data.success && data.data && Array.isArray(data.data)) {
      // Response format: {"success": true, "data": [...]}
      programStudies.value = data.data;
    } else if (data.data && data.data.data && Array.isArray(data.data.data)) {
      // Laravel paginated response format
      programStudies.value = data.data.data;
    } else if (data.data && Array.isArray(data.data)) {
      // Simple array response format
      programStudies.value = data.data;
    } else if (Array.isArray(data)) {
      // Direct array response
      programStudies.value = data;
    } else {
      console.warn('Unexpected program studies API response format:', data);
      programStudies.value = [];
    }

      } catch (error) {
    console.error('Error loading program studies:', error);
    programStudies.value = [];
  }
};

const loadClassesForBatch = async (programStudyId = null) => {
  try {
    const token = localStorage.getItem('token');
    if (!token) {
      console.warn('No authentication token found');
      classes.value = [];
      return;
    }

    let url = '/api/classes';
    if (programStudyId) {
      url += `?program_study_id=${programStudyId}`;
    }

    const response = await fetch(url, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    });

    if (!response.ok) {
      console.error('Classes API error:', response.status, response.statusText);
      classes.value = [];
      return;
    }

    const data = await response.json();

    // Handle different response formats
    if (data.success && data.data && data.data.data && Array.isArray(data.data.data)) {
      // Response format: {"success": true, "data": {"data": [...]}}
      classes.value = data.data.data;
      console.log('Classes loaded successfully:', classes.value.length, 'items');
    } else if (data.success && data.data && Array.isArray(data.data)) {
      // Response format: {"success": true, "data": [...]}
      classes.value = data.data;
      console.log('Classes loaded successfully:', classes.value.length, 'items');
    } else if (data.data && data.data.data && Array.isArray(data.data.data)) {
      // Laravel paginated response format
      classes.value = data.data.data;
      console.log('Classes loaded successfully:', classes.value.length, 'items');
    } else if (data.data && Array.isArray(data.data)) {
      // Simple array response format
      classes.value = data.data;
      console.log('Classes loaded successfully:', classes.value.length, 'items');
    } else if (Array.isArray(data)) {
      // Direct array response
      classes.value = data;
      console.log('Classes loaded successfully:', classes.value.length, 'items');
    } else {
      console.warn('Unexpected classes API response format:', data);
      classes.value = [];
    }

      } catch (error) {
    console.error('Error loading classes:', error);
    classes.value = [];
  }
};

const loadCoursesForBatch = async () => {
  try {
    const token = localStorage.getItem('token');
    if (!token) {
      console.warn('No authentication token found for courses');
      courses.value = [];
      return;
    }

    const response = await fetch('/api/courses', {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    });

    if (!response.ok) {
      console.error('Courses API error:', response.status, response.statusText);
      courses.value = [];
      return;
    }

    const data = await response.json();

    // Handle different response formats
    if (data.success && data.data && Array.isArray(data.data)) {
      // Response format: {"success": true, "data": [...]}
      courses.value = data.data;
    } else if (data.data && data.data.data && Array.isArray(data.data.data)) {
      // Laravel paginated response format
      courses.value = data.data.data;
    } else if (data.data && Array.isArray(data.data)) {
      // Simple array response format
      courses.value = data.data;
    } else if (Array.isArray(data)) {
      // Direct array response
      courses.value = data;
    } else {
      console.warn('Unexpected courses API response format:', data);
      courses.value = [];
    }

      } catch (error) {
    console.error('Error loading courses:', error);
    courses.value = [];
  }
};

const loadRoomsForBatch = async () => {
  try {
    const token = localStorage.getItem('token');
    if (!token) {
      console.warn('No authentication token found');
      rooms.value = [];
      return;
    }

    const response = await fetch('/api/rooms', {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    });

    if (!response.ok) {
      console.error('Rooms API error:', response.status, response.statusText);
      rooms.value = [];
      return;
    }

    const data = await response.json();
    if (data.data) {
      rooms.value = data.data;
    } else if (Array.isArray(data)) {
      rooms.value = data;
    } else {
      console.warn('Unexpected rooms API response format:', data);
      rooms.value = [];
    }
  } catch (error) {
    console.error('Error loading rooms:', error);
    rooms.value = [];
  }
};

const loadLecturersForBatch = async () => {
  try {
    const token = localStorage.getItem('token');
    if (!token) {
      console.warn('No authentication token found for lecturers');
      lecturers.value = [];
      return;
    }

    console.log('Loading lecturers...');

    // Try multiple endpoints for lecturers
    const endpoints = [
      '/api/lecturers/active',
      '/api/lecturers'
    ];

    let lecturersData = [];
    let success = false;

    for (const endpoint of endpoints) {
      try {
        console.log(`Trying endpoint: ${endpoint}`);
        const response = await fetch(endpoint, {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          }
        });

        console.log(`Endpoint ${endpoint} response status:`, response.status);

        if (response.ok) {
          const data = await response.json();
          console.log(`Endpoint ${endpoint} response data:`, data);

          // Handle different response formats
          if (data.data && Array.isArray(data.data)) {
            lecturersData = data.data;
          } else if (data.data && data.data.data && Array.isArray(data.data.data)) {
            // Handle Laravel pagination with data.data.data structure
            lecturersData = data.data.data;
          } else if (Array.isArray(data)) {
            lecturersData = data;
          } else {
            console.warn(`Unexpected response format from ${endpoint}:`, data);
            continue;
          }

          if (lecturersData.length > 0) {
            success = true;
            break;
          }
        } else {
          console.warn(`Endpoint ${endpoint} failed:`, response.status, response.statusText);
        }
      } catch (error) {
        console.warn(`Error with endpoint ${endpoint}:`, error);
        continue;
      }
    }

    if (!success) {
      console.error('All lecturer endpoints failed');
      lecturers.value = [];
      return;
    }

    lecturers.value = lecturersData;
    console.log('Lecturers loaded successfully:', lecturers.value.length, 'items');

    // Log first few lecturers for debugging
    if (lecturers.value.length > 0) {
      console.log('First lecturer:', lecturers.value[0]);
    }
  } catch (error) {
    console.error('Error loading lecturers:', error);
    lecturers.value = [];
  }
};

const loadAcademicYearsForBatch = async () => {
  try {
    const token = localStorage.getItem('token');
    if (!token) {
      console.warn('No authentication token found for academic years');
      academicYears.value = [];
      return;
    }

    const response = await fetch('/api/academic-years', {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    });

    if (!response.ok) {
      console.error('Academic years API error:', response.status, response.statusText);
      academicYears.value = [];
      return;
    }

    const data = await response.json();

    // Handle different response formats
    if (data.success && data.data && Array.isArray(data.data)) {
      // Response format: {"success": true, "data": [...]}
      academicYears.value = data.data;
    } else if (data.data && data.data.data && Array.isArray(data.data.data)) {
      // Laravel paginated response format
      academicYears.value = data.data.data;
    } else if (data.data && Array.isArray(data.data)) {
      // Simple array response format
      academicYears.value = data.data;
    } else if (Array.isArray(data)) {
      // Direct array response
      academicYears.value = data;
    } else {
      console.warn('Unexpected academic years API response format:', data);
      academicYears.value = [];
    }

    // Find active academic year and set as default
    const activeYear = academicYears.value.find(year => year.is_active);
    const currentYear = new Date().getFullYear();

    console.log('🔍 Looking for active academic year...');

    if (activeYear) {
      // Priority 1: Use active academic year
      const yearValue = activeYear.academic_calendar_year || activeYear.year || activeYear.name;
      batchConfig.academic_year = yearValue;
      console.log('✅ Using active academic year:', yearValue, '(marked as active)');
    } else {
      console.log('❌ No active academic year found, looking for fallback...');
      // Priority 2: Find current or most recent academic year
      const recentYear = academicYears.value.find(year =>
        (year.year && year.year.toString().includes(currentYear.toString())) ||
        (year.academic_calendar_year && year.academic_calendar_year.includes(currentYear.toString())) ||
        (year.name && year.name.includes(currentYear.toString()))
      );

      if (recentYear) {
        const yearValue = recentYear.academic_calendar_year || recentYear.year || recentYear.name;
        batchConfig.academic_year = yearValue;
        console.log('✅ Using recent academic year:', yearValue, '(contains current year)');
      } else if (academicYears.value.length > 0) {
        // Priority 3: Use first available year
        const yearValue = academicYears.value[0].academic_calendar_year || academicYears.value[0].year || academicYears.value[0].name;
        batchConfig.academic_year = yearValue;
        console.log('✅ Using first academic year:', yearValue, '(fallback)');
      } else {
        console.log('❌ No academic years available');
      }
    }

    console.log('📋 Final academic_year set to:', batchConfig.academic_year);
    console.log('🎯 Available academic years:', academicYears.value.map(y => ({
      year: y.year,
      is_active: y.is_active,
      label: `${y.year} ${y.is_active ? '✅ AKTIF' : ''}`
    })));
    console.log('Academic years loaded successfully:', academicYears.value.length, 'items');
  } catch (error) {
    console.error('Error loading academic years:', error);
    academicYears.value = [];
  }
};

watch(() => props.show, async (newVal) => {
  if (newVal) {
    // Reset batch config and state
    batchConfig.program_study_id = '';
    batchConfig.school_class_id = '';
    batchConfig.academic_year = '';
    classes.value = [];
    courses.value = [];

    // Load initial data
    await loadProgramStudiesForBatch();
    await loadAcademicYearsForBatch();
    await loadCoursesForBatch();
    await loadRoomsForBatch();
    await loadLecturersForBatch();
  }
}, { immediate: true });
</script>