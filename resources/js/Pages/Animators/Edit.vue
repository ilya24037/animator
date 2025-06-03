<script setup>
import { useForm } from '@inertiajs/vue3'
import { useLocationStore } from '@/Stores/useLocationStore'

const props = defineProps({ animator: Object })
const store = useLocationStore()

const form = useForm({
  name: props.animator.name,
  city: store.city || props.animator.city,
  price: props.animator.price,
  type: props.animator.type,
  is_online: props.animator.is_online,
  is_verified: props.animator.is_verified,
  age: props.animator.age,
  height: props.animator.height,
  weight: props.animator.weight,
})

const submit = () => {
  form.put(route('animators.update', props.animator.id))
}
</script>

<template>
  <div class="max-w-xl mx-auto space-y-4">
    <h1 class="text-2xl font-bold">Редактировать анкету</h1>

    <input v-model="form.name" placeholder="Имя" class="input" />
    <input v-model="form.city" placeholder="Город" class="input" />
    <input type="number" v-model="form.price" placeholder="Цена" class="input" />

    <select v-model="form.type" class="input">
      <option value="private">Частный</option>
      <option value="company">Компания</option>
    </select>

    <label><input type="checkbox" v-model="form.is_online" /> Онлайн</label>
    <label><input type="checkbox" v-model="form.is_verified" /> Проверен</label>

    <input type="number" v-model="form.age" placeholder="Возраст" class="input" />
    <input type="number" v-model="form.height" placeholder="Рост" class="input" />
    <input type="number" v-model="form.weight" placeholder="Вес" class="input" />

    <button @click="submit" class="btn">Сохранить</button>
  </div>
</template>
