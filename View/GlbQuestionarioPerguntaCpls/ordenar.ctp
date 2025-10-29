<?php
//echo $this->Html->script('jquery-1.10.2.min.js');
echo $this->Html->script('jquery-ui.js');
?>
<script type="text/javascript">
    $(document).ready(function() {
        $(function() {
            $("#contentLeft ul").sortable({opacity: 0.6, cursor: 'move', update: function() {
                    var order = $(this).sortable("serialize"); //+ '&action=updateRecordsListings';
                    order = order.split("&");

                    var length = order.length,
                            element = null;
                    var novaOrdem = "";
                    for (var i = 0; i < length; i++) {
                        newOrder = order[i].split("=");
                        novaOrdem = novaOrdem + "." + newOrder[1];

                    }

                    $("#GlbQuestionarioPerguntaCplOrdemAtualizada").val(novaOrdem.substring(1));
                }
            });

            $('#cancelar').click(function() {
                window.location = "<?php echo $this->Html->url(array('action' => 'listaResposta', $this->params["pass"][0],$this->params["pass"][1])); ?>";
                return false;

            });
        });

    });
</script>

<div class="glbQuestionarioPerguntaCpls index">
    <?php echo $this->Form->create('GlbQuestionarioPerguntaCpl'); ?>
    <fieldset>
        <legend><?php echo __('Editar Ordem Resposta'); ?></legend>
        <div id="contentLeft">
            <ul>
                <?php
                echo "Clique na opção e arraste até a posição desejada: </br></br>";
                $i = 0;
                foreach ($glbQuestionarioPerguntaCpls as $glbQuestionarioPerguntaCpl):
                    ?>
                    <li id="records_<?php echo $i; ?>">
                        <?php
                        @$ordem .= "." . $glbQuestionarioPerguntaCpl['GlbQuestionarioPerguntaCpl']['id'];
                        echo ($glbQuestionarioPerguntaCpl['GlbQuestionarioPerguntaCpl']['ds_pergunta_cpl']);
                        @$x.= "." . $i++;
                        ?>
                    </li>

                    <?php
                endforeach;
                $ordem = substr($ordem, 1);
                $x = substr($x, 1);
                echo $this->Form->input('ordem', array("type" => "hidden", "label" => false, "value" => $ordem));
                echo $this->Form->input('ordemAtualizada', array("type" => "hidden", "label" => false,));
                echo $this->Form->input('cd_pergunta', array("type" => "hidden", "value" => $this->params["pass"][0],));
                echo $this->Form->input('tp_pergunta', array("type" => "hidden", "value" => $this->params["pass"][1],));
                ?>
            </ul>
        </div>
    </fieldset>
    <table>
        <tr>
            <td style="width: 0.1px;"><?php echo $this->Form->end(__('Salvar Ordem')); ?></td>
            <td style="width: 880px;"><br><?php echo $this->Form->button('Cancelar', array('type' => 'button', 'id' => 'cancelar', 'class' => 'botaoCancel', 'action' => 'index')); ?></td>
        </tr>
    </table>



</div>
