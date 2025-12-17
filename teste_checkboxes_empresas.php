<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Teste - Empresas</title>
    <style>
.checkbox-card {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 16px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    cursor: pointer;
    background: #fff;
    margin-bottom: 10px;
}

.checkbox-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.checkbox-content strong {
    font-size: 14px;
    font-weight: 600;
    color: #1e293b !important;
}

.checkbox-content small {
    font-size: 12px;
    color: #64748b !important;
}
    </style>
</head>
<body>
    <h1>Teste de Checkboxes de Empresas</h1>
    
    <?php
    require_once 'config/database.php';
    require_once 'models/Empresa.php';
    
    $Empresa = new Empresa();
    $empresas = $Empresa->listarTodas();
    
    if (!empty($empresas)):
        foreach ($empresas as $empresa):
    ?>
        <label class="checkbox-card">
            <input type="checkbox" 
                   name="empresas[]" 
                   value="<?= $empresa['cd_empresa'] ?>">
            <div class="checkbox-content">
                <strong><?= htmlspecialchars($empresa['nm_empresa'] ?? 'Sem nome') ?></strong>
                <small><?= htmlspecialchars($empresa['ds_host'] ?? '') ?> / <?= htmlspecialchars($empresa['ds_banco'] ?? '') ?></small>
            </div>
        </label>
    <?php 
        endforeach;
    else:
    ?>
        <p>Nenhuma empresa cadastrada</p>
    <?php endif; ?>
    
</body>
</html>
