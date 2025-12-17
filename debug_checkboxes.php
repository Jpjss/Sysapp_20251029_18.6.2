<?php
define('BASE_PATH', __DIR__);
define('BASE_URL', 'http://localhost:8000');
define('DB_HOST', 'localhost');
define('DB_NAME', 'sysapp');
define('DB_USER', 'postgres');
define('DB_PASS', 'postgres');
define('DB_PORT', '5432');

require_once 'config/database.php';
require_once 'models/Usuario.php';
require_once 'models/Empresa.php';

$Usuario = new Usuario();
$Empresa = new Empresa();

// Simula edição do usuário admin
$cd_usuario = 1;
$usuario = $Usuario->findById($cd_usuario);
$empresas = $Empresa->listarTodas();

$empUsuario = $Usuario->getEmpresas($cd_usuario);
$empresas_usuario = [];
foreach ($empUsuario as $emp) {
    $empresas_usuario[] = $emp['cd_empresa'];
}

$permissoes_usuario = $Usuario->getPermissoes($cd_usuario);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Debug - Formulário de Usuário</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .card { background: white; padding: 24px; border-radius: 8px; max-width: 1200px; margin: 0 auto; }
        .section-divider { margin: 32px 0 20px; padding-bottom: 12px; border-bottom: 2px solid #e2e8f0; }
        .checkbox-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 12px; margin-bottom: 24px; }
        .checkbox-card { display: flex; gap: 12px; padding: 16px; border: 2px solid #e2e8f0; border-radius: 8px; cursor: pointer; }
        .checkbox-card input[type="checkbox"] { width: 20px; height: 20px; margin-top: 2px; }
        .checkbox-content { flex: 1; }
        .checkbox-content strong { display: block; font-size: 14px; margin-bottom: 4px; }
        .checkbox-content small { display: block; font-size: 12px; color: #64748b; }
        .debug { background: #fef2f2; border: 2px solid #fecaca; padding: 16px; margin-bottom: 20px; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>DEBUG - Formulário de Usuário</h1>
        
        <div class="debug">
            <strong>DEBUG INFO:</strong><br>
            Empresas disponíveis: <?= count($empresas) ?><br>
            Empresas do usuário: <?= implode(', ', $empresas_usuario) ?><br>
            Permissões do usuário: <?= implode(', ', $permissoes_usuario) ?>
        </div>
        
        <form method="POST">
            <!-- SELEÇÃO DE EMPRESAS -->
            <div class="section-divider">
                <h3>Empresas com Acesso</h3>
                <p>Selecione as empresas que este usuário poderá acessar</p>
            </div>

            <div class="checkbox-grid">
                <?php if (!empty($empresas)): ?>
                    <?php foreach ($empresas as $empresa): ?>
                        <label class="checkbox-card">
                            <input type="checkbox" 
                                   name="empresas[]" 
                                   value="<?= $empresa['cd_empresa'] ?>"
                                   <?= in_array($empresa['cd_empresa'], $empresas_usuario ?? []) ? 'checked' : '' ?>>
                            <div class="checkbox-content">
                                <strong><?= htmlspecialchars($empresa['nm_empresa']) ?></strong>
                                <small><?= htmlspecialchars($empresa['ds_host']) ?> / <?= htmlspecialchars($empresa['ds_banco']) ?></small>
                            </div>
                        </label>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-data">❌ Nenhuma empresa cadastrada.</p>
                <?php endif; ?>
            </div>

            <!-- SELEÇÃO DE PERMISSÕES -->
            <div class="section-divider">
                <h3>Permissões do Sistema</h3>
                <p>Selecione as funcionalidades que o usuário poderá acessar</p>
            </div>

            <div class="checkbox-grid">
                <?php 
                $permissoes_disponiveis = [
                    'relatorios' => ['nome' => 'Dashboard e Relatórios', 'desc' => 'Visualizar dashboard e relatórios gerenciais'],
                    'clientes' => ['nome' => 'Clientes', 'desc' => 'Gerenciar cadastro de clientes'],
                    'questionarios' => ['nome' => 'Questionários', 'desc' => 'Realizar questionários e atendimentos'],
                    'admin' => ['nome' => 'Administração', 'desc' => 'Acesso ao painel administrativo'],
                    'usuarios' => ['nome' => 'Usuários', 'desc' => 'Gerenciar usuários do sistema'],
                    'empresas' => ['nome' => 'Empresas', 'desc' => 'Gerenciar empresas cadastradas']
                ];
                ?>
                <?php foreach ($permissoes_disponiveis as $key => $perm): ?>
                    <label class="checkbox-card">
                        <input type="checkbox" 
                               name="permissoes[]" 
                               value="<?= $key ?>"
                               <?= in_array($key, $permissoes_usuario ?? []) ? 'checked' : '' ?>>
                        <div class="checkbox-content">
                            <strong><?= $perm['nome'] ?></strong>
                            <small><?= $perm['desc'] ?></small>
                        </div>
                    </label>
                <?php endforeach; ?>
            </div>
            
            <button type="submit">Salvar</button>
        </form>
    </div>
</body>
</html>
