import axios from 'axios'

const api = axios.create({
  baseURL: useRuntimeConfig().public.apiUrl as string,
  headers: { 'Content-Type': 'application/json' },
})

api.interceptors.request.use((config) => {
  if (import.meta.client) {
    const token = localStorage.getItem('nx_token')
    if (token) config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

api.interceptors.response.use(
  (res) => res,
  (error) => {
    if (error.response?.status === 401 && import.meta.client) {
      localStorage.removeItem('nx_token')
      window.location.href = '/login'
    }
    return Promise.reject(error)
  }
)

export default api
