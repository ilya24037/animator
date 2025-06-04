<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  counts: Object,
  current: String,
});

const tabs = [
  { key: 'waiting',  label: 'Ждут действий' },
  { key: 'draft',    label: 'Черновики'     },
  { key: 'archived', label: 'Архив'         },
];
</script>

<template>
  <nav class="flex gap-6 text-lg font-medium mb-6">
    <Link
      v-for="t in tabs"
      :key="t.key"
      :href="`/profile/items?status=${t.key}`"
      preserve-scroll
      :class="[
        'relative pb-2 transition-colors',
        t.key === current
          ? 'border-b-2 border-blue-600 text-blue-600'
          : 'text-gray-500 hover:text-blue-600'
      ]"
    >
      {{ t.label }}
      <span
        v-if="counts && counts[t.key] !== undefined"
        class="ml-1 text-sm text-gray-400"
      >
        {{ counts[t.key] }}
      </span>
    </Link>
  </nav>
</template>
