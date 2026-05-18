import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useUiStore = defineStore('ui', () => {
  const searchOpen = ref(false)
  const mobileMenuOpen = ref(false)
  const loading = ref(false)
  const toast = ref(null)

  let toastTimer = null

  function showToast(message, type = 'success', duration = 3000) {
    if (toastTimer) clearTimeout(toastTimer)
    toast.value = { message, type }
    toastTimer = setTimeout(() => { toast.value = null }, duration)
  }

  function hideToast() {
    toast.value = null
    if (toastTimer) clearTimeout(toastTimer)
  }

  return {
    searchOpen, mobileMenuOpen, loading, toast,
    showToast, hideToast,
  }
})
