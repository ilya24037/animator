<script setup>
defineProps(['item'])
</script>

<template>
  <div class="border rounded-lg p-4 mb-4 flex justify-between items-center bg-white shadow-sm hover:shadow-md transition">
    <div class="flex items-center gap-4">
      <!-- Превью (если появится картинка) -->
      <div class="w-14 h-14 bg-gray-100 rounded-lg flex items-center justify-center text-gray-300 text-2xl shrink-0">
        <span v-if="item.preview_url">
          <img :src="item.preview_url" alt="Превью" class="w-full h-full object-cover rounded-lg" />
        </span>
        <span v-else>?</span>
      </div>
      <div>
        <div class="font-semibold text-lg mb-1">{{ item.title }}</div>
        <div v-if="item.price" class="font-medium text-blue-700 mb-1">Цена: {{ item.price }} ₽</div>
        <div class="text-gray-500 text-xs mb-1">
          Статус: 
          <span v-if="item.status === 'draft'">Черновик</span>
          <span v-else-if="item.status === 'published'">Активно</span>
          <span v-else-if="item.status === 'inactive'">Отклонено</span>
          <span v-else-if="item.status === 'archive'">В архиве</span>
          <span v-else>{{ item.status }}</span>
        </div>
        <div v-if="item.created_at" class="text-gray-400 text-xs">
          Добавлено: {{ item.created_at.substring(0, 10) }}
        </div>
      </div>
    </div>
    <div class="flex flex-col gap-2 items-end">
      <button class="btn btn-sm">Редактировать</button>
      <button class="btn btn-sm bg-red-600 hover:bg-red-700">Удалить</button>
    </div>
  </div>
</template>

<style scoped>
.btn {
  @apply px-4 py-2 rounded-xl font-semibold text-white bg-black transition;
}
.btn-sm {
  @apply px-3 py-1 text-sm;
}
</style>
