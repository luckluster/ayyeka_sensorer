<?php

class Test_model extends MY_Model {
	
	function __construct() {
		$this->init_model_values(TBL_PREFIX.'user', 'usr_id'); 
		parent::__construct();
		
	}
	
}