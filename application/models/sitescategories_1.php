<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Sitescategories extends Model {

    private $table_name = 'sitios_categorias';

    function __construct() {
        parent::__construct();
    }

    function get_all_sites_by_category($cat_id) {

        $query = $this->db->query("select * from $this->table_name where id_categoria = $cat_id;");

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function insert_site_cat($site_id, $cat_id) {
        $this->db->set('id_sitio', $site_id);
        $this->db->set('id_categoria', $cat_id);

        return $this->db->insert($this->table_name);
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

    function get_cat_by_site($site_id, $id_categoria) {
        $this->db->where('id_sitio', $site_id);
        $this->db->where('id_categoria', $id_categoria);
        $query = $this->db->get($this->table_name);

        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    function delete_site($site_id) {
        $this->db->where('id_sitio', $site_id);
        $this->db->delete($this->table_name);
        if ($this->db->affected_rows() > 0) {
            return TRUE;
        }
        return FALSE;
    }

}