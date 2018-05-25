<table id="lista_campanas">
    <thead>
        <tr>
            <th>Nombre de la campa&ntilde;a</th>
            <th>Segmentaci&oacute;n</th>
            <th>Fecha Inicio</th>
            <th>Fecha Fin</th>
            <th>Estado</th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($campanias as $row) {
            ?>
            <tr>
                <td><?= $row->nombre ?></td>
                <td class="textCenter"><?= $row->descripcion_segmentacion ?></td>
                <td class="textCenter"><?= MySQLDateToDate($row->fecha_inicio) ?></td>
                <td class="textCenter"><?= MySQLDateToDate($row->fecha_fin) ?></td>
                <td class="textCenter">
                    <?php
                    if ($row->estado == "PENDIENTE") {
                        echo PENDIENTE;
                    } else {
                        echo '<span style="color:green;font-weight:bold;">' . CAMPANIA_APROBADA . '</span>';
                    }
                    ?>
                </td>
                <td class="textCenter">
                    <a href="#" data-menu="on" data-menu-name="acciones_campanas" data-campania="<?= $row->id ?>" data-campania-name="<?= $row->nombre ?>">Acciones</a>
                </td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table> <!-- end #lista_campanas -->

<div class="submenu_right" id="acciones_campanas">
    <ul>
        <li><a href="#" data-accion="datos_campania" data-campania="" data-campania-name="">Datos de la campa&ntilde;a</a></li>
        <li><a href="#" data-accion="obtener_reporte" data-campania="" data-campania-name="">Obtener reporte2</a></li>
        <li><a href="#" data-accion="modificar_campania" data-campania="" data-campania-name="">Modificar campa&ntilde;a</a></li>
    </ul>
</div> <!-- end header #acciones_campanas -->

<script src="js/subMenu.js" type="text/javascript"></script>

<script type="text/javascript">
    $(document).ready(function(){
        // MENU DE "ACCIONES" ******************************************
        // DATOS DE CAMPANIA
        $('a[data-accion="datos_campania"]').click(function(){
            var total_tabs = $('#campanas_tabs li').length + 1;

            if(total_tabs <= 7){

                var total_tabs_width = 0;

                // obtengo los datos de la campana y agrego la pestana al menu
                var id_campania = $(this).attr('data-campania');
                var nombre_campania = $(this).attr('data-campania-name');

                var html = '<li><a href="#datos_campana_' + id_campania + '" title="Datos: ' + nombre_campania + '" class="selected">Datos: ' + nombre_campania + '</a><span class="closeTab">x</span></li>';

                $('#campanas_tabs').append(html);

                // calculo el ancho total.
                $('#campanas_tabs li').each(function(){
                    total_tabs_width += $(this).outerWidth(true);
                });

                // si sobrepasa el maximo de 566px la suma de todos entonces
                if(total_tabs_width > 1200){
                    var width = 500 / total_tabs - 10;

                    // asigno clase wrap a los li > a y un width personalizado
                    $('#campanas_tabs li a').each(function(){
                        $(this).addClass('nowrap');
                        $(this).attr('style', 'width: ' + width + 'px;');
                    });
                }

                // creo el div contenedor de la pestana
                html = '<div id="datos_campana_' + id_campania + '" style="display: none;">' + divLoader + '</div>';
                $('.contenainer_tabs').append(html);

                $('#datos_campana_' + id_campania).load("/campania/ver/" + id_campania);

                $('.Tabs').idTabs({
                    click: function(id, all, container, settings){
                        var id = $(this).attr('href').replace('#', '');
                        $( '#' + id ).attr('style','display: block;');
                        return true;
                    }
                });
            }else{
                mensajeGeneral('warning', 'No puedes agregar más pestañas. Por favor cierra algunas y vuelve a intentarlo.');
            }
        });

        // MODIFICAR CAMPANIA
        $('a[data-accion="modificar_campania"]').click(function(){
            var total_tabs = $('#campanas_tabs li').length + 1;

            if(total_tabs <= 7){

                var total_tabs_width = 0;

                // obtengo los datos de la campana y agrego la pestana al menu
                var id_campania = $(this).attr('data-campania');
                var nombre_campania = $(this).attr('data-campania-name');

                var html = '<li><a href="#modificar_campana_' + id_campania + '" title="Datos: ' + nombre_campania + '" class="selected">Modificar: ' + nombre_campania + '</a><span class="closeTab">x</span></li>';

                $('#campanas_tabs').append(html);

                // calculo el ancho total.
                $('#campanas_tabs li').each(function(){
                    total_tabs_width += $(this).outerWidth(true);
                });

                // si sobrepasa el maximo de 566px la suma de todos entonces
                if(total_tabs_width > 1200){
                    var width = 500 / total_tabs - 10;

                    // asigno clase wrap a los li > a y un width personalizado
                    $('#campanas_tabs li a').each(function(){
                        $(this).addClass('nowrap');
                        $(this).attr('style', 'width: ' + width + 'px;');
                    });
                }

                // creo el div contenedor de la pestana
                html = '<div id="modificar_campana_' + id_campania + '" style="display: none;">' + divLoader + '</div>';
                $('.contenainer_tabs').append(html);

                $('#modificar_campana_' + id_campania).load("/campania/modificar/" + id_campania);

                $('.Tabs').idTabs({
                    click: function(id, all, container, settings){
                        var id = $(this).attr('href').replace('#', '');
                        $( '#' + id ).attr('style','display: block;');
                        return true;
                    }
                });
            }else{
                mensajeGeneral('warning', 'No puedes agregar más pestañas. Por favor cierra algunas y vuelve a intentarlo.');
            }
        });

        // OBTENER REPORTE
        $('a[data-accion="obtener_reporte"]').click(function(){
            var total_tabs = $('#campanas_tabs li').length + 1;

            if(total_tabs <= 7){

                var total_tabs_width = 0;

                // obtengo los datos de la campana y agrego la pestana al menu
                var id_campania = $(this).attr('data-campania');
                var nombre_campania = $(this).attr('data-campania-name');

                var html = '<li><a href="#obtener_reporte_' + id_campania + '" title="Reporte: ' + nombre_campania + '" class="selected">Reporte: ' + nombre_campania + '</a><span class="closeTab">x</span></li>';

                $('#campanas_tabs').append(html);

                // calculo el ancho total.
                $('#campanas_tabs li').each(function(){
                    total_tabs_width += $(this).outerWidth(true);
                });

                // si sobrepasa el maximo de 566px la suma de todos entonces
                if(total_tabs_width > 1200){
                    var width = 500 / total_tabs - 10;

                    // asigno clase wrap a los li > a y un width personalizado
                    $('#campanas_tabs li a').each(function(){
                        $(this).addClass('nowrap');
                        $(this).attr('style', 'width: ' + width + 'px;');
                    });
                }

                // creo el div contenedor de la pestana
                html = '<div id="obtener_reporte_' + id_campania + '" style="display: none;">' + divLoader + '</div>';
                $('.contenainer_tabs').append(html);

                $('#obtener_reporte_' + id_campania).load("/welcome/reporte/" + id_campania);

                $('.Tabs').idTabs({
                    click: function(id, all, container, settings){
                        var id = $(this).attr('href').replace('#', '');
                        $( '#' + id ).attr('style','display: block;');
                        return true;
                    }
                });
            }else{
                mensajeGeneral('warning', 'No puedes agregar más pestañas. Por favor cierra algunas y vuelve a intentarlo.');
            }
        });

        $('a[data-menu-name="acciones_campanas"]').click(function(){
            //alert('id: ' + $(this).attr('data-campania') + ' - Nombre: ' + $(this).attr('data-campania-name'));
            // una vez pulsado el botón de "acciones" asigno a los enlaces del menu los datos de la campana
            $('#acciones_campanas a').attr('data-campania', $(this).attr('data-campania'));
            $('#acciones_campanas a').attr('data-campania-name', $(this).attr('data-campania-name'));
        });
        // *************************************************************

        // LISTO LAS CAMPAÑAS ******************************************
        $('#lista_campanas').dataTable({
            <?php if(sizeof($campanias) > 10){ ?>
                "bPaginate": true,
            <?php }else{ ?>
                "bPaginate": false,
            <?php } ?>
            "sPaginationType": "full_numbers",
            "iDisplayLength": 10,
            "bLengthChange": false,
            "bFilter": true,
            "bInfo": false,
            "bLength": false,
            'oLanguage': {
                "oPaginate": {
                    "sFirst": "<<",
                    "sLast": ">>",
                    "sNext": ">",
                    "sPrevious": "<"
                },
                "sSearch": 'Buscar'
            },
            "aoColumns": [
                null,
                null,
                { "sType": "uk_date" },
                { "sType": "uk_date" },
                null,
                { "bSortable": false }
            ]
        });
        // **************************************************************
    });
</script>