<?php
require_once 'application/libraries/TableExtractor.php';
require_once 'application/libraries/config.inc';
require_once 'application/libraries/Caller.php';
require_once 'application/libraries/TableExtractor.php';
/*
header('Content-Type: text/html; charset=ISO-8859-1');
header("Content-type: application/vnd.ms-excel; name='excel'");
header("Content-type: application/x-msexcel; name='excel'; charset=utf-8");
header("Content-Disposition: attachment; filename=\"MediaFem - Reporte por Sitios Formatos.xls\"");
header("Content-Transfer-Encoding: binary");
header("Pragma: no-cache");
header("Expires: 0");
*/
$interval = "";

$data_table = utf8_encode($_POST['datos_a_enviar']);
$data_table = utf8_decode($data_table);
$data_table = str_replace("Sitio Web", "SitioWeb", $data_table);
$data_table = str_replace("Canales Tematicos", "CanalesTematicos", $data_table) ;

var_dump($data_table);
die();

$tx = new tableExtractor;

$tx->source = $data_table;
$tx->anchor = '<h2>Sitios</h2>';
$tx->anchorWithin = true;
$arr_tabla = $tx->extractTable();

$total_ctr = 0;
$total_imps = 0;
$total_clicks = 0;

$rango = $_POST['interval'];
$texto_fecha = "";

if ($rango == "today") {
    $texto_fecha = date("d/m/y");
} elseif ($rango == "yesterday") {
    $dia = time() - (1 * 24 * 60 * 60); //Te resta un dia (2*24*60*60) te resta dos y //asi...
    $texto_fecha = date('d/m/y', $dia);
}

foreach ($arr_tabla as $row) {

    $pais = $row['Pais'];
    $size = $row['Formato'];
    $imps = $row['Impresiones'];


    if ($pais != "Totales:") {
        $total_imps = $total_imps + str_replace(".", "", $imps);
        $arr_reporte[] = array('pais' => $pais, 'formato'=>$size, 'imps' => $imps);
    }
}

$texto_paises = $_POST['texto_pais'];

if($texto_paises != "Todos"){
    $texto_paises = substr(htmlentities($texto_paises, ENT_QUOTES, 'UTF-8'), 0, - 2);
}

$texto_canales_tematicos = $_POST['texto_categoria'];

if($texto_canales_tematicos != "Todos"){
    $texto_canales_tematicos = substr(htmlentities($texto_canales_tematicos, ENT_QUOTES, 'UTF-8'), 0, - 2);
}

?>
<table>
    <tr>
        <td colspan="2"><b>Sitios de MediaFem</b></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td colspan="2"><b>Pa&iacute;ses: <?= $texto_paises ?></b></td>
    </tr>
    <tr>
        <td colspan="2"><b>Canales Tem&aacute;ticos: <?= $texto_canales_tematicos ?></b></td>
    </tr>
    <?php
    if (strlen($texto_fecha)) {
        ?>
        <tr>
            <td colspan="2"><b>Fecha: <?= $texto_fecha ?></b></td>
        </tr>
        <?php
    }
    ?>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td style="background-color: #E5E5E5;border-top: 1px solid #000;border-bottom: 1px solid #000">Pa&iacute;ses:</td>
        <td style="background-color: #E5E5E5;border-top: 1px solid #000;border-bottom: 1px solid #000">Formato</td>
        <td style="background-color: #E5E5E5;border-top: 1px solid #000;border-bottom: 1px solid #000">Impresiones</td>
    </tr>
    <?php
    $contador = 0;
    foreach ($arr_reporte as $row) {
        $contador++;

        if ($contador > 1) {
            $color = 'background-color:#E2E4FF';
            $contador = 0;
        } else {
            $color = 'background-color:#FFFFFF';
        }
        ?>
        <tr>
            <td style="<?= $color ?>"><?= $row["pais"] ?></td>
            <td style="<?= $color ?>"><?= $row["formato"] ?></td>

            <td style="text-align: right;<?= $color ?>"><?= $row["imps"] ?></td>
        </tr>
        <?php
    }
    ?>
    <tr style="font-weight: bold">
        <td style="background-color: #E5E5E5;border-top: 1px solid #000;width: 280px">Totales</td>
        <td style="background-color: #E5E5E5;border-top: 1px solid #000;width: 100px">&nbsp;</td>
        <td style="background-color: #E5E5E5;border-top: 1px solid #000;text-align: right;width: 100px"><?= number_format($total_imps, 0, ',', '.') ?></td>
    </tr>
</table>
