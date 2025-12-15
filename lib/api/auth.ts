import { apiRequest } from '@/lib/utils'

export interface User {
  id: number
  nome: string
  email: string
  tipo: string
  empresas: Empresa[]
}

export interface Empresa {
  id: number
  nm_empresa: string
  database: string
  host: string
  usuario: string
  porta: number
}

export interface LoginCredentials {
  email: string
  senha: string
}

export interface AuthResponse {
  success: boolean
  authenticated?: boolean
  user?: User
  error?: string
}

export const authApi = {
  // Verifica sessão ativa
  async checkSession(): Promise<AuthResponse> {
    return apiRequest('/auth/session')
  },

  // Fazer login
  async login(credentials: LoginCredentials): Promise<AuthResponse> {
    return apiRequest('/auth/login', {
      method: 'POST',
      body: JSON.stringify(credentials),
    })
  },

  // Fazer logout
  async logout(): Promise<{ success: boolean; message: string }> {
    return apiRequest('/auth/logout', {
      method: 'POST',
    })
  },
}
