<script setup>
import SidebarLink from './SidebarLink.vue'
import { usePage } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

const page = usePage()
const user = page.props.auth?.user || {
  name: 'Имя',
  avatarUrl: '',
  rating: 4.2,
  reviewsCount: 5,
}
const defaultAvatar = 'https://www.avito.st/stub_avatars/%D0%98/12_256x256.png'
</script>

<template>
  <div class="bg-white rounded-2xl shadow p-6">
    <div class="flex flex-col items-center mb-8">
      <div class="relative mb-3">
        <img
          :src="user.avatarUrl || defaultAvatar"
          alt="Аватар"
          class="w-20 h-20 rounded-full object-cover border-2 border-pink-400"
        />
      </div>
      <div class="font-bold text-lg mb-1">{{ user.name }}</div>
      <div class="flex items-center mb-2">
        <span class="font-semibold text-yellow-500 mr-1">{{ user.rating }}</span>
        <span class="text-xs text-gray-400 ml-2">{{ user.reviewsCount }} отзывов</span>
      </div>
    </div>

    <nav class="space-y-6 text-sm w-full">
      <!-- “Мои объявления” теперь ведёт на маршрут profile.items -->
      <SidebarLink
        :href="route('profile.items')"
        :active="route().current('profile.items')"
        text="Мои объявления"
      />

      <!-- Остальные пункты меню -->
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
  </div>
</template>

<style scoped>
/* Если нужен белый фон только у боковой панели, bg-white оставьте только тут */
</style>

