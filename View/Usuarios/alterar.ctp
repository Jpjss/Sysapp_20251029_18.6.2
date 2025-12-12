<?php
echo $this->Html->script('jquery-ui.js');
echo $this->Html->script('jquery-ui-timepicker-addon.js');
echo $this->Html->script('jquery.maskedinput.min.js');
echo $this->Html->script('select2.min.js');
echo $this->Html->css('jquery-ui-1.10.3.custom');
echo $this->Html->css('select2');
?>

<script>
    $(document).ready(function () {
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

        $('#cancelar').click(function () {
            javascript:navigator_Go('/SysApp/app/webroot/index.php/Usuarios/visualizar');
            return false;
        });

        $("#marcaTodasEmpresas").change(function () {
            $(".empresas").prop('checked', $(this).prop("checked"));
        });
        $("#marcaTodosRelatorios").change(function () {
            $(".relatorios").prop('checked', $(this).prop("checked"));
        });


        $("input").blur(function () {
            if ($(this).val() == "") {
                $(this).css({"border-color": "#F00", "padding": "2px"});
            } else {
                $(this).css({"border-color": "#8CC3EE", "padding": "2px"});
            }
        });

        /*         $("#login_usuario").blur(function(){
         if($(this).val() != ""){
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
         url: CbunnyObj.APP_PATH + 'Usuarios/verifica_email',
         data: {
         login_usuario : $('#login_usuario').val()
         },
         dataType: "html",
         success: function(result){
         if(result.substring(0,5) == "Email"){
         $('.mensagem').html("<div class='alert alert-danger alert-dismissible' role='alert' size='2'><button type='button' class='close' data-dismiss='alert' aria-label='Fechar'><span aria-hidden='true'>&times;</span></button><strong>E-mail ja em uso, Por favor utilize outro !</strong></div>");
         $('.hidden').html("<input type='hidden' id='verificarEmail' value='"+$('#login_usuario').val()+"'>");
         $.unblockUI();
         return false;
         }else{
         $('.mensagem').html("<div class='alert alert-success alert-dismissible' role='alert' size='2'><button type='button' class='close' data-dismiss='alert' aria-label='Fechar'><span aria-hidden='true'>&times;</span></button><strong>E-mail v&aacute;lido !</strong></div>");
         $('.hidden').html("<input type='hidden' id='verificarEmail' value=''>");
         $.unblockUI();
         return true;
         }
         $.unblockUI();
         }
         });
         }
         }); */

        $('#enviar').click(function () {

            if ($('#login_usuario').val() == $("#verificarEmail").val()) {
                alert("Email j&aacute; em uso, por favor escolha outro !");
                $("#login_usuario").focus();
                return false;
            }
            if ($('#senha_usuario').val() != $("#prox_senha_usuario_confirm").val()) {
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
            /* 		     if ($("#senha_usuario").val() === '') {
             alert("Por favor, preencha com senha do usuario !");
             $("#senha_usuario").focus();
             return false;
             }
             if ($("#prox_senha_usuario_confirm").val() === '') {
             alert("Por favor, preencha confirme a senha do usuario !");
             $("#prox_senha_usuario_confirm").focus();
             return false;
             } */

            var checkedsEmpresa = new Array();
            $("input[name='data[Relatorios][empresa][]']:checked").each(function () {
                checkedsEmpresa.push($(this).val());
            });
            if (checkedsEmpresa == "") {
                alert("Por favor, marque ao menos uma empresa !");
                $(".container3").focus();
                $(".container3").css({"border-color": "#8CC3EE", "padding": "2px"});
                return false;
            }

            var checkedsRelatorio = new Array();
            $("input[name='data[Relatorios][]']:checked").each(function () {
                checkedsRelatorio.push($(this).val());
            });
            if (checkedsRelatorio == "") {
                alert("Por favor, marque ao menos um Relatorio !");
                $(".container4").focus();
                $(".container4").css({"border-color": "#8CC3EE", "padding": "2px"});
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
                url: CbunnyObj.APP_PATH + 'Usuarios/alterarInfosUsuario',
                data: {
                    cd_usuario: $('#cd_usuario').val(),
                    nome_usuario: $('#nome_usuario').val(),
                    login_usuario: $('#login_usuario').val(),
                    senha_usuario: $('#senha_usuario').val(),
                    prox_senha_usuario_confirm: $('#prox_senha_usuario_confirm').val(),
                    nome_empresa: $('#nome_empresa').val(),
                    cd_empresa: checkedsEmpresa,
                    cd_interface: checkedsRelatorio,
                    cd_usu_erp: $('#cd_usu_erp').val()
                },
                dataType: "html",
                success: function (result) {
                    if (result.substring(0, 1) == "1") {
                        $('.mensagem').html("<div class='alert alert-success fade in' role='alert' size='2'><button type='button' class='close' data-dismiss='alert' aria-label='Fechar'><span aria-hidden='true'>&times;</span></button><strong>Usu&aacute;rio atualizado com sucesso !</strong></div>");
                        $(".mensagem").fadeTo(3000, 500).slideUp(500, function () {
                            $(".mensagem").alert('close');
                        });
                    } else {
                        $('.mensagem').html("<div class='alert alert-danger fade in' role='alert' size='2'><button type='button' class='close' data-dismiss='alert' aria-label='Fechar'><span aria-hidden='true'>&times;</span></button><strong>Ocorreu um problema, contate o administrador !</strong></div>");
                        $(".mensagem").fadeTo(3000, 500).slideUp(500, function () {
                            $(".mensagem").alert('close');
                        });
                    }
                    $("html, body").animate({scrollTop: 0}, "slow");
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
    <h2 style="color:black;"><?php echo __(utf8_encode('Alterar Informa&ccedil;&otilde;es do Usu&aacute;rio ')); ?></h2>

    <?php echo $this->Form->create('Usuarios', array('name' => "UsuariosNovoUsuarioForm")); ?>
    <!-- Text input-->
    <?php
    echo $this->Session->flash('good');
    echo $this->Session->flash('bad');
    ?>
    <div class="mensagem">
    </div>
    <div class="hidden">
    </div>
    <div class="container">
        <div class="form-group">
            <?php
            foreach ($dadosUsuario as $chave) {
                foreach ($chave as $value) {
                    ?>
                    <div class="col-md-4">
                        <label class="col-sd-6 control-label" for="nome_usuario"><?php echo utf8_encode('Primeiro nome do usu&aacute;rio'); ?></label>  
                        <input type="hidden" id="cd_usuario" name="cd_usuario" class="form-control input-md" value="<?php echo $value['cd_usuario']; ?>" >
                        <br/>
                        <input type="text" id="nome_usuario" name="nome_usuario" class="form-control input-md" value="<?php echo $value['nome_usuario'] ?>">
                        <br/>
                        
                        <label class="col-sd-6 control-label" for="cd_usu_erp"><?php echo utf8_encode('Codigo Usu&aacute;rio ERP'); ?></label>  
                        <input type="text" id="cd_usu_erp" name="cd_usu_erp" class="form-control input-md" value="<?php echo $value['cd_usu_erp'] ?>">
                        <br/>
                        
                        <label class="col-sd-6 control-label" for="login_usuario"><?php echo utf8_encode('E-mail ( Login )'); ?></label> 
                        <input type="text" id="login_usuario" name="login_usuario" class="form-control input-md" value="<?php echo $value['login_usuario'] ?>" readOnly>
                        <br/>
                        <label class="col-sd-6 control-label" for="senha_usuario"><?php echo utf8_encode('Digite a nova senha'); ?></label> 
                        <input type="password" id="senha_usuario" name="senha_usuario" class="form-control input-md" value="" >
                        <br/>
                        <label class="col-sd-6 control-label" for="prox_senha_usuario_confirm"><?php echo utf8_encode('Confirme a senha'); ?></label> 
                        <input type="password" id="prox_senha_usuario_confirm" name="prox_senha_usuario_confirm" class="form-control input-md" value="">
                        <br/>
                    </div>
                </div>
            </div>
            <?php
        }
    }
    ?>
    <div class="container">
        <div class="form-group">
            <label class="col-md-5 control-label" for="nome_empresa"><?php echo utf8_encode('Empresas'); ?></label>  
            <div class="col-md-4">
                <div class="container3">
                    <table id="tabela">
                        <tr>
                            <td style="width: 13px;"><input type="checkbox" id="marcaTodasEmpresas" /></td>
                            <td>SELECIONAR TODAS AS EMPRESAS</td>
                        </tr>
                        <?php
                        foreach ($infoEmpresas as $chave) {
                            foreach ($chave as $value) {
                                ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" class="empresas" name="data[Relatorios][empresa][]" <?php if (in_array($value['cd_empresa'], $cdUsuarioEmpresas)) {
                                    echo "checked=true";
                                } ?> value="<?php echo $value['cd_empresa']; ?>"/>
                                    </td>
                                    <td><?php echo utf8_encode($value['nome_empresa']); ?></td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </table>
                </div>  
            </div>
        </div>
    </div>
    <div class="container">
        <div class="form-group">
            <label class="col-md-5 control-label" for="nome_empresa"><?php echo utf8_encode('Relatorios'); ?></label>  
            <div class="col-md-4">
                <div class="container4">
                    <table id="tabela">
                        <tr>
                            <td style="width: 13px;"><input type="checkbox" id="marcaTodosRelatorios" /></td>
                            <td>SELECIONAR TODOS RELATORIOS</td>
                        </tr>
<?php
foreach ($relatorios as $chave) {
    foreach ($chave as $value) {
        ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" class="relatorios" name="data[Relatorios][]" <?php if (in_array($value['cd_interface'], $cdRelatorios)) {
            echo "checked=true";
        } ?>  value="<?php echo $value['cd_interface']; ?>" />
                                    </td>
                                    <td><?php echo utf8_encode($value['nome_interface']); ?></td>
                                </tr>
        <?php
    }
}
?>
                    </table>
                </div>  
            </div>
        </div>
    </div>
    <div class="container">
        <input id="enviar" type="button" class="btn btn-primary" value="Enviar" >
        <input id="cancelar" type="button" class="btn btn-danger" value="Cancelar" >
    </div>
</div>
<?php echo $this->Form->end(); ?>
