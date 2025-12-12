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

            $('#dt_fi').datepicker({dateFormat: 'dd/mm/yy'});
            $('#dt_in').datepicker({dateFormat: 'dd/mm/yy'});
                        
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
    	    $('#RelatoriosOrcamentoVendaForm').validate({
    	        rules: {
    	            dt_fi : 'dateITA',
    	            dt_in : 'dateITA',
    	        },
    	        messages: {
    	        	dt_fi: '<b style="color:red">Por favor, selecione uma data v&aacute;lida!</b>',
    	        	dt_in : '<b style="color:red">Por favor, selecione uma data v&aacute;lida!</b>'
    	        }
    	    });

    	    $('#dt_fi').on('onChange', function () {
    	        $('#RelatoriosOrcamentoVendaForm').valid();
    	    });
    	    $('#dt_in').on('onChange', function () {
    	        $('#RelatoriosOrcamentoVendaForm').valid();
    	    });

    	})(jQuery);

        $('#cancelar').click(function() {
            window.location = "<?php echo $this->Html->url(array('action' => 'index')); ?>";
            return false;
        });
        
        $("input:checkbox").prop('checked', true);
        $(".vlr_tac").prop('checked', false);

        $("#marcaTodas").change(function() {
            $("input:checkbox").prop('checked', $(this).prop("checked"));
        });

    	var checkeds = new Array();
    	$("input[name='data[Relatorios][filial][]']:checked").each(function (){
    	   checkeds.push( $(this).val());
    	});
 
/*         $('#enviar').click(function(){
	        $.ajax({
		        type: "POST",
	            url: '/SysApp/index.php/Relatorios/orcamento_venda/',
	            data: {
	                dt_in: $('#dt_in').val(),
	                dt_fi: $('#dt_fi').val(),
	                filiais: checkeds
	            },
	            dataType: "json",
	            success: function(data) {
		            console.log('toaqui');
	            },
	            error: function(request,status, error, data) {
	                alert(data);
	                console.log(error);
	            }
	        });
        }); */

    });

</script>
    <style>
        select{ font-size: 15px; font-family: Tahoma, sans-serif; color: #9E2424; min-width: 220px; }
        input{ font-size: 15px; font-family: Tahoma, sans-serif; color: #9E2424; width: 85px;} 
        .container { border:2px solid #ccc; width:250px; height: 145px; overflow-y: auto; overflow-x: hidden; }
        .container2 { border:0px solid #ccc; width:200px; height: 190px;}
        .container3 { border:2px solid #ccc; width:210px; height: 230px; overflow-y: auto; overflow-x: hidden; }

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
		.OrcamentoVenda{
		font-size: 10px !important;
		}

    </style>
<div class="OrcamentoVenda">
    <h2><?php echo __('Gr&aacute;fico de Or&ccedil;amento de Venda'); ?></h2>
    <div class='mensagem'>
    </div>

    <?php echo $this->Form->create('Relatorios'); ?>

    <table class="tabelaPrincipal">
        <tr>
            <td colspan="2"><b>Filiais</b><br>
                <div class="container3">
                    <table id="tabela">
                        <tr>
                            <td style="width: 13px;"><input type="checkbox" id="marcaTodas" value="" /></td>
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
			            <td>
			            	<b>Período :</b>
			            </td>
			            <tr>
			            	<td>
			            		<input type="text" name="dt_in" id="dt_in" value="<?php echo date("01/m/Y"/*,strtotime("-1 month")*/) ?>"/><br> a <br></b><input name="dt_fi" value="<?php echo date("d/m/Y"); ?>" id="dt_fi" type="text">
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
