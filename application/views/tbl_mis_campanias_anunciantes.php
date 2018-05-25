<table id="lista_campanas">
    <thead>
        <tr>
            <th>Anunciante</th>
            <th>Nombre de la campa&ntilde;a</th>
            <th>Segmentaci&oacute;n</th>
            <th>Fecha inicio</th>
            <th>Fecha fin</th>
            <th>Producto</th>
            <!--<th>Consumido</th>-->
            <th>Estado</th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($campanias as $row) {
            ?>
            <tr>
                <td><?= $row->nombre_anunciante ?></td>
                <td><?= htmlentities($row->nombre, ENT_QUOTES, 'UTF-8') ?></td>
                <td class="textCenter"><?= $row->descripcion_segmentacion ?></td>
                <td class="textCenter"><?= MySQLDateToDate($row->fecha_inicio) ?></td>
                <td class="textCenter"><?= MySQLDateToDate($row->fecha_fin) ?></td>
                <td class="textCenter">
                    <?php
                    if ($row->empresa_campania == 0) {
                        echo 'MediaFem';
                    } else {
                        echo 'AdTomatik';
                    }
                    ?>
                </td>
                <!--
                <td class="textCenter">
                <?php
                if ($row->consumido != '-') {
                    echo $row->consumido . ' ' . $this->user_data->moneda;
                } else {
                    echo $row->consumido;
                }
                ?>
                </td>
                -->
                <td class="textCenter">
                    <?php
                    if ($row->estado == "PENDIENTE") {
                        echo PENDIENTE;
                    } else if ($row->estado == "APROBADA") {
                        echo '<span style="color:green;font-weight:bold;">' . CAMPANIA_APROBADA . '</span>';
                    } else if ($row->estado == "NO_APROBADA") {
                        echo '<span style="color:red;font-weight:bold;">' . CAMPANIA_NO_APROBADA . '</span>';
                    } else if ($row->estado == "PAUSADA") {
                        echo '<span style="color:#FF8C00;font-weight:bold;">Pausada</span>';
                    } else if ($row->estado == "PENDIENTE_PAUSA") {
                        echo '<span style="color:#FF8C00;font-weight:bold;">' . PENDIENTE_PAUSA . '</span>';
                    } else if ($row->estado == "FINALIZADA") {
                        echo '<span style="color:#bbb;font-weight:bold;">Finalizada</span>';
                    }
                    ?>
                </td>
                <td class="textCenter">
                    <?php
                    if (strlen(trim($row->id_lineItem_appnexus)) != 0 || !$row->creada_desde_anunciantes) {
                        if (!$row->id_cliente) {
                            ?>
                            <a href="javascript:;" data-menu="on" data-menu-name="acciones_campanas_sin_duplicar" data-campania="<?= $row->id ?>" data-campania-name="<?= $row->nombre ?>">Acciones</a>
                            <?php
                            }else{
                            ?>
                            <a href="javascript:;" data-menu="on" data-menu-name="acciones_campanas_appnexus" data-campania="<?= $row->id ?>" data-campania-name="<?= $row->nombre ?>">Acciones</a>
                            <?php
                        }
                    } else {

                        if ($row->estado == "APROBADA") {
                            ?>
                            <a href="javascript:;" data-menu="on" data-menu-name="acciones_campanas_pausar" data-campania="<?= $row->id ?>" data-campania-name="<?= $row->nombre ?>">Acciones</a>
                            <?php
                        } else if ($row->estado == "PAUSADA") {
                            ?>
                            <a href="javascript:;" data-menu="on" data-menu-name="acciones_campanas_reactivar" data-campania="<?= $row->id ?>" data-campania-name="<?= $row->nombre ?>">Acciones</a>
                            <?php
                        } else if ($row->estado == "FINALIZADA") {
                            ?>
                            <a href="javascript:;" data-menu="on" data-menu-name="acciones_campanas_finalizada" data-campania="<?= $row->id ?>" data-campania-name="<?= $row->nombre ?>">Acciones</a>
                            <?php
                        } else {
                            ?>
                            <a href="javascript:;" data-menu="on" data-menu-name="acciones_campanas" data-campania="<?= $row->id ?>" data-campania-name="<?= $row->nombre ?>">Acciones</a>
                            <?php
                        }
                    }
                    ?>
                </td>
            </tr>
            <?php
        }
        ?>

        <?php
        if ($publinotas) {
            foreach ($publinotas as $row) {
                ?>
                <tr>
                    <td><?= $row->nombre_anunciante ?></td>
                    <td><?= htmlentities($row->nombre, ENT_QUOTES, 'UTF-8') ?></td>
                    <?php
                    if($row->tipo == 'SITIOS' || $row->tipo == 'MENCION_SITIOS'){
                    ?>
                        <td class="textCenter">Sitios especificos</td>
                    <?php
                    }else{
                    ?>    
                        <td class="textCenter">Embajadoras</td>
                    <?php
                    }
                    ?>    
                    <td class="textCenter"><?= MySQLDateToDate($row->fecha_inicio) ?></td>
                    <td class="textCenter"><?= MySQLDateToDate($row->fecha_fin) ?></td>
                    <td class="textCenter">MediaFem</td>
                    <!--<td class="textCenter">-</td>-->
                    <td class="textCenter">
                        <?php
                        if ($row->estado == "CONFIRMAR" || $row->estado == 'APROBADA_ADMIN') {
                            echo 'A confirmar';
                        } else if ($row->estado == "APROBADA" || $row->estado == "APROBADA_ADMIN") {
                            echo '<span style="color:green;font-weight:bold;">' . CAMPANIA_APROBADA . '</span>';
                        } else if ($row->estado == "NO_APROBADA" || $row->estado == "DESAPROBADA_ADMIN") {
                            echo '<span style="color:red;font-weight:bold;">' . CAMPANIA_NO_APROBADA . '</span>';
                        }
                        ?>
                    </td>
                    <td class="textCenter">
                        <a href="javascript:;" data-menu="on" data-menu-name="acciones_publinotas" data-campania="<?= $row->Id ?>" data-campania-name="<?= $row->nombre ?>">Acciones</a>
                    </td>
                </tr>
                <?php
            }
        }
        ?>
    </tbody>
</table> <!-- end #lista_campanas -->

<div class="submenu_right" id="acciones_campanas">
    <ul>
        <?php
        if ($usuario->id != '565') {
            ?>
            <li><a href="javascript:;" data-accion="datos_campania" data-campania="" data-campania-name="">Orden de compra</a></li>
            <?php
        }
        ?>
        <li><a href="javascript:;" data-accion="obtener_reporte" data-campania="" data-campania-name="">Obtener reporte</a></li>
        <?php
        if ($puede_modificar) {
            ?>
            <li><a href="javascript:;" data-accion="modificar_campania" data-campania="" data-campania-name="">Modificar campa&ntilde;a</a></li>
            <li><a href="javascript:;" data-reveal-id="duplicar_campania_modal" data-accion="duplicar_campania" data-campania="">Duplicar campa&ntilde;a</a></li>
            <?php
        }
        ?>
    </ul>
</div> <!-- end header #acciones_campanas -->

<div class="submenu_right" id="acciones_publinotas">
    <ul>
        <li><a href="javascript:;" data-accion="datos_campania" data-campania="" data-campania-name="" data-publinota="1">Orden de compra</a></li>
    </ul>
</div> <!-- end header #acciones_campanas -->


<div class="submenu_right" id="acciones_campanas_pausar">
    <ul>
        <?php
        if ($usuario->id != '565') {
            ?>
            <li><a href="javascript:;" data-accion="datos_campania" data-campania="" data-campania-name="">Orden de compra</a></li>
            <?php
        }
        ?>
        <li><a href="javascript:;" data-accion="obtener_reporte" data-campania="" data-campania-name="">Obtener reporte</a></li>
        <?php
        if ($puede_modificar) {
            ?>
            <li><a href="javascript:;" data-accion="modificar_campania" data-campania="" data-campania-name="">Modificar campa&ntilde;a</a></li>
            <li><a href="javascript:;" data-reveal-id="duplicar_campania_modal" data-accion="duplicar_campania" data-campania="">Duplicar campa&ntilde;a</a></li>
            <li><a href="javascript:;" data-reveal-id="pausar_campania_modal" data-accion="pausar_campania" data-campania="" data-campania-name="">Pausar campa&ntilde;a</a></li>
            <?php
        }
        ?>
    </ul>
</div> <!-- end header #acciones_campanas -->


<div class="submenu_right" id="acciones_campanas_reactivar">
    <ul>
        <?php
        if ($usuario->id != '565') {
            ?>
            <li><a href="javascript:;" data-accion="datos_campania" data-campania="" data-campania-name="">Orden de compra</a></li>
            <?php
        }
        ?>
        <li><a href="javascript:;" data-accion="obtener_reporte" data-campania="" data-campania-name="">Obtener reporte</a></li>
        <?php
        if ($puede_modificar) {
            ?>
            <li><a href="javascript:;" data-accion="modificar_campania" data-campania="" data-campania-name="">Modificar campa&ntilde;a</a></li>
            <li><a href="javascript:;" data-reveal-id="duplicar_campania_modal" data-accion="duplicar_campania" data-campania="">Duplicar campa&ntilde;a</a></li>
            <li><a href="javascript:;" data-reveal-id="reactivar_campania_modal" data-accion="reactivar_campania" data-campania="" data-campania-name="">Reactivar campa&ntilde;a</a></li>
            <?php
        }
        ?>
    </ul>
</div> <!-- end header #acciones_campanas_reactivar -->

<div class="submenu_right" id="acciones_campanas_finalizada">
    <ul>
        <?php
        if ($usuario->id != '565') {
            ?>
            <li><a href="javascript:;" data-accion="datos_campania" data-campania="" data-campania-name="">Orden de compra</a></li>
            <?php
        }
        ?>
        <li><a href="javascript:;" data-accion="obtener_reporte" data-campania="" data-campania-name="">Obtener reporte</a></li>
        <?php
        if ($puede_modificar) {
            ?>
            <li><a href="javascript:;" data-reveal-id="duplicar_campania_modal" data-accion="duplicar_campania" data-campania="">Duplicar campa&ntilde;a</a></li>
            <?php
        }
        ?>
    </ul>
</div> <!-- end header #acciones_campanas_finalizada -->


<div class="submenu_right" id="acciones_campanas_sin_duplicar">
    <ul>
        <?php
        if ($usuario->id != '565') {
            ?>
            <li><a href="javascript:;" data-accion="datos_campania" data-campania="" data-campania-name="">Orden de compra</a></li>
            <?php
        }
        ?>
        <li><a href="javascript:;" data-accion="obtener_reporte" data-campania="" data-campania-name="">Obtener reporte</a></li>
        <?php
        if ($puede_modificar) {
            ?>
            <li><a href="javascript:;" data-accion="modificar_campania" data-campania="" data-campania-name="">Modificar campa&ntilde;a</a></li>
            <?php
        }
        ?>
    </ul>
</div> <!-- end header #acciones_campanas -->

<div class="submenu_right" id="acciones_campanas_appnexus">
    <ul>
        <?php
        if ($usuario->id != '565') {
            ?>
            <li><a href="javascript:;" data-accion="datos_campania" data-campania="" data-campania-name="">Orden de compra</a></li>
            <?php
        }
        ?>
        <li><a href="javascript:;" data-accion="obtener_reporte" data-campania="" data-campania-name="">Obtener reporte</a></li>
        <?php
        if ($puede_modificar) {
            ?>
            <li><a href="javascript:;" data-reveal-id="duplicar_campania_modal" data-accion="duplicar_campania" data-campania="">Duplicar campa&ntilde;a</a></li>
            <li><a href="javascript:;" data-accion="modificar_campania" data-campania="" data-campania-name="">Modificar campa&ntilde;a</a></li>
            <li><a href="javascript:;" data-reveal-id="pausar_campania_modal" data-accion="pausar_campania" data-campania="" data-campania-name="">Pausar campa&ntilde;a</a></li>
            <?php
        }
        ?>
    </ul>
</div> <!-- end header #acciones_campanas_appnexus -->


<div id="duplicar_campania_modal" class="reveal-modal"></div>
<div id="pausar_campania_modal" class="reveal-modal"></div>
<div id="reactivar_campania_modal" class="reveal-modal"></div>

<script src="js/subMenu.js" type="text/javascript"></script>

<script type="text/javascript">
            $(document).ready(function() {

    // MENU DE "ACCIONES" ******************************************
    // DATOS DE CAMPANIA
    $('a[data-accion="datos_campania"]').click(function() {
    var total_tabs = $('#campanas_tabs li').length + 1;
            if (total_tabs <= 7) {

    var total_tabs_width = 0;
            // obtengo los datos de la campana y agrego la pestana al menu
            var id_campania = $(this).attr('data-campania');
            var nombre_campania = $(this).attr('data-campania-name');
            var html = '<li><a href="#datos_campana_' + id_campania + '" title="Datos: ' + nombre_campania + '" class="selected">Datos: ' + nombre_campania + '</a><span class="closeTab">x</span></li>';
            $('#campanas_tabs').append(html);
            // calculo el ancho total.
            $('#campanas_tabs li').each(function() {
    total_tabs_width += $(this).outerWidth(true);
    });
            // si sobrepasa el maximo de 566px la suma de todos entonces
            if (total_tabs_width > 1200) {
    var width = 300 / total_tabs - 10;
            // asigno clase wrap a los li > a y un width personalizado
            $('#campanas_tabs li a').each(function() {
    var href = $(this).attr('href');
            if (href != '#mis_campanas' && href != '#status_campanas' && href != '#nueva_campana'){
    $(this).addClass('nowrap');
            $(this).attr('style', 'width: ' + width + 'px;');
    }
    });
    }

    // creo el div contenedor de la pestana
    html = '<div id="datos_campana_' + id_campania + '" style="display: none;">' + divLoader + '</div>';
            $('.contenainer_tabs').append(html);
            if ($(this).attr('data-publinota') == 1){
    $('#datos_campana_' + id_campania).load("/campania/ver_estado_publinota/" + id_campania);
    } else{
    $('#datos_campana_' + id_campania).load("/campania/ver/" + id_campania + '/0/0');
    }

    $('.Tabs').idTabs({
    click: function(id, all, container, settings) {
    var id = $(this).attr('href').replace('#', '');
            $('#' + id).attr('style', 'display: block;');
            return true;
    }
    });
    } else {
    mensajeGeneral('warning', 'No puedes agregar más pestañas. Por favor cierra algunas y vuelve a intentarlo.');
    }
    });
            // MODIFICAR CAMPANIA
            $('a[data-accion="modificar_campania"]').click(function() {
    var total_tabs = $('#campanas_tabs li').length + 1;
            if (total_tabs <= 7) {

    var total_tabs_width = 0;
            // obtengo los datos de la campana y agrego la pestana al menu
            var id_campania = $(this).attr('data-campania');
            var nombre_campania = $(this).attr('data-campania-name');
            var html = '<li><a href="#modificar_campana_' + id_campania + '" title="Modificar: ' + nombre_campania + '" class="selected">Modificar: ' + nombre_campania + '</a><span class="closeTab">x</span></li>';
            $('#campanas_tabs').append(html);
            // calculo el ancho total.
            $('#campanas_tabs li').each(function() {
    total_tabs_width += $(this).outerWidth(true);
    });
            // si sobrepasa el maximo de 566px la suma de todos entonces
            if (total_tabs_width > 1200) {
    var width = 300 / total_tabs - 10;
            // asigno clase wrap a los li > a y un width personalizado
            $('#campanas_tabs li a').each(function() {
    var href = $(this).attr('href');
            if (href != '#mis_campanas' && href != '#status_campanas' && href != '#nueva_campana'){
    $(this).addClass('nowrap');
            $(this).attr('style', 'width: ' + width + 'px;');
    }
    });
    }

    // creo el div contenedor de la pestana
    html = '<div id="modificar_campana_' + id_campania + '" style="display: none;">' + divLoader + '</div>';
            $('.contenainer_tabs').append(html);
            $('#modificar_campana_' + id_campania).load("/campania/modificar/" + id_campania);
            $('.Tabs').idTabs({
    click: function(id, all, container, settings) {
    var id = $(this).attr('href').replace('#', '');
            $('#' + id).attr('style', 'display: block;');
            return true;
    }
    });
    } else {
    mensajeGeneral('warning', 'No puedes agregar más pestañas. Por favor cierra algunas y vuelve a intentarlo.');
    }
    });
            // DUPLICAR CAMPANIA
            $('a[data-accion="duplicar_campania"]').click(function() {
    $('#duplicar_campania_modal').html(' ');
            $('#duplicar_campania_modal').load('/campania/show_duplicar/' + $(this).attr('data-campania'));
    });
            // PAUSAR CAMPANIA
            $('a[data-accion="pausar_campania"]').click(function() {
    $('#pausar_campania_modal').html(' ');
            $('#pausar_campania_modal').load('/campania/show_pausar/' + $(this).attr('data-campania'));
    });
            // REACTIVAR CAMPANIA
            $('a[data-accion="reactivar_campania"]').click(function() {
    $('#reactivar_campania_modal').html(' ');
            $('#reactivar_campania_modal').load('/campania/show_reactivar/' + $(this).attr('data-campania'));
    });
            // OBTENER REPORTE
            $('a[data-accion="obtener_reporte"]').click(function() {
    var total_tabs = $('#campanas_tabs li').length + 1;
            if (total_tabs <= 7) {

    var total_tabs_width = 0;
            // obtengo los datos de la campana y agrego la pestana al menu
            var id_campania = $(this).attr('data-campania');
            var nombre_campania = $(this).attr('data-campania-name');
            var html = '<li><a href="#obtener_reporte_' + id_campania + '" title="Reporte: ' + nombre_campania + '" class="selected">Reporte: ' + nombre_campania + '</a><span class="closeTab">x</span></li>';
            $('#campanas_tabs').append(html);
            // calculo el ancho total.
            $('#campanas_tabs li').each(function() {
    total_tabs_width += $(this).outerWidth(true);
    });
            // si sobrepasa el maximo de 566px la suma de todos entonces
            if (total_tabs_width > 1200) {
    var width = 300 / total_tabs - 10;
            // asigno clase wrap a los li > a y un width personalizado
            $('#campanas_tabs li a').each(function() {
    var href = $(this).attr('href');
            if (href != '#mis_campanas' && href != '#status_campanas' && href != '#nueva_campana'){
    $(this).addClass('nowrap');
            $(this).attr('style', 'width: ' + width + 'px;');
    }
    });
    }

    // creo el div contenedor de la pestana
    html = '<div id="obtener_reporte_' + id_campania + '" style="display: none;">' + divLoader + '</div>';
            $('.contenainer_tabs').append(html);
            $('#obtener_reporte_' + id_campania).load("/welcome/reporte/" + id_campania);
            $('.Tabs').idTabs({
    click: function(id, all, container, settings) {
    var id = $(this).attr('href').replace('#', '');
            $('#' + id).attr('style', 'display: block;');
            return true;
    }
    });
    } else {
    mensajeGeneral('warning', 'No puedes agregar más pestañas. Por favor cierra algunas y vuelve a intentarlo.');
    }
    });
            $('a[data-menu-name="acciones_campanas"]').click(function() {
    // una vez pulsado el botón de "acciones" asigno a los enlaces del menu los datos de la campana
    $('#acciones_campanas a').attr('data-campania', $(this).attr('data-campania'));
            $('#acciones_campanas a').attr('data-campania-name', $(this).attr('data-campania-name'));
    });
            $('a[data-menu-name="acciones_publinotas"]').click(function() {
    // una vez pulsado el botón de "acciones" asigno a los enlaces del menu los datos de la campana
    $('#acciones_publinotas a').attr('data-campania', $(this).attr('data-campania'));
            $('#acciones_publinotas a').attr('data-campania-name', $(this).attr('data-campania-name'));
    });
            $('a[data-menu-name="acciones_campanas_pausar"]').click(function() {
    // una vez pulsado el botón de "acciones" asigno a los enlaces del menu los datos de la campana
    $('#acciones_campanas_pausar a').attr('data-campania', $(this).attr('data-campania'));
            $('#acciones_campanas_pausar a').attr('data-campania-name', $(this).attr('data-campania-name'));
    });
            $('a[data-menu-name="acciones_campanas_reactivar"]').click(function() {
    // una vez pulsado el botón de "acciones" asigno a los enlaces del menu los datos de la campana
    $('#acciones_campanas_reactivar a').attr('data-campania', $(this).attr('data-campania'));
            $('#acciones_campanas_reactivar a').attr('data-campania-name', $(this).attr('data-campania-name'));
    });
            $('a[data-menu-name="acciones_campanas_finalizada"]').click(function() {
    // una vez pulsado el botón de "acciones" asigno a los enlaces del menu los datos de la campana
    $('#acciones_campanas_finalizada a').attr('data-campania', $(this).attr('data-campania'));
            $('#acciones_campanas_finalizada a').attr('data-campania-name', $(this).attr('data-campania-name'));
    });
            $('a[data-menu-name="acciones_campanas_sin_duplicar"]').click(function() {
    // una vez pulsado el botón de "acciones" asigno a los enlaces del menu los datos de la campana
    $('#acciones_campanas_sin_duplicar a').attr('data-campania', $(this).attr('data-campania'));
            $('#acciones_campanas_sin_duplicar a').attr('data-campania-name', $(this).attr('data-campania-name'));
    });
            $('a[data-menu-name="acciones_campanas_appnexus"]').click(function() {
    // una vez pulsado el botón de "acciones" asigno a los enlaces del menu los datos de la campana
    $('#acciones_campanas_appnexus a').attr('data-campania', $(this).attr('data-campania'));
            $('#acciones_campanas_appnexus a').attr('data-campania-name', $(this).attr('data-campania-name'));
    });
            // *************************************************************

            // LISTO LAS CAMPAÑAS ******************************************
            $('#lista_campanas').dataTable({
<?php if (sizeof($campanias) > 10) { ?>
        "sPaginationType": "full_numbers",
<?php } else { ?>
        "bPaginate": false,
<?php } ?>
    "bLengthChange": false,
            "aaSorting": [[4, "desc"]],
            "bFilter": true,
            "bInfo": false,
            "bLength": false,
            "sSearch": 'Buscar',
            "oLanguage": {
            "sSearch": "Buscar: ",
                    "sLengthMenu": "Mostrar  : _MENU_ de  <?= sizeof($campanias); ?>",
                    "oPaginate": {
                    "sFirst": "<<",
                            "sLast": ">>",
                            "sNext": ">",
                            "sPrevious": "<"
                    }
            },
            "aoColumns": [
                    null,
                    null,
                    null,
            {"sType": "uk_date"},
            {"sType": "uk_date"},
                    null,
                    /*{"sType": "slo"},*/
                    null,
            {"bSortable": false}
            ]
    });
            // **************************************************************
    });
</script>