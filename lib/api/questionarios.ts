import { apiRequest } from '@/lib/utils'

export interface Questionario {
  id: number
  titulo: string
  descricao: string
  ativo: boolean
}

export interface Cliente {
  id: number
  nome: string
  fantasia: string
  telefone: string
  celular: string
  email: string
  ultimo_atendimento: string | null
  qtde_compras: number
  valor_total_compras: number
}

export interface Pergunta {
  id: number
  ordem: number
  pergunta: string
  tipo_resposta: string
  obrigatoria: boolean
  opcoes: string | null
}

export interface Resposta {
  perguntaId: number
  resposta: string
}

export interface AtendimentoData {
  clienteId: number
  questionarioId: number
  respostas: Resposta[]
  observacao?: string
}

export const questionariosApi = {
  // Lista questionários disponíveis
  async listar(): Promise<{ success: boolean; questionarios: Questionario[] }> {
    return apiRequest('/questionarios')
  },

  // Lista clientes pendentes de atendimento
  async getPendentes(): Promise<{ success: boolean; clientes: Cliente[] }> {
    return apiRequest('/questionarios/pendentes')
  },

  // Obtém perguntas de um questionário
  async getPerguntas(questionarioId: number): Promise<{ success: boolean; perguntas: Pergunta[] }> {
    return apiRequest(`/questionarios/${questionarioId}/perguntas`)
  },

  // Salva respostas de questionário
  async responder(data: AtendimentoData): Promise<{ success: boolean; atendimentoId: number; message: string }> {
    return apiRequest('/questionarios/responder', {
      method: 'POST',
      body: JSON.stringify(data),
    })
  },

  // Histórico de atendimentos
  async getHistorico(limite = 50, offset = 0): Promise<{ success: boolean; historico: any[] }> {
    return apiRequest(`/questionarios/historico?limite=${limite}&offset=${offset}`)
  },
}
