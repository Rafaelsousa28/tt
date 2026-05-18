<template>
  <Teleport to="body">
    <!-- Backdrop -->
    <Transition name="fade">
      <div
        v-if="cart.isOpen"
        class="fixed inset-0 bg-black/50 z-40"
        @click="cart.closeCart()"
      />
    </Transition>

    <!-- Sidebar -->
    <Transition name="slide-right">
      <aside v-if="cart.isOpen" class="fixed right-0 top-0 bottom-0 w-full max-w-sm bg-white z-50 flex flex-col shadow-2xl">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
          <div class="flex items-center gap-2">
            <h2 class="font-semibold text-[#1B1B1B] text-lg" style="font-family: 'Playfair Display', serif">Seu Carrinho</h2>
            <span class="text-xs bg-[#D8F3DC] text-[#2D6A4F] px-2 py-0.5 rounded-full font-medium">{{ cart.itemCount }}</span>
          </div>
          <button
            @click="cart.closeCart()"
            class="w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center text-gray-400 hover:text-gray-600 transition-colors"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
          </button>
        </div>

        <!-- Empty state -->
        <div v-if="cart.items.length === 0" class="flex-1 flex flex-col items-center justify-center gap-4 px-8 text-center">
          <div class="w-24 h-24 bg-[#F8F4F0] rounded-full flex items-center justify-center">
            <svg class="w-10 h-10 text-[#A7F3D0]" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 2C9.5 2 7 4 7 7c0 2.5 1.5 4.5 3.5 5.5L10 22h4l-.5-9.5C15.5 11.5 17 9.5 17 7c0-3-2.5-5-5-5z"/>
            </svg>
          </div>
          <div>
            <p class="font-medium text-[#1B1B1B] text-lg" style="font-family: 'Playfair Display', serif">Carrinho vazio</p>
            <p class="text-gray-400 text-sm mt-1">Adicione plantas lindas ao seu carrinho</p>
          </div>
          <RouterLink to="/plantas" @click="cart.closeCart()" class="btn-primary mt-2">
            Ver Plantas
          </RouterLink>
        </div>

        <!-- Items -->
        <div v-else class="flex-1 overflow-y-auto scrollbar-thin px-6 py-4 space-y-4">
          <div
            v-for="item in cart.items"
            :key="item.id"
            class="flex gap-3 py-3 border-b border-gray-50 last:border-0"
          >
            <RouterLink :to="`/plantas/${item.slug}`" @click="cart.closeCart()" class="flex-shrink-0">
              <img
                v-if="item.image"
                :src="item.image"
                :alt="item.name"
                class="w-16 h-16 object-cover rounded-xl bg-[#F8F4F0]"
              />
              <div v-else class="w-16 h-16 bg-[#F8F4F0] rounded-xl flex items-center justify-center">
                <span class="text-2xl">🌱</span>
              </div>
            </RouterLink>

            <div class="flex-1 min-w-0">
              <RouterLink :to="`/plantas/${item.slug}`" @click="cart.closeCart()">
                <p class="text-sm font-medium text-[#1B1B1B] line-clamp-2 leading-snug">{{ item.name }}</p>
              </RouterLink>
              <p class="text-[#2D6A4F] font-semibold text-sm mt-1">{{ formatPrice(item.price) }}</p>

              <!-- Quantity controls -->
              <div class="flex items-center gap-2 mt-2">
                <button
                  @click="cart.updateQuantity(item.id, item.quantity - 1)"
                  class="w-6 h-6 rounded-full border border-gray-200 flex items-center justify-center text-gray-500 hover:border-[#2D6A4F] hover:text-[#2D6A4F] transition-colors text-xs"
                >−</button>
                <span class="text-sm font-medium w-5 text-center">{{ item.quantity }}</span>
                <button
                  @click="cart.updateQuantity(item.id, item.quantity + 1)"
                  :disabled="item.quantity >= item.stock"
                  class="w-6 h-6 rounded-full border border-gray-200 flex items-center justify-center text-gray-500 hover:border-[#2D6A4F] hover:text-[#2D6A4F] transition-colors text-xs disabled:opacity-40"
                >+</button>
              </div>
            </div>

            <div class="flex flex-col items-end justify-between">
              <button
                @click="cart.removeItem(item.id)"
                class="text-gray-300 hover:text-red-400 transition-colors"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <polyline points="3,6 5,6 21,6"/><path d="M19,6v14a2,2,0,0,1-2,2H7a2,2,0,0,1-2-2V6m3,0V4a1,1,0,0,1,1-1h4a1,1,0,0,1,1,1v2"/>
                </svg>
              </button>
              <span class="text-sm font-semibold text-[#1B1B1B]">{{ formatPrice(item.price * item.quantity) }}</span>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div v-if="cart.items.length > 0" class="px-6 py-5 border-t border-gray-100 space-y-3">
          <div class="flex justify-between text-sm text-gray-500">
            <span>Subtotal</span>
            <span>{{ formatPrice(cart.subtotal) }}</span>
          </div>
          <div v-if="cart.shippingCost === 0" class="flex justify-between text-sm text-[#52B788] font-medium">
            <span>Frete</span>
            <span>Grátis 🎉</span>
          </div>
          <div v-else class="flex justify-between text-sm text-gray-500">
            <span>Frete estimado</span>
            <span>{{ formatPrice(cart.shippingCost) }}</span>
          </div>
          <div class="flex justify-between font-semibold text-[#1B1B1B] text-base pt-2 border-t border-gray-100">
            <span>Total</span>
            <span>{{ formatPrice(cart.total) }}</span>
          </div>

          <p v-if="cart.shippingCost > 0" class="text-xs text-[#52B788] text-center">
            Frete grátis para compras acima de R$ 200,00
          </p>

          <RouterLink
            to="/checkout"
            @click="cart.closeCart()"
            class="btn-primary w-full justify-center py-3.5"
          >
            Finalizar Compra
          </RouterLink>
          <RouterLink
            to="/carrinho"
            @click="cart.closeCart()"
            class="block text-center text-sm text-gray-400 hover:text-[#2D6A4F] transition-colors"
          >
            Ver carrinho completo
          </RouterLink>
        </div>
      </aside>
    </Transition>
  </Teleport>

  <!-- Toast Notification -->
  <Teleport to="body">
    <Transition name="toast">
      <div
        v-if="ui.toast"
        class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[60] px-5 py-3 rounded-full shadow-lg text-sm font-medium flex items-center gap-2"
        :class="{
          'bg-[#2D6A4F] text-white': ui.toast.type === 'success',
          'bg-red-500 text-white': ui.toast.type === 'error',
          'bg-amber-500 text-white': ui.toast.type === 'warning',
        }"
      >
        <span v-if="ui.toast.type === 'success'">✓</span>
        {{ ui.toast.message }}
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { useCartStore } from '../stores/cart.js'
import { useUiStore } from '../stores/ui.js'

const cart = useCartStore()
const ui   = useUiStore()

function formatPrice(value) {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value)
}
</script>

<style scoped>
.slide-right-enter-active, .slide-right-leave-active { transition: transform 0.3s cubic-bezier(0.4,0,0.2,1); }
.slide-right-enter-from, .slide-right-leave-to { transform: translateX(100%); }

.toast-enter-active, .toast-leave-active { transition: all 0.3s ease; }
.toast-enter-from, .toast-leave-to { opacity: 0; transform: translateX(-50%) translateY(1rem); }
</style>
