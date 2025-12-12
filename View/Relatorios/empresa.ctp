<?php

echo $this->Html->script('jquery-ui.js');
echo $this->Html->script('jquery-ui-timepicker-addon.js');
echo $this->Html->script('jquery.maskedinput.min.js');
echo $this->Html->script('select2.min.js');

echo $this->Html->css('jquery-ui-1.10.3.custom');
echo $this->Html->css('select2');
?>
<script type="text/javascript">
    $(document).ready(function () {

        $('#cancelar').click(function () {
            window.location = "<?php echo $this->Html->url(array('action' => 'index')); ?>";
            return false;
        });

    });

    function navigator_Go(url) {
        window.location.assign(url); // This technique is almost exactly the same as a full <a> page refresh, but it prevents Mobile Safari from jumping out of full-screen mode
    }

    function pegaValor(data) {
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
            success: function (data) {
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

</style>

<div class="DatabaseChoose">
    <h2 style="color:black"><?php echo __('Selecione a empresa...'); ?></h2>

    <?php echo $this->Form->create('Relatorios'); $i = 1;?>    
    <table>
        <tr>
  			<?php foreach($arrayFinal as $value){?>
            <td>
    			<?php 
    			echo $this->Html->image('Companies_Icon_128.png', array('id' => 'imagemLink' ,'onClick' => "pegaValor("."'".$value['nome_banco']."'".");", 'alt' => 'Empresa'));
    			//echo $value['nome_empresa'];
    			/* echo $this->Html->link($this->Html->image('Companies_Icon_128.png') . ' ' . __($value['nome_empresa']), "#",
    					array('id' => $i,'value' => $value['nome_banco'],'class' => 'empresa', 'onClick' => "pegaValor("."'".$value['nome_banco']."'".");",'escape' => false)); */
    			?>
        <figcaption><b><?php echo $value['nome_empresa'];?></b></figcaption>
        </td>
    		<?php }?>
        </tr>
    </table>
</div>
