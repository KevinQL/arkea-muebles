<script setup lang="ts">
import type { Categoria } from '~/composables/useApi'

definePageMeta({ middleware: 'admin', layout: 'admin' })
useHead({ title: 'Categorías | Arkea Admin' })

const token = useCookie('arkea_token')
const apiBase = useRuntimeConfig().public.apiBase
const headers = { Authorization: `Bearer ${token.value}` }

const { data: categorias, refresh } = await useFetch<Categoria[]>(
  `${apiBase}/admin/categorias`,
  { headers },
)

const form = reactive({ id: null as number | null, nombre: '', descripcion: '', icono: '' })
const guardando = ref(false)
const error = ref('')

const nueva = () => {
  form.id = null
  form.nombre = ''
  form.descripcion = ''
  form.icono = ''
}

const editar = (cat: Categoria) => {
  form.id = cat.id
  form.nombre = cat.nombre
  form.descripcion = cat.descripcion ?? ''
  form.icono = cat.icono ?? ''
}

const guardar = async () => {
  error.value = ''
  guardando.value = true
  try {
    const body = {
      nombre: form.nombre,
      tipo: 'plana',
      descripcion: form.descripcion || null,
      icono: form.icono || null,
    }
    if (form.id) {
      await $fetch(`${apiBase}/admin/categorias/${form.id}`, { method: 'PUT', headers, body })
    } else {
      await $fetch(`${apiBase}/admin/categorias`, { method: 'POST', headers, body })
    }
    nueva()
    await refresh()
  } catch (e: any) {
    error.value = Object.values(e?.data?.errors ?? {}).flat().join(' · ') || 'Error al guardar'
  } finally {
    guardando.value = false
  }
}

const desactivar = async (cat: Categoria) => {
  if (!confirm(`¿Desactivar "${cat.nombre}"?`)) return
  await $fetch(`${apiBase}/admin/categorias/${cat.id}`, { method: 'DELETE', headers })
  await refresh()
}
</script>

<template>
  <div class="max-w-3xl">
    <h1 class="font-display text-2xl text-madera-800">Categorías</h1>
    <p class="mt-1 text-sm text-neutral-500">Organiza los muebles del catálogo</p>

    <!-- Formulario -->
    <form class="mt-6 rounded-xl border border-madera-200 bg-white p-5" @submit.prevent="guardar">
      <h2 class="font-semibold text-madera-700">
        {{ form.id ? 'Editar categoría' : 'Nueva categoría' }}
      </h2>

      <div class="mt-4 grid gap-4 sm:grid-cols-2">
        <div>
          <label class="mb-1 block text-sm font-medium">Nombre *</label>
          <input
            v-model="form.nombre"
            required
            type="text"
            class="w-full rounded-lg border border-madera-300 px-4 py-2.5 outline-none focus:border-madera-600"
            placeholder="salon, comedor, dormitorio..."
          />
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium">Icono (nombre archivo)</label>
          <input
            v-model="form.icono"
            type="text"
            class="w-full rounded-lg border border-madera-300 px-4 py-2.5 outline-none focus:border-madera-600"
            placeholder="sofa-icon.svg"
          />
        </div>
        <div class="sm:col-span-2">
          <label class="mb-1 block text-sm font-medium">Descripción</label>
          <input
            v-model="form.descripcion"
            type="text"
            class="w-full rounded-lg border border-madera-300 px-4 py-2.5 outline-none focus:border-madera-600"
          />
        </div>
      </div>

      <p v-if="error" class="mt-3 rounded-lg bg-red-50 px-4 py-2 text-sm text-red-600">
        {{ error }}
      </p>

      <div class="mt-4 flex gap-3">
        <button
          type="submit"
          :disabled="guardando"
          class="rounded-full bg-madera-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-madera-500 disabled:opacity-60"
        >
          {{ guardando ? 'Guardando...' : form.id ? 'Guardar' : 'Crear' }}
        </button>
        <button
          v-if="form.id"
          type="button"
          class="rounded-full border border-madera-300 px-6 py-2.5 text-sm font-semibold text-madera-700"
          @click="nueva"
        >
          Cancelar
        </button>
      </div>
    </form>

    <!-- Listado -->
    <ul class="mt-6 space-y-3">
      <li
        v-for="cat in categorias"
        :key="cat.id"
        class="flex items-center justify-between rounded-xl border border-madera-200 bg-white px-5 py-4"
      >
        <div>
          <p class="font-semibold capitalize text-neutral-800">{{ cat.nombre }}</p>
          <p class="text-xs text-neutral-400">
            {{ cat.total_muebles ?? 0 }} mueble(s) · {{ cat.descripcion }}
          </p>
        </div>
        <div class="flex gap-3 text-sm">
          <button class="font-medium text-madera-600 hover:underline" @click="editar(cat)">
            Editar
          </button>
          <button
            v-if="cat.is_active !== false"
            class="font-medium text-red-500 hover:underline"
            @click="desactivar(cat)"
          >
            Desactivar
          </button>
        </div>
      </li>
    </ul>
  </div>
</template>
