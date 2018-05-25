<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Tarifario extends CI_Controller {

    function __construct() {
        parent::__construct();

        $this->load->model('tarifarios');
    }

    function get_valor($formato, $segmentacion, $modalidad){
        $valor = $this->tarifarios->get_by_formato_segmentacion_modalidad($formato, $segmentacion, $modalidad);
        $valor = htmlentities($valor[0]->valor);

        echo json_encode($valor);
    }

}
?>