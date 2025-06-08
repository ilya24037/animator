<template>
  <div class="absolute top-4 right-4 text-sm">
    <transition name="fade" mode="out-in">
      <!-- Сохранение -->
      <div v-if="saving" key="saving" class="flex items-center gap-2 text-gray-500">
        <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" />
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
        </svg>
        <span>Сохранение...</span>
      </div>
      
      <!-- Сохранено -->
      <div v-else-if="lastSaved && !error" key="saved" class="flex items-center gap-2 text-green-600">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
        <span>Сохранено {{ formatTime(lastSaved) }}</span>
      </div>
      
      <!-- Ошибка -->
      <div v-else-if="error" key="error" class="flex items-center gap-2 text-red-600">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
        </svg>
        <span>{{ error }}</span>
      </div>
      
      <!-- Начальное состояние -->
      <div v-else key="initial" class="text-gray-400">
        Черновик сохраняется автоматически
      </div>
    </transition>
  </div>
</template>

<script setup>
const props = defineProps({
  saving: Boolean,
  lastSaved: Date,
  error: String
})

function formatTime(date) {
  if (!date) return ''
  
  const now = new Date()
  const diff = Math.floor((now - date) / 1000) // разница в секундах
  
  if (diff < 10) return 'только что'
  if (diff < 60) return `${diff} сек. назад`
  if (diff < 3600) return `${Math.floor(diff / 60)} мин. назад`
  
  return date.toLocaleTimeString('ru-RU', { 
    hour: '2-digit', 
    minute: '2-digit' 
  })
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>