<?php include_once 'config.php';?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width">

        <link rel="stylesheet" href="<?php print URL;?>css/bootstrap.css">
        <style>
            body {
                padding-top: 60px;
                padding-bottom: 40px;
            }
        </style>
        <link rel="stylesheet" href="<?php print URL;?>css/bootstrap-responsive.min.css">
        <link rel="stylesheet" href="<?php print URL;?>css/smoothness/jquery-ui-1.8.22.custom.css">
        <link rel="stylesheet" href="<?php print URL;?>css/main.css">
        
        <script src="<?php print URL;?>js/jquery-1.8.2.js" type="text/javascript"></script>
        <script src="<?php print URL;?>js/jquery.validate.min.js" type="text/javascript"></script>
    
        <script type="text/javascript" src="<?php print URL;?>js/jquery-ui-1.8.16.custom.min.js"></script>
        <script type="text/javascript" src="<?php print URL;?>js/ui/jquery.ui.datepicker.js"></script>
        <script type="text/javascript" src="<?php print URL;?>js/ui/i18n/jquery.ui.datepicker-es.js"></script>

        <script src="<?php print URL;?>js/modernizr-2.6.1-respond-1.1.0.min.js"></script>
    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="chromeframe">You are using an outdated browser. <a href="http://browsehappy.com/">Upgrade your browser today</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to better experience this site.</p>
        <![endif]-->

        <!-- This code is taken from http://twitter.github.com/bootstrap/examples/hero.html -->

        <div class="navbar navbar-inverse navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container">
                    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </a>
                    <a class="brand" href="#"><?php print NOMBRE ?></a>
                    <div class="nav-collapse collapse">
                        <ul class="nav">
                            <li class="active"><a href="#">inicio</a></li>
                            <li><a href="<?php print URL;?>menu.php">menu</a></li>
                            <li><a href="#contact">contacto</a></li>
                            
                        </ul>
                        <form class="navbar-form pull-right">
                            <input class="span2" type="text" placeholder="Usuario">
                            <input class="span2" type="password" placeholder="clave">
                            <button type="submit" class="btn">Ingresar</button>
                        </form>
                    </div><!--/.nav-collapse -->
                </div>
            </div>
        </div>

        <div class="container">

            
            <div class="hero-unit">
                <div class="row well">
                               
               
