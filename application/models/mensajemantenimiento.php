<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Mensajemantenimiento extends CI_Model {

    private $table_name = 'mensaje_mantenimiento';

    function __construct() {
        parent::__construct();
    }

    function get_all_mensajes() {
        $this->db->order_by('id', 'asc');
        $query = $this->db->get($this->table_name);
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_mensaje_mantenimiendo_by_id($mensaje_id) {
        //Query the data table for every record and row
        $this->db->where('id', $mensaje_id);
        $query = $this->db->get($this->table_name);

        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    function get_mensaje_mantenimiendo_by_estado($estado) {
        //Query the data table for every record and row
        $this->db->where('estado', $estado);
        $query = $this->db->get($this->table_name);

        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    function inactivar_todos() {
        $data = array('estado' => 'I');
        $this->db->update($this->table_name, $data);
        return $this->db->affected_rows() > 0;
    }

    function update_mensaje_by_id($mensaje_id, $data) {
        $this->db->where('id', $mensaje_id);

        $this->db->update($this->table_name, $data);
        return $this->db->affected_rows() > 0;
    }

}