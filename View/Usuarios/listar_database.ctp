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
function confirmDelete(){

	var boxContentString = "<h2 style='color:#AF9D33;' size=2>Tem certeza que deseja excluir este Banco de dados ?</h1>";
	var id = $('#cd_empresa').val();
	
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
						    url: CbunnyObj.APP_PATH + 'Usuarios/excluirDatabaseConfirmado',
						    data: {
						    	cd_empresa : id
						    	 },
						    dataType: "html",
						    success: function(result){
							    if(result.substring(0,1) == "1"){
				 				 	$('.mensagem').html("<div class='alert alert-success alert-dismissible' role='alert' size='2'><button type='button' class='close' data-dismiss='alert' aria-label='Fechar'><span aria-hidden='true'>&times;</span></button><strong>Database exclu&iacute;do com sucesso !</strong></div>");
				 				 	$("#dialog").dialog("close");
									$(".mensagem").fadeTo(3000, 500).slideUp(500, function(){
									  location.reload(); 
									});
									$.unblockUI();
									//location.reload(); 
							    }else{
				 				 	$('.mensagem').html("<div class='alert alert-danger alert-dismissible' role='alert' size='2'><button type='button' class='close' data-dismiss='alert' aria-label='Fechar'><span aria-hidden='true'>&times;</span></button><strong>Ocorreu um problema, por favor contate o administrador !</strong></div>");
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

function excluir(id){
	var boxContentString = "<h2 style='color:#AF9D33;' size=2>Tem certeza que deseja excluir este Banco de dados ?</h1>";
	
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
			    		    url: CbunnyObj.APP_PATH + 'Usuarios/excluirDatabase',
			    		    data: {
			    		    	cd_empresa : id
			    		    	 },
			    		    dataType: "html",
			    		    success: function(data){
			    	 		    if(data.substring(0,1) == "0"){
			    	 		    	document.getElementById("dialogPesquisa").innerHTML = data.substring(1,data.length);
			    			    	$("#dialog").dialog({width :'50%'});
			    			    	$("#dialog").dialog("open");
			    					$.unblockUI();
			    			    }else{
			    					$('.mensagem').html("<div class='alert alert-success alert-dismissible' role='alert' size='2'><button type='button' class='close' data-dismiss='alert' aria-label='Fechar'><span aria-hidden='true'>&times;</span></button><strong>Database exclu&iacute;do com sucesso !</strong></div>");
			    					$(".mensagem").fadeTo(3000, 500).slideUp(500, function(){
			    					   $(".mensagem").alert('close');
			    					});
			    					$.unblockUI();
			    					location.reload(); 
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

    	$("#dialog").dialog({
    	    autoOpen: false,
    	    height: 300,
    	    width: 200,
    	    show: {
    	        effect: "clip",
    	        duration: 300
    	    },
    	    hide: {
    	        effect: "clip",
    	        duration: 300
    	    },
    	    position: {my: "center", at: "center", of: window}
    	});
        
        $('#cancelar').click(function() {
        	javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/');
            return false;
        });
        $('#cancel').click(function() {
        	$("#dialog").dialog("close");
        });

        $("input").blur(function(){
            if($(this).val() == ""){
                    $(this).css({"border-color" : "#F00", "padding": "2px"});
                }else{
                	$(this).css({"border-color" : "#8CC3EE", "padding": "2px"});
            }
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
		.tabelaUsuarios{
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
		.ui-dialog-titlebar-close {
		  visibility: hidden;
		}


    </style>
<div class="painelAdmin">
    <h2 style="color:black"><?php echo __(utf8_encode('Listando Banco de Dados Cadastrados ')); ?></h2>
		<!-- Text input-->
		<?php 	echo $this->Session->flash('good');
				echo $this->Session->flash('bad');
		 ?>
		 <div class="mensagem">
		 </div>
		<div class="container">
		  <h2><?php utf8_encode('Listando Banco de Dados Cadastrados ')?></h2>
			  <table class="table-bordered table-hover">
			    <thead>
			      <tr style="background-color:#E4F4FD; height:35px;">
			        <th>Empresa</th>
			        <th><?php echo utf8_encode('A��ES')?></th>
			      </tr>
			    </thead>
			    <tbody>
			    <?php foreach($dados as $chave){
						foreach($chave as $value){
			    	?>
			      <tr>
			        <td><?php echo $value['nome_empresa']?></td>
			        <td><?php echo $this->Html->link(
									$this->Html->image('editar.png', array('alt' => 'editar', 'class'=>'link-editar')),
									array('controller' => 'Usuarios', 'action' => 'alterarDatabase',$value['cd_empresa']),array('escape' => false)); 
					        echo "  "; 
					        echo $this->Html->link(
					        		$this->Html->image('excluir.png', array('alt' => 'excluir', 'class'=>'link-editar', 'onClick' => "excluir(".$value['cd_empresa'].");")),"#",array('escape' => false));
			        ?>
			        </td>
			      </tr>
			      <?php 
						}
				      }
			      ?>
			    </tbody>
			  </table>
			  <div id="dialog" style="width:100px;" title="<?php echo utf8_encode("Usu�rios existentes para este banco de dados"); ?>">
			  <div id="dialogPesquisa">
			  		<table class="table-bordered table-hover" id="pesquisa">
						<thead>
							 <tr style="background-color:#E4F4FD; height:35px;">
								  <th>C&oacute;digo do Usu&aacute;rio</th>
							      <th>Nome</th>
							      <th>Login Utilizado</th>
							 </tr>
						</thead>
						<tbody>
							 <tr>
							      <td>Aguarde, carregando...</td>
							      <td></td>
							      <td></td>
							 </tr>
						</tbody>
					</table>
				</div>
				<div>
					<input id="enviar" type="button" class="btn btn-primary" value="Excluir mesmo assim" onClick="javascript:confirmDelete();">
				    <input id="cancel" type="button" class="btn btn-danger" value="Cancelar" >
			    </div>
			  </div>
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
