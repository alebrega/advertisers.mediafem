<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Inventario_anunciantes extends CI_Model {

    private $table = 'inventario_anunciantes';

    function __construct() {
        parent::__construct();
    }

    function get_inventario($fecha_desde, $fecha_hasta, $group_by) {
        $query = $this->db->query("SELECT totalImpressions, adUnit_ID_2, adUnit_ID_3, countryAd_ID FROM inventario_anunciantes WHERE fecha_datos BETWEEN '$fecha_desde' AND '$fecha_hasta';");

        if ($query->num_rows() > 0)
            return $query->result();

        return null;
    }

    function get_inventario_appnexus($fecha_desde, $fecha_hasta, $group_by) {
        $query = $this->db->query("SELECT imps_total, site_id, placement_id, geo_country_id FROM inventario_appnexus WHERE fecha_datos BETWEEN '$fecha_desde' AND '$fecha_hasta';");

        if ($query->num_rows() > 0)
            return $query->result();

        return null;
    }

    function get_inventario_by_sites($adunits_id) {
        $fecha_ayer = date('Y-m-d', strtotime("-1 day"));

        $query = $this->db->query("SELECT SUM(totalImpressions) AS totalImpressions, SUM(totalClicks) AS totalClicks FROM inventario_anunciantes WHERE adUnit_ID_2 IN (" . $adunits_id . ") AND fecha_datos BETWEEN '" . $fecha_ayer . " 00:00:00' AND '2013-08-01 23:59:59';");

        if ($query->num_rows() == 1)
            return $query->row();

        return null;
    }

    function get_inventario_by_sites_appnexus($id_sitios) {

        $fecha_ayer = date('Y-m-d 00:00:00', strtotime("-1 day"));

        $query = $this->db->query("SELECT SUM(imps_total) AS totalImpressions, SUM(clicks_total) AS totalClicks
                                    FROM inventario_appnexus
                                    WHERE site_id IN ($id_sitios) 
                                    AND fecha_datos BETWEEN '$fecha_ayer' AND CURDATE();");

        if ($query->num_rows() == 1)
            return $query->row();

        return null;
    }

    function get_inventario_by_sites_por_mes($adunits_id) {
        $fecha_ayer = date('Y-m-d', strtotime("-1 day"));

        $query = $this->db->query("SELECT SUM(totalImpressions) AS totalImpressions FROM inventario_anunciantes WHERE adUnit_ID_2 IN (" . $adunits_id . ") AND totalImpressions > 0 AND fecha_datos BETWEEN '" . $fecha_ayer . " 00:00:00' AND '2013-08-01 23:59:59';");

        if ($query->num_rows() == 1)
            return $query->row();

        return null;
    }

}
