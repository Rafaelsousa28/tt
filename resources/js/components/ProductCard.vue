<template>
  <RouterLink :to="`/plantas/${product.slug}`" class="group block">
    <article class="card overflow-hidden cursor-pointer">
      <!-- Image -->
      <div class="relative aspect-square bg-[#F8F4F0] overflow-hidden">
        <img
          v-if="product.first_image_url"
          :src="product.first_image_url"
          :alt="product.name"
          class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
          loading="lazy"
        />
        <div v-else class="w-full h-full flex items-center justify-center">
          <svg class="w-16 h-16 text-[#A7F3D0]" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2C9.5 2 7 4 7 7c0 2.5 1.5 4.5 3.5 5.5L10 22h4l-.5-9.5C15.5 11.5 17 9.5 17 7c0-3-2.5-5-5-5z"/>
          </svg>
        </div>

        <!-- Badges -->
        <div class="absolute top-3 left-3 flex flex-col gap-1.5">
          <span v-if="product.is_on_sale" class="badge badge-sale">Promoção</span>
          <span v-if="product.is_featured && !product.is_on_sale" class="badge badge-new">Destaque</span>
          <span v-if="product.stock <= 3 && product.stock > 0" class="badge badge-low-stock">Últimas unidades</span>
        </div>

        <!-- Quick Add -->
        <div class="absolute bottom-3 left-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
          <button
            @click.prevent="handleAddToCart"
            :disabled="product.stock === 0"
            class="w-full btn-primary py-2.5 text-xs justify-center shadow-lg"
          >
            {{ product.stock === 0 ? 'Esgotado' : 'Adicionar ao Carrinho' }}
          </button>
        </div>
      </div>

      <!-- Info -->
      <div class="p-4">
        <p class="text-xs text-[#52B788] font-medium mb-1">{{ product.category?.name }}</p>
        <h3 class="font-medium text-[#1B1B1B] text-sm leading-snug mb-2 line-clamp-2">{{ product.name }}</h3>

        <!-- Care indicators -->
        <div class="flex items-center gap-3 mb-3">
          <div class="flex items-center gap-1 text-xs text-gray-400">
            <span>{{ careLevelIcon }}</span>
            <span>{{ careLevelLabel }}</span>
          </div>
        </div>

        <!-- Price -->
        <div class="flex items-center justify-between">
          <div>
            <div v-if="product.is_on_sale" class="flex items-center gap-2">
              <span class="text-lg font-semibold text-[#2D6A4F]">
                {{ formatPrice(product.current_price) }}
              </span>
              <span class="text-sm text-gray-400 line-through">
                {{ formatPrice(product.price) }}
              </span>
            </div>
            <span v-else class="text-lg font-semibold text-[#2D6A4F]">
              {{ formatPrice(product.current_price ?? product.price) }}
            </span>
          </div>

          <button
            @click.prevent="handleAddToCart"
            :disabled="product.stock === 0"
            class="w-9 h-9 bg-[#D8F3DC] text-[#2D6A4F] rounded-full flex items-center justify-center
                   hover:bg-[#2D6A4F] hover:text-white transition-colors duration-200 disabled:opacity-40"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
              <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
          </button>
        </div>
      </div>
    </article>
  </RouterLink>
</template>

<script setup>
import { computed } from 'vue'
import { useCartStore } from '../stores/cart.js'
import { useUiStore } from '../stores/ui.js'

const props  = defineProps({ product: { type: Object, required: true } })
const cart   = useCartStore()
const ui     = useUiStore()

const careLevelIcon = computed(() => ({ facil: '🌿', medio: '🌱', dificil: '🌵' }[props.product.care_level] ?? '🌱'))
const careLevelLabel = computed(() => ({ facil: 'Fácil cuidado', medio: 'Cuidado médio', dificil: 'Especialista' }[props.product.care_level] ?? ''))

function formatPrice(value) {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value)
}

function handleAddToCart() {
  if (props.product.stock === 0) return
  cart.addItem(props.product)
  ui.showToast(`"${props.product.name}" adicionado ao carrinho!`)
}
</script>
