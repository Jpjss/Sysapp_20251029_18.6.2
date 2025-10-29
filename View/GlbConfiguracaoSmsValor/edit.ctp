<?php
echo $this->Html->script('jquery-ui.js');
echo $this->Html->script('jquery-ui-timepicker-addon.js');
echo $this->Html->script('jquery.maskedinput.min.js');
echo $this->Html->css('jquery-ui-1.10.3.custom');
$data = date("d/m/Y", mktime(gmdate("d"), gmdate("m"), gmdate("Y")));
?>

<div class="glbConfiguracaoSmsValor form">
    <?php echo $this->Form->create('GlbConfiguracaoSmsValor'); ?>

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
            $("#GlbConfiguracaoSmsValorDtVigenciaIni").mask("99/99/9999");
            $("#GlbConfiguracaoSmsValorDtVigenciaFim").mask("99/99/9999");
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
                dataIn = $("#GlbConfiguracaoSmsValorDtVigenciaIni").val().split(" ");
                compara1 = parseInt(dataIn[0].substr(6, 4) + dataIn[0].substr(3, 2) + dataIn[0].substr(0, 2) + dataIn[1].substr(0, 2) + dataIn[1].substr(3, 2));
                horaIn = dataIn[1].substr(0, 2) + dataIn[1].substr(3, 2);
                dataFim = $("#GlbConfiguracaoSmsValorDtVigenciaFim").val().split(" ");
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
            $('#GlbConfiguracaoSmsValorDtVigenciaFim').datepicker({});
            $('#GlbConfiguracaoSmsValorDtVigenciaIni').datepicker({});
            $('#GlbConfiguracaoSmsValorValorSms').maskMoney({thousands: '.', decimal: ',', allowZero: false, allowNegative: true, defaultZero: false});


            $('#cancelar').click(function() {
                window.location = "<?php echo $this->Html->url(array('action' => 'index')); ?>";
                return false;

            });

        });
    </script>
    <fieldset>
        <legend><?php echo __('Configuração Valor de SMS por Período'); ?></legend>
        <span style="font-size: 18px;">Período de Vigência</span>
        <table>
            <tr>
                <td><?php echo $this->Form->input('dt_vigencia_ini', array("label" => "De:", "type" => "text", 'value' => $data, "div" => false)); ?></td>
                <td><?php echo $this->Form->input('dt_vigencia_fim', array("label" => "Até:", "type" => "text", 'value' => $data, "div" => false)); ?></td>
            </tr>

        </table>
        <?php
        echo $this->Form->input('valor_sms', array("label" => "Valor:", "type" => "text", "div" => false));
        echo $this->Form->input('cd_usu_cad', array("type" => "hidden", "value" => 2));
        echo $this->Form->input('dt_cad', array("type" => "hidden", "value" => $data));
        echo $this->Form->input('cd_configuracao');
        ?>
    </fieldset>
    <table>
        <tr>
            <td style="width: 0.1px;"><?php echo $this->Form->end(__('Submit')); ?></td>
            <td style="width: 880px;"><br><?php echo $this->Form->button('Cancelar', array('type' => 'button', 'id' => 'cancelar', 'class' => 'botaoCancel', 'action' => 'index')); ?></td>
        </tr>
    </table>
</div>
