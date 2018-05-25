<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Columnas extends CI_Model {

    private $table_columnas = 'columnas';

    function __construct() {
        parent::__construct();
    }

    function get_all_columnas() {
        $this->db->order_by('orden', 'asc');
        $query = $this->db->get($this->table_columnas);

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_columna_by_id($id) {
        $this->db->where('id', $id);
        
        $query = $this->db->get($this->table_columnas);
        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

}