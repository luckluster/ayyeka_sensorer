<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * Config variables related to the 'auth' library.
 * 
 */


/*
|--------------------------------------------------------------------------
| DB field names
|--------------------------------------------------------------------------
| Name of important DB fields - just so it'll be easier to change them in a different application.
|
*/
$config['AUTH__user_fields'] = array (
	'id' 				=> 'usr_id',
	'username' 			=> 'usr_name',
	'password' 			=> 'usr_password',   // may be hashed!
	'type' 				=> 'usr_type',  
	'status' 			=> 'usr_status',
	'full_name' 		=> 'usr_fullname',
	'last_login' 		=> 'usr_last_login',
	'facebook_id' 		=> 'usr_fb_id',
	'twitter_id' 		=> 'usr_twitter_id'
);


/*
|--------------------------------------------------------------------------
| User statuses
|--------------------------------------------------------------------------
| Used in the users table
|
*/
define ('USER_STATUS__NEW', 1);  // Might not always be in use. Depends on the app
define ('USER_STATUS__ACTIVE', 2);  
define ('USER_STATUS__BANNED', 3);

// User statuses which may log-in
$config['AUTH__allowed_user_statuses'] = array (USER_STATUS__NEW, USER_STATUS__ACTIVE);

//User types
define ('USER_TYPE__NORMAL', 1);
define ('USER_TYPE__ADMIN', 100);
