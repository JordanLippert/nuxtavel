<script setup lang="ts">
const props = defineProps<{ open: boolean; userName: string; userId: number }>()
const emit  = defineEmits<{ close: []; deleted: [] }>()

const users   = useUsersStore()
const toast   = useToast()
const loading = ref(false)

async function handleDelete() {
  loading.value = true
  try {
    await users.deleteUser(props.userId)
    toast.success(`${props.userName} foi excluído.`)
    emit('deleted')
    emit('close')
  } catch {
    toast.error('Erro ao excluir usuário.')
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="open" class="modal-overlay" @click.self="emit('close')">
        <div class="nx-card delete-box">
          <div style="display:flex;gap:14px;">
            <div style="width:40px;height:40px;border-radius:20px;background:var(--nx-danger-bg);color:var(--nx-danger);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 9v4M12 17h.01"/><path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/>
              </svg>
            </div>
            <div>
              <h3 style="font-family:var(--nx-font-serif);font-size:22px;font-weight:400;margin:0 0 6px;">Excluir {{ userName }}?</h3>
              <p style="font-size:13px;color:var(--nx-text-2);margin:0;line-height:1.5;">
                O usuário perderá acesso ao painel imediatamente. Esta ação não pode ser desfeita.
              </p>
            </div>
          </div>
          <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:22px;">
            <button class="nx-btn nx-btn-secondary" @click="emit('close')">Cancelar</button>
            <button class="nx-btn nx-btn-danger" :disabled="loading" @click="handleDelete">
              {{ loading ? 'Excluindo…' : 'Excluir usuário' }}
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
.modal-overlay { position: fixed; inset: 0; background: rgba(10,20,40,0.35); display: flex; align-items: center; justify-content: center; padding: 40px; z-index: 200; }
.delete-box { width: 100%; max-width: 420px; padding: 24px; box-shadow: var(--nx-shadow-lg); }
.modal-enter-active, .modal-leave-active { transition: opacity 0.15s ease; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
</style>
