<script setup lang="ts">
import type { Mueble } from '~/composables/useApi'

definePageMeta({ middleware: 'admin', layout: 'admin' })
useHead({ title: 'Editar mueble | Arkea Admin' })

const route = useRoute()
const token = useCookie('arkea_token')
const apiBase = useRuntimeConfig().public.apiBase

const { data: mueble, error } = await useFetch<Mueble>(
  `${apiBase}/admin/muebles/${route.params.id}`,
  { headers: { Authorization: `Bearer ${token.value}` } },
)
</script>

<template>
  <div>
    <div
      v-if="error"
      class="rounded-xl border border-red-200 bg-red-50 px-5 py-6 text-sm text-red-600"
    >
      No se pudo cargar el mueble.
      <NuxtLink to="/admin/muebles" class="underline">Volver al listado</NuxtLink>
    </div>

    <AdminMuebleForm v-else-if="mueble" :mueble="mueble" />
  </div>
</template>
