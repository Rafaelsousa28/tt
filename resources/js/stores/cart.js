import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useCartStore = defineStore('cart', () => {
  const items = ref(JSON.parse(localStorage.getItem('cart') ?? '[]'))
  const coupon = ref(null)
  const isOpen = ref(false)

  const itemCount = computed(() => items.value.reduce((acc, i) => acc + i.quantity, 0))

  const subtotal = computed(() =>
    items.value.reduce((acc, i) => acc + i.price * i.quantity, 0)
  )

  const discount = computed(() => coupon.value?.discount ?? 0)

  const shippingCost = computed(() => {
    if (subtotal.value >= 200) return 0
    return subtotal.value > 0 ? 15.00 : 0
  })

  const total = computed(() =>
    Math.max(0, subtotal.value - discount.value + shippingCost.value)
  )

  function save() {
    localStorage.setItem('cart', JSON.stringify(items.value))
  }

  function addItem(product, quantity = 1) {
    const existing = items.value.find(i => i.id === product.id)
    if (existing) {
      existing.quantity = Math.min(existing.quantity + quantity, product.stock ?? 999)
    } else {
      items.value.push({
        id:       product.id,
        name:     product.name,
        slug:     product.slug,
        price:    parseFloat(product.current_price ?? product.price),
        image:    product.first_image_url ?? null,
        stock:    product.stock,
        quantity: quantity,
      })
    }
    save()
    isOpen.value = true
  }

  function removeItem(productId) {
    items.value = items.value.filter(i => i.id !== productId)
    save()
  }

  function updateQuantity(productId, quantity) {
    const item = items.value.find(i => i.id === productId)
    if (!item) return
    if (quantity <= 0) {
      removeItem(productId)
    } else {
      item.quantity = Math.min(quantity, item.stock ?? 999)
      save()
    }
  }

  function applyCoupon(couponData) {
    coupon.value = couponData
  }

  function removeCoupon() {
    coupon.value = null
  }

  function clearCart() {
    items.value = []
    coupon.value = null
    save()
  }

  function openCart() { isOpen.value = true }
  function closeCart() { isOpen.value = false }
  function toggleCart() { isOpen.value = !isOpen.value }

  return {
    items, coupon, isOpen,
    itemCount, subtotal, discount, shippingCost, total,
    addItem, removeItem, updateQuantity,
    applyCoupon, removeCoupon, clearCart,
    openCart, closeCart, toggleCart,
  }
})
