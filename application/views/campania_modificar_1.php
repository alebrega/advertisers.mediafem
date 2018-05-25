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

    #inversion_neta_modificar, #txt_inversion_total_modificar{
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

        var habilitar_descuentos = <?= $habilitar_descuentos ?>;

        $('#tbl_materiales_modificar').load('/campania/mostrar_materiales/' + id_campana);

        // SE USA PARA FILTRAR PAISES Y SITIOS
        function strpos(cadena, busqueda){
            var i = (cadena.toLowerCase()).indexOf(busqueda.toLowerCase());
            return i === -1 ? false : true;
        }

        // SITIOS ESPECIFICOS **************************************************
        var mostrar_listado_sitios_modificar = false;
        var cantidad_sitios_modificar = 0;
        var sitios_modificar = '';

        var form_data = {};

        $.ajax({
            type: "GET",
            url: "/campania/get_publishers_y_sitios_json/",
            data: form_data,
            dataType: "json",
            success: function(msg){
                if(msg){
                   sitios_modificar = eval(msg);
                   cantidad_sitios_modificar = sitios_modificar.length;

                   // recorro uno por uno los publisher y creo un <objgroup>
                   for(var a = 0; a < cantidad_sitios_modificar; a++){
                       if(sitios_modificar[a].url != ''){
                           if($('#cmb_sitios_2_modificar option[value="' + sitios_modificar[a].id_sitio + '"]').length == 0){
                               $("#cmb_sitios_modificar").append('<option value="' + sitios_modificar[a].id_sitio + '">' + sitios_modificar[a].url + '</option>');
                           }
                       }
                   }

                   $("#loader_cmb_sitios_modificar").css("display", "none");
                   $("#btn_pasar_sitio_del_text_modificar").attr("disabled", "");
                   $("#txt_sitios_web_modificar").attr("disabled", false);
                   $("#cmb_sitios_modificar").attr("disabled", false);
                   $("#cmb_sitios_2_modificar").attr("disabled", false);
                   $("#btn_borrar_sitio_modificar").attr("disabled", false);
                   $("#btn_pasar_sitio_modificar").attr("disabled", false);
                   mostrar_listado_sitios_modificar = true;
               }else{
                   alert(msg.error);
               }
            }
        });

        $("#txt_sitios_web_modificar").keyup( function(event){
            $('#cmb_sitios_modificar').html('');

            var busqueda_modificar = $(this).val();

            if(busqueda_modificar != ''){
                var cantidad_sitios_modificar = sitios_modificar.length;

                // recorro uno por uno los publisher y creo un <objgroup>
                for(var a=0; a < cantidad_sitios_modificar; a++){
                     if(strpos( sitios_modificar[a].url, busqueda_modificar ) != false)
                         $("#cmb_sitios_modificar").append('<option value="' + sitios_modificar[a].id_sitio + '">' + sitios_modificar[a].url + '</option>');
                }
            }else{
                var cantidad_sitios_modificar = sitios_modificar.length;

                for(var a = 0; a < cantidad_sitios_modificar; a++){
                   if($('#cmb_sitios_2_modificar option[value="' + sitios_modificar[a].id_sitio + '"]').length == 0)
                       $("#cmb_sitios_modificar").append('<option value="' + sitios_modificar[a].id_sitio + '">' + sitios_modificar[a].url + '</option>');
                }
            }

        });

        $("#cmb_sitios_modificar").dblclick( function (){
            $("#btn_pasar_sitio_modificar").click();
        });

        $("#cmb_sitios_2_modificar").dblclick( function (){
            $("#btn_borrar_sitio_modificar").click();
        });

        $("#btn_pasar_sitio_modificar").click( function (){
            $('#cmb_sitios_modificar option:selected').appendTo("#cmb_sitios_2_modificar");
        });

        $("#btn_borrar_sitio_modificar").click( function (){
            var seleccionados_modificar = $('#cmb_sitios_2_modificar option:selected');
            for(var a = 0; a < seleccionados_modificar.length; a++){
                $(seleccionados_modificar[a]).appendTo($("#cmb_sitios_modificar"));
            }
        });

        // FECHA DE INICIO Y FIN ***********************************************
        $.datepicker.setDefaults($.datepicker.regional['es']);
        $("#fecha_inicio_modificar").datepicker({ dateFormat:'dd-mm-yy' });
        $("#fecha_fin_modificar").datepicker({ dateFormat:'dd-mm-yy' });

        $("#fecha_inicio_modificar").change(function(){
            calcular_inversion_neta();
        });

        $("#fecha_fin_modificar").change(function(){
            calcular_inversion_neta();
        });

        // MODALIDAD DE COMPRA E INVERSION *************************************
        $('select[name="modalidad_compra_modificar"]').change(function(){
            $('#modalidad_valor_modificar').html($('select[name="modalidad_compra_modificar"]').find(':selected').text());

            calcular_inversion_neta();

            if($(this).find(':selected').val() == 'cpm'){
                $('#tipo_cantidad_modificar').html(' impresiones.');
            }else{
                $('#tipo_cantidad_modificar').html(' clicks.');
            }
        });

        $("#valor_unitario_modificar").keyup(function(){
            calcular_inversion_neta();
        });

        $("#cantidad_modificar").keyup(function(){
            calcular_inversion_neta();
        });

        $("#descuento_modificar").keyup(function(){
            calcular_inversion_neta();
        });

        $("#comision_modificar").keyup(function(){
            calcular_inversion_neta();
        });

        function calcular_inversion_neta(){
            var modalidad = $('select[name="modalidad_compra_modificar"]').find(':selected').val();
            var valor = $.trim($("#valor_unitario_modificar").val().split(",").join("."));
            var cantidad = $.trim($("#cantidad_modificar").val().split(",").join("."));
            var inversion_neta = $.trim($("#inversion_neta_modificar").val());

            var inversion_bruta = $.trim($("#inversion_bruta_modificar").val());
            var descuento = $.trim($("#descuento_modificar").val());
            var comision = $.trim($("#comision_modificar").val());

            if(habilitar_descuentos == 0){
                if( modalidad == 'cpc'){
                    inversion_neta = cantidad * valor;
                }else{
                    inversion_neta = (cantidad * valor) / 1000;
                }
            }else{
                if( modalidad == 'cpc'){
                    inversion_bruta = cantidad * valor;
                }else{
                    inversion_bruta = (cantidad * valor) / 1000;
                }

                $("#inversion_bruta_modificar").val( inversion_bruta );

                descuento = (inversion_bruta * descuento) / 100;

                inversion_neta = inversion_bruta - descuento;

                comision = (inversion_neta * comision) / 100;

                inversion_neta = inversion_neta - comision;
            }

            if(inversion_neta == 'NaN'){
                inversion_neta = 0;
            }

            $("#inversion_neta_modificar").val((Math.round( inversion_neta * 100 ) / 100).toFixed(2));

            var cant_dias = getNumeroDeNits( $.trim( $('#fecha_inicio_modificar').val()), $.trim( $('#fecha_fin_modificar').val() )) + 1;

            $("#txt_inversion_total_modificar").val(((inversion_neta * cant_dias).toFixed(2)));
        }

        function strpos(cadena, busqueda){
            var i = (cadena.toLowerCase()).indexOf(busqueda.toLowerCase());
            return i === -1 ? false : true;
        }

        /*************************** SEGMENTACIONES ***************************/
        $("#cmb_segmentaciones_modificar").change(function(event){
            var id = $("#cmb_segmentaciones_modificar").find(':selected').val();
            $("#error_segmentacion_modificar").css("display", "none");

            if(id=="1"){
                $("#canales_tematicos_modificar").css("display", "none");
                $("#sitios_especificos_modificar").css("display", "none");
            }else if(id=="2"){
                $("#canales_tematicos_modificar").css("display", "block");
                $("#sitios_especificos_modificar").css("display", "none");
            }else if(id=="3"){
                $("#canales_tematicos_modificar").css("display", "none");
                $("#sitios_especificos_modificar").css("display", "block");
                if(mostrar_listado_sitios_modificar == false){
                    $("#loader_cmb_sitios_modificar").css("display", "inline");
                    $("#btn_pasar_sitio_del_text_modificar").attr("disabled", "disabled");
                    $("#txt_sitios_web_modificar").attr("disabled", "disabled");
                    $("#cmb_sitios_modificar").attr("disabled", "disabled");
                    $("#cmb_sitios_2_modificar").attr("disabled", "disabled");
                    $("#btn_borrar_sitio_modificar").attr("disabled", "disabled");
                    $("#btn_pasar_sitio_modificar").attr("disabled", "disabled");
                }
            }
        });

        /********************************* PAISES *********************************/
        // cargo los paises en el listado.
        var paises_modificar = '';
        var form_data_modificar = {};

        $.ajax({
            type: "GET",
            url: "/campania/get_paises_json/",
            data: form_data_modificar,
            dataType: "json",
            success: function(msg){
                paises_modificar = eval(msg);

                $("#txt_paises_modificar").keyup();
            }
        });

        $("#txt_paises_modificar").keyup( function(event){
            $('#cmb_paises_modificar').empty();

            var busqueda_modificar = $(this).val();

            if(busqueda_modificar != ''){
                for (var campo in paises_modificar ){
                    var agregar_modificar = true;
                    if(strpos( paises_modificar[campo].descripcion, busqueda_modificar ) != false){
                        if($('#cmb_paises_2_modificar option').length > 0){
                            $('#cmb_paises_2_modificar option').each(function(){
                                if( paises_modificar[campo].descripcion == $(this).text() ){
                                    agregar_modificar = false;
                                }
                            });

                            if(agregar_modificar){
                                $("#cmb_paises_modificar").append('<option value="'+paises_modificar[campo].id+'">'+paises_modificar[campo].descripcion+'</option>');
                            }
                        }else{
                            $("#cmb_paises_modificar").append('<option value="'+paises_modificar[campo].id+'">'+paises_modificar[campo].descripcion+'</option>');
                        }
                    }
                }
            }else{
                if($('#cmb_paises_2_modificar option').length > 0){
                    for (var campo in paises_modificar ){
                        if($('#cmb_paises_2_modificar option[value="'+paises_modificar[campo].id+'"]').length == 0){
                            $("#cmb_paises_modificar").append('<option value="'+paises_modificar[campo].id+'">'+paises_modificar[campo].descripcion+'</option>');
                        }
                    }
                }else{
                    for (var campo in paises_modificar ){
                        $("#cmb_paises_modificar").append('<option value="'+paises_modificar[campo].id+'">'+paises_modificar[campo].descripcion+'</option>');
                    }
                }
            }
        });

        $("#btn_pasar_pais_modificar").click( function (){
            $('#cmb_paises_modificar option:selected').appendTo("#cmb_paises_2_modificar");
        });

        $("#btn_borrar_pais_modificar").click( function (){
            $('#cmb_paises_2_modificar option:selected').appendTo("#cmb_paises_modificar");
        });

        $("#cmb_paises_modificar").dblclick( function (){
            $('#cmb_paises_modificar option:selected').appendTo("#cmb_paises_2_modificar");
        });

        $("#cmb_paises_2_modificar").dblclick( function (){
            $('#cmb_paises_2_modificar option:selected').appendTo("#cmb_paises_modificar");
        });


        // SUBIR ARCHIVOS ******************************************************
        $("#uploader_modificar").plupload({
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
                    $('#tbl_materiales_modificar').load('/campania/mostrar_materiales/' + id_campana);

                    if(info.response != 'ok'){
                        $("#error_al_subir_modificar").html($("#error_al_subir_modificar").html() + info.response).css("display", "block");
                        archivo_subido = 0;
                    }else{
                        archivo_subido = 1;
                    }
                }
            }
        });

        // GUARDAR MODIFICACIONES **********************************************
        $('#guardar_cambios').click(function(){
            var error = false;
            $("#error_guardar_cambios").css("display", "none");
            $("#ok_guardar_cambios").css("display", "none");
            $("#loader_guardar_cambios").css("display", "inline");

            // valido nombre de la campania
            var nombre_campania = $.trim($('#nombre_campania_modificar').val());
            if( nombre_campania.length <= 0 ){
                $("#error_guardar_cambios").html('Por favor indique un nombre para la campaña.').css("display", "inline");
                error = true;
            }

            //valido la fecha de inicio
            var fecha_inicio = $.trim( $('#fecha_inicio_modificar').val() );
            var fecha_fin = $.trim( $('#fecha_fin_modificar').val() );

            if( fecha_inicio == '' || fecha_fin == '' ){
                $("#error_guardar_cambios").html('Por favor ingrese la fecha de inicio y fin de la campaña.').css("display", "inline");
                error = true;
            }

            var segmentacion_modificar = $('#cmb_segmentaciones_modificar').find(':selected').val();
            var id_canales_tematicos_modificar = "";
            var id_sitios_modificar = "";
            // valido que haya seleccionado al menos un canal cuando la sergmentación sea '2'
            if(segmentacion_modificar == '2'){
                $("input[name='chk_canales_tematicos_modificar[]']:checked").each(function(){
                    id_canales_tematicos_modificar = id_canales_tematicos_modificar + $(this).val() + ";";
                });

                if(id_canales_tematicos_modificar == ''){
                    $("#error_segmentacion_modificar").html('Por favor seleccione al menos un canal.').css("display", "inline");
                    error = true;
                }
            }else if(segmentacion_modificar == "3"){
                $("#cmb_sitios_2_modificar option").each(function(){
                    id_sitios_modificar = id_sitios_modificar + $(this).attr('value') + ";";
                });

                if(id_sitios_modificar == ''){
                    $("#error_segmentacion_modificar").html('Por favor seleccione al menos un sitio.').css("display", "inline");
                    error = true;
                }
            }

            // valido los paises seleccionados
            var id_paises = "";
            $("#cmb_paises_2_modificar option").each(function(){
                id_paises = id_paises + $(this).attr('value') + ";";
            });

            if(id_paises == ''){
                $("#error_guardar_cambios").html('Por favor seleccione al menos un pais del listado.').css("display", "inline");
                error = true;
            }

            // valido cantidad
            var cantidad = $.trim( $('#cantidad_modificar').val() );

            if( cantidad == '' || cantidad <= 0 ){
                $("#error_guardar_cambios").html('Por favor ingrese una cantidad valida.').css("display", "inline");
                error = true;
            }

            // valido modalidad de compra
            var modalidad_compra = $('select[name="modalidad_compra_modificar"]').find(':selected').val();

            // valido valor por unidad
            var valor_unidad = $.trim( $('#valor_unitario_modificar').val() );

            if( valor_unidad == '' || valor_unidad <= 0 ){
                $("#error_guardar_cambios").html('Por favor ingrese un valor unitario valido.').css("display", "inline");
                error = true;
            }

            if(valor_unidad < <?= $inversion_cpc_cpm ?>){
                $('#error_guardar_cambios').html('El valor por unidad no puede ser menor a <?= $this->api->notacion($inversion_cpc_cpm) ?>  <?= $this->user_data->moneda ?>.').css("display", "inline");
                return false;
            }

            // valido valor por unidad
            var inversion_neta = $.trim( $('#inversion_neta_modificar').val() );

            if( inversion_neta == '' || inversion_neta <= 0 ){
                $("#error_guardar_cambios").html('Inversión neta invalida.').css("display", "inline");
                error = true;
            }

            if(inversion_neta < <?= $inversion_neta ?>){
                $("#error_guardar_cambios").html('La inversión diaria no puede ser menor a <?= $this->api->notacion($inversion_neta) ?>  <?= $this->user_data->moneda ?>.').css("display", "inline");
                error = true;
            }


            var descuento = $.trim( $('#descuento_modificar').val() );
            var comision = $.trim( $('#comision_modificar').val() );

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
                segmentacion: segmentacion_modificar,
                id_sitios : id_sitios_modificar,
                id_canales_tematicos: id_canales_tematicos_modificar,
                id_paises: id_paises,
                cantidad: cantidad,
                inversion_neta: inversion_neta,
                modalidad_compra: modalidad_compra,
                valor_unidad: valor_unidad,
                descuento: descuento,
                comision: comision
            };

            //alert(id_campana + '/' + nombre_campania + '/' + fecha_inicio + '/' + fecha_fin + '/' + segmentacion_modificar + '/' + id_sitios_modificar + '/' + id_canales_tematicos_modificar + '/' + id_paises);
            //alert(cantidad + '/' + inversion_neta + '/' + modalidad_compra + '/' + valor_unidad + '/' + descuento + '/' + comision);

            $.ajax({
                type: "POST",
                url: "/campania/update/",
                data: form_data,
                dataType: "json",
                success: function(msg){
                    if(msg.validate){
                        $("#loader_guardar_cambios").css("display", "none");
                        $("#ok_guardar_cambios").css("display", "inline").delay(2000).fadeOut(800);

                        window.location.replace("/campania?modificada_ok=true");
                        return false;
                    }else{
                        $("#error_guardar_cambios").html(msg.error);
                        $("#error_guardar_cambios").css("display", "inline");
                        $("#loader_guardar_cambios").css("display", "none");
                    }
                }
            });
        });

        calcular_inversion_neta();

        $("#cmb_segmentaciones_modificar").change();

        $("#valor_unitario_modificar").val((Math.round( $("#valor_unitario_modificar").val() * 100 ) / 100).toFixed(2));

        //$("#txt_inversion_total_modificar").val($("#inversion_neta_modificar").val());

        calcular_inversion_neta();

        $('#modalidad_valor_modificar').html($('select[name="modalidad_compra_modificar"]').find(':selected').text());

        $('#uploader_start').click(function(){
            $("#error_al_subir").css("display", "none");
        });
    });
</script>

<h2 class="border_bottom" style="margin-top: 0;">Orden de compra</h2>

<div class="row">
    <label>Anunciante: </label>
    <span><?= $nombre_anunciante ?></span>
</div>

<div class="row">
    <label>Campa&ntilde;a: </label>
    <span>
        <input type="text" name="nombre_campania_modificar" maxlength="128" id="nombre_campania_modificar" value="<?= $nombre_campania ?>" />
    </span>
    <div style="font-style: italic; margin: 5px 0 0 165px; font-size:0.8em">
        Recomendamos que elija un nombre que sea f&aacute;cilmente de identificar.<br />
        Es una buena practica agregar el nombre del pa&iacute;s o regi&oacute;n al nombre de la campa&ntilde;a.
    </div>
</div>

<!-- FECHA DE INICIO Y DE FIN -->
<div class="row">
    <label>Fecha: </label>
    <input type="text" name="fecha_inicio" id="fecha_inicio_modificar" value="<?= $fecha_inicio ?>" style="width:135px !important;" /> al
    <input type="text" name="fecha_fin" id="fecha_fin_modificar" value="<?= $fecha_fin ?>" style="width:136px !important;" />
    <span class="msg_error" style="display:none;" id="error_fechas_modificar">Por favor ingrese la fecha de inicio y fin de la campa&ntilde;a.</span>
</div>

<!-- SEGMENTACION -->
<h2 class="border_bottom">Segmentaci&oacute;n</h2>
<div class="row">
    <div class="row border_bottom">
        <label>Segmentaci&oacute;n: </label>
        <select name="segmentacion_modificar" id="cmb_segmentaciones_modificar">
            <?php
            foreach ($segmentaciones as $row) {
                $selected = '';
                if($row->id == $segmentacion_id)
                    $selected = 'selected="selected"';
                echo '<option value="' . $row->id . '"' . $selected . '>' . $row->descripcion . '</option>';
            }
            ?>
        </select>
        <span id="loader_segmentaciones_modificar" style="display:none"><img src="/images/ajax-loader.gif" height="10px" /></span>
        <span id="loader_cmb_sitios_modificar" style="display:none"><img src="/images/ajax-loader.gif" height="10px" />
            Cargando sitios, por favor espere
        </span>
        <span class="msg_error" style="display:none;" id="error_segmentacion_modificar"></span>
    </div>

    <table id="canales_tematicos_modificar" style="display:none;margin-bottom:25px;margin-left:165px;">
        <?php
        $i = 0;
        foreach ($canales_tematicos as $categorie) {
            $selected = '';
            if($canales_seleccionados){
                if(in_array($categorie->id, $canales_seleccionados))
                        $selected = 'checked="checked"';
            }

            if ($i == 3) {
                ?>
                <tr>
                    <td><input type="checkbox" name="chk_canales_tematicos_modificar[]" value="<?= $categorie->id ?>" <?= $selected ?> />&nbsp;<?= $categorie->nombre ?></td>
                    <?php
                    $i = 1;
                } elseif ($i == 0 || $i == 1) {
                    ?>
                    <td><input type="checkbox" name="chk_canales_tematicos_modificar[]" value="<?= $categorie->id ?>" <?= $selected ?> />&nbsp;<?= $categorie->nombre ?></td>
                    <?php
                    $i++;
                } else {
                    ?>
                    <td><input type="checkbox" name="chk_canales_tematicos_modificar[]" value="<?= $categorie->id ?>" <?= $selected ?> />&nbsp;<?= $categorie->nombre ?></td>
                </tr>
                <?php
                $i++;
            }
        }
        ?>
    </table>

    <table cellpadding="0px" id="sitios_especificos_modificar" style="display:none;margin-bottom:25px;margin-left:165px;">
        <tr>
            <td colspan="3">
                Buscar: <input size="23" type="text" id="txt_sitios_web_modificar" name="q" disabled="disabled" style="width:143px !important; margin-bottom:5px;" />
                <span id="loader_comprobar_sitios_modificar" style="display:none"><img src="/images/ajax-loader.gif" /> Comprobando...</span>
            </td>
        </tr>
        <tr>
            <td style="width: 200px">
                <select style="width:201px;height:170px;" size="10" id="cmb_sitios_modificar" name="cmb_sitios_modificar" disabled="disabled" multiple="multiple">
                </select>
            </td>
            <td style="width: 10px; vertical-align: middle !important; padding:0 10px;">
                <table>
                    <tr>
                        <td><input type="button" value=">>" id="btn_pasar_sitio_modificar" disabled="disabled" class="button_new" /></td>
                    </tr>
                    <tr>
                        <td><input type="button" value="<<" id="btn_borrar_sitio_modificar" disabled="disabled" class="button_new" /></td>
                    </tr>
                </table>
            </td>
            <td>
                <select style="width:200px;height:170px;" size="10" id="cmb_sitios_2_modificar" name="cmb_sitios_2_modificar" disabled="disabled" multiple="multiple">
                    <?php
                        if($segmentacion_id == 3 && $sitios_seleccionados){
                            foreach ($sitios_seleccionados as $sitio)
                                echo '<option value="' . $sitio['id'] . '">' . $sitio['nombre'] . '</option>';
                        }
                    ?>
                </select>
            </td>
        </tr>
    </table>

    <hr class="border_bottom" style="margin: 20px 55px; border-color: #eee;" />

    <label>Pa&iacute;s o Paises:</label>

    <table style="margin-left:165px !important; margin-top: -29px;">
        <tr>
            <td colspan="3" style="padding:5px 5px 0 !important">
                <input size="23" type="text" id="txt_paises_modificar" name="q" style="width:195px !important;" placeholder="Buscar" />
                <span class="msg_error" style="display:none;" id="error_paises_modificar">Por favor seleccione al menos un pais del listado.</span>
            </td>
        </tr>
        <tr>
            <td style="padding:5px !important">
                <select style="width: 201px;height:170px;" size="10" id="cmb_paises_modificar" name="cmb_paises" multiple="multiple">
                </select>
            </td>
            <td style="width: 10px">
                <table>
                    <tr>
                        <td style="padding:5px !important"><input type="button" value=">>" id="btn_pasar_pais_modificar" class="button_new" /></td>
                    </tr>
                    <tr>
                        <td style="padding:5px !important"><input type="button" value="<<" id="btn_borrar_pais_modificar" class="button_new" /></td>
                    </tr>
                </table>
            </td>
            <td style="padding:5px !important">
                <select style="width: 201px;height:170px;" size="10" id="cmb_paises_2_modificar" name="cmb_paises_2" multiple="multiple">
                    <?php
                    if($paises_seleccionados){
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
    <select name="modalidad_compra_modificar" disabled="disabled" style="width:135px !important;">
        <option value="cpm" <?php if ($modalidad_de_compra == 'cpm') { echo 'selected="selected"'; } ?> >CPM</option>
        <option value="cpc" <?php if ($modalidad_de_compra == 'cpc') { echo 'selected="selected"'; } ?> >CPC</option>
    </select>
</div>

<!-- VALOR UNITARIO -->
<div class="row">
    <label>Valor <span id="modalidad_valor_modificar"></span>:</label>
    <?= $this->user_data->moneda ?>
    <input type="text" name="valor_unitario" id="valor_unitario_modificar" value="<?= $monto ?>" style="width:100px !important;" />
</div>

<!-- CANTIDAD -->
<div class="row">
    <label>Cantidad diaria:</label>
    <input type="text" name="cantidad" id="cantidad_modificar" value="<?= $cantidad ?>" style="width:130px !important;" />
    <span id="tipo_cantidad_modificar"> <?php if ($modalidad_de_compra == 'cpm') { echo 'impresiones'; } else { echo 'clicks'; } ?>.</span>
</div>

<!-- INVERSION NETA -->
<div class="row">
    <label>Inversi&oacute;n diaria:</label>
    <?= $this->user_data->moneda ?>
    <input type="text" name="inversion_neta" id="inversion_neta_modificar" value="0" disabled="disabled" style="width:100px !important;" />
</div>

<?php if($habilitar_descuentos){ ?>
    <!-- DESCUENTO -->
    <div class="row">
        <label>Descuento:</label>
        <input type="text" name="descuento" id="descuento_modificar" value="<?= $descuento ?>" size="3" style="width:113px!important;" /> %
    </div>

    <!-- COMISION -->
    <div class="row">
        <label>Comisi&oacute;n: </label>
        <input type="text" name="comision" id="comision_modificar" value="<?= $comision ?>" size="3" style="width:113px !important;" /> %
    </div>
<?php } ?>

<div class="row">
    <label>Inversi&oacute;n total:</label>
    <?= $this->user_data->moneda ?>
    <input type="text" name="txt_inversion_total_modificar" id="txt_inversion_total_modificar" value="0" disabled="disabled" style="width:100px !important;" />
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

    <div id="error_al_subir_modificar" class="msg_error"></div>

    <div class="row">
        <div id="uploader_modificar"></div>
    </div>
</div>

<!-- TABLA MATERIALES -->
<div class="row" id="tbl_materiales_modificar"></div>

<hr />

<div class="row">
    <input type="button" class="button_new superButton" value="Guardar cambios" id="guardar_cambios" />
    <span class="msg_error" style="display:none;" id="error_guardar_cambios"></span>
    <span class="msg_error" style="display:none;color: green !important;" id="ok_guardar_cambios">Cambios guardados correctamente. Aguarde por favor...</span>
    <img id="loader_guardar_cambios" style="display:none;" src="/images/ajax-loader.gif" height="10px" />
</div>