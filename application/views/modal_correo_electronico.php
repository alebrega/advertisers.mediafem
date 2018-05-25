<table style="width:494px;margin-top:9px;">
    <tr class="encabezado_rosa">
        <td colspan="2">Recibir informe por correo electr&oacute;nico</td>
    </tr>
    <tr>
        <td width="110">Remitente</td>
        <td><input type="text" name="remitente" value="<?= $email ?>" maxlength="100" class="txt_default" /></td>
    </tr>
    <tr>
        <td>Frecuencia</td>
        <td>
            <select name="frecuencia" class="cmb_default" id="frecuencia">
                <option value="diaria">Diaria</option>
                <option value="semanal">Semanal</option>
                <option value="mensual">Mensual</option>
                <option value="trimestral">Trimestral</option>
            </select>
        </td>        
    </tr>
    <tr>
        <td colspan="2">
            <table id="dia_de_la_semana" style="display: none;">
                <tr>
                    <td width="110">Dia de la semana:</td>
                    <td>
                        <select name="dia_de_la_semana" class="cmb_default">
                            <option value="1">Lunes</option>
                            <option value="2">Martes</option>
                            <option value="3">Miercoles</option>
                            <option value="4">Jueves</option>
                            <option value="5">Viernes</option>
                            <option value="6">Sabado</option>
                            <option value="0">Domingo</option>
                        </select>
                    </td>
                </tr>
            </table>
            <table id="dia_del_mes" style="display: none;">
                <td width="110">Dia del mes:</td> 
                <td>
                    <select name="dia_del_mes" class="cmb_default" id="select_dia_del_mes">
                    </select>
                </td>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <input type="button" name="aceptar" value="Aceptar" class="aceptar btn_default" /> 
            <input type="button" name="cancelar" value="Cancelar" class="cancelar btn_default" />
            <input type="button" name="cancelar" value="Dejar de recibir el informe en mi casilla de correo" class="abandonar btn_default" />
            <span id="mensaje_configurar_informe"></span>
        </td>
    </tr>
</table>

<script type="text/javascript">
    $(document).ready(function(){
        $('#frecuencia').change(function(){
            if( $(this).find(':selected').val() == 'semanal'){
                $('#dia_de_la_semana').css('display', 'block');
                $('#dia_del_mes').css('display', 'none');
            }else if( $(this).find(':selected').val() == 'mensual'){
                $('#dia_de_la_semana').css('display', 'none');
                $('#dia_del_mes').css('display', 'block');
            }else{
                $('#dia_de_la_semana').css('display', 'none');
                $('#dia_del_mes').css('display', 'none');
            }
        });
        
        for(var dia = 1; dia <= 28; dia++){
            $('#select_dia_del_mes').append('<option value="' + dia + '">' + dia + '</option>');
        }
        $('#select_dia_del_mes').append('<option value="ultimo">Último día</option>');
        
        $('.cancelar').click(function(){
            parent.$.fancybox.close();
            return false;
        });
        
        $('.abandonar').click(function(){            
            $('#mensaje_configurar_informe').load('/welcome/abandonar_informe_correo/', function(){
                setTimeout(function(){
                    parent.$.fancybox.close();
                    return false;
                }, 2000);
            });
        });
        
        $('.aceptar').click(function(){
            // CONTROLES DEL MODAL
            var remitente = $('input[name="remitente"]').val();
            var frecuencia = $('#frecuencia').find(':selected').val();
            
            if(frecuencia == 'semanal'){
                var dia = $('#dia_de_la_semana').find(':selected').val();
            }else if(frecuencia == 'mensual'){
                var dia = $('#dia_del_mes').find(':selected').val();
            }else{
                var dia = '';    
            }
            
            var id_adserver = $("#id_adserver").val();
            //var por_sitio = $("#por_sitio").val();        
            
            // CONTROLES DEL FILTRO
            var anunciante_id = $('#cmb_anunciantes').val();
            var intervalo = $('#cmb_interval').val();
            var rango = $('#cmb_range').val();
            if(rango == 'especific'){
                alert('No puede configurar la recepción de reportes por fechas especificas.');
                return false;
            }
            
            if(id_adserver == "1"){
                var columnas = "";
                $("input[name='chk_columnas[]']:checked").each(function(){
                    columnas = columnas + $(this).val() + ";";
                });

                if(columnas==""){
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
                $("#cmb_paises_agregados option").each(function(){
                    filtros_paises = filtros_paises + $(this).attr('value') + ";";
                });

                var grupos = "";
                $("input[name='chk_grupos[]']:checked").each(function(){
                    grupos = grupos + $(this).val() + ";";
                });

                var orden = "";
                $("input[name='chk_columnas[]']:checked").each(function(){
                    orden = $(this).val();
                    return false;
                });

                $("input[name='chk_grupos[]']:checked").each(function(){
                    orden = $(this).val();
                    return false;
                });

                /*rango = fixedEncodeURIComponent(rango);
                filtros_li = fixedEncodeURIComponent(filtros_li);
                filtros_cr = fixedEncodeURIComponent(filtros_cr);
                filtros_sizes = fixedEncodeURIComponent(filtros_sizes);
                filtros_paises = fixedEncodeURIComponent(filtros_paises);
                grupos = fixedEncodeURIComponent(grupos);
                columnas = fixedEncodeURIComponent(columnas);*/

                if(filtros_li=="") filtros_li = 0;
                if(filtros_cr=="") filtros_cr = 0;
                if(filtros_sizes=="") filtros_sizes = 0;
                if(filtros_paises=="") filtros_paises = 0;
                if(grupos=="") grupos = 0;
                
                var form_data = {
                    remitente: remitente,
                    frecuencia: frecuencia,
                    dia: dia,
                    id_adserver: id_adserver,
                    anunciante_id: anunciante_id,
                    intervalo: intervalo,
                    rango: rango,
                    metricas: columnas,
                    filtros_li: filtros_li,
                    filtros_cr: filtros_cr,
                    filtros_sizes: filtros_sizes,
                    filtros_paises: filtros_paises,
                    grupos: grupos
                };
                
                            
            }else if(id_adserver == "2"){
                var filtros_paises = "";
                $("#cmb_paises_agregados option").each(function(){
                    filtros_paises = filtros_paises + $(this).attr('value') + ";";
                });

                var columnas = "";
                $("input[name='chk_columnas[]']:checked").each(function(){
                    columnas = columnas + $(this).val() + ";";
                });

                if(columnas==""){
                    alert("Debe elegir al menos una columna");
                    return;
                }

                var grupos = "";
                $("input[name='chk_grupos[]']:checked").each(function(){
                    grupos = grupos + $(this).val() + ";";
                });
                
                /*rango = fixedEncodeURIComponent(rango);
                filtros_paises = fixedEncodeURIComponent(filtros_paises);
                grupos = fixedEncodeURIComponent(grupos);
                columnas = fixedEncodeURIComponent(columnas);*/             

                if(filtros_paises=="") filtros_paises = 0;
                if(grupos=="") grupos = 0;
                
                
                var form_data = {
                    remitente: remitente,
                    frecuencia: frecuencia,
                    dia: dia,
                    id_adserver: id_adserver,
                    anunciante_id: anunciante_id,
                    intervalo: intervalo,
                    rango: rango,
                    metricas: columnas,
                    filtros_paises: filtros_paises,
                    grupos: grupos
                };
            }

            $.ajax({
                type: "POST",
                url: '/welcome/configurar_informe_correo/',
                data: form_data,
                dataType: "json",
                success: function(){
                    setTimeout(function(){
                        parent.$.fancybox.close();
                        return false;
                    }, 2000);                    
                }
            });
        });
    });
</script>