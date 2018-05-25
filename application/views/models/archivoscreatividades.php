<?php
/*
 *
 * MODELO DE CAMPAÑAS (DFP)
 * ------------------------------------
 *
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Archivoscreatividades extends CI_Model {

    private $table = 'archivos_creatividades';

    function __construct() {
        parent::__construct();
    }

    function insert_archivo($data) {
        return $this->db->insert($this->table, $data);
    }

    function delete($id) {
        $this->db->where('id', $id);
        $this->db->delete($this->table);
        if ($this->db->affected_rows() > 0)
            return TRUE;
        return FALSE;
    }

    function get_archivos($id_campania) {
        $this->db->where('id_campania', $id_campania);
        $query = $this->db->get($this->table);

        if ($query->num_rows() > 0)
            return $query->result();

        return null;
    }

    function get_archivo($id_material) {
        $this->db->where('id', $id_material);
        $query = $this->db->get($this->table);

        if ($query->num_rows() == 1)
            return $query->row();

        return null;
    }
}