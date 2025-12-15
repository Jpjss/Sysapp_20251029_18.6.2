"use client"

import { useEffect, useState } from "react"
import { useRouter } from "next/navigation"
import { authApi } from "@/lib/api/auth"
import { empresasApi } from "@/lib/api/empresas"
import { questionariosApi, type Cliente, type Questionario } from "@/lib/api/questionarios"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs"
import { useToast } from "@/components/ui/use-toast"
import { Search, Phone, Mail, ArrowLeft } from "lucide-react"
import Link from "next/link"

export default function QuestionariosPage() {
  const router = useRouter()
  const { toast } = useToast()
  const [clientes, setClientes] = useState<Cliente[]>([])
  const [questionarios, setQuestionarios] = useState<Questionario[]>([])
  const [historico, setHistorico] = useState<any[]>([])
  const [isLoading, setIsLoading] = useState(true)
  const [searchTerm, setSearchTerm] = useState("")

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

        const [clientesRes, questionariosRes, historicoRes] = await Promise.all([
          questionariosApi.getPendentes(),
          questionariosApi.listar(),
          questionariosApi.getHistorico()
        ])

        setClientes(clientesRes.clientes)
        setQuestionarios(questionariosRes.questionarios)
        setHistorico(historicoRes.historico)
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

  const filteredClientes = clientes.filter(cliente =>
    cliente.nome.toLowerCase().includes(searchTerm.toLowerCase()) ||
    cliente.fantasia?.toLowerCase().includes(searchTerm.toLowerCase())
  )

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
            <h1 className="text-3xl font-bold">Questionários</h1>
            <p className="text-muted-foreground">Gerencie atendimentos de clientes</p>
          </div>
        </div>

        <Tabs defaultValue="pendentes" className="space-y-4">
          <TabsList>
            <TabsTrigger value="pendentes">Clientes Pendentes ({clientes.length})</TabsTrigger>
            <TabsTrigger value="historico">Histórico ({historico.length})</TabsTrigger>
            <TabsTrigger value="questionarios">Questionários ({questionarios.length})</TabsTrigger>
          </TabsList>

          <TabsContent value="pendentes" className="space-y-4">
            <div className="flex gap-4">
              <div className="relative flex-1">
                <Search className="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
                <Input
                  placeholder="Buscar cliente..."
                  value={searchTerm}
                  onChange={(e) => setSearchTerm(e.target.value)}
                  className="pl-10"
                />
              </div>
            </div>

            <div className="grid gap-4">
              {filteredClientes.map((cliente) => (
                <Card key={cliente.id}>
                  <CardContent className="flex items-center justify-between p-6">
                    <div className="space-y-1">
                      <h3 className="font-semibold text-lg">{cliente.nome}</h3>
                      {cliente.fantasia && (
                        <p className="text-sm text-muted-foreground">{cliente.fantasia}</p>
                      )}
                      <div className="flex gap-4 text-sm text-muted-foreground">
                        {cliente.telefone && (
                          <span className="flex items-center gap-1">
                            <Phone className="h-3 w-3" />
                            {cliente.telefone}
                          </span>
                        )}
                        {cliente.email && (
                          <span className="flex items-center gap-1">
                            <Mail className="h-3 w-3" />
                            {cliente.email}
                          </span>
                        )}
                      </div>
                      <p className="text-xs text-muted-foreground">
                        Último atendimento: {cliente.ultimo_atendimento || 'Nunca'}
                      </p>
                    </div>
                    <Link href={`/questionarios/responder?clienteId=${cliente.id}`}>
                      <Button>Atender</Button>
                    </Link>
                  </CardContent>
                </Card>
              ))}
            </div>
          </TabsContent>

          <TabsContent value="historico" className="space-y-4">
            <div className="grid gap-4">
              {historico.map((item) => (
                <Card key={item.id}>
                  <CardContent className="p-6">
                    <div className="flex justify-between items-start">
                      <div>
                        <h3 className="font-semibold">{item.cliente_nome}</h3>
                        <p className="text-sm text-muted-foreground">{item.questionario_titulo}</p>
                        <p className="text-xs text-muted-foreground mt-1">
                          Atendido por: {item.usuario_nome} • {new Date(item.data_atendimento).toLocaleString('pt-BR')}
                        </p>
                      </div>
                    </div>
                  </CardContent>
                </Card>
              ))}
            </div>
          </TabsContent>

          <TabsContent value="questionarios" className="space-y-4">
            <div className="grid gap-4 md:grid-cols-2">
              {questionarios.map((questionario) => (
                <Card key={questionario.id}>
                  <CardHeader>
                    <CardTitle>{questionario.titulo}</CardTitle>
                    <CardDescription>{questionario.descricao}</CardDescription>
                  </CardHeader>
                  <CardContent>
                    <span className={`inline-block px-2 py-1 rounded text-xs ${questionario.ativo ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}`}>
                      {questionario.ativo ? 'Ativo' : 'Inativo'}
                    </span>
                  </CardContent>
                </Card>
              ))}
            </div>
          </TabsContent>
        </Tabs>
      </div>
    </div>
  )
}
