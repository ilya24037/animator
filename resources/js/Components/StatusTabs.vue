<script setup>
import { computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

const props = defineProps({ counts: Object, current: String });

const tabs = [
  { key: 'waiting',  label: 'Ждут действий' },
  { key: 'draft',    label: 'Черновики'     },
  { key: 'archived', label: 'Архив'         },
];

function go(status) {
  router.visit(route('profile.items', { status }), { preserveScroll: true });
}

const page = usePage();
const isLoading = computed(() => page.props.value.processing ?? false);
</script>

<template>
  <nav class="flex gap-6 text-lg font-medium mb-6">
    <button
      v-for="t in tabs"
      :key="t.key"
      :disabled="isLoading"
      @click="go(t.key)"
      :class="[
        'relative pb-2 transition-colors',
        t.key === current ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500 hover:text-blue-600'
      ]"
    >
      {{ t.label }}
      <span
        v-if="counts[t.key]"
        class="ml-1 text-sm text-gray-400"
      >{{ counts[t.key] }}</span>
    </button>
  </nav>
</template>