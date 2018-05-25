<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Clientes_model extends CI_Model {

    private $table = 'clientes';
    private $table_saldos = 'clientes_saldos';
    private $table_clientes_anunciantes = 'clientes_anunciantes';

    function __construct() {
        parent::__construct();
    }

    function getAll($id_anunciante) {
        $query = $this->db->query("select cli.razon_social, cli.id, cli.moneda, cli.limite_credito, SUM(cs.importe) as saldo_disponible from clientes as cli, clientes_anunciantes as cli_anu, clientes_saldos as cs where cli_anu.anunciante_id='$id_anunciante' and cli_anu.cliente_id=cli.id and cli.estado='1' and cli.validado_administracion='1' and cs.id_cliente=cli.id group by cli.id order by cli.razon_social asc;");

        if ($query->num_rows() > 0)
            return $query->result();

        return null;
    }
    
    function getAll2($id_anunciante) {
        $query = $this->db->query("select cli.razon_social, cli.id, cli.limite_credito from clientes as cli, clientes_anunciantes as cli_anu where cli_anu.anunciante_id='$id_anunciante' and cli_anu.cliente_id=cli.id and cli.estado='1' and cli.validado_administracion='1' order by cli.razon_social asc;");

        if ($query->num_rows() > 0)
            return $query->result();

        return null;
    }

    function getSaldosByCliente($id) {
        $this->db->where('id_cliente', $id);

        $query = $this->db->get($this->table_saldos);

        if ($query->num_rows() > 0)
            return $query->result();

        return NULL;
    }

    function getSaldoDisponible($id_cliente) {
        $query = $this->db->query("select SUM(importe) as saldo_disponible from clientes_saldos where id_cliente=$id_cliente;");

        if ($query->num_rows() == 1)
            return $query->row();

        return 0;
    }
        
    function getClientesActivos($id_anunciante) {
        $query = $this->db->query("select cli.id, cli.estado from clientes as cli, clientes_anunciantes as cli_anu where cli_anu.anunciante_id='$id_anunciante' and cli_anu.cliente_id=cli.id and cli.estado='1' and cli.validado_administracion = '1';");

        if ($query->num_rows() > 0)
            return $query->result();

        return 0;
    }

    function getByID($id) {
        $this->db->where('id', $id);

        $query = $this->db->get($this->table);

        if ($query->num_rows() == 1)
            return $query->row();

        return NULL;
    }

    function insert_cliente($data) {
        return $this->db->insert($this->table, $data);
    }

    function insert_cliente_saldo($data) {
        return $this->db->insert($this->table_saldos, $data);
    }

    function asignar_anunciante($data) {
        return $this->db->insert($this->table_clientes_anunciantes, $data);
    }

    function save_cliente($id, $data) {
        $this->db->where('id', $id);
        $this->db->update($this->table, $data);
        return $this->db->affected_rows() > 0;
    }

}