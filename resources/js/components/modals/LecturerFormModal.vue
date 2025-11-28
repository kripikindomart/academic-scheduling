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
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                  {{ isEditing ? 'Edit Dosen' : 'Tambah Dosen Baru' }}
                </h3>

                <!-- Informasi Pribadi Section -->
                <div class="mb-6">
                  <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Informasi Pribadi
                  </h4>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Profile Photo -->
                    <div class="md:col-span-2">
                      <label class="block text-sm font-medium text-gray-700 mb-2">Foto Profil</label>
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
                              class="h-32 w-32 rounded-2xl bg-gradient-to-br from-blue-50 to-indigo-100 border-4 border-white shadow-xl flex items-center justify-center transition-all duration-300 group-hover:from-blue-100 group-hover:to-indigo-200"
                            >
                              <div class="text-center">
                                <svg class="h-16 w-16 text-blue-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <p class="text-xs text-blue-600 font-medium">No Photo</p>
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
                                 'hover:border-blue-400 hover:bg-blue-50/30 cursor-pointer',
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
                                class="px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors duration-200"
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
                    <!-- NIP/Employee Number -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">NIP/NIDN <span class="text-red-500">*</span></label>
                      <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                          <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                          </svg>
                        </div>
                        <input
                          v-model="form.employee_number"
                          type="text"
                          required
                          class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors"
                          placeholder="Nomor Induk Pegawai/Dosen"
                        />
                      </div>
                    </div>

                    <!-- Nama Lengkap -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                      <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                          <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                          </svg>
                        </div>
                        <input
                          v-model="form.name"
                          type="text"
                          required
                          class="block w-full pl-10 pr-3 py-2 border rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors"
                          :class="fieldErrors.name ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300'"
                          placeholder="Nama lengkap dosen"
                        />
                        <div v-if="fieldErrors.name" class="mt-1 text-sm text-red-600">
                          {{ fieldErrors.name[0] }}
                        </div>
                      </div>
                    </div>

                    <!-- Email -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                      <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                          <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                          </svg>
                        </div>
                        <input
                          v-model="form.email"
                          type="email"
                          required
                          class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors"
                          placeholder="email@universitas.ac.id"
                        />
                      </div>
                    </div>

                    <!-- Telepon -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Telepon</label>
                      <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                          <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                          </svg>
                        </div>
                        <input
                          v-model="form.phone"
                          type="tel"
                          class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors"
                          placeholder="+62 812-3456-7890"
                        />
                      </div>
                    </div>

                    <!-- Alamat -->
                    <div class="md:col-span-2">
                      <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                      <div class="relative">
                        <div class="absolute top-3 left-3 pointer-events-none">
                          <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                          </svg>
                        </div>
                        <textarea
                          v-model="form.address"
                          rows="2"
                          class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors resize-none"
                          placeholder="Alamat lengkap"
                        ></textarea>
                      </div>
                    </div>

                    <!-- Kota -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Kota</label>
                      <input
                        v-model="form.city"
                        type="text"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors"
                        placeholder="Kota"
                      />
                    </div>

                    <!-- Provinsi -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Provinsi</label>
                      <input
                        v-model="form.province"
                        type="text"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors"
                        placeholder="Provinsi"
                      />
                    </div>

                    <!-- Kode Pos -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Kode Pos</label>
                      <input
                        v-model="form.postal_code"
                        type="text"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors"
                        placeholder="12345"
                      />
                    </div>
                  </div>
                </div>

                <!-- Data Diri Lengkap Section -->
                <div class="mb-6">
                  <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                    </svg>
                    Data Diri Lengkap
                  </h4>
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Tempat Lahir -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Tempat Lahir</label>
                      <input
                        v-model="form.birth_place"
                        type="text"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors"
                        placeholder="Kota kelahiran"
                      />
                    </div>

                    <!-- Tanggal Lahir -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir</label>
                      <input
                        v-model="form.birth_date"
                        type="date"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors"
                      />
                    </div>

                    <!-- Jenis Kelamin -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin</label>
                      <select
                        v-model="form.gender"
                        class="block w-full px-3 py-2 border rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors"
                        :class="fieldErrors.gender ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300'"
                      >
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                      </select>
                      <div v-if="fieldErrors.gender" class="mt-1 text-sm text-red-600">
                        {{ fieldErrors.gender[0] }}
                      </div>
                    </div>

                    <!-- Agama -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Agama</label>
                      <select
                        v-model="form.religion"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors"
                      >
                        <option value="">Pilih Agama</option>
                        <option value="islam">Islam</option>
                        <option value="kristen">Kristen</option>
                        <option value="katolik">Katolik</option>
                        <option value="hindu">Hindu</option>
                        <option value="budha">Budha</option>
                        <option value="konghucu">Konghucu</option>
                        <option value="lainnya">Lainnya</option>
                      </select>
                    </div>

                    <!-- Kewarganegaraan -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Kewarganegaraan</label>
                      <select
                        v-model="form.nationality"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors"
                      >
                        <option value="">Pilih Kewarganegaraan</option>
                        <option value="WNI">WNI</option>
                        <option value="WNA">WNA</option>
                      </select>
                    </div>

                    <!-- Golongan Darah -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Golongan Darah</label>
                      <select
                        v-model="form.blood_type"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors"
                      >
                        <option value="">Pilih Golongan Darah</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="AB">AB</option>
                        <option value="O">O</option>
                      </select>
                    </div>

                    <!-- No. KTP -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">No. KTP</label>
                      <input
                        v-model="form.id_card_number"
                        type="text"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors"
                        placeholder="Nomor KTP"
                      />
                    </div>

                    <!-- No. Paspor -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">No. Paspor</label>
                      <input
                        v-model="form.passport_number"
                        type="text"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors"
                        placeholder="Nomor Paspor (opsional)"
                      />
                    </div>
                  </div>
                </div>

                <!-- Informasi Akademik Section -->
                <div class="mb-6">
                  <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    Informasi Akademik
                  </h4>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Program Studi -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Program Studi <span class="text-red-500">*</span></label>
                      <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                          <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                          </svg>
                        </div>
                        <select
                          v-model="form.program_study_id"
                          required
                          class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors appearance-none bg-white"
                        >
                          <option value="">Pilih Program Studi</option>
                          <option
                            v-for="programStudy in programStudies"
                            :key="programStudy.id"
                            :value="programStudy.id"
                          >
                            {{ programStudy.degree }} - {{ programStudy.name }}
                          </option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                          <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                          </svg>
                        </div>
                      </div>
                      <p class="mt-1 text-xs text-gray-500">Pilih program studi dari database</p>
                    </div>

                    <!-- Fakultas -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Fakultas</label>
                      <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                          <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                          </svg>
                        </div>
                        <select
                          v-model="form.faculty"
                          class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors appearance-none bg-white"
                        >
                          <option value="">Pilih Fakultas</option>
                          <option
                            v-for="faculty in faculties"
                            :key="faculty"
                            :value="faculty"
                          >
                            {{ faculty }}
                          </option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                          <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                          </svg>
                        </div>
                      </div>
                      <p class="mt-1 text-xs text-gray-500">Default: Sekolah Pascasarjana</p>
                    </div>

                    <!-- Pangkat Akademik -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Pangkat Akademik</label>
                      <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                          <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                          </svg>
                        </div>
                        <select
                          v-model="form.rank"
                          class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors appearance-none bg-white"
                        >
                          <option value="">Pilih Pangkat</option>
                          <option value="Asisten Ahli">Asisten Ahli</option>
                          <option value="Lektor">Lektor</option>
                          <option value="Lektor Kepala">Lektor Kepala</option>
                          <option value="Profesor">Profesor</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                          <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                          </svg>
                        </div>
                      </div>
                    </div>

                    <!-- Bidang Keahlian -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Bidang Keahlian</label>
                      <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                          <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                          </svg>
                        </div>
                        <input
                          v-model="form.specialization"
                          type="text"
                          class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors"
                          placeholder="Contoh: Machine Learning, Database"
                        />
                      </div>
                    </div>
                  </div>
                </div>

  
                <!-- Informasi Kepegawaian Section -->
                <div class="mb-6">
                  <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A9.942 9.942 0 0112 22c-5.376 0-9.944-4.319-9.944-9.745 0-5.426 4.568-9.745 9.944-9.745 2.523 0 4.883.912 6.742 2.555m2.553-2.553A8.966 8.966 0 0121 12v0m-9.193 9.193L3 21l3.873-8.607" />
                    </svg>
                    Informasi Kepegawaian
                  </h4>
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Jenis Pegawai -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Pegawai <span class="text-red-500">*</span></label>
                      <select
                        v-model="form.employment_type"
                        required
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors"
                      >
                        <option value="">Pilih Jenis Pegawai</option>
                        <option value="Tetap">PNS/Tetap</option>
                        <option value="Kontrak">Kontrak</option>
                        <option value="Paruh">Paruh Waktu</option>
                        <option value="Tamu">Tamu</option>
                      </select>
                    </div>

                    <!-- Status Kepegawaian -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Status Kepegawaian <span class="text-red-500">*</span></label>
                      <select
                        v-model="form.employment_status"
                        required
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors"
                      >
                        <option value="">Pilih Status</option>
                        <option value="permanent">Tetap</option>
                        <option value="contract">Kontrak</option>
                        <option value="part_time">Paruh Waktu</option>
                        <option value="guest">Tamu</option>
                      </select>
                    </div>

                    <!-- Status Dosen -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Status Dosen <span class="text-red-500">*</span></label>
                      <select
                        v-model="form.status"
                        required
                        class="block w-full px-3 py-2 border rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors"
                        :class="fieldErrors.status ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300'"
                      >
                        <option value="">Pilih Status Dosen</option>
                        <option value="Aktif">Aktif</option>
                        <option value="Cuti">Cuti</option>
                        <option value="Tidak">Non-aktif</option>
                      </select>
                      <div v-if="fieldErrors.status" class="mt-1 text-sm text-red-600">
                        {{ fieldErrors.status[0] }}
                      </div>
                    </div>

                    <!-- Jabatan -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Jabatan</label>
                      <input
                        v-model="form.position"
                        type="text"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors"
                        placeholder="Contoh: Dosen, Kepala Lab, etc."
                      />
                    </div>

                    <!-- Tanggal Masuk -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Masuk</label>
                      <input
                        v-model="form.hire_date"
                        type="date"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors"
                      />
                    </div>

                    <!-- Ruang Kantor -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Ruang Kantor</label>
                      <input
                        v-model="form.office_room"
                        type="text"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors"
                        placeholder="Contoh: Lt. 3 Ruang 301"
                      />
                    </div>

                    <!-- Departemen (Auto-fill dari Program Studi) -->
                    <div class="md:col-span-2 lg:col-span-1">
                      <label class="block text-sm font-medium text-gray-700 mb-2">Departemen</label>
                      <input
                        v-model="form.department"
                        type="text"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors"
                        placeholder="Departemen (diisi otomatis)"
                        readonly
                      />
                      <p class="mt-1 text-xs text-gray-500">Akan terisi otomatis berdasarkan Program Studi</p>
                    </div>
                  </div>
                </div>

                <!-- Informasi Pendidikan Section -->
                <div class="mb-6">
                  <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                    </svg>
                    Informasi Pendidikan
                  </h4>
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Pendidikan Tertinggi -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Pendidikan Tertinggi</label>
                      <select
                        v-model="form.highest_education"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors"
                      >
                        <option value="">Pilih Pendidikan</option>
                        <option value="S1">S1/D4</option>
                        <option value="S2">S2/Magister</option>
                        <option value="S3">S3/Doktor</option>
                      </select>
                    </div>

                    <!-- Institusi Pendidikan -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Institusi Pendidikan</label>
                      <input
                        v-model="form.education_institution"
                        type="text"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors"
                        placeholder="Universitas/Institusi"
                      />
                    </div>

                    <!-- Jurusan -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Jurusan</label>
                      <input
                        v-model="form.education_major"
                        type="text"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors"
                        placeholder="Jurusan/Program Studi"
                      />
                    </div>

                    <!-- Tahun Lulus -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Lulus</label>
                      <input
                        v-model="form.graduation_year"
                        type="number"
                        min="1970"
                        :max="new Date().getFullYear()"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors"
                        placeholder="Tahun kelulusan"
                      />
                    </div>

                    <!-- Bidang Keahlian (Spesialisasi) -->
                    <div class="md:col-span-2 lg:col-span-1">
                      <label class="block text-sm font-medium text-gray-700 mb-2">Bidang Keahlian</label>
                      <input
                        v-model="form.specialization"
                        type="text"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors"
                        placeholder="Bidang spesialisasi keahlian"
                      />
                    </div>
                  </div>
                </div>

                <!-- Catatan Tambahan -->
                <div class="mb-6">
                  <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Catatan Tambahan
                  </h4>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                    <textarea
                      v-model="form.notes"
                      rows="3"
                      class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors resize-none"
                      placeholder="Catatan tambahan mengenai dosen (opsional)"
                    ></textarea>
                  </div>
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
              <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ isEditing ? 'Update' : 'Simpan' }}
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
import { ref, computed, watch, onMounted } from 'vue'
import { useToastStore } from '@/stores/toast'
import lecturerService from '@/services/lecturerService'
import programStudyService from '@/services/programStudyService'

const props = defineProps({
  show: {
    type: Boolean,
    required: true
  },
  lecturer: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['close', 'saved'])

const toastStore = useToastStore()
const loading = ref(false)
const programStudies = ref([])
const faculties = ref(['Sekolah Pascasarjana']) // Default faculty
const fieldErrors = ref({}) // For storing validation errors per field

const defaultForm = {
  // Core required fields
  employee_number: '',
  name: '',
  email: '',
  phone: '',
  address: '',
  gender: 'male',
  birth_date: '',
  birth_place: '',
  department: '',
  faculty: 'Sekolah Pascasarjana',
  program_study_id: null,
  rank: '',
  employment_type: '',
  employment_status: '',
  hire_date: '',
  position: '',
  status: 'Aktif',
  specialization: '',
  highest_education: '',
  photo: null,
  // Optional fields that exist in database
  city: '',
  province: '',
  postal_code: '',
  nationality: 'Indonesia',
  religion: '',
  blood_type: '',
  id_card_number: '',
  passport_number: '',
  education_institution: '',
  education_major: '',
  graduation_year: null,
  office_room: '',
  notes: ''
}

const form = ref({ ...defaultForm })
const photoPreview = ref(null)
const photoInput = ref(null)

const isEditing = computed(() => !!props.lecturer)

// Reset form when modal opens/closes or lecturer changes
watch(() => props.show, (show) => {
  if (show) {
    resetForm()
    loadProgramStudies() // Load program studies when modal opens
  }
})

watch(() => props.lecturer, (lecturer) => {
  console.log('📋 LecturerFormModal - props.lecturer changed:', lecturer)

  if (lecturer) {
    console.log('📋 Setting form with lecturer data:', lecturer)

    // Process and format the data before setting form
    const processedLecturer = { ...lecturer }

    // Format dates for HTML input (yyyy-MM-dd)
    if (lecturer.birth_date) {
      processedLecturer.birth_date = lecturer.birth_date.split('T')[0]
    }
    if (lecturer.hire_date) {
      processedLecturer.hire_date = lecturer.hire_date.split('T')[0]
    }
    if (lecturer.termination_date) {
      processedLecturer.termination_date = lecturer.termination_date.split('T')[0]
    }

    form.value = { ...defaultForm, ...processedLecturer }
    console.log('📋 Form after setting:', form.value)

    // Set photo preview if photo exists
    if (lecturer.photo) {
      photoPreview.value = `/storage/${lecturer.photo}`
      console.log('📸 Photo preview set:', `/storage/${lecturer.photo}`)
    }

    // Set department from program study after program studies are loaded
    if (lecturer.program_study_id && programStudies.value.length > 0) {
      const selectedProgram = programStudies.value.find(ps => ps.id === lecturer.program_study_id)
      if (selectedProgram) {
        form.value.department = selectedProgram.name
        console.log('📋 Department set from program study:', selectedProgram.name)
      }
    }
  } else {
    console.log('📋 No lecturer data, resetting form')
    resetForm()
  }
})

// Watch for program_study_id changes to update department
watch(() => form.value.program_study_id, (newProgramId) => {
  if (newProgramId) {
    const selectedProgram = programStudies.value.find(ps => ps.id === newProgramId)
    form.value.department = selectedProgram ? selectedProgram.name : ''
  }
})

// Watch for programStudies loading to update department if we have a lecturer
watch(programStudies, (newProgramStudies) => {
  if (newProgramStudies.length > 0 && props.lecturer?.program_study_id) {
    const selectedProgram = newProgramStudies.find(ps => ps.id === props.lecturer.program_study_id)
    if (selectedProgram) {
      form.value.department = selectedProgram.name
    }
  }
})

const resetForm = () => {
  if (props.lecturer) {
    form.value = { ...defaultForm, ...props.lecturer }

    // Set department from program study if available
    if (props.lecturer.program_study_id && programStudies.value.length > 0) {
      const selectedProgram = programStudies.value.find(ps => ps.id === props.lecturer.program_study_id)
      if (selectedProgram) {
        form.value.department = selectedProgram.name
      }
    }
  } else {
    form.value = { ...defaultForm }
  }

  // Reset photo preview
  if (props.lecturer?.photo) {
    photoPreview.value = `/storage/${props.lecturer.photo}`
  } else {
    photoPreview.value = null
  }

  // Reset field errors
  fieldErrors.value = {}
}

const handlePhotoChange = (event) => {
  const file = event.target.files[0]
  console.log('📸 File selected:', file ? file.name : 'No file selected', file)

  if (file) {
    // Validate file size (5MB max)
    if (file.size > 5 * 1024 * 1024) {
      toastStore.error('Error', 'Ukuran foto maksimal 5MB')
      event.target.value = ''
      return
    }

    // Validate file type
    if (!file.type.startsWith('image/')) {
      toastStore.error('Error', 'File harus berupa gambar')
      event.target.value = ''
      return
    }

    // Create preview
    const reader = new FileReader()
    reader.onload = (e) => {
      photoPreview.value = e.target.result
      form.value.photo = file
      console.log('📸 Photo set in form:', form.value.photo instanceof File ? 'File object' : typeof form.value.photo)
    }
    reader.readAsDataURL(file)
  } else {
    console.log('📸 No file selected')
  }
}

const removePhoto = () => {
  photoPreview.value = null
  form.value.photo = null
  if (photoInput.value) {
    photoInput.value.value = ''
  }
}

// Fetch program studies on component mount
onMounted(async () => {
  console.log('🔍 LecturerFormModal mounted, show:', props.show)
  console.log('🔍 User logged in:', !!localStorage.getItem('token'))
  await fetchProgramStudies()
})

const fetchProgramStudies = async () => {
  try {
    console.log('🔄 Starting fetchProgramStudies...')
    console.log('📋 Token exists:', !!localStorage.getItem('token'))
    console.log('📋 Token value:', localStorage.getItem('token')?.substring(0, 20) + '...')

    const response = await programStudyService.getAll({
      is_active: true,
      per_page: 100
    })

    console.log('📦 API Response:', response)

    if (response && response.data) {
      const responseData = response.data
      console.log('📊 Response data structure:', {
        success: responseData.success,
        message: responseData.message,
        dataType: typeof responseData.data,
        isArray: Array.isArray(responseData.data),
        length: responseData.data?.length
      })

      if (responseData.data && Array.isArray(responseData.data)) {
        programStudies.value = responseData.data
        console.log('✅ Program studies loaded for form:', programStudies.value.length, 'items')
        programStudies.value.forEach((ps, index) => {
          console.log(`  ${index + 1}. ${ps.name} (${ps.code})`)
        })
      } else if (Array.isArray(responseData)) {
        programStudies.value = responseData
        console.log('✅ Program studies loaded (direct array):', programStudies.value.length, 'items')
      }
    } else {
      console.warn('⚠️ No response data received')
    }
  } catch (error) {
    console.error('❌ Error fetching program studies for form:', error)
    console.error('❌ Error details:', {
      message: error.message,
      response: error.response?.data,
      status: error.response?.status
    })
    toastStore.addToast({
      type: 'error',
      title: 'Error',
      message: 'Gagal memuat data program studi'
    })
  }
}

const handleSubmit = async () => {
  console.log('🚀 handleSubmit called - isEditing:', isEditing.value)
  console.log('🚀 Form data before submission:', form.value)

  // Check required fields
  const requiredFields = ['employee_number', 'name', 'email', 'program_study_id', 'employment_type', 'employment_status', 'status']
  const missingFields = requiredFields.filter(field => !form.value[field] || form.value[field] === '')

  if (missingFields.length > 0) {
    console.error('❌ Missing required fields:', missingFields)
    toastStore.error('Error', `Harap lengkapi field: ${missingFields.join(', ')}`)
    return
  }

  loading.value = true

  try {
    // Create FormData for file upload
    const formData = new FormData()

    // Define valid fields that exist in database
    const validFields = [
      'employee_number', 'name', 'email', 'phone', 'gender', 'birth_date', 'birth_place',
      'address', 'city', 'province', 'postal_code', 'nationality', 'religion', 'blood_type',
      'id_card_number', 'passport_number', 'status', 'employment_status', 'employment_type',
      'hire_date', 'position', 'rank', 'specialization', 'highest_education',
      'education_institution', 'education_major', 'graduation_year',
      'office_room', 'notes', 'department', 'faculty', 'program_study_id'
    ]

    // Add method override for PUT requests
    if (isEditing.value) {
      formData.append('_method', 'PUT')
    }

    // Handle photo upload separately
    if (form.value.photo instanceof File) {
      formData.append('photo', form.value.photo)
      console.log('📸 Adding photo file:', form.value.photo.name, form.value.photo.type, form.value.photo.size)
    }

    // Add only valid form fields (excluding photo)
    validFields.forEach(key => {
      const value = form.value[key]

      // Handle numeric fields
      if (key === 'graduation_year' || key === 'program_study_id') {
        if (value !== null && value !== '' && value !== undefined) {
          formData.append(key, value)
        }
      }
      // Handle regular string fields
      else if (typeof value === 'string') {
        if (value.trim() !== '') {
          formData.append(key, value.trim())
        }
      }
      // Handle other types (but exclude arrays)
      else if (value !== null && value !== undefined && !Array.isArray(value)) {
        formData.append(key, value)
      }
    })

    // Debug FormData content
    console.log('📝 FormData entries:')
    for (let [key, value] of formData.entries()) {
      console.log(`  ${key}:`, value instanceof File ? `[File: ${value.name}]` : value)
    }

    
    if (isEditing.value) {
      await lecturerService.update(props.lecturer.id, formData)
      toastStore.success('Berhasil', 'Data dosen berhasil diperbarui')
    } else {
      await lecturerService.create(formData)
      toastStore.success('Berhasil', 'Dosen baru berhasil ditambahkan')
    }

    emit('saved')
  } catch (error) {
    console.error('❌ Form submission error:', error)
    console.error('❌ Error response data:', error.response?.data)

    // Handle Laravel validation errors specifically
    if (error.response?.status === 422 && error.response?.data?.errors) {
      const validationErrors = error.response.data.errors
      const errorMessages = Object.values(validationErrors).flat()

      // Set error object for display in form fields
      fieldErrors.value = validationErrors

      // Show toast with all validation errors
      const title = 'Validasi Gagal'
      const description = errorMessages.join(', ')
      toastStore.error(title, description)

      // Log detailed errors for debugging
      console.error('❌ Validation errors:', validationErrors)
    } else {
      // Handle other types of errors
      let errorMessage = 'Terjadi kesalahan saat menyimpan data'
      if (error.response?.data?.message) {
        errorMessage = error.response.data.message
      } else if (error.response?.data?.meta?.message) {
        errorMessage = error.response.data.meta.message
      } else if (error.message) {
        errorMessage = error.message
      }

      // Show error toast using handleError method
      toastStore.handleError(error, isEditing.value ? 'Update dosen' : 'Tambah dosen')
    }
  } finally {
    loading.value = false
  }
}
</script>