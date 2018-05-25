<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Anunciantessaldos extends CI_Model {

    private $table_name = 'anunciantes_saldos';

    function __construct() {
        parent::__construct();
    }

    function get_saldos_por_anunciante($id) {

        $this->db->where($this->table_name . '.id_anunciante', $id);
        $this->db->order_by('fecha', 'desc');
        $query = $this->db->get($this->table_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_ultimo_saldo_por_anunciante($id) {

        $this->db->where($this->table_name . '.id_anunciante', $id);
        $this->db->order_by('fecha', 'desc');

        $query = $this->db->get($this->table_name, 1, 0);

        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    function get_saldos_por_campania($id) {
        $this->db->where($this->table_name . '.id_anunciante', $id);
        $this->db->where($this->table_name . '.id_campania !=', '');
        $this->db->limit(1);
        $this->db->order_by('fecha', 'desc');
        $query = $this->db->get($this->table_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function insert_anunciante_saldo($data) {
        return $this->db->insert($this->table_name, $data);
    }

}