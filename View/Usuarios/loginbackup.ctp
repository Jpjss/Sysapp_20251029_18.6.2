<?php 
echo $this->Html->css('login');
?>
<script type="text/javascript">
    $(document).ready(function() {
        $("#UsuariosCdUsu").focus();
    });
</script>
<div class="login">
    <?php echo $this->Form->create('Usuarios'); ?>
    <?php echo $this->Html->image("login.png", array("border" => "none", "alt" => "Systec Inteligência da Informação")); ?>
    <br><br>
    <div class="error">
        <?php echo $this->Session->flash(); ?>
    </div>
    <div id="simplificado">
        <?php echo $this->Form->input('email', array('label' => 'Usuário:', 'placeholder' => 'E-mail')); ?>
        <br>
        <?php echo $this->Form->input('senha', array('label' => '&nbsp;&nbsp;Senha:', 'type' => 'password')); ?>
        <br>
    </div>
    <?php echo $this->Form->end(__('Entrar')); ?>
    <br>
    <div style="bottom: 0px; font-size: 9px; font-weight: bold; color: #838383; cursor: pointer;" id="versao">Versão Mobile Beta</div>
</div>
