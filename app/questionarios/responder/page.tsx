"use client"

import { useEffect, useState, Suspense } from "react"
import { useRouter, useSearchParams } from "next/navigation"
import { authApi } from "@/lib/api/auth"
import { empresasApi } from "@/lib/api/empresas"
import { questionariosApi, type Pergunta, type Questionario } from "@/lib/api/questionarios"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Textarea } from "@/components/ui/textarea"
import { RadioGroup, RadioGroupItem } from "@/components/ui/radio-group"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import { useToast } from "@/components/ui/use-toast"
import { ArrowLeft } from "lucide-react"
import Link from "next/link"

function ResponderQuestionarioContent() {
  const router = useRouter()
  const searchParams = useSearchParams()
  const { toast } = useToast()
  
  const clienteId = searchParams.get('clienteId')
  const [questionarios, setQuestionarios] = useState<Questionario[]>([])
  const [selectedQuestionario, setSelectedQuestionario] = useState<number | null>(null)
  const [perguntas, setPerguntas] = useState<Pergunta[]>([])
  const [respostas, setRespostas] = useState<Record<number, string>>({})
  const [observacao, setObservacao] = useState("")
  const [isLoading, setIsLoading] = useState(true)
  const [isSaving, setIsSaving] = useState(false)

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

        const questionariosRes = await questionariosApi.listar()
        setQuestionarios(questionariosRes.questionarios.filter(q => q.ativo))
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

  useEffect(() => {
    if (selectedQuestionario) {
      loadPerguntas(selectedQuestionario)
    }
  }, [selectedQuestionario])

  const loadPerguntas = async (questionarioId: number) => {
    try {
      const response = await questionariosApi.getPerguntas(questionarioId)
      setPerguntas(response.perguntas)
      setRespostas({})
    } catch (error: any) {
      toast({
        title: "Erro ao carregar perguntas",
        description: error.message,
        variant: "destructive",
      })
    }
  }

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    
    if (!selectedQuestionario || !clienteId) {
      toast({
        title: "Erro",
        description: "Selecione um questionário",
        variant: "destructive",
      })
      return
    }

    // Valida respostas obrigatórias
    const perguntasObrigatorias = perguntas.filter(p => p.obrigatoria)
    const respostasObrigatoriasFaltando = perguntasObrigatorias.filter(
      p => !respostas[p.id] || respostas[p.id].trim() === ''
    )

    if (respostasObrigatoriasFaltando.length > 0) {
      toast({
        title: "Erro de validação",
        description: "Responda todas as perguntas obrigatórias",
        variant: "destructive",
      })
      return
    }

    setIsSaving(true)

    try {
      const respostasArray = Object.entries(respostas).map(([perguntaId, resposta]) => ({
        perguntaId: Number(perguntaId),
        resposta
      }))

      await questionariosApi.responder({
        clienteId: Number(clienteId),
        questionarioId: selectedQuestionario,
        respostas: respostasArray,
        observacao
      })

      toast({
        title: "Sucesso!",
        description: "Atendimento registrado com sucesso",
      })

      router.push('/questionarios')
    } catch (error: any) {
      toast({
        title: "Erro ao salvar atendimento",
        description: error.message,
        variant: "destructive",
      })
    } finally {
      setIsSaving(false)
    }
  }

  const renderPergunta = (pergunta: Pergunta) => {
    const value = respostas[pergunta.id] || ''

    switch (pergunta.tipo_resposta) {
      case 'texto_curto':
        return (
          <Input
            value={value}
            onChange={(e) => setRespostas({ ...respostas, [pergunta.id]: e.target.value })}
            required={pergunta.obrigatoria}
          />
        )

      case 'texto_longo':
        return (
          <Textarea
            value={value}
            onChange={(e) => setRespostas({ ...respostas, [pergunta.id]: e.target.value })}
            required={pergunta.obrigatoria}
            rows={4}
          />
        )

      case 'multipla_escolha':
        const opcoes = pergunta.opcoes ? pergunta.opcoes.split(',') : []
        return (
          <RadioGroup
            value={value}
            onValueChange={(val) => setRespostas({ ...respostas, [pergunta.id]: val })}
            required={pergunta.obrigatoria}
          >
            {opcoes.map((opcao, idx) => (
              <div key={idx} className="flex items-center space-x-2">
                <RadioGroupItem value={opcao.trim()} id={`${pergunta.id}-${idx}`} />
                <Label htmlFor={`${pergunta.id}-${idx}`}>{opcao.trim()}</Label>
              </div>
            ))}
          </RadioGroup>
        )

      case 'selecao':
        const opcoesSelect = pergunta.opcoes ? pergunta.opcoes.split(',') : []
        return (
          <Select
            value={value}
            onValueChange={(val) => setRespostas({ ...respostas, [pergunta.id]: val })}
            required={pergunta.obrigatoria}
          >
            <SelectTrigger>
              <SelectValue placeholder="Selecione uma opção" />
            </SelectTrigger>
            <SelectContent>
              {opcoesSelect.map((opcao, idx) => (
                <SelectItem key={idx} value={opcao.trim()}>
                  {opcao.trim()}
                </SelectItem>
              ))}
            </SelectContent>
          </Select>
        )

      default:
        return (
          <Input
            value={value}
            onChange={(e) => setRespostas({ ...respostas, [pergunta.id]: e.target.value })}
            required={pergunta.obrigatoria}
          />
        )
    }
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
      <div className="container mx-auto px-4 py-8 max-w-3xl">
        <div className="mb-6 flex items-center gap-4">
          <Link href="/questionarios">
            <Button variant="outline" size="icon">
              <ArrowLeft className="h-4 w-4" />
            </Button>
          </Link>
          <div>
            <h1 className="text-3xl font-bold">Responder Questionário</h1>
            <p className="text-muted-foreground">Cliente ID: {clienteId}</p>
          </div>
        </div>

        <form onSubmit={handleSubmit} className="space-y-6">
          <Card>
            <CardHeader>
              <CardTitle>Selecione o Questionário</CardTitle>
            </CardHeader>
            <CardContent>
              <Select
                value={selectedQuestionario?.toString()}
                onValueChange={(val) => setSelectedQuestionario(Number(val))}
                required
              >
                <SelectTrigger>
                  <SelectValue placeholder="Escolha um questionário" />
                </SelectTrigger>
                <SelectContent>
                  {questionarios.map((q) => (
                    <SelectItem key={q.id} value={q.id.toString()}>
                      {q.titulo}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </CardContent>
          </Card>

          {perguntas.length > 0 && (
            <>
              {perguntas.map((pergunta) => (
                <Card key={pergunta.id}>
                  <CardHeader>
                    <CardTitle className="text-base">
                      {pergunta.pergunta}
                      {pergunta.obrigatoria && <span className="text-red-500 ml-1">*</span>}
                    </CardTitle>
                  </CardHeader>
                  <CardContent>
                    {renderPergunta(pergunta)}
                  </CardContent>
                </Card>
              ))}

              <Card>
                <CardHeader>
                  <CardTitle className="text-base">Observações</CardTitle>
                  <CardDescription>Informações adicionais sobre o atendimento (opcional)</CardDescription>
                </CardHeader>
                <CardContent>
                  <Textarea
                    value={observacao}
                    onChange={(e) => setObservacao(e.target.value)}
                    rows={4}
                    placeholder="Digite observações adicionais..."
                  />
                </CardContent>
              </Card>

              <div className="flex gap-4">
                <Link href="/questionarios" className="flex-1">
                  <Button type="button" variant="outline" className="w-full">
                    Cancelar
                  </Button>
                </Link>
                <Button type="submit" className="flex-1" disabled={isSaving}>
                  {isSaving ? "Salvando..." : "Salvar Atendimento"}
                </Button>
              </div>
            </>
          )}
        </form>
      </div>
    </div>
  )
}

export default function ResponderQuestionarioPage() {
  return (
    <Suspense fallback={
      <div className="min-h-screen flex items-center justify-center">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mx-auto"></div>
      </div>
    }>
      <ResponderQuestionarioContent />
    </Suspense>
  )
}
