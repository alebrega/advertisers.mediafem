<?php
$ingresos_total = 0;
$arr_data = null;
$contador = 0;
$total_reg = 0;

$total_imps = 0;
$total_clicks = 0;
$total_cpm = 0;
$total_conv = 0;

$promedio_imps = 0;
$promedio_clicks = 0;
$ctr_total = 0;

$cols = "";

for ($i = 0; $i < count($columnas); $i++) {
    $cols = $cols . $columnas[$i] . ";";
}
$cols = $cols . "geo_country_name;";
?>
<input type="hidden" id="cols" name="cols" value="<?= $cols ?>"/>
<?php
if ($reporte) {
    ?>
    <table>
        <tr>
            <td>
                <form action="/welcome/export_excel" method="post" target="_blank" id="FormularioExportacion">
                    <input type="hidden" id="datos_a_enviar" name="datos_a_enviar" />
                    <input type="hidden" id="nombre_anunciante_excel" name="nombre_anunciante_excel" />
                    <input type="hidden" id="rango_excel" name="rango_excel" />
                    <input type="hidden" id="grupos_excel" name="grupos_excel" />
                    <input type="hidden" id="columnas_excel" name="columnas_excel" />
                    <input type="hidden" id="fecha_inicio_excel" name="fecha_inicio_excel" />
                    <input type="hidden" id="fecha_fin_excel" name="fecha_fin_excel" />
                    <?php
                    for ($j = 0; $j < count($columnas); $j++) {
                        if (!empty($columnas[$j])) {
                            ?>
                            <input type="hidden" id="agrupar_por" name="agrupar_por" value="<?= $columnas[$j] ?>"/>
                            <?php
                        }
                        break;
                    }
                    ?>
                    <input type="submit" name="submit_export_excel" value="Exportar a Excel" class="exportarPorSitio button_new" />
                </form>
            </td>
            <td>
                <form action="/welcome/create_pdf" method="post" target="_blank" id="FormularioExportacionPDF">
                    <input type="hidden" id="tabla_pdf" name="tabla_pdf" />
                    <input type="hidden" id="nombre_anunciante" name="nombre_anunciante" />
                    <input type="hidden" id="rango" name="rango" />
                    <input type="hidden" id="grupos" name="grupos" />
                    <input type="hidden" id="columnas" name="columnas" />
                    <input type="hidden" id="fecha_inicio" name="fecha_inicio" />
                    <input type="hidden" id="fecha_fin" name="fecha_fin" />
                    <input type="hidden" id="id_adserver" name="id_adserver" value="2" />
                    <input type="hidden" id="total_imps_pdf" name="total_imps_pdf" value="" />
                    <input type="hidden" id="total_clicks_pdf" name="total_clicks_pdf" value="" />
                    <input type="hidden" id="total_convs_pdf" name="total_convs_pdf" value="" />
                    <input type="hidden" id="total_ctr_pdf" name="total_ctr_pdf" value="" />
                    <input type="hidden" id="total_costo_pdf" name="total_costo_pdf" value="" />
                    <input type="submit" name="submit_export_excel" value="Exportar a PDF" class="exportarPDF button_new" />
                </form>
            </td>
        </tr>
    </table>
    <?php
}
?>
<table class="display" id="tbl_report" style="width: 100%">
    <thead>
        <tr>
            <?php
            for ($j = 0; $j < count($columnas); $j++) {
                if (!empty($columnas[$j])) {
                    $columna = $this->columnas->get_columna_by_id($columnas[$j]);
                    ?>
                    <th><?= $columna->descripcion ?></th>
                    <?php
                }
            }
            ?>
            <th>Pais</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($reporte as $row) {
            $total_reg++;

            echo "<tr>";

            $pais = $row['pais'];
            $imps = $row['imps'];
            $clicks = $row['clicks'];
            $ctr = $row['ctr'];

            $total_imps = ($total_imps + str_replace(".", "", $imps));
            $total_clicks = ($total_clicks + str_replace(".", "", $clicks));

            for ($i = 0; $i < count($columnas); $i++) {
                $columna = $columnas[$i];

                if ($columna == "imps") {
                    echo "<td>$imps</td>";
                } elseif ($columna == "clicks") {
                    echo "<td>$clicks</td>";
                } elseif ($columna == "ctr") {
                    echo "<td>$ctr</td>";
                }
            }
            echo "<td>$pais</td>";
            echo "</tr>";
        }

        if ($total_imps) {
            $promedio_imps = ($total_imps / $total_reg);
        } else {
            $promedio_imps = "-";
        }

        if ($total_clicks) {
            $promedio_clicks = ($total_clicks / $total_reg);
        } else {
            $promedio_clicks = "-";
        }

        if ($total_clicks == 0 && $total_imps == 0) {
            $ctr_total = 0;
        } else {
            $ctr_total = (($total_clicks / $total_imps) * 100);
        }
        ?>
    </tbody>

    <tfoot  style="color: #000; font-weight: bold;">
        <tr style="background-color: #E5E5E5;border: 1px solid #CCC;font-size: 15px">
            <?php
            for ($i = 0; $i < count($columnas); $i++) {
                $columna = $columnas[$i];
                if ($columna == "imps") {
                    echo "<td>" . number_format($total_imps, 0, ',', '.') . "</td>";
                } elseif ($columna == "clicks") {
                    echo "<td>" . number_format($total_clicks, 0, ',', '.') . "</td>";
                } elseif ($columna == "ctr") {
                    echo "<td>" . number_format($ctr_total, 2, ',', '.') . "%</td>";
                }
            }
            ?>
            <td></td>
        </tr>
    </tfoot>
</table>

<input type="hidden" id="total_imps" name="total_imps_pdf" value="<?= number_format($total_imps, 0, ',', '.') ?>" />
<input type="hidden" id="total_ctr" name="total_ctr_pdf" value="<?= number_format($ctr_total, 2, ',', '.') ?>%" />
<input type="hidden" id="total_clicks" name="total_clicks_pdf" value="<?= number_format($total_clicks, 0, ',', '.') ?>" />
<input type="hidden" id="total_costo" name="total_costo_pdf" value="US$ 0" />
<input type="hidden" id="total_costo" name="total_convs_pdf" value="US$ 0" />

<script type="text/javascript">
    $(document).ready(function(){
        $(".exportarPorSitio").click(function(){
            $("#datos_a_enviar").val( $("<div>").append( $("#tbl_report").eq(0).clone()).html());
            $("#nombre_anunciante_excel").val($("#cmb_anunciantes").find(':selected').html());

            var rango = $("#cmb_range").find(':selected').val();
            $("#rango_excel").val(rango);

            if(rango=="especific"){
                var fecha_desde = $("#fecha_desde").val();
                var fecha_hasta = $("#fecha_hasta").val();
            }else{
                var fecha_desde = 0;
                var fecha_hasta = 0;
            }

            $("#fecha_inicio_excel").val(fecha_desde);
            $("#fecha_fin_excel").val(fecha_hasta);
            $("#FormularioExportacion").submit();
        });

        $(".exportarPDF").click(function(){
            $("#tabla_pdf").val( $("<div>").append( $("#tbl_report").eq(0).clone()).html());
            $("#nombre_anunciante").val($("#cmb_anunciantes").find(':selected').html());
            $("#grupos").val("geo_country_name;");

            var columnas_ids = "";

            $("input[name='chk_columnas[]']:checked").each(function(){
                columnas_ids = columnas_ids + $(this).val() + ";";
            });

            $("#columnas").val(columnas_ids);

            var rango = $("#cmb_range").find(':selected').val();
            $("#rango").val(rango);

            if(rango=="especific"){
                var fecha_desde = $("#fecha_desde").val();
                var fecha_hasta = $("#fecha_hasta").val();
            }else{
                var fecha_desde = 0;
                var fecha_hasta = 0;
            }

            $("#fecha_inicio").val(fecha_desde);
            $("#fecha_fin").val(fecha_hasta);

            $("#total_imps_pdf").val($('#total_imps').val());
            $("#total_clicks_pdf").val($('#total_clicks').val());
            $("#total_ctr_pdf").val($('#total_ctr').val());
            $("#total_costo_pdf").val($('#total_costo').val());

            $("#FormularioExportacionPDF").submit();
        });
    });
</script>

<script type="text/javascript">
    function fnGetSelected( oTableLocal )
    {
        return oTableLocal.$('tr.row_selected');
    }

    $(document).ready(function(){

        var ks = $('#cols').val().split(";");

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

        $('#tbl_report').dataTable({
            "bPaginate": false,
            "sPaginationType": "full_numbers",
            "bJQueryUI": true,
            "aLengthMenu": [[10, 20, 50, 100, -1], [10, 20, 50, 100, "Todas"]],
            iDisplayLength: -1,
            "bInfo": false,
            "aaSorting": [[ 0, "desc" ]],
            //"bFilter":false,
            //"sDom": '<"H">rt<"F"flp>',
            "aoColumns": columnas/*,
            "sScrollY": 400,
            "bScrollCollapse": true*/
        });

        $('.fg-toolbar').removeClass("ui-widget-header");

        $('#tbl_report tr').click( function() {
            $(this).toggleClass('row_selected');
        } );
    });
</script>