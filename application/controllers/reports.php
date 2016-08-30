<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reports extends MY_Controller {
	
	public function __construct() {
		parent::__construct();
		$this->load->model('machines_model');
	}

	public function index() {
		if (!get_user_id()) {
			redirect(site_url('main/login'));
		}
		
		
		$orca_grid_container = Orca_grid::get_grid_container(
			'rprt', 
			site_url('/reports/grid_content_function'),
			array(
				'container_datacallback_params' => array (
					'mcn_id' => $this->input->get('machine_id'), 
					'theperiod' => $this->input->get('theperiod'),
					'sql_time_from' =>  $this->input->get('sql_time_from'),
					'sql_time_until' =>  $this->input->get('sql_time_until')
				)
			)
		);
		//$orca_grid_container  = "";  
		$page_data = array(
			'orca_grid_container' => $orca_grid_container,
			'user_machines' => array('0' => 'All') +  $this->machines_model->get_user_machines(get_user_id())
			
		);
		$page_params = array (
// 			'scripts' => array (
// 				array('script' => 'muki.js', 'init_line' => 'alert("Hi all!");')
// 			)
		);
		
		$this->render_view("reports/reports-index", $page_data, $page_params);
		
	}
	
	/**
	 * AJAX call: Provides the content of the grid
	 */
	public function grid_content_function() {
		if (!get_user_id()) {
			echo "not logged in!!!";
			return;
		}
		$this->load->model('data_processed_model');
		//echo json_encode(array('ok' => 1, 'data' => 'meow'));
		//exit();
		
		
		$grid_params = array (
			'hide_filters_row_by_default' => true,
			'actions' => array (
				'edit_link' => '',
				'delete_link' => '',
				'addnew_link' => '',
			),
			'rpp_presets' => array(10, 20, 50, 100, 'A'),
			'default_rpp' => 10,
			'date_format_callback' => 'get_human_date',  // This function is defined in general_helper
			'fields' => array (
//				'Things' => array (
//					'type' => 'flags',
//					//'title' => 'sadf'
//				),
				'mcn_id' => array (
					'title' => 'Machine ID',
					'type' => 'string',
					'td_id' => 'usr_id__[[usr_id]]',  // just for testing
					'td_class' => 'my_nice_user_2', // just for testing
					//'filter' => array('type' => 'equals'),
					'order_field' => true,
				),
				'mcn_title' => array (
					'title' => 'Machine Title',
					'type' => 'string',
					'order_field' => true
				),
				
				'dp_grouping_value' => array (
					'title' => 'Period',
					'type' => 'string',
					'order_field' => true
				),
				
				'dp_values_count' => array (
					'title' => 'Number of values logged',
					'type' => 'string',
					'order_field' => true
				),
				
				'dp_value' => array (
					'title' => 'Average Value',
					'type' => 'string',
					'order_field' => true
				),

			
			),
			'data_callback' => array ($this->data_processed_model, 'get_procssed_data_for_grid'),
			
		);
		
		
		$grid = new Orca_grid($grid_params);
		$grid->render_grid();
	}
	
	
}
