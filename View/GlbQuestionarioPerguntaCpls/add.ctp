<?php
if($this->params['pass'][1] == 2){
?>
<script>
    $(function() {
        $('#GlbQuestionarioPerguntaCplDsPerguntaCpl').attr('maxlength', '3').filter_input({regex: '[0-9]'});
    })
</script>
<?php
}
?>
<script>
    $(function() {
        $('#GlbQuestionarioPerguntaCplDsPerguntaCpl').limit('30', '#charsLeft');

        $('#cancelar').click(function() {
            window.location = "<?php echo $this->Html->url(array('controller' => 'GlbQuestionarioPerguntas', 'action' => 'index')); ?>";
            return false;

        });
    })
</script>
<div class="glbQuestionarioPerguntaCpls form">
    <?php echo $this->Form->create('GlbQuestionarioPerguntaCpl'); ?>
    <fieldset>
        <legend><?php echo __('Adicionar Resposta'); ?></legend>
        <?php
        echo $this->Form->input('cd_pergunta', array("type" => "hidden", "value" => $this->params["pass"][0]));
        echo $this->Form->input('tp_pergunta', array("type" => "hidden", "value" => $this->params["pass"][1]));
        echo $this->Form->input('cd_pergunta_cpl', array("type" => "hidden", "value" => @$respostas["GlbQuestionarioPerguntaCpl"]["cd_pergunta_cpl"]));
        echo $this->Form->input('prioridade', array("type" => "hidden", "value" => @$prioridade['GlbQuestionarioPerguntaCpl']['prioridade'] + 1));
        echo $this->Form->input('ds_pergunta_cpl', array("label" => "Resposta:", "div" => false));
        echo '<br><span id="charsLeft"></span> caracteres restantes.' . "<br><br>";
//        echo $this->Form->input('cd_usu_cad',array("type"=>"hidden","value"=>1));
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
