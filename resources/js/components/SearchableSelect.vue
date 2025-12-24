<template>
  <div class="relative" ref="containerRef">
    <label v-if="label" class="block text-sm font-medium text-gray-700 mb-1">{{ label }}</label>
    <div class="relative">
      <input
        type="text"
        v-model="searchQuery"
        @focus="open = true"
        @click="open = true"
        :placeholder="placeholder"
        :disabled="disabled"
        class="w-full rounded-md border border-gray-300 bg-white py-2 pl-3 pr-10 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 sm:text-sm disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed"
        :class="{'border-red-500': error}"
      />
      <button 
        v-if="searchQuery && !disabled && !open" 
        @click="clear" 
        type="button"
        class="absolute inset-y-0 right-8 flex items-center pr-2 text-gray-400 hover:text-gray-600"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
      <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
        <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
          <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 8.879a1 1 0 011.414-1.414L10 12.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3z" clip-rule="evenodd" />
        </svg>
      </div>
    </div>

    <!-- Dropdown -->
    <div v-if="open" class="absolute z-50 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
      <template v-if="filteredOptions.length > 0">
          <div
            v-for="option in filteredOptions"
            :key="option.id"
            @click="select(option)"
            class="relative cursor-pointer select-none py-2 pl-3 pr-9 hover:bg-blue-50 text-gray-900"
            :class="{'bg-blue-50': modelValue === option.id}"
          >
            <span class="block truncate" :class="{'font-semibold': modelValue === option.id}">
                {{ option.name }}
            </span>
            <span v-if="modelValue === option.id" class="absolute inset-y-0 right-0 flex items-center pr-4 text-blue-600">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            </span>
          </div>
      </template>
      <div v-else class="py-2 pl-3 pr-9 text-gray-500 italic text-sm">
          {{ emptyMessage }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { onClickOutside } from '@vueuse/core';

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  options: {
    type: Array,
    default: () => []
  },
  label: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: 'Pilih...'
  },
  disabled: {
    type: Boolean,
    default: false
  },
  error: {
    type: Boolean,
    default: false
  },
  emptyMessage: {
    type: String,
    default: 'Tidak ada data ditemukan'
  }
});

const emit = defineEmits(['update:modelValue']);
const containerRef = ref(null);
const open = ref(false);
const searchQuery = ref('');

onClickOutside(containerRef, () => {
    open.value = false;
    // Revert to selected option name if closed without selection
    const selected = props.options.find(o => o.id === props.modelValue);
    if (selected) {
        searchQuery.value = selected.name;
    } else {
        searchQuery.value = '';
    }
});

const filteredOptions = computed(() => {
    const query = searchQuery.value.trim().toLowerCase();
    if (!query) return props.options;

    // If query matches the currently selected option's name, show all options
    // This allows the user to see the full list when they click/focus the input which has a value
    const selectedOption = props.options.find(o => o.id === props.modelValue);
    if (selectedOption && selectedOption.name.toLowerCase() === query) {
        return props.options;
    }
    
    return props.options.filter(option => 
        option.name.toLowerCase().includes(query)
    );
});

// Watch modelValue to update display text
watch(() => props.modelValue, (newVal) => {
    const selected = props.options.find(o => o.id === newVal);
    if (selected) {
        searchQuery.value = selected.name;
    } else {
        searchQuery.value = '';
    }
}, { immediate: true });

// Handle internal search query changes? No need.

const select = (option) => {
    emit('update:modelValue', option.id);
    searchQuery.value = option.name;
    open.value = false;
};

const clear = () => {
    emit('update:modelValue', '');
    searchQuery.value = '';
};

// When clicking input while closed, if it has value, maybe select text or show all?
// If we want to show all, we need a separate "filterQuery" vs "displayQuery".
// Simple workaround: If user focuses, show all?
// To show all, we need filteredOptions to allow empty query.
// But searchQuery has value.
// We can use a separate variable for "isFiltering".

// Let's stick to simple: Input filters.
// If user wants to see others, they must backspace. 
// A "Clear" X button helps. Added above.

</script>
