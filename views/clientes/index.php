<div class="page-header">
    <h2>
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 10px;">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
            <circle cx="9" cy="7" r="4"></circle>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
        </svg>
        Clientes
    </h2>
    <div style="display: flex; align-items: center; gap: 12px;">
        <span class="stats-badge">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="8.5" cy="7" r="4"></circle>
                <line x1="20" y1="8" x2="20" y2="14"></line>
                <line x1="23" y1="11" x2="17" y2="11"></line>
            </svg>
            <?= number_format($total, 0, ',', '.') ?> <?= $total != 1 ? 'clientes' : 'cliente' ?>
        </span>
    </div>
</div>

<!-- Barra de Busca Moderna -->
<div class="search-bar-modern">
    <form method="GET" action="<?= BASE_URL ?>/clientes/index" class="search-form-modern">
        <div class="search-input-wrapper">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="search-icon">
                <circle cx="11" cy="11" r="8"></circle>
                <path d="m21 21-4.35-4.35"></path>
            </svg>
            <input type="text" 
                   name="filtro" 
                   value="<?= htmlspecialchars($filtro) ?>" 
                   placeholder="Buscar por nome, razão social, CPF/CNPJ..."
                   class="search-input-modern">
            <?php if ($filtro): ?>
                <a href="<?= BASE_URL ?>/clientes/index" class="clear-search" title="Limpar busca">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </a>
            <?php endif; ?>
        </div>
        <button type="submit" class="btn-search-modern">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"></circle>
                <path d="m21 21-4.35-4.35"></path>
            </svg>
            Buscar
        </button>
    </form>
</div>

<!-- Grid de Cards de Clientes -->
<div class="clientes-grid">
    <?php if (!empty($clientes)): ?>
        <?php foreach ($clientes as $cliente): ?>
            <div class="cliente-card">
                <div class="cliente-card-header">
                    <div class="cliente-avatar">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <div class="cliente-info">
                        <h3 class="cliente-nome"><?= htmlspecialchars($cliente['nm_fant']) ?></h3>
                        <span class="cliente-codigo">#<?= htmlspecialchars($cliente['cd_pessoa']) ?></span>
                    </div>
                </div>
                
                <div class="cliente-card-body">
                    <?php if (!empty($cliente['nm_razao'])): ?>
                        <div class="cliente-detail">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            </svg>
                            <span><?= htmlspecialchars($cliente['nm_razao']) ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($cliente['cpf_cnpj'])): ?>
                        <div class="cliente-detail">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                            </svg>
                            <span><?= htmlspecialchars($cliente['cpf_cnpj']) ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($cliente['fone'])): ?>
                        <div class="cliente-detail">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                            </svg>
                            <span><?= htmlspecialchars($cliente['fone']) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="cliente-card-footer">
                    <a href="<?= BASE_URL ?>/clientes/view/<?= $cliente['cd_pessoa'] ?>" 
                       class="btn-card-action btn-view">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        Ver Detalhes
                    </a>
                    <a href="<?= BASE_URL ?>/questionarios/historico/<?= $cliente['cd_pessoa'] ?>" 
                       class="btn-card-action btn-history">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 3v18h18"></path>
                            <path d="m19 9-5 5-4-4-5 5"></path>
                        </svg>
                        Histórico
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="empty-state">
            <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            <h3>Nenhum cliente encontrado</h3>
            <p><?= $filtro ? 'Tente ajustar sua busca' : 'Ainda não há clientes cadastrados' ?></p>
        </div>
    <?php endif; ?>
</div>

<!-- Paginação Moderna -->
<?php if ($totalPages > 1): ?>
    <div class="pagination-modern">
        <div class="pagination-info">
            Página <?= $page ?> de <?= $totalPages ?>
        </div>
        <div class="pagination-buttons">
            <?php if ($page > 1): ?>
                <a href="?page=1<?= $filtro ? '&filtro=' . urlencode($filtro) : '' ?>" 
                   class="pagination-btn" title="Primeira página">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="11 17 6 12 11 7"></polyline>
                        <polyline points="18 17 13 12 18 7"></polyline>
                    </svg>
                </a>
                <a href="?page=<?= $page - 1 ?><?= $filtro ? '&filtro=' . urlencode($filtro) : '' ?>" 
                   class="pagination-btn" title="Página anterior">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </a>
            <?php endif; ?>
            
            <?php
            $start = max(1, $page - 2);
            $end = min($totalPages, $page + 2);
            for ($i = $start; $i <= $end; $i++):
            ?>
                <a href="?page=<?= $i ?><?= $filtro ? '&filtro=' . urlencode($filtro) : '' ?>" 
                   class="pagination-number <?= $i === $page ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
            
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?><?= $filtro ? '&filtro=' . urlencode($filtro) : '' ?>" 
                   class="pagination-btn" title="Próxima página">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>
                <a href="?page=<?= $totalPages ?><?= $filtro ? '&filtro=' . urlencode($filtro) : '' ?>" 
                   class="pagination-btn" title="Última página">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="13 17 18 12 13 7"></polyline>
                        <polyline points="6 17 11 12 6 7"></polyline>
                    </svg>
                </a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<style>
/* Stats Badge */
.stats-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
    color: white;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
}

/* Barra de Busca Moderna */
.search-bar-modern {
    margin-bottom: 32px;
}

.search-form-modern {
    display: flex;
    gap: 12px;
    max-width: 800px;
}

.search-input-wrapper {
    flex: 1;
    position: relative;
    display: flex;
    align-items: center;
}

.search-icon {
    position: absolute;
    left: 16px;
    color: #64748b;
    pointer-events: none;
}

.search-input-modern {
    width: 100%;
    padding: 14px 48px 14px 48px;
    border: 2px solid #cbd5e1;
    border-radius: 12px;
    font-size: 15px;
    background: #f8fafc;
    color: #1a202c;
    transition: all 0.3s;
    font-weight: 500;
}

.search-input-modern:focus {
    outline: none;
    border-color: #5a67d8;
    background: white;
    box-shadow: 0 0 0 4px rgba(90, 103, 216, 0.15);
}

.clear-search {
    position: absolute;
    right: 16px;
    color: #94a3b8;
    cursor: pointer;
    transition: all 0.3s;
    padding: 4px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.clear-search:hover {
    color: #ef4444;
    background: #fee2e2;
}

.btn-search-modern {
    padding: 14px 24px;
    background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 12px rgba(90, 103, 216, 0.4);
    white-space: nowrap;
}

.btn-search-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(90, 103, 216, 0.6);
}

/* Grid de Clientes */
.clientes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
    gap: 24px;
    margin-bottom: 32px;
}

.cliente-card {
    background: #111827;
    border: 1px solid #1f2937;
    border-radius: 16px;
    padding: 24px;
    transition: all 0.3s;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.cliente-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.3);
    border-color: #374151;
}

[data-theme="light"] .cliente-card {
    background: white;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

[data-theme="light"] .cliente-card:hover {
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    border-color: #cbd5e1;
}

.cliente-card-header {
    display: flex;
    align-items: center;
    gap: 16px;
}

.cliente-avatar {
    width: 56px;
    height: 56px;
    background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
}

.cliente-info {
    flex: 1;
    min-width: 0;
}

.cliente-nome {
    font-size: 18px;
    font-weight: 700;
    color: #f1f5f9;
    margin: 0 0 4px 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

[data-theme="light"] .cliente-nome {
    color: #0f172a;
}

.cliente-codigo {
    font-size: 13px;
    color: #94a3b8;
    font-weight: 600;
}

.cliente-card-body {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.cliente-detail {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #cbd5e1;
    font-size: 14px;
    padding: 8px 0;
}

[data-theme="light"] .cliente-detail {
    color: #475569;
}

.cliente-detail svg {
    color: #6366f1;
    flex-shrink: 0;
}

.cliente-detail span {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.cliente-card-footer {
    display: flex;
    gap: 10px;
    padding-top: 12px;
    border-top: 1px solid #1f2937;
}

[data-theme="light"] .cliente-card-footer {
    border-top: 1px solid #e2e8f0;
}

.btn-card-action {
    flex: 1;
    padding: 10px 16px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.3s;
    text-decoration: none;
    border: none;
    cursor: pointer;
}

.btn-view {
    background: rgba(99, 102, 241, 0.15);
    color: #a5b4fc;
    border: 1px solid rgba(99, 102, 241, 0.3);
}

.btn-view:hover {
    background: #4f46e5;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.4);
}

.btn-history {
    background: rgba(34, 197, 94, 0.15);
    color: #86efac;
    border: 1px solid rgba(34, 197, 94, 0.3);
}

.btn-history:hover {
    background: #22c55e;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(34, 197, 94, 0.4);
}

[data-theme="light"] .btn-view {
    background: #eef2ff;
    color: #4f46e5;
    border-color: #c7d2fe;
}

[data-theme="light"] .btn-history {
    background: #f0fdf4;
    color: #16a34a;
    border-color: #bbf7d0;
}

/* Empty State */
.empty-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 80px 20px;
    color: #94a3b8;
}

.empty-state svg {
    margin-bottom: 24px;
    opacity: 0.5;
}

.empty-state h3 {
    font-size: 24px;
    color: #cbd5e1;
    margin-bottom: 8px;
}

[data-theme="light"] .empty-state h3 {
    color: #475569;
}

.empty-state p {
    font-size: 16px;
    color: #94a3b8;
}

/* Paginação Moderna */
.pagination-modern {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 24px;
    background: #111827;
    border: 1px solid #1f2937;
    border-radius: 12px;
    gap: 20px;
    flex-wrap: wrap;
}

[data-theme="light"] .pagination-modern {
    background: white;
    border: 1px solid #e2e8f0;
}

.pagination-info {
    font-size: 14px;
    color: #94a3b8;
    font-weight: 600;
}

.pagination-buttons {
    display: flex;
    gap: 8px;
    align-items: center;
}

.pagination-btn,
.pagination-number {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s;
    text-decoration: none;
    color: #cbd5e1;
    background: #1f2937;
    border: 1px solid #374151;
}

[data-theme="light"] .pagination-btn,
[data-theme="light"] .pagination-number {
    color: #475569;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
}

.pagination-btn:hover,
.pagination-number:hover {
    background: #374151;
    border-color: #4b5563;
    transform: translateY(-2px);
}

[data-theme="light"] .pagination-btn:hover,
[data-theme="light"] .pagination-number:hover {
    background: #e2e8f0;
    border-color: #cbd5e1;
}

.pagination-number.active {
    background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
    color: white;
    border-color: transparent;
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.4);
}

/* Responsivo */
@media (max-width: 768px) {
    .clientes-grid {
        grid-template-columns: 1fr;
    }
    
    .search-form-modern {
        flex-direction: column;
    }
    
    .pagination-modern {
        flex-direction: column;
        text-align: center;
    }
    
    .pagination-buttons {
        flex-wrap: wrap;
        justify-content: center;
    }
}
</style>
