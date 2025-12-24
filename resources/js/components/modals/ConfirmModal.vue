<template>
  <Transition
    enter-active-class="transition-opacity duration-300 ease-out"
    leave-active-class="transition-opacity duration-200 ease-in"
    enter-from-class="opacity-0"
    enter-to-class="opacity-100"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <!-- Backdrop -->
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
          <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" @click="$emit('cancel')"></div>
        </div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal Panel -->
        <Transition
          enter-active-class="transition-all duration-300 ease-out"
          leave-active-class="transition-all duration-200 ease-in"
          enter-from-class="opacity-0 scale-95 translate-y-4"
          enter-to-class="opacity-100 scale-100 translate-y-0"
          leave-from-class="opacity-100 scale-100 translate-y-0"
          leave-to-class="opacity-0 scale-95 translate-y-4"
        >
          <div v-if="show" class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
            <!-- Gradient Top Border -->
            <div :class="['h-1.5', getGradientClass()]"></div>
            
            <div class="bg-white px-6 pt-6 pb-4">
              <div class="flex flex-col items-center text-center">
                <!-- Animated Icon -->
                <div :class="[
                  'flex items-center justify-center h-16 w-16 rounded-full mb-4 animate-bounce-gentle',
                  getIconBackgroundClass()
                ]">
                  <svg :class="['h-8 w-8', getIconClass()]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path v-if="type === 'danger'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    <path v-else-if="type === 'warning'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.314 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    <path v-else-if="type === 'success'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                
                <!-- Title -->
                <h3 class="text-xl font-bold text-gray-900 mb-2">
                  {{ title }}
                </h3>
                
                <!-- Message -->
                <p class="text-sm text-gray-500 leading-relaxed max-w-sm">
                  {{ message }}
                </p>
                
                <!-- Optional Details Slot -->
                <div v-if="details" class="mt-4 p-3 bg-gray-50 rounded-lg w-full">
                  <p class="text-xs text-gray-600 font-medium">{{ details }}</p>
                </div>
              </div>
            </div>
            
            <!-- Actions -->
            <div class="bg-gray-50/80 px-6 py-4 flex flex-col-reverse sm:flex-row gap-3 sm:justify-center">
              <button
                type="button"
                @click="$emit('cancel')"
                class="w-full sm:w-auto inline-flex justify-center items-center rounded-xl border-2 border-gray-200 px-6 py-2.5 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 transition-all duration-200"
              >
                {{ cancelText }}
              </button>
              <button
                type="button"
                @click="$emit('confirm')"
                :disabled="loading"
                :class="[
                  'w-full sm:w-auto inline-flex justify-center items-center rounded-xl px-6 py-2.5 text-sm font-semibold text-white focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 active:scale-95',
                  loading ? 'opacity-75 cursor-not-allowed' : '',
                  getButtonClass()
                ]"
              >
                <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ confirmText }}
              </button>
            </div>
          </div>
        </Transition>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  show: {
    type: Boolean,
    required: true
  },
  title: {
    type: String,
    default: 'Konfirmasi'
  },
  message: {
    type: String,
    required: true
  },
  details: {
    type: String,
    default: ''
  },
  confirmText: {
    type: String,
    default: 'Ya, Lanjutkan'
  },
  cancelText: {
    type: String,
    default: 'Batal'
  },
  type: {
    type: String,
    default: 'info',
    validator: (value) => ['info', 'success', 'warning', 'danger'].includes(value)
  },
  loading: {
    type: Boolean,
    default: false
  }
})

defineEmits(['confirm', 'cancel'])

const getGradientClass = () => {
  switch (props.type) {
    case 'success':
      return 'bg-gradient-to-r from-green-400 to-emerald-500'
    case 'warning':
      return 'bg-gradient-to-r from-yellow-400 to-orange-500'
    case 'danger':
      return 'bg-gradient-to-r from-red-400 to-rose-500'
    default:
      return 'bg-gradient-to-r from-blue-400 to-indigo-500'
  }
}

const getIconBackgroundClass = () => {
  switch (props.type) {
    case 'success':
      return 'bg-gradient-to-br from-green-100 to-emerald-100'
    case 'warning':
      return 'bg-gradient-to-br from-yellow-100 to-orange-100'
    case 'danger':
      return 'bg-gradient-to-br from-red-100 to-rose-100'
    default:
      return 'bg-gradient-to-br from-blue-100 to-indigo-100'
  }
}

const getIconClass = () => {
  switch (props.type) {
    case 'success':
      return 'text-green-600'
    case 'warning':
      return 'text-yellow-600'
    case 'danger':
      return 'text-red-600'
    default:
      return 'text-blue-600'
  }
}

const getButtonClass = () => {
  switch (props.type) {
    case 'success':
      return 'bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 focus:ring-green-500 shadow-lg shadow-green-500/30'
    case 'warning':
      return 'bg-gradient-to-r from-yellow-500 to-orange-600 hover:from-yellow-600 hover:to-orange-700 focus:ring-yellow-500 shadow-lg shadow-yellow-500/30'
    case 'danger':
      return 'bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 focus:ring-red-500 shadow-lg shadow-red-500/30'
    default:
      return 'bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 focus:ring-blue-500 shadow-lg shadow-blue-500/30'
  }
}
</script>

<style scoped>
@keyframes bounce-gentle {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-5px);
  }
}

.animate-bounce-gentle {
  animation: bounce-gentle 2s ease-in-out infinite;
}
</style>