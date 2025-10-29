<style>
    body{font-size: 12px;}
</style>
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="text-align: center;">
    <tr>
        <td style="font-size: 24px; color: #59a6d6;">Relatório Simplificado de Quantidades<br></td>
    </tr>
    <?php foreach ($filiais as $filial) { ?>
        <tr>
            <td><?php echo utf8_decode($filial['PrcFilial']['nm_fant']); ?></td>
        </tr>
    <?php } ?>
    <tr>
        <td>MOVIMENTAÇÃO POR ESTOQUE: <?php echo $periodo; ?></td>
    </tr>
</table>
<br>
<div class="glbRelatorios index">
    <table>
        <tr style="text-align: center; background: #59a6d6; border: 0px;">
            <td style="width: 100px;">Referencia</td>
            <td style="width: 750px;">Descrição</td>
            <td style="width: 20px;">Cor</td>
            <td style="width: 400px;">Fornecedor</td>
            <td>Grade</td>
            <td style="width: 70px;">Quantidade</td>
        </tr>

        <?php
        foreach ($resultado as $value) {
            $resultados[$value['VwRelatorioSimplificado']['cd_cpl']][] = array_merge($value['VwRelatorioSimplificado'], $value[0]);
        }
        if ($ordem == 'cd_ref_fabrica') {
            @ksort($resultados);
        }

        $i = 0;
        $totalEstoque = 0;
        $totalVenda = 0;
        $totalCompra = 0;
        if (isset($resultados)) {
            foreach ($resultados as $value) {
                $i++;
                $tamanhosCompra = '';
                $tamanhosVenda = '';
                $tamanhosEstoque = '';

                $qtdeVenda   = 0;
                $qtdeCompra  = 0;
                $qtdeEstoque = 0;

                $ultVenda    = 0;
                $ultCompra   = 0;
                $ultEntrada  = 0;

                foreach ($value as $produto) {
                    if ($produto['qtde_pedido'] > 0) {
                        $qtdeCompra = $produto['qtde_pedido'] + $qtdeCompra;
                        @$tamanhosCompra[$produto['ds_tamanho']] += $produto['qtde_pedido'];
                    }
                    if ($produto['qtde_venda'] > 0) {
                        $qtdeVenda = $produto['qtde_venda'] + $qtdeVenda;
                        @$tamanhosVenda[$produto['ds_tamanho']] += $produto['qtde_venda'];
                    }
                    if ($produto['qtde_estoque'] > 0) {
                        $qtdeEstoque = $produto['qtde_estoque'] + $qtdeEstoque;
                        @$tamanhosEstoque[$produto['ds_tamanho']] += $produto['qtde_estoque'];
                    }
                    if ($produto['dt_ult_pedido'] > $ultCompra) {
                        $ultCompra = $produto['dt_ult_pedido'];
                    }
                    if ($produto['dt_ult_entrada'] > $ultEntrada) {
                        $ultEntrada = $produto['dt_ult_entrada'];
                    }
                    if ($produto['dt_ult_venda'] > $ultVenda) {
                        $ultVenda = $produto['dt_ult_venda'];
                    }
                }
                $totalVenda   += $qtdeVenda;
                $totalCompra  += $qtdeCompra;
                $totalEstoque += $qtdeEstoque;
                ?>

                <tr style="text-align: center; background: #EFF5FF;">
                    <td style="width: 100px;"><?php echo $produto['cd_cpl']; ?></td>
                    <td style="width: 750px;"><?php echo utf8_encode($produto['ds_prod_y']); ?></td>
                    <td style="width: 20px;"><?php echo utf8_encode($produto['ds_cor']); ?></td>
                    <td style="width: 400px;"><?php echo utf8_encode($produto['nome_fornecedor']); ?></td>
                    <td style="min-width: 200px;" >
                        <table style="min-width: 200px;">
                            <tr style="text-align: center; font-weight: bold;">
                                <?php foreach ($tam[$produto['cd_cpl']] as $value) { ?>
                                    <td style="min-width: 17px; text-align: center;"><?php echo $value; ?></td>
                                <?php } ?>
                            </tr>
                        </table>
                    </td>
                    <td style="width: 70px;">Qtde</td>
                </tr>
                <tr style="text-align: center;">
                    <td style="text-align: center;" colspan="3">Último Pedido: <?php echo $this->Funcionalidades->formatarDataAp($ultCompra); ?></td>
                    <td bgcolor="#F9F9F9" style="text-align: right">Compra:</td>
                    <td bgcolor="#F9F9F9" style="min-width: 200px;" >

                        <table style="min-width: 200px;">
                            <tr style="text-align: center;">
                                <?php foreach ($tam[$produto['cd_cpl']] as $value) { ?>

                                    <td style="min-width: 17px; text-align: center;"><?php
                                    if (isset($tamanhosCompra[$value])) {
                                        echo $tamanhosCompra[$value];
                                    } else {
                                        echo "0";
                                    }
                                    ?></td>
                                    <?php } ?>
                            </tr>
                        </table>
                    </td>
                    <td bgcolor="#F9F9F9" style="width: 70px;"><?php echo $qtdeCompra; ?></td>
                </tr>
                <tr style="text-align: center;">
                    <td style="text-align: center;" colspan="3">Última Venda: <?php echo $this->Funcionalidades->formatarDataAp($ultVenda); ?></td>
                    <td style="text-align: right">Venda:</td>
                    <td style="min-width: 200px;" >
                        <table style="min-width: 200px;">
                            <tr style="text-align: center;">
                                <?php foreach ($tam[$produto['cd_cpl']] as $value) { ?>

                                    <td style="min-width: 17px; text-align: center;"><?php
                                    if (isset($tamanhosVenda[$value])) {
                                        echo $tamanhosVenda[$value];
                                    } else {
                                        echo "0";
                                    }
                                    ?></td>
                                    <?php } ?>
                            </tr>
                        </table>
                    </td>
                    <td style="width: 70px;"><?php echo $qtdeVenda; ?></td>
                </tr>
                <tr style="text-align: center;">
                    <td style="text-align: center;" colspan="3">Última Entrada: <?php echo substr($this->Funcionalidades->formatarDataAp($ultEntrada), 0, 10); ?></td>
                    <td bgcolor="#F9F9F9" style="text-align: right">Estoque:</td>
                    <td bgcolor="#F9F9F9" style="min-width: 200px;" >
                        <table style="min-width: 200px;">
                            <tr style="text-align: center;">
                                <?php foreach ($tam[$produto['cd_cpl']] as $value) { ?>

                                    <td style="min-width: 17px; text-align: center;">
                                        <?php
                                        if (isset($tamanhosEstoque[$value])) {
                                            echo $tamanhosEstoque[$value];
                                        } else {
                                            echo "0";
                                        }
                                        ?>

                                    </td>
                                <?php } ?>
                            </tr>
                        </table>
                    </td>
                    <td bgcolor="#F9F9F9" style="width: 70px;"><?php echo $qtdeEstoque; ?></td>
                </tr>

                <?php
            }
        } else {
            echo "Não existem produtos para esse filtro.";
        }
        ?>
        <tr style="text-align: center; background: #59a6d6; border: 0px;">
            <td colspan="6" style="width: 100px; color: #FFFFFF">Total: <?php echo $i; ?><td>
        </tr>
        <tr style="text-align: center; background: #59a6d6; border: 0px;">
            <td colspan="6" style="width: 100px; color: #FFFFFF">Total Compras: <?php echo $totalCompra; ?><td>
        </tr>
        <tr style="text-align: center; background: #59a6d6; border: 0px;">
            <td colspan="6" style="width: 100px; color: #FFFFFF">Total Vendas: <?php echo $totalVenda; ?><td>
        </tr>
        <tr style="text-align: center; background: #59a6d6; border: 0px;">
            <td colspan="6" style="width: 100px; color: #FFFFFF">Total Estoque: <?php echo $totalEstoque; ?><td>
        </tr>

    </table>
</div>
