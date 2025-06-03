<!-- resources/js/Pages/Personal/Drafts.vue -->
<script setup lang="ts">
import { ref } from 'vue'
import { usePage } from '@inertiajs/vue3'
import AppLayout     from '@/Layouts/AppLayout.vue'
import SidebarColumn from '@/Components/SidebarColumn.vue'
import SidebarLink   from '@/Components/SidebarLink.vue'
import ItemCard      from '@/Components/ItemCard.vue'

/* ------------ props от Inertia ------------- */
interface Item {
  id: number
  title: string
  price: string | null       /* “5000 ₽ за час” либо null */
  lifetime: string           /* “Удалится навсегда через 30 дней” */
  imageUrl: string | null
  status: 'pending' | 'draft' | 'archive'
}

// Получаем данные из Inertia (props). В контроллере передаётся только массив drafts.
const page = usePage()
const drafts = page.props.drafts as Item[]  // единственный переданный пропс

// defineOptions для указания layout-компонента
defineOptions({ layout: AppLayout })

// Вкладка по умолчанию — «Черновики», так как других пропсов нет
const activeTab = ref<'drafts'>('drafts')
</script>

<template>
  <div class="flex gap-8">
    <aside class="w-[300px] shrink-0">
      <SidebarColumn>
        <SidebarLink :href="route('dashboard')" :active="false">Главная</SidebarLink>
        <SidebarLink :href="route('profile.items')" :active="true">Мои объявления</SidebarLink>
      </SidebarColumn>
    </aside>
    <main class="flex-1 min-w-0">
      <h1 class="text-3xl font-bold mb-6">Черновики</h1>

      <!-- Промо-блок -->
      <div class="p-6 rounded-xl bg-blue-50 border flex items-center gap-4 mb-8">
        <img
          src="https://avito.st/static/ims/53b13974-fbee-4af7-ada2-ba6595e64de9_promo_icon_common.svg"
          alt=""
          class="w-14 h-14 shrink-0"
        />
        <div class="flex-1">
          <h3 class="font-semibold">Скидки и акции</h3>
          <p class="text-sm text-gray-600">настройте для покупателей</p>
        </div>
        <button class="btn btn-primary">Хочу попробовать</button>
      </div>

      <!-- Список черновиков -->
      <div>
        <ItemCard
          v-for="item in drafts"
          :key="item.id"
          :item="item"
        />
        <p v-if="!drafts.length" class="text-gray-500">
          У вас пока нет черновиков
        </p>
      </div>
    </main>
  </div>
</template>

<style scoped>
.btn {
  @apply px-4 py-2 rounded-xl font-semibold text-white bg-black;
}
</style>
