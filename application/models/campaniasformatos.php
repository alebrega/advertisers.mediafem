<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Campaniasformatos extends CI_Model {

    private $table_name = 'campanias_formatos';
    private $table_modalidades = 'modalidades_de_compra';

    function __construct() {
        parent::__construct();
    }

    function update_campania_formato($id_campania, $data) {
        $this->db->where('id_campania', $id_campania);
        $this->db->update($this->table_name, $data);
        return $this->db->affected_rows() > 0;
    }

    function insert_campania_formato($data) {
        return $this->db->insert($this->table_name, $data);
    }

    function get_formatos_by_campania($id) {

        $this->db->select($this->table_name . '.id_campania');
        $this->db->select($this->table_name . '.id_formato');
        $this->db->select($this->table_name . '.monto');
        $this->db->select($this->table_name . '.id_modalidad_compra');
        $this->db->select($this->table_name . '.cantidad');
        //$this->db->select($this->table_modalidades . '.descripcion as desc_modalidad');
        $this->db->select($this->table_name . '.pagina_destino');

        //$this->db->join($this->table_modalidades, $this->table_modalidades . ".id = " . $this->table_name . ".id_modalidad_compra");

        $this->db->where('id_campania', $id);

        $query = $this->db->get($this->table_name);
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_formatos_by_order_dfp($id) {

        $query = $this->db->query("SELECT c.*, cf.*
                                   FROM campania c, campanias_formatos cf
                                   WHERE c.id_orden_dfp = $id AND cf.id_campania = c.id;"
        );

        if ($query->num_rows() > 0) {
            return $query->result();
        } else if ($query->num_rows() == 0) {
            return $query->row();
        } else {
            return null;
        }
    }

    function get_formatos_by_lineItem_appnexus($id) {
        $query = $this->db->query("SELECT c.*, cf.*
                                   FROM campania c, campanias_formatos cf
                                   WHERE c.id_lineItem_appnexus = $id AND cf.id_campania = c.id;"
        );
        
        if ($query->num_rows() > 0) {
            return $query->result();
        } else if ($query->num_rows() == 0) {
            return $query->row();
        } else {
            return null;
        }
    }

    function delete_formatos_by_campania($id_campania) {
        return $this->db->query("delete from $this->table_name where id_campania=$id_campania;");
    }

}