<style type="text/css">
    #form_alta_campania{
        margin: 15px;
    }

    #form_alta_campania div{
        clear: none;
        /*margin: 8px 0;*/
    }

    #form_alta_campania div label{
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

    .ocultar{
        display: none;
        margin-bottom: 20px;
    }

    .ocultar label, #label_nombre_anunciante{
        margin-bottom: 15px !important;
    }

    #pasos {
        border-bottom: 1px solid #ddd;
        color: #ddd;
        font: normal bold 15px/15px Arial,Helvetica,sans-serif;
        margin: 0 0 20px;
        padding: 0 0 10px;
    }

    #pasos span {
        margin: 0 20px 0 0;
    }

    #pasos span.activo {
        color: #000;
    }

    .msg_error {
        color: red;
    }

    .item_tipo_campania{
        border-bottom: 1px solid #ddd;
        cursor: pointer;
        font: bold normal 15px Arial, Helvetica, sans-serif;
        padding: 15px 0;
    }

    .item_tipo_campania:hover{
        background: #fff url(<?= base_url() ?>images/icon_right_arrow.png) center right no-repeat;
    }

    .item_tipo_campania:first-of-type{
        border-top: 1px solid #ddd;
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

        function calcular_neto(){
            var inversion_comision = (($('#inversion_bruta').val() * $('#comision').val()) / 100);
            var inversion_descuento = (($('#inversion_bruta').val() * $('#descuento').val()) / 100);
            var inversion_total = $('#inversion_bruta').val() - inversion_comision - inversion_descuento;

            $('#inversion').val((Math.round( inversion_total * 100 ) / 100).toFixed(2));
        }

        $('#inversion_bruta').focusout(function(){
            if(isNaN($(this).val()) || $(this).val() < 0){
                $('#error_comision').html('Por favor ingrese una comisión valida.').css('display', 'inline');
                $(this).css('borderColor', 'red');
                return false;
            }else{
                $('#error_comision').css('display', 'none');
                $(this).css('borderColor', '');
            }

            calcular_neto();
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

            calcular_neto();
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

            calcular_neto();
        });

        $('.msg_error').css('display', 'none');

        $('#id_anunciante').val(<?= $id_anunciante ?>);

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

            if($("#archivo_subido").val() == 1){

                var id_campania = $('#id_campania').val();
                var tipo_campania = $('#tipo_campana').val();

                $('#loader_segmentaciones').css('display', 'inline');

                $('#mostrar_formatos').load('/campania/mostrar_formatos/' + id_campania + '/' + tipo_campania, function(){
                    $('#loader_segmentaciones').css('display', 'none');
                });
            }
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

        $('#inversion').keyup(function(){
            var text = $('#inversion').val();
            text = text.replace(',','.');
            $('#inversion').val(text);
        });

        /***************************** CREAR CAMPAÑA *****************************/
        $("#uploader").plupload({
            runtimes : 'html5,gears,browserplus,silverlight,flash,html4',
            url : '/campania/subir_archivos',
            max_file_size : '10mb',
            chunk_size : '1mb',
            unique_names : false,
            multipart: true,
            filters : [
                {title : "Imagen", extensions : "jpg,gif,png"},
                {title : "Zip", extensions : "zip"},
                {title : "SWF", extensions : "swf"},
                {title : "TXT", extensions : "txt"},
                {title : "DOC", extensions : "doc"},
                {title : "DOCx", extensions : "docx"},
                {title : "XLS", extensions : "xls"},
                {title : "XLSs", extensions : "xlsx"}
            ],

            // Flash/Silverlight paths
            flash_swf_url: '/js/plupload.flash.swf',
            silverlight_xap_url: '/js/plupload.silverlight.xap',

            init: {
                FileUploaded: function(up, file, info) {
                    if(info.response != 'ok'){
                        $("#error_al_subir").html($("#error_al_subir").html() + info.response).css("display", "block");
                        //$("#error_creatividades").html('Hay creatividades que no corresponden a un formato de anuncio aceptado.').css("display", "inline");
                    }else{
                        $("#archivo_subido").val("1");
                    }
                }
            }
        });

        $('#btn_hacia_paso_1').click(function(){
            $("#archivo_subido").val("0");
            $("#div_paso_2").css("display", "none");
            $("#div_paso_1").css("display", "block");

            $(".div_paso_2").removeClass("activo");
            $(".div_paso_1").addClass("activo");
        });

        $('#btn_hacia_paso_2').click(function(){
            $("#div_paso_3").css("display", "none");
            $("#div_paso_2").css("display", "block");

            $(".div_paso_3").removeClass("activo");
            $(".div_paso_2").addClass("activo");
        });

        $('#btn_hacia_paso_3').click(function(){
            $('#label_nombre_anunciante').css('display', 'inline');
            $('#nombre_anunciante_campania').css('display', 'inline');
            $('.ocultar').css('display', 'block');

            $("#div_paso_4").css("display", "none");
            $("#div_paso_3").css("display", "block");

            $(".div_paso_4").removeClass("activo");
            $(".div_paso_3").addClass("activo");
        });

        $('.item_tipo_campania').click(function(){
            $("#div_paso_0").css("display", "none");
            $("#div_paso_1").css("display", "block");

            $(".nombre_anunciante").removeClass("ocultar");

            $(".div_paso_0").removeClass("activo");
            $(".div_paso_1").addClass("activo");
        });

        $('#btn_paso_1').click(function(e){
            var error = false;
            $("#error_paso_1").css("display", "none");
            $("#loader_btn_paso_1").css("display", "inline");

            $("#error_nombre_campania").html(' ');

            $('.ui-icon-circle-check').click();

            // valido el anunciante
            if($('#creado_desde_sitio').val() == 1){
                var id_anunciante = $.trim($('#id_anunciante').val());
            }else{
                if($('#anunciante_nuevo_creado').val() == 1){
                    var id_anunciante = $.trim($('#id_anunciante').val());

                }else{
                    var id_anunciante = $("#cmb_anunciantes").find(':selected').val();
                }
            }

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

            var tipo_campania = $.trim($('#tipo_campana').val());

            //valido la fecha de inicio
            var fecha_inicio = $.trim( $('#fecha_inicio').val() );
            var fecha_fin = $.trim( $('#fecha_fin').val() );

            if( fecha_inicio == '' || fecha_fin == '' ){
                $("#error_fechas").html('Por favor ingrese la fecha de inicio y fin de la campaña.').css("display", "inline");
                error = true;
            }else{
                $("#error_fechas").css("display", "none");
            }

            //valido la inversion
            var inversion = $.trim( $('#inversion').val() );
            if( inversion == '' || isNaN(inversion) || inversion <= 0){
                $("#error_inversion").css("display", "inline");
                error = true;
            }else{
                $("#error_inversion").css("display", "none");
            }

            if(error == true){
                $(this).attr('disabled', false);

                $("#loader_btn_paso_1").css("display", "none");

                return false;
            }

            var inversion_bruta = 0;
            var descuento = 0;
            var comision = 0;

            if ($('#inversion_bruta').length){
                var inversion_bruta = $.trim( $('#inversion_bruta').val() );
                var descuento = $.trim( $('#descuento').val() );
                var comision = $.trim( $('#comision').val() );
            }

            var form_data = {
                id_anunciante: id_anunciante,
                tipo_campania: tipo_campania,
                nombre_campania: nombre_campania,
                fecha_inicio: fecha_inicio,
                fecha_fin: fecha_fin,
                inversion: inversion,
                inversion_bruta : inversion_bruta,
                descuento : descuento,
                comision : comision
            };

            $.ajax({
                type: "POST",
                url: "/campania/insertar_campania_primer_paso/",
                data: form_data,
                dataType: "json",
                success: function(msg){
                    if(msg.validate){
                        $("#id_campania").val(msg.id_campania);

                        $("#div_paso_1").css("display", "none");
                        $("#div_paso_2").css("display", "inline");

                        $("#loader_btn_paso_1").css("display", "none");

                        $("#cmb_anunciantes").css("display", "none");

                        $("#label_nombre_anunciante").html($("#cmb_anunciantes").find(':selected').text());
                        $("#label_nombre_campana").html($.trim($("#nombre_campania").val()));
                        $("#label_fechas").html($.trim($("#fecha_inicio").val()) + ' al ' + $.trim($("#fecha_fin").val()));

                        $("#label_inversion").html('U$S ' + msg.inversion_neta);
                        $(".ocultar").css("display", "block");
                        $("#label_nombre_anunciante").css("display", "inline");
                        $("#label_nombre_campana").css("display", "inline");
                        $("#label_fechas").css("display", "inline");
                        $("#label_inversion").css("display", "inline");

                        $("#crear_anunciante").css("display", "none");
                        $("#nuevo_anunciante").css("display", "none");

                        //Habilitar 2 Paso
                        $(".div_paso_1").removeClass("activo");
                        $(".div_paso_2").addClass("activo");
                    }else{
                        $("#error_paso_1").html(msg.error);
                        $("#error_paso_1").css("display", "inline");
                        $("#loader_btn_paso_1").css("display", "none");
                    }
                }
            });
        });

        $('#btn_paso_2').click(function(e){
            $("#loader_btn_paso_2").css("display", "inline");

            if($("#archivo_subido").val() == 1){

                var id_campania = $('#id_campania').val();
                var tipo_campania = $('#tipo_campana').val();

                $('#mostrar_formatos').load('/campania/mostrar_formatos/' + id_campania + '/' + tipo_campania, function(){
                    //Habilitar 3 Paso
                    $("#div_paso_2").css("display", "none");
                    $("#div_paso_3").css("display", "inline");

                    $("#loader_btn_paso_2").css("display", "none");

                    $(".div_paso_2").removeClass("activo");
                    $(".div_paso_3").addClass("activo");
                })
            }else{
                $("#error_creatividades").html('Debe subir al menos una creatividad').css("display", "inline");

                $("#loader_btn_paso_2").css("display", "none");
            }
        });

        $('#btn_paso_3').click(function(e){
            e.preventDefault();

            $("#loader_btn_paso_3").css("display", "inline");
            $("#error_paso_3").html(' ');

            var error = false;
            var id_campania = $("#id_campania").val();

            var segmentacion = $('#cmb_segmentaciones').val();

            var id_canales_tematicos = "";
            var id_sitios = "";

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
                $("#cmb_sitios_2 option").each(function(){
                    id_sitios = id_sitios + $(this).attr('value') + ";";
                });

                if(id_sitios == ''){
                    $("#error_segmentacion").html('Por favor seleccione al menos un sitio.').css("display", "inline");
                    error = true;
                }
            }

            // valido los formatos seleccionados
            var formatos = "";
            var cantidad_total = 0;
            $("input[name='chk_formatos[]']:checked").each(function(){
                var id = $(this).val();
                var rel = $(this).attr('rel');
                var modalidad = $("#modalidad_"+rel).find(':selected').val();
                var cantidad = parseFloat($("#cantidad_"+rel).val());
                var monto = $.trim($("#monto_"+rel).val());
                var pagina_destino = $.trim($("#pagina_destino_"+rel).val());

                if(isNaN(cantidad) || cantidad <= 0){
                    $("#error_formato").html('').css("display", "inline");
                    error = true;
                    formatos = 'a';

                    $("#cantidad_"+rel).css('borderColor', 'red');
                }

                cantidad_total += cantidad;

                if(isNaN(monto) || monto <= 0){
                    $("#error_formato").html('').css("display", "inline");
                    error = true;
                    formatos = 'a';
                    $("#monto_"+rel).css('borderColor', 'red');
                }

                formatos = formatos+id+":"+modalidad+":"+cantidad+":"+monto+":"+pagina_destino+";";
            });

            if( cantidad_total != 100){
                $("#error_paso_3").html('La suma de el/los porcentajes debe ser igual a 100').css("display", "inline");
                error = true;
            }

            if(formatos == '' && error == false){
                $("#error_formato").html('Por favor seleccione al menos un tamaño.').css("display", "inline");
                error = true;
            }

            $("#error_paises").css("display", "none");

            // valido los paises seleccionados
            var id_paises = "";
            $("#cmb_paises_2 option").each(function(){
                id_paises = id_paises + $(this).attr('value') + ";";
            });

            if(id_paises == ''){
                $("#error_paises").html('Por favor seleccione al menos un pa&iacute;s del listado.').css("display", "inline");
                return false;
            }

            // si surgió algun error durante la validación.
            if(error == true){
                $('#loader_btn_paso_3').fadeOut("fast", function(){
                    $("#error_paso_3").css("display", "inline");
                });

                $(this).attr('disabled', false);

                return false;
            }

            $('#resumen_campania').addClass('textCenter');
            $('#resumen_campania').html('<img src="/images/loader.gif"/>');

            var form_data = {
                id_campania : id_campania,
                segmentacion: segmentacion,
                id_sitios : id_sitios,
                id_canales_tematicos: id_canales_tematicos,
                formatos: formatos,
                id_paises: id_paises
            };

            $.ajax({
                type: "POST",
                url: "/campania/insertar_campania_tercer_paso/",
                data: form_data,
                dataType: "json",
                success: function(msg){

                    if(msg.validate){
                        //Habilitar 3 Paso
                        $("#div_paso_3").css("display", "none");
                        $("#div_paso_4").css("display", "inline");

                        $("#loader_btn_paso_3").css("display", "none");

                        $(".div_paso_3").removeClass("activo");
                        $(".div_paso_4").addClass("activo");

                        // oculto los datos para mostrar resumen
                        $('#label_nombre_anunciante').css('display', 'none');
                        $('#nombre_anunciante_campania').css('display', 'none');
                        $('.ocultar').css('display', 'none');

                        $('#resumen_campania').load('/campania/ver/' + id_campania, function(){
                            $('#resumen_campania').removeClass('textCenter');
                        });
                    }else{
                        //alert("error");
                        $("#loader_btn_paso_3").css("display", "none");
                    }
                }
            });

            $('#btn_finalizar').click(function(e){
                e.preventDefault();

                var nombre_campania = $.trim($('#nombre_campania').val() );

                var form_data = {
                    nombre_campania: nombre_campania,
                    id_campania: id_campania
                };

                $("#loader_submit").css("display", "inline");

                $(this).attr('disabled', 'disabled');

                $.ajax({
                    type: "POST",
                    url: "/campania/insertar_campania_finalizar/",
                    data: form_data,
                    dataType: "json",
                    success: function(msg){
                        if(msg.validate){
                            window.location.replace("/campania?creada_ok=true");
                            return false;
                        }else{
                            $("#info").html(msg.error);
                            $(this).attr('disabled', false);
                        }
                    }
                });
            });
        });

        $('#crear_anunciante').click( function(){
            $('#ocultar_paso_1').fadeOut('fast');
            $('#combo_anunciantes').fadeOut(0);
            $('#nuevo_anunciante').fadeIn('fast');
            $('#id_anunciante').val('0');
        });

        $('#aceptar_nuevo_anunciante').click( function(){
            $(this).attr('disabled', 'disabled');
            $('#new_anunciante').attr('disabled', 'disabled');
            $('#error_anunciante').css('color', 'red');
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
                        $('#anunciante_nuevo_creado').val(1);
                        $('#id_anunciante').val( msg.id_anunciante );
                        $('#error_anunciante').html(' ');
                        $('#new_anunciante').attr('disabled', 'disabled');
                        $('#aceptar_nuevo_anunciante').attr('disabled', 'disabled');
                        $('#cancelar_nuevo_anunciante').attr('disabled', 'disabled');

                        $('#error_anunciante').html('Anunciante creado correctamente.');
                        $('#error_anunciante').css({'display': 'inline', 'color': 'green'}).delay(1500).fadeOut('slow');

                        $('#aceptar_nuevo_anunciante').css('display', 'none');
                        $('#cancelar_nuevo_anunciante').css('display', 'none');

                        $('#ocultar_paso_1').fadeIn('fast');
                    }else{
                        $('#id_anunciante').val('0');

                        $('#error_anunciante').html(msg.error);
                        $('#error_anunciante').css('display', 'inline');

                        $(this).attr('disabled', false);
                        $('#new_anunciante').attr('disabled', false);
                        $('#loader_anunciantes').css('display', 'none');

                        $('#new_anunciante').focus();
                    }
                }
            });
        });

        $('#cancelar_nuevo_anunciante').click( function(){
            $('#nuevo_anunciante').fadeOut(0);
            $('#combo_anunciantes').fadeIn('fast');

            $('#loader_anunciantes').css('display', 'none');
            $('#aceptar_nuevo_anunciante').attr('disabled', false);
            $('#new_anunciante').attr('disabled', false);
            $('#new_anunciante').val('');
            $('#id_anunciante').val('0');

            $("#error_anunciante").css('display', 'none');

            $('#ocultar_paso_1').fadeIn('fast');
        });
    });
</script>

<div id="form_alta_campania">
    <input type="hidden" id="id_sitio" name="id_sitio" value=""/>
    <input type="hidden" id="archivo_subido" name="archivo_subido" value="0"/>
    <input type="hidden" id="id_campania" name="id_campania" value=""/>
    <input type="hidden" id="anunciante_nuevo_creado" name="anunciante_nuevo_creado" value="0"/>
    <input type="hidden" id="creado_desde_sitio" name="creado_desde_sitio" value="<?= $creado_desde_sitio ?> "/>
    <input type="hidden" id="tipo_campana" name="tipo_campana" value=""/>

    <div id="pasos">
        <span class="div_paso_0 activo">Formato de la campa&ntilde;a</span>
        <span class="div_paso_1">Datos de la campa&ntilde;a</span>
        <span class="div_paso_2">Subir materiales</span>
        <span class="div_paso_3">Segmentaci&oacute;n</span>
        <span class="div_paso_4">Resumen</span>
    </div>

    <?
    if ($creado_desde_sitio) {
        ?>

        <div class="ocultar nombre_anunciante">
            <label id="nombre_anunciante_campania">Anunciante: </label>
            <span id="anunciante" style="font-size: 12px;">
                <strong><?= $nombre_anunciante ?></strong>
            </span>
            <input type="hidden" name="id_anunciante" id="id_anunciante" value="0" />
            <span class="msg_error" style="display:none;" id="error_anunciante"></span>
        </div>

        <?php
    } else {
        ?>

        <div class="ocultar nombre_anunciante">
            <label id="nombre_anunciante_campania">Anunciante: </label>
            <input type="hidden" name="id_anunciante" id="id_anunciante" value="0" />
            <span id="combo_anunciantes">
                <?php if (sizeof($anunciantes_adserver)) { ?>
                    <select name="cmb_anunciantes" id="cmb_anunciantes">
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

            <label id="label_nombre_anunciante" style="display: none;"></label>
        </div>

        <div class="ocultar">
            <div>
                <br />

                <label>Nombre de la campa&ntilde;a: </label>
                <label id="label_nombre_campana" style="display: none;"></label>

                <br />

                <label>Fecha: </label>
                <label id="label_fechas" style="display: none;"></label>

                <br />

                <label>Inversi&oacute;n neta: </label>
                <label id="label_inversion" style="display: none;"></label>

            </div>

            <span id="nuevo_anunciante" style="display:none;">
                <input type="text" name="new_anunciante" id="new_anunciante" maxlength="127" value="" />
                <input type="button" name="aceptar_nuevo_anunciante" id="aceptar_nuevo_anunciante" value="Crear" class="button_new" />
                <input type="button" name="cancelar_nuevo_anunciante" id="cancelar_nuevo_anunciante" value="Cancelar" class="button_new" />
            </span>
            <img src="/images/ajax-loader.gif" id="loader_anunciantes" height="10" style="display:none" />
            <span class="msg_error" style="display:none;" id="error_anunciante"></span>
        </div>

        <?php
    }
    ?>

    <div id="div_paso_0">
        <div class="item_tipo_campania" onClick="$('#tipo_campana').val('tradicional');">
            Tradicionales
        </div>
        <div class="item_tipo_campania" onClick="$('#tipo_campana').val('layer');">
            Layer
        </div>
        <div class="item_tipo_campania" onClick="$('#tipo_campana').val('skin');">
            Skin
        </div>
        <div class="item_tipo_campania" onClick="$('#tipo_campana').val('expandible');">
            Expandible
        </div>
        <div class="item_tipo_campania" onClick="$('#tipo_campana').val('video_zocalo');">
            Video Z&oacute;calo
        </div>
        <div class="item_tipo_campania" onClick="$('#tipo_campana').val('video_viral');">
            Video Viral
        </div>
    </div>

    <div id="ocultar_paso_1" >
        <div id="div_paso_1" style="margin-top:20px; display:none;">
            <div>
                <label>Nombre de la campa&ntilde;a: </label>
                <input type="text" name="nombre_campania" maxlength="128" id="nombre_campania" value="" />
                <span class="msg_error" style="display:none;" id="error_nombre_campania"></span>
            </div>

            <div style="margin-top:15px;">
                <label>Fecha: </label>
                <input type="text" name="fecha_inicio" id="fecha_inicio" value="" style="width:140px !important;" /> al
                <input type="text" name="fecha_fin" id="fecha_fin" value="" style="width:140px !important;" />
                <span class="msg_error" style="display:none;" id="error_fechas">Por favor ingrese la fecha de inicio y fin de la campa&ntilde;a.</span>
            </div>

            <?php
            if ($habilitar_descuentos) {
                ?>
                <div style="margin-top:15px;">
                    <label>Inversi&oacute;n bruta: </label>US$
                    <input type="text" name="inversion_bruta" id="inversion_bruta" value="0" size="5" style="width:121px !important;" />
                </div>

                <div style="margin-top:15px;">
                    <label>Descuento: </label>
                    <input type="text" name="descuento" id="descuento" value="0" size="3" style="width:121px!important;" /> %
                    <span class="msg_error" style="display:none;" id="error_descuento"></span>
                </div>

                <div style="margin-top:15px;">
                    <label>Comisi&oacute;n: </label>
                    <input type="text" name="comision" id="comision" value="0" size="3" style="width:121px !important;" /> %
                    <span class="msg_error" style="display:none;" id="error_comision"></span>
                </div>

                <div style="margin-top:15px;">
                    <label>Inversi&oacute;n neta: </label>
                    U$S <input readonly="true" type="text" name="inversion" id="inversion" value="" style="width:121px !important;" />
                    <span class="msg_error" style="display:none;" id="error_inversion">Por favor ingrese la inversi&oacute;n deseada para la campa&ntilde;a.</span>
                </div>

                <?php
            } else {
                ?>

                <div style="margin-top:15px;">
                    <label>Inversi&oacute;n neta: </label>
                    U$S <input type="text" name="inversion" id="inversion" value="" style="width:121px !important;" />
                    <span class="msg_error" style="display:none;" id="error_inversion">Por favor ingrese la inversi&oacute;n deseada para la campa&ntilde;a.</span>
                </div>

                <?php
            }
            ?>
            <div style="margin-top:25px;">
                <input type="button" class="button_new" value="Siguiente paso >>>" id="btn_paso_1" style="font-weight: bold; padding: 6px 16px; background-color: #ddd;" />
                <span class="msg_error" style="display:none;" id="error_paso_1"></span>
                <span id="loader_btn_paso_1" style="display:none">
                    <img src="/images/ajax-loader.gif" height="10px" />
                </span>
            </div>
        </div>
    </div>


    <div id="div_paso_2" style="display:none;">

        <div style="margin-top:10px;font-size: 12px;">
            Pulse <b>"Agregar archivos"</b>; seleccione archivos <b>.ZIP, .PNG, .JPG, .GIF, .SWF, .DOC, .DOCx, .TXT, .XLS o .XLSx </b> y luego pulse <b>"Subir los archivos seleccionados"</b>
        </div>

        <div id="error_al_subir" class="msg_error" style="margin-top:20px;">

        </div>

        <div style="margin-top:10px;">
            <div id="uploader"></div>
        </div>

        <div style="margin-top:25px;">
            <input type="button" class="button_new" value="<<< Atr&aacute;s" id="btn_hacia_paso_1" style="font-weight: bold; padding: 6px 16px; background-color: #ddd;" />

            <input type="button" class="button_new" value="Siguiente paso >>>" id="btn_paso_2" style="font-weight: bold; padding: 6px 16px; background-color: #ddd;" />
            <span id="loader_btn_paso_2" style="display:none">
                <img src="/images/ajax-loader.gif" height="10px" />
            </span>
            <span class="msg_error" style="display:none;" id="error_creatividades"></span>
        </div>
    </div>

    <div id="div_paso_3" style="margin-top:20px;display:none;">

        <div style="margin-bottom: 15px;">
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

        <hr style="border: none; border-bottom:1px solid #ddd;margin:30px 0 25px" />

        <div>
            <label>Tama&ntilde;o: </label>
            <span class="msg_error" style="display:none;" id="error_formato">Por favor seleccione un tipo de formato y asigne la cantidad deseada.</span>
        </div>

        <div id="mostrar_formatos" style="margin-left:165px;margin-bottom:25px;margin-top:-5px">

        </div>

        <hr style="border: none; border-bottom:1px solid #ddd;margin-bottom:25px" />

        <div>
            <div id="paises_filtrados"></div>
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

        <div style="margin-top:25px;">
            <input type="button" class="button_new" value="<<< Atr&aacute;s" id="btn_hacia_paso_2" style="font-weight: bold; padding: 6px 16px; background-color: #ddd;" />

            <input type="button" class="button_new" value="Siguiente paso >>>" id="btn_paso_3" style="font-weight: bold; padding: 6px 16px; background-color: #ddd;" />
            <span id="loader_btn_paso_3" style="display:none">
                <img src="/images/ajax-loader.gif" height="10px" />
            </span>
            <span class="msg_error" style="display:none;" id="error_paso_3"></span>
        </div>

    </div>

    <div id="div_paso_4" style="margin-top:20px;display:none;">

        <div id="resumen_campania">
        </div>

        <div style="margin-top: 20px;">
            <input type="button" class="button_new" value="<<< Atr&aacute;s" id="btn_hacia_paso_3" style="font-weight: bold; padding: 6px 16px; background-color: #ddd;" />

            <input type="button" name="btn_finalizar" id="btn_finalizar" value="Finalizar" class="button_new" style="font-weight: bold; padding: 6px 16px; background-color: #ddd;" />

            <span id="info" style="display:none"></span>

            <span id="loader_submit" style="display:none">
                <img src="/images/ajax-loader.gif" height="10px" />
                Almacenando campa&ntilde;a, aguarde por favor...
            </span>

            <span class="msg_error" style="display:none;" id="error_campania">Se encontraron errores al intentar crear la campa&ntilde;a.</span>
        </div>

    </div>
</div>