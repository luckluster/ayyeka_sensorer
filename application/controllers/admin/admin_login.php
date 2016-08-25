<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_login extends MY_Controller {
	
	public function __construct() {
		parent::__construct();
		my_load_lang('login');
	}
	
	public function index() {
		// Check if already logged in!
		if (is_admin()) {
			redirect(site_url('/admin/dashboard'));
		}
		
		$page_data = array (
			'message' => ''
		);
		
		// Do login..
		if ($this->input->post('go')) {
			// Find that user..?
			$user_info = $this->users_model->get_user_info_for_session(null, array('usr_username' => $this->input->post('username')));
				
			$result = $this->auth->try_to_login_user($user_info, 
				array('password' => $this->input->post('password', true), 'redirect_url' => site_url('/admin/dashboard'))
			);
			
			if ($result['ok']) {
				//echo "redirecting to {$result['redirect_url']}";
				//return; 
				redirect($result['redirect_url']);
			} else {
				$page_data['message'] = $result['error_msg'];
			}
		}
		
		
		$this->render_view("admin/admin-login", $page_data);
		
	}
	
}