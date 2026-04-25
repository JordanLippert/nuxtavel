import { defineStore } from 'pinia'
import api from '~/services/api'

interface AuthUser {
  id: number
  name: string
  email: string
  birth_date: string
  avatar_url: string | null
}

export const useAuthStore = defineStore('auth', () => {
  const token = ref<string | null>(null)
  const user  = ref<AuthUser | null>(null)

  function init() {
    if (import.meta.client) {
      token.value = localStorage.getItem('nx_token')
    }
  }

  async function login(email: string, password: string) {
    const { data } = await api.post('/login', { email, password })
    token.value = data.token
    user.value  = data.user
    if (import.meta.client) localStorage.setItem('nx_token', data.token)
  }

  async function register(payload: {
    name: string
    email: string
    password: string
    birth_date: string
  }) {
    const { data } = await api.post('/register', payload)
    token.value = data.token
    user.value  = data.user
    if (import.meta.client) localStorage.setItem('nx_token', data.token)
  }

  async function logout() {
    await api.post('/logout').catch(() => {})
    token.value = null
    user.value  = null
    if (import.meta.client) localStorage.removeItem('nx_token')
  }

  async function fetchMe() {
    const { data } = await api.get('/users/me')
    user.value = data
  }

  const isAuthenticated = computed(() => !!token.value)

  return { token, user, isAuthenticated, init, login, register, logout, fetchMe }
})
