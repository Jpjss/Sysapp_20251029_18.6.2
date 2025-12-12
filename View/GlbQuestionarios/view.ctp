<div class="glbQuestionarios view">
    <h2><?php echo __('Pesquisa'); ?></h2>
    <dl>
        <dt><?php echo __('Cod Pesquisa'); ?></dt>
        <dd>
            <?php echo h($glbQuestionario['GlbQuestionario']['cd_questionario']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Descrição'); ?></dt>
        <dd>
            <?php echo h($glbQuestionario['GlbQuestionario']['ds_questionario']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Data de Inicio'); ?></dt>
        <dd>
            <?php echo $this->Formatacao->dataCompleta($glbQuestionario['GlbQuestionario']['dt_vigencia_ini']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Data do Fim'); ?></dt>
        <dd>
            <?php echo $this->Formatacao->dataCompleta($glbQuestionario['GlbQuestionario']['dt_vigencia_fim']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Observação'); ?></dt>
        <dd>
            <?php echo h($glbQuestionario['GlbQuestionario']['obs']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Perguntas'); ?></dt>
        <dd>
            <?php
            if ($perguntas != null) {
                ?>
                <table>
                    <?php foreach ($perguntas as $value) { ?>
                        <tr>
                            <td style="font-size: 12px; font-weight: bold;">• <?php echo $value['qPergunta']['ds_pergunta']; ?></td>
                        </tr>  
                    <?php } ?>
                </table>
                <?php
            } else {
                echo "Ainda não existem perguntas para esse questionário!";
            }
            ?>
        </dd>

    </dl>
    <div class="actions" style="width: 500px;">
        <?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $glbQuestionario['GlbQuestionario']['cd_questionario'])); ?>
        <?php echo $this->Html->link(__('Adicionar Pergunta(s)'), array('controller' => 'GlbQuestionarioGlbQuestionarioPerguntas', 'action' => 'add', $glbQuestionario['GlbQuestionario']['cd_questionario'])); ?>
        <?php echo $this->Html->link(__('Voltar'), array('action' => 'index')); ?>
    </div>
</div>
