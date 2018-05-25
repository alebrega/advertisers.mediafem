<?php
require_once 'application/libraries/TableExtractor.php';

$lineItem = $_POST['id_lineItem'];

$anunciante = $_POST['nombre_anunciante_excel_' . $lineItem];
$orden = $_POST['nombre_orden_excel_' . $lineItem];
$rango = $_POST['rango_excel_' . $lineItem];
$fecha_desde = $_POST['fecha_inicio_excel_' . $lineItem];
$fecha_hasta = $_POST['fecha_fin_excel_' . $lineItem];
$empresa_campania = $_POST['empresa_campania_orden_' . $lineItem];


if ($rango == "today") {
    $fecha_inicio = date('d/m/Y');
    $fecha_fin = date('d/m/Y');
} elseif ($rango == "yesterday") {
    $fecha_inicio = date('d/m/Y', strtotime("-1 day"));
    $fecha_fin = date('d/m/Y', strtotime("-1 day"));
} elseif ($rango == "last_7_days") {
    $fecha_inicio = date('d/m/Y', strtotime("-7 days"));
    $fecha_fin = date('d/m/Y');
} elseif ($rango == "month_to_date") {
    $fecha_inicio = date('d/m/Y', strtotime('this month', strtotime(date('Y-m-01'))));
    $fecha_fin = date('d/m/Y');
} elseif ($rango == "last_month") {
    $fecha_inicio = date('d/m/Y', strtotime('-1 month', strtotime(date('Y-m-01'))));
    $fecha_fin = date('d/m/Y', strtotime("-" . Date("d") . " days"));
} elseif ($rango == "especific") {
    $fecha_inicio = str_replace("-", "/", $fecha_desde);
    $fecha_fin = str_replace("-", "/", $fecha_hasta);
    //$fecha_fin = date('d/m/Y', strtotime("-" . Date("d") . " days"));
} elseif ($rango == "lifetime") {
    $fecha_inicio = "01/01/2011";
    $fecha_fin = date('d/m/Y');
}

header("Content-type: application/vnd.ms-excel; name='excel'");
header("Content-type: application/x-msexcel; name='excel'; charset=utf-8");

if ($empresa_campania == 0) {
    if ($rango == "lifetime") {
        header("Content-Disposition: attachment; filename=\"MediaFem - $orden.xls\"");
    } else {
        header("Content-Disposition: attachment; filename=\"MediaFem - $orden - $fecha_inicio al $fecha_fin.xls\"");
    }
} else {
    if ($rango == "lifetime") {
        header("Content-Disposition: attachment; filename=\"AdTomatik - $orden.xls\"");
    } else {
        header("Content-Disposition: attachment; filename=\"AdTomatik - $orden - $fecha_inicio al $fecha_fin.xls\"");
    }
}

header("Content-Transfer-Encoding: binary");
header("Pragma: no-cache");
header("Expires: 0");

$interval = "";

$data_table = $_POST['datos_a_enviar_' . $lineItem];

$data_table = utf8_decode($data_table);

$tx = new tableExtractor;

$tx->source = $data_table;

if ($empresa_campania == 0) {
    $tx->anchor = '<h2>MediaFem</h2>';
} else {
    $tx->anchor = '<h2>AdTomatik</h2>';
}

$tx->anchorWithin = true;
$arr_tabla = $tx->extractTable();

$total_ctr = 0;
$total_imps = 0;
$total_clicks = 0;
$total_convs = 0;
$total_costo = 0;

$total_ctr = $_POST['total_ctr_excel_' . $lineItem];
$total_imps = $_POST['total_imps_excel_' . $lineItem];
$total_clicks = $_POST['total_clicks_excel_' . $lineItem];
$total_convs = $_POST['total_convs_excel_' . $lineItem];
$total_costo = $_POST['total_costo_excel_' . $lineItem];

$columnas = null;

$verificar_col = true;

$i = 0;
foreach ($arr_tabla as $clave => $row) {
    $i++;
    if ($i == 1 || $i == 2)
        continue;

    if ($verificar_col) {
        foreach ($row as $clave_columna => $col) {
            $indice_col_principal = $clave_columna;
            break;
        }
        $verificar_col = false;
    }

    $col_principal = trim((String) $row[$indice_col_principal]);

    if ($col_principal != "Totales" && $col_principal != "Promedios" && strlen($col_principal)) {

        if (!count($columnas)) {
            foreach ($row as $clave_columna => $col) {
                $columnas[] = $clave_columna;
            }
        }

        for ($i = 0; $i < count($columnas); $i++) {
            $data[$columnas[$i]] = $row[$columnas[$i]];
        }

        $data_tabla[] = $data;
    }
}
?>
<table CELLPADDING="0px" CELLSPACING="0px">
    <tr>
        <td colspan="2"><b><?php if($empresa_campania == 0){  echo 'MediaFem'; }else{ echo 'AdTomatik'; } ?></b></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>Anunciante:</td>
        <td colspan="3"><?= htmlentities($anunciante, ENT_QUOTES, 'UTF-8') ?></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>Campa&ntilde;a:</td>
        <td colspan="3"><?= htmlentities($orden, ENT_QUOTES, 'UTF-8') ?></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <?php
        if ($rango != "lifetime") {
            ?>
            <td>Fechas del reporte:</td>
            <td colspan="3"><?= $fecha_inicio . " al " . $fecha_fin ?></td>
            <?php
        } else {
            ?>
            <td colspan="4">&nbsp;</td>
            <?php
        }
        ?>
    </tr>

    <tr>
        <td>&nbsp;</td>
    </tr>
</table>
<table CELLPADDING="0px" CELLSPACING="0px" border="1">
    <tr>
        <?php
        for ($i = 0; $i < count($columnas); $i++) {
            $columnas[$i] = str_replace('í', 'i', $columnas[$i]);
            $columnas[$i] = str_replace('ó', 'o', $columnas[$i]);
            ?>
            <td style="font-weight:bold;background-color: #E5E5E5;text-align: left"><?= $columnas[$i] ?></td>
            <?php
        }
        ?>
    </tr>
    <?php
    $contador = 0;
    foreach ($data_tabla as $row) {
        $contador++;

        if ($contador > 1) {
            $color = 'background-color:#E2E4FF';
            $contador = 0;
        } else {
            $color = 'background-color:#FFFFFF';
        }
        ?>
        <tr>
            <?php
            for ($i = 0; $i < count($columnas); $i++) {
                ?>
                <td style="<?= $color ?>;text-align: left"><?= $row[$columnas[$i]] ?></td>
                <?php
            }
            ?>
        </tr>
        <?php
    }
    ?>
    <tr style="font-weight:bold;">
        <?php
        for ($i = 0; $i < count($columnas); $i++) {
            if ($columnas[$i] == "Clicks") {
                ?>
                <td style="background-color: #E5E5E5;text-align: left"><?= $total_clicks ?></td>
                <?php
            } elseif ($columnas[$i] == "Impresiones") {
                ?>
                <td style="background-color: #E5E5E5;text-align: left"><?= $total_imps ?></td>
                <?php
            } elseif ($columnas[$i] == "CTR") {
                ?>
                <td style="background-color: #E5E5E5;text-align: left"><?= $total_ctr ?></td>
                <?php
            } elseif ($columnas[$i] == "Conversiones") {
                ?>
                <td style="background-color: #E5E5E5;text-align: left"><?= $total_convs ?></td>
                <?php
            } elseif ($columnas[$i] == "Costo") {
                ?>
                <td style="background-color: #E5E5E5;text-align: left"><?= $total_costo ?></td>

                <?php
            } else {
                ?>
                <td style="background-color: #E5E5E5;text-align: left">&nbsp;</td>
                <?php
            }
        }
        ?>
    </tr>
</table>
