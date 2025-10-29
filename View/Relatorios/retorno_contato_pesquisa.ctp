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

        $("#RelatoriosRetornoContatoPesquisaForm").submit(function(event) {
            if (!$('.pesquisas').is(':checked')) {
                alert("Você deve selecionar uma Pesquisa!");
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
            yearSuffix: ''
        };
        $.datepicker.setDefaults($.datepicker.regional['pt']);

        function somarData(txtData, DiasAdd) {
            var d = new Date();
            d.setTime(Date.parse(txtData.split("/").reverse().join("/")) + (86400000 * (DiasAdd)));
            var DataFinal;
            DataFinal = (d.getDate() < 10) ? "0" + d.getDate().toString() : d.getDate().toString();
            DataFinal += "/";
            DataFinal += ((d.getMonth() + 1) < 10) ? "0" + (d.getMonth() + 1).toString() : (d.getMonth() + 1).toString();
            return DataFinal + "/" + d.getFullYear().toString();
        }

        function diferencaDatas(data1, data2) {
            DAY = 1000 * 60 * 60 * 24;

            var nova1 = data1.toString().split('/');
            Nova1 = nova1[1] + "/" + nova1[0] + "/" + nova1[2];

            var nova2 = data2.toString().split('/');
            Nova2 = nova2[1] + "/" + nova2[0] + "/" + nova2[2];

            d1 = new Date(Nova1);
            d2 = new Date(Nova2);

            days_passed = Math.round((d2.getTime() - d1.getTime()) / DAY);

            return days_passed;
        }
        ;

        $("#per_ini_envio").mask("99/99/9999");
        $("#per_ini_envio").datepicker();
        $("#per_fim_envio").mask("99/99/9999");
        $("#per_fim_envio").datepicker();

        $("#per_ini_retorno").mask("99/99/9999");
        $("#per_ini_retorno").datepicker();
        $("#per_fim_retorno").mask("99/99/9999");
        $("#per_fim_retorno").datepicker();

        $('#cancelar').click(function() {
            window.location = "<?php echo $this->Html->url(array('action' => 'index')); ?>";
            return false;
        });


        $("#marcaTodasPesquisas").change(function() {
            $(".pesquisas").prop('checked', $(this).prop("checked"));
        });
        $('form').submit(function() {
            var diferencaEnvio = diferencaDatas($("#per_ini_envio").val(), $("#per_fim_envio").val());
            var diferencaRetorno = diferencaDatas($("#per_ini_retorno").val(), $("#per_fim_retorno").val());
            if ((diferencaEnvio < 0)/*|| (diferencaEnvio > 31)*/) {
                $("<div style='text-align: left;'>- A <b>data final</b> deve ser maior do que a <b>data inicial</b>.</div>").dialog();
                return false;
            }
            if ((diferencaRetorno < 0)/*|| (diferencaRetorno > 31)*/) {
                $("<div tittle='teste' style='text-align: left;'>- A <b>data final</b> deve ser maior do que a <b>data inicial</b>.</div>").dialog();
                return false;
            }

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
    <h2><?php echo __('Relatório Retorno de Contato/Pesquisa'); ?></h2>
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
                            <td style="width: 13px;"><input type="checkbox" id="marcaTodasPesquisas" value="" /></td>
                            <td>TODAS AS PESQUISAS</td>
                        </tr>
                        <?php foreach ($pesquisa as $key => $pesquisas) { ?>
                            <tr>
                                <td style="width: 13px;"><input type="checkbox" class="pesquisas" name="data[Relatorios][pesquisa][]" value="<?php echo $key; ?>" /></td>
                                <td><?php echo ($pesquisas); ?></td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>  
            </td>
        </tr>
        <tr>
            <td colspan="2"><b>Cliente</b></td>
        </tr>
        <tr>
            <td colspan="2">                
                <table width="300" border="0" cellspacing="0" cellpadding="0">
                    <tr style="background-color: #FFF">
                        <td width="22" height="19">                
                            <table border="0" style="width: 200px;" cellspacing="0" cellpadding="0">
                                <tr style="background-color: #FFF">
                                    <td><input name="data[Relatorios][clientes]" checked="true" value="T" type="radio" /></td>
                                    <td >Todos</td>
                                    <td><input name="data[Relatorios][clientes]" value="E" type="radio" /></td>
                                    <td>Específico</td>
                                </tr>
                            </table>
                        </td>
                        <td></td>
                    </tr>
                    <tr style="background-color: #FFF">
                        <td height="19"><input type="text" name="cliente" style="width: 400px;" id="cliente" ></td>
                        <td></td>
                    </tr>
                </table>    
            </td>
        </tr>
        <tr>
            <td><b>Período de Atendimento</b></td>
            <td><b>Período de Retorno</b></td>
        </tr>
        <tr>
            <td><input name="data[Relatorios][per_ini_envio]" id="per_ini_envio"  required="" type="text"> a <input name="data[Relatorios][per_fim_envio]" id="per_fim_envio" required="" type="text"></td>
            <td><input name="data[Relatorios][per_ini_retorno]" id="per_ini_retorno" required="" type="text"> a <input name="data[Relatorios][per_fim_retorno]" id="per_fim_retorno" required="" type="text"></td>
        </tr>
        <tr>
            <td><b>Exportar para</b></td>
            <td><b></b></td>
        </tr>
        <td>
            <table  style="width: 200px;" border="0" cellspacing="0" cellpadding="0">
                <tr style="background-color: #FFF">
                    <td><?php echo $this->Html->image('iconPdf.jpg', array('alt' => 'Ativo', "width" => "30px")); ?><br>(Pdf)</td>
                    <td><?php echo $this->Html->image('iconExcel.jpg', array('alt' => 'Ativo', "width" => "30px")); ?><br>(Excel)</td>
                </tr>
                <tr style="background-color: #FFF">
                    <td><input name="data[Relatorios][tipo_arquivo]" checked="true" value="PDF" type="radio" /></td>
                    <td><input name="data[Relatorios][tipo_arquivo]" value="EXCEL" type="radio" /></td>
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
