<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Teste Login - Usuario teste</title>
    <style>
        body { font-family: Arial; padding: 40px; background: #f0f0f0; }
        .box { background: white; padding: 30px; border-radius: 8px; max-width: 500px; margin: 0 auto; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { margin-top: 0; color: #333; }
        .info { background: #e3f2fd; padding: 15px; border-radius: 4px; margin: 15px 0; }
        .success { background: #c8e6c9; padding: 15px; border-radius: 4px; margin: 15px 0; color: #2e7d32; }
        .error { background: #ffcdd2; padding: 15px; border-radius: 4px; margin: 15px 0; color: #c62828; }
        a { display: inline-block; margin-top: 20px; padding: 10px 20px; background: var(--accent-1); color: white; text-decoration: none; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="box">
        <h2>🔐 Teste de Login - Usuário "teste"</h2>
        
        <?php
        session_start();
        
        define('BASE_PATH', __DIR__);
        define('BASE_URL', 'http://localhost:8000');
        define('SECURITY_SALT', 'DYhG93b0qyJfIxfs2guVoUubWwvniR2G0FgaC9mi');
        
        require_once 'config/database.php';
        require_once 'models/Usuario.php';
        
        $Usuario = new Usuario();
        
        echo "<div class='info'>";
        echo "<strong>Dados do Teste:</strong><br>";
        echo "Login: <code>teste</code><br>";
        echo "Senha: <code>teste123</code>";
        echo "</div>";
        
        // Busca usuário
        $configUser = $Usuario->findByLogin('teste');
        
        if (!$configUser) {
            echo "<div class='error'>❌ Usuário não encontrado com login 'teste'</div>";
            exit;
        }
        
        echo "<div class='success'>✅ Usuário encontrado! ID: {$configUser['cd_usuario']}</div>";
        
        $cd_usuario = $configUser['cd_usuario'];
        
        // Busca dados completos
        $usuario = $Usuario->findForAuth($cd_usuario);
        
        if (!$usuario) {
            echo "<div class='error'>❌ Erro ao buscar dados do usuário</div>";
            exit;
        }
        
        echo "<div class='success'>✅ Dados do usuário carregados</div>";
        echo "<div class='info'>";
        echo "<strong>Informações:</strong><br>";
        echo "ID: {$usuario['cd_usuario']}<br>";
        echo "Nome: {$usuario['nome_usuario']}<br>";
        echo "Senha no banco: {$usuario['senha_usuario']}";
        echo "</div>";
        
        // Verifica senha
        if ('teste123' !== $usuario['senha_usuario']) {
            echo "<div class='error'>❌ Senha incorreta! 'teste123' ≠ '{$usuario['senha_usuario']}'</div>";
            exit;
        }
        
        echo "<div class='success'>✅ Senha correta!</div>";
        
        // Busca empresas
        $empresas = $Usuario->getEmpresas($cd_usuario);
        
        if (empty($empresas)) {
            echo "<div class='error'>❌ Usuário sem empresas vinculadas</div>";
            exit;
        }
        
        echo "<div class='success'>✅ Empresas: " . count($empresas) . "</div>";
        
        // Busca permissões
        $permissoes = $Usuario->getPermissoes($cd_usuario);
        
        if (empty($permissoes)) {
            echo "<div class='error'>❌ Usuário sem permissões</div>";
            exit;
        }
        
        echo "<div class='success'>✅ Permissões: " . implode(', ', $permissoes) . "</div>";
        
        echo "<div class='success' style='margin-top: 30px; font-size: 18px; font-weight: bold;'>";
        echo "🎉 TODOS OS TESTES PASSARAM!<br><br>";
        echo "O usuário 'teste' está configurado corretamente e DEVE conseguir fazer login.";
        echo "</div>";
        
        echo "<div class='info' style='margin-top: 20px;'>";
        echo "<strong>Para fazer login:</strong><br>";
        echo "1. Acesse: <a href='http://localhost:8000/usuarios/login' target='_blank'>Tela de Login</a><br>";
        echo "2. Digite: <code>teste</code> / <code>teste123</code><br>";
        echo "3. Clique em Entrar";
        echo "</div>";
        ?>
        
        <a href="<?= BASE_URL ?>/usuarios/login">🔑 Ir para Tela de Login</a>
    </div>
</body>
</html>
