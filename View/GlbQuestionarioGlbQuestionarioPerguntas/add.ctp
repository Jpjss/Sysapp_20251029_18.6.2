<?php
$relacionadas = "";
foreach ($pergRelacionadas as $value) {
    $relacionadas[] = $value["GlbQuestionarioGlbQuestionarioPergunta"]["cd_pergunta"];
}
?>
<script>
    $(document).ready(function() {
        $('#cancelar').click(function() {
            window.location = "<?php echo $this->Html->url(array('controller' => 'GlbQuestionarios', 'action' => 'index')); ?>";
            return false;

        });
    });
</script>
<div class="glbQuestionarioGlbQuestionarioPerguntas form">
    <?php echo $this->Form->create('GlbQuestionarioGlbQuestionarioPergunta'); ?>
    <fieldset>
        <legend><?php echo __('Relacionar perguntas ao questionario'); ?></legend>
        <?php
        echo "<b>" . $questionario[0]["GlbQuestionario"]["ds_questionario"] . "</b><br>";
        echo $questionario[0]["GlbQuestionario"]["obs"] . "<br>";
        echo "Data de vigência: " . $this->Funcionalidades->formatarDataAp($questionario[0]["GlbQuestionario"]["dt_vigencia_ini"]) . " a " . $this->Funcionalidades->formatarDataAp($questionario[0]["GlbQuestionario"]["dt_vigencia_fim"]) . "<br><br>";
        foreach ($perguntas as $pergunta) {
            $opcoes[$pergunta["GlbQuestionarioPergunta"]["cd_pergunta"]] = ($pergunta["GlbQuestionarioPergunta"]["ds_pergunta"]);
        }
        echo $this->Form->input('perguntas', array('label' => false,
            'div' => true,
            'type' => 'select',
            'multiple' => 'checkbox',
            'legend' => 'false',
            'selected' => $relacionadas,
            'options' => $opcoes
        ));
        echo $this->Form->input('cd_questionario', array("type" => "hidden", "value" => $this->params["pass"][0]));
        ?>
    </fieldset>
    <table>
        <tr>
            <td style="width: 0.1px;"><?php echo $this->Form->end(__('Submit')); ?></td>
            <td style="width: 880px;"><br><?php echo $this->Form->button('Cancelar', array('type' => 'button', 'id' => 'cancelar', 'class' => 'botaoCancel', 'action' => 'index')); ?></td>
        </tr>
    </table>
</div>
