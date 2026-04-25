import { defineStore } from 'pinia'
import api from '~/services/api'

export interface NxUser {
  id: number
  name: string
  email: string
  birth_date: string
  role: string | null
  status: string | null
  avatar_url: string | null
  created_at: string
}

interface Pagination {
  total: number
  current_page: number
  last_page: number
  per_page: number
}

export const useUsersStore = defineStore('users', () => {
  const list       = ref<NxUser[]>([])
  const pagination = ref<Pagination>({ total: 0, current_page: 1, last_page: 1, per_page: 10 })
  const loading    = ref(false)
  const current    = ref<NxUser | null>(null)

  async function fetchUsers(page = 1, search = '', role = '', status = '') {
    loading.value = true
    try {
      const { data } = await api.get('/users', { params: { page, search, role, status } })
      list.value       = data.data
      pagination.value = data.meta
    } finally {
      loading.value = false
    }
  }

  async function getUserById(id: number): Promise<NxUser> {
    loading.value = true
    try {
      const { data } = await api.get(`/users/${id}`)
      // Laravel wraps single UserResource responses in {"data": {...}}
      const user = data.data ?? data
      current.value = user
      return user
    } finally {
      loading.value = false
    }
  }

  async function createUser(formData: FormData): Promise<NxUser> {
    const { data } = await api.post('/users', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    return data
  }

  async function updateUser(id: number, formData: FormData): Promise<NxUser> {
    const { data } = await api.post(`/users/${id}/update`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    return data
  }

  async function deleteUser(id: number): Promise<void> {
    await api.delete(`/users/${id}`)
    list.value = list.value.filter((u) => u.id !== id)
  }

  return { list, pagination, loading, current, fetchUsers, getUserById, createUser, updateUser, deleteUser }
})
