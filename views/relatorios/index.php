<div class="page-header">
    <h2>Dashboard</h2>
    <?php if (Session::check('Config.empresa')): ?>
        <span class="empresa-badge"><?= htmlspecialchars(Session::read('Config.empresa')) ?></span>
    <?php endif; ?>
</div>

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
            <h3><?= number_format($stats['total_questionarios'], 0, ',', '.') ?></h3>
            <p>Questionários</p>
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
            <p>Atendimentos Hoje</p>
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
            <p>Atendimentos no Mês</p>
        </div>
    </div>
</div>

<!-- Gráfico de Atendimentos -->
<div class="card">
    <h3>Atendimentos dos Últimos 7 Dias</h3>
    <canvas id="chartAtendimentos" width="400" height="100"></canvas>
</div>

<!-- Top Clientes -->
<div class="card">
    <h3>Top 5 Clientes Mais Atendidos</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Total Atendimentos</th>
                <th>Último Atendimento</th>
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
                    <td colspan="4" class="text-center">Nenhum atendimento registrado</td>
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
            label: 'Atendimentos',
            data: data,
            borderColor: '#3498db',
            backgroundColor: 'rgba(52, 152, 219, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
</script>
