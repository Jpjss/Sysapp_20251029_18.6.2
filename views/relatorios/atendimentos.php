<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Atendimentos - SysApp</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--accent-1) 0%, var(--accent-3) 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .page-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Header */
        .page-header {
            background: white;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-header h1 {
            color: #2c3e50;
            font-size: 28px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .page-header .icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--accent-1) 0%, var(--accent-3) 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent-1) 0%, var(--accent-3) 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
        }

        /* Card de Filtros */
        .filter-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .filter-card h3 {
            color: #2c3e50;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .filter-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: 600;
            color: #475569;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-control {
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent-1);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .button-group {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        /* Cards de Resumo */
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .summary-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .summary-card:hover {
            transform: translateY(-4px);
        }

        .summary-card .card-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            color: white;
        }

        .summary-card h4 {
            color: #64748b;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .summary-card .value {
            font-size: 32px;
            font-weight: 700;
            color: #1e293b;
        }

        .summary-card .sub-value {
            font-size: 14px;
            color: #64748b;
            margin-top: 8px;
        }

        /* Card de Dados */
        .data-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .data-card h3 {
            color: #2c3e50;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Tabela */
        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table thead {
            background: linear-gradient(135deg, var(--accent-1) 0%, var(--accent-3) 100%);
            color: white;
        }

        table thead th {
            padding: 16px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
        }

        table tbody tr {
            border-bottom: 1px solid #e2e8f0;
            transition: background 0.2s;
        }

        table tbody tr:hover {
            background: #f8fafc;
        }

        table tbody td {
            padding: 16px;
            color: #475569;
            font-size: 14px;
        }

        table tfoot {
            background: #f8fafc;
            font-weight: 700;
        }

        table tfoot td {
            padding: 16px;
            color: #1e293b;
            border-top: 2px solid #cbd5e1;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #64748b;
        }

        .empty-state svg {
            width: 80px;
            height: 80px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .empty-state h3 {
            color: #475569;
            margin-bottom: 8px;
        }

        .empty-state p {
            color: #94a3b8;
        }

        /* Loading Overlay */
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }

        .loading-overlay.active {
            display: flex;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Responsivo */
        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }

            .filter-row {
                grid-template-columns: 1fr;
            }

            .summary-cards {
                grid-template-columns: 1fr;
            }

            .button-group {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="page-container">
        <!-- Header -->
        <div class="page-header">
            <h1>
                <div class="icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 11l3 3L22 4"></path>
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                    </svg>
                </div>
                Relatório de Atendimentos
            </h1>
            <a href="<?= BASE_URL ?>/relatorios/index" class="btn btn-secondary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Voltar
            </a>
        </div>

        <!-- Filtros -->
        <div class="filter-card">
            <h3>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                </svg>
                Filtros
            </h3>
            <form method="GET" action="<?= BASE_URL ?>/relatorios/atendimentos" id="filterForm">
                <div class="filter-row">
                    <div class="form-group">
                        <label for="dt_inicio">Data Início</label>
                        <input type="date" 
                               id="dt_inicio" 
                               name="dt_inicio" 
                               value="<?= $dt_inicio ?? date('Y-m-01') ?>"
                               class="form-control"
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label for="dt_fim">Data Fim</label>
                        <input type="date" 
                               id="dt_fim" 
                               name="dt_fim" 
                               value="<?= $dt_fim ?? date('Y-m-d') ?>"
                               class="form-control"
                               required>
                    </div>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn btn-primary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.35-4.35"></path>
                        </svg>
                        Filtrar
                    </button>
                    
                    <button type="button" class="btn btn-success" onclick="exportarPDF()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                        Exportar PDF
                    </button>
                    
                    <button type="button" class="btn btn-success" onclick="exportarExcel()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <rect x="8" y="12" width="8" height="6"></rect>
                        </svg>
                        Exportar Excel
                    </button>
                </div>
            </form>
        </div>

        <?php if (!empty($atendimentos)): ?>
        <!-- Cards de Resumo -->
        <div class="summary-cards">
            <div class="summary-card">
                <div class="card-icon" style="background: linear-gradient(135deg, var(--accent-1) 0%, var(--accent-2) 100%);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 11l3 3L22 4"></path>
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                    </svg>
                </div>
                <h4>Total de Atendimentos</h4>
                <div class="value"><?= number_format($totais['total_atendimentos'], 0, ',', '.') ?></div>
            </div>

            <div class="summary-card">
                <div class="card-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
                <h4>Clientes Únicos</h4>
                <div class="value"><?= number_format($totais['clientes_unicos'], 0, ',', '.') ?></div>
            </div>

            <div class="summary-card">
                <div class="card-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <h4>Tempo Total</h4>
                <div class="value"><?= $totais['tempo_total_formatado'] ?></div>
                <div class="sub-value"><?= $totais['tempo_medio_formatado'] ?> por atendimento</div>
            </div>

            <div class="summary-card">
                <div class="card-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="1" x2="12" y2="23"></line>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                    </svg>
                </div>
                <h4>Valor Total</h4>
                <div class="value">R$ <?= number_format($totais['valor_total'], 2, ',', '.') ?></div>
                <div class="sub-value">R$ <?= number_format($totais['ticket_medio'], 2, ',', '.') ?> ticket médio</div>
            </div>
        </div>

        <!-- Tabela de Dados -->
        <div class="data-card">
            <h3>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
                Atendimentos por Dia
            </h3>
            
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th class="text-right">Total de Atendimentos</th>
                            <th class="text-right">Clientes Únicos</th>
                            <th class="text-right">Tempo Total</th>
                            <th class="text-right">Valor Total (R$)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($atendimentos as $item): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($item['data'])) ?></td>
                            <td class="text-right"><?= number_format($item['total_atendimentos'], 0, ',', '.') ?></td>
                            <td class="text-right"><?= number_format($item['clientes_unicos'], 0, ',', '.') ?></td>
                            <td class="text-right"><?= $item['tempo_total_formatado'] ?></td>
                            <td class="text-right">R$ <?= number_format($item['valor_total'], 2, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td><strong>TOTAL</strong></td>
                            <td class="text-right"><strong><?= number_format($totais['total_atendimentos'], 0, ',', '.') ?></strong></td>
                            <td class="text-right"><strong><?= number_format($totais['clientes_unicos'], 0, ',', '.') ?></strong></td>
                            <td class="text-right"><strong><?= $totais['tempo_total_formatado'] ?></strong></td>
                            <td class="text-right"><strong>R$ <?= number_format($totais['valor_total'], 2, ',', '.') ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <?php else: ?>
        <!-- Estado Vazio -->
        <div class="data-card">
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <h3>Nenhum atendimento encontrado</h3>
                <p>Não há atendimentos no período selecionado. Tente ajustar os filtros.</p>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner"></div>
    </div>

    <script>
        function exportarPDF() {
            const dtInicio = document.getElementById('dt_inicio').value;
            const dtFim = document.getElementById('dt_fim').value;
            
            if (!dtInicio || !dtFim) {
                alert('Por favor, selecione o período antes de exportar.');
                return;
            }
            
            const loadingOverlay = document.getElementById('loadingOverlay');
            loadingOverlay.classList.add('active');
            
            window.location.href = `<?= BASE_URL ?>/relatorios/exportarPDF?dt_inicio=${dtInicio}&dt_fim=${dtFim}`;
            
            setTimeout(() => {
                loadingOverlay.classList.remove('active');
            }, 2000);
        }

        function exportarExcel() {
            const dtInicio = document.getElementById('dt_inicio').value;
            const dtFim = document.getElementById('dt_fim').value;
            
            if (!dtInicio || !dtFim) {
                alert('Por favor, selecione o período antes de exportar.');
                return;
            }
            
            const loadingOverlay = document.getElementById('loadingOverlay');
            loadingOverlay.classList.add('active');
            
            window.location.href = `<?= BASE_URL ?>/relatorios/exportarExcel?dt_inicio=${dtInicio}&dt_fim=${dtFim}`;
            
            setTimeout(() => {
                loadingOverlay.classList.remove('active');
            }, 2000);
        }
    </script>
</body>
</html>
