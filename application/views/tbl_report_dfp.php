<?php
if (!isset($columnas)) {
    ?>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#error_en_reporte_<?= $id_orden ?>').fadeIn(500).delay(3000).fadeOut(500);
        });
    </script>
    <?php
}
?>


<?php
if (sizeof($reporte) > 0) {
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
        </tr>
    </table>

    <?php
}
?>

<table class="display" id="tbl_report_<?= $id_orden ?>">
    <thead>
        <tr>
            <?php
            for ($i = 0; $i < sizeof($groupby); $i++) {
                ?>
                <th>
                    <?= $groupby[$i] ?>
                </th>
                <?php
            }
            ?>
            <?php
            for ($i = 0; $i < sizeof($columnas); $i++) {
                ?>
                <th>
                    <?= $columnas[$i] ?>
                </th>
                <?php
            }
            ?>
        </tr>
    </thead>
    <tbody>
        <?php
        for ($i = 0; $i < sizeof($reporte); $i++) {
            ?>
            <tr>
                <?php
                $fila = $reporte[$i];
                for ($j = 0; $j < sizeof($fila); $j++) {
                    ?>
                    <td>
                        <?php
                        $value = $fila[$j];
                        $value = str_replace('"', '', $value);
                        $value = str_replace(',', '.', $value);
                        echo $value;
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
            for($i=0;$i<sizeof($totales);$i++){
            ?>
                <th>
                    <?php
                    $value = $totales[$i];
                    $value = str_replace('"', '', $value);
                    $value = str_replace(',', '.', $value);
                    echo $value;
                    ?>
                </th>
            <?php
            }
            ?>
        </tr> 
</table>

<input type="hidden" id="cols_<?= $id_orden ?>" name="cols" value="<?= $texto_columnas ?>" />

<input type="hidden" id="total_imps_<?= $id_orden ?>" name="total_imps" value="<?= $total_imps ?>"/>
<input type="hidden" id="total_ctr_<?= $id_orden ?>" name="total_ctr" value="<?= $total_ctr ?>%" />
<input type="hidden" id="total_clicks_<?= $id_orden ?>" name="total_clicks" value="<?= $total_clicks ?>" />
<input type="hidden" id="total_views_<?= $id_orden ?>" name="total_views" value="<?= $total_views ?>" />
<input type="hidden" id="total_costo_<?= $id_orden ?>" name="total_costo" value="US$ <?= $total_revenue ?>" />

<script type="text/javascript">
    function fnGetSelected(oTableLocal)
    {
        return oTableLocal.$('tr.row_selected');
    }

    $(document).ready(function() {
        /*
         var ks = $('#cols_<?= $id_orden ?>').val().split(";");
         
         var columnas = new Array();
         
         for (i = 0; i < ks.length; i++) {
         var col = ks[i];
         if (col == "impresiones") {
         columnas[i] = {"sType": "slo"};
<?php if ($modalidad_de_compra == 'cpv') { ?>
             } else if (col == "total_views") {
             columnas[i] = {"sType": "slo"};
<?php } ?>
         } else if (col == "revenue" || col == "total_revenue") {
         columnas[i] = {"sType": "currency"};
         } else if (col == "clicks") {
         columnas[i] = {"sType": "slo"};
         } else if (col == "ctr") {
         columnas[i] = {"sType": "slo"};
         } else if (col == "lineItem_id") {
         columnas[i] = null;
         } else if (col == "lineItem_name") {
         columnas[i] = null;
         } else if (col == "creatividad_name") {
         columnas[i] = null;
         } else if (col == "pais_name") {
         columnas[i] = null;
         } else if (col == "fecha") {
         columnas[i] = {"sType": "uk_date"};
         } else if (col == "sitio_id") {
         columnas[i] = null;
         } else if (col == "sitio_name") {
         columnas[i] = null;
         } else if (col == "creatividad_tamano") {
         columnas[i] = null;
         }
         }
         */
        $('#tbl_report_<?= $id_orden ?>').dataTable({
            "bPaginate": false,
            "sPaginationType": "full_numbers",
            "aLengthMenu": [[10, 20, 50, 100, -1], [10, 20, 50, 100, "Todas"]],
            iDisplayLength: -1,
            "bInfo": false,
            "aaSorting": [[0, "desc"]],
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
            //"aoColumns": columnas,
            "sDom": "<'header'<'span8'l><'span8'f>r>t<'row'<'span8'i><'span8'p>>"
        });
    });
</script>

<script type="text/javascript">
    $(document).ready(function() {

        $(".exportarExcel_<?= $id_orden ?>").click(function() {

            $("#datos_a_enviar_<?= $id_orden ?>").val($("<div>").append($("#tbl_report_<?= $id_orden ?>").eq(0).clone()).html());
            //$("#nombre_anunciante_excel_<?= $id_orden ?>").val($("#cmb_anunciantes_<?= $id_orden ?>").find(':selected').html());
            //$("#nombre_orden_excel_<?= $id_orden ?>").val($("#cmb_ordenes_<?= $id_orden ?>").find(':selected').html());

            var rango = $("#cmb_range_<?= $id_orden ?>").find(':selected').val();
            $("#rango_excel_<?= $id_orden ?>").val(rango);

            if (rango == "especific") {
                var fecha_desde = $("#fecha_desde_<?= $id_orden ?>").val();
                var fecha_hasta = $("#fecha_hasta_<?= $id_orden ?>").val();
            } else {
                var fecha_desde = 0;
                var fecha_hasta = 0;
            }
            //alert(fecha_desde+' '+fecha_hasta);
            //return false;
            $("#fecha_inicio_excel_<?= $id_orden ?>").val(fecha_desde);
            $("#fecha_fin_excel_<?= $id_orden ?>").val(fecha_hasta);

            $("#total_imps_excel_<?= $id_orden ?>").val($('#total_imps_<?= $id_orden ?>').val());
            $("#total_clicks_excel_<?= $id_orden ?>").val($('#total_clicks_<?= $id_orden ?>').val());
            $("#total_views_excel_<?= $id_orden ?>").val($('#total_views_<?= $id_orden ?>').val());
            $("#total_ctr_excel_<?= $id_orden ?>").val($('#total_ctr_<?= $id_orden ?>').val());
            $("#total_costo_excel_<?= $id_orden ?>").val($('#total_costo_<?= $id_orden ?>').val());

            $("#FormularioExportacion_<?= $id_orden ?>").submit();
        });

        $(".exportarPDF_<?= $id_orden ?>").click(function() {

            $("#tabla_pdf_<?= $id_orden ?>").val($("<div>").append($("#tbl_report_<?= $id_orden ?>").eq(0).clone()).html());
            //$("#nombre_anunciante_<?= $id_orden ?>").val($("#cmb_anunciantes_<?= $id_orden ?>").find(':selected').html());
            //$("#nombre_orden_<?= $id_orden ?>").val($("#cmb_ordenes_<?= $id_orden ?>").find(':selected').html());

            var columnas_ids = "";

            $("input[name='chk_columnas_<?= $id_orden ?>[]']:checked").each(function() {
                columnas_ids = columnas_ids + $(this).val() + ";";
            });

            $("#columnas_<?= $id_orden ?>").val(columnas_ids);

            var grupo_ids = "";

            $("input[name='chk_grupos_<?= $id_orden ?>[]']:checked").each(function() {
                grupo_ids = grupo_ids + $(this).val() + ";";
            });

            $("#grupos_<?= $id_orden ?>").val(grupo_ids);

            var rango = $("#cmb_range_<?= $id_orden ?>").find(':selected').val();
            $("#rango_<?= $id_orden ?>").val(rango);

            if (rango == "especific") {
                var fecha_desde = $("#fecha_desde_<?= $id_orden ?>").val();
                var fecha_hasta = $("#fecha_hasta_<?= $id_orden ?>").val();
            } else {
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