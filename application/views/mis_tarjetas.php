<?php if(sizeof($tarjetas) < 3){ ?>
    <div style="background-color: #EEEEEE; border-radius: 4px; margin-bottom: 10px; padding: 8px 5px;">
        <a href="#" data-reveal-id="myModal" class="button_new" style="background: url(../images/background_button.png) repeat-x scroll left top #DDD;border: 1px solid #C7C7C7;border-radius: 3px;color: #333333;cursor: pointer;padding: 3px 8px;">Agregar tarjeta</a>
    </div>

    <div id="myModal" class="reveal-modal mlarge">
    </div>

    <script type="text/javascript">
        $(document).ready(function(){
            $('#myModal').append(divLoader);
            $('#myModal').load('/campania/mostrar_ingresar_tarjeta/mis_tarjetas');
        });
    </script>
<?php } ?>

<?php
if ($tarjetas) {
    ?>
    <table class="display" id="tbl_tarjetas" style="width: 100%">
        <thead>
            <tr>
                <th>N&uacute;mero de tarjeta</th>
                <th>Fecha de alta</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($tarjetas as $row) {
                ?>
                <tr>
                    <td style="text-align: center;"><?= $row->nro_tarjeta ?></td>
                    <td style="text-align: center;"><?= MySQLDateToDate($row->fecha_alta) ?></td>
                    <td style="text-align: center;">
                        <?php
                        if ($row->estado == 'PENDIENTE') {
                            echo 'Aprobaci&oacute;n pendiente';
                        } else if ($row->estado == 'APROBADA') {
                            echo '<span style="color:green;font-weight:bold;">Aprobada</span>';
                        } else if ($row->estado == 'RECHAZADA') {
                            echo '<span style="color:red;font-weight:bold;">Rechazada</span>';
                        }
                        ?>
                    </td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>

    <script type="text/javascript">
        $(document).ready(function(){
            // LISTO LAS TARJETAS **********************************************
            $('#tbl_tarjetas').dataTable({
                "bPaginate": false,
                "iDisplayLength": 3,
                "bLengthChange": false,
                "bFilter": false,
                "bInfo": false,
                "bLength": false,
                "aaSorting": [[ 1, "desc" ]],
                "aoColumns": [
                    null,
                    { "sType": "uk_date" },
                    null
                ]
            });
            // *****************************************************************
        });
    </script>

    <?php
} else {
    ?>

    <div class="alerta">A&uacute;n no se ha cargado ning&uacute;na tarjeta en el sistema.</div>

    <?php
}
?>
