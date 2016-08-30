<?
// Model for machines
class Data_raw_model extends MY_Model {
	
	function __construct() {
		$this->init_model_values(TBL_PREFIX.'data_raw', '');   // notice no primary key
		parent::__construct();
	}

	
	public function add_machine_value($machine_id, $sql_time, $value) {
		
		$machine_id = (int)$machine_id;
		$sql_time = $this->db->escape($sql_time);
		$value = $this->db->escape($value);
		
		
		// how to update the new value:
		//for 2 values:   (old value)*1/2 + new_value/2
		//for 3 values:   (old value)*2/3  + new_value/3
		//for 4 values:   (old value)*3/4  + new_value/4 
		// etc
			
		$sql = "
		INSERT INTO ".$this->get_table_name()."
		SET rd_machine_id=$machine_id, rd_timestamp=$sql_time, rd_value=$value
		";
		
		return $this->db->query($sql);
	}
	
	
	
}