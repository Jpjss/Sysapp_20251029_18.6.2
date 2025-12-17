<?php
require_once 'config/database.php';
require_once 'models/Empresa.php';

define('BASE_URL', 'http://localhost:8000');

$Empresa = new Empresa();
$empresas = $Empresa->listarTodas();

echo "=== HTML GERADO PARA EMPRESAS ===\n\n";

if (!empty($empresas)) {
    echo "Empresas encontradas: " . count($empresas) . "\n\n";
    
    foreach ($empresas as $empresa) {
        $nome = htmlspecialchars($empresa['nm_empresa'] ?? $empresa['nome_empresa'] ?? 'Sem nome');
        $host = htmlspecialchars($empresa['ds_host'] ?? '');
        $banco = htmlspecialchars($empresa['ds_banco'] ?? $empresa['nome_banco'] ?? '');
        
        echo "Checkbox para empresa CD {$empresa['cd_empresa']}:\n";
        echo "  Nome: '$nome'\n";
        echo "  Host/Banco: '$host / $banco'\n";
        echo "  ---\n";
    }
    
    echo "\n\nHTML renderizado:\n\n";
    
    foreach ($empresas as $empresa) {
?>
                    <label class="checkbox-card">
                        <input type="checkbox" 
                               name="empresas[]" 
                               value="<?= $empresa['cd_empresa'] ?>">
                        <div class="checkbox-content">
                            <strong><?= htmlspecialchars($empresa['nm_empresa'] ?? $empresa['nome_empresa'] ?? 'Sem nome') ?></strong>
                            <small><?= htmlspecialchars($empresa['ds_host'] ?? '') ?> / <?= htmlspecialchars($empresa['ds_banco'] ?? $empresa['nome_banco'] ?? '') ?></small>
                        </div>
                    </label>
<?php
    }
} else {
    echo "❌ Nenhuma empresa cadastrada\n";
}
