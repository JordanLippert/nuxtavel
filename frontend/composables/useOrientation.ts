export function useOrientation() {
  const isLandscape = ref(false)

  function update() {
    if (import.meta.client) {
      isLandscape.value = window.matchMedia('(orientation: landscape)').matches
    }
  }

  onMounted(() => {
    update()
    window.addEventListener('orientationchange', update)
    window.addEventListener('resize', update)
  })

  onUnmounted(() => {
    window.removeEventListener('orientationchange', update)
    window.removeEventListener('resize', update)
  })

  return { isLandscape }
}
