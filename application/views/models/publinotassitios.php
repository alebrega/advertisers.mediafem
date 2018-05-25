<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Publinotassitios extends CI_Model {

    private $table = 'publinotas_sitios';

    function __construct() {
        parent::__construct();
    }

    function get_sitios_by_publinota($id) {
        $this->db->where('id_publinota', $id);
        $query = $this->db->get($this->table);

        if ($query->num_rows() > 0)
            return $query->result();

        return null;
    }

    function insertar($data) {
        return $this->db->insert($this->table, $data);
    }

    function delete_by_publinota($id_publinota){
        $this->db->where('id_publinota', $id_publinota);
        $this->db->delete($this->table);

        if ($this->db->affected_rows() > 0)
            return TRUE;

        return FALSE;
    }

    function update($id_sitio, $id_publinota, $data) {
        $this->db->where('id_sitio', $id_sitio);
        $this->db->where('id_publinota', $id_publinota);
        $this->db->update($this->table, $data);
        return $this->db->affected_rows() > 0;
    }
}