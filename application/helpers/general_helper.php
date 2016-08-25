<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * General utility functions - autoloaded
 * The type of functions which are needed ALL THE TIME
 * @author Trevize
 */


/**
 * Shorter than writing $this->config->item every time.
 * @param string $item
 * @param string $index
 * @return string (maybe)
 * @author Trevize
 */
function my_config_item($item, $index = '') {
	$CI =& get_instance();
	
	return $CI->config->item($item, $index);
}

/**
 * Returns a general setting from the 'general' table
 * Caches all items from table to memory on first call,
 * so you can call this function as many times you want.
 * 
 * Note on caching:  uses session if logged in, otherwise gets the data by using a caching call
 * @param string $page_name
 * @param string $field_name
 * @return string
 */
function get_general_setting($page_name, $field) {
	$CI =& get_instance();
	
	// Get all settings from DB (a cached call)
	// Global variable exists?
	if (!isset($GLOBALS['GLB_get_general_settings_values'])) {
		// Put into the global variable to prevent multiple file reads
		$CI->load->model('general_model');
		// GEt all settings from the caching function. Also load into a global variable.
		$GLOBALS['GLB_get_general_settings_values'] = $CI->general_model->get_all_settings__cached();
	}
	$all_settings = $GLOBALS['GLB_get_general_settings_values'];
	  
	// Now find the item
	$ret = null;
	
	foreach ($all_settings as $setting) {
		if ($setting['gnr_page_name'] == $page_name && $setting['gnr_field'] == $field) {
			$ret = $setting['gnr_value'];
		}		
	}
	return $ret;
}

// See MY_Controller::my_load_lang()
function my_load_lang($lang_file) {
	$CI =& get_instance();
	$CI->my_load_lang($lang_file); 
}

/**
 * Requested by Greg as a quick way to get language keys
 * @param string $key
 * @param array $params - see below
 * @return string
 */
function l($key, $params = array()) {
	// $params may contain
	//   'no_default_text' - don't return the default text (the $key) if no language line was found for this item
	$CI =& get_instance();
	$ret = $CI->lang->line($key);

	if (!strlen($ret)) {
		if (!arr_get_value($params, 'no_default_text')) {
			$ret = $key;
		}
	}
	return $ret;
}


/**
 * Translates a string and then runs a replace operation on it
 * @param string $key
 * @param mixed $search - passed to str_replace
 * @param mixed $replace - passed to str_replace
 * @param array $params - passed to l()
 * @return string
 */
function lang_r($key, $search, $replace, $params = array())  {
	$ret = l($key, $params);
	
	$ret = str_replace($search, $replace, $ret);
	
	return $ret;
}
/**
 * Translates a string and then runs a replace operation on it - using a nicer array than lang_r
 * @param string $key
 * @param string/array $search - if an array, it'll be in the format of array('search' => 'replace_to'). Otherwise string
 * @param mixed $replace - string to replace, if $search is string
 * @param array $params - passed to l()
 * @return string
 */
function lang_r2($key, $search, $replace = null, $params = array())  {
	$ret = l($key, $params);
	if (!is_array($search)) {
	
		$ret = str_replace($search, $replace, $ret);
	} else {
		foreach ($search as $search_for => $replace_to) {
			$ret = str_replace($search_for, $replace_to, $ret);
		}
	}
	
	return $ret;
}

/**
 * Returns an item from the array, or $default if not found / not an array 
 * @param array $arr
 * @param mixed $key
 * @param mixed $default
 * @author Trevize
 */
function arr_get_value($arr, $key, $default = null) {
	if (!is_array($arr) || !isset($arr[$key])) {
		return $default;
	} 
	
	return $arr[$key];
}

/**
 * An easy way to turn an array into an SQL 'IN' clause
 * @param string $field_name
 * @param array $field_values
 * @param boolean $escape
 * @return string
 */
function make_in_clause($field_name, array $field_values, $escape = true) {
	$CI =& get_instance();
	
	if (!count($field_values)) {
		return "0=1  # make_in_clause exclusion for $field_name";
	}
	
	$items = array();
	foreach ($field_values as $value) {
		
		$items []= ($escape ?  $CI->db->escape($value) : $value); 
	}
	
	
	$ret = "$field_name IN (".implode(",", $items).")";
	
	//echo "WHAT".$ret;
	
	return $ret;
}

/**
 * Returns the current date-time in SQL format
 * @param DateTime $datetime_obj - may be omitted in order to show the current date time
 * @return string
 */
function sql_time($datetime_obj = null, $date_only = false) {
	if (is_null($datetime_obj)) {
		$datetime_obj = new DateTime();
	}
	$format = ($date_only ? 'Y-m-d' : 'Y-m-d H:i:s');
	return $datetime_obj->format($format);
}

/**
 * Another code that may repeat a lot
 * @param int/null $limit
 * @param int/null $offset
 * @return string
 */
function get_limit_string($limit, $offset) {
	$ret = "";
	if (!is_null($limit) && $limit > 0) {
		if (!is_null($offset) && $offset > 0) {
			//$db->limit($limit, $offset);
			$ret = " LIMIT $offset, $limit ";
		} else {
			$ret = " LIMIT $limit ";
		}
	}
	return $ret;
	
}


/**
 * Returns the date in format useful for the local humans
 * @param string/datetime_obj $date
 * @param boolean/null $show_time - show the time or not? By default tries to detect if it contains time
 * 	- autodetection only works for SQL dates (strings)
 * @return string
 */
function get_human_date($date,  $show_time = null) {
	if (is_string($date)) {
		if (!strlen($date) || substr($date, 0, 4) == "0000") {
			return "";
		}
		// Try to detect if it has a time in it
		if (is_null($show_time)) {
			$show_time = (strpos($date, " ") !== false);
		}
		
	}
	if (!is_a($date, 'DateTime')) {
		$date = new DateTime($date);
	}
	if ($date->format('Y') == 0) {
		return "";
	}
	$format = (!$show_time ? 'M d, Y' : 'H:i:s M d, Y');
	return $date->format($format); 
}

/**
 * Turns the user date of birth into age (in years)
 * @param string $date - SQL date
 * @return int
 */
function get_age($date) {
	$interval  = date_diff(new DateTime("now"), new DateTime($date));
	return $interval->format("%y");
}

/**
 * Converts string from datepicker (dd/mm/yyyy) to SQL date (yyyy-mm-dd)
 * @param string $date
 * @return string
 */
function pickerdate_to_sqldate($date) {
	$a = explode(".", $date);
	return arr_get_value($a, 2)."-".arr_get_value($a, 1)."-".arr_get_value($a, 0);
}

/**
 * It's fun to dump information, isn't it?
 * @param mixed $var
 * @param boolean $return_it - don't print it but make it the function return value
 * @param boolean $exit_after - kill script afterwards 
 * @return string
 */
function my_print_r($var, $exit_after = false, $return_it = false) {
	$result = "<div dir='ltr' align='left'><pre>".print_r($var, true)."</pre></div>";
	
	if ($return_it) {
		return $result;
	} else {
		echo $result;
		if ($exit_after)  exit();
	}
}


/**
 * Returns a constant without a warning if it doesn't exist
 * @param string $constant_name
 * @return mixed
 */
function get_constant($constant_name) {
	if (defined($constant_name)) {
		return constant($constant_name);
	}
}


function is_valid_sql_date($date) {
	$a = explode("-", $date);
	$ret = checkdate(0+arr_get_value($a, 1), 0+arr_get_value($a, 2), 0+arr_get_value($a, 0));
	return $ret; 
}

function string_begins_with($string, $begins_with) {
	return substr($string, 0, strlen($begins_with)) == $begins_with; 
}


function is_allowed_image_filename($filename) {
	$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
	$allowed_extensions = array('gif', 'jpg', 'jpeg', 'png');
	if (!in_array($ext, $allowed_extensions)) {
		return false;
	}
	return true;
}

function full_url($remove_params = array(),$add_params=array())
{
	$ci=& get_instance();
	$return = $ci->config->site_url().$ci->uri->uri_string();
	
	$get = array();
	if(count($_GET) > 0)
	{
		foreach($_GET as $key => $val)
		{
			if (!in_array($key, $remove_params))
				$get[$key] = $key.'='.$val;
		}
	}

	if(count($add_params) > 0)
	{
		foreach($add_params as $key => $val)
		{
			$get[$key] = $key.'='.$val;
		}
	}
	if (count($get))
		$return .= '?'.implode('&',$get);
	
	
	return $return;
}


/**
 * Returns an IMG SRC if $img_src is not empty
 * @param string $img_src
 * @param string $extra
 * @return string
 */
function html_conditional_image($img_src, $extra = "") {
	if ($img_src) {
		return "<img src='$img_src' $extra />";
	}
}


/**
 * Redirects the user to a page asking him to login,
 * and after login, the user will return to the current URL
 */
function ask_user_to_login_first() {
	redirect(site_url('/login/?redirect_to='.urlencode($_SERVER['REQUEST_URI'])));
}


/**
 * Just returns the difference in days between old date and new date
 * @param string/DateTime $old_date
 * @param string/DateTime $new_date
 * @return int
 */
function date_diff_in_days($old_date, $new_date) {
	$interval = date_diff(date_create($old_date), date_create($new_date));
	return $interval->format('%r%a');  // %r means there will be a - sign if negative (shouldn't happen!)
}


/**
 * Limit long texts
 * @param unknown_type $str
 * @param unknown_type $ln
 * @param unknown_type $add_points
 */
function trim_text($str, $ln,$add_points = true) {
	if (!$ln)
		return $str;
	$str = strip_tags(html_entity_decode($str));
	if (trim ( mb_strlen ( $str ) ) > $ln) {
		$str = trim(mb_substr ( $str, 0, (mb_strrpos ( mb_substr ( $str, 0, $ln ), ' ' )) ),',-');
		if ($add_points)
			$str .= '...';
	}
	return $str;
}
