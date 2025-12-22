<?php
/**
 * Teste de Sessão - Debug
 */

require_once __DIR__ . '/core/Session.php';

Session::init();

echo "<h1>🔍 Teste de Sessão</h1>";
echo "<style>body { font-family: monospace; background: #1e293b; color: #e2e8f0; padding: 20px; } h1 { color: #60a5fa; } .ok { color: #10b981; } .error { color: #ef4444; } pre { background: #0f172a; padding: 15px; border-radius: 8px; } </style>";

echo "<h2>Status da Sessão:</h2>";

if (Session::isValid()) {
    echo "<p class='ok'>✅ Sessão VÁLIDA - Usuário está autenticado</p>";
} else {
    echo "<p class='error'>❌ Sessão INVÁLIDA - Usuário NÃO está autenticado</p>";
}

echo "<h2>Variáveis de Sessão:</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h2>Verificações Específicas:</h2>";

echo "<p><strong>Session::check('Config.database'):</strong> ";
if (Session::check('Config.database')) {
    echo "<span class='ok'>✅ SIM</span>";
    echo "<pre>";
    print_r(Session::read('Config.database'));
    echo "</pre>";
} else {
    echo "<span class='error'>❌ NÃO - Empresa não selecionada</span>";
}
echo "</p>";

echo "<p><strong>Session::check('Config.empresa'):</strong> ";
if (Session::check('Config.empresa')) {
    echo "<span class='ok'>✅ SIM - " . Session::read('Config.empresa') . "</span>";
} else {
    echo "<span class='error'>❌ NÃO</span>";
}
echo "</p>";

echo "<h2>Links de Teste:</h2>";
echo "<p><a href='/relatorios/index' style='color: #60a5fa;'>➜ Ir para Dashboard Principal</a></p>";
echo "<p><a href='/marcasvendas/dashboard' style='color: #60a5fa;'>➜ Ir para Dashboard de Marcas</a></p>";
echo "<p><a href='/relatorios/empresa' style='color: #60a5fa;'>➜ Selecionar Empresa</a></p>";
