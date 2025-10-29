<div class="page-header">
    <h2>Usuários Cadastrados</h2>
    <a href="<?= BASE_URL ?>/usuarios/novo" class="btn btn-primary">Novo Usuário</a>
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Código</th>
                <th>Nome</th>
                <th>Login</th>
                <th>Cód. ERP</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($usuarios)): ?>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?= htmlspecialchars($usuario['cd_usuario']) ?></td>
                        <td><?= htmlspecialchars($usuario['nome_usuario']) ?></td>
                        <td><?= htmlspecialchars($usuario['login_usuario']) ?></td>
                        <td><?= htmlspecialchars($usuario['cd_usu_erp'] ?? '-') ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>/usuarios/alterar/<?= $usuario['cd_usuario'] ?>" 
                               class="btn btn-sm btn-info">Editar</a>
                            <a href="<?= BASE_URL ?>/usuarios/excluir/<?= $usuario['cd_usuario'] ?>" 
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Deseja realmente excluir?')">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">Nenhum usuário encontrado</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>" 
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
