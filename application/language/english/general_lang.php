<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * General language file - for header, footer and frequently repeating text.
 * Should be always loaded (through MY_controller)
 */

$lang['HEADER_my_profile'] = 'My profile';


$lang['HEADER_not_logged_in'] = "Not logged in";
$lang['HEADER_login'] = "Log in";
$lang['HEADER_sign_up'] = "Sign up";


// Some DB fields
$lang['usr_name'] = 'User name';
$lang['usr_fullname'] = 'Full name';
$lang['usr_email'] = 'Email';
$lang['usr_password'] = 'Password';
$lang['usr_artist_paypal_email'] = "Paypal email";
$lang['usr_country_id'] = "Country";


// Some general strings:
$lang['missing_field_{s}'] = "Missing field: {s}";


// Password validation (password helper)
$lang['PASSWORD__cannot_be_the_same_as_username'] = "Password cannot be the same as the username";
$lang['PASSWORD__too_simple_please_choose_something_less_obvious'] = "Password too simple - please use something less obvious.";
$lang['PASSWORD__too_simple_we_require_at_least_{n}_different_chars'] = "Password too simple - please enter at least {n} different characters";
$lang['PASSWORD__need_to_be_at_least_{n}_characters'] = "Password too small - at least {n} characters are required";
$lang['PASSWORD__passwords_do_not_match'] = "Passwords don't match";
$lang['PASSWORD__incorrect_current_password'] = "Incorrect current password!";


// Auth strings
$lang['AUTH__account_not_found'] = "User not found.";
$lang['AUTH__invalid_password'] = "Invalid password!";
$lang['AUTH__account_suspended'] = "Your account is suspended.";
$lang['AUTH__to_access_this_page_you_need_to_login'] = "To access this page, you need to login.";


// user helper stuff
$lang['USERHELPER__profile_image_file_too_big_max_size_{n}'] = "Profile image file is too big (max size: {n})";
$lang['USERHELPER__profile_image_file_invalid'] = "Profile image file is not a valid image";


$lang['GENERAL__saving_msg'] = "Saving...";
$lang['GENERAL__updated_successfully'] = "Updated successfully!";