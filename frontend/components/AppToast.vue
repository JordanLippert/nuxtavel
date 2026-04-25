<script setup lang="ts">
const toastStore = useToastStore()
</script>

<template>
  <div style="position:fixed;bottom:24px;right:24px;display:flex;flex-direction:column;gap:10px;z-index:9999;width:320px;pointer-events:none;">
    <TransitionGroup name="toast">
      <div
        v-for="toast in toastStore.toasts"
        :key="toast.id"
        class="nx-card"
        style="padding:12px 14px;display:flex;gap:12px;box-shadow:var(--nx-shadow-lg);align-items:flex-start;pointer-events:all;"
      >
        <div
          :style="{
            width: '28px', height: '28px', borderRadius: '14px', flexShrink: '0',
            display: 'flex', alignItems: 'center', justifyContent: 'center',
            background: toast.variant === 'success' ? 'var(--nx-success-bg)'
              : toast.variant === 'birthday' ? 'var(--nx-primary-50)'
              : 'var(--nx-danger-bg)',
            color: toast.variant === 'success' ? 'var(--nx-success)'
              : toast.variant === 'birthday' ? 'var(--nx-primary)'
              : 'var(--nx-danger)',
          }"
        >
          <span v-if="toast.variant === 'birthday'" style="font-size:14px;">🎂</span>
          <svg v-else-if="toast.variant === 'success'" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"/>
          </svg>
          <svg v-else width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 9v4M12 17h.01"/><path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/>
          </svg>
        </div>
        <div style="flex:1;font-size:13px;line-height:1.4;color:var(--nx-text);">{{ toast.message }}</div>
        <button
          class="nx-btn nx-btn-ghost nx-btn-icon"
          style="width:24px;height:24px;flex-shrink:0;"
          @click="toastStore.remove(toast.id)"
        >
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
            <path d="M6 6l12 12M18 6 6 18"/>
          </svg>
        </button>
      </div>
    </TransitionGroup>
  </div>
</template>

<style scoped>
.toast-enter-active, .toast-leave-active { transition: all 0.2s ease; }
.toast-enter-from { opacity: 0; transform: translateY(10px); }
.toast-leave-to   { opacity: 0; transform: translateX(20px); }
</style>
