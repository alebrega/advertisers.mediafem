<?php
require_once 'application/libraries/TableExtractor.php';
require_once 'application/libraries/functions.php';

$rango = $_POST['rango_excel'];
$fecha_desde = $_POST['fecha_inicio_excel'];
$fecha_hasta = $_POST['fecha_fin_excel'];

if ($rango == "today") {
    $fecha_inicio = date('d/m/Y');
    $fecha_fin = date('d/m/Y');

    $periodo = 'Hoy';

} elseif ($rango == "yesterday") {
    $fecha_inicio = date('d/m/Y', strtotime("-1 day"));
    $fecha_fin = date('d/m/Y', strtotime("-1 day"));

    $periodo = 'Ayer';

} elseif ($rango == "last_7_days") {
    $fecha_inicio = date('d/m/Y', strtotime("-7 days"));
    $fecha_fin = date('d/m/Y');

    $periodo = 'Ultimos 7 dias';

} elseif ($rango == "month_to_date") {
    $fecha_inicio = date('d/m/Y', strtotime('this month', strtotime(date('Y-m-01'))));
    $fecha_fin = date('d/m/Y');

    $periodo = getMesEsp(date('m')) . ' de ' . date('Y');

} elseif ($rango == "last_month") {
    $fecha_inicio = date('d/m/Y', strtotime('-1 month', strtotime(date('Y-m-01'))));
    $fecha_fin = date('d/m/Y', strtotime("-" . Date("d") . " days"));

    $anio = date('Y');
    $mes = getMesEsp(date('m') - 1);

    if( $mes == 'Diciembre' )
        $anio -= 1;

    $periodo = $mes . ' de ' . $anio;

} elseif ($rango == "especific") {
    $fecha_inicio = str_replace("-", "/", $fecha_desde);
    $fecha_fin = str_replace("-", "/", $fecha_hasta);
} elseif ($rango == "lifetime") {
    $fecha_inicio = "01/01/2011";
    $fecha_fin = date('d/m/Y');

    $periodo = 'Siempre';

}

header("Content-type: application/vnd.ms-excel; name='excel'");
header("Content-type: application/x-msexcel; name='excel'; charset=utf-8");

if ($rango == "lifetime") {
    header("Content-Disposition: attachment; filename=\"MediaFem.xls\"");
}else{
    header("Content-Disposition: attachment; filename=\"MediaFem - $fecha_inicio al $fecha_fin.xls\"");
}

header("Content-Transfer-Encoding: binary");
header("Pragma: no-cache");
header("Expires: 0");

$interval = "";

$data_table = $_POST['datos_a_enviar'];

//$data_table = utf8_decode($data_table);

$tx = new tableExtractor;

$tx->source = $data_table;
$tx->anchor = '<h2>MediaFem</h2>';
$tx->anchorWithin = true;
$arr_tabla = $tx->extractTable();

$total_ctr = 0;
$total_imps = 0;
$total_clicks = 0;

/*
  $rango = $_POST['interval'];
  $texto_fecha = "";

  if ($rango == "today") {
  $texto_fecha = date("d/m/y");
  } elseif ($rango == "yesterday") {
  $dia = time() - (1 * 24 * 60 * 60); //Te resta un dia (2*24*60*60) te resta dos y //asi...
  $texto_fecha = date('d/m/y', $dia);
  }
 */

$columnas = null;

$verificar_col = true;

$i = 0;
foreach ($arr_tabla as $clave => $row) {
    $i++;
    if ($i == 1)
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

        if (isset($row['Impresiones']))
            $total_imps = $total_imps + str_replace(".", "", $row['Impresiones']);

        if (isset($row['Clicks']))
            $total_clicks = $total_clicks + str_replace(".", "", $row['Clicks']);

        for ($i = 0; $i < count($columnas); $i++) {
            $data[$columnas[$i]] = $row[$columnas[$i]];
        }

        $data_tabla[] = $data;
    }
}

if ($total_imps && $total_clicks) {
    $total_ctr = (($total_clicks / $total_imps) * 100);
}
?>

<table CELLPADDING="0px" CELLSPACING="0px">
    <tr>
        <td colspan="2"><b>MediaFem</b></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <?php if ($rango == "especific") { ?>
                <td>Periodo del reporte:</td>
                <td colspan="3"><?= $fecha_inicio . " al " . $fecha_fin ?></td>
        <?php }else{ ?>
                <td>Periodo del reporte:</td>
                <td colspan="3"><?= $periodo ?>.</td>
        <?php } ?>
    </tr>

    <tr>
        <td>&nbsp;</td>
    </tr>
</table>
<table CELLPADDING="0px" CELLSPACING="0px" border="1">
    <tr>
        <?php
        for ($i = 0; $i < count($columnas); $i++) {
            $columnas[$i] = str_replace('í' , 'i' , $columnas[$i]);
            $columnas[$i] = str_replace('ó' , 'o' , $columnas[$i]);
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
            <td style="<?= $color ?>;text-align: left"><?= $row[$columnas[$i]]; ?></td>
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
                <td style="background-color: #E5E5E5;text-align: left">-</td>
                <?php
            } elseif ($columnas[$i] == "Impresiones") {
                ?>
                <td style="background-color: #E5E5E5;text-align: left">-</td>
                <?php
            } elseif ($columnas[$i] == "CTR") {
                ?>
                <td style="background-color: #E5E5E5;text-align: left">-</td>
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
