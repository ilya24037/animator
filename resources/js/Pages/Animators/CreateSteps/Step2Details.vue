<script setup lang="ts">
import { ref } from 'vue'

/* пропсы от родителя */
const props = defineProps<{
  form: { title: string }
  errors: Record<string, string>
}>()

/* ref для прямого фокуса */
const titleInput = ref<HTMLInputElement | null>(null)
defineExpose({ titleInput })
</script>

<template>
  <section>
    <h2 class="text-2xl font-bold mb-4">Подробности</h2>

    <label class="block mb-1 font-semibold">Название объявления</label>
    <input
      ref="titleInput"
      v-model="props.form.title"
      v-error-path="'details.title'"
      :class="[
        'w-full border rounded-xl px-4 py-3 mb-1',
        props.errors?.['details.title'] ? 'border-red-500' : 'border-gray-200'
      ]"
      placeholder="Напр.: Классический массаж"
    />

    <span
      v-if="props.errors?.['details.title']"
      class="text-xs text-red-500"
    >
      {{ props.errors['details.title'] }}
    </span>
  </section>
</template>
