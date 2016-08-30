<?
// Model for machines
class Data_processed_model extends MY_Model {
	
	function __construct() {
		$this->init_model_values(TBL_PREFIX.'data_processed', '');   // notice no primary key
		parent::__construct();
	}
	
	public function add_machine_value($machine_id, $sql_time, $value) {
		// to maintain order!
		$this->db->trans_start();
		
		$machine_id = (int)$machine_id;
		$value = $this->db->escape($value);
		// How to update the new value:
		//for 2 values:   (old value + new_value) / 2
		//for 3 values:   (old value*2  + new_value) /3
		//for 4 values:   (old value*3  + new_value) /4 
		// etc.
		
		foreach (my_config_item('GROUPING_options') as $grouping_option => $grouping_option_name) {
			// get some information about the current value, if any
			// yes it's a query in a loop which is not good, but used for the sake of simplicty
			
			$grouping_value = convert_time_to_data_grouping_value($sql_time, $grouping_option);
			
			$sql = "
			SELECT * FROM ".$this->get_table_name()."
			WHERE dp_machine_id=$machine_id AND dp_grouping_type=$grouping_option AND  dp_grouping_value='$grouping_value'
			";
			$row = $this->generic_query($sql, false);
			if ($row) {
				$new_dp_values_count = $row['dp_values_count']+1;
				$new_dp_value = ($row['dp_value']*$row['dp_values_count'] + $value)/$new_dp_values_count;
			} else {
				$new_dp_values_count = 1;
				$new_dp_value = $value;
			}
			
			
			
			if ($row) {
				$sql = "
				UPDATE ".$this->get_table_name()."
				SET dp_value=$new_dp_value, dp_values_count=$new_dp_values_count
				WHERE dp_machine_id=$machine_id AND dp_grouping_type=$grouping_option AND dp_grouping_value='$grouping_value'
				";
			} else {
				$sql = "
				INSERT INTO ".$this->get_table_name()."
				SET dp_machine_id=$machine_id, dp_grouping_type=$grouping_option, dp_grouping_value='$grouping_value', dp_value=$new_dp_value, dp_values_count=$new_dp_values_count
				";
			}
			
			
			$this->db->query($sql);
		}
		
		// yes we're done!
		$this->db->trans_complete();
	}

}