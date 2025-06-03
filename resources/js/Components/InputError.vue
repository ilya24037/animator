<!-- resources/js/Components/InputError.vue -->
<template>
  <p v-if="message" class="mt-2 text-sm text-red-600">
    {{ message }}
  </p>
</template>

<script setup lang="ts">
import { defineProps } from 'vue'

const props = defineProps<{
  message?: string
}>()
// передаём сюда строку с ошибкой, например из Inertia: page.props.errors.email
</script>

<style scoped>
.text-red-600 {
  color: #e3342f;
}
</style>
