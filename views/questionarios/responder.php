<div class="page-header">
    <h2>Responder Questionário: <?= htmlspecialchars($questionario['ds_questionario']) ?></h2>
    <a href="<?= BASE_URL ?>/questionarios/index" class="btn btn-secondary">Voltar</a>
</div>

<div class="card">
    <form method="POST" action="<?= BASE_URL ?>/questionarios/responder/<?= $questionario['cd_questionario'] ?>" id="formQuestionario">
        <div class="form-group">
            <label for="cd_pessoa">Cliente: *</label>
            <select id="cd_pessoa" name="cd_pessoa" class="form-control select2" required>
                <?php if ($cliente): ?>
                    <option value="<?= $cliente['cd_pessoa'] ?>" selected>
                        <?= htmlspecialchars($cliente['nm_fant']) ?>
                    </option>
                <?php else: ?>
                    <option value="">Selecione um cliente...</option>
                <?php endif; ?>
            </select>
        </div>
        
        <hr>
        
        <h3>Perguntas</h3>
        
        <?php if (!empty($perguntas)): ?>
            <?php foreach ($perguntas as $pergunta): ?>
                <div class="form-group">
                    <label for="resposta_<?= $pergunta['cd_pergunta'] ?>">
                        <?= htmlspecialchars($pergunta['ds_pergunta']) ?>
                    </label>
                    <textarea id="resposta_<?= $pergunta['cd_pergunta'] ?>" 
                              name="respostas[<?= $pergunta['cd_pergunta'] ?>]" 
                              rows="3"
                              class="form-control"></textarea>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Este questionário não possui perguntas cadastradas.</p>
        <?php endif; ?>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Salvar Respostas</button>
            <a href="<?= BASE_URL ?>/questionarios/index" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Inicializa Select2 para busca de clientes
    $('#cd_pessoa').select2({
        ajax: {
            url: '<?= BASE_URL ?>/clientes/search',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true
        },
        minimumInputLength: 2,
        placeholder: 'Digite para buscar cliente...'
    });
});
</script>
