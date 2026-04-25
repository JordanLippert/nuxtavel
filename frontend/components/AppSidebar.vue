<script setup lang="ts">
const route  = useRoute()
const auth   = useAuthStore()
const router = useRouter()

const users = useUsersStore()

const navItems = [
  { id: 'index',    label: 'Painel',   path: '/',        icon: 'dashboard' },
  { id: 'users',    label: 'Usuários', path: '/users',   icon: 'users' },
  { id: 'settings', label: 'Ajustes',  path: '/settings',icon: 'settings' },
]

const activeId = computed(() => {
  if (route.path.startsWith('/users')) return 'users'
  if (route.path === '/')              return 'index'
  return 'settings'
})

async function handleLogout() {
  await auth.logout()
  router.push('/login')
}

const initials = computed(() =>
  (auth.user?.name ?? 'U')
    .split(' ')
    .slice(0, 2)
    .map((p: string) => p[0])
    .join('')
    .toUpperCase()
)
</script>

<template>
  <aside class="nx-sidebar nx">
    <div class="nx-sidebar__logo">
      <span class="nx-lockup">
        <NxLogo :size="22" />
        <span class="nx-lockup__name">nuxtavel</span>
      </span>
    </div>

    <nav class="nx-sidebar__nav">
      <span class="nx-sidebar__section-label">Menu</span>
      <NuxtLink
        v-for="item in navItems"
        :key="item.id"
        :to="item.path"
        :class="['nx-sidebar__item', activeId === item.id && 'nx-sidebar__item--active']"
      >
        <!-- Dashboard icon -->
        <svg v-if="item.icon === 'dashboard'" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="nx-sidebar__item-ic">
          <rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/>
        </svg>
        <!-- Users icon -->
        <svg v-else-if="item.icon === 'users'" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="nx-sidebar__item-ic">
          <circle cx="9" cy="8" r="3.5"/><path d="M2 19c1-3 3.5-5 7-5s6 2 7 5"/><path d="M16 4a3.5 3.5 0 0 1 0 7M22 19c-.6-2.4-2.2-4-4.5-4.7"/>
        </svg>
        <!-- Settings icon -->
        <svg v-else-if="item.icon === 'settings'" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="nx-sidebar__item-ic">
          <circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 0 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 0 1-4 0v-.1A1.7 1.7 0 0 0 9 19.4a1.7 1.7 0 0 0-1.8.3l-.1.1a2 2 0 0 1-2.8-2.8l.1-.1A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-1.5-1H3a2 2 0 0 1 0-4h.1A1.7 1.7 0 0 0 4.6 9a1.7 1.7 0 0 0-.3-1.8l-.1-.1a2 2 0 0 1 2.8-2.8l.1.1A1.7 1.7 0 0 0 9 4.6a1.7 1.7 0 0 0 1-1.5V3a2 2 0 0 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1a2 2 0 0 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.8 1.7 1.7 0 0 0 1.5 1H21a2 2 0 0 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z"/>
        </svg>
        <span style="flex:1">{{ item.label }}</span>
        <span v-if="item.id === 'users' && users.pagination.total > 0" style="font:500 11px var(--nx-font-mono);color:var(--nx-text-3);">{{ users.pagination.total }}</span>
      </NuxtLink>
    </nav>

    <div class="nx-sidebar__footer">
      <NxAvatar :name="auth.user?.name ?? 'U'" :avatar-url="auth.user?.avatar_url" />
      <div class="nx-sidebar__user-info">
        <div class="nx-sidebar__user-name">{{ auth.user?.name ?? '—' }}</div>
        <div class="nx-sidebar__user-role">Admin</div>
      </div>
      <button class="nx-btn nx-btn-ghost nx-btn-icon" title="Sair" @click="handleLogout">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
          <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5M21 12H9"/>
        </svg>
      </button>
    </div>
  </aside>
</template>

<style scoped>
.nx-sidebar {
  width: 240px; border-right: 1px solid var(--nx-border);
  background: var(--nx-surface); display: flex; flex-direction: column;
  flex-shrink: 0; height: 100vh; position: sticky; top: 0;
}
.nx-sidebar__logo { padding: 20px 20px 16px; }
.nx-lockup { display: inline-flex; align-items: center; gap: 8px; }
.nx-lockup__name {
  font-family: var(--nx-font-sans); font-weight: 300;
  font-size: 22px; letter-spacing: -0.6px; color: #0d2236;
}
.nx-sidebar__nav { padding: 12px; display: flex; flex-direction: column; gap: 2px; flex: 1; }
.nx-sidebar__section-label {
  font: 500 11px var(--nx-font-mono); color: var(--nx-text-3);
  text-transform: uppercase; letter-spacing: 0.1em;
  padding: 8px 12px 4px; display: block;
}
.nx-sidebar__item {
  display: flex; align-items: center; gap: 10px;
  padding: 8px 12px; border-radius: 6px; font-size: 14px;
  color: var(--nx-text-2); text-decoration: none;
  transition: background 0.1s;
}
.nx-sidebar__item-ic { flex-shrink: 0; }
.nx-sidebar__item:hover { background: var(--nx-surface-2); }
.nx-sidebar__item--active { font-weight: 500; color: var(--nx-primary); background: var(--nx-primary-50); }
.nx-sidebar__footer {
  padding: 12px; border-top: 1px solid var(--nx-border);
  display: flex; align-items: center; gap: 10px;
}
.nx-sidebar__user-info { flex: 1; min-width: 0; }
.nx-sidebar__user-name {
  font-size: 13px; font-weight: 500;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.nx-sidebar__user-role { font-size: 11px; color: var(--nx-text-3); }

@media (max-width: 1023px) {
  .nx-sidebar { display: none; }
}
</style>
