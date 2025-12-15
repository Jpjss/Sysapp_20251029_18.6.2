<div class="page-header">
    <h2>Gerenciamento de Usuários</h2>
    <a href="<?= BASE_URL ?>/admin/usuarioForm" class="btn btn-primary">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        Novo Usuário
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h3>Usuários Cadastrados</h3>
        <p>Total: <?= number_format($total, 0, ',', '.') ?> usuários</p>
    </div>
    
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Login</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($usuarios)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: #94a3b8;">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" style="margin: 0 auto 16px;">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            <p>Nenhum usuário encontrado</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?= $usuario['cd_usuario'] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($usuario['nome_usuario']) ?></strong>
                            </td>
                            <td><?= htmlspecialchars($usuario['ds_login']) ?></td>
                            <td><?= htmlspecialchars($usuario['ds_email']) ?></td>
                            <td>
                                <?php if ($usuario['fg_ativo'] === 'S'): ?>
                                    <span class="badge badge-success">Ativo</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Inativo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="<?= BASE_URL ?>/admin/usuarioForm/<?= $usuario['cd_usuario'] ?>" 
                                       class="btn btn-sm btn-secondary" title="Editar">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                        </svg>
                                        Editar
                                    </a>
                                    <a href="<?= BASE_URL ?>/admin/usuarioEmpresasForm/<?= $usuario['cd_usuario'] ?>" 
                                       class="btn btn-sm btn-info" title="Vincular Empresas">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="8.5" cy="7" r="4"></circle>
                                            <line x1="20" y1="8" x2="20" y2="14"></line>
                                            <line x1="23" y1="11" x2="17" y2="11"></line>
                                        </svg>
                                        Empresas
                                    </a>
                                    <a href="<?= BASE_URL ?>/admin/usuarioDelete/<?= $usuario['cd_usuario'] ?>" 
                                       class="btn btn-sm btn-danger" 
                                       title="Excluir"
                                       onclick="return confirm('Tem certeza que deseja excluir este usuário?')">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        </svg>
                                        Excluir
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>" 
                   class="pagination-link <?= $i == $page ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.btn-group {
    display: flex;
    gap: 4px;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 13px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.badge {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.badge-success {
    background: #dcfce7;
    color: #166534;
}

.badge-danger {
    background: #fee2e2;
    color: #991b1b;
}

.badge-info {
    background: #dbeafe;
    color: #1e40af;
}

.table-responsive {
    overflow-x: auto;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table th {
    background: #f8fafc;
    padding: 12px;
    text-align: left;
    font-weight: 600;
    color: #475569;
    border-bottom: 2px solid #e2e8f0;
}

.table td {
    padding: 12px;
    border-bottom: 1px solid #e2e8f0;
}

.table tbody tr:hover {
    background: #f8fafc;
}

.pagination {
    display: flex;
    gap: 4px;
    padding: 16px;
    justify-content: center;
}

.pagination-link {
    padding: 8px 12px;
    border-radius: 6px;
    background: white;
    border: 1px solid #e2e8f0;
    color: #64748b;
    text-decoration: none;
    transition: all 0.2s;
}

.pagination-link:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
}

.pagination-link.active {
    background: #667eea;
    color: white;
    border-color: #667eea;
}
</style>
