<?php

function new_var_dump( $array ){
    echo "<pre>";
    var_dump($array);
    echo "</pre>";
}

function redirect_login_js() {
    if(ENVIRONMENT == 'production'){
        echo "<script type='text/javascript'>
              window.location='https://www.mediafem.com/anunciantes/';
              </script>";
        die();
    }
    /*else{
        echo "<script type='text/javascript'>
              window.location='/auth/login';
              </script>";
        die();
    }*/
}

function DateToQuotedMySQLDate($Fecha) {
    if ($Fecha <> "") {
        $trozos = explode("/", $Fecha, 3);
        return "'" . $trozos[2] . "-" . $trozos[1] . "-" . $trozos[0] . "'";
    } else {
        return "NULL";
    }
}

function MySQLDateToDate($MySQLFecha) {
    if (($MySQLFecha == "") or ($MySQLFecha == "0000-00-00")) {
        return "";
    } else {
        return date("d/m/Y", strtotime($MySQLFecha));
    }
}

function ColumnHourToDate($fecha) {
    if (($fecha == "") or ($fecha == "0000-00-00")) {
        return "";
    } else {
        return date("d/m/Y H:i", strtotime($fecha));
    }
}

function ColumnDayToDate($fecha) {
    if (($fecha == "") or ($fecha == "0000-00-00")) {
        return "";
    } else {
        return date("d/m/Y", strtotime($fecha));
    }
}

function ColumnMonthToDate($fecha) {
    if (($fecha == "") or ($fecha == "0000-00-00")) {
        return "";
    } else {
        return getMesEsp(date("m", strtotime($fecha)));
    }
}

function MySQLDateToDateDatepicker($MySQLFecha) {
    if (($MySQLFecha == "") or ($MySQLFecha == "0000-00-00")) {
        return "";
    } else {
        return date("d-m-Y", strtotime($MySQLFecha));
    }
}

function utf8_urldecode($str) {
    $str = preg_replace("/%u([0-9a-f]{3,4})/i", "&#x\\1;", urldecode($str));
    return html_entity_decode($str, null, 'UTF-8');
    ;
}

function utf8RawUrlDecode($source) {
    $decodedStr = "";
    $pos = 0;
    $len = strlen($source);
    while ($pos < $len) {
        $charAt = substr($source, $pos, 1);
        if ($charAt == "%") {
            $pos++;
            $charAt = substr($source, $pos, 1);
            if ($charAt == "u") {
// we got a unicode character
                $pos++;
                $unicodeHexVal = substr($source, $pos, 4);
                $unicode = hexdec($unicodeHexVal);
                $entity = "&#" . $unicode . ";";
                $decodedStr .= utf8_encode($entity);
                $pos += 4;
            } else {
// we have an escaped ascii character
                $hexVal = substr($source, $pos, 2);
                $decodedStr .= chr(hexdec($hexVal));
                $pos += 2;
            }
        } else {
            $decodedStr .= $charAt;
            $pos++;
        }
    }
    return $decodedStr;
}


function mesEspaniol($mes){
    switch ($mes) {
        case 'January':
            $mes = 'Enero';
            break;
        case 'February':
            $mes = 'Febrero';
            break;
        case 'March':
            $mes = 'Marzo';
            break;
        case 'April':
            $mes = 'Abril';
            break;
        case 'May':
            $mes = 'Mayo';
            break;
        case 'June':
            $mes = 'Junio';
            break;
        case 'July':
            $mes = 'Julio';
            break;
        case 'August':
            $mes = 'Agosto';
            break;
        case 'September':
            $mes = 'Septiembre';
            break;
        case 'October':
            $mes = 'Octubre';
            break;
        case 'November':
            $mes = 'Noviembre';
            break;
        case 'December':
            $mes = 'Diciembre';
            break;
        default:
            break;
    }

    return $mes;
}

function getMesEsp($mes) {

    if ($mes == "1")
        return "Enero";
    if ($mes == "2")
        return "Febrero";
    if ($mes == "3")
        return "Marzo";
    if ($mes == "4")
        return "Abril";
    if ($mes == "5")
        return "Mayo";
    if ($mes == "6")
        return "Junio";
    if ($mes == "7")
        return "Julio";
    if ($mes == "8")
        return "Agosto";
    if ($mes == "9")
        return "Septiembre";
    if ($mes == "10")
        return "Octubre";
    if ($mes == "11")
        return "Noviembre";
    if ($mes == "12" || $mes == "0")
        return "Diciembre";
}

function arreglar_cadena($rb) {
    $rb = str_replace("Ã¡", "&aacute;", $rb);
    $rb = str_replace("Ã©", "&eacute;", $rb);
    $rb = str_replace("Â®", "&reg;", $rb);
    $rb = str_replace("Ã­", "&iacute;", $rb);
    $rb = str_replace("ï¿½", "&iacute;", $rb);
    $rb = str_replace("Ã³", "&oacute;", $rb);
    $rb = str_replace("Ãº", "&uacute;", $rb);
    $rb = str_replace("n~", "&ntilde;", $rb);
    $rb = str_replace("Âº", "&ordm;", $rb);
    $rb = str_replace("Âª", "&ordf;", $rb);
    $rb = str_replace("ÃƒÂ¡", "&aacute;", $rb);
    $rb = str_replace("Ã±", "&ntilde;", $rb);
    $rb = str_replace("Ã‘", "&Ntilde;", $rb);
    $rb = str_replace("ÃƒÂ±", "&ntilde;", $rb);
    $rb = str_replace("n~", "&ntilde;", $rb);
    $rb = str_replace("Ãš", "&Uacute;", $rb);
    return $rb;
}

function arreglar_string($rb) {
    $rb = str_replace("Ã¡", "á", $rb);
    $rb = str_replace("Ã©", "&eacute;", $rb);
    $rb = str_replace("Â®", "&reg;", $rb);
    $rb = str_replace("Ã­", "&iacute;", $rb);
    $rb = str_replace("ï¿½", "í", $rb);
    $rb = str_replace("Ã³", "&oacute;", $rb);
    $rb = str_replace("Ãº", "&uacute;", $rb);
    $rb = str_replace("n~", "ñ", $rb);
    $rb = str_replace("Âº", "&ordm;", $rb);
    $rb = str_replace("Âª", "&ordf;", $rb);
    $rb = str_replace("ÃƒÂ¡", "&aacute;", $rb);
    $rb = str_replace("Ã±", "&ntilde;", $rb);
    $rb = str_replace("Ã‘", "&Ntilde;", $rb);
    $rb = str_replace("ÃƒÂ±", "&ntilde;", $rb);
    $rb = str_replace("n~", "&ntilde;", $rb);
    $rb = str_replace("Ãš", "&Uacute;", $rb);
    return $rb;
}

function corregir_acentos($cadena) {
    $search = explode(",", "á,é,í,ó,ú,ñ,Á,É,Í,Ó,Ú,Ñ,Ã¡,Ã©,Ã­,Ã³,Ãº,Ã±,ÃÃ¡,ÃÃ©,ÃÃ­,ÃÃ³,ÃÃº,ÃÃ±,Ã“,Ã ,Ã‰,Ã ,Ãš,â€œ,â€ ,Â¿,ü");
    $replace = explode(",", "á,é,í,ó,ú,ñ,Á,É,Í,Ó,Ú,Ñ,á,é,í,ó,ú,ñ,Á,É,Í,Ó,Ú,Ñ,Ó,Á,É,Í,Ú,\",\",¿,&uuml;");
    $cadena = str_replace($search, $replace, $cadena);

    return $cadena;
}

function urls_amigables($url) {

    // Tranformamos todo a minusculas

    $url = strtolower($url);

    //Rememplazamos caracteres especiales latinos

    $find = array('&aacute;', '&eacute;', '&iacute;', '&oacute;', '&uacute;', 'ñ');

    $repl = array('a', 'e', 'i', 'o', 'u', 'n');

    $url = str_replace($find, $repl, $url);

    // Añaadimos los guiones

    $find = array(' ', '&', '\r\n', '\n', '+', '/');
    $url = str_replace($find, '-', $url);

    // Eliminamos y Reemplazamos dem&aacute;s caracteres especiales

    $find = array('/[^a-z0-9\-<>]/', '/[\-]+/', '/<[^>]*>/');

    $repl = array('', '-', '');

    $url = preg_replace($find, $repl, $url);

    return $url;
}

?>