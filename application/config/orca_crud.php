<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	//For view all the languages go to the folder assets/orca_crud/languages/
	$config['orca_crud_default_language']	= 'english';

	// There are only three choices: "uk-date" (dd/mm/yyyy), "us-date" (mm/dd/yyyy) or "sql-date" (yyyy-mm-dd)
	$config['orca_crud_date_format']			= 'uk-date';

	// The default per page when a user firstly see a list page
	$config['orca_crud_default_per_page']	= 25;

	$config['orca_crud_file_upload_allow_file_types'] 		= 'gif|jpeg|jpg|png|tiff|doc|docx|txt|odt|xls|xlsx|pdf|ppt|pptx|pps|ppsx|mp3|m4a|ogg|wav|mp4|m4v|mov|wmv|flv|avi|mpg|ogv|3gp|3g2';
	$config['orca_crud_file_upload_max_file_size'] 			= '20MB'; //ex. '10MB' (Mega Bytes), '1067KB' (Kilo Bytes), '5000B' (Bytes)

	//You can choose 'ckeditor','tinymce' or 'markitup'
	$config['orca_crud_default_text_editor'] = 'ckeditor';
	//You can choose 'minimal' or 'full'
	$config['orca_crud_text_editor_type'] 	= 'full';

	//The character limiter at the list page, zero(0) value if you don't want character limiter at your list page
	$config['orca_crud_character_limiter'] 	= 30;

	//Having some options at the list paging. This is the default one that all the websites are using.
	//Make sure that the number of orca_crud_default_per_page variable is included to this array.
	$config['orca_crud_paging_options'] = array('10','25','50','100');

	/**********************/
	/***** CONSTANTS  *****/
	/**********************/
	
	
	/*
	|--------------------------------------------------------------------------
	| CRUD endpoints
	|
	*/
	define('CRUD_ENDPOINT_CREATE', 'create');
	define('CRUD_ENDPOINT_READ', 'read');
	