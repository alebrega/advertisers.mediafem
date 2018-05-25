<?php
if (!isset($columnas)) {
    ?>
    <script type="text/javascript">
        $(document).ready(function(){
            $('#error_en_reporte_<?= $id_orden ?>').fadeIn(500).delay(3000).fadeOut(500);
        });
    </script>
    <?php
    die();
}

$texto_columnas = '';
$total_columnas = sizeof($columnas);
for ($a = 0; $a < $total_columnas; $a++) {
    if ($a == ($total_columnas - 1)) {
        $texto_columnas .= $columnas[$a][2];
    } else {
        $texto_columnas .= $columnas[$a][2] . ';';
    }
}

$total_imps = $total_clicks = $total_ctr = $total_revenue = $total_views = 0;
?>

<!-- GRAFICO REPORTES ************************************************************************* -->
<?php if(false){ ?>
    <style type="text/css">
        #highcharts-0{
            overflow: visible !important;
        }

        #grafico_<?= $id_orden ?>{
            margin-bottom: 30px;
        }
    </style>

    <?php
    if(sizeof($reporte[0]) > 12){
        $max = 12;
    }else{
        $max = sizeof($reporte[0]) - 1;
    }
    ?>

    <script type="text/javascript">
        $(function () {
            $('#grafico_<?= $id_orden ?>').highcharts({
                chart: {
                    marginTop: 50,
                    zoomType: 'xy'
                },
                title: {
                    text: '',
                    x: -20 //center
                },
                xAxis: {
                    categories: [<?= $graph_categories ?>],
                    min: 0,
                    max: <?= $max ?>
                },
                yAxis: {
                    title: {text: ''},
                    plotLines: [{
                        value: 0,
                        width: 1,
                        color: '#808080'
                    }]
                },
                tooltip: {
                    crosshairs: true,
                    valueSuffix: ''
                },
                legend: {
                    align: 'left',
                    verticalAlign: 'top',
                    floating: true,
                    x: 25,
                    y: 0,
                    borderWidth: 0
                },
                credits: {
                    enabled: true,
                    href: "https://www.mediafem.com",
                    text: "www.mediafem.com"
                },
                colors: ["#007FFF", "#00B200", "#FFC926"],
                series: [
                    <?php if($modalidad_de_compra == 'cpm'){ ?>
                    {
                    name: 'Impresiones',
                    data: [<?= $graph_imps ?>]
                    }
                    <?php } ?>

                    <?php if($modalidad_de_compra == 'cpc'){ ?>
                    {
                    name: 'Clicks',
                    data: [<?= $graph_clicks ?>]
                    }
                    <?php } ?>

                    <?php if($modalidad_de_compra == 'cpv'){ ?>
                    {
                    name: 'Vistas',
                    data: [<?= $graph_views ?>]
                    }
                    <?php } ?>
                    ],
                scrollbar: {
                    enabled: true,
                    barBackgroundColor: '#eee',
                    barBorderRadius: 2,
                    barBorderWidth: 0,
                    buttonBackgroundColor: '#eee',
                    buttonBorderWidth: 0,
                    buttonArrowColor: 'white',
                    buttonBorderRadius: 2,
                    rifleColor: 'white',
                    trackBackgroundColor: 'white',
                    trackBorderWidth: 1,
                    trackBorderColor: '#eee',
                    trackBorderRadius: 2
                }
            });
        });
    </script>

    <div id="grafico_<?= $id_orden ?>" style="height: 400px; min-width: 1125px;"></div>
<?php } ?>
<!-- TABLA REPORTES *************************************************************************** -->

<?php
if (sizeof($reporte[0]) > 0) {
    ?>
    <style type="text/css">
        #div_programar_reporte_<?= $id_orden ?>{
            border-bottom: 1px solid #ddd;
            border-top: 1px solid #ddd;
            display: none;
            margin: 5px 0;
            padding: 7px;
        }
    </style>

    <table style="margin-bottom: 20px">
        <tr>
            <td style="padding-right: 10px;">
                <form action="/welcome/export_excel_dfp" method="post" target="_blank" id="FormularioExportacion_<?= $id_orden ?>">
                    <input type="hidden" id="datos_a_enviar_<?= $id_orden ?>" name="datos_a_enviar" />
                    <input type="hidden" id="nombre_anunciante_excel_<?= $id_orden ?>" name="nombre_anunciante_excel" value="<?= $nombre_anunciante ?>" />
                    <input type="hidden" id="nombre_orden_excel_<?= $id_orden ?>" name="nombre_orden_excel" value="<?= $nombre_orden ?>" />
                    <input type="hidden" id="rango_excel_<?= $id_orden ?>" name="rango_excel" />
                    <input type="hidden" id="grupos_excel_<?= $id_orden ?>" name="grupos_excel" />
                    <input type="hidden" id="columnas_excel_<?= $id_orden ?>" name="columnas_excel" />
                    <input type="hidden" id="fecha_inicio_excel_<?= $id_orden ?>" name="fecha_inicio_excel" />
                    <input type="hidden" id="fecha_fin_exce_<?= $id_orden ?>l" name="fecha_fin_excel" />
                    <input type="hidden" id="total_imps_excel_<?= $id_orden ?>" name="total_imps_excel" value="" />
                    <input type="hidden" id="total_views_excel_<?= $id_orden ?>" name="total_views_excel" value="" />
                    <input type="hidden" id="total_clicks_excel_<?= $id_orden ?>" name="total_clicks_excel" value="" />
                    <input type="hidden" id="total_convs_excel_<?= $id_orden ?>" name="total_convs_excel" value="" />
                    <input type="hidden" id="total_ctr_excel_<?= $id_orden ?>" name="total_ctr_excel" value="" />
                    <input type="hidden" id="total_costo_excel_<?= $id_orden ?>" name="total_costo_excel" value="" />
                    <input type="submit" name="submit_export_excel" value="Exportar a Excel" class="exportarExcel_<?= $id_orden ?> button_new" />
                </form>
            </td>
            <td style="padding-right: 10px;">
                <form action="/welcome/create_pdf_dfp" method="post" target="_blank" id="FormularioExportacionPDF_<?= $id_orden ?>">
                    <input type="hidden" id="tabla_pdf_<?= $id_orden ?>" name="tabla_pdf" />
                    <input type="hidden" id="id_orden_pdf" name="id_orden_pdf" />
                    <input type="hidden" id="nombre_anunciante_<?= $id_orden ?>" name="nombre_anunciante" value="<?= $nombre_anunciante ?>" />
                    <input type="hidden" id="nombre_orden_<?= $id_orden ?>" name="nombre_orden" value="<?= $nombre_orden ?>" />
                    <input type="hidden" id="rango_<?= $id_orden ?>" name="rango" />
                    <input type="hidden" id="grupos_<?= $id_orden ?>" name="grupos" />
                    <input type="hidden" id="columnas_<?= $id_orden ?>" name="columnas" />
                    <input type="hidden" id="fecha_inicio_<?= $id_orden ?>" name="fecha_inicio" />
                    <input type="hidden" id="fecha_fin_<?= $id_orden ?>" name="fecha_fin" />
                    <input type="hidden" id="id_adserver_<?= $id_orden ?>" name="id_adserver" value="1" />
                    <input type="hidden" id="total_imps_pdf_<?= $id_orden ?>" name="total_imps_pdf" value="" />
                    <input type="hidden" id="total_clicks_pdf_<?= $id_orden ?>" name="total_clicks_pdf" value="" />
                    <input type="hidden" id="total_views_pdf_<?= $id_orden ?>" name="total_views_pdf" value="" />
                    <input type="hidden" id="total_convs_pdf_<?= $id_orden ?>" name="total_convs_pdf" value="" />
                    <input type="hidden" id="total_ctr_pdf_<?= $id_orden ?>" name="total_ctr_pdf" value="" />
                    <input type="hidden" id="total_costo_pdf_<?= $id_orden ?>" name="total_costo_pdf" value="" />
                    <input type="submit" name="submit_export_excel" value="Exportar a PDF" class="exportarPDF_<?= $id_orden ?> button_new" />
                </form>
            </td>
            <!--
            <td style="padding-right: 10px;">
                <form action="#" method="post">
                    <input type="button" name="programar_reporte_<?= $id_orden ?>" value="Programar este reporte" class="button_new" />
                </form>
            </td>
            -->
        </tr>
    </table>


    <div id="div_programar_reporte_<?= $id_orden ?>">
        <b>Programar reporte:</b>

        <select name="enviar_cada_<?= $id_orden ?>" class="combo" style="width:185px; margin-right: 15px;">
            <option value="diariamente">Diariamente</option>
            <option value="dia_de_la_semana">D&iacute;a de la semana</option>
            <option value="al_finalizar">Al finalizar la campa&ntilde;a</option>
        </select>

        <select name="dia_de_la_semana_<?= $id_orden ?>" class="combo" style="width:185px; margin: 0 15px 0 0; display: none;">
            <option value="Monday">Lunes</option>
            <option value="Tuesday">Martes</option>
            <option value="Wednesday">Mi&eacute;rcoles</option>
            <option value="Thursday">Jueves</option>
            <option value="Friday">Viernes</option>
            <option value="Saturday">S&aacute;bado</option>
            <option value="Sunday">Domingo</option>
        </select>

        <input type="button" name="save_programar_reporte_<?= $id_orden ?>" value="Aceptar" class="button_new" />
        <input type="button" name="cancel_programar_reporte_<?= $id_orden ?>" value="Cancelar" class="button_new" />
    </div>

    <?php
}
?>
<table class="display" id="tbl_report_<?= $id_orden ?>">
    <thead>
        <tr>
            <?php
            foreach ($columnas as $col) {
                if($col[1] == 'Vistas'){
                    if($modalidad_de_compra == 'cpv'){
                ?>
                    <th><?= $col[1] ?></th>
                <?php
                    }
                }else{
                ?>
                    <th><?= $col[1] ?></th>
                <?php
                }
            }
            ?>
        </tr>
    </thead>
    <tbody>
        <?php
            if(sizeof($reporte[0]) > 0) {
                foreach ($reporte[0] as $report) {
                ?>
                <tr>
                    <?php
                    foreach ($columnas as $col) {
                        if($col[2] == 'total_views'){
                            if($modalidad_de_compra == 'cpv'){
                        ?>
                        <td>
                            <?php
                            switch ($col[2]) {
                                case 'total_views':
                                    if ($usuario->notacion == 0) {
                                        $dato = number_format($report['vistas'], 0, '.', ',');
                                    } else if ($usuario->notacion == 1) {
                                        $dato = number_format($report['vistas'], 0, ',', '.');
                                    }
                                    echo $dato;
                                    break;
                                default:
                                    echo str_replace('*', '/', $report[$col[2]]);
                                    break;
                            }
                            ?>
                        </td>
                        <?php
                            }
                        }else{
                        ?>
                            <td>
                            <?php
                            switch ($col[2]) {
                                case 'impresiones':
                                    if ($usuario->notacion == 0) {
                                        $dato = number_format($report[$col[2]], 0, '.', ',');
                                    } else if ($usuario->notacion == 1) {
                                        $dato = number_format($report[$col[2]], 0, ',', '.');
                                    }
                                    echo $dato;
                                    break;
                                case 'clicks':
                                    if ($usuario->notacion == 0) {
                                        $dato = number_format($report[$col[2]], 0, '.', ',');
                                    } else if ($usuario->notacion == 1) {
                                        $dato = number_format($report[$col[2]], 0, ',', '.');
                                    }
                                    echo $dato;
                                    break;
                                case 'ctr':
                                    if ($usuario->notacion == 0) {
                                        $dato = number_format($report[$col[2]], 2, '.', ',');
                                    } else if ($usuario->notacion == 1) {
                                        $dato = number_format($report[$col[2]], 2, ',', '.');
                                    }
                                    echo $dato . '%';
                                    break;
                                case 'revenue':
                                    if ($usuario->notacion == 0) {
                                        $dato = number_format($report[$col[2]], 2, '.', ',');
                                    } else if ($usuario->notacion == 1) {
                                        $dato = number_format($report[$col[2]], 2, ',', '.');
                                    }
                                    echo $dato . ' ' . $this->user_data->moneda;
                                    break;
                                default:
                                    echo str_replace('*', '/', $report[$col[2]]);
                                    break;
                            }
                            ?>
                        </td>
                        <?php
                        }
                    }
                    ?>
                </tr>
                <?php
                }
            }else{
                ?>
                <tr>
                    <?php
                    foreach ($columnas as $col) {
                        ?>
                        <td>
                            <?php
                            switch ($col[2]) {
                                case 'impresiones':
                                    if ($usuario->notacion == 0) {
                                        $dato = number_format(0, 0, '.', ',');
                                    } else if ($usuario->notacion == 1) {
                                        $dato = number_format(0, 0, ',', '.');
                                    }
                                    echo $dato;
                                    break;
                                case 'total_views':
                                    if ($usuario->notacion == 0) {
                                        $dato = number_format(0, 0, '.', ',');
                                    } else if ($usuario->notacion == 1) {
                                        $dato = number_format(0, 0, ',', '.');
                                    }
                                    echo $dato;
                                    break;
                                case 'clicks':
                                    if ($usuario->notacion == 0) {
                                        $dato = number_format(0, 0, '.', ',');
                                    } else if ($usuario->notacion == 1) {
                                        $dato = number_format(0, 0, ',', '.');
                                    }
                                    echo $dato;
                                    break;
                                case 'ctr':
                                    if ($usuario->notacion == 0) {
                                        $dato = number_format(0, 2, '.', ',');
                                    } else if ($usuario->notacion == 1) {
                                        $dato = number_format(0, 2, ',', '.');
                                    }
                                    echo $dato . '%';
                                    break;
                                case 'revenue':
                                    if ($usuario->notacion == 0) {
                                        $dato = number_format(0, 2, '.', ',');
                                    } else if ($usuario->notacion == 1) {
                                        $dato = number_format(0, 2, ',', '.');
                                    }
                                    echo 'US$ ' . $dato;
                                    break;
                                default:
                                    echo $report[$col[2]];
                                    break;
                            }
                            ?>
                        </td>
                        <?php
                    }
                    ?>
                </tr>
                <?php
            }
        ?>
    </tbody>
    <tfoot style="color: #000; font-weight: bold;">
        <tr style="background-color: #E5E5E5;border: 1px solid #CCC;font-size: 15px">
            <?php
            foreach ($columnas as $col) {
                if ($col[0] == "imps") {
                    if ($usuario->notacion == 0) {
                        $total_imps = number_format($totales['imps'], 0, '.', ',');
                    } else if ($usuario->notacion == 1) {
                        $total_imps = number_format($totales['imps'], 0, ',', '.');
                    }
                    echo "<td>" . $total_imps . "</td>";

                } elseif ($col[0] == "clicks") {
                    if ($usuario->notacion == 0) {
                        $total_clicks = number_format($totales['clicks'], 0, '.', ',');
                    } else if ($usuario->notacion == 1) {
                        $total_clicks = number_format($totales['clicks'], 0, ',', '.');
                    }
                    echo "<td>" . $total_clicks . "</td>";

                } elseif ($col[0] == "ctr") {
                    if ($usuario->notacion == 0) {
                        $total_ctr = number_format($totales['ctr'], 2, '.', ',');
                    } else if ($usuario->notacion == 1) {
                        $total_ctr = number_format($totales['ctr'], 2, ',', '.');
                    }
                    echo "<td>" . $total_ctr . "%</td>";

                } elseif ($col[0] == "revenue" || $col[0] == 'total_revenue') {
                    if ($usuario->notacion == 0) {
                        $total_revenue = number_format($totales['revenue'], 2, '.', ',');
                    } else if ($usuario->notacion == 1) {
                        $total_revenue = number_format($totales['revenue'], 2, ',', '.');
                    }
                    echo "<td>US$ " . $total_revenue . "</td>";

                }elseif ($col[0] == "total_views") {
                    if ($usuario->notacion == 0) {
                        $total_views = number_format($totales['vistas'], 0, '.', ',');
                    } else if ($usuario->notacion == 1) {
                        $total_views = number_format($totales['vistas'], 0, ',', '.');
                    }

                    if($modalidad_de_compra == 'cpv')
                        echo "<td>" . $total_views . "</td>";

                } else {
                    if ($col[0] != "sitio_id") {
                        echo "<td>&nbsp;</td>";
                    }
                }
            }
            ?>
        </tr>
    </tfoot>
</table>

<input type="hidden" id="cols_<?= $id_orden ?>" name="cols" value="<?= $texto_columnas ?>" />

<input type="hidden" id="total_imps_<?= $id_orden ?>" name="total_imps" value="<?= $total_imps ?>"/>
<input type="hidden" id="total_ctr_<?= $id_orden ?>" name="total_ctr" value="<?= $total_ctr ?>%" />
<input type="hidden" id="total_clicks_<?= $id_orden ?>" name="total_clicks" value="<?= $total_clicks ?>" />
<input type="hidden" id="total_views_<?= $id_orden ?>" name="total_views" value="<?= $total_views ?>" />
<input type="hidden" id="total_costo_<?= $id_orden ?>" name="total_costo" value="US$ <?= $total_revenue ?>" />

<?php
die();
?>

<script type="text/javascript">
    function fnGetSelected( oTableLocal )
    {
        return oTableLocal.$('tr.row_selected');
    }

    $(document).ready(function(){

        var ks = $('#cols_<?= $id_orden ?>').val().split(";");

        var columnas=new Array();

        for(i=0;i<ks.length;i++){
            var col = ks[i];
            if(col=="impresiones"){
                columnas[i]= {"sType": "slo"};
            <?php if($modalidad_de_compra == 'cpv'){ ?>
            }else if(col=="total_views"){
                columnas[i]= {"sType": "slo"};
            <?php } ?>
            }else if(col=="revenue" || col=="total_revenue"){
                columnas[i]= {"sType": "currency"};
            }else if(col=="clicks"){
                columnas[i]= {"sType": "slo"};
            }else if(col=="ctr"){
                columnas[i]= {"sType": "slo"};
            }else if(col=="lineItem_id"){
                columnas[i]= null;
            }else if(col=="lineItem_name"){
                columnas[i]= null;
            }else if(col=="creatividad_name"){
                columnas[i]= null;
            }else if(col=="pais_name"){
                columnas[i]= null;
            }else if(col=="fecha"){
                columnas[i]= {"sType": "uk_date"};
            }else if(col=="sitio_id"){
                columnas[i]= null;
            }else if(col=="sitio_name"){
                columnas[i]= null;
            }else if(col=="creatividad_tamano"){
                columnas[i]= null;
            }
        }

        $('#tbl_report_<?= $id_orden ?>').dataTable({
            "bPaginate": false,
            "sPaginationType": "full_numbers",
            "aLengthMenu": [[10, 20, 50, 100, -1], [10, 20, 50, 100, "Todas"]],
            iDisplayLength: -1,
            "bInfo": false,
            "aaSorting": [[ 0, "desc" ]],
            "oLanguage": {
                "sZeroRecords": "No se encontraron resultados",
                "sSearch": "Buscar: ",
                "sLengthMenu": "Mostrar filas: _MENU_",
                "oPaginate": {
                    "sFirst": "<<",
                    "sLast": ">>",
                    "sNext": ">",
                    "sPrevious": "<"
                }
            },
            "aoColumns": columnas,
            "sDom": "<'header'<'span8'l><'span8'f>r>t<'row'<'span8'i><'span8'p>>"
        });
    });
</script>

<script type="text/javascript">
    $(document).ready(function(){
        $('select[name="enviar_cada_<?= $id_orden ?>"]').change(function(){
            if($(this).find(':selected').val() == 'dia_de_la_semana'){
                $('select[name="dia_de_la_semana_<?= $id_orden ?>"]').fadeIn(1000);
            }else{
                $('select[name="dia_de_la_semana_<?= $id_orden ?>"]').fadeOut(1000);
            }
        });

        $('input[name="programar_reporte_<?= $id_orden ?>"]').click(function(){
            $('#div_programar_reporte_<?= $id_orden ?>').fadeIn(1000);
        });

        $('input[name="save_programar_reporte_<?= $id_orden ?>"]').click(function(){

            $(this).val('Guardando...');

            $(this).attr('disabled');
            $('select[name="enviar_cada_<?= $id_orden ?>"]').attr('disabled');
            $('select[name="dia_de_la_semana_<?= $id_orden ?>"]').attr('disabled');
            $('input[name="cancel_programar_reporte_<?= $id_orden ?>"]').attr('disabled');

            var correo_electronico = '<?= $this->nombre_usuario ?>';

            var extension = 'xls';

            var id_adserver = $("#id_adserver_<?= $id_orden ?>").val();
            var por_sitio = $("#por_sitio_<?= $id_orden ?>").val();

            var now = new Date();
            var seconds = now.getSeconds()+"o"+now.getMinutes();

            if(id_adserver == '0'){
                var adv_id = $("#id_anunciante_adserver_<?= $id_orden ?>").val();
                var orden_id = $("#cmb_ordenes_<?= $id_orden ?>").val();
                var rango = $("#cmb_range_<?= $id_orden ?>").find(':selected').val();

                var intervalo = "cumulative";

                $("input[name='chk_intervalo_<?= $id_orden ?>[]']:checked").each(function(){
                    intervalo = $(this).val();
                });

                if(rango=="especific"){
                    var fecha_desde = $("#fecha_desde_<?= $id_orden ?>").val();
                    var fecha_hasta = $("#fecha_hasta_<?= $id_orden ?>").val();

                    if(fecha_desde=="" || fecha_hasta==""){
                        $("#loader_report_<?= $id_orden ?>").css("display", "none");
                        alert("Debe completar las 2 fechas");
                        return;
                    }
                }else{
                    var fecha_desde = 0;
                    var fecha_hasta = 0;
                }

                var columnas = "";

                $("input[name='chk_columnas_<?= $id_orden ?>[]']:checked").each(function(){
                    columnas = columnas + $(this).val() + ";";
                });

                if(columnas==""){
                    $("#loader_report_<?= $id_orden ?>").css("display", "none");
                    alert("Debe elegir al menos una columna");
                    return;
                }

                var filtros_li = "";

                $("#cmb_line_items_agregados_<?= $id_orden ?> option").each(function(){
                    filtros_li = filtros_li + $(this).attr('value') + ";";
                });

                var filtros_cr = "";

                $("#cmb_creatives_agregados_<?= $id_orden ?> option").each(function(){
                    filtros_cr = filtros_cr + $(this).attr('value') + ";";
                });

                var filtros_sizes = "";

                $("#cmb_sizes_agregados_<?= $id_orden ?> option").each(function(){
                    filtros_sizes = filtros_sizes + $(this).attr('value') + ";";
                });

                var filtros_paises = "";

                $("#cmb_paises_agregados_<?= $id_orden ?> option").each(function(){
                    filtros_paises = filtros_paises + $(this).attr('value') + ";";
                });

                var grupos = "";

                $("input[name='chk_grupos_<?= $id_orden ?>[]']:checked").each(function(){
                    grupos = grupos + $(this).val() + ";";
                });

                var orden = "";

                $("input[name='chk_columnas_<?= $id_orden ?>[]']:checked").each(function(){
                    orden = $(this).val();
                    return false;
                });

                $("input[name='chk_grupos_<?= $id_orden ?>[]']:checked").each(function(){
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
                enviar_cada :       $('select[name="enviar_cada_<?= $id_orden ?>"]').find(':selected').val(),
                dia_de_la_semana :  $('select[name="dia_de_la_semana_<?= $id_orden ?>"]').find(':selected').val(),
                correo_electronico : correo_electronico,
                extension : extension,
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
            /*
            alert($('select[name="enviar_cada"]').find(':selected').val() + '-' + $('select[name="dia_de_la_semana"]').find(':selected').val() + '-' + correo_electronico + '-' + extension + '-' + id_adserver + '-' + adv_id + '-' + orden_id + '-' +
                por_sitio + '-' + rango + '-' + filtros_li + '-' + filtros_cr + '-' + filtros_sizes + '-' + filtros_paises + '-' + grupos + '-' + columnas + '-' +
                intervalo + '-' + fecha_desde + '-' + fecha_hasta + '-' + seconds);
             */
            $.ajax({
                type: "POST",
                url: "/welcome/suscribir_reporte/",
                data: form_data,
                dataType: "json",
                success: function(msg){
                    if(msg.validate == true){
                        $('input[name="save_programar_reporte_<?= $id_orden ?>"]').attr('disabled', false);

                        $('input[name="save_programar_reporte_<?= $id_orden ?>"]').attr('value', 'Aceptar');

                        $('input[name="cancel_programar_reporte_<?= $id_orden ?>"]').click();

                        $('select[name="enviar_cada_<?= $id_orden ?>"]').attr('disabled', false);
                        $('select[name="dia_de_la_semana_<?= $id_orden ?>"]').attr('disabled', false);
                        $('input[name="cancel_programar_reporte_<?= $id_orden ?>"]').attr('disabled', false);
                    }
                }
            });
        });

        $('input[name="cancel_programar_reporte_<?= $id_orden ?>"]').click(function(){
            $('#div_programar_reporte_<?= $id_orden ?>').fadeOut(1000);
        });

        $(".exportarExcel_<?= $id_orden ?>").click(function(){

            $("#datos_a_enviar_<?= $id_orden ?>").val( $("<div>").append( $("#tbl_report_<?= $id_orden ?>").eq(0).clone()).html());
            //$("#nombre_anunciante_excel_<?= $id_orden ?>").val($("#cmb_anunciantes_<?= $id_orden ?>").find(':selected').html());
            //$("#nombre_orden_excel_<?= $id_orden ?>").val($("#cmb_ordenes_<?= $id_orden ?>").find(':selected').html());

            var rango = $("#cmb_range_<?= $id_orden ?>").find(':selected').val();
            $("#rango_excel_<?= $id_orden ?>").val(rango);

            if(rango=="especific"){
                var fecha_desde = $("#fecha_desde_<?= $id_orden ?>").val();
                var fecha_hasta = $("#fecha_hasta_<?= $id_orden ?>").val();
            }else{
                var fecha_desde = 0;
                var fecha_hasta = 0;
            }

            $("#fecha_inicio_excel_<?= $id_orden ?>").val(fecha_desde);
            $("#fecha_fin_excel_<?= $id_orden ?>").val(fecha_hasta);

            $("#total_imps_excel_<?= $id_orden ?>").val($('#total_imps_<?= $id_orden ?>').val());
            $("#total_clicks_excel_<?= $id_orden ?>").val($('#total_clicks_<?= $id_orden ?>').val());
            $("#total_views_excel_<?= $id_orden ?>").val($('#total_views_<?= $id_orden ?>').val());
            $("#total_ctr_excel_<?= $id_orden ?>").val($('#total_ctr_<?= $id_orden ?>').val());
            $("#total_costo_excel_<?= $id_orden ?>").val($('#total_costo_<?= $id_orden ?>').val());

            $("#FormularioExportacion_<?= $id_orden ?>").submit();
        });

        $(".exportarPDF_<?= $id_orden ?>").click(function(){

            $("#tabla_pdf_<?= $id_orden ?>").val( $("<div>").append( $("#tbl_report_<?= $id_orden ?>").eq(0).clone()).html());
            //$("#nombre_anunciante_<?= $id_orden ?>").val($("#cmb_anunciantes_<?= $id_orden ?>").find(':selected').html());
            //$("#nombre_orden_<?= $id_orden ?>").val($("#cmb_ordenes_<?= $id_orden ?>").find(':selected').html());

            var columnas_ids = "";

            $("input[name='chk_columnas_<?= $id_orden ?>[]']:checked").each(function(){
                columnas_ids = columnas_ids + $(this).val() + ";";
            });

            $("#columnas_<?= $id_orden ?>").val(columnas_ids);

            var grupo_ids = "";

            $("input[name='chk_grupos_<?= $id_orden ?>[]']:checked").each(function(){
                grupo_ids = grupo_ids + $(this).val() + ";";
            });

            $("#grupos_<?= $id_orden ?>").val(grupo_ids);

            var rango = $("#cmb_range_<?= $id_orden ?>").find(':selected').val();
            $("#rango_<?= $id_orden ?>").val(rango);

            if(rango=="especific"){
                var fecha_desde = $("#fecha_desde_<?= $id_orden ?>").val();
                var fecha_hasta = $("#fecha_hasta_<?= $id_orden ?>").val();
            }else{
                var fecha_desde = 0;
                var fecha_hasta = 0;
            }

            $("#fecha_inicio_<?= $id_orden ?>").val(fecha_desde);
            $("#fecha_fin_<?= $id_orden ?>").val(fecha_hasta);

            $("#total_imps_pdf_<?= $id_orden ?>").val($('#total_imps_<?= $id_orden ?>').val());
            $("#total_clicks_pdf_<?= $id_orden ?>").val($('#total_clicks_<?= $id_orden ?>').val());
            $("#total_ctr_pdf_<?= $id_orden ?>").val($('#total_ctr_<?= $id_orden ?>').val());
            $("#total_views_pdf_<?= $id_orden ?>").val($('#total_views_<?= $id_orden ?>').val());
            $("#total_costo_pdf_<?= $id_orden ?>").val($('#total_costo_<?= $id_orden ?>').val());

            $("#id_orden_pdf").val(<?= $id_orden ?>);

            $("#FormularioExportacionPDF_<?= $id_orden ?>").submit();
        });
    });
</script>
<?php
die();
?>