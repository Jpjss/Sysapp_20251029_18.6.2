<style>
        .database-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .database-card {
            background: #1e293b;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3), 0 2px 4px -1px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(148, 163, 184, 0.1);
            transition: all 0.3s ease;
        }

        [data-theme="light"] .database-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .database-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid rgba(148, 163, 184, 0.2);
        }

        [data-theme="light"] .database-header {
            border-bottom-color: #e2e8f0;
        }

        .database-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--accent-1) 0%, var(--accent-2) 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }

        .database-title h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #e2e8f0;
            margin: 0;
        }

        [data-theme="light"] .database-title h1 {
            color: #1e293b;
        }

        .database-title p {
            font-size: 0.875rem;
            color: #94a3b8;
            margin: 0.25rem 0 0 0;
        }

        [data-theme="light"] .database-title p {
            color: #64748b;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        @media (min-width: 768px) {
            .form-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .form-grid .full-width {
                grid-column: 1 / -1;
            }
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-group label {
            font-size: 0.875rem;
            font-weight: 600;
            color: #cbd5e1;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        [data-theme="light"] .form-group label {
            color: #475569;
        }

        .form-group label .required {
            color: #ef4444;
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem 1rem;
            background-color: #1e293b;
            border: 1px solid rgba(148, 163, 184, 0.2);
            border-radius: 8px;
            color: #e2e8f0;
            font-size: 0.9375rem;
            transition: all 0.2s ease;
            outline: none;
            box-sizing: border-box;
        }

        [data-theme="light"] .form-group input {
            background-color: #ffffff;
            border-color: #cbd5e1;
            color: #1e293b;
        }

        .form-group input:focus {
            border-color: var(--accent-1);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-group input::placeholder {
            color: #64748b;
        }

        [data-theme="light"] .form-group input::placeholder {
            color: #94a3b8;
        }

        .form-group input.error {
            border-color: #ef4444;
        }

        .form-group .error-message {
            font-size: 0.75rem;
            color: #ef4444;
            display: none;
        }

        .form-group input.error + .error-message {
            display: block;
        }

        .password-wrapper {
            position: relative;
        }

        .password-wrapper input {
            padding-right: 3rem;
        }

        .password-toggle {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s ease;
        }

        .password-toggle:hover {
            color: #cbd5e1;
        }

        [data-theme="light"] .password-toggle {
            color: #64748b;
        }

        [data-theme="light"] .password-toggle:hover {
            color: #475569;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(148, 163, 184, 0.1);
        }

        [data-theme="light"] .form-actions {
            border-top-color: #e2e8f0;
        }

        .btn {
            padding: 0.625rem 1.25rem;
            border: none;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: var(--accent-1);
            color: white;
        }

        .btn-primary:hover:not(:disabled) {
            background: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }

        .btn-secondary {
            background-color: #64748b;
            color: white;
        }

        [data-theme="light"] .btn-secondary {
            background-color: #94a3b8;
        }

        .btn-secondary:hover:not(:disabled) {
            background-color: #475569;
        }

        [data-theme="light"] .btn-secondary:hover:not(:disabled) {
            background-color: #64748b;
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btn-icon {
            width: 20px;
            height: 20px;
        }

        /* Loading Overlay */
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }

        .loading-overlay.active {
            display: flex;
        }

        .loading-content {
            background: #1e293b;
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        [data-theme="light"] .loading-content {
            background: white;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid rgba(59, 130, 246, 0.2);
            border-top-color: var(--accent-1);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .loading-text {
            color: #e2e8f0;
            font-size: 1rem;
            font-weight: 500;
        }

        [data-theme="light"] .loading-text {
            color: #1e293b;
        }

        /* Alert Messages */
        .alert {
            padding: 1rem 1.25rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: none;
            align-items: center;
            gap: 0.75rem;
            animation: slideDown 0.3s ease;
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

        .alert.show {
            display: flex;
        }

        .alert-success {
            background-color: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: #22c55e;
        }

        [data-theme="light"] .alert-success {
            background-color: rgba(34, 197, 94, 0.1);
            border-color: rgba(34, 197, 94, 0.3);
            color: #16a34a;
        }

        .alert-error {
            background-color: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #ef4444;
        }

        [data-theme="light"] .alert-error {
            background-color: rgba(239, 68, 68, 0.1);
            border-color: rgba(239, 68, 68, 0.3);
            color: #dc2626;
        }

        .alert-icon {
            flex-shrink: 0;
            width: 20px;
            height: 20px;
        }

        .info-text {
            font-size: 0.8125rem;
            color: #94a3b8;
            margin-top: 1rem;
            padding: 0.875rem 1rem;
            background-color: rgba(59, 130, 246, 0.08);
            border-left: 3px solid var(--accent-1);
            border-radius: 4px;
            line-height: 1.5;
        }

        [data-theme="light"] .info-text {
            color: #64748b;
            background-color: rgba(59, 130, 246, 0.05);
        }
    </style>
</head>
<body>
    <div class="database-container">
        <div class="page-header">
            <h2>Cadastrar Novo Banco de Dados</h2>
            <p style="color: #94a3b8; font-size: 0.875rem; margin-top: 0.5rem;">Configure uma nova conexão de banco de dados para o sistema</p>
        </div>

        <div class="card">
            <div id="alertContainer"></div>

            <form id="databaseForm" method="POST">
                <input type="hidden" name="cd_empresa" id="cd_empresa" value="<?php echo isset($cd_empresa[0][0]['cd_empresa']) ? $cd_empresa[0][0]['cd_empresa'] : ''; ?>">
                
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label for="nome_empresa">
                            Nome da Empresa <span class="required">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="nome_empresa" 
                            name="nome_empresa" 
                            placeholder="Digite o nome da empresa"
                            required
                        >
                        <span class="error-message">Campo obrigatório</span>
                    </div>

                    <div class="form-group">
                        <label for="hostname">
                            Host do Banco <span class="required">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="hostname" 
                            name="hostname" 
                            placeholder="localhost ou IP"
                            required
                        >
                        <span class="error-message">Campo obrigatório</span>
                    </div>

                    <div class="form-group">
                        <label for="nome_banco">
                            Nome do Database <span class="required">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="nome_banco" 
                            name="nome_banco" 
                            placeholder="nome_do_banco"
                            required
                        >
                        <span class="error-message">Campo obrigatório</span>
                    </div>

                    <div class="form-group">
                        <label for="usuario_banco">
                            Usuário <span class="required">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="usuario_banco" 
                            name="usuario_banco" 
                            placeholder="postgres"
                            required
                        >
                        <span class="error-message">Campo obrigatório</span>
                    </div>

                    <div class="form-group">
                        <label for="senha_banco">
                            Senha <span class="required">*</span>
                        </label>
                        <div class="password-wrapper">
                            <input 
                                type="password" 
                                id="senha_banco" 
                                name="senha_banco" 
                                placeholder="Digite a senha"
                                required
                            >
                            <button type="button" class="password-toggle" onclick="togglePassword()">
                                <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20" height="20">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                        <span class="error-message">Campo obrigatório</span>
                    </div>

                    <div class="form-group">
                        <label for="porta_banco">
                            Porta <span class="required">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="porta_banco" 
                            name="porta_banco" 
                            placeholder="5432"
                            value="5432"
                            required
                        >
                        <span class="error-message">Campo obrigatório</span>
                    </div>
                </div>

                <div class="info-text">
                    <strong>💡 Dica:</strong> Certifique-se de que as informações de conexão estão corretas antes de salvar. O sistema tentará validar a conexão com o banco de dados.
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="cancelar()">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="btn-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="btn-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Cadastrar Banco
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            <div class="spinner"></div>
            <p class="loading-text">Salvando configurações...</p>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Sincroniza tema com localStorage na inicialização
        (function() {
            const theme = localStorage.getItem('theme') || 'dark';
            document.documentElement.setAttribute('data-theme', theme);
        })();

        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('senha_banco');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                `;
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                `;
            }
        }

        // Show alert message
        function showAlert(message, type) {
            const alertContainer = document.getElementById('alertContainer');
            const alertClass = type === 'success' ? 'alert-success' : 'alert-error';
            const icon = type === 'success' 
                ? '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="alert-icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'
                : '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="alert-icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';
            
            alertContainer.innerHTML = `
                <div class="alert ${alertClass} show">
                    ${icon}
                    <span>${message}</span>
                </div>
            `;

            // Auto hide after 5 seconds
            setTimeout(() => {
                const alert = alertContainer.querySelector('.alert');
                if (alert) alert.classList.remove('show');
            }, 5000);

            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Form validation
        function validateForm() {
            let isValid = true;
            const requiredFields = ['nome_empresa', 'hostname', 'nome_banco', 'usuario_banco', 'senha_banco', 'porta_banco'];
            
            requiredFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (!field.value.trim()) {
                    field.classList.add('error');
                    isValid = false;
                } else {
                    field.classList.remove('error');
                }
            });

            return isValid;
        }

        // Remove error on input
        document.querySelectorAll('input[required]').forEach(input => {
            input.addEventListener('input', function() {
                this.classList.remove('error');
            });
        });

        // Form submission
        document.getElementById('databaseForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!validateForm()) {
                showAlert('Por favor, preencha todos os campos obrigatórios.', 'error');
                return;
            }

            // Show loading
            document.getElementById('loadingOverlay').classList.add('active');
            document.getElementById('submitBtn').disabled = true;

            // Submit via AJAX
            $.ajax({
                url: '<?php echo BASE_URL; ?>/usuarios/adiciona_database',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    document.getElementById('loadingOverlay').classList.remove('active');
                    document.getElementById('submitBtn').disabled = false;

                    // Se retornou string "1" (sucesso antigo)
                    if (response == "1" || response === 1 || response.success === true) {
                        showAlert('✓ Banco de dados cadastrado e testado com sucesso!', 'success');
                        setTimeout(() => {
                            window.location.href = '<?php echo BASE_URL; ?>/relatorios/index';
                        }, 2000);
                    } else if (response.success === false && response.message) {
                        // Mensagem específica do servidor
                        showAlert('✗ ' + response.message, 'error');
                    } else {
                        showAlert('✗ Erro ao cadastrar banco de dados. Tente novamente.', 'error');
                    }
                },
                error: function(xhr) {
                    document.getElementById('loadingOverlay').classList.remove('active');
                    document.getElementById('submitBtn').disabled = false;
                    
                    // Tentar parsear resposta de erro
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            showAlert('✗ ' + response.message, 'error');
                            return;
                        }
                    } catch(e) {}
                    
                    showAlert('✗ Erro ao processar solicitação. Verifique sua conexão.', 'error');
                }
            });
        });

        // Cancel button
        function cancelar() {
            if (confirm('Deseja realmente cancelar? Todas as informações digitadas serão perdidas.')) {
                window.location.href = '<?php echo BASE_URL; ?>/relatorios/index';
            }
        }
    </script>
</body>
</html>
