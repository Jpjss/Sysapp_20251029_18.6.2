<div id="tempoAtendimento">
    <?php echo $this->Html->image("atendimento.gif") ?><br>
    Tempo de atendimento
    <div id="hms"></div>
</div>
<div class="glbQuestionarioRespostas form">
    <?php
//    echo $this->Html->script('jquery-1.10.2.min.js');
    echo $this->Html->script('jquery.barrating.js');
    echo $this->Html->script('jquery.countdown.js');
    echo $this->Html->script('jquery.alphanumeric.js');
    echo $this->Html->css('rating');
    echo $this->Form->create('GlbQuestionarioResposta');
    ?>
    <script type="text/javascript">
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


        });
    </script>
    <fieldset>
        <legend><?php echo __('Atendimento Ao Cliente'); ?></legend>
        <?php
        echo "<b>" . $questionario[0]["GlbQuestionario"]["ds_questionario"] . "</b><br>";
        echo $questionario[0]["GlbQuestionario"]["obs"] . "<br>";
        echo "Data de vigência da pesquisa: <b>" . $this->Funcionalidades->formatarDataAp($questionario[0]["GlbQuestionario"]["dt_vigencia_ini"]) . "</b> a <b>" . $this->Funcionalidades->formatarDataAp($questionario[0]["GlbQuestionario"]["dt_vigencia_fim"]) . "</b><br><br><br>";
        echo $this->Form->input('hora_inicio', array("type" => "hidden"));
        echo $this->Form->input('hora_fim', array("type" => "hidden"));
        echo $this->Form->input('dt_cad', array("type" => "hidden", "value" => date("Y-m-d")));
        echo $this->Form->input('cd_pessoa', array("type" => "hidden", "value" => 88));
        echo $this->Form->input('cd_questionario', array("type" => "hidden", "value" => $this->params["pass"][0]));
        foreach ($perguntas as $pergunta) {
            $asPerguntas[$pergunta["VwQuestionario"]["cd_pergunta"]]["cod"] = $pergunta["VwQuestionario"]["cd_pergunta"];
            $asPerguntas[$pergunta["VwQuestionario"]["cd_pergunta"]]["pergunta"] = $pergunta["VwQuestionario"]["pergunta"];
            $asPerguntas[$pergunta["VwQuestionario"]["cd_pergunta"]]["tipo"] = $pergunta["VwQuestionario"]["tipo_pergunta"];
            $asPerguntas[$pergunta["VwQuestionario"]["cd_pergunta"]]["cd_pergunta_cpl"] = $pergunta["VwQuestionario"]["cd_pergunta_cpl"];
            $asPerguntas[$pergunta["VwQuestionario"]["cd_pergunta"]][] = $pergunta["VwQuestionario"]["opcoes_pergunta"];
            $asRespostas[$pergunta["VwQuestionario"]["cd_pergunta"]][] = $pergunta["VwQuestionario"]["opcoes_pergunta"];
            $asRespostas[$pergunta["VwQuestionario"]["cd_pergunta"]]["prioridade"][] = $pergunta["VwQuestionario"]["prioridade_respostas"];
            $asRespostas[$pergunta["VwQuestionario"]["cd_pergunta"]]["opcoes"][] = $pergunta["VwQuestionario"]["opcoes_pergunta"];
        }
        if (!empty($asPerguntas)) {
            foreach ($asPerguntas as $pergs) {
                $respostas = array_combine($asRespostas[$pergs["cod"]]["prioridade"], $asRespostas[$pergs["cod"]]["opcoes"]);
                ksort($respostas);
                echo $pergs["pergunta"];
                switch ($pergs["tipo"]) {
                    case(0):
                        $Opcoes = array_combine($respostas, $respostas);
                        echo $this->Form->input('pergunta_' . $pergs["cod"] . "_" . $pergs["tipo"] . "_" . $pergs["cd_pergunta_cpl"], array('options' => $Opcoes, 'default' => '0', "label" => false));
                        $Opcoes = null;
                        $opcoes = null;
                        break;
                    case(1):
                        echo $this->Form->input('pergunta_' . $pergs["cod"] . "_" . $pergs["tipo"] . "_" . $pergs["cd_pergunta_cpl"], array('class' => 'input-texto', "label" => false));
                        break;
                    case(2):
                        $Opcoes = array_combine($respostas, $respostas);
                        echo $this->Form->input('pergunta_' . $pergs["cod"] . "_" . $pergs["tipo"] . "_" . $pergs["cd_pergunta_cpl"], array('class' => 'rating-class', 'div' => "input select rating-c", 'options' => $Opcoes, 'default' => '0', 'label' => false));
                        $Opcoes = null;
                        $opcoes = null;
                        break;
                }
            }
            echo $this->Form->input('status_atendimento', array('options' => array(1 => "Sem Contato", 2 => "Concluído"), 'default' => '0', 'label' => "O Atendimento foi finalizado:", 'empty' => "Selecione"));
            echo "</fieldset>";
            echo $this->Form->end(__('Submit'));
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
