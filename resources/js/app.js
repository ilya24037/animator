import '../css/app.css'
import './bootstrap'

import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { createApp, h } from 'vue'
import { ZiggyVue } from '../../vendor/tightenco/ziggy'
import { createPinia } from 'pinia'
import errorPath from './directives/errorPath.js'    // <- ваша директива

const appName = import.meta.env.VITE_APP_NAME || 'Laravel'

createInertiaApp({
  /* … */
  setup({ el, App, props, plugin }) {
    const pinia = createPinia()

    return createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ZiggyVue)
      .use(pinia)
     .directive('error-path', errorPath)              // <- регистрируем глобально
      .mount(el)
  },
  /* … */
})
