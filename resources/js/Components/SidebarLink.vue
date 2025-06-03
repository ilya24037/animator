<template>
  <a :href="href"
    :class="[
      'block w-full font-medium transition',
      'hover:bg-gray-50',
      active ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700',
      $attrs.class]">
    <span>{{ text }}</span>
    <span v-if="badge" class="inline-block ml-2 px-2 py-0.5 bg-pink-100 text-pink-600 rounded text-xs align-middle">{{ badge }}</span>
  </a>
</template>
<script setup>
defineProps({
  href: String,
  text: String,
  badge: String,
  active: Boolean,
})
</script>
