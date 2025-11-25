<template>
  <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-gray-50">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <div class="text-center">
        <h2 class="text-3xl font-extrabold text-gray-900">
          Daftar Akun Baru
        </h2>
        <p class="mt-2 text-sm text-gray-600">
          Atau
          <Link href="/login" class="font-medium text-primary-600 hover:text-primary-500">
            masuk ke akun yang ada
          </Link>
        </p>
      </div>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
        <form class="space-y-6" @submit.prevent="submit">
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700">
              Nama Lengkap
            </label>
            <div class="mt-1">
              <input
                id="name"
                name="name"
                type="text"
                autocomplete="name"
                required
                v-model="form.name"
                class="input"
                :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': form.errors.name }"
              />
              <p v-if="form.errors.name" class="mt-2 text-sm text-red-600">
                {{ form.errors.name }}
              </p>
            </div>
          </div>

          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">
              Email
            </label>
            <div class="mt-1">
              <input
                id="email"
                name="email"
                type="email"
                autocomplete="email"
                required
                v-model="form.email"
                class="input"
                :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': form.errors.email }"
              />
              <p v-if="form.errors.email" class="mt-2 text-sm text-red-600">
                {{ form.errors.email }}
              </p>
            </div>
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">
              Password
            </label>
            <div class="mt-1">
              <input
                id="password"
                name="password"
                type="password"
                autocomplete="new-password"
                required
                v-model="form.password"
                class="input"
                :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': form.errors.password }"
              />
              <p v-if="form.errors.password" class="mt-2 text-sm text-red-600">
                {{ form.errors.password }}
              </p>
            </div>
          </div>

          <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
              Konfirmasi Password
            </label>
            <div class="mt-1">
              <input
                id="password_confirmation"
                name="password_confirmation"
                type="password"
                autocomplete="new-password"
                required
                v-model="form.password_confirmation"
                class="input"
                :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': form.errors.password_confirmation }"
              />
              <p v-if="form.errors.password_confirmation" class="mt-2 text-sm text-red-600">
                {{ form.errors.password_confirmation }}
              </p>
            </div>
          </div>

          <div class="text-sm text-gray-600">
            <p>Dengan mendaftar, Anda menyetujui:</p>
            <ul class="list-disc list-inside mt-2 space-y-1">
              <li>Kebijakan privasi kami</li>
              <li>Syarat dan ketentuan layanan</li>
            </ul>
          </div>

          <div>
            <button
              type="submit"
              class="btn btn-primary w-full justify-center"
              :disabled="form.processing"
            >
              <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Daftar
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useForm, Link } from '@inertiajs/vue3'

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

const submit = () => {
  form.post('/register', {
    onFinish: () => form.reset('password', 'password_confirmation'),
  })
}
</script>