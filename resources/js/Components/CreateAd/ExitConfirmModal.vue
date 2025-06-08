<template>
  <transition name="modal">
    <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
      <!-- Затемнение -->
      <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="$emit('cancel')"></div>
      
      <!-- Модальное окно -->
      <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6 transform transition-all">
          <!-- Иконка -->
          <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-yellow-100 mb-4">
            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          </div>
          
          <!-- Контент -->
          <div class="text-center">
            <h3 class="text-lg font-medium text-gray-900 mb-2">
              Сохранить черновик?
            </h3>
            <p class="text-sm text-gray-500 mb-6">
              Вы можете сохранить объявление как черновик и вернуться к редактированию позже
            </p>
          </div>
          
          <!-- Кнопки -->
          <div class="flex gap-3">
            <button
              @click="$emit('cancel')"
              class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition"
            >
              Продолжить редактирование
            </button>
            <button
              @click="$emit('confirm')"
              class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
            >
              Сохранить и выйти
            </button>
          </div>
          
          <!-- Кнопка выхода без сохранения -->
          <button
            @click="exitWithoutSave"
            class="w-full mt-3 text-sm text-gray-500 hover:text-red-600 transition"
          >
            Выйти без сохранения
          </button>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { router } from '@inertiajs/vue3'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['confirm', 'cancel'])

function exitWithoutSave() {
  router.visit('/profile/items/draft/all')
}
</script>

<style scoped>
.modal-enter-active, .modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-active .relative {
  transition: all 0.3s ease;
}

.modal-enter-from {
  opacity: 0;
}

.modal-enter-from .relative {
  transform: scale(0.9);
}

.modal-leave-to {
  opacity: 0;
}
</style>