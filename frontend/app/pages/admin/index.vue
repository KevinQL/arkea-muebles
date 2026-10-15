<script setup lang="ts">
import { formatearPrecio } from '~/composables/useApi'

definePageMeta({ middleware: 'admin', layout: 'admin' })
useHead({ title: 'Dashboard | Arkea Admin' })

const { $admin } = useAuth()

const { data: muebles } = await useFetch<any>(
  `${useRuntimeConfig().public.apiBase}/admin/muebles`,
  {
    query: { per_page: 100 },
    headers: { Authorization: `Bearer ${useCookie('arkea_token').value}` },
  },
)

const { data: categorias } = await useFetch<any[]>(
  `${useRuntimeConfig().public.apiBase}/categorias`,
)

const stats = computed(() => {
  const lista = muebles.value?.data ?? []
  const porPrecio = [...lista].sort((a, b) => Number(b.precio) - Number(a.precio))
  return {
    total: muebles.value?.total ?? 0,
    categorias: categorias.value?.length ?? 0,
    nuevo: lista.filter((m: any) => m.estado === 'nuevo').length,
    masCaro: porPrecio[0],
    masBarato: porPrecio[porPrecio.length - 1],
    valorCatalogo: lista.reduce((acc: number, m: any) => acc + Number(m.precio), 0),
  }
})

const recientes = computed(() => (muebles.value?.data ?? []).slice(0, 5))
</script>

<template>
  <div>
    <h1 class="font-display text-2xl text-madera-800">Dashboard</h1>
    <p class="mt-1 text-sm text-neutral-500">Resumen del catálogo Arkea Muebles</p>

    <!-- Stats -->
    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
      <div class="rounded-xl border border-madera-200 bg-white p-5">
        <span class="text-xs uppercase tracking-wider text-madera-500">Muebles</span>
        <p class="mt-1 text-3xl font-bold text-madera-700">{{ stats.total }}</p>
      </div>
      <div class="rounded-xl border border-madera-200 bg-white p-5">
        <span class="text-xs uppercase tracking-wider text-madera-500">Categorías</span>
        <p class="mt-1 text-3xl font-bold text-madera-700">{{ stats.categorias }}</p>
      </div>
      <div class="rounded-xl border border-madera-200 bg-white p-5">
        <span class="text-xs uppercase tracking-wider text-madera-500">Nuevos</span>
        <p class="mt-1 text-3xl font-bold text-emerald-600">{{ stats.nuevo }}</p>
      </div>
      <div class="rounded-xl border border-madera-200 bg-white p-5">
        <span class="text-xs uppercase tracking-wider text-madera-500">Valor catálogo</span>
        <p class="mt-1 text-3xl font-bold text-oro-600">
          {{ formatearPrecio(stats.valorCatalogo) }}
        </p>
      </div>
    </div>

    <!-- Más caro / más barato -->
    <div class="mt-6 grid gap-4 sm:grid-cols-2">
      <div v-if="stats.masCaro" class="rounded-xl border border-madera-200 bg-white p-5">
        <span class="text-xs uppercase tracking-wider text-madera-500">Más caro</span>
        <p class="mt-1 font-semibold text-madera-800">{{ stats.masCaro.nombre }}</p>
        <p class="text-lg font-bold text-madera-700">
          {{ formatearPrecio(stats.masCaro.precio) }}
        </p>
      </div>
      <div v-if="stats.masBarato" class="rounded-xl border border-madera-200 bg-white p-5">
        <span class="text-xs uppercase tracking-wider text-madera-500">Más económico</span>
        <p class="mt-1 font-semibold text-madera-800">{{ stats.masBarato.nombre }}</p>
        <p class="text-lg font-bold text-madera-700">
          {{ formatearPrecio(stats.masBarato.precio) }}
        </p>
      </div>
    </div>

    <!-- Recientes -->
    <div class="mt-8 rounded-xl border border-madera-200 bg-white">
      <div class="flex items-center justify-between border-b border-madera-100 px-5 py-4">
        <h2 class="font-semibold text-madera-800">Últimos muebles</h2>
        <NuxtLink to="/admin/muebles" class="text-sm text-madera-600 hover:underline">
          Ver todos →
        </NuxtLink>
      </div>
      <ul>
        <li
          v-for="mueble in recientes"
          :key="mueble.id"
          class="flex items-center justify-between border-b border-madera-100 px-5 py-3 last:border-0"
        >
          <div>
            <p class="font-medium text-neutral-800">{{ mueble.nombre }}</p>
            <p class="text-xs text-neutral-400">{{ mueble.categoria_id ? 'cat. ' + mueble.categoria_id : 'sin categoría' }} · {{ mueble.estado }}</p>
          </div>
          <span class="font-semibold text-madera-700">{{ formatearPrecio(mueble.precio) }}</span>
        </li>
      </ul>
    </div>
  </div>
</template>
