<script setup>
const props = defineProps({
  modelValue: String,
  label: String,
  name: String,
})
const emit = defineEmits(['update:modelValue'])
</script>

<template>
  <div>
    <label :for="name" class="block mb-2 font-medium">{{ label }}</label>
    <textarea
      :name="name"
      :id="name"
      rows="4"
      class="w-full border rounded-xl px-4 py-2"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
    />
  </div>
</template>
