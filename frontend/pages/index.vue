<script setup lang="ts">
definePageMeta({ middleware: 'auth', layout: 'panel' })

const auth  = useAuthStore()
const users = useUsersStore()
const toast = useToast()

onMounted(async () => {
  await auth.fetchMe()
  await users.fetchUsers()
  checkBirthday()
})

function checkBirthday() {
  const bd = auth.user?.birth_date
  if (!bd) return
  const [day, month] = bd.split('/')
  const now = new Date()
  if (Number(day) === now.getDate() && Number(month) === now.getMonth() + 1) {
    toast.birthday(`🎂 Feliz aniversário, ${auth.user?.name}! Que seu dia seja incrível.`)
  }
}

const activeCount = computed(() =>
  users.list.filter((u) => u.created_at).length
)
</script>

<template>
  <div class="dashboard nx">
    <AppTopbar title="Painel" crumb="Início" />

    <div class="dashboard__body">
      <div class="dashboard__welcome nx-card">
        <h2 class="dashboard__welcome-title">
          Seja bem-vindo ao Nuxtavel, {{ auth.user?.name?.split(' ')[0] }}!
        </h2>
        <p style="font-size:14px;color:var(--nx-text-2);margin:8px 0 0;line-height:1.6;">
          Gerencie sua equipe, crie novos usuários e acompanhe o painel administrativo.
        </p>
      </div>

      <div class="dashboard__stats">
        <div class="nx-card dashboard__stat">
          <div class="dashboard__stat-value">{{ users.pagination.total }}</div>
          <div class="dashboard__stat-label">Total de usuários</div>
        </div>
        <div class="nx-card dashboard__stat">
          <div class="dashboard__stat-value">{{ activeCount }}</div>
          <div class="dashboard__stat-label">Usuários ativos</div>
        </div>
        <div class="nx-card dashboard__stat">
          <div class="dashboard__stat-value">{{ users.list.length }}</div>
          <div class="dashboard__stat-label">Nesta página</div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.dashboard { flex: 1; display: flex; flex-direction: column; }
.dashboard__body { padding: 32px; display: flex; flex-direction: column; gap: 24px; }
.dashboard__welcome { padding: 28px 32px; }
.dashboard__welcome-title { font-family: var(--nx-font-serif); font-size: 28px; font-weight: 400; margin: 0; letter-spacing: -0.3px; }
.dashboard__stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
.dashboard__stat { padding: 24px; }
.dashboard__stat-value { font-family: var(--nx-font-serif); font-size: 40px; font-weight: 400; color: var(--nx-primary); letter-spacing: -1px; }
.dashboard__stat-label { font-size: 13px; color: var(--nx-text-3); margin-top: 4px; }

@media (max-width: 639px) {
  .dashboard__body { padding: 16px; }
  .dashboard__stats { grid-template-columns: 1fr; }
}
</style>
