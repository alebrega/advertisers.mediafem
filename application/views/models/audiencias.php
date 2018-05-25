<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Audiencias extends CI_Model {

    private $table = 'audiencias';

    function __construct() {
        parent::__construct();
    }

    function get_by_anunciante($id_anunciante){
        $this->db->where('id_anunciante', $id_anunciante);
        $this->db->order_by('name', 'asc');

        $query = $this->db->get($this->table);

        if ($query->num_rows() > 0)
            return $query->result();

        return null;
    }

    function insert($data) {
        if ($this->db->insert($this->table, $data))
            return TRUE;

        return NULL;
    }

    function update($id, $data) {
        $this->db->where('id', $id);
        $this->db->update($this->table, $data);
        return $this->db->affected_rows() > 0;
    }

}

