<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{ form: Record<string, any>, errors?: Record<string, any> }>()

// ref для плавного scroll/focus
const descriptionInput = ref(null)
defineExpose({ descriptionInput })
</script>

<template>
  <section>
    <h2 class="text-xl font-bold mb-4">Описание</h2>
    <label class="block mb-1 font-semibold" for="description">Описание услуги</label>
    <textarea
      ref="descriptionInput"
      id="description"
      v-model="props.form.description"
      rows="6"
      placeholder="Опишите услугу, опыт, сертификаты, особенности работы и др."
      :class="[
        'w-full border rounded-xl px-4 py-3 mb-1 resize-vertical',
        props.errors?.['details.description'] ? 'border-red-500' : 'border-gray-200'
      ]"
    />
    <span v-if="props.errors?.['details.description']" class="text-xs text-red-500">
      {{ props.errors['details.description'] }}
    </span>
    <p class="text-xs text-gray-500 mt-2">
      Не указывайте здесь телефон и почту — для них есть отдельные поля
    </p>
  </section>
</template>