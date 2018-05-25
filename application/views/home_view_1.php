<style type="text/css">
    #extension{
        width: 222px;
        color: #333;
        border: 1px solid #C7C7C7;
        border-radius: 2px; /* El estándar.*/
    }

    input, select{
        width: auto !important;
    }

    .tabla_interior{
        border-spacing: 7px !important;
        vertical-align: middle !important;
    }
</style>

<script type="text/javascript">
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
        $.datepicker.setDefaults($.datepicker.regional['es']);
        $("#fecha_desde_<?= $id_order_dfp ?>").datepicker({ dateFormat:'dd-mm-yy'});
        $("#fecha_hasta_<?= $id_order_dfp ?>").datepicker({ dateFormat:'dd-mm-yy'});

        var now = new Date();
        var seconds = now.getSeconds()+'o'+now.getMinutes();

        $("#cmb_range_<?= $id_order_dfp ?>").change(function(){
            var range = $("#cmb_range_<?= $id_order_dfp ?>").find(':selected').val();

            if(range=='especific'){
                $("#div_fechas_especificas_<?= $id_order_dfp ?>").css("visibility", "visible");
            }else{
                $("#div_fechas_especificas_<?= $id_order_dfp ?>").css("visibility", "hidden");
            }
        });


        var id_adserver = $("#id_adserver_<?= $id_order_dfp ?>").val();
        var adv_id = $("#id_anunciante_adserver_<?= $id_order_dfp ?>").val();
        var por_sitio = $("#por_sitio_<?= $id_order_dfp ?>").val();
        var orden_id = $("#id_order_dfp_<?= $id_order_dfp ?>").val();
        var rango = $("#cmb_range_<?= $id_order_dfp ?>").find(':selected').val();

        var intervalo = "cumulative";

        $("input[name='chk_intervalo_<?= $id_order_dfp ?>[]']:checked").each(function(){
            intervalo = $(this).val();
        });

        var timezone = 'EST5EDT';

        var fecha_desde = 0;
        var fecha_hasta = 0;

        var columnas = "";
        var filtros_li = "";
        var filtros_cr = "";
        var filtros_sizes = "";
        var filtros_paises = "";
        var grupos = "";
        var orden = "";
        var direccion = "ASC";

        // TRAIGO LOS LINEITEMS *******************************************
        $("#loader_filtros_li_<?= $id_order_dfp ?>").css("display", "inline");
        $("#cmb_line_items_<?= $id_order_dfp ?>").load('/welcome/get_filtros_line_items/'+id_adserver+'/'+adv_id+'/'+seconds+'/'+orden_id, function(){
            $("#loader_filtros_li_<?= $id_order_dfp ?>").css("display", "none");
            $("#cmb_line_items_agregados_<?= $id_order_dfp ?>").html('');
        });

        // TRAIGO LAS CREATIVIDADES ***************************************
        $("#loader_filtros_cr_<?= $id_order_dfp ?>").css("display", "inline");
        $("#cmb_creatives_<?= $id_order_dfp ?>").load('/welcome/get_filtros_creatives/'+id_adserver+'/'+adv_id+'/'+seconds, function(){
            $("#loader_filtros_cr_<?= $id_order_dfp ?>").css("display", "none");
            $("#cmb_creatives_agregados_<?= $id_order_dfp ?>").html('');
        });

        // MOSTRAR ENVIAR REPORTE POR CORREO ******************************
        $('#enviar_correo_<?= $id_order_dfp ?>').click( function(){
            $(this).fadeOut(function(){
                $('#enviar_por_correo_<?= $id_order_dfp ?>').fadeIn();
            });
        });

        // ENVIAR REPORTE POR CORREO **************************************
        $('#enviar_reporte_<?= $id_order_dfp ?>').click( function(){
            var correo_electronico = $.trim($('#correo_electronico_<?= $id_order_dfp ?>').val());

            var extension = $("#extension_<?= $id_order_dfp ?>").find(':selected').val();

            if(id_adserver == '0'){
                rango = $("#cmb_range_<?= $id_order_dfp ?>").find(':selected').val();

                var intervalo = "cumulative";

                $("input[name='chk_intervalo_<?= $id_order_dfp ?>[]']:checked").each(function(){
                    intervalo = $(this).val();
                });

                if(rango=="especific"){
                    fecha_desde = $("#fecha_desde_<?= $id_order_dfp ?>").val();
                    fecha_hasta = $("#fecha_hasta_<?= $id_order_dfp ?>").val();

                    if(fecha_desde=="" || fecha_hasta==""){
                        $("#loader_report_<?= $id_order_dfp ?>").css("display", "none");
                        alert("Debe completar las 2 fechas");
                        return;
                    }
                }else{
                    fecha_desde = 0;
                    fecha_hasta = 0;
                }

                columnas = "";

                $("input[name='chk_columnas_<?= $id_order_dfp ?>[]']:checked").each(function(){
                    columnas = columnas + $(this).val() + ";";
                });

                if(columnas==""){
                    $("#loader_report_<?= $id_order_dfp ?>").css("display", "none");
                    alert("Debe elegir al menos una columna");
                    return;
                }

                filtros_li = "";

                $("#cmb_line_items_agregados_<?= $id_order_dfp ?> option").each(function(){
                    filtros_li = filtros_li + $(this).attr('value') + ";";
                });

                filtros_cr = "";

                $("#cmb_creatives_agregados_<?= $id_order_dfp ?> option").each(function(){
                    filtros_cr = filtros_cr + $(this).attr('value') + ";";
                });

                filtros_sizes = "";

                $("#cmb_sizes_agregados_<?= $id_order_dfp ?> option").each(function(){
                    filtros_sizes = filtros_sizes + $(this).attr('value') + ";";
                });

                filtros_paises = "";

                $("#cmb_paises_agregados_<?= $id_order_dfp ?> option").each(function(){
                    filtros_paises = filtros_paises + $(this).attr('value') + ";";
                });

                grupos = "";

                $("input[name='chk_grupos_<?= $id_order_dfp ?>[]']:checked").each(function(){
                    grupos = grupos + $(this).val() + ";";
                });

                orden = "";

                $("input[name='chk_columnas_<?= $id_order_dfp ?>[]']:checked").each(function(){
                    orden = $(this).val();
                    return false;
                });

                $("input[name='chk_grupos_<?= $id_order_dfp ?>[]']:checked").each(function(){
                    orden = $(this).val();
                    return false;
                });

                if(filtros_li=="") filtros_li = 0;
                if(filtros_cr=="") filtros_cr = 0;
                if(filtros_sizes=="") filtros_sizes = 0;
                if(filtros_paises=="") filtros_paises = 0;
                if(grupos=="") grupos = 0;
                if(fecha_desde=="") fecha_desde = 0;
                if(fecha_hasta=="") fecha_hasta = 0;

            }else if(id_adserver == '1'){

                rango = $("#cmb_range_<?= $id_order_dfp ?>").find(':selected').val();

                var intervalo = "cumulative";

                $("input[name='chk_intervalo_<?= $id_order_dfp ?>[]']:checked").each(function(){
                    intervalo = $(this).val();
                });

                if(rango=="especific"){
                    fecha_desde = $("#fecha_desde_<?= $id_order_dfp ?>").val();
                    fecha_hasta = $("#fecha_hasta_<?= $id_order_dfp ?>").val();

                    if(fecha_desde=="" || fecha_hasta==""){
                        $("#loader_report_<?= $id_order_dfp ?>").css("display", "none");
                        alert("Debe completar las 2 fechas");
                        return;
                    }
                }else{
                    fecha_desde = 0;
                    fecha_hasta = 0;
                }

                columnas = "";

                $("input[name='chk_columnas_<?= $id_order_dfp ?>[]']:checked").each(function(){
                    columnas = columnas + $(this).val() + ";";
                });

                if(columnas==""){
                    $("#loader_report_<?= $id_order_dfp ?>").css("display", "none");
                    alert("Debe elegir al menos una columna");
                    return;
                }

                filtros_li = "";

                $("#cmb_line_items_agregados_<?= $id_order_dfp ?> option").each(function(){
                    filtros_li = filtros_li + $(this).attr('value') + ";";
                });

                filtros_cr = "";

                $("#cmb_creatives_agregados_<?= $id_order_dfp ?> option").each(function(){
                    filtros_cr = filtros_cr + $(this).attr('value') + ";";
                });

                filtros_sizes = "";

                $("#cmb_sizes_agregados_<?= $id_order_dfp ?> option").each(function(){
                    filtros_sizes = filtros_sizes + $(this).attr('value') + ";";
                });

                filtros_paises = "";

                $("#cmb_paises_agregados_<?= $id_order_dfp ?> option").each(function(){
                    filtros_paises = filtros_paises + $(this).attr('value') + ";";
                });

                grupos = "";

                $("input[name='chk_grupos_<?= $id_order_dfp ?>[]']:checked").each(function(){
                    grupos = grupos + $(this).val() + ";";
                });

                orden = "";

                $("input[name='chk_columnas_<?= $id_order_dfp ?>[]']:checked").each(function(){
                    orden = $(this).val();
                    return false;
                });

                $("input[name='chk_grupos_<?= $id_order_dfp ?>[]']:checked").each(function(){
                    orden = $(this).val();
                    return false;
                });

                if(filtros_li=="") filtros_li = 0;
                if(filtros_cr=="") filtros_cr = 0;
                if(filtros_sizes=="") filtros_sizes = 0;
                if(filtros_paises=="") filtros_paises = 0;
                if(grupos=="") grupos = 0;
                if(fecha_desde=="") fecha_desde = 0;
                if(fecha_hasta=="") fecha_hasta = 0;
            }

            var form_data = {
                correo_electronico : correo_electronico,
                extension : extension,
                enviar_cada : 'unica_vez',
                dia_de_la_semana : '',
                id_adserver: id_adserver,
                id_anunciante: adv_id,
                id_orden: orden_id,
                por_sitio: por_sitio,
                rango: intervalo,
                filtro_li: filtros_li,
                filtro_cr: filtros_cr,
                filtro_formatos: filtros_sizes,
                filtro_paises : filtros_paises,
                grupos: grupos,
                columnas: columnas,
                intervalo : rango,
                fecha_desde : fecha_desde,
                fecha_hasta : fecha_hasta,
                seconds : seconds
            };

            $.ajax({
                type: "POST",
                url: "/welcome/suscribir_reporte/",
                data: form_data,
                dataType: "json",
                success: function(msg){
                    if(msg.validate == true)
                        window.location.replace("/campania?suscripcion=ok");
                }
            });
        });

        // CANCELAR REPORTE POR CORREO ************************************
        $('#cancelar_reporte_' + <?= $id_order_dfp ?>).click( function(){
            $('#enviar_por_correo_' + <?= $id_order_dfp ?>).fadeOut(function(){
                $('#enviar_correo_' + <?= $id_order_dfp ?>).fadeIn();
            });
        });


        $("#btn_ejecutar_reporte_" + <?= $id_order_dfp ?>).click(function(){
            por_sitio = $("#por_sitio_" + <?= $id_order_dfp ?>).val();

            if(id_adserver == '0'){
                cargando(1);

                rango = $("#cmb_range_" + <?= $id_order_dfp ?>).find(':selected').val();
                timezone = 'EST5EDT';

                if(rango=="especific"){
                    fecha_desde = $("#fecha_desde_" + <?= $id_order_dfp ?>).val();
                    fecha_hasta = $("#fecha_hasta_" + <?= $id_order_dfp ?>).val();

                    if(fecha_desde=="" || fecha_hasta==""){
                        $("#loader_report_" + <?= $id_order_dfp ?>).css("display", "none");
                        alert("Debe completar las 2 fechas");
                        return;
                    }
                }else{
                    fecha_desde = 0;
                    fecha_hasta = 0;
                }

                var intervalo = "cumulative";

                $("input[name='chk_intervalo_" + <?= $id_order_dfp ?> + "[]']:checked").each(function(){
                    intervalo = $(this).val();
                });

                var columnas = "";

                $("input[name='chk_columnas_" + <?= $id_order_dfp ?> + "[]']:checked").each(function(){
                    columnas = columnas + $(this).val() + ";";
                });

                if(columnas==""){
                    $("#loader_report_" + <?= $id_order_dfp ?>).css("display", "none");
                    alert("Debe elegir al menos una columna");
                    return;
                }

                var filtros_li = "";

                $("#cmb_line_items_agregados_" + <?= $id_order_dfp ?> + " option").each(function(){
                    filtros_li = filtros_li + $(this).attr('value') + ";";
                });

                var filtros_cr = "";

                $("#cmb_creatives_agregados_" + <?= $id_order_dfp ?> + " option").each(function(){
                    filtros_cr = filtros_cr + $(this).attr('value') + ";";
                });

                var filtros_sizes = "";

                $("#cmb_sizes_agregados_" + <?= $id_order_dfp ?> + " option").each(function(){
                    filtros_sizes = filtros_sizes + $(this).attr('value') + ";";
                });

                var filtros_paises = "";

                $("#cmb_paises_agregados_" + <?= $id_order_dfp ?> + " option").each(function(){
                    filtros_paises = filtros_paises + $(this).attr('value') + ";";
                });

                var grupos = "";

                $("input[name='chk_grupos_" + <?= $id_order_dfp ?> + "[]']:checked").each(function(){
                    grupos = grupos + $(this).val() + ";";
                });

                var orden = "";

                $("input[name='chk_columnas_" + <?= $id_order_dfp ?> + "[]']:checked").each(function(){
                    orden = $(this).val();
                    return false;
                });

                $("input[name='chk_grupos_" + <?= $id_order_dfp ?> + "[]']:checked").each(function(){
                    orden = $(this).val();
                    return false;
                });

                rango = fixedEncodeURIComponent(rango);
                filtros_li = fixedEncodeURIComponent(filtros_li);
                filtros_cr = fixedEncodeURIComponent(filtros_cr);
                filtros_sizes = fixedEncodeURIComponent(filtros_sizes);
                filtros_paises = fixedEncodeURIComponent(filtros_paises);
                grupos = fixedEncodeURIComponent(grupos);
                columnas = fixedEncodeURIComponent(columnas);

                if(filtros_li=="") filtros_li = 0;
                if(filtros_cr=="") filtros_cr = 0;
                if(filtros_sizes=="") filtros_sizes = 0;
                if(filtros_paises=="") filtros_paises = 0;
                if(grupos=="") grupos = 0;
                if(fecha_desde=="") fecha_desde = 0;
                if(fecha_hasta=="") fecha_hasta = 0;


                //alert('/welcome/obtener_reporte_dinamico_por_sitio_DFP/'+orden_id+'/'+intervalo+'/'+rango+'/'+fecha_desde+'/'+fecha_hasta+'/'+columnas+'/'+grupos+'/'+filtros_li+'/'+filtros_cr+'/'+filtros_paises+'/'+seconds);

                var timer = setTimeout("$('#esperando_<?= $id_order_dfp ?>').show();", 10000);

                $("#btn_ejecutar_reporte_" + <?= $id_order_dfp ?>).attr('disabled', true);

                $("#tbl_reporte_" + <?= $id_order_dfp ?>).load('/welcome/obtener_reporte_dinamico_por_sitio_DFP/'+orden_id+'/'+intervalo+'/'+rango+'/'+fecha_desde+'/'+fecha_hasta+'/'+columnas+'/'+grupos+'/'+filtros_li+'/'+filtros_cr+'/'+filtros_paises+'/'+seconds, function(){
                    clearTimeout(timer);
                    $("#loader_report_" + <?= $id_order_dfp ?>).css("display", "none");
                    $("#btn_ejecutar_reporte_" + <?= $id_order_dfp ?>).attr('disabled', false);

                    cargando(0);
                });

            }else if(id_adserver == '1'){
                cargando(1);

                rango = $("#cmb_range_" + <?= $id_order_dfp ?>).find(':selected').val();
                timezone = 'EST5EDT';

                if(rango=="especific"){
                    fecha_desde = $("#fecha_desde_" + <?= $id_order_dfp ?>).val();
                    fecha_hasta = $("#fecha_hasta_" + <?= $id_order_dfp ?>).val();

                    if(fecha_desde=="" || fecha_hasta==""){
                        $("#loader_report_" + <?= $id_order_dfp ?>).css("display", "none");
                        alert("Debe completar las 2 fechas");
                        return;
                    }
                }else{
                    fecha_desde = 0;
                    fecha_hasta = 0;
                }

                var intervalo = "cumulative";

                $("input[name='chk_intervalo_" + <?= $id_order_dfp ?> + "[]']:checked").each(function(){
                    intervalo = $(this).val();
                });

                columnas = "";

                $("input[name='chk_columnas_" + <?= $id_order_dfp ?> + "[]']:checked").each(function(){
                    columnas = columnas + $(this).val() + ";";
                });

                if(columnas==""){
                    $("#loader_report_" + <?= $id_order_dfp ?>).css("display", "none");
                    alert("Debe elegir al menos una columna");
                    return;
                }

                filtros_li = "";

                $("#cmb_line_items_agregados_" + <?= $id_order_dfp ?> + " option").each(function(){
                    filtros_li = filtros_li + $(this).attr('value') + ";";
                });

                filtros_cr = "";

                $("#cmb_creatives_agregados_" + <?= $id_order_dfp ?> + "  option").each(function(){
                    filtros_cr = filtros_cr + $(this).attr('value') + ";";
                });

                filtros_sizes = "";

                $("#cmb_sizes_agregados_" + <?= $id_order_dfp ?> + "  option").each(function(){
                    filtros_sizes = filtros_sizes + $(this).attr('value') + ";";
                });

                filtros_paises = "";

                $("#cmb_paises_agregados_" + <?= $id_order_dfp ?> + "  option").each(function(){
                    filtros_paises = filtros_paises + $(this).attr('value') + ";";
                });

                grupos = "";

                $("input[name='chk_grupos_" + <?= $id_order_dfp ?> + "[]']:checked").each(function(){
                    grupos = grupos + $(this).val() + ";";
                });

                orden = "";

                $("input[name='chk_columnas_" + <?= $id_order_dfp ?> + "[]']:checked").each(function(){
                    orden = $(this).val();
                    return false;
                });

                $("input[name='chk_grupos_" + <?= $id_order_dfp ?> + "[]']:checked").each(function(){
                    orden = $(this).val();
                    return false;
                });

                var direccion = "ASC";

                rango = fixedEncodeURIComponent(rango);
                filtros_li = fixedEncodeURIComponent(filtros_li);
                filtros_cr = fixedEncodeURIComponent(filtros_cr);
                filtros_sizes = fixedEncodeURIComponent(filtros_sizes);
                filtros_paises = fixedEncodeURIComponent(filtros_paises);
                grupos = fixedEncodeURIComponent(grupos);
                columnas = fixedEncodeURIComponent(columnas);

                if(filtros_li=="") filtros_li = 0;
                if(filtros_cr=="") filtros_cr = 0;
                if(filtros_sizes=="") filtros_sizes = 0;
                if(filtros_paises=="") filtros_paises = 0;
                if(grupos=="") grupos = 0;
                if(fecha_desde=="") fecha_desde = 0;
                if(fecha_hasta=="") fecha_hasta = 0;

                var timer = setTimeout("$('#esperando_<?= $id_order_dfp ?>').show();", 10000);

                $("#btn_ejecutar_reporte_" + <?= $id_order_dfp ?>).attr('disabled', true);

                //alert('/welcome/obtener_reporte_dinamico_por_sitio/'+adv_id+'/'+rango+'/'+filtros_li+'/'+grupos+'/'+columnas+'/'+fecha_desde+'/'+fecha_hasta+'/'+filtros_paises+'/'+filtros_cr+'/'+filtros_sizes+'/'+intervalo+'/'+timezone+'/'+orden+'/'+direccion+'/'+seconds);

                if(por_sitio == "1"){
                    $("#tbl_reporte_" + <?= $id_order_dfp ?>).load('/welcome/obtener_reporte_dinamico_por_sitio/'+adv_id+'/'+rango+'/'+filtros_li+'/'+grupos+'/'+columnas+'/'+fecha_desde+'/'+fecha_hasta+'/'+filtros_paises+'/'+filtros_cr+'/'+filtros_sizes+'/'+intervalo+'/'+timezone+'/'+orden+'/'+direccion+'/'+seconds, function(){
                        clearTimeout(timer);
                        $("#loader_report_" + <?= $id_order_dfp ?> ).css("display", "none");
                        $("#btn_ejecutar_reporte_" + <?= $id_order_dfp ?>).attr('disabled', false);

                        cargando(0);
                    });
                }else{
                    $("#tbl_reporte").load('/welcome/obtener_reporte_dinamico/'+adv_id+'/'+rango+'/'+filtros_li+'/'+grupos+'/'+columnas+'/'+fecha_desde+'/'+fecha_hasta+'/'+filtros_paises+'/'+filtros_cr+'/'+filtros_sizes+'/'+intervalo+'/'+timezone+'/'+orden+'/'+direccion+'/'+seconds, function(){
                        clearTimeout(timer);
                        $("#loader_report_" + <?= $id_order_dfp ?> ).css("display", "none");
                        $("#btn_ejecutar_reporte_" + <?= $id_order_dfp ?>).attr('disabled', false);

                        cargando(0);
                    });
                }
            }
        });

        $('.chk_grupos_<?= $id_order_dfp ?>').click(function(){
            if(id_adserver == '2'){
                if ($(this).attr('checked')) {
                    if($(this).attr('id')=="chk_pais_<?= $id_order_dfp ?>"){
                        $("#chk_creatividad_<?= $id_order_dfp ?>").attr('checked', false);
                    }else if($(this).attr('id')=="chk_creatividad_<?= $id_order_dfp ?>"){
                        $("#chk_pais_<?= $id_order_dfp ?>").attr('checked', false);
                    }
                }
            }
        });

        $("#btn_pasar_line_item_<?= $id_order_dfp ?>").click( function (){
            $('#cmb_line_items_<?= $id_order_dfp ?> option:selected').appendTo("#cmb_line_items_agregados_<?= $id_order_dfp ?>");

            if($("#texto_filtros_line_items_<?= $id_order_dfp ?>").val()=="Todos"){
                $("#texto_filtros_line_items_<?= $id_order_dfp ?>").val("");
            }

            var label = $("#texto_filtros_line_items_<?= $id_order_dfp ?>").val();
            $("#cmb_line_items_agregados_<?= $id_order_dfp ?> option").each(function(){
                $("#texto_filtros_line_items_<?= $id_order_dfp ?>").val(label + $(this).text() + ",");
            });

            if($("#texto_filtros_line_items_<?= $id_order_dfp ?>").val().length > 25){
                $("#td_line_item_<?= $id_order_dfp ?>").text( $("#texto_filtros_line_items_<?= $id_order_dfp ?>").val().substr(0, 25)+"..." );
            }else{
                $("#td_line_item_<?= $id_order_dfp ?>").text( $("#texto_filtros_line_items_<?= $id_order_dfp ?>").val().substr(0, $("#texto_filtros_line_items_<?= $id_order_dfp ?>").val().length-1));
            }
        });

        $("#btn_borrar_line_item_<?= $id_order_dfp ?>").click( function (){
            var texto = $('#cmb_line_items_agregados_<?= $id_order_dfp ?> option:selected').text()+",";
            var label = $("#texto_filtros_line_items_<?= $id_order_dfp ?>").val();

            var nuevo_texto = label.replace(texto, "");

            $("#texto_filtros_line_items_<?= $id_order_dfp ?>").val(nuevo_texto);

            if($("#texto_filtros_line_items_<?= $id_order_dfp ?>").val()==""){
                $("#texto_filtros_line_items_<?= $id_order_dfp ?>").val("Todos");
                $("#td_line_item_<?= $id_order_dfp ?>").text($("#texto_filtros_line_items_<?= $id_order_dfp ?>").val());
            }else{
                if($("#texto_filtros_line_items_<?= $id_order_dfp ?>").val().length > 25){
                    $("#td_line_item_<?= $id_order_dfp ?>").text( $("#texto_filtros_line_items_<?= $id_order_dfp ?>").val().substr(0, 25)+"..." );
                }else{
                    $("#td_line_item_<?= $id_order_dfp ?>").text( $("#texto_filtros_line_items_<?= $id_order_dfp ?>").val().substr(0, $("#texto_filtros_line_items_<?= $id_order_dfp ?>").val().length-1));
                }
            }
            $('#cmb_line_items_agregados_<?= $id_order_dfp ?> option:selected').appendTo("#cmb_line_items_<?= $id_order_dfp ?>");

        });

        $("#btn_pasar_creative_<?= $id_order_dfp ?>").click( function (){
            $('#cmb_creatives_<?= $id_order_dfp ?> option:selected').appendTo("#cmb_creatives_agregados_<?= $id_order_dfp ?>");

            if($("#texto_filtros_creatives_<?= $id_order_dfp ?>").val()=="Todas"){
                $("#texto_filtros_creatives_<?= $id_order_dfp ?>").val("");
            }

            var label = $("#texto_filtros_creatives_<?= $id_order_dfp ?>").val();
            $("#cmb_creatives_agregados_<?= $id_order_dfp ?> option").each(function(){
                $("#texto_filtros_creatives_<?= $id_order_dfp ?>").val(label + $(this).text() + ",");
            });

            if($("#texto_filtros_creatives_<?= $id_order_dfp ?>").val().length > 25){
                $("#td_creative_<?= $id_order_dfp ?>").text( $("#texto_filtros_creatives_<?= $id_order_dfp ?>").val().substr(0, 25)+"..." );
            }else{
                $("#td_creative_<?= $id_order_dfp ?>").text( $("#texto_filtros_creatives_<?= $id_order_dfp ?>").val().substr(0, $("#texto_filtros_creatives_<?= $id_order_dfp ?>").val().length-1));
            }
        });

        $("#btn_borrar_creative_<?= $id_order_dfp ?>").click( function (){

            var texto = $('#cmb_creatives_agregados_<?= $id_order_dfp ?> option:selected').text()+",";
            var label = $("#texto_filtros_creatives_<?= $id_order_dfp ?>").val();

            var nuevo_texto = label.replace(texto, "");

            $("#texto_filtros_creatives_<?= $id_order_dfp ?>").val(nuevo_texto);

            if($("#texto_filtros_creatives_<?= $id_order_dfp ?>").val()==""){
                $("#texto_filtros_creatives_<?= $id_order_dfp ?>").val("Todas");
                $("#td_creative_<?= $id_order_dfp ?>").text($("#texto_filtros_creatives_<?= $id_order_dfp ?>").val());
            }else{
                if($("#texto_filtros_creatives_<?= $id_order_dfp ?>").val().length > 25){
                    $("#td_creative_<?= $id_order_dfp ?>").text( $("#texto_filtros_creatives_<?= $id_order_dfp ?>").val().substr(0, 25)+"..." );
                }else{
                    $("#td_creative_<?= $id_order_dfp ?>").text( $("#texto_filtros_creatives_<?= $id_order_dfp ?>").val().substr(0, $("#texto_filtros_creatives_<?= $id_order_dfp ?>").val().length-1));
                }
            }
            $('#cmb_creatives_agregados_<?= $id_order_dfp ?> option:selected').appendTo("#cmb_creatives_<?= $id_order_dfp ?>");
        });

        $("#btn_pasar_size_<?= $id_order_dfp ?>").click( function (){
            $('#cmb_sizes_<?= $id_order_dfp ?> option:selected').appendTo("#cmb_sizes_agregados_<?= $id_order_dfp ?>");

            if($("#texto_filtros_sizes_<?= $id_order_dfp ?>").val()=="Todos"){
                $("#texto_filtros_sizes_<?= $id_order_dfp ?>").val("");
            }

            var label = $("#texto_filtros_sizes_<?= $id_order_dfp ?>").val();
            $("#cmb_sizes_agregados_<?= $id_order_dfp ?> option").each(function(){
                $("#texto_filtros_sizes_<?= $id_order_dfp ?>").val(label + $(this).text() + ",");
            });

            if($("#texto_filtros_sizes_<?= $id_order_dfp ?>").val().length > 25){
                $("#td_size_<?= $id_order_dfp ?>").text( $("#texto_filtros_sizes_<?= $id_order_dfp ?>").val().substr(0, 25)+"..." );
            }else{
                $("#td_size_<?= $id_order_dfp ?>").text( $("#texto_filtros_sizes_<?= $id_order_dfp ?>").val().substr(0, $("#texto_filtros_sizes_<?= $id_order_dfp ?>").val().length-1));
            }
        });

        $("#btn_borrar_size_<?= $id_order_dfp ?>").click( function (){

            var texto = $('#cmb_sizes_agregados_<?= $id_order_dfp ?> option:selected').text()+",";
            var label = $("#texto_filtros_sizes_<?= $id_order_dfp ?>").val();

            var nuevo_texto = label.replace(texto, "");

            $("#texto_filtros_sizes_<?= $id_order_dfp ?>").val(nuevo_texto);

            if($("#texto_filtros_sizes_<?= $id_order_dfp ?>").val()==""){
                $("#texto_filtros_sizes_<?= $id_order_dfp ?>").val("Todos");
                $("#td_size_<?= $id_order_dfp ?>").text($("#texto_filtros_sizes_<?= $id_order_dfp ?>").val());
            }else{
                if($("#texto_filtros_sizes_<?= $id_order_dfp ?>").val().length > 25){
                    $("#td_size_<?= $id_order_dfp ?>").text( $("#texto_filtros_sizes_<?= $id_order_dfp ?>").val().substr(0, 25)+"..." );
                }else{
                    $("#td_size_<?= $id_order_dfp ?>").text( $("#texto_filtros_sizes_<?= $id_order_dfp ?>").val().substr(0, $("#texto_filtros_sizes_<?= $id_order_dfp ?>").val().length-1));
                }
            }
            $('#cmb_sizes_agregados_<?= $id_order_dfp ?> option:selected').appendTo("#cmb_sizes_<?= $id_order_dfp ?>");
        });

        $("#btn_pasar_pais_<?= $id_order_dfp ?>").click( function (){
            $('#cmb_country_<?= $id_order_dfp ?> option:selected').appendTo("#cmb_paises_agregados_<?= $id_order_dfp ?>");

            if($("#texto_filtros_paises_<?= $id_order_dfp ?>").val()=="Todos"){
                $("#texto_filtros_paises_<?= $id_order_dfp ?>").val("");
            }

            var label = $("#texto_filtros_paises_<?= $id_order_dfp ?>").val();
            $("#cmb_paises_agregados_<?= $id_order_dfp ?> option").each(function(){
                $("#texto_filtros_paises_<?= $id_order_dfp ?>").val(label + $(this).text() + ",");
            });

            if($("#texto_filtros_paises_<?= $id_order_dfp ?>").val().length > 25){
                $("#td_pais_<?= $id_order_dfp ?>").text( $("#texto_filtros_paises_<?= $id_order_dfp ?>").val().substr(0, 25)+"..." );
            }else{
                $("#td_pais_<?= $id_order_dfp ?>").text( $("#texto_filtros_paises_<?= $id_order_dfp ?>").val().substr(0, $("#texto_filtros_paises_<?= $id_order_dfp ?>").val().length-1));
            }

        });

        $("#btn_borrar_pais_<?= $id_order_dfp ?>").click( function (){

            var texto = $('#cmb_paises_agregados_<?= $id_order_dfp ?> option:selected').text()+",";
            var label = $("#texto_filtros_paises_<?= $id_order_dfp ?>").val();

            var nuevo_texto = label.replace(texto, "");

            $("#texto_filtros_paises_<?= $id_order_dfp ?>").val(nuevo_texto);

            if($("#texto_filtros_paises_<?= $id_order_dfp ?>").val()==""){
                $("#texto_filtros_paises_<?= $id_order_dfp ?>").val("Todos");
                $("#td_pais_<?= $id_order_dfp ?>").text($("#texto_filtros_paises_<?= $id_order_dfp ?>").val());
            }else{
                if($("#texto_filtros_paises_<?= $id_order_dfp ?>").val().length > 25){
                    $("#td_pais_<?= $id_order_dfp ?>").text( $("#texto_filtros_paises_<?= $id_order_dfp ?>").val().substr(0, 25)+"..." );
                }else{
                    $("#td_pais_<?= $id_order_dfp ?>").text( $("#texto_filtros_paises_<?= $id_order_dfp ?>").val().substr(0, $("#texto_filtros_paises_<?= $id_order_dfp ?>").val().length-1));
                }
            }
            $('#cmb_paises_agregados_<?= $id_order_dfp ?> option:selected').appendTo("#cmb_country_<?= $id_order_dfp ?>");
        });

        $(".fancy").fancybox({
            'transitionIn'		: 'none',
            'transitionOut'		: 'none'
        });

        $(".scerrar_fancybox").click(function(event){
            event.preventDefault();
            $("#fancy_outer",window.parent.document).hide();
            $("#fancy_overlay",window.parent.document).hide();
        });

        $(".cerrar_fancybox").click(function(event){
            parent.$.fancybox.close();
            return false;
        });

        $("#chk_sitio_<?= $id_order_dfp ?>").change(function(){
            var thisCheck = $(this);
            if(thisCheck.is(':checked'))
            {
                if($("#chk_creatividad_<?= $id_order_dfp ?>").attr("checked")){
                    $("#chk_creatividad_<?= $id_order_dfp ?>").attr("checked", false);
                }
                if($("#chk_dia_<?= $id_order_dfp ?>").attr("checked")){
                    $("#chk_dia_<?= $id_order_dfp ?>").attr("checked", false);
                }
                $("#por_sitio_<?= $id_order_dfp ?>").attr('value', '1');
                $("#chk_costo_<?= $id_order_dfp ?>").attr('value', 'revenue');
                $("#cmb_creatives_agregados_<?= $id_order_dfp ?> option").each(function(){
                    $(this).appendTo("#cmb_creatives_<?= $id_order_dfp ?>");
                });
                $("#texto_filtros_creatives_<?= $id_order_dfp ?>").val("Todas");
                $("#td_creative_<?= $id_order_dfp ?>").text($("#texto_filtros_creatives_<?= $id_order_dfp ?>").val());

            }else{
                $("#por_sitio_<?= $id_order_dfp ?>").attr('value', '0');
                $("#chk_costo_<?= $id_order_dfp ?>").attr('value', 'total_revenue');
            }
        });

        $("#chk_creatividad_<?= $id_order_dfp ?>").change(function(){
            var thisCheck = $(this);
            if(thisCheck.is(':checked'))
            {
                if($("#chk_sitio_<?= $id_order_dfp ?>").attr("checked")){
                    $("#chk_sitio_<?= $id_order_dfp ?>").attr("checked", false);
                }
                $("#por_sitio_<?= $id_order_dfp ?>").attr('value', '0');
                $("#chk_costo_<?= $id_order_dfp ?>").attr('value', 'total_revenue');
            }else{
                $("#por_sitio_<?= $id_order_dfp ?>").attr('value', '1');
                $("#chk_costo_<?= $id_order_dfp ?>").attr('value', 'revenue');
            }
        });

        $("#chk_mes_<?= $id_order_dfp ?>").change(function(){
            var thisCheck = $(this);
            if(thisCheck.is(':checked'))
            {
                if($("#chk_dia_<?= $id_order_dfp ?>").attr("checked")){
                    $("#chk_dia_<?= $id_order_dfp ?>").attr("checked", false);
                }
            }
        });

        $("#chk_dia_<?= $id_order_dfp ?>").change(function(){
            var thisCheck = $(this);
            if(thisCheck.is(':checked'))
            {
                if($("#chk_mes_<?= $id_order_dfp ?>").attr("checked")){
                    $("#chk_mes_<?= $id_order_dfp ?>").attr("checked", false);
                }
                if($("#chk_sitio_<?= $id_order_dfp ?>").attr("checked")){
                    $("#chk_sitio_<?= $id_order_dfp ?>").attr("checked", false);
                }
            }
        });

        $("#cmb_country_<?= $id_order_dfp ?>").dblclick( function (){

            $('#cmb_country_<?= $id_order_dfp ?> option:selected').appendTo("#cmb_paises_agregados_<?= $id_order_dfp ?>");

            if($("#texto_filtros_paises_<?= $id_order_dfp ?>").val()=="Todos"){
                $("#texto_filtros_paises_<?= $id_order_dfp ?>").val("");
            }

            var label = $("#texto_filtros_paises_<?= $id_order_dfp ?>").val();
            $("#cmb_paises_agregados_<?= $id_order_dfp ?> option").each(function(){
                $("#texto_filtros_paises_<?= $id_order_dfp ?>").val(label + $(this).text() + ",");
            });

            if($("#texto_filtros_paises_<?= $id_order_dfp ?>").val().length > 25){
                $("#td_pais_<?= $id_order_dfp ?>").text( $("#texto_filtros_paises_<?= $id_order_dfp ?>").val().substr(0, 25)+"..." );
            }else{
                $("#td_pais_<?= $id_order_dfp ?>").text( $("#texto_filtros_paises_<?= $id_order_dfp ?>").val().substr(0, $("#texto_filtros_paises_<?= $id_order_dfp ?>").val().length-1));
            }

            if($("#texto_filtros_paises_<?= $id_order_dfp ?>").val()=="Todos"){
                $("#texto_filtros_paises_<?= $id_order_dfp ?>").val("");
            }
        });

        $("#cmb_paises_agregados_<?= $id_order_dfp ?>").dblclick( function (){
            var texto = $('#cmb_paises_agregados_<?= $id_order_dfp ?> option:selected').text()+",";
            var label = $("#texto_filtros_paises_<?= $id_order_dfp ?>").val();

            var nuevo_texto = label.replace(texto, "");

            $("#texto_filtros_paises_<?= $id_order_dfp ?>").val(nuevo_texto);

            if($("#texto_filtros_paises_<?= $id_order_dfp ?>").val()==""){
                $("#texto_filtros_paises_<?= $id_order_dfp ?>").val("Todos");
                $("#td_pais_<?= $id_order_dfp ?>").text($("#texto_filtros_paises_<?= $id_order_dfp ?>").val());
            }else{
                if($("#texto_filtros_paises_<?= $id_order_dfp ?>").val().length > 25){
                    $("#td_pais_<?= $id_order_dfp ?>").text( $("#texto_filtros_paises_<?= $id_order_dfp ?>").val().substr(0, 25)+"..." );
                }else{
                    $("#td_pais_<?= $id_order_dfp ?>").text( $("#texto_filtros_paises_<?= $id_order_dfp ?>").val().substr(0, $("#texto_filtros_paises_<?= $id_order_dfp ?>").val().length-1));
                }
            }
            $('#cmb_paises_agregados_<?= $id_order_dfp ?> option:selected').appendTo("#cmb_country_<?= $id_order_dfp ?>");
        });

        $("#cmb_line_items_<?= $id_order_dfp ?>").dblclick( function (){
            $('#cmb_line_items_<?= $id_order_dfp ?> option:selected').appendTo("#cmb_line_items_agregados_<?= $id_order_dfp ?>");

            if($("#texto_filtros_line_items_<?= $id_order_dfp ?>").val()=="Todos"){
                $("#texto_filtros_line_items_<?= $id_order_dfp ?>").val("");
            }

            var label = $("#texto_filtros_line_items_<?= $id_order_dfp ?>").val();
            $("#cmb_line_items_agregados_<?= $id_order_dfp ?> option").each(function(){
                $("#texto_filtros_line_items_<?= $id_order_dfp ?>").val(label + $(this).text() + ",");
            });

            if($("#texto_filtros_line_items_<?= $id_order_dfp ?>").val().length > 25){
                $("#td_line_item_<?= $id_order_dfp ?>").text( $("#texto_filtros_line_items_<?= $id_order_dfp ?>").val().substr(0, 25)+"..." );
            }else{
                $("#td_line_item_<?= $id_order_dfp ?>").text( $("#texto_filtros_line_items_<?= $id_order_dfp ?>").val().substr(0, $("#texto_filtros_line_items_<?= $id_order_dfp ?>").val().length-1));
            }
        });

        $("#cmb_line_items_agregados_<?= $id_order_dfp ?>").dblclick( function (){
            var texto = $('#cmb_line_items_agregados_<?= $id_order_dfp ?> option:selected').text()+",";
            var label = $("#texto_filtros_line_items_<?= $id_order_dfp ?>").val();

            var nuevo_texto = label.replace(texto, "");

            $("#texto_filtros_line_items_<?= $id_order_dfp ?>").val(nuevo_texto);

            if($("#texto_filtros_line_items_<?= $id_order_dfp ?>").val()==""){
                $("#texto_filtros_line_items_<?= $id_order_dfp ?>").val("Todos");
                $("#td_line_item_<?= $id_order_dfp ?>").text($("#texto_filtros_line_items_<?= $id_order_dfp ?>").val());
            }else{
                if($("#texto_filtros_line_items_<?= $id_order_dfp ?>").val().length > 25){
                    $("#td_line_item_<?= $id_order_dfp ?>").text( $("#texto_filtros_line_items_<?= $id_order_dfp ?>").val().substr(0, 25)+"..." );
                }else{
                    $("#td_line_item_<?= $id_order_dfp ?>").text( $("#texto_filtros_line_items_<?= $id_order_dfp ?>").val().substr(0, $("#texto_filtros_line_items_<?= $id_order_dfp ?>").val().length-1));
                }
            }
            $('#cmb_line_items_agregados_<?= $id_order_dfp ?> option:selected').appendTo("#cmb_line_items_<?= $id_order_dfp ?>");
        });

        $("#cmb_creatives_<?= $id_order_dfp ?>").dblclick( function (){
            $('#cmb_creatives_<?= $id_order_dfp ?> option:selected').appendTo("#cmb_creatives_agregados_<?= $id_order_dfp ?>");

            if($("#texto_filtros_creatives_<?= $id_order_dfp ?>").val()=="Todas"){
                $("#texto_filtros_creatives_<?= $id_order_dfp ?>").val("");
            }

            var label = $("#texto_filtros_creatives_<?= $id_order_dfp ?>").val();
            $("#cmb_creatives_agregados_<?= $id_order_dfp ?> option").each(function(){
                $("#texto_filtros_creatives_<?= $id_order_dfp ?>").val(label + $(this).text() + ",");
            });

            if($("#texto_filtros_creatives_<?= $id_order_dfp ?>").val().length > 25){
                $("#td_creative_<?= $id_order_dfp ?>").text( $("#texto_filtros_creatives_<?= $id_order_dfp ?>").val().substr(0, 25)+"..." );
            }else{
                $("#td_creative_<?= $id_order_dfp ?>").text( $("#texto_filtros_creatives_<?= $id_order_dfp ?>").val().substr(0, $("#texto_filtros_creatives_<?= $id_order_dfp ?>").val().length-1));
            }
        });

        $("#cmb_creatives_agregados_<?= $id_order_dfp ?>").dblclick( function (){
            var texto = $('#cmb_creatives_agregados_<?= $id_order_dfp ?> option:selected').text()+",";
            var label = $("#texto_filtros_creatives_<?= $id_order_dfp ?>").val();

            var nuevo_texto = label.replace(texto, "");

            $("#texto_filtros_creatives_<?= $id_order_dfp ?>").val(nuevo_texto);

            if($("#texto_filtros_creatives_<?= $id_order_dfp ?>").val()==""){
                $("#texto_filtros_creatives_<?= $id_order_dfp ?>").val("Todas");
                $("#td_creative_<?= $id_order_dfp ?>").text($("#texto_filtros_creatives_<?= $id_order_dfp ?>").val());
            }else{
                if($("#texto_filtros_creatives").val().length > 25){
                    $("#td_creative_<?= $id_order_dfp ?>").text( $("#texto_filtros_creatives_<?= $id_order_dfp ?>").val().substr(0, 25)+"..." );
                }else{
                    $("#td_creative_<?= $id_order_dfp ?>").text( $("#texto_filtros_creatives_<?= $id_order_dfp ?>").val().substr(0, $("#texto_filtros_creatives_<?= $id_order_dfp ?>").val().length-1));
                }
            }
            $('#cmb_creatives_agregados_<?= $id_order_dfp ?> option:selected').appendTo("#cmb_creatives_<?= $id_order_dfp ?>");
        });

        $("#cmb_sizes_<?= $id_order_dfp ?>").dblclick( function (){
            $('#cmb_sizes_<?= $id_order_dfp ?> option:selected').appendTo("#cmb_sizes_agregados_<?= $id_order_dfp ?>");

            if($("#texto_filtros_sizes_<?= $id_order_dfp ?>").val()=="Todos"){
                $("#texto_filtros_sizes_<?= $id_order_dfp ?>").val("");
            }

            var label = $("#texto_filtros_sizes_<?= $id_order_dfp ?>").val();
            $("#cmb_sizes_agregados_<?= $id_order_dfp ?> option").each(function(){
                $("#texto_filtros_sizes_<?= $id_order_dfp ?>").val(label + $(this).text() + ",");
            });

            if($("#texto_filtros_sizes_<?= $id_order_dfp ?>").val().length > 25){
                $("#td_size_<?= $id_order_dfp ?>").text( $("#texto_filtros_sizes_<?= $id_order_dfp ?>").val().substr(0, 25)+"..." );
            }else{
                $("#td_size_<?= $id_order_dfp ?>").text( $("#texto_filtros_sizes_<?= $id_order_dfp ?>").val().substr(0, $("#texto_filtros_sizes_<?= $id_order_dfp ?>").val().length-1));
            }
        });

        $("#cmb_sizes_agregados_<?= $id_order_dfp ?>").dblclick( function (){
            var texto = $('#cmb_sizes_agregados_<?= $id_order_dfp ?> option:selected').text()+",";
            var label = $("#texto_filtros_sizes_<?= $id_order_dfp ?>").val();

            var nuevo_texto = label.replace(texto, "");

            $("#texto_filtros_sizes_<?= $id_order_dfp ?>").val(nuevo_texto);

            if($("#texto_filtros_sizes_<?= $id_order_dfp ?>").val()==""){
                $("#texto_filtros_sizes_<?= $id_order_dfp ?>").val("Todos");
                $("#td_size_<?= $id_order_dfp ?>").text($("#texto_filtros_sizes_<?= $id_order_dfp ?>").val());
            }else{
                if($("#texto_filtros_sizes_<?= $id_order_dfp ?>").val().length > 25){
                    $("#td_size_<?= $id_order_dfp ?>").text( $("#texto_filtros_sizes_<?= $id_order_dfp ?>").val().substr(0, 25)+"..." );
                }else{
                    $("#td_size_<?= $id_order_dfp ?>").text( $("#texto_filtros_sizes_<?= $id_order_dfp ?>").val().substr(0, $("#texto_filtros_sizes_<?= $id_order_dfp ?>").val().length-1));
                }
            }
            $('#cmb_sizes_agregados_<?= $id_order_dfp ?> option:selected').appendTo("#cmb_sizes");
        });

        $("#btn_pasar_line_items_1_<?= $id_order_dfp ?>").click( function (){
            $("#cmb_line_items_<?= $id_order_dfp ?> option").each(function(){
                $(this).appendTo("#cmb_line_items_agregados_<?= $id_order_dfp ?>");

                if($("#texto_filtros_line_items_<?= $id_order_dfp ?>").val()=="Todos"){
                    $("#texto_filtros_line_items_<?= $id_order_dfp ?>").val("");
                }

                var label = $("#texto_filtros_line_items_<?= $id_order_dfp ?>").val();
                $("#cmb_line_items_agregados_<?= $id_order_dfp ?> option").each(function(){
                    $("#texto_filtros_line_items_<?= $id_order_dfp ?>").val(label + $(this).text() + ",");
                });

                if($("#texto_filtros_line_items_<?= $id_order_dfp ?>").val().length > 25){
                    $("#td_line_item_<?= $id_order_dfp ?>").text( $("#texto_filtros_line_items_<?= $id_order_dfp ?>").val().substr(0, 25)+"..." );
                }else{
                    $("#td_line_item_<?= $id_order_dfp ?>").text( $("#texto_filtros_line_items_<?= $id_order_dfp ?>").val().substr(0, $("#texto_filtros_line_items_<?= $id_order_dfp ?>").val().length-1));
                }
            });
        });

        $("#btn_pasar_line_items_2_<?= $id_order_dfp ?>").click( function (){
            $("#cmb_line_items_agregados_<?= $id_order_dfp ?> option").each(function(){
                $(this).appendTo("#cmb_line_items_<?= $id_order_dfp ?>");
            });
            $("#texto_filtros_line_items_<?= $id_order_dfp ?>").val("Todos");
            $("#td_line_item_<?= $id_order_dfp ?>").text($("#texto_filtros_line_items_<?= $id_order_dfp ?>").val());
        });

        $("#btn_pasar_creatives_1_<?= $id_order_dfp ?>").click( function (){
            $("#cmb_creatives_<?= $id_order_dfp ?> option").each(function(){
                $(this).appendTo("#cmb_creatives_agregados_<?= $id_order_dfp ?>");
                if($("#texto_filtros_creatives_<?= $id_order_dfp ?>").val()=="Todas"){
                    $("#texto_filtros_creatives_<?= $id_order_dfp ?>").val("");
                }

                var label = $("#texto_filtros_creatives_<?= $id_order_dfp ?>").val();
                $("#cmb_creatives_agregados_<?= $id_order_dfp ?> option").each(function(){
                    $("#texto_filtros_creatives_<?= $id_order_dfp ?>").val(label + $(this).text() + ",");
                });

                if($("#texto_filtros_creatives_<?= $id_order_dfp ?>").val().length > 25){
                    $("#td_creative_<?= $id_order_dfp ?>").text( $("#texto_filtros_creatives_<?= $id_order_dfp ?>").val().substr(0, 25)+"..." );
                }else{
                    $("#td_creative_<?= $id_order_dfp ?>").text( $("#texto_filtros_creatives_<?= $id_order_dfp ?>").val().substr(0, $("#texto_filtros_creatives_<?= $id_order_dfp ?>").val().length-1));
                }
            });
        });

        $("#btn_pasar_creatives_2_<?= $id_order_dfp ?>").click( function (){
            $("#cmb_creatives_agregados_<?= $id_order_dfp ?> option").each(function(){
                $(this).appendTo("#cmb_creatives_<?= $id_order_dfp ?>");
            });
            $("#texto_filtros_creatives_<?= $id_order_dfp ?>").val("Todas");
            $("#td_creative_<?= $id_order_dfp ?>").text($("#texto_filtros_creatives_<?= $id_order_dfp ?>").val());
        });

        $("#btn_pasar_sizes_1_<?= $id_order_dfp ?>").click( function (){
            $("#cmb_sizes_<?= $id_order_dfp ?> option").each(function(){
                $(this).appendTo("#cmb_sizes_agregados_<?= $id_order_dfp ?>");
                if($("#texto_filtros_sizes_<?= $id_order_dfp ?>").val()=="Todos"){
                    $("#texto_filtros_sizes_<?= $id_order_dfp ?>").val("");
                }

                var label = $("#texto_filtros_sizes_<?= $id_order_dfp ?>").val();
                $("#cmb_sizes_agregados_<?= $id_order_dfp ?> option").each(function(){
                    $("#texto_filtros_sizes_<?= $id_order_dfp ?>").val(label + $(this).text() + ",");
                });

                if($("#texto_filtros_sizes_<?= $id_order_dfp ?>").val().length > 25){
                    $("#td_size_<?= $id_order_dfp ?>").text( $("#texto_filtros_sizes_<?= $id_order_dfp ?>").val().substr(0, 25)+"..." );
                }else{
                    $("#td_size_<?= $id_order_dfp ?>").text( $("#texto_filtros_sizes_<?= $id_order_dfp ?>").val().substr(0, $("#texto_filtros_sizes_<?= $id_order_dfp ?>").val().length-1));
                }
            });
        });

        $("#btn_pasar_sizes_2_<?= $id_order_dfp ?>").click( function (){
            $("#cmb_sizes_agregados_<?= $id_order_dfp ?> option").each(function(){
                $(this).appendTo("#cmb_sizes_<?= $id_order_dfp ?>");
            });
            $("#texto_filtros_sizes_<?= $id_order_dfp ?>").val("Todos");
            $("#td_size_<?= $id_order_dfp ?>").text($("#texto_filtros_sizes_<?= $id_order_dfp ?>").val());
        });

        $("#btn_pasar_paises_1_<?= $id_order_dfp ?>").click( function (){
            $("#cmb_country_<?= $id_order_dfp ?> option").each(function(){
                $(this).appendTo("#cmb_paises_agregados_<?= $id_order_dfp ?>");
                if($("#texto_filtros_paises_<?= $id_order_dfp ?>").val()=="Todos"){
                    $("#texto_filtros_paises_<?= $id_order_dfp ?>").val("");
                }

                var label = $("#texto_filtros_paises_<?= $id_order_dfp ?>").val();
                $("#cmb_paises_agregados_<?= $id_order_dfp ?> option").each(function(){
                    $("#texto_filtros_paises_<?= $id_order_dfp ?>").val(label + $(this).text() + ",");
                });

                if($("#texto_filtros_paises_<?= $id_order_dfp ?>").val().length > 25){
                    $("#td_pais_<?= $id_order_dfp ?>").text( $("#texto_filtros_paises_<?= $id_order_dfp ?>").val().substr(0, 25)+"..." );
                }else{
                    $("#td_pais_<?= $id_order_dfp ?>").text( $("#texto_filtros_paises_<?= $id_order_dfp ?>").val().substr(0, $("#texto_filtros_paises_<?= $id_order_dfp ?>").val().length-1));
                }
            });
        });

        $("#btn_pasar_paises_2_<?= $id_order_dfp ?>").click( function (){
            $("#cmb_paises_agregados_<?= $id_order_dfp ?> option").each(function(){
                $(this).appendTo("#cmb_country_<?= $id_order_dfp ?>");
            });
            $("#texto_filtros_paises_<?= $id_order_dfp ?>").val("Todos");
            $("#td_pais_<?= $id_order_dfp ?>").text($("#texto_filtros_paises_<?= $id_order_dfp ?>").val());
        });

        $("#seleccionar_todo_metricas_<?= $id_order_dfp ?>").click(function()
        {
            var checked_status = this.checked;

            if(id_adserver == '1' || id_adserver == '0'){
                $("input[name='chk_columnas_<?= $id_order_dfp ?>[]']").each(function(){
                    this.checked = checked_status;
                });
            }else{
                $("input[name='chk_columnas_<?= $id_order_dfp ?>[]']").each(function(){
                    if($(this).attr('id') == 'chk_imps_<?= $id_order_dfp ?>' || $(this).attr('id') == 'chk_clicks_<?= $id_order_dfp ?>' || $(this).attr('id') == 'chk_ctr_<?= $id_order_dfp ?>')
                    this.checked = checked_status;
                });
            }

        });

        $("#seleccionar_todo_dimensiones_<?= $id_order_dfp ?>").click(function()
        {
            var checked_status = this.checked;

            if(id_adserver == '1' || id_adserver == '0'){
                $("input[name='chk_grupos_<?= $id_order_dfp ?>[]']").each(function(){
                    if($(this).attr('id') == 'chk_sitio_<?= $id_order_dfp ?>'){
                        this.checked = false;
                    }else{
                        this.checked = checked_status;
                    }
                });
            }else{
                $("input[name='chk_grupos_<?= $id_order_dfp ?>[]']").each(function(){
                    if($(this).attr('id') == 'chk_pais_<?= $id_order_dfp ?>')
                    this.checked = checked_status;
                });
            }
        });
    });
</script>

<body class="ex_highlight_row">
    <input type="hidden" id="texto_filtros_line_items_<?= $id_order_dfp ?>" value="Todos"/>
    <input type="hidden" id="texto_filtros_creatives_<?= $id_order_dfp ?>" value="Todas"/>
    <input type="hidden" id="texto_filtros_paises_<?= $id_order_dfp ?>" value="Todos"/>
    <input type="hidden" id="texto_filtros_sizes_<?= $id_order_dfp ?>" value="Todos"/>
    <input type="hidden" id="id_adserver_<?= $id_order_dfp ?>" value="<?= $id_adserver ?>"/>
    <input type="hidden" id="por_sitio_<?= $id_order_dfp ?>" value="0"/>
    <input type="hidden" id="id_order_dfp_<?= $id_order_dfp ?>" value="<?= $id_order_dfp ?>"/>

    <input type="hidden" id="id_anunciante_adserver_<?= $id_order_dfp ?>" value="<?= $id_anunciante_adserver ?>" />

    <div class="row">
        <b>Campa&ntilde;a</b> <?= $order_dfp ?>
    </div>

    <div class="row">
        <b>Anunciante</b> <?= $anunciante_adserver ?>
    </div>

    <div>
        <div class="floatLeft">
            <b>Rango</b>
            <select id="cmb_range_<?= $id_order_dfp ?>" name="cmb_range_<?= $id_order_dfp ?>">
                <?php
                foreach ($arr_range as $c => $v) {
                    ?>
                    <option value="<?= $c ?>"><?= $v ?></option>
                    <?php
                }
                ?>
            </select>
        </div>

        <div id="div_fechas_especificas_<?= $id_order_dfp ?>" style="visibility: hidden; padding-left: 200px;">
            <input type="text" id="fecha_desde_<?= $id_order_dfp ?>" name="fecha_desde_<?= $id_order_dfp ?>" placeholder="Fecha desde..." style="width: 100px !important;" />
            <b>-</b>
            <input type="text" id="fecha_hasta_<?= $id_order_dfp ?>" name="fecha_hasta_<?= $id_order_dfp ?>" placeholder="Fecha hasta..." style="width: 100px !important;" />
        </div>
    </div>

    <hr style="border:none;border-bottom: 1px solid #eee;margin:15px 0 10px" />

    <div class="clear">

        <table class="tabla" cellspacing="30" width="100%">
            <tr class="encabezado">
                <td style="padding: 4px 6px;border-top: 3px solid #FFF;border-right: 6px solid #FFF;border-left: 6px solid #FFF;background-color:#eee;" colspan="2">
                    <div class="floatLeft">
                        <b>M&eacute;tricas</b>
                    </div>
                    <div class="floatRight">
                        <input type="checkbox" id="seleccionar_todo_metricas_<?= $id_order_dfp ?>" />
                    </div>
                </td>
                <td style="padding: 6px 4px;width: 200px;border-right: 6px solid #FFF;border-top: 3px solid #FFF;background-color:#eee;">
                    <div class="floatLeft">
                        <b>Filtros</b>
                    </div>
                </td>
                <td style="padding: 6px 4px;border-right: 6px solid #FFF;border-top: 3px solid #FFF;background-color:#eee;" colspan="2">
                    <div class="floatLeft">
                        <b>Dimensiones</b>
                    </div>
                    <div class="floatRight">
                        <input type="checkbox" id="seleccionar_todo_dimensiones_<?= $id_order_dfp ?>" />
                    </div>
                </td>
            </tr>

            <tr class="segundo_encabezado">
                <td style="padding: 6px 4px;border-right: 6px solid #FFF;border-left: 6px solid #FFF;border-bottom: 1px solid #eee;font-size:0.9em" colspan="2"><b>Columnas</b></td>
                <td style="padding: 6px 4px;border-right: 6px solid #FFF;border-bottom: 1px solid #eee;font-size:0.9em"><b>Filtrar por</b></td>
                <td style="padding: 6px 4px;border-right: 6px solid #FFF;border-bottom: 1px solid #eee;font-size:0.9em" colspan="2"><b>Agrupar por</b></td>
            </tr>

            <tr>
                <td style="width: 15%;border-left: 6px solid #FFF;"><input checked type="checkbox" value="imps" name="chk_columnas_<?= $id_order_dfp ?>[]" id="chk_imps_<?= $id_order_dfp ?>" />Impresiones</td>
                <td style="border-right: 6px solid #FFF;width: 15%"><input checked type="checkbox" value="clicks" name="chk_columnas_<?= $id_order_dfp ?>[]" id="chk_clicks_<?= $id_order_dfp ?>" />Clicks</td>
                <td style="width: 35%;border-right: 6px solid #FFF;padding: 6px 4px;">
                    <table style="width: 100%">
                        <tr>
                            <td style="width: 35%;">Tama&ntilde;os</td>
                            <td id="td_size_<?= $id_order_dfp ?>" style="color:#99BC99;width: 55%;">Todos</td>
                            <td style="text-align: right;width: 10%">
                                <a id="link_filtro_formato_<?= $id_order_dfp ?>" class="fancy" href="#div_filtro_sizes_<?= $id_order_dfp ?>" style="text-align: right">Editar</a>
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width: 15%;"><input type="checkbox" value="size" name="chk_grupos_<?= $id_order_dfp ?>[]" id="chk_formato_<?= $id_order_dfp ?>"/>Tama&ntilde;o</td>
                <td style="width: 15%;border-right: 6px solid #FFF;"><input class="chk_intervalo_<?= $id_order_dfp ?>" type="checkbox" value="day" name="chk_intervalo_<?= $id_order_dfp ?>[]" id="chk_dia_<?= $id_order_dfp ?>" />D&iacute;a</td>
            </tr>

            <tr>
                <td style="border-left: 6px solid #FFF;"><input checked type="checkbox" value="ctr" name="chk_columnas_<?= $id_order_dfp ?>[]" id="chk_ctr_<?= $id_order_dfp ?>" />CTR</td>
                <td id="td_chk_convs" style="border-right: 6px solid #FFF"><input type="checkbox" value="total_convs" name="chk_columnas_<?= $id_order_dfp ?>[]" id="chk_convs_<?= $id_order_dfp ?>" />Conversiones</td>
                <td style="border-right: 6px solid #FFF;padding: 6px 4px;">
                    <table style="width: 100%">
                        <tr>
                            <td style="width: 35%">Creatividad</td>
                            <td id="td_creative_<?= $id_order_dfp ?>" style="color:#99BC99;width: 55%;">Todas</td>
                            <td style="text-align: right;width: 10%">
                                <a id="link_filtro_creatividad_<?= $id_order_dfp ?>" class="fancy" href="#div_filtro_creatives_<?= $id_order_dfp ?>" style="text-align: right">Editar</a>
                            </td>
                        </tr>
                    </table>
                </td>
                <td><input class="chk_grupos_<?= $id_order_dfp ?>" type="checkbox" value="creative_name" name="chk_grupos_<?= $id_order_dfp ?>[]" id="chk_creatividad_<?= $id_order_dfp ?>"/>Creatividad</td>
                <td style="width: 15%;border-right: 6px solid #FFF;"><input class="chk_intervalo_<?= $id_order_dfp ?>" type="checkbox" value="month" name="chk_intervalo_<?= $id_order_dfp ?>[]" id="chk_mes_<?= $id_order_dfp ?>" />Mes</td>
            </tr>

            <tr>
                <?php if (!$es_agencia) { ?>
                    <td style="border-left: 6px solid #FFF;"><input type="checkbox" value="total_revenue" name="chk_columnas_<?= $id_order_dfp ?>[]" id="chk_costo_<?= $id_order_dfp ?>"/>Costo</td>
                <?php } else { ?>
                    <td style="border-left: 6px solid #FFF;">&nbsp;</td>
                <?php } ?>
                <td style="border-right: 6px solid #FFF;">&nbsp;</td>
                <td style="border-right: 6px solid #FFF;padding: 6px 4px;">
                    <table style="width: 100%;">
                        <tr>
                            <td style="width: 35%">Segmentaci&oacute;n</td>
                            <td id="td_line_item_<?= $id_order_dfp ?>" style="color:#99BC99;width: 55%;">Todos</td>
                            <td style="text-align: right;width: 10%">
                                <a id="link_filtro_canal_tematico_<?= $id_order_dfp ?>" class="fancy" href="#div_filtro_line_items_<?= $id_order_dfp ?>" style="text-align: right">Editar</a>
                            </td>
                        </tr>
                    </table>
                </td>
                <td>
                    <input type="checkbox" value="line_item_name" name="chk_grupos_<?= $id_order_dfp ?>[]" id="chk_canal_tematico_<?= $id_order_dfp ?>"/> Segmentaci&oacute;n
                </td>
                <td style="border-right: 6px solid #FFF;">
                    <?php if($mostrar_filtro_pais){ ?>
                        <input class="chk_grupos_<?= $id_order_dfp ?>" type="checkbox" value="geo_country" name="chk_grupos_<?= $id_order_dfp ?>[]" id="chk_pais_<?= $id_order_dfp ?>" />Pa&iacute;s
                    <?php }else{ ?>
                        &nbsp;
                    <?php } ?>
                </td>
            </tr>

            <tr>
                <td style="border-left: 6px solid #FFF;">&nbsp;</td>
                <td style="border-right: 6px solid #FFF;">&nbsp;</td>
                <td style="border-right: 6px solid #FFF;padding: 6px 4px;">
                    <?php if($mostrar_filtro_pais){ ?>
                    <table cellpadding="0" cellspacing="0" style="width: 100%">
                        <tr>
                            <td style="width: 35%">Pa&iacute;s</td>
                            <td id="td_pais_<?= $id_order_dfp ?>" style="color:#99BC99;width: 55%;">Todos</td>
                            <td style="text-align: right; width: 10%">
                                <a id="link_filtro_pais_<?= $id_order_dfp ?>" class="fancy" href="#div_filtro_paises_<?= $id_order_dfp ?>" style="text-align: right">Editar</a>
                            </td>
                        </tr>
                    </table>
                    <?php }else{ ?>
                        &nbsp;
                    <?php } ?>
                </td>
                <td>
                    <?php if ($agrupar_por_sitio) { ?>
                        <input type="checkbox" value="site_name" name="chk_grupos_<?= $id_order_dfp ?>[]" id="chk_sitio_<?= $id_order_dfp ?>"/>
                        Sitio espec&iacute;fico
                    <?php } ?>
                </td>
                <td style="border-right: 6px solid #FFF;">&nbsp;</td>
            </tr>
        </table>

        <hr style="border:none;border-bottom: 1px solid #eee;margin:10px 0 15px" />

        <table style="width: 100%;">
            <tr>
                <td style="width: 120px;padding-left: 6px;">
                    <input class="btn_ejecutar_reporte button_new" type="button" value="Ejecutar reporte" id="btn_ejecutar_reporte_<?= $id_order_dfp ?>" name="btn_ejecutar_reporte" />
                    <!-- <input class="fancy btn_ejecutar_reporte" href="#div_suscripcion_reporte" type="button" value="Recibir reporte por correo electr&oacute;nico" id="por_correo_electronico" name="por_correo_electronico" /> -->
                </td>
                <td style="padding-top: 6px;">
                    <div id="loader_report_<?= $id_order_dfp ?>" style="display: none">
                        <div style="float: left">
                            <img height="18" src="/images/ajax-loader.gif" />
                        </div>
                        <div style="float: left;margin-left: 6px">
                            <label style="font-weight: bold" >Espere un momento para ver los resultados ...</label>
                        </div>
                    </div>

                    <div id="error_en_reporte_<?= $id_order_dfp ?>" style="display: none;">
                        <div style="float: left;margin-left: 6px">
                            <label style="font-weight: bold; color: red;" >No se pudieron obtener datos seg&uacute;n el rango especificado.</label>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <div style="margin-top: 20px;float: left; width: 100%" id="tbl_reporte_<?= $id_order_dfp ?>"></div>

        <div style="display: none;">
            <div id="div_filtro_line_items_<?= $id_order_dfp ?>" style="width:470px !important;height:295px !important;overflow:auto;">
                <b>Segmentaci&oacute;n</b>
                <img src="images/ajax-loader.gif" id="loader_filtros_li_<?= $id_order_dfp ?>" style="display:none; height: 10px;" />
                <table class="tabla_interior">
                    <tr>
                        <td><input type="button" value=">>>" id="btn_pasar_line_items_1_<?= $id_order_dfp ?>"/></td>
                        <td>&nbsp;</td>
                        <td style="text-align: right;"><input type="button" value="<<<" id="btn_pasar_line_items_2_<?= $id_order_dfp ?>"/></td>
                    </tr>
                    <tr>
                        <td id="tbl_line_items">
                            <select style="width: 200px !important;" size="10" id="cmb_line_items_<?= $id_order_dfp ?>" name="cmb_line_items_<?= $id_order_dfp ?>">
                            </select>
                        </td>
                        <td>
                            <table>
                                <tr>
                                    <td><input type="button" value=">>" id="btn_pasar_line_item_<?= $id_order_dfp ?>"></td>
                                </tr>
                                <tr>
                                    <td><input type="button" value="<<" id="btn_borrar_line_item_<?= $id_order_dfp ?>"></td>
                                </tr>
                            </table>
                        </td>
                        <td style="text-align: right;">
                            <select style="width: 200px !important;" size="10" id="cmb_line_items_agregados_<?= $id_order_dfp ?>" name="cmb_line_items_agregados_<?= $id_order_dfp ?>">
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            &nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center" colspan="3">
                            <input type="button" value="Aceptar" class="cerrar_fancybox" />
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div style="display: none;">
            <div id="div_filtro_creatives_<?= $id_order_dfp ?>" style="width:585px !important;height:305px !important;overflow:auto;">
                <b>Creatividades</b>
                <img src="/images/ajax-loader.gif" id="loader_filtros_cr_<?= $id_order_dfp ?>" style="display:none; height: 10px;" />
                <table class="tabla_interior">
                    <tr>
                        <td><input type="button" value=">>>" id="btn_pasar_creatives_1_<?= $id_order_dfp ?>"/></td>
                        <td>&nbsp;</td>
                        <td style="text-align: right;"><input type="button" value="<<<" id="btn_pasar_creatives_2_<?= $id_order_dfp ?>"/></td>
                    </tr>
                    <tr>
                        <td id="tbl_creatives">
                            <select style="width: 250px !important;" size="10" id="cmb_creatives_<?= $id_order_dfp ?>" name="cmb_creatives_<?= $id_order_dfp ?>">
                            </select>
                        </td>
                        <td>
                            <table>
                                <tr>
                                    <td><input type="button" value=">>" id="btn_pasar_creative_<?= $id_order_dfp ?>"></td>
                                </tr>
                                <tr>
                                    <td><input type="button" value="<<" id="btn_borrar_creative_<?= $id_order_dfp ?>"></td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <select style="width: 250px !important;" size="10" id="cmb_creatives_agregados_<?= $id_order_dfp ?>" name="cmb_creatives_agregados_<?= $id_order_dfp ?>">
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            &nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center" colspan="3">
                            <input type="button" value="Aceptar" class="cerrar_fancybox"/>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div style="display: none;">
            <div id="div_filtro_paises_<?= $id_order_dfp ?>" style="width:485px;height:305px;overflow:auto;">
                <b>Pa&iacute;s</b>
                <table class="tabla_interior">
                    <tr>
                        <td><input type="button" value=">>>" id="btn_pasar_paises_1_<?= $id_order_dfp ?>"/></td>
                        <td>&nbsp;</td>
                        <td style="text-align: right;"><input type="button" value="<<<" id="btn_pasar_paises_2_<?= $id_order_dfp ?>"/></td>
                    </tr>
                    <tr>
                        <td>
                            <select size="10" style="width: 200px !important;" id="cmb_country_<?= $id_order_dfp ?>" name="cmb_country_<?= $id_order_dfp ?>">
                                <?php
                                foreach ($paises as $row) {
                                    if ($row->descripcion != "Desconocido") {
                                        ?>
                                        <option value="<?= $row->id ?>"><?= $row->descripcion ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        </td>
                        <td>
                            <table>
                                <tr>
                                    <td><input type="button" value=">>" id="btn_pasar_pais_<?= $id_order_dfp ?>"></td>
                                </tr>
                                <tr>
                                    <td><input type="button" value="<<" id="btn_borrar_pais_<?= $id_order_dfp ?>"></td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <select style="width: 200px !important;" size="10" id="cmb_paises_agregados_<?= $id_order_dfp ?>" name="cmb_paises_agregados_<?= $id_order_dfp ?>">
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            &nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center" colspan="3">
                            <input type="button" value="Aceptar" class="cerrar_fancybox" />
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div style="display: none;">
            <div id="div_filtro_sizes_<?= $id_order_dfp ?>" style="width:480px;height:305px;overflow:auto;">
                <b>Tama&ntilde;os</b>
                <table class="tabla_interior">
                    <tr>
                        <td><input type="button" value=">>>" id="btn_pasar_sizes_1_<?= $id_order_dfp ?>"/></td>
                        <td>&nbsp;</td>
                        <td style="text-align: right;"><input type="button" value="<<<" id="btn_pasar_sizes_2_<?= $id_order_dfp ?>"/></td>
                    </tr>
                    <tr>
                        <td>
                            <select style="width: 200px !important;" size="10" id="cmb_sizes_<?= $id_order_dfp ?>" name="cmb_sizes_<?= $id_order_dfp ?>">
                                <?php
                                foreach ($tamanios as $size) {
                                    if ($size->descripcion != 'Layer' && $size->descripcion != 'Skin') {
                                        ?>
                                        <option value="<?= $size->descripcion ?>"><?= $size->descripcion ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        </td>
                        <td>
                            <table>
                                <tr>
                                    <td><input type="button" value=">>" id="btn_pasar_size_<?= $id_order_dfp ?>"></td>
                                </tr>
                                <tr>
                                    <td><input type="button" value="<<" id="btn_borrar_size_<?= $id_order_dfp ?>"></td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <select style="width: 200px !important;" size="10" id="cmb_sizes_agregados_<?= $id_order_dfp ?>" name="cmb_sizes_agregados_<?= $id_order_dfp ?>">
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            &nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center" colspan="3">
                            <input type="button" value="Aceptar" class="cerrar_fancybox"/>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div id="question" style="display:none;cursor: default;padding: 10px;">
        <h2>Por favor, aguarde un momento ...</h2>
        <p>Este reporte puede llegar a demorarse hasta 5 minutos.</p>

        <!--
        <hr />
        <input type="button" id="enviar_correo_<?= $id_order_dfp ?>" name="enviar_reporte2" class="button_new" value="No quiero esperar, enviarmelo a mi correo" />

        <div id="enviar_por_correo_<?= $id_order_dfp ?>" style="display: none;">
            <p>Ingrese su correo electr&oacute;nico y se le enviar&aacute; el reporte solicitado.</p>

            <input type="text" name="correo_electronico" id="correo_electronico_<?= $id_order_dfp ?>" value="<?= $this->email_usuario ?>" style="padding: 6px 4px; font-size: 11px; width: 160px !important" />

            <select name="extension" id="extension_<?= $id_order_dfp ?>" style="width: 60px; padding: 2px;">
                <option value="xls">Excel</option>
                <option value="pdf">PDF</option>
            </select>

            <input type="button" id="enviar_reporte_<?= $id_order_dfp ?>" name="enviar_reporte" class="button_new" value="Enviar" />
            <input type="button" id="cancelar_reporte_<?= $id_order_dfp ?>" name="cancelar_reporte" class="button_new" value="Cancelar" />
        </div>
        -->
    </div>