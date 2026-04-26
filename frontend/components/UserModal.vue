<script setup lang="ts">
const props = defineProps<{ open: boolean }>()
const emit  = defineEmits<{ close: []; created: [] }>()

const users  = useUsersStore()
const toast  = useToast()
const loading = ref(false)

const form = reactive({
  name: '', email: '', birth_date: '', role: 'Editor', status: 'Ativo', password: '', avatar: null as File | null,
})

function maskDate(e: Event) {
  const input = e.target as HTMLInputElement
  let v = input.value.replace(/\D/g, '').slice(0, 8)
  if (v.length >= 5) v = v.slice(0, 2) + '/' + v.slice(2, 4) + '/' + v.slice(4)
  else if (v.length >= 3) v = v.slice(0, 2) + '/' + v.slice(2)
  form.birth_date = v
}
const avatarPreview = ref<string | null>(null)

function onAvatarChange(e: Event) {
  const file = (e.target as HTMLInputElement).files?.[0]
  if (!file) return
  form.avatar = file
  avatarPreview.value = URL.createObjectURL(file)
}

const initials = computed(() =>
  form.name.split(' ').filter(Boolean).slice(0, 2).map((p) => p[0]).join('').toUpperCase() || '?'
)

async function handleSubmit() {
  loading.value = true
  try {
    const fd = new FormData()
    fd.append('name',       form.name)
    fd.append('email',      form.email)
    fd.append('birth_date', form.birth_date)
    fd.append('role',       form.role)
    fd.append('status',     form.status)
    fd.append('password',   form.password)
    if (form.avatar) fd.append('avatar', form.avatar)

    await users.createUser(fd)
    toast.success(`Usuário ${form.name} criado com sucesso.`)
    emit('created')
    emit('close')
  } catch (e: any) {
    const errs = e.response?.data?.errors
    toast.error(errs ? Object.values(errs).flat().join(' ') : 'Erro ao criar usuário.')
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="open" class="modal-overlay" @click.self="emit('close')">
        <div class="modal-box nx-card">
          <div class="modal-header">
            <h2 class="modal-title">Novo usuário</h2>
            <button class="nx-btn nx-btn-ghost nx-btn-icon" @click="emit('close')">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 6l12 12M18 6 6 18"/>
              </svg>
            </button>
          </div>

          <div class="modal-body">
            <!-- Avatar -->
            <div style="display:flex;align-items:center;gap:16px;margin-bottom:8px;">
              <div class="nx-avatar nx-avatar-lg" style="width:56px;height:56px;font-size:18px;">
                <img v-if="avatarPreview" :src="avatarPreview" style="width:100%;height:100%;border-radius:50%;object-fit:cover;" />
                <span v-else>{{ initials }}</span>
              </div>
              <label class="nx-btn nx-btn-secondary nx-btn-sm" style="cursor:pointer;">
                Enviar foto
                <input type="file" accept="image/*" style="display:none;" @change="onAvatarChange" />
              </label>
            </div>

            <div style="display:flex;flex-direction:column;gap:16px;">
              <div>
                <label class="nx-label">Nome completo <span style="color:var(--nx-danger)">*</span></label>
                <input v-model="form.name" class="nx-input" placeholder="Ex. Ana Beatriz Cardoso" required />
              </div>
              <div>
                <label class="nx-label">E-mail <span style="color:var(--nx-danger)">*</span></label>
                <input v-model="form.email" type="email" class="nx-input" placeholder="nome@empresa.com" required />
                <div class="nx-hint">O e-mail deve ser único.</div>
              </div>
              <div>
                <label class="nx-label">Data de nascimento <span style="color:var(--nx-danger)">*</span></label>
                <div style="position:relative;">
                  <svg style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--nx-text-3);pointer-events:none;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                  </svg>
                  <input :value="form.birth_date" class="nx-input" style="padding-left:36px;font-variant-numeric:tabular-nums;" placeholder="dd/mm/aaaa" maxlength="10" required @input="maskDate" />
                </div>
              </div>
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                  <label class="nx-label">Papel</label>
                  <div style="position:relative;">
                    <select v-model="form.role" class="nx-input" style="appearance:none;padding-right:32px;cursor:pointer;">
                      <option>Admin</option>
                      <option>Editor</option>
                      <option>Viewer</option>
                    </select>
                    <svg style="position:absolute;right:10px;top:50%;transform:translateY(-50%);pointer-events:none;color:var(--nx-text-3);" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                      <path d="m6 9 6 6 6-6"/>
                    </svg>
                  </div>
                </div>
                <div>
                  <label class="nx-label">Status</label>
                  <div style="position:relative;">
                    <select v-model="form.status" class="nx-input" style="appearance:none;padding-right:32px;cursor:pointer;">
                      <option>Ativo</option>
                      <option>Inativo</option>
                    </select>
                    <svg style="position:absolute;right:10px;top:50%;transform:translateY(-50%);pointer-events:none;color:var(--nx-text-3);" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                      <path d="m6 9 6 6 6-6"/>
                    </svg>
                  </div>
                </div>
              </div>
              <div>
                <label class="nx-label">Senha provisória <span style="color:var(--nx-danger)">*</span></label>
                <input v-model="form.password" type="password" class="nx-input" placeholder="Mínimo 8 caracteres" required />
                <div class="nx-hint">O usuário receberá um convite por e-mail para redefinir.</div>
              </div>
            </div>
          </div>

          <div style="padding: 0 24px 12px;">
            <p style="font-size:12px;color:var(--nx-text-3);margin:0;"><span style="color:var(--nx-danger)">*</span> Campos obrigatórios</p>
          </div>

          <div class="modal-footer">
            <button class="nx-btn nx-btn-secondary" @click="emit('close')">Cancelar</button>
            <button class="nx-btn nx-btn-primary" :disabled="loading" @click="handleSubmit">
              {{ loading ? 'Criando…' : 'Criar usuário' }}
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
.modal-overlay { position: fixed; inset: 0; background: rgba(10,20,40,0.35); display: flex; align-items: center; justify-content: center; padding: 40px; z-index: 200; }
.modal-box { width: 100%; max-width: 520px; display: flex; flex-direction: column; max-height: 90vh; }
.modal-header { padding: 20px 24px 16px; border-bottom: 1px solid var(--nx-border); display: flex; justify-content: space-between; align-items: center; }
.modal-title { font-family: var(--nx-font-serif); font-size: 24px; font-weight: 400; margin: 0; }
.modal-body { padding: 24px; overflow-y: auto; flex: 1; }
.modal-footer { padding: 14px 24px; border-top: 1px solid var(--nx-border); display: flex; justify-content: flex-end; gap: 8px; background: var(--nx-surface-2); }

.modal-enter-active, .modal-leave-active { transition: opacity 0.15s ease; }
.modal-enter-from, .modal-leave-to { opacity: 0; }

@media (max-width: 639px) {
  .modal-overlay { padding: 0; align-items: flex-end; }
  .modal-box { max-width: 100%; border-radius: var(--nx-r-lg) var(--nx-r-lg) 0 0; max-height: 92vh; }
}
</style>
