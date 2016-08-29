<?php
/**
 * 
 * NOTE: Contains more classes below - currently also contains "MY_Admin_Controller"
 * (CI limitation - I'd prefer to put it in a different class)
 * @author Trevize
 *
 */
class MY_Controller extends CI_Controller {
	
	// Current layout - which is the folders under application/views 
	private $layout = null;
	private $lang_arr = array();
	protected $seo_settings = array();
	
	
	public function __construct () {		
		parent::__construct();	
		$this->layout = LAYOUT_WEB;  // The default. May change later according to domain/GET param!
	
		# SEO library
		$this->load->library('seo');
		$this->load->config('seo');
		
		$this->seo_settings["title"] = my_config_item('site_title');
		$this->seo_settings["description"] = my_config_item('site_description');
		$this->seo_settings["keywords"] = my_config_item('site_keywords');
		
		$this->my_load_lang("general");
		
		require_once(APPPATH."libraries/orca_grid/orca_grid.php");
		
		$this->init_cache();
	}

	public function set_layout($layout){
		$this->layout = $layout;
	}
	
	
	public function load_grid_library() {
		
	}
	
	/**
	 * Renders the specified view - and echos it on the browser or returns it as HTML (depending on params)
	 * Public so it can be called from the libraries too.
	 * 
	 * @param string $filename - filename to load from  application/views/[mobile/web/json]/ folder  - depending on $this->layout
	 * @param array $data - all the variables we're sending to the view
	 * @param array $params  - see below
	 * @return string - HTML (if specified in $params)
	 */
	public function render_view($filename, array $data = array(), array $params = array()) {
		// $params may contain... (everything is optional)
		// 'title'				=> HTML header title
		// 'return_html'    	=> don't echo on screen.
		//		ATTENTION: using this switch will prevent from CodeIgniter from caching the page..
		//		so in order for this and CodeIgniter WebPageCaching to work correctly, 
		//		any data returned this way must be finally outputted through a render_view called without the 'return_html' option  
		// 'head_extra_attributes' => Add this text to the <head .... >. Required for FB open graph
		// 'head_extra_tags'    => Add this text to the <head>...</head> section. Required for FB open graph or for additional JS libraries to load
		// 'body_extra_attributes' => Add more attributes to the <body... > tag. Required for the "authorize me" page 
		// 'no_headerfooter'	=> don't use the standard header+footer
		// 'scripts' => load these scripts in page footer. Syntax:
		//		array ( array('script' => base_url('/assets/js/stuff/stuff.js'), 'init_line' => 'stuff.init();'), ... ) 
		//		'init_line' is the line to execute in the $(document).ready section - optional
		// 'css' => array of CSS files to load in header (not implemented yet) 
		// 'minimal_headerfooter'=> Use header&footer without extra HTML. - but keep loading all the JS and CSS
		// 'no_layout_subfolder'	=> take the view file straight from the application/views folder (ignore $this->layout)
		// 'popup'				=> use header/footer suited for popups instead of the normal header/footer - not implemented yet
		
		
		$html = "";
		$subfolder = "";
		if (!arr_get_value($params, 'no_layout_subfolder')) {
			$subfolder .= $this->layout."/";
		}
		
		$is_ajax = $this->input->get('ajax');
		$no_headerfooter = arr_get_value($params, 'no_headerfooter') || $is_ajax;

		// And the params! They might be needed
		$data['params'] = $params;
		
	

		$return_html = arr_get_value($params, 'return_html');
		
		
		if (!$no_headerfooter) {
			if (!$return_html) {
				$this->load->view($subfolder."common/header", $data);
			} else {
				$html .= $this->load->view($subfolder."common/header", $data, true);
			}
		}
		
		if (!$return_html) {
			$this->load->view($subfolder.$filename, $data);
		} else {
			$html .= $this->load->view($subfolder.$filename, $data, true);
		}
		
		
		if (!$no_headerfooter) {
			if (!$return_html) {
				$this->load->view ($subfolder."common/footer", $data);
			} else {
				$html .= $this->load->view ($subfolder."common/footer", $data, true); 
			}
		}
		
		if ($return_html) {
			return $html;
		} 
		
	}
	
	/**
	 * Loads a language file using the current language.
	 * Also puts it in an array which will be later be exported to JS land under the name GLB_lang (maybe - not implemented currently)
	 * 
	 * Note: Currently the default language is just defined in the settings. It can be changed later to a user setting. 
	 * @param string $lang_file
	 */
		
	public function my_load_lang($lang_file) {
		// Put in our secret array (for JS)
		$this->lang_arr += $this->lang->load($lang_file, my_config_item('SETTINGS__default_language'), true);
		// Load normally:
		$this->lang->load($lang_file, my_config_item('SETTINGS__default_language'));
	}
		
	
	
	/**
	 * Function for enabling cache for every controller
	 * MUST not be called from this controller, since some controllers MUST not be cached,
	 * such as the webservices controller.
	 */
	protected function set_cache_for_controller() {
		$page_cache_timeout = my_config_item('CACHE_page_timeout');
		if ($page_cache_timeout) {
			$this->output->cache($page_cache_timeout);
		}
	}
	
	/**
	 * Simply return the last segment of the URL
	 * @param int $allow_only_int - allows validation for numeric last segment
	 */	
	protected function route_last_segment($allow_only_int=false){
		$segment = $this->uri->segment_array();
		$last_seg = $segment[$this->uri->total_segments()];
		if ($allow_only_int && is_numeric($last_seg))
			return $last_seg;
		else
			return false;
	}
	
	/**
	 * Generates all that it has to do with SEO stuff
	 * @param array $seo_data
	 * @param unknown_type $page_title
	 */
	protected function set_page_seo(array $seo_data,$page_title=''){
		/*
		 * $seo_data should have title, description, keywords
		 * optionally, may have other settings, no problems
		 * See documentation in application/libraries/seo.php
		 */
		extract($seo_data);		
		$seo_title = (strlen(trim($title))) ? $title : $page_title;
	
		@$this->seo_settings["title"] = (strlen(trim($seo_title))) ? trim($seo_title) : $this->seo_settings["title"];
		@$this->seo_settings["description"] = (strlen(trim($description))) ? trim($description) : $this->seo_settings["description"];
		@$this->seo_settings["keywords"] = (strlen(trim($keywords))) ? trim($keywords) : $this->seo_settings["keywords"];
		@$this->seo_settings["canonical"] = (isset($canonical)) ? $canonical : seo_link($this->uri->uri_string());
		
		$this->seo->set(array_merge($seo_data,$this->seo_settings));
	}	
	
	/**
	 * Generates all that it has to do with SEO stuff only for general pages
	 * @param unknown_type $page
	 * @param unknown_type $page_title
	 */
	protected function set_general_page_seo($page,$page_title){
		$seo_title_row = $this->general_model->get(array('gnr_page_name'=>$page,'gnr_field'=>'page_seo_title'));
		$seo_description_row = $this->general_model->get(array('gnr_page_name'=>$page,'gnr_field'=>'page_seo_description'));
		$seo_keywords_row = $this->general_model->get(array('gnr_page_name'=>$page,'gnr_field'=>'page_seo_keywords'));
		
		$seo_data = array();
		$seo_data["title"] = $seo_title_row['gnr_value'];
		$seo_data["description"] = $seo_description_row['gnr_value'];
		$seo_data["keywords"] = $seo_keywords_row['gnr_value'];
		$seo_data["canonical"] = $this->uri->uri_string();
		
		$this->set_page_seo($seo_data, $page_title);	
	}

	
	/**
	 * Response output for errors
	 * @param String $msg
	 * @param String $method
	 */
	protected function response_err($msg='',$method='ajax'){
		if ($method == 'ajax'){
			$resp = json_encode(array('status'=>'err','data'=>$msg));
			$this->output->set_content_type('application/json')->set_output($resp); 
		}else{
			$this->load->view('error_general');
			exit;
		}
	}

	/**
	 * Response output for all OK
	 * @param string $msg
	 * @param string $method
	 */
	protected function response_ok($data,$method='ajax'){
		if ($method == 'ajax'){
			$resp = json_encode(array('status'=>'ok','data'=>$data));
			$this->output->set_content_type('application/json')->set_output($resp);
		}else{//@todo
				
		}
	}
	
	
/** PRIVATE METHODS **/
	
	/**
	 * 
	 * Load the cache driver
	 */
	private function init_cache() {
		$this->load->driver('cache', array('adapter' => 'file'));
	}
	
}



class MY_Admin_Controller extends My_Controller {

    public function __construct() {

        parent::__construct();
		$original_url = $this->uri->uri_string();
		if (!is_admin()) {
            redirect('/admin/admin_login?redirect_to=' . $original_url);
        }
    }
}

/**
 * Interface to implement all relevent methods in 
 * 		the controllers
 * @author gregra
 *
 */
interface MY_Orca_CRUD_Interface{
	
	public function create();
	public function read($id);
	public function update($id);
	public function delete($id);
	public function list_view();
}

