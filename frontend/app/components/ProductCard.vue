<script setup lang="ts">
import type { Mueble } from '~/composables/useApi'
import { formatearPrecio, useImgUrl, imagenEfectiva } from '~/composables/useApi'

const props = defineProps<{ mueble: Mueble }>()

const img = useImgUrl()
const foto = computed(() => img(imagenEfectiva(props.mueble)))

const etiquetaEstado: Record<string, { texto: string; clase: string }> = {
  nuevo: { texto: 'NUEVO', clase: 'bg-madera-600 text-white' },
  renovado: { texto: 'RENOVADO', clase: 'bg-oro-500 text-madera-900' },
  usado: { texto: 'USADO', clase: 'bg-neutral-500 text-white' },
  agendado: { texto: 'AGENDADO', clase: 'bg-amber-700 text-white' },
}
</script>

<template>
  <NuxtLink
    :to="`/detalle/${mueble.slug}`"
    class="group overflow-hidden rounded-xl border border-madera-200 bg-white shadow-sm transition-all hover:-translate-y-1 hover:shadow-lg"
  >
    <!-- Imagen / placeholder -->
    <div class="relative aspect-[4/3] overflow-hidden bg-madera-100">
      <NuxtImg
        v-if="foto"
        :src="foto"
        :alt="mueble.nombre"
        loading="lazy"
        width="400"
        height="300"
        class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
      />
      <div v-else class="flex h-full w-full items-center justify-center">
        <svg viewBox="0 0 64 64" class="h-16 w-16 opacity-30" aria-hidden="true">
          <polygon
            points="32,3 59,18 59,46 32,61 5,46 5,18"
            fill="none"
            stroke="#8b5a2b"
            stroke-width="3"
          />
          <text x="32" y="41" text-anchor="middle" font-size="26" fill="#8b5a2b" font-family="Georgia, serif">A</text>
        </svg>
      </div>

      <span
        v-if="etiquetaEstado[mueble.estado]"
        class="absolute left-3 top-3 rounded-full px-2.5 py-1 text-[0.65rem] font-bold tracking-wider"
        :class="etiquetaEstado[mueble.estado].clase"
      >
        {{ etiquetaEstado[mueble.estado].texto }}
      </span>
    </div>

    <!-- Info -->
    <div class="p-4">
      <span class="text-xs uppercase tracking-widest text-madera-500">
        {{ mueble.categoria?.nombre ?? 'Mueble' }}
      </span>
      <h3 class="mt-1 truncate text-lg font-semibold text-neutral-800 group-hover:text-madera-600">
        {{ mueble.nombre }}
      </h3>
      <p class="mt-1 line-clamp-2 min-h-10 text-sm text-neutral-500">
        {{ mueble.descripcion_corta }}
      </p>

      <div class="mt-3 flex items-center justify-between">
        <span class="text-lg font-bold text-madera-700">{{ formatearPrecio(mueble.precio) }}</span>
        <span
          v-if="mueble.informacion_adicional?.envio_disponible"
          class="text-xs text-emerald-600"
        >
          📦 Envío
        </span>
      </div>
    </div>
  </NuxtLink>
</template>
