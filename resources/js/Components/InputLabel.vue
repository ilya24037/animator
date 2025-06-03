<!-- resources/js/Components/InputLabel.vue -->
<template>
  <label :for="forId" class="block font-medium text-sm text-gray-700">
    <slot />
  </label>
</template>

<script setup lang="ts">
import { defineProps } from 'vue'

const props = defineProps<{
  forId: string
}>()
// Используйте так: <InputLabel forId="email">Email</InputLabel>
</script>

<style scoped>
.block {
  display: block;
}
.font-medium {
  font-weight: 500;
}
.text-sm {
  font-size: 0.875rem;
}
.text-gray-700 {
  color: #4a5568;
}
</style>
