<script setup lang="ts">
import { formatearPrecio, waLink, useImgUrl } from '~/composables/useApi'

const route = useRoute()
const slug = route.params.slug as string

const { fetchMueble, fetchWhatsapp } = useApi()
const { data: mueble, error } = await fetchMueble(slug)
const { data: configs } = await fetchWhatsapp()

useHead(() => ({
  title: mueble.value ? `${mueble.value.nombre} | Arkea Muebles` : 'Arkea Muebles',
}))

const fotoActual = ref(0)
const img = useImgUrl()

const galeria = computed(() => {
  if (!mueble.value) return []
  // Prioridad: imagen subida al servidor > path manual > galería adicional
  const crudas = [
    mueble.value.galeria_subida,
    mueble.value.galeria_principal,
    ...(mueble.value.galeria ?? []),
  ].filter((f): f is string => Boolean(f))
  return [...new Set(crudas)].map((f) => img(f)).filter((f): f is string => Boolean(f))
})

const waConfig = computed(() => {
  if (!mueble.value || !configs.value) return null
  const id = mueble.value.informacion_adicional?.consultas_whatsapp_id
  return configs.value.find((c) => c.id === id) ?? configs.value.find((c) => c.tipo === 'general')
})

const waHref = computed(() => {
  if (!waConfig.value || !mueble.value) return '#'
  const mensaje = (waConfig.value.mensaje_default ?? 'Hola, me interesa: [NOMBRE]').replace(
    /\[NOMBRE\]/g,
    `${mueble.value.nombre} (${formatearPrecio(mueble.value.precio)})`,
  )
  return waLink(waConfig.value.numero_telefono, mensaje)
})

const etiquetaEstado: Record<string, string> = {
  nuevo: 'NUEVO',
  renovado: 'RENOVADO',
  usado: 'USADO',
  agendado: 'AGENDADO',
}
</script>

<template>
  <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6">
    <!-- 404 -->
    <div
      v-if="error"
      class="rounded-xl border border-dashed border-madera-300 bg-white py-20 text-center"
    >
      <h1 class="font-display text-2xl text-madera-800">Mueble no encontrado</h1>
      <NuxtLink to="/" class="mt-4 inline-block text-madera-600 underline">
        Volver al catálogo
      </NuxtLink>
    </div>

    <template v-else-if="mueble">
      <!-- Breadcrumb -->
      <nav class="mb-6 text-sm text-neutral-500">
        <NuxtLink to="/" class="hover:text-madera-600">Catálogo</NuxtLink>
        <span class="mx-2">/</span>
        <span>{{ mueble.categoria?.nombre }}</span>
        <span class="mx-2">/</span>
        <span class="text-madera-700">{{ mueble.nombre }}</span>
      </nav>

      <div class="grid gap-10 lg:grid-cols-2">
        <!-- GALERÍA -->
        <div>
          <div
            class="aspect-[4/3] overflow-hidden rounded-2xl border border-madera-200 bg-white"
          >
            <NuxtImg
              v-if="galeria[fotoActual]"
              :src="galeria[fotoActual]"
              :alt="mueble.nombre"
              width="800"
              height="600"
              class="h-full w-full object-contain"
            />
            <div v-else class="flex h-full items-center justify-center text-madera-300">
              <svg viewBox="0 0 64 64" class="h-24 w-24" aria-hidden="true">
                <polygon
                  points="32,3 59,18 59,46 32,61 5,46 5,18"
                  fill="none"
                  stroke="#bfa285"
                  stroke-width="3"
                />
                <text x="32" y="41" text-anchor="middle" font-size="26" fill="#bfa285" font-family="Georgia, serif">A</text>
              </svg>
            </div>
          </div>

          <!-- Thumbnails -->
          <div v-if="galeria.length > 1" class="mt-4 flex gap-3">
            <button
              v-for="(foto, i) in galeria"
              :key="i"
              class="h-20 w-20 overflow-hidden rounded-lg border-2 transition"
              :class="fotoActual === i ? 'border-madera-600' : 'border-transparent opacity-70 hover:opacity-100'"
              @click="fotoActual = i"
            >
              <NuxtImg :src="foto" :alt="`${mueble.nombre} ${i + 1}`" width="100" height="100" class="h-full w-full object-cover" />
            </button>
          </div>
        </div>

        <!-- DETALLES -->
        <div>
          <div class="flex items-center gap-3">
            <span class="rounded-full bg-madera-600 px-3 py-1 text-xs font-bold tracking-wider text-white">
              {{ etiquetaEstado[mueble.estado] ?? mueble.estado }}
            </span>
            <span class="text-sm uppercase tracking-widest text-madera-500">
              {{ mueble.categoria?.nombre }}
            </span>
          </div>

          <h1 class="font-display mt-3 text-3xl text-madera-800 sm:text-4xl">
            {{ mueble.nombre }}
          </h1>

          <p class="mt-4 text-lg leading-relaxed text-neutral-600">
            {{ mueble.descripcion_completa ?? mueble.descripcion_corta }}
          </p>

          <p class="mt-6 text-3xl font-bold text-madera-700">
            {{ formatearPrecio(mueble.precio) }}
          </p>

          <!-- Acciones -->
          <div class="mt-6 flex flex-wrap gap-3">
            <a
              :href="waHref"
              target="_blank"
              rel="noopener"
              class="flex items-center gap-2 rounded-full bg-emerald-600 px-7 py-3 font-semibold text-white transition hover:bg-emerald-500"
            >
              <svg viewBox="0 0 32 32" class="h-5 w-5 fill-current" aria-hidden="true">
                <path
                  d="M16 3C9.4 3 4 8.4 4 15c0 2.1.6 4.2 1.6 6L4 29l8.2-1.5c1.7.9 3.7 1.4 5.7 1.4h.1c6.6 0 12-5.4 12-12S22.6 3 16 3zm0 21.9c-1.8 0-3.5-.5-5-1.4l-.4-.2-4.9.9.9-4.7-.2-.4c-1-1.6-1.5-3.4-1.5-5.3 0-5.4 4.4-9.9 9.9-9.9s9.9 4.4 9.9 9.9-4.4 10.1-8.7 10.1z"
                />
              </svg>
              Consultar por WhatsApp
            </a>
            <NuxtLink
              to="/contacto"
              class="rounded-full border border-madera-600 px-7 py-3 font-semibold text-madera-700 transition hover:bg-madera-600 hover:text-white"
            >
              Solicitar presupuesto
            </NuxtLink>
          </div>

          <!-- Especificaciones -->
          <div class="mt-8 grid gap-4 sm:grid-cols-2">
            <div
              v-for="(valor, clave) in mueble.medidas"
              :key="'m-' + clave"
              class="rounded-xl border border-madera-200 bg-white p-4"
            >
              <span class="block text-xs uppercase tracking-wider text-madera-500">
                {{ clave.replaceAll('_', ' ') }}
              </span>
              <span class="mt-1 block font-semibold text-madera-800">{{ valor }}</span>
            </div>
          </div>

          <!-- Materiales -->
          <div v-if="mueble.materiales" class="mt-6">
            <h2 class="font-display text-xl text-madera-800">Materiales</h2>
            <ul class="mt-2 space-y-1 text-sm text-neutral-600">
              <li v-for="(valor, clave) in mueble.materiales" :key="'mat-' + clave">
                <strong class="capitalize text-madera-700">{{ clave.replaceAll('_', ' ') }}:</strong>
                {{ Array.isArray(valor) ? valor.join(', ') : valor }}
              </li>
            </ul>
          </div>

          <!-- Especificaciones extra -->
          <div v-if="mueble.especificaciones" class="mt-6">
            <h2 class="font-display text-xl text-madera-800">Características</h2>
            <ul class="mt-2 space-y-1 text-sm text-neutral-600">
              <li v-for="(valor, clave) in mueble.especificaciones" :key="'esp-' + clave">
                <strong class="capitalize text-madera-700">{{ clave.replaceAll('_', ' ') }}:</strong>
                {{ typeof valor === 'boolean' ? (valor ? 'Sí' : 'No') : valor }}
              </li>
            </ul>
          </div>

          <!-- Info adicional -->
          <div class="mt-6 flex flex-wrap gap-4 text-sm">
            <span
              v-if="mueble.informacion_adicional?.garantia_anios"
              class="rounded-lg bg-madera-100 px-3 py-2 text-madera-700"
            >
              🛡️ Garantía {{ mueble.informacion_adicional.garantia_anios }} año(s)
            </span>
            <span
              v-if="mueble.informacion_adicional?.envio_disponible"
              class="rounded-lg bg-madera-100 px-3 py-2 text-madera-700"
            >
              📦 Envío disponible
            </span>
            <span class="rounded-lg bg-madera-100 px-3 py-2 text-madera-700">
              📍 {{ mueble.ubicacion }}
            </span>
          </div>

          <!-- Colores -->
          <div
            v-if="mueble.informacion_adicional?.color_disponible?.length"
            class="mt-6"
          >
            <h2 class="font-display text-xl text-madera-800">Colores disponibles</h2>
            <div class="mt-2 flex flex-wrap gap-2">
              <span
                v-for="color in mueble.informacion_adicional.color_disponible"
                :key="color"
                class="rounded-full border border-madera-300 bg-white px-4 py-1.5 text-sm text-madera-700"
              >
                {{ color }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>
