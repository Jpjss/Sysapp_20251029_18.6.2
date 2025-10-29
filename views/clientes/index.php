<div class="page-header">
    <h2>Clientes</h2>
</div>

<div class="card">
    <form method="GET" action="<?= BASE_URL ?>/clientes/index" class="search-form">
        <div class="search-group">
            <input type="text" 
                   name="filtro" 
                   value="<?= htmlspecialchars($filtro) ?>" 
                   placeholder="Buscar por nome ou razão social..."
                   class="form-control">
            <button type="submit" class="btn btn-primary">Buscar</button>
            <?php if ($filtro): ?>
                <a href="<?= BASE_URL ?>/clientes/index" class="btn btn-secondary">Limpar</a>
            <?php endif; ?>
        </div>
    </form>
    
    <table class="table">
        <thead>
            <tr>
                <th>Código</th>
                <th>Nome Fantasia</th>
                <th>Razão Social</th>
                <th>CPF/CNPJ</th>
                <th>Telefone</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($clientes)): ?>
                <?php foreach ($clientes as $cliente): ?>
                    <tr>
                        <td><?= htmlspecialchars($cliente['cd_pessoa']) ?></td>
                        <td><?= htmlspecialchars($cliente['nm_fant']) ?></td>
                        <td><?= htmlspecialchars($cliente['nm_razao'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($cliente['cpf_cnpj'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($cliente['fone'] ?? '-') ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>/clientes/view/<?= $cliente['cd_pessoa'] ?>" 
                               class="btn btn-sm btn-info">Ver</a>
                            <a href="<?= BASE_URL ?>/questionarios/historico/<?= $cliente['cd_pessoa'] ?>" 
                               class="btn btn-sm btn-primary">Histórico</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">Nenhum cliente encontrado</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?><?= $filtro ? '&filtro=' . urlencode($filtro) : '' ?>" 
                   class="page-link <?= $i === $page ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
    
    <div class="table-info">
        Total de registros: <?= $total ?>
    </div>
</div>
