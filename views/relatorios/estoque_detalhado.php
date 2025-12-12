<div class="page-header">
    <h2>📦 Relatório de Estoque Detalhado por Família/Grupo</h2>
    <a href="<?= BASE_URL ?>/relatorios/index" class="btn btn-secondary">Voltar</a>
</div>

<div class="card">
    <div class="info-box">
        <h4>📊 Sobre este Relatório</h4>
        <p>Este relatório exibe o estoque detalhado agrupado por Família ou Grupo de produtos, mostrando:</p>
        <ul>
            <li><strong>Custo do Estoque:</strong> Valor total em R$ do estoque</li>
            <li><strong>Quantidade:</strong> Quantidade total de itens em estoque</li>
            <li><strong>Total de SKUs:</strong> Quantidade de produtos diferentes</li>
            <li><strong>Percentuais:</strong> Representatividade sobre o total (quantidade e valor)</li>
        </ul>
    </div>
</div>

<div class="card">
    <form method="POST" action="<?= BASE_URL ?>/relatorios/estoque_detalhado" class="filter-form">
        <div class="form-row">
            <div class="form-group">
                <label for="dt_referencia">Data de Referência:</label>
                <input type="date" 
                       id="dt_referencia" 
                       name="dt_referencia" 
                       value="<?= date('Y-m-d') ?>"
                       class="form-control">
                <small>Data para cálculo do estoque</small>
            </div>
            
            <div class="form-group">
                <label for="tipo_agrupamento">Agrupar por:</label>
                <select name="tipo_agrupamento" id="tipo_agrupamento" class="form-control">
                    <option value="FAMILIA">Família</option>
                    <option value="GRUPO">Grupo</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="ordenacao">Ordenar por:</label>
                <select name="ordenacao" id="ordenacao" class="form-control">
                    <option value="VALOR_DESC">Valor (Maior para Menor)</option>
                    <option value="VALOR_ASC">Valor (Menor para Maior)</option>
                    <option value="QTDE_DESC">Quantidade (Maior para Menor)</option>
                    <option value="QTDE_ASC">Quantidade (Menor para Maior)</option>
                    <option value="NOME">Nome (A-Z)</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="tipo_arquivo">Formato:</label>
                <select name="tipo_arquivo" id="tipo_arquivo" class="form-control">
                    <option value="HTML">Visualizar (HTML)</option>
                    <option value="EXCEL">Exportar (CSV)</option>
                </select>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label><strong>Filiais:</strong></label>
                <div class="checkbox-container" style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 4px;">
                    <label style="display: block; margin-bottom: 10px;">
                        <input type="checkbox" id="select_all_filiais" checked style="margin-right: 8px;">
                        <strong>Marcar/Desmarcar Todas</strong>
                    </label>
                    <hr style="margin: 10px 0;">
                    <?php if (!empty($filiais)): ?>
                        <?php foreach ($filiais as $filial): ?>
                            <label style="display: block; margin-bottom: 5px;">
                                <input type="checkbox" 
                                       name="filiais[]" 
                                       value="<?= $filial['cd_filial'] ?>" 
                                       class="filial-checkbox"
                                       checked
                                       style="margin-right: 8px;">
                                <?= htmlspecialchars($filial['nm_fant']) ?>
                            </label>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="color: #999;">Nenhuma filial encontrada.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>
                    <input type="checkbox" 
                           name="exibir_estoque_zerado" 
                           id="exibir_estoque_zerado"
                           style="margin-right: 8px;">
                    <strong>Incluir categorias com estoque zerado</strong>
                </label>
            </div>
        </div>
        
        <div class="form-row">
            <button type="submit" class="btn btn-primary">📊 Gerar Relatório</button>
        </div>
    </form>
</div>

<script>
document.getElementById('select_all_filiais').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.filial-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
});
</script>

<style>
.info-box {
    background-color: #f0f8ff;
    border: 1px solid #4682b4;
    border-radius: 5px;
    padding: 15px;
    margin-bottom: 20px;
}

.info-box h4 {
    margin-top: 0;
    color: #4682b4;
}

.info-box ul {
    margin-bottom: 0;
}

.form-row {
    display: flex;
    gap: 15px;
    margin-bottom: 15px;
    flex-wrap: wrap;
}

.form-group {
    flex: 1;
    min-width: 200px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
}

.form-control, .form-select {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.checkbox-container label {
    cursor: pointer;
    user-select: none;
}

.checkbox-container label:hover {
    background-color: #f5f5f5;
    padding: 2px 5px;
    border-radius: 3px;
}
</style>
