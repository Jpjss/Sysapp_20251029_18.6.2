<?php

echo $this->Html->script('jquery-ui.js');
echo $this->Html->script('jquery-ui-timepicker-addon.js');
echo $this->Html->script('jquery.maskedinput.min.js');
echo $this->Html->script('select2.min.js');
echo $this->Html->script('amcharts.js');
echo $this->Html->script('serial.js');
echo $this->Html->script('responsive.min.js');

echo $this->Html->css('jquery-ui-1.10.3.custom');
echo $this->Html->css('select2');
?>
<style>
    #pai{
        width: 100%;
        align: center;
    }
    #cabecalhoEmissao {
        text-align: right;
        padding-right: 150px;
    }
    #cabecalhoPeriodo{
        text-align: right;
        padding-right: 150px;
    }
    .contentRelatorio{
        width:100%;
        height:100%;
        text-align: center;
        margin-left: auto;
        margin-right: auto;
        /* border: 1px solid black; */
    }
    #voltar{
        width: 10%;
        margin-left: auto;
        margin-right: auto;
    }
    .voltarTopo {
        background: none repeat scroll 0 0 #000000 !important;
        bottom: 20px !important;
        color: #FFFFFF;
        text-align: center;
        display: block;
        font-size: 10px;
        font-weight: bold;
        height: 20px;
        position: fixed;
        right: 10px;
        text-transform: uppercase;
        width: 50px;    	
    }
    .horas{
        max-width: 950px;
    }
    .filiais{
        max-width: 950px;
    }
</style>
<?php if(isset($variavel)){?>
<script type="text/javascript">
    var chartData =
                        <?php echo $variavel; ?>;

    var chartDataFilial =
                        <?php echo $dadosFilial; ?>;

</script>
<script type="text/javascript">
    AmCharts.ready(function () {
        // SERIAL CHART
        chart = new AmCharts.AmSerialChart();
        chart.dataProvider = chartData;
        chart.categoryField = "hora";
        chart.depth3D = 20;
        chart.angle = 30;
        chart.responsive = {
            "enabled": true,
            "addDefaultRules": true,
            "rules": [
                {
                    "minWidth": 500,
                    "overrides": {
                        "innerRadius": "50%",
                    }
                }
            ]
        };

        // AXES
        // category
        var categoryAxis = chart.categoryAxis;
        categoryAxis.labelRotation = 90;
        categoryAxis.gridPosition = "start";

        // value
        var valueAxis = new AmCharts.ValueAxis();
        valueAxis.title = "Quantidade de Orcamentos";
        chart.addValueAxis(valueAxis);

        // GRAPH
        var graph = new AmCharts.AmGraph();
        graph.valueField = "quantidade";
        graph.colorField = "color";
        graph.balloonText = "<span style='font-size:14px'>[[category]]: <b>[[value]]</b></span>";
        graph.type = "column";
        graph.lineAlpha = 0;
        graph.fillAlphas = 1;
        chart.addGraph(graph);

        // CURSOR
        var chartCursor = new AmCharts.ChartCursor();
        chartCursor.cursorAlpha = 0;
        chartCursor.zoomable = false;
        chartCursor.categoryBalloonEnabled = false;
        chart.addChartCursor(chartCursor);

        // WRITE
        chart.write("chartdiv");
    });
</script>
<script type="text/javascript">
    AmCharts.ready(function () {
        // SERIAL CHART
        chart = new AmCharts.AmSerialChart();
        chart.dataProvider = chartDataFilial;
        chart.categoryField = "filial";
        chart.depth3D = 20;
        chart.angle = 30;
        chart.responsive = {
            "enabled": true,
            "addDefaultRules": true,
            "rules": [
                {
                    "minWidth": 500,
                    "overrides": {
                        "innerRadius": "50%",
                    }
                }
            ]
        };

        // AXES
        // category
        var categoryAxis = chart.categoryAxis;
        categoryAxis.labelRotation = 90;
        categoryAxis.gridPosition = "start";

        // value
        var valueAxis = new AmCharts.ValueAxis();
        valueAxis.title = "Quantidade de Orcamentos";
        chart.addValueAxis(valueAxis);

        // GRAPH
        var graph = new AmCharts.AmGraph();
        graph.valueField = "quantidade";
        graph.colorField = "color";
        graph.balloonText = "<span style='font-size:14px'>[[category]]: <b>[[value]]</b></span>";
        graph.type = "column";
        graph.lineAlpha = 0;
        graph.fillAlphas = 1;
        chart.addGraph(graph);

        // CURSOR
        var chartCursor = new AmCharts.ChartCursor();
        chartCursor.cursorAlpha = 0;
        chartCursor.zoomable = false;
        chartCursor.categoryBalloonEnabled = false;
        chart.addChartCursor(chartCursor);

        // WRITE
        chart.write("chartdivFilial");

    });
</script>
<?php }?>
<div id="pai">
    <body id="voltarTopo">
        <table id="cabecalhoTabela" width="100%" border="0" cellspacing="0" cellpadding="0" style="text-align: center; border-color:red">
            <tr>
                <td style="font-size: 24px;">Gr&aacute;fico de Or&ccedil;amento de Vendas <?php echo $this->Session->read('Conexao.Ativa'); ?><br></td>
            </tr>
            <tr>
                <td id="cabecalhoEmissao"><b>Emiss&atilde;o: <?php echo date('d/m/Y H:i:s')?></b><br>
                    <b>Per&iacute;odo: <?php echo $data_formatada_inicial?> a <?php echo $data_formatada_final?></b></td>

            </tr>
        </table>
        <br>
        <br>
        <input type="button" class="btn btn-primary" id="voltar" value="Voltar" onClick="javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/orcamento_venda')"/>
        <br>
        <div class="contentRelatorio">
					<?php 
						if(isset($variavel)){
					?>
            <div class="horas" style="margin-left: auto; margin-right: auto;">
                <h2 style="text-align: center;"> Por Horas </h2>
                <div id="chartdiv" style=" width: 100%; height: 400px;"></div>
            </div>
            <div class="filiais" style="margin-left: auto; margin-right: auto;">
                <h2 style="text-align: center;"> Por Filial </h2>
                <div id="chartdivFilial" style=" width: 100%; height: 400px;"></div>
            </div>
				<?php 	}else{
					echo "<h2 style='color:red;'>Sua busca n&atilde;o retornou resultados !</h2>";
				}?>
        </div>
    </body>
</div>
