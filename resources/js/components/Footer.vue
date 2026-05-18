<template>
  <footer class="bg-[#1B4332] text-white mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
        <!-- Brand -->
        <div class="md:col-span-1">
          <!-- Logo Pure Garden no footer -->
          <div class="flex items-center gap-3 mb-5">
            <svg class="h-10 w-auto text-[#52B788]" viewBox="0 0 44 52" fill="currentColor">
              <path d="M10 30 L13 48 H31 L34 30 Z" opacity="0.9"/>
              <rect x="20.5" y="12" width="3" height="20" rx="1.5"/>
              <path d="M21 22 C18 18 12 18 12 14 C16 14 20 17 21 22Z" opacity="0.85"/>
              <path d="M23 18 C26 14 32 13 32 9 C28 10 23 13 23 18Z" opacity="0.85"/>
              <path d="M22 12 C21 9 19 7 20 4 C22 6 23 9 22 12Z"/>
            </svg>
            <div class="leading-none">
              <div class="font-black uppercase tracking-widest text-lg text-white" style="letter-spacing: 0.12em;">
                PURE <span class="text-[#52B788]">GARDEN</span>
              </div>
              <div class="text-[9px] tracking-[0.25em] uppercase text-[#A7F3D0]/70 font-medium mt-0.5">Jardins e Decorações</div>
            </div>
          </div>
          <p class="text-[#A7F3D0]/80 text-sm leading-relaxed">
            Jardins, mudas e decorações selecionadas com carinho para transformar seu espaço.
          </p>
          <div class="flex gap-4 mt-6">
            <a href="#" class="w-9 h-9 bg-white/10 rounded-full flex items-center justify-center hover:bg-white/20 transition-colors">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
            </a>
            <a href="#" class="w-9 h-9 bg-white/10 rounded-full flex items-center justify-center hover:bg-white/20 transition-colors">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
            </a>
          </div>
        </div>

        <!-- Links -->
        <div>
          <h4 class="font-semibold text-sm tracking-wider uppercase text-[#52B788] mb-5">Loja</h4>
          <ul class="space-y-3">
            <li v-for="link in shopLinks" :key="link.to">
              <RouterLink :to="link.to" class="text-[#D8F3DC]/70 text-sm hover:text-white transition-colors">{{ link.label }}</RouterLink>
            </li>
          </ul>
        </div>

        <div>
          <h4 class="font-semibold text-sm tracking-wider uppercase text-[#52B788] mb-5">Ajuda</h4>
          <ul class="space-y-3">
            <li v-for="link in helpLinks" :key="link.label">
              <a :href="link.href" class="text-[#D8F3DC]/70 text-sm hover:text-white transition-colors">{{ link.label }}</a>
            </li>
          </ul>
        </div>

        <div>
          <h4 class="font-semibold text-sm tracking-wider uppercase text-[#52B788] mb-5">Contato</h4>
          <ul class="space-y-3 text-[#D8F3DC]/70 text-sm">
            <li class="flex items-start gap-2">
              <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-[#52B788]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
              <span>Av. Chermont Alves de Oliveira, 1911<br/>Alto Brilhante — Tauá, CE</span>
            </li>
            <li class="flex items-start gap-2">
              <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-[#52B788]" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.127.558 4.126 1.532 5.859L.054 23.077a.75.75 0 0 0 .924.924l5.217-1.478A11.952 11.952 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.907 0-3.691-.528-5.215-1.446l-.371-.228-3.898 1.104 1.104-3.898-.228-.371A9.956 9.956 0 0 1 2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
              <a href="https://wa.me/5588999042983" target="_blank" rel="noopener" class="hover:text-white transition-colors">
                (88) 99904-2983
              </a>
            </li>
            <li class="flex items-start gap-2">
              <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-[#52B788]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
              contato@puregarden.com.br
            </li>
            <li class="flex items-start gap-2">
              <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-[#52B788]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
              Seg–Sex: 8h–18h
            </li>
          </ul>
        </div>
      </div>
    </div>

    <div class="border-t border-white/10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <p class="text-[#A7F3D0]/60 text-xs">© {{ new Date().getFullYear() }} Pure Garden. Todos os direitos reservados.</p>
        <div class="flex items-center gap-4">
          <img src="https://logospng.org/download/mercado-pago/logo-mercado-pago-icone-1024.png" alt="Mercado Pago" class="h-6 opacity-60" />
          <span class="text-[#A7F3D0]/40 text-xs">Pagamento seguro</span>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
const shopLinks = [
  { to: '/plantas',                            label: 'Todos os Produtos' },
  { to: '/plantas?categoria=mudas-citricas',   label: 'Mudas Cítricas' },
  { to: '/plantas?categoria=ornamentais',      label: 'Ornamentais' },
  { to: '/plantas?featured=true',              label: 'Destaques' },
]
const helpLinks = [
  { href: '#', label: 'Como Comprar' },
  { href: '#', label: 'Prazo de Entrega' },
  { href: '#', label: 'Trocas e Devoluções' },
  { href: '#', label: 'Cuidados com as Plantas' },
]
</script>
