import http from './http'

export const catalogApi = {
  getCategories() {
    return http.get('/categories').then((res) => res.data)
  },

  getProducts(params = {}) {
    return http.get('/products', { params }).then((res) => res.data)
  },

  getProduct(id) {
    return http.get(`/products/${id}`).then((res) => res.data)
  },
}
