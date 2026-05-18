<template>
  <div class="min-h-screen bg-white pt-20">

    <!-- Loading -->
    <div v-if="loading" class="max-w-7xl mx-auto px-4 py-16">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 animate-pulse">
        <div class="aspect-square bg-gray-100 rounded-2xl"></div>
        <div class="space-y-4 py-4">
          <div class="h-4 bg-gray-100 rounded w-24"></div>
          <div class="h-8 bg-gray-100 rounded w-3/4"></div>
          <div class="h-10 bg-gray-100 rounded w-1/3 mt-4"></div>
          <div class="h-6 bg-gray-100 rounded w-1/2"></div>
          <div class="h-12 bg-gray-100 rounded mt-6"></div>
        </div>
      </div>
    </div>

    <div v-else-if="product" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

      <!-- Breadcrumb -->
      <nav class="flex items-center gap-1.5 text-xs text-gray-400 mb-8 flex-wrap">
        <RouterLink to="/" class="hover:text-[#2D6A4F]">Início</RouterLink>
        <span>/</span>
        <RouterLink to="/plantas" class="hover:text-[#2D6A4F]">Plantas</RouterLink>
        <span>/</span>
        <RouterLink v-if="product.category" :to="`/categorias/${product.category.slug}`" class="hover:text-[#2D6A4F]">{{ product.category.name }}</RouterLink>
        <span>/</span>
        <span class="text-[#1B1B1B] font-medium truncate max-w-[200px]">{{ product.name }}</span>
      </nav>

      <!-- Main grid -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16">

        <!-- ── Galeria ── -->
        <div class="flex gap-3">
          <!-- Thumbnails coluna esquerda -->
          <div v-if="product.image_urls?.length > 1" class="flex flex-col gap-2 w-16 flex-shrink-0">
            <button
              v-for="(img, i) in product.image_urls"
              :key="i"
              @click="currentImageIndex = i"
              class="w-16 h-16 rounded-xl overflow-hidden border-2 transition-all duration-200 flex-shrink-0"
              :class="currentImageIndex === i
                ? 'border-[#2D6A4F] shadow-sm'
                : 'border-gray-100 hover:border-[#52B788]'"
            >
              <img :src="img" :alt="`foto ${i+1}`" class="w-full h-full object-cover" />
            </button>
          </div>

          <!-- Imagem principal -->
          <div class="flex-1 relative rounded-2xl overflow-hidden bg-[#F8F4F0] aspect-square">
            <img
              v-if="currentImage"
              :src="currentImage"
              :alt="product.name"
              class="w-full h-full object-cover transition-opacity duration-300"
            />
            <div v-else class="w-full h-full flex items-center justify-center text-8xl">🌱</div>

            <!-- Badge desconto -->
            <div v-if="product.is_on_sale" class="absolute top-3 left-3 bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full">
              -{{ discountPercent }}%
            </div>
          </div>
        </div>

        <!-- ── Informações ── -->
        <div class="flex flex-col gap-5">

          <!-- Categoria + Nome -->
          <div>
            <RouterLink
              v-if="product.category"
              :to="`/categorias/${product.category.slug}`"
              class="text-xs font-semibold text-[#52B788] uppercase tracking-widest hover:text-[#2D6A4F]"
            >{{ product.category.name }}</RouterLink>
            <h1 class="text-2xl md:text-3xl font-bold text-[#1B1B1B] leading-snug mt-1"
              style="font-family: 'Playfair Display', serif">{{ product.name }}</h1>

            <!-- Avaliações -->
            <div class="flex items-center gap-2 mt-2">
              <div class="flex text-amber-400 text-sm">★★★★★</div>
              <span class="text-xs text-gray-400">(47 avaliações)</span>
              <span v-if="product.sku" class="text-xs text-gray-300 ml-2">SKU: {{ product.sku }}</span>
            </div>
          </div>

          <!-- ── BLOCO DE PREÇO ── -->
          <div class="bg-[#F8F4F0] rounded-2xl p-5 space-y-2">
            <!-- Preço riscado (se promoção) -->
            <p v-if="product.is_on_sale" class="text-sm text-gray-400 line-through">
              De: {{ formatPrice(product.price) }}
            </p>

            <!-- Preço principal -->
            <div class="flex items-baseline gap-3 flex-wrap">
              <span class="text-4xl font-extrabold text-[#1B1B1B]">{{ formatPrice(currentPrice) }}</span>
              <span v-if="product.is_on_sale" class="text-sm font-semibold bg-red-100 text-red-600 px-2 py-0.5 rounded-full">
                {{ discountPercent }}% OFF
              </span>
            </div>

            <!-- À vista com desconto PIX -->
            <p class="text-base font-bold text-[#2D6A4F]">
              {{ formatPrice(pixPrice) }}
              <span class="text-sm font-medium text-gray-500"> à vista via PIX</span>
              <span class="ml-2 text-xs bg-[#D8F3DC] text-[#2D6A4F] font-bold px-2 py-0.5 rounded-full">7% OFF</span>
            </p>

            <!-- Parcelamento -->
            <p class="text-sm text-gray-600">
              ou <span class="font-semibold text-[#1B1B1B]">3x de {{ formatPrice(installmentPrice) }}</span> sem juros no cartão
            </p>
          </div>

          <!-- Estoque -->
          <div class="flex items-center gap-2">
            <span v-if="product.stock > 5"    class="w-2 h-2 bg-green-400 rounded-full"></span>
            <span v-else-if="product.stock > 0" class="w-2 h-2 bg-amber-400 rounded-full animate-pulse"></span>
            <span v-else                        class="w-2 h-2 bg-red-400 rounded-full"></span>
            <span class="text-sm text-gray-600 font-medium">
              <template v-if="product.stock > 5">Em estoque</template>
              <template v-else-if="product.stock > 0">Últimas <strong>{{ product.stock }}</strong> unidades!</template>
              <template v-else>Fora de estoque</template>
            </span>
          </div>

          <!-- Quantidade -->
          <div class="flex items-center gap-4">
            <span class="text-sm font-medium text-gray-700">Quantidade:</span>
            <div class="flex items-center border border-gray-200 rounded-xl overflow-hidden">
              <button @click="quantity = Math.max(1, quantity - 1)"
                class="px-4 py-2.5 hover:bg-gray-50 text-gray-600 text-lg font-medium transition-colors">−</button>
              <span class="w-10 text-center font-semibold text-[#1B1B1B]">{{ quantity }}</span>
              <button @click="quantity = Math.min(product.stock, quantity + 1)"
                :disabled="quantity >= product.stock"
                class="px-4 py-2.5 hover:bg-gray-50 text-gray-600 text-lg font-medium transition-colors disabled:opacity-30">+</button>
            </div>
          </div>

          <!-- Botões de ação -->
          <div class="flex flex-col gap-3">
            <button
              @click="addToCart"
              :disabled="product.stock === 0"
              class="btn-primary py-4 justify-center text-base w-full disabled:opacity-50"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                <line x1="3" y1="6" x2="21" y2="6"/>
                <path d="M16 10a4 4 0 0 1-8 0"/>
              </svg>
              {{ product.stock === 0 ? 'Produto Esgotado' : 'Adicionar ao Carrinho' }}
            </button>

            <!-- WhatsApp -->
            <a
              :href="whatsappLink"
              target="_blank"
              rel="noopener"
              class="flex items-center justify-center gap-2 w-full py-4 rounded-full border-2 border-[#25D366] text-[#25D366] font-semibold text-base hover:bg-[#25D366] hover:text-white transition-all duration-200"
            >
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12 0C5.373 0 0 5.373 0 12c0 2.127.558 4.126 1.532 5.859L.054 23.077a.75.75 0 0 0 .924.924l5.217-1.478A11.952 11.952 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.907 0-3.691-.528-5.215-1.446l-.37-.228-3.899 1.104 1.104-3.898-.228-.371A9.956 9.956 0 0 1 2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
              </svg>
              Comprar pelo WhatsApp
            </a>
          </div>

          <!-- Calculadora de frete -->
          <div class="border border-gray-100 rounded-2xl p-4">
            <p class="text-sm font-semibold text-[#1B1B1B] mb-3 flex items-center gap-2">
              <svg class="w-4 h-4 text-[#52B788]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 5v3h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>
              </svg>
              Calcular frete
            </p>
            <div class="flex gap-2">
              <input
                v-model="cep"
                @keydown.enter="calcularFrete"
                type="text"
                placeholder="Digite seu CEP"
                maxlength="9"
                class="input-field text-sm py-2.5 flex-1"
              />
              <button @click="calcularFrete" class="btn-primary text-sm py-2.5 px-5 whitespace-nowrap">
                Calcular
              </button>
            </div>
            <a href="https://buscacepinter.correios.com.br" target="_blank" rel="noopener"
              class="text-xs text-[#52B788] hover:underline mt-1.5 block">Não sei meu CEP</a>

            <!-- Resultado frete -->
            <div v-if="freteResult" class="mt-3 space-y-2">
              <div v-for="opcao in freteResult" :key="opcao.label"
                class="flex items-center justify-between bg-[#F8F4F0] rounded-xl px-4 py-2.5">
                <div>
                  <p class="text-sm font-semibold text-[#1B1B1B]">{{ opcao.label }}</p>
                  <p class="text-xs text-gray-400">{{ opcao.prazo }}</p>
                </div>
                <span class="text-sm font-bold text-[#2D6A4F]">{{ opcao.valor }}</span>
              </div>
            </div>
          </div>

          <!-- Garantias -->
          <div class="grid grid-cols-3 gap-3 pt-1">
            <div class="text-center">
              <div class="text-2xl mb-1">📦</div>
              <p class="text-xs font-medium text-gray-600">Embalagem segura</p>
            </div>
            <div class="text-center">
              <div class="text-2xl mb-1">🌱</div>
              <p class="text-xs font-medium text-gray-600">Qualidade garantida</p>
            </div>
            <div class="text-center">
              <div class="text-2xl mb-1">🔒</div>
              <p class="text-xs font-medium text-gray-600">Compra segura</p>
            </div>
          </div>
        </div>
      </div>

      <!-- ── TABS: Descrição / Características / Pagamento ── -->
      <div class="mt-14 border-t border-gray-100 pt-10">
        <div class="flex gap-1 border-b border-gray-200 mb-8">
          <button
            v-for="tab in tabs"
            :key="tab.key"
            @click="activeTab = tab.key"
            class="px-6 py-3 text-sm font-medium border-b-2 transition-colors duration-200 -mb-px"
            :class="activeTab === tab.key
              ? 'border-[#2D6A4F] text-[#2D6A4F]'
              : 'border-transparent text-gray-500 hover:text-[#2D6A4F]'"
          >{{ tab.label }}</button>
        </div>

        <!-- Descrição -->
        <div v-if="activeTab === 'descricao'" class="max-w-3xl">
          <div
            v-if="product.description"
            class="prose prose-green max-w-none text-gray-600 leading-relaxed"
            v-html="product.description"
          ></div>
          <p v-else class="text-gray-400">Nenhuma descrição disponível.</p>
        </div>

        <!-- Características -->
        <div v-if="activeTab === 'caracteristicas'">
          <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mb-8">
            <div v-for="char in productChars" :key="char.label"
              class="bg-[#F8F4F0] rounded-2xl p-4 flex flex-col items-center text-center gap-2">
              <span class="text-3xl">{{ char.icon }}</span>
              <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ char.label }}</p>
              <p class="text-sm font-bold text-[#1B1B1B]">{{ char.value }}</p>
            </div>
          </div>

          <!-- Tabela de dados extras -->
          <div v-if="product.sku || product.height_cm || product.pot_size_cm" class="border border-gray-100 rounded-2xl overflow-hidden">
            <table class="w-full text-sm">
              <tbody>
                <tr v-if="product.sku" class="border-b border-gray-50">
                  <td class="px-5 py-3 font-medium text-gray-500 bg-[#F8F4F0] w-1/3">SKU</td>
                  <td class="px-5 py-3 text-[#1B1B1B]">{{ product.sku }}</td>
                </tr>
                <tr v-if="product.height_cm" class="border-b border-gray-50">
                  <td class="px-5 py-3 font-medium text-gray-500 bg-[#F8F4F0]">Altura</td>
                  <td class="px-5 py-3 text-[#1B1B1B]">{{ product.height_cm }} cm</td>
                </tr>
                <tr v-if="product.pot_size_cm">
                  <td class="px-5 py-3 font-medium text-gray-500 bg-[#F8F4F0]">Tamanho do Vaso</td>
                  <td class="px-5 py-3 text-[#1B1B1B]">{{ product.pot_size_cm }} cm</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Formas de Pagamento -->
        <div v-if="activeTab === 'pagamento'" class="max-w-xl space-y-5">
          <div class="bg-[#F8F4F0] rounded-2xl p-5">
            <div class="flex items-center gap-3 mb-4">
              <span class="text-2xl">🏦</span>
              <div>
                <p class="font-semibold text-[#1B1B1B]">PIX</p>
                <p class="text-sm text-[#2D6A4F] font-medium">7% de desconto — {{ formatPrice(pixPrice) }}</p>
              </div>
            </div>
            <div class="flex items-center gap-3">
              <span class="text-2xl">💳</span>
              <div>
                <p class="font-semibold text-[#1B1B1B]">Cartão de Crédito</p>
                <div class="text-sm text-gray-500 space-y-0.5">
                  <p>1x de {{ formatPrice(currentPrice) }} sem juros</p>
                  <p>2x de {{ formatPrice(currentPrice / 2) }} sem juros</p>
                  <p class="font-semibold text-[#1B1B1B]">3x de {{ formatPrice(installmentPrice) }} sem juros</p>
                  <p>até 12x com juros</p>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-[#F8F4F0] rounded-2xl p-5 flex items-start gap-3">
            <span class="text-2xl">💚</span>
            <div>
              <p class="font-semibold text-[#1B1B1B]">WhatsApp</p>
              <p class="text-sm text-gray-500">Negocie diretamente e pague de forma facilitada.</p>
              <a :href="whatsappLink" target="_blank" rel="noopener"
                class="text-sm text-[#25D366] font-medium hover:underline mt-1 inline-block">(88) 99904-2983</a>
            </div>
          </div>

          <p class="text-xs text-gray-400 flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
            Pagamentos processados com segurança pelo Mercado Pago
          </p>
        </div>
      </div>

      <!-- ── Produtos Relacionados ── -->
      <div v-if="related.length" class="mt-16 pt-10 border-t border-gray-100">
        <h2 class="text-2xl font-bold text-[#1B1B1B] mb-2" style="font-family: 'Playfair Display', serif">
          Aproveite para plantar também
        </h2>
        <p class="text-gray-400 text-sm mb-8">Produtos que combinam com este</p>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
          <ProductCard v-for="p in related" :key="p.id" :product="p" />
        </div>
      </div>
    </div>

    <!-- Not found -->
    <div v-else class="text-center py-32">
      <div class="text-6xl mb-4">🌿</div>
      <h2 class="text-2xl font-semibold text-gray-700">Produto não encontrado</h2>
      <RouterLink to="/plantas" class="btn-primary mt-6 inline-flex">Ver Todos os Produtos</RouterLink>
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
const activeTab         = ref('descricao')
const cep               = ref('')
const freteResult       = ref(null)

const tabs = [
  { key: 'descricao',    label: 'Descrição' },
  { key: 'caracteristicas', label: 'Características' },
  { key: 'pagamento',    label: 'Formas de Pagamento' },
]

const currentImage = computed(() => {
  const imgs = product.value?.image_urls ?? []
  return imgs[currentImageIndex.value] ?? product.value?.first_image_url ?? null
})

const currentPrice = computed(() =>
  parseFloat(product.value?.current_price ?? product.value?.price ?? 0)
)

const pixPrice = computed(() => currentPrice.value * 0.93)

const installmentPrice = computed(() => currentPrice.value / 3)

const discountPercent = computed(() => {
  if (!product.value?.is_on_sale) return 0
  return Math.round((1 - currentPrice.value / parseFloat(product.value.price)) * 100)
})

const whatsappLink = computed(() => {
  if (!product.value) return 'https://wa.me/5588999042983'
  const msg = encodeURIComponent(
    `Olá! Tenho interesse em: *${product.value.name}* — ${formatPrice(currentPrice.value)}\n` +
    `Link: ${window.location.href}`
  )
  return `https://wa.me/5588999042983?text=${msg}`
})

const productChars = computed(() => {
  if (!product.value) return []
  const chars = []
  if (product.value.care_level) {
    chars.push({
      icon: { facil: '🌿', medio: '🌱', dificil: '🌵' }[product.value.care_level] ?? '🌱',
      label: 'Cuidado',
      value: { facil: 'Fácil', medio: 'Médio', dificil: 'Difícil' }[product.value.care_level],
    })
  }
  if (product.value.light_requirement) {
    chars.push({
      icon: '☀️',
      label: 'Luminosidade',
      value: { baixa: 'Sombra', media: 'Meia-sombra', alta: 'Sol indireto', pleno_sol: 'Pleno sol' }[product.value.light_requirement],
    })
  }
  if (product.value.water_frequency) {
    chars.push({ icon: '💧', label: 'Rega', value: product.value.water_frequency })
  }
  if (product.value.height_cm) {
    chars.push({ icon: '📏', label: 'Altura', value: `${product.value.height_cm} cm` })
  }
  if (product.value.pot_size_cm) {
    chars.push({ icon: '🪴', label: 'Vaso', value: `Ø ${product.value.pot_size_cm} cm` })
  }
  return chars
})

function formatPrice(v) {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(v)
}

function addToCart() {
  if (!product.value || product.value.stock === 0) return
  cart.addItem(product.value, quantity.value)
  ui.showToast(`"${product.value.name}" adicionado ao carrinho!`)
}

async function calcularFrete() {
  const clean = cep.value.replace(/\D/g, '')
  if (clean.length !== 8) return

  const stateRates = {
    SP: { pac: 15.90, sedex: 28.90, pacDias: '5–8 dias úteis', sedexDias: '1–2 dias úteis' },
    RJ: { pac: 18.90, sedex: 32.90, pacDias: '5–8 dias úteis', sedexDias: '2–3 dias úteis' },
    MG: { pac: 16.90, sedex: 29.90, pacDias: '4–7 dias úteis', sedexDias: '2–3 dias úteis' },
    CE: { pac: 22.90, sedex: 38.90, pacDias: '6–10 dias úteis', sedexDias: '3–4 dias úteis' },
  }

  try {
    const res   = await axios.get(`https://viacep.com.br/ws/${clean}/json/`)
    const state = res.data.uf ?? 'XX'
    const rates = stateRates[state] ?? { pac: 34.90, sedex: 54.90, pacDias: '8–12 dias úteis', sedexDias: '4–5 dias úteis' }

    freteResult.value = [
      { label: 'PAC', prazo: rates.pacDias,   valor: currentPrice.value >= 200 ? 'Grátis' : formatPrice(rates.pac) },
      { label: 'SEDEX', prazo: rates.sedexDias, valor: formatPrice(rates.sedex) },
    ]
  } catch {
    ui.showToast('CEP inválido ou não encontrado.', 'error')
  }
}

async function fetchProduct() {
  loading.value    = true
  activeTab.value  = 'descricao'
  freteResult.value = null
  try {
    const res     = await axios.get(`/products/${route.params.slug}`)
    product.value = res.data.product
    related.value = res.data.related
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
