<?php

require_once BASEPATH . '/application/libraries/config.inc';
require_once BASEPATH . '/application/libraries/Caller.php';
require_once BASEPATH . '/application/libraries/api_functions.php';
require_once BASEPATH . '/application/libraries/pagination_array.php';
require_once BASEPATH . '/application/libraries/functions.php';

class Advertisers extends Controller {

    function __construct() {
        parent::Controller();
        $this->load->helper(array('url', 'form'));
        $this->lang->load('tank_auth');
        $this->load->library('tank_auth');
        $this->load->model('anunciantes');
        $this->load->model('constants');
        $this->load->model('anunciantespagos');
        $this->load->model('anunciantessaldos');
    }

    function index() {
        if (!$this->tank_auth->is_logged_in()) {
            redirect('/auth/login');
        }

        $this->output->clear_page_cache();

        $limite_de_compra = $this->constants->get_constant_by_id(LIMITE_DE_COMPRA);

        $data['anunciantes'] = $this->anunciantes->get_all_anunciantes();
        $data['limite_de_compra'] = $limite_de_compra->value;

        $data['page_title'] = "Administrador MediaFem Sitios";
        $this->load->view('advertisers_view', $data);
    }

    function get_advertisers($seconds) {
        $anunciantes = $this->anunciantes->get_all_anunciantes();

        foreach ($anunciantes as $row) {
            $arr_advs[] = array('id' => $row->id, 'email' => $row->email, 'nombre' => $row->name, 'estado' => $row->activated,
                'username' => $row->username, 'es_agencia' => $row->agencia, 'creado_desde_sitio' => $row->creado_desde_sitio);
        }
        $data['advertisers'] = $arr_advs;

        $this->load->view('tbl_advs', $data);
    }

    function get_facturacion_by_id($id_anunciante, $seconds) {

        $pagos_tarjeta = $this->anunciantespagos->get_pagos_por_anunciante($id_anunciante);

        $saldos_mediafem = $this->anunciantessaldos->get_saldos_por_anunciante($id_anunciante);

        $data['pagos'] = $pagos_tarjeta;
        $data['saldos'] = $saldos_mediafem;

        $this->load->view('tbl_facturacion_anunciante', $data);
    }

    function actualizar_advertisers_adserver() {
        // traigo todos los anunciantes adserver de la DB
        $anunciantes_adservers = $this->anunciantes->get_all_anunciantes_adservers();
        foreach ($anunciantes_adservers as $value) {
            if ($value->adserver_actual == 0) {
                $anunciantes_adservers_arr[] = $value->id_dfp;
            } else if ($value->adserver_actual == 1) {
                $anunciantes_adservers_arr[] = $value->id_appnexus;
            } else if ($value->adserver_actual == 2) {
                $anunciantes_adservers_arr[] = $value->id_eplanning;
            }
        }

        // ANUNCIANTES DFP
        $user = new DfpUser();
        $user->LogDefaults();
        $companyService = $user->GetService('CompanyService', 'v201208');
        $vars = MapUtils::GetMapEntries(array('type' => new TextValue('ADVERTISER'), 'creditStatus' => new TextValue('ACTIVE')));
        $filterStatement = new Statement("WHERE type = :type AND creditStatus = :creditStatus ORDER BY name", $vars);
        $results = $companyService->getCompaniesByStatement($filterStatement);

        if ($results->totalResultSetSize > 0) {
            foreach ($results->results as $result) {
                $fecha_modificacion = $result->lastModifiedDateTime->date->year . '-' . $result->lastModifiedDateTime->date->month . '-' . $result->lastModifiedDateTime->date->day . ' ' . $result->lastModifiedDateTime->hour . ':' . $result->lastModifiedDateTime->minute . ':' . $result->lastModifiedDateTime->second;
                $arr_advs[] = array('id' => $result->id, 'nombre' => (String) $result->name, 'fecha_modificacion' => (String) $fecha_modificacion, 'adserver' => 0);
            }
        }

        // ANUNCIANTES APPNEXUS
        $anunciantes = getAnunciantes($this->token, 0);
        foreach ($anunciantes as $row) {
            $arr_advs[] = array('id' => $row->id, 'nombre' => $row->name, 'fecha_modificacion' => $row->last_modified, 'adserver' => 1);
        }

        $anunciantes = getAnunciantes($this->token, 100);
        foreach ($anunciantes as $row) {
            $arr_advs[] = array('id' => $row->id, 'nombre' => $row->name, 'fecha_modificacion' => $row->last_modified, 'adserver' => 1);
        }

        $anunciantes = getAnunciantes($this->token, 200);
        foreach ($anunciantes as $row) {
            $arr_advs[] = array('id' => $row->id, 'nombre' => $row->name, 'fecha_modificacion' => $row->last_modified, 'adserver' => 1);
        }

        $anunciantes = getAnunciantes($this->token, 300);
        foreach ($anunciantes as $row) {
            $arr_advs[] = array('id' => $row->id, 'nombre' => $row->name, 'fecha_modificacion' => $row->last_modified, 'adserver' => 1);
        }

        $anunciantes = getAnunciantes($this->token, 400);
        foreach ($anunciantes as $row) {
            $arr_advs[] = array('id' => $row->id, 'nombre' => $row->name, 'fecha_modificacion' => $row->last_modified, 'adserver' => 1);
        }

        // ANUNCIANTES E-PLANNING
        $res_anunciantes = listar_anunciantes();

        $xml = simplexml_load_string($res_anunciantes);

        foreach ($xml as $row) {
            $arr_advs[] = array('id' => $row->anunciante_id, 'nombre' => (String) $row->nombre, 'fecha_modificacion' => (String) $row->fecha_alta, 'adserver' => 2);
        }

        $dia_actual = date('Y-m-d H:i:s');

        foreach ($arr_advs as $anunciante) {
            if ($anunciante['adserver'] == 0) {
                $adserver = 'id_dfp';
            } else if ($anunciante['adserver'] == 1) {
                $adserver = 'id_appnexus';
            } else if ($anunciante['adserver'] == 2) {
                $adserver = 'id_eplanning';
            }

            $data = array(
                'nombre' => $anunciante['nombre'],
                'adserver_actual' => $anunciante['adserver'],
                $adserver => $anunciante['id'],
                'fecha_alta' => $anunciante['fecha_modificacion'],
                'fecha_modificacion' => $dia_actual
            );

            // busco el anunciante en la base, si no esta lo agrego
            if (!in_array((String) $anunciante['id'], $anunciantes_adservers_arr)) {
                $this->anunciantes->insert_anunciante_adserver($data);
            }
        }
    }

    function get_advertisers_adserver($seconds) {
        $anunciantes_adservers = $this->anunciantes->get_all_anunciantes_adservers();

        $data['anunciantes'] = NULL;

        if ($anunciantes_adservers) {
            foreach ($anunciantes_adservers as $row) {
                switch ($row->adserver_actual) {
                    case 0:
                        $adserver = 'DFP';
                        break;
                    case 1:
                        $adserver = 'AppNexus';
                        break;
                    case 2:
                        $adserver = 'E-Planning';
                        break;
                    default:
                        $adserver = '-';
                        break;
                }

                $fecha_alta = MySQLDateToDate($row->fecha_alta);

                $arr_advs[] = array('id' => $row->id, 'nombre' => $row->nombre, 'adserver' => $adserver, 'fecha_alta' => $fecha_alta);
            }

            $data['anunciantes'] = $arr_advs;
        }

        $this->load->view('tbl_advs_2', $data);
    }

    function get_data_by_id($id_anunciante, $seconds) {
        $anunciante = $this->anunciantes->get_anunciante_by_id($id_anunciante);

        $anunciantes_asociados = $this->anunciantes->get_anunciantes_asociados_by_id($id_anunciante);

        $data['anunciante_adserver'] = $data_adv_adserver = null;

        if ($anunciantes_asociados) {
            foreach ($anunciantes_asociados as $adv) {

                $advertiser = $this->anunciantes->get_anunciante_adserver_by_id($adv->id_anunciante_adserver);

                if ($advertiser) {
                    switch ($advertiser->adserver_actual) {
                        case '0':
                            $adserver = 'DFP';
                            break;
                        case '1':
                            $adserver = 'AppNexus';
                            break;
                        case '2':
                            $adserver = 'E-Planning';
                            break;
                        default:
                            $adserver = '-';
                            break;
                    }

                    $fecha_alta = MySQLDateToDate($advertiser->fecha_alta);

                    $data_adv_adserver[] = array('id' => $advertiser->id, 'nombre' => $advertiser->nombre, 'adserver' => $adserver, 'fecha_alta' => $fecha_alta);
                }
            }

            if ($data_adv_adserver)
                $data['anunciante_adserver'] = $data_adv_adserver;
        }

        $data['id_anunciante'] = $id_anunciante;
        $data['anunciante'] = $anunciante;

        $this->load->view('tbl_advs_appnexus', $data);
        
        /*aca cargar la tabla , primero oobtener los datos luego pasarselos a la tabla*/
       
        
   
    }
        
    function cargar_reportes_usuarios($id_anunciante, $second){
        $data1["datos_reportes"] = $this->anunciantes->get_datos_reporte_usuarios($id_anunciante);
        
        if ($data1["datos_reportes"]) {
            $this->load->view('td_solicitudes_reportes', $data1);
        } else {
            echo 'El usuario no tiene reportes para mostrar';
        }   
        
    }             
    

    function get_anunciantes_by_name() {
        $result_busqueda = $this->anunciantes->get_anunciantes_by_name($this->input->post('q'));

        if ($result_busqueda) {
            foreach ($result_busqueda as $row) {
                echo $row->name . "\n";
            }
        }
    }

    function get_anunciantes_by_name_appnexus() {

        $anunciantes = getAdvertisers($this->token);

        $q = strtolower($this->input->post('q'));

        if (!$q)
            return;

        foreach ($anunciantes as $row) {
            $items[$row->name] = $row->id;
        }

        foreach ($items as $key => $value) {
            if (strpos(strtolower($key), $q) !== false) {
                echo "$key|$value\n";
            }
        }
    }

    function add() {
        $nombre = $this->input->post('nombre_anunciante');
        $username = $this->input->post('username_anunciante');
        $ids = $this->input->post('ids');
        $password = $this->input->post('passField');
        $es_agencia = $this->input->post('es_agencia');
        $agrupar_por_sitio = $this->input->post('agrupar_por_sitio');
        $habilitar_limite_de_compra = $this->input->post('habilitar_limite_de_compra');
        $habilitar_descuentos = $this->input->post('habilitar_descuentos');
        $limite_de_compra = $this->input->post('limite_de_compra');

        $email = $this->input->post('correo_anunciante');

        $hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
        $hashed_password = $hasher->HashPassword($password);

        if (!$habilitar_limite_de_compra)
            $limite_de_compra = 0;

        $data_anunciante = array('name' => $nombre, 'username' => $username, 'password' => $hashed_password, 'agencia' => $es_agencia, 'email' => $email,
            'agrupar_por_sitio' => $agrupar_por_sitio, 'limite_de_compra' => $limite_de_compra, 'habilitar_descuentos' => $habilitar_descuentos);

        $res_limite_de_compra = $this->constants->get_constant_by_id(LIMITE_DE_COMPRA);

        $limite_de_compra_default = $res_limite_de_compra->value;

        $data['limite_de_compra'] = $limite_de_compra_default;

        if ($id = $this->anunciantes->create_anunciante($data_anunciante)) {

            if (strlen(trim($ids))) {

                $pieces = explode("o", $ids);

                for ($i = 0; $i < count($pieces); $i++) {
                    $data_asociados = array('id_anunciante_redvlog' => $id, 'id_anunciante_adserver' => $pieces[$i]);
                    $this->anunciantes->insert_anunciante_asociado($data_asociados);
                }
            }

            if ($habilitar_limite_de_compra) {

                $data_insert_pago = array(
                    'id_anunciante' => $id,
                    'id_campania' => '',
                    'credito' => $limite_de_compra,
                    'balance' => $limite_de_compra,
                    'descripcion' => "Primer recarga saldo Mediafem - " . getMesEsp(date('m')) . " " . date('Y'),
                    'tipo_de_pago' => '3'
                );

                $this->anunciantessaldos->insert_anunciante_saldo($data_insert_pago);
            }

            $data['anunciantes'] = $this->anunciantes->get_all_anunciantes();
            $data['page_title'] = "Administrador MediaFem Sitios";
            $data['mensaje'] = "El Anunciante se guard&oacute; correctamente";
            $this->load->view('advertisers_view', $data);
        } else {

            $data['anunciantes'] = $this->anunciantes->get_all_anunciantes();
            $data['page_title'] = "Administrador MediaFem Sitios";
            $data['mensaje'] = "Ha ocurrido un error, intente mas tarde";
            $this->load->view('advertisers_view', $data);
        }
    }

    function actualizar_anunciante() {
        $id_anunciante = $this->input->post('id_anunciante');
        $email = $this->input->post('email');
        $agencia = $this->input->post('agencia');
        $agrupar_por_sitio = $this->input->post('agrupar_por_sitio');
        $limite_de_compra = $this->input->post('limite_de_compra');
        $habilitar_limite_de_compra = $this->input->post('habilitar_limite_de_compra');
        $habilitar_descuentos = $this->input->post('habilitar_descuentos');
        $limite_de_compra_default = $this->input->post('limite_de_compra_default');

        if (!$habilitar_limite_de_compra) {
            $limite_de_compra = 0;
        } else {
            if (!$limite_de_compra_default) {
                
                $data_insert_pago = array(
                    'id_anunciante' => $id_anunciante,
                    'id_campania' => '',
                    'credito' => $limite_de_compra,
                    'balance' => $limite_de_compra,
                    'descripcion' => "Primer recarga saldo Mediafem - " . getMesEsp(date('m')) . " " . date('Y'),
                    'tipo_de_pago' => '3'
                );

                $this->anunciantessaldos->insert_anunciante_saldo($data_insert_pago);
            }
        }

        $data_update = array('email' => $email, 'agencia' => $agencia, 'agrupar_por_sitio' => $agrupar_por_sitio, 'limite_de_compra' => $limite_de_compra,
            'habilitar_descuentos' => $habilitar_descuentos);

        if ($this->anunciantes->update_anunciante($id_anunciante, $data_update)) {
            echo json_encode(array('validate' => TRUE));
        } else {
            echo json_encode(array('validate' => FALSE));
        }
    }

    function delete_anunciante_asociado($id_adv_redvlog, $id_anunciante_adserver, $seconds) {
        if ($this->anunciantes->delete_anunciante_asociado($id_adv_redvlog, $id_anunciante_adserver)) {
            //echo "correcto";
        } else {
            //echo "incorrecto";
        }
    }

    function add_anunciante_asociado($id_adv_redvlog, $id_adv_adserver, $seconds) {

        $data = array("id_anunciante_redvlog" => $id_adv_redvlog, "id_anunciante_adserver" => $id_adv_adserver);

        if ($this->anunciantes->insert_anunciante_asociado($data)) {
            //echo "correcto";
        } else {
            //echo "incorrecto";
        }
    }

    function delete_adv_redvlog() {
        $id_adv_redvlog = $this->input->post("id");
        if ($this->anunciantes->delete_user($id_adv_redvlog)) {
            echo json_encode(array('validate' => TRUE));
        } else {
            echo json_encode(array('validate' => FALSE));
        }
    }

    function activar_adv_redvlog() {
        $id_adv_redvlog = $this->input->post("id");

        if ($this->anunciantes->active_user($id_adv_redvlog)) {
            echo json_encode(array('validate' => TRUE));
        } else {
            echo json_encode(array('validate' => FALSE));
        }
    }

    function cambiar_es_agencia() {

        $id_adv_redvlog = $this->input->post("id");
        $es_agencia = $this->input->post("es_agencia");

        $data = array('agencia' => $es_agencia);

        if ($this->anunciantes->update_anunciante($id_adv_redvlog, $data)) {
            echo json_encode(array('validate' => TRUE));
        } else {
            echo json_encode(array('validate' => FALSE));
        }
    }

    function get_modificacion_adv($id, $seconds) {
        $data['anunciante'] = $this->anunciantes->get_anunciante_adserver_by_id($id);
        $this->load->view('modificacion_adv_view', $data);
    }

    function modif_advertise_appnexus($id, $nombre, $estado, $seconds) {
        $nombre = str_replace("_", "%", $nombre);
        $nombre = urldecode($nombre);

        if (modifyAdvertiser($this->token, $id, $nombre, $estado)) {
            echo "Los cambios se han guardado correctamente";
        } else {
            echo "Ha ocurrido un error, intente mas tarde";
        }
    }

    function check_user() {
        $username = trim($this->input->post('username_anunciante'));

        $disponible = ((strlen($username) > 0) AND $this->anunciantes->is_username_available($username));

        if ($disponible) {
            $valid = "true";
        } else {
            $valid = "false";
        }
        echo $valid;
    }

}

?>