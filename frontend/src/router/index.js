import { createRouter, createWebHistory } from 'vue-router'
import { storage } from '@/utils/storage'

const routes = [
  {
    path: '/',
    name: 'catalog',
    component: () => import('@/views/CatalogView.vue'),
  },
  {
    path: '/products/:id',
    name: 'product-detail',
    component: () => import('@/views/ProductDetailView.vue'),
    props: true,
  },
  {
    path: '/cart',
    name: 'cart',
    component: () => import('@/views/CartView.vue'),
  },
  {
    path: '/checkout',
    name: 'checkout',
    component: () => import('@/views/CheckoutView.vue'),
  },
  {
    path: '/orders/:orderNumber/success',
    name: 'order-success',
    component: () => import('@/views/OrderSuccessView.vue'),
    props: true,
  },
  {
    path: '/admin/login',
    name: 'admin-login',
    component: () => import('@/views/admin/LoginView.vue'),
  },
  {
    path: '/admin/products',
    name: 'admin-products',
    component: () => import('@/views/admin/ProductListView.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/admin/products/new',
    name: 'admin-products-new',
    component: () => import('@/views/admin/ProductFormView.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/admin/products/:id/edit',
    name: 'admin-products-edit',
    component: () => import('@/views/admin/ProductFormView.vue'),
    meta: { requiresAuth: true },
    props: true,
  },
  {
    path: '/admin/orders',
    name: 'admin-orders',
    component: () => import('@/views/admin/OrderListView.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/admin/orders/:id',
    name: 'admin-orders-detail',
    component: () => import('@/views/admin/OrderDetailView.vue'),
    meta: { requiresAuth: true },
    props: true,
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: () => import('@/views/NotFoundView.vue'),
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    }
    if (to.hash) {
      return { el: to.hash, behavior: 'smooth' }
    }
    // If only query params change on the same page (e.g. category filter, search), keep scroll position
    if (from && to.path === from.path) {
      return false
    }
    return { top: 0 }
  },
})

router.beforeEach((to) => {
  const token = storage.get('minishop_admin_token')
  const isAuthenticated = !!token

  if (to.meta.requiresAuth && !isAuthenticated) {
    return { name: 'admin-login' }
  } else if (to.name === 'admin-login' && isAuthenticated) {
    return { name: 'admin-products' }
  }
  return true
})

export default router
