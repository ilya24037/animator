<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import { Link } from '@inertiajs/vue3'
</script>

<template>
  <div class="flex min-h-screen items-center justify-center bg-gray-100">
    <div class="w-full max-w-md">
      <div class="flex flex-col items-center mb-8">
        <Link href="/">
          <ApplicationLogo class="h-20 w-20 fill-current text-gray-500" />
        </Link>
      </div>
      <div class="bg-white px-6 py-8 shadow-xl rounded-2xl">
        <slot />
      </div>
    </div>
  </div>
</template>
