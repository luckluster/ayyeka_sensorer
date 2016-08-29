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
	
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */