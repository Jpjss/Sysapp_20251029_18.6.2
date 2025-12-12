"use client"

import { useState } from "react"
import { useToast } from "@/components/ui/use-toast"

export default function CreateDatabasePage() {
  const [dbName, setDbName] = useState("")
  const [isLoading, setIsLoading] = useState(false)
  const { toast } = useToast()

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    if (!dbName) {
      toast({
        title: "Erro de Validação",
        description: "Por favor, insira um nome para o banco de dados.",
        variant: "destructive",
      })
      return
    }

    setIsLoading(true)

    try {
      const formData = new FormData();
      // CakePHP espera os dados aninhados em um array com o nome do Model (ou 'data' genérico)
      formData.append('data[dbName]', dbName);

      const response = await fetch("/api/admin/admin/create_database", {
        method: "POST",
        body: formData,
      });

      const result = await response.json();

      if (!response.ok) {
        throw new Error(result.error || "Ocorreu um erro desconhecido.");
      }

      toast({
        title: "Sucesso!",
        description: result.success,
      })
      setDbName("") // Limpa o campo após o sucesso

    } catch (error: any) {
      toast({
        title: "Erro ao Criar Banco de Dados",
        description: error.message,
        variant: "destructive",
      })
    } finally {
      setIsLoading(false)
    }
  }

  return (
    <div className="container mx-auto p-4">
      <h1 className="text-2xl font-bold mb-4">Criar Novo Banco de Dados de Cliente</h1>
      <form onSubmit={handleSubmit} className="max-w-md">
        <div className="mb-4">
          <label htmlFor="dbName" className="block text-sm font-medium text-gray-700 dark:text-gray-300">
            Nome do Banco de Dados
          </label>
          <input
            type="text"
            id="dbName"
            value={dbName}
            onChange={(e) => setDbName(e.target.value)}
            className="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            placeholder="ex: cliente_novo_123"
            disabled={isLoading}
          />
        </div>
        <button
          type="submit"
          className="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
          disabled={isLoading}
        >
          {isLoading ? "Criando..." : "Criar Banco de Dados"}
        </button>
      </form>
    </div>
  )
}
