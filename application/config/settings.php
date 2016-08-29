<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
SETTINGS - 
Put all the application settings here - such as default language, folders and so.
Notice that these settings are relevant to applications both in DEVELOPMENT  AND  PRODUCTION

For settings which change between machines, use local_machine_settings.php which is in the root folder.

For constants (for enumarating meanings, such as GENDER__x), use defs.php in this folder.

Thank you.
 - Yaron 
 */


/*
|--------------------------------------------------------------------------
| General stuff
|--------------------------------------------------------------------------
*/
define ('TBL_PREFIX', '');


// For my_load_lang
$config['SETTINGS__default_language'] = 'english';

// For encrypting the passwords
// WARNING: changing this will render all existing accounts inaccessible!
$config['SETTINGS__salt'] = "";  // currently no salt...
// yup
$config['SETTINGS__min_password_length'] = 6;



/*
|--------------------------------------------------------------------------
| Email addresses
|--------------------------------------------------------------------------
*/
// Error reporting: send error reports to these emails - comma separated
$config['EMAIL_error_reporting_addresses'] = 'yaronk@3fishmedia.com, yaronk@3fishmedia.com';


// Program email address
$config['EMAIL_send_from_address'] = 'no-reply@caricame.com';
$config['EMAIL_send_from_name'] = 'CaricaMe';



