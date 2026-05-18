<template>
  <div class="min-h-screen bg-[#F8F4F0] flex items-center justify-center pt-20">
    <div class="text-center px-4">
      <div class="text-8xl mb-6">🌱</div>
      <h1 class="text-6xl font-bold text-[#2D6A4F] mb-2" style="font-family: 'Playfair Display', serif">404</h1>
      <h2 class="text-2xl font-semibold text-gray-700 mb-4">Página não encontrada</h2>
      <p class="text-gray-500 mb-8">Parece que esta página não existe. Que tal explorar nossas plantas?</p>
      <div class="flex gap-3 justify-center">
        <RouterLink to="/" class="btn-primary">Ir para o Início</RouterLink>
        <RouterLink to="/plantas" class="btn-secondary">Ver Plantas</RouterLink>
      </div>
    </div>
  </div>
</template>
