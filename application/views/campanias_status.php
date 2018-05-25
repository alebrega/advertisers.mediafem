<?php
function extract_email_address ($string) {
    foreach(preg_split('/\s/', $string) as $token) {
        $email = filter_var(filter_var($token, FILTER_SANITIZE_EMAIL), FILTER_VALIDATE_EMAIL);
        if ($email !== false)
            return $email;
    }
    return false;
}
?>

<div style="background-color: #EEEEEE; border-radius: 4px; margin-bottom: 10px; padding: 8px 5px;">
    <b>Intervalo:</b>
    <select id="cmb_rango_status" name="cmb_rango" style="width:150px; margin-right: 20px">
        <option value="today">Ayer</option>
        <option value="yesterday">Antes de ayer</option>
    </select>

    <b>Estado:</b>
    <select id="cmb_estado_campanias_status"  style="width:150px; margin-right: 20px">
        <option value="0">Todos</option>
        <option value="Atrasada">Atrasada</option>
        <option value="Adelantada">Adelantada</option>
        <option value="Finalizada">Finalizada</option>
        <option value="OK">OK</option>
    </select>

    <input id="btn_ejecutar_status_campanas" type="button" value="Actualizar filtros" class="button_new" />
</div>

<table id="lista_status_campanas" cellspacing="0" cellpadding="0" border="0">
    <thead>
        <tr>
            <th>Nombre de la campa&ntilde;a</th>
            <th>Estado</th>
            <th>Imps/Clicks necesarios por d&iacute;a</th>
            <th>Imps/Clicks faltantes / adicionales</th>
            <th>D&iacute;as restantes</th>
            <th>Impresiones del d&iacute;a</th>
            <th>Clicks del d&iacute;a</th>
            <th>CTR del d&iacute;a</th>
            <th>Conversiones del d&iacute;a</th>
            <th>Impresiones consumidas totales</th>
            <th>Clicks consumidos totales</th>
            <th>CTR total</th>
            <th>Conversiones consumidas totales</th>
            <th>Impresiones contratadas</th>
            <th>Clicks contratados</th>
            <th>Fecha inicio</th>
            <th>Fecha de fin</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($campanias as $row) {

            if($row->nombre_anunciante == '')
                continue;

            $nombre_anunciante = extract_email_address($row->nombre_anunciante);

            if($nombre_anunciante)
                $row->nombre_anunciante = str_replace(extract_email_address($row->nombre_anunciante) . ' - ', '', $row->nombre_anunciante);

            $estilo = "";
            if ($row->estado == "OK") {
                $estilo = 'style="color: green;font-weight:bold;"';
            } elseif ($row->estado == "Adelantada") {
                $estilo = 'style="color: #000080;font-weight:bold;"';
            } elseif ($row->estado == "Finalizada") {
                $estilo = 'style="color:#FF8C00;font-weight:bold;"';
            } else {
                $estilo = 'style="color: red;font-weight:bold;"';
            }
            ?>
            <tr>
                <td><?= htmlentities(utf8_decode($row->nombre_anunciante), ENT_QUOTES, 'UTF-8') ?></td>
                <td <?= $estilo ?>><?= $row->estado ?></td>
                <td>
                    <?php
                    if ($row->necesarias_x_dia > 0) {
                        if (strtoupper($row->modalidad_de_compra) == "CPM")
                            echo number_format($row->necesarias_x_dia, 0, ',', '.') . " imps";
                        if (strtoupper($row->modalidad_de_compra) == "CPC")
                            echo number_format($row->necesarias_x_dia, 0, ',', '.') . " clicks";
                    }else {
                        if (strtoupper($row->modalidad_de_compra) == "CPM")
                            echo "0 imps";
                        if (strtoupper($row->modalidad_de_compra) == "CPC")
                            echo "0 clicks";
                    }
                    ?>
                </td>
                <td>
                    <?php
                    if ($row->necesarias_x_dia > 0) {
                        if (strtoupper($row->modalidad_de_compra) == "CPM")
                            echo number_format(($row->necesarias_x_dia - $row->imps_ayer), 0, ',', '.') . " imps";
                        if (strtoupper($row->modalidad_de_compra) == "CPC")
                            echo number_format(($row->necesarias_x_dia - $row->clicks_ayer), 0, ',', '.') . " clicks";
                    }else {
                        if (strtoupper($row->modalidad_de_compra) == "CPM")
                            echo "0 imps";
                        if (strtoupper($row->modalidad_de_compra) == "CPC")
                            echo "0 clicks";
                    }
                    ?>
                </td>
                <td><?= $row->dias_restantes ?></td>
                <td><?= number_format($row->imps_ayer, 0, ',', '.') ?></td>
                <td><?= number_format($row->clicks_ayer, 0, ',', '.') ?></td>
                <td><?= str_replace(".", ",", $row->ctr_ayer) ?> %</td>
                <td><?= number_format($row->conversiones_ayer, 0, ',', '.') ?></td>
                <td><?= number_format($row->imps_total, 0, ',', '.') ?></td>
                <td><?= number_format($row->clicks_total, 0, ',', '.') ?></td>
                <td><?= str_replace(".", ",", $row->ctr_total) ?> %</td>
                <td><?= number_format($row->conversiones_total, 0, ',', '.') ?></td>
                <td><?= number_format($row->imps_contratadas, 0, ',', '.') ?></td>
                <td><?= number_format($row->clicks_contratados, 0, ',', '.') ?></td>
                <td><?= MySQLDateToDate($row->fecha_inicio) ?></td>
                <td>
                    <?php
                    if ($row->fecha_fin == "0000-00-00 00:00:00") {
                        echo " - ";
                    } else {
                        echo MySQLDateToDate($row->fecha_fin);
                    }
                    ?>
                </td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table> <!-- end #lista_status_campanas -->

<div id="scrool_tabla_status" style="width: 100%; overflow-x: scroll; overflow-y: hidden; white-space:nowrap;  margin: 20px 0;  padding: 40px 0 0;">
</div>

<script type="text/javascript">
    $(document).ready( function () {

        $('#btn_ejecutar_status_campanas').click(function(){
            var rango = $("#cmb_rango_status").find(':selected').val();
            var estado = $("#cmb_estado_campanias_status").find(':selected').val();

            $('#status_campanas').html(' ');
            $('#status_campanas').append(divLoader);

            $('#status_campanas').load('/campania/status_campanias/' + rango + '/' + estado);
        });

        $('#lista_status_campanas').dataTable({
            <?php if(sizeof($campanias) > 10){ ?>
                "sPaginationType": "full_numbers",
            <?php }else{ ?>
                "bPaginate": false,
            <?php } ?>

            "aaSorting": [[1, "desc"]],
            "bFilter": true,
            "bInfo": false,
            "bLengthChange": false,
            "bLength": false,
            "oLanguage": {
                "sSearch": "Buscar: ",
                "sLengthMenu": "Mostrar  : _MENU_",
                "oPaginate": {
                    "sFirst": "<<",
                    "sLast": ">>",
                    "sNext": ">",
                    "sPrevious": "<"
                }
            },
            "aoColumns": [
                {"bSortable": true},
                {"bSortable": true},
                {"bSortable": false},
                {"bSortable": false},
                {"bSortable": false},
                {"bSortable": false},
                {"bSortable": false},
                {"bSortable": false},
                {"bSortable": false},
                {"bSortable": false},
                {"bSortable": false},
                {"bSortable": false},
                {"bSortable": false},
                {"bSortable": false},
                {"bSortable": false},
                {"bSortable": false},
                {"bSortable": false}
            ]
        });

        $("#lista_status_campanas").appendTo("#scrool_tabla_status");

        $("#lista_status_campanas_filter").appendTo("#lista_status_campanas_wrapper");
        $("#scrool_tabla_status").appendTo("#lista_status_campanas_wrapper");
        $("#lista_status_campanas_paginate").appendTo("#lista_status_campanas_wrapper");
    } );
</script>