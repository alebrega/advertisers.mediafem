<?php
$arr_data = null;
$total_reg = 0;

$total_imps = 0;
$total_clicks = 0;
$total_cpm = 0;
$total_conv = 0;
$total_costo = 0;
$promedio_imps = 0;
$promedio_clicks = 0;
$ctr_total = 0;

//$datos = $report->data;
//$datos = str_replace('"', '', $datos);
$rows = explode("\n", $datos);

for ($i = 1; $i < count($rows); $i++) {
    if (strlen($rows[$i]) > 0)
        $arr_data[] = $rows[$i];
}

$columnas = "";
for ($i = 0; $i < count($arr_columnas); $i++) {
    $columnas = $columnas . $arr_columnas[$i] . ";";
}
?>

<input type="hidden" id="cols_<?= $lineItem ?>" name="cols" value="<?= $columnas ?>"/>

<?php
if (count($arr_data)) {
    ?>
    <table style="margin-bottom: 20px">
        <tr>
            <td style="padding-right: 10px;">
                <form action="/welcome/export_excel_appnexus" method="post" target="_blank" id="FormularioExportacion_<?= $lineItem ?>">
                    <input type="hidden" id="datos_a_enviar_<?= $lineItem ?>" name="datos_a_enviar_<?= $lineItem ?>" value="" />
                    <input type="hidden" id="nombre_anunciante_excel_<?= $lineItem ?>" name="nombre_anunciante_excel_<?= $lineItem ?>" value="" />
                    <input type="hidden" id="nombre_orden_excel_<?= $lineItem ?>" name="nombre_orden_excel_<?= $lineItem ?>" value="" />
                    <input type="hidden" id="rango_excel_<?= $lineItem ?>" name="rango_excel_<?= $lineItem ?>" value="" />
                    <input type="hidden" id="grupos_excel_<?= $lineItem ?>" name="grupos_excel_<?= $lineItem ?>" value="" />
                    <input type="hidden" id="columnas_excel_<?= $lineItem ?>" name="columnas_excel_<?= $lineItem ?>" value="" />
                    <input type="hidden" id="fecha_inicio_excel_<?= $lineItem ?>" name="fecha_inicio_excel_<?= $lineItem ?>" value="" />
                    <input type="hidden" id="fecha_fin_excel_<?= $lineItem ?>" name="fecha_fin_excel_<?= $lineItem ?>" value="" />
                    <input type="hidden" id="total_imps_excel_<?= $lineItem ?>" name="total_imps_excel_<?= $lineItem ?>" value="" />
                    <input type="hidden" id="total_clicks_excel_<?= $lineItem ?>" name="total_clicks_excel_<?= $lineItem ?>" value="" />
                    <input type="hidden" id="total_convs_excel_<?= $lineItem ?>" name="total_convs_excel_<?= $lineItem ?>" value="" />
                    <input type="hidden" id="total_ctr_excel_<?= $lineItem ?>" name="total_ctr_excel_<?= $lineItem ?>" value="" />
                    <input type="hidden" id="total_costo_excel_<?= $lineItem ?>" name="total_costo_excel_<?= $lineItem ?>" value="" />
                    <input type="hidden" id="id_lineItem" name="id_lineItem" value="<?= $lineItem ?>" />
                    <input type="hidden" id="empresa_campania_orden_<?= $lineItem ?>" name="empresa_campania_orden_<?= $lineItem ?>" value="" />
                    <input type="submit" name="submit_export_excel_<?= $lineItem ?>" value="Exportar a Excel" class="exportarExcel_<?= $lineItem ?> button_new" />
                </form>
            </td>
            <td style="padding-right: 10px;">
                <form action="/welcome/create_pdf" method="post" target="_blank" id="FormularioExportacionPDF_<?= $lineItem ?>">
                    <input type="hidden" id="tabla_pdf_<?= $lineItem ?>" name="tabla_pdf_<?= $lineItem ?>" value="" />
                    <input type="hidden" id="nombre_anunciante_<?= $lineItem ?>" name="nombre_anunciante_<?= $lineItem ?>" value="" />
                    <input type="hidden" id="nombre_orden_<?= $lineItem ?>" name="nombre_orden_<?= $lineItem ?>" value="" />
                    <input type="hidden" id="rango_<?= $lineItem ?>" name="rango_<?= $lineItem ?>" value="" />
                    <input type="hidden" id="grupos_<?= $lineItem ?>" name="grupos_<?= $lineItem ?>" value="" />
                    <input type="hidden" id="columnas_<?= $lineItem ?>" name="columnas_<?= $lineItem ?>" value="" />
                    <input type="hidden" id="fecha_inicio_<?= $lineItem ?>" name="fecha_inicio_<?= $lineItem ?>" value="" />
                    <input type="hidden" id="fecha_fin_<?= $lineItem ?>" name="fecha_fin_<?= $lineItem ?>" value="" />
                    <input type="hidden" id="id_adserver_<?= $lineItem ?>" name="id_adserver_<?= $lineItem ?>" value="1" />
                    <input type="hidden" id="total_imps_pdf_<?= $lineItem ?>" name="total_imps_pdf_<?= $lineItem ?>" value="" />
                    <input type="hidden" id="total_clicks_pdf_<?= $lineItem ?>" name="total_clicks_pdf_<?= $lineItem ?>" value="" />
                    <input type="hidden" id="total_convs_pdf_<?= $lineItem ?>" name="total_convs_pdf_<?= $lineItem ?>" value="" />
                    <input type="hidden" id="total_ctr_pdf_<?= $lineItem ?>" name="total_ctr_pdf_<?= $lineItem ?>" value="" />
                    <input type="hidden" id="total_costo_pdf_<?= $lineItem ?>" name="total_costo_pdf_<?= $lineItem ?>" value="" />
                    <input type="hidden" id="id_lineItem" name="id_lineItem" value="<?= $lineItem ?>" />
                    <input type="hidden" id="empresa_campania_orden_pdf_<?= $lineItem ?>" name="empresa_campania_orden_pdf_<?= $lineItem ?>" value="" />
                    <input type="submit" name="submit_export_excel_<?= $lineItem ?>" value="Exportar a PDF" class="exportarPDF_<?= $lineItem ?> button_new" />
                </form>
            </td>
        </tr>
    </table>
    <?php
}
?>

<table class="display" id="tbl_report_<?= $lineItem ?>">
    <thead>
        <tr>
            <?php
            for ($j = 0; $j < count($arr_columnas); $j++) {
                if (!empty($arr_columnas[$j])) {
                    $columna = $this->columnas->get_columna_by_id($arr_columnas[$j]);
                    ?>
                    <th><?= $columna->descripcion ?></th>
                    <?php
                }
            }
            ?>
        </tr>
    </thead>
    <tbody>
        <?php
        if (isset($arr_data)) {
            foreach ($arr_data as $c => $v) {
                $total_reg++;
                $fields = explode(",", $v);
                ?>
                <tr>
                    <?php
                    for ($i = 0; $i < count($arr_columnas); $i++) {

                        $campo = $fields[$i];

                        if ($arr_columnas[$i] == "ecpm") {
                            $cpm = $campo;
                            $total_cpm = ($total_cpm + $cpm);
                            $campo = "US$ " . number_format($campo, 2, ',', '.');
                        } elseif ($arr_columnas[$i] == "ecpc") {
                            $cpc = $campo;
                            $total_cpc = ($total_cpc + $cpc);
                            $campo = "US$" . number_format($campo, 2, ',', '.');
                        } elseif ($arr_columnas[$i] == "revenue") {
                            $campo = "US$ " . number_format($campo, 2, ',', '.');
                        } elseif ($arr_columnas[$i] == "total_revenue") {
                            $total_costo = ($total_costo + $campo);
                            $campo = "US$ " . number_format($campo, 2, ',', '.');
                        } elseif ($arr_columnas[$i] == "ctr") {
                            $campo = ($campo * 100);
                            $campo = number_format($campo, 2, ',', '.') . "%";
                        } elseif ($arr_columnas[$i] == "clicks") {
                            $total_clicks = ($total_clicks + $campo);
                            $campo = number_format($campo, 0, ',', '.');
                        } elseif ($arr_columnas[$i] == "imps") {
                            $total_imps = ($total_imps + $campo);
                            $campo = number_format($campo, 0, ',', '.');
                        } elseif ($arr_columnas[$i] == "total_convs") {
                            $total_conv = ($total_conv + $campo);
                            $campo = number_format($campo, 0, ',', '.');
                        } elseif ($arr_columnas[$i] == "hour") {
                            $campo = ColumnHourToDate($campo);
                        } elseif ($arr_columnas[$i] == "day") {
                            $campo = ColumnDayToDate($campo);
                        } elseif ($arr_columnas[$i] == "month") {
                            $campo = ColumnMonthToDate($campo);
                        } elseif ($arr_columnas[$i] == "creative_name") {
                            //$width_final = 25;
                        } elseif ($arr_columnas[$i] == "line_item_name") {
                            //$width_final = 15;
                        } elseif ($arr_columnas[$i] == "geo_country") {
                            //$campo = str_replace(",", "", $campo);
                            if (array_key_exists(trim($campo), $paises))
                                $campo = $paises[trim($campo)];
                            else
                                $campo = "Desconocido";
                        }

                        echo '<td>' . $campo . '</td>';
                    }
                    ?>
                </tr>
                <?php
            }
        }
        if ($total_imps)
            $promedio_imps = ($total_imps / $total_reg);

        if ($total_clicks)
            $promedio_clicks = ($total_clicks / $total_reg);

        if ($total_imps && $total_clicks)
            $ctr_total = ($total_clicks / $total_imps) * 100;
        ?>
    </tbody>

    <tfoot style="color: #000; font-weight: bold;">
        <tr style="background-color: #E5E5E5;border-left: 1px solid #CCC;border-right: 1px solid #CCC;
            border-top: 1px solid #CCC;height: 15px">
            <?php
            for ($i = 0; $i < count($arr_columnas); $i++) {
                echo "<td></td>";
            }
            ?>
        </tr>
        <tr style="background-color: #E5E5E5;border: 1px solid #CCC;font-size: 15px">
            <?php
            for ($i = 0; $i < count($arr_columnas); $i++) {
                $columna = $arr_columnas[$i];
                if ($columna == "imps") {
                    echo "<td>" . number_format($total_imps, 0, ',', '.') . "</td>";
                } elseif ($columna == "clicks") {
                    echo "<td>" . number_format($total_clicks, 0, ',', '.') . "</td>";
                } elseif ($columna == "total_convs") {
                    echo "<td>" . number_format($total_conv, 0, ',', '.') . "</td>";
                } elseif ($columna == "ctr") {
                    echo "<td>" . number_format($ctr_total, 2, ',', '.') . "%</td>";
                } elseif ($columna == "total_revenue") {
                    echo "<td>US$ " . number_format($total_costo, 2, ',', '.') . "</td>";
                } else {
                    echo "<td></td>";
                }
            }
            ?>
        </tr>
    </tfoot>
</table>

<input type="hidden" id="total_imps_<?= $lineItem ?>" name="total_imps" value="<?= number_format($total_imps, 0, ',', '.') ?>" />
<input type="hidden" id="total_clicks_<?= $lineItem ?>" name="total_clicks" value="<?= number_format($total_clicks, 0, ',', '.') ?>" />
<input type="hidden" id="total_convs_<?= $lineItem ?>" name="total_convs" value="<?= number_format($total_conv, 0, ',', '.') ?>" />
<input type="hidden" id="total_ctr_<?= $lineItem ?>" name="total_ctr" value="<?= number_format($ctr_total, 2, ',', '.') . "%" ?>" />
<input type="hidden" id="total_costo_<?= $lineItem ?>" name="total_costo" value="<?= "US$ " . number_format($total_costo, 2, ',', '.') ?>" />

<script type="text/javascript">
    $(document).ready(function(){
        $(".exportarExcel_<?= $lineItem ?>").click(function(){
            $("#datos_a_enviar_<?= $lineItem ?>").val( $("<div>").append( $("#tbl_report_<?= $lineItem ?>").eq(0).clone()).html());
            $("#nombre_anunciante_excel_<?= $lineItem ?>").val($("#name_anunciante_adserver_<?= $lineItem ?>").val());
            $("#nombre_orden_excel_<?= $lineItem ?>").val($("#name_orden_adserver_<?= $lineItem ?>").val());

            $("#empresa_campania_orden_<?= $lineItem ?>").val($("#empresa_campania_orden_home_<?= $lineItem ?>").val());

            var rango = $("#cmb_range_<?= $lineItem ?>").find(':selected').val();
            $("#rango_excel_<?= $lineItem ?>").val(rango);

            if(rango=="especific"){
                var fecha_desde = $("#fecha_desde_<?= $lineItem ?>").val();
                var fecha_hasta = $("#fecha_hasta_<?= $lineItem ?>").val();
            }else{
                var fecha_desde = 0;
                var fecha_hasta = 0;
            }

            $("#fecha_inicio_excel_<?= $lineItem ?>").val(fecha_desde);
            $("#fecha_fin_excel_<?= $lineItem ?>").val(fecha_hasta);

            $("#total_imps_excel_<?= $lineItem ?>").val($("#total_imps_<?= $lineItem ?>").val());
            $("#total_clicks_excel_<?= $lineItem ?>").val($("#total_clicks_<?= $lineItem ?>").val());
            $("#total_convs_excel_<?= $lineItem ?>").val($("#total_convs_<?= $lineItem ?>").val());
            $("#total_ctr_excel_<?= $lineItem ?>").val($("#total_ctr_<?= $lineItem ?>").val());
            $("#total_costo_excel_<?= $lineItem ?>").val($("#total_costo_<?= $lineItem ?>").val());

            $("#FormularioExportacion_<?= $lineItem ?>").submit();
        });

        $(".exportarPDF_<?= $lineItem ?>").click(function(){
            $("#tabla_pdf_<?= $lineItem ?>").val( $("<div>").append( $("#tbl_report_<?= $lineItem ?>").eq(0).clone()).html());
            $("#nombre_anunciante_<?= $lineItem ?>").val($("#name_anunciante_adserver_<?= $lineItem ?>").val());
            $("#nombre_orden_<?= $lineItem ?>").val($("#name_orden_adserver_<?= $lineItem ?>").val());

            $("#empresa_campania_orden_pdf_<?= $lineItem ?>").val($("#empresa_campania_orden_home_<?= $lineItem ?>").val());

            var columnas_ids = "";

            $("input[name='chk_columnas[]']:checked").each(function(){
                columnas_ids = columnas_ids + $(this).val() + ";";
            });

            $("#columnas_<?= $lineItem ?>").val(columnas_ids);

            var grupo_ids = "";

            $("input[name='chk_grupos[]']:checked").each(function(){
                grupo_ids = grupo_ids + $(this).val() + ";";
            });

            $("#grupos_<?= $lineItem ?>").val(grupo_ids);

            var rango = $("#cmb_range_<?= $lineItem ?>").find(':selected').val();
            $("#rango_<?= $lineItem ?>").val(rango);

            if(rango=="especific"){
                var fecha_desde = $("#fecha_desde_<?= $lineItem ?>").val();
                var fecha_hasta = $("#fecha_hasta_<?= $lineItem ?>").val();
            }else{
                var fecha_desde = 0;
                var fecha_hasta = 0;
            }

            $("#fecha_inicio_<?= $lineItem ?>").val(fecha_desde);
            $("#fecha_fin_<?= $lineItem ?>").val(fecha_hasta);

            $("#total_imps_pdf_<?= $lineItem ?>").val($("#total_imps_<?= $lineItem ?>").val());
            $("#total_clicks_pdf_<?= $lineItem ?>").val($("#total_clicks_<?= $lineItem ?>").val());
            $("#total_convs_pdf_<?= $lineItem ?>").val($("#total_convs_<?= $lineItem ?>").val());
            $("#total_ctr_pdf_<?= $lineItem ?>").val($("#total_ctr_<?= $lineItem ?>").val());
            $("#total_costo_pdf_<?= $lineItem ?>").val($("#total_costo_<?= $lineItem ?>").val());

            $("#FormularioExportacionPDF_<?= $lineItem ?>").submit();
        });
    });
</script>

<script type="text/javascript">
    function fnGetSelected( oTableLocal )
    {
        return oTableLocal.$('tr.row_selected');
    }

    $(document).ready(function(){

        var ks = $('#cols_<?= $lineItem ?>').val().split(";");

        var columnas=new Array();

        for(i=0;i<ks.length;i++){
            var col = ks[i];

            if(col=="imps"){
                columnas[i]= {"sType": "slo"};
            }else if(col=="total_revenue"){
                columnas[i]= {"sType": "currency"};
            }else if(col=="clicks"){
                columnas[i]= {"sType": "slo"};
            }else if(col=="ctr"){
                columnas[i]= {"sType": "slo"};
            }else if(col=="total_convs"){
                columnas[i]= {"sType": "slo"};
            }else if(col=="line_item"){
                columnas[i]= null;
            }else if(col=="line_item_name"){
                columnas[i]= null;
            }else if(col=="creative_name"){
                columnas[i]= null;
            }else if(col=="size"){
                columnas[i]= null;
            }else if(col=="geo_country"){
                columnas[i]= null;
            }else if(col=="hour"){
                columnas[i]= null;
            }else if(col=="day"){
                columnas[i]= {"sType": "uk_date"};
            }else if(col=="month"){
                columnas[i]= null;
            }else if(col=="site_id"){
                columnas[i]= null;
            }else if(col=="site_name"){
                columnas[i]= null;
            }else if(col=="revenue"){
                columnas[i]= {"sType": "currency"};
            }
        }

        $('#tbl_report_<?= $lineItem ?>').dataTable({
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
<?php
die();
?>
