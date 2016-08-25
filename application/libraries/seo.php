<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * ciSEO : CodeIgniter Search Engine Optimization Library
 * This library is meant to help you when dealing with one of the most important
 * stages of development: SEO.
 *
 * This library will help you with:
 *  - Titles
 *  - Keywords
 *  - Descriptions
 *  - Canonical
 *  - Prev, Next, Start links
 *  - Google, MSN and Yahoo Verification codes
 *
 * Needs: configuracion/config_general.php
 *
 * @subpackage	Libraries
 * @category	Search Engine Optimization
 * @author		Mario "Kuroir" Ricalde
 * @version 0.1
 */
class Seo
{
    # Determines the which method to use. True for automatic
    public $auto                 = true;
    # Filter Words for keywords? check function filterWords for more information
    public $filterWordsForOutput = true;
    # Output
    private $output;
    # Array for filterWords
    private $filter              = array();
    # Weappers for the final output (tab and new line by default).
    private $wrappers            = array("pre" => "\t", "post" => "\n");
    # Array that tracks which elements have been set.
    private $history             =  array('title'       => false,
                                          'keywords'    => false,
                                          'description' => false,
                                          'canonical'   => false,
                                          'start'       => false,
                                          'prev'        => false,
                                          'next'        => false,
                                          'google'      => false,
                                          'msn'         => false,
                                          'yahoo'       => false);
    private $CI;
    
    /**
     * Constructor, accepts two boolean parameters as an array in this order:
     * - Automatic Method (true or false)
     * - Filter Words on Keywords method (true or false)
     * @param mixed $params
     */
    public function __construct($params = '')
    {
        log_message('debug', "Seo Class Initialized");
        if(is_array($params))
        {
            $this->auto = isset($params['0']) ? $params['0'] : $this->auto;
            $this->filterWordsForOutput = isset($params['1']) ? $params['1'] : $this->filterWordsForOutput;
        }
        $this->CI =& get_instance();
    }
    
    /**
     * Method for setting everything with one call, it only accepts an associative
     * array as parameter.
     * @params mixed $params "method" => "value"
     */
    public function set($params = '')
    {
        # Verify it's an array
        if($params === '' || is_array($params) === false)
        {
            trigger_error("SEO: set() only accepts arrays", E_USER_WARNING);
        }

        foreach($params as $function => $val)
        {
            # Standarize the string so it works with out function setFunction
            $function = strtolower($function);
            $function = ucfirst($function);
            $function = "set{$function}";
            # Check if the Method exists.
            if(method_exists($this, $function))
            {
                # Call the function passing the value
                $this->$function($val);
            } else {
                trigger_error("SEO: The Method doesn't exist when called by set().", E_USER_WARNING);
            }
        }
    }
    
    /**
     * Method for Keywords, it accepts strings and arrays. Once you pass a variable
     * it will filter words if enabled, remove everything but alphanumeric characters,
     * separate by commas, and limit to 150 characters.
     * @params mixed $string
     */
    public function setKeywords($string = '')
    {
        if($string === '')
        {
           trigger_error("SEO: setKeywords no acepta variables vacías.", E_USER_WARNING);
        }
        
        # If its an array we convert it to a string
        if(is_array($string))
        {
            $string = implode(' ', $string);
        }
        
        # Verify if we need to filter the string
        if($this->filterWordsForOutput === true)
        {
            $string = $this->filterWords($string);
        }
                
        # Sanitize String
        $string = $this->sanitize($string);
        
        # Remove anything but alphanumeric characters
        $string = preg_replace('/[^\p{L}\p{N}]+/u', ' ', $string);
        
        # Remove excesive spaces.
        $string = trim($string);
        
        # Add commas.
        $string = preg_replace('/\s/', ', ', $string);
        
        # Build Output
        return $this->buildOutput(array('keywords' => (string)$string));
    }
    
    /**
     * Method that sets the title by passing a string, it'll check the configuration
     * file for site_persistent_title and add the title at the end of the passed
     * string if enabled.
     * @params string $string Site title
     */
    public function setTitle($string = '')
    {
        # We only allow strings.
        if(false === is_string($string))
        {
            trigger_error("SEO: setTitle() only accepts strings.", E_USER_WARNING);
        }
        
        # Sanitize the String
        $string = $this->sanitize($string);
         
        # Verify if site_persistent_title is enabled and if it is then check the
        # string is not the same as the persistent_title to prevent: Sitename - Sitename
        if($this->CI->config->item('site_persistent_title') === true &&
           $this->CI->config->item('site_title') !== $string)
        {
            $string .= " - {$this->CI->config->item('site_title')}";
        }
        
        # Build
        return $this->buildOutput(array("title" => (string)$string));
    }
    
    /**
     * Method for the setting the description, it sanitize the string and limits
     * the string to 150 characters.
     * @params string $string
     */
    public function setDescription($string = '')
    {
        # We only allow strings.
        if(false === is_string($string))
        {
            trigger_error("SEO: setDescription() only accepts strings.", E_USER_WARNING);
        }
        # Sanitize the String
        $string = $this->sanitize($string);
        # Build
        return $this->buildOutput(array("description" => (string)$string)); 
    }
        
    /**
     * Method for setting Canonical. It uses site_url() tu build a good url structure.
     * it accepts empty URI.
     * @params string $variable URI
     */
    public function setCanonical($string = '')
    {       
        # Build the url
        return $this->buildOutput(array("canonical" => $this->buildUrl($string)));;
    }
    
    /**
     * Method for setting start. It uses site_url() tu build a good url structure.
     * it accepts empty URI.
     * @params string $variable URI
     */
    public function setStart($string = '')
    {
        # Generamos el Start
        return $this->buildOutput(array("start" => $this->buildUrl($string)));;
    }

    /**
     * Method for setting next. It uses site_url() tu build a good url structure.
     * it accepts empty URI.
     * @params string $variable URI
     */
    public function setNext($string = '')
    {
        # Generamos el Start
        return $this->buildOutput(array("next" => $this->buildUrl($string)));;
    }
    
    /**
     * Method for setting prev. It uses site_url() tu build a good url structure.
     * it accepts empty URI.
     * @params string $variable URI
     */
    public function setPrev($string = '')
    {
        # Generamos el prev
        return $this->buildOutput(array("prev" => $this->buildUrl($string)));;
    }
    
    /**
     * Method for Google Verification code. If on automatic mode there's no need
     * to explicitly call it, it'll be automatically included taking the info from
     * the config file.
     * @params string $string verification code
     */
    public function setGoogle($string = '')
    {
        if($string === '')
        {
            //trigger_error('SEO: setGoogle no acepta strings vacías', E_USER_WARNING);
        }
        
        return $this->buildOutput(array("google" => $string));
    }
    
    /**
     * Method for MSN Verification code. If on automatic mode there's no need
     * to explicitly call it, it'll be automatically included taking the info from
     * the config file.
     * @params string $string verification code
     */
    public function setMsn($string = '')
    {
        if($string === '')
        {
            //trigger_error('SEO: setMsn no acepta strings vacías', E_USER_WARNING);
        }
        
        return $this->buildOutput(array("msn" => $string));
    }
    
    /**
     * Method for Yahoo Verification code. If on automatic mode there's no need
     * to explicitly call it, it'll be automatically included taking the info from
     * the config file.
     * @params string $string verification code
     */
    public function setYahoo($string = '')
    {
        if($string === '')
        {
            //trigger_error('SEO: setYahoo no acepta strings vacías', E_USER_WARNING);
        }
        
        return $this->buildOutput(array("yahoo" => $string));
    }
    
    /**
     * Building Method
     * The parameter needs to be an associative array, tipe => value
     * @params array $data 
     */
    private function buildOutput($data = '')
    {
        if( ! is_array($data))
        {
            return false;
        }
        
        # Get the Key
        $key = (string)key($data);
        
        # Wrappers
        $ps  =& $this->wrappers["pre"];
        $pe  =& $this->wrappers["post"];
        
        # Switch with all html values.
        # We use $history to prevent from printing or saving the string twice.
        switch($key)
        {
            case "title":
                if($this->history[$key] === true)
                {
                    break;
                }
                $output = $ps."<title>{$data[$key]}</title>".$pe;
                $this->history[$key] = true;
            break;
            case "keywords":
            case "description":
                if($this->history[$key] === true)
                {
                    break;
                }
                $data[$key] = mb_substr($data[$key], 0, 150);
                $output = $ps."<meta content='{$data[$key]}' name='{$key}'/>".$pe;
                $this->history[$key] = true;
            break;
            case "canonical":
            case "start":
            case "prev":
            case "next":
                if($this->history[$key] === true)
                {
                    break;
                }
                $output = $ps."<link href='{$data[$key]}' rel='{$key}'/>".$pe;
                $this->history[$key] = true;
            break;
            case "google":
                if($this->history[$key] === true)
                {
                    break;
                }
                $output = $ps."<meta content='{$data[$key]}' name='verify-v1'/>".$pe;
                $this->history[$key] = true;
            break;
            
            /*
            case "msn":
                if($this->history[$key] === true)
                {
                    break;
                }
                $output = $ps."<meta content='{$data[$key]}' name='msvalidate.01'/>".$pe;
                $this->history[$key] = true;
            break;
            case "yahoo":
                if($this->history[$key] === true)
                {
                    break;
                }
                $output = $ps."<meta content='{$data[$key]}' name='y_key'/>".$pe;
                $this->history[$key] = true;
            break;
            */
        }
        
        # Check the output is not empty because of a repeated try.
        if( ! empty($output) )
        {
            # Return Value
            if($this->auto === true)
            {
                $this->output .= $output;
            } elseif($this->auto === false) {
                return $output;
            } else {
                trigger_error('SEO: $auto needs to be boolean');
            }
        }
    }
    
    /**
     * Method to filter words on a string. It's used to remove prepositions, pronouns,etc
     * @params tipe $variable descripcion
     */
    public function filterWords($data = '')
    {
        # Verificamos que el string no este vacío.
        if($data === '')
        {
            trigger_error('SEO: filterWords doesnt allow empty strings.', E_USER_WARNING);
        }
        # Check if the filter is already set.
        if(empty($this->filter))
        {
            $this->filter =
                array(
                    # Prepositions (spanish)
                    " a "," ante "," bajo "," cabe "," con "," contra "," de ",
                    " desde "," durante "," en "," entre "," hacia "," hasta ",
                    " mediante "," para "," según "," por "," sin "," so ",
                    " sobre "," tras "," vía ",
                    # Pronouns (spanish)
                    " yo "," me "," mi "," tu "," vos "," usted "," te "," ti ",
                    " el "," lo "," ella "," la "," se "," si "," nosotros ",
                    " nosotras "," nos "," ustedes "," vosotros "," vosotras ",
                    " ellos "," ellas "," los "," las "," les ",
                    # Etc
                    " que "," es "," son "," he "," ha ", " hemos "
                );
        }
        # Filter words, add spaces at the begin and the end of the string so we can replace everything.
        return str_replace($this->filter, ' ', ' '.$data.' ');
    }
    
    /**
     * Function to sanitize everything. This way you can add custom filters.
     * @params string $variable
     */
    private function sanitize($string)
    {
        return strip_tags($string);
    }
    
    /**
     * Builds the URL using base_url() or site_url().
     * @params tipe $variable descripcion
     */
    private function buildUrl($string = '')
    {
        # Check it's not an empty 
        if($string === '')
        {
            return base_url();
        } else {
            return site_url($string);
        }
    }
    
    /**
     * If on automatic mode, checks for the $history array to verify that all
     * the mandatory methods have been called by the user and then returns the
     * output.
     * @return string Output
     */
    public function output()
    {
        # If Automatic check for the mandatory methods.
        if($this->auto === true)
        {
            if($this->history['title'] === false)
            {
                $this->setTitle($this->CI->config->item('site_title'));
            }
            if($this->history['description'] === false)
            {
                $this->setDescription($this->CI->config->item('site_description'));
            }
            if($this->history['keywords'] === false)
            {
            	$this->setKeywords($this->CI->config->item('site_keywords'));
            }            
            if($this->history['google'] === false)
            {
                $this->setGoogle($this->CI->config->item('seo_google_vcode'));
            }
            if($this->history['msn'] === false)
            {
               $this->setMsn($this->CI->config->item('seo_msn_vcode'));
            }
            if($this->history['yahoo'] === false)
            {
               $this->setYahoo($this->CI->config->item('seo_yahoo_vcode'));
            }
        }
        return $this->output;
    }
}