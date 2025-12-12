<?php
//echo $this->Html->script('jquery-1.10.2.min.js');
echo $this->Html->script('jquery-ui.js');
echo $this->Html->script('jquery-ui-timepicker-addon.js');
echo $this->Html->script('jquery.maskedinput.min.js');
echo $this->Html->css('jquery-ui-1.10.3.custom');

$data = date("d/m/Y", mktime(gmdate("d"), gmdate("m"), gmdate("Y")));

$hora = date("H:i:s", mktime(gmdate("H") - 3, gmdate("i"), gmdate("s")));
?>
<script>
    jQuery(function($) {
        data = new Date();
        dia = data.getDate();
        mes = data.getMonth();
        ano = data.getFullYear();

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
        $("#GlbQuestionarioDtVigenciaIni").mask("99/99/9999 99:99:99");

        $("#GlbQuestionarioDtVigenciaFim").mask("99/99/9999 99:99:99");


        var myControl = {
            create: function(tp_inst, obj, unit, val, min, max, step) {
                $('<input class="ui-timepicker-input" value="' + val + '" style="width:50%">')
                        .appendTo(obj)
                        .spinner({
                    min: min,
                    max: max,
                    step: step,
                    change: function(e, ui) { // key events
                        // don't call if api was used and not key press
                        if (e.originalEvent !== undefined)
                            tp_inst._onTimeChange();
                        tp_inst._onSelectHandler();
                    },
                    spin: function(e, ui) { // spin events
                        tp_inst.control.value(tp_inst, obj, unit, ui.value);
                        tp_inst._onTimeChange();
                        tp_inst._onSelectHandler();
                    }
                });
                return obj;
            },
            options: function(tp_inst, obj, unit, opts, val) {
                if (typeof(opts) == 'string' && val !== undefined)
                    return obj.find('.ui-timepicker-input').spinner(opts, val);
                return obj.find('.ui-timepicker-input').spinner(opts);
            },
            value: function(tp_inst, obj, unit, val) {
                if (val !== undefined)
                    return obj.find('.ui-timepicker-input').spinner('value', val);
                return obj.find('.ui-timepicker-input').spinner('value');
            }
        };
        $('form').submit(function() {
            dataIn = $("#GlbQuestionarioDtVigenciaIni").val().split(" ");
            compara1 = parseInt(dataIn[0].substr(6, 4) + dataIn[0].substr(3, 2) + dataIn[0].substr(0, 2) + dataIn[1].substr(0, 2) + dataIn[1].substr(3, 2));
            horaIn = dataIn[1].substr(0, 2) + dataIn[1].substr(3, 2);

            dataFim = $("#GlbQuestionarioDtVigenciaFim").val().split(" ");
            compara2 = parseInt(dataFim[0].substr(6, 4) + dataFim[0].substr(3, 2) + dataFim[0].substr(0, 2) + dataFim[1].substr(0, 2) + dataFim[1].substr(3, 2));
            horaFim = dataFim[1].substr(0, 2) + dataFim[1].substr(3, 2);
            if (compara1 > compara2) {
                if (horaIn > horaFim) {
                    $("<div style='text-align: center;'>A hora final deve ser maior que a hora inicial.</div>").dialog();
                } else {
                    $("<div style='text-align: center;'>A data final deve ser maior que a data inicial.</div>").dialog();
                }

                return false;
            }

        });

        $('#GlbQuestionarioDtVigenciaFim').datetimepicker({
            controlType: myControl,
            minDate: new Date(ano, mes, dia),
            beforeShow: function(input, inst) {
                setTimeout(function() {
                    inst.dpDiv.css({
                        left: 511
                    });
                }, 0);
            }
        });


        $('#GlbQuestionarioDtVigenciaIni').datetimepicker({
            controlType: myControl,
            minDate: new Date(ano, mes, dia),
            beforeShow: function(input, inst) {
                setTimeout(function() {
                    inst.dpDiv.css({
                        left: 700
                    });
                }, 0);
            }
        });
        $("#anim").change(function() {
            $("#GlbQuestionarioDtVigenciaIni").datepicker("option", "showAnim", "bounce");
        });

        $('#GlbQuestionarioDsQuestionario').limit('150', '#charsLeft');
        $('#GlbQuestionarioObs').limit('400', '#charsLeftObs');

        $('#cancelar').click(function() {
            window.location = "<?php echo $this->Html->url(array('action' => 'index')); ?>";
            return false;

        });

    });
</script>
<div class="glbQuestionarios form">
    <?php echo $this->Form->create('GlbQuestionario'); ?>
    <fieldset>
        <legend><?php echo __('Adicionar Pesquisa'); ?></legend>
        <?php
        echo $this->Form->input('ds_questionario', array("label" => "Descrição:", "type" => "textarea", "maxlength" => "150", "rows" => 3, "div" => false));
        echo '<br><span id="charsLeft"></span> caracteres restantes.' . "<br><br>";
        ?>
        <table>
            <tr>
                <td><?php echo $this->Form->input('dt_vigencia_ini', array("label" => "Data de Inicio:", "type" => "text", "div" => false, 'value' => $this->Funcionalidades->formatarDataAp($this->data["GlbQuestionario"]["dt_vigencia_ini"]))); ?></td>
                <td><?php echo $this->Form->input('dt_vigencia_fim', array("label" => "Data do Fim:", "type" => "text", "div" => false, 'value' => $this->Funcionalidades->formatarDataAp($this->data["GlbQuestionario"]["dt_vigencia_fim"]))); ?></td>
            </tr>

        </table>
        <?php
        echo $this->Form->input('cd_questionario', array("type" => "hidden"));
        echo $this->Form->input('obs', array("label" => "Observação:", "type" => "textarea", "maxlength" => "400", "rows" => 4, "div" => false));
        echo '<br><span id="charsLeftObs"></span> caracteres restantes.' . "<br><br>";
        echo $this->Form->input('tipo_questionario', array('options' => array(1 => "Aniversariante", 2 => "Inativo", 3 => 'Prospecção', 4=> "Satisfação/Pós-Venda", 5 => 'VIP'), 'default' => '0', 'label' => "Tipo da pesquisa:", 'empty' => "Selecione"));
        echo $this->Form->input('cd_usu_cad', array("type" => "hidden", "value" => 2));
        echo $this->Form->input('dt_cad', array("type" => "hidden"));
//        echo $this->Form->input('dt_cad', array("type" => "hidden", "value" => $data . " " . $hora));
        ?>
    </fieldset>
    <table>
        <tr>
            <td style="width: 0.1px;"><?php echo $this->Form->end(__('Submit')); ?></td>
            <td style="width: 880px;"><br><?php echo $this->Form->button('Cancelar', array('type' => 'button', 'id' => 'cancelar', 'class' => 'botaoCancel', 'action' => 'index')); ?></td>
        </tr>
    </table>
</div>
