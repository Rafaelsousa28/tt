<template>
  <div class="min-h-screen bg-white pt-20">
    <!-- Loading state -->
    <div v-if="loading" class="max-w-7xl mx-auto px-4 py-16 animate-pulse">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
        <div class="aspect-square bg-gray-100 rounded-3xl"></div>
        <div class="space-y-4 py-4">
          <div class="h-4 bg-gray-100 rounded w-24"></div>
          <div class="h-8 bg-gray-100 rounded w-3/4"></div>
          <div class="h-6 bg-gray-100 rounded w-1/3"></div>
          <div class="h-24 bg-gray-100 rounded"></div>
        </div>
      </div>
    </div>

    <!-- Product -->
    <div v-else-if="product" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <!-- Breadcrumb -->
      <nav class="flex items-center gap-2 text-sm text-gray-400 mb-8">
        <RouterLink to="/" class="hover:text-[#2D6A4F]">Início</RouterLink>
        <span>/</span>
        <RouterLink to="/plantas" class="hover:text-[#2D6A4F]">Plantas</RouterLink>
        <span>/</span>
        <RouterLink v-if="product.category" :to="`/categorias/${product.category.slug}`" class="hover:text-[#2D6A4F]">{{ product.category.name }}</RouterLink>
        <span>/</span>
        <span class="text-[#1B1B1B] font-medium truncate max-w-[200px]">{{ product.name }}</span>
      </nav>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20">
        <!-- Images -->
        <div class="space-y-4">
          <!-- Main image -->
          <div class="relative rounded-3xl overflow-hidden bg-[#F8F4F0] aspect-square">
            <img
              v-if="currentImage"
              :src="currentImage"
              :alt="product.name"
              class="w-full h-full object-cover transition-opacity duration-300"
            />
            <div v-else class="w-full h-full flex items-center justify-center text-8xl">🌱</div>

            <!-- Sale badge -->
            <div v-if="product.is_on_sale" class="absolute top-4 left-4">
              <span class="badge-sale badge text-sm px-3 py-1">
                -{{ discountPercent }}%
              </span>
            </div>
          </div>

          <!-- Thumbnails -->
          <div v-if="product.image_urls?.length > 1" class="flex gap-3 overflow-x-auto scrollbar-thin pb-1">
            <button
              v-for="(img, i) in product.image_urls"
              :key="i"
              @click="currentImageIndex = i"
              class="flex-shrink-0 w-20 h-20 rounded-xl overflow-hidden border-2 transition-colors duration-200"
              :class="currentImageIndex === i ? 'border-[#2D6A4F]' : 'border-transparent hover:border-[#52B788]'"
            >
              <img :src="img" :alt="`${product.name} ${i+1}`" class="w-full h-full object-cover" />
            </button>
          </div>
        </div>

        <!-- Product info -->
        <div class="space-y-6">
          <div>
            <RouterLink v-if="product.category" :to="`/categorias/${product.category.slug}`" class="text-sm text-[#52B788] font-medium hover:text-[#2D6A4F]">
              {{ product.category.name }}
            </RouterLink>
            <h1 class="text-3xl md:text-4xl font-bold text-[#1B1B1B] leading-tight mt-1"
              style="font-family: 'Playfair Display', serif">{{ product.name }}</h1>

            <!-- Rating placeholder -->
            <div class="flex items-center gap-2 mt-3">
              <div class="flex text-amber-400">★★★★★</div>
              <span class="text-sm text-gray-500">(47 avaliações)</span>
            </div>
          </div>

          <!-- Price -->
          <div class="flex items-baseline gap-3">
            <span class="text-4xl font-bold text-[#2D6A4F]">{{ formatPrice(product.current_price) }}</span>
            <span v-if="product.is_on_sale" class="text-xl text-gray-400 line-through">{{ formatPrice(product.price) }}</span>
          </div>

          <p class="text-gray-600 leading-relaxed">{{ product.short_description }}</p>

          <!-- Care info pills -->
          <div class="flex flex-wrap gap-3">
            <div class="flex items-center gap-2 bg-[#F8F4F0] rounded-xl px-4 py-2.5">
              <span class="text-lg">{{ careLevelIcon }}</span>
              <div>
                <p class="text-xs text-gray-400">Cuidado</p>
                <p class="text-sm font-medium text-[#1B1B1B]">{{ careLevelLabel }}</p>
              </div>
            </div>
            <div v-if="product.light_requirement" class="flex items-center gap-2 bg-[#F8F4F0] rounded-xl px-4 py-2.5">
              <span class="text-lg">☀️</span>
              <div>
                <p class="text-xs text-gray-400">Luz</p>
                <p class="text-sm font-medium text-[#1B1B1B]">{{ lightLabel }}</p>
              </div>
            </div>
            <div v-if="product.water_frequency" class="flex items-center gap-2 bg-[#F8F4F0] rounded-xl px-4 py-2.5">
              <span class="text-lg">💧</span>
              <div>
                <p class="text-xs text-gray-400">Rega</p>
                <p class="text-sm font-medium text-[#1B1B1B]">{{ product.water_frequency }}</p>
              </div>
            </div>
            <div v-if="product.height_cm" class="flex items-center gap-2 bg-[#F8F4F0] rounded-xl px-4 py-2.5">
              <span class="text-lg">📏</span>
              <div>
                <p class="text-xs text-gray-400">Altura</p>
                <p class="text-sm font-medium text-[#1B1B1B]">{{ product.height_cm }} cm</p>
              </div>
            </div>
          </div>

          <!-- Stock indicator -->
          <div class="flex items-center gap-2">
            <span v-if="product.stock > 5" class="w-2 h-2 bg-green-400 rounded-full"></span>
            <span v-else-if="product.stock > 0" class="w-2 h-2 bg-amber-400 rounded-full animate-pulse"></span>
            <span v-else class="w-2 h-2 bg-red-400 rounded-full"></span>
            <span class="text-sm text-gray-600">
              <template v-if="product.stock > 5">Em estoque</template>
              <template v-else-if="product.stock > 0">Últimas {{ product.stock }} unidades!</template>
              <template v-else>Fora de estoque</template>
            </span>
          </div>

          <!-- Quantity + Add to cart -->
          <div class="flex items-center gap-4">
            <div class="flex items-center gap-2 border border-gray-200 rounded-xl overflow-hidden">
              <button @click="quantity = Math.max(1, quantity - 1)" class="px-4 py-3 hover:bg-gray-50 transition-colors text-lg font-medium text-gray-600">−</button>
              <span class="w-10 text-center font-semibold">{{ quantity }}</span>
              <button @click="quantity = Math.min(product.stock, quantity + 1)" :disabled="quantity >= product.stock" class="px-4 py-3 hover:bg-gray-50 transition-colors text-lg font-medium text-gray-600 disabled:opacity-30">+</button>
            </div>

            <button
              @click="addToCart"
              :disabled="product.stock === 0"
              class="flex-1 btn-primary py-3.5 justify-center text-base disabled:opacity-50"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                <line x1="3" y1="6" x2="21" y2="6"/>
                <path d="M16 10a4 4 0 0 1-8 0"/>
              </svg>
              {{ product.stock === 0 ? 'Esgotado' : 'Adicionar ao Carrinho' }}
            </button>
          </div>

          <!-- Perks -->
          <div class="grid grid-cols-2 gap-3 pt-4 border-t border-gray-100">
            <div class="flex items-center gap-2 text-sm text-gray-500">
              <svg class="w-4 h-4 text-[#52B788]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
              Entrega em todo Brasil
            </div>
            <div class="flex items-center gap-2 text-sm text-gray-500">
              <svg class="w-4 h-4 text-[#52B788]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
              Garantia de qualidade
            </div>
            <div class="flex items-center gap-2 text-sm text-gray-500">
              <svg class="w-4 h-4 text-[#52B788]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
              Pagamento seguro
            </div>
            <div class="flex items-center gap-2 text-sm text-gray-500">
              <svg class="w-4 h-4 text-[#52B788]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
              Embalagem especial
            </div>
          </div>
        </div>
      </div>

      <!-- Description -->
      <div v-if="product.description" class="mt-16 max-w-3xl">
        <h2 class="text-2xl font-semibold text-[#1B1B1B] mb-6" style="font-family: 'Playfair Display', serif">Sobre esta planta</h2>
        <div class="prose prose-green max-w-none text-gray-600 leading-relaxed" v-html="product.description"></div>
      </div>

      <!-- Related products -->
      <div v-if="related.length" class="mt-20">
        <h2 class="text-2xl font-semibold text-[#1B1B1B] mb-8" style="font-family: 'Playfair Display', serif">Você também pode gostar</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
          <ProductCard v-for="p in related" :key="p.id" :product="p" />
        </div>
      </div>
    </div>

    <!-- Not found -->
    <div v-else class="text-center py-32">
      <div class="text-6xl mb-4">🌿</div>
      <h2 class="text-2xl font-semibold text-gray-700">Produto não encontrado</h2>
      <RouterLink to="/plantas" class="btn-primary mt-6">Ver Todos os Produtos</RouterLink>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import ProductCard from '../components/ProductCard.vue'
import { useCartStore } from '../stores/cart.js'
import { useUiStore } from '../stores/ui.js'

const route   = useRoute()
const cart    = useCartStore()
const ui      = useUiStore()

const product           = ref(null)
const related           = ref([])
const loading           = ref(true)
const quantity          = ref(1)
const currentImageIndex = ref(0)

const currentImage = computed(() => {
  const imgs = product.value?.image_urls ?? []
  return imgs[currentImageIndex.value] ?? product.value?.first_image_url ?? null
})

const discountPercent = computed(() => {
  if (!product.value?.is_on_sale) return 0
  return Math.round((1 - product.value.current_price / product.value.price) * 100)
})

const careLevelIcon  = computed(() => ({ facil: '🌿', medio: '🌱', dificil: '🌵' }[product.value?.care_level] ?? '🌿'))
const careLevelLabel = computed(() => ({ facil: 'Fácil', medio: 'Médio', dificil: 'Difícil' }[product.value?.care_level] ?? ''))
const lightLabel     = computed(() => ({
  baixa: 'Sombra', media: 'Meia-sombra', alta: 'Luminoso', pleno_sol: 'Pleno sol',
}[product.value?.light_requirement] ?? ''))

function formatPrice(v) {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(v)
}

function addToCart() {
  if (!product.value || product.value.stock === 0) return
  cart.addItem(product.value, quantity.value)
  ui.showToast(`"${product.value.name}" adicionado ao carrinho!`)
}

async function fetchProduct() {
  loading.value = true
  try {
    const res        = await axios.get(`/products/${route.params.slug}`)
    product.value    = res.data.product
    related.value    = res.data.related
    currentImageIndex.value = 0
  } catch {
    product.value = null
  } finally {
    loading.value = false
  }
}

watch(() => route.params.slug, fetchProduct)
onMounted(fetchProduct)
</script>
