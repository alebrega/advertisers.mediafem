<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Campaniassitios extends CI_Model {

    private $table_name = 'campanias_sitios';

    function __construct() {
        parent::__construct();
    }

    function insert_campania_sitio($data) {
        return $this->db->insert($this->table_name, $data);
    }

    function get_sitios_by_campania($id) {
        $this->db->where('id_campania', $id);

        $query = $this->db->get($this->table_name);
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_campania_sitio($id_campania, $id_sitio) {
        $this->db->where('id_campania', $id_campania);
        $this->db->where('id_sitio', $id_sitio);

        $query = $this->db->get($this->table_name);
        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    function delete_sitio($id_campania, $id_sitio) {
        $this->db->where('id_campania', $id_campania);
        $this->db->where('id_sitio', $id_sitio);
        $this->db->delete($this->table_name);
        if ($this->db->affected_rows() > 0) {
            return TRUE;
        }
        return FALSE;
    }

    function delete_sitio_by_campania($id_campania) {
        $this->db->where('id_campania', $id_campania);
        $this->db->delete($this->table_name);
        if ($this->db->affected_rows() > 0) {
            return TRUE;
        }
        return FALSE;
    }

    function update_campania_sitios($id_campania, $ids_sitios, $data) {
        $where = "id_campania='$id_campania' and id_sitio in ($ids_sitios)";

        $this->db->where($where, NULL, FALSE);
        $this->db->update($this->table_name, $data);
        return $this->db->affected_rows() > 0;
    }

}