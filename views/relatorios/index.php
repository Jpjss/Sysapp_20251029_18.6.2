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
        <div class="stat-icon" style="background: #3498db;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
        </div>
        <div class="stat-content">
            <h3><?= number_format($stats['total_clientes'], 0, ',', '.') ?></h3>
            <p>Total de Clientes</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #2ecc71;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
                <polyline points="10 9 9 9 8 9"></polyline>
            </svg>
        </div>
        <div class="stat-content">
            <h3><?= number_format($stats['total_respostas'], 0, ',', '.') ?></h3>
            <p><?= $stats['total_questionarios'] == 0 ? 'Total de Vendas' : 'Questionários' ?></p>
            <?php if ($stats['total_questionarios'] == 0 && isset($stats['valor_total_vendas'])): ?>
                <small style="color: #2ecc71; font-weight: 600;">
                    R$ <?= number_format($stats['valor_total_vendas'], 2, ',', '.') ?>
                </small>
            <?php endif; ?>
        </div>
    </div>
    
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
    <canvas id="chartAtendimentos" width="400" height="100"></canvas>
</div>

<!-- Gráficos Adicionais -->
<div class="grid-2">
    <div class="card">
        <h3>Distribuição por Tipo</h3>
        <canvas id="chartTipos" height="200"></canvas>
    </div>
    
    <div class="card">
        <h3>Tendência Mensal</h3>
        <canvas id="chartTendencia" height="200"></canvas>
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
// Gráfico de atendimentos
const ctx = document.getElementById('chartAtendimentos').getContext('2d');
const chartData = <?= json_encode($atendimentosPeriodo) ?>;

const labels = chartData.map(item => {
    const date = new Date(item.data);
    return date.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' });
});

const data = chartData.map(item => parseInt(item.total));

new Chart(ctx, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: '<?= $stats['total_questionarios'] == 0 ? "Vendas" : "Atendimentos" ?>',
            data: data,
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
        responsive: true,
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
    }
});

// Gráfico de Distribuição por Tipo (Pizza)
const ctxTipos = document.getElementById('chartTipos');
if (ctxTipos) {
    new Chart(ctxTipos.getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Clientes Ativos', 'Atendimentos Hoje', 'Atendimentos no Mês'],
            datasets: [{
                data: [
                    <?= $stats['total_clientes'] ?>,
                    <?= $stats['atendimentos_hoje'] ?>,
                    <?= $stats['atendimentos_mes'] ?>
                ],
                backgroundColor: [
                    'rgba(52, 152, 219, 0.8)',
                    'rgba(231, 76, 60, 0.8)',
                    'rgba(243, 156, 18, 0.8)'
                ],
                borderColor: [
                    '#3498db',
                    '#e74c3c',
                    '#f39c12'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: { size: 12 }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(30, 41, 59, 0.95)',
                    titleFont: { size: 14, weight: 'bold' },
                    bodyFont: { size: 13 },
                    padding: 12
                }
            }
        }
    });
}

// Gráfico de Tendência (Barras)
const ctxTendencia = document.getElementById('chartTendencia');
if (ctxTendencia) {
    new Chart(ctxTendencia.getContext('2d'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total',
                data: data,
                backgroundColor: 'rgba(46, 204, 113, 0.7)',
                borderColor: '#2ecc71',
                borderWidth: 2,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(30, 41, 59, 0.95)',
                    titleFont: { size: 14, weight: 'bold' },
                    bodyFont: { size: 13 },
                    padding: 12
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    },
                    grid: {
                        color: 'rgba(203, 213, 225, 0.3)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

// ========================================
// AUTO-REFRESH: Atualiza dados a cada 30 segundos
// ========================================
let isUpdating = false;
const updateInterval = 30000; // 30 segundos (pode ajustar: 60000 = 1 minuto)

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
            
            // Total de Clientes
            const totalClientesEl = document.querySelector('.stat-card:nth-child(1) h3');
            if (totalClientesEl) {
                totalClientesEl.textContent = formatNumber(stats.total_clientes);
            }
            
            // Total de Vendas
            const totalVendasEl = document.querySelector('.stat-card:nth-child(2) h3');
            const totalVendasValorEl = document.querySelector('.stat-card:nth-child(2) small');
            if (totalVendasEl) {
                totalVendasEl.textContent = formatNumber(stats.total_respostas);
            }
            if (totalVendasValorEl && stats.valor_total_vendas) {
                totalVendasValorEl.textContent = formatMoney(stats.valor_total_vendas);
            }
            
            // Vendas Hoje
            const vendasHojeEl = document.querySelector('.stat-card:nth-child(3) h3');
            const vendasHojeValorEl = document.querySelector('.stat-card:nth-child(3) small');
            if (vendasHojeEl) {
                vendasHojeEl.textContent = formatNumber(stats.atendimentos_hoje);
            }
            if (vendasHojeValorEl && stats.valor_vendas_hoje) {
                vendasHojeValorEl.textContent = formatMoney(stats.valor_vendas_hoje);
            }
            
            // Vendas no Mês
            const vendasMesEl = document.querySelector('.stat-card:nth-child(4) h3');
            const vendasMesValorEl = document.querySelector('.stat-card:nth-child(4) small');
            if (vendasMesEl) {
                vendasMesEl.textContent = formatNumber(stats.atendimentos_mes);
            }
            if (vendasMesValorEl && stats.valor_vendas_mes) {
                vendasMesValorEl.textContent = formatMoney(stats.valor_vendas_mes);
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

// Adiciona animação CSS
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
`;
document.head.appendChild(style);

// Adiciona elementos visuais
const updateIndicator = document.createElement('div');
updateIndicator.id = 'update-indicator';
updateIndicator.innerHTML = '<div class="spinner"></div><span>Atualizando dados...</span>';
document.body.appendChild(updateIndicator);

const lastUpdate = document.createElement('div');
lastUpdate.id = 'last-update';
lastUpdate.textContent = 'Última atualização: ' + new Date().toLocaleTimeString('pt-BR');
document.body.appendChild(lastUpdate);

console.log('✅ Auto-refresh ativado! Atualizando a cada ' + (updateInterval/1000) + ' segundos');
</script>

