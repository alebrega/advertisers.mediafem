<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Segmentacion extends CI_Model {

    private $table_name = 'segmentacion';

    function __construct() {
        parent::__construct();
    }

    function get_all_adservers() {
        $this->db->order_by('id', 'asc');

        $query = $this->db->get($this->table_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_segmentacion() {
        $this->db->where('estado', 'A');
        $this->db->order_by('id', 'asc');

        $query = $this->db->get($this->table_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_segmentacion_by_id($id) {
        $this->db->where('id', $id);

        $query = $this->db->get($this->table_name);
        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

     function update_segmentacion($id, $data) {

        $this->db->where('id', $id);

        $this->db->update($this->table_name, $data);
        return $this->db->affected_rows() > 0;
    }

}