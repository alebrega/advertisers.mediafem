<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Categoriasaexcluir extends CI_Model {

    private $table_name = 'categorias_a_excluir';

    function __construct() {
        parent::__construct();
    }

    function get_all_categorias() {
        
        $query = $this->db->get($this->table_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }


}