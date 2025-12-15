import { apiRequest } from '@/lib/utils'
import { Empresa } from './auth'

export interface EmpresaAtual {
  id: number
  nome: string
  database: string
}

export const empresasApi = {
  // Lista empresas do usuário
  async listar(): Promise<{ success: boolean; empresas: Empresa[] }> {
    return apiRequest('/empresas')
  },

  // Seleciona uma empresa
  async selecionar(empresaId: number): Promise<{ success: boolean; empresa: EmpresaAtual }> {
    return apiRequest('/empresas/selecionar', {
      method: 'POST',
      body: JSON.stringify({ empresaId }),
    })
  },

  // Retorna empresa selecionada
  async getAtual(): Promise<{ success: boolean; empresa: EmpresaAtual | null }> {
    return apiRequest('/empresas/atual')
  },
}
