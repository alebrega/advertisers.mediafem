<?php
$total_reg = 0;
$total_imps = 0;
$total_clicks = 0;
$total_cpm = 0;
$total_ctr = 0;
?>
<table>
    <tr>
        <td>
            <form action="/inventario/exportar_excel_sitio_formato" method="post" target="_blank" id="FormularioExportacion">
                <input type="hidden" id="datos_a_enviar" name="datos_a_enviar" />
                <input type="hidden" id="interval" name="interval" />
                <input type="hidden" id="texto_categoria" name="texto_categoria" />
                <input type="hidden" id="texto_pais" name="texto_pais" />
                <input type="submit" name="submit_export_excel" value="Exportar a Excel" class="exportarPorSitioFormato button_new" />
            </form>
        </td>
    </tr>
</table>
<table class="display" id="tbl_report_sites" style="width: 100%">
    <thead>
        <tr>
            <th><b>Sitio Web</b></th>
            <th>Formato</th>
            <th>Canales Tematicos</th>
            <th>Impresiones</th>
        </tr>
    </thead>
    <tbody id="tbody_sites">
        <?php
        $datos = $sitios->data;
        $rows = explode("\n", $datos);

        for ($i = 1; $i < count($rows); $i++) {
            if (strlen($rows[$i]) > 0)
                $arr_data[] = $rows[$i];
        }
        $site_id_2 = "";
        //$imps_sitio = 0;

        if (isset($arr_data)) {
            $cant_formatos = 0;
            foreach ($arr_data as $c => $v) {

                $total_reg++;
                $fields = explode(",", $v);
                
                $imps = $fields[2];
                $imps = ($imps * $this->multiplicacion_volumen);
                
                $total_imps = $total_imps + $imps;
                $total_clicks = $total_clicks + $fields[3];

                $ctr = ($fields[4] * 100);
                $size = $fields[5];
                $site_id = $fields[1];

                $site_name = substr($fields[0], 0, strpos($fields[0], '('));
                //$site_name = str_replace("www.", "", $site_name);
                $site_name = str_replace("http://", "", $site_name);
                $site_name = str_replace("/", "", $site_name);

                $texto_cats = $arr_site_cat[$site_id];
                //$site = $this->sites->get_site_by_id($site_id);

                if ($site_id == $site_id_2) {
                    $imps_sitio = $imps_sitio + $imps;
                    $cant_formatos++;
                } else {
                    if ($site_id_2 != "") {
                        //$imps_sitio = $imps_sitio + $fields[2];
                        $texto_cats_2 = $arr_site_cat[$site_id_2];
                        ?>
                        <tr>
                            <td><?= $site_name_2 ?></td>
                            <td>Layer</td>
                            <td><?= $texto_cats_2 ?></td>
                            <td><?= number_format(($imps_sitio/$cant_formatos), 0, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td><?= $site_name_2 ?></td>
                            <td>Skin</td>
                            <td><?= $texto_cats_2 ?></td>
                            <td><?= number_format(($imps_sitio/$cant_formatos), 0, ',', '.') ?></td>
                        </tr>
                        <?php
                        $site_id_2 = $site_id;
                        $site_name_2 = $site_name;
                        $imps_sitio = 0;
                        $imps_sitio = $imps_sitio + $imps;
                        $cant_formatos = 0;
                        $cant_formatos++;
                    } else {
                        $site_id_2 = $site_id;
                        $site_name_2 = $site_name;
                        $imps_sitio = 0;
                        $imps_sitio = $imps_sitio + $imps;
                        $cant_formatos = 0;
                        $cant_formatos++;
                    }
                }
                ?>
                <tr>
                    <td><?= $site_name ?></td>
                    <td><?= $size ?></td>
                    <td><?= $texto_cats ?></td>
                    <td><?= number_format($imps, 0, ',', '.') ?></td>
                </tr>

                <?php
            }
            $total_ctr = ($total_clicks / $total_imps) * 100;
        }
        ?>
    </tbody>
    <tfoot style="color: #000; font-weight: bold;">
        <tr style="background-color: #E5E5E5;border: 1px solid #CCC;font-size: 15px;">
            <td>Totales:</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
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
        
        $(".exportarPorSitioFormato").click(function(){
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
            "aaSorting": [[ 0, "desc" ]],
            "bInfo": false,
            //"sScrollY": 400,
            //"sScrollX": "25%",
            //"bScrollCollapse": true,
            "aoColumns": [
                null,
                null,
                null,
                { "sType": "slo" }
            ],
            "oLanguage": {
                "sSearch": "Buscar"
            }
            //"sDom": '<"H">rt<"F"flp>'
        })
        
        
        $('#tbl_report_sites tr').click( function() {
            $(this).toggleClass('row_selected');
        } );
    });
</script>
