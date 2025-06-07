// resources/js/directives/errorPath.js

export default {
  // Метод `mounted` вызывается, когда элемент добавляется в DOM
  mounted(el, binding) {
    // Записываем значение из `v-error-path="..."` в data-атрибут
    el.dataset.path = binding.value
  }
}
