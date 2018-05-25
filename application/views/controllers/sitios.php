<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Sitios extends CI_Controller {

    function __construct() {
        parent::__construct();

        $this->load->helper('url');
        $this->load->library('tank_auth');
        $this->load->model('columnas');
        $this->load->model('constants');
        $this->load->model('users');
        $this->load->model('paises');
        $this->load->model('categoriasaexcluir');
        
        $result_multiplicacion_volumen = $this->constants->get_constant_by_id(ID_MULTIPLICAR_VOLUMEN);
        $this->multiplicacion_volumen = $result_multiplicacion_volumen->value;
    }

    function index() {
        if (!$this->tank_auth->is_logged_in()) {
            redirect('/auth/login/');
        } else {

            $arr_rango = array('today' => 'Hoy', 'yesterday' => 'Ayer', 'last_7_days' => 'Ultimos 7 dias',
                'month_to_date' => 'este mes: ' . getMesEsp(date("m")), 'last_month' => 'el mes pasado: ' . getMesEsp(date("m") - 1), 'lifetime' => 'Siempre',
                'especific' => 'Fechas Especificas');

            $paises = $this->paises->get_paises();

            $data['paises'] = $paises;
            $data['arr_rango'] = $arr_rango;
            $data['user_id'] = $this->tank_auth->get_user_id();
            $data['username'] = $this->tank_auth->get_username();

            $this->load->view('sitios_view', $data);
        }
    }

    function export_excel() {
        $this->load->view('excel_sitios');
    }

    function exportar_excel_sitio_formato() {
        $this->load->view('excel_sitios_formatos');
    }

    function exportar_excel_formato() {
        $this->load->view('excel_formatos');
    }

    function exportar_excel_pais() {
        $this->load->view('excel_pais');
    }

    function obtenerReportePorSitios($id_cats, $id_paises, $interval, $imps_minimas, $seconds) {

        $cats_excluir = $this->categoriasaexcluir->get_all_categorias();
        $sites = getSites($this->token);
        $custom_cats = getCustomCategories($this->token);

        if (!$id_cats) {
            foreach ($custom_cats as $row) {
                $filtrar = 0;
                if (!$filtrar) {
                    foreach ($cats_excluir as $row_cat) {
                        if ($row->id == $row_cat->id) {
                            $filtrar = 1;
                        }
                    }
                }
                if (!$filtrar) {
                    $id_cats = $id_cats . $row->id . "o";
                }
            }
        }

        $partes_cats = explode("o", $id_cats);
        $partes_paises = explode("o", $id_paises);

        $arr_sites = null;

        if ($sites) {
            foreach ($sites as $row) {
                $nombres_categorias = "";
                //CATEGORIAS DEL SITIO
                $arr_cats = $row->content_categories;
                if ($arr_cats) {
                    //RECORRO CATEGORIAS DEL SITIO
                    foreach ($arr_cats as $cat) {
                        //RECORRO LAS CATEGORIAS CUSTOMS
                        foreach ($custom_cats as $custom_cat) {
                            //SI LA CATEGORIA CUSTOM ES IGUAL A LA CATEGORIA DEL SITIO
                            if ($custom_cat->id == $cat->id) {
                                $nombres_categorias = $nombres_categorias . $cat->name . ", ";
                            }
                        }
                    }
                    if ($id_cats) {
                        foreach ($arr_cats as $cat) {
                            for ($m = 0; $m < count($partes_cats); $m++) {
                                if (!empty($partes_cats[$m])) {
                                    if ($partes_cats[$m] == $cat->id) {
                                        $arr_sites[] = array('site_name' => $row->name, 'id' => $row->id, 'nombres_categorias' => $nombres_categorias);
                                    }
                                }
                            }
                        }
                    } else {
                        $arr_sites[] = array('site_name' => $row->name, 'id' => $row->id, 'nombres_categorias' => $nombres_categorias);
                    }
                }
            }
        } else {
            echo '<tr>
                        <td colspan="2">No se han encontrado resultados</td>
                    </tr>';
            return;
        }

        foreach ($arr_sites as $row_site) {
            $arr_site_cat[$row_site['id']] = substr($row_site['nombres_categorias'], 0, - 2);
        }

        foreach ($arr_sites as $row_site) {
            $arr_filtro[$row_site['id']] = $row_site['id'];
        }

        $arr_filtro_sitios = array_values(array_unique($arr_filtro));

        if ($id_paises) {
            for ($m = 0; $m < count($partes_paises); $m++) {
                if (!empty($partes_paises[$m])) {
                    $arr_filtro_paises[] = $partes_paises[$m];
                }
            }
        } else {
            $arr_filtro_paises = null;
        }

        $report_sites = obtenerReportePorSitio($this->token, $interval, $arr_filtro_sitios, $arr_filtro_paises, $imps_minimas);

        $data['arr_site_cat'] = $arr_site_cat;
        $data['sitios'] = $report_sites;

        $this->load->view('tbl_sitios', $data);
    }

    function obtenerReportePorSitiosFechaEspecifica($id_cats, $id_paises, $imps_minimas, $fecha_desde, $fecha_hasta, $seconds) {
        
        $cats_excluir = $this->categoriasaexcluir->get_all_categorias();
        $sites = getSites($this->token);
        $custom_cats = getCustomCategories($this->token);

        if (!$id_cats) {
            foreach ($custom_cats as $row) {
                $filtrar = 0;
                if (!$filtrar) {
                    foreach ($cats_excluir as $row_cat) {
                        if ($row->id == $row_cat->id) {
                            $filtrar = 1;
                        }
                    }
                }
                if (!$filtrar) {
                    $id_cats = $id_cats . $row->id . "o";
                }
            }
        }
        
        if ($fecha_desde != 0) {
            list($dia_desde, $mes_desde, $anio_desde) = explode("-", $fecha_desde);
            list($dia_hasta, $mes_hasta, $anio_hasta) = explode("-", $fecha_hasta);

            $start_date = $anio_desde . "-" . $mes_desde . "-" . $dia_desde . ' 00:00:00';
            $end_date = $anio_hasta . "-" . $mes_hasta . "-" . $dia_hasta . ' 23:59:59';
        }

        $partes_cats = explode("o", $id_cats);
        $partes_paises = explode("o", $id_paises);

        $arr_sites = null;

        if ($sites) {
            foreach ($sites as $row) {
                $nombres_categorias = "";
                $arr_cats = $row->content_categories;
                if ($arr_cats) {
                    foreach ($arr_cats as $cat) {
                        foreach ($custom_cats as $custom_cat) {
                            if ($custom_cat->id == $cat->id) {
                                $nombres_categorias = $nombres_categorias . $cat->name . ", ";
                            }
                        }
                    }
                    if ($id_cats) {
                        foreach ($arr_cats as $cat) {
                            for ($m = 0; $m < count($partes_cats); $m++) {
                                if (!empty($partes_cats[$m])) {
                                    if ($partes_cats[$m] == $cat->id) {
                                        $arr_sites[] = array('site_name' => $row->name, 'id' => $row->id, 'nombres_categorias' => $nombres_categorias);
                                    }
                                }
                            }
                        }
                    } else {
                        $arr_sites[] = array('site_name' => $row->name, 'id' => $row->id, 'nombres_categorias' => $nombres_categorias);
                    }
                }
            }
        } else {
            echo '<tr>
                        <td colspan="2">No se han encontrado resultados</td>
                    </tr>';
            return;
        }

        foreach ($arr_sites as $row_site) {
            $arr_site_cat[$row_site['id']] = substr($row_site['nombres_categorias'], 0, - 2);
        }

        foreach ($arr_sites as $row_site) {
            $arr_filtro[$row_site['id']] = $row_site['id'];
        }

        $arr_filtro_sitios = array_values(array_unique($arr_filtro));

        if ($id_paises) {
            for ($m = 0; $m < count($partes_paises); $m++) {
                if (!empty($partes_paises[$m])) {
                    $arr_filtro_paises[] = $partes_paises[$m];
                }
            }
        } else {
            $arr_filtro_paises = null;
        }

        $report_sites = obtenerReportePorSitioFechaEspecifica($this->token, $arr_filtro_sitios, $arr_filtro_paises, $imps_minimas, $start_date, $end_date);

        $data['arr_site_cat'] = $arr_site_cat;
        $data['sitios'] = $report_sites;

        $this->load->view('tbl_sitios', $data);
    }

    function obtenerReportePorSitiosFormato($id_cats, $id_paises, $interval, $imps_minimas, $seconds) {

        $partes_cats = explode("o", $id_cats);
        $partes_paises = explode("o", $id_paises);

        $sites = getSites($this->token);
        $custom_cats = getCustomCategories($this->token);

        $arr_sites = null;

        if ($sites) {
            foreach ($sites as $row) {
                $nombres_categorias = "";
                $arr_cats = $row->content_categories;
                if ($arr_cats) {
                    foreach ($arr_cats as $cat) {
                        foreach ($custom_cats as $custom_cat) {
                            if ($custom_cat->id == $cat->id) {
                                $nombres_categorias = $nombres_categorias . $cat->name . ", ";
                            }
                        }
                    }
                    if ($id_cats) {
                        foreach ($arr_cats as $cat) {
                            for ($m = 0; $m < count($partes_cats); $m++) {
                                if (!empty($partes_cats[$m])) {
                                    if ($partes_cats[$m] == $cat->id) {
                                        $arr_sites[] = array('site_name' => $row->name, 'id' => $row->id, 'nombres_categorias' => $nombres_categorias);
                                    }
                                }
                            }
                        }
                    } else {
                        $arr_sites[] = array('site_name' => $row->name, 'id' => $row->id, 'nombres_categorias' => $nombres_categorias);
                    }
                }
            }
        } else {
            echo '<tr>
                        <td colspan="2">No se han encontrado resultados</td>
                    </tr>';
            return;
        }

        foreach ($arr_sites as $row_site) {
            $arr_site_cat[$row_site['id']] = substr($row_site['nombres_categorias'], 0, - 2);
        }

        foreach ($arr_sites as $row_site) {
            $arr_filtro[$row_site['id']] = $row_site['id'];
        }

        $arr_filtro_sitios = array_values(array_unique($arr_filtro));

        if ($id_paises) {
            for ($m = 0; $m < count($partes_paises); $m++) {
                if (!empty($partes_paises[$m])) {
                    $arr_filtro_paises[] = $partes_paises[$m];
                }
            }
        } else {
            $arr_filtro_paises = null;
        }

        $report_sites = obtenerReportePorSitioFormato($this->token, $interval, $arr_filtro_sitios, $arr_filtro_paises, $imps_minimas);

        $data['arr_site_cat'] = $arr_site_cat;
        $data['sitios'] = $report_sites;

        $this->load->view('tbl_sitios_formato', $data);
    }

    function obtenerReportePorSitiosFormatoFechaEspecifica($id_cats, $id_paises, $imps_minimas, $fecha_desde, $fecha_hasta, $seconds) {

        if ($fecha_desde != 0) {
            list($dia_desde, $mes_desde, $anio_desde) = explode("-", $fecha_desde);
            list($dia_hasta, $mes_hasta, $anio_hasta) = explode("-", $fecha_hasta);

            $start_date = $anio_desde . "-" . $mes_desde . "-" . $dia_desde . ' 00:00:00';
            $end_date = $anio_hasta . "-" . $mes_hasta . "-" . $dia_hasta . ' 23:59:59';
        }

        $partes_cats = explode("o", $id_cats);
        $partes_paises = explode("o", $id_paises);

        $sites = getSites($this->token);
        $custom_cats = getCustomCategories($this->token);

        $arr_sites = null;

        if ($sites) {
            foreach ($sites as $row) {
                $nombres_categorias = "";
                $arr_cats = $row->content_categories;
                if ($arr_cats) {
                    foreach ($arr_cats as $cat) {
                        foreach ($custom_cats as $custom_cat) {
                            if ($custom_cat->id == $cat->id) {
                                $nombres_categorias = $nombres_categorias . $cat->name . ", ";
                            }
                        }
                    }
                    if ($id_cats) {
                        foreach ($arr_cats as $cat) {
                            for ($m = 0; $m < count($partes_cats); $m++) {
                                if (!empty($partes_cats[$m])) {
                                    if ($partes_cats[$m] == $cat->id) {
                                        $arr_sites[] = array('site_name' => $row->name, 'id' => $row->id, 'nombres_categorias' => $nombres_categorias);
                                    }
                                }
                            }
                        }
                    } else {
                        $arr_sites[] = array('site_name' => $row->name, 'id' => $row->id, 'nombres_categorias' => $nombres_categorias);
                    }
                }
            }
        } else {
            echo '<tr>
                        <td colspan="2">No se han encontrado resultados</td>
                    </tr>';
            return;
        }

        foreach ($arr_sites as $row_site) {
            $arr_site_cat[$row_site['id']] = substr($row_site['nombres_categorias'], 0, - 2);
        }

        foreach ($arr_sites as $row_site) {
            $arr_filtro[$row_site['id']] = $row_site['id'];
        }

        $arr_filtro_sitios = array_values(array_unique($arr_filtro));

        if ($id_paises) {
            for ($m = 0; $m < count($partes_paises); $m++) {
                if (!empty($partes_paises[$m])) {
                    $arr_filtro_paises[] = $partes_paises[$m];
                }
            }
        } else {
            $arr_filtro_paises = null;
        }

        $report_sites = obtenerReportePorSitioFormatoFechaEspecifica($this->token, $arr_filtro_sitios, $arr_filtro_paises, $imps_minimas, $start_date, $end_date);

        $data['arr_site_cat'] = $arr_site_cat;
        $data['sitios'] = $report_sites;

        $this->load->view('tbl_sitios_formato', $data);
    }

    function obtenerReportePorFormato($id_cats, $id_paises, $interval, $imps_minimas, $seconds) {

        $partes_cats = explode("o", $id_cats);
        $partes_paises = explode("o", $id_paises);

        $sites = getSites($this->token);
        $custom_cats = getCustomCategories($this->token);

        $arr_sites = null;

        if ($sites) {
            foreach ($sites as $row) {
                $nombres_categorias = "";
                $arr_cats = $row->content_categories;
                if ($arr_cats) {
                    foreach ($arr_cats as $cat) {
                        foreach ($custom_cats as $custom_cat) {
                            if ($custom_cat->id == $cat->id) {
                                $nombres_categorias = $nombres_categorias . $cat->name . ", ";
                            }
                        }
                    }
                    if ($id_cats) {
                        foreach ($arr_cats as $cat) {
                            for ($m = 0; $m < count($partes_cats); $m++) {
                                if (!empty($partes_cats[$m])) {
                                    if ($partes_cats[$m] == $cat->id) {
                                        $arr_sites[] = array('site_name' => $row->name, 'id' => $row->id, 'nombres_categorias' => $nombres_categorias);
                                    }
                                }
                            }
                        }
                    } else {
                        $arr_sites[] = array('site_name' => $row->name, 'id' => $row->id, 'nombres_categorias' => $nombres_categorias);
                    }
                }
            }
        } else {
            echo '<tr>
                        <td colspan="2">No se han encontrado resultados</td>
                    </tr>';
            return;
        }

        foreach ($arr_sites as $row_site) {
            $arr_site_cat[$row_site['id']] = substr($row_site['nombres_categorias'], 0, - 2);
        }

        foreach ($arr_sites as $row_site) {
            $arr_filtro[$row_site['id']] = $row_site['id'];
        }

        $arr_filtro_sitios = array_values(array_unique($arr_filtro));

        if ($id_paises) {
            for ($m = 0; $m < count($partes_paises); $m++) {
                if (!empty($partes_paises[$m])) {
                    $arr_filtro_paises[] = $partes_paises[$m];
                }
            }
        } else {
            $arr_filtro_paises = null;
        }

        $report = obtenerReportePorSitioFormato($this->token, $interval, $arr_filtro_sitios, $arr_filtro_paises, $imps_minimas);
        $data['report'] = $report;

        $this->load->view('tbl_formato', $data);
    }

    function obtenerReportePorFormatoFechaEspecifica($id_cats, $id_paises, $imps_minimas, $fecha_desde, $fecha_hasta, $seconds) {

        if ($fecha_desde != 0) {
            list($dia_desde, $mes_desde, $anio_desde) = explode("-", $fecha_desde);
            list($dia_hasta, $mes_hasta, $anio_hasta) = explode("-", $fecha_hasta);

            $start_date = $anio_desde . "-" . $mes_desde . "-" . $dia_desde . ' 00:00:00';
            $end_date = $anio_hasta . "-" . $mes_hasta . "-" . $dia_hasta . ' 23:59:59';
        }

        $partes_cats = explode("o", $id_cats);
        $partes_paises = explode("o", $id_paises);

        $sites = getSites($this->token);
        $custom_cats = getCustomCategories($this->token);

        $arr_sites = null;

        if ($sites) {
            foreach ($sites as $row) {
                $nombres_categorias = "";
                $arr_cats = $row->content_categories;
                if ($arr_cats) {
                    foreach ($arr_cats as $cat) {
                        foreach ($custom_cats as $custom_cat) {
                            if ($custom_cat->id == $cat->id) {
                                $nombres_categorias = $nombres_categorias . $cat->name . ", ";
                            }
                        }
                    }
                    if ($id_cats) {
                        foreach ($arr_cats as $cat) {
                            for ($m = 0; $m < count($partes_cats); $m++) {
                                if (!empty($partes_cats[$m])) {
                                    if ($partes_cats[$m] == $cat->id) {
                                        $arr_sites[] = array('site_name' => $row->name, 'id' => $row->id, 'nombres_categorias' => $nombres_categorias);
                                    }
                                }
                            }
                        }
                    } else {
                        $arr_sites[] = array('site_name' => $row->name, 'id' => $row->id, 'nombres_categorias' => $nombres_categorias);
                    }
                }
            }
        } else {
            echo '<tr>
                        <td colspan="2">No se han encontrado resultados</td>
                    </tr>';
            return;
        }

        foreach ($arr_sites as $row_site) {
            $arr_site_cat[$row_site['id']] = substr($row_site['nombres_categorias'], 0, - 2);
        }

        foreach ($arr_sites as $row_site) {
            $arr_filtro[$row_site['id']] = $row_site['id'];
        }

        $arr_filtro_sitios = array_values(array_unique($arr_filtro));

        if ($id_paises) {
            for ($m = 0; $m < count($partes_paises); $m++) {
                if (!empty($partes_paises[$m])) {
                    $arr_filtro_paises[] = $partes_paises[$m];
                }
            }
        } else {
            $arr_filtro_paises = null;
        }

        $report = obtenerReportePorSitioFormatoFechaEspecifica($this->token, $arr_filtro_sitios, $arr_filtro_paises, $imps_minimas, $start_date, $end_date);

        $data['report'] = $report;

        $this->load->view('tbl_formato', $data);
    }

    function obtenerReportePorPais($id_cats, $id_paises, $interval, $imps_minimas, $seconds) {
        $partes_cats = explode("o", $id_cats);
        $partes_paises = explode("o", $id_paises);

        $sites = getSites($this->token);
        $custom_cats = getCustomCategories($this->token);

        $arr_sites = null;

        if ($sites) {
            foreach ($sites as $row) {
                $nombres_categorias = "";
                $arr_cats = $row->content_categories;
                if ($arr_cats) {
                    foreach ($arr_cats as $cat) {
                        foreach ($custom_cats as $custom_cat) {
                            if ($custom_cat->id == $cat->id) {
                                $nombres_categorias = $nombres_categorias . $cat->name . ", ";
                            }
                        }
                    }
                    if ($id_cats) {
                        foreach ($arr_cats as $cat) {
                            for ($m = 0; $m < count($partes_cats); $m++) {
                                if (!empty($partes_cats[$m])) {
                                    if ($partes_cats[$m] == $cat->id) {
                                        $arr_sites[] = array('site_name' => $row->name, 'id' => $row->id, 'nombres_categorias' => $nombres_categorias);
                                    }
                                }
                            }
                        }
                    } else {
                        $arr_sites[] = array('site_name' => $row->name, 'id' => $row->id, 'nombres_categorias' => $nombres_categorias);
                    }
                }
            }
        } else {
            echo '<tr>
                        <td colspan="2">No se han encontrado resultados</td>
                    </tr>';
            return;
        }

        foreach ($arr_sites as $row_site) {
            $arr_site_cat[$row_site['id']] = substr($row_site['nombres_categorias'], 0, - 2);
        }

        foreach ($arr_sites as $row_site) {
            $arr_filtro[$row_site['id']] = $row_site['id'];
        }

        $arr_filtro_sitios = array_values(array_unique($arr_filtro));

        if ($id_paises) {
            for ($m = 0; $m < count($partes_paises); $m++) {
                if (!empty($partes_paises[$m])) {
                    $arr_filtro_paises[] = $partes_paises[$m];
                }
            }
        } else {
            $arr_filtro_paises = null;
        }

        $report = obtenerReportePorPais($this->token, $interval, $arr_filtro_sitios, $arr_filtro_paises, $imps_minimas);
        $data['report'] = $report;

        $this->load->view('tbl_pais', $data);
    }

    function obtenerReportePorPaisFechaEspecifica($id_cats, $id_paises, $imps_minimas, $fecha_desde, $fecha_hasta, $seconds) {

        if ($fecha_desde != 0) {
            list($dia_desde, $mes_desde, $anio_desde) = explode("-", $fecha_desde);
            list($dia_hasta, $mes_hasta, $anio_hasta) = explode("-", $fecha_hasta);

            $start_date = $anio_desde . "-" . $mes_desde . "-" . $dia_desde . ' 00:00:00';
            $end_date = $anio_hasta . "-" . $mes_hasta . "-" . $dia_hasta . ' 23:59:59';
        }

        $partes_cats = explode("o", $id_cats);
        $partes_paises = explode("o", $id_paises);

        $sites = getSites($this->token);
        $custom_cats = getCustomCategories($this->token);

        $arr_sites = null;

        if ($sites) {
            foreach ($sites as $row) {
                $nombres_categorias = "";
                $arr_cats = $row->content_categories;
                if ($arr_cats) {
                    foreach ($arr_cats as $cat) {
                        foreach ($custom_cats as $custom_cat) {
                            if ($custom_cat->id == $cat->id) {
                                $nombres_categorias = $nombres_categorias . $cat->name . ", ";
                            }
                        }
                    }
                    if ($id_cats) {
                        foreach ($arr_cats as $cat) {
                            for ($m = 0; $m < count($partes_cats); $m++) {
                                if (!empty($partes_cats[$m])) {
                                    if ($partes_cats[$m] == $cat->id) {
                                        $arr_sites[] = array('site_name' => $row->name, 'id' => $row->id, 'nombres_categorias' => $nombres_categorias);
                                    }
                                }
                            }
                        }
                    } else {
                        $arr_sites[] = array('site_name' => $row->name, 'id' => $row->id, 'nombres_categorias' => $nombres_categorias);
                    }
                }
            }
        } else {
            echo '<tr>
                        <td colspan="2">No se han encontrado resultados</td>
                    </tr>';
            return;
        }

        foreach ($arr_sites as $row_site) {
            $arr_site_cat[$row_site['id']] = substr($row_site['nombres_categorias'], 0, - 2);
        }

        foreach ($arr_sites as $row_site) {
            $arr_filtro[$row_site['id']] = $row_site['id'];
        }

        $arr_filtro_sitios = array_values(array_unique($arr_filtro));

        if ($id_paises) {
            for ($m = 0; $m < count($partes_paises); $m++) {
                if (!empty($partes_paises[$m])) {
                    $arr_filtro_paises[] = $partes_paises[$m];
                }
            }
        } else {
            $arr_filtro_paises = null;
        }

        $report = obtenerReportePorPaisFechaEspecifica($this->token, $arr_filtro_sitios, $arr_filtro_paises, $imps_minimas, $start_date, $end_date);
        $data['report'] = $report;

        $this->load->view('tbl_pais', $data);
    }

    function getCategorias($seconds) {

        $cats_excluir = $this->categoriasaexcluir->get_all_categorias();

        $result_cats = getCustomCategories($this->token);

        if ($result_cats) {
            foreach ($result_cats as $row) {
                $filtrar = 0;
                if (!$filtrar) {
                    foreach ($cats_excluir as $row_cat) {
                        if ($row->id == $row_cat->id) {
                            $filtrar = 1;
                        }
                    }
                }
                if (!$filtrar) {
                    echo '<option value="' . $row->id . '">' . $row->name . '</option>';
                }
            }
        }
    }

}