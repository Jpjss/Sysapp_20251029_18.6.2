<!-- Cabeçalho -->
<div style="margin-bottom: 30px;">
    <h1 style="font-size: 32px; font-weight: 600; margin-bottom: 8px; color: #f1f5f9;">📊 Relatórios</h1>
    <p style="color: #cbd5e1; font-size: 16px;">Selecione um relatório para visualizar análises detalhadas e gerar exportações</p>
</div>

<!-- Grid de Relatórios -->
<div class="reports-grid" style="margin-top: 40px;">
    <!-- Relatório de Atendimentos -->
    <a href="<?= BASE_URL ?>/relatorios/atendimentos" class="report-card">
        <div class="report-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
        </div>
        <h4>Relatório de Atendimentos</h4>
        <p>Acompanhe os atendimentos realizados, clientes únicos, tempo gasto e valores por período</p>
    </a>

    <!-- Entrada x Vendas -->
    <a href="<?= BASE_URL ?>/relatorios/entrada_vendas" class="report-card">
        <div class="report-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
            </svg>
        </div>
        <h4>Entrada x Vendas</h4>
        <p>Compare entradas de estoque versus vendas por marca e filial com análise de margens</p>
    </a>

    <!-- Vendas por Vendedor -->
    <a href="<?= BASE_URL ?>/relatorios/vendas_vendedor" class="report-card">
        <div class="report-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
            </svg>
        </div>
        <h4>Vendas por Vendedor</h4>
        <p>Análise de desempenho de vendas por vendedor com metas e comissões</p>
    </a>

    <!-- Estoque Detalhado -->
    <a href="<?= BASE_URL ?>/relatorios/estoque_detalhado" class="report-card">
        <div class="report-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
            </svg>
        </div>
        <h4>Estoque Detalhado</h4>
        <p>Visualização completa do estoque por família, grupo e filial com valores atualizados</p>
    </a>

    <!-- Análise de Lucros -->
    <a href="<?= BASE_URL ?>/relatorios/analise_lucros" class="report-card">
        <div class="report-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                <line x1="12" y1="1" x2="12" y2="23"></line>
                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
            </svg>
        </div>
        <h4>Análise de Lucros</h4>
        <p>Demonstrativo de resultados com análise de margem bruta e líquida por período</p>
    </a>

    <!-- Marcas Mais Vendidas -->
    <a href="<?= BASE_URL ?>/marcasvendas/dashboard" class="report-card">
        <div class="report-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                <path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path>
                <path d="M22 12A10 10 0 0 0 12 2v10z"></path>
            </svg>
        </div>
        <h4>Marcas Mais Vendidas</h4>
        <p>Dashboard interativo com as marcas mais vendidas e análise temporal de vendas</p>
    </a>

    <!-- Fluxo de Orçamento -->
    <a href="<?= BASE_URL ?>/relatorios/fluxo_orcamento" class="report-card">
        <div class="report-icon" style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                <line x1="8" y1="21" x2="16" y2="21"></line>
                <line x1="12" y1="17" x2="12" y2="21"></line>
            </svg>
        </div>
        <h4>Fluxo de Orçamento por Hora</h4>
        <p>Monitore o fluxo de orçamentos e vendas distribuídos ao longo do dia</p>
    </a>

    <!-- Pedido de Compras -->
    <a href="<?= BASE_URL ?>/relatorios/pedido_compras" class="report-card">
        <div class="report-icon" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                <path d="M9 11l3 3L22 4"></path>
                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
            </svg>
        </div>
        <h4>Pedido de Compras</h4>
        <p>Gestão de pedidos de compra com fornecedores, prazos e status de entrega</p>
    </a>
</div>
