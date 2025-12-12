<div class="page-header">
    <h2>Aniversariantes do Mês</h2>
</div>

<div class="card">
    <form method="GET" action="<?= BASE_URL ?>/questionarios/aniversariantes" class="filter-form">
        <div class="form-group">
            <label for="mes">Mês:</label>
            <select id="mes" name="mes" class="form-control" onchange="this.form.submit()">
                <?php 
                $meses = [
                    1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
                    5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
                    9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
                ];
                foreach ($meses as $num => $nome): ?>
                    <option value="<?= $num ?>" <?= $num == $mes ? 'selected' : '' ?>>
                        <?= $nome ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>
    
    <table class="table">
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Data Nascimento</th>
                <th>Telefone</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($aniversariantes)): ?>
                <?php foreach ($aniversariantes as $aniv): ?>
                    <tr>
                        <td><?= htmlspecialchars($aniv['nm_cliente'] ?? $aniv['nm_fant'] ?? '-') ?></td>
                        <td><?= $aniv['dt_nasc'] ? date('d/m/Y', strtotime($aniv['dt_nasc'])) : '-' ?></td>
                        <td><?= htmlspecialchars($aniv['fone'] ?? '-') ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>/clientes/view/<?= $aniv['cd_pessoa'] ?>" 
                               class="btn btn-sm btn-info">Ver Cliente</a>
                            <a href="<?= BASE_URL ?>/questionarios/responder/1/<?= $aniv['cd_pessoa'] ?>" 
                               class="btn btn-sm btn-primary">Atender</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center">Nenhum aniversariante neste mês</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
