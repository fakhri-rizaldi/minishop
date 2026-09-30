import http from './http'

export const ordersApi = {
  createOrder(payload) {
    return http.post('/orders', payload).then((res) => res.data)
  },

  getOrder(orderNumber) {
    return http.get(`/orders/${orderNumber}`).then((res) => res.data)
  },
}
