<?php
echo $this->Html->script('jquery-ui.js');
echo $this->Html->script('jquery-ui-timepicker-addon.js');
echo $this->Html->script('jquery.maskedinput.min.js');
echo $this->Html->script('select2.min.js');
echo $this->Html->css('jquery-ui-1.10.3.custom');
echo $this->Html->css('select2');
?>

<script type="text/javascript">
    $(document).ready(function() {

        $('#cancelar').click(function() {
            window.location = "<?php echo $this->Html->url(array('action' => 'index')); ?>";
            return false;
        });

     });

    function navigator_Go(url) {
        window.location.assign(url); // This technique is almost exactly the same as a full <a> page refresh, but it prevents Mobile Safari from jumping out of full-screen mode
    }
    
		function pegaValor(data){
		  var dbSelecionado = data;
	      var request_uri = document.location.hostname;
		  var host = document.location.port;
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
              url: 'empresa',
              data: {
                  nome_db: dbSelecionado
              },
             dataType: "html",
              success: function(data) {
            	  javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/');
              	$.unblockUI();
					  //window.location = ("http://"+ request_uri + ":" + host +"/SysApp/app/webroot/index.php/Relatorios/")
             }
          });
		}
</script>
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
        tabela, tr, td {
         border: none; 
        } 
        .descricao{
            width: 350px;
        }
        #imagemLink{
        	cursor: pointer;
        }
        .DatabaseChoose{
        font-size: 15px !important;
        }
        .bolaVermelha {
		    border-radius: 50%;
		    display: inline-block;
		    height: 30px;
		    width: 30px;
		    border: 1px solid #000000;
		    background-color: #DD0000;
		}
		.bolaAmarela {
		    border-radius: 50%;
		    display: inline-block;
		    height: 30px;
		    width: 30px;
		    border: 1px solid #000000;
		    background-color: #FFFF00;
		}
		.bolaVerde {
		    border-radius: 50%;
		    display: inline-block;
		    height: 30px;
		    width: 30px;
		    border: 1px solid #000000;
		    background-color: #00DD00;
		}
		.teste{
			text-align: center;
		}

    </style>
    
<div class="conexoes">
    
    <h2 style="color:black; text-align:center;"><?php echo utf8_encode('Conex�es Ativas'); ?></h2>
    
	<div class="container-fluid">
		<div class="row-fluid">
		<fieldset style="color:black; border:1px solid #211E1A;">
			<div>
				<label>Legenda:</label>
				<div class="col-md-2">
					<input type="button" class="bolaVermelha"/><label>Varias Conexoes</label>
				</div>
				<div class="col-md-2">
					<input type="button" class="bolaAmarela"/><label>Conexoes Consideraveis</label>
				</div>
				<div class="col-md-2">
					<input type="button" class="bolaVerde"/><label>Poucas Conexoes</label>
				</div>
			</div>
		</fieldset>
		
	 
	<?php if(isset($conexoes)){?>
	
	<!-- isset => Determine if a variable is set and is not NULL. Neste caso ta valiando se a variavel $conexoes est� nula ou nao -->
	
		<?php foreach($conexoes as $valor){
				foreach($valor as $value){
			?>
			
			<?php if(in_array("VERMELHA", $value)){?>
				<div class="col-md-2">
					<table>
						<tr>
							<td><h3 style="color:black;"><label><?php echo $value['nome_empresa']; ?></label></h3><input type="button" class="bolaVermelha" /></td>
						</tr>
					</table>
				</div>
			<?php }?>
			<?php if(in_array("AMARELA", $value)){?>
				<div class="col-md-2">
					<table>
						<tr>
							<td><h3 style="color:black;"><label><?php echo $value['nome_empresa']; ?></label></h3><input type="button" class="bolaAmarela" /></td>
						</tr>
					</table>
				</div>
			<?php }?>
			<?php if(in_array("VERDE", $value)){ ?>
				<div class="col-md-2">
					<table>
						<tr>
							<td><h3 style="color:black;"><label><?php echo $value['nome_empresa']; ?></label></h3><input type="button" class="bolaVerde" /></td>
						</tr>
					</table>
				</div>
			<?php }?>
		<?php 	}
			}?>
	<?php }?>
		</div>
	</div>
</div>
