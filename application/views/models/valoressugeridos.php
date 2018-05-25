<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Valoressugeridos extends CI_Model {

    private $table_name = 'valores_sugeridos_por_segmentacion_pais';

    function __construct() {
        parent::__construct();
    }

    function get_valores($id_pais, $id_categorias) {
        $query = $this->db->query("SELECT * FROM $this->table_name WHERE id_pais IN ($id_pais) AND id_categoria IN ($id_categorias) ORDER BY cpm_sugerido DESC LIMIT 1;");

        //return "SELECT * FROM $this->table_name WHERE id_pais IN ($id_pais) AND id_categoria IN ($id_categorias) ORDER BY cpm_sugerido DESC LIMIT 1;";

        if ($query->num_rows() > 0)
            return $query->row();

        return NULL;
    }

    function update($id, $data) {
        $this->db->where('id', $id);
        $this->db->update($this->table_name, $data);
        return $this->db->affected_rows() > 0;
    }
}