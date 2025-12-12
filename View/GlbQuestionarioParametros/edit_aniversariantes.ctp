<script>
    $(document).ready(function() {
        $('#cancelar').click(function() {
            window.location = "<?php echo $this->Html->url(array('action' => 'index')); ?>";
            return false;
        });

        $("#GlbQuestionarioParametroParametrosForm").submit(function(event) {
            if (!$('.todasFiliais').is(':checked')) {
                alert("Você deve selecionar uma filial!");
                $('#marcaTodasFiliais').focus();
                return false;
            }
            if (!$('.todasQuantidade').is(':checked')) {
                alert("Você deve selecionar uma quantidade de compra!");
                $('#marcaTodasQuantidade').focus();
                return false;
            }
            if (!$('.todasDtUltCompra').is(':checked')) {
                alert("Você deve selecionar uma data da última compra!");
                $('#marcaTodasDtUltCompra').focus();
                return false;
            }
            if (!$('.todasVlrUltCompra').is(':checked')) {
                alert("Você deve selecionar um valor da última compra!");
                $('#marcaTodasVlrUltCompra').focus();
                return false;
            }
            if (!$('.todasVlrMedio').is(':checked')) {
                alert("Você deve selecionar um valor médio de compra!");
                $('#marcaTodasVlrMedio').focus();
                return false;
            }
            if (!$('.todasDtCadastro').is(':checked')) {
                alert("Você deve selecionar uma data de cadastro!");
                $('#marcaTodasDtCadastro').focus();
                return false;
            }
            if (!$('.todasDtAtualizacao').is(':checked')) {
                alert("Você deve selecionar uma data de atualização!");
                $('#marcaTodasDtAtualizacao').focus();
                return false;
            }
        });


        $(".regioes").change(function() {
            $.blockUI({
                message: "<b>Por Favor, Aguarde!</b>",
                css: {
                    border: 'none',
                    padding: '15px',
                    backgroundColor: '#000',
                    '-webkit-border-radius': '10px',
                    '-moz-border-radius': '10px',
                    opacity: .5,
                    color: '#fff',
                }});
            valor = '';
            $("input[name='data[GlbQuestionarioParametro][regiao_cliente][]']").each(function() {
                if ($(this).is(':checked')) {
                    valor = valor + ',' + $(this).val();
                }
            });
            $.ajax({
                type: "POST",
                url: '<?php echo Router::url(array('controller' => 'GlbQuestionarioParametros', 'action' => 'listarFiliais')); ?>',
                data: {
                    regioes: valor
                },
                dataType: "html",
                success: function(data) {
                    $("#container_filiais").html(data);
                    $.unblockUI();
                },
            });

        });


        $("#marcaTodasFiliais").change(function() {
            $(".todasFiliais").prop('checked', $(this).prop("checked"));
        });
        $("#marcaTodasQuantidade").change(function() {
            $(".todasQuantidade").prop('checked', $(this).prop("checked"));
        });
        $("#marcaTodasDtUltCompra").change(function() {
            $(".todasDtUltCompra").prop('checked', $(this).prop("checked"));
        });
        $("#marcaTodasVlrUltCompra").change(function() {
            $(".todasVlrUltCompra").prop('checked', $(this).prop("checked"));
        });
        $("#marcaTodasVlrMedio").change(function() {
            $(".todasVlrMedio").prop('checked', $(this).prop("checked"));
        });
        $("#marcaTodasDtCadastro").change(function() {
            $(".todasDtCadastro").prop('checked', $(this).prop("checked"));
        });
        $("#marcaTodasDtAtualizacao").change(function() {
            $(".todasDtAtualizacao").prop('checked', $(this).prop("checked"));
        });
    });
</script>
<?php
//var_dump($this->request->data);
foreach ($quantRel as $value) {
    $quantRelacionadas[] = $value["GlbQuestionarioParametroVincFaixaQuantidadeCompra"]["cd_parametro"];
}
foreach ($valorMedioRel as $value) {
    $valorMedioRelacionadas[] = $value["GlbQuestionarioParametroVincValorMedioCompra"]["cd_parametro_faix"];
}
foreach ($dataUltCompraRel as $value) {
    $dataUltCompraRelacionadas[] = $value["GlbQuestionarioParametroVincDataUltCompra"]["cd_parametro_data_ul"];
}
?>
<div class="glbQuestionarios index">
    <h2><?php echo __('Parâmetros'); ?></h2>
    <style>
        .container { border:2px solid #ccc; width:300px; height: 150px; overflow-y: scroll; }
        .container2 { border:0px solid #ccc; width:300px; height: 190px;}
        .container3 { border:2px solid #ccc; width:620px;}
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
            width:280px;
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
        tabela, tr, td { border: none; } 

    </style>

    <?php
    echo $this->Form->create('GlbQuestionarioParametro');
    echo $this->Form->input('cd_parametro_questionario', array("type" => "hidden"));
    ?>
    <table>
        <tr>
            <td style="width: 280px">
                <table id="tabelaTitulo" border="1">
                    <tr style="font-weight: bold; ">
                        <td style="width: 3px;"></td>
                        <td>Região</td>
                        <td>Qtde</td>
                    </tr>
                </table>
                <div style="padding: 16px; padding-bottom: 0px; padding-top: 0px; height: 3px;">&nbsp;</div>
                <div class="container">
                    <table id="tabela" border="1">
                        <?php foreach ($regiaoFilial as $regiao) { ?>
                            <tr class="coluna">
                                <td style="width: 3px;"><input class="regioes" name="data[GlbQuestionarioParametro][regiao_cliente][]" value="<?php echo $regiao["PrcRegiaoFilial"]["cd_regiao"] ?>" type="checkbox" /></td>
                                <td style="width: 150px;"><?php echo $regiao["PrcRegiaoFilial"]["ds_regiao"]; ?></td>
                                <td style="width: 150px;">0</td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>    
            </td>
            <td style="width: 280px">
                <table id="tabelaTitulo" border="1">
                    <tr style="font-weight: bold; ">
                        <td style="width: 3px;"></td>
                        <td>Filial da Última Compra</td>
                        <td>Qtde</td>
                    </tr>
                </table>
                <div style="padding: 16px; padding-bottom: 0px; padding-top: 0px; height: 3px;"><input type="checkbox" id="marcaTodasFiliais" value="" />&nbsp;&nbsp;MARCAR TODAS</div>
                <div id='container_filiais' class="container">
                    <table id="tabela" border="1">

                        <?php
                        $filiaisEscolhidas = explode(',', $this->request->data["GlbQuestionarioParametro"]["filial_ult_compra"]);
                        foreach ($filiais as $filial) {
                            if (in_array($filial["PrcFilial"]["cd_filial"], $filiaisEscolhidas)) {
                                $filChecked = 'checked="checked"';
                            } else {
                                $filChecked = "";
                            }
                            ?>
                            <tr class="coluna">
                                <td style="width: 3px;"><input class="todasFiliais" <?php echo $filChecked; ?> type="checkbox" name="data[GlbQuestionarioParametro][filial_ult_compra][]" value="<?php echo $filial["PrcFilial"]["cd_filial"]; ?>" /></td>
                                <td style="width: 150px;"><?php echo utf8_encode($filial["PrcFilial"]["nm_fant"]); ?></td>
                                <td>0</td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>  
            </td>
            <td>
                <table id="tabelaTitulo" border="1">
                    <tr style="font-weight: bold; ">
                        <td style="width: 3px;"></td>
                        <td>Qtd de compras</td>
                        <td>Qtde</td>
                    </tr>
                </table>
                <div style="padding: 16px; padding-bottom: 0px; padding-top: 0px; height: 3px;"><input type="checkbox" id="marcaTodasQuantidade" value="" />&nbsp;&nbsp;MARCAR TODAS</div>
                <div class="container">
                    <table id="tabela" border="1">
                        <?php
                        foreach ($faixaQuantidade as $qtdCompras) {
                            if (@in_array($qtdCompras["GlbQuestionarioParametroFaixaQuantidadeCompra"]["cd_parametro_fai"], $quantRelacionadas)) {
                                $qtdChecked = 'checked="checked"';
                            } else {
                                $qtdChecked = "";
                            }
                            ?>
                            <tr class="coluna">
                                <td style="width: 3px;"><input class="todasQuantidade" type="checkbox" <?php echo $qtdChecked; ?> name="data[GlbQuestionarioParametro][qtdCompras][]" value="<?php echo $qtdCompras["GlbQuestionarioParametroFaixaQuantidadeCompra"]["cd_parametro_fai"]; ?>" /></td>
                                <td style="width: 150px;"><?php echo $qtdCompras["GlbQuestionarioParametroFaixaQuantidadeCompra"]["ds_parametro_fai"]; ?></td>
                                <td>0</td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <table id="tabelaTitulo" border="1">
                    <tr style="font-weight: bold; ">
                        <td style="width: 3px;"></td>
                        <td>Data da última compra</td>
                        <td>Qtde</td>
                    </tr>
                </table>
                <div style="padding: 16px; padding-bottom: 0px; padding-top: 0px; height: 3px;"><input type="checkbox" id="marcaTodasDtUltCompra" value="" />&nbsp;&nbsp;MARCAR TODAS</div>
                <div class="container">
                    <table id="tabela" border="1">
                        <?php
                        foreach ($dataUltCompra as $dtUltCompra) {

                            if (@in_array($dtUltCompra["GlbQuestionarioParametroDataUltCompra"]["cd_parametro_data_ult_co"], $dataUltCompraRelacionadas)) {
                                $dtUltChecked = 'checked="checked"';
                            } else {
                                $dtUltChecked = "";
                            }
                            ?>
                            <tr class="coluna">
                                <td style="width: 3px;"><input class="todasDtUltCompra"  type="checkbox" <?php echo $dtUltChecked; ?> name="data[GlbQuestionarioParametro][dtUltCompra][]" value="<?php echo $dtUltCompra["GlbQuestionarioParametroDataUltCompra"]["cd_parametro_data_ult_co"]; ?>" /></td>
                                <td style="width: 150px;"><?php echo $dtUltCompra["GlbQuestionarioParametroDataUltCompra"]["ds_parametro_data_ult_co"] ?></td>
                                <td>0</td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>    
            </td>

            <td>
                <table id="tabelaTitulo" border="1">
                    <tr style="font-weight: bold; ">
                        <td style="width: 3px;"></td>
                        <td>Valor médio de compra</td>
                        <td>Qtde</td>
                    </tr>
                </table>
                <div style="padding: 16px; padding-bottom: 0px; padding-top: 0px; height: 3px;"><input type="checkbox" id="marcaTodasVlrMedio" value="" />&nbsp;&nbsp;MARCAR TODAS</div>
                <div class="container">
                    <table id="tabela" border="1">
                        <?php
                        foreach ($valorMedio as $vlrMedio) {
                            if (@in_array($vlrMedio["GlbQuestionarioParametroFaixaValorMedioCompra"]["cd_parametro_fai"], $valorMedioRelacionadas)) {
                                $medioChecked = 'checked="checked"';
                            } else {
                                $medioChecked = "";
                            }
                            ?>
                            <tr class="coluna">
                                <td style="width: 3px;"><input class="todasVlrMedio"  type="checkbox" <?php echo $medioChecked; ?> name="data[GlbQuestionarioParametro][vlrMedio][]" value="<?php echo $vlrMedio["GlbQuestionarioParametroFaixaValorMedioCompra"]["cd_parametro_fai"]; ?>" /></td>
                                <td style="width: 150px;"><?php echo $vlrMedio["GlbQuestionarioParametroFaixaValorMedioCompra"]["ds_parametro_fai"] ?></td>
                                <td>0</td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>
            </td>
            <td></td>
        </tr>
        <tr>
            <td colspan="3">
                Data de Aniversário: <?php echo $this->Form->input('dt_vigencia', array('label' => false, 'class' => 'dataAniversario', 'type' => 'text')); ?>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="container3">
                    <table id="tabelas" style="width:620px; border: 0px;" border="1">
                        <tr class="coluna">
                            <td colspan="2">Descrição da Pesquisa: <?php echo $this->Form->input('ds_parametro_questionario', array('label' => false, "type" => "textarea", "maxlength" => "255", "rows" => 4)); ?></td>
                        </tr>
                        <tr class="coluna">
                            <td colspan="2">Desconsiderar Funcionários: 
                                <?php
                                $status = array('0' => 'Não', '1' => 'Sim');
                                echo $this->Form->input('desconsiderar_funcionarios', array('options' => $status, 'default' => $this->data['GlbQuestionarioParametro']['desconsiderar_funcionarios'], 'label' => false));
                                ?>
                            </td>
                        </tr>
                        <tr class="coluna">
                            <td colspan="2">Status: 
                                <?php
                                $status = array('0' => 'Inativo', '1' => 'Ativo', '2' => 'Cancelada');
                                echo $this->Form->input('sts_parametro_cobranca', array('options' => $status, 'default' => $this->data['GlbQuestionarioParametro']['sts_parametro_cobranca'], 'label' => false));
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>    
            </td>
        </tr>
    </table>
    <table>
        <tr>
            <td style="width: 0.1px;"><?php echo $this->Form->end(__('Submit')); ?></td>
            <td style="width: 880px;"><br><?php echo $this->Form->button('Cancelar', array('type' => 'button', 'id' => 'cancelar', 'class' => 'botaoCancel', 'action' => 'index')); ?></td>
        </tr>
    </table>
</div>
