<script setup lang="ts">
import api from '~/services/api'

definePageMeta({ middleware: 'auth', layout: 'panel' })

const auth   = useAuthStore()
const users  = useUsersStore()
const toast  = useToastStore()

const activeTab = ref<'profile' | 'security'>('profile')
const saving    = ref(false)

// ── Perfil ────────────────────────────────────────────────────────────────────
const profile = reactive({
  name: '', email: '', birth_date: '', avatar: null as File | null,
})
const avatarPreview   = ref<string | null>(null)
const currentAvatar   = ref<string | null>(null)
const pendingFile     = ref<File | null>(null)
const pendingPreview  = ref<string | null>(null)
const showAvatarModal = ref(false)

const displayAvatar = computed(() => avatarPreview.value ?? currentAvatar.value)
const initials = computed(() =>
  profile.name.split(' ').filter(Boolean).slice(0, 2).map((p) => p[0]).join('').toUpperCase()
)

onMounted(async () => {
  await auth.fetchMe()
  profile.name       = auth.user?.name       ?? ''
  profile.email      = auth.user?.email      ?? ''
  profile.birth_date = auth.user?.birth_date ?? ''
  currentAvatar.value = auth.user?.avatar_url ?? null
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
  profile.avatar = pendingFile.value
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

async function saveProfile() {
  saving.value = true
  try {
    const fd = new FormData()
    fd.append('name',       profile.name)
    fd.append('email',      profile.email)
    fd.append('birth_date', profile.birth_date)
    if (profile.avatar) fd.append('avatar', profile.avatar)

    await users.updateUser(auth.user!.id, fd)
    await auth.fetchMe()
    currentAvatar.value = auth.user?.avatar_url ?? null
    avatarPreview.value = null
    profile.avatar = null
    toast.add('Perfil atualizado com sucesso.')
  } catch (e: any) {
    const errs = e.response?.data?.errors
    toast.add(errs ? Object.values(errs).flat().join(' ') : 'Erro ao salvar perfil.', 'error')
  } finally {
    saving.value = false
  }
}

// ── Segurança ─────────────────────────────────────────────────────────────────
const security = reactive({
  current_password: '', password: '', password_confirmation: '',
})
const savingSec = ref(false)

async function savePassword() {
  if (security.password !== security.password_confirmation) {
    toast.add('As senhas não coincidem.', 'error')
    return
  }
  savingSec.value = true
  try {
    await api.post('/change-password', {
      current_password:      security.current_password,
      password:              security.password,
      password_confirmation: security.password_confirmation,
    })
    toast.add('Senha alterada com sucesso.')
    security.current_password = ''
    security.password = ''
    security.password_confirmation = ''
  } catch (e: any) {
    const errs = e.response?.data?.errors
    toast.add(errs ? Object.values(errs).flat().join(' ') : 'Senha atual incorreta.', 'error')
  } finally {
    savingSec.value = false
  }
}
</script>

<template>
  <div class="nx" style="flex:1;display:flex;flex-direction:column;">
    <AppTopbar title="Ajustes" crumb="Início / Ajustes">
      <button
        v-if="activeTab === 'profile'"
        class="nx-btn nx-btn-primary nx-btn-sm"
        :disabled="saving"
        @click="saveProfile"
      >{{ saving ? 'Salvando…' : 'Salvar perfil' }}</button>
      <button
        v-else
        class="nx-btn nx-btn-primary nx-btn-sm"
        :disabled="savingSec"
        @click="savePassword"
      >{{ savingSec ? 'Salvando…' : 'Alterar senha' }}</button>
    </AppTopbar>

    <div class="settings-body">
      <div class="settings-layout">

        <!-- Tabs laterais -->
        <nav class="settings-nav nx-card">
          <button
            class="settings-nav__item"
            :class="{ 'settings-nav__item--active': activeTab === 'profile' }"
            @click="activeTab = 'profile'"
          >
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="8" r="4"/><path d="M4 20c1-4 4-6 8-6s7 2 8 6"/>
            </svg>
            Perfil
          </button>
          <button
            class="settings-nav__item"
            :class="{ 'settings-nav__item--active': activeTab === 'security' }"
            @click="activeTab = 'security'"
          >
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
            Segurança
          </button>
        </nav>

        <!-- Conteúdo -->
        <div class="settings-content">

          <!-- Aba Perfil -->
          <div v-if="activeTab === 'profile'" class="nx-card settings-card">
            <h3 class="settings-card__title">Informações pessoais</h3>
            <p class="settings-card__desc">Atualize seu nome, e-mail, data de nascimento e foto de perfil.</p>

            <!-- Avatar -->
            <div style="display:flex;align-items:center;gap:16px;margin-bottom:28px;padding-bottom:24px;border-bottom:1px solid var(--nx-border);">
              <div class="nx-avatar nx-avatar-lg" style="width:64px;height:64px;font-size:20px;flex-shrink:0;">
                <img v-if="displayAvatar" :src="displayAvatar" style="width:100%;height:100%;border-radius:50%;object-fit:cover;" />
                <span v-else>{{ initials }}</span>
              </div>
              <div>
                <div style="font-size:14px;font-weight:500;margin-bottom:4px;">{{ profile.name || 'Foto de perfil' }}</div>
                <div style="font-size:12px;color:var(--nx-text-3);margin-bottom:10px;">JPG, PNG ou WebP · Máx. 2 MB</div>
                <label class="nx-btn nx-btn-secondary nx-btn-sm" style="cursor:pointer;">
                  Alterar foto
                  <input type="file" accept="image/*" style="display:none;" @change="onAvatarChange" />
                </label>
              </div>
            </div>

            <div style="display:flex;flex-direction:column;gap:16px;">
              <div>
                <label class="nx-label">Nome completo <span class="nx-required">*</span></label>
                <input v-model="profile.name" class="nx-input" placeholder="Seu nome completo" />
              </div>
              <div>
                <label class="nx-label">E-mail <span class="nx-required">*</span></label>
                <input v-model="profile.email" type="email" class="nx-input" />
                <div class="nx-hint">O e-mail deve ser único no sistema.</div>
              </div>
              <div>
                <label class="nx-label">Data de nascimento <span class="nx-required">*</span></label>
                <div style="position:relative;">
                  <svg style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--nx-text-3);pointer-events:none;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                  </svg>
                  <input v-model="profile.birth_date" class="nx-input" style="padding-left:36px;font-variant-numeric:tabular-nums;" placeholder="dd/mm/aaaa" />
                </div>
              </div>
            </div>
          </div>

          <!-- Aba Segurança -->
          <div v-else class="nx-card settings-card">
            <h3 class="settings-card__title">Alterar senha</h3>
            <p class="settings-card__desc">Use uma senha forte com no mínimo 8 caracteres.</p>

            <div style="display:flex;flex-direction:column;gap:16px;">
              <div>
                <label class="nx-label">Senha atual <span class="nx-required">*</span></label>
                <input v-model="security.current_password" type="password" class="nx-input" placeholder="••••••••" autocomplete="current-password" />
              </div>
              <div style="height:1px;background:var(--nx-border);margin:4px 0;" />
              <div>
                <label class="nx-label">Nova senha <span class="nx-required">*</span></label>
                <input v-model="security.password" type="password" class="nx-input" placeholder="••••••••" autocomplete="new-password" />
                <div class="nx-hint">Mínimo de 8 caracteres.</div>
              </div>
              <div>
                <label class="nx-label">Confirmar nova senha <span class="nx-required">*</span></label>
                <input v-model="security.password_confirmation" type="password" class="nx-input" placeholder="••••••••" autocomplete="new-password" />
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
.settings-body    { padding: 32px; flex: 1; }
.settings-layout  { display: grid; grid-template-columns: 200px 1fr; gap: 24px; max-width: 860px; margin: 0 auto; }

.settings-nav { padding: 8px; display: flex; flex-direction: column; gap: 2px; align-self: start; }
.settings-nav__item {
  display: flex; align-items: center; gap: 10px;
  padding: 9px 12px; border-radius: 6px; font-size: 14px;
  color: var(--nx-text-2); background: transparent; border: none;
  cursor: pointer; text-align: left; transition: background 0.1s;
  width: 100%;
}
.settings-nav__item:hover { background: var(--nx-surface-2); color: var(--nx-text); }
.settings-nav__item--active { font-weight: 500; color: var(--nx-primary); background: var(--nx-primary-50); }

.settings-card { padding: 28px; }
.settings-card__title { font-family: var(--nx-font-serif); font-size: 20px; font-weight: 400; margin: 0 0 4px; letter-spacing: -0.2px; }
.settings-card__desc  { font-size: 13px; color: var(--nx-text-3); margin: 0 0 24px; }

.nx-required { color: var(--nx-danger); cursor: help; position: relative; }
.nx-required::after {
  content: 'Campo obrigatório';
  position: absolute; bottom: calc(100% + 6px); left: 50%; transform: translateX(-50%);
  background: #1a1a17; color: #fff; font-size: 11px; font-weight: 400;
  white-space: nowrap; padding: 4px 8px; border-radius: 5px;
  pointer-events: none; opacity: 0; transition: opacity .15s;
}
.nx-required:hover::after { opacity: 1; }

@media (max-width: 767px) {
  .settings-body   { padding: 16px; }
  .settings-layout { grid-template-columns: 1fr; }
  .settings-nav    { flex-direction: row; }
}

.avatar-modal-overlay {
  position: fixed; inset: 0; background: rgba(0,0,0,0.45);
  display: flex; align-items: center; justify-content: center;
  z-index: 999; backdrop-filter: blur(2px);
}
.avatar-modal {
  background: var(--nx-surface); border: 1px solid var(--nx-border);
  border-radius: var(--nx-r-xl); box-shadow: var(--nx-shadow-lg);
  width: 100%; max-width: 480px; padding: 24px;
  display: flex; flex-direction: column; gap: 16px;
}
.avatar-modal__header {
  display: flex; align-items: center; justify-content: space-between;
  font-size: 14px; font-weight: 500; color: var(--nx-text);
}
.avatar-modal__preview {
  display: flex; gap: 20px; align-items: center; justify-content: center;
  background: var(--nx-surface-2); border: 1px solid var(--nx-border);
  border-radius: var(--nx-r-lg); padding: 24px;
}
.avatar-modal__img { width: 160px; height: 160px; object-fit: cover; border-radius: var(--nx-r-md); border: 1px solid var(--nx-border); }
.avatar-modal__circle { width: 100px; height: 100px; border-radius: 50%; overflow: hidden; border: 2px solid var(--nx-border-strong); flex-shrink: 0; }
.avatar-modal__circle img { width: 100%; height: 100%; object-fit: cover; }
.avatar-modal__hint { font-size: 12px; color: var(--nx-text-3); margin: 0; text-align: center; }
.avatar-modal__actions { display: flex; justify-content: flex-end; gap: 8px; }
</style>
