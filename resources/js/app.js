// 1) Стили и Bootstrap/axios
import '../css/app.css'
import './bootstrap'

// 2) Импорты из Vue, Inertia и вспомогалок
import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { ZiggyVue } from '../../vendor/tightenco/ziggy'
import { createPinia } from 'pinia'

// 3) Наша директива
import errorPath from './directives/errorPath.js'

// 4) Название приложения (необязательно)
const appName = import.meta.env.VITE_APP_NAME || 'Laravel'

// 5) Тело инициализации Inertia+Vue
createInertiaApp({
  //  Обязательно: как разрешать (resolve) компоненты-страницы
  resolve: name => resolvePageComponent(
    `./Pages/${name}.vue`,
    import.meta.glob('./Pages/**/*.vue')
  ),

  //  Необязательно, но удобно: заголовок вкладки
  title: title => `${title} — ${appName}`,

  // 6) Функция setup, где мы «собираем» Vue-приложение
  setup({ el, App, props, plugin }) {
    const pinia = createPinia()               // создаём Pinia

    return createApp({ render: () => h(App, props) }) // создаём корень Vue
      .use(plugin)                            // подключаем Inertia
      .use(ZiggyVue)                          // подключаем Ziggy (роутинг Laravel)
      .use(pinia)                             // подключаем Pinia
      .directive('error-path', errorPath)     // регистрируем директиву ГЛОБАЛЬНО
      .mount(el)                              // «вешаем» Vue на <div id="app">
  },

  // 7) Прогресс-бар Inertia (опционально)
  progress: {
    delay: 250,
    color: '#29d',
    includeCSS: true,
    showSpinner: false,
  },
})
