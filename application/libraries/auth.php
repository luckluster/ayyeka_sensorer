<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Auth class - for knowing whether a user is logged-on or not, and for information about that user.
 * Auto-loaded.
 * @author Trevize
 * @since 2012-07-08
 */


/*
Data we put in the session
~~~~~~~~~~~~~~~~~~~~~~~~~~

'usr_id' => Local user id from (crm_app_)users table
'usr_password' => the hashed password from the users table

 
// Information about users, or other kind of information, which gets loaded only once during log-in:
'session_cached_info' => array (
	'categories' => all categories ( required for header - returned from $categories_model->get_all_categories() )
	'techniques' => ...
)

// Registration:

// reg from facebook
'reg__use_facebook' => true if reg has to take FB details
'reg__fb_userinfo' =>  result of FB API call:  /me  (array)  


*/

class Auth {
	private $_is_initialized = false;
	// Used when LMC_FAKE_LOGIN_ID - contains the corresponding user row from the DB
	private $_user_info = array();

	private $_user_fields = array();

	// -------------------------------------------------------
	// Infrastracture
	// -------------------------------------------------------
	
	public function __construct() {
		$CI =& get_instance();
		
		if (!$CI->input->is_cli_request()) {
			session_start();
		}
		
		$this->_user_fields = my_config_item('AUTH__user_fields');
	}
	

	/**
	 * Get some information from session/hardcoded debug values - and put it in the program
	 * @throws Exception
	 */
	private function _init() {
		if ($this->_is_initialized) return;
		$CI =& get_instance();
		
		
		
		// In local server always work with usr_id=(constant)  
		$user_id = get_constant('LMC_FAKE_LOGIN_ID');
		if (!$CI->input->is_cli_request() && !$user_id && $this->mysession_get('usr_id')) {
			$user_id = $this->mysession_get('usr_id');
		}
		if ($user_id) {
			$this->_user_info = $CI->users_model->get_user_info_for_session($user_id);
			if (!$this->_user_info) {
				//throw new Exception("DEBUG - problem - I can't find the user ID ".$user_id);
				echo "Problem - I can't find the user id $user_id - please reload the page";
				log_message("error", "Can't find the user id ($user_id) from the session");
				$this->mysession_destroy();
				die();
			}
			if ($this->_user_info[ $this->_user_fields['status'] ] == USER_STATUS__BANNED) {
				echo "X";
				die();
			}
			if ($this->_user_info[ $this->_user_fields['password']] != $this->mysession_get('usr_password')) {
				echo "Seems that your password has been changed. Please log-in again.";
				$this->mysession_destroy();
				die();
			}
	
		}
		$this->_is_initialized = true;
	}
	
	// Updates this object with the new user record
	private function _re_init($user_info) {
		//$this->is_initialized = false;
		$this->_user_info = $user_info;
	}
	
	// -------------------------------------------------------
	// Login material
	// -------------------------------------------------------
	
	/**
	 * Tries to login the user.
	 * If successful, tells where to redirect the user now.
	 * @param array $user_info - stuff fetched earlier by get_user_info_for_session()
	 * @param array $params - may contain:
	 * 	auth methods:
	 * 	'password' - current password - for normal user/password authentication
	 *  'facebook_id' - for FB authentication
	 *  'twitter_id' - for Twitter authentication (later)
	 *  other stuff:
	 *  'redirect_to' - where to redirect the user to:
	 *  	will be used in the returned value, unless this user must access a different page first (for example if he needs to pay or something like that)
	 * @return array 
	 * 	if ok:  array('ok' => 1, 'redirect_url' => <where to send our user now>)
	 * 	if !ok: array('ok' => 0, 'error_msg' => ...)
	 */
	public function try_to_login_user($user_info, $params) {
		if (!$user_info) {
			return  $this->_ttlu_error_response(l('AUTH__account_not_found'));
		}
		$redirect_url = arr_get_value($params, 'redirect_url', site_url('/'));
		
		if (arr_get_value($params, 'password')) {
			if ($user_info[ $this->_user_fields['password'] ] != password__crypt($params['password'])) {
				
				return $this->_ttlu_error_response(l('AUTH__invalid_password'));
//				. "password in db: ".$user_info[ $this->_user_fields['password'] ]."; crypt: ".password__crypt($params['password']));
			}
		} elseif (arr_get_value($params, 'facebook_id')) {
			// should always work!
			if ($user_info[ $this->_user_fields['facebook_id'] ] != $params['facebook_id']) {
				return $this->_ttlu_error_response("VERY unexpected error with FB ID mismatch");
			}
		} elseif (arr_get_value($params, 'usr_twitter_id')) {
			// should always work!
			if ($user_info[ $this->_user_fields['twitter_id'] ] != $params['usr_twitter_id']) {
				return $this->_ttlu_error_response("VERY unexpected error with Twitter ID mismatch"); // {$user_info['usr_twitter_id']} vs  {$params['usr_twitter_id']}");
			}
		}
			
		if (!in_array($user_info[ $this->_user_fields['status'] ], my_config_item('AUTH__allowed_user_statuses'))) {
			return $this->_ttlu_error_response(l("AUTH__account_suspended"));
		}
		
		// Seems ok. Let's log in.
		$this->mark_user_as_logged_in($user_info);
		
		return array('ok' => 1, 'redirect_url' => $redirect_url);
	}
	
	// Just turns a string into the error response of try_to_login_user()
	private function _ttlu_error_response($string) {
		return array('ok' => 0, 'error_msg' => $string);
	}	
	
	/**
	 * Sets everything required in the session and in this class so the user will be marked as logged in
	 * After successfull log-in, call this please!
	 * @param array $user_info - stuff returned from users_model::get_user_info_for_session()
	 */	
	public function mark_user_as_logged_in($user_info) {
		$CI =& get_instance();
		mysession_set('usr_id', $user_info[ $this->_user_fields['id'] ]);
		mysession_set('usr_password', $user_info[ $this->_user_fields['password'] ]);
		
		$user_row_updates = array();
		
		// Update last login time
		$user_row_updates[ $this->_user_fields['last_login'] ] = sql_time();
		
		// Send all updates to SQL server
		$CI->users_model->update_record($user_info[ $this->_user_fields['id'] ], $user_row_updates);
		
		$this->_re_init($user_info);
		return true;
	}
	
	
	// -------------------------------------------------------
	// Session wrapper 
	// (in case we'd later want to use something different than PHP session) 
	// -------------------------------------------------------
	
	/**
	 * Gets a variable from session.
	 * Supports variable number of parameters - each parameter implies a level in the array.
	 * formal declaration:  mysession_get($key_name1, key_name2, ... , key_nameN) -
	 * results in the equivalent of $_SESSION[$key_name1][$key_name2] ...[$key_nameN]
	 * 
	 * @param string $key_name
	 * @return mixed
	 */
	public function mysession_get($key_name) {
		// For simple cases:
		if (func_num_args() == 1) {
			return arr_get_value($_SESSION, $key_name);
		}
		$args = func_get_args();
		
		$v = $_SESSION;
		
		// Sadly, we can't use variable variables with $_SESSION so we'll have to do something else
		// Move towards the requested level in the session array 
		foreach ($args as $arg) {
			if (!isset($v[$arg])) {
				return null;
			}
			$v = $v[$arg];
		}
		return $v;
	}

	/**
	 * Sets a variable in session.
	 * Supports variable number of parameters - each parameter implies a level in the array.
	 * formal declaration:  mysession_get($key_name1, key_name2, ... , key_nameN,  $new_value) -
	 * results in the equivalent of $_SESSION[$key_name1][$key_name2] ...[$key_nameN] = $new_value;
	 *  
	 * Currently: maximum value of N can be 6;  Probably we won't get to such a case
	 * 
	 * 
	 * @param string $key_name
	 * @return mixed
	 */	
	public function mysession_set($key_name,   $value) {
		//$_SESSION[$key_name] = $value;
		$args = func_get_args();
		// You know, all this mess is because we can't use variable variables with $_SESSION
		// (trying to avoid 'eval' in all costs)
		switch (count($args)) {
			case 0: throw new Exception("mysession_set: no parameters specified!");
			case 1: throw new Exception("mysession_set: missing target value");
			case 2:
				$_SESSION[$args[0]] = $args[1];
				break;
			case 3:
				$_SESSION[$args[0]][$args[1]] = $args[2];
				break;
			case 4:
				$_SESSION[$args[0]][$args[1]][$args[2]] = $args[3];
				break;
			case 5:
				$_SESSION[$args[0]][$args[1]][$args[2]][$args[3]] = $args[4];
				break;
			case 6:
				$_SESSION[$args[0]][$args[1]][$args[2]][$args[3]][$args[4]] = $args[5];
				break;
			case 7:
				$_SESSION[$args[0]][$args[1]][$args[2]][$args[3]][$args[4]][$args[5]] = $args[6];
				break;
			//-unindent
		}
	}
	
	/**
	 * Unsets a value from the session.
	 * Formal declaration:  mysession_unset($key_name1, $key_name2, .., $key_nameN)
	 * N max value: 6 
	 * 
	 * @param string $key_name
	 */
	public function mysession_unset($key_name) {
		$args = func_get_args();
		switch (count($args)) {
			case 0: throw new Exception("mysession_set: missing target value");
			case 1:
				unset($_SESSION[$args[0]]);
				break;
			case 2:
				unset($_SESSION[$args[0]][$args[1]]);
				break;
			case 3:
				unset($_SESSION[$args[0]][$args[1]][$args[2]]);
				break;
			case 4:
				unset($_SESSION[$args[0]][$args[1]][$args[2]][$args[3]]);
				break;
			case 5:
				unset($_SESSION[$args[0]][$args[1]][$args[2]][$args[3]][$args[4]]);
				break;
			case 6:
				unset($_SESSION[$args[0]][$args[1]][$args[2]][$args[3]][$args[4]][$args[5]]);
				break;
			//-unindent
		}
	}
	
	public function mysession_get_all() {
		return $_SESSION;
	}
	
	public function mysession_get_session_id() {
		return session_id();
	}
	
	public function mysession_destroy() {
		@session_destroy();
	}
	
	

	// -------------------------------------------------------
	// User information
	// -------------------------------------------------------
	
	
	/**
	 * Gets a user property - which is taken from the session (or something else)
	 * See users_model::get_user_info_for_session for seeing what is in this array
	 * @param string $prop
	 * @return string (probably)
	 */
	public function get_user_prop($prop) {
		$user_info = $this->get_all_user_props();
		return arr_get_value($user_info, $prop);
	}
	
	
	public function get_all_user_props() {
		if (!$this->_is_initialized) {
			$this->_init();
		}
		return $this->_user_info;
	}
	
	
	public function get_session_cached_info() {
		return $this->mysession_get('session_cached_info');
	}

	
	/**
	 * For knowing whether the user is properly logged in or not!  (including an FB session AND an account on our DB)
	 * @return int (0 if not logged in)
	 */
	public function get_user_id() {
		return $this->get_user_prop( $this->_user_fields['id'] );
	}


	
	// Do we need it? Not sure!
	public function verify_logged_in_user() {
		if (!$this->get_user_id()) {
			echo "You are not logged in!!";
			die();
		}
	}
	
	
	public function get_human_name() {
		return get_user_prop( $this->_user_fields['full_name'] );
	}
	

	// Means - non-admin
	public function is_normal_user() {
		return get_user_prop( $this->_user_fields['type'] ) == USER_TYPE__NORMAL; 
	}
	
	public function is_admin() {
		return get_user_prop( $this->_user_fields['type'] ) == USER_TYPE__ADMIN;
	}
	
	
}
