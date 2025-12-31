<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório Entrada x Vendas - SysApp</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .page-container {
            max-width: 1600px;
            margin: 0 auto;
        }

        /* Header */
        .page-header {
            background: white;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .header-info {
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            font-size: 14px;
            color: #64748b;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-item strong {
            color: #475569;
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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

        .filter-section {
            margin-bottom: 25px;
            padding-bottom: 25px;
            border-bottom: 1px solid #e2e8f0;
        }

        .filter-section:last-child {
            border-bottom: none;
        }

        .filter-section-title {
            font-weight: 700;
            color: #475569;
            margin-bottom: 15px;
            font-size: 15px;
        }

        .filter-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 15px;
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
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .checkbox-group {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .checkbox-item input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .checkbox-item label {
            cursor: pointer;
            color: #475569;
            font-size: 14px;
        }

        .button-group {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        /* Tabela */
        .data-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        table thead th {
            padding: 14px 10px;
            text-align: left;
            font-weight: 600;
            font-size: 12px;
            white-space: nowrap;
        }

        table thead th.text-right {
            text-align: right;
        }

        table thead th.text-center {
            text-align: center;
        }

        table tbody tr {
            border-bottom: 1px solid #e2e8f0;
            transition: background 0.2s;
        }

        table tbody tr:hover {
            background: #f8fafc;
        }

        table tbody td {
            padding: 12px 10px;
            color: #475569;
        }

        table tbody tr.filial-header {
            background: #f1f5f9;
            font-weight: 700;
            color: #1e293b;
        }

        table tbody tr.filial-header td {
            padding: 16px 10px;
            font-size: 14px;
        }

        table tbody tr.filial-subtotal {
            background: #f8fafc;
            font-weight: 600;
            color: #334155;
        }

        table tfoot {
            background: #f8fafc;
            font-weight: 700;
        }

        table tfoot td {
            padding: 16px 10px;
            color: #1e293b;
            border-top: 2px solid #cbd5e1;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .badge-positive {
            color: #10b981;
            font-weight: 600;
        }

        .badge-negative {
            color: #ef4444;
            font-weight: 600;
        }

        .badge-zero {
            color: #94a3b8;
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
            .header-content {
                flex-direction: column;
                gap: 15px;
            }

            .filter-row {
                grid-template-columns: 1fr;
            }

            .button-group {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }

            table {
                font-size: 11px;
            }

            table thead th,
            table tbody td {
                padding: 8px 6px;
            }
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .filter-card,
            .btn,
            .no-print {
                display: none !important;
            }

            .page-header {
                box-shadow: none;
                border-bottom: 2px solid #e2e8f0;
            }

            .data-card {
                box-shadow: none;
            }

            table {
                page-break-inside: auto;
            }

            table tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
        }
    </style>
</head>
<body>
    <div class="page-container">
        <!-- Header -->
        <div class="page-header">
            <div class="header-content">
                <h1>
                    <div class="icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                        </svg>
                    </div>
                    Relatório Entrada x Vendas
                </h1>
                <a href="<?= BASE_URL ?>/relatorios/index" class="btn btn-secondary no-print">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    Voltar
                </a>
            </div>

            <?php if (isset($periodoInfo) && !empty($dados)): ?>
            <div class="header-info">
                <div class="info-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </rect>
                    <span><strong>Período Vendas:</strong> <?= $periodoInfo['vendas'] ?></span>
                </div>
                <div class="info-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </rect>
                    <span><strong>Período Entradas:</strong> <?= $periodoInfo['entradas'] ?></span>
                </div>
                <div class="info-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    <span><strong>Emitido em:</strong> <?= date('d/m/Y H:i:s') ?></span>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Filtros -->
        <div class="filter-card no-print">
            <h3>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                </svg>
                Filtros do Relatório
            </h3>

            <form method="POST" action="<?= BASE_URL ?>/relatorios/entrada_vendas" id="filterForm">
                <!-- Filtro de Filiais -->
                <div class="filter-section">
                    <div class="filter-section-title">Filiais</div>
                    <div class="checkbox-group">
                        <div class="checkbox-item">
                            <input type="checkbox" id="todas_filiais" name="todas_filiais" value="1" 
                                   <?= (!isset($_POST['filiais']) || in_array('todas', $_POST['filiais'] ?? [])) ? 'checked' : '' ?>
                                   onchange="toggleFiliais(this)">
                            <label for="todas_filiais">Todas as Filiais</label>
                        </div>
                        <?php if (!empty($filiais)): ?>
                            <?php foreach ($filiais as $filial): ?>
                                <div class="checkbox-item">
                                    <input type="checkbox" 
                                           id="filial_<?= $filial['cd_filial'] ?>" 
                                           name="filiais[]" 
                                           value="<?= $filial['cd_filial'] ?>"
                                           class="filial-checkbox"
                                           <?= (isset($_POST['filiais']) && in_array($filial['cd_filial'], $_POST['filiais'])) ? 'checked' : '' ?>>
                                    <label for="filial_<?= $filial['cd_filial'] ?>"><?= htmlspecialchars($filial['nm_filial']) ?></label>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Período de Vendas -->
                <div class="filter-section">
                    <div class="filter-section-title">Período de Vendas</div>
                    <div class="filter-row">
                        <div class="form-group">
                            <label for="venda_dt_inicio">Data Início</label>
                            <input type="date" 
                                   id="venda_dt_inicio" 
                                   name="venda_dt_inicio" 
                                   value="<?= $_POST['venda_dt_inicio'] ?? date('Y-m-01') ?>"
                                   class="form-control"
                                   required>
                        </div>
                        <div class="form-group">
                            <label for="venda_dt_fim">Data Fim</label>
                            <input type="date" 
                                   id="venda_dt_fim" 
                                   name="venda_dt_fim" 
                                   value="<?= $_POST['venda_dt_fim'] ?? date('Y-m-d') ?>"
                                   class="form-control"
                                   required>
                        </div>
                    </div>
                </div>

                <!-- Período de Entradas -->
                <div class="filter-section">
                    <div class="filter-section-title">Período de Entrada de Estoque</div>
                    <div class="filter-row">
                        <div class="form-group">
                            <label for="entrada_dt_inicio">Data Início</label>
                            <input type="date" 
                                   id="entrada_dt_inicio" 
                                   name="entrada_dt_inicio" 
                                   value="<?= $_POST['entrada_dt_inicio'] ?? date('Y-m-01') ?>"
                                   class="form-control"
                                   required>
                        </div>
                        <div class="form-group">
                            <label for="entrada_dt_fim">Data Fim</label>
                            <input type="date" 
                                   id="entrada_dt_fim" 
                                   name="entrada_dt_fim" 
                                   value="<?= $_POST['entrada_dt_fim'] ?? date('Y-m-d') ?>"
                                   class="form-control"
                                   required>
                        </div>
                    </div>
                </div>

                <!-- Filtros de Estoque -->
                <div class="filter-section">
                    <div class="filter-section-title">Filtros de Estoque</div>
                    <div class="checkbox-group">
                        <div class="checkbox-item">
                            <input type="checkbox" id="est_positivo" name="est_positivo" value="1" 
                                   <?= (isset($_POST['est_positivo']) || !isset($_POST['submit'])) ? 'checked' : '' ?>>
                            <label for="est_positivo">Qtde Estoque Positivo</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" id="est_zerado" name="est_zerado" value="1"
                                   <?= isset($_POST['est_zerado']) ? 'checked' : '' ?>>
                            <label for="est_zerado">Qtde Estoque Zerado</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" id="est_negativo" name="est_negativo" value="1"
                                   <?= isset($_POST['est_negativo']) ? 'checked' : '' ?>>
                            <label for="est_negativo">Qtde Estoque Negativo</label>
                        </div>
                    </div>
                </div>

                <!-- Botões de Ação -->
                <div class="button-group">
                    <button type="submit" name="submit" value="visualizar" class="btn btn-primary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        Visualizar Relatório
                    </button>
                    
                    <?php if (isset($dados) && !empty($dados)): ?>
                    <button type="button" class="btn btn-success" onclick="exportarPDF()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
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
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <?php if (isset($dados) && !empty($dados)): ?>
        <!-- Tabela de Dados -->
        <div class="data-card">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Marca</th>
                            <th class="text-right">Estoque Atual</th>
                            <th class="text-right">Qtde Entradas</th>
                            <th class="text-right">% Qtde Estoque</th>
                            <th class="text-right">Qtde Vendida</th>
                            <th class="text-right">% Qtde Venda</th>
                            <th class="text-right">Valor Estoque (R$)</th>
                            <th class="text-right">% Valor Estoque</th>
                            <th class="text-right">Valor Vendido (R$)</th>
                            <th class="text-right">% Valor Venda</th>
                            <th class="text-right">Rel. Est./R$</th>
                            <th class="text-right">Rel. Est./Qtde</th>
                            <th class="text-right">Preço Custo</th>
                            <th class="text-right">Preço Venda</th>
                            <th class="text-right">Margem (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dados as $filial => $marcas): ?>
                            <tr class="filial-header">
                                <td colspan="15">
                                    <strong>Filial: <?= htmlspecialchars($filial) ?></strong>
                                </td>
                            </tr>
                            <?php foreach ($marcas['itens'] as $marca): ?>
                            <tr>
                                <td><?= htmlspecialchars($marca['marca']) ?></td>
                                <td class="text-right <?= $marca['estoque_atual'] == 0 ? 'badge-zero' : ($marca['estoque_atual'] < 0 ? 'badge-negative' : '') ?>">
                                    <?= number_format($marca['estoque_atual'], 0, ',', '.') ?>
                                </td>
                                <td class="text-right"><?= number_format($marca['qtde_entradas'], 0, ',', '.') ?></td>
                                <td class="text-right"><?= number_format($marca['perc_qtde_estoque'], 2, ',', '.') ?>%</td>
                                <td class="text-right"><?= number_format($marca['qtde_vendida'], 0, ',', '.') ?></td>
                                <td class="text-right"><?= number_format($marca['perc_qtde_venda'], 2, ',', '.') ?>%</td>
                                <td class="text-right">R$ <?= number_format($marca['valor_estoque'], 2, ',', '.') ?></td>
                                <td class="text-right"><?= number_format($marca['perc_valor_estoque'], 2, ',', '.') ?>%</td>
                                <td class="text-right">R$ <?= number_format($marca['valor_vendido'], 2, ',', '.') ?></td>
                                <td class="text-right"><?= number_format($marca['perc_valor_venda'], 2, ',', '.') ?>%</td>
                                <td class="text-right"><?= number_format($marca['rel_estoque_valor'], 2, ',', '.') ?></td>
                                <td class="text-right"><?= number_format($marca['rel_estoque_qtde'], 2, ',', '.') ?></td>
                                <td class="text-right">R$ <?= number_format($marca['preco_custo'], 2, ',', '.') ?></td>
                                <td class="text-right">R$ <?= number_format($marca['preco_venda'], 2, ',', '.') ?></td>
                                <td class="text-right <?= $marca['margem'] < 0 ? 'badge-negative' : 'badge-positive' ?>">
                                    <?= number_format($marca['margem'], 2, ',', '.') ?>%
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <tr class="filial-subtotal">
                                <td><strong>Subtotal <?= htmlspecialchars($filial) ?></strong></td>
                                <td class="text-right"><strong><?= number_format($marcas['subtotal']['estoque_atual'], 0, ',', '.') ?></strong></td>
                                <td class="text-right"><strong><?= number_format($marcas['subtotal']['qtde_entradas'], 0, ',', '.') ?></strong></td>
                                <td class="text-right"><strong>100,00%</strong></td>
                                <td class="text-right"><strong><?= number_format($marcas['subtotal']['qtde_vendida'], 0, ',', '.') ?></strong></td>
                                <td class="text-right"><strong>100,00%</strong></td>
                                <td class="text-right"><strong>R$ <?= number_format($marcas['subtotal']['valor_estoque'], 2, ',', '.') ?></strong></td>
                                <td class="text-right"><strong>100,00%</strong></td>
                                <td class="text-right"><strong>R$ <?= number_format($marcas['subtotal']['valor_vendido'], 2, ',', '.') ?></strong></td>
                                <td class="text-right"><strong>100,00%</strong></td>
                                <td class="text-right"><strong>-</strong></td>
                                <td class="text-right"><strong>-</strong></td>
                                <td class="text-right"><strong>-</strong></td>
                                <td class="text-right"><strong>-</strong></td>
                                <td class="text-right"><strong><?= number_format($marcas['subtotal']['margem'], 2, ',', '.') ?>%</strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td><strong>TOTAL GERAL</strong></td>
                            <td class="text-right"><strong><?= number_format($totais['estoque_atual'], 0, ',', '.') ?></strong></td>
                            <td class="text-right"><strong><?= number_format($totais['qtde_entradas'], 0, ',', '.') ?></strong></td>
                            <td class="text-right"><strong>100,00%</strong></td>
                            <td class="text-right"><strong><?= number_format($totais['qtde_vendida'], 0, ',', '.') ?></strong></td>
                            <td class="text-right"><strong>100,00%</strong></td>
                            <td class="text-right"><strong>R$ <?= number_format($totais['valor_estoque'], 2, ',', '.') ?></strong></td>
                            <td class="text-right"><strong>100,00%</strong></td>
                            <td class="text-right"><strong>R$ <?= number_format($totais['valor_vendido'], 2, ',', '.') ?></strong></td>
                            <td class="text-right"><strong>100,00%</strong></td>
                            <td class="text-right"><strong>-</strong></td>
                            <td class="text-right"><strong>-</strong></td>
                            <td class="text-right"><strong>-</strong></td>
                            <td class="text-right"><strong>-</strong></td>
                            <td class="text-right"><strong><?= number_format($totais['margem'], 2, ',', '.') ?>%</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <?php elseif (isset($_POST['submit'])): ?>
        <!-- Estado Vazio -->
        <div class="data-card">
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <h3>Nenhum dado encontrado</h3>
                <p>Não há dados para os filtros selecionados. Tente ajustar os parâmetros.</p>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner"></div>
    </div>

    <script>
        function toggleFiliais(checkbox) {
            const filialCheckboxes = document.querySelectorAll('.filial-checkbox');
            filialCheckboxes.forEach(cb => {
                cb.checked = checkbox.checked;
                cb.disabled = checkbox.checked;
            });
        }

        function exportarPDF() {
            const form = document.getElementById('filterForm');
            const formData = new FormData(form);
            
            const params = new URLSearchParams();
            for (const [key, value] of formData.entries()) {
                params.append(key, value);
            }
            
            const loadingOverlay = document.getElementById('loadingOverlay');
            loadingOverlay.classList.add('active');
            
            window.location.href = `<?= BASE_URL ?>/relatorios/exportarEntradaVendasPDF?${params.toString()}`;
            
            setTimeout(() => {
                loadingOverlay.classList.remove('active');
            }, 2000);
        }

        function exportarExcel() {
            const form = document.getElementById('filterForm');
            const formData = new FormData(form);
            
            const params = new URLSearchParams();
            for (const [key, value] of formData.entries()) {
                params.append(key, value);
            }
            
            const loadingOverlay = document.getElementById('loadingOverlay');
            loadingOverlay.classList.add('active');
            
            window.location.href = `<?= BASE_URL ?>/relatorios/exportarEntradaVendasExcel?${params.toString()}`;
            
            setTimeout(() => {
                loadingOverlay.classList.remove('active');
            }, 2000);
        }

        // Inicializar estado dos checkboxes de filial
        document.addEventListener('DOMContentLoaded', function() {
            const todasFiliais = document.getElementById('todas_filiais');
            if (todasFiliais && todasFiliais.checked) {
                toggleFiliais(todasFiliais);
            }
        });
    </script>
</body>
</html>
