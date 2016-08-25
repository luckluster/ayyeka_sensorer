<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
CONSTANTS and DROPDOWN LISTS 
For giving numbers to meanings, such as USER GENDER, 
and supplying lists for dropdowns

This is NOT the place for settings, such as folders or database usernames and passwords.

For settings which change between machines, use local_machine_settings.php which is in the root folder.
For settings which are the same for all working copies (such as folder names), use settings.php in this folder.

Thank you,
  - Yaron
 
 */


/*
|--------------------------------------------------------------------------
| General
|--------------------------------------------------------------------------
| 
*/


/*
|--------------------------------------------------------------------------
| Page names and field types
|--------------------------------------------------------------------------
| Used in the 'general' table.
|
*/
define('CONFPAGE_GENERAL', 'general');
define('CONFPAGE_MAIN', 'main');
define('CONFPAGE_ARTIST', 'artist');
define('CONFPAGE_HEADER', 'header'); 

define ('FIELDTYPE_TEXTBOX', 1);
define ('FIELDTYPE_TEXTAREA', 2);
define ('FIELDTYPE_RICHTEXT', 3);
define ('FIELDTYPE_IMAGE', 4);


/*
|--------------------------------------------------------------------------
| Layouts
|--------------------------------------------------------------------------
|
| Chooses what kind of views we'll show
| Such as - web (default), mobile, json (when communicating with a mobile app) 
|
*/
define('LAYOUT_WEB',"web");
define('LAYOUT_EMAIL',"email");
define('LAYOUT_JSON', "json");
define('LAYOUT_MOBILE', "mobile");


/*
|--------------------------------------------------------------------------
| File types - probably not in use now...
|--------------------------------------------------------------------------
|
| For the files stored at the 'files' table. 
|
*/
define ('FILETYPE_ITEM', 1);  // an image of an item


/*
|--------------------------------------------------------------------------
| Gender
|--------------------------------------------------------------------------
| Used for users and items
|
*/
define ('GENDER_UNKNOWN', 0);
define ('GENDER_MALE', 1);
define ('GENDER_FEMALE', 2);

// Lang mapping:
$config['GENDER_options'] = array (
	GENDER_UNKNOWN	=> 'GENDER_UNKNOWN',
	GENDER_MALE 	=> 'GENDER_MALE',
	GENDER_FEMALE 	=> 'GENDER_FEMALE'
);





/* End of file constants.php */
/* Location: ./application/config/constants.php */