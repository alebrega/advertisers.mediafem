<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Reportes extends CI_Model {

    private $table_name = 'suscripcion_reportes';

    function __construct() {
        parent::__construct();
    }

    function get_suscripcion_by_enviado($enviado) {
        $this->db->where('enviado', $enviado);
        $this->db->where('solicitado_desde', 'ANUNCIANTES');

        $query = $this->db->get($this->table_name);
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function insertar_suscripcion($data) {
        if ($this->db->insert($this->table_name, $data))
            return TRUE;

        return NULL;
    }

    function update_suscripcion($id_suscripcion, $data) {
        $this->db->where('id', $id_suscripcion);

        $this->db->update($this->table_name, $data);
        return $this->db->affected_rows() > 0;
    }
     
    
}