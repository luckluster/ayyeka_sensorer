<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @author Gregra
 * @version 0.1
 */
class Orca_form {

	/**
	 * PUBLIC properties are called inside the view
	 */
	//The HTML id of the form (optional)
    public $form_id = 'orca_form';
    //The fields to show (mandatory)
    public $fields = array();
    
    //The data of the form
    public $formdata = array();
    
    //The ID of the item that we want to edit (optional). If not supplied, the form will assume this is a new item (creation)
    private $item_id = null;
    
    //The model that we are reffering to - must be of type 'MY_Model' 
	private $data_model;
    
	//The URL that the page will be redirected to upon successful insert
	private $redirect_url=null;
    /**
     * Constructor
     * $params consists of:
     * 		form_id - The HTML id of the form (optional)
     * 		item_id - The ID of the item that we want to edit (optional). If not supplied, the form will assume this is a new item (creation)
     * 		fields - The fields to show (required)
     * 			a single field may have is an array that has:
	 *							name: The input name
	 *							db_name: The name of the field in the DB
	 *							header: The label of the field
	 *							required: Does the field required
	 *							validation: CI validation extra rules (see http://ellislab.com/codeigniter/user-guide/libraries/form_validation.html)
	 *							form_control: The type of HTML input (text/checkbox/dropdown/textarea/datepicker/datetimepicker/timepicker/file)
	 *							type: The type of the input (string/int/date/datetime/time)
	 *					),
     *		redirect_url - The URL that the page will be redirected to upon successful insert (mandatory)
     * 		data_model - The model that we are reffering to (mandatory) - must be of type 'MY_Model'
    **/
    public function __construct($params = array())
    {
        $this->CI = & get_instance();
        $this->CI->load->language('orca_crud');
        $this->CI->load->config('orca_crud');
        $this->form_id = arr_get_value($params, 'form_id', $this->form_id);
        $this->item_id = arr_get_value($params, 'item_id');
        $this->fields = arr_get_value($params, 'fields');
        $this->redirect_url = arr_get_value($params, 'redirect_url');
        $data_model_str = arr_get_value($params, 'data_model');
        
        if (!$this->fields){
        	throw new Exception('Fields weren\'t supplied');
        }
        
        if (!$data_model_str){
        	throw new Exception('Data model wasn\'t supplied');
        }
        $this->CI->load->model($data_model_str,'orca_model');
       	$data_model = new $data_model_str;

        if (!is_a($data_model, 'MY_Model')){
        	throw new Exception('Data model is not an instance of MY_Model');
        }
        $this->data_model = $data_model;
        
    }

    /**
     * Run
    **/
    public function run($validate = false)
    {
        $this->CI->load->library('form_validation');

        $item_data = null;
        if (!is_null($this->item_id)){
            $item_data = $this->CI->orca_model->get_by_primary($this->item_id);
            
            //If item does not exist in DB - redirect
            if (!$item_data){
            	$redirect_url = $this->_guess_redirection(0);
            	redirect($redirect_url);
            }
        }

        foreach ($this->fields as $key => $column){
            // Set up validation rules
            $validation = 'trim|xss_clean' . (arr_get_value($column, 'validation')  ? ('|' . arr_get_value($column, 'validation')) : '');

            // Set type specific validation
            switch (arr_get_value($column, 'type')){
                case 'integer':
                    $validation .= "|integer";
                    break;
                case 'date':
                    $validation .= "|check_date[".arr_get_value($column, 'date_format')."]";
                    break;
                case 'datetime':
                    $validation .= "|check_date[".arr_get_value($column, 'time_format')."]";
                    break;
                case 'time':
                    $validation .= "|check_date[".arr_get_value($column, 'time_format')."]";
                    break;
            }

            if (!is_null(arr_get_value($column, 'min_length'))){
                $validation .= '|min_length[' . arr_get_value($column, 'min_length') . ']';
            }
            if (!is_null(arr_get_value($column, 'max_length'))){
                $validation .= '|max_length[' . arr_get_value($column, 'max_length') . ']';
            }
            if (arr_get_value($column,'required')){
                $validation .= '|required';
            }

            if (arr_get_value($column,'form_control') == 'file'){
                $path_temp = rtrim(arr_get_value($column,'upload_path_temp'), '/') . '/';

                if (isset($_FILES['cg_field_' . $key]) && $_FILES['og_field_' . $key]['name'])
                {
                    // Delete old file
                    if (file_exists($path_temp . $this->CI->input->post['og_field_' . $key]))
                    {
                        @unlink($path_temp . $this->CI->input->post['og_field_' . $key]);
                    }
                    $_POST['og_field_' . $key] = $_FILES['og_field_' . $key]['name'];
                }
                else if ($this->CI->input->post('og_delete_file_' . $key) !== FALSE)
                {
                    // Delete old file
                    if (file_exists($path_temp . $this->CI->input->post['og_field_' . $key]))
                    {
                        @unlink($path_temp . $this->CI->input->post['og_field_' . $key]);
                    }
                    $_POST['og_field_' . $key] = '';
                }
                $validation .= '|check_upload[og_field_'. $key .
                    ':' . arr_get_value($column,'upload_path_temp') .
                    ':' . preg_replace('/\|/', '&', arr_get_value($column,'allowed_types')) .
                    ':' . arr_get_value($column, 'max_size') . ']';
            }

            $this->CI->form_validation->set_rules('og_field_'.$key, arr_get_value($column,'name'), $validation);
            if ($db_field=arr_get_value($column,'db_name')){
            	$fields_data[$db_field] = arr_get_value($_POST,'og_field_' . $key);
            }
        }
        $this->CI->form_validation->set_error_delimiters('<li class="alert alert-warning">', '</li>');

        //If form is validated, try to update or insert
        if ($validate){
            if ($this->CI->form_validation->run() !== false){
            	//Update data or insert data
                $result = $this->item_id 	? $this->CI->orca_model->update_record($this->item_id, $fields_data)
                							: $this->CI->orca_model->add_new_record($fields_data);               
                //All OK
                if ($result){
                	if ($this->redirect_url){
                		redirect($this->redirect_url);
                	}else{
                		
                		//redirect only when creating
                		if (!$this->item_id){
	                		//We need to check if this was a new item so we can redirect it properly
	                		$redirect_url = $this->_guess_redirection($result);
                		}else{
                			$redirect_url = $this->_guess_redirection($this->item_id);
                		}
                		redirect($redirect_url);
                	}
                }else{
                	return false;
                }	
            }
        }
        // Set form values
        foreach ($this->fields as $key => $column){
            if (($value = $this->CI->input->post('og_field_' . $key)) !== false){
                if (arr_get_value($column,'form_control') == 'file' && form_error('cg_field_' . $key))
                {
                    $this->formdata[$key] = '';
                }
                else
                {
                    $this->formdata[$key] = $value;
                }
            }else{
                // Set type specific value formating
                switch (arr_get_value($column,'type')){
                    case 'date':
                        $this->formdata[$key] = is_null($this->item_id) ? arr_get_value($column,'form_default') 
                        												: orca_format_date(arr_get_value($item_data,$column['db_name']), 'Y-m-d', arr_get_value($column,'date_format'));
                        break;
                    case 'datetime':
                        $this->formdata[$key] = is_null($this->item_id) ? arr_get_value($column,'form_default') 
                        												: orca_format_date($arr_get_value($item_data,$column['db_name']), 'Y-m-d H:i:s', arr_get_value($column,'date_format') . ' ' . arr_get_value($column,'time_format'));
                        break;
                    case 'time':
                        $this->formdata[$key] = is_null($this->item_id) ? arr_get_value($column,'form_default') : orca_format_date(arr_get_value($item_data,$column['db_name']), 'H:i:s', arr_get_value($column,'time_format'));
                        break;
                    case '1-n': //@todo
                        $this->formdata[$key] = is_null($this->item_id) ? arr_get_value($column,'form_default') : arr_get_value($item_data,$column['db_name'] . '_id');
                        break;
                    default:
                    	$this->formdata[$key] = is_null($this->item_id) ? arr_get_value($column,'form_default') : arr_get_value($item_data,$column['db_name']);
                    	break;
                }
            }
        }
        return FALSE;
    }

    /**
     * Render the form
    **/
    public function render()
    {
        return $this->CI->load->view('orca/orca_form', array('form' => $this), true);
    }
    
    
    /*************************/
    /**** PRIVATE METHODS ****/
    /*************************/
	
    /**
     * Attempts to guess the redirection page,
     * after the page was created
     */
    private function _guess_redirection($item_id){
    	$redirection_url = site_url() . '/';
    	$trace = debug_backtrace();
    	$called_by_class = $trace[2]['class'];
    	
    	$redirection_url .= strtolower($called_by_class) . '/';
    	
    	if ($item_id){
    		$redirection_url .= CRUD_ENDPOINT_READ.'/' . $item_id;
    	}else{
    		$redirection_url .= CRUD_ENDPOINT_CREATE;
    	} 
    
    	return $redirection_url;
    }
    
}

/* End of file Carboform.php */
/* Location: ./application/libraries/Carboform.php */
