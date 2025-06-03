<template>
  <div :class="compact
    ? 'flex flex-col items-center gap-2 text-[#1f1f1f] hover:text-[#006aff]'
    : 'flex items-center gap-6 group text-[#1f1f1f] hover:text-[#006aff]'">
    
    <!-- Избранное -->
    <a href="/favorites" class="relative flex flex-col items-center">
      <div class="relative">
        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="w-6 h-6" viewBox="0 0 24 24">
          <path d="m10.65 20.3-1.725-1.575a68.212 68.212 0 0 1-4.787-4.813C2.713 12.321 2 10.567 2 8.65c0-1.567.525-2.875 1.575-3.925C4.625 3.675 5.933 3.15 7.5 3.15c.883 0 1.717.187 2.5.562a5.96 5.96 0 0 1 2 1.538 5.962 5.962 0 0 1 2-1.538 5.721 5.721 0 0 1 2.5-.562c1.567 0 2.875.525 3.925 1.575C21.475 5.775 22 7.083 22 8.65c0 1.917-.708 3.675-2.125 5.275a59.847 59.847 0 0 1-4.825 4.825l-1.7 1.55a1.89 1.89 0 0 1-1.35.55 1.89 1.89 0 0 1-1.35-.55Z" />
        </svg>
        <span v-if="!compact" class="absolute top-0 right-0 translate-x-1/2 -translate-y-1/2 bg-[#f1177e] text-white text-[10px] font-bold w-4 h-4 flex items-center justify-center rounded-full">2</span>
      </div>
      <span v-if="!compact" class="text-xs mt-1 hidden lg:inline">Избранное</span>
    </a>

    <!-- Сравнение -->
    <a href="/compare" class="relative flex flex-col items-center">
      <div class="relative">
        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="w-6 h-6" viewBox="0 0 24 24">
          <path fill-rule="evenodd" d="M6 8.69a1 1 0 0 0-1 1v10.2a1 1 0 1 0 2 0V9.68a1 1 0 0 0-1-1Zm13 11.2v-5.34a1 1 0 0 0-1.77-.63.99.99 0 0 0-.23.63v5.33a1 1 0 0 0 1 1 1 1 0 0 0 1-1Zm-6 0a1 1 0 1 1-2 0V4.11a1 1 0 1 1 2 0v15.76Z" clip-rule="evenodd" />
        </svg>
        <span v-if="!compact" class="absolute top-0 right-0 translate-x-1/2 -translate-y-1/2 bg-[#f1177e] text-white text-[10px] font-bold w-4 h-4 flex items-center justify-center rounded-full">3</span>
      </div>
      <span v-if="!compact" class="text-xs mt-1 hidden lg:inline">Сравнение</span>
    </a>
  </div>
</template>

<script setup>
defineProps({
  compact: {
    type: Boolean,
    default: false
  }
})
</script>
