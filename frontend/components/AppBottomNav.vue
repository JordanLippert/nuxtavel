<script setup lang="ts">
const route = useRoute()
const navItems = [
  { label: 'Painel',   path: '/',        icon: '⊞' },
  { label: 'Usuários', path: '/users',   icon: '👤' },
  { label: 'Ajustes',  path: '/settings', icon: '⚙' },
]
</script>

<template>
  <nav class="bottom-nav nx">
    <NuxtLink
      v-for="item in navItems"
      :key="item.path"
      :to="item.path"
      :class="['bottom-nav__item', route.path === item.path || (item.path !== '/' && route.path.startsWith(item.path)) ? 'bottom-nav__item--active' : '']"
    >
      <span class="bottom-nav__icon">{{ item.icon }}</span>
      <span class="bottom-nav__label">{{ item.label }}</span>
    </NuxtLink>
  </nav>
</template>

<style scoped>
.bottom-nav {
  display: none; position: fixed; bottom: 0; left: 0; right: 0;
  background: var(--nx-surface); border-top: 1px solid var(--nx-border);
  z-index: 100; height: 60px;
}
.bottom-nav__item {
  flex: 1; display: flex; flex-direction: column;
  align-items: center; justify-content: center; gap: 2px;
  font-size: 11px; color: var(--nx-text-3); text-decoration: none;
  transition: color 0.1s;
}
.bottom-nav__item--active { color: var(--nx-primary); font-weight: 500; }
.bottom-nav__icon { font-size: 18px; line-height: 1; }
.bottom-nav__label { font-size: 10px; }

@media (max-width: 1023px) {
  .bottom-nav { display: flex; }
}
</style>
