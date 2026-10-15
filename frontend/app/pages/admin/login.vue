<script setup lang="ts">
definePageMeta({ middleware: 'admin', layout: false })
useHead({ title: 'Login | Arkea Admin' })

const { login } = useAuth()

const email = ref('admin@arkeamuebles.com')
const password = ref('')
const error = ref('')
const cargando = ref(false)

const enviar = async () => {
  error.value = ''
  cargando.value = true
  try {
    await login(email.value, password.value)
    await navigateTo('/admin')
  } catch (e: any) {
    error.value = e?.data?.message ?? 'Credenciales incorrectas'
  } finally {
    cargando.value = false
  }
}
</script>

<template>
  <div class="flex min-h-screen items-center justify-center bg-madera-900 px-4">
    <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-xl">
      <div class="mb-8 text-center">
        <span class="font-display block text-3xl tracking-widest text-madera-700">ARKEA</span>
        <span class="mt-1 block text-xs tracking-[0.35em] text-madera-500">MUEBLES - ADMIN</span>
      </div>

      <form class="space-y-4" @submit.prevent="enviar">
        <div>
          <label class="mb-1 block text-sm font-medium text-madera-800">Correo</label>
          <input
            v-model="email"
            type="email"
            required
            class="w-full rounded-lg border border-madera-300 px-4 py-2.5 outline-none focus:border-madera-600 focus:ring-2 focus:ring-madera-600/20"
          />
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-madera-800">Contraseña</label>
          <input
            v-model="password"
            type="password"
            required
            class="w-full rounded-lg border border-madera-300 px-4 py-2.5 outline-none focus:border-madera-600 focus:ring-2 focus:ring-madera-600/20"
          />
        </div>

        <p v-if="error" class="rounded-lg bg-red-50 px-4 py-3 text-sm text-red-600">
          {{ error }}
        </p>

        <button
          type="submit"
          :disabled="cargando"
          class="w-full rounded-full bg-madera-600 py-3 font-semibold text-white transition hover:bg-madera-500 disabled:opacity-60"
        >
          {{ cargando ? 'Ingresando...' : 'Ingresar' }}
        </button>
      </form>

      <NuxtLink to="/" class="mt-6 block text-center text-sm text-madera-500 hover:text-madera-700">
        ← Volver al catálogo
      </NuxtLink>
    </div>
  </div>
</template>
