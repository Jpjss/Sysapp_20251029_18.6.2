<?php
echo $this->Html->link(
    '<div class="moduloQuestionario">' . $this->Html->image('iconQuestionario.png', array('alt' => 'questionarios', "width" => "140px")) . '<br>Atendimento</div>', 
    array('controller' => 'GlbQuestionarioRespostas', 'action' => 'atender'),array('escape' => false));

echo $this->Html->link(
    '<div class="moduloRelatorio">' . $this->Html->image('iconRelatorios.png', array('alt' => 'relatorios', "width" => "122px")) . '<br>Relatórios</div>', 
    array('controller' => 'Relatorios', 'action' => 'index'),array('escape' => false));
?>
