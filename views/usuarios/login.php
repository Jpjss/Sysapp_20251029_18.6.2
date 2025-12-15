<div class="login-wrapper">
    <div class="login-container">
        <!-- Left Side - Image/Branding -->
        <div class="login-brand">
            <div class="brand-content">
                <div class="brand-icon">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                </div>
                <h1><?= APP_NAME ?></h1>
                <p>Sistema de Gestão e Atendimento</p>
                <div class="brand-features">
                    <div class="feature">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span>Dashboard Intuitivo</span>
                    </div>
                    <div class="feature">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span>Relatórios Completos</span>
                    </div>
                    <div class="feature">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span>Gestão de Clientes</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="login-form-container">
            <div class="login-form-content">
                <div class="form-header">
                    <h2>Bem-vindo de volta</h2>
                    <p>Entre com suas credenciais para acessar o sistema</p>
                </div>

                <form method="POST" action="<?= BASE_URL ?>/usuarios/login" class="modern-form">
                    <div class="form-group">
                        <label for="email">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            Usuário
                        </label>
                        <input type="text" 
                               id="email" 
                               name="email" 
                               placeholder="Digite seu e-mail ou usuário" 
                               required
                               autocomplete="username"
                               class="form-input">
                    </div>

                    <div class="form-group">
                        <label for="senha">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                            Senha
                        </label>
                        <div class="password-input-wrapper">
                            <input type="password" 
                                   id="senha" 
                                   name="senha" 
                                   placeholder="Digite sua senha" 
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
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </button>
                </form>

                <div class="form-footer">
                    <div class="version-badge">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        Versão <?= APP_VERSION ?>
                    </div>
                </div>
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
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    background: #f8fafc;
    min-height: 100vh;
    overflow-x: hidden;
}

.login-wrapper {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.login-container {
    display: flex;
    background: white;
    border-radius: 24px;
    box-shadow: 0 20px 80px rgba(0, 0, 0, 0.3);
    overflow: hidden;
    max-width: 1100px;
    width: 100%;
    min-height: 650px;
    animation: slideUp 0.5s ease-out;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Left Side - Branding */
.login-brand {
    flex: 1;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    position: relative;
    overflow: hidden;
}

.login-brand::before {
    content: '';
    position: absolute;
    width: 300px;
    height: 300px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    top: -100px;
    right: -100px;
}

.login-brand::after {
    content: '';
    position: absolute;
    width: 200px;
    height: 200px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 50%;
    bottom: -50px;
    left: -50px;
}

.brand-content {
    position: relative;
    z-index: 1;
}

.brand-icon {
    width: 80px;
    height: 80px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 32px;
    backdrop-filter: blur(10px);
}

.brand-content h1 {
    font-size: 40px;
    font-weight: 700;
    margin-bottom: 12px;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
}

.brand-content > p {
    font-size: 18px;
    opacity: 0.9;
    margin-bottom: 48px;
}

.brand-features {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.feature {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 16px;
    opacity: 0.95;
}

.feature svg {
    flex-shrink: 0;
}

/* Right Side - Form */
.login-form-container {
    flex: 1;
    padding: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
}

.login-form-content {
    width: 100%;
    max-width: 420px;
}

.form-header {
    margin-bottom: 40px;
}

.form-header h2 {
    font-size: 32px;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 8px;
}

.form-header p {
    font-size: 16px;
    color: #475569;
    font-weight: 500;
}

.modern-form {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group label {
    font-size: 15px;
    font-weight: 700;
    color: #1a202c;
    display: flex;
    align-items: center;
    gap: 8px;
}

.form-group label svg {
    color: #5a67d8;
}

.form-input {
    width: 100%;
    padding: 14px 16px;
    border: 2px solid #cbd5e1;
    border-radius: 12px;
    font-size: 15px;
    transition: all 0.3s;
    background: #f8fafc;
    font-family: inherit;
    color: #1a202c;
    font-weight: 500;
}

.form-input:focus {
    outline: none;
    border-color: #5a67d8;
    background: white;
    box-shadow: 0 0 0 4px rgba(90, 103, 216, 0.15);
}

.form-input::placeholder {
    color: #a0aec0;
}

.password-input-wrapper {
    position: relative;
    width: 100%;
}

.password-input-wrapper .form-input {
    padding-right: 48px;
}

.toggle-password {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #718096;
    cursor: pointer;
    padding: 8px;
    border-radius: 6px;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.toggle-password:hover {
    color: #5a67d8;
    background: #e2e8f0;
}

.btn-login {
    width: 100%;
    padding: 18px 24px;
    background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 17px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    margin-top: 8px;
    box-shadow: 0 6px 20px rgba(90, 103, 216, 0.5);
    position: relative;
    overflow: hidden;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
}

.btn-login::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.btn-login:hover::before {
    left: 100%;
}

.btn-login:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(90, 103, 216, 0.7);
    background: linear-gradient(135deg, #4c51bf 0%, #5a3d99 100%);
}

.btn-login:active {
    transform: translateY(0);
}

.btn-login span {
    font-size: 17px;
    letter-spacing: 0.5px;
    font-weight: 700;
}

.btn-login svg {
    transition: transform 0.3s;
    flex-shrink: 0;
}

.btn-login:hover svg {
    transform: translateX(4px);
}

.btn-login:hover svg {
    transform: translateX(4px);
}

.form-footer {
    margin-top: 32px;
    text-align: center;
}

.version-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
    border-radius: 24px;
    color: #64748b;
    font-size: 13px;
    font-weight: 600;
    border: 1px solid #e2e8f0;
    transition: all 0.3s;
}

.version-badge:hover {
    border-color: #cbd5e1;
    background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.version-badge svg {
    animation: rotate 8s linear infinite;
}

@keyframes rotate {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

/* Responsivo */
@media (max-width: 968px) {
    .login-brand {
        display: none;
    }

    .login-container {
        max-width: 500px;
    }

    .login-form-container {
        padding: 40px 32px;
    }
}

@media (max-width: 480px) {
    .login-wrapper {
        padding: 16px;
    }

    .login-form-container {
        padding: 32px 24px;
    }

    .form-header h2 {
        font-size: 26px;
    }

    .form-header p {
        font-size: 14px;
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

// Auto-focus no primeiro campo ao carregar
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('email').focus();
});
</script>
