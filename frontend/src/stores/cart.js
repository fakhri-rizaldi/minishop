import { defineStore } from 'pinia'
import { storage } from '@/utils/storage'

export const useCartStore = defineStore('cart', {
  state: () => ({
    // Load persisted items with schema validation (REQ-CART-10, 11)
    items: storage.get('minishop_cart', []).filter((item) => (
      item &&
      typeof (item.id || item.product_id) === 'number' &&
      typeof item.price === 'number' &&
      typeof item.quantity === 'number' &&
      item.quantity >= 1
    )).map((item) => ({
      id: item.id || item.product_id,
      product_id: item.product_id || item.id,
      name: item.name || '',
      price: item.price,
      stock: item.stock ?? 9999,
      quantity: item.quantity,
      image_url: item.image_url || '',
    })),
  }),

  getters: {
    totalItems: (state) => state.items.reduce((sum, item) => sum + item.quantity, 0),
    count: (state) => state.items.reduce((sum, item) => sum + item.quantity, 0),
    totalPrice: (state) => state.items.reduce((sum, item) => sum + (item.price * item.quantity), 0),
    total: (state) => state.items.reduce((sum, item) => sum + (item.price * item.quantity), 0),
    isEmpty: (state) => state.items.length === 0,
    subtotal: () => (item) => item.price * item.quantity,
    getItemQuantity: (state) => (productId) => {
      const item = state.items.find((i) => i.product_id === productId || i.id === productId)
      return item ? item.quantity : 0
    },
  },

  actions: {
    save() {
      storage.set('minishop_cart', this.items)
    },

    /**
     * Add product to cart (REQ-CART-01..03)
     * @param {Object} product
     * @param {number} quantity
     * @returns {{ success: boolean, message?: string }}
     */
    addItem(product, quantity = 1) {
      const pId = product.id || product.product_id
      if (product.stock <= 0) {
        return { success: false, message: 'Stok produk sudah habis.' }
      }

      const existing = this.items.find((i) => i.id === pId || i.product_id === pId)
      const currentQty = existing ? existing.quantity : 0
      const targetQty = currentQty + quantity

      if (targetQty > product.stock) {
        const remaining = product.stock - currentQty
        return {
          success: false,
          message: remaining > 0
            ? `Hanya dapat menambahkan ${remaining} item lagi (stok: ${product.stock}).`
            : `Semua stok (${product.stock}) sudah ada di keranjang.`,
        }
      }

      if (existing) {
        existing.quantity = targetQty
        existing.stock = product.stock
        existing.price = product.price
      } else {
        this.items.push({
          id: pId,
          product_id: pId,
          name: product.name,
          price: product.price,
          stock: product.stock,
          quantity: targetQty,
          image_url: product.image_url,
        })
      }

      this.save()
      return { success: true }
    },

    /**
     * Set exact quantity for an item clamped to [1, stock] (REQ-CART-04..06)
     * @param {number} productId
     * @param {number} quantity
     * @returns {{ quantity: number, clamped: boolean, message?: string }}
     */
    setQuantity(productId, quantity) {
      const existing = this.items.find((i) => i.id === productId || i.product_id === productId)
      if (!existing) return { quantity: 0, clamped: false }

      const raw = parseInt(quantity, 10)
      if (isNaN(raw) || raw < 1) {
        existing.quantity = 1
        this.save()
        return { quantity: 1, clamped: true, message: 'Jumlah minimal adalah 1 item.' }
      }

      if (raw > existing.stock) {
        existing.quantity = existing.stock
        this.save()
        return {
          quantity: existing.stock,
          clamped: true,
          message: `Jumlah dibatasi sesuai sisa stok (${existing.stock} item).`,
        }
      }

      existing.quantity = raw
      this.save()
      return { quantity: raw, clamped: false }
    },

    updateQuantity(productId, quantity) {
      return this.setQuantity(productId, quantity)
    },

    /**
     * Remove item from cart (REQ-CART-07)
     * @param {number} productId
     */
    removeItem(productId) {
      this.items = this.items.filter((i) => i.id !== productId && i.product_id !== productId)
      this.save()
    },

    remove(productId) {
      this.removeItem(productId)
    },

    clearCart() {
      this.items = []
      storage.remove('minishop_cart')
    },

    clear() {
      this.clearCart()
    },

    /**
     * Synchronize cart with 409 INSUFFICIENT_STOCK response details (REQ-CO-11)
     * @param {Array<{product_id: number, name: string, requested: number, available: number}>} insufficientItems
     * @returns {Array<string>} list of adjustment messages
     */
    applyStockConflicts(insufficientItems) {
      const messages = []

      for (const item of insufficientItems) {
        const cartItem = this.items.find((i) => i.id === item.product_id || i.product_id === item.product_id)
        if (!cartItem) continue

        if (item.available <= 0) {
          this.removeItem(item.product_id)
          messages.push(`"${item.name}" dihapus dari keranjang karena stok habis.`)
        } else {
          cartItem.stock = item.available
          cartItem.quantity = Math.min(cartItem.quantity, item.available)
          messages.push(`"${item.name}" disesuaikan menjadi ${cartItem.quantity} item (stok tersedia: ${item.available}).`)
        }
      }

      this.save()
      return messages
    },

    syncWithStockChanges(insufficientItems) {
      return this.applyStockConflicts(insufficientItems)
    },
  },
})
