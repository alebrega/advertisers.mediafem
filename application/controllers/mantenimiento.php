<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Mantenimiento extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('constants');
        $this->load->model('mensajemantenimiento');
    }

    function index(){
        $data['mensaje'] = $this->mensajemantenimiento->get_mensaje_mantenimiendo_by_estado('A')->contenido;
        $this->load->view('mantenimiento_view', $data);
    }

}