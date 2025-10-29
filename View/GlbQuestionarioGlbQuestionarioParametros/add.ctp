<script>
    $(document).ready(function() {
        $('#cancelar').click(function() {
            window.location = "<?php echo $this->Html->url(array('action' => 'index')); ?>";
            return false;

        });
    });
</script>
<div class="glbQuestionarioGlbQuestionarioParametros form">
    <?php echo $this->Form->create('GlbQuestionarioGlbQuestionarioParametros'); ?>
    <fieldset>
        <legend><?php echo __('Relacionar Pesquisa &hArr; Parâmetro'); ?></legend>
        <?php
        echo "<b>Pesquisas:</b><br>";
        echo '<table>';
        foreach ($questionario as $value) {
            echo '<tr>';
            echo '<td style="width: 10px;"><input name="data[GlbQuestionarioGlbQuestionarioParametro][cd_questionario]" id="cd_questionario" value="' . $value["GlbQuestionario"]["cd_questionario"] . '" type="radio" /> </td>';
            echo '<td style="text-align: left;">' . $value["GlbQuestionario"]["ds_questionario"] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        echo "<b>Parâmetros:</b><br>";
        echo '<table>';
        foreach ($parametro as $value) {
            echo '<tr>';
            echo '<td style="width: 10px;"><input name="data[GlbQuestionarioGlbQuestionarioParametro][cd_parametro_questionario]" id="cd_parametro_questionario" value="' . $value["GlbQuestionarioParametro"]["cd_parametro_questionario"] . '" type="radio" /> </td>';
            echo '<td style="text-align: left;">' . $value["GlbQuestionarioParametro"]["ds_parametro_questionario"] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        ?>
    </fieldset>
    <table>
        <tr>
            <td style="width: 0.1px; text-align: left;"><?php echo $this->Form->end(__('Submit')); ?></td>
            <td style="width: 880px;"><br><?php echo $this->Form->button('Cancelar', array('type' => 'button', 'id' => 'cancelar', 'class' => 'botaoCancel', 'action' => 'index')); ?></td>
        </tr>
    </table>
</div>
