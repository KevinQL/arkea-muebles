<script setup lang="ts">
definePageMeta({ middleware: 'admin', layout: 'admin' })
useHead({ title: 'Configuración | Arkea Admin' })

const { $admin } = useAuth()

const { data: configs, refresh } = await useFetch<any[]>(
  `${useRuntimeConfig().public.apiBase}/admin/whatsapp/config`,
  { headers: { Authorization: `Bearer ${useCookie('arkea_token').value}` } },
)

const guardando = ref(false)
const guardado = ref(false)
const error = ref('')

const guardar = async () => {
  error.value = ''
  guardado.value = false
  guardando.value = true
  try {
    await $admin('/admin/whatsapp/config', {
      method: 'PUT',
      body: {
        configs: configs.value?.map((c) => ({
          id: c.id,
          numero_telefono: c.numero_telefono,
          mensaje_default: c.mensaje_default,
          api_key: c.api_key,
          is_active: c.is_active,
        })),
      },
    })
    guardado.value = true
    setTimeout(() => (guardado.value = false), 3000)
  } catch (e: any) {
    error.value = Object.values(e?.data?.errors ?? {}).flat().join(' · ') || 'Error al guardar'
  } finally {
    guardando.value = false
  }
}
</script>

<template>
  <div class="max-w-3xl">
    <h1 class="font-display text-2xl text-madera-800">Configuración</h1>
    <p class="mt-1 text-sm text-neutral-500">
      Números de WhatsApp y mensajes del catálogo. Usa <code>[NOMBRE]</code> para insertar el
      nombre del mueble en el mensaje.
    </p>

    <form class="mt-6 space-y-5" @submit.prevent="guardar">
      <div
        v-for="config in configs"
        :key="config.id"
        class="rounded-xl border border-madera-200 bg-white p-5"
      >
        <div class="flex items-center justify-between">
          <h2 class="font-semibold text-madera-700">
            {{ config.nombre_usuario }}
          </h2>
          <span
            class="rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wider"
            :class="config.tipo === 'general' ? 'bg-madera-600 text-white' : 'bg-oro-500 text-madera-900'"
          >
            {{ config.tipo }}
          </span>
        </div>

        <div class="mt-4 grid gap-4 sm:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm font-medium">Número de teléfono</label>
            <input
              v-model="config.numero_telefono"
              type="text"
              class="w-full rounded-lg border border-madera-300 px-4 py-2.5 outline-none focus:border-madera-600"
              placeholder="+51987654321"
            />
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium">API Key (opcional)</label>
            <input
              v-model="config.api_key"
              type="text"
              class="w-full rounded-lg border border-madera-300 px-4 py-2.5 font-mono text-sm outline-none focus:border-madera-600"
              placeholder="whatsapp business api key"
            />
          </div>
          <div class="sm:col-span-2">
            <label class="mb-1 block text-sm font-medium">Mensaje predefinido</label>
            <textarea
              v-model="config.mensaje_default"
              rows="2"
              class="w-full rounded-lg border border-madera-300 px-4 py-2.5 outline-none focus:border-madera-600"
            />
          </div>
          <label class="flex items-center gap-2 text-sm">
            <input v-model="config.is_active" type="checkbox" class="h-4 w-4 accent-madera-600" />
            Activo
          </label>
        </div>
      </div>

      <p v-if="error" class="rounded-lg bg-red-50 px-4 py-3 text-sm text-red-600">{{ error }}</p>
      <p v-if="guardado" class="rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-600">
        ✅ Configuración guardada
      </p>

      <button
        type="submit"
        :disabled="guardando"
        class="rounded-full bg-madera-600 px-7 py-3 font-semibold text-white transition hover:bg-madera-500 disabled:opacity-60"
      >
        {{ guardando ? 'Guardando...' : 'Guardar configuración' }}
      </button>
    </form>
  </div>
</template>
