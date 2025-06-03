import { defineAsyncComponent, h } from 'vue'

/**
 * Безопасный defineAsyncComponent:  
 *  • если компонент загрузился — отдаём его;  
 *  • если loader упал или файла нет — выводим заглушку
 */
export function safeAsync(loader: () => Promise<any>, name = 'Unknown') {
  const Fallback = {
    name: `${name}Fallback`,
    render() {
      return h(
        'div',
        { class: 'border border-red-400 bg-red-50 text-red-700 p-4 rounded-md my-4' },
        `Шаг «${name}» временно недоступен`
      )
    }
  }

  return defineAsyncComponent({
    loader,
    // через 0 мс показываем fallback, если loader ещё не выполнился
    delay: 0,
    timeout: 15_000,          // защита от «вечной» загрузки
    errorComponent: Fallback, // если import() бросил ошибку
    onError(err, _retry, fail) {
      console.warn(`Не удалось загрузить компонент "${name}"`, err)
      // сразу выводим заглушку, не пытаемся бесконечно ретраить
      fail()
    }
  })
}
