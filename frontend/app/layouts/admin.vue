<script setup lang="ts">
const route = useRoute()
const { user, logout, fetchMe } = useAuth()

const secciones = [
  { to: '/admin', label: 'Dashboard', icon: '📊' },
  { to: '/admin/muebles', label: 'Muebles', icon: '🛋️' },
  { to: '/admin/categorias', label: 'Categorías', icon: '🗂️' },
  { to: '/admin/configuracion', label: 'Configuración', icon: '⚙️' },
]

const menuAbierto = ref(false)

onMounted(async () => {
  if (!user.value) await fetchMe()
})

const esActiva = (to: string) =>
  to === '/admin' ? route.path === '/admin' : route.path.startsWith(to)
</script>

<template>
  <div class="min-h-screen bg-madera-100">
    <!-- Topbar -->
    <header class="sticky top-0 z-40 bg-madera-900 text-madera-100">
      <div class="flex h-16 items-center justify-between px-4 sm:px-6">
        <div class="flex items-center gap-4">
          <button
            class="rounded-md p-2 hover:bg-madera-800 sm:hidden"
            @click="menuAbierto = !menuAbierto"
          >
            ☰
          </button>
          <NuxtLink to="/" class="leading-none">
            <span class="font-display block text-xl tracking-widest text-oro-500">ARKEA</span>
            <span class="block text-[0.6rem] tracking-[0.3em] text-madera-300">ADMIN</span>
          </NuxtLink>
        </div>

        <div class="flex items-center gap-4 text-sm">
          <span class="hidden text-madera-300 sm:inline">
            {{ user?.nombre }} {{ user?.apellido }}
          </span>
          <button
            class="rounded-full border border-madera-600 px-4 py-1.5 transition hover:bg-madera-700"
            @click="logout"
          >
            Salir
          </button>
        </div>
      </div>
    </header>

    <div class="mx-auto flex max-w-7xl">
      <!-- Sidebar -->
      <aside
        class="fixed inset-x-0 top-16 z-30 border-r border-madera-200 bg-white p-4 transition-transform sm:static sm:w-60 sm:shrink-0 sm:translate-x-0 sm:bg-transparent"
        :class="menuAbierto ? 'translate-x-0' : '-translate-x-full sm:translate-x-0'"
      >
        <nav class="space-y-1">
          <NuxtLink
            v-for="seccion in secciones"
            :key="seccion.to"
            :to="seccion.to"
            class="flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-medium transition"
            :class="
              esActiva(seccion.to)
                ? 'bg-madera-600 text-white'
                : 'text-madera-700 hover:bg-madera-100'
            "
            @click="menuAbierto = false"
          >
            <span>{{ seccion.icon }}</span>
            {{ seccion.label }}
          </NuxtLink>
        </nav>
      </aside>

      <!-- Contenido -->
      <main class="min-w-0 flex-1 p-4 sm:p-6 sm:pl-8">
        <slot />
      </main>
    </div>
  </div>
</template>
