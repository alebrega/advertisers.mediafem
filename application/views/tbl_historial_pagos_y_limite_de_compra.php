<?php
if ($saldos) {
    ?>
    <table class="display" id="tbl_historial_saldos_mediafem" style="width: 100%">
        <thead>
            <tr>
                <th>Id</th>
                <th>Fecha</th>
                <th>Descripci&oacute;n</th>
                <th>Cr&eacute;dito</th>
                <th>D&eacute;bito</th>
                <th>Balance</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $db_balance = 0;
            foreach ($saldos as $row) {
                
                $db_balance += $row->importe; 
                
                if($row->tipo == 0){
                    $row->debito = $row->importe;
                }else{
                    $row->debito = 0;
                }
                
                if($row->tipo == 0){
                    $row->credito = 0;
                }else{
                    $row->credito = $row->importe;
                }
                
                if ($this->user_data->notacion == 0) {
                    $credito = number_format($row->credito, 2, '.', ',');
                    $debito = number_format($row->debito, 2, '.', ',');
                    $balance = number_format($db_balance, 2, '.', ',');
                } else if ($this->user_data->notacion == 1) {
                    $credito = number_format($row->credito, 2, ',', '.');
                    $debito = number_format($row->debito, 2, ',', '.');
                    $balance = number_format($db_balance, 2, ',', '.');
                }
                ?>
                <tr>
                    <td><?= $row->id ?></td>
                    <td><?= MySQLDateToDate($row->fecha) ?></td>
                    <td><?= $row->descripcion ?></td>
                    <td><?= $credito . ' ' . $cliente->moneda ?></td>
                    <td><?= $debito . ' ' . $cliente->moneda ?></td>
                    <td><?= $balance . ' ' . $cliente->moneda ?></td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>

    <script type="text/javascript">
        $().ready(function(){
            // LISTO LOS PAGOS *************************************************
            $('#tbl_historial_saldos_mediafem').dataTable({

            <?php if(sizeof($saldos) > 10){ ?>
                "sPaginationType": "full_numbers",
            <?php }else{ ?>
                "bPaginate": false,
            <?php } ?>

                "iDisplayLength": 10,
                "bLengthChange": false,
                "bFilter": false,
                "bInfo": false,
                "bLength": false,
                "aaSorting": [[ 0, "desc" ]],
                'oLanguage': {
                    "oPaginate": {
                        "sFirst": "<<",
                        "sLast": ">>",
                        "sNext": ">",
                        "sPrevious": "<"
                    }
                },
                "aoColumns": [
                    { "bVisible": false },
                    { "sType": "uk_date" },
                    null,
                    { "sType": "currency" },
                    { "sType": "currency" },
                    { "sType": "currency" }
                ]
            });
            // *****************************************************************
        });
    </script>

    <?php
} else {
    ?>

    <div class="alerta">A&uacute;n no se ha generado ning&uacute;n movimiento de ingresos o egresos en el saldo de tu cuenta.</div>

    <?php
}
?>