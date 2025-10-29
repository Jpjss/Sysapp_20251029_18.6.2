<script>
    $(document).ready(function() {
        $('#cancelar').click(function() {
            window.location = "<?php echo $this->Html->url(array('action' => 'index')); ?>";
            return false;

        });
    });
</script>
<div class="glbQuestionarioGlbQuestionarioParametros index">
    <?php
    echo $this->Form->create('GlbQuestionarioGlbQuestionarioParametro');
    echo $this->Form->input('cd_parametro_questionario', array("type" => "hidden"));
    ?>
        <fieldset>
        <legend><?php echo __('Pesquisa &hArr; Parâmetro'); ?></legend>
        <?php
        echo "<b>Pesquisa:</b><br>";
        echo $questionario['GlbQuestionario']['ds_questionario'];
        echo $this->Form->input('cd_questionario', array("type" => "hidden", 'value' => $questionario['GlbQuestionario']['cd_questionario']));
        echo '<br><br>';
        echo "<b>Parâmetros:</b><br>";
        echo '<table>';
        foreach ($parametro as $value) {
            if($this->request->data['GlbQuestionarioGlbQuestionarioParametro']['cd_parametro_questiona'] == $value['GlbQuestionarioParametro']['cd_parametro_questionario']){
                $checkedPar = 'checked="checked"';
            }else{
                $checkedPar = '';
            }
            echo '<tr>';
            echo '<td><input name="data[GlbQuestionarioGlbQuestionarioParametro][cd_parametro_questionario]" id="cd_parametro_questionario" '.$checkedPar.' value="'.$value["GlbQuestionarioParametro"]["cd_parametro_questionario"].'" type="radio" /> </td>';
            echo '<td>'.$value["GlbQuestionarioParametro"]["ds_parametro_questionario"].'</td>';
        }
        echo '</table>';
        ?>
    </fieldset>
    <table>
        <tr>
            <td style="width: 0.1px;"><?php echo $this->Form->end(__('Submit')); ?></td>
            <td style="width: 880px;"><br><?php echo $this->Form->button('Cancelar', array('type' => 'button', 'id' => 'cancelar', 'class' => 'botaoCancel', 'action' => 'index')); ?></td>
        </tr>
    </table>
</div>
