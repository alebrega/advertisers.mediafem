<?php

/*
 *
 * MODELO DE CAMPAÑAS (DFP)
 * ------------------------------------
 *
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Campanias extends CI_Model {

    private $table = 'campania';
    private $table_anunciantes_adservers = 'anunciantes_adservers';
    private $tabla_canales_tematicos = 'campanias_canales_tematicos';
    private $table_creatividades = 'archivos_creatividades';
    private $table_segmentacion = 'segmentacion';
    private $table_audiencias = 'campanias_audiencias';
    private $table_unificadas = 'campanias_unificadas';

    function __construct() {
        parent::__construct();
    }

    function get_campania_padre($id_campania){
        $this->db->where('id_campania_hija', $id_campania);
        $query = $this->db->get($this->table_unificadas);
        if ($query->num_rows() > 0)
            return $query->row();
        return NULL;
    }

    function get_campanias() {
        $this->db->where('eliminada', '0');
        $this->db->where('alta_finalizada', '1');
        $query = $this->db->get($this->table);
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_campania_by_id_campanias_canales_tematicos($id) {
        $this->db->where('id_campania', $id);
        $query = $this->db->get($this->tabla_canales_tematicos);
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_campanias_activadas() {
        $this->db->where('eliminada', '0');
        $this->db->where('activada', '1');
        $this->db->where('alta_finalizada', '1');
        $query = $this->db->get($this->table);
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_campania_by_id($id) {
        $this->db->where('id', $id);
        $this->db->where('eliminada', '0');
        //$this->db->where('alta_finalizada', '1');
        $query = $this->db->get($this->table);
        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    function get_campania_by_lineItem_appnexus($id) {
        $this->db->where('id_lineItem_appnexus', $id);
        $this->db->where('eliminada', '0');
        $this->db->where('alta_finalizada', '1');
        $query = $this->db->get($this->table);
        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    function get_campania_by_order_id($id) {
        $this->db->where('id_lineItem_appnexus', $id);
        $this->db->where('eliminada', '0');
        $this->db->where('alta_finalizada', '1');
        $query = $this->db->get($this->table);

        if ($query->num_rows() == 1)
            return $query->row();

        $this->db->where('id_orden_dfp', $id);
        $this->db->where('eliminada', '0');
        $this->db->where('alta_finalizada', '1');
        $query = $this->db->get($this->table);

        if ($query->num_rows() == 1)
            return $query->row();

        return NULL;
    }

    function get_campania_by_nombre($nombre) {
        $this->db->where('nombre', $nombre);
        $this->db->where('alta_finalizada', '1');
        $query = $this->db->get($this->table);
        if ($query->num_rows() >= 1)
            return $query->row();
        return NULL;
    }


    function get_campanias_by_anunciante_periodicas($id) {
        $fecha_actual = date('Y-m-j');
        $fecha_anterior = strtotime( '-2 month' , strtotime ( $fecha_actual ) ) ;
        $fecha_anterior = date( 'Y-m-j' , $fecha_anterior );

        $query = 'SELECT campania.*, segmentacion.descripcion as descripcion_segmentacion, anunciantes_adservers.nombre as nombre_anunciante FROM campania, segmentacion, anunciantes_adservers WHERE id_anunciante = ' . $id . ' AND alta_finalizada = 1 AND segmentacion.id = campania.segmentacion_id AND anunciantes_adservers.id = campania.id_anunciante AND campania.fecha_alta BETWEEN "' . $fecha_anterior . ' 00:00:00" AND "' . $fecha_actual . ' 23:59:59" ORDER BY campania.nombre DESC;';

        $query = $this->db->query($query);

        if($query->num_rows() > 0)
            return $query->result();
        return NULL;
    }


    function get_campanias_by_anunciante($id) {
        $this->db->select($this->table . '.*');
        $this->db->select($this->table_segmentacion . '.descripcion as descripcion_segmentacion');
        $this->db->select($this->table_anunciantes_adservers . '.nombre as nombre_anunciante');

        $this->db->where('id_anunciante', $id);
        $this->db->where('alta_finalizada', '1');
        $this->db->where('eliminada', '0');
        
        $this->db->join($this->table_segmentacion, $this->table_segmentacion . ".id = " . $this->table . ".segmentacion_id");
        $this->db->join($this->table_anunciantes_adservers, $this->table_anunciantes_adservers . ".id = " . $this->table . ".id_anunciante");

        $query = $this->db->get($this->table);

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_campanias_by_usuario_creador($id) {
        $this->db->where('usuario_creador', $id);
        $this->db->where('eliminada', '0');
        $this->db->where('alta_finalizada', '1');
        $query = $this->db->get($this->table);

        if ($query->num_rows() > 0)
            return $query->result();

        return null;
    }

    function get_last_campania_by_user($id_anunciante) {
        $query = $this->db->query("select id from $this->table where id_anunciante = $id_anunciante order by id desc limit 1;");

        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    function get_last_campania() {
        $query = $this->db->query("select * from $this->table where alta_finalizada = 1 order by id desc limit 1;");

        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    function get_campania_by_name($nombre) {
        $this->db->where('nombre', $nombre);
        $this->db->where('eliminada', '0');
        $this->db->where('alta_finalizada', '1');
        $query = $this->db->get($this->table);
        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    function get_creatividades_campanias($id_campania) {
        $this->db->where('id_campania', $id_campania);
        $query = $this->db->get($this->table_creatividades);

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_full_status_by_campania($id_campania){
        $query = $this->db->query("SELECT fecha_reporte, imps_ayer, clicks_ayer, vistas_ayer FROM status_campanias WHERE id_anunciante = $id_campania AND fila_total_campania = 1 ORDER BY fecha_reporte ASC;");

        if ($query->num_rows() > 0)
            return $query->result();

        return NULL;
    }

    function get_full_status_by_campania_appnexus($id_campania){
        //$query = $this->db->query("SELECT fecha_reporte, imps_ayer, clicks_ayer, vistas_ayer FROM status_campanias WHERE id_line_item = $id_campania AND fila_total_campania = 1 ORDER BY fecha_reporte ASC;");

        $query = $this->db->query("SELECT sc.fecha_reporte, sc.imps_ayer, sc.clicks_ayer, sc.modalidad_de_compra
                                    FROM status_campanias sc, anunciantes_adservers aa, campania ca
                                    WHERE (ca.id_lineItem_appnexus = $id_campania)
                                    AND aa.id = ca.id_anunciante
                                    AND sc.id_anunciante = aa.id_appnexus
                                    AND fila_total_campania = 1
                                    ORDER BY fecha_reporte ASC;");



        if ($query->num_rows() > 0)
            return $query->result();

        return NULL;
    }


    function get_status_by_campania($fecha_desde, $fecha_hasta, $id_campania){
        $query = $this->db->query("SELECT * FROM status_campanias WHERE id_anunciante = $id_campania AND date(fecha_reporte) between '$fecha_desde' and '$fecha_hasta' ORDER BY id DESC LIMIT 1;");

        if ($query->num_rows() > 0)
            return $query->row();

        return NULL;
    }

    function get_status_by_campania_appnexus($fecha_desde, $fecha_hasta, $id_campania){
        $query = $this->db->query("SELECT sc.fecha_reporte, sc.imps_ayer, sc.clicks_ayer
                                    FROM status_campanias sc, anunciantes_adservers aa, campania ca
                                    WHERE (ca.id_lineItem_appnexus = $id_campania)
                                    AND aa.id = ca.id_anunciante
                                    AND sc.id_anunciante = aa.id_appnexus
                                    AND fila_total_campania = 1
                                    ORDER BY fecha_reporte ASC;");
        if ($query->num_rows() > 0)
            return $query->row();

        return NULL;
    }

    function insertar_campania($data) {
        return $this->db->insert($this->table, $data);
    }

    function insertar_campania_unificada($data) {
        return $this->db->insert('campanias_unificadas', $data);
    }

    function update_campania_unificada($id_campania_hija, $data) {
        $this->db->where('id_campania_hija', $id_campania_hija);
        $this->db->update($this->table_unificadas, $data);
        if($this->db->affected_rows() > 0){
            return TRUE;
        }else{
            $this->insertar_campania_unificada($data);
        }
    }

    function delete_campania_unificada($id_campania_hija){
        $this->db->where('id_campania_hija', $id_campania_hija);
        $this->db->delete($this->table_unificadas);

        if ($this->db->affected_rows() > 0)
            return TRUE;

        return FALSE;
    }

    function insertar_audiencia_campania($data) {
        return $this->db->insert($this->table_audiencias, $data);
    }

    function get_audiencias_by_campania($id_campania){
        $query = $this->db->query("select a.*, ca.* from audiencias a, campanias_audiencias ca where ca.id_campania = $id_campania and a.id = ca.id_audiencia;");

        if ($query->num_rows() > 0)
            return $query->result();

        return NULL;
    }

    function get_status_by_campania_and_estado($fecha_desde, $fecha_hasta, $id_campania, $estado){
        $query = $this->db->query("SELECT * FROM status_campanias WHERE id_anunciante = $id_campania AND estado = '$estado' AND date(fecha_reporte) between '$fecha_desde' and '$fecha_hasta' ORDER BY id DESC LIMIT 1;");

        if ($query->num_rows() > 0)
            return $query->row();

        return NULL;
    }

    function get_consumido_by_campania($fecha_desde, $fecha_hasta, $id_campania){
        $query = $this->db->query("SELECT consumido FROM status_campanias WHERE id_anunciante = $id_campania AND date(fecha_reporte) between '$fecha_desde' and '$fecha_hasta' ORDER BY id DESC LIMIT 1;");

        if ($query->num_rows() > 0)
            return $query->row();

        return NULL;
    }

    function get_consumido_by_campania_appnexus($fecha_desde, $fecha_hasta, $id_campania){
        $query = $this->db->query("SELECT consumido FROM status_campanias WHERE id_line_item = $id_campania AND date(fecha_reporte) between '$fecha_desde' and '$fecha_hasta' ORDER BY id DESC LIMIT 1;");

        if ($query->num_rows() > 0)
            return $query->row();

        return NULL;
    }

    function update_campania($id, $data) {
        $this->db->where('id', $id);
        $this->db->update($this->table, $data);
        return $this->db->affected_rows() > 0;
    }

    function update_campania_toda_la_red($id, $data) {
        $this->db->where('id', $id);
        $this->db->update($this->table, $data);
        return $this->db->affected_rows() > 0;
    }

}