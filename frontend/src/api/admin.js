import http from './http'

export const adminApi = {
  login(credentials) {
    return http.post('/admin/login', credentials).then((res) => res.data)
  },

  logout() {
    return http.post('/admin/logout')
  },

  getProducts(params = {}) {
    return http.get('/admin/products', { params }).then((res) => res.data)
  },

  getProduct(id) {
    return http.get(`/admin/products/${id}`).then((res) => res.data)
  },

  createProduct(payload) {
    return http.post('/admin/products', payload).then((res) => res.data)
  },

  updateProduct(id, payload) {
    return http.put(`/admin/products/${id}`, payload).then((res) => res.data)
  },

  deleteProduct(id) {
    return http.delete(`/admin/products/${id}`)
  },

  getOrders(params = {}) {
    return http.get('/admin/orders', { params }).then((res) => res.data)
  },

  getOrder(id) {
    return http.get(`/admin/orders/${id}`).then((res) => res.data)
  },
}
