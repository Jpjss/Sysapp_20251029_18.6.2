import { apiRequest } from '@/lib/utils'

export interface DashboardStats {
  total_clientes: number
  total_respostas: number
  atendimentos_hoje: number
  atendimentos_mes: number
  total_questionarios: number
  tipo: 'questionarios' | 'comercial'
  valor_total_vendas?: number
  valor_vendas_hoje?: number
  valor_vendas_mes?: number
}

export interface UltimosDiasData {
  data: string
  total: number
}

export interface TopCliente {
  cd_pessoa: number
  nm_fant: string
  total_atendimentos: number
  ultimo_atendimento: string
}

export interface ProdutoEstoque {
  cd_produto: string
  nm_produto: string
  nm_marca: string
  quantidade: number
  vl_venda: number
  valor_total: number
}

export interface VendaDia {
  data: string
  total_vendas: number
  valor_total: number
  clientes_distintos: number
}

export interface TopProduto {
  cd_produto: string
  nm_produto: string
  nm_marca: string
  quantidade_vendida: number
  valor_total: number
}

export interface VendaPorMarca {
  data: string
  quantidade: number
  valor: number
}

export const relatoriosApi = {
  // Dados do dashboard
  async getDashboard(): Promise<{ success: boolean; stats: DashboardStats }> {
    return apiRequest('/relatorios/dashboard')
  },

  // Últimos 7 dias
  async getUltimosDias(): Promise<{ success: boolean; dados: UltimosDiasData[]; tipo: string }> {
    return apiRequest('/relatorios/ultimos-dias')
  },

  // Top 5 clientes
  async getTopClientes(): Promise<{ success: boolean; clientes: TopCliente[]; tipo: string }> {
    return apiRequest('/relatorios/top-clientes')
  },

  // Relatório de estoque
  async getEstoque(params?: { limite?: number; offset?: number; busca?: string }): Promise<{ success: boolean; produtos: ProdutoEstoque[] }> {
    const queryParams = new URLSearchParams()
    if (params?.limite) queryParams.append('limite', params.limite.toString())
    if (params?.offset) queryParams.append('offset', params.offset.toString())
    if (params?.busca) queryParams.append('busca', params.busca)
    
    const query = queryParams.toString()
    return apiRequest(`/relatorios/estoque${query ? '?' + query : ''}`)
  },

  // Relatório de vendas
  async getVendas(dataInicio: string, dataFim: string): Promise<{ success: boolean; vendas: VendaDia[] }> {
    return apiRequest(`/relatorios/vendas?dataInicio=${dataInicio}&dataFim=${dataFim}`)
  },

  // Top produtos mais vendidos
  async getTopProdutos(params?: { limite?: number; dataInicio?: string; dataFim?: string }): Promise<{ success: boolean; produtos: TopProduto[] }> {
    const queryParams = new URLSearchParams()
    if (params?.limite) queryParams.append('limite', params.limite.toString())
    if (params?.dataInicio) queryParams.append('dataInicio', params.dataInicio)
    if (params?.dataFim) queryParams.append('dataFim', params.dataFim)
    
    const query = queryParams.toString()
    return apiRequest(`/relatorios/top-produtos${query ? '?' + query : ''}`)
  },

  // Vendas por marca
  async getVendasPorMarca(marca: string, dataInicio: string, dataFim: string): Promise<{ success: boolean; vendas: VendaPorMarca[]; marca: string }> {
    const queryParams = new URLSearchParams()
    queryParams.append('marca', marca)
    queryParams.append('dataInicio', dataInicio)
    queryParams.append('dataFim', dataFim)
    
    return apiRequest(`/relatorios/vendas-por-marca?${queryParams.toString()}`)
  },
}
