<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Welcome extends CI_Controller {

    function __construct() {
        parent::__construct();

        $this->load->model('columnas');
        $this->load->model('campanias');
        $this->load->model('users');
        $this->load->model('paises');
        $this->load->model('categoriasaexcluir');
        $this->load->model('campaniasformatos');
        $this->load->model('formatosdfp');
        $this->load->model('reportes');

        if (!$this->tank_auth->is_logged_in())
            redirect_login_js();
    }

    function index() {

        if (!$this->tank_auth->is_logged_in()) {
            redirect('auth/login/');
        } else {

            if ($this->campanias_aprobadas)
                redirect('/campania');
            /*
              if (!$this->tarjeta_certificada && $this->creado_desde_sitio && $this->limite_de_compra == 0.00)
              redirect('/micuenta');
             */
            if (!$this->campanias_aprobadas && $this->creado_desde_sitio)
                redirect('/campania');

            $anunciantes_adserver = $this->users->get_anunciantes_app_by_id($this->tank_auth->get_user_id());

            if (!$this->creado_desde_sitio && !$anunciantes_adserver)
                redirect('/campania');

            redirect('/campania');
        }
    }

    function reporte($id_campania) {
      
        if (!isset($id_campania)) {
            echo '<div class="alerta">Por favor especifique la campa&ntilde;a de cual desea obtener el reporte.</div>';
            return FALSE;
        }

        // averiguo si no esta en mantenimiento esta opcion
        $this->esta_en_mantenimiento(MENSAJE_MANTENIMIENTO_OBTENER_REPORTES_ANUNCIANTES);

        $campania = $this->campanias->get_campania_by_id($id_campania);
        /*
          if($this->tank_auth->get_user_id() == 338){
          new_var_dump($campania);
          } */
       
        if ($campania) {
            // campania
            //var_dump($campania);
            //die();
            $data['campania'] = $campania;
            $data['id_order_dfp'] = $campania->id_orden_dfp;
            
            $data['order_dfp'] = $campania->nombre;

            $data['tipo_campania'] = $campania->tipo_campania;

            $data['segmentacion'] = $campania->segmentacion_id;

            $data['id_lineItem_appnexus'] = $campania->id_lineItem_appnexus;

            // anunciante
            $advertiser = $this->users->get_anunciantes_adservers_by_id($campania->id_anunciante);
            if ($advertiser) {
                switch ($advertiser->adserver_actual) {
                    case '0':
                        $id_anunciante_adserver = $advertiser->id_dfp;
                        break;
                    case '1':
                        $id_anunciante_adserver = $advertiser->id_appnexus;
                        break;
                    case '2':
                        $id_anunciante_adserver = $advertiser->id_eplanning;
                        break;
                }

                $data['id_adserver'] = $advertiser->adserver_actual;
                $data['id_anunciante_adserver'] = $id_anunciante_adserver;

                $data['anunciante_adserver'] = $advertiser->nombre;
            }

            // usuario
            $usuario = $this->users->get_user_by_id($this->tank_auth->get_user_id(), 1);
            $data['es_agencia'] = $usuario->agencia;
            $data['agrupar_por_sitio'] = $usuario->agrupar_por_sitio;
            $data['mostrar_filtro_pais'] = $usuario->mostrar_filtro_pais;

            // paises
            $data['paises'] = $this->paises->get_paises();

            //rangos
            $data['arr_range'] = array(
                'today' => 'Hoy'
                , 'yesterday' => 'Ayer'
                , 'last_7_days' => 'Ultimos 7 dias'
                , 'month_to_date' => 'este mes: ' . getMesEsp(date("m"))
                , 'last_month' => 'el mes pasado: ' . getMesEsp(date("m") - 1)
                , 'especific' => 'Fechas Especificas'
                , 'lifetime' => 'Siempre'
            );

            // tamanios
            $data['tamanios'] = $this->formatosdfp->get_formatos();
             //var_dump($data);
            if ($data['id_lineItem_appnexus'] != '' && $data['id_order_dfp'] != '' || $data['id_lineItem_appnexus'] != '' && $data['id_order_dfp'] != NULL) {
                $this->load->view('home_view_appnexus', $data);

                return TRUE;
            } else if ($data['id_lineItem_appnexus'] != '' && $data['id_order_dfp'] == '' || $data['id_lineItem_appnexus'] != '' && $data['id_order_dfp'] != NULL) {
                $this->load->view('home_view_appnexus', $data);

                return TRUE;
            } else if ($data['id_order_dfp'] != '' && $data['id_lineItem_appnexus'] == '') {
                $this->load->view('home_view', $data);

                return TRUE;
            }
        } else {
            echo '<div class="alerta">No puedes obtener el reporte de esta campa&ntilde;a</div>';
            return FALSE;
        }
    }

    function suscribir_reporte() {

        $data = array(
            'correo_electronico' => $this->input->post('correo_electronico')
            , 'tipo' => 'anunciantes_campania'
            , 'enviar_cada' => $this->input->post('enviar_cada')
            , 'dia_de_semana' => $this->input->post('dia_de_la_semana')
            , 'extension' => $this->input->post('extension')
            , 'id_adserver' => $this->input->post('id_adserver')
            , 'id_anunciante' => $this->input->post('id_anunciante')
            , 'id_orden' => $this->input->post('id_orden')
            , 'por_sitio' => $this->input->post('por_sitio')
            , 'rango' => $this->input->post('rango')
            , 'token' => $this->token
            , 'filtro_li' => $this->input->post('filtro_li')
            , 'filtro_cr' => $this->input->post('filtro_cr')
            , 'filtro_formatos' => $this->input->post('filtro_formatos')
            , 'filtro_paises' => $this->input->post('filtro_paises')
            , 'grupos' => $this->input->post('grupos')
            , 'columnas' => $this->input->post('columnas')
            , 'intervalo' => $this->input->post('intervalo')
            , 'fecha_desde' => $this->input->post('fecha_desde')
            , 'fecha_hasta' => $this->input->post('fecha_hasta')
            , 'seconds' => $this->input->post('seconds')
            , 'solicitado_desde' => 'ANUNCIANTES'
        );

        $this->reportes->insertar_suscripcion($data);

        echo json_encode(array('validate' => TRUE));
    }

    function obtener_intervalos($id_adserver, $seconds) {
        if ($id_adserver == "1") {

            $arr_interval = array('cumulative' => 'Acumulativo', 'hour' => 'Hora', 'day' => 'Dia', 'month' => 'Mes');

            foreach ($arr_interval as $c => $v) {
                echo '<option value="' . $c . '">' . $v . '</option>';
            }
        } elseif ($id_adserver == "2") {

            $arr_interval = array('cumulative' => 'Acumulativo', 'day' => 'Dia', 'month' => 'Mes');

            foreach ($arr_interval as $c => $v) {
                echo '<option value="' . $c . '">' . $v . '</option>';
            }
        } elseif ($id_adserver == "0") {

            $arr_interval = array('cumulative' => 'Acumulativo', 'day' => 'Dia', 'month' => 'Mes');

            foreach ($arr_interval as $c => $v) {
                echo '<option value="' . $c . '">' . $v . '</option>';
            }
        }
    }

    function obtener_anunciantes_adservers($seconds) {
        $anunciantes_adserver = $this->users->get_anunciantes_app_by_id($this->tank_auth->get_user_id());

        foreach ($anunciantes_adserver as $adv) {
            $adv_appnexus = $this->users->get_anunciantes_adservers_by_id($adv->id_anunciante_adserver);
            if ($adv_appnexus) {
                echo '<option value="' . $adv_appnexus->id . '">' . $adv_appnexus->nombre . '</option>';
            }
        }
    }

    /*
      function test(){
      $ordenes = $this->api->obtenerOrdenesPorAnunciante_DFP(20641591);

      echo "ORDENES TEST  <br>";
      new_var_dump($ordenes);
      }
     */

    function obtener_ordenes_anunciante($anunciante_id) {
        $ordenes = $this->api->obtenerOrdenesPorAnunciante_DFP($anunciante_id);
        foreach ($ordenes as $orden) {
            if ($orden->status != 'DRAFT' && $orden->status == 'APPROVED' || $orden->status == 'PAUSED' || $orden->status == 'PENDING_APPROVAL') {
                $style = '';
                if ($orden->status == 'PAUSED') {
                    $style .= 'color: orange; font-style: italic;';
                } else if ($orden->isArchived == true) {
                    $style = 'color: red; font-style: italic;';
                } else {
                    $style = 'color: green; font-weight: bold;';
                }

                echo '<option value="' . $orden->id . '" style="' . $style . '">' . $orden->name . '</option>';
            }
        }
    }

    function obtener_reporte_dinamico_status($orden_id, $ad_server = 'DFP') {
        if ($ad_server == 'DFP') {
            $reporte = $this->campanias->get_full_status_by_campania($orden_id);
        } else {
            $reporte = $this->campanias->get_full_status_by_campania_appnexus($orden_id);
        }

        if (!$reporte) {
            echo '<div class="alerta">No se encontraron datos.</div>';
            return FALSE;
        }

        $graph_categories = $graph_imps = $graph_views = $graph_clicks = '';

        $total_imps = $total_views = $total_clicks = $total_ctr = 0;

        foreach ($reporte as $row) {
            $row->fecha_reporte = MySQLDateToDate($row->fecha_reporte);

            $graph_categories .= '"' . $row->fecha_reporte . '" ,';
            $graph_imps .= $row->imps_ayer . ' ,';
            $graph_views .= $row->vistas_ayer . ' ,';
            $graph_clicks .= $row->clicks_ayer . ' ,';

            $row->ctr_ayer = 0;

            if ($row->imps_ayer > 0)
                $row->ctr_ayer = ($row->clicks_ayer / $row->imps_ayer) * 100;

            $total_imps += $row->imps_ayer;
            $total_views += $row->vistas_ayer;
            $total_clicks += $row->clicks_ayer;

            $reportes[] = $row;
        }

        if ($total_imps > 0)
            $total_ctr = ($total_clicks / $total_imps) * 100;

        $campania = $this->campanias->get_campania_by_order_id($orden_id);

        $this->load->model('anunciantes');

        $data['nombre_anunciante'] = $this->anunciantes->get_anunciante_adserver_by_id($campania->id_anunciante);

        $data['nombre_anunciante'] = $data['nombre_anunciante']->nombre;

        $data['nombre_orden'] = $campania->nombre;

        $data['usuario'] = $this->users->get_notacion_user($this->tank_auth->get_user_id());

        $data['id_orden'] = $orden_id;

        $data['graph_categories'] = trim($graph_categories, ' ,');
        $data['graph_imps'] = trim($graph_imps, ' ,');
        $data['graph_views'] = trim($graph_views, ' ,');
        $data['graph_clicks'] = trim($graph_clicks, ' ,');

        $data['texto_columnas'] = 'fecha;imps;clicks;ctr;vistas';

        $data['reporte'] = $reportes;

        $data['total_imps'] = $total_imps;
        $data['total_views'] = $total_views;
        $data['total_clicks'] = $total_clicks;
        $data['total_ctr'] = $total_ctr;

        $this->load->model('campaniasformatos');

        if ($ad_server == 'DFP') {
            $data['modalidad_de_compra'] = $campania->modalidad_compra;
        } else {
            $data['modalidad_de_compra'] = $reporte[0]->modalidad_de_compra;
        }

        $this->load->view('tbl_report_por_sitio_status', $data);
    }

    function obtener_reporte_dinamico_por_sitio_DFP($orden_id, $intervalo, $rango, $fecha_desde, $fecha_hasta, $columnas, $dimensiones, $filtros_li, $filtros_cr, $filtros_paises, $seconds) {
        $rango = urldecode($rango);
        $columnas = urldecode($columnas);
        $dimensiones = urldecode($dimensiones);
        $filtros_paises = urldecode($filtros_paises);
        $filtros_li = urldecode($filtros_li);
        $filtros_cr = urldecode($filtros_cr);

        if ($filtros_paises != 0)
            $filtros_paises = explode(';', trim($filtros_paises, ';'));
        $filtros['paises'] = $filtros_paises;

        if ($filtros_li != 0)
            $filtros_li = explode(';', trim($filtros_li, ';'));
        $filtros['lineItems'] = $filtros_li;

        if ($filtros_cr != 0)
            $filtros_cr = explode(';', trim($filtros_cr, ';'));
        $filtros['creatividades'] = $filtros_cr;

        $res_columnas = $this->columnas->get_all_columnas();
        foreach ($res_columnas as $row) {
            $columnas_base_dfp[$row->id] = $row->value_dfp;
            $columnas_base_desc[$row->id] = $row->descripcion;
        }

        //DIMENSIONES API
        $dimensiones_api = null;

        if ($intervalo != 'cumulative')
            $dimensiones_api[] = 'DATE';

        if ($dimensiones) {
            $dimensiones_tmp = explode(';', trim($dimensiones, ';'));
            for ($i = 0; $i < sizeof($dimensiones_tmp); $i++) {
                $dimensiones_api[] = $columnas_base_dfp[$dimensiones_tmp[$i]];
                $columnas_names[] = $columnas_base_desc[$dimensiones_tmp[$i]];
            }
        }

        //COLUMNAS API
        $columnas_api = null;
        if ($columnas) {
            $columnas_tmp = explode(';', trim($columnas, ';'));
            for ($i = 0; $i < sizeof($columnas_tmp); $i++) {
                $columnas_api[] = $columnas_base_dfp[$columnas_tmp[$i]];
                $columnas_names[] = $columnas_base_desc[$columnas_tmp[$i]];
            }
        }

        if ($rango == 'MONTH_TO_DATE') {
            $rango = 'especific';
            $fecha_desde = date('d-m-Y', strtotime('this month', strtotime(date('Y-m-01'))));
            $fecha_hasta = date('d-m-Y 23:59:59');
        }
        /*
          echo "OrdenId: " . $orden_id . "</br>";
          echo "Intervalo: " . $intervalo . "</br>";
          echo "Rango: " . $rango . "</br>";
          echo "FechaDesde: " . $fecha_desde . "</br>";
          echo "FechaHasta: " . $fecha_hasta . "</br>";
          echo "Columnas: " . $columnas . "</br>";
          echo "Dimensiones: " . $dimensiones . "</br>";
          echo "Filtros: " . $filtros . "</br>";
         */
        $credentials = array(
            'oauth2info' => array('client_id' => "56018185674-7d8uc8mc9bjo1j1hb5lrpte9qptn6jj3.apps.googleusercontent.com",
                'client_secret' => "OeYlLmOFyIfp9KaxGaJhm20w",
                'refresh_token' => '1/DU2ZnIb3oKBpThisOt15Q3-_jTRdrESg8NJtyrPVFRk'),
            'code' => '25379366', 'name' => 'AdtomatikForAdmin'
        );

        $reportData = new stdClass();
        $reportData->report = new stdClass();
        $reportData->report->filter = 'ORDER_ID = ' . $orden_id;

        if ($rango == 'especific') {
            $reportData->report->date = 'CUSTOM_DATE';
            $reportData->report->startDate = $fecha_desde;
            $reportData->report->endDate = $fecha_hasta;
        } else {
            $reportData->report->date = strtoupper($rango);
        }

        $reportData->report->order = true;
        $reportData->report->groupby = $dimensiones_api;
        $reportData->report->columns = $columnas_api;

        $request = new Request();
        $request->method = 'post';
        $request->uri = 'http://adserver.adtomatik.com/get_report';
        $request->data = array('credentials' => $credentials, 'data' => $reportData);
        $request->decodeResponse = true;
        /*
          echo '<pre>';
          var_dump($request);
          echo '</pre>'; */
        $res = json_decode(Caller::call_dfp($request));

        $filesPath = $res->message;

        //var_dump($filesPath);

        $res_report_api = $this->unpackAndProcessData($filesPath);

        $data_report_api = $res_report_api['report'];
        $total = $res_report_api['total'];

        //echo '<pre>';
        //var_dump($request);
        //var_dump($res_report_api);
        //die();

        $data['reporte'] = $data_report_api;
        $data['columnas'] = $columnas_names;
        $data['totales'] = $total;

        if ($intervalo != 'cumulative')
            $data['groupby'] = ['Dia'];

        //echo '</pre>';
        //die();

        $data['id_orden'] = $orden_id;

        $campania = $this->campanias->get_campania_by_order_id($orden_id);

        $this->load->model('anunciantes');

        $data['nombre_anunciante'] = $this->anunciantes->get_anunciante_adserver_by_id($campania->id_anunciante);

        $data['nombre_anunciante'] = $data['nombre_anunciante']->nombre;

        $data['nombre_orden'] = $campania->nombre;

        $this->load->model('campaniasformatos');

        $data['modalidad_de_compra'] = $campania->modalidad_compra;

        $data['usuario'] = $this->users->get_notacion_user($this->tank_auth->get_user_id());

        $this->load->view('tbl_report_dfp', $data);
    }

    private function unpackAndProcessData($filesPath) {
        $paths = array();
        $report = array();
        if (!is_array($filesPath)) {
            $paths[] = $filesPath;
        } else {
            $paths = $filesPath;
        }
        $total = null;
        foreach ($paths as $filePath) {
            $ruta = explode('/', $filePath);
            $rows = gzfile('http://adserver.adtomatik.com/tempfiles/' . $ruta[count($ruta) - 1]);
            //echo "\tRuta del reporte DFP: " . 'http://adserver.adtomatik.com/tempfiles/' . $ruta[count($ruta) - 1] . "\n";
            //Quita primer elemento
            array_shift($rows);
            //Quita ultimo elemento
            //array_pop($rows);
            try {
                if (sizeof($rows) > 0) {
                    //echo "\tFilas en el reporte " . sizeof($rows) . "<br/>";
                    foreach ($rows as $row) {
                        try {
                            $columns = explode("\t", $row);
                            //echo "Columna: ".$columns[0]."<br/>";
                            if (trim($columns[0]) == 'Total') {
                                $total = $columns;
                            } elseif (trim($columns[0]) != '' || trim($columns[0]) != 'Total') {
                                $report[] = $columns;
                            }
                        } catch (Exception $exc) {
                            echo $exc->getTraceAsString() . "\n";
                        }
                    }
                } else {
                    //echo "\nFilas con datos relevantes: " . sizeof($rows) . "\n";
                }
            } catch (Exception $exc) {
                echo $exc->getTraceAsString() . "\n";
            }
            //$this->deleteTempFiles($filePath);
        }

        return array('report' => $report, 'total' => $total);
    }

    function obtener_reporte_dinamico_por_sitio($anunciante_id, $rango, $filtros_li, $grupos, $columnas, $fecha_desde, $fecha_hasta, $filtros_paises, $filtros_cr, $filtros_sizes, $interval, $timezone, $orden, $direccion, $seconds) {

        //$this->obtener_reporte_dinamico($anunciante_id, $rango, $filtros_li, $grupos, $columnas, $fecha_desde, $fecha_hasta, $filtros_paises, $filtros_cr, $filtros_sizes, $interval, $timezone, $orden, $direccion, $seconds);
        //die();
        /*
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
          if ($row_cat->id == $cat->id) {
          $filtrar = 1;
          }
          }
          }
          }
          }

          if ($filtrar) {
          $arr_sites[] = array('site_name' => $row->name, 'id' => $row->id);
          }
          }
          }

          foreach ($arr_sites as $row_site) {
          $arr_filtro[$row_site['id']] = $row_site['id'];
          }

          $arr_filtro_sitios = array_values(array_unique($arr_filtro));
         */

        //$arr_filtros[] = array('site_id' => $arr_filtro_sitios);
        // traigo todos los sitios

        $sitios = $this->sites->get_all_sites();
        if (!$sitios)
            return FALSE;

        // traigo todas las categorias existentes y armo el string
        $categorias = $this->categorias->get_categorias();
        $filtro_categorias = '';
        foreach ($categorias as $categoria)
            $filtro_categorias .= $categoria->id . 'o';

        // filtro los sitios categorizados.
        $arr_sites = $this->api->_filtrarSitiosPorCategorias($sitios, $filtro_categorias);
        if ($arr_sites == FALSE)
            return FALSE;

        foreach ($arr_sites as $row_site) {
            $arr_filtro_sitios[$row_site->id_site] = $row_site->id_site;
        }

        $arr_filtro_sitios = array_values(array_unique($arr_filtro_sitios));


        $filtros_li = urldecode($filtros_li);

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

        $filtros_cr = urldecode($filtros_cr);

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

        $filtros_sizes = urldecode($filtros_sizes);

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

        $filtros_paises = urldecode($filtros_paises);

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

        //$arr_filtros[] = array('seller_type' => "Direct");

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

        $arr_filtros[] = array('advertiser_id' => $anunciante_id);

        $rango = urldecode($rango);
        $columnas = urldecode($columnas);

        $partes_columnas = explode(";", $columnas);

        for ($j = 0; $j < count($partes_columnas); $j++) {
            if (!empty($partes_columnas[$j])) {
                $arr_columnas[] = $partes_columnas[$j];
            }
        }

        $texto_fecha_desde = "";
        $texto_fecha_hasta = "";

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

        if ($grupos) {
            $grupos = urldecode($grupos);

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

        //$datos = getNetworkDynamicReport($this->token, $anunciante_id, $rango, $arr_filtros, $arr_grupos, $arr_columnas_ordenado, $start_date, $end_date, $timezone, $orden, $direccion);
        $datos = getAdvertiserDynamicReport($this->token, $anunciante_id, $rango, $arr_filtros, $arr_grupos, $arr_columnas_ordenado, $start_date, $end_date, $timezone, $orden, $direccion);

        $data['sitios_ocultos'] = $arr_filtro_sitios;
        $data['datos'] = $datos;
        $data['arr_columnas'] = $arr_columnas_ordenado;
        $data['lineItem'] = $partes_li[0];

        $data['usuario'] = $this->users->get_notacion_user($this->tank_auth->get_user_id());

        $this->load->view('tbl_report_por_sitio', $data);
    }

    function obtener_reporte_dinamico_para_grafico_appnexus($anunciante_id, $rango, $filtros_li, $grupos, $columnas, $fecha_desde, $fecha_hasta, $filtros_paises, $filtros_cr, $filtros_sizes, $interval, $timezone, $orden, $direccion, $seconds, $orden_id = 0, $por_sitio = 0) {
        $filtros_li = urldecode($filtros_li);

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

        $filtros_cr = urldecode($filtros_cr);

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

        $filtros_sizes = urldecode($filtros_sizes);

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

        $filtros_paises = urldecode($filtros_paises);

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

        //$arr_filtros[] = array('seller_type' => "Direct");

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

        $rango = urldecode($rango);
        $columnas = urldecode($columnas);

        $arr_columnas[] = 'imps';
        $arr_columnas[] = 'clicks';

        $texto_fecha_desde = "";
        $texto_fecha_hasta = "";

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

        if ($grupos) {
            $grupos = urldecode($grupos);

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

        /*
          if ($interval != "cumulative") {
          $arr_columnas[] = $interval;
          $orden = $interval;
          }
         */

        $arr_columnas[] = 'day';
        $orden = 'day';

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
                //if ($col->id == $arr_columnas[$i] && $col->id != 'site_name') {
                if ($col->id == $arr_columnas[$i]) {
                    $arr_columnas_ordenado[] = $col->id;
                }
            }
        }

        $arr_columnas_ordenado = array('day', 'imps', 'clicks');

        $datos = getAdvertiserDynamicReport($this->token, $anunciante_id, $rango, $arr_filtros, $arr_grupos, $arr_columnas_ordenado, $start_date, $end_date, $timezone, $orden, $direccion);

        $result_paises = $this->paises->get_paises();

        foreach ($result_paises as $row) {
            $paises[$row->id] = $row->descripcion;
        }

        $data['paises'] = $paises;
        $data['datos'] = $datos;
        $data['arr_columnas'] = $arr_columnas_ordenado;

        $data['lineItem'] = $partes_li[0];

        $rows = explode("\n", $datos);

        // datos para los graficos
        $graph_categories = '';
        $graph_imps = '';
        $graph_clicks = '';
        $cant_datos = 0;

        foreach ($rows as $row) {
            $row2 = explode(',', $row);

            if ($row2[0] != 'day' && $row2[0] != '') {
                $graph_categories .= '"' . MySQLDateToDate($row2[0]) . '" ,';

                //if ($por_sitio == 0) {
                $graph_imps .= trim($row2[1], ' ') . ' ,';

                $graph_clicks .= (int) $row2[2] . ' ,';
                //} /*else {
                //$graph_imps .= trim($row2[2], ' ') . ' ,';
                //$graph_clicks .= (int) $row2[3] . ' ,';
                //}*/
                $cant_datos++;
            }
        }

        $data['graph_categories'] = trim($graph_categories, ' ,');
        $data['graph_imps'] = trim($graph_imps, ' ,');
        $data['graph_clicks'] = trim($graph_clicks, ' ,');

        $data['anunciante_id'] = $anunciante_id;

        //$cf = $this->campaniasformatos->get_formatos_by_lineItem_appnexus($anunciante_id);
        $cf = $this->campanias->get_campania_by_lineItem_appnexus($orden_id);

        if ($cf) {
            $data['modalidad_de_compra'] = $cf->modalidad_compra;
        } else {
            $data['modalidad_de_compra'] = 'cpm';
        }

        $data['cant_datos'] = $cant_datos;


        $this->load->view('tbl_report_grafico_appnexus', $data);
    }

    function obtener_reporte_dinamico($anunciante_id, $rango, $filtros_li, $grupos, $columnas, $fecha_desde, $fecha_hasta, $filtros_paises, $filtros_cr, $filtros_sizes, $interval, $timezone, $orden, $direccion, $seconds) {
        $filtros_li = urldecode($filtros_li);

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

        $filtros_cr = urldecode($filtros_cr);

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

        $filtros_sizes = urldecode($filtros_sizes);

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

        $filtros_paises = urldecode($filtros_paises);

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

        //$arr_filtros[] = array('seller_type' => "Direct");

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

        $rango = urldecode($rango);
        $columnas = urldecode($columnas);

        $partes_columnas = explode(";", $columnas);

        for ($j = 0; $j < count($partes_columnas); $j++) {
            if (!empty($partes_columnas[$j])) {
                $arr_columnas[] = $partes_columnas[$j];
            }
        }

        $texto_fecha_desde = "";
        $texto_fecha_hasta = "";

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

        if ($grupos) {
            $grupos = urldecode($grupos);

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

        $datos = getAdvertiserDynamicReport($this->token, $anunciante_id, $rango, $arr_filtros, $arr_grupos, $arr_columnas_ordenado, $start_date, $end_date, $timezone, $orden, $direccion);

        $result_paises = $this->paises->get_paises();

        foreach ($result_paises as $row) {
            $paises[$row->id] = $row->descripcion;
        }

        $data['paises'] = $paises;
        $data['datos'] = $datos;
        $data['arr_columnas'] = $arr_columnas_ordenado;

        $data['lineItem'] = $partes_li[0];


        /*
          $this->load->model('anunciantes');

          $data['nombre_anunciante'] = $this->anunciantes->get_anunciante_adserver_by_id_appnexus($anunciante_id);

          $data['nombre_anunciante'] = $data['nombre_anunciante']->nombre;

          $this->load->model('campanias');

          new_var_dump($orden);

          $data['nombre_campania'] = $this->campanias->get_campania_by_order_id($orden);

          $data['nombre_campania'] = $data['nombre_campania']->nombre;
         */


        $this->load->view('tbl_report', $data);
    }

    function obtener_reporte_eplanning($id_anunciante, $rango, $grupo, $columnas, $fecha_desde, $fecha_hasta, $filtros_paises, $intervalo, $seconds) {

        $rango = urldecode($rango);
        $grupo = urldecode($grupo);
        $columnas = urldecode($columnas);
        $filtros_paises = urldecode($filtros_paises);

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

        $partes_columnas = explode(";", $columnas);

        for ($j = 0; $j < count($partes_columnas); $j++) {
            if (!empty($partes_columnas[$j])) {
                $arr_columnas[] = $partes_columnas[$j];
            }
        }

        $arr_paises = null;

        $filtros_paises = urldecode($filtros_paises);
        if ($filtros_paises) {
            $partes_paises = explode(";", $filtros_paises);
            for ($j = 0; $j < count($partes_paises); $j++) {
                if (!empty($partes_paises[$j])) {
                    $pais = $this->paises->get_pais_by_id($partes_paises[$j]);
                    $arr_paises[] = $pais->descripcion;
                }
            }
        }

        if ($grupo == "creative_name") {
            if ($intervalo == "cumulative") {
                $datos = obtener_reporte_por_anuncio($id_anunciante, $fecha_inicio, $fecha_fin);

                $datos = utf8_urldecode($datos);
                $rows = explode("\n", $datos);

                $arr_data_eplanning = null;

                for ($i = 1; $i < count($rows); $i++) {
                    if (strlen($rows[$i]) > 0)
                        $arr_data_eplanning[] = $rows[$i];
                }

                if (isset($arr_data_eplanning)) {

                    foreach ($arr_data_eplanning as $c => $v) {
                        $fields = explode(";", $v);

                        $espacio = $fields[0];
                        $imps = $fields[1];
                        $clicks = $fields[2];
                        $ctr = $fields[3];

                        $reporte[] = array('espacio' => $espacio, 'imps' => $imps, 'clicks' => $clicks, 'ctr' => $ctr);
                    }

                    $data['columnas'] = $arr_columnas;
                    $data['reporte'] = $reporte;

                    $this->load->view('tbl_report_eplanning_por_anuncio', $data);
                } else {
                    $data['reporte'] = null;
                    $data['columnas'] = $arr_columnas;

                    $this->load->view('tbl_report_eplanning_por_anuncio', $data);
                }
            } elseif ($intervalo == "day") {
                $datos = obtener_reporte_por_anuncio_por_dia($id_anunciante, $fecha_inicio, $fecha_fin);

                $datos = utf8_urldecode($datos);
                $rows = explode("\n", $datos);

                $arr_data_eplanning = null;

                for ($i = 1; $i < count($rows); $i++) {
                    if (strlen($rows[$i]) > 0)
                        $arr_data_eplanning[] = $rows[$i];
                }

                if (isset($arr_data_eplanning)) {

                    foreach ($arr_data_eplanning as $c => $v) {
                        $fields = explode(";", $v);

                        $fecha = $fields[0];
                        $espacio = $fields[1];
                        $imps = $fields[2];
                        $clicks = $fields[3];
                        $ctr = $fields[4];

                        $reporte[] = array('fecha' => $fecha, 'espacio' => $espacio, 'imps' => $imps, 'clicks' => $clicks, 'ctr' => $ctr);
                    }

                    $data['columnas'] = $arr_columnas;
                    $data['reporte'] = $reporte;

                    $this->load->view('tbl_report_eplanning_por_anuncio_por_dia', $data);
                } else {
                    $data['reporte'] = null;
                    $data['columnas'] = $arr_columnas;

                    $this->load->view('tbl_report_eplanning_por_anuncio_por_dia', $data);
                }
            } elseif ($intervalo == "month") {
                $datos = obtener_reporte_por_anuncio_por_mes($id_anunciante, $fecha_inicio, $fecha_fin);

                $datos = utf8_urldecode($datos);
                $rows = explode("\n", $datos);

                $arr_data_eplanning = null;

                for ($i = 1; $i < count($rows); $i++) {
                    if (strlen($rows[$i]) > 0)
                        $arr_data_eplanning[] = $rows[$i];
                }

                if (isset($arr_data_eplanning)) {

                    foreach ($arr_data_eplanning as $c => $v) {
                        $fields = explode(";", $v);

                        $fecha = $fields[0];
                        $espacio = $fields[1];
                        $imps = $fields[2];
                        $clicks = $fields[3];
                        $ctr = $fields[4];

                        $reporte[] = array('fecha' => $fecha, 'espacio' => $espacio, 'imps' => $imps, 'clicks' => $clicks, 'ctr' => $ctr);
                    }

                    $data['reporte'] = $reporte;
                    $data['columnas'] = $arr_columnas;
                    $this->load->view('tbl_report_eplanning_por_anuncio_por_mes', $data);
                } else {
                    $data['reporte'] = null;
                    $data['columnas'] = $arr_columnas;

                    $this->load->view('tbl_report_eplanning_por_anuncio_por_mes', $data);
                }
            }
        }

        if ($grupo == "geo_country") {
            if ($intervalo == "cumulative") {

                $datos = obtener_reporte_por_pais($id_anunciante, $fecha_inicio, $fecha_fin);

                $datos = utf8_urldecode($datos);
                $rows = explode("\n", $datos);

                $arr_data_eplanning = null;

                for ($i = 1; $i < count($rows); $i++) {
                    if (strlen($rows[$i]) > 0)
                        $arr_data_eplanning[] = $rows[$i];
                }

                if (isset($arr_data_eplanning)) {

                    foreach ($arr_data_eplanning as $c => $v) {
                        $fields = explode(";", $v);

                        $pais = str_replace(".", "", $fields[0]);
                        $imps = $fields[1];
                        $clicks = $fields[2];
                        $ctr = $fields[3];

                        if ($arr_paises) {
                            for ($i = 0; $i < count($arr_paises); $i++) {
                                if ($arr_paises[$i] == $pais)
                                    $reporte[] = array('pais' => $pais, 'imps' => $imps, 'clicks' => $clicks, 'ctr' => $ctr);
                            }
                        }else {
                            $reporte[] = array('pais' => $pais, 'imps' => $imps, 'clicks' => $clicks, 'ctr' => $ctr);
                        }
                    }

                    $data['reporte'] = $reporte;
                    $data['columnas'] = $arr_columnas;
                    $this->load->view('tbl_report_eplanning_por_pais', $data);
                } else {
                    $data['reporte'] = null;
                    $data['columnas'] = $arr_columnas;

                    $this->load->view('tbl_report_eplanning_por_pais', $data);
                }
            } elseif ($intervalo == "day") {
                $datos = obtener_reporte_por_pais_por_dia($id_anunciante, $fecha_inicio, $fecha_fin);

                $datos = utf8_urldecode($datos);
                $rows = explode("\n", $datos);

                $arr_data_eplanning = null;

                for ($i = 1; $i < count($rows); $i++) {
                    if (strlen($rows[$i]) > 0)
                        $arr_data_eplanning[] = $rows[$i];
                }

                if (isset($arr_data_eplanning)) {

                    foreach ($arr_data_eplanning as $c => $v) {
                        $fields = explode(";", $v);

                        $fecha = $fields[0];
                        $pais = str_replace(".", "", $fields[1]);
                        $imps = $fields[2];
                        $clicks = $fields[3];
                        $ctr = $fields[4];

                        if ($arr_paises) {
                            for ($i = 0; $i < count($arr_paises); $i++) {
                                if ($arr_paises[$i] == $pais)
                                    $reporte[] = array('fecha' => $fecha, 'pais' => $pais, 'imps' => $imps, 'clicks' => $clicks, 'ctr' => $ctr);
                            }
                        }else {
                            $reporte[] = array('fecha' => $fecha, 'pais' => $pais, 'imps' => $imps, 'clicks' => $clicks, 'ctr' => $ctr);
                        }
                    }

                    $data['reporte'] = $reporte;
                    $data['columnas'] = $arr_columnas;
                    $this->load->view('tbl_report_eplanning_por_pais_por_dia', $data);
                } else {
                    $data['reporte'] = null;
                    $data['columnas'] = $arr_columnas;

                    $this->load->view('tbl_report_eplanning_por_pais_por_dia', $data);
                }
            } elseif ($intervalo == "month") {
                $datos = obtener_reporte_por_pais_por_mes($id_anunciante, $fecha_inicio, $fecha_fin);

                $datos = utf8_urldecode($datos);
                $rows = explode("\n", $datos);

                $arr_data_eplanning = null;

                for ($i = 1; $i < count($rows); $i++) {
                    if (strlen($rows[$i]) > 0)
                        $arr_data_eplanning[] = $rows[$i];
                }

                if (isset($arr_data_eplanning)) {

                    foreach ($arr_data_eplanning as $c => $v) {
                        $fields = explode(";", $v);

                        $fecha = $fields[0];
                        $pais = str_replace(".", "", $fields[1]);
                        $imps = $fields[2];
                        $clicks = $fields[3];
                        $ctr = $fields[4];

                        if ($arr_paises) {
                            for ($i = 0; $i < count($arr_paises); $i++) {
                                if ($arr_paises[$i] == $pais)
                                    $reporte[] = array('fecha' => $fecha, 'pais' => $pais, 'imps' => $imps, 'clicks' => $clicks, 'ctr' => $ctr);
                            }
                        }else {
                            $reporte[] = array('fecha' => $fecha, 'pais' => $pais, 'imps' => $imps, 'clicks' => $clicks, 'ctr' => $ctr);
                        }
                    }

                    $data['reporte'] = $reporte;
                    $data['columnas'] = $arr_columnas;
                    $this->load->view('tbl_report_eplanning_por_pais_por_mes', $data);
                } else {
                    $data['reporte'] = null;
                    $data['columnas'] = $arr_columnas;

                    $this->load->view('tbl_report_eplanning_por_pais_por_mes', $data);
                }
            }
        }

        if (!$grupo) {
            if ($intervalo == "cumulative") {
                $datos = obtener_reporte_por_pais($id_anunciante, $fecha_inicio, $fecha_fin);

                $datos = utf8_urldecode($datos);
                $rows = explode("\n", $datos);

                $arr_data_eplanning = null;

                for ($i = 1; $i < count($rows); $i++) {
                    if (strlen($rows[$i]) > 0)
                        $arr_data_eplanning[] = $rows[$i];
                }

                if (isset($arr_data_eplanning)) {

                    foreach ($arr_data_eplanning as $c => $v) {
                        $fields = explode(";", $v);

                        $imps = $fields[1];
                        $clicks = $fields[2];
                        $ctr = $fields[3];

                        $reporte[] = array('imps' => $imps, 'clicks' => $clicks, 'ctr' => $ctr);
                    }

                    $data['reporte'] = $reporte;
                    $data['columnas'] = $arr_columnas;

                    $this->load->view('tbl_report_eplanning', $data);
                } else {
                    $data['reporte'] = null;
                    $data['columnas'] = $arr_columnas;

                    $this->load->view('tbl_report_eplanning', $data);
                }
            } elseif ($intervalo == "day") {
                $datos = obtener_reporte_por_dia($id_anunciante, $fecha_inicio, $fecha_fin);

                $datos = utf8_urldecode($datos);
                $rows = explode("\n", $datos);

                $arr_data_eplanning = null;

                for ($i = 1; $i < count($rows); $i++) {
                    if (strlen($rows[$i]) > 0)
                        $arr_data_eplanning[] = $rows[$i];
                }

                if (isset($arr_data_eplanning)) {

                    foreach ($arr_data_eplanning as $c => $v) {
                        $fields = explode(";", $v);

                        $fecha = $fields[0];
                        $imps = $fields[1];
                        $clicks = $fields[2];
                        $ctr = $fields[3];

                        $reporte[] = array('fecha' => $fecha, 'imps' => $imps, 'clicks' => $clicks, 'ctr' => $ctr);
                    }

                    $data['reporte'] = $reporte;
                    $data['columnas'] = $arr_columnas;
                    $this->load->view('tbl_report_eplanning_por_dia', $data);
                } else {
                    $data['reporte'] = null;
                    $data['columnas'] = $arr_columnas;

                    $this->load->view('tbl_report_eplanning_por_dia', $data);
                }
            } elseif ($intervalo == "month") {
                $datos = obtener_reporte_por_mes($id_anunciante, $fecha_inicio, $fecha_fin);

                $datos = utf8_urldecode($datos);
                $rows = explode("\n", $datos);

                $arr_data_eplanning = null;

                for ($i = 1; $i < count($rows); $i++) {
                    if (strlen($rows[$i]) > 0)
                        $arr_data_eplanning[] = $rows[$i];
                }

                if (isset($arr_data_eplanning)) {

                    foreach ($arr_data_eplanning as $c => $v) {
                        $fields = explode(";", $v);

                        $fecha = $fields[0];
                        $imps = $fields[1];
                        $clicks = $fields[2];
                        $ctr = $fields[3];

                        $reporte[] = array('fecha' => $fecha, 'imps' => $imps, 'clicks' => $clicks, 'ctr' => $ctr);
                    }

                    $data['reporte'] = $reporte;
                    $data['columnas'] = $arr_columnas;
                    $this->load->view('tbl_report_eplanning_por_mes', $data);
                } else {
                    $data['reporte'] = null;
                    $data['columnas'] = $arr_columnas;

                    $this->load->view('tbl_report_eplanning_por_mes', $data);
                }
            }
        }
    }

    function consultar_adserver() {
        $id_anunciante = $this->input->post("id_anunciante");
        $advertiser = $this->users->get_anunciantes_adservers_by_id($id_anunciante);
        if ($advertiser) {
            switch ($advertiser->adserver_actual) {
                case '0':
                    $id_anunciante_adserver = $advertiser->id_dfp;
                    break;
                case '1':
                    $id_anunciante_adserver = $advertiser->id_appnexus;
                    break;
                case '2':
                    $id_anunciante_adserver = $advertiser->id_eplanning;
                    break;
            }
            echo json_encode(array('validate' => TRUE, 'id_adserver' => $advertiser->adserver_actual, 'id_anunciante_adserver' => $id_anunciante_adserver));
        } else {
            echo json_encode(array('validate' => FALSE));
        }
    }

    function get_filtros_line_items($adserver, $anunciante_id, $seconds, $order_id = '0') {
        $data['line_items'] = $this->api->getLineItemsByAdv($this->token, $adserver, $anunciante_id, $order_id);
        $this->load->view('filtros_line_items', $data);
    }

    function get_filtros_creatives($adserver, $anunciante_id, $seconds) {
        $data['creatives'] = $this->api->getCreativesByAdv($this->token, $adserver, $anunciante_id);
        $this->load->view('filtros_creatives', $data);
    }

    function export_excel() {
        $this->load->view('excel');
    }

    function export_excel_appnexus() {
        $this->load->view('excel_appnexus');
    }

    function export_excel_dfp() {
        $this->load->view('excel_dfp');
    }

    function export_excel_appnexus_category() {
        $this->load->view('excel_appnexus_category');
    }

    function create_pdf() {
        $id_lineItem = $_POST['id_lineItem'];

        $total_ctr = 0;
        $total_imps = 0;
        $total_clicks = 0;
        $total_convs = 0;

        $hoy = date("j") . " de " . getMesEsp(date("m")) . " del " . date("Y");

        $rango = trim($this->input->post("rango_" . $id_lineItem));
        $fecha_desde = trim($this->input->post("fecha_inicio_" . $id_lineItem));
        $fecha_hasta = trim($this->input->post("fecha_fin_" . $id_lineItem));

        $total_ctr = $_POST['total_ctr_pdf_' . $id_lineItem];
        $total_imps = $_POST['total_imps_pdf_' . $id_lineItem];
        $total_clicks = $_POST['total_clicks_pdf_' . $id_lineItem];
        $total_convs = $_POST['total_convs_pdf_' . $id_lineItem];
        $total_costo = $_POST['total_costo_pdf_' . $id_lineItem];
        $empresa_campania = $_POST['empresa_campania_orden_pdf_' . $id_lineItem];

        $id_adserver = trim($this->input->post("id_adserver_" . $id_lineItem));

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

        $grupos = trim($this->input->post("grupos_" . $id_lineItem));
        $columnas = trim($this->input->post("columnas_" . $id_lineItem));
        $anunciante = trim($this->input->post("nombre_anunciante_" . $id_lineItem));

        $orden = trim($this->input->post("nombre_orden_" . $id_lineItem));

        $partes_grupos = explode(";", $grupos);

        for ($j = 0; $j < count($partes_grupos); $j++) {
            if (!empty($partes_grupos[$j])) {
                $col = $this->columnas->get_columna_by_id($partes_grupos[$j]);
                $arr_txt_agrupado[] = $col->descripcion;
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

        $partes_columnas = explode(";", $columnas);

        for ($j = 0; $j < count($partes_columnas); $j++) {
            if (!empty($partes_columnas[$j])) {
                $col = $this->columnas->get_columna_by_id($partes_columnas[$j]);
                $arr_txt_columnas[] = $col->descripcion;
            }
        }

        $texto_columnas = "";
        if (isset($arr_txt_columnas)) {
            for ($x = 0; $x < count($arr_txt_columnas); $x++) {
                if ($x == 0) {
                    $texto_columnas = $texto_columnas . " " . $arr_txt_columnas[$x];
                } else {
                    $texto_columnas = $texto_columnas . ", " . $arr_txt_columnas[$x];
                }
            }
        }

        $data_table = $this->input->post("tabla_pdf_" . $id_lineItem);

        $tx = new tableExtractor;

        $tx->source = $data_table;
        $tx->anchor = '<h2>MediaFem</h2>';
        $tx->anchorWithin = true;
        $arr_tabla = null;
        $arr_tabla = $tx->extractTable();

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

        $texto_formato = "";

        if ($id_adserver == "1") {
            $texto_formato = "(Formatos Tradicionales)";
        } elseif ($id_adserver == "2") {
            $texto_formato = "(Formatos Rich-Media)";
        }

        if ($empresa_campania == 0) {
            $color_titulos = '#943634';
            $color_encabezados_tabla = '#D99795';
            $color_fila = '#E2E4FF';
        } else {
            $color_titulos = '#89C003';
            $color_encabezados_tabla = '#89C003';
            $color_fila = '#E3FEA5';
        }

        $html = '<html>
                    <body style="font-family: Arial,Helvetica,sans-serif;">';

        $html .= '<div>
                    <table style="width:100%;">
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="text-align:right">' . $hoy . '</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="color:' . $color_titulos . ';"><b>Anunciante: <u>' . $anunciante . '</u></b></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="color:' . $color_titulos . ';"><b>Campa&ntilde;a: <u>' . $orden . '</u></b></td>
                    </tr>
                    ';

        if (strlen($agrupado_por) && strlen($texto_columnas)) {
            $html .= '<tr>
                        <td>Reporte de ' . $texto_columnas . ' agrupado por ' . $agrupado_por . '</td>
                    </tr>';
        }

        if ($rango != "lifetime") {
            if ($fecha_inicio != '' && $fecha_fin != '') {
                $html .= '<tr>
                                <td>Del ' . $fecha_inicio . ' al ' . $fecha_fin . '</td>
                            </tr>';
            }
        }

        $html .= '<tr>
                    <td>&nbsp;</td>
                </tr>
            </table>';

        $html .= '<table CELLPADDING="0px" CELLSPACING="0px" style="width:100%">
            <tr>';

        for ($i = 0; $i < count($columnas); $i++) {
            if ($i == 0) {
                $html.= '<td style="text-align:center;padding:4px;font-weight:bold;background-color: ' . $color_encabezados_tabla . ';border-top: 1px solid #000;border-right: 1px solid #000;border-left: 1px solid #000;color:#FFF" >' . $columnas[$i] . '</td>';
            } else {
                $html.= '<td style="text-align:center;padding:4px;font-weight:bold;background-color: ' . $color_encabezados_tabla . ';border-top: 1px solid #000;border-right: 1px solid #000;color:#FFF" >' . $columnas[$i] . '</td>';
            }
        }

        $html .= '</tr>';
        $contador = 0;

        foreach ($data_tabla as $row) {
            $contador++;

            if ($contador > 1) {
                $color = 'background-color:' . $color_fila;
                $contador = 0;
            } else {
                $color = 'background-color:#FFFFFF';
            }
            $html .= '<tr>';

            for ($i = 0; $i < count($columnas); $i++) {
                if ($i == 0) {
                    $html .= '<td style="text-align:center;padding-right:4px;padding-left:4px;border-top: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;' . $color . '">' . $row[$columnas[$i]] . '</td>';
                } else {
                    $html .= '<td style="text-align:center;padding-right:4px;padding-left:4px;border-top: 1px solid #000;border-right: 1px solid #000;' . $color . '">' . $row[$columnas[$i]] . '</td>';
                }
            }
            $html .= '</tr>';
        }

        $html .= '<tr style="background-color: ' . $color_encabezados_tabla . ';">';

        for ($i = 0; $i < count($columnas); $i++) {
            if ($i == 0) {
                if ($columnas[$i] == "Clicks") {
                    $html.='<td style="text-align:center;color:#000;padding-right:4px;padding-left:4px;font-weight:bold;border-top: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;border-left: 1px solid #000;">' . $total_clicks . '</td>';
                } elseif ($columnas[$i] == "Impresiones") {
                    $html.='<td style="text-align:center;color:#000;padding-right:4px;padding-left:4px;font-weight:bold;border-top: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;border-left: 1px solid #000;">' . $total_imps . '</td>';
                } elseif ($columnas[$i] == "Conversiones") {
                    $html.='<td style="text-align:center;color:#000;padding-right:4px;padding-left:4px;font-weight:bold;border-top: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;border-left: 1px solid #000;">' . $total_convs . '</td>';
                } elseif ($columnas[$i] == "CTR") {
                    $html.='<td style="text-align:center;color:#000;padding-right:4px;padding-left:4px;font-weight:bold;border-top: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;border-left: 1px solid #000;">' . $total_ctr . '</td>';
                } elseif ($columnas[$i] == "Costo") {
                    $html.='<td style="text-align:center;color:#000;padding-right:4px;padding-left:4px;font-weight:bold;border-top: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;border-left: 1px solid #000;">' . $total_costo . '</td>';
                } else {
                    $html.='<td style="text-align:center;color:#000;padding-right:4px;padding-left:4px;font-weight:bold;border-top: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;border-left: 1px solid #000;">&nbsp;</td>';
                }
            } else {
                if ($columnas[$i] == "Clicks") {
                    $html.='<td style="text-align:center;color:#000;padding-right:4px;padding-left:4px;font-weight:bold;border-top: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;">' . $total_clicks . '</td>';
                } elseif ($columnas[$i] == "Impresiones") {
                    $html.='<td style="text-align:center;color:#000;padding-right:4px;padding-left:4px;font-weight:bold;border-top: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;">' . $total_imps . '</td>';
                } elseif ($columnas[$i] == "Conversiones") {
                    $html.='<td style="text-align:center;color:#000;padding-right:4px;padding-left:4px;font-weight:bold;border-top: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;">' . $total_convs . '</td>';
                } elseif ($columnas[$i] == "CTR") {
                    $html.='<td style="text-align:center;color:#000;padding-right:4px;padding-left:4px;font-weight:bold;border-top: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;">' . $total_ctr . '</td>';
                } elseif ($columnas[$i] == "Costo") {
                    $html.='<td style="text-align:center;color:#000;padding-right:4px;padding-left:4px;font-weight:bold;border-top: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;">' . $total_costo . '</td>';
                } else {
                    $html.='<td style="text-align:center;color:#000;padding-right:4px;padding-left:4px;font-weight:bold;border-top: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;">&nbsp;</td>';
                }
            }
        }

        $html.='</table>
                </body>
                </html>';

        if ($empresa_campania == 0) {
            $header = '<div style="width:100%;padding-top: 10px;background-color:#E43B8E;padding-left: 10px;padding-bottom:10px">
                        <img alt="MediaFem" height="32px" src="/images/logo.png">
                    </div>';

            $footer = '<div style="height:40px;width: 100%;background-color:#E43B8E;padding-left: 10px;padding-top: 10px;color:#FFF;font-size:12px">

                        <div>E-mail: anunciantes@mediafem.com - Tel.: (+5411) 4243-4000</div>
                    </div>';
        } else {
            $header = '<div style="border-bottom: 2px solid #89C003;
                                   padding: 20px; margin-bottom: 10px;
                                   text-align: center;
                                   width:100%;">
                           <img alt="MediaFem" src="/images/adtomatik_logo.png">
                       </div>';

            $footer = '<div style="border-top: 2px solid #89C003;
                                   color: #89C003;
                                   font: normal normal 0.9em ' . "'Calibri'" . ', Arial, Helvetica, sans-serif;
                                   padding: 10px;
                                   width:100%;">
                           <div style="padding: 5px;">Tel.: +1 786-315-9918</div>
                           <div style="padding: 5px;">&copy; ' . date('Y') . ' AdTomatik by MediaFem LLC.</div>
                       </div>';
        }

        $new_name = str_replace('&amp;', '&', $anunciante);

        if (!$new_name)
            $new_name = $anunciante;

        $this->load->library('mpdf');

        $this->mpdf->SetHTMLFooter($footer);
        $this->mpdf->SetHTMLHeader($header);
        $this->mpdf->SetMargins(0, 0, 30, 26);
        $this->mpdf->WriteHTML($html);


        if ($empresa_campania == 0) {
            if ($rango == "lifetime") {
                $this->mpdf->Output("MediaFem - " . $orden . ".pdf", "D");
            } else {
                $this->mpdf->Output("MediaFem - " . $orden . " - $fecha_inicio al $fecha_fin.pdf", "D");
            }
        } else {
            if ($rango == "lifetime") {
                $this->mpdf->Output("AdTomatik - " . $orden . ".pdf", "D");
            } else {
                $this->mpdf->Output("AdTomatik - " . $orden . " - $fecha_inicio al $fecha_fin.pdf", "D");
            }
        }
    }

    function create_pdf_dfp() {

        $total_ctr = 0;
        $total_imps = 0;
        $total_clicks = 0;
        $total_views = 0;
        $total_convs = 0;

        $id_orden = $this->input->post("id_orden_pdf");

        $hoy = date("j") . " de " . getMesEsp(date("m")) . " del " . date("Y");

        $rango = trim($this->input->post("rango"));
        $fecha_desde = trim($this->input->post("fecha_inicio"));
        $fecha_hasta = trim($this->input->post("fecha_fin"));

        $total_ctr = $_POST['total_ctr_pdf'];
        $total_imps = $_POST['total_imps_pdf'];
        $total_clicks = $_POST['total_clicks_pdf'];
        $total_views = $_POST['total_views_pdf'];
        $total_convs = $_POST['total_convs_pdf'];
        $total_costo = $_POST['total_costo_pdf'];

        $id_adserver = trim($this->input->post("id_adserver"));

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

        $grupos = trim($this->input->post("grupos"));
        $columnas = trim($this->input->post("columnas"));
        $anunciante = trim($this->input->post("nombre_anunciante"));
        $orden = trim($this->input->post("nombre_orden"));

        $partes_grupos = explode(";", $grupos);

        for ($j = 0; $j < count($partes_grupos); $j++) {
            if (!empty($partes_grupos[$j])) {
                $col = $this->columnas->get_columna_by_id($partes_grupos[$j]);
                $arr_txt_agrupado[] = $col->descripcion;
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

        $partes_columnas = explode(";", $columnas);

        for ($j = 0; $j < count($partes_columnas); $j++) {
            if (!empty($partes_columnas[$j])) {
                $col = $this->columnas->get_columna_by_id($partes_columnas[$j]);
                $arr_txt_columnas[] = $col->descripcion;
            }
        }

        $texto_columnas = "";
        if (isset($arr_txt_columnas)) {
            for ($x = 0; $x < count($arr_txt_columnas); $x++) {
                if ($x == 0) {
                    $texto_columnas = $texto_columnas . " " . $arr_txt_columnas[$x];
                } else {
                    $texto_columnas = $texto_columnas . ", " . $arr_txt_columnas[$x];
                }
            }
        }

        $data_table = $this->input->post("tabla_pdf");

        $tx = new tableExtractor;

        $tx->source = $data_table;
        $tx->anchor = '<h2>MediaFem</h2>';
        $tx->anchorWithin = true;
        $arr_tabla = null;
        $arr_tabla = $tx->extractTable();

        $columnas = null;
        $verificar_col = true;

        $cont = 0;

        foreach ($arr_tabla as $clave => $row) {
            $cont++;

            //if ($i == 1)
            //   continue;

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

                if ($cont == 1) {
                    $data_totales[] = $data;
                } else {
                    $data_tabla[] = $data;
                }
            }
        }

        $texto_formato = "";

        if ($id_adserver == "1") {
            $texto_formato = "(Formatos Tradicionales)";
        } elseif ($id_adserver == "2") {
            $texto_formato = "(Formatos Rich-Media)";
        }

        $html = '<html>
                    <body style="font-family: Arial,Helvetica,sans-serif;">';

        $html .= '<div>
                    <table style="width:100%;">
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="text-align:right">' . $hoy . '</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="color:#943634;"><b><u>' . $anunciante . '</u></b></td>
                    </tr>
                    <tr>
                        <td style="color:#943634;"><b>Orden: </b>' . $orden . '</td>
                    </tr>';

        if (strlen($agrupado_por) && strlen($texto_columnas)) {
            $html .= '<tr>
                        <td>Reporte de ' . $texto_columnas . ' agrupado por ' . $agrupado_por . '</td>
                    </tr>';
        }

        if ($rango != "lifetime") {
            if ($fecha_inicio != '' || $fecha_fin != '') {
                $html .= '<tr>
                    <td>Del ' . $fecha_inicio . ' al ' . $fecha_fin . '</td>
                </tr>';
            }
        }

        $html .= '<tr>
                    <td>&nbsp;</td>
                </tr>
            </table>';

        $html .= '<table CELLPADDING="0px" CELLSPACING="0px" style="width:100%">
            <tr>';

        for ($i = 0; $i < count($columnas); $i++) {
            if ($i == 0) {
                $html.= '<td style="text-align:center;padding:4px;font-weight:bold;background-color: #D99795;border-top: 1px solid #000;border-right: 1px solid #000;border-left: 1px solid #000;color:#FFF" >' . $columnas[$i] . '</td>';
            } else {
                $html.= '<td style="text-align:center;padding:4px;font-weight:bold;background-color: #D99795;border-top: 1px solid #000;border-right: 1px solid #000;color:#FFF" >' . $columnas[$i] . '</td>';
            }
        }

        $html .= '</tr>';
        $contador = 0;

        foreach ($data_tabla as $row) {
            $contador++;

            if ($contador > 1) {
                $color = 'background-color:#E2E4FF';
                $contador = 0;
            } else {
                $color = 'background-color:#FFFFFF';
            }
            $html .= '<tr>';

            for ($i = 0; $i < count($columnas); $i++) {
                if ($i == 0) {
                    $html .= '<td style="text-align:center;padding-right:4px;padding-left:4px;border-top: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;' . $color . '">' . $row[$columnas[$i]] . '</td>';
                } else {
                    $html .= '<td style="text-align:center;padding-right:4px;padding-left:4px;border-top: 1px solid #000;border-right: 1px solid #000;' . $color . '">' . $row[$columnas[$i]] . '</td>';
                }
            }
            $html .= '</tr>';
        }
        
        foreach ($data_totales as $row) {
            $html .= '<tr style="background-color: #D99795;">';

            for ($i = 0; $i < count($columnas); $i++) {
                if ($i == 0) {
                    $html .= '<td style="text-align:center;padding-right:4px;padding-left:4px;border-top: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;border-bottom: 1px solid #000;' . $color . '">' . $row[$columnas[$i]] . '</td>';
                } else {
                    $html .= '<td style="text-align:center;padding-right:4px;padding-left:4px;border-top: 1px solid #000;border-right: 1px solid #000;border-bottom: 1px solid #000;' . $color . '">' . $row[$columnas[$i]] . '</td>';
                }
            }
            $html .= '</tr>';
        }
        
        //$html .= '<tr style="background-color: #D99795;">';
        /*
        for ($i = 0; $i < count($columnas); $i++) {
            if ($i == 0) {
                if ($columnas[$i] == "Clicks") {
                    $html.='<td style="text-align:center;color:#000;padding-right:4px;padding-left:4px;font-weight:bold;border-top: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;border-left: 1px solid #000;">' . $total_clicks . '</td>';
                } elseif ($columnas[$i] == "Impresiones") {
                    $html.='<td style="text-align:center;color:#000;padding-right:4px;padding-left:4px;font-weight:bold;border-top: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;border-left: 1px solid #000;">' . $total_imps . '</td>';
                } elseif ($columnas[$i] == "Vistas") {
                    $html.='<td style="text-align:center;color:#000;padding-right:4px;padding-left:4px;font-weight:bold;border-top: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;border-left: 1px solid #000;">' . $total_views . '</td>';
                } elseif ($columnas[$i] == "Conversiones") {
                    $html.='<td style="text-align:center;color:#000;padding-right:4px;padding-left:4px;font-weight:bold;border-top: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;border-left: 1px solid #000;">' . $total_convs . '</td>';
                } elseif ($columnas[$i] == "CTR") {
                    $html.='<td style="text-align:center;color:#000;padding-right:4px;padding-left:4px;font-weight:bold;border-top: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;border-left: 1px solid #000;">' . $total_ctr . '</td>';
                } elseif ($columnas[$i] == "Costo") {
                    $html.='<td style="text-align:center;color:#000;padding-right:4px;padding-left:4px;font-weight:bold;border-top: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;border-left: 1px solid #000;">' . $total_costo . '</td>';
                } else {
                    $html.='<td style="text-align:center;color:#000;padding-right:4px;padding-left:4px;font-weight:bold;border-top: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;border-left: 1px solid #000;">&nbsp;</td>';
                }
            } else {
                if ($columnas[$i] == "Clicks") {
                    $html.='<td style="text-align:center;color:#000;padding-right:4px;padding-left:4px;font-weight:bold;border-top: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;">' . $total_clicks . '</td>';
                } elseif ($columnas[$i] == "Impresiones") {
                    $html.='<td style="text-align:center;color:#000;padding-right:4px;padding-left:4px;font-weight:bold;border-top: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;">' . $total_imps . '</td>';
                } elseif ($columnas[$i] == "Vistas") {
                    $html.='<td style="text-align:center;color:#000;padding-right:4px;padding-left:4px;font-weight:bold;border-top: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;">' . $total_views . '</td>';
                } elseif ($columnas[$i] == "Conversiones") {
                    $html.='<td style="text-align:center;color:#000;padding-right:4px;padding-left:4px;font-weight:bold;border-top: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;">' . $total_convs . '</td>';
                } elseif ($columnas[$i] == "CTR") {
                    $html.='<td style="text-align:center;color:#000;padding-right:4px;padding-left:4px;font-weight:bold;border-top: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;">' . $total_ctr . '</td>';
                } elseif ($columnas[$i] == "Costo") {
                    $html.='<td style="text-align:center;color:#000;padding-right:4px;padding-left:4px;font-weight:bold;border-top: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;">' . $total_costo . '</td>';
                } else {
                    $html.='<td style="text-align:center;color:#000;padding-right:4px;padding-left:4px;font-weight:bold;border-top: 1px solid #000;border-bottom: 1px solid #000;border-right: 1px solid #000;">&nbsp;</td>';
                }
            }
        }*/

        $html.='</table>
                </body>
                </html>';

        $header = '<div style="width:100%;padding-top: 10px;background-color:#E43B8E;padding-left: 10px;padding-bottom:10px">
                        <img alt="MediaFem" height="32px" src="/images/logo.png">
                    </div>';

        $footer = '<div style="height:40px;width: 100%;background-color:#E43B8E;padding-left: 10px;padding-top: 10px;color:#FFF;font-size:12px">

                        <div>E-mail: anunciantes@mediafem.com - Tel.: (+5411) 4243-4000</div>
                    </div>';

        $new_name = str_replace('&amp;', '&', $anunciante);

        if (!$new_name)
            $new_name = $anunciante;

        $this->load->library('mpdf');

        $this->mpdf->SetHTMLFooter($footer);
        $this->mpdf->SetHTMLHeader($header);
        $this->mpdf->SetMargins(0, 0, 30, 26);
        $this->mpdf->WriteHTML($html);

        if ($rango == "lifetime") {
            $this->mpdf->Output("MediaFem - " . $orden . ".pdf", "D");
        } else {
            $this->mpdf->Output("MediaFem - " . $orden . " - $fecha_inicio al $fecha_fin.pdf", "D");
        }
    }

    function esta_en_mantenimiento($constant) {
        $mantenimiento = $this->constants->get_constant_by_id($constant);

        if ($mantenimiento->value == 1) {
            $this->load->model('mensajemantenimiento');
            $data['mensaje'] = $this->mensajemantenimiento->get_mensaje_mantenimiendo_by_estado('A')->contenido;

            $this->load->view('mantenimiento_view', $data);
        } else {
            return false;
        }
    }

}
