<?php
echo $this->Html->script('jquery.min.js');
echo $this->Html->script('jquery-ui.min.js');
echo $this->Html->script('jquery-ui-timepicker-addon.js');
echo $this->Html->script('jquery.maskedinput.min.js');
echo $this->Html->script('select2.min.js');
echo $this->Html->script('moment.js');
echo $this->Html->script('jquery.validate.js');
echo $this->Html->script('additional-methods.js');
echo $this->Html->script('jquery.ui.datepicker.validation.js');

echo $this->Html->css('jquery-ui-1.10.3.custom');
echo $this->Html->css('select2');
?>
<script>
    $(document).ready(function() {

        $("#marcaTodasFilial").prop('checked', true);
        $(".filiais").prop('checked', true);

        $.datepicker.regional['pt'] = {
            closeText: 'Fechar',
            prevText: '<Anterior',
            nextText: 'Seguinte',
            currentText: 'Hoje',
            monthNames: ['Janeiro', 'Fevereiro', 'Mar&ccedil;o', 'Abril', 'Maio', 'Junho',
                'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
            monthNamesShort: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun',
                'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
            dayNames: ['Domingo', 'Segunda-feira', 'Ter&ccedil;a-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'S&aacute;bado'],
            dayNamesShort: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'S&aacute;b'],
            dayNamesMin: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'S&aacute;b'],
            weekHeader: 'Sem',
            dateFormat: 'dd/mm/yy',
            firstDay: 0,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: ''
        };
        $.datepicker.setDefaults($.datepicker.regional['pt']); 

        $('#dt_referencia').datepicker({dateFormat: 'dd/mm/yy'});

        (function ($) {
            $('#RelatoriosEstoqueDetalhadoForm').validate({
                rules: {
                    dt_referencia : 'dateITA'
                },
                messages: {
                    dt_referencia : '<b style="color:red">Por favor, selecione uma data v&aacute;lida!</b>'
                }
            });

            $('#dt_referencia').on('onChange', function () {
                $('#RelatoriosEstoqueDetalhadoForm').valid();
            });

        })(jQuery);

        $('#cancelar').click(function() {
            window.location = "<?php echo $this->Html->url(array('action' => 'index')); ?>";
            return false;
        });
        
        $("#marcaTodasFilial").change(function() {
            $('.filiais').each(function(){
                if ($("#marcaTodasFilial").prop("checked")){ 
                    $(this).prop("checked", true);
                }else{
                    $(this).prop("checked", false);
                }               
            });
        });
        
    });
</script>

<style>
    body {
        font-family: Verdana, sans-serif;
        font-size: 13px;
    }
    select { 
        font-size: 15px; 
        font-family: Tahoma, sans-serif; 
        color: #9E2424; 
        min-width: 220px; 
    }
    input { 
        font-size: 15px; 
        font-family: Tahoma, sans-serif; 
        color: #9E2424; 
        width: 100px;
    } 
    .container { 
        border: 2px solid #ccc; 
        width: 250px; 
        height: 145px; 
        overflow-y: scroll; 
    }
    .container2 { 
        border: 0px solid #ccc; 
        width: 300px; 
        height: 190px;
    }
    #Menu {
        padding: 0;
        margin: 0;
        list-style-type: none;
        font-size: 13px;
        color: #717171;
        width: 280px;
    }
    #Menu li {
        border-bottom: 1px solid #eeeeee;
        padding: 7px 10px 7px 10px;
    }
    #Menu li:hover {
        color: #000;
        background-color: #eeeeee;
    }
    #tabelaTitulo {
        border-collapse: collapse;
        width: 300px;
        text-align: center;
        border: none;
        border: solid 0;
    }
    #tabela {
        border-collapse: collapse;
        width: 600px;
        text-align: center;
        border: none;
    }
    .coluna {
        width: 50px;
        height: 10px;
        font-size: 12px;
    }
    .coluna:hover {
        background-color: #eeeeee;
    }
    .descricao {
        width: 350px;
    }
    .info-box {
        background-color: #f0f8ff;
        border: 1px solid #4682b4;
        border-radius: 5px;
        padding: 10px;
        margin: 10px 0;
    }
    .info-box h4 {
        margin-top: 0;
        color: #4682b4;
    }
</style>

<?php echo $this->Form->create('Relatorios', array('target' => '_blank')); ?>

<div class="glbQuestionarios index">
    <h2><?php echo __('Relatório de Estoque Detalhado por Família/Grupo'); ?></h2>
    
    <div class="info-box">
        <h4>📊 Sobre este Relatório</h4>
        <p>Este relatório exibe o estoque detalhado agrupado por Família ou Grupo de produtos, mostrando:</p>
        <ul>
            <li><strong>Custo do Estoque:</strong> Valor total em R$ do estoque</li>
            <li><strong>Quantidade:</strong> Quantidade total de itens em estoque</li>
            <li><strong>Total de SKUs:</strong> Quantidade de produtos diferentes</li>
            <li><strong>Percentuais:</strong> Representatividade sobre o total (quantidade e valor)</li>
        </ul>
    </div>

    <table width="700" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <table id="tabelaTitulo">
                    <tr>
                        <td>
                            <fieldset style="border: solid 1px; width: 700px; margin-top: 10px; min-height: 250px;">
                                <legend><strong>Filtros do Relatório</strong></legend>
                                
                                <!-- Data de Referência -->
                                <table width="100%" cellpadding="5">
                                    <tr>
                                        <td width="200"><strong>Data de Referência:</strong></td>
                                        <td>
                                            <input type="text" name="dt_referencia" id="dt_referencia" 
                                                   value="<?php echo date('d/m/Y'); ?>" 
                                                   style="width: 150px;" />
                                            <small>(Data para cálculo do estoque)</small>
                                        </td>
                                    </tr>
                                </table>

                                <!-- Seleção de Filiais -->
                                <fieldset style="border: solid 1px; margin: 10px 0;">
                                    <legend><strong>Filiais</strong></legend>
                                    <table>
                                        <tr>
                                            <td>
                                                <input type="checkbox" id="marcaTodasFilial" checked />
                                                <label for="marcaTodasFilial"><strong>Marcar/Desmarcar Todas</strong></label>
                                            </td>
                                        </tr>
                                    </table>
                                    <div class="container">
                                        <table style="width: 100%;">
                                            <?php foreach ($filiais as $filial): ?>
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" 
                                                               class="filiais" 
                                                               name="data[Relatorios][filial][]" 
                                                               value="<?php echo $filial['PrcFilial']['cd_filial']; ?>" 
                                                               checked />
                                                        <?php echo $filial['PrcFilial']['nm_fant']; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </table>
                                    </div>
                                </fieldset>

                                <!-- Tipo de Agrupamento -->
                                <table width="100%" cellpadding="5">
                                    <tr>
                                        <td width="200"><strong>Agrupar por:</strong></td>
                                        <td>
                                            <select name="tipo_agrupamento" style="width: 200px;">
                                                <option value="FAMILIA">Família</option>
                                                <option value="GRUPO">Grupo</option>
                                            </select>
                                        </td>
                                    </tr>
                                    
                                    <!-- Ordenação -->
                                    <tr>
                                        <td><strong>Ordenar por:</strong></td>
                                        <td>
                                            <select name="ordenacao" style="width: 200px;">
                                                <option value="VALOR_DESC">Valor (Maior para Menor)</option>
                                                <option value="VALOR_ASC">Valor (Menor para Maior)</option>
                                                <option value="QTDE_DESC">Quantidade (Maior para Menor)</option>
                                                <option value="QTDE_ASC">Quantidade (Menor para Maior)</option>
                                                <option value="NOME">Nome (A-Z)</option>
                                            </select>
                                        </td>
                                    </tr>

                                    <!-- Exibir Estoque Zerado -->
                                    <tr>
                                        <td colspan="2">
                                            <input type="checkbox" 
                                                   name="exibir_estoque_zerado" 
                                                   id="exibir_estoque_zerado" 
                                                   value="1" />
                                            <label for="exibir_estoque_zerado">
                                                <strong>Incluir categorias com estoque zerado</strong>
                                            </label>
                                        </td>
                                    </tr>

                                    <!-- Tipo de Arquivo -->
                                    <tr>
                                        <td><strong>Tipo de Arquivo:</strong></td>
                                        <td>
                                            <select name="data[Relatorios][tipo_arquivo]" style="width: 200px;">
                                                <option value="HTML">HTML (Visualizar na tela)</option>
                                                <option value="EXCEL">Excel</option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>

                            </fieldset>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- Botões -->
        <tr>
            <td style="padding-top: 15px;">
                <input type="submit" class="btn btn-primary" value="Gerar Relatório" />
                <input type="button" class="btn btn-default" id="cancelar" value="Cancelar" />
            </td>
        </tr>
    </table>
</div>

<?php echo $this->Form->end(); ?>
