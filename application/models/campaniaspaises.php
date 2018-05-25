<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Campaniaspaises extends CI_Model {

    private $table_name = 'campanias_paises';
    private $table_paises = 'paises';

    function __construct() {
        parent::__construct();
    }

    function insert_campania_pais($data){
        return $this->db->insert($this->table_name, $data);
    }

    function get_paises_by_campania($id) {
        $this->db->select($this->table_name . '.id_campania');
        $this->db->select($this->table_name . '.id_pais');
        $this->db->select($this->table_paises . '.descripcion');
        $this->db->select($this->table_paises . '.id_dfp');

        $this->db->where('id_campania', $id);

        $this->db->join($this->table_paises, $this->table_paises . ".id = " . $this->table_name . ".id_pais");

        $query = $this->db->get($this->table_name);
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

     function get_campania_pais($id_campania, $id_pais) {
        $this->db->where('id_campania', $id_campania);
        $this->db->where('id_pais', $id_pais);

        $query = $this->db->get($this->table_name);
        if ($query->num_rows() == 1)
            return false;
        return true;
    }

    function delete_pais($id_campania, $id_pais) {
        $this->db->where('id_campania', $id_campania);
        $this->db->where('id_pais', $id_pais);
        $this->db->delete($this->table_name);
        if ($this->db->affected_rows() > 0) {
            return TRUE;
        }
        return FALSE;
    }

    function delete_paises_by_campania($id_campania) {
        $this->db->where('id_campania', $id_campania);
        $this->db->delete($this->table_name);
        if ($this->db->affected_rows() > 0) {
            return TRUE;
        }
        return FALSE;
    }

}