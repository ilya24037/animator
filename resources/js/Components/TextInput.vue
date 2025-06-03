<template>
  <input
    :id="id"
    :type="type"
    class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"
    :value="modelValue"
    @input="$emit('update:modelValue', $event.target.value)"
    v-bind="$attrs"
    :disabled="disabled"
  />
</template>

<script setup lang="ts">
import { defineProps, defineEmits } from 'vue'

const props = defineProps<{
  id: string
  modelValue: string
  type?: string
  disabled?: boolean
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void
}>()

// По умолчанию устанавливаем type="text"
if (!props.type) {
  props.type = 'text'
}
</script>

<style scoped>
.border-gray-300 {
  border-color: #d2d6dc;
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
.rounded-md {
  border-radius: 0.375rem;
}
.shadow-sm {
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}
.w-full {
  width: 100%;
}
</style>
