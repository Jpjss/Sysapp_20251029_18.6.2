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


        $("#per_ini_vendas").mask("99/99/9999");
        $("#per_ini_vendas").datepicker();
        $("#per_fim_vendas").mask("99/99/9999");
        $("#per_fim_vendas").datepicker();

        $('#cancelar').click(function() {
        	javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/');
            return false;
        });
        
        $("input:checkbox").prop('checked', true);

        $("#marcaTodas").change(function() {
            $("input:checkbox").prop('checked', $(this).prop("checked"));
        });

		$('#enviar').click(function(){
	        $.blockUI({
	            message: "<b>Por Favor, Aguarde!</b>",
	            css: {
	                border: 'none',
	                padding: '15px',
	                backgroundColor: '#000',
	                '-webkit-border-radius': '10px',
	                '-moz-border-radius': '10px',
	                opacity: .5,
	                color: '#fff'
	            }});
			$.ajax({
			    type: "POST",
			    url: CbunnyObj.APP_PATH + 'Usuarios/change_password',
			    data: {
				    senha_usuario : $('#senha_usuario').val(),
			    	prox_senha_usuario : $('#prox_senha_usuario').val(),
			    	prox_senha_usuario_confirm : $('#prox_senha_usuario_confirm').val()
			    	 },
			    dataType: "html",
			    success: function(data){
				    $('.painelTrocarSenha').html(data);
				    $.unblockUI();
			        //alert('Senha atualizada com sucesso !');
			    }
			});
		});

    });
</script>
<script>
/*     $(document).ready(function() {

        $('#vendedor').select2({
            placeholder: "Selecionar Vendedor",
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

    }); */
</script>
<div class="painelTrocarSenha">
    <h2><?php echo __('Troque sua Senha '); ?></h2>
    <style>
        select{ font-size: 15px; font-family: Tahoma, sans-serif; color: #9E2424; min-width: 220px; }
        input{ font-size: 15px; font-family: Tahoma, sans-serif; color: #9E2424; width: 200px; height:30px;} 
        .container { border:2px solid #ccc; text-align:left; }
        .container2 { border:0px solid #ccc; width:200px; height: 190px;}
        .container3 { border:2px solid #ccc; width:auto; height: 230px; overflow-y: auto; }

        .painelTrocarSenha{
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
		
		#enviar{
			width: 30%;
		}
		#cancelar{
			width: 30%;
		}

    </style>
    <?php echo $this->Form->create('Usuarios'); ?>
		<!-- Text input-->
		<?php 	echo $this->Session->flash('good');
				echo $this->Session->flash('bad');
		 ?>
		<div class="container">
		<div class="form-group">
		  <label class="col-md-5 control-label" for="senha_usuario"><?php echo utf8_encode('Digite sua senha atual');?></label>  
			<div class="col-md-4">
			   <input type="password" id="senha_usuario" name="senha_usuario" class="form-control input-md" >
			   <br/>
 			  <label class="col-sd-6 control-label" for="prox_senha_usuario"><?php echo utf8_encode('Digite sua nova senha');?></label> 
  			     <input type="password" id="prox_senha_usuario" name="prox_senha_usuario" class="form-control input-md" >
  			     <br/>
  			    <label class="col-sd-6 control-label" for="prox_senha_usuario_confirm"><?php echo utf8_encode('Confirme sua nova senha');?></label> 
  			    	<input type="password" id="prox_senha_usuario_confirm" name="prox_senha_usuario_confirm" class="form-control input-md" >
  			     	<br/>
       			<input id="enviar" type="button" class="btn btn-primary" value="Trocar" >
       			<input id="cancelar" type="button" class="btn btn-danger" value="Cancelar" >
       			
       		</div>
		</div>
		</div>
     <?php echo $this->Form->end(); ?>
