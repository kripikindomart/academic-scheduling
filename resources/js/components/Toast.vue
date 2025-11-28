<template>
  <transition
    enter-active-class="transition ease-out duration-300"
    enter-from-class="opacity-0 translate-y-2"
    enter-to-class="opacity-100 translate-y-0"
    leave-active-class="transition ease-in duration-200"
    leave-from-class="opacity-100 translate-y-0"
    leave-to-class="opacity-0 translate-y-2"
  >
    <div
      v-if="show"
      :class="[
        'fixed top-4 right-4 z-50 max-w-sm w-full rounded-lg shadow-lg p-4',
        'transform transition-all duration-300',
        getToastClasses()
      ]"
    >
      <div class="flex items-start">
        <div class="flex-shrink-0">
          <div :class="getIconClasses()" class="h-6 w-6 flex items-center justify-center">
            <svg v-if="type === 'success'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <svg v-else-if="type === 'error'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <svg v-else-if="type === 'warning'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <svg v-else class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
        <div class="ml-3 w-0 flex-1">
          <p class="text-sm font-medium" :class="getTextClasses()">
            {{ title }}
          </p>
          <p v-if="description" class="mt-1 text-sm" :class="getDescriptionClasses()">
            {{ description }}
          </p>
        </div>
        <div class="ml-auto pl-3">
          <div class="-mx-1.5 -my-1.5">
            <button
              @click="hideToast"
              :class="getCloseButtonClasses()"
              class="inline-flex rounded-md p-1.5 focus:outline-none focus:ring-2 focus:ring-offset-2"
            >
              <span class="sr-only">Dismiss</span>
              <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  description: {
    type: String,
    default: ''
  },
  type: {
    type: String,
    default: 'info' // success, error, warning, info
  },
  duration: {
    type: Number,
    default: 5000
  },
  show: {
    type: Boolean,
    default: true
  }
})

const emit = defineEmits(['close'])


const getToastClasses = () => {
  switch (props.type) {
    case 'success':
      return 'bg-green-50 border-green-200 text-green-800'
    case 'error':
      return 'bg-red-50 border-red-200 text-red-800'
    case 'warning':
      return 'bg-yellow-50 border-yellow-200 text-yellow-800'
    default:
      return 'bg-blue-50 border-blue-200 text-blue-800'
  }
}

const getIconClasses = () => {
  switch (props.type) {
    case 'success':
      return 'text-green-400'
    case 'error':
      return 'text-red-400'
    case 'warning':
      return 'text-yellow-400'
    default:
      return 'text-blue-400'
  }
}

const getTextClasses = () => {
  switch (props.type) {
    case 'success':
      return 'text-green-800'
    case 'error':
      return 'text-red-800'
    case 'warning':
      return 'text-yellow-800'
    default:
      return 'text-blue-800'
  }
}

const getDescriptionClasses = () => {
  switch (props.type) {
    case 'success':
      return 'text-green-700'
    case 'error':
      return 'text-red-700'
    case 'warning':
      return 'text-yellow-700'
    default:
      return 'text-blue-700'
  }
}

const getCloseButtonClasses = () => {
  switch (props.type) {
    case 'success':
      return 'text-green-400 hover:bg-green-100 focus:ring-green-500 focus:ring-offset-green-50'
    case 'error':
      return 'text-red-400 hover:bg-red-100 focus:ring-red-500 focus:ring-offset-red-50'
    case 'warning':
      return 'text-yellow-400 hover:bg-yellow-100 focus:ring-yellow-500 focus:ring-offset-yellow-50'
    default:
      return 'text-blue-400 hover:bg-blue-100 focus:ring-blue-500 focus:ring-offset-blue-50'
  }
}

const hideToast = () => {
  emit('close')
}

// Auto hide after duration when component is shown
watch(() => props.show, (newValue) => {
  if (newValue && props.duration > 0) {
    setTimeout(() => {
      hideToast()
    }, props.duration)
  }
})
</script>