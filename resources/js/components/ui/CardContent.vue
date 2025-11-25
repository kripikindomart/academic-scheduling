<template>
  <div :class="contentClasses">
    <slot />
  </div>
</template>

<script setup>
import { computed } from 'vue';

const contentClasses = computed(() => {
  return 'p-6 pt-0';
});
</script>