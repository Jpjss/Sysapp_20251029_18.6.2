<?php
/**
 * Dashboard de Marcas Mais Vendidas em Tempo Real
 * Atualiza apenas os gráficos sem recarregar a página
 */

$pageTitle = 'Dashboard de Marcas - Vendas em Tempo Real';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .dashboard-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .dashboard-header {
            border-bottom: 3px solid #667eea;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .dashboard-header h1 {
            color: #667eea;
            font-weight: 700;
            margin: 0;
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: #e8f5e9;
            color: #2e7d32;
            border-radius: 20px;
            font-weight: 600;
        }
        
        .status-badge .pulse {
            width: 10px;
            height: 10px;
            background: #4caf50;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.2); }
        }
        
        .chart-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }
        
        .chart-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .chart-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .chart-title i {
            color: #667eea;
        }
        
        .controls-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .btn-refresh {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 10px 25px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-refresh:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .last-update {
            color: #666;
            font-size: 0.9rem;
            font-style: italic;
        }
        
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        
        .loading-spinner {
            background: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
        }
        
        .spinner-border {
            color: #667eea;
        }
        
        .table-container {
            overflow-x: auto;
            margin-top: 20px;
        }
        
        .table-marcas {
            background: white;
        }
        
        .table-marcas th {
            background: #667eea;
            color: white;
            font-weight: 600;
        }
        
        .table-marcas tbody tr:hover {
            background: #f8f9fa;
        }
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Carregando...</span>
            </div>
            <p class="mt-3">Atualizando dados...</p>
        </div>
    </div>

    <div class="container-fluid">
        <div class="dashboard-container">
            <!-- Header -->
            <div class="dashboard-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h1><i class="fas fa-chart-line"></i> <?php echo $pageTitle; ?></h1>
                    </div>
                    <div class="col-md-6 text-end">
                        <span class="status-badge">
                            <span class="pulse"></span>
                            Atualização Automática Ativa
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Controles -->
            <div class="controls-section">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Período:</label>
                        <select class="form-select" id="periodoSelect">
                            <option value="7">Últimos 7 dias</option>
                            <option value="15">Últimos 15 dias</option>
                            <option value="30" selected>Últimos 30 dias</option>
                            <option value="60">Últimos 60 dias</option>
                            <option value="90">Últimos 90 dias</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Top Marcas:</label>
                        <select class="form-select" id="limiteSelect">
                            <option value="5">Top 5</option>
                            <option value="10" selected>Top 10</option>
                            <option value="15">Top 15</option>
                            <option value="20">Top 20</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Intervalo de Atualização:</label>
                        <select class="form-select" id="intervaloSelect">
                            <option value="10">10 segundos</option>
                            <option value="30" selected>30 segundos</option>
                            <option value="60">1 minuto</option>
                            <option value="120">2 minutos</option>
                            <option value="300">5 minutos</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">&nbsp;</label>
                        <button class="btn btn-refresh w-100" onclick="atualizarDados()">
                            <i class="fas fa-sync-alt"></i> Atualizar Agora
                        </button>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <p class="last-update mb-0">
                            <i class="fas fa-clock"></i> Última atualização: <span id="lastUpdate">Carregando...</span>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Gráficos -->
            <div class="row">
                <div class="col-lg-6">
                    <div class="chart-card">
                        <div class="chart-title">
                            <i class="fas fa-chart-bar"></i>
                            Quantidade Vendida por Marca
                        </div>
                        <canvas id="chartQuantidade"></canvas>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="chart-card">
                        <div class="chart-title">
                            <i class="fas fa-dollar-sign"></i>
                            Valor Total por Marca (R$)
                        </div>
                        <canvas id="chartValor"></canvas>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-12">
                    <div class="chart-card">
                        <div class="chart-title">
                            <i class="fas fa-shopping-cart"></i>
                            Total de Vendas por Marca
                        </div>
                        <canvas id="chartVendas"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Tabela Detalhada -->
            <div class="chart-card">
                <div class="chart-title">
                    <i class="fas fa-table"></i>
                    Detalhamento das Marcas
                </div>
                <div class="table-container">
                    <table class="table table-marcas table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Código</th>
                                <th>Marca</th>
                                <th class="text-end">Quantidade Vendida</th>
                                <th class="text-end">Total de Vendas</th>
                                <th class="text-end">Valor Total (R$)</th>
                            </tr>
                        </thead>
                        <tbody id="tabelaMarcas">
                            <tr>
                                <td colspan="6" class="text-center">Carregando dados...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Variáveis globais
        let chartQuantidade, chartValor, chartVendas;
        let intervaloAtualizacao;
        
        // Configuração padrão dos gráficos
        const chartOptions = {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14
                    },
                    bodyFont: {
                        size: 13
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        font: {
                            size: 11
                        }
                    }
                },
                x: {
                    ticks: {
                        font: {
                            size: 10
                        },
                        maxRotation: 45,
                        minRotation: 45
                    }
                }
            }
        };
        
        // Inicializar gráficos vazios
        function inicializarGraficos() {
            const ctxQuantidade = document.getElementById('chartQuantidade').getContext('2d');
            const ctxValor = document.getElementById('chartValor').getContext('2d');
            const ctxVendas = document.getElementById('chartVendas').getContext('2d');
            
            chartQuantidade = new Chart(ctxQuantidade, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Quantidade',
                        data: [],
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2
                    }]
                },
                options: chartOptions
            });
            
            chartValor = new Chart(ctxValor, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Valor (R$)',
                        data: [],
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 2
                    }]
                },
                options: chartOptions
            });
            
            chartVendas = new Chart(ctxVendas, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Total de Vendas',
                        data: [],
                        backgroundColor: 'rgba(255, 159, 64, 0.2)',
                        borderColor: 'rgba(255, 159, 64, 1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: chartOptions
            });
        }
        
        // Atualizar dados dos gráficos
        async function atualizarDados() {
            const periodo = document.getElementById('periodoSelect').value;
            const limite = document.getElementById('limiteSelect').value;
            
            // Mostrar loading
            document.getElementById('loadingOverlay').style.display = 'flex';
            
            try {
                const response = await fetch(`/api/marcas_vendas.php?periodo=${periodo}&limite=${limite}`);
                const result = await response.json();
                
                if (result.success) {
                    // Atualizar gráficos
                    const labels = result.data.labels;
                    
                    // Gráfico de Quantidade
                    chartQuantidade.data.labels = labels;
                    chartQuantidade.data.datasets[0].data = result.data.datasets[0].data;
                    chartQuantidade.update('none'); // Sem animação para atualização suave
                    
                    // Gráfico de Valor
                    chartValor.data.labels = labels;
                    chartValor.data.datasets[0].data = result.data.datasets[1].data;
                    chartValor.update('none');
                    
                    // Gráfico de Vendas
                    chartVendas.data.labels = labels;
                    chartVendas.data.datasets[0].data = result.data.datasets[2].data;
                    chartVendas.update('none');
                    
                    // Atualizar tabela
                    atualizarTabela(result.marcas_detalhadas);
                    
                    // Atualizar timestamp
                    document.getElementById('lastUpdate').textContent = result.timestamp;
                } else {
                    console.error('Erro ao carregar dados:', result.error);
                    alert('Erro ao atualizar dados: ' + result.error);
                }
            } catch (error) {
                console.error('Erro na requisição:', error);
                alert('Erro ao conectar com o servidor');
            } finally {
                // Esconder loading
                document.getElementById('loadingOverlay').style.display = 'none';
            }
        }
        
        // Atualizar tabela
        function atualizarTabela(marcas) {
            const tbody = document.getElementById('tabelaMarcas');
            tbody.innerHTML = '';
            
            if (marcas.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center">Nenhuma marca encontrada</td></tr>';
                return;
            }
            
            marcas.forEach((marca, index) => {
                const row = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${marca.cd_marca}</td>
                        <td><strong>${marca.ds_marca}</strong></td>
                        <td class="text-end">${parseInt(marca.quantidade_vendida).toLocaleString('pt-BR')}</td>
                        <td class="text-end">${parseInt(marca.total_vendas).toLocaleString('pt-BR')}</td>
                        <td class="text-end">R$ ${parseFloat(marca.valor_total).toLocaleString('pt-BR', {minimumFractionDigits: 2})}</td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        }
        
        // Configurar intervalo de atualização automática
        function configurarIntervalo() {
            if (intervaloAtualizacao) {
                clearInterval(intervaloAtualizacao);
            }
            
            const intervalo = parseInt(document.getElementById('intervaloSelect').value) * 1000;
            intervaloAtualizacao = setInterval(atualizarDados, intervalo);
        }
        
        // Event Listeners
        document.getElementById('periodoSelect').addEventListener('change', atualizarDados);
        document.getElementById('limiteSelect').addEventListener('change', atualizarDados);
        document.getElementById('intervaloSelect').addEventListener('change', configurarIntervalo);
        
        // Inicialização
        document.addEventListener('DOMContentLoaded', function() {
            inicializarGraficos();
            atualizarDados();
            configurarIntervalo();
        });
    </script>
</body>
</html>
