const memoryFallback = new Map()

export const storage = {
  get(key, defaultValue = null) {
    try {
      const item = localStorage.getItem(key)
      if (item === null) {
        return defaultValue
      }
      return JSON.parse(item)
    } catch {
      return memoryFallback.has(key) ? memoryFallback.get(key) : defaultValue
    }
  },

  set(key, value) {
    try {
      localStorage.setItem(key, JSON.stringify(value))
    } catch {
      memoryFallback.set(key, value)
    }
  },

  remove(key) {
    try {
      localStorage.removeItem(key)
    } catch {
      memoryFallback.delete(key)
    }
  },

  clear() {
    try {
      localStorage.clear()
    } catch {
      memoryFallback.clear()
    }
  },
}
