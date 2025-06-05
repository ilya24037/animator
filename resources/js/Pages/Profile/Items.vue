<script setup lang="ts">
import { ref } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import SidebarColumn from '@/Components/SidebarColumn.vue'
import SidebarLink from '@/Components/SidebarLink.vue'
import ItemCard from '@/Components/ItemCard.vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const items = page.props.items || []
const tab = page.props.tab || 'draft'
const filter = page.props.filter || 'all'
const counts = page.props.counts || {}

const tabs = [
  { key: 'draft', label: 'Черновики' },
  { key: 'published', label: 'Активные' },
  { key: 'inactive', label: 'Отклонённые' },
  { key: 'old', label: 'Архив' }
]

// Функция построения URL для таба (как у Avito)
const routeToTab = (key) =>
  filter ? `/profile/items/${key}/${filter}` : `/profile/items/${key}/all`

defineOptions({ layout: AppLayout })
</script>

<template>
  <div class="flex gap-8">
    <aside class="w-[300px] shrink-0">
      <SidebarColumn>
        <SidebarLink :href="route('dashboard')">Главная</SidebarLink>
        <SidebarLink :href="routeToTab(tab)" :active="true">Мои объявления</SidebarLink>
      </SidebarColumn>
    </aside>
    <main class="flex-1 min-w-0">
      <h1 class="text-3xl font-bold mb-6">Мои объявления</h1>

      <!-- Вкладки -->
      <div class="flex border-b mb-6 gap-10">
        <a
          v-for="t in tabs"
          :key="t.key"
          :href="routeToTab(t.key)"
          :class="[
            'relative pb-3 text-lg transition',
            tab === t.key ? 'font-semibold border-b-2 border-black' : 'text-gray-500 hover:text-black'
          ]"
        >
          {{ t.label }}
          <span class="ml-1 text-base">{{ counts[t.key] || 0 }}</span>
        </a>
      </div>

      <!-- Фильтр/чекбокс для черновиков (Avito-style) -->
      <div v-if="tab === 'draft'" class="mb-4">
        <input type="checkbox" id="selectAll" /> <label for="selectAll">Выбрать все черновики</label>
      </div>

      <!-- Список объявлений -->
      <div>
        <ItemCard
          v-for="item in items"
          :key="item.id"
          :item="item"
        />
        <p v-if="!items.length" class="text-gray-500">Нет объявлений</p>
      </div>
    </main>
  </div>
</template>
