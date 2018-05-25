<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Sitescategories extends CI_Model {

    private $table_name = 'sitios_categorias';

    function __construct() {
        parent::__construct();
    }

    function get_sites_in_categories($categories) {
        $query = $this->db->query("SELECT s.id, s.nombre_appnexus, s.nombre_dfp, s.id_adunit_site, s.id_site FROM sitios_categorias sc, sites s WHERE sc.id_categoria IN ($categories) AND s.id = sc.id_sitio AND s.id_adunit_site != '' GROUP BY s.id;");
        if ($query->num_rows() > 0)
            return $query->result();
        return null;
    }

    function get_all_sites_by_category($cat_id) {

        $query = $this->db->query("select * from $this->table_name where id_categoria = $cat_id;");

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_all_cats_by_site($site_id) {
        $this->db->where('id_sitio', $site_id);
        $query = $this->db->get($this->table_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_cats_by_site($site_id) {
        //$query = $this->db->query("select c.nombre from categorias c, sitios_categorias sc WHERE sc.id_sitio = $site_id AND c.excluir = 0 AND c.estado = 'A';");
        $query = $this->db->query("SELECT c.nombre FROM categorias c, sitios_categorias sc WHERE sc.id_sitio = $site_id AND sc.id_categoria = c.id AND c.excluir = 0 AND c.estado = 'A';");
        if ($query->num_rows() > 0)
            return $query->result();
        return null;
    }

    function get_cat_by_site($site_id, $id_categoria) {
        $this->db->where('id_sitio', $site_id);
        $this->db->where('id_categoria', $id_categoria);
        $query = $this->db->get($this->table_name);

        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

}