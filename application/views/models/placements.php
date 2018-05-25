<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Placements extends CI_Model {

    private $table_name = 'placements';
    private $table_sites = 'sites';
    private $table_formatos_dfp = 'formatos_dfp';

    function __construct() {
        parent::__construct();
    }

    function insert_placement($data) {
        return $this->db->insert($this->table_name, $data);
    }

    function get_placements(){
        $query = $this->db->get($this->table_name);

        if ($query->num_rows() > 0)
            return $query->result();

        return null;
    }

    function get_all_placements() {

        $query = $this->db->query("select * from placements
                                    where id_espacio_appnexus != '0'
                                    and id_espacio_appnexus is not null
                                    and procesado = '0'
                                    and nombre_appnexus is null
                                    order by id_espacio_appnexus desc;");


        /* $query = $this->db->query("select * from placements
          where id = 261;"); */
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_placement_by_id($id) {

        $this->db->where($this->table_name . '.id', $id);

        $query = $this->db->get($this->table_name);
        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    function get_placement_by_id_mediafem($id) {
        $this->db->select($this->table_name . '.id');
        $this->db->select($this->table_name . '.id_espacio_appnexus');
        $this->db->select($this->table_name . '.id_espacio_dfp');
        $this->db->select($this->table_name . '.id_espacio_adx');
        $this->db->select($this->table_name . '.nombre_dfp');
        $this->db->select($this->table_name . '.nombre_adx');
        $this->db->select($this->table_name . '.id_sitio');
        $this->db->select($this->table_name . '.id_age');
        $this->db->select($this->table_name . '.id_position');
        $this->db->select($this->table_name . '.id_tamanio');
        $this->db->select($this->table_name . '.estado');

        $this->db->select($this->table_formatos_dfp . '.descripcion as descripcion_tamanio');
        $this->db->select($this->table_formatos_dfp . '.valor as valor_tamanio');
        $this->db->select($this->table_sites . '.id_adunit_site as id_sitio_dfp');

        $this->db->join($this->table_formatos_dfp, $this->table_formatos_dfp . ".id = " . $this->table_name . ".id_tamanio");
        $this->db->join($this->table_sites, $this->table_sites . ".id = " . $this->table_name . ".id_sitio");

        $this->db->where($this->table_name . '.id', $id);

        $query = $this->db->get($this->table_name);
        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    function update_placement($placement_id, $data) {
        $this->db->where('id', $placement_id);
        $this->db->update($this->table_name, $data);
        return $this->db->affected_rows() > 0;
    }

    function update_placement_dfp($placement_id, $data) {
        $this->db->where('id_espacio_dfp', $placement_id);
        $this->db->update($this->table_name, $data);
        return $this->db->affected_rows() > 0;
    }

    function update_placement_appnexus($placement_id, $data) {
        $this->db->where('id_espacio_appnexus', $placement_id);
        $this->db->update($this->table_name, $data);
        return $this->db->affected_rows() > 0;
    }

    function get_placement_by_id_dfp($id) {
        $this->db->select($this->table_name . '.id');
        $this->db->select($this->table_name . '.id_espacio_dfp');
        $this->db->select($this->table_name . '.nombre_dfp');
        $this->db->select($this->table_name . '.id_sitio');
        $this->db->select($this->table_name . '.id_age');
        $this->db->select($this->table_name . '.id_position');
        $this->db->select($this->table_name . '.id_tamanio');
        $this->db->select($this->table_name . '.estado');

        $this->db->select($this->table_formatos_dfp . '.descripcion as descripcion_tamanio');
        $this->db->select($this->table_formatos_dfp . '.valor as valor_tamanio');

        $this->db->select($this->table_sites . '.id_adunit_site as id_sitio_dfp');

        $this->db->join($this->table_formatos_dfp, $this->table_formatos_dfp . ".id = " . $this->table_name . ".id_tamanio");
        $this->db->join($this->table_sites, $this->table_sites . ".id = " . $this->table_name . ".id_sitio");

        $this->db->where($this->table_name . 'id_espacio_dfp', $id);

        $query = $this->db->get($this->table_name);

        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    function get_placements_by_site_dfp($id) {

        $this->db->select($this->table_name . '.id');
        $this->db->select($this->table_name . '.id_espacio_dfp');
        $this->db->select($this->table_name . '.id_espacio_adx');
        $this->db->select($this->table_name . '.nombre_dfp');
        $this->db->select($this->table_name . '.id_sitio');
        $this->db->select($this->table_name . '.id_age');
        $this->db->select($this->table_name . '.id_position');
        $this->db->select($this->table_name . '.id_tamanio');
        $this->db->select($this->table_name . '.estado');

        $this->db->select($this->table_sites . '.id_adunit_site as id_sitio_dfp');
        $this->db->select($this->table_sites . '.id_site as id_sitio_appnexus');
        $this->db->select($this->table_formatos_dfp . '.descripcion as descripcion_tamanio');

        $this->db->join($this->table_sites, $this->table_sites . ".id = " . $this->table_name . ".id_sitio");
        $this->db->join($this->table_formatos_dfp, $this->table_formatos_dfp . ".id = " . $this->table_name . ".id_tamanio");

        $this->db->where($this->table_name . '.id_sitio', $id);

        $query = $this->db->get($this->table_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_placements_by_site($id) {

        $this->db->select($this->table_name . '.id');
        $this->db->select($this->table_name . '.id_espacio_dfp');
        $this->db->select($this->table_name . '.id_espacio_adx');
        $this->db->select($this->table_name . '.id_espacio_appnexus');
        $this->db->select($this->table_name . '.nombre_dfp');
        $this->db->select($this->table_name . '.nombre_appnexus');
        $this->db->select($this->table_name . '.id_sitio');
        $this->db->select($this->table_name . '.id_age');
        $this->db->select($this->table_name . '.id_position');
        $this->db->select($this->table_name . '.id_tamanio');
        $this->db->select($this->table_name . '.estado');

        $this->db->select($this->table_formatos_dfp . '.descripcion as descripcion_tamanio');

        $this->db->join($this->table_formatos_dfp, $this->table_formatos_dfp . ".id = " . $this->table_name . ".id_tamanio");

        $this->db->where($this->table_name . '.id_sitio', $id);

        $query = $this->db->get($this->table_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_placement_by_id_sitio_and_formato($id_sitio, $id_tamanio) {

        $this->db->where('id_sitio', $id_sitio);
        $this->db->where('id_tamanio', $id_tamanio);

        $query = $this->db->get($this->table_name);
        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    function get_placement_by_id_sitio($id_sitio) {
        $this->db->where('id_sitio', $id_sitio);
        $this->db->where('estado', 'A');
        $this->db->where('id_espacio_dfp !=', 'null');

        $query = $this->db->get($this->table_name);
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_placement_by_id_DFP2($id) {
        $this->db->where('id_espacio_dfp', $id);

        $query = $this->db->get($this->table_name);
        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    function get_placement_by_id_appnexus($id) {
        $this->db->where('id_espacio_appnexus', $id);

        $query = $this->db->get($this->table_name);
        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    function get_data_placement_by_id_DFP($id) {
        $query = $this->db->query("SELECT f.descripcion FROM placements p, formatos_dfp f WHERE p.id_espacio_dfp = '$id' AND f.id = p.id_tamanio;");
        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    function get_data_placement_by_id_appnexus($id) {
        $query = $this->db->query("SELECT f.descripcion FROM placements p, formatos_dfp f WHERE p.id_espacio_appnexus = '$id' AND f.id = p.id_tamanio;");
        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    function get_placement_by_nombre_eplanning($nombre, $id_sitio) {
        $this->db->where('nombre_eplanning', $nombre);
        $this->db->where('id_sitio', $id_sitio);
        $query = $this->db->get($this->table_name);

        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

}