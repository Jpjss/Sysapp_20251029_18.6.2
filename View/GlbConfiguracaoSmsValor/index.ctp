<div class="glbQuestionarios index">
    <h2><?php echo __('Configuração Valor de SMS'); ?></h2>
    <table cellpadding="0" cellspacing="0">
        <?php echo $this->Html->image("novaConfiguracao.png", array("alt" => "Adicionar configuração", 'url' => array('action' => 'add'))); ?>
        <div style="width: 100%; text-align: right;">
            <?php echo $this->Html->image("legendaMenu.png", array("alt" => "Faixa", 'url' => array('action' => 'add'))); ?>
        </div>
        <tr>
            <th><?php echo $this->Paginator->sort('dt_vigencia_ini', "Inicio"); ?></th>
            <th><?php echo $this->Paginator->sort('dt_vigencia_fim', "Fim"); ?></th>
            <th><?php echo $this->Paginator->sort('valor_sms', "Valor"); ?></th>
            <th class="actions"></th>
        </tr>
        <?php foreach ($glbConfiguracaoSmsValores as $glbConfiguracaoSmsValor):
            ?>
            <tr>
                <td><?php echo $this->Funcionalidades->formatarDataAp($glbConfiguracaoSmsValor['GlbConfiguracaoSmsValor']['dt_vigencia_ini']); ?>&nbsp;</td>
                <td><?php echo $this->Funcionalidades->formatarDataAp($glbConfiguracaoSmsValor['GlbConfiguracaoSmsValor']['dt_vigencia_fim']); ?>&nbsp;</td>
                <td><?php echo $this->Formatacao->moeda($glbConfiguracaoSmsValor['GlbConfiguracaoSmsValor']['valor_sms']); ?>&nbsp;</td>
                <td style="min-width: 130px;">
                    <?php echo $this->Html->image("visualizar.png", array("alt" => "Visualizar", 'url' => array('action' => 'view', $glbConfiguracaoSmsValor['GlbConfiguracaoSmsValor']['cd_configuracao']))); ?>
                    <?php echo $this->Html->image("editar.png", array("alt" => "Editar", 'url' => array('action' => 'edit', $glbConfiguracaoSmsValor['GlbConfiguracaoSmsValor']['cd_configuracao']))); ?>
                    <?php echo $this->Form->postLink($this->Html->image("excluir.png", array("alt" => "Excluir")), array('action' => 'delete', $glbConfiguracaoSmsValor['GlbConfiguracaoSmsValor']['cd_configuracao']), array('escape' => false), __('Tem certeza de que deseja excluir: %s?', $glbConfiguracaoSmsValor['GlbConfiguracaoSmsValor']['cd_configuracao'])); ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <p>
        <?php
        echo $this->Paginator->counter(array(
            'format' => __('Página {:page} de {:pages}, mostrando {:current} registros de um total de {:count}, iniciando em {:start}, finalizando em {:end}')
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
