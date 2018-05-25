<?php

require_once 'application/libraries/config.inc';
require_once 'application/libraries/Caller.php';
require_once 'application/libraries/api_functions.php';
require_once 'application/libraries/functions.php';
require_once 'application/libraries/TableExtractor.php';

require_once 'application/libraries/Google/Api/Ads/Dfp/Lib/DfpUser.php';
require_once 'application/libraries/DisplayUtils.php';
require_once 'application/libraries/ServiceUserManager.php';
require_once 'application/libraries/WebUtils.php';
require_once 'application/libraries/Google/Api/Ads/Dfp/Util/ServiceUtils.php';
require_once 'application/libraries/Google/Api/Ads/Dfp/Util/ReportUtils.php';
require_once 'application/libraries/Google/Api/Ads/Dfp/Util/DateTimeUtils.php';

class Detect {

    function __construct() {

        $this->ci = &get_instance();

        $this->ci->load->helper('language');
        $this->ci->load->library('tank_auth');
        $this->ci->lang->load('tank_auth');
        $this->ci->load->model('tank_auth/users');
        $this->ci->load->model('tokens');
        $this->ci->load->model('campanias');
        $this->ci->load->model('anunciantes');
        $this->ci->load->model('constants');

        if ($this->ci->tank_auth->is_logged_in()) {

            $url_actual = $_SERVER['REQUEST_URI'];
            $url_actual = explode("/", $url_actual);
            $url_actual[1] = '/' . $url_actual[1];

            if ($this->ci->constants->get_constant_by_id(MENSAJE_MANTENIMIENTO_ANUNCIANTES)->value == 1 && $url_actual[1] != '/mantenimiento') {
                redirect('/mantenimiento');
            }

            $this->ci->user_data = $usuario = $this->ci->users->get_user_by_id($this->ci->tank_auth->get_user_id(), 1);
            
            //echo $url_actual[2]; die();

            if ($usuario->banned && $url_actual[2] == 'logout') {
                $this->ci->load->helper('cookie');
                if ($cookie = get_cookie($this->ci->config->item('autologin_cookie_name', 'tank_auth'), TRUE)) {

                    $data = unserialize($cookie);

                    $this->ci->load->model('tank_auth/user_autologin');
                    $this->ci->user_autologin->delete($data['user_id'], md5($data['key']));

                    delete_cookie($this->ci->config->item('autologin_cookie_name', 'tank_auth'));
                }
                $this->ci->session->set_userdata(array('user_id' => '', 'username' => '', 'status' => ''));

                $this->ci->session->sess_destroy();
            }

            if ($usuario->banned && $url_actual[1] != '/banned') {
                redirect('/banned');
            }

            if (strlen($usuario->email)) {
                $this->ci->nombre_usuario = $usuario->email;
                $this->ci->email_usuario = $usuario->email;
            } else {
                $this->ci->nombre_usuario = $usuario->username;
                $this->ci->email_usuario = '';
            }

            $this->ci->limite_de_compra = $usuario->limite_de_compra;

            //$this->ci->tarjeta_certificada = $usuario->tarjeta_certificada;

            $this->ci->creado_desde_sitio = $usuario->creado_desde_sitio;

            // consulto si el usuario posee campanias aprobadas
            $anunciantes_asociados = $this->ci->anunciantes->get_anunciantes_asociados_by_id($usuario->id);

            $this->ci->tiene_campanias = FALSE;

            $campanias = NULL;

            $campanias_aprobadas = null;

            if ($anunciantes_asociados) {
                foreach ($anunciantes_asociados as $anunciante_asociado) {
                    $campanias_aprobadas = $this->ci->campanias->get_campanias_by_anunciante($anunciante_asociado->id_anunciante_adserver);

                    if ($campanias_aprobadas != NULL) {
                        $this->ci->tiene_campanias = TRUE;
                        break;
                    }
                }
            }

            $this->ci->campanias_aprobadas = FALSE;

            if ($campanias_aprobadas) {
                foreach ($campanias_aprobadas as $campania) {
                    if ($campania->estado == 'APROBADA') {
                        $this->ci->campanias_aprobadas = TRUE;
                        break;
                    }
                }
            }

            /*
              $idioma_url = $this->ci->uri->segment(3);


              if (strlen($idioma_url)) {
              if ($idioma_url == "ES") {
              $this->ci->lang->load('general', 'spanish');
              $lang = "ES";
              } elseif ($idioma_url == "EN") {
              $this->ci->lang->load('general', 'english');
              $lang = "EN";
              }
              } else {
              $this->ci->lang->load('general', 'spanish');
              $lang = "ES";
              }
             *
             */
        } else {
            /*

              $idioma_url = $this->ci->uri->segment(3);

              if (strlen($idioma_url)) {
              if ($idioma_url == "ES") {
              $this->ci->lang->load('general', 'spanish');
              $lang = "ES";
              } elseif ($idioma_url == "EN") {
              $this->ci->lang->load('general', 'english');
              $lang = "EN";
              }
              } else {
              $this->ci->lang->load('general', 'spanish');
              $lang = "ES";
              }
             *
             */
        }
        /*
          $this->ci->idioma_usuario = $lang;
         */
        $res_ultimo_token = $this->ci->tokens->get_last_token();

        $ultimo_token = $res_ultimo_token->token;
        $minutos_pasados = $res_ultimo_token->diferencia;

        if ($minutos_pasados > 90 || !strlen($ultimo_token)) {
            //echo "1 ";
            $token = getAuthToken(DW_USERNAME, DW_PASSWORD);
            $data_token = array('token' => $token);
            $result = $this->ci->tokens->insert_token($data_token);
        } else {
            //echo "2 ";
            $token = $ultimo_token;
        }

        $this->ci->token = $token;
    }

}