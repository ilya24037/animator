<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{ form: Record<string, any>, errors?: Record<string, any> }>()

// Пример: ref для плавного scroll/focus
const discountInput = ref(null)
defineExpose({ discountInput })
</script>

<template>
  <section class="space-y-8">
    <h2 class="text-2xl font-bold mb-4">Акции</h2>

    <!-- Скидка новым клиентам -->
    <div>
      <label class="block font-medium mb-2" for="discount">
        Скидка новым клиентам <span class="text-gray-400">(в %)</span>
      </label>

      <input
        ref="discountInput"
        id="discount"
        type="number"
        v-model.number="props.form.discount"
        min="0" max="100"
        placeholder="Напр. 10"
        :class="[
          'w-full border rounded-xl px-4 py-3',
          props.errors?.discount ? 'border-red-500' : 'border-gray-200'
        ]"
      />
      <span v-if="props.errors?.discount" class="text-xs text-red-500">{{ props.errors.discount }}</span>
    </div>

    <!-- Подарок -->
    <div>
      <label class="block font-medium mb-2" for="gift">Подарок</label>

      <input
        id="gift"
        type="text"
        v-model.trim="props.form.gift"
        placeholder="Что и на каких условиях дарите"
        class="w-full border rounded-xl px-4 py-3"
      />

      <p class="text-sm text-gray-500 mt-1">
        Можно не заполнять, если акции нет
      </p>
    </div>
  </section>
</template>
