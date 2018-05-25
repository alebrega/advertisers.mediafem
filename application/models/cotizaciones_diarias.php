<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cotizaciones_diarias extends CI_Model {

    private $table = 'cotizaciones_diarias';

    function __construct() {
        parent::__construct();
    }

    function get_cotizacion_today($currency) {
        $query = $this->db->query("SELECT * FROM $this->table WHERE to_currency = '$currency' ORDER BY id DESC LIMIT 1;");

        if ($query->num_rows() > 0)
            return $query->row();

       return null;
    }

}