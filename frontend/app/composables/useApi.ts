export interface Categoria {
  id: number
  nombre: string
  tipo: string
  subcategoria: string | null
  descripcion: string | null
  icono: string | null
  thumbnail: string | null
  total_muebles?: number
}

export interface Mueble {
  id: number
  categoria_id: number | null
  nombre: string
  slug: string
  precio: string
  estado: 'nuevo' | 'usado' | 'renovado' | 'agendado'
  ubicacion: string
  descripcion_corta: string | null
  descripcion_completa: string | null
  galeria_principal: string | null
  galeria_subida: string | null
  galeria: string[] | null
  materiales: Record<string, any> | null
  medidas: Record<string, number> | null
  especificaciones: Record<string, any> | null
  informacion_adicional: Record<string, any> | null
  categoria?: Categoria | null
}

export interface Paginacion {
  data: Mueble[]
  current_page: number
  last_page: number
  total: number
  per_page: number
}

export const useApi = () => {
  const config = useRuntimeConfig()
  const base = config.public.apiBase

  const fetchMuebles = (params: Record<string, any> = {}) =>
    useFetch<Paginacion>(`${base}/muebles`, {
      query: params,
      key: `muebles-${JSON.stringify(params)}`,
    })

  const fetchMueble = (slug: string) =>
    useFetch<Mueble>(`${base}/muebles/${slug}`, { key: `mueble-${slug}` })

  const fetchCategorias = () =>
    useFetch<Categoria[]>(`${base}/categorias`, { key: 'categorias' })

  const fetchWhatsapp = () =>
    useFetch<any[]>(`${base}/whatsapp`, { key: 'whatsapp' })

  return { fetchMuebles, fetchMueble, fetchCategorias, fetchWhatsapp }
}

export const waLink = (numero: string, mensaje: string) =>
  `https://wa.me/${numero.replace(/[^0-9]/g, '')}?text=${encodeURIComponent(mensaje)}`

export const formatearPrecio = (precio: string | number) =>
  `S/ ${Number(precio).toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`

/**
 * Resuelve la URL de una imagen del catálogo.
 * Las imágenes subidas al servidor (/uploads/...) se sirven desde la API;
 * las rutas externas http(s) se devuelven tal cual.
 * La imagen SUBIDA tiene prioridad sobre la del path.
 */
export const useImgUrl = () => {
  const config = useRuntimeConfig()
  const origin = config.public.apiBase.replace(/\/api\/?$/, '')

  return (path?: string | null): string | null => {
    if (!path) return null
    if (/^https?:\/\//.test(path)) return path
    return origin + (path.startsWith('/') ? '' : '/') + path
  }
}

/** Imagen efectiva de un mueble: subida (servidor) > path manual > null */
export const imagenEfectiva = (mueble: {
  galeria_subida?: string | null
  galeria_principal?: string | null
}) => mueble.galeria_subida || mueble.galeria_principal || null
