<script setup lang="ts">
defineProps<{
  title: string
  crumb?: string
  count?: number
}>()

const { toggleMenu } = useMobileMenu()
</script>

<template>
  <header class="nx-topbar nx">
    <div v-if="crumb" class="nx-topbar__crumb">{{ crumb }}</div>
    <div class="nx-topbar__row">
      <div class="nx-topbar__title-group">
        <!-- Hambúrguer (só mobile) -->
        <button class="nx-topbar__hamburger nx-btn nx-btn-ghost nx-btn-icon" @click="toggleMenu">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
            <line x1="3" y1="6"  x2="21" y2="6"/>
            <line x1="3" y1="12" x2="21" y2="12"/>
            <line x1="3" y1="18" x2="21" y2="18"/>
          </svg>
        </button>
        <h1 class="nx-topbar__title">{{ title }}</h1>
        <span v-if="count != null" class="nx-topbar__count">{{ count }} resultados</span>
      </div>
      <div class="nx-topbar__actions">
        <slot />
      </div>
    </div>
  </header>
</template>

<style scoped>
.nx-topbar {
  padding: 20px 32px 16px; border-bottom: 1px solid var(--nx-border);
  background: var(--nx-surface); flex-shrink: 0;
}
.nx-topbar__crumb { font: 400 12px var(--nx-font-mono); color: var(--nx-text-3); margin-bottom: 6px; }
.nx-topbar__row { display: flex; align-items: center; justify-content: space-between; gap: 16px; }
.nx-topbar__title-group { display: flex; align-items: center; gap: 12px; }
.nx-topbar__title {
  font-family: var(--nx-font-serif); font-size: 32px;
  font-weight: 400; margin: 0; letter-spacing: -0.4px;
  align-self: baseline;
}
.nx-topbar__count { font-size: 13px; color: var(--nx-text-3); align-self: baseline; }
.nx-topbar__actions { display: flex; gap: 8px; align-items: center; }

.nx-topbar__hamburger { display: none; }

@media (max-width: 1023px) {
  .nx-topbar__hamburger { display: flex; }
}

@media (max-width: 639px) {
  .nx-topbar { padding: 14px 16px 12px; }
  .nx-topbar__title { font-size: 24px; }
}
</style>
