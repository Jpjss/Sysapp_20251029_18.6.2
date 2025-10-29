<?php
echo $this->Html->script('jquery-ui.js');
echo $this->Html->script('jquery-ui-timepicker-addon.js');
echo $this->Html->script('jquery.maskedinput.min.js');
echo $this->Html->script('select2.min.js');
echo $this->Html->css('jquery-ui-1.10.3.custom');
echo $this->Html->css('select2');
?>

<script>
function mudar(){
	var x = document.getElementById("senha_banco").getAttribute("type");
	if(x == "password"){
		$('#senha_banco').attr('type', 'text');
		$('.link-editar').attr('title', 'Ocultar senha');
	}else{
		$('#senha_banco').attr('type', 'password');
		$('.link-editar').attr('title', 'Mostrar senha');
	}
}
    $(document).ready(function() {
        $('#cancelar').click(function() {
        	javascript:navigator_Go('/SysApp/app/webroot/index.php/Usuarios/listar_database');
            return false;
        });
        $('#senha_banco').click(function() {
            $(this).css({"type": "password"});
            console.log($(this));
            console.log('toaqui');
        });

        $("input").blur(function(){
            if($(this).val() == ""){
                    $(this).css({"border-color" : "#F00", "padding": "2px"});
                }else{
                	$(this).css({"border-color" : "#8CC3EE", "padding": "2px"});
                    }
           });

		$('#enviar').click(function(){

			if($('#nome_empresa').val() === ''){
				alert("<?php echo utf8_encode('Nome da empresa � obrigat�rio !'); ?>");
				$("#nome_empresa").focus();
				return false;
			} 
			if($('#hostname').val() === ''){
				alert("<?php echo utf8_encode('Host do banco � obrigat�rio !'); ?>");
				$("#hostname").focus();
				return false;
			} 
		     if ($("#nome_banco").val() === '') {
		         alert("<?php echo utf8_encode('Nome do database � obrigat�rio !'); ?>");
		         $("#nome_banco").focus();
		         return false;
		     }
		     if ($("#usuario_banco").val() === '') {
		         alert("<?php echo utf8_encode('Usu�rio � obrigat�rio !'); ?>");
		         $("#usuario_banco").focus();
		         return false;
		     }
		     if ($("#senha_banco").val() === '') {
		         alert("<?php echo utf8_encode('Senha � obrigat�rio !'); ?>");
		         $("#senha_banco").focus();
		         return false;
		     }
		     if ($("#porta_banco").val() === '') {
		         alert("<?php echo utf8_encode('Porta � obrigat�rio !'); ?>");
		         $("#porta_banco").focus();
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
			    url: CbunnyObj.APP_PATH + 'Usuarios/alterarInfoDatabase',
			    data: {
			    	cd_empresa : $('#cd_empresa').val(),
			    	nome_empresa : $('#nome_empresa').val(),
			    	hostname : $('#hostname').val(),
			    	nome_banco : $('#nome_banco').val(),
			    	usuario_banco : $('#usuario_banco').val(),
			    	senha_banco : $('#senha_banco').val(),
			    	porta_banco : $('#porta_banco').val()
			    	 },
			    dataType: "html",
			    success: function(result){
				    if(result.substring(0,1) == "1"){
	 					$('.mensagem').html("<div class='alert alert-success fade in' role='alert' size='2'><button type='button' class='close' data-dismiss='alert' aria-label='Fechar'><span aria-hidden='true'>&times;</span></button><strong>Banco de dados modificado com sucesso !</strong></div>");
						$(".mensagem").fadeTo(3000, 500).slideUp(500, function(){
						    $(".mensagem").alert('close');
						});
						$('#DatabaseAdicionaDatabaseForm').each (function(){
							  this.reset();
						});
				    }else{
	 					$('.mensagem').html("<div class='alert alert-danger fade in' role='alert' size='2'><button type='button' class='close' data-dismiss='alert' aria-label='Fechar'><span aria-hidden='true'>&times;</span></button><strong>Ocorreu um problema, contate o administrador !</strong></div>");
						$(".mensagem").fadeTo(3000, 500).slideUp(500, function(){
						    $(".mensagem").alert('close');
						});
					}
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
		.form-inline{
			display: inline-block;
		}
		#senha_banco{
			-moz-border-radius-bottomright:5px;
			-webkit-border-bottom-right-radius:5px;
			border-bottom-right-radius:5px;
			-moz-border-radius-bottomleft:5px;
			-webkit-border-bottom-left-radius:5px;
			border-bottom-left-radius:5px;
			-moz-border-radius-topright:5px;
			-webkit-border-top-right-radius:5px;
			border-top-right-radius:5px;
			-moz-border-radius-topleft:5px;
			-webkit-border-top-left-radius:5px;
			border-top-left-radius:5px;
			width:98%;
		}
		.container{
			-moz-border-radius-bottomright:5px;
			-webkit-border-bottom-right-radius:5px;
			border-bottom-right-radius:5px;
			-moz-border-radius-bottomleft:5px;
			-webkit-border-bottom-left-radius:5px;
			border-bottom-left-radius:5px;
			-moz-border-radius-topright:5px;
			-webkit-border-top-right-radius:5px;
			border-top-right-radius:5px;
			-moz-border-radius-topleft:5px;
			-webkit-border-top-left-radius:5px;
			border-top-left-radius:5px;
		}


    </style>
<div class="painelAdmin">
    <h2 style="color:black;"><?php echo __(utf8_encode('Alterar Informa��es do Banco de Dados ')); ?></h2>

    <?php echo $this->Form->create('Database', array('name' => "DatabaseAlterarDatabaseForm")); ?>
		<!-- Text input-->
		<?php 	echo $this->Session->flash('good');
				echo $this->Session->flash('bad');
		 ?>
		 <div class="mensagem">
		 </div>
		 <div class="hidden">
		 </div>
			<div class="container">
				<div class="form-group">
				<?php foreach($dadosCdEmpresas as $chave){
						foreach($chave as $value){
					?>
				  <label class="col-md-5 control-label" for="nome_empresa"><?php echo utf8_encode('Nome da Empresa');?></label>  
					<div class="col-md-4">
				     <input type="hidden" id="cd_empresa" name="cd_empresa" class="form-control input-md" value="<?php echo $value['cd_empresa']; ?>" >
		  			   <br/>
					   <input type="text" id="nome_empresa" name="nome_empresa" class="form-control input-md" value="<?php echo $value['nome_empresa']; ?>">
					   <br/>
					   
					    <label class="col-sd-6 control-label" for="hostname"><?php echo utf8_encode('Host do Banco');?></label> 
		  			     <input type="text" id="hostname" name="hostname" class="form-control input-md" value="<?php echo $value['hostname_banco']; ?>">
		  			    <br/>
		  			    
		 			 	<label class="col-sd-6 control-label" for="nome_banco"><?php echo utf8_encode('Nome do Database');?></label> 
		  			     <input type="text" id="nome_banco" name="nome_banco" class="form-control input-md" value="<?php echo $value['nome_banco']; ?>" >
		  			     <br/>
		  			     
		  			    <label class="col-sd-6 control-label" for="usuario_banco"><?php echo utf8_encode('Usu�rio');?></label> 
		  			    	<input type="text" id="usuario_banco" name="usuario_banco" class="form-control input-md" value="<?php echo $value['usuario_banco']; ?>">
		  			     	<br/>
		  			     	
						<label class="col-sd-6 control-label" for="senha_banco"><?php echo utf8_encode('Senha');?></label> 
						   <div class="input-group">
						      <input type="password" class="form-control input-md" id="senha_banco" name="senha_banco" value="<?php echo $value['senha_banco']; ?>">
						      <span class="input-group-btn">
						        <?php echo $this->Html->link(
									$this->Html->image('visualizar.png', array('title' => 'Mostrar Senha', 'class'=>'link-editar')),
									"javascript:mudar();",array('escape' => false)); ?>
						      </span>
						    </div>
		  			     	<br/>
		  			    	
		  			    <label class="col-sd-6 control-label" for="porta_banco"><?php echo utf8_encode('Porta');?></label> 
		  			    	<input type="text" id="porta_banco" name="porta_banco" class="form-control input-md" value="<?php echo $value['porta_banco']; ?>">
		  			     	<br/>	  	
		  		  	</div>
				</div>
			</div>
	  		<?php 
			 		}
				}
	  		?>
		<div class="container">
			<input id="enviar" type="button" class="btn btn-primary" value="Enviar" >
		    <input id="cancelar" type="button" class="btn btn-danger" value="Cancelar" >
	    </div>
</div>
     <?php echo $this->Form->end(); ?>
