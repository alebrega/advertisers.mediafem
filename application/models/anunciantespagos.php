<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Anunciantespagos extends CI_Model {

    private $table_name = 'anunciantes_pagos';

    function __construct() {
        parent::__construct();
    }

    function get_pagos_por_anunciante($id) {

        $this->db->where($this->table_name . '.id_anunciante', $id);
        $tipos_de_pago = array('4');
        $this->db->where_not_in('tipo_de_pago', $tipos_de_pago);

        $query = $this->db->get($this->table_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_pagos_por_anunciante_json($id) {

        $this->db->where($this->table_name . '.id_anunciante', $id);
        //$this->db->where($this->table_name . '.respuesta_json', 'is not NULL');
        $this->db->where($this->table_name . '.tipo_de_pago', '1');

        $query = $this->db->get($this->table_name);

        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    function insert_anunciante_pago($data) {
        return $this->db->insert($this->table_name, $data);
    }

}