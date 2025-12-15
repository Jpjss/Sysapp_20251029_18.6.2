'use client'

import { usePathname, useRouter } from 'next/navigation'
import { useEffect, useState } from 'react'
import { authApi } from '@/lib/api/auth'

export function MainLayout({ children }: { children: React.ReactNode }) {
  const pathname = usePathname()
  const router = useRouter()
  const [user, setUser] = useState<any>(null)
  const [loading, setLoading] = useState(true)
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false)

  // Páginas públicas que não exigem autenticação
  const publicPages = ['/login']
  const isPublicPage = publicPages.includes(pathname)

  useEffect(() => {
    async function checkAuth() {
      if (isPublicPage) {
        setLoading(false)
        return
      }

      try {
        const response = await authApi.checkSession()
        if (response.authenticated) {
          setUser(response.user)
          
          // Verifica se tem empresa selecionada (exceto na página de escolher empresa)
          if (pathname !== '/escolher-empresa') {
            try {
              const empresaResponse = await fetch(`${process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000/api'}/empresas/atual`, {
                credentials: 'include',
              })
              
              if (empresaResponse.ok) {
                const data = await empresaResponse.json()
                if (!data.empresa) {
                  router.push('/escolher-empresa')
                  return
                }
              } else {
                router.push('/escolher-empresa')
                return
              }
            } catch (error) {
              console.log('Nenhuma empresa selecionada, redirecionando...')
              router.push('/escolher-empresa')
              return
            }
          }
        } else {
          router.push('/login')
        }
      } catch (error) {
        router.push('/login')
      } finally {
        setLoading(false)
      }
    }

    checkAuth()
  }, [pathname, isPublicPage, router])

  const handleLogout = async () => {
    await authApi.logout()
    router.push('/login')
  }

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-gray-50">
        <div className="text-center">
          <div className="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600"></div>
          <p className="mt-4 text-gray-600">Carregando...</p>
        </div>
      </div>
    )
  }

  // Se for página pública, renderiza apenas o conteúdo
  if (isPublicPage) {
    return <>{children}</>
  }

  // Se não tiver usuário autenticado, não renderiza nada (redirecionamento em andamento)
  if (!user) {
    return null
  }

  return (
    <div className="min-h-screen flex flex-col bg-gray-50">
      {/* Header Moderno */}
      <header className="sticky top-0 z-50 bg-gradient-to-r from-purple-600 to-purple-800 shadow-xl">
        <div className="max-w-screen-2xl mx-auto px-6">
          <div className="flex items-center justify-between gap-8">
            {/* Logo */}
            <div className="flex items-center gap-3 py-4">
              <a href="/dashboard" className="flex items-center gap-3 hover:opacity-90 transition-opacity">
                <div className="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="2">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                  </svg>
                </div>
                <h1 className="text-2xl font-bold text-white">SysApp</h1>
              </a>
            </div>

            {/* Mobile Menu Toggle */}
            <button
              className="lg:hidden p-2 rounded-lg bg-white/10 border border-white/20 text-white"
              onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
            >
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
              </svg>
            </button>

            {/* Navigation Desktop */}
            <nav className="hidden lg:flex items-center gap-2 flex-1">
              <NavLink href="/dashboard" current={pathname}>
                <svg className="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                  <rect x="3" y="3" width="7" height="7"></rect>
                  <rect x="14" y="3" width="7" height="7"></rect>
                  <rect x="14" y="14" width="7" height="7"></rect>
                  <rect x="3" y="14" width="7" height="7"></rect>
                </svg>
                Dashboard
              </NavLink>
              <NavLink href="/clientes" current={pathname}>
                <svg className="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                  <circle cx="9" cy="7" r="4"></circle>
                  <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                  <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                Clientes
              </NavLink>
              <NavLink href="/questionarios" current={pathname}>
                <svg className="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                  <polyline points="14 2 14 8 20 8"></polyline>
                  <line x1="16" y1="13" x2="8" y2="13"></line>
                  <line x1="16" y1="17" x2="8" y2="17"></line>
                </svg>
                Questionários
              </NavLink>
              <NavLink href="/atendimentos" current={pathname}>
                <svg className="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                  <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                  <line x1="16" y1="2" x2="16" y2="6"></line>
                  <line x1="8" y1="2" x2="8" y2="6"></line>
                  <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                Atendimentos
              </NavLink>
              <NavLink href="/relatorios" current={pathname}>
                <svg className="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                  <line x1="18" y1="20" x2="18" y2="10"></line>
                  <line x1="12" y1="20" x2="12" y2="4"></line>
                  <line x1="6" y1="20" x2="6" y2="14"></line>
                </svg>
                Relatórios
              </NavLink>
              <NavLink href="/xml" current={pathname}>
                <svg className="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                  <polyline points="14 2 14 8 20 8"></polyline>
                </svg>
                Correção XML
              </NavLink>
              <NavLink href="/usuarios" current={pathname}>
                <svg className="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                  <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                  <circle cx="8.5" cy="7" r="4"></circle>
                  <line x1="20" y1="8" x2="20" y2="14"></line>
                  <line x1="23" y1="11" x2="17" y2="11"></line>
                </svg>
                Usuários
              </NavLink>
            </nav>

            {/* User Menu */}
            <div className="hidden lg:block relative group">
              <div className="flex items-center gap-3 px-4 py-2 bg-white/10 rounded-xl border border-white/20 backdrop-blur-sm cursor-pointer hover:bg-white/15 transition-colors">
                <div className="w-8 h-8 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                  {user?.nome?.charAt(0).toUpperCase()}
                </div>
                <div className="flex flex-col">
                  <span className="text-sm font-semibold text-white leading-tight">{user?.nome}</span>
                  <span className="text-xs text-white/70 leading-tight">Administrador</span>
                </div>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="2">
                  <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
              </div>
              
              {/* Dropdown Menu */}
              <div className="absolute top-full right-0 mt-2 w-56 bg-white rounded-xl shadow-2xl border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 p-2">
                <a href="/usuarios/trocar-senha" className="flex items-center gap-3 px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors text-sm">
                  <svg className="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                  </svg>
                  Trocar Senha
                </a>
                <a href="/usuarios/adicionar-database" className="flex items-center gap-3 px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors text-sm">
                  <svg className="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                    <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                    <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                    <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                  </svg>
                  Adicionar Database
                </a>
                <hr className="my-2 border-gray-200" />
                <button 
                  onClick={handleLogout}
                  className="flex items-center gap-3 px-3 py-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors text-sm w-full text-left"
                >
                  <svg className="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                  </svg>
                  Sair
                </button>
              </div>
            </div>
          </div>

          {/* Mobile Navigation */}
          {mobileMenuOpen && (
            <nav className="lg:hidden pb-4 space-y-1">
              <MobileNavLink href="/dashboard" current={pathname} onClick={() => setMobileMenuOpen(false)}>Dashboard</MobileNavLink>
              <MobileNavLink href="/clientes" current={pathname} onClick={() => setMobileMenuOpen(false)}>Clientes</MobileNavLink>
              <MobileNavLink href="/questionarios" current={pathname} onClick={() => setMobileMenuOpen(false)}>Questionários</MobileNavLink>
              <MobileNavLink href="/atendimentos" current={pathname} onClick={() => setMobileMenuOpen(false)}>Atendimentos</MobileNavLink>
              <MobileNavLink href="/relatorios" current={pathname} onClick={() => setMobileMenuOpen(false)}>Relatórios</MobileNavLink>
              <MobileNavLink href="/xml" current={pathname} onClick={() => setMobileMenuOpen(false)}>Correção XML</MobileNavLink>
              <MobileNavLink href="/usuarios" current={pathname} onClick={() => setMobileMenuOpen(false)}>Usuários</MobileNavLink>
              <hr className="my-2 border-white/20" />
              <button 
                onClick={handleLogout}
                className="w-full text-left px-4 py-3 text-white/90 hover:bg-white/15 rounded-lg transition-colors text-sm"
              >
                Sair
              </button>
            </nav>
          )}
        </div>
      </header>

      {/* Main Content */}
      <main className="flex-1">
        <div className="max-w-screen-2xl mx-auto px-6 py-8">
          {children}
        </div>
      </main>

      {/* Footer */}
      <footer className="bg-white border-t border-gray-200 py-6 mt-auto">
        <div className="max-w-screen-2xl mx-auto px-6">
          <p className="text-center text-gray-600 text-sm">
            &copy; {new Date().getFullYear()} SysApp - Versão 18.6.2
          </p>
        </div>
      </footer>
    </div>
  )
}

function NavLink({ href, current, children }: { href: string; current: string; children: React.ReactNode }) {
  const isActive = current === href || current.startsWith(href + '/')
  
  return (
    <a
      href={href}
      className={`flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all ${
        isActive
          ? 'bg-white/20 text-white'
          : 'text-white/90 hover:bg-white/15 hover:text-white'
      }`}
    >
      {children}
    </a>
  )
}

function MobileNavLink({ href, current, onClick, children }: { href: string; current: string; onClick: () => void; children: React.ReactNode }) {
  const isActive = current === href || current.startsWith(href + '/')
  
  return (
    <a
      href={href}
      onClick={onClick}
      className={`block px-4 py-3 rounded-lg text-sm font-medium transition-colors ${
        isActive
          ? 'bg-white/20 text-white'
          : 'text-white/90 hover:bg-white/15'
      }`}
    >
      {children}
    </a>
  )
}
