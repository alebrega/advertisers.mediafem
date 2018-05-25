<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Procesos extends CI_Controller {

    private $paises_db = NULL;
    private $paises_suscripcion = '';
    private $categorias_db = NULL;
    private $categorias_suscripcion = '';
    private $periodo_suscripcion = '';
    private $anunciante_nombre = '';
    private $orden_nombre = '';
    private $estado_campania = 'asd';
    private $data_campania = '';
    private $rango_de_fechas = '';

    function __construct() {
        parent::__construct();

        $this->load->model('anunciantes');
        $this->load->model('reportes');
        $this->load->model('campanias');
        $this->load->model('categorias');
        $this->load->model('categoriasaexcluir');
        $this->load->model('columnas');
        $this->load->model('paises');

        $this->load->library('mpdf');
        $this->load->library('My_PHPMailer');

        $this->paises_db = $this->paises->get_paises();

        $this->categorias_db = $this->categorias->get_categorias();
    }

    function campanias_by_appnexus($password) {
        try {
            if ($password == '4eZj6AuzBpTDYhYqqtAxNWUoXeOfLx4V') {
                // traigo los anunciantes pertenecientes a AppNexus
                $anunciantes_appnexus = $this->anunciantes->get_all_anunciantes_appnexus();

                $campanias = NULL;

                $lineItemText = 'line-items';

                foreach ($anunciantes_appnexus as $anunciante_appnexus) {
                    $request = new Request();
                    $request->method = 'get';
                    $request->uri = BASE_URI . '/line-item?advertiser_id=' . $anunciante_appnexus->id_appnexus;
                    $request->token = $this->token;
                    echo "Anunciante $anunciante_appnexus->id ----------- ";

                    //do {
                    $res = Caller::call($request);
                    //} while ($res == "RATE_EXCEEDED");
                    echo "HECHO <br>";


                    if ($res->response->$lineItemText) {
                        foreach ($res->response->$lineItemText as $li) {
                            if ($li->state == 'active') {
                                if ($this->campanias->get_campania_by_lineItem_appnexus($li->id) == NULL) {
                                    unset($camp);

                                    $camp['id_anunciante'] = $anunciante_appnexus->id;
                                    $camp['name'] = $li->name;
                                    $camp['inversion_bruta'] = $li->lifetime_budget;
                                    $camp['inversion_neta'] = $li->lifetime_budget;
                                    $camp['fecha_inicio'] = $li->start_date;
                                    $camp['fecha_fin'] = $li->end_date;
                                    $camp['id_lineItem_appnexus'] = $li->id;
                                    $camp['usuario_creador'] = 37;
                                    $camp['estado'] = 'APROBADA';
                                    $camp['alta_finalizada'] = 1;

                                    unset($insert);

                                    $insert = array(
                                        'id_anunciante' => $camp['id_anunciante'],
                                        'nombre' => $camp['name'],
                                        'inversion_bruta' => $camp['inversion_bruta'],
                                        'inversion_neta' => $camp['inversion_neta'],
                                        'fecha_inicio' => $camp['fecha_inicio'],
                                        'fecha_fin' => $camp['fecha_fin'],
                                        'id_lineItem_appnexus' => $camp['id_lineItem_appnexus'],
                                        'usuario_creador' => $camp['usuario_creador'],
                                        'estado' => $camp['estado'],
                                        'alta_finalizada' => $camp['alta_finalizada']
                                    );

                                    $this->campanias->insertar_campania($insert);
                                }
                            }
                        }
                    }

                    //sleep(1);
                }
            }
        } catch (Exception $ex) {
            echo "ERROR: " . $ex->getMessage() . "<br>";
            die();
            return false;
        }
    }

    function enviar_reportes_suscriptos($password) {
        if ($password == '4eZj6AuzBpTDYhYqqtAxNWUoXeOfLx4V') {
            $reporte = FALSE;
            $tipo = ''; // guarda el tipo de reporte listo para imprimir
            // obtengo las suscripciones
            $suscripciones = $this->reportes->get_suscripcion_by_enviado('0');
            // si hay suscripciones
            if ($suscripciones) {
                // recorro cada una
                foreach ($suscripciones as $suscripcion) {
                    // obtengo el nombre de los paises de la suscripcion
                    $this->paises_suscripcion = $this->get_paises_by_suscripcion($suscripcion->filtro_paises);

                    // obtengo el nombre de las categorias de la suscripcion
                    $this->categorias_suscripcion = $this->get_categorias_by_suscripcion($suscripcion->filtro_categorias);

                    // obtengo el periodo del reporte solicitado
                    $this->periodo_suscripcion = $this->get_periodo_by_suscripcion($suscripcion->intervalo, $suscripcion->fecha_desde, $suscripcion->fecha_hasta);

                    // genero el reporte solicitado
                    switch ($suscripcion->tipo) {
                        case 'por_sitio':
                            $tipo = 'Sitio web';

                            do {
                                $reporte = $this->api->reportePorSitio($suscripcion->token, $suscripcion->filtro_categorias, $suscripcion->filtro_paises, $suscripcion->intervalo, $suscripcion->fecha_desde, $suscripcion->fecha_hasta);
                            } while ($reporte == FALSE);

                            $totales = $reporte['totales'];
                            $reporte = $reporte['sitios'];

                            $columnas = array(
                                'url_sitio' => array('clave' => 'url_sitio', 'texto' => 'Sitio Web')
                                , 'categorias' => array('clave' => 'categorias', 'texto' => 'Canales tematicos')
                                , 'imps' => array('clave' => 'imps', 'texto' => 'Impresiones')
                            );

                            break;

                        case 'por_sitio_formato':
                            $tipo = 'Sitio web y Formato';

                            do {
                                $reporte = $this->api->reportePorSitioFormato($suscripcion->token, $suscripcion->filtro_categorias, $suscripcion->filtro_paises, $suscripcion->intervalo, $suscripcion->fecha_desde, $suscripcion->fecha_hasta);
                            } while ($reporte == FALSE);

                            $totales = $reporte['totales'];
                            $reporte = $reporte['sitios'];

                            $columnas = array(
                                'url_sitio' => array('clave' => 'url_sitio', 'texto' => 'Sitio Web')
                                , 'formatos' => array('clave' => 'formatos', 'texto' => 'Formatos')
                                , 'categorias' => array('clave' => 'categorias', 'texto' => 'Canales tematicos')
                                , 'imps' => array('clave' => 'imps', 'texto' => 'Impresiones')
                            );

                            break;

                        case 'por_pais':
                            $tipo = 'País';

                            do {
                                $reporte = $this->api->reportePorPais($suscripcion->token, $suscripcion->filtro_categorias, $suscripcion->filtro_paises, $suscripcion->intervalo, $suscripcion->fecha_desde, $suscripcion->fecha_hasta);
                            } while ($reporte == FALSE);

                            $totales = $reporte['totales'];
                            $reporte = $reporte['paises'];

                            $columnas = array(
                                'paises' => array('clave' => 'nombre', 'texto' => 'Pa&iacute;s')
                                , 'imps' => array('clave' => 'imps', 'texto' => 'Impresiones')
                            );

                            break;

                        case 'por_formato':
                            $tipo = 'Formato';

                            do {
                                $reporte = $this->api->reportePorFormato($suscripcion->token, $suscripcion->filtro_categorias, $suscripcion->filtro_paises, $suscripcion->intervalo, $suscripcion->fecha_desde, $suscripcion->fecha_hasta);
                            } while ($reporte == FALSE);

                            $totales = $reporte['totales'];
                            $reporte = $reporte['formatos'];

                            $columnas = array(
                                'formatos' => array('clave' => 'nombre', 'texto' => 'Formatos')
                                , 'imps' => array('clave' => 'imps', 'texto' => 'Impresiones')
                            );

                            break;

                        case 'por_categoria':
                            $tipo = 'Canal temático';

                            do {
                                $reporte = $this->api->reportePorCategoria($suscripcion->token, $suscripcion->filtro_categorias, $suscripcion->filtro_paises, $suscripcion->intervalo, $suscripcion->fecha_desde, $suscripcion->fecha_hasta);
                            } while ($reporte == FALSE);

                            $totales = array('imps' => '-');
                            $reporte = $reporte['categorias'];

                            $columnas = array(
                                'categorias' => array('clave' => 'nombre', 'texto' => 'Canales tematicos')
                                , 'imps' => array('clave' => 'imps', 'texto' => 'Impresiones')
                            );

                            break;

                        case 'anunciantes_campania':
                            if ($suscripcion->id_adserver == '0') {

                                $this->estadoCampana($suscripcion);

                                $enviar = TRUE;
                                if ($suscripcion->enviar_cada == 'unica_vez') {
                                    $enviar = TRUE;
                                } else if ($suscripcion->enviar_cada == 'diariamente') {
                                    if ($this->_dateDiff($suscripcion->fecha_envio, date('Y-m-d')) < 1) {
                                        $enviar = FALSE;
                                    } else if ($this->_dateDiff($suscripcion->fecha_envio, date('Y-m-d')) == 1) {
                                        if ($this->estado_campania == TRUE) {
                                            $enviar = FALSE;
                                            $this->marcarEnviado($suscripcion);
                                        }
                                    }
                                } else if ($suscripcion->enviar_cada == 'semanalmente') {
                                    if (date('l', strtotime(date('Y-m-d'))) == $suscripcion->dia_de_la_semana)
                                        $enviar = TRUE;
                                } else if ($suscripcion->enviar_cada == 'al_finalizar') {
                                    if ($this->estado_campania == TRUE)
                                        $enviar = TRUE;
                                }

                                $filtros_paises = $suscripcion->filtro_paises;
                                $filtros_li = $suscripcion->filtro_li;
                                $filtros_cr = $suscripcion->filtro_cr;

                                if ($filtros_paises != 0)
                                    $filtros_paises = explode(';', trim($filtros_paises, ';'));
                                $filtros['paises'] = $filtros_paises;

                                if ($filtros_li != 0)
                                    $filtros_li = explode(';', trim($filtros_li, ';'));
                                $filtros['lineItems'] = $filtros_li;

                                if ($filtros_cr != 0)
                                    $filtros_cr = explode(';', trim($filtros_cr, ';'));
                                $filtros['creatividades'] = $filtros_cr;

                                if ($enviar) {
                                    do {
                                        $reporte = $this->api->obtenerReporteDinamico_DFP($suscripcion->id_orden, $suscripcion->rango, $suscripcion->intervalo, $suscripcion->fecha_desde, $suscripcion->fecha_hasta, $suscripcion->columnas, $suscripcion->grupos, $filtros);
                                    } while ($reporte == FALSE);
                                }

                                if (!$reporte)
                                    break;

                                foreach ($reporte[2] as $value)
                                    $arr_columnas[] = $value;

                                foreach ($reporte[3] as $value)
                                    $arr_columnas[] = $value;

                                $arr_columnas[] = $suscripcion->rango;

                                $res_columnas = $this->columnas->get_all_columnas();
                                foreach ($res_columnas as $col) {
                                    for ($i = 0; $i < count($arr_columnas); $i++) {
                                        if ($col->id == $arr_columnas[$i])
                                            $arr_columnas_ordenado[] = array($col->id, $col->descripcion, $col->array_para_dfp);
                                    }
                                }

                                $columnas = $arr_columnas_ordenado;
                            }elseif ($suscripcion->id_adserver == '1') {
                                if ($suscripcion->por_sitio == 1) {
                                    do {
                                        $reporte = $this->obtener_reporte_campania_por_sitio_appnexus($suscripcion);
                                    } while ($reporte == FALSE);
                                } else {
                                    echo 'Es E-Planning <br>';
                                }
                            }

                            break;

                        default:
                            break;
                    }

                    if (!$reporte)
                        continue;

                    if ($suscripcion->tipo == 'anunciantes_campania') {
                        /*
                         * REPORTE DE ANUNCIANTES CAMPANIA
                         */
                        if ($suscripcion->id_adserver == 0) {

                            $this->anunciante_nombre = $this->anunciantes->get_anunciantes_by_id_DFP($suscripcion->id_anunciante);

                            if ($suscripcion->extension == 'xls') {
                                $tabla = $this->generar_tabla_anunciantes_campania_DFP($reporte, $columnas);
                            } else {
                                $tabla = $this->generar_tabla_anunciantes_campania_DFP_PDF($reporte, $columnas);
                            }
                        } else if ($suscripcion->id_adserver == 1) {
                            if ($suscripcion->por_sitio == 1) {
                                $tabla = $this->generar_tabla_anunciantes_campania_por_sitio_APPNEXUS($reporte);
                            } else {

                            }
                        }
                    } else {

                        /*
                         * REPORTE DE ANUNCIANTES INVENTARIO
                         */
                        $tabla = $this->generar_tabla_anunciantes_inventario($reporte, $columnas, $suscripcion->tipo, $totales);
                    }


                    // FECHA EN QUE SOLICITO EL REPORTE
                    $fecha_solicitud = explode(" ", $suscripcion->fecha_solicitud);
                    list($anio_solicitud, $mes_solicitud, $dia_solicitud) = explode("-", $fecha_solicitud[0]);
                    $fecha_solicitud = $dia_solicitud . "-" . $mes_solicitud . "-" . $anio_solicitud;

                    // RANGO DE FECHAS SOLICITADAS
                    // DATOS DE LA CAMPANA
                    $this->data_campania = $this->campanias->get_campania_by_order_id($suscripcion->id_orden);

                    // genero el contenido del correo electronico
                    if ($suscripcion->tipo == 'anunciantes_campania') {
                        if ($suscripcion->intervalo != 'lifetime') {
                            $titulo = 'MediaFem – Campaña ' . $this->data_campania->nombre . ' de Anunciante ' . $this->anunciante_nombre->nombre . $this->rango_de_fechas;
                        } else {
                            $titulo = 'MediaFem – Campaña ' . $this->data_campania->nombre . ' de Anunciante ' . $this->anunciante_nombre->nombre;
                        }
                    } else {
                        $titulo = 'Inventario de MediaFem' . $this->rango_de_fechas . ' por ' . $tipo;
                    }

                    $contenido = 'En este correo encontrara adjunto el reporte que genero el día ' . $fecha_solicitud . ' desde nuestra plataforma MediaFem para Anunciantes ( http://anunciantes.mediafem.com ), este correo es generado automáticamente.';



                    // con los datos obtenidos genero el Excel y lo almaceno en tmp
                    if ($suscripcion->extension == 'xls') {
                        $sfile = "MediaFem-reporte_anunciantes.xls"; //ruta del archivo a generar

                        $fp = fopen($sfile, "w+");
                        if ($fp) {
                            // reemplazo el contenido del excel
                            fwrite($fp, $tabla);

                            echo "Envio de correo al correo electronico " . $suscripcion->correo_electronico . ".\n";

                            // envio el correo electronico con el archivo excel del reporte adjuntado
                            if ($this->enviar_email_reporte($suscripcion->correo_electronico, $titulo, $contenido, $sfile) == true) {
                                $this->marcarEnviado($suscripcion->id);

                                echo "  ---- OK.\n\n";
                            } else {
                                echo "  ---- ERROR: No se pudo enviar el correo electronico.\n\n";
                            }
                        }
                        fclose($fp);
                    } else {
                        $sfile = "MediaFem-reporte_anunciantes.pdf"; //ruta del archivo a generar

                        $html = '<html>
                                <body style="font-family: Arial,Helvetica,sans-serif;">';

                        $header = '<div style="width:100%;padding-top: 10px;background-color:#E43B8E;padding-left: 10px;padding-bottom:10px">
                                    <img alt="MediaFem" height="32px" src="/images/logo.jpg">
                                </div>';

                        $html .= $tabla;

                        $footer = '<div style="height:40px;width: 100%;background-color:#E43B8E;padding-left: 10px;padding-top: 10px;color:#FFF;font-size:12px">
                                    <div>MediaFem – Rivera 302 – Lomas de Zamora, Buenos Aires, Argentina</div>
                                    <div>e.mail: anunciantes@mediafem.com - Tel.: (+5411) 4243-4000</div>
                                </div>';

                        $html .='    </body>
                            </html>';

                        $tabla = $html;

                        $mpdf = new mPDF();
                        $this->mpdf->SetHTMLFooter($footer);
                        $this->mpdf->SetHTMLHeader($header);
                        $this->mpdf->SetMargins(0, 0, 30, 26);
                        $this->mpdf->WriteHTML($tabla);
                        $content = $this->mpdf->Output($sfile, "S");
                        $this->mpdf->Output($sfile);

                        echo "Envio de correo al correo electronico " . $suscripcion->correo_electronico . ".\n";

                        // envio el correo electronico con el archivo excel del reporte adjuntado
                        if ($this->enviar_email_reporte($suscripcion->correo_electronico, $titulo, $contenido, $sfile) == true) {

                            $this->marcarEnviado($suscripcion);

                            echo "  ---- OK.\n\n";
                        } else {
                            echo "  ---- ERROR: No se pudo enviar el correo electronico.\n\n";
                        }
                    }

                    // por cada correo que se envía se aguarda 5 segundos para solicitar e enviar un nuevo reporte
                    sleep(5);
                }
            }

            die();
        }

        redirect('');
    }

    private function marcarEnviado($suscripcion_id) {
        if ($suscripcion->enviar_cada == 'unica_vez') {
            // marco como "enviado"  el reporte
            $this->reportes->update_suscripcion($suscripcion_id, array('enviado' => 1));
        } else {
            // si no se envia por unica vez consulto si se completo la campaña
            // si se completo la campaña entonces se marca como enviada
            if ($this->estado_campania)
                $this->reportes->update_suscripcion($suscripcion_id, array('enviado' => 1));
        }
    }

    private function estadoCampana($suscripcion) {
        $reporte = FALSE;
        $result = TRUE;

        do {
            $reporte = $this->api->lineItemsByOrder_DFP($suscripcion->id_orden);
        } while ($reporte == FALSE);

        foreach ($reporte->results as $lineItem) {
            if ($lineItem->status != 'COMPLETED')
                $result = FALSE;
        }

        $this->estado_campania = $result;

        return TRUE;
    }

    private function get_paises_by_suscripcion($paises_reporte) {
        $paises_global = 'AFoALoDZoASoADoAOoAIoAGoARoAMoAWoAUoAToAZoBSoBHoBDoBBoBYoBEoBZoBJoBMoBOoBAoBWoBRoBNoBGoBFoBIoKHoCMoCAoCVoKYoCFoCLoCNoCOoCGoCDoCKoCRoCIoHRoCYoCZoDKoDJoDMoECoEGoSVoGQoESoEEoEToFOoFMoFJoFIoFRoGFoPFoGAoGMoGEoDEoGHoGIoGRoGLoGDoGPoGUoGToGNoGYoHToVAoHNoHKoHUoISoINoIDoIQoIEoILoIToJMoJPoJOoKZoKEoKIoKWoKGoLAoLVoLBoLSoLRoLYoLIoLToLUoMOoMKoMGoMWoMYoMVoMLoMToFKoMQoMRoMUoYToMXoMDoMCoMNoMSoMAoMZoNAoNPoNLoANoNCoNZoNIoNEoNGoMPoNOoOMoPKoPWoPSoPAoPGoPYoPEoPHoPLoPToPRoQAoKRoDOoREoROoRUoRWoKNoLCoPMoVCoSMoSToSAoSNoSLoSGoSKoSIoSBoSOoZAoLKoSRoSZoSEoCHoTWoTZoTHoTLoTGoTOoTToTNoTRoTMoTCoUGoUAoAEoGBoUSoUMoUYoUZoVUoVEoVNoVGoVIoYEoZMoZWo';

        if ($paises_reporte == $paises_global) {
            return 'Todos';
        } else {
            $texto_paises = '';

            $paises_reporte = explode('o', trim($paises_reporte, 'o'));
            $cant_paises = sizeof($paises_reporte);

            $a = 0;
            foreach ($this->paises_db as $paises) {
                foreach ($paises_reporte as $pais) {
                    if ($paises->id == $pais) {
                        if ($a == ($cant_paises - 2)) {
                            $texto_paises .= $paises->descripcion . ' y ';
                        } else {
                            $texto_paises .= $paises->descripcion . ', ';
                        }

                        $a++;
                    }
                }
            }

            return trim($texto_paises, ', ');
        }
    }

    private function get_categorias_by_suscripcion($categorias_reporte) {
        $categorias_global = '35o20o32o22o55o31o36o23o24o27o19o33o25o34o21o28o';

        if ($categorias_reporte == $categorias_global) {
            return 'Todas';
        } else {
            $texto_categorias = '';

            $categorias_reporte = explode('o', trim($categorias_reporte, 'o'));
            $cant_paises = sizeof($categorias_reporte);

            $a = 0;
            foreach ($this->categorias_db as $categorias) {
                foreach ($categorias_reporte as $categoria) {
                    if ($categorias->id == $categoria) {
                        if ($a == ($cant_paises - 2)) {
                            $texto_categorias .= $categorias->nombre . ' y ';
                        } else {
                            $texto_categorias .= $categorias->nombre . ', ';
                        }

                        $a++;
                    }
                }
            }

            return trim($texto_categorias, ', ');
        }
    }

    private function get_periodo_by_suscripcion($periodo, $fecha_inicio, $fecha_fin) {
        switch ($periodo) {
            case 'today':
                $texto_fecha = date("d/m/y");

                $this->rango_de_fechas = ' del día ' . $texto_fecha;

                break;
            case 'yesterday':
                $dia = time() - (1 * 24 * 60 * 60); //Te resta un dia (2*24*60*60) te resta dos y //asi...
                $texto_fecha = date('d/m/y', $dia);

                $this->rango_de_fechas = ' del día ' . $texto_fecha;

                break;
            case 'last_7_days':
                $texto_fecha = 'Últimos 7 dias';

                $this->rango_de_fechas = ' de los últimos 7 dias';

                break;
            case 'month_to_date':
                $texto_fecha = 'este mes: ' . getMesEsp(date("m"));

                $this->rango_de_fechas = ' del mes de ' . getMesEsp(date("m"));

                break;
            case 'last_month':
                $texto_fecha = 'mes pasado: ' . getMesEsp(date("m") - 1);

                $this->rango_de_fechas = ' del mes de ' . getMesEsp(date("m") - 1);

                break;
            case 'especific':
                $texto_fecha = 'Fechas Especificas ( desde el ' . $fecha_inicio . ' al ' . $fecha_fin . ' )';

                $this->rango_de_fechas = ' entre ' . $fecha_inicio . ' y ' . $fecha_fin;

                break;
            case 'lifetime':
                $texto_fecha = 'Siempre';
                break;
            default:
                $texto_fecha = '';
                break;
        }

        return $texto_fecha;
    }

    private function obtener_reporte_campania_por_sitio_appnexus($suscripcion) {

        $categorias = $this->categoriasaexcluir->get_all_categorias();

        $sites = getSites($this->token);

        if ($sites) {
            foreach ($sites as $row) {
                $filtrar = 0;
                $arr_cats = $row->content_categories;
                if ($arr_cats) {
                    foreach ($categorias as $row_cat) {
                        if (!$filtrar) {
                            foreach ($arr_cats as $cat) {
                                if ($row_cat->id == $cat->id)
                                    $filtrar = 1;
                            }
                        }
                    }
                }

                if ($filtrar)
                    $arr_sites[] = array('site_name' => $row->name, 'id' => $row->id);
            }
        }

        foreach ($arr_sites as $row_site)
            $arr_filtro[$row_site['id']] = $row_site['id'];

        $arr_filtro_sitios = array_values(array_unique($arr_filtro));

        $filtros_li = $suscripcion->filtro_li;

        if ($filtros_li) {
            $arr_por[] = 'Canal Tem&aacute;tico';

            $partes_li = explode(";", $filtros_li);

            for ($j = 0; $j < count($partes_li); $j++) {
                if (!empty($partes_li[$j])) {
                    $arr_li[] = $partes_li[$j];
                }
            }
            $arr_filtros[] = array('line_item_id' => $arr_li);
        }

        $filtros_cr = $suscripcion->filtro_cr;

        if ($filtros_cr) {
            $arr_por[] = 'Creatividad';

            $partes_cr = explode(";", $filtros_cr);

            for ($j = 0; $j < count($partes_cr); $j++) {
                if (!empty($partes_cr[$j])) {
                    $arr_cr[] = $partes_cr[$j];
                }
            }

            $arr_filtros[] = array('creative_id' => $arr_cr);
        }

        $filtros_sizes = $suscripcion->filtro_formatos;

        if ($filtros_sizes) {
            $arr_por[] = 'Tama&ntilde;o';

            $partes_sizes = explode(";", $filtros_sizes);

            for ($j = 0; $j < count($partes_sizes); $j++) {
                if (!empty($partes_sizes[$j])) {
                    $arr_sizes[] = $partes_sizes[$j];
                }
            }

            $arr_filtros[] = array('size' => $arr_sizes);
        }

        $filtros_paises = $suscripcion->filtro_paises;

        if ($filtros_paises) {
            $arr_por[] = 'Pa&iacute;s';

            $partes_paises = explode(";", $filtros_paises);

            for ($j = 0; $j < count($partes_paises); $j++) {
                if (!empty($partes_paises[$j])) {
                    $arr_paises[] = $partes_paises[$j];
                }
            }

            $arr_filtros[] = array('geo_country' => $arr_paises);
        }

        $filtrado_por = "";
        if (isset($arr_por)) {
            for ($x = 0; $x < count($arr_por); $x++) {
                if ($x == 0) {
                    $filtrado_por = $filtrado_por . " " . $arr_por[$x];
                } else {
                    $filtrado_por = $filtrado_por . ", " . $arr_por[$x];
                }
            }
        }

        $anunciante_id = $suscripcion->id_anunciante;

        $arr_filtros[] = array('advertiser_id' => $anunciante_id);

        $rango = $suscripcion->intervalo;
        $columnas = $suscripcion->columnas;

        $partes_columnas = explode(";", $columnas);

        for ($j = 0; $j < count($partes_columnas); $j++) {
            if (!empty($partes_columnas[$j])) {
                $arr_columnas[] = $partes_columnas[$j];
            }
        }

        $texto_fecha_desde = "";
        $texto_fecha_hasta = "";

        $fecha_desde = $suscripcion->fecha_desde;
        $fecha_hasta = $suscripcion->fecha_hasta;

        if ($fecha_desde && $fecha_hasta) {

            list($dia_desde, $mes_desde, $anio_desde) = explode("-", $fecha_desde);
            list($dia_hasta, $mes_hasta, $anio_hasta) = explode("-", $fecha_hasta);

            $start_date = $anio_desde . "-" . $mes_desde . "-" . $dia_desde . ' 00:00:00';
            $end_date = $anio_hasta . "-" . $mes_hasta . "-" . $dia_hasta . ' 23:59:59';

            $texto_fecha_desde = $dia_desde . "/" . $mes_desde . "/" . $anio_desde;
            $texto_fecha_hasta = $dia_hasta . "/" . $mes_hasta . "/" . $anio_hasta;
        }

        if (!isset($arr_filtros)) {
            $arr_filtros = null;
        }

        if ($suscripcion->grupos) {
            $grupos = $suscripcion->grupos;

            $partes_grupos = explode(";", $grupos);

            for ($j = 0; $j < count($partes_grupos); $j++) {
                if (!empty($partes_grupos[$j])) {
                    $col = $this->columnas->get_columna_by_id($partes_grupos[$j]);
                    $arr_txt_agrupado[] = $col->descripcion;
                    $arr_columnas[] = $partes_grupos[$j];
                }
            }
        }

        $agrupado_por = "";
        if (isset($arr_txt_agrupado)) {
            for ($x = 0; $x < count($arr_txt_agrupado); $x++) {
                if ($x == 0) {
                    $agrupado_por = $agrupado_por . " " . $arr_txt_agrupado[$x];
                } else {
                    $agrupado_por = $agrupado_por . ", " . $arr_txt_agrupado[$x];
                }
            }
        }

        $interval = $suscripcion->rango;

        if ($interval != "cumulative") {
            $arr_columnas[] = $interval;
            $orden = $interval;
        }

        $arr_grupos = null;

        if ($rango == "today") {
            $start_date = date('Y-m-d 00:00:00');
            $end_date = date('Y-m-d 23:59:59');
        } elseif ($rango == "yesterday") {
            $start_date = date('Y-m-d 00:00:00', strtotime("-1 day"));
            $end_date = date('Y-m-d 23:59:59', strtotime("-1 day"));
        } elseif ($rango == "last_7_days") {
            $start_date = date('Y-m-d 00:00:00', strtotime("-7 days"));
            $end_date = date('Y-m-d 23:59:59');
        } elseif ($rango == "month_to_date") {
            $start_date = date('Y-m-d 00:00:00', strtotime('this month', strtotime(date('Y-m-01'))));
            $end_date = date('Y-m-d 23:59:59');
        } elseif ($rango == "last_month") {
            $start_date = date('Y-m-d 00:00:00', strtotime('-1 month', strtotime(date('Y-m-01'))));
            $end_date = date('Y-m-d 23:59:59', strtotime("-" . Date("d") . " days"));
        } elseif ($rango == "lifetime") {
            $start_date = null;
            $end_date = null;
        }

        if ($rango != "lifetime") {
            $rango = null;
        }

        $res_columnas = $this->columnas->get_all_columnas();

        foreach ($res_columnas as $col) {
            for ($i = 0; $i < count($arr_columnas); $i++) {
                if ($col->id == $arr_columnas[$i]) {
                    $arr_columnas_ordenado[] = $col->id;
                }
            }
        }

        $arr_columnas_ordenado[] = "site_id";

        $datos = getNetworkDynamicReport($this->token, $anunciante_id, $rango, $arr_filtros, $arr_grupos, $arr_columnas_ordenado, $start_date, $end_date, 'EST5EDT', $orden, 'ASC');

        $data['sitios_ocultos'] = $arr_filtro_sitios;
        $data['datos'] = $datos;
        $data['arr_columnas'] = $arr_columnas_ordenado;

        return $data;
    }

    private function generar_tabla_anunciantes_inventario($reporte, $columnas, $tipo, $totales) {
        $tabla = '<table>';
        $tabla .= '<tr>';
        $tabla .= '<td colspan="7"><b>Sitios de MediaFem</b></td>';
        $tabla .= '</tr>';

        $tabla .= '<tr>';
        $tabla .= '<td colspan="7"> </td>';
        $tabla .= '</tr>';

        $tabla .= '<tr>';
        $tabla .= '<td><b>Pa&iacute;ses:</b></td>';
        $tabla .= '<td colspan="6">' . htmlentities($this->paises_suscripcion, ENT_QUOTES, 'UTF-8') . '</td>';
        $tabla .= '</tr>';

        $tabla .= '<tr>';
        $tabla .= '<td><b>Canales tem&aacute;ticos:</b></td>';
        $tabla .= '<td colspan="6">' . htmlentities($this->categorias_suscripcion, ENT_QUOTES, 'UTF-8') . '</td>';
        $tabla .= '</tr>';

        $tabla .= '<tr>';
        $tabla .= '<td><b>Rango del reporte:</b></td>';
        $tabla .= '<td colspan="6" style="text-align:left;">' . htmlentities($this->periodo_suscripcion, ENT_QUOTES, 'UTF-8') . '</td>';
        $tabla .= '</tr>';

        $tabla .= '<tr>';
        $tabla .= '<td colspan="7"> </td>';
        $tabla .= '</tr>';
        $tabla .= '</table>';

        // genero el excel del reporte
        $tabla .= '<table>';
        // encabezado de la tabla
        $tabla .= '<tr>';
        foreach ($columnas as $columna) {
            $tabla .= '<th>';
            $tabla .= $columna['texto'];
            $tabla .= '</th>';
        }
        $tabla .= '</tr>';

        foreach ($reporte as $key => $row) {
            $aux[$key] = (int) str_replace('.', '', $row['imps']);
        }

        array_multisort($aux, SORT_DESC, $reporte);

        // datos del reporte
        if ($tipo == 'por_sitio_formato') { // por sitio y formato
            $contador = 0;
            foreach ($reporte as $sitio) {

                if ($sitio['formatos']) {

                    foreach ($sitio['formatos'] as $formato) {
                        $contador++;

                        if ($contador > 1) {
                            $color = 'background-color:#E2E4FF';
                            $contador = 0;
                        } else {
                            $color = 'background-color:#FFFFFF';
                        }

                        $tabla .= '<tr>';
                        foreach ($columnas as $columna) {
                            $tabla .= '<td style="' . $color . '">';
                            if ($columna['clave'] == 'formatos') {
                                $tabla .= htmlentities($formato['nombre'], ENT_QUOTES, 'UTF-8');
                            } else if ($columna['clave'] == 'imps') {
                                $tabla .= htmlentities($formato['imps'], ENT_QUOTES, 'UTF-8');
                            } else {
                                $tabla .= htmlentities($sitio[$columna['clave']], ENT_QUOTES, 'UTF-8');
                            }
                            $tabla .= '</td>';
                        }
                        $tabla .= '</tr>';
                    }
                }
            }
        } else { // otro tipo de reporte
            $contador = 0;
            foreach ($reporte as $sitio) {
                $contador++;

                if ($contador > 1) {
                    $color = 'background-color:#E2E4FF';
                    $contador = 0;
                } else {
                    $color = 'background-color:#FFFFFF';
                }

                $tabla .= '<tr>';
                foreach ($columnas as $columna) {
                    $tabla .= '<td style="' . $color . '">';
                    $tabla .= htmlentities($sitio[$columna['clave']], ENT_QUOTES, 'UTF-8');
                    $tabla .= '</td>';
                }
                $tabla .= '</tr>';
            }
        }

        // totales del reporte
        $tabla .= '<tr>';
        $tabla .= '<td><b>Totales:</b></td>';

        if (sizeof($columnas) == 3)
            $tabla .= '<td> </td>';

        if (sizeof($columnas) == 4) {
            $tabla .= '<td> </td>';
            $tabla .= '<td> </td>';
        }

        $tabla .= '<td><b>' . $totales['imps'] . '</b></td>';
        $tabla .= '</tr>';

        $tabla .= '</table>';

        return $tabla;
    }

    private function generar_tabla_anunciantes_campania_por_sitio_APPNEXUS($reporte) {
        $datos = $reporte['datos'];
        $arr_columnas = $reporte['arr_columnas'];
        $sitios_ocultos = $reporte['sitios_ocultos'];

        $arr_data = null;
        $contador = 0;
        $total_reg = 0;
        $total_sitios_reales = 0;
        $imps_ocultas_por_sitio = 0;
        $clicks_ocultos_por_sitio = 0;
        $convs_ocultas_por_sitio = 0;

        $total_imps = 0;
        $total_imps_ocultas = 0;
        $total_imps_reales = 0;
        $total_clicks = 0;
        $total_clicks_ocultos = 0;
        $total_clicks_reales = 0;
        $total_cpm = 0;
        $total_convs = 0;
        $total_convs_ocultas = 0;
        $total_convs_reales = 0;
        $total_revenue = 0;

        $promedio_imps = 0;
        $promedio_clicks = 0;
        $ctr_total = 0;

        $posicion_imps = "";
        $posicion_clicks = "";
        $posicion_convs = "";
        $posicion_site_id = "";

        $porcentaje_imps = 0;
        $porcentaje_clicks = 0;
        $porcentaje_convs = 0;

        $rows = explode("\n", $datos);

        for ($i = 1; $i < count($rows); $i++) {
            if (strlen($rows[$i]) > 0)
                $arr_data[] = $rows[$i];
        }

        $columnas = "";
        for ($i = 0; $i < count($arr_columnas); $i++) {
            if ($arr_columnas[$i] != "site_id") {
                $columnas = $columnas . $arr_columnas[$i] . ";";
            }
            if ($arr_columnas[$i] == "imps") {
                $posicion_imps = $i;
            }
            if ($arr_columnas[$i] == "clicks") {
                $posicion_clicks = $i;
            }
            if ($arr_columnas[$i] == "total_convs") {
                $posicion_convs = $i;
            }
            if ($arr_columnas[$i] == "site_id") {
                $posicion_site_id = $i;
            }
        }

        $arr_sitios = null;

        if (isset($arr_data)) {
            foreach ($arr_data as $c => $v) {
                $fields = explode(",", $v);
                $total_sitios_reales++;

                for ($i = 0; $i < count($arr_columnas); $i++) {
                    $campo = $fields[$i];
                    if ($arr_columnas[$i] == "site_name") {
                        if (substr($campo, 0, 6) == "Hidden") {
                            if (strlen($posicion_imps)) {
                                $total_imps_ocultas = $total_imps_ocultas + $fields[$posicion_imps];
                            }
                            if (strlen($posicion_clicks)) {
                                $total_clicks_ocultos = $total_clicks_ocultos + $fields[$posicion_clicks];
                            }
                            if (strlen($posicion_convs)) {
                                $total_convs_ocultas = $total_convs_ocultas + $fields[$posicion_convs];
                            }
                            $total_sitios_reales--;
                        } else {
                            if (in_array($fields[$posicion_site_id], $sitios_ocultos)) {
                                if (strlen($posicion_imps)) {
                                    $total_imps_ocultas = $total_imps_ocultas + $fields[$posicion_imps];
                                }
                                if (strlen($posicion_clicks)) {
                                    $total_clicks_ocultos = $total_clicks_ocultos + $fields[$posicion_clicks];
                                }
                                if (strlen($posicion_convs)) {
                                    $total_convs_ocultas = $total_convs_ocultas + $fields[$posicion_convs];
                                }
                                $total_sitios_reales--;
                            } else {
                                $imps_reales = 0;
                                $clicks_reales = 0;
                                $convs_reales = 0;

                                if (strlen($posicion_imps)) {
                                    $total_imps_reales = $total_imps_reales + $fields[$posicion_imps];
                                    $imps_reales = $fields[$posicion_imps];
                                }
                                if (strlen($posicion_clicks)) {
                                    $total_clicks_reales = $total_clicks_reales + $fields[$posicion_clicks];
                                    $clicks_reales = $fields[$posicion_clicks];
                                }
                                if (strlen($posicion_convs)) {
                                    $total_convs_reales = $total_convs_reales + $fields[$posicion_convs];
                                    $convs_reales = $fields[$posicion_convs];
                                }

                                $arr_sitios[trim($fields[$posicion_site_id])] = array('imps' => $imps_reales, 'clicks' => $clicks_reales, 'convs' => $convs_reales);
                            }
                        }
                    }
                }
            }
        }

        $sitios = null;

        foreach ($arr_sitios as $key => $value) {
            $imps = $value['imps'];
            $clicks = $value['clicks'];
            $convs = $value['convs'];

            $imps_ocultas = 0;
            $clicks_ocultos = 0;
            $convs_ocultas = 0;

            if (strlen($posicion_imps)) {
                if ($total_imps_reales)
                    $porcentaje_imps = (($imps * 100) / $total_imps_reales);
                $imps_ocultas = ($porcentaje_imps * $total_imps_ocultas) / 100;
            }
            if (strlen($posicion_clicks)) {
                if ($total_clicks_reales)
                    $porcentaje_clicks = (($clicks * 100) / $total_clicks_reales);
                $clicks_ocultos = ($porcentaje_clicks * $total_clicks_ocultos) / 100;
            }
            if (strlen($posicion_convs)) {
                if ($total_convs_reales)
                    $porcentaje_convs = (($convs * 100) / $total_convs_reales);
                $convs_ocultas = ($porcentaje_convs * $total_convs_ocultas) / 100;
            }

            $sitios[trim($key)] = array('imps_ocultas' => $imps_ocultas, 'clicks_ocultos' => $clicks_ocultos, 'convs_ocultas' => $convs_ocultas);
        }

        if ($total_imps_ocultas)
            $imps_ocultas_por_sitio = ($total_imps_ocultas / $total_sitios_reales);

        if ($total_clicks_ocultos)
            $clicks_ocultos_por_sitio = ($total_clicks_ocultos / $total_sitios_reales);

        if ($total_convs_ocultas)
            $convs_ocultas_por_sitio = ($total_convs_ocultas / $total_sitios_reales);

        if (strlen($posicion_imps))
            $total_imps = ($total_imps_reales + $total_imps_ocultas);

        if (strlen($posicion_clicks))
            $total_clicks = ($total_clicks_reales + $total_clicks_ocultos);

        if (strlen($posicion_convs))
            $total_convs = ($total_convs_reales + $total_convs_ocultas);


        $tabla = '<table>';

// ENCABEZADO
        $tabla .= '<tr>';

        for ($j = 0; $j < count($arr_columnas); $j++) {
            if (!empty($arr_columnas[$j])) {
                if ($arr_columnas[$j] != "site_id") {
                    $columna = $this->columnas->get_columna_by_id($arr_columnas[$j]);
                    $tabla .= '<th>' . htmlentities($columna->descripcion, ENT_QUOTES, 'UTF-8') . '</th>';
                }
            }
        }

        $tabla .= '</tr>';

// CUERPO
        if (isset($arr_data)) {
            $contador = 0;
            foreach ($arr_data as $c => $v) {
                $total_reg++;
                $fields = explode(",", $v);
                $row_html = "";
                $mostrar = 1;

                for ($i = 0; $i < count($arr_columnas); $i++) {

                    $campo = $fields[$i];

                    if ($arr_columnas[$i] == "ecpm") {
                        $cpm = $campo;
                        $total_cpm = ($total_cpm + $cpm);
                        $campo = "US$ " . number_format($campo, 2, ',', '.');
                    } elseif ($arr_columnas[$i] == "ecpc") {
                        $cpc = $campo;
                        $total_cpc = ($total_cpc + $cpc);
                        $campo = "US$" . number_format($campo, 2, ',', '.');
                    } elseif ($arr_columnas[$i] == "revenue") {
                        $revenue = $campo;
                        $campo = "US$ " . number_format($campo, 2, ',', '.');
                    } elseif ($arr_columnas[$i] == "total_revenue") {
                        $campo = "US$ " . number_format($campo, 2, ',', '.');
                    } elseif ($arr_columnas[$i] == "ctr") {
                        if (strlen($posicion_clicks) && strlen($posicion_imps)) {
                            if (array_key_exists(trim($fields[$posicion_site_id]), $sitios)) {
                                $clicks_ocultos_del_sitio = $sitios[trim($fields[$posicion_site_id])]['clicks_ocultos'];
                                $imps_ocultas_del_sitio = $sitios[trim($fields[$posicion_site_id])]['imps_ocultas'];
                                $campo = (($fields[$posicion_clicks] + $clicks_ocultos_del_sitio) / ($fields[$posicion_imps] + $imps_ocultas_del_sitio)) * 100;
                            }
                        } else {
                            $campo = ($campo * 100);
                        }
                        $campo = number_format($campo, 2, ',', '.') . "%";
                    } elseif ($arr_columnas[$i] == "clicks") {
                        if (array_key_exists(trim($fields[$posicion_site_id]), $sitios)) {
                            $campo = ($campo + $sitios[trim($fields[$posicion_site_id])]['clicks_ocultos']);
                        }
//$total_clicks = $total_clicks + $campo;
                        $campo = number_format($campo, 0, ',', '.');
                    } elseif ($arr_columnas[$i] == "imps") {
                        if (array_key_exists(trim($fields[$posicion_site_id]), $sitios)) {
                            $campo = ($campo + $sitios[trim($fields[$posicion_site_id])]['imps_ocultas']);
                        }
//$total_imps = ($total_imps + $campo);
                        $campo = number_format($campo, 0, ',', '.');
                    } elseif ($arr_columnas[$i] == "total_convs") {
                        if (array_key_exists(trim($fields[$posicion_site_id]), $sitios)) {
                            $campo = ($campo + $sitios[trim($fields[$posicion_site_id])]['convs_ocultas']);
                        }
                        $campo = number_format($campo, 0, ',', '.');
                    } elseif ($arr_columnas[$i] == "hour") {
                        $campo = ColumnHourToDate($campo);
                    } elseif ($arr_columnas[$i] == "day") {
                        $campo = ColumnDayToDate($campo);
                    } elseif ($arr_columnas[$i] == "month") {
                        $campo = ColumnMonthToDate($campo);
                    } elseif ($arr_columnas[$i] == "site_name") {
                        if (substr($campo, 0, 6) == "Hidden") {
                            $campo = "Sitio oculto";
                            $mostrar = 0;
                        } else {
                            if (in_array($fields[$posicion_site_id], $sitios_ocultos)) {
                                $campo = "Sitio oculto";
                                $mostrar = 0;
                            }
                        }
                    }
                    if ($arr_columnas[$i] != "site_id") {
                        $row_html.= '<td>' . $campo . "</td>";
                    }
                    if ($arr_columnas[$i] == "revenue" && $mostrar == 1) {
                        $total_revenue += $revenue;
                    }
                }
                if ($mostrar) {
                    $contador++;

                    if ($contador > 1) {
                        $color = 'background-color:#E2E4FF';
                        $contador = 0;
                    } else {
                        $color = 'background-color:#FFFFFF';
                    }

                    $tabla .= '<tr>' . str_replace('<td>', '<td  style="' . $color . '">', $row_html) . "</tr>";
                }
            }
        }
        if ($total_imps)
            $promedio_imps = ($total_imps / $total_reg);

        if ($total_clicks)
            $promedio_clicks = ($total_clicks / $total_reg);

        if ($total_imps && $total_clicks)
            $ctr_total = ($total_clicks / $total_imps) * 100;

// TOTALES
        /*
          $tabla .= '<tr>';

          for ($i = 0; $i < count($arr_columnas); $i++) {
          $columna = $arr_columnas[$i];
          if ($columna == "imps") {
          $tabla .= "<td><b>" . number_format($total_imps, 0, ',', '.') . "</b></td>";
          } elseif ($columna == "clicks") {
          $tabla .= "<td><b>" . number_format($total_clicks, 0, ',', '.') . "</b></td>";
          } elseif ($columna == "total_convs") {
          $tabla .= "<td><b>" . number_format($total_convs, 0, ',', '.') . "</b></td>";
          } elseif ($columna == "ctr") {
          $tabla .= "<td><b>" . number_format($ctr_total, 2, ',', '.') . "%</b></td>";
          } elseif ($columna == "revenue") {
          $tabla .= "<td><b>" . "US$ " . number_format($total_revenue, 2, ',', '.') . "</b></td>";
          } else {
          if ($arr_columnas[$i] != "site_id") {
          $tabla .= "<td></td>";
          }
          }
          }

          $tabla .= '</tr>';
         */
        $tabla .= '</table>';

        return $tabla;
    }

    private function generar_tabla_anunciantes_campania_DFP($reporte, $columnas) {
        if ($reporte) {

            $tabla = '<table width="100%">';
            $tabla .= '<tr>';
            $tabla .= '<td colspan="7"><b>MediaFem</b></td>';
            $tabla .= '</tr>';

            $tabla .= '<tr>';
            $tabla .= '<td colspan="7"> </td>';
            $tabla .= '</tr>';

            $tabla .= '<tr>';
            $tabla .= '<td><b>Anunciante:</b></td>';
            $tabla .= '<td colspan="6">' . htmlentities($this->anunciante_nombre->nombre, ENT_QUOTES, 'UTF-8') . '</td>';
            $tabla .= '</tr>';

            $tabla .= '<tr>';
            $tabla .= '<td><b>Fechas del reporte:</b></td>';
            $tabla .= '<td colspan="6">' . htmlentities($this->periodo_suscripcion, ENT_QUOTES, 'UTF-8') . '</td>';
            $tabla .= '</tr>';

            $tabla .= '<tr>';
            $tabla .= '<td colspan="7"> </td>';
            $tabla .= '</tr>';
            $tabla .= '</table>';


            $tabla .= '<table width="100%">';

// ENCABEZADO
            $tabla .= '<tr>';

            foreach ($columnas as $col)
                $tabla .= '<th>' . htmlentities($col[1], ENT_QUOTES, 'UTF-8') . '</th>';

            $tabla .= '</tr>';

// CUERPO
            $contador = 0;
            foreach ($reporte[0] as $report) {
                $contador++;

                if ($contador > 1) {
                    $color = 'background-color:#E2E4FF';
                    $contador = 0;
                } else {
                    $color = 'background-color:#FFFFFF';
                }

                $tabla .= '<tr>';
                foreach ($columnas as $col) {
                    $tabla .= '<td style="' . $color . '">';

                    switch ($col[2]) {
                        case 'impresiones':
                            $tabla .= number_format($report[$col[2]], 0, ',', '.');
                            break;
                        case 'clicks':
                            $tabla .= number_format($report[$col[2]], 0, ',', '.');
                            break;
                        case 'ctr':
                            $tabla .= number_format($report[$col[2]], 2, ',', '.') . '%';
                            break;
                        case 'revenue':
                            $tabla .= 'US$ ' . number_format($report[$col[2]], 2, ',', '.');
                            break;
                        default:
                            $tabla .= $report[$col[2]];
                            break;
                    }

                    $tabla .= '</td>';
                }
                $tabla .= '</tr>';
            }

// TOTALES
            $tabla .= '<tr>';

            foreach ($columnas as $col) {
                if ($col[0] == "imps") {
                    $total_imps = number_format($reporte[4]['imps'], 0, ',', '.');
                    $tabla .= "<td><b>" . $total_imps . "</b></td>";
                } elseif ($col[0] == "clicks") {
                    $total_clicks = number_format($reporte[4]['clicks'], 0, ',', '.');
                    $tabla .= "<td><b>" . $total_clicks . "</b></td>";
                } elseif ($col[0] == "ctr") {
                    $total_ctr = number_format($reporte[4]['ctr'], 2, ',', '.');
                    $tabla .= "<td><b>" . $total_ctr . "%</b></td>";
                } elseif ($col[0] == "revenue" || $col[0] == 'total_revenue') {
                    $total_revenue = number_format($reporte[4]['revenue'], 2, ',', '.');
                    $tabla .= "<td><b>US$ " . $total_revenue . "</b></td>";
                } else {
                    if ($col[0] != "sitio_id") {
                        $tabla .= "<td><b>Totales:</b></td>";
                    }
                }
            }

            $tabla .= '<tr>';

            $tabla .= '</table>';

            return $tabla;
        }
    }

    private function generar_tabla_anunciantes_campania_DFP_PDF($reporte, $columnas) {
        if ($reporte) {

            $tabla = '<table width="100%">';
            $tabla .= '<tr>';
            $tabla .= '<td><b>Anunciante:</b></td>';
            $tabla .= '<td colspan="6">' . htmlentities($this->anunciante_nombre->nombre, ENT_QUOTES, 'UTF-8') . '</td>';
            $tabla .= '</tr>';

            $tabla .= '<tr>';
            $tabla .= '<td><b>Fechas del reporte:</b></td>';
            $tabla .= '<td colspan="6">' . htmlentities($this->periodo_suscripcion, ENT_QUOTES, 'UTF-8') . '</td>';
            $tabla .= '</tr>';

            $tabla .= '<tr>';
            $tabla .= '<td colspan="7"> </td>';
            $tabla .= '</tr>';
            $tabla .= '</table>';


            $tabla .= '<table width="100%">';

// ENCABEZADO
            $tabla .= '<tr>';

            foreach ($columnas as $col)
                $tabla .= '<th style="text-align:center;padding:4px;font-weight:bold;background-color: #D99795;border-top: 1px solid #000;border-right: 1px solid #000;border-left: 1px solid #000;color:#FFF">' . htmlentities($col[1], ENT_QUOTES, 'UTF-8') . '</th>';

            $tabla .= '</tr>';

// CUERPO
            $contador = 0;
            foreach ($reporte[0] as $report) {
                $contador++;

                if ($contador > 1) {
                    $color = 'background-color:#E2E4FF;text-align:center;';
                    $contador = 0;
                } else {
                    $color = 'background-color:#FFFFFF;text-align:center;';
                }

                $tabla .= '<tr>';
                foreach ($columnas as $col) {
                    $tabla .= '<td style="' . $color . '">';

                    switch ($col[2]) {
                        case 'impresiones':
                            $tabla .= number_format($report[$col[2]], 0, ',', '.');
                            break;
                        case 'clicks':
                            $tabla .= number_format($report[$col[2]], 0, ',', '.');
                            break;
                        case 'ctr':
                            $tabla .= number_format($report[$col[2]], 2, ',', '.') . '%';
                            break;
                        case 'revenue':
                            $tabla .= 'US$ ' . number_format($report[$col[2]], 2, ',', '.');
                            break;
                        default:
                            $tabla .= $report[$col[2]];
                            break;
                    }

                    $tabla .= '</td>';
                }
                $tabla .= '</tr>';
            }

// TOTALES
            $tabla .= '<tr>';

            foreach ($columnas as $col) {
                if ($col[0] == "imps") {
                    $total_imps = number_format($reporte[4]['imps'], 0, ',', '.');
                    $tabla .= '<td style="text-align:center;padding:4px;font-weight:bold;background-color: #D99795;border-top: 1px solid #000;border-right: 1px solid #000;border-left: 1px solid #000;color:#FFF"><b>' . $total_imps . "</b></td>";
                } elseif ($col[0] == "clicks") {
                    $total_clicks = number_format($reporte[4]['clicks'], 0, ',', '.');
                    $tabla .= '<td style="text-align:center;padding:4px;font-weight:bold;background-color: #D99795;border-top: 1px solid #000;border-right: 1px solid #000;border-left: 1px solid #000;color:#FFF"><b>' . $total_clicks . "</b></td>";
                } elseif ($col[0] == "ctr") {
                    $total_ctr = number_format($reporte[4]['ctr'], 2, ',', '.');
                    $tabla .= '<td style="text-align:center;padding:4px;font-weight:bold;background-color: #D99795;border-top: 1px solid #000;border-right: 1px solid #000;border-left: 1px solid #000;color:#FFF"><b>' . $total_ctr . "%</b></td>";
                } elseif ($col[0] == "revenue" || $col[0] == 'total_revenue') {
                    $total_revenue = number_format($reporte[4]['revenue'], 2, ',', '.');
                    $tabla .= '<td style="text-align:center;padding:4px;font-weight:bold;background-color: #D99795;border-top: 1px solid #000;border-right: 1px solid #000;border-left: 1px solid #000;color:#FFF"><b>US$ ' . $total_revenue . "</b></td>";
                } else {
                    if ($col[0] != "sitio_id") {
                        $tabla .= '<td style="text-align:center;padding:4px;font-weight:bold;background-color: #D99795;border-top: 1px solid #000;border-right: 1px solid #000;border-left: 1px solid #000;color:#FFF">&nbsp;</td>';
                    }
                }
            }

            $tabla .= '<tr>';

            $tabla .= '</table>';

            return $tabla;
        }
    }

    private function enviar_email_reporte($destinatario, $titulo, $contenido, $archivo) {
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->SMTPAuth = true; // habilitamos la autenticaciÃ³n SMTP
        $mail->SetLanguage('en', BASEPATH . '/application/libraries/PHPMailer/language/');
        $mail->Host = "ssl://smtp.gmail.com";      // sets GMAIL as the SMTP server
        $mail->Port = 465;
        $mail->Username = "mailing@mediafem.com";  // la cuenta de correo GMail
        $mail->Password = "Sebastian02";            // password de la cuenta GMail
        $mail->FromName = 'MediaFem para anunciantes'; // nombre de la persona que envÃ­a el correo
        $mail->From = 'noreply@mediafem.com';  //Quien envÃ­a el correo
        $mail->Subject = $titulo;
        $mail->IsHTML(true);
        $mail->Body = $contenido;
        $mail->ContentType = 'text/html; charset=UTF-8';
        $mail->CharSet = 'UTF-8';
        $mail->AddAttachment($archivo);
        $mail->AddAddress($destinatario);
        $estado = $mail->Send();
        return $estado;
    }

    private function _dateDiff($start, $end) {
        $start_ts = strtotime($start);
        $end_ts = strtotime($end);
        $diff = $end_ts - $start_ts;
        return round($diff / 86400);
    }

}

?>