<script setup lang="ts">
definePageMeta({ middleware: 'auth', layout: 'panel' })

const route  = useRoute()
const router = useRouter()
const users  = useUsersStore()
const toast  = useToastStore()

const userId  = Number(route.params.id)
const loading = ref(true)
const saving  = ref(false)

const form = reactive({
  name: '', email: '', birth_date: '', role: 'Editor', status: 'Ativo', avatar: null as File | null,
})
const avatarPreview  = ref<string | null>(null)
const currentAvatar  = ref<string | null>(null)
const pendingFile    = ref<File | null>(null)
const pendingPreview = ref<string | null>(null)
const showAvatarModal = ref(false)

onMounted(async () => {
  try {
    const user = await users.getUserById(userId)
    form.name       = user.name
    form.email      = user.email
    form.birth_date = user.birth_date
    form.role       = user.role   ?? 'Editor'
    form.status     = user.status ?? 'Ativo'
    currentAvatar.value = user.avatar_url
  } finally {
    loading.value = false
  }
})

function onAvatarChange(e: Event) {
  const file = (e.target as HTMLInputElement).files?.[0]
  if (!file) return
  pendingFile.value    = file
  pendingPreview.value = URL.createObjectURL(file)
  showAvatarModal.value = true
  ;(e.target as HTMLInputElement).value = ''
}

function confirmAvatar() {
  form.avatar = pendingFile.value
  avatarPreview.value = pendingPreview.value
  showAvatarModal.value = false
  pendingFile.value = null
  pendingPreview.value = null
}

function cancelAvatar() {
  showAvatarModal.value = false
  pendingFile.value = null
  pendingPreview.value = null
}

const initials = computed(() =>
  form.name.split(' ').filter(Boolean).slice(0, 2).map((p) => p[0]).join('').toUpperCase()
)

const displayAvatar = computed(() => avatarPreview.value ?? currentAvatar.value)

async function handleSave() {
  saving.value = true
  try {
    const fd = new FormData()
    fd.append('name',       form.name)
    fd.append('email',      form.email)
    fd.append('birth_date', form.birth_date)
    fd.append('role',       form.role)
    fd.append('status',     form.status)
    if (form.avatar) fd.append('avatar', form.avatar)

    await users.updateUser(userId, fd)
    toast.add('Usuário atualizado com sucesso.')
    setTimeout(() => router.push('/users'), 1500)
  } catch (e: any) {
    const errs = e.response?.data?.errors
    toast.add(errs ? Object.values(errs).flat().join(' ') : 'Erro ao salvar.', 'error')
  } finally {
    saving.value = false
  }
}
</script>

<template>
  <div class="nx" style="flex:1;display:flex;flex-direction:column;">
    <AppTopbar :title="form.name || 'Editar usuário'" crumb="Início / Usuários / Editar">
      <button class="nx-btn nx-btn-secondary nx-btn-sm" @click="router.push('/users')">Cancelar</button>
      <button class="nx-btn nx-btn-primary nx-btn-sm" :disabled="saving" @click="handleSave">
        {{ saving ? 'Salvando…' : 'Salvar' }}
      </button>
    </AppTopbar>

    <div class="edit-body">
      <!-- Skeleton -->
      <div v-if="loading" style="max-width:680px;margin:0 auto;">
        <div class="nx-card edit-card">
          <div v-for="i in 4" :key="i" style="margin-bottom:20px;">
            <div style="width:100px;height:12px;background:var(--nx-surface-2);border-radius:4px;margin-bottom:8px;"/>
            <div style="width:100%;height:38px;background:var(--nx-surface-2);border-radius:8px;"/>
          </div>
        </div>
        <div style="display:flex;flex-direction:column;gap:16px;">
          <div class="nx-card" style="padding:18px;height:120px;background:var(--nx-surface-2);"/>
        </div>
      </div>

      <div v-else style="max-width:680px;margin:0 auto;">
      <div class="nx-card edit-card">
        <!-- Avatar -->
        <div style="display:flex;align-items:center;gap:16px;margin-bottom:24px;">
          <div class="nx-avatar nx-avatar-lg" style="width:56px;height:56px;font-size:18px;">
            <img v-if="displayAvatar" :src="displayAvatar" style="width:100%;height:100%;border-radius:50%;object-fit:cover;" />
            <span v-else>{{ initials }}</span>
          </div>
          <label class="nx-btn nx-btn-secondary nx-btn-sm" style="cursor:pointer;">
            Enviar foto
            <input type="file" accept="image/*" style="display:none;" @change="onAvatarChange" />
          </label>
        </div>

        <div style="display:flex;flex-direction:column;gap:16px;">
          <div>
            <label class="nx-label">Nome completo <span class="nx-required">*</span></label>
            <input v-model="form.name" class="nx-input" placeholder="Ex. Ana Beatriz Cardoso" />
          </div>
          <div>
            <label class="nx-label">E-mail <span class="nx-required">*</span></label>
            <input v-model="form.email" type="email" class="nx-input" />
            <div class="nx-hint">O e-mail deve ser único.</div>
          </div>
          <div>
            <label class="nx-label">Data de nascimento <span class="nx-required">*</span></label>
            <div style="position:relative;">
              <svg style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--nx-text-3);pointer-events:none;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
              </svg>
              <input v-model="form.birth_date" class="nx-input" style="padding-left:36px;font-variant-numeric:tabular-nums;" placeholder="dd/mm/aaaa" />
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
        </div>
      </div>

      </div>
    </div>

    <!-- Modal de preview de avatar -->
    <Teleport to="body">
      <div v-if="showAvatarModal" class="avatar-modal-overlay" @click.self="cancelAvatar">
        <div class="avatar-modal">
          <div class="avatar-modal__header">
            <span>Pré-visualização da foto</span>
            <button class="nx-btn nx-btn-ghost nx-btn-icon" @click="cancelAvatar">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
          </div>
          <div class="avatar-modal__preview">
            <img :src="pendingPreview!" alt="Preview" class="avatar-modal__img" />
            <div class="avatar-modal__circle">
              <img :src="pendingPreview!" alt="Preview circular" />
            </div>
          </div>
          <p class="avatar-modal__hint">À esquerda como a imagem original, à direita como ficará no perfil.</p>
          <div class="avatar-modal__actions">
            <button class="nx-btn nx-btn-secondary" @click="cancelAvatar">Cancelar</button>
            <button class="nx-btn nx-btn-primary" @click="confirmAvatar">Usar esta foto</button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<style scoped>
.edit-body { padding: 32px; }
.edit-card { padding: 28px; }
.nx-required {
  color: var(--nx-danger);
  cursor: help;
  position: relative;
}
.nx-required::after {
  content: 'Campo obrigatório';
  position: absolute;
  bottom: calc(100% + 6px);
  left: 50%;
  transform: translateX(-50%);
  background: #1a1a17;
  color: #fff;
  font-size: 11px;
  font-weight: 400;
  white-space: nowrap;
  padding: 4px 8px;
  border-radius: 5px;
  pointer-events: none;
  opacity: 0;
  transition: opacity .15s;
}
.nx-required:hover::after {
  opacity: 1;
}

@media (max-width: 767px) {
  .edit-body { padding: 16px; }
  .edit-body > div { grid-template-columns: 1fr !important; }
}

.avatar-modal-overlay {
  position: fixed; inset: 0;
  background: rgba(0,0,0,0.45);
  display: flex; align-items: center; justify-content: center;
  z-index: 999;
  backdrop-filter: blur(2px);
}
.avatar-modal {
  background: var(--nx-surface);
  border: 1px solid var(--nx-border);
  border-radius: var(--nx-r-xl);
  box-shadow: var(--nx-shadow-lg);
  width: 100%;
  max-width: 480px;
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 16px;
}
.avatar-modal__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 14px;
  font-weight: 500;
  color: var(--nx-text);
}
.avatar-modal__preview {
  display: flex;
  gap: 20px;
  align-items: center;
  justify-content: center;
  background: var(--nx-surface-2);
  border: 1px solid var(--nx-border);
  border-radius: var(--nx-r-lg);
  padding: 24px;
}
.avatar-modal__img {
  width: 160px;
  height: 160px;
  object-fit: cover;
  border-radius: var(--nx-r-md);
  border: 1px solid var(--nx-border);
}
.avatar-modal__circle {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  overflow: hidden;
  border: 2px solid var(--nx-border-strong);
  flex-shrink: 0;
}
.avatar-modal__circle img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.avatar-modal__hint {
  font-size: 12px;
  color: var(--nx-text-3);
  margin: 0;
  text-align: center;
}
.avatar-modal__actions {
  display: flex;
  justify-content: flex-end;
  gap: 8px;
}
</style>
