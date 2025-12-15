"use client"

import { useEffect, useState } from "react"
import { useRouter } from "next/navigation"
import { authApi } from "@/lib/api/auth"
import { empresasApi } from "@/lib/api/empresas"
import { relatoriosApi, type ProdutoEstoque, type VendaDia, type TopProduto } from "@/lib/api/relatorios"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs"
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table"
import { useToast } from "@/components/ui/use-toast"
import { Search, ArrowLeft, Package, TrendingUp, Calendar } from "lucide-react"
import Link from "next/link"

export default function RelatoriosPage() {
  const router = useRouter()
  const { toast } = useToast()
  const [produtos, setProdutos] = useState<ProdutoEstoque[]>([])
  const [vendas, setVendas] = useState<VendaDia[]>([])
  const [topProdutos, setTopProdutos] = useState<TopProduto[]>([])
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
        </Tabs>
      </div>
    </div>
  )
}
