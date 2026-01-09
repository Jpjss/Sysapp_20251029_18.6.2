<div class="login-wrapper">

    <!-- Painel de Login -->
    <div class="login-panel">
        <div class="login-panel-inner">
            <!-- Ícone e título -->
            <div class="panel-header">
                <div class="panel-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                </div>
                <h1><?= APP_NAME ?></h1>
                <p class="subtitle">Sistema de Gestão e Atendimento</p>
            </div>

            <!-- Título secundário -->
            <div class="welcome-header">
                <h2>Bem-vindo de volta</h2>
                <p>Entre com suas credenciais para acessar o sistema</p>
            </div>

            <!-- Mensagens flash -->
            <?php
            $flash = Session::flash();
            if ($flash):
            ?>
                <div class="alert alert-<?= $flash['type'] ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <?php if ($flash['type'] === 'error'): ?>
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        <?php else: ?>
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        <?php endif; ?>
                    </svg>
                    <span><?= htmlspecialchars($flash['message']) ?></span>
                </div>
            <?php endif; ?>

            <!-- Formulário -->
            <form method="POST" action="<?= BASE_URL ?>/usuarios/login" class="login-form">
                <div class="form-group">
                    <label for="email">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        Usuário
                    </label>
                    <input type="text" 
                           id="email" 
                           name="email" 
                           placeholder="Digite seu usuário" 
                           required
                           autocomplete="username"
                           class="form-input">
                </div>

                <div class="form-group">
                    <label for="senha">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                        Senha
                    </label>
                    <div class="password-wrapper">
                        <input type="password" 
                               id="senha" 
                               name="senha" 
                               placeholder="••••••" 
                               required
                               autocomplete="current-password"
                               class="form-input">
                        <button type="button" class="toggle-password" onclick="togglePassword()">
                            <svg id="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    <span>Entrar</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </button>
            </form>

            <!-- Versão -->
            <div class="version-badge">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                Versão <?= APP_VERSION ?>
            </div>
        </div>
    </div>
</div>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica Neue', Arial, sans-serif;
    min-height: 100vh;
    overflow: hidden;
    background: url("<?= BASE_URL ?>/public/images/wla-stand.jpg") center / cover no-repeat fixed;
}

.login-wrapper {
    min-height: 100vh;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

/* Logo SYSTEC posicionada à esquerda */
/* Painel de Login - Glassmorphism com borda azul */
.login-panel {
    position: relative;
    width: 420px;
    margin-left: 300px;
    padding: 3px;
    border-radius: 20px;
    background: linear-gradient(135deg, 
        rgba(59, 130, 246, 0.8) 0%, 
        rgba(37, 99, 235, 0.6) 25%,
        rgba(59, 130, 246, 0.4) 50%,
        rgba(37, 99, 235, 0.6) 75%,
        rgba(59, 130, 246, 0.8) 100%
    );
    box-shadow: 
        0 0 40px rgba(59, 130, 246, 0.3),
        0 0 80px rgba(59, 130, 246, 0.1),
        0 25px 50px rgba(0, 0, 0, 0.4);
    animation: fadeIn 0.6s ease-out, glow 3s ease-in-out infinite alternate;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes glow {
    from {
        box-shadow: 
            0 0 40px rgba(59, 130, 246, 0.3),
            0 0 80px rgba(59, 130, 246, 0.1),
            0 25px 50px rgba(0, 0, 0, 0.4);
    }
    to {
        box-shadow: 
            0 0 50px rgba(59, 130, 246, 0.4),
            0 0 100px rgba(59, 130, 246, 0.15),
            0 25px 50px rgba(0, 0, 0, 0.4);
    }
}

.login-panel-inner {
    position: relative;
    width: 420px;
    padding: 40px 36px;
    border-radius: 20px;

    /* Glassmorphism */
    background: linear-gradient(
        180deg,
        rgba(18, 32, 72, 0.85),
        rgba(10, 20, 50, 0.9)
    );
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);

    /* Neon border */
    border: 1px solid rgba(120, 170, 255, 0.35);

    /* Glow melhorado */
    box-shadow:
        0 0 0 1px rgba(120, 170, 255, 0.15),
        0 25px 70px rgba(0, 0, 0, 0.7),
        inset 0 0 35px rgba(120, 170, 255, 0.12);

    color: #ffffff;
}

/* Efeito de luz suave nas bordas (top) */
.login-panel-inner::before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: 20px;
    background: linear-gradient(
        180deg,
        rgba(120, 170, 255, 0.15),
        transparent 40%
    );
    pointer-events: none;
}

/* Header do painel */
.panel-header {
    text-align: center;
    margin-bottom: 36px;
}

.panel-icon {
    width: 56px;
    height: 56px;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.3) 0%, rgba(37, 99, 235, 0.2) 100%);
    border: 1px solid rgba(59, 130, 246, 0.4);
    border-radius: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 16px;
    color: #60a5fa;
}

.panel-header h1 {
    font-size: 28px;
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 6px;
    letter-spacing: -0.5px;
}

.panel-header .subtitle {
    font-size: 14px;
    color: rgba(148, 163, 184, 0.9);
    font-weight: 400;
}

/* Welcome header */
.welcome-header {
    text-align: center;
    margin-bottom: 28px;
}

.welcome-header h2 {
    font-size: 22px;
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 6px;
}

.welcome-header p {
    font-size: 13px;
    color: rgba(148, 163, 184, 0.8);
    font-weight: 400;
}

/* Formulário */
.login-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group label {
    font-size: 13px;
    font-weight: 600;
    color: rgba(226, 232, 240, 0.9);
    display: flex;
    align-items: center;
    gap: 8px;
}

.form-group label svg {
    color: #60a5fa;
}

.form-input {
    width: 100%;
    padding: 14px 16px;
    background: rgba(255, 255, 255, 0.12);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    border: 1px solid rgba(130, 170, 255, 0.3);
    border-radius: 12px;
    font-size: 15px;
    color: #fff;
    font-family: inherit;
    transition: all 0.25s ease;
}

.form-input:focus {
    outline: none;
    border-color: #7aa2ff;
    box-shadow:
        0 0 0 3px rgba(122, 162, 255, 0.25),
        inset 0 0 6px rgba(255, 255, 255, 0.15);
}

.form-input::placeholder {
    color: rgba(255, 255, 255, 0.55);
}

.password-wrapper {
    position: relative;
}

.password-wrapper .form-input {
    padding-right: 48px;
}

.toggle-password {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: rgba(148, 163, 184, 0.7);
    cursor: pointer;
    padding: 6px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.toggle-password:hover {
    color: #60a5fa;
    background: rgba(59, 130, 246, 0.1);
}

/* Botão de login */
.btn-login {
    width: 100%;
    margin-top: 18px;
    padding: 14px;
    border-radius: 14px;
    border: none;
    background: linear-gradient(135deg, var(--button-grad-start), var(--button-grad-end));
    color: #fff;
    font-weight: 600;
    font-size: 16px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    box-shadow:
        0 10px 25px rgba(120, 130, 255, 0.5),
        inset 0 0 10px rgba(255, 255, 255, 0.15);
    transition: all 0.25s ease;
    position: relative;
    overflow: hidden;
}

.btn-login::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.15), transparent);
    transition: left 0.5s;
}

.btn-login:hover {
    transform: translateY(-1px);
    box-shadow: 0 18px 40px rgba(105, 110, 255, 0.65);
}

.btn-login:hover::before {
    left: 100%;
}

.btn-login:active {
    transform: translateY(0);
}

.btn-login svg {
    transition: transform 0.3s;
}

.btn-login:hover svg {
    transform: translateX(4px);
}

/* Badge de versão */
.version-badge {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-top: 18px;
    font-size: 12px;
    color: rgba(255, 255, 255, 0.45);
    text-align: center;
}

.version-badge svg {
    opacity: 0.6;
}

/* Alertas */
.alert {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    border-radius: 10px;
    margin-bottom: 20px;
    font-size: 13px;
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.alert-error {
    background: rgba(239, 68, 68, 0.15);
    color: #fca5a5;
    border: 1px solid rgba(239, 68, 68, 0.3);
}

.alert-success {
    background: rgba(34, 197, 94, 0.15);
    color: #86efac;
    border: 1px solid rgba(34, 197, 94, 0.3);
}

.alert-info {
    background: rgba(59, 130, 246, 0.15);
    color: #93c5fd;
    border: 1px solid rgba(59, 130, 246, 0.3);
}

/* Responsivo */
@media (max-width: 1200px) {
    .login-panel {
        margin-left: 100px;
    }
}

@media (max-width: 968px) {
    .login-panel {
        margin-left: 0;
        margin-top: 120px;
        width: 90%;
        max-width: 400px;
    }
    
    .login-wrapper {
        flex-direction: column;
        padding: 20px;
    }
}

@media (max-width: 480px) {
    .login-panel-inner {
        padding: 36px 24px;
    }
    
    .panel-header h1 {
        font-size: 24px;
    }
}
</style>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('senha');
    const eyeIcon = document.getElementById('eye-icon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
    } else {
        passwordInput.type = 'password';
        eyeIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('email').focus();
});
</script>
