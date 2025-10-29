<div class="glbQuestionarios view">
    <h2><?php echo __('Configuração de Valor de SMS'); ?></h2>
    <dl>
        <dt><?php echo __('Cod Configuração'); ?></dt>
        <dd>
            &nbsp;
            <?php echo h($glbConfiguracaoSmsValor['GlbConfiguracaoSmsValor']['cd_configuracao']); ?>
        </dd>
        <dt><?php echo __('Data de Inicio'); ?></dt>
        <dd>
            <?php echo $this->Formatacao->dataCompleta($glbConfiguracaoSmsValor['GlbConfiguracaoSmsValor']['dt_vigencia_ini']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Data do Fim'); ?></dt>
        <dd>
            <?php echo $this->Formatacao->dataCompleta($glbConfiguracaoSmsValor['GlbConfiguracaoSmsValor']['dt_vigencia_fim']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Valor SMS'); ?></dt>
        <dd>
            <?php echo $this->Formatacao->moeda($glbConfiguracaoSmsValor['GlbConfiguracaoSmsValor']['valor_sms']); ?>
            &nbsp;
        </dd>
    </dl>
    <div class="actions" style="width: 500px;">
        <?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $glbConfiguracaoSmsValor['GlbConfiguracaoSmsValor']['cd_configuracao'])); ?>
        <?php echo $this->Html->link(__('Voltar'), array('action' => 'index')); ?>
    </div>
</div>
