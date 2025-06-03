<script setup lang="ts">
import { ref } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import SidebarColumn from '@/Components/SidebarColumn.vue'
import SidebarLink from '@/Components/SidebarLink.vue'
import ItemCard from '@/Components/ItemCard.vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const pending = page.props.pending || []
const drafts = page.props.drafts || []
const archive = page.props.archive || []

const tabs = [
  { key: 'pending', label: 'Ждут действий', counter: pending.length },
  { key: 'drafts', label: 'Черновики', counter: drafts.length },
  { key: 'archive', label: 'Архив', counter: archive.length }
]
const activeTab = ref<'pending' | 'drafts' | 'archive'>('pending')

defineOptions({ layout: AppLayout })
</script>

<template>
  <div class="flex gap-8">
    <aside class="w-[300px] shrink-0">
      <SidebarColumn>
        <SidebarLink :href="route('dashboard')">Главная</SidebarLink>
        <SidebarLink :href="route('profile.items')" :active="true">Мои объявления</SidebarLink>
      </SidebarColumn>
    </aside>
    <main class="flex-1 min-w-0">
      <h1 class="text-3xl font-bold mb-6">Мои объявления</h1>

      <!-- Промо-блок -->
      <div class="p-6 rounded-xl bg-blue-50 border flex items-center gap-4 mb-8">
        <img src="https://avito.st/static/ims/53b13974-fbee-4af7-ada2-ba6595e64de9_promo_icon_common.svg"
             alt="" class="w-14 h-14 shrink-0">
        <div class="flex-1">
          <h3 class="font-semibold">Скидки и акции</h3>
          <p class="text-sm text-gray-600">настройте для покупателей</p>
        </div>
        <button class="btn btn-primary">Хочу попробовать</button>
      </div>

      <!-- Табы -->
      <div class="flex border-b mb-6 gap-10">
        <button
          v-for="tab in tabs"
          :key="tab.key"
          type="button"
          :class="[
            'relative pb-3 text-lg transition',
            activeTab.value === tab.key ? 'font-semibold border-b-2 border-black' : 'text-gray-500 hover:text-black'
          ]"
          @click="activeTab = tab.key"
        >
          {{ tab.label }}
          <span class="ml-1 text-base">{{ tab.counter }}</span>
        </button>
      </div>

      <!-- Заголовок раздела -->
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold">
          <template v-if="activeTab.value === 'pending'">Все ждущие действий объявления</template>
          <template v-if="activeTab.value === 'drafts'">Все черновики</template>
          <template v-if="activeTab.value === 'archive'">Все из архива</template>
          <span class="ml-2 text-gray-500 text-base">
            {{
              activeTab.value === 'pending'
                ? pending.length
                : activeTab.value === 'drafts'
                  ? drafts.length
                  : archive.length
            }}
          </span>
        </h2>
      </div>

      <!-- Список объявлений -->
      <div>
        <ItemCard
          v-for="item in (
            activeTab.value === 'pending' ? pending :
            activeTab.value === 'drafts' ? drafts : archive
          )"
          :key="item.id"
          :item="item"
        />
        <p v-if="activeTab.value === 'pending' && !pending.length" class="text-gray-500">Нет объявлений, которые ждут действий</p>
        <p v-if="activeTab.value === 'drafts' && !drafts.length" class="text-gray-500">У вас пока нет черновиков</p>
        <p v-if="activeTab.value === 'archive' && !archive.length" class="text-gray-500">Архив пуст</p>
      </div>
    </main>
  </div>
</template>

<style scoped>
.btn {
  @apply px-4 py-2 rounded-xl font-semibold text-white bg-black;
}
</style>
