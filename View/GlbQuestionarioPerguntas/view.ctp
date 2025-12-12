<div class="glbQuestionarioPerguntas view">
    <h2><?php echo __('Pergunta'); ?></h2>
    <dl>
        <dt><?php echo __('Cod Pergunta'); ?></dt>
        <dd>
            <?php echo h($glbQuestionarioPergunta['GlbQuestionarioPergunta']['cd_pergunta']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Descrição'); ?></dt>
        <dd>
            <?php echo h($glbQuestionarioPergunta['GlbQuestionarioPergunta']['ds_pergunta']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Tipo Pergunta'); ?></dt>
        <dd>
            <?php
            switch ($glbQuestionarioPergunta['GlbQuestionarioPergunta']['tp_pergunta']) {
                case(0):
                    echo "Opções";
                    break;
                case(1):
                    echo "Dissertativa";
                    break;
                case(2):
                    echo "Pontuação";
                    break;
                case(3):
                    echo "Sugestão";
                    break;
            };
            ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Observação'); ?></dt>
        <dd>
            <?php echo h($glbQuestionarioPergunta['GlbQuestionarioPergunta']['obs']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Usuário Cadastro'); ?></dt>
        <dd>
            <?php echo h($glbQuestionarioPergunta['GlbQuestionarioPergunta']['cd_usu_cad']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Data Cadastro'); ?></dt>
        <dd>
            <?php echo $this->Formatacao->dataCompleta($glbQuestionarioPergunta['GlbQuestionarioPergunta']['dt_cad']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Respostas'); ?></dt>
        <dd>
            <?php
            sort($respostas);
            foreach ($respostas as $resposta) {
                echo " • " . $resposta["GlbQuestionarioPerguntaCpl"]["ds_pergunta_cpl"]."<br>";
            }
            ?>
            &nbsp;
        </dd>
    </dl>
    <div class="actions" style="width: 500px;">
        <?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $glbQuestionarioPergunta['GlbQuestionarioPergunta']['cd_pergunta'])); ?>
        <?php echo $this->Html->link(__('Adicionar Resposta(s)'), array('controller' => 'GlbQuestionarioPerguntaCpls', 'action' => 'add',$glbQuestionarioPergunta['GlbQuestionarioPergunta']['cd_pergunta'],$glbQuestionarioPergunta['GlbQuestionarioPergunta']['tp_pergunta'] )); ?>
        <?php echo $this->Html->link(__('Voltar'), array('action' => 'index')); ?>
    </div>
</div>
