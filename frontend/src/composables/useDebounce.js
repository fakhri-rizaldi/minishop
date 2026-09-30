import { ref, watch } from 'vue'

/**
 * Hook to debounce a ref value by `delay` ms (REQ-CAT-03, BR-CAT-1)
 * @param {import('vue').Ref<any>} value - Ref value to debounce
 * @param {number} delay - Delay in milliseconds (default 250ms)
 * @returns {import('vue').Ref<any>} Debounced ref value
 */
export function useDebounce(value, delay = 250) {
  const debouncedValue = ref(value.value)
  let timeout = null

  watch(value, (newVal) => {
    clearTimeout(timeout)
    timeout = setTimeout(() => {
      debouncedValue.value = newVal
    }, delay)
  })

  return debouncedValue
}

/**
 * Standard debounce function helper
 * @param {Function} fn
 * @param {number} delay
 * @returns {Function}
 */
export function debounce(fn, delay = 250) {
  let timeoutId = null
  return function (...args) {
    clearTimeout(timeoutId)
    timeoutId = setTimeout(() => {
      fn.apply(this, args)
    }, delay)
  }
}
