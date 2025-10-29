<div class="login-container">
    <div class="login-box">
        <div class="login-header">
            <img src="<?= BASE_URL ?>/public/images/login.png" alt="<?= APP_NAME ?>" class="login-logo">
        </div>
        
        <form method="POST" action="<?= BASE_URL ?>/usuarios/login" class="login-form">
            <div class="form-group">
                <label for="email">Usuário:</label>
                <input type="text" 
                       id="email" 
                       name="email" 
                       placeholder="E-mail" 
                       required 
                       autofocus
                       class="form-control">
            </div>
            
            <div class="form-group">
                <label for="senha">Senha:</label>
                <input type="password" 
                       id="senha" 
                       name="senha" 
                       placeholder="Senha" 
                       required
                       class="form-control">
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">Entrar</button>
            </div>
        </form>
        
        <div class="login-footer">
            <div class="version">Versão Mobile Beta</div>
            <div class="version"><?= APP_VERSION ?></div>
        </div>
    </div>
</div>
