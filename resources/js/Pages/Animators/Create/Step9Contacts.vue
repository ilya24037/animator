<script setup lang="ts">
import { ref, computed } from 'vue'

const props = defineProps<{ form: Record<string, any>, errors?: Record<string, any> }>()

const phoneInput = ref(null)
defineExpose({ phoneInput })

const f = props.form

// Безопасные computed (на случай отсутствия ключа)
const contactPhone = computed({
  get: () => f.phone ?? '',
  set: v => (f.phone = v)
})
const contactWays = computed({
  get: () => (Array.isArray(f.contactWays) ? f.contactWays : (f.contactWays = ['call'])),
  set: v => (f.contactWays = v)
})
</script>

<template>
  <section class="space-y-6">
    <h2 class="text-2xl font-bold">Контакты</h2>

    <!-- телефон -->
    <div>
      <label class="block font-semibold mb-1">Телефон</label>
      <input
        ref="phoneInput"
        v-model="contactPhone"
        type="tel"
        placeholder="8 ___ ___-__-__"
        :class="[
          'w-full rounded-xl border px-4 py-3',
          props.errors?.['contacts.phone'] ? 'border-red-500' : 'border-gray-200'
        ]"
      />
      <span v-if="props.errors?.['contacts.phone']" class="text-xs text-red-500">{{ props.errors['contacts.phone'] }}</span>
      <p class="text-xs text-gray-500 mt-1">
        Чтобы ваш настоящий номер не попал в базы мошенников, мы показываем подменный.
      </p>
    </div>

    <!-- способы связи -->
    <div>
      <label class="block font-semibold mb-2">Способ связи</label>
      <div class="space-y-2">
        <label class="flex items-center gap-2">
          <input
            v-model="contactWays"
            type="radio"
            value="any"
            class="accent-blue-600"
          /> Звонки и&nbsp;сообщения
        </label>
        <label class="flex items-center gap-2">
          <input
            v-model="contactWays"
            type="radio"
            value="phone"
            class="accent-blue-600"
          /> Только звонки
        </label>
        <label class="flex items-center gap-2">
          <input
            v-model="contactWays"
            type="radio"
            value="message"
            class="accent-blue-600"
          /> Только сообщения
        </label>
      </div>
    </div>
  </section>
</template>