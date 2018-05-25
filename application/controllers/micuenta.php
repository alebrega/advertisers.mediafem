<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Micuenta extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('users');
        $this->load->model('usuariostarjetasdecredito');
        $this->load->model('tarjetasdecredito');
        $this->load->model('paises');
        $this->load->model('anunciantespagos');
        $this->load->model('anunciantestarjetas');
        $this->load->model('anunciantessaldos');
        $this->load->model('transaccionessaldos');
        $this->load->model('cupones_model');
        $this->load->model('cotizaciones_diarias');
        $this->load->model('clientes_model');

        $this->load->library('tank_auth');

        $this->load->helper('url');
    }

    function get_saldo_disponible() {
        echo 'Saldo disponible: ' . $this->api->notacion($this->limite_de_compra) . ' ' . $this->user_data->moneda;
    }

    function index() {
        if (!$this->tank_auth->is_logged_in())
            redirect('/auth/login/');

        $id_usuario = $this->tank_auth->get_user_id();

        $usuario = $this->users->get_user_by_id($id_usuario, 1);

        $tarjeta_usuario = $this->usuariostarjetasdecredito->get_tarjetas_de_credito_por_usuario($id_usuario);
        $pago = $this->anunciantespagos->get_pagos_por_anunciante_json($id_usuario);

        $data['usuario'] = $usuario;
        $data['paises'] = $this->paises->get_paises();
        $data['tarjeta_usuario'] = $tarjeta_usuario;

        $data['respuesta_json'] = null;
        if ($pago)
            $data['respuesta_json'] = $pago->respuesta_json;

        if (isset($_GET['tarjeta']))
            $data['tarjeta_ok'] = ($_GET['tarjeta'] == "ok") ? 1 : 0;
        else
            $data['tarjeta_ok'] = 0;

        $this->load->view('micuenta_view', $data);
    }

    function mis_datos() {

        $id_usuario = $this->tank_auth->get_user_id();

        $usuario = $this->users->get_user_by_id($id_usuario, 1);

        $tarjeta_usuario = $this->usuariostarjetasdecredito->get_tarjetas_de_credito_por_usuario($id_usuario);

        $pago = $this->anunciantespagos->get_pagos_por_anunciante_json($id_usuario);

        $data['usuario'] = $usuario;

        $data['paises'] = $this->paises->get_paises();

        $data['tarjeta_usuario'] = $tarjeta_usuario;

        $data['respuesta_json'] = null;
        if ($pago)
            $data['respuesta_json'] = $pago->respuesta_json;

        if (isset($_GET['tarjeta']))
            $data['tarjeta_ok'] = ($_GET['tarjeta'] == "ok") ? 1 : 0;
        else
            $data['tarjeta_ok'] = 0;

        $this->load->view('mis_datos', $data);
    }

    function actualizar_datos_cuenta() {
        $id_usuario = $this->tank_auth->get_user_id();

        $nombre_beneficiario = $this->input->post('txt_nombre_beneficiario');
        $empresa = $this->input->post('txt_empresa');
        $domicilio = $this->input->post('txt_domicilio');
        $ciudad = $this->input->post('txt_ciudad');
        $codigo_postal = $this->input->post('txt_codigo_postal');
        $telefono = $this->input->post('txt_telefono');
        $pais = $this->input->post('cmb_pais');
        $notacion = $this->input->post('notacion');
        $moneda = $this->input->post('moneda');

        $data_update = array(
            'name' => $nombre_beneficiario,
            'empresa' => $empresa,
            'address' => $domicilio,
            'city' => $ciudad,
            'country' => $pais,
            'zip_code' => $codigo_postal,
            'telefono' => $telefono,
            'notacion' => $notacion,
            'moneda' => $moneda
        );

        if ($this->users->update_user($id_usuario, $data_update)) {
            echo json_encode(array('validate' => TRUE));
        } else {
            echo json_encode(array('validate' => FALSE));
        }
    }

    function mi_facturacion() {
        if (!$this->tank_auth->is_logged_in())
            redirect('/auth/login/');
        
        $data['clientes'] = $this->clientes_model->getAll2($this->tank_auth->get_user_id());
                
        $this->load->view('mi_facturacion',$data);
    }

    function mis_saldos($id_cliente) {
        if (!$this->tank_auth->is_logged_in())
            redirect('/auth/login/');

        $usuario = $this->users->get_user_by_id($this->tank_auth->get_user_id(), 1);
        $data['id_anunciante'] = $this->tank_auth->get_user_id();
        $data['id_cliente'] = $id_cliente;
        $data['cupon_de_promocion'] = $usuario->cupon_de_promocion;
        //$data['monto_minimo_para_promocion'] = $monto . " " . $this->user_data->moneda;
        $data['monto_minimo_para_promocion'] = 0;

        /*
          $usuario = $this->users->get_user_by_id($this->tank_auth->get_user_id(), 1);

          $monto = '50';

          if ($this->user_data->moneda != 'USD') {
          // averiguo la cotizacion del dolar en base a la moneda seleccionada al crear la campana.
          $cotizacion = $this->cotizaciones_diarias->get_cotizacion_today($this->user_data->moneda);
          if ($cotizacion) {
          $cotizacion = (float) $cotizacion->amount;

          if ($this->user_data->moneda == 'ARS') {
          $cambio_fijo = $this->constants->get_constant_by_id(CAMBIO_FIJO_ARGENTINA);
          } else {
          $cambio_fijo = $this->constants->get_constant_by_id(CAMBIO_FIJO_MEXICO);
          }

          $cambio_fijo = (float) $cambio_fijo->value;

          if ($cambio_fijo > $cotizacion)
          $cotizacion = $cambio_fijo;

          $monto = $monto * $cotizacion;
          } else {
          return FALSE;
          }
          }

          $data['id_anunciante'] = $this->tank_auth->get_user_id();
          $data['clientes'] = $this->clientes_model->getAll($this->tank_auth->get_user_id());
          $data['moneda'] = $this->user_data->moneda;
          $data['cotizacion'] = $this->cotizaciones_diarias->get_cotizacion_today($data['moneda']);
          $data['cupon_de_promocion'] = $usuario->cupon_de_promocion;
          $data['monto_minimo_para_promocion'] = $monto . " " . $this->user_data->moneda;
         */

        $this->load->view('mis_saldos', $data);
    }

    function cargar_saldo() {
        if (!$this->tank_auth->is_logged_in())
            redirect('/auth/login/');

        $metodo = $this->input->post('metodo');
        $monto = (float) $this->input->post('monto');

        if ($metodo == 'mercadopago') {
            $this->load->library('mercadopago_gasti');

            $this->mercadopago_gasti->init($this->user_data->country);

            //$this->mercadopago_gasti->sandbox_mode(FALSE);

            if ($this->user_data->country == 'MX') {
                $currency_type = 'MXN';
            } else if ($this->user_data->country == 'AR') {
                $currency_type = 'ARS';
            }

            $preference = array(
                "items" => array(
                    array(
                        "title" => "MediaFem – Carga de saldo a su cuenta",
                        "quantity" => 1,
                        "currency_id" => $currency_type,
                        "unit_price" => $monto
                    )
                )
            );

            $preferenceResult = $this->mercadopago_gasti->create_preference($preference);

            echo json_encode(array('validate' => TRUE, 'init_point' => $preferenceResult["response"]["init_point"]));
            die();
        } else {
            echo json_encode(array('validate' => FALSE, 'error' => 'Ha ocurrido un error inesperado.'));
            die();
        }
    }

    function cargar_cupon() {

        $codigo = $this->input->post('codigo');
        //$codigo = '0WPTyWK';
        // consulto si el cupon esta utilizado o si no esta vencido
        $cupon = $this->cupones_model->get_cupones_by_codigo($codigo);

        if ($cupon->consumido == 0) {
            if ($this->_dateDiff(date('Y-m-d H:i:s'), $cupon->fecha_vencimiento) >= 0) {
                // si no esta utilizado lo consumo
                $update = array(
                    'consumido' => 1,
                    'anunciante_consumio' => $this->user_data->id,
                    'fecha_consumio' => date('Y-m-d H:i:s')
                );

                if ($this->cupones_model->update($cupon->id, $update)) {
                    $monto = $cupon->valor;

                    // convierto a la moneda del usuario
                    if ($this->user_data->moneda != 'USD') {
                        // averiguo la cotizacion del dolar en base a la moneda seleccionada al crear la campana.
                        $cotizacion = $this->cotizaciones_diarias->get_cotizacion_today($this->user_data->moneda);
                        if ($cotizacion) {
                            $cotizacion = (float) $cotizacion->amount;

                            if ($this->user_data->moneda == 'ARS') {
                                $cambio_fijo = $this->constants->get_constant_by_id(CAMBIO_FIJO_ARGENTINA);
                            } else {
                                $cambio_fijo = $this->constants->get_constant_by_id(CAMBIO_FIJO_MEXICO);
                            }

                            $cambio_fijo = (float) $cambio_fijo->value;

                            if ($cambio_fijo > $cotizacion)
                                $cotizacion = $cambio_fijo;

                            $monto = $monto * $cotizacion;
                        } else {
                            return FALSE;
                        }
                    }

                    // cargo el valor del cupon como saldo
                    $data_insert2 = array(
                        'id_anunciante' => $this->tank_auth->get_user_id(),
                        'credito' => $monto,
                        'balance' => $this->user_data->limite_de_compra + $monto,
                        'moneda' => $this->user_data->moneda,
                        'descripcion' => 'Carga de cupón de descuento (Cod. ' . $cupon->codigo . ').',
                        'tipo_de_pago' => 1
                    );

                    $this->anunciantessaldos->insert_anunciante_saldo($data_insert2);

                    // obtengo el saldo prepago actual y le sumo lo recien cargado
                    // al mismo tiempo actualizo el nuevo limite de compra

                    $data_update = array(
                        'limite_de_compra' => $this->user_data->limite_de_compra + $monto,
                        'saldo_prepago' => $this->user_data->saldo_prepago + $monto,
                    );

                    $this->anunciantes->update_anunciante($this->tank_auth->get_user_id(), $data_update);

                    echo json_encode(array('validate' => TRUE));
                    die();
                } else {
                    echo json_encode(array('validate' => FALSE, 'error' => 'No se pudo cargar el cup&oacute;n.'));
                    die();
                }
            } else {
                echo json_encode(array('validate' => FALSE, 'error' => 'El cup&oacute;n se encuentra vencido.'));
                die();
            }
        } else {
            echo json_encode(array('validate' => FALSE, 'error' => 'El cup&oacute;n ya se encuentra consumido'));
            die();
        }
    }

    function guardar_transaccion_mercadopago($collection_id, $collection_status, $monto, $id_cliente) {
        if ($this->user_data->country == 'MX') {
            $currency_type = 'MXN';
        } else if ($this->user_data->country == 'AR') {
            $currency_type = 'ARS';
        }
        // ingreso la transaccion realizada por el usuario en "anunciantes_trasacciones_saldos"
        $data_insert = array(
            'id_anunciante' => $this->tank_auth->get_user_id(),
            'sistema_pago' => 'MERCADOPAGO',
            'moneda' => $currency_type,
            'monto' => $monto,
            'MP_collection_id' => $collection_id,
            'MP_collection_status' => $collection_status
        );

        $this->transaccionessaldos->insert($data_insert);

        // si el pago es "APROBADO" entonces cargo el saldo correspondiente al usuario en "anunciantes_saldos"
        if ($collection_status == 'approved') {
            // cargo el credito del saldo
            $data_insert2 = array(
                'id_anunciante' => $this->tank_auth->get_user_id(),
                'credito' => $monto,
                'balance' => $this->user_data->limite_de_compra + $monto,
                'moneda' => 'ARS',
                'descripcion' => 'Carga de saldo en MediaFem (MercadoPago)',
                'tipo_de_pago' => 1
            );

            $this->anunciantessaldos->insert_anunciante_saldo($data_insert2);
            
            
            $data_cliente = array(
                'id_cliente' => $id_cliente,
                'descripcion' => 'Carga de saldo mediante PayPal',
                'importe' => $monto,
                'tipo' => '1',
                'tipo_saldo' => '0'
            );
            
            $this->clientes_model->insert_cliente_saldo($data_cliente);
            
            
            // obtengo el saldo prepago actual y le sumo lo recien cargado
            // al mismo tiempo actualizo el nuevo limite de compra

            $data_update = array(
                'limite_de_compra' => $this->user_data->limite_de_compra + $monto,
                'saldo_prepago' => $this->user_data->saldo_prepago + $monto,
            );

            $this->anunciantes->update_anunciante($this->tank_auth->get_user_id(), $data_update);

            //CARGAR CUPON SOLO SI CARGO COMO MINIMO 50 USD

            $cotizacion = $this->cotizaciones_diarias->get_cotizacion_today($this->user_data->moneda);
            if ($cotizacion) {
                $cotizacion = (float) $cotizacion->amount;

                if ($this->user_data->moneda == 'ARS') {
                    $cambio_fijo = $this->constants->get_constant_by_id(CAMBIO_FIJO_ARGENTINA);
                } else {
                    $cambio_fijo = $this->constants->get_constant_by_id(CAMBIO_FIJO_MEXICO);
                }

                $cambio_fijo = (float) $cambio_fijo->value;

                if ($cambio_fijo > $cotizacion)
                    $cotizacion = $cambio_fijo;

                $monto_usd = $monto / $cotizacion;
            } else {
                return FALSE;
            }

            if ($monto_usd < 50)
                return false;

            $anunciante = $this->users->get_user_by_id($this->tank_auth->get_user_id(), 1);

            if ($anunciante->cupon_de_promocion == '1') {

                /* genero el cupon */
                $caracteres = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890"; //posibles caracteres a usar
                $numerodeletras = 7; //numero de letras para generar el texto
                $cupon = ""; //variable para almacenar la cadena generada
                for ($i = 0; $i < $numerodeletras; $i++) {
                    $cupon .= substr($caracteres, rand(0, strlen($caracteres)), 1); /* Extraemos 1 caracter de los caracteres
                      entre el rango 0 a Numero de letras que tiene la cadena */
                }
                /* chequeo si el cupon ya existe */
                if (!$this->cupones_model->get_cupones_by_codigo($cupon)) {
                    // inserto
                    $insert = array(
                        'codigo' => $cupon,
                        'valor' => 50,
                        'fecha_vencimiento' => date('Y-m-d', strtotime('+1 month'))
                    );
                    $this->cupones_model->insert($insert);
                }
                $cupon = $this->cupones_model->get_cupones_by_codigo($cupon);

                if ($cupon->consumido == 0) {
                    // si no esta utilizado lo consumo
                    $update = array(
                        'consumido' => 1,
                        'anunciante_consumio' => $user_id,
                        'fecha_consumio' => date('Y-m-d H:i:s')
                    );
                    if ($this->cupones_model->update($cupon->id, $update)) {

                        $monto = $cupon->valor;
                        // convierto a la moneda del usuario
                        if ($anunciante->moneda != 'USD') {
                            // averiguo la cotizacion del dolar en base a la moneda seleccionada al crear la campana.
                            $cotizacion = $this->cotizaciones_diarias->get_cotizacion_today($anunciante->moneda);

                            if ($cotizacion) {
                                $cotizacion = (float) $cotizacion->amount;

                                if ($anunciante->moneda == 'ARS') {
                                    $cambio_fijo = $this->constants->get_constant_by_id(CAMBIO_FIJO_ARGENTINA);
                                } else {
                                    $cambio_fijo = $this->constants->get_constant_by_id(CAMBIO_FIJO_MEXICO);
                                }
                                $cambio_fijo = (float) $cambio_fijo->value;

                                if ($cambio_fijo > $cotizacion)
                                    $cotizacion = $cambio_fijo;

                                $monto = $monto * $cotizacion;
                            } else {
                                return FALSE;
                            }
                        }
                        // cargo el valor del cupon como saldo
                        $data_insert2 = array(
                            'id_anunciante' => $anunciante->id,
                            'credito' => $monto,
                            'balance' => $anunciante->limite_de_compra + $monto,
                            'moneda' => $anunciante->moneda,
                            'descripcion' => 'Carga de cup&oacute;n de promoci&oacute;n (Cod. ' . $cupon->codigo . ').',
                            'tipo_de_pago' => 1
                        );

                        $this->anunciantessaldos->insert_anunciante_saldo($data_insert2);

                        // obtengo el saldo prepago actual y le sumo lo recien cargado
                        // al mismo tiempo actualizo el nuevo limite de compra

                        $data_update = array(
                            'cupon_de_promocion' => '0',
                            'limite_de_compra' => $anunciante->limite_de_compra + $monto,
                            'saldo_prepago' => $anunciante->saldo_prepago + $monto,
                        );

                        $this->anunciantes->update_anunciante($anunciante->id, $data_update);
                    }
                }
            }
        }
    }

    function success_paypal($id_anunciante, $id_cliente) {
        //$id_anunciante = $this->tank_auth->get_user_id();

        $monto = explode(' ', $_POST['option_selection1']);
        $monto = (float) $monto[1];

        //$monto = 75.00;
        //$_POST['payment_status'] = 'Completed';
        //$_POST['txn_id'] = '123';
        // ingreso la transaccion realizada por el usuario en "anunciantes_trasacciones_saldos"
        $data_insert = array(
            'id_anunciante' => $id_anunciante,
            'sistema_pago' => 'PAYPAL',
            'moneda' => 'USD',
            'monto' => $monto,
            'PP_txn_id' => $_POST['txn_id'],
            'PP_payment_status' => $_POST['payment_status']
        );
        $this->transaccionessaldos->insert($data_insert);
        // si el pago es "APROBADO" entonces cargo el saldo correspondiente al usuario en "anunciantes_saldos"
        if ($_POST['payment_status'] == 'Completed') {
            // traigo el ultimo balance del usuario
            $ultimo_movimiento = $this->anunciantessaldos->get_ultimo_saldo_por_anunciante($id_anunciante);
            // cargo el credito del saldo
            $data_insert2 = array(
                'id_anunciante' => $id_anunciante,
                'credito' => $monto,
                'balance' => $ultimo_movimiento->balance + $monto,
                'moneda' => 'USD',
                'descripcion' => 'Carga de saldo en MediaFem (PayPal)',
                'tipo_de_pago' => 1
            );

            $this->anunciantessaldos->insert_anunciante_saldo($data_insert2);
            // obtengo el saldo prepago actual y le sumo lo recien cargado
            // al mismo tiempo actualizo el nuevo limite de compra

            $data_update = array(
                'limite_de_compra' => $ultimo_movimiento->balance + $monto,
                'saldo_prepago' => $this->user_data->saldo_prepago + $monto,
            );

            $this->anunciantes->update_anunciante($id_anunciante, $data_update);
            
            $data_cliente = array(
                'id_cliente' => $id_cliente,
                'descripcion' => 'Carga de saldo mediante PayPal',
                'importe' => $monto,
                'tipo' => 1,
                'tipo_saldo' => 0
            );
            
            $this->clientes_model->insert_cliente_saldo($data_cliente);
            
            
            //CARGAR CUPON SOLO SI CARGO COMO MINIMO 50 USD

            if ($monto < 50)
                return false;

            $anunciante = $this->users->get_user_by_id($this->tank_auth->get_user_id(), 1);
            if ($anunciante->cupon_de_promocion == '1') {
                /* genero el cupon */
                $caracteres = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890"; //posibles caracteres a usar
                $numerodeletras = 7; //numero de letras para generar el texto
                $cupon = ""; //variable para almacenar la cadena generada
                for ($i = 0; $i < $numerodeletras; $i++) {
                    $cupon .= substr($caracteres, rand(0, strlen($caracteres)), 1); /* Extraemos 1 caracter de los caracteres
                      entre el rango 0 a Numero de letras que tiene la cadena */
                }
                /* chequeo si el cupon ya existe */
                if (!$this->cupones_model->get_cupones_by_codigo($cupon)) {
                    // inserto
                    $insert = array(
                        'codigo' => $cupon,
                        'valor' => 50,
                        'fecha_vencimiento' => date('Y-m-d', strtotime('+1 month'))
                    );
                    $this->cupones_model->insert($insert);
                }
                $cupon = $this->cupones_model->get_cupones_by_codigo($cupon);
                if ($cupon->consumido == 0) {
                    // si no esta utilizado lo consumo
                    $update = array(
                        'consumido' => 1,
                        'anunciante_consumio' => $user_id,
                        'fecha_consumio' => date('Y-m-d H:i:s')
                    );
                    if ($this->cupones_model->update($cupon->id, $update)) {
                        $monto = $cupon->valor;
                        // convierto a la moneda del usuario
                        if ($anunciante->moneda != 'USD') {
                            // averiguo la cotizacion del dolar en base a la moneda seleccionada al crear la campana.
                            $cotizacion = $this->cotizaciones_diarias->get_cotizacion_today($anunciante->moneda);

                            if ($cotizacion) {
                                $cotizacion = (float) $cotizacion->amount;

                                if ($anunciante->moneda == 'ARS') {
                                    $cambio_fijo = $this->constants->get_constant_by_id(CAMBIO_FIJO_ARGENTINA);
                                } else {
                                    $cambio_fijo = $this->constants->get_constant_by_id(CAMBIO_FIJO_MEXICO);
                                }
                                $cambio_fijo = (float) $cambio_fijo->value;

                                if ($cambio_fijo > $cotizacion)
                                    $cotizacion = $cambio_fijo;

                                $monto = $monto * $cotizacion;
                            } else {
                                return FALSE;
                            }
                        }
                        // cargo el valor del cupon como saldo
                        $data_insert2 = array(
                            'id_anunciante' => $anunciante->id,
                            'credito' => $monto,
                            'balance' => $anunciante->limite_de_compra + $monto,
                            'moneda' => $anunciante->moneda,
                            'descripcion' => 'Carga de cup&oacute;n de promoci&oacute;n (Cod. ' . $cupon->codigo . ').',
                            'tipo_de_pago' => 1
                        );

                        $this->anunciantessaldos->insert_anunciante_saldo($data_insert2);
                        
            
                        // obtengo el saldo prepago actual y le sumo lo recien cargado
                        // al mismo tiempo actualizo el nuevo limite de compra

                        $data_update = array(
                            'cupon_de_promocion' => '0',
                            'limite_de_compra' => $anunciante->limite_de_compra + $monto,
                            'saldo_prepago' => $anunciante->saldo_prepago + $monto,
                        );
                        $this->anunciantes->update_anunciante($anunciante->id, $data_update);
                    }
                }
            }
        }
    }

    function obtener_historial_pagos_y_limite_de_compra($id_cliente, $seconds) {
        //$data['saldos'] = $this->anunciantessaldos->get_saldos_por_anunciante($this->tank_auth->get_user_id());
        $data['saldos'] = $this->clientes_model->getSaldosByCliente($id_cliente);
        $data['cliente'] = $this->clientes_model->getByID($id_cliente);

        $this->load->view('tbl_historial_pagos_y_limite_de_compra', $data);
    }

    function _dateDiff($start, $end) {
        $start_ts = strtotime($start);
        $end_ts = strtotime($end);
        $diff = $end_ts - $start_ts;
        return round($diff / 86400);
    }

}