<template>
  <div class="min-h-screen flex flex-col bg-[#f5f7fa] text-gray-800">
    <Container>
      <!-- Белая шапка с закруглением снизу -->
      <div class="sticky top-0 z-50 bg-white shadow rounded-b-2xl">
        <Navbar />
      </div>
      
      <!-- Белый фон контента, совпадает по ширине с шапкой -->
      <main class="pt-6 pb-8">
        <slot />
      </main>
      
      <footer class="bg-gray-200 py-6 text-sm text-gray-600 text-center rounded-t-xl mt-12">
        © {{ new Date().getFullYear() }} Animatorr — Все права защищены
      </footer>
    </Container>
    
    <!-- Компонент уведомлений -->
    <ToastNotifications ref="toastRef" />
  </div>
</template>

<script setup>
import { ref, provide } from 'vue'
import Navbar from '@/Components/Navbar.vue'
import Container from '@/Components/Container.vue'
import ToastNotifications from '@/Components/ToastNotifications.vue'

const toastRef = ref(null)

// Предоставляем функцию показа уведомлений всем дочерним компонентам
provide('showToast', (type, message) => {
  toastRef.value?.addToast(type, message)
})
</script>