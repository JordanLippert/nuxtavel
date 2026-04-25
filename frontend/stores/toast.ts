import { defineStore } from 'pinia'

export type ToastVariant = 'success' | 'error' | 'birthday'

interface Toast {
  id: number
  message: string
  variant: ToastVariant
}

export const useToastStore = defineStore('toast', () => {
  const toasts = ref<Toast[]>([])
  let counter = 0

  function add(message: string, variant: ToastVariant = 'success') {
    const id = ++counter
    const duration = variant === 'birthday' ? 8000 : 4000
    toasts.value.push({ id, message, variant })
    setTimeout(() => remove(id), duration)
  }

  function remove(id: number) {
    toasts.value = toasts.value.filter((t) => t.id !== id)
  }

  return { toasts, add, remove }
})
