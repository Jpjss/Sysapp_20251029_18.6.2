<div class="page-header">
    <h2>Trocar Senha</h2>
</div>

<div class="card">
    <form method="POST" action="<?= BASE_URL ?>/usuarios/changePassword" class="form">
        <div class="form-group">
            <label for="senha_usuario">Senha Atual:</label>
            <input type="password" 
                   id="senha_usuario" 
                   name="senha_usuario" 
                   required
                   class="form-control">
        </div>
        
        <div class="form-group">
            <label for="prox_senha_usuario">Nova Senha:</label>
            <input type="password" 
                   id="prox_senha_usuario" 
                   name="prox_senha_usuario" 
                   required
                   class="form-control">
        </div>
        
        <div class="form-group">
            <label for="prox_senha_usuario_confirm">Confirmar Nova Senha:</label>
            <input type="password" 
                   id="prox_senha_usuario_confirm" 
                   name="prox_senha_usuario_confirm" 
                   required
                   class="form-control">
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Atualizar Senha</button>
            <a href="<?= BASE_URL ?>/relatorios" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
