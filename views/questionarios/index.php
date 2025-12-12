<div class="page-header">
    <h2>Questionários</h2>
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Código</th>
                <th>Descrição</th>
                <th>Tipo</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($questionarios)): ?>
                <?php foreach ($questionarios as $quest): ?>
                    <tr>
                        <td><?= htmlspecialchars($quest['cd_questionario']) ?></td>
                        <td><?= htmlspecialchars($quest['ds_questionario']) ?></td>
                        <td><?= htmlspecialchars($quest['tp_questionario'] ?? '-') ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>/questionarios/responder/<?= $quest['cd_questionario'] ?>" 
                               class="btn btn-sm btn-primary">Responder</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center">Nenhum questionário encontrado</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>" 
                   class="page-link <?= $i === $page ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>
