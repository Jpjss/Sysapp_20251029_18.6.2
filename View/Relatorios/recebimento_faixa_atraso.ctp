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


        $("#per_ini_baixa").mask("99/99/9999");
        $("#per_ini_baixa").datepicker();
        $("#per_fim_baixa").mask("99/99/9999");
        $("#per_fim_baixa").datepicker();

        $('#cancelar').click(function() {
            window.location = "<?php echo $this->Html->url(array('action' => 'index')); ?>";
            return false;
        });

        $("#marcaTodosDoc").change(function() {
        	$('.docs').each(
        	         function(){
        	           if ($("#marcaTodosDoc").prop("checked")){ 
            	            $(this).prop("checked", true);
        	           }else{
       	           			$(this).prop("checked", false);
        	           }               
        	         }
        	    );
        });

        $("#marcaTodas").change(function() {
        	$('.filiais').each(
        	         function(){
        	           if ($("#marcaTodas").prop("checked")){ 
            	            $(this).prop("checked", true);
        	           }else{
       	           			$(this).prop("checked", false);
        	           }               
        	         }
        	    );
        });

        $("#marcaTodasFaixas").change(function() {
        	$('.faixas').each(
        	         function(){
        	           if ($("#marcaTodasFaixas").prop("checked")){ 
            	            $(this).prop("checked", true);
        	           }else{
       	           			$(this).prop("checked", false);
        	           }               
        	         }
        	    );
        });

    });
</script>
<script>
     $(document).ready(function() {
         
         $("input:checkbox").prop('checked', true);

        $('#cliente').select2({
            minimumInputLength: 1,
            allowClear: true,
            placeholder: "Selecione o cliente", 
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
   	 document.getElementById('cliente').value = "1";
        
    });
     
     function limpa_cliente(){
         clienteSelecionado = document.getElementById('cliente').value;
    	 document.getElementById('cliente').value = "1";
         
         if(clienteSelecionado != ""){
        	 $('.select2-choice').addClass('select2-default');
        	 $('span').text('Selecione o cliente');
        	 document.getElementById('cliente').value = "1";
         } 
     }
     
</script>
    <style>
/*         .container  { border:2px solid #ccc; width:250px; height: 145px; overflow-y: scroll; overflow-x: hidden;}
        .container2 { border:0px solid #ccc; width:300px; height: 190px;}
        .container3 { border:2px solid #ccc; width:300px; height: 230px; overflow-y: auto; overflow-x: hidden;} */

        
        
        select{ font-size: 15px; font-family: Tahoma, sans-serif; color: #9E2424; min-width: 220px; }
        input{ font-size: 15px; font-family: Tahoma, sans-serif; color: #9E2424; width: 110px; } 
        .container { border:2px solid #ccc; width:250px; height: 145px; overflow-y: auto; overflow-x: hidden; }
        .container2 { border:0px solid #ccc; width:200px; height: 190px;}
        .container3 { border:2px solid #ccc; width:250px; height: 230px; overflow-y: auto; overflow-x:hidden; }
        .container4 { border:2px solid #ccc; width:280px; height: 230px; overflow-y: auto; overflow-x: hidden;}
        .container5 { border:2px solid #ccc; width:300px; height: 230px; overflow-y: auto; overflow-x: hidden;}

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
		.recebimentoFaixaAtraso{
		font-size: 10px !important;
		}
        
        
        
        
  
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
            width:300px;
            text-align:center;
            border:none;
        }
        #tabelaFilial{
            border-collapse:collapse;
            width:650px;
            height: auto;
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
        tabela, tr, td {
         border: none;
        } 
        .descricao{
            width: 350px;
        }
        #docAtraso{
        	width:30%;
        }

    </style>
<div class="recebimentoFaixaAtraso">
    <h2><?php echo __('Relatório de Recebimento por Faixa de Atraso'); ?></h2>

    <?php echo $this->Form->create('Relatorios', array('url' => array('controller' => 'relatorios', 'action' => 'recebimento_faixa_atraso'))); ?>

    <table width="397" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td colspan="2"><b>Filiais</b><br>
                <div class="container4">
                    <table id="tabelaFilial" border="1">
                        <tr>
                            <td style="width: 13px;"><input type="checkbox" id="marcaTodas" value="" /></td>
                            <td>TODAS FILIAIS</td>
                        </tr>
                        <?php foreach ($filiais as $filial) { ?>
                            <tr>
                                <td style="width: 13px;"><input type="checkbox" class="filiais" name="data[Relatorios][filial][]" value="<?php echo $filial["PrcFilial"]["cd_filial"]; ?>" /></td>
                                <td><?php echo utf8_encode($filial["PrcFilial"]["nm_fant"]); ?></td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>  
            </td>
        </tr>
    </table>
    <table id="docAtraso">
	        <tr>
	           <td colspan="2"><b>Documentos</b><br>
	                <div class="container3">
	                    <table id="tabela" border="1">
	                        <tr>
	                            <td style="width: 13px;"><input type="checkbox" id="marcaTodosDoc" value="" /></td>
	                            <td>Todos os Documentos</td>
	                        </tr>
	                        <?php foreach ($tpPagamentos as $tpPagamento) { ?>
	                            <tr>
	                                <td><input type="checkbox" class="docs" name="data[Relatorios][pgto][]" value="<?php echo $tpPagamento["TipoPagamento"]["cd_doc"]; ?>" /></td>
	                                <td><?php echo utf8_encode($tpPagamento["TipoPagamento"]["ds_doc"]); ?></td>
	                            </tr>
	                        <?php } ?>
	                    </table>
	                </div>  
	            </td>
	            <td><b>Faixas de Atraso</b><br>
	            	<div class="container3">
	                    <table id="tabela" border="1">
	                        <tr>
	                            <td style="width: 13px;"><input type="checkbox" id="marcaTodasFaixas" value="" /></td>
	                            <td>Todas as Faixas de Atraso</td>
	                        </tr>
	                        <?php foreach ($faixaAtrasos as $faixaAtraso) { ?>
	                            <tr>
	                                <td style="width: 13px;"><input type="checkbox" class="faixas" name="data[Relatorios][faixaAtraso][]" value="<?php echo $faixaAtraso["FaixaAtraso"]["atraso_inicial"]; ?>" /></td>
	                                <td><?php echo utf8_encode($faixaAtraso["FaixaAtraso"]["ds_periodo"]); ?></td>
	                            </tr>
	                        <?php } ?>
	                    </table>
	                </div>	                	 
	            </td>
	         </tr>
	    </table>  
	    <table id="tabelaPeriodo">
        <tr>
            <td><b>Período de Baixa</b></td>
        </tr>
        <tr>
            <td><input name="data[Relatorios][per_ini_baixa]" value="<?php echo date("01/m/Y"/*,strtotime("-1 month")*/) ?>" id="per_ini_baixa" type="text" placeholder="__/__/___"> a <input name="data[Relatorios][per_fim_baixa]" value="<?php echo date("d/m/Y"); ?>" id="per_fim_baixa" type="text"  placeholder="__/__/___"></td>
        </tr>
        <tr>
        	<td><b>Cliente</b></td>
        </tr>
        <tr>
           <td colspan="2">                
              <table width="300" border="0" cellspacing="0" cellpadding="0">
                 <tr style="background-color: #FFF">
                    <td width="22" height="19">  
			        	<table  style="width: 200px;" border="0" cellspacing="0" cellpadding="0">
			                <tr style="background-color: #FFF">
			        			<td><input name="data[Relatorios][cliente]" type="radio" onFocus="limpa_cliente()" value="1" checked="true" /></td>
			        			<td>Todos</td>
			                </tr>
			                <tr style="background-color: #FFF">
			                	<td><input name="data[Relatorios][cliente]" type="radio" id="clienteEspecifico"/></td>
			        			<td>Específico <input type="text" name="data[Relatorios][cliente]" id="cliente" style="width:380px;" onClick="document.getElementById('clienteEspecifico').checked=true;"></td>
			                </tr>
			                <tr style="background-color: #FFF">
			                	<td>
			                		<input type="checkbox" name="data[Relatorios][pgtoAntecipado]" value="1">
			                	</td>
			                	<td><label for="data[Relatorios][pgtoAntecipado]" style="width:380px;">Listar Clientes com Pagamento Antecipado</label></td>
			                </tr>
	                    </table>
                    </td>
                        <td></td>
                    </tr>
            	</table>
         </table>
            </td>
        </tr>
    </table>
    <table>
        <tr>
            <td><?php
                $options = array('id' => 'enviar');
                echo $this->Form->end($options);
                ?></td>
            <td><br><?php echo $this->Form->button('Cancelar', array('type' => 'button', 'id' => 'cancelar', 'class' => 'botaoCancel', 'action' => 'index')); ?></td>
        </tr>
    </table>
</div>
