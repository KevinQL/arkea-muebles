<template>
  <footer class="mt-16 bg-madera-900 text-madera-300">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 py-12 sm:grid-cols-2 sm:px-6 lg:grid-cols-3">
      <div>
        <span class="font-display block text-2xl tracking-widest text-oro-500">ARKEA MUEBLES</span>
        <p class="mt-3 text-sm leading-relaxed">
          Catálogo digital de muebles con calidad y diseño. Atendemos en Andahuaylas, Apurímac.
        </p>
      </div>

      <div>
        <h3 class="mb-3 text-sm font-semibold uppercase tracking-widest text-madera-100">
          Contacto
        </h3>
        <ul class="space-y-2 text-sm">
          <li>WhatsApp: +51 987 654 321</li>
          <li>Correo: contacto@arkeamuebles.com</li>
          <li>Andahuaylas - Apurímac, Perú</li>
        </ul>
      </div>

      <div>
        <h3 class="mb-3 text-sm font-semibold uppercase tracking-widest text-madera-100">
          Enlaces
        </h3>
        <ul class="space-y-2 text-sm">
          <li><NuxtLink to="/" class="hover:text-oro-400">Catálogo</NuxtLink></li>
          <li><NuxtLink to="/contacto" class="hover:text-oro-400">Contacto</NuxtLink></li>
        </ul>
      </div>
    </div>

    <div class="border-t border-madera-800 py-4 text-center text-xs">
      © {{ new Date().getFullYear() }} Arkea Muebles. Todos los derechos reservados.
    </div>
  </footer>
</template>
