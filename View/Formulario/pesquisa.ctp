<div class="glbParmPergs form">
    <?php
    echo $this->Html->script('jquery-1.10.2.min.js');
    echo $this->Html->script('jquery.barrating.js');
    echo $this->Html->script('jquery.alphanumeric.js');
    echo $this->Html->css('rating');
    echo $this->Form->create('GlbParmPerg');
    ?>
    <script type="text/javascript">
        $(function() {
            $('.input-texto').alphanumeric({allow:"., "});
            $('.rating-class').barrating('show', {
                showValues: true,
                showSelectedRating: false
            });
        });
    </script>
    <fieldset>
        <legend><?php echo __('Formulário'); ?></legend>
        <?php
        $i = 0;
       
        foreach ($perguntas as $pergunta) {
            echo $pergunta["GlbParmPerg"]["ds_perg"];

            switch ($pergunta["GlbParmPerg"]["tp_perg"]) {
                case(0):
//                    $ds_perg_cpl = array_keys($valores["cd_perg"], $pergunta["GlbParmPerg"]["cd_perg"]);
//                    $i = 0;
//                    foreach ($ds_perg_cpl as $ds) {
//                        $opcoes[$i] = $valores["ds_perg_cpl"][$ds];
//                        $i++;
//                    }
//                    sort($opcoes);
                        $opcoes = "";
                    echo $this->Form->input('tp_perg', array('options' => $opcoes, 'default' => '0', "label" => ""));
                    break;
                case(1):
                    echo $this->Form->input('ds_perg', array('class' => 'input-texto',"label" => ""));
                    break;
                case(2):
//                    $ds_perg_cpl = array_keys($valores["cd_perg"], $pergunta["GlbParmPerg"]["cd_perg"]);
//                    $i = 1;
//                    foreach ($ds_perg_cpl as $ds) {
//                        $opcoes[$i] = $valores["ds_perg_cpl"][$ds];
//                        $i++;
//                    }
//                    sort($opcoes);
                    $opcoes = "";
                    echo $this->Form->input('tp_perg', array('class' => 'rating-class', 'div' => "input select rating-c", 'options' => $opcoes, 'default' => '0', 'label' => ""));
                    break;
            }
        }
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
    <h3><?php echo __('Actions'); ?></h3>
    <ul>

        <li><?php echo $this->Html->link(__('Listar Perguntas'), array('action' => 'index')); ?></li>

    </ul>
</div>
