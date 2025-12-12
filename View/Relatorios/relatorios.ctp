<?php

echo $this->Html->script('jquery-ui.js');
echo $this->Html->script('jquery-ui-timepicker-addon.js');
echo $this->Html->script('jquery.maskedinput.min.js');
echo $this->Html->script('select2.min.js');
echo $this->Html->css('jquery-ui-1.10.3.custom');
echo $this->Html->css('select2');
?>

<style>
    .containerRelatorios{
        font-size:12px !important;
        position: relative;
        top: 2em;
        /* height: 460px;*/
    }

</style>
<div class="containerRelatorios">
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="col-md-12">
                <?php
                $permissoes = $_SESSION;
                /*
                  echo $this->Html->link('<div class="relatorioOpcoes">' . $this->Html->image('relatorioEnvioSms.png', array('alt' => 'questionarios', "width" => "140px")) . '<br>Relatório de Envio de SMS</div>', array('controller' => 'Relatorios', 'action' => 'envioSms'), array('escape' => false));

                  echo $this->Html->link('<div class="relatorioOpcoes">' . $this->Html->image('relatorioCobranca.png', array('alt' => 'relatorios', "width" => "140px")) . '<br>Relatório de Cobrança</div>', array('controller' => 'Relatorios', 'action' => 'smsCobranca'), array('escape' => false));

                  echo $this->Html->link('<div class="relatorioOpcoes" id="smsValor">' . $this->Html->image('relatorioSmsValorSintetico.png', array('alt' => 'relatorios', "width" => "140px")) . '<br>SMS X Valor SMS</div>', array('controller' => 'Relatorios', 'action' => 'smsValor'), array('escape' => false));

                  echo $this->Html->link('<div class="relatorioOpcoes">' . $this->Html->image('fluxoVendas.png', array('alt' => 'relatorios', "width" => "140px")) . '<br>Fluxo de Orçamentos por Hora</div>', array('controller' => 'Relatorios', 'action' => 'fluxoVendasHora'), array('escape' => false));

                  echo $this->Html->link('<div class="relatorioOpcoes">' . $this->Html->image('relatorioEcommerce.png', array('alt' => 'relatorios', "width" => "140px")) . '<br>Acompanhamento Pedido Ecommerce</div>', array('controller' => 'Relatorios', 'action' => 'acompanhamentoEcommerce', 'painel'), array('escape' => false));

                  echo $this->Html->link('<div class="relatorioOpcoes">' . $this->Html->image('relatorioAtendimentos.png', array('alt' => 'relatorios', "width" => "140px")) . '<br>Relatório de Atendimentos</div>', array('controller' => 'Relatorios', 'action' => 'atendimentos'), array('escape' => false));

                  echo $this->Html->link('<div class="relatorioOpcoes">' . $this->Html->image('relatorioSugestoes.png', array('alt' => 'relatorios', "width" => "140px")) . '<br>Relatório de Sugestões</div>', array('controller' => 'Relatorios', 'action' => 'sugestoes'), array('escape' => false));

                  echo $this->Html->link('<div class="relatorioOpcoes">' . $this->Html->image('cargo.png', array('alt' => 'relatorios', "width" => "140px")) . '<br>Relatório de Inadimplencia Cargo/Ano</div>', array('controller' => 'Relatorios', 'action' => 'inadimplencia'), array('escape' => false));

                  echo $this->Html->link('<div class="relatorioOpcoes">' . $this->Html->image('relatorioSimplificado.png', array('alt' => 'relatorios', "width" => "140px")) . '<br>Relatório Simplificado de Qtdes</div>', array('controller' => 'Relatorios', 'action' => 'simplificadoQuantidade'), array('escape' => false));

                  echo $this->Html->link('<div class="relatorioOpcoes">' . $this->Html->image('relatorioAtendimento.png', array('alt' => 'relatorios', "width" => "140px")) . '<br>Relatório de Descrição de Atendimento</div>', array('controller' => 'Relatorios', 'action' => 'descricaoAtendimento'), array('escape' => false));

                  echo $this->Html->link('<div class="relatorioOpcoes">' . $this->Html->image('tempoCrediario.png', array('alt' => 'relatorios', "width" => "140px")) . '<br>Relatório de Acompanhamento Tempo Crediário</div>', array('controller' => 'Relatorios', 'action' => 'acompanhamentoTempoCrediario'), array('escape' => false));

                  echo $this->Html->link('<div class="relatorioOpcoes">' . $this->Html->image('atendimentoAniversariante.png', array('alt' => 'relatorios', "width" => "140px")) . '<br>Relatório Atendimentos de Aniversariante</div>', array('controller' => 'Relatorios', 'action' => 'atendimentoAniversariante'), array('escape' => false));

                  echo $this->Html->link('<div class="relatorioOpcoes">' . $this->Html->image('retornoContatoPesquisa.png', array('alt' => 'relatorios', "width" => "140px")) . '<br>Relatório Retorno de Contato/Pesquisa</div>', array('controller' => 'Relatorios', 'action' => 'retornoContatoPesquisa'), array('escape' => false));

                  echo $this->Html->link('<div class="col-md-2">' . $this->Html->image('respostaPesquisas.png', array('alt' => 'relatorios', "width" => "140px")) . '<br>Relatório Resposta por Pesquisa</div>', array('controller' => 'Relatorios', 'action' => 'respostaPesquisa'), array('escape' => false));
                 */
                if (in_array('Vendas por Loja', $permissoes['Questionarios']['permissoes'])) {
                    echo $this->Html->link('<div class="col-md-2">' . $this->Html->image('relatorio_vendas.png', array('alt' => 'relatorios', "width" => "160px", "height" => "200px")) . ' Vendas por Loja</div>', "javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/vendas_por_loja');", array('escape' => false));
                }
                if (in_array('Vendas por Vendedor', $permissoes['Questionarios']['permissoes'])) {
                    echo $this->Html->link('<div class="col-md-2">' . $this->Html->image('relatorio_vendedor.png', array('alt' => 'relatorios', "width" => "160px", "height" => "200px")) . ' Vendas por Vendedor</div>', "javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/vendas_por_vendedor');", array('escape' => false));
                }
                if (in_array('Fluxo Recebimento Parcela Hora', $permissoes['Questionarios']['permissoes'])) {
                    echo $this->Html->link('<div class="col-md-2">' . $this->Html->image('relatorio_grafico_vendas.png', array('alt' => 'relatorios', "width" => "160px", "height" => "200px")) . ' Fluxo de Recebimento de Parcela</div>', "javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/fluxo_recebimento_parcela_hora');", array('escape' => false));
                }
                if (in_array('Fluxo Vendas Hora', $permissoes['Questionarios']['permissoes'])) {
                    echo $this->Html->link('<div class="col-md-2">' . $this->Html->image('relatorioOrcamento.png', array('alt' => 'relatorios', "width" => "160px", "height" => "200px")) . ' Fluxo de Or&ccedil;amento por Hora</div>', "javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/fluxo_vendas_hora');", array('escape' => false));
                }
                if (in_array('Analise Lucros', $permissoes['Questionarios']['permissoes'])) {
                    echo $this->Html->link('<div class="col-md-2">' . $this->Html->image('relatorio_lojas.png', array('alt' => 'relatorios', "width" => "160px", "height" => "200px")) . ' An&aacute;lise de Lucros</div>', "javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/analise_lucros');", array('escape' => false));
                }
                
                if (in_array('Grafico Orcamento Vendas', $permissoes['Questionarios']['permissoes'])) {
                    echo $this->Html->link('<div class="col-md-2">' . $this->Html->image('chart.png', array('alt' => 'relatorios', "width" => "160px", "height" => "200px")) . ' Or&ccedil;amento de Venda</div>', "javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/orcamento_venda');", array('escape' => false));
                }
                ?>
            </div>
        </div>

        <div class="row-fluid">
            <div class="col-md-12">
                <?php
                if (in_array('Comparativo Vendas', $permissoes['Questionarios']['permissoes'])) {
                    echo $this->Html->link('<div class="col-md-2">' . $this->Html->image('relatorio_comparativo_vendas.png', array('alt' => 'relatorios', "width" => "160px", "height" => "200px")) . ' Comparativo de Vendas</div>', "javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/comparativo_vendas');", array('escape' => false));
                }
                if (in_array('Entrada x Vendas', $permissoes['Questionarios']['permissoes'])) {
                    echo $this->Html->link('<div class="col-md-2">' . $this->Html->image('relatorio_lojas.png', array('alt' => 'relatorios', "width" => "160px", "height" => "200px")) . ' Entrada x Vendas por Per&iacute;odo </div>', "javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/entradas_x_vendas');", array('escape' => false));
                }
                if (in_array('Vendas Estoque Marca', $permissoes['Questionarios']['permissoes'])) {
                    echo $this->Html->link('<div class="col-md-2">' . $this->Html->image('relatorio_lojas.png', array('alt' => 'relatorios', "width" => "160px", "height" => "200px")) . ' Vendas/Estoque por Marca </div>', "javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/vendas_estoque_marca');", array('escape' => false));
                }
                if (in_array('Vendas Estoque Grupo', $permissoes['Questionarios']['permissoes'])) {
                    echo $this->Html->link('<div class="col-md-2">' . $this->Html->image('relatorio_lojas.png', array('alt' => 'relatorios', "width" => "160px", "height" => "200px")) . ' Vendas/Estoque por Grupo </div>', "javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/vendas_estoque_grupo');", array('escape' => false));
                }
                if (in_array('Vendas Estoque Familia', $permissoes['Questionarios']['permissoes'])) {
                    echo $this->Html->link('<div class="col-md-2">' . $this->Html->image('relatorio_lojas.png', array('alt' => 'relatorios', "width" => "160px", "height" => "200px")) . ' Relatório Vendas/Estoque por Familia </div>', "javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/vendas_estoque_familia');", array('escape' => false));
                }
                
                // Relatório de Estoque Detalhado por Família/Grupo
                if (in_array('Relatórios', $permissoes['Questionarios']['permissoes'])) {
                    echo $this->Html->link('<div class="col-md-2">' . $this->Html->image('relatorio_lojas.png', array('alt' => 'relatorios', "width" => "160px", "height" => "200px")) . ' 📦 Estoque Detalhado por Família/Grupo </div>', "javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/estoque_detalhado');", array('escape' => false));
                }
                
                if (in_array('Fluxo Caixa', $permissoes['Questionarios']['permissoes'])) {
                    echo $this->Html->link('<div class="col-md-2">' . $this->Html->image('relatorio_comparativo_vendas.png', array('alt' => 'relatorios', "width" => "160px", "height" => "200px")) . ' Relatório Fluxo de Caixa</div>', "javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/fluxo_caixa');", array('escape' => false));
                }

                //WEDER pediu pra escoder 13/09/2016
                //echo $this->Html->link('<div class="col-md-2">' . $this->Html->image('relatorio_comparativo_vendas.png', array('alt' => 'relatorios', 'width' => "160px", "height" => "200px")) . ' Previs&atilde;o Financeira a Receber </div>', "javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/prev_finaceira_receber');", array('escape' => false));
                
                //relatorio de pedido de compras, 16134
                if (in_array('Pedido Compras', $permissoes['Questionarios']['permissoes'])) {
                    echo $this->Html->link('<div class="col-md-2">' . $this->Html->image('relatorio_pedido_compras.png', array('alt' => 'relatorios', 'width' => "160px", "height" => "200px")) . ' Pedido de Compras </div>', "javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/pedido_compras');", array('escape' => false));
                }
                
                //relatorio de pedido de compras, 16870
                if (in_array('Vendas Condicao Pagamento', $permissoes['Questionarios']['permissoes'])) {
                    echo $this->Html->link('<div class="col-md-2">' . $this->Html->image('relatorio_vendas_condicao_pagamento.png', array('alt' => 'relatorios', 'width' => "160px", "height" => "200px")) . ' Vendas por Condi&ccedil;&atilde;o de Pagamento </div>', "javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/vendas_condicao_pagamento');", array('escape' => false));
                }
                
                if (in_array('Contas Pagar Filial Data', $permissoes['Questionarios']['permissoes'])) {
                    echo $this->Html->link('<div class="col-md-2">' . $this->Html->image('relatorio_lojas.png', array('alt' => 'relatorios', "width" => "160px", "height" => "200px")) . ' Contas a Pagar Filial/Data </div>', "javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/prev_financeira_pagar');", array('escape' => false));
                }
                
                //25031: Sugestão de relatório SysApp - Controle de vendas
                if (in_array('Controle Vendas', $permissoes['Questionarios']['permissoes'])) {
                    echo $this->Html->link('<div class="col-md-2">' . $this->Html->image('controle_vendas.jpg', array('alt' => 'relatorios', 'width' => "160px", "height" => "200px")) . ' Controle Vendas </div>', "javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/controle_vendas');", array('escape' => false));
                }
                
                ?>

            </div>
        </div>

        <!--        <div class="row-fluid">
                    <div class="col-md-12">-->
        <?php
        /*
          if (in_array('Fluxo Caixa', $permissoes['Questionarios']['permissoes'])) {
          echo $this->Html->link('<div class="col-md-2">' . $this->Html->image('relatorio_comparativo_vendas.png', array('alt' => 'relatorios', "width" => "160px", "height" => "200px")) . ' Relatório Fluxo de Caixa</div>', "javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/fluxo_caixa');", array('escape' => false));
          }

          echo $this->Html->link('<div class="col-md-2">' . $this->Html->image('relatorio_comparativo_vendas.png', array('alt' => 'relatorios', 'width' => "160px", "height" => "200px")) . ' Previs&atilde;o Financeira a Receber </div>', "javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/prev_finaceira_receber');", array('escape' => false));

          //relatorio de pedido de compras, 16134
          echo $this->Html->link('<div class="col-md-2">' . $this->Html->image('relatorio_pedido_compras.png', array('alt' => 'relatorios', 'width' => "160px", "height" => "200px")) . ' Pedido de Compras </div>', "javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/pedido_compras');", array('escape' => false));
         * */
        ?>
        <!--            </div>
                </div>-->
    </div>
</div>
