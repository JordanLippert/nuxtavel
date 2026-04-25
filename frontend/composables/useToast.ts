export function useToast() {
  const store = useToastStore()
  return {
    success:  (msg: string) => store.add(msg, 'success'),
    error:    (msg: string) => store.add(msg, 'error'),
    birthday: (msg: string) => store.add(msg, 'birthday'),
  }
}
