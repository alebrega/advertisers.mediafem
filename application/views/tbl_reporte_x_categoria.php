<?php
$total_imps = 0;
?>
<table>
    <tr>
        <td>
            <form action="/welcome/export_excel_appnexus_category" method="post" target="_blank" id="FormularioExportacionPorCanal">
                <input type="hidden" id="datos_a_enviar" name="datos_a_enviar" />
                <input type="hidden" id="nombre_anunciante_excel" name="nombre_anunciante_excel" />
                <input type="hidden" id="rango_excel" name="rango_excel" />
                <input type="hidden" id="grupos_excel" name="grupos_excel" />
                <input type="hidden" id="columnas_excel" name="columnas_excel" />
                <input type="hidden" id="fecha_inicio_excel" name="fecha_inicio_excel" />
                <input type="hidden" id="fecha_fin_excel" name="fecha_fin_excel" />
                <input type="submit" name="submit_export_excel" value="Exportar a Excel" class="exportarPorCanal button_new" />
            </form>
        </td>
    </tr>
</table>
<table class="display" id="tbl_report_sites" style="width: 100%">
    <thead>
        <tr>
            <th><b>Canal Tematico</b></th>
            <th>Impresiones</th>
        </tr>
    </thead>
    <tbody id="tbody_reporte">
        <?php
        foreach ($canales as $row) {
            $imps = $row['imps'] * $this->multiplicacion_volumen;
            
            $total_imps = $total_imps + $imps;
            
            if($imps > 0){
            ?>
                <tr>
                    <td><?= $row['name'] ?></td>
                    <td><?= number_format($imps, 0, ',', '.') ?></td>
                </tr>
            <?php
            }
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
        
        $(".exportarPorCanal").click(function(){
            $("#datos_a_enviar").val( $("<div>").append( $("#tbl_report_sites").eq(0).clone()).html());
            $("#nombre_anunciante_excel").val('');
            
            var rango = $("#cmb_rango").val();
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
                        
            $("#FormularioExportacionPorCanal").submit();
        });
        
        var oTable = $('#tbl_report_sites').dataTable( {
            "sPaginationType": "full_numbers",
            "aLengthMenu": [[10, 20, 50, 100, -1], [10, 20, 50, 100, "Todos"]],
            iDisplayLength: -1,
            "bJQueryUI": true,
            "aaSorting": [[ 1, "desc" ]],
            "bInfo": false,
            "aoColumns": [
                null,
                { "sType": "slo" },
            ],
            "oLanguage": {
                "sSearch": "Buscar"
            }
        });
        
        $('#tbl_report_sites tr').click( function() {
            $(this).toggleClass('row_selected');
        } );
    });
</script>
