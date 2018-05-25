<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <title>MediaFem para Anunciantes - Campa&ntilde;as</title>
        <?php require_once 'head_links.php'; ?>
        <?php require_once 'application/views/analytics.html'; ?>

        <style type="text/css">
            #form_alta_campania{
                margin: 15px;
            }

            #form_alta_campania div{
                clear: both;
                margin: 8px 0;
            }

            #form_alta_campania div label{
                font: normal bold 12px Arial, Helvetica, sans-serif;
                width: 140px;
                display: inline-block;
            }

            select, input[type="text"]{
                border: 1px solid #C7C7C7;
                -moz-border-radius: 2px; /* Firefox*/
                -ms-border-radius: 2px; /* IE 8.*/
                -webkit-border-radius: 2px; /* Safari,Chrome.*/
                border-radius: 2px; /* El estándar.*/

                color: #333;
                font: normal normal 11px Arial, Helvetica, sans-serif;
                padding: 3px 0;
                width: 303px;
            }

            input[type="text"]{
                padding: 4px 2px !important;
                width: 297px !important;
            }

            .alerta{
                background: none repeat scroll 0 0 #FEFFD5;
                border: 1px solid #FDFFB3;
                font: 11px/24px Arial,Helvetica,sans-serif;
                margin: 0 20px 20px;
                padding: 5px 10px;
                text-align: center;
            }
        </style>

        <script type="text/javascript">
            jQuery(function($){
                $.datepicker.regional['es'] = {
                    monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                    monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
                    dayNames: ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado'],
                    dayNamesShort: ['Dom','Lun','Mar','Mie','Juv','Vie','Sab'],
                    dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sa']};
                $.datepicker.setDefaults($.datepicker.regional['es']);
            });

            $().ready(function() {
                $('.msg_error').css('display', 'none');

                $('#id_anunciante').val(<?= $id_anunciante ?>);

                $('input[name="monto"]').focusout(function(){
                    if(isNaN($(this).val()) || $(this).val() <= 0){
                        $("#error_formato").html('Por favor ingrese un monto valido.').css("display", "inline");
                        $(this).css('borderColor', 'red');
                        return false;
                    }else{
                        $("#error_formato").html('').css("display", "none");
                        $(this).css('borderColor', '');
                    }

                    var rel = $(this).attr('rel');
                    if( parseFloat( $(this).val() ) < parseFloat( $('#monto_oculto_' + rel).attr('value') ) ){
                        alert('El valor indicado no puede ser inferior al valor mínimo.');
                        $(this).css('borderColor', 'red');
                    }else{
                        $(this).css('borderColor', '');
                    }
                });

                $('input[name="monto"]').keyup(function(){
                    $(this).val( $(this).val().replace(/,/g,".") );
                    if(isNaN($(this).val()) || $(this).val() <= 0){
                        $("#error_formato").html('Por favor ingrese un monto valido.').css("display", "inline");
                        $(this).css('borderColor', 'red');
                        return false;
                    }else{
                        $("#error_formato").html('').css("display", "none");
                        $(this).css('borderColor', '');
                    }

                    //$('#inversion_bruta').val( calcular_bruto() );

                    //calcular_neto();
                });

                $('input[name="cantidad"]').keyup(function(){
                    if(isNaN($(this).val()) || $(this).val() <= 0){
                        $("#error_formato").html('Por favor ingrese una cantidad valida.').css("display", "inline");
                        $(this).css('borderColor', 'red');
                        return false;
                    }else{
                        $("#error_formato").html('').css("display", "none");
                        $(this).css('borderColor', '');
                    }

                    //$('#inversion_bruta').val( calcular_bruto() );

                    //calcular_neto();
                });

                $("#comision").keyup( function(){
                    if(isNaN($(this).val()) || $(this).val() < 0){
                        $('#error_comision').html('Por favor ingrese una comisión valida.').css('display', 'inline');
                        $(this).css('borderColor', 'red');
                        return false;
                    }else{
                        $('#error_comision').css('display', 'none');
                        $(this).css('borderColor', '');
                    }

                    //calcular_neto();
                });

                $("#descuento").keyup( function(){
                    if(isNaN($(this).val()) || $(this).val() < 0){
                        $('#error_descuento').html('Por favor ingrese un descuento valido.').css('display', 'inline');
                        $(this).css('borderColor', 'red');
                        return false;
                    }else{
                        $('#error_descuento').css('display', 'none');
                        $(this).css('borderColor', '');
                    }

                    //calcular_neto();
                });


                function strpos(cadena, busqueda){
                    var i = (cadena.toLowerCase()).indexOf(busqueda.toLowerCase());
                    return i === -1 ? false : true;
                }

                /************************ SITIOS ESPECIFICOS ***************************/

                var mostrar_listado_sitios = false;
                var cantidad_publisher = 0;
                var publishers = '';

                var form_data = {};

                $.ajax({
                    type: "GET",
                    url: "/campania/get_publishers_y_sitios_json/",
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


                $("#cmb_segmentaciones").change(function(event){
                    var id = $("#cmb_segmentaciones").find(':selected').val();
                    $("#error_segmentacion").css("display", "none");

                    if(id=="1"){
                        $("#canales_tematicos").css("display", "none");
                        $("#sitios_especificos").css("display", "none");
                    }else if(id=="2"){
                        $("#canales_tematicos").css("display", "block");
                        $("#sitios_especificos").css("display", "none");
                    }else if(id=="3"){
                        $("#canales_tematicos").css("display", "none");
                        $("#sitios_especificos").css("display", "block");
                        if(mostrar_listado_sitios == false){
                            $("#loader_cmb_sitios").css("display", "inline");
                            $("#btn_pasar_sitio_del_text").attr("disabled", "disabled");
                            $("#txt_sitios_web").attr("disabled", "disabled");
                            $("#cmb_sitios").attr("disabled", "disabled");
                            $("#cmb_sitios_2").attr("disabled", "disabled");
                            $("#btn_borrar_sitio").attr("disabled", "disabled");
                            $("#btn_pasar_sitio").attr("disabled", "disabled");
                        }
                    }

                    $('#formato').change();
                });


                /***************** CHECKBOX FORMATOS *********************/
                $('input[type="checkbox"]').click(function(){
                    if( $(this).attr('name') == 'chk_formatos[]' ){
                        var rel = $(this).attr('rel');

                        // traigo el valor correspondiente.
                        if($(this).attr('checked') == 'checked'){
                            var formato = $('select[id="modalidad_'+rel+'"]').attr('formato');
                            var segmentacion = $('#cmb_segmentaciones').val();
                            var modalidad = $('select[id="modalidad_'+rel+'"]').val();

                            var select = $('select[name="modalidad_'+rel+'"]');

                            $('#loader_tamano_'+rel).css('display','inline');

                            $('#monto_oculto_'+rel).load('/tarifario/get_valor/'+formato+'/'+segmentacion+'/'+modalidad, function(monto){
                                monto = monto.replace('"','');
                                monto = monto.replace('"','');
                                monto = monto.replace('\\','');

                                if($('#monto_'+rel).val() == ''){
                                    $('#monto_oculto_'+rel).val(monto);
                                    $('#monto_'+rel).val(monto);
                                }

                                if( monto == 'N/A' ){
                                    $('#monto_'+rel).attr('disabled', 'disabled');
                                    $('#cantidad_'+rel).attr('disabled', 'disabled');

                                    alert('Mensaje de que no corresponde la modalidad en el formato seleccionado.');
                                    $(select).attr('acepta','false');
                                }else{
                                    $('#monto_'+rel).attr('disabled', false);
                                    $('#cantidad_'+rel).attr('disabled', false);
                                    $(select).attr('acepta','true');
                                }

                                //$('#inversion_bruta').val( calcular_bruto() );

                                $('#loader_tamano_'+rel).css('display','none');
                            });
                        }

                        // si clikie en todos los tipos
                        if(rel == '0'){
                            if($(this).attr('checked') == 'checked'){
                                // bloqueo todos los elementos y al finalizar activo los de "Todos los tipos"
                                $('select[name="modalidad"]').each(function(){
                                    $(this).attr('disabled','disabled');
                                });
                                $('input[name="monto"]').each(function(){
                                    $(this).attr('disabled','disabled');
                                });
                                $('input[name="cantidad"]').each(function(){
                                    $(this).attr('disabled','disabled');
                                });

                                $('input[type="checkbox"]').each(function(){
                                    if( $(this).attr('name') == 'chk_formatos[]' ){
                                        $(this).attr('checked',false);
                                    }
                                });

                                $('input[rel="0"]').attr('checked','checked');

                                $('#modalidad_0').each(function(){
                                    $(this).attr('disabled',false);
                                });
                                $('#monto_0').each(function(){
                                    $(this).attr('disabled',false);
                                });
                                $('#cantidad_0').each(function(){
                                    $(this).attr('disabled',false);
                                });
                            }else{
                                // bloqueo los elementos de "Todos los tipos"
                                $('select[name="modalidad"]').each(function(){
                                    $(this).attr('disabled','disabled');
                                });
                                $('input[name="monto"]').each(function(){
                                    $(this).attr('disabled','disabled');
                                });
                                $('input[name="cantidad"]').each(function(){
                                    $(this).attr('disabled','disabled');
                                });
                            }
                        }else{
                            if($(this).attr('checked') == 'checked'){
                                // si selecciono otro entonces bloqueo "Todos los tipos" y habilito el seleccionado
                                $('#modalidad_0').each(function(){
                                    $(this).attr('disabled','disabled');
                                });
                                $('#monto_0').each(function(){
                                    $(this).attr('disabled','disabled');
                                });
                                $('#cantidad_0').each(function(){
                                    $(this).attr('disabled','disabled');
                                });

                                $('input[rel="0"]').attr('checked',false);

                                $('#modalidad_' + rel).each(function(){
                                    $(this).attr('disabled',false);
                                });
                                $('#monto_' + rel).each(function(){
                                    $(this).attr('disabled',false);
                                });
                                $('#cantidad_' + rel).each(function(){
                                    $(this).attr('disabled',false);
                                });
                            }else{
                                $('#modalidad_' + rel).each(function(){
                                    $(this).attr('disabled', 'disabled');
                                });
                                $('#monto_' + rel).each(function(){
                                    $(this).attr('disabled','disabled');
                                });
                                $('#cantidad_' + rel).each(function(){
                                    $(this).attr('disabled','disabled');
                                });
                            }
                        }

                        // recorro todos los checks y los que estan activos calculo la inversion neta y bruta
                        //$('#inversion_bruta').val( calcular_bruto() );

                        //calcular_neto();
                    }
                });


                $('select[name="modalidad"]').change(function(){
                    var rel = $(this).attr('rel');
                    var formato = $(this).attr('formato');
                    var segmentacion = $('#cmb_segmentaciones').val();
                    var modalidad = $(this).val();

                    var select = this;

                    $('#loader_tamano_'+rel).css('display','inline');

                    $('#monto_oculto_'+rel).load('/tarifario/get_valor/'+formato+'/'+segmentacion+'/'+modalidad, function(monto){
                        monto = monto.replace('"','');
                        monto = monto.replace('"','');
                        monto = monto.replace('\\','');

                        $('#monto_oculto_'+rel).attr('value', monto);
                        $('#monto_'+rel).val(monto);

                        if( monto == 'N/A' ){
                            $('#monto_'+rel).attr('disabled', 'disabled');
                            $('#cantidad_'+rel).attr('disabled', 'disabled');

                            alert('Mensaje de que no corresponde la modalidad en el formato seleccionado.');
                            $(select).attr('acepta','false');
                        }else{
                            $('#monto_'+rel).attr('disabled', false);
                            $('#cantidad_'+rel).attr('disabled', false);
                            $(select).attr('acepta','true');
                        }

                        //$('#inversion_bruta').val( calcular_bruto() );

                        $('#loader_tamano_'+rel).css('display','none');
                    });
                });


                /********************************* PAISES *********************************/

                // cargo los paises en el listado.
                var paises = '';
                var form_data = {};

                $.ajax({
                    type: "GET",
                    url: "/campania/get_paises_json/",
                    data: form_data,
                    dataType: "json",
                    success: function(msg){
                        paises = eval(msg);

                        for (var campo in paises ){
                            $("#cmb_paises").append('<option value="'+paises[campo].id+'">'+paises[campo].descripcion+'</option>');
                        }
                    }
                });

                $("#txt_paises").keyup( function(event){
                    $('#cmb_paises').html('');

                    var busqueda = $(this).val();

                    if(busqueda != ''){
                        for (var campo in paises ){
                            var agregar = true;
                            if(strpos( paises[campo].descripcion, busqueda ) != false){
                                if($('#cmb_paises_2 option').length > 0){
                                    $('#cmb_paises_2 option').each(function(){
                                        if( paises[campo].descripcion == $(this).text() ){
                                            agregar = false;
                                        }
                                    });

                                    if(agregar){
                                        $("#cmb_paises").append('<option value="'+paises[campo].id+'">'+paises[campo].descripcion+'</option>');
                                    }
                                }else{
                                    $("#cmb_paises").append('<option value="'+paises[campo].id+'">'+paises[campo].descripcion+'</option>');
                                }
                            }
                        }
                    }else{
                        if($('#cmb_paises_2 option').length > 0){
                            for (var campo in paises ){
                                $('#cmb_paises_2 option').each(function(){
                                    if( paises[campo].descripcion != $(this).text() ){
                                        $("#cmb_paises").append('<option value="'+paises[campo].id+'">'+paises[campo].descripcion+'</option>');
                                    }
                                });
                            }
                        }else{
                            for (var campo in paises ){
                                $("#cmb_paises").append('<option value="'+paises[campo].id+'">'+paises[campo].descripcion+'</option>');
                            }
                        }
                    }
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

                $.datepicker.setDefaults($.datepicker.regional['es']);
                $("#fecha_inicio").datepicker({ dateFormat:'dd-mm-yy' });
                $("#fecha_fin").datepicker({ dateFormat:'dd-mm-yy' });


                /***************************** CREAR CAMPAÑA *****************************/
                $('#enviar').click(function(e){
                    e.preventDefault();

                    $('#loader_submit').fadeIn("fast");
                    $(this).attr('disabled', 'disabled');

                    // oculto los errores
                    $(".msg_error").css("display", "none");

                    var error = false;

                    // valido el anunciante
                    var id_anunciante = $.trim($('#id_anunciante').val());
                    if( id_anunciante == 0 ){
                        $("#error_anunciante").html('Por favor seleccione un anunciante para la campaña.').css("display", "inline");
                        error = true;
                    }

                    // valido nombre de la campania
                    var nombre_campania = $.trim($('#nombre_campania').val());
                    if( nombre_campania.length <= 0 ){
                        $("#error_nombre_campania").html('Por favor indique un nombre para la campaña.').css("display", "inline");
                        error = true;
                    }

                    var segmentacion = $('#cmb_segmentaciones').val();

                    var id_canales_tematicos = "";

                    // valido que haya seleccionado al menos un canal cuando la sergmentación sea '2'
                    if(segmentacion == '2'){
                        $("input[name='chk_canales_tematicos[]']:checked").each(function(){
                            id_canales_tematicos = id_canales_tematicos + $(this).val() + ";";
                        });

                        if(id_canales_tematicos == ''){
                            $("#error_segmentacion").html('Por favor seleccione al menos un canal.').css("display", "inline");
                            error = true;
                        }
                    }else if(segmentacion == "3"){
                        var id_sitios = '';

                        $("#cmb_sitios_2 option").each(function(){
                            id_sitios = id_sitios + $(this).attr('value') + ";";
                        });

                        if(id_sitios == ''){
                            $("#error_segmentacion").html('Por favor seleccione al menos un sitio.').css("display", "inline");
                            error = true;
                        }
                    }

                    // valido los formatos seleccionados
                    var formatos ="";
                    $("input[name='chk_formatos[]']:checked").each(function(){
                        var id = $(this).val();
                        var rel = $(this).attr('rel');
                        var modalidad = $("#modalidad_"+rel).find(':selected').val();
                        var cantidad = $("#cantidad_"+rel).val();
                        var monto = $.trim($("#monto_"+rel).val());

                        if(isNaN(cantidad) || cantidad <= 0){
                            $("#error_formato").html('').css("display", "inline");
                            error = true;
                            formatos = 'a';

                            $("#cantidad_"+rel).css('borderColor', 'red');
                        }

                        if(isNaN(monto) || monto <= 0){
                            $("#error_formato").html('').css("display", "inline");
                            error = true;
                            formatos = 'a';
                            $("#monto_"+rel).css('borderColor', 'red');
                        }

                        formatos = formatos+id+":"+modalidad+":"+cantidad+":"+monto+";";
                    });

                    if(formatos == '' && error == false){
                        $("#error_formato").html('Por favor seleccione al menos un tamaño.').css("display", "inline");
                        error = true;
                    }

                    // valido los paises seleccionados
                    var id_paises = "";
                    $("#cmb_paises_2 option").each(function(){
                        id_paises = id_paises + $(this).attr('value') + ";";
                    });

                    if(id_paises == ''){
                        $("#error_paises").html('Por favor seleccione al menos un pais del listado.').css("display", "inline");
                        error = true;
                    }


                    //valido la fecha de inicio
                    var fecha_inicio = $.trim( $('#fecha_inicio').val() );
                    var fecha_fin = $.trim( $('#fecha_fin').val() );

                    if( fecha_inicio == '' || fecha_fin == '' ){
                        $("#error_fechas").html('Por favor ingrese la fecha de inicio y fin de la campaña.').css("display", "inline");
                        error = true;
                    }else{
                        $("#error_fechas").css("display", "none");
                    }

                    // si surgió algun error durante la validación.
                    if(error == true){
                        $('#loader_submit').fadeOut("fast", function(){
                            $("#error_campania").css("display", "inline");
                        });

                        $(this).attr('disabled', false);

                        return false;
                    }


                    var form_data = {
                        id_anunciante: id_anunciante,
                        nombre_campania: nombre_campania,
                        segmentacion: segmentacion,
                        id_canales_tematicos: id_canales_tematicos,
                        id_sitios: id_sitios,
                        formatos: formatos,
                        id_paises: id_paises,
                        fecha_inicio: fecha_inicio,
                        fecha_fin: fecha_fin
                    };

                    //alert(id_anunciante + '/' + nombre_campania + '/' + segmentacion + '/' + id_canales_tematicos + '/' + formatos + '/' + id_paises + '/' + fecha_inicio + '/' + fecha_fin);

                    $.ajax({
                        type: "POST",
                        url: "/campania/insertar/",
                        data: form_data,
                        dataType: "json",
                        success: function(msg){
                            $('#loader_submit').fadeOut("fast");

                            if(msg.validate){
                                $("#info").html('Redireccionando...');
                                window.location.replace("/campania/crear_campania_result/");
                            }else{
                                $("#info").html(msg.error);
                                $(this).attr('disabled', false);
                            }
                        }
                    });
                });
            });
        </script>
    </head>

    <body>
        <?php
        require_once 'application/views/top.php';
        ?>

        <form id="form_alta_campania" name="form_alta_campania" method="post" action="insertar">
            <input type="hidden" id="id_sitio" name="id_sitio" value=""/>
            <div>
                <label>Anunciante: </label>
                <span id="anunciante" style="font-size: 12px;">
                    <strong><?= $nombre_anunciante ?></strong>
                </span>
                <input type="hidden" name="id_anunciante" id="id_anunciante" value="0" />
                <span class="msg_error" style="display:none;" id="error_anunciante"></span>
            </div>

            <div>
                <label>Nombre de la campa&ntilde;a: </label>
                <input type="text" name="nombre_campania" maxlength="128" id="nombre_campania" value="" />
                <span class="msg_error" style="display:none;" id="error_nombre_campania"></span>
            </div>

            <div>
                <label>Segmentaci&oacute;n: </label>
                <select name="segmentacion" id="cmb_segmentaciones">
                    <?php
                    foreach ($segmentaciones as $row) {
                        echo '<option value="' . $row->id . '">' . $row->descripcion . '</option>';
                    }
                    ?>
                </select>
                <span id="loader_segmentaciones" style="display:none"><img src="/images/ajax-loader.gif" height="10px" /></span>
                <span id="loader_cmb_sitios" style="display:none"><img src="/images/ajax-loader.gif" height="10px" />
                    Cargando sitios, por favor espere
                </span>
                <span class="msg_error" style="display:none;" id="error_segmentacion"></span>
            </div>

            <table id="canales_tematicos" style="display:none;margin-bottom:25px;margin-left:150px;">
                <?php
                $i = 0;
                foreach ($canales_tematicos as $categorie) {
                    if ($i == 3) {
                        ?>
                        <tr>
                            <td><input type="checkbox" name="chk_canales_tematicos[]" value="<?= $categorie->id ?>"  />&nbsp;<?= $categorie->nombre ?></td>
                            <?php
                            $i = 1;
                        } elseif ($i == 0 || $i == 1) {
                            ?>
                            <td><input type="checkbox" name="chk_canales_tematicos[]" value="<?= $categorie->id ?>" />&nbsp;<?= $categorie->nombre ?></td>
                            <?php
                            $i++;
                        } else {
                            ?>
                            <td><input type="checkbox" name="chk_canales_tematicos[]" value="<?= $categorie->id ?>" />&nbsp;<?= $categorie->nombre ?></td>
                        </tr>
                        <?php
                        $i++;
                    }
                }
                ?>
            </table>

            <table cellpadding="0px" id="sitios_especificos" style="display:none;margin-bottom:25px;margin-left:150px;">
                <tr>
                    <td colspan="3">
                        Buscar: <input size="23" type="text" id="txt_sitios_web" name="q" disabled="disabled" style="width:158px !important;" />
                        <span id="loader_comprobar_sitios" style="display:none"><img src="/images/ajax-loader.gif" /> Comprobando...</span>
                    </td>
                </tr>
                <tr>
                    <td style="width: 200px">
                        <select style="width:201px;height:170px;" size="10" id="cmb_sitios" name="cmb_sitios" disabled="disabled" multiple="multiple">
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
                        <select style="width:200px;height:170px;" size="10" id="cmb_sitios_2" name="cmb_sitios_2" disabled="disabled" multiple="multiple">
                        </select>
                    </td>
                </tr>
            </table>

            <div>
                <label>Tama&ntilde;o: </label>
                <span class="msg_error" style="display:none;" id="error_formato">Por favor seleccione un tipo de formato y asigne la cantidad deseada.</span>
            </div>

            <div id="mostrar_formatos" style="margin-left:140px;margin-bottom:25px;margin-top:-5px">
                <table id="personalizar_formatos">
                    <tr>
                        <th style="padding-right: 20px;">Tipo</th>
                        <th style="padding-right: 20px;">Modalidad de compra</th>
                        <th>Valor</th>
                        <th style="padding-right: 20px;">Cantidad</th>
                    </tr>

                    <?php
                    $a = 1;
                    foreach ($formatos as $formato) {
                        if ($formato->id != 9 && $formato->id != 12) {
                            ?>
                            <tr>
                                <td style="padding-right: 20px;">
                                    <input type="checkbox" name="chk_formatos[]" value="<?= $formato->id ?>" rel="<?= $a ?>" />&nbsp;<?= $formato->descripcion ?>
                                </td>
                                <td style="padding-right: 20px;">
                                    <select name="modalidad" id="modalidad_<?= $a ?>" style="width:104px !important;" formato="<?= $formato->id ?>" acepta="true"  rel="<?= $a ?>" disabled="disabled">
                                        <option selected="selected" value="CPM">CPM</option>
                                        <option value="CPC">CPC</option>
                                    </select>
                                </td>
                                <td style="padding-right: 20px;">
                                    US$ <input type="text" name="monto" style="width:50px !important;" id="monto_<?= $a ?>" value="" size="5" disabled="disabled" rel="<?= $a ?>" />
                                    <input type="hidden" name="monto_oculto" id="monto_oculto_<?= $a ?>" value="" />
                                </td>
                                <td style="padding-right: 20px;">
                                    <input type="text" name="cantidad" style="width:50px !important;" id="cantidad_<?= $a ?>" value="0" size="5" rel="<?= $a ?>" disabled="disabled" />
                                    <img src="/images/ajax-loader.gif" height="10px" id="loader_tamano_<?= $a ?>" style="display:none" />
                                </td>
                            </tr>
                            <?php
                            $a++;
                        }
                    }
                    ?>
                </table>
            </div>

            <div>
                <div id="paises_filtrados"></div>
                <label>Pa&iacute;s o Paises: </label>
                <table style="margin-left:140px !important;">
                    <tr>
                        <td colspan="3">
                            Buscar: <input size="23" type="text" id="txt_paises" name="q" style="width:158px !important;" />
                            <span class="msg_error" style="display:none;" id="error_paises">Por favor seleccione al menos un pais del listado.</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <select style="width: 201px;height:170px;" size="10" id="cmb_paises" name="cmb_paises" multiple="multiple">
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
                            <select style="width: 201px;height:170px;" size="10" id="cmb_paises_2" name="cmb_paises_2" multiple="multiple">
                            </select>
                        </td>
                    </tr>
                </table>
            </div>

            <div>
                <label>Fecha: </label>
                <input type="text" name="fecha_inicio" id="fecha_inicio" value="" style="width:140px !important;" /> al
                <input type="text" name="fecha_fin" id="fecha_fin" value="" style="width:140px !important;" />
                <span class="msg_error" style="display:none;" id="error_fechas">Por favor ingrese la fecha de inicio y fin de la campa&ntilde;a.</span>
            </div>

            <?php
            if ($tarjeta_usuario) {
                ?>
                <div class="alerta">
                    Al crear la campaña se debitar&aacute;n U$S 5.00 de su tarjeta de cr&eacute;dito.
                </div>

                <div style="margin-top: 20px;">
                    <input type="submit" name="enviar" id="enviar" value="Aceptar" class="button_new" />

                    <span id="info" style="display:none"></span>

                    <span id="loader_submit" style="display:none">
                        <img src="/images/ajax-loader.gif" height="10px" />
                        Almacenando campa&ntilde;a, espere por favor...
                    </span>

                    <span class="msg_error" style="display:none;" id="error_campania">Se encontraron errores al intentar crear la campa&ntilde;a.</span>
                </div>

                <?php
            } else {
                ?>
                <div class="alerta">
                    Antes de crear una campaña debe dar de alta los datos de su tarjeta de cr&eacute;dito <a href="/micuenta">aqu&iacute;</a>.
                </div>
                <?php
            }
            ?>
        </form>

        <?php require_once 'application/views/footer.php'; ?>
    </body>
</html>