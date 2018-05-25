<!DOCTYPE html>
<html lang="es">
    <head>
        <title>MediaFem para Anunciantes</title>

        <meta charset="utf-8">

        <link href="<?= base_url() ?>css/stylesheet.css?20131003" rel="stylesheet" type="text/css" />

        <!-- jQuery -->
        <script src="<?= base_url() ?>js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <!-- Tabs (Pestañas) -->
        <script src="<?= base_url() ?>js/idTabs.min.js" type="text/javascript"></script>
        <!-- dataTables -->
        <script src="<?= base_url() ?>js/dataTables.min.js" type="text/javascript"></script>
        <!-- notificaciones -->
        <script src="<?= base_url() ?>js/noty/jquery.noty.js" type="text/javascript"></script>
        <script src="<?= base_url() ?>js/noty/layouts/inline.js" type="text/javascript"></script>
        <script src="<?= base_url() ?>js/noty/themes/default.js" type="text/javascript"></script>

        <script src="<?= base_url() ?>js/jquery.reveal.js" type="text/javascript"></script>

        <!-- tiny_mce -->
        <script src="<?= base_url() ?>js/tiny_mce/tiny_mce.js" type="text/javascript"></script>

        <!-- Highcharts
        <script src="<?= base_url() ?>js/Highcharts/highcharts.js"></script>
        <script src="<?= base_url() ?>js/Highcharts/modules/exporting.js"></script>-->
        <script src="<?= base_url() ?>js/highstock.js"></script>
        <script src="<?= base_url() ?>js/exporting.js"></script>


        <!-- COMPROBAR Y MEJORAR -->
        <script src="<?= base_url() ?>js/jquery.validate.js" type="text/javascript"></script>

        <script src="<?= base_url() ?>js/jquery-ui.min.js" type="text/javascript" charset="utf-8"></script>
        <script src="<?= base_url() ?>js/jquery.blockUI.js" type="text/javascript" charset="utf-8"></script>

        <link href="<?= base_url() ?>css/jquery-ui.css" rel="stylesheet" type="text/css" />

        <link rel="stylesheet" href="<?= base_url() ?>js/jquery.ui.plupload/css/jquery.ui.plupload.css" type="text/css" media="screen" />

        <script type="text/javascript" src="<?= base_url() ?>js/browserplus-min.js"></script>
        <!--
        <script type="text/javascript" src="<?= base_url() ?>js/plupload.js"></script>
        <script type="text/javascript" src="<?= base_url() ?>js/plupload.gears.js"></script>
        <script type="text/javascript" src="<?= base_url() ?>js/plupload.silverlight.js"></script>
        <script type="text/javascript" src="<?= base_url() ?>js/plupload.flash.js"></script>
        <script type="text/javascript" src="<?= base_url() ?>js/plupload.browserplus.js"></script>
        <script type="text/javascript" src="<?= base_url() ?>js/plupload.html4.js"></script>
        <script type="text/javascript" src="<?= base_url() ?>js/plupload.html5.js"></script>
        -->
        <script type="text/javascript" src="<?= base_url() ?>js/jquery.plupload.queue/jquery.plupload.queue.js"></script>

        <script type="text/javascript" src="<?= base_url() ?>js/plupload.full.js"></script>
        <script type="text/javascript" src="<?= base_url() ?>js/jquery.ui.plupload/jquery.ui.plupload.js"></script>


        <script type="text/javascript" src="<?= base_url() ?>js/jquery.fancybox-1.3.4.pack.js"></script>


        <!-- Funciones generales del tema -->
        <script src="<?= base_url() ?>js/theme_functions.js" type="text/javascript"></script>
        <script src="<?= base_url() ?>js/subMenu.js" type="text/javascript"></script>

        <link rel="shortcut icon" href="https://mediafem.com/img/favico.jpg" />

        <!-- html5.js - IE  9 -->
        <!--[if lt IE 9]>
            <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->

        <!-- css3-mediaqueries.js for IE - 9 -->
        <!--[if lt IE 9]>
            <script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
        <![endif]-->
    </head>

    <body>
        <header>
            <div class="container">
                <a href="<?= base_url() ?>" class="floatLeft"><img src="images/logo.png" alt="MediaFem" /></a>

                <a href="#" id="user_nav" data-menu="on" data-menu-name="user_menu"><?= $this->nombre_usuario ?></a>

                <div class="submenu_right" id="user_menu">
                    <ul>
                        <li><a href="/micuenta" class="icon icon_user">Mi cuenta</a></li>
                        <li><a href="/auth/logout" class="icon icon_logout">Cerrar sesi&oacute;n</a></li>
                    </ul>
                </div> <!-- end header #user_menu -->

            </div> <!-- end header .container -->

            <div class="clear"></div>
        </header>

        <nav>
            <?php
            $solapa = $_SERVER['REQUEST_URI'];

            if(stristr($solapa, 'campania'))
                $solapa = 'campania';

            if(stristr($solapa, 'inventario'))
                $solapa = 'inventario';
            ?>

            <div class="container">
                <ul>
                    <li <?php if($solapa == 'campania'){ ?> class="active" <?php } ?>><a href="/campania">Campa&ntilde;as</a></li>

                    <?php if ($this->creado_desde_sitio == 1){
                            //if($this->tarjeta_certificada){
                        ?>
                                <li <?php if($solapa == 'inventario'){ ?> class="active" <?php } ?>><a href="/inventario">Inventario</a></li>
                    <?php
                            //}
                          }else{ ?>
                            <li <?php if($solapa == 'inventario'){ ?> class="active" <?php } ?>><a href="/inventario">Inventario</a></li>
                    <?php } ?>

                    <li><a href="http://ayuda.mediafem.com/mediafem-anunciantes/primeros-pasos/subir-campana-paso-a-paso" target="_BLANK">Ayuda</a></li>
                </ul> <!-- end nav ul -->

                <div id="saldo_disponible"></div>
            </div>
            <div class="clear"></div>
        </nav>

        <div id="mensajes"></div>

        <?php if(isset($_GET['creada_ok'])){ ?>
                <script type="text/javascript">
                    mensajeGeneral('success', 'Campaña creada correctamente.');
                </script>
        <?php } ?>

        <?php if(isset($_GET['modificada_ok'])){ ?>
                <script type="text/javascript">
                    mensajeGeneral('success', 'Campaña modificada correctamente.');
                </script>
        <?php } ?>


        <section class="container">