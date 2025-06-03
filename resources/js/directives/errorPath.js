export default {
  mounted (el, binding) {
    el.setAttribute('data-path', binding.value)
  }
}
