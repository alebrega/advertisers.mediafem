<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cupones_model extends CI_Model {
    private $table = 'cupones_descuento';

    function __construct() {
        parent::__construct();
    }
/*
    function get_campanias() {
        $this->db->where('eliminada', '0');
        $query = $this->db->get('campania');
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_campanias_activadas() {
        $this->db->where('eliminada', '0');
        $this->db->where('activada', '1');
        $query = $this->db->get('campania');
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }
*/
    function get_cupones_by_codigo($codigo) {
        $this->db->where('codigo', $codigo);

        $query = $this->db->get($this->table);

        if ($query->num_rows() == 1)
            return $query->row();

        return NULL;
    }

    function update($id, $data) {
        $this->db->where('id', $id);
        $this->db->update($this->table, $data);
        
        return $this->db->affected_rows() > 0;
    }
    function insert($data) {
        return $this->db->insert('cupones_descuento', $data);
    }
/*
    function insertar_campania($data){
        return $this->db->insert('campania', $data);
    }

    function update_campania($id, $data) {
        $this->db->where('id', $id);
        $this->db->update('campania', $data);
        return $this->db->affected_rows() > 0;
    }
 *
 */
}