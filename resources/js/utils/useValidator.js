import { reactive, watch } from 'vue'

/**
 * Универсальный валидатор для любых форм
 * @param {object} form — реактивный объект формы
 * @param {object} rules — объект вида: { "details.title": value => errorMessageOrEmpty }
 * @returns {object} { errors, validate, resetErrors }
 */
export function useValidator(form, rules) {
  const errors = reactive({})

  // Основная функция валидации: пробегает по всем правилам
  function validate() {
    let valid = true
    for (const [path, rule] of Object.entries(rules)) {
      const value = getValue(form, path)
      const error = rule(value, form)
      errors[path] = error
      if (error) valid = false
    }
    return valid
  }

  // Вотчер: сбрасывает ошибку поля, если оно стало валидным
  for (const [path, rule] of Object.entries(rules)) {
    watch(
      () => getValue(form, path),
      (val) => {
        const error = rule(val, form)
        if (!error) errors[path] = ''
      }
    )
  }

  // Утилита: сбросить все ошибки
  function resetErrors() {
    Object.keys(errors).forEach(key => errors[key] = '')
  }

  return { errors, validate, resetErrors }
}

/**
 * Достаёт значение вложенного поля по "details.title" → form.details.title
 */
function getValue(obj, path) {
  return path.split('.').reduce((acc, key) => acc && acc[key], obj)
}
