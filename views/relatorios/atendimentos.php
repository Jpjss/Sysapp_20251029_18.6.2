<div class="page-header">
    <h2>Relatório de Atendimentos</h2>
    <a href="<?= BASE_URL ?>/relatorios/index" class="btn btn-secondary">Voltar</a>
</div>

<div class="card">
    <form method="GET" action="<?= BASE_URL ?>/relatorios/atendimentos" class="filter-form">
        <div class="form-row">
            <div class="form-group">
                <label for="dt_inicio">Data Início:</label>
                <input type="date" 
                       id="dt_inicio" 
                       name="dt_inicio" 
                       value="<?= $dt_inicio ?>"
                       class="form-control">
            </div>
            
            <div class="form-group">
                <label for="dt_fim">Data Fim:</label>
                <input type="date" 
                       id="dt_fim" 
                       name="dt_fim" 
                       value="<?= $dt_fim ?>"
                       class="form-control">
            </div>
            
            <div class="form-group" style="align-self: flex-end;">
                <button type="submit" class="btn btn-primary">Filtrar</button>
                <a href="<?= BASE_URL ?>/relatorios/exportar?tipo=atendimentos&dt_inicio=<?= $dt_inicio ?>&dt_fim=<?= $dt_fim ?>" 
                   class="btn btn-info">Exportar CSV</a>
            </div>
        </div>
    </form>
</div>

<div class="card">
    <h3>Atendimentos por Dia</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Data</th>
                <th>Total Atendimentos</th>
                <th>Clientes Únicos</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($atendimentos)): ?>
                <?php 
                $totalAtendimentos = 0;
                $totalClientes = 0;
                foreach ($atendimentos as $atend): 
                    $totalAtendimentos += $atend['total'];
                    $totalClientes += $atend['clientes_unicos'];
                ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($atend['data'])) ?></td>
                        <td><?= $atend['total'] ?></td>
                        <td><?= $atend['clientes_unicos'] ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td><strong>TOTAL</strong></td>
                    <td><strong><?= $totalAtendimentos ?></strong></td>
                    <td><strong><?= $totalClientes ?></strong></td>
                </tr>
            <?php else: ?>
                <tr>
                    <td colspan="3" class="text-center">Nenhum atendimento no período</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="card">
    <h3>Atendimentos por Usuário</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Usuário</th>
                <th>Total Atendimentos</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($atendimentosUsuario)): ?>
                <?php foreach ($atendimentosUsuario as $atend): ?>
                    <tr>
                        <td><?= htmlspecialchars($atend['nome_usuario'] ?? 'Sistema') ?></td>
                        <td><?= $atend['total_atendimentos'] ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="2" class="text-center">Nenhum atendimento no período</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
