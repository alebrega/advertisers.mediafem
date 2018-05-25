<?php
date_default_timezone_set("America/New_York");

$imps_ayer = 0;
$clicks_ayer = 0;

$imps_siempre = 0;
$clicks_siempre = 0;

$ctr_total_ayer = 0;
$ctr_total_siempre = 0;

$total_inversion_neta = 0;
$total_consumido = 0;
?>
<table style="width: 100%;margin-top: 20px">
    <tr>
        <td style="width: 120px">
            Fecha de an&aacute;lisis:
        </td>
        <td>
            <b><?= MySQLDateToDate($fecha_analisis) ?></b>
        </td>
    </tr>
    <tr>
        <td>
            Con datos del:
        </td>
        <td>
            <b><?= date("d/m/Y 23:59:59", strtotime("$fecha_datos")); ?></b>
        </td>
    </tr>
    <tr>
        <td>
            Fecha actual:
        </td>
        <td>
            <b><?= date("d/m/Y H:i:s"); ?></b> / ET
        </td>
    </tr>
</table>
<div style="font-size: 10px;color: #000;margin-top: 20px">
    <table class="display" id="tbl_status_campanias">
        <thead>
            <tr>
                <th>Anunciante</th>
                <th>Campa&ntilde;a</th>
                <th>Estado</th>
                <th>Imps/Clicks necesarios por d&iacute;a</th>
                <th>Imps/Clicks faltantes / adicionales</th>
                <th>Impresiones del d&iacute;a</th>
                <th>Clicks del d&iacute;a</th>
                <th>CTR del d&iacute;a</th>
                <th>D&iacute;as restantes</th>
                <!--<th style="text-align: center; width: 30px !important;">Conversiones del d&iacute;a</th>-->
                <th>Conversiones del d&iacute;a</th>
                <th>Adserver</th>
                <th>Modalidad de compra</th>
                <th>Impresiones consumidas totales</th>
                <th>Clicks consumidos totales</th>
                <th>CTR total</th>
                <th>Conversiones consumidas totales</th>
                <th>Impresiones contratadas</th>
                <th>Clicks contratados</th>
                <th>¿Us&oacute; exchange en el d&iacute;a?</th>
                <th>Ejecutiva/o de cuentas</th>
                <th>Inversi&oacute;n neta</th>
                <th>Consumido</th>
                <th>Inversi&oacute;n restante</th>
                <th>Fecha inicio</th>
                <th>Fecha de fin</th>
                <th>Ejecutiva/o de medios</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($reporte) {
                foreach ($reporte as $row) {

                    if ($estado_campania) {
                        if (!in_array($row->id_anunciante, $anunciantes_filtrados))
                            continue;
                    }

                    if (!$row->fila_total_campania && (trim(substr($row->nombre_line_item, 0, 4)) != "(**)" && trim(substr($row->nombre_line_item, 0, 4)) != "(++)")) {
                        $imps_ayer = ($imps_ayer + $row->imps_ayer);
                        $clicks_ayer = ($clicks_ayer + $row->clicks_ayer);

                        $imps_siempre = ($imps_siempre + $row->imps_total);
                        $clicks_siempre = ($clicks_siempre + $row->clicks_total);

                        $total_inversion_neta = $total_inversion_neta + $row->inversion_neta;
                        $total_consumido = $total_consumido + $row->consumido;
                    }
                    $estilo = "";
                    if ($row->estado == "OK") {
                        $estilo = 'style="color: green;font-weight:bold;"';
                    } elseif ($row->estado == "Adelantada") {
                        $estilo = 'style="color: #000080;font-weight:bold;;"';
                    } elseif ($row->estado == "Finalizada") {
                        $estilo = 'style="color:#FF8C00;font-weight:bold;"';
                    } else {
                        $estilo = 'style="color: red;font-weight:bold;"';
                    }

                    $estilo_fila = "";
                    if ($row->fila_total_campania)
                        $estilo_fila = 'style="background-color: #FFC0CB;"';
                    ?>
                    <tr <?= $estilo_fila ?> >
                        <td><?= utf8_decode($row->nombre_anunciante) ?></td>
                        <td><?= utf8_decode($row->nombre_line_item) ?></td>
                        <td <?= $estilo ?>><?= $row->estado ?></td>
                        <td>
                            <?php
                            if (strtoupper($row->modalidad_de_compra) == "CPM")
                                echo number_format($row->necesarias_x_dia, 0, ',', '.') . " imps";
                            if (strtoupper($row->modalidad_de_compra) == "CPC")
                                echo number_format($row->necesarias_x_dia, 0, ',', '.') . " clicks";
                            /*
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
                              } */
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
                        <td><?= number_format($row->imps_ayer, 0, ',', '.') ?></td>
                        <td><?= number_format($row->clicks_ayer, 0, ',', '.') ?></td>
                        <td><?= str_replace(".", ",", $row->ctr_ayer) ?> %</td>
                        <td><?= $row->dias_restantes ?></td>
                        <td><?= number_format($row->conversiones_ayer, 0, ',', '.') ?></td>
                        <td><?= $row->adserver ?></td>
                        <td>
                            <?php
                            if ($row->valor > 0)
                                echo strtoupper($row->modalidad_de_compra) . " USD " . number_format($row->valor, 2, ',', '.');
                            else
                                echo "-";
                            ?>
                        </td>

                        <td><?= number_format($row->imps_total, 0, ',', '.') ?></td>
                        <td><?= number_format($row->clicks_total, 0, ',', '.') ?></td>
                        <td><?= str_replace(".", ",", $row->ctr_total) ?> %</td>
                        <td><?= number_format($row->conversiones_total, 0, ',', '.') ?></td>
                        <td><?= number_format($row->imps_contratadas, 0, ',', '.') ?></td>
                        <td><?= number_format($row->clicks_contratados, 0, ',', '.') ?></td>
                        <td>
                            <?php
                            if (strlen($row->exchange))
                                echo $row->exchange;
                            else
                                echo " N/A ";
                            ?>
                        </td>
                        <td>
                            <?php
                            if (strlen($row->ejecutiva))
                                echo $row->ejecutiva;
                            else
                                echo " N/A ";
                            ?>
                        </td>
                        <td>USD <?= number_format($row->inversion_neta, 2, ',', '.') ?></td>
                        <td>USD <?= number_format($row->consumido, 2, ',', '.') ?></td>
                        <td>USD <?= number_format(($row->inversion_neta - $row->consumido), 2, ',', '.') ?></td>
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
                        <td>
                            <?php
                            if (strlen($row->trafficker))
                                echo $row->trafficker;
                            else
                                echo " N/A ";
                            ?>
                        </td>
                        <td>
                            <?php
                            if ($row->fila_total_campania && $row->adserver == "DFP") {
                                ?>
                                <a href="" id="<?= $row->id ?>" class="link_ver_orden" >Ver</a>
                                <div id="loader_ver_<?= $row->id ?>" style="display:none"><img alt="cargando" height="10px" src="/images/ajax-loader.gif" /></div>
                                <?php
                            } else {
                                ?>
                                &nbsp;
                                <?php
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            echo $row->id;
                            ?>
                        </td>
                    </tr>
                    <?php
                }
                $ctr_total_ayer = (($clicks_ayer / $imps_ayer) * 100);
                $ctr_total_siempre = (($clicks_siempre / $imps_siempre) * 100);
            }
            ?>
        </tbody>
        <tfoot>
            <tr style="background-color: #E5E5E5;border-left: 1px solid #CCC;border-right: 1px solid #CCC;
                border-top: 1px solid #CCC;height: 15px"><td colspan="25"></td>
            </tr>
            <tr style="background-color: #E5E5E5;border: 1px solid #CCC;font-size: 12px;">
                <td colspan="25">
                    <div style="width: 100%">
                        <div style="float: left">CTR global del d&iacute;a: <b><?= number_format($ctr_total_ayer, 2, ',', '.') ?>%</b></div>
                        <div style="float: left;margin-left: 20px">CTR global de lo consumido: <b><?= number_format($ctr_total_siempre, 2, ',', '.') ?>%</b></div>
                        <div style="float: left;margin-left: 20px">Total inversi&oacute;n neta: <b>USD <?= number_format($total_inversion_neta, 2, ',', '.') ?></b></div>
                        <div style="float: left;margin-left: 20px">Total consumido al d&iacute;a: <b>USD <?= number_format($total_consumido, 2, ',', '.') ?></b></div>
                    </div>
                </td>
            </tr>
        </tfoot>
    </table>
</div>

<script type="text/javascript">
    jQuery.fn.dataTableExt.oSort['uk_date-asc'] = function(a, b) {
        var ukDatea = a.split('/');
        var ukDateb = b.split('/');

        var x = (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
        var y = (ukDateb[2] + ukDateb[1] + ukDateb[0]) * 1;

        return ((x < y) ? -1 : ((x > y) ? 1 : 0));
    };

    jQuery.fn.dataTableExt.oSort['uk_date-desc'] = function(a, b) {
        var ukDatea = a.split('/');
        var ukDateb = b.split('/');

        var x = (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
        var y = (ukDateb[2] + ukDateb[1] + ukDateb[0]) * 1;

        return ((x < y) ? 1 : ((x > y) ? -1 : 0));
    };

    jQuery.fn.dataTableExt.oSort['slo-asc'] = function(a, b) {
        var x = (a == "-") ? 0 : a.replace(/\./g, "").replace(/,/, ".");
        var y = (b == "-") ? 0 : b.replace(/\./g, "").replace(/,/, ".");
        x = parseFloat(x);
        y = parseFloat(y);
        return ((x < y) ? -1 : ((x > y) ? 1 : 0));
    };

    jQuery.fn.dataTableExt.oSort['slo-desc'] = function(a, b) {
        var x = (a == "-") ? 0 : a.replace(/\./g, "").replace(/,/, ".");
        var y = (b == "-") ? 0 : b.replace(/\./g, "").replace(/,/, ".");
        x = parseFloat(x);
        y = parseFloat(y);
        return ((x < y) ? 1 : ((x > y) ? -1 : 0));
    };

    jQuery.fn.dataTableExt.oSort['currency-asc'] = function(a, b) {
        /* Remove any commas (assumes that if present all strings will have a fixed number of d.p) */
        var x = (a == "-") ? 0 : a.replace(/\./g, "").replace(/,/, ".");
        var y = (b == "-") ? 0 : b.replace(/\./g, "").replace(/,/, ".");

        /* Remove the currency sign */
        x = x.substring(4);
        y = y.substring(4);

        /* Parse and return */
        x = parseFloat(x);
        y = parseFloat(y);
        //return x - y;
        return ((x < y) ? -1 : ((x > y) ? 1 : 0));
    };

    jQuery.fn.dataTableExt.oSort['currency-desc'] = function(a, b) {
        /* Remove any commas (assumes that if present all strings will have a fixed number of d.p) */
        var x = (a == "-") ? 0 : a.replace(/\./g, "").replace(/,/, ".");
        var y = (b == "-") ? 0 : b.replace(/\./g, "").replace(/,/, ".");

        /* Remove the currency sign */
        x = x.substring(4);
        y = y.substring(4);

        /* Parse and return */
        x = parseFloat(x);
        y = parseFloat(y);
        //return y - x;
        return ((x < y) ? 1 : ((x > y) ? -1 : 0));
    };

    function fnGetSelected(oTableLocal)
    {
        return oTableLocal.$('tr.row_selected');
    }

    function GridRowCount() {
        $('span.rowCount-grid').remove();
        $('input.expandedOrCollapsedGroup').remove();

        $('.dataTables_wrapper').find('[id|=group-id]').each(function() {
            var rowCount = $(this).nextUntil('[id|=group-id]').length;
            $(this).find('td').append($('<span />', {'class': 'rowCount-grid'}).append($('<b />', {'text': ''})));
        });

        $('.dataTables_wrapper').find('.dataTables_length').append($('<input />', {'type': 'button', 'class': 'expandedOrCollapsedGroup expanded', 'value': 'Contraer'}));

        $('.expandedOrCollapsedGroup').live('click', function() {
            if ($(this).hasClass('collapsed')) {
                $(this).addClass('expanded').removeClass('collapsed').val('Contraer').parents('.dataTables_wrapper').find('.collapsed-group').trigger('click');
            }
            else {
                $(this).addClass('collapsed').removeClass('expanded').val('Expandir').parents('.dataTables_wrapper').find('.expanded-group').trigger('click');
            }
        });
    }

    $().ready(function() {
        var oTable = $('#tbl_status_campanias').dataTable({
            "sPaginationType": "full_numbers",
            "aLengthMenu": [[10, 20, 50, 100, -1], [10, 20, 50, 100, "Todas"]],
            iDisplayLength: -1,
            "bJQueryUI": true,
            "bInfo": false,
            "aaSorting": [[27, "asc"]],
            "oLanguage": {
                "sSearch": "Buscar"
            },
            "sScrollY": 400,
            "sScrollX": "25%",
            "bScrollCollapse": true,
            "aoColumns": [
                {"bSortable": false, "sWidth": '200px'},
                {"bSortable": false, "sWidth": '240px'},
                {"bSortable": false, "sWidth": '90px'},
                {"bSortable": false, "sType": "slo", "sWidth": '100px'},
                {"bSortable": false, "sWidth": '100px'},
                {"bSortable": false, "sWidth": '70px'},
                {"bSortable": false, "sWidth": '30px'},
                {"bSortable": false, "sType": "slo", "sWidth": '30px'},
                {"bSortable": false, "sType": "slo", "sWidth": '20px'},
                {"bSortable": false, "sWidth": '20px'},
                {"bSortable": false, "sType": "slo", "sWidth": '80px'},
                {"bSortable": false, "sType": "slo", "sWidth": '100px'},
                {"bSortable": false, "sType": "slo", "sWidth": '50px'},
                {"bSortable": false, "sType": "slo", "sWidth": '80px'},
                {"bSortable": false, "sType": "slo", "sWidth": '80px'},
                {"bSortable": false, "sType": "slo", "sWidth": '50px'},
                {"bSortable": false, "sType": "slo", "sWidth": '50px'},
                {"bSortable": false, "sType": "slo", "sWidth": '120px'},
                {"bSortable": false, "sWidth": '80px'},
                {"bSortable": false, "sWidth": '120px'},
                {"bSortable": false, "sType": "currency", "sWidth": '120px'},
                {"bSortable": false, "sType": "currency", "sWidth": '120px'},
                {"bSortable": false, "sType": "currency", "sWidth": '120px'},
                {"bSortable": false, "sType": "uk_date", "sWidth": '50px'},
                {"bSortable": false, "sType": "uk_date", "sWidth": '50px'},
                {"bSortable": false, "sWidth": '160px'},
                {"bSortable": false, "sWidth": '40px'},
                {"bVisible": false}
            ],
            "fnRowCallback": function(nRow, aData, iDisplayIndex) {
                $("#cantidad_segmentaciones").html("<b>" + (iDisplayIndex + 1) + "</b>");
            },
            "fnDrawCallback": function(nRow, aData, iDisplayIndex) {
                var groupCount = 0;
                $('.dataTables_wrapper').find('[id|=group-id]').each(function() {
                    groupCount++;
                });
                $("#cantidad_campanias").html("<b>" + groupCount + "</b>");
            }
        }).rowGrouping({
            bExpandableGrouping: true
        });

        GridRowCount();

        var groupCount = 0;
        $('.dataTables_wrapper').find('[id|=group-id]').each(function() {
            groupCount++;
        });
        $("#cantidad_campanias").html("<b>" + groupCount + "</b>");

        $("#tbl_status_campanias_wrapper").css("width", "960px");
        /*new FixedColumns( oTable, {
         "iLeftColumns": 2,
         "iLeftWidth": 350
         } );*/

        $('#tbl_status_campanias tr').click(function() {
            $(this).toggleClass('row_selected');
        });

        /* Init the table */
        //var oTable = $('#tbl_status_campanias').dataTable( );

        $("#filtro_adserver").html(fnCreateSelect(oTable.fnGetColumnData(10)));

        $('select', $("#filtro_adserver")).change(function() {
            oTable.fnFilter($(this).val(), 10);
            $("#cantidad_segmentaciones").html("<b>" + oTable.fnSettings().fnRecordsDisplay() + "</b>");
        });

        $("#filtro_ejecutiva_cuentas").html(fnCreateSelect(oTable.fnGetColumnData(19)));

        $('select', $("#filtro_ejecutiva_cuentas")).change(function() {
            oTable.fnFilter($(this).val(), 19);
            $("#cantidad_segmentaciones").html("<b>" + oTable.fnSettings().fnRecordsDisplay() + "</b>");
        });

        $("#filtro_ejecutiva_medios").html(fnCreateSelect(oTable.fnGetColumnData(25)));

        $('select', $("#filtro_ejecutiva_medios")).change(function() {
            oTable.fnFilter($(this).val(), 25);
            $("#cantidad_segmentaciones").html("<b>" + oTable.fnSettings().fnRecordsDisplay() + "</b>");
        });

        $("#cantidad_segmentaciones").html("<b>" + oTable.fnGetData().length + "</b>");
        //$("#cantidad_campanias").html("<b>"+oTable.fnGetData().length+"</b>");

        $(".link_ver_orden").click(function() {

            event.preventDefault();
            var id = $(this).attr('id');

            $("#loader_ver_" + id).css("display", "inline");

            var now = new Date();
            var seconds = now.getSeconds() + 'o' + now.getMinutes();

            $("#td_datos_campania").load("/statuscampanias/obtener_datos_orden/" + id + "/" + seconds, function() {
                $("#loader_ver_" + id).css("display", "none");
            });
        });
    });
</script>