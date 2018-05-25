<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Sites extends Model {

    private $table_name = 'sites';   // user accounts

    function __construct() {
        parent::__construct();
    }

    function get_site_by_id($id_site) {
        $this->db->where('id_site', $id_site);

        $query = $this->db->get($this->table_name);

        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    function get_site_by_redvlog($id_site) {
        $this->db->where('id', $id_site);

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

    function get_site_by_id_adunit_site($id) {
        $this->db->where('id_adunit_site', $id);

        $query = $this->db->get($this->table_name);
        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    function get_site_by_id_mediafem($id_site) {
        $this->db->where('id', $id_site);

        $query = $this->db->get($this->table_name);

        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    function get_sites() {
        $query = $this->db->query("select s.id, s.id_adunit_site, s.nombre_dfp, s.id_site, s.estado, u.id_adunit_publisher, u.publisher_name,
                                (select p.credit from payments p where p.user_id = s.user_id and pago = '0' limit 1) as ingresos_ultimo_mes
                                from sites s, users u
                                where s.user_id is not null and s.user_id != ''
                                and s.nombre_dfp is not null and s.nombre_dfp != ''
                                and s.user_id = u.id
                                order by id desc;");

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

    function get_sites_by_date($fecha_desde, $fecha_hasta) {

        $query = $this->db->query("select * from $this->table_name where date(fecha_alta) between '$fecha_desde' and '$fecha_hasta'");
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

    function add_field($id_campo) {
        return $this->db->query("ALTER TABLE $this->table_name ADD COLUMN `$id_campo` CHAR(1) NULL DEFAULT '0';");
    }

    function insert_site($data) {
        return $this->db->insert($this->table_name, $data);
    }

    function delete_site($id_site) {
        $this->db->where('id_site', $id_site);
        $this->db->delete($this->table_name);
        if ($this->db->affected_rows() > 0) {
            return TRUE;
        }
        return FALSE;
    }

    function update_site($id_site, $data) {
        $this->db->where('id_site', $id_site);

        $this->db->update($this->table_name, $data);
        return $this->db->affected_rows() > 0;
    }

    function update_site_by_mediafem($id_site, $data) {
        $this->db->where('id', $id_site);

        $this->db->update($this->table_name, $data);
        return $this->db->affected_rows() > 0;
    }

    function update_site_by_idDFP($id_site, $data) {
        $this->db->where('id_adunit_site', $id_site);

        $this->db->update($this->table_name, $data);
        return $this->db->affected_rows() > 0;
    }

    function get_sites_by_user($user_id) {

        $this->db->where('user_id', $user_id);

        $query = $this->db->get($this->table_name);
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_sites_by_publisher_dfp($id_publisher) {
        $this->db->where('id_adunit_publisher', $id_publisher);
        $query = $this->db->get($this->table_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

}