<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Reportesusuarios extends CI_Model {

    private $table_name = 'reportes_usuarios';

    function __construct() {
        parent::__construct();
    }

    /* Inserto fecha en que se presiono el boton visualizar */

    function insertar_reporte_usuarios($data_insert) {

        $this->db->insert($this->table_name, $data_insert);
    }

    function update($data_update, $user_id) {
        $this->db->where('user_id', $user_id);
        $this->db->where('aplicacion', 2);
        $this->db->order_by('id', 'desc');
        $this->db->limit(1);

        $query = $this->db->get($this->table_name);

        if ($query->num_rows() == 1) {
            $registro = $query->row();

            $this->db->where('id', $registro->id);
            $this->db->update($this->table_name, $data_update);


        }
    }

    function get_inventario_by_usuarios_unicos() {
        $query = $this->db->query("SELECT * from usuarios_unicos order by id;");

        if ($query->num_rows() > 0)
            return $query->result();

       return null;
    }
    /* Inserto fecha en que se mostro el reporte */

    function update_fecha_viasualizacion($data_update, $user_id) {
        $this->db->where('user_id', $user_id);
        $this->db->where('aplicacion', 1);
        $this->db->order_by('id', 'desc');
        $this->db->limit(1);

        $query = $this->db->get($this->table_name);

        if ($query->num_rows() == 1) {
            $registro = $query->row();

            $this->db->where('id', $registro->id);
            $this->db->update($this->table_name, $data_update);


        }
    }
    /*update campo no deseo esperar a 1*/
function no_deseo_esperar ($data_update){
      $this->db->where('user_id', $user_id);
        $this->db->where('aplicacion', 2);
        $this->db->order_by('id', 'desc');
        $this->db->limit(1);

        $query = $this->db->get($this->table_name);

        if ($query->num_rows() == 1) {
            $registro = $query->row();

            $this->db->where('id', $registro->id);
            $this->db->update($this->table_name, $data_update);


        }


}
    function get_all_ages() {
        $this->db->order_by('id', 'asc');

        $query = $this->db->get($this->table_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

}