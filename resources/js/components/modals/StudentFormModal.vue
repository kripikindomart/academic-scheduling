<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 transition-opacity" aria-hidden="true">
        <div class="absolute inset-0 bg-gray-500 opacity-75" @click="$emit('close')"></div>
      </div>

      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl sm:w-full">
        <form @submit.prevent="handleSubmit">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="w-full">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                  {{ isEditing ? 'Edit Mahasiswa' : 'Tambah Mahasiswa Baru' }}
                </h3>

                <!-- Foto Profil Section -->
                <div class="mb-6">
                  <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Foto Profil
                  </h4>
                  <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-4 sm:space-y-0 sm:space-x-6">
                    <!-- Photo Preview -->
                    <div class="relative group">
                      <div class="relative">
                        <img
                          v-if="photoPreview"
                          :src="photoPreview"
                          alt="Profile photo"
                          class="h-32 w-32 object-cover rounded-2xl border-4 border-white shadow-xl transition-transform duration-300 group-hover:scale-105"
                        >
                        <div
                          v-else
                          class="h-32 w-32 rounded-2xl bg-gradient-to-br from-green-50 to-emerald-100 border-4 border-white shadow-xl flex items-center justify-center transition-all duration-300 group-hover:from-green-100 group-hover:to-emerald-200"
                        >
                          <div class="text-center">
                            <svg class="h-16 w-16 text-green-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <p class="text-xs text-green-600 font-medium">No Photo</p>
                          </div>
                        </div>

                        <!-- Remove Button -->
                        <button
                          v-if="photoPreview"
                          @click="removePhoto"
                          type="button"
                          class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-1.5 shadow-lg transition-colors duration-200 opacity-0 group-hover:opacity-100"
                          title="Remove photo"
                        >
                          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                          </svg>
                        </button>
                      </div>
                    </div>

                    <!-- Upload Area -->
                    <div class="flex-1 w-full">
                      <div class="border-2 border-dashed rounded-xl p-6 text-center transition-all duration-200"
                           :class="[
                             'hover:border-green-400 hover:bg-green-50/30 cursor-pointer',
                             photoPreview ? 'border-green-400 bg-green-50/30' : 'border-gray-300 bg-gray-50/50'
                           ]"
                           @click="$refs.photoInput.click()">

                        <!-- Upload Icon -->
                        <div class="mx-auto">
                          <svg v-if="!photoPreview" class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                          </svg>
                          <svg v-else class="h-12 w-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                          </svg>
                        </div>

                        <!-- Upload Text -->
                        <div class="mt-4">
                          <p class="text-sm font-medium" :class="photoPreview ? 'text-green-600' : 'text-gray-700'">
                            {{ photoPreview ? 'Photo uploaded successfully!' : 'Click to upload photo' }}
                          </p>
                          <p class="text-xs text-gray-500 mt-1">
                            PNG, JPG, GIF up to 5MB
                          </p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-4 flex justify-center space-x-2">
                          <button
                            v-if="photoPreview"
                            @click.stop="removePhoto"
                            type="button"
                            class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors duration-200"
                          >
                            Remove Photo
                          </button>
                          <button
                            v-else
                            type="button"
                            class="px-3 py-1.5 text-xs font-medium text-green-600 bg-green-50 rounded-lg hover:bg-green-100 transition-colors duration-200"
                          >
                            Choose File
                          </button>
                        </div>
                      </div>

                      <!-- Hidden File Input -->
                      <input
                        ref="photoInput"
                        type="file"
                        class="sr-only"
                        @change="handlePhotoChange"
                        accept="image/*"
                      >

                      <!-- Requirements -->
                      <div class="mt-3 text-xs text-gray-500 bg-gray-50/50 rounded-lg p-3">
                        <p class="font-medium text-gray-700 mb-1">Requirements:</p>
                        <ul class="space-y-1">
                          <li class="flex items-center">
                            <svg class="h-3 w-3 text-gray-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                              <circle cx="10" cy="10" r="3" />
                            </svg>
                            Format: PNG, JPG, GIF
                          </li>
                          <li class="flex items-center">
                            <svg class="h-3 w-3 text-gray-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                              <circle cx="10" cy="10" r="3" />
                            </svg>
                            Maximum size: 5MB
                          </li>
                          <li class="flex items-center">
                            <svg class="h-3 w-3 text-gray-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                              <circle cx="10" cy="10" r="3" />
                            </svg>
                            Ideal ratio: 1:1 (square)
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Informasi Pribadi Section -->
                <div class="mb-6">
                  <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Informasi Pribadi
                  </h4>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama Lengkap -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nama Lengkap <span class="text-red-500">*</span>
                      </label>
                      <input
                        v-model="form.name"
                        type="text"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        :class="{ 'border-red-500': errors.name }"
                      >
                      <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ Array.isArray(errors.name) ? errors.name[0] : errors.name }}</p>
                    </div>

                    <!-- NIM -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">
                        NIM <span class="text-red-500">*</span>
                      </label>
                      <input
                        v-model="form.student_number"
                        type="text"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        :class="{ 'border-red-500': errors.student_number }"
                      >
                      <p v-if="errors.student_number" class="mt-1 text-sm text-red-600">{{ Array.isArray(errors.student_number) ? errors.student_number[0] : errors.student_number }}</p>
                    </div>

                    <!-- Email -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">
                        Email <span class="text-red-500">*</span>
                      </label>
                      <input
                        v-model="form.email"
                        type="email"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        :class="{ 'border-red-500': errors.email }"
                      >
                      <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ Array.isArray(errors.email) ? errors.email[0] : errors.email }}</p>
                    </div>

                    <!-- No. HP -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
                      <input
                        v-model="form.phone"
                        type="tel"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        :class="{ 'border-red-500': errors.phone }"
                      >
                      <p v-if="errors.phone" class="mt-1 text-sm text-red-600">{{ Array.isArray(errors.phone) ? errors.phone[0] : errors.phone }}</p>
                    </div>

                    <!-- No. KTP -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">No. KTP</label>
                      <input
                        v-model="form.id_card_number"
                        type="text"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        :class="{ 'border-red-500': errors.id_card_number }"
                      >
                      <p v-if="errors.id_card_number" class="mt-1 text-sm text-red-600">{{ Array.isArray(errors.id_card_number) ? errors.id_card_number[0] : errors.id_card_number }}</p>
                    </div>

                    <!-- Jenis Kelamin -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                      <select
                        v-model="form.gender"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        :class="{ 'border-red-500': errors.gender }"
                      >
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                      </select>
                      <p v-if="errors.gender" class="mt-1 text-sm text-red-600">{{ Array.isArray(errors.gender) ? errors.gender[0] : errors.gender }}</p>
                    </div>

                    <!-- Tempat Lahir -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                      <input
                        v-model="form.birth_place"
                        type="text"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        :class="{ 'border-red-500': errors.birth_place }"
                      >
                      <p v-if="errors.birth_place" class="mt-1 text-sm text-red-600">{{ Array.isArray(errors.birth_place) ? errors.birth_place[0] : errors.birth_place }}</p>
                    </div>

                    <!-- Tanggal Lahir -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                      <input
                        v-model="form.birth_date"
                        type="date"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        :class="{ 'border-red-500': errors.birth_date }"
                      >
                      <p v-if="errors.birth_date" class="mt-1 text-sm text-red-600">{{ Array.isArray(errors.birth_date) ? errors.birth_date[0] : errors.birth_date }}</p>
                    </div>

                    <!-- Program Studi -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">
                        Program Studi <span class="text-red-500">*</span>
                      </label>
                      <select
                        v-model="form.program_study_id"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        :class="{ 'border-red-500': errors.program_study_id }"
                      >
                        <option value="">Pilih Program Studi</option>
                        <option v-for="program in programStudies" :key="program.id" :value="program.id">
                          {{ program.name }}
                        </option>
                      </select>
                      <p v-if="errors.program_study_id" class="mt-1 text-sm text-red-600">{{ Array.isArray(errors.program_study_id) ? errors.program_study_id[0] : errors.program_study_id }}</p>
                    </div>

                    <!-- Angkatan -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">
                        Angkatan <span class="text-red-500">*</span>
                      </label>
                      <input
                        v-model="form.batch_year"
                        type="number"
                        required
                        min="2000"
                        :max="new Date().getFullYear() + 1"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        :class="{ 'border-red-500': errors.batch_year }"
                      >
                      <p v-if="errors.batch_year" class="mt-1 text-sm text-red-600">{{ Array.isArray(errors.batch_year) ? errors.batch_year[0] : errors.batch_year }}</p>
                    </div>

                    <!-- Status Mahasiswa -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Status Mahasiswa</label>
                      <select
                        v-model="form.is_regular"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        :class="{ 'border-red-500': errors.is_regular }"
                      >
                        <option :value="true">Regular</option>
                        <option :value="false">Non-Regular</option>
                      </select>
                      <p v-if="errors.is_regular" class="mt-1 text-sm text-red-600">{{ Array.isArray(errors.is_regular) ? errors.is_regular[0] : errors.is_regular }}</p>
                    </div>

                    <!-- Status Aktif -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                      <select
                        v-model="form.status"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        :class="{ 'border-red-500': errors.status }"
                      >
                        <option value="active">Aktif</option>
                        <option value="inactive">Tidak Aktif</option>
                        <option value="graduated">Lulus</option>
                        <option value="dropped_out">Drop Out</option>
                        <option value="on_leave">Cuti</option>
                      </select>
                      <p v-if="errors.status" class="mt-1 text-sm text-red-600">{{ Array.isArray(errors.status) ? errors.status[0] : errors.status }}</p>
                    </div>
                  </div>
                </div>

                <!-- Alamat Section -->
                <div class="mb-6">
                  <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Alamat
                  </h4>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Alamat Lengkap -->
                    <div class="md:col-span-2">
                      <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                      <textarea
                        v-model="form.address"
                        rows="2"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                      ></textarea>
                    </div>

                    <!-- Kota -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Kota</label>
                      <input
                        v-model="form.city"
                        type="text"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                      >
                    </div>

                    <!-- Provinsi -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Provinsi</label>
                      <input
                        v-model="form.province"
                        type="text"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                      >
                    </div>

                    <!-- Kode Pos -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Kode Pos</label>
                      <input
                        v-model="form.postal_code"
                        type="text"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                      >
                    </div>

                    <!-- Kewarganegaraan -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Kewarganegaraan</label>
                      <input
                        v-model="form.nationality"
                        type="text"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                      >
                    </div>
                  </div>
                </div>

                <!-- Informasi Akademik Section -->
                <div class="mb-6">
                  <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    Informasi Akademik
                  </h4>
                  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Semester Saat Ini -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Semester Saat Ini</label>
                      <input
                        v-model="form.current_semester"
                        type="number"
                        min="1"
                        max="14"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                      >
                    </div>

                    <!-- Tahun Ajar Saat Ini -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Ajar Saat Ini</label>
                      <input
                        v-model="form.current_year"
                        type="number"
                        min="1"
                        max="10"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                      >
                    </div>

                    <!-- IPK -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">IPK</label>
                      <input
                        v-model="form.gpa"
                        type="number"
                        step="0.01"
                        min="0"
                        max="4"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                      >
                    </div>

                    <!-- Kelas -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                      <input
                        v-model="form.class"
                        type="text"
                        placeholder="Contoh: A, B, C"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                      >
                    </div>

                    <!-- Tanggal Masuk -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Masuk</label>
                      <input
                        v-model="form.enrollment_date"
                        type="date"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                      >
                    </div>

                    <!-- Tanggal Lulus -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lulus</label>
                      <input
                        v-model="form.graduation_date"
                        type="date"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        :class="{ 'border-red-500': errors.graduation_date }"
                      >
                      <p v-if="errors.graduation_date" class="mt-1 text-sm text-red-600">{{ Array.isArray(errors.graduation_date) ? errors.graduation_date[0] : errors.graduation_date }}</p>
                    </div>
                  </div>
                </div>

                <!-- Informasi Orang Tua Section -->
                <div class="mb-6">
                  <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Informasi Orang Tua
                  </h4>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama Ayah -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Nama Ayah</label>
                      <input
                        v-model="form.father_name"
                        type="text"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                      >
                    </div>

                    <!-- Nama Ibu -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Nama Ibu</label>
                      <input
                        v-model="form.mother_name"
                        type="text"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                      >
                    </div>

                    <!-- No. HP Orang Tua -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">No. HP Orang Tua</label>
                      <input
                        v-model="form.parent_phone"
                        type="tel"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                      >
                    </div>

                    <!-- Email Orang Tua -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Email Orang Tua</label>
                      <input
                        v-model="form.parent_email"
                        type="email"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                      >
                    </div>

                    <!-- Alamat Orang Tua -->
                    <div class="md:col-span-2">
                      <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Orang Tua</label>
                      <textarea
                        v-model="form.parent_address"
                        rows="2"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                      ></textarea>
                    </div>
                  </div>
                </div>

                <!-- Informasi Tambahan Section -->
                <div class="mb-6">
                  <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Informasi Tambahan
                  </h4>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Agama -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Agama</label>
                      <select
                        v-model="form.religion"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                      >
                        <option value="">Pilih Agama</option>
                        <option value="Islam">Islam</option>
                        <option value="Kristen">Kristen</option>
                        <option value="Katolik">Katolik</option>
                        <option value="Hindu">Hindu</option>
                        <option value="Buddha">Buddha</option>
                        <option value="Konghucu">Konghucu</option>
                      </select>
                    </div>

                    <!-- Golongan Darah -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Golongan Darah</label>
                      <select
                        v-model="form.blood_type"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                      >
                        <option value="">Pilih Golongan Darah</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="AB">AB</option>
                        <option value="O">O</option>
                      </select>
                    </div>

                    <!-- Catatan -->
                    <div class="md:col-span-2">
                      <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                      <textarea
                        v-model="form.notes"
                        rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        placeholder="Masukkan catatan atau informasi tambahan..."
                      ></textarea>
                    </div>
                  </div>
                </div>

                <!-- Buat Akun User Section (hanya untuk create) -->
                <div v-if="!isEditing" class="mb-6">
                  <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Akun User Portal
                  </h4>
                  <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                    <div class="flex items-start">
                      <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      <div class="flex-1">
                        <h5 class="text-sm font-medium text-blue-900 mb-1">Informasi Akun User</h5>
                        <p class="text-sm text-blue-700">
                          Akun user akan dibuat secara otomatis dengan:
                          <br>• Username: {{ form.student_number || '[NIM]' }}
                          <br>• Default Password: {{ form.student_number || '[NIM]' }}
                          <br>• Email: {{ form.email || '[email mahasiswa]' }}
                          <br>• Role: Mahasiswa
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              type="submit"
              :disabled="loading"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ loading ? 'Menyimpan...' : (isEditing ? 'Update' : 'Simpan') }}
            </button>
            <button
              type="button"
              @click="$emit('close')"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Batal
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, watch, onMounted, computed } from 'vue';
import studentService from '@/services/studentService';
import programStudyService from '@/services/programStudyService';
import { useToastStore } from '@/stores/toast';

export default {
  name: 'StudentFormModal',
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
  emits: ['close', 'saved'],
  setup(props, { emit }) {
    const toastStore = useToastStore();
    const loading = ref(false);
    const errors = ref({});
    const programStudies = ref([]);
    const photoPreview = ref(null);
    const photoInput = ref(null);

    const form = reactive({
      student_number: '',
      name: '',
      email: '',
      phone: '',
      id_card_number: '',
      gender: '',
      birth_place: '',
      birth_date: '',
      address: '',
      city: '',
      province: '',
      postal_code: '',
      nationality: 'Indonesia',
      religion: '',
      blood_type: '',
      status: 'active',
      enrollment_date: '',
      graduation_date: '',
      current_semester: 1,
      current_year: 1,
      gpa: 0,
      class: '',
      batch_year: new Date().getFullYear(),
      is_regular: true,
      is_active: true,
      father_name: '',
      mother_name: '',
      parent_phone: '',
      parent_email: '',
      parent_address: '',
      program_study_id: '',
      notes: '',
      photo: null
    });

    const isEditing = computed(() => {
      return props.student && props.student.id;
    });

    // Load program studies
    const loadProgramStudies = async () => {
      try {
        const response = await programStudyService.getAll();
        programStudies.value = response.data || [];
      } catch (error) {
        console.error('Error loading program studies:', error);
      }
    };

    // Reset form
    const resetForm = () => {
      Object.keys(form).forEach(key => {
        if (typeof form[key] === 'boolean') {
          form[key] = key === 'is_regular' || key === 'is_active';
        } else if (typeof form[key] === 'number') {
          form[key] = key === 'batch_year' ? new Date().getFullYear() :
                    key === 'current_semester' || key === 'current_year' ? 1 : 0;
        } else {
          form[key] = key === 'nationality' ? 'Indonesia' :
                    key === 'status' ? 'active' : '';
        }
      });
      // Reset photo
      form.photo = null;
      photoPreview.value = null;
      errors.value = {};
    };

    // Populate form for editing
    const populateForm = () => {
      if (props.student) {
        Object.keys(form).forEach(key => {
          if (props.student[key] !== undefined) {
            form[key] = props.student[key];
          }
        });
        // Set photo preview if photo exists
        if (props.student.photo) {
          photoPreview.value = `/storage/${props.student.photo}`;
        } else {
          photoPreview.value = null;
        }
      }
    };

    // Validate form
    const validateForm = () => {
      const newErrors = {};

      if (!form.name) newErrors.name = 'Nama wajib diisi';
      if (!form.student_number) newErrors.student_number = 'NIM wajib diisi';
      if (!form.email) newErrors.email = 'Email wajib diisi';
      if (!form.phone) newErrors.phone = 'No. HP wajib diisi';
      if (!form.program_study_id) newErrors.program_study_id = 'Program studi wajib dipilih';
      if (!form.batch_year) newErrors.batch_year = 'Angkatan wajib diisi';

      // Email validation
      if (form.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
        newErrors.email = 'Format email tidak valid';
      }

      // Phone validation (Indonesian format)
      if (form.phone && !/^(\+62|62)?[0-9]+$/.test(form.phone.replace(/[-\s]/g, ''))) {
        newErrors.phone = 'Format nomor HP tidak valid';
      }

      errors.value = newErrors;
      return Object.keys(newErrors).length === 0;
    };

    // Handle photo change
    const handlePhotoChange = (event) => {
      const file = event.target.files[0];
      console.log('📸 File selected:', file ? file.name : 'No file selected', file);

      if (file) {
        // Validate file size (5MB max)
        if (file.size > 5 * 1024 * 1024) {
          toastStore.error('Error', 'Ukuran foto maksimal 5MB');
          event.target.value = '';
          return;
        }

        // Validate file type
        if (!file.type.startsWith('image/')) {
          toastStore.error('Error', 'File harus berupa gambar');
          event.target.value = '';
          return;
        }

        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
          photoPreview.value = e.target.result;
          form.photo = file;
          console.log('📸 Photo set in form:', form.photo instanceof File ? 'File object' : typeof form.photo);
        };
        reader.readAsDataURL(file);
      } else {
        console.log('📸 No file selected');
      }
    };

    // Remove photo
    const removePhoto = () => {
      photoPreview.value = null;
      form.photo = null;
      if (photoInput.value) {
        photoInput.value.value = '';
      }
    };

    // Handle submit
    const handleSubmit = async () => {
      if (!validateForm()) {
        return;
      }

      loading.value = true;

      try {
        // Create FormData for file upload
        const formData = new FormData();

        // Add method override for PUT requests
        if (isEditing.value) {
          formData.append('_method', 'PUT');
        }

        // Handle photo upload separately
        if (form.photo instanceof File) {
          formData.append('photo', form.photo);
          console.log('📸 Adding photo file:', form.photo.name, form.photo.type, form.photo.size);
        }

        // Add all form fields except photo
        Object.keys(form).forEach(key => {
          if (key !== 'photo') {
            const value = form[key];

            // Handle different field types
            if (value !== null && value !== undefined && value !== '') {
              if (typeof value === 'boolean') {
                formData.append(key, value ? '1' : '0');
              } else if (typeof value === 'number') {
                formData.append(key, value.toString());
              } else {
                formData.append(key, value);
              }
            }
          }
        });

        // Debug FormData content
        console.log('📝 FormData entries:');
        for (let [key, value] of formData.entries()) {
          console.log(`  ${key}:`, value instanceof File ? `[File: ${value.name}]` : value);
        }

        let response;
        if (isEditing.value) {
          response = await studentService.update(props.student.id, formData);
        } else {
          response = await studentService.create(formData);
        }

        // Emit saved event with operation type
        emit('saved', {
          student: response.data?.data || response.data,
          isEditing: isEditing.value
        });
        emit('close');
      } catch (error) {
        console.error('Error saving student:', error);

        if (error.response?.status === 422) {
          // Handle Laravel validation errors
          const validationErrors = error.response.data.errors || {};
          errors.value = validationErrors;

          // Show validation error summary as toast
          const errorMessages = Object.values(validationErrors).flat();
          if (errorMessages.length > 0) {
            toastStore.error('Validasi Gagal', errorMessages.join(', '));
          }
        } else {
          // Handle other types of errors
          let errorMessage = 'Terjadi kesalahan saat menyimpan data mahasiswa';
          if (error.response?.data?.message) {
            errorMessage = error.response.data.message;
          } else if (error.response?.data?.meta?.message) {
            errorMessage = error.response.data.meta.message;
          } else if (error.message) {
            errorMessage = error.message;
          }

          toastStore.error('Error', errorMessage);
        }
      } finally {
        loading.value = false;
      }
    };

    // Watch for props changes
    watch(() => props.show, (newShow) => {
      if (newShow) {
        if (isEditing.value) {
          populateForm();
        } else {
          resetForm();
        }
      }
    });

    // Watch for student changes
    watch(() => props.student, () => {
      if (props.show && isEditing.value) {
        populateForm();
      }
    });

    onMounted(() => {
      loadProgramStudies();
    });

    return {
      form,
      errors,
      loading,
      isEditing,
      programStudies,
      photoPreview,
      photoInput,
      handleSubmit,
      handlePhotoChange,
      removePhoto
    };
  }
};
</script>