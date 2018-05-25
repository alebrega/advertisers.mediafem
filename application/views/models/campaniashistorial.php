<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Campaniashistorial extends CI_Model {

    private $table = 'campanias_historial';
    private $table_comentario = 'campanias_comentarios';

    function __construct() {
        parent::__construct();
    }

    function insert($data){
        return $this->db->insert($this->table, $data);
    }

    function insert_comentario($data){
        return $this->db->insert($this->table_comentario, $data);
    }

}