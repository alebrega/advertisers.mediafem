<style type="text/css">
    #tbl_report_sites{
        border-spacing: 0;
        font-size: 0.8em;
    }
</style>

<h2 class="border_bottom">Inventario por pa&iacute;s.</h2>

<div style="margin-bottom: 20px;">
            <form action="/inventario/exportar_excel_pais" method="post" target="_blank" id="FormularioExportacion">
                <input type="hidden" id="datos_a_enviar" name="datos_a_enviar" />
                <input type="hidden" id="interval" name="interval" />
                <input type="hidden" id="texto_categoria" name="texto_categoria" />
                <input type="hidden" id="texto_pais" name="texto_pais" />
                <input type="hidden" id="notacion" name="notacion" value="<?= $notacion ?>" />
                <input type="submit" name="submit_export_excel" value="Exportar a Excel" class="exportarPorPais button_new" />
            </form>
</div>
<table class="display" id="tbl_report_sites" style="width: 100%">
    <thead>
        <tr>
            <th><b>Pais</b></th>
            <th>Impresiones</th>
        </tr>
    </thead>
    <tbody id="tbody_sites">
        <?php
        foreach ($paises as $pais) {
            if($pais['imps'] > 0){
        ?>
            <tr>
                <td><?= $pais['nombre'] ?></td>
                <td><?= $pais['imps'] ?></td>
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

        $(".exportarPorPais").click(function(){

            var oSettings = oTable.fnSettings();
            oSettings._iDisplayLength = -1;
            oTable.fnDraw();

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

        oTable = $('#tbl_report_sites').dataTable( {
            <?php if(sizeof($paises) > 10){ ?>
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
                "sLengthMenu": "Mostrar  : _MENU_ de  <?=  sizeof($paises); ?>",
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
