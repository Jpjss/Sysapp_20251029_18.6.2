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
            
            <button type="submit" id="btnProcessar" class="btn-processar" disabled>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                Iniciar Correção
            </button>
        </form>
    </div>
</div>

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
    <h3>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
            <polyline points="14 2 14 8 20 8"></polyline>
        </svg>
        Logs de Processamento
    </h3>
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

/* File List */
.file-list {
    margin: 24px 0;
    text-align: left;
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

/* Botão Processar */
.btn-processar {
    padding: 16px 48px;
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 4px 12px rgba(34, 197, 94, 0.4);
}

.btn-processar:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(34, 197, 94, 0.6);
}

.btn-processar:disabled {
    opacity: 0.5;
    cursor: not-allowed;
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

.logs-container h3 {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #f1f5f9;
    margin-bottom: 16px;
    font-size: 18px;
}

[data-theme="light"] .logs-container h3 {
    color: #0f172a;
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

/* Responsivo */
@media (max-width: 768px) {
    .stats-container {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('xmlFiles');
    const fileLabel = document.getElementById('fileLabel');
    const fileList = document.getElementById('fileList');
    const btnProcessar = document.getElementById('btnProcessar');
    const form = document.getElementById('xmlForm');
    
    // Atualiza lista de arquivos selecionados
    fileInput.addEventListener('change', function() {
        const files = Array.from(this.files);
        
        if (files.length > 0) {
            fileLabel.innerHTML = `
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span>${files.length} arquivo(s) selecionado(s)</span>
            `;
            
            fileList.innerHTML = files.map(file => `
                <div class="file-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                    </svg>
                    <span>${file.name}</span>
                </div>
            `).join('');
            
            btnProcessar.disabled = false;
        } else {
            fileLabel.innerHTML = `
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="17 8 12 3 7 8"></polyline>
                    <line x1="12" y1="3" x2="12" y2="15"></line>
                </svg>
                <span>Selecionar arquivos XML</span>
            `;
            fileList.innerHTML = '';
            btnProcessar.disabled = true;
        }
    });
    
    // Submit do formulário
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        
        // Mostra progress
        document.getElementById('progressContainer').style.display = 'block';
        document.getElementById('logsContainer').style.display = 'none';
        document.getElementById('statsContainer').style.display = 'none';
        document.getElementById('downloadContainer').style.display = 'none';
        btnProcessar.disabled = true;
        
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
            
            // Mostra logs
            if (data.logs && data.logs.length > 0) {
                document.getElementById('logsContainer').style.display = 'block';
                document.getElementById('logsList').innerHTML = data.logs.map(log => `
                    <div class="log-item ${log.tipo}">
                        ${getLogIcon(log.tipo)}
                        <div class="log-content">
                            <div class="log-arquivo">${log.arquivo}</div>
                            <div>${log.mensagem}</div>
                        </div>
                    </div>
                `).join('');
            }
            
            // Mostra stats
            if (data.stats) {
                document.getElementById('statsContainer').style.display = 'grid';
                document.getElementById('statTotal').textContent = data.stats.total;
                document.getElementById('statCorrigidos').textContent = data.stats.corrigidos;
                document.getElementById('statSemDivergencia').textContent = data.stats.sem_divergencia;
                document.getElementById('statErros').textContent = data.stats.erros;
                
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
        } finally {
            btnProcessar.disabled = false;
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
});
</script>
