<script setup lang="ts">
defineProps<{
  title: string
  crumb?: string
  count?: number
}>()
</script>

<template>
  <header class="nx-topbar nx">
    <div v-if="crumb" class="nx-topbar__crumb">{{ crumb }}</div>
    <div class="nx-topbar__row">
      <div class="nx-topbar__title-group">
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
.nx-topbar__title-group { display: flex; align-items: baseline; gap: 12px; }
.nx-topbar__title {
  font-family: var(--nx-font-serif); font-size: 32px;
  font-weight: 400; margin: 0; letter-spacing: -0.4px;
}
.nx-topbar__count { font-size: 13px; color: var(--nx-text-3); }
.nx-topbar__actions { display: flex; gap: 8px; align-items: center; }

@media (max-width: 639px) {
  .nx-topbar { padding: 14px 16px 12px; }
  .nx-topbar__title { font-size: 24px; }
}
</style>
