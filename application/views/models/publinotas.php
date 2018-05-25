<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Publinotas extends CI_Model {

    private $table = 'publinotas';
    private $table_anunciantes_adservers = 'anunciantes_adservers';

    function __construct() {
        parent::__construct();
    }

    function get_publinota_by_id($id_publinota) {
        $this->db->where('id', $id_publinota);
        $query = $this->db->get($this->table);

        if ($query->num_rows() == 1)
            return $query->row();

        return NULL;
    }

    function get_publinotas_by_anunciante($id) {
        $this->db->select($this->table . '.*');
        $this->db->select($this->table_anunciantes_adservers . '.nombre as nombre_anunciante');

        $this->db->where('Id_anunciante', $id);
        $this->db->where('alta_finalizada', 1);

        $this->db->join($this->table_anunciantes_adservers, $this->table_anunciantes_adservers . ".id = " . $this->table . ".Id_anunciante");

        $query = $this->db->get($this->table);

        if ($query->num_rows() > 0)
            return $query->result();

        return null;
    }

    function insertar($data) {
        return $this->db->insert($this->table, $data);
    }

    function update($id, $data) {
        $this->db->where('id', $id);
        $this->db->update($this->table, $data);
        return $this->db->affected_rows() > 0;
    }
}