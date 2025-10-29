<?php
echo $this->Html->script('jquery-ui.js');
echo $this->Html->script('jquery-ui-timepicker-addon.js');
echo $this->Html->script('jquery.maskedinput.min.js');
echo $this->Html->script('select2.min.js');
echo $this->Html->script('moment.js');
echo $this->Html->css('jquery-ui-1.10.3.custom');
echo $this->Html->css('select2');
?>

<script>
    $(document).ready(function () {
        
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
        $("#per_ini_vendas").mask("99/99/9999");
        $("#per_ini_vendas").datepicker();
        $("#per_fim_vendas").mask("99/99/9999");
        $("#per_fim_vendas").datepicker();
        $('#cancelar').click(function () {
            window.location = "<?php echo $this->Html->url(array('action' => 'index')); ?>";
            return false;
        });

        $("#marcaTodasFiliais").change(function () {
            $(".filiais").prop('checked', $(this).prop("checked"));
        });

        $('#enviar').click(function () {

            moment.locale('pt-br');
            var per_ini_vendas = moment($('#per_ini_vendas').val(), 'DD/MM/YYYY');
            var per_fim_vendas = moment($('#per_fim_vendas').val(), 'DD/MM/YYYY');
            var diff = per_fim_vendas.diff(per_ini_vendas, 'days');

            if (diff > '366') {
                $('.mensagem').html("<div class='alert alert-danger fade in' role='alert' size='2'><button type='button' class='close' data-dismiss='alert' aria-label='Fechar'><span aria-hidden='true'>&times;</span></button><strong>Selecione um per&iacute;odo inferior a 12 meses da data final!</strong></div>");
                $(".mensagem").fadeTo(3000, 500).slideUp(500, function () {
                    $(".mensagem").alert('close');
                });
                return false;
            }

            //funcoes java script
            // Pass the checkbox name to the function
            function getCheckedBoxes(chkboxName) {
                var checkboxes = document.getElementsByName(chkboxName);
                var checkboxesChecked = [];
                // loop over them all
                for (var i = 0; i < checkboxes.length; i++) {
                    // And stick the checked ones onto an array...
                    if (checkboxes[i].checked) {
                        checkboxesChecked.push(checkboxes[i]);
                    }
                }
                // Return the array if it is non-empty, or null
                if (checkboxesChecked.length > 0) {
                    return true;
                } else {
                    return false;
                }
            }

            function ValidaData(strTexto) {
                var strData = "";
                var dia = 0;
                var mes = 0;
                var ano = 0;
                var fevereiro = 0;

                if (strTexto.trim() === "") {
                    return false;
                } else if (strTexto.length < 10) {
                    return false;
                }

                strData = strTexto; //recebe o texto completo 01/01/2016

                dia = Number(strData.substr(0, 2));
                mes = Number(strData.substr(3, 2));
                ano = Number(strData.substr(6, 4));

                if (mes === 1 || mes === 3 || mes === 5 || mes === 7 || mes === 8 || mes === 10 || mes === 12) {

                    if (dia < 01 || dia > 31) {
                        return false;
                    }
                }

                if (mes === 2) {

                    if (dia >= 30) {
                        return false;
                    }

                    fevereiro = ano % 4;

                    if (fevereiro !== 0 && dia === 29) {
                        return false;
                    }
                }

                if (mes === 2 || mes === 4 || mes === 6 || mes === 9 || mes === 11) {

                    if (dia < 1 || dia > 30) {
                        return false;
                    }
                }

                if (mes < 1 || mes > 12) {
                    return false;
                }

                if (ano < 1900) {
                    return false;
                }

                return true;
            }

            //funcoes java script

            var booFilialMarcada = getCheckedBoxes("data[Relatorios][filial][]");

            if (booFilialMarcada === false) {
                $('.mensagem').html("<div class='alert alert-danger fade in' role='alert' size='2'><button type='button' class='close' data-dismiss='alert' aria-label='Fechar'><span aria-hidden='true'>&times;</span></button><strong>Nenhuma filial selecionada!</strong></div>");
                $(".mensagem").fadeTo(3000, 500).slideUp(500, function () {
                    $(".mensagem").alert('close');
                });
                return false;
            }

            ///VALIDACAO DOS CAMPOS
            if ($('#per_ini_vendas').val().length < 10) {
                $('.mensagem').html("<div class='alert alert-danger fade in' role='alert' size='2'><button type='button' class='close' data-dismiss='alert' aria-label='Fechar'><span aria-hidden='true'>&times;</span></button><strong>Data inicial inv&aacute;lida!</strong></div>");
                $(".mensagem").fadeTo(3000, 500).slideUp(500, function () {
                    $(".mensagem").alert('close');
                });
                return false;
            }

            if ($('#per_fim_vendas').val().length < 10) {
                $('.mensagem').html("<div class='alert alert-danger fade in' role='alert' size='2'><button type='button' class='close' data-dismiss='alert' aria-label='Fechar'><span aria-hidden='true'>&times;</span></button><strong>Data final inv&aacute;lida!</strong></div>");
                $(".mensagem").fadeTo(3000, 500).slideUp(500, function () {
                    $(".mensagem").alert('close');
                });
                return false;
            }

            if (!ValidaData($('#per_ini_vendas').val())) {
                $('.mensagem').html("<div class='alert alert-danger fade in' role='alert' size='2'><button type='button' class='close' data-dismiss='alert' aria-label='Fechar'><span aria-hidden='true'>&times;</span></button><strong>Data inicial inv&aacute;lida!</strong></div>");
                $(".mensagem").fadeTo(3000, 500).slideUp(500, function () {
                    $(".mensagem").alert('close');
                });
                return false;
            }

            if (!ValidaData($('#per_fim_vendas').val())) {
                $('.mensagem').html("<div class='alert alert-danger fade in' role='alert' size='2'><button type='button' class='close' data-dismiss='alert' aria-label='Fechar'><span aria-hidden='true'>&times;</span></button><strong>Data final inv&aacute;lida!</strong></div>");
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
    .container3 { border:2px solid #ccc; width:auto; height: 303px; overflow-y: auto; } /* div filiais*/
    .container4 { border:2px solid #ccc; width:auto; height: 120px; overflow:hidden; margin-bottom: 4px; } /* div pedidos pendentes*/
    .container5 { border:2px solid #ccc; width:auto; height: 165px; overflow:hidden; } /* div periodo*/
    .container6 { border:2px solid #ccc; width:480px; height: 100px; overflow:hidden; } /* div filtro adicional*/

    .tabelaPrincipal{
        width:auto;
        min-width:480px;
        text-align:center;
    }
    #tabela{
        width:auto;
        min-width:190px;
        text-align:center;
        padding-top: 1px;   
    }
    #tabelaPedPendente{
        width: auto;
        max-width: 120px;
        text-align: center;
    }
    #tabelaPedPendente td{
        height: 1px;
        width: 60px;
        vertical-align: middle;
    }
    tabela, tr, td {
        border:none;
    } 
    .vendasVendedor{
        font-size: 10px !important;
    }

</style>

<div class="vendasVendedor">
    <h2><?php echo __('Relatório de Pedido de Compras'); ?></h2>
    <div class='mensagem'></div>

    <?php echo $this->Form->create('Relatorios'); ?>

    <table class="tabelaPrincipal">
        <tr>
            <td colspan="2"><b>Filiais</b><br>
                <div class="container3">
                    <table id="tabela">
                        <tr>
                            <td style="width: 13px;"><input type="checkbox" id="marcaTodasFiliais" value="" checked="true" /></td>
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
                <strong>Pedidos Pendentes</strong><br> 
                <div class="container4">
                    <table id="tabelaPedPendente"> 
                        <tr>
                            <td><input name="opt_ped_pendente" type="radio" value="sim" /></td>
                            <td>Sim</td>
                        </tr>
                        <tr>
                            <td><input name="opt_ped_pendente" type="radio" value="nao" /></td>
                            <td>N&atildeo</td>
                        </tr>
                        <tr>
                            <td><input checked="checked" name="opt_ped_pendente" type="radio" value="ambos" /></td>
                            <td>Ambos</td>
                        </tr>
                    </table>
                </div>
                <strong>Período</strong><br>
                <div class="container5">
                    <table id="tabelaPedPendente" >
                        <tr>
                            <td><input checked="checked" name="opt_tipo_periodo" type="radio" value="faturamento" /></td>
                            <td >Faturamento</td>
                        </tr>
                        <tr>
                            <td><input name="opt_tipo_periodo" type="radio" value="entrada" /></td>
                            <td>Entrada</td>
                        </tr>
                        <tr>
                            <td colspan="2"><input name="data[Relatorios][per_ini_vendas]" value="<?php echo date("01/m/Y"/* ,strtotime("-1 month") */) ?>" id="per_ini_vendas" type="text"><br> a <br></b><input name="data[Relatorios][per_fim_vendas]" value="<?php echo date("d/m/Y"); ?>" id="per_fim_vendas" type="text"></td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
        <tr style="background-color: #FFFFFF;">
            <td colspan="3">
                <div class="container6">
                    <table>
                        <tr>
                            <td><input checked="checked" name="opt_filtro_adicional" type="radio" value="produto" /></td>
                            <td>Produto</td>
                            <td rowspan="2">Descri&ccedil;&atilde;o Produto<br><input type="text" name="txt_desc_produto" style="width: 380px; text-transform: uppercase;" /></td>
                        </tr>
                        <tr>
                            <td><input name="opt_filtro_adicional" type="radio" value="linha" /></td>
                            <td>Linha</td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>
    <table>
        <?php echo $this->Form->button('Visualizar', array('type' => 'submit', 'id' => 'enviar', 'class' => 'botaoCancel')); ?>
        <?php echo $this->Form->button('Voltar', array('type' => 'button', 'id' => 'cancelar', 'class' => 'botaoCancel', 'action' => 'index')); ?>
    </table>
</div>
