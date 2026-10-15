<script setup lang="ts">
import type { Categoria, Mueble } from '~/composables/useApi'

const props = defineProps<{ mueble?: Mueble }>()

const token = useCookie('arkea_token')
const apiBase = useRuntimeConfig().public.apiBase
const headers = { Authorization: `Bearer ${token.value}` }

const { data: categorias } = await useFetch<Categoria[]>(`${apiBase}/categorias`, { headers })
const { data: whatsappConfigs } = await useFetch<any[]>(`${apiBase}/whatsapp`)

const esEdicion = computed(() => Boolean(props.mueble))

const slugify = (texto: string) =>
  texto
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/(^-|-$)/g, '')

const form = reactive({
  nombre: props.mueble?.nombre ?? '',
  slug: props.mueble?.slug ?? '',
  categoria_id: props.mueble?.categoria_id ?? null,
  precio: props.mueble?.precio ?? '',
  estado: props.mueble?.estado ?? 'nuevo',
  ubicacion: props.mueble?.ubicacion ?? 'Andahuaylas',
  descripcion_corta: props.mueble?.descripcion_corta ?? '',
  descripcion_completa: props.mueble?.descripcion_completa ?? '',
  galeria_principal: props.mueble?.galeria_principal ?? '',
  galeria_subida: props.mueble?.galeria_subida ?? '',
  estructura: props.mueble?.materiales?.estructura ?? '',
  tapizado: props.mueble?.materiales?.tapizado ?? '',
  alto_cm: props.mueble?.medidas?.alto_cm ?? '',
  ancho_cm: props.mueble?.medidas?.ancho_cm ?? '',
  profundidad_cm: props.mueble?.medidas?.profundidad_cm ?? '',
  peso_kg: props.mueble?.medidas?.peso_kg ?? '',
  garantia_anios: props.mueble?.informacion_adicional?.garantia_anios ?? 1,
  envio_disponible: props.mueble?.informacion_adicional?.envio_disponible ?? false,
  colores: (props.mueble?.informacion_adicional?.color_disponible ?? []).join(', '),
  whatsapp_id:
    props.mueble?.informacion_adicional?.consultas_whatsapp_id ??
    whatsappConfigs.value?.[0]?.id ??
    1,
  is_active: props.mueble?.is_active ?? true,
})

const slugManual = ref(Boolean(props.mueble))

watch(
  () => form.nombre,
  (nombre) => {
    if (!slugManual.value) form.slug = slugify(nombre)
  },
)

const guardando = ref(false)
const error = ref('')
const guardado = ref(false)

const imgUrl = useImgUrl()
const subiendo = ref(false)
const inputArchivo = ref<HTMLInputElement | null>(null)
const arrastrando = ref(false)

const previewSubida = computed(() => imgUrl(form.galeria_subida))
const previewPath = computed(() => imgUrl(form.galeria_principal))
// Prioridad: imagen subida al servidor > path manual
const previewActiva = computed(() => previewSubida.value ?? previewPath.value)

const TIPOS_PERMITIDOS = ['image/jpeg', 'image/png', 'image/webp']
const MAXIMO_BYTES = 4 * 1024 * 1024

const seleccionarArchivo = () => inputArchivo.value?.click()

const subirArchivo = async (archivo: File) => {
  if (!TIPOS_PERMITIDOS.includes(archivo.type)) {
    error.value = 'Formato no permitido. Usa JPG, PNG o WebP.'
    return
  }
  if (archivo.size > MAXIMO_BYTES) {
    error.value = 'La imagen supera el máximo de 4 MB.'
    return
  }

  error.value = ''
  subiendo.value = true
  try {
    const datos = new FormData()
    datos.append('imagen', archivo)
    const res = await $fetch<{ url: string }>(`${apiBase}/admin/media`, {
      method: 'POST',
      headers,
      body: datos,
    })
    form.galeria_subida = res.url
  } catch (e: any) {
    error.value =
      Object.values(e?.data?.errors ?? {}).flat().join(' · ') ||
      'Error al subir la imagen (máx. 4 MB, jpg/png/webp)'
  } finally {
    subiendo.value = false
  }
}

const onArchivoSeleccionado = async (event: Event) => {
  const input = event.target as HTMLInputElement
  const archivo = input.files?.[0]
  input.value = ''
  if (archivo) await subirArchivo(archivo)
}

// --- Drag & Drop ---
const onDragOver = (event: DragEvent) => {
  event.preventDefault()
  if (!subiendo.value) arrastrando.value = true
}

const onDragLeave = (event: DragEvent) => {
  // Ignora el leave cuando el puntero entra a un hijo del dropzone
  const zona = event.currentTarget as HTMLElement
  const hijo = event.relatedTarget as Node | null
  if (hijo && zona.contains(hijo)) return
  arrastrando.value = false
}

const onDrop = async (event: DragEvent) => {
  event.preventDefault()
  arrastrando.value = false
  if (subiendo.value) return
  const archivo = event.dataTransfer?.files?.[0]
  if (archivo) await subirArchivo(archivo)
}

const quitarImagenSubida = () => {
  form.galeria_subida = ''
}

const cuerpo = () => ({
  nombre: form.nombre,
  slug: form.slug || slugify(form.nombre),
  categoria_id: form.categoria_id ? Number(form.categoria_id) : null,
  precio: Number(form.precio),
  estado: form.estado,
  ubicacion: form.ubicacion,
  descripcion_corta: form.descripcion_corta,
  descripcion_completa: form.descripcion_completa,
  galeria_principal: form.galeria_principal || null,
  galeria_subida: form.galeria_subida || null,
  materiales: {
    ...(form.estructura ? { estructura: form.estructura } : {}),
    ...(form.tapizado ? { tapizado: form.tapizado } : {}),
  },
  medidas: {
    alto_cm: Number(form.alto_cm) || 0,
    ancho_cm: Number(form.ancho_cm) || 0,
    profundidad_cm: Number(form.profundidad_cm) || 0,
    peso_kg: Number(form.peso_kg) || 0,
  },
  especificaciones: props.mueble?.especificaciones ?? {},
  informacion_adicional: {
    garantia_anios: Number(form.garantia_anios) || 0,
    envio_disponible: form.envio_disponible,
    disponibilidad: 'stock',
    color_disponible: form.colores
      .split(',')
      .map((c: string) => c.trim())
      .filter(Boolean),
    consultas_whatsapp_id: Number(form.whatsapp_id),
  },
  is_active: form.is_active,
})

const guardar = async () => {
  error.value = ''
  guardado.value = false
  guardando.value = true
  try {
    if (esEdicion.value) {
      await $fetch(`${apiBase}/admin/muebles/${props.mueble!.id}`, {
        method: 'PUT',
        headers,
        body: cuerpo(),
      })
    } else {
      await $fetch(`${apiBase}/admin/muebles`, {
        method: 'POST',
        headers,
        body: cuerpo(),
      })
    }
    guardado.value = true
    if (!esEdicion.value) {
      setTimeout(() => navigateTo('/admin/muebles'), 800)
    }
  } catch (e: any) {
    const errores = e?.data?.errors
    error.value = errores
      ? Object.values(errores).flat().join(' · ')
      : e?.data?.message ?? 'Error al guardar'
  } finally {
    guardando.value = false
  }
}
</script>

<template>
  <div class="max-w-3xl">
    <h1 class="font-display text-2xl text-madera-800">
      {{ esEdicion ? 'Editar mueble' : 'Nuevo mueble' }}
    </h1>

    <form class="mt-6 space-y-6" @submit.prevent="guardar">
      <!-- Básico -->
      <fieldset class="rounded-xl border border-madera-200 bg-white p-5">
        <legend class="px-2 font-semibold text-madera-700">Información básica</legend>

        <div class="grid gap-4 sm:grid-cols-2">
          <div class="sm:col-span-2">
            <label class="mb-1 block text-sm font-medium">Nombre *</label>
            <input
              v-model="form.nombre"
              required
              type="text"
              class="w-full rounded-lg border border-madera-300 px-4 py-2.5 outline-none focus:border-madera-600"
            />
          </div>

          <div class="sm:col-span-2">
            <label class="mb-1 block text-sm font-medium">Slug (URL) </label>
            <input
              v-model="form.slug"
              type="text"
              class="w-full rounded-lg border border-madera-300 px-4 py-2.5 font-mono text-sm outline-none focus:border-madera-600"
              @input="slugManual = true"
            />
            <p class="mt-1 text-xs text-neutral-400">
              Se genera automáticamente del nombre si lo dejas vacío al crear.
            </p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium">Categoría</label>
            <select
              v-model="form.categoria_id"
              class="w-full rounded-lg border border-madera-300 px-4 py-2.5 outline-none focus:border-madera-600"
            >
              <option :value="null">Sin categoría</option>
              <option v-for="cat in categorias" :key="cat.id" :value="cat.id">
                {{ cat.nombre }}
              </option>
            </select>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium">Precio (S/) *</label>
            <input
              v-model="form.precio"
              required
              type="number"
              step="0.01"
              min="0"
              class="w-full rounded-lg border border-madera-300 px-4 py-2.5 outline-none focus:border-madera-600"
            />
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium">Estado</label>
            <select
              v-model="form.estado"
              class="w-full rounded-lg border border-madera-300 px-4 py-2.5 outline-none focus:border-madera-600"
            >
              <option value="nuevo">Nuevo</option>
              <option value="renovado">Renovado</option>
              <option value="usado">Usado</option>
              <option value="agendado">Agendado</option>
            </select>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium">Ubicación</label>
            <input
              v-model="form.ubicacion"
              type="text"
              class="w-full rounded-lg border border-madera-300 px-4 py-2.5 outline-none focus:border-madera-600"
            />
          </div>
        </div>
      </fieldset>

      <!-- Descripción e imagen -->
      <fieldset class="rounded-xl border border-madera-200 bg-white p-5">
        <legend class="px-2 font-semibold text-madera-700">Descripción e imagen</legend>

        <div class="space-y-4">
          <div>
            <label class="mb-1 block text-sm font-medium">Descripción corta</label>
            <textarea
              v-model="form.descripcion_corta"
              rows="2"
              class="w-full rounded-lg border border-madera-300 px-4 py-2.5 outline-none focus:border-madera-600"
            />
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium">Descripción completa</label>
            <textarea
              v-model="form.descripcion_completa"
              rows="4"
              class="w-full rounded-lg border border-madera-300 px-4 py-2.5 outline-none focus:border-madera-600"
            />
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium">Foto principal (path)</label>
            <input
              v-model="form.galeria_principal"
              type="text"
              placeholder="/uploads/muebles/sofa-01.jpg"
              class="w-full rounded-lg border border-madera-300 px-4 py-2.5 font-mono text-sm outline-none focus:border-madera-600"
            />
            <p class="mt-1 text-xs text-neutral-400">
              Ruta manual de la imagen. Si subes una imagen al servidor, esta tiene prioridad
              sobre el path.
            </p>

            <!-- Zona de subida: clic o arrastrar y soltar -->
            <input
              ref="inputArchivo"
              type="file"
              accept="image/jpeg,image/png,image/webp"
              class="hidden"
              @change="onArchivoSeleccionado"
            />
            <div
              class="mt-3 cursor-pointer rounded-lg border-2 border-dashed p-6 text-center transition-colors"
              :class="
                arrastrando
                  ? 'border-madera-600 bg-madera-100'
                  : 'border-madera-300 bg-madera-50 hover:border-madera-400'
              "
              role="button"
              tabindex="0"
              :aria-disabled="subiendo || undefined"
              @click="seleccionarArchivo"
              @keydown.enter.prevent="seleccionarArchivo"
              @keydown.space.prevent="seleccionarArchivo"
              @dragover="onDragOver"
              @dragleave="onDragLeave"
              @drop="onDrop"
            >
              <template v-if="subiendo">
                <p class="text-2xl">⏳</p>
                <p class="mt-1 text-sm font-semibold text-madera-700">Subiendo imagen...</p>
              </template>
              <template v-else-if="arrastrando">
                <p class="text-3xl">📥</p>
                <p class="mt-1 text-sm font-semibold text-madera-700">Suelta la imagen aquí</p>
              </template>
              <template v-else>
                <p class="text-3xl">🖼️</p>
                <p class="mt-1 text-sm font-semibold text-madera-700">
                  Arrastra una imagen aquí o haz clic para elegir
                </p>
                <p class="mt-1 text-xs text-neutral-400">JPG, PNG o WebP · máx. 4 MB</p>
              </template>
            </div>

            <!-- Preview: la subida prima sobre el path -->
            <div v-if="previewActiva" class="mt-3 rounded-lg border border-madera-200 p-3">
              <NuxtImg
                :src="previewActiva"
                alt="preview"
                width="400"
                height="200"
                class="h-32 w-full rounded border border-madera-100 bg-white object-contain"
              />
              <div class="mt-2 flex items-center justify-between gap-2">
                <span
                  v-if="form.galeria_subida"
                  class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700"
                >
                  ✅ Imagen del servidor (tiene prioridad)
                </span>
                <span v-else class="rounded-full bg-madera-100 px-3 py-1 text-xs font-semibold text-madera-700">
                  📝 Path manual
                </span>
                <button
                  v-if="form.galeria_subida"
                  type="button"
                  class="text-xs font-medium text-red-500 hover:underline"
                  @click="quitarImagenSubida"
                >
                  Quitar imagen subida
                </button>
              </div>
            </div>
            <p v-else class="mt-2 text-xs text-neutral-400">
              Sin imagen: se muestra el logo Arkea en el catálogo.
            </p>
          </div>
        </div>
      </fieldset>

      <!-- Técnicos -->
      <fieldset class="rounded-xl border border-madera-200 bg-white p-5">
        <legend class="px-2 font-semibold text-madera-700">Datos técnicos</legend>

        <div class="grid gap-4 sm:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm font-medium">Estructura / material</label>
            <input
              v-model="form.estructura"
              type="text"
              placeholder="Madera de roble"
              class="w-full rounded-lg border border-madera-300 px-4 py-2.5 outline-none focus:border-madera-600"
            />
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium">Tapizado / superficie</label>
            <input
              v-model="form.tapizado"
              type="text"
              placeholder="Tela lino premium"
              class="w-full rounded-lg border border-madera-300 px-4 py-2.5 outline-none focus:border-madera-600"
            />
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium">Alto (cm)</label>
            <input v-model="form.alto_cm" type="number" step="0.1" class="w-full rounded-lg border border-madera-300 px-4 py-2.5 outline-none focus:border-madera-600" />
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium">Ancho (cm)</label>
            <input v-model="form.ancho_cm" type="number" step="0.1" class="w-full rounded-lg border border-madera-300 px-4 py-2.5 outline-none focus:border-madera-600" />
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium">Profundidad (cm)</label>
            <input v-model="form.profundidad_cm" type="number" step="0.1" class="w-full rounded-lg border border-madera-300 px-4 py-2.5 outline-none focus:border-madera-600" />
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium">Peso (kg)</label>
            <input v-model="form.peso_kg" type="number" step="0.1" class="w-full rounded-lg border border-madera-300 px-4 py-2.5 outline-none focus:border-madera-600" />
          </div>
        </div>
      </fieldset>

      <!-- Adicional -->
      <fieldset class="rounded-xl border border-madera-200 bg-white p-5">
        <legend class="px-2 font-semibold text-madera-700">Información adicional</legend>

        <div class="grid gap-4 sm:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm font-medium">Garantía (años)</label>
            <input v-model="form.garantia_anios" type="number" min="0" class="w-full rounded-lg border border-madera-300 px-4 py-2.5 outline-none focus:border-madera-600" />
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium">Contacto WhatsApp</label>
            <select
              v-model="form.whatsapp_id"
              class="w-full rounded-lg border border-madera-300 px-4 py-2.5 outline-none focus:border-madera-600"
            >
              <option v-for="cfg in whatsappConfigs" :key="cfg.id" :value="cfg.id">
                {{ cfg.nombre_usuario }} ({{ cfg.tipo }})
              </option>
            </select>
          </div>
          <div class="sm:col-span-2">
            <label class="mb-1 block text-sm font-medium">Colores disponibles (separados por coma)</label>
            <input
              v-model="form.colores"
              type="text"
              placeholder="gris oscuro, beige, naranja"
              class="w-full rounded-lg border border-madera-300 px-4 py-2.5 outline-none focus:border-madera-600"
            />
          </div>
          <label class="flex items-center gap-2 text-sm">
            <input v-model="form.envio_disponible" type="checkbox" class="h-4 w-4 accent-madera-600" />
            Envío disponible
          </label>
          <label class="flex items-center gap-2 text-sm">
            <input v-model="form.is_active" type="checkbox" class="h-4 w-4 accent-madera-600" />
            Visible en catálogo
          </label>
        </div>
      </fieldset>

      <!-- Errores / éxito -->
      <p v-if="error" class="rounded-lg bg-red-50 px-4 py-3 text-sm text-red-600">{{ error }}</p>
      <p v-if="guardado" class="rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-600">
        ✅ Guardado correctamente
      </p>

      <!-- Acciones -->
      <div class="flex gap-3">
        <button
          type="submit"
          :disabled="guardando"
          class="rounded-full bg-madera-600 px-7 py-3 font-semibold text-white transition hover:bg-madera-500 disabled:opacity-60"
        >
          {{ guardando ? 'Guardando...' : esEdicion ? 'Guardar cambios' : 'Crear mueble' }}
        </button>
        <NuxtLink
          to="/admin/muebles"
          class="rounded-full border border-madera-300 px-7 py-3 font-semibold text-madera-700 transition hover:bg-madera-100"
        >
          Cancelar
        </NuxtLink>
      </div>
    </form>
  </div>
</template>
