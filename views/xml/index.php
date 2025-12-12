<div class="page-header">
    <h2>
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 10px;">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
            <polyline points="14 2 14 8 20 8"></polyline>
            <line x1="16" y1="13" x2="8" y2="13"></line>
            <line x1="16" y1="17" x2="8" y2="17"></line>
            <polyline points="10 9 9 9 8 9"></polyline>
        </svg>
        Correção de XMLs NFe
    </h2>
</div>

<!-- Card de Upload -->
<div class="xml-upload-container">
    <div class="xml-upload-card">
        <div class="upload-icon">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="17 8 12 3 7 8"></polyline>
                <line x1="12" y1="3" x2="12" y2="15"></line>
            </svg>
        </div>
        
        <h3>Selecione os arquivos XML</h3>
        <p>Faça upload dos XMLs de NFe que precisam de correção de valores</p>
        
        <form id="xmlForm" enctype="multipart/form-data">
            <div class="file-input-wrapper">
                <input type="file" 
                       id="xmlFiles" 
                       name="xmls[]" 
                       accept=".xml" 
                       multiple 
                       required 
                       class="file-input">
                <label for="xmlFiles" class="file-label">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="17 8 12 3 7 8"></polyline>
                        <line x1="12" y1="3" x2="12" y2="15"></line>
                    </svg>
                    <span id="fileLabel">Selecionar arquivos XML</span>
                </label>
            </div>
            
            <div id="fileList" class="file-list"></div>
        </form>
    </div>
    
    <!-- Badge de Contador Flutuante -->
    <div id="fileCounter" class="file-counter" style="display: none;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
            <polyline points="14 2 14 8 20 8"></polyline>
        </svg>
        <span id="fileCountText">0 arquivos</span>
    </div>
</div>

<!-- Botão Flutuante Fixo -->
<button type="submit" form="xmlForm" id="btnProcessarFloat" class="btn-processar-float" style="display: none;">
    <div class="btn-float-content">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <polyline points="20 6 9 17 4 12"></polyline>
        </svg>
        <div class="btn-float-text">
            <span class="btn-float-title">Iniciar Correção</span>
            <span class="btn-float-subtitle" id="floatFileCount">0 arquivos selecionados</span>
        </div>
    </div>
</button>

<!-- Progress Bar -->
<div id="progressContainer" class="progress-container" style="display: none;">
    <div class="progress-info">
        <span id="progressText">Processando...</span>
        <span id="progressPercent">0%</span>
    </div>
    <div class="progress-bar">
        <div id="progressFill" class="progress-fill"></div>
    </div>
</div>

<!-- Logs -->
<div id="logsContainer" class="logs-container" style="display: none;">
    <div class="logs-header">
        <h3>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
            </svg>
            Logs de Processamento
        </h3>
        <div class="logs-filters">
            <button class="filter-btn active" data-filter="todos">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                </svg>
                Todos
            </button>
            <button class="filter-btn" data-filter="corrigidos">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                Corrigidos
            </button>
            <button class="filter-btn" data-filter="erros">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="15" y1="9" x2="9" y2="15"></line>
                    <line x1="9" y1="9" x2="15" y2="15"></line>
                </svg>
                Erros
            </button>
        </div>
    </div>
    <div id="logsList" class="logs-list"></div>
</div>

<!-- Stats -->
<div id="statsContainer" class="stats-container" style="display: none;">
    <div class="stat-card stat-total">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
            <polyline points="14 2 14 8 20 8"></polyline>
        </svg>
        <div class="stat-info">
            <span class="stat-value" id="statTotal">0</span>
            <span class="stat-label">Total Processados</span>
        </div>
    </div>
    
    <div class="stat-card stat-success">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
        </svg>
        <div class="stat-info">
            <span class="stat-value" id="statCorrigidos">0</span>
            <span class="stat-label">Corrigidos</span>
        </div>
    </div>
    
    <div class="stat-card stat-info">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="16" x2="12" y2="12"></line>
            <line x1="12" y1="8" x2="12.01" y2="8"></line>
        </svg>
        <div class="stat-info">
            <span class="stat-value" id="statSemDivergencia">0</span>
            <span class="stat-label">Sem Divergência</span>
        </div>
    </div>
    
    <div class="stat-card stat-error">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="15" y1="9" x2="9" y2="15"></line>
            <line x1="9" y1="9" x2="15" y2="15"></line>
        </svg>
        <div class="stat-info">
            <span class="stat-value" id="statErros">0</span>
            <span class="stat-label">Erros</span>
        </div>
    </div>
</div>

<!-- Botão Download -->
<div id="downloadContainer" class="download-container" style="display: none;">
    <a href="<?= BASE_URL ?>/xml/download" class="btn-download">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
            <polyline points="7 10 12 15 17 10"></polyline>
            <line x1="12" y1="15" x2="12" y2="3"></line>
        </svg>
        Baixar XMLs Corrigidos
    </a>
    
    <button id="btnNovaCorrecao" class="btn-nova-correcao">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 5v14M5 12h14"></path>
        </svg>
        Nova Correção
    </button>
</div>

<style>
/* Upload Container */
.xml-upload-container {
    max-width: 700px;
    margin: 0 auto 32px;
}

.xml-upload-card {
    background: #111827;
    border: 2px dashed #374151;
    border-radius: 16px;
    padding: 48px 32px;
    text-align: center;
    transition: all 0.3s;
}

.xml-upload-card:hover {
    border-color: #4f46e5;
    background: #1a1f2e;
}

[data-theme="light"] .xml-upload-card {
    background: white;
    border-color: #e2e8f0;
}

[data-theme="light"] .xml-upload-card:hover {
    border-color: #4f46e5;
    background: #f8fafc;
}

.upload-icon {
    color: #6366f1;
    margin-bottom: 24px;
}

.xml-upload-card h3 {
    font-size: 24px;
    color: #f1f5f9;
    margin-bottom: 8px;
    font-weight: 700;
}

[data-theme="light"] .xml-upload-card h3 {
    color: #0f172a;
}

.xml-upload-card p {
    color: #94a3b8;
    margin-bottom: 32px;
    font-size: 15px;
}

/* File Input */
.file-input-wrapper {
    margin-bottom: 24px;
}

.file-input {
    display: none;
}

.file-label {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 14px 32px;
    background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
    color: white;
    border-radius: 12px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.4);
}

.file-label:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(79, 70, 229, 0.6);
}

.file-list {
    margin: 24px 0;
    text-align: left;
    max-height: 300px;
    overflow-y: auto;
    padding: 0 8px;
}

.file-list::-webkit-scrollbar {
    width: 8px;
}

.file-list::-webkit-scrollbar-track {
    background: #1f2937;
    border-radius: 4px;
}

[data-theme="light"] .file-list::-webkit-scrollbar-track {
    background: #e2e8f0;
}

.file-list::-webkit-scrollbar-thumb {
    background: #4f46e5;
    border-radius: 4px;
}

.file-list::-webkit-scrollbar-thumb:hover {
    background: #6366f1;
}

.file-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    background: #1f2937;
    border-radius: 8px;
    margin-bottom: 8px;
    color: #cbd5e1;
    font-size: 14px;
}

[data-theme="light"] .file-item {
    background: #f1f5f9;
    color: #475569;
}

.file-item svg {
    color: #6366f1;
    flex-shrink: 0;
}

.file-item.placeholder {
    background: transparent;
    border: 1px dashed #374151;
    color: #6b7280;
    justify-content: center;
    font-style: italic;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.file-item.placeholder:hover {
    border-color: #4f46e5;
    color: #818cf8;
    background: rgba(79, 70, 229, 0.05);
}

[data-theme="light"] .file-item.placeholder {
    border-color: #cbd5e1;
    color: #94a3b8;
}

[data-theme="light"] .file-item.placeholder:hover {
    border-color: #4f46e5;
    color: #6366f1;
    background: rgba(79, 70, 229, 0.05);
}

/* Contador de Arquivos Flutuante */
.file-counter {
    position: fixed;
    top: 120px;
    right: 24px;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 20px;
    background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
    color: white;
    border-radius: 50px;
    font-size: 14px;
    font-weight: 700;
    box-shadow: 0 8px 24px rgba(79, 70, 229, 0.5);
    z-index: 999;
    animation: slideInRight 0.4s ease-out;
}

@keyframes slideInRight {
    from {
        transform: translateX(200px);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/* Botão Flutuante Fixo */
.btn-processar-float {
    position: fixed;
    bottom: 32px;
    right: 32px;
    padding: 0;
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
    color: white;
    border: none;
    border-radius: 16px;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 12px 40px rgba(34, 197, 94, 0.5);
    z-index: 1000;
    animation: bounceIn 0.6s ease-out;
    min-width: 240px;
}

.btn-processar-float:hover {
    transform: translateY(-4px) scale(1.03);
    box-shadow: 0 16px 56px rgba(34, 197, 94, 0.7);
}

.btn-processar-float:active {
    transform: translateY(-2px) scale(1.01);
}

.btn-float-content {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 20px 28px;
}

.btn-float-content svg {
    flex-shrink: 0;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
}

.btn-float-text {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    text-align: left;
}

.btn-float-title {
    font-size: 16px;
    font-weight: 800;
    letter-spacing: 0.3px;
}

.btn-float-subtitle {
    font-size: 12px;
    opacity: 0.9;
    font-weight: 600;
    margin-top: 2px;
}

@keyframes bounceIn {
    0% {
        transform: translateY(200px) scale(0.5);
        opacity: 0;
    }
    60% {
        transform: translateY(-10px) scale(1.05);
        opacity: 1;
    }
    80% {
        transform: translateY(5px) scale(0.98);
    }
    100% {
        transform: translateY(0) scale(1);
    }
}

/* Pulso do botão quando há muitos arquivos */
@keyframes pulse {
    0%, 100% {
        box-shadow: 0 12px 40px rgba(34, 197, 94, 0.5);
    }
    50% {
        box-shadow: 0 12px 40px rgba(34, 197, 94, 0.8), 0 0 0 8px rgba(34, 197, 94, 0.2);
    }
}

.btn-processar-float.pulse {
    animation: pulse 2s infinite;
}

/* Botão Processar (Escondido - mantido apenas para fallback) */
.btn-processar {
    display: none;
}

/* Progress */
.progress-container {
    max-width: 800px;
    margin: 0 auto 32px;
    padding: 24px;
    background: #111827;
    border: 1px solid #1f2937;
    border-radius: 12px;
}

[data-theme="light"] .progress-container {
    background: white;
    border-color: #e2e8f0;
}

.progress-info {
    display: flex;
    justify-content: space-between;
    margin-bottom: 12px;
    font-size: 14px;
    font-weight: 600;
}

#progressText {
    color: #cbd5e1;
}

[data-theme="light"] #progressText {
    color: #475569;
}

#progressPercent {
    color: #6366f1;
}

.progress-bar {
    height: 32px;
    background: #1f2937;
    border-radius: 16px;
    overflow: hidden;
}

[data-theme="light"] .progress-bar {
    background: #f1f5f9;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
    border-radius: 16px;
    width: 0%;
    transition: width 0.3s;
    box-shadow: 0 0 20px rgba(79, 70, 229, 0.6);
}

/* Logs */
.logs-container {
    max-width: 800px;
    margin: 0 auto 32px;
    padding: 24px;
    background: #111827;
    border: 1px solid #1f2937;
    border-radius: 12px;
}

[data-theme="light"] .logs-container {
    background: white;
    border-color: #e2e8f0;
}

.logs-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
    flex-wrap: wrap;
    gap: 16px;
}

.logs-container h3 {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #f1f5f9;
    font-size: 18px;
    margin: 0;
}

[data-theme="light"] .logs-container h3 {
    color: #0f172a;
}

.logs-filters {
    display: flex;
    gap: 8px;
}

.filter-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    background: #1f2937;
    color: #94a3b8;
    border: 1px solid #374151;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.filter-btn:hover {
    border-color: #4f46e5;
    color: #818cf8;
}

.filter-btn.active {
    background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
    color: white;
    border-color: #4f46e5;
}

[data-theme="light"] .filter-btn {
    background: #f1f5f9;
    color: #64748b;
    border-color: #cbd5e1;
}

[data-theme="light"] .filter-btn:hover {
    border-color: #4f46e5;
    color: #4f46e5;
}

[data-theme="light"] .filter-btn.active {
    background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
    color: white;
    border-color: #4f46e5;
}

.logs-list {
    max-height: 400px;
    overflow-y: auto;
}

.log-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 8px;
    font-size: 14px;
}

.log-item.sucesso {
    background: rgba(34, 197, 94, 0.1);
    border-left: 3px solid #22c55e;
    color: #86efac;
}

.log-item.info {
    background: rgba(59, 130, 246, 0.1);
    border-left: 3px solid #3b82f6;
    color: #93c5fd;
}

.log-item.erro {
    background: rgba(239, 68, 68, 0.1);
    border-left: 3px solid #ef4444;
    color: #fca5a5;
}

[data-theme="light"] .log-item.sucesso {
    color: #166534;
}

[data-theme="light"] .log-item.info {
    color: #1e40af;
}

[data-theme="light"] .log-item.erro {
    color: #991b1b;
}

.log-item svg {
    flex-shrink: 0;
    margin-top: 2px;
}

.log-content {
    flex: 1;
}

.log-arquivo {
    font-weight: 600;
    margin-bottom: 4px;
}

/* Stats */
.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    max-width: 1000px;
    margin: 0 auto 32px;
}

.stat-card {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 24px;
    background: #111827;
    border: 1px solid #1f2937;
    border-radius: 12px;
    transition: all 0.3s;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
}

[data-theme="light"] .stat-card {
    background: white;
    border-color: #e2e8f0;
}

.stat-card svg {
    flex-shrink: 0;
}

.stat-total svg { color: #6366f1; }
.stat-success svg { color: #22c55e; }
.stat-info svg { color: #3b82f6; }
.stat-error svg { color: #ef4444; }

.stat-info {
    display: flex;
    flex-direction: column;
}

.stat-value {
    font-size: 32px;
    font-weight: 800;
    color: #f1f5f9;
    line-height: 1;
}

[data-theme="light"] .stat-value {
    color: #0f172a;
}

.stat-label {
    font-size: 13px;
    color: #94a3b8;
    margin-top: 4px;
    font-weight: 600;
}

/* Download Button */
.download-container {
    text-align: center;
    display: flex;
    gap: 16px;
    justify-content: center;
    flex-wrap: wrap;
}

.btn-download {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 16px 48px;
    background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
    color: white;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.3s;
    box-shadow: 0 4px 12px rgba(14, 165, 233, 0.4);
}

.btn-download:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(14, 165, 233, 0.6);
}

.btn-nova-correcao {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 16px 48px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
}

.btn-nova-correcao:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.6);
}

/* Responsivo */
@media (max-width: 768px) {
    .stats-container {
        grid-template-columns: 1fr;
    }
    
    .btn-processar-float {
        bottom: 20px;
        right: 20px;
        min-width: 200px;
    }
    
    .btn-float-content {
        padding: 16px 20px;
        gap: 12px;
    }
    
    .btn-float-content svg {
        width: 20px;
        height: 20px;
    }
    
    .btn-float-title {
        font-size: 14px;
    }
    
    .btn-float-subtitle {
        font-size: 11px;
    }
    
    .file-counter {
        top: 80px;
        right: 16px;
        padding: 10px 16px;
        font-size: 13px;
    }
    
    .logs-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .logs-filters {
        width: 100%;
        justify-content: space-between;
    }
    
    .filter-btn {
        flex: 1;
        justify-content: center;
        padding: 10px 12px;
        font-size: 12px;
    }
    
    .filter-btn svg {
        width: 14px;
        height: 14px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('xmlFiles');
    const fileLabel = document.getElementById('fileLabel');
    const fileList = document.getElementById('fileList');
    const btnProcessarFloat = document.getElementById('btnProcessarFloat');
    const fileCounter = document.getElementById('fileCounter');
    const fileCountText = document.getElementById('fileCountText');
    const floatFileCount = document.getElementById('floatFileCount');
    const btnNovaCorrecao = document.getElementById('btnNovaCorrecao');
    const form = document.getElementById('xmlForm');
    
    let allFiles = [];
    let isExpanded = false;
    let allLogs = [];
    let currentFilter = 'todos';
    
    // Atualiza lista de arquivos selecionados
    fileInput.addEventListener('change', function() {
        allFiles = Array.from(this.files);
        isExpanded = false;
        renderFileList();
        
        if (allFiles.length > 0) {
            // Atualiza label do input
            fileLabel.innerHTML = `
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span>${allFiles.length} arquivo(s) selecionado(s)</span>
            `;
            
            // Atualiza contador flutuante
            const fileText = allFiles.length === 1 ? '1 arquivo' : `${allFiles.length.toLocaleString('pt-BR')} arquivos`;
            fileCountText.textContent = fileText;
            fileCounter.style.display = 'flex';
            
            // Atualiza e mostra botão flutuante
            floatFileCount.textContent = `${allFiles.length.toLocaleString('pt-BR')} arquivo${allFiles.length > 1 ? 's' : ''} selecionado${allFiles.length > 1 ? 's' : ''}`;
            btnProcessarFloat.style.display = 'block';
            
            // Adiciona efeito de pulso se tiver muitos arquivos (>100)
            if (allFiles.length > 100) {
                btnProcessarFloat.classList.add('pulse');
            } else {
                btnProcessarFloat.classList.remove('pulse');
            }
            
        } else {
            // Reseta tudo
            fileLabel.innerHTML = `
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="17 8 12 3 7 8"></polyline>
                    <line x1="12" y1="3" x2="12" y2="15"></line>
                </svg>
                <span>Selecionar arquivos XML</span>
            `;
            fileList.innerHTML = '';
            btnProcessarFloat.style.display = 'none';
            fileCounter.style.display = 'none';
        }
    });
    
    function renderFileList() {
        if (allFiles.length === 0) {
            fileList.innerHTML = '';
            return;
        }
        
        let displayFiles = [];
        
        if (isExpanded || allFiles.length <= 10) {
            // Mostra todos
            displayFiles = allFiles.map(file => ({ name: file.name, isPlaceholder: false }));
            
            // Adiciona botão "Ver menos" se expandido e tiver mais de 10
            if (isExpanded && allFiles.length > 10) {
                displayFiles.push({
                    name: '▲ Ver menos arquivos',
                    isPlaceholder: true,
                    isCollapse: true
                });
            }
        } else {
            // Mostra primeiros 5 e últimos 5
            displayFiles = [
                ...allFiles.slice(0, 5).map(file => ({ name: file.name, isPlaceholder: false })),
                { 
                    name: `▼ ... e mais ${allFiles.length - 10} arquivos ... (clique para expandir)`, 
                    isPlaceholder: true,
                    isExpand: true
                },
                ...allFiles.slice(-5).map(file => ({ name: file.name, isPlaceholder: false }))
            ];
        }
        
        fileList.innerHTML = displayFiles.map((file, index) => `
            <div class="file-item ${file.isPlaceholder ? 'placeholder' : ''}" ${file.isPlaceholder ? 'data-action="' + (file.isExpand ? 'expand' : 'collapse') + '"' : ''}>
                ${!file.isPlaceholder ? `
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                    </svg>
                ` : ''}
                <span>${file.name}</span>
            </div>
        `).join('');
        
        // Adiciona eventos de clique nos placeholders
        fileList.querySelectorAll('.file-item.placeholder').forEach(item => {
            item.addEventListener('click', function() {
                const action = this.getAttribute('data-action');
                if (action === 'expand') {
                    isExpanded = true;
                } else if (action === 'collapse') {
                    isExpanded = false;
                }
                renderFileList();
                
                // Scroll suave para o placeholder
                if (action === 'collapse') {
                    setTimeout(() => {
                        this.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }, 100);
                }
            });
        });
    }
    
    // Filtros de logs
    document.addEventListener('click', function(e) {
        if (e.target.closest('.filter-btn')) {
            const btn = e.target.closest('.filter-btn');
            const filter = btn.getAttribute('data-filter');
            
            // Atualiza botões ativos
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            currentFilter = filter;
            renderLogs();
        }
    });
    
    function renderLogs() {
        const logsList = document.getElementById('logsList');
        let filteredLogs = allLogs;
        
        if (currentFilter === 'corrigidos') {
            filteredLogs = allLogs.filter(log => log.tipo === 'sucesso');
        } else if (currentFilter === 'erros') {
            filteredLogs = allLogs.filter(log => log.tipo === 'erro');
        }
        
        if (filteredLogs.length === 0) {
            logsList.innerHTML = `
                <div style="text-align: center; padding: 40px; color: #64748b;">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom: 12px; opacity: 0.5;">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <p style="margin: 0; font-size: 14px;">Nenhum log encontrado para este filtro</p>
                </div>
            `;
            return;
        }
        
        logsList.innerHTML = filteredLogs.map(log => `
            <div class="log-item ${log.tipo}">
                ${getLogIcon(log.tipo)}
                <div class="log-content">
                    <div class="log-arquivo">${log.arquivo}</div>
                    <div>${log.mensagem}</div>
                </div>
            </div>
        `).join('');
    }
    
    // Submit do formulário
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const fileCount = fileInput.files.length;
        
        // Esconde botão flutuante e contador durante processamento
        btnProcessarFloat.style.display = 'none';
        fileCounter.style.display = 'none';
        
        // Mostra progress
        document.getElementById('progressContainer').style.display = 'block';
        document.getElementById('logsContainer').style.display = 'none';
        document.getElementById('statsContainer').style.display = 'none';
        document.getElementById('downloadContainer').style.display = 'none';
        
        // Atualiza texto do progress
        document.getElementById('progressText').textContent = `Processando ${fileCount.toLocaleString('pt-BR')} arquivo${fileCount > 1 ? 's' : ''}...`;
        
        try {
            const response = await fetch('<?= BASE_URL ?>/xml/processar', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            // Atualiza progress para 100%
            document.getElementById('progressFill').style.width = '100%';
            document.getElementById('progressPercent').textContent = '100%';
            document.getElementById('progressText').textContent = 'Concluído!';
            
            // Armazena logs
            allLogs = data.logs || [];
            currentFilter = 'todos';
            
            // Mostra logs
            if (allLogs.length > 0) {
                document.getElementById('logsContainer').style.display = 'block';
                
                // Reseta filtro para "Todos"
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                document.querySelector('.filter-btn[data-filter="todos"]').classList.add('active');
                
                renderLogs();
            }
            
            // Mostra stats
            if (data.stats) {
                document.getElementById('statsContainer').style.display = 'grid';
                document.getElementById('statTotal').textContent = data.stats.total.toLocaleString('pt-BR');
                document.getElementById('statCorrigidos').textContent = data.stats.corrigidos.toLocaleString('pt-BR');
                document.getElementById('statSemDivergencia').textContent = data.stats.sem_divergencia.toLocaleString('pt-BR');
                document.getElementById('statErros').textContent = data.stats.erros.toLocaleString('pt-BR');
                
                // Mostra botão de download se houver arquivos processados
                if (data.stats.corrigidos > 0 || data.stats.sem_divergencia > 0) {
                    document.getElementById('downloadContainer').style.display = 'block';
                }
            }
            
            // Mostra mensagem
            if (data.message) {
                alert(data.message);
            }
            
        } catch (error) {
            alert('Erro ao processar arquivos: ' + error.message);
            // Mostra botão novamente em caso de erro
            btnProcessarFloat.style.display = 'block';
            fileCounter.style.display = 'flex';
        }
    });
    
    function getLogIcon(tipo) {
        switch(tipo) {
            case 'sucesso':
                return `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>`;
            case 'info':
                return `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>`;
            case 'erro':
                return `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="15" y1="9" x2="9" y2="15"></line>
                    <line x1="9" y1="9" x2="15" y2="15"></line>
                </svg>`;
            default:
                return '';
        }
    }
    
    // Botão Nova Correção - Resetar formulário
    btnNovaCorrecao.addEventListener('click', function() {
        // Limpar arquivos selecionados
        fileInput.value = '';
        allFiles = [];
        isExpanded = false;
        allLogs = [];
        currentFilter = 'todos';
        
        fileLabel.innerHTML = `
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="17 8 12 3 7 8"></polyline>
                <line x1="12" y1="3" x2="12" y2="15"></line>
            </svg>
            <span>Selecionar arquivos XML</span>
        `;
        fileList.innerHTML = '';
        btnProcessarFloat.style.display = 'none';
        fileCounter.style.display = 'none';
        
        // Esconder containers de resultado
        document.getElementById('progressContainer').style.display = 'none';
        document.getElementById('logsContainer').style.display = 'none';
        document.getElementById('statsContainer').style.display = 'none';
        document.getElementById('downloadContainer').style.display = 'none';
        
        // Resetar progress bar
        document.getElementById('progressFill').style.width = '0%';
        document.getElementById('progressPercent').textContent = '0%';
        document.getElementById('progressText').textContent = 'Processando...';
        
        // Resetar filtros
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        document.querySelector('.filter-btn[data-filter="todos"]').classList.add('active');
        
        // Scroll suave para o topo
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
});
</script>
