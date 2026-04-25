const open = ref(false)

export function useMobileMenu() {
  function openMenu()  { open.value = true  }
  function closeMenu() { open.value = false }
  function toggleMenu() { open.value = !open.value }

  return { open, openMenu, closeMenu, toggleMenu }
}
