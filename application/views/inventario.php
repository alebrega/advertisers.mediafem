<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Inventario extends CI_Controller {

    public $user_notacion = 0;

    function __construct() {
        parent::__construct();

        $this->load->model('reportes');
        $this->load->model('categorias');
        $this->load->model('paises');
        $this->load->model('reportesusuarios');


        $this->load->model('users');

        $notacion = $this->users->get_notacion_user($this->tank_auth->get_user_id());
        if ($notacion)
            $this->user_notacion = $notacion->notacion;
    }

    function index() {
        if (!$this->tank_auth->is_logged_in())
            redirect('/auth/login/');

        /*
        if ((!$this->tarjeta_certificada && $this->creado_desde_sitio == 1) && $this->limite_de_compra == 0.00)
            redirect('/micuenta');
            */

        if (($this->creado_desde_sitio == 1) && $this->limite_de_compra == 0.00)
            redirect('/micuenta');

        $data['canales_tematicos'] = $this->get_canales_tematicos();
        $data['paises'] = $this->get_paises();

        $this->load->view('inventario_view', $data);
    }

    /*
     * REPORTES
     */

    function suscribir_reporte() {
        $data = array(
            'correo_electronico' => $this->input->post('correo_electronico')
            , 'tipo' => $this->input->post('tipo')
            , 'token' => $this->token
            , 'filtro_categorias' => $this->input->post('filtro_categorias')
            , 'filtro_paises' => $this->input->post('filtro_paises')
            , 'intervalo' => $this->input->post('intervalo')
            , 'fecha_desde' => $this->input->post('fecha_desde')
            , 'fecha_hasta' => $this->input->post('fecha_hasta')
            , 'seconds' => $this->input->post('seconds')
            , 'solicitado_desde' => 'ANUNCIANTES'
        );

        $this->reportes->insertar_suscripcion($data);

        echo json_encode(array('validate' => TRUE));
    }

    function reporte_sitio($filtro_categorias = 0, $filtro_paises = 0, $intervalo = 'today', $fecha_desde = 0, $fecha_hasta = 0, $seconds) {
        $reporte = $this->api->reportePorSitio($filtro_categorias, $filtro_paises, $intervalo, $fecha_desde, $fecha_hasta);

        $reporte['mostrar_alerta'] = $this->api->error_al_obtener_reporte;

        $reporte['notacion'] = $this->user_notacion;

        $data_update = array(
            'fecha_ejecucion_final' => date("y/m/d H:i:s"),
            'reporte_mostrado' => 1
        );

        $user_id = $this->tank_auth->get_user_id();

        $this->reportesusuarios->update($data_update, $user_id);

        $this->load->view('reporte_sitios', $reporte);
    }

    function reporte_categoria($filtro_categorias = 0, $filtro_paises = 0, $intervalo = 'today', $fecha_desde = 0, $fecha_hasta = 0, $seconds) {
        $reporte = $this->api->reportePorCategoria($this->token, $filtro_categorias, $filtro_paises, $intervalo, $fecha_desde, $fecha_hasta);

        $reporte['mostrar_alerta'] = $this->api->error_al_obtener_reporte;

        $reporte['notacion'] = $this->user_notacion;

        $data_update = array(
            'fecha_ejecucion_final' => date("y/m/d H:i:s"),
            'reporte_mostrado' => 1
        );
        $user_id = $this->tank_auth->get_user_id();

        $this->reportesusuarios->update($data_update, $user_id);
        $this->load->view('reporte_categoria', $reporte);
    }

    function reporte_sitio_formato($filtro_categorias = 0, $filtro_paises = 0, $intervalo = 'today', $fecha_desde = 0, $fecha_hasta = 0, $seconds) {
        $reporte = $this->api->reportePorSitioFormato($this->token, $filtro_categorias, $filtro_paises, $intervalo, $fecha_desde, $fecha_hasta);

        $reporte['mostrar_alerta'] = $this->api->error_al_obtener_reporte;

        $reporte['notacion'] = $this->user_notacion;

        $data_update = array(
            'fecha_ejecucion_final' => date("y/m/d H:i:s"),
            'reporte_mostrado' => 1
        );
        $user_id = $this->tank_auth->get_user_id();

        $this->reportesusuarios->update($data_update, $user_id);
        $this->load->view('reporte_sitios_formatos', $reporte);
    }

    function reporte_formato($filtro_categorias = 0, $filtro_paises = 0, $intervalo = 'today', $fecha_desde = 0, $fecha_hasta = 0, $seconds) {
        $reporte = $this->api->reportePorFormato($this->token, $filtro_categorias, $filtro_paises, $intervalo, $fecha_desde, $fecha_hasta);

        $reporte['mostrar_alerta'] = $this->api->error_al_obtener_reporte;

        $reporte['notacion'] = $this->user_notacion;

        $data_update = array(
            'fecha_ejecucion_final' => date("y/m/d H:i:s"),
            'reporte_mostrado' => 1
        );
        $user_id = $this->tank_auth->get_user_id();

        $this->reportesusuarios->update($data_update, $user_id);
        $this->load->view('reporte_formato', $reporte);
    }

    function reporte_pais($filtro_categorias = 0, $filtro_paises = 0, $intervalo = 'today', $fecha_desde = 0, $fecha_hasta = 0, $seconds) {
        $reporte = $this->api->reportePorPais($this->token, $filtro_categorias, $filtro_paises, $intervalo, $fecha_desde, $fecha_hasta);

        $reporte['mostrar_alerta'] = $this->api->error_al_obtener_reporte;

        $reporte['notacion'] = $this->user_notacion;

        $data_update = array(
            'fecha_ejecucion_final' => date("y/m/d H:i:s"),
            'reporte_mostrado' => 1
        );
        $user_id = $this->tank_auth->get_user_id();

        $this->reportesusuarios->update($data_update, $user_id);
        $this->load->view('reporte_pais', $reporte);
    }

    /* reporte por paos formato */

    function reporte_pais_y_formato($filtro_categorias = 0, $filtro_paises = 0, $intervalo = 'today', $fecha_desde = 0, $fecha_hasta = 0, $seconds){
/*
        $reporte = $this->api->reportePorPaisFormato($this->token, $filtro_categorias, $filtro_paises, $intervalo, $fecha_desde, $fecha_hasta);

        $reporte['mostrar_alerta'] = $this->api->error_al_obtener_reporte;

        $reporte['notacion'] = $this->user_notacion;

        $data_update = array(
            'fecha_ejecucion_final' => date("y/m/d H:i:s"),
            'reporte_mostrado' => 1
        );
        $user_id = $this->tank_auth->get_user_id();

        $this->reportesusuarios->update($data_update, $user_id);

        $data['reporte'] = $reporte;

        $this->load->view('reporte_sitios_pais_formato', $data);*/
        echo"hola";
    }

    function insert_reporte_usuarios() {

        $data_insert = array(
            'user_id' => $this->tank_auth->get_user_id(),
            'aplicacion' => 2,
            'tipo_de_reporte' => $this->input->post('tipo_de_reporte')
        );
        /* mandar al modelo los datos y hacer el insert */
        $this->reportesusuarios->insertar_reporte_usuarios($data_insert);
    }

    function update_no_deseo_esperar() {
        $data_update = array(
            'no_espero' => 1
        );

        $this->reportesusuarios->update($data_update, $this->tank_auth->get_user_id());
    }

    /*
     * EXPORTAR
     */

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

    function export_excel_category() {
        $this->load->view('excel_category');
    }

    function excel_pais_formato() {
        $this->load->view('excel_pais_formatos');
    }

    /*
     * GETs
     */

    private function get_canales_tematicos() {
        return $this->categorias->get_categorias();
    }

    private function get_paises() {
        return $this->paises->get_paises();
    }

}