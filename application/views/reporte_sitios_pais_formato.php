<style type="text/css">
    #tbl_report_sites{
        border-spacing: 0;
        font-size: 0.8em;
    }
</style>

<h2 class="border_bottom">Reportes por pa&iacute;s y formato.</h2>

<div style="margin-bottom: 20px;">
            <form action="/inventario/excel_pais_formato" method="post" target="_blank" id="FormularioExportacion">
                <input type="hidden" id="datos_a_enviar" name="datos_a_enviar" value=""/>
                <input type="hidden" id="interval" name="interval" value=""/>
                <input type="hidden" id="texto_categoria" name="texto_categoria" value="" />
                <input type="hidden" id="texto_pais" name="texto_pais" value=""/>
                <input type="hidden" id="notacion" name="notacion" value="<?= $notacion ?>" />
                <input type="submit" name="submit_export_excel" value="Exportar a Excel" class="exportarPorPais-formato button_new" />
            </form>
</div>
<!--
<div style="margin-bottom: 20px;">
    <form action="/inventario/excel_pais_formato" method="post" target="_blank" id="FormularioExportacion">
        <input type="hidden" id="datos_a_enviar" name="datos_a_enviar" />
        <input type="hidden" id="interval" name="interval" />
        <input type="hidden" id="texto_formato" name="texto_categoria" />
        <input type="hidden" id="texto_pais" name="texto_pais" />
        <input type="submit" name="submit_export_excel" value="Exportar a Excel" class="exportarPorSitio-Pais button_new" />
    </form>
</div>
-->
<table class="display" id="tbl_report_sites" style="width: 100%">

    <thead>
        <tr>
            <th>Pais</th>
            <th>Formato</th>
            <th>Impresiones</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($reporte['paises'] as $nombre => $row_pais) {
            $formatos = $row_pais;
            if ($formatos != null) {
                foreach ($formatos as $nombre_formato => $row_formato) {
                    if ($row_formato['imps'] > 0) {
                        $nombre_formato = $nombre_formato;
                        $imps_formato = $row_formato['imps'];

                        echo '<tr>';
                        echo '<td>';
                        echo $nombre . '</td>';
                        echo '<td>';
                        echo $nombre_formato . '</td>';
                        echo '<td>';
                        echo $imps_formato . '</td>';
                        echo '</tr>';
                    }
                }
            }
        }
        ?>
    </tbody>
    <tfoot style="color: #000; font-weight: bold;">
        <tr style="background-color: #E5E5E5;border: 1px solid #CCC;font-size: 15px;">
            <td>Totales:</td>
            <td></td>
            <td><? echo $reporte['totales']["imps"]; ?></td>
        </tr>
    </tfoot>
</table>


<script type="text/javascript">

    $(document).ready(function() {


        $(".exportarPorPais-formato").click(function() {
            $("#datos_a_enviar").val($("<div>").append($("#tbl_report_sites").eq(0).clone()).html());
            $("#interval").val($("#cmb_rango").find(':selected').val());

            var texto_canales_tematicos = "";
            $("#cmb_canales_tematicos_2 option").each(function() {
                texto_canales_tematicos = texto_canales_tematicos + $(this).html() + ", ";
            });

            var texto_paises = "";
            $("#cmb_paises_2 option").each(function() {
                texto_paises = texto_paises + $(this).html() + ", ";
            });

            if (texto_canales_tematicos == "")
                texto_canales_tematicos = "Todos";

            if (texto_paises == "")
                texto_paises = "Todos";

            $("#texto_categoria").val(texto_canales_tematicos);
            $("#texto_pais").val(texto_paises);

            $("#FormularioExportacion").submit();
        });

        oTable = $('#tbl_report_sites').dataTable({
            <?php if(sizeof($reporte['paises']) > 10){ ?>
                "bPaginate ": true,
            <?php }else{ ?>
                "bPaginate ": false,
            <?php } ?>
            "sPaginationType": "full_numbers",
            "aLengthMenu": [[10, 20, 50, 100, -1], [10, 20, 50, 100, "Todos"]],
            iDisplayLength: 10,
            "aaSorting": [[2, "desc"]],
            "bInfo": false,
            "oLanguage": {
                "sSearch": "Buscar: ",
                "sLengthMenu": "Mostrar  : _MENU_ de  <?= sizeof($formatos); ?>",
                "sEmptyTable": "No se encontraron datos."
            },
            "oPaginate": {
                "sFirst": "<<",
                "sLast": ">>",
                "sNext": ">",
                "sPrevious": "<"

            },
            "aoColumns": [
                null,
                null,
                null
            ],
            "sDom": "<'header'<'span8'l><'span8'f>r>t<'row'<'span8'i><'span8'p>>"
        });
    });
</script>
