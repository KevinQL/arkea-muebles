<script setup lang="ts">
import { refDebounced } from '@vueuse/core'

const { fetchMuebles, fetchCategorias } = useApi()

const busqueda = ref('')
const busquedaDebounced = refDebounced(busqueda, 400)
const categoria = ref('')
const precioMin = ref('')
const precioMax = ref('')
const pagina = ref(1)

const params = computed(() => {
  const p: Record<string, any> = { page: pagina.value, per_page: 12 }
  if (busquedaDebounced.value) p.buscador = busquedaDebounced.value
  if (categoria.value) p.categoria = categoria.value
  if (precioMin.value) p.precio_min = precioMin.value
  if (precioMax.value) p.precio_max = precioMax.value
  return p
})

const { data: muebles, status } = await useFetch(
  () => `${useRuntimeConfig().public.apiBase}/muebles`,
  { query: params, watch: [params] },
)

const { data: categorias } = await fetchCategorias()

watch([busquedaDebounced, categoria, precioMin, precioMax], () => {
  pagina.value = 1
})

const aplicarCategoria = (nombre: string) => {
  categoria.value = categoria.value === nombre ? '' : nombre
}

const limpiarFiltros = () => {
  busqueda.value = ''
  categoria.value = ''
  precioMin.value = ''
  precioMax.value = ''
  pagina.value = 1
}
</script>

<template>
  <div>
    <!-- HERO -->
    <section class="bg-madera-900 text-madera-100">
      <div class="mx-auto flex max-w-7xl flex-col items-center gap-6 px-4 py-20 text-center sm:px-6 lg:py-28">
        <span class="text-xs uppercase tracking-[0.4em] text-oro-500">Andahuaylas - Apurímac</span>
        <h1 class="font-display max-w-3xl text-4xl leading-tight sm:text-5xl lg:text-6xl">
          Muebles con <span class="text-oro-500">estilo</span> y calidad para tu hogar
        </h1>
        <p class="max-w-2xl text-lg text-madera-300">
          Descubre nuestro catálogo de muebles para sala, comedor, dormitorio y oficina.
        </p>
        <a
          href="#catalogo"
          class="rounded-full bg-oro-500 px-8 py-3 font-semibold text-madera-900 transition-colors hover:bg-oro-400"
        >
          Ver catálogo
        </a>
      </div>
    </section>

    <!-- CATÁLOGO -->
    <section id="catalogo" class="mx-auto max-w-7xl px-4 py-14 sm:px-6">
      <div class="mb-8 flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
        <div>
          <h2 class="font-display text-3xl text-madera-800">Catálogo de muebles</h2>
          <p class="mt-1 text-sm text-neutral-500">
            {{ muebles?.total ?? 0 }} mueble(s) disponibles
          </p>
        </div>

        <!-- Buscador -->
        <div class="w-full lg:max-w-md">
          <input
            v-model="busqueda"
            type="search"
            placeholder="Buscar mueble..."
            class="w-full rounded-full border border-madera-300 bg-white px-5 py-3 text-sm outline-none transition focus:border-madera-600 focus:ring-2 focus:ring-madera-600/20"
          />
        </div>
      </div>

      <!-- Categorías + filtros precio -->
      <div class="mb-8 flex flex-col gap-4">
        <div id="categorias" class="flex flex-wrap gap-2">
          <button
            class="rounded-full border px-4 py-2 text-sm transition"
            :class="
              categoria === ''
                ? 'border-madera-600 bg-madera-600 text-white'
                : 'border-madera-300 bg-white text-madera-700 hover:border-madera-600'
            "
            @click="aplicarCategoria('')"
          >
            Todas
          </button>
          <button
            v-for="cat in categorias"
            :key="cat.id"
            class="rounded-full border px-4 py-2 text-sm transition"
            :class="
              categoria === cat.nombre
                ? 'border-madera-600 bg-madera-600 text-white'
                : 'border-madera-300 bg-white text-madera-700 hover:border-madera-600'
            "
            @click="aplicarCategoria(cat.nombre)"
          >
            {{ cat.nombre }}
            <span class="ml-1 text-xs opacity-70">({{ cat.total_muebles }})</span>
          </button>
        </div>

        <div class="flex flex-wrap items-center gap-3 text-sm">
          <span class="text-neutral-500">Precio:</span>
          <input
            v-model="precioMin"
            type="number"
            min="0"
            placeholder="Mín S/"
            class="w-28 rounded-lg border border-madera-300 bg-white px-3 py-2 outline-none focus:border-madera-600"
          />
          <span class="text-neutral-400">—</span>
          <input
            v-model="precioMax"
            type="number"
            min="0"
            placeholder="Máx S/"
            class="w-28 rounded-lg border border-madera-300 bg-white px-3 py-2 outline-none focus:border-madera-600"
          />
          <button
            v-if="busqueda || categoria || precioMin || precioMax"
            class="text-madera-600 underline-offset-2 hover:underline"
            @click="limpiarFiltros"
          >
            Limpiar filtros
          </button>
        </div>
      </div>

      <!-- Grid -->
      <div v-if="status === 'pending'" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <div
          v-for="n in 6"
          :key="n"
          class="h-80 animate-pulse rounded-xl bg-madera-200"
        />
      </div>

      <div
        v-else-if="muebles?.data.length"
        class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3"
      >
        <ProductCard v-for="mueble in muebles.data" :key="mueble.id" :mueble="mueble" />
      </div>

      <div
        v-else
        class="rounded-xl border border-dashed border-madera-300 bg-white py-16 text-center"
      >
        <p class="text-lg text-neutral-500">No se encontraron muebles con esos filtros.</p>
        <button class="mt-3 text-madera-600 underline" @click="limpiarFiltros">
          Limpiar filtros
        </button>
      </div>

      <!-- Paginación -->
      <div
        v-if="muebles && muebles.last_page > 1"
        class="mt-10 flex items-center justify-center gap-3"
      >
        <button
          class="rounded-lg border border-madera-300 bg-white px-4 py-2 text-sm disabled:opacity-40"
          :disabled="pagina <= 1"
          @click="pagina--"
        >
          ← Anterior
        </button>
        <span class="text-sm text-neutral-600">
          Página {{ muebles.current_page }} de {{ muebles.last_page }}
        </span>
        <button
          class="rounded-lg border border-madera-300 bg-white px-4 py-2 text-sm disabled:opacity-40"
          :disabled="pagina >= muebles.last_page"
          @click="pagina++"
        >
          Siguiente →
        </button>
      </div>
    </section>
  </div>
</template>
