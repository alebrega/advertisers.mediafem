<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Tokens extends CI_Model {

    private $table_name = 'tokens';

    function __construct() {
        parent::__construct();
    }
    
    function insert_token($data){
        return $this->db->insert($this->table_name, $data);
    }

    function get_last_token() {
        
        $query = $this->db->query('select id, token, fecha, TIMESTAMPDIFF(MINUTE, fecha, NOW()) as diferencia from '.$this->table_name.' order by id desc limit 1;');
        
        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }


}