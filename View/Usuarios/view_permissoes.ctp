<div class="glbQuestionarios view">
    <h2><?php echo __('Controle de acesso'); ?></h2>
    <dl>
        <dt><?php echo __('Usuário'); ?></dt>
        <dd>
            <?php echo $usuario['Usuario']['nm_usu']; ?>
            &nbsp;
        </dd>
        <dt>Permissões</dt>
        <?php foreach($permissoes as $value){ ?>
        <dt>&nbsp;</dt>
        <dd>
            <?php echo $nomesPermissoes[$value[0]['interface']];  ?>
            &nbsp;
        </dd>
        <?php } ?>
    </dl>
    <div class="actions" style="width: 500px;">
        <?php echo $this->Html->link(__('Edit'), array('action' => 'editPermissoes', $usuario['Usuario']['cd_usu'])); ?>
        <?php echo $this->Html->link(__('Voltar'), array('action' => 'permissoes')); ?>
    </div>
</div>
