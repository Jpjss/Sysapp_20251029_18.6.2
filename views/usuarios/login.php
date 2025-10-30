<div class="login-container">
    <div class="login-box">
        <div class="login-header">
            <h2><?= APP_NAME ?></h2>
        </div>
        
        <form method="POST" action="<?= BASE_URL ?>/usuarios/login" class="login-form">
            <div class="form-group">
                <label for="email">Usuário:</label>
                <input type="text" 
                       id="email" 
                       name="email" 
                       placeholder="E-mail ou Nome de Usuário" 
                       value=""
                       class="form-control">
            </div>
            
            <div class="form-group">
                <label for="senha">Senha:</label>
                <input type="password" 
                       id="senha" 
                       name="senha" 
                       placeholder="Senha" 
                       value=""
                       class="form-control">
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">Entrar</button>
            </div>
        </form>
        
        <div class="login-footer">
            <div class="version"><?= APP_VERSION ?></div>
        </div>
    </div>
</div>
