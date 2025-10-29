<table style="font-size: 10px;">
    <tr style="font-weight: bold;">
        <td>Status</td>
        <td>Lj</td>
        <td>Parc</td>
        <td>Dt. Vencimento</td>
        <td>Vlr. Tot</td>
        <td>D. Atr</td>
        <td>Valor Pg</td>
<!--        <td>Valor dev.</td>
        <td>C/ Juros</td>
        <td>Taxa</td>-->
    </tr>
    <?php
    foreach ($parcelas as $value) {
        if ($value['VwRcLancCplDetalhe']['dias_atz'] > 0) {
            $dias = 'style="color: red;"';
        } else {
            $dias = 'style="color: green;"';
        }
        switch ($value['VwRcLancCplDetalhe']['sts_parc']) {
            case 0:
                $status = 'Em Aberto';
                $cssStatus = '';
                break;
            case 1:
                $status = 'Quitado em ' . $this->Funcionalidades->formatarDataAp($value['VwRcLancCplDetalhe']['dt_lanc']);
                $cssStatus = 'style="color: green;"';
                break;
            case 2:
                $status = 'Quitado Parcialmente';
                $cssStatus = 'style="color: #59a6d6;"';
                break;
            case 4:
                $status = 'Cancelada';
                $cssStatus = 'style="color: red;"';
                break;
        }
        ?>
        <tr class="coluna">
            <!--<td><?php echo $value['VwRcLancCplDetalhe']['cd_lanc']; ?></td>-->
            <td <?php echo $cssStatus; ?>><?php echo $status; ?></td>
            <td><?php echo $value['VwRcLancCplDetalhe']['cd_filial']; ?></td>
            <td><?php echo $value['VwRcLancCplDetalhe']['parc']; ?></td>
            <td><?php echo $this->Funcionalidades->formatarDataAp(substr($value['VwRcLancCplDetalhe']['dt_vencto'], 0, 10)); ?></td>
            <td><?php echo $this->Formatacao->moeda($value['VwRcLancCplDetalhe']['vlr_parc']); ?></td>
            <td <?php echo $dias; ?>><?php echo $value['VwRcLancCplDetalhe']['dias_atz']; ?></td>
            <td><?php echo $this->Formatacao->moeda($value['VwRcLancCplDetalhe']['vlr_pgto']); ?></td>
<!--            <td><?php echo $this->Formatacao->moeda($value['VwRcLancCplDetalhe']['vlr_pgto']); ?></td>
            <td><?php echo $this->Formatacao->moeda($value['VwRcLancCplDetalhe']['vlr_pgto']); ?></td>
            <td><?php echo $this->Formatacao->porcentagem($value['VwRcLancCplDetalhe']['tx_jur_mes']); ?></td>-->
        </tr>
    <?php } ?>
</table>
