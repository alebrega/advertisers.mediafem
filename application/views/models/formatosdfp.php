<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Formatosdfp extends CI_Model {

    private $table_name = 'formatos_dfp';

    function __construct() {
        parent::__construct();
    }

    function get_formato_by_id($id) {
        $this->db->where('id', $id);

        $query = $this->db->get($this->table_name);
        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    function get_formato_by_valor($valor) {
        $this->db->where('valor', $valor);

        $query = $this->db->get($this->table_name);
        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    function get_formato_by_data_type($data_type) {
        $this->db->where('data_type_anunciantes', $data_type);

        $query = $this->db->get($this->table_name);
        if ($query->num_rows() == 1)
            return $query->row();

        if ($query->num_rows() > 0)
            return $query->result();

        return NULL;
    }

    function get_formato_by_sizes($alto, $ancho) {
        $this->db->where('height', $alto);
        $this->db->where('width', $ancho);
        $this->db->where('estado', 'A');

        $no_mostrar_formatos = array(9, 12);
        $this->db->where_not_in('id',$no_mostrar_formatos);

        $query = $this->db->get($this->table_name);
        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    function get_formatos() {
        $this->db->where('estado', 'A');
        $this->db->where_not_in('id', '9', '10', '11');
        $this->db->order_by('id', 'asc');

        $query = $this->db->get($this->table_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_all_formats() {
        $this->db->where_not_in('id', 9);
        $this->db->order_by('id', 'asc');

        $query = $this->db->get($this->table_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_tradicionales() {
        $no_tradicionales = array(
            'Layer - Skin',
            'Skin',
            'Layer',
            'Skin - nuevo',
            'Layer - nuevo',
            'Todos los formatos',
            'Expandible',
            'Video Zócalo',
            'Video Viral',
            'Layer (Rich-Media)',
            'Skin (Rich-Media)',
            'Layer (Rich Media) - viejo',
            'Skin (Rich Media) - viejo',
            '137x31',
            '640x480 (Video Stream)',
            '400x300 (Video Stream)',
            '300x50 (Mobile)',
            '300x600',
            '234x60',
            'Layer - Skin (Rich Media)',
            '320x50 (Mobile)',
            '450x50',
            'Facebook Like Ads',
            'Twitter Timeline ads',
            'Video in Banner',
            'Pre Roll',
            'Overlay',
            '640x480 (Video)',
            '400x300 (Video)',
            '1024x768',
            '216x36',
            '88x31',
            '940x230'
        );

        $this->db->where_not_in('descripcion', $no_tradicionales);
        $this->db->order_by('id', 'asc');

        $query = $this->db->get($this->table_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_video_zocalo() {
        $this->db->where('descripcion', 'Video Zócalo');

        $query = $this->db->get($this->table_name);

        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return null;
        }
    }

    function get_video_viral() {
        $this->db->where('descripcion', 'Video Viral');

        $query = $this->db->get($this->table_name);

        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return null;
        }
    }

    function get_facebook_like() {
        $this->db->where('descripcion', 'Facebook Like Ads');

        $query = $this->db->get($this->table_name);

        if ($query->num_rows() > 0)
            return $query->row();
        return null;
    }

    function get_twitter_timeline() {
        $this->db->where('descripcion', 'Twitter Timeline ads');

        $query = $this->db->get($this->table_name);

        if ($query->num_rows() > 0)
            return $query->row();
        return null;
    }

    function get_pre_roll() {
        $this->db->where('descripcion', 'Pre Roll');

        $query = $this->db->get($this->table_name);

        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return null;
        }
    }

    function get_overlay() {
        $this->db->where('descripcion', 'Overlay');

        $query = $this->db->get($this->table_name);

        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return null;
        }
    }

    function get_video_in_banner() {
        $this->db->where('descripcion', 'Video in Banner');

        $query = $this->db->get($this->table_name);

        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return null;
        }
    }


    function get_video() {
        $this->db->where('tipo', 4);

        $query = $this->db->get($this->table_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_mobile() {
        $this->db->where('tipo', 5);

        $query = $this->db->get($this->table_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }
}