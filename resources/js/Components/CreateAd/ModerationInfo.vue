<template>
  <div class="bg-yellow-50 rounded-2xl p-6">
    <h3 class="font-semibold mb-3 flex items-center gap-2">
      <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
      </svg>
      Модерация объявлений
    </h3>
    
    <div class="space-y-2 text-sm text-yellow-800">
      <p>После публикации ваше объявление будет проверено модератором.</p>
      
      <div class="mt-3">
        <p class="font-medium mb-1">Что проверяется:</p>
        <ul class="space-y-1 ml-4">
          <li>• Соответствие правилам сервиса</li>
          <li>• Корректность фотографий</li>
          <li>• Достоверность информации</li>
          <li>• Отсутствие запрещенного контента</li>
        </ul>
      </div>
      
      <div class="mt-3 pt-3 border-t border-yellow-200">
        <p class="font-medium">Время проверки:</p>
        <p>Обычно от 15 минут до 2 часов в рабочее время</p>
      </div>
    </div>
  </div>
</template>

<script setup>
// Компонент статичный, логики нет
</script>