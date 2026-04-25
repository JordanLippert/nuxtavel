<template>
  <div class="nx panel-layout">
    <AppSidebar />
    <div class="panel-layout__main">
      <slot />
    </div>
    <AppBottomNav />
    <AppToast />
  </div>
</template>

<style scoped>
.panel-layout {
  display: flex; height: 100vh; background: var(--nx-bg); overflow: hidden;
}
.panel-layout__main {
  flex: 1; display: flex; flex-direction: column;
  min-width: 0; overflow-y: auto;
}

@media (max-width: 1023px) {
  .panel-layout__main { padding-bottom: 60px; }
}
</style>
