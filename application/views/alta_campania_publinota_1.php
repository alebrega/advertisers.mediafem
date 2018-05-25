<style type="text/css">
    label{
        font: normal bold 12px Arial, Helvetica, sans-serif;
        width: 180px;
        display: inline-block;
    }

    select, input[type="text"]{
        /*border: 1px solid #C7C7C7;*/
        -moz-border-radius: 2px; /* Firefox*/
        -ms-border-radius: 2px; /* IE 8.*/
        -webkit-border-radius: 2px; /* Safari,Chrome.*/
        border-radius: 2px; /* El estándar.*/

        /*color: #333;*/
        font: normal normal 11px Arial, Helvetica, sans-serif;
        padding: 3px 3px 3px 0;
        width: 303px;
    }

    input[type="text"]{
        padding: 4px 2px !important;
        width: 297px !important;
    }

    hr{
        border: none;
        border-bottom:1px solid #ddd;
        margin:20px 0 20px;
    }

    .ocultar{
        display: none;
        margin-bottom: 20px;
    }

    .ocultar label, #label_nombre_anunciante{
        margin-bottom: 15px !important;
    }

    .msg_error {
        color: red;
        display: none;
    }

    #lbl_inversion_total label{
        width: 100% !important;
        text-align: left;
        margin: 20px 18px;
    }

    .mceToolbarRow2, .mceToolbarRow3, .mceStatusbar{
        display: none;
    }

    #textarea_parent, .mceEditor{
        display: block;
        margin-left: 205px;
    }

    .contenido_publinota iframe{
        height: 300px !important;
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

    $(document).ready(function(){
        var tipo_campana = 'publinota';
        var id_campana = 0;
        var id_anunciante = $("#cmb_anunciantes").find(':selected').val();

        var cantidad_links_insertados = 0;

        var habilitar_descuentos = <?= $habilitar_descuentos ?>;

        $.datepicker.setDefaults($.datepicker.regional['es']);
        $("#fecha_inicio").datepicker({ dateFormat:'dd-mm-yy' });
        $("#fecha_fin").datepicker({ dateFormat:'dd-mm-yy' });


        tinyMCE.init({
            // General options
            mode : "textareas",
            theme : "advanced",

            width: 918,

            // Theme options
            theme_advanced_buttons1 : "bold,italic,underline,strikethrough,link",
            theme_advanced_toolbar_location : "top",
            theme_advanced_toolbar_align : "left",
            theme_advanced_statusbar_location : "bottom"
        });

        $('.mceToolbarRow2').css('display', 'none');
        $('.mceToolbarRow3').css('display', 'none');
        $('.mceStatusbar').css('display', 'none');

        $('#textarea_link').click( function(){
            cantidad_links_insertados = cantidad_links_insertados + 1;

            alert(cantidad_links_insertados);
        });

        // SE USA PARA FILTRAR PAISES Y SITIOS
        function strpos(cadena, busqueda){
            var i = (cadena.toLowerCase()).indexOf(busqueda.toLowerCase());
            return i === -1 ? false : true;
        }


        // SITIOS ESPECIFICOS **************************************************
        var mostrar_listado_sitios = false;
        var cantidad_sitios = 0;
        var sitios = '';

        var form_data = {};

        $.ajax({
            type: "GET",
            url: "/campania/get_publishers_y_sitios_json/",
            data: form_data,
            dataType: "json",
            success: function(msg){
               if(msg){
                   sitios = eval(msg);
                   cantidad_sitios = sitios.length;

                   for(var a = 0; a < cantidad_sitios; a++){
                       if(sitios[a].url != '')
                           $("#cmb_sitios").append('<option value="' + sitios[a].id_sitio + '">' + sitios[a].url + '</option>');
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
                var cantidad_sitios = sitios.length;

                for(var a = 0; a < cantidad_sitios; a++){
                    if(strpos( sitios[a].url, busqueda ) != false)
                        $("#cmb_sitios").append('<option value="' + sitios[a].id_sitio + '">' + sitios[a].url + '</option>');
                }
            }else{
                var cantidad_sitios = 0;
                cantidad_sitios = sitios.length;

                for(var a = 0; a < cantidad_sitios; a++){
                    $("#cmb_sitios").append('<option value="' + sitios[a].id_sitio + '">' + sitios[a].url + '</option>');
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
            $('#cmb_sitios option:selected').appendTo("#cmb_sitios_2");
        });

        $("#btn_borrar_sitio").click( function (){
            var seleccionados = $('#cmb_sitios_2 option:selected');
            for(var a = 0; a < seleccionados.length; a++){
                $(seleccionados[a]).appendTo("#cmb_sitios");
            }
        });

        // BOTONES DE ATRAS *******************************************************
        $('#btn_hacia_paso_1').click(function(e){
            $('#nueva_campana').html('').append(divLoader).load('/campania/crear/');
        });

        $('#btn_hacia_paso_2').click(function(){
            $("#paso_3").css("display", "none");
            $("#paso_2").css("display", "block");

            $(".div_paso_3").removeClass("activo");
            $(".div_paso_2").addClass("activo");
        });

        $('#btn_hacia_paso_3').click(function(){
            $("#paso_4").css("display", "none");
            $("#paso_3").css("display", "block");

            $(".div_paso_4").removeClass("activo");
            $(".div_paso_3").addClass("activo");
        });

        $('#btn_hacia_paso_4').click(function(){
            $("#paso_5").css("display", "none");
            $("#paso_4").css("display", "block");

            $(".div_paso_5").removeClass("activo");
            $(".div_paso_4").addClass("activo");
        });

        $('#btn_hacia_paso_5').click(function(){
            $("#paso_6").css("display", "none");
            $("#paso_5").css("display", "block");

            $(".div_paso_6").removeClass("activo");
            $(".div_paso_5").addClass("activo");
        });

        // PASO DOS ***************************************************************
        $('#crear_anunciante').click( function(){
            $('#list_anunciante').fadeOut(0);
            $('#nuevo_anunciante').fadeIn('fast');

            id_anunciante = 0;
        });

        $("#cmb_anunciantes").change(function(){
            id_anunciante = $("#cmb_anunciantes").find(':selected').val();
        });


        $('#cancelar_nuevo_anunciante').click( function(){
            $('#nuevo_anunciante').fadeOut(0);
            $('#list_anunciante').fadeIn('fast');

            $('#loader_anunciantes').css('display', 'none');
            $('#aceptar_nuevo_anunciante').attr('disabled', false);
            $('#new_anunciante').attr('disabled', false);
            $('#new_anunciante').val('');
            id_anunciante = 0;

            $("#error_anunciante").css('display', 'none');
        });

        function btn_paso_2(){
            var error = false;
            $("#error_paso_1").css("display", "none");
            $("#loader_btn_paso_1").css("display", "inline");

            $("#error_nombre_campania").html(' ');

            $('.ui-icon-circle-check').click();

            // valido nombre de la campania
            var nombre_campania = $.trim($('#nombre_campania').val());
            if( nombre_campania.length <= 0 ){
                $("#error_nombre_campania").html('Por favor indique un nombre para la campaña.').css("display", "inline");
                error = true;
            }

            var tipo_campania = tipo_campana;

            //valido la fecha de inicio
            var fecha_inicio = $.trim( $('#fecha_inicio').val() );
            var fecha_fin = $.trim( $('#fecha_fin').val() );

            if( fecha_inicio == '' || fecha_fin == '' ){
                $("#error_fechas").html('Por favor ingrese la fecha de inicio y fin de la campaña.').css("display", "inline");
                error = true;
            }else{
                $("#error_fechas").css("display", "none");
            }

            var mantener_publicada = 0;

            if($('#chk_mantener_publicada').is(":checked")){
                mantener_publicada = 1;
            }

            if(error == true){
                $(this).attr('disabled', false);

                $("#loader_btn_paso_1").css("display", "none");

                return false;
            }

            var form_data = {
                id_anunciante: id_anunciante,
                tipo_campania: tipo_campania,
                nombre_campania: nombre_campania,
                fecha_inicio: fecha_inicio,
                fecha_fin: fecha_fin,
                mantener_publicada: mantener_publicada
            };

            $.ajax({
                type: "POST",
                url: "/campania/insertar_publinota_primer_paso/",
                data: form_data,
                dataType: "json",
                success: function(msg){
                    if(msg.validate){
                        id_campana = msg.id_campania;

                        $("#loader_btn_paso_1").css("display", "none");

                        $("#lbl_inversion_total label span.fecha_inicio").html(fecha_inicio);
                        $("#lbl_inversion_total label span.fecha_fin").html(fecha_fin);

                        $("#paso_2").css("display", "none");
                        $("#paso_3").css("display", "inline");

                        //Habilitar 3 Paso
                        $(".div_paso_2").removeClass("activo");
                        $(".div_paso_3").addClass("activo");
                    }else{
                        $("#error_paso_1").html(msg.error);
                        $("#error_paso_1").css("display", "inline");
                        $("#loader_btn_paso_1").css("display", "none");
                    }
                }
            });
        }

        // HACIA PASO TRES ********************************************************
        $('#btn_paso_2').click(function(e){
            if($('#nuevo_anunciante').is(':visible')){
                // CREO EL ANUNCIANTE
                $('#new_anunciante').attr('disabled', 'disabled');
                $('#error_anunciante').css('color', 'red');
                $('#error_anunciante').html('');
                $('#loader_anunciantes').css('display', 'inline');

                var nombre = $.trim( $('#new_anunciante').val() );

                if( nombre == '' ){
                    $('#error_anunciante').html('Por favor, indique el nombre de anunciante.');
                    $('#error_anunciante').css('display', 'inline');

                    $(this).attr('disabled', false);
                    $('#new_anunciante').attr('disabled', false);
                    $('#loader_anunciantes').css('display', 'none');

                    $('#new_anunciante').focus();
                    return false;
                }

                var form_data = { nombre: nombre };

                $.ajax({
                    type: "POST",
                    url: "/campania/alta_anunciante_json/",
                    data: form_data,
                    dataType: "json",
                    success: function(msg){
                        $('#loader_anunciantes').css('display', 'none');
                        if( msg.validate ){
                            id_anunciante = msg.id_anunciante;
                            $('#error_anunciante').html(' ');
                            $('#new_anunciante').attr('disabled', 'disabled');
                            $('#aceptar_nuevo_anunciante').attr('disabled', 'disabled');
                            $('#cancelar_nuevo_anunciante').attr('disabled', 'disabled');

                            $('#error_anunciante').html('Anunciante creado correctamente.');
                            $('#error_anunciante').css({'display': 'inline', 'color': 'green'}).delay(1500).fadeOut('slow');

                            $('#aceptar_nuevo_anunciante').css('display', 'none');
                            $('#cancelar_nuevo_anunciante').css('display', 'none');

                            btn_paso_2();

                        }else{
                            id_anunciante = 0;

                            $('#error_anunciante').html(msg.error);
                            $('#error_anunciante').css('display', 'inline');

                            $(this).attr('disabled', false);
                            $('#new_anunciante').attr('disabled', false);
                            $('#aceptar_nuevo_anunciante').attr('disabled', false);
                            $('#loader_anunciantes').css('display', 'none');

                            $('#new_anunciante').focus();
                        }
                    }
                });
            }else{
                btn_paso_2();
            }
        });

        // HACIA PASO CUATRO ******************************************************
        $('#btn_paso_3').click(function(e){
            e.preventDefault();

            $('#loader_btn_paso_3').fadeIn("fast");

            $("#loader_btn_paso_3").css("display", "inline");
            $("#error_paso_3").html(' ');

            var error = false;

            var id_sitios = "";

            // valido que haya seleccionado al menos un canal cuando la sergmentación sea '2'
            $("#cmb_sitios_2 option").each(function(){
                id_sitios = id_sitios + $(this).attr('value') + ";";
            });

            if(id_sitios == ''){
                $("#error_segmentacion").html('Por favor seleccione al menos un sitio.').css("display", "inline");
                error = true;
            }

            // si surgió algun error durante la validación.
            if(error == true){
                $('#loader_btn_paso_3').fadeOut("fast", function(){
                    $("#error_paso_3").css("display", "inline");
                });

                $(this).attr('disabled', false);

                return false;
            }

            var form_data = {
                id_campania : id_campana,
                id_sitios : id_sitios
            };

            $.ajax({
                type: "POST",
                url: "/campania/insertar_publinota_segundo_paso/",
                data: form_data,
                dataType: "json",
                success: function(msg){
                    if(msg.validate){
                        //Habilitar 4 Paso
                        $("#paso_3").css("display", "none");
                        $("#paso_4").css("display", "inline");

                        $("#loader_btn_paso_3").css("display", "none");

                        $('#inversion_neta').val(msg.precio_publinota)

                        $(".div_paso_3").removeClass("activo");
                        $(".div_paso_4").addClass("activo");
                    }else{
                        $("#loader_btn_paso_3").css("display", "none");
                    }
                }
            });
        });

        // HACIA EL PASO 5 ********************************************************
        $('#btn_paso_4').click(function(){
            $('#error_paso_4').html('');
            $("#loader_btn_paso_4").css('display', 'inline');

            var inversion_neta = $.trim($('#inversion_neta').val());

            var form_data = {
                id_campania : id_campana,
                inversion_neta: inversion_neta
            };

            $.ajax({
                type: "POST",
                url: "/campania/insertar_publinota_tercer_paso/",
                data: form_data,
                dataType: "json",
                success: function(msg){
                    if(msg.validate){
                        $("#loader_btn_paso_4").css("display", "none");

                        //Habilitar 5 Paso
                        $("#paso_4").css("display", "none");
                        $("#paso_5").css("display", "inline");

                        $(".div_paso_4").removeClass("activo");
                        $(".div_paso_5").addClass("activo");
                    }
                }
            });
        });

        function dump(obj) {
            var out = '';
            for (var i in obj) {
                out += i + ": " + obj[i] + "\n";
            }

            var pre = document.createElement('pre');
            pre.innerHTML = out;
            document.body.appendChild(pre)
        }

        // HACIA EL RESUMEN *******************************************************
        $('#btn_paso_5').click(function(){
            $('#error_paso_5').html('');
            $("#loader_btn_paso_5").css('display', 'inline');

            // GUARDAMOS LA IMAGEN ************************************************
            var inputFileImage = document.getElementById('archivoImage');

            var file = inputFileImage.files[0];

            var data = new FormData();

            data.append('archivo',file);

            $.ajax({
                url: "/campania/subir_imagen_publinota/",
                type: 'POST',
                contentType: false,
                data: data,
                processData: false,
                cache: false
            }).done(function(msg){

                var msg = jQuery.parseJSON( msg );

                var file_name = msg.file_name;

                if(!msg.validate){
                    //$('#error_paso_5').html('No se pudo subir la imagen indicada.');
                    $('#error_paso_5').html(msg.error);

                    $('#loader_btn_paso_5').fadeOut("fast", function(){
                        $("#error_paso_5").css("display", "inline");
                    });

                    $(this).attr('disabled', false);

                    return false;
                }


                // GUARDAMOS LOS DEMAS DATOS ******************************************
                var titulo = $.trim($('#titulo').val());
                var mensaje = $.trim(tinyMCE.get('textarea').getContent());

                var form_data = {
                    id_campania : id_campana,
                    titulo : titulo,
                    mensaje: mensaje,
                    imagen: file_name
                };

                $.ajax({
                    type: "POST",
                    url: "/campania/insertar_publinota_cuarto_paso/",
                    data: form_data,
                    dataType: "json",
                    success: function(msg){
                        if(msg.validate){
                            $('#detalle_publinota').load('campania/ver_publinota/' + id_campana, function(){
                                $("#loader_btn_paso_5").css("display", "none");

                                //Habilitar 5 Paso
                                $("#paso_5").css("display", "none");
                                $("#paso_6").css("display", "inline");

                                $(".div_paso_5").removeClass("activo");
                                $(".div_paso_6").addClass("activo");
                            });
                        }else{
                            $("#loader_btn_paso_5").css("display", "none");
                            $("#error_paso_5").html(msg.error);
                        }
                    }
                });

            });
        });

        $('#btn_finalizar').click(function(e){
            e.preventDefault();

            var form_data = {
                id_campania : id_campana
            };

            $.ajax({
                type: "POST",
                url: "/campania/insertar_publinota_quinto_paso/",
                data: form_data,
                dataType: "json",
                success: function(msg){
                    if(msg.validate){
                        $("#loader_btn_paso_6").css("display", "inline");
                        $(this).attr('disabled', 'disabled');

                        window.location.replace("/campania?creada_ok=true");
                        return false;
                    }
                }
            });
        });
    });
</script>

<input type="hidden" name="id_anunciante" id="id_anunciante" value="0" />

<!-- Pasos list -->
<div id="pasos">
    <span class="div_paso_1">Formato de la campa&ntilde;a</span>
    <span class="div_paso_2 activo">Datos de la campa&ntilde;a</span>
    <span class="div_paso_3">Segmentaci&oacute;n</span>
    <span class="div_paso_4">Inversi&oacute;n</span>
    <span class="div_paso_5">Dise&ntilde;o</span>
    <span class="div_paso_6">Orden de compra</span>
</div>

<!-- paso_2 -->
<div id="paso_2">
    <div class="row">
        <label>Anunciante:</label>
        <span id="list_anunciante">
            <?php if (sizeof($anunciantes_adserver)) { ?>
                <select name="cmb_anunciantes" id="cmb_anunciantes" style="width: 300px;">
                    <?php
                    foreach ($anunciantes_adserver as $row) {
                        if($row->adserver_actual == 0)
                            echo '<option value="' . $row->id . '">' . $row->nombre . '</option>';
                    }
                    ?>
                </select>
                <?php
            }
            ?>

            <input type="button" name="crear_anunciante" id="crear_anunciante" value="Nuevo anunciante" class="button_new" />
        </span>

        <span id="nuevo_anunciante" style="display:none;">
            <input type="text" name="new_anunciante" id="new_anunciante" maxlength="127" value="" />
            <!--<input type="button" name="aceptar_nuevo_anunciante" id="aceptar_nuevo_anunciante" value="Crear" class="button_new" />-->
            <input type="button" name="cancelar_nuevo_anunciante" id="cancelar_nuevo_anunciante" value="Cancelar" class="button_new" />
        </span>

        <img src="/images/ajax-loader.gif" id="loader_anunciantes" height="10" style="display:none" />
        <span class="msg_error" style="display:none;" id="error_anunciante"></span>
    </div>

    <div class="row">
        <label>Nombre de la campa&ntilde;a: </label>
        <input type="text" name="nombre_campania" maxlength="128" id="nombre_campania" value="" />
        <span class="msg_error" style="display:none;" id="error_nombre_campania"></span>
        <div style="font-style: italic; margin: 5px 0 0 205px; font-size:0.8em">
            Recomendamos que elija un nombre que sea f&aacute;cilmente identificable.<br />
            Es una buena practica agregar el nombre del pa&iacute;s o regi&oacute;n al nombre de la campa&ntilde;a o al anunciante.
        </div>
    </div>

    <div class="row">
        <label>Fecha de publicaci&oacute;n esperada: </label>
        <input type="text" name="fecha_inicio" id="fecha_inicio" value="" style="width:140px !important;" /> al
        <input type="text" name="fecha_fin" id="fecha_fin" value="" style="width:140px !important;" />
        <span class="msg_error" style="display:none;" id="error_fechas">Por favor ingrese la fecha de inicio y fin de la campa&ntilde;a.</span>
    </div>

    <div class="row">
        <input type="checkbox" name="chk_mantener_publicada" id="chk_mantener_publicada" checked="checked" value="1" /> Autorizo a que mi publinota se mantenga publicada mas alla de la fecha de fin (Recomendado).
    </div>

    <hr />

    <div class="row">
        <input type="button" class="button_new superButton" value="<<< Atr&aacute;s" id="btn_hacia_paso_1" />

        <input type="button" class="button_new superButton" value="Siguiente paso >>>" id="btn_paso_2" />
        <span class="msg_error" style="display:none;" id="error_paso_1"></span>
        <img id="loader_btn_paso_1" src="/images/ajax-loader.gif" height="10px" style="display:none" />
    </div>
</div>

<!-- paso_3 -->
<div id="paso_3" style="display:none;">

    <!-- SEGMENTACION -->
    <div class="row" style="display: none;">
        <label>Segmentaci&oacute;n: </label>
        <select name="segmentacion" id="cmb_segmentaciones" disabled="disabled">
            <option value="3">Sitio específico</option>
        </select>
        <span id="loader_cmb_sitios" style="display:none">
            <img src="/images/ajax-loader.gif" height="10px" />
            Cargando sitios, por favor espere
        </span>
        <span class="msg_error" style="display:none;" id="error_segmentacion"></span>
    </div>

    <div class="row">
        <b>Por favor elija uno o varios sitios donde desea colocar la publinota.</b>
    </div>

    <table cellpadding="0px" id="sitios_especificos" style="margin-bottom:25px;margin-left:25px;">
        <tr>
            <td colspan="3">
                Buscar: <input size="23" type="text" id="txt_sitios_web" name="q" disabled="disabled" style="width:143px !important; margin-bottom:5px;" />
                <span id="loader_comprobar_sitios" style="display:none"><img src="/images/ajax-loader.gif" /> Comprobando...</span>
                <span id="loader_cmb_sitios" style="display:none">
                    <img src="/images/ajax-loader.gif" height="10px" />
                    Cargando sitios, por favor espere
                </span>
                <span class="msg_error" style="display:none;" id="error_segmentacion"></span>
            </td>
        </tr>
        <tr>
            <td style="width: 200px">
                <select style="width:201px;height:170px;" size="10" id="cmb_sitios" name="cmb_sitios" disabled="disabled" multiple="multiple">
                </select>
            </td>
            <td style="width: 10px; vertical-align: middle !important; padding:0 10px;">
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

    <hr />

    <div class="row">
        <input type="button" class="button_new superButton" value="<<< Atr&aacute;s" id="btn_hacia_paso_2" />

        <input type="button" class="button_new superButton" value="Siguiente paso >>>" id="btn_paso_3" />
        <span class="msg_error" style="display:none;" id="error_paso_3"></span>
        <img id="loader_btn_paso_3" src="/images/ajax-loader.gif" height="10px" style="display:none" />
    </div>
</div>

<!-- paso_4 -->
<div id="paso_4" style="display:none;">
    <div class="row">
        <label>Precio por publicaci&oacute;n por Sitio:</label>

        <?= $this->user_data->moneda ?>
        <input type="text" name="inversion_neta" id="inversion_neta" value="0" style="width:100px !important;" />

        <div style="font-style: italic; margin: 5px 0 0 205px; font-size:0.8em">
            Mientras mayor sea valor el precio que defina , mas chances tendr&aacute; de que el sitio acepte la publinota.
        </div>
    </div>

    <div class="row" id="lbl_inversion_total">
        <label>Esta publinota estar&aacute; activa hasta el <span class="fecha_fin"></span>, a menos que desee que el sitio no la borre.</label>
    </div>

    <hr />

    <div class="row">
        <input type="button" class="button_new superButton" value="<<< Atr&aacute;s" id="btn_hacia_paso_3" />

        <input type="button" class="button_new superButton" value="Siguiente paso >>>" id="btn_paso_4" />
        <span class="msg_error" id="error_paso_4" style="display: inline-block;"></span>
        <img id="loader_btn_paso_4" src="/images/ajax-loader.gif" height="10px" style="display:none" />
    </div>
</div>

<!-- paso_5 -->
<div id="paso_5" style="display:none;">
    <div class="row">
        <label>Titulo:</label>
        <input type="text" name="titulo" id="titulo" value="" maxlength="255" />
    </div>

    <div class="row">
        <label>Imagen:</label>
        <input type="file" name="archivoImage" id="archivoImage" style="width: 289px;" />
    </div>

    <div class="row contenido_publinota">
        <label>Contenido:</label>
        <textarea id="textarea" name="textarea"></textarea>
    </div>

    <hr />

    <div class="row">
        <input type="button" class="button_new superButton" value="<<< Atr&aacute;s" id="btn_hacia_paso_4" />

        <input type="button" class="button_new superButton" value="Siguiente paso >>>" id="btn_paso_5" />
        <span class="msg_error" id="error_paso_5" style="display: inline-block;"></span>
        <img id="loader_btn_paso_5" src="/images/ajax-loader.gif" height="10px" style="display:none" />
    </div>
</div>

<!-- paso_6 -->
<div id="paso_6" style="display:none;">
    <div class="row" id="detalle_publinota"></div>

    <hr />

    <div class="row">
        <input type="button" class="button_new superButton" value="<<< Atr&aacute;s" id="btn_hacia_paso_5" />

        <input type="button" class="button_new superButton" value="Aceptar orden de compra" id="btn_finalizar" />
        <span class="msg_error" id="error_paso_6" style="display: inline-block;"></span>
        <img id="loader_btn_paso_6" src="/images/ajax-loader.gif" height="10px" style="display:none" />
    </div>
</div>