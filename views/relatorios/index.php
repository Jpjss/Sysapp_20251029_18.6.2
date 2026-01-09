<div class="page-header">
    <h2>Dashboard</h2>
    <?php if (Session::check('Config.empresa')): ?>
        <span class="empresa-badge"><?= htmlspecialchars(Session::read('Config.empresa')) ?></span>
    <?php endif; ?>
</div>

<?php if ($stats['total_questionarios'] == 0 && $stats['total_respostas'] == 0): ?>
<div class="card" style="background: rgba(245, 158, 11, 0.1); border: 2px solid rgba(245, 158, 11, 0.3);">
    <div style="display: flex; align-items: center; gap: 15px;">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
        </svg>
        <div>
            <h3 style="color: #f59e0b; margin: 0 0 5px 0;">Banco ERP Comercial Detectado</h3>
            <p style="margin: 0; color: #94a3b8;">
                Este banco não possui dados de questionários/atendimentos. Os números abaixo representam <strong>vendas e transações comerciais</strong> do sistema ERP.
            </p>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Cards de Estatísticas -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: #e74c3c;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
            </svg>
        </div>
        <div class="stat-content">
            <h3><?= number_format($stats['atendimentos_hoje'], 0, ',', '.') ?></h3>
            <p><?= $stats['total_questionarios'] == 0 ? 'Vendas Hoje' : 'Atendimentos Hoje' ?></p>
            <?php if ($stats['total_questionarios'] == 0 && isset($stats['valor_vendas_hoje'])): ?>
                <small style="color: #e74c3c; font-weight: 600;">
                    R$ <?= number_format($stats['valor_vendas_hoje'], 2, ',', '.') ?>
                </small>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #f39c12;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
        </div>
        <div class="stat-content">
            <h3><?= number_format($stats['atendimentos_mes'], 0, ',', '.') ?></h3>
            <p><?= $stats['total_questionarios'] == 0 ? 'Vendas no Mês' : 'Atendimentos no Mês' ?></p>
            <?php if ($stats['total_questionarios'] == 0 && isset($stats['valor_vendas_mes'])): ?>
                <small style="color: #f39c12; font-weight: 600;">
                    R$ <?= number_format($stats['valor_vendas_mes'], 2, ',', '.') ?>
                </small>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Gráfico de Atendimentos -->
<div class="card">
    <h3><?= $stats['total_questionarios'] == 0 ? 'Vendas dos Últimos 7 Dias' : 'Atendimentos dos Últimos 7 Dias' ?></h3>
    <div class="chart-wrapper">
        <canvas id="chartAtendimentos"></canvas>
    </div>
</div>

<!-- Gráficos Adicionais -->
<div class="grid-2">
    <div class="card">
        <h3>Distribuição por Tipo</h3>
        <div class="chart-wrapper">
            <canvas id="chartTipos"></canvas>
        </div>
    </div>
    
    <div class="card">
        <h3>Tendência Mensal</h3>
        <div class="chart-wrapper">
            <canvas id="chartTendencia"></canvas>
        </div>
    </div>
</div>

<!-- Top Clientes -->
<div class="card">
    <h3><?= $stats['total_questionarios'] == 0 ? 'Top 5 Clientes com Mais Compras' : 'Top 5 Clientes Mais Atendidos' ?></h3>
    <table class="table">
        <thead>
            <tr>
                <th>Cliente</th>
                <th><?= $stats['total_questionarios'] == 0 ? 'Total de Compras' : 'Total Atendimentos' ?></th>
                <th><?= $stats['total_questionarios'] == 0 ? 'Última Compra' : 'Último Atendimento' ?></th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($topClientes)): ?>
                <?php foreach ($topClientes as $cliente): ?>
                    <tr>
                        <td><?= htmlspecialchars($cliente['nm_fant']) ?></td>
                        <td><?= $cliente['total_atendimentos'] ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($cliente['ultimo_atendimento'])) ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>/clientes/view/<?= $cliente['cd_pessoa'] ?>" 
                               class="btn btn-sm btn-info">Ver</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center"><?= $stats['total_questionarios'] == 0 ? 'Nenhuma compra registrada' : 'Nenhum atendimento registrado' ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Ações Rápidas -->
<div class="card">
    <h3>Ações Rápidas</h3>
    <div class="quick-actions">
        <a href="<?= BASE_URL ?>/questionarios/proximosAtendimentos" class="action-btn">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
            <span>Próximos Atendimentos</span>
        </a>
        
        <a href="<?= BASE_URL ?>/questionarios/aniversariantes" class="action-btn">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
            <span>Aniversariantes</span>
        </a>
        
        <a href="<?= BASE_URL ?>/clientes/index" class="action-btn">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
            <span>Buscar Clientes</span>
        </a>
        
        <a href="<?= BASE_URL ?>/relatorios/atendimentos" class="action-btn">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="1" x2="12" y2="23"></line>
                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
            </svg>
            <span>Relatórios</span>
        </a>
        
        <a href="<?= BASE_URL ?>/relatorios/estoque_detalhado" class="action-btn" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
            </svg>
            <span>📦 Estoque Detalhado</span>
        </a>
        
        <a href="<?= BASE_URL ?>/relatorios/entrada_vendas" class="action-btn" style="background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%); color: white;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
            </svg>
            <span>📈 Entrada x Vendas</span>
        </a>
        
        <a href="<?= BASE_URL ?>/marcasvendas/dashboard" class="action-btn" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path>
                <path d="M22 12A10 10 0 0 0 12 2v10z"></path>
            </svg>
            <span>📊 Marcas Mais Vendidas</span>
        </a>
        
        <a href="<?= BASE_URL ?>/usuarios/adiciona_database" class="action-btn" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
            </svg>
            <span>Adicionar Database</span>
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
// ========================================
// SISTEMA AVANÇADO DE RESPONSIVIDADE PARA GRÁFICOS
// CORREÇÃO DE GRÁFICOS CORTADOS AO SCROLL (Page Down / Espaço)
// ========================================

// ========================================
// CONFIGURAÇÃO GLOBAL DO SISTEMA
// ========================================

// Armazenamento global das instâncias dos gráficos
window.dashboardCharts = {};

// Estado de renderização dos gráficos (lazy loading)
window.chartStates = {
    chartAtendimentos: { rendered: false, visible: false },
    chartTipos: { rendered: false, visible: false },
    chartTendencia: { rendered: false, visible: false }
};

// Dados globais dos gráficos (para lazy rendering)
window.chartData = {
    atendimentos: {
        labels: [],
        data: [],
        ready: false
    },
    tipos: {
        data: [],
        ready: false
    },
    tendencia: {
        labels: [],
        data: [],
        ready: false
    }
};

// ========================================
// FUNÇÕES UTILITÁRIAS AVANÇADAS
// ========================================

// Função debounce para otimizar eventos
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Função para verificar se elemento está na viewport
function isElementInViewport(el) {
    if (!el) return false;
    const rect = el.getBoundingClientRect();
    const windowHeight = window.innerHeight || document.documentElement.clientHeight;
    const windowWidth = window.innerWidth || document.documentElement.clientWidth;

    // Elemento precisa estar pelo menos 10% visível
    const vertInView = (rect.top <= windowHeight * 0.9) && ((rect.top + rect.height) >= windowHeight * 0.1);
    const horInView = (rect.left <= windowWidth) && ((rect.left + rect.width) >= 0);

    return vertInView && horInView;
}

// Função para forçar resize de gráfico específico
function forceChartResize(chartId) {
    const chart = window.dashboardCharts[chartId];
    if (chart && typeof chart.resize === 'function') {
        // Pequeno delay para garantir que o DOM esteja atualizado
        setTimeout(() => {
            chart.resize();
            console.log(`📊 ${chartId} redimensionado com sucesso`);
        }, 50);
    }
}

// Função para redimensionar todos os gráficos visíveis
function resizeVisibleCharts() {
    Object.keys(window.dashboardCharts).forEach(chartId => {
        const chartWrapper = document.querySelector(`#${chartId}`).parentElement;
        if (chartWrapper && isElementInViewport(chartWrapper)) {
            forceChartResize(chartId);
        }
    });
}

// ========================================
// INTERSECTION OBSERVER PARA DETECTAR ENTRADA NA VIEWPORT
// ========================================

function setupIntersectionObserver() {
    const chartContainers = document.querySelectorAll('.chart-wrapper');

    const observerOptions = {
        root: null, // viewport
        rootMargin: '50px 0px', // margem de 50px para antecipar
        threshold: [0, 0.1, 0.2, 0.5, 1.0] // múltiplos thresholds
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            const chartWrapper = entry.target;
            const canvas = chartWrapper.querySelector('canvas');
            if (!canvas) return;

            const chartId = canvas.id;
            const isVisible = entry.isIntersecting;
            const intersectionRatio = entry.intersectionRatio;

            // Atualiza estado de visibilidade
            if (window.chartStates[chartId]) {
                window.chartStates[chartId].visible = isVisible;
            }

            if (isVisible && intersectionRatio > 0.1) {
                // Gráfico entrou na viewport - forçar resize
                console.log(`👁️ ${chartId} entrou na viewport (${(intersectionRatio * 100).toFixed(1)}% visível)`);

                // Renderizar se ainda não foi renderizado (lazy loading)
                if (!window.chartStates[chartId].rendered) {
                    renderChartIfReady(chartId);
                }

                // Sempre redimensionar quando visível
                forceChartResize(chartId);

                // Remover classe de loading se existir
                chartWrapper.classList.remove('loading');

            } else if (!isVisible) {
                // Gráfico saiu da viewport
                console.log(`🚫 ${chartId} saiu da viewport`);
            }
        });
    }, observerOptions);

    // Observar todos os containers de gráfico
    chartContainers.forEach(container => {
        observer.observe(container);
    });

    return observer;
}

// ========================================
// RESIZE OBSERVER PARA MUDANÇAS NO CONTAINER
// ========================================

function setupChartResizeObserver(chartId, chartInstance) {
    const chartWrapper = document.querySelector(`#${chartId}`).parentElement;
    if (!chartWrapper) return;

    const resizeObserver = new ResizeObserver(debounce((entries) => {
        // Só redimensionar se o gráfico estiver visível
        if (window.chartStates[chartId] && window.chartStates[chartId].visible) {
            if (chartInstance && typeof chartInstance.resize === 'function') {
                chartInstance.resize();
                console.log(`🔄 ${chartId} redimensionado via ResizeObserver`);
            }
        }
    }, 100));

    resizeObserver.observe(chartWrapper);
    return resizeObserver;
}

// ========================================
// SISTEMA DE LAZY RENDERING
// ========================================

function renderChartIfReady(chartId) {
    const state = window.chartStates[chartId];
    if (!state || state.rendered) return;

    const data = window.chartData[chartId.replace('chart', '').toLowerCase()];
    if (!data || !data.ready) {
        console.log(`⏳ ${chartId} aguardando dados...`);
        return;
    }

    console.log(`🎨 Renderizando ${chartId} (lazy loading)`);

    // Renderizar o gráfico
    switch(chartId) {
        case 'chartAtendimentos':
            renderChartAtendimentos();
            break;
        case 'chartTipos':
            renderChartTipos();
            break;
        case 'chartTendencia':
            renderChartTendencia();
            break;
    }

    // Marcar como renderizado
    state.rendered = true;
}

// ========================================
// CONFIGURAÇÕES GLOBAIS DOS GRÁFICOS
// ========================================

const chartDefaults = {
    responsive: true,
    maintainAspectRatio: false,
    animation: {
        duration: 300, // Animação mais rápida para melhor UX
        easing: 'easeOutQuart'
    },
    plugins: {
        legend: {
            display: false
        },
        tooltip: {
            backgroundColor: 'rgba(30, 41, 59, 0.95)',
            titleFont: { size: 14, weight: 'bold' },
            bodyFont: { size: 13 },
            padding: 12,
            borderColor: 'rgba(102, 126, 234, 0.3)',
            borderWidth: 1
        }
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                stepSize: 1
            },
            grid: {
                color: 'rgba(203, 213, 225, 0.3)',
                drawBorder: false
            }
        },
        x: {
            grid: {
                display: false
            }
        }
    }
};

// ========================================
// PREPARAÇÃO DOS DADOS (LAZY LOADING)
// ========================================

// Preparar dados do gráfico de atendimentos
const rawAtendimentosData = <?= json_encode($atendimentosPeriodo) ?>;
window.chartData.atendimentos.labels = rawAtendimentosData.map(item => {
    const date = new Date(item.data);
    return date.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' });
});
window.chartData.atendimentos.data = rawAtendimentosData.map(item => parseInt(item.total));
window.chartData.atendimentos.ready = true;

// Preparar dados do gráfico de tipos
window.chartData.tipos.data = [
    <?= $stats['atendimentos_hoje'] ?>,
    <?= $stats['atendimentos_mes'] ?>
];
window.chartData.tipos.ready = true;

// Preparar dados do gráfico de tendência
window.chartData.tendencia.labels = window.chartData.atendimentos.labels;
window.chartData.tendencia.data = window.chartData.atendimentos.data;
window.chartData.tendencia.ready = true;

// ========================================
// FUNÇÕES DE RENDERIZAÇÃO INDIVIDUAL
// ========================================

function renderChartAtendimentos() {
    const ctx = document.getElementById('chartAtendimentos');
    if (!ctx) return;

    window.dashboardCharts.chartAtendimentos = new Chart(ctx, {
        type: 'line',
        data: {
            labels: window.chartData.atendimentos.labels,
            datasets: [{
                label: '<?= $stats['total_questionarios'] == 0 ? "Vendas" : "Atendimentos" ?>',
                data: window.chartData.atendimentos.data,
                borderColor: '#3498db',
                backgroundColor: 'rgba(52, 152, 219, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#3498db',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            ...chartDefaults,
            plugins: {
                ...chartDefaults.plugins,
                legend: { display: false }
            }
        }
    });

    setupChartResizeObserver('chartAtendimentos', window.dashboardCharts.chartAtendimentos);
}

function renderChartTipos() {
    const ctx = document.getElementById('chartTipos');
    if (!ctx) return;

    window.dashboardCharts.chartTipos = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Vendas Hoje', 'Vendas no Mês'],
            datasets: [{
                data: window.chartData.tipos.data,
                backgroundColor: [
                    'rgba(231, 76, 60, 0.8)',
                    'rgba(243, 156, 18, 0.8)'
                ],
                borderColor: [
                    '#e74c3c',
                    '#f39c12'
                ],
                borderWidth: 2
            }]
        },
        options: {
            ...chartDefaults,
            plugins: {
                ...chartDefaults.plugins,
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: { size: 12 }
                    }
                }
            }
        }
    });

    setupChartResizeObserver('chartTipos', window.dashboardCharts.chartTipos);
}

function renderChartTendencia() {
    const ctx = document.getElementById('chartTendencia');
    if (!ctx) return;

    window.dashboardCharts.chartTendencia = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: window.chartData.tendencia.labels,
            datasets: [{
                label: 'Total',
                data: window.chartData.tendencia.data,
                backgroundColor: 'rgba(46, 204, 113, 0.7)',
                borderColor: '#2ecc71',
                borderWidth: 2,
                borderRadius: 6
            }]
        },
        options: {
            ...chartDefaults,
            plugins: {
                ...chartDefaults.plugins,
                legend: { display: false }
            }
        }
    });

    setupChartResizeObserver('chartTendencia', window.dashboardCharts.chartTendencia);
}

// ========================================
// INICIALIZAÇÃO DO SISTEMA
// ========================================

document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Inicializando sistema avançado de gráficos...');

    // Setup Intersection Observer
    setupIntersectionObserver();

    // Verificar gráficos inicialmente visíveis
    setTimeout(() => {
        document.querySelectorAll('.chart-wrapper').forEach(wrapper => {
            const canvas = wrapper.querySelector('canvas');
            if (canvas && isElementInViewport(wrapper)) {
                const chartId = canvas.id;
                if (window.chartStates[chartId]) {
                    window.chartStates[chartId].visible = true;
                    renderChartIfReady(chartId);
                }
            }
        });
    }, 100);

    // Resize da janela com debounce
    window.addEventListener('resize', debounce(resizeVisibleCharts, 150));

    // Orientação do dispositivo (mobile)
    window.addEventListener('orientationchange', () => {
        setTimeout(resizeVisibleCharts, 200);
    });

    // Scroll handler otimizado (apenas para debug)
    let scrollTimeout;
    window.addEventListener('scroll', () => {
        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(() => {
            // Verificar se algum gráfico ficou visível durante o scroll
            document.querySelectorAll('.chart-wrapper').forEach(wrapper => {
                const canvas = wrapper.querySelector('canvas');
                if (canvas) {
                    const chartId = canvas.id;
                    const currentlyVisible = isElementInViewport(wrapper);

                    if (currentlyVisible && !window.chartStates[chartId].visible) {
                        console.log(`📜 ${chartId} tornou-se visível durante scroll`);
                        window.chartStates[chartId].visible = true;
                        renderChartIfReady(chartId);
                        forceChartResize(chartId);
                    } else if (!currentlyVisible && window.chartStates[chartId].visible) {
                        window.chartStates[chartId].visible = false;
                    }
                }
            });
        }, 100);
    });
});

// ========================================
// AUTO-REFRESH: Atualiza dados a cada 30 segundos
// ========================================
let isUpdating = false;
const updateInterval = 30000; // 30 segundos

// Função para formatar números
function formatNumber(num) {
    return new Intl.NumberFormat('pt-BR').format(num);
}

// Função para formatar moeda
function formatMoney(value) {
    return new Intl.NumberFormat('pt-BR', { 
        style: 'currency', 
        currency: 'BRL' 
    }).format(value);
}

// Função para atualizar estatísticas
async function updateStats() {
    if (isUpdating) return;
    
    isUpdating = true;
    
    // Mostra indicador de atualização
    const updateIndicator = document.getElementById('update-indicator');
    if (updateIndicator) {
        updateIndicator.style.display = 'flex';
    }
    
    try {
        const response = await fetch('<?= BASE_URL ?>/relatorios/getEstatisticasJson');
        const data = await response.json();
        
        if (data.success) {
            // Atualiza cards de estatísticas
            const stats = data.stats;
            
            // Vendas Hoje
            const vendasHojeEl = document.querySelector('.stat-card:nth-child(1) h3');
            const vendasHojeValorEl = document.querySelector('.stat-card:nth-child(1) small');
            if (vendasHojeEl) {
                vendasHojeEl.textContent = formatNumber(stats.atendimentos_hoje);
            }
            if (vendasHojeValorEl && stats.valor_vendas_hoje) {
                vendasHojeValorEl.textContent = formatMoney(stats.valor_vendas_hoje);
            }
            
            // Vendas no Mês
            const vendasMesEl = document.querySelector('.stat-card:nth-child(2) h3');
            const vendasMesValorEl = document.querySelector('.stat-card:nth-child(2) small');
            if (vendasMesEl) {
                vendasMesEl.textContent = formatNumber(stats.atendimentos_mes);
            }
            if (vendasMesValorEl && stats.valor_vendas_mes) {
                vendasMesValorEl.textContent = formatMoney(stats.valor_vendas_mes);
            }
            
            // Atualiza dados dos gráficos
            if (stats.atendimentos_hoje !== undefined && stats.atendimentos_mes !== undefined) {
                window.chartData.tipos.data = [stats.atendimentos_hoje, stats.atendimentos_mes];
                
                // Atualiza gráfico de tipos se já renderizado
                if (window.dashboardCharts.chartTipos && window.chartStates.chartTipos.rendered) {
                    window.dashboardCharts.chartTipos.data.datasets[0].data = window.chartData.tipos.data;
                    window.dashboardCharts.chartTipos.update('none');
                }
            }
            
            // Atualiza timestamp
            const timestampEl = document.getElementById('last-update');
            if (timestampEl) {
                const now = new Date();
                timestampEl.textContent = 'Última atualização: ' + now.toLocaleTimeString('pt-BR');
            }
            
            // Animação de "flash" nos cards atualizados
            document.querySelectorAll('.stat-card').forEach(card => {
                card.style.animation = 'pulse 0.5s ease';
                setTimeout(() => {
                    card.style.animation = '';
                }, 500);
            });
        }
    } catch (error) {
        console.error('Erro ao atualizar estatísticas:', error);
    } finally {
        isUpdating = false;
        
        // Esconde indicador de atualização
        const updateIndicator = document.getElementById('update-indicator');
        if (updateIndicator) {
            updateIndicator.style.display = 'none';
        }
    }
}

// Inicia atualização automática
setInterval(updateStats, updateInterval);

// ========================================
// CSS AVANÇADO PARA RESPONSIVIDADE E SCROLL
// ========================================

const style = document.createElement('style');
style.textContent = `
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.02); }
    }
    
    #update-indicator {
        position: fixed;
        top: 20px;
        right: 20px;
        background: rgba(52, 152, 219, 0.95);
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        display: none;
        align-items: center;
        gap: 10px;
        z-index: 9999;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        font-size: 14px;
        font-weight: 500;
    }
    
    #update-indicator .spinner {
        border: 3px solid rgba(255,255,255,0.3);
        border-top: 3px solid white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    #last-update {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: rgba(30, 41, 59, 0.9);
        color: #94a3b8;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 12px;
        z-index: 9998;
    }
    
    /* ======================================== */
    /* SISTEMA AVANÇADO DE RESPONSIVIDADE */
    /* ======================================== */
    
    /* Container base para gráficos - ESSENCIAL */
    .chart-wrapper {
        width: 100%;
        position: relative;
        overflow: hidden;
        background: transparent;
        border-radius: 4px;
    }
    
    /* Canvas sempre 100% do container - CRÍTICO */
    .chart-wrapper canvas {
        width: 100% !important;
        height: 100% !important;
        display: block;
        max-width: 100%;
        max-height: 100%;
    }
    
    /* Estado de loading */
    .chart-wrapper.loading {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }
    
    .chart-wrapper.loading canvas {
        opacity: 0;
    }
    
    @keyframes loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
    
    /* ======================================== */
    /* BREAKPOINTS AVANÇADOS */
    /* ======================================== */
    
    /* Desktop (≥1200px) */
    @media (min-width: 1200px) {
        .stats-grid {
            display: flex;
            gap: 20px;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .stat-card {
            flex: 1;
            max-width: 300px;
        }
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        /* Altura confortável para desktop */
        .chart-wrapper {
            min-height: 320px;
            max-height: 400px;
            height: 360px; /* Altura fixa para desktop */
        }
    }
    
    /* Notebook (768px - 1199px) */
    @media (max-width: 1199px) and (min-width: 768px) {
        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 25px;
        }
        .stat-card {
            max-width: none;
        }
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
        }
        /* Altura proporcional */
        .chart-wrapper {
            min-height: 280px;
            max-height: 350px;
            height: 320px; /* Altura consistente */
            aspect-ratio: 16/9;
        }
    }
    
    /* Mobile (≤767px) */
    @media (max-width: 767px) {
        .stats-grid {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 20px;
        }
        .stat-card {
            width: 100%;
            padding: 15px;
        }
        .stat-card h3 {
            font-size: 1.5rem;
        }
        .stat-card p {
            font-size: 0.9rem;
        }
        .grid-2 {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
        .action-btn {
            padding: 12px;
            font-size: 0.8rem;
        }
        .action-btn span {
            font-size: 0.75rem;
        }
        /* Altura maior para legibilidade */
        .chart-wrapper {
            min-height: 250px;
            max-height: 300px;
            height: 280px; /* Altura consistente para mobile */
            aspect-ratio: 4/3;
        }
        .table {
            font-size: 0.8rem;
        }
        .table th, .table td {
            padding: 8px 4px;
        }
        #update-indicator, #last-update {
            font-size: 12px;
            padding: 8px 12px;
        }
        #update-indicator {
            top: 10px;
            right: 10px;
        }
        #last-update {
            bottom: 10px;
            right: 10px;
        }
    }
    
    /* ======================================== */
    /* OTIMIZAÇÕES PARA SCROLL */
    /* ======================================== */
    
    .card {
        margin-bottom: 20px;
        overflow: hidden;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        transition: box-shadow 0.3s ease;
        contain: layout style paint; /* Otimização de performance */
    }
    
    .card:hover {
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    }
    
    /* Previne scroll horizontal */
    * {
        box-sizing: border-box;
    }
    
    body {
        overflow-x: hidden;
        scroll-behavior: smooth;
    }
    
    /* Smooth transitions para gráficos */
    .chart-wrapper canvas {
        transition: opacity 0.3s ease;
        will-change: auto; /* Otimização de performance */
    }
    
    /* Performance: reduzir repaints */
    .chart-wrapper {
        transform: translateZ(0); /* Hardware acceleration */
        backface-visibility: hidden;
    }
`;
document.head.appendChild(style);

// ========================================
// ELEMENTOS VISUAIS
// ========================================

const updateIndicator = document.createElement('div');
updateIndicator.id = 'update-indicator';
updateIndicator.innerHTML = '<div class="spinner"></div><span>Atualizando dados...</span>';
document.body.appendChild(updateIndicator);

const lastUpdate = document.createElement('div');
lastUpdate.id = 'last-update';
lastUpdate.textContent = 'Última atualização: ' + new Date().toLocaleTimeString('pt-BR');
document.body.appendChild(lastUpdate);

// ========================================
// LOG DE INICIALIZAÇÃO
// ========================================

console.log('✅ Sistema avançado de responsividade ativado!');
console.log('👁️ IntersectionObserver configurado para detectar entrada na viewport');
console.log('🔄 ResizeObserver configurado para mudanças no container');
console.log('🎨 Lazy rendering habilitado');
console.log('📊 Gráficos preparados para renderização:', Object.keys(window.chartStates));
console.log('🔄 Auto-refresh ativado! Atualizando a cada ' + (updateInterval/1000) + ' segundos');
</script>

