<style type="text/css">
    #tbl_report_sites{
        border-spacing: 0;
        font-size: 0.8em;
    }
</style>

<h2 class="border_bottom">Inventario por sitios.</h2>

<div style="margin-bottom: 20px;">
    <form action="/inventario/export_excel" method="post" target="_blank" id="FormularioExportacion">
        <input type="hidden" id="datos_a_enviar" name="datos_a_enviar" />
        <input type="hidden" id="interval" name="interval" />
        <input type="hidden" id="texto_categoria" name="texto_categoria" />
        <input type="hidden" id="texto_pais" name="texto_pais" />
                <input type="hidden" id="notacion" name="notacion" value="<?= $notacion ?>" />
        <input type="submit" name="submit_export_excel" value="Exportar a Excel" class="exportarPorSitio button_new" />
    </form>
</div>

<table class="display" id="tbl_report_sites" style="width: 100%">
    <thead>
        <tr>
            <th>Sitio Web</th>
            <th style="width: 55%">Canales Tematicos</th>
            <th>Impresiones</th>


        </tr>
    </thead>
    <tbody id="tbody_sites">

        <?php

        if (isset($sitios)) {
            foreach ($sitios as $sitio) {
                if($sitio['imps'] > 0){
                ?>
                    <tr>
                        <td><?= $sitio['url_sitio'] ?></td>
                        <td><?= $sitio['categorias'] ?></td>
                        <td><?= $sitio['imps'] ?></td>
                    </tr>
                <?php
                }
            }
        }
        ?>
    </tbody>
    <tfoot style="color: #000; font-weight: bold;">
        <tr style="background-color: #E5E5E5;border: 1px solid #CCC;font-size: 15px;">
            <td>Totales:</td>
            <td>&nbsp;</td>
            <td><?php
        if (isset($totales['imps'])) {
            echo $totales['imps'];
        } else {
            echo '0.00';
        }
        ?></td>
        </tr>
    </tfoot>
</table>

<script type="text/javascript">
    $(document).ready(function(){
        var oTable;

<?php if ($mostrar_alerta) { ?>
            $(".alerta").css('display', 'block');
<?php } ?>

        $(".exportarPorSitio").click(function(){

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
            <?php if(sizeof($sitios) > 10){ ?>
                "bPaginate ": true,
            <?php }else{ ?>
                "bPaginate ": false,
            <?php } ?>
            "sPaginationType": "full_numbers",
            "aLengthMenu": [[10, 20, 50, 100, -1], [10, 20, 50, 100, "Todos"]],
            iDisplayLength: 10,
            "aaSorting": [[ 2, "desc" ]],
            "bInfo": false,
            "oLanguage": {
                "sSearch": "Buscar: ",
                "sLengthMenu": "Mostrar  : _MENU_ de  <?=  sizeof($sitios); ?>",
                "sEmptyTable": "No se encontraron datos.",
                "oPaginate": {
                    "sFirst": "<<",
                    "sLast": ">>",
                    "sNext": ">",
                    "sPrevious": "<"
                }
            },
            "aoColumns": [
                null,
                null,
                <?php if($notacion == 0){ ?>
                        { "sType": "sloComma" }
                <?php }else{ ?>
                        { "sType": "slo" }
                <?php } ?>
            ],
            "sDom": "<'header'<'span8'l><'span8'f>r>t<'row'<'span8'i><'span8'p>>"
        });
    });
</script>
