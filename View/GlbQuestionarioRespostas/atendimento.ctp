<?php
//var_dump($cliente['VwQuestionarioCliente']);
$tiposFones = array("0" => "Residencial", "1" => "Recado", "2" => "Comercial", "3" => "Gerente", "4" => "Cobrança", "5" => "Vendedor", "6" => "Fax",
    "7" => "Pai", "8" => "Mãe", "9" => "Comercial Conjuge", "10" => "Celular", "11" => "Outras Rendas", "12" => "Referencia Pessoal 1",
    "13" => "Referencia Pessoal 2", "14" => "Referencia Pessoal 3", "15" => "Referencia Pessoal 4", "16" => "Celular Conjuge", "17" => "Referencia Pessoal 5",
    "18" => "Referencia Pessoal 6", "19" => "Referencia Pessoal 7", "20" => "Referencia Pessoal 8");
date_default_timezone_set("Brazil/East");
$hora = date("H:i:s", mktime(gmdate("H") - 3, gmdate("i"), gmdate("s")));
?>
<script>
    var placar = 0;
    var competicao = 106;
    c = navigator.appVersion.toLowerCase();
    if (c.indexOf("msie 5") != -1)
        document.write('<link href="styles-ie5.css" rel="stylesheet" type="text/css" />');

    function showDown(evt)
    {
        evt = (evt) ? evt : ((event) ? event : null);

        if (evt)
        {
            if (navigator.appName == "Netscape")
            {
                if (evt.which == 116)
                {
                    // When F5 is pressed
                    cancelKey(evt);
                }
                else if (evt.ctrlKey && (evt.which == 82))
                {
                    // When ctrl is pressed with R or N
                    cancelKey(evt);
                }
            }
            else
            {
                if (event.keyCode == 116)
                {
                    // When F5 is pressed
                    cancelKey(evt);
                }
                else if (event.ctrlKey && (event.keyCode == 78 || event.keyCode == 82))
                {
                    // When ctrl is pressed with R or N
                    cancelKey(evt);
                }
            }
        }
    }

    function cancelKey(evt)
    {
        if (evt.preventDefault)
        {
            evt.preventDefault();
            return false;
        }
        else
        {
            evt.keyCode = 0;
            evt.returnValue = false;
        }
    }

    if (navigator.appName == "Netscape")
        document.addEventListener("keypress", showDown, true);


    document.onkeydown = showDown;

    $(document).ready(function() {
        function horarioLocal() {
            localtime = new Date();
            var mseg = localtime.getMilliseconds();   // 0-999
            var horario = localtime.getHours() + ":" + localtime.getMinutes() + ":" + localtime.getSeconds();
            return horario;
        }
        $('#GlbQuestionarioRespostaHoraInicio').val(horarioLocal());
        $('.input-texto').alphanumeric({allow: "., "});
        $('.rating-class').barrating('show', {
            showValues: true,
            showSelectedRating: false
        });
        $('form').submit(function() {

            $('#GlbQuestionarioRespostaHoraFim').val(horarioLocal());

        });

        $(function() {
            $('#hms').countdown({since: 0, compact: true,
                format: 'HMS', description: ''});


        });


        $(':radio').click(function() {
            if ($(this).is(':checked')) {
                $("#telFone").html($(this).val());
                $("#telefone").val($(this).val());
                $("#cTelefones").dialog("close");
            }
        })
        $('#iniciar').click(function() {

            var data = new Date();

            var hora = data.getHours();          // 0-23
            var min = data.getMinutes();        // 0-59
            var seg = data.getSeconds();        // 0-59
            var mseg = data.getMilliseconds();   // 0-999
            var tz = data.getTimezoneOffset(); // em minutos

            var str_hora = hora + ':' + min + ':' + seg;

            $('#tmpLigacao').countdown({since: 0, compact: true,
                format: 'HMS', description: ''});

            $("#hr_inicio").val(str_hora);
            $('#iniciar').attr('disabled', 'disabled');
            $('#iniciarPausar').removeAttr('disabled');
            $('#salvarLigacao').removeAttr('disabled');
        });
        $('#iniciarPausar').click(function() {
            if ($(this).text() == 'Pausar Ligação') {
                $(this).text('Voltar à Ligação');
                $('#salvarLigacao').attr('disabled', 'disabled');
                $('#iniciar').attr('disabled', 'disabled');
                $('#tmpLigacao').countdown('pause');
            } else {
                $(this).text('Pausar Ligação');
                $('#tmpLigacao').countdown('resume');
                $('#iniciarPausar').removeAttr('disabled');
                $('#salvarLigacao').removeAttr('disabled');
            }
        });

        $('#salvarLigacao').click(function() {
            var data = new Date();
            var hora = data.getHours();          // 0-23
            var min = data.getMinutes();        // 0-59
            var seg = data.getSeconds();        // 0-59
            var mseg = data.getMilliseconds();   // 0-999
            var tz = data.getTimezoneOffset(); // em minutos

            var hr_final = hora + ':' + min + ':' + seg;

            $.blockUI({
                message: "<b>Por Favor, Aguarde!</b>",
                css: {
                    border: 'none',
                    padding: '15px',
                    backgroundColor: '#000',
                    '-webkit-border-radius': '10px',
                    '-moz-border-radius': '10px',
                    opacity: .5,
                    color: '#fff',
                }});

            var ds_historico = $("#ds_historico").val();
            var telefone = $("#telefone").val();
            var cd_pessoa = $("#GlbQuestionarioRespostaCdPessoa").val();
            var cd_resposta = $("#GlbQuestionarioRespostaCdResposta").val();
            var cd_usu = $("#GlbQuestionarioRespostaCdUsuCad").val();
            var cd_tipo_atendimento = $("#cd_tipo_atendimento").val();
            var protocolo = $("#GlbQuestionarioRespostaProtocolo").val();
            var hr_inicio = $("#hr_inicio").val();
            hoje = new Date()
            dataAtual = hoje.getFullYear() + '-' + (hoje.getMonth() + 1) + '-' + hoje.getDate()

            $.ajax({
                type: "POST",
                url: '<?php echo Router::url(array('controller' => 'glbQuestionarioRespostaHistoricos', 'action' => 'add')); ?>',
                data: {
                    ds_historico: ds_historico,
                    telefone: telefone,
                    cd_pessoa: cd_pessoa,
                    cd_resposta: cd_resposta,
                    dt_cad: dataAtual,
                    cd_usu: cd_usu,
                    cd_tipo_atendimento: cd_tipo_atendimento,
                    protocolo: protocolo,
                    hr_inicio: hr_inicio,
                    hr_final: hr_final
                },
                dataType: "html",
                success: function(data) {
                    $("#tmpLigacao").countdown('destroy');
                    $("#tmpLigacao").html('00:00:00');
                    $("#cHistorico").html(data);
                    $('#iniciar').removeAttr('disabled');
                    $('#iniciarPausar').attr('disabled', 'disabled');
                    $('#salvarLigacao').attr('disabled', 'disabled');
                    $('#ds_historico').val('');
                    $.unblockUI();
                },
            });
        });

        $("#cParcelas").dialog({
            autoOpen: false,
            width: 800,
            show: {
                effect: "clip",
                duration: 300
            },
            hide: {
                effect: "clip",
                duration: 300
            },
            position: {my: "center", at: "top", of: window},
        });

        $("#cHistorico").dialog({
            autoOpen: false,
            width: 800,
            show: {
                effect: "clip",
                duration: 300
            },
            hide: {
                effect: "clip",
                duration: 300
            },
            position: {my: "center", at: "top", of: window}

        });
        $("#cTelefones").dialog({
            autoOpen: false,
            width: 800,
            show: {
                effect: "clip",
                duration: 300
            },
            hide: {
                effect: "clip",
                duration: 300
            },
            position: {my: "center", at: "top", of: window}

        });

        $("#hParcelas").click(function() {
            $("#cParcelas").dialog("open");
        });
        $("#hHistorico").click(function() {
            $("#cHistorico").dialog("open");
        });
        $("#telefones").click(function() {
            $("#cTelefones").dialog("open");
        });

        $("#GlbQuestionarioRespostaStatusAtendimento").change(function() {
            if ($("#GlbQuestionarioRespostaStatusAtendimento").val() == 1) {
                $(".opcoes").removeAttr('required');
            } else {
                $(".opcoes").attr('required', 'required');

            }

        })

        $("#contratos tr td").click(function() {
            $.blockUI({
                message: "<b>Por Favor, Aguarde!</b>",
                css: {
                    border: 'none',
                    padding: '15px',
                    backgroundColor: '#000',
                    '-webkit-border-radius': '10px',
                    '-moz-border-radius': '10px',
                    opacity: .5,
                    color: '#fff',
                }});

            $.ajax({
                type: "POST",
                url: '<?php echo Router::url(array('controller' => 'VwRcLancCplDetalhes', 'action' => 'listar')); ?>',
                data: {
                    cd_lanc: $('td:first', $(this).parents('tr')).text(),
                    cd_filial: $('td:nth-child(2)', $(this).parents('tr')).text()
                },
                dataType: "html",
                success: function(data) {
                    $("#valorContratos").html(data);
                    $.unblockUI();
                },
            });
        });


    });
</script>
<style>
    .container { border:2px solid #ccc; width:780px; height: 130px; overflow-y: scroll; }
    .coluna{
        width:50px;
        height:10px;
        font-size: 12px;
    }
    .coluna:hover{
        background-color:#eeeeee;
    }
</style>
<div id="menuHorizontal">
    <?php echo "Cliente: " . utf8_encode($cliente['VwQuestionarioCliente']["nome_cliente"]); ?><br>
    <?php echo "Nº Ficha: " . $cliente['VwQuestionarioCliente']["cd_pessoa"]; ?><br>
</div>
<!--Bloco de Parcelas do cliente. Aparece ao clicar em "Parcelas" que está no bloco de Dados do cliente.-->
<div id="cParcelas" title="Parcelas">
    <br>
    <p style="font-size: 10px; color: #008C23">Contratos:</p>
    <div id="contratos" class="container">
        <table id="contratos" style="font-size: 10px;">
            <tr style="font-weight: bold;">
                <td>Contrato</td>
                <td>Lj</td>
                <td>Vlr. Contrato</td>
                <td>Vlr. Pago</td>
                <td>Vlr. Dev.</td>
                <td>Vlr. c/ Juros</td>
                <td>Data Compra</td>
                <td>Tp Compra</td>
            </tr>
            <?php foreach ($contratos as $value) { ?>
                <tr class="coluna">
                    <td><?php echo $value['VwQuestionarioCompras']['cd_lanc']; ?></td>
                    <td><?php echo $value['VwQuestionarioCompras']['cd_filial']; ?></td>
                    <td><?php echo $this->Formatacao->moeda($value['VwQuestionarioCompras']['vlr_lanc']); ?></td>
                    <td><?php echo $this->Formatacao->moeda($value['VwQuestionarioCompras']['vlr_entrada']); ?></td>
                    <td>00,00</td>
                    <td>00,00</td>
                    <td><?php echo $this->Formatacao->dataHora($value['VwQuestionarioCompras']['dt_hr_ped']); ?></td>
                    <td><?php echo $value['VwQuestionarioCompras']['ds_tipo_pgto']; ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
    <br>
    <p style="font-size: 10px; color: #008C23">Parcelas:</p>
    <div id="valorContratos" class="container">
        <table style="font-size: 10px;">
            <tr style="font-weight: bold;">
                <td>Status</td>
                <td>Lj</td>
                <td>Parc</td>
                <td>Dt. Vencimento</td>
                <td>Vlr. Tot</td>
                <td>D. Atr</td>
                <td>Valor Pg</td>
<!--                <td>Valor dev.</td>
                <td>C/ Juros</td>
                <td>Taxa</td>-->
            </tr>
        </table>
    </div>
    <br>
    <p style="font-size: 10px; color: #008C23">Resumo Geral:</p>
    <table style="font-size: 10px;">
        <tr style="font-weight: bold;">
            <td>Qtd. Pago Atrasado</td>
            <td>Vlr. Pago Atrasado</td>
            <td>Qtd. em Atraso</td>
            <td>Vlr. em Atraso</td>
            <td>Maior Atraso</td>
            <td>Total Pago</td>
            <td>Total Juros</td>
        </tr>
        <tr>
            <td><?php echo $parcelas[0]["GlbPessoaCtrVd"]["qtd_parc_atz"]; ?></td>
            <td><?php echo $this->Formatacao->moeda($parcelas[0]["GlbPessoaCtrVd"]["vlr_pgto_atz"], array('negative' => '-')); ?></td>
            <td><?php echo $parcelas[0]["GlbPessoaCtrVd"]["qtd_atz"]; ?></td>
            <td><?php echo $this->Formatacao->moeda($parcelas[0]["GlbPessoaCtrVd"]["vlr_atz"], array('negative' => '-')); ?></td>
            <td><?php echo $this->Formatacao->moeda($parcelas[0]["GlbPessoaCtrVd"]["vlr_maior_atz"], array('negative' => '-')); ?></td>
            <td><?php echo $this->Formatacao->moeda($parcelas[0]["GlbPessoaCtrVd"]["vlr_tot_compras"], array('negative' => '-')); ?></td>
            <td><?php echo $this->Formatacao->moeda($parcelas[0]["GlbPessoaCtrVd"]["vlr_tot_juro"], array('negative' => '-')); ?></td>
        </tr>
        <tr style="font-weight: bold;">
            <td>Qtd. Pago Adiantado</td>
            <td>Vlr. Pago Adiantado</td>
            <td>Qtd. em Aberto</td>
            <td>Vlr. em Aberto</td>
            <td>Vlr. Maior Atraso</td>
            <td>Média de Atraso</td>
            <td>Total Desc</td>
        </tr>
        <tr>
            <td><?php echo $parcelas[0]["GlbPessoaCtrVd"]["qtd_pgto_adiant"]; ?></td>
            <td><?php echo $this->Formatacao->moeda($parcelas[0]["GlbPessoaCtrVd"]["vlr_pgto_adiant"], array('negative' => '-')); ?></td>
            <td><?php echo $parcelas[0]["GlbPessoaCtrVd"]["qtd_parc_aberto"]; ?></td>
            <td><?php echo $this->Formatacao->moeda($parcelas[0]["GlbPessoaCtrVd"]["vlr_parc_aberto"], array('negative' => '-')); ?></td>
            <td><?php echo $this->Formatacao->moeda($parcelas[0]["GlbPessoaCtrVd"]["vlr_maior_atz"], array('negative' => '-')); ?></td>
            <td><?php echo $this->Formatacao->moeda($parcelas[0]["GlbPessoaCtrVd"]["md_atz"], array('negative' => '-')); ?></td>
            <td><?php echo $this->Formatacao->moeda($parcelas[0]["GlbPessoaCtrVd"]["vlr_tot_desconto"], array('negative' => '-')); ?></td>
        </tr>
    </table>
</div>
<!--Fim do bloco de Parcelas-->
<!--Bloco de histórico de atendimento do cliente. Aparece ao clicar em "Histórico de atendimento" que está no bloco de Dados do cliente.-->
<div id="cHistorico" title="Histórico de Atendimento">
    <table style="font-size: 10px;">
        <tr style="font-weight: bold;">
            <td>Protocolo</td>
            <td>Tipo</td>
            <td>Observação</td>
            <td>Data</td>
            <td>Dt. Prev.</td>
            <td>Telefone</td>
        </tr>
        <?php foreach ($historico as $value) { ?>
            <tr>
                <td><?php echo $value["glbQuestionarioRespostaHistorico"]["protocolo"]; ?></td>
                <td><?php echo $this->Html->image($this->Funcionalidades->imagemPesquisa($value["glbQuestionarioRespostaHistorico"]["cd_tipo_atendimento"])); ?></td>
                <td><?php echo $value["glbQuestionarioRespostaHistorico"]["ds_historico"]; ?></td>
                <td><?php echo $this->Funcionalidades->formatarDataAp($value["glbQuestionarioRespostaHistorico"]["dt_cad"]); ?></td>
                <td><?php echo $value["glbQuestionarioRespostaHistorico"]["telefone"]; ?></td>
                <td><?php echo $value["glbQuestionarioRespostaHistorico"]["telefone"]; ?></td>
            </tr>
        <?php } ?>
    </table>
</div>
<!--Fim do bloco Histórico de atendimento-->
<!--Bloco de telefones de contato. Aparece ao clicar em "Todos Telefones" que está no bloco de Dados do cliente.-->
<div id="cTelefones" title="Telefones para Contato">
    <table style="font-size: 10px;">
        <tr style="font-weight: bold;">
            <td></td>
            <td>Telefone</td>
            <td>Tipo</td>
        </tr>
        <?php foreach ($telefones as $fones) { ?>
            <tr>
                <td><input type="radio" name="tel" value="<?php echo $fones["GlbPessoaFone"]["fone"]; ?>" /></td>
                <td><?php echo $fones["GlbPessoaFone"]["fone"]; ?></td>
                <td><?php echo $tiposFones[$fones["GlbPessoaFone"]["tp_fone"]]; ?></td>
            </tr>
        <?php } ?>
    </table>
</div>
<!--Fim do bloco de Telefones-->
<!--Bloco de dados do cliente. (Posicionado do lado esquerdo da tela)-->
<div id="menuslider">
    <div id="navimenu">
        <div id="dadosClientex">
            <b> <?php echo $this->Html->image('dadosCliente.jpg', array('alt' => 'separador', "width" => "30px")); ?>DADOS DO CLIENTE </b><br><br>
            <b>Nome:</b>
            <?php echo utf8_encode($cliente['VwQuestionarioCliente']["nome_cliente"]); ?><br>
            <b>Sexo:</b> 
            <?php echo $cliente['VwQuestionarioCliente']["sexo"]; ?><br>
            <b>Nº Ficha:</b> 
            <?php echo $cliente['VwQuestionarioCliente']["cd_pessoa"]; ?><br>
            <b>Data de Cadastro:</b> 
            <?php echo $this->Funcionalidades->formatarDataAp($cliente['VwQuestionarioCliente']["dt_cadastro_cliente"]); ?><br>
            <b>Idade:</b> 
            <?php echo $cliente['VwQuestionarioCliente']["idade"]; ?><br>
            <b>Nascimento:</b> 
            <?php echo $this->Funcionalidades->formatarDataAp($cliente['VwQuestionarioCliente']["dt_nasc"]); ?><br>
            <b>CPF:</b> 
            <?php echo $cliente['VwQuestionarioCliente']["cpf"]; ?><br>
            <b>Estado Civil:</b> 
            <?php echo utf8_encode($cliente['VwQuestionarioCliente']["estado_civil"]); ?><br>
            <?php echo $this->Html->image('separador.png', array('alt' => 'separador')); ?><br>
            <b><?php echo $this->Html->image('iconeContatos.png', array('alt' => 'separador', "width" => "30px")); ?> CONTATOS </b><br><br>
            <?php echo $this->Html->image('telefones.png', array('alt' => 'separador', "id" => "telefones", "class" => "botoes")); ?><br>
            <?php echo $this->Html->image('separador.png', array('alt' => 'separador')); ?><br>
            <b><?php echo $this->Html->image('iconeEndereco.jpg', array('alt' => 'separador', "width" => "30px")); ?> ENDEREÇO </b><br><br>
            <b>Endereço:</b> 
            <?php echo utf8_encode($cliente['VwQuestionarioCliente']["endereco"]); ?><br>
            <?php echo utf8_encode(@$cliente['VwQuestionarioCliente']["complemento_endereco"]); ?><br>
            <b>Setor:</b> 
            <?php echo utf8_encode($cliente['VwQuestionarioCliente']["ds_bairro"]); ?><br>
            <b>CEP:</b> 
            <?php echo $cliente['VwQuestionarioCliente']["cep"]; ?><br>
            <b>Cidade:</b> 
            <?php echo utf8_encode($cliente['VwQuestionarioCliente']["cidade_mora"]); ?><br>
            <b>Estado:</b> 
            <?php echo $cliente['VwQuestionarioCliente']["uf"]; ?><br>
            <?php echo $this->Html->image('separador.png', array('alt' => 'separador')); ?><br>
            <b> VISUALIZAR </b><br><br>
            <?php echo $this->Html->image('parcelas.png', array('alt' => 'separador', "id" => "hParcelas", "class" => "botoes")); ?><br>
            <?php echo $this->Html->image('historico.png', array('alt' => 'separador', "id" => "hHistorico", "class" => "botoes")); ?><br>
        </div>
    </div>
    <div class="hidemenu"><?php echo $this->Html->image('iconOlho.png', array('alt' => 'Vips')); ?></div>
</div>

<!--Fim do bloco de Dados do cliente.-->
<!--Bloco de atendimento, contém todas as informações sobre o atendimento que está sendo realizado. (Posicionado do lado direito da tela)-->
<div id="tempoAtendimento">
    <?php echo $this->Html->image("atendimento.gif") ?><br>
    Protocolo de Atendimento<br>
    <p style="font-size: 20px;"><?php echo $dados["cd_resposta"] . "/" . $dados["cd_usu_cad"]; ?></p>
    Hora Inicio: <p><?php echo $dados['hora_inicio']; ?></p>
    Tempo de atendimento
    <div id="hms"></div><br>
    <?php echo $this->Html->image('separador.png', array('alt' => 'separador')); ?><br>
    Tempo de Ligação
    <div id="tmpLigacao">00:00:00</div><br>
    Telefone em atendimento:
    <p id="telFone" style="font-size: 20px;"><?php echo $cliente['VwQuestionarioCliente']["fone_celular"]; ?></p>
    <?php echo $this->Form->input('Iniciar Nova Ligação', array("type" => "button", "id" => "iniciar", "label" => false)); ?>
    <?php echo $this->Form->input('Pausar Ligação', array("type" => "button", "id" => "iniciarPausar", "label" => false, "disabled" => "disabled")); ?>
    <?php echo $this->Form->input('Salvar/Encerrar Ligação', array("type" => "button", "id" => "salvarLigacao", "label" => false, "disabled" => "disabled")); ?>
    <?php echo $this->Form->input('ds_historico', array("type" => "textarea", "label" => "Descrição Ligação", "maxlength" => "300", "rows" => 4)); ?>
    <?php echo $this->Form->input('telefone', array("type" => "hidden", "label" => false, "value" => $cliente['VwQuestionarioCliente']["fone_celular"])); ?>
    <?php echo $this->Form->input('cd_tipo_atendimento', array("type" => "hidden", "label" => false, "value" => $dados['tipo_atendimento'])); ?>
    <?php echo $this->Form->input('hr_inicio', array("type" => "hidden", "label" => false)); ?>
</div>
<!--Fim do bloco de atendimento.-->
<!--Bloco de perguntas para o atendimento. (Posicionado no centro da tela)-->
<div class="glbQuestionarioRespostas form">
    <?php
    echo $this->Form->create('GlbQuestionarioResposta');
    ?>

    <fieldset>
        <legend><?php echo __('Atendimento ao Cliente'); ?></legend>
        <?php
        echo "<b>" . $questionario[0]["GlbQuestionario"]["ds_questionario"] . "</b><br><br>";
        echo "<b>" . $questionario[0]["GlbQuestionario"]["obs"] . "</b><br><br>";
        echo "Data de vigência da pesquisa: <b>" . $this->Funcionalidades->formatarDataAp($questionario[0]["GlbQuestionario"]["dt_vigencia_ini"]) . "</b> a <b>" . $this->Funcionalidades->formatarDataAp($questionario[0]["GlbQuestionario"]["dt_vigencia_fim"]) . "</b><br><br><br>";
        echo $this->Form->input('cd_resposta', array("type" => "hidden", "value" => $dados["cd_resposta"]));
        echo $this->Form->input('cd_usu_cad', array("type" => "hidden", "value" => $dados["cd_usu_cad"]));
        echo $this->Form->input('dt_cad', array("type" => "hidden", "value" => $dados["dt_cad"]));
        echo $this->Form->input('hora_inicio', array("type" => "hidden", "value" => $dados["hora_inicio"]));
        echo $this->Form->input('cd_pessoa', array("type" => "hidden", "value" => $cliente['VwQuestionarioCliente']["cd_pessoa"]));
        echo $this->Form->input('cd_questionario', array("type" => "hidden", "value" => $dados["cd_questionario"]));
        echo $this->Form->input('protocolo', array("type" => "hidden", "value" => $dados["protocolo"]));
        echo $this->Form->input('tipo_atendimento', array("type" => "hidden", "value" => $this->params['pass'][1]));

        foreach ($perguntas as $pergunta) {
            $asPerguntas[$pergunta["VwQuestionario"]["cd_pergunta"]]["cod"] = $pergunta["VwQuestionario"]["cd_pergunta"];
            $asPerguntas[$pergunta["VwQuestionario"]["cd_pergunta"]]["pergunta"] = $pergunta["VwQuestionario"]["pergunta"];
            $asPerguntas[$pergunta["VwQuestionario"]["cd_pergunta"]]["tipo"] = $pergunta["VwQuestionario"]["tipo_pergunta"];
            $asPerguntas[$pergunta["VwQuestionario"]["cd_pergunta"]]["cd_pergunta_cpl"] = $pergunta["VwQuestionario"]["cd_pergunta_cpl"];
            $asPerguntas[$pergunta["VwQuestionario"]["cd_pergunta"]][] = $pergunta["VwQuestionario"]["opcoes_pergunta"];
            $asRespostas[$pergunta["VwQuestionario"]["cd_pergunta"]][] = $pergunta["VwQuestionario"]["opcoes_pergunta"];
            $asRespostas[$pergunta["VwQuestionario"]["cd_pergunta"]]["prioridade"][] = $pergunta["VwQuestionario"]["prioridade_respostas"];
            $asRespostas[$pergunta["VwQuestionario"]["cd_pergunta"]]["opcoes"][] = $pergunta["VwQuestionario"]["cd_pergunta_cpl"] . "_" . $pergunta["VwQuestionario"]["opcoes_pergunta"];
        }

        if (!empty($asPerguntas)) {
            if (!isset($asPerguntas[0])) {
                foreach ($asPerguntas as $pergs) {
                    //Cria um array usando um array para chaves e outro para valores
                    $respostas = array_combine($asRespostas[$pergs["cod"]]["prioridade"], $asRespostas[$pergs["cod"]]["opcoes"]);
                    //Função para ordernar um array pela chave
                    ksort($respostas);

                    echo ($pergs["pergunta"]);
                    switch ($pergs["tipo"]) {
                        case(0): //Tipo OPÇÕES
                            /*
                             * Combinando um array com ele mesmo, para a chave ficar com o mesmo valor do value
                             */
                            foreach ($respostas as $resp) {
                                $limparNome = explode("_", $resp);
                                $Opcoes[$resp] = $limparNome[1];
                            }
//                            $Opcoes = array_combine($respostas, $respostas);
                            array_unshift($Opcoes, array("" => 'Selecione'));
                            echo $this->Form->input('pergunta_' . $pergs["cod"] . "_" . $pergs["tipo"] . "_" . $pergs["cd_pergunta_cpl"], array('options' => $Opcoes, 'default' => '0', "label" => false, 'required' => true, 'class' => 'opcoes'));
                            $Opcoes = null;
                            break;
                        case(1): //Tipo DISCURSIVA
                            echo $this->Form->input('pergunta_' . $pergs["cod"] . "_" . $pergs["tipo"] . "_" . $pergs["cd_pergunta_cpl"], array('class' => 'input-texto', "label" => false));
                            break;
                        case(2): //Tipo PONTUAÇÃO
                            $Opcoes = array_combine($respostas, $respostas);
                            echo $this->Form->input('pergunta_' . $pergs["cod"] . "_" . $pergs["tipo"] . "_" . $pergs["cd_pergunta_cpl"], array('class' => 'rating-class', 'div' => "input select rating-c", 'options' => $Opcoes, 'default' => '0', 'label' => false));
                            $Opcoes = null;
                            break;
                        case(3): //Tipo SUGESTÃO
                            echo $this->Form->input('pergunta_' . $pergs["cod"] . "_" . $pergs["tipo"] . "_" . $pergs["cd_pergunta_cpl"], array('class' => 'input-texto', "label" => false));
                            break;
                    }
                }
            }
            echo $this->Form->input('status_atendimento', array('options' => array("" => 'Selecione', 1 => "Sem Contato", 2 => "Concluído"), 'label' => "O Atendimento foi finalizado:", 'required' => true));
            echo $this->Form->input('status_finalizar', array("type" => "checkbox", "value" => 1, "label" => 'Continuar atendendo', 'default' => 1));
            echo "</fieldset>";
            if ($dados['tipo_atendimento'] == 1) {
                $atendidos = explode(" ", $atendidos[0]['VwQuestionarioProxAtendimentoAniversarianteAtendido']['resultado']);
                echo "<b>Total de Aniversariantes:</b> " . $atendidos[2] . " | <b>Total Atendido(s):</b> " . $atendidos[0] . "<br><br>";
            } else {
                $atendidos = explode(" ", $atendidos[0]['VwQuestionarioProxAtendimentoQtdeAtendimento']['resultado']);
                echo "<b>Total de clientes:</b> " . $atendidos[2] . " | <b>Total Atendido(s):</b> " . $atendidos[0] . "<br><br>";
            }
            echo $this->Form->end(__('Encerrar Atendimento'));
        } else {
            echo "<b>Formulário sem perguntas relacionadas.</b><br><br><br>";
            ?>
            <div class="actions">
                <?php echo $this->Html->link(__('Ad Perguntas'), array('controller' => 'GlbQuestionarioGlbQuestionarioPerguntas', 'action' => 'add', $this->params["pass"][0])); ?>
            </div>
            <?php
        }
        ?>
</div>
<!--Fim das perguntas do atendimento-->
