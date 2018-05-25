<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Auth extends CI_Controller {

    function __construct() {
        parent::__construct();

        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('security');
        $this->load->library('tank_auth');
        $this->lang->load('tank_auth');
        $this->load->library('My_PHPMailer');
        $this->load->model('paises');
        $this->load->model('anunciantes');
        $this->load->model('cupones_model');
        $this->load->model('anunciantessaldos');
        $this->load->model('cotizaciones_diarias');
    }

    function index() {
        if ($message = $this->session->flashdata('message')) {

            $this->load->view('auth/login_form', array('message' => $message));
        } else {
            redirect('/auth/login/');
        }
    }

    /**
     * Login user on the site
     *
     * @return void
     */
    function login($mensaje = null) {
        if ($this->tank_auth->is_logged_in()) {         // logged in
            redirect('');
            //} elseif ($this->tank_auth->is_logged_in(FALSE)) {      // logged in, not activated
            //redirect('/auth/send_again/');
        } else {

            if (isset($mensaje) && $mensaje == 'R')
                redirect('https://www.mediafem.com/anunciantes/?register=ok');

            if (isset($mensaje) && $mensaje == 'A')
                redirect('https://www.mediafem.com/anunciantes/?activate=ok');

            if (isset($mensaje) && $mensaje == 'A')
                redirect('https://www.mediafem.com/anunciantes/?activate=error');

            $data['login_by_username'] = ($this->config->item('login_by_username', 'tank_auth'));
            $data['login_by_email'] = $this->config->item('login_by_email', 'tank_auth');

            $this->form_validation->set_rules('login', 'Login', 'trim|required|xss_clean');
            $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');
            $this->form_validation->set_rules('remember', 'Remember me', 'integer');

            // Get login for counting attempts to login
            if ($this->config->item('login_count_attempts', 'tank_auth') AND
                    ($login = $this->input->post('login'))) {
                $login = $this->security->xss_clean($login);
            } else {
                $login = '';
            }
            $data['use_recaptcha'] = $this->config->item('use_recaptcha', 'tank_auth');
            if ($this->tank_auth->is_max_login_attempts_exceeded($login)) {
                if ($data['use_recaptcha'])
                    $this->form_validation->set_rules('recaptcha_response_field', 'Confirmation Code', 'trim|xss_clean|required|callback__check_recaptcha');
                else
                    $this->form_validation->set_rules('captcha', 'Confirmation Code', 'trim|xss_clean|required|callback__check_captcha');
            }
            $data['errors'] = array();

            if ($this->form_validation->run()) {
                // validation ok
                if ($this->tank_auth->login(
                                $this->form_validation->set_value('login'), $this->form_validation->set_value('password'), $this->form_validation->set_value('remember'), $data['login_by_username'], $data['login_by_email'])) {        // success
                    redirect('');
                } else {
                    $errors = $this->tank_auth->get_error_message();
                    if (isset($errors['banned'])) {        // banned user
                        $this->_show_message($this->lang->line('auth_message_banned') . ' ' . $errors['banned']);
                        //} elseif (isset($errors['not_activated'])) {    // not activated user
                        //redirect('/auth/send_again/');
                    } else {             // fail
                        foreach ($errors as $k => $v)
                            $data['errors'][$k] = $this->lang->line($v);
                    }
                }
            }
            $data['show_captcha'] = FALSE;
            if ($this->tank_auth->is_max_login_attempts_exceeded($login)) {
                $data['show_captcha'] = TRUE;
                if ($data['use_recaptcha']) {
                    $data['recaptcha_html'] = $this->_create_recaptcha();
                } else {
                    $data['captcha_html'] = $this->_create_captcha();
                }
            }

            if (!$this->tank_auth->is_logged_in()) {
                redirect_login_js();
            }

            $this->load->view('auth/login_form', $data);
        }
    }

    function banned() {
        $this->load->view('auth/banned_view');
    }

    /**
     * Logout user
     *
     * @return void
     */
    function logout() {
        $this->tank_auth->logout();

        $this->_show_message($this->lang->line('auth_message_logged_out'));
    }

    /**
     * Register user on the site
     *
     * @return void
     */
    function register() {
        if ($this->tank_auth->is_logged_in()) {         // logged in
            redirect('');
            //} elseif ($this->tank_auth->is_logged_in(FALSE)) {      // logged in, not activated
            //redirect('/auth/send_again/');
        } elseif (!$this->config->item('allow_registration', 'tank_auth')) { // registration is off
            $this->_show_message($this->lang->line('auth_message_registration_disabled'));
        } else {
            $use_username = $this->config->item('use_username', 'tank_auth');
            //if ($use_username)
            //$this->form_validation->set_rules('username', 'Usuario', 'trim|required|xss_clean|min_length[' . $this->config->item('username_min_length', 'tank_auth') . ']|max_length[' . $this->config->item('username_max_length', 'tank_auth') . ']|alpha_dash');

            $this->form_validation->set_rules('nombre_beneficiario', 'Nombre Beneficiario', 'required');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|valid_email');
            $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|min_length[' . $this->config->item('password_min_length', 'tank_auth') . ']|max_length[' . $this->config->item('password_max_length', 'tank_auth') . ']|alpha_dash');
            $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'trim|required|xss_clean|matches[password]');

            $captcha_registration = $this->config->item('captcha_registration', 'tank_auth');

            $use_recaptcha = $this->config->item('use_recaptcha', 'tank_auth');
            if ($captcha_registration) {
                if ($use_recaptcha) {
                    $this->form_validation->set_rules('recaptcha_response_field', 'Confirmation Code', 'trim|xss_clean|required|callback__check_recaptcha');
                } else {
                    $this->form_validation->set_rules('captcha', 'Confirmation Code', 'trim|xss_clean|required|callback__check_captcha');
                }
            }
            $data['errors'] = array();

            $email_activation = $this->config->item('email_activation', 'tank_auth');

            $data['username'] = '';
            $data['email'] = '';
            $data['password'] = '';
            $data['confirm_password'] = '';
            $data['nombre_beneficiario'] = '';
            $data['empresa'] = '';
            $data['cmb_country'] = '';
            $data['telefono'] = '';
            $data['direccion'] = '';
            $data['codigo_postal'] = '';
            $data['ciudad'] = '';
            $data['provincia'] = '';

            $aplicar_cupon = '0';

            $cupon = $this->uri->segment(3);

            if ($cupon == "cpn") {
                $aplicar_cupon = '1';
            }

            if ($this->form_validation->run()) {        // validation ok
                if (!is_null($data = $this->tank_auth->create_user(
                                $use_username ? $_POST['username'] : '', $_POST['email'], $_POST['password'], $email_activation, $_POST['nombre_beneficiario'], $_POST['empresa'], $_POST['cmb_country'], $_POST['telefono'], $_POST['direccion'], $_POST['codigo_postal'], $_POST['ciudad'], $_POST['provincia'], $aplicar_cupon
                        ))) {         // success
                    $data['site_name'] = $this->config->item('website_name', 'tank_auth');

                    if ($email_activation) {         // send "activate" email
                        $data['activation_period'] = $this->config->item('email_activation_expire', 'tank_auth') / 3600;

                        $this->_send_email('activate', $data['email'], $data);

                        unset($data['password']); // Clear password (just for any case)

                        /*
                          if($_POST['empresa'] != ''){
                          $nombre = $_POST['empresa'];
                          }else{
                          $nombre = $_POST['nombre_beneficiario'];
                          }
                          $anunciante_dfp = $this->api->crear_anunciante_dfp($nombre);

                          if ($anunciante_dfp) {
                          $anunciante_mediafem = $data['user_id'];

                          $dataUpdate = array(
                          'nombre' => $nombre,
                          'id_dfp' => $anunciante_dfp['id']
                          );

                          $this->anunciantes->insert_anunciante_adserver($dataUpdate);

                          $anunciante_adserver =  $this->db->insert_id();

                          $dataUpdate = array(
                          'id_anunciante_redvlog' => $anunciante_mediafem,
                          'id_anunciante_adserver' => $anunciante_adserver
                          );

                          $this->anunciantes->insert_anunciante_asociado($dataUpdate);
                          }
                         */

                        //redirect('/auth/login?register=ok');
                        redirect('/auth/login/R');
                        // $this->_show_message($this->lang->line('auth_message_registration_completed_1'));
                    } else {
                        if ($this->config->item('email_account_details', 'tank_auth')) { // send "welcome" email
                            $this->_send_email('welcome', $data['email'], $data);
                        }
                        unset($data['password']); // Clear password (just for any case)

                        $this->_show_message($this->lang->line('auth_message_registration_completed_2') . ' ' . anchor('/auth/login/', 'Login'));
                    }
                } else {
                    $errors = $this->tank_auth->get_error_message();
                    foreach ($errors as $k => $v)
                        $data['errors'][$k] = $this->lang->line($v);


                    $use_username ? $data['username'] = $_POST['username'] : $data['username'] = '';
                    $data['email'] = $_POST['email'];
                    $data['password'] = $_POST['password'];
                    $data['confirm_password'] = '';
                    $data['nombre_beneficiario'] = $_POST['nombre_beneficiario'];
                    $data['empresa'] = $_POST['empresa'];
                    $data['direccion'] = $_POST['direccion'];
                    $data['codigo_postal'] = $_POST['codigo_postal'];
                    $data['ciudad'] = $_POST['ciudad'];
                    $data['provincia'] = $_POST['provincia'];
                    $data['cmb_country'] = $_POST['cmb_country'];
                    $data['telefono'] = $_POST['telefono'];
                    $data['cupon_de_promocion'] = $aplicar_cupon;
                }
            } else {
                $use_username ? $data['username'] = $_POST['username'] : $data['username'] = '';
                $data['email'] = $_POST['email'];
                $data['password'] = $_POST['password'];
                $data['confirm_password'] = '';
                $data['nombre_beneficiario'] = $_POST['nombre_beneficiario'];
                $data['empresa'] = $_POST['empresa'];
                $data['direccion'] = $_POST['direccion'];
                $data['codigo_postal'] = $_POST['codigo_postal'];
                $data['ciudad'] = $_POST['ciudad'];
                $data['provincia'] = $_POST['provincia'];
                $data['cmb_country'] = $_POST['cmb_country'];
                $data['telefono'] = $_POST['telefono'];
                $data['cupon_de_promocion'] = $aplicar_cupon;
            }

            if ($captcha_registration) {
                if ($use_recaptcha) {
                    $data['recaptcha_html'] = $this->_create_recaptcha();
                } else {
                    $data['captcha_html'] = $this->_create_captcha();
                }
            }
            $data['use_username'] = $use_username;
            $data['captcha_registration'] = $captcha_registration;
            $data['use_recaptcha'] = $use_recaptcha;

            $data['paises'] = $this->paises->get_paises();

            $this->load->view('auth/register_form', $data);
        }
    }

    function register_ok() {
        $this->load->view('auth/register_ok');
    }

    /**
     * Send activation email again, to the same or new email address
     *
     * @return void
     */
    function send_again() {
        if (!$this->tank_auth->is_logged_in(FALSE)) {       // not logged in or activated
            redirect('/auth/login/');
        } else {
            $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|valid_email');

            $data['errors'] = array();

            if ($this->form_validation->run()) {        // validation ok
                if (!is_null($data = $this->tank_auth->change_email(
                                $this->form_validation->set_value('email')))) {   // success
                    $data['site_name'] = $this->config->item('website_name', 'tank_auth');
                    $data['activation_period'] = $this->config->item('email_activation_expire', 'tank_auth') / 3600;

                    $this->_send_email('activate', $data['email'], $data);

                    $this->_show_message(sprintf($this->lang->line('auth_message_activation_email_sent'), $data['email']));
                } else {
                    $errors = $this->tank_auth->get_error_message();
                    foreach ($errors as $k => $v)
                        $data['errors'][$k] = $this->lang->line($v);
                }
            }
            $this->load->view('auth/send_again_form', $data);
        }
    }

    /**
     * Activate user account.
     * User is verified by user_id and authentication code in the URL.
     * Can be called by clicking on link in mail.
     *
     * @return void
     */
    function activate() {
        $user_id = $this->uri->segment(3);
        $new_email_key = $this->uri->segment(4);

        if ($this->tank_auth->activate_user($user_id, $new_email_key)) {  // success
            
            $user = $this->anunciantes->get_anunciante_by_id($user_id);
            
            $data['email'] = $user->email;
            $data['nombre_beneficiario'] = $user->name;
            $data['empresa'] = $user->empresa;
            
            $this->_send_email('activate-to-zendesk', 'account@mediafem.com', $data);

            $this->tank_auth->logout();
            redirect('/auth/login/A');
        } else {
            redirect('/auth/login/I');
        }
    }
   
    /**
     * Generate reset code (to change password) and send it to user
     *
     * @return void
     */
    /* function forgot_password() {
      if ($this->tank_auth->is_logged_in()) {         // logged in
      redirect('');
      } elseif ($this->tank_auth->is_logged_in(FALSE)) {      // logged in, not activated
      redirect('/auth/send_again/');
      } else {
      $this->form_validation->set_rules('login', 'Email or login', 'trim|required|xss_clean');

      $data['errors'] = array();

      if ($this->form_validation->run()) {        // validation ok
      if (!is_null($data = $this->tank_auth->forgot_password(
      $this->form_validation->set_value('login')))) {

      $data['site_name'] = $this->config->item('website_name', 'tank_auth');

      // Send email with password activation link
      $this->_send_email('forgot_password', $data['email'], $data);

      $this->_show_message($this->lang->line('auth_message_new_password_sent'));
      } else {
      $errors = $this->tank_auth->get_error_message();
      foreach ($errors as $k => $v)
      $data['errors'][$k] = $this->lang->line($v);
      }
      }
      $this->load->view('auth/forgot_password_form', $data);
      }
      } */

    function forgot_password() {
        $login = $this->input->post('login');

        if ($this->tank_auth->is_logged_in(FALSE)) {      // logged in, not activated
            echo json_encode(array('estado' => FALSE, 'mensaje' => 'Por favor revise su casilla de correo electrónico.'));
        } else if (!is_null($data = $this->tank_auth->forgot_password($login))) {
            $data['site_name'] = $this->config->item('website_name', 'tank_auth');
            if ($data['email']) {
                // Send email with password activation link
                $this->_send_email('forgot_password', $data['email'], $data);
                echo json_encode(array('estado' => TRUE, 'mensaje' => 'Por favor revise su casilla de correo electrónico.'));
            } else {
                echo json_encode(array('estado' => FALSE, 'mensaje' => 'No se encuentra el nombre de usuario o la casilla de correo electrónico asociada a una cuenta.'));
            }
        } else {
            echo json_encode(array('estado' => FALSE, 'mensaje' => 'No se encuentra el nombre de usuario o la casilla de correo electrónico asociada a una cuenta.'));
        }
    }

    /**
     * Replace user password (forgotten) with a new one (set by user).
     * User is verified by user_id and authentication code in the URL.
     * Can be called by clicking on link in mail.
     *
     * @return void
     */
    function reset_password() {
        $user_id = $this->uri->segment(3);
        $new_pass_key = $this->uri->segment(4);

        $this->form_validation->set_rules('new_password', 'New Password', 'trim|required|xss_clean|min_length[' . $this->config->item('password_min_length', 'tank_auth') . ']|max_length[' . $this->config->item('password_max_length', 'tank_auth') . ']|alpha_dash');
        $this->form_validation->set_rules('confirm_new_password', 'Confirm new Password', 'trim|required|xss_clean|matches[new_password]');

        $data['errors'] = array();

        if ($this->form_validation->run()) {        // validation ok
            if (!is_null($data = $this->tank_auth->reset_password(
                            $user_id, $new_pass_key, $this->form_validation->set_value('new_password')))) { // success
                $data['site_name'] = $this->config->item('website_name', 'tank_auth');

                // Send email with new password
                $this->_send_email('reset_password', $data['email'], $data);

                redirect('/auth/reset_password_form_ok/');

                //$this->_show_message($this->lang->line('auth_message_new_password_activated') . ' ' . anchor('/auth/login/', 'Login'));
            } else {              // fail
                //$this->_show_message($this->lang->line('auth_message_new_password_failed'));
            }
        } else {
            // Try to activate user by password key (if not activated yet)
            if ($this->config->item('email_activation', 'tank_auth')) {
                $this->tank_auth->activate_user($user_id, $new_pass_key, FALSE);
            }

            if (!$this->tank_auth->can_reset_password($user_id, $new_pass_key)) {
                //$this->_show_message($this->lang->line('auth_message_new_password_failed'));
            }
        }
        $this->load->view('auth/reset_password_form', $data);
    }

    function reset_password_form_ok() {
        $this->load->view('auth/reset_password_form_ok');
    }

    /**
     * Change user password
     *
     * @return void
     */
    function change_password() {
        if (!$this->tank_auth->is_logged_in()) {        // not logged in or not activated
            redirect('/auth/login/');
        } else {
            $this->form_validation->set_rules('old_password', 'Old Password', 'trim|required|xss_clean');
            $this->form_validation->set_rules('new_password', 'New Password', 'trim|required|xss_clean|min_length[' . $this->config->item('password_min_length', 'tank_auth') . ']|max_length[' . $this->config->item('password_max_length', 'tank_auth') . ']|alpha_dash');
            $this->form_validation->set_rules('confirm_new_password', 'Confirm new Password', 'trim|required|xss_clean|matches[new_password]');

            $data['errors'] = array();

            if ($this->form_validation->run()) {        // validation ok
                if ($this->tank_auth->change_password(
                                $this->form_validation->set_value('old_password'), $this->form_validation->set_value('new_password'))) { // success
                    $this->_show_message($this->lang->line('auth_message_password_changed'));
                } else {              // fail
                    $errors = $this->tank_auth->get_error_message();
                    foreach ($errors as $k => $v)
                        $data['errors'][$k] = $this->lang->line($v);
                }
            }
            $this->load->view('auth/change_password_form', $data);
        }
    }

    /**
     * Change user email
     *
     * @return void
     */
    function change_email() {
        if (!$this->tank_auth->is_logged_in()) {        // not logged in or not activated
            redirect('/auth/login/');
        } else {
            $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|valid_email');

            $data['errors'] = array();

            if ($this->form_validation->run()) {        // validation ok
                if (!is_null($data = $this->tank_auth->set_new_email(
                                $this->form_validation->set_value('email'), $this->form_validation->set_value('password')))) {   // success
                    $data['site_name'] = $this->config->item('website_name', 'tank_auth');

                    // Send email with new email address and its activation link
                    $this->_send_email('change_email', $data['new_email'], $data);

                    $this->_show_message(sprintf($this->lang->line('auth_message_new_email_sent'), $data['new_email']));
                } else {
                    $errors = $this->tank_auth->get_error_message();
                    foreach ($errors as $k => $v)
                        $data['errors'][$k] = $this->lang->line($v);
                }
            }
            $this->load->view('auth/change_email_form', $data);
        }
    }

    /**
     * Replace user email with a new one.
     * User is verified by user_id and authentication code in the URL.
     * Can be called by clicking on link in mail.
     *
     * @return void
     */
    function reset_email() {
        $user_id = $this->uri->segment(3);
        $new_email_key = $this->uri->segment(4);

        // Reset email
        if ($this->tank_auth->activate_new_email($user_id, $new_email_key)) { // success
            $this->tank_auth->logout();
            $this->_show_message($this->lang->line('auth_message_new_email_activated') . ' ' . anchor('/auth/login/', 'Login'));
        } else {                // fail
            $this->_show_message($this->lang->line('auth_message_new_email_failed'));
        }
    }

    /**
     * Delete user from the site (only when user is logged in)
     *
     * @return void
     */
    function unregister() {
        if (!$this->tank_auth->is_logged_in()) {        // not logged in or not activated
            redirect('/auth/login/');
        } else {
            $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');

            $data['errors'] = array();

            if ($this->form_validation->run()) {        // validation ok
                if ($this->tank_auth->delete_user(
                                $this->form_validation->set_value('password'))) {  // success
                    $this->_show_message($this->lang->line('auth_message_unregistered'));
                } else {              // fail
                    $errors = $this->tank_auth->get_error_message();
                    foreach ($errors as $k => $v)
                        $data['errors'][$k] = $this->lang->line($v);
                }
            }
            $this->load->view('auth/unregister_form', $data);
        }
    }

    /**
     * Show info message
     *
     * @param	string
     * @return	void
     */
    function _show_message($message) {
        $this->session->set_flashdata('message', $message);
        redirect('/auth/');
    }

    /**
     * Send email message of given type (activate, forgot_password, etc.)
     *
     * @param	string
     * @param	string
     * @param	array
     * @return	void
     */
    function _send_email($type, $email, &$data) {
        if ($type == 'activate') {
            $contenido = $this->load->view('email/' . $type . '-txt', $data, TRUE);

            $contenido = str_replace("<<EMAIL>>", $data['email'], $contenido);
            $contenido = str_replace("<<LINK_ACTIVACION>>", site_url('/auth/activate/' . $data['user_id'] . '/' . $data['new_email_key']), $contenido);

                    
        }elseif ($type == 'activate-to-zendesk') {
            $contenido = $this->load->view('email/' . $type . '-html', $data, TRUE);

            $contenido = str_replace("<<USERNAME>>", $data['username'], $contenido);
            $contenido = str_replace("<<EMAIL>>", $data['email'], $contenido);
            $contenido = str_replace("<<NOMBRE_BENEFICIARIO>>", $data['nombre_beneficiario'], $contenido);
            $contenido = str_replace("<<EMPRESA>>", $data['empresa'], $contenido);
            $contenido = str_replace("<<PAIS>>", $data['cmb_country'], $contenido);


        } else {
            $contenido = $this->load->view('email/' . $type . '-html', $data, TRUE);
        }
        echo "1<br/>";
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->SetLanguage('en', BASEPATH . '/application/libraries/PHPMailer/language/');
        $mail->SMTPAuth = true;                  // enable SMTP authentication
        //$mail->SMTPSecure = "tls";                 // sets the prefix to the servier
        $mail->Host = "ssl://smtp.gmail.com";      // sets GMAIL as the SMTP server
        $mail->Port = 465;
        $mail->Username = "mailing@mediafem.com";
        $mail->Password = "Sebastian02";
        $mail->From = 'media@mediafem.com';  //Quien envía el correo
        echo "2<br/>";
/*
        $this->load->library('email');
        $this->email->from('argaccount@mediafem.com', 'MediaFem para Anunciantes');
        $this->email->to($email);
        $this->email->message($contenido);
*/
        if ($type == 'activate') {
            $mail->Subject = 'Bienvenido a MediaFem para Anunciantes';
	}else if ($type == 'activate-to-zendesk') {
            $mail->Subject = 'Se activo un nuevo anunciante desde anunciantes.mediafem.com';
        } else {
            $mail->Subject = '¿Olvidaste tu contraseña en MediaFem para Anunciantes?';
        }
        echo "3<br/>";
        $mail->IsHTML(true);
        $mail->Body = $contenido;
        $mail->AddAddress($email);
        
        $mail->Send();
        echo "4<br/>";
    }

    /**
     * Create CAPTCHA image to verify user as a human
     *
     * @return	string
     */
    function _create_captcha() {
        $this->load->helper('captcha');

        $cap = create_captcha(array(
            'img_path' => './' . $this->config->item('captcha_path', 'tank_auth'),
            'img_url' => base_url() . $this->config->item('captcha_path', 'tank_auth'),
            'font_path' => './' . $this->config->item('captcha_fonts_path', 'tank_auth'),
            'font_size' => $this->config->item('captcha_font_size', 'tank_auth'),
            'img_width' => $this->config->item('captcha_width', 'tank_auth'),
            'img_height' => $this->config->item('captcha_height', 'tank_auth'),
            'show_grid' => $this->config->item('captcha_grid', 'tank_auth'),
            'expiration' => $this->config->item('captcha_expire', 'tank_auth'),
        ));

        // Save captcha params in session
        $this->session->set_flashdata(array(
            'captcha_word' => $cap['word'],
            'captcha_time' => $cap['time'],
        ));

        return $cap['image'];
    }

    /**
     * Callback function. Check if CAPTCHA test is passed.
     *
     * @param	string
     * @return	bool
     */
    function _check_captcha($code) {
        $time = $this->session->flashdata('captcha_time');
        $word = $this->session->flashdata('captcha_word');

        list($usec, $sec) = explode(" ", microtime());
        $now = ((float) $usec + (float) $sec);

        if ($now - $time > $this->config->item('captcha_expire', 'tank_auth')) {
            $this->form_validation->set_message('_check_captcha', $this->lang->line('auth_captcha_expired'));
            return FALSE;
        } elseif (($this->config->item('captcha_case_sensitive', 'tank_auth') AND
                $code != $word) OR
                strtolower($code) != strtolower($word)) {
            $this->form_validation->set_message('_check_captcha', $this->lang->line('auth_incorrect_captcha'));
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Create reCAPTCHA JS and non-JS HTML to verify user as a human
     *
     * @return	string
     */
    function _create_recaptcha() {
        $this->load->helper('recaptcha');

        // Add custom theme so we can get only image
        $options = "<script>var RecaptchaOptions = {theme: 'custom', custom_theme_widget: 'recaptcha_widget'};</script>\n";

        // Get reCAPTCHA JS and non-JS HTML
        $html = recaptcha_get_html($this->config->item('recaptcha_public_key', 'tank_auth'));

        return $html;
    }

    /**
     * Callback function. Check if reCAPTCHA test is passed.
     *
     * @return	bool
     */
    function _check_recaptcha() {
        $this->load->helper('recaptcha');

        $resp = recaptcha_check_answer($this->config->item('recaptcha_private_key', 'tank_auth'), $_SERVER['REMOTE_ADDR'], $_POST['recaptcha_challenge_field'], $_POST['recaptcha_response_field']);

        if (!$resp->is_valid) {
            $this->form_validation->set_message('_check_recaptcha', $this->lang->line('auth_incorrect_captcha'));
            return FALSE;
        }
        return TRUE;
    }

}

/* End of file auth.php */
/* Location: ./application/controllers/auth.php */