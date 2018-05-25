<?php
require_once 'application/libraries/TableExtractor.php';
require_once 'application/libraries/config.inc';
require_once 'application/libraries/Caller.php';
require_once 'application/libraries/TableExtractor.php';

//header('Content-Type: text/html; charset=ISO-8859-1');
header("Content-type: application/vnd.ms-excel; name='excel' charset=utf-8");
header("Content-type: application/x-msexcel; name='excel'; charset=utf-8");
header("Content-Disposition: attachment; filename=\"MediaFem - Reporte por Sitios Formatos.xls\"");
header("Content-Transfer-Encoding: binary");
header("Pragma: no-cache");
header("Expires: 0");

$interval = "";

//new_var_dump($_POST);

$data_table = utf8_encode($_POST['datos_a_enviar']);
$data_table = utf8_decode($data_table);




//$data_table = str_replace("Impresiones","Pais", "Formatos", $data_table) ;

$tx = new tableExtractor;

$tx->source = $data_table;
$tx->anchor = '<h2>Sitios</h2>';
$tx->anchorWithin = true;
$arr_tabla = $tx->extractTable();

$total_ctr = 0;
$total_imps = 0;
$total_clicks = 0;
$notacion = $_POST['notacion'];




foreach ($arr_tabla as $row) {

    $site_name = $row['Pais'];
    $size = $row['Formato'];
    $imps = $row['Impresiones'];


    if ($site_name != "Totales:") {
        $rImps = str_replace(".", "", $imps);
        $rImps = str_replace(",", "", $rImps);

        $total_imps = $total_imps + str_replace(".", "", $rImps);
        $arr_sites[] = array('site_name' => $site_name, 'formato'=>$size, 'imps' => $imps, 'cats' => $cats);
    }
}

$texto_paises = $_POST['texto_pais'];


if($texto_paises != "Todos"){
    $texto_paises = substr(htmlentities($texto_paises, ENT_QUOTES, 'UTF-8'), 0, - 2);
}

$intervalo = $_POST['interval'];
if($intervalo=="yesterday"){
    $interval="Ayer";
}elseif($intervalo=="last_7_days"){
     $interval="Ultimos siete dias";
}elseif($intervalo=="last_7_days"){
     $interval="Ultimos siete dias";
}elseif($intervalo=="last_month"){
     $interval="Ultimos Mes";
}elseif($intervalo=="month_to_date"){
     $interval="Este Mes";
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
        <td colspan="2"><b>Intervalo: <?= $interval ?></b></td>
    </tr>
    <tr>
        <td colspan="2"><b>Formatos: <?= $texto_canales_tematicos ?></b></td>
    </tr>


    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td style="background-color: #E5E5E5;border-top: 1px solid #000;border-bottom: 1px solid #000">Pais</td>
        <td style="background-color: #E5E5E5;border-top: 1px solid #000;border-bottom: 1px solid #000">Formatos</td>
        <td style="background-color: #E5E5E5;border-top: 1px solid #000;border-bottom: 1px solid #000">Impresiones</td>
    </tr>
    <?php
    $contador = 0;
    foreach ($arr_sites as $site) {
        $contador++;

        if ($contador > 1) {
            $color = 'background-color:#E2E4FF';
            $contador = 0;
        } else {
            $color = 'background-color:#FFFFFF';
        }
        ?>
        <tr>
            <td style="<?= $color ?>"><?= $site["site_name"] ?></td>
            <td style="<?= $color ?>"><?= $site["formato"] ?></td>
            <td style="text-align: right;<?= $color ?>"><?= $site["imps"] ?></td>
        </tr>
        <?php
    }
    ?>
    <tr style="font-weight: bold">
        <td style="background-color: #E5E5E5;border-top: 1px solid #000;width: 280px">Totales</td>
        <td style="background-color: #E5E5E5;border-top: 1px solid #000;width: 100px">&nbsp;</td>
        <td style="background-color: #E5E5E5;border-top: 1px solid #000;text-align: right;width: 100px">
            <?php
            if ($this->user_notacion == 0) {
                echo number_format($total_imps, 0, '.', ',');
            } else if ($this->user_notacion == 1) {
                echo number_format($total_imps, 0, ',', '.');
            }
            ?>
        </td>
    </tr>
</table>
