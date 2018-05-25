<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Payments extends CI_Model {

    private $table_name = 'payments';
    private $table_payment_type = 'payment_type';
    private $table_users = 'users';

    function __construct() {
        parent::__construct();
    }

    function get_all_payments_by_id($user_id, $num, $offset) {
        $this->db->where('user_id', $user_id);
        $this->db->order_by('fecha', 'desc');

        $query = $this->db->get($this->table_name, $num, $offset);

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_last_payments($balance_minimo) {

        if ($balance_minimo > 0) {
            $query = $this->db->query('SELECT t.user_id, t.concept, t.fecha, t.balance, t.pago, u.publisher_name, u.site_default, a.nombre_completo, t.profit
                                    FROM payments t,users u, admins a
                                    WHERE fecha=(SELECT max(t2.fecha)
                                                FROM payments t2
                                                WHERE t2.user_id=t.user_id AND (t2.pago="0" or t2.pago="1"))
                                    AND (t.pago="0" or t.pago="1")
                                    AND t.user_id=u.id
                                    AND a.id = u.id_ejecutivo_medios
                                    AND t.balance>' . $balance_minimo . ';');
        } else {
            $query = $this->db->query('SELECT t.user_id, t.concept, t.fecha, t.balance, t.pago, u.publisher_name, u.site_default, a.nombre_completo, t.profit
                                    FROM payments t,users u, admins a
                                    WHERE fecha=(SELECT max(t2.fecha)
                                                FROM payments t2
                                                WHERE t2.user_id=t.user_id AND (t2.pago="0" or t2.pago="1"))
                                    AND (t.pago="0" or t.pago="1")
                                    AND t.user_id=u.id
                                    and a.id = u.id_ejecutivo_medios;');
        }
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_pagos_mensuales() {
        $query = $this->db->query("select p.fecha, u.id, u.publisher_name, p.credit, p.profit, p.ingresos_total, a.nombre_completo
                                    from payments p, users u, admins a
                                    where p.pago = '0'
                                    and p.user_id = u.id
                                    and u.id_ejecutivo_medios = a.id
                                    and p.credit > 0
                                    order by p.id desc;");
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_payment_history() {

        $this->db->where('pago', '1');
        $this->db->order_by('fecha', 'desc');

        $this->db->join($this->table_users, $this->table_users . ".id = " . $this->table_name . ".user_id");

        $query = $this->db->get($this->table_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_count_payments_by_id($user_id) {
        $this->db->where('user_id', $user_id);
        $this->db->order_by('fecha', 'desc');

        $query = $this->db->get($this->table_name);
        return $query->num_rows();
    }

    function register_payment($user_id, $descripcion, $monto, $payment_type, $balance) {
        $this->db->set('user_id', $user_id);
        $this->db->set('concept', $descripcion);
        $this->db->set('debit', $monto);
        $this->db->set('balance', $balance);
        $this->db->set('pago', '1');
        $this->db->set('payment_type_id', $payment_type);
        $this->db->set('fecha', 'now()', false);

        return $this->db->insert($this->table_name);
    }

    function registrar_pago($data) {
        return $this->db->insert($this->table_name, $data);
    }

    function get_last_payments_by_id($user_id) {

        $this->db->where('user_id', $user_id);
        $this->db->where('pago', '1');
        $this->db->order_by('fecha', 'desc');
        $this->db->join("payment_type", "payment_type.id = payments.payment_type_id ");

        $query = $this->db->get($this->table_name, 1, 0);

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_last_payment_by_id($user_id) {

        $this->db->where('user_id', $user_id);
        $this->db->order_by('fecha', 'desc');
        $this->db->where("(pago = '0' OR pago = '1')");

        $query = $this->db->get($this->table_name, 1, 0);

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_payment_type_by_id($id) {
        $this->db->where('id', $id);

        $query = $this->db->get($this->table_payment_type);

        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    function update_payment($id, $data) {
        $this->db->where('id', $id);
        $this->db->update($this->table_name, $data);
        return $this->db->affected_rows() > 0;
    }

    function get_ingresos_by_id($user_id) {
        $this->db->where('user_id', $user_id);
        $this->db->where("(pago = '0' OR pago = '1')");

        $this->db->order_by('fecha', 'asc');

        $query = $this->db->get($this->table_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_pagos_en_proceso_by_id($user_id) {
        $this->db->where('user_id', $user_id);
        $this->db->where('pago', '2');

        $this->db->order_by('fecha', 'asc');

        $query = $this->db->get($this->table_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_pagos_en_proceso($balance_minimo, $user_id, $mes ,$mes_ingles) {

        $sql = "SELECT * FROM " . $this->table_name . " where pago= '2' and debit > " . $balance_minimo . '';
        if ($user_id != 'todos') {
            $sql.= " AND  user_id ='" . $user_id . "'";
        }
        if ($mes != '0') {
           $sql.= " AND concept  LIKE ('%".$mes."%') OR concept LIKE '%".$mes_ingles."%'";
        }
        $sql.=";";

        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_publishers_pagos_en_proceso() {
        $sql = "SELECT p.user_id, u.publisher_name FROM payments p, users u where p.pago= '2'
AND p.user_id = u.id
GROUP BY p.user_id ORDER BY u.publisher_name";


        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

}