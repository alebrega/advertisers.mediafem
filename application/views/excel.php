<?php
require_once 'application/libraries/TableExtractor.php';

$anunciante = $_POST['nombre_anunciante_excel'];
$rango = $_POST['rango_excel'];
$fecha_desde = $_POST['fecha_inicio_excel'];
$fecha_hasta = $_POST['fecha_fin_excel'];

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
} elseif ($rango == "lifetime") {
    $fecha_inicio = "01/01/2010";
    $fecha_fin = date('d/m/Y');
}

header("Content-type: application/vnd.ms-excel; name='excel'");

if ($rango == "lifetime") {
    header("Content-Disposition: filename=MediaFem - $anunciante.xls");
}else{
    header("Content-Disposition: filename=MediaFem - $anunciante - $fecha_inicio al $fecha_fin.xls");
}

header("Pragma: no-cache");
header("Expires: 0");

$interval = "";

$data_table = $_POST['datos_a_enviar'];

$data_table = utf8_decode($data_table);

$tx = new tableExtractor;

$tx->source = $data_table;
$tx->anchor = '<h2>Sitios</h2>';
$tx->anchorWithin = true;
$arr_tabla = $tx->extractTable();

$total_ctr = 0;
$total_imps = 0;
$total_clicks = 0;

$agrupar_por = $_POST['agrupar_por'];

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

$grupo = "";

$i = 0;

foreach ($arr_tabla as $clave => $row) {
    $i++;
    if ($i == 1 || $i == 2)
        continue;

    if ($agrupar_por == "anuncio") {
        if (isset($row['Creatividad'])) {
            $grupo = trim($row['Creatividad']);
        }
    } elseif ($agrupar_por == "pais") {
        if (isset($row['Pais'])) {
            $grupo = trim($row['Pais']);
        }
    } elseif ($agrupar_por == "dia") {
        if (isset($row['Dia'])) {
            $grupo = trim($row['Dia']);
        }
    } elseif ($agrupar_por == "mes") {
        if (isset($row['Mes'])) {
            $grupo = trim($row['Mes']);
        }
    } elseif ($agrupar_por == "imps") {
        if (isset($row['Impresiones'])) {
            $grupo = trim($row['Impresiones']);
        }
    } elseif ($agrupar_por == "clicks") {
        if (isset($row['Clicks'])) {
            $grupo = trim($row['Clicks']);
        }
    } elseif ($agrupar_por == "ctr") {
        if (isset($row['CTR'])) {
            $grupo = trim($row['CTR']);
        }
    }

    if (isset($row['Impresiones']))
        $imps = trim($row['Impresiones']);
    if (isset($row['Clicks']))
        $clicks = trim($row['Clicks']);
    if (isset($row['CTR']))
        $ctr = trim($row['CTR']);

    if ($grupo != "Totales" && $grupo != "Promedios" && $grupo != "") {

        if (!count($columnas)) {
            foreach ($row as $clave_columna => $col) {
                $columnas[] = $clave_columna;
            }
        }

        if (isset($row['Impresiones']))
            $total_imps = $total_imps + str_replace(".", "", $imps);

        if (isset($row['Clicks']))
            $total_clicks = $total_clicks + str_replace(".", "", $clicks);

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
        <td>Anunciante:</td>
        <td><?= htmlentities($anunciante, ENT_QUOTES, 'UTF-8') ?></td>
    </tr>
    <tr>
        <?php
        if ($rango != "lifetime") {
            ?>
            <td>Fechas del reporte:</td>
            <td><?= $fecha_inicio . " al " . $fecha_fin ?></td>
            <?php
        } else {
            ?>
            <td colspan="2">&nbsp;</td>
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
                <td style="<?= $color ?>;text-align: left"><?= $row[$columnas[$i]] ?></td>
                <?php
            }
            ?>
        </tr>
        <?php
    }
    ?>
    <tr style="background-color: #E5E5E5;font-weight:bold;">
        <?php
        for ($i = 0; $i < count($columnas); $i++) {
            if ($columnas[$i] == "Clicks") {
                ?>
                <td style="text-align: left"><?= number_format($total_clicks, 0, ',', '.') ?></td>
                <?php
            } elseif ($columnas[$i] == "Impresiones") {
                ?>
                <td style="text-align: left"><?= number_format($total_imps, 0, ',', '.') ?></td>
                <?php
            } elseif ($columnas[$i] == "CTR") {
                ?>
                <td style="text-align: left"><?= number_format($total_ctr, 2, ',', '.') . "%" ?></td>
                <?php
            } else {
                ?>
                <td style="text-align: left">&nbsp;</td>
                <?php
            }
        }
        ?>
    </tr>
</table>
