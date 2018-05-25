<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Sites extends CI_Model {

    private $table_name = 'sites';

    function __construct() {
        parent::__construct();
    }

    function get_site_by_id($id_site) {
        $this->db->where('id', $id_site);

        $query = $this->db->get($this->table_name);

        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    function get_sites_by_date($fecha_desde, $fecha_hasta) {
        $query = $this->db->query("select * from $this->table_name where date(fecha_alta) between '$fecha_desde' and '$fecha_hasta'");
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_all_sites() {
        $this->db->order_by('id_site', 'desc');
        $query = $this->db->get($this->table_name);
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_sitios_sin_categorias_ocultas($id_cats){

        $query = $this->db->query("select s.* from sites s, sitios_categorias c
                                    where c.id_categoria not in ($id_cats)
                                    and c.id_sitio = s.id
                                    group by s.id;");

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }

    }

    function get_site_by_nombre_eplanning($nombre) {
        $this->db->where('nombre_eplanning', $nombre);

        $query = $this->db->get($this->table_name);

        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    function get_site_by_id_adunit_publisher($id) {
        $this->db->where('id_adunit_publisher', $id);

        $query = $this->db->get($this->table_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }
    
    
     function get_inventario_fechas_esp($fecha_desde, $fecha_hasta) {
        $query = $this->db->query("SELECT i.adUnit_ID_2, i.adUnit_2 FROM inventario_anunciantes i 
WHERE (i.fecha_datos BETWEEN '".$fecha_desde."' AND '".$fecha_hasta."')group by i.adUnit_ID_2;
");

        if ($query->num_rows() > 0)
            return $query->result();

       return null;
    }
}