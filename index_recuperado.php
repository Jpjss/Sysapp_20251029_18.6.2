<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>SysApp - Portal de Acesso</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .container { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); max-width: 500px; width: 90%; }
        h1 { color: #333; margin-bottom: 10px; }
        p { color: #666; margin-bottom: 30px; }
        .info-box { background: #f0f0f0; padding: 15px; border-left: 4px solid #667eea; margin-bottom: 20px; border-radius: 4px; }
        .info-box strong { color: #333; }
        .info-box code { background: #fff; padding: 2px 6px; border-radius: 3px; font-family: monospace; color: #e74c3c; }
        .buttons { display: flex; gap: 10px; }
        a { display: block; padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; text-align: center; font-weight: bold; border: none; cursor: pointer; }
        a:hover { background: #5a67d8; }
        .secondary { background: #95a5a6; }
        .secondary:hover { background: #7f8c8d; }
        .status { padding: 10px; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 4px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎉 SysApp Restaurado!</h1>
        <p>O sistema foi restaurado e está funcionando normalmente.</p>
        
        <div class="status">
            ✅ Servidor: Ativo  
            ✅ Banco de Dados: Conectado  
            ✅ Usuários: Configurados
        </div>
        
        <div class="info-box">
            <strong>Credenciais de Acesso:</strong><br><br>
            Login: <code>admin</code><br>
            Senha: <code>admin123</code>
        </div>
        
        <div class="info-box">
            <strong>Endereços Úteis:</strong><br><br>
            🔗 <a href="http://localhost:8000/usuarios/login" style="display: inline; padding: 0; background: none; color: #667eea; text-decoration: underline;">Login (http://localhost:8000/usuarios/login)</a><br>
            🔧 <a href="http://localhost:8000/teste_login_direto.php" style="display: inline; padding: 0; background: none; color: #667eea; text-decoration: underline;">Teste de Conexão</a><br>
            📊 <a href="http://localhost:8000/diagnostico.php" style="display: inline; padding: 0; background: none; color: #667eea; text-decoration: underline;">Diagnóstico do Sistema</a>
        </div>
        
        <div class="buttons">
            <a href="/usuarios/login">🔓 Acessar Login</a>
            <a href="/teste_login_direto.php" class="secondary">🔧 Diagnosticar</a>
        </div>
        
        <hr style="margin: 30px 0; border: none; border-top: 1px solid #ddd;">
        
        <p style="font-size: 13px; color: #999;">
            <strong>Resumo das Alterações:</strong><br><br>
            ✓ Habilitado relatório de erros (config/config.php)<br>
            ✓ Corrigido fluxo de login para usuários sem empresas<br>
            ✓ Criado usuário admin automaticamente<br>
            ✓ Nomes de colunas ajustados (ds_login, ds_senha, nm_usuario)<br>
            ✓ Servidor PHP limpo e reiniciado
        </p>
    </div>
</body>
</html>
