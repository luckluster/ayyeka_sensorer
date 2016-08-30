<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Input extends MY_Controller {
	
	public function __construct() {
		parent::__construct();
		$this->load->model('machines_model');
	}
	
	public function index()
	{
		
		//$this->load->view('welcome_message');
		if (!get_user_id()) {
			redirect(site_url('main/login'));
		}
		
		$data = array (
			'user_machines' => $this->machines_model->get_user_machines(get_user_id())
		);
		$this->render_view('input/input-index', $data);
	}
	
	
	/**
	 * receives data from a machine.
	 * Updates the 2 tables: data_raw and data_processed
	 * POST parameters: machine_id,  value, sql_time (optional)
	 * returns '1' on success or json error on failure
	 */
	public function receive_data() {
		$this->load->model('data_raw_model');
		$this->load->model('data_processed_model');
		$this->load->model('machines_model');
		print_r($_POST);
		
		// does the machine exist??
		$machine_id = (int)$this->input->post('machine_id');
		$value = 0+$this->input->post('value');  // force to number
		$sql_time = $this->input->post('sql_time');
		if (!$sql_time || !strtotime($sql_time)) {
			$sql_time = sql_time();
		}
		
		$row = $this->machines_model->get_by_primary($machine_id);
		
		
		if (!$row) {
			echo json_encode(array('ok' => 0, 'error_msg' => 'Invalid machine!'));
			die();
		}
		
		// save it!
		$this->data_raw_model->add_machine_value($machine_id, $sql_time, $value);
		$this->data_processed_model->add_machine_value($machine_id, $sql_time, $value);
		
		// yay!
		
		echo json_encode(array('ok' => 1));
	}
	
}
