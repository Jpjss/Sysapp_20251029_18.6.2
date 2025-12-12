<table style="font-size: 10px;">
    <tr style="font-weight: bold;">
        <td>Protocolo</td>
        <td>Tipo</td>
        <td>Descrição</td>
        <td>Data</td>
        <td>Dt. Prev.</td>
        <td>Telefone</td>
    </tr>
    <?php foreach ($glbQuestionarioRespostaHistorico as $value) { ?>
        <tr>
            <td><?php echo @$value["GlbQuestionarioRespostaHistorico"]["protocolo"]; ?></td>
            <td><?php
                switch (@$value["GlbQuestionarioRespostaHistorico"]["cd_tipo_atendimento"]) {
                    case(1):
                        echo $this->Html->image('aniversariantes.png', array('alt' => 'Aniversariantes'));
                        break;
                    case(2):
                        echo $this->Html->image('vips.png', array('alt' => 'Vips'));
                        break;
                    case(3):
                        echo $this->Html->image('pos.png', array('alt' => 'Pos-venda'));
                        break;
                }
                ?></td>
            <td><?php echo $value["GlbQuestionarioRespostaHistorico"]["ds_historico"]; ?></td>
            <td><?php echo $this->Funcionalidades->formatarDataAp($value["GlbQuestionarioRespostaHistorico"]["dt_cad"]); ?></td>
            <td><?php echo $this->Funcionalidades->formatarDataAp($value["GlbQuestionarioRespostaHistorico"]["dt_prevista"]); ?></td>
            <td><?php echo $value["GlbQuestionarioRespostaHistorico"]["telefone"]; ?></td>
        </tr>
    <?php } ?>
</table>
