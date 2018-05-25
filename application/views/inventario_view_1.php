<?php require_once 'application/views/top.php'; ?>

<style type="text/css" title="currentStyle">
    #buscar_canal,
    #buscar_pais,
    #cmb_rango{
        width: 202px !important;
    }

    table{
        border-spacing: 5px;
    }

    table td{
        vertical-align: middle;
    }
</style>

<script type="text/javascript">
    function strpos(cadena, busqueda){
        var i = (cadena.toLowerCase()).indexOf(busqueda.toLowerCase());
        return i === -1 ? false : true;
    }

    function cargando( estado ){
        if(estado == 1){
            $.blockUI({ css: {
                    border: 'none',
                    padding: '2px',
                    backgroundColor: '#000',
                    '-webkit-border-radius': '5px',
                    '-moz-border-radius': '5px',
                    'border-radius': '5px',
                    opacity: .7,
                    color: '#fff'
                },
                message: $('#question')
            });
        }else{
            $.unblockUI();
        }
    }
     
    function fnCreateSelect( aData )
    {
        var r='<select><option value="">Todos</option>', i, iLen=aData.length;
        for ( i=0 ; i<iLen ; i++ )
        {
            r += '<option value="'+aData[i]+'">'+aData[i]+'</option>';
        }
        return r+'</select>';
    }

    jQuery.fn.dataTableExt.oSort['formatted-num-asc'] = function(x,y){
        x = x.replace('.','');
        y = y.replace('.','');
        if(x.indexOf('/')>=0)x = eval(x);
        if(y.indexOf('/')>=0)y = eval(y);
        return x/1 - y/1;
    }
    jQuery.fn.dataTableExt.oSort['formatted-num-desc'] = function(x,y){
        x = x.replace('.','');
        y = y.replace('.','');
        if(x.indexOf('/')>=0)x = eval(x);
        if(y.indexOf('/')>=0)y = eval(y);
        return y/1 - x/1;
    }

    jQuery(function($){
        $.datepicker.regional['es'] = {
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
            dayNames: ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado'],
            dayNamesShort: ['Dom','Lun','Mar','Mie','Juv','Vie','Sab'],
            dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sa']};
        $.datepicker.setDefaults($.datepicker.regional['es']);
    });

    function reemplazar(texto,s1,s2){
        return texto.split(s1).join(s2);
    }

    $().ready(function() {
        $('#close_alerta').click(function(){
            $('.alerta').css('display', 'none');
        });

        // quito el cartel de alerta luego de 5 segundos
        $('.alerta').delay(5000).fadeOut(500);
        /************************ refrescar sitios ***************************/
        function refresh_sites(){
            $("#loader_cmb_sitios").css("display", "inline");
            $('#cmb_sitios').html(' ');
            /************************ SITIOS ESPECIFICOS ***************************/
            var mostrar_listado_sitios = false;
            var cantidad_publisher = 0;
            var publishers = '';

            var canales = '';

            $('#cmb_canales_tematicos_2 option').each(function(){
                canales += $(this).attr('value') + '-';
            });

            var form_data = {id_canal: canales};
            // alert(canales);
            $.ajax({
                type: "POST",
                url: "/inventario/get_publishers_y_sitios_json/",
                data: form_data,
                dataType: "json",
                success: function(msg){
                    if(msg){
                        publishers = eval(msg);
                        // alert(publishers.length);
                        cantidad_publisher = publishers.length;
                        var cantidad_sitios = 0;
                        // recorro uno por uno los publisher y creo un <objgroup>
                        for(var a=0; a < cantidad_publisher; a++){
                            if(publishers[a].sitios != ''){
                                $("#cmb_sitios").append('<optgroup label="'+publishers[a].publisher_name+'"></optgroup>');
                                // recorro los sitios del publisher y creo los <option>
                                cantidad_sitios = publishers[a].sitios.length;
                                for(var b=0; b < cantidad_sitios; b++){
                                    $('optgroup[label="'+publishers[a].publisher_name+'"]').append('<option value="'+publishers[a].sitios[b].id_sitio+'" rel="'+publishers[a].publisher_name+'">'+publishers[a].sitios[b].url+'</option>');
                                }
                            }
                        }

                        $("#loader_cmb_sitios").css("display", "none");
                        $("#btn_pasar_sitio_del_text").attr("disabled", "");
                        $("#txt_sitios_web").attr("disabled", false);
                        $("#cmb_sitios").attr("disabled", false);
                        $("#cmb_sitios_2").attr("disabled", false);
                        $("#btn_borrar_sitio").attr("disabled", false);
                        $("#btn_pasar_sitio").attr("disabled", false);
                        mostrar_listado_sitios = true;
                    }else{
                        //  alert(msg.error);
                    }
                }
            });
        }
        /************************ SITIOS ESPECIFICOS ***************************/
        var mostrar_listado_sitios = false;
        var cantidad_publisher = 0;
        var publishers = '';

        var form_data = {};

        $.ajax({
            type: "POST",
            url: "/inventario/get_publishers_y_sitios_json/",
            data: form_data,
            dataType: "json",
            success: function(msg){
                if(msg){
                    publishers = eval(msg);
                    cantidad_publisher = publishers.length;
                    var cantidad_sitios = 0;
                    // recorro uno por uno los publisher y creo un <objgroup>
                    for(var a=0; a < cantidad_publisher; a++){
                        if(publishers[a].sitios != ''){
                            $("#cmb_sitios").append('<optgroup label="'+publishers[a].publisher_name+'"></optgroup>');
                            // recorro los sitios del publisher y creo los <option>
                            cantidad_sitios = publishers[a].sitios.length;
                            for(var b=0; b < cantidad_sitios; b++){
                                $('optgroup[label="'+publishers[a].publisher_name+'"]').append('<option value="'+publishers[a].sitios[b].id_sitio+'" rel="'+publishers[a].publisher_name+'">'+publishers[a].sitios[b].url+'</option>');
                            }
                        }
                    }

                    $("#loader_cmb_sitios").css("display", "none");
                    $("#btn_pasar_sitio_del_text").attr("disabled", "");
                    $("#txt_sitios_web").attr("disabled", false);
                    $("#cmb_sitios").attr("disabled", false);
                    $("#cmb_sitios_2").attr("disabled", false);
                    $("#btn_borrar_sitio").attr("disabled", false);
                    $("#btn_pasar_sitio").attr("disabled", false);
                    mostrar_listado_sitios = true;
                }else{
                    alert(msg.error);
                }
            }
        });

        $("#txt_sitios_web").keyup( function(event){
            $('#cmb_sitios').html('');

            var busqueda = $(this).val();

            if(busqueda != ''){
                var cantidad_sitios = 0;

                // recorro uno por uno los publisher y creo un <objgroup>
                for(var a=0; a < cantidad_publisher; a++){
                    if(publishers[a].sitios != ''){
                        var encontrado = false;

                        // recorro los sitios del publisher y creo los <option>
                        cantidad_sitios = publishers[a].sitios.length;
                        for(var b=0; b < cantidad_sitios; b++){
                            if(strpos( publishers[a].sitios[b].url, busqueda ) != false){
                                encontrado = true;
                                break;
                            }
                        }

                        if(encontrado){
                            $("#cmb_sitios").append('<optgroup label="'+publishers[a].publisher_name+'"></optgroup>');
                            // recorro los sitios del publisher y creo los <option>
                            cantidad_sitios = publishers[a].sitios.length;
                            for(var b=0; b < cantidad_sitios; b++){
                                if(strpos( publishers[a].sitios[b].url, busqueda ) != false){
                                    $('optgroup[label="'+publishers[a].publisher_name+'"]').append('<option value="'+publishers[a].sitios[b].id_sitio+'" rel="'+publishers[a].publisher_name+'">'+publishers[a].sitios[b].url+'</option>');
                                }
                            }
                        }
                    }
                }
            }else{
                var cantidad_sitios = 0;
                // recorro uno por uno los publisher y creo un <objgroup>
                for(var a=0; a < cantidad_publisher; a++){
                    if(publishers[a].sitios != ''){
                        $("#cmb_sitios").append('<optgroup label="'+publishers[a].publisher_name+'"></optgroup>');
                        // recorro los sitios del publisher y creo los <option>
                        cantidad_sitios = publishers[a].sitios.length;
                        for(var b=0; b < cantidad_sitios; b++){
                            $('optgroup[label="'+publishers[a].publisher_name+'"]').append('<option value="'+publishers[a].sitios[b].id_sitio+'" rel="'+publishers[a].publisher_name+'">'+publishers[a].sitios[b].url+'</option>');
                        }
                    }
                }
            }

        });

        $("#cmb_sitios").dblclick( function (){
            $("#btn_pasar_sitio").click();
        });

        $("#cmb_sitios_2").dblclick( function (){
            $("#btn_borrar_sitio").click();
        });

        $("#btn_pasar_sitio").click( function (){
            if($('#cmb_sitios option:selected').siblings().length == 0){
                $('#cmb_sitios option:selected').parent().css('display', 'none');
                $('#cmb_sitios option:selected').appendTo("#cmb_sitios_2");
            }else{
                $('#cmb_sitios option:selected').parent().css('display', 'block');
                $('#cmb_sitios option:selected').appendTo("#cmb_sitios_2");
            }
        });

        $("#btn_borrar_sitio").click( function (){
            var seleccionados = $('#cmb_sitios_2 option:selected');
            for(var a = 0; a < seleccionados.length; a++){
                // nombre del publisher al que pertenece.
                var publisher = $(seleccionados[a]).attr('rel');

                $('#cmb_sitios optgroup').each(function(){
                    if($(this).attr('label') == publisher){
                        $(this).css('display', 'block')
                    }
                });

                $(seleccionados[a]).appendTo($("#cmb_sitios").find("optgroup[label='" + publisher + "']") );
            }
        });
        // LLENAR PAISES **************************************************
        var paises = [];
<?php for ($a = 0; $a < sizeof($paises); $a++) { ?>
            paises[<?= $a ?>] = ["<?= $paises[$a]->id ?>", "<?= $paises[$a]->descripcion ?>"];
            $('#cmb_paises').append('<option value="'+paises[<?= $a ?>][0]+'">'+paises[<?= $a ?>][1]+'</option>');
<?php } ?>

        // BUSCAR PAISES **************************************************
        $("#buscar_pais").keyup( function(event){
            $('#cmb_paises').html('');

            var busqueda = $(this).val();

            if(busqueda != ''){
                for(var a=0; a < paises.length; a++){
                    if(strpos( paises[a][1], busqueda ) != false){
                        $('#cmb_paises').append('<option value="'+paises[a][0]+'">'+paises[a][1]+'</option>');
                    }
                }
            }else{
                for(var a=0; a < paises.length; a++){
                    $('#cmb_paises').append('<option value="'+paises[a][0]+'">'+paises[a][1]+'</option>');
                }
            }
        });

        // LLENAR CANALES TEMATICOS ***************************************
        var canales_tematicos = [];
<?php
for ($a = 0; $a < sizeof($canales_tematicos); $a++) {
    ?>
                canales_tematicos[<?= $a ?>] = ["<?= $canales_tematicos[$a]->id ?>", "<?= $canales_tematicos[$a]->nombre ?>"];
                $('#cmb_canales_tematicos').append('<option value="'+canales_tematicos[<?= $a ?>][0]+'">'+canales_tematicos[<?= $a ?>][1]+'</option>');
    <?php
}
?>

        // BUSCAR CANALES TEMATICOS ***************************************
        $("#buscar_canal").keyup( function(){
            $('#cmb_canales_tematicos').html('');

            var busqueda = $(this).val();

            if(busqueda != ''){
                for(var a = 0 ; a < canales_tematicos.length ; a++){
                    if(strpos( canales_tematicos[a][1], busqueda ) != false){
                        $('#cmb_canales_tematicos').append('<option value="'+canales_tematicos[a][0]+'">'+canales_tematicos[a][1]+'</option>');
                    }
                }
            }else{
                for(var a = 0 ; a < canales_tematicos.length ; a++){
                    $('#cmb_canales_tematicos').append('<option value="'+canales_tematicos[a][0]+'">'+canales_tematicos[a][1]+'</option>');
                }
            }
        });

        // OTROS **********************************************************
        $("#btn_pasar_canal_tematico").click( function (){
            $('#cmb_canales_tematicos option:selected').appendTo("#cmb_canales_tematicos_2");

            refresh_sites();

            $("#txt_sitios_web").keyup( function(event){
                $('#cmb_sitios').html('');

                var busqueda = $(this).val();

                if(busqueda != ''){
                    var cantidad_sitios = 0;

                    // recorro uno por uno los publisher y creo un <objgroup>
                    for(var a=0; a < cantidad_publisher; a++){
                        if(publishers[a].sitios != ''){
                            var encontrado = false;

                            // recorro los sitios del publisher y creo los <option>
                            cantidad_sitios = publishers[a].sitios.length;
                            for(var b=0; b < cantidad_sitios; b++){
                                if(strpos( publishers[a].sitios[b].url, busqueda ) != false){
                                    encontrado = true;
                                    break;
                                }
                            }

                            if(encontrado){
                                $("#cmb_sitios").append('<optgroup label="'+publishers[a].publisher_name+'"></optgroup>');
                                // recorro los sitios del publisher y creo los <option>
                                cantidad_sitios = publishers[a].sitios.length;
                                for(var b=0; b < cantidad_sitios; b++){
                                    if(strpos( publishers[a].sitios[b].url, busqueda ) != false){
                                        $('optgroup[label="'+publishers[a].publisher_name+'"]').append('<option value="'+publishers[a].sitios[b].id_sitio+'" rel="'+publishers[a].publisher_name+'">'+publishers[a].sitios[b].url+'</option>');
                                    }
                                }
                            }
                        }
                    }
                }else{
                    var cantidad_sitios = 0;
                    // recorro uno por uno los publisher y creo un <objgroup>
                    for(var a=0; a < cantidad_publisher; a++){
                        if(publishers[a].sitios != ''){
                            $("#cmb_sitios").append('<optgroup label="'+publishers[a].publisher_name+'"></optgroup>');
                            // recorro los sitios del publisher y creo los <option>
                            cantidad_sitios = publishers[a].sitios.length;
                            for(var b=0; b < cantidad_sitios; b++){
                                $('optgroup[label="'+publishers[a].publisher_name+'"]').append('<option value="'+publishers[a].sitios[b].id_sitio+'" rel="'+publishers[a].publisher_name+'">'+publishers[a].sitios[b].url+'</option>');
                            }
                        }
                    }
                }

            });
        });

        $("#btn_borrar_canal_tematico").click( function (){
            $('#cmb_canales_tematicos_2 option:selected').appendTo("#cmb_canales_tematicos");
            refresh_sites();
        });

        $("#cmb_canales_tematicos").dblclick( function (){
            $('#cmb_canales_tematicos option:selected').appendTo("#cmb_canales_tematicos_2");

            refresh_sites();

            $("#txt_sitios_web").keyup( function(event){
                $('#cmb_sitios').html('');

                var busqueda = $(this).val();

                if(busqueda != ''){
                    var cantidad_sitios = 0;

                    // recorro uno por uno los publisher y creo un <objgroup>
                    for(var a=0; a < cantidad_publisher; a++){
                        if(publishers[a].sitios != ''){
                            var encontrado = false;

                            // recorro los sitios del publisher y creo los <option>
                            cantidad_sitios = publishers[a].sitios.length;
                            for(var b=0; b < cantidad_sitios; b++){
                                if(strpos( publishers[a].sitios[b].url, busqueda ) != false){
                                    encontrado = true;
                                    break;
                                }
                            }

                            if(encontrado){
                                $("#cmb_sitios").append('<optgroup label="'+publishers[a].publisher_name+'"></optgroup>');
                                // recorro los sitios del publisher y creo los <option>
                                cantidad_sitios = publishers[a].sitios.length;
                                for(var b=0; b < cantidad_sitios; b++){
                                    if(strpos( publishers[a].sitios[b].url, busqueda ) != false){
                                        $('optgroup[label="'+publishers[a].publisher_name+'"]').append('<option value="'+publishers[a].sitios[b].id_sitio+'" rel="'+publishers[a].publisher_name+'">'+publishers[a].sitios[b].url+'</option>');
                                    }
                                }
                            }
                        }
                    }
                }else{
                    var cantidad_sitios = 0;
                    // recorro uno por uno los publisher y creo un <objgroup>
                    for(var a=0; a < cantidad_publisher; a++){
                        if(publishers[a].sitios != ''){
                            $("#cmb_sitios").append('<optgroup label="'+publishers[a].publisher_name+'"></optgroup>');
                            // recorro los sitios del publisher y creo los <option>
                            cantidad_sitios = publishers[a].sitios.length;
                            for(var b=0; b < cantidad_sitios; b++){
                                $('optgroup[label="'+publishers[a].publisher_name+'"]').append('<option value="'+publishers[a].sitios[b].id_sitio+'" rel="'+publishers[a].publisher_name+'">'+publishers[a].sitios[b].url+'</option>');
                            }
                        }
                    }
                }

            });
        });

        $("#cmb_canales_tematicos_2").dblclick( function (){
            $('#cmb_canales_tematicos_2 option:selected').appendTo("#cmb_canales_tematicos");
            refresh_sites();
        });


        $("#btn_pasar_pais").click( function (){
            $('#cmb_paises option:selected').appendTo("#cmb_paises_2");
        });

        $("#btn_borrar_pais").click( function (){
            $('#cmb_paises_2 option:selected').appendTo("#cmb_paises");
        });

        $("#cmb_paises").dblclick( function (){
            $('#cmb_paises option:selected').appendTo("#cmb_paises_2");
        });

        $("#cmb_paises_2").dblclick( function (){
            $('#cmb_paises_2 option:selected').appendTo("#cmb_paises");
        });

        $("#select_all_canales").click( function (){
            if( $(this).attr('checked') ){
                $('#cmb_canales_tematicos option').each( function(){
                    $(this).attr('selected', 'selected');
                })
            }else{
                $('#cmb_canales_tematicos option').each( function(){
                    $(this).attr('selected', false);
                })
            }
        });

        $("#select_all_paises").click( function (){
            if( $(this).attr('checked') ){
                $('#cmb_paises option').each( function(){
                    $(this).attr('selected', 'selected');
                })
            }else{
                $('#cmb_paises option').each( function(){
                    $(this).attr('selected', false);
                })
            }
        });

        $.datepicker.setDefaults($.datepicker.regional['es']);
        $("#fecha_desde").datepicker({ dateFormat:'dd-mm-yy' });
        $("#fecha_hasta").datepicker({ dateFormat:'dd-mm-yy' });

        $("#cmb_rango").click(function(){
            var rango = $("#cmb_rango").find(':selected').val();

            if(rango=='especific'){
                $("#div_datapickers").css("display", "inline");
            }else{
                $("#div_datapickers").css("display", "none");
            }
        });



        // FILTRO REPORTES ************************************************
        var filtro_categorias = '';
        var filtro_paises = '';
        var intervalo = 'today';
        var fecha_desde = 0;
        var fecha_hasta = 0;
        function filtro_reportes(){
            filtro_categorias = '';
            filtro_paises = '';
            intervalo = 'today';
            fecha_desde = 0;
            fecha_hasta = 0;

            intervalo = $("#cmb_rango").val();

            fecha_desde = $.trim( $("#fecha_desde").val() );
            fecha_hasta = $.trim( $("#fecha_hasta").val() );

            if(fecha_desde == '')
                fecha_desde = 0;
            if(fecha_hasta == '')
                fecha_hasta = 0;

            $("#cmb_canales_tematicos_2 option").each(function(){
                filtro_categorias = filtro_categorias + $(this).attr('value') + 'o';
            });

            if(filtro_categorias == ''){
                $("#cmb_canales_tematicos option").each(function(){
                    filtro_categorias = filtro_categorias + $(this).attr('value') + 'o';
                });
            }

            $("#cmb_paises_2 option").each(function(){
                filtro_paises = filtro_paises + $(this).attr('value') + "o";
            });

            if(filtro_paises == ''){
                $("#cmb_paises option").each(function(){
                    filtro_paises = filtro_paises + $(this).attr('value') + "o";
                });
            }
        }


        var tipo_reporte_solicitado = 'por_sitio';

        // MOSTRAR ENVIAR REPORTE POR CORREO ******************************
        $('#enviar_correo').click( function(){
            $(this).fadeOut(function(){
                $('#enviar_por_correo').fadeIn();
            });
        });

        // ENVIAR REPORTE POR CORREO **************************************
        $('#enviar_reporte').click( function(){

            $.ajax({
                type: "POST",
                url: "/inventario/update_no_deseo_esperar/",
                dataType: "json"
            });
            var correo_electronico = $.trim($('#correo_electronico').val());
            var now = new Date();
            var seconds = now.getSeconds()+'o'+now.getMinutes();

            var form_data = {
                correo_electronico : correo_electronico,
                tipo : tipo_reporte_solicitado,
                filtro_categorias : filtro_categorias,
                filtro_paises : filtro_paises,
                intervalo : intervalo,
                fecha_desde : fecha_desde,
                fecha_hasta : fecha_hasta,
                seconds : seconds
            };

            $.ajax({
                type: "POST",
                url: "/inventario/suscribir_reporte/",
                data: form_data,
                dataType: "json",
                success: function(msg){
                    if(msg.validate == true)
                        window.location.replace("/inventario?suscripcion=ok");
                }
            });
        });

        // CANCELAR REPORTE POR CORREO ************************************
        $('#cancelar_reporte').click( function(){
            $('#enviar_por_correo').fadeOut(function(){
                $('#enviar_correo').fadeIn();
            });
        });

        // REPORTE POR SITIO **********************************************
        $("#btn_ejecutar_reporte_x_sitio").click( function (){
            cargando(1);

            var tipo_de_reporte = 1;
            /*1 es por sitio*/
            var form_data = {
                tipo_de_reporte : tipo_de_reporte
            };

            $.ajax({
                data: form_data,
                type: "POST",
                url: "/inventario/insert_reporte_usuarios/",
                dataType: "json"
            });
            tipo_reporte_solicitado = 'por_sitio';
            url_reporte_solicitado =

                filtro_reportes();

            var now = new Date();
            var seconds = now.getSeconds()+'o'+now.getMinutes();

            alert('/inventario/reporte_sitio/' + filtro_categorias + '/' + filtro_paises + '/' + intervalo + '/' + fecha_desde + '/' + fecha_hasta + '/' + seconds);

            $('#tbl_reporte').load('/inventario/reporte_sitio/' + filtro_categorias + '/' + filtro_paises + '/' + intervalo + '/' + fecha_desde + '/' + fecha_hasta + '/' + seconds, function(){
                cargando(0);
            });
        });

        // REPORTE POR CATEGORIA ******************************************
        $("#btn_ejecutar_reporte_x_categoria").click( function (){
            cargando(1);
            var tipo_de_reporte = 2;
            /*1 es por canal tematico*/
            var form_data = {
                tipo_de_reporte : tipo_de_reporte
            };

            $.ajax({
                data: form_data,
                type: "POST",
                url: "/inventario/insert_reporte_usuarios/",
                dataType: "json"
            });
            tipo_reporte_solicitado = 'por_categoria';

            filtro_reportes();

            var now = new Date();
            var seconds = now.getSeconds()+'o'+now.getMinutes();

            $('#tbl_reporte').load('/inventario/reporte_categoria/' + filtro_categorias + '/' + filtro_paises + '/' + intervalo + '/' + fecha_desde + '/' + fecha_hasta + '/' + seconds, function(){
                cargando(0);
            });
        });

        // REPORTE POR FORMATO ********************************************
        $("#btn_ejecutar_reporte_x_formato").click( function (){
            cargando(1);
            var tipo_de_reporte = 3;
            /*1 es por formato*/
            var form_data = {
                tipo_de_reporte : tipo_de_reporte
            };

            $.ajax({
                data: form_data,
                type: "POST",
                url: "/inventario/insert_reporte_usuarios/",
                dataType: "json"
            });

            tipo_reporte_solicitado = 'por_formato';

            filtro_reportes();

            var now = new Date();
            var seconds = now.getSeconds()+'o'+now.getMinutes();

            $('#tbl_reporte').load('/inventario/reporte_formato/' + filtro_categorias + '/' + filtro_paises + '/' + intervalo + '/' + fecha_desde + '/' + fecha_hasta + '/' + seconds, function(){
                cargando(0);
            });
        });

        // REPORTE POR SITIO-FORMATO **************************************
        $("#btn_ejecutar_reporte_x_sitio_formato").click( function (){
            cargando(1);
            var tipo_de_reporte = 4;
            /*1 es por sitio-formato*/
            var form_data = {
                tipo_de_reporte : tipo_de_reporte
            };

            $.ajax({
                data: form_data,
                type: "POST",
                url: "/inventario/insert_reporte_usuarios/",
                dataType: "json"
            });
            tipo_reporte_solicitado = 'por_sitio_formato';

            filtro_reportes();

            var now = new Date();
            var seconds = now.getSeconds()+'o'+now.getMinutes();

            $('#tbl_reporte').load('/inventario/reporte_sitio_formato/' + filtro_categorias + '/' + filtro_paises + '/' + intervalo + '/' + fecha_desde + '/' + fecha_hasta + '/' + seconds, function(){
                cargando(0);
            });
        });

        // REPORTE POR PAIS ***********************************************
        $("#btn_ejecutar_reporte_x_pais").click( function (){
            cargando(1);
            var tipo_de_reporte = 5;
            /*1 es por pais*/
            var form_data = {
                tipo_de_reporte : tipo_de_reporte
            };

            $.ajax({
                data: form_data,
                type: "POST",
                url: "/inventario/insert_reporte_usuarios/",
                dataType: "json"
            });
            tipo_reporte_solicitado = 'por_pais';

            filtro_reportes();

            var now = new Date();
            var seconds = now.getSeconds()+'o'+now.getMinutes();

            $('#tbl_reporte').load('/inventario/reporte_pais/' + filtro_categorias + '/' + filtro_paises + '/' + intervalo + '/' + fecha_desde + '/' + fecha_hasta + '/' + seconds, function(){
                cargando(0);
            });
        });
        /*
        // REPORTE POR PAIS Y FORMATO***********************************************
        $("#btn_ejecutar_reporte_x_pais_y_formato").click( function (){
            cargando(1);
            var tipo_de_reporte = 6;
            var form_data = {
                tipo_de_reporte : tipo_de_reporte
            };

            $.ajax({
                data: form_data,
                type: "POST",
                url: "/inventario/insert_reporte_usuarios/",
                dataType: "json"
            });
            tipo_reporte_solicitado = 'por_pais-formato';

            filtro_reportes();

            var now = new Date();
            var seconds = now.getSeconds()+'o'+now.getMinutes();

            //alert('/inventario/reporte_pais_y_formato/' + filtro_categorias + '/' + filtro_paises + '/' + intervalo + '/' + fecha_desde + '/' + fecha_hasta + '/' + seconds);

            $('#tbl_reporte').load('/inventario/reporte_pais_y_formato/' + filtro_categorias + '/' + filtro_paises + '/' + intervalo + '/' + fecha_desde + '/' + fecha_hasta + '/' + seconds, function(){
                cargando(0);
            });
        });
        */
    });
</script>

<table cellpadding="0px" class="tabla" width="80%" style="margin-bottom: 30px">
    <?php
    if (isset($_GET['suscripcion'])) {
        ?>
        <tr>
            <td colspan="3">
                <div class="alerta">
                    <h2>Su petici&oacute;n fu&eacute; realizada. </h2>
                    El reporte solicitado se enviar&aacute; a la casilla asignada ni bien se encuentre realizado.

                    <b class="floatRight" style="margin-top: -16px; display: inline-block; cursor: pointer" id="close_alerta">x</b>
                </div>
            </td>
        </tr>
        <?php
    }
    ?>
    <tr>
        <td>
            <table id="tbl_cmb_canales_tematicos">
                <tr><td colspan="3"><b>Canales Tem&aacute;ticos</b></td></tr>
                <tr>
                    <td>
                        <input type="text" name="buscar_canal" id="buscar_canal" placeholder="Buscar" />
                    </td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td style="width: 180px">
                        <div id="loader_cmb_cats" style="display:none"><img height="10px" alt="agregar" src="images/ajax-loader.gif" /></div>
                        <select style="width: 216px" size="7" id="cmb_canales_tematicos" name="cmb_sitios" multiple="multiple">
                        </select>
                    </td>
                    <td style="width: 10px">
                        <table>
                            <tr>
                                <td><input type="button" value=">>" id="btn_pasar_canal_tematico" class="button_new" /></td>
                            </tr>
                            <tr>
                                <td><input type="button" value="<<" id="btn_borrar_canal_tematico" class="button_new" /></td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <select style="width: 216px" size="7" id="cmb_canales_tematicos_2" name="cmb_sitios_2" multiple="multiple">
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label><input type="checkbox" id="select_all_canales" name="select_all_canales" value="1" />
                            Seleccionar todos</label>
                    </td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </table>
        </td>

        <td>
            <table>
                <tr><td colspan="3"><b>Pa&iacute;s</b></td></tr>
                <tr>
                    <td style="width: 180px !important;">
                        <input type="text" name="buscar_pais" id="buscar_pais" placeholder="Buscar" />
                    </td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td style="width: 180px !important;">
                        <select style="width: 216px" size="7" id="cmb_paises" name="cmb_paises" multiple="multiple">
                        </select>
                    </td>
                    <td style="width: 10px">
                        <table>
                            <tr>
                                <td><input type="button" value=">>" id="btn_pasar_pais" class="button_new" /></td>
                            </tr>
                            <tr>
                                <td><input type="button" value="<<" id="btn_borrar_pais" class="button_new" /></td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <select style="width: 216px" size="7" id="cmb_paises_2" name="cmb_paises_2" multiple="multiple">
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label><input type="checkbox" id="select_all_paises" name="select_all_paises" value="1" />
                            Seleccionar todos</label>
                    </td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <td style="width: 340px ">

                </td>
            </table>
        </td>
    </tr>
    <!--
     <table cellpadding="0px" id="sitios_especificos">
                        <tr>
                            <td colspan="3">
                                <input placeholder="Buscar" size="23" type="text" id="txt_sitios_web" name="q" disabled="disabled"  />
                                <span id="loader_cmb_sitios" style="display:none"><img src="/images/ajax-loader.gif" /> Comprobando...</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 200px">
                                <select style="width:200px;height:96px;margin-bottom: 14px;" size="10" id="cmb_sitios" name="cmb_sitios" disabled="disabled" multiple="multiple">
                                </select>
                            </td>
                            <td style="width: 10px">
                                <table>
                                    <tr>
                                        <td><input type="button" value=">>" id="btn_pasar_sitio" disabled="disabled" class="button_new" /></td>
                                    </tr>
                                    <tr>
                                        <td><input type="button" value="<<" id="btn_borrar_sitio" disabled="disabled" class="button_new" /></td>
                                    </tr>
                                </table>
                            </td>
                            <td>
                                <select style="width:200px;height:96px;margin-bottom: 14px;" size="10" id="cmb_sitios_2" name="cmb_sitios_2" disabled="disabled" multiple="multiple">
                                </select>
                            </td>
                        </tr>
                    </table>

        <td colspan="3">&nbsp;</td>
    </tr>
    -->
    <tr>
        <td colspan="3"><b>Rango de fechas del reporte</b></td>
    </tr>
    <tr>
        <td>
            <select id="cmb_rango" name="cmb_rango" class="combo" style="width: 216px;">
                <option value="today">Hoy</option>
                <option value="yesterday">Ayer</option>
                <option value="last_7_days">&Uacute;ltimos 7 dias</option>
                <option value="month_to_date">este mes: <?= getMesEsp(date('m')) ?></option>
                <option value="last_month">mes pasado: <?= getMesEsp(date('m') - 1) ?></option>
                <option value="especific">Fechas Especificas</option>
                <option value="lifetime">Siempre</option>
            </select>
            <span id="div_datapickers" style="display: none;position:relative;top:-25px;left:240px;">
                <table style="margin-left: 20px;" cellspacing="0" cellpadding="2" border="0">
                    <tr>
                        <td style="background-color: #E6E6FF"><input type="text" size="12" id="fecha_desde" name="fecha_desde" /></td>
                        <td style="background-color: #E6E6FF">-</td>
                        <td style="background-color: #E6E6FF"><input type="text" size="12" id="fecha_hasta" name="fecha_hasta" /></td>
                    </tr>
                </table>
            </span>
        </td>
    </tr>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td colspan="5">
            <input type="button" class="button_new" value="Por sitio" id="btn_ejecutar_reporte_x_sitio" />
            <input type="button" class="button_new" value="Por canal tem&aacute;tico" id="btn_ejecutar_reporte_x_categoria" />
            <input type="button" class="button_new" value="Por sitio-formato" id="btn_ejecutar_reporte_x_sitio_formato" />
            <input type="button" class="button_new" value="Por formato" id="btn_ejecutar_reporte_x_formato" />
            <input type="button" class="button_new" value="Por pa&iacute;s" id="btn_ejecutar_reporte_x_pais" />
            <!--
            <input type="button" class="button_new" value="Por pa&iacute;s -formato" id="btn_ejecutar_reporte_x_pais_y_formato" />
            -->
            <span id="loader_btn_ejecutar_reporte" style="display:none"><img height="14px" alt="agregar" src="images/ajax-loader.gif" /></span>
        </td>
    </tr>
</table>


<div id="tbl_reporte"></div>


<div id="question" style="display:none;cursor: default;padding: 10px;">
    <h2>Por favor, aguarde un momento ...</h2>
    <p>Este reporte puede llegar a demorarse hasta 5 minutos.</p>

    <!--
    <hr />
    <input type="button" id="enviar_correo" name="enviar_reporte" class="button_new" value="No quiero esperar, enviarmelo a mi correo" />

    <div id="enviar_por_correo" style="display: none;">
        <p>Ingrese su correo electr&oacute;nico y se le enviar&aacute; el reporte solicitado.</p>

        <input type="text" name="correo_electronico" id="correo_electronico" value="<?= $this->email_usuario ?>" style="padding: 4px; font-size: 11px; width: 170px;" />

        <input type="button" id="enviar_reporte" name="enviar_reporte" class="button_new" value="Enviar" />
        <input type="button" id="cancelar_reporte" name="cancelar_reporte" class="button_new" value="Cancelar" />
    </div>
    -->
</div>

<?php require_once 'application/views/footer.php'; ?>