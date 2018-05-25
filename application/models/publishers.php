<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Users
 *
 * This model represents user authentication data. It operates the following tables:
 * - user account data,
 * - user profiles
 *
 * @package	Tank_auth
 * @author	Ilya Konyukhov (http://konyukhov.com/soft/)
 */
class Publishers extends CI_Model {

    private $table_name = 'users';   // user accounts
    private $profile_table_name = 'user_profiles'; // user profiles

    function __construct() {
        parent::__construct();

        $ci = & get_instance();
        $this->table_name = $ci->config->item('db_table_prefix', 'tank_auth') . $this->table_name;
        $this->profile_table_name = $ci->config->item('db_table_prefix', 'tank_auth') . $this->profile_table_name;
    }

    function get_all_users() {
        $this->db->where('id_perfil', 1);
        $query = $this->db->get($this->table_name);
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_active_users() {
        //$this->db->where('banned', '0');
        //$query = $this->db->get($this->table_name);

        $query = $this->db->query("select u.*, a.nombre_completo as nombre_ejecutivo from $this->table_name u, admins a where u.id_ejecutivo_medios=a.id;");

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_users_by_publishername($q) {

        $this->db->like('LOWER(publisher_name)', strtolower($q));
        $this->db->where_not_in('id', '207');
        $query = $this->db->get($this->table_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_users_by_email($q) {

        $this->db->like('LOWER(email)', strtolower($q));
        $query = $this->db->get($this->table_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_users_by_username($q) {

        $this->db->like('LOWER(username)', strtolower($q));
        $query = $this->db->get($this->table_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_users_by_site_default($q) {

        $this->db->like('LOWER(site_default)', strtolower($q));
        $this->db->where_not_in('id', '207');
        $query = $this->db->get($this->table_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    function get_user_by_id($user_id, $activated) {
        $this->db->where('id', $user_id);
        //$this->db->where('activated', $activated ? 1 : 0);

        $query = $this->db->get($this->table_name);
        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    function get_sitios_principales() {
        $query = $this->db->get($this->table_name);
        $this->db->order_by('site_default', 'asc');

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    /**
     * Get user record by login (username or email)
     *
     * @param	string
     * @return	object
     */
    function get_user_by_login($login) {
        $this->db->where('LOWER(username)=', strtolower($login));
        $this->db->or_where('LOWER(email)=', strtolower($login));

        $query = $this->db->get($this->table_name);
        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    /**
     * Get user record by username
     *
     * @param	string
     * @return	object
     */
    function get_user_by_username($username) {
        $this->db->where('LOWER(username)=', strtolower($username));

        $query = $this->db->get($this->table_name);
        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    function get_user_by_publisher_id_campanias($publisher_id) {
        $this->db->where('publisher_id', $publisher_id);

        $query = $this->db->get($this->table_name);
        if ($query->num_rows() == 1) {
            $row = $query->row();
            if ($row->banned == '0') {
                return true;
            }
            return false;
        }
        return FALSE;
    }

    function get_user_by_publisher_id($publisher_id) {
        $this->db->where('publisher_id', $publisher_id);
        $this->db->where_not_in('id', '207');

        $query = $this->db->get($this->table_name);
        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    function get_user_by_site_default($sitio) {
        $this->db->where('LOWER(site_default)=', strtolower($sitio));

        $query = $this->db->get($this->table_name);
        //if ($query->num_rows() == 1)
        return $query->row();
        return NULL;
    }

    function get_user_by_publishername($publisher_name) {
        $this->db->where('LOWER(publisher_name)=', strtolower($publisher_name));
        $query = $this->db->get($this->table_name);
        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    /**
     * Get user record by email
     *
     * @param	string
     * @return	object
     */
    function get_user_by_email($email) {
        $this->db->where('LOWER(email)=', strtolower($email));

        $query = $this->db->get($this->table_name);
        if ($query->num_rows() == 1)
            return $query->row();
        return NULL;
    }

    /**
     * Check if username available for registering
     *
     * @param	string
     * @return	bool
     */
    function is_username_available($username) {
        $this->db->select('1', FALSE);
        $this->db->where('LOWER(username)=', strtolower($username));

        $query = $this->db->get($this->table_name);
        return $query->num_rows() == 0;
    }

    /**
     * Check if email available for registering
     *
     * @param	string
     * @return	bool
     */
    function is_email_available($email) {
        $this->db->select('1', FALSE);
        $this->db->where('LOWER(email)=', strtolower($email));
        $this->db->or_where('LOWER(new_email)=', strtolower($email));

        $query = $this->db->get($this->table_name);
        return $query->num_rows() == 0;
    }

    /**
     * Create new user record
     *
     * @param	array
     * @param	bool
     * @return	array
     */
    function create_user($data, $activated = TRUE) {
        $data['created'] = date('Y-m-d H:i:s');
        $data['activated'] = $activated ? 1 : 0;

        if ($this->db->insert($this->table_name, $data)) {
            $user_id = $this->db->insert_id();
            if ($activated)
                $this->create_profile($user_id);
            return array('user_id' => $user_id);
        }
        return NULL;
    }

    /**
     * Activate user if activation key is valid.
     * Can be called for not activated users only.
     *
     * @param	int
     * @param	string
     * @param	bool
     * @return	bool
     */
    function activate_user($user_id, $activation_key, $activate_by_email) {
        $this->db->select('1', FALSE);
        $this->db->where('id', $user_id);
        if ($activate_by_email) {
            $this->db->where('new_email_key', $activation_key);
        } else {
            $this->db->where('new_password_key', $activation_key);
        }
        $this->db->where('activated', 0);
        $query = $this->db->get($this->table_name);

        if ($query->num_rows() == 1) {

            $this->db->set('activated', 1);
            $this->db->set('new_email_key', NULL);
            $this->db->where('id', $user_id);
            $this->db->update($this->table_name);

            $this->create_profile($user_id);
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Purge table of non-activated users
     *
     * @param	int
     * @return	void
     */
    function purge_na($expire_period = 172800) {
        $this->db->where('activated', 0);
        $this->db->where('UNIX_TIMESTAMP(created) <', time() - $expire_period);
        $this->db->delete($this->table_name);
    }

    /**
     * Delete user record
     *
     * @param	int
     * @return	bool
     */
    function delete_user($user_id) {
        $this->db->where('id', $user_id);
        $this->db->delete($this->table_name);
        if ($this->db->affected_rows() > 0) {
            $this->delete_profile($user_id);
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Set new password key for user.
     * This key can be used for authentication when resetting user's password.
     *
     * @param	int
     * @param	string
     * @return	bool
     */
    function set_password_key($user_id, $new_pass_key) {
        $this->db->set('new_password_key', $new_pass_key);
        $this->db->set('new_password_requested', date('Y-m-d H:i:s'));
        $this->db->where('id', $user_id);

        $this->db->update($this->table_name);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Check if given password key is valid and user is authenticated.
     *
     * @param	int
     * @param	string
     * @param	int
     * @return	void
     */
    function can_reset_password($user_id, $new_pass_key, $expire_period = 900) {
        $this->db->select('1', FALSE);
        $this->db->where('id', $user_id);
        $this->db->where('new_password_key', $new_pass_key);
        $this->db->where('UNIX_TIMESTAMP(new_password_requested) >', time() - $expire_period);

        $query = $this->db->get($this->table_name);
        return $query->num_rows() == 1;
    }

    /**
     * Change user password if password key is valid and user is authenticated.
     *
     * @param	int
     * @param	string
     * @param	string
     * @param	int
     * @return	bool
     */
    function reset_password($user_id, $new_pass, $new_pass_key, $expire_period = 900) {
        $this->db->set('password', $new_pass);
        $this->db->set('new_password_key', NULL);
        $this->db->set('new_password_requested', NULL);
        $this->db->where('id', $user_id);
        $this->db->where('new_password_key', $new_pass_key);
        $this->db->where('UNIX_TIMESTAMP(new_password_requested) >=', time() - $expire_period);

        $this->db->update($this->table_name);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Change user password
     *
     * @param	int
     * @param	string
     * @return	bool
     */
    function change_password($user_id, $new_pass) {
        $this->db->set('password', $new_pass);
        $this->db->where('id', $user_id);

        $this->db->update($this->table_name);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Set new email for user (may be activated or not).
     * The new email cannot be used for login or notification before it is activated.
     *
     * @param	int
     * @param	string
     * @param	string
     * @param	bool
     * @return	bool
     */
    function set_new_email($user_id, $new_email, $new_email_key, $activated) {
        $this->db->set($activated ? 'new_email' : 'email', $new_email);
        $this->db->set('new_email_key', $new_email_key);
        $this->db->where('id', $user_id);
        $this->db->where('activated', $activated ? 1 : 0);

        $this->db->update($this->table_name);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Activate new email (replace old email with new one) if activation key is valid.
     *
     * @param	int
     * @param	string
     * @return	bool
     */
    function activate_new_email($user_id, $new_email_key) {
        $this->db->set('email', 'new_email', FALSE);
        $this->db->set('new_email', NULL);
        $this->db->set('new_email_key', NULL);
        $this->db->where('id', $user_id);
        $this->db->where('new_email_key', $new_email_key);

        $this->db->update($this->table_name);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Update user login info, such as IP-address or login time, and
     * clear previously generated (but not activated) passwords.
     *
     * @param	int
     * @param	bool
     * @param	bool
     * @return	void
     */
    function update_login_info($user_id, $record_ip, $record_time) {
        $this->db->set('new_password_key', NULL);
        $this->db->set('new_password_requested', NULL);

        if ($record_ip)
            $this->db->set('last_ip', $this->input->ip_address());
        if ($record_time)
            $this->db->set('last_login', date('Y-m-d H:i:s'));

        $this->db->where('id', $user_id);
        $this->db->update($this->table_name);
    }

    /**
     * Ban user
     *
     * @param	int
     * @param	string
     * @return	void
     */
    function ban_user($publisher_id, $reason = NULL) {
        $this->db->where('publisher_id', $publisher_id);
        $this->db->update($this->table_name, array(
            'banned' => 1,
            'ban_reason' => $reason,
        ));
    }

    /**
     * Unban user
     *
     * @param	int
     * @return	void
     */
    function unban_user($publisher_id) {
        $this->db->where('publisher_id', $publisher_id);
        $this->db->update($this->table_name, array(
            'banned' => 0,
            'ban_reason' => NULL,
        ));
    }

    /**
     * Create an empty profile for a new user
     *
     * @param	int
     * @return	bool
     */
    private function create_profile($user_id) {
        $this->db->set('user_id', $user_id);
        return $this->db->insert($this->profile_table_name);
    }

    /**
     * Delete user profile
     *
     * @param	int
     * @return	void
     */
    private function delete_profile($user_id) {
        $this->db->where('user_id', $user_id);
        $this->db->delete($this->profile_table_name);
    }

    function update_user($user_id, $data) {
        $this->db->where('id', $user_id);

        $this->db->update($this->table_name, $data);
        return $this->db->affected_rows() > 0;
    }

    function update_all_users($data) {
        $this->db->update($this->table_name, $data);
        return $this->db->affected_rows() > 0;
    }

    function get_users_by_date($fecha_desde, $fecha_hasta) {
        $query = $this->db->query("select u.*, a.nombre_completo as nombre_ejecutivo from $this->table_name u, admins a where date(u.created) between '$fecha_desde' and '$fecha_hasta' and u.id_ejecutivo_medios=a.id;");

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

}

/* End of file users.php */
/* Location: ./application/models/auth/users.php */