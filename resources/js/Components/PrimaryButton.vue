<!-- resources/js/Components/PrimaryButton.vue -->
<template>
  <button
    type="submit"
    :class="[
      disabled ? 'opacity-25 cursor-not-allowed' : '',
      'inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring focus:ring-indigo-200 active:bg-indigo-600 transition ease-in-out duration-150'
    ]"
    :disabled="disabled"
  >
    <slot />
  </button>
</template>

<script setup lang="ts">
import { defineProps } from 'vue'

const props = defineProps<{
  disabled?: boolean
}>()
</script>

<style scoped>
.inline-flex {
  display: inline-flex;
}
.items-center {
  align-items: center;
}
.px-4 {
  padding-left: 1rem;
  padding-right: 1rem;
}
.py-2 {
  padding-top: 0.5rem;
  padding-bottom: 0.5rem;
}
.bg-indigo-600 {
  background-color: #4f46e5;
}
.border {
  border-width: 1px;
}
.border-transparent {
  border-color: transparent;
}
.rounded-md {
  border-radius: 0.375rem;
}
.font-semibold {
  font-weight: 600;
}
.text-xs {
  font-size: 0.75rem;
}
.text-white {
  color: #ffffff;
}
.uppercase {
  text-transform: uppercase;
}
.tracking-widest {
  letter-spacing: 0.1em;
}
.hover\:bg-indigo-500:hover {
  background-color: #6366f1;
}
.focus\:outline-none:focus {
  outline: 0;
}
.focus\:ring:focus {
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.5);
}
.focus\:ring-indigo-200:focus {
  --tw-ring-color: #c7d2fe;
}
.active\:bg-indigo-600:active {
  background-color: #4f46e5;
}
.transition {
  transition-property: all;
}
.ease-in-out {
  transition-timing-function: cubic-bezier(0.4,0,0.2,1);
}
.duration-150 {
  transition-duration: 150ms;
}
.opacity-25 {
  opacity: 0.25;
}
.cursor-not-allowed {
  cursor: not-allowed;
}
</style>
