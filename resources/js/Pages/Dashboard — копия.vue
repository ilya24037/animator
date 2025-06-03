<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import SidebarColumn from '@/Components/SidebarColumn.vue'
import SidebarLink from '@/Components/SidebarLink.vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const user = page.props.auth?.user || {
  name: 'Илья',
  avatarUrl: '',
  rating: 4.2,
  reviewsCount: 5,
}
const defaultAvatar = 'https://www.avito.st/stub_avatars/%D0%98/12_256x256.png'

defineOptions({ layout: AppLayout })
</script>

<template>
  <div class="flex gap-8">
    <!-- Sidebar -->
    <aside class="w-[300px] shrink-0">
      <SidebarColumn>
        <!-- Аватар -->
        <div class="flex flex-col items-center mb-8">
          <div class="relative mb-3">
            <img
              :src="user.avatarUrl || defaultAvatar"
              alt="Аватар"
              class="w-20 h-20 rounded-full object-cover border-2 border-pink-400"
            />
            <!-- Иконка загрузки аватара -->
            <label
              class="absolute bottom-0 right-0 w-8 h-8 bg-pink-500 flex items-center justify-center rounded-full border-2 border-white cursor-pointer"
              title="Загрузить новое фото"
            >
              <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M12 4v16m8-8H4" />
              </svg>
              <input type="file" class="hidden" />
            </label>
          </div>
          <!-- Имя -->
          <div class="font-bold text-lg mb-1">{{ user.name }}</div>
          <!-- Рейтинг -->
          <div class="flex items-center mb-2">
            <span class="font-semibold text-yellow-500 mr-1">{{ user.rating }}</span>
            <div class="flex items-center">
              <svg v-for="star in 5" :key="star" class="w-5 h-5"
                :class="star <= Math.round(user.rating) ? 'text-yellow-400' : 'text-gray-200'" fill="currentColor"
                viewBox="0 0 20 20">
                <path
                  d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.563 4.818a1 1 0 00.95.69h5.065c.969 0 1.371 1.24.588 1.81l-4.1 2.983a1 1 0 00-.364 1.118l1.563 4.818c.3.921-.755 1.688-1.54 1.118l-4.1-2.983a1 1 0 00-1.176 0l-4.1 2.983c-.784.57-1.838-.197-1.54-1.118l1.563-4.818a1 1 0 00-.364-1.118l-4.1-2.983c-.783-.57-.38-1.81.588-1.81h5.065a1 1 0 00.95-.69l1.563-4.818z" />
              </svg>
            </div>
            <span class="text-xs text-gray-400 ml-2">{{ user.reviewsCount }} отзывов</span>
          </div>
        </div>
        <!-- Меню -->
        <nav class="space-y-6 text-sm w-full">
          <SidebarLink :href="route('profile.items')" :active="route().current('profile.items')">
  Мои объявления
</SidebarLink>
          <SidebarLink href="/orders" text="Мои объявления" />
          <SidebarLink href="/profile/contacts" text="Мои отзывы" />
          <SidebarLink href="/favorites" text="Избранное" />
          <SidebarLink href="/profile/messenger" text="Сообщения" />
          <SidebarLink href="/profile/notifications" text="Уведомления" />
          <SidebarLink href="/account" text="Кошелёк" />
          <SidebarLink href="/paid-services/listing-fees" text="Платные услуги" />
          <SidebarLink href="/profile/basic" text="Управление профилем" />
          <SidebarLink href="/profile/settings" text="Настройки" />
          <SidebarLink href="/logout" text="Выйти" class="text-red-500" />
        </nav>
      </SidebarColumn>
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

      <!-- Мои объявления -->
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
