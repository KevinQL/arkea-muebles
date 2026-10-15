<script setup lang="ts">
const route = useRoute()

const links = [
  { to: '/', label: 'Catálogo' },
  { to: '/#categorias', label: 'Categorías' },
  { to: '/contacto', label: 'Contacto' },
]

const isActive = (to: string) => route.path === to
</script>

<template>
  <header class="sticky top-0 z-40 border-b border-madera-800/40 bg-madera-900 text-madera-100">
    <div class="mx-auto flex h-20 max-w-7xl items-center justify-between px-4 sm:px-6">
      <NuxtLink to="/" class="flex items-center gap-3">
        <!-- Logo hexágono dorado -->
        <svg viewBox="0 0 64 64" class="h-11 w-11 shrink-0" aria-hidden="true">
          <polygon
            points="32,3 59,18 59,46 32,61 5,46 5,18"
            fill="none"
            stroke="#c9a66e"
            stroke-width="3"
          />
          <text
            x="32"
            y="41"
            text-anchor="middle"
            font-size="26"
            font-family="Georgia, serif"
            fill="#c9a66e"
          >
            A
          </text>
        </svg>
        <span class="leading-none">
          <span class="font-display block text-2xl tracking-widest text-oro-500">ARKEA</span>
          <span class="mt-1 block text-[0.7rem] tracking-[0.35em] text-madera-300">MUEBLES</span>
        </span>
      </NuxtLink>

      <nav class="hidden items-center gap-8 text-sm uppercase tracking-widest sm:flex">
        <NuxtLink
          v-for="link in links"
          :key="link.to"
          :to="link.to"
          class="transition-colors hover:text-oro-400"
          :class="isActive(link.to) ? 'text-oro-400' : 'text-madera-200'"
        >
          {{ link.label }}
        </NuxtLink>
      </nav>

      <a
        href="https://wa.me/51987654321"
        target="_blank"
        rel="noopener"
        class="rounded-full bg-madera-600 px-5 py-2.5 text-sm font-semibold transition-colors hover:bg-madera-500"
      >
        WhatsApp
      </a>
    </div>
  </header>
</template>
