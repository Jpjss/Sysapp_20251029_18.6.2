<div class="page-header">
    <h2>Alterar Usuário</h2>
</div>

<div class="card">
    <form method="POST" action="<?= BASE_URL ?>/usuarios/alterar/<?= $usuario['cd_usuario'] ?>" class="form" id="formUsuario">
        <input type="hidden" name="cd_usuario" value="<?= $usuario['cd_usuario'] ?>">
        
        <div class="form-row">
            <div class="form-group">
                <label for="nome_usuario">Nome: *</label>
                <input type="text" 
                       id="nome_usuario" 
                       name="nome_usuario" 
                       value="<?= htmlspecialchars($usuario['nome_usuario']) ?>"
                       required
                       class="form-control">
            </div>
            
            <div class="form-group">
                <label for="cd_usu_erp">Código ERP:</label>
                <input type="number" 
                       id="cd_usu_erp" 
                       name="cd_usu_erp"
                       value="<?= htmlspecialchars($usuario['cd_usu_erp'] ?? '') ?>"
                       class="form-control">
            </div>
        </div>
        
        <div class="form-group">
            <label for="login_usuario">E-mail (Login): *</label>
            <input type="email" 
                   id="login_usuario" 
                   name="login_usuario" 
                   value="<?= htmlspecialchars($usuario['login_usuario']) ?>"
                   required
                   class="form-control">
            <small class="form-text" id="emailStatus"></small>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="senha_usuario">Nova Senha: <small>(deixe em branco para não alterar)</small></label>
                <input type="password" 
                       id="senha_usuario" 
                       name="senha_usuario"
                       class="form-control">
            </div>
            
            <div class="form-group">
                <label for="senha_confirm">Confirmar Nova Senha:</label>
                <input type="password" 
                       id="senha_confirm" 
                       name="senha_confirm"
                       class="form-control">
            </div>
        </div>
        
        <div class="form-group">
            <label>Empresas: *</label>
            <div class="checkbox-group">
                <?php foreach ($empresas as $empresa): ?>
                    <label class="checkbox-label">
                        <input type="checkbox" 
                               name="cd_empresa[]" 
                               value="<?= $empresa['cd_empresa'] ?>"
                               <?= in_array($empresa['cd_empresa'], $empresasUsuario) ? 'checked' : '' ?>>
                        <?= htmlspecialchars($empresa['nome_empresa']) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="form-group">
            <label>Permissões (Relatórios): *</label>
            <div class="checkbox-group">
                <?php foreach ($interfaces as $interface): ?>
                    <label class="checkbox-label">
                        <input type="checkbox" 
                               name="cd_interface[]" 
                               value="<?= $interface['cd_interface'] ?>"
                               <?= in_array($interface['cd_interface'], $interfacesUsuario) ? 'checked' : '' ?>>
                        <?= htmlspecialchars($interface['nome_interface']) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Atualizar</button>
            <a href="<?= BASE_URL ?>/usuarios/visualizar" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    // Validação de senha
    $('#formUsuario').on('submit', function(e) {
        const senha = $('#senha_usuario').val();
        const confirma = $('#senha_confirm').val();
        
        if (senha && senha !== confirma) {
            e.preventDefault();
            alert('As senhas não coincidem!');
            return false;
        }
    });
    
    // Verifica email
    $('#login_usuario').on('blur', function() {
        const email = $(this).val();
        
        if (email) {
            $.post('<?= BASE_URL ?>/usuarios/verificaEmail', {
                login_usuario: email,
                cd_usuario: <?= $usuario['cd_usuario'] ?>
            }, function(response) {
                $('#emailStatus').text(response);
                
                if (response.includes('já em uso')) {
                    $('#emailStatus').css('color', 'red');
                } else {
                    $('#emailStatus').css('color', 'green');
                }
            });
        }
    });
});
</script>
