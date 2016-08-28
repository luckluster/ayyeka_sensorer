<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @author Gregra
 * @version 0.1
 */

class MY_Form_validation extends CI_Form_validation {
	
    public function __construct()
    {
        parent::__construct();
        $this->CI = & get_instance();
        $this->CI->load->helper('orca_helper');
    }

    /**
     * Check date
    **/
    public function check_date($date, $format)
    {
        if (!$format)
        {
            $format = 'm/d/Y';
        }

        $pformat = preg_replace('/([dDljmMFnYyGgHhAais])/', '%$1', $format);

        $ret = orca_strptime($date, $pformat);

        if ($ret === FALSE OR !isset($ret['tm_mon']) OR !isset($ret['tm_mday']) OR !isset($ret['tm_year']))
        {
            return FALSE;
        }

        if (!checkdate($ret['tm_mon'], $ret['tm_mday'], $ret['tm_year']))
        {
            return FALSE;
        }

        return carbo_format_date($ret, $format, $format);
    }
    
    /**
     * Check upload
     **/
    function check_upload($value, $str)
    {
    	$str = explode(':', $str);
    	$field = $str[0];
    	$upload_path_temp = isset($str[1]) ? $str[1] : './temp';
    	$allowed_types = isset($str[2]) ? preg_replace('/\&/', '|', $str[2]) : 'gif|jpg|png';
    	$max_size = isset($str[3]) ? $str[3] : 1024;
    
    	if (isset($_FILES[$field]) && $_FILES[$field]['name'])
    	{
    		$config['upload_path'] = $upload_path_temp;
    		$config['allowed_types'] = $allowed_types;
    		$config['max_size'] = $max_size;
    
    		$this->CI->load->library('upload', $config);
    
    		if (!$this->CI->upload->do_upload($field))
    		{
    			$this->CI->form_validation->set_message('check_upload', '%s: ' . $this->CI->upload->display_errors('', ''));
    			return FALSE;
    		}
    		else
    		{
    			$data = $this->CI->upload->data();
    			return $data['file_name'];
    		}
    	}
    	return TRUE;
    }
    
    
}

/* End of file MY_Form_validation.php */
/* Location: ./system/application/libraries/MY_Form_validation.php */
