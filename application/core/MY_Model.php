<?php
/**
 * MY_model
 * 
 * NOTE about emulated_index_name - please see /z_docs/notes.txt for explanation about it.
 * Read it - it's mandatory!
 * 
 * @author Trevize
 */


class MY_Model extends CI_Model {
	// The following vars are initialized using init_model_values which should be called in the constructor of the inheriting model
	protected $model_table_name = null; 
	protected $model_index_name = null;
	protected $model_emulated_index_name = null;
	protected $force_no_cache = false;  
	private $seo_fields = array('seo_title','seo_description','seo_keywords'); 
	
	function __construct () {
		if (is_null($this->model_table_name)) {
			//throw new Exception("mdTableName not defined in model ".get_called_class());
		}
		if (is_null($this->model_index_name)) {
			//throw new Exception("mdIndexName not defined in model!");
		}
		parent::__construct();		
		//die("no!");
	}
	
	/**
	 * Must be called in the __construct of the inheriting class!
	 * "index_name" can remain empty if the table has no index or has a compound index
	 * @param string $table_name
	 * @param string $index_name
	 * @param string $emulated_index_name - see comment on top of file!
	 */
	function init_model_values($table_name, $index_name, $emulated_index_name = null) {
		$this->model_table_name = $table_name;
		$this->model_index_name = $index_name;
		$this->model_emulated_index_name = $emulated_index_name;
	}
	
	public function force_no_cache() {
		$this->force_no_cache = true;
	}
	public function get_table_name() {
		return $this->model_table_name;
	}
	public function get_index_name() {
		return $this->model_index_name;
	}
	public function get_emulated_index_name() {
		return $this->model_emulated_index_name;
	}
	
	/**
	 * Gets the record whose ID is given in the parameters.
	 * Supports emulated index name (see comment on top of file)
	 * @param various $id
	 * @param array $columns - if specified, return only these columns from the DB
	 * @return array (DB row)
	 */
	public function get_by_primary($id, $columns = array()) {
		$sql = "SELECT ";
		
		
		$index_name = $this->get_index_name();
		$emulated_index_name = $this->get_emulated_index_name();
		
		
		
		if ($columns) {
		// 	Check if we have the emulated index name in the $columns - if so it must be replaced to the real index name
		
			foreach ($columns as $k => $col) {
				if ($col == $emulated_index_name) {
					unset($columns[$k]);
					$columns []= $index_name;
				}
			}
			
			$sql .= implode(",", $columns);
		} else {
			$sql .= " *";
		}
		
		
		$sql .= " FROM ".$this->get_table_name()." WHERE ".$this->get_index_name()."=".$id;
		
		$query = $this->db->query($sql);
		
		$ret = $query->row_array();
		$query->free_result();
		
		if ($emulated_index_name && isset($ret[$index_name])) {
			$ret[$emulated_index_name] = $ret[$index_name];
		}
		return $ret;
	}
	
	/**
	 * Adds a new record - and returns the newly created ID or null if some failure.
	 * Also supports updating instead of adding, in case of duplicate  (upsert)
	 * @param array $data - what to add
	 * @param array $on_duplicate  - array of data to put in case of a duplicate
	 * @return insert_id / NULL
	 */
	public function add_new_record(array $data, array $on_duplicate = array(), $return_id_on_insert=true) {
		$sql = $this->db->insert_string($this->get_table_name(), $data);
		if (count($on_duplicate)) {
			$sql .= " ON DUPLICATE KEY UPDATE ";
			$strings = array();
			foreach ($on_duplicate as $field => $value) {
				$strings []= "`$field`=".$this->db->escape($value);
			}
			$sql .= implode(", ", $strings);
		}
		//echo $sql;
		$result = $this->db->query($sql);
		if ($result) {
			return $return_id_on_insert ? $this->db->insert_id() : true;
		}
		return null; 
	}
   
	/**
	 * Updates a record of a specific ID!
	 * @param int $id
	 * @param array $data
	 * @throws Exception
	 */
	public function update_record($id, array $data) {
		$index_name =  $this->get_index_name();
		if (!$index_name) {
			throw new Exception("Please define an index for the model ".get_class($this));
		} 
		$where = "`$index_name`=".$this->db->escape($id);
		
		$sql = $this->db->update_string($this->get_table_name(), $data, $where); 		
		$result = $this->db->query($sql);
		return $result;
	} 
	
	/**
	 * Just passes $conditions to db::where  and return a row or an array of rows
	 * @param array $p - see below:
	 * @return array (record / array of records  - depending on parameter) 
	 */
	public function get_by_conditions($p) {
		// Things to pass to $p:
		// array/string $conditions - passed to db->where. 
		//	examples:  'conditions' => array ("itm_title = 'meow'",  'usr_cellphone' => $cellphone)
		//  NOTE: the space after the field name is important in the "tim_title" example. Otherwise it won't work.
		//  note: you can't use numeric DB field names with this function (in case you're nuts enough to have such a thing!)
		// boolean $multiple_records - TRUE by default
		// string $key_field_name - for multiple records - return them indexed by this.
		// 		the default is to index by the table index, if any. use NULL to force the result rows to be unindexed.
		//		relevant only if $multiple_records  
		// string $value_field_name - for multiple records, instead of the entire record return only this field
		// string $order_by - 
		// int $limit - optional
		// int $offset  - optional
			
		
		$conditions = arr_get_value($p, 'conditions', array());
		$limit = arr_get_value($p, 'limit');
		$offset = arr_get_value($p, 'offset');
		$multiple_records = arr_get_value($p, 'multiple_records', true);
		$key_field_name = arr_get_value($p, 'key_field_name', '');
		$value_field_name = arr_get_value($p, 'value_field_name');
		$order_by = arr_get_value($p, 'order_by');

		// CACHE: create cache key and check if we didn't cache the result alreay
		$cache_key = "getbyconditions_".md5(serialize($p));
		$cache_timeout = my_config_item('CACHE_sql_timeout');
		if ($this->force_no_cache) {
			$cache_timeout = 0;  // no cache!
		} 
		if ($cache_timeout) {
			// Check if there's a value already in the chache
			$result = $this->cache->get($cache_key);
			if ($result) {
				return $result;
			}
		}
		
		if (!is_null($limit)) {
			if (!is_null($offset)) {
				$this->db->limit($limit, $offset);
			} else {
				$this->db->limit($limit);
			}
		}
		
		if ($order_by) {
			$this->db->order_by($order_by);
		}
		
		if ($this->get_emulated_index_name()) {
			$this->db->select("*, ".$this->get_index_name()." AS ".$this->get_emulated_index_name());
		}
		
		foreach ($conditions as $k => $condition) {

			if (is_numeric($k)) {
				// Consider it a full sentence
				$this->db->where($condition);
			} else {
				// Consider it a field => value
				$this->db->where($k, $condition);
			}
		}
	
		$query = $this->db->get($this->get_table_name());
		//$query = $this->db->query();
		if ($multiple_records) {
			//return $query->result_array();
			// I am sorry, this is copied and pasted from generic_query
			$rows =  $query->result_array();
			$ret = array();
			
			// Try to automatically index by key field, if possible
			if (!is_null($key_field_name) && $key_field_name == "") {
				
				if ($this->get_index_name()) {
					$key_field_name = $this->get_index_name();
					
				} else {
					$key_field_name = null;  // not going to work!
				}
			}
			
		
			if (!is_null($key_field_name)) {
				foreach ($rows as $row) {
					if ($value_field_name) {
						$ret[$row[$key_field_name]] = $row[$value_field_name];
					} else {
						$ret[$row[$key_field_name]] = $row;
					}
				}
			} else {
				$ret = $rows;
			}
		} else {  // no multiple records
			$ret = $query->row_array();
		}
		
		$query->free_result();
		
		if ($cache_timeout) {
			// Store in cache for next time
			$this->cache->save($cache_key, $ret, $cache_timeout*60); // since cache_tiemout is in minutes
		}
		
		return $ret;
	}
	
	
	/**
	 * Yes, another function for queries
	 * @param string $sql
	 * @param boolean $multiple_records
	 * @param string $key_field_name - return rows indexed by this
	 * @param string $value_field_name - only return this value
	 * @return array
	 */
	public function generic_query($sql, $multiple_records = true, $key_field_name = null,  $value_field_name = null) {
		// CACHE: create cache key and check if we didn't cache the result alreay
		$cache_key = "genericquery_".md5($sql);
		$cache_timeout = my_config_item('CACHE_sql_timeout');
		if ($this->force_no_cache) {
			$cache_timeout = 0;  // no cache!
		} 
		if ($cache_timeout) {
			// Check if there's a value already in the chache
			$result = $this->cache->get($cache_key);
			if ($result) {
				return $result;
			}
		}
		
		
		$query = $this->db->query($sql);
		if (!$multiple_records) {
			$ret = $query->row_array();
			
		} else {
			
			$rows =  $query->result_array();
			$ret = array();
			if ($key_field_name) {
				foreach ($rows as $row) {
					if ($value_field_name) {
						$ret[$row[$key_field_name]] = arr_get_value($row, $value_field_name);
					} else {
						$ret[$row[$key_field_name]] = $row;
					}
				}
			} else {
				$ret = $rows;
			}
			
			
		}
		
		$query->free_result();
		
		if ($cache_timeout) {
			// Store in cache for next time
			$this->cache->save($cache_key, $ret, $cache_timeout*60); // since cache_tiemout is in minutes
		}		
		return $ret;
	}

	// Gets all rows, yup..
	// Supported the emulated index name
	// Tries to make it indexed by the ID, if provided
	public function get_all_rows() {
		$sql = "SELECT *";
		if ($this->get_emulated_index_name()) {
			
			$sql .= ", ".$this->get_index_name()." AS ".$this->get_emulated_index_name();
		}
		
		$sql .= " FROM ".$this->get_table_name();
		
		
		$index_name = $this->get_index_name() ?  $this->get_index_name() : null;
		
		return $this->generic_query($sql, true, $index_name);
	}
	
	public function get_seo_data($item_id){
		return $this->get_by_primary($item_id,$this->seo_fields);
	}
	
}