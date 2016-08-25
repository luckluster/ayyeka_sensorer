<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends MY_Controller implements MY_Orca_CRUD_Interface {
	public function __construct() {
		parent::__construct();
		
		if (get_constant('LMC_ENVIRONMENT') == 'production') {
			throw new Exception("not to be used in production!");
		}
	}
	
	public function crypt($str) {
		$this->load->helper('password_helper');
		echo password__crypt($str);
	}
	
	public function test_form(){

	}
	
	public function create(){
		$this->load->helper('language_helper');
		$params = array(
				'fields' => array(
						array(
								'name' => 'username',
								'db_name' => 'usr_username',
								'header' => 'Username',
								'required' => true,
								'validation' => 'alpha_dash',
								'form_control' => 'text',
								'type' => 'string'
						),
						array(
								'name' => 'email',
								'db_name' => 'usr_email',
								'header' => 'Email',
								'required' => true,
								'validation' => 'valid_email|is_unique[user.usr_email]',
								'form_control' => 'text',
								'type' => 'string'
						),
				),
		
				'data_model'=>'test_model'
		);
		//$this->load->library('orca_grid/orca_form',$params);
		require_once FCPATH . 'application/libraries/orca_grid/orca_form.php';
		$form_obj = new Orca_form($params);
		$form_obj->run(true);
		$form_html = $form_obj->render();
		
		echo $form_html;		
	}
	public function read($item_id){
		$this->load->helper('language_helper');
		$params = array(
				'item_id'=>$item_id,
				'fields' => array(
						array(
								'name' => 'username',
								'db_name' => 'usr_username',
								'header' => 'Username',
								'required' => true,
								'validation' => 'alpha_dash',
								'form_control' => 'text',
								'type' => 'string'
						),
						array(
								'name' => 'email',
								'db_name' => 'usr_email',
								'header' => 'Email',
								'required' => true,
								'validation' => 'valid_email',
								'form_control' => 'text',
								'type' => 'string'
						),
				),
		
				'data_model'=>'test_model'
		);
		//$this->load->library('orca_grid/orca_form',$params);
		require_once FCPATH . 'application/libraries/orca_grid/orca_form.php';
		$form_obj = new Orca_form($params);
		$form_obj->run(true);
		$form_html = $form_obj->render();
		
		echo $form_html;
	}
	public function update($item_id){
		
	}
	public function delete($item_id){
		
	}
	public function list_view(){
		
	}
	
	
	
}
