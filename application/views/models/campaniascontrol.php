<?php
/*
 *
 * MODELO DE CAMPAÑAS (DFP)
 * ------------------------------------
 *
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Campaniascontrol extends CI_Model {

    private $table_control = 'campania_control';
    private $table_lineItem = 'campania_lineItem';

    function __construct() {
        parent::__construct();
    }

    function get_control_by_campania_id($id) {
        $this->db->where('id_campania', $id);
        $query = $this->db->get($this->table_control);
        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    function get_lineItems_by_campania_id($id) {
        $this->db->where('id_campania', $id);
        $query = $this->db->get($this->table_lineItem);
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function insert_control($data) {
        return $this->db->insert($this->table_control, $data);
    }

    function update_control_campania($id, $data) {
        $this->db->where('id_campania', $id);

        $this->db->update($this->table_control, $data);
        return $this->db->affected_rows() > 0;
    }
}