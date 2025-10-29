<script>
    $(function() {
        $('#GlbQuestionarioPerguntaDsPergunta').limit('150', '#charsLeft');
        $('#GlbQuestionarioPerguntaObs').limit('300', '#charsLeftObs');

        $('#cancelar').click(function() {
            window.location = "<?php echo $this->Html->url(array('action' => 'index')); ?>";
            return false;

        });
    })
</script>
<div class="glbQuestionarioPerguntas form">
    <?php echo $this->Form->create('GlbQuestionarioPergunta'); ?>
    <fieldset>
        <legend><?php echo __('Editar Pergunta'); ?></legend>
        <?php
        echo $this->Form->input('cd_pergunta');
        echo $this->Form->input('ds_pergunta', array("label" => "Descrição:", "type" => "textarea", "maxlength" => "150", "div" => false));
        echo '<br><span id="charsLeft"></span> caracteres restantes.' . "<br><br>";
        echo $this->Form->input('tp_pergunta', array('options' => array(0 => "Opções", 1 => "Dissertativa", 2 => "Pontuação", 3 => "Sugestão"), 'empty' => 'Selecione', "label" => "Tipo da pergunta:"));
        echo $this->Form->input('obs', array("label" => "Observação:", "type" => "textarea", "maxlength" => "300", "div" => false));
        echo '<br><span id="charsLeftObs"></span> caracteres restantes.' . "<br><br>";
        echo $this->Form->input('cd_usu_cad', array("type" => "hidden"));
        echo $this->Form->input('dt_cad', array("type" => "hidden", "value" => date("Y-m-d H:i:s")));
        ?>
    </fieldset>
    <table>
        <tr>
            <td style="width: 0.1px;"><?php echo $this->Form->end(__('Submit')); ?></td>
            <td style="width: 880px;"><br><?php echo $this->Form->button('Cancelar', array('type' => 'button', 'id' => 'cancelar', 'class' => 'botaoCancel', 'action' => 'index')); ?></td>
        </tr>
    </table>
</div>
