<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');	
/**
 * For hashing those pesky passwords
 * Autoloaded
 */

/**
 * Hashes the password with the agreed one-way hash, 
 * so we can save it to the db and/or compare the db value to the user-supplied value  
 * @param string $password
 * @return string
 */
function password__crypt($password) {
	$result = hash("md5", my_config_item('SETTINGS_salt').$password); 
	return $result;
}

/**
 * Checks if the password isn't too dumb
 * @param string $password
 * @param string $username - since the password isn't allowed to be euqal to this
 * @return true / error string
 */
function password__is_complicated_enough($password, $username)  {
	// Uses language strings from general_lang
	$forbidden_passwords = array (
		'password',
		'123456',
		'1234567',
		'12345678',
		'654321',
		'7654321',
		'87654321',
		'1q2w3e',
		'1q2w3e4r',
		'qazwsx',
		'qazwsxedc',
		'qwerty',
		'qweasd',
		'carica',
		'caricame'
	);
	
	if ($username == $password) {
		return l("PASSWORD__cannot_be_the_same_as_username");
	}
	$min_password_length = my_config_item('SETTINGS_min_password_length');
	if (strlen($password) < $min_password_length) {
		return lang_r("PASSWORD__need_to_be_at_least_{n}_characters", "{n}", $min_password_length); 
	}
	
	if (in_array(strtolower($password), $forbidden_passwords)) {
		return (l("PASSWORD__too_simple_please_choose_something_less_obvious"));
	}
	
	// Count how many unique characters are in the password
	$chars = array();
	for($i=0; $i<strlen($password); $i++) {
		$chars[$password[$i]] = 1;
	}
	
	$min_password_chars = 4;
	if (count($chars) < $min_password_chars) {
		return lang_r("PASSWORD__too_simple_we_require_at_least_{n}_different_chars", "{n}", $min_password_chars);
	}

	return true;
}