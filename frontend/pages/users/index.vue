<script setup lang="ts">
import api from '~/services/api'

definePageMeta({ middleware: 'auth', layout: 'panel' })

const users  = useUsersStore()
const auth   = useAuthStore()
const toast  = useToastStore()
const router = useRouter()

const search       = ref('')
const roleFilter   = ref('')
const statusFilter = ref('')
const showModal    = ref(false)
const deleteTarget = ref<{ id: number; name: string } | null>(null)
const openDropdown = ref<'role' | 'status' | null>(null)

// Seleção de linhas
const selected     = ref<Set<number>>(new Set())
const allChecked   = computed(() => users.list.length > 0 && users.list.every(u => selected.value.has(u.id)))
const someChecked  = computed(() => users.list.some(u => selected.value.has(u.id)) && !allChecked.value)

function toggleAll() {
  if (allChecked.value) {
    users.list.forEach(u => selected.value.delete(u.id))
  } else {
    users.list.forEach(u => selected.value.add(u.id))
  }
}

function toggleOne(id: number) {
  if (selected.value.has(id)) selected.value.delete(id)
  else selected.value.add(id)
}

// Limpa seleção ao trocar de página/filtro
watch(() => users.list, () => { selected.value = new Set() })

// Indeterminate no checkbox do header (não pode ser feito via attr no Vue)
const headerCheckbox = ref<HTMLInputElement | null>(null)
watchEffect(() => {
  if (headerCheckbox.value) headerCheckbox.value.indeterminate = someChecked.value
})

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
    toast.add(`🎂 Feliz aniversário, ${auth.user?.name}!`)
  }
}

function fetchFiltered(page = 1) {
  users.fetchUsers(page, search.value, roleFilter.value, statusFilter.value)
}

let searchTimeout: ReturnType<typeof setTimeout>
watch(search, () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => fetchFiltered(1), 400)
})

function setRole(val: string) {
  roleFilter.value = val
  openDropdown.value = null
  fetchFiltered(1)
}

function setStatus(val: string) {
  statusFilter.value = val
  openDropdown.value = null
  fetchFiltered(1)
}

function toggleDropdown(name: 'role' | 'status') {
  openDropdown.value = openDropdown.value === name ? null : name
}

function closeDropdowns() {
  openDropdown.value = null
}

async function goToPage(page: number) {
  await fetchFiltered(page)
}

function onCreated() {
  fetchFiltered(users.pagination.current_page)
}

async function handleExport() {
  try {
    const response = await api.get('/users/export', {
      params: { search: search.value, role: roleFilter.value, status: statusFilter.value },
      responseType: 'blob',
    })
    const url = URL.createObjectURL(new Blob([response.data], { type: 'text/csv;charset=utf-8;' }))
    const a   = document.createElement('a')
    a.href     = url
    a.download = `usuarios_${new Date().toISOString().slice(0, 10)}.csv`
    a.click()
    URL.revokeObjectURL(url)
  } catch {
    toast.add('Erro ao exportar usuários.', 'error')
  }
}
</script>

<template>
  <div class="nx" style="flex:1;display:flex;flex-direction:column;">
    <AppTopbar title="Usuários" crumb="Início / Usuários" :count="users.pagination.total">
      <button class="nx-btn nx-btn-secondary nx-btn-sm" style="display:flex;align-items:center;gap:6px;" @click="handleExport">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Exportar
      </button>
      <button class="nx-btn nx-btn-primary nx-btn-sm" @click="showModal = true">+ Novo usuário</button>
    </AppTopbar>

    <!-- Filtros -->
    <div class="users-filters" @click.self="closeDropdowns">
      <div style="position:relative;width:280px;">
        <svg style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--nx-text-3);pointer-events:none;" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>
        </svg>
        <input
          v-model="search"
          class="nx-input"
          style="height:32px;font-size:13px;padding-left:34px;"
          placeholder="Buscar por nome ou e-mail..."
          @click="closeDropdowns"
        />
      </div>

      <!-- Filtro Papel -->
      <div style="position:relative;">
        <button class="nx-btn nx-btn-secondary nx-btn-sm" :class="{ 'nx-btn-active': roleFilter }" style="display:flex;align-items:center;gap:6px;" @click="toggleDropdown('role')">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
          Papel: {{ roleFilter || 'todos' }}
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" :style="openDropdown === 'role' ? 'transform:rotate(180deg)' : ''"><path d="m6 9 6 6 6-6"/></svg>
        </button>
        <div v-if="openDropdown === 'role'" class="nx-dropdown">
          <button class="nx-dropdown__item" :class="{ 'nx-dropdown__item--active': !roleFilter }" @click="setRole('')">Todos</button>
          <button class="nx-dropdown__item" :class="{ 'nx-dropdown__item--active': roleFilter === 'Admin' }" @click="setRole('Admin')">Admin</button>
          <button class="nx-dropdown__item" :class="{ 'nx-dropdown__item--active': roleFilter === 'Editor' }" @click="setRole('Editor')">Editor</button>
          <button class="nx-dropdown__item" :class="{ 'nx-dropdown__item--active': roleFilter === 'Viewer' }" @click="setRole('Viewer')">Viewer</button>
        </div>
      </div>

      <!-- Filtro Status -->
      <div style="position:relative;">
        <button class="nx-btn nx-btn-secondary nx-btn-sm" :class="{ 'nx-btn-active': statusFilter }" style="display:flex;align-items:center;gap:6px;" @click="toggleDropdown('status')">
          Status: {{ statusFilter || 'todos' }}
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" :style="openDropdown === 'status' ? 'transform:rotate(180deg)' : ''"><path d="m6 9 6 6 6-6"/></svg>
        </button>
        <div v-if="openDropdown === 'status'" class="nx-dropdown">
          <button class="nx-dropdown__item" :class="{ 'nx-dropdown__item--active': !statusFilter }" @click="setStatus('')">Todos</button>
          <button class="nx-dropdown__item" :class="{ 'nx-dropdown__item--active': statusFilter === 'Ativo' }" @click="setStatus('Ativo')">Ativo</button>
          <button class="nx-dropdown__item" :class="{ 'nx-dropdown__item--active': statusFilter === 'Inativo' }" @click="setStatus('Inativo')">Inativo</button>
        </div>
      </div>

      <div style="flex:1;"/>
      <span style="font-size:12px;color:var(--nx-text-3);">Ordenar por</span>
      <button class="nx-btn nx-btn-secondary nx-btn-sm" style="display:flex;align-items:center;gap:6px;">
        Mais recentes
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
      </button>
    </div>

    <!-- Skeleton -->
    <div v-if="users.loading" style="flex:1;overflow:auto;">
      <table class="nx-table">
        <thead>
          <tr>
            <th style="width:36px;"/>
            <th>Nome</th><th>E-mail</th><th>Nascimento</th><th>Status</th><th style="width:48px;"/>
          </tr>
        </thead>
        <tbody>
          <tr v-for="i in 6" :key="i">
            <td><div style="width:14px;height:14px;background:var(--nx-surface-2);border-radius:3px;"/></td>
            <td>
              <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:32px;height:32px;border-radius:16px;background:var(--nx-surface-2);"/>
                <div style="display:flex;flex-direction:column;gap:6px;">
                  <div style="width:160px;height:12px;background:var(--nx-surface-2);border-radius:4px;"/>
                  <div style="width:90px;height:10px;background:var(--nx-surface-2);border-radius:4px;"/>
                </div>
              </div>
            </td>
            <td><div style="width:180px;height:12px;background:var(--nx-surface-2);border-radius:4px;"/></td>
            <td><div style="width:80px;height:12px;background:var(--nx-surface-2);border-radius:4px;"/></td>
            <td><div style="width:60px;height:18px;background:var(--nx-surface-2);border-radius:12px;"/></td>
            <td/>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Empty state -->
    <div v-else-if="!users.loading && users.list.length === 0" style="flex:1;display:flex;align-items:center;justify-content:center;padding:40px;">
      <div style="text-align:center;max-width:380px;">
        <div style="width:64px;height:64px;border-radius:32px;background:var(--nx-primary-50);color:var(--nx-primary);display:inline-flex;align-items:center;justify-content:center;margin-bottom:18px;border:1px solid var(--nx-primary-100);">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="9" cy="8" r="3.5"/><path d="M2 19c1-3 3.5-5 7-5s6 2 7 5"/><path d="M16 4a3.5 3.5 0 0 1 0 7M22 19c-.6-2.4-2.2-4-4.5-4.7"/>
            </svg>
          </div>
        <h2 style="font-family:var(--nx-font-serif);font-size:28px;font-weight:400;margin:0 0 6px;">Nenhum usuário ainda</h2>
        <p style="font-size:14px;color:var(--nx-text-2);margin:0 0 20px;line-height:1.55;">Crie o primeiro usuário para começar a gerenciar sua equipe.</p>
        <button class="nx-btn nx-btn-primary" @click="showModal = true">+ Criar primeiro usuário</button>
      </div>
    </div>

    <!-- Tabela -->
    <div v-else style="flex:1;overflow:auto;">
      <table class="nx-table">
        <thead>
          <tr>
            <th style="width:36px;">
              <input
                ref="headerCheckbox"
                type="checkbox"
                :checked="allChecked"
                :style="{ accentColor: 'var(--nx-primary)' }"
                @change="toggleAll"
              />
            </th>
            <th>Nome</th>
            <th>E-mail</th>
            <th>Nascimento</th>
            <th>Papel</th>
            <th>Status</th>
            <th style="width:64px;"/>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in users.list" :key="user.id" :class="{ 'nx-row-selected': selected.has(user.id) }">
            <td>
              <input
                type="checkbox"
                :checked="selected.has(user.id)"
                :style="{ accentColor: 'var(--nx-primary)' }"
                @change="toggleOne(user.id)"
              />
            </td>
            <td>
              <div style="display:flex;align-items:center;gap:10px;">
                <NxAvatar :name="user.name" :avatar-url="user.avatar_url" />
                <div>
                  <div style="font-weight:500;">{{ user.name }}</div>
                  <div style="font-size:11px;color:var(--nx-text-3);">Criado {{ new Date(user.created_at).toLocaleDateString('pt-BR') }}</div>
                </div>
              </div>
            </td>
            <td style="font-family:var(--nx-font-mono);font-size:13px;color:var(--nx-text-2);">{{ user.email }}</td>
            <td style="font-size:13px;color:var(--nx-text-2);font-family:var(--nx-font-mono);">{{ user.birth_date }}</td>
            <td>
              <span class="nx-badge" :class="user.role === 'Admin' ? '' : 'nx-badge-neutral'">{{ user.role ?? '—' }}</span>
            </td>
            <td>
              <span
                class="nx-badge"
                :class="user.status === 'Ativo' ? 'nx-badge-success' : 'nx-badge-danger'"
              >
                <span style="width:6px;height:6px;border-radius:3px;background:currentColor;"/>
                {{ user.status ?? '—' }}
              </span>
            </td>
            <td>
              <div style="display:flex;gap:4px;">
                <button class="nx-btn nx-btn-ghost nx-btn-icon" title="Editar" @click="router.push(`/users/${user.id}/edit`)">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
                  </svg>
                </button>
                <button class="nx-btn nx-btn-ghost nx-btn-icon" title="Excluir" style="color:var(--nx-danger);" @click="deleteTarget = { id: user.id, name: user.name }">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                  </svg>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Paginação -->
    <div v-if="!users.loading && users.list.length > 0" class="users-pagination">
      <span style="font-size:13px;color:var(--nx-text-3);">
        Exibindo {{ (users.pagination.current_page - 1) * users.pagination.per_page + 1 }}–{{ Math.min(users.pagination.current_page * users.pagination.per_page, users.pagination.total) }} de {{ users.pagination.total }}
      </span>
      <div style="display:flex;gap:4px;">
        <button class="nx-btn nx-btn-secondary nx-btn-sm" :disabled="users.pagination.current_page === 1" @click="goToPage(users.pagination.current_page - 1)">‹</button>
        <button
          v-for="p in users.pagination.last_page"
          :key="p"
          :class="['nx-btn nx-btn-sm', p === users.pagination.current_page ? 'nx-btn-primary' : 'nx-btn-secondary']"
          style="width:30px;padding:0;"
          @click="goToPage(p)"
        >{{ p }}</button>
        <button class="nx-btn nx-btn-secondary nx-btn-sm" :disabled="users.pagination.current_page === users.pagination.last_page" @click="goToPage(users.pagination.current_page + 1)">›</button>
      </div>
    </div>

    <UserModal :open="showModal" @close="showModal = false" @created="onCreated" />
    <UserDeleteDialog
      v-if="deleteTarget"
      :open="!!deleteTarget"
      :user-name="deleteTarget.name"
      :user-id="deleteTarget.id"
      @close="deleteTarget = null"
      @deleted="users.fetchUsers(users.pagination.current_page, search)"
    />
  </div>
</template>

<style scoped>
.users-filters { padding: 16px 32px; display: flex; gap: 8px; align-items: center; border-bottom: 1px solid var(--nx-border); background: var(--nx-surface); }
.users-pagination { padding: 12px 32px; border-top: 1px solid var(--nx-border); display: flex; align-items: center; justify-content: space-between; background: var(--nx-surface); }

.nx-btn-active {
  border-color: var(--nx-primary);
  color: var(--nx-primary);
  background: var(--nx-primary-50);
}

.nx-dropdown {
  position: absolute;
  top: calc(100% + 4px);
  left: 0;
  z-index: 100;
  background: var(--nx-surface);
  border: 1px solid var(--nx-border);
  border-radius: var(--nx-r-lg);
  box-shadow: var(--nx-shadow-lg);
  padding: 4px;
  min-width: 130px;
  display: flex;
  flex-direction: column;
  gap: 1px;
}
.nx-dropdown__item {
  display: block;
  width: 100%;
  text-align: left;
  padding: 7px 12px;
  font-size: 13px;
  border-radius: 6px;
  border: none;
  background: transparent;
  color: var(--nx-text-2);
  cursor: pointer;
  transition: background 0.1s;
}
.nx-dropdown__item:hover { background: var(--nx-surface-2); color: var(--nx-text); }
.nx-dropdown__item--active { color: var(--nx-primary); font-weight: 500; background: var(--nx-primary-50); }

.nx-row-selected { background: var(--nx-primary-50) !important; }

@media (max-width: 639px) {
  .users-filters { padding: 12px 16px; }
  .users-filters input { width: 100%; }
  .users-pagination { padding: 10px 16px; flex-direction: column; gap: 8px; }
}
</style>
