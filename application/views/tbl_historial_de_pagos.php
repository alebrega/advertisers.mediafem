<?php
if ($pagos) {
    ?>
    <tr class="encabezado">
        <td class="titulo">
            Historial de Pagos
        </td>
    </tr>
    <tr>
        <td id="tbl_historial_pagos">
            <table class="display" id="tbl_historial_de_pagos" style="width: 100%">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Descripci&oacute;n</th>
                        <th>Importe</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_debitos = 0;

                    foreach ($pagos as $row) {
                        $total_debitos += $row->importe;
                        ?>
                        <tr>
                            <td><?= MySQLDateToDate($row->fecha_pago) ?></td>
                            <td><?= $row->descripcion ?></td>
                            <td>
                                <?php
                                if($usuario->notacion == 0){
                                    $importe = number_format($row->importe, 2, '.', ',');
                                }else if($usuario->notacion == 1){
                                    $importe = number_format($row->importe, 2, ',', '.');
                                }

                                echo 'U$S ' . $importe;
                                ?>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
                <?php
                if ($pagos) {
                    ?>
                    <tfoot style="color: #000; font-weight: bold;">
                        <tr style="background-color: #E5E5E5;border-left: 1px solid #CCC;border-right: 1px solid #CCC;
                            border-top: 1px solid #CCC;height: 15px">
                            <td colspan="3"></td>
                        </tr>
                        <tr style="background-color: #E5E5E5;border: 1px solid #CCC;font-size: 15px">
                            <td colspan="2">&nbsp;</td>
                            <td>
                                <?php
                                if($usuario->notacion == 0){
                                    $total_debitos = number_format($total_debitos, 2, '.', ',');
                                }else if($usuario->notacion == 1){
                                    $total_debitos = number_format($total_debitos, 2, ',', '.');
                                }

                                echo 'U$S ' . $total_debitos;
                                ?>
                            </td>
                        </tr>
                    </tfoot>
                    <?php
                }
                ?>
            </table>
        </td>
    </tr>
    <?php
    if (sizeof($pagos) > 10) {
        ?>
        <script type="text/javascript">
            $(document).ready(function(){

                $('#tbl_historial_de_pagos').dataTable({
                    <?php if(sizeof($pagos) > 10){ ?>
                        "sPaginationType": "full_numbers",
                    <?php }else{ ?>
                        "bPaginate": false,
                    <?php } ?>

                    "bJQueryUI": true,
                    "aLengthMenu": [[10, 20, 50, 100], [10, 20, 50, 100]],
                    iDisplayLength: 10,
                    "bInfo": false,
                    "bFilter":false,
                    "aaSorting": [[ 0, "desc" ]],
                    "aoColumns": [
                        { "sType": "uk_date" },
                        null,
                        { "sType": "currency" },
                    ],
                    "oLanguage": {
                        "sSearch": "<?= $this->lang->line('buscar') ?>",
                        "sZeroRecords" : "A&uacute;n no ha hecho ning&uacute;n pago"
                    },
                    "sDom": '<"H">rt<"F"flp>'
                });
            });
        </script>
    <?php } else { ?>

        <script type="text/javascript">
            $(document).ready(function(){

                $('#tbl_historial_de_pagos').dataTable({
                    "bPaginate": false,
                    "sPaginationType": "full_numbers",
                    "bJQueryUI": true,
                    "aLengthMenu": [[10, 20, 50, 100], [10, 20, 50, 100]],
                    iDisplayLength: 10,
                    "bInfo": false,
                    "bFilter":false,
                    "aaSorting": [[ 0, "desc" ]],
                    "aoColumns": [
                        { "sType": "uk_date" },
                        null,
                        { "sType": "currency" }
                    ],
                    "oLanguage": {
                        "sSearch": "<?= $this->lang->line('buscar') ?>",
                        "sZeroRecords" : "A&uacute;n no ha hecho ning&uacute;n pago"
                    },
                    "sDom": '<"H">rt<"F"flp>'
                });
                $('.fg-toolbar').removeClass("ui-widget-header");
                $('.fg-toolbar').removeClass("ui-widget-header-simple");
            });
        </script>
    <?php
    }
}
?>
