"use client"

import { useEffect, useState } from "react"
import { useRouter } from "next/navigation"
import { authApi } from "@/lib/api/auth"
import { empresasApi } from "@/lib/api/empresas"
import { relatoriosApi, type ProdutoEstoque, type VendaDia, type TopProduto, type VendaPorMarca } from "@/lib/api/relatorios"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs"
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table"
import { useToast } from "@/components/ui/use-toast"
import { Search, ArrowLeft, Package, TrendingUp, Calendar } from "lucide-react"
import Link from "next/link"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import { LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer } from 'recharts'

export default function RelatoriosPage() {
  const router = useRouter()
  const { toast } = useToast()
  const [produtos, setProdutos] = useState<ProdutoEstoque[]>([])
  const [vendas, setVendas] = useState<VendaDia[]>([])
  const [topProdutos, setTopProdutos] = useState<TopProduto[]>([])
  const [selectedBrand, setSelectedBrand] = useState<string>("")
  const [brandSalesData, setBrandSalesData] = useState<VendaPorMarca[]>([])
  const [isLoading, setIsLoading] = useState(true)
  const [searchTerm, setSearchTerm] = useState("")
  const [dataInicio, setDataInicio] = useState(new Date().toISOString().split('T')[0].slice(0, 8) + '01') // Primeiro dia do mês
  const [dataFim, setDataFim] = useState(new Date().toISOString().split('T')[0])

  useEffect(() => {
    async function loadData() {
      try {
        const sessionResponse = await authApi.checkSession()
        if (!sessionResponse.authenticated) {
          router.push('/login')
          return
        }

        const empresaResponse = await empresasApi.getAtual()
        if (!empresaResponse.empresa) {
          router.push('/escolher-empresa')
          return
        }

        await Promise.all([
          loadEstoque(),
          loadVendas(),
          loadTopProdutos()
        ])
      } catch (error: any) {
        toast({
          title: "Erro ao carregar dados",
          description: error.message,
          variant: "destructive",
        })
      } finally {
        setIsLoading(false)
      }
    }

    loadData()
  }, [router, toast])

  const loadEstoque = async () => {
    try {
      const response = await relatoriosApi.getEstoque({ limite: 100 })
      setProdutos(response.produtos)
    } catch (error: any) {
      toast({
        title: "Erro ao carregar estoque",
        description: error.message,
        variant: "destructive",
      })
    }
  }

  const loadVendas = async () => {
    try {
      const response = await relatoriosApi.getVendas(dataInicio, dataFim)
      setVendas(response.vendas)
    } catch (error: any) {
      toast({
        title: "Erro ao carregar vendas",
        description: error.message,
        variant: "destructive",
      })
    }
  }

  const loadTopProdutos = async () => {
    try {
      const response = await relatoriosApi.getTopProdutos({
        limite: 10,
        dataInicio,
        dataFim
      })
      setTopProdutos(response.produtos)
    } catch (error: any) {
      toast({
        title: "Erro ao carregar top produtos",
        description: error.message,
        variant: "destructive",
      })
    }
  }

  const handleBuscar = async () => {
    try {
      const response = await relatoriosApi.getEstoque({ limite: 100, busca: searchTerm })
      setProdutos(response.produtos)
    } catch (error: any) {
      toast({
        title: "Erro ao buscar produtos",
        description: error.message,
        variant: "destructive",
      })
    }
  }

  const handleFiltrarVendas = async () => {
    await Promise.all([loadVendas(), loadTopProdutos()])
  }

  const loadBrandSalesData = async (brand: string) => {
    if (!brand) {
      setBrandSalesData([])
      return
    }

    try {
      const response = await relatoriosApi.getVendasPorMarca(brand, dataInicio, dataFim)
      setBrandSalesData(response.vendas)
    } catch (error: any) {
      toast({
        title: "Erro ao carregar dados da marca",
        description: error.message,
        variant: "destructive",
      })
    }
  }

  // Atualizar dados quando a marca selecionada mudar
  useEffect(() => {
    if (selectedBrand) {
      loadBrandSalesData(selectedBrand)
    }
  }, [selectedBrand, dataInicio, dataFim])

  const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('pt-BR', {
      style: 'currency',
      currency: 'BRL'
    }).format(value)
  }

  if (isLoading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mx-auto"></div>
      </div>
    )
  }

  return (
    <div className="min-h-screen bg-background">
      <div className="container mx-auto px-4 py-8">
        <div className="mb-6 flex items-center gap-4">
          <Link href="/dashboard">
            <Button variant="outline" size="icon">
              <ArrowLeft className="h-4 w-4" />
            </Button>
          </Link>
          <div>
            <h1 className="text-3xl font-bold">Relatórios</h1>
            <p className="text-muted-foreground">Visualize informações de estoque e vendas</p>
          </div>
        </div>

        <Tabs defaultValue="estoque" className="space-y-4">
          <TabsList>
            <TabsTrigger value="estoque">
              <Package className="h-4 w-4 mr-2" />
              Estoque
            </TabsTrigger>
            <TabsTrigger value="vendas">
              <TrendingUp className="h-4 w-4 mr-2" />
              Vendas
            </TabsTrigger>
            <TabsTrigger value="top-produtos">
              <TrendingUp className="h-4 w-4 mr-2" />
              Top Produtos
            </TabsTrigger>
            <TabsTrigger value="marcas-vendidas">
              <TrendingUp className="h-4 w-4 mr-2" />
              Marcas Vendidas
            </TabsTrigger>
          </TabsList>

          <TabsContent value="estoque" className="space-y-4">
            <Card>
              <CardHeader>
                <CardTitle>Relatório de Estoque</CardTitle>
                <CardDescription>Produtos disponíveis em estoque</CardDescription>
              </CardHeader>
              <CardContent>
                <div className="flex gap-4 mb-4">
                  <div className="relative flex-1">
                    <Search className="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
                    <Input
                      placeholder="Buscar produto..."
                      value={searchTerm}
                      onChange={(e) => setSearchTerm(e.target.value)}
                      onKeyDown={(e) => e.key === 'Enter' && handleBuscar()}
                      className="pl-10"
                    />
                  </div>
                  <Button onClick={handleBuscar}>Buscar</Button>
                </div>

                <div className="rounded-md border">
                  <Table>
                    <TableHeader>
                      <TableRow>
                        <TableHead>Código</TableHead>
                        <TableHead>Produto</TableHead>
                        <TableHead>Marca</TableHead>
                        <TableHead className="text-right">Quantidade</TableHead>
                        <TableHead className="text-right">Valor Unit.</TableHead>
                        <TableHead className="text-right">Valor Total</TableHead>
                      </TableRow>
                    </TableHeader>
                    <TableBody>
                      {produtos.map((produto) => (
                        <TableRow key={produto.cd_produto}>
                          <TableCell className="font-medium">{produto.cd_produto}</TableCell>
                          <TableCell>{produto.nm_produto}</TableCell>
                          <TableCell>{produto.nm_marca || '-'}</TableCell>
                          <TableCell className="text-right">{produto.quantidade}</TableCell>
                          <TableCell className="text-right">{formatCurrency(produto.vl_venda)}</TableCell>
                          <TableCell className="text-right font-semibold">{formatCurrency(produto.valor_total)}</TableCell>
                        </TableRow>
                      ))}
                    </TableBody>
                  </Table>
                </div>
              </CardContent>
            </Card>
          </TabsContent>

          <TabsContent value="vendas" className="space-y-4">
            <Card>
              <CardHeader>
                <CardTitle>Relatório de Vendas</CardTitle>
                <CardDescription>Vendas realizadas no período</CardDescription>
              </CardHeader>
              <CardContent>
                <div className="flex gap-4 mb-4">
                  <div className="flex gap-2 items-center">
                    <Calendar className="h-4 w-4 text-muted-foreground" />
                    <Input
                      type="date"
                      value={dataInicio}
                      onChange={(e) => setDataInicio(e.target.value)}
                    />
                  </div>
                  <span className="flex items-center">até</span>
                  <Input
                    type="date"
                    value={dataFim}
                    onChange={(e) => setDataFim(e.target.value)}
                  />
                  <Button onClick={handleFiltrarVendas}>Filtrar</Button>
                </div>

                <div className="rounded-md border">
                  <Table>
                    <TableHeader>
                      <TableRow>
                        <TableHead>Data</TableHead>
                        <TableHead className="text-right">Nº Vendas</TableHead>
                        <TableHead className="text-right">Valor Total</TableHead>
                        <TableHead className="text-right">Clientes Distintos</TableHead>
                      </TableRow>
                    </TableHeader>
                    <TableBody>
                      {vendas.map((venda, idx) => (
                        <TableRow key={idx}>
                          <TableCell>{new Date(venda.data).toLocaleDateString('pt-BR')}</TableCell>
                          <TableCell className="text-right">{venda.total_vendas}</TableCell>
                          <TableCell className="text-right font-semibold">{formatCurrency(parseFloat(venda.valor_total))}</TableCell>
                          <TableCell className="text-right">{venda.clientes_distintos}</TableCell>
                        </TableRow>
                      ))}
                    </TableBody>
                  </Table>
                </div>
              </CardContent>
            </Card>
          </TabsContent>

          <TabsContent value="top-produtos" className="space-y-4">
            <Card>
              <CardHeader>
                <CardTitle>Top 10 Produtos Mais Vendidos</CardTitle>
                <CardDescription>Produtos com maior volume de vendas no período</CardDescription>
              </CardHeader>
              <CardContent>
                <div className="rounded-md border">
                  <Table>
                    <TableHeader>
                      <TableRow>
                        <TableHead>Posição</TableHead>
                        <TableHead>Código</TableHead>
                        <TableHead>Produto</TableHead>
                        <TableHead>Marca</TableHead>
                        <TableHead className="text-right">Qtd. Vendida</TableHead>
                        <TableHead className="text-right">Valor Total</TableHead>
                      </TableRow>
                    </TableHeader>
                    <TableBody>
                      {topProdutos.map((produto, idx) => (
                        <TableRow key={produto.cd_produto}>
                          <TableCell className="font-bold">{idx + 1}º</TableCell>
                          <TableCell>{produto.cd_produto}</TableCell>
                          <TableCell>{produto.nm_produto}</TableCell>
                          <TableCell>{produto.nm_marca || '-'}</TableCell>
                          <TableCell className="text-right">{produto.quantidade_vendida}</TableCell>
                          <TableCell className="text-right font-semibold">{formatCurrency(parseFloat(produto.valor_total))}</TableCell>
                        </TableRow>
                      ))}
                    </TableBody>
                  </Table>
                </div>
              </CardContent>
            </Card>
          </TabsContent>

          <TabsContent value="marcas-vendidas" className="space-y-4">
            <Card>
              <CardHeader>
                <CardTitle>Análise de Vendas por Marca</CardTitle>
                <CardDescription>Selecione uma marca para visualizar o histórico de vendas</CardDescription>
              </CardHeader>
              <CardContent className="space-y-6">
                <div className="flex flex-col sm:flex-row gap-4">
                  <div className="flex-1">
                    <label className="text-sm font-medium mb-2 block">Selecione a Marca</label>
                    <Select value={selectedBrand} onValueChange={setSelectedBrand}>
                      <SelectTrigger>
                        <SelectValue placeholder="Escolha uma marca do top produtos" />
                      </SelectTrigger>
                      <SelectContent>
                        {topProdutos
                          .filter((p, idx, arr) => arr.findIndex(item => item.nm_marca === p.nm_marca) === idx)
                          .map((produto) => (
                            <SelectItem key={produto.nm_marca || 'sem-marca'} value={produto.nm_marca || 'Sem Marca'}>
                              {produto.nm_marca || 'Sem Marca'}
                            </SelectItem>
                          ))}
                      </SelectContent>
                    </Select>
                  </div>
                  
                  <div className="flex gap-2">
                    <div>
                      <label className="text-sm font-medium mb-2 block">Data Início</label>
                      <Input
                        type="date"
                        value={dataInicio}
                        onChange={(e) => setDataInicio(e.target.value)}
                      />
                    </div>
                    <div>
                      <label className="text-sm font-medium mb-2 block">Data Fim</label>
                      <Input
                        type="date"
                        value={dataFim}
                        onChange={(e) => setDataFim(e.target.value)}
                      />
                    </div>
                  </div>
                </div>

                {selectedBrand && brandSalesData.length > 0 && (
                  <div className="space-y-4">
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                      <Card>
                        <CardHeader className="pb-2">
                          <CardTitle className="text-sm font-medium text-muted-foreground">Total de Vendas</CardTitle>
                        </CardHeader>
                        <CardContent>
                          <div className="text-2xl font-bold">
                            {formatCurrency(brandSalesData.reduce((sum, item) => sum + item.valor, 0))}
                          </div>
                        </CardContent>
                      </Card>
                      
                      <Card>
                        <CardHeader className="pb-2">
                          <CardTitle className="text-sm font-medium text-muted-foreground">Quantidade Total</CardTitle>
                        </CardHeader>
                        <CardContent>
                          <div className="text-2xl font-bold">
                            {brandSalesData.reduce((sum, item) => sum + item.quantidade, 0)}
                          </div>
                        </CardContent>
                      </Card>
                      
                      <Card>
                        <CardHeader className="pb-2">
                          <CardTitle className="text-sm font-medium text-muted-foreground">Dias com Vendas</CardTitle>
                        </CardHeader>
                        <CardContent>
                          <div className="text-2xl font-bold">
                            {brandSalesData.length}
                          </div>
                        </CardContent>
                      </Card>
                    </div>

                    <Card>
                      <CardHeader>
                        <CardTitle>Histórico de Vendas - {selectedBrand}</CardTitle>
                        <CardDescription>Evolução diária de vendas e quantidade</CardDescription>
                      </CardHeader>
                      <CardContent>
                        <ResponsiveContainer width="100%" height={400}>
                          <LineChart data={brandSalesData}>
                            <CartesianGrid strokeDasharray="3 3" />
                            <XAxis 
                              dataKey="data" 
                              tickFormatter={(value) => {
                                const date = new Date(value)
                                return `${date.getDate()}/${date.getMonth() + 1}`
                              }}
                            />
                            <YAxis yAxisId="left" />
                            <YAxis yAxisId="right" orientation="right" />
                            <Tooltip 
                              formatter={(value: any, name: string) => {
                                if (name === 'valor') return formatCurrency(value)
                                return value
                              }}
                              labelFormatter={(label) => {
                                const date = new Date(label)
                                return date.toLocaleDateString('pt-BR')
                              }}
                            />
                            <Legend />
                            <Line 
                              yAxisId="left"
                              type="monotone" 
                              dataKey="valor" 
                              stroke="#8884d8" 
                              strokeWidth={2}
                              name="Valor (R$)"
                              dot={{ r: 4 }}
                              activeDot={{ r: 6 }}
                            />
                            <Line 
                              yAxisId="right"
                              type="monotone" 
                              dataKey="quantidade" 
                              stroke="#82ca9d" 
                              strokeWidth={2}
                              name="Quantidade"
                              dot={{ r: 4 }}
                              activeDot={{ r: 6 }}
                            />
                          </LineChart>
                        </ResponsiveContainer>
                      </CardContent>
                    </Card>
                  </div>
                )}

                {selectedBrand && brandSalesData.length === 0 && (
                  <div className="text-center py-12 text-muted-foreground">
                    <TrendingUp className="h-12 w-12 mx-auto mb-4 opacity-50" />
                    <p>Nenhum dado de vendas encontrado para esta marca no período selecionado.</p>
                  </div>
                )}

                {!selectedBrand && (
                  <div className="text-center py-12 text-muted-foreground">
                    <TrendingUp className="h-12 w-12 mx-auto mb-4 opacity-50" />
                    <p>Selecione uma marca para visualizar o histórico de vendas.</p>
                  </div>
                )}
              </CardContent>
            </Card>
          </TabsContent>
        </Tabs>
      </div>
    </div>
  )
}
