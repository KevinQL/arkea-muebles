<script setup lang="ts">
useHead({ title: 'Contacto | Arkea Muebles' })

const { fetchWhatsapp } = useApi()
const { data: configs } = await fetchWhatsapp()

const general = computed(() => configs.value?.find((c) => c.tipo === 'general') ?? configs.value?.[0])

const form = reactive({
  nombre: '',
  email: '',
  telefono: '',
  asunto: 'Consulta de mueble',
  mensaje: '',
})

const enviado = ref(false)

const waHref = computed(() => {
  if (!general.value) return '#'
  const texto = `Hola Arkea Muebles.\n\nNombre: ${form.nombre}\nAsunto: ${form.asunto}\n\n${form.mensaje}`
  return waLink(general.value.numero_telefono, texto)
})

const enviar = () => {
  enviado.value = true
}
</script>

<template>
  <div class="mx-auto max-w-5xl px-4 py-14 sm:px-6">
    <div class="text-center">
      <span class="text-xs uppercase tracking-[0.4em] text-madera-500">Estamos para ayudarte</span>
      <h1 class="font-display mt-2 text-4xl text-madera-800">Contacto</h1>
      <p class="mx-auto mt-3 max-w-xl text-neutral-600">
        Escríbenos por WhatsApp o completa el formulario y te responderemos pronto.
      </p>
    </div>

    <div class="mt-10 grid gap-8 lg:grid-cols-2">
      <!-- Info de contacto -->
      <div class="space-y-5">
        <a
          v-if="general"
          :href="`https://wa.me/${general.numero_telefono.replace(/[^0-9]/g, '')}`"
          target="_blank"
          rel="noopener"
          class="flex items-center gap-4 rounded-2xl border border-madera-200 bg-white p-5 transition hover:border-madera-600"
        >
          <span class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-500 text-white">
            <svg viewBox="0 0 32 32" class="h-6 w-6 fill-current" aria-hidden="true">
              <path
                d="M16 3C9.4 3 4 8.4 4 15c0 2.1.6 4.2 1.6 6L4 29l8.2-1.5c1.7.9 3.7 1.4 5.7 1.4h.1c6.6 0 12-5.4 12-12S22.6 3 16 3z"
              />
            </svg>
          </span>
          <span>
            <strong class="block text-madera-800">WhatsApp</strong>
            <span class="text-sm text-neutral-500">{{ general.numero_telefono }}</span>
          </span>
        </a>

        <div class="rounded-2xl border border-madera-200 bg-white p-5">
          <strong class="block text-madera-800">Ubicación</strong>
          <span class="text-sm text-neutral-500">Andahuaylas, Apurímac - Perú</span>
        </div>

        <div class="rounded-2xl border border-madera-200 bg-white p-5">
          <strong class="block text-madera-800">Correo</strong>
          <span class="text-sm text-neutral-500">contacto@arkeamuebles.com</span>
        </div>
      </div>

      <!-- Formulario -->
      <form class="rounded-2xl border border-madera-200 bg-white p-6" @submit.prevent="enviar">
        <div class="space-y-4">
          <div>
            <label class="mb-1 block text-sm font-medium text-madera-800">Nombre *</label>
            <input
              v-model="form.nombre"
              required
              type="text"
              class="w-full rounded-lg border border-madera-300 px-4 py-2.5 outline-none focus:border-madera-600 focus:ring-2 focus:ring-madera-600/20"
              placeholder="Tu nombre"
            />
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-madera-800">Correo *</label>
            <input
              v-model="form.email"
              required
              type="email"
              class="w-full rounded-lg border border-madera-300 px-4 py-2.5 outline-none focus:border-madera-600 focus:ring-2 focus:ring-madera-600/20"
              placeholder="tu@email.com"
            />
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-madera-800">Teléfono</label>
            <input
              v-model="form.telefono"
              type="tel"
              class="w-full rounded-lg border border-madera-300 px-4 py-2.5 outline-none focus:border-madera-600 focus:ring-2 focus:ring-madera-600/20"
              placeholder="+51 999 999 999"
            />
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-madera-800">Asunto</label>
            <select
              v-model="form.asunto"
              class="w-full rounded-lg border border-madera-300 px-4 py-2.5 outline-none focus:border-madera-600 focus:ring-2 focus:ring-madera-600/20"
            >
              <option>Consulta de mueble</option>
              <option>Solicitar presupuesto</option>
              <option>Disponibilidad</option>
              <option>Otro</option>
            </select>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-madera-800">Mensaje *</label>
            <textarea
              v-model="form.mensaje"
              required
              rows="4"
              class="w-full rounded-lg border border-madera-300 px-4 py-2.5 outline-none focus:border-madera-600 focus:ring-2 focus:ring-madera-600/20"
              placeholder="Cuéntanos qué necesitas..."
            />
          </div>
        </div>

        <button
          type="submit"
          class="mt-5 w-full rounded-full bg-madera-600 py-3 font-semibold text-white transition hover:bg-madera-500"
        >
          Enviar consulta
        </button>

        <p v-if="enviado" class="mt-4 text-center text-sm text-emerald-600">
          ✅ Consulta preparada. Te redirigimos a WhatsApp para enviarla.
        </p>
        <a
          v-if="enviado && general"
          :href="waHref"
          target="_blank"
          rel="noopener"
          class="mt-2 block text-center text-sm font-semibold text-madera-600 underline"
        >
          Abrir WhatsApp →
        </a>
      </form>
    </div>
  </div>
</template>
