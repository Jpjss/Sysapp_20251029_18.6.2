<div class="empresa-selection">
    <div class="empresa-container">
        <!-- Header -->
        <div class="empresa-header">
            <div class="empresa-icon">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
            </div>
            <h1>Selecione a Empresa</h1>
            <p>Escolha a empresa que deseja acessar</p>
        </div>
        
        <form method="POST" action="<?= BASE_URL ?>/relatorios/empresa" class="empresa-form">
            <div class="empresas-grid">
                <?php foreach ($empresas as $empresa): ?>
                    <label class="empresa-card">
                        <input type="radio" 
                               name="cd_empresa" 
                               value="<?= $empresa['cd_empresa'] ?>" 
                               required>
                        <div class="card-content">
                            <div class="card-icon">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="3" y1="9" x2="21" y2="9"></line>
                                    <line x1="9" y1="21" x2="9" y2="9"></line>
                                </svg>
                            </div>
                            <div class="card-body">
                                <h3><?= htmlspecialchars($empresa['nome_empresa']) ?></h3>
                                <span class="badge"><?= htmlspecialchars($empresa['nome_banco']) ?></span>
                            </div>
                            <div class="check-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </div>
                        </div>
                    </label>
                <?php endforeach; ?>
            </div>
            
            <button type="submit" class="btn-continuar">
                Continuar
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </button>
        </form>
    </div>
</div>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.empresa-selection {
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 24px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}

.empresa-container {
    background: white;
    border-radius: 24px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    padding: 48px;
    max-width: 900px;
    width: 100%;
    animation: slideUp 0.4s ease-out;
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

.empresa-header {
    text-align: center;
    margin-bottom: 40px;
}

.empresa-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    color: white;
    margin-bottom: 24px;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
}

.empresa-header h1 {
    font-size: 32px;
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 8px;
}

.empresa-header p {
    font-size: 16px;
    color: #718096;
}

.empresas-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
}

.empresa-card {
    position: relative;
    cursor: pointer;
    display: block;
}

.empresa-card input[type="radio"] {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

.card-content {
    position: relative;
    background: #f7fafc;
    border: 2px solid #e2e8f0;
    border-radius: 16px;
    padding: 24px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    flex-direction: column;
    gap: 16px;
    min-height: 160px;
}

.empresa-card:hover .card-content {
    border-color: #667eea;
    background: #fff;
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(102, 126, 234, 0.15);
}

.empresa-card input[type="radio"]:checked + .card-content {
    border-color: #667eea;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.2);
}

.card-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    transition: transform 0.3s ease;
}

.empresa-card:hover .card-icon {
    transform: scale(1.1);
}

.card-body {
    flex: 1;
}

.card-body h3 {
    font-size: 18px;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 8px;
    line-height: 1.4;
}

.badge {
    display: inline-block;
    padding: 4px 12px;
    background: #e2e8f0;
    color: #4a5568;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.check-icon {
    position: absolute;
    top: 16px;
    right: 16px;
    width: 32px;
    height: 32px;
    background: #667eea;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    opacity: 0;
    transform: scale(0);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.empresa-card input[type="radio"]:checked ~ .card-content .check-icon {
    opacity: 1;
    transform: scale(1);
}

.btn-continuar {
    width: 100%;
    padding: 16px 32px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    box-shadow: 0 4px 14px rgba(102, 126, 234, 0.4);
}

.btn-continuar:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
}

.btn-continuar:active {
    transform: translateY(0);
}

.btn-continuar svg {
    transition: transform 0.3s ease;
}

.btn-continuar:hover svg {
    transform: translateX(4px);
}

/* Responsivo */
@media (max-width: 768px) {
    .empresa-container {
        padding: 32px 24px;
    }
    
    .empresa-header h1 {
        font-size: 24px;
    }
    
    .empresas-grid {
        grid-template-columns: 1fr;
    }
    
    .empresa-icon {
        width: 64px;
        height: 64px;
    }
}
</style>
