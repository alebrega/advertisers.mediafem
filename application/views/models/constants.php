<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Constants extends CI_Model {

    private $table_constants = 'constants';

    function __construct() {
        parent::__construct();
    }

    function get_constant_by_id($constant_id) {
        $this->db->where('id', $constant_id);

        $query = $this->db->get($this->table_constants);
        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    function update_constant($constant_id, $data) {
        $this->db->where('id', $constant_id);
        $this->db->update($this->table_constants, $data);
        return $this->db->affected_rows() > 0;
    }
    
    

}