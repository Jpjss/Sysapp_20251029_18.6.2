<div class="empresa-selection">
    <div class="empresa-box">
        <h2>Selecione a Empresa</h2>
        <p>Você tem acesso a múltiplas empresas. Selecione uma para continuar:</p>
        
        <form method="POST" action="<?= BASE_URL ?>/relatorios/empresa" class="empresa-form">
            <div class="empresas-list">
                <?php foreach ($empresas as $empresa): ?>
                    <label class="empresa-item">
                        <input type="radio" 
                               name="cd_empresa" 
                               value="<?= $empresa['cd_empresa'] ?>" 
                               required>
                        <div class="empresa-info">
                            <strong><?= htmlspecialchars($empresa['nome_empresa']) ?></strong>
                            <small><?= htmlspecialchars($empresa['nome_banco']) ?></small>
                        </div>
                    </label>
                <?php endforeach; ?>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Continuar</button>
        </form>
    </div>
</div>

<style>
.empresa-selection {
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.empresa-box {
    background: white;
    border-radius: 10px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    padding: 40px;
    max-width: 600px;
    width: 100%;
}

.empresa-box h2 {
    margin-bottom: 10px;
    color: #2c3e50;
}

.empresa-box p {
    margin-bottom: 30px;
    color: #666;
}

.empresas-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin-bottom: 30px;
}

.empresa-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    border: 2px solid #ddd;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s;
}

.empresa-item:hover {
    border-color: #667eea;
    background: #f8f9fa;
}

.empresa-item input[type="radio"] {
    cursor: pointer;
}

.empresa-item input[type="radio"]:checked + .empresa-info {
    color: #667eea;
}

.empresa-info {
    flex: 1;
}

.empresa-info strong {
    display: block;
    font-size: 16px;
    margin-bottom: 5px;
}

.empresa-info small {
    color: #666;
    font-size: 13px;
}
</style>
