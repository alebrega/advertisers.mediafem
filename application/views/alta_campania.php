<link rel="stylesheet" href="<?= base_url() ?>css/jquery.qtip.min.css" type="text/css"></style>
<script src="<?= base_url() ?>js/jquery.qtip.min.js"></script>


<style type="text/css">
    label{
        font: normal bold 12px Arial, Helvetica, sans-serif;
        width: 140px;
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

    #inversion_neta, #inversion_bruta, #txt_inversion_total{
        background-color: #ddd;
    }

    #lbl_inversion_total label{
        width: 100% !important;
        text-align: left;
        margin: 20px 18px;
    }
</style>

<script type="text/javascript">
    jQuery(function($) {
        $.datepicker.regional['es'] = {
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            dayNames: ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado'],
            dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mie', 'Juv', 'Vie', 'Sab'],
            dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa']};
        $.datepicker.setDefaults($.datepicker.regional['es']);
    });

    $(document).ready(function() {

        // MAKE SURE YOUR SELECTOR MATCHES SOMETHING IN YOUR HTML!!!
        $('#icon_question').each(function() {
            $(this).qtip({
                content: {
                    text: $(this).next('.tooltiptext')
                }
            });
        });

        var tipo_campana = 'tradicional';
        var id_campana = 0;
        var id_anunciante = $("#cmb_anunciantes").find(':selected').val();
        var archivo_subido = 0;

        var gbl_valor_minimo = 0;

        function loadValorMinimo() {
            var valor_minimo = 0;

            var segmentacion = $('#cmb_segmentaciones').val();

            // valido los formatos seleccionados
            var formatos = "";
            $("input[name='chk_formatos[]']:checked").each(function() {
                var id = $(this).val();

                formatos = formatos + id + ";";
            });

            var id_paises = "";
            $("#cmb_paises_2 option").each(function() {
                id_paises = id_paises + $(this).attr('value') + ";";
            });

            var modalidad = $('select[name="modalidad_compra"]').find(':selected').val();

            var form_data = {
                id_paises: id_paises,
                modalidad: modalidad,
                formatos: formatos,
                segmentacion: segmentacion
            };

            $.ajax({
                type: "POST",
                url: "/campania/valor_minimo/",
                data: form_data,
                dataType: "json",
                success: function(msg) {
                    valor_minimo = msg.valor_minimo;

                    gbl_valor_minimo = valor_minimo;

<?php if ($this->user_data->notacion == 1) { // Notacion espanola            ?>
                        /*var gbl_valor_minimo = gbl_valor_minimo.toFixed(2);
                         gbl_valor_minimo = gbl_valor_minimo.toString();
                         gbl_valor_minimo = gbl_valor_minimo.replace('.', ',');*/
<?php } else { ?>
                        //gbl_valor_minimo.toFixed(2);
<?php } ?>

                    $('#lbl_valor_minimo').html(gbl_valor_minimo);

                    if (gbl_valor_minimo <= 0) {
                        $('#div_lbl_modalidad_valor').css('display', 'none');
                    } else {
                        $('#div_lbl_modalidad_valor').css('display', 'block');
                    }

                    if ($('#valor_cpm_sugerido').val() == '0,00' || $('#valor_cpm_sugerido').val() == '0.00') {
                        $('#valor_unitario').val(gbl_valor_minimo);
                    }
                }
            });
        }

        var habilitar_descuentos = <?= $habilitar_descuentos ?>;

        $.datepicker.setDefaults($.datepicker.regional['es']);
        $("#fecha_inicio").datepicker({dateFormat: 'dd-mm-yy'});
        $("#fecha_fin").datepicker({dateFormat: 'dd-mm-yy'});

        // SE USA PARA FILTRAR PAISES Y SITIOS
        function strpos(cadena, busqueda) {
            var i = (cadena.toLowerCase()).indexOf(busqueda.toLowerCase());
            return i === -1 ? false : true;
        }

        // SITIOS ESPECIFICOS **************************************************
        var mostrar_listado_sitios = false;
        var cantidad_sitios = 0;
        var sitios = '';
        /*
         var form_data = {};
         
         $.ajax({
         type: "GET",
         url: "/campania/get_publishers_y_sitios_json/",
         data: form_data,
         dataType: "json",
         success: function(msg) {
         if (msg) {
         sitios = eval(msg);
         cantidad_sitios = sitios.length;
         
         // recorro uno por uno los publisher y creo un <objgroup>
         for (var a = 0; a < cantidad_sitios; a++) {
         if (sitios[a].url != '')
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
         } else {
         alert(msg.error);
         }
         }
         });*/

        $("#txt_sitios_web").keyup(function(event) {
            $('#cmb_sitios').html('');

            var busqueda = $(this).val();

            if (busqueda != '') {
                var cantidad_sitios = sitios.length;

                for (var a = 0; a < cantidad_sitios; a++) {
                    if (strpos(sitios[a].url, busqueda) != false)
                        $("#cmb_sitios").append('<option value="' + sitios[a].id_sitio + '">' + sitios[a].url + '</option>');
                }
            } else {
                var cantidad_sitios = 0;
                cantidad_sitios = sitios.length;

                for (var a = 0; a < cantidad_sitios; a++) {
                    $("#cmb_sitios").append('<option value="' + sitios[a].id_sitio + '">' + sitios[a].url + '</option>');
                }
            }

        });

        $("#cmb_sitios").dblclick(function() {
            $("#btn_pasar_sitio").click();
        });

        $("#cmb_sitios_2").dblclick(function() {
            $("#btn_borrar_sitio").click();
        });

        $("#btn_pasar_sitio").click(function() {
            $('#cmb_sitios option:selected').appendTo("#cmb_sitios_2");
        });

        $("#btn_borrar_sitio").click(function() {
            var seleccionados = $('#cmb_sitios_2 option:selected');
            for (var a = 0; a < seleccionados.length; a++) {
                $(seleccionados[a]).appendTo("#cmb_sitios");
            }
        });


        // PAISES **************************************************************
        // cargo los paises en el listado.
        var paises = '';
        var form_data = {};

        $.ajax({
            type: "GET",
            url: "/campania/get_paises_json/",
            data: form_data,
            dataType: "json",
            success: function(msg) {
                paises = eval(msg);

                for (var campo in paises) {
                    $("#cmb_paises").append('<option value="' + paises[campo].id + '">' + paises[campo].descripcion + '</option>');
                }
            }
        });

        $("#txt_paises").keyup(function(event) {
            $('#cmb_paises').html('');

            var busqueda = $(this).val();

            if (busqueda != '') {
                $('#cmb_paises').html('');

                for (var campo in paises) {
                    var agregar = true;
                    if (strpos(paises[campo].descripcion, busqueda) != false) {
                        if ($('#cmb_paises_2 option').length > 0) {
                            $('#cmb_paises_2 option').each(function() {
                                if (paises[campo].descripcion == $(this).text()) {
                                    agregar = false;
                                }
                            });

                            if (agregar) {
                                $("#cmb_paises").append('<option value="' + paises[campo].id + '">' + paises[campo].descripcion + '</option>');
                            }
                        } else {
                            $("#cmb_paises").append('<option value="' + paises[campo].id + '">' + paises[campo].descripcion + '</option>');
                        }
                    }
                }
            } else {
                $('#cmb_paises').html('');

                if ($('#cmb_paises_2 option').length > 0) {
                    var agregar = false;

                    for (var campo in paises) {
                        $('#cmb_paises_2 option').each(function() {
                            if (paises[campo].descripcion != $(this).text()) {
                                agregar = true;
                            }
                        });

                        if (agregar)
                            $("#cmb_paises").append('<option value="' + paises[campo].id + '">' + paises[campo].descripcion + '</option>');
                    }
                } else {
                    for (var campo in paises) {
                        $("#cmb_paises").append('<option value="' + paises[campo].id + '">' + paises[campo].descripcion + '</option>');
                    }
                }
            }
        });

        $("#btn_pasar_pais").click(function() {
            $('#cmb_paises option:selected').appendTo("#cmb_paises_2");
        });

        $("#btn_borrar_pais").click(function() {
            $('#cmb_paises_2 option:selected').appendTo("#cmb_paises");
        });

        $("#cmb_paises").dblclick(function() {
            $('#cmb_paises option:selected').appendTo("#cmb_paises_2");
        });

        $("#cmb_paises_2").dblclick(function() {
            $('#cmb_paises_2 option:selected').appendTo("#cmb_paises");
        });


        // BOTONES DE ATRAS ****************************************************
        // *********************************************************************
        $('#btn_hacia_paso_0').click(function() {
            $('#nueva_campana').html('').append(divLoader);
            $('#nueva_campana').load('/campania/tipo_campania/' + seconds);
        });

        $('#btn_hacia_paso_1').click(function() {
            $("#paso_2").css("display", "none");
            $("#paso_1").css("display", "block");

            $(".div_paso_2").removeClass("activo");
            $(".div_paso_1").addClass("activo");
        });

        $('#btn_hacia_paso_2').click(function() {
            $("#paso_3").css("display", "none");
            $("#paso_2").css("display", "block");

            $(".div_paso_3").removeClass("activo");
            $(".div_paso_2").addClass("activo");

            $('#error_formato').html('');
        });

        $('#btn_hacia_paso_3').click(function() {
            $("#paso_4").css("display", "none");
            $("#paso_3").css("display", "block");

            $(".div_paso_4").removeClass("activo");
            $(".div_paso_3").addClass("activo");

            $('#error_formato').html('');
        });

        $('#btn_hacia_paso_4').click(function() {
            $("#paso_5").css("display", "none");
            $("#paso_4").css("display", "block");

            $(".div_paso_5").removeClass("activo");
            $(".div_paso_4").addClass("activo");
        });

        $('#btn_hacia_paso_5').click(function() {
            $("#paso_6").css("display", "none");
            $("#paso_5").css("display", "block");

            $(".div_paso_6").removeClass("activo");
            $(".div_paso_5").addClass("activo");
        });

        // PRIMER PASO *********************************************************
        // *********************************************************************
        $('.item_tipo_campania').click(function() {
            tipo_campana = $(this).attr('data-type');

            if (tipo_campana == 'publinota' || tipo_campana == 'publinota_sitios') {
                $('#nueva_campana').html('').append(divLoader).load('/campania/crear_publinota/');
            }else if (tipo_campana == 'publinota_embajadoras'){
                $('#nueva_campana').html('').append(divLoader).load('/campania/crear_publinota_embajadoras/');
            }else if (tipo_campana == 'mencion_sitios'){
                $('#nueva_campana').html('').append(divLoader).load('/campania/crear_mencion_sitios/');
            }else if (tipo_campana == 'mencion_embajadoras'){
                $('#nueva_campana').html('').append(divLoader).load('/campania/crear_mencion_embajadoras/');
            }else if (tipo_campana == 'encuesta') {
                $('#nueva_campana').html('').append(divLoader).load('/campania/crear_encuesta/');

            } else {
                $("#paso_1").css("display", "none");
                $("#paso_2").css("display", "block");

                $(".div_paso_1").removeClass("activo");
                $(".div_paso_2").addClass("activo");
            }
        });

        // SEGUNDO PASO ********************************************************
        // *********************************************************************
        $('#crear_anunciante').click(function() {
            $('#list_anunciante').fadeOut(0);
            $('#nuevo_anunciante').fadeIn('fast');

            id_anunciante = 0;
        });

        $("#cmb_anunciantes").change(function() {
            id_anunciante = $("#cmb_anunciantes").find(':selected').val();
        });


        $('#cancelar_nuevo_anunciante').click(function() {
            $('#nuevo_anunciante').fadeOut(0);
            $('#list_anunciante').fadeIn('fast');

            $('#loader_anunciantes').css('display', 'none');
            $('#aceptar_nuevo_anunciante').attr('disabled', false);
            $('#new_anunciante').attr('disabled', false);
            $('#new_anunciante').val('');
            id_anunciante = 0;

            $("#error_anunciante").css('display', 'none');
        });

        function btn_paso_2() {
            var error = false;
            $("#error_paso_1").css("display", "none");
            $("#loader_btn_paso_1").css("display", "inline");

            $("#error_nombre_campania").html(' ');

            $('.ui-icon-circle-check').click();

            // valido nombre de la campania
            var nombre_campania = $.trim($('#nombre_campania').val());
            if (nombre_campania.length <= 0) {
                $("#error_nombre_campania").html('Por favor indique un nombre para la campaña.').css("display", "inline");
                error = true;
            }

            var tipo_campania = tipo_campana;

            //valido la fecha de inicio
            var fecha_inicio = $.trim($('#fecha_inicio').val());
            var fecha_fin = $.trim($('#fecha_fin').val());
            var adserver = $('#cmb_adserver option:selected').val();

            if (fecha_inicio == '' || fecha_fin == '') {
                $("#error_fechas").html('Por favor ingrese la fecha de inicio y fin de la campaña.').css("display", "inline");
                error = true;
            } else {
                $("#error_fechas").css("display", "none");
            }

            if (error == true) {
                $(this).attr('disabled', false);

                $("#loader_btn_paso_1").css("display", "none");

                return false;
            }

            // bloqueo la segmentacion
            if (tipo_campania == 'data') {
                $('#cmb_segmentaciones option[value="2"]').attr('selected', 'selected');
                $('#cmb_segmentaciones').attr('disabled', 'disabled');

                $("#cmb_segmentaciones").change();
            }

            var unificar_campania = $('#cmb_campanias option:selected').val();

            var form_data = {
                id_anunciante: id_anunciante,
                tipo_campania: tipo_campania,
                nombre_campania: nombre_campania,
                cliente: $('#cmb_clientes option:selected').val(),
                fecha_inicio: fecha_inicio,
                fecha_fin: fecha_fin,
                unificar_campania: unificar_campania,
                adserver: adserver
            };

            var form_data_sitios = {
                adserver: adserver
            };

            $.ajax({
                type: "GET",
                url: "/campania/get_publishers_y_sitios_json_by_adserver/",
                data: form_data_sitios,
                dataType: "json",
                success: function(msg) {
                    if (msg) {
                        sitios = eval(msg);
                        cantidad_sitios = sitios.length;

                        // recorro uno por uno los publisher y creo un <objgroup>
                        for (var a = 0; a < cantidad_sitios; a++) {
                            if (sitios[a].url != '')
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
                    } else {
                        alert(msg.error);
                    }
                }
            });

            $.ajax({
                type: "POST",
                url: "/campania/insertar_campania_primer_paso/",
                data: form_data,
                dataType: "json",
                success: function(msg) {
                    if (msg.validate) {
                        id_campana = msg.id_campania;

                        $("#loader_btn_paso_1").css("display", "none");

                        // cargo los formatos para la campana
                        $('#mostrar_formatos').load('/campania/mostrar_formatos/' + tipo_campana + '/' + id_campana, function() {
                            $("#lbl_inversion_total label span.fecha_inicio").html(fecha_inicio);
                            $("#lbl_inversion_total label span.fecha_fin").html(fecha_fin);

                            $("#paso_2").css("display", "none");
                            $("#paso_3").css("display", "inline");

                            //Habilitar 3 Paso
                            $(".div_paso_2").removeClass("activo");
                            $(".div_paso_3").addClass("activo");
                        });
                    } else {
                        $("#error_paso_1").html(msg.error);
                        $("#error_paso_1").css("display", "inline");
                        $("#loader_btn_paso_1").css("display", "none");
                    }
                }
            });
        }

        // VOY AL PASO 3 *******************************************************
        $('#btn_paso_2').click(function(e) {
            if ($('#nuevo_anunciante').is(':visible')) {
                // CREO EL ANUNCIANTE
                $('#new_anunciante').attr('disabled', 'disabled');
                $('#error_anunciante').css('color', 'red');
                $('#error_anunciante').html('');
                $('#loader_anunciantes').css('display', 'inline');

                var nombre = $.trim($('#new_anunciante').val());

                if (nombre == '') {
                    $('#error_anunciante').html('Por favor, indique el nombre de anunciante.');
                    $('#error_anunciante').css('display', 'inline');

                    $(this).attr('disabled', false);
                    $('#new_anunciante').attr('disabled', false);
                    $('#loader_anunciantes').css('display', 'none');

                    $('#new_anunciante').focus();
                    return false;
                }

                var form_data = {nombre: nombre};

                $.ajax({
                    type: "POST",
                    url: "/campania/alta_anunciante_json/1",
                    data: form_data,
                    dataType: "json",
                    success: function(msg) {
                        $('#loader_anunciantes').css('display', 'none');
                        if (msg.validate) {
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

                        } else {
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
            } else {
                btn_paso_2();
            }
        });

        // TERCER PASO *********************************************************
        // *********************************************************************
        $("#cmb_segmentaciones").change(function(event) {
            var id = $("#cmb_segmentaciones").find(':selected').val();
            $("#error_segmentacion").css("display", "none");

            if (id == "1") {
                $("#canales_tematicos").css("display", "none");
                $("#sitios_especificos").css("display", "none");
            } else if (id == "2") {
                $("#canales_tematicos").css("display", "block");
                $("#sitios_especificos").css("display", "none");
            } else if (id == "3") {
                $("#canales_tematicos").css("display", "none");
                $("#sitios_especificos").css("display", "block");
                if (mostrar_listado_sitios == false) {
                    $("#loader_cmb_sitios").css("display", "inline");
                    $("#btn_pasar_sitio_del_text").attr("disabled", "disabled");
                    $("#txt_sitios_web").attr("disabled", "disabled");
                    $("#cmb_sitios").attr("disabled", "disabled");
                    $("#cmb_sitios_2").attr("disabled", "disabled");
                    $("#btn_borrar_sitio").attr("disabled", "disabled");
                    $("#btn_pasar_sitio").attr("disabled", "disabled");
                }
            }
        });

        // VOY AL PASO 4 *******************************************************
        $('#btn_paso_3').click(function(e) {
            e.preventDefault();

            $('#loader_btn_paso_3').fadeIn("fast");

            $("#loader_btn_paso_3").css("display", "inline");
            $("#error_paso_3").html(' ');
            $("#error_paso_4").html(' ');

            var error = false;

            loadValorMinimo();

            var segmentacion = $('#cmb_segmentaciones').val();

            var frecuencia = $('#cmb_frecuencia').val();

            var id_canales_tematicos = "";
            var id_sitios = "";

            // valido que haya seleccionado al menos un canal cuando la sergmentación sea '2'
            if (segmentacion == '2') {
                $("input[name='chk_canales_tematicos[]']:checked").each(function() {
                    id_canales_tematicos = id_canales_tematicos + $(this).val() + ";";
                });

                if (id_canales_tematicos == '') {
                    $("#error_segmentacion").html('Por favor seleccione al menos un canal.').css("display", "inline");
                    error = true;
                }
            } else if (segmentacion == "3") {
                $("#cmb_sitios_2 option").each(function() {
                    id_sitios = id_sitios + $(this).attr('value') + ";";
                });

                if (id_sitios == '') {
                    $("#error_segmentacion").html('Por favor seleccione al menos un sitio.').css("display", "inline");
                    error = true;
                }
            }

            // valido los formatos seleccionados
            var formatos = "";
            $("input[name='chk_formatos[]']:checked").each(function() {
                var id = $(this).val();
                var rel = $(this).attr('rel');

                var pagina_destino = $.trim($("#pagina_destino_" + rel).val());

                if ($("input[name='chk_agregar_mediafem']").is(':checked') && pagina_destino != '') {
                    if (pagina_destino.indexOf('?') != -1) {
                        if (pagina_destino.indexOf('utm_source') == -1) {
                            pagina_destino = pagina_destino + '&utm_source=MediaFem';
                        }
                    } else {
                        if (pagina_destino.indexOf('utm_source') == -1) {
                            pagina_destino = pagina_destino + '?utm_source=MediaFem';
                        }
                    }
                }

                formatos = formatos + id + "|" + pagina_destino + ";";
            });

            if (formatos == '' && error == false) {
                $("#error_formato").html('Por favor seleccione al menos un tamaño.').css("display", "inline");
                error = true;
            }

            $("#error_paises").css("display", "none");

            // valido los paises seleccionados
            var id_paises = "";
            $("#cmb_paises_2 option").each(function() {
                id_paises = id_paises + $(this).attr('value') + ";";
            });

            if (id_paises == '') {
                $("#error_paises").html('Por favor seleccione al menos un pa&iacute;s del listado.').css("display", "inline");
                return false;
            }

            // si surgió algun error durante la validación.
            if (error == true) {
                $('#loader_btn_paso_3').fadeOut("fast", function() {
                    $("#error_paso_3").css("display", "inline");
                });

                $(this).attr('disabled', false);

                return false;
            }

<?php
if ($type == 0) { // Campania con Daily
    $type_DFP = 'PRICE_PRIORITY';
} else {
    $type_DFP = 'STANDARD';
}
?>

            var type_DFP = '<?= $type_DFP ?>';

            var cant_dias = getNumeroDeNits($.trim($('#fecha_inicio').val()), $.trim($('#fecha_fin').val())) + 1;


            var device_desktop = 0;
            var device_tablet = 0;
            var device_phone = 0;

            if ($('#device_desktop').is(":checked")) {
                device_desktop = 1;
            }

            if ($('#device_tablet').is(":checked")) {
                device_tablet = 1;
            }

            if ($('#device_phone').is(":checked")) {
                device_phone = 1;
            }

            var form_data = {
                id_campania: id_campana,
                segmentacion: segmentacion,
                id_sitios: id_sitios,
                id_canales_tematicos: id_canales_tematicos,
                formatos: formatos,
                id_paises: id_paises,
                type_DFP: type_DFP,
                cant_dias: cant_dias,
                tipo_campania: tipo_campana,
                frecuencia: frecuencia,
                device_desktop: device_desktop,
                device_tablet: device_tablet,
                device_phone: device_phone
            };

            $.ajax({
                type: "POST",
                url: "/campania/insertar_campania_tercer_paso/",
                data: form_data,
                dataType: "json",
                success: function(msg) {
                    if (msg.validate) {

                        var tc = tipo_campana;
                        if (tc == 'layer' || tc == 'skin' || tc == 'expandible' || tc == 'facebook_like_ads' || tc == 'twitter_timeline_ads' || tc == 'video_zocalo' || tc == 'video_viral' || tc == 'video_banner' || tc == 'overlay' || tc == 'data') {
                            $('select[name="modalidad_compra"]').empty();
                            $('select[name="modalidad_compra"]').append('<option value="cpm">CPM</option>');
                        } else if (tc == 'pre_roll') {
                            $('select[name="modalidad_compra"]').empty();
                            $('select[name="modalidad_compra"]').append('<option value="cpm">CPM</option>');
                            $('select[name="modalidad_compra"]').append('<option value="cpv">CPV</option>');
                        }

                        //Habilitar 4 Paso
                        if (msg.valor_cpm_sugerido == '0,000' || msg.valor_cpm_sugerido == '0.000') {
                            $('#div_lbl_sugerido').css('display', 'none')
                        } else {
                            $('#div_lbl_sugerido').css('display', 'block')
                        }

                        $('#totalImpressions').val(msg.totalImpressions);
                        $('#totalClicks').val(msg.totalClicks);
                        $('#valor_cpm_sugerido').val(msg.valor_cpm_sugerido);
                        $('#valor_cpc_sugerido').val(msg.valor_cpc_sugerido);

<?php if ($this->user_data->notacion == 1) { // Notacion espanola            ?>

                            var text = $('#valor_cpm_sugerido').val().split(",").join(".") * $('#cmb_frecuencia').find(':selected').attr('data-multiplicacion');
                            text = text.toFixed(2);
                            text = text.toString();
                            $('#lbl_valor_sugerido').html(text.replace('.', ','));

<?php } else { ?>

                            var text = $('#valor_cpm_sugerido').val() * $('#cmb_frecuencia').find(':selected').attr('data-multiplicacion');
                            $('#lbl_valor_sugerido').html(text.toFixed(2));

<?php } ?>

                        $('#valor_unitario').val($('#valor_cpm_sugerido').val());

                        $('.lbl_modalidad_valor').html($('select[name="modalidad_compra"]').find(':selected').text());

                        $('#id_valor_sugerido').val(msg.id_valor_sugerido);

                        //$('#cantidad').val(msg.totalImpressions);


                        loadValorMinimo();

                        $("#paso_3").css("display", "none");
                        $("#paso_4").css("display", "inline");

                        $("#loader_btn_paso_3").css("display", "none");

                        $(".div_paso_3").removeClass("activo");
                        $(".div_paso_4").addClass("activo");
                    } else {
                        $("#loader_btn_paso_3").css("display", "none");
                    }
                }
            });
        });


        // CUARTO PASO *********************************************************
        // *********************************************************************
        // VOY AL PASO 5 *******************************************************
        $('#btn_paso_4').click(function() {
            $("#loader_modal_paso_4").css('display', 'none');
            $("#continuar_modal_paso_4").css('display', 'inline');
            // aviso si no hay suficiente volumen para el sitio seleccionado
            if ($('#cmb_segmentaciones').val() == 3) {
                var modalidad = $('select[name="modalidad_compra"]').find(':selected').val();
                var cantidad = $.trim($("#cantidad").val());
                // si la cantidad ingresada es mayor a la cantidad disponible
                if (modalidad == 'cpm') {
                    if (cantidad > parseFloat($('#totalImpressions').val())) {
                        // muestro el modal
                        $('a[data-reveal-id="ModalCantidadDisponible"]').click();
                    } else {
                        $('#btn_modal_paso_4_ok').click();
                    }
                } else {

                    if (cantidad > parseFloat($('#totalClicks').val())) {
                        // muestro el modal
                        $('a[data-reveal-id="ModalCantidadDisponible"]').click();
                    } else {
                        $('#btn_modal_paso_4_ok').click();
                    }
                }
            } else {
                $('#btn_modal_paso_4_ok').click();
            }
        });

        $('#btn_modal_paso_4_cancel').click(function() {
            $('.reveal-modal-bg').click();
            $('#cantidad').focus();
        });

        $('#btn_modal_paso_4_ok').click(function() {
            $('#error_paso_4').html('');
            $("#loader_btn_paso_4").fadeIn(0);

            $("#continuar_modal_paso_4").fadeOut('fast', function() {
                $("#loader_modal_paso_4").fadeIn(0);
            });

<?php if ($this->user_data->notacion == 1) { // Notacion espanola            ?>
                $("#inversion_neta").val($.trim($("#inversion_neta").val().split(",").join(".")));

    <?php if ($type == 0) { // Campania con Daily            ?>
                    $("#txt_inversion_total").val($.trim($("#txt_inversion_total").val().split(",").join(".")));
    <?php } ?>
<?php } ?>

            var modalidad = $('select[name="modalidad_compra"]').find(':selected').val();
            var valor = $.trim($("#valor_unitario").val());
            var cantidad = $.trim($("#cantidad").val());
            var inversion_neta = $.trim($("#inversion_neta").val());

<?php if ($this->user_data->notacion == 1) { // Notacion espanola            ?>
                var new_valor = $.trim($("#valor_unitario").val().split(",").join("."));

                var gbl_valor_minimo2 = $.trim(gbl_valor_minimo.split(",").join("."));
<?php } else { ?>
                var new_valor = $.trim($("#valor_unitario").val());

                var gbl_valor_minimo2 = $.trim(gbl_valor_minimo);
<?php } ?>

            new_valor = parseFloat(new_valor);
            gbl_valor_minimo2 = parseFloat(gbl_valor_minimo2);

            if (new_valor < gbl_valor_minimo2) {
                $("#loader_btn_paso_4").css("display", "none");
                $('#error_paso_4').html('El valor por unidad no puede ser menor a ' + gbl_valor_minimo + ' <?= $this->user_data->moneda ?>.');

                $('.reveal-modal-bg').click();

                $("#loader_modal_paso_4").fadeOut(0, function() {
                    $("#continuar_modal_paso_4").fadeIn(0);
                });

                return false;
            }


            if (inversion_neta < <?= $inversion_neta ?>) {
                $("#loader_btn_paso_4").css("display", "none");
                $('#error_paso_4').html('La inversión diaria no puede ser menor a <?= $this->api->notacion($inversion_neta, 2) ?> <?= $this->user_data->moneda ?>.');

                $('.reveal-modal-bg').click();

                $("#loader_modal_paso_4").fadeOut(0, function() {
                    $("#continuar_modal_paso_4").fadeIn(0);
                });
                $("#loader_btn_paso_4").css("display", "none");

                return false;
            }

<?php if ($no_gastar_saldo == 0) { ?>
    <?php if ($type == 0) { // Campania con Daily            ?>
                    //if($("#txt_inversion_total").val() > <?= $limite_de_compra ?>){
                    if (parseFloat($("#txt_inversion_total").val()) > parseFloat($('#cmb_clientes option:selected').attr('data-saldo'))) {
                        $("#loader_btn_paso_4").css("display", "none");
                        $('#error_paso_4').html('No posees suficiente saldo prepago para crear esta campaña.');

                        $('.reveal-modal-bg').click();

                        $("#loader_modal_paso_4").fadeOut(0, function() {
                            $("#continuar_modal_paso_4").fadeIn(0);
                        });
                        $("#loader_btn_paso_4").css("display", "none");

                        return false;
                    }
    <?php } ?>
<?php } ?>

            var inversion_bruta = $.trim($("#inversion_bruta").val());
            var descuento = $.trim($("#descuento").val());
            var comision = $.trim($("#comision").val());

            var maximo_descuentos = parseFloat(descuento) + parseFloat(comision);

            maximo_descuentos = parseFloat(maximo_descuentos);

            if (maximo_descuentos > 50) {
                $("#loader_btn_paso_4").css("display", "none");
                $('#error_paso_4').html('La suma del descuento y comisión no puede superar el 50%.');

                return false;
            }

<?php
if ($type == 0) { // Campania con Daily
    $type_DFP = 'PRICE_PRIORITY';
} else {
    $type_DFP = 'STANDARD';
}
?>

            var type_DFP = '<?= $type_DFP ?>';


            if (inversion_neta > 0) {
                var form_data = {
                    id_campania: id_campana,
                    modalidad: modalidad,
                    valor: valor,
                    cantidad: cantidad,
                    inversion_neta: inversion_neta,
                    inversion_bruta: inversion_bruta,
                    descuento: descuento,
                    comision: comision,
                    type_DFP: type_DFP
                };

                $.ajax({
                    type: "POST",
                    url: "/campania/insertar_campania_cuarto_paso/",
                    data: form_data,
                    dataType: "json",
                    success: function(msg) {
                        if (msg.validate) {
                            $('.reveal-modal-bg').click();

                            $("#loader_modal_paso_4").fadeOut('fast', function() {
                                $("#continuar_modal_paso_4").fadeIn(0);
                            });
                            $("#loader_btn_paso_4").css("display", "none");

                            //Habilitar 5 Paso
                            $("#paso_4").css("display", "none");
                            $("#paso_5").css("display", "inline");

                            $(".div_paso_4").removeClass("activo");
                            $(".div_paso_5").addClass("activo");
                        }
                    }
                });

            } else {
                $('.reveal-modal-bg').click();

                $("#loader_modal_paso_4").fadeOut(0, function() {
                    $("#continuar_modal_paso_4").fadeIn(0);
                });

                $("#loader_btn_paso_4").css("display", "none");
                $('#error_paso_4').html('La inversión total no es válida.');
            }
        });


        // QUINTO PASO *********************************************************
        // *********************************************************************
        $("#uploader").plupload({
            runtimes: 'html5,flash,html4,gears,browserplus,silverlight,',
            url: '/campania/subir_archivos',
            max_file_size: '30mb',
            chunk_size: '30mb',
            unique_names: false,
            multipart: true,
            filters: [
                {title: "Imagen", extensions: "jpg,gif,png"},
                {title: "PDF", extensions: "pdf"},
                {title: "MP4", extensions: "mp4"},
                {title: "Zip", extensions: "zip"},
                {title: "SWF", extensions: "swf"},
                {title: "TXT", extensions: "txt"},
                {title: "DOC", extensions: "doc"},
                {title: "DOCx", extensions: "docx"},
                {title: "XLS", extensions: "xls"},
                {title: "XLSs", extensions: "xlsx"},
                {title: "XML", extensions: "xml"}
            ],
            // Flash/Silverlight paths
            flash_swf_url: '/js/plupload.flash.swf',
            silverlight_xap_url: '/js/plupload.silverlight.xap',
            init: {
                StateChanged: function(up) {
                    if (up.state != plupload.STARTED) {
                        if (archivo_subido == 1) {
                            $('#resumen_campania').html(' ');

                            $('#resumen_campania').addClass('textCenter');
                            $('#resumen_campania').html('<img src="/images/loader.gif"/>');

                            $('#resumen_campania').load('/campania/ver/' + id_campana, function() {
                                $('#resumen_campania').removeClass('textCenter');

                                //Habilitar 6 Paso
                                $("#paso_5").css("display", "none");
                                $("#paso_6").css("display", "inline");

                                $(".div_paso_5").removeClass("activo");
                                $(".div_paso_6").addClass("activo");
                            });
                        } else {
                            $("#error_al_subir").html('Por favor seleccione los archivos a subir como anuncios y luego pulse "Subir los archivos seleccionados".').css("display", "block");
                        }
                    }
                },
                FileUploaded: function(up, file, info) {
                    if (info.response != 'ok') {
                        $("#error_al_subir").html($("#error_al_subir").html() + info.response).css("display", "block");

                        if (archivo_subido == 1) {
                            archivo_subido = 1;
                        } else {
                            archivo_subido = 0;
                        }
                    } else {
                        archivo_subido = 1;
                    }
                }
            }
        });

        // VOY AL PASO 6 *******************************************************
        $('#btn_paso_5').click(function() {
            if (archivo_subido == 0) {
                $('#uploader_start').click();
            } else {
                $('#resumen_campania').html(' ');

                $('#resumen_campania').addClass('textCenter');
                $('#resumen_campania').html('<img src="/images/loader.gif"/>');

                $('#resumen_campania').load('/campania/ver/' + id_campana, function() {
                    $('#resumen_campania').removeClass('textCenter');

                    //Habilitar 6 Paso
                    $("#paso_5").css("display", "none");
                    $("#paso_6").css("display", "inline");

                    $(".div_paso_5").removeClass("activo");
                    $(".div_paso_6").addClass("activo");
                });
            }
        });


        // SEXTO PASO **********************************************************
        // *********************************************************************
        $('#btn_finalizar').click(function(e) {
            e.preventDefault();

            $("#info").fadeOut('fast');

            if ($('#chk_acepto_terminos').is(":checked")) {
                $("#loader_submit").css("display", "inline");
                $(this).attr('disabled', 'disabled');

                var nombre_campania = $.trim($('#nombre_campania').val());

                var form_data = {
                    id_cliente: $('#cmb_clientes option:selected').val(),
                    nombre_campania: nombre_campania,
                    id_campania: id_campana,
                    id_valor_sugerido: $('#id_valor_sugerido').val(),
                    valor_cpm_sugerido: $('#valor_cpm_sugerido').val(),
                    valor_cpc_sugerido: $('#valor_cpc_sugerido').val(),
                    valor_unitario: $('#valor_unitario').val(),
                    modalidad: $('select[name="modalidad_compra"]').find(':selected').val()
                };

                $.ajax({
                    type: "POST",
                    url: "/campania/insertar_campania_finalizar/",
                    data: form_data,
                    dataType: "json",
                    success: function(msg) {
                        if (msg.validate) {
                            window.location.replace("/campania?creada_ok=true");
                            return false;
                        } else {
                            $("#info").html(msg.error);
                            $(this).attr('disabled', false);
                        }
                    }
                });
            } else {
                $("#info").html('<span class="msg_error" style="display: inline;">Por favor acepte los terminos y condiciones para poder crear la campaña.</span>').fadeIn('fast');
            }
        });

        $('select[name="modalidad_compra"]').change(function() {
            $('#modalidad_valor').html($('select[name="modalidad_compra"]').find(':selected').text());

            $('.lbl_modalidad_valor').html($('select[name="modalidad_compra"]').find(':selected').text());

            if ($('select[name="modalidad_compra"]').find(':selected').text() == 'CPM') {
                if ($('#valor_cpm_sugerido').val() == '0,000' || $('#valor_cpm_sugerido').val() == '0.000') {
                    $('#div_lbl_sugerido').css('display', 'none');
                } else {
                    $('#div_lbl_sugerido').css('display', 'block')
                }

<?php if ($this->user_data->notacion == 1) { // Notacion espanola            ?>

                    var text = $('#valor_cpc_sugerido').val().split(",").join(".") * $('#cmb_frecuencia').find(':selected').attr('data-multiplicacion');

                    text = text.toString();
                    $('#lbl_valor_sugerido').html(text.replace('.', ','));

<?php } else { ?>

                    $('#lbl_valor_sugerido').html($('#valor_cpm_sugerido').val() * $('#cmb_frecuencia').find(':selected').attr('data-multiplicacion'));

<?php } ?>

                $('#valor_unitario').val($('#valor_cpm_sugerido').val());
            } else {
                if ($('#valor_cpc_sugerido').val() == '0,000' || $('#valor_cpc_sugerido').val() == '0.000') {
                    $('#div_lbl_sugerido').css('display', 'none')
                } else {
                    $('#div_lbl_sugerido').css('display', 'block')
                }

<?php if ($this->user_data->notacion == 1) { // Notacion espanola            ?>

                    var text = $('#valor_cpc_sugerido').val().split(",").join(".") * $('#cmb_frecuencia').find(':selected').attr('data-multiplicacion');
                    text = text.toString();
                    $('#lbl_valor_sugerido').html(text.replace('.', ','));

<?php } else { ?>

                    $('#lbl_valor_sugerido').html($('#valor_cpc_sugerido').val() * $('#cmb_frecuencia').find(':selected').attr('data-multiplicacion'));

<?php } ?>

                $('#valor_unitario').val($('#valor_cpc_sugerido').val());
            }

            if ($(this).find(':selected').val() == 'cpm') {
                $('#tipo_cantidad').html(' impresiones.');
                $('#cantidad').val($('#totalImpressions').val());
            } else if ($(this).find(':selected').val() == 'cpc') {
                $('#tipo_cantidad').html(' clicks.');
                $('#cantidad').val($('#totalClicks').val());
            } else if ($(this).find(':selected').val() == 'cpv') {
                $('#tipo_cantidad').html(' vistas.');
                $('#cantidad').val($('#totalClicks').val());
            }

            calcular_inversion_neta();

            loadValorMinimo();
        });

        $("#valor_unitario").keyup(function() {
            calcular_inversion_neta();
        });

        $("#cantidad").keydown(function(event) {
            // Allow: backspace, delete, tab, escape, and enter
            if (event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 13 ||
                    // Allow: Ctrl+A
                            (event.keyCode == 65 && event.ctrlKey === true) ||
                            // Allow: home, end, left, right
                            event.keyCode == 35 || event.keyCode == 36) {
                // let it happen, don't do anything
                event.preventDefault();
            } else {

                if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105)) {

                    if (event.keyCode == 8 || event.keyCode == 46 || (event.keyCode >= 37 && event.keyCode <= 40)) {
                    } else {
                        event.preventDefault();
                    }

                }
            }

            $("#cantidad").keyup();
        });

        $("#cantidad").keyup(function(event) {
            calcular_inversion_neta();
        });

        $("#inversion_bruta").keyup(function() {
            calcular_inversion_neta();
        });

        $("#descuento").keyup(function() {
            calcular_inversion_neta();
        });

        $("#comision").keyup(function() {
            calcular_inversion_neta();
        });

        function calcular_inversion_neta() {
            var modalidad = $('select[name="modalidad_compra"]').find(':selected').val();
            var valor = $.trim($("#valor_unitario").val().split(",").join("."));
            var cantidad = $.trim($("#cantidad").val().split(",").join("."));
            var inversion_neta = $.trim($("#inversion_neta").val());

            var inversion_bruta = $.trim($("#inversion_bruta").val());
            var descuento = $.trim($("#descuento").val());
            var comision = $.trim($("#comision").val());

            if (habilitar_descuentos == 0) {
                if (modalidad == 'cpc' || modalidad == 'cpv') {
                    inversion_neta = cantidad * valor;
                } else {
                    inversion_neta = (cantidad * valor) / 1000;
                }
            } else {
                if (modalidad == 'cpc' || modalidad == 'cpv') {
                    inversion_bruta = cantidad * valor;
                } else {
                    inversion_bruta = (cantidad * valor) / 1000;
                }

                $("#inversion_bruta").val(inversion_bruta.toFixed(3));

                descuento = (inversion_bruta * descuento) / 100;

                inversion_neta = inversion_bruta - descuento;

                comision = (inversion_neta * comision) / 100;

                inversion_neta = inversion_neta - comision;
            }

            if (inversion_neta == 'NaN') {
                inversion_neta = 0;
            }

            //$("#inversion_neta").val((Math.round( inversion_neta * 100 ) / 100).toFixed(3));
            $("#inversion_neta").val(((inversion_neta * 100) / 100).toFixed(3));

<?php if ($type == 0) { // Campania con Daily            ?>

                var cant_dias = getNumeroDeNits($.trim($('#fecha_inicio').val()), $.trim($('#fecha_fin').val())) + 1;

                var cuenta = ((inversion_neta * cant_dias).toFixed(3));

                if (cuenta == 'NaN') {
                    $("#txt_inversion_total").val('0,000');
                } else {
                    //$("#txt_inversion_total").val(((inversion_neta * cant_dias).toFixed(3)));
                    $("#txt_inversion_total").val((($("#inversion_neta").val() * cant_dias).toFixed(3)));
                }

<?php } ?>

<?php if ($this->user_data->notacion == 1) { // Notacion espanola            ?>
                $("#inversion_neta").val($.trim($("#inversion_neta").val().split(".").join(",")));

    <?php if ($type == 0) { ?>
                    $("#txt_inversion_total").val($.trim($("#txt_inversion_total").val().split(".").join(",")));
    <?php } ?>
<?php } ?>

            $("#lbl_inversion_total label span.fecha_inicio").html($.trim($('#fecha_inicio').val()));
            $("#lbl_inversion_total label span.fecha_fin").html($.trim($('#fecha_fin').val()));
        }

        $('#uploader_start').click(function() {
            $("#error_al_subir").css("display", "none");
        });

        $('select[name="modalidad_compra"]').change();

        var estado_item_mas_tipos = 0;
        $('.item_mas_tipos').click(function() {
            if (estado_item_mas_tipos == 0) {
                $('.item_mas_tipos div').fadeOut('fast');
                $('.item_mas_tipos[data-type="' + $(this).data('type') + '"]').css('background', 'none');
                $('.item_mas_tipos[data-type="' + $(this).data('type') + '"] div').fadeIn('fast');
                estado_item_mas_tipos = 1;
            } else {
                $('.item_mas_tipos div').fadeOut('fast');
                estado_item_mas_tipos = 0;
            }
        });

        $('#btn_hacia_paso_0').click(function() {
            $('#saldo_disponible').html('');
        });

        $('#cmb_clientes').change(function() {
            $('#saldo_disponible').html($('#cmb_clientes option:selected').attr('data-moneda') + ' ' + $('#cmb_clientes option:selected').attr('data-saldo'));
        });

        $('#cmb_clientes').change();
    });
</script>

<input type="hidden" name="id_anunciante" id="id_anunciante" value="0" />

<!-- Pasos list -->
<div id="pasos">
    <span class="div_paso_1 activo">Formato de la campa&ntilde;a</span>
    <span class="div_paso_2">Datos de la campa&ntilde;a</span>
    <span class="div_paso_3">Segmentaci&oacute;n</span>
    <span class="div_paso_4">Inversi&oacute;n</span>
    <span class="div_paso_5">Subir anuncios</span>
    <span class="div_paso_6">Orden de compra</span>
</div>

<!-- paso_1 -->
<div id="paso_1">
    <div class="alerta" style="text-align: left; font-weight: normal; margin-bottom: 20px;">
        - Para examinar los requisitos técnicos necesarios para su próxima campaña, cualquier otra duda o información adicional por favor ingrese <a href="http://www.mediafem.com/files/MediaFem_Especificaciones.pdf" target="_BLANK" title="ESPECIFICACIONES TÉCNICAS">aquí</a>. <br />
        - Si necesitas m&aacute;s ayuda para saber como crear tus campa&ntilde;as, visita nuestro <a href="http://ayuda.mediafem.com/mediafem-anunciantes/primeros-pasos/subir-campana-paso-a-paso" target="_blank">tutorial</a>.
    </div>

    <div class="item_tipo_campania" data-type="tradicional">
        Tradicionales <span style="color: #cdcdcd;">(<?= $formatos_tradicionales ?>)</span>
    </div>

    <div class="item_mas_tipos" data-type="rich_media">
        Rich Media

        <div class="item_tipo_campania" data-type="layer">
            Layer
        </div>
        <div class="item_tipo_campania" data-type="skin">
            Skin
        </div>
    </div>

    <div class="item_mas_tipos" data-type="mobile">
        Mobile

        <div class="item_tipo_campania" data-type="sitios_movilizados">
            Sitios Movilizados <span style="color: #cdcdcd;">(320x50 y 300x50)</span>
        </div>
        <div class="item_tipo_campania" data-type="impresiones_mobile">
            Impresiones Mobile <span style="color: #cdcdcd;">(<?= $formatos_tradicionales ?>)</span>
        </div>
    </div>

    <div class="item_tipo_campania" data-type="expandible">
        Expandible
    </div>

    <div class="item_mas_tipos" data-type="social_ads">
        Social Ads

        <div class="item_tipo_campania" data-type="facebook_like_ads">
            Facebook Like Ads
        </div>
        <div class="item_tipo_campania" data-type="twitter_timeline_ads">
            Twitter Timeline ads
        </div>
        <div class="item_tipo_campania" data-type="video_zocalo">
            Video Z&oacute;calo
        </div>
        <div class="item_tipo_campania" data-type="video_viral">
            Video Viral
        </div>
    </div>

    <div class="item_mas_tipos" data-type="video">
        Video

        <div class="item_tipo_campania" data-type="video_banner">
            Video in Banner
        </div>

        <div class="item_tipo_campania" data-type="pre_roll">
            Pre roll
        </div>

        <div class="item_tipo_campania" data-type="overlay">
            Overlay
        </div>
    </div>
    <!--
    <div class="item_tipo_campania" data-type="publinota">
        Publinota
    </div>
    -->
    <div class="item_tipo_campania" data-type="data">
        Data
    </div>

    
    <div class="item_mas_tipos" data-type="contenido">
        Contenido

        <div class="item_tipo_campania" data-type="publinota_sitios">
            Publinota en Sitios Web
        </div>

        <div class="item_tipo_campania" data-type="publinota_embajadoras">
            Publinota Embajadoras
        </div>

        <div class="item_tipo_campania" data-type="mencion_sitios">
            Mención en redes sociales de Sitios Web
        </div>

        <div class="item_tipo_campania" data-type="mencion_embajadoras">
            Mención Embajadoras
        </div>
    </div>
    
    <!--
    <div class="item_tipo_campania" data-type="encuesta">
        Encuesta
    </div>
    -->

    <div class="row" style="margin-top: 20px;">
        <input type="button" class="button_new superButton" value="<<< Atr&aacute;s" id="btn_hacia_paso_0" />
    </div>
</div>

<!-- paso_2 -->
<div id="paso_2" style="display:none;">

    <?php if ($permitir_unificar_campanias && $campanias_padres) { ?>
        <div class="row">
            <label>Unificar con campa&ntilde;a:</label>
            <select name="cmb_campanias" id="cmb_campanias" style="width: 300px;">
                <option value="NINGUNA">---- Ninguna ----</option>
                <?php
                foreach ($campanias_padres as $row) {
                    echo '<option value="' . $row->id . '">' . $row->nombre . '</option>';
                }
                ?>
            </select>
        </div>
    <?php } ?>

    <?php if (sizeof($clientes)) { ?>
        <div class="row" <?php
        if (sizeof($clientes) == 1) {
            echo 'style="display:none;"';
        }
        ?>>
            <label>Clientes:</label>
            <span id="list_clientes">
                <select name="cmb_clientes" id="cmb_clientes" style="width: 300px;">
                    <?php
                    foreach ($clientes as $cliente)
                        echo '<option value="' . $cliente->id . '" data-saldo="' . $cliente->saldo_disponible . '" data-moneda="' . $cliente->moneda . '">' . $cliente->razon_social . '</option>';
                    ?>
                </select>
            </span>
        </div>
    <?php } ?>

    <div class="row">
        <label>Anunciante:</label>
        <span id="list_anunciante">
            <?php if (sizeof($anunciantes_adserver)) { ?>
                <select name="cmb_anunciantes" id="cmb_anunciantes" style="width: 300px;">
                    <?php
                    foreach ($anunciantes_adserver as $row) {
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
        <div style="font-style: italic; margin: 5px 0 0 165px; font-size:0.8em">
            Recomendamos que elija un nombre que sea f&aacute;cilmente identificable.<br />
            Es una buena practica agregar el nombre del pa&iacute;s o regi&oacute;n al nombre de la campa&ntilde;a o al anunciante.
        </div>
    </div>

    <div class="row">
        <label>Fecha: </label>
        <input type="text" name="fecha_inicio" id="fecha_inicio" value="" style="width:140px !important;" /> al
        <input type="text" name="fecha_fin" id="fecha_fin" value="" style="width:140px !important;" />
        <span class="msg_error" style="display:none;" id="error_fechas">Por favor ingrese la fecha de inicio y fin de la campa&ntilde;a.</span>
    </div>

    <div class="row" style="display:none">
        <label>Adserver: </label>
        <select name="cmb_adserver" id="cmb_adserver">
            <option value="APPNEXUS">AppNexus</option>
            <option selected="selected" value="DFP">DFP</option>
        </select>
    </div>

    <div class="row">
        <input type="button" class="button_new superButton" value="<<< Atr&aacute;s" id="btn_hacia_paso_1" />

        <input type="button" class="button_new superButton" value="Siguiente paso >>>" id="btn_paso_2" />
        <span class="msg_error" style="display:none;" id="error_paso_1"></span>
        <span id="loader_btn_paso_1" style="display:none">
            <img src="/images/ajax-loader.gif" height="10px" />
        </span>
    </div>
</div>

<!-- paso_3 -->
<div id="paso_3" style="display:none;">

    <!-- SEGMENTACION -->
    <div class="row">
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

    <table id="canales_tematicos" style="display:none;margin-bottom:25px;margin-left:165px;">
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

    <table cellpadding="0px" id="sitios_especificos" style="display:none;margin-bottom:25px;margin-left:165px;">
        <tr>
            <td colspan="3">
                Buscar: <input size="23" type="text" id="txt_sitios_web" name="q" disabled="disabled" style="width:143px !important; margin-bottom:5px;" />
                <span id="loader_comprobar_sitios" style="display:none"><img src="/images/ajax-loader.gif" /> Comprobando...</span>
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
        <label>Rotaci&oacute;n de anuncios: </label>
        <select name="frecuencia" id="cmb_frecuencia">
            <option value="NORMAL" data-multiplicacion="1">Optimizada</option>
            <option value="1x24" data-multiplicacion="2">1 impresi&oacute;n cada 24 horas</option>
            <option value="2x24" data-multiplicacion="3">2 impresiones cada 24 horas</option>
        </select>

        <img src="<?= base_url() ?>images/icon_question.png" id="icon_question" style="top:5px; position:relative;" />
        <div class="tooltiptext" style="display: none;">
            <b>Optimizada</b>: Los anuncios con mejor tasa de clics seran los mas visualizados.<br /><br />
            <b>1 impresión cada 24 horas</b>: Los usuarios visualizaran hasta un anuncio de la campaña cada 24 horas.<br /><br />
            <b>2 impresiones cada 24 horas</b>: Los usuarios visualizaran hasta dos anuncios de la campaña cada 24 horas.
        </div>
    </div>

    <hr />

    <div class="row">
        <label>Dispositivo: </label>
        <input type="checkbox" name="device_desktop" id="device_desktop" value="1" /> Desktops & Laptops
        <input type="checkbox" name="device_tablet" id="device_tablet" value="1" /> Tablets
        <input type="checkbox" name="device_phone" id="device_phone" value="1" /> Phones
    </div>

    <hr />

    <div class="row">
        <label>Tama&ntilde;o: </label>
        <span class="msg_error" style="display:none;" id="error_formato">Por favor seleccione un tipo de formato y asigne la cantidad deseada.</span>
    </div>

    <div id="mostrar_formatos" style="margin-left:165px;margin-bottom:25px;margin-top:-5px"></div>

    <hr />

    <!-- PAISES -->
    <div class="row">
        <label>Pa&iacute;s o Paises: </label>
        <table style="margin-left:165px !important;">
            <tr>
                <td colspan="3">
                    Buscar: <input size="23" type="text" id="txt_paises" name="q" style="width:143px !important; margin-bottom:5px;" />
                    <span class="msg_error" style="display:none;" id="error_paises">Por favor seleccione al menos un pais del listado.</span>
                </td>
            </tr>
            <tr>
                <td>
                    <select style="width: 201px;height:170px;" size="10" id="cmb_paises" name="cmb_paises" multiple="multiple">
                    </select>
                </td>
                <td style="width: 10px; vertical-align: middle !important; padding:0 10px;">
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
                    <select style="width: 201px !important;height:170px;" size="10" id="cmb_paises_2" name="cmb_paises_2" multiple="multiple">
                    </select>
                </td>
            </tr>
        </table>
    </div>

    <hr />

    <div class="row">
        <input type="button" class="button_new superButton" value="<<< Atr&aacute;s" id="btn_hacia_paso_2" />

        <input type="button" class="button_new superButton" value="Siguiente paso >>>" id="btn_paso_3" />
        <span class="msg_error" style="display:none;" id="error_paso_3"></span>
        <span id="loader_btn_paso_3" style="display:none">
            <img src="/images/ajax-loader.gif" height="10px" />
        </span>
    </div>
</div>

<!-- paso_4 -->
<div id="paso_4" style="display:none;">

    <input type="hidden" id="totalImpressions" value="0" />
    <input type="hidden" id="totalClicks" value="0" />
    <input type="hidden" id="valor_cpm_sugerido" value="0" />
    <input type="hidden" id="valor_cpc_sugerido" value="0" />
    <input type="hidden" id="id_valor_sugerido" value="0" />

    <div class="row">
        <label>Modalidad de compra:</label>
        <select name="modalidad_compra" style="width:135px !important;">
            <option value="cpm">CPM</option>
            <option value="cpc">CPC</option>
        </select>
    </div>

    <?php if ($type == 0) { // Campania con Daily   ?>

        <div class="row">
            <label>Valor <span id="modalidad_valor"></span>:</label>
            <?= $this->user_data->moneda ?>
            <input type="text" name="valor_unitario" id="valor_unitario" value="0.00" style="width:100px !important;" />
            <!--
            <div style="font-style: italic; margin: 5px 0 0 165px; font-size:0.8em" id="div_lbl_sugerido">
                El valor sugerido para su segmentaci&oacute;n es de <span class="lbl_modalidad_valor"></span> <span id="lbl_valor_sugerido"></span> <?= $this->user_data->moneda ?>.<br />
            </div>
            -->
            <div style="font-style: italic; margin: 5px 0 0 165px; font-size:0.8em" id="div_lbl_modalidad_valor">
                El valor minimo que MediaFem acepta es <span class="lbl_modalidad_valor"></span> <span id="lbl_valor_minimo"></span> <?= $this->user_data->moneda ?>.
            </div>
        </div>

        <div class="row">
            <label>Cantidad diaria:</label>
            <input type="text" name="cantidad" id="cantidad" value="0" style="width:130px !important;" />
            <span id="tipo_cantidad"> impresiones.</span>
        </div>

        <?php
        if ($habilitar_descuentos) {
            ?>
            <div class="row">
                <label>Inversi&oacute;n bruta diaria: </label><?= $this->user_data->moneda ?>
                <input type="text" name="inversion_bruta" id="inversion_bruta" disabled="disabled" value="0" size="5" style="width:100px !important;" />
            </div>

            <div class="row">
                <label>Descuento: </label>
                <input type="text" name="descuento" id="descuento" value="0" size="3" style="width:100px!important;" /> %
                <span class="msg_error" style="display:none;" id="error_descuento"></span>
            </div>

            <div class="row">
                <label>Comisi&oacute;n: </label>
                <input type="text" name="comision" id="comision" value="0" size="3" style="width:100px !important;" /> %
                <span class="msg_error" style="display:none;" id="error_comision"></span>
            </div>

            <div class="row">
                <label>Inversi&oacute;n diaria: </label>
                <?= $this->user_data->moneda ?>
                <input type="text" name="inversion_neta" id="inversion_neta" value="0" disabled="disabled" style="width:100px !important;" />
                <span class="msg_error" style="display:none;" id="error_inversion">Por favor ingrese la inversi&oacute;n deseada para la campa&ntilde;a.</span>
            </div>

            <div class="row">
                <label>Inversi&oacute;n total:</label>
                <?= $this->user_data->moneda ?>
                <input type="text" name="txt_inversion_total" id="txt_inversion_total" value="0" disabled="disabled" style="width:100px !important;" />
            </div>

            <div class="row" id="lbl_inversion_total">
                <label>Esta campa&ntilde;a estar&aacute; activa desde el <span class="fecha_inicio"></span> hasta el <span class="fecha_fin"></span></label>
            </div>

            <?php
        } else {
            ?>

            <div class="row">
                <label>Inversi&oacute;n diaria:</label>
                <?= $this->user_data->moneda ?>
                <input type="text" name="inversion_neta" id="inversion_neta" value="0" disabled="disabled" style="width:100px !important;" />
            </div>

            <div class="row">
                <label>Inversi&oacute;n total:</label>
                <?= $this->user_data->moneda ?>
                <input type="text" name="txt_inversion_total" id="txt_inversion_total" value="0" disabled="disabled" style="width:100px !important;" />
            </div>

            <div class="row" id="lbl_inversion_total">
                <label>Esta campa&ntilde;a estar&aacute; activa desde el <span class="fecha_inicio"></span> hasta el <span class="fecha_fin"></span></label>
            </div>

            <?php
        }
        ?>

    <?php } else { // Campania Standard   ?>

        <div class="row">
            <label>Valor <span id="modalidad_valor"></span>:</label>
            <?= $this->user_data->moneda ?>
            <input type="text" name="valor_unitario" id="valor_unitario" value="0.00" style="width:100px !important;" />
            <!--<div style="font-style: italic; margin: 5px 0 0 165px; font-size:0.8em" id="div_lbl_sugerido">
                El valor sugerido para su segmentaci&oacute;n es de <span class="lbl_modalidad_valor"></span> <span id="lbl_valor_sugerido"></span> <?= $this->user_data->moneda ?>.<br />
                El valor minimo que MediaFem acepta es <span class="lbl_modalidad_valor"></span> <?= $inversion_cpc_cpm ?> <?= $this->user_data->moneda ?>.
            </div>-->
            <!--
            <div style="font-style: italic; margin: 5px 0 0 165px; font-size:0.8em" id="div_lbl_sugerido">
                El valor sugerido para su segmentaci&oacute;n es de <span class="lbl_modalidad_valor"></span> <span id="lbl_valor_sugerido"></span> <?= $this->user_data->moneda ?>.<br />
            </div>
            -->
            <div style="font-style: italic; margin: 5px 0 0 165px; font-size:0.8em" id="div_lbl_modalidad_valor">
                El valor minimo que MediaFem acepta es <span class="lbl_modalidad_valor"></span> <?= $inversion_cpc_cpm ?> <?= $this->user_data->moneda ?>.
            </div>
        </div>

        <div class="row">
            <label>Cantidad:</label>
            <input type="text" name="cantidad" id="cantidad" value="0" style="width:130px !important;" />
            <span id="tipo_cantidad"> impresiones.</span>
        </div>

        <?php
        if ($habilitar_descuentos) {
            ?>
            <div class="row">
                <label>Inversi&oacute;n bruta: </label><?= $this->user_data->moneda ?>
                <input type="text" name="inversion_bruta" id="inversion_bruta" disabled="disabled" value="0" size="5" style="width:100px !important;" />
            </div>

            <div class="row">
                <label>Descuento: </label>
                <input type="text" name="descuento" id="descuento" value="0" size="3" style="width:100px!important;" /> %
                <span class="msg_error" style="display:none;" id="error_descuento"></span>
            </div>

            <div class="row">
                <label>Comisi&oacute;n: </label>
                <input type="text" name="comision" id="comision" value="0" size="3" style="width:100px !important;" /> %
                <span class="msg_error" style="display:none;" id="error_comision"></span>
            </div>

            <div class="row">
                <label>Inversi&oacute;n total: </label>
                <?= $this->user_data->moneda ?>
                <input type="text" name="inversion_neta" id="inversion_neta" value="0" disabled="disabled" style="width:100px !important;" />
                <span class="msg_error" style="display:none;" id="error_inversion">Por favor ingrese la inversi&oacute;n deseada para la campa&ntilde;a.</span>
            </div>

            <div class="row" id="lbl_inversion_total">
                <label>Esta campa&ntilde;a estar&aacute; activa desde el <span class="fecha_inicio"></span> hasta el <span class="fecha_fin"></span></label>
            </div>

            <?php
        } else {
            ?>

            <div class="row">
                <label>Inversi&oacute;n total:</label>
                <?= $this->user_data->moneda ?>
                <input type="text" name="inversion_neta" id="inversion_neta" value="0" disabled="disabled" style="width:100px !important;" />
            </div>

            <div class="row" id="lbl_inversion_total">
                <label>Esta campa&ntilde;a estar&aacute; activa desde el <span class="fecha_inicio"></span> hasta el <span class="fecha_fin"></span></label>
            </div>

            <?php
        }
        ?>

    <?php } ?>

    <div class="row">
        <input type="button" class="button_new superButton" value="<<< Atr&aacute;s" id="btn_hacia_paso_3" />

        <input type="button" class="button_new superButton" value="Siguiente paso >>>" id="btn_paso_4" />
        <span class="msg_error" id="error_paso_4" style="display: inline-block;"></span>
        <span id="loader_btn_paso_4" style="display:none">
            <img src="/images/ajax-loader.gif" height="10px" />
        </span>
    </div>
</div>

<!-- paso_5 -->
<div id="paso_5" style="display:none;">
    <div class="alerta" style="text-align:left; font-weight:normal;">
        <b>Antes de subir sus anuncios, asegurese que cumplen con las especificaciones técnicas de MediaFem:</b><br />
        - Todos los anuncios deben tener visible la marca o logo del anunciante o dominio<br />
        - Los tamaños de anuncios soportados son: 300x250, 728x90, 160x600, 468x60, 120x600, 336x280, 320x50. Ademas de formatos vídeos y publinotas.<br />
        - Los formatos de anuncios gráficos pueden ser .png, .gif, .jpg y .swf. Con un peso de cada pieza menor a 40kb. La animación de los anuncios no debe durar mas de 30 segundos. Los anuncios deben contener un borde (para fondos transparentes o blancos) o fondo de color que los delimite del contenido del sitio.<br />
        - Los formatos de anuncios de video pueden ser .mov, .mp4, .flv., .mpg, .avi. y .wmv. Recomendamos .mov y .mpg. Con una longitud máxima del video de 30 segundos y un peso del video menor a 60 MB.<br />
        - Las publinotas deben tener solo una imagen y no deben exceder las 400 palabras.<br /><br />

        Ademas existen mas especificaciones sobre el tipo de contenido a anunciarse y algunos detalles técnicos a tener en cuenta. Para conocer mas visite nuestras <a href="http://www.mediafem.com/files/MediaFem_Especificaciones.pdf" target="_BLANK">Especificaciones técnicas</a>.
    </div>

    <div class="row">
        Pulse <b>"Agregar archivos"</b>; seleccione archivos <b>.ZIP, .PNG, .JPG, .GIF, .SWF, .DOC, .DOCx, .PDF, .TXT, .XLS o .XLSx </b> y luego pulse <b>"Subir los archivos seleccionados"</b>
    </div>

    <div id="error_al_subir" class="msg_error"></div>

    <div class="row">
        <div id="uploader"></div>
    </div>

    <div class="row">
        <input type="button" class="button_new superButton" value="<<< Atr&aacute;s" id="btn_hacia_paso_4" />

        <input type="button" class="button_new superButton" value="Siguiente paso >>>" id="btn_paso_5" />
        <span class="msg_error" style="display:none;" id="error_paso_5"></span>
        <span id="loader_btn_paso_5" style="display:none">
            <img src="/images/ajax-loader.gif" height="10px" />
        </span>
    </div>
</div>

<!-- paso_6 -->
<div id="paso_6" style="display:none;">
    <div id="resumen_campania" class="row"></div>

    <div class="row">
        <input type="checkbox" name="chk_acepto_terminos" id="chk_acepto_terminos" value="1" /> Acepto los <a href="https://ayuda.mediafem.com/mediafem-sitios/conceptos-basicos-mediafem-sitios/politicas-del-programa-mediafem-para-sitios" target="_BLANK">t&eacute;rminos y condiciones</a> <a href="http://ayuda.mediafem.com/mediafem-anunciantes/terminos-y-condiciones-de-la-orden-de-compra-de-mediafem" target="_BLANK">de la orden de compra</a>.
    </div>

    <div class="row">
        <input type="button" class="button_new superButton" value="<<< Atr&aacute;s" id="btn_hacia_paso_5" />

        <input type="button" name="btn_finalizar" id="btn_finalizar" value="Aceptar orden de compra" class="button_new superButton" />

        <span id="info" style="display:none"></span>

        <span id="loader_submit" style="display:none">
            <img src="/images/ajax-loader.gif" height="10px" />
            Almacenando campa&ntilde;a, espere por favor...
        </span>

        <span class="msg_error" style="display:none;" id="error_campania">Se encontraron errores al intentar crear la campa&ntilde;a.</span>
    </div>
</div>

<a href="#" data-reveal-id="ModalCantidadDisponible" style="display: none;">&nbsp;</a>
<div id="ModalCantidadDisponible" class="reveal-modal">
    <div class="content">

        <form action="#" method="post">
            <h2 class="border_bottom">Atenci&oacute;n:</h2>

            <div class="row" style="margin-bottom: 30px;">Los sitios seleccionados para la campa&ntilde;a no cuentan con el volumen seleccionado. &iquest;Desea continuar de todas maneras?</div>

            <div style="text-align: right;">
                <a id="btn_modal_paso_4_ok" class="button_new" style="font-weight: bold;">
                    <span id="continuar_modal_paso_4">
                        Si, continuar.
                    </span>
                    <span id="loader_modal_paso_4" style="display:none">
                        <img src="/images/ajax-loader.gif" height="10px" />
                        espere por favor...
                    </span>
                </a>
                <a id="btn_modal_paso_4_cancel" class="button_new" style="font-weight: bold;">Cambiar la cantidad solicitada.</a>
            </div>
        </form>
    </div>
</div>