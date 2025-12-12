<!DOCTYPE html>
<html lang="pt">
    <head>
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
		<link rel="apple-touch-startup-image" href="http://i.imgur.com/aXmBYJc.png">
		<meta name="msapplication-TileColor" content="#2b5797">
		<meta name="msapplication-TileImage" content="http://i.imgur.com/K2CxhI6.png">
		<meta name="msapplication-config" content="http://127.0.0.1/SysApp/browserconfig.xml">
		<meta name="theme-color" content="#ffffff">
    	
        <meta http-equiv="X-UA-Compatible" content="IE=edge; IE=9; IE=8; chrome=1">
        <meta name="HandheldFriendly" content="true"/> 
		<meta name="MobileOptimized" content="240">
        <meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="mobile-web-app-capable" content="yes">
		<meta name="viewport" content="width=device-width, initial-scale=0.7, maximum-scale=1, user-scalable=no">
        <?php 
        echo $this->Html->charset(); ?>
        <title>
            Systec App
        </title>
        <?php
                        
        echo $this->Html->css('login');

        echo $this->fetch('meta');
        echo $this->fetch('css');
        echo $this->fetch('script');
        
        echo $this->Html->css('jquery-ui-1.10.3.custom');
        echo $this->Html->script('jquery-1.10.2.min.js');
        echo $this->Html->script('jquery-ui.js');
        echo $this->Html->script('jquery.blockUI.js');
        echo $this->Html->script('jquery.filter_input');
        echo $this->Html->script('jqsimplemenu');
        ?>
        <script type="text/javascript">
		     // Listen for ALL links at the top level of the document. For
		     // testing purposes, we're not going to worry about LOCAL vs.
		     // EXTERNAL links - we'll just demonstrate the feature.
		     $( document ).on(
		         "click",
		         "a",
		         function( event ){
		
		             // Stop the default behavior of the browser, which
		             // is to change the URL of the page.
		             event.preventDefault();
		console.log(event);
		             // Manually change the location of the page to stay in
		             // "Standalone" mode and change the URL at the same time.
		             location.href = $( event.target ).attr( "href" );
		
		         }
		     );
        </script>
    </head>
    <body>
        <?php echo $this->fetch('content'); ?>
    </body>
</html>
