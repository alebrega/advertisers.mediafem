<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Anunciantestarjetas extends CI_Model {

    private $table_name = 'anunciantes_tarjetas_de_credito';

    function __construct() {
        parent::__construct();
    }

    function get_tarjetas_by_idAnunciante($id_anunciante){
        $this->db->where('id_anunciante', $id_anunciante);
        $query = $this->db->get($this->table_name);
        if ($query->num_rows() > 0)
            return $query->result();
        return null;
    }

    function insert($data) {
        return $this->db->insert($this->table_name, $data);
    }

}