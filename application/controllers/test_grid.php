<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test_grid extends MY_Controller {
	

	public function index() {
		$orca_grid_container = Orca_grid::get_grid_container('my_grid_identifier', site_url('/test_grid/testgrid_content_function'));
		//$orca_grid_container  = "";  
		$page_data = array('orca_grid_container' => $orca_grid_container);
		$page_params = array (
// 			'scripts' => array (
// 				array('script' => 'muki.js', 'init_line' => 'alert("Hi all!");')
// 			)
		);
		
		$this->render_view("test_grid/test_grid-index", $page_data, $page_params);
		
	}
	
	/**
	 * AJAX call: Provides the content of the grid
	 */
	public function testgrid_content_function() {
		$this->load->model('users_model');
		$this->load->config('auth_config'); // whatever!
		//echo json_encode(array('ok' => 1, 'data' => 'meow'));
		//exit();
		
		$user_status_values = array (
			null => 'All',
			USER_STATUS__ACTIVE => 'Active',
			USER_STATUS__NEW => 'New',
			USER_STATUS__BANNED => 'Banned'
		);
		
		$grid_params = array (
			'hide_filters_row_by_default' => true,
			'actions' => array (
				'edit_link' => '',
				'delete_link' => '',
				'addnew_link' => '',
			),
			'rpp_presets' => array(2,3,10, 'A'),
			'default_rpp' => 3,
			'date_format_callback' => 'get_human_date',  // This function is defined in general_helper
			'fields' => array (
				'Things' => array (
					'type' => 'flags',
					//'title' => 'sadf'
				),
				'Edit link' => array (
					'type' => 'url',
					'url' => site_url('/do/some/thing/with/[[usr_id]]'),
					'url_caption' => 'Edit user no. [[usr_id]]',
					'td_id' => 'usr_id[[usr_id]]', // just for testing
					'url_new_window' => true					
				),
				'usr_name' => array (
					'title' => 'Name',
					'type' => 'string',
					'td_id' => 'usr_id__[[usr_id]]',  // just for testing
					'td_class' => 'my_nice_user_2', // just for testing
					'filter' => array('type' => 'contains'),
					'order_field' => true,
					'html_class' => 'class-for-first-name'
				),
				'usr_status' => array (
					'title' => 'Status',
					'type' => 'key_value',
					'values' => $user_status_values,
					'filter' => array('type' => 'dropdown', 'values' => $user_status_values, 'default_value' => USER_STATUS__ACTIVE) 
				),

				'usr_phone' => array (
					'title' => 'Phone',
					'type' => 'string',
					'filter' => array('type' => 'contains'),
					'order_field' => true
				),
// 				'worker_id' => array(
// 					'type' => 'dropdown',
// 					'callback_function' => array($this->workers_model, 'get_workers_for_dropdown'),
// 					'title_field' => 'worker_title',
// 					'id_field' => 'worker_id',
// 				),
				'usr_last_login' => array (
					'title' => 'Last login',
					'type' => 'datetime',
					'order_field' => true
				),

			
			),
			'data_callback' => array ($this->users_model, 'get_users_for_grid'),
			
// 			'data' => array (
// 				array ('usr_firstname' => 'muki',	'usr_last_login' => '2012-10-10', 'usr_android_registration_id' => 5),
// 				array ('usr_firstname' => 'shuki',	'usr_last_login' => '2012-12-11', 'usr_android_registration_id' => 1),
// 				array ('usr_firstname' => 'ariel',	'usr_last_login' => '2012-09-03', 'usr_android_registration_id' => 200)
// 			)
		);
		
		
		$grid = new Orca_grid($grid_params);
		$grid->render_grid();
	}
	
	
}
