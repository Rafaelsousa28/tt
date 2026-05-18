<template>
  <div>
    <!-- Hero Section -->
    <section class="relative min-h-screen flex items-center overflow-hidden">
      <!-- Background -->
      <div class="absolute inset-0 bg-gradient-to-br from-[#1B4332] via-[#2D6A4F] to-[#52B788]">
        <div class="absolute inset-0 opacity-10"
          :style="bgPattern">
        </div>
      </div>

      <!-- Hero slider dots indicator -->
      <div v-if="banners.heroes.length > 1" class="absolute bottom-10 left-1/2 -translate-x-1/2 flex gap-2 z-10">
        <button
          v-for="(_, i) in banners.heroes"
          :key="i"
          @click="currentSlide = i"
          class="w-2 h-2 rounded-full transition-all duration-300"
          :class="currentSlide === i ? 'bg-white w-6' : 'bg-white/40'"
        />
      </div>

      <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
          <div>
            <span class="inline-flex items-center gap-2 text-[#A7F3D0] text-sm font-medium mb-6 bg-white/10 px-4 py-2 rounded-full">
              <span class="w-2 h-2 bg-[#52B788] rounded-full animate-pulse"></span>
              Mudas & Plantas Selecionadas
            </span>
            <h1 class="text-5xl md:text-6xl lg:text-7xl font-bold text-white leading-tight mb-6"
              style="font-family: 'Playfair Display', serif">
              Natureza <br/>
              <span class="text-[#A7F3D0]">que floresce</span><br/>
              em casa
            </h1>
            <p class="text-[#D8F3DC]/80 text-lg md:text-xl leading-relaxed mb-8 max-w-lg">
              Mudas cítricas, ornamentais e plantas exóticas escolhidas com carinho. Frete grátis acima de R$ 200.
            </p>
            <div class="flex flex-wrap gap-4">
              <RouterLink to="/plantas" class="bg-white text-[#2D6A4F] px-8 py-4 rounded-full font-semibold text-sm hover:bg-[#D8F3DC] transition-colors duration-300 flex items-center gap-2">
                Explorar Plantas
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                  <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12,5 19,12 12,19"/>
                </svg>
              </RouterLink>
              <RouterLink to="/plantas?featured=true" class="border border-white/40 text-white px-8 py-4 rounded-full font-medium text-sm hover:bg-white/10 transition-colors duration-300">
                Ver Destaques
              </RouterLink>
            </div>

            <!-- Stats -->
            <div class="flex gap-8 mt-12 pt-8 border-t border-white/20">
              <div v-for="stat in stats" :key="stat.label" class="text-center">
                <p class="text-2xl font-bold text-white" style="font-family: 'Playfair Display', serif">{{ stat.value }}</p>
                <p class="text-[#A7F3D0] text-xs mt-1">{{ stat.label }}</p>
              </div>
            </div>
          </div>

          <!-- Floating image cards -->
          <div class="hidden lg:grid grid-cols-2 gap-4 relative">
            <div class="space-y-4 mt-8">
              <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-3 border border-white/20">
                <div class="aspect-square bg-[#D8F3DC] rounded-xl overflow-hidden">
                  <div class="w-full h-full flex items-center justify-center text-6xl">🍋</div>
                </div>
                <p class="text-white font-medium text-sm mt-2 px-1">Limão Siciliano</p>
                <p class="text-[#A7F3D0] text-xs px-1">A partir de R$ 45,00</p>
              </div>
            </div>
            <div class="space-y-4">
              <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-3 border border-white/20">
                <div class="aspect-square bg-[#D8F3DC] rounded-xl overflow-hidden">
                  <div class="w-full h-full flex items-center justify-center text-6xl">🌿</div>
                </div>
                <p class="text-white font-medium text-sm mt-2 px-1">Costela de Adão</p>
                <p class="text-[#A7F3D0] text-xs px-1">A partir de R$ 35,00</p>
              </div>
              <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-3 border border-white/20">
                <div class="aspect-square bg-[#D8F3DC] rounded-xl overflow-hidden">
                  <div class="w-full h-full flex items-center justify-center text-6xl">🌸</div>
                </div>
                <p class="text-white font-medium text-sm mt-2 px-1">Orquídea Phalaenopsis</p>
                <p class="text-[#A7F3D0] text-xs px-1">A partir de R$ 80,00</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Perks strip -->
    <section class="bg-[#F8F4F0] py-8 border-y border-gray-100">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
          <div v-for="perk in perks" :key="perk.label" class="flex items-center gap-3">
            <span class="text-2xl">{{ perk.icon }}</span>
            <div>
              <p class="font-semibold text-[#1B1B1B] text-sm">{{ perk.label }}</p>
              <p class="text-gray-500 text-xs">{{ perk.desc }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Categories Section -->
    <section class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-12">
        <h2 class="section-title">Navegue por Categoria</h2>
        <p class="section-subtitle">Encontre a planta perfeita para cada ambiente</p>
      </div>

      <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <RouterLink
          v-for="cat in categories"
          :key="cat.id"
          :to="`/categorias/${cat.slug}`"
          class="group relative rounded-2xl overflow-hidden aspect-square bg-[#D8F3DC] cursor-pointer"
        >
          <img v-if="cat.image_url" :src="cat.image_url" :alt="cat.name" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
          <div class="absolute inset-0 bg-gradient-to-t from-[#1B4332]/80 via-transparent to-transparent"></div>
          <div class="absolute bottom-0 left-0 right-0 p-4">
            <p class="text-white font-semibold text-sm">{{ cat.name }}</p>
            <p class="text-[#A7F3D0] text-xs">{{ cat.products_count }} produtos</p>
          </div>
          <div v-if="!cat.image_url" class="absolute inset-0 flex items-center justify-center text-5xl">
            {{ categoryEmoji(cat.name) }}
          </div>
        </RouterLink>
      </div>
    </section>

    <!-- Featured Products -->
    <section class="py-20 bg-[#F8F4F0]">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-12">
          <div>
            <h2 class="section-title">Plantas em Destaque</h2>
            <p class="section-subtitle">Seleções especiais da nossa equipe</p>
          </div>
          <RouterLink to="/plantas?featured=true" class="btn-ghost hidden md:flex">
            Ver todos
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12,5 19,12 12,19"/>
            </svg>
          </RouterLink>
        </div>

        <div v-if="loadingFeatured" class="grid grid-cols-2 lg:grid-cols-4 gap-5">
          <div v-for="i in 4" :key="i" class="rounded-2xl bg-gray-200 animate-pulse aspect-[3/4]"></div>
        </div>

        <div v-else class="grid grid-cols-2 lg:grid-cols-4 gap-5">
          <ProductCard v-for="product in featured" :key="product.id" :product="product" />
        </div>

        <div class="text-center mt-8 md:hidden">
          <RouterLink to="/plantas?featured=true" class="btn-secondary">Ver todos os destaques</RouterLink>
        </div>
      </div>
    </section>

    <!-- Promo Banners -->
    <section v-if="banners.promos.length" class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div
          v-for="promo in banners.promos.slice(0, 2)"
          :key="promo.id"
          class="relative rounded-3xl overflow-hidden min-h-[220px] bg-gradient-to-br from-[#2D6A4F] to-[#1B4332] cursor-pointer group"
          @click="promo.button_url && $router.push(promo.button_url)"
        >
          <img v-if="promo.image_url" :src="promo.image_url" :alt="promo.title" class="absolute inset-0 w-full h-full object-cover opacity-30 group-hover:opacity-40 transition-opacity duration-300" />
          <div class="relative p-8 h-full flex flex-col justify-between">
            <div>
              <p class="text-[#A7F3D0] text-xs font-semibold tracking-widest uppercase mb-2">Promoção Especial</p>
              <h3 class="text-2xl font-bold text-white leading-tight" style="font-family: 'Playfair Display', serif">{{ promo.title }}</h3>
              <p v-if="promo.subtitle" class="text-[#D8F3DC]/80 mt-2 text-sm">{{ promo.subtitle }}</p>
            </div>
            <RouterLink v-if="promo.button_url" :to="promo.button_url" class="inline-flex items-center gap-2 text-white font-medium text-sm hover:gap-3 transition-all duration-200 mt-4">
              {{ promo.button_text || 'Ver oferta' }}
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12,5 19,12 12,19"/>
              </svg>
            </RouterLink>
          </div>
        </div>
      </div>
    </section>

    <!-- Newsletter -->
    <section class="py-20 bg-[#D8F3DC]">
      <div class="max-w-2xl mx-auto px-4 text-center">
        <span class="text-3xl mb-4 block">🌱</span>
        <h2 class="section-title text-[#1B4332] mb-3">Receba novidades & dicas</h2>
        <p class="text-[#2D6A4F] mb-8">Assine nossa newsletter e receba dicas de cuidados com plantas e ofertas exclusivas.</p>
        <form @submit.prevent="subscribeNewsletter" class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
          <input
            v-model="email"
            type="email"
            placeholder="seu@email.com"
            class="input-field flex-1"
            required
          />
          <button type="submit" class="btn-primary whitespace-nowrap justify-center">
            Assinar
          </button>
        </form>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import ProductCard from '../components/ProductCard.vue'
import { useUiStore } from '../stores/ui.js'

const ui             = useUiStore()
const featured       = ref([])
const categories     = ref([])
const banners        = ref({ heroes: [], promos: [] })
const loadingFeatured = ref(true)
const currentSlide   = ref(0)
const email          = ref('')

const bgPattern = 'background-image: repeating-linear-gradient(45deg, rgba(255,255,255,0.05) 0px, rgba(255,255,255,0.05) 1px, transparent 1px, transparent 10px)'

const stats = [
  { value: '500+', label: 'Espécies disponíveis' },
  { value: '10k+', label: 'Clientes felizes' },
  { value: '4.9★', label: 'Avaliação média' },
]

const perks = [
  { icon: '🚚', label: 'Frete Grátis', desc: 'Acima de R$ 200' },
  { icon: '📦', label: 'Embalagem Segura', desc: 'Chega perfeita' },
  { icon: '🌱', label: 'Plantas Saudáveis', desc: 'Garantia de qualidade' },
  { icon: '💳', label: 'Pagamento Seguro', desc: 'Via Mercado Pago' },
]

function categoryEmoji(name) {
  const n = name.toLowerCase()
  if (n.includes('cítri') || n.includes('citri')) return '🍋'
  if (n.includes('ornam') || n.includes('flor')) return '🌸'
  if (n.includes('sucu') || n.includes('cacto')) return '🌵'
  if (n.includes('erva') || n.includes('herb')) return '🌿'
  if (n.includes('árv') || n.includes('arv')) return '🌳'
  return '🪴'
}

async function subscribeNewsletter() {
  ui.showToast('Inscrição realizada com sucesso! 🌱')
  email.value = ''
}

async function fetchData() {
  try {
    const [prodRes, catRes, banRes] = await Promise.all([
      axios.get('/products/featured'),
      axios.get('/categories'),
      axios.get('/banners'),
    ])
    featured.value   = prodRes.data.data
    categories.value = catRes.data.data
    banners.value    = banRes.data
  } finally {
    loadingFeatured.value = false
  }
}

onMounted(fetchData)
</script>
