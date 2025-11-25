<template>
  <div :class="headerClasses">
    <slot />
  </div>
</template>

<script setup>
import { computed } from 'vue';

const headerClasses = computed(() => {
  return 'flex flex-col space-y-1.5 p-6';
});
</script>