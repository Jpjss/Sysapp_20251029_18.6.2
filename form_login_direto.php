<!DOCTYPE html>
<html>
<head>
    <title>Teste de Login Direto</title>
    <style>
        body { font-family: Arial; padding: 40px; background: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #333; margin-top: 0; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
        button { background: var(--accent-1); color: white; padding: 12px 30px; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; width: 100%; }
        button:hover { background: #5568d3; }
        .result { margin-top: 20px; padding: 15px; border-radius: 4px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🔐 Teste de Login - Formulário Direto</h2>
        
        <div class="info">
            <strong>ℹ️ Este formulário envia diretamente para o controller</strong><br>
            Vai ajudar a identificar se o problema é no JavaScript ou no servidor.
        </div>
        
        <form method="POST" action="http://localhost:8000/usuarios/login">
            <div class="form-group">
                <label>Usuário:</label>
                <input type="text" name="email" value="teste" required>
            </div>
            
            <div class="form-group">
                <label>Senha:</label>
                <input type="password" name="senha" value="teste123" required>
            </div>
            
            <button type="submit">🔑 Fazer Login</button>
        </form>
        
        <div style="margin-top: 30px; padding: 15px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px;">
            <strong>⚠️ Importante:</strong><br>
            - Os campos já estão preenchidos com: <code>teste</code> / <code>teste123</code><br>
            - Apenas clique no botão "Fazer Login"<br>
            - Se funcionar, você será redirecionado para o dashboard<br>
            - Se falhar, voltará para a tela de login
        </div>
        
        <?php
        // Mostra mensagem de sessão se houver
        session_start();
        if (isset($_SESSION['flash'])) {
            $tipo = $_SESSION['flash']['type'] ?? 'info';
            $msg = $_SESSION['flash']['message'] ?? '';
            echo "<div class='result $tipo' style='margin-top: 20px;'>";
            echo "<strong>" . ($tipo === 'error' ? '❌' : '✅') . " Mensagem:</strong><br>";
            echo htmlspecialchars($msg);
            echo "</div>";
            unset($_SESSION['flash']);
        }
        ?>
    </div>
</body>
</html>
