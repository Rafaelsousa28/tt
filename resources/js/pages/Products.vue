<template>
  <div class="min-h-screen bg-white pt-24 pb-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="section-title">{{ pageTitle }}</h1>
        <p class="section-subtitle">{{ meta.total ?? '...' }} produtos encontrados</p>
      </div>

      <div class="flex gap-8">
        <!-- Filters Sidebar -->
        <aside class="hidden lg:block w-64 flex-shrink-0">
          <div class="sticky top-24 space-y-6">
            <!-- Categories -->
            <div>
              <h3 class="font-semibold text-[#1B1B1B] text-sm mb-4">Categorias</h3>
              <ul class="space-y-2">
                <li>
                  <button
                    @click="filters.category = null; fetchProducts()"
                    class="flex items-center justify-between w-full text-left text-sm py-1.5 px-2 rounded-lg transition-colors"
                    :class="!filters.category ? 'bg-[#D8F3DC] text-[#2D6A4F] font-medium' : 'text-gray-600 hover:text-[#2D6A4F] hover:bg-gray-50'"
                  >
                    <span>Todos</span>
                    <span class="text-xs text-gray-400">{{ meta.total ?? '' }}</span>
                  </button>
                </li>
                <li v-for="cat in categories" :key="cat.id">
                  <button
                    @click="filters.category = cat.slug; fetchProducts()"
                    class="flex items-center justify-between w-full text-left text-sm py-1.5 px-2 rounded-lg transition-colors"
                    :class="filters.category === cat.slug ? 'bg-[#D8F3DC] text-[#2D6A4F] font-medium' : 'text-gray-600 hover:text-[#2D6A4F] hover:bg-gray-50'"
                  >
                    <span>{{ cat.name }}</span>
                    <span class="text-xs text-gray-400">{{ cat.products_count }}</span>
                  </button>
                </li>
              </ul>
            </div>

            <!-- Price Range -->
            <div class="pt-4 border-t border-gray-100">
              <h3 class="font-semibold text-[#1B1B1B] text-sm mb-4">Faixa de Preço</h3>
              <div class="flex gap-2 items-center">
                <input v-model="filters.min_price" type="number" placeholder="Min" class="input-field text-sm py-2 px-3" />
                <span class="text-gray-400">–</span>
                <input v-model="filters.max_price" type="number" placeholder="Max" class="input-field text-sm py-2 px-3" />
              </div>
              <button @click="fetchProducts()" class="btn-secondary text-xs mt-3 py-2 w-full justify-center">Aplicar</button>
            </div>

            <!-- Care level -->
            <div class="pt-4 border-t border-gray-100">
              <h3 class="font-semibold text-[#1B1B1B] text-sm mb-4">Nível de Cuidado</h3>
              <div class="space-y-2">
                <label v-for="level in careLevels" :key="level.value" class="flex items-center gap-3 cursor-pointer group">
                  <input
                    type="radio"
                    :value="level.value"
                    v-model="filters.care_level"
                    @change="fetchProducts()"
                    class="accent-[#2D6A4F]"
                  />
                  <span class="text-sm text-gray-600 group-hover:text-[#2D6A4F]">{{ level.icon }} {{ level.label }}</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer group">
                  <input type="radio" value="" v-model="filters.care_level" @change="fetchProducts()" class="accent-[#2D6A4F]" />
                  <span class="text-sm text-gray-600 group-hover:text-[#2D6A4F]">Todos</span>
                </label>
              </div>
            </div>

            <!-- Clear filters -->
            <button v-if="hasActiveFilters" @click="clearFilters" class="w-full text-sm text-red-400 hover:text-red-600 transition-colors py-2">
              Limpar filtros
            </button>
          </div>
        </aside>

        <!-- Products Grid -->
        <div class="flex-1 min-w-0">
          <!-- Toolbar -->
          <div class="flex items-center justify-between mb-6 gap-4">
            <!-- Mobile Filter button -->
            <button @click="mobileFiltersOpen = true" class="lg:hidden btn-ghost border border-gray-200">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <line x1="4" y1="6" x2="20" y2="6"/><line x1="8" y1="12" x2="20" y2="12"/><line x1="12" y1="18" x2="20" y2="18"/>
              </svg>
              Filtros
            </button>

            <!-- Search -->
            <div class="flex-1 max-w-xs">
              <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
                <input
                  v-model="filters.search"
                  @input="debouncedFetch"
                  type="text"
                  placeholder="Buscar..."
                  class="input-field pl-9 py-2.5 text-sm"
                />
              </div>
            </div>

            <!-- Sort -->
            <select v-model="filters.sort" @change="fetchProducts()" class="input-field py-2.5 text-sm w-auto pr-8">
              <option value="newest">Mais recentes</option>
              <option value="price_asc">Menor preço</option>
              <option value="price_desc">Maior preço</option>
              <option value="name_asc">A–Z</option>
            </select>
          </div>

          <!-- Loading skeleton -->
          <div v-if="loading" class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-5">
            <div v-for="i in 8" :key="i" class="rounded-2xl bg-gray-100 animate-pulse aspect-[3/4]"></div>
          </div>

          <!-- Products -->
          <div v-else-if="products.length" class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-5">
            <ProductCard v-for="product in products" :key="product.id" :product="product" />
          </div>

          <!-- Empty state -->
          <div v-else class="text-center py-20">
            <div class="text-6xl mb-4">🔍</div>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Nenhum produto encontrado</h3>
            <p class="text-gray-400 mb-6">Tente ajustar os filtros ou buscar por outro termo</p>
            <button @click="clearFilters" class="btn-primary">Limpar Filtros</button>
          </div>

          <!-- Pagination -->
          <div v-if="meta.last_page > 1" class="flex justify-center gap-2 mt-10">
            <button
              v-for="page in pages"
              :key="page"
              @click="goToPage(page)"
              class="w-10 h-10 rounded-full text-sm font-medium transition-colors"
              :class="page === meta.current_page
                ? 'bg-[#2D6A4F] text-white'
                : 'text-gray-600 hover:bg-gray-100'"
            >{{ page }}</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import ProductCard from '../components/ProductCard.vue'

const route  = useRoute()
const router = useRouter()

const products          = ref([])
const categories        = ref([])
const meta              = ref({})
const loading           = ref(true)
const mobileFiltersOpen = ref(false)

const filters = ref({
  search:     route.query.search ?? '',
  category:   route.query.category ?? route.params.slug ?? null,
  care_level: route.query.care_level ?? '',
  min_price:  route.query.min_price ?? '',
  max_price:  route.query.max_price ?? '',
  sort:       route.query.sort ?? 'newest',
  page:       1,
  featured:   route.query.featured ?? false,
})

const careLevels = [
  { value: 'facil',   label: 'Fácil',    icon: '🌿' },
  { value: 'medio',   label: 'Médio',    icon: '🌱' },
  { value: 'dificil', label: 'Difícil',  icon: '🌵' },
]

const pageTitle = computed(() => {
  if (filters.value.search) return `Busca: "${filters.value.search}"`
  if (filters.value.category) {
    const cat = categories.value.find(c => c.slug === filters.value.category)
    return cat?.name ?? 'Plantas'
  }
  return 'Todas as Plantas'
})

const hasActiveFilters = computed(() =>
  filters.value.search || filters.value.category || filters.value.care_level ||
  filters.value.min_price || filters.value.max_price
)

const pages = computed(() => {
  const total = meta.value.last_page ?? 1
  const current = meta.value.current_page ?? 1
  const range = []
  for (let i = Math.max(1, current - 2); i <= Math.min(total, current + 2); i++) {
    range.push(i)
  }
  return range
})

async function fetchProducts() {
  loading.value = true
  try {
    const params = {}
    if (filters.value.search)     params.search     = filters.value.search
    if (filters.value.category)   params.category   = filters.value.category
    if (filters.value.care_level) params.care_level = filters.value.care_level
    if (filters.value.min_price)  params.min_price  = filters.value.min_price
    if (filters.value.max_price)  params.max_price  = filters.value.max_price
    if (filters.value.featured)   params.featured   = 1
    params.sort     = filters.value.sort
    params.page     = filters.value.page
    params.per_page = 12

    const res     = await axios.get('/products', { params })
    products.value = res.data.data
    meta.value     = res.data.meta
  } finally {
    loading.value = false
  }
}

async function fetchCategories() {
  const res = await axios.get('/categories')
  categories.value = res.data.data
}

function clearFilters() {
  filters.value.search = ''
  filters.value.category = null
  filters.value.care_level = ''
  filters.value.min_price = ''
  filters.value.max_price = ''
  filters.value.page = 1
  fetchProducts()
}

function goToPage(page) {
  filters.value.page = page
  fetchProducts()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

let debounceTimer = null
function debouncedFetch() {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(fetchProducts, 400)
}

watch(() => route.query.search, (v) => { filters.value.search = v ?? ''; fetchProducts() })

onMounted(() => { fetchProducts(); fetchCategories() })
</script>
