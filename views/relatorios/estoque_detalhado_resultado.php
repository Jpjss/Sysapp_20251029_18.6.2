<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Estoque Detalhado</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            padding: 20px;
            background-color: #f5f5f5;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #4682B4;
            padding-bottom: 20px;
        }
        
        .header h1 {
            font-size: 24px;
            color: #333;
            margin-bottom: 10px;
        }
        
        .header .subtitle {
            font-size: 14px;
            color: #666;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th {
            background-color: #4682B4;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
        }
        
        th.text-right, td.text-right {
            text-align: right;
        }
        
        th.text-center, td.text-center {
            text-align: center;
        }
        
        td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        tr:hover {
            background-color: #f0f8ff;
        }
        
        .categoria-principal {
            font-weight: bold;
            background-color: #e6f2ff !important;
        }
        
        .total-geral {
            background-color: #d3d3d3 !important;
            font-weight: bold;
            font-size: 14px;
        }
        
        .valor-monetario {
            color: #2e7d32;
            font-weight: 500;
        }
        
        .sem-dados {
            text-align: center;
            padding: 40px;
            color: #999;
            font-size: 16px;
        }
        
        .botoes {
            margin-top: 30px;
            text-align: center;
        }
        
        .btn {
            padding: 10px 20px;
            margin: 5px;
            cursor: pointer;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background-color: #4682B4;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #36648B;
        }
        
        @media print {
            .botoes {
                display: none;
            }
            body {
                background-color: white;
            }
            .container {
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Relatório de Estoque Detalhado por <?= ucfirst(strtolower($tipo_agrupamento)) ?></h1>
            <div class="subtitle">
                <strong>Emissão:</strong> <?= date('d/m/Y H:i:s') ?><br>
                <strong>Data de Referência:</strong> <?= $data_formatada ?>
            </div>
        </div>

        <?php if (!empty($dadosRelatorio)): ?>
            <table>
                <thead>
                    <tr>
                        <th style="width: 35%;">Família/Grupo</th>
                        <th style="width: 15%;" class="text-right">Custo Estoque (Total)</th>
                        <th style="width: 13%;" class="text-right">Qtde Estoque (Total)</th>
                        <th style="width: 10%;" class="text-center">Total SKUs</th>
                        <th style="width: 13%;" class="text-right">Total Estoque (Em %)</th>
                        <th style="width: 14%;" class="text-right">Valor Estoque (Em %)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dadosRelatorio as $item): ?>
                        <tr class="categoria-principal">
                            <td><?= htmlspecialchars($item['ds_categoria']) ?></td>
                            <td class="text-right valor-monetario">
                                R$ <?= number_format($item['custo_total'], 2, ',', '.') ?>
                            </td>
                            <td class="text-right">
                                <?= number_format($item['qtde_total'], 0, ',', '.') ?>
                            </td>
                            <td class="text-center">
                                <?= number_format($item['total_skus'], 0, ',', '.') ?>
                            </td>
                            <td class="text-right">
                                <?= number_format($item['perc_qtde'], 2, ',', '.') ?>%
                            </td>
                            <td class="text-right">
                                <?= number_format($item['perc_valor'], 2, ',', '.') ?>%
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    
                    <tr class="total-geral">
                        <td><strong>TOTAL GERAL</strong></td>
                        <td class="text-right valor-monetario">
                            R$ <?= number_format($total_custo, 2, ',', '.') ?>
                        </td>
                        <td class="text-right">
                            <?= number_format($total_qtde, 0, ',', '.') ?>
                        </td>
                        <td class="text-center">
                            <?= number_format($total_skus, 0, ',', '.') ?>
                        </td>
                        <td class="text-right">100,00%</td>
                        <td class="text-right">100,00%</td>
                    </tr>
                </tbody>
            </table>
        <?php else: ?>
            <div class="sem-dados">
                <p>📦 Nenhum dado encontrado para os filtros selecionados.</p>
                <p>Tente ajustar os filtros e gerar o relatório novamente.</p>
            </div>
        <?php endif; ?>

        <div class="botoes">
            <button onclick="window.print();" class="btn btn-primary">
                🖨️ Imprimir
            </button>
            <a href="<?= BASE_URL ?>/relatorios/estoque_detalhado" class="btn btn-primary">
                ⬅️ Voltar
            </a>
            <button onclick="window.close();" class="btn btn-primary">
                ✖️ Fechar
            </button>
        </div>
    </div>
</body>
</html>
