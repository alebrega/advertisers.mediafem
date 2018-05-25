<?php
$arr_data = null;
$contador = 0;
$total_reg = 0;
$total_sitios_reales = 0;
$imps_ocultas_por_sitio = 0;
$clicks_ocultos_por_sitio = 0;
$convs_ocultas_por_sitio = 0;

$total_imps = 0;
$total_imps_ocultas = 0;
$total_imps_reales = 0;
$total_clicks = 0;
$total_clicks_ocultos = 0;
$total_clicks_reales = 0;
$total_cpm = 0;
$total_convs = 0;
$total_convs_ocultas = 0;
$total_convs_reales = 0;
$total_revenue = 0;

$promedio_imps = 0;
$promedio_clicks = 0;
$ctr_total = 0;

$posicion_imps = "";
$posicion_clicks = "";
$posicion_convs = "";
$posicion_site_id = "";

$porcentaje_imps = 0;
$porcentaje_clicks = 0;
$porcentaje_convs = 0;
/*
if (isset($report->data))
    $datos = $report->data;
else
    $datos = "";

//$datos = str_replace('"', '', $datos);
 *
 */

$rows = explode("\n", $datos);

for ($i = 1; $i < count($rows); $i++) {
    if (strlen($rows[$i]) > 0)
        $arr_data[] = $rows[$i];
}

$columnas = "";
for ($i = 0; $i < count($arr_columnas); $i++) {
    if ($arr_columnas[$i] != "site_id") {
        $columnas = $columnas . $arr_columnas[$i] . ";";
    }
    if ($arr_columnas[$i] == "imps") {
        $posicion_imps = $i;
    }
    if ($arr_columnas[$i] == "clicks") {
        $posicion_clicks = $i;
    }
    if ($arr_columnas[$i] == "total_convs") {
        $posicion_convs = $i;
    }
    if ($arr_columnas[$i] == "site_id") {
        $posicion_site_id = $i;
    }
}

$arr_sitios = null;

if (isset($arr_data)) {
    foreach ($arr_data as $c => $v) {
        $fields = explode(",", $v);
        $total_sitios_reales++;

        for ($i = 0; $i < count($arr_columnas); $i++) {
            $campo = $fields[$i];
            if ($arr_columnas[$i] == "site_name") {
                if (substr($campo, 0, 6) == "Hidden") {
                    if (strlen($posicion_imps)) {
                        $total_imps_ocultas = $total_imps_ocultas + $fields[$posicion_imps];
                    }
                    if (strlen($posicion_clicks)) {
                        $total_clicks_ocultos = $total_clicks_ocultos + $fields[$posicion_clicks];
                    }
                    if (strlen($posicion_convs)) {
                        $total_convs_ocultas = $total_convs_ocultas + $fields[$posicion_convs];
                    }
                    $total_sitios_reales--;
                } else {
                    if (in_array($fields[$posicion_site_id], $sitios_ocultos)) {
                        if (strlen($posicion_imps)) {
                            $total_imps_ocultas = $total_imps_ocultas + $fields[$posicion_imps];
                        }
                        if (strlen($posicion_clicks)) {
                            $total_clicks_ocultos = $total_clicks_ocultos + $fields[$posicion_clicks];
                        }
                        if (strlen($posicion_convs)) {
                            $total_convs_ocultas = $total_convs_ocultas + $fields[$posicion_convs];
                        }
                        $total_sitios_reales--;
                    } else {
                        $imps_reales = 0;
                        $clicks_reales = 0;
                        $convs_reales = 0;

                        if (strlen($posicion_imps)) {
                            $total_imps_reales = $total_imps_reales + $fields[$posicion_imps];
                            $imps_reales = $fields[$posicion_imps];
                        }
                        if (strlen($posicion_clicks)) {
                            $total_clicks_reales = $total_clicks_reales + $fields[$posicion_clicks];
                            $clicks_reales = $fields[$posicion_clicks];
                        }
                        if (strlen($posicion_convs)) {
                            $total_convs_reales = $total_convs_reales + $fields[$posicion_convs];
                            $convs_reales = $fields[$posicion_convs];
                        }

                        $arr_sitios[trim($fields[$posicion_site_id])] = array('imps' => $imps_reales, 'clicks' => $clicks_reales, 'convs' => $convs_reales);
                    }
                }
            }
        }
    }
}

$sitios = null;

foreach ($arr_sitios as $key => $value) {
    $imps = $value['imps'];
    $clicks = $value['clicks'];
    $convs = $value['convs'];

    $imps_ocultas = 0;
    $clicks_ocultos = 0;
    $convs_ocultas = 0;

    if (strlen($posicion_imps)) {
        if ($total_imps_reales)
            $porcentaje_imps = (($imps * 100) / $total_imps_reales);
        $imps_ocultas = ($porcentaje_imps * $total_imps_ocultas) / 100;
    }
    if (strlen($posicion_clicks)) {
        if ($total_clicks_reales)
            $porcentaje_clicks = (($clicks * 100) / $total_clicks_reales);
        $clicks_ocultos = ($porcentaje_clicks * $total_clicks_ocultos) / 100;
    }
    if (strlen($posicion_convs)) {
        if ($total_convs_reales)
            $porcentaje_convs = (($convs * 100) / $total_convs_reales);
        $convs_ocultas = ($porcentaje_convs * $total_convs_ocultas) / 100;
    }

    $sitios[trim($key)] = array('imps_ocultas' => $imps_ocultas, 'clicks_ocultos' => $clicks_ocultos, 'convs_ocultas' => $convs_ocultas);
}

if ($total_imps_ocultas)
    $imps_ocultas_por_sitio = ($total_imps_ocultas / $total_sitios_reales);

if ($total_clicks_ocultos)
    $clicks_ocultos_por_sitio = ($total_clicks_ocultos / $total_sitios_reales);

if ($total_convs_ocultas)
    $convs_ocultas_por_sitio = ($total_convs_ocultas / $total_sitios_reales);
/*
  echo "<br/><br/>";
  echo "Total Imps Reales: " . number_format($total_imps_reales, 0, ',', '.') . "<br/>";
  echo "Total impresiones ocultas : " . $total_imps_ocultas . "<br/>";
  echo "Impresiones ocultas por sitio : " . $imps_ocultas_por_sitio . "<br/>";

  echo "Total Clicks Reales: " . number_format($total_clicks_reales, 0, ',', '.') . "<br/>";
  echo "Total clicks ocultos : " . $total_clicks_ocultos . "<br/>";
  echo "Clicks ocultos por sitio : " . $clicks_ocultos_por_sitio . "<br/>";

  echo "Total Convs Reales: " . number_format($total_convs_reales, 0, ',', '.') . "<br/>";
  echo "Total convs ocultos : " . $total_convs_ocultas . "<br/>";
  echo "Convs ocultos por sitio : " . $convs_ocultas_por_sitio . "<br/>";
 */
if (strlen($posicion_imps))
    $total_imps = ($total_imps_reales + $total_imps_ocultas);
if (strlen($posicion_clicks))
    $total_clicks = ($total_clicks_reales + $total_clicks_ocultos);
if (strlen($posicion_convs))
    $total_convs = ($total_convs_reales + $total_convs_ocultas);
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
                    if ($arr_columnas[$j] != "site_id") {
                        $columna = $this->columnas->get_columna_by_id($arr_columnas[$j]);
                        ?>
                        <th><?= $columna->descripcion ?></th>
                        <?php
                    }
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
                $row_html = "";
                $mostrar = 1;

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
                        $revenue = $campo;
                        $campo = "US$ " . number_format($campo, 2, ',', '.');
                    } elseif ($arr_columnas[$i] == "total_revenue") {
                        $campo = "US$ " . number_format($campo, 2, ',', '.');
                    } elseif ($arr_columnas[$i] == "ctr") {
                        if (strlen($posicion_clicks) && strlen($posicion_imps)) {
                            if (array_key_exists(trim($fields[$posicion_site_id]), $sitios)) {
                                $clicks_ocultos_del_sitio = $sitios[trim($fields[$posicion_site_id])]['clicks_ocultos'];
                                $imps_ocultas_del_sitio = $sitios[trim($fields[$posicion_site_id])]['imps_ocultas'];
                                $campo = (($fields[$posicion_clicks] + $clicks_ocultos_del_sitio) / ($fields[$posicion_imps] + $imps_ocultas_del_sitio)) * 100;
                            }
                        } else {
                            $campo = ($campo * 100);
                        }
                        $campo = number_format($campo, 2, ',', '.') . "%";
                    } elseif ($arr_columnas[$i] == "clicks") {
                        if (array_key_exists(trim($fields[$posicion_site_id]), $sitios)) {
                            $campo = ($campo + $sitios[trim($fields[$posicion_site_id])]['clicks_ocultos']);
                        }
                        //$total_clicks = $total_clicks + $campo;
                        $campo = number_format($campo, 0, ',', '.');
                    } elseif ($arr_columnas[$i] == "imps") {
                        if (array_key_exists(trim($fields[$posicion_site_id]), $sitios)) {
                            $campo = ($campo + $sitios[trim($fields[$posicion_site_id])]['imps_ocultas']);
                        }
                        //$total_imps = ($total_imps + $campo);
                        $campo = number_format($campo, 0, ',', '.');
                    } elseif ($arr_columnas[$i] == "total_convs") {
                        if (array_key_exists(trim($fields[$posicion_site_id]), $sitios)) {
                            $campo = ($campo + $sitios[trim($fields[$posicion_site_id])]['convs_ocultas']);
                        }
                        $campo = number_format($campo, 0, ',', '.');
                    } elseif ($arr_columnas[$i] == "hour") {
                        $campo = ColumnHourToDate($campo);
                    } elseif ($arr_columnas[$i] == "day") {
                        $campo = ColumnDayToDate($campo);
                    } elseif ($arr_columnas[$i] == "month") {
                        $campo = ColumnMonthToDate($campo);
                    } elseif ($arr_columnas[$i] == "site_name") {
                        if (substr($campo, 0, 6) == "Hidden") {
                            $campo = "Sitio oculto";
                            $mostrar = 0;
                        } else {
                            if (in_array($fields[$posicion_site_id], $sitios_ocultos)) {
                                $campo = "Sitio oculto";
                                $mostrar = 0;
                            }
                        }
                    }
                    if ($arr_columnas[$i] != "site_id") {
                        $row_html.= "<td>" . $campo . "</td>";
                    }
                    if ($arr_columnas[$i] == "revenue" && $mostrar == 1) {
                        $total_revenue += $revenue;
                    }
                }
                if ($mostrar) {
                    echo "<tr>" . $row_html . "</tr>";
                }
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
                if ($arr_columnas[$i] != "site_id") {
                    echo "<td></td>";
                }
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
                    echo "<td>" . number_format($total_convs, 0, ',', '.') . "</td>";
                } elseif ($columna == "ctr") {
                    echo "<td>" . number_format($ctr_total, 2, ',', '.') . "%</td>";
                } elseif ($columna == "revenue") {
                    echo "<td>" . "US$ " . number_format($total_revenue, 2, ',', '.') . "</td>";
                } else {
                    if ($arr_columnas[$i] != "site_id") {
                        echo "<td></td>";
                    }
                }
            }
            ?>
        </tr>
    </tfoot>
</table>

<?php if(isset($total_imps)){ ?>
<input type="hidden" id="total_imps_<?= $lineItem ?>" name="total_imps" value="<?= number_format($total_imps, 0, ',', '.') ?>" />
<?php } ?>

<?php if(isset($total_clicks)){ ?>
<input type="hidden" id="total_clicks_<?= $lineItem ?>" name="total_clicks" value="<?= number_format($total_clicks, 0, ',', '.') ?>" />
<?php } ?>

<?php if(isset($total_conv)){ ?>
<input type="hidden" id="total_convs_<?= $lineItem ?>" name="total_convs" value="<?= number_format($total_conv, 0, ',', '.') ?>" />
<?php } ?>

<?php if(isset($ctr_total)){ ?>
<input type="hidden" id="total_ctr_<?= $lineItem ?>" name="total_ctr" value="<?= number_format($ctr_total, 2, ',', '.') . "%" ?>" />
<?php } ?>

<?php if(isset($total_costo)){ ?>
<input type="hidden" id="total_costo_<?= $lineItem ?>" name="total_costo" value="<?= "US$ " . number_format($total_costo, 2, ',', '.') ?>" />
<?php } ?>

<script type="text/javascript">
    $(document).ready(function(){
            /*
        $(".exportarExcel_<?= $lineItem ?>").click(function(){
            $("#datos_a_enviar_<?= $lineItem ?>").val( $("<div>").append( $("#tbl_report_<?= $lineItem ?>").eq(0).clone()).html());
            $("#nombre_anunciante_excel_<?= $lineItem ?>").val($("#name_anunciante_adserver_<?= $lineItem ?>").val());
            $("#nombre_orden_excel_<?= $lineItem ?>").val($("#name_orden_adserver_<?= $lineItem ?>").val());

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
        });*/
        
        $(".exportarExcel_<?= $lineItem ?>").click(function(){
            $("#datos_a_enviar_<?= $lineItem ?>").val( $("<div>").append( $("#tbl_report_<?= $lineItem ?>").eq(0).clone()).html());
            $("#nombre_anunciante_excel_<?= $lineItem ?>").val($("#name_anunciante_adserver_<?= $lineItem ?>").val());
            $("#nombre_orden_excel_<?= $lineItem ?>").val($("#name_orden_adserver_<?= $lineItem ?>").val());

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
            }else if(col=="geo_country_name"){
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
            "bJQueryUI": true,
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
/*
        $("#tbl_report_wrapper").css("width", "960px");

        $('#tbl_report tr').click( function() {
            $(this).toggleClass('row_selected');
        });
        $('.fg-toolbar').removeClass("ui-widget-header");
        */
    });
</script>
<?php
die();
?>
