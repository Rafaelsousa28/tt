<template>
  <div class="min-h-screen bg-[#F8F4F0] pt-20 pb-16 flex items-center justify-center">
    <div class="max-w-2xl mx-auto px-4 text-center">
      <!-- Success state -->
      <div v-if="status !== 'failed'">
        <div class="w-24 h-24 bg-[#D8F3DC] rounded-full flex items-center justify-center mx-auto mb-6 animate-bounce">
          <svg class="w-12 h-12 text-[#2D6A4F]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <polyline points="20,6 9,17 4,12"/>
          </svg>
        </div>
        <h1 class="text-4xl font-bold text-[#1B4332] mb-3" style="font-family: 'Playfair Display', serif">
          {{ status === 'pending' ? 'Pedido Recebido!' : 'Pedido Confirmado!' }}
        </h1>
        <p class="text-gray-600 text-lg mb-2">
          {{ status === 'pending'
            ? 'Seu pedido foi recebido e o pagamento está sendo processado.'
            : 'Seu pagamento foi confirmado e seu pedido está sendo preparado.' }}
        </p>
        <p class="text-[#2D6A4F] font-semibold text-xl mb-8">Nº {{ $route.params.number }}</p>

        <div v-if="order" class="bg-white rounded-2xl p-6 shadow-sm text-left mb-8">
          <h2 class="font-semibold text-[#1B1B1B] mb-4">Resumo do Pedido</h2>
          <div class="space-y-3 divide-y divide-gray-50">
            <div v-for="item in order.items" :key="item.id" class="flex justify-between py-2">
              <span class="text-sm text-gray-700">{{ item.product_name }} × {{ item.quantity }}</span>
              <span class="text-sm font-medium text-[#1B1B1B]">{{ formatPrice(item.total) }}</span>
            </div>
          </div>
          <div class="flex justify-between font-bold text-[#1B1B1B] text-lg pt-4 mt-2 border-t border-gray-100">
            <span>Total</span>
            <span class="text-[#2D6A4F]">{{ formatPrice(order.total) }}</span>
          </div>
          <div class="mt-4 p-4 bg-[#F8F4F0] rounded-xl text-sm text-gray-600">
            <p><strong>Entregar em:</strong> {{ formatAddress(order.shipping_address) }}</p>
          </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
          <RouterLink to="/plantas" class="btn-primary">Continuar Comprando</RouterLink>
          <RouterLink to="/" class="btn-secondary">Voltar ao Início</RouterLink>
        </div>

        <p class="text-sm text-gray-400 mt-8 flex items-center justify-center gap-1">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>
          </svg>
          Confirmação enviada para {{ order?.customer_email }}
        </p>
      </div>

      <!-- Failed state -->
      <div v-else>
        <div class="w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
          <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
          </svg>
        </div>
        <h1 class="text-4xl font-bold text-gray-800 mb-3" style="font-family: 'Playfair Display', serif">Pagamento não concluído</h1>
        <p class="text-gray-500 mb-8">Não foi possível processar seu pagamento. Nenhuma cobrança foi realizada.</p>
        <RouterLink to="/checkout" class="btn-primary">Tentar Novamente</RouterLink>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'

const route  = useRoute()
const order  = ref(null)
const status = computed(() => route.query.status ?? 'approved')

function formatPrice(v) {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(v)
}

function formatAddress(addr) {
  if (!addr) return ''
  return `${addr.street}, ${addr.number}${addr.complement ? ` ${addr.complement}` : ''} – ${addr.district}, ${addr.city}/${addr.state}`
}

onMounted(async () => {
  try {
    const res = await axios.get(`/orders/${route.params.number}`)
    order.value = res.data.data
  } catch { /* ignore */ }
})
</script>
