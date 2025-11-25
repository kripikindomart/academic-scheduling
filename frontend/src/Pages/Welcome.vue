<template>
  <div class="min-h-screen bg-gradient-to-br from-primary-50 to-secondary-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <div class="text-center">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">
          Academic Scheduling System
        </h1>
        <p class="text-lg text-gray-600 mb-8">
          Sistem Penjadwalan Perkuliahan Terintegrasi
        </p>
      </div>

      <div class="bg-white py-8 px-4 shadow-lg sm:rounded-lg sm:px-10">
        <div class="space-y-6">
          <div class="text-center">
            <svg class="mx-auto h-12 w-12 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <h2 class="mt-4 text-xl font-semibold text-gray-900">
              Selamat Datang di Sistem Penjadwalan
            </h2>
            <p class="mt-2 text-sm text-gray-600">
              Kelola jadwal perkuliahan, absensi, dan penilaian dengan mudah
            </p>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div class="text-center p-4 bg-primary-50 rounded-lg">
              <div class="text-2xl font-bold text-primary-600">60+</div>
              <div class="text-sm text-gray-600">Modul Terintegrasi</div>
            </div>
            <div class="text-center p-4 bg-success-50 rounded-lg">
              <div class="text-2xl font-bold text-success-600">100%</div>
              <div class="text-sm text-gray-600">Responsif</div>
            </div>
          </div>

          <div class="space-y-3">
            <a href="/login" class="btn btn-primary w-full justify-center">
              Masuk ke Sistem
            </a>
            <div class="text-center">
              <span class="text-sm text-gray-600">
                Belum punya akun?
                <a href="/register" class="font-medium text-primary-600 hover:text-primary-500">
                  Daftar di sini
                </a>
              </span>
            </div>
          </div>
        </div>
      </div>

      <div class="mt-8 text-center text-sm text-gray-500">
        <p>&copy; 2024 Academic Scheduling System. All rights reserved.</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// Welcome page - no specific logic needed
</script>