<?php
// NOTE: Autoloaded for your convenience
// Model for users. 

class Users_model extends MY_Model {
	
	function __construct() {
		$this->init_model_values(TBL_PREFIX.'users', 'usr_id'); 
		parent::__construct();
	}
	
	
	function get_all_users() {
		$query = $this->db->query('select * from '.$this->get_table_name());
		return $query->result_array();
	}
	
	
	
	/**
	 * Makes a query of frequently used data about the user, to be stored in the session for quick access.
	 *  
	 * @param int $user_id - put null if searching by something else
	 * @param array $params - other options:
	 * 	'usr_email' =>  search by email address instead
	 *  'usr_fb_id' => search by fb id, yup
	 *  'usr_twitter_id' => search by twitter id
	 * 
	 * @return array 
	 * 
	 * Format of returned array:
	 * $ret = array (
	 *   'usr_id', 'user_first_name' ...  and all other records from 'users' table
	 *   'city_id', 'city_name', 'rgn_id', 'rgn_name', 'ctr_id', 'ctr_name'
	 * )  
	 */
	public function get_user_info_for_session($user_id, array $params = array()) {
		$user_id = (int)$user_id;
		$sql = "
		SELECT 
			user.* 
			 
		FROM ".TBL_PREFIX."users AS user
		
		";
		if ($user_id) {
			$sql .= "WHERE ".$this->get_index_name()."=$user_id ";
		} elseif ($usr_username = arr_get_value($params, 'usr_username')) {
			$sql .= "WHERE usr_username=".$this->db->escape($usr_username)." ";
		} elseif ($usr_email = arr_get_value($params, 'usr_email')) {
			$sql .= "WHERE usr_email=".$this->db->escape($usr_email)." ";
		} elseif ($usr_fb_id = arr_get_value($params, 'usr_fb_id')) {
			$sql .= "WHERE usr_fb_id=".$this->db->escape($usr_fb_id)." ";
		} elseif ($usr_twitter_id = arr_get_value($params, 'usr_twitter_id')) {
		$sql .= "WHERE usr_twitter_id=".$this->db->escape($usr_twitter_id)." ";
		} else {
			throw new Exception ("get_user_info_for_session: no means of search were specified!");
		}
			
		$row = $this->generic_query($sql, false);
	
		
		return $row;
	}
	
	
	/**
	 * Data callback for Orca_grid
	 * @param array $params - standard options sent by orca_grid - see documentation in orca_grid class (section 'data_callback')
	 * @return array - of rows, or just a single item: 'record_count'
	 */
	public function get_users_for_grid($params) {
		$where_text = "WHERE 1=1";
		if (arr_get_value($params, 'filters')) {
			//my_print_r($params['filters']);
			$where_text .= Orca_grid::get_sql_filter_text($params['filters'], true);
		}
		
		if (!arr_get_value($params, 'count_records')) {
			$sql  = "SELECT * FROM ".$this->get_table_name()." ".$where_text;
			
			if ($order_field = arr_get_value($params, 'order_field')) {
				$sql .= " ORDER BY $order_field ".(arr_get_value($params, 'order_dir') == "A" ? "ASC" : "DESC"); 
			}
			
			$sql .= get_limit_string(arr_get_value($params, 'limit'), arr_get_value($params, 'offset'));
			
			if (arr_get_value($params, 'filters')) {
				//my_print_r($sql);
			}
			
			return $this->generic_query($sql);
		} else {
			$sql = "SELECT COUNT(*) as record_count FROM ".$this->get_table_name()." ".$where_text;
			return $this->generic_query($sql, false);
		}
		
		
	}
	
	
	/**
	 * Checks if the email address is use in the system
	 * If so, returns the user ID owning it
	 * @param string $email
	 * @param number $user_id - ID of current user, to exclude it from the test (0 if this is user registration)
	 * @return int  (false if not in use)
	 */
	public function email_is_in_use($email, $user_id = 0) {
		$email = $this->db->escape($email);
		$user_id = (int)$user_id;
		$sql = "
		SELECT ".$this->get_index_name()." FROM ".$this->get_table_name()." 
		WHERE (usr_email=$email OR usr_artist_paypal_email=$email)
		";
		if ($user_id) {
			$sql .= " AND ".$this->get_index_name()."!= $user_id";
		}
		$row = $this->generic_query($sql, false);
		return $row ? $row[$this->get_index_name()] : false;
	}
		
	
	
	/**
	 * Gets info about all your friends that use this app
	 * ordered by their total score
	 * [ Currently not in use ]
	 * @param array $fb_friends - array in the format of facebook API call: "/me/friends"
	 * @return array (usr_id => (some fields about this user, including 'badges' nested array), ...)
	 */
	public function get_user_app_friends($fb_friends){
		//Pull users that use the app and match them with the fb_users array
		$friends = array();
		//my_print_r($fb_friends);
		
		if ($fb_friends) {
			$fb_ids = array();
			foreach ($fb_friends as $friend) {
				$fb_ids []= $friend['id'];
			}
			
			$sql = "
			SELECT user.id AS usr_id,usr_fullname,usr_fb_id
			FROM " . $this->get_table_name () . " AS user 
			WHERE  " . make_in_clause ( "usr_fb_id", $fb_ids ) . "

			";
					
					
			$friends = $this->generic_query($sql, true, 'usr_id');
			
			// Let's get their badges too
			if (false && $friends) {
				$this->load->model('user_badges_model');
				$badges = $this->user_badges_model->get_users_badges(array_keys($friends));
				foreach ($friends as $k => $row) {  
					$friends[$k]['badges'] = $badges[$row['usr_id']];  // copy from function to returned array
				}
			}	
			
		}
		return $friends;
	}
	
	/**
	 * Just creates the user and returns the new user ID
	 * @param array $user_data
	 * @return int
	 */
	public function create_user($user_data) {
		$new_id = $this->add_new_record($user_data); 
		return $new_id;
	}


	
	/**
	 * Gets artist data
	 * @param int $user_id
	 * @param int $user_type - USER_TYPE_X or 0 if no check
	 */
	public function get_user($user_id,$user_type = 0){
		
		$conditions = array ('id' => (int)$user_id);
		if ($user_type) {
			$conditions['usr_type'] = (int)$user_type;
		}
		$user = $this->get_by_conditions(array(
				'conditions' => $conditions,
				'multiple_records'=>false,
		) );
		$user['image'] = $this->_get_user_small_progile_image_url($user);
		return $user;		
	}

	
	/*****************
	 * PRIVATE METHODS 
	 ****************/
	

	/**
	 * Fetches user's small profile image url
	 * @param unknown_type $artist
	 */
	private function _get_user_small_progile_image_url($user){
		$this->load->library('files_profile_image');
	
		if (!$user['usr_image_filename']) {
			return "";
		}
		return $this->files_profile_image->get_file_url($user['usr_image_filename'], Files_profile_image::ALTRES_HEADER);
	}
	
	
	
	
}