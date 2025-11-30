<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition-all duration-300"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-all duration-300"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="show"
        class="fixed inset-0 z-[9999] overflow-y-auto"
        @click="handleBackdropClick"
      >
        <!-- Backdrop -->
        <div class="flex min-h-full items-center justify-center p-4">
          <div class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>

          <!-- Dialog -->
          <Transition
            enter-active-class="transition-all duration-300"
            enter-from-class="opacity-0 scale-95 translate-y-4"
            enter-to-class="opacity-100 scale-100 translate-y-0"
            leave-active-class="transition-all duration-300"
            leave-from-class="opacity-100 scale-100 translate-y-0"
            leave-to-class="opacity-0 scale-95 translate-y-4"
          >
            <div
              v-if="show"
              class="relative mx-auto max-w-md w-full transform rounded-2xl shadow-2xl transition-all"
              @click.stop
            >
              <!-- Gradient Background Card -->
              <div class="relative overflow-hidden rounded-2xl bg-white shadow-xl">
                <!-- Header with Gradient -->
                <div class="relative bg-gradient-to-r from-red-500 to-pink-600 px-6 py-6">
                  <!-- Icon -->
                  <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-white/20 backdrop-blur-sm">
                    <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </div>

                  <!-- Title -->
                  <h3 class="mt-4 text-center text-xl font-bold text-white">
                    {{ title }}
                  </h3>
                </div>

                <!-- Body -->
                <div class="px-6 py-6">
                  <div class="text-center">
                    <p class="text-gray-600">
                      {{ message }}
                    </p>

                    <!-- Additional Details -->
                    <div v-if="details" class="mt-4 rounded-lg bg-red-50 p-4">
                      <div class="flex items-center justify-center space-x-2">
                        <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm font-medium text-red-800">{{ details }}</p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Actions -->
                <div class="bg-gray-50 px-6 py-4">
                  <div class="flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-3 sm:space-x-reverse space-y-3 space-y-reverse sm:space-y-0">
                    <!-- Cancel Button -->
                    <button
                      type="button"
                      class="inline-flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-3 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200"
                      @click="handleCancel"
                    >
                      <span class="flex items-center">
                        <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Batal
                      </span>
                    </button>

                    <!-- Confirm Delete Button -->
                    <button
                      type="button"
                      class="inline-flex w-full justify-center rounded-lg border border-transparent bg-gradient-to-r from-red-600 to-pink-600 px-4 py-3 text-base font-medium text-white shadow-lg hover:from-red-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105"
                      @click="handleConfirm"
                    >
                      <span class="flex items-center">
                        <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Hapus
                      </span>
                    </button>
                  </div>
                </div>
              </div>

              <!-- Loading Overlay -->
              <div
                v-if="loading"
                class="absolute inset-0 flex items-center justify-center rounded-2xl bg-white/80 backdrop-blur-sm"
              >
                <div class="flex items-center space-x-3">
                  <div class="h-6 w-6 animate-spin rounded-full border-2 border-red-600 border-t-transparent"></div>
                  <span class="text-gray-700">Menghapus...</span>
                </div>
              </div>
            </div>
          </Transition>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    default: 'Konfirmasi Hapus'
  },
  message: {
    type: String,
    default: 'Apakah Anda yakin ingin menghapus data ini?'
  },
  details: {
    type: String,
    default: ''
  },
  loading: {
    type: Boolean,
    default: false
  },
  closeOnBackdrop: {
    type: Boolean,
    default: true
  }
})

const emit = defineEmits(['close', 'confirm'])

const handleBackdropClick = () => {
  if (props.closeOnBackdrop && !props.loading) {
    handleCancel()
  }
}

const handleCancel = () => {
  if (!props.loading) {
    emit('close')
  }
}

const handleConfirm = () => {
  if (!props.loading) {
    emit('confirm')
  }
}

// Handle escape key
const handleEscapeKey = (event) => {
  if (event.key === 'Escape' && props.show && !props.loading) {
    handleCancel()
  }
}

onMounted(() => {
  document.addEventListener('keydown', handleEscapeKey)
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleEscapeKey)
})

// Prevent body scroll when modal is open
watch(() => props.show, (newShow) => {
  if (newShow) {
    document.body.style.overflow = 'hidden'
  } else {
    document.body.style.overflow = ''
  }
}, { immediate: true })
</script>

<style scoped>
/* Custom animations */
@keyframes modalSlideIn {
  from {
    opacity: 0;
    transform: scale(0.95) translateY(20px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

@keyframes modalSlideOut {
  from {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
  to {
    opacity: 0;
    transform: scale(0.95) translateY(20px);
  }
}

/* Ensure proper z-index stacking */
.z-\[9999\] {
  z-index: 9999;
}
</style>