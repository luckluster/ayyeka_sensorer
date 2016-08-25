<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Dashboard extends MY_Admin_Controller {

	public function __construct()
	{
		parent::__construct();

		//pr($this->the_user);
		
	}

	public function index()
	{
		
	}

	

	
	/*******************************/
	/******** PRIVATE METHODS ******/
	/*******************************/ 
	

	
	public function logout() {
		session_destroy();
		
		redirect(site_url('/admin'));
	}
	

}