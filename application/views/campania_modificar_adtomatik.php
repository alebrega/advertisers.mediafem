<style type="text/css">
    label{
        font: normal bold 12px Arial, Helvetica, sans-serif;
        width: 140px;
        display: inline-block;
    }

    select, input[type="text"]{
        border-radius: 2px; /* El estándar.*/
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

    .msg_error {
        color: red;
        display: none;
    }

    #inversion_neta_modificar_<?= $id_campania ?>, #txt_inversion_total_modificar_<?= $id_campania ?>{
        background-color: #ddd;
    }


    h2{
        font-size: 1.1em !important;
        margin-top: 40px;
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

    $().ready(function(){
        var id_campana = <?= $id_campania ?>;

        var gbl_valor_minimo = 0;
        
        var modifico_audiencias = false;


        var habilitar_descuentos = <?= $habilitar_descuentos ?>;

        $('#tbl_materiales_modificar_<?= $id_campania ?>').load('/campania/mostrar_materiales/' + id_campana);

        // SE USA PARA FILTRAR PAISES Y SITIOS
        function strpos(cadena, busqueda){
            var i = (cadena.toLowerCase()).indexOf(busqueda.toLowerCase());
            return i === -1 ? false : true;
        }

        // SITIOS ESPECIFICOS **************************************************
        var mostrar_listado_sitios_modificar_<?= $id_campania ?> = false;
        var cantidad_sitios_modificar_<?= $id_campania ?> = 0;
        var sitios_modificar_<?= $id_campania ?> = '';

        var form_data = {};

        $.ajax({
            type: "GET",
            url: "/campania/get_publishers_y_sitios_json/",
            data: form_data,
            dataType: "json",
            success: function(msg){
                if(msg){
                    sitios_modificar_<?= $id_campania ?> = eval(msg);
                    cantidad_sitios_modificar_<?= $id_campania ?> = sitios_modificar_<?= $id_campania ?>.length;

                    // recorro uno por uno los publisher y creo un <objgroup>
                    for(var a = 0; a < cantidad_sitios_modificar_<?= $id_campania ?>; a++){
                        if(sitios_modificar_<?= $id_campania ?>[a].url != ''){
                            if($('#cmb_sitios_2_modificar_<?= $id_campania ?> option[value="' + sitios_modificar_<?= $id_campania ?>[a].id_sitio + '"]').length == 0){
                                $("#cmb_sitios_modificar_<?= $id_campania ?>").append('<option value="' + sitios_modificar_<?= $id_campania ?>[a].id_sitio + '">' + sitios_modificar_<?= $id_campania ?>[a].url + '</option>');
                            }
                        }
                    }

                    $("#loader_cmb_sitios_modificar_<?= $id_campania ?>").css("display", "none");
                    $("#btn_pasar_sitio_del_text_modificar_<?= $id_campania ?>").attr("disabled", "");
                    $("#txt_sitios_web_modificar_<?= $id_campania ?>").attr("disabled", false);
                    $("#cmb_sitios_modificar_<?= $id_campania ?>").attr("disabled", false);
                    $("#cmb_sitios_2_modificar_<?= $id_campania ?>").attr("disabled", false);
                    $("#btn_borrar_sitio_modificar_<?= $id_campania ?>").attr("disabled", false);
                    $("#btn_pasar_sitio_modificar_<?= $id_campania ?>").attr("disabled", false);
                    mostrar_listado_sitios_modificar_<?= $id_campania ?> = true;
                }else{
                    alert(msg.error);
                }
            }
        });

        $("#txt_sitios_web_modificar_<?= $id_campania ?>").keyup( function(event){
            $('#cmb_sitios_modificar_<?= $id_campania ?>').html('');

            var busqueda_modificar_<?= $id_campania ?> = $(this).val();

            if(busqueda_modificar_<?= $id_campania ?> != ''){
                var cantidad_sitios_modificar_<?= $id_campania ?> = sitios_modificar_<?= $id_campania ?>.length;

                // recorro uno por uno los publisher y creo un <objgroup>
                for(var a=0; a < cantidad_sitios_modificar_<?= $id_campania ?>; a++){
                    if(strpos( sitios_modificar_<?= $id_campania ?>[a].url, busqueda_modificar_<?= $id_campania ?> ) != false)
                    $("#cmb_sitios_modificar_<?= $id_campania ?>").append('<option value="' + sitios_modificar_<?= $id_campania ?>[a].id_sitio + '">' + sitios_modificar_<?= $id_campania ?>[a].url + '</option>');
                }
            }else{
                var cantidad_sitios_modificar_<?= $id_campania ?> = sitios_modificar_<?= $id_campania ?>.length;

                for(var a = 0; a < cantidad_sitios_modificar_<?= $id_campania ?>; a++){
                    if($('#cmb_sitios_2_modificar_<?= $id_campania ?> option[value="' + sitios_modificar_<?= $id_campania ?>[a].id_sitio + '"]').length == 0)
                    $("#cmb_sitios_modificar_<?= $id_campania ?>").append('<option value="' + sitios_modificar_<?= $id_campania ?>[a].id_sitio + '">' + sitios_modificar_<?= $id_campania ?>[a].url + '</option>');
                }
            }

        });

        $("#cmb_sitios_modificar_<?= $id_campania ?>").dblclick( function (){
            $("#btn_pasar_sitio_modificar_<?= $id_campania ?>").click();
        });

        $("#cmb_sitios_2_modificar_<?= $id_campania ?>").dblclick( function (){
            $("#btn_borrar_sitio_modificar_<?= $id_campania ?>").click();
        });

        $("#btn_pasar_sitio_modificar_<?= $id_campania ?>").click( function (){
            $('#cmb_sitios_modificar_<?= $id_campania ?> option:selected').appendTo("#cmb_sitios_2_modificar_<?= $id_campania ?>");
        });

        $("#btn_borrar_sitio_modificar_<?= $id_campania ?>").click( function (){
            var seleccionados_modificar_<?= $id_campania ?> = $('#cmb_sitios_2_modificar_<?= $id_campania ?> option:selected');
            for(var a = 0; a < seleccionados_modificar_<?= $id_campania ?>.length; a++){
                $(seleccionados_modificar_<?= $id_campania ?>[a]).appendTo($("#cmb_sitios_modificar_<?= $id_campania ?>"));
            }
        });

        // FECHA DE INICIO Y FIN ***********************************************
        $.datepicker.setDefaults($.datepicker.regional['es']);
        $("#fecha_inicio_modificar_<?= $id_campania ?>").datepicker({ dateFormat:'dd-mm-yy' });
        $("#fecha_fin_modificar_<?= $id_campania ?>").datepicker({ dateFormat:'dd-mm-yy' });

        $("#fecha_inicio_modificar_<?= $id_campania ?>").change(function(){
            calcular_inversion_neta();
        });

        $("#fecha_fin_modificar_<?= $id_campania ?>").change(function(){
            calcular_inversion_neta();
        });

        // FORMATOS SELECCIONADOS **********************************************
        // cargo los formatos para la campana
        var tipo_campana = '<?= $tipo_campania ?>';
        //alert('/campania/mostrar_formatos/' + tipo_campana + '/' + '<?= $id_campania ?>');
        $('#mostrar_formatos_modificar_<?= $id_campania ?>').load('/campania/mostrar_formatos/' + tipo_campana + '/' + <?= $id_campania ?>);

        // MODALIDAD DE COMPRA E INVERSION *************************************
        $('select[name="modalidad_compra_modificar_<?= $id_campania ?>"]').change(function(){
            $('#modalidad_valor_modificar_<?= $id_campania ?>').html($('select[name="modalidad_compra_modificar_<?= $id_campania ?>"]').find(':selected').text());

            calcular_inversion_neta();

            if($(this).find(':selected').val() == 'cpm'){
                $('#tipo_cantidad_modificar_<?= $id_campania ?>').html(' impresiones.');
            }else{
                $('#tipo_cantidad_modificar_<?= $id_campania ?>').html(' clicks.');
            }
        });

        $("#valor_unitario_modificar_<?= $id_campania ?>").keyup(function(){
            calcular_inversion_neta();
        });

        $("#cantidad_modificar_<?= $id_campania ?>").keydown(function(event){
            // Allow: backspace, delete, tab, escape, and enter
            if ( event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 13 ||
                // Allow: Ctrl+A
            (event.keyCode == 65 && event.ctrlKey === true) ||
                // Allow: home, end, left, right
            event.keyCode == 35 || event.keyCode == 36 ) {
                // let it happen, don't do anything
                event.preventDefault();
            } else {

                if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {

                    if(event.keyCode == 8 || event.keyCode == 46 || (event.keyCode >= 37 && event.keyCode <= 40)){
                    }else{
                        event.preventDefault();
                    }

                }
            }

            $("#cantidad_modificar_<?= $id_campania ?>").keyup();
        });

        $("#cantidad_modificar_<?= $id_campania ?>").keyup(function(event){
            calcular_inversion_neta();
        });

        $("#descuento_modificar_<?= $id_campania ?>").keyup(function(){
            calcular_inversion_neta();
        });

        $("#comision_modificar_<?= $id_campania ?>").keyup(function(){
            calcular_inversion_neta();
        });

        function calcular_inversion_neta(){

            var modalidad = '<?= strtolower($modalidad_de_compra) ?>';
            var valor = $.trim($("#valor_unitario_modificar_<?= $id_campania ?>").val().split(",").join("."));
            var cantidad = $.trim($("#cantidad_modificar_<?= $id_campania ?>").val());
            var inversion_neta = $.trim($("#inversion_neta_modificar_<?= $id_campania ?>").val());

            var inversion_bruta = $.trim($("#inversion_bruta_modificar_<?= $id_campania ?>").val());
            var descuento = $.trim($("#descuento_modificar_<?= $id_campania ?>").val());
            var comision = $.trim($("#comision_modificar_<?= $id_campania ?>").val());

            if(habilitar_descuentos == 0){
                if( modalidad == 'cpc' || modalidad == 'cpv' ){
                    inversion_neta = cantidad * valor;
                }else{
                    inversion_neta = (cantidad * valor) / 1000;
                }

                console.log('Descuentos habilitados.');
            }else{
                if( modalidad == 'cpc' || modalidad == 'cpv' || modalidad == 'cpa' ){
                    inversion_bruta = cantidad * valor;
                }else{
                    inversion_bruta = (cantidad * valor) / 1000;
                }

                $("#inversion_bruta_modificar_<?= $id_campania ?>").val( inversion_bruta.toFixed(3) );

                descuento = (inversion_bruta * descuento) / 100;

                inversion_neta = inversion_bruta - descuento;

                comision = (inversion_neta * comision) / 100;

                inversion_neta = inversion_neta - comision;

                console.log('Descuentos DEShabilitados.');
            }

            if(inversion_neta == 'NaN'){
                inversion_neta = 0;
            }

            //$("#inversion_neta_modificar_<?= $id_campania ?>").val((Math.round( inversion_neta * 100 ) / 100).toFixed(3));
            $("#inversion_neta_modificar_<?= $id_campania ?>").val((( inversion_neta * 100 ) / 100).toFixed(3));

<?php if ($type == 'PRICE_PRIORITY') { // Campania con Daily     ?>
                inversion_neta = $("#inversion_neta_modificar_<?= $id_campania ?>").val();

                var cant_dias = getNumeroDeNits( $.trim( $('#fecha_inicio_modificar_<?= $id_campania ?>').val()), $.trim( $('#fecha_fin_modificar_<?= $id_campania ?>').val() )) + 1;

                //var cuenta = ((inversion_neta * cant_dias).toFixed(3));
                var cuenta = (($("#inversion_neta_modificar_<?= $id_campania ?>").val() * cant_dias).toFixed(3));

                if(cuenta == 'NaN'){
                    $("#txt_inversion_total").val('0,000');
                }else{
                    $("#txt_inversion_total_modificar_<?= $id_campania ?>").val(cuenta);
                }
<?php } ?>

<?php if ($this->user_data->notacion == 1) { // Notacion espanola     ?>
                $("#inversion_neta_modificar_<?= $id_campania ?>").val($.trim($("#inversion_neta_modificar_<?= $id_campania ?>").val().split(".").join(",")));

    <?php if ($type == 'PRICE_PRIORITY') { ?>
                        $("#txt_inversion_total_modificar_<?= $id_campania ?>").val($.trim($("#txt_inversion_total_modificar_<?= $id_campania ?>").val().split(".").join(",")));
    <?php } ?>
<?php } ?>

            //$("#txt_inversion_total_modificar_<?= $id_campania ?>").val(((inversion_neta * cant_dias).toFixed(2)));
        }

        function strpos(cadena, busqueda){
            var i = (cadena.toLowerCase()).indexOf(busqueda.toLowerCase());
            return i === -1 ? false : true;
        }

        /*************************** SEGMENTACIONES **************************
        $("#cmb_segmentaciones_modificar_<?= $id_campania ?>").change(function(event){
            var id = $("#cmb_segmentaciones_modificar_<?= $id_campania ?>").find(':selected').val();
            $("#error_segmentacion_modificar_<?= $id_campania ?>").css("display", "none");

            if(id=="1"){
                $("#canales_tematicos_modificar_<?= $id_campania ?>").css("display", "none");
                $("#sitios_especificos_modificar_<?= $id_campania ?>").css("display", "none");
            }else if(id=="2"){
                $("#canales_tematicos_modificar_<?= $id_campania ?>").css("display", "block");
                $("#sitios_especificos_modificar_<?= $id_campania ?>").css("display", "none");
            }else if(id=="3"){
                $("#canales_tematicos_modificar_<?= $id_campania ?>").css("display", "none");
                $("#sitios_especificos_modificar_<?= $id_campania ?>").css("display", "block");
                if(mostrar_listado_sitios_modificar_<?= $id_campania ?> == false){
                    $("#loader_cmb_sitios_modificar_<?= $id_campania ?>").css("display", "inline");
                    $("#btn_pasar_sitio_del_text_modificar_<?= $id_campania ?>").attr("disabled", "disabled");
                    $("#txt_sitios_web_modificar_<?= $id_campania ?>").attr("disabled", "disabled");
                    $("#cmb_sitios_modificar_<?= $id_campania ?>").attr("disabled", "disabled");
                    $("#cmb_sitios_2_modificar_<?= $id_campania ?>").attr("disabled", "disabled");
                    $("#btn_borrar_sitio_modificar_<?= $id_campania ?>").attr("disabled", "disabled");
                    $("#btn_pasar_sitio_modificar_<?= $id_campania ?>").attr("disabled", "disabled");
                }
            }
        });*/

        /********************************* PAISES *********************************/
        // cargo los paises en el listado.
        var paises_modificar_<?= $id_campania ?> = '';
        var form_data_modificar_<?= $id_campania ?> = {};

        $.ajax({
            type: "GET",
            url: "/campania/get_paises_json/",
            data: form_data_modificar_<?= $id_campania ?>,
            dataType: "json",
            success: function(msg){
                paises_modificar_<?= $id_campania ?> = eval(msg);

                $("#txt_paises_modificar_<?= $id_campania ?>").keyup();
            }
        });

        $("#txt_paises_modificar_<?= $id_campania ?>").keyup( function(event){
            $('#cmb_paises_modificar_<?= $id_campania ?>').empty();

            var busqueda_modificar_<?= $id_campania ?> = $(this).val();

            if(busqueda_modificar_<?= $id_campania ?> != ''){
                for (var campo in paises_modificar_<?= $id_campania ?> ){
                    var agregar_modificar_<?= $id_campania ?> = true;
                    if(strpos( paises_modificar_<?= $id_campania ?>[campo].descripcion, busqueda_modificar_<?= $id_campania ?> ) != false){
                        if($('#cmb_paises_2_modificar_<?= $id_campania ?> option').length > 0){
                            $('#cmb_paises_2_modificar_<?= $id_campania ?> option').each(function(){
                                if( paises_modificar_<?= $id_campania ?>[campo].descripcion == $(this).text() ){
                                    agregar_modificar_<?= $id_campania ?> = false;
                                }
                            });

                            if(agregar_modificar_<?= $id_campania ?>){
                                $("#cmb_paises_modificar_<?= $id_campania ?>").append('<option value="'+paises_modificar_<?= $id_campania ?>[campo].id+'">'+paises_modificar_<?= $id_campania ?>[campo].descripcion+'</option>');
                            }
                        }else{
                            $("#cmb_paises_modificar_<?= $id_campania ?>").append('<option value="'+paises_modificar_<?= $id_campania ?>[campo].id+'">'+paises_modificar_<?= $id_campania ?>[campo].descripcion+'</option>');
                        }
                    }
                }
            }else{
                if($('#cmb_paises_2_modificar_<?= $id_campania ?> option').length > 0){
                    for (var campo in paises_modificar_<?= $id_campania ?> ){
                        if($('#cmb_paises_2_modificar_<?= $id_campania ?> option[value="'+paises_modificar_<?= $id_campania ?>[campo].id+'"]').length == 0){
                            $("#cmb_paises_modificar_<?= $id_campania ?>").append('<option value="'+paises_modificar_<?= $id_campania ?>[campo].id+'">'+paises_modificar_<?= $id_campania ?>[campo].descripcion+'</option>');
                        }
                    }
                }else{
                    for (var campo in paises_modificar_<?= $id_campania ?> ){
                        $("#cmb_paises_modificar_<?= $id_campania ?>").append('<option value="'+paises_modificar_<?= $id_campania ?>[campo].id+'">'+paises_modificar_<?= $id_campania ?>[campo].descripcion+'</option>');
                    }
                }
            }
        });

        $("#btn_pasar_pais_modificar_<?= $id_campania ?>").click( function (){
            $('#cmb_paises_modificar_<?= $id_campania ?> option:selected').appendTo("#cmb_paises_2_modificar_<?= $id_campania ?>");
        });

        $("#btn_borrar_pais_modificar_<?= $id_campania ?>").click( function (){
            $('#cmb_paises_2_modificar_<?= $id_campania ?> option:selected').appendTo("#cmb_paises_modificar_<?= $id_campania ?>");
        });

        $("#cmb_paises_modificar_<?= $id_campania ?>").dblclick( function (){
            $('#cmb_paises_modificar_<?= $id_campania ?> option:selected').appendTo("#cmb_paises_2_modificar_<?= $id_campania ?>");
        });

        $("#cmb_paises_2_modificar_<?= $id_campania ?>").dblclick( function (){
            $('#cmb_paises_2_modificar_<?= $id_campania ?> option:selected').appendTo("#cmb_paises_modificar_<?= $id_campania ?>");
        });


        // SUBIR ARCHIVOS ******************************************************
        $("#uploader_modificar_<?= $id_campania ?>").plupload({
            runtimes : 'html5,gears,browserplus,silverlight,flash,html4',
            url : '/campania/subir_archivos/modificar',
            max_file_size : '30mb',
            chunk_size : '30mb',
            unique_names : false,
            multipart: true,
            filters : [
                {title : "Imagen", extensions : "jpg,gif,png"},
                {title : "PDF", extensions : "pdf"},
                {title : "MP4", extensions : "mp4"},
                {title : "Zip", extensions : "zip"},
                {title : "SWF", extensions : "swf"},
                {title : "TXT", extensions : "txt"},
                {title : "DOC", extensions : "doc"},
                {title : "DOCx", extensions : "docx"},
                {title : "XLS", extensions : "xls"},
                {title : "XLSs", extensions : "xlsx"},
                {title : "RAR", extensions : "rar"}
            ],

            // Flash/Silverlight paths
            flash_swf_url: '/js/plupload.flash.swf',
            silverlight_xap_url: '/js/plupload.silverlight.xap',

            init: {
                StateChanged: function(up) {
                    if(up.state == plupload.STARTED){
                        var estado = 'STARTED';
                    }else{
                        var estado = 'STOPPED';
                    }

                    if(estado == 'STOPPED'){
                        $('.reveal-modal-bg').click();
                    }
                },

                FileUploaded: function(up, file, info) {
                    $('#tbl_materiales_modificar_<?= $id_campania ?>').load('/campania/mostrar_materiales/' + id_campana);

                    if(info.response != 'ok'){
                        $("#error_al_subir_modificar_<?= $id_campania ?>").html($("#error_al_subir_modificar_<?= $id_campania ?>").html() + info.response).css("display", "block");
                        archivo_subido = 0;
                    }else{
                        archivo_subido = 1;
                    }
                }
            }
        });
        
        
        $('#audiencia_guardar_seleccion').click(function(){
            modifico_audiencias = true;
        });

        // GUARDAR MODIFICACIONES **********************************************
        $('#guardar_cambios').click(function(){
            var error = false;
            $("#error_guardar_cambios").css("display", "none");
            $("#ok_guardar_cambios").css("display", "none");
            $("#loader_guardar_cambios").css("display", "inline");

            var unificar_campania = $('#cmb_campanias_modificar_<?= $id_campania ?> option:selected').val();

            // valido nombre de la campania
            var nombre_campania = $.trim($('#nombre_campania_modificar_<?= $id_campania ?>').val());
            if( nombre_campania.length <= 0 ){
                $("#error_guardar_cambios").html('Por favor indique un nombre para la campaña.').css("display", "inline");
                error = true;
            }

            //valido la fecha de inicio
            var fecha_inicio = $.trim( $('#fecha_inicio_modificar_<?= $id_campania ?>').val() );
            var fecha_fin = $.trim( $('#fecha_fin_modificar_<?= $id_campania ?>').val() );

            if( fecha_inicio == '' || fecha_fin == '' ){
                $("#error_guardar_cambios").html('Por favor ingrese la fecha de inicio y fin de la campaña.').css("display", "inline");
                error = true;
            }

            var segmentacion_modificar_<?= $id_campania ?> = 1;
            //var frecuencia_modificar_<?= $id_campania ?> = $('#cmb_frecuencia_modificar_<?= $id_campania ?>').find(':selected').val();

            var id_canales_tematicos_modificar_<?= $id_campania ?> = "";
            var id_sitios_modificar_<?= $id_campania ?> = "";
            // valido que haya seleccionado al menos un canal cuando la sergmentación sea '2'
            if(segmentacion_modificar_<?= $id_campania ?> == '2'){
                $("input[name='chk_canales_tematicos_modificar_<?= $id_campania ?>[]']:checked").each(function(){
                    id_canales_tematicos_modificar_<?= $id_campania ?> = id_canales_tematicos_modificar_<?= $id_campania ?> + $(this).val() + ";";
                });

                if(id_canales_tematicos_modificar_<?= $id_campania ?> == ''){
                    $("#error_segmentacion_modificar_<?= $id_campania ?>").html('Por favor seleccione al menos un canal.').css("display", "inline");
                    error = true;
                }
            }else if(segmentacion_modificar_<?= $id_campania ?> == "3"){
                $("#cmb_sitios_2_modificar_<?= $id_campania ?> option").each(function(){
                    id_sitios_modificar_<?= $id_campania ?> = id_sitios_modificar_<?= $id_campania ?> + $(this).attr('value') + ";";
                });

                if(id_sitios_modificar_<?= $id_campania ?> == ''){
                    $("#error_segmentacion_modificar_<?= $id_campania ?>").html('Por favor seleccione al menos un sitio.').css("display", "inline");
                    error = true;
                }
            }

            // valido los formatos seleccionados
            var formatos = "";
            $("#mostrar_formatos_modificar_<?= $id_campania ?> input[name='chk_formatos[]']:checked").each(function(){
                var id = $(this).val();
                var rel = $(this).attr('rel');

                var pagina_destino = $.trim($("#mostrar_formatos_modificar_<?= $id_campania ?> #pagina_destino_"+rel).val());

                if($("#mostrar_formatos_modificar_<?= $id_campania ?> input[name='chk_agregar_mediafem']").is(':checked') && pagina_destino != '') {
                    if(pagina_destino.search('utm_source=MediaFem') < 0){
                        if(pagina_destino.indexOf('?') != -1){
                            if(pagina_destino.indexOf('utm_source') == -1){
                                pagina_destino = pagina_destino + '&utm_source=MediaFem';
                            }
                        }else{
                            if(pagina_destino.indexOf('utm_source') == -1){
                                pagina_destino = pagina_destino + '?utm_source=MediaFem';
                            }
                        }
                    }
                }else{
                    /*
                    if(pagina_destino.search('?utm_source=MediaFem') >= 0){
                        pagina_destino.replace('?utm_source=MediaFem', '');
                    }

                    if(pagina_destino.search('&utm_source=MediaFem') >= 0){
                        pagina_destino.replace('&utm_source=MediaFem', '');
                    }
                     */
                }

                formatos = formatos + id + "|" + pagina_destino + ";";
            });

            if(formatos == '' && error == false){
                $("#mostrar_formatos_modificar_<?= $id_campania ?> #error_formato").html('Por favor seleccione al menos un tamaño.').css("display", "inline");
                error = true;
            }

            // valido los paises seleccionados
            var id_paises = "";
            $("#cmb_paises_2_modificar_<?= $id_campania ?> option").each(function(){
                id_paises = id_paises + $(this).attr('value') + ";";
            });

            if(id_paises == ''){
                $("#error_guardar_cambios").html('Por favor seleccione al menos un pais del listado.').css("display", "inline");
                error = true;
            }

            // valido cantidad
            var cantidad = $.trim( $('#cantidad_modificar_<?= $id_campania ?>').val() );

            if( cantidad == '' || cantidad <= 0 ){
                $("#error_guardar_cambios").html('Por favor ingrese una cantidad valida.').css("display", "inline");
                error = true;
            }

            // valido modalidad de compra
            var modalidad_compra = '<?= strtolower($modalidad_de_compra) ?>';

            // valido valor por unidad
            var valor_unidad = $.trim( $('#valor_unitario_modificar_<?= $id_campania ?>').val() );

            if( valor_unidad == '' || valor_unidad <= 0 ){
                $("#error_guardar_cambios").html('Por favor ingrese un valor unitario valido.').css("display", "inline");
                error = true;
            }

            if(valor_unidad < <?= $inversion_cpc_cpm ?>){
                $('#error_guardar_cambios').html('El valor por unidad no puede ser menor a <?= $this->api->notacion($inversion_cpc_cpm) ?>  <?= $this->user_data->moneda ?>.').css("display", "inline");
                error = true;
            }

            // valido valor por unidad
            var inversion_neta = $.trim( $('#inversion_neta_modificar_<?= $id_campania ?>').val() );

            if( inversion_neta == '' || inversion_neta <= 0 ){
                $("#error_guardar_cambios").html('Inversión neta invalida.').css("display", "inline");
                error = true;
            }

            if(inversion_neta < <?= $inversion_neta ?>){
                $("#error_guardar_cambios").html('La inversión diaria no puede ser menor a <?= $this->api->notacion($inversion_neta) ?>  <?= $this->user_data->moneda ?>.').css("display", "inline");
                error = true;
            }


            var descuento = $.trim( $('#descuento_modificar_<?= $id_campania ?>').val() );
            var comision = $.trim( $('#comision_modificar_<?= $id_campania ?>').val() );

            var maximo_descuentos = parseFloat(descuento) + parseFloat(comision);

            maximo_descuentos = parseFloat(maximo_descuentos);

            if(maximo_descuentos > 50){
                $("#error_guardar_cambios").html('La suma del descuento y comisión no puede superar el 50%.').css("display", "inline");
                error = true;
            }



            // controlo el tarifario
            var formatos_tarifario = "";
            $("input[name='chk_formatos[]']:checked").each(function(){
                var id = $(this).val();

                formatos_tarifario = formatos_tarifario + id + ";";
            });

            var form_data = {
                id_paises: id_paises,
                modalidad: modalidad_compra,
                formatos: formatos_tarifario,
                segmentacion: 1
            };

            $.ajax({
                type: "POST",
                url: "/campania/valor_minimo/",
                data: form_data,
                dataType: "json",
                success: function(msg){
                    valor_minimo = msg.valor_minimo;

                    var valor_unidad2 = $.trim( $('#valor_unitario_modificar_<?= $id_campania ?>').val() );

                    if(valor_unidad2 < valor_minimo){
                        $("#error_guardar_cambios").html('El valor por unidad no puede ser menor a ' + valor_minimo + ' <?= $this->user_data->moneda ?>.').css("display", "inline");
                        error = true;
                    }


                    if(error == true){
                        $(this).attr('disabled', false);

                        $("#loader_guardar_cambios").css("display", "none");

                        return false;
                    }

                    var form_data = {
                        id_campana: id_campana,
                        nombre: nombre_campania,
                        fecha_inicio: fecha_inicio,
                        fecha_fin: fecha_fin,
                        segmentacion: 1,
                        modifico_audiencias: modifico_audiencias,
                        frecuencia: 1,
                        id_sitios : id_sitios_modificar_<?= $id_campania ?>,
                        id_canales_tematicos: id_canales_tematicos_modificar_<?= $id_campania ?>,
                        id_paises: id_paises,
                        cantidad: cantidad,
                        inversion_neta: inversion_neta,
                        modalidad_compra: modalidad_compra,
                        valor_unidad: valor_unidad,
                        descuento: descuento,
                        comision: comision,
                        formatos: formatos,
                        unificar_campania: unificar_campania
                    };

                    //alert(id_campana + '/' + nombre_campania + '/' + fecha_inicio + '/' + fecha_fin + '/' + segmentacion_modificar_<?= $id_campania ?> + '/' + id_sitios_modificar_<?= $id_campania ?> + '/' + id_canales_tematicos_modificar_<?= $id_campania ?> + '/' + id_paises);
                    //alert(cantidad + '/' + inversion_neta + '/' + modalidad_compra + '/' + valor_unidad + '/' + descuento + '/' + comision);

                    $.ajax({
                        type: "POST",
                        url: "/campania/update/",
                        data: form_data,
                        dataType: "json",
                        success: function(msg){
                            $("#loader_guardar_cambios").css("display", "none");

                            if(msg.validate){
                                $("#ok_guardar_cambios").css("display", "inline").delay(2000).fadeOut(800);

                                window.location.replace("/campania?modificada_ok=true");
                                return false;
                            }else{
                                $("#error_guardar_cambios").html(msg.error);
                                $("#error_guardar_cambios").css("display", "inline");
                            }
                        }
                    });


                }
            });
            // fin de control de tarifario
        });

        calcular_inversion_neta();
        
        $('#seleccionar_audiencias_<?= $id_campania ?>').html(divLoader).load('/campania/mostrar_audiencias/' + <?= $id_campania ?>);

        //$("#cmb_segmentaciones_modificar_<?= $id_campania ?>").change();

        $("#valor_unitario_modificar_<?= $id_campania ?>").val((Math.round( $("#valor_unitario_modificar_<?= $id_campania ?>").val() * 100 ) / 100).toFixed(3));

<?php if ($this->user_data->notacion == 1) { // Notacion espanola     ?>
            $("#valor_unitario_modificar_<?= $id_campania ?>").val($("#valor_unitario_modificar_<?= $id_campania ?>").val().toString().replace('.', ','));
<?php } ?>
        //$("#txt_inversion_total_modificar_<?= $id_campania ?>").val($("#inversion_neta_modificar_<?= $id_campania ?>").val());

        calcular_inversion_neta();

        $('#modalidad_valor_modificar_<?= $id_campania ?>').html($('select[name="modalidad_compra_modificar_<?= $id_campania ?>"]').find(':selected').text());

        $('#uploader_start').click(function(){
            $("#error_al_subir").css("display", "none");
        });
    });
</script>

<h2 class="border_bottom" style="margin-top: 0;">Orden de compra</h2>

<?php if ($permitir_unificar_campanias && $campanias_padres) { ?>
    <div class="row">
        <label>Unificar con campa&ntilde;a:</label>
        <select name="cmb_campanias_modificar_<?= $id_campania ?>" id="cmb_campanias_modificar_<?= $id_campania ?>" style="width: 300px;">
            <option value="NINGUNA">---- Ninguna ----</option>
            <?php
            foreach ($campanias_padres as $row) {
                if ($row->id != $id_campania) {
                    $selected = '';
                    if ($la_campania_padre) {
                        if ($row->id == $la_campania_padre->id_campania_padre)
                            $selected = 'selected="selecteed"';
                    }
                    echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->nombre . '</option>';
                }
            }
            ?>
        </select>
    </div>
<?php } ?>

<div class="row">
    <label>Anunciante: </label>
    <span><?= $nombre_anunciante ?></span>
</div>

<div class="row">
    <label>Campa&ntilde;a: </label>
    <span>
        <input type="text" name="nombre_campania_modificar_<?= $id_campania ?>" maxlength="128" id="nombre_campania_modificar_<?= $id_campania ?>" value="<?= $nombre_campania ?>" />
    </span>
    <div style="font-style: italic; margin: 5px 0 0 165px; font-size:0.8em">
        Recomendamos que elija un nombre que sea f&aacute;cilmente de identificar.<br />
        Es una buena practica agregar el nombre del pa&iacute;s o regi&oacute;n al nombre de la campa&ntilde;a.
    </div>
</div>

<!-- FECHA DE INICIO Y DE FIN -->
<div class="row">
    <label>Fecha: </label>
    <input type="text" name="fecha_inicio" id="fecha_inicio_modificar_<?= $id_campania ?>" value="<?= $fecha_inicio ?>" style="width:135px !important;" /> al
    <input type="text" name="fecha_fin" id="fecha_fin_modificar_<?= $id_campania ?>" value="<?= $fecha_fin ?>" style="width:136px !important;" />
    <span class="msg_error" style="display:none;" id="error_fechas_modificar_<?= $id_campania ?>">Por favor ingrese la fecha de inicio y fin de la campa&ntilde;a.</span>
</div>

<!-- SEGMENTACION -->
<h2 class="border_bottom">Segmentaci&oacute;n</h2>
<div class="row">
    <div class="row border_bottom">
        <label>Audiencias: </label>
        <div id="seleccionar_audiencias_<?= $id_campania ?>" style="margin: 20px 55px; border-color: #eee;">
            
        </div>
    </div>

    <hr class="border_bottom" style="margin: 20px 55px; border-color: #eee;" />

    <div id="mostrar_formatos_modificar_<?= $id_campania ?>" style="margin-left:165px;margin-bottom:25px;margin-top:-5px"></div>

    <hr class="border_bottom" style="margin: 20px 55px; border-color: #eee;" />

    <label>Pa&iacute;s o Paises:</label>

    <table style="margin-left:165px !important; margin-top: -29px;">
        <tr>
            <td colspan="3" style="padding:5px 5px 0 !important">
                <input size="23" type="text" id="txt_paises_modificar_<?= $id_campania ?>" name="q" style="width:195px !important;" placeholder="Buscar" />
                <span class="msg_error" style="display:none;" id="error_paises_modificar_<?= $id_campania ?>">Por favor seleccione al menos un pais del listado.</span>
            </td>
        </tr>
        <tr>
            <td style="padding:5px !important">
                <select style="width: 201px;height:170px;" size="10" id="cmb_paises_modificar_<?= $id_campania ?>" name="cmb_paises" multiple="multiple">
                </select>
            </td>
            <td style="width: 10px">
                <table>
                    <tr>
                        <td style="padding:5px !important"><input type="button" value=">>" id="btn_pasar_pais_modificar_<?= $id_campania ?>" class="button_new" /></td>
                    </tr>
                    <tr>
                        <td style="padding:5px !important"><input type="button" value="<<" id="btn_borrar_pais_modificar_<?= $id_campania ?>" class="button_new" /></td>
                    </tr>
                </table>
            </td>
            <td style="padding:5px !important">
                <select style="width: 201px;height:170px;" size="10" id="cmb_paises_2_modificar_<?= $id_campania ?>" name="cmb_paises_2" multiple="multiple">
                    <?php
                    if ($paises_seleccionados) {
                        foreach ($paises_seleccionados as $pais_seleccionado)
                            echo '<option value="' . $pais_seleccionado->id_pais . '">' . $pais_seleccionado->descripcion . '</option>';
                    }
                    ?>
                </select>
            </td>
        </tr>
    </table>
</div>


<h2 class="border_bottom">Inversi&oacute;n</h2>

<!-- MODALIDAD DE COMPRA -->
<div class="row">
    <label>Modalidad de compra:</label>
    <?= strtoupper($modalidad_de_compra) ?>
</div>

<!-- VALOR UNITARIO -->
<div class="row">
    <label>Valor <span id="modalidad_valor_modificar_<?= $id_campania ?>"></span>:</label>
    <?= $this->user_data->moneda ?>
    <input type="text" name="valor_unitario" id="valor_unitario_modificar_<?= $id_campania ?>" value="<?= $monto ?>" style="width:100px !important;" />
</div>

<!-- CANTIDAD -->
<div class="row">
    <label>Cantidad diaria:</label>
    <input type="text" name="cantidad" id="cantidad_modificar_<?= $id_campania ?>" value="<?= $cantidad ?>" style="width:130px !important;" />
    <span id="tipo_cantidad_modificar_<?= $id_campania ?>"> <?php
    if ($modalidad_de_compra == 'cpm') {
        echo 'impresiones';
    } else if ($modalidad_de_compra == 'cpv') {
        echo 'vistas';
    } else if ($modalidad_de_compra == 'cpa') {
        echo 'acciones';
    } else {
        echo 'clicks';
    }
    ?>.</span>
</div>

<!-- INVERSION NETA -->
<div class="row">
    <label>Inversi&oacute;n diaria:</label>
    <?= $this->user_data->moneda ?>
    <input type="text" name="inversion_neta" id="inversion_neta_modificar_<?= $id_campania ?>" value="0" disabled="disabled" style="width:100px !important;" />
</div>

<?php if ($habilitar_descuentos) { ?>
    <!-- DESCUENTO -->
    <div class="row">
        <label>Descuento:</label>
        <input type="text" name="descuento" id="descuento_modificar_<?= $id_campania ?>" value="<?= $descuento ?>" size="3" style="width:113px!important;" /> %
    </div>

    <!-- COMISION -->
    <div class="row">
        <label>Comisi&oacute;n: </label>
        <input type="text" name="comision" id="comision_modificar_<?= $id_campania ?>" value="<?= $comision ?>" size="3" style="width:113px !important;" /> %
    </div>
<?php } ?>

<div class="row">
    <label>Inversi&oacute;n total:</label>
    <?= $this->user_data->moneda ?>
    <input type="text" name="txt_inversion_total_modificar_<?= $id_campania ?>" id="txt_inversion_total_modificar_<?= $id_campania ?>" value="0" disabled="disabled" style="width:100px !important;" />
</div>

<!-- MATERIALES -->
<h2 class="border_bottom" style="margin-bottom: 13px;">Anuncios</h2>

<div style="background-color: #EEEEEE; border-radius: 4px; margin-bottom: 10px; padding: 8px 5px;">
    <a href="#" data-reveal-id="subirMaterialesModal" class="button_new" style="background: url(../images/background_button.png) repeat-x scroll left top #DDD;border: 1px solid #C7C7C7;border-radius: 3px;color: #333333;cursor: pointer;padding: 3px 8px;">Agregar materiales</a>
</div>

<!-- MODAL AGREGAR MATERIALES -->
<div id="subirMaterialesModal" class="reveal-modal xlarge">
    <div class="row">
        Pulse <b>"Agregar archivos"</b>; seleccione archivos <b>.ZIP, .PNG, .JPG, .GIF, .SWF, .DOC, .DOCx, .TXT, .XLS o .XLSx </b> y luego pulse <b>"Subir los archivos seleccionados"</b>
    </div>

    <div id="error_al_subir_modificar_<?= $id_campania ?>" class="msg_error"></div>

    <div class="row">
        <div id="uploader_modificar_<?= $id_campania ?>"></div>
    </div>
</div>

<!-- TABLA MATERIALES -->
<div class="row" id="tbl_materiales_modificar_<?= $id_campania ?>"></div>

<hr />

<div class="row">
    <input type="button" class="button_new superButton" value="Guardar cambios" id="guardar_cambios" />
    <span class="msg_error" style="display:none;" id="error_guardar_cambios"></span>
    <span class="msg_error" style="display:none;color: green !important;" id="ok_guardar_cambios">Cambios guardados correctamente. espere por favor...</span>
    <img id="loader_guardar_cambios" style="display:none;" src="/images/ajax-loader.gif" height="10px" />
</div>