<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{ form: Record<string, any>, errors?: Record<string, any> }>()

// refs для плавного scroll/focus
const fileInput = ref(null)
const videoUrlInput = ref(null)
defineExpose({ fileInput, videoUrlInput })

function handleFiles(e: Event) {
  const target = e.target as HTMLInputElement
  const files = target.files ? Array.from(target.files) : []
  files.forEach(f => {
    if (!('preview' in f)) {
      Object.defineProperty(f, 'preview', {
        value: URL.createObjectURL(f),
        enumerable: false,
        configurable: true
      })
    }
  })
  props.form.files = files
}
</script>

<template>
  <section>
    <h2 class="text-2xl font-bold mb-4">Фото и видео</h2>

    <!-- Фото -->
    <label class="block font-semibold mb-2">Фотографии (до 10 файлов)</label>
    <input
      ref="fileInput"
      type="file"
      multiple
      accept="image/*"
      @change="handleFiles"
      class="mb-4"
      :class="[
        props.errors?.files ? 'border border-red-500 rounded-xl px-4 py-3' : ''
      ]"
    />
    <span v-if="props.errors?.files" class="text-xs text-red-500">{{ props.errors.files }}</span>

    <div v-if="props.form.files?.length" class="flex flex-wrap gap-3 mb-6">
      <div
        v-for="file in props.form.files"
        :key="file.name"
        class="w-24 h-24 bg-gray-100 rounded-lg overflow-hidden"
      >
        <img :src="file.preview" class="object-cover w-full h-full" />
      </div>
    </div>

    <!-- Видео -->
    <label class="block font-semibold mb-2">Видео (YouTube/Rutube)</label>
    <input
      ref="videoUrlInput"
      v-model="props.form.videoUrl"
      type="url"
      placeholder="https://youtu.be/..."
      class="w-full rounded-xl border px-4 py-3 mb-6"
      :class="[
        props.errors?.videoUrl ? 'border-red-500' : 'border-gray-200'
      ]"
    />
    <span v-if="props.errors?.videoUrl" class="text-xs text-red-500">{{ props.errors.videoUrl }}</span>
  </section>
</template>

