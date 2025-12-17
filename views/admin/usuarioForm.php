<div class="page-header">
    <h2><?= $usuario ? 'Editar Usuário' : 'Novo Usuário' ?></h2>
    <a href="<?= BASE_URL ?>/admin/usuarios" class="btn btn-secondary">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        Voltar
    </a>
</div>

<div class="card">
    <form method="POST" action="">
        <div class="form-grid">
            <div class="form-group">
                <label for="nome_usuario">Nome Completo *</label>
                <input type="text" 
                       id="nome_usuario" 
                       name="nome_usuario" 
                       class="form-control" 
                       value="<?= $usuario['nome_usuario'] ?? '' ?>"
                       required>
            </div>
            
            <div class="form-group">
                <label for="ds_login">Login *</label>
                <input type="text" 
                       id="ds_login" 
                       name="ds_login" 
                       class="form-control" 
                       value="<?= $usuario['ds_login'] ?? '' ?>"
                       required>
            </div>
            
            <div class="form-group">
                <label for="ds_email">Email *</label>
                <input type="email" 
                       id="ds_email" 
                       name="ds_email" 
                       class="form-control" 
                       value="<?= $usuario['ds_email'] ?? '' ?>"
                       required>
            </div>
            
            <div class="form-group">
                <label for="senha_usuario">Senha <?= $usuario ? '(deixe em branco para manter)' : '*' ?></label>
                <input type="password" 
                       id="senha_usuario" 
                       name="senha_usuario" 
                       class="form-control"
                       <?= !$usuario ? 'required' : '' ?>>
            </div>
            
            <div class="form-group full-width">
                <label class="checkbox-label">
                    <input type="checkbox" 
                           name="fg_ativo" 
                           value="S"
                           <?= (!$usuario || $usuario['fg_ativo'] === 'S') ? 'checked' : '' ?>>
                    <span>Usuário Ativo</span>
                </label>
            </div>
        </div>

        <!-- SELEÇÃO DE EMPRESAS -->
        <div class="section-divider">
            <h3>Empresas com Acesso</h3>
            <p class="hint">Selecione as empresas que este usuário poderá acessar</p>
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
                            <strong><?= htmlspecialchars($empresa['nm_empresa'] ?? $empresa['nome_empresa'] ?? 'Sem nome') ?></strong>
                            <small><?= htmlspecialchars($empresa['ds_host'] ?? '') ?> / <?= htmlspecialchars($empresa['ds_banco'] ?? $empresa['nome_banco'] ?? '') ?></small>
                        </div>
                    </label>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-data">Nenhuma empresa cadastrada. <a href="<?= BASE_URL ?>/admin/empresas">Cadastre uma empresa</a> primeiro.</p>
            <?php endif; ?>
        </div>

        <!-- SELEÇÃO DE PERMISSÕES -->
        <div class="section-divider">
            <h3>Permissões do Sistema</h3>
            <p class="hint">Selecione as funcionalidades que o usuário poderá acessar</p>
        </div>

        <div class="checkbox-grid">
            <?php 
            $permissoes_disponiveis = [
                'relatorios' => ['nome' => 'Dashboard e Relatórios', 'desc' => 'Visualizar dashboard e relatórios gerenciais'],
                'clientes' => ['nome' => 'Clientes', 'desc' => 'Gerenciar cadastro de clientes'],
                'questionarios' => ['nome' => 'Questionários', 'desc' => 'Realizar questionários e atendimentos'],
                'admin' => ['nome' => 'Administração', 'desc' => 'Acesso ao painel administrativo', 'danger' => true],
                'usuarios' => ['nome' => 'Usuários', 'desc' => 'Gerenciar usuários do sistema', 'danger' => true],
                'empresas' => ['nome' => 'Empresas', 'desc' => 'Gerenciar empresas cadastradas', 'danger' => true]
            ];
            ?>
            <?php foreach ($permissoes_disponiveis as $key => $perm): ?>
                <label class="checkbox-card <?= isset($perm['danger']) ? 'danger' : '' ?>">
                    <input type="checkbox" 
                           name="permissoes[]" 
                           value="<?= $key ?>"
                           <?= in_array($key, $permissoes_usuario ?? []) ? 'checked' : '' ?>>
                    <div class="checkbox-content">
                        <strong><?= $perm['nome'] ?></strong>
                        <small><?= $perm['desc'] ?></small>
                        <?php if (isset($perm['danger'])): ?>
                            <span class="badge-danger">Admin</span>
                        <?php endif; ?>
                    </div>
                </label>
            <?php endforeach; ?>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                    <polyline points="7 3 7 8 15 8"></polyline>
                </svg>
                Salvar
            </button>
            <a href="<?= BASE_URL ?>/admin/usuarios" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<style>
.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    margin-bottom: 24px;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #334155;
}

.form-control {
    width: 100%;
    padding: 10px 12px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.2s;
}

.form-control:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
}

.checkbox-label input[type="checkbox"] {
    width: 20px;
    height: 20px;
    cursor: pointer;
}

.form-actions {
    display: flex;
    gap: 12px;
    padding-top: 16px;
    border-top: 2px solid #e2e8f0;
}

.section-divider {
    margin: 32px 0 20px;
    padding-bottom: 12px;
    border-bottom: 2px solid #e2e8f0;
}

.section-divider h3 {
    font-size: 18px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 4px;
}

.section-divider .hint {
    font-size: 14px;
    color: #64748b;
    margin: 0;
}

.checkbox-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 12px;
    margin-bottom: 24px;
}

.checkbox-card {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 16px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
    background: #fff;
}

.checkbox-card:hover {
    border-color: #cbd5e1;
    background: #f8fafc;
}

.checkbox-card input[type="checkbox"] {
    margin-top: 2px;
    width: 20px;
    height: 20px;
    cursor: pointer;
    flex-shrink: 0;
}

.checkbox-card input[type="checkbox"]:checked ~ .checkbox-content {
    color: #1e293b;
}

.checkbox-card.danger {
    border-color: #fecaca;
    background: #fef2f2;
}

.checkbox-card.danger:hover {
    border-color: #fca5a5;
}

.checkbox-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 4px;
    color: #64748b;
    transition: color 0.2s;
}

.checkbox-content strong {
    font-size: 14px !important;
    font-weight: 600 !important;
    color: #1e293b !important;
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
}

.checkbox-content small {
    font-size: 12px !important;
    color: #64748b !important;
    line-height: 1.4;
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
}

.badge-danger {
    display: inline-block;
    padding: 2px 8px;
    background: #ef4444;
    color: white;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    margin-top: 4px;
    align-self: flex-start;
}

.no-data {
    grid-column: 1 / -1;
    text-align: center;
    padding: 32px;
    color: #64748b;
}

.no-data a {
    color: #667eea;
    font-weight: 600;
    text-decoration: none;
}

.no-data a:hover {
    text-decoration: underline;
}

.validation-error {
    background: #fef2f2;
    border: 2px solid #fecaca;
    color: #dc2626;
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 16px;
    font-weight: 500;
}

.section-divider.error h3,
.section-divider.error .hint {
    color: #dc2626;
}

.section-divider.error {
    border-color: #fecaca;
}
</style>

<script>
document.querySelector('form').addEventListener('submit', function(e) {
    // Remove erros anteriores
    document.querySelectorAll('.validation-error').forEach(el => el.remove());
    document.querySelectorAll('.section-divider.error').forEach(el => el.classList.remove('error'));
    
    let errors = [];
    
    // Verifica se pelo menos uma empresa foi selecionada
    const empresasChecked = document.querySelectorAll('input[name="empresas[]"]:checked').length;
    if (empresasChecked === 0) {
        errors.push('Selecione pelo menos uma empresa para o usuário ter acesso.');
        document.querySelectorAll('.section-divider').forEach(div => {
            if (div.querySelector('h3')?.textContent.includes('Empresas')) {
                div.classList.add('error');
            }
        });
    }
    
    // Verifica se pelo menos uma permissão foi selecionada
    const permissoesChecked = document.querySelectorAll('input[name="permissoes[]"]:checked').length;
    if (permissoesChecked === 0) {
        errors.push('Selecione pelo menos uma permissão para o usuário.');
        document.querySelectorAll('.section-divider').forEach(div => {
            if (div.querySelector('h3')?.textContent.includes('Permissões')) {
                div.classList.add('error');
            }
        });
    }
    
    if (errors.length > 0) {
        e.preventDefault();
        
        // Mostra os erros
        const errorDiv = document.createElement('div');
        errorDiv.className = 'validation-error';
        errorDiv.innerHTML = '<strong>⚠️ Atenção:</strong><br>' + errors.join('<br>');
        
        document.querySelector('.card').insertBefore(errorDiv, document.querySelector('form'));
        
        // Scroll para o topo
        window.scrollTo({ top: 0, behavior: 'smooth' });
        
        return false;
    }
});
</script>
