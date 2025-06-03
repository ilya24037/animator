<template>
  <!-- КЛЮЧЕВОЙ МОМЕНТ: sticky top-0 z-50 на header -->
  <header class="sticky top-0 z-50 bg-white shadow rounded-b-2xl">
    <div class="flex items-center justify-between py-5 px-6">

<!-- Логотип -->

<Link href="/" class="text-2xl font-black tracking-tight">
  ANIMATORR
</Link>

<!-- Кнопка выбора города -->

<button
        class="ml-6 flex items-center gap-2 text-base px-4 py-2 rounded-lg hover:bg-gray-100 transition"
        @click="showModal = true"
      >
        <span class="font-medium">{{ location.city || 'Ваш город' }}</span>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
      </button>

<!-- Поисковая форма -->

<form
          @submit.prevent="search"
          class="flex items-center h-[38px] px-[2px] bg-[#006aff] rounded-[12px] w-full max-w-[500px]"
        >
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Поиск"
            class="flex-grow h-[34px] px-4 bg-white text-sm text-gray-800 rounded-[10px] outline-none border-none min-w-[200px]"
            autocomplete="off"
            spellcheck="false"
            maxlength="255"
          />
          <button
            type="submit"
            class="ml-2 h-[36px] px-5 text-white text-sm font-semibold rounded-[10px] bg-[#006aff] hover:bg-[#0051cc] transition"
          >
            Найти
          </button>
        </form>


      <!-- Блок избранного и кнопка объявления -->
      <div class="flex items-center space-x-6 ml-6">
        <FavoriteBlock />

<!-- ▼▼▼ ДОБАВЛЕННАЯ КНОПКА ▼▼▼ -->
      <a
  v-if="user"
  href="/dashboard"
  class="flex items-center gap-2 px-2 h-10 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 transition"
>
  <span class="bg-pink-500 text-white font-bold w-7 h-7 flex items-center justify-center rounded-full text-base">
    {{ user.name ? user.name.charAt(0).toUpperCase() : 'Л' }}
  </span>
  <span class="font-semibold text-base">{{ user.name }}</span>
</a>
      <a
        v-else
        href="/login"
        class="px-4 py-2 rounded-lg border text-blue-600 border-blue-600 hover:bg-blue-600 hover:text-white transition"
      >
        Войти
      </a>
      <!-- ▲▲▲ ДОБАВЛЕННАЯ КНОПКА ▲▲▲ -->

<button
  class="bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg px-5 py-2 ml-2 w-[230px] h-[38px] flex items-center justify-center"
  @click="goToCreate"
>
          Разместить объявление
        </button>

</div>
    </div>

<!-- Модалка выбора города -->
    <CitySelectorModal
      v-if="showModal"
      @close="showModal = false"
      @select="handleSelectCity"
    />
  </header>
</template>

<script setup>
import FavoriteBlock from '@/Components/FavoriteBlock.vue'
import CitySelectorModal from '@/Components/CitySelectorModal.vue'
import { useLocationStore } from '@/Stores/useLocationStore'
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import ApplicationLogo from './ApplicationLogo.vue'
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'

const user = computed(() => usePage().props.auth?.user)
const showModal = ref(false)
const location = useLocationStore()
const searchQuery = ref('')


const handleSelectCity = (selected) => {
  location.setCity(selected)
  showModal.value = false
}

const search = () => {
  alert('Поиск: ' + searchQuery.value)
}

const goToCreate = () => {
  router.get(route('animators.create'))
}

onMounted(() => {
  if (!location.city || location.city === 'Ваш город') {
    fetch('https://ipapi.co/json')
      .then(res => res.json())
      .then(data => {
        if (data.city) {
          location.setCity(data.city)
        }
      })
  }
})
</script>
