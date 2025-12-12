<div class="page-header">
    <h2>Detalhes do Cliente</h2>
    <a href="<?= BASE_URL ?>/clientes/index" class="btn btn-secondary">Voltar</a>
</div>

<div class="card">
    <h3>Informações Básicas</h3>
    <div class="info-grid">
        <div class="info-item">
            <strong>Código:</strong>
            <span><?= htmlspecialchars($cliente['cd_pessoa']) ?></span>
        </div>
        <div class="info-item">
            <strong>Nome Fantasia:</strong>
            <span><?= htmlspecialchars($cliente['nm_fant']) ?></span>
        </div>
        <div class="info-item">
            <strong>Razão Social:</strong>
            <span><?= htmlspecialchars($cliente['nm_razao'] ?? '-') ?></span>
        </div>
        <div class="info-item">
            <strong>CPF/CNPJ:</strong>
            <span><?= htmlspecialchars($cliente['cpf_cnpj'] ?? '-') ?></span>
        </div>
        <?php if (!empty($cliente['endereco'])): ?>
        <div class="info-item">
            <strong>Endereço:</strong>
            <span><?= htmlspecialchars($cliente['endereco']) ?></span>
        </div>
        <?php endif; ?>
        <?php if (!empty($cliente['bairro'])): ?>
        <div class="info-item">
            <strong>Bairro:</strong>
            <span><?= htmlspecialchars($cliente['bairro']) ?></span>
        </div>
        <?php endif; ?>
        <?php if (!empty($cliente['cidade'])): ?>
        <div class="info-item">
            <strong>Cidade:</strong>
            <span><?= htmlspecialchars($cliente['cidade']) ?></span>
        </div>
        <?php endif; ?>
        <?php if (!empty($cliente['uf'])): ?>
        <div class="info-item">
            <strong>UF:</strong>
            <span><?= htmlspecialchars($cliente['uf']) ?></span>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($telefones)): ?>
<div class="card">
    <h3>Telefones</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Tipo</th>
                <th>Número</th>
                <th>Contato</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($telefones as $tel): ?>
                <tr>
                    <td><?= htmlspecialchars($tel['tp_fone'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($tel['nr_fone'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($tel['nm_contato'] ?? '-') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php else: ?>
<div class="card">
    <h3>Telefones</h3>
    <p style="color: #94a3b8; text-align: center; padding: 20px;">Nenhum telefone cadastrado</p>
</div>
<?php endif; ?>

<?php if (!empty($historico)): ?>
<div class="card">
    <h3>Histórico de Compras/Vendas</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Pedido</th>
                <th>Data</th>
                <th>Valor</th>
                <th>Entrada</th>
                <th>Qtd. Peças</th>
                <th>Situação</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($historico as $venda): ?>
                <tr>
                    <td><?= htmlspecialchars($venda['cd_ped']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($venda['dt_hr_ped'])) ?></td>
                    <td style="color: #22c55e; font-weight: 600;">
                        R$ <?= number_format($venda['vlr_vd'], 2, ',', '.') ?>
                    </td>
                    <td>R$ <?= number_format($venda['vlr_entrada'], 2, ',', '.') ?></td>
                    <td><?= $venda['qtd_pecas'] ?></td>
                    <td>
                        <span class="badge badge-<?= $venda['sit_ped'] == 1 ? 'success' : ($venda['sit_ped'] == 2 ? 'danger' : 'warning') ?>">
                            <?= htmlspecialchars($venda['ds_situacao']) ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr style="background: rgba(52, 152, 219, 0.1); font-weight: 600;">
                <td colspan="2">TOTAL</td>
                <td style="color: #22c55e;">
                    R$ <?php 
                        $total = array_sum(array_column($historico, 'vlr_vd'));
                        echo number_format($total, 2, ',', '.');
                    ?>
                </td>
                <td>
                    R$ <?php 
                        $totalEntrada = array_sum(array_column($historico, 'vlr_entrada'));
                        echo number_format($totalEntrada, 2, ',', '.');
                    ?>
                </td>
                <td><?= array_sum(array_column($historico, 'qtd_pecas')) ?></td>
                <td><?= count($historico) ?> pedidos</td>
            </tr>
        </tfoot>
    </table>
</div>
<?php else: ?>
<div class="card">
    <h3>Histórico de Compras/Vendas</h3>
    <p style="color: #94a3b8; text-align: center; padding: 20px;">Nenhuma compra registrada</p>
</div>
<?php endif; ?>

<?php if (!empty($observacoes)): ?>
<div class="card">
    <h3>Observações de Contato</h3>
    <div class="observacoes-list">
        <?php foreach ($observacoes as $obs): ?>
            <div class="observacao-item">
                <div class="obs-header">
                    <strong><?= date('d/m/Y H:i', strtotime($obs['dt_obs'])) ?></strong>
                </div>
                <div class="obs-content">
                    <?= nl2br(htmlspecialchars($obs['ds_obs'])) ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <h3>Ações</h3>
    <div class="action-buttons">
        <a href="<?= BASE_URL ?>/questionarios/historico/<?= $cliente['cd_pessoa'] ?>" 
           class="btn btn-primary">Ver Histórico de Atendimentos</a>
    </div>
</div>

<style>
.badge {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-success {
    background: rgba(34, 197, 94, 0.2);
    color: #22c55e;
}

.badge-warning {
    background: rgba(245, 158, 11, 0.2);
    color: #f59e0b;
}

.badge-danger {
    background: rgba(239, 68, 68, 0.2);
    color: #ef4444;
}

.table tfoot tr {
    border-top: 2px solid #3b82f6;
}
</style>
