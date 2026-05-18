<template>
  <div class="min-h-screen bg-[#F8F4F0] pt-20 pb-16">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
      <h1 class="section-title mb-2">Seu Carrinho</h1>
      <p class="section-subtitle mb-10">{{ cart.itemCount }} {{ cart.itemCount === 1 ? 'item' : 'itens' }}</p>

      <!-- Empty -->
      <div v-if="cart.items.length === 0" class="text-center py-24 bg-white rounded-3xl">
        <div class="text-7xl mb-5">🌿</div>
        <h3 class="text-2xl font-semibold text-gray-700 mb-3" style="font-family: 'Playfair Display', serif">Seu carrinho está vazio</h3>
        <p class="text-gray-400 mb-8">Que tal explorar nossas plantas?</p>
        <RouterLink to="/plantas" class="btn-primary">Explorar Plantas</RouterLink>
      </div>

      <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Items -->
        <div class="lg:col-span-2 space-y-4">
          <TransitionGroup name="list">
            <div
              v-for="item in cart.items"
              :key="item.id"
              class="bg-white rounded-2xl p-5 shadow-sm flex gap-4"
            >
              <RouterLink :to="`/plantas/${item.slug}`" class="flex-shrink-0">
                <div class="w-24 h-24 bg-[#F8F4F0] rounded-xl overflow-hidden">
                  <img v-if="item.image" :src="item.image" :alt="item.name" class="w-full h-full object-cover" />
                  <div v-else class="w-full h-full flex items-center justify-center text-3xl">🌱</div>
                </div>
              </RouterLink>

              <div class="flex-1 min-w-0">
                <RouterLink :to="`/plantas/${item.slug}`">
                  <h3 class="font-medium text-[#1B1B1B] leading-snug hover:text-[#2D6A4F] transition-colors">{{ item.name }}</h3>
                </RouterLink>
                <p class="text-[#2D6A4F] font-semibold mt-1">{{ formatPrice(item.price) }} / un</p>

                <div class="flex items-center gap-4 mt-3">
                  <div class="flex items-center gap-2 border border-gray-200 rounded-lg overflow-hidden">
                    <button @click="cart.updateQuantity(item.id, item.quantity - 1)" class="px-3 py-1.5 hover:bg-gray-50 transition-colors text-gray-600">−</button>
                    <span class="w-8 text-center text-sm font-semibold">{{ item.quantity }}</span>
                    <button @click="cart.updateQuantity(item.id, item.quantity + 1)" :disabled="item.quantity >= item.stock" class="px-3 py-1.5 hover:bg-gray-50 transition-colors text-gray-600 disabled:opacity-30">+</button>
                  </div>
                  <button @click="cart.removeItem(item.id)" class="text-gray-400 hover:text-red-400 transition-colors text-sm flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <polyline points="3,6 5,6 21,6"/><path d="M19,6v14a2,2,0,0,1-2,2H7a2,2,0,0,1-2-2V6m3,0V4a1,1,0,0,1,1-1h4a1,1,0,0,1,1,1v2"/>
                    </svg>
                    Remover
                  </button>
                </div>
              </div>

              <div class="flex-shrink-0 text-right">
                <p class="font-semibold text-[#1B1B1B] text-lg">{{ formatPrice(item.price * item.quantity) }}</p>
              </div>
            </div>
          </TransitionGroup>

          <div class="flex justify-between items-center pt-2">
            <RouterLink to="/plantas" class="btn-ghost text-[#2D6A4F]">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12,19 5,12 12,5"/>
              </svg>
              Continuar comprando
            </RouterLink>
            <button @click="cart.clearCart()" class="text-sm text-gray-400 hover:text-red-400 transition-colors">Limpar carrinho</button>
          </div>
        </div>

        <!-- Summary -->
        <div>
          <div class="bg-white rounded-2xl p-6 shadow-sm sticky top-24">
            <h2 class="font-semibold text-[#1B1B1B] text-lg mb-5">Resumo</h2>

            <div class="space-y-3 text-sm">
              <div class="flex justify-between text-gray-600">
                <span>Subtotal ({{ cart.itemCount }} itens)</span>
                <span>{{ formatPrice(cart.subtotal) }}</span>
              </div>
              <div v-if="cart.discount > 0" class="flex justify-between text-[#52B788]">
                <span>Desconto</span>
                <span>-{{ formatPrice(cart.discount) }}</span>
              </div>
              <div class="flex justify-between text-gray-600">
                <span>Frete estimado</span>
                <span v-if="cart.shippingCost === 0" class="text-[#52B788] font-medium">Grátis 🎉</span>
                <span v-else>{{ formatPrice(cart.shippingCost) }}</span>
              </div>
            </div>

            <div class="flex justify-between font-bold text-[#1B1B1B] text-xl pt-4 mt-3 border-t border-gray-100">
              <span>Total</span>
              <span class="text-[#2D6A4F]">{{ formatPrice(cart.total) }}</span>
            </div>

            <p v-if="cart.shippingCost > 0" class="text-xs text-gray-400 mt-2 text-center">
              Falta <span class="text-[#2D6A4F] font-medium">{{ formatPrice(200 - cart.subtotal) }}</span> para frete grátis
            </p>

            <!-- Free shipping bar -->
            <div v-if="cart.shippingCost > 0" class="mt-3 h-1.5 bg-gray-100 rounded-full overflow-hidden">
              <div
                class="h-full bg-[#52B788] rounded-full transition-all duration-500"
                :style="`width: ${Math.min(100, (cart.subtotal / 200) * 100)}%`"
              ></div>
            </div>

            <RouterLink to="/checkout" class="btn-primary w-full justify-center py-4 mt-6 text-base">
              Finalizar Compra
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12,5 19,12 12,19"/>
              </svg>
            </RouterLink>

            <div class="flex items-center justify-center gap-2 mt-4 text-xs text-gray-400">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
              </svg>
              Compra segura e protegida
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useCartStore } from '../stores/cart.js'
const cart = useCartStore()

function formatPrice(v) {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(v)
}
</script>

<style scoped>
.list-move, .list-enter-active, .list-leave-active { transition: all 0.3s ease; }
.list-enter-from, .list-leave-to { opacity: 0; transform: translateX(-20px); }
.list-leave-active { position: absolute; }
</style>
