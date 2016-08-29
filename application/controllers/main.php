<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends MY_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		//$this->load->view('welcome_message');
		if (get_user_id()) {
			redirect(site_url('main/dashboard'));
		} else {
			redirect(site_url('main/login'));
		}
		
		$this->render_view('main/test');
	}
	
	
	public function dashboard() {
		$this->render_view('main/main-dashboard');
	}
	
	public function login() {

		$page_data = array (
			'message' => ''
		);
		
		// Do login..
		if ($this->input->post('login')) {
			// Find that user..?
			$user_info = $this->users_model->get_user_info_for_session(null, array('usr_name' => $this->input->post('username')));
				
			$result = $this->auth->try_to_login_user($user_info, 
				array('password' => $this->input->post('password', true), 'redirect_url' => site_url('/main/dashboard'))
			);
			
			if ($result['ok']) {
				//echo "redirecting to {$result['redirect_url']}";
				//return; 
				redirect($result['redirect_url']);
			} else {
				$page_data['message'] = $result['error_msg'];
			}
		}
		
		
		$this->render_view("main/main-login", $page_data);	
	}
	
	
	public function logout() {
		session_destroy();
		redirect(site_url());
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */