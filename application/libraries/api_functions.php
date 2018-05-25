<?php

function obtener_reporte_por_anuncio($id_anunciante, $fecha_inicio, $fecha_fin) {
    try {
        $request = new Request();
        $request->method = 'post';

        $request->uri = "https://admin.ar.e-planning.net/admin/adnet/pub/stats/informe.csv?fecha_inicio=$fecha_inicio&fecha_fin=$fecha_fin&anunciante_id=$id_anunciante&informe_id=96";

        $res = Caller::call_eplanning($request);
        return htmlentities($res);
    } catch (Exception $ex) {
        return false;
    }
}

function obtener_reporte_por_anuncio_por_dia($id_anunciante, $fecha_inicio, $fecha_fin) {
    try {
        $request = new Request();
        $request->method = 'post';

        $request->uri = "https://admin.ar.e-planning.net/admin/adnet/pub/stats/informe.csv?fecha_inicio=$fecha_inicio&fecha_fin=$fecha_fin&anunciante_id=$id_anunciante&informe_id=95";

        $res = Caller::call_eplanning($request);
        return htmlentities($res);
    } catch (Exception $ex) {
        return false;
    }
}

function obtener_reporte_por_anuncio_por_mes($id_anunciante, $fecha_inicio, $fecha_fin) {
    try {
        $request = new Request();
        $request->method = 'post';

        $request->uri = "https://admin.ar.e-planning.net/admin/adnet/pub/stats/informe.csv?fecha_inicio=$fecha_inicio&fecha_fin=$fecha_fin&anunciante_id=$id_anunciante&informe_id=94";

        $res = Caller::call_eplanning($request);
        return htmlentities($res);
    } catch (Exception $ex) {
        return false;
    }
}

function obtener_reporte_por_pais($id_anunciante, $fecha_inicio, $fecha_fin) {
    try {
        $request = new Request();
        $request->method = 'post';

        $request->uri = "https://admin.ar.e-planning.net/admin/adnet/pub/stats/informe.csv?fecha_inicio=$fecha_inicio&fecha_fin=$fecha_fin&anunciante_id=$id_anunciante&informe_id=99";

        $res = Caller::call_eplanning($request);
        return htmlentities($res);
    } catch (Exception $ex) {
        return false;
    }
}

function obtener_reporte_por_pais_por_dia($id_anunciante, $fecha_inicio, $fecha_fin) {
    try {
        $request = new Request();
        $request->method = 'post';

        $request->uri = "https://admin.ar.e-planning.net/admin/adnet/pub/stats/informe.csv?fecha_inicio=$fecha_inicio&fecha_fin=$fecha_fin&anunciante_id=$id_anunciante&informe_id=98";

        $res = Caller::call_eplanning($request);
        return htmlentities($res);
    } catch (Exception $ex) {
        return false;
    }
}

function obtener_reporte_por_pais_por_mes($id_anunciante, $fecha_inicio, $fecha_fin) {
    try {
        $request = new Request();
        $request->method = 'post';

        $request->uri = "https://admin.ar.e-planning.net/admin/adnet/pub/stats/informe.csv?fecha_inicio=$fecha_inicio&fecha_fin=$fecha_fin&anunciante_id=$id_anunciante&informe_id=97";

        $res = Caller::call_eplanning($request);
        return htmlentities($res);
    } catch (Exception $ex) {
        return false;
    }
}

function obtener_reporte_por_mes($id_anunciante, $fecha_inicio, $fecha_fin) {
    try {
        $request = new Request();
        $request->method = 'post';

        $request->uri = "https://admin.ar.e-planning.net/admin/adnet/pub/stats/informe.csv?fecha_inicio=$fecha_inicio&fecha_fin=$fecha_fin&anunciante_id=$id_anunciante&informe_id=93";

        $res = Caller::call_eplanning($request);
        return htmlentities($res);
    } catch (Exception $ex) {
        return false;
    }
}

function obtener_reporte_por_dia($id_anunciante, $fecha_inicio, $fecha_fin) {
    try {
        $request = new Request();
        $request->method = 'post';

        $request->uri = "https://admin.ar.e-planning.net/admin/adnet/pub/stats/informe.csv?fecha_inicio=$fecha_inicio&fecha_fin=$fecha_fin&anunciante_id=$id_anunciante&informe_id=87";

        $res = Caller::call_eplanning($request);
        return htmlentities($res);
    } catch (Exception $ex) {
        return false;
    }
}

function listar_anunciantes() {
    try {
        $request = new Request();
        $request->method = 'get';

        $request->uri = BASE_URI_EPLANNING . "/admin/adnet/pub/admin/anunciantes.html?op=l&o=xml";

        $res = Caller::call_eplanning($request);
        return $res;
    } catch (Exception $ex) {
        return false;
    }
}

function getAuthToken($username, $password) {
    try {
        $data = new stdClass();
        $data->auth->username = $username;
        $data->auth->password = $password;
        $request = new Request();
        $request->method = 'post';      // send http POST
        $request->uri = BASE_URI . '/auth'; // calling auth service
        $request->data = $data; // post data
        //var_dump($data);
        $res = Caller::call($request);
        return $res->response->token;
    } catch (Exception $ex) {
        return false;
    }
}

function getAdvertisers($token) {
    try {
        $request = new Request();
        $request->method = 'get';
        $request->token = $token;
        $request->uri = BASE_URI . '/advertiser';
        $res = Caller::call($request);
        return $res->response->advertisers;
    } catch (Exception $ex) {
        return false;
    }
}

function getAdvertiserById($token, $adv_id) {
    try {
        $request = new Request();
        $request->method = 'get';
        $request->token = $token;
        $request->uri = BASE_URI . '/advertiser?id=' . $adv_id;
        $res = Caller::call($request);

        if ($res == "NO_EXISTE") {
            return false;
        }
        while (!$res) {
            $res = Caller::call($request);
            if ($res == "NO_EXISTE") {
                return false;
            }
        }
        return $res->response->advertiser;
    } catch (Exception $ex) {
        return false;
    }
}

function getLineItemsByAdv($token, $adv_id) {
    try {
        $request = new Request();
        $request->method = 'get';
        $request->token = $token;
        $request->uri = BASE_URI . '/line-item?advertiser_id=' . $adv_id;
        $res = Caller::call($request);
        while (!isset($res->{'response'}->{'line-items'})) {
            $res = Caller::call($request);
        }
        return $res->response;
    } catch (Exception $ex) {
        return false;
    }
}

function getCreativesByAdv($token, $adv_id) {
    try {
        $request = new Request();
        $request->method = 'get';
        $request->token = $token;
        $request->uri = BASE_URI . '/creative?advertiser_id=' . $adv_id;
        $res = Caller::call($request);

        while ($res == "RATE_EXCEEDED") {
            $res = Caller::call($request);
        }

        if (!isset($res->response->creatives)) {
            return false;
        }

        return $res->response;
    } catch (Exception $ex) {
        return false;
    }
}

function getAdvertiserDynamicReport($token, $id, $interval, $filtros, $grupos, $columnas, $start_date, $end_date, $timezone, $orden, $direccion) {
    try {

        $data = new stdClass();
        $data->report = new stdClass();
        $data->report->report_type = "network_advertiser_analytics";
        //$data->report->report_type = "network_analytics";

        $data->report->columns = $columnas;
        $data->report->row_per = array('day');
        $data->report->report_interval = $interval;
        $data->report->start_date = $start_date;
        $data->report->end_date = $end_date;
        //$data->report->timezone = $timezone;
        $data->report->timezone = 'UTC';

        $data->report->filters = $filtros;
        $data->report->filters->fixed_columns = array('day');

        $data->report->orders = array((object) array("order_by" => $orden, "direction" => "ASC"));

        $request = new Request();
        $request->method = 'post';
        $request->uri = BASE_URI . '/report?advertiser_id=' . $id;
        $request->token = $token;
        $request->data = $data;

        $res = Caller::call($request);

        //var_dump($request);

        while ($res == "RATE_EXCEEDED") {
            $res = Caller::call($request);
        }

        $id_report = $res->response->report_id;
        /* --------------------------- */
        //echo 'REPORT ID: '.$id_report.'<br/>';

        if (strlen($id_report)) {
            $request_report = new Request();
            $request_report->method = 'get';
            $request_report->uri = BASE_URI . '/report?id=' . $id_report;
            $request_report->token = $token;
            $res_report = Caller::call($request_report);
            //while (!isset($res_report->response->report->data)) {
            while ($res_report == "RATE_EXCEEDED" || !isset($res_report->response->report->url)) {
                if (!isset($res_report->response->report->url))
                    sleep(3);
                $res_report = Caller::call($request_report);
            }

            $url_download = $res_report->response->report->url;

            $request_file = new Request();
            $request_file->method = 'get';
            $request_file->token = $token;
            $request_file->uri = BASE_URI . '/' . $url_download;

            $datos = Caller::call_download_report_appnexus($request_file);

            return $datos;

        }
    } catch (Exception $ex) {
        return false;
    }
}

function getNetworkDynamicReport($token, $id, $interval, $filtros, $grupos, $columnas, $start_date, $end_date, $timezone, $orden, $direccion) {

    try {
        $data = new stdClass();
        $data->report->report_type = "network_analytics";

        $data->report->columns = $columnas;
        $data->report->report_interval = $interval;
        $data->report->start_date = $start_date;
        $data->report->end_date = $end_date;
        //$data->report->timezone = $timezone;
        $data->report->timezone = 'UTC';

        $data->report->filters = $filtros;

        $request = new Request();
        $request->method = 'post';
        $request->uri = BASE_URI . '/report?advertiser_id=' . $id;
        $request->token = $token;
        $request->data = $data;

        $res = Caller::call($request);

        $intentos = 1;
        while ($res == "RATE_EXCEEDED") {
            if ($intentos >= 20) {
                $this->error_al_obtener_reporte = TRUE;
                return FALSE;
            } else {
                $intentos++;
                $res = Caller::call($request);
            }
        }

        $id_report = $res->response->report_id;
        /* --------------------------- */
        //echo 'REPORT ID: '.$id_report.'              ';

        if (strlen($id_report)) {
            $request_report = new Request();
            $request_report->method = 'get';
            $request_report->uri = BASE_URI . '/report?id=' . $id_report;
            $request_report->token = $token;
            $res_report = Caller::call($request_report);

            $intentos = 1;
            while ($res_report == "RATE_EXCEEDED" || !isset($res_report->response->report->url)) {
                if ($intentos >= 20) {
                    $this->error_al_obtener_reporte = TRUE;
                    return FALSE;
                } else {
                    if (!isset($res_report->response->report->url))
                        sleep(3);

                    $intentos++;
                    $res_report = Caller::call($request_report);
                }
            }

            $url_download = $res_report->response->report->url;
            $request_file = new Request();
            $request_file->method = 'get';
            $request_file->token = $token;
            $request_file->uri = BASE_URI . '/' . $url_download;

            $datos = Caller::call_download_report_appnexus($request_file);

            return $datos;
        }
    } catch (Exception $ex) {
        return false;
    }
}

function getCategories($token) {
    try {
        $request = new Request();
        $request->method = 'get';
        $request->token = $token;
        $request->uri = BASE_URI . '/content-category';
        $res = Caller::call($request);
        return $res->{'response'}->{'content-categories'};
        //return $res->response->content-categories;
    } catch (Exception $ex) {
        return false;
    }
}

function getCountries($token) {
    try {
        $request = new Request();
        $request->method = 'get';
        $request->token = $token;
        $request->uri = BASE_URI . '/city';
        $res = Caller::call($request);
        while (!isset($res->response->countries)) {
            $res = Caller::call($request_report);
        }
        return $res->response->countries;
    } catch (Exception $ex) {
        return false;
    }
}

function getSites($token) {
    try {
        $request = new Request();
        $request->method = 'get';
        $request->token = $token;
        $request->uri = BASE_URI . '/site?start_element=0&num_elements=1500';
        $res = Caller::call($request);
        while (!isset($res->response->sites)) {
            sleep(2);
            $res = Caller::call($request);
        }
        return $res->response->sites;
    } catch (Exception $ex) {
        return false;
    }
}

function getCustomCategories($token) {
    try {
        $request = new Request();
        $request->method = 'get';
        $request->token = $token;
        $request->uri = BASE_URI . '/content-category?category_type=custom';
        $res = Caller::call($request);
        while (!isset($res->{'response'}->{'content-categories'})) {
            sleep(2);
            $res = Caller::call($request);
        }
        return $res->{'response'}->{'content-categories'};
        //return $res->response->content-categories;
    } catch (Exception $ex) {
        return false;
    }
}

function obtenerReportePorSitio($token, $interval, $filtro_sitios, $filtro_pais, $imps_minimas) {
    try {

        $arr_order[] = array('order_by' => 'imps', 'direction' => 'DESC');
        $arr_filtros[] = array('site_id' => $filtro_sitios);

        if ($filtro_pais) {
            $arr_filtros[] = array('geo_country' => $filtro_pais);
        }

        if ($imps_minimas)
            $imps[] = array('imps' => array("operator" => ">", "value" => $imps_minimas));

        $data = new stdClass();

        $data->report->report_type = "network_analytics";

        $data->report->special_pixel_reporting = false;
        $data->report->timezone = "EST5EDT";
        $data->report->pivot_report = false;

        $data->report->columns = array("site", "site_id", "imps", "clicks", "ctr");
        $data->report->row_per = array("site_id");
        $data->report->ui_columns = array("site", "site_id", "imps", "clicks", "ctr");

        $data->report->report_interval = $interval;
        $data->report->orders = $arr_order;
        $data->report->filters = $arr_filtros;

        if ($imps_minimas)
            $data->report->group_filters = $imps;

        $request = new Request();
        $request->method = 'post';
        $request->uri = BASE_URI . '/report';
        $request->token = $token;
        $request->data = $data;

        $res = Caller::call($request);

        while ($res == "RATE_EXCEEDED") {
            $res = Caller::call($request);
        }

        $id_report = $res->response->report_id;

        /* --------------------------- */
        //echo 'REPORT ID: '.$id_report.'              ';
        if (strlen($id_report)) {
            $request_report = new Request();
            $request_report->method = 'get';
            $request_report->uri = BASE_URI . '/report?id=' . $id_report;
            $request_report->token = $token;
            $res_report = Caller::call($request_report);
            //var_dump($res_report);
            //die();
            //while (!isset($res_report->response->report->data)) {
            while ($res_report == "RATE_EXCEEDED" || !isset($res_report->response->report->data)) {
                if (!isset($res_report->response->report->data))
                    sleep(3);
                $res_report = Caller::call($request_report);
            }

            return $res_report->response->report;
        }
    } catch (Exception $ex) {
        return false;
    }
}

function obtenerReportePorSitioFechaEspecifica($token, $filtro_sitios, $filtro_pais, $imps_minimas, $start_date, $end_date) {
    try {

        $arr_order[] = array('order_by' => 'imps', 'direction' => 'DESC');
        $arr_filtros[] = array('site_id' => $filtro_sitios);

        if ($filtro_pais) {
            $arr_filtros[] = array('geo_country' => $filtro_pais);
        }

        if ($imps_minimas)
            $imps[] = array('imps' => array("operator" => ">", "value" => $imps_minimas));

        $data = new stdClass();

        $data->report->report_type = "network_analytics";

        $data->report->special_pixel_reporting = false;
        $data->report->timezone = "EST5EDT";
        $data->report->pivot_report = false;

        $data->report->columns = array("site", "site_id", "imps", "clicks", "ctr");
        $data->report->row_per = array("site_id");
        $data->report->ui_columns = array("site", "site_id", "imps", "clicks", "ctr");

        $data->report->start_date = $start_date;
        $data->report->end_date = $end_date;
        $data->report->orders = $arr_order;
        $data->report->filters = $arr_filtros;

        if ($imps_minimas)
            $data->report->group_filters = $imps;

        $request = new Request();
        $request->method = 'post';
        $request->uri = BASE_URI . '/report';
        $request->token = $token;
        $request->data = $data;

        $res = Caller::call($request);

        while ($res == "RATE_EXCEEDED") {
            $res = Caller::call($request);
        }

        $id_report = $res->response->report_id;

        /* --------------------------- */
        //echo 'REPORT ID: '.$id_report.'              ';
        if (strlen($id_report)) {
            $request_report = new Request();
            $request_report->method = 'get';
            $request_report->uri = BASE_URI . '/report?id=' . $id_report;
            $request_report->token = $token;
            $res_report = Caller::call($request_report);
            //var_dump($res_report);
            //die();
            //while (!isset($res_report->response->report->data)) {
            while ($res_report == "RATE_EXCEEDED" || !isset($res_report->response->report->data)) {
                if (!isset($res_report->response->report->data))
                    sleep(3);
                $res_report = Caller::call($request_report);
            }
            return $res_report->response->report;
        }
    } catch (Exception $ex) {
        return false;
    }
}

function obtenerReportePorSitioFormato($token, $interval, $filtro_sitios, $filtro_pais, $imps_minimas) {
    try {

        $arr_order[] = array('order_by' => 'site_id', 'direction' => 'DESC');
        $arr_filtros[] = array('site_id' => $filtro_sitios);

        if ($filtro_pais) {
            $arr_filtros[] = array('geo_country' => $filtro_pais);
        }

        if ($imps_minimas)
            $imps[] = array('imps' => array("operator" => ">", "value" => $imps_minimas));

        $data = new stdClass();

        $data->report->report_type = "network_analytics";

        $data->report->special_pixel_reporting = false;
        $data->report->timezone = "EST5EDT";
        $data->report->pivot_report = false;

        $data->report->columns = array("site", "site_id", "imps", "clicks", "ctr", "size");
        $data->report->row_per = array("site_id", "size");
        $data->report->ui_columns = array("site", "site_id", "imps", "clicks", "ctr", "size");

        $data->report->report_interval = $interval;
        $data->report->orders = $arr_order;
        $data->report->filters = $arr_filtros;

        if ($imps_minimas)
            $data->report->group_filters = $imps;

        $request = new Request();
        $request->method = 'post';
        $request->uri = BASE_URI . '/report';
        $request->token = $token;
        $request->data = $data;

        $res = Caller::call($request);

        while ($res == "RATE_EXCEEDED") {
            $res = Caller::call($request);
        }

        $id_report = $res->response->report_id;

        /* --------------------------- */
        //echo 'REPORT ID: '.$id_report.'              ';
        if (strlen($id_report)) {
            $request_report = new Request();
            $request_report->method = 'get';
            $request_report->uri = BASE_URI . '/report?id=' . $id_report;
            $request_report->token = $token;
            $res_report = Caller::call($request_report);
            //var_dump($res_report);
            //die();
            //while (!isset($res_report->response->report->data)) {
            while ($res_report == "RATE_EXCEEDED" || !isset($res_report->response->report->data)) {
                if (!isset($res_report->response->report->data))
                    sleep(3);
                $res_report = Caller::call($request_report);
            }
            return $res_report->response->report;
        }
    } catch (Exception $ex) {
        return false;
    }
}

function obtenerReportePorSitioFormatoFechaEspecifica($token, $filtro_sitios, $filtro_pais, $imps_minimas, $start_date, $end_date) {
    try {

        $arr_order[] = array('order_by' => 'site_id', 'direction' => 'DESC');
        $arr_filtros[] = array('site_id' => $filtro_sitios);

        if ($filtro_pais) {
            $arr_filtros[] = array('geo_country' => $filtro_pais);
        }

        if ($imps_minimas)
            $imps[] = array('imps' => array("operator" => ">", "value" => $imps_minimas));

        $data = new stdClass();

        $data->report->report_type = "network_analytics";

        $data->report->special_pixel_reporting = false;
        $data->report->timezone = "EST5EDT";
        $data->report->pivot_report = false;

        $data->report->columns = array("site", "site_id", "imps", "clicks", "ctr", "size");
        $data->report->row_per = array("site_id", "size");
        $data->report->ui_columns = array("site", "site_id", "imps", "clicks", "ctr", "size");

        $data->report->start_date = $start_date;
        $data->report->end_date = $end_date;
        $data->report->orders = $arr_order;
        $data->report->filters = $arr_filtros;

        if ($imps_minimas)
            $data->report->group_filters = $imps;

        $request = new Request();
        $request->method = 'post';
        $request->uri = BASE_URI . '/report';
        $request->token = $token;
        $request->data = $data;

        $res = Caller::call($request);

        while ($res == "RATE_EXCEEDED") {
            $res = Caller::call($request);
        }

        $id_report = $res->response->report_id;

        /* --------------------------- */
        //echo 'REPORT ID: '.$id_report.'              ';
        if (strlen($id_report)) {
            $request_report = new Request();
            $request_report->method = 'get';
            $request_report->uri = BASE_URI . '/report?id=' . $id_report;
            $request_report->token = $token;
            $res_report = Caller::call($request_report);
            //var_dump($res_report);
            //die();
            //while (!isset($res_report->response->report->data)) {
            while ($res_report == "RATE_EXCEEDED" || !isset($res_report->response->report->data)) {
                if (!isset($res_report->response->report->data))
                    sleep(3);
                $res_report = Caller::call($request_report);
            }
            return $res_report->response->report;
        }
    } catch (Exception $ex) {
        return false;
    }
}

function obtenerReportePorPais($token, $interval, $filtro_sitios, $filtro_pais, $imps_minimas) {
    try {

        $arr_filtros[] = array('site_id' => $filtro_sitios);

        if ($filtro_pais) {
            $arr_filtros[] = array('geo_country' => $filtro_pais);
        }

        if ($imps_minimas)
            $imps[] = array('imps' => array("operator" => ">", "value" => $imps_minimas));

        $data = new stdClass();

        $data->report->report_type = "network_analytics";

        $data->report->special_pixel_reporting = false;
        $data->report->timezone = "EST5EDT";
        $data->report->pivot_report = false;

        $data->report->columns = array("geo_country", "imps", "clicks");
        $data->report->row_per = array("geo_country");

        $data->report->report_interval = $interval;
        $data->report->filters = $arr_filtros;

        if ($imps_minimas)
            $data->report->group_filters = $imps;

        $request = new Request();
        $request->method = 'post';
        $request->uri = BASE_URI . '/report';
        $request->token = $token;
        $request->data = $data;

        $res = Caller::call($request);

        while ($res == "RATE_EXCEEDED") {
            $res = Caller::call($request);
        }

        $id_report = $res->response->report_id;

        /* --------------------------- */
        //echo 'REPORT ID: '.$id_report.'              ';
        if (strlen($id_report)) {
            $request_report = new Request();
            $request_report->method = 'get';
            $request_report->uri = BASE_URI . '/report?id=' . $id_report;
            $request_report->token = $token;
            $res_report = Caller::call($request_report);
            //var_dump($res_report);
            //die();
            //while (!isset($res_report->response->report->data)) {
            while ($res_report == "RATE_EXCEEDED" || !isset($res_report->response->report->data)) {
                if (!isset($res_report->response->report->data))
                    sleep(3);
                $res_report = Caller::call($request_report);
            }
            return $res_report->response->report;
        }
    } catch (Exception $ex) {
        return false;
    }
}

function obtenerReportePorPaisFechaEspecifica($token, $filtro_sitios, $filtro_pais, $imps_minimas, $start_date, $end_date) {
    try {

        $arr_filtros[] = array('site_id' => $filtro_sitios);

        if ($filtro_pais) {
            $arr_filtros[] = array('geo_country' => $filtro_pais);
        }

        if ($imps_minimas)
            $imps[] = array('imps' => array("operator" => ">", "value" => $imps_minimas));

        $data = new stdClass();

        $data->report->report_type = "network_analytics";

        $data->report->special_pixel_reporting = false;
        $data->report->timezone = "EST5EDT";
        $data->report->pivot_report = false;

        $data->report->columns = array("geo_country_name", "imps", "clicks");
        $data->report->row_per = array("geo_country");

        $data->report->start_date = $start_date;
        $data->report->end_date = $end_date;
        $data->report->filters = $arr_filtros;

        if ($imps_minimas)
            $data->report->group_filters = $imps;

        $request = new Request();
        $request->method = 'post';
        $request->uri = BASE_URI . '/report';
        $request->token = $token;
        $request->data = $data;

        $res = Caller::call($request);

        while ($res == "RATE_EXCEEDED") {
            $res = Caller::call($request);
        }

        $id_report = $res->response->report_id;

        /* --------------------------- */
        //echo 'REPORT ID: '.$id_report.'              ';
        if (strlen($id_report)) {
            $request_report = new Request();
            $request_report->method = 'get';
            $request_report->uri = BASE_URI . '/report?id=' . $id_report;
            $request_report->token = $token;
            $res_report = Caller::call($request_report);
            //var_dump($res_report);
            //die();
            //while (!isset($res_report->response->report->data)) {
            while ($res_report == "RATE_EXCEEDED" || !isset($res_report->response->report->data)) {
                if (!isset($res_report->response->report->data))
                    sleep(2);
                $res_report = Caller::call($request_report);
            }
            return $res_report->response->report;
        }
    } catch (Exception $ex) {
        return false;
    }
}

function obtenerReportePorFormato($token, $interval, $filtro_sitios, $filtro_pais, $imps_minimas) {
    try {

        $arr_filtros[] = array('site_id' => $filtro_sitios);

        if ($filtro_pais) {
            $arr_filtros[] = array('geo_country' => $filtro_pais);
        }

        if ($imps_minimas)
            $imps[] = array('imps' => array("operator" => ">", "value" => $imps_minimas));

        $data = new stdClass();

        $data->report->report_type = "network_analytics";

        $data->report->special_pixel_reporting = false;
        $data->report->timezone = "EST5EDT";
        $data->report->pivot_report = false;

        $data->report->columns = array("size", "imps", "clicks", "ctr");
        $data->report->row_per = array("size");
        $data->report->ui_columns = array("size", "imps", "clicks", "ctr");

        $data->report->report_interval = $interval;
        $data->report->filters = $arr_filtros;

        if ($imps_minimas)
            $data->report->group_filters = $imps;

        $request = new Request();
        $request->method = 'post';
        $request->uri = BASE_URI . '/report';
        $request->token = $token;
        $request->data = $data;

        $res = Caller::call($request);

        while ($res == "RATE_EXCEEDED") {
            $res = Caller::call($request);
        }

        $id_report = $res->response->report_id;

        /* --------------------------- */
        //echo 'REPORT ID: '.$id_report.'              ';
        if (strlen($id_report)) {
            $request_report = new Request();
            $request_report->method = 'get';
            $request_report->uri = BASE_URI . '/report?id=' . $id_report;
            $request_report->token = $token;
            $res_report = Caller::call($request_report);
            //var_dump($res_report);
            //die();
            //while (!isset($res_report->response->report->data)) {
            while ($res_report == "RATE_EXCEEDED" || !isset($res_report->response->report->data)) {
                if (!isset($res_report->response->report->data))
                    sleep(3);
                $res_report = Caller::call($request_report);
            }
            return $res_report->response->report;
        }
    } catch (Exception $ex) {
        return false;
    }
}

?>