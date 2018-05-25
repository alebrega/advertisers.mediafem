<script type="text/JavaScript">

    $(document).ready(function(){
        $(".celda-titulo-estadisticas").click(function(event){
            var now = new Date()
            var seconds = now.getSeconds()+"o"+now.getMinutes();
            
            $("#loader_report").css("display", "inline");

            var orden = $(this).attr('id');

            var direccion = $("#direccion").val();

            if($("#direccion").val()=="ASC"){
                $("#direccion").val("DESC");
            }else{
                $("#direccion").val("ASC");
            }
            
            var adv_id = $("#cmb_anunciantes").find(':selected').val();
            var rango = $("#cmb_range").find(':selected').val();
            var intervalo = $("#cmb_interval").find(':selected').val();
            var timezone = $("#cmb_timezone").find(':selected').val();

            if(rango=="especific"){
                var fecha_desde = $("#fecha_desde").val();
                var fecha_hasta = $("#fecha_hasta").val();

                if(fecha_desde=="" || fecha_hasta==""){
                    $("#loader_report").css("display", "none");
                    alert("Debe completar las 2 fechas");
                    return;
                }

            }else{
                var fecha_desde = 0;
                var fecha_hasta = 0;
            }

            var columnas = "";

            $("input[name='chk_columnas[]']:checked").each(function(){
                columnas = columnas + $(this).val() + ";";
            });

            if(columnas==""){
                $("#loader_report").css("display", "none");
                alert("Debe elegir al menos una columna");
                return;
            }

            var filtros_li = "";

            $("#cmb_line_items_agregados option").each(function(){
                filtros_li = filtros_li + $(this).attr('value') + ";";
            });

            var filtros_cr = "";

            $("#cmb_creatives_agregados option").each(function(){
                filtros_cr = filtros_cr + $(this).attr('value') + ";";
            });

            var filtros_sizes = "";

            $("#cmb_sizes_agregados option").each(function(){
                filtros_sizes = filtros_sizes + $(this).attr('value') + ";";
            });

            var filtros_paises = "";

            $("#cmb_country option").each(function(){
                filtros_paises = filtros_paises + $(this).attr('value') + ";";
            });

            var grupos = "";

            $("input[name='chk_grupos[]']:checked").each(function(){
                grupos = grupos + $(this).val() + ";";
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

            var timer = setTimeout("$('#esperando').show();", 10000);

            $("#tbl_reporte").load('/welcome/get_dynamic_report/'+adv_id+'/'+rango+'/'+filtros_li+'/'+grupos+'/'+columnas+'/'+fecha_desde+'/'+fecha_hasta+'/'+filtros_paises+'/'+filtros_cr+'/'+filtros_sizes+'/'+intervalo+'/'+timezone+'/'+orden+'/'+direccion+'/'+seconds, function(){
                $("#script_orden").load('/welcome/get_script_orden');
                clearTimeout(timer);
                $("#loader_report").css("display", "none");
                $("#div_excel").css("display", "inline");
                $("#div_exportar").css("display", "inline")
                $("#div_pdf").css("display", "inline");
                $('#esperando').hide();
            });

        });

    });
</script>