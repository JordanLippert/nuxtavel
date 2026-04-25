<script setup lang="ts">
definePageMeta({ middleware: 'guest', layout: false })

const auth   = useAuthStore()
const router = useRouter()

const form = reactive({ name: '', email: '', password: '', birth_date: '' })

function maskDate(e: Event) {
  const input = e.target as HTMLInputElement
  let v = input.value.replace(/\D/g, '').slice(0, 8)
  if (v.length >= 5) v = v.slice(0,2) + '/' + v.slice(2,4) + '/' + v.slice(4)
  else if (v.length >= 3) v = v.slice(0,2) + '/' + v.slice(2)
  form.birth_date = v
}
const loading = ref(false)
const error   = ref('')

async function handleRegister() {
  if (loading.value) return
  error.value = ''
  loading.value = true
  try {
    await auth.register(form)
    router.push('/')
  } catch (e: any) {
    const errs = e.response?.data?.errors
    error.value = errs
      ? Object.values(errs).flat().join('\n')
      : e.response?.data?.message ?? 'Erro ao criar conta.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="nx login-split">
    <div class="login-split__brand" style="position:relative;overflow:hidden;">
      <!-- Stripe overlay -->
      <svg width="100%" height="100%" style="position:absolute;inset:0;opacity:0.08;pointer-events:none;" aria-hidden="true">
        <defs>
          <pattern id="register-stripes" width="8" height="8" patternUnits="userSpaceOnUse" patternTransform="rotate(45)">
            <line x1="0" y1="0" x2="0" y2="8" stroke="#ffffff" stroke-width="6"/>
          </pattern>
        </defs>
        <rect width="100%" height="100%" fill="url(#register-stripes)"/>
      </svg>

      <div style="position:relative;" class="login-split__brand-logo">
        <NxLogo :size="26" variant="mesh" color="#ffffff" />
        <span>nuxtavel</span>
      </div>
      <div style="position:relative;">
        <div class="login-split__brand-headline">
          Crie sua<br/>conta <em>agora</em>.
        </div>
        <p class="login-split__brand-sub">Acesse o painel e gerencie sua equipe em minutos.</p>
      </div>
      <div style="position:relative;" class="login-split__brand-footer">© 2026 Nuxtavel · desafio NDS</div>
    </div>

    <div class="login-split__form-side">
      <div class="login-split__mobile-logo">
        <NxLogo :size="22" />
        <span>nuxtavel</span>
      </div>

      <div class="login-split__form-box">
        <h1 class="login-split__title">Criar conta</h1>
        <p class="login-split__subtitle">Preencha os dados para começar.</p>

        <div v-if="error" class="login-error-banner">{{ error }}</div>

        <form style="display:flex;flex-direction:column;gap:16px;" @submit.prevent="handleRegister">
          <div>
            <label class="nx-label">Nome completo <span style="color:var(--nx-danger)">*</span></label>
            <input v-model="form.name" type="text" class="nx-input" placeholder="Ana Beatriz Cardoso" required />
          </div>
          <div>
            <label class="nx-label">E-mail <span style="color:var(--nx-danger)">*</span></label>
            <input v-model="form.email" type="email" class="nx-input" placeholder="nome@empresa.com" required />
          </div>
          <div>
            <label class="nx-label">Data de nascimento <span style="color:var(--nx-danger)">*</span></label>
            <input :value="form.birth_date" type="text" class="nx-input" placeholder="dd/mm/aaaa" maxlength="10" required @input="maskDate" />
          </div>
          <div>
            <label class="nx-label">Senha <span style="color:var(--nx-danger)">*</span></label>
            <input v-model="form.password" type="password" class="nx-input" placeholder="Mínimo 8 caracteres, letras e números" required />
          </div>
          <button type="submit" class="nx-btn nx-btn-primary" style="height:40px;font-size:14px;" :disabled="loading">
            {{ loading ? 'Criando conta…' : 'Criar conta' }}
          </button>
        </form>

        <p style="text-align:center;font-size:13px;color:var(--nx-text-3);margin-top:20px;">
          Já tem conta?
          <NuxtLink to="/login" style="color:var(--nx-primary);text-decoration:none;">Entrar</NuxtLink>
        </p>
      </div>
    </div>
  </div>
</template>

<style scoped>
.login-split { display: flex; height: 100vh; background: var(--nx-bg); }
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
.login-error-banner { padding: 12px; background: var(--nx-danger-bg); border: 1px solid rgba(179,38,30,0.2); border-radius: 8px; margin-bottom: 20px; color: var(--nx-danger); font-size: 13px; line-height: 1.4; white-space: pre-line; }
@media (max-width: 1023px) {
  .login-split__brand { display: none; }
  .login-split__mobile-logo { display: flex; }
  .login-split__form-side { justify-content: flex-start; padding-top: 48px; }
}
</style>
