<script setup lang="ts">
import { ref } from 'vue'
import Multiselect from '@vueform/multiselect'
import '@vueform/multiselect/themes/default.css'

const props = defineProps<{ form: Record<string, any>, errors?: Record<string, any> }>()
const specializationInput = ref(null)
defineExpose({ specializationInput })
</script>

<template>
  <div class="space-y-12">

    <!-- 1. Специальность или сфера -->
    <section>
      <label class="block mb-1 font-semibold">Специальность или сфера</label>
      <Multiselect
        ref="specializationInput"
        v-model="props.form.specialization"
        :options="['Аппаратный массаж', 'Массаж', 'Солярий', 'Спа-процедуры']"
        placeholder="Выберите специальность"
        :class="['w-full', props.errors?.['workFormat.specialization'] ? 'border-red-500' : '']"
      />
      <span v-if="props.errors?.['workFormat.specialization']" class="text-xs text-red-500">
        {{ props.errors['workFormat.specialization'] }}
      </span>
    </section>

    <!-- 2. Ваши клиенты -->
    <section>
      <h3 class="font-semibold mb-2">Ваши клиенты</h3>
      <div class="space-y-2">
        <label class="flex items-center gap-2">
          <input
            type="checkbox"
            value="Женщины"
            v-model="props.form.clients"
            class="accent-blue-600"
          />
          Женщины
        </label>
        <label class="flex items-center gap-2">
          <input
            type="checkbox"
            value="Мужчины"
            v-model="props.form.clients"
            class="accent-blue-600"
          />
          Мужчины
        </label>
      </div>
      <span v-if="props.errors?.['workFormat.clients']" class="text-xs text-red-500">
        {{ props.errors['workFormat.clients'] }}
      </span>
    </section>

    <!-- 3. Где вы оказываете услуги -->
    <section>
      <h3 class="font-semibold mb-2">Где вы оказываете услуги</h3>
      <div class="space-y-2">
        <label class="flex items-center gap-2" v-for="place in [
          'У заказчика дома',
          'У себя дома',
          'В салоне',
          'В коворкинге',
          'В клинике'
        ]" :key="place">
          <input
            type="checkbox"
            :value="place"
            v-model="props.form.workFormats"
            class="accent-blue-600"
          />
          {{ place }}
        </label>
      </div>
      <span v-if="props.errors?.['workFormat.workFormats']" class="text-xs text-red-500">
        {{ props.errors['workFormat.workFormats'] }}
      </span>
    </section>

    <!-- 4. Формат работы -->
    <section>
      <h3 class="font-semibold mb-2">Формат работы</h3>
      <div class="space-y-3">
        <label class="flex items-start gap-3">
          <input
            type="radio"
            value="private"
            v-model="props.form.type"
            :class="['accent-blue-600 mt-1', props.errors?.['workFormat.type'] ? 'border-red-500 ring-2 ring-red-200' : '']"
          />
          <span>
            <strong>Частный мастер</strong><br />
            <small class="text-gray-500">Работаете в одиночку</small>
          </span>
        </label>
        <label class="flex items-start gap-3">
          <input
            type="radio"
            value="salon"
            v-model="props.form.type"
            :class="['accent-blue-600 mt-1', props.errors?.['workFormat.type'] ? 'border-red-500 ring-2 ring-red-200' : '']"
          />
          <span>
            <strong>Салон</strong><br />
            <small class="text-gray-500">Есть отдельное помещение и штат мастеров</small>
          </span>
        </label>
        <label class="flex items-start gap-3">
          <input
            type="radio"
            value="chain"
            v-model="props.form.type"
            :class="['accent-blue-600 mt-1', props.errors?.['workFormat.type'] ? 'border-red-500 ring-2 ring-red-200' : '']"
          />
          <span>
            <strong>Сеть салонов</strong><br />
            <small class="text-gray-500">Несколько филиалов с одной концепцией</small>
          </span>
        </label>
      </div>
      <span v-if="props.errors?.['workFormat.type']" class="text-xs text-red-500">
        {{ props.errors['workFormat.type'] }}
      </span>
    </section>

    <!-- 5. Кто оказывает услуги -->
    <section>
      <h3 class="font-semibold mb-2">Кто оказывает услуги</h3>
      <div class="space-y-2">
        <label class="flex items-center gap-2">
          <input
            type="checkbox"
            value="Женщина"
            v-model="props.form.serviceProviders"
            class="accent-blue-600"
          />
          Женщина
        </label>
        <label class="flex items-center gap-2">
          <input
            type="checkbox"
            value="Мужчина"
            v-model="props.form.serviceProviders"
            class="accent-blue-600"
          />
          Мужчина
        </label>
      </div>
      <span v-if="props.errors?.['workFormat.serviceProviders']" class="text-xs text-red-500">
        {{ props.errors['workFormat.serviceProviders'] }}
      </span>
    </section>

    <!-- 6. Опыт работы -->
    <section>
      <h3 class="font-semibold mb-2">Опыт работы</h3>
      <select
        v-model="props.form.experience"
        :class="['w-full rounded-lg border px-4 py-2', props.errors?.['workFormat.experience'] ? 'border-red-500' : 'border-gray-200']"
      >
        <option value="" disabled>Выберите из списка…</option>
        <option>Меньше года</option>
        <option>1–3 года</option>
        <option>4–7 лет</option>
        <option>8–10 лет</option>
        <option>Больше 10 лет</option>
      </select>
      <span v-if="props.errors?.['workFormat.experience']" class="text-xs text-red-500">
        {{ props.errors['workFormat.experience'] }}
      </span>
    </section>
  </div>
</template>

