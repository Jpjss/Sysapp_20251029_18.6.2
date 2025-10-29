<div class="glbQuestionarioGlbQuestionarioPerguntas index">
    <h2><?php echo __('Glb Questionario Glb Questionario Perguntas'); ?></h2>
    <table cellpadding="0" cellspacing="0">
        <tr>
            <th><?php echo $this->Paginator->sort('cd_questionario'); ?></th>
            <th><?php echo $this->Paginator->sort('cd_pergunta'); ?></th>
            <th><?php echo $this->Paginator->sort('prioridade'); ?></th>
            <th class="actions"><?php echo __('Actions'); ?></th>
        </tr>
        <?php foreach ($glbQuestionarioGlbQuestionarioPerguntas as $glbQuestionarioGlbQuestionarioPergunta): ?>
            <tr>
                <td><?php echo h($glbQuestionarioGlbQuestionarioPergunta['GlbQuestionarioGlbQuestionarioPergunta']['cd_questionario']); ?>&nbsp;</td>
                <td><?php echo h($glbQuestionarioGlbQuestionarioPergunta['GlbQuestionarioGlbQuestionarioPergunta']['cd_pergunta']); ?>&nbsp;</td>
                <td><?php echo h($glbQuestionarioGlbQuestionarioPergunta['GlbQuestionarioGlbQuestionarioPergunta']['prioridade']); ?>&nbsp;</td>
                <td class="actions">
                    <?php echo $this->Html->link(__('View'), array('action' => 'view', $glbQuestionarioGlbQuestionarioPergunta['GlbQuestionarioGlbQuestionarioPergunta']['cd_questionario,cd_pergunta'])); ?>
                    <?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $glbQuestionarioGlbQuestionarioPergunta['GlbQuestionarioGlbQuestionarioPergunta']['cd_questionario,cd_pergunta'])); ?>
                    <?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $glbQuestionarioGlbQuestionarioPergunta['GlbQuestionarioGlbQuestionarioPergunta']['cd_questionario,cd_pergunta']), null, __('Are you sure you want to delete # %s?', $glbQuestionarioGlbQuestionarioPergunta['GlbQuestionarioGlbQuestionarioPergunta']['cd_questionario,cd_pergunta'])); ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <p>
        <?php
        echo $this->Paginator->counter(array(
            'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
        ));
        ?>	</p>
    <div class="paging">
        <?php
        echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
        echo $this->Paginator->numbers(array('separator' => ''));
        echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
        ?>
    </div>
</div>
