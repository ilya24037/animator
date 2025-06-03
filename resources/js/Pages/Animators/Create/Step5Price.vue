<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{ form: Record<string, any>, errors?: Record<string, any> }>()

// ref для плавного scroll/focus
const priceInput = ref(null)
defineExpose({ priceInput })
</script>

<template>
  <section>
    <h2 class="text-xl font-bold mb-4">Стоимость основной услуги</h2>

    <div class="flex flex-col md:flex-row md:items-center gap-4">
      <!-- поле "от … ₽" -->
      <div class="flex items-center gap-2 w-full md:w-2/5">
        <span class="text-gray-400">от</span>
        <input
          ref="priceInput"
          v-model="props.form.value"
          type="number"
          min="0"
          placeholder="5000"
          :class="[
            'flex-1 rounded-xl border px-4 py-2 focus:ring-2 focus:ring-blue-400',
            props.errors?.['price.value'] ? 'border-red-500' : 'border-gray-200'
          ]"
        />
        <span class="text-gray-500">₽</span>
      </div>
      <!-- селект единицы измерения -->
      <select
        v-model="props.form.unit"
        class="rounded-xl border px-4 py-2 w-full md:w-2/5"
      >
        <option value="за час">за час</option>
        <option value="за услугу">за услугу</option>
        <option value="за день">за день</option>
        <option value="за месяц">за месяц</option>
        <option value="за единицу">за единицу</option>
        <option value="за минуту">за минуту</option>
      </select>
    </div>

    <!-- Текст ошибки -->
    <span v-if="props.errors?.['price.value']" class="text-xs text-red-500">{{ props.errors['price.value'] }}</span>

    <label class="flex items-center gap-2 text-sm mt-4">
      <input type="checkbox" v-model="props.form.isBasePrice" class="accent-blue-600" />
      это начальная цена
    </label>

    <p class="text-xs text-gray-500 mt-1">
      Заказчик увидит эту цену рядом с названием объявления.
    </p>
  </section>
</template>