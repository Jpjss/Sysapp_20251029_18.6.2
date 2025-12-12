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
        	javascript:navigator_Go('/SysApp/app/webroot/index.php/Usuarios/visualizar');
            return false;
        });

        $('.add').click(function(){
        	var checkedsEmail = new Array();
            $('.all').prop("checked",false);
            var items = $("#list1 input:checked:not('.all')");
            var n = items.length;
          	if (n > 0) {
              items.each(function(idx,item){
                var choice = $(item);
                choice.prop("checked",false);
                choice.parent().appendTo("#list2");
                choice.parent().appendTo("#lista2");
                checkedsEmail.push($(this).val());
              });
  	          	$.ajax({
	      		  type: "POST",
	      		  url: "/SysApp/index.php/Relatorios/controle_emails_informativo",
	      		  data: {
	      			  'emails':checkedsEmail
	      		  },
	              dataType: "html",
	      		  success: function(data){
	 					$('.mensagem').html("<div class='alert alert-success fade in' role='alert' size='2'><button type='button' class='close' data-dismiss='alert' aria-label='Fechar'><span aria-hidden='true'>&times;</span></button><strong>Usu&aacute;rio inserido com sucesso !</strong></div>");
						$(".mensagem").fadeTo(3000, 500).slideUp(500, function(){
						    $(".mensagem").alert('close');
						});
	      		  },
	      		  error: function(error){
	 					$('.mensagem').html("<div class='alert alert-danger fade in' role='alert' size='2'><button type='button' class='close' data-dismiss='alert' aria-label='Fechar'><span aria-hidden='true'>&times;</span></button><strong>Ocorreu um problema, contate o administrador !</strong></div>");
						$(".mensagem").fadeTo(3000, 500).slideUp(500, function(){
						    $(".mensagem").alert('close');
						});
						console.log(error);
			      }
	      		});
          	}else {
          		alert("Selecione pelo menos 1 item da Lista de Emails");
            }

        });

        $('.remove').click(function(){
        	var checkedsEmailsPermitidos = new Array();
            $('.all').prop("checked",false);
            var items = $("#list2 input:checked:not('.all')");
            var n = items.length;
          	if (n > 0) {
	        	items.each(function(idx,item){
	              var choice = $(item);
	              choice.prop("checked",false);
	              choice.parent().appendTo("#list1");
	              choice.parent().appendTo("#lista1");
	              checkedsEmailsPermitidos.push($(this).val());
	            });
	        	$.ajax({
		      		  type: "POST",
		      		  url: "/SysApp/index.php/Relatorios/controle_emails_informativo",
		      		  data: {
		      			  'emailsPermitidos':checkedsEmailsPermitidos
		      		  },
		              dataType: "html",
		      		  success: function(data){
		 					$('.mensagem').html("<div class='alert alert-success fade in' role='alert' size='2'><button type='button' class='close' data-dismiss='alert' aria-label='Fechar'><span aria-hidden='true'>&times;</span></button><strong>Usu&aacute;rio removido com sucesso !</strong></div>");
							$(".mensagem").fadeTo(3000, 500).slideUp(500, function(){
							    $(".mensagem").alert('close');
							});
		      		  },
		      		  error: function(error){
		 					$('.mensagem').html("<div class='alert alert-danger fade in' role='alert' size='2'><button type='button' class='close' data-dismiss='alert' aria-label='Fechar'><span aria-hidden='true'>&times;</span></button><strong>Ocorreu um problema, contate o administrador !</strong></div>");
							$(".mensagem").fadeTo(3000, 500).slideUp(500, function(){
							    $(".mensagem").alert('close');
							});
				      }
		      	});
          	}else{
          		alert("Selecione pelo menos 1 item da Lista de Emails Permitidos");
            }
        });

        /* toggle all checkboxes in group */
        $('.all').click(function(e){
        	e.stopPropagation();
        	var $this = $(this);
            if($this.is(":checked")) {
            	$this.parents('.list-group').find("[type=checkbox]").prop("checked",true);
            }
            else {
            	$this.parents('.list-group').find("[type=checkbox]").prop("checked",false);
                $this.prop("checked",false);
            }
        });

        $('[type=checkbox]').click(function(e){
          e.stopPropagation();
        });

        /* toggle checkbox when list group item is clicked */
        $('.list-group a').click(function(e){
            e.stopPropagation();
          	var $this = $(this).find("[type=checkbox]");
            if($this.is(":checked")) {
            	$this.prop("checked",false);
            }
            else {
            	$this.prop("checked",true);
            }
            if ($this.hasClass("all")) {
            	$this.trigger('click');
            }
        });
 
    });

        
</script>
    <style>
        select{ font-size: 15px; font-family: Tahoma, sans-serif; color: #9E2424; min-width: 220px; }
        .container { border:2px solid #ccc; text-align:left; }
        .container2 { border:0px solid #ccc; width:200px; height: 190px;}
        .container3 { border:2px solid #ccc; width:100%; height: 230px; overflow-y: auto; font-size: 12px !important;}
        .container4 { border:2px solid #ccc; width:20%; height: 230px; overflow-y: auto; font-size: 12px !important;}

        .painelAdmin{
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
			width: 20%;
		}
		#cancelar{
			width: 20%;
		}
		.v-center {
		  min-height:200px;
		  display: flex;
		  justify-content:center;
		  flex-flow: column wrap;
		}

    </style>
<div class="painelAdmin">
		<?php 	echo $this->Session->flash('good');
				echo $this->Session->flash('bad');
		 ?>
		<div class="mensagem"></div>
		<div class="hidden"></div>
</div>
		<div class="container">
		  <div class="row">
		        <div class="col-md-12 text-center"><h2 style="color:black;"><?php echo __(utf8_encode('Controle de Emails para Envios de Informativo Di�rio ')); ?></h2></div>
		  		<div class="col-sm-4 col-sm-offset-1">
		          <div class="list-group" id="list1">
		          <a href="#" class="list-group-item active">Lista de Emails <input title="toggle all" class="all pull-right" type="checkbox"></a>
  		          <fieldset style="height: 300px; overflow: auto;" class="lista1" id="lista1">
		          <?php foreach($usuarios as $chave){
		          			foreach($chave as $value){
		          	?>
		          <a href="#" class="list-group-item" value="<?php echo $value['nome_usuario']; ?>"><?php echo $value['nome_usuario']; ?> - <?php echo $value['login_usuario']; ?> <input value="<?php echo $value['login_usuario']; ?>" class="pull-right" type="checkbox"></a>
		          <?php 	}
		          		}
		          	?>
		          	</fieldset>
		          </div>
		        </div>
		        <div class="col-md-2 v-center">
		     		<button title="Adicionar para receber Informativo" class="btn btn-default center-block add"><i class="glyphicon glyphicon-chevron-right"></i></button>
		            <button title="Remover da lista de receber Informativo" class="btn btn-default center-block remove"><i class="glyphicon glyphicon-chevron-left"></i></button>
		        </div>
		        <div class="col-sm-4">
		    	  <div class="list-group" id="list2">
		          <a href="#" class="list-group-item active">Lista de Emails Permitidos <input title="toggle all" class="all pull-right" type="checkbox"></a>
  		          <fieldset style="height: 300px; overflow: auto;" id="lista2">
				  <?php foreach($usuariosPermitidos as $chave){
				   			foreach($chave as $value){
				   ?>
				  <a href="#" class="list-group-item"><?php echo $value['nome_usuario']; ?> - <?php echo $value['email_usuario']; ?> <input value="<?php echo $value['email_usuario']; ?>" class="pull-right" type="checkbox"></a>
				   <?php 
				   			}
				   		 }
				   ?>
				   </fieldset>
		          </div>
		        </div>
		  </div>
		</div>
