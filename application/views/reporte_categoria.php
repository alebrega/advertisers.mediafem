<style type="text/css">
    #tbl_report_sites{
        border-spacing: 0;
        font-size: 0.8em;
    }
</style>

<h2 class="border_bottom">Inventario por categor&iacute;as.</h2>

<div style="margin-bottom: 20px;">
            <form action="/inventario/export_excel_category" method="post" target="_blank" id="FormularioExportacionPorCanal">
                <input type="hidden" id="datos_a_enviar" name="datos_a_enviar" />
                <input type="hidden" id="nombre_anunciante_excel" name="nombre_anunciante_excel" />
                <input type="hidden" id="rango_excel" name="rango_excel" />
                <input type="hidden" id="grupos_excel" name="grupos_excel" />
                <input type="hidden" id="columnas_excel" name="columnas_excel" />
                <input type="hidden" id="fecha_inicio_excel" name="fecha_inicio_excel" />
                <input type="hidden" id="fecha_fin_excel" name="fecha_fin_excel" />
                <input type="submit" name="submit_export_excel" value="Exportar a Excel" class="exportarPorCanal button_new" />
            </form>
</div>

<table class="display" id="tbl_report_sites" style="width: 100%">
    <thead>
        <tr>
            <th>Canal Tematico</th>
            <th>Impresiones</th>
        </tr>
    </thead>
    <tbody id="tbody_reporte">
        <?php
        foreach ($categorias as $row) {
            if($row['imps'] > 0){
            ?>
                <tr>
                    <td><?= $row['nombre'] ?></td>
                    <td><?= $row['imps'] ?></td>
                </tr>
            <?php
            }
        }
        ?>
    </tbody>
    <tfoot style="color: #000; font-weight: bold;">
        <tr style="background-color: #E5E5E5;border: 1px solid #CCC;font-size: 15px;">
            <td>Totales:</td>
            <td><?= $totales['imps'] ?></td>
        </tr>
    </tfoot>
</table>
<script type="text/javascript">
    $(document).ready(function(){

        var oTable;

        <?php if($mostrar_alerta){ ?>
                $(".alerta").css('display', 'block');
        <?php } ?>

        $(".exportarPorCanal").click(function(){

            var oSettings = oTable.fnSettings();
            oSettings._iDisplayLength = -1;
            oTable.fnDraw();

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

        oTable = $('#tbl_report_sites').dataTable( {
            <?php if(sizeof($categorias) > 10){ ?>
                "bPaginate ": true,
            <?php }else{ ?>
                "bPaginate ": false,
            <?php } ?>
            "sPaginationType": "full_numbers",
            "aLengthMenu": [[10, 20, 50, 100, -1], [10, 20, 50, 100, "Todos"]],
            iDisplayLength: 10,
            "aaSorting": [[ 1, "desc" ]],
            "bInfo": false,
            "aoColumns": [
                null,
                <?php if($notacion == 0){ ?>
                        { "sType": "sloComma" }
                <?php }else{ ?>
                        { "sType": "slo" }
                <?php } ?>
            ],
            "oLanguage": {
                "sSearch": "Buscar: ",
                "sLengthMenu": "Mostrar  : _MENU_ de  <?=  sizeof($categorias); ?>",
                "sEmptyTable": "No se encontraron datos.",
                "oPaginate": {
                    "sFirst": "<<",
                    "sLast": ">>",
                    "sNext": ">",
                    "sPrevious": "<"
                }
            },
            "sDom": "<'header'<'span8'l><'span8'f>r>t<'row'<'span8'i><'span8'p>>"
        });
    });
</script>
