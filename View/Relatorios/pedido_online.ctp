<?php
echo $this->Html->script('jquery-1.10.2.min.js');
echo $this->Html->script('jquery-ui.js');
echo $this->Html->script('jquery.appendGrid-1.3.2.min');
//echo $this->Html->script('jquery-ui-timepicker-addon.js');
//echo $this->Html->script('jquery.maskedinput.min.js');
//echo $this->Html->script('select2.min.js');

echo $this->Html->css('jquery-ui-1.10.3.custom_pedidoOnline');
echo $this->Html->css('jquery.appendGrid-1.3.2');
echo $this->Html->css('pedido_online');

$opcoes = "0: 'Opção 1', 2: 'Opção 2', 3: 'Opção 3', 4: 'Opção 4', 5: 'Opção 5', 6: 'Opção 6'"
?>
<script>
    
    $(function() {
        $("#tabs").tabs();
    });
    function teste(rowIndex) {
        alert('ai ai ai' + rowIndex);
    }
    $(function() {
        // Initialize appendGrid
        $('#listaProdutos').appendGrid('init', {
            caption: 'Produtos do Pedido',
            initRows: 1,
            columns: [
                {name: 'subgrupo', display: 'Subgrupo', type: 'select', ctrlOptions: {<?php echo $opcoes; ?>}, onChange: function(evt, rowIndex) {
//                    alert('You have changed value of Album at row ' + rowIndex);
                        teste(rowIndex);
                    }},
                {name: 'linha', display: 'Linha', type: 'text', ctrlAttr: {maxlength: 100, title: 'Qual a linha'}, ctrlCss: {width: '100px'}, uiTooltip: {show: true}},
                {name: 'ref', display: 'Ref.', type: 'ui-spinner', class: 'teste', ctrlAttr: {maxlength: 4}, ctrlCss: {width: '40px'}, uiOption: {min: 1}},
                {name: 'material', display: 'Material', type: 'ui-autocomplete', uiOption: {source: ['Hong Kong', 'Taiwan', 'Japan', 'Korea', 'US', 'Others']}},
                {name: 'cor', display: 'Cor', type: 'ui-datepicker', ctrlAttr: {maxlength: 10}, ctrlCss: {width: '80px'}, uiOption: {dateFormat: 'yy/mm/dd'}},
                {name: 'inf', display: 'Inf.', type: 'text', ctrlAttr: {maxlength: 10}, ctrlCss: {width: '100px', 'text-align': 'right'}, value: 0},
                {name: 'qtd_total', display: 'Qtd Total', type: 'ui-spinner', ctrlAttr: {maxlength: 4}, ctrlCss: {width: '40px'}, uiOption: {min: 1}},
                {name: 'vlr_total', display: 'Vlr Total', type: 'text', ctrlAttr: {maxlength: 10}, ctrlCss: {width: '100px', 'text-align': 'right'}, value: 0},
                {name: 'custo_bt', display: 'Custo BT', type: 'text', ctrlAttr: {maxlength: 10}, ctrlCss: {width: '100px', 'text-align': 'right'}, value: 0},
                {name: 'custo_liq', display: 'Custo Líq', type: 'text', ctrlAttr: {maxlength: 10}, ctrlCss: {width: '50px', 'text-align': 'right'}, value: 0},
                {name: 'preco_vd', display: 'Preço Vd.', type: 'text', ctrlAttr: {maxlength: 10}, ctrlCss: {width: '50px', 'text-align': 'right'}, value: 0},
                {name: 'mk', display: 'MK', type: 'text', ctrlAttr: {maxlength: 10}, ctrlCss: {width: '50px', 'text-align': 'right'}, value: 0},
                {name: 'custo_vdor', display: 'Custo Vdor', type: 'text', ctrlAttr: {maxlength: 10}, ctrlCss: {width: '50px', 'text-align': 'right'}, value: 0},
                {name: 'inf2', display: 'Inf.', type: 'text', ctrlAttr: {maxlength: 10}, ctrlCss: {width: '50px', 'text-align': 'right'}, value: 0}
            ],
            initData: [
//                {'subgrupo': '', 'linha': '', 'Year': '', 'Origin': '', 'StockIn': '', 'Price': ''},
                {'subgrupo': '', 'linha': '', 'Year': '', 'Origin': '', 'StockIn': '', 'Price': ''}
            ],
            hideButtons: {moveUp: true, moveDown: true}
        });
    });
</script>
<?php echo $this->Form->create('Relatorios', array('target' => '_blank', 'id' => 'testex')); ?>

<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Dados do Pedido</a></li>
        <li><a href="#tabs-2">Resumo Financeiro</a></li>
    </ul>
    <div id="tabs-1">
        <div class="dadosPedido">
            <div class="divImput">
                <label for="codigo">Código</label>
                <input class="inputP" type="number" readonly name="codigo" id="codigo" autofocus/>
            </div>
            <div class="divImput">
                <label for="data_pedido">Data pedido</label>
                <input class="inputP" type="text" name="data_pedido" placeholder="__/__/____" value="<?php echo date("d/m/Y"); ?>" id="data_pedido"/>
            </div>
            <div class="divImput">
                <label for="comprador">Comprador</label>
                <input class="inputG" type="text" name="comprador" placeholder="Nome do Comprador" required id="comprador"/>
            </div>
            <div class="divImput">
                <label for="tipo_pedido">Tipo do pedido</label>
                <select class="inputM" name="tipo_pedido" id="tipo_pedido">
                    <option selected value="">Pedido Fornecedor</option>
                    <option value="">Pedido Bonificação</option>
                </select>
            </div>
            <div class="divImput">
                <label for="n_pedido_fornecedor">Nº Pedido fornecedor</label>
                <input class="inputMi" type="text" name="n_pedido_fornecedor" id="n_pedido_fornecedor"/>
            </div>
            <div class="divImput">
                <label for="representante">Representante</label>
                <input class="inputGG" type="text" name="representante" placeholder="Nome do Representante" id="representante"/>
            </div>
            <div class="divImput">
                <label for="cod_fabricante">Código</label>
                <input class="inputMi" type="text" name="cod_fabricante" id="cod_fabricante"/>
            </div>
            <div class="divImput">
                <label for="fabricante">Fabricante</label>
                <input class="inputGG" type="text" name="fabricante" placeholder="Nome do Fabricante" id="fabricante"/>
            </div>
            <div class="divImput">
                <label for="dt_faturamento">Dt faturamento</label>
                <input class="inputMi" type="date" name="dt_faturamento" placeholder="__/__/____" id="dt_faturamento"/>
            </div>
            <div class="divImput">
                <label for="cond_pagamento">Cond. Pagamento</label>
                <input class="inputG" type="text" name="cond_pagamento" id="cond_pagamento"/>
            </div>
            <div class="divImput">
                <label for="frete">Frete</label>
                <select class="inputM" name="frete" id="frete">
                    <option value="">C.I.F.</option>
                    <option value="">F.O.B.</option>
                </select>
            </div>
        </div>
        <div class="dadosPedido">
            <fieldset>
                <legend>Parâmetros da Compra</legend>
                <div class="divImput">
                    <label for="tipo_cobranca">Tipo de Cobrança</label>
                    <select class="inputM" name="tipo_cobranca" id="tipo_cobranca">
                        <option value="">Normal</option>
                        <option value="">Vendor</option>
                    </select>
                </div>
                <div class="divImput">
                    <label for="tx_vendor">Tx. vendor(%)</label>
                    <input type="text" name="tx_vendor" id="tx_vendor"/>
                </div>
                <div class="divImput">
                    <label for="beneficios">Benefícios</label>
                    <input type="text" name="beneficios" id="beneficios"/>
                </div>
                <div class="divImput">
                    <label for="desc_pontual">Desc. Pontual</label>
                    <input type="text" name="desc_pontual" id="desc_pontual"/>
                </div>
                <br><br>
                <br><br>
                <div class="tabelaDescontos">
                    <div class="descontos">Descontos(%)</div>
                    <div class="divImputDescontos">
                        <label for="desc_pontual">Desc. 01</label>
                        <input class="inputPP" type="text" name="desc_pontual" id="desc_pontual"/>
                    </div>
                    <div class="divImputDescontos">
                        <label for="desc_pontual">Desc. 01</label>
                        <input class="inputPP" type="text" name="desc_pontual" id="desc_pontual"/>
                    </div>
                    <div class="divImputDescontos">
                        <label for="desc_pontual">Desc. 02</label>
                        <input class="inputPP" type="text" name="desc_pontual" id="desc_pontual"/>
                    </div>
                    <div class="divImputDescontos">
                        <label for="desc_pontual">Desc. 03</label>
                        <input class="inputPP" type="text" name="desc_pontual" id="desc_pontual"/>
                    </div>
                    <div class="divImputDescontos">
                        <label for="desc_pontual">Desc. 04</label>
                        <input class="inputPP" type="text" name="desc_pontual" id="desc_pontual"/>
                    </div>
                    <div class="divImputDescontos">
                        <label for="desc_pontual">Desc. 05</label>
                        <input class="inputPP" type="text" name="desc_pontual" id="desc_pontual"/>
                    </div>
                    <div class="divImputDescontos">
                        <label for="desc_pontual">Desc. 06</label>
                        <input class="inputPP" type="text" name="desc_pontual" id="desc_pontual"/>
                    </div>
                    <div class="divImputDescontos">
                        <label for="desc_pontual">Desc. 07</label>
                        <input class="inputPP" type="text" name="desc_pontual" id="desc_pontual"/>
                    </div>
                    <div class="divImputDescontos">
                        <label for="desc_pontual">NrNForm</label>
                        <input class="inputPP" type="text" name="desc_pontual" id="desc_pontual"/>
                    </div>
                </div>
            </fieldset>
        </div>
        <table id="listaProdutos"></table>
        <br>
        <div class="dadosPedido">
            <div class="divImput">
                <label>Observações</label>
                <textarea rows="3" cols="100"></textarea>
            </div>
        </div>
        <div class="dadosPedido">
            <div class="divImput">
                <label>Qtde Pares</label>
                <input class="inputM" type="number" name="dt_faturamento" id="dt_faturamento"/>
            </div>
            <div class="divImput">
                <label>Total do pedido</label>
                <input class="inputM" type="number" name="dt_faturamento"  id="dt_faturamento"/>
            </div>
            <div class="divImput">
                <label>Total pedido vendor</label>
                <input class="inputM" type="number" name="dt_faturamento" id="dt_faturamento"/>
            </div>
        </div>
        <br><br>
    </div>
    <div id="tabs-2">
        <div class="tabelaPosicao">
            <div class="descontos">Posição</div>
            <div style="width: 100%">
                <div class="divImputDescontos">
                    <label for="desc_pontual">Descrição</label>
                    <input class="inputM" type="text" name="desc_pontual" id="desc_pontual" value="Verba Planejada" readonly="readonly"/>
                </div>
                <div class="divImputDescontos">
                    <label for="desc_pontual">Fev/2014</label>
                    <input class="inputM" type="text" name="desc_pontual" id="desc_pontual"/>
                </div>
                <div class="divImputDescontos">
                    <label for="desc_pontual">Mar/2014</label>
                    <input class="inputM" type="text" name="desc_pontual" id="desc_pontual"/>
                </div>
                <div class="divImputDescontos">
                    <label for="desc_pontual">Abr/2014</label>
                    <input class="inputM" type="text" name="desc_pontual" id="desc_pontual"/>
                </div>
                <div class="divImputDescontos">
                    <label for="desc_pontual">Mai/2014</label>
                    <input class="inputM" type="text" name="desc_pontual" id="desc_pontual"/>
                </div>
                <div class="divImputDescontos">
                    <input class="inputM" type="text" name="desc_pontual" id="desc_pontual" value="Utilizado" readonly="readonly"/>
                </div>
                <div class="divImputDescontos">
                    <input class="inputM" type="text" name="desc_pontual" id="desc_pontual"/>
                </div>
                <div class="divImputDescontos">
                    <input class="inputM" type="text" name="desc_pontual" id="desc_pontual"/>
                </div>
                <div class="divImputDescontos">
                    <input class="inputM" type="text" name="desc_pontual" id="desc_pontual"/>
                </div>
                <div class="divImputDescontos">
                    <input class="inputM" type="text" name="desc_pontual" id="desc_pontual"/>
                </div>
                <div class="divImputDescontos">
                    <input class="inputM" type="text" name="desc_pontual" id="desc_pontual" value="Saldo" readonly="readonly"/>
                </div>
                <div class="divImputDescontos">
                    <input class="inputM" type="text" name="desc_pontual" id="desc_pontual"/>
                </div>
                <div class="divImputDescontos">
                    <input class="inputM" type="text" name="desc_pontual" id="desc_pontual"/>
                </div>
                <div class="divImputDescontos">
                    <input class="inputM" type="text" name="desc_pontual" id="desc_pontual"/>
                </div>
                <div class="divImputDescontos">
                    <input class="inputM" type="text" name="desc_pontual" id="desc_pontual"/>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$options = array('id' => 'enviar');
echo $this->Form->end($options);
?>
