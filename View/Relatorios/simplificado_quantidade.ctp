<script>
    $(document).ready(function() {
        function verifica() {
            if ($('#RelatoriosCategoria').val() !== '' && $('#RelatoriosFamilia').val() !== '' && $('#RelatoriosDepartamento').val() !== '') {
                $('input:radio[name="data[Relatorios][grupo_todos]"]').removeAttr('disabled');
                $("#msgGrupo").html('');
            } else {
                $('input:radio[name="data[Relatorios][grupo_todos]"]').attr('disabled', 'disabled');
                $("#msgGrupo").html('Escolha Categoria, Família e Departamento');
            }
        }

//        $.datepicker.regional['pt'] = {
//            closeText: 'Fechar',
//            prevText: '<Anterior',
//            nextText: 'Seguinte',
//            currentText: 'Hoje',
//            monthNames: ['Janeiro', 'Fevereiro', 'Mar&ccedil;o', 'Abril', 'Maio', 'Junho',
//                'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
//            monthNamesShort: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun',
//                'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
//            dayNames: ['Domingo', 'Segunda-feira', 'Ter&ccedil;a-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'S&aacute;bado'],
//            dayNamesShort: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'S&aacute;b'],
//            dayNamesMin: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'S&aacute;b'],
//            weekHeader: 'Sem',
//            dateFormat: 'dd/mm/yy',
//            firstDay: 0,
//            isRTL: false,
//            showMonthAfterYear: false,
//            yearSuffix: ''
//        };
//        $.datepicker.setDefaults($.datepicker.regional['pt']);

//
//        $("#RelatoriosPeriodoIni").mask("99/99/9999");
//        $("#RelatoriosPeriodoIni").datepicker();
//        $("#RelatoriosPeriodoFim").mask("99/99/9999");
//        $("#RelatoriosPeriodoFim").datepicker();

        $('#cancelar').click(function() {
            window.location = "<?php echo $this->Html->url(array('action' => 'index')); ?>";
            return false;
        });
        $("select").attr('disabled', 'disabled');
        $("#RelatoriosOrdem").removeAttr('disabled');

        $('input:radio[name="data[Relatorios][categoria_todos]"]').change(function() {
            if ($(this).val() == 0) {
                $('#RelatoriosCategoria').removeAttr('disabled');
            } else {
                $('#RelatoriosCategoria').attr('disabled', 'disabled');
            }

        });

        $('input:radio[name="data[Relatorios][familia_todos]"]').change(function() {
            if ($(this).val() == 0) {
                $('#RelatoriosFamilia').removeAttr('disabled');
            } else {
                $('#RelatoriosFamilia').attr('disabled', 'disabled');
            }

        });

        $('input:radio[name="data[Relatorios][departamento_todos]"]').change(function() {
            if ($(this).val() == 0) {
                $('#RelatoriosDepartamento').removeAttr('disabled');
            } else {
                $('#RelatoriosDepartamento').attr('disabled', 'disabled');
            }

        });

        $('input:radio[name="data[Relatorios][grupo_todos]"]').change(function() {
            if ($(this).val() == 0) {
                $('#RelatoriosGrupo').removeAttr('disabled');
            } else {
                $('#RelatoriosGrupo').attr('disabled', 'disabled');
            }

        });

        $('input:radio[name="data[Relatorios][categoria_todos]"]').change(function() {
            if ($(this).val() == 0) {
                $('#RelatoriosCategoria').removeAttr('disabled');
            } else {
                $('#RelatoriosCategoria').attr('disabled', 'disabled');
            }

        });

        $('input:radio[name="data[Relatorios][fabricante_todos]"]').change(function() {
            if ($(this).val() == 0) {
                $('#RelatoriosFabricante').removeAttr('disabled');
            } else {
                $('#RelatoriosFabricante').attr('disabled', 'disabled');
            }

        });

        $('input:radio[name="data[Relatorios][marca_todos]"]').change(function() {
            if ($(this).val() == 0) {
                $('#RelatoriosMarca').removeAttr('disabled');
            } else {
                $('#RelatoriosMarca').attr('disabled', 'disabled');
            }

        });
        $('#RelatoriosDepartamento').change(function() {
            verifica();
        });

        $('#RelatoriosCategoria').change(function() {
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
            $.ajax({
                type: "POST",
                url: '<?php echo Router::url(array('controller' => 'Relatorios', 'action' => 'listar_combo')); ?>',
                data: {
                    categoria: $(this).val()
                },
                dataType: "html",
                success: function(data) {
                    $("#RelatoriosFamilia").html(data);
                    $.unblockUI();
                    verifica();
                },
            });
        });

        $('#RelatoriosFamilia').change(function() {
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
            $.ajax({
                type: "POST",
                url: '<?php echo Router::url(array('controller' => 'Relatorios', 'action' => 'listar_combo')); ?>',
                data: {
                    familia: $(this).val()
                },
                dataType: "html",
                success: function(data) {
                    $("#RelatoriosGrupo").html(data);
                    $.unblockUI();
                    verifica();
                },
            });
        });


        $("#marcaTodas").change(function() {
            $("input:checkbox").prop('checked', $(this).prop("checked"));
        });
//        $("#enviar").click(function() {
//            $.blockUI({
//                message: "<b>Por Favor, aguarde enquanto o relatório está sendo gerado!</b>",
//                css: {
//                    border: 'none',
//                    padding: '15px',
//                    backgroundColor: '#000',
//                    '-webkit-border-radius': '10px',
//                    '-moz-border-radius': '10px',
//                    opacity: .5,
//                    color: '#fff',
//                }});
//
//        })

    });
</script>
<div class="glbQuestionarios index">
    <h2><?php echo __('Relatório Simplificado de Quantidades'); ?></h2>
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

    <?php echo $this->Form->create('Relatorios',array('target' => '_blank')); ?>
    <table>
        <tr>

            <td colspan="3" style="width: 400px">
                <table id="tabela" border="1">
                    <tr style="font-weight: bold; ">
                        <td style="width: 3px;"></td>
                        <td>Filial</td>
                    </tr>
                </table>
                <div class="container3">
                    <table id="tabela" border="1">
                        <tr>
                            <td style="width: 13px;"><input type="checkbox" id="marcaTodas" value="" /></td>
                            <td>TODAS</td>
                        </tr>
                        <?php foreach ($filiais as $filial) { ?>
                            <tr>
                                <td style="width: 13px;"><input type="checkbox" class="filiais" name="data[Relatorios][filial][]" value="<?php echo $filial["PrcFilial"]["cd_filial"]; ?>" /></td>
                                <td><?php echo utf8_encode($filial["PrcFilial"]["nm_apresentacao_carne"]); ?></td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>    
            </td>
        </tr>
        <tr>
            <td>
                <table id="tabelaTitulo" border="1">
                    <tr style="font-weight: bold; ">
                        <td style="width: 3px;"></td>
                        <td>Categoria</td>
                    </tr>
                </table>
                <div class="container">
                    <table id="tabela" style="width: 45px;" border="1">
                        <tr>
                            <td style="width: 3px;"><input type="radio" checked="checked" name="data[Relatorios][categoria_todos]" value="1" />Todos</td>
                        </tr>
                        <tr>
                            <td style="width: 3px;"><input type="radio" name="data[Relatorios][categoria_todos]" value="0" />Selecionar</td>
                        </tr>
                        <tr>
                            <td style="width: 3px; font-size: 12px;"><?php echo $this->Form->select('categoria', array($categoria)); ?></td>
                        </tr>
                    </table>
                </div>    
            </td>
            <td>
                <table id="tabelaTitulo" border="1">
                    <tr style="font-weight: bold; ">
                        <td style="width: 3px;"></td>
                        <td>Família</td>
                    </tr>
                </table>
                <div class="container">
                    <table id="tabela" style="width: 45px;" border="1">

                        <tr>
                            <td style="width: 3px;"><input type="radio" checked="checked" name="data[Relatorios][familia_todos]" value="1" />Todos</td>
                        </tr>
                        <tr>
                            <td style="width: 3px;"><input type="radio" name="data[Relatorios][familia_todos]" value="0" />Selecionar</td>
                        </tr>
                        <tr>
                            <td style="width: 3px; font-size: 12px;"><?php echo $this->Form->select('familia', $familia); ?></td>
                        </tr>
                    </table>
                </div>  
            </td>
            <td>
                <table id="tabelaTitulo" border="1">
                    <tr style="font-weight: bold; ">
                        <td style="width: 3px;"></td>
                        <td>Departamento</td>
                    </tr>
                </table>
                <div class="container">
                    <table id="tabela" style="width: 45px;" border="1">

                        <tr>
                            <td style="width: 3px;"><input type="radio" checked="checked" name="data[Relatorios][departamento_todos]" value="1" />Todos</td>
                        </tr>
                        <tr>
                            <td style="width: 3px;"><input type="radio" name="data[Relatorios][departamento_todos]" value="0" />Selecionar</td>
                        </tr>
                        <tr>
                            <td style="width: 3px; font-size: 12px;"><?php echo $this->Form->select('departamento', $departamento); ?></td>
                        </tr>
                    </table>
                </div>  
            </td>

        </tr>
        <tr>
            <td>
                <table id="tabelaTitulo" border="1">
                    <tr style="font-weight: bold; ">
                        <td style="width: 3px;"></td>
                        <td>Grupo</td>
                    </tr>
                </table>
                <div class="container">
                    <table id="tabela" style="width: 45px;" border="1">

                        <tr>
                            <td style="width: 3px;"><span id="msgGrupo" style="font-size: 9px">Escolha Categoria, Família e Departamento</span><br><input type="radio" disabled="disabled" checked="checked" name="data[Relatorios][grupo_todos]" value="1" />Todos</td>
                        </tr>
                        <tr>
                            <td style="width: 3px;"><input type="radio" disabled="disabled" name="data[Relatorios][grupo_todos]" value="0" />Selecionar</td>
                        </tr>
                        <tr>
                            <td style="width: 3px; font-size: 12px;"><?php echo $this->Form->select('grupo', $grupo); ?></td>
                        </tr>
                    </table>
                </div>  
            </td>
            <td>
                <table id="tabelaTitulo" border="1">
                    <tr style="font-weight: bold; ">
                        <td style="width: 3px;"></td>
                        <td>Fabricante</td>
                    </tr>
                </table>
                <div class="container">
                    <table id="tabela" style="width: 45px;" border="1">
                        <tr>
                            <td style="width: 3px;"><input type="radio" checked="checked" name="data[Relatorios][fabricante_todos]" value="1" />Todos</td>
                        </tr>
                        <tr>
                            <td style="width: 3px;"><input type="radio" name="data[Relatorios][fabricante_todos]" value="0" />Selecionar</td>
                        </tr>
                        <tr>
                            <td style="width: 3px; font-size: 12px;"><?php echo $this->Form->select('fabricante', $fabricante); ?></td>
                        </tr>
                    </table>
                </div>  
            </td>
            <td>
                <table id="tabelaTitulo" border="1">
                    <tr style="font-weight: bold; ">
                        <td style="width: 3px;"></td>
                        <td>Marca</td>
                    </tr>
                </table>
                <div class="container">
                    <table id="tabela" style="width: 45px;" border="1">

                        <tr>
                            <td style="width: 3px;"><input type="radio" checked="checked" name="data[Relatorios][marca_todos]" value="1" />Todos</td>
                        </tr>
                        <tr>
                            <td style="width: 3px;"><input type="radio" name="data[Relatorios][marca_todos]" value="0" />Selecionar</td>
                        </tr>
                        <tr>
                            <td style="width: 3px; font-size: 12px;"><?php echo $this->Form->select('marca', $marca); ?></td>
                        </tr>
                    </table>
                </div>  
            </td>

        </tr>
    </table>
    <table id="tabela" border="1">
        <tr style="font-weight: bold; ">
            <td colspan="3" style="width: 3px;">Descrição do Produto:</td>
        </tr>
        <tr style="font-weight: bold; ">
            <td style="width: 20px;"><?php echo $this->Form->input('descricao', array('type' => 'text', 'label' => false, 'class' => 'descricao')); ?></td>
        </tr>
    </table>
    <table id="tabela" border="1">
        <tr style="font-weight: bold; ">
            <td colspan="3" style="width: 3px;">Período de movimentação:</td>
        </tr>
        <tr style="font-weight: bold; ">
            <td style="width: 20px;"><?php echo $this->Form->input('periodoIni', array('type' => 'text', 'label' => false)); ?></td>
            <td><br>a</td>
            <td style="width: 20px;"><?php echo $this->Form->input('periodoFim', array('type' => 'text', 'label' => false)); ?></td>
            <td style="width: 520px;"></td>
        </tr>
    </table>
    <table id="tabela" border="1">
        <tr style="font-weight: bold; ">
            <td style="width: 3px;">Ordenar por:</td>
        </tr>
        <tr style="font-weight: bold; ">
            <td style="width: 20px;"><?php echo $this->Form->input('ordem', array('type' => 'select', 'label' => false, 'options' => array('cd_ref_fabrica' => 'Referencia de Fábrica', 'ds_prod_y' => 'Nome Produto'))); ?></td>
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
