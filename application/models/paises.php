<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Paises extends CI_Model {

    private $table_name = 'paises';

    function __construct() {
        parent::__construct();
    }

    function insert_pais($data){
        return $this->db->insert($this->table_name, $data);
    }

    function get_paises() {
        $this->db->where('estado', 'A');
        $this->db->where_not_in('descripcion', 'Desconocido');
        $this->db->where_not_in('id_dfp', '');
        $this->db->order_by('descripcion', 'asc');

        $query = $this->db->get($this->table_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_pais_by_id($id) {

        $this->db->where($this->table_name . '.id', $id);

        $query = $this->db->get($this->table_name);
        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    function get_pais_by_id_DFP($id_dfp) {

        $this->db->where($this->table_name . '.id_dfp', $id_dfp);

        $query = $this->db->get($this->table_name);
        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }
    
    function update_pais($id, $data) {

        $this->db->where('id', $id);

        $this->db->update($this->table_name, $data);
        return $this->db->affected_rows() > 0;
    }
    
}