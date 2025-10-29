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
        <div class="info-item">
            <strong>Endereço:</strong>
            <span><?= htmlspecialchars($cliente['endereco'] ?? '-') ?></span>
        </div>
        <div class="info-item">
            <strong>Bairro:</strong>
            <span><?= htmlspecialchars($cliente['bairro'] ?? '-') ?></span>
        </div>
        <div class="info-item">
            <strong>Cidade:</strong>
            <span><?= htmlspecialchars($cliente['cidade'] ?? '-') ?></span>
        </div>
        <div class="info-item">
            <strong>UF:</strong>
            <span><?= htmlspecialchars($cliente['uf'] ?? '-') ?></span>
        </div>
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
