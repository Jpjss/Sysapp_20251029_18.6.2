<?php
echo $this->Html->script('jquery-ui.js');
echo $this->Html->script('jquery-ui-timepicker-addon.js');
echo $this->Html->script('jquery.maskedinput.min.js');
echo $this->Html->script('select2.min.js');

echo $this->Html->css('jquery-ui-1.10.3.custom');
echo $this->Html->css('select2');
?>
<script>
    $(document).ready(function() {
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


        $("#per_ini_pesquisas").mask("99/99/9999");
        $("#per_ini_pesquisas").datepicker();
        $("#per_fim_pesquisas").mask("99/99/9999");
        $("#per_fim_pesquisas").datepicker();

        $('#cancelar').click(function() {
            window.location = "<?php echo $this->Html->url(array('action' => 'index')); ?>";
            return false;
        });


        $("#marcaTodas").change(function() {
            $("input:checkbox").prop('checked', $(this).prop("checked"));
        });


    });
</script>
<script>
    $(document).ready(function() {

        $('#cliente').select2({
            placeholder: "Selecionar Cliente",
            minimumInputLength: 1,
            ajax: {
                url: CbunnyObj.APP_PATH + 'clientes/search',
                dataType: 'json',
                data: function(term, page) {
                    return {
                        q: term
                    };
                },
                results: function(data, page) {
                    return {results: data};
                }
            }
        });

    });
</script>
<div class="glbQuestionarios index">
    <h2><?php echo __('Relatório de Atendimentos'); ?></h2>
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

    <table width="397" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td colspan="2"><b>Pesquisas</b><br>

                <div class="container3">
                    <table id="tabela" border="1">
                        <tr>
                            <td style="width: 13px;"><input type="checkbox" id="marcaTodas" value="" /></td>
                            <td>MARCAR TODAS</td>
                        </tr>
                        <?php foreach ($pesquisas as $pesquisa) { ?>
                            <tr>
                                <td style="width: 13px;"><input type="checkbox" class="pesquisas" name="data[Relatorios][pesquisas][]" value="<?php echo $pesquisa["GlbQuestionario"]["cd_questionario"]; ?>" /></td>
                                <td><?php echo $pesquisa["GlbQuestionario"]["ds_questionario"]; ?></td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>  
            </td>
        </tr>
        <tr>
            <td><b>Período de Pesquisas</b></td>
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
                        <td><?php echo $this->Html->image('iconPdf.jpg', array('alt' => 'Ativo',"width" => "30px")); ?><br>(Pdf)</td>
                        <td><?php echo $this->Html->image('iconExcel.jpg', array('alt' => 'Ativo',"width" => "30px")); ?><br>(Excel)</td>
                        <td><?php echo $this->Html->image('iconGrafico.png', array('alt' => 'Ativo',"width" => "30px")); ?><br>(Gráfico)</td>
                    </tr>
                    <tr style="background-color: #FFF">
                        <td><input name="data[Relatorios][tipo_arquivo]" checked="true" value="PDF" type="radio" /></td>
                        <td><input name="data[Relatorios][tipo_arquivo]" value="EXCEL" type="radio" /></td>
                        <td><input name="data[Relatorios][tipo_arquivo]" value="GRAFICO" type="radio" /></td>
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
