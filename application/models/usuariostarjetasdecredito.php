<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Usuariostarjetasdecredito extends CI_Model {

    private $table_name = 'anunciantes_tarjetas_de_credito';
    private $table_tarjetas_de_credito = 'tarjetas_de_credito';

    function __construct() {
        parent::__construct();
    }

    function insert_tarjeta_de_credito($data) {
        return $this->db->insert($this->table_name, $data);
    }
	
	function delete_tarjetas_de_credito_por_usuario($user_id){
        $data['eliminada'] = 1;
        $this->db->where('id_anunciante', $user_id);
        $this->db->update($this->table_name, $data);
        $this->db->affected_rows() > 0;

        unset($data);

        $data['tarjeta_certificada'] = 0;
        $this->db->where('id', $user_id);
        $this->db->update('anunciantes', $data);
        return $this->db->affected_rows() > 0;
    }

    function get_tarjetas_de_credito_por_usuario($id_usuario) {

        $this->db->select($this->table_name . '.nro_tarjeta');
        $this->db->select($this->table_name . '.id_tipo_tarjeta');
        $this->db->select($this->table_name . '.mes_expiracion');
        $this->db->select($this->table_name . '.anio_expiracion');
        $this->db->select($this->table_name . '.ccv');
        $this->db->select($this->table_tarjetas_de_credito . '.descripcion');
        
        $this->db->where('id_anunciante', $id_usuario);
		
		$this->db->where('eliminada !=', 1);

        $this->db->join($this->table_tarjetas_de_credito, $this->table_tarjetas_de_credito . ".id = " . $this->table_name . ".id_tipo_tarjeta");

        $query = $this->db->get($this->table_name);

        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    function update_usuario_tarjeta_de_credito($user_id, $nro_tarjeta, $data) {
        $this->db->where('id_anunciante', $user_id);

        $this->db->update($this->table_name, $data);
        return $this->db->affected_rows() > 0;
    }

    function insert_anunciante_tarjeta($data) {
        return $this->db->insert($this->table_name, $data);
    }

}