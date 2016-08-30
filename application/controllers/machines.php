<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class for showing my machines and adding more machines, yup
 */
class Machines extends MY_Controller {
	
	public function __construct() {
		parent::__construct();
		$this->load->model('machines_model');
		
		if (!get_user_id()) {
			redirect(site_url('main/login'));
		}		
	}
	
	public function index()
	{
		
		$data = array (
			'added_new_machine' => $this->input->get('added_new_machine'),
			'user_machines' => $this->machines_model->get_user_machines(get_user_id())
		);
		$this->render_view('machines/machines-index', $data);
	}
	
	
	/**
	 * Just adds a machine and then redirects back to the index
	 * Expects POST param: machine_title
	 */
	public function add_machine() {
		if (!strlen($this->input->post('machine_title'))) {
			echo "new machine name not specified! :(";
			return;
		}
		
		$this->machines_model->add_new_record (array('mcn_title' => $this->input->post('machine_title'), 'mcn_user_id' => get_user_id()));
		
		redirect(site_url('/machines/?added_new_machine=1'));
	}
	
}
