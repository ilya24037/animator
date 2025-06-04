<script setup>
import { router } from '@inertiajs/vue3';
const props = defineProps({ item: Object });

function toEdit() {
  router.visit(`/items/${props.item.id}/edit`);
}
function publish() {
  router.patch(`/profile/items/${props.item.id}/status`, { status: 'waiting' });
}
function remove() {
  router.patch(`/profile/items/${props.item.id}/status`, { status: 'archived' });
}
</script>

<template>
  <div class="flex gap-4 p-4 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow">
    <img :src="item.preview_url" alt="" class="w-24 h-24 object-cover rounded-md" />
    <div class="flex-1">
      <div class="flex justify-between items-start">
        <h3 class="text-lg font-semibold">{{ item.title }}</h3>
        <span class="text-gray-400 text-sm">{{ item.price }} ₽</span>
      </div>
      <p class="text-gray-500 text-sm">{{ item.city }}</p>
      <div v-if="item.reason" class="mt-2 text-xs text-red-500">
        {{ item.reason }}
      </div>
      <div class="mt-3 flex gap-3 text-sm">
        <button @click="toEdit" class="text-blue-600 hover:underline">Изменить</button>
        <button v-if="item.status === 'draft'" @click="publish" class="text-green-600 hover:underline">Опубликовать</button>
        <button @click="remove" class="text-gray-400 hover:underline">Удалить</button>
      </div>
    </div>
  </div>
</template>