<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Formatosdfp extends Model {

    private $table_name = 'formatos_dfp';

    function __construct() {
        parent::__construct();
    }
    
    function get_formato_by_id($id) {
        $this->db->where('id', $id);

        $query = $this->db->get($this->table_name);
        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }
    
    function get_formato_by_valor($valor) {
        $this->db->where('valor', $valor);

        $query = $this->db->get($this->table_name);
        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }
    
    function get_formatos() {
        $this->db->where('estado', 'A');
        $this->db->where_not_in('id', 9);
        $this->db->order_by('id', 'asc');

        $query = $this->db->get($this->table_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

}