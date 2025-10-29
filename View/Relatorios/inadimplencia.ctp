<script>
    $(document).ready(function() {
        $('#cancelar').click(function() {
            window.location = "<?php echo $this->Html->url(array('action' => 'index')); ?>";
            return false;
        });
        $("#RelatoriosInadimplenciaForm").submit(function(event) {
            /*if (!$('.regioes').is(':checked')) {
                alert("Você deve selecionar uma Região!");
                return false;
            }*/
            if (!$('.todasFiliais').is(':checked')) {
                alert("Você deve selecionar uma Filial!");
                return false;
            }
            if (!$('.todasCargos').is(':checked')) {
                alert("Você deve selecionar um Cargo!");
                return false;
            }
        });
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
            yearSuffix: '',
            onClose: function(selectedDate) {
                $("#per_fim_pesquisas").datepicker("option", "minDate", selectedDate);
                $("#per_ini_pesquisas").datepicker("option", "maxDate", selectedDate);
                $.datepicker.setDefaults($.datepicker.regional['pt']);
            }
        };
        $.datepicker.setDefaults($.datepicker.regional['pt']);
        $("#per_ini_pesquisas").mask("99/99/9999");
        $("#per_ini_pesquisas").datepicker();
        $("#per_fim_pesquisas").mask("99/99/9999");
        $("#per_fim_pesquisas").datepicker();
        $("#enviar").click(function() {
            $.unblockUI({
                message: "<b>Por Favor, aguarde.</b>",
                css: {
                    border: 'none',
                    padding: '15px',
                    backgroundColor: '#000',
                    '-webkit-border-radius': '10px',
                    '-moz-border-radius': '10px',
                    opacity: .5,
                    color: '#fff',
                }});
        })
        $(".regioes").change(function() {
            $.blockUI({
                message: "<b>Por Favor, Aguarde!</b>",
                css: {
                    border: 'none',
                    padding: '15px',
                    backgroundColor: '#000',
                    '-webkit-border-radius': '10px',
                    '-moz-border-radius': '10px',
                    opacity: .5,
                    color: '#fff',
                }});
            valor = '';
            $("input[name='data[Relatorios][regiao][]']").each(function() {
                if ($(this).is(':checked')) {
                    valor = valor + ',' + $(this).val();
                }
            });
            $.ajax({
                type: "POST",
                url: '<?php echo Router::url(array('controller' => 'Relatorios', 'action' => 'listar_filiais')); ?>',
                data: {
                    regioes: valor
                },
                dataType: "html",
                success: function(data) {
                    $("#listar_filiais").html(data);
                    $.unblockUI();
                },
            });
        });
        $(document).on('click', '#marcarTodasFiliais', function() {
            $(".todasFiliais").prop('checked', $(this).prop("checked"));
        });
        $("#marcaTodasFiliais").change(function() {
            $(".todasFiliais").prop('checked', $(this).prop("checked"));
        });
        $("#marcaTodasCargos").change(function() {
            $(".todasCargos").prop('checked', $(this).prop("checked"));
        });



    });
</script>
<?php
echo $this->Html->script('jquery-ui.js');
echo $this->Html->script('jquery-ui-timepicker-addon.js');
echo $this->Html->script('jquery.maskedinput.min.js');
echo $this->Html->script('select2.min.js');
echo $this->Html->css('jquery-ui-1.10.3.custom');
echo $this->Html->css('select2');
?>
<style>
    select{ font-size: 15px; font-family: Tahoma, sans-serif; color: #9E2424; min-width: 220px; }
    input{ font-size: 15px; font-family: Tahoma, sans-serif; color: #9E2424; width: 100px;} 
    .container { border:2px solid #ccc; width:250px; height: 145px; overflow-y: scroll; }
    .container2 { border:0px solid #ccc; width:300px; height: 190px;}
    .container3 { border:2px solid #ccc; width:630px; height: 230px; overflow-y: scroll; }
    #Menu
    {
        padding:0;
        margin:0;
        list-style-type:none;
        font-size:13px;
        color:#717171;
        width:280px;
    }

    #Menu li
    {
        border-bottom:1px solid #eeeeee;
        padding:7px 10px 7px 10px;
    }

    #Menu li:hover
    {
        color:#000;
        background-color:#eeeeee;
    }
    #tabelaTitulo{
        border-collapse:collapse;
        width:300px;
        text-align:center;
        border:none;
        border: solid 0;
    }
    #tabela{
        border-collapse:collapse;
        width:600px;
        text-align:center;
        border:none;
    }
    .coluna{
        width:50px;
        height:10px;
        font-size: 12px;
    }
    .coluna:hover{
        background-color:#eeeeee;
    }
    tabela, tr, td { border: none; } 
    .descricao{
        width: 350px;
    }
</style>
<?php echo $this->Form->create('Relatorios', array('target' => '_blank')); ?>
<div class="glbQuestionarios index">
    <h2><?php echo __('Relatório de Inadimplência Cargo/Ano'); ?></h2>
    <table>
        <tr>
            <td colspan="3" style="width: 400px">
                <table id="tabela" border="1">
                    <tr style="font-weight: bold; ">
                        <td style="width: 3px;"></td>
                        <td>Região</td>
                    </tr>
                </table>
                <div class="container3">
                    <table id="tabela" border="1">
<?php foreach ($regiao as $key => $regioes) { ?>
                            <tr>
                                <td style="width: 13px;"><input type="checkbox" class="regioes" name="data[Relatorios][regiao][]" value="<?php echo $key; ?>" type="checkbox" /></td>
                                <td><?php echo utf8_encode($regioes); ?></td>
                            </tr>
<?php } ?>
                    </table>
                </div>    
            </td>
        </tr>
        <table>
            <tr>
                <td colspan="3" style="width: 400px">
                    <table id="tabela" border="1">
                        <tr style="font-weight: bold; ">
                            <td style="width: 3px;"></td>
                            <td>Filial</td>
                        </tr>
                    </table>
                    <div id ="listar_filiais" class="container3">
                        <table id="tabela" border="1">
                            <tr>
                                <td style="width: 13px;"><input type="checkbox"  id="marcaTodasFiliais" value="" /></td>
                                <td>TODAS</td>
                            </tr>
<?php foreach ($filial as $key => $filiais) { ?>
                                <tr>
                                    <td style="width: 13px;"><input type="checkbox" class="todasFiliais" name="data[Relatorios][filial][]" value="<?php echo $key; ?>" type="checkbox" /></td>
                                    <td><?php echo utf8_encode($filiais); ?></td>
                                </tr>
<?php } ?>
                        </table>
                    </div>    
                </td>
            </tr>
            <table>
                <tr>
                    <td colspan="3" style="width: 400px">
                        <table id="tabela" border="1">
                            <tr style="font-weight: bold; ">
                                <td style="width: 3px;"></td>
                                <td>Cargo</td>
                            </tr>
                        </table>
                        <div class="container3">
                            <table id="tabela" border="1">
                                <tr>
                                    <td style="width: 13px;"><input type="checkbox" id="marcaTodasCargos" value="" /></td>
                                    <td>TODAS</td>
                                </tr>
<?php foreach ($cargo as $key => $cargos) { ?>
                                    <tr>
                                        <td style="width: 13px;"><input type="checkbox" class="todasCargos" name="data[Relatorios][cargo][]" value="<?php echo $key; ?>"/></td>
                                        <td><?php echo utf8_encode($cargos); ?></td>
                                    </tr>
<?php } ?>
                            </table>
                        </div>    
                    </td>
                </tr>
                <tr>
                    <td><b>Período de Pesquisas</b></td> 
                </tr>
                </tr>
                <tr>
                    <td><input name="data[Relatorios][per_ini_pesquisas]" value="" id="per_ini_pesquisas" type="text"> a <input name="data[Relatorios][per_fim_pesquisas]" value="" id="per_fim_pesquisas" type="text"></td>
                </tr>
                <tr>
                    <td><b>Exportar para</b></td>
                </tr>
                <tr>
                    <td>
                        <table  style="width: 200px;" border="0" cellspacing="0" cellpadding="0">
                            <tr style="background-color: #FFF">
                                <td><?php echo $this->Html->image('iconPdf.jpg', array('alt' => 'Ativo', "width" => "30px")); ?><br>(Pdf)</td>
                                <td><?php echo $this->Html->image('iconExcel.jpg', array('alt' => 'Ativo', "width" => "30px")); ?><br>(Excel)</td>
                            </tr>
                            <tr style="background-color: #FFF">
                                <td><input name="data[Relatorios][tipo_arquivo]" checked="true" value="PDF" type="radio" /></td>
                                <td><input name="data[Relatorios][tipo_arquivo]"  value="EXCEL" type="radio" /></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <table>
                <tr>
                    <td style="width: 0.1px;"><?php
                        $options = array('id' => 'enviar');
                        echo $this->Form->end($options);
                        ?></td>
                    <td style="width: 880px;"><br><?php echo $this->Form->button('Cancelar', array('type' => 'button', 'id' => 'cancelar', 'class' => 'botaoCancel', 'action' => 'index')); ?></td>
                </tr>
            </table>
            </div>
