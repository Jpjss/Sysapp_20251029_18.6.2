import Link from "next/link"

export function Sidebar() {
  return (
    <div className="w-64 h-screen bg-gray-100 dark:bg-gray-900 p-4 flex flex-col fixed">
      <h2 className="text-xl font-bold mb-4">SysApp</h2>
      <nav className="flex flex-col space-y-2">
        <Link href="/" className="px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-800">
          Início
        </Link>
        <Link href="/admin/create-database" className="px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-800">
          Criar Banco de Dados
        </Link>
      </nav>
    </div>
  )
}
