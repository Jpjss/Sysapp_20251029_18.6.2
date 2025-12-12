<div class="page-header">
    <h2>Histórico de Atendimentos - <?= htmlspecialchars($cliente['nm_fant']) ?></h2>
    <a href="<?= BASE_URL ?>/clientes/view/<?= $cliente['cd_pessoa'] ?>" class="btn btn-secondary">Voltar</a>
</div>

<div class="card">
    <?php if (!empty($historico)): ?>
        <div class="historico-list">
            <?php foreach ($historico as $hist): ?>
                <div class="historico-item">
                    <div class="hist-header">
                        <strong><?= htmlspecialchars($hist['ds_questionario']) ?></strong>
                        <span class="hist-date"><?= date('d/m/Y H:i', strtotime($hist['dt_resposta'])) ?></span>
                    </div>
                    <div class="hist-user">
                        Atendido por: <?= htmlspecialchars($hist['nome_usuario'] ?? 'Sistema') ?>
                    </div>
                    <div class="hist-content">
                        <?= nl2br(htmlspecialchars($hist['ds_resposta'] ?? 'Sem observações')) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-center">Nenhum atendimento registrado para este cliente.</p>
    <?php endif; ?>
</div>
