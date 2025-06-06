<template>
  <div class="fixed top-4 right-4 z-50 space-y-2">
    <TransitionGroup name="toast">
      <div
        v-for="toast in toasts"
        :key="toast.id"
        :class="[
          'px-4 py-3 rounded-lg shadow-lg max-w-sm',
          toast.type === 'success' ? 'bg-green-500 text-white' : '',
          toast.type === 'error' ? 'bg-red-500 text-white' : '',
          toast.type === 'info' ? 'bg-blue-500 text-white' : ''
        ]"
      >
        <div class="flex items-center">
          <div class="flex-1">{{ toast.message }}</div>
          <button
            @click="removeToast(toast.id)"
            class="ml-4 text-white hover:text-gray-200"
          >
            ×
          </button>
        </div>
      </div>
    </TransitionGroup>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'

const toasts = ref([])
const page = usePage()

// Следим за flash сообщениями
watch(() => page.props.flash, (flash) => {
  if (flash?.success) {
    addToast('success', flash.success)
  }
  if (flash?.error) {
    addToast('error', flash.error)
  }
  if (flash?.info) {
    addToast('info', flash.info)
  }
}, { deep: true })

function addToast(type, message) {
  const id = Date.now()
  toasts.value.push({ id, type, message })
  
  // Автоматически удаляем через 5 секунд
  setTimeout(() => {
    removeToast(id)
  }, 5000)
}

function removeToast(id) {
  const index = toasts.value.findIndex(t => t.id === id)
  if (index > -1) {
    toasts.value.splice(index, 1)
  }
}

// Экспортируем функцию для использования в других компонентах
defineExpose({ addToast })
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}

.toast-enter-from {
  transform: translateX(100%);
  opacity: 0;
}

.toast-leave-to {
  transform: translateX(100%);
  opacity: 0;
}
</style>