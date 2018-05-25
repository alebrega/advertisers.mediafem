<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Tarifarios extends CI_Model {

    private $table_name = 'tarifarios';

    function __construct() {
        parent::__construct();
    }

    function get_all_tarifas() {
        $this->db->order_by('id', 'asc');
        $query = $this->db->get($this->table_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_by_formato($formato){
        $this->db->where('id_formato', $formato);
        $query = $this->db->get($this->table_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_by_formato_segmentacion($formato, $segmentacion) {
        $this->db->where('formato', $formato);
        $this->db->where('segmentacion', $segmentacion);
        $this->db->order_by('id', 'asc');
        $query = $this->db->get($this->table_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_by_formato_tipo_segmentacion($formato, $tipo, $segmentacion) {
        $this->db->where('formato', $formato);
        $this->db->where('tipo', $tipo);
        $this->db->where('segmentacion', $segmentacion);
        $this->db->order_by('id', 'asc');
        $query = $this->db->get($this->table_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }
    /*
    function get_by_formato_segmentacion_modalidad($formato, $segmentacion, $modalidad) {
        $this->db->where('id_formato', $formato);
        $this->db->where('id_segmentacion', $segmentacion);
        $this->db->where('modalidad', $modalidad);
        $this->db->order_by('id', 'asc');
        $query = $this->db->get($this->table_name);

        if ($query->num_rows() > 0)
            return $query->row();
        return null;
    }
     *
     */

    function get_by_formato_segmentacion_modalidad($formatos, $id_segmentacion, $modalidad, $paises){
        $query = $this->db->query("
                SELECT * FROM tarifarios
                WHERE modalidad = '$modalidad'
                AND id_segmentacion = '$id_segmentacion'
                AND id_formato IN ($formatos)
                AND id_pais IN ($paises)
                ORDER BY valor DESC LIMIT 1;
            ");

        if ($query->num_rows() > 0)
            return $query->row();

        return null;
    }

    function update($formato, $segmentacion, $modalidad, $valor){
        if( $this->get_by_formato_segmentacion_modalidad($formato, $segmentacion, $modalidad) != null ){
            $data = array('valor' => $valor);
            $this->db->where('id_formato', $formato);
            $this->db->where('id_segmentacion', $segmentacion);
            $this->db->where('modalidad', $modalidad);

            $this->db->update($this->table_name, $data);
        }else{
            $data = array(
                'id_formato' => $formato,
                'id_segmentacion' => $segmentacion,
                'modalidad' => $modalidad,
                'valor' => $valor
                );

            $this->db->insert($this->table_name, $data);
        }
    }

    function get_valor_minimo($modalidad, $id_segmentacion, $formatos, $paises){
        if(!$paises || !$modalidad || !$id_segmentacion || !$formatos)
            return null;

        $query = $this->db->query("
                SELECT * FROM tarifarios
                WHERE modalidad = '$modalidad'
                AND id_segmentacion = '$id_segmentacion'
                AND id_formato IN ($formatos)
                AND id_pais IN ($paises)
                ORDER BY valor_minimo DESC LIMIT 1;
            ");

        if ($query->num_rows() > 0)
            return $query->result();

        return null;
    }
}