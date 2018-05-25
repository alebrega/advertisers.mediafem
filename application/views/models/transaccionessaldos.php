<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Transaccionessaldos extends CI_Model {

    private $table_name = 'anunciantes_trasacciones_saldos';

    function __construct() {
        parent::__construct();
    }

    function insert($data){
        return $this->db->insert($this->table_name, $data);
    }
}