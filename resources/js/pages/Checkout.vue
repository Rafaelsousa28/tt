<template>
  <div class="min-h-screen bg-[#F8F4F0] pt-20 pb-16">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
      <h1 class="section-title mb-2">Finalizar Pedido</h1>
      <p class="section-subtitle mb-10">Quase lá! Preencha seus dados para concluir a compra.</p>

      <!-- Empty cart redirect -->
      <div v-if="cart.items.length === 0" class="text-center py-20">
        <div class="text-6xl mb-4">🛒</div>
        <h3 class="text-xl font-semibold text-gray-700 mb-2">Seu carrinho está vazio</h3>
        <RouterLink to="/plantas" class="btn-primary mt-4">Voltar para a Loja</RouterLink>
      </div>

      <form v-else @submit.prevent="submitOrder" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Form columns -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Personal Info -->
          <div class="bg-white rounded-2xl p-6 shadow-sm">
            <h2 class="font-semibold text-[#1B1B1B] text-lg mb-5 flex items-center gap-2">
              <span class="w-7 h-7 bg-[#D8F3DC] text-[#2D6A4F] rounded-full flex items-center justify-center text-sm font-bold">1</span>
              Dados Pessoais
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nome completo *</label>
                <input v-model="form.customer_name" type="text" class="input-field" placeholder="João da Silva" required />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">E-mail *</label>
                <input v-model="form.customer_email" type="email" class="input-field" placeholder="joao@email.com" required />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Telefone / WhatsApp *</label>
                <input v-model="form.customer_phone" type="tel" class="input-field" placeholder="(11) 99999-9999" required />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">CPF</label>
                <input v-model="form.customer_cpf" type="text" class="input-field" placeholder="000.000.000-00" />
              </div>
            </div>
          </div>

          <!-- Shipping Address -->
          <div class="bg-white rounded-2xl p-6 shadow-sm">
            <h2 class="font-semibold text-[#1B1B1B] text-lg mb-5 flex items-center gap-2">
              <span class="w-7 h-7 bg-[#D8F3DC] text-[#2D6A4F] rounded-full flex items-center justify-center text-sm font-bold">2</span>
              Endereço de Entrega
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">CEP *</label>
                <input
                  v-model="form.shipping_address.zip_code"
                  @blur="fetchCep"
                  type="text"
                  class="input-field"
                  placeholder="00000-000"
                  maxlength="9"
                  required
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Estado *</label>
                <select v-model="form.shipping_address.state" class="input-field" required>
                  <option value="">Selecione...</option>
                  <option v-for="uf in ufs" :key="uf" :value="uf">{{ uf }}</option>
                </select>
              </div>
              <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Rua / Logradouro *</label>
                <input v-model="form.shipping_address.street" type="text" class="input-field" placeholder="Rua das Flores" required />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Número *</label>
                <input v-model="form.shipping_address.number" type="text" class="input-field" placeholder="123" required />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Complemento</label>
                <input v-model="form.shipping_address.complement" type="text" class="input-field" placeholder="Apto 12" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Bairro *</label>
                <input v-model="form.shipping_address.district" type="text" class="input-field" placeholder="Jardim Paulista" required />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Cidade *</label>
                <input v-model="form.shipping_address.city" type="text" class="input-field" placeholder="São Paulo" required />
              </div>
            </div>
          </div>

          <!-- Notes -->
          <div class="bg-white rounded-2xl p-6 shadow-sm">
            <h2 class="font-semibold text-[#1B1B1B] text-lg mb-4">Observações (opcional)</h2>
            <textarea v-model="form.notes" rows="3" class="input-field resize-none" placeholder="Instruções especiais para entrega, cuidados, etc."></textarea>
          </div>
        </div>

        <!-- Order Summary -->
        <div class="space-y-4">
          <div class="bg-white rounded-2xl p-6 shadow-sm sticky top-24">
            <h2 class="font-semibold text-[#1B1B1B] text-lg mb-5">Resumo do Pedido</h2>

            <!-- Items -->
            <div class="space-y-3 mb-5">
              <div v-for="item in cart.items" :key="item.id" class="flex gap-3">
                <div class="w-12 h-12 bg-[#F8F4F0] rounded-lg flex-shrink-0 overflow-hidden">
                  <img v-if="item.image" :src="item.image" :alt="item.name" class="w-full h-full object-cover" />
                  <div v-else class="w-full h-full flex items-center justify-center text-xl">🌱</div>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium text-gray-700 truncate">{{ item.name }}</p>
                  <p class="text-xs text-gray-400">Qtd: {{ item.quantity }}</p>
                </div>
                <p class="text-sm font-semibold text-[#1B1B1B] flex-shrink-0">{{ formatPrice(item.price * item.quantity) }}</p>
              </div>
            </div>

            <!-- Coupon -->
            <div class="border-t border-gray-100 pt-4 mb-4">
              <div class="flex gap-2">
                <input
                  v-model="couponCode"
                  type="text"
                  placeholder="Cupom de desconto"
                  class="input-field text-sm py-2 flex-1"
                  :disabled="!!cart.coupon"
                />
                <button
                  v-if="!cart.coupon"
                  @click.prevent="applyCoupon"
                  type="button"
                  class="btn-secondary text-xs py-2 px-4 whitespace-nowrap"
                >Aplicar</button>
                <button
                  v-else
                  @click.prevent="removeCoupon"
                  type="button"
                  class="btn-ghost text-xs py-2 px-3 text-red-400 hover:text-red-600 border border-red-200"
                >✕</button>
              </div>
              <p v-if="couponError" class="text-red-500 text-xs mt-1.5">{{ couponError }}</p>
              <p v-if="cart.coupon" class="text-[#52B788] text-xs mt-1.5">✓ Cupom "{{ cart.coupon.code }}" aplicado!</p>
            </div>

            <!-- Totals -->
            <div class="space-y-2 text-sm">
              <div class="flex justify-between text-gray-600">
                <span>Subtotal</span>
                <span>{{ formatPrice(cart.subtotal) }}</span>
              </div>
              <div v-if="cart.discount > 0" class="flex justify-between text-[#52B788]">
                <span>Desconto</span>
                <span>-{{ formatPrice(cart.discount) }}</span>
              </div>
              <div class="flex justify-between text-gray-600">
                <span>Frete</span>
                <span v-if="cart.shippingCost === 0" class="text-[#52B788] font-medium">Grátis</span>
                <span v-else>{{ formatPrice(cart.shippingCost) }}</span>
              </div>
              <div class="flex justify-between font-bold text-[#1B1B1B] text-lg pt-2 border-t border-gray-100">
                <span>Total</span>
                <span class="text-[#2D6A4F]">{{ formatPrice(cart.total) }}</span>
              </div>
            </div>

            <!-- Submit -->
            <button
              type="submit"
              :disabled="submitting"
              class="btn-primary w-full justify-center py-4 mt-5 text-base"
            >
              <svg v-if="submitting" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
              </svg>
              <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/>
              </svg>
              {{ submitting ? 'Processando...' : 'Pagar com Mercado Pago' }}
            </button>

            <p class="text-xs text-gray-400 text-center mt-3 flex items-center justify-center gap-1">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
              </svg>
              Pagamento 100% seguro via Mercado Pago
            </p>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { useCartStore } from '../stores/cart.js'
import { useUiStore } from '../stores/ui.js'

const router    = useRouter()
const cart      = useCartStore()
const ui        = useUiStore()
const submitting = ref(false)
const couponCode = ref('')
const couponError = ref('')

const ufs = ['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO']

const form = reactive({
  customer_name:    '',
  customer_email:   '',
  customer_phone:   '',
  customer_cpf:     '',
  shipping_address: { street: '', number: '', complement: '', district: '', city: '', state: '', zip_code: '' },
  notes:            '',
})

function formatPrice(v) {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(v)
}

async function fetchCep() {
  const cep = form.shipping_address.zip_code.replace(/\D/g, '')
  if (cep.length !== 8) return
  try {
    const res = await axios.get(`https://viacep.com.br/ws/${cep}/json/`)
    if (!res.data.erro) {
      form.shipping_address.street   = res.data.logradouro
      form.shipping_address.district = res.data.bairro
      form.shipping_address.city     = res.data.localidade
      form.shipping_address.state    = res.data.uf
    }
  } catch { /* ignore */ }
}

async function applyCoupon() {
  couponError.value = ''
  if (!couponCode.value.trim()) return
  try {
    const res = await axios.post('/coupons/validate', {
      code: couponCode.value,
      subtotal: cart.subtotal,
    })
    cart.applyCoupon(res.data)
    ui.showToast('Cupom aplicado com sucesso!')
  } catch (err) {
    couponError.value = err.response?.data?.message ?? 'Cupom inválido.'
    ui.showToast(couponError.value, 'error')
  }
}

function removeCoupon() {
  cart.removeCoupon()
  couponCode.value = ''
  couponError.value = ''
}

async function submitOrder() {
  if (submitting.value) return
  submitting.value = true

  try {
    const payload = {
      ...form,
      items: cart.items.map(i => ({ product_id: i.id, quantity: i.quantity })),
      coupon_code: cart.coupon?.code ?? null,
    }

    const res = await axios.post('/orders', payload)

    cart.clearCart()

    // Redirect to Mercado Pago
    if (res.data.init_point) {
      window.location.href = res.data.init_point
    } else {
      router.push(`/pedido/${res.data.order_number}`)
    }
  } catch (err) {
    const msg = err.response?.data?.message ?? 'Erro ao processar pedido. Tente novamente.'
    ui.showToast(msg, 'error')
  } finally {
    submitting.value = false
  }
}
</script>
