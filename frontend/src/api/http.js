import axios from 'axios'
import { storage } from '@/utils/storage'

const http = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api',
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
})

// Request interceptor: attach Sanctum Bearer token
http.interceptors.request.use((config) => {
  const token = storage.get('minishop_admin_token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

// Response interceptor: handle 401 on admin routes
http.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      const isAuthUrl = error.config?.url?.includes('/admin/login')
      if (!isAuthUrl && window.location.pathname.startsWith('/admin')) {
        storage.remove('minishop_admin_token')
        storage.remove('minishop_admin_user')
        window.location.href = '/admin/login'
      }
    }
    return Promise.reject(error)
  }
)

export default http
