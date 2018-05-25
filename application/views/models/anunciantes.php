<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Anunciantes extends CI_Model {

    private $table_name = 'anunciantes';
    private $table_asoc = 'anunciantes_asociados';   // user accounts
    private $anunciantes_adservers = 'anunciantes_adservers';

    function __construct() {
        parent::__construct();
    }

    function get_all_anunciantes() {
        $query = $this->db->get($this->table_name);
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_anunciantes_activos() {
        $this->db->where('activated', '1');
        $this->db->order_by('created', 'desc');

        $query = $this->db->get($this->table_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_anunciantes_by_id_DFP($q) {
        $this->db->where('id_dfp', $q);
        $query = $this->db->get($this->anunciantes_adservers);

        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    function get_anunciantes_by_name($q) {

        $this->db->like('LOWER(name)', strtolower($q));
        $query = $this->db->get($this->table_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_anunciante_by_name($q) {

        $this->db->where('name', $q);
        $query = $this->db->get($this->table_name);

        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    function get_anunciante_by_id($id) {

        $this->db->where('id', $id);
        $query = $this->db->get($this->table_name);

        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    function get_anunciantes_asociados_by_id($id) {
        $this->db->where('id_anunciante_redvlog', $id);

        $query = $this->db->get($this->table_asoc);

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_anunciantes_asociados() {
        $query = $this->db->get($this->table_asoc);

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function create_anunciante($data, $activated = TRUE) {
        $data['created'] = date('Y-m-d H:i:s');
        $data['activated'] = $activated ? 1 : 0;

        if ($this->db->insert($this->table_name, $data)) {
            $user_id = $this->db->insert_id();
            return $user_id;
        }
        return NULL;
    }

    function insert_anunciante_asociado($data) {
        if ($this->db->insert($this->table_asoc, $data)) {
            return TRUE;
        }
        return NULL;
    }

    function delete_anunciante_asociado($id_anunciante_redvlog, $id_anunciante_adserver) {
        $this->db->where('id_anunciante_redvlog', $id_anunciante_redvlog);
        $this->db->where('id_anunciante_adserver', $id_anunciante_adserver);
        $this->db->delete($this->table_asoc);

        if ($this->db->affected_rows() > 0) {
            return TRUE;
        }
        return FALSE;
    }

    function get_anunciante_asociado_by_id($id_anunciante_redvlog, $id_anunciante_adserver) {
        $this->db->where('id_anunciante_redvlog', $id_anunciante_redvlog);
        $this->db->where('id_anunciante_adserver', $id_anunciante_adserver);

        $query = $this->db->get($this->table_asoc);

        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    function update_anunciante_asociado($id, $data) {
        $this->db->where('id', $id);
        $this->db->update($this->table_asoc, $data);
        return $this->db->affected_rows() > 0;
    }

    function delete_user($user_id) {
        $this->db->where('id', $user_id);
        $this->db->update($this->table_name, array(
            'activated' => 0
        ));
        if ($this->db->affected_rows() > 0) {
            return TRUE;
        }
        return FALSE;
    }

    function active_user($user_id) {
        $this->db->where('id', $user_id);
        $this->db->update($this->table_name, array(
            'activated' => 1
        ));
        if ($this->db->affected_rows() > 0) {
            return TRUE;
        }
        return FALSE;
    }

    function is_username_available($username) {
        $this->db->select('1', FALSE);
        $this->db->where('LOWER(username)=', strtolower($username));

        $query = $this->db->get($this->table_name);
        return $query->num_rows() == 0;
    }

    function update_anunciante($user_id, $data) {
        $this->db->where('id', $user_id);
        $this->db->update($this->table_name, $data);
        return $this->db->affected_rows() > 0;
    }

    function get_anunciante_by_anunciante_asociado($id_anunciante_mediafem, $id_anunciante_adserver) {

        $query = $this->db->query("select a2.*
        from anunciantes_asociados a1, anunciantes a2
        where (a1.id_anunciante_adserver = $id_anunciante_adserver and a1.id_anunciante_redvlog = $id_anunciante_mediafem)
        and a1.id_anunciante_redvlog = a2.id;");

        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    // ANUNCIANTES ADSERVERS
    function get_all_anunciantes_adservers() {
        $this->db->order_by('nombre', 'asc');
        $query = $this->db->get($this->anunciantes_adservers);
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_all_anunciantes_appnexus() {
        $this->db->where('id_appnexus !=', '');
        $this->db->group_by('id_appnexus');
        $this->db->order_by('nombre', 'asc');
        $query = $this->db->get($this->anunciantes_adservers);
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_anunciante_adserver_by_id($id) {
        $this->db->where('id', $id);
        $query = $this->db->get($this->anunciantes_adservers);

        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    function get_anunciante_adserver_by_id_appnexus($id) {
        $this->db->where('id_appnexus', $id);
        $query = $this->db->get($this->anunciantes_adservers);

        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    function get_anunciante_adserver_by_nombre($nombre) {
        $this->db->where('nombre', $nombre);
        $query = $this->db->get($this->anunciantes_adservers);

        if ($query->num_rows() > 0)
            return $query->row();
        return NULL;
    }

    function insert_anunciante_adserver($data) {
        return $this->db->insert($this->anunciantes_adservers, $data);
    }

    function update_anunciante_adserver($anunciante_id, $data) {
        $this->db->where('id', $anunciante_id);
        $this->db->update($this->anunciantes_adservers, $data);
        return $this->db->affected_rows() > 0;
    }

}

