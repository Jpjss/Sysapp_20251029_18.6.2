<div class="glbQuestionarioRespostas view">
    <h2><?php echo __('Atendimento'); ?></h2>
    <dl>
        <dt><?php echo __('Protocolo'); ?></dt>
        <dd>
            <?php echo h($glbQuestionarioResposta['GlbQuestionarioResposta']['protocolo']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Pesquisa'); ?></dt>
        <dd>
            <?php echo h($questionario['glbQuestionario']['ds_questionario']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Cliente'); ?></dt>
        <dd>
            <?php echo h($cliente['glbPessoa']['nm_pessoa']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Atendente'); ?></dt>
        <dd>
            <?php echo h($usuario['Usuario']['nm_usu']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Hora Inicio'); ?></dt>
        <dd>
            <?php echo h($glbQuestionarioResposta['GlbQuestionarioResposta']['hora_inicio']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Hora Fim'); ?></dt>
        <dd>
            <?php echo h($glbQuestionarioResposta['GlbQuestionarioResposta']['hora_fim']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Data'); ?></dt>
        <dd>
            <?php echo substr($this->Formatacao->dataCompleta($glbQuestionarioResposta['GlbQuestionarioResposta']['dt_cad']), 0, -10); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Status'); ?></dt>
        <dd>
            <?php
            switch ($glbQuestionarioResposta['GlbQuestionarioResposta']['status_atendimento']) {
                case(0):
                    echo "Iniciado";
                    break;
                case(1):
                    echo "<span style='color: #BE2524;'><b>Sem Contato</b></span>";
                    break;
                case(2):
                    echo "<span style='color: #31B956;'><b>Concluído</b></span>";
                    break;
            }
            ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Ligações'); ?></dt>
        <dd>
            <?php
            foreach ($questionarioHistorico as $value) {
                echo $this->Formatacao->data($value['glbQuestionarioRespostaHistorico']['dt_cad']) . ' - ' . $value['glbQuestionarioRespostaHistorico']['ds_historico'] ."<br>";
            }
            ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Respostas'); ?></dt>
        <dd>
            <table>
                <?php foreach ($respostas as $value) { ?>
                    <tr>
                        <td style="font-size: 12px; font-weight: bold;">• <?php echo $value['qPergunta']['ds_pergunta']; ?></td>
                    </tr>  
                    <tr>
                        <td style="font-size: 12px; color: #BE2524;">
                             <?php
                            
                            if ($value['qPergunta']['tp_pergunta'] == 2) {
                                $qtd = $pergCpl[$value['qPergunta']['cd_pergunta']] / 3;
                                for ($i = 0; $i <= $pergCpl[$value['qPergunta']['cd_pergunta']]; $i++) {
                                    if ($i < round($qtd) && $i <= $value['glbQuestionarioRespostaCpl']['cd_pergunta_cpl']) {
                                        echo '<div class="estrelaVermelha">' . $i . '</div>';
                                    } else if($i >= round($qtd) && $i <= round($qtd)*2 && $i <= $value['glbQuestionarioRespostaCpl']['cd_pergunta_cpl']) {
                                        echo '<div class="estrelaAmarela">' . $i . '</div>';
                                    } else if($i >= round($qtd)*2 && $i <= $value['glbQuestionarioRespostaCpl']['cd_pergunta_cpl']) {
                                        echo '<div class="estrelaVerde">' . $i . '</div>';
                                    } else if($i > $value['glbQuestionarioRespostaCpl']['cd_pergunta_cpl']) {
                                        echo '<div class="estrelaNaotem">' . $i . '</div>';
                                    }
                                }
                            } else {
                                echo $value['glbQuestionarioRespostaCpl']['ds_resposta'];
                            }
                            ?>

                        </td>
                    </tr>  
                <?php } ?>
            </table>
        </dd>

    </dl>
    <br>
    <div class="actions">
        <?php echo $this->Html->link(__('Voltar'), array('action' => 'index')); ?>
        <?php // echo $this->Html->link(__('Edit'), array('action' => 'edit', $glbQuestionario['GlbQuestionario']['cd_questionario'])); ?>
    </div>
</div>
