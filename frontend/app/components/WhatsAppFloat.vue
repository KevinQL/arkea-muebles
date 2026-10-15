<script setup lang="ts">
const { fetchWhatsapp } = useApi()
const { data } = await fetchWhatsapp()

const general = computed(() => data.value?.find((c) => c.tipo === 'general') ?? data.value?.[0])

const href = computed(() =>
  general.value
    ? `https://wa.me/${general.value.numero_telefono.replace(/[^0-9]/g, '')}?text=${encodeURIComponent(general.value.mensaje_default.replace(/\[NOMBRE\]/g, ''))}`
    : '#',
)
</script>

<template>
  <a
    v-if="general"
    :href="href"
    target="_blank"
    rel="noopener"
    class="fixed bottom-6 right-6 z-50 flex h-14 w-14 items-center justify-center rounded-full bg-emerald-500 text-white shadow-lg transition-transform hover:scale-110"
    aria-label="Escríbenos por WhatsApp"
  >
    <svg viewBox="0 0 32 32" class="h-7 w-7 fill-current" aria-hidden="true">
      <path
        d="M16 3C9.4 3 4 8.4 4 15c0 2.1.6 4.2 1.6 6L4 29l8.2-1.5c1.7.9 3.7 1.4 5.7 1.4h.1c6.6 0 12-5.4 12-12S22.6 3 16 3zm0 21.9c-1.8 0-3.5-.5-5-1.4l-.4-.2-4.9.9.9-4.7-.2-.4c-1-1.6-1.5-3.4-1.5-5.3 0-5.4 4.4-9.9 9.9-9.9s9.9 4.4 9.9 9.9-4.4 10.1-8.7 10.1zm5.4-7.4c-.3-.1-1.8-.9-2-1s-.5-.1-.7.1c-.2.3-.8 1-1 1.2-.2.2-.4.2-.7.1-.3-.1-1.3-.5-2.4-1.5-.9-.8-1.5-1.8-1.7-2.1-.2-.3 0-.5.1-.6l.5-.6c.1-.2.2-.3.3-.5.1-.2 0-.4 0-.5-.1-.1-.7-1.7-1-2.3-.2-.6-.5-.5-.7-.5h-.6c-.2 0-.5.1-.8.4-.3.3-1 1-1 2.5s1.1 2.9 1.2 3.1c.1.2 2.1 3.2 5.1 4.5.7.3 1.3.5 1.7.6.7.2 1.4.2 1.9.1.6-.1 1.8-.7 2-1.4.3-.7.3-1.3.2-1.4-.1-.1-.3-.2-.6-.3z"
      />
    </svg>
  </a>
</template>
