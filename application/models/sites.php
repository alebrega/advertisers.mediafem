<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Sites extends CI_Model {

    private $table_name = 'sites';

    function __construct() {
        parent::__construct();
    }

    function registrar_pago($data) {
        return $this->db->insert($this->table_name, $data);
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

    function get_all_sites($order_by = 'id_site', $sort = 'desc') {
        /*
          $this->db->order_by($order_by, $sort);
          $query = $this->db->get($this->table_name);
          if ($query->num_rows() > 0) {
          return $query->result();
          } else {
          return null;
          }
         *
         */
        
        $query = $this->db->query("select s.* from sites s, users u
                                    where s.user_id = u.id
                                    and u.activated = 1
                                    and u.aprobado = 1
                                    and u.usuario_prueba = 0
                                    and s.nombre_appnexus is not null
                                    and s.nombre_appnexus != ''	 
                                    order by $order_by $sort;");

        if ($query->num_rows() > 0)
            return $query->result();

        return null;
    }

    function get_sitios_sin_categorias_ocultas($id_cats) {

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

}