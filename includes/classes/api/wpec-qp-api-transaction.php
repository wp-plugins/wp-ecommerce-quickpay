<?php
/**
 * WPEC_QP_API_Transaction class
 * 
 * Used for common methods transaction methods. Currently only payments.
 *
 * @class 		WPEC_QP_API_Transaction
 * @since		1.0.0
 * @category	Class
 * @author 		PerfectSolution
 * @docs        http://tech.quickpay.net/api/services/?scope=merchant
 */

class WPEC_QP_API_Transaction extends WPEC_QP_API
{
    /**
	* get_current_type function.
	* 
	* Returns the current payment type
	*
	* @access public
	* @return void
	*/ 
    public function get_current_type() 
    {
    	$last_operation = $this->get_last_operation();
        
        if( ! is_object( $last_operation ) ) 
        {
            throw new Quickpay_API_Exception( "Malformed operation response", 0 ); 
        }
        
        return $last_operation->type;
    }


  	/**
	* get_last_operation function.
	* 
	* Returns the last successful transaction operation
	*
	* @access public
	* @return void
	* @throws Quickpay_API_Exception
	*/ 
	public function get_last_operation() 
	{
		if( ! is_object( $this->resource_data ) ) 
		{
			throw new Quickpay_API_Exception( 'No API payment resource data available.', 0 );
		}

		// Loop through all the operations and return only the operations that were successful (based on the qp_status_code and pending mode).
		$successful_operations = array_filter($this->resource_data->operations, function( $operation ) {
			return $operation->qp_status_code == 20000 || $operation->pending == TRUE;
		} );
        
        $last_operation = end( $successful_operations );
        
        if( $last_operation->pending == TRUE ) {
            $last_operation->type = __( 'Pending - check your Quickpay manager', 'wpec-quickpay' );   
        }
        
		return $last_operation;
	}

    
    /**
	* is_test function.
	* 
	* Tests if a payment was made in test mode.
	*
	* @access public
	* @return boolean
	* @throws Quickpay_API_Exception
	*/     
    public function is_test() 
    {
		if( ! is_object( $this->resource_data ) ) {
			throw new Quickpay_API_Exception( 'No API payment resource data available.', 0 );
		}

    	return $this->resource_data->test_mode;
    }
}