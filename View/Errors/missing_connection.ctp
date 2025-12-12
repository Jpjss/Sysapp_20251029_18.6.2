<h2><?php echo $name; ?></h2>
<p class="error">
    <strong>
        <?php echo __d('cake', 'Error'); ?>: 
    </strong>
    <?php
    //$s é necessario para passar qual sera a variavel do mesmo. Funciona como % do raise notice do postgresql
    $erroMessage = utf8_encode($erroMessage);
    
    printf(__d('cake', 'Problema de conex&atilde;o encontrado! Erro: %s '), "<strong>'{$erroMessage}'</strong>"
    );
    ?>
</p>
<?php
if (Configure::read('debug') > 0):
    echo $this->element('exception_stack_trace');
endif;
?>
