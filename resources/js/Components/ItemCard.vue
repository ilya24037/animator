<script setup lang="ts">
const props = defineProps({
  item: { type: Object, required: true }
})
</script>

<template>
  <div class="flex items-start gap-4 py-4 border-b last:border-none">
    <div class="w-24 h-18 bg-gray-100 rounded-lg flex-shrink-0 overflow-hidden">
      <img v-if="item.imageUrl" :src="item.imageUrl" class="object-cover w-full h-full" />
      <div v-else class="w-full h-full flex items-center justify-center text-xs text-gray-400">Нет фото</div>
    </div>
    <div class="flex-1 min-w-0">
      <h3 class="font-medium text-blue-600 truncate">
        <a :href="item.link || '#'">{{ item.title }}</a>
      </h3>
      <p class="text-sm text-gray-800 mt-1">
        {{ item.price || 'Цена договорная' }}
      </p>
      <p class="text-xs text-gray-500 mt-1">
        {{ item.lifetime || item.status || '' }}
      </p>
    </div>
    <div class="flex flex-col items-end gap-2">
      <a v-if="item.editUrl" :href="item.editUrl"
         class="px-4 py-1.5 text-sm border rounded-lg hover:bg-gray-50">Редактировать</a>
      <button class="px-2 py-1 hover:bg-gray-50 rounded-lg">
        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" stroke-width="2"
             viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
        </svg>
      </button>
    </div>
  </div>
</template>
