<template>
  <div class="space-y-6">
    <div>
      <h2 class="text-2xl font-bold mb-2">Фото и видео</h2>
      <p class="text-gray-600">Объявления с фото получают в 5 раз больше откликов</p>
    </div>

    <!-- Зона загрузки -->
    <div
      @drop="handleDrop"
      @dragover.prevent
      @dragenter.prevent
      @dragleave="isDragging = false"
      class="relative"
    >
      <input
        ref="fileInput"
        type="file"
        multiple
        accept="image/*,video/*"
        @change="handleFileSelect"
        class="hidden"
      >

      <div
        @click="$refs.fileInput.click()"
        class="border-2 border-dashed rounded-xl p-8 text-center cursor-pointer transition-all"
        :class="[
          isDragging 
            ? 'border-blue-500 bg-blue-50' 
            : 'border-gray-300 hover:border-gray-400'
        ]"
      >
        <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
        </svg>
        
        <p class="text-lg font-medium mb-2">
          Перетащите фото сюда или нажмите для выбора
        </p>
        <p class="text-sm text-gray-500">
          Максимум 10 фото, до 10 МБ каждое
        </p>
        <p class="text-xs text-gray-400 mt-2">
          Поддерживаемые форматы: JPG, PNG, GIF, MP4, MOV
        </p>
      </div>
    </div>

    <!-- Загруженные файлы -->
    <div v-if="form.photos?.length > 0" class="space-y-4">
      <div class="flex items-center justify-between">
        <h3 class="font-medium">Загружено файлов: {{ form.photos.length }}</h3>
        <button
          @click="clearAll"
          class="text-sm text-red-600 hover:text-red-700"
        >
          Удалить все
        </button>
      </div>

      <draggable
        v-model="form.photos"
        item-key="id"
        class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4"
        :animation="200"
        handle=".drag-handle"
      >
        <template #item="{ element, index }">
          <div class="relative group">
            <!-- Превью -->
            <div class="aspect-square rounded-lg overflow-hidden bg-gray-100">
              <img
                v-if="element.type === 'image'"
                :src="element.preview"
                :alt="`Фото ${index + 1}`"
                class="w-full h-full object-cover"
              >
              <video
                v-else
                :src="element.preview"
                class="w-full h-full object-cover"
                muted
              />
            </div>

            <!-- Действия -->
            <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center gap-2">
              <!-- Перетаскивание -->
              <button
                class="drag-handle p-2 bg-white rounded-lg shadow hover:shadow-lg transition cursor-move"
                title="Перетащить"
              >
                <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
              </button>

              <!-- Редактировать -->
              <button
                @click="editPhoto(index)"
                class="p-2 bg-white rounded-lg shadow hover:shadow-lg transition"
                title="Редактировать"
              >
                <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
              </button>

              <!-- Удалить -->
              <button
                @click="removePhoto(index)"
                class="p-2 bg-white rounded-lg shadow hover:shadow-lg transition"
                title="Удалить"
              >
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              </button>
            </div>

            <!-- Метка главного фото -->
            <div v-if="index === 0" class="absolute top-2 left-2 bg-blue-600 text-white text-xs px-2 py-1 rounded">
              Главное фото
            </div>

            <!-- Прогресс загрузки -->
            <div v-if="element.uploading" class="absolute inset-0 bg-white bg-opacity-90 flex items-center justify-center rounded-lg">
              <div class="text-center">
                <svg class="animate-spin h-8 w-8 mx-auto mb-2 text-blue-600" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" />
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                </svg>
                <p class="text-sm">{{ element.progress }}%</p>
              </div>
            </div>
          </div>
        </template>
      </draggable>
    </div>

    <!-- Добавить видео с YouTube -->
    <div class="border-t pt-6">
      <h3 class="font-medium mb-3">Видео с YouTube</h3>
      <div class="flex gap-3">
        <input
          v-model="youtubeUrl"
          type="url"
          placeholder="Вставьте ссылку на видео YouTube"
          class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        >
        <button
          @click="addYoutubeVideo"
          :disabled="!youtubeUrl"
          class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition"
        >
          Добавить
        </button>
      </div>
      
      <!-- Превью YouTube видео -->
      <div v-if="form.youtube_url" class="mt-4">
        <div class="relative aspect-video rounded-lg overflow-hidden bg-gray-100">
          <iframe
            :src="`https://www.youtube.com/embed/${getYoutubeId(form.youtube_url)}`"
            frameborder="0"
            class="w-full h-full"
            allowfullscreen
          />
          <button
            @click="form.youtube_url = null"
            class="absolute top-2 right-2 p-1 bg-black bg-opacity-50 rounded text-white hover:bg-opacity-70 transition"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Советы -->
    <div class="bg-blue-50 rounded-lg p-4">
      <h4 class="font-medium text-blue-900 mb-2">Советы для хороших фото:</h4>
      <ul class="text-sm text-blue-800 space-y-1">
        <li>• Используйте естественное освещение</li>
        <li>• Показывайте услугу с разных ракурсов</li>
        <li>• Добавьте фото процесса работы</li>
        <li>• Покажите результаты вашей работы</li>
      </ul>
    </div>
  </div>

  <!-- Модалка редактирования фото -->
  <PhotoEditor
    v-if="editingPhoto"
    :photo="editingPhoto"
    @save="saveEditedPhoto"
    @close="editingPhoto = null"
  />
</template>

<script setup>
import { ref } from 'vue'
import draggable from 'vuedraggable'
import PhotoEditor from '@/Components/CreateAd/PhotoEditor.vue'

const props = defineProps({
  form: {
    type: Object,
    required: true
  },
  errors: {
    type: Object,
    default: () => ({})
  }
})

// Состояние
const isDragging = ref(false)
const youtubeUrl = ref('')
const editingPhoto = ref(null)
const editingPhotoIndex = ref(null)

// Инициализация массива фото
if (!props.form.photos) {
  props.form.photos = []
}

// Обработка drag & drop
function handleDrop(e) {
  e.preventDefault()
  isDragging.value = false
  
  const files = Array.from(e.dataTransfer.files)
  processFiles(files)
}

// Обработка выбора файлов
function handleFileSelect(e) {
  const files = Array.from(e.target.files)
  processFiles(files)
}

// Обработка файлов
async function processFiles(files) {
  const maxFiles = 10
  const maxSize = 10 * 1024 * 1024 // 10 MB
  
  for (const file of files) {
    // Проверки
    if (props.form.photos.length >= maxFiles) {
      showNotification('error', `Максимум ${maxFiles} файлов`)
      break
    }
    
    if (file.size > maxSize) {
      showNotification('error', `Файл ${file.name} слишком большой`)
      continue
    }
    
    if (!file.type.match(/^(image|video)\//)) {
      showNotification('error', `Файл ${file.name} не является изображением или видео`)
      continue
    }
    
    // Создаем объект фото
    const photo = {
      id: Date.now() + Math.random(),
      file: file,
      name: file.name,
      size: file.size,
      type: file.type.startsWith('image/') ? 'image' : 'video',
      preview: URL.createObjectURL(file),
      uploading: false,
      progress: 0
    }
    
    props.form.photos.push(photo)
    
    // Симуляция загрузки (в реальном проекте здесь будет загрузка на сервер)
    simulateUpload(photo)
  }
}

// Симуляция загрузки
function simulateUpload(photo) {
  photo.uploading = true
  
  const interval = setInterval(() => {
    photo.progress += 10
    if (photo.progress >= 100) {
      clearInterval(interval)
      photo.uploading = false
    }
  }, 200)
}

// Удаление фото
function removePhoto(index) {
  const photo = props.form.photos[index]
  if (photo.preview) {
    URL.revokeObjectURL(photo.preview)
  }
  props.form.photos.splice(index, 1)
}

// Очистить все
function clearAll() {
  props.form.photos.forEach(photo => {
    if (photo.preview) {
      URL.revokeObjectURL(photo.preview)
    }
  })
  props.form.photos = []
}

// Редактирование фото
function editPhoto(index) {
  editingPhotoIndex.value = index
  editingPhoto.value = { ...props.form.photos[index] }
}

function saveEditedPhoto(editedPhoto) {
  props.form.photos[editingPhotoIndex.value] = editedPhoto
  editingPhoto.value = null
  editingPhotoIndex.value = null
}

// YouTube
function addYoutubeVideo() {
  if (!youtubeUrl.value) return
  
  const videoId = getYoutubeId(youtubeUrl.value)
  if (videoId) {
    props.form.youtube_url = youtubeUrl.value
    youtubeUrl.value = ''
  } else {
    showNotification('error', 'Неверная ссылка на YouTube')
  }
}

function getYoutubeId(url) {
  const regex = /(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([^&\n?#]+)/
  const match = url.match(regex)
  return match ? match[1] : null
}

// Уведомления
function showNotification(type, message) {
  // Реализация уведомлений
  console.log(type, message)
}
</script>