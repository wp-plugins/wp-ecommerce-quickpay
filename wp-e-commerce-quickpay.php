<?php
/*
    Plugin Name: WP e-Commerce Quickpay
    Plugin URI: http://wordpress.org/plugins/wp-e-commerce-quickpay/
    Description: Integrates your Quickpay payment getway into your WP e-Commerce webshop.
    Version: 1.0.0
    Author: Perfect Solution
    Text Domain: wpe-quickpay
    Author URI: http://perfect-solution.dk
*/

class WPEC_Quickpay 
{
    
    /**
    * $_instance
    * 
    * @var mixed
    * @public
    * @static
    */
    public static $_instance = NULL;
 
    
    /**
    * get_instance
    * 
    * Returns a new instance of self, if it does not already exist.
    * 
    * @static
    * @return object WC_Quickpay
    */
    public static function get_instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    
    /**
     * The class construct
     * 
     * @public
     * @return void
     */
    public function __construct()
    {
        // Load the plugin files
        $this->prepare_files();

        // Prepare hooks and filters
        $this->hooks_and_filters();
        
        // Setup the gateway in WP E-Commerce
        $this->setup_gateway();
    }
    
    /**
     * Includes all the vital plugin files containing functions and classes
     * 
     * @public
     * @return  void
     * @since   1.0.0
     */    
    public function prepare_files()
    {
        $this->require_file( 'includes/classes/wpec-qp-settings.php' );
        $this->require_file( 'includes/classes/wpec-qp-checkout.php' );
        $this->require_file( 'includes/classes/wpec-qp-logs.php' );
        $this->require_file( 'includes/classes/wpec-qp-exceptions.php' );
        $this->require_file( 'includes/classes/api/wpec-qp-api.php' );
        $this->require_file( 'includes/classes/api/wpec-qp-api-transaction.php' );
        $this->require_file( 'includes/classes/api/wpec-qp-api-payment.php' );
        $this->require_file( 'includes/classes/wpec-qp-gateway.php' );
        $this->require_file( 'includes/functions.php' );
    }
    
    
    /**
     * Prepares all the hooks and filters
     * 
     * @public
     * @return  void
     * @since   1.0.0
     */
    public function hooks_and_filters() 
    {
        add_action( 'init', array( 'WPEC_QP_Gateway', 'callback' ) );
        add_action( 'wp_ajax_quickpay_manual_transaction_actions', array( 'WPEC_QP_Gateway', 'ajax_manual_transaction_actions' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'static_files_admin' ) );
    }
    
    
    /**
     * Checks for file availability and requires the file if it exists.
     * 
     * @private 
     * @param  string $file_path > the file to require.
     * @return boolean/void > returns FALSE if the requested file doesn't exist. Return void otherwise. 
     */
    private function require_file( $file_path )
    {
        $dir_path = plugin_dir_path( __FILE__ );
        
        if( ! file_exists( $dir_path . $file_path ) ) 
        {
            return FALSE;
        }
        
        require_once( $dir_path . $file_path );
    }
     
                   
    /**
     * Enqueue static css/js in the admin area
     * 
     * @public
     * @return void
     */
    public function static_files_admin()
    {
        wp_register_style( 'wpec-quickpay-admin', plugins_url( '/assets/css/admin.css', __FILE__ ), FALSE, '1.0.0' );
        wp_enqueue_style( 'wpec-quickpay-admin' );
        
	    wp_enqueue_script( 'wpec-quickpay-admin', plugins_url( '/assets/js/admin.js', __FILE__ ), array( 'jquery' ) );
	    wp_localize_script( 'wpec-quickpay-admin', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
    }
    
    
    /**
     * Prepares the gateway
     * 
     * @public
     * @return void
     */
    public function setup_gateway()
    {
        global $nzshpcrt_gateways;
        $num = time();
        $nzshpcrt_gateways[$num]['name']            = 'Quickpay';
        $nzshpcrt_gateways[$num]['internalname']    = 'Quickpay';

        $nzshpcrt_gateways[$num]['function']        = 'wpec_quickpay_gateway';
        $nzshpcrt_gateways[$num]['form']            = 'wpec_quickpay_gateway_form';
        $nzshpcrt_gateways[$num]['submit_function'] = 'wpec_quickpay_gateway_submit';
    }
}
   
if( ! function_exists( 'WPEC_QP' ) ) {
    function WPEC_QP()
    {
        return WPEC_Quickpay::get_instance();
    }

    WPEC_QP();
}
?>