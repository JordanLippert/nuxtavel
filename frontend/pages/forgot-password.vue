<script setup lang="ts">
definePageMeta({ middleware: 'guest', layout: false })

import api from '~/services/api'

const router = useRouter()
const toast  = useToastStore()

const form    = reactive({ email: '', password: '' })
const loading = ref(false)
const showPassword = ref(false)

async function handleSubmit() {
  loading.value = true
  try {
    await api.post('/forgot-password', { email: form.email, password: form.password })
    toast.add('Senha redefinida com sucesso.')
    setTimeout(() => router.push('/login'), 1500)
  } catch (e: any) {
    const msg = e.response?.data?.message ?? 'Erro ao redefinir senha.'
    toast.add(msg, 'error')
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="nx login-split">
    <!-- Brand -->
    <div class="login-split__brand" style="position:relative;overflow:hidden;">
      <svg width="100%" height="100%" style="position:absolute;inset:0;opacity:0.08;pointer-events:none;" aria-hidden="true">
        <defs>
          <pattern id="forgot-stripes" width="8" height="8" patternUnits="userSpaceOnUse" patternTransform="rotate(45)">
            <line x1="0" y1="0" x2="0" y2="8" stroke="#ffffff" stroke-width="6"/>
          </pattern>
        </defs>
        <rect width="100%" height="100%" fill="url(#forgot-stripes)"/>
      </svg>

      <div style="position:relative;" class="login-split__brand-logo">
        <NxLogo :size="26" variant="mesh" color="#ffffff" />
        <span>nuxtavel</span>
      </div>
      <div style="position:relative;">
        <div class="login-split__brand-headline">
          Nova senha<br/>em <em>segundos</em>.
        </div>
        <p class="login-split__brand-sub">
          Informe o e-mail cadastrado e escolha uma nova senha.
        </p>
      </div>
      <div style="position:relative;" class="login-split__brand-footer">© 2026 Nuxtavel · desafio NDS</div>
    </div>

    <!-- Formulário -->
    <div class="login-split__form-side">
      <div class="login-split__mobile-logo">
        <NxLogo :size="22" />
        <span>nuxtavel</span>
      </div>

      <div class="login-split__form-box">
        <h1 class="login-split__title">Redefinir senha</h1>
        <p class="login-split__subtitle">Informe seu e-mail e a nova senha.</p>

        <form style="display:flex;flex-direction:column;gap:16px;" @submit.prevent="handleSubmit">
          <div>
            <label class="nx-label">E-mail</label>
            <div style="position:relative;">
              <svg style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--nx-text-3);pointer-events:none;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
              </svg>
              <input
                v-model="form.email"
                type="email"
                class="nx-input"
                style="padding-left:38px;"
                placeholder="nome@empresa.com"
                required
              />
            </div>
          </div>

          <div>
            <label class="nx-label">Nova senha</label>
            <div style="position:relative;">
              <svg style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--nx-text-3);pointer-events:none;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
              </svg>
              <input
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                class="nx-input"
                style="padding-left:38px;padding-right:38px;"
                placeholder="Mínimo 8 caracteres"
                required
              />
              <button
                type="button"
                :aria-label="showPassword ? 'Ocultar senha' : 'Mostrar senha'"
                style="position:absolute;right:8px;top:50%;transform:translateY(-50%);background:transparent;border:none;cursor:pointer;color:var(--nx-text-3);padding:6px;display:flex;"
                @click="showPassword = !showPassword"
              >
                <svg v-if="showPassword" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>
                </svg>
                <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                </svg>
              </button>
            </div>
          </div>

          <button type="submit" class="nx-btn nx-btn-primary" style="height:40px;font-size:14px;" :disabled="loading">
            {{ loading ? 'Redefinindo…' : 'Redefinir senha' }}
          </button>
        </form>

        <p style="text-align:center;font-size:13px;color:var(--nx-text-3);margin-top:20px;">
          Lembrou a senha?
          <NuxtLink to="/login" style="color:var(--nx-primary);text-decoration:none;">Voltar para o login</NuxtLink>
        </p>
      </div>
    </div>
  </div>
</template>

<style scoped>
.login-split { display: flex; height: 100vh; overflow: hidden; background: var(--nx-bg); }
.login-split__brand { flex: 1; background: var(--nx-primary); color: #fff; padding: 48px; display: flex; flex-direction: column; justify-content: space-between; }
.login-split__brand-logo { display: flex; align-items: center; gap: 10px; font-family: var(--nx-font-sans); font-weight: 300; font-size: 26px; letter-spacing: -0.6px; }
.login-split__brand-headline { font-family: var(--nx-font-serif); font-size: 52px; line-height: 1.05; letter-spacing: -1px; margin-bottom: 16px; }
.login-split__brand-headline em { font-style: italic; }
.login-split__brand-sub { font-size: 15px; opacity: 0.8; max-width: 400px; line-height: 1.55; }
.login-split__brand-footer { font-size: 12px; opacity: 0.6; font-family: var(--nx-font-mono); }
.login-split__form-side { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px; }
.login-split__mobile-logo { display: none; align-items: center; gap: 8px; margin-bottom: 32px; font-family: var(--nx-font-sans); font-weight: 300; font-size: 22px; letter-spacing: -0.6px; color: #0d2236; }
.login-split__form-box { width: 100%; max-width: 360px; }
.login-split__title { font-family: var(--nx-font-serif); font-size: 34px; font-weight: 400; margin: 0 0 6px; letter-spacing: -0.4px; }
.login-split__subtitle { font-size: 14px; color: var(--nx-text-2); margin: 0 0 28px; }
@media (max-width: 1023px) {
  .login-split__brand { display: none; }
  .login-split__mobile-logo { display: flex; }
  .login-split__form-side { justify-content: flex-start; padding-top: 48px; }
}
</style>
