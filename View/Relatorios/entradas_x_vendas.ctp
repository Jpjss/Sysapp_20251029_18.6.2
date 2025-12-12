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
    $(document).ready(function () {

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
        $("#dt_in").mask("99/99/9999");
        $("#dt_in").datepicker();
        $("#dt_fi").mask("99/99/9999");
        $("#dt_fi").datepicker();

        $("#dt_in_2").mask("99/99/9999");
        $("#dt_in_2").datepicker();
        $("#dt_fi_2").mask("99/99/9999");
        $("#dt_fi_2").datepicker();

        (function ($) {
            var currentYr = (new Date()).getFullYear(),
                    jan = new Date(currentYr, 0, 15, 12, 0, 0, 0),
                    jul = new Date(currentYr, 6, 15, 12, 0, 0, 0),
                    janOffset = -jan.getTimezoneOffset() / 60,
                    julOffset = -jul.getTimezoneOffset() / 60,
                    formatDate = function (dt) {
                        return dt.getDate() + '/' + (dt.getMonth() + 1) + '/' + dt.getFullYear();
                    },
                    dst;
            $('#RelatoriosAnaliseLucrosForm').validate({
                rules: {
                    dt_fi: 'dateITA',
                    dt_in: 'dateITA',
                    dt_fi_2: 'dateITA',
                    dt_in_2: 'dateITA',
                },
                messages: {
                    dt_fi: '<b style="color:red">Por favor, selecione uma data v&aacute;lida!</b>',
                    dt_in: '<b style="color:red">Por favor, selecione uma data v&aacute;lida!</b>',
                    dt_fi_2: '<b style="color:red">Por favor, selecione uma data v&aacute;lida!</b>',
                    dt_in_2: '<b style="color:red">Por favor, selecione uma data v&aacute;lida!</b>'
                }
            });

            $('#dt_fi').on('onChange', function () {
                $('#RelatoriosAnaliseLucrosForm').valid();
            });
            $('#dt_in').on('onChange', function () {
                $('#RelatoriosAnaliseLucrosForm').valid();
            });
            $('#dt_fi_2').on('onChange', function () {
                $('#RelatoriosAnaliseLucrosForm').valid();
            });
            $('#dt_in_2').on('onChange', function () {
                $('#RelatoriosAnaliseLucrosForm').valid();
            });

        })(jQuery);

        $('#cancelar').click(function () {
            window.location = "<?php echo $this->Html->url(array('action' => 'index')); ?>";
            return false;
        });

        $("#marcaTodasFilial").change(function () {
            $('.filiais').each(
                    function () {
                        if ($("#marcaTodasFilial").prop("checked")) {
                            $(this).prop("checked", true);
                        } else {
                            $(this).prop("checked", false);
                        }
                    }
            );
        });

        $("#marcaTodasDespesas").change(function () {
            $('.despesas').each(
                    function () {
                        if ($("#marcaTodasDespesas").prop("checked")) {
                            $(this).prop("checked", true);
                        } else {
                            $(this).prop("checked", false);
                        }
                    }
            );
        });

        $("#marcaTodasCategorias").change(function () {
            $('.categorias').each(
                    function () {
                        if ($("#marcaTodasCategorias").prop("checked")) {
                            $(this).prop("checked", true);
                        } else {
                            $(this).prop("checked", false);
                        }
                    }
            );
        });

        $('#enviar').click(function () {
            moment.locale('pt-br');
            var per_ini_vendas = moment($('#per_ini_fluxo').val(), 'DD/MM/YYYY');
            var per_fim_vendas = moment($('#per_fim_fluxo').val(), 'DD/MM/YYYY');
            var diff = per_fim_vendas.diff(per_ini_vendas, 'days');

            if (diff > '366') {
                $('.mensagem').html("<div class='alert alert-danger fade in' role='alert' size='2'><button type='button' class='close' data-dismiss='alert' aria-label='Fechar'><span aria-hidden='true'>&times;</span></button><strong>Selecione um per&iacute;odo inferior a 12 meses da data final!</strong></div>");
                $(".mensagem").fadeTo(3000, 500).slideUp(500, function () {
                    $(".mensagem").alert('close');
                });
                return false;
            }
        });

    });

</script>
<style>
    select{ font-size: 15px; font-family: Tahoma, sans-serif; color: #9E2424; min-width: 220px; }
    input{ font-size: 15px; font-family: Tahoma, sans-serif; color: #9E2424; width: 85px;} 
    .container { border:2px solid #ccc; width:250px; height: 145px; overflow-y: auto; overflow-x: hidden; }
    .container2 { border:0px solid #ccc; width:200px; height: 190px;}
    .container3 { border:2px solid #ccc; width: 210px; height: 230px; overflow-y: auto; overflow-x: hidden; }

    .tabelaPrincipal{
        width:auto;
        min-width:480px;
        text-align:center;
        /* border: 1px solid red;*/
    }
    #tabela{
        width:auto;
        min-width:190px;
        text-align:center;
        /* border: 2px solid green;*/       
    }
    tabela, tr, td {
        border:none;
    }
    .analiseLucros{
        font-size: 10px !important;
    }

</style>
<div class="analiseLucros">
    <h2><?php echo __('Entrada x Vendas Per&iacute;odo'); ?></h2>
    <div class='mensagem'>
    </div>

    <?php echo $this->Form->create('Relatorios'); ?>

    <table class="tabelaPrincipal">
        <tr>
            <td colspan="2"><b>Filiais</b><br>
                <div class="container3">
                    <table id="tabela">
                        <tr>
                            <td style="width: 13px;"><input type="checkbox" id="marcaTodasFilial" value="" /></td>
                            <td>TODAS FILIAIS</td>
                        </tr>
                        <?php foreach ($filiais as $filial) { ?>
                        <tr>
                            <td>
                                <input type="checkbox" class="filiais" name="data[Relatorios][filial][]" value="<?php echo $filial["PrcFilial"]["cd_filial"]; ?>" />
                            </td>
                            <td><?php echo utf8_encode($filial["PrcFilial"]["nm_fant"]); ?></td>
                        </tr>
                        <?php } ?>
                    </table>
                </div>  
            </td>
            <td>
                <div>
                    <table id='tabela'>
                        <tr>
                            <td>
                                <b>Período Vendas:</b>
                            </td>
                        </tr> 
                        <tr>
                            <td>
                                <input type="text" name="dt_in" id="dt_in" value="<?php echo date("01/m/Y") ?>"/><br> a <br></b><input name="dt_fi" value="<?php echo date("d/m/Y"); ?>" id="dt_fi" type="text">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>Período Entrada:</b>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="text" name="dt_in_2" id="dt_in_2" value="<?php echo date("01/m/Y") ?>"/><br> a <br></b><input name="dt_fi_2" value="<?php echo date("d/m/Y"); ?>" id="dt_fi_2" type="text">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="container4">
                                    <table>
                                        <tr>
                                            <td>
                                            <input type="checkbox" id="qtde_estoque_zerado" name="qtde_estoque_zerado"><td>Qtde Estoque Zerado</td>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                            <input type="checkbox" id="qtde_estoque_negativo" name="qtde_estoque_negativo"><td>Qtde Estoque Negativo</td>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                            <input type="checkbox" id="qtde_estoque_positivo" name="qtde_estoque_positivo" checked><td>Qtde Estoque Positivo</td>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        <td>
						   	<?php echo $this->Form->button('Visualizar', array('type' => 'submit', 'id' => 'enviar', 'class' => 'botaoCancel')); ?>
						  	<?php echo $this->Form->button('Voltar', array('type' => 'button', 'id' => 'cancelar', 'class' => 'botaoCancel', 'action' => 'index')); ?>
                        </td>
                    </table>
                </div>
            </td>
        </tr>
    </table>
</div>
