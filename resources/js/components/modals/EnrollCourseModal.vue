<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <!-- Background overlay -->
      <div class="fixed inset-0 bg-gray-900 bg-opacity-75 backdrop-blur-sm transition-opacity" aria-hidden="true" @click="closeModal"></div>

      <!-- Modal panel -->
      <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-6xl sm:w-full">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-indigo-600 via-blue-600 to-cyan-600 px-6 py-4">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <div class="p-2 bg-white bg-opacity-20 rounded-xl">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
              </div>
              <div>
                <h3 class="text-lg font-bold text-white">Enroll Matakuliah</h3>
                <p class="text-blue-100 text-sm">{{ classSchedule?.kelas?.class_name || 'Kelas' }} - {{ classSchedule?.academic_year?.name || '' }}</p>
              </div>
            </div>
            <button @click="closeModal" class="text-white hover:text-red-300 p-2 hover:bg-white hover:bg-opacity-10 rounded-lg transition-all">
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Content -->
        <div class="px-6 py-4 max-h-[75vh] overflow-y-auto">
          <!-- Global Settings -->
          <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-4 mb-4 border border-blue-100">
            <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
              <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
              Setting Global (Default untuk semua matakuliah)
            </h4>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
              <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal Mulai</label>
                <input
                  v-model="globalSettings.startDate"
                  type="date"
                  class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal Selesai</label>
                <input
                  v-model="globalSettings.endDate"
                  type="date"
                  class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Sesi/Pertemuan</label>
                <select
                  v-model="globalSettings.sessionsPerMeeting"
                  @change="updateGlobalMeetings"
                  class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                  <option :value="1">1 Sesi (16 pertemuan)</option>
                  <option :value="2">2 Sesi (8 pertemuan)</option>
                </select>
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Jumlah Pertemuan</label>
                <input
                  v-model.number="globalSettings.totalMeetings"
                  type="number"
                  min="1"
                  max="30"
                  class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                <p class="text-xs text-gray-500 mt-1">*Termasuk UTS & UAS</p>
              </div>
              <div class="flex items-end">
                <div class="w-full px-3 py-2 bg-blue-50 rounded-lg border border-blue-200">
                  <p class="text-xs text-blue-600 font-medium">Simulasi:</p>
                  <p class="text-xs text-blue-800">{{ globalSimulationDates }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Search and Select All -->
          <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
            <div class="relative flex-1">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Cari matakuliah..."
                class="w-full pl-10 pr-10 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              >
              <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
              <button
                v-if="searchQuery"
                @click="searchQuery = ''"
                class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>
            <div class="flex items-center space-x-4">
              <label class="flex items-center space-x-2 cursor-pointer">
                <input
                  type="checkbox"
                  :checked="isAllSelected"
                  @change="toggleSelectAll"
                  class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                >
                <span class="text-sm text-gray-700">Pilih Semua</span>
              </label>
              <span class="text-sm text-gray-500">
                {{ selectedCount }} dari {{ filteredCourses.length }} dipilih
              </span>
            </div>
          </div>

          <!-- Conflict Warning -->
          <div v-if="conflicts.length > 0" class="bg-red-50 border border-red-200 rounded-xl p-3 mb-4">
            <div class="flex items-start">
              <svg class="w-5 h-5 text-red-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
              </svg>
              <div class="flex-1">
                <p class="text-sm font-medium text-red-800">{{ conflicts.length }} Bentrok Terdeteksi</p>
                <ul class="mt-1 text-xs text-red-700 space-y-1">
                  <li v-for="(conflict, idx) in conflicts" :key="idx">
                    {{ conflict.message }}
                  </li>
                </ul>
              </div>
            </div>
          </div>

          <!-- Course List -->
          <div class="space-y-3">
            <div v-if="loading" class="flex items-center justify-center py-8">
              <svg class="animate-spin h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span class="ml-2 text-gray-600">Memuat matakuliah...</span>
            </div>

            <div v-else-if="filteredCourses.length === 0" class="text-center py-8">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <p class="mt-2 text-gray-500">Tidak ada matakuliah ditemukan</p>
            </div>

            <!-- Course Items -->
            <div
              v-for="course in filteredCourses"
              :key="course.id"
              :class="[
                'border rounded-xl p-4 transition-all duration-200',
                courseSettings[course.id]?.selected
                  ? 'border-blue-300 bg-blue-50 shadow-sm'
                  : 'border-gray-200 bg-white hover:border-gray-300',
                isConflicted(course.id) ? 'border-red-300 bg-red-50' : ''
              ]"
            >
              <!-- Course Header -->
              <div class="flex items-start space-x-3">
                <input
                  type="checkbox"
                  :checked="courseSettings[course.id]?.selected"
                  @change="toggleCourse(course.id)"
                  class="mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                >
                <div class="flex-1 min-w-0">
                  <div class="flex items-center justify-between">
                    <div>
                      <h5 class="text-sm font-semibold text-gray-900">{{ course.course_name }}</h5>
                      <p class="text-xs text-gray-500">{{ course.course_code }} | {{ course.credits }} SKS | Semester {{ course.semester }}</p>
                    </div>
                    <div class="flex items-center space-x-2">
                      <span
                        v-if="isConflicted(course.id)"
                        class="px-2 py-1 text-xs font-medium text-red-700 bg-red-100 rounded-full"
                      >
                        Bentrok
                      </span>
                      <label 
                        v-if="courseSettings[course.id]?.selected"
                        class="flex items-center space-x-1 text-xs cursor-pointer"
                      >
                        <input
                          type="checkbox"
                          :checked="courseSettings[course.id]?.useCustom"
                          @change="toggleCustomSettings(course.id)"
                          class="w-3 h-3 text-orange-500 border-gray-300 rounded focus:ring-orange-500"
                        >
                        <span class="text-orange-600 font-medium">Custom</span>
                      </label>
                    </div>
                  </div>

                  <!-- Inline Settings (show when selected) -->
                  <div v-if="courseSettings[course.id]?.selected" class="mt-3">
                    <!-- Custom Date Settings (if enabled) -->
                    <div v-if="courseSettings[course.id]?.useCustom" class="mb-3 p-3 bg-orange-50 rounded-lg border border-orange-200">
                      <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-medium text-orange-700">Custom Settings</span>
                      </div>
                      <div class="grid grid-cols-4 gap-2">
                        <div>
                          <label class="block text-xs text-gray-500 mb-1">Tanggal Mulai</label>
                          <input
                            v-model="courseSettings[course.id].customStartDate"
                            type="date"
                            @change="checkConflicts"
                            class="w-full px-2 py-1.5 text-sm border border-orange-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                          >
                        </div>
                        <div>
                          <label class="block text-xs text-gray-500 mb-1">Tanggal Selesai</label>
                          <input
                            v-model="courseSettings[course.id].customEndDate"
                            type="date"
                            @change="checkConflicts"
                            class="w-full px-2 py-1.5 text-sm border border-orange-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                          >
                        </div>
                        <div>
                          <label class="block text-xs text-gray-500 mb-1">Sesi</label>
                          <select
                            v-model="courseSettings[course.id].customSessions"
                            @change="updateCourseMeetings(course.id)"
                            class="w-full px-2 py-1.5 text-sm border border-orange-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                          >
                            <option :value="1">1 Sesi</option>
                            <option :value="2">2 Sesi</option>
                          </select>
                        </div>
                        <div>
                          <label class="block text-xs text-gray-500 mb-1">Pertemuan</label>
                          <input
                            v-model.number="courseSettings[course.id].customMeetings"
                            type="number"
                            min="1"
                            max="30"
                            class="w-full px-2 py-1.5 text-sm border border-orange-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                          >
                        </div>
                      </div>
                    </div>

                    <!-- Day/Time/Room/Lecturer Settings -->
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-2">
                      <!-- Day -->
                      <div>
                        <label class="block text-xs text-gray-500 mb-1">Hari</label>
                        <select
                          v-model="courseSettings[course.id].day"
                          @change="checkConflicts"
                          class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        >
                          <option value="Senin">Senin</option>
                          <option value="Selasa">Selasa</option>
                          <option value="Rabu">Rabu</option>
                          <option value="Kamis">Kamis</option>
                          <option value="Jumat">Jumat</option>
                          <option value="Sabtu">Sabtu</option>
                        </select>
                      </div>

                      <!-- Start Time -->
                      <div>
                        <label class="block text-xs text-gray-500 mb-1">Mulai</label>
                        <input
                          v-model="courseSettings[course.id].startTime"
                          @change="checkConflicts"
                          type="time"
                          class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        >
                      </div>

                      <!-- End Time -->
                      <div>
                        <label class="block text-xs text-gray-500 mb-1">Selesai</label>
                        <input
                          v-model="courseSettings[course.id].endTime"
                          @change="checkConflicts"
                          type="time"
                          class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        >
                      </div>

                      <!-- Room (Multi-Select with Chips) -->
                      <div class="relative col-span-2">
                        <label class="block text-xs text-gray-500 mb-1">Ruangan</label>
                        <!-- Selected Rooms Chips -->
                        <div class="flex flex-wrap gap-1 mb-1" v-if="courseSettings[course.id].roomIds?.length > 0">
                          <span 
                            v-for="roomId in courseSettings[course.id].roomIds" 
                            :key="roomId"
                            class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-green-100 text-green-800 rounded-full"
                          >
                            {{ getRoomName(roomId) }}
                            <button @click.stop="removeRoom(course.id, roomId)" class="ml-1 text-green-600 hover:text-green-900">
                              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                              </svg>
                            </button>
                          </span>
                        </div>
                        <!-- Search Input -->
                        <div class="relative">
                          <input
                            v-model="courseSettings[course.id].roomSearch"
                            @focus="openRoomDropdown(course.id)"
                            @input="filterRooms(course.id)"
                            type="text"
                            placeholder="Tambah ruangan..."
                            class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                          >
                          <div 
                            v-if="activeRoomDropdown === course.id"
                            class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-40 overflow-y-auto"
                          >
                            <div
                              v-for="room in getAvailableRooms(course.id)"
                              :key="room.id"
                              @click="addRoom(course.id, room)"
                              class="px-3 py-2 text-sm hover:bg-blue-50 cursor-pointer"
                            >
                              {{ room.name }} <span class="text-xs text-gray-500">({{ room.capacity }} orang)</span>
                            </div>
                            <div v-if="getAvailableRooms(course.id).length === 0" class="px-3 py-2 text-sm text-gray-500">
                              Tidak ditemukan
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Lecturer (Multi-Select with Chips) -->
                      <div class="relative col-span-2">
                        <label class="block text-xs text-gray-500 mb-1">Dosen (Team Teaching)</label>
                        <!-- Selected Lecturers Chips -->
                        <div class="flex flex-wrap gap-1 mb-1" v-if="courseSettings[course.id].lecturerIds?.length > 0">
                          <span 
                            v-for="lecId in courseSettings[course.id].lecturerIds" 
                            :key="lecId"
                            class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-800 rounded-full"
                          >
                            {{ getLecturerName(lecId) }}
                            <button @click.stop="removeLecturer(course.id, lecId)" class="ml-1 text-blue-600 hover:text-blue-900">
                              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                              </svg>
                            </button>
                          </span>
                        </div>
                        <!-- Search Input -->
                        <div class="relative">
                          <input
                            v-model="courseSettings[course.id].lecturerSearch"
                            @focus="openLecturerDropdown(course.id)"
                            @input="filterLecturers(course.id)"
                            type="text"
                            placeholder="Tambah dosen..."
                            class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                          >
                          <div 
                            v-if="activeLecturerDropdown === course.id"
                            class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-40 overflow-y-auto"
                          >
                            <div
                              v-for="lecturer in getAvailableLecturers(course.id)"
                              :key="lecturer.id"
                              @click="addLecturer(course.id, lecturer)"
                              class="px-3 py-2 text-sm hover:bg-blue-50 cursor-pointer"
                            >
                              {{ lecturer.full_name || lecturer.name }}
                            </div>
                            <div v-if="getAvailableLecturers(course.id).length === 0" class="px-3 py-2 text-sm text-gray-500">
                              Tidak ditemukan
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Schedule Simulation -->
                    <div class="mt-2 p-2 bg-gray-50 rounded-lg">
                      <p class="text-xs text-gray-600">
                        <span class="font-medium">📅 Simulasi Jadwal:</span>
                        {{ getCourseSimulation(course.id) }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Already Enrolled Courses -->
          <div v-if="enrolledCourses.length > 0" class="mt-6">
            <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
              <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              Matakuliah Sudah Terdaftar ({{ enrolledCourses.length }})
            </h4>
            <div class="space-y-2">
              <div
                v-for="enrolled in enrolledCourses"
                :key="enrolled.id"
                class="flex items-center justify-between bg-green-50 border border-green-200 rounded-lg px-4 py-2"
              >
                <div>
                  <p class="text-sm font-medium text-gray-900">{{ enrolled.course?.course_name || 'N/A' }}</p>
                  <p class="text-xs text-gray-500">
                    {{ enrolled.day_of_week }} | {{ enrolled.start_time }} - {{ enrolled.end_time }}
                    <span v-if="enrolled.room">| {{ enrolled.room.name }}</span>
                    <span v-if="enrolled.lecturer">| {{ enrolled.lecturer.full_name || enrolled.lecturer.name }}</span>
                  </p>
                </div>
                <button
                  @click="removeEnrolled(enrolled.id)"
                  class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-100 rounded-lg transition-colors"
                  title="Hapus"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
          <div class="flex items-center justify-between">
            <p class="text-sm text-gray-500">
              <span v-if="selectedCount > 0">{{ selectedCount }} matakuliah akan di-enroll</span>
              <span v-else>Pilih matakuliah untuk di-enroll</span>
            </p>
            <div class="flex space-x-3">
              <button
                @click="closeModal"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
              >
                Tutup
              </button>
              <button
                @click="enrollCourses"
                :disabled="selectedCount === 0 || enrolling"
                class="px-6 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg hover:from-blue-700 hover:to-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all flex items-center"
              >
                <svg v-if="enrolling" class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ enrolling ? 'Menyimpan...' : `Enroll ${selectedCount} Matakuliah` }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, watch, onMounted, onUnmounted } from 'vue'
import courseService from '@/services/courseService'
import scheduleService from '@/services/scheduleService'
import roomService from '@/services/roomService'
import lecturerService from '@/services/lecturerService'
import { useToastStore } from '@/stores/toast'

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

const emit = defineEmits(['close', 'enrolled', 'refresh'])

const toastStore = useToastStore()

// State
const loading = ref(false)
const enrolling = ref(false)
const searchQuery = ref('')
const courses = ref([])
const rooms = ref([])
const lecturers = ref([])
const enrolledCourses = ref([])
const courseSettings = reactive({})
const conflicts = ref([])

// Searchable dropdown states
const activeRoomDropdown = ref(null)
const activeLecturerDropdown = ref(null)
const filteredRooms = ref([])
const filteredLecturers = ref([])

// Global Settings
const globalSettings = reactive({
  startDate: '',
  endDate: '',
  totalMeetings: 16,
  sessionsPerMeeting: 1
})

// Day mapping for date calculation
const dayMap = {
  'Senin': 1,
  'Selasa': 2,
  'Rabu': 3,
  'Kamis': 4,
  'Jumat': 5,
  'Sabtu': 6,
  'Minggu': 0
}

// Computed
const filteredCourses = computed(() => {
  if (!searchQuery.value) return courses.value

  const query = searchQuery.value.toLowerCase()
  return courses.value.filter(course =>
    course.course_name?.toLowerCase().includes(query) ||
    course.course_code?.toLowerCase().includes(query)
  )
})

const selectedCount = computed(() => {
  return Object.values(courseSettings).filter(s => s.selected).length
})

const isAllSelected = computed(() => {
  if (filteredCourses.value.length === 0) return false
  return filteredCourses.value.every(course => courseSettings[course.id]?.selected)
})

const globalSimulationDates = computed(() => {
  if (!globalSettings.startDate || !globalSettings.endDate) return 'Set tanggal mulai & selesai'
  return `${formatDate(globalSettings.startDate)} - ${formatDate(globalSettings.endDate)} (${globalSettings.totalMeetings} pertemuan)`
})

// Methods
const closeModal = () => {
  emit('close')
}

const formatDate = (dateStr) => {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

const updateGlobalMeetings = () => {
  // Auto-calculate meetings based on sessions
  if (globalSettings.sessionsPerMeeting === 1) {
    globalSettings.totalMeetings = 16
  } else if (globalSettings.sessionsPerMeeting === 2) {
    globalSettings.totalMeetings = 8
  }
}

const updateCourseMeetings = (courseId) => {
  const settings = courseSettings[courseId]
  if (!settings) return
  
  if (settings.customSessions === 1) {
    settings.customMeetings = 16
  } else if (settings.customSessions === 2) {
    settings.customMeetings = 8
  }
}

const fetchData = async () => {
  if (!props.classSchedule?.id) return

  loading.value = true
  try {
    const programStudyId = props.classSchedule.program_study_id || 
                          props.classSchedule.kelas?.program_study_id

    console.log('Fetching data for classSchedule:', props.classSchedule.id, 'programStudy:', programStudyId)

    // Parallel fetch
    const [coursesRes, roomsRes, lecturersRes, scheduleRes] = await Promise.all([
      programStudyId ? courseService.getByProgramStudy(programStudyId, { is_active: true }) : courseService.getAll({ is_active: true, per_page: 100 }),
      roomService.getAll({ per_page: 200 }),  // Removed is_active filter
      lecturerService.getAll({ per_page: 200 }),  // Removed is_active filter
      scheduleService.getById(props.classSchedule.id)
    ])

    console.log('Raw responses:', { coursesRes, roomsRes, lecturersRes, scheduleRes })
    console.log('roomsRes type:', typeof roomsRes, 'keys:', roomsRes ? Object.keys(roomsRes) : 'null')
    console.log('roomsRes.data type:', typeof roomsRes?.data, 'isArray:', Array.isArray(roomsRes?.data))
    if (roomsRes?.data && typeof roomsRes.data === 'object') {
      console.log('roomsRes.data keys:', Object.keys(roomsRes.data))
    }

    // Parse courses
    courses.value = parseResponse(coursesRes)
    console.log('Parsed courses:', courses.value.length)

    // Parse rooms
    rooms.value = parseResponse(roomsRes)
    console.log('Parsed rooms:', rooms.value.length, 'First room:', rooms.value[0])

    // Parse lecturers
    lecturers.value = parseResponse(lecturersRes)
    console.log('Parsed lecturers:', lecturers.value.length)

    // Get enrolled courses from schedule details
    const scheduleData = scheduleRes?.data || scheduleRes
    enrolledCourses.value = scheduleData?.details || []
    console.log('Enrolled courses:', enrolledCourses.value.length)

    // Filter out already enrolled courses
    const enrolledIds = enrolledCourses.value.map(e => e.course_id)
    courses.value = courses.value.filter(c => !enrolledIds.includes(c.id))

    // Initialize settings for each course
    courses.value.forEach(course => {
      if (!courseSettings[course.id]) {
        initCourseSettings(course.id)
      }
    })

    // Set default dates from academic year if available
    if (props.classSchedule.academic_year) {
      const ay = props.classSchedule.academic_year
      if (ay.start_date) globalSettings.startDate = ay.start_date
      if (ay.end_date) globalSettings.endDate = ay.end_date
    }

    // Initialize filtered lists
    filteredRooms.value = rooms.value.slice(0, 20)
    filteredLecturers.value = lecturers.value.slice(0, 20)

  } catch (error) {
    console.error('Failed to fetch data:', error)
    toastStore.error('Gagal memuat data: ' + (error.message || 'Unknown error'))
  } finally {
    loading.value = false
  }
}

const parseResponse = (response) => {
  console.log('parseResponse input:', response)
  
  // If response is already an array
  if (Array.isArray(response)) {
    console.log('parseResponse: response is array, length:', response.length)
    return response
  }
  
  // ResponseService format: { success: true, data: [...], meta: {...} }
  // After axios interceptor, this becomes just the response.data from API
  // So we receive: { success: true, data: [...], meta: {...} }
  if (response?.success === true && Array.isArray(response.data)) {
    console.log('parseResponse: ResponseService format, data length:', response.data.length)
    return response.data
  }
  
  // Legacy format 1: { data: [...] }
  if (response?.data && Array.isArray(response.data)) {
    console.log('parseResponse: legacy format 1, data length:', response.data.length)
    return response.data
  }
  
  // Legacy format 2: { data: { data: [...] } } (nested)
  if (response?.data?.data && Array.isArray(response.data.data)) {
    console.log('parseResponse: legacy format 2, data.data length:', response.data.data.length)
    return response.data.data
  }
  
  // Try to find any array property
  if (response && typeof response === 'object') {
    for (const key of Object.keys(response)) {
      if (Array.isArray(response[key])) {
        console.log(`parseResponse: found array in key "${key}", length:`, response[key].length)
        return response[key]
      }
    }
  }
  
  console.log('parseResponse: no valid format found, returning empty array')
  return []
}

const initCourseSettings = (courseId) => {
  courseSettings[courseId] = {
    selected: false,
    day: 'Senin',
    startTime: '08:00',
    endTime: '10:00',
    roomIds: [],      // Changed to array for multi-room
    lecturerIds: [],  // Changed to array for multi-lecturer
    roomSearch: '',
    lecturerSearch: '',
    useCustom: false,
    customStartDate: globalSettings.startDate || '',
    customEndDate: globalSettings.endDate || '',
    customSessions: 1,
    customMeetings: 16
  }
}

const toggleCourse = (courseId) => {
  if (!courseSettings[courseId]) {
    initCourseSettings(courseId)
  }
  courseSettings[courseId].selected = !courseSettings[courseId].selected
  checkConflicts()
}

const toggleCustomSettings = (courseId) => {
  if (courseSettings[courseId]) {
    courseSettings[courseId].useCustom = !courseSettings[courseId].useCustom
    // Initialize custom dates from global if not set
    if (courseSettings[courseId].useCustom) {
      if (!courseSettings[courseId].customStartDate) {
        courseSettings[courseId].customStartDate = globalSettings.startDate
      }
      if (!courseSettings[courseId].customEndDate) {
        courseSettings[courseId].customEndDate = globalSettings.endDate
      }
    }
    checkConflicts()
  }
}

const toggleSelectAll = () => {
  const newState = !isAllSelected.value
  filteredCourses.value.forEach(course => {
    if (!courseSettings[course.id]) {
      initCourseSettings(course.id)
    }
    courseSettings[course.id].selected = newState
  })
  checkConflicts()
}

// Room dropdown methods
const openRoomDropdown = (courseId) => {
  activeRoomDropdown.value = courseId
  activeLecturerDropdown.value = null
  filteredRooms.value = rooms.value.slice(0, 20)
}

const filterRooms = (courseId) => {
  const search = courseSettings[courseId]?.roomSearch?.toLowerCase() || ''
  if (!search) {
    filteredRooms.value = rooms.value.slice(0, 20)
  } else {
    filteredRooms.value = rooms.value.filter(r => 
      (r.name || '').toLowerCase().includes(search) ||
      (r.room_code || '').toLowerCase().includes(search)
    ).slice(0, 20)
  }
}

// Get available rooms (exclude already selected)
const getAvailableRooms = (courseId) => {
  const selectedIds = courseSettings[courseId]?.roomIds || []
  const search = courseSettings[courseId]?.roomSearch?.toLowerCase() || ''
  
  let available = rooms.value.filter(r => !selectedIds.includes(r.id))
  
  if (search) {
    available = available.filter(r => 
      (r.name || '').toLowerCase().includes(search) ||
      (r.room_code || '').toLowerCase().includes(search)
    )
  }
  
  return available.slice(0, 20)
}

// Add room to course (multi-select)
const addRoom = (courseId, room) => {
  if (!courseSettings[courseId].roomIds) {
    courseSettings[courseId].roomIds = []
  }
  if (!courseSettings[courseId].roomIds.includes(room.id)) {
    courseSettings[courseId].roomIds.push(room.id)
  }
  courseSettings[courseId].roomSearch = ''
  activeRoomDropdown.value = null
  checkConflicts()
}

// Remove room from course
const removeRoom = (courseId, roomId) => {
  if (courseSettings[courseId]?.roomIds) {
    courseSettings[courseId].roomIds = courseSettings[courseId].roomIds.filter(id => id !== roomId)
  }
  checkConflicts()
}

const getRoomName = (roomId) => {
  if (!roomId) return ''
  const room = rooms.value.find(r => r.id === roomId)
  return room ? room.name : ''
}

// Lecturer dropdown methods
const openLecturerDropdown = (courseId) => {
  activeLecturerDropdown.value = courseId
  activeRoomDropdown.value = null
  filteredLecturers.value = lecturers.value.slice(0, 20)
}

const filterLecturers = (courseId) => {
  const search = courseSettings[courseId]?.lecturerSearch?.toLowerCase() || ''
  if (!search) {
    filteredLecturers.value = lecturers.value.slice(0, 20)
  } else {
    filteredLecturers.value = lecturers.value.filter(l => 
      (l.full_name || l.name || '').toLowerCase().includes(search) ||
      (l.nidn || '').toLowerCase().includes(search)
    ).slice(0, 20)
  }
}

// Get available lecturers (exclude already selected)
const getAvailableLecturers = (courseId) => {
  const selectedIds = courseSettings[courseId]?.lecturerIds || []
  const search = courseSettings[courseId]?.lecturerSearch?.toLowerCase() || ''
  
  let available = lecturers.value.filter(l => !selectedIds.includes(l.id))
  
  if (search) {
    available = available.filter(l => 
      (l.full_name || l.name || '').toLowerCase().includes(search) ||
      (l.nidn || '').toLowerCase().includes(search)
    )
  }
  
  return available.slice(0, 20)
}

// Add lecturer to course (multi-select)
const addLecturer = (courseId, lecturer) => {
  if (!courseSettings[courseId].lecturerIds) {
    courseSettings[courseId].lecturerIds = []
  }
  if (!courseSettings[courseId].lecturerIds.includes(lecturer.id)) {
    courseSettings[courseId].lecturerIds.push(lecturer.id)
  }
  courseSettings[courseId].lecturerSearch = ''
  activeLecturerDropdown.value = null
}

// Remove lecturer from course
const removeLecturer = (courseId, lecturerId) => {
  if (courseSettings[courseId]?.lecturerIds) {
    courseSettings[courseId].lecturerIds = courseSettings[courseId].lecturerIds.filter(id => id !== lecturerId)
  }
}

const getLecturerName = (lecturerId) => {
  if (!lecturerId) return ''
  const lecturer = lecturers.value.find(l => l.id === lecturerId)
  return lecturer ? (lecturer.full_name || lecturer.name) : ''
}

// Get all lecturer names for display (for enrolled courses)
const getLecturerNames = (lecturerIds) => {
  if (!lecturerIds || lecturerIds.length === 0) return ''
  return lecturerIds.map(id => getLecturerName(id)).filter(n => n).join(', ')
}

// Close dropdowns when clicking outside
const handleClickOutside = (event) => {
  if (!event.target.closest('.relative')) {
    activeRoomDropdown.value = null
    activeLecturerDropdown.value = null
  }
}

// Time helpers
const timeToMinutes = (time) => {
  if (!time) return 0
  const [hours, minutes] = time.split(':').map(Number)
  return hours * 60 + minutes
}

const timeOverlaps = (start1, end1, start2, end2) => {
  const s1 = timeToMinutes(start1)
  const e1 = timeToMinutes(end1)
  const s2 = timeToMinutes(start2)
  const e2 = timeToMinutes(end2)
  return s1 < e2 && s2 < e1
}

// Date range overlap check
const dateRangesOverlap = (start1, end1, start2, end2) => {
  if (!start1 || !end1 || !start2 || !end2) return true // If dates not set, assume overlap
  const s1 = new Date(start1).getTime()
  const e1 = new Date(end1).getTime()
  const s2 = new Date(start2).getTime()
  const e2 = new Date(end2).getTime()
  return s1 <= e2 && s2 <= e1
}

// Get effective dates for a course
const getEffectiveDates = (courseId) => {
  const settings = courseSettings[courseId]
  if (!settings) return { startDate: globalSettings.startDate, endDate: globalSettings.endDate }
  
  if (settings.useCustom) {
    return {
      startDate: settings.customStartDate || globalSettings.startDate,
      endDate: settings.customEndDate || globalSettings.endDate
    }
  }
  return { startDate: globalSettings.startDate, endDate: globalSettings.endDate }
}

const checkConflicts = () => {
  const newConflicts = []
  const selectedList = Object.entries(courseSettings)
    .filter(([id, data]) => data.selected)
    .map(([id, data]) => {
      const effectiveDates = getEffectiveDates(parseInt(id))
      return {
        id: parseInt(id),
        ...data,
        effectiveStartDate: effectiveDates.startDate,
        effectiveEndDate: effectiveDates.endDate,
        courseName: courses.value.find(c => c.id === parseInt(id))?.course_name || ''
      }
    })

  for (let i = 0; i < selectedList.length; i++) {
    for (let j = i + 1; j < selectedList.length; j++) {
      const a = selectedList[i]
      const b = selectedList[j]

      // Check if date ranges overlap first
      const datesOverlap = dateRangesOverlap(
        a.effectiveStartDate, a.effectiveEndDate,
        b.effectiveStartDate, b.effectiveEndDate
      )

      // Only check time conflict if dates overlap
      if (datesOverlap && a.day === b.day) {
        // Check time overlap - this is conflict for same class
        if (timeOverlaps(a.startTime, a.endTime, b.startTime, b.endTime)) {
          newConflicts.push({
            courseIds: [a.id, b.id],
            type: 'time',
            message: `${a.courseName} & ${b.courseName} bentrok waktu (${a.day} ${a.startTime}-${a.endTime})`
          })
        }
      }

      // Check same room conflict (if any rooms overlap)
      const commonRooms = (a.roomIds || []).filter(id => (b.roomIds || []).includes(id))
      if (commonRooms.length > 0 && a.day === b.day && 
          timeOverlaps(a.startTime, a.endTime, b.startTime, b.endTime) && datesOverlap) {
        const roomNames = commonRooms.map(id => getRoomName(id)).join(', ')
        newConflicts.push({
          courseIds: [a.id, b.id],
          type: 'room',
          message: `${a.courseName} & ${b.courseName} menggunakan ruangan sama (${roomNames}) di waktu yang sama`
        })
      }

      // Check same lecturer conflict (if any lecturers overlap)
      const commonLecturers = (a.lecturerIds || []).filter(id => (b.lecturerIds || []).includes(id))
      if (commonLecturers.length > 0 && a.day === b.day && 
          timeOverlaps(a.startTime, a.endTime, b.startTime, b.endTime) && datesOverlap) {
        const lecturerNames = commonLecturers.map(id => getLecturerName(id)).join(', ')
        newConflicts.push({
          courseIds: [a.id, b.id],
          type: 'lecturer',
          message: `${a.courseName} & ${b.courseName} menggunakan dosen sama (${lecturerNames}) di waktu yang sama`
        })
      }
    }
  }

  conflicts.value = newConflicts
}

const isConflicted = (courseId) => {
  return conflicts.value.some(c => c.courseIds.includes(courseId))
}

// Generate simulation dates
const getCourseSimulation = (courseId) => {
  const settings = courseSettings[courseId]
  if (!settings) return 'Setting tidak tersedia'

  const startDate = settings.useCustom ? settings.customStartDate : globalSettings.startDate
  const endDate = settings.useCustom ? settings.customEndDate : globalSettings.endDate
  const meetings = settings.useCustom ? settings.customMeetings : globalSettings.totalMeetings

  if (!startDate || !endDate) return 'Tentukan tanggal mulai & selesai'

  // Calculate meeting dates
  const day = settings.day
  const dayNum = dayMap[day]
  const dates = []
  
  let currentDate = new Date(startDate)
  const endDateObj = new Date(endDate)
  
  // Find first occurrence of the day
  while (currentDate.getDay() !== dayNum && currentDate <= endDateObj) {
    currentDate.setDate(currentDate.getDate() + 1)
  }
  
  // Generate dates
  while (dates.length < meetings && currentDate <= endDateObj) {
    dates.push(new Date(currentDate))
    currentDate.setDate(currentDate.getDate() + 7) // Next week
  }

  if (dates.length === 0) return 'Tidak ada jadwal yang dapat di-generate'

  const firstDate = formatDate(dates[0].toISOString().split('T')[0])
  const lastDate = dates.length > 1 ? formatDate(dates[dates.length - 1].toISOString().split('T')[0]) : firstDate

  return `${settings.day} ${settings.startTime}-${settings.endTime}: ${firstDate} - ${lastDate} (${dates.length}/${meetings} pertemuan)`
}

// Validation function for time and date
const validateEnrollment = () => {
  const errors = []
  
  // Validate global settings dates
  if (globalSettings.startDate && globalSettings.endDate) {
    if (globalSettings.endDate < globalSettings.startDate) {
      errors.push('Tanggal selesai global tidak boleh lebih awal dari tanggal mulai')
    }
  }
  
  // Validate each selected course
  const selectedList = Object.entries(courseSettings).filter(([id, data]) => data.selected)
  
  for (const [id, data] of selectedList) {
    const courseName = courses.value.find(c => c.id === parseInt(id))?.course_name || `Course ${id}`
    
    // Validate time
    if (data.startTime && data.endTime) {
      if (data.endTime <= data.startTime) {
        errors.push(`${courseName}: Jam selesai tidak boleh lebih awal atau sama dengan jam mulai`)
      }
    }
    
    // Validate custom dates if using custom settings
    if (data.useCustom) {
      if (data.customStartDate && data.customEndDate) {
        if (data.customEndDate < data.customStartDate) {
          errors.push(`${courseName}: Tanggal selesai kustom tidak boleh lebih awal dari tanggal mulai`)
        }
      }
    }
    
    // Validate required fields
    if (!data.day) {
      errors.push(`${courseName}: Hari harus dipilih`)
    }
    if (!data.startTime) {
      errors.push(`${courseName}: Jam mulai harus diisi`)
    }
    if (!data.endTime) {
      errors.push(`${courseName}: Jam selesai harus diisi`)
    }
  }
  
  return errors
}

const enrollCourses = async () => {
  if (selectedCount.value === 0) return

  // Run validation
  const validationErrors = validateEnrollment()
  if (validationErrors.length > 0) {
    alert('Validasi gagal:\n\n' + validationErrors.join('\n'))
    return
  }

  if (conflicts.value.length > 0) {
    const confirm = window.confirm(`Terdapat ${conflicts.value.length} bentrok. Tetap lanjutkan?`)
    if (!confirm) return
  }

  enrolling.value = true
  try {
    const coursesToEnroll = Object.entries(courseSettings)
      .filter(([id, data]) => data.selected)
      .map(([id, data]) => {
        const effectiveDates = getEffectiveDates(parseInt(id))
        const meetings = data.useCustom ? data.customMeetings : globalSettings.totalMeetings
        const sessions = data.useCustom ? data.customSessions : globalSettings.sessionsPerMeeting

        return {
          course_id: parseInt(id),
          day: data.day,
          start_time: data.startTime,
          end_time: data.endTime,
          room_ids: data.roomIds || [],
          lecturer_ids: data.lecturerIds || [],
          start_date: effectiveDates.startDate,
          end_date: effectiveDates.endDate,
          total_meetings: meetings,
          sessions_per_meeting: sessions
        }
      })

    let successCount = 0
    let failCount = 0

    for (const courseData of coursesToEnroll) {
      try {
        await scheduleService.addCourse(props.classSchedule.id, courseData)
        successCount++
        delete courseSettings[courseData.course_id]
      } catch (err) {
        failCount++
        console.error(`Failed to enroll course ${courseData.course_id}:`, err)
      }
    }

    if (successCount > 0) {
      toastStore.success(`${successCount} matakuliah berhasil di-enroll`)
      emit('refresh')
      await fetchData()
    }

    if (failCount > 0) {
      toastStore.error(`${failCount} matakuliah gagal di-enroll`)
    }

  } catch (error) {
    console.error('Failed to enroll courses:', error)
    toastStore.error('Gagal menyimpan data')
  } finally {
    enrolling.value = false
  }
}

const removeEnrolled = async (detailId) => {
  if (!confirm('Hapus matakuliah ini dari jadwal?')) return

  try {
    await scheduleService.removeCourse(props.classSchedule.id, detailId)
    toastStore.success('Matakuliah berhasil dihapus')
    emit('refresh')
    await fetchData()
  } catch (error) {
    console.error('Failed to remove course:', error)
    toastStore.error('Gagal menghapus matakuliah')
  }
}

// Lifecycle
onMounted(() => {
  console.log('EnrollCourseModal mounted, show:', props.show, 'classSchedule:', props.classSchedule)
  if (props.show && props.classSchedule?.id) {
    fetchData()
  }
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})

// Watchers
watch(() => props.show, (newVal) => {
  console.log('Watch show changed:', newVal, 'classSchedule:', props.classSchedule)
  if (newVal && props.classSchedule?.id) {
    fetchData()
  }
}, { immediate: true })

watch(() => props.classSchedule, (newVal) => {
  console.log('Watch classSchedule changed:', newVal, 'show:', props.show)
  if (newVal?.id && props.show) {
    fetchData()
  }
}, { deep: true })

// Watch global settings changes to update simulation
watch(() => [globalSettings.startDate, globalSettings.endDate, globalSettings.totalMeetings], () => {
  // Trigger reactivity for course simulations
  courses.value.forEach(course => {
    if (courseSettings[course.id] && !courseSettings[course.id].useCustom) {
      // Force reactivity update
      courseSettings[course.id] = { ...courseSettings[course.id] }
    }
  })
  checkConflicts()
})
</script>
