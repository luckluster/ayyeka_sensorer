<?
// Model for machines
class Machines_model extends MY_Model {
	
	function __construct() {
		$this->init_model_values(TBL_PREFIX.'machines', 'mcn_id'); 
		parent::__construct();
	}

	
	public function get_user_machines($user_id) {
		return $this->generic_query(
			"SELECT * FROM ".$this->get_table_name()." WHERE mcn_user_id=".(int)$user_id,
			true,
			'mcn_id',
			'mcn_title'
		);
	}
	
	
	
	
}