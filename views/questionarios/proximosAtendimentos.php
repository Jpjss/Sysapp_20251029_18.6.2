<div class="page-header">
    <h2>Próximos Atendimentos</h2>
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Telefone</th>
                <th>Última Compra</th>
                <th>Próximo Atendimento</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($atendimentos)): ?>
                <?php foreach ($atendimentos as $atend): ?>
                    <tr>
                        <td><?= htmlspecialchars($atend['nm_cliente'] ?? $atend['nm_fant'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($atend['fone'] ?? '-') ?></td>
                        <td><?= $atend['dt_ult_compra'] ? date('d/m/Y', strtotime($atend['dt_ult_compra'])) : '-' ?></td>
                        <td><?= $atend['dt_prox_atendimento'] ? date('d/m/Y', strtotime($atend['dt_prox_atendimento'])) : '-' ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>/clientes/view/<?= $atend['cd_pessoa'] ?>" 
                               class="btn btn-sm btn-info">Ver Cliente</a>
                            <a href="<?= BASE_URL ?>/questionarios/responder/1/<?= $atend['cd_pessoa'] ?>" 
                               class="btn btn-sm btn-primary">Atender</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">Nenhum atendimento pendente</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
