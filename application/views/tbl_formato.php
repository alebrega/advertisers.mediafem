<?php
$total_imps = 0;

$imps_300x250 = 0;
$imps_728x90 = 0;
$imps_120x600 = 0;
$imps_468x60 = 0;
$imps_160x600 = 0;
$imps_richmedia = 0;
?>
<table>
    <tr>
        <td>
            <form action="/inventario/exportar_excel_formato" method="post" target="_blank" id="FormularioExportacion">
                <input type="hidden" id="datos_a_enviar" name="datos_a_enviar" />
                <input type="hidden" id="interval" name="interval" />
                <input type="hidden" id="texto_categoria" name="texto_categoria" />
                <input type="hidden" id="texto_pais" name="texto_pais" />
                <input type="submit" name="submit_export_excel" value="Exportar a Excel" class="exportarPorFormato button_new" />
            </form>
        </td>
    </tr>
</table>
<table class="display" id="tbl_report_sites" style="width: 100%">
    <thead>
        <tr>
            <th>Formato</th>
            <th>Impresiones</th>
        </tr>
    </thead>
    <tbody id="tbody_sites">
        <?php
        $datos = $report->data;
        $rows = explode("\n", $datos);

        for ($i = 1; $i < count($rows); $i++) {
            if (strlen($rows[$i]) > 0)
                $arr_data[] = $rows[$i];
        }
        $site_id_2 = "";
        if (isset($arr_data)) {
                
            $cant_formatos = 0;
           
            foreach ($arr_data as $c => $v) {
                $fields = explode(",", $v);
                
                $imps = $fields[2] * $this->multiplicacion_volumen;
                
                $total_imps = $total_imps + $imps;

                $size = trim($fields[5]);
                $site_id = $fields[1];
                
                if ($size == "300x250") {
                    $imps_300x250 += $imps;
                } elseif ($size == "728x90") {
                    $imps_728x90 = ($imps_728x90 + $imps);
                } elseif ($size == "120x600") {
                    $imps_120x600 = ($imps_120x600 + $imps);
                } elseif ($size == "468x60") {
                    $imps_468x60 = ($imps_468x60 + $imps);
                } elseif ($size == "160x600") {
                    $imps_160x600 = ($imps_160x600 + $imps);
                }
                
                if ($site_id == $site_id_2) {
                    $imps_sitio = $imps_sitio + $imps;
                    $cant_formatos++;
                } else {
                    if ($site_id_2 != "") {
                        $imps_richmedia = $imps_richmedia+($imps_sitio/$cant_formatos);
                        
                        $site_id_2 = $site_id;
                        $imps_sitio = 0;
                        $imps_sitio = $imps_sitio + $imps;
                        $cant_formatos = 0;
                        $cant_formatos++;
                    } else {
                        $site_id_2 = $site_id;
                        $imps_sitio = 0;
                        $imps_sitio = $imps_sitio + $imps;
                        $cant_formatos = 0;
                        $cant_formatos++;
                    }
                }
            }
            $total_imps = $total_imps+($imps_richmedia*2);
            ?>
            <tr>
                <td>300x250</td>
                <td><?= number_format($imps_300x250, 0, ',', '.') ?></td>
            </tr>
            <tr>
                <td>728x90</td>
                <td><?= number_format($imps_728x90, 0, ',', '.') ?></td>
            </tr>
            <tr>
                <td>120x600</td>
                <td><?= number_format($imps_120x600, 0, ',', '.') ?></td>
            </tr>
            <tr>
                <td>468x60</td>
                <td><?= number_format($imps_468x60, 0, ',', '.') ?></td>
            </tr>
            <tr>
                <td>160x600</td>
                <td><?= number_format($imps_160x600, 0, ',', '.') ?></td>
            </tr>
            <tr>
                <td>Layer</td>
                <td><?= number_format($imps_richmedia, 0, ',', '.') ?></td>
            </tr>
            <tr>
                <td>Skin</td>
                <td><?= number_format($imps_richmedia, 0, ',', '.') ?></td>
            </tr>
            <?php
        }
        ?>
    </tbody>
    <tfoot style="color: #000; font-weight: bold;">
        <tr style="background-color: #E5E5E5;border: 1px solid #CCC;font-size: 15px;">
            <td>Totales:</td>
            <td><?= number_format($total_imps, 0, ',', '.') ?></td>
        </tr>
    </tfoot>
</table>
<script type="text/javascript">
    jQuery.fn.dataTableExt.oSort['uk_date-asc']  = function(a,b) {
        var ukDatea = a.split('/');
        var ukDateb = b.split('/');

        var x = (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
        var y = (ukDateb[2] + ukDateb[1] + ukDateb[0]) * 1;

        return ((x < y) ? -1 : ((x > y) ?  1 : 0));
    };

    jQuery.fn.dataTableExt.oSort['uk_date-desc'] = function(a,b) {
        var ukDatea = a.split('/');
        var ukDateb = b.split('/');

        var x = (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
        var y = (ukDateb[2] + ukDateb[1] + ukDateb[0]) * 1;

        return ((x < y) ? 1 : ((x > y) ?  -1 : 0));
    };
    jQuery.fn.dataTableExt.oSort['slo-asc'] = function(a,b) {
        var x = (a == "-") ? 0 : a.replace( /\./g, "" ).replace( /,/, "." );
        var y = (b == "-") ? 0 : b.replace( /\./g, "" ).replace( /,/, "." );
        x = parseFloat( x );
        y = parseFloat( y );
        return ((x < y) ? -1 : ((x > y) ? 1 : 0));
    };

    jQuery.fn.dataTableExt.oSort['slo-desc'] = function(a,b) {
        var x = (a == "-") ? 0 : a.replace( /\./g, "" ).replace( /,/, "." );
        var y = (b == "-") ? 0 : b.replace( /\./g, "" ).replace( /,/, "." );
        x = parseFloat( x );
        y = parseFloat( y );
        return ((x < y) ? 1 : ((x > y) ? -1 : 0));
    };
    $(document).ready(function(){

        $(".exportarPorFormato").click(function(){
            $("#datos_a_enviar").val( $("<div>").append( $("#tbl_report_sites").eq(0).clone()).html());
            $("#interval").val($("#cmb_rango").find(':selected').val());

            var texto_canales_tematicos = "";
            $("#cmb_canales_tematicos_2 option").each(function(){
                texto_canales_tematicos = texto_canales_tematicos + $(this).html() + ", ";
            });

            var texto_paises = "";
            $("#cmb_paises_2 option").each(function(){
                texto_paises = texto_paises + $(this).html() + ", ";
            });

            if(texto_canales_tematicos == "")
                texto_canales_tematicos = "Todos";

            if(texto_paises == "")
                texto_paises = "Todos";

            $("#texto_categoria").val(texto_canales_tematicos);
            $("#texto_pais").val(texto_paises);

            $("#FormularioExportacion").submit();
        });

        var oTable = $('#tbl_report_sites').dataTable( {
            "sPaginationType": "full_numbers",
            "aLengthMenu": [[10, 20, 50, 100, -1], [10, 20, 50, 100, "Todos"]],
            iDisplayLength: -1,
            "bJQueryUI": true,
            "aaSorting": [[ 2, "asc" ]],
            "bInfo": false,
            //"sScrollY": 400,
            //"sScrollX": "25%",
            //"bScrollCollapse": true,
            "aoColumns": [
                null,
                { "sType": "slo" }
            ],
            "oLanguage": {
                "sSearch": "Buscar"
            }
            //"sDom": '<"H">rt<"F"flp>'
        });

        $('#tbl_report_sites tr').click( function() {
            $(this).toggleClass('row_selected');
        } );
    });
</script>
