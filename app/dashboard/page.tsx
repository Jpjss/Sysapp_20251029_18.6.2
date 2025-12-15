"use client"

import { useEffect, useState } from "react"
import { useRouter } from "next/navigation"
import { authApi, type User } from "@/lib/api/auth"
import { empresasApi, type EmpresaAtual } from "@/lib/api/empresas"
import { relatoriosApi, type DashboardStats } from "@/lib/api/relatorios"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Button } from "@/components/ui/button"
import { Users, ClipboardCheck, Clock, TrendingUp, Building2, LogOut } from "lucide-react"
import { useToast } from "@/components/ui/use-toast"
import Link from "next/link"

export default function DashboardPage() {
  const router = useRouter()
  const { toast } = useToast()
  const [user, setUser] = useState<User | null>(null)
  const [empresa, setEmpresa] = useState<EmpresaAtual | null>(null)
  const [stats, setStats] = useState<DashboardStats | null>(null)
  const [isLoading, setIsLoading] = useState(true)

  useEffect(() => {
    async function loadData() {
      try {
        // Verifica autenticação
        const sessionResponse = await authApi.checkSession()
        if (!sessionResponse.authenticated || !sessionResponse.user) {
          router.push('/login')
          return
        }
        setUser(sessionResponse.user)

        // Verifica empresa selecionada
        const empresaResponse = await empresasApi.getAtual()
        if (!empresaResponse.empresa) {
          router.push('/escolher-empresa')
          return
        }
        setEmpresa(empresaResponse.empresa)

        // Carrega estatísticas
        const statsResponse = await relatoriosApi.getDashboard()
        setStats(statsResponse.stats)

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

  const handleLogout = async () => {
    try {
      await authApi.logout()
      router.push('/login')
    } catch (error: any) {
      toast({
        title: "Erro ao fazer logout",
        description: error.message,
        variant: "destructive",
      })
    }
  }

  if (isLoading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="text-center">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mx-auto"></div>
          <p className="mt-4 text-muted-foreground">Carregando dashboard...</p>
        </div>
      </div>
    )
  }

  return (
    <div className="min-h-screen bg-background">
      {/* Header */}
      <header className="border-b bg-card">
        <div className="container mx-auto px-4 py-4 flex items-center justify-between">
          <div>
            <h1 className="text-2xl font-bold">SysApp</h1>
            {empresa && (
              <p className="text-sm text-muted-foreground flex items-center gap-2">
                <Building2 className="h-4 w-4" />
                {empresa.nome}
              </p>
            )}
          </div>
          <div className="flex items-center gap-4">
            <p className="text-sm">
              Olá, <span className="font-semibold">{user?.nome}</span>
            </p>
            <Button variant="outline" size="sm" onClick={handleLogout}>
              <LogOut className="h-4 w-4 mr-2" />
              Sair
            </Button>
          </div>
        </div>
      </header>

      <div className="container mx-auto px-4 py-8">
        {/* Stats Cards */}
        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4 mb-8">
          <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Total de Clientes</CardTitle>
              <Users className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">{stats?.total_clientes || 0}</div>
              <p className="text-xs text-muted-foreground">Clientes ativos</p>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Atendimentos Hoje</CardTitle>
              <ClipboardCheck className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">{stats?.atendimentos_hoje || 0}</div>
              <p className="text-xs text-muted-foreground">Realizados hoje</p>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Atendimentos no Mês</CardTitle>
              <TrendingUp className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">{stats?.atendimentos_mes || 0}</div>
              <p className="text-xs text-muted-foreground">Neste mês</p>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Clientes Pendentes</CardTitle>
              <Clock className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">{stats?.clientes_pendentes || 0}</div>
              <p className="text-xs text-muted-foreground">Sem atendimento (30+ dias)</p>
            </CardContent>
          </Card>
        </div>

        {/* Quick Actions */}
        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
          <Card className="cursor-pointer hover:border-primary transition-colors">
            <Link href="/questionarios">
              <CardHeader>
                <CardTitle>Questionários</CardTitle>
                <CardDescription>
                  Realizar atendimentos e visualizar histórico
                </CardDescription>
              </CardHeader>
            </Link>
          </Card>

          <Card className="cursor-pointer hover:border-primary transition-colors">
            <Link href="/relatorios">
              <CardHeader>
                <CardTitle>Relatórios</CardTitle>
                <CardDescription>
                  Visualizar relatórios de estoque e vendas
                </CardDescription>
              </CardHeader>
            </Link>
          </Card>

          <Card className="cursor-pointer hover:border-primary transition-colors">
            <Link href="/clientes">
              <CardHeader>
                <CardTitle>Clientes</CardTitle>
                <CardDescription>
                  Gerenciar cadastro de clientes
                </CardDescription>
              </CardHeader>
            </Link>
          </Card>
        </div>
      </div>
    </div>
  )
}
