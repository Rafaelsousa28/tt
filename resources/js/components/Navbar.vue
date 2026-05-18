<template>
  <header
    class="fixed top-0 left-0 right-0 z-50 transition-all duration-300"
    :class="scrolled ? 'bg-white/95 backdrop-blur-sm shadow-sm' : 'bg-transparent'"
  >
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <nav class="flex items-center justify-between h-16 md:h-20">
        <!-- Logo Pure Garden -->
        <RouterLink to="/" class="group flex items-center gap-3">
          <!-- Ícone: vaso com muda, fiel à logo oficial -->
          <svg
            class="h-9 w-auto transition-colors duration-300"
            :class="scrolled ? 'text-[#2D5016]' : 'text-white'"
            viewBox="0 0 44 52" fill="currentColor" xmlns="http://www.w3.org/2000/svg"
          >
            <!-- vaso -->
            <path d="M10 30 L13 48 H31 L34 30 Z" opacity="0.9"/>
            <!-- haste -->
            <rect x="20.5" y="12" width="3" height="20" rx="1.5"/>
            <!-- folha esquerda -->
            <path d="M21 22 C18 18 12 18 12 14 C16 14 20 17 21 22Z" opacity="0.85"/>
            <!-- folha direita -->
            <path d="M23 18 C26 14 32 13 32 9 C28 10 23 13 23 18Z" opacity="0.85"/>
            <!-- brotar topo -->
            <path d="M22 12 C21 9 19 7 20 4 C22 6 23 9 22 12Z"/>
          </svg>

          <!-- Wordmark -->
          <div class="leading-none">
            <div
              class="font-black uppercase tracking-widest text-lg transition-colors duration-300"
              :class="scrolled ? 'text-[#1C3A0E]' : 'text-white'"
              style="font-family: 'Inter', sans-serif; letter-spacing: 0.12em;"
            >
              PURE <span :class="scrolled ? 'text-[#2D6A4F]' : 'text-[#A7F3D0]'">GARDEN</span>
            </div>
            <div
              class="text-[9px] tracking-[0.25em] uppercase font-medium transition-colors duration-300"
              :class="scrolled ? 'text-[#2D6A4F]' : 'text-[#A7F3D0]/80'"
            >Jardins e Decorações</div>
          </div>
        </RouterLink>

        <!-- Desktop Nav -->
        <ul class="hidden md:flex items-center gap-8">
          <li v-for="link in navLinks" :key="link.to">
            <RouterLink
              :to="link.to"
              class="text-sm font-medium transition-colors duration-200"
              :class="scrolled ? 'text-gray-700 hover:text-[#2D6A4F]' : 'text-white/90 hover:text-white'"
            >{{ link.label }}</RouterLink>
          </li>
        </ul>

        <!-- Actions -->
        <div class="flex items-center gap-3">
          <button
            @click="uiStore.searchOpen = true"
            class="p-2 rounded-full transition-colors duration-200"
            :class="scrolled ? 'text-gray-600 hover:bg-gray-100' : 'text-white hover:bg-white/10'"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
            </svg>
          </button>

          <button
            @click="cartStore.openCart()"
            class="relative p-2 rounded-full transition-colors duration-200"
            :class="scrolled ? 'text-gray-600 hover:bg-gray-100' : 'text-white hover:bg-white/10'"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/>
              <path d="M16 10a4 4 0 0 1-8 0"/>
            </svg>
            <span
              v-if="cartStore.itemCount > 0"
              class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-[#2D6A4F] text-white text-[10px] font-bold rounded-full flex items-center justify-center"
            >{{ cartStore.itemCount }}</span>
          </button>

          <!-- Mobile menu button -->
          <button
            @click="uiStore.mobileMenuOpen = !uiStore.mobileMenuOpen"
            class="md:hidden p-2 rounded-full transition-colors duration-200"
            :class="scrolled ? 'text-gray-600 hover:bg-gray-100' : 'text-white hover:bg-white/10'"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
          </button>
        </div>
      </nav>
    </div>

    <!-- Mobile Menu -->
    <Transition name="slide-down">
      <div v-if="uiStore.mobileMenuOpen" class="md:hidden bg-white border-t border-gray-100 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 py-4 space-y-1">
          <RouterLink
            v-for="link in navLinks"
            :key="link.to"
            :to="link.to"
            class="block px-4 py-3 rounded-xl text-gray-700 hover:bg-[#D8F3DC] hover:text-[#2D6A4F] transition-colors font-medium"
            @click="uiStore.mobileMenuOpen = false"
          >{{ link.label }}</RouterLink>
        </div>
      </div>
    </Transition>

    <!-- Search Overlay -->
    <Transition name="fade">
      <div v-if="uiStore.searchOpen" class="fixed inset-0 bg-black/60 z-50 flex items-start justify-center pt-24 px-4">
        <div class="w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden">
          <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
            </svg>
            <input
              ref="searchInput"
              v-model="searchQuery"
              @keydown.enter="goSearch"
              @keydown.esc="closeSearch"
              type="text"
              placeholder="Buscar plantas, vasos, mudas..."
              class="flex-1 text-base outline-none placeholder-gray-400"
            />
            <button @click="closeSearch" class="text-gray-400 hover:text-gray-600">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
              </svg>
            </button>
          </div>
          <div class="px-5 py-3 text-xs text-gray-400">Pressione Enter para buscar</div>
        </div>
      </div>
    </Transition>
  </header>
</template>

<script setup>
import { ref, onMounted, onUnmounted, nextTick, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '../stores/cart.js'
import { useUiStore } from '../stores/ui.js'

const router    = useRouter()
const cartStore = useCartStore()
const uiStore   = useUiStore()

const scrolled      = ref(false)
const searchQuery   = ref('')
const searchInput   = ref(null)

const navLinks = [
  { to: '/',         label: 'Início' },
  { to: '/plantas',  label: 'Plantas' },
  { to: '/plantas?categoria=mudas-citricas', label: 'Mudas Cítricas' },
  { to: '/plantas?categoria=ornamentais',    label: 'Ornamentais' },
]

function handleScroll() {
  scrolled.value = window.scrollY > 20
}

function goSearch() {
  if (!searchQuery.value.trim()) return
  router.push({ path: '/plantas', query: { search: searchQuery.value } })
  closeSearch()
}

function closeSearch() {
  uiStore.searchOpen = false
  searchQuery.value = ''
}

watch(() => uiStore.searchOpen, async (v) => {
  if (v) {
    await nextTick()
    searchInput.value?.focus()
  }
})

onMounted(() => window.addEventListener('scroll', handleScroll))
onUnmounted(() => window.removeEventListener('scroll', handleScroll))
</script>

<style scoped>
.slide-down-enter-active, .slide-down-leave-active { transition: all 0.2s ease; }
.slide-down-enter-from, .slide-down-leave-to { opacity: 0; transform: translateY(-10px); }
</style>
