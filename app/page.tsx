"use client"

import { ThemeToggle } from "@/components/theme-toggle"

export default function Home() {
  return (
    <div className="min-h-screen flex items-center justify-center">
      <div className="text-center">
        <h1 className="text-4xl font-bold mb-4">SysApp</h1>
        <p className="text-muted-foreground">Sistema de Questionários - Versão 18.6.2</p>
        <div className="mt-4">
          <ThemeToggle />
        </div>
      </div>
    </div>
  )
}