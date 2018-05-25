<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Campaniassitiosformatos extends CI_Model {

    private $table_name = 'campanias_sitios_formatos';

    function __construct() {
        parent::__construct();
    }

    function insert_campania_sitio_formato($data) {
        return $this->db->insert($this->table_name, $data);
    }

    function get_sitios_espacios_formato_por_campania($id_campania) {
        $this->db->where('id_campania', $id_campania);

        $query = $this->db->get($this->table_name);
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_sitios_espacios_formato($id_campania, $id_sitio) {
        $this->db->where('id_campania', $id_campania);
        $this->db->where('id_sitio', $id_sitio);

        $query = $this->db->get($this->table_name);
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_formatos_aceptados_por_campania_sitio($id_campania, $id_sitio) {
        $where = "id_campania='$id_campania' and id_sitio='$id_sitio' and aceptada='S'";

        $this->db->where($where, NULL, FALSE);

        $query = $this->db->get($this->table_name);
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
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

    function delete_formatos_by_campania($id_campania){
        return $this->db->query("delete from $this->table_name where id_campania=$id_campania;");
    }

}