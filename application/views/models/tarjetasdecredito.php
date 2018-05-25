<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Tarjetasdecredito extends CI_Model {

    private $table_name = 'tarjetas_de_credito';

    function __construct() {
        parent::__construct();
    }

    function get_tarjeta_de_credito_by_id($id) {

        $this->db->where($this->table_name . '.id', $id);

        $query = $this->db->get($this->table_name);
        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    function get_all() {
        $this->db->order_by('descripcion', 'asc');
        $query = $this->db->get($this->table_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

}