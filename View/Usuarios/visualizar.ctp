<?php
echo $this->Html->script('jquery-ui.js');
echo $this->Html->script('jquery-ui-timepicker-addon.js');
echo $this->Html->script('jquery.maskedinput.min.js');
echo $this->Html->script('select2.min.js');
echo $this->Html->script('bootbox.min.js');
echo $this->Html->css('jquery-ui-1.10.3.custom');
echo $this->Html->css('select2');

?>

<script>


function excluir(id){
	var boxContentString = "<h2 style='color:#AF9D33;' size=2>Tem certeza que deseja excluir este usu&aacute;rio?</h1>";
  	bootbox.dialog({
		  message: boxContentString,
		  onEscape: function() {},
		  show: true,
		  backdrop: true,
		  closeButton: true,
		  animate: true,
		  className: "my-modal",
		  buttons: {
		    success: {   
		      label: "Sim!",
		      className: "btn-success",
		      callback: function() {
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
						    url: CbunnyObj.APP_PATH + 'Usuarios/excluir',
						    data: {
						    	cd_usuario : id
						    	 },
						    dataType: "html",
						    success: function(result){
							    if(result.substring(0,1) == "1"){
				 				 	$('.mensagem').html("<div class='alert alert-success alert-dismissible' role='alert' size='2'><button type='button' class='close' data-dismiss='alert' aria-label='Fechar'><span aria-hidden='true'>&times;</span></button><strong>Usu&aacute;rio exclu&iacute;do com sucesso !</strong></div>");
									$(".mensagem").fadeTo(3000, 500).slideUp(500, function(){
									    $(".mensagem").alert('close');
									});
									$.unblockUI();
									location.reload(); 
							    }else{
				 				 	$('.mensagem').html("<div class='alert alert-danger alert-dismissible' role='alert' size='2'><button type='button' class='close' data-dismiss='alert' aria-label='Fechar'><span aria-hidden='true'>&times;</span></button><strong>Usu&aacute;rio n&atilde;o exclu&iacute;do, ocorreu um problema !</strong></div>");
									$(".mensagem").fadeTo(3000, 500).slideUp(500, function(){
									    $(".mensagem").alert('close');
									});
									$.unblockUI();
								}
						    }
						});
		    		  }
				    },
					    cancel: {
			    		  label: 'N&atilde;o!',
					      className: "btn-danger",
					      callback: function() {
							
			    		  }
					    }
		  }
		});
}

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
        
        $("#marcaTodasEmpresas").change(function() {
            $(".empresas").prop('checked', $(this).prop("checked"));
        });
        $("#marcaTodosRelatorios").change(function() {
            $(".relatorios").prop('checked', $(this).prop("checked"));
        });


        $("input").blur(function(){
            if($(this).val() == ""){
                    $(this).css({"border-color" : "#F00", "padding": "2px"});
                }else{
                	$(this).css({"border-color" : "#8CC3EE", "padding": "2px"});
                    }
           });

		$('#enviar').click(function(){

			if($('#login_usuario').val() == $("#verificarEmail").val()){
				alert("Email j&aacute; em uso, por favor escolha outro !");
				$("#login_usuario").focus();
				return false;
			} 
			if($('#senha_usuario').val() != $("#prox_senha_usuario_confirm").val()){
				alert("A senha e confirmacao de senha precisam ser iguais !");
				$("#senha_usuario").focus();
				return false;
			} 
		     if ($("#nome_usuario").val() === '') {
		         alert("Por favor, preencha com o primeiro nome do usuario !");
		         $("#nome_usuario").focus();
		         return false;
		     }
		     if ($("#login_usuario").val() === '') {
		         alert("Por favor, preencha com o login(e-mail) do usuario !");
		         $("#login_usuario").focus();
		         return false;
		     }
		     if ($("#senha_usuario").val() === '') {
		         alert("Por favor, preencha com senha do usuario !");
		         $("#senha_usuario").focus();
		         return false;
		     }
		     if ($("#prox_senha_usuario_confirm").val() === '') {
		         alert("Por favor, preencha confirme a senha do usuario !");
		         $("#prox_senha_usuario_confirm").focus();
		         return false;
		     }

			var checkedsEmpresa = new Array();
			$("input[name='data[Relatorios][empresa][]']:checked").each(function (){
				checkedsEmpresa.push( $(this).val());
			});
			if(checkedsEmpresa == ""){
		         alert("Por favor, marque ao menos uma empresa !");
		         $(".container3").focus();
		         $(".container3").css({"border-color" : "#8CC3EE", "padding": "2px"});
				return false;
			}
			
			var checkedsRelatorio = new Array();
			$("input[name='data[Relatorios][]']:checked").each(function (){
				checkedsRelatorio.push( $(this).val());
			});
			if(checkedsRelatorio == ""){
		         alert("Por favor, marque ao menos um Relatorio !");
		         $(".container4").focus();
		         $(".container4").css({"border-color" : "#8CC3EE", "padding": "2px"});
				return false;
			}

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
			    url: CbunnyObj.APP_PATH + 'Usuarios/novo_usuario',
			    data: {
			    	cd_usuario : $('#cd_usuario').val(),
			    	nome_usuario : $('#nome_usuario').val(),
			    	login_usuario : $('#login_usuario').val(),
			    	senha_usuario : $('#senha_usuario').val(),
			    	prox_senha_usuario_confirm : $('#prox_senha_usuario_confirm').val(),
			    	nome_empresa : $('#nome_empresa').val(),
			    	cd_empresa : checkedsEmpresa,
			    	cd_interface : checkedsRelatorio
			    	 },
			    dataType: "html",
			    success: function(data){
				    $('.painelAdmin').html(data);
				    $("html, body").animate({ scrollTop: 0 }, "slow");
				    $.unblockUI();
			    }
			});
		});

    });
</script>
    <style>
        select{ font-size: 15px; font-family: Tahoma, sans-serif; color: #9E2424; min-width: 220px; }
        input{ font-size: 15px; font-family: Tahoma, sans-serif; color: #9E2424; width: 200px; height:30px;} 
        .container { border:2px solid #ccc; text-align:left; }
        .container2 { border:0px solid #ccc; width:200px; height: 190px;}
        .container3 { border:2px solid #ccc; width:70%; height: 230px; overflow-y: auto; font-size: 12px !important;}
        .container4 { border:2px solid #ccc; width:70%; height: 230px; overflow-y: auto; font-size: 12px !important;}

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


    </style>
<div class="painelAdmin">
    <h2 style="color:black"><?php echo __(utf8_encode('Listando Usu�rios Cadastrados ')); ?></h2>
		<!-- Text input-->
		<?php 	echo $this->Session->flash('good');
				echo $this->Session->flash('bad');
		 ?>
		 <div class="mensagem">
		 </div>
		<div class="container">
		  <h2><?php utf8_encode('Listando Usu�rios Cadastrados ')?></h2>
			  <table class="table-bordered table-hover">
			    <thead>
			      <tr style="background-color:#E4F4FD; height:35px;">
			        <th>NOME</th>
			        <th>LOGIN</th>
			        <th><?php echo utf8_encode('A��ES')?></th>
			      </tr>
			    </thead>
			    <tbody>
			    <?php foreach($dados as $chave){
						foreach($chave as $value){
			    	?>
			      <tr>
			        <td><?php echo $value['nome_usuario']?></td>
			        <td><?php echo ($value['login_usuario'])?></td>
			        <td><?php echo $this->Html->link(
									$this->Html->image('editar.png', array('alt' => 'editar', 'class'=>'link-editar')),
									array('controller' => 'Usuarios', 'action' => 'alterar',$value['cd_usuario']),array('escape' => false)); 
					        echo "  "; 
					        echo $this->Html->link(
					        		$this->Html->image('excluir.png', array('alt' => 'editar', 'class'=>'link-editar', 'onClick' => "excluir(".$value['cd_usuario'].");")),"#",array('escape' => false));
			        ?>
			        </td>
			      </tr>
			      <?php 
						}
				      }
			      ?>
			    </tbody>
			  </table>
		<?php 
		    if($totalinsc > 0){  
		?>
        <div class="paginacao">
            <table border="0" width="100%">
                <tr>
                    <td class="total">Total: <?php echo $totalinsc; ?></td>
                    <td class="paginas">
                        <?=$this->Paginator->prev('<< Anterior', array(), NULL, array('class' => 'disabled'));?>
                        | <?=$this->Paginator->numbers();?>
                        | <?=$this->Paginator->next('Próximo >>', array(), NULL, array('class' => 'disabled'));?>
                    </td>
                </tr>
            </table>
        </div>
		<?php } ?>
		</div>
</div>
