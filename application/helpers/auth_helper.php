<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Functions for knowing what's the session state now -
 * meaning for knowing if the user is logged in or not.
 * Generally just allows calling functions from "auth" without having to type the class name all the time
 *
 * Expects the "auth" library to be loaded.
 * @author Trevize
 */

/**
 * Returns the ID of the currently logged-in user (corresponds to the 'id' from the 'users' table)
 */
function get_user_id() {
	$CI =& get_instance();	
	return $CI->auth->get_user_id();
}

function get_human_name() {
	$CI =& get_instance();	
	return $CI->auth->get_human_name();
}

function verify_logged_in_user() {
	$CI =& get_instance();
	return $CI->auth->verify_logged_in_user();
} 

function get_fb_id($refresh_session = false, $force_refresh_session = false){
	$CI =& get_instance();
	return $CI->auth->get_fb_id($refresh_session, $force_refresh_session);	
}

function is_fb_logged(){
	$CI =& get_instance();
	return $CI->auth->is_logged_from_facebook();
}

function get_user_prop($prop) {
	$CI =& get_instance();
	return $CI->auth->get_user_prop($prop);
}

function get_all_user_props() {
	$CI =& get_instance();
	return $CI->auth->get_all_user_props();
}

function get_user_app_friends() {
	$CI =& get_instance();
	return $CI->auth->get_user_app_friends();
}

function get_fb_token() {
	$CI =& get_instance();
	return $CI->auth->get_fb_token();
}


function get_session_cached_info() {
	$CI =& get_instance();
	return $CI->auth->get_session_cached_info();
}


function mysession_get($key_name, $k2 = null, $k3 = null) {
	$CI =& get_instance();
	// Just call the function from the 'auth' library with the same paramters
	return call_user_func_array(array($CI->auth, 'mysession_get') , func_get_args());
}

function mysession_set($key_name, $value) {
	$CI =& get_instance();
	// Just call the function from the 'auth' library with the same paramters
	return call_user_func_array(array($CI->auth, 'mysession_set') , func_get_args());
}

function mysession_unset($key_name, $k2=null) {
	$CI =& get_instance();
	// Just call the function from the 'auth' library with the same paramters
	return call_user_func_array(array($CI->auth, 'mysession_unset') , func_get_args());
}


function is_admin() {
	$CI =& get_instance();
	return $CI->auth->is_admin();
}

function is_normal_user() {
	$CI =& get_instance();
	return $CI->auth->is_normal_user();
}

