<!doctype html>
<html>
<head>
    <title><?php echo $page; ?></title>
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta charset="UTF-8"/>
    <meta name="HandheldFriendly" content="true"/><!--Blackberry Column View-->
    <meta name="viewport" content="initial-scale=1.0"/><!--iPod-->
    <meta name="viewport" content="width=device-width"/><!--android-->

    <link rel="stylesheet" href="/theme/<?php echo $theme; ?>/mobile.css" type="text/css" media="only screen and (max-width : 39em)"/>
    <!--[if lt IE 9]>
    <link rel="stylesheet" media="all" type="text/css" href="/theme/<?php echo $theme; ?>/main.css"/>
    <![endif]-->
    <link rel="stylesheet" href="/theme/<?php echo $theme; ?>/main.css" type="text/css" media="only screen and (min-width : 39em)"/>

    <!--<link href="/theme/images/logo57.png" rel="apple-touch-icon"/>-->
    
 </head>

<body>


<div id="Banner">
 <a href="#Menu">Skip to Menu</a>
<h1><a href="/">BillCreswell.com</a></h1>  
</div>

<div id="Content">
    <?php echo getContent($page); ?>
</div>

<div id="Menu" role="navigation">
     <?php echo getMenuList(); ?>
     <br style="clear:both"/>
</div>

<div id="Footer">
Powered by <a href="https://github.com/billcreswell/filecms">filecms</a>
        Network: <a href="http://captionwire.com">Captionwire</a>
    <a href="http://grcomputerworks.com">GrComputerWorks</a>
    <a href="http://grwebguy.net">GrWebGuy.net</a>
    <a href="http://twitter.com/grwebguy">Twitter</a>
    <a href="http://www.arvixe.com" target="_blank">Hosted By Arvixe</a>
</div>
</body>
</html>
