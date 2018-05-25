<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Api {

    public $user_notacion = 0;
    public $error_al_obtener_reporte = 0;

    function __construct() {
        $this->ci = & get_instance();

        $this->ci->load->library('session');

        $this->ci->load->model('categorias');
        $this->ci->load->model('columnas');
        $this->ci->load->model('constants');
        $this->ci->load->model('cotizaciones_diarias');
        $this->ci->load->model('formatosdfp');
        $this->ci->load->model('inventario_anunciantes');
        $this->ci->load->model('paises');
        $this->ci->load->model('placements');
        $this->ci->load->model('sites');
        $this->ci->load->model('sitescategories');
        $this->ci->load->model('users');

        $result_multiplicacion_volumen = $this->ci->constants->get_constant_by_id(ID_MULTIPLICAR_VOLUMEN);
        $this->multiplicacion_volumen = $result_multiplicacion_volumen->value;

        $notacion = $this->ci->users->get_notacion_user($this->ci->tank_auth->get_user_id());
        if ($notacion)
            $this->user_notacion = $notacion->notacion;
    }

    function notacion($number, $decimal = 2) {
        return $this->ci->user_data->notacion == 0 ? number_format($number, $decimal, '.', ',') : number_format($number, $decimal, ',', '.');
    }

    public function get_Devices() {
        try {
            $user = new DfpUser();

            $pqlService = $user->GetService('PublisherQueryLanguageService', 'v201208');

            $selectStatement = new Statement("SELECT * FROM Device_Category");

            // Get all cities.
            // A limit of 500 is set here. You may want to page through such a large
            // result set.
            // For criteria that do not have a 'targetable' property, that predicate
            // may be left off, i.e. just "SELECT * FROM Browser_Groups LIMIT 500".
            $resultSet = $pqlService->select($selectStatement);

            // Display results.
            if (isset($resultSet)) {
                new_var_dump($resultSet);
            } else {
                print "No results returned.\n";
            }
        } catch (Exception $ex) {
            echo $ex->getMessage();
            return false;
        }
    }

    public function get_countries() {
        try {
            $user = new DfpUser();

            $pqlService = $user->GetService('PublisherQueryLanguageService', 'v201208');

            $selectStatement = new Statement("SELECT * FROM City WHERE regioncode = 'AR-B'");

            // Get all cities.
            // A limit of 500 is set here. You may want to page through such a large
            // result set.
            // For criteria that do not have a 'targetable' property, that predicate
            // may be left off, i.e. just "SELECT * FROM Browser_Groups LIMIT 500".
            $resultSet = $pqlService->select($selectStatement);

            // Display results.
            if (isset($resultSet)) {
                $columnLabels = array_map(
                        create_function('$columnType', 'return $columnType->labelName;'), $resultSet->columnTypes);
                printf("Columns are: %s\n", implode(', ', $columnLabels));
                $i = 0;
                foreach ($resultSet->rows as $row) {
                    $values = array_map(create_function('$value', 'return $value->value;'), $row->values);
                    printf("%d) %s\n", $i, implode(', ', $values));
                    echo "<br>";
                    $i++;
                }
            } else {
                print "No results returned.\n";
            }
        } catch (Exception $ex) {
            echo $ex->getMessage();
            return false;
        }
    }

    public function soy_desarrollador() {
        if ($this->ci->tank_auth->get_user_id() == 143 || $this->ci->tank_auth->get_user_id() == 230 || $this->ci->tank_auth->get_user_id() == 242)
            return true;

        return false;
    }

    public function pausar_campania($id, $id_dfp) {
        try {
            $user = new DfpUser();

            $orderService = $user->GetService('OrderService', 'v201208');

            $vars = MapUtils::GetMapEntries(array('orderId' => new NumberValue($id_dfp)));

            $filterStatement = new Statement("WHERE id = :orderId", $vars);

            $page = $orderService->getOrdersByStatement($filterStatement);

            if (isset($page->results)) {
                $orders = $page->results;

                array_filter($orders, create_function('$order', 'return !$order->isArchived;'));

                foreach ($orders as $order)
                    $order->status = 'PAUSED';

                $orders = $orderService->updateOrders($orders);

                $this->ci->campanias->update_campania($id, array('estado' => 'PAUSADA'));
            }
        } catch (Exception $ex) {
            //echo $ex->getMessage();
            return false;
        }
    }

    public function reactivar_campania($id, $id_dfp) {
        try {
            $user = new DfpUser();

            $orderService = $user->GetService('OrderService', 'v201208');

            $vars = MapUtils::GetMapEntries(array('orderId' => new NumberValue($id_dfp)));

            $filterStatement = new Statement("WHERE id = :orderId", $vars);

            $page = $orderService->getOrdersByStatement($filterStatement);

            if (isset($page->results)) {
                $orders = $page->results;

                array_filter($orders, create_function('$order', 'return !$order->isArchived;'));

                foreach ($orders as $order)
                    $order->status = 'APPROVED';

                $orders = $orderService->updateOrders($orders);

                $this->ci->campanias->update_campania($id, array('estado' => 'APROBADA'));
            }
        } catch (Exception $ex) {
            //echo $ex->getMessage();
            return false;
        }
    }

    public function get_lineitems($orderId) {
        try {
            $user = new DfpUser();

            // Get the LineItemService.
            $lineItemService = $user->GetService('LineItemService', 'v201208');

            // Create bind variables.
            $vars = MapUtils::GetMapEntries(array('orderId' => new NumberValue($orderId)));

            // Create a statement to only select line items that need creatives
            // from a given order.
            $filterStatement = new Statement("WHERE orderId = :orderId LIMIT 500", $vars);

            // Get line items by statement.
            $page = $lineItemService->getLineItemsByStatement($filterStatement);

            new_var_dump($page);
        } catch (Exception $exc) {
            echo $exc->getMessage();
            return FALSE;
        }
    }

    public function reporte_by_orden_DFP($orderId) {
        try {
            $user = new DfpUser();

            $lineItemService = $user->GetService('LineItemService', 'v201208');

            $page = new LineItemPage();
            $vars = MapUtils::GetMapEntries(array('orderId' => new NumberValue($orderId)));
            $filterStatement = new Statement(NULL, $vars);
            $offset = 0;

            $customFieldIds = array();
            do {
                $filterStatement->query = sprintf('WHERE orderId = :orderId LIMIT 500 OFFSET %d', $offset);

                $page = $lineItemService->getLineItemsByStatement($filterStatement);

                if (isset($page->results)) {
                    foreach ($page->results as $lineItem) {
                        if (isset($lineItem->customFieldValues)) {
                            foreach ($lineItem->customFieldValues as $customFieldValue) {
                                $customFieldId = $customFieldValue->customFieldId;
                                if (!in_array($customFieldId, $customFieldIds))
                                    $customFieldIds[] = $customFieldId;
                            }
                        }
                    }
                }

                $offset += 500;
            } while ($offset < $page->totalResultSetSize);

            $reportService = $user->GetService('ReportService', 'v201208');

            $reportJob = new ReportJob();

            $reportQuery = new ReportQuery();

            $reportQuery->dateRangeType = 'CUSTOM_DATE';
            $start_date = new Date(2000, 1, 1);
            $end_date = new Date(2015, 12, 31);
            $reportQuery->startDate = $start_date;
            $reportQuery->endDate = $end_date;

            $reportQuery->dimensions = array('LINE_ITEM');
            $reportQuery->customFieldIds = $customFieldIds;
            $reportQuery->columns = array('TOTAL_LINE_ITEM_LEVEL_CPM_AND_CPC_REVENUE');
            $reportJob->reportQuery = $reportQuery;

            $filterStatement->query = 'WHERE ORDER_ID = :orderId';
            $reportQuery->statement = $filterStatement;

            $reportJob = $reportService->runReportJob($reportJob);

            do {
                $reportJob = $reportService->getReportJob($reportJob->id);
            } while ($reportJob->reportJobStatus == 'IN_PROGRESS');

            if ($reportJob->reportJobStatus == 'FAILED') {
                return FALSE;
            } else {
                $reportJobId = $reportJob->id;
                $fileName = 'report' . rand(100, 99999) . '.txt.gz';

                $filePath = dirname(__FILE__) . '/tmp/' . $fileName;

                $downloadUrl = $reportService->getReportDownloadURL($reportJobId, 'TSV');

                ReportUtils::DownloadReport($downloadUrl, $filePath);

                $lineas = gzfile($filePath);

                $totales = $lineas[sizeof($lineas) - 1];

                $totales = explode("\t", $totales);

                return str_replace('$', '', $totales[sizeof($totales) - 1]);
            }
        } catch (Exception $exc) {
            echo $exc->getMessage();
            return FALSE;
        }
    }

    public function getLineItemsByAdv($token, $adserver, $anunciante_id, $orderId = '0') {
        if ($adserver == '1') {
            // appnexus
            try {
                $request = new Request();
                $request->method = 'get';
                $request->token = $token;
                $request->uri = BASE_URI . '/line-item?advertiser_id=' . $anunciante_id;
                $res = Caller::call($request);
                while (!isset($res->{'response'}->{'line-items'})) {
                    $res = Caller::call($request);
                }
                $texto = "line-items";
                return $res->response->$texto;
            } catch (Exception $ex) {
                return false;
            }
        } else if ($adserver == '0') {
            // DFP
            try {
                $user = $this->getDFPUser();
                $lineItemService = $user->GetService('LineItemService', 'v201208');
                $vars = MapUtils::GetMapEntries(array('orderId' => new NumberValue($orderId)));
                $filterStatement = new Statement("WHERE orderId = :orderId  AND isArchived = false LIMIT 500", $vars);
                $page = $lineItemService->getLineItemsByStatement($filterStatement);

                return $page->results;
            } catch (Exception $ex) {
                return false;
            }
        }
    }

    public function reporteDinamicoPorSitio($token, $anunciante_id, $filtros_li, $filtros_cr, $filtros_sizes, $filtros_paises, $columnas, $grupos, $interval, $rango, $fecha_desde, $fecha_hasta) {
        // traigo todos los sitios segun el intervalo seleccionado.
        $sitios = $this->ci->sites->get_all_sites();

        if (!$sitios)
            return FALSE;

        // traigo todas las categorias existentes y armo el string
        $categorias = $this->ci->categorias->get_categorias();
        $filtro_categorias = '';
        foreach ($categorias as $categoria) {
            $filtro_categorias .= $categoria->id . 'o';
        }

        // si aplique un filtro por categorias, filtro los sitios que corresponden a esa categoria.
        if ($filtro_categorias != 0) {
            $sitios = $this->_filtrarSitiosPorCategorias($sitios, $filtro_categorias);

            if ($sitios == '')
                return FALSE;
        }

        $filtros_li = urldecode($filtros_li);

        if ($filtros_li) {
            $arr_por[] = 'Canal Tem&aacute;tico';

            $partes_li = explode(";", $filtros_li);

            foreach ($partes_li as $partes) {
                if (!empty($partes))
                    $arr_li[] = $partes;
            }

            $arr_filtros[] = array('line_item_id' => $arr_li);
        }

        $filtros_cr = urldecode($filtros_cr);

        if ($filtros_cr) {
            $arr_por[] = 'Creatividad';

            $partes_cr = explode(";", $filtros_cr);

            foreach ($partes_cr as $partes) {
                if (!empty($partes))
                    $arr_cr[] = $partes;
            }

            $arr_filtros[] = array('creative_id' => $arr_cr);
        }

        $filtros_sizes = urldecode($filtros_sizes);

        if ($filtros_sizes) {
            $arr_por[] = 'Tama&ntilde;o';

            $partes_sizes = explode(";", $filtros_sizes);
            foreach ($partes_sizes as $partes) {
                if (!empty($partes))
                    $arr_sizes[] = $partes;
            }

            $arr_filtros[] = array('size' => $arr_sizes);
        }

        $filtros_paises = urldecode($filtros_paises);

        if ($filtros_paises) {
            $arr_por[] = 'Pa&iacute;s';

            $partes_paises = explode(";", $filtros_paises);

            foreach ($partes_paises as $partes) {
                if (!empty($partes))
                    $arr_paises[] = $partes;
            }

            $arr_filtros[] = array('geo_country' => $arr_paises);
        }

        $filtrado_por = "";
        if (isset($arr_por)) {
            foreach ($arr_por as $por) {
                $filtrado_por .= $por . ",";
            }

            $filtrado_por = $this->_AString($filtrado_por);
        }

        $arr_filtros[] = array('advertiser_id' => $anunciante_id);

        $columnas = urldecode($columnas);

        if ($columnas) {
            $partes_columnas = explode(";", $columnas);

            foreach ($partes_columnas as $partes) {
                if (!empty($partes))
                    $arr_columnas[] = $partes;
            }
        }

        if ($grupos) {
            $grupos = urldecode($grupos);

            $partes_grupos = explode(";", $grupos);

            foreach ($partes_columnas as $partes) {
                if (!empty($partes)) {
                    $col = $this->ci->columnas->get_columna_by_id($partes);
                    $txt_agrupado = $col->descripcion . ',';
                    $arr_columnas[] = $partes;
                }
            }
        }

        $agrupado_por = "";
        if (isset($txt_agrupado))
            $agrupado_por = $this->_AString($txt_agrupado);

        $arr_grupos = null;

        if ($interval != "cumulative") {
            $arr_columnas[] = $interval;
            $orden = $interval;
        }

        $rango = urldecode($rango);

        $fechas = $this->_fechasPorIntervalo($rango, $fecha_desde, $fecha_hasta);

        if ($rango != "lifetime")
            $rango = null;


        $res_columnas = $this->ci->columnas->get_all_columnas();
        foreach ($res_columnas as $col) {
            for ($i = 0; $i < count($arr_columnas); $i++) {
                if ($col->id == $arr_columnas[$i]) {
                    $arr_columnas_ordenado[] = $col->id;
                }
            }
        }

        $arr_columnas_ordenado[] = "site_id";


        $report = obtenerReporteDinamico_DFP($token, $anunciante_id, $rango, $arr_filtros, $arr_columnas_ordenado, $fechas['desde'], $fechas['hasta']);
    }

    function obtenerOrdenesPorAnunciante_DFP($anunciante_id) {

        $user = $this->getDFPUser();
        $orderService = $user->GetService('OrderService', 'v201208');
        $vars = MapUtils::GetMapEntries(array('advertiserId' => new NumberValue($anunciante_id)));
        $filterStatement = new Statement("WHERE advertiserId = :advertiserId LIMIT 500", $vars);

        $order = $orderService->getOrdersByStatement($filterStatement);

        // new_var_dump($order);

        return $order->results;
        /* } catch (Exception $ex) {
          print $ex->getMessage() . " \n";
          //return false;
          } */
    }

    function infoOrden_DFP($orden_id) {
        try {
            $user = $this->getDFPUser();
            $orderService = $user->GetService('OrderService', 'v201208');

            $order = $orderService->getOrder($orden_id);

            if (isset($order))
                return $order;

            return FALSE;
        } catch (Exception $e) {
            print $e->getMessage() . "\n";
            return FALSE;
        }
    }

    function lineItemsByOrder_DFP($orden_id) {
        try {
            $user = $this->getDFPUser();
            $lineItemService = $user->GetService('LineItemService', 'v201208');

            $vars = MapUtils::GetMapEntries(array('orderId' => new NumberValue($orden_id)));

            $filterStatement = new Statement("WHERE orderId = :orderId", $vars);

            $page = $lineItemService->getLineItemsByStatement($filterStatement);

            return $page;
        } catch (Exception $e) {
            print $e->getMessage() . "\n";
            return FALSE;
        }
    }

    function obtenerReporteDinamico_DFP($orden_id, $intervalo, $rango, $fecha_desde, $fecha_hasta, $columnas, $dimensiones, $filtros) {
        try {
            $user = $this->getDFPUser();
            $reportService = $user->GetService('ReportService', 'v201208');

            // filtro la orden seleccionada
            $vars = MapUtils::GetMapEntries(array('orderId' => new NumberValue($orden_id)));
            $filterStatementText = 'WHERE order_id = :orderId';

            // filtro los paises
            if ($filtros['paises']) {
                $filtros['paises'] = explode(';', trim($filtros['paises'], ';'));
                foreach ($filtros['paises'] as $pais) {
                    $paisDB = $this->ci->paises->get_pais_by_id($pais);
                    if ($paisDB)
                        $filtroPaises[] = array('id' => $paisDB->id, 'id_dfp' => $paisDB->id_dfp, 'descripcion' => $paisDB->descripcion);
                }

                $total_filtro_paises = sizeof($filtroPaises);
                $filterStatementText .= ' AND COUNTRY_CRITERIA_ID IN (';
                for ($a = 0; $a < $total_filtro_paises; $a++) {
                    if ($a == ($total_filtro_paises - 1)) {
                        $filterStatementText .= $filtroPaises[$a]['id_dfp'];
                    } else {
                        $filterStatementText .= $filtroPaises[$a]['id_dfp'] . ', ';
                    }
                }
                $filterStatementText .= ')';
            }

            // filtro los lineitems
            /*
              if ($filtros['lineItems'] != 0) {
              $total_filtro_lineItems = sizeof($filtros['lineItems']);

              $filterStatementText .= ' AND LINE_ITEM_ID IN (';
              for ($a = 0; $a < $total_filtro_lineItems; $a++) {
              if ($a == ($total_filtro_lineItems - 1)) {
              $filterStatementText .= $filtros['lineItems'][$a];
              } else {
              $filterStatementText .= $filtros['lineItems'][$a] . ', ';
              }
              }
              $filterStatementText .= ')';
              }
             *
             */

            // filtro las creatividades
            if ($filtros['creatividades']) {
                $total_filtro_creatividades = sizeof($filtros['creatividades']);

                $filterStatementText .= ' AND CREATIVE_ID IN (';
                for ($a = 0; $a < $total_filtro_creatividades; $a++) {
                    if ($a == ($total_filtro_creatividades - 1)) {
                        $filterStatementText .= $filtros['creatividades'][$a];
                    } else {
                        $filterStatementText .= $filtros['creatividades'][$a] . ', ';
                    }
                }
                $filterStatementText .= ')';
            }

            $filterStatement = new Statement($filterStatementText, $vars);

            $reportJob = new ReportJob();

            // dateRangeType SIEMPRE ES CUSTOM_DATE. LAS FECHAS LAS MANEJA LA FUNCION _fechasPorIntervalo
            $reportQuery = new ReportQuery();
            $reportQuery->dateRangeType = "CUSTOM_DATE";

            $fechas = $this->_fechasPorIntervalo($rango, $fecha_desde, $fecha_hasta);

            if ($fechas['desde'] == FALSE || $fechas['hasta'] == FALSE)
                return FALSE;

            $start_date = explode(" ", $fechas['desde']);
            $end_date = explode(" ", $fechas['hasta']);

            list($dia_desde, $mes_desde, $anio_desde) = explode("-", $start_date[0]);
            list($dia_hasta, $mes_hasta, $anio_hasta) = explode("-", $end_date[0]);

            $reportQuery->startDate = new Date($dia_desde, $mes_desde, $anio_desde);
            $reportQuery->endDate = new Date($dia_hasta, $mes_hasta, $anio_hasta);

            $dimensions = array('ORDER', 'DATE');

            if ($intervalo == 'month')
                $dimensions[] = 'MONTH';

            /*
              $dimensions = array('ORDER');
              if ($intervalo == 'day') {
              $dimensions[] = 'DATE';
              } else if ($intervalo == 'month') {
              $dimensions[] = 'MONTH';
              } */


            $dimensiones = explode(';', trim($dimensiones, ';'));
            foreach ($dimensiones as $dimension) {
                if ($dimension == 'size') {
                    $dimensions[] = 'CREATIVE_SIZE';
                } else if ($dimension == 'geo_country') {
                    $dimensions[] = 'COUNTRY_NAME';
                } else if ($dimension == 'site_name') {
                    $dimensions[] = 'AD_UNIT';
                } else if ($dimension == 'creative_name') {
                    $dimensions[] = 'CREATIVE';
                } else if ($dimension == 'line_item_name') {
                    $dimensions[] = 'LINE_ITEM';
                }
            }

            $reportQuery->dimensions = $dimensions;

            $columnas = explode(';', trim($columnas, ';'));
            foreach ($columnas as $columna) {
                /*
                  if ($columna == 'clicks') {
                  $columns[] = 'TOTAL_LINE_ITEM_LEVEL_CLICKS';
                  } else if ($columna == 'imps') {
                  $columns[] = 'TOTAL_LINE_ITEM_LEVEL_IMPRESSIONS';
                  } else*
                 */
                if ($columna == 'ctr') {
                    $columns[] = 'TOTAL_LINE_ITEM_LEVEL_CTR';
                } else if ($columna == 'revenue' || $columna == 'total_revenue') {
                    $columns[] = 'TOTAL_LINE_ITEM_LEVEL_CPM_AND_CPC_REVENUE';
                }

                if ($columna == 'total_views')
                    $columns[] = 'VIDEO_INTERACTION_COMPLETE';
            }

            $columns[] = 'TOTAL_LINE_ITEM_LEVEL_CLICKS';
            $columns[] = 'TOTAL_LINE_ITEM_LEVEL_IMPRESSIONS';

            if (!isset($columns))
                return FALSE;

            $reportQuery->columns = $columns;

            $reportQuery->adUnitView = 'HIERARCHICAL';

            $reportQuery->statement = $filterStatement;
            $reportJob->reportQuery = $reportQuery;

            // corro el reporte
            $reportJob = $reportService->runReportJob($reportJob);

            $intentos = 0;
            do {
                try {
                    $intentos++;
                    $error_reporte = false;
                    $reportJob = $reportService->getReportJob($reportJob->id);
                } catch (Exception $ex) {
                    //print "error: " . $ex->getCode() . " - " . $ex->getMessage() . "<br/><br/>";
                    if ($ex->getMessage() == "[QuotaError.EXCEEDED_QUOTA @ ]")
                        sleep(2);
                    $error_reporte = true;
                }
            } while ($reportJob->reportJobStatus == 'IN_PROGRESS' || $error_reporte == true);

            if ($reportJob->reportJobStatus == 'FAILED') {
                return 'ERROR';
            } else {

                $reportJobId = $reportJob->id;
                $fileName = 'report.txt.gz';
                $filePath = dirname(__FILE__) . '/tmp/' . $fileName;
                $downloadUrl = $reportService->getReportDownloadURL($reportJobId, 'TSV');
                ReportUtils::DownloadReport($downloadUrl, $filePath);

                $lineas = gzfile($filePath);
                $total_lineas = sizeof($lineas);

                if ($total_lineas > 2) {
                    // traigo todos los sitios
                    $sitios = $this->ci->sites->get_all_sites();
                    if (!$sitios)
                        return FALSE;

                    // traigo todas las categorias existentes y armo el string
                    $categorias = $this->ci->categorias->get_categorias();
                    $filtro_categorias = '';
                    foreach ($categorias as $categoria)
                        $filtro_categorias .= $categoria->id . 'o';

                    // filtro los sitios categorizados.
                    $sitios = $this->_filtrarSitiosPorCategorias($sitios, $filtro_categorias);
                    if ($sitios == '')
                        return FALSE;

                    $cont = 0;
                    $totales['imps'] = 0;
                    $totales['vistas'] = 0;
                    $totales['ctr'] = 0;
                    $totales['revenue'] = 0;
                    $totales['clicks'] = 0;

                    // asigno datos a mostrar
                    foreach ($lineas as $linea) {
                        $campos = explode("\t", $linea);
                        if ($cont == 0) {
                            $encabezados = $campos;
                        } else if ($cont >= 1 && trim($campos[0]) != 'Total' && !is_numeric(trim($campos[0]))) {
                            for ($a = 0; $a < sizeof($encabezados); $a++) {
                                if (trim($encabezados[$a]) == 'Order')
                                    $array['order_name'] = $campos[$a];

                                if (trim($encabezados[$a]) == 'Line item')
                                    $array['lineItem_name'] = $campos[$a];

                                if (trim($encabezados[$a]) == 'Creative size')
                                    $array['creatividad_tamano'] = $campos[$a];

                                // esto es obsoleto. pais_name se guarda cuando
                                // consulta a la DB el nombre del pais segun su ID
                                if (trim($encabezados[$a]) == 'Country')
                                    $array['pais_name'] = $campos[$a];

                                if (trim($encabezados[$a]) == 'Ad unit 1') {
                                    $publisher_name_orig = $campos[$a];
                                    $publisher_name = explode('_', $publisher_name_orig);
                                    if (isset($publisher_name[1])) {
                                        if (is_numeric($publisher_name[1])) {
                                            $array['publisher_name'] = $publisher_name[0];
                                        } else {
                                            $array['publisher_name'] = $publisher_name_orig;
                                        }
                                    } else {
                                        $array['publisher_name'] = $publisher_name_orig;
                                    }
                                }

                                if (trim($encabezados[$a]) == 'Ad unit 2') {
                                    $sitio_name_orig = $campos[$a];
                                    $sitio_name = explode('_', $sitio_name_orig);
                                    if (isset($sitio_name[1])) {
                                        if (is_numeric($sitio_name[1])) {
                                            $array['sitio_name'] = $sitio_name[0];
                                        } else {
                                            $array['sitio_name'] = $sitio_name_orig;
                                        }

                                        if ($array['sitio_name'] == 'N/A')
                                            $array['sitio_name'] = $array['publisher_name'];
                                    }else {
                                        if ($sitio_name[0] == 'N/A') {
                                            $array['sitio_name'] = $array['publisher_name'];
                                        } else {
                                            $array['sitio_name'] = $sitio_name_orig;
                                        }
                                    }
                                }


                                if (isset($array['sitio_name'])) {
                                    if ($array['sitio_name'] == 'Black_Life') {
                                        $array['sitio_name'] = 'blacklife.glam.com';
                                    } else if ($array['sitio_name'] == 'Bliss') {
                                        $array['sitio_name'] = 'www.bliss.com';
                                    } else if ($array['sitio_name'] == 'Brash') {
                                        $array['sitio_name'] = 'www.brash.com';
                                    } else if ($array['sitio_name'] == 'Entertainment') {
                                        $array['sitio_name'] = 'entertainment.glam.com';
                                    } else if ($array['sitio_name'] == 'Family_and_mom') {
                                        $array['sitio_name'] = 'www.glam.com/tag/family-moms';
                                    } else if ($array['sitio_name'] == 'Foodie') {
                                        $array['sitio_name'] = 'recipes.foodie.com';
                                    } else if ($array['sitio_name'] == 'Health_and_wellness') {
                                        $array['sitio_name'] = 'http://www.tend.com/';
                                    } else if ($array['sitio_name'] == 'Living') {
                                        $array['sitio_name'] = 'living.glam.com';
                                    } else if ($array['sitio_name'] == 'RON') {
                                        $array['sitio_name'] = 'http://www.glammedia.com/';
                                    } else if ($array['sitio_name'] == 'Rotten_tomatoes') {
                                        $array['sitio_name'] = 'uk.rottentomatoes.com';
                                    } else if ($array['sitio_name'] == 'Stripes_Sequins') {
                                        $array['sitio_name'] = 'www.stripesandsequins.com';
                                    } else if ($array['sitio_name'] == 'Style') {
                                        $array['sitio_name'] = 'www.glam.com';
                                    } else if ($array['sitio_name'] == 'Taaz') {
                                        $array['sitio_name'] = 'www.taaz.com';
                                    } else if ($array['sitio_name'] == 'Windows_to_the_Universe') {
                                        $array['sitio_name'] = 'www.windows2universe.org';
                                    } else if ($array['sitio_name'] == 'Young_Black_and_Fabulous') {
                                        $array['sitio_name'] = 'theybf.com';
                                    } else {
                                        $array['sitio_name'] = $array['sitio_name'];
                                    }
                                }

                                if (trim($encabezados[$a]) == 'Ad unit 3')
                                    $array['espacio_name'] = $campos[$a];

                                if (trim($encabezados[$a]) == 'Creative')
                                    $array['creatividad_name'] = $campos[$a];

                                if (trim($encabezados[$a]) == 'Date') {
                                    $array['fecha'] = explode('/', $campos[$a]);

                                    if ($array['fecha'][1] <= 9)
                                        $array['fecha'][1] = '0' . $array['fecha'][1];

                                    if ($array['fecha'][0] <= 9)
                                        $array['fecha'][0] = '0' . $array['fecha'][0];

                                    $array['fecha'] = $array['fecha'][1] . '/' . $array['fecha'][0] . '/20' . $array['fecha'][2];
                                }

                                if (trim($encabezados[$a]) == 'Month')
                                    $array['mes'] = mesEspaniol($campos[$a]);

                                if (trim($encabezados[$a]) == 'Order ID')
                                    $array['order_id'] = $campos[$a];

                                if (trim($encabezados[$a]) == 'Line item ID')
                                    $array['lineItem_id'] = $campos[$a];

                                if (trim($encabezados[$a]) == 'Country ID') {
                                    $array['pais_id'] = $campos[$a];

                                    $pais_id = $this->ci->paises->get_pais_by_id_DFP($array['pais_id']);
                                    if ($pais_id != NULL) {
                                        $array['pais_name'] = $pais_id->descripcion;
                                    } else {
                                        $array['pais_name'] = 'Desconocido';
                                    }
                                }

                                if (trim($encabezados[$a]) == 'Ad unit ID 1')
                                    $array['publisher_id'] = $campos[$a];

                                if (trim($encabezados[$a]) == 'Ad unit ID 2')
                                    $array['sitio_id'] = $campos[$a];

                                if (trim($encabezados[$a]) == 'Ad unit ID 3')
                                    $array['espacio_id'] = $campos[$a];

                                if (trim($encabezados[$a]) == 'Creative ID')
                                    $array['creatividad_id'] = $campos[$a];

                                if (trim($encabezados[$a]) == 'Total impressions') {
                                    $array['impresiones'] = str_replace('"', '', $campos[$a]);
                                    $array['impresiones'] = str_replace(',', '', $array['impresiones']);
                                    //$totales['imps'] += $array['impresiones'];
                                }

                                if (trim($encabezados[$a]) == 'Complete') {
                                    $array['vistas'] = str_replace('"', '', $campos[$a]);
                                    $array['vistas'] = str_replace(',', '', $array['vistas']);
                                    //$totales['imps'] += $array['impresiones'];

                                    if ($array['vistas'] == '-')
                                        $array['vistas'] = 0;
                                }

                                if (trim($encabezados[$a]) == 'Total clicks') {
                                    $array['clicks'] = str_replace('"', '', $campos[$a]);
                                    $array['clicks'] = str_replace(',', '', $array['clicks']);
                                    //$totales['clicks'] += $array['clicks'];
                                }

                                if (trim($encabezados[$a]) == 'Total CTR') {
                                    $array['ctr'] = str_replace('"', '', $campos[$a]);
                                    $array['ctr'] = str_replace('%', '', $array['ctr']);
                                }

                                if (trim($encabezados[$a]) == 'Total CPM and CPC revenue') {
                                    if (isset($campos[$a]) || $campos[$a] != '') {
                                        $array['revenue'] = trim(str_replace('$', '', $campos[$a]));
                                        //$totales['revenue'] += $array['revenue'];
                                    } else {
                                        $array['revenue'] = '0';
                                    }
                                }
                            }

                            if ($filtros['lineItems'] != 0) { // Si selecciono alguna segmentacione n particular
                                if (in_array($array['lineItem_id'], $filtros['lineItems'])) {

                                    $lineItemName = $array['lineItem_name'];

                                    if (strstr($lineItemName, '(**)') || strstr($lineItemName, '(++)')) {
                                        // si hay (**) o (++) entonces compruebo el texto siguiente del LI y lo asigno al original.
                                        if (strstr($lineItemName, '(**)')) {
                                            $name = explode('(**)', $lineItemName);
                                            $name = substr(strtoupper(trim($name[1])), 0, 4);
                                        }

                                        if (strstr($lineItemName, '(++)')) {
                                            $name = explode('(++)', $lineItemName);
                                            $name = substr(strtoupper(trim($name[1])), 0, 4);
                                        }

                                        $lineItemName = $name;
                                    } else {
                                        $lineItemName = substr(strtoupper($lineItemName), 0, 4);
                                    }

                                    $lineItems_Seleccionados[$array['lineItem_id']] = $lineItemName;
                                }
                            }

                            $resultados[] = $array;
                        }

                        $cont++;
                    }

                    if (isset($resultados) && $resultados != NULL) {
                        if ($intervalo != 'day' && $intervalo != 'month') {
                            if ($dimensiones && in_array('site_name', $dimensiones) && !in_array('geo_country', $dimensiones) && !in_array('line_item_name', $dimensiones)) {
                                foreach ($resultados as $result) {
                                    $sitio_id = $result['sitio_id'];
                                    if (isset($new_resultados[$sitio_id])) {

                                        if (isset($result['impresiones']))
                                            $new_resultados[$sitio_id]['impresiones'] += $result['impresiones'];

                                        if (isset($result['vistas']))
                                            $new_resultados[$sitio_id]['vistas'] += $result['vistas'];

                                        if (isset($result['clicks']))
                                            $new_resultados[$sitio_id]['clicks'] += $result['clicks'];

                                        if (isset($result['ctr']))
                                            $new_resultados[$sitio_id]['ctr'] = ( $new_resultados[$sitio_id]['clicks'] / $new_resultados[$sitio_id]['impresiones'] ) * 100;

                                        if (isset($result['revenue']))
                                            $new_resultados[$sitio_id]['revenue'] += $result['revenue'];
                                    }else {
                                        $new_resultados[$sitio_id] = $result;
                                    }

                                    $totales['imps'] += $result['impresiones'];
                                    $totales['vistas'] += $result['vistas'];
                                    $totales['clicks'] += $result['clicks'];


                                    if (!isset($result['revenue']))
                                        $result['revenue'] = 0;

                                    $totales['revenue'] += $result['revenue'];
                                }
                            } else if ($dimensiones && in_array('site_name', $dimensiones) && in_array('geo_country', $dimensiones) && !in_array('line_item_name', $dimensiones)) {
                                foreach ($resultados as $result) {
                                    $sitio_id = $result['sitio_id'];
                                    $pais_name = $result['pais_name'];

                                    if (isset($new_resultados_2[$sitio_id][$pais_name])) {

                                        if (isset($result['impresiones']))
                                            $new_resultados_2[$sitio_id][$pais_name]['impresiones'] += $result['impresiones'];

                                        if (isset($result['vistas']))
                                            $new_resultados_2[$sitio_id][$pais_name]['vistas'] += $result['vistas'];

                                        if (isset($result['clicks']))
                                            $new_resultados_2[$sitio_id][$pais_name]['clicks'] += $result['clicks'];

                                        if (isset($result['ctr']))
                                            $new_resultados_2[$sitio_id][$pais_name]['ctr'] = ( $new_resultados_2[$sitio_id][$pais_name]['clicks'] / $new_resultados_2[$sitio_id][$pais_name]['impresiones'] ) * 100;

                                        if (isset($result['revenue']))
                                            $new_resultados_2[$sitio_id][$pais_name]['revenue'] += $result['revenue'];
                                    }else {
                                        $new_resultados_2[$sitio_id][$pais_name] = $result;
                                    }

                                    $totales['imps'] += $result['impresiones'];
                                    $totales['vistas'] += $result['vistas'];
                                    $totales['clicks'] += $result['clicks'];


                                    if (!isset($result['revenue']))
                                        $result['revenue'] = 0;

                                    $totales['revenue'] += $result['revenue'];
                                }

                                foreach ($new_resultados_2 as $value) {
                                    foreach ($value as $value2)
                                        $new_resultados[] = $value2;
                                }
                            } else if ($dimensiones && in_array('site_name', $dimensiones) && !in_array('geo_country', $dimensiones) && in_array('line_item_name', $dimensiones)) {
                                foreach ($resultados as $result) {
                                    $sitio_id = $result['sitio_id'];
                                    $lineItemName = $result['lineItem_name'];
                                    $lineItemID = $result['lineItem_id'];

                                    // reviso si hay (**) o (++)
                                    if (strstr($lineItemName, '(**)') || strstr($lineItemName, '(++)')) {
                                        // si hay (**) o (++) entonces compruebo el texto siguiente del LI y lo asigno al original.
                                        if (strstr($lineItemName, '(**)')) {
                                            $name = explode('(**)', $lineItemName);
                                            $name = substr(strtoupper(trim($name[1])), 0, 4);
                                        }

                                        if (strstr($lineItemName, '(++)')) {
                                            $name = explode('(++)', $lineItemName);
                                            $name = substr(strtoupper(trim($name[1])), 0, 4);
                                        }

                                        $lineItemName = $name;
                                    } else {
                                        $lineItemName = substr(strtoupper($lineItemName), 0, 4);
                                    }

                                    if ($filtros['lineItems'] == 0 || in_array($lineItemID, $filtros['lineItems']) || in_array($lineItemName, $lineItems_Seleccionados)) {

                                        if (isset($new_resultados_2[$sitio_id][$lineItemName])) {

                                            if (isset($result['impresiones']))
                                                $new_resultados_2[$sitio_id][$lineItemName]['impresiones'] += $result['impresiones'];

                                            if (isset($result['vistas']))
                                                $new_resultados_2[$sitio_id][$lineItemName]['vistas'] += $result['vistas'];

                                            if (isset($result['clicks']))
                                                $new_resultados_2[$sitio_id][$lineItemName]['clicks'] += $result['clicks'];

                                            if (isset($result['ctr']))
                                                $new_resultados_2[$sitio_id][$lineItemName]['ctr'] = ( $new_resultados_2[$sitio_id][$lineItemName]['clicks'] / $new_resultados_2[$sitio_id][$lineItemName]['impresiones'] ) * 100;

                                            if (isset($result['revenue']))
                                                $new_resultados_2[$sitio_id][$lineItemName]['revenue'] += $result['revenue'];
                                        }else {
                                            $new_resultados_2[$sitio_id][$lineItemName] = $result;
                                        }

                                        $totales['imps'] += $result['impresiones'];
                                        $totales['vistas'] += $result['vistas'];
                                        $totales['clicks'] += $result['clicks'];


                                        if (!isset($result['revenue']))
                                            $result['revenue'] = 0;

                                        $totales['revenue'] += $result['revenue'];
                                    }
                                }

                                foreach ($new_resultados_2 as $value) {
                                    foreach ($value as $value2)
                                        $new_resultados[] = $value2;
                                }
                            } else if ($dimensiones && in_array('site_name', $dimensiones) && in_array('geo_country', $dimensiones) && in_array('line_item_name', $dimensiones)) {
                                foreach ($resultados as $result) {
                                    $sitio_id = $result['sitio_id'];
                                    $pais_name = $result['pais_name'];
                                    $lineItemName = $result['lineItem_name'];
                                    $lineItemID = $result['lineItem_id'];

                                    // reviso si hay (**) o (++)
                                    if (strstr($lineItemName, '(**)') || strstr($lineItemName, '(++)')) {
                                        // si hay (**) o (++) entonces compruebo el texto siguiente del LI y lo asigno al original.
                                        if (strstr($lineItemName, '(**)')) {
                                            $name = explode('(**)', $lineItemName);
                                            $name = substr(strtoupper(trim($name[1])), 0, 4);
                                        }

                                        if (strstr($lineItemName, '(++)')) {
                                            $name = explode('(++)', $lineItemName);
                                            $name = substr(strtoupper(trim($name[1])), 0, 4);
                                        }

                                        $lineItemName = $name;
                                    } else {
                                        $lineItemName = substr(strtoupper($lineItemName), 0, 4);
                                    }

                                    if ($filtros['lineItems'] == 0 || in_array($lineItemID, $filtros['lineItems']) || in_array($lineItemName, $lineItems_Seleccionados)) {

                                        if (isset($new_resultados_2[$sitio_id][$lineItemName][$pais_name])) {

                                            if (isset($result['impresiones']))
                                                $new_resultados_2[$sitio_id][$lineItemName][$pais_name]['impresiones'] += $result['impresiones'];

                                            if (isset($result['vistas']))
                                                $new_resultados_2[$sitio_id][$lineItemName][$pais_name]['vistas'] += $result['vistas'];

                                            if (isset($result['clicks']))
                                                $new_resultados_2[$sitio_id][$lineItemName][$pais_name]['clicks'] += $result['clicks'];

                                            if (isset($result['ctr']))
                                                $new_resultados_2[$sitio_id][$lineItemName][$pais_name]['ctr'] = ( $new_resultados_2[$sitio_id][$lineItemName][$pais_name]['clicks'] / $new_resultados_2[$sitio_id][$lineItemName][$pais_name]['impresiones'] ) * 100;

                                            if (isset($result['revenue']))
                                                $new_resultados_2[$sitio_id][$lineItemName][$pais_name]['revenue'] += $result['revenue'];
                                        }else {
                                            $new_resultados_2[$sitio_id][$lineItemName][$pais_name] = $result;
                                        }

                                        $totales['imps'] += $result['impresiones'];
                                        $totales['vistas'] += $result['vistas'];
                                        $totales['clicks'] += $result['clicks'];


                                        if (!isset($result['revenue']))
                                            $result['revenue'] = 0;

                                        $totales['revenue'] += $result['revenue'];
                                    }
                                }

                                foreach ($new_resultados_2 as $value) {
                                    foreach ($value as $value2) {
                                        foreach ($value2 as $value3)
                                            $new_resultados[] = $value3;
                                    }
                                }
                            } else if ($dimensiones && !in_array('site_name', $dimensiones) && !in_array('geo_country', $dimensiones) && in_array('line_item_name', $dimensiones)) {
                                foreach ($resultados as $result) {
                                    $sitio_id = $result['sitio_id'];
                                    $lineItemName = $result['lineItem_name'];
                                    $lineItemID = $result['lineItem_id'];

                                    // reviso si hay (**) o (++)
                                    if (strstr($lineItemName, '(**)') || strstr($lineItemName, '(++)')) {
                                        // si hay (**) o (++) entonces compruebo el texto siguiente del LI y lo asigno al original.
                                        if (strstr($lineItemName, '(**)')) {
                                            $name = explode('(**)', $lineItemName);
                                            $name = substr(strtoupper(trim($name[1])), 0, 4);
                                        }

                                        if (strstr($lineItemName, '(++)')) {
                                            $name = explode('(++)', $lineItemName);
                                            $name = substr(strtoupper(trim($name[1])), 0, 4);
                                        }

                                        $lineItemName = $name;
                                    } else {
                                        $lineItemName = substr(strtoupper($lineItemName), 0, 4);
                                    }

                                    if ($filtros['lineItems'] == 0 || in_array($lineItemID, $filtros['lineItems']) || in_array($lineItemName, $lineItems_Seleccionados)) {
                                        if (isset($new_resultados_2[$sitio_id][$lineItemName])) {

                                            if (isset($result['impresiones']))
                                                $new_resultados_2[$sitio_id][$lineItemName]['impresiones'] += $result['impresiones'];

                                            if (isset($result['vistas']))
                                                $new_resultados_2[$sitio_id][$lineItemName]['vistas'] += $result['vistas'];

                                            if (isset($result['clicks']))
                                                $new_resultados_2[$sitio_id][$lineItemName]['clicks'] += $result['clicks'];

                                            if (isset($result['ctr']))
                                                $new_resultados_2[$sitio_id][$lineItemName]['ctr'] = ( $new_resultados_2[$sitio_id][$lineItemName]['clicks'] / $new_resultados_2[$sitio_id][$lineItemName]['impresiones'] ) * 100;

                                            if (isset($result['revenue']))
                                                $new_resultados_2[$sitio_id][$lineItemName]['revenue'] += $result['revenue'];
                                        }else {
                                            $new_resultados_2[$sitio_id][$lineItemName] = $result;
                                        }

                                        $totales['imps'] += $result['impresiones'];
                                        $totales['vistas'] += $result['vistas'];
                                        $totales['clicks'] += $result['clicks'];


                                        if (!isset($result['revenue']))
                                            $result['revenue'] = 0;

                                        $totales['revenue'] += $result['revenue'];
                                    }
                                }

                                foreach ($new_resultados_2 as $value) {
                                    foreach ($value as $value2)
                                        $new_resultados[] = $value2;
                                }
                            } else {

                                foreach ($resultados as $result) {
                                    $totales['imps'] += $result['impresiones'];
                                    $totales['vistas'] += $result['vistas'];
                                    $totales['clicks'] += $result['clicks'];

                                    if (!isset($result['revenue']))
                                        $result['revenue'] = 0;

                                    $totales['revenue'] += $result['revenue'];
                                }

                                $new_resultados = $resultados;
                            }
                        } else if ($intervalo == 'day' && $intervalo != 'month') {
                            if ($dimensiones && in_array('site_name', $dimensiones) && !in_array('geo_country', $dimensiones) && !in_array('line_item_name', $dimensiones)) {
                                foreach ($resultados as $result) {
                                    $sitio_id = $result['sitio_id'];
                                    $fecha = $result['fecha'];

                                    if (isset($new_resultados_2[$sitio_id][$fecha])) {

                                        if (isset($result['impresiones']))
                                            $new_resultados_2[$sitio_id][$fecha]['impresiones'] += $result['impresiones'];

                                        if (isset($result['vistas']))
                                            $new_resultados_2[$sitio_id][$fecha]['vistas'] += $result['vistas'];

                                        if (isset($result['clicks']))
                                            $new_resultados_2[$sitio_id][$fecha]['clicks'] += $result['clicks'];

                                        if (isset($result['ctr']))
                                            $new_resultados_2[$sitio_id][$fecha]['ctr'] = ( $new_resultados_2[$sitio_id][$fecha]['clicks'] / $new_resultados_2[$sitio_id][$fecha]['impresiones'] ) * 100;

                                        if (isset($result['revenue']))
                                            $new_resultados_2[$sitio_id][$fecha]['revenue'] += $result['revenue'];
                                    }else {
                                        $new_resultados_2[$sitio_id][$fecha] = $result;
                                    }

                                    $totales['imps'] += $result['impresiones'];
                                    $totales['vistas'] += $result['vistas'];
                                    $totales['clicks'] += $result['clicks'];

                                    if (!isset($result['revenue']))
                                        $result['revenue'] = 0;

                                    $totales['revenue'] += $result['revenue'];
                                }

                                foreach ($new_resultados_2 as $value) {
                                    foreach ($value as $value2)
                                        $new_resultados[] = $value2;
                                }
                            } else if ($dimensiones && in_array('site_name', $dimensiones) && in_array('geo_country', $dimensiones) && !in_array('line_item_name', $dimensiones)) {
                                foreach ($resultados as $result) {
                                    $sitio_id = $result['sitio_id'];
                                    $pais_name = $result['pais_name'];
                                    $fecha = $result['fecha'];

                                    if (isset($new_resultados_2[$sitio_id][$pais_name][$fecha])) {

                                        if (isset($result['impresiones']))
                                            $new_resultados_2[$sitio_id][$pais_name][$fecha]['impresiones'] += $result['impresiones'];

                                        if (isset($result['vistas']))
                                            $new_resultados_2[$sitio_id][$pais_name][$fecha]['vistas'] += $result['vistas'];

                                        if (isset($result['clicks']))
                                            $new_resultados_2[$sitio_id][$pais_name][$fecha]['clicks'] += $result['clicks'];

                                        if (isset($result['ctr']))
                                            $new_resultados_2[$sitio_id][$pais_name][$fecha]['ctr'] = ( $new_resultados_2[$sitio_id][$pais_name][$fecha]['clicks'] / $new_resultados_2[$sitio_id][$pais_name][$fecha]['impresiones'] ) * 100;

                                        if (isset($result['revenue']))
                                            $new_resultados_2[$sitio_id][$pais_name][$fecha]['revenue'] += $result['revenue'];
                                    }else {
                                        $new_resultados_2[$sitio_id][$pais_name][$fecha] = $result;
                                    }

                                    $totales['imps'] += $result['impresiones'];
                                    $totales['vistas'] += $result['vistas'];
                                    $totales['clicks'] += $result['clicks'];


                                    if (!isset($result['revenue']))
                                        $result['revenue'] = 0;

                                    $totales['revenue'] += $result['revenue'];
                                }

                                foreach ($new_resultados_2 as $value) {
                                    foreach ($value as $value2) {
                                        foreach ($value2 as $value3)
                                            $new_resultados[] = $value3;
                                    }
                                }
                            } else if ($dimensiones && in_array('site_name', $dimensiones) && !in_array('geo_country', $dimensiones) && in_array('line_item_name', $dimensiones)) {
                                foreach ($resultados as $result) {
                                    $sitio_id = $result['sitio_id'];
                                    $lineItemName = $result['lineItem_name'];
                                    $fecha = $result['fecha'];
                                    $lineItemID = $result['lineItem_id'];

                                    // reviso si hay (**) o (++)
                                    if (strstr($lineItemName, '(**)') || strstr($lineItemName, '(++)')) {
                                        // si hay (**) o (++) entonces compruebo el texto siguiente del LI y lo asigno al original.
                                        if (strstr($lineItemName, '(**)')) {
                                            $name = explode('(**)', $lineItemName);
                                            $name = substr(strtoupper(trim($name[1])), 0, 4);
                                        }

                                        if (strstr($lineItemName, '(++)')) {
                                            $name = explode('(++)', $lineItemName);
                                            $name = substr(strtoupper(trim($name[1])), 0, 4);
                                        }

                                        $lineItemName = $name;
                                    } else {
                                        $lineItemName = substr(strtoupper($lineItemName), 0, 4);
                                    }

                                    if ($filtros['lineItems'] == 0 || in_array($lineItemID, $filtros['lineItems']) || in_array($lineItemName, $lineItems_Seleccionados)) {

                                        if (isset($new_resultados_2[$sitio_id][$lineItemName][$fecha])) {

                                            if (isset($result['impresiones']))
                                                $new_resultados_2[$sitio_id][$lineItemName][$fecha]['impresiones'] += $result['impresiones'];

                                            if (isset($result['vistas']))
                                                $new_resultados_2[$sitio_id][$lineItemName][$fecha]['vistas'] += $result['vistas'];

                                            if (isset($result['clicks']))
                                                $new_resultados_2[$sitio_id][$lineItemName][$fecha]['clicks'] += $result['clicks'];

                                            if (isset($result['ctr']))
                                                $new_resultados_2[$sitio_id][$lineItemName][$fecha]['ctr'] = ( $new_resultados_2[$sitio_id][$lineItemName][$fecha]['clicks'] / $new_resultados_2[$sitio_id][$lineItemName][$fecha]['impresiones'] ) * 100;

                                            if (isset($result['revenue']))
                                                $new_resultados_2[$sitio_id][$lineItemName][$fecha]['revenue'] += $result['revenue'];
                                        }else {
                                            $new_resultados_2[$sitio_id][$lineItemName][$fecha] = $result;
                                        }

                                        $totales['imps'] += $result['impresiones'];
                                        $totales['vistas'] += $result['vistas'];
                                        $totales['clicks'] += $result['clicks'];


                                        if (!isset($result['revenue']))
                                            $result['revenue'] = 0;

                                        $totales['revenue'] += $result['revenue'];
                                    }
                                }

                                foreach ($new_resultados_2 as $value) {
                                    foreach ($value as $value2) {
                                        foreach ($value2 as $value3)
                                            $new_resultados[] = $value3;
                                    }
                                }
                            } else if ($dimensiones && in_array('site_name', $dimensiones) && in_array('geo_country', $dimensiones) && in_array('line_item_name', $dimensiones)) {
                                foreach ($resultados as $result) {
                                    $sitio_id = $result['sitio_id'];
                                    $lineItemName = $result['lineItem_name'];
                                    $pais_name = $result['pais_name'];
                                    $fecha = $result['fecha'];
                                    $lineItemID = $result['lineItem_id'];

                                    // reviso si hay (**) o (++)
                                    if (strstr($lineItemName, '(**)') || strstr($lineItemName, '(++)')) {
                                        // si hay (**) o (++) entonces compruebo el texto siguiente del LI y lo asigno al original.
                                        if (strstr($lineItemName, '(**)')) {
                                            $name = explode('(**)', $lineItemName);
                                            $name = substr(strtoupper(trim($name[1])), 0, 4);
                                        }

                                        if (strstr($lineItemName, '(++)')) {
                                            $name = explode('(++)', $lineItemName);
                                            $name = substr(strtoupper(trim($name[1])), 0, 4);
                                        }

                                        $lineItemName = $name;
                                    } else {
                                        $lineItemName = substr(strtoupper($lineItemName), 0, 4);
                                    }

                                    if ($filtros['lineItems'] == 0 || in_array($lineItemID, $filtros['lineItems']) || in_array($lineItemName, $lineItems_Seleccionados)) {

                                        if (isset($new_resultados_2[$sitio_id][$lineItemName][$pais_name][$fecha])) {

                                            if (isset($result['impresiones']))
                                                $new_resultados_2[$sitio_id][$lineItemName][$pais_name][$fecha]['impresiones'] += $result['impresiones'];

                                            if (isset($result['vistas']))
                                                $new_resultados_2[$sitio_id][$lineItemName][$pais_name][$fecha]['vistas'] += $result['vistas'];

                                            if (isset($result['clicks']))
                                                $new_resultados_2[$sitio_id][$lineItemName][$pais_name][$fecha]['clicks'] += $result['clicks'];

                                            if (isset($result['ctr']))
                                                $new_resultados_2[$sitio_id][$lineItemName][$pais_name][$fecha]['ctr'] = ( $new_resultados_2[$sitio_id][$lineItemName][$pais_name][$fecha]['clicks'] / $new_resultados_2[$sitio_id][$lineItemName][$pais_name][$fecha]['impresiones'] ) * 100;

                                            if (isset($result['revenue']))
                                                $new_resultados_2[$sitio_id][$lineItemName][$pais_name][$fecha]['revenue'] += $result['revenue'];
                                        }else {
                                            $new_resultados_2[$sitio_id][$lineItemName][$pais_name][$fecha] = $result;
                                        }

                                        $totales['imps'] += $result['impresiones'];
                                        $totales['vistas'] += $result['vistas'];
                                        $totales['clicks'] += $result['clicks'];


                                        if (!isset($result['revenue']))
                                            $result['revenue'] = 0;

                                        $totales['revenue'] += $result['revenue'];
                                    }
                                }

                                foreach ($new_resultados_2 as $value) {
                                    foreach ($value as $value2) {
                                        foreach ($value2 as $value3) {
                                            foreach ($value3 as $value4)
                                                $new_resultados[] = $value4;
                                        }
                                    }
                                }
                            } else if ($dimensiones && !in_array('site_name', $dimensiones) && !in_array('geo_country', $dimensiones) && in_array('line_item_name', $dimensiones)) {
                                foreach ($resultados as $result) {
                                    $sitio_id = $result['sitio_id'];
                                    $lineItemName = $result['lineItem_name'];
                                    $fecha = $result['fecha'];
                                    $lineItemID = $result['lineItem_id'];

                                    // reviso si hay (**) o (++)
                                    if (strstr($lineItemName, '(**)') || strstr($lineItemName, '(++)')) {
                                        // si hay (**) o (++) entonces compruebo el texto siguiente del LI y lo asigno al original.
                                        if (strstr($lineItemName, '(**)')) {
                                            $name = explode('(**)', $lineItemName);
                                            $name = substr(strtoupper(trim($name[1])), 0, 4);
                                        }

                                        if (strstr($lineItemName, '(++)')) {
                                            $name = explode('(++)', $lineItemName);
                                            $name = substr(strtoupper(trim($name[1])), 0, 4);
                                        }

                                        $lineItemName = $name;
                                    } else {
                                        $lineItemName = substr(strtoupper($lineItemName), 0, 4);
                                    }

                                    if ($filtros['lineItems'] == 0 || in_array($lineItemID, $filtros['lineItems']) || in_array($lineItemName, $lineItems_Seleccionados)) {

                                        if (isset($new_resultados_2[$sitio_id][$lineItemName][$fecha])) {

                                            if (isset($result['impresiones']))
                                                $new_resultados_2[$sitio_id][$lineItemName][$fecha]['impresiones'] += $result['impresiones'];

                                            if (isset($result['vistas']))
                                                $new_resultados_2[$sitio_id][$lineItemName][$fecha]['vistas'] += $result['vistas'];

                                            if (isset($result['clicks']))
                                                $new_resultados_2[$sitio_id][$lineItemName][$fecha]['clicks'] += $result['clicks'];

                                            if (isset($result['ctr']))
                                                $new_resultados_2[$sitio_id][$lineItemName][$fecha]['ctr'] = ( $new_resultados_2[$sitio_id][$lineItemName][$fecha]['clicks'] / $new_resultados_2[$sitio_id][$lineItemName][$fecha]['impresiones'] ) * 100;

                                            if (isset($result['revenue']))
                                                $new_resultados_2[$sitio_id][$lineItemName][$fecha]['revenue'] += $result['revenue'];
                                        }else {
                                            $new_resultados_2[$sitio_id][$lineItemName][$fecha] = $result;
                                        }

                                        $totales['imps'] += $result['impresiones'];
                                        $totales['vistas'] += $result['vistas'];
                                        $totales['clicks'] += $result['clicks'];


                                        if (!isset($result['revenue']))
                                            $result['revenue'] = 0;

                                        $totales['revenue'] += $result['revenue'];
                                    }
                                }

                                foreach ($new_resultados_2 as $value) {
                                    foreach ($value as $value2) {
                                        foreach ($value2 as $value3)
                                            $new_resultados[] = $value3;
                                    }
                                }
                            } else {
                                foreach ($resultados as $result) {
                                    if (!isset($result['impresiones']))
                                        $result['impresiones'] = 0;

                                    if (!isset($result['vistas']))
                                        $result['vistas'] = 0;

                                    if (!isset($result['clicks']))
                                        $result['clicks'] = 0;

                                    if (!isset($result['revenue']))
                                        $result['revenue'] = 0;

                                    $totales['imps'] += $result['impresiones'];
                                    $totales['vistas'] += $result['vistas'];
                                    $totales['clicks'] += $result['clicks'];
                                    $totales['revenue'] += $result['revenue'];
                                }

                                $new_resultados = $resultados;
                            }
                        } else if ($intervalo !== 'day' && $intervalo == 'month') {
                            if ($dimensiones && in_array('site_name', $dimensiones) && !in_array('geo_country', $dimensiones) && !in_array('line_item_name', $dimensiones)) {
                                foreach ($resultados as $result) {
                                    $sitio_id = $result['sitio_id'];
                                    $fecha = $result['mes'];

                                    if (isset($new_resultados_2[$sitio_id][$fecha])) {

                                        if (isset($result['impresiones']))
                                            $new_resultados_2[$sitio_id][$fecha]['impresiones'] += $result['impresiones'];

                                        if (isset($result['vistas']))
                                            $new_resultados_2[$sitio_id][$fecha]['vistas'] += $result['vistas'];

                                        if (isset($result['clicks']))
                                            $new_resultados_2[$sitio_id][$fecha]['clicks'] += $result['clicks'];

                                        if (isset($result['ctr']))
                                            $new_resultados_2[$sitio_id][$fecha]['ctr'] = ( $new_resultados_2[$sitio_id][$fecha]['clicks'] / $new_resultados_2[$sitio_id][$fecha]['impresiones'] ) * 100;

                                        if (isset($result['revenue']))
                                            $new_resultados_2[$sitio_id][$fecha]['revenue'] += $result['revenue'];
                                    }else {
                                        $new_resultados_2[$sitio_id][$fecha] = $result;
                                    }

                                    $totales['imps'] += $result['impresiones'];
                                    $totales['vistas'] += $result['vistas'];
                                    $totales['clicks'] += $result['clicks'];


                                    if (!isset($result['revenue']))
                                        $result['revenue'] = 0;

                                    $totales['revenue'] += $result['revenue'];
                                }

                                foreach ($new_resultados_2 as $value) {
                                    foreach ($value as $value2)
                                        $new_resultados[] = $value2;
                                }
                            } else if ($dimensiones && in_array('site_name', $dimensiones) && in_array('geo_country', $dimensiones) && !in_array('line_item_name', $dimensiones)) {
                                foreach ($resultados as $result) {
                                    $sitio_id = $result['sitio_id'];
                                    $pais_name = $result['pais_name'];
                                    $fecha = $result['mes'];

                                    if (isset($new_resultados_2[$sitio_id][$pais_name][$fecha])) {

                                        if (isset($result['impresiones']))
                                            $new_resultados_2[$sitio_id][$pais_name][$fecha]['impresiones'] += $result['impresiones'];

                                        if (isset($result['vistas']))
                                            $new_resultados_2[$sitio_id][$pais_name][$fecha]['vistas'] += $result['vistas'];

                                        if (isset($result['clicks']))
                                            $new_resultados_2[$sitio_id][$pais_name][$fecha]['clicks'] += $result['clicks'];

                                        if (isset($result['ctr']))
                                            $new_resultados_2[$sitio_id][$pais_name][$fecha]['ctr'] = ( $new_resultados_2[$sitio_id][$pais_name][$fecha]['clicks'] / $new_resultados_2[$sitio_id][$pais_name][$fecha]['impresiones'] ) * 100;

                                        if (isset($result['revenue']))
                                            $new_resultados_2[$sitio_id][$pais_name][$fecha]['revenue'] += $result['revenue'];
                                    }else {
                                        $new_resultados_2[$sitio_id][$pais_name][$fecha] = $result;
                                    }

                                    $totales['imps'] += $result['impresiones'];
                                    $totales['vistas'] += $result['vistas'];
                                    $totales['clicks'] += $result['clicks'];


                                    if (!isset($result['revenue']))
                                        $result['revenue'] = 0;

                                    $totales['revenue'] += $result['revenue'];
                                }

                                foreach ($new_resultados_2 as $value) {
                                    foreach ($value as $value2) {
                                        foreach ($value2 as $value3)
                                            $new_resultados[] = $value3;
                                    }
                                }
                            } else if ($dimensiones && in_array('site_name', $dimensiones) && !in_array('geo_country', $dimensiones) && in_array('line_item_name', $dimensiones)) {
                                foreach ($resultados as $result) {
                                    $sitio_id = $result['sitio_id'];
                                    $lineItemName = $result['lineItem_name'];
                                    $fecha = $result['mes'];
                                    $lineItemID = $result['lineItem_id'];

                                    // reviso si hay (**) o (++)
                                    if (strstr($lineItemName, '(**)') || strstr($lineItemName, '(++)')) {
                                        // si hay (**) o (++) entonces compruebo el texto siguiente del LI y lo asigno al original.
                                        if (strstr($lineItemName, '(**)')) {
                                            $name = explode('(**)', $lineItemName);
                                            $name = substr(strtoupper(trim($name[1])), 0, 4);
                                        }

                                        if (strstr($lineItemName, '(++)')) {
                                            $name = explode('(++)', $lineItemName);
                                            $name = substr(strtoupper(trim($name[1])), 0, 4);
                                        }

                                        $lineItemName = $name;
                                    } else {
                                        $lineItemName = substr(strtoupper($lineItemName), 0, 4);
                                    }

                                    if ($filtros['lineItems'] == 0 || in_array($lineItemID, $filtros['lineItems']) || in_array($lineItemName, $lineItems_Seleccionados)) {

                                        if (isset($new_resultados_2[$sitio_id][$lineItemName][$fecha])) {

                                            if (isset($result['impresiones']))
                                                $new_resultados_2[$sitio_id][$lineItemName][$fecha]['impresiones'] += $result['impresiones'];

                                            if (isset($result['vistas']))
                                                $new_resultados_2[$sitio_id][$lineItemName][$fecha]['vistas'] += $result['vistas'];

                                            if (isset($result['clicks']))
                                                $new_resultados_2[$sitio_id][$lineItemName][$fecha]['clicks'] += $result['clicks'];

                                            if (isset($result['ctr']))
                                                $new_resultados_2[$sitio_id][$lineItemName][$fecha]['ctr'] = ( $new_resultados_2[$sitio_id][$lineItemName][$fecha]['clicks'] / $new_resultados_2[$sitio_id][$lineItemName][$fecha]['impresiones'] ) * 100;

                                            if (isset($result['revenue']))
                                                $new_resultados_2[$sitio_id][$lineItemName][$fecha]['revenue'] += $result['revenue'];
                                        }else {
                                            $new_resultados_2[$sitio_id][$lineItemName][$fecha] = $result;
                                        }

                                        $totales['imps'] += $result['impresiones'];
                                        $totales['vistas'] += $result['vistas'];
                                        $totales['clicks'] += $result['clicks'];


                                        if (!isset($result['revenue']))
                                            $result['revenue'] = 0;

                                        $totales['revenue'] += $result['revenue'];
                                    }
                                }

                                foreach ($new_resultados_2 as $value) {
                                    foreach ($value as $value2) {
                                        foreach ($value2 as $value3)
                                            $new_resultados[] = $value3;
                                    }
                                }
                            } else if ($dimensiones && in_array('site_name', $dimensiones) && in_array('geo_country', $dimensiones) && in_array('line_item_name', $dimensiones)) {
                                foreach ($resultados as $result) {
                                    $sitio_id = $result['sitio_id'];
                                    $lineItemName = $result['lineItem_name'];
                                    $pais_name = $result['pais_name'];
                                    $fecha = $result['mes'];
                                    $lineItemID = $result['lineItem_id'];

                                    // reviso si hay (**) o (++)
                                    if (strstr($lineItemName, '(**)') || strstr($lineItemName, '(++)')) {
                                        // si hay (**) o (++) entonces compruebo el texto siguiente del LI y lo asigno al original.
                                        if (strstr($lineItemName, '(**)')) {
                                            $name = explode('(**)', $lineItemName);
                                            $name = substr(strtoupper(trim($name[1])), 0, 4);
                                        }

                                        if (strstr($lineItemName, '(++)')) {
                                            $name = explode('(++)', $lineItemName);
                                            $name = substr(strtoupper(trim($name[1])), 0, 4);
                                        }

                                        $lineItemName = $name;
                                    } else {
                                        $lineItemName = substr(strtoupper($lineItemName), 0, 4);
                                    }

                                    if ($filtros['lineItems'] == 0 || in_array($lineItemID, $filtros['lineItems']) || in_array($lineItemName, $lineItems_Seleccionados)) {

                                        if (isset($new_resultados_2[$sitio_id][$lineItemName][$pais_name][$fecha])) {

                                            if (isset($result['impresiones']))
                                                $new_resultados_2[$sitio_id][$lineItemName][$pais_name][$fecha]['impresiones'] += $result['impresiones'];

                                            if (isset($result['vistas']))
                                                $new_resultados_2[$sitio_id][$lineItemName][$pais_name][$fecha]['vistas'] += $result['vistas'];

                                            if (isset($result['clicks']))
                                                $new_resultados_2[$sitio_id][$lineItemName][$pais_name][$fecha]['clicks'] += $result['clicks'];

                                            if (isset($result['ctr']))
                                                $new_resultados_2[$sitio_id][$lineItemName][$pais_name][$fecha]['ctr'] = ( $new_resultados_2[$sitio_id][$lineItemName][$pais_name][$fecha]['clicks'] / $new_resultados_2[$sitio_id][$lineItemName][$pais_name][$fecha]['impresiones'] ) * 100;

                                            if (isset($result['revenue']))
                                                $new_resultados_2[$sitio_id][$lineItemName][$pais_name][$fecha]['revenue'] += $result['revenue'];
                                        }else {
                                            $new_resultados_2[$sitio_id][$lineItemName][$pais_name][$fecha] = $result;
                                        }

                                        $totales['imps'] += $result['impresiones'];
                                        $totales['vistas'] += $result['vistas'];
                                        $totales['clicks'] += $result['clicks'];


                                        if (!isset($result['revenue']))
                                            $result['revenue'] = 0;

                                        $totales['revenue'] += $result['revenue'];
                                    }
                                }

                                foreach ($new_resultados_2 as $value) {
                                    foreach ($value as $value2) {
                                        foreach ($value2 as $value3) {
                                            foreach ($value3 as $value4)
                                                $new_resultados[] = $value4;
                                        }
                                    }
                                }
                            } else if ($dimensiones && !in_array('site_name', $dimensiones) && !in_array('geo_country', $dimensiones) && in_array('line_item_name', $dimensiones)) {
                                foreach ($resultados as $result) {
                                    $sitio_id = $result['sitio_id'];
                                    $lineItemName = $result['lineItem_name'];
                                    $fecha = $result['mes'];
                                    $lineItemID = $result['lineItem_id'];

                                    // reviso si hay (**) o (++)
                                    if (strstr($lineItemName, '(**)') || strstr($lineItemName, '(++)')) {
                                        // si hay (**) o (++) entonces compruebo el texto siguiente del LI y lo asigno al original.
                                        if (strstr($lineItemName, '(**)')) {
                                            $name = explode('(**)', $lineItemName);
                                            $name = substr(strtoupper(trim($name[1])), 0, 4);
                                        }

                                        if (strstr($lineItemName, '(++)')) {
                                            $name = explode('(++)', $lineItemName);
                                            $name = substr(strtoupper(trim($name[1])), 0, 4);
                                        }

                                        $lineItemName = $name;
                                    } else {
                                        $lineItemName = substr(strtoupper($lineItemName), 0, 4);
                                    }

                                    if ($filtros['lineItems'] == 0 || in_array($lineItemID, $filtros['lineItems']) || in_array($lineItemName, $lineItems_Seleccionados)) {
                                        if (isset($new_resultados_2[$sitio_id][$lineItemName][$fecha])) {

                                            if (isset($result['impresiones']))
                                                $new_resultados_2[$sitio_id][$lineItemName][$fecha]['impresiones'] += $result['impresiones'];

                                            if (isset($result['vistas']))
                                                $new_resultados_2[$sitio_id][$lineItemName][$fecha]['vistas'] += $result['vistas'];

                                            if (isset($result['clicks']))
                                                $new_resultados_2[$sitio_id][$lineItemName][$fecha]['clicks'] += $result['clicks'];

                                            if (isset($result['ctr']))
                                                $new_resultados_2[$sitio_id][$lineItemName][$fecha]['ctr'] = ( $new_resultados_2[$sitio_id][$lineItemName][$fecha]['clicks'] / $new_resultados_2[$sitio_id][$lineItemName][$fecha]['impresiones'] ) * 100;

                                            if (isset($result['revenue']))
                                                $new_resultados_2[$sitio_id][$lineItemName][$fecha]['revenue'] += $result['revenue'];
                                        } else {
                                            $new_resultados_2[$sitio_id][$lineItemName][$fecha] = $result;
                                        }

                                        $totales['imps'] += $result['impresiones'];
                                        $totales['vistas'] += $result['vistas'];
                                        $totales['clicks'] += $result['clicks'];


                                        if (!isset($result['revenue']))
                                            $result['revenue'] = 0;

                                        $totales['revenue'] += $result['revenue'];
                                    }
                                }

                                foreach ($new_resultados_2 as $value) {
                                    foreach ($value as $value2) {
                                        foreach ($value2 as $value3)
                                            $new_resultados[] = $value3;
                                    }
                                }
                            } else {
                                foreach ($resultados as $result) {
                                    $totales['imps'] += $result['impresiones'];
                                    $totales['vistas'] += $result['vistas'];
                                    $totales['clicks'] += $result['clicks'];


                                    if (!isset($result['revenue']))
                                        $result['revenue'] = 0;

                                    $totales['revenue'] += $result['revenue'];
                                }

                                $new_resultados = $resultados;
                            }
                        } else {
                            $new_resultados = $resultados;
                        }
                    }
                }

                if (in_array('site_name', $dimensiones)) {
                    // filtro todos los sitios que no se pueden mostrar y reparto impresiones
                    unset($resultados);
                    $total_imps_excluidos = $total_views_excluidos = $total_clicks_excluidos = $total_ctr_excluidos = $total_revenue_excluidos = 0;
                    $total_imps_no_excluidos = $total_views_no_excluidos = $total_clicks_no_excluidos = $total_ctr_no_excluidos = $total_revenue_no_excluidos = 0;

                    foreach ($new_resultados as $resultados) {
                        $encontrado = false;
                        foreach ($sitios as $sitios_db) {
                            if ($resultados['sitio_id'] == $sitios_db->id_adunit_site) {
                                $encontrado = true;
                                break;
                            }
                        }

                        if ($encontrado) {
                            // si el sitio corre en las categorias activadas y no excluidas
                            $sitios_no_excluidos[] = $resultados;

                            if (isset($resultados['impresiones']))
                                $total_imps_no_excluidos += $resultados['impresiones'];

                            if (isset($resultados['vistas']))
                                $total_views_no_excluidos += $resultados['vistas'];

                            if (isset($resultados['clicks']))
                                $total_clicks_no_excluidos += $resultados['clicks'];

                            if (isset($resultados['ctr']))
                                $total_ctr_no_excluidos = ($total_clicks_no_excluidos / $total_imps_no_excluidos) * 100;

                            if (isset($resultados['revenue']))
                                $total_revenue_no_excluidos += $resultados['revenue'];
                        }else {
                            // si no corre entonces excluir para repartir impresiones
                            $sitios_a_excluir[] = $resultados;

                            if (isset($resultados['impresiones']))
                                $total_imps_excluidos += $resultados['impresiones'];

                            if (isset($resultados['vistas']))
                                $total_views_excluidos += $resultados['vistas'];

                            if (isset($resultados['clicks']))
                                $total_clicks_excluidos += $resultados['clicks'];

                            if (isset($resultados['ctr']))
                                $total_ctr_excluidos = ($total_clicks_excluidos / $total_imps_excluidos) * 100;

                            if (isset($resultados['revenue']))
                                $total_revenue_excluidos += $resultados['revenue'];
                        }
                    }
                    $total_clicks_provisorios = 0;
                    $total_imps_provisorias = 0;
                    $total_views_provisorias = 0;

                    if (isset($sitios_no_excluidos)) {
                        foreach ($sitios_no_excluidos as $value) {
                            if (isset($value['impresiones']) && $value['impresiones'] > 0) {
                                $porcentaje = (($value['impresiones'] * 100) / $total_imps_no_excluidos);
                                $value['impresiones'] = round($value['impresiones'] + ($porcentaje * $total_imps_excluidos) / 100);
                                $total_imps_provisorias+=$value['impresiones'];
                            }

                            if (isset($value['vistas']) && $value['vistas'] > 0) {
                                $porcentaje = (($value['vistas'] * 100) / $total_views_no_excluidos);
                                $value['vistas'] = round($value['vistas'] + ($porcentaje * $total_views_excluidos) / 100);
                                $total_views_provisorias+=$value['vistas'];
                            }

                            if (isset($value['clicks']) && $value['clicks'] > 0) {
                                $porcentaje = (($value['clicks'] * 100) / $total_clicks_no_excluidos);
                                $value['clicks'] = round($value['clicks'] + ($porcentaje * $total_clicks_excluidos) / 100);
                                $total_clicks_provisorios+=$value['clicks'];
                            }

                            if (isset($value['ctr']))
                                $value['ctr'] = ($value['clicks'] / $value['impresiones']) * 100;

                            if (isset($value['revenue']) && $value['revenue'] > 0) {
                                $porcentaje = (($value['revenue'] * 100) / $total_revenue_no_excluidos);
                                $value['revenue'] = $value['revenue'] + ($porcentaje * $total_revenue_excluidos) / 100;
                            }

                            $new_array[] = $value;
                        }

                        $new_resultados = $new_array;
                    }

                    $new_resultados[0]['impresiones'] += ((int) $totales['imps'] - (int) $total_imps_provisorias);
                    $new_resultados[0]['vistas'] += ((int) $totales['vistas'] - (int) $total_views_provisorias);
                    $new_resultados[0]['clicks'] += ((int) $totales['clicks'] - (int) $total_clicks_provisorios);
                    $new_resultados[0]['ctr'] += ($new_resultados[0]['clicks'] / $new_resultados[0]['impresiones']) * 100;
                }

                if (isset($new_resultados)) {
                    unset($result, $value);
                    foreach ($new_resultados as $key => $value)
                        $new_resultados[$key]['revenue'] = $this->cambiar_moneda($new_resultados[$key]['revenue']);
                }

                if ($totales['imps'] && $totales['clicks'])
                    $totales['ctr'] = ($totales['clicks'] / $totales['imps']) * 100;

                if (!isset($new_resultados))
                    $new_resultados = null;

                return array($new_resultados, $encabezados, $dimensiones, $columnas, $totales);
            }
        } catch (Exception $ex) {
            //echo $ex->getMessage();
            return false;
        }
    }

    public function getCreativesByAdv($token, $adserver, $anunciante_id) {
        try {
            if ($adserver == '1') {
                $request = new Request();
                $request->method = 'get';
                $request->token = $token;
                $request->uri = BASE_URI . '/creative?advertiser_id=' . $anunciante_id;
                $res = Caller::call($request);

                while ($res == "RATE_EXCEEDED") {
                    $res = Caller::call($request);
                }

                if (!isset($res->response->creatives)) {
                    return false;
                }

                return $res->response->creatives;
            } else if ($adserver == '0') {

                $user = $this->getDFPUser();
                $creativeService = $user->GetService('CreativeService', 'v201208');
                $vars = MapUtils::GetMapEntries(array('advertiserId' => new TextValue($anunciante_id)));
                $filterStatement = new Statement("WHERE advertiserId = :advertiserId LIMIT 500", $vars);
                $page = $creativeService->getCreativesByStatement($filterStatement);

                return $page->results;
            }
        } catch (Exception $ex) {
            return false;
        }
    }

    private function filtrarSitiosParaReporte($filtro_categorias, $filtro_paises, $intervalo, $fecha_desde, $fecha_hasta) {
        $sitios = $categorias = null;

        // intervalo de fechas seleccionado.
        $fechas = $this->_fechasPorIntervalo($intervalo, $fecha_desde, $fecha_hasta);
        if ($fechas['desde'] == false || $fechas['hasta'] == false)
            return FALSE;

        // remplazo la 'o' por ',' para realizar consulta de sitios por categoria
        $filtro_categorias = str_replace('o', ',', trim($filtro_categorias, 'o'));

        // traigo todos los sitios segun el intervalo de categorias seleccionadas (son las categorias no excluidas).
        $sitios = $this->ci->sitescategories->get_sites_in_categories($filtro_categorias);

        // filtro los paises
        if ($filtro_paises == '') {
            $filtro_paises = $this->ci->paises->get_paises();
            foreach ($filtro_paises as $pais)
                $paises[$pais->id] = $pais->id;
            //$paises[$pais->id_dfp] = $pais->id_dfp;
        } else {
            $filtro_paises = explode('o', trim($filtro_paises, 'o'));
            foreach ($filtro_paises as $pais) {
                $pais = $this->ci->paises->get_pais_by_id($pais);
                $paises[$pais->id] = $pais->id;
                //$paises[$pais->id_dfp] = $pais->id_dfp;
            }
        }

        if ($sitios && $paises)
            return array('fecha_desde' => $fechas['desde'], 'fecha_hasta' => $fechas['hasta'], 'sitios' => $sitios, 'paises' => $paises);

        return FALSE;
    }

    private function reemplazarNombresGlam($id_site) {
        if ($id_site == 1524) {
            return 'blacklife.glam.com';
        } else if ($id_site == 1525) {
            return 'www.bliss.com';
        } else if ($id_site == 1526) {
            return 'www.brash.com';
        } else if ($id_site == 1527) {
            return 'entertainment.glam.com';
        } else if ($id_site == 1528) {
            return 'www.glam.com/tag/family-moms';
        } else if ($id_site == 1529) {
            return 'recipes.foodie.com';
        } else if ($id_site == 1530) {
            return 'www.tend.com';
        } else if ($id_site == 1531) {
            return 'living.glam.com';
        } else if ($id_site == 1532) {
            return 'http://www.glammedia.com/';
        } else if ($id_site == 1533) {
            return 'uk.rottentomatoes.com';
        } else if ($id_site == 1534) {
            return 'www.stripesandsequins.com';
        } else if ($id_site == 1535) {
            return 'www.glam.com';
        } else if ($id_site == 1536) {
            return 'www.taaz.com';
        } else if ($id_site == 1537) {
            return 'www.windows2universe.org';
        } else if ($id_site == 1538) {
            return 'theybf.com';
        }

        return FALSE;
    }

    public function reportePorSitio($filtro_categorias, $filtro_paises, $intervalo, $fecha_desde, $fecha_hasta) {
        $filtros = $this->filtrarSitiosParaReporte($filtro_categorias, $filtro_paises, $intervalo, $fecha_desde, $fecha_hasta);

        if (!$filtros)
            return FALSE;

        $reporte = $this->ci->inventario_anunciantes->get_inventario_appnexus($filtros['fecha_desde'], $filtros['fecha_hasta'], 'adUnit_ID_2');

        $totales['imps'] = 0;

        foreach ($reporte as $report) {
            if (isset($filtros['paises'][$report->geo_country_id])) {
                if (isset($new_reporte[$report->site_id])) {
                    $new_reporte[$report->site_id]['imps'] += $report->imps_total * $this->multiplicacion_volumen;
                } else {
                    $new_reporte[$report->site_id]['imps'] = $report->imps_total * $this->multiplicacion_volumen;
                }
            }
        }

        foreach ($filtros['sitios'] as $sitio) {
            if (isset($new_reporte[$sitio->id_site])) {
                $sitios_filtrados[$sitio->id]['id'] = $sitio->id_site;

                $sitios_filtrados[$sitio->id]['url_sitio'] = $sitio->nombre_appnexus;
                if ($sitios_filtrados[$sitio->id]['url_sitio'] == '')
                    $sitios_filtrados[$sitio->id]['url_sitio'] = $sitio->nombre_dfp;

                // Personalizo los nombres para GLAM
                $nombre_glam = $this->reemplazarNombresGlam($sitio->id);
                if ($nombre_glam)
                    $sitios_filtrados[$sitio->id]['url_sitio'] = $nombre_glam;

                $sitios_filtrados[$sitio->id]['url_sitio'] = str_replace("http://", "", $sitios_filtrados[$sitio->id]['url_sitio']);
                $sitios_filtrados[$sitio->id]['url_sitio'] = str_replace("/", "", $sitios_filtrados[$sitio->id]['url_sitio']);

                $sitios_filtrados[$sitio->id]['categorias'] = $this->_categoriasPorSitio($sitio->id);
                if ($sitios_filtrados[$sitio->id]['categorias'] == '')
                    $sitios_filtrados[$sitio->id]['categorias'] = '-';

                $totales['imps'] += $new_reporte[$sitio->id_site]['imps'];

                if ($this->user_notacion == 0) {
                    $sitios_filtrados[$sitio->id]['imps'] = number_format($new_reporte[$sitio->id_site]['imps'], 0, '.', ',');
                } else if ($this->user_notacion == 1) {
                    $sitios_filtrados[$sitio->id]['imps'] = number_format($new_reporte[$sitio->id_site]['imps'], 0, ',', '.');
                }
            }
        }

        if (!isset($sitios_filtrados))
            return FALSE;

        if ($this->user_notacion == 0) {
            $totales['imps'] = number_format($totales['imps'], 0, '.', ',');
        } else if ($this->user_notacion == 1) {
            $totales['imps'] = number_format($totales['imps'], 0, ',', '.');
        }

        $data['totales'] = $totales;
        $data['sitios'] = $sitios_filtrados;

        return $data;

        /*
          $filtros = $this->filtrarSitiosParaReporte($filtro_categorias, $filtro_paises, $intervalo, $fecha_desde, $fecha_hasta);

          if (!$filtros)
          return FALSE;

          $reporte = $this->ci->inventario_anunciantes->get_inventario($filtros['fecha_desde'], $filtros['fecha_hasta'], 'adUnit_ID_2');

          $totales['imps'] = 0;

          foreach ($reporte as $report) {
          if (isset($filtros['paises'][$report->countryAd_ID])) {
          if (isset($new_reporte[$report->adUnit_ID_2])) {
          $new_reporte[$report->adUnit_ID_2]['imps'] += $report->totalImpressions * $this->multiplicacion_volumen;
          } else {
          $new_reporte[$report->adUnit_ID_2]['imps'] = $report->totalImpressions * $this->multiplicacion_volumen;
          }
          }
          }

          foreach ($filtros['sitios'] as $sitio) {
          if (isset($new_reporte[$sitio->id_adunit_site])) {
          $sitios_filtrados[$sitio->id]['id'] = $sitio->id_adunit_site;

          $sitios_filtrados[$sitio->id]['url_sitio'] = $sitio->nombre_appnexus;
          if ($sitios_filtrados[$sitio->id]['url_sitio'] == '')
          $sitios_filtrados[$sitio->id]['url_sitio'] = $sitio->nombre_dfp;

          // Personalizo los nombres para GLAM
          $nombre_glam = $this->reemplazarNombresGlam($sitio->id);
          if ($nombre_glam)
          $sitios_filtrados[$sitio->id]['url_sitio'] = $nombre_glam;

          $sitios_filtrados[$sitio->id]['url_sitio'] = str_replace("http://", "", $sitios_filtrados[$sitio->id]['url_sitio']);
          $sitios_filtrados[$sitio->id]['url_sitio'] = str_replace("/", "", $sitios_filtrados[$sitio->id]['url_sitio']);

          $sitios_filtrados[$sitio->id]['categorias'] = $this->_categoriasPorSitio($sitio->id);
          if ($sitios_filtrados[$sitio->id]['categorias'] == '')
          $sitios_filtrados[$sitio->id]['categorias'] = '-';

          $totales['imps'] += $new_reporte[$sitio->id_adunit_site]['imps'];

          if ($this->user_notacion == 0) {
          $sitios_filtrados[$sitio->id]['imps'] = number_format($new_reporte[$sitio->id_adunit_site]['imps'], 0, '.', ',');
          } else if ($this->user_notacion == 1) {
          $sitios_filtrados[$sitio->id]['imps'] = number_format($new_reporte[$sitio->id_adunit_site]['imps'], 0, ',', '.');
          }
          }
          }

          if (!isset($sitios_filtrados))
          return FALSE;

          if ($this->user_notacion == 0) {
          $totales['imps'] = number_format($totales['imps'], 0, '.', ',');
          } else if ($this->user_notacion == 1) {
          $totales['imps'] = number_format($totales['imps'], 0, ',', '.');
          }

          $data['totales'] = $totales;
          $data['sitios'] = $sitios_filtrados;

          return $data;
         *
         */
    }

    public function reportePorCategoria($token, $filtro_categorias, $filtro_paises, $intervalo, $fecha_desde, $fecha_hasta) {
        $filtros = $this->filtrarSitiosParaReporte($filtro_categorias, $filtro_paises, $intervalo, $fecha_desde, $fecha_hasta);

        if (!$filtros)
            return FALSE;

        $reporte = $this->ci->inventario_anunciantes->get_inventario_appnexus($filtros['fecha_desde'], $filtros['fecha_hasta'], 'adUnit_ID_2');

        foreach ($reporte as $report) {
            if (isset($filtros['paises'][$report->geo_country_id])) {
                if (isset($new_reporte[$report->site_id])) {
                    $new_reporte[$report->site_id]['imps'] += $report->imps_total * $this->multiplicacion_volumen;
                } else {
                    $new_reporte[$report->site_id]['imps'] = $report->imps_total * $this->multiplicacion_volumen;
                }
            }
        }

        $categorias = null;

        if ($filtro_categorias == '0') {
            $filtro_categorias = $this->ci->categorias->get_categorias();
            foreach ($filtro_categorias as $cat) {
                $categorias[] = array($cat->id, $cat->nombre);
            }
        } else {
            $filtro_categorias = explode('o', trim($filtro_categorias, 'o'));
            foreach ($filtro_categorias as $cat) {
                $categoria = $this->ci->categorias->get_categoria_by_id($cat);
                $categorias[] = array($categoria->id, $categoria->nombre);
            }
        }

        $totales['imps'] = '-';

        unset($categoria);

        // recorro una por una las categorias y voy sumando las impresiones
        foreach ($categorias as $cat) {
            $categoria['nombre'] = $cat[1];
            $categoria['imps'] = 0;

            foreach ($filtros['sitios'] as $sitio) {
                if (isset($new_reporte[$sitio->id_site])) {
                    $corre = FALSE;

                    if ($this->ci->sitescategories->get_cat_by_site($sitio->id, $cat[0]))
                        $corre = TRUE;

                    if ($corre)
                        $categoria['imps'] += $new_reporte[$sitio->id_site]['imps'];
                }
            }

            if ($this->user_notacion == 0) {
                $categoria['imps'] = number_format($categoria['imps'], 0, '.', ',');
            } else if ($this->user_notacion == 1) {
                $categoria['imps'] = number_format($categoria['imps'], 0, ',', '.');
            }

            $categories[] = $categoria;
        }

        $data['totales'] = $totales;
        $data['categorias'] = $categories;

        return $data;

        /*
          $reporte = $this->ci->inventario_anunciantes->get_inventario($filtros['fecha_desde'], $filtros['fecha_hasta'], 'adUnit_ID_2');

          foreach ($reporte as $report) {
          if (isset($filtros['paises'][$report->countryAd_ID])) {
          if (isset($new_reporte[$report->adUnit_ID_2])) {
          $new_reporte[$report->adUnit_ID_2]['imps'] += $report->totalImpressions * $this->multiplicacion_volumen;
          } else {
          $new_reporte[$report->adUnit_ID_2]['imps'] = $report->totalImpressions * $this->multiplicacion_volumen;
          }
          }
          }

          $categorias = null;

          if ($filtro_categorias == '0') {
          $filtro_categorias = $this->ci->categorias->get_categorias();
          foreach ($filtro_categorias as $cat) {
          $categorias[] = array($cat->id, $cat->nombre);
          }
          } else {
          $filtro_categorias = explode('o', trim($filtro_categorias, 'o'));
          foreach ($filtro_categorias as $cat) {
          $categoria = $this->ci->categorias->get_categoria_by_id($cat);
          $categorias[] = array($categoria->id, $categoria->nombre);
          }
          }

          $totales['imps'] = '-';

          unset($categoria);

          // recorro una por una las categorias y voy sumando las impresiones
          foreach ($categorias as $cat) {
          $categoria['nombre'] = $cat[1];
          $categoria['imps'] = 0;

          foreach ($filtros['sitios'] as $sitio) {
          if (isset($new_reporte[$sitio->id_adunit_site])) {
          $corre = FALSE;

          if ($this->ci->sitescategories->get_cat_by_site($sitio->id, $cat[0]))
          $corre = TRUE;

          if ($corre)
          $categoria['imps'] += $new_reporte[$sitio->id_adunit_site]['imps'];
          }
          }

          if ($this->user_notacion == 0) {
          $categoria['imps'] = number_format($categoria['imps'], 0, '.', ',');
          } else if ($this->user_notacion == 1) {
          $categoria['imps'] = number_format($categoria['imps'], 0, ',', '.');
          }

          $categories[] = $categoria;
          }

          $data['totales'] = $totales;
          $data['categorias'] = $categories;

          return $data;
         *
         */
    }

    public function reportePorFormato($token, $filtro_categorias, $filtro_paises, $intervalo, $fecha_desde, $fecha_hasta) {
        $filtros = $this->filtrarSitiosParaReporte($filtro_categorias, $filtro_paises, $intervalo, $fecha_desde, $fecha_hasta);

        if (!$filtros)
            return FALSE;

        $reporte = $this->ci->inventario_anunciantes->get_inventario_appnexus($filtros['fecha_desde'], $filtros['fecha_hasta'], 'adUnit_ID_3');

        foreach ($filtros['sitios'] as $sitio)
            $sitios[$sitio->id_site] = $sitio;

        $ya_consultados[] = 0;
        foreach ($reporte as $report) {
            if (isset($filtros['paises'][$report->geo_country_id])) {
                if (isset($sitios[$report->site_id])) {
                    if (isset($ya_consultados[$report->placement_id])) {
                        $new_reporte[$ya_consultados[$report->placement_id]]['imps'] += $report->imps_total * $this->multiplicacion_volumen;
                    } else {
                        $placement = $this->ci->placements->get_placement_by_id_appnexus($report->placement_id);
                        if ($placement) {
                            $ya_consultados[$report->placement_id] = $placement->id_tamanio;
                            if (isset($new_reporte[$placement->id_tamanio])) {
                                $new_reporte[$placement->id_tamanio]['imps'] += $report->imps_total * $this->multiplicacion_volumen;
                            } else {
                                $new_reporte[$placement->id_tamanio]['imps'] = $report->imps_total * $this->multiplicacion_volumen;
                            }
                        }
                    }
                }
            }
        }

        // recorro uno por uno los sitios del reporte
        $totales['imps'] = 0;

        unset($formato);

        // recorro una por una las categorias y voy sumando las impresiones
        foreach ($new_reporte as $llave => $valor) {
            // consulto los datos del formato
            $format = $this->ci->formatosdfp->get_formato_by_id($llave);

            if ($format) {
                $formato['nombre'] = $format->descripcion;

                $formato['imps'] = $new_reporte[$llave]['imps'];

                $totales['imps'] += $formato['imps'];

                if ($this->user_notacion == 0) {
                    $formato['imps'] = number_format($formato['imps'], 0, '.', ',');
                } else {
                    $formato['imps'] = number_format($formato['imps'], 0, ',', '.');
                }

                $formats[$llave] = $formato;
            }
        }

        if ($this->user_notacion == 0) {
            $totales['imps'] = number_format($totales['imps'], 0, '.', ',');
        } else if ($this->user_notacion == 1) {
            $totales['imps'] = number_format($totales['imps'], 0, ',', '.');
        }

        $data['totales'] = $totales;

        $data['formatos'] = $formats;

        return $data;

        /*
          $reporte = $this->ci->inventario_anunciantes->get_inventario($filtros['fecha_desde'], $filtros['fecha_hasta'], 'adUnit_ID_3');

          foreach ($filtros['sitios'] as $sitio)
          $sitios[$sitio->id_adunit_site] = $sitio;

          $ya_consultados[] = 0;
          foreach ($reporte as $report) {
          if (isset($filtros['paises'][$report->countryAd_ID])) {
          if (isset($sitios[$report->adUnit_ID_2])) {
          if (isset($ya_consultados[$report->adUnit_ID_3])) {
          $new_reporte[$ya_consultados[$report->adUnit_ID_3]]['imps'] += $report->totalImpressions * $this->multiplicacion_volumen;
          } else {
          $placement = $this->ci->placements->get_placement_by_id_DFP2($report->adUnit_ID_3);
          if ($placement) {
          $ya_consultados[$report->adUnit_ID_3] = $placement->id_tamanio;
          if (isset($new_reporte[$placement->id_tamanio])) {
          $new_reporte[$placement->id_tamanio]['imps'] += $report->totalImpressions * $this->multiplicacion_volumen;
          } else {
          $new_reporte[$placement->id_tamanio]['imps'] = $report->totalImpressions * $this->multiplicacion_volumen;
          }
          }
          }
          }
          }
          }

          // recorro uno por uno los sitios del reporte
          $totales['imps'] = 0;

          unset($formato);

          // recorro una por una las categorias y voy sumando las impresiones
          foreach ($new_reporte as $llave => $valor) {
          // consulto los datos del formato
          $format = $this->ci->formatosdfp->get_formato_by_id($llave);

          if ($format) {
          $formato['nombre'] = $format->descripcion;

          $formato['imps'] = $new_reporte[$llave]['imps'];

          $totales['imps'] += $formato['imps'];

          if ($this->user_notacion == 0) {
          $formato['imps'] = number_format($formato['imps'], 0, '.', ',');
          } else {
          $formato['imps'] = number_format($formato['imps'], 0, ',', '.');
          }

          $formats[$llave] = $formato;
          }
          }

          if ($this->user_notacion == 0) {
          $totales['imps'] = number_format($totales['imps'], 0, '.', ',');
          } else if ($this->user_notacion == 1) {
          $totales['imps'] = number_format($totales['imps'], 0, ',', '.');
          }

          $data['totales'] = $totales;

          $data['formatos'] = $formats;

          return $data;
         *
         */
    }

    public function reportePorPaisFormato($token, $filtro_categorias, $filtro_paises, $intervalo, $fecha_desde, $fecha_hasta) {
        $filtros = $this->filtrarSitiosParaReporte($filtro_categorias, $filtro_paises, $intervalo, $fecha_desde, $fecha_hasta);

        if (!$filtros)
            return FALSE;

        $reporte_1 = $this->ci->inventario_anunciantes->get_inventario_appnexus($filtros['fecha_desde'], $filtros['fecha_hasta'], 'adUnit_ID_3');

        $totales['imps'] = 0;

        foreach ($reporte_1 as $report) {
            if (isset($filtros['paises'][$report->geo_country_id])) {
                if (isset($new_reporte[$report->geo_country_id][$report->placement_id])) {
                    $new_reporte[$report->geo_country_id][$report->placement_id]['imps'] += $report->imps_total * $this->multiplicacion_volumen;
                } else {
                    $placement = $this->ci->placements->get_data_placement_by_id_appnexus($report->placement_id);
                    if ($placement) {
                        $new_reporte[$report->geo_country_id][$report->placement_id]['imps'] = $report->imps_total * $this->multiplicacion_volumen;
                        $new_reporte[$report->geo_country_id][$report->placement_id]['nombre'] = $placement->descripcion;
                    }
                }
            }
        }

        $new_report_2 = null;
        foreach ($new_reporte as $id_pais => $pais) {
            foreach ($pais as $espacios) {
                if (isset($asd[$espacios['nombre']])) {
                    $asd[$espacios['nombre']]['imps'] = $espacios['imps'];
                } else {
                    $asd[$espacios['nombre']]['imps'] = $espacios['imps'];
                }

                $totales['imps'] += $asd[$espacios['nombre']]['imps'];

                if ($this->user_notacion == 0) {
                    $asd[$espacios['nombre']]['imps'] = number_format($asd[$espacios['nombre']]['imps'], 0, ',', '.');
                } else if ($this->user_notacion == 1) {
                    $asd[$espacios['nombre']]['imps'] = number_format($asd[$espacios['nombre']]['imps'], 0, ',', '.');
                }
            }

            $nombre_pais = $this->ci->paises->get_pais_by_id($id_pais);

            $new_report_2[$nombre_pais->descripcion] = $asd;
        }

        $data['paises'] = $new_report_2;

        if ($this->user_notacion == 0) {
            $totales['imps'] = number_format($totales['imps'], 0, ',', '.');
        } else if ($this->user_notacion == 1) {
            $totales['imps'] = number_format($totales['imps'], 0, ',', '.');
        }

        $data['totales'] = $totales;

        return $data;

        /*
          $reporte_1 = $this->ci->inventario_anunciantes->get_inventario($filtros['fecha_desde'], $filtros['fecha_hasta'], 'adUnit_ID_3');

          $totales['imps'] = 0;

          foreach ($reporte_1 as $report) {
          if (isset($filtros['paises'][$report->countryAd_ID])) {
          if (isset($new_reporte[$report->countryAd_ID][$report->adUnit_ID_3])) {
          $new_reporte[$report->countryAd_ID][$report->adUnit_ID_3]['imps'] += $report->totalImpressions * $this->multiplicacion_volumen;
          } else {
          $placement = $this->ci->placements->get_data_placement_by_id_DFP($report->adUnit_ID_3);
          if ($placement) {
          $new_reporte[$report->countryAd_ID][$report->adUnit_ID_3]['imps'] = $report->totalImpressions * $this->multiplicacion_volumen;
          $new_reporte[$report->countryAd_ID][$report->adUnit_ID_3]['nombre'] = $placement->descripcion;
          }
          }
          }
          }

          $new_report_2 = null;
          foreach ($new_reporte as $id_pais => $pais) {
          foreach ($pais as $espacios) {
          if (isset($asd[$espacios['nombre']])) {
          $asd[$espacios['nombre']]['imps'] = $espacios['imps'];
          } else {
          $asd[$espacios['nombre']]['imps'] = $espacios['imps'];
          }

          $totales['imps'] += $asd[$espacios['nombre']]['imps'];
          }

          $nombre_pais = $this->ci->paises->get_pais_by_id_DFP($id_pais);

          $new_report_2[$nombre_pais->descripcion] = $asd;
          }

          $data['paises'] = $new_report_2;

          if ($this->user_notacion == 0) {
          $totales['imps'] = number_format($totales['imps'], 0, ',', '.');
          } else if ($this->user_notacion == 1) {
          $totales['imps'] = number_format($totales['imps'], 0, ',', '.');
          }

          $data['totales'] = $totales;

          return $data;
         *
         */
    }

    public function reportePorSitioFormato($token, $filtro_categorias, $filtro_paises, $intervalo, $fecha_desde, $fecha_hasta) {
        $filtros = $this->filtrarSitiosParaReporte($filtro_categorias, $filtro_paises, $intervalo, $fecha_desde, $fecha_hasta);

        if (!$filtros)
            return FALSE;

        $reporte = $this->ci->inventario_anunciantes->get_inventario_appnexus($filtros['fecha_desde'], $filtros['fecha_hasta'], 'adUnit_ID_3');

        $totales['imps'] = 0;

        foreach ($reporte as $report) {
            if (isset($filtros['paises'][$report->geo_country_id])) {
                if (isset($new_reporte[$report->site_id]['formatos'][$report->placement_id])) {
                    $new_reporte[$report->site_id]['formatos'][$report->placement_id]['imps'] += $report->imps_total * $this->multiplicacion_volumen;
                } else {
                    $placement = $this->ci->placements->get_data_placement_by_id_appnexus($report->placement_id);
                    if ($placement) {
                        $new_reporte[$report->site_id]['formatos'][$report->placement_id]['imps'] = $report->imps_total * $this->multiplicacion_volumen;
                        $new_reporte[$report->site_id]['formatos'][$report->placement_id]['nombre'] = $placement->descripcion;
                    }
                }
            }
        }

        foreach ($filtros['sitios'] as $sitio) {
            if (isset($new_reporte[$sitio->id_site])) {
                if ($new_reporte[$sitio->id_site]['formatos']) {
                    $sitios_filtrados[$sitio->id]['id'] = $sitio->id_site;

                    $sitios_filtrados[$sitio->id]['url_sitio'] = $sitio->nombre_appnexus;
                    if ($sitios_filtrados[$sitio->id]['url_sitio'] == '')
                        $sitios_filtrados[$sitio->id]['url_sitio'] = $sitio->nombre_dfp;

                    // Personalizo los nombres para GLAM
                    $nombre_glam = $this->reemplazarNombresGlam($sitio->id);
                    if ($nombre_glam)
                        $sitios_filtrados[$sitio->id]['url_sitio'] = $nombre_glam;

                    $sitios_filtrados[$sitio->id]['url_sitio'] = str_replace("http://", "", $sitios_filtrados[$sitio->id]['url_sitio']);
                    $sitios_filtrados[$sitio->id]['url_sitio'] = str_replace("/", "", $sitios_filtrados[$sitio->id]['url_sitio']);

                    $sitios_filtrados[$sitio->id]['categorias'] = $this->_categoriasPorSitio($sitio->id);
                    if ($sitios_filtrados[$sitio->id]['categorias'] == '')
                        $sitios_filtrados[$sitio->id]['categorias'] = '-';

                    foreach ($new_reporte[$sitio->id_site]['formatos'] as $format) {
                        $totales['imps'] += $format['imps'];

                        $formato['nombre'] = $format['nombre'];

                        if ($this->user_notacion == 0) {
                            $formato['imps'] = number_format($format['imps'], 0, '.', ',');
                        } else if ($this->user_notacion == 1) {
                            $formato['imps'] = number_format($format['imps'], 0, ',', '.');
                        }

                        $formatos[] = $formato;
                    }

                    $sitios_filtrados[$sitio->id]['formatos'] = $formatos;
                }
            }
        }

        $totales['imps'] += $new_reporte[$sitio->id_site]['imps'];

        if ($this->user_notacion == 0) {
            $sitios_filtrados[$sitio->id]['imps'] = number_format($new_reporte[$sitio->id_site]['imps'], 0, '.', ',');
        } else if ($this->user_notacion == 1) {
            $sitios_filtrados[$sitio->id]['imps'] = number_format($new_reporte[$sitio->id_site]['imps'], 0, ',', '.');
        }

        if (!isset($sitios_filtrados))
            return FALSE;

        if ($this->user_notacion == 0) {
            $totales['imps'] = number_format($totales['imps'], 0, ',', '.');
        } else if ($this->user_notacion == 1) {
            $totales['imps'] = number_format($totales['imps'], 0, ',', '.');
        }

        $data['totales'] = $totales;

        $data['sitios'] = $sitios_filtrados;

        return $data;
    }

    public function reportePorPais($token, $filtro_categorias, $filtro_paises, $intervalo, $fecha_desde, $fecha_hasta) {
        $filtros = $this->filtrarSitiosParaReporte($filtro_categorias, $filtro_paises, $intervalo, $fecha_desde, $fecha_hasta);

        if (!$filtros)
            return FALSE;

        $reporte = $this->ci->inventario_anunciantes->get_inventario_appnexus($filtros['fecha_desde'], $filtros['fecha_hasta'], 'adUnit_ID_2');

        foreach ($reporte as $report) {
            $paises_reportes[$report->geo_country_id] = $report->geo_country_id;

            if (isset($new_reporte[$report->site_id][$report->geo_country_id])) {
                $new_reporte[$report->site_id][$report->geo_country_id]['imps'] += $report->imps_total * $this->multiplicacion_volumen;
            } else {
                $new_reporte[$report->site_id][$report->geo_country_id]['imps'] = $report->imps_total * $this->multiplicacion_volumen;
            }
        }

        if ($filtro_paises == '') {
            $filtro_paises = $this->ci->paises->get_paises();
            foreach ($filtro_paises as $pais)
                $paises[] = array($pais->id, $pais->id, $pais->descripcion);
        } else {
            $filtro_paises = explode('o', trim($filtro_paises, 'o'));
            foreach ($filtro_paises as $pais) {
                $pais = $this->ci->paises->get_pais_by_id($pais);
                $paises[] = array($pais->id, $pais->id, $pais->descripcion);
            }
        }

        $totales['imps'] = 0;

        unset($pais);

        // recorro una por una los paises y voy sumando las impresiones
        foreach ($paises as $country) {
            $paises_reportes[$country[1]] = 'EJECUTADO';

            $pais['nombre'] = $country[2];
            $pais['imps'] = 0;

            foreach ($filtros['sitios'] as $sitio) {
                if (isset($new_reporte[$sitio->id_site][$country[1]])) {
                    $pais['imps'] += $new_reporte[$sitio->id_site][$country[1]]['imps'];
                }
            }

            $totales['imps'] += $pais['imps'];

            if ($this->user_notacion == 0) {
                $pais['imps'] = number_format($pais['imps'], 0, '.', ',');
            } else {
                $pais['imps'] = number_format($pais['imps'], 0, ',', '.');
            }

            $countries[] = $pais;
        }

        $hay_desconocidos = FALSE;
        foreach ($paises_reportes as $country) {
            if ($country != 'EJECUTADO') {
                $paises_reportes[$country] = 'EJECUTADO';

                foreach ($filtros['sitios'] as $sitio) {
                    if (isset($new_reporte[$sitio->id_site][$country])) {
                        $pais['imps'] += $new_reporte[$sitio->id_site][$country]['imps'];
                        $hay_desconocidos = TRUE;
                    }
                }
            }
        }

        if ($hay_desconocidos && $filtro_paises == '') {
            $pais['nombre'] = 'Desconocido';

            $totales['imps'] += $pais['imps'];

            if ($this->user_notacion == 0) {
                $pais['imps'] = number_format($pais['imps'], 0, '.', ',');
            } else if ($this->user_notacion == 1) {
                $pais['imps'] = number_format($pais['imps'], 0, ',', '.');
            }

            $countries[] = $pais;
        }

        if ($this->user_notacion == 0) {
            $totales['imps'] = number_format($totales['imps'], 0, '.', ',');
        } else if ($this->user_notacion == 1) {
            $totales['imps'] = number_format($totales['imps'], 0, ',', '.');
        }

        $data['totales'] = $totales;

        $data['paises'] = $countries;

        return $data;

        /*
          $reporte = $this->ci->inventario_anunciantes->get_inventario($filtros['fecha_desde'], $filtros['fecha_hasta'], 'adUnit_ID_2');

          foreach ($reporte as $report) {
          $paises_reportes[$report->countryAd_ID] = $report->countryAd_ID;

          if (isset($new_reporte[$report->adUnit_ID_2][$report->countryAd_ID])) {
          $new_reporte[$report->adUnit_ID_2][$report->countryAd_ID]['imps'] += $report->totalImpressions * $this->multiplicacion_volumen;
          } else {
          $new_reporte[$report->adUnit_ID_2][$report->countryAd_ID]['imps'] = $report->totalImpressions * $this->multiplicacion_volumen;
          }
          }

          if ($filtro_paises == '') {
          $filtro_paises = $this->ci->paises->get_paises();
          foreach ($filtro_paises as $pais)
          $paises[] = array($pais->id, $pais->id_dfp, $pais->descripcion);
          } else {
          $filtro_paises = explode('o', trim($filtro_paises, 'o'));
          foreach ($filtro_paises as $pais) {
          $pais = $this->ci->paises->get_pais_by_id($pais);
          $paises[] = array($pais->id, $pais->id_dfp, $pais->descripcion);
          }
          }

          $totales['imps'] = 0;

          unset($pais);

          // recorro una por una los paises y voy sumando las impresiones
          foreach ($paises as $country) {
          $paises_reportes[$country[1]] = 'EJECUTADO';

          $pais['nombre'] = $country[2];
          $pais['imps'] = 0;

          foreach ($filtros['sitios'] as $sitio) {
          if (isset($new_reporte[$sitio->id_adunit_site][$country[1]])) {
          $pais['imps'] += $new_reporte[$sitio->id_adunit_site][$country[1]]['imps'];
          }
          }

          $totales['imps'] += $pais['imps'];

          if ($this->user_notacion == 0) {
          $pais['imps'] = number_format($pais['imps'], 0, '.', ',');
          } else if ($this->user_notacion == 1) {
          $pais['imps'] = number_format($pais['imps'], 0, ',', '.');
          }

          $countries[] = $pais;
          }

          $hay_desconocidos = FALSE;
          foreach ($paises_reportes as $country) {
          if ($country != 'EJECUTADO') {
          $paises_reportes[$country] = 'EJECUTADO';

          foreach ($filtros['sitios'] as $sitio) {
          if (isset($new_reporte[$sitio->id_adunit_site][$country])) {
          $pais['imps'] += $new_reporte[$sitio->id_adunit_site][$country]['imps'];
          $hay_desconocidos = TRUE;
          }
          }
          }
          }

          if ($hay_desconocidos && $filtro_paises == '') {
          $pais['nombre'] = 'Desconocido';

          $totales['imps'] += $pais['imps'];

          if ($this->user_notacion == 0) {
          $pais['imps'] = number_format($pais['imps'], 0, '.', ',');
          } else if ($this->user_notacion == 1) {
          $pais['imps'] = number_format($pais['imps'], 0, ',', '.');
          }

          $countries[] = $pais;
          }

          if ($this->user_notacion == 0) {
          $totales['imps'] = number_format($totales['imps'], 0, '.', ',');
          } else if ($this->user_notacion == 1) {
          $totales['imps'] = number_format($totales['imps'], 0, ',', '.');
          }

          $data['totales'] = $totales;

          $data['paises'] = $countries;

          return $data;
         *
         */
    }

    private function _categoriasPorSitio($id_sitio) {
        $string = '';
        $categorias_sitio = $this->ci->sitescategories->get_cats_by_site($id_sitio);

        if ($categorias_sitio) {
            if (sizeof($categorias_sitio) > 1) {
                foreach ($categorias_sitio as $cats)
                    $string .= $cats->nombre . ', ';

                return $string = trim(substr_replace($string, ' y ', strrpos(trim($string, ', '), ', '), 1), ', ');
            } else {
                return $categorias_sitio[0]->nombre;
            }
        }

        return FALSE;
    }

    public function _filtrarSitiosPorCategorias($array_sitios, $categorias_seleccionadas) {

        $arr_sitios = FALSE;
        $filtro_categorias = explode('o', trim($categorias_seleccionadas, 'o'));
        foreach ($array_sitios as $sitio) {
            // traigo todas las categorias de cada sitio
            $categorias_sitio = $this->ci->sitescategories->get_all_cats_by_site($sitio->id);
            if ($categorias_sitio) {
                // el sitio corre en la categoria seleccionada?
                foreach ($categorias_sitio as $cat_sitio) {
                    if (in_array($cat_sitio->id_categoria, $filtro_categorias)) {
                        $arr_sitios[] = $sitio;
                        break;
                    }
                }
            }
        }

        return $arr_sitios;
    }

    private function _filtrarSitiosPorTamanio($array_sitios, $tamanios_seleccionados) {
        $arr_sitios = '';
        $filtro_tamanios = explode('o', trim($tamanios_seleccionados, 'o'));
        foreach ($array_sitios as $sitio) {
            // traigo todos los placements de cada sitio
            $tamanios_sitio = $this->ci->placements->get_placement_by_id_sitio($sitio->id);
            if ($tamanios_sitio != NULL) {
                // el sitio corre en la categoria seleccionada?
                foreach ($tamanios_sitio as $tam_sitio) {
                    if ($tam_sitio->id_tamanio == 9 || $tam_sitio->id_tamanio == 10 || $tam_sitio->id_tamanio == 11) {
                        if (in_array(9, $filtro_tamanios)) {
                            $arr_sitios[] = $sitio;
                            break;
                        }
                        if (in_array(10, $filtro_tamanios)) {
                            $arr_sitios[] = $sitio;
                            break;
                        }
                        if (in_array(11, $filtro_tamanios)) {
                            $arr_sitios[] = $sitio;
                            break;
                        }
                    } else {
                        if (in_array($tam_sitio->id_tamanio, $filtro_tamanios)) {
                            $arr_sitios[] = $sitio;
                            break;
                        }
                    }
                }
            }
        }

        return $arr_sitios;
    }

    private function _filtrarSitiosPorCategorias_dinamico($array_sitios, $categorias_seleccionadas) {
        $arr_sitios = '';
        $filtro_categorias = explode('o', trim($categorias_seleccionadas, 'o'));
        foreach ($array_sitios as $sitio) {
            // traigo todas las categorias de cada sitio
            $categorias_sitio = $this->ci->sitescategories->get_all_cats_by_site($sitio->id);
            if ($categorias_sitio) {
                // el sitio corre en la categoria seleccionada?
                foreach ($categorias_sitio as $cat_sitio) {
                    if (in_array($cat_sitio->id_categoria, $filtro_categorias)) {
                        $arr_sitios['visibles'] = $sitio;
                        break;
                    }
                }
            }
        }

        return $arr_sitios;
    }

    private function _AString($string, $separador = ',') {
        // quito la ultima coma y la transformo en "Y"
        $cats = explode($separador, trim($string, ','));
        $cant = sizeof($cats);
        $string = '';
        for ($a = 0; $a < $cant; $a++) {
            if ($a == ($cant - 2)) {
                $string .= $cats[$a] . ' y ';
            } else {
                $string .= $cats[$a] . ', ';
            }
        }

        return substr($string, 0, - 2);
    }

    private function _fechasPorIntervalo($intervalo, $fecha_desde = '', $fecha_hasta = '') {
        if ($intervalo == 'today') {
            // solamente hoy
            $fecha_desde = date('Y-m-d') . ' 00:00:00';
            $fecha_hasta = date('Y-m-d') . ' 23:59:59';
        } else if ($intervalo == 'yesterday') {
            // desde hoy menos un dia, hasta hoy menos un dia
            $fecha_desde = date('Y-m-d', strtotime("-1 day")) . ' 00:00:00';
            $fecha_hasta = date('Y-m-d', strtotime("-1 day")) . ' 23:59:59';
        } else if ($intervalo == 'last_7_days') {
            // desde hoy menos siete dias, hasta hoy
            $fecha_desde = date('Y-m-d', strtotime("-7 day")) . ' 00:00:00';
            $fecha_hasta = date('Y-m-d') . ' 23:59:59';
        } else if ($intervalo == 'month_to_date') {
            // desde el 1 de este mes de este año, hasta hoy
            $fecha_desde = date('Y-m-01') . ' 00:00:00';
            $fecha_hasta = date('Y-m-d') . ' 23:59:59';
        } else if ($intervalo == 'last_month') {
            // desde el 1 de el mes pasado de este año, hasta el 30 de el mes pasado de este año
            $fecha = date('Y-m-01');
            $fecha_desde = date('Y-m-d', strtotime("$fecha last month")) . ' 00:00:00';
            $fecha_hasta = date('Y-m-t', strtotime("$fecha last month")) . ' 23:59:59';
        } else if ($intervalo == 'especific') {
            // fechas especificas (ingresadas por el usuario)
            list($dia_desde, $mes_desde, $anio_desde) = explode("-", $fecha_desde);
            list($dia_hasta, $mes_hasta, $anio_hasta) = explode("-", $fecha_hasta);

            if (checkdate($mes_desde, $dia_desde, $anio_desde)) {
                $fecha_desde = $anio_desde . "-" . $mes_desde . "-" . $dia_desde . ' 00:00:00';
            } else {
                $fecha_desde = false;
            }

            if (checkdate($mes_hasta, $dia_hasta, $anio_hasta)) {
                $fecha_hasta = $anio_hasta . "-" . $mes_hasta . "-" . $dia_hasta . ' 23:59:59';
            } else {
                $fecha_desde = false;
            }
        } else if ($intervalo == 'lifetime') {
            // desde el principio de los tiempos hasta el dia de hoy
            $fecha_desde = '2010-01-01' . ' 00:00:00';
            $fecha_hasta = date('Y-m-d') . ' 23:59:59';
        }

        return array('desde' => $fecha_desde, 'hasta' => $fecha_hasta);
    }

    function crear_anunciante_dfp($nombre, $email = null) {
        try {

            if ($email == null)
                $email = $this->ci->user_data->email;

            $user = $this->getDFPUser();
            $companyService = $user->GetService('CompanyService', 'v201208');

            $data = new Company();
            $data->name = $nombre . ' - ' . $email;
            $data->type = 'ADVERTISER';
            $data->creditStatus = 'ACTIVE';

            $res = $companyService->createCompany($data);

            if ($res) {
                return array('id' => $res->id, 'nombre' => $nombre . ' - ' . $email);
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    function crear_anunciante_appnexus($nombre, $email = null) {
        try {

            if ($email == null)
                $email = $this->ci->user_data->email;

            $data = new stdClass();
            $data->advertiser = new stdClass();
            $data->advertiser->name = $nombre;
            $data->advertiser->state = 'active';

            $request = new Request();
            $request->method = 'post';
            $request->uri = BASE_URI . '/advertiser';
            $request->token = $this->ci->token;
            $request->data = $data;
            $res = Caller::call($request);

            if ($res->response->status == 'OK') {
                return array('id' => $res->response->id, 'nombre' => $nombre . ' - ' . $email);
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    function crear_audiencia($nombre, $id_anunciante_adserver) {
        try {

            $anunciante_adserver = $this->ci->anunciantes->get_anunciante_adserver_by_id($id_anunciante_adserver);

            $data = new stdClass();
            $data->segment = new stdClass();
            $data->segment->short_name = $nombre;
            $data->segment->advertiser_id = $anunciante_adserver->id_appnexus;

            $request = new Request();
            $request->method = 'post';
            $request->uri = BASE_URI . '/segment';
            $request->token = $this->ci->token;
            $request->data = $data;
            $res = Caller::call($request);

            if ($res->response->status == 'OK') {
                return array('id' => $res->response->id, 'nombre' => $nombre);
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    function crear_campania_AppNexus($id_campania, $campania_de = 'adtk') {
        try {
            $campania = $this->ci->campanias->get_campania_by_id($id_campania);
            if (!$campania){
		//die('1');
                return FALSE;
		}
            $anunciante = $this->ci->anunciantes->get_anunciante_adserver_by_id($campania->id_anunciante);
            if (!$anunciante){
		//die('2');
                return FALSE;
		}
            // CREO EL ADVERTISER EN APPNEXUS **********************************
            if (!$anunciante->id_appnexus || $anunciante->id_appnexus == 0) {
                $anunciante_appnexus = $this->crear_anunciante_appnexus($anunciante->nombre);

                if ($anunciante_appnexus)
                    $this->ci->anunciantes->update_anunciante_adserver($campania->id_anunciante, array('id_appnexus' => $anunciante_appnexus['id'], 'adserver_actual' => 1));

                $anunciante = $this->ci->anunciantes->get_anunciante_adserver_by_id($campania->id_anunciante);
                if (!$anunciante){
			//die('3');
                    return FALSE;
		}
            }

            // CREO EL LINE ITEM DE LA CAMPANIA ********************************
            $lineItem = "line-item";

            $revenue_value = $campania->valor_unidad - (($campania->comision * $campania->valor_unidad) / 100);
            $revenue_value = $revenue_value - (($campania->descuento * $revenue_value) / 100);

            $impresiones_clicks = $campania->cantidad;

            if ($campania->modalidad_compra == 'cpv') {
                $revenue_value = ($impresiones_clicks * $revenue_value) / (($impresiones_clicks * 2) / 1000);
                $impresiones_clicks = $impresiones_clicks * 2;

                $campania->modalidad_compra = 'cpm';
            }

            if ($campania->modalidad_compra == 'cpa')
                $campania->modalidad_compra = 'cpc';

            $campania->inversion_neta = $campania->inversion_neta * $this->_dateDiff($campania->fecha_inicio, $campania->fecha_fin);

            $data = new stdClass();
            $data->$lineItem = new stdClass();
            $data->$lineItem->name = $campania->nombre;
            $data->$lineItem->advertiser_id = $anunciante->id_appnexus;
            $data->$lineItem->state = 'inactive';
            $data->$lineItem->revenue_type = $campania->modalidad_compra;
            $data->$lineItem->revenue_value = $this->cambiar_moneda($revenue_value, $campania->moneda);
            $data->$lineItem->lifetime_budget = $this->cambiar_moneda($campania->inversion_neta, $campania->moneda);
            $data->$lineItem->start_date = $campania->fecha_inicio;
            $data->$lineItem->end_date = $campania->fecha_fin;

            /*
              if($campania->tipo_campania == 'tradicional'){
              $data->$lineItem->goal_type = 'ctr';
              $data->$lineItem->goal_value = (double) '0,15';
              //$data->$lineItem->valuation = new stdClass();
              //$data->$lineItem->valuation->goal_target = 0.15;
              }
             *
             */

            $request = new Request();
            $request->method = 'post';
            $request->uri = BASE_URI . '/line-item?advertiser_id=' . $anunciante->id_appnexus;
            $request->token = $this->ci->token;
            $request->data = $data;
            $res = Caller::call($request);

            if ($res->response->status == 'OK') {
                $lineItem_id = $res->response->id;
            } else {
		//die('4');	
                return FALSE;
            }


            if ($campania_de == 'mf') {
                if ($campania->segmentacion_id != 2) {
                    // CREO UN PROFILE PARA EL TARGET DE LA CAMPANIA *******************
                    // selecciono los paises de la campania
                    $paisescampania = $this->ci->campaniaspaises->get_paises_by_campania($id_campania);
                    $paises = NULL;

                    if ($paisescampania) {
                        foreach ($paisescampania as $pais) {
                            $tmp_pais = new stdClass();
                            $tmp_pais->country = $pais->id_pais;

                            $paises[] = $tmp_pais;
                        }
                    }

                    // selecciono las audiencias de la campania
                    if ($campania_de == 'adtk') {
                        $audienciascampania = $this->ci->campanias->get_audiencias_by_campania($id_campania);
                        $audiencias = NULL;

                        if ($audienciascampania) {
                            foreach ($audienciascampania as $audiencia) {
                                $tmp_audiencia = new stdClass();
                                $tmp_audiencia->id = (int) $audiencia->id_appnexus;
                                $tmp_audiencia->action = $audiencia->action;

                                $audiencias[] = $tmp_audiencia;
                            }

                            $segment_group_target[0] = new stdClass();
                            $segment_group_target[0]->boolean_operator = 'and';
                            $segment_group_target[0]->segments = $audiencias;
                        }
                    }

                    // si es toda la red selecciono todos los canales no excluidos.
                    if ($campania_de == 'mf') {
                        if ($campania->segmentacion_id == 1) {
                            $canalesAppnexus = $this->ci->categorias->get_categorias();
                            foreach ($canalesAppnexus as $value) {
                                if ($value->id_appnexus)
                                    $canales_appnexus[$value->id] = $value;
                            }

                            $content_categories = NULL;

                            foreach ($canales_appnexus as $canal_appnexus) {
                                $content_categorie = new stdClass();
                                $content_categorie->id = $canal_appnexus->id_appnexus;
                                $content_categorie->action = 'include';
                                $content_categories[] = $content_categorie;
                            }
                        }
                    }

                    // selecciono los sitios especificos de la campania en caso de ser necesario
                    if ($campania_de == 'mf') {
                        if ($campania->segmentacion_id == 3) {
                            $sitiosAppnexus = $this->ci->sites->get_all_sites();
                            foreach ($sitiosAppnexus as $value) {
                                if ($value->id_site)
                                    $sitios_appnexus[$value->id] = $value;
                            }

                            $sitios_campania = $this->ci->campaniassitios->get_sitios_by_campania($campania->id);

                            $site_targets = NULL;

                            if ($sitios_campania) {
                                foreach ($sitios_campania as $campania_sitio) {
                                    if (isset($sitios_appnexus[$campania_sitio->id_sitio])) {
                                        $site_target = new stdClass();
                                        $site_target->id = $sitios_appnexus[$campania_sitio->id_sitio]->id_site;
                                        $site_target->action = 'include';

                                        $site_targets[] = $site_target;
                                    }
                                }
                            }
                        }
                    }


                    unset($data);
                    $data = new stdClass();
                    $data->profile = new stdClass();
                    $data->profile->country_action = 'include';
                    $data->profile->country_targets = $paises;

                    if ($campania_de == 'adtk') {
                        if ($audienciascampania) {
                            $data->profile->segment_boolean_operator = 'or';
                            $data->profile->segment_group_targets = $segment_group_target;
                        }
                    }

                    if ($campania_de == 'mf') {
                        if ($campania->segmentacion_id == 1 && $content_categories) {
                            $data->profile->content_category_targets = new stdClass();
                            $data->profile->content_category_targets->allow_unknown = false;
                            $data->profile->content_category_targets->content_categories = $content_categories;
                        }
                    }

                    /*
                      if ($campania_de == 'mf') {
                      if ($campania->segmentacion_id == 2 && $content_categories) {
                      $data->profile->content_category_targets = new stdClass();
                      $data->profile->content_category_targets->allow_unknown = false;
                      $data->profile->content_category_targets->content_categories = $content_categories;
                      }
                      }
                     *
                     */

                    if ($campania_de == 'mf') {
                        if ($campania->segmentacion_id == 3 && $site_targets) {
                            $data->profile->inventory_action = 'exclude';
                            $data->profile->site_targets = $site_targets;
                        }
                    }

                    // asigno la frecuencia que va a correr la campania
                    if ($campania_de == 'mf') {
                        if ($campania->frecuencia == '1x24')
                            $data->profile->max_day_imps = 1;

                        if ($campania->frecuencia == '2x24')
                            $data->profile->max_day_imps = 2;
                    }


                    $data->profile->allow_unaudited = true;


                    $request = new Request();
                    $request->method = 'post';
                    $request->uri = BASE_URI . '/profile?advertiser_id=' . $anunciante->id_appnexus;
                    $request->token = $this->ci->token;
                    $request->data = $data;
                    $res = Caller::call($request);

                    if ($res->response->status == 'OK') {
                        $profile_id = $res->response->id;
                    } else {
                        return FALSE;
                    }

                    // CREO LA CAMPANIA PARA EL LINE ITEM CREADO ***********************
                    unset($data);
                    $data = new stdClass();
                    $data->campaign = new stdClass();
                    $data->campaign->name = 'Direct';
                    $data->campaign->advertiser_id = $anunciante->id_appnexus;
                    $data->campaign->line_item_id = $lineItem_id;
                    $data->campaign->inventory_type = 'direct';
                    $data->campaign->profile_id = $profile_id;

                    $request = new Request();
                    $request->method = 'post';
                    $request->uri = BASE_URI . '/campaign?advertiser_id=' . $anunciante->id_appnexus;
                    $request->token = $this->ci->token;
                    $request->data = $data;
                    $res = Caller::call($request);

                    if ($res->response->status == 'OK') {
                        $campaign_id = $res->response->id;
                    } else {
                        return FALSE;
                    }
                } else if ($campania->segmentacion_id == 2) {

                    // SELECCIONO LOS CANALES TEMATICOS DE LA CAMPANIA *********
                    $canalesAppnexus = $this->ci->categorias->get_categorias();
                    foreach ($canalesAppnexus as $value) {
                        if ($value->id_appnexus)
                            $canales_appnexus[$value->id] = $value;
                    }

                    $canales_campania = $this->ci->campaniascanalestematicos->get_canales_tematicos_by_campania($campania->id);


                    // selecciono los paises de la campania
                    $paisescampania = $this->ci->campaniaspaises->get_paises_by_campania($id_campania);
                    $paises = NULL;

                    if ($paisescampania) {
                        foreach ($paisescampania as $pais) {
                            $tmp_pais = new stdClass();
                            $tmp_pais->country = $pais->id_pais;

                            $paises[] = $tmp_pais;
                        }
                    }


                    // selecciono las audiencias de la campania
                    if ($campania_de == 'adtk') {
                        $audienciascampania = $this->ci->campanias->get_audiencias_by_campania($id_campania);
                        $audiencias = NULL;

                        if ($audienciascampania) {
                            foreach ($audienciascampania as $audiencia) {
                                $tmp_audiencia = new stdClass();
                                $tmp_audiencia->id = (int) $audiencia->id_appnexus;
                                $tmp_audiencia->action = $audiencia->action;

                                $audiencias[] = $tmp_audiencia;
                            }

                            $segment_group_target[0] = new stdClass();
                            $segment_group_target[0]->boolean_operator = 'and';
                            $segment_group_target[0]->segments = $audiencias;
                        }
                    }



                    if ($canales_campania) {
                        foreach ($canales_campania as $campania_canal) {
                            if (isset($canales_appnexus[$campania_canal->id_canal_tematico])) {
                                // CREO UN PROFILE POR CADA CANAL TEMATICO PARA EL TARGET DE LA CAMPANIA *******************

                                unset($data, $content_categorie, $content_categories, $profile_id);

                                $content_categorie = new stdClass();
                                $content_categorie->id = $canales_appnexus[$campania_canal->id_canal_tematico]->id_appnexus;
                                $content_categorie->action = 'include';
                                $content_categories[] = $content_categorie;


                                $data = new stdClass();
                                $data->profile = new stdClass();
                                $data->profile->country_action = 'include';
                                $data->profile->country_targets = $paises;

                                if ($campania_de == 'adtk') {
                                    if ($audienciascampania) {
                                        $data->profile->segment_boolean_operator = 'or';
                                        $data->profile->segment_group_targets = $segment_group_target;
                                    }
                                }

                                if ($campania_de == 'mf') {
                                    if ($campania->segmentacion_id == 2 && $content_categories) {
                                        $data->profile->content_category_targets = new stdClass();
                                        $data->profile->content_category_targets->allow_unknown = false;
                                        $data->profile->content_category_targets->content_categories = $content_categories;
                                    }
                                }

                                // asigno la frecuencia que va a correr la campania
                                if ($campania_de == 'mf') {
                                    if ($campania->frecuencia == '1x24')
                                        $data->profile->max_day_imps = 1;

                                    if ($campania->frecuencia == '2x24')
                                        $data->profile->max_day_imps = 2;
                                }


                                $data->profile->allow_unaudited = true;


                                $request = new Request();
                                $request->method = 'post';
                                $request->uri = BASE_URI . '/profile?advertiser_id=' . $anunciante->id_appnexus;
                                $request->token = $this->ci->token;
                                $request->data = $data;
                                $res = Caller::call($request);

                                if ($res->response->status == 'OK') {
                                    $profile_id = $res->response->id;
                                } else {
                                    return FALSE;
                                }

                                // CREO LA CAMPANIA PARA EL LINE ITEM CREADO ***********************
                                unset($data);
                                $data = new stdClass();
                                $data->campaign = new stdClass();
                                $data->campaign->name = $canales_appnexus[$campania_canal->id_canal_tematico]->nombre;
                                $data->campaign->advertiser_id = $anunciante->id_appnexus;
                                $data->campaign->line_item_id = $lineItem_id;
                                $data->campaign->inventory_type = 'direct';
                                $data->campaign->profile_id = $profile_id;

                                $request = new Request();
                                $request->method = 'post';
                                $request->uri = BASE_URI . '/campaign?advertiser_id=' . $anunciante->id_appnexus;
                                $request->token = $this->ci->token;
                                $request->data = $data;
                                $res = Caller::call($request);

                                if ($res->response->status == 'OK') {
                                    $campaign_id = $res->response->id;
                                } else {
                                    return FALSE;
                                }
                            }
                        }
                    }
                }
            } else if ($campania_de == 'adtk') {
                // CREO UN PROFILE PARA EL TARGET DE LA CAMPANIA *******************
                // selecciono los paises de la campania
                $paisescampania = $this->ci->campaniaspaises->get_paises_by_campania($id_campania);
                $paises = NULL;

                if ($paisescampania) {
                    foreach ($paisescampania as $pais) {
                        $tmp_pais = new stdClass();
                        $tmp_pais->country = $pais->id_pais;

                        $paises[] = $tmp_pais;
                    }
                }

                // selecciono las audiencias de la campania
                if ($campania_de == 'adtk') {
                    $audienciascampania = $this->ci->campanias->get_audiencias_by_campania($id_campania);
                    $audiencias = NULL;

                    if ($audienciascampania) {
                        foreach ($audienciascampania as $audiencia) {
                            $tmp_audiencia = new stdClass();
                            $tmp_audiencia->id = (int) $audiencia->id_appnexus;
                            $tmp_audiencia->action = $audiencia->action;

                            $audiencias[] = $tmp_audiencia;
                        }

                        $segment_group_target[0] = new stdClass();
                        $segment_group_target[0]->boolean_operator = 'and';
                        $segment_group_target[0]->segments = $audiencias;
                    }
                }


                unset($data);
                $data = new stdClass();
                $data->profile = new stdClass();
                $data->profile->country_action = 'include';
                $data->profile->country_targets = $paises;

                if ($campania_de == 'adtk') {
                    if ($audienciascampania) {
                        $data->profile->segment_boolean_operator = 'or';
                        $data->profile->segment_group_targets = $segment_group_target;
                    }
                }

                $data->profile->allow_unaudited = true;

                $request = new Request();
                $request->method = 'post';
                $request->uri = BASE_URI . '/profile?advertiser_id=' . $anunciante->id_appnexus;
                $request->token = $this->ci->token;
                $request->data = $data;
                $res = Caller::call($request);

                if ($res->response->status == 'OK') {
                    $profile_id = $res->response->id;
                } else {
                    return FALSE;
                }

                // CREO LA CAMPANIA PARA EL LINE ITEM CREADO ***********************
                unset($data);
                $data = new stdClass();
                $data->campaign = new stdClass();
                $data->campaign->name = 'Direct';
                $data->campaign->advertiser_id = $anunciante->id_appnexus;
                $data->campaign->line_item_id = $lineItem_id;
                $data->campaign->inventory_type = 'direct';
                $data->campaign->profile_id = $profile_id;

                $request = new Request();
                $request->method = 'post';
                $request->uri = BASE_URI . '/campaign?advertiser_id=' . $anunciante->id_appnexus;
                $request->token = $this->ci->token;
                $request->data = $data;
                $res = Caller::call($request);

                if ($res->response->status == 'OK') {
                    $campaign_id = $res->response->id;
                } else {
                    return FALSE;
                }
            }

            $update = array(
                'id_lineItem_appnexus' => $lineItem_id
            );

            $this->ci->campanias->update_campania($id_campania, $update);

            return TRUE;
        } catch (Exception $e) {
            print "ERROR: " . $e->getMessage() . "\n";
            return FALSE;
        }
    }

    function crear_campania_dfp($id_campania) {
        try {
            // preparo los datos para la creacion
            $campania = $this->ci->campanias->get_campania_by_id($id_campania);
            if (!$campania)
                return FALSE;

            $anunciantes_adserver = $this->ci->users->get_anunciantes_app_by_id($this->ci->tank_auth->get_user_id());

            if (sizeof($anunciantes_adserver > 1)) {
                $anunciante = $this->ci->anunciantes->get_anunciante_adserver_by_id($campania->id_anunciante);

                $nombre = $anunciante->nombre;

                if (!$anunciante)
                    return FALSE;
            }else {
                $anunciante = $this->ci->anunciantes->get_anunciante_by_id($campania->id_anunciante);

                if (!$anunciante)
                    return FALSE;

                if ($anunciante->empresa) {
                    $nombre = $anunciante->name . ' (' . $anunciante->empresa . ')';
                } else {
                    $nombre = $anunciante->name;
                }

                $anunciante = $this->ci->anunciantes->get_anunciante_adserver_by_nombre($nombre);
            }

            if (!$anunciante)
                return FALSE;

            $formatosdfp = $this->ci->formatosdfp->get_all_formats();
            foreach ($formatosdfp as $value)
                $formatos_dfp[$value->id] = $value;

            $formatoscampania = $this->ci->campaniasformatos->get_formatos_by_campania($campania->id);
            foreach ($formatoscampania as $formatocampania)
                $formatos[$formatocampania->id_modalidad_compra][$formatocampania->monto][] = $formatocampania;

            $paisesdfp = $this->ci->paises->get_paises();
            foreach ($paisesdfp as $value)
                $paises_dfp[$value->id] = $value;

            $paisescampania = $this->ci->campaniaspaises->get_paises_by_campania($campania->id);

            $sitiosdfp = $this->ci->sites->get_all_sites();
            foreach ($sitiosdfp as $value) {
                if ($value->id_adunit_site)
                    $sitios_dfp[$value->id] = $value;
            }

            $sitioscampania = $this->ci->campaniassitios->get_sitios_by_campania($campania->id);

            $canalesdfp = $this->ci->categorias->get_categorias();
            foreach ($canalesdfp as $value) {
                if ($value->id_dfp)
                    $canales_dfp[$value->id] = $value;
            }

            $canales_campania = $this->ci->campaniascanalestematicos->get_canales_tematicos_by_campania($campania->id);

            $user = $this->getDFPUser();

            // Creo una orden para dicha campania
            $orderService = $user->GetOrderService('v201208');

            $order = new Order();
            $order->name = $campania->nombre . '_' . $campania->id;
            $order->advertiserId = $anunciante->id_dfp;
            $order->traffickerId = 80227591;
            $order->status = 'PENDING_APPROVAL';

            $orden = $orderService->createOrder($order);

            // creo los lineitems correspondietes para la campania creada anteriormente.
            $lineItemService = $user->GetService('LineItemService', 'v201208');


            if ($campania->segmentacion_id == 1) { // toda la red
                // asigno el target (paises y sitios a los que va dirigida la campania)
                $inventoryTargeting = new InventoryTargeting();

                // Paises en los cuales debe correr la campania
                $geoTargeting = new GeoTargeting();

                foreach ($paisescampania as $pais) {
                    $countryLocation = new DfpLocation();
                    $countryLocation->id = $pais->id_dfp;

                    $countryLocations[] = $countryLocation;
                }

                $geoTargeting->targetedLocations = $countryLocations;


                // Canales tematicos no excluidos
                foreach ($canales_dfp as $canal)
                    $targetedPlacements[] = $canal->id_dfp;

                $inventoryTargeting->targetedPlacementIds = $targetedPlacements;


                if ($campania->tipo_campania == 'impresiones_mobile') {
                    $technologyTargeting = new TechnologyTargeting();

                    $deviceCategoryTargeting = new DeviceCategoryTargeting();

                    $deviceCategoryTechnology = new Technology();
                    $deviceCategoryTechnology->id = 30000;

                    $deviceCategoryTargeting->excludedDeviceCategories = array($deviceCategoryTechnology);

                    $technologyTargeting->deviceCategoryTargeting = $deviceCategoryTargeting;
                }

                $targeting = new Targeting();
                $targeting->inventoryTargeting = $inventoryTargeting;
                $targeting->geoTargeting = $geoTargeting;

                if ($campania->tipo_campania == 'impresiones_mobile')
                    $targeting->technologyTargeting = $technologyTargeting;


                foreach ($formatos as $modalidad) {
                    foreach ($modalidad as $precio) {
                        // por cada modalidad de compra (CPC - CPM) y cada precio dentro de esa modalidad creo un LineItem,
                        // los formatos estan dentro de los precios, sumo las cantidades de impresiones/clicks

                        $lineItem = new LineItem();

                        $lineItem->orderId = $orden->id;
                        //$lineItem->name = 'Toda la red (' . strtoupper($precio[0]->id_modalidad_compra) . ' - ' . $precio[0]->monto . ')';
                        $lineItem->name = 'Toda la red';
                        $lineItem->disableSameAdvertiserCompetitiveExclusion = TRUE;

                        $impresiones_clicks = 0;

                        unset($creativePlaceholders);

                        foreach ($precio as $formato_campania) {
                            if ($formato_campania->id_formato == FORMATO_L) {
                                $width = $formatos_dfp[FORMATO_L]->width;
                                $height = $formatos_dfp[FORMATO_L]->height;
                                $sizeType = 'INTERSTITIAL';
                            } else if ($formato_campania->id_formato == FORMATO_S) {
                                $width = $formatos_dfp[FORMATO_S]->width;
                                $height = $formatos_dfp[FORMATO_S]->height;
                                $sizeType = 'INTERSTITIAL';
                            } else if ($formato_campania->id_formato == FORMATO_LS) {
                                $width = $formatos_dfp[FORMATO_LS]->width;
                                $height = $formatos_dfp[FORMATO_LS]->height;
                                $sizeType = 'INTERSTITIAL';
                            } else if ($formato_campania->id_formato == FORMATO_VIDEO_ZOCALO) {
                                $width = 300;
                                $height = 250;
                                $sizeType = 'PIXEL';
                            } else if ($formato_campania->id_formato == FORMATO_VIDEO_VIRAL) {
                                $width = 300;
                                $height = 250;
                                $sizeType = 'PIXEL';
                            } else {
                                $width = $formatos_dfp[$formato_campania->id_formato]->width;
                                $height = $formatos_dfp[$formato_campania->id_formato]->height;
                                $sizeType = 'PIXEL';
                            }

                            if ($formatos_dfp[$formato_campania->id_formato]->tipo == 4)
                                $lineItem->environmentType = 'VIDEO_PLAYER';

                            $creativePlaceholder = new CreativePlaceholder();
                            $creativePlaceholder->size = new Size($width, $height, FALSE);
                            $creativePlaceholder->creativeSizeType = $sizeType;
                            $creativePlaceholders[] = $creativePlaceholder;

                            $impresiones_clicks += $formato_campania->cantidad;
                        }
                        $lineItem->creativePlaceholders = $creativePlaceholders;


                        $fecha = explode(' ', $campania->fecha_inicio);
                        if ($fecha[0] == date('Y-m-d')) {
                            $hora = date('H');
                            $minuto = date('i');
                            $segundos = '00';

                            $fecha_inicio = date('Y-m-d') . ' ' . $hora . ':' . $minuto . ':' . $segundos;
                        } else {
                            $fecha_inicio = $campania->fecha_inicio;
                        }


                        if ($campania->tipo_campania == 'data') {
                            $lineItem->lineItemType = 'SPONSORSHIP';
                            $lineItem->startDateTime = DateTimeUtils::GetDfpDateTime(new DateTime($fecha_inicio));
                            $lineItem->endDateTime = DateTimeUtils::GetDfpDateTime(new DateTime($campania->fecha_fin));

                            $lineItem->deliveryRateType = 'AS_FAST_AS_POSSIBLE';
                            $lineItem->creativeRotationType = 'OPTIMIZED';
                        } else {
                            if ($campania->type_DFP == 'PRICE_PRIORITY') {
                                $lineItem->lineItemType = 'PRICE_PRIORITY';
                                $lineItem->startDateTime = DateTimeUtils::GetDfpDateTime(new DateTime($fecha_inicio));
                                $lineItem->endDateTime = DateTimeUtils::GetDfpDateTime(new DateTime($campania->fecha_fin));
                                $lineItem->duration = 'DAILY';

                                $lineItem->deliveryRateType = 'AS_FAST_AS_POSSIBLE';
                                $lineItem->creativeRotationType = 'OPTIMIZED';
                            } else if ($campania->type_DFP == 'STANDARD') {
                                $lineItem->lineItemType = 'STANDARD';
                                $lineItem->startDateTime = DateTimeUtils::GetDfpDateTime(new DateTime($fecha_inicio));
                                $lineItem->endDateTime = DateTimeUtils::GetDfpDateTime(new DateTime($campania->fecha_fin));

                                $lineItem->deliveryRateType = 'EVENLY';
                                $lineItem->creativeRotationType = 'OPTIMIZED';
                            }
                        }

                        if ($campania->modalidad_compra == 'cpm') {
                            $lineItem->costType = 'CPM';
                            $lineItem->unitType = 'IMPRESSIONS';
                        } else if ($campania->modalidad_compra == 'cpc') {
                            $lineItem->costType = 'CPC';
                            $lineItem->unitType = 'CLICKS';
                        } else if ($campania->modalidad_compra == 'cpv') {
                            $lineItem->costType = 'CPM';
                            $lineItem->unitType = 'IMPRESSIONS';
                        } else {
                            $lineItem->costType = 'CPM';
                            $lineItem->unitType = 'IMPRESSIONS';
                        }

                        $impresiones_clicks = $campania->cantidad;

                        $monto = $campania->valor_unidad - (($campania->comision * $campania->valor_unidad) / 100);
                        $monto = $monto - (($campania->descuento * $campania->valor_unidad) / 100);

                        if ($campania->modalidad_compra == 'cpv') {
                            $monto = ($impresiones_clicks * $monto) / (($impresiones_clicks * 2) / 1000);
                            $impresiones_clicks = $impresiones_clicks * 2;
                        }

                        // paso la moneda segun la cotizacion del dia.
                        if ($campania->moneda != 'USD') {
                            // averiguo la cotizacion del dolar en base a la moneda seleccionada al crear la campana.
                            $cotizacion = $this->ci->cotizaciones_diarias->get_cotizacion_today($campania->moneda);
                            if ($cotizacion) {
                                $cotizacion = (float) $cotizacion->amount;

                                if ($this->ci->user_data->moneda == 'ARS') {
                                    $cambio_fijo = $this->ci->constants->get_constant_by_id(CAMBIO_FIJO_ARGENTINA);
                                } else {
                                    $cambio_fijo = $this->ci->constants->get_constant_by_id(CAMBIO_FIJO_MEXICO);
                                }

                                $cambio_fijo = (float) $cambio_fijo->value;

                                if ($cambio_fijo > $cotizacion)
                                    $cotizacion = $cambio_fijo;

                                $monto = $monto / $cotizacion;
                            } else {
                                return FALSE;
                            }
                        }

                        $monto = $monto * 1000000;

                        $lineItem->costPerUnit = new Money('USD', $monto);
                        $lineItem->unitsBought = round($impresiones_clicks);

                        $lineItem->targeting = $targeting;

                        // frecuencia
                        if ($campania->frecuencia == '1x24') {
                            $frecuencia = new FrequencyCap(1, 1, 'DAY');
                        } else if ($campania->frecuencia == '2x24') {
                            $frecuencia = new FrequencyCap(2, 1, 'DAY');
                        }

                        if ($campania->frecuencia != 'NORMAL')
                            $lineItem->frequencyCaps = $frecuencia;

                        if ($campania->tipo_campania == 'impresiones_mobile')
                            $lineItem->targetPlatform = 'MOBILE';

                        $lineItems[] = $lineItem;
                    }
                }
            }

            if ($campania->segmentacion_id == 2) { // canales tematicos
                // asigno el target (paises y sitios a los que va dirigida la campania)
                $inventoryTargeting = new InventoryTargeting();

                // Paises en los cuales debe correr la campania
                $geoTargeting = new GeoTargeting();

                foreach ($paisescampania as $pais) {
                    $countryLocation = new DfpLocation();
                    $countryLocation->id = $pais->id_dfp;

                    $countryLocations[] = $countryLocation;
                }

                $geoTargeting->targetedLocations = $countryLocations;

                if ($campania->tipo_campania == 'impresiones_mobile') {
                    $technologyTargeting = new TechnologyTargeting();

                    $deviceCategoryTargeting = new DeviceCategoryTargeting();

                    $deviceCategoryTechnology = new Technology();
                    $deviceCategoryTechnology->id = 30000;

                    $deviceCategoryTargeting->excludedDeviceCategories = array($deviceCategoryTechnology);

                    $technologyTargeting->deviceCategoryTargeting = $deviceCategoryTargeting;
                }

                $conteo = $cantidad_total = 0;

                // Canales tematicos en los cuales debe correr la campania
                foreach ($canales_campania as $campania_canal) {
                    if (isset($canales_dfp[$campania_canal->id_canal_tematico])) {

                        $conteo++;

                        unset($targetedPlacements);

                        $targetedPlacements[] = $canales_dfp[$campania_canal->id_canal_tematico]->id_dfp;

                        $inventoryTargeting = new InventoryTargeting();
                        $inventoryTargeting->targetedPlacementIds = $targetedPlacements;


                        $targeting = new Targeting();
                        $targeting->inventoryTargeting = $inventoryTargeting;
                        $targeting->geoTargeting = $geoTargeting;

                        if ($campania->tipo_campania == 'impresiones_mobile')
                            $targeting->technologyTargeting = $technologyTargeting;


                        foreach ($formatos as $modalidad) {
                            foreach ($modalidad as $precio) {
                                // por cada modalidad de compra (CPC - CPM) y cada precio dentro de esa modalidad creo un LineItem,
                                // los formatos estan dentro de los precios, sumo las cantidades de impresiones/clicks

                                $lineItem = new LineItem();

                                $lineItem->orderId = $orden->id;
                                //$lineItem->name = 'Canales temáticos (' . $precio[0]->id_modalidad_compra . ' - ' . $precio[0]->monto . ')';
                                $lineItem->name = $canales_dfp[$campania_canal->id_canal_tematico]->nombre;
                                $lineItem->disableSameAdvertiserCompetitiveExclusion = TRUE;

                                $impresiones_clicks = 0;

                                unset($creativePlaceholders);

                                foreach ($precio as $formato_campania) {
                                    if ($formato_campania->id_formato == FORMATO_L) {
                                        $width = $formatos_dfp[FORMATO_L]->width;
                                        $height = $formatos_dfp[FORMATO_L]->height;
                                        $sizeType = 'INTERSTITIAL';
                                    } else if ($formato_campania->id_formato == FORMATO_S) {
                                        $width = $formatos_dfp[FORMATO_S]->width;
                                        $height = $formatos_dfp[FORMATO_S]->height;
                                        $sizeType = 'INTERSTITIAL';
                                    } else if ($formato_campania->id_formato == FORMATO_LS) {
                                        $width = $formatos_dfp[FORMATO_LS]->width;
                                        $height = $formatos_dfp[FORMATO_LS]->height;
                                        $sizeType = 'INTERSTITIAL';
                                    } else {
                                        $width = $formatos_dfp[$formato_campania->id_formato]->width;
                                        $height = $formatos_dfp[$formato_campania->id_formato]->height;
                                        $sizeType = 'PIXEL';
                                    }

                                    if ($formatos_dfp[$formato_campania->id_formato]->tipo == 4)
                                        $lineItem->environmentType = 'VIDEO_PLAYER';

                                    $creativePlaceholder = new CreativePlaceholder();
                                    $creativePlaceholder->size = new Size($width, $height, FALSE);
                                    $creativePlaceholder->creativeSizeType = $sizeType;
                                    $creativePlaceholders[] = $creativePlaceholder;

                                    $impresiones_clicks += $formato_campania->cantidad;
                                }
                                $lineItem->creativePlaceholders = $creativePlaceholders;

                                $fecha = explode(' ', $campania->fecha_inicio);
                                if ($fecha[0] == date('Y-m-d')) {
                                    $hora = date('H');
                                    $minuto = date('i');
                                    $segundos = '00';

                                    $fecha_inicio = date('Y-m-d') . ' ' . $hora . ':' . $minuto . ':' . $segundos;
                                } else {
                                    $fecha_inicio = $campania->fecha_inicio;
                                }

                                if ($campania->tipo_campania == 'data') {
                                    $lineItem->lineItemType = 'SPONSORSHIP';
                                    $lineItem->startDateTime = DateTimeUtils::GetDfpDateTime(new DateTime($fecha_inicio));
                                    $lineItem->endDateTime = DateTimeUtils::GetDfpDateTime(new DateTime($campania->fecha_fin));

                                    $lineItem->deliveryRateType = 'AS_FAST_AS_POSSIBLE';
                                    $lineItem->creativeRotationType = 'OPTIMIZED';
                                } else {
                                    if ($campania->type_DFP == 'PRICE_PRIORITY') {
                                        $lineItem->lineItemType = 'PRICE_PRIORITY';
                                        $lineItem->startDateTime = DateTimeUtils::GetDfpDateTime(new DateTime($fecha_inicio));
                                        $lineItem->endDateTime = DateTimeUtils::GetDfpDateTime(new DateTime($campania->fecha_fin));
                                        $lineItem->duration = 'DAILY';

                                        $lineItem->deliveryRateType = 'AS_FAST_AS_POSSIBLE';
                                        $lineItem->creativeRotationType = 'OPTIMIZED';
                                    } else if ($campania->type_DFP == 'STANDARD') {
                                        $lineItem->lineItemType = 'STANDARD';
                                        $lineItem->startDateTime = DateTimeUtils::GetDfpDateTime(new DateTime($fecha_inicio));
                                        $lineItem->endDateTime = DateTimeUtils::GetDfpDateTime(new DateTime($campania->fecha_fin));

                                        $lineItem->deliveryRateType = 'EVENLY';
                                        $lineItem->creativeRotationType = 'OPTIMIZED';
                                    }
                                }


                                if ($campania->modalidad_compra == 'cpm') {
                                    $lineItem->costType = 'CPM';
                                    $lineItem->unitType = 'IMPRESSIONS';
                                } else if ($campania->modalidad_compra == 'cpc') {
                                    $lineItem->costType = 'CPC';
                                    $lineItem->unitType = 'CLICKS';
                                } else if ($campania->modalidad_compra == 'cpv') {
                                    $lineItem->costType = 'CPM';
                                    $lineItem->unitType = 'IMPRESSIONS';
                                } else {
                                    $lineItem->costType = 'CPM';
                                    $lineItem->unitType = 'IMPRESSIONS';
                                }

                                if ($conteo == sizeof($canales_campania)) {
                                    $impresiones_clicks = round($campania->cantidad - $cantidad_total);
                                } else {
                                    $impresiones_clicks = round($campania->cantidad / sizeof($canales_campania));
                                }

                                $cantidad_total += $impresiones_clicks;

                                /*
                                  // calculo la cantidad de clicks o impresiones que tiene que hacer por dia (total_impresiones_clicks / cantidad de dias).
                                  $monto = $precio[0]->monto - (($campania->comision * $precio[0]->monto) / 100);
                                  $monto = $monto - (($campania->descuento * $precio[0]->monto) / 100);
                                  $monto = $monto * 1000000;

                                  // paso la moneda segun la cotizacion del dia.
                                  if($campania->moneda != 'USD'){
                                  // averiguo la cotizacion del dolar en base a la moneda seleccionada al crear la campana.
                                  $cotizacion = $this->ci->cotizaciones_diarias->get_cotizacion_today($campania->moneda);
                                  if($cotizacion){
                                  if($monto >= $cotizacion->amount){
                                  $monto = $monto / $cotizacion->amount;
                                  }else{
                                  $monto = $cotizacion->amount / $monto;
                                  }
                                  }else{
                                  return FALSE;
                                  }
                                  }
                                 *
                                 */

                                $monto = $campania->valor_unidad - (($campania->comision * $campania->valor_unidad) / 100);
                                $monto = $monto - (($campania->descuento * $campania->valor_unidad) / 100);

                                if ($campania->modalidad_compra == 'cpv') {
                                    $monto = ($impresiones_clicks * $monto) / (($impresiones_clicks * 2) / 1000);
                                    $impresiones_clicks = $impresiones_clicks * 2;
                                }

                                // paso la moneda segun la cotizacion del dia.
                                if ($campania->moneda != 'USD') {
                                    // averiguo la cotizacion del dolar en base a la moneda seleccionada al crear la campana.
                                    $cotizacion = $this->ci->cotizaciones_diarias->get_cotizacion_today($campania->moneda);
                                    if ($cotizacion) {
                                        $cotizacion = (float) $cotizacion->amount;

                                        if ($this->ci->user_data->moneda == 'ARS') {
                                            $cambio_fijo = $this->ci->constants->get_constant_by_id(CAMBIO_FIJO_ARGENTINA);
                                        } else {
                                            $cambio_fijo = $this->ci->constants->get_constant_by_id(CAMBIO_FIJO_MEXICO);
                                        }

                                        $cambio_fijo = (float) $cambio_fijo->value;

                                        if ($cambio_fijo > $cotizacion)
                                            $cotizacion = $cambio_fijo;

                                        $monto = $monto / $cotizacion;
                                    } else {
                                        return FALSE;
                                    }
                                }

                                $monto = $monto * 1000000;

                                $lineItem->costPerUnit = new Money('USD', $monto);

                                if ($campania->tipo_campania == 'data') {
                                    $lineItem->unitsBought = 100;
                                } else {
                                    $lineItem->unitsBought = $impresiones_clicks;
                                }

                                $lineItem->targeting = $targeting;
                            }
                        }

                        // frecuencia
                        if ($campania->frecuencia == '1x24') {
                            $frecuencia = new FrequencyCap(1, 1, 'DAY');
                        } else if ($campania->frecuencia == '2x24') {
                            $frecuencia = new FrequencyCap(2, 1, 'DAY');
                        }

                        if ($campania->frecuencia != 'NORMAL')
                            $lineItem->frequencyCaps = $frecuencia;

                        if ($campania->tipo_campania == 'impresiones_mobile')
                            $lineItem->targetPlatform = 'MOBILE';

                        $lineItems[] = $lineItem;
                    }
                }
            }


            if ($campania->segmentacion_id == 3) { // sitios especificos
                // asigno el target (paises y sitios a los que va dirigida la campania)
                $inventoryTargeting = new InventoryTargeting();

                // Paises en los cuales debe correr la campania
                $geoTargeting = new GeoTargeting();

                foreach ($paisescampania as $pais) {
                    $countryLocation = new DfpLocation();
                    $countryLocation->id = $pais->id_dfp;

                    $countryLocations[] = $countryLocation;
                }

                $geoTargeting->targetedLocations = $countryLocations;


                // Sitios en los cuales debe correr la campania
                foreach ($sitioscampania as $campania_sitios) {
                    if (isset($sitios_dfp[$campania_sitios->id_sitio])) {
                        $targetedAdUnit = new AdUnitTargeting();
                        $targetedAdUnit->adUnitId = $sitios_dfp[$campania_sitios->id_sitio]->id_adunit_site;

                        $targetedAdUnits[] = $targetedAdUnit;
                    }
                }

                $inventoryTargeting = new InventoryTargeting();
                $inventoryTargeting->targetedAdUnits = $targetedAdUnits;

                if ($campania->tipo_campania == 'impresiones_mobile') {
                    $technologyTargeting = new TechnologyTargeting();

                    $deviceCategoryTargeting = new DeviceCategoryTargeting();

                    $deviceCategoryTechnology = new Technology();
                    $deviceCategoryTechnology->id = 30000;

                    $deviceCategoryTargeting->excludedDeviceCategories = array($deviceCategoryTechnology);

                    $technologyTargeting->deviceCategoryTargeting = $deviceCategoryTargeting;
                }


                $targeting = new Targeting();
                $targeting->inventoryTargeting = $inventoryTargeting;
                $targeting->geoTargeting = $geoTargeting;

                if ($campania->tipo_campania == 'impresiones_mobile')
                    $targeting->technologyTargeting = $technologyTargeting;


                foreach ($formatos as $modalidad) {
                    foreach ($modalidad as $precio) {
                        // por cada modalidad de compra (CPC - CPM) y cada precio dentro de esa modalidad creo un LineItem,
                        // los formatos estan dentro de los precios, sumo las cantidades de impresiones/clicks

                        $lineItem = new LineItem();

                        $lineItem->orderId = $orden->id;
                        $lineItem->name = 'Sitios específicos';
                        $lineItem->disableSameAdvertiserCompetitiveExclusion = TRUE;

                        $impresiones_clicks = 0;

                        unset($creativePlaceholders);

                        foreach ($precio as $formato_campania) {
                            if ($formato_campania->id_formato == FORMATO_L) {
                                $width = $formatos_dfp[FORMATO_L]->width;
                                $height = $formatos_dfp[FORMATO_L]->height;
                                $sizeType = 'INTERSTITIAL';
                            } else if ($formato_campania->id_formato == FORMATO_S) {
                                $width = $formatos_dfp[FORMATO_S]->width;
                                $height = $formatos_dfp[FORMATO_S]->height;
                                $sizeType = 'INTERSTITIAL';
                            } else if ($formato_campania->id_formato == FORMATO_LS) {
                                $width = $formatos_dfp[FORMATO_LS]->width;
                                $height = $formatos_dfp[FORMATO_LS]->height;
                                $sizeType = 'INTERSTITIAL';
                            } else {
                                $width = $formatos_dfp[$formato_campania->id_formato]->width;
                                $height = $formatos_dfp[$formato_campania->id_formato]->height;
                                $sizeType = 'PIXEL';
                            }

                            if ($formatos_dfp[$formato_campania->id_formato]->tipo == 4)
                                $lineItem->environmentType = 'VIDEO_PLAYER';

                            $creativePlaceholder = new CreativePlaceholder();
                            $creativePlaceholder->size = new Size($width, $height, FALSE);
                            $creativePlaceholder->creativeSizeType = $sizeType;
                            $creativePlaceholders[] = $creativePlaceholder;

                            $impresiones_clicks += $formato_campania->cantidad;
                        }
                        $lineItem->creativePlaceholders = $creativePlaceholders;

                        $fecha = explode(' ', $campania->fecha_inicio);
                        if ($fecha[0] == date('Y-m-d')) {
                            $hora = date('H');
                            $minuto = date('i');
                            $segundos = '00';

                            $fecha_inicio = date('Y-m-d') . ' ' . $hora . ':' . $minuto . ':' . $segundos;
                        } else {
                            $fecha_inicio = $campania->fecha_inicio;
                        }


                        if ($campania->tipo_campania == 'data') {
                            $lineItem->lineItemType = 'SPONSORSHIP';
                            $lineItem->startDateTime = DateTimeUtils::GetDfpDateTime(new DateTime($fecha_inicio));
                            $lineItem->endDateTime = DateTimeUtils::GetDfpDateTime(new DateTime($campania->fecha_fin));

                            $lineItem->deliveryRateType = 'AS_FAST_AS_POSSIBLE';
                            $lineItem->creativeRotationType = 'OPTIMIZED';
                        } else {
                            if ($campania->type_DFP == 'PRICE_PRIORITY') {
                                $lineItem->lineItemType = 'PRICE_PRIORITY';
                                $lineItem->startDateTime = DateTimeUtils::GetDfpDateTime(new DateTime($fecha_inicio));
                                $lineItem->endDateTime = DateTimeUtils::GetDfpDateTime(new DateTime($campania->fecha_fin));
                                $lineItem->duration = 'DAILY';

                                $lineItem->deliveryRateType = 'AS_FAST_AS_POSSIBLE';
                                $lineItem->creativeRotationType = 'OPTIMIZED';
                            } else if ($campania->type_DFP == 'STANDARD') {
                                $lineItem->lineItemType = 'STANDARD';
                                $lineItem->startDateTime = DateTimeUtils::GetDfpDateTime(new DateTime($fecha_inicio));
                                $lineItem->endDateTime = DateTimeUtils::GetDfpDateTime(new DateTime($campania->fecha_fin));

                                $lineItem->deliveryRateType = 'EVENLY';
                                $lineItem->creativeRotationType = 'OPTIMIZED';
                            }
                        }

                        if ($campania->modalidad_compra == 'cpm') {
                            $lineItem->costType = 'CPM';
                            $lineItem->unitType = 'IMPRESSIONS';
                        } else if ($campania->modalidad_compra == 'cpc') {
                            $lineItem->costType = 'CPC';
                            $lineItem->unitType = 'CLICKS';
                        } else if ($campania->modalidad_compra == 'cpv') {
                            $lineItem->costType = 'CPM';
                            $lineItem->unitType = 'IMPRESSIONS';
                        } else {
                            $lineItem->costType = 'CPM';
                            $lineItem->unitType = 'IMPRESSIONS';
                        }

                        $impresiones_clicks = $campania->cantidad;

                        /*
                          $monto = $precio[0]->monto - (($campania->comision * $precio[0]->monto) / 100);
                          $monto = $monto - (($campania->descuento * $precio[0]->monto) / 100);
                          $monto = $monto * 1000000;

                          // paso la moneda segun la cotizacion del dia.
                          if($campania->moneda != 'USD'){
                          // averiguo la cotizacion del dolar en base a la moneda seleccionada al crear la campana.
                          $cotizacion = $this->ci->cotizaciones_diarias->get_cotizacion_today($campania->moneda);
                          if($cotizacion){
                          if($monto >= $cotizacion->amount){
                          $monto = $monto / $cotizacion->amount;
                          }else{
                          $monto = $cotizacion->amount / $monto;
                          }
                          }else{
                          return FALSE;
                          }
                          }
                         *
                         */

                        $monto = $campania->valor_unidad - (($campania->comision * $campania->valor_unidad) / 100);
                        $monto = $monto - (($campania->descuento * $campania->valor_unidad) / 100);

                        if ($campania->modalidad_compra == 'cpv') {
                            $monto = ($impresiones_clicks * $monto) / (($impresiones_clicks * 2) / 1000);
                            $impresiones_clicks = $impresiones_clicks * 2;
                        }

                        // paso la moneda segun la cotizacion del dia.
                        if ($campania->moneda != 'USD') {
                            // averiguo la cotizacion del dolar en base a la moneda seleccionada al crear la campana.
                            $cotizacion = $this->ci->cotizaciones_diarias->get_cotizacion_today($campania->moneda);
                            if ($cotizacion) {
                                $cotizacion = (float) $cotizacion->amount;

                                if ($this->ci->user_data->moneda == 'ARS') {
                                    $cambio_fijo = $this->ci->constants->get_constant_by_id(CAMBIO_FIJO_ARGENTINA);
                                } else {
                                    $cambio_fijo = $this->ci->constants->get_constant_by_id(CAMBIO_FIJO_MEXICO);
                                }

                                $cambio_fijo = (float) $cambio_fijo->value;

                                if ($cambio_fijo > $cotizacion)
                                    $cotizacion = $cambio_fijo;

                                $monto = $monto / $cotizacion;
                            } else {
                                return FALSE;
                            }
                        }

                        $monto = $monto * 1000000;

                        $lineItem->costPerUnit = new Money('USD', $monto);
                        $lineItem->unitsBought = round($impresiones_clicks);

                        $lineItem->targeting = $targeting;

                        // frecuencia
                        if ($campania->frecuencia == '1x24') {
                            $frecuencia = new FrequencyCap(1, 1, 'DAY');
                        } else if ($campania->frecuencia == '2x24') {
                            $frecuencia = new FrequencyCap(2, 1, 'DAY');
                        }

                        if ($campania->frecuencia != 'NORMAL')
                            $lineItem->frequencyCaps = $frecuencia;

                        if ($campania->tipo_campania == 'impresiones_mobile')
                            $lineItem->targetPlatform = 'MOBILE';

                        $lineItems[] = $lineItem;
                    }
                }
            }

            $lineItems = $lineItemService->createLineItems($lineItems);

            $update = array(
                'id_orden_dfp' => $orden->id,
                'alta_DFP' => 1
            );

            $this->ci->campanias->update_campania($id_campania, $update);

            return TRUE;
        } catch (Exception $e) {
            print "ERROR: " . $e->getMessage() . "\n";
            return FALSE;
        }
    }

    function update($id_campania) {
        try {
            // preparo los datos para la creacion
            $campania = $this->ci->campanias->get_campania_by_id($id_campania);
            if (!$campania)
                return FALSE;

            $paisescampania = $this->ci->campaniaspaises->get_paises_by_campania($campania->id);

            //conexion con DFP
            $user = new DfpUser();

            $orderService = $user->GetOrderService('v201208');

            // cambio el nombre de la campania
            $orderService = $user->GetService('OrderService', 'v201208');

            $filterStatement = new Statement("WHERE id = " . $campania->id_orden_dfp . ' LIMIT 500');

            $page = $orderService->getOrdersByStatement($filterStatement);

            if (isset($page->results)) {
                $orders = $page->results;

                foreach ($orders as $order)
                    $order->name = $campania->nombre;

                $orderService->updateOrders($orders);
            }


            // fecha de inicio de la campania.
            $fecha = explode(' ', $campania->fecha_inicio);
            if ($fecha[0] == date('Y-m-d')) {
                $hora = date('H');
                $minuto = date('i');
                $segundos = '00';

                $fecha_inicio = date('Y-m-d') . ' ' . $hora . ':' . $minuto . ':' . $segundos;
            } else {
                $fecha_inicio = $campania->fecha_inicio;
            }

            $this->ci->load->model('campaniasformatos');

            // valores de compra y modalidad de compra
            $formatoscampania = $this->ci->campaniasformatos->get_formatos_by_campania($campania->id);
            $modalidad_de_compra = $formatoscampania[0]->id_modalidad_compra;
            if ($modalidad_de_compra == '')
                $modalidad_de_compra = 'cpm';

            $impresiones_clicks = $campania->cantidad;

            // calculo la cantidad de clicks o impresiones que tiene que hacer por dia (total_impresiones_clicks / cantidad de dias).
            /*
              $cant_dias = $this->_dateDiff($fecha_inicio, $campania->fecha_fin) + 1;
              $imps_clicks_por_dia = $impresiones_clicks / $cant_dias;
             */

            $monto = $formatoscampania[0]->monto - (($campania->comision * $formatoscampania[0]->monto) / 100);
            $monto = $monto - (($campania->descuento * $formatoscampania[0]->monto) / 100);
            $monto = $monto * 1000000;


            // cambio los datos de los LineItems
            $lineItemService = $user->GetService('LineItemService', 'v201208');

            $filterStatement = new Statement("WHERE orderId = " . $campania->id_orden_dfp . ' LIMIT 500');

            $page = $lineItemService->getLineItemsByStatement($filterStatement);

            if (isset($page->results)) {
                $lineItems = $page->results;

                array_filter($lineItems, create_function('$lineItem', 'return !$lineItem->isArchived;'));

                $geoTargeting = new GeoTargeting();
                foreach ($paisescampania as $pais) {
                    $countryLocation = new DfpLocation();
                    $countryLocation->id = $pais->id_dfp;

                    $countryLocations[] = $countryLocation;
                }
                $geoTargeting->targetedLocations = $countryLocations;

                // modifico todos los ListItem pertenecientes a la orden
                foreach ($lineItems as $lineItem) {
                    // fecha de inicio y fecha de fin
                    $lineItem->startDateTime = DateTimeUtils::GetDfpDateTime(new DateTime($fecha_inicio));
                    $lineItem->endDateTime = DateTimeUtils::GetDfpDateTime(new DateTime($campania->fecha_fin));

                    // modalidad de compra
                    if ($modalidad_de_compra == 'cpm') {
                        $lineItem->costType = 'CPM';
                        $lineItem->unitType = 'IMPRESSIONS';
                    } else if ($modalidad_de_compra == 'cpc') {
                        $lineItem->costType = 'CPC';
                        $lineItem->unitType = 'CLICKS';
                    }

                    // valor de compra y cantidad
                    $lineItem->costPerUnit = new Money('USD', $monto);
                    $lineItem->unitsBought = round($impresiones_clicks);

                    // paises
                    $targeting = new Targeting();
                    $targeting->inventoryTargeting = $lineItem->targeting->inventoryTargeting;
                    $targeting->geoTargeting = $geoTargeting;

                    $lineItem->targeting = $targeting;
                }

                $lineItems = $lineItemService->updateLineItems($lineItems);

                if (isset($lineItems)) {

                } else {
                    print "No line items updated.\n";
                }
            } else {
                print "No line items found to update.\n";
            }
        } catch (Exception $e) {
            print "ERROR: " . $e->getMessage() . "\n";
            return FALSE;
        }
    }

    function _dateDiff($start, $end) {
        $start_ts = strtotime($start);
        $end_ts = strtotime($end);
        $diff = $end_ts - $start_ts;
        return round($diff / 86400);
    }

    function getDFPUser() {

        //Si no existe el objeto de Session DFPUser lo creo, si existe lo reutilizo
        if (!$this->ci->session->userdata('DfpUser')) {

            $user = new DfpUser();

            $session_data = array(
                'DfpUser' => $user
            );

            $this->ci->session->set_userdata($session_data);
        } else {
            $user = $this->ci->session->userdata('DfpUser');
        }

        return $user;
    }

    public function reportePorSitioApi($token, $filtro_categorias, $filtro_paises, $intervalo, $fecha_desde, $fecha_hasta) {
        $filtros = $this->filtrarSitiosParaReporte($filtro_categorias, $filtro_paises, $intervalo, $fecha_desde, $fecha_hasta);

        if (!$filtros)
            return FALSE;

        $reporte_eplanning = null;
        $reporte_appnexus = null;
        $reporte_dfp = null;

        //$reporte_eplanning = $this->obtenerReporte_eplanning('sitios', $filtros['fechas']['desde'], $filtros['fechas']['hasta'], $filtros['paises'], $filtros['sitios'], 0);
        //$reporte_appnexus = $this->obtenerReporte_appnexus($token, 'sitios', $filtros['fechas']['desde'], $filtros['fechas']['hasta'], $filtros['sitios_appnexus'], $filtros['paises'], 0);

        do {
            $reporte_appnexus = $this->obtenerReporte_appnexus($token, 'sitios', $filtros['fechas']['desde'], $filtros['fechas']['hasta'], $filtros['sitios_appnexus'], $filtros['paises'], 0);
        } while ($reporte_appnexus == false);

        $totales['imps'] = 0;

        foreach ($filtros['sitios'] as $sitio) {


            if ($reporte_appnexus) {
                foreach ($reporte_appnexus as $report_appnexus) {
                    if ($sitio->id_site) {
                        if ($report_appnexus['site_id'] == $sitio->id_site) {
                            if (isset($sitios_filtrados[$sitio->id])) {
                                $sitios_filtrados[$sitio->id]['imps'] += $report_appnexus['imps'] * $this->multiplicacion_volumen;
                            } else {
                                $sitios_filtrados[$sitio->id]['imps'] = $report_appnexus['imps'] * $this->multiplicacion_volumen;
                            }
                        }
                    }
                }
            }

            if (isset($sitios_filtrados[$sitio->id])) {
                $sitios_filtrados[$sitio->id]['id'] = $sitio->id;

                $sitios_filtrados[$sitio->id]['url_sitio'] = $sitio->nombre_appnexus;
                if ($sitios_filtrados[$sitio->id]['url_sitio'] == '')
                    $sitios_filtrados[$sitio->id]['url_sitio'] = $sitio->nombre_dfp;

                // Personalizo los nombres para GLAM
                $nombre_glam = $this->reemplazarNombresGlam($sitio->id);
                if ($nombre_glam)
                    $sitios_filtrados[$sitio->id]['url_sitio'] = $nombre_glam;

                $sitios_filtrados[$sitio->id]['url_sitio'] = str_replace("http://", "", $sitios_filtrados[$sitio->id]['url_sitio']);
                $sitios_filtrados[$sitio->id]['url_sitio'] = str_replace("/", "", $sitios_filtrados[$sitio->id]['url_sitio']);

                $sitios_filtrados[$sitio->id]['categorias'] = $this->_categoriasPorSitio($sitio->id);
                if ($sitios_filtrados[$sitio->id]['categorias'] == '')
                    $sitios_filtrados[$sitio->id]['categorias'] = '-';

                $totales['imps'] += $sitios_filtrados[$sitio->id]['imps'];

                $sitios_filtrados[$sitio->id]['imps'] = number_format($sitios_filtrados[$sitio->id]['imps'], 0, ',', '.');
            }
        }

        $totales['imps'] = number_format($totales['imps'], 0, ',', '.');

        $data['totales'] = $totales;

        if (!isset($sitios_filtrados))
            return FALSE;

        $data['sitios'] = $sitios_filtrados;

        return $data;

        /*
          do {
          $reporte_dfp = $this->obtenerReporte_DFP('sitios', $filtros['fechas']['desde'], $filtros['fechas']['hasta'], $filtros['sitios'], $filtros['paises_dfp'], 0);
          } while ($reporte_dfp == false);

          $totales['imps'] = 0;

          foreach ($filtros['sitios'] as $sitio) {


          if ($reporte_dfp) {
          foreach ($reporte_dfp as $report_dfp) {
          if ($sitio->id_adunit_site) {
          if ($report_dfp['site_id'] == $sitio->id_adunit_site) {
          if (isset($sitios_filtrados[$sitio->id])) {
          $sitios_filtrados[$sitio->id]['imps'] += $report_dfp['imps'] * $this->multiplicacion_volumen;
          } else {
          $sitios_filtrados[$sitio->id]['imps'] = $report_dfp['imps'] * $this->multiplicacion_volumen;
          }
          }
          }
          }
          }

          if (isset($sitios_filtrados[$sitio->id])) {
          $sitios_filtrados[$sitio->id]['id'] = $sitio->id;

          $sitios_filtrados[$sitio->id]['url_sitio'] = $sitio->nombre_appnexus;
          if ($sitios_filtrados[$sitio->id]['url_sitio'] == '')
          $sitios_filtrados[$sitio->id]['url_sitio'] = $sitio->nombre_dfp;

          // Personalizo los nombres para GLAM
          $nombre_glam = $this->reemplazarNombresGlam($sitio->id);
          if ($nombre_glam)
          $sitios_filtrados[$sitio->id]['url_sitio'] = $nombre_glam;

          $sitios_filtrados[$sitio->id]['url_sitio'] = str_replace("http://", "", $sitios_filtrados[$sitio->id]['url_sitio']);
          $sitios_filtrados[$sitio->id]['url_sitio'] = str_replace("/", "", $sitios_filtrados[$sitio->id]['url_sitio']);

          $sitios_filtrados[$sitio->id]['categorias'] = $this->_categoriasPorSitio($sitio->id);
          if ($sitios_filtrados[$sitio->id]['categorias'] == '')
          $sitios_filtrados[$sitio->id]['categorias'] = '-';

          $totales['imps'] += $sitios_filtrados[$sitio->id]['imps'];

          $sitios_filtrados[$sitio->id]['imps'] = number_format($sitios_filtrados[$sitio->id]['imps'], 0, ',', '.');
          }
          }

          $totales['imps'] = number_format($totales['imps'], 0, ',', '.');

          $data['totales'] = $totales;

          if (!isset($sitios_filtrados))
          return FALSE;

          $data['sitios'] = $sitios_filtrados;

          return $data;
         *
         */
    }

    function obtenerReporte_DFP($tipo = 'sitios', $start_date, $end_date, $filtro_sitios, $filtro_paises, $impresiones_minimas) {

        $max_por_reporte = 3000;

        foreach ($filtro_sitios as $id_sitio) {
            // traigo todos los espacios del sitio
            $espacios = $this->ci->placements->get_placement_by_id_sitio($id_sitio->id);

            if ($espacios) {
                foreach ($espacios as $espacio) {
                    if ($espacio->id_espacio_dfp) {
                        $arr_espacios[] = $espacio->id_espacio_dfp;

                        $this->ids_tamanios[$espacio->id_espacio_dfp] = 0;
                        if ($espacio->id_tamanio)
                            $this->ids_tamanios[$espacio->id_espacio_dfp] = $espacio->id_tamanio;
                    }
                }
            }
        }

        $cant_vueltas = round(sizeof($arr_espacios) / $max_por_reporte) + 1;

        for ($a = 0; $a < $cant_vueltas; $a++) {
            $espacios_str = '';
            $desde = $a * $max_por_reporte;
            $hasta = (($a + 1) * $max_por_reporte) - 1;

            for ($b = $desde; $b < $hasta; $b++) {
                if (isset($arr_espacios[$b]))
                    $espacios_str .= $arr_espacios[$b] . ',';
            }

            $espacios_str = trim($espacios_str, ',');
            echo date("h:i:s") . "\n";
            $res_reporte_dfp = $this->reporte_DFP($espacios_str, $start_date, $end_date);
            while ($res_reporte_dfp === "error_api") {
                $res_reporte_dfp = $this->reporte_DFP($espacios_str, $start_date, $end_date);
            }
        }

        if (is_array($this->resultado_DFP)) {
            $total_impresiones = 0;
            try {
                foreach ($this->resultado_DFP as $results) {
                    if ($results >= $impresiones_minimas && in_array($results['pais_id'], $filtro_paises)) {
                        // consulto si el espacio es un layer-skin y si es lo sumo a imps_1x1
                        if ($results['espacio_id'] && isset($this->ids_tamanios[$results['espacio_id']])) {
                            if ($this->ids_tamanios[$results['espacio_id']] == 9)
                                $results['imps_1x1'] = $results['imps'];

                            $resultados[] = $results;
                            $total_impresiones += $results['imps'];
                        }
                    }
                }
            } catch (Exception $ex) {
                echo $ex->getMessage();
            }

            //echo "Total impresiones DFP: $total_impresiones <br>";

            return $resultados;
        } else {
            return false;
        }
    }

    function reporte_DFP($espacios_str, $start_date, $end_date) {
        try {
            if ($espacios_str != '') {
                $user = $this->getDFPUser();

                $reportService = $user->GetService('ReportService', 'v201208');

                $filterStatement = new Statement();

                $reportJob = new ReportJob();
                $reportQuery = new ReportQuery();

                $reportQuery->dateRangeType = "CUSTOM_DATE";

                $start_date = explode(" ", $start_date);
                $end_date = explode(" ", $end_date);

                list($dia_desde, $mes_desde, $anio_desde) = explode("-", $start_date[0]);
                list($dia_hasta, $mes_hasta, $anio_hasta) = explode("-", $end_date[0]);

                $start_date = new Date($dia_desde, $mes_desde, $anio_desde);
                $end_date = new Date($dia_hasta, $mes_hasta, $anio_hasta);

                $reportQuery->startDate = $start_date;
                $reportQuery->endDate = $end_date;

                $reportQuery->dimensions = array('AD_UNIT_NAME', 'COUNTRY_NAME');

                $reportQuery->columns = array(
                    'TOTAL_INVENTORY_LEVEL_IMPRESSIONS',
                    'AD_SERVER_IMPRESSIONS',
                    'AD_EXCHANGE_LINE_ITEM_LEVEL_IMPRESSIONS',
                    'TOTAL_INVENTORY_LEVEL_UNFILLED_IMPRESSIONS',
                    'TOTAL_INVENTORY_LEVEL_CLICKS',
                    'TOTAL_INVENTORY_LEVEL_CTR'
                );

                $reportQuery->adUnitView = 'HIERARCHICAL';
                $reportJob->reportQuery = $reportQuery;

                $filterStatement->query = "WHERE AD_UNIT_ID in ($espacios_str)";
                $reportQuery->statement = $filterStatement;

                $reportJob = $reportService->runReportJob($reportJob);

                $intentos = 0;

                do {
                    try {
                        $intentos++;
                        if ($intentos == 20)
                            die();
                        $error_reporte = false;
                        $reportJob = $reportService->getReportJob($reportJob->id);
                    } catch (Exception $ex) {
                        //print "error: " . $ex->getCode() . " - " . $ex->getMessage() . "<br/><br/>";
                        if ($ex->getMessage() == "[QuotaError.EXCEEDED_QUOTA @ ]") {
                            sleep(2);
                        }
                        $error_reporte = true;
                    }
                } while ($reportJob->reportJobStatus == 'IN_PROGRESS' || $error_reporte == true);

                if ($reportJob->reportJobStatus == 'FAILED') {
                    return false;
                } else {
                    $reportJobId = $reportJob->id;
                    $fileName = 'report' . rand(100, 99999) . '.txt.gz';

                    $filePath = dirname(__FILE__) . '/tmp/' . $fileName;

                    $downloadUrl = $reportService->getReportDownloadURL($reportJobId, 'TSV');

                    ReportUtils::DownloadReport($downloadUrl, $filePath);

                    $lineas = gzfile($filePath);

                    if (sizeof($lineas) > 2) {
                        $cont = 0;
                        $totales = 0;
                        foreach ($lineas as $linea) {

                            if ($cont >= 1) {
                                $campos = explode("\t", $linea);

                                $resultado['publisher_name'] = trim($campos[0]);
                                $resultado['publisher_name'] = explode('_', $resultado['publisher_name']);
                                $resultado['publisher_name'] = $resultado['publisher_name'][0];

                                $resultado['site_name'] = trim($campos[1]);
                                $resultado['site_name'] = explode('_', $resultado['site_name']);
                                $resultado['site_name'] = $resultado['site_name'][0];

                                $resultado['espacio_name'] = trim($campos[2]);
                                $resultado['espacio_name'] = explode('_', $resultado['espacio_name']);
                                $resultado['espacio_name'] = $resultado['espacio_name'][0];

                                $resultado['pais_name'] = trim($campos[3]);
                                $resultado['publisher_id'] = trim($campos[4]);
                                $resultado['site_id'] = trim($campos[5]);
                                $resultado['espacio_id'] = trim($campos[6]);
                                $resultado['pais_id'] = trim($campos[7]);
                                $resultado['id_tamanio'] = 0;

                                $resultado['imps'] = trim($campos[12]);
                                $resultado['imps'] = str_replace('"', '', $resultado['imps']);
                                $resultado['imps'] = str_replace(',', '', $resultado['imps']);

                                $resultado['imps_kept'] = trim($campos[8]);
                                $resultado['imps_kept'] = str_replace('"', '', $resultado['imps_kept']);
                                $resultado['imps_kept'] = str_replace(',', '', $resultado['imps_kept']);

                                $resultado['imps_resold'] = trim($campos[13]);
                                $resultado['imps_resold'] = str_replace('"', '', $resultado['imps_resold']);
                                $resultado['imps_resold'] = str_replace(',', '', $resultado['imps_resold']);

                                $resultado['imps_default'] = 0;

                                $resultado['imps_psa'] = trim($campos[10]);
                                $resultado['imps_psa'] = str_replace('"', '', $resultado['imps_psa']);
                                $resultado['imps_psa'] = str_replace(',', '', $resultado['imps_psa']);

                                $resultado['imps_1x1'] = 0;

                                $resultado['clicks'] = trim($campos[11]);
                                $resultado['clicks'] = str_replace('"', '', $resultado['clicks']);
                                $resultado['clicks'] = str_replace(',', '', $resultado['clicks']);

                                $resultado['ctr'] = trim($campos[9]);
                                $resultado['ctr'] = str_replace('%', '', $resultado['ctr']);

                                $this->resultado_DFP[] = $resultado;
                            }
                            $cont++;
                        }
                        return true;
                    }
                    return true;
                }
                return true;
            } else {
                return false;
            }
        } catch (Exception $ex) {
            //print $ex->getCode()." - ".$ex->getMessage() . "<br/><br/>";
            //var_dump($ex);
            if ($ex->getMessage() == "[QuotaError.EXCEEDED_QUOTA @ ]") {
                sleep(2);
            }
            return "error_api";
        }
    }

    public function reportePorCategoriaApi($token, $filtro_categorias, $filtro_paises, $intervalo, $fecha_desde, $fecha_hasta) {
        $filtros = $this->filtrarSitiosParaReporte($filtro_categorias, $filtro_paises, $intervalo, $fecha_desde, $fecha_hasta);

        if (!$filtros)
            return FALSE;

        $reporte_eplanning = null;
        $reporte_appnexus = null;
        $reporte_dfp = null;

        do {
            $reporte_appnexus = $this->obtenerReporte_appnexus($token, 'sitios', $filtros['fechas']['desde'], $filtros['fechas']['hasta'], $filtros['sitios_appnexus'], $filtros['paises'], 0);
        } while ($reporte_appnexus == false);

        $categorias = null;

        if ($filtro_categorias == '0') {
            $filtro_categorias = $this->ci->categorias->get_categorias();
            foreach ($filtro_categorias as $cat) {
                $categorias[] = array($cat->id, $cat->nombre);
            }
        } else {
            $filtro_categorias = explode('o', trim($filtro_categorias, 'o'));
            foreach ($filtro_categorias as $cat) {
                $categoria = $this->ci->categorias->get_categoria_by_id($cat);
                $categorias[] = array($categoria->id, $categoria->nombre);
            }
        }

        $totales['imps'] = '-';

        unset($categoria);

        // recorro una por una las categorias y voy sumando las impresiones
        foreach ($categorias as $cat) {
            $categoria['nombre'] = $cat[1];
            $categoria['imps'] = 0;

            foreach ($filtros['sitios'] as $sitio) {
                try {
                    $total_categorias_sitio = 1;
                    $corre = FALSE;
                    $cats_site = $this->ci->sitescategories->get_all_cats_by_site($sitio->id);
                    if ($cats_site) {
                        foreach ($cats_site as $categorizacion) {
                            if ($categorizacion->id_categoria == $cat[0]) {
                                $corre = TRUE;
                                break;
                            }
                        }
                    }

                    if ($reporte_appnexus) {
                        foreach ($reporte_appnexus as $report_appnexus) {
                            if ($report_appnexus['id_sitio'] == $sitio->id_site && $corre)
                                $categoria['imps'] += $report_appnexus['imps'] * $this->multiplicacion_volumen;
                        }
                    }
                } catch (Exception $ex) {
                    echo "ERROR : " . $ex->getMessage() . "<br>";
                }
            }

            $categoria['imps'] = number_format($categoria['imps'], 0, ',', '.');

            $categories[] = $categoria;
        }

        $data['totales'] = $totales;
        $data['categorias'] = $categories;

        return $data;

        //$reporte_eplanning = $this->obtenerReporte_eplanning('sitios', $filtros['fechas']['desde'], $filtros['fechas']['hasta'], $filtros['paises'], $filtros['sitios'], 0);
        //$reporte_appnexus = $this->obtenerReporte_appnexus($token, 'sitios', $filtros['fechas']['desde'], $filtros['fechas']['hasta'], $filtros['sitios_appnexus'], $filtros['paises'], 0);

        /*
          do {
          $reporte_dfp = $this->obtenerReporte_DFP('sitios', $filtros['fechas']['desde'], $filtros['fechas']['hasta'], $filtros['sitios'], $filtros['paises_dfp'], 0);
          } while ($reporte_dfp == false);

          $categorias = null;

          if ($filtro_categorias == '0') {
          $filtro_categorias = $this->ci->categorias->get_categorias();
          foreach ($filtro_categorias as $cat) {
          $categorias[] = array($cat->id, $cat->nombre);
          }
          } else {
          $filtro_categorias = explode('o', trim($filtro_categorias, 'o'));
          foreach ($filtro_categorias as $cat) {
          $categoria = $this->ci->categorias->get_categoria_by_id($cat);
          $categorias[] = array($categoria->id, $categoria->nombre);
          }
          }

          $totales['imps'] = '-';

          unset($categoria);

          // recorro una por una las categorias y voy sumando las impresiones
          foreach ($categorias as $cat) {
          $categoria['nombre'] = $cat[1];
          $categoria['imps'] = 0;

          foreach ($filtros['sitios'] as $sitio) {
          try {
          $total_categorias_sitio = 1;
          $corre = FALSE;
          $cats_site = $this->ci->sitescategories->get_all_cats_by_site($sitio->id);
          if ($cats_site) {
          foreach ($cats_site as $categorizacion) {
          if ($categorizacion->id_categoria == $cat[0]) {
          $corre = TRUE;
          break;
          }
          }
          }

          if ($reporte_eplanning) {
          foreach ($reporte_eplanning as $report_eplanning) {
          if ($report_eplanning['id_sitio'] == $sitio->id && $corre)
          $categoria['imps'] += $report_eplanning['imps'] * $this->multiplicacion_volumen;
          }
          }


          if ($reporte_appnexus) {
          foreach ($reporte_appnexus as $report_appnexus) {
          if ($report_appnexus['id_sitio'] == $sitio->id_site && $corre)
          $categoria['imps'] += $report_appnexus['imps'] * $this->multiplicacion_volumen;
          }
          }

          if ($reporte_dfp) {
          foreach ($reporte_dfp as $report_dfp) {
          if ($sitio->id_adunit_site) {
          if ($report_dfp['site_id'] == $sitio->id_adunit_site && $corre)
          $categoria['imps'] += $report_dfp['imps'] * $this->multiplicacion_volumen;
          }
          }
          }
          } catch (Exception $ex) {
          echo "ERROR : " . $ex->getMessage() . "<br>";
          }
          }

          $categoria['imps'] = number_format($categoria['imps'], 0, ',', '.');

          $categories[] = $categoria;
          }

          $data['totales'] = $totales;
          $data['categorias'] = $categories;

          return $data;
         *
         */
    }

    public function reportePorSitioFormatoApi($token, $filtro_categorias, $filtro_paises, $intervalo, $fecha_desde, $fecha_hasta) {
        $filtros = $this->filtrarSitiosParaReporte($filtro_categorias, $filtro_paises, $intervalo, $fecha_desde, $fecha_hasta);

        if (!$filtros)
            return FALSE;

        $reporte_eplanning = null;
        $reporte_appnexus = null;
        $reporte_dfp = null;

        //$reporte_eplanning = $this->obtenerReporte_eplanning('sitios', $filtros['fechas']['desde'], $filtros['fechas']['hasta'], $filtros['paises'], $filtros['sitios'], 0);
        //$reporte_appnexus = $this->obtenerReporte_appnexus($token, 'sitios', $filtros['fechas']['desde'], $filtros['fechas']['hasta'], $filtros['sitios_appnexus'], $filtros['paises'], 0);

        do {
            $reporte_appnexus = $this->obtenerReporte_appnexus($token, 'sitios', $filtros['fechas']['desde'], $filtros['fechas']['hasta'], $filtros['sitios_appnexus'], $filtros['paises'], 0);
        } while ($reporte_appnexus == false);
        /*
          do {
          $reporte_dfp = $this->obtenerReporte_DFP('sitios', $filtros['fechas']['desde'], $filtros['fechas']['hasta'], $filtros['sitios'], $filtros['paises_dfp'], 0);
          } while ($reporte_dfp == false);
         */

        $formats_db = $this->ci->formatosdfp->get_formatos();
        foreach ($formats_db as $value)
            $formatos_db[$value->id] = $value;

        $totales['imps'] = 0;

        foreach ($filtros['sitios'] as $sitio) {
            unset($site, $formatos, $formato);
            $site['url_sitio'] = $sitio->nombre_appnexus;
            if ($site['url_sitio'] == '')
                $site['url_sitio'] = $sitio->nombre_dfp;

            // Personalizo los nombres para GLAM
            $nombre_glam = $this->reemplazarNombresGlam($sitio->id);
            if ($nombre_glam)
                $site['url_sitio'] = $nombre_glam;

            $site['url_sitio'] = str_replace("http://", "", $site['url_sitio']);
            $site['url_sitio'] = str_replace("/", "", $site['url_sitio']);

            $site['categorias'] = $this->_categoriasPorSitio($sitio->id);
            if ($site['categorias'] == '')
                $site['categorias'] = '-';

            $formato['nombre'] = '';
            $formato['imps'] = 0;

            $formatos = null;
            /*
              if ($reporte_eplanning) {
              foreach ($reporte_eplanning as $report_eplanning) {
              if ($report_eplanning['id_sitio'] == $sitio->id) {

              $formato['nombre'] = $report_eplanning['size'];

              if ($formato['nombre'] == 9 || $formato['nombre'] == 10 || $formato['nombre'] == 11)
              $formato['nombre'] = 'Layer-Skin';

              $formato['imps'] = $report_eplanning['imps'] * $this->multiplicacion_volumen;

              $totales['imps'] += $formato['imps'];

              if (isset($formatos[$formato['nombre']])) {
              $formatos[$formato['nombre']]['imps'] += $formato['imps'];
              } else {
              $formatos[$formato['nombre']] = $formato;
              }
              }
              }
              }
             */


            if ($reporte_appnexus) {
                foreach ($reporte_appnexus as $report_appnexus) {
                    if ($report_appnexus['id_sitio'] == $sitio->id_site) {
                        $formato['nombre'] = trim($report_appnexus['size']);

                        $formato['imps'] = $report_appnexus['imps'] * $this->multiplicacion_volumen;

                        $totales['imps'] += $formato['imps'];

                        if ($formato['nombre'] != '1x1' && $formato['nombre'] != 'Layer - Skin')
                            $formatos[$formato['nombre']] = $formato;
                    }
                }
            }
            /*
              if ($reporte_dfp) {
              foreach ($reporte_dfp as $report_dfp) {
              if ($sitio->id_adunit_site) {
              if ($report_dfp['site_id'] == $sitio->id_adunit_site) {
              $formato_id = $this->ids_tamanios[$report_dfp['espacio_id']];

              if ($formato_id == 9 || $formato_id == 10 || $formato_id == 11) {
              $formato['nombre'] = 'Layer-Skin';
              } else {
              $formato['nombre'] = $formatos_db[$formato_id]->descripcion;
              }

              $formato['imps'] = $report_dfp['imps'] * $this->multiplicacion_volumen;

              $totales['imps'] += $formato['imps'];

              if (isset($formatos[$formato['nombre']])) {
              $formatos[$formato['nombre']]['imps'] += $formato['imps'];
              } else {
              $formatos[$formato['nombre']] = $formato;
              }
              }
              }
              }
              }
             */

            $site['formatos'] = $formatos;

            $sitios[] = $site;
        }

        $totales['imps'] = number_format($totales['imps'], 0, ',', '.');

        $data['totales'] = $totales;

        if (!isset($sitios))
            return FALSE;

        $data['sitios'] = $sitios;

        return $data;
    }

    public function reportePorFormatoApi($token, $filtro_categorias, $filtro_paises, $intervalo, $fecha_desde, $fecha_hasta) {
        $filtros = $this->filtrarSitiosParaReporte($filtro_categorias, $filtro_paises, $intervalo, $fecha_desde, $fecha_hasta);

        if (!$filtros)
            return FALSE;

        $reporte_eplanning = null;
        $reporte_appnexus = null;
        $reporte_dfp = null;

        do {
            $reporte_appnexus = $this->obtenerReporte_appnexus($token, 'sitios', $filtros['fechas']['desde'], $filtros['fechas']['hasta'], $filtros['sitios_appnexus'], $filtros['paises'], 0);
        } while ($reporte_appnexus == false);

        $formatos = null;

        $filtro_tamanios = $this->ci->formatosdfp->get_formatos();
        foreach ($filtro_tamanios as $form)
            $formatos[] = array($form->id, $form->descripcion, $form->valor);

        // recorro uno por uno los sitios del reporte
        $totales['imps'] = 0;

        unset($formato);
        // recorro una por una las categorias y voy sumando las impresiones
        foreach ($formatos as $form) {
            if ($form[0] == 10 || $form[0] == 9 || $form[0] == 11) {
                $formato['nombre'] = 'Layer-Skin';
            } else {
                $formato['nombre'] = $form[1];
            }
            $formato['imps'] = 0;

            if ($reporte_appnexus) {
                foreach ($reporte_appnexus as $report_appnexus) {
                    if (trim($report_appnexus['size']) == trim($form[2]))
                        $formato['imps'] += $report_appnexus['imps'] * $this->multiplicacion_volumen;
                }
            }

            if ($form[0] == 11)
                $formats[10]['imps'] += $formato['imps'];

            $totales['imps'] += $formato['imps'];

            if ($form[0] != 10 && $form[0] != 11) {
                $formato['imps'] = number_format($formato['imps'], 0, ',', '.');
            } else if ($form[0] == 11) {
                $formats[10]['imps'] = number_format($formats[10]['imps'], 0, ',', '.');
            }

            if ($form[0] != 11)
                $formats[$form[0]] = $formato;
        }

        $totales['imps'] = number_format($totales['imps'], 0, ',', '.');

        $data['totales'] = $totales;

        $data['formatos'] = $formats;

        return $data;

        //$reporte_eplanning = $this->obtenerReporte_eplanning('sitios', $filtros['fechas']['desde'], $filtros['fechas']['hasta'], $filtros['paises'], $filtros['sitios'], 0);
        //$reporte_appnexus = $this->obtenerReporte_appnexus($token, 'sitios', $filtros['fechas']['desde'], $filtros['fechas']['hasta'], $filtros['sitios_appnexus'], $filtros['paises'], 0);

        /*
          do {
          $reporte_dfp = $this->obtenerReporte_DFP('sitios', $filtros['fechas']['desde'], $filtros['fechas']['hasta'], $filtros['sitios'], $filtros['paises_dfp'], 0);
          } while ($reporte_dfp == false);

          $formatos = null;

          $filtro_tamanios = $this->ci->formatosdfp->get_formatos();
          foreach ($filtro_tamanios as $form)
          $formatos[] = array($form->id, $form->descripcion, $form->valor);

          // recorro uno por uno los sitios del reporte
          $totales['imps'] = 0;

          unset($formato);
          // recorro una por una las categorias y voy sumando las impresiones
          foreach ($formatos as $form) {
          if ($form[0] == 10 || $form[0] == 9 || $form[0] == 11) {
          $formato['nombre'] = 'Layer-Skin';
          } else {
          $formato['nombre'] = $form[1];
          }
          $formato['imps'] = 0;

          if ($reporte_eplanning) {
          foreach ($reporte_eplanning as $report_eplanning) {
          if (isset($report_eplanning['size'])) {
          if ($report_eplanning['size'] == $form[0])
          $formato['imps'] += $report_eplanning['imps'] * $this->multiplicacion_volumen;
          }
          }
          }


          if ($reporte_appnexus) {
          foreach ($reporte_appnexus as $report_appnexus) {
          if (trim($report_appnexus['size']) == trim($form[2]))
          $formato['imps'] += $report_appnexus['imps'] * $this->multiplicacion_volumen;
          }
          }

          if ($reporte_dfp) {
          foreach ($reporte_dfp as $report_dfp) {
          if ($this->ids_tamanios[$report_dfp['espacio_id']] == trim($form[0]))
          $formato['imps'] += $report_dfp['imps'] * $this->multiplicacion_volumen;

          if (trim($form[0]) == 11) {
          if ($this->ids_tamanios[$report_dfp['espacio_id']] == 9)
          $formato['imps'] += $report_dfp['imps'] * $this->multiplicacion_volumen;
          }
          }
          }

          if ($form[0] == 11)
          $formats[10]['imps'] += $formato['imps'];

          $totales['imps'] += $formato['imps'];

          if ($form[0] != 10 && $form[0] != 11) {
          $formato['imps'] = number_format($formato['imps'], 0, ',', '.');
          } else if ($form[0] == 11) {
          $formats[10]['imps'] = number_format($formats[10]['imps'], 0, ',', '.');
          }

          if ($form[0] != 11)
          $formats[$form[0]] = $formato;
          }

          $totales['imps'] = number_format($totales['imps'], 0, ',', '.');

          $data['totales'] = $totales;

          $data['formatos'] = $formats;

          return $data;
         *
         */
    }

    public function reportePorPaisApi($token, $filtro_categorias, $filtro_paises, $intervalo, $fecha_desde, $fecha_hasta) {
        $filtros = $this->filtrarSitiosParaReporte($filtro_categorias, $filtro_paises, $intervalo, $fecha_desde, $fecha_hasta);

        if (!$filtros)
            return FALSE;

        $reporte_eplanning = null;
        $reporte_appnexus = null;
        $reporte_dfp = null;

        do {
            $reporte_appnexus = $this->obtenerReporte_appnexus($token, 'pais', $filtros['fechas']['desde'], $filtros['fechas']['hasta'], $filtros['sitios_appnexus'], $filtros['paises'], 0);
        } while ($reporte_appnexus == false);

        if ($filtro_paises == '') {
            echo 'filtro';
            $filtro_paises = $this->ci->paises->get_paises();
            foreach ($filtro_paises as $pais) {
                $paises[] = array($pais->id, $pais->id, $pais->descripcion);
            }
        } else {
            $filtro_paises = explode('o', trim($filtro_paises, 'o'));
            foreach ($filtro_paises as $pais) {
                $pais = $this->ci->paises->get_pais_by_id($pais);
                $paises[] = array($pais->id, $pais->id, $pais->descripcion);
            }
        }

        $totales['imps'] = 0;

        unset($pais);

        // recorro una por una los paises y voy sumando las impresiones
        foreach ($paises as $country) {
            $pais['nombre'] = $country[2];
            $pais['imps'] = 0;

            $total_imps['imps'] = 0;

            if ($reporte_appnexus) {
                foreach ($reporte_appnexus as $report_appnexus) {
                    if ($report_appnexus['id_pais'] == $country[0])
                        $pais['imps'] += $report_appnexus['imps'] * $this->multiplicacion_volumen;
                }
            }

            $totales['imps'] += $pais['imps'];

            $pais['imps'] = number_format($pais['imps'], 0, ',', '.');

            $countries[] = $pais;
        }

        $totales['imps'] = number_format($totales['imps'], 0, ',', '.');

        $data['totales'] = $totales;

        $data['paises'] = $countries;

        return $data;

        //$reporte_eplanning = $this->obtenerReporte_eplanning('sitios', $filtros['fechas']['desde'], $filtros['fechas']['hasta'], $filtros['paises'], $filtros['sitios'], 0);
        //$reporte_appnexus = $this->obtenerReporte_appnexus($token, 'pais', $filtros['fechas']['desde'], $filtros['fechas']['hasta'], $filtros['sitios_appnexus'], $filtros['paises'], 0);

        /*
          do {
          $reporte_dfp = $this->obtenerReporte_DFP('pais', $filtros['fechas']['desde'], $filtros['fechas']['hasta'], $filtros['sitios'], $filtros['paises_dfp'], 0);
          echo 'reporte dfp';
          } while ($reporte_dfp == false);

          if ($filtro_paises == '') {
          echo 'filtro';
          $filtro_paises = $this->ci->paises->get_paises();
          foreach ($filtro_paises as $pais) {
          $paises[] = array($pais->id, $pais->id_dfp, $pais->descripcion);
          }
          } else {
          $filtro_paises = explode('o', trim($filtro_paises, 'o'));
          foreach ($filtro_paises as $pais) {
          $pais = $this->ci->paises->get_pais_by_id($pais);
          $paises[] = array($pais->id, $pais->id_dfp, $pais->descripcion);
          }
          }

          $totales['imps'] = 0;

          unset($pais);

          // recorro una por una los paises y voy sumando las impresiones
          foreach ($paises as $country) {
          $pais['nombre'] = $country[2];
          $pais['imps'] = 0;

          $total_imps['imps'] = 0;

          if ($reporte_eplanning) {
          foreach ($reporte_eplanning as $report_eplanning) {
          if ($report_eplanning['nombre_pais'] == $country[2])
          $pais['imps'] += $report_eplanning['imps'] * $this->multiplicacion_volumen;
          }
          }

          if ($reporte_appnexus) {
          foreach ($reporte_appnexus as $report_appnexus) {
          if ($report_appnexus['id_pais'] == $country[0])
          $pais['imps'] += $report_appnexus['imps'] * $this->multiplicacion_volumen;
          }
          }

          if ($reporte_dfp) {
          foreach ($reporte_dfp as $report_dfp) {
          if ($report_dfp['pais_id'] == $country[1])
          $pais['imps'] += $report_dfp['imps'] * $this->multiplicacion_volumen;
          }
          }


          $totales['imps'] += $pais['imps'];

          $pais['imps'] = number_format($pais['imps'], 0, ',', '.');

          $countries[] = $pais;
          }

          $totales['imps'] = number_format($totales['imps'], 0, ',', '.');

          $data['totales'] = $totales;

          $data['paises'] = $countries;

          return $data;
         *
         */
    }

    public function reportePorPaisFormatoApi($token, $filtro_categorias, $filtro_paises, $intervalo, $fecha_desde, $fecha_hasta) {
        $filtros = $this->filtrarSitiosParaReporte($filtro_categorias, $filtro_paises, $intervalo, $fecha_desde, $fecha_hasta);

        if (!$filtros)
            return FALSE;

        $reporte_eplanning = null;
        $reporte_appnexus = null;
        $reporte_dfp = null;

        do {
            $reporte_appnexus = $this->obtenerReporte_appnexus($token, 'pais', $filtros['fechas']['desde'], $filtros['fechas']['hasta'], $filtros['sitios_appnexus'], $filtros['paises'], 0);
        } while ($reporte_appnexus == false);

        if ($filtro_paises == '') {
            $filtro_paises = $this->ci->paises->get_paises();
            foreach ($filtro_paises as $pais) {
                $paises[] = array($pais->id, $pais->id, $pais->descripcion);
            }
        } else {
            $filtro_paises = explode('o', trim($filtro_paises, 'o'));
            foreach ($filtro_paises as $pais) {
                $pais = $this->ci->paises->get_pais_by_id($pais);
                $paises[] = array($pais->id, $pais->id, $pais->descripcion);
            }
        }

        $formats_db = $this->ci->formatosdfp->get_formatos();
        foreach ($formats_db as $value)
            $formatos_db[$value->id] = $value;

        $totales['imps'] = 0;

        unset($pais);

        $formato['nombre'] = '';
        $formato['imps'] = 0;

        // recorro una por una los paises y voy sumando las impresiones
        foreach ($paises as $country) {

            unset($formato);

            $formatos = null;

            $pais['nombre'] = $country[2];
            $pais['imps'] = 0;

            $total_imps['imps'] = 0;

            if ($reporte_appnexus) {
                foreach ($reporte_appnexus as $report_dfp) {
                    if ($report_dfp['pais_id'] == $country[1]) {

                        $pais['imps'] += $report_dfp['imps'] * $this->multiplicacion_volumen;

                        $formato_id = $this->ids_tamanios[$report_dfp['espacio_id']];

                        if ($formato_id == 18)
                            continue;

                        if ($formato_id == 9 || $formato_id == 10 || $formato_id == 11) {
                            $formato['nombre'] = 'Layer-Skin';
                        } else {
                            $formato['nombre'] = $formatos_db[$formato_id]->descripcion;
                        }

                        $formato['imps'] = $report_dfp['imps'] * $this->multiplicacion_volumen;

                        $totales['imps'] += $formato['imps'];

                        if (isset($formatos[$formato['nombre']])) {
                            $formatos[$formato['nombre']]['imps'] += $formato['imps'];
                        } else {
                            $formatos[$formato['nombre']] = $formato;
                        }
                    }
                }
            }

            $totales['imps'] += $pais['imps'];

            $pais['formatos'] = $formatos;

            $pais['imps'] = number_format($pais['imps'], 0, ',', '.');

            $countries[] = $pais;
        }

        $totales['imps'] = number_format($totales['imps'], 0, ',', '.');

        $data['totales'] = $totales;

        $data['paises'] = $countries;

        return $data;

        /*
          do {
          $reporte_dfp = $this->obtenerReporte_DFP('pais', $filtros['fechas']['desde'], $filtros['fechas']['hasta'], $filtros['sitios'], $filtros['paises_dfp'], 0);
          } while ($reporte_dfp == false);

          if ($filtro_paises == '') {
          $filtro_paises = $this->ci->paises->get_paises();
          foreach ($filtro_paises as $pais) {
          $paises[] = array($pais->id, $pais->id_dfp, $pais->descripcion);
          }
          } else {
          $filtro_paises = explode('o', trim($filtro_paises, 'o'));
          foreach ($filtro_paises as $pais) {
          $pais = $this->ci->paises->get_pais_by_id($pais);
          $paises[] = array($pais->id, $pais->id_dfp, $pais->descripcion);
          }
          }

          $formats_db = $this->ci->formatosdfp->get_formatos();
          foreach ($formats_db as $value)
          $formatos_db[$value->id] = $value;

          $totales['imps'] = 0;

          unset($pais);

          $formato['nombre'] = '';
          $formato['imps'] = 0;

          // recorro una por una los paises y voy sumando las impresiones
          foreach ($paises as $country) {

          unset($formato);

          $formatos = null;

          $pais['nombre'] = $country[2];
          $pais['imps'] = 0;

          $total_imps['imps'] = 0;

          if ($reporte_dfp) {
          foreach ($reporte_dfp as $report_dfp) {
          if ($report_dfp['pais_id'] == $country[1]) {

          $pais['imps'] += $report_dfp['imps'] * $this->multiplicacion_volumen;

          $formato_id = $this->ids_tamanios[$report_dfp['espacio_id']];

          if ($formato_id == 18)
          continue;

          if ($formato_id == 9 || $formato_id == 10 || $formato_id == 11) {
          $formato['nombre'] = 'Layer-Skin';
          } else {
          $formato['nombre'] = $formatos_db[$formato_id]->descripcion;
          }

          $formato['imps'] = $report_dfp['imps'] * $this->multiplicacion_volumen;

          $totales['imps'] += $formato['imps'];

          if (isset($formatos[$formato['nombre']])) {
          $formatos[$formato['nombre']]['imps'] += $formato['imps'];
          } else {
          $formatos[$formato['nombre']] = $formato;
          }
          }
          }
          }

          $totales['imps'] += $pais['imps'];

          $pais['formatos'] = $formatos;

          $pais['imps'] = number_format($pais['imps'], 0, ',', '.');

          $countries[] = $pais;
          }

          $totales['imps'] = number_format($totales['imps'], 0, ',', '.');

          $data['totales'] = $totales;

          $data['paises'] = $countries;

          return $data;
         *
         */
    }

    function cambiar_moneda($valor, $moneda = NULL) {

        $this->ci->load->model('constants');
        $this->ci->load->model('cotizaciones_diarias');

        if ($moneda == NULL)
            $moneda = $this->ci->user_data->moneda;

        if ($moneda != 'USD') {
            // averiguo la cotizacion del dolar en base a la moneda seleccionada al crear la campana.
            $cotizacion = $this->ci->cotizaciones_diarias->get_cotizacion_today($this->ci->user_data->moneda);
            if ($cotizacion) {
                $cotizacion = (float) $cotizacion->amount;

                if ($this->ci->user_data->moneda == 'ARS') {
                    $cambio_fijo = $this->ci->constants->get_constant_by_id(CAMBIO_FIJO_ARGENTINA);
                } else {
                    $cambio_fijo = $this->ci->constants->get_constant_by_id(CAMBIO_FIJO_MEXICO);
                }

                $cambio_fijo = (float) $cambio_fijo->value;

                if ($cambio_fijo > $cotizacion)
                    $cotizacion = $cambio_fijo;

                $valor = $valor * $cotizacion;
            }
        }

        return $valor;
    }

}