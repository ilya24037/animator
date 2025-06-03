<template>
  <input
    type="checkbox"
    :id="id"
    :checked="modelValue"
    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
    :disabled="disabled"
    @change="$emit('update:modelValue', $event.target.checked)"
  />
</template>

<script setup lang="ts">
import { defineProps, defineEmits } from 'vue'

const props = defineProps<{
  id: string
  modelValue: boolean
  disabled?: boolean
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: boolean): void
}>()
// Используется как <Checkbox id="remember" v-model="form.remember" />
</script>

<style scoped>
.rounded {
  border-radius: 0.25rem;
}
.border-gray-300 {
  border-color: #d2d6dc;
}
.text-indigo-600 {
  color: #4f46e5;
}
.shadow-sm {
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}
.focus\:border-indigo-300:focus {
  border-color: #a5b4fc;
}
.focus\:ring:focus {
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.5);
}
.focus\:ring-indigo-200:focus {
  --tw-ring-color: #c7d2fe;
}
.focus\:ring-opacity-50:focus {
  --tw-ring-opacity: 0.5;
}
</style>
