<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import SidebarColumn from '@/Components/SidebarColumn.vue'

defineOptions({ layout: AppLayout })
</script>

<template>
  <div class="flex gap-8">
    <!-- Sidebar -->
    <aside class="w-[300px] shrink-0">
      <SidebarColumn />
    </aside>
    <!-- Main content -->
    <main class="flex-1">
      <!-- Баннер -->
      <div class="bg-white rounded-2xl shadow p-6 flex items-center justify-between mb-6">
        <div>
          <h2 class="font-bold text-2xl mb-1">Изменилась комиссия за продажу с Авито Доставкой</h2>
          <div class="text-gray-600 text-sm">Сумму можно увидеть при размещении и редактировании объявления</div>
          <a
            href="https://support.avito.ru/sections/489?articleId=2676"
            class="mt-3 inline-block px-5 py-2 bg-blue-50 text-blue-700 rounded-lg font-medium hover:bg-blue-100 transition"
            target="_blank"
            >Узнать больше</a>
        </div>
        <img src="https://avito.st/static/ims/77348324-6824-4ab9-8965-d9fdfd6c1e37_cpt_commission_info_banner_common_261x225.png"
          class="w-36 rounded-lg" alt="баннер">
      </div>

      <!-- Мои объявления (пример раздела) -->
      <section>
        <h1 class="text-3xl font-bold mb-5">Мои объявления</h1>
        <div class="flex items-center gap-6 mb-6">
          <button class="font-semibold text-lg border-b-2 border-black px-2 pb-1">Черновики 1</button>
          <button class="font-semibold text-lg text-gray-500 hover:text-black px-2 pb-1">Архив 11</button>
        </div>
        <div class="bg-white rounded-2xl shadow-md p-6">
          <div class="flex flex-col md:flex-row gap-6 md:items-center">
            <div class="w-24 h-24 rounded-lg bg-gray-100 flex items-center justify-center text-3xl font-bold text-gray-300">
              ?
            </div>
            <div class="flex-1">
              <div class="font-semibold text-xl text-blue-600 mb-1">Массаж</div>
              <div class="font-medium text-gray-800">Цена договорная</div>
              <div class="text-gray-500 text-sm">Удалится навсегда через 24 дня</div>
            </div>
            <div class="flex flex-col gap-2">
              <a href="/additem?draftId=1369830213" class="inline-block px-4 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 transition">Редактировать</a>
              <button class="inline-block px-2 py-1 rounded-lg border border-gray-300 text-gray-700 font-medium hover:bg-gray-100">...</button>
            </div>
          </div>
        </div>
      </section>
    </main>
  </div>
</template>
