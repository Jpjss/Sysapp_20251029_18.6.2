"use client"

import { useEffect, useState } from "react"
import { useRouter } from "next/navigation"
import { empresasApi } from "@/lib/api/empresas"
import { authApi, type Empresa } from "@/lib/api/auth"
import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { useToast } from "@/components/ui/use-toast"
import { Building2 } from "lucide-react"

export default function EscolherEmpresaPage() {
  const router = useRouter()
  const { toast } = useToast()
  const [empresas, setEmpresas] = useState<Empresa[]>([])
  const [isLoading, setIsLoading] = useState(true)
  const [selectedId, setSelectedId] = useState<number | null>(null)

  useEffect(() => {
    async function loadEmpresas() {
      try {
        const sessionResponse = await authApi.checkSession()
        
        if (!sessionResponse.authenticated) {
          router.push('/login')
          return
        }
        
        const response = await empresasApi.listar()
        setEmpresas(response.empresas)
      } catch (error: any) {
        toast({
          title: "Erro ao carregar empresas",
          description: error.message,
          variant: "destructive",
        })
      } finally {
        setIsLoading(false)
      }
    }

    loadEmpresas()
  }, [router, toast])

  const handleSelectEmpresa = async (empresaId: number) => {
    setSelectedId(empresaId)
    
    try {
      await empresasApi.selecionar(empresaId)
      toast({
        title: "Empresa selecionada!",
        description: "Redirecionando para o dashboard...",
      })
      router.push('/dashboard')
    } catch (error: any) {
      toast({
        title: "Erro ao selecionar empresa",
        description: error.message,
        variant: "destructive",
      })
      setSelectedId(null)
    }
  }

  if (isLoading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="text-center">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mx-auto"></div>
          <p className="mt-4 text-muted-foreground">Carregando empresas...</p>
        </div>
      </div>
    )
  }

  return (
    <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-primary/20 to-background p-4">
      <Card className="w-full max-w-2xl">
        <CardHeader>
          <CardTitle className="text-2xl">Escolha uma Empresa</CardTitle>
          <CardDescription>
            Selecione a empresa que deseja acessar
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div className="grid gap-4">
            {empresas.map((empresa) => (
              <Card
                key={empresa.id}
                className="cursor-pointer hover:border-primary transition-colors"
                onClick={() => handleSelectEmpresa(empresa.id)}
              >
                <CardContent className="flex items-center justify-between p-6">
                  <div className="flex items-center gap-4">
                    <div className="p-3 bg-primary/10 rounded-lg">
                      <Building2 className="h-6 w-6 text-primary" />
                    </div>
                    <div>
                      <h3 className="font-semibold text-lg">{empresa.nm_empresa}</h3>
                      <p className="text-sm text-muted-foreground">
                        {empresa.database} • {empresa.host}:{empresa.porta}
                      </p>
                    </div>
                  </div>
                  <Button
                    disabled={selectedId === empresa.id}
                    onClick={(e) => {
                      e.stopPropagation()
                      handleSelectEmpresa(empresa.id)
                    }}
                  >
                    {selectedId === empresa.id ? "Selecionando..." : "Selecionar"}
                  </Button>
                </CardContent>
              </Card>
            ))}
          </div>
        </CardContent>
      </Card>
    </div>
  )
}
