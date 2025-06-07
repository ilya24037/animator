<script setup>
import SidebarLink from './SidebarLink.vue'
import { usePage }  from '@inertiajs/vue3'
import { route }    from 'ziggy-js'

/* ---------- данные пользователя ---------- */
const page = usePage()
const user = page.props.auth?.user ?? {
  name:          'Имя',
  avatarUrl:     '',
  rating:        0,
  reviewsCount:  0,
}
const defaultAvatar =
  'https://www.avito.st/stub_avatars/%D0%98/12_256x256.png'

/* ---------- безопасная ссылка “Мои объявления” ---------- */
let itemsHref   = '/profile/items/draft/all' // fallback по умолчанию
let itemsActive = window.location.pathname.startsWith('/profile/items')

if (typeof route().has === 'function' && route().has('profile.items')) {
  itemsHref   = route('profile.items', { tab: 'draft', filter: 'all' })
  itemsActive = route().current('profile.items')
}
</script>

<template>
  <div class="bg-white rounded-2xl shadow p-6">
    <!-- блок профиля -->
    <div class="flex flex-col items-center mb-8">
      <img
        :src="user.avatarUrl || defaultAvatar"
        class="w-20 h-20 rounded-full object-cover border-2 border-pink-400 mb-3"
        :alt="user.name"
      />
      <div class="font-bold text-lg mb-1">{{ user.name }}</div>

      <div class="flex items-center text-sm text-gray-500">
        <span class="font-semibold text-yellow-500 mr-1">{{ user.rating }}</span>
        <span class="text-xs ml-2">{{ user.reviewsCount }} отзывов</span>
      </div>
    </div>

    <!-- меню -->
    <nav class="space-y-6 text-sm w-full">
      <SidebarLink
        :href="itemsHref"
        :active="itemsActive"
        text="Мои объявления"
      />

      <!-- остальные пункты без изменений -->
      <SidebarLink href="/profile/contacts"            text="Мои отзывы"      />
      <SidebarLink href="/favorites"                   text="Избранное"       />
      <SidebarLink href="/profile/messenger"           text="Сообщения"       />
      <SidebarLink href="/profile/notifications"       text="Уведомления"     />
      <SidebarLink href="/account"                     text="Кошелёк"         />
      <SidebarLink href="/paid-services/listing-fees"  text="Платные услуги" />
      <SidebarLink href="/profile/basic"               text="Управление профилем" />
      <SidebarLink href="/profile/settings"            text="Настройки"       />
      <SidebarLink href="/logout" class="text-red-500" text="Выйти"           />
    </nav>
  </div>
</template>

<style scoped>
/* Здесь остаются (или добавляются) локальные стили панели при необходимости */
</style>
