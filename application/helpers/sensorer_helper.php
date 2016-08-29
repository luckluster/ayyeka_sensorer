<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Functions needed speficially for the sensorer
 * @author Trevize
 */

 
 /**
  * Takes a timestamp and converts (simplifies) it to the grouping value, 
  * which is used in the 'dp_grouping_value' field in the data_processed table
  */
 function convert_time_to_data_grouping_value($sql_time, $grouping_option) {
	$ts = strtotime($sql_time);
	$grouping_value = null;
	switch ($grouping_option) {
		case GROUPING_TYPE__year:
			$grouping_value = date("Y", $ts);
			break;
		GROUPING_TYPE__month:
			$grouping_value = date("Y-m", $ts);
			break;
		case GROUPING_TYPE__day:
			$grouping_value = date("Y-m-d", $ts);
			break;
		case GROUPING_TYPE__hour:
			$grouping_value = date("Y-m-d H", $ts);
			break;
		//-unindent
	}
	
	return $grouping_value;
	
 }