<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Campania extends CI_Controller {

    function __construct() {
        parent::__construct();

        if (!$this->tank_auth->is_logged_in())
            redirect('/auth/login/');

        /*
          if ((!$this->tarjeta_certificada && $this->creado_desde_sitio == 1) && $this->limite_de_compra == 0.00)
          redirect('/micuenta');
         */

        $this->load->model('admins');
        $this->load->model('anunciantes');
        $this->load->model('anunciantespagos');
        $this->load->model('anunciantessaldos');
        $this->load->model('anunciantestarjetas');
        $this->load->model('audiencias');
        $this->load->model('tarjetasdecredito');
        $this->load->model('usuariostarjetasdecredito');
        $this->load->model('campanias');
        $this->load->model('constants');
        $this->load->model('clientes_model');
        $this->load->model('archivoscreatividades');
        $this->load->model('campaniascontrol');
        $this->load->model('campaniascanalestematicos');
        $this->load->model('campaniasformatos');
        $this->load->model('campaniaspaises');
        $this->load->model('campaniassitios');
        $this->load->model('campaniassitiosformatos');
        $this->load->model('categorias');
        $this->load->model('cotizaciones_diarias');
        $this->load->model('formatosdfp');
        $this->load->model('paises');
        $this->load->model('segmentacion');
        $this->load->model('sites');
        $this->load->model('sitescategories');
        $this->load->model('tarifarios');
        $this->load->model('payments');
        $this->load->model('publishers');
        $this->load->model('publinotas');
        $this->load->model('publinotassitios');
        $this->load->model('users');
        $this->load->model('valoressugeridos');
        $this->load->model('inventario_anunciantes');


        $this->load->model('campaniashistorial');

        $this->load->library('My_PHPMailer');
        $this->load->library('session');

        $this->error_al_cargar_creatividad = '';
    }

    function test($campania) {
        new_var_dump($this->api->crear_campania_AppNexus($campania, 'mf'));
    }

    function ticket($campania) {
        $this->crear_ticket_zendesk($campania);
    }

    function test_dfp($id_campania) {

        new_var_dump($this->api->crear_campania_dfp($id_campania));
    }

    function test_pais($country) {

        new_var_dump($this->api->get_pais_appnexus($country));

        die();
        $paises = $this->api->get_paises(0)->countries;
        foreach ($paises as $pais) {
            echo $pais->code . " - " . $pais->id . "<br/>";
            $data = array('id_appnexus' => $pais->id);
            $this->paises->update_pais($pais->code, $data);
        }

        $paises = $this->api->get_paises(100)->countries;

        foreach ($paises as $pais) {
            echo $pais->code . " - " . $pais->id . "<br/>";
            $data = array('id_appnexus' => $pais->id);
            $this->paises->update_pais($pais->code, $data);
        }
        $paises = $this->api->get_paises(200)->countries;

        foreach ($paises as $pais) {
            echo $pais->code . " - " . $pais->id . "<br/>";
            $data = array('id_appnexus' => $pais->id);
            $this->paises->update_pais($pais->code, $data);
        }
    }

    function index() {

        $data['tiene_campanias'] = $this->tiene_campanias;

        $clientes_activos = $this->clientes_model->getClientesActivos($this->tank_auth->get_user_id());

        $data['mostrar_poner_tarjera'] = TRUE;
        $data['mostrar_crear_campania'] = FALSE;

        if ($clientes_activos) {
            $data['clientes_activos'] = TRUE;
            foreach ($clientes_activos as $row) {
                $saldo = $this->clientes_model->getSaldoDisponible($row->id);
                if ($saldo->saldo_disponible) {
                    $data['mostrar_crear_campania'] = TRUE;
                    $data['mostrar_poner_tarjera'] = FALSE;
                    break;
                }
            }
        } else {
            $data['clientes_activos'] = FALSE;
        }

        $this->load->view('campanias_view', $data);
    }

    function exportar_orden_PDF($id_campania) {
// retomo los datos de la campa&ntilde;a.
//$id_campania = $this->input->post('id_campania');

        $campania = $this->campanias->get_campania_by_id($id_campania);

        $creatividades = $this->campanias->get_creatividades_campanias($id_campania);

        $usuario = $this->users->get_user_by_id($this->tank_auth->get_user_id(), 1);

        $data['habilitar_descuentos'] = $usuario->habilitar_descuentos;

        if ($campania) {
            $data['creatividades'] = $creatividades;
            $data['id_campania'] = $id_campania;
            $data['nombre_campania'] = $campania->nombre;
            $data['anunciante_id'] = $campania->id_anunciante;
            $data['anunciante_nombre'] = '';
            $data['segmentacion_id'] = $campania->segmentacion_id;
            $data['segmentacion'] = '';
            $data['inversion_bruta'] = $campania->inversion_bruta;
            $data['descuento'] = $campania->descuento;
            $data['comision'] = $campania->comision;
            $data['inversion_neto'] = $campania->inversion_neta;
            $data['modalidad_compra'] = $campania->modalidad_compra;
            $data['cantidad_compra'] = $campania->cantidad;
            $data['paises'] = '';
            $data['fecha_inicio'] = MySQLDateToDateDatepicker($campania->fecha_inicio);
            $data['fecha_fin'] = MySQLDateToDateDatepicker($campania->fecha_fin);
            $data['fecha_alta'] = MySQLDateToDateDatepicker($campania->fecha_alta);
            $data['forma_completarse'] = $campania->forma_completarse;
            $data['facturacion'] = $campania->distribucion;
            $data['ejecutivo_cuentas_id'] = $campania->usuario_cuentas;
            $data['ejecutivo_cuentas'] = '';
            $data['ejecutivo_implementa_id'] = $campania->usuario_implementa;
            $data['ejecutivo_implementa'] = '';
            $data['ejecutivo_revisa_id'] = $campania->usuario_revisa;
            $data['ejecutivo_revisa'] = '';
            $data['ejecutivo_director_id'] = $campania->usuario_director;
            $data['ejecutivo_director'] = '';
            $data['ticket'] = $campania->ticket_mantis;
            $data['activada'] = $campania->activada;
            $data['mostrar_exportar'] = FALSE;

            $data['id_lineItem_appnexus'] = $campania->id_lineItem_appnexus;

            $data['estado'] = $campania->estado;
            $data['creada_desde_anunciantes'] = $campania->creada_desde_anunciantes;


            // retomo el nombre del cliente
            $cliente = $this->clientes_model->getByID($campania->id_cliente);
            if ($cliente) {
                $data['cliente_nombre'] = $cliente->razon_social;
            } else {
                $data['cliente_nombre'] = 'Cliente inexistente en la base de datos.';
            }

// retomo el nombre del anunciante
            $anunciante = $this->anunciantes->get_anunciante_adserver_by_id($data['anunciante_id']);
            if ($anunciante) {
                $data['anunciante_nombre'] = $anunciante->nombre;
            } else {
                $data['anunciante_nombre'] = 'Anunciante inexistente en la base de datos.';
            }

// retomo el nombre de la segmentacion
            $segmentacion = $this->segmentacion->get_segmentacion_by_id($data['segmentacion_id']);
            if ($segmentacion) {
                $data['segmentacion'] = $segmentacion->descripcion;
            } else {
                $data['segmentacion'] = 'Nombre de segmentaci&oacute;n inexistente en la base de datos.';
            }

// retomo los paises pertenecientes a las campa&ntilde;as.
            $campania_paises = $this->campaniaspaises->get_paises_by_campania($data['id_campania']);

            if ($campania_paises) {
                $texto_paises = '';
                $a = 0;
                foreach ($campania_paises as $row) {
                    $a++;

                    $pais = $this->paises->get_pais_by_id($row->id_pais);
                    if ($a == sizeof($campania_paises) - 1) {
                        $texto_paises = $texto_paises . $pais->descripcion . " y ";
                    } else {
                        $texto_paises = $texto_paises . $pais->descripcion . ", ";
                    }
                }

                $data['paises'] = substr($texto_paises, 0, - 2);
            }

            if ($data['paises'] == "Colombia, Colombia, Colombia, Colombia, Colombia, Colombia, Colombia, Colombia, Andorra, Albania, Armenia, Argentina, Austria, Australia, Bosnia and Herzegovina, Belgium, Bulgaria, Brazil, Belarus, Canada, Switzerland, Cote d'Ivoire, Chile, China, Colombia, Cyprus, Czech Republic, Germany, Denmark, Estonia, Espa&ntilde;a, Finland, France, United Kingdom, Georgia, Greece, Hong Kong, Croatia, Hungary, Ireland, Israel, India, Iceland, Italy, Japan, Republic of Korea, Lithuania, Luxembourg, Latvia, Macedonia, Montserrat, Mexico, Malaysia, Netherlands, Norway, New Zealand, Peru, Philippines, Portugal, Romania, Russian Federation, Sweden, Singapore, Slovenia, Slovakia, San Marino, Turkey, Taiwan, Ukraine, United States, Uruguay, Paraguay, Costa Rica, El Salvador, Republica Dominicana, Guatemala, Venezuela, Panama, Puerto Rico, Bolivia, Pakistan, Ecuador, Bangladesh, Nigeria, Kenya, Morocco, Uganda, Egipto, Honduras, Indonesia, Jamaica, Kuwait, Nicaragua, United Arab Emirates, Antigua and Barbuda, Netherlands Antilles, American Samoa, Azerbaijan, Barbados, Bahrain, Bermuda, Botswana, Belize, Cameroon, Dominica, Algeria, Federated States of Micronesia, Grenada, French Guiana, Ghana, Guinea, Guam, Iraq, Jordan, Saint Kitts and Nevis, Cayman Islands, Lebanon, Saint Lucia, Sri Lanka, Liberia, Libyan Arab Jamahiriya, Monaco, Moldova, Republic of, Northern Mariana Islands, Malta, Maldives, Nepal, Oman, Poland, Palestinian Territory, Qatar, Reunion, Saudi Arabia, Sierra Leone, Suriname, Turks and Caicos Islands, Thailand, Tunisia, Trinidad and Tobago, Tanzania, United Republic of, United States Minor Outlying Islands, Virgin Islands, British, Virgin Islands, U.S., Vietnam, Yemen, South Africa, Zambia, Zimbabwe, Afghanistan, Haiti, Aruba, Bahamas, Brunei Darussalam, Guadeloupe, Gibraltar, Mozambique, Senegal, Namibia, Equatorial Guinea, Angola, Cambodia, Gabon, Papua New Guinea, Kazakhstan, Madagascar, Timor-Leste, Congo, The Democratic Republic of the, Malvinas, Malawi, Martinique, Fiji, Mauritius, Mongolia, Tonga, Macao, Cape Verde, Niger, Ethiopia, Turkmenistan, Lesotho, Sao Tome and Principe, Vanuatu, Central African Republic, Cook Islands, Desconocido, Congo, Solomon Islands, Desconocido, Desconocido, Greenland, Swaziland, Palau, Saint Pierre and Miquelon, Faroe Islands, Desconocido, Somalia, Holy See (Vatican City State), Burundi, Anguilla, Rwanda, Mali, Burkina Faso, Kiribati, Togo, Uzbekistan, Mayotte, Liechtenstein, Gambia, Djibouti, French Polynesia, Benin, Saint Vincent and the Grenadines, Guyana, Mauritania, Lao People's Democratic Republic, Kyrgyzstan, New Caledonia, Andorra, Albania, Armenia, Argentina, Austria, Australia, Bosnia and Herzegovina, Belgium, Bulgaria, Brazil, Belarus, Canada, Switzerland, Cote d'Ivoire, Chile, China, Colombia, Cyprus, Czech Republic, Germany, Denmark, Estonia, Espa&ntilde;a, Finland, France, United Kingdom, Georgia, Greece, Hong Kong, Croatia, Hungary, Ireland, Israel, India, Iceland, Italy, Japan, Republic of Korea, Lithuania, Luxembourg, Latvia, Macedonia, Montserrat, Mexico, Malaysia, Netherlands, Norway, New Zealand, Peru, Philippines, Portugal, Romania, Russian Federation, Sweden, Singapore, Slovenia, Slovakia, San Marino, Turkey, Taiwan, Ukraine, United States, Uruguay, Paraguay, Costa Rica, El Salvador, Republica Dominicana, Guatemala, Venezuela, Panama, Puerto Rico, Bolivia, Pakistan, Ecuador, Bangladesh, Nigeria, Kenya, Morocco, Uganda, Egipto, Honduras, Indonesia, Jamaica, Kuwait, Nicaragua, United Arab Emirates, Antigua and Barbuda, Netherlands Antilles, American Samoa, Azerbaijan, Barbados, Bahrain, Bermuda, Botswana, Belize, Cameroon, Dominica, Algeria, Federated States of Micronesia, Grenada, French Guiana, Ghana, Guinea, Guam, Iraq, Jordan, Saint Kitts and Nevis, Cayman Islands, Lebanon, Saint Lucia, Sri Lanka, Liberia, Libyan Arab Jamahiriya, Monaco, Moldova, Republic of, Northern Mariana Islands, Malta, Maldives, Nepal, Oman, Poland, Palestinian Territory, Qatar, Reunion, Saudi Arabia, Sierra Leone, Suriname, Turks and Caicos Islands, Thailand, Tunisia, Trinidad and Tobago, Tanzania, United Republic of, United States Minor Outlying Islands, Virgin Islands, British, Virgin Islands, U.S., Vietnam, Yemen, South Africa, Zambia, Zimbabwe, Afghanistan, Haiti, Aruba, Bahamas, Brunei Darussalam, Guadeloupe, Gibraltar, Mozambique, Senegal, Namibia, Equatorial Guinea, Angola, Cambodia, Gabon, Papua New Guinea, Kazakhstan, Madagascar, Timor-Leste, Congo, The Democratic Republic of the, Malvinas, Malawi, Martinique, Fiji, Mauritius, Mongolia, Tonga, Macao, Cape Verde, Niger, Ethiopia, Turkmenistan, Lesotho, Sao Tome and Principe, Vanuatu, Central African Republic, Cook Islands, Desconocido, Congo, Solomon Islands, Desconocido, Desconocido, Greenland, Swaziland, Palau, Saint Pierre and Miquelon, Faroe Islands, Desconocido, Somalia, Holy See (Vatican City State), Burundi, Anguilla, Rwanda, Mali, Burkina Faso, Kiribati, Togo, Uzbekistan, Mayotte, Liechtenstein, Gambia, Djibouti, French Polynesia, Benin, Saint Vincent and the Grenadines, Guyana, Mauritania, Lao People's Democratic Republic, Kyrgyzstan, New Caledonia, Andorra, Albania, Armenia, Argentina, Austria, Australia, Bosnia and Herzegovina, Belgium, Bulgaria, Brazil, Belarus, Canada, Switzerland, Cote d'Ivoire, Chile, China, Colombia, Cyprus, Czech Republic, Germany, Denmark, Estonia, Espa&ntilde;a, Finland, France, United Kingdom, Georgia, Greece, Hong Kong, Croatia, Hungary, Ireland, Israel, India, Iceland, Italy, Japan, Republic of Korea, Lithuania, Luxembourg, Latvia, Macedonia, Montserrat, Mexico, Malaysia, Netherlands, Norway, New Zealand, Peru, Philippines, Portugal, Romania, Russian Federation, Sweden, Singapore, Slovenia, Slovakia, San Marino, Turkey, Taiwan, Ukraine, United States, Uruguay, Paraguay, Costa Rica, El Salvador, Republica Dominicana, Guatemala, Venezuela, Panama, Puerto Rico, Bolivia, Pakistan, Ecuador, Bangladesh, Nigeria, Kenya, Morocco, Uganda, Egipto, Honduras, Indonesia, Jamaica, Kuwait, Nicaragua, United Arab Emirates, Antigua and Barbuda, Netherlands Antilles, American Samoa, Azerbaijan, Barbados, Bahrain, Bermuda, Botswana, Belize, Cameroon, Dominica, Algeria, Federated States of Micronesia, Grenada, French Guiana, Ghana, Guinea, Guam, Iraq, Jordan, Saint Kitts and Nevis, Cayman Islands, Lebanon, Saint Lucia, Sri Lanka, Liberia, Libyan Arab Jamahiriya, Monaco, Moldova, Republic of, Northern Mariana Islands, Malta, Maldives, Nepal, Oman, Poland, Palestinian Territory, Qatar, Reunion, Saudi Arabia, Sierra Leone, Suriname, Turks and Caicos Islands, Thailand, Tunisia, Trinidad and Tobago, Tanzania, United Republic of, United States Minor Outlying Islands, Virgin Islands, British, Virgin Islands, U.S., Vietnam, Yemen, South Africa, Zambia, Zimbabwe, Afghanistan, Haiti, Aruba, Bahamas, Brunei Darussalam, Guadeloupe, Gibraltar, Mozambique, Senegal, Namibia, Equatorial Guinea, Angola, Cambodia, Gabon, Papua New Guinea, Kazakhstan, Madagascar, Timor-Leste, Congo, The Democratic Republic of the, Malvinas, Malawi, Martinique, Fiji, Mauritius, Mongolia, Tonga, Macao, Cape Verde, Niger, Ethiopia, Turkmenistan, Lesotho, Sao Tome and Principe, Vanuatu, Central African Republic, Cook Islands, Desconocido, Congo, Solomon Islands, Desconocido, Desconocido, Greenland, Swaziland, Palau, Saint Pierre and Miquelon, Faroe Islands, Desconocido, Somalia, Holy See (Vatican City State), Burundi, Anguilla, Rwanda, Mali, Burkina Faso, Kiribati, Togo, Uzbekistan, Mayotte, Liechtenstein, Gambia, Djibouti, French Polynesia, Benin, Saint Vincent and the Grenadines, Guyana, Mauritania, Lao People's Democratic Republic, Kyrgyzstan, New Caledonia, Andorra, Albania, Armenia, Argentina, Austria, Australia, Bosnia and Herzegovina, Belgium, Bulgaria, Brazil, Belarus, Canada, Switzerland, Cote d'Ivoire, Chile, China, Colombia, Cyprus, Czech Republic, Germany, Denmark, Estonia, Espa&ntilde;a, Finland, France, United Kingdom, Georgia, Greece, Hong Kong, Croatia, Hungary, Ireland, Israel, India, Iceland, Italy, Japan, Republic of Korea, Lithuania, Luxembourg, Latvia, Macedonia, Montserrat, Mexico, Malaysia, Netherlands, Norway, New Zealand, Peru, Philippines, Portugal, Romania, Russian Federation, Sweden, Singapore, Slovenia, Slovakia, San Marino, Turkey, Taiwan, Ukraine, United States, Uruguay, Paraguay, Costa Rica, El Salvador, Republica Dominicana, Guatemala, Venezuela, Panama, Puerto Rico, Bolivia, Pakistan, Ecuador, Bangladesh, Nigeria, Kenya, Morocco, Uganda, Egipto, Honduras, Indonesia, Jamaica, Kuwait, Nicaragua, United Arab Emirates, Antigua and Barbuda, Netherlands Antilles, American Samoa, Azerbaijan, Barbados, Bahrain, Bermuda, Botswana, Belize, Cameroon, Dominica, Algeria, Federated States of Micronesia, Grenada, French Guiana, Ghana, Guinea, Guam, Iraq, Jordan, Saint Kitts and Nevis, Cayman Islands, Lebanon, Saint Lucia, Sri Lanka, Liberia, Libyan Arab Jamahiriya, Monaco, Moldova, Republic of, Northern Mariana Islands, Malta, Maldives, Nepal, Oman, Poland, Palestinian Territory, Qatar, Reunion, Saudi Arabia, Sierra Leone, Suriname, Turks and Caicos Islands, Thailand, Tunisia, Trinidad and Tobago, Tanzania, United Republic of, United States Minor Outlying Islands, Virgin Islands, British, Virgin Islands, U.S., Vietnam, Yemen, South Africa, Zambia, Zimbabwe, Afghanistan, Haiti, Aruba, Bahamas, Brunei Darussalam, Guadeloupe, Gibraltar, Mozambique, Senegal, Namibia, Equatorial Guinea, Angola, Cambodia, Gabon, Papua New Guinea, Kazakhstan, Madagascar, Timor-Leste, Congo, The Democratic Republic of the, Malvinas, Malawi, Martinique, Fiji, Mauritius, Mongolia, Tonga, Macao, Cape Verde, Niger, Ethiopia, Turkmenistan, Lesotho, Sao Tome and Principe, Vanuatu, Central African Republic, Cook Islands, Desconocido, Congo, Solomon Islands, Desconocido, Desconocido, Greenland, Swaziland, Palau, Saint Pierre and Miquelon, Faroe Islands, Desconocido, Somalia, Holy See (Vatican City State), Burundi, Anguilla, Rwanda, Mali, Burkina Faso, Kiribati, Togo, Uzbekistan, Mayotte, Liechtenstein, Gambia, Djibouti, French Polynesia, Benin, Saint Vincent and the Grenadines, Guyana, Mauritania, Lao People's Democratic Republic, Kyrgyzstan y New Caledonia") {
                $data['paises'] = 'Todos los paises';
            }

// retomo los sitios o canales tematicos seg&uacute;n la segmentaci&oacute;n de la campa&ntilde;a.
            if ($data['segmentacion_id'] == 2) {
// obtengo los canales tematicos seleccionados
                $canales = $this->campaniascanalestematicos->get_canales_tematicos_by_campania($data['id_campania']);
// recorro por cada canal obtengo su nombre desde la base.
                if ($canales) {
                    foreach ($canales as $canal) {
//obtengo los datos del canal de la BD
                        $data_canal = $this->categorias->get_categoria_by_id($canal->id_canal_tematico);
                        $canales_tematicos[] = array('id' => $data_canal->id, 'name' => $data_canal->nombre);
                    }
                    $data['canales_tematicos'] = $canales_tematicos;
                } else {
                    $data['canales_tematicos'] = 'No se encontraron categor&iacute;as asociadas a la campa&ntilde;a.';
                }
            } else {
// selecciono todos los sitios correspondientes a la camapania
                $sitios = $this->campaniassitios->get_sitios_by_campania($data['id_campania']);

                if ($sitios) {
                    foreach ($sitios as $site) {
// de cada sitio obtengo su nombre y lo guardo en el array para pasar a la vista.
                        $sitio = $this->sites->get_site_by_id($site->id_sitio);
                        $nombre_sitio = $sitio->nombre_appnexus;
                        if ($nombre_sitio == '')
                            $nombre_sitio = $sitio->nombre_dfp;

                        $arr_sitios[] = array('nombre' => $nombre_sitio);
                    }
                    $data['sitios'] = $arr_sitios;
                }else {
                    $data['sitios'] = 'No se encontraron sitios asociados a la campa&ntilde;a.';
                }
            }

            if ($campania->empresa_campania == 1) {
                // selecciono las audiencias de la campania
                $data['audiencias'] = $this->campanias->get_audiencias_by_campania($campania->id);
            }

// obtengo los formatos de la campa&ntilde;a
            $formatos = $this->campaniasformatos->get_formatos_by_campania($data['id_campania']);

            if ($formatos) {
                foreach ($formatos as $row) {
// obtengo el nombre del formato
                    $formato = $this->formatosdfp->get_formato_by_id($row->id_formato);

                    if ($usuario->notacion == 0) {
                        $monto = number_format($row->monto, 2, '.', ',');
                        $cantidad = number_format($row->cantidad, 0, '.', ',');
                    } else if ($usuario->notacion == 1) {
                        $monto = number_format($row->monto, 2, ',', '.');
                        $cantidad = number_format($row->cantidad, 0, ',', '.');
                    }

                    $arr_formatos[$row->id_formato] = array(
                        'descripcion' => $formato->descripcion,
                        'modalidad' => strtoupper($row->id_modalidad_compra),
                        'monto' => $monto . ' ' . $this->user_data->moneda,
                        'cantidad' => $cantidad,
                        'pagina_destino' => $row->pagina_destino
                    );
                }
                $data['formatos'] = $arr_formatos;
            }
            $dias_campania = $this->_dateDiff($campania->fecha_inicio, $campania->fecha_fin);
            $inversion_neta = ($data['inversion_neto'] * $dias_campania);

            if ($campania->modalidad_compra == 'cpm') {
                $data['diario'] = ($data['inversion_neto'] / $campania->cantidad) * 1000;
            } else {
                $data['diario'] = ($data['inversion_neto'] / $campania->cantidad);
            }

            if ($usuario->notacion == 0) {
                $data['inversion_neta_total'] = number_format($inversion_neta, 2, '.', ',');
                $data['inversion_neto'] = number_format($data['inversion_neto'], 2, '.', ',');
                $data['diario'] = number_format($data['diario'], 2, '.', ',') . ' ' . $this->user_data->moneda;
            } else if ($usuario->notacion == 1) {
                $data['inversion_neta_total'] = number_format($inversion_neta, 2, ',', '.');
                $data['inversion_neto'] = number_format($data['inversion_neto'], 2, ',', '.');
                $data['diario'] = number_format($data['diario'], 2, ',', '.') . ' ' . $this->user_data->moneda;
            }

// la campa&ntilde;a esta activada?
            $data['activada'] == 0 ? $data['activada'] = 'NO' : $data['activada'] = 'SI';

            $data['usuario'] = $usuario;

            $data['campania'] = $campania;

            if ($campania->frecuencia == 'NORMAL') {
                $frecuencia = 'Optimizada';
            } else if ($campania->frecuencia == '1x24') {
                $frecuencia = '1 impresi&oacute;n cada 24 horas';
            } else if ($campania->frecuencia == '2x24') {
                $frecuencia = '2 impresiones cada 24 horas';
            }

            $data['frecuencia'] = $frecuencia;

            $data['campania_padre'] = FALSE;

            $campania_padre = $this->campanias->get_campania_padre($campania->id);

            if ($campania_padre) {
                $data_campania_padre = $this->campanias->get_campania_by_id($campania_padre->id_campania_padre);

                if ($data_campania_padre)
                    $data['campania_padre'] = $data_campania_padre->nombre;
            }

            $html = $this->load->view('campanias_ver_PDF', $data, TRUE);


            if ($campania->empresa_campania == 0) {
                $header = '<html><head></head><body style="margin:3000mm 10000mm;"><div style="background-color:#E43B8E;
                                   color: #fff;
                                   font: normal normal 1em ' . "'Calibri'" . ', Arial, Helvetica, sans-serif;
                                   padding: 10px;
                                   text-align: center;
                                   width:100%;">
                           <img alt="MediaFem" src="/images/logo.png">
                       </div>';
            } else {
                $header = '<html><head></head><body style="margin:3000mm 10000mm;"><div style="border-bottom: 2px solid #89C003;
                                   padding: 20px; margin-bottom: 10px;
                                   text-align: center;
                                   width:100%;">
                           <img alt="MediaFem" src="/images/adtomatik_logo.png">
                       </div>';
            }
            if ($campania->empresa_campania == 0) {
                $footer = '<div style="background-color:#E43B8E;
                                   color: #fff;
                                   font: normal normal 0.9em ' . "'Calibri'" . ', Arial, Helvetica, sans-serif;
                                   padding: 10px;
                                   width:100%;">
                           <div style="padding: 5px;">E-mail: ' . EMAIL_ANUNCIANTES . '</div>
                           <div style="padding: 5px;">Tel.: ' . TELEFONO_ANUNCIANTES . '</div>
                           <div style="padding: 5px;">&copy; ' . date('Y') . ' MediaFem - Media Fem LLC.</div>
                       </div></body></html>';
            } else {
                $footer = '<div style="border-top: 2px solid #89C003;
                                   color: #89C003;
                                   font: normal normal 0.9em ' . "'Calibri'" . ', Arial, Helvetica, sans-serif;
                                   padding: 10px;
                                   width:100%;">
                           <div style="padding: 5px;">Tel.: +1 786-315-9918</div>
                           <div style="padding: 5px;">&copy; ' . date('Y') . ' AdTomatik by MediaFem LLC.</div>
                       </div></body></html>';
            }

            $this->load->library('mpdf');

            $this->mpdf->SetHTMLHeader($header);
            $this->mpdf->SetHTMLFooter($footer);
            $this->mpdf->SetMargins(0, 0, 30, 26);
            $this->mpdf->WriteHTML($html);

            if ($campania->empresa_campania == 0) {
                $this->mpdf->Output("MediaFem - " . $data['nombre_campania'] . ".pdf", "D");
            } else {
                $this->mpdf->Output("AdTomatik - " . $data['nombre_campania'] . ".pdf", "D");
            }
        }
    }

    function mostrar_materiales($id_campania) {
        $data['materiales'] = $this->archivoscreatividades->get_archivos($id_campania);

        $data['id_campania'] = $id_campania;

        $this->load->view('tbl_materiales_modificar', $data);
    }

    function eliminar_material($id_material, $id_campania) {
        $material = $this->archivoscreatividades->get_archivo($id_material);

        $this->archivoscreatividades->delete($id_material);

        $update_campania = array(
            'estado' => 'PENDIENTE'
        );

        $this->campanias->update_campania($id_campania, $update_campania);

        $data['materiales'] = $this->archivoscreatividades->get_archivos($id_campania);

        $data['id_campania'] = $id_campania;

        $campania = $this->campanias->get_campania_by_id($id_campania);

        if ($campania && $material) {
            $version = $campania->historial_version + 1;
// creo un historial
            $this->insert_historial($id_campania, $version, 'Se actualizaron las piezas, se elimino la siguiente: ' . $material->nombre_real . '.');

// inserto un comentario para ordenar todo
            $data_insert_comentario = array(
                'id_campania' => $id_campania,
                'comentario' => HISTORIAL_STRING . ' - Historial - ' . $version,
                'fecha_alta' => date('Y-m-j H:i:s', strtotime('-1 hour', strtotime(date('Y-m-d H:i:s'))))
            );
            $this->campaniashistorial->insert_comentario($data_insert_comentario);

            $update = array(
                'historial_version' => $version
            );

            $this->campanias->update_campania($id_campania, $update);
        }

        $this->load->view('tbl_materiales_modificar', $data);
    }

    function modificar($id_campania) {
        $campania = $this->campanias->get_campania_by_id($id_campania);

        if ($campania) {

            $newdata = array(
                'id_campania' => $id_campania
            );

            $this->session->set_userdata($newdata);

            $data['id_campania'] = $id_campania;

// nombre de la campania
            $data['nombre_campania'] = $campania->nombre;

// segmentacion
            $data['segmentacion_id'] = $campania->segmentacion_id;

// retomo el nombre del anunciante
            $anunciante = $this->anunciantes->get_anunciante_adserver_by_id($campania->id_anunciante);
            if ($anunciante) {
                $data['nombre_anunciante'] = $anunciante->nombre;
            } else {
                $data['nombre_anunciante'] = 'Anunciante inexistente en la base de datos.';
            }

// fecha de inicio y fecha de fin
            $data['fecha_inicio'] = MySQLDateToDateDatepicker($campania->fecha_inicio);
            $data['fecha_fin'] = MySQLDateToDateDatepicker($campania->fecha_fin);

// paises seleccionados de la campania
            $data['paises_seleccionados'] = $this->campaniaspaises->get_paises_by_campania($data['id_campania']);

// modalidad de compra
            $formatoscampania = $this->campaniasformatos->get_formatos_by_campania($campania->id);

            $data['modalidad_de_compra'] = $campania->modalidad_compra;

            $data['cantidad'] = $campania->cantidad;

            $data['monto'] = $campania->valor_unidad;

            $data['descuento'] = $campania->descuento;
            $data['comision'] = $campania->comision;

            $usuario = $this->users->get_user_by_id($this->tank_auth->get_user_id(), 1);
            $data['habilitar_descuentos'] = $usuario->habilitar_descuentos;

            $data['segmentaciones'] = $this->get_segmentaciones();
            $data['canales_tematicos'] = $this->get_canales_tematicos();

            $data['sitios_seleccionados'] = $data['canales_seleccionados'] = NULL;

            if ($data['segmentacion_id'] == 2) {
                $canales_seleccionados = $this->campaniascanalestematicos->get_canales_tematicos_by_campania($id_campania);
                if ($canales_seleccionados) {
                    foreach ($canales_seleccionados as $canal)
                        $data['canales_seleccionados'][$canal->id_canal_tematico] = $canal->id_canal_tematico;
                }
            }

            if ($data['segmentacion_id'] == 3) {
                $sitios = $this->campaniassitios->get_sitios_by_campania($id_campania);
                if ($sitios) {
                    foreach ($sitios as $sitio) {
                        $sitio_data = $this->sites->get_site_by_id($sitio->id_sitio);
                        if ($sitio_data) {
                            $nombre = $sitio_data->nombre_dfp;
                            if ($nombre == '')
                                $nombre = $sitio_data->nombre_appnexus;

                            $data['sitios_seleccionados'][] = array(
                                'id' => $sitio->id_sitio,
                                'nombre' => $nombre
                            );
                        }
                    }
                }
            }

            $inversion_neta = $this->constants->get_constant_by_id(19);
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

                    $data['inversion_neta'] = $inversion_neta->value * $cotizacion;
                } else {
                    return FALSE;
                }
            } else {
                $data['inversion_neta'] = $inversion_neta->value;
            }

            if ($campania->empresa_campania == 0) {
                $inversion_cpc_cpm = $this->constants->get_constant_by_id(20);
            } else {
                $inversion_cpc_cpm = $this->constants->get_constant_by_id(29);
            }
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

                    $data['inversion_cpc_cpm'] = $inversion_cpc_cpm->value * $cotizacion;
                } else {
                    return FALSE;
                }
            } else {
                $data['inversion_cpc_cpm'] = $inversion_cpc_cpm->value;
            }

            $data['tipo_campania'] = $campania->tipo_campania;
            $data['type'] = $campania->type_DFP;
            $data['frecuencia'] = $campania->frecuencia;
            $data['campania_padre'] = $campania->campania_padre;
            $data['empresa_campania'] = $campania->empresa_campania;
            $data['campania'] = $campania;

            if ($campania->empresa_campania != 0) {
                $data['permitir_unificar_campanias'] = $data['campanias_padres'] = $data['la_campania_padre'] = FALSE;

                $this->load->view('campania_modificar_adtomatik', $data);
            } else {
                $data['permitir_unificar_campanias'] = $this->user_data->permitir_unificar_campanias;

                $data['campanias_padres'] = $this->campanias_padres();

                $data['la_campania_padre'] = $this->campania_padre($campania->id);

                $this->load->view('campania_modificar', $data);
            }
        }
    }

    function update() {
        $id_campana = trim($this->input->post('id_campana'));
        $nombre = trim($this->input->post('nombre'));
        $fecha_inicio = trim($this->input->post('fecha_inicio'));
        $fecha_fin = trim($this->input->post('fecha_fin'));

        $modifico_audiencias = trim($this->input->post('modifico_audiencias'));

        $segmentacion = trim($this->input->post('segmentacion'));
        $id_sitios = trim($this->input->post('id_sitios'));
        $id_canales_tematicos = trim($this->input->post('id_canales_tematicos'));

        $id_paises = trim($this->input->post('id_paises'));
        $cantidad = trim($this->input->post('cantidad'));
        $inversion_neta = trim($this->input->post('inversion_neta'));
        $inversion_neta = str_replace(',', '.', $inversion_neta);

        $modalidad_compra = trim($this->input->post('modalidad_compra'));
        $valor_unidad = trim($this->input->post('valor_unidad'));

        $descuento = trim($this->input->post('descuento'));
        $comision = trim($this->input->post('comision'));

        $formatos = trim($this->input->post('formatos'));

        $frecuencia = trim($this->input->post('frecuencia'));

        $device_desktop = trim($this->input->post('device_desktop'));
        $device_tablet = trim($this->input->post('device_tablet'));
        $device_phone = trim($this->input->post('device_phone'));

        list($dia_desde, $mes_desde, $anio_desde) = explode("-", $fecha_inicio);
        list($dia_hasta, $mes_hasta, $anio_hasta) = explode("-", $fecha_fin);
        $fecha_inicio = $anio_desde . "-" . $mes_desde . "-" . $dia_desde . ' 00:00:00';
        $fecha_fin = $anio_hasta . "-" . $mes_hasta . "-" . $dia_hasta . ' 23:59:59';

// valido la fecha de inicio
        if (strlen($nombre) <= 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'Por favor ingrese un nombre para la campa&ntilde;a.'));
            die();
        }

// valido la fecha de inicio
        if (strlen($fecha_inicio) <= 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'Por favor ingrese la fecha de inicio correspondiente a la campa&ntilde;a.'));
            die();
        }

// valido foramtos seleccionados para la campa&ntilde;a.
        if (strlen($formatos) <= 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'Por favor seleccione al menos un formato para la campa&ntilde;a.'));
            die();
        }

        $fecha_actual = date('Y-m-d');
// valido que la fecha de inicio no sea menor a la fecha actual
        /*
          if ($this->_dateDiff($fecha_inicio, $fecha_actual) > 0) {
          echo json_encode(array('validate' => FALSE, 'error' => 'La fecha de inicio es menor a la fecha actual.'));
          die();
          }
         */

// valido que la fecha de inicio no sea mayor a la fecha de fin
        if ($this->_dateDiff($fecha_inicio, $fecha_fin) < 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'La fecha de inicio es mayor a la fecha de fin de la campa&ntilde;a.'));
            die();
        }

// valido que la fecha de fin no sea menor a la fecha actual
        if ($this->_dateDiff($fecha_fin, $fecha_actual) > 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'La fecha de fin es menor a la fecha actual.'));
            die();
        }

// valido que la fecha de fin no sea menor a la fecha de inicio
        if ($this->_dateDiff($fecha_fin, $fecha_inicio) > 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'La fecha de fin es menor a la fecha de inicio de la campa&ntilde;a.'));
            die();
        }

// valido la fecha de fin
        if (strlen($fecha_fin) <= 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'Por favor ingrese la fecha de fin correspondiente a la campa&ntilde;a.'));
            die();
        }

// valido que haya seleccionado al menos un pais
        if (strlen($id_paises) <= 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'Por favor seleccione al menos un pa&iacute;s correspondiente a la campa&ntilde;a.'));
            die();
        }

// valido la cantidad
        if (strlen($cantidad) <= 0 || $cantidad <= 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'Por favor ingrese una cantidad valida.'));
            die();
        }

// valido la inversion
        if (strlen($inversion_neta) <= 0 || $inversion_neta <= 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'Inversi&oacute;n neta invalida.'));
            die();
        }

        $campania = $this->campanias->get_campania_by_id($id_campana);
        $id_cliente = $campania->id_cliente;

        if (!$campania) {
            echo json_encode(array('validate' => FALSE, 'error' => 'No se encontr&oacute; la campa&ntilde;a especificada.'));
            die();
        }

        if ($campania->empresa_campania != 0) {
            $unificar_campania = FALSE;
        } else {
            $unificar_campania = trim($this->input->post('unificar_campania'));
        }

        $inversion_neta_old = $inversion_neta;

        // si el usuario modifico la inversion de la campania.
        if ((float) $inversion_neta != (float) $campania->inversion_neta) {
            $saldo_prepago_anunciante = $this->user_data->saldo_prepago;
            $saldo_prestamo_anunciante = $this->user_data->saldo_prestamo;
            $limite_compra_anunciante = $this->user_data->limite_de_compra;

            $consumo_saldo_prepago = $campania->consumo_saldo_prepago;
            $consumo_saldo_prestamo = $campania->consumo_saldo_prestamo;

            $cantidad_dias = $this->_dateDiff($campania->fecha_inicio, $campania->fecha_fin);

            if ($campania->type_DFP == 'PRICE_PRIORITY') {
                $inversion_neta = $inversion_neta * $cantidad_dias;

                $campania->inversion_neta = $campania->inversion_neta * $cantidad_dias;
            }

            // si la inversion neta actual de la campania es mayor a la inversion modificada entonces devuelvo plata.
            if ($campania->inversion_neta >= $inversion_neta) {

                $a_devolver = $campania->inversion_neta - $inversion_neta;
                $usuario = $this->users->get_user_by_id($this->tank_auth->get_user_id(), 1);

                if ($id_cliente) {
                    //Acredito
                    $data_credito_cliente = array(
                        'id_cliente' => $campania->id_cliente,
                        'descripcion' => 'Reintegro por modificaci&oacute;n en la inversi&oacute;n de la campa&ntilde;a ' . $campania->nombre . '.',
                        'tipo' => '1',
                        'tipo_saldo' => ($usuario->creado_desde_sitio) ? '0' : '1',
                        'importe' => $a_devolver,
                        'cargado_por' => trim($this->tank_auth->get_user_id())
                    );

                    $this->clientes_model->insert_cliente_saldo($data_credito_cliente);
                }
                /*
                  // cargo el credito del saldo
                  $data_insert2 = array(
                  'id_anunciante' => $this->tank_auth->get_user_id(),
                  'credito' => $a_devolver,
                  'balance' => $limite_compra_anunciante + $a_devolver,
                  'moneda' => $this->user_data->moneda,
                  'descripcion' => 'Reintegro por modificaci&oacute;n en la inversi&oacute;n de la campa&ntilde;a ' . $campania->nombre . '.',
                  'tipo_de_pago' => 1
                  );

                  $this->anunciantessaldos->insert_anunciante_saldo($data_insert2);
                 */
                if ($campania->consumo_saldo_prepago >= $a_devolver) {
                    //devuelvo primero todo lo posible de prepago
                    $saldo_prepago_anunciante = $saldo_prepago_anunciante + $a_devolver;
                    $limite_compra_anunciante = $limite_compra_anunciante + $a_devolver;

                    $consumo_saldo_prepago = $campania->consumo_saldo_prepago - $a_devolver;
                } else {
                    $saldo_prepago_anunciante = $saldo_prepago_anunciante + $campania->consumo_saldo_prepago;
                    $saldo_prestamo_anunciante = $saldo_prestamo_anunciante + ($a_devolver - $campania->consumo_saldo_prepago);

                    $consumo_saldo_prepago = 0;
                    $consumo_saldo_prestamo = $consumo_saldo_prestamo - ($a_devolver - $campania->consumo_saldo_prepago);
                }

                $data_update = array(
                    'limite_de_compra' => $limite_compra_anunciante,
                    'saldo_prepago' => $saldo_prepago_anunciante,
                    'saldo_prestamo' => $saldo_prestamo_anunciante
                );

                //$this->anunciantes->update_anunciante($this->tank_auth->get_user_id(), $data_update);

                $update_campania = array(
                    'consumo_saldo_prepago' => $consumo_saldo_prepago,
                    'consumo_saldo_prestamo' => $consumo_saldo_prestamo,
                );

                $this->campanias->update_campania($id_campana, $update_campania);
            } else {

                // si la inversion neta actual de la campania es menor a la inversion modificada entonces quito plata.
                $a_quitar = $inversion_neta - $campania->inversion_neta;

                if ($id_cliente) {
                    //Debito
                    $data_credito_cliente = array(
                        'id_cliente' => $campania->id_cliente,
                        'descripcion' => 'Pago de la campa&ntilde;a ' . $campania->nombre . '.',
                        'tipo' => '0',
                        'tipo_saldo' => ($usuario->creado_desde_sitio) ? '0' : '1',
                        'importe' => '-' . $a_quitar,
                        'cargado_por' => trim($this->tank_auth->get_user_id())
                    );

                    $this->clientes_model->insert_cliente_saldo($data_credito_cliente);
                }
                /*
                  // cargo el debito del saldo
                  $data_insert2 = array(
                  'id_anunciante' => $this->tank_auth->get_user_id(),
                  'debito' => $a_quitar,
                  'balance' => $limite_compra_anunciante - $a_quitar,
                  'moneda' => $this->user_data->moneda,
                  'descripcion' => 'Pago de la campa&ntilde;a ' . $campania->nombre . '.',
                  'tipo_de_pago' => 2
                  );

                  $this->anunciantessaldos->insert_anunciante_saldo($data_insert2);
                 */
                //si tiene saldo prepago entonces quito lo mas que pueda de este.
                if ($saldo_prepago_anunciante >= $a_quitar) {
                    $saldo_prepago_anunciante = $saldo_prepago_anunciante - $a_quitar;
                    $limite_compra_anunciante = $limite_compra_anunciante - $a_quitar;

                    $consumo_saldo_prepago = $campania->consumo_saldo_prepago + $a_quitar;
                } else {
                    //si el saldo prepago no alcanza para descontar todo entonces descuento del prestamo
                    $saldo_prepago_anunciante = 0;
                    $saldo_prestamo_anunciante = $saldo_prestamo_anunciante - ($a_quitar - $this->user_data->saldo_prepago);

                    $limite_compra_anunciante = $limite_compra_anunciante - $a_quitar;

                    $consumo_saldo_prepago = $consumo_saldo_prepago + $this->user_data->saldo_prepago;

                    if ($inversion_neta > $consumo_saldo_prepago) {
                        $consumo_saldo_prestamo = $inversion_neta - $consumo_saldo_prepago;
                    } else {
                        $consumo_saldo_prestamo = $consumo_saldo_prestamo + ($consumo_saldo_prepago - $inversion_neta);
                    }
                }

                $data_update = array(
                    'limite_de_compra' => $limite_compra_anunciante,
                    'saldo_prepago' => $saldo_prepago_anunciante,
                    'saldo_prestamo' => $saldo_prestamo_anunciante
                );

                //$this->anunciantes->update_anunciante($this->tank_auth->get_user_id(), $data_update);

                $update_campania = array(
                    'consumo_saldo_prepago' => $consumo_saldo_prepago,
                    'consumo_saldo_prestamo' => $consumo_saldo_prestamo
                );

                $this->campanias->update_campania($id_campana, $update_campania);
            }
        }

// HISTORIAL ***********************************************************
        $version = $campania->historial_version + 1;

// campania padre
        $campania_padre = $this->campanias->get_campania_padre($id_campana);

        if ($campania_padre) {
            if ($campania_padre->id_campania_padre != $unificar_campania) {
                if ($unificar_campania == 'NINGUNA') {
                    $this->insert_historial($id_campana, $version, 'Se unific&oacute; la campa&ntilde;a con : -- NINGUNA -- .');
                } else {
                    $data_campania_padre = $this->campanias->get_campania_by_id($unificar_campania);

                    if ($data_campania_padre)
                        $this->insert_historial($id_campana, $version, 'Se unific&oacute; la campa&ntilde;a con : ' . $data_campania_padre->nombre . '.');
                }
            }
        }else {
            if ($unificar_campania == 'NINGUNA')
                $this->insert_historial($id_campana, $version, 'Se unific&oacute; la campa&ntilde;a con : -- NINGUNA -- .');
        }

// nombre
        if ($campania->nombre != $nombre) {
            $this->insert_historial($id_campana, $version, 'Se actualiz&oacute; el nombre de la campa&ntilde;a a ' . $nombre . '.');
        }

// fecha inicio
        if ($campania->fecha_inicio != $fecha_inicio) {
            $this->insert_historial($id_campana, $version, 'Se actualiz&oacute; la fecha de inicio de campa&ntilde;a a ' . MySQLDateToDate($fecha_inicio) . '.');
        }

// fecha fin
        if ($campania->fecha_fin != $fecha_fin) {
            $this->insert_historial($id_campana, $version, 'Se actualiz&oacute; la fecha de fin de campa&ntilde;a a ' . MySQLDateToDate($fecha_fin) . '.');
        }

// frecuencia
        if ($campania->frecuencia != $frecuencia) {
            if ($campania->empresa_campania == 0) {
                if ($frecuencia == 'NORMAL') {
                    $texto_frecuencia = 'Optimizada';
                } else if ($frecuencia == '1x24') {
                    $texto_frecuencia = '1 impresi&oacute;n cada 24 horas';
                } else if ($frecuencia == '2x24') {
                    $texto_frecuencia = '2 impresiones cada 24 horas';
                }
                $this->insert_historial($id_campana, $version, 'Se actualiz&oacute; la frecuencia de la campa&ntilde;a a ' . $texto_frecuencia . '.');
            }
        }

// segmentacion
        if ($campania->segmentacion_id != $segmentacion) {
            if ($campania->empresa_campania == 0) {
                if ($segmentacion == 1) {
                    $texto = 'Se actualiz&oacute; la segmentaci&oacute;n a Toda la Red.';
                } else if ($segmentacion == 2) {
                    $texto = 'Se actualiz&oacute; la segmentaci&oacute;n a Canales tem&aacute;ticos.';
                } else if ($segmentacion == 3) {
                    $texto = 'Se actualiz&oacute; la segmentaci&oacute;n a Sitios especificos.';
                }

                $this->insert_historial($id_campana, $version, $texto);
            }
        }

// audiencias
        if ($modifico_audiencias) {
            if ($campania->empresa_campania == 1) {
                $audiencias = $this->campanias->get_audiencias_by_campania($campania->id);

                if ($audiencias) {
                    $texto = '<b>Audiencias: </b>Se actualizaron las segmentaciones:<ul>';

                    foreach ($audiencias as $audiencia)
                        $texto .= '<li>' . $audiencia->name . '</li>';

                    $texto .= '</ul>';
                } else {
                    $texto = '<b>Audiencias: </b>Se quitaron las audiencias en esta campa&ntilde;a.';
                }

                $this->insert_historial($id_campana, $version, $texto);
            }
        }

// cantidad
        if ($campania->cantidad != $cantidad) {
            $this->insert_historial($id_campana, $version, 'Se actualiz&oacute; la cantidad de la campa&ntilde;a a ' . $cantidad . '.');
        }

        $this->insert_historial($id_campana, $version, 'Se actualiz&oacute; el estado de la campa&ntilde;a a PENDIENTE.');

        if ($unificar_campania) {
            if ($unificar_campania == 'NINGUNA') {
                $campania_padre = '1';
            } else {
                $campania_padre = '0';
            }
        } else {
            $campania_padre = '0';
        }

        $update = array(
            'nombre' => $nombre,
            'segmentacion_id' => $segmentacion,
            'valor_unidad' => (float) str_replace(',', '.', $valor_unidad),
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'cantidad' => $cantidad,
            'inversion_bruta' => $inversion_neta_old,
            'inversion_neta' => $inversion_neta_old,
            'descuento' => $descuento,
            'comision' => $comision,
            'para_modificar' => 1,
            'frecuencia' => $frecuencia,
            'estado' => 'PENDIENTE',
            'campania_padre' => $campania_padre,
            'historial_version' => $version,
            'device_desktop' => $device_desktop,
            'device_tablet' => $device_tablet,
            'device_phone' => $device_phone,
        );

        // si se unifica con otra campania entonces las asocio
        /*
          if ($unificar_campania) {
          if ($unificar_campania != 'NINGUNA') {
          $data_unificadas = array(
          'id_campania_padre' => $unificar_campania
          , 'id_campania_hija' => $id_campana
          );

          $this->campanias->update_campania_unificada($id_campana, $data_unificadas);
          } else {
          $this->campanias->delete_campania_unificada($id_campana);
          }
          }
         */
        // se vuelve a validar si solamente se cambia la inversion neta de la campania
        $this->campanias->update_campania($id_campana, $update);

        if ($campania->inversion_neta != $inversion_neta_old) {

            $data_control = array(
                'validado_por_cuentas_alta' => '0'
            );

            $this->campaniascontrol->update_control_campania($id_campana, $data_control);
        }

// borro todos los sitios anteriores
        $this->campaniassitios->delete_sitio_by_campania($id_campana);

// segmentacion por sitio especifico
        if ($segmentacion == 3) {
// obtengo los sitios seleccionados
            $sitios = explode(";", $id_sitios);

// si hay sitios los agrego al array
            if ($sitios) {
                foreach ($sitios as $sitio) {
                    if ($sitio)
                        $arr_sitios[] = $sitio;
                }
            }
        }

// traigo todos los canales de la campana para ver si se modificaron
        $canales_campania = $this->campaniascanalestematicos->get_canales_tematicos_by_campania($id_campana);

        $str_canales_campania = '';

        $canales_modificados = FALSE;

// borro todos los canales tematicos anteriores
        $this->campaniascanalestematicos->delete_canales_by_campania($id_campana);
// segmentacion por canal tematico
        if ($segmentacion == 2) {
// obtengo los canales seleccionados
            $canales_seleccionados = explode(";", $id_canales_tematicos);

//$canales = '';
// recorro los canales seleccionados
            for ($a = 0; $a < sizeof($canales_seleccionados); $a++) {
                if (!empty($canales_seleccionados[$a])) {
// ingreso los canales tematicos que soporta la campa&ntilde;a.
                    $data_campania_canales = array(
                        'id_campania' => $id_campana,
                        'id_canal_tematico' => $canales_seleccionados[$a],
                    );

// almaceno en la base de datos los canales que soporta la campa&ntilde;a
                    $this->campaniascanalestematicos->insert_campania_canal_tematico($data_campania_canales);

                    $datos_categoria = $this->categorias->get_categoria_by_id($canales_seleccionados[$a]);
                    if ($datos_categoria) {
                        if ($canales_campania) {
                            if (!$this->in_array_field($datos_categoria->id, 'id_canal_tematico', $canales_campania))
                                $canales_modificados = TRUE;
                        }

                        if ($a == (sizeof($canales_seleccionados) - 1) - 2) {
                            $str_canales_campania .= $datos_categoria->nombre . ' y ';
                        } else {
                            $str_canales_campania .= $datos_categoria->nombre . ', ';
                        }
                    }

// traigo todos los sitios que soportan este canal para asociar la campa&ntilde;a con los sitios
                    $sitios = $this->sitescategories->get_all_sites_by_category($canales_seleccionados[$a]);

// si hay sitios los agrego al array para asociar la campa&ntilde;a con los sitios
                    if ($sitios) {
                        foreach ($sitios as $sitio) {
                            if ($sitio != NULL)
                                $arr_sitios[] = $sitio->id;
                        }
                    }
                }
            }

            if (sizeof($canales_seleccionados) - 1 != sizeof($canales_campania))
                $canales_modificados = TRUE;

            $str_canales_campania = substr($str_canales_campania, 0, - 2);

            if ($canales_modificados)
                $this->insert_historial($id_campana, $version, 'Se actualizaron los canales tematicos: ' . trim($str_canales_campania, ', '));
        }

// segmentacion por Toda la red
        if ($segmentacion == 1) {
// traigo todos los sitios de nuestra red
            $sitios = $this->sites->get_all_sites();

// si hay sitios los agrego al array
            if ($sitios) {
                foreach ($sitios as $sitio) {
                    if ($sitio->user_id)
                        $arr_sitios[] = $sitio->id;
                }
            }
        }


// asocio en la DB la campania con los sitios
        if (isset($arr_sitios)) {
// traigo los formatos seleccionados en la campania
//$formatos = $this->campaniasformatos->get_formatos_by_campania($id_campana);
// elimino los sitios duplicados del array
            $sitios = array_values(array_unique($arr_sitios));

// traigo todos los formatos de la campana para ver si se modificaron
            $formatos_campania = $this->campaniasformatos->get_formatos_by_campania($id_campana);

// borro los sitios formatos para la campania
            $this->campaniassitiosformatos->delete_formatos_by_campania($id_campana);

// borro los formatos de la campania
            $this->campaniasformatos->delete_formatos_by_campania($id_campana);

// valor por unidad
            if ($formatos_campania[0]->monto != str_replace(',', '.', $valor_unidad)) {
                $this->insert_historial($id_campana, $version, 'Se actualiz&oacute; el valor por unidad de la campa&ntilde;a a ' . $valor_unidad . '.');
            }

            $str_formatos_campania = '';

            $formatos_modificados = FALSE;

// separo los formatos seleccionados
            $formatos = explode(";", $formatos);

            for ($a = 0; $a < sizeof($sitios); $a++) {
// asocio los sitios a la campa&ntilde;a creada
                $data_campania_sitio = array('id_campania' => $id_campana, 'id_sitio' => $sitios[$a]);
                $this->campaniassitios->insert_campania_sitio($data_campania_sitio);

                /*
                  // asocio cada sitio a los formatos seleccionados
                  // recorro los formatos seleccionados
                  foreach ($formatos as $formato) {
                  // asocio cada sitio a los formatos seleccionados
                  $data_campania_sitio_formato = array('id_campania' => $id_campana, 'id_sitio' => $sitios[$a], 'id_formato' => $formato->id_formato);
                  $this->campaniassitiosformatos->insert_campania_sitio_formato($data_campania_sitio_formato);
                  }
                 *
                 */

// asocio cada sitio a los formatos seleccionados
// recorro los formatos seleccionados
                for ($b = 0; $b < sizeof($formatos); $b++) {
                    if (!empty($formatos[$b])) {
                        $formatoX = explode("|", $formatos[$b]);
// retomo el id del formato seleccionado
                        $id_formato = $formatoX[0];
// asocio cada sitio a los formatos seleccionados
                        $data_campania_sitio_formato = array('id_campania' => $id_campana, 'id_sitio' => $sitios[$a], 'id_formato' => $id_formato);
                        $this->campaniassitiosformatos->insert_campania_sitio_formato($data_campania_sitio_formato);
                    }
                }
            }

// almaceno los formatos seleccionados para la campa&ntilde;a.
            for ($b = 0; $b < sizeof($formatos); $b++) {
                if (!empty($formatos[$b])) {
                    $formato = explode("|", $formatos[$b]);

                    $pagina_destino = $formato[1];

// retomo los datos de los formatos seleccionado
                    $data_formato = array(
                        'id_campania' => $id_campana,
                        'id_formato' => $formato[0],
                        'pagina_destino' => $pagina_destino
                    );

// ingreso los datos en la tabla
                    $this->campaniasformatos->insert_campania_formato($data_formato);

                    $datos_formato = $this->formatosdfp->get_formato_by_id($formato[0]);
                    if ($datos_formato) {
                        if ($formatos_campania) {
                            if (!$this->in_array_field($datos_formato->id, 'id_formato', $formatos_campania))
                                $formatos_modificados = TRUE;
                        }

                        if ($a == (sizeof($formatos) - 1) - 2) {
                            $str_formatos_campania .= $datos_formato->descripcion . ' y ';
                        } else {
                            $str_formatos_campania .= $datos_formato->descripcion . ', ';
                        }
                    }
                }
            }

            $update_2 = array(
                'id_modalidad_compra' => $modalidad_compra,
                'monto' => (float) str_replace(',', '.', $valor_unidad)
            );

            $this->campaniasformatos->update_campania_formato($id_campana, $update_2);

            if (sizeof($formatos) - 1 != sizeof($formatos_campania))
                $formatos_modificados = TRUE;

            $str_formatos_campania = substr($str_formatos_campania, 0, - 2);

            if ($formatos_modificados)
                $this->insert_historial($id_campana, $version, 'Se actualizaron los formatos de la campa&ntilde;a a: ' . trim($str_formatos_campania, ', '));
        }

// traigo todos los paises de la campana para ver si se modificaron
        $paises_campania = $this->campaniaspaises->get_paises_by_campania($id_campana);

// borro todos los paises de la campana para poder guardar los nuevos si es que los hay.
        $this->campaniaspaises->delete_paises_by_campania($id_campana);

// asocio los paises seleccionados con la campania creada.
        $partes_paises = explode(";", $id_paises);

        $str_paises_campania = '';

        $paises_modificados = FALSE;

        for ($m = 0; $m < sizeof($partes_paises); $m++) {
            if (!empty($partes_paises[$m])) {
                $data_campania_pais = array('id_campania' => $id_campana, 'id_pais' => $partes_paises[$m]);
                $this->campaniaspaises->insert_campania_pais($data_campania_pais);

                $pais = $this->paises->get_pais_by_id($partes_paises[$m]);
                if ($pais) {
                    if (!$this->in_array_field($pais->descripcion, 'descripcion', $paises_campania))
                        $paises_modificados = TRUE;

                    if ($m == (sizeof($partes_paises) - 1) - 2) {
                        $str_paises_campania .= $pais->descripcion . ' y ';
                    } else {
                        $str_paises_campania .= $pais->descripcion . ', ';
                    }
                }
            }
        }

        if (sizeof($partes_paises) - 1 != sizeof($paises_campania))
            $paises_modificados = TRUE;

        $str_paises_campania = substr($str_paises_campania, 0, - 2);

        if ($paises_modificados)
            $this->insert_historial($id_campana, $version, 'Se actualizaron los pa&iacute;ses: ' . trim($str_paises_campania, ', '));

        /*
          // update en DFP
          $this->api->update($id_campana);
         */

// Avisamos a los chicos de medios que se modifico la campania
//$this->crear_ticket_zendesk($id_campana, 'modificar');
// Envio el correo al usuario para informar la modificacion de la campania
        $usuario = $this->users->get_user_by_id($this->tank_auth->get_user_id(), 1);

        $campania = $this->campanias->get_campania_by_id($id_campana);

        $titulo = 'Campana modificada: ' . $nombre;

        $contenido = '<table><tr><td><p style="color:#F385B6;font-weight:bold;font-size:24px;font-family: arial;">MediaFem</p></td></tr>';
        $contenido .= '<tr><td style="font: normal normal 0.9em ' . "'Calibri'" . ', Arial, Helvetica, sans-serif;">';
        $contenido .= '<p>Se modific&oacute; la siguiente campa&ntilde;a:' . "</br>\n</p>";


        $campania_padre = $this->campanias->get_campania_padre($id_campana);

        if ($campania_padre) {
            $data_campania_padre = $this->campanias->get_campania_by_id($campania_padre->id_campania_padre);

            if ($data_campania_padre)
                $contenido .= '<p>Campa&ntilde;a unificada con: ' . $data_campania_padre->nombre . "</br>\n</p>";
        }


        $contenido .= '<p>Nombre de la campa&ntilde;a: ' . $nombre . "</br>\n</p>";

// segmentacion de la campania
        switch ($campania->segmentacion_id) {
            case 1:
                $segmentacion = 'Toda la red';
                break;
            case 2:
                $segmentacion = 'Canales tem&aacute;ticos';
                break;
            case 3:
                $segmentacion = 'Sitios especificos';
                break;
            default:
                $segmentacion = 'Toda la red';
                break;
        }

        $contenido .= '<p>Segmentaci&oacute;n: ' . $segmentacion . "</br>\n</p>";

        // audiencias
        if ($modifico_audiencias) {
            $audiencias = $this->campanias->get_audiencias_by_campania($campania->id);

            if ($audiencias) {
                $contenido .= '<p>Audiencias: Se actualizaron las segmentaciones:</p><ul>';

                foreach ($audiencias as $audiencia)
                    $contenido .= '<li>' . $audiencia->name . '</li>';

                $contenido .= '</ul>';
            } else {
                $contenido .= '<p>Audiencias: Se quitaron las audiencias en esta campa&ntilde;a.</p>';
            }
        }

        $contenido .= '<p>Periodo: desde el ' . MySQLDateToDate($campania->fecha_inicio) . ' al ' . MySQLDateToDate($campania->fecha_fin) . "</br>\n</br>\n</p>";

// retomo los paises pertenecientes a las campa&ntilde;as.
        $campania_paises = $this->campaniaspaises->get_paises_by_campania($campania->id);
        if ($campania_paises) {
            $texto_paises = '';
            $a = 0;
            foreach ($campania_paises as $row) {
                $a++;

                $pais = $this->paises->get_pais_by_id($row->id_pais);
                if ($a == sizeof($campania_paises) - 1) {
                    $texto_paises = $texto_paises . $pais->descripcion . " y ";
                } else {
                    $texto_paises = $texto_paises . $pais->descripcion . ", ";
                }
            }

            $contenido .= '<p>Paises: ' . substr($texto_paises, 0, - 2) . "</br>\n</br>\n</p>";
        }

// obtengo los formatos de la campa&ntilde;a
        $formatos = $this->campaniasformatos->get_formatos_by_campania($campania->id);
        if ($formatos) {
            $texto_formatos = '';
            $a = 0;
            foreach ($formatos as $row) {
                $a++;

                $formato = $this->formatosdfp->get_formato_by_id($row->id_formato);
                if ($a == sizeof($formatos) - 1) {
                    $texto_formatos = $texto_formatos . $formato->descripcion . " y ";
                } else {
                    $texto_formatos = $texto_formatos . $formato->descripcion . ", ";
                }
            }

            $contenido .= '<p>Formatos: ' . substr($texto_formatos, 0, - 2) . "</br>\n</br>\n</p>";
        }

        $contenido .= '<p>Modalidad de compra: ' . strtoupper($campania->modalidad_compra) . "</br>\n</br>\n</p>";

        $contenido .= '<p>Valor ' . strtoupper($campania->modalidad_compra) . ': ' . number_format($campania->valor_unidad, 2, ',', '.') . ' ' . $campania->moneda . "</br>\n</br>\n</p>";

        $contenido .= '<p>Cantidad: ' . $campania->cantidad . "</br>\n</br>\n</p>";


        if ($campania->type_DFP == 'STANDARD') {
            $contenido .= '<p>Inversi&oacute;n total neta: ' . number_format($campania->inversion_neta, 3, ',', '.') . ' ' . $campania->moneda . "</br>\n</br>\n</p>";
        } else {
            $campania->inversion_neta = $campania->inversion_neta * $this->_dateDiff($campania->fecha_inicio, $campania->fecha_fin);
            $contenido .= '<p>Inversi&oacute;n total neta: ' . number_format($campania->inversion_neta, 3, ',', '.') . ' ' . $campania->moneda . "</br>\n</br>\n</p>";
        }

        $contenido .= '</td></tr></table>';

        $usuario_cuentas = $this->admins->get_user_by_id($campania->usuario_cuentas, 1);
        $usuario_medios = $this->admins->get_user_by_id($campania->usuario_implementa, 1);


        if (ENVIRONMENT == 'production') {

            $this->_send_email_to($usuario->email, $titulo, $contenido);

            if (strpos($_SERVER['HTTP_USER_AGENT'], 'testMediaFem') === FALSE) {
                $this->crear_ticket_zendesk($id_campana, 'modificar');

                $this->_send_email_to($usuario_cuentas->email, $titulo, $contenido);
                $this->_send_email_to($usuario_medios->email, $titulo, $contenido);
                $this->_send_email_to('1459773446@mediafem.glip.com', $titulo, $contenido);

                if ($this->tank_auth->get_user_id() == '328') {
                    $this->_send_email_to('asolerp@gmail.com', $titulo, $contenido);
                    $this->_send_email_to('lina@agentedigital.com', $titulo, $contenido);
                    $this->_send_email_to('sandra@agentedigital.com', $titulo, $contenido);
                }
            } else {
                $this->_send_email_to('test.mediafem@gmail.com', $titulo, $contenido);
            }
        }

// inserto un comentario para ordenar todo
        $data_insert_comentario = array(
            'id_campania' => $campania->id,
            'comentario' => HISTORIAL_STRING . ' - Historial - ' . $version,
            'fecha_alta' => date('Y-m-j H:i:s', strtotime('-1 hour', strtotime(date('Y-m-d H:i:s'))))
        );
        $this->campaniashistorial->insert_comentario($data_insert_comentario);


        echo json_encode(array('validate' => TRUE));
    }

    function show_duplicar($id_campania_original) {
// averiguo si no esta en mantenimiento esta opcion
        $this->esta_en_mantenimiento(MENSAJE_MANTENIMIENTO_CREAR_CAMPANIAS_ANUNCIANTES);

// traigo los datos de la tabla campania
        $campania_original = $this->campanias->get_campania_by_id($id_campania_original);

        if ($campania_original) {
// nombre del anunciante
            $anunciante = $this->anunciantes->get_anunciante_adserver_by_id($campania_original->id_anunciante);

// ingreso la nueva campania en la tabla campania
            $cantidad_copias = $campania_original->cantidad_copias;
            if ($cantidad_copias > 0) {
                $cantidad_copias = $cantidad_copias + 1;
                $nombre = $campania_original->nombre . ' (copia ' . $cantidad_copias . ')';
            } else {
                $cantidad_copias = $cantidad_copias + 1;
                $nombre = $campania_original->nombre . ' (copia)';
            }

            $data = array(
                'id_campania' => $campania_original->id,
                'id_anunciante' => $campania_original->id_anunciante,
                'nombre_anunciante' => $anunciante->nombre,
                'nombre' => $nombre,
                'fecha_inicio' => MySQLDateToDate($campania_original->fecha_inicio),
                'fecha_fin' => MySQLDateToDate($campania_original->fecha_fin)
            );

            $this->load->view('campanias_duplicar', $data);
        }
    }

    function show_pausar($id_campania) {
// traigo los datos de la tabla campania
        $campania = $this->campanias->get_campania_by_id($id_campania);

        if ($campania) {
            $data = array(
                'id_campania' => $campania->id,
                'nombre' => $campania->nombre,
                'id_dfp_campania' => $campania->id_orden_dfp
            );

            $this->load->view('campanias_pausar', $data);
        }
    }

    function show_reactivar($id_campania) {
// traigo los datos de la tabla campania
        $campania = $this->campanias->get_campania_by_id($id_campania);

        if ($campania) {
            $data = array(
                'id_campania' => $campania->id,
                'nombre' => $campania->nombre,
                'id_dfp_campania' => $campania->id_orden_dfp
            );

            $this->load->view('campanias_reactivar', $data);
        }
    }

    function pausar() {
        $id_campana = trim($this->input->post('id_campana'));

        if ($this->campanias->update_campania($id_campana, array('estado' => 'PENDIENTE_PAUSA'))) {
            $this->crear_ticket_zendesk($id_campana, 'pausar');

            $campania = $this->campanias->get_campania_by_id($id_campana);

            $version = $campania->historial_version + 1;

            // creo el comentario en el historial.
            $this->insert_historial($id_campana, $version, 'Campa&ntilde;a pausada.');

            // inserto un comentario para ordenar todo
            $data_insert_comentario = array(
                'id_campania' => $id_campana,
                'comentario' => HISTORIAL_STRING . ' - Historial - ' . $version,
                'fecha_alta' => date('Y-m-j H:i:s', strtotime('-1 hour', strtotime(date('Y-m-d H:i:s'))))
            );
            $this->campaniashistorial->insert_comentario($data_insert_comentario);

            echo json_encode(array('validate' => TRUE));
            die();
        } else {
            echo json_encode(array('validate' => FALSE, 'error' => 'No se pudo pausar la campa&ntilde;a.'));
            die();
        }
    }

    function reactivar() {
        $id_campana = trim($this->input->post('id_campana'));
        $id_dfp_campana = trim($this->input->post('id_dfp_campana'));

        if ($this->api->reactivar_campania($id_campana, $id_dfp_campana)) {
            echo json_encode(array('validate' => TRUE));
            die();
        } else {
            echo json_encode(array('validate' => FALSE, 'error' => 'No se pudo reactivar la campa&ntilde;a.'));
            die();
        }
    }

    function duplicar() {
        try {
            $id_campania_duplicada = null;


            $id_campania_original = trim($this->input->post('id_campania'));
            $nombre = trim($this->input->post('nombre'));
            $fecha_inicio = str_replace('/', '-', trim($this->input->post('fecha_inicio')));
            $fecha_fin = str_replace('/', '-', trim($this->input->post('fecha_fin')));

            list($dia_desde, $mes_desde, $anio_desde) = explode("-", $fecha_inicio);
            list($dia_hasta, $mes_hasta, $anio_hasta) = explode("-", $fecha_fin);
            $fecha_inicio = $anio_desde . "-" . $mes_desde . "-" . $dia_desde . ' 00:00:00';
            $fecha_fin = $anio_hasta . "-" . $mes_hasta . "-" . $dia_hasta . ' 23:59:59';

// valido la fecha de inicio
            if (strlen($nombre) <= 0) {
                echo json_encode(array('validate' => FALSE, 'error' => 'Por favor ingrese un nombre para la campa&ntilde;a.'));
                die();
            }

// valido la fecha de inicio
            if (strlen($fecha_inicio) <= 0) {
                echo json_encode(array('validate' => FALSE, 'error' => 'Por favor ingrese la fecha de inicio correspondiente a la campa&ntilde;a.'));
                die();
            }

            $fecha_actual = date('Y-m-d');
// valido que la fecha de inicio no sea menor a la fecha actual
            if ($this->_dateDiff($fecha_inicio, $fecha_actual) > 0) {
                echo json_encode(array('validate' => FALSE, 'error' => 'La fecha de inicio es menor a la fecha actual.'));
                die();
            }

// valido que la fecha de inicio no sea mayor a la fecha de fin
            if ($this->_dateDiff($fecha_inicio, $fecha_fin) < 0) {
                echo json_encode(array('validate' => FALSE, 'error' => 'La fecha de inicio es mayor a la fecha de fin de la campa&ntilde;a.'));
                die();
            }

// valido que la fecha de fin no sea menor a la fecha actual
            if ($this->_dateDiff($fecha_fin, $fecha_actual) > 0) {
                echo json_encode(array('validate' => FALSE, 'error' => 'La fecha de fin es menor a la fecha actual.'));
                die();
            }

// valido que la fecha de fin no sea menor a la fecha de inicio
            if ($this->_dateDiff($fecha_fin, $fecha_inicio) > 0) {
                echo json_encode(array('validate' => FALSE, 'error' => 'La fecha de fin es menor a la fecha de inicio de la campa&ntilde;a.'));
                die();
            }

// valido la fecha de fin
            if (strlen($fecha_fin) <= 0) {
                echo json_encode(array('validate' => FALSE, 'error' => 'Por favor ingrese la fecha de fin correspondiente a la campa&ntilde;a.'));
                die();
            }

// traigo los datos de la tabla campania
            $campania_original = $this->campanias->get_campania_by_id($id_campania_original);

            if ($campania_original) {
// ingreso la nueva campania en la tabla campania
                $cantidad_copias = $campania_original->cantidad_copias;
                if ($cantidad_copias > 0) {
                    $cantidad_copias = $cantidad_copias + 1;
//$nombre = $nombre . ' (copia ' . $cantidad_copias . ')';
                } else {
                    $cantidad_copias = $cantidad_copias + 1;
                }

                $id_cliente = $campania_original->id_cliente;

                $dataInsert = array(
                    'id_cliente' => $id_cliente,
                    'id_anunciante' => $campania_original->id_anunciante,
                    'nombre' => $nombre,
                    'tipo_campania' => $campania_original->tipo_campania,
                    'frecuencia' => $campania_original->frecuencia,
                    'modalidad_compra' => $campania_original->modalidad_compra,
                    'valor_unidad' => $campania_original->valor_unidad,
                    'segmentacion_id' => $campania_original->segmentacion_id,
                    'inversion_bruta' => $campania_original->inversion_bruta,
                    'descuento' => $campania_original->descuento,
                    'comision' => $campania_original->comision,
                    'inversion_neta' => $campania_original->inversion_neta,
                    'cantidad' => $campania_original->cantidad,
                    'fecha_alta' => date('Y-m-d H:i:s'),
                    'fecha_inicio' => $fecha_inicio,
                    'fecha_fin' => $fecha_fin,
                    'forma_completarse' => $campania_original->forma_completarse,
                    'distribucion' => $campania_original->distribucion,
                    'usuario_creador' => $campania_original->usuario_creador,
                    'usuario_cuentas' => $campania_original->usuario_cuentas,
                    'usuario_implementa' => $campania_original->usuario_implementa,
                    'usuario_revisa' => $campania_original->usuario_revisa,
                    'usuario_director' => $campania_original->usuario_director,
                    'ticket_mantis' => $campania_original->ticket_mantis,
                    'estado' => 'PENDIENTE',
                    'empresa_campania' => $campania_original->empresa_campania,
                    'creada_desde_anunciantes' => 1
                );

                $this->campanias->insertar_campania($dataInsert);

// ID de la nueva campania
                $id_campania_duplicada = $this->db->insert_id();

// ingreso los datos en la tabla campanias_control
                $dataCampaniaControl = array('id_campania' => $id_campania_duplicada);
                $this->campaniascontrol->insert_control($dataCampaniaControl);
            }

            if ($id_campania_duplicada != null) {

// PAISES **********************************************************
// traigo los datos de la tabla campanias_paises
                $campania_original_paises = $this->campaniaspaises->get_paises_by_campania($id_campania_original);

                if ($campania_original_paises) {
// ingreso en la tabla campanias_paises los paises para la campania duplicada.
                    foreach ($campania_original_paises as $campania_original_pais)
                        $this->campaniaspaises->insert_campania_pais(array('id_campania' => $id_campania_duplicada, 'id_pais' => $campania_original_pais->id_pais));
                }

// CANALES TEMATICOS ***********************************************
// traigo los datos de la tabla campanias_canales_tematicos
                if ($campania_original->segmentacion_id == 2) {
                    $campania_original_canales = $this->campaniascanalestematicos->get_canales_tematicos_by_campania($id_campania_original);

                    if ($campania_original_canales) {
// ingreso en la tabla campanias_canales_tematicos para la campania duplicada.
                        foreach ($campania_original_canales as $campania_original_canal)
                            $this->campaniascanalestematicos->insert_campania_canal_tematico(array('id_campania' => $id_campania_duplicada, 'id_canal_tematico' => $campania_original_canal->id_canal_tematico));
                    }
                }

// SITIOS **********************************************************
// traigo los datos de la tabla campanias_sitios
                $campania_original_sitios = $this->campaniassitios->get_sitios_by_campania($id_campania_original);

                if ($campania_original_sitios) {
// ingreso en la tabla campanias_sitios para la campania duplicada.
                    foreach ($campania_original_sitios as $campania_original_sitio)
                        $this->campaniassitios->insert_campania_sitio(array('id_campania' => $id_campania_duplicada, 'id_sitio' => $campania_original_sitio->id_sitio));
                }

// FORMATOS ********************************************************
// traigo los datos de la tabla campanias_formatos
                $campania_original_formatos = $this->campaniasformatos->get_formatos_by_campania($id_campania_original);

                if ($campania_original_formatos) {
                    foreach ($campania_original_formatos as $campania_original_formato) {
                        $dataInsertFormato = array(
                            'id_campania' => $id_campania_duplicada,
                            'id_formato' => $campania_original_formato->id_formato,
                            'monto' => $campania_original_formato->monto,
                            'id_modalidad_compra' => $campania_original_formato->id_modalidad_compra,
                            'cantidad' => $campania_original_formato->cantidad,
                            'pagina_destino' => $campania_original_formato->pagina_destino
                        );

// ingreso en la tabla campanias_formatos para la campania duplicada.
                        $this->campaniasformatos->insert_campania_formato($dataInsertFormato);
                    }
                }

// SITIOS - FORMATOS ***********************************************
// traigo los datos de la tabla campanias_sitios_formatos
                $campania_original_sitios_formatos = $this->campaniassitiosformatos->get_sitios_espacios_formato_por_campania($id_campania_original);

                if ($campania_original_sitios_formatos) {
                    foreach ($campania_original_sitios_formatos as $campania_original_sitio_formato) {
                        $dataInsertSitioFormato = array(
                            'id_campania' => $id_campania_duplicada,
                            'id_sitio' => $campania_original_sitio_formato->id_sitio,
                            'id_formato' => $campania_original_sitio_formato->id_formato
                        );

// ingreso en la tabla campanias_sitios_formatos para la campania duplicada.
                        $this->campaniassitiosformatos->insert_campania_sitio_formato($dataInsertSitioFormato);
                    }
                }

// MATERIALES ******************************************************
// traigo los datos de la tabla archivos_creatividades
                $campania_original_materiales = $this->archivoscreatividades->get_archivos($id_campania_original);

                if ($campania_original_materiales) {
                    foreach ($campania_original_materiales as $campania_original_material) {
                        $dataInsertMaterial = array(
                            'id_campania' => $id_campania_duplicada,
                            'nombre_real' => $campania_original_material->nombre_real,
                            'nombre_archivo' => $campania_original_material->nombre_archivo,
                            'mime' => $campania_original_material->mime
                        );

// ingreso en la tabla campanias_sitios_formatos para la campania duplicada.
                        $this->archivoscreatividades->insert_archivo($dataInsertMaterial);
                    }
                }


// AUDIENCIAS ******************************************************
                if ($campania_original->empresa_campania == 1) {
                    // traigo las audiencias de la campania original
                    $audiencias_originales = $this->campanias->get_audiencias_by_campania($id_campania_original);

                    if ($audiencias_originales) {
                        foreach ($audiencias_originales as $audiencia_original) {
                            $dataInsertAudiencia = array(
                                'id_campania' => $id_campania_duplicada,
                                'id_audiencia' => $audiencia_original->id_audiencia,
                                'action' => $audiencia_original->action,
                            );

                            $this->campanias->insertar_audiencia_campania($dataInsertAudiencia);
                        }
                    }
                }

// Finalizo el alta de la campania

                $empresa_campania = 'mf';

                if ($campania_original->empresa_campania == 1)
                    $empresa_campania = 'adtk';

                $alta_dfp = $this->crear_campania_AppNexus($id_campania_duplicada, $empresa_campania);
                if ($alta_dfp) {
                    $this->campanias->update_campania($id_campania_duplicada, array('alta_finalizada' => 1, 'cantidad_copias' => $cantidad_copias));
                    $this->campanias->update_campania($id_campania_original, array('cantidad_copias' => $cantidad_copias));

                    $this->crear_ticket_zendesk($id_campania_duplicada, 'duplicar');

// si se cargo todo correcto en DFP entonces cobro la campa&ntilde;a al anunciante
                    $camp = $this->campanias->get_campania_by_id($id_campania_duplicada);
                    $this->historial_alta_campania($camp, $nombre);

                    // inserto un comentario para ordenar todo
                    $data_insert_comentario = array(
                        'id_campania' => $id_campania_duplicada,
                        'comentario' => HISTORIAL_STRING . ' - Historial - ' . 1,
                        'fecha_alta' => date('Y-m-j H:i:s', strtotime('-1 hour', strtotime(date('Y-m-d H:i:s'))))
                    );
                    $this->campaniashistorial->insert_comentario($data_insert_comentario);

                    if ($id_cliente)
                        $this->cobrar_campania($id_campania_duplicada);

                    /*
                      // creo un nuevo movimiento de saldo descontando el balance
                      $data_insert_pago = array(
                      'id_anunciante' => $this->tank_auth->get_user_id(),
                      'id_campania' => $id_campania_duplicada,
                      'debito' => $importe_a_cobrar,
                      'balance' => ($this->user_data->limite_de_compra - $importe_a_cobrar),
                      'moneda' => $this->user_data->moneda,
                      'descripcion' => "Pago inicial de campa&ntilde;a " . $nombre,
                      'tipo_de_pago' => '2'
                      );

                      if (!$this->anunciantessaldos->insert_anunciante_saldo($data_insert_pago)) {
                      echo json_encode(array('validate' => FALSE, 'error' => 'Ocurri&oacute; un error al intentar cobrar la campa&ntilde;a.'));
                      die();
                      }

                      // ACA LO JODIDO! ******************************************************

                      $limite_de_compra = $this->user_data->limite_de_compra;
                      $saldo_prepago = $this->user_data->saldo_prepago;
                      $saldo_prestamo = $this->user_data->saldo_prestamo;

                      // descuento el total de la campa&ntilde;a del saldo prepago
                      $restante_a_cobrar = $importe_a_cobrar - $saldo_prepago;

                      // si todabia queda dinero a cobrar se lo descuento al prestamo
                      if ($restante_a_cobrar > 0) {
                      // cuanto consumio del prepago y prestamo la campa&ntilde;a
                      $consumo_prepago = $saldo_prepago;
                      $consumo_prestamo = $restante_a_cobrar;

                      // no tiene mas saldo prepago
                      $saldo_prepago = 0;

                      $saldo_prestamo = $saldo_prestamo - $restante_a_cobrar;
                      } else { // tiene mas plata de prepago que lo consumido en la campa&ntilde;a
                      // cuanto consumio del prepago y prestamo la campa&ntilde;a
                      $consumo_prepago = $importe_a_cobrar;
                      $consumo_prestamo = 0;

                      $saldo_prepago = 0 - $restante_a_cobrar;
                      }

                      // actualizo los saldos del anunciante
                      $data_update = array(
                      'limite_de_compra' => $limite_de_compra - $importe_a_cobrar,
                      'saldo_prepago' => $saldo_prepago,
                      'saldo_prestamo' => $saldo_prestamo
                      );

                      $this->anunciantes->update_anunciante($this->tank_auth->get_user_id(), $data_update);

                      // guardo en el registro de la campa&ntilde;a cuanto consumio de cada saldo
                      $update_campania = array(
                      'alta_finalizada' => 1,
                      'consumo_saldo_prepago' => $consumo_prepago,
                      'consumo_saldo_prestamo' => $consumo_prestamo,
                      'moneda' => $this->user_data->moneda
                      );

                      $this->campanias->update_campania($id_campania_duplicada, $update_campania);

                      // FINAL DE LO JODIDO! *************************************************
                     */
                    echo json_encode(array('validate' => TRUE));
                    die();
                } else {
                    echo json_encode(array('validate' => FALSE, 'error' => 'ERROR: Al crear la campa&ntilde;a en el adserver.'));
                    die();
                }
            } else {
                echo json_encode(array('validate' => FALSE, 'error' => 'ERROR: Al duplicar la campa&ntilde;a en la base de datos.'));
                die();
            }

            die();
        } catch (Exception $ex) {
            echo json_encode(array('validate' => FALSE, 'error' => 'ERROR: ' . $ex->getMessage()));
            die();
        }
    }

    function cobrar_campania($id_campania) {

        $campania = $this->campanias->get_campania_by_id($id_campania);
        $usuario = $this->users->get_user_by_id($this->tank_auth->get_user_id(), 1);

        $id_cliente = $campania->id_cliente;
        $nombre = $campania->nombre;
        $dias_campania = $this->_dateDiff($campania->fecha_inicio, $campania->fecha_fin);
        $importe_a_cobrar = ($campania->inversion_neta * $dias_campania);

        $data_insert_pago = array(
            'id_cliente' => $id_cliente,
            'descripcion' => "Pago inicial de campa&ntilde;a " . $nombre,
            'importe' => '-' . $importe_a_cobrar,
            'tipo' => '0',
            'tipo_saldo' => ($usuario->creado_desde_sitio) ? '0' : '1',
            'cargado_por' => $this->tank_auth->get_user_id()
        );

        if (!$this->clientes_model->insert_cliente_saldo($data_insert_pago)) {
            echo json_encode(array('validate' => FALSE, 'error' => 'Ocurri&oacute; un error al intentar cobrar la campa&ntilde;a.'));
            die();
        }
    }

    function mostrar_cargar_saldo($secods) {
        echo '<div class="alerta">No posee saldo suficiente para crear campa&ntilde;as. Puede cargar saldo <a href="' . base_url() . 'micuenta#mis_saldos">aqu&iacute;</a>.</div>';
    }

    function obtener_campanias($seconds) {
        $anunciantes_asociados = $this->anunciantes->get_anunciantes_asociados_by_id($this->tank_auth->get_user_id());

        $usuario = $this->users->get_user_by_id($this->tank_auth->get_user_id(), 1);

        $campanias = $publinotas = NULL;

        if ($anunciantes_asociados) {
            foreach ($anunciantes_asociados as $anunciante_asociado) {
                $campania = $this->campanias->get_campanias_by_anunciante($anunciante_asociado->id_anunciante_adserver);

                if ($campania) {
                    foreach ($campania as $row) {

//$row->puede_duplicar = $this->_dateDiff(date('Y-m-d'), date("Y-m-d", strtotime(trim($row->fecha_inicio))));
                        /* if ($row->id_orden_dfp || $row->id_lineItem_appnexus) {

                          if (!$row->id_lineItem_appnexus) {
                          $status = $this->campanias->get_consumido_by_campania($row->fecha_inicio, $row->fecha_fin, $row->id_orden_dfp);
                          if ($status) {
                          $row->consumido = $this->cambiar_moneda($status->consumido);
                          } else {
                          $row->consumido = '-';
                          }
                          } else {
                          $status = $this->campanias->get_consumido_by_campania_appnexus($row->fecha_inicio, $row->fecha_fin, $row->id_lineItem_appnexus);

                          if ($status) {
                          $row->consumido = $this->cambiar_moneda($status->consumido);
                          } else {
                          $row->consumido = '-';
                          }
                          }
                          } else {
                          $row->consumido = '-';
                          } */
                        $row->consumido = '-';
                        $campanias[] = $row;
                    }
                }

                $publinota = $this->publinotas->get_publinotas_by_anunciante($anunciante_asociado->id_anunciante_adserver);

                if ($publinota) {
                    foreach ($publinota as $row)
                        $publinotas[] = $row;
                }
            }

            if ($campanias == NULL) {
                $this->load->view('tbl_mis_campanias_NULL');
                return true;
            }

            $data['publinotas'] = $publinotas;
            $data['campanias'] = $campanias;
            $data['agencia'] = $usuario->agencia;
            $data['usuario'] = $usuario;

            if ($usuario->creado_desde_sitio) {
                $data['puede_modificar'] = 1;
            } else {
                $data['puede_modificar'] = $usuario->modificar_duplicar_campanias;
            }

            if (sizeof($anunciantes_asociados) >= 1)
                $this->load->view('tbl_mis_campanias_anunciantes', $data);
            else
                $this->load->view('tbl_mis_campanias', $data);
        }else {
            $this->load->view('tbl_mis_campanias_NULL');
        }
    }

    function campania_padre($id_campania) {
        $campania_padre = $this->campanias->get_campania_padre($id_campania);

        return $campania_padre;
    }

    function campanias_padres() {
        $anunciantes_asociados = $this->anunciantes->get_anunciantes_asociados_by_id($this->tank_auth->get_user_id());

        $usuario = $this->users->get_user_by_id($this->tank_auth->get_user_id(), 1);

        $campanias = $publinotas = NULL;

        if ($anunciantes_asociados) {
            foreach ($anunciantes_asociados as $anunciante_asociado) {
                $campania = $this->campanias->get_campanias_by_anunciante_periodicas($anunciante_asociado->id_anunciante_adserver);

                if ($campania) {
                    foreach ($campania as $row) {
                        if ($row->campania_padre == 1)
                            $campanias[] = $row;
                    }
                }

                $publinota = $this->publinotas->get_publinotas_by_anunciante($anunciante_asociado->id_anunciante_adserver);

                if ($publinota) {
                    foreach ($publinota as $row) {
                        if ($row->campania_padre == 1)
                            $campanias[] = $row;
                    }
                }
            }
        }

        return $campanias;
    }

    function status_campanias($interval = 'today', $estado = '0') {
        $anunciantes_asociados = $this->anunciantes->get_anunciantes_asociados_by_id($this->tank_auth->get_user_id());

        $data['campanias'] = NULL;

        if ($interval == "today") {
            $fecha_analisis = date('Y-m-d 00:00:00');
            $fecha_datos = date('Y-m-d 00:00:00', strtotime("-1 day"));

            $fecha_desde = date('Y-m-d 00:00:00', strtotime("-1 day"));
            $fecha_hasta = date('Y-m-d 23:59:59', strtotime("-1 day"));
        } elseif ($interval == "yesterday") {
            $fecha_analisis = date('Y-m-d 00:00:00', strtotime("-1 day"));
            $fecha_datos = date('Y-m-d 00:00:00', strtotime("-2 day"));

            $fecha_desde = date('Y-m-d 00:00:00', strtotime("-2 day"));
            $fecha_hasta = date('Y-m-d 23:59:59', strtotime("-2 day"));
        }

        if ($anunciantes_asociados) {
            foreach ($anunciantes_asociados as $anunciante_asociado) {
                $campania = $this->campanias->get_campanias_by_anunciante($anunciante_asociado->id_anunciante_adserver);

                if ($campania) {
                    $anunciantes_appnexus_arr = $anunciantes_appnexus_str = NULL;

                    foreach ($campania as $row) {
                        $status = FALSE;

                        if ($row->id_orden_dfp) {
                            if ($estado == '0') {
                                $status = $this->campanias->get_status_by_campania($fecha_desde, $fecha_hasta, $row->id_orden_dfp);
                            } else {
                                $status = $this->campanias->get_status_by_campania_and_estado($fecha_desde, $fecha_hasta, $row->id_orden_dfp, $estado);
                            }

                            if ($status)
                                $data['campanias'][] = $status;
                        }

                        if ($row->id_lineItem_appnexus) {
                            if (!isset($anunciantes_appnexus_arr[$row->id_anunciante]))
                                $anunciantes_appnexus_arr[$row->id_anunciante] = $row->id_anunciante;
                        }
                    }

                    if ($anunciantes_appnexus_arr) {
                        $status = FALSE;

                        foreach ($anunciantes_appnexus_arr as $anunciante_appnexus) {
                            $anunciante_adserver = $this->anunciantes->get_anunciante_adserver_by_id($anunciante_appnexus);
                            $anunciantes_appnexus_str .= $anunciante_adserver->id_appnexus . ',';
                        }

                        $anunciantes_appnexus_str = trim($anunciantes_appnexus_str, ',');

                        if ($estado == '0') {
                            $status = $this->campanias->get_status_by_campania($fecha_desde, $fecha_hasta, $anunciante_adserver->id_appnexus);
                        } else {
                            $status = $this->campanias->get_status_by_campania_and_estado($fecha_desde, $fecha_hasta, $anunciante_adserver->id_appnexus, $estado);
                        }

                        if ($status)
                            $data['campanias'][] = $status;
                    }
                }




                /*
                  else if ($row->id_lineItem_appnexus) {
                  $anunciante_adserver = $this->anunciantes->get_anunciante_adserver_by_id($row->id_anunciante);

                  $status = $this->campanias->get_status_by_campania($fecha_desde, $fecha_hasta, $anunciante_adserver->id_appnexus);
                  }

                  if ($status && !in_array($data['campanias'], $row_status) ) {
                  foreach ($status as $row_status)
                  $data['campanias'][] = $row_status;
                  }
                 */
            }
        }

        if ($data['campanias'] != NULL) {
            $this->load->view('campanias_status', $data);
        } else {
            echo '<div class="alerta">No se encontro ninguna campa&ntilde;a.</div>';
        }
    }

    function nueva() {
        if (!$this->tank_auth->is_logged_in())
            redirect('/auth/login');

        $data['segmentaciones'] = $this->get_segmentaciones();
        $data['canales_tematicos'] = $this->get_canales_tematicos();
        $data['formatos'] = $this->get_formatos();

        $usuario = $this->users->get_user_by_id($this->tank_auth->get_user_id(), 1);
        $data['creado_desde_sitio'] = $usuario->creado_desde_sitio;
        $data['id_anunciante'] = $this->tank_auth->get_user_id();

        $tarjeta_usuario = $this->usuariostarjetasdecredito->get_tarjetas_de_credito_por_usuario($this->tank_auth->get_user_id());

        if ($usuario->empresa) {
            if ($usuario->name) {
                $nombre = $usuario->name . ' (' . $usuario->empresa . ')';
            } else {
                $nombre = $usuario->empresa;
            }
        } else {
            $nombre = $usuario->name;
        }

        $data['nombre_anunciante'] = $nombre;

        $data['tarjeta_usuario'] = $tarjeta_usuario;

        $this->load->view('campanias_nueva', $data);
    }

    function valor_minimo($empresa = 'mf') {
        $id_paises = $this->input->post('id_paises');
        $modalidad = $this->input->post('modalidad');
        $formatos = $this->input->post('formatos');
        $id_segmentacion = $this->input->post('segmentacion');

        $id_paises = explode(';', $id_paises);

        $str_paises = '';
        foreach ($id_paises as $id_pais) {
            if ($id_pais != '')
                $str_paises .= "'" . $id_pais . "'" . ',';
        }

        $str_paises = trim($str_paises, ',');

        $formatos = explode(';', $formatos);

        $str_formatos = '';
        foreach ($formatos as $formato) {
            if ($formato != '')
                $str_formatos .= "'" . $formato . "'" . ',';
        }

        $str_formatos = trim($str_formatos, ',');

        if ($empresa == 'mf') {
            $tarifario = $this->tarifarios->get_valor_minimo($modalidad, $id_segmentacion, $str_formatos, $str_paises);

            if ($tarifario) {
// si no existe tarifario para ese formato traigo el valor minimo de la constante
                if ($tarifario[0]->valor_minimo == 0) {
                    if ($empresa == 'mf') {

                        //echo "1";

                        $inversion_cpc_cpm = $this->constants->get_constant_by_id(20);
                    } else {

                        //echo "2";

                        $inversion_cpc_cpm = $this->constants->get_constant_by_id(29);
                    }

                    $tarifario = $inversion_cpc_cpm->value;
                } else {

                    //echo "3";
                    $tarifario = $tarifario[0]->valor_minimo;
                }
            } else {
                if ($empresa == 'mf') {
                    //echo "4";
                    $inversion_cpc_cpm = $this->constants->get_constant_by_id(20);
                } else {
                    //echo "5";
                    $inversion_cpc_cpm = $this->constants->get_constant_by_id(29);
                }

                //echo "6";
                $tarifario = $inversion_cpc_cpm->value;
            }
        } else {
            $inversion_cpc_cpm = $this->constants->get_constant_by_id(29);

            $tarifario = $inversion_cpc_cpm->value;
        }


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

                $inversion_cpc_cpm = $tarifario * $cotizacion;
            } else {
                return FALSE;
            }
        } else {
            $inversion_cpc_cpm = $tarifario;
        }

        if ($this->user_data->notacion == 0) {
            $inversion_cpc_cpm = number_format($inversion_cpc_cpm, 2, '.', ',');
        } else if ($this->user_data->notacion == 1) {
            $inversion_cpc_cpm = number_format($inversion_cpc_cpm, 2, ',', '.');
        }

        echo json_encode(array('valor_minimo' => $inversion_cpc_cpm));
    }

    function tipo_campania() {
        $this->load->view('campania_seleccion_tipo');
    }

    function crear_appnexus() {
        if (!$this->tank_auth->is_logged_in())
            redirect('/auth/login');

// averiguo si no esta en mantenimiento esta opcion
        $this->esta_en_mantenimiento(MENSAJE_MANTENIMIENTO_CREAR_CAMPANIAS_ANUNCIANTES);

        $inversion_neta = $this->constants->get_constant_by_id(19);
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

                $data['inversion_neta'] = $inversion_neta->value * $cotizacion;
            } else {
                return FALSE;
            }
        } else {
            $data['inversion_neta'] = $inversion_neta->value;
        }

        $inversion_cpc_cpm = $this->constants->get_constant_by_id(29);
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

                $data['inversion_cpc_cpm'] = $inversion_cpc_cpm->value * $cotizacion;
            } else {
                return FALSE;
            }
        } else {
            $data['inversion_cpc_cpm'] = $inversion_cpc_cpm->value;
        }

        $data['segmentaciones'] = $this->get_segmentaciones();
        $data['canales_tematicos'] = $this->get_canales_tematicos();
        $data['formatos'] = $this->get_formatos();

        $usuario = $this->users->get_user_by_id($this->tank_auth->get_user_id(), 1);
        $data['creado_desde_sitio'] = $usuario->creado_desde_sitio;
        $data['habilitar_descuentos'] = $usuario->habilitar_descuentos;
        $data['id_anunciante'] = $this->tank_auth->get_user_id();

        $tarjeta_usuario = $this->usuariostarjetasdecredito->get_tarjetas_de_credito_por_usuario($this->tank_auth->get_user_id());

        if ($usuario->empresa) {
            if ($usuario->name) {
                $nombre = $usuario->name . ' (' . $usuario->empresa . ')';
            } else {
                $nombre = $usuario->empresa;
            }
        } else {
            $nombre = $usuario->name;
        }

        $anunciantes_adserver = $this->users->get_anunciantes_app_by_id($this->tank_auth->get_user_id());

        $data['clientes'] = $this->clientes_model->getAll($this->tank_auth->get_user_id());
        $data['anunciantes_adserver'] = $anunciantes_adserver;
        $data['nombre_anunciante'] = $nombre;
        $data['tarjeta_usuario'] = $tarjeta_usuario;
        $data['tarjeta_usuario'] = $tarjeta_usuario;
        $data['usuarios'] = $this->admins->get_users();

        $data['limite_de_compra'] = $usuario->limite_de_compra;

        $data['no_gastar_saldo'] = $usuario->no_gastar_saldo;
        $data['type'] = $usuario->carga_inversion_total;

        $data['audiencias'] = $this->audiencias->get_by_anunciante($this->tank_auth->get_user_id());

        $formatos = $this->formatosdfp->get_tradicionales();

        if ($formatos) {
            $texto_formatos = '';
            $a = 0;
            foreach ($formatos as $row) {
                $a++;
                if ($a == sizeof($formatos) - 1) {
                    $texto_formatos = $texto_formatos . $row->descripcion . " y ";
                } else {
                    $texto_formatos = $texto_formatos . $row->descripcion . ", ";
                }
            }

            $data['formatos_tradicionales'] = substr($texto_formatos, 0, - 2);
        }

        $this->load->view('alta_campania_appnexus', $data);
    }

    function crear() {
        if (!$this->tank_auth->is_logged_in())
            redirect('/auth/login');

// averiguo si no esta en mantenimiento esta opcion
        $this->esta_en_mantenimiento(MENSAJE_MANTENIMIENTO_CREAR_CAMPANIAS_ANUNCIANTES);


        $inversion_neta = $this->constants->get_constant_by_id(19);
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
                $data['inversion_neta'] = $inversion_neta->value * $cotizacion;
            } else {
                return FALSE;
            }
        } else {
            $data['inversion_neta'] = $inversion_neta->value;
        }

        $inversion_cpc_cpm = $this->constants->get_constant_by_id(20);
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

                $data['inversion_cpc_cpm'] = $inversion_cpc_cpm->value * $cotizacion;
            } else {
                return FALSE;
            }
        } else {
            $data['inversion_cpc_cpm'] = $inversion_cpc_cpm->value;
        }
        $data['segmentaciones'] = $this->get_segmentaciones();
        $data['canales_tematicos'] = $this->get_canales_tematicos();
        $data['formatos'] = $this->get_formatos();

        $usuario = $this->users->get_user_by_id($this->tank_auth->get_user_id(), 1);
        $data['creado_desde_sitio'] = $usuario->creado_desde_sitio;
        $data['habilitar_descuentos'] = $usuario->habilitar_descuentos;
        $data['id_anunciante'] = $this->tank_auth->get_user_id();

        $tarjeta_usuario = $this->usuariostarjetasdecredito->get_tarjetas_de_credito_por_usuario($this->tank_auth->get_user_id());

        if ($usuario->empresa) {
            if ($usuario->name) {
                $nombre = $usuario->name . ' (' . $usuario->empresa . ')';
            } else {
                $nombre = $usuario->empresa;
            }
        } else {
            $nombre = $usuario->name;
        }
        $anunciantes_adserver = $this->users->get_anunciantes_app_by_id($this->tank_auth->get_user_id());

        $data['clientes'] = $this->clientes_model->getAll($this->tank_auth->get_user_id());

        $data['anunciantes_adserver'] = $anunciantes_adserver;
        $data['nombre_anunciante'] = $nombre;
        $data['tarjeta_usuario'] = $tarjeta_usuario;
        $data['usuarios'] = $this->admins->get_users();

        $data['limite_de_compra'] = $usuario->limite_de_compra;

        $data['permitir_unificar_campanias'] = $usuario->permitir_unificar_campanias;

        $data['no_gastar_saldo'] = $usuario->no_gastar_saldo;
        $data['type'] = $usuario->carga_inversion_total;
        $formatos = $this->formatosdfp->get_tradicionales();

        if ($formatos) {
            $texto_formatos = '';
            $a = 0;
            foreach ($formatos as $row) {
                $a++;
                if ($a == sizeof($formatos) - 1) {
                    $texto_formatos = $texto_formatos . $row->descripcion . " y ";
                } else {
                    $texto_formatos = $texto_formatos . $row->descripcion . ", ";
                }
            }

            $data['formatos_tradicionales'] = substr($texto_formatos, 0, - 2);
        }

        $data['campanias_padres'] = $this->campanias_padres();
        $this->load->view('alta_campania', $data);
    }

    function ver_publinota($id_publinota) {
        $publinota = $this->publinotas->get_publinota_by_id($id_publinota);

        if ($publinota) {
// selecciono todos los sitios correspondientes a la camapania
            $sitios = $this->publinotassitios->get_sitios_by_publinota($publinota->Id);

            if ($sitios) {
                foreach ($sitios as $site) {
// de cada sitio obtengo su nombre y lo guardo en el array para pasar a la vista.
                    $sitio = $this->sites->get_site_by_id($site->id_sitio);
                    $nombre_sitio = $sitio->nombre_appnexus;
                    if ($nombre_sitio == '')
                        $nombre_sitio = $sitio->nombre_dfp;

                    $arr_sitios[] = array('nombre' => $nombre_sitio, 'estado' => $site->estado, 'url' => $site->url_publinota);
                }
                $data['sitios'] = $arr_sitios;
            }else {
                $data['sitios'] = 'No se encontraron sitios asociados a la campa&ntilde;a.';
            }

            $publinota->fecha_inicio = MySQLDateToDate($publinota->fecha_inicio);
            $publinota->fecha_fin = MySQLDateToDate($publinota->fecha_fin);

            if ($this->user_data->notacion == 0) {
                $publinota->precio_total = number_format($publinota->precio * sizeof($arr_sitios), 2, '.', ',');
                $publinota->precio = number_format($publinota->precio, 2, '.', ',');
            } else if ($this->user_data->notacion == 1) {
                $publinota->precio_total = number_format($publinota->precio * sizeof($arr_sitios), 2, ',', '.');
                $publinota->precio = number_format($publinota->precio, 2, ',', '.');
            }

            $publinota->name_anunciante = $this->anunciantes->get_anunciante_adserver_by_id($publinota->Id_anunciante);
            $publinota->name_anunciante = $publinota->name_anunciante->nombre;

            $data['publinota_precio_total'] = $publinota->precio_total;
            $data['publinota'] = $publinota;

            $this->load->view('campanias_ver_publinotas', $data);
        } else {
            echo '<div class="alerta">No se encuentra la publinota indicada.</div>';
        }
    }

    function ver_estado_publinota($id_publinota) {
        $publinota = $this->publinotas->get_publinota_by_id($id_publinota);

        if ($publinota) {
// selecciono todos los sitios correspondientes a la camapania
            $sitios = $this->publinotassitios->get_sitios_by_publinota($publinota->Id);

            if ($sitios) {
                foreach ($sitios as $site) {
// de cada sitio obtengo su nombre y lo guardo en el array para pasar a la vista.
                    $sitio = $this->sites->get_site_by_id($site->id_sitio);
                    $nombre_sitio = $sitio->nombre_appnexus;
                    if ($nombre_sitio == '')
                        $nombre_sitio = $sitio->nombre_dfp;

                    $arr_sitios[] = array('nombre' => $nombre_sitio, 'id' => $site->id_sitio, 'estado' => $site->estado, 'estado_anunciante' => $site->estado_anunciante, 'url' => $site->url_publinota);
                }
                $data['sitios'] = $arr_sitios;
            }else {
                $data['sitios'] = 'No se encontraron sitios asociados a la campa&ntilde;a.';
            }

            $publinota->fecha_inicio = MySQLDateToDate($publinota->fecha_inicio);
            $publinota->fecha_fin = MySQLDateToDate($publinota->fecha_fin);

            if ($this->user_data->notacion == 0) {
                $publinota->precio_total = number_format($publinota->precio * sizeof($arr_sitios), 2, '.', ',');
                $publinota->precio = number_format($publinota->precio, 2, '.', ',');
            } else if ($this->user_data->notacion == 1) {
                $publinota->precio_total = number_format($publinota->precio * sizeof($arr_sitios), 2, ',', '.');
                $publinota->precio = number_format($publinota->precio, 2, ',', '.');
            }

            $publinota->name_anunciante = $this->anunciantes->get_anunciante_adserver_by_id($publinota->Id_anunciante);
            $publinota->name_anunciante = $publinota->name_anunciante->nombre;

            $data['publinota_precio_total'] = $publinota->precio_total;
            $data['publinota'] = $publinota;

            $this->load->view('campanias_ver_estado_publinotas', $data);
        } else {
            echo '<div class="alerta">No se encuentra la publinota indicada.</div>';
        }
    }

    function aceptar_publinota() {
        $id_sitio = $this->input->post('id_sitio');
        $id_publinota = $this->input->post('id_publinota');
        $usuario = $this->users->get_user_by_id($this->tank_auth->get_user_id(), 1);

        $this->publinotassitios->update($id_sitio, $id_publinota, array('estado_anunciante' => 'A'));

// descuento del saldo del anunciante lo que vale un sitio fijandome si corresponde a prepago o prestamo
        $publinota = $this->publinotas->get_publinota_by_id($id_publinota);
        if ($publinota) {
// selecciono todos los sitios correspondientes a la camapania
            $sitios = $this->publinotassitios->get_sitios_by_publinota($publinota->Id);

            $cantidad_sitios = 0;

            if ($sitios)
                $cantidad_sitios = sizeof($sitios);

            $importe_a_cobrar = $precio_por_sitio = $publinota->precio;

            if ($publinota->id_cliente) {

                $data_insert_pago = array(
                    'id_cliente' => $publinota->id_cliente,
                    'descripcion' => "Pago por Publinota",
                    'importe' => '-' . $importe_a_cobrar,
                    'tipo' => '0',
                    'tipo_saldo' => ($usuario->creado_desde_sitio) ? '0' : '1',
                    'cargado_por' => $this->tank_auth->get_user_id()
                );

                if (!$this->clientes_model->insert_cliente_saldo($data_insert_pago)) {
                    echo json_encode(array('validate' => FALSE, 'error' => 'Ocurri&oacute; un error al intentar cobrar la campa&ntilde;a.'));
                    die();
                }
            }
            /*
              // creo un nuevo movimiento de saldo descontando el balance
              $data_insert_pago = array(
              'id_anunciante' => $this->tank_auth->get_user_id(),
              'id_campania' => $id_publinota,
              'debito' => $importe_a_cobrar,
              'balance' => ($this->user_data->limite_de_compra - $importe_a_cobrar),
              'moneda' => $this->user_data->moneda,
              'descripcion' => "Pago por la aceptaci&oacute;n de sitio en la publinota " . $publinota->nombre,
              'tipo_de_pago' => '2',
              'tipo_campania' => 'publinota'
              );

              if (!$this->anunciantessaldos->insert_anunciante_saldo($data_insert_pago)) {
              echo json_encode(array('validate' => FALSE, 'error' => 'Ocurri&oacute; un error al intentar cobrar la publinota.'));
              die();
              }

              // ACA LO JODIDO! ******************************************************

              $limite_de_compra = $this->user_data->limite_de_compra;
              $saldo_prepago = $this->user_data->saldo_prepago;
              $saldo_prestamo = $this->user_data->saldo_prestamo;

              // descuento el total de la campa&ntilde;a del saldo prepago
              $restante_a_cobrar = $importe_a_cobrar - $saldo_prepago;

              // si todabia queda dinero a cobrar se lo descuento al prestamo
              if ($restante_a_cobrar > 0) {
              // cuanto consumio del prepago y prestamo la campa&ntilde;a
              $consumo_prepago = $saldo_prepago;
              $consumo_prestamo = $restante_a_cobrar;

              // no tiene mas saldo prepago
              $saldo_prepago = 0;

              $saldo_prestamo = $saldo_prestamo - $restante_a_cobrar;
              } else { // tiene mas plata de prepago que lo consumido en la campa&ntilde;a
              // cuanto consumio del prepago y prestamo la campa&ntilde;a
              $consumo_prepago = $importe_a_cobrar;
              $consumo_prestamo = 0;

              $saldo_prepago = 0 - $restante_a_cobrar;
              }

              // actualizo los saldos del anunciante
              $data_update = array(
              'limite_de_compra' => $limite_de_compra - $importe_a_cobrar,
              'saldo_prepago' => $saldo_prepago,
              'saldo_prestamo' => $saldo_prestamo
              );

              $this->anunciantes->update_anunciante($this->tank_auth->get_user_id(), $data_update);

              // guardo en el registro de la campa&ntilde;a cuanto consumio de cada saldo
              $update_campania = array(
              'consumo_saldo_prepago' => $consumo_prepago,
              'consumo_saldo_prestamo' => $consumo_prestamo
              );
             */
            //$this->publinotas->update($id_publinota, $update_campania);
// FINAL DE LO JODIDO! *************************************************
        }

// le doy el %50 de lo descontado al sitio *********************************
// averiguo el id del usuario del sitio
        $sitio = $this->sites->get_site_by_id($id_sitio);
        if ($sitio) {
            $user_id = $sitio->user_id;

// averiguo el idiaoma actual del usuario perteneciente al sitio
            $usuario = $this->publishers->get_user_by_id($user_id, 1);
            if ($usuario) {
                if ($usuario->idioma == 'ES') {
                    $descripcion = 'Pago por publinota ' . $publinota->nombre;
                } else {
                    $descripcion = 'Payment for ' . $publinota->nombre;
                }
            }

            if ($publinota->moneda == 'USD') {
                //$importe = 100;
                
                if($publinota->importe_sitio)
                    $importe = $publinota->importe_sitio;
                else
                    $importe = 100;
                
            } else {
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

                    $importe = ($precio_por_sitio / 2) * $cotizacion;
                }
            }

            $dias_de_pago = $this->constants->get_constant_by_id(5);

            $dia = time() + ( round($dias_de_pago->value) * 24 * 60 * 60);
            $texto_fecha = date('Y-m-d H:i:s', $dia);

            $ultimo_pago = $this->payments->get_last_payment_by_id($user_id);

            $balance = 0;
            if ($ultimo_pago) {
                foreach ($ultimo_pago as $row) {
                    $ultimo_balance = $row->balance;
                    $balance = $ultimo_balance + $importe;
                }

                $data_ingresos = array("concept" => "Ingresos " . $descripcion, "credit" => $importe, "pago" => "0", "user_id" => $user_id,
                    "balance" => $balance, "fecha" => date('Y-m-d H:i:s'), 'periodo' => date('Y-m-d H:i:s'));

                $this->payments->registrar_pago($data_ingresos);
            }
            $importe = str_replace(",", ".", $importe);

            $data_pago = array("concept" => $descripcion, "debit" => $importe, "pago" => "2", "user_id" => $user_id,
                "fecha" => $texto_fecha, 'periodo' => date('Y-m-d H:i:s'));

            $this->payments->registrar_pago($data_pago);
        }
    }

    function rechazar_publinota() {
        $id_sitio = $this->input->post('id_sitio');
        $id_publinota = $this->input->post('id_publinota');

        $this->publinotassitios->update($id_sitio, $id_publinota, array('estado_anunciante' => 'R'));
        /*
          // le devuelvo el valor de ese sitio al anunciante
          $publinota = $this->publinotas->get_publinota_by_id($id_publinota);
          if ($publinota) {
          // selecciono todos los sitios correspondientes a la camapania
          $sitios = $this->publinotassitios->get_sitios_by_publinota($publinota->Id);

          $cantidad_sitios = 0;

          if ($sitios)
          $cantidad_sitios = sizeof($sitios);

          $importe_a_cobrar = $precio_por_sitio = $publinota->precio;

          // creo un nuevo movimiento de saldo descontando el balance
          $data_insert_pago = array(
          'id_anunciante' => $this->tank_auth->get_user_id(),
          'id_campania' => $id_publinota,
          'credito' => $importe_a_cobrar,
          'balance' => ($this->user_data->limite_de_compra + $importe_a_cobrar),
          'moneda' => $this->user_data->moneda,
          'descripcion' => "Devoluci&oacute;n por el rechazo de sitio en la publinota " . $publinota->nombre,
          'tipo_de_pago' => 1,
          'tipo_campania' => 'publinota'
          );

          if (!$this->anunciantessaldos->insert_anunciante_saldo($data_insert_pago)) {
          echo json_encode(array('validate' => FALSE, 'error' => 'Ocurri&oacute; un error al intentar cobrar la publinota.'));
          die();
          }

          // ACA LO JODIDO! ******************************************************

          $limite_de_compra = $this->user_data->limite_de_compra;
          $saldo_prepago = $this->user_data->saldo_prepago;
          $saldo_prestamo = $this->user_data->saldo_prestamo;

          if ($publinota->consumo_saldo_prepago > 0) {

          $saldo_prepago += $importe_a_cobrar;

          $consumo_prepago = $publinota->consumo_saldo_prepago - $importe_a_cobrar;
          $consumo_prestamo = $publinota->consumo_saldo_prestamo;
          } else if ($publinota->consumo_saldo_prestamo > 0) {

          $saldo_prestamo += $importe_a_cobrar;

          $consumo_prepago = $publinota->consumo_saldo_prepago;
          $consumo_prestamo = $publinota->consumo_saldo_prestamo - $importe_a_cobrar;
          }

          // actualizo los saldos del anunciante
          $data_update = array(
          'limite_de_compra' => $limite_de_compra + $importe_a_cobrar,
          'saldo_prepago' => $saldo_prepago,
          'saldo_prestamo' => $saldo_prestamo
          );

          $this->anunciantes->update_anunciante($this->tank_auth->get_user_id(), $data_update);

          // guardo en el registro de la campa&ntilde;a cuanto consumio de cada saldo
          $update_campania = array(
          'consumo_saldo_prepago' => $consumo_prepago,
          'consumo_saldo_prestamo' => $consumo_prestamo
          );

          $this->publinotas->update($id_publinota, $update_campania);

          // FINAL DE LO JODIDO! *************************************************
          }
         */
    }

    function crear_publinota() {
        if (!$this->tank_auth->is_logged_in())
            redirect('/auth/login');

        $anunciantes_adserver = $this->users->get_anunciantes_app_by_id($this->tank_auth->get_user_id());

        $usuario = $this->users->get_user_by_id($this->tank_auth->get_user_id(), 1);

        $data['clientes'] = $this->clientes_model->getAll($this->tank_auth->get_user_id());
        $data['anunciantes_adserver'] = $anunciantes_adserver;
        $data['creado_desde_sitio'] = $usuario->creado_desde_sitio;
        $data['habilitar_descuentos'] = $usuario->habilitar_descuentos;
        $data['id_anunciante'] = $usuario->id;

        $this->load->view('alta_campania_publinota', $data);
    }

    function crear_publinota_embajadoras() {
        if (!$this->tank_auth->is_logged_in())
            redirect('/auth/login');

        $anunciantes_adserver = $this->users->get_anunciantes_app_by_id($this->tank_auth->get_user_id());

        $usuario = $this->users->get_user_by_id($this->tank_auth->get_user_id(), 1);

        $data['clientes'] = $this->clientes_model->getAll($this->tank_auth->get_user_id());
        $data['anunciantes_adserver'] = $anunciantes_adserver;
        $data['creado_desde_sitio'] = $usuario->creado_desde_sitio;
        $data['habilitar_descuentos'] = $usuario->habilitar_descuentos;
        $data['id_anunciante'] = $usuario->id;

        $this->load->view('alta_campania_publinota_embajadoras', $data);
    }

    function crear_mencion_sitios() {
        if (!$this->tank_auth->is_logged_in())
            redirect('/auth/login');

        $anunciantes_adserver = $this->users->get_anunciantes_app_by_id($this->tank_auth->get_user_id());

        $usuario = $this->users->get_user_by_id($this->tank_auth->get_user_id(), 1);

        $data['clientes'] = $this->clientes_model->getAll($this->tank_auth->get_user_id());
        $data['anunciantes_adserver'] = $anunciantes_adserver;
        $data['creado_desde_sitio'] = $usuario->creado_desde_sitio;
        $data['habilitar_descuentos'] = $usuario->habilitar_descuentos;
        $data['id_anunciante'] = $usuario->id;

        $this->load->view('alta_campania_mencion_sitios', $data);
    }

    function crear_mencion_embajadoras() {
        if (!$this->tank_auth->is_logged_in())
            redirect('/auth/login');

        $anunciantes_adserver = $this->users->get_anunciantes_app_by_id($this->tank_auth->get_user_id());

        $usuario = $this->users->get_user_by_id($this->tank_auth->get_user_id(), 1);

        $data['clientes'] = $this->clientes_model->getAll($this->tank_auth->get_user_id());
        $data['anunciantes_adserver'] = $anunciantes_adserver;
        $data['creado_desde_sitio'] = $usuario->creado_desde_sitio;
        $data['habilitar_descuentos'] = $usuario->habilitar_descuentos;
        $data['id_anunciante'] = $usuario->id;

        $this->load->view('alta_campania_mencion_embajadoras', $data);
    }

    function crear_encuesta() {
        if (!$this->tank_auth->is_logged_in())
            redirect('/auth/login');

        $anunciantes_adserver = $this->users->get_anunciantes_app_by_id($this->tank_auth->get_user_id());

        $usuario = $this->users->get_user_by_id($this->tank_auth->get_user_id(), 1);

        $data['anunciantes_adserver'] = $anunciantes_adserver;
        $data['creado_desde_sitio'] = $usuario->creado_desde_sitio;
        $data['habilitar_descuentos'] = $usuario->habilitar_descuentos;
        $data['id_anunciante'] = $usuario->id;

        $this->load->view('alta_campania_encuesta', $data);
    }

    /*
     * SETs
     */

    function prueba_pago() {

        $data_pago = null;

        $tarjeta_usuario = $this->usuariostarjetasdecredito->get_tarjetas_de_credito_por_usuario($this->tank_auth->get_user_id());
        $usuario = $this->users->get_user_by_id($this->tank_auth->get_user_id(), 1);

        $data_pago['tipo_tarjeta'] = $tarjeta_usuario->id_tipo_tarjeta;
        $data_pago['nro_tarjeta'] = $tarjeta_usuario->nro_tarjeta;
        $data_pago['ccv'] = $tarjeta_usuario->ccv;
        $data_pago['mes_expiracion'] = $tarjeta_usuario->mes_expiracion;
        $data_pago['anio_expiracion'] = $tarjeta_usuario->anio_expiracion;
        $data_pago['email_usuario'] = $usuario->email;
        $data_pago['descripcion_pago'] = "Pago inicial de campa&ntilde;a";
        $data_pago['id_usuario'] = $this->tank_auth->get_user_id();

        $this->ejecutar_pago_directo($data_pago);
    }

    function ejecutar_pago_directo($data) {
        try {
            $importe = '5.00';

            $res_tarjeta_de_credito = $this->tarjetasdecredito->get_tarjeta_de_credito_by_id($data['tipo_tarjeta']);

            $DPFields = array(
                'paymentaction' => 'Sale',
                'ipaddress' => $_SERVER['REMOTE_ADDR'],
                'returnfmfdetails' => '1'
            );

            $CCDetails = array(
                'creditcardtype' => $res_tarjeta_de_credito->id_paypal,
                'acct' => $data['nro_tarjeta'],
                'expdate' => $data['mes_expiracion'] . $data['anio_expiracion'],
                'cvv2' => $data['ccv'],
                'startdate' => '',
                'issuenumber' => ''
            );

            $PayerInfo = array(
                'email' => $data['email_usuario'],
                'payerid' => '',
                'payerstatus' => '',
                'business' => 'MediaFem'
            );

            $PaymentDetails = array(
                'amt' => $importe,
                'currencycode' => 'USD',
                'itemamt' => '0.00',
                'shippingamt' => '0.00',
                'shipdiscamt' => '',
                'handlingamt' => '',
                'taxamt' => '',
                'desc' => $data['descripcion_pago'],
                'custom' => '',
                'invnum' => '',
                'notifyurl' => ''
            );

            $PayPalRequestData = array(
                'DPFields' => $DPFields,
                'CCDetails' => $CCDetails,
                'PayerInfo' => $PayerInfo,
                'PaymentDetails' => $PaymentDetails
            );

            $PayPalResult = $this->paypal_pro->DoDirectPayment($PayPalRequestData);

            if (!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK'])) {
//$errors = array('Errors' => $PayPalResult['ERRORS']);
                return false;
            } else {
// Successful call.  Load view or whatever you need to do here.

                $data_insert = array(
                    'id_anunciante' => $data['id_usuario'],
                    'nro_tarjeta' => $data['nro_tarjeta'],
                    'descripcion' => $data['descripcion_pago'],
                    'importe' => $importe,
                    'id_moneda' => 1,
                    'tipo_de_pago' => 2,
                    'id_transaccion' => $PayPalResult['TRANSACTIONID'],
                    'estado' => 1
                );

                if ($this->anunciantespagos->insert_anunciante_pago($data_insert)) {
                    return true;
                } else {
                    return false;
                }
            }
        } catch (Exception $ex) {
            return false;
        }
    }

    function getStamp() {
        list($Mili, $bot) = explode(" ", microtime());
        $DM = substr(strval($Mili), 2, 4);
        return strval(date("Y") . date("m") . date("d") . date("H") . date("i") . date("s") . $DM);
    }

    function subir_archivos($desde = 'alta') {
// HTTP headers for no cache etc
        header("Expires: Mon, 26 Jul 2050 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        $id_campania = $this->session->userdata('id_campania');

// Settings
        $targetDir = 'creatividades';

        $cleanupTargetDir = true; // Remove old files
        $maxFileAge = 5 * 3600; // Temp file age in seconds
// 5 minutes execution time
        @set_time_limit(5 * 60);

// Get parameters
        $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
        $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
        $nombre_real = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';

        $extension = explode('.', $nombre_real);

        $fileName = $this->getStamp() . '.' . $extension[1];

// Clean the fileName for security reasons
        $fileName = preg_replace('/[^\w\._]+/', '_', $fileName);

// Make sure the fileName is unique but only if chunking is disabled
        if ($chunks < 2 && file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName)) {
            $ext = strrpos($fileName, '.');
            $fileName_a = substr($fileName, 0, $ext);
            $fileName_b = substr($fileName, $ext);

            $count = 1;
            while (file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName_a . '_' . $count . $fileName_b))
                $count++;

            $fileName = $fileName_a . '_' . $count . $fileName_b;
        }

        $fileName = $id_campania . "_" . $fileName;
        $filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

// Create target dir
        if (!file_exists($targetDir))
            @mkdir($targetDir);

// Remove old temp files
        if ($cleanupTargetDir && is_dir($targetDir) && ($dir = opendir($targetDir))) {
            while (($file = readdir($dir)) !== false) {
                $tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

// Remove temp file if it is older than the max age and is not the current file
                if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge) && ($tmpfilePath != "{$filePath}.part")) {
                    @unlink($tmpfilePath);
                }
            }

            closedir($dir);
        } else
            die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');


// Look for the content type header
        if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
            $contentType = $_SERVER["HTTP_CONTENT_TYPE"];

        if (isset($_SERVER["CONTENT_TYPE"]))
            $contentType = $_SERVER["CONTENT_TYPE"];


// Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
        if (strpos($contentType, "multipart") !== false) {
            if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
// Open temp file
                $out = fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
                if ($out) {

// Read binary input stream and append it to temp file
                    $in = fopen($_FILES['file']['tmp_name'], "rb");

                    if ($in) {
                        while ($buff = fread($in, 4096))
                            fwrite($out, $buff);

                        $mime = $_FILES['file']['type'];

                        $data_insert = array('id_campania' => $id_campania, 'nombre_real' => $nombre_real, 'nombre_archivo' => $fileName, 'mime' => $mime);

                        $this->archivoscreatividades->insert_archivo($data_insert);

                        $update_campania = array(
                            'estado' => 'PENDIENTE'
                        );

                        $this->campanias->update_campania($id_campania, $update_campania);

                        if ($desde == 'modificar') {
                            $campania = $this->campanias->get_campania_by_id($id_campania);

                            $version = $campania->historial_version + 1;

// inserto el comentario y el historial
                            $this->insert_historial($id_campania, $version, 'Se actualizaron las piezas, se agrego la siguiente: ' . $nombre_real . '.');

// inserto un comentario para ordenar todo
                            $data_insert_comentario = array(
                                'id_campania' => $id_campania,
                                'comentario' => HISTORIAL_STRING . ' - Historial - ' . $version,
                                'fecha_alta' => date('Y-m-j H:i:s', strtotime('-1 hour', strtotime(date('Y-m-d H:i:s'))))
                            );
                            $this->campaniashistorial->insert_comentario($data_insert_comentario);

                            $update = array(
                                'historial_version' => $version
                            );

                            $this->campanias->update_campania($id_campania, $update);
                        }
                    } else
                        die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
                    fclose($in);
                    fclose($out);
                    @unlink($_FILES['file']['tmp_name']);
                } else
                    die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
            } else
                die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
        } else {
// Open temp file
            $out = fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
            if ($out) {
// Read binary input stream and append it to temp file
                $in = fopen("php://input", "rb");

                if ($in) {
                    while ($buff = fread($in, 4096))
                        fwrite($out, $buff);
                } else
                    die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');

                fclose($in);
                fclose($out);
            } else
                die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
        }

// Check if file has been uploaded
        if (!$chunks || $chunk == $chunks - 1) {
// Strip the temp .part suffix off
            rename("{$filePath}.part", $filePath);
        }

        die('ok');
    }

// CAMPA&Ntilde;AS PUBLINOTAS - PRIMER PASO *************************************************
    function insertar_publinota_primer_paso() {
// recibo todos los datos del post
        $id_anunciante = trim($this->input->post('id_anunciante')); // validado
        $id_cliente = trim($this->input->post('id_cliente')); // validado
        $nombre_campania = trim($this->input->post('nombre_campania')); // validado
        $tipo_campania = trim($this->input->post('tipo_campania'));

        $fecha_inicio = trim($this->input->post('fecha_inicio')); // validado,
        $fecha_fin = trim($this->input->post('fecha_fin')); // validado

        $mantener_publicada = trim($this->input->post('mantener_publicada')); // validado

        list($dia_desde, $mes_desde, $anio_desde) = explode("-", $fecha_inicio);
        list($dia_hasta, $mes_hasta, $anio_hasta) = explode("-", $fecha_fin);
        $fecha_inicio = $anio_desde . "-" . $mes_desde . "-" . $dia_desde;
        $fecha_fin = $anio_hasta . "-" . $mes_hasta . "-" . $dia_hasta;

// valido los datos ingresados
// valido anunciante
        if ($id_anunciante == 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'Por favor seleccione un anunciante para la campa&ntilde;a, si es su primer campa&ntilde;a previamente debe hacer click en "Nuevo Anunciante" para seleccionarlo.'));
            die();
        }

// valido nombre de campa&ntilde;a.
        if (strlen($nombre_campania) <= 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'Por favor ingrese un nombre para la campa&ntilde;a.'));
            die();
        }

        $tipo_campania = str_replace('_', ' ', $tipo_campania);
        $nombre_campania = $nombre_campania . ' - ' . ucfirst($tipo_campania);

// valido que el nombre de la campa&ntilde;a no exista
        if ($this->campanias->get_campania_by_nombre($nombre_campania) != null) {
            echo json_encode(array('validate' => FALSE, 'error' => 'El nombre de la campa&ntilde;a ya se encuenta registrado, por favor indique otro nombre para la campa&ntilde;a.'));
            die();
        }

// valido la fecha de inicio
        if (strlen($fecha_inicio) <= 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'Por favor ingrese la fecha de inicio correspondiente a la campa&ntilde;a.'));
            die();
        }

        $fecha_actual = date('Y-m-d');
// valido que la fecha de inicio no sea menor a la fecha actual
        if ($this->_dateDiff($fecha_inicio, $fecha_actual) > 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'La fecha de inicio es menor a la fecha actual.'));
            die();
        }

// valido que la fecha de inicio no sea mayor a la fecha de fin
        if ($this->_dateDiff($fecha_inicio, $fecha_fin) < 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'La fecha de inicio es mayor a la fecha de fin de la campa&ntilde;a.'));
            die();
        }

// valido que la fecha de fin no sea menor a la fecha actual
        if ($this->_dateDiff($fecha_fin, $fecha_actual) > 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'La fecha de fin es menor a la fecha actual.'));
            die();
        }

// valido que la fecha de fin no sea menor a la fecha de inicio
        if ($this->_dateDiff($fecha_fin, $fecha_inicio) > 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'La fecha de fin es menor a la fecha de inicio de la campa&ntilde;a.'));
            die();
        }

// valido la fecha de fin
        if (strlen($fecha_fin) <= 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'Por favor ingrese la fecha de fin correspondiente a la campa&ntilde;a.'));
            die();
        }

// comienzo el ingreso en la tabla campania de la DB
        $data = array(
            'Id_anunciante' => $id_anunciante,
            'id_cliente' => $id_cliente,
            'nombre' => $nombre_campania,
            'precio' => $tipo_campania,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'fecha_alta' => date('Y-m-d H:i:s'),
            'usuario_creador' => $this->tank_auth->get_user_id(),
            'creada_desde_anunciantes' => '1',
            'moneda' => $this->user_data->moneda,
            'mantener_publicada' => $mantener_publicada
        );

        if ($this->publinotas->insertar($data)) {
// si se ingreso la campania correctamente entonces obtengo el id de la campania creada y cargo los demas datos
            $id_campania = $this->db->insert_id();

            $newdata = array(
                'id_campania' => $id_campania
            );

            $this->session->set_userdata($newdata);

            echo json_encode(array('validate' => TRUE, 'ok' => 'Campa&ntilde;a creada correctamente.', 'id_campania' => $id_campania));
        } else {
            echo json_encode(array('validate' => FALSE, 'error' => 'Ocurrio un error al intentar crear la campa&ntilde;a en la base de datos.'));
        }
    }

    function insertar_publinota_embajadoras_primer_paso() {
// recibo todos los datos del post
        $id_anunciante = trim($this->input->post('id_anunciante')); // validado
        $id_cliente = trim($this->input->post('id_cliente')); // validado
        $nombre_campania = trim($this->input->post('nombre_campania')); // validado
        $tipo_campania = trim($this->input->post('tipo_campania'));
        $otros = trim($this->input->post('otros'));

        $fecha_inicio = trim($this->input->post('fecha_inicio')); // validado,
        $fecha_fin = trim($this->input->post('fecha_fin')); // validado

        $mantener_publicada = trim($this->input->post('mantener_publicada')); // validado

        list($dia_desde, $mes_desde, $anio_desde) = explode("-", $fecha_inicio);
        list($dia_hasta, $mes_hasta, $anio_hasta) = explode("-", $fecha_fin);
        $fecha_inicio = $anio_desde . "-" . $mes_desde . "-" . $dia_desde;
        $fecha_fin = $anio_hasta . "-" . $mes_hasta . "-" . $dia_hasta;

// valido los datos ingresados
// valido anunciante
        if ($id_anunciante == 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'Por favor seleccione un anunciante para la campa&ntilde;a, si es su primer campa&ntilde;a previamente debe hacer click en "Nuevo Anunciante" para seleccionarlo.'));
            die();
        }

// valido nombre de campa&ntilde;a.
        if (strlen($nombre_campania) <= 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'Por favor ingrese un nombre para la campa&ntilde;a.'));
            die();
        }

        $tipo_campania = str_replace('_', ' ', $tipo_campania);
        $nombre_campania = $nombre_campania . ' - ' . ucfirst($tipo_campania);

// valido que el nombre de la campa&ntilde;a no exista
        if ($this->campanias->get_campania_by_nombre($nombre_campania) != null) {
            echo json_encode(array('validate' => FALSE, 'error' => 'El nombre de la campa&ntilde;a ya se encuenta registrado, por favor indique otro nombre para la campa&ntilde;a.'));
            die();
        }

// valido la fecha de inicio
        if (strlen($fecha_inicio) <= 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'Por favor ingrese la fecha de inicio correspondiente a la campa&ntilde;a.'));
            die();
        }

        $fecha_actual = date('Y-m-d');
// valido que la fecha de inicio no sea menor a la fecha actual
        if ($this->_dateDiff($fecha_inicio, $fecha_actual) > 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'La fecha de inicio es menor a la fecha actual.'));
            die();
        }

// valido que la fecha de inicio no sea mayor a la fecha de fin
        if ($this->_dateDiff($fecha_inicio, $fecha_fin) < 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'La fecha de inicio es mayor a la fecha de fin de la campa&ntilde;a.'));
            die();
        }

// valido que la fecha de fin no sea menor a la fecha actual
        if ($this->_dateDiff($fecha_fin, $fecha_actual) > 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'La fecha de fin es menor a la fecha actual.'));
            die();
        }

// valido que la fecha de fin no sea menor a la fecha de inicio
        if ($this->_dateDiff($fecha_fin, $fecha_inicio) > 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'La fecha de fin es menor a la fecha de inicio de la campa&ntilde;a.'));
            die();
        }

// valido la fecha de fin
        if (strlen($fecha_fin) <= 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'Por favor ingrese la fecha de fin correspondiente a la campa&ntilde;a.'));
            die();
        }

// comienzo el ingreso en la tabla campania de la DB
        $data = array(
            'Id_anunciante' => $id_anunciante,
            'id_cliente' => $id_cliente,
            'nombre' => $nombre_campania,
            'precio' => $tipo_campania,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'fecha_alta' => date('Y-m-d H:i:s'),
            'usuario_creador' => $this->tank_auth->get_user_id(),
            'creada_desde_anunciantes' => '1',
            'moneda' => $this->user_data->moneda,
            'mantener_publicada' => $mantener_publicada,
            'otros' => $otros
        );

        if ($this->publinotas->insertar($data)) {
// si se ingreso la campania correctamente entonces obtengo el id de la campania creada y cargo los demas datos
            $id_campania = $this->db->insert_id();

            $newdata = array(
                'id_campania' => $id_campania
            );

            $this->session->set_userdata($newdata);

            echo json_encode(array('validate' => TRUE, 'ok' => 'Campa&ntilde;a creada correctamente.', 'id_campania' => $id_campania));
        } else {
            echo json_encode(array('validate' => FALSE, 'error' => 'Ocurrio un error al intentar crear la campa&ntilde;a en la base de datos.'));
        }
    }


    function insertar_mencion_sitios_primer_paso() {
// recibo todos los datos del post
        $id_anunciante = trim($this->input->post('id_anunciante')); // validado
        $id_cliente = trim($this->input->post('id_cliente')); // validado
        $nombre_campania = trim($this->input->post('nombre_campania')); // validado
        $tipo_campania = trim($this->input->post('tipo_campania'));
        $otros = trim($this->input->post('otros'));

        $fecha_inicio = trim($this->input->post('fecha_inicio')); // validado,
        $fecha_fin = trim($this->input->post('fecha_fin')); // validado

        $mantener_publicada = trim($this->input->post('mantener_publicada')); // validado

        list($dia_desde, $mes_desde, $anio_desde) = explode("-", $fecha_inicio);
        list($dia_hasta, $mes_hasta, $anio_hasta) = explode("-", $fecha_fin);
        $fecha_inicio = $anio_desde . "-" . $mes_desde . "-" . $dia_desde;
        $fecha_fin = $anio_hasta . "-" . $mes_hasta . "-" . $dia_hasta;

// valido los datos ingresados
// valido anunciante
        if ($id_anunciante == 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'Por favor seleccione un anunciante para la campa&ntilde;a, si es su primer campa&ntilde;a previamente debe hacer click en "Nuevo Anunciante" para seleccionarlo.'));
            die();
        }

// valido nombre de campa&ntilde;a.
        if (strlen($nombre_campania) <= 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'Por favor ingrese un nombre para la campa&ntilde;a.'));
            die();
        }

        $tipo_campania = str_replace('_', ' ', $tipo_campania);
        $nombre_campania = $nombre_campania . ' - ' . ucfirst($tipo_campania);

// valido que el nombre de la campa&ntilde;a no exista
        if ($this->campanias->get_campania_by_nombre($nombre_campania) != null) {
            echo json_encode(array('validate' => FALSE, 'error' => 'El nombre de la campa&ntilde;a ya se encuenta registrado, por favor indique otro nombre para la campa&ntilde;a.'));
            die();
        }

// valido la fecha de inicio
        if (strlen($fecha_inicio) <= 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'Por favor ingrese la fecha de inicio correspondiente a la campa&ntilde;a.'));
            die();
        }

        $fecha_actual = date('Y-m-d');
// valido que la fecha de inicio no sea menor a la fecha actual
        if ($this->_dateDiff($fecha_inicio, $fecha_actual) > 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'La fecha de inicio es menor a la fecha actual.'));
            die();
        }

// valido que la fecha de inicio no sea mayor a la fecha de fin
        if ($this->_dateDiff($fecha_inicio, $fecha_fin) < 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'La fecha de inicio es mayor a la fecha de fin de la campa&ntilde;a.'));
            die();
        }

// valido que la fecha de fin no sea menor a la fecha actual
        if ($this->_dateDiff($fecha_fin, $fecha_actual) > 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'La fecha de fin es menor a la fecha actual.'));
            die();
        }

// valido que la fecha de fin no sea menor a la fecha de inicio
        if ($this->_dateDiff($fecha_fin, $fecha_inicio) > 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'La fecha de fin es menor a la fecha de inicio de la campa&ntilde;a.'));
            die();
        }

// valido la fecha de fin
        if (strlen($fecha_fin) <= 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'Por favor ingrese la fecha de fin correspondiente a la campa&ntilde;a.'));
            die();
        }

// comienzo el ingreso en la tabla campania de la DB
        $data = array(
            'Id_anunciante' => $id_anunciante,
            'id_cliente' => $id_cliente,
            'nombre' => $nombre_campania,
            'precio' => $tipo_campania,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'fecha_alta' => date('Y-m-d H:i:s'),
            'usuario_creador' => $this->tank_auth->get_user_id(),
            'creada_desde_anunciantes' => '1',
            'moneda' => $this->user_data->moneda,
            'mantener_publicada' => $mantener_publicada,
            'otros' => $otros,
            'tipo' => 'MENCION_SITIOS'
        );

        if ($this->publinotas->insertar($data)) {
// si se ingreso la campania correctamente entonces obtengo el id de la campania creada y cargo los demas datos
            $id_campania = $this->db->insert_id();

            $newdata = array(
                'id_campania' => $id_campania
            );

            $this->session->set_userdata($newdata);

            echo json_encode(array('validate' => TRUE, 'ok' => 'Campa&ntilde;a creada correctamente.', 'id_campania' => $id_campania));
        } else {
            echo json_encode(array('validate' => FALSE, 'error' => 'Ocurrio un error al intentar crear la campa&ntilde;a en la base de datos.'));
        }
    }

    function insertar_mencion_embajadoras_primer_paso() {
// recibo todos los datos del post
        $id_anunciante = trim($this->input->post('id_anunciante')); // validado
        $id_cliente = trim($this->input->post('id_cliente')); // validado
        $nombre_campania = trim($this->input->post('nombre_campania')); // validado
        $tipo_campania = trim($this->input->post('tipo_campania'));
        $otros = trim($this->input->post('otros'));

        $fecha_inicio = trim($this->input->post('fecha_inicio')); // validado,
        $fecha_fin = trim($this->input->post('fecha_fin')); // validado

        $mantener_publicada = trim($this->input->post('mantener_publicada')); // validado

        list($dia_desde, $mes_desde, $anio_desde) = explode("-", $fecha_inicio);
        list($dia_hasta, $mes_hasta, $anio_hasta) = explode("-", $fecha_fin);
        $fecha_inicio = $anio_desde . "-" . $mes_desde . "-" . $dia_desde;
        $fecha_fin = $anio_hasta . "-" . $mes_hasta . "-" . $dia_hasta;

// valido los datos ingresados
// valido anunciante
        if ($id_anunciante == 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'Por favor seleccione un anunciante para la campa&ntilde;a, si es su primer campa&ntilde;a previamente debe hacer click en "Nuevo Anunciante" para seleccionarlo.'));
            die();
        }

// valido nombre de campa&ntilde;a.
        if (strlen($nombre_campania) <= 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'Por favor ingrese un nombre para la campa&ntilde;a.'));
            die();
        }

        $tipo_campania = str_replace('_', ' ', $tipo_campania);
        $nombre_campania = $nombre_campania . ' - ' . ucfirst($tipo_campania);

// valido que el nombre de la campa&ntilde;a no exista
        if ($this->campanias->get_campania_by_nombre($nombre_campania) != null) {
            echo json_encode(array('validate' => FALSE, 'error' => 'El nombre de la campa&ntilde;a ya se encuenta registrado, por favor indique otro nombre para la campa&ntilde;a.'));
            die();
        }

// valido la fecha de inicio
        if (strlen($fecha_inicio) <= 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'Por favor ingrese la fecha de inicio correspondiente a la campa&ntilde;a.'));
            die();
        }

        $fecha_actual = date('Y-m-d');
// valido que la fecha de inicio no sea menor a la fecha actual
        if ($this->_dateDiff($fecha_inicio, $fecha_actual) > 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'La fecha de inicio es menor a la fecha actual.'));
            die();
        }

// valido que la fecha de inicio no sea mayor a la fecha de fin
        if ($this->_dateDiff($fecha_inicio, $fecha_fin) < 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'La fecha de inicio es mayor a la fecha de fin de la campa&ntilde;a.'));
            die();
        }

// valido que la fecha de fin no sea menor a la fecha actual
        if ($this->_dateDiff($fecha_fin, $fecha_actual) > 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'La fecha de fin es menor a la fecha actual.'));
            die();
        }

// valido que la fecha de fin no sea menor a la fecha de inicio
        if ($this->_dateDiff($fecha_fin, $fecha_inicio) > 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'La fecha de fin es menor a la fecha de inicio de la campa&ntilde;a.'));
            die();
        }

// valido la fecha de fin
        if (strlen($fecha_fin) <= 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'Por favor ingrese la fecha de fin correspondiente a la campa&ntilde;a.'));
            die();
        }

// comienzo el ingreso en la tabla campania de la DB
        $data = array(
            'Id_anunciante' => $id_anunciante,
            'id_cliente' => $id_cliente,
            'nombre' => $nombre_campania,
            'precio' => $tipo_campania,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'fecha_alta' => date('Y-m-d H:i:s'),
            'usuario_creador' => $this->tank_auth->get_user_id(),
            'creada_desde_anunciantes' => '1',
            'moneda' => $this->user_data->moneda,
            'mantener_publicada' => $mantener_publicada,
            'otros' => $otros,
            'tipo' => 'MENCION_EMBAJADORAS'
        );

        if ($this->publinotas->insertar($data)) {
// si se ingreso la campania correctamente entonces obtengo el id de la campania creada y cargo los demas datos
            $id_campania = $this->db->insert_id();

            $newdata = array(
                'id_campania' => $id_campania
            );

            $this->session->set_userdata($newdata);

            echo json_encode(array('validate' => TRUE, 'ok' => 'Campa&ntilde;a creada correctamente.', 'id_campania' => $id_campania));
        } else {
            echo json_encode(array('validate' => FALSE, 'error' => 'Ocurrio un error al intentar crear la campa&ntilde;a en la base de datos.'));
        }
    }

    function insertar_mencion_sitios_segundo_paso() {
// recibo todos los datos del post
        $id_campania = trim($this->input->post('id_campania'));
        $url_sitio = trim($this->input->post('url_sitio'));

// borro todos los sitios, para poder guardar los nuevos si es que los hay.
        $this->publinotassitios->delete_by_publinota($id_campania);

        $precio_publinota = $this->constants->get_constant_by_id(VALOR_PUBLINOTA);

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

                $valor_publinota = $precio_publinota->value * $cotizacion;
            }
        }else {
            $valor_publinota = $precio_publinota->value;
        }

        if ($this->user_data->notacion == 0) {
            $valor_publinota = number_format($valor_publinota, 3, '.', ',');
        } else if ($this->user_data->notacion == 1) {
            $valor_publinota = number_format($valor_publinota, 3, ',', '.');
        }


        $data = array('url_sitio' => $url_sitio);

        $this->publinotas->update($id_campania, $data);

        echo json_encode(array('validate' => TRUE, 'precio_publinota' => $valor_publinota, 'id_campania' => $id_campania));
    }

    function insertar_mencion_embajadoras_segundo_paso() {
// recibo todos los datos del post
        $id_campania = trim($this->input->post('id_campania'));
        $nombre_embajadora = trim($this->input->post('nombre_embajadora'));

// borro todos los sitios, para poder guardar los nuevos si es que los hay.
        $this->publinotassitios->delete_by_publinota($id_campania);

        $precio_publinota = $this->constants->get_constant_by_id(VALOR_PUBLINOTA);

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

                $valor_publinota = $precio_publinota->value * $cotizacion;
            }
        }else {
            $valor_publinota = $precio_publinota->value;
        }

        if ($this->user_data->notacion == 0) {
            $valor_publinota = number_format($valor_publinota, 3, '.', ',');
        } else if ($this->user_data->notacion == 1) {
            $valor_publinota = number_format($valor_publinota, 3, ',', '.');
        }


        $data = array('nombre_embajadora' => $nombre_embajadora);

        $this->publinotas->update($id_campania, $data);

        echo json_encode(array('validate' => TRUE, 'precio_publinota' => $valor_publinota, 'id_campania' => $id_campania));
    }

    function insertar_publinota_embajadoras_segundo_paso() {
// recibo todos los datos del post
        $id_campania = trim($this->input->post('id_campania'));
        $nombre_embajadora = trim($this->input->post('nombre_embajadora'));

// borro todos los sitios, para poder guardar los nuevos si es que los hay.
        $this->publinotassitios->delete_by_publinota($id_campania);

        $precio_publinota = $this->constants->get_constant_by_id(VALOR_PUBLINOTA);

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

                $valor_publinota = $precio_publinota->value * $cotizacion;
            }
        }else {
            $valor_publinota = $precio_publinota->value;
        }

        if ($this->user_data->notacion == 0) {
            $valor_publinota = number_format($valor_publinota, 3, '.', ',');
        } else if ($this->user_data->notacion == 1) {
            $valor_publinota = number_format($valor_publinota, 3, ',', '.');
        }


        $data = array('nombre_embajadora' => $nombre_embajadora,
                        'tipo' => 'EMBAJADORAS');

        $this->publinotas->update($id_campania, $data);

        echo json_encode(array('validate' => TRUE, 'precio_publinota' => $valor_publinota, 'id_campania' => $id_campania));
    }

// CAMPA&Ntilde;AS PUBLINOTAS - SEGUNDO PASO ************************************************
    function insertar_publinota_segundo_paso() {
// recibo todos los datos del post
        $id_campania = trim($this->input->post('id_campania'));
        $id_sitios = trim($this->input->post('id_sitios'));

// borro todos los sitios, para poder guardar los nuevos si es que los hay.
        $this->publinotassitios->delete_by_publinota($id_campania);

// obtengo los sitios seleccionados
        $sitios = explode(";", $id_sitios);

// si hay sitios los agrego al array
        if ($sitios) {
            foreach ($sitios as $sitio) {
                if ($sitio)
                    $arr_sitios[] = $sitio;
            }
        }

// asocio en la DB la publinota con los sitios
        if (isset($arr_sitios)) {
            $in_sitios = '';
// elimino los sitios duplicados del array
            $sitios = array_values(array_unique($arr_sitios));
            for ($a = 0; $a < sizeof($sitios); $a++) {
// asocio los sitios a la campa&ntilde;a creada
                $data_campania_sitio = array('id_publinota' => $id_campania, 'id_sitio' => $sitios[$a]);
                $this->publinotassitios->insertar($data_campania_sitio);
            }
        }

        $precio_publinota = $this->constants->get_constant_by_id(VALOR_PUBLINOTA);

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

                $valor_publinota = $precio_publinota->value * $cotizacion;
            }
        }else {
            $valor_publinota = $precio_publinota->value;
        }

        if ($this->user_data->notacion == 0) {
            $valor_publinota = number_format($valor_publinota, 3, '.', ',');
        } else if ($this->user_data->notacion == 1) {
            $valor_publinota = number_format($valor_publinota, 3, ',', '.');
        }

        echo json_encode(array('validate' => TRUE, 'precio_publinota' => $valor_publinota, 'id_campania' => $id_campania));
    }

    function insertar_publinota_embajadoras_tercer_paso() {
// recibo todos los datos del post
        $id_campania = trim($this->input->post('id_campania'));
        $precio = trim($this->input->post('inversion_neta'));

        if ($this->user_data->notacion == 0) {
            $precio = str_replace(',', '', $precio);
        } else if ($this->user_data->notacion == 1) {
            $precio = str_replace('.', '', $precio);
            $precio = str_replace(',', '.', $precio);
        }

        $data = array('precio' => $precio);

        $this->publinotas->update($id_campania, $data);

        echo json_encode(array('validate' => TRUE, 'ok' => 'Campa&ntilde;a creada correctamente.', 'id_campania' => $id_campania));
    }

    function insertar_mencion_sitios_tercer_paso() {
// recibo todos los datos del post
        $id_campania = trim($this->input->post('id_campania'));
        $precio = trim($this->input->post('inversion_neta'));

        if ($this->user_data->notacion == 0) {
            $precio = str_replace(',', '', $precio);
        } else if ($this->user_data->notacion == 1) {
            $precio = str_replace('.', '', $precio);
            $precio = str_replace(',', '.', $precio);
        }

        $data = array('precio' => $precio);

        $this->publinotas->update($id_campania, $data);

        echo json_encode(array('validate' => TRUE, 'ok' => 'Campa&ntilde;a creada correctamente.', 'id_campania' => $id_campania));
    }

// CAMPA&Ntilde;AS PUBLINOTAS - TERCER PASO ************************************************
    function insertar_publinota_tercer_paso() {
// recibo todos los datos del post
        $id_campania = trim($this->input->post('id_campania'));
        $precio = trim($this->input->post('inversion_neta'));

        if ($this->user_data->notacion == 0) {
            $precio = str_replace(',', '', $precio);
        } else if ($this->user_data->notacion == 1) {
            $precio = str_replace('.', '', $precio);
            $precio = str_replace(',', '.', $precio);
        }

        $data = array('precio' => $precio);

        $this->publinotas->update($id_campania, $data);

        echo json_encode(array('validate' => TRUE, 'ok' => 'Campa&ntilde;a creada correctamente.', 'id_campania' => $id_campania));
    }

// CAMPA&Ntilde;AS PUBLINOTAS - SUBIR IMAGEN ************************************************
    function subir_imagen_publinota() {
        if (!isset($_FILES['archivo']['name'])) {
            echo json_encode(array('validate' => FALSE, 'error' => "Ocurrio un error al subir el archivo. No pudo guardarse."));
            die();
        }

        $upload_folder = 'creatividades/publinotas';

        $nombre_archivo = date('YmdHis') . '_' . $_FILES['archivo']['name'];

        $tmp_archivo = $_FILES['archivo']['tmp_name'];

        $archivador = $upload_folder . '/' . $nombre_archivo;

        if (!move_uploaded_file($tmp_archivo, $archivador)) {
            $return = array('validate' => FALSE, 'error' => "Ocurrio un error al subir el archivo. No pudo guardarse.");
        } else {
            $return = array('validate' => TRUE, 'file_name' => $nombre_archivo);
        }

        echo json_encode($return);
    }

// CAMPA&Ntilde;AS PUBLINOTAS - CUARTO PASO ************************************************
    function insertar_publinota_cuarto_paso() {
// recibo todos los datos del post
        $id_campania = trim($this->input->post('id_campania'));
        $titulo = trim($this->input->post('titulo'));
        $contenido = trim($this->input->post('mensaje'));
        $imagen = trim($this->input->post('imagen'));

        if (strlen($titulo) < 1) {
            echo json_encode(array('validate' => FALSE, 'error' => 'Por favor ingrese el t&iacute;tulo de la publinota.', 'id_campania' => $id_campania));
            die();
        }

        if (strlen($contenido) < 1) {
            echo json_encode(array('validate' => FALSE, 'error' => 'Por favor ingrese el mensaje de la publinota.', 'id_campania' => $id_campania));
            die();
        }

        $cantidad_de_links = substr_count($contenido, 'href="');

        if ($cantidad_de_links > 1) {
            echo json_encode(array('validate' => FALSE, 'error' => 'El contenido tiene m&aacute;s de un enlace.', 'id_campania' => $id_campania));
            die();
        } else {
            $data = array('titulo' => $titulo, 'contenido' => $contenido, 'imagen' => $imagen);

            $this->publinotas->update($id_campania, $data);

            echo json_encode(array('validate' => TRUE, 'ok' => 'Campa&ntilde;a creada correctamente.', 'id_campania' => $id_campania));
        }
    }

// CAMPA&Ntilde;AS PUBLINOTAS - QUINTO PASO ************************************************
    function insertar_publinota_quinto_paso() {
// recibo todos los datos del post
        $id_campania = trim($this->input->post('id_campania'));

        $data = array('alta_finalizada' => 1);

        $this->publinotas->update($id_campania, $data);

        echo json_encode(array('validate' => TRUE, 'ok' => 'Campa&ntilde;a creada correctamente.', 'id_campania' => $id_campania));
    }

    function get_sites_for_publinotas() {
        $categorias = $this->categorias->get_categorias();

//$multiplicar = $this->constants->get_constant_by_id(ID_MULTIPLICAR_VOLUMEN);

        $arr_categorias = NULL;

        if ($categorias) {
            foreach ($categorias as $categoria) {
                $sitio = $sitios = $arr_sitios = NULL;

                $sites = $this->sitescategories->get_all_sites_by_category($categoria->id);

                if ($sites) {
                    foreach ($sites as $site) {
                        $sitio = $this->sites->get_site_by_id($site->id_sitio);

                        if ($sitio) {
                            $sitios['nombre'] = $sitio->nombre_appnexus;
                            if (!strlen(trim($sitios['nombre'])))
                                $sitios['nombre'] = $sitio->nombre_dfp;

                            $sitios['id'] = $sitio->id;
                            /*
                              $sitios['impresiones'] = $sitio->impresiones_ayer;

                              if ($sitios['impresiones'] > 0)
                             *
                             */
                            if ($sitio->posts_patrocinados == 1)
                                $arr_sitios[] = $sitios;
                        }
                    }

                    $sort = array();
                    foreach ($arr_sitios as $k => $v) {
                        $sort['id'][$k] = $v['id'];
                        $sort['nombre'][$k] = $v['nombre'];
                        //$sort['impresiones'][$k] = $v['impresiones'];
                    }

                    //array_multisort($sort['impresiones'], SORT_DESC, $sort['nombre'], SORT_ASC, $arr_sitios);

                    if ($arr_sitios) {
                        $arr_categorias[] = array(
                            'id' => $categoria->id,
                            'name' => $categoria->nombre,
                            'sites' => $arr_sitios
                        );
                    }
                }
            }
        }

        $data['categorias'] = $arr_categorias;

        $this->load->view('list_sites_publinota', $data);
    }

// CAMPA&Ntilde;AS NORMALES *****************************************************************
    function insertar_campania_appnexus_primer_paso() {
// recibo todos los datos del post
        $id_anunciante = trim($this->input->post('id_anunciante')); // validado
        $nombre_campania = trim($this->input->post('nombre_campania')); // validado
        $tipo_campania = $tipo_campania2 = trim($this->input->post('tipo_campania'));

        $fecha_inicio = trim($this->input->post('fecha_inicio')); // validado,
        $fecha_fin = trim($this->input->post('fecha_fin')); // validado

        $clientes = trim($this->input->post('cliente'));

        list($dia_desde, $mes_desde, $anio_desde) = explode("-", $fecha_inicio);
        list($dia_hasta, $mes_hasta, $anio_hasta) = explode("-", $fecha_fin);
        $fecha_inicio = $anio_desde . "-" . $mes_desde . "-" . $dia_desde . ' 00:00:00';
        $fecha_fin = $anio_hasta . "-" . $mes_hasta . "-" . $dia_hasta . ' 23:59:59';

// valido los datos ingresados
// valido anunciante
        if ($id_anunciante == 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'Por favor seleccione un anunciante para la campa&ntilde;a, si es su primer campa&ntilde;a previamente debe hacer click en "Nuevo Anunciante" para seleccionarlo.'));
            die();
        }

// valido nombre de campa&ntilde;a.
        if (strlen($nombre_campania) <= 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'Por favor ingrese un nombre para la campa&ntilde;a.'));
            die();
        }

        $tipo_campania = str_replace('_', ' ', $tipo_campania);
        $nombre_campania = $nombre_campania . ' - ' . ucfirst($tipo_campania);

// valido que el nombre de la campa&ntilde;a no exista
        if ($this->campanias->get_campania_by_nombre($nombre_campania) != null) {
            echo json_encode(array('validate' => FALSE, 'error' => 'El nombre de la campa&ntilde;a ya se encuenta registrado, por favor indique otro nombre para la campa&ntilde;a.'));
            die();
        }

// valido la fecha de inicio
        if (strlen($fecha_inicio) <= 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'Por favor ingrese la fecha de inicio correspondiente a la campa&ntilde;a.'));
            die();
        }

        $fecha_actual = date('Y-m-d');
// valido que la fecha de inicio no sea menor a la fecha actual
        if ($this->_dateDiff($fecha_inicio, $fecha_actual) > 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'La fecha de inicio es menor a la fecha actual.'));
            die();
        }

// valido que la fecha de inicio no sea mayor a la fecha de fin
        if ($this->_dateDiff($fecha_inicio, $fecha_fin) < 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'La fecha de inicio es mayor a la fecha de fin de la campa&ntilde;a.'));
            die();
        }

// valido que la fecha de fin no sea menor a la fecha actual
        if ($this->_dateDiff($fecha_fin, $fecha_actual) > 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'La fecha de fin es menor a la fecha actual.'));
            die();
        }

// valido que la fecha de fin no sea menor a la fecha de inicio
        if ($this->_dateDiff($fecha_fin, $fecha_inicio) > 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'La fecha de fin es menor a la fecha de inicio de la campa&ntilde;a.'));
            die();
        }

// valido la fecha de fin
        if (strlen($fecha_fin) <= 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'Por favor ingrese la fecha de fin correspondiente a la campa&ntilde;a.'));
            die();
        }

// comienzo el ingreso en la tabla campania de la DB
        $data = array(
            'id_anunciante' => $id_anunciante,
            'id_cliente' => $clientes,
            'nombre' => $nombre_campania,
            'tipo_campania' => $tipo_campania2,
            'fecha_fin' => $fecha_fin,
            'fecha_inicio' => $fecha_inicio,
            'fecha_alta' => date('Y-m-d H:i:s'),
            'usuario_creador' => $this->tank_auth->get_user_id(),
            'creada_desde_anunciantes' => '1',
            'adserver' => 'APPNEXUS',
            'moneda' => $this->user_data->moneda,
            'empresa_campania' => '1'
        );

        if ($this->campanias->insertar_campania($data)) {
// si se ingreso la campania correctamente entonces obtengo el id de la campania creada y cargo los demas datos
            $id_campania = $this->db->insert_id();

            $data_campania = array('id_campania' => $id_campania);
            $this->campaniascontrol->insert_control($data_campania);

            $newdata = array(
                'id_campania' => $id_campania
            );

            $this->session->set_userdata($newdata);

            $inversion_neta = number_format((float) trim($this->input->post('inversion')), 2, ',', '.'); // validado
// consulto si el anunciante esta registrado en appnexus y lo creo en caso de ser necesario
            $existe_anunciante = $this->anunciantes->get_anunciante_adserver_by_id($id_anunciante);
            if ($existe_anunciante->id_appnexus == NULL) {

                $nombre = $existe_anunciante->nombre;
                $email = $this->user_data->email;

                $anunciante_appnexus = $this->api->crear_anunciante_appnexus($nombre, $email);
                if ($anunciante_appnexus) {
                    unset($data);
                    $data = array('id_appnexus' => $anunciante_appnexus['id'], 'adserver_actual' => 1);

                    $this->anunciantes->update_anunciante_adserver($id_anunciante, $data);
                }
            }

            echo json_encode(array('validate' => TRUE, 'ok' => 'Campa&ntilde;a creada correctamente.', 'id_campania' => $id_campania, 'inversion_neta' => $inversion_neta));
        } else {
            echo json_encode(array('validate' => FALSE, 'error' => 'Ocurrio un error al intentar crear la campa&ntilde;a en la base de datos.'));
        }
    }

    function insertar_campania_primer_paso() {
// recibo todos los datos del post
        $id_anunciante = trim($this->input->post('id_anunciante')); // validado
        $nombre_campania = trim($this->input->post('nombre_campania')); // validado
        $tipo_campania = $tipo_campania2 = trim($this->input->post('tipo_campania'));
        //$adserver = trim($this->input->post('adserver')); // validado
        
        $adserver_anunciantes = $this->constants->get_constant_by_id(32);
        $adserver = $adserver_anunciantes->string_value;
        
        $unificar_campania = trim($this->input->post('unificar_campania'));

        $cliente = trim($this->input->post('cliente'));

        $fecha_inicio = trim($this->input->post('fecha_inicio')); // validado,
        $fecha_fin = trim($this->input->post('fecha_fin')); // validado

        list($dia_desde, $mes_desde, $anio_desde) = explode("-", $fecha_inicio);
        list($dia_hasta, $mes_hasta, $anio_hasta) = explode("-", $fecha_fin);
        $fecha_inicio = $anio_desde . "-" . $mes_desde . "-" . $dia_desde . ' 00:00:00';
        $fecha_fin = $anio_hasta . "-" . $mes_hasta . "-" . $dia_hasta . ' 23:59:59';

// valido los datos ingresados
// valido anunciante
        if ($id_anunciante == 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'Por favor seleccione un anunciante para la campa&ntilde;a, si es su primer campa&ntilde;a previamente debe hacer click en "Nuevo Anunciante" para seleccionarlo.'));
            die();
        }

// valido nombre de campa&ntilde;a.
        if (strlen($nombre_campania) <= 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'Por favor ingrese un nombre para la campa&ntilde;a.'));
            die();
        }

        $tipo_campania = str_replace('_', ' ', $tipo_campania);
        $nombre_campania = $nombre_campania . ' - ' . ucfirst($tipo_campania);

// valido que el nombre de la campa&ntilde;a no exista
        if ($this->campanias->get_campania_by_nombre($nombre_campania) != null) {
            echo json_encode(array('validate' => FALSE, 'error' => 'El nombre de la campa&ntilde;a ya se encuenta registrado, por favor indique otro nombre para la campa&ntilde;a.'));
            die();
        }

// valido la fecha de inicio
        if (strlen($fecha_inicio) <= 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'Por favor ingrese la fecha de inicio correspondiente a la campa&ntilde;a.'));
            die();
        }

        $fecha_actual = date('Y-m-d');
// valido que la fecha de inicio no sea menor a la fecha actual
        if ($this->_dateDiff($fecha_inicio, $fecha_actual) > 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'La fecha de inicio es menor a la fecha actual.'));
            die();
        }

// valido que la fecha de inicio no sea mayor a la fecha de fin
        if ($this->_dateDiff($fecha_inicio, $fecha_fin) < 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'La fecha de inicio es mayor a la fecha de fin de la campa&ntilde;a.'));
            die();
        }

// valido que la fecha de fin no sea menor a la fecha actual
        if ($this->_dateDiff($fecha_fin, $fecha_actual) > 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'La fecha de fin es menor a la fecha actual.'));
            die();
        }

// valido que la fecha de fin no sea menor a la fecha de inicio
        if ($this->_dateDiff($fecha_fin, $fecha_inicio) > 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'La fecha de fin es menor a la fecha de inicio de la campa&ntilde;a.'));
            die();
        }

// valido la fecha de fin
        if (strlen($fecha_fin) <= 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'Por favor ingrese la fecha de fin correspondiente a la campa&ntilde;a.'));
            die();
        }

        if ($unificar_campania == 'NINGUNA') {
            $campania_padre = '1';
        } else {
            $campania_padre = '0';
        }

// comienzo el ingreso en la tabla campania de la DB
        $data = array(
            'id_anunciante' => $id_anunciante,
            'id_cliente' => $cliente,
            'nombre' => $nombre_campania,
            'tipo_campania' => $tipo_campania2,
            'fecha_fin' => $fecha_fin,
            'fecha_inicio' => $fecha_inicio,
            'fecha_alta' => date('Y-m-d H:i:s'),
            'usuario_creador' => $this->tank_auth->get_user_id(),
            'creada_desde_anunciantes' => '1',
            'campania_padre' => $campania_padre,
            'moneda' => $this->user_data->moneda,
            'adserver' => $adserver,
            'empresa_campania' => '0'
        );

        if ($this->campanias->insertar_campania($data)) {
// si se ingreso la campania correctamente entonces obtengo el id de la campania creada y cargo los demas datos
            $id_campania = $this->db->insert_id();

            $data_campania = array('id_campania' => $id_campania);
            $this->campaniascontrol->insert_control($data_campania);

            // si se unifica con otra campania entonces las asocio
            if ($unificar_campania != 'NINGUNA' && $unificar_campania != 0) {
                $data_unificadas = array(
                    'id_campania_padre' => $unificar_campania
                    , 'id_campania_hija' => $id_campania
                );

                $this->campanias->insertar_campania_unificada($data_unificadas);
            }


            $newdata = array(
                'id_campania' => $id_campania
            );

            $this->session->set_userdata($newdata);

            $inversion_neta = number_format((float) trim($this->input->post('inversion')), 2, ',', '.'); // validado
// consulto si el anunciante esta registrado en appnexus y lo creo en caso de ser necesario
            $existe_anunciante = $this->anunciantes->get_anunciante_adserver_by_id($id_anunciante);
            if ($existe_anunciante->id_appnexus == NULL) {

                $nombre = $existe_anunciante->nombre;
                $email = $this->user_data->email;

                $anunciante_appnexus = $this->api->crear_anunciante_appnexus($nombre, $email);
                if ($anunciante_appnexus) {
                    unset($data);
                    $data = array('id_appnexus' => $anunciante_appnexus['id'], 'adserver_actual' => 1);

                    $this->anunciantes->update_anunciante_adserver($id_anunciante, $data);
                }
            }

            echo json_encode(array('validate' => TRUE, 'ok' => 'Campa&ntilde;a creada correctamente.', 'id_campania' => $id_campania, 'inversion_neta' => $inversion_neta));
        } else {
            echo json_encode(array('validate' => FALSE, 'error' => 'Ocurrio un error al intentar crear la campa&ntilde;a en la base de datos.'));
        }
    }

    function mostrar_formatos($tipo_campania, $id_campania = 0) {
        if ($tipo_campania == 'tradicional') {
            $formatos = $this->formatosdfp->get_tradicionales();
        } elseif ($tipo_campania == 'layer') {
            $formatos = $this->formatosdfp->get_formato_by_valor('i');
        } elseif ($tipo_campania == 'skin') {
            $formatos = $this->formatosdfp->get_formato_by_valor('v');
        } elseif ($tipo_campania == 'expandible') {
            $formatos = $this->formatosdfp->get_tradicionales();
        } elseif ($tipo_campania == 'facebook_like_ads') {
            $formatos = $this->formatosdfp->get_facebook_like();
        } elseif ($tipo_campania == 'twitter_timeline_ads') {
            $formatos = $this->formatosdfp->get_twitter_timeline();
        } elseif ($tipo_campania == 'video_zocalo') {
            $formatos = $this->formatosdfp->get_video_zocalo();
        } elseif ($tipo_campania == 'video_viral') {
            $formatos = $this->formatosdfp->get_video_viral();
        } elseif ($tipo_campania == 'video_banner') {
            $formatos = $this->formatosdfp->get_tradicionales();
        } elseif ($tipo_campania == 'pre_roll') {
            $formatos = $this->formatosdfp->get_pre_roll();
        } elseif ($tipo_campania == 'overlay') {
            $formatos = $this->formatosdfp->get_overlay();
        } elseif ($tipo_campania == 'video_banner') {
            $formatos = $this->formatosdfp->get_video_in_banner();
        } elseif ($tipo_campania == 'data') {
            $formatos = $this->formatosdfp->get_tradicionales();
        } elseif ($tipo_campania == 'sitios_movilizados') {
            $formatos = $this->formatosdfp->get_mobile();
        } elseif ($tipo_campania == 'impresiones_mobile') {
            $formatos = $this->formatosdfp->get_tradicionales();
        }

        if ($id_campania != 0) {
            $formatos_campanias = $this->campaniasformatos->get_formatos_by_campania($id_campania);
        }

        $data['formatos'] = $formatos;
        $data['formatos_campanias'] = $formatos_campanias;
        $data['tipo_campania'] = $tipo_campania;

        $this->load->view('mostrar_formatos', $data);
    }

    function insertar_campania_tercer_paso() {
// recibo todos los datos del post
        $id_campania = trim($this->input->post('id_campania')); // validado
        $segmentacion = trim($this->input->post('segmentacion')); // validado
        $id_canales_tematicos = trim($this->input->post('id_canales_tematicos'));
        $id_sitios = trim($this->input->post('id_sitios'));
        $formatos = trim($this->input->post('formatos'));

        $id_paises = trim($this->input->post('id_paises'));

        $type_DFP = trim($this->input->post('type_DFP'));
        $cant_dias = trim($this->input->post('cant_dias'));

        $frecuencia = trim($this->input->post('frecuencia'));

        $device_desktop = trim($this->input->post('device_desktop'));
        $device_tablet = trim($this->input->post('device_tablet'));
        $device_phone = trim($this->input->post('device_phone'));

// valido foramtos seleccionados para la campa&ntilde;a.
        if (strlen($formatos) <= 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'Por favor seleccione al menos un formato para la campa&ntilde;a.'));
            die();
        }

// valido que haya seleccionado al menos un pais
        if (strlen($id_paises) <= 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'Por favor seleccione al menos un pa&iacute;s correspondiente a la campa&ntilde;a.'));
            die();
        }

// borro todas las categorias, los sitios, los formatos, los
// sitios formatos y los paises de la campana para poder guardar
// los nuevos si es que los hay.
        $this->campaniascanalestematicos->delete_canales_by_campania($id_campania);
        $this->campaniassitios->delete_sitio_by_campania($id_campania);
        $this->campaniasformatos->delete_formatos_by_campania($id_campania);
        $this->campaniassitiosformatos->delete_formatos_by_campania($id_campania);
        $this->campaniaspaises->delete_paises_by_campania($id_campania);

        if ($segmentacion == 0)
            $segmentacion = 6;

// comienzo el ingreso en la tabla campania de la DB
        $data_update = array(
            'frecuencia' => $frecuencia,
            'segmentacion_id' => $segmentacion,
            'device_desktop' => $device_desktop,
            'device_tablet' => $device_tablet,
            'device_phone' => $device_phone,
        );

        $this->campanias->update_campania($id_campania, $data_update);

// separo los formatos seleccionados
        $formatos = explode(";", $formatos);

// segmentacion por Toda la red
        if ($segmentacion == 1) {
// traigo todos los sitios de nuestra red
            $sitios = $this->sites->get_all_sites();

// si hay sitios los agrego al array
            if ($sitios) {
                foreach ($sitios as $sitio) {
                    if ($sitio->user_id)
                        $arr_sitios[] = $sitio->id;
                }
            }
        }

// segmentacion por canal tematico
        if ($segmentacion == 2) {
// obtengo los canales seleccionados
            $canales_seleccionados = explode(";", $id_canales_tematicos);

// recorro los canales seleccionados
            for ($a = 0; $a < sizeof($canales_seleccionados); $a++) {
                if (!empty($canales_seleccionados[$a])) {
// ingreso los canales tematicos que soporta la campa&ntilde;a.
                    $data_campania_canales = array(
                        'id_campania' => $id_campania,
                        'id_canal_tematico' => $canales_seleccionados[$a],
                    );

// almaceno en la base de datos los canales que soporta la campa&ntilde;a
                    $this->campaniascanalestematicos->insert_campania_canal_tematico($data_campania_canales);

// traigo todos los sitios que soportan este canal para asociar la campa&ntilde;a con los sitios
                    $sitios = $this->sitescategories->get_all_sites_by_category($canales_seleccionados[$a]);

// si hay sitios los agrego al array para asociar la campa&ntilde;a con los sitios
                    if ($sitios) {
                        foreach ($sitios as $sitio) {
                            if ($sitio != NULL)
                                $arr_sitios[] = $sitio->id;
                        }
                    }
                }
            }
        }

// segmentacion por sitio especifico
        if ($segmentacion == 3) {
// obtengo los sitios seleccionados
            $sitios = explode(";", $id_sitios);

// si hay sitios los agrego al array
            if ($sitios) {
                foreach ($sitios as $sitio) {
                    if ($sitio)
                        $arr_sitios[] = $sitio;
                }
            }
        }

// asocio en la DB la campania con los sitios
        if (isset($arr_sitios)) {
            $in_sitios = '';
// elimino los sitios duplicados del array
            $sitios = array_values(array_unique($arr_sitios));
            for ($a = 0; $a < sizeof($sitios); $a++) {
// asocio los sitios a la campa&ntilde;a creada
                $data_campania_sitio = array('id_campania' => $id_campania, 'id_sitio' => $sitios[$a]);
                $this->campaniassitios->insert_campania_sitio($data_campania_sitio);

// asocio cada sitio a los formatos seleccionados
// recorro los formatos seleccionados
                for ($b = 0; $b < sizeof($formatos); $b++) {
                    if (!empty($formatos[$b])) {
                        $formatoX = explode(":", $formatos[$b]);
// retomo el id del formato seleccionado
                        $id_formato = $formatoX[0];
// asocio cada sitio a los formatos seleccionados
                        $data_campania_sitio_formato = array('id_campania' => $id_campania, 'id_sitio' => $sitios[$a], 'id_formato' => $id_formato);
                        $this->campaniassitiosformatos->insert_campania_sitio_formato($data_campania_sitio_formato);
                    }
                }

                //$site = $this->sites->get_site_by_id($sitios[$a]);
                //if ($site)
                //$in_sitios .= $site->id_site . ',';
            }

            // $in_sitios = trim($in_sitios, ',');
        }

// almaceno los formatos seleccionados para la campa&ntilde;a.
        for ($b = 0; $b < sizeof($formatos); $b++) {
            if (!empty($formatos[$b])) {
                $formato = explode("|", $formatos[$b]);

                $pagina_destino = $formato[1];

// retomo los datos de los formatos seleccionado
                $data_formato = array(
                    'id_campania' => $id_campania,
                    'id_formato' => $formato[0],
                    'pagina_destino' => $pagina_destino
                );

// ingreso los datos en la tabla
                $this->campaniasformatos->insert_campania_formato($data_formato);
            }
        }

// asocio los paises seleccionados con la campania creada.
        $partes_paises = explode(";", $id_paises);

        for ($m = 0; $m < sizeof($partes_paises); $m++) {
            if (!empty($partes_paises[$m])) {
                $data_campania_pais = array('id_campania' => $id_campania, 'id_pais' => $partes_paises[$m]);
                $this->campaniaspaises->insert_campania_pais($data_campania_pais);
            }
        }

        $totalImpressions = 999999999;
        $totalClicks = 999999999;

// contabilizo las impresiones de los sitios seleccionados y compruebo
        /*
          if ($segmentacion == 3) {
          $multiplicar = $this->constants->get_constant_by_id(ID_MULTIPLICAR_VOLUMEN);

          $inventario = $this->inventario_anunciantes->get_inventario_by_sites_appnexus($in_sitios);

          if ($inventario) {
          $totalImpressions = ($inventario->totalImpressions * $multiplicar->value);
          $totalClicks = ($inventario->totalClicks * $multiplicar->value);

          if ($type_DFP == 'STANDARD') {
          $totalImpressions *= $cant_dias;
          $totalClicks *= $cant_dias;
          }
          } else {
          $totalImpressions = 0;
          $totalClicks = 0;
          }
          } */


// PRECIO SUGERIDO ********************************************************
        $valor_sugerido2 = $valor_cpc_sugerido = $valor_sugerido = 0;
        $tipo_campania = $this->input->post('tipo_campania');
        /*
          if ($tipo_campania == 'tradicional') {
          // preparo los paises
          if ($id_paises) {
          $partes_paises = explode(';', $id_paises);

          for ($m = 0; $m < sizeof($partes_paises); $m++) {
          if (!empty($partes_paises[$m]))
          $id_paises = "'" . $partes_paises[$m] . "';";
          }

          $id_paises = str_replace(';', ',', trim($id_paises, ';'));
          }

          //preparo los canales tematicos
          if ($id_canales_tematicos == '') {
          // traigo todos los canales y los separo por ','
          $canales_tematicos = $this->get_canales_tematicos();
          $canales = '';
          foreach ($canales_tematicos as $canal)
          $canales .= $canal->id . ',';

          $id_canales_tematicos = trim($canales, ',');
          } else {
          $id_canales_tematicos = str_replace(';', ',', trim($id_canales_tematicos, ';'));
          }

          // traigo el valor mas alto
          $valor_sugerido = $this->valoressugeridos->get_valores($id_paises, $id_canales_tematicos);

          if ($valor_sugerido) {
          $valor_sugerido2 = $valor_sugerido->cpm_sugerido;

          if ($segmentacion == 3)
          $valor_sugerido2 = $valor_sugerido2 * 2;

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

          $valor_sugerido2 = $valor_sugerido2 * $cotizacion;
          }
          }

          $ctr_standard = $this->constants->get_constant_by_id(CTR_STANDARD);

          $valor_cpc_sugerido = $valor_sugerido2 / (($ctr_standard->value * 1000) / 100);
          }else {
          $valor_sugerido2 = 0;
          $valor_sugerido->id = 0;
          }
          } else if ($tipo_campania != 'tradicional' && $tipo_campania != 'data' && $tipo_campania != 'publinota') {
         *
         */

        if ($tipo_campania != 'data' && $tipo_campania != 'publinota') {
// preparo los paises
            if ($id_paises) {
                $partes_paises = explode(';', $id_paises);

                for ($m = 0; $m < sizeof($partes_paises); $m++) {
                    if (!empty($partes_paises[$m]))
                        $id_paises = "'" . $partes_paises[$m] . "';";
                }

                $id_paises = str_replace(';', ',', trim($id_paises, ';'));
            }

// consulto el ID del formato segun el tipo de campania solicitada
            $formato = $this->formatosdfp->get_formato_by_data_type($tipo_campania);

// consulto el tarifario del formato segun la segmentacion y la modalidad
// consulto primero el cpm
            if ($formato) {
                if (sizeof($formato) > 1) {
                    $valor_cpm = $this->tarifarios->get_by_formato_segmentacion_modalidad($formato[0]->id, $segmentacion, 'cpm', $id_paises);
                } else {
                    $valor_cpm = $this->tarifarios->get_by_formato_segmentacion_modalidad($formato->id, $segmentacion, 'cpm', $id_paises);
                }
            } else {
                $valor_cpm = NULL;
            }

            if (!$valor_cpm) {
                $valor_cpm = 0;
            } else {
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

                        $valor_cpm = $valor_cpm->valor_minimo * $cotizacion;
                    }
                }else {
                    $valor_cpm = $valor_cpm->valor_minimo;
                }
            }

            if ($formato) {
// consulto primero el cpv
                if (sizeof($formato) > 1) {
                    $valor_cpv = $this->tarifarios->get_by_formato_segmentacion_modalidad($formato[0]->id, $segmentacion, 'cpv', $id_paises);
                } else {
                    $valor_cpv = $this->tarifarios->get_by_formato_segmentacion_modalidad($formato->id, $segmentacion, 'cpv', $id_paises);
                }
            } else {
                $valor_cpv = NULL;
            }

            if (!$valor_cpv) {
                $valor_cpv = 0;
            } else {
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

                        $valor_cpv = $valor_cpv->valor_minimo * $cotizacion;
                    }
                }else {
                    $valor_cpv = $valor_cpv->valor_minimo;
                }
            }

            $valor_sugerido2 = $valor_cpm;
            $valor_cpc_sugerido = $valor_cpv;
            $valor_sugerido = 0;
        }


        if ($this->user_data->notacion == 0) {
            $valor_cpm_sugerido = number_format($valor_sugerido2, 2, '.', ',');
            $valor_cpc_sugerido = number_format($valor_cpc_sugerido, 2, '.', ',');
        } else if ($this->user_data->notacion == 1) {
            $valor_cpm_sugerido = number_format($valor_sugerido2, 2, ',', '.');
            $valor_cpc_sugerido = number_format($valor_cpc_sugerido, 2, ',', '.');
        }


        if (isset($valor_sugerido->id)) {
            $valor_sugerido = $valor_sugerido->id;
        } else {
            $valor_sugerido = 0;
        }

        echo json_encode(array(
            'validate' => TRUE,
            'ok' => 'Campa&ntilde;a creada correctamente.',
            'id_campania' => $id_campania,
            'totalImpressions' => $totalImpressions,
            'totalClicks' => $totalClicks,
            'valor_cpm_sugerido' => $valor_cpm_sugerido,
            'valor_cpc_sugerido' => $valor_cpc_sugerido,
            'id_valor_sugerido' => $valor_sugerido
        ));
    }

    function insertar_campania_cuarto_paso() {
// recibo todos los datos del post
        $id_campania = trim($this->input->post('id_campania'));
        $modalidad = trim($this->input->post('modalidad'));
        $valor = trim($this->input->post('valor'));
        $cantidad = trim($this->input->post('cantidad'));
        $inversion_neta = trim($this->input->post('inversion_neta'));

        $inversion_bruta = trim($this->input->post('inversion_bruta'));
        $descuento = trim($this->input->post('descuento'));
        $comision = trim($this->input->post('comision'));

        $type_DFP = trim($this->input->post('type_DFP'));

        if (strlen($valor) <= 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'Por favor ingrese un valor unitario.'));
            die();
        }

        if (strlen($cantidad) <= 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'Por favor ingrese la cantidad a consumir.'));
            die();
        }

        if (strlen($inversion_neta) <= 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'Por favor ingrese la inversion a consumir.'));
            die();
        }

        $update_campanias_formatos = array(
            'id_modalidad_compra' => $modalidad,
            'monto' => str_replace(',', '.', $valor)
        );

        $this->campaniasformatos->update_campania_formato($id_campania, $update_campanias_formatos);


        if ($inversion_bruta == 0)
            $inversion_bruta = $inversion_neta;

        $update_campanias = array(
            'modalidad_compra' => $modalidad,
            'valor_unidad' => str_replace(',', '.', $valor),
            'cantidad' => $cantidad,
            'inversion_neta' => $inversion_neta,
            'inversion_bruta' => $inversion_bruta,
            'descuento' => $descuento,
            'comision' => $comision,
            'type_DFP' => $type_DFP
        );

        $this->campanias->update_campania($id_campania, $update_campanias);

        echo json_encode(array('validate' => TRUE, 'ok' => 'Campa&ntilde;a creada correctamente.', 'id_campania' => $id_campania));
    }

    function insertar_campania_appnexus_finalizar() {
// recibo todos los datos del post
        $id_campania = trim($this->input->post('id_campania'));
        $id_cliente = trim($this->input->post('id_cliente'));
        $nombre_campania = trim($this->input->post('nombre_campania'));
        $camp = $this->campanias->get_campania_by_id($id_campania);
//$data_campania = array('id_campania' => $id_campania);
//$this->campaniascontrol->insert_control($data_campania);
// creo la campania en el adserver
        $alta_DFP = $this->crear_campania_AppNexus($id_campania);
        /*
          while (!$alta_DFP) {
          $alta_DFP = $this->crear_campania_DFP($id_campania);
          }
         */

        if (!$alta_DFP) {
            echo json_encode(array('validate' => FALSE, 'error' => 'Ocurrio un error al intentar crear la campa&ntilde;a en el adserver.'));
            die();
        }

// si se cargo todo correcto en DFP entonces cobro la campa&ntilde;a al anunciante

        $this->cobrar_campania($id_campania);

        /*
          // creo un nuevo movimiento de saldo descontando el balance
          $data_insert_pago = array(
          'id_anunciante' => $this->tank_auth->get_user_id(),
          'id_campania' => $id_campania,
          'debito' => $importe_a_cobrar,
          'balance' => ($this->user_data->limite_de_compra - $importe_a_cobrar),
          'moneda' => $this->user_data->moneda,
          'descripcion' => "Pago inicial de campa&ntilde;a " . $nombre_campania,
          'tipo_de_pago' => '2'
          );

          if (!$this->anunciantessaldos->insert_anunciante_saldo($data_insert_pago)) {
          echo json_encode(array('validate' => FALSE, 'error' => 'Ocurri&oacute; un error al intentar cobrar la campa&ntilde;a.'));
          die();
          }
         */


// creo un nuevo movimiento de saldo descontando el balance    
// ACA LO JODIDO! ******************************************************
        /*
          $limite_de_compra = $this->user_data->limite_de_compra;
          $saldo_prepago = $this->user_data->saldo_prepago;
          $saldo_prestamo = $this->user_data->saldo_prestamo;

          // descuento el total de la campa&ntilde;a del saldo prepago
          $restante_a_cobrar = $importe_a_cobrar - $saldo_prepago;

          // si todabia queda dinero a cobrar se lo descuento al prestamo
          if ($restante_a_cobrar > 0) {
          // cuanto consumio del prepago y prestamo la campa&ntilde;a
          $consumo_prepago = $saldo_prepago;
          $consumo_prestamo = $restante_a_cobrar;

          // no tiene mas saldo prepago
          $saldo_prepago = 0;

          $saldo_prestamo = $saldo_prestamo - $restante_a_cobrar;
          } else { // tiene mas plata de prepago que lo consumido en la campa&ntilde;a
          // cuanto consumio del prepago y prestamo la campa&ntilde;a
          $consumo_prepago = $importe_a_cobrar;
          $consumo_prestamo = 0;

          $saldo_prepago = 0 - $restante_a_cobrar;
          }

          // actualizo los saldos del anunciante
          $data_update = array(
          'limite_de_compra' => $limite_de_compra - $importe_a_cobrar,
          'saldo_prepago' => $saldo_prepago,
          'saldo_prestamo' => $saldo_prestamo
          );

          $this->anunciantes->update_anunciante($this->tank_auth->get_user_id(), $data_update);

          // guardo en el registro de la campa&ntilde;a cuanto consumio de cada saldo
          $update_campania = array(
          'consumo_saldo_prepago' => $consumo_prepago,
          'consumo_saldo_prestamo' => $consumo_prestamo,
          'moneda' => $this->user_data->moneda
          );

          $this->campanias->update_campania($id_campania, $update_campania);
         */
// FINAL DE LO JODIDO! *************************************************
// Asigno la campania a un traficker
        if ($camp->empresa_campania == 1) {
            $nuevo_traficker = USUARIO_SANTI;
        } else {
            // leo todos los trafickers disponibles y armo un array
            $trafickers_db = $this->admins->get_users_by_departamento(ID_DEPARTAMENTO_TRAFICKER);

// leo el ultimo traficker designado
            $ultima_campania = $this->campanias->get_last_campania();

            $nuevo_traficker = 0;

            if (sizeof($trafickers_db) > 2) {
// recorro todos los trafickers y si corresponde obtengo su indice
                foreach ($trafickers_db as $indice => $traficker) {
                    if ($traficker->id == $ultima_campania->usuario_implementa)
                        $nuevo_traficker = $indice + 1;
                }
// si existe un traficker mas en el listado ok, sino pongo en 0
                if (isset($trafickers_db[$nuevo_traficker])) {
                    $nuevo_traficker = $trafickers_db[$nuevo_traficker]->id;
                } else {
                    $nuevo_traficker = $trafickers_db[0]->id;
                }
            } else {
// recorro todos los trafickers y si corresponde obtengo su indice
                foreach ($trafickers_db as $indice => $traficker) {
                    if ($traficker->id != $ultima_campania->usuario_implementa)
                        $nuevo_traficker = $indice;
                }

// si existe un traficker mas en el listado ok, sino pongo en 0
                $nuevo_traficker = $trafickers_db[$nuevo_traficker]->id;
            }
        }

        $update_campania = array(
            'alta_finalizada' => 1,
            'usuario_implementa' => $nuevo_traficker
        );

        $this->campanias->update_campania($id_campania, $update_campania);


// MODIFICO EL VALOR SUGERIDO SI SE LO MERECE *****************************
        $id_valor_sugerido = trim($this->input->post('id_valor_sugerido'));
        $valor_cpm_sugerido = floatval(trim($this->input->post('valor_cpm_sugerido')));
        $valor_cpc_sugerido = floatval(trim($this->input->post('valor_cpc_sugerido')));
        $valor_unitario = floatval(trim($this->input->post('valor_unitario')));
        $modalidad = trim($this->input->post('modalidad'));

//echo $id_valor_sugerido . ' ** ' . $valor_cpm_sugerido . ' ** ' . $valor_cpc_sugerido . ' ** ' . $valor_unitario . ' ** ' . $modalidad;
        if ($camp->frecuencia != 'normal') {
            if ($id_valor_sugerido > 0) {
// si la modalidad es cpm comparo si es mas grande o no y guardo en caso de ser necesario
                if ($modalidad == 'cpm') {
                    if ($valor_unitario > $valor_cpm_sugerido) {
// sumo un centavo al valor ingresado y lo guardo
                        $valor_unitario = $valor_unitario + 0.01;

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

                                $valor_unitario = $valor_unitario / $cotizacion;
                            }
                        }

                        $data = array('cpm_sugerido' => $valor_unitario);

                        $this->valoressugeridos->update($id_valor_sugerido, $data);
                    }
                }else {
// si la modalidad es CPC calculo el valor de cada click con el CTR y lo guardo en caso de ser necesario

                    if ($valor_unitario > $valor_cpc_sugerido) {

//calculo en valor en base al CTR
                        $ctr_standard = $this->constants->get_constant_by_id(CTR_STANDARD);

                        $cantidad_click = (($ctr_standard->value * 1000) / 100);

                        $valor_unitario = $valor_unitario * $cantidad_click;

// sumo un centavo al valor ingresado y lo guardo
                        $valor_unitario = $valor_unitario + 0.01;

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

                                $valor_unitario = $valor_unitario / $cotizacion;
                            }
                        }

                        $data = array('cpm_sugerido' => $valor_unitario);

                        $this->valoressugeridos->update($id_valor_sugerido, $data);
                    }
                }
            }
        }

// enviar correo a media con los datos de la campania
        $this->crear_ticket_zendesk($id_campania);

        $this->historial_alta_campania($camp, $nombre_campania);

// inserto un comentario para ordenar todo
        $data_insert_comentario = array(
            'id_campania' => $id_campania,
            'comentario' => HISTORIAL_STRING . ' - Historial - ' . 1,
            'fecha_alta' => date('Y-m-j H:i:s', strtotime('-1 hour', strtotime(date('Y-m-d H:i:s'))))
        );
        $this->campaniashistorial->insert_comentario($data_insert_comentario);

        echo json_encode(array('validate' => TRUE, 'ok' => 'Campa&ntilde;a creada correctamente.', 'id_campania' => $id_campania));
    }

    function insertar_campania_finalizar() {
// recibo todos los datos del post
        $id_campania = trim($this->input->post('id_campania'));
        $id_cliente = trim($this->input->post('id_cliente'));
        $nombre_campania = trim($this->input->post('nombre_campania'));
        $camp = $this->campanias->get_campania_by_id($id_campania);
//$data_campania = array('id_campania' => $id_campania);
//$this->campaniascontrol->insert_control($data_campania);
// creo la campania en el adserver
        $alta_DFP = $this->crear_campania_AppNexus($id_campania, 'mf');
        /*
          while (!$alta_DFP) {
          $alta_DFP = $this->crear_campania_DFP($id_campania);
          }
         */

        if (!$alta_DFP) {
            echo json_encode(array('validate' => FALSE, 'error' => 'Ocurrio un error al intentar crear la campa&ntilde;a en el adserver.'));
            die();
        }

// si se cargo todo correcto en DFP entonces cobro la campa&ntilde;a al anunciante

        $this->cobrar_campania($id_campania);

        /*
          // creo un nuevo movimiento de saldo descontando el balance
          $data_insert_pago = array(
          'id_anunciante' => $this->tank_auth->get_user_id(),
          'id_campania' => $id_campania,
          'debito' => $importe_a_cobrar,
          'balance' => ($this->user_data->limite_de_compra - $importe_a_cobrar),
          'moneda' => $this->user_data->moneda,
          'descripcion' => "Pago inicial de campa&ntilde;a " . $nombre_campania,
          'tipo_de_pago' => '2'
          );

          if (!$this->anunciantessaldos->insert_anunciante_saldo($data_insert_pago)) {
          echo json_encode(array('validate' => FALSE, 'error' => 'Ocurri&oacute; un error al intentar cobrar la campa&ntilde;a.'));
          die();
          }
         */

// creo un nuevo movimiento de saldo descontando el balance        
// ACA LO JODIDO! ******************************************************
        /*
          $limite_de_compra = $this->user_data->limite_de_compra;
          $saldo_prepago = $this->user_data->saldo_prepago;
          $saldo_prestamo = $this->user_data->saldo_prestamo;

          // descuento el total de la campa&ntilde;a del saldo prepago
          $restante_a_cobrar = $importe_a_cobrar - $saldo_prepago;

          // si todabia queda dinero a cobrar se lo descuento al prestamo
          if ($restante_a_cobrar > 0) {
          // cuanto consumio del prepago y prestamo la campa&ntilde;a
          $consumo_prepago = $saldo_prepago;
          $consumo_prestamo = $restante_a_cobrar;

          // no tiene mas saldo prepago
          $saldo_prepago = 0;

          $saldo_prestamo = $saldo_prestamo - $restante_a_cobrar;
          } else { // tiene mas plata de prepago que lo consumido en la campa&ntilde;a
          // cuanto consumio del prepago y prestamo la campa&ntilde;a
          $consumo_prepago = $importe_a_cobrar;
          $consumo_prestamo = 0;

          $saldo_prepago = 0 - $restante_a_cobrar;
          }

          // actualizo los saldos del anunciante
          $data_update = array(
          'limite_de_compra' => $limite_de_compra - $importe_a_cobrar,
          'saldo_prepago' => $saldo_prepago,
          'saldo_prestamo' => $saldo_prestamo
          );

          $this->anunciantes->update_anunciante($this->tank_auth->get_user_id(), $data_update);

          // guardo en el registro de la campa&ntilde;a cuanto consumio de cada saldo
          $update_campania = array(
          'consumo_saldo_prepago' => $consumo_prepago,
          'consumo_saldo_prestamo' => $consumo_prestamo,
          'moneda' => $this->user_data->moneda
          );

          $this->campanias->update_campania($id_campania, $update_campania);
         */
// FINAL DE LO JODIDO! *************************************************
// Asigno la campania a un traficker
// leo todos los trafickers disponibles y armo un array
        $trafickers_db = $this->admins->get_users_by_departamento(ID_DEPARTAMENTO_TRAFICKER);

// leo el ultimo traficker designado
        $ultima_campania = $this->campanias->get_last_campania();

        $nuevo_traficker = 0;

        if (sizeof($trafickers_db) > 2) {
// recorro todos los trafickers y si corresponde obtengo su indice
            foreach ($trafickers_db as $indice => $traficker) {
                if ($traficker->id == $ultima_campania->usuario_implementa)
                    $nuevo_traficker = $indice + 1;
            }
// si existe un traficker mas en el listado ok, sino pongo en 0
            if (isset($trafickers_db[$nuevo_traficker])) {
                $nuevo_traficker = $trafickers_db[$nuevo_traficker]->id;
            } else {
                $nuevo_traficker = $trafickers_db[0]->id;
            }
        } else {
// recorro todos los trafickers y si corresponde obtengo su indice
            foreach ($trafickers_db as $indice => $traficker) {
                if ($traficker->id != $ultima_campania->usuario_implementa)
                    $nuevo_traficker = $indice;
            }

// si existe un traficker mas en el listado ok, sino pongo en 0
            $nuevo_traficker = $trafickers_db[$nuevo_traficker]->id;
        }

        $update_campania = array(
            'alta_finalizada' => 1,
            'usuario_implementa' => $nuevo_traficker,
            'usuario_alertas' => '40', //Romi
            'usuario_revisa' => '36'  //Gonza
        );

        $this->campanias->update_campania($id_campania, $update_campania);


// MODIFICO EL VALOR SUGERIDO SI SE LO MERECE *****************************
        $id_valor_sugerido = trim($this->input->post('id_valor_sugerido'));
        $valor_cpm_sugerido = floatval(trim($this->input->post('valor_cpm_sugerido')));
        $valor_cpc_sugerido = floatval(trim($this->input->post('valor_cpc_sugerido')));
        $valor_unitario = floatval(trim($this->input->post('valor_unitario')));
        $modalidad = trim($this->input->post('modalidad'));

//echo $id_valor_sugerido . ' ** ' . $valor_cpm_sugerido . ' ** ' . $valor_cpc_sugerido . ' ** ' . $valor_unitario . ' ** ' . $modalidad;
        if ($camp->frecuencia != 'normal') {
            if ($id_valor_sugerido > 0) {
// si la modalidad es cpm comparo si es mas grande o no y guardo en caso de ser necesario
                if ($modalidad == 'cpm') {
                    if ($valor_unitario > $valor_cpm_sugerido) {
// sumo un centavo al valor ingresado y lo guardo
                        $valor_unitario = $valor_unitario + 0.01;

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

                                $valor_unitario = $valor_unitario / $cotizacion;
                            }
                        }

                        $data = array('cpm_sugerido' => $valor_unitario);

                        $this->valoressugeridos->update($id_valor_sugerido, $data);
                    }
                }else {
// si la modalidad es CPC calculo el valor de cada click con el CTR y lo guardo en caso de ser necesario

                    if ($valor_unitario > $valor_cpc_sugerido) {

//calculo en valor en base al CTR
                        $ctr_standard = $this->constants->get_constant_by_id(CTR_STANDARD);

                        $cantidad_click = (($ctr_standard->value * 1000) / 100);

                        $valor_unitario = $valor_unitario * $cantidad_click;

// sumo un centavo al valor ingresado y lo guardo
                        $valor_unitario = $valor_unitario + 0.01;

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

                                $valor_unitario = $valor_unitario / $cotizacion;
                            }
                        }

                        $data = array('cpm_sugerido' => $valor_unitario);

                        $this->valoressugeridos->update($id_valor_sugerido, $data);
                    }
                }
            }
        }

        // enviar correo a media con los datos de la campania
        $this->crear_ticket_zendesk($id_campania);

        $this->historial_alta_campania($camp, $nombre_campania);


// inserto un comentario para ordenar todo
        $data_insert_comentario = array(
            'id_campania' => $id_campania,
            'comentario' => HISTORIAL_STRING . ' - Historial - ' . 1,
            'fecha_alta' => date('Y-m-j H:i:s', strtotime('-1 hour', strtotime(date('Y-m-d H:i:s'))))
        );
        $this->campaniashistorial->insert_comentario($data_insert_comentario);


        echo json_encode(array('validate' => TRUE, 'ok' => 'Campa&ntilde;a creada correctamente.', 'id_campania' => $id_campania));
    }

    function historial_alta_campania($camp, $nombre_campania) {
        $comentario_historial = '&bull; <b>Campa&ntilde;a creada ' . $nombre_campania . '</b>. <br />';

        // retomo el nombre del anunciante
        $anunciante = $this->anunciantes->get_anunciante_adserver_by_id($camp->id_anunciante);
        if ($anunciante) {
            $anunciante = $anunciante->nombre;
        } else {
            $anunciante = 'Anunciante inexistente en la base de datos.';
        }

        $comentario_historial .= '&bull; Anunciante: <b>' . $anunciante . '</b>. <br />';

        // retomo la campania padre
        $campania_padre = $this->campanias->get_campania_padre($camp->id);

        if ($campania_padre) {
            $data_campania_padre = $this->campanias->get_campania_by_id($campania_padre->id_campania_padre);

            if ($data_campania_padre) {
                $campania_padre = $data_campania_padre->nombre;
            } else {
                $campania_padre = ' - ';
            }
        } else {
            $campania_padre = ' - ';
        }

        $comentario_historial .= '&bull; Campa&ntilde;a padre: <b>' . $campania_padre . '</b>. <br />';

        $comentario_historial .= '&bull; Periodo: Desde el <b>' . MySQLDateToDateDatepicker($camp->fecha_inicio) . '</b> al <b>' . MySQLDateToDateDatepicker($camp->fecha_fin) . '</b> <br />';

        $frecuencia = 'Optimizada';
        if ($camp->frecuencia == '1x24') {
            $frecuencia = '1 impresi&oacute;n cada 24 horas';
        } else if ($camp->frecuencia == '2x24') {
            $frecuencia = '2 impresiones cada 24 horas';
        }

        $comentario_historial .= '&bull; Rotaci&oacute;n de anuncios: <b>' . $frecuencia . '</b> <br />';



// retomo los paises pertenecientes a las campa&ntilde;as.
        $campania_paises = $this->campaniaspaises->get_paises_by_campania($camp->id);

        if ($campania_paises) {
            $texto_paises = '';
            $a = 0;
            foreach ($campania_paises as $row) {
                $a++;

                $pais = $this->paises->get_pais_by_id($row->id_pais);
                if ($a == sizeof($campania_paises) - 1) {
                    $texto_paises = $texto_paises . $pais->descripcion . " y ";
                } else {
                    $texto_paises = $texto_paises . $pais->descripcion . ", ";
                }
            }

            $paises = substr($texto_paises, 0, - 2);
        }

        if ($paises == "Colombia, Colombia, Colombia, Colombia, Colombia, Colombia, Colombia, Colombia, Andorra, Albania, Armenia, Argentina, Austria, Australia, Bosnia and Herzegovina, Belgium, Bulgaria, Brazil, Belarus, Canada, Switzerland, Cote d'Ivoire, Chile, China, Colombia, Cyprus, Czech Republic, Germany, Denmark, Estonia, Espa&ntilde;a, Finland, France, United Kingdom, Georgia, Greece, Hong Kong, Croatia, Hungary, Ireland, Israel, India, Iceland, Italy, Japan, Republic of Korea, Lithuania, Luxembourg, Latvia, Macedonia, Montserrat, Mexico, Malaysia, Netherlands, Norway, New Zealand, Peru, Philippines, Portugal, Romania, Russian Federation, Sweden, Singapore, Slovenia, Slovakia, San Marino, Turkey, Taiwan, Ukraine, United States, Uruguay, Paraguay, Costa Rica, El Salvador, Republica Dominicana, Guatemala, Venezuela, Panama, Puerto Rico, Bolivia, Pakistan, Ecuador, Bangladesh, Nigeria, Kenya, Morocco, Uganda, Egipto, Honduras, Indonesia, Jamaica, Kuwait, Nicaragua, United Arab Emirates, Antigua and Barbuda, Netherlands Antilles, American Samoa, Azerbaijan, Barbados, Bahrain, Bermuda, Botswana, Belize, Cameroon, Dominica, Algeria, Federated States of Micronesia, Grenada, French Guiana, Ghana, Guinea, Guam, Iraq, Jordan, Saint Kitts and Nevis, Cayman Islands, Lebanon, Saint Lucia, Sri Lanka, Liberia, Libyan Arab Jamahiriya, Monaco, Moldova, Republic of, Northern Mariana Islands, Malta, Maldives, Nepal, Oman, Poland, Palestinian Territory, Qatar, Reunion, Saudi Arabia, Sierra Leone, Suriname, Turks and Caicos Islands, Thailand, Tunisia, Trinidad and Tobago, Tanzania, United Republic of, United States Minor Outlying Islands, Virgin Islands, British, Virgin Islands, U.S., Vietnam, Yemen, South Africa, Zambia, Zimbabwe, Afghanistan, Haiti, Aruba, Bahamas, Brunei Darussalam, Guadeloupe, Gibraltar, Mozambique, Senegal, Namibia, Equatorial Guinea, Angola, Cambodia, Gabon, Papua New Guinea, Kazakhstan, Madagascar, Timor-Leste, Congo, The Democratic Republic of the, Malvinas, Malawi, Martinique, Fiji, Mauritius, Mongolia, Tonga, Macao, Cape Verde, Niger, Ethiopia, Turkmenistan, Lesotho, Sao Tome and Principe, Vanuatu, Central African Republic, Cook Islands, Desconocido, Congo, Solomon Islands, Desconocido, Desconocido, Greenland, Swaziland, Palau, Saint Pierre and Miquelon, Faroe Islands, Desconocido, Somalia, Holy See (Vatican City State), Burundi, Anguilla, Rwanda, Mali, Burkina Faso, Kiribati, Togo, Uzbekistan, Mayotte, Liechtenstein, Gambia, Djibouti, French Polynesia, Benin, Saint Vincent and the Grenadines, Guyana, Mauritania, Lao People's Democratic Republic, Kyrgyzstan, New Caledonia, Andorra, Albania, Armenia, Argentina, Austria, Australia, Bosnia and Herzegovina, Belgium, Bulgaria, Brazil, Belarus, Canada, Switzerland, Cote d'Ivoire, Chile, China, Colombia, Cyprus, Czech Republic, Germany, Denmark, Estonia, Espa&ntilde;a, Finland, France, United Kingdom, Georgia, Greece, Hong Kong, Croatia, Hungary, Ireland, Israel, India, Iceland, Italy, Japan, Republic of Korea, Lithuania, Luxembourg, Latvia, Macedonia, Montserrat, Mexico, Malaysia, Netherlands, Norway, New Zealand, Peru, Philippines, Portugal, Romania, Russian Federation, Sweden, Singapore, Slovenia, Slovakia, San Marino, Turkey, Taiwan, Ukraine, United States, Uruguay, Paraguay, Costa Rica, El Salvador, Republica Dominicana, Guatemala, Venezuela, Panama, Puerto Rico, Bolivia, Pakistan, Ecuador, Bangladesh, Nigeria, Kenya, Morocco, Uganda, Egipto, Honduras, Indonesia, Jamaica, Kuwait, Nicaragua, United Arab Emirates, Antigua and Barbuda, Netherlands Antilles, American Samoa, Azerbaijan, Barbados, Bahrain, Bermuda, Botswana, Belize, Cameroon, Dominica, Algeria, Federated States of Micronesia, Grenada, French Guiana, Ghana, Guinea, Guam, Iraq, Jordan, Saint Kitts and Nevis, Cayman Islands, Lebanon, Saint Lucia, Sri Lanka, Liberia, Libyan Arab Jamahiriya, Monaco, Moldova, Republic of, Northern Mariana Islands, Malta, Maldives, Nepal, Oman, Poland, Palestinian Territory, Qatar, Reunion, Saudi Arabia, Sierra Leone, Suriname, Turks and Caicos Islands, Thailand, Tunisia, Trinidad and Tobago, Tanzania, United Republic of, United States Minor Outlying Islands, Virgin Islands, British, Virgin Islands, U.S., Vietnam, Yemen, South Africa, Zambia, Zimbabwe, Afghanistan, Haiti, Aruba, Bahamas, Brunei Darussalam, Guadeloupe, Gibraltar, Mozambique, Senegal, Namibia, Equatorial Guinea, Angola, Cambodia, Gabon, Papua New Guinea, Kazakhstan, Madagascar, Timor-Leste, Congo, The Democratic Republic of the, Malvinas, Malawi, Martinique, Fiji, Mauritius, Mongolia, Tonga, Macao, Cape Verde, Niger, Ethiopia, Turkmenistan, Lesotho, Sao Tome and Principe, Vanuatu, Central African Republic, Cook Islands, Desconocido, Congo, Solomon Islands, Desconocido, Desconocido, Greenland, Swaziland, Palau, Saint Pierre and Miquelon, Faroe Islands, Desconocido, Somalia, Holy See (Vatican City State), Burundi, Anguilla, Rwanda, Mali, Burkina Faso, Kiribati, Togo, Uzbekistan, Mayotte, Liechtenstein, Gambia, Djibouti, French Polynesia, Benin, Saint Vincent and the Grenadines, Guyana, Mauritania, Lao People's Democratic Republic, Kyrgyzstan, New Caledonia, Andorra, Albania, Armenia, Argentina, Austria, Australia, Bosnia and Herzegovina, Belgium, Bulgaria, Brazil, Belarus, Canada, Switzerland, Cote d'Ivoire, Chile, China, Colombia, Cyprus, Czech Republic, Germany, Denmark, Estonia, Espa&ntilde;a, Finland, France, United Kingdom, Georgia, Greece, Hong Kong, Croatia, Hungary, Ireland, Israel, India, Iceland, Italy, Japan, Republic of Korea, Lithuania, Luxembourg, Latvia, Macedonia, Montserrat, Mexico, Malaysia, Netherlands, Norway, New Zealand, Peru, Philippines, Portugal, Romania, Russian Federation, Sweden, Singapore, Slovenia, Slovakia, San Marino, Turkey, Taiwan, Ukraine, United States, Uruguay, Paraguay, Costa Rica, El Salvador, Republica Dominicana, Guatemala, Venezuela, Panama, Puerto Rico, Bolivia, Pakistan, Ecuador, Bangladesh, Nigeria, Kenya, Morocco, Uganda, Egipto, Honduras, Indonesia, Jamaica, Kuwait, Nicaragua, United Arab Emirates, Antigua and Barbuda, Netherlands Antilles, American Samoa, Azerbaijan, Barbados, Bahrain, Bermuda, Botswana, Belize, Cameroon, Dominica, Algeria, Federated States of Micronesia, Grenada, French Guiana, Ghana, Guinea, Guam, Iraq, Jordan, Saint Kitts and Nevis, Cayman Islands, Lebanon, Saint Lucia, Sri Lanka, Liberia, Libyan Arab Jamahiriya, Monaco, Moldova, Republic of, Northern Mariana Islands, Malta, Maldives, Nepal, Oman, Poland, Palestinian Territory, Qatar, Reunion, Saudi Arabia, Sierra Leone, Suriname, Turks and Caicos Islands, Thailand, Tunisia, Trinidad and Tobago, Tanzania, United Republic of, United States Minor Outlying Islands, Virgin Islands, British, Virgin Islands, U.S., Vietnam, Yemen, South Africa, Zambia, Zimbabwe, Afghanistan, Haiti, Aruba, Bahamas, Brunei Darussalam, Guadeloupe, Gibraltar, Mozambique, Senegal, Namibia, Equatorial Guinea, Angola, Cambodia, Gabon, Papua New Guinea, Kazakhstan, Madagascar, Timor-Leste, Congo, The Democratic Republic of the, Malvinas, Malawi, Martinique, Fiji, Mauritius, Mongolia, Tonga, Macao, Cape Verde, Niger, Ethiopia, Turkmenistan, Lesotho, Sao Tome and Principe, Vanuatu, Central African Republic, Cook Islands, Desconocido, Congo, Solomon Islands, Desconocido, Desconocido, Greenland, Swaziland, Palau, Saint Pierre and Miquelon, Faroe Islands, Desconocido, Somalia, Holy See (Vatican City State), Burundi, Anguilla, Rwanda, Mali, Burkina Faso, Kiribati, Togo, Uzbekistan, Mayotte, Liechtenstein, Gambia, Djibouti, French Polynesia, Benin, Saint Vincent and the Grenadines, Guyana, Mauritania, Lao People's Democratic Republic, Kyrgyzstan, New Caledonia, Andorra, Albania, Armenia, Argentina, Austria, Australia, Bosnia and Herzegovina, Belgium, Bulgaria, Brazil, Belarus, Canada, Switzerland, Cote d'Ivoire, Chile, China, Colombia, Cyprus, Czech Republic, Germany, Denmark, Estonia, Espa&ntilde;a, Finland, France, United Kingdom, Georgia, Greece, Hong Kong, Croatia, Hungary, Ireland, Israel, India, Iceland, Italy, Japan, Republic of Korea, Lithuania, Luxembourg, Latvia, Macedonia, Montserrat, Mexico, Malaysia, Netherlands, Norway, New Zealand, Peru, Philippines, Portugal, Romania, Russian Federation, Sweden, Singapore, Slovenia, Slovakia, San Marino, Turkey, Taiwan, Ukraine, United States, Uruguay, Paraguay, Costa Rica, El Salvador, Republica Dominicana, Guatemala, Venezuela, Panama, Puerto Rico, Bolivia, Pakistan, Ecuador, Bangladesh, Nigeria, Kenya, Morocco, Uganda, Egipto, Honduras, Indonesia, Jamaica, Kuwait, Nicaragua, United Arab Emirates, Antigua and Barbuda, Netherlands Antilles, American Samoa, Azerbaijan, Barbados, Bahrain, Bermuda, Botswana, Belize, Cameroon, Dominica, Algeria, Federated States of Micronesia, Grenada, French Guiana, Ghana, Guinea, Guam, Iraq, Jordan, Saint Kitts and Nevis, Cayman Islands, Lebanon, Saint Lucia, Sri Lanka, Liberia, Libyan Arab Jamahiriya, Monaco, Moldova, Republic of, Northern Mariana Islands, Malta, Maldives, Nepal, Oman, Poland, Palestinian Territory, Qatar, Reunion, Saudi Arabia, Sierra Leone, Suriname, Turks and Caicos Islands, Thailand, Tunisia, Trinidad and Tobago, Tanzania, United Republic of, United States Minor Outlying Islands, Virgin Islands, British, Virgin Islands, U.S., Vietnam, Yemen, South Africa, Zambia, Zimbabwe, Afghanistan, Haiti, Aruba, Bahamas, Brunei Darussalam, Guadeloupe, Gibraltar, Mozambique, Senegal, Namibia, Equatorial Guinea, Angola, Cambodia, Gabon, Papua New Guinea, Kazakhstan, Madagascar, Timor-Leste, Congo, The Democratic Republic of the, Malvinas, Malawi, Martinique, Fiji, Mauritius, Mongolia, Tonga, Macao, Cape Verde, Niger, Ethiopia, Turkmenistan, Lesotho, Sao Tome and Principe, Vanuatu, Central African Republic, Cook Islands, Desconocido, Congo, Solomon Islands, Desconocido, Desconocido, Greenland, Swaziland, Palau, Saint Pierre and Miquelon, Faroe Islands, Desconocido, Somalia, Holy See (Vatican City State), Burundi, Anguilla, Rwanda, Mali, Burkina Faso, Kiribati, Togo, Uzbekistan, Mayotte, Liechtenstein, Gambia, Djibouti, French Polynesia, Benin, Saint Vincent and the Grenadines, Guyana, Mauritania, Lao People's Democratic Republic, Kyrgyzstan y New Caledonia") {
            $paises = 'Todos los paises';
        }

        $comentario_historial .= '&bull; Paises: <b>' . $paises . '</b> <br />';


        // retomo el nombre de la segmentacion
        $segmentacion = $this->segmentacion->get_segmentacion_by_id($camp->segmentacion_id);
        if ($segmentacion) {
            $segmentacion = $segmentacion->descripcion;
        } else {
            $segmentacion = 'Nombre de segmentaci&oacute;n inexistente en la base de datos.';
        }

        $comentario_historial .= '&bull; Segmentaci&oacute;n: <b>' . $segmentacion . '</b> <br />';


        if ($camp->empresa_campania == 0) {
// retomo los sitios o canales tematicos seg&uacute;n la segmentaci&oacute;n de la campa&ntilde;a.
            if ($camp->segmentacion_id == 2) {
                $texto_canales = '';
// obtengo los canales tematicos seleccionados
                $canales = $this->campaniascanalestematicos->get_canales_tematicos_by_campania($camp->id);
// recorro por cada canal obtengo su nombre desde la base.
                if ($canales) {
                    foreach ($canales as $canal) {
//obtengo los datos del canal de la BD
                        $data_canal = $this->categorias->get_categoria_by_id($canal->id_canal_tematico);
                        if ($a == sizeof($canales) - 1) {
                            $texto_canales = $texto_canales . $canal->descripcion . " y ";
                        } else {
                            $texto_canales = $texto_canales . $canal->descripcion . ", ";
                        }

                        $texto_canales = substr($texto_canales, 0, - 2);
                    }
                } else {
                    $texto_canales = 'No se encontraron canales tem&aacute;ticos asociados a la campa&ntilde;a.';
                }

                $comentario_historial .= '&bull; Canales tem&aacute;ticos: <b>' . $texto_canales . '</b> <br />';
            } else if ($camp->segmentacion_id == 3) {
// selecciono todos los sitios correspondientes a la camapania
                $sitios = $this->campaniassitios->get_sitios_by_campania($camp->id);

                $texto_sitios = '';

                if ($sitios) {
                    foreach ($sitios as $site) {
// de cada sitio obtengo su nombre y lo guardo en el array para pasar a la vista.
                        $sitio = $this->sites->get_site_by_id($site->id_sitio);
                        $nombre_sitio = $sitio->nombre_appnexus;
                        if ($nombre_sitio == '')
                            $nombre_sitio = $sitio->nombre_dfp;

                        if ($a == sizeof($sitios) - 1) {
                            $texto_sitios = $texto_sitios . $nombre_sitio . " y ";
                        } else {
                            $texto_sitios = $texto_sitios . $nombre_sitio . ", ";
                        }
                    }

                    $texto_sitios = substr($texto_sitios, 0, - 2);
                } else {
                    $texto_sitios = 'No se encontraron sitios asociados a la campa&ntilde;a.';
                }

                $comentario_historial .= '&bull; Sitios especificos: <b>' . $texto_sitios . '</b> <br />';
            }
        }


        if ($camp->empresa_campania == 1) {
            // selecciono las audiencias de la campania
            $audiencias = $this->campanias->get_audiencias_by_campania($camp->id);

            $texto_audiencias = '';
            if ($audiencias) {
                foreach ($audiencias as $audiencia) {
                    if ($a == sizeof($audiencia) - 1) {
                        $texto_audiencias = $texto_audiencias . $audiencia->name . " y ";
                    } else {
                        $texto_audiencias = $texto_audiencias . $audiencia->name . ", ";
                    }
                }

                $texto_audiencias = substr($texto_audiencias, 0, - 2);
            } else {
                $texto_audiencias = 'No se encontraron audiencias asociadas a la campa&ntilde;a.';
            }

            $comentario_historial .= '&bull; Audiencias: <b>' . $texto_audiencias . '</b> <br />';
        }


        $comentario_historial .= '&bull; Modalidad de compra: <b>' . strtoupper($camp->modalidad_compra) . '</b> <br />';
        $comentario_historial .= '&bull; Cantidad diaria: <b>' . $camp->cantidad . '</b> <br />';
        $comentario_historial .= '&bull; Valor CPM/CPC/CPA: <b>' . $camp->valor_unidad . '</b> <br />';
        $comentario_historial .= '&bull; Descuento: <b>' . $camp->descuento . '</b> <br />';
        $comentario_historial .= '&bull; Comisi&oacute;n: <b>' . $camp->comision . '</b> <br />';


        // creo el comentario en el historial.
        $this->insert_historial($camp->id, 1, $comentario_historial);
    }

    function crear_ticket_zendesk($id_campania, $tipo = 'nuevo') {

        if (ENVIRONMENT != 'production' && ENVIRONMENT != 'testing')
            return FALSE;

        $campania = $this->campanias->get_campania_by_id($id_campania);

        if (!$campania) {
            return false;
        } else {

            if ($tipo == 'nuevo') {

                $traficker = $this->admins->get_user_by_id($campania->usuario_implementa, 1);

                $copia = $traficker->email;

                $titulo = 'Nueva campaña creada: ' . $campania->nombre . ' ** ' . $traficker->email;
                $contenido = '<p>Se cre&oacute; la siguiente campa&ntilde;a:' . "</br>\n</p>";
            } else if ($tipo == 'modificar') {

                $traficker = $this->admins->get_user_by_id($campania->usuario_implementa, 1);

                $copia = $traficker->email;

                $titulo = 'Campa&ntilde;a modificada: ' . $campania->nombre . ' ** ' . $traficker->email;
                $contenido = '<p>Se modific&oacute; la siguiente campa&ntilde;a:' . "</br>\n</p>";
            } else if ($tipo == 'duplicar') {
                $titulo = 'Campa&ntilde;a duplicada: ' . $campania->nombre;
                $contenido = '<p>Se duplic&oacute; la siguiente campa&ntilde;a:' . "</br>\n</p>";
            } else if ($tipo == 'pausar') {
                $traficker = $this->admins->get_user_by_id($campania->usuario_implementa, 1);

                $copia = $traficker->email;

                $titulo = 'Campa&ntilde;a pausada: ' . $campania->nombre . ' ** ' . $traficker->email;
                $contenido = '<p>Se paus&oacute; la siguiente campa&ntilde;a:' . "</br>\n</p>";
            }

            if ($campania->campania_padre == 0) {
                $campania_padre = $this->campania_padre($campania->id);

                if ($campania_padre) {
                    $data_campania_padre = $this->campanias->get_campania_by_id($campania_padre->id_campania_padre);

                    if ($data_campania_padre) {
                        $contenido .= '<p>Campa&ntilde;a unificada con: ' . $data_campania_padre->nombre . "</br>\n</p>";
                    }
                }
            }

            $contenido .= '<p>Nombre de la campa&ntilde;a: ' . $campania->nombre . "</br>\n</p>";

// segmentacion de la campania
            switch ($campania->segmentacion_id) {
                case 1:
                    $segmentacion = 'Toda la red';
                    break;
                case 2:
                    $segmentacion = 'Canales tem&aacute;ticos';
                    break;
                case 3:
                    $segmentacion = 'Sitios especificos';
                    break;
                default:
                    $segmentacion = 'Toda la red';
                    break;
            }

            $contenido .= '<p>Segmentaci&oacute;n: ' . $segmentacion . "</br>\n</p>";

            $contenido .= '<p>Periodo: desde el ' . MySQLDateToDate($campania->fecha_inicio) . ' al ' . MySQLDateToDate($campania->fecha_fin) . "</br>\n</br>\n</p>";

            if ($campania->frecuencia == 'NORMAL') {
                $frecuencia = 'Optimizada';
            } else if ($campania->frecuencia == '1x24') {
                $frecuencia = '1 impresi&oacute;n cada 24 horas';
            } else if ($campania->frecuencia == '2x24') {
                $frecuencia = '2 impresiones cada 24 horas';
            }

            $contenido .= '<p>Rotaci&oacute;n de anuncios: ' . $frecuencia . "</br>\n</br>\n</p>";

// retomo los paises pertenecientes a las campa&ntilde;as.
            $campania_paises = $this->campaniaspaises->get_paises_by_campania($campania->id);
            if ($campania_paises) {
                $texto_paises = '';
                $a = 0;
                foreach ($campania_paises as $row) {
                    $a++;

                    $pais = $this->paises->get_pais_by_id($row->id_pais);
                    if ($a == sizeof($campania_paises) - 1) {
                        $texto_paises = $texto_paises . $pais->descripcion . " y ";
                    } else {
                        $texto_paises = $texto_paises . $pais->descripcion . ", ";
                    }
                }

                $contenido .= '<p>Paises: ' . substr($texto_paises, 0, - 2) . "</br>\n</br>\n</p>";
            }

// obtengo los formatos de la campa&ntilde;a
            $formatos = $this->campaniasformatos->get_formatos_by_campania($campania->id);
            if ($formatos) {
                $texto_formatos = '';
                $a = 0;
                foreach ($formatos as $row) {
                    $a++;

                    $formato = $this->formatosdfp->get_formato_by_id($row->id_formato);
                    if ($a == sizeof($formatos) - 1) {
                        $texto_formatos = $texto_formatos . $formato->descripcion . " y ";
                    } else {
                        $texto_formatos = $texto_formatos . $formato->descripcion . ", ";
                    }
                }

                $contenido .= '<p>Formatos: ' . substr($texto_formatos, 0, - 2) . "</br>\n</br>\n</p>";
            }

            $contenido .= '<p>Modalidad de compra: ' . strtoupper($campania->modalidad_compra) . "</br>\n</br>\n</p>";

            $contenido .= '<p>Valor ' . strtoupper($campania->modalidad_compra) . ': ' . number_format($campania->valor_unidad, 2, ',', '.') . ' ' . $campania->moneda . "</br>\n</br>\n</p>";

            $contenido .= '<p>Cantidad: ' . $campania->cantidad . "</br>\n</br>\n</p>";


            if ($campania->type_DFP == 'STANDARD') {
                $contenido .= '<p>Inversi&oacute;n total neta: ' . number_format($campania->inversion_neta, 3, ',', '.') . ' ' . $campania->moneda . "</br>\n</br>\n</p>";
            } else {
                $campania->inversion_neta = $campania->inversion_neta * $this->_dateDiff($campania->fecha_inicio, $campania->fecha_fin);
                $contenido .= '<p>Inversi&oacute;n total neta: ' . number_format($campania->inversion_neta, 3, ',', '.') . ' ' . $campania->moneda . "</br>\n</br>\n</p>";
            }

            switch (base_url()) {
                case 'http://devanunciantes2.mediafem.com/':
                    $host = 'http://devadmin2.mediafem.com/';
                    break;
                case 'https://devanunciantes2.mediafem.com/':
                    $host = 'https://devadmin2.mediafem.com/';
                    break;
                case 'http://devanunciantes.mediafem.com/':
                    $host = 'http://devadmin.mediafem.com/';
                    break;
                case 'https://devanunciantes.mediafem.com/':
                    $host = 'https://devadmin.mediafem.com/';
                    break;
                case 'http://testanunciantes.mediafem.com/':
                    $host = 'http://testadmin.mediafem.com/';
                    break;
                case 'https://testanunciantes.mediafem.com/':
                    $host = 'https://testadmin.mediafem.com/';
                    break;
                case 'http://anunciantes.mediafem.com/':
                    $host = 'http://admin.mediafem.com/';
                    break;
                case 'https://anunciantes.mediafem.com/':
                    $host = 'https://admin.mediafem.com/';
                    break;
                default:
                    break;
            }
            $contenido .= '<p>Puede ver m&aacute;s datos de la campa&ntilde;a desde <a href="' . $host . 'campanias/ver/' . $campania->id . '">aqu&iacute;</a></p>';

            /*
              if (strpos($_SERVER['HTTP_USER_AGENT'], 'testMediaFem') === FALSE) {
              return $this->_send_email($titulo, $contenido, $copia);
              } else {
              return $this->_send_email_to('test.mediafem@gmail.com', $titulo, $contenido, $copia);
              }
             */

            if (ENVIRONMENT == 'production') {
                if (strpos($_SERVER['HTTP_USER_AGENT'], 'testMediaFem') === FALSE) {

                    $usuario = $this->users->get_user_by_id($this->tank_auth->get_user_id(), 1);
                    if ($usuario->country == "MX") {
                        $this->_send_email_to('nathalie.mercado@mediafem.com', $titulo, $contenido);
                        $this->_send_email_to('sc@mediafem.com', $titulo, $contenido);
                        $this->_send_email_to('1459765254@mediafem.glip.com', $titulo, $contenido);
                    }
                    return $this->_send_email($titulo, $contenido, $copia);
                } else {
                    $this->_send_email_to('test.mediafem@gmail.com', $titulo, $contenido);
                }
            }
        }
    }

    function insertar() {
// recibo todos los datos del post
        $id_anunciante = trim($this->input->post('id_anunciante')); // validado
        $nombre_campania = trim($this->input->post('nombre_campania')); // validado
        $segmentacion = trim($this->input->post('segmentacion')); // validado
        $id_canales_tematicos = trim($this->input->post('id_canales_tematicos'));
        $id_sitios = trim($this->input->post('id_sitios'));
        $formatos = trim($this->input->post('formatos'));
        $id_paises = trim($this->input->post('id_paises'));

        $fecha_inicio = trim($this->input->post('fecha_inicio')); // validado
        $fecha_fin = trim($this->input->post('fecha_fin')); // validado

        list($dia_desde, $mes_desde, $anio_desde) = explode("-", $fecha_inicio);
        list($dia_hasta, $mes_hasta, $anio_hasta) = explode("-", $fecha_fin);
        $fecha_inicio = $anio_desde . "-" . $mes_desde . "-" . $dia_desde . ' 00:00:00';
        $fecha_fin = $anio_hasta . "-" . $mes_hasta . "-" . $dia_hasta . ' 23:59:59';

// valido los datos ingresados
// valido anunciante
        if ($id_anunciante == 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'Por favor seleccione un anunciante para la campa&ntilde;a.'));
            die();
        }

// valido nombre de campa&ntilde;a.
        if (strlen($nombre_campania) <= 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'Por favor ingrese un nombre para la campa&ntilde;a.'));
            die();
        }

// valido foramtos seleccionados para la campa&ntilde;a.
        if (strlen($formatos) <= 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'Por favor seleccione al menos un formato para la campa&ntilde;a.'));
            die();
        }

// valido que haya seleccionado al menos un pais
        if (strlen($id_paises) <= 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'Por favor seleccione al menos un pa&iacute;s correspondiente a la campa&ntilde;a.'));
            die();
        }

// valido la fecha de inicio
        if (strlen($fecha_inicio) <= 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'Por favor ingrese la fecha de inicio correspondiente a la campa&ntilde;a.'));
            die();
        }

// valido la fecha de fin
        if (strlen($fecha_fin) <= 0) {
            echo json_encode(array('validate' => FALSE, 'error' => 'Por favor ingrese la fecha de fin correspondiente a la campa&ntilde;a.'));
            die();
        }

// comienzo el ingreso en la tabla campania de la DB
        $data = array(
            'id_anunciante' => $id_anunciante,
            'nombre' => $nombre_campania,
            'segmentacion_id' => $segmentacion,
            'fecha_alta' => date('Y-m-d H:i:s'),
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'usuario_creador' => $this->tank_auth->get_user_id(),
            'creada_desde_anunciantes' => '1'
        );

        if ($this->campanias->insertar_campania($data)) {
// si se ingreso la campania correctamente entonces obtengo el id de la campania creada y cargo los demas datos
            $id_campania = $this->db->insert_id();

// separo los formatos seleccionados
            $formatos = explode(";", $formatos);

// segmentacion por Toda la red
            if ($segmentacion == 1) {
// traigo todos los sitios de nuestra red
                $sitios = $this->sites->get_all_sites();

// si hay sitios los agrego al array
                if ($sitios) {
                    foreach ($sitios as $sitio) {
                        if ($sitio->user_id)
                            $arr_sitios[] = $sitio->id;
                    }
                }
            }

// segmentacion por canal tematico
            if ($segmentacion == 2) {
// obtengo los canales seleccionados
                $canales_seleccionados = explode(";", $id_canales_tematicos);

// recorro los canales seleccionados
                for ($a = 0; $a < sizeof($canales_seleccionados); $a++) {
                    if (!empty($canales_seleccionados[$a])) {
// ingreso los canales tematicos que soporta la campa&ntilde;a.
                        $data_campania_canales = array(
                            'id_campania' => $id_campania,
                            'id_canal_tematico' => $canales_seleccionados[$a],
                        );

// almaceno en la base de datos los canales que soporta la campa&ntilde;a
                        $this->campaniascanalestematicos->insert_campania_canal_tematico($data_campania_canales);

// traigo todos los sitios que soportan este canal para asociar la campa&ntilde;a con los sitios
                        $sitios = $this->sitescategories->get_all_sites_by_category($canales_seleccionados[$a]);

// si hay sitios los agrego al array para asociar la campa&ntilde;a con los sitios
                        if ($sitios) {
                            foreach ($sitios as $sitio) {
                                if ($sitio != NULL)
                                    $arr_sitios[] = $sitio->id;
                            }
                        }
                    }
                }
            }

// segmentacion por sitio especifico
            if ($segmentacion == 3) {
// obtengo los sitios seleccionados
                $sitios = explode(";", $id_sitios);

// si hay sitios los agrego al array
                if ($sitios) {
                    foreach ($sitios as $sitio) {
                        if ($sitio)
                            $arr_sitios[] = $sitio;
                    }
                }
            }


// asocio en la DB la campania con los sitios
            if (isset($arr_sitios)) {
// elimino los sitios duplicados del array
                $sitios = array_values(array_unique($arr_sitios));
                for ($a = 0; $a < sizeof($sitios); $a++) {
// asocio los sitios a la campa&ntilde;a creada
                    $data_campania_sitio = array('id_campania' => $id_campania, 'id_sitio' => $sitios[$a]);
                    $this->campaniassitios->insert_campania_sitio($data_campania_sitio);

// asocio cada sitio a los formatos seleccionados
// recorro los formatos seleccionados
                    for ($b = 0; $b < sizeof($formatos); $b++) {
                        if (!empty($formatos[$b])) {
                            $formatoX = explode(":", $formatos[$b]);
// retomo el id del formato seleccionado
                            $id_formato = $formatoX[0];
// asocio cada sitio a los formatos seleccionados
                            $data_campania_sitio_formato = array('id_campania' => $id_campania, 'id_sitio' => $sitios[$a], 'id_formato' => $id_formato);
                            $this->campaniassitiosformatos->insert_campania_sitio_formato($data_campania_sitio_formato);
                        }
                    }
                }
            }


// almaceno los formatos seleccionados para la campa&ntilde;a.
            for ($b = 0; $b < sizeof($formatos); $b++) {
                if (!empty($formatos[$b])) {
                    $formato = explode(":", $formatos[$b]);
// retomo los datos de los formatos seleccionado
                    $data_formato = array(
                        'id_campania' => $id_campania,
                        'id_formato' => $formato[0],
                        'id_modalidad_compra' => strtolower($formato[1]),
                        'cantidad' => $formato[2],
                        'monto' => $formato[3]
                    );

                    $data_formato['monto'] = trim($data_formato['monto'], "&nbsp;");
                    $data_formato['monto'] = str_replace(" ", "", $data_formato['monto']);
                    $data_formato['monto'] = htmlentities($data_formato['monto']);
                    $data_formato['monto'] = str_replace("&iuml;&raquo;&iquest;", "", $data_formato['monto']);

// ingreso los datos en la tabla
                    $this->campaniasformatos->insert_campania_formato($data_formato);
                }
            }

// asocio los paises seleccionados con la campania creada.
            $partes_paises = explode(";", $id_paises);

            for ($m = 0; $m < sizeof($partes_paises); $m++) {
                if (!empty($partes_paises[$m])) {
                    $data_campania_pais = array('id_campania' => $id_campania, 'id_pais' => $partes_paises[$m]);
                    $this->campaniaspaises->insert_campania_pais($data_campania_pais);
                }
            }

// doy de alta la campa&ntilde;a en la tabla de controles
            unset($data_campania);
            $data_campania = array('id_campania' => $id_campania);
            $this->campaniascontrol->insert_control($data_campania);


// creo la campania en el adserver
            $alta_DFP = $this->crear_campania_DFP($id_campania);


            if (!$alta_DFP) {
                echo json_encode(array('validate' => FALSE, 'error' => 'Ocurrio un error al intentar crear la campa&ntilde;a en el adserver.'));
                die();
            }

//Cobrar campa&ntilde;a
            /*
              $data_pago = null;

              $tarjeta_usuario = $this->usuariostarjetasdecredito->get_tarjetas_de_credito_por_usuario($this->tank_auth->get_user_id());
              $usuario = $this->users->get_user_by_id($this->tank_auth->get_user_id(), 1);

              $data_pago['tipo_tarjeta'] = $tarjeta_usuario->id_tipo_tarjeta;
              $data_pago['nro_tarjeta'] = $tarjeta_usuario->nro_tarjeta;
              $data_pago['ccv'] = $tarjeta_usuario->ccv;
              $data_pago['mes_expiracion'] = $tarjeta_usuario->mes_expiracion;
              $data_pago['anio_expiracion'] = $tarjeta_usuario->anio_expiracion;
              $data_pago['email_usuario'] = $usuario->email;
              $data_pago['descripcion_pago'] = "Pago inicial de campa&ntilde;a " . $nombre_campania;
              $data_pago['id_usuario'] = $this->tank_auth->get_user_id();

              if (!$this->ejecutar_pago_directo($data_pago)) {
              echo json_encode(array('validate' => FALSE, 'error' => 'Ocurri&oacute; un error al intentar cobrar la campa&ntilde;a.'));
              die();
              }
             *
             */

            echo json_encode(array('validate' => TRUE, 'ok' => 'Campa&ntilde;a creada correctamente.', 'id_campania' => $id_campania));
        } else {
            echo json_encode(array('validate' => FALSE, 'error' => 'Ocurrio un error al intentar crear la campa&ntilde;a en la base de datos.'));
        }
    }

    function crear_campania_result() {
        $this->load->view('campanias_creada');
    }

    function crear_campania_AppNexus($id_campania, $campania_de = 'adtk') {
        $campania = $this->campanias->get_campania_by_id($id_campania);

        if (!$campania)
            return FALSE;

// creo el anunciante en DFP y el usuario en anunciantes_adserver
        $anunciante = $this->get_anunciante_campania($campania);

        if (!$anunciante)
            return FALSE;
        
        $adserver = $campania->adserver;
        
        if(!strlen(trim($campania->adserver))){
            
            $adserver_anunciantes = $this->constants->get_constant_by_id(32);
            $adserver = $adserver_anunciantes->string_value;
            
            $update = array('adserver' => $adserver);
            $this->campanias->update_campania($id_campania, $update);
        }
        
        if($campania->empresa_campania){
            $adserver = 'APPNEXUS';
        }
        
        if ($adserver == 'APPNEXUS') {
            if (!$this->api->crear_campania_AppNexus($id_campania, $campania_de))
                return FALSE;
        }elseif ($adserver == 'DFP') {
            die('va a crear dfp');
            $this->api->crear_campania_dfp($id_campania);
        }
// asocio el anunciante_adserver al usuario_anunciante
        if ($this->anunciantes->get_anunciante_asociado_by_id($campania->id_anunciante, $anunciante) == NULL) {
            $data_anunciante_asociado = array('id_anunciante_redvlog' => $campania->id_anunciante, 'id_anunciante_adserver' => $anunciante);
            $this->anunciantes->insert_anunciante_asociado($data_anunciante_asociado);
        }

        return $anunciante;
    }

    function crear_campania_DFP($id_campania) {
        $campania = $this->campanias->get_campania_by_id($id_campania);

        if (!$campania)
            return FALSE;

// creo el anunciante en DFP y el usuario en anunciantes_adserver
        $anunciante = $this->get_anunciante_campania($campania);

        if (!$anunciante)
            return FALSE;

        if (!$this->api->crear_campania_dfp($id_campania))
            return FALSE;

// asocio el anunciante_adserver al usuario_anunciante
        if ($this->anunciantes->get_anunciante_asociado_by_id($campania->id_anunciante, $anunciante) == NULL) {
            $data_anunciante_asociado = array('id_anunciante_redvlog' => $campania->id_anunciante, 'id_anunciante_adserver' => $anunciante);
            $this->anunciantes->insert_anunciante_asociado($data_anunciante_asociado);
        }

        return $anunciante;
    }

    /*
     * GETs
     */

    function get_anunciante_campania($campania) {
// leo los datos del usuario anunciante que creo la campania

        $anunciantes_adserver = $this->users->get_anunciantes_app_by_id($this->tank_auth->get_user_id());

        if (sizeof($anunciantes_adserver > 1)) {
            $user_anunciante = $this->anunciantes->get_anunciante_adserver_by_id($campania->id_anunciante);
        } else {
            $user_anunciante = $this->anunciantes->get_anunciante_by_id($campania->id_anunciante);
        }

        if (!$user_anunciante)
            return FALSE;

        if (isset($user_anunciante->empresa)) {
            if ($user_anunciante->empresa) {
                $nombre = $user_anunciante->name . ' (' . $user_anunciante->empresa . ')';
            } else {
                $nombre = $user_anunciante->name;
            }
        } else {
            $nombre = $user_anunciante->nombre;
        }


// si el anunciante no esta creado en DFP entonces lo creo
        $existe_anunciante = $this->anunciantes->get_anunciante_adserver_by_nombre($nombre);

        if ($existe_anunciante == NULL) {
            $data = array(
                'nombre' => $nombre,
                'id_campania_mediafem' => $campania->id,
                'adserver_actual' => 0,
                'fecha_alta' => date('Y-m-d H:i:s')
            );
            $anunciante = $this->anunciantes->insert_anunciante_adserver($data);

            if ($anunciante) {
                $anunciante_id = $this->db->insert_id();

                $email = $this->user_data->email;

                $anunciante_dfp = $this->api->crear_anunciante_dfp($nombre, $email);
                if ($anunciante_dfp) {
                    unset($data);
                    $data = array('id_dfp' => $anunciante_dfp['id'], 'adserver_actual' => 0);

                    $this->anunciantes->update_anunciante_adserver($anunciante_id, $data);

                    $anunciante_id_dfp = $anunciante_id;
                }
            }
        } else {
            $anunciante_id_dfp = $existe_anunciante->id;
        }

        return $anunciante_id_dfp;
    }

    function get_anunciantes() {
//return $this->api->obtener_anunciantes();
        return $this->anunciantes->get_all_anunciantes_adservers();
    }

    function get_canales_tematicos() {
//return $this->api->obtener_canales_tematicos();
        return $this->categorias->get_categorias();
    }

    function get_formatos() {
        return $this->formatosdfp->get_formatos();
    }

    function get_paises() {
        return $this->paises->get_paises();
    }

    function get_paises_json() {
        $data = $this->paises->get_paises();
        echo json_encode($data);
    }

    function get_publishers_y_sitios_json() {
        /*
         *  SITIOS CON NOMBRES DE PUBLISHERS
          $encontrados = false;
          $publishers = $this->publishers->get_all_users();

          if ($publishers) {
          $publisher_array = array();

          foreach ($publishers as $publisher) {
          if ($publisher->id_adunit_publisher != '' && $publisher->id_adunit_publisher != NULL) {

          $sitios_publisher = '';
          $sitios = $this->sites->get_site_by_id_adunit_publisher($publisher->id_adunit_publisher);

          if ($sitios) {
          foreach ($sitios as $sitio) {
          $sitios_publisher[] = array(
          'id_sitio' => $sitio->id,
          'url' => $sitio->nombre_dfp
          );
          }

          $publisher_array[] = array(
          'id' => $publisher->id,
          'publisher_name' => strtolower($publisher->publisher_name),
          'sitios' => $sitios_publisher
          );

          $encontrados = true;
          }
          }
          }

          if ($encontrados) {
          echo json_encode($publisher_array);
          }
          } else {
          echo json_encode(array('error' => 'No se encontraron publishers.'));
          }
         *
         */
        $sites = $this->sites->get_all_sites('nombre_appnexus', 'asc');

        if ($sites) {
            foreach ($sites as $sitio) {
                $sitios[] = array(
                    'id_sitio' => $sitio->id,
                    'url' => $sitio->nombre_appnexus
                );
            }

            //echo json_encode($this->get_sites_adt());
            echo json_encode($sitios);
        } else {
            echo json_encode(array('error' => 'No se encontraron sitios.'));
        }
    }

    function get_publishers_y_sitios_json_by_adserver() {
        $adserver = $this->input->get('adserver');
        
        if ($adserver == 'APPNEXUS') {

            $sites = $this->sites->get_all_sites('nombre_appnexus', 'asc');

            if ($sites) {
                foreach ($sites as $sitio) {
                    $sitios[] = array(
                        'id_sitio' => $sitio->id,
                        'url' => $sitio->nombre_appnexus
                    );
                }
                echo json_encode($sitios);
            } else {
                echo json_encode(array('error' => 'No se encontraron sitios.'));
            }
        } elseif ($adserver == 'DFP') {
            echo json_encode($this->get_sites_adt());
        }
    }

    function get_sites_adt() {

        $mysqli = $this->_connect_adt_db();

        $sql = "select s.sit_name, sa.adv_sit_adserver_key from sites s, adserver_site sa 
                where sa.adserver_id = 3
                and s.sit_name not like '%mediafem.com%' 
                and s.sit_name not like '%adtomatik.com%'
                and sa.site_id = s.sit_id;";

        $sites = null;

        if ($result = $mysqli->query($sql)) {
            while ($obj = $result->fetch_object()) {
                $sites[] = array('id_sitio' => $obj->adv_sit_adserver_key, 'url' => $obj->sit_name);
            }
        }

        return $sites;
    }

    public function _connect_adt_db() {
        $mysqli = mysqli_connect('205.186.153.231', 'prod_adt', 'v0_T5l9q', 'prod_adt_2');
        if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }
        return $mysqli;
    }

    function get_segmentaciones() {
        return $this->segmentacion->get_segmentacion();
    }

    function get_tarifas_formato($formato) {
        $valores = $this->tarifarios->get_by_formato($formato);

        if ($valores) {
            foreach ($valores as $valor) {
                if ($valor->modalidad == 'CPM') {
                    if ($valor->id_segmentacion == 1) {
                        $data['Red_CPM'] = $valor->valor;
                    } else if ($valor->id_segmentacion == 2) {
                        $data['Canales_CPM'] = $valor->valor;
                    } else if ($valor->id_segmentacion == 3) {
                        $data['Sitios_CPM'] = $valor->valor;
                    }
                } else if ($valor->modalidad == 'CPC') {
                    if ($valor->id_segmentacion == 1) {
                        $data['Red_CPC'] = $valor->valor;
                    } else if ($valor->id_segmentacion == 2) {
                        $data['Canales_CPC'] = $valor->valor;
                    } else if ($valor->id_segmentacion == 3) {
                        $data['Sitios_CPC'] = $valor->valor;
                    }
                }
            }

            return $data;
        } else {
            return NULL;
        }
    }

    /*
     * OTHERs
     */

    function _dateDiff($start, $end) {
        $start_ts = strtotime($start);
        $end_ts = strtotime($end);
        $diff = $end_ts - $start_ts;
        return round($diff / 86400);
    }

    function borrar_directorio($dir, $borrarme) {
        if (!$dh = @opendir($dir))
            return;
        while (false !== ($obj = readdir($dh))) {
            if ($obj == '.' || $obj == '..')
                continue;
            if (!@unlink($dir . '/' . $obj))
                borrar_directorio($dir . '/' . $obj, true);
        }
        closedir($dh);
        if ($borrarme) {
            @rmdir($dir);
        }
    }

    function crear_audiencia() {
        $nombre = trim($this->input->post('audiencia_name'));
        $id_anunciante_adserver = trim($this->input->post('id_anunciante'));

        if ($nombre == '') {
            echo json_encode(array('validate' => FALSE, 'error' => 'Especifique un nombre de audiencia.'));
            die();
        }


        $data = array(
            'name' => $nombre
            , 'id_anunciante' => $this->tank_auth->get_user_id()
        );

        if ($this->audiencias->insert($data)) {
            $audiencia_id = $this->db->insert_id();

            $audiencia_appnexus = $this->api->crear_audiencia($nombre, $id_anunciante_adserver);

            if ($audiencia_appnexus) {

                $data_update = array(
                    'id_appnexus' => $audiencia_appnexus['id']
                );

                if ($this->audiencias->update($audiencia_id, $data_update)) {

                    echo json_encode(array('validate' => TRUE, 'id_audiencia' => $audiencia_id));
                } else {
                    echo json_encode(array('validate' => FALSE, 'error' => 'No se pudo modificar el ID del adserver en la base de datos.'));
                }
            } else {
                echo json_encode(array('validate' => FALSE, 'error' => 'No se pudo crear la audiencia en el adserver.'));
            }
        } else {
            echo json_encode(array('validate' => FALSE, 'error' => 'No se pudo crear la audiencia en la base de datos.'));
        }
    }

    function mostrar_audiencias($id_campania) {
        $data['audiencias'] = $this->audiencias->get_by_anunciante($this->tank_auth->get_user_id());
        $data['audiencias_campania'] = $this->campanias->get_audiencias_by_campania($id_campania);
        $data['id_campania'] = $id_campania;

        $this->load->view('mostrar_audiencias', $data);
    }

    function asociar_audiencias_a_campania() {
        $audiencias = trim($this->input->post('audiencias_seleccionadas'), ';');
        $id_campania = trim($this->input->post('id_campania'));

        $audiencias = explode(';', $audiencias);

        foreach ($audiencias as $audiencia) {

            $audiencia = explode(',', $audiencia);

            $data = array(
                'id_campania' => $id_campania
                , 'id_audiencia' => $audiencia[0]
                , 'action' => $audiencia[1]
            );

            $this->campanias->insertar_audiencia_campania($data);

            /*
              if(!$this->campanias->insertar_audiencia_campania($data)){
              echo json_encode(array('validate' => FALSE, 'error' => 'No se pudo asociar la audiencia a la campaña.'));
              die();
              }
             *
             */
        }

//echo json_encode(array('validate' => TRUE));
    }

    function alta_anunciante_json($adserver = 1, $empresa = 'mf') {
        $nombre = trim($this->input->post('nombre'));

        if ($nombre != '') {
            $existe_anunciante = $this->anunciantes->get_anunciante_adserver_by_nombre($nombre);

            if ($existe_anunciante == NULL) {
                $data = array(
                    'nombre' => $nombre,
                    'adserver_actual' => $adserver,
                    'fecha_alta' => date('Y-m-d H:i:s')
                );
                $anunciante = $this->anunciantes->insert_anunciante_adserver($data);

                if ($anunciante) {
                    $anunciante_id = $this->db->insert_id();

                    $email = $this->user_data->email;

//alta anunciante en DFP
                    if ($adserver == 0) {
                        $anunciante_dfp = $this->api->crear_anunciante_dfp($nombre, $email);
                        if ($anunciante_dfp) {

                            unset($data);
                            $data = array('id_dfp' => $anunciante_dfp['id'], 'adserver_actual' => 0);

                            $this->anunciantes->update_anunciante_adserver($anunciante_id, $data);

                            $data_anunciante_asociado = array('id_anunciante_redvlog' => $this->tank_auth->get_user_id(), 'id_anunciante_adserver' => $anunciante_id);
                            $this->anunciantes->insert_anunciante_asociado($data_anunciante_asociado);

                            echo json_encode(array('validate' => TRUE, 'id_anunciante' => $anunciante_id));
                        } else {
                            echo json_encode(array('validate' => FALSE, 'error' => 'No se pudo crear el anunciante en el AdServer.'));
                        }

                        // alta de anunciante en AppNexus
                    } else if ($adserver == 1) {
                        $anunciante_appnexus = $this->api->crear_anunciante_appnexus($nombre, $email, $empresa);
                        if ($anunciante_appnexus) {

                            unset($data);
                            $data = array('id_appnexus' => $anunciante_appnexus['id'], 'adserver_actual' => 1);

                            $this->anunciantes->update_anunciante_adserver($anunciante_id, $data);

                            $data_anunciante_asociado = array('id_anunciante_redvlog' => $this->tank_auth->get_user_id(), 'id_anunciante_adserver' => $anunciante_id);
                            $this->anunciantes->insert_anunciante_asociado($data_anunciante_asociado);

                            echo json_encode(array('validate' => TRUE, 'id_anunciante' => $anunciante_id));
                        } else {
                            echo json_encode(array('validate' => FALSE, 'error' => 'No se pudo crear el anunciante en el AdServer.'));
                        }
                    }
                } else {
                    echo json_encode(array('validate' => FALSE, 'error' => 'El nombre de anunciante ya se encuentra registrado.'));
                }
            } else {
                echo json_encode(array('validate' => FALSE, 'error' => 'El nombre del anunciante especificado ya se encuentra registrado.'));
            }
        } else {
            echo json_encode(array('validate' => FALSE, 'error' => 'Por favor indique el nombre del anunciante.'));
        }
    }

    function ver($id_campania, $seconds = 'asdasdasd', $mostrar_inversion_total = 1) {

        /*
         *
         * ATENCION!!!! : SI SE MODIFICA ALGO ACA TAMBIEN MODIFICAR LA EXPORTACION DEL PDF
         * ---------------------------------------------------------------------------------
         *
         */


// retomo los datos de la campa&ntilde;a.
        $campania = $this->campanias->get_campania_by_id($id_campania);

        $creatividades = $this->campanias->get_creatividades_campanias($id_campania);

        $usuario = $this->users->get_user_by_id($this->tank_auth->get_user_id(), 1);

        $data['habilitar_descuentos'] = $usuario->habilitar_descuentos;

        if ($campania) {
            $data['creatividades'] = $creatividades;
            $data['id_campania'] = $id_campania;
            $data['nombre_campania'] = $campania->nombre;
            $data['anunciante_id'] = $campania->id_anunciante;
            $data['anunciante_nombre'] = '';
            $data['segmentacion_id'] = $campania->segmentacion_id;
            $data['segmentacion'] = '';
            $data['inversion_bruta'] = $campania->inversion_bruta;
            $data['descuento'] = $campania->descuento;
            $data['comision'] = $campania->comision;
            $data['inversion_neto'] = $campania->inversion_neta;
            $data['modalidad_compra'] = strtoupper($campania->modalidad_compra);
            $data['cantidad_compra'] = $campania->cantidad;
            $data['paises'] = '';
            $data['fecha_inicio'] = MySQLDateToDateDatepicker($campania->fecha_inicio);
            $data['fecha_fin'] = MySQLDateToDateDatepicker($campania->fecha_fin);
            $data['fecha_alta'] = MySQLDateToDateDatepicker($campania->fecha_alta);
            $data['forma_completarse'] = $campania->forma_completarse;
            $data['facturacion'] = $campania->distribucion;
            $data['ejecutivo_cuentas_id'] = $campania->usuario_cuentas;
            $data['ejecutivo_cuentas'] = '';
            $data['ejecutivo_implementa_id'] = $campania->usuario_implementa;
            $data['ejecutivo_implementa'] = '';
            $data['ejecutivo_revisa_id'] = $campania->usuario_revisa;
            $data['ejecutivo_revisa'] = '';
            $data['ejecutivo_director_id'] = $campania->usuario_director;
            $data['ejecutivo_director'] = '';
            $data['ticket'] = $campania->ticket_mantis;
            $data['activada'] = $campania->activada;
            $data['mostrar_exportar'] = FALSE;

            $data['id_lineItem_appnexus'] = $campania->id_lineItem_appnexus;

            $data['estado'] = $campania->estado;
            $data['creada_desde_anunciantes'] = $campania->creada_desde_anunciantes;

            // retomo el nombre del cliente
            $cliente = $this->clientes_model->getByID($campania->id_cliente);
            if ($cliente) {
                $data['cliente_nombre'] = $cliente->razon_social;
            } else {
                $data['cliente_nombre'] = 'N/A.';
            }

// retomo el nombre del anunciante
            $anunciante = $this->anunciantes->get_anunciante_adserver_by_id($data['anunciante_id']);
            if ($anunciante) {
                $data['anunciante_nombre'] = $anunciante->nombre;
            } else {
                $data['anunciante_nombre'] = 'Anunciante inexistente en la base de datos.';
            }

// retomo el nombre de la segmentacion
            $segmentacion = $this->segmentacion->get_segmentacion_by_id($data['segmentacion_id']);
            if ($segmentacion) {
                $data['segmentacion'] = $segmentacion->descripcion;
            } else {
                $data['segmentacion'] = 'Nombre de segmentaci&oacute;n inexistente en la base de datos.';
            }

// retomo los paises pertenecientes a las campa&ntilde;as.
            $campania_paises = $this->campaniaspaises->get_paises_by_campania($data['id_campania']);

            if ($campania_paises) {
                $texto_paises = '';
                $a = 0;
                foreach ($campania_paises as $row) {
                    $a++;

                    $pais = $this->paises->get_pais_by_id($row->id_pais);
                    if ($a == sizeof($campania_paises) - 1) {
                        $texto_paises = $texto_paises . $pais->descripcion . " y ";
                    } else {
                        $texto_paises = $texto_paises . $pais->descripcion . ", ";
                    }
                }

                $data['paises'] = substr($texto_paises, 0, - 2);
            }

            if ($data['paises'] == "Colombia, Colombia, Colombia, Colombia, Colombia, Colombia, Colombia, Colombia, Andorra, Albania, Armenia, Argentina, Austria, Australia, Bosnia and Herzegovina, Belgium, Bulgaria, Brazil, Belarus, Canada, Switzerland, Cote d'Ivoire, Chile, China, Colombia, Cyprus, Czech Republic, Germany, Denmark, Estonia, Espa&ntilde;a, Finland, France, United Kingdom, Georgia, Greece, Hong Kong, Croatia, Hungary, Ireland, Israel, India, Iceland, Italy, Japan, Republic of Korea, Lithuania, Luxembourg, Latvia, Macedonia, Montserrat, Mexico, Malaysia, Netherlands, Norway, New Zealand, Peru, Philippines, Portugal, Romania, Russian Federation, Sweden, Singapore, Slovenia, Slovakia, San Marino, Turkey, Taiwan, Ukraine, United States, Uruguay, Paraguay, Costa Rica, El Salvador, Republica Dominicana, Guatemala, Venezuela, Panama, Puerto Rico, Bolivia, Pakistan, Ecuador, Bangladesh, Nigeria, Kenya, Morocco, Uganda, Egipto, Honduras, Indonesia, Jamaica, Kuwait, Nicaragua, United Arab Emirates, Antigua and Barbuda, Netherlands Antilles, American Samoa, Azerbaijan, Barbados, Bahrain, Bermuda, Botswana, Belize, Cameroon, Dominica, Algeria, Federated States of Micronesia, Grenada, French Guiana, Ghana, Guinea, Guam, Iraq, Jordan, Saint Kitts and Nevis, Cayman Islands, Lebanon, Saint Lucia, Sri Lanka, Liberia, Libyan Arab Jamahiriya, Monaco, Moldova, Republic of, Northern Mariana Islands, Malta, Maldives, Nepal, Oman, Poland, Palestinian Territory, Qatar, Reunion, Saudi Arabia, Sierra Leone, Suriname, Turks and Caicos Islands, Thailand, Tunisia, Trinidad and Tobago, Tanzania, United Republic of, United States Minor Outlying Islands, Virgin Islands, British, Virgin Islands, U.S., Vietnam, Yemen, South Africa, Zambia, Zimbabwe, Afghanistan, Haiti, Aruba, Bahamas, Brunei Darussalam, Guadeloupe, Gibraltar, Mozambique, Senegal, Namibia, Equatorial Guinea, Angola, Cambodia, Gabon, Papua New Guinea, Kazakhstan, Madagascar, Timor-Leste, Congo, The Democratic Republic of the, Malvinas, Malawi, Martinique, Fiji, Mauritius, Mongolia, Tonga, Macao, Cape Verde, Niger, Ethiopia, Turkmenistan, Lesotho, Sao Tome and Principe, Vanuatu, Central African Republic, Cook Islands, Desconocido, Congo, Solomon Islands, Desconocido, Desconocido, Greenland, Swaziland, Palau, Saint Pierre and Miquelon, Faroe Islands, Desconocido, Somalia, Holy See (Vatican City State), Burundi, Anguilla, Rwanda, Mali, Burkina Faso, Kiribati, Togo, Uzbekistan, Mayotte, Liechtenstein, Gambia, Djibouti, French Polynesia, Benin, Saint Vincent and the Grenadines, Guyana, Mauritania, Lao People's Democratic Republic, Kyrgyzstan, New Caledonia, Andorra, Albania, Armenia, Argentina, Austria, Australia, Bosnia and Herzegovina, Belgium, Bulgaria, Brazil, Belarus, Canada, Switzerland, Cote d'Ivoire, Chile, China, Colombia, Cyprus, Czech Republic, Germany, Denmark, Estonia, Espa&ntilde;a, Finland, France, United Kingdom, Georgia, Greece, Hong Kong, Croatia, Hungary, Ireland, Israel, India, Iceland, Italy, Japan, Republic of Korea, Lithuania, Luxembourg, Latvia, Macedonia, Montserrat, Mexico, Malaysia, Netherlands, Norway, New Zealand, Peru, Philippines, Portugal, Romania, Russian Federation, Sweden, Singapore, Slovenia, Slovakia, San Marino, Turkey, Taiwan, Ukraine, United States, Uruguay, Paraguay, Costa Rica, El Salvador, Republica Dominicana, Guatemala, Venezuela, Panama, Puerto Rico, Bolivia, Pakistan, Ecuador, Bangladesh, Nigeria, Kenya, Morocco, Uganda, Egipto, Honduras, Indonesia, Jamaica, Kuwait, Nicaragua, United Arab Emirates, Antigua and Barbuda, Netherlands Antilles, American Samoa, Azerbaijan, Barbados, Bahrain, Bermuda, Botswana, Belize, Cameroon, Dominica, Algeria, Federated States of Micronesia, Grenada, French Guiana, Ghana, Guinea, Guam, Iraq, Jordan, Saint Kitts and Nevis, Cayman Islands, Lebanon, Saint Lucia, Sri Lanka, Liberia, Libyan Arab Jamahiriya, Monaco, Moldova, Republic of, Northern Mariana Islands, Malta, Maldives, Nepal, Oman, Poland, Palestinian Territory, Qatar, Reunion, Saudi Arabia, Sierra Leone, Suriname, Turks and Caicos Islands, Thailand, Tunisia, Trinidad and Tobago, Tanzania, United Republic of, United States Minor Outlying Islands, Virgin Islands, British, Virgin Islands, U.S., Vietnam, Yemen, South Africa, Zambia, Zimbabwe, Afghanistan, Haiti, Aruba, Bahamas, Brunei Darussalam, Guadeloupe, Gibraltar, Mozambique, Senegal, Namibia, Equatorial Guinea, Angola, Cambodia, Gabon, Papua New Guinea, Kazakhstan, Madagascar, Timor-Leste, Congo, The Democratic Republic of the, Malvinas, Malawi, Martinique, Fiji, Mauritius, Mongolia, Tonga, Macao, Cape Verde, Niger, Ethiopia, Turkmenistan, Lesotho, Sao Tome and Principe, Vanuatu, Central African Republic, Cook Islands, Desconocido, Congo, Solomon Islands, Desconocido, Desconocido, Greenland, Swaziland, Palau, Saint Pierre and Miquelon, Faroe Islands, Desconocido, Somalia, Holy See (Vatican City State), Burundi, Anguilla, Rwanda, Mali, Burkina Faso, Kiribati, Togo, Uzbekistan, Mayotte, Liechtenstein, Gambia, Djibouti, French Polynesia, Benin, Saint Vincent and the Grenadines, Guyana, Mauritania, Lao People's Democratic Republic, Kyrgyzstan, New Caledonia, Andorra, Albania, Armenia, Argentina, Austria, Australia, Bosnia and Herzegovina, Belgium, Bulgaria, Brazil, Belarus, Canada, Switzerland, Cote d'Ivoire, Chile, China, Colombia, Cyprus, Czech Republic, Germany, Denmark, Estonia, Espa&ntilde;a, Finland, France, United Kingdom, Georgia, Greece, Hong Kong, Croatia, Hungary, Ireland, Israel, India, Iceland, Italy, Japan, Republic of Korea, Lithuania, Luxembourg, Latvia, Macedonia, Montserrat, Mexico, Malaysia, Netherlands, Norway, New Zealand, Peru, Philippines, Portugal, Romania, Russian Federation, Sweden, Singapore, Slovenia, Slovakia, San Marino, Turkey, Taiwan, Ukraine, United States, Uruguay, Paraguay, Costa Rica, El Salvador, Republica Dominicana, Guatemala, Venezuela, Panama, Puerto Rico, Bolivia, Pakistan, Ecuador, Bangladesh, Nigeria, Kenya, Morocco, Uganda, Egipto, Honduras, Indonesia, Jamaica, Kuwait, Nicaragua, United Arab Emirates, Antigua and Barbuda, Netherlands Antilles, American Samoa, Azerbaijan, Barbados, Bahrain, Bermuda, Botswana, Belize, Cameroon, Dominica, Algeria, Federated States of Micronesia, Grenada, French Guiana, Ghana, Guinea, Guam, Iraq, Jordan, Saint Kitts and Nevis, Cayman Islands, Lebanon, Saint Lucia, Sri Lanka, Liberia, Libyan Arab Jamahiriya, Monaco, Moldova, Republic of, Northern Mariana Islands, Malta, Maldives, Nepal, Oman, Poland, Palestinian Territory, Qatar, Reunion, Saudi Arabia, Sierra Leone, Suriname, Turks and Caicos Islands, Thailand, Tunisia, Trinidad and Tobago, Tanzania, United Republic of, United States Minor Outlying Islands, Virgin Islands, British, Virgin Islands, U.S., Vietnam, Yemen, South Africa, Zambia, Zimbabwe, Afghanistan, Haiti, Aruba, Bahamas, Brunei Darussalam, Guadeloupe, Gibraltar, Mozambique, Senegal, Namibia, Equatorial Guinea, Angola, Cambodia, Gabon, Papua New Guinea, Kazakhstan, Madagascar, Timor-Leste, Congo, The Democratic Republic of the, Malvinas, Malawi, Martinique, Fiji, Mauritius, Mongolia, Tonga, Macao, Cape Verde, Niger, Ethiopia, Turkmenistan, Lesotho, Sao Tome and Principe, Vanuatu, Central African Republic, Cook Islands, Desconocido, Congo, Solomon Islands, Desconocido, Desconocido, Greenland, Swaziland, Palau, Saint Pierre and Miquelon, Faroe Islands, Desconocido, Somalia, Holy See (Vatican City State), Burundi, Anguilla, Rwanda, Mali, Burkina Faso, Kiribati, Togo, Uzbekistan, Mayotte, Liechtenstein, Gambia, Djibouti, French Polynesia, Benin, Saint Vincent and the Grenadines, Guyana, Mauritania, Lao People's Democratic Republic, Kyrgyzstan, New Caledonia, Andorra, Albania, Armenia, Argentina, Austria, Australia, Bosnia and Herzegovina, Belgium, Bulgaria, Brazil, Belarus, Canada, Switzerland, Cote d'Ivoire, Chile, China, Colombia, Cyprus, Czech Republic, Germany, Denmark, Estonia, Espa&ntilde;a, Finland, France, United Kingdom, Georgia, Greece, Hong Kong, Croatia, Hungary, Ireland, Israel, India, Iceland, Italy, Japan, Republic of Korea, Lithuania, Luxembourg, Latvia, Macedonia, Montserrat, Mexico, Malaysia, Netherlands, Norway, New Zealand, Peru, Philippines, Portugal, Romania, Russian Federation, Sweden, Singapore, Slovenia, Slovakia, San Marino, Turkey, Taiwan, Ukraine, United States, Uruguay, Paraguay, Costa Rica, El Salvador, Republica Dominicana, Guatemala, Venezuela, Panama, Puerto Rico, Bolivia, Pakistan, Ecuador, Bangladesh, Nigeria, Kenya, Morocco, Uganda, Egipto, Honduras, Indonesia, Jamaica, Kuwait, Nicaragua, United Arab Emirates, Antigua and Barbuda, Netherlands Antilles, American Samoa, Azerbaijan, Barbados, Bahrain, Bermuda, Botswana, Belize, Cameroon, Dominica, Algeria, Federated States of Micronesia, Grenada, French Guiana, Ghana, Guinea, Guam, Iraq, Jordan, Saint Kitts and Nevis, Cayman Islands, Lebanon, Saint Lucia, Sri Lanka, Liberia, Libyan Arab Jamahiriya, Monaco, Moldova, Republic of, Northern Mariana Islands, Malta, Maldives, Nepal, Oman, Poland, Palestinian Territory, Qatar, Reunion, Saudi Arabia, Sierra Leone, Suriname, Turks and Caicos Islands, Thailand, Tunisia, Trinidad and Tobago, Tanzania, United Republic of, United States Minor Outlying Islands, Virgin Islands, British, Virgin Islands, U.S., Vietnam, Yemen, South Africa, Zambia, Zimbabwe, Afghanistan, Haiti, Aruba, Bahamas, Brunei Darussalam, Guadeloupe, Gibraltar, Mozambique, Senegal, Namibia, Equatorial Guinea, Angola, Cambodia, Gabon, Papua New Guinea, Kazakhstan, Madagascar, Timor-Leste, Congo, The Democratic Republic of the, Malvinas, Malawi, Martinique, Fiji, Mauritius, Mongolia, Tonga, Macao, Cape Verde, Niger, Ethiopia, Turkmenistan, Lesotho, Sao Tome and Principe, Vanuatu, Central African Republic, Cook Islands, Desconocido, Congo, Solomon Islands, Desconocido, Desconocido, Greenland, Swaziland, Palau, Saint Pierre and Miquelon, Faroe Islands, Desconocido, Somalia, Holy See (Vatican City State), Burundi, Anguilla, Rwanda, Mali, Burkina Faso, Kiribati, Togo, Uzbekistan, Mayotte, Liechtenstein, Gambia, Djibouti, French Polynesia, Benin, Saint Vincent and the Grenadines, Guyana, Mauritania, Lao People's Democratic Republic, Kyrgyzstan y New Caledonia") {
                $data['paises'] = 'Todos los paises';
            }

            if ($campania->empresa_campania == 0) {
// retomo los sitios o canales tematicos seg&uacute;n la segmentaci&oacute;n de la campa&ntilde;a.
                if ($data['segmentacion_id'] == 2) {
// obtengo los canales tematicos seleccionados
                    $canales = $this->campaniascanalestematicos->get_canales_tematicos_by_campania($data['id_campania']);
// recorro por cada canal obtengo su nombre desde la base.
                    if ($canales) {
                        foreach ($canales as $canal) {
//obtengo los datos del canal de la BD
                            $data_canal = $this->categorias->get_categoria_by_id($canal->id_canal_tematico);
                            $canales_tematicos[] = array('id' => $data_canal->id, 'name' => $data_canal->nombre);
                        }
                        $data['canales_tematicos'] = $canales_tematicos;
                    } else {
                        $data['canales_tematicos'] = 'No se encontraron categor&iacute;as asociadas a la campa&ntilde;a.';
                    }
                } else {
// selecciono todos los sitios correspondientes a la camapania
                    $sitios = $this->campaniassitios->get_sitios_by_campania($data['id_campania']);

                    if ($sitios) {
                        foreach ($sitios as $site) {
// de cada sitio obtengo su nombre y lo guardo en el array para pasar a la vista.
                            $sitio = $this->sites->get_site_by_id($site->id_sitio);
                            $nombre_sitio = $sitio->nombre_appnexus;
                            if ($nombre_sitio == '')
                                $nombre_sitio = $sitio->nombre_dfp;

                            $arr_sitios[] = array('nombre' => $nombre_sitio);
                        }
                        $data['sitios'] = $arr_sitios;
                    }else {
                        $data['sitios'] = 'No se encontraron sitios asociados a la campa&ntilde;a.';
                    }
                }
            }


            if ($campania->empresa_campania == 1) {
                // selecciono las audiencias de la campania
                $data['audiencias'] = $this->campanias->get_audiencias_by_campania($campania->id);
            }

// obtengo los formatos de la campa&ntilde;a
            $formatos = $this->campaniasformatos->get_formatos_by_campania($data['id_campania']);

            if ($formatos) {
                foreach ($formatos as $row) {
// obtengo el nombre del formato
                    $formato = $this->formatosdfp->get_formato_by_id($row->id_formato);

                    if ($usuario->notacion == 0) {
                        $monto = number_format($row->monto, 2, '.', ',');
                        $cantidad = number_format($row->cantidad, 0, '.', ',');
                    } else if ($usuario->notacion == 1) {
                        $monto = number_format($row->monto, 2, ',', '.');
                        $cantidad = number_format($row->cantidad, 0, ',', '.');
                    }

                    $arr_formatos[$row->id_formato] = array(
                        'descripcion' => $formato->descripcion,
                        'monto' => $monto . ' ' . $this->user_data->moneda,
                        'cantidad' => $cantidad,
                        'pagina_destino' => $row->pagina_destino
                    );
                }
                $data['formatos'] = $arr_formatos;
            }
            $dias_campania = $this->_dateDiff($campania->fecha_inicio, $campania->fecha_fin);
            $inversion_neta = ($data['inversion_neto'] * $dias_campania);

            if ($campania->modalidad_compra == 'cpm') {
                $data['diario'] = ($data['inversion_neto'] / $campania->cantidad) * 1000;
            } else {
                $data['diario'] = ($data['inversion_neto'] / $campania->cantidad);
            }

            if ($usuario->notacion == 0) {
                $data['inversion_neta_total'] = number_format($inversion_neta, 3, '.', ',');
                $data['inversion_neto'] = number_format($data['inversion_neto'], 3, '.', ',');
                $data['diario'] = number_format($data['diario'], 3, '.', ',') . ' ' . $this->user_data->moneda;
            } else if ($usuario->notacion == 1) {
                $data['inversion_neta_total'] = number_format($inversion_neta, 3, ',', '.');
                $data['inversion_neto'] = number_format($data['inversion_neto'], 3, ',', '.');
                $data['diario'] = number_format($data['diario'], 3, ',', '.') . ' ' . $this->user_data->moneda;
            }

// la campa&ntilde;a esta activada?
            $data['activada'] == 0 ? $data['activada'] = 'NO' : $data['activada'] = 'SI';

            $data['usuario'] = $usuario;

            $data['campania'] = $campania;

            if ($campania->alta_finalizada)
                $data['mostrar_exportar'] = TRUE;

            $frecuencia = 'Optimizada';
            if ($campania->frecuencia == 'NORMAL') {
                $frecuencia = 'Optimizada';
            } else if ($campania->frecuencia == '1x24') {
                $frecuencia = '1 impresi&oacute;n cada 24 horas';
            } else if ($campania->frecuencia == '2x24') {
                $frecuencia = '2 impresiones cada 24 horas';
            }


            $data['campania_padre'] = FALSE;

            $campania_padre = $this->campanias->get_campania_padre($campania->id);

            if ($campania_padre) {
                $data_campania_padre = $this->campanias->get_campania_by_id($campania_padre->id_campania_padre);

                if ($data_campania_padre)
                    $data['campania_padre'] = $data_campania_padre->nombre;
            }

            $data['frecuencia'] = $frecuencia;

            $data['mostrar_inversion_total'] = $mostrar_inversion_total;

            $this->load->view('campanias_ver', $data);
        }
    }

    /*
     * OTHERs
     */

    function _send_email($titulo, $contenido, $copia = FALSE) {
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->SetLanguage('en', BASEPATH . '/application/libraries/PHPMailer/language/');
        $mail->SMTPAuth = true;                  // enable SMTP authentication
        $mail->Host = "ssl://smtp.gmail.com";      // sets GMAIL as the SMTP server
        $mail->Port = 465;
        $mail->Username = "mailing@mediafem.com";  // la cuenta de correo GMail
        $mail->Password = "Sebastian02";            // password de la cuenta GMail
        $mail->FromName = 'MediaFem'; // nombre de la persona que env&iacute;a el correo
        $mail->From = 'media@mediafem.com';  //Quien env&iacute;a el correo

        if ($copia)
            $mail->AddCC($copia);

        $mail->Subject = $titulo;
        $mail->IsHTML(true);
        $mail->Body = $contenido;
        $mail->ContentType = 'text/html; charset=UTF-8';
        $mail->CharSet = 'UTF-8';
        $mail->AddAddress('argaccount@mediafem.com');
        $mail->AddAddress('admin@mediafem.com');
        $mail->Send();
        return true;
    }

    function _send_email_to($destinatario, $titulo, $contenido) {
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->SMTPAuth = true; // habilitamos la autenticaci&oacute;n SMTP
        $mail->SetLanguage('en', BASEPATH . '/application/libraries/PHPMailer/language/');
        $mail->Host = "ssl://smtp.gmail.com";      // sets GMAIL as the SMTP server
        $mail->Port = 465;
        $mail->Username = "mailing@mediafem.com";  // la cuenta de correo GMail
        $mail->Password = "Sebastian02";            // password de la cuenta GMail
        $mail->FromName = 'MediaFem para Anunciantes'; // nombre de la persona que env&iacute;a el correo
        $mail->From = 'media@mediafem.com';  //Quien env&iacute;a el correo
        $mail->Subject = $titulo;
        $mail->IsHTML(true);
        $mail->Body = $contenido;
        $mail->ContentType = 'text/html; charset=UTF-8';
        $mail->CharSet = 'UTF-8';
        $mail->AddAddress($destinatario);
        $mail->Send();
        return true;
    }

    function esta_en_mantenimiento($constant) {
        $mantenimiento = $this->constants->get_constant_by_id($constant);

        if ($mantenimiento->value == 1) {
            $this->load->model('mensajemantenimiento');
            $data['mensaje'] = $this->mensajemantenimiento->get_mensaje_mantenimiendo_by_estado('A')->contenido;

            $this->load->view('mantenimiento_view', $data);
        } else {
            return false;
        }
    }

    function insert_historial($id_campania, $version, $texto) {
        $update = array('historial_version' => $version);
        $this->campanias->update_campania($id_campania, $update);

        $data_insert_historial = array(
            'id_campania' => $id_campania,
            'texto' => $texto,
            'usuario' => $this->tank_auth->get_user_id(),
            'fecha_creacion' => date('Y-m-j H:i:s', strtotime('-1 hour', strtotime(date('Y-m-d H:i:s')))),
            'historial_version' => $version
        );

        return $this->campaniashistorial->insert($data_insert_historial);
    }

    function in_array_field($needle, $needle_field, $haystack, $strict = false) {
        if ($strict) {
            foreach ($haystack as $item)
                if (isset($item->$needle_field) && $item->$needle_field === $needle)
                    return true;
        }
        else {
            foreach ($haystack as $item)
                if (isset($item->$needle_field) && $item->$needle_field == $needle)
                    return true;
        }
        return false;
    }

    function cambiar_moneda($valor) {
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

                $valor = $valor * $cotizacion;
            }
        }

        return $valor;
    }

}
