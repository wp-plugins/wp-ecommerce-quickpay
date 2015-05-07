<?php

class Quickpay_Exception extends Exception
{
	/**
	 * Contains a log object instance
	 * @access protected
	 */
	protected $log;


  	/**
	* __Construct function.
	* 
	* Redefine the exception so message isn't optional
	*
	* @access public
	* @return void
	*/ 
    public function __construct($message, $code = 0, Exception $previous = null) {
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);

        $this->log = new WPEC_QP_Logs();
    }


  	/**
	* write_to_logs function.
	* 
	* Stores the exception dump in the system logs
	*
	* @access public
	* @return void
	*/  
	public function write_to_logs() 
	{
		$this->log->separator();
		$this->log->add( 'Quickpay Exception file: ' . $this->getFile() );
		$this->log->add( 'Quickpay Exception line: ' . $this->getLine() );
		$this->log->add( 'Quickpay Exception code: ' . $this->getCode() );
		$this->log->add( 'Quickpay Exception message: ' . $this->getMessage() );
		$this->log->separator();
	}


  	/**
	* write_standard_warning function.
	* 
	* Prints out a standard warning
	*
	* @access public
	* @return void
	*/ 
	public function write_standard_warning()
	{	
		echo wp_kses( 
				__( "An error occured. For more information check out the errors logs inside <strong>Tools -> Quickpay Logs</strong>.", 'wpec-quickpay' ), array( 'strong' => array() ) 
			);

	}
}


class Quickpay_API_Exception extends Quickpay_Exception 
{
  	
  	/**
	* write_to_logs function.
	* 
	* Stores the exception dump in the system logs
	*
	* @access public
	* @return void
	*/  
	public function write_to_logs() 
	{
		$this->log->separator();
		$this->log->add( 'Quickpay API Exception file: ' . $this->getFile() );
		$this->log->add( 'Quickpay API Exception line: ' . $this->getLine() );
		$this->log->add( 'Quickpay API Exception code: ' . $this->getCode() );
		$this->log->add( 'Quickpay API Exception message: ' . $this->getMessage() );
		$this->log->separator();
	}
}
?>