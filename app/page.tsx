"use client"

import { useEffect } from "react"
import { useRouter } from "next/navigation"
import { authApi } from "@/lib/api/auth"

export default function Home() {
  const router = useRouter()

  useEffect(() => {
    async function checkAuth() {
      try {
        const response = await authApi.checkSession()
        
        if (response.authenticated) {
          router.push('/dashboard')
        } else {
          router.push('/login')
        }
      } catch (error) {
        router.push('/login')
      }
    }

    checkAuth()
  }, [router])

  return (
    <div className="min-h-screen flex items-center justify-center">
      <div className="text-center">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mx-auto"></div>
        <p className="mt-4 text-muted-foreground">Carregando...</p>
      </div>
    </div>
  )
}