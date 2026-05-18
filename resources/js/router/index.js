import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  {
    path: '/',
    name: 'home',
    component: () => import('../pages/Home.vue'),
  },
  {
    path: '/plantas',
    name: 'products',
    component: () => import('../pages/Products.vue'),
  },
  {
    path: '/plantas/:slug',
    name: 'product-detail',
    component: () => import('../pages/ProductDetail.vue'),
  },
  {
    path: '/categorias/:slug',
    name: 'category',
    component: () => import('../pages/Products.vue'),
  },
  {
    path: '/carrinho',
    name: 'cart',
    component: () => import('../pages/Cart.vue'),
  },
  {
    path: '/checkout',
    name: 'checkout',
    component: () => import('../pages/Checkout.vue'),
  },
  {
    path: '/pedido/:number',
    name: 'order-success',
    component: () => import('../pages/OrderSuccess.vue'),
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: () => import('../pages/NotFound.vue'),
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) return savedPosition
    return { top: 0, behavior: 'smooth' }
  },
})

export default router
