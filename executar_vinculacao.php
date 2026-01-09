<?php
/**
 * Testa vinculação e exibe resultado
 */

// Executa o script de vinculação
ob_start();
include 'vincular_empresas_automatico.php';
$resultado = ob_get_clean();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Vinculação de Empresas</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--accent-1) 0%, var(--accent-3) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 12px;
            padding: 40px;
            max-width: 600px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }
        h1 {
            color: #2c3e50;
            margin-top: 0;
        }
        .result {
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            font-size: 16px;
            font-weight: 500;
        }
        .success {
            background: #d4edda;
            border-left: 4px solid #28a745;
            color: #155724;
        }
        .info {
            background: #d1ecf1;
            border-left: 4px solid #17a2b8;
            color: #0c5460;
        }
        .error {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            color: #721c24;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: var(--accent-1);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            margin-top: 20px;
            transition: all 0.3s;
        }
        .btn:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        .btn-logout {
            background: #dc3545;
            margin-left: 10px;
        }
        .btn-logout:hover {
            background: #c82333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔗 Vinculação de Empresas</h1>
        
        <?php if (strpos($resultado, 'SUCESSO') !== false): ?>
            <div class="result success">
                ✅ <?= htmlspecialchars($resultado) ?>
            </div>
            <p><strong>⚠️ Importante:</strong> Faça logout e login novamente para ver as novas empresas!</p>
            <a href="usuarios/logout" class="btn btn-logout">🚪 Fazer Logout</a>
        <?php elseif (strpos($resultado, 'OK') !== false): ?>
            <div class="result info">
                ℹ️ <?= htmlspecialchars($resultado) ?>
            </div>
        <?php else: ?>
            <div class="result error">
                ❌ <?= htmlspecialchars($resultado) ?>
            </div>
        <?php endif; ?>
        
        <a href="relatorios/index" class="btn">📊 Ir para o Dashboard</a>
    </div>
</body>
</html>
