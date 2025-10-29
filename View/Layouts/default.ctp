<?php

/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
?>
<!DOCTYPE html>
<html>
    <head>
        <?php echo $this->Html->charset(); ?>
        <link rel="apple-touch-icon" sizes="57x57" href="http://i.imgur.com/3clUBPf.png">
        <link rel="apple-touch-icon" sizes="60x60" href="http://i.imgur.com/eUltSsc.png">
        <link rel="apple-touch-icon" sizes="72x72" href="http://i.imgur.com/0yB9YHX.png">
        <link rel="apple-touch-icon" sizes="76x76" href="http://i.imgur.com/8fitK9n.png">
        <link rel="apple-touch-icon" sizes="114x114" href="http://i.imgur.com/9go9oGT.png">
        <link rel="apple-touch-icon" sizes="120x120" href="http://i.imgur.com/femWjJf.png">
        <link rel="apple-touch-icon" sizes="144x144" href="http://i.imgur.com/uGT9O4K.png">
        <link rel="apple-touch-icon" sizes="152x152" href="http://i.imgur.com/lXVQPeA.png">
        <link rel="apple-touch-icon" sizes="180x180" href="http://i.imgur.com/4z3W7WZ.png">
        <link rel="icon" type="image/png" href="http://i.imgur.com/gtFtdwk.png" sizes="32x32">
        <link rel="icon" type="image/png" href="http://i.imgur.com/aXmBYJc.png" sizes="192x192">
        <link rel="icon" type="image/png" href="http://i.imgur.com/c7xz3E3.png" sizes="96x96">
        <link rel="icon" type="image/png" href="http://i.imgur.com/XM3dsSr.png" sizes="16x16">
        <link rel="manifest" href="http://127.0.0.1/SysApp/manifest.json">
        <link rel="shortcut icon" href="http://www.iconj.com/ico/t/3/t3c14scn7l.ico" type="image/x-icon">
        <link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/themes/south-street/jquery-ui.css"> 
        <meta name="msapplication-TileColor" content="#2b5797">
        <meta name="msapplication-TileImage" content="http://i.imgur.com/K2CxhI6.png">
        <meta name="msapplication-config" content="http://127.0.0.1/SysApp/browserconfig.xml">
        <meta name="theme-color" content="#ffffff">

        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="viewport" content="width=min-device-width, initial-scale=0.7, maximum-scale=1, user-scalable=no">
        <title>
            Systec App
        </title>

        <?php
        echo $this->Html->css('bootstrap.min');
        echo $this->Html->css('bootstrap-theme.min');
        echo $this->Html->css('style-horizontal');
        echo $this->Html->css('rating');
        echo $this->Html->css('jquery-ui-1.10.3.custom');
        echo $this->Html->css('cake.generic');
        echo $this->Html->css('jqsimplemenu');


        echo $this->fetch('meta');
        echo $this->fetch('css');
        echo $this->fetch('script');

        echo $this->Html->script('jquery-1.11.3.js');
        echo $this->Html->script('jquery.barrating.js');
        echo $this->Html->script('jquery-ui.js');
        echo $this->Html->script('jquery.blockUI.js');
        echo $this->Html->script('jquery.countdown.js');
        echo $this->Html->script('jquery.alphanumeric.js');
        echo $this->Html->script('jquery.limit-1.2.source');
        echo $this->Html->script('jquery.maskMoney');
        echo $this->Html->script('jquery.filter_input');
        //echo $this->Html->script('jqsimplemenu');
        echo $this->Html->script('bootstrap.min.js');
        
        ?> 
        <script type="text/javascript">

            function navigator_Go(url) {
                window.location.assign(url);
            }
        </script>


        <style>
            .ui-tooltip, .arrow:after {
                background: black;
                border: 2px solid white;
            }
            .ui-tooltip {
                padding: 10px 20px;
                color: white;
                border-radius: 20px;
                font: bold 14px "Helvetica Neue", Sans-Serif;
                text-transform: uppercase;
                box-shadow: 0 0 7px black;
            }
            .arrow {
                width: 70px;
                height: 16px;
                overflow: hidden;
                position: absolute;
                left: 50%;
                margin-left: -35px;
                bottom: -16px;
            }
            .arrow.top {
                top: -16px;
                bottom: auto;
            }
            .arrow.left {
                left: 20%;
            }
            .arrow:after {
                content: "";
                position: absolute;
                left: 20px;
                top: -20px;
                width: 25px;
                height: 25px;
                box-shadow: 6px 5px 9px -9px black;
                -webkit-transform: rotate(45deg);
                -moz-transform: rotate(45deg);
                -ms-transform: rotate(45deg);
                -o-transform: rotate(45deg);
                transform: rotate(45deg);
            }
            .arrow.top:after {
                bottom: -20px;
                top: auto;
            }
            .navbar-brand{
                margin-top: -8px;
            }
            li:hover{
                background-color: #DADADA;
            }
        </style>

    </head>

    <body>
        <?php
        //Uso do Autocomplete
        $cbunny = array(
            'APP_PATH' => Router::url('/', true)
        );
        echo $this->Html->scriptBlock('var CbunnyObj = ' . $this->Js->object($cbunny) . ';');
        //Fim Autocomplete
        ?>
        <div id="header">
            <h1>&nbsp;</h1>
        </div>
        <?php
        if ($this->params['action'] != 'modulos' && $this->params['action'] != 'atendimento') {
            ?>
        <div class="div-menu" style="margin-bottom: -18px;">
            <nav class="navbar navbar-default">
                <div class="container-fluid">
                    <!-- Brand and toggle get grouped for better mobile display -->
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="#">
                            <img alt="SysApp" src="/SysApp/android-chrome-36x36.png">
                        </a>
                    </div>

                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <ul class="nav navbar-nav">
                            <li><a href="javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/')">HOME <span class="sr-only"></span></a></li>
				        <?php 
							if($this->Session->check('Dados')){
						?>
                            <li><a href="javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/empresa')">EMPRESA</a></li>
				        <?php
							}
						?>
                            <li><a href="javascript:navigator_Go('/SysApp/app/webroot/index.php/Usuarios/change_password')">TROCAR SENHA</a></li>

				        <?php 
							if($this->Session->read('Questionarios.cd_usu') == '1')
						{?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">PAINEL ADMINISTRADOR <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="javascript:navigator_Go('/SysApp/app/webroot/index.php/Usuarios/novo_usuario')">NOVO USU&Aacute;RIO</a></li>
                                    <li><a href="javascript:navigator_Go('/SysApp/app/webroot/index.php/Usuarios/visualizar')">LISTAR USU&Aacute;RIOS</a></li>
                                    <li><a href="javascript:navigator_Go('/SysApp/app/webroot/index.php/Usuarios/adiciona_database')">ADICIONAR DATABASE</a></li>
                                    <li><a href="javascript:navigator_Go('/SysApp/app/webroot/index.php/Usuarios/listar_database')">MANUTEN&Ccedil;&Atilde;O DATABASE</a></li>
                                    <li><a href="javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/conexoes')">CONEX&Otilde;ES ATIVAS</a></li>
                                    <li><a href="javascript:navigator_Go('/SysApp/app/webroot/index.php/Relatorios/controle_emails_informativo')">EMAILS PARA INFORMATIVO</a></li>
                                </ul>
                            </li>
				        <?php
				        }?>

                        </ul>
                        <ul class="nav navbar-nav navbar-right">
                            <li><a href="javascript:navigator_Go('/SysApp/app/webroot/index.php/Usuarios/logout')">SAIR</a></li>
                        </ul>
                    </div><!-- /.navbar-collapse -->
                </div><!-- /.container-fluid -->
            </nav>
        </div>
  		<?php } ?> 


        <div id="content">
            <?php echo $this->Session->flash(); ?>
            <?php echo $this->fetch('content'); ?>
            <div id="logado">
                <font size="1"><b>Usu&aacute;rio:</b>&nbsp;<?php echo $this->Session->read('Questionarios.nm_usu'); ?> &nbsp;<br><b>Login em:</b>&nbsp;<?php echo $this->Session->read('Questionarios.hora_login'); ?></font><br>
            </div>
        </div>
        <div id="footer">
            <b><h2 style="color:black">© 2015-2016 Systec - Intelig&ecirc;ncia da Informa&ccedil;&atilde;o</h2></b><br>
            <font size="1"> Telefone: (62) 3932-9946</font><br>
            <a href="http://www.systecinfo.com.br" target="_blank"><font size="2">www.systecinfo.com.br</font></a> | <a href="mailto:contato@systecinfo.com.br"><font size="2">contato@systecinfo.com.br</font></a><br>
        </div>
        <?php // echo $this->element('sql_dump');    ?>

    </body>

</html>
