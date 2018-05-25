<style type="text/css">
    #tbl_report_sites{
        border-spacing: 0;
        font-size: 0.8em;
    }
</style>

<h2 class="border_bottom">Inventario por Usuarios unicos mensuales.</h2>

<div style="margin-bottom: 20px;">
    <form action="/inventario/exportar_excel_usuarios_unicos" method="post" target="_blank" id="FormularioExportacion">
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
            <th>Pais</th>
             <th>Desktop</th>
            <th>Tablets</th>
            <th>Smartphones</th>
           


        </tr>
    </thead>
    <tbody id="tbody_sites">

        <?php
            foreach ($reporte as $row) {     
        ?>
                        <tr>
                            <td><?= $row->pais ?></td>
                            <td><?= $row->desktop ?></td>
                            <td><?= $row->tablets ?></td>
                            <td><?= $row->smartphones ?></td>
                    
                        </tr>
        <?php            
                }
        
        ?>
    </tbody>
  
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
                null,
                null
            ],
            "sDom": "<'header'<'span8'l><'span8'f>r>t<'row'<'span8'i><'span8'p>>"
        });
    });
</script>
