<script setup lang="ts">
import { formatearPrecio } from '~/composables/useApi'

definePageMeta({ middleware: 'admin', layout: 'admin' })
useHead({ title: 'Muebles | Arkea Admin' })

const token = useCookie('arkea_token')
const apiBase = useRuntimeConfig().public.apiBase

const pagina = ref(1)
const { data, refresh } = await useFetch<any>(`${apiBase}/admin/muebles`, {
  query: { page: pagina, per_page: 20 },
  headers: { Authorization: `Bearer ${token.value}` },
  watch: [pagina],
})

const eliminando = ref<number | null>(null)

const eliminar = async (mueble: any) => {
  if (!confirm(`¿Eliminar "${mueble.nombre}"? Esta acción no se puede deshacer.`)) return
  eliminando.value = mueble.id
  try {
    await $fetch(`${apiBase}/admin/muebles/${mueble.id}`, {
      method: 'DELETE',
      headers: { Authorization: `Bearer ${token.value}` },
    })
    await refresh()
  } finally {
    eliminando.value = null
  }
}

const etiquetaEstado: Record<string, string> = {
  nuevo: 'bg-emerald-100 text-emerald-700',
  renovado: 'bg-amber-100 text-amber-700',
  usado: 'bg-neutral-100 text-neutral-600',
  agendado: 'bg-orange-100 text-orange-700',
}
</script>

<template>
  <div>
    <div class="flex items-center justify-between">
      <div>
        <h1 class="font-display text-2xl text-madera-800">Muebles</h1>
        <p class="mt-1 text-sm text-neutral-500">{{ data?.total ?? 0 }} en total</p>
      </div>
      <NuxtLink
        to="/admin/muebles/nuevo"
        class="rounded-full bg-madera-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-madera-500"
      >
        + Nuevo mueble
      </NuxtLink>
    </div>

    <div class="mt-6 overflow-x-auto rounded-xl border border-madera-200 bg-white">
      <table class="w-full text-left text-sm">
        <thead class="border-b border-madera-100 bg-madera-50 text-xs uppercase tracking-wider text-madera-500">
          <tr>
            <th class="px-5 py-3">Mueble</th>
            <th class="px-5 py-3">Precio</th>
            <th class="px-5 py-3">Estado</th>
            <th class="px-5 py-3">Activo</th>
            <th class="px-5 py-3 text-right">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="mueble in data?.data ?? []"
            :key="mueble.id"
            class="border-b border-madera-100 last:border-0 hover:bg-madera-50/50"
          >
            <td class="px-5 py-3">
              <p class="font-medium text-neutral-800">{{ mueble.nombre }}</p>
              <p class="text-xs text-neutral-400">/{{ mueble.slug }}</p>
            </td>
            <td class="px-5 py-3 font-semibold text-madera-700">
              {{ formatearPrecio(mueble.precio) }}
            </td>
            <td class="px-5 py-3">
              <span
                class="rounded-full px-2.5 py-1 text-xs font-semibold capitalize"
                :class="etiquetaEstado[mueble.estado]"
              >
                {{ mueble.estado }}
              </span>
            </td>
            <td class="px-5 py-3">
              <span :class="mueble.is_active ? 'text-emerald-600' : 'text-neutral-400'">
                {{ mueble.is_active ? 'Sí' : 'No' }}
              </span>
            </td>
            <td class="px-5 py-3 text-right">
              <NuxtLink
                :to="`/admin/muebles/${mueble.id}`"
                class="mr-3 text-sm font-medium text-madera-600 hover:underline"
              >
                Editar
              </NuxtLink>
              <button
                class="text-sm font-medium text-red-500 hover:underline disabled:opacity-50"
                :disabled="eliminando === mueble.id"
                @click="eliminar(mueble)"
              >
                {{ eliminando === mueble.id ? '...' : 'Eliminar' }}
              </button>
            </td>
          </tr>
          <tr v-if="data && !data.data.length">
            <td colspan="5" class="px-5 py-10 text-center text-neutral-400">
              No hay muebles todavía.
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Paginación -->
    <div
      v-if="data && data.last_page > 1"
      class="mt-6 flex items-center justify-center gap-3 text-sm"
    >
      <button
        class="rounded-lg border border-madera-300 bg-white px-4 py-2 disabled:opacity-40"
        :disabled="pagina <= 1"
        @click="pagina--"
      >
        ←
      </button>
      <span>Página {{ data.current_page }} de {{ data.last_page }}</span>
      <button
        class="rounded-lg border border-madera-300 bg-white px-4 py-2 disabled:opacity-40"
        :disabled="pagina >= data.last_page"
        @click="pagina++"
      >
        →
      </button>
    </div>
  </div>
</template>
