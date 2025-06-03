<script setup lang="ts">
import { ref, computed } from 'vue'

const props = defineProps<{ form: Record<string, any>, errors?: Record<string, any> }>()

// ref для плавного scroll/focus
const cityInput = ref(null)
const addressInput = ref(null)
defineExpose({ cityInput, addressInput })

const f = props.form

/** Безопасный computed для города (на случай отсутствия ключа) */
const city = computed({
  get: () => f.city ?? '',
  set: val => (f.city = val)
})
/** Безопасный computed для адреса */
const address = computed({
  get: () => f.address ?? '',
  set: val => (f.address = val)
})
</script>

<template>
  <section>
    <h2 class="text-2xl font-bold mb-4">География</h2>

    <!-- Город -->
    <div>
      <label class="block font-semibold mb-1">Город</label>
      <input
        ref="cityInput"
        v-model="city"
        type="text"
        placeholder="Ваш город"
        :class="[
          'w-full rounded-xl border px-4 py-3 mb-2',
          props.errors?.['geo.city'] ? 'border-red-500' : 'border-gray-200'
        ]"
      />
      <span v-if="props.errors?.['geo.city']" class="text-xs text-red-500">{{ props.errors['geo.city'] }}</span>
    </div>

    <!-- Адрес -->
    <div>
      <label class="font-semibold block mb-1">Адрес</label>
      <input
        ref="addressInput"
        v-model="address"
        type="text"
        placeholder="Начните вводить адрес"
        :class="[
          'w-full rounded-xl border px-4 py-3',
          props.errors?.['geo.address'] ? 'border-red-500' : 'border-gray-200'
        ]"
      />
      <span v-if="props.errors?.['geo.address']" class="text-xs text-red-500">{{ props.errors['geo.address'] }}</span>
      <p class="text-xs text-gray-500 mt-1">
        Клиенты выбирают исполнителя по&nbsp;точному адресу, когда ищут поблизости.
      </p>
    </div>

    <!-- Куда выезжаете -->
    <div class="mt-6">
      <label class="font-semibold block mb-2">Куда выезжаете</label>
      <div class="space-y-2">
        <label class="flex items-center gap-3">
          <input
            v-model="f.visitType"
            type="radio"
            value="no_visit"
            class="accent-blue-600"
          /> Не выезжаю
        </label>
        <label class="flex items-center gap-3">
          <input
            v-model="f.visitType"
            type="radio"
            value="all_city"
            class="accent-blue-600"
          /> По всему городу
        </label>
        <label class="flex items-center gap-3">
          <input
            v-model="f.visitType"
            type="radio"
            value="zones"
            class="accent-blue-600"
          /> В выбранные зоны
        </label>
      </div>
    </div>

    <!-- Мини-карта-заглушка -->
    <div class="w-full h-48 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 mt-6">
      Здесь будет карта (Yandex / Leaflet)
    </div>
  </section>
</template>