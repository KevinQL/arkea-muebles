export interface AuthUser {
  id: number
  nombre: string
  apellido: string | null
  email: string
  rol: string
}

export const useAuth = () => {
  const config = useRuntimeConfig()
  const token = useCookie<string | null>('arkea_token', {
    sameSite: 'lax',
    maxAge: 60 * 60 * 24 * 7,
  })
  const user = useState<AuthUser | null>('auth-user', () => null)

  const authHeaders = computed(() =>
    token.value ? { Authorization: `Bearer ${token.value}` } : {},
  )

  const login = async (email: string, password: string) => {
    const res = await $fetch<{ token: string; usuario: AuthUser }>(
      `${config.public.apiBase}/auth/login`,
      { method: 'POST', body: { email, password } },
    )
    token.value = res.token
    user.value = res.usuario
    return res
  }

  const logout = async () => {
    try {
      await $fetch(`${config.public.apiBase}/admin/logout`, {
        method: 'POST',
        headers: authHeaders.value,
      })
    } catch {
      // token puede ya no ser válido; limpiamos de todos modos
    }
    token.value = null
    user.value = null
    await navigateTo('/admin/login')
  }

  const fetchMe = async () => {
    if (!token.value) return null
    try {
      const me = await $fetch<AuthUser>(`${config.public.apiBase}/admin/me`, {
        headers: authHeaders.value,
      })
      user.value = me
      return me
    } catch {
      token.value = null
      user.value = null
      return null
    }
  }

  const api = $fetch.create({
    baseURL: `${config.public.apiBase}`,
    headers: authHeaders.value,
  })

  const $admin = async <T = any>(path: string, options: any = {}): Promise<T> => {
    return await $fetch<T>(`${config.public.apiBase}${path}`, {
      ...options,
      headers: { ...authHeaders.value, ...(options.headers ?? {}) },
    })
  }

  return { token, user, login, logout, fetchMe, $admin }
}
