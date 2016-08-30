<?
// Model for machines
class Data_processed_model extends MY_Model {
	
	function __construct() {
		$this->init_model_values(TBL_PREFIX.'data_processed', '');   // notice no primary key
		parent::__construct();
	}
	
	/**
	 * Adds a value reported by a machine,
	 * while updating all the current averages.
	 * @param int $machine_id
	 * @param string  $sql_time
	 * @param float $value
	 */
	public function add_machine_value($machine_id, $sql_time, $value) {
		// to maintain order!
		$this->db->trans_start();
		
		$machine_id = (int)$machine_id;
		$value = $this->db->escape($value);
		// How to update an existing value:
		//for 2 values:   (old value + new_value) / 2
		//for 3 values:   (old value*2  + new_value) /3
		//for 4 values:   (old value*3  + new_value) /4 
		// etc.
		
		// Loop all over the  grouping options (year, month etc) and save the value for each of those ways
		foreach (my_config_item('GROUPING_options') as $grouping_option => $grouping_option_name) {
			// get some information about the current value, if any
			// yes it's a query in a loop which is not good, but used for the sake of simplicty
			
			$grouping_value = convert_time_to_data_grouping_value($sql_time, $grouping_option);
			
			$sql = "
			SELECT * FROM ".$this->get_table_name()."
			WHERE dp_machine_id=$machine_id AND dp_grouping_type=$grouping_option AND  dp_grouping_value='$grouping_value'
			";
			$row = $this->generic_query($sql, false);
			
			// Is there already a value for this grouping option?
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
	
	/**
	 * Data callback for Orca_grid
	 * @param array $params - standard options sent by orca_grid - see documentation in orca_grid class (section 'data_callback')
		expects to have the following params:
			'container_datacallback_params' => array('mcn_id', 'theperiod', 'sql_time_from', 'sql_time_until')
			'extra_params' => array('usr_id')
	 * @return array - of rows, or just a single item: 'record_count'
	 */
	public function get_procssed_data_for_grid($params) {
		// initial WHERE clause
		$where_text = "WHERE mcn_user_id=".$params['extra_params']['usr_id'];
		
		
		if (arr_get_value($params, 'filters')) {
			//my_print_r($params['filters']);
			$where_text .= Orca_grid::get_sql_filter_text($params['filters'], true);
		}
		
		// Handle filters sent from the UI
		$more_params = arr_get_value($params, 'container_datacallback_params', array());  // sent from the controller itself through GET
		if (arr_get_value($more_params, 'mcn_id')) {
			$where_text .= " AND dp_machine_id=".(int)$more_params['mcn_id'];
		}
		if (arr_get_value($more_params, 'theperiod')) {
			$where_text .= " AND dp_grouping_type=".(int)$more_params['theperiod'];
			
			if (arr_get_value($more_params, 'sql_time_from')) {
				$val = convert_time_to_data_grouping_value( $more_params['sql_time_from'], $more_params['theperiod']);

				$where_text .= " AND dp_grouping_value >= ".$this->db->escape($val);
			}
			if (arr_get_value($more_params, 'sql_time_until')) {
				
				if ($more_params['theperiod'] == GROUPING_TYPE__hour) {
					$val = $more_params['sql_time_until']." 59";  // fix time problem. yup
				} else {
					$val = convert_time_to_data_grouping_value( $more_params['sql_time_until'], $more_params['theperiod'] );		  // normal behavior..
				}
					
				$where_text .= " AND dp_grouping_value <= ".$this->db->escape($val);
			}
			
		}
		//print_r($where_text);
		
		if (!arr_get_value($params, 'count_records')) {
			$sql  = "
			SELECT dp.*, mcn.mcn_title, mcn.mcn_id
			FROM ".$this->get_table_name()." AS dp
			JOIN ".TBL_PREFIX."machines AS mcn ON mcn_id=dp_machine_id
			".$where_text;
			
			if ($order_field = arr_get_value($params, 'order_field')) {
				$sql .= " ORDER BY $order_field ".(arr_get_value($params, 'order_dir') == "A" ? "ASC" : "DESC"); 
			}
			
			$sql .= get_limit_string(arr_get_value($params, 'limit'), arr_get_value($params, 'offset'));
			
			if (arr_get_value($params, 'filters')) {
				//my_print_r($sql);
			}
			//my_print_r($sql);
			return $this->generic_query($sql);
		} else {
			$sql = "
			SELECT COUNT(*) as record_count 
			FROM ".$this->get_table_name()."
			JOIN ".TBL_PREFIX."machines AS mcn ON mcn_id=dp_machine_id
			".$where_text;
			return $this->generic_query($sql, false);
		}
		
		
	}
	

}