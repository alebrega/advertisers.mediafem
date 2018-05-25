<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Campaniascanalestematicos extends CI_Model {

    private $table_name = 'campanias_canales_tematicos';

    function __construct() {
        parent::__construct();
    }

    function insert_campania_canal_tematico($data){
        return $this->db->insert($this->table_name, $data);
    }

    function get_canales_tematicos_by_campania($id) {
        $this->db->where('id_campania', $id);
        //$this->db->where('id_canal_tematico = 20');
        $this->db->order_by('id_campania', 'asc');
        
        $query = $this->db->get($this->table_name);
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function delete_canales_by_campania($id_campania) {
        $this->db->where('id_campania', $id_campania);
        $this->db->delete($this->table_name);
        if ($this->db->affected_rows() > 0) {
            return TRUE;
        }
        return FALSE;
    }

}