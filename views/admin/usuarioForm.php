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
</style>
