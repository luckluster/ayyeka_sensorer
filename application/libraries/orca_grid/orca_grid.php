<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 * @author Trevize
 * Welcome to the ORCA Grid
 * This will represent our way to show data from databases and from other sources.
 * 
 */
class Orca_grid {
	protected $grid_name = null;
	protected $grid_vars = null;  // These are the parameters collected from the URL. See z_docs/orca_grid.txt for explanation
	protected $grid_params = null; // These are the parameters given to the constructor. See below.
	//protected $rpp = null;
	//protected $page = null;
	
	/**
	 * Our nice constructor. 
	 * Used for giving all the required parameters for the grid which will be shown later on by render_grid
	 * (render_grid is to be called from a DIV created by get_grid_container)
	 * The grid will be shows as an HTML table which will contain the given data, 
	 * along with sorting, pagination and even filtering.
	 * @param array $params
	 Supported values inside $params:
	 'id_field' => name of field inside 'fields' which is our ID column
	 'default_order_by' - name of field inside 'fields' which we order by, by default.
	 	If missing, the grid will display the data unsorted by default
	 'default_order_dir' - A or D
	 'disable_order' => if true, don't support changing the order (not implemented yet)
	 'fields' => array( field_identifier => field_data, field2_identifier => field2_details, ... ) - array of fields 
	 	- see below for the format  
	 'default_rpp' => int - records per page to display (may be changed by the user later!)
	 'rpp_presets' => array (10,25,50,100, 0) - array of items to show in the "view X records per page" dropdown
	 	while 0 means ALL. 
	 'filters_control_new_line' => if true, prints a BR before printing the filter controls in the chosen filters column
	 	(for aesthetic reasons which may vary)
	 	See 'filters_control_column' in the 'fields'
	 'hide_filters_row_by_default' => If true, then the filters row will be hidden by default (display:none),
	 	and we will show an empty TR with a button: 'show filters', and clicking on it will display the hidden filters row
	 'date_format_callback' => A callback function for formatting fields of 'date' or 'datetime' fields.
	 	For example, get_human_date() in STC.
	 	Passed to call_user_func()
	 	Function is expected to receive an SQL date or datetime, 
	 	and is expected to find if it's a date or a datetime and then return a string of
	 	date or datetime accordingly - formatted according to the system settings/preferences
	 'data_callback' => a callback function which you can pass to call_user_func() 
	 	Has to support the following params (which are given as an array)
	 		'extra_params' - optional value(s) given in 'data_callback_extra_params' - see below
	 		'container_datacallback_params' - params sent throgh the grid container - from the function Orca_grid::get_grid_container
	 			  For example, additional filter values sent from the form or another page.
	 			  ATTENTION: Parameters may be tampered by the user so they can't be trusted 
	 			  (use extra_params for parameters requiring trust, such as current user id)  
	 		'count_records'  - if true, should return record count instead of records
	 		'limit' - how many records to return? 
	 		'offset' - offset of records to return
	 		'order_by' - a field name. Will be an identifier from 'fields' above
	 		'order_dir' - 'A' or 'D'
	 		'filters' - array of filters which were chosen by the user,
	 			// so the data callback should filter by them. 
	 			// See also: get_sql_filter_text
	 			// each item, and each item inside field_name may be optional
	 			array (
	 				[field_identifier] => array (
	 					'type' - type of filter. May be: 'contains', 'equals' or 'minmax'
	 					'value' - value to comapre to, in case of type 'contains' or 'equals'
	 					'min'- min value in case of type 'minmax'
	 					'max - like above but for max value
	 				) 
	 			)
	 	Has to return the following stucture:
	 		'records' - array of records which was returned according to the parameters, OR:
	 		'record_count' - number of records matching this criteria - if 'count_records' is given as parameter
	 		
	 'data_callback_extra_params' => Any extra data you would like to pass to the data callback (such as current user ID) 
	 
	 'data' => array of rows which will be used as data to show, instead of calling function of 'data_callback'
	 NOTE: currently sorting and filtering data is not supported yet!
	 
	 Format of 'fields': 
	 array (
	 	'field_identifier' => array (    // field_identifier is how this field is called in the DB / data array
	 		'title' => Title to show on grid. If absent, we will use the field identifier instead
	 		'type' => type of data. 
	 			Currently supported types: 
	 			'string'
	 			'date' / 'datetime' - date/datetime string in SQL format
	 				'date' will not display the time
	 				See also: 'date_format_callback'
	 			'custom_text' - a user-specified-text to appear -
	 				Can be used for many purposes, 
	 				for example, for For displaying images inside the grid,
	 				or for concatenating two or more text fields together 
	 				Text itself is specified by the 'text' property, see below
	 			'url' - Means that this will column will be an URL, constructed from 'url' and 'text',
	 				see 'url', 'url_caption' and 'new_window' below
	 			'callback' => Uses a callback function to format the text, see 'field_callback_function' below
	 			'key_value' - Replace the value of this field with a key-value array specified later,
	 				For example, to use with an int status column.
	 				see 'values' below
	 			'flags' - A place to put various flags. 
	 				Also if using filters, the "search" and "reset filters" will be in this column in the filters row
	 		'custom_text' => For fields of type 'custom_text', the text to put in the table.
	 			Text may contain "replaceables" - see below
	 			Example: "<img src='[[image_url]]' />"
	 			Or "<a href='/profile/edit/[[id]]'>edit</a> <a href='/profile/view/[[id]]'>view</a>"
	 		'url' => For fields of type 'url', what URL to use. May contain "replaceables" - see below
	 			For example:  site_url('/auctions')."/tenders/[[tender_id]]"
	 		'url_caption' => For fields of type 'url', the text of the clickable caption.
	 			May contain replaceables inside double brackets
	 		'url_new_window' => For fields of type URL, says that the URL will open a new window
	 		'values' => For fields of type 'key_value', an array of keys and values which will replace the field value
	 		'field_callback_function' => A function parameter ( passed to call_user_func() )
	 			whose output will be printed in the grid. 
	 			(NOTE: printed "raw" as is. It's YOUR responsibility to escape it!)
	 			Parameters sent to it: 
	 				$row - the current row
	 		'raw' => For fields of type 'string' and 'custom_text:
	 			 Don't use htmlspecialchars - print this value AS IS on the grid 
	 			(don't use it for user-supplied input!)
	 		'order_field' => string/boolean - an identifier
	 			means that you can order by this column (so the column header is clickable) 
	 			This identifier is passed to the data_callback function as an 'order_by' param.
	 			Using boolean TRUE will be the same as setting:  order_field = field_identifier
	 		'td_id' => string - the HTML ID property for the TD element of this field. May contain replaceables(see below). Will be escaped
	 		'html_class' => string - HTML class property to add for ALL TDs/THs related to this column
	 			This includes the TH in the header, the TD in the filters row (if any), and the repeating TD of the column itself.
	 			Will be escaped
	 			
	 		'td_class' => string - the HTML class property for the TD element of this field. May contain replaceables(see below). Will be escaped
	 		'th_style' => string - the HTML style for the top TH (table header) for this element (the title) - useful for specifying width
	 		'filter' => array (
	 			// Defining this field will cause that a filter row will appear
	 			// as the first row, and will contain filters that the end user may specify.
	 			// These filters will be passed to the callback_function (see callback_function please)
	 			'type' =>
		 			Defines a user-modifiable filter for this field.
		 			The type may be:
		 			equals (for string)
		 			contains (for string)
		 			dropdown (see 'values' below)
		 			number_minmax (for numbers, duh) - not yet supported 
		 			date_minmax - not yet implemented
		 		'field_identifier' => Optional - Send a different field identifier to the callback function 
		 			when the user filters by this field. For example: use "tenders_id.id" for the "id" column to prevent ambiguity 
		 		'values' => In case of 'dropdown'-type filter, an array of key-value of things to show in the dropdown 
		 			- if the key is null or empty string, then it is considered unselected and will not be filtered
		 			- Also supports OPTGROUP - the 'value' can be another key-value array itself (so the key will be the label text) 
		 		'default_value' => Default value for filter, in case no value is given by user
		 			- currently doesn't work for number_minmax and date_minmax
		 	)
		 	'filters_control_column' =>  if true, then this column will have the "Apply filters" and "Reset filters" buttons, 
		 		in the filters row. 
		 		If there's a filter row but this property isn't set, then the "Apply" and "Reset" buttons will be shown
		 		in the first column whose type is 'flags', and if not found, then in the first column which has a filter
	 	)
	 	   
	 )
	 
 	Explanation for the term "replaceables"
 	Replaceables are (programmer-specified) strings inside dobule brakcets, which will be replaced with the field
 	from the current row being displayed.
 	Example: specifying "The user email for current row is [[usr_email]]" as a custom_text parameter. 
	 
	 	
	 
	 */
	public function __construct(array $params) {
		$CI =& get_instance();
		require_once('orca_grid_field.php');
		
		my_load_lang('orca_grid');
		
		$this->grid_params = $params;
		// @TODO: support defaults configuration instead of those hardcoded defaults

		// Need to make a clear difference between parameters and state variables.
		
		
		//require_once('orca_grid_field_string.php');
	}
	
		
	/**
	 * Returns the HTML Displays a container with our grid
	 * Note: Copies any relevant parameters from the GET line into the grid vars as parameters 
	 * 
	 * @param string $grid_name - identifier for grid. can be blank, but has to be unique for this page
	 * @param string $grid_content_function_url - URL to call for rendering the grid itself
	 * @param array $more_params - currently supports:
	 * 	'container_datacallback_params' - any params you would like to send to the data callback,
	 *  such as filter values.
	 *  NOTE that the user may tamper with those values as they are passed through GET.
	 *  Will be json_encode'd here and json_decode'd back before passed to the data callback
	 *  
	 * @return string - what you have to display on the view in order to have a nice grid container
	 */
	public static function get_grid_container($grid_name, $grid_content_function_url, $more_params = array()) {
		$CI =& get_instance();
		
		// Do we have GET params relevant to this grid?
		$grid_vars = json_decode($CI->input->get($grid_name."_vars"), true);
		if (!is_array($grid_vars)) {
			$grid_vars = array();
		}
		unset($grid_vars['grid_name']);  // Prevent any illegal var name, as If someone tries to mess with us??
		if (isset($more_params['container_datacallback_params'])) {
			$grid_vars['container_datacallback_params'] = json_encode($more_params['container_datacallback_params']);
		}
		
		$data = array (
			'grid_name' => $grid_name,
			'grid_vars' => $grid_vars,
			'grid_content_function_url' => $grid_content_function_url,
			'more_params' => $more_params
		);
		$params = array (
			'no_headerfooter' => true,
			'no_layout_subfolder' => true,
			'return_html' => true
		);
		$ret = $CI->render_view('orca/orca_grid-container', $data, $params);
		//$ret = "meow";
		
		return $ret;   
	}
	
	
	/**
	 * Called from the grid rendering function, 
	 * which is meant to be called from the grid content function, 
	 * whose url is given to get_grid_container() above
	 *  
	 * @param array $more_params - may contain:
	 * 	'alternative_get_array' - array  - use these GET parameters instead of what we got from $_GET
	 */
	public function render_grid(array $more_params = array()) {
		$CI =& get_instance();
		
		$this->grid_vars = $_GET;
		if (isset($more_params['alternative_get_array'])) {
			$this->grid_vars = $more_params['alternative_get_array'];
		}
		
		$this->grid_name = self::safe_param_value(arr_get_value($this->grid_vars, 'grid_name'));
		
		//$this->set_grid_var("ariel", "kobim");
		
		$rpp = $this->get_grid_var("rpp");
		// Is the RPP variable set, and valid?
		//my_print_r($rpp, true);
		if (! (is_array(arr_get_value($this->grid_params, 'rpp_presets')) && in_array($rpp, $this->grid_params['rpp_presets'])) ) {
			// Invalid RPP!  Use the default
			$rpp = arr_get_value($this->grid_params, 'default_rpp');
			$this->set_grid_var('rpp', $rpp);
		} 
		
		// What page are we?
		if (!strlen($this->get_grid_var('page'))) {
			$this->set_grid_var('page', 0);
		}
		
		// Do we have the record count?
		if (!strlen($this->get_grid_var('record_count'))) {
			$this->set_grid_var('record_count', (int)$this->call_data_function(true));
		}

		// How many pages (of records) do have, corresponding to the current value of rpp?
		$pages_count = (int)$this->get_grid_var('rpp') ?  ceil($this->get_grid_var('record_count') / $this->get_grid_var('rpp')) : 1;
		
		// Do we have to sort by? if so, check if it's a valid field
		$order_field = $this->get_grid_var('order_field');
		$is_valid_order_field = false;
		if ($order_field) {
			foreach ($this->grid_params['fields'] as $field_name => $field_options) {
				if ($other_order_field = arr_get_value($field_options, 'order_field')) {
					if ($other_order_field == $order_field ||  ($other_order_field === true && $order_field == $field_name)) {
						$is_valid_order_field = true;
						break;
					} 
				}
			}
			unset ($other_field_name); unset ($field_name); unset ($field_options);
		}
		
		if (!$is_valid_order_field) {
			$order_field = ""; // Not accepted!
			// Take from default if applicable?
			if (arr_get_value($this->grid_params, 'default_order_by')) {
				$order_field = $this->grid_params['default_order_by'];
				$this->set_grid_var('order_dir', arr_get_value($this->grid_params, 'default_order_dir'));
			}
		}
		$this->set_grid_var('order_field', $order_field);
		
		// Show or hide the filters row?
		if (!strlen($this->get_grid_var('hide_filters'))) {
			if (arr_get_value($this->grid_params, 'hide_filters_row_by_default')) {
				$this->set_grid_var('hide_filters', 1); 
			}  else {
				$this->set_grid_var('hide_filters', 0);
			}
		}
	
		
		// Find the data that we need to load
		$records_data = $this->get_grid_data();
		
		// Define the RPP data to be shown on the dropdown
		$rpp_array = array();
		if (is_array(arr_get_value($this->grid_params, 'rpp_presets'))) {
			foreach ($this->grid_params['rpp_presets'] as $rpp_preset) {
				if (strtoupper($rpp_preset) == "A") {
					$rpp_text = self::l("orca_grid__rpp_all");
					$rpp_val = "A";
				} else {
					$rpp_text = (int)$rpp_preset;
					$rpp_val = $rpp_text;
				}
				$rpp_array[$rpp_val] = $rpp_text;
			}			
		}
		
		// Process filters
		$enable_filters_row = false;
		$column_for_filters_control_buttons = null;
		$first_flags_column = $first_filter_column = null;
		// Check if we have filtes at all,  
		// and find where to show the filters control buttons
		// (either in a 'flags'-type column, or in the first column with a filter)
		foreach ($this->grid_params['fields'] as $field_name => $field_options) {
			// Search for candidates for the filters control column
			if (!$column_for_filters_control_buttons && arr_get_value($field_options, 'filters_control_column')) {
				// Explicitly set 
				$column_for_filters_control_buttons = $field_name;
			} 
			if (!$first_flags_column && arr_get_value($field_options, 'type') == 'flags') {
				$first_flags_column = $field_name;
			}
			if (!$first_filter_column && arr_get_value($field_options, 'filter')) {
				$first_filter_column = $field_name;
			}
			
			if (arr_get_value($field_options, 'filter')) {
				$enable_filters_row = true;
			}
		}
		
		if ($enable_filters_row && !$column_for_filters_control_buttons) {
			// It filters control buttons was not explicitly set by the user, so choose somewhere else to place it
			if ($first_flags_column) {
				$column_for_filters_control_buttons = $first_flags_column;
			} elseif ($first_filter_column) {
				$column_for_filters_control_buttons = $first_filter_column;
			}
		}
				
		 
		$page_data = array (
			'grid_params' => $this->grid_params,
			'grid_vars' => $this->grid_vars,
			'record_data' => $records_data,
			'rpp_array' => $rpp_array,
			'rpp' => $this->get_grid_var('rpp'),
			'page' => $this->get_grid_var('page'),
			'record_count' => $this->get_grid_var('record_count'),
			'page_count' => $pages_count,
			'order_field' => $this->get_grid_var('order_field'),
			'order_dir' => $this->get_grid_var('order_dir'),
			'enable_filters_row' => $enable_filters_row,
			'hide_filters' => $this->get_grid_var('hide_filters'),
			'column_for_filters_control_buttons' => $column_for_filters_control_buttons
		);
		$page_params = array (
			'no_headerfooter' => true,
			'no_layout_subfolder' => true,
			'return_html' => true
		);
		$html = $CI->render_view('orca/orca_grid-content', $page_data, $page_params);
		
		echo json_encode(array('ok' => 1, 'data' => $html, 'grid_vars' => $this->grid_vars));
	}
	
	/**
	 * Gets the data we need to display, according to the current pagination state
	 * Either takes it from the 'data' array (if present) or calls the data_callback 
	 */
	protected function get_grid_data() {
		
		if ($data = arr_get_value($this->grid_params, 'data')) {
			// Take the data from the array
			// @TODO: sort and filter the data array according to the params!  
			// (currently sorting and filtering is only supported for DB data callback)  
			
			$limit = (int)$this->get_grid_var('rpp');
			$offset = (int)$limit*$this->get_grid_var('page');
			
			$ret = array_slice($data, $offset, $limit, true);
		} else {
			// Get the data from our nice callback function
			$ret = $this->call_data_function(false);
		}
		
		return $ret;
	}
	
	/**
	 * Calls the function that returns the data
	 * @param boolean $get_record_count - return the record count instead of all the records
	 * @return array / int (in case of record count)
	 */
	protected function call_data_function($get_record_count = false) {
		$params = array();
		
		if (!$get_record_count) {
			$params['limit'] = (int)$this->get_grid_var('rpp');
			$params['offset'] = (int)$params['limit']*$this->get_grid_var('page');
			$params['order_field'] = $this->get_grid_var('order_field');
			$params['order_dir'] = $this->get_grid_var('order_dir');
		} else {
			$params['count_records'] = true;
		}
		
		// Create information for explaining how to filter the data
		$filters_data = json_decode($this->get_grid_var('filters'), true);
		// Don't collect the filters blindly from GET 
		//- instead take only the filters which are allowd in the grid params
		foreach ($this->grid_params['fields'] as $field_name => $field_details) {
			$filter_data = arr_get_value($filters_data, $field_name, array());
			$filter_param = arr_get_value($field_details, 'filter', array());
			
			if ($filter_type = arr_get_value($filter_param, 'type')) {
				// Choose which type of filter it is - minmax or value
				$this_filter = array();
				if ($filter_type == "number_minmax" || $filter_type == "date_minmax") {
					$min = arr_get_value($filter_data, 'min');
					
					if (strlen($min)) {
						if ($filter_type == "date_minmax")  { $min .= " 00:00:00"; }
						$this_filter['min'] = $min;
					}					
					$max =  arr_get_value($filter_data, 'max');
					if (strlen($max)) {
						// Fix date range, in case it's datetime (otherwise results from the date $max will not be shown)
						if ($filter_type == "date_minmax")  { $max .= " 23:59:59"; }
						$this_filter['max'] = $max;
					}

					if (strlen($min) || $strlen($max)) {
						$this_filter['type'] = $filter_type;
					}
				}  else {
					$val = arr_get_value($filter_data, 'value');
					// Check if we should use the default value for filter, if no filter specified
					if (!isset($filters_data[$field_name])) {
						$val = arr_get_value($filter_param, 'default_value');
					}
					
					// Be paranoid: If it's a dropdown, only filter by this value if it's defined in one of the dropdown values
					if ( $filter_type != 'dropdown'
						|| $filter_type == 'dropdown' && array_key_exists( $val, arr_get_value($filter_param, 'values', array()) ) ) {
						 
						if (strlen($val)) {
							$this_filter['value'] = $val;
							$this_filter['type'] = $filter_type;
						}
					}

				}
				
				// Did we get data for a filter? Put it in the function params then
				if ($this_filter) {
					// Note the default - we use by default the normal field name, unless an alternative is specified
					$field_identifier = arr_get_value($filter_param, 'field_identifier', $field_name);  
					$params['filters'][$field_identifier] = $this_filter;
				}
				
			} // if ($filter_type = arr_get_value($filter_param, 'type'))
		} // foreach grid fields
		
		unset ($field_details); unset ($filter_param); unset ($field_name); unset ($filter_type); unset($field_identifier);
		
		$params['extra_params'] = arr_get_value($this->grid_params, 'data_callback_extra_params');
		$params['container_datacallback_params'] = json_decode($this->get_grid_var('container_datacallback_params'), true);
		
		$result = call_user_func($this->grid_params['data_callback'], $params);
		
		if ($get_record_count) {
			$result = $result['record_count'];
		} 
		
		return $result;
	}
	
	
	
	/**
	 * Returns a grid var value - 
	 * tries to get it first from the GET params ($this->grid_vars)
	 * @param string $var_name
	 * @return string  or something else which should be a simple var type, really
	 */
	protected function get_grid_var($var_name) {
		$val = (string)arr_get_value($this->grid_vars, $var_name);
		
		return $val;
	}
	
	protected function set_grid_var($var_name, $val) {
		$this->grid_vars[$var_name] = $val;
	}
	
	/**
	 * Removes almost all nonalphanumeric chars from the parameter- used for certain param values
	 * @param string $str
	 * @return string
	 */
	protected static function safe_param_value($str) {
		return preg_replace("/[^A-Za-z0-9_-]/", "", $str);
	}
	

	/**
	 * "Abstraction" - just calls the l() function from general_helper
	 * @param string $key
	 * @param array $params
	 * @return string
	 */
	public static function l($key, $params = array()) {
		return l($key, $params);
	}
	
	/**
	 * "Abstraction" - just calls the lang_r2() function from general_helper
	 * @param string $key
	 * @param array $params
	 * @return string
	 */
	public static function lang_r2($key, $search, $replace = null, $params = array()) {
		return lang_r2($key, $search, $replace, $params);
	}
	
	
	/**
	 * Takes the filters (sent by call_data_function()) 
	 * and converts them into SQL conditions (for the WHERE clause)
	 * 
	 * Meant to be called from the model function as a shortcut, instead of the model function itself having
	 * to construct all those filters again and again
	 * 
	 * @param array $filters - values sent to callback using call_data_function() 
	 * @param boolean $prefix_with_AND - if any filters are provided, prefix them with AND
	 * @return string
	 */
	public static function get_sql_filter_text($filters, $prefix_with_AND) {
		
		$sql = "";
		$filters_arr = array();
		foreach ($filters as $field_name => $filter) {
			$type = arr_get_value($filter, 'type');
			if ($type == 'number_minmax' || $type == 'date_minmax') {
				$min = arr_get_value($filter, 'min');
				$max = arr_get_value($filter, 'max');
				if (strlen($min) && strlen($max)) {
					$filters_arr []= " $field_name >= '".addslashes($min)."' AND $field_name <= '".addslashes($max)."' \n ";
				} elseif (strlen($min)) {
					$filters_arr []= " $field_name >= '".addslashes($min)."' \n";
				} elseif (strlen($max)) {
					$filters_arr []= " $field_name <= '".addslashes($max)."' \n";
				} 
			} else {
				$value = arr_get_value($filter, 'value');
				
				if (strlen($value)) {
					if ($type == "contains") {
						$value = addslashes($value);
						$value = str_replace('%', "\%", $value); // escape it
						$filters_arr []= " $field_name LIKE '%{$value}%' \n";
					} else {
						$filters_arr []= " $field_name = '".addslashes($value)."' \n";
					}
				}
			}
		}
		
		$sql = implode(' AND  ', $filters_arr);
		if ($prefix_with_AND && $sql) {
			$sql = " AND ".$sql;
		}

		return $sql;
	}
	
	
}