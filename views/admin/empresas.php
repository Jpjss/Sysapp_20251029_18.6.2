<div class="page-header">
    <h2>Gerenciamento de Empresas</h2>
    <a href="<?= BASE_URL ?>/admin/empresaForm" class="btn btn-primary">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        Nova Empresa
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h3>Empresas Cadastradas</h3>
        <p>Total: <?= number_format($total, 0, ',', '.') ?> empresas</p>
    </div>
    
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Banco de Dados</th>
                    <th>Servidor</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($empresas)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: #94a3b8;">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" style="margin: 0 auto 16px;">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                                <line x1="12" y1="22.08" x2="12" y2="12"></line>
                            </svg>
                            <p>Nenhuma empresa encontrada</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($empresas as $empresa): ?>
                        <tr>
                            <td><?= $empresa['cd_empresa'] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($empresa['nm_empresa']) ?></strong>
                            </td>
                            <td>
                                <code style="background: #f1f5f9; padding: 4px 8px; border-radius: 4px; font-size: 13px;">
                                    <?= htmlspecialchars($empresa['nm_banco']) ?>
                                </code>
                            </td>
                            <td><?= htmlspecialchars($empresa['nm_servidor']) ?>:<?= $empresa['nm_porta'] ?></td>
                            <td>
                                <?php if ($empresa['fg_ativa'] === 'S'): ?>
                                    <span class="badge badge-success">Ativa</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Inativa</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button onclick="testarConexao(<?= $empresa['cd_empresa'] ?>)" 
                                            class="btn btn-sm btn-success" 
                                            title="Testar Conexão"
                                            id="btn-test-<?= $empresa['cd_empresa'] ?>">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                                        </svg>
                                        Testar
                                    </button>
                                    <a href="<?= BASE_URL ?>/admin/empresaForm/<?= $empresa['cd_empresa'] ?>" 
                                       class="btn btn-sm btn-secondary" 
                                       title="Editar">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                        </svg>
                                        Editar
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

<!-- Modal de Resultado -->
<div id="resultModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Teste de Conexão</h3>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div id="modalMessage"></div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal()">Fechar</button>
        </div>
    </div>
</div>

<script>
async function testarConexao(cd_empresa) {
    const btn = document.getElementById(`btn-test-${cd_empresa}`);
    const originalText = btn.innerHTML;
    
    // Mostra loading
    btn.disabled = true;
    btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation: spin 1s linear infinite;"><circle cx="12" cy="12" r="10"></circle></svg> Testando...';
    
    try {
        const response = await fetch(`<?= BASE_URL ?>/admin/empresaTestarConexao/${cd_empresa}`);
        const data = await response.json();
        
        showModal(
            data.success ? 'Conexão Bem-Sucedida!' : 'Erro na Conexão',
            data.success ? data.message : data.error,
            data.success ? 'success' : 'error'
        );
    } catch (error) {
        showModal('Erro', 'Erro ao testar conexão: ' + error.message, 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
}

function showModal(title, message, type) {
    const modal = document.getElementById('resultModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalMessage = document.getElementById('modalMessage');
    
    modalTitle.textContent = title;
    
    const icon = type === 'success' 
        ? '<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#2ecc71" stroke-width="2" style="margin: 0 auto 16px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>'
        : '<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#e74c3c" stroke-width="2" style="margin: 0 auto 16px;"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>';
    
    modalMessage.innerHTML = `
        <div style="text-align: center;">
            ${icon}
            <p style="font-size: 16px; color: ${type === 'success' ? '#2ecc71' : '#e74c3c'};">${message}</p>
        </div>
    `;
    
    modal.style.display = 'flex';
}

function closeModal() {
    document.getElementById('resultModal').style.display = 'none';
}

// Fecha modal ao clicar fora
window.onclick = function(event) {
    const modal = document.getElementById('resultModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
}
</script>

<style>
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.modal {
    display: none;
    position: fixed;
    z-index: 10000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    align-items: center;
    justify-content: center;
}

.modal-content {
    background: white;
    border-radius: 12px;
    max-width: 500px;
    width: 90%;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.modal-header {
    padding: 20px 24px;
    border-bottom: 2px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
    color: #334155;
}

.modal-close {
    background: none;
    border: none;
    font-size: 28px;
    color: #94a3b8;
    cursor: pointer;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    transition: all 0.2s;
}

.modal-close:hover {
    background: #f1f5f9;
    color: #334155;
}

.modal-body {
    padding: 24px;
}

.modal-footer {
    padding: 16px 24px;
    border-top: 2px solid #e2e8f0;
    text-align: right;
}

code {
    font-family: 'Courier New', monospace;
}
</style>
