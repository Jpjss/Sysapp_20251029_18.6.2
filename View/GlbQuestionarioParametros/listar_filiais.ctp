<table id="tabela" border="1">
    <?php foreach ($filiais as $filial) { ?>
        <tr class="coluna">
            <td style="width: 3px;"><input class="todasFiliais" type="checkbox" name="data[GlbQuestionarioParametro][filial_ult_compra][]" value="<?php echo $filial["filial"]["cd_filial"]; ?>" /></td>
            <td style="width: 150px;"><?php echo utf8_encode($filial["filial"]["nm_fant"]); ?></td>
            <td>0</td>
        </tr>
    <?php } ?>
</table>
