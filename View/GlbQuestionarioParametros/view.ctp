<div class="glbQuestionarioRespostas view">
    <h2><?php echo __('Parâmetro'); ?></h2>
    <dl>
        <dt><?php echo __('Descrição'); ?></dt>
        <dd>
            <?php echo h($parametro['GlbQuestionarioParametro']['ds_parametro_questionario']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Data de Cadastro'); ?></dt>
        <dd>
            <?php echo substr($this->Formatacao->dataCompleta($parametro['GlbQuestionarioParametro']['dt_cad']), 0, -10); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Desconsiderar Funcionários'); ?></dt>
        <dd>
            <?php echo ($parametro['GlbQuestionarioParametro']['desconsiderar_funcionarios'] == 1) ? "Sim" : "Não"; ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Status'); ?></dt>
        <dd>
            <?php
            switch ($parametro['GlbQuestionarioParametro']['sts_parametro_cobranca']) {
                case(0):
                    echo $this->Html->image('mesinativo.png', array('alt' => 'Inativo'));
                    break;
                case(1):
                    echo $this->Html->image('mesativo.png', array('alt' => 'Ativo'));
                    break;
                case(2):
                    echo $this->Html->image('mescancelado.png', array('alt' => 'Cancelado'));
                    break;
            }
            ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Região do Cliente'); ?></dt>
        <dd>
            <?php echo @$regiaoFilial['PrcRegiaoFilial']['ds_regiao']; ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Filiais'); ?></dt>
        <dd>
            <?php
            foreach ($filiais as $value) {
                echo utf8_encode($value['PrcFilial']['nm_fant']) . "<br>";
            }
            ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Qtd de compras'); ?></dt>
        <dd>
            <?php
            foreach ($qtdCompras as $value) {
                echo $value['FaixaQtd']['ds_parametro_faixa_quantidade_compra'] . "<br>";
            }
            ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Dt Últ. compra'); ?></dt>
        <dd>
            <?php
            foreach ($dtUltCompra as $value) {
                echo $value['DtUltimaCompra']['ds_parametro_data_ult_compra'] . "<br>";
            }
            ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Valor Últ. compra'); ?></dt>
        <dd>
            <?php
            foreach ($valorUltCompra as $value) {
                echo $value['ValorUltimaCompra']['ds_parametro_faixa_valor_ult_compra'] . "<br>";
            }
            ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Vlr Médio compra'); ?></dt>
        <dd>
            <?php
            foreach ($valorMedio as $value) {
                echo $value['ValorMedio']['ds_parametro_faixa_valor_medio_compra'] . "<br>";
            }
            ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Média de Atraso'); ?></dt>
        <dd>
            <?php
            foreach ($mediaAtraso as $value) {
                echo $value['MediaAtraso']['ds_parametro_faixa_media_atraso'] . "<br>";
            }
            ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Data de Cadastro'); ?></dt>
        <dd>
            <?php
            foreach ($dtCadastro as $value) {
                echo $value['DataCadastro']['ds_parametro_faixa_data_cadastro'] . "<br>";
            }
            ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Data de Atualização'); ?></dt>
        <dd>
            <?php
            foreach ($dtAtualizacao as $value) {
                echo $value['DataAtualizacao']['ds_parametro_faixa_data_atualizacao'] . "<br>";
            }
            ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Data do Último Pagamento'); ?></dt>
        <dd>
            <?php
            foreach ($dtUltPagamento as $value) {
                echo $value['UltimoPagamento']['ds_parametro_faixa_dt_ult_pagamento'] . "<br>";
            }
            ?>
            &nbsp;
        </dd>
    </dl>
    <div class="actions">
        <?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $parametro['GlbQuestionarioParametro']['cd_parametro_questionario'])); ?>
        <?php echo $this->Html->link(__('Voltar'), array('action' => 'index')); ?>
    </div>
</div>
