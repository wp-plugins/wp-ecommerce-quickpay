<?php
/**
 * WPEC_QP_API_Payment class
 *
 * @class 		WPEC_QP_API_Payment
 * @since		1.0.0
 * @category	Class
 * @author 		PerfectSolution
 * @docs        http://tech.quickpay.net/api/services/?scope=merchant
 */

class WPEC_QP_API_Payment extends WPEC_QP_API_Transaction
{
  	/**
	* __construct function.
	*
	* @access public
	* @return void
	*/      
    public function __construct( $resource_data = NULL ) 
    {
    	// Run the parent construct
    	parent::__construct();

    	// Set the resource data to an object passed in on object instantiation.
    	// Usually done when we want to perform actions on an object returned from 
    	// the API sent to the plugin callback handler.
  		if( is_object( $resource_data ) ) 
  		{
  			$this->resource_data = $resource_data;
  		}

    	// Append the main API url
        $this->api_url = $this->api_url . 'payments/';
    }


   	/**
	* capture function.
	* 
	* Sends a 'capture' request to the Quickpay API
	*
	* @access public
	* @param  int $transaction_id
	* @param  int $order_id
	* @param  int $amount
	* @return void
	* @throws Quickpay_API_Exception
	*/   
    public function capture( $transaction_id, $order_id, $amount = NULL ) 
    {
        global $wpdb;
        
        // Check if a custom amount ha been set
        if( $amount === NULL ) 
        {
            // No custom amount set. Default to the order total
            $amount = $wpdb->get_var("SELECT totalprice FROM " . WPSC_TABLE_PURCHASE_LOGS . " WHERE id = '$order_id' LIMIT 1");   
        }
        
    	$request = $this->post( sprintf( '%d/%s', $transaction_id, "capture" ), array( 'amount' => WPEC_QP_Gateway::price_multiply( $amount ) ) );
    }


  	/**
	* cancel function.
	* 
	* Sends a 'cancel' request to the Quickpay API
	*
	* @access public
	* @param  int $transaction_id
	* @return void
	* @throws Quickpay_API_Exception
	*/   
    public function cancel( $transaction_id ) 
    {
    	$request = $this->post( sprintf( '%d/%s', $transaction_id, "cancel" ) );
    }

    
   	/**
	* refund function.
	* 
	* Sends a 'refund' request to the Quickpay API
	*
	* @access public
	* @param  int $transaction_id
	* @param  int $amount
	* @return void
	* @throws Quickpay_API_Exception
	*/   
    public function refund( $transaction_id, $order_id, $amount = NULL ) 
    {
        global $wpdb;
        
        // Check if a custom amount ha been set
        if( $amount === NULL ) 
        {
            // No custom amount set. Default to the order total
            $amount = $wpdb->get_var("SELECT totalprice FROM " . WPSC_TABLE_PURCHASE_LOGS . " WHERE id = '$order_id' LIMIT 1");   
        }
    
        $request = $this->post( sprintf( '%d/%s', $transaction_id, "refund" ), array( 'amount' =>  WPEC_QP_Gateway::price_multiply( $amount ) ) );
    }


	/**
	* is_action_allowed function.
	*
	* Check if the action we are about to perform is allowed according to the current transaction state.
	*
	* @access public
	* @return boolean
	*/
	public function is_action_allowed( $action ) 
    {
		$state = $this->get_current_type();

		$allowed_states = array(
			'capture' => array( 'authorize' ),
			'cancel' => array( 'authorize' ),
			'refund' => array( 'capture', 'refund' ),
			'renew' => array( 'authorize' ),
			'splitcapture' => array( 'authorize', 'capture' ),
			'recurring' => array( 'subscribe' ),
            'standard_actions' => array( 'authorize', 'capture' )
		);

		return in_array( $state, $allowed_states[$action] );
	}
}