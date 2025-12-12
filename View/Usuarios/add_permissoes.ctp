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
        $("#per_ini_envio").mask("99/99/9999");
        $("#per_ini_envio").datepicker();
        $("#per_fim_envio").mask("99/99/9999");
        $("#per_fim_envio").datepicker();

        $('#cancelar').click(function() {
            window.location = "<?php echo $this->Html->url(array('action' => 'permissoes')); ?>";
            return false;
        });


        $("#marcaTodas").change(function() {
            $(".permissoes").prop('checked', $(this).prop("checked"));
        });

        $("#marcaTodos").change(function() {
            $(".usuarios").prop('checked', $(this).prop("checked"));
        });

    });
</script>

<div class="glbQuestionarios index">
    <h2><?php echo __('Controle de Permissões'); ?></h2>
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

    <?php echo $this->Form->create('Usuarios'); ?>

    <table width="397" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td colspan="2"><b>Usuários</b><br>

                <div class="container3">
                    <table id="tabela" border="1">
                        <tr>
                            <td style="width: 13px;"><input type="checkbox" id="marcaTodos" value="" /></td>
                            <td>TODOS USUÁRIOS</td>
                        </tr>
                        <?php foreach ($usuarios as $usuario) {
                            ?>
                            <tr>
                                <td style="width: 13px;"><input type="checkbox" class="usuarios" name="data[Usuarios][usuarios][]" value="<?php echo $usuario[0]['cd_usu']; ?>" /></td>
                                <td><?php echo utf8_encode($usuario[0]['nm_usu']); ?></td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>  
            </td>
        </tr>

        
    </table>
    <table width="397" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td colspan="2"><b>Permissões</b><br>

                <div class="container3">
                    <table id="tabela" border="1">
                        <tr>
                            <td style="width: 13px;"><input type="checkbox" id="marcaTodas" value="" /></td>
                            <td>TODAS PERMISSÕES</td>
                        </tr>
                            <tr>
                                <td style="width: 13px;"><input type="checkbox" class="permissoes" name="data[Usuarios][permissoes][]" value="atendimento" /></td>
                                <td>Atendimento</td>
                            </tr>
                            <tr>
                                <td style="width: 13px;"><input type="checkbox" class="permissoes" name="data[Usuarios][permissoes][]" value="filtros" /></td>
                                <td>Filtros</td>
                            </tr>
                            <tr>
                                <td style="width: 13px;"><input type="checkbox" class="permissoes" name="data[Usuarios][permissoes][]" value="parametros" /></td>
                                <td>Parâmetros</td>
                            </tr>
                            <tr>
                                <td style="width: 13px;"><input type="checkbox" class="permissoes" name="data[Usuarios][permissoes][]" value="perguntas" /></td>
                                <td>Perguntas</td>
                            </tr>
                            <tr>
                                <td style="width: 13px;"><input type="checkbox" class="permissoes" name="data[Usuarios][permissoes][]" value="permissoes" /></td>
                                <td>Permissões</td>
                            </tr>
                            <tr>
                                <td style="width: 13px;"><input type="checkbox" class="permissoes" name="data[Usuarios][permissoes][]" value="pesquisaParametro" /></td>
                                <td>Relacionar Pesquisas e Parâmetros</td>
                            </tr>
                            <tr>
                                <td style="width: 13px;"><input type="checkbox" class="permissoes" name="data[Usuarios][permissoes][]" value="pesquisas" /></td>
                                <td>Pesquisas</td>
                            </tr>
                    </table>
                </div>  
            </td>
        </tr>

        
    </table>
    <table>
        <tr>
            <td style="width: 0.1px;">
                <?php
                $options = array('id' => 'enviar');
                echo $this->Form->end($options);
                ?>
            </td>
            <td style="width: 880px;"><br><?php echo $this->Form->button('Cancelar', array('type' => 'button', 'id' => 'cancelar', 'class' => 'botaoCancel', 'action' => 'index')); ?></td>
        </tr>
    </table>
</div>
