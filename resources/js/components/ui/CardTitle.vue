<template>
  <h3 :class="titleClasses">
    <slot />
  </h3>
</template>

<script setup>
import { computed } from 'vue';

const titleClasses = computed(() => {
  return 'text-2xl font-semibold leading-none tracking-tight';
});
</script>