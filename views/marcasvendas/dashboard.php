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
            position: relative;
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
        
        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .brand-selector-box {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f8f9fa;
            padding: 8px 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.08);
            border: 1px solid #dee2e6;
        }
        
        .brand-selector-box label {
            font-size: 0.9rem;
            font-weight: 600;
            color: #495057;
            margin: 0;
            white-space: nowrap;
        }
        
        .brand-selector-box select {
            border: 1px solid #ced4da;
            border-radius: 5px;
            padding: 5px 10px;
            font-size: 0.9rem;
            min-width: 200px;
            background: white;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .brand-selector-box select:hover {
            border-color: #667eea;
        }
        
        .brand-selector-box select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .btn-back-overview {
            display: none;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
        }
        
        .btn-back-overview:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        
        .btn-back-overview i {
            margin-right: 5px;
        }
        
        .chart-subtitle {
            font-size: 0.85rem;
            color: #6c757d;
            font-style: italic;
            margin-top: -10px;
            margin-bottom: 10px;
        }
        
        .stats-summary {
            display: none;
            background: #e8f5e9;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
            border-left: 4px solid #4caf50;
        }
        
        .stats-summary.active {
            display: block;
        }
        
        .stats-summary-content {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            font-size: 0.9rem;
        }
        
        .stat-item {
            display: flex;
            flex-direction: column;
        }
        
        .stat-label {
            font-weight: 600;
            color: #2e7d32;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .stat-value {
            color: #1b5e20;
            font-size: 1.1rem;
            font-weight: 700;
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
        
        .sortable-header {
            cursor: pointer;
            user-select: none;
            position: relative;
            padding-right: 25px !important;
            transition: background-color 0.2s ease;
        }
        
        .sortable-header:hover {
            background: #5a66d0 !important;
        }
        
        .sort-icon {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            display: inline-flex;
            flex-direction: column;
            gap: 2px;
            opacity: 0.4;
            transition: opacity 0.2s ease;
        }
        
        .sortable-header:hover .sort-icon {
            opacity: 0.7;
        }
        
        .sort-icon.active {
            opacity: 1 !important;
        }
        
        .sort-arrow {
            width: 0;
            height: 0;
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
        }
        
        .sort-arrow-up {
            border-bottom: 5px solid white;
        }
        
        .sort-arrow-down {
            border-top: 5px solid white;
        }
        
        .sort-arrow.active-up {
            border-bottom-color: #ffd700;
        }
        
        .sort-arrow.active-down {
            border-top-color: #ffd700;
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
                        <div class="chart-header">
                            <div class="chart-title">
                                <i class="fas fa-shopping-cart"></i>
                                <span id="chartVendasTitle">Total de Vendas por Marca</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <button class="btn-back-overview" id="btnBackOverview" onclick="voltarVisaoGeral()">
                                    <i class="fas fa-arrow-left"></i> Voltar para visão geral
                                </button>
                                <div class="brand-selector-box" id="brandSelectorBox">
                                    <label for="brandSelect">
                                        <i class="fas fa-filter"></i> Selecionar Marca:
                                    </label>
                                    <select id="brandSelect" class="form-select-sm">
                                        <option value="">Carregando marcas...</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="chart-subtitle" id="chartVendasSubtitle"></div>
                        <div class="stats-summary" id="statsSummary">
                            <div class="stats-summary-content">
                                <div class="stat-item">
                                    <span class="stat-label">Total de Vendas</span>
                                    <span class="stat-value" id="statTotalVendas">-</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-label">Quantidade Total</span>
                                    <span class="stat-value" id="statQuantidadeTotal">-</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-label">Valor Total</span>
                                    <span class="stat-value" id="statValorTotal">-</span>
                                </div>
                            </div>
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
                                <th class="text-end sortable-header" onclick="ordenarPorQuantidade()" id="headerQuantidade">
                                    Quantidade Vendida
                                    <span class="sort-icon" id="sortIconQuantidade">
                                        <span class="sort-arrow sort-arrow-up"></span>
                                        <span class="sort-arrow sort-arrow-down"></span>
                                    </span>
                                </th>
                                <th class="text-end">Total de Vendas</th>
                                <th class="text-end sortable-header" onclick="ordenarPorValor()" id="headerValorTotal">
                                    Valor Total (R$)
                                    <span class="sort-icon" id="sortIconValor">
                                        <span class="sort-arrow sort-arrow-up"></span>
                                        <span class="sort-arrow sort-arrow-down"></span>
                                    </span>
                                </th>
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
        let marcasTop10 = []; // Armazena as Top 10 marcas
        let modoVisualizacao = 'overview'; // 'overview' ou 'detalhado'
        let marcaSelecionada = null;
        let ordenacaoAtual = 'desc'; // 'desc' = maior para menor, 'asc' = menor para maior
        let colunaOrdenacao = 'valor'; // 'valor' ou 'quantidade'
        let marcasOriginais = []; // Armazena dados originais para ordenação
        
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
            },
            animation: {
                duration: 750,
                easing: 'easeInOutQuart'
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
        
        // Atualizar dados dos gráficos - Visão Geral
        async function atualizarDados() {
            const periodo = document.getElementById('periodoSelect').value;
            const limite = document.getElementById('limiteSelect').value;
            
            // Mostrar loading
            document.getElementById('loadingOverlay').style.display = 'flex';
            
            try {
                const response = await fetch(`/api/marcas_vendas.php?periodo=${periodo}&limite=${limite}`);
                const result = await response.json();
                
                if (result.success) {
                    // Armazenar Top 10
                    marcasTop10 = result.marcas_detalhadas || [];
                    
                    // Atualizar dropdown de marcas
                    atualizarDropdownMarcas();
                    
                    // Atualizar gráficos
                    const labels = result.data.labels;
                    
                    // Gráfico de Quantidade
                    chartQuantidade.data.labels = labels;
                    chartQuantidade.data.datasets[0].data = result.data.datasets[0].data;
                    chartQuantidade.update('active');
                    
                    // Gráfico de Valor
                    chartValor.data.labels = labels;
                    chartValor.data.datasets[0].data = result.data.datasets[1].data;
                    chartValor.update('active');
                    
                    // Gráfico de Vendas - Modo Overview
                    chartVendas.data.labels = labels;
                    chartVendas.data.datasets[0].data = result.data.datasets[2].data;
                    chartVendas.data.datasets[0].label = 'Total de Vendas';
                    chartVendas.data.datasets[0].backgroundColor = 'rgba(255, 159, 64, 0.2)';
                    chartVendas.data.datasets[0].borderColor = 'rgba(255, 159, 64, 1)';
                    chartVendas.options.scales.x.title = { display: false };
                    chartVendas.update('active');
                    
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
        
        // Atualizar dropdown de marcas
        function atualizarDropdownMarcas() {
            const select = document.getElementById('brandSelect');
            select.innerHTML = '<option value="">-- Selecione uma marca --</option>';
            
            marcasTop10.forEach(marca => {
                const option = document.createElement('option');
                option.value = marca.cd_marca;
                option.textContent = marca.ds_marca;
                select.appendChild(option);
            });
        }
        
        // Carregar dados históricos de uma marca específica
        async function carregarHistoricoMarca(cd_marca) {
            if (!cd_marca) {
                voltarVisaoGeral();
                return;
            }
            
            const periodo = document.getElementById('periodoSelect').value;
            
            // Mostrar loading
            document.getElementById('loadingOverlay').style.display = 'flex';
            
            try {
                const response = await fetch(`/api/marca_historico.php?cd_marca=${cd_marca}&periodo=${periodo}&agrupamento=dia`);
                const result = await response.json();
                
                if (result.success) {
                    marcaSelecionada = result;
                    modoVisualizacao = 'detalhado';
                    
                    // Atualizar apenas o gráfico de vendas
                    chartVendas.data.labels = result.data.labels;
                    chartVendas.data.datasets[0].data = result.data.datasets[2].data; // Total de vendas
                    chartVendas.data.datasets[0].label = `Vendas - ${result.ds_marca}`;
                    chartVendas.data.datasets[0].backgroundColor = 'rgba(102, 126, 234, 0.2)';
                    chartVendas.data.datasets[0].borderColor = 'rgba(102, 126, 234, 1)';
                    chartVendas.options.scales.x.title = { 
                        display: true, 
                        text: 'Período',
                        font: { size: 12, weight: 'bold' }
                    };
                    chartVendas.update('active');
                    
                    // Atualizar UI
                    document.getElementById('chartVendasTitle').textContent = `Progresso de Vendas – ${result.ds_marca}`;
                    document.getElementById('chartVendasSubtitle').textContent = `Acompanhamento diário nos últimos ${periodo} dias`;
                    document.getElementById('btnBackOverview').style.display = 'inline-block';
                    
                    // Mostrar estatísticas
                    const statsSummary = document.getElementById('statsSummary');
                    statsSummary.classList.add('active');
                    document.getElementById('statTotalVendas').textContent = result.totais.vendas.toLocaleString('pt-BR');
                    document.getElementById('statQuantidadeTotal').textContent = result.totais.quantidade.toLocaleString('pt-BR') + ' unidades';
                    document.getElementById('statValorTotal').textContent = 'R$ ' + result.totais.valor.toLocaleString('pt-BR', {minimumFractionDigits: 2});
                    
                    // Atualizar timestamp
                    document.getElementById('lastUpdate').textContent = result.timestamp;
                } else {
                    console.error('Erro ao carregar histórico:', result.error);
                    alert('Erro ao carregar histórico da marca: ' + result.error);
                }
            } catch (error) {
                console.error('Erro na requisição:', error);
                alert('Erro ao conectar com o servidor');
            } finally {
                // Esconder loading
                document.getElementById('loadingOverlay').style.display = 'none';
            }
        }
        
        // Voltar para visão geral
        function voltarVisaoGeral() {
            modoVisualizacao = 'overview';
            marcaSelecionada = null;
            
            // Resetar dropdown
            document.getElementById('brandSelect').value = '';
            
            // Esconder botão voltar
            document.getElementById('btnBackOverview').style.display = 'none';
            
            // Esconder estatísticas
            document.getElementById('statsSummary').classList.remove('active');
            
            // Resetar título
            document.getElementById('chartVendasTitle').textContent = 'Total de Vendas por Marca';
            document.getElementById('chartVendasSubtitle').textContent = '';
            
            // Recarregar dados gerais
            atualizarDados();
        }
        
        // Atualizar tabela
        function atualizarTabela(marcas) {
            // Armazenar dados originais
            marcasOriginais = [...marcas];
            
            // Aplicar ordenação atual
            const marcasOrdenadas = ordenarMarcas(marcasOriginais, colunaOrdenacao, ordenacaoAtual);
            
            const tbody = document.getElementById('tabelaMarcas');
            tbody.innerHTML = '';
            
            if (marcasOrdenadas.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center">Nenhuma marca encontrada</td></tr>';
                return;
            }
            
            marcasOrdenadas.forEach((marca, index) => {
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
            
            // Atualizar ícone de ordenação
            atualizarIconeOrdenacao();
        }
        
        // Ordenar marcas por valor total ou quantidade
        function ordenarMarcas(marcas, coluna, ordem) {
            const marcasClone = [...marcas];
            
            marcasClone.sort((a, b) => {
                let valorA, valorB;
                
                if (coluna === 'valor') {
                    valorA = parseFloat(a.valor_total);
                    valorB = parseFloat(b.valor_total);
                } else if (coluna === 'quantidade') {
                    valorA = parseInt(a.quantidade_vendida);
                    valorB = parseInt(b.quantidade_vendida);
                }
                
                if (ordem === 'desc') {
                    return valorB - valorA; // Maior para menor
                } else {
                    return valorA - valorB; // Menor para maior
                }
            });
            
            return marcasClone;
        }
        
        // Função chamada ao clicar no cabeçalho da coluna Valor Total
        function ordenarPorValor() {
            // Se já estava ordenando por valor, alternar direção
            if (colunaOrdenacao === 'valor') {
                ordenacaoAtual = ordenacaoAtual === 'desc' ? 'asc' : 'desc';
            } else {
                // Se estava ordenando por outra coluna, definir valor como coluna e desc como padrão
                colunaOrdenacao = 'valor';
                ordenacaoAtual = 'desc';
            }
            
            // Reordenar e atualizar tabela
            atualizarTabela(marcasOriginais);
        }
        
        // Função chamada ao clicar no cabeçalho da coluna Quantidade Vendida
        function ordenarPorQuantidade() {
            // Se já estava ordenando por quantidade, alternar direção
            if (colunaOrdenacao === 'quantidade') {
                ordenacaoAtual = ordenacaoAtual === 'desc' ? 'asc' : 'desc';
            } else {
                // Se estava ordenando por outra coluna, definir quantidade como coluna e desc como padrão
                colunaOrdenacao = 'quantidade';
                ordenacaoAtual = 'desc';
            }
            
            // Reordenar e atualizar tabela
            atualizarTabela(marcasOriginais);
        }
        
        // Atualizar ícone de ordenação visual
        function atualizarIconeOrdenacao() {
            // Resetar todos os ícones
            const sortIconValor = document.getElementById('sortIconValor');
            const sortIconQuantidade = document.getElementById('sortIconQuantidade');
            
            // Remover classes ativas de ambos
            if (sortIconValor) {
                sortIconValor.classList.remove('active');
                sortIconValor.querySelectorAll('.sort-arrow').forEach(arrow => {
                    arrow.classList.remove('active-up', 'active-down');
                });
            }
            
            if (sortIconQuantidade) {
                sortIconQuantidade.classList.remove('active');
                sortIconQuantidade.querySelectorAll('.sort-arrow').forEach(arrow => {
                    arrow.classList.remove('active-up', 'active-down');
                });
            }
            
            // Ativar o ícone correto
            let sortIconAtivo;
            if (colunaOrdenacao === 'valor') {
                sortIconAtivo = sortIconValor;
            } else if (colunaOrdenacao === 'quantidade') {
                sortIconAtivo = sortIconQuantidade;
            }
            
            if (sortIconAtivo) {
                const arrowUp = sortIconAtivo.querySelector('.sort-arrow-up');
                const arrowDown = sortIconAtivo.querySelector('.sort-arrow-down');
                
                sortIconAtivo.classList.add('active');
                
                // Adicionar classe ativa conforme ordenação
                if (ordenacaoAtual === 'desc') {
                    arrowDown.classList.add('active-down');
                } else {
                    arrowUp.classList.add('active-up');
                }
            }
        }
        
        // Configurar intervalo de atualização automática
        function configurarIntervalo() {
            if (intervaloAtualizacao) {
                clearInterval(intervaloAtualizacao);
            }
            
            const intervalo = parseInt(document.getElementById('intervaloSelect').value) * 1000;
            intervaloAtualizacao = setInterval(() => {
                if (modoVisualizacao === 'overview') {
                    atualizarDados();
                } else if (marcaSelecionada) {
                    carregarHistoricoMarca(marcaSelecionada.cd_marca);
                }
            }, intervalo);
        }
        
        // Event Listeners
        document.getElementById('periodoSelect').addEventListener('change', () => {
            if (modoVisualizacao === 'overview') {
                atualizarDados();
            } else if (marcaSelecionada) {
                carregarHistoricoMarca(marcaSelecionada.cd_marca);
            }
        });
        
        document.getElementById('limiteSelect').addEventListener('change', atualizarDados);
        document.getElementById('intervaloSelect').addEventListener('change', configurarIntervalo);
        
        // Event listener para o dropdown de marcas
        document.getElementById('brandSelect').addEventListener('change', function() {
            const cd_marca = this.value;
            if (cd_marca) {
                carregarHistoricoMarca(cd_marca);
            } else {
                voltarVisaoGeral();
            }
        });
        
        // Inicialização
        document.addEventListener('DOMContentLoaded', function() {
            inicializarGraficos();
            atualizarDados();
            configurarIntervalo();
        });
    </script>
</body>
</html>
