<?php
/* 
 * 
 * MODELO DE CAMPAÑAS (DFP)
 * ------------------------------------
 * 
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Campania extends Model {

    function __construct() {
        parent::__construct();
    }
    
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
    
    function get_campania_by_id($id) {
        $this->db->where('id', $id);
        $this->db->where('eliminada', '0');
        $query = $this->db->get('campania');
        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }
    
    function insertar_campania($data){
        return $this->db->insert('campania', $data);
    }
    
    function update_campania($id, $data) {
        $this->db->where('id', $id);
        $this->db->update('campania', $data);
        return $this->db->affected_rows() > 0;
    }
}