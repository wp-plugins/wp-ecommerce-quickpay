<?php
/**
 * WPEC_QP_Logs class
 *
 * @class 		WPEC_QP_Logs
 * @since		1.0.0
 * @category	Logs
 * @author 		PerfectSolution
 */
class WPEC_QP_Logs {
    
    /* The domain handler used to name the log */
    private $_file_name = 'errors.log';
    
    private $_file_path;
    
    /* The file resource */
    private $_resource;
    
    
    /**
	* __construct.
	*
	* @access public 
	* @return void
	*/	
    public function __construct() 
    {
        add_action('shutdown', array($this, 'shutdown'));
        
        // Set the file path
        $this->_file_path = dirname(dirname(plugin_dir_path(__FILE__))) . '/' . $this->_file_name;
        
        // Get file resource
        $this->open_file();                            
    }

    /**
	* open_file.
	*
	* @access public 
	* @return void
	*/	
    public function open_file() 
    {
        $this->_file = fopen( $this->_file_path, 'a+');        
    }
    
    
    /**
	* add function.
	*
	* Uses the build in logging method in WooCommerce.
	* Logs are available inside the System status tab
	*
	* @access public 
	* @param  string|array|object
	* @return void
	*/	
    public function add( $param ) 
    {
        if( is_array( $param ) ) {
            $param = print_r( $param, TRUE );  
        }
        
        fwrite($this->_file, date('Y-m-d H:i:s') . ' - ' . $param . PHP_EOL );
    }
 
    
    /**
	* output function.
	*
	* Prints out the content of the log file.
	*
	* @access public 
	* @return string
	*/	
    public function output() 
    {
        if( file_exists( $this->_file_path ) ) {
            return file_get_contents( $this->_file_path );
        }
    }
    
    /**
	* clear function.
	*
	* Clears the entire log file
	*
	* @access public 
	* @return void
	*/	
    public function clear() 
    {
        if( file_exists( $this->_file_path ) ) 
        {
            unlink( $this->_file_path );
        }
        
        $this->open_file();
    }
 
    
    /**
	* separator function.
	*
	* Inserts a separation line for better overview in the logs.
	*
	* @access public 
	* @return void
	*/	
    public function separator() 
    {
        $this->add( '--------------------' );  
    }


    /**
	* get_domain function.
	*
	* Returns the log text domain
	*
	* @access public 
	* @return string
	*/	
    public function get_domain() 
    {
    	return $this->_domain;
    }
    
    
    /**
     * Closes the file resource on shutdown
     * 
     * @public
     * @return void
     */
    public function shutdown() 
    {
        if( $this->_file ) {
            fclose( $this->_file );
        }
        
        if( file_exists( $this->_file_path ) ) {
            chmod( $this->_file_path, 0600 );   
        }
    }
}