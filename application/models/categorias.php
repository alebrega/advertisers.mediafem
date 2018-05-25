<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Categorias extends CI_Model {

    private $table_name = 'categorias';

    function __construct() {
        parent::__construct();
    }

    function get_categoria_by_id($categoria_id) {
        $this->db->where('id', $categoria_id);

        $query = $this->db->get($this->table_name);
        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    function update_category($categoria_id, $data) {
        $this->db->where('id', $categoria_id);

        $this->db->update($this->table_name,$data);
        return $this->db->affected_rows() > 0;
    }

    function add_category($data) {
        return $this->db->insert($this->table_name, $data);
    }

    function get_categorias() {
        $this->db->where('excluir', '0');
        $this->db->where('estado', 'A');
        $this->db->order_by('nombre', 'asc');

        $query = $this->db->get($this->table_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_categorias_excluidas() {
        $this->db->where('excluir', '1');
        $this->db->order_by('nombre', 'asc');

        $query = $this->db->get($this->table_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

}