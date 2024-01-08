<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.upwork.com/o/profiles/users/_~01b8271e5700438133/
 * @since      1.0.0
 *
 * @package    Admin_Quote_Invoices
 * @subpackage Admin_Quote_Invoices/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Admin_Quote_Invoices
 * @subpackage Admin_Quote_Invoices/admin
 * @author     Jyoti M <jmodanwal789@gmail.com>
 */
class Admin_Quote_Invoices_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Admin_Quote_Invoices_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Admin_Quote_Invoices_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_register_style('jquery-ui', 'https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.min.css');
        wp_enqueue_style( 'jquery-ui' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/admin-quote-invoices-admin.css', array(), '', 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Admin_Quote_Invoices_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Admin_Quote_Invoices_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		 wp_enqueue_script( 'jquery-ui-datepicker' ); 
		 wp_enqueue_media();

		 wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/admin-quote-invoice-admin.js', array( 'jquery' ), '2.0', true );

	}
	public function aqi_admin_menu(){
		$aqi_screen_page = add_menu_page( __('Quotes Page',$this->plugin_name), __('Quotes',$this->plugin_name), 'read', 'quotes',array(&$this, 'quotes_action'),'dashicons-admin-site',6);
		
		add_submenu_page( 'quotes', __('Invoice page',$this->plugin_name), __('Invoices',$this->plugin_name), 'read', 'invoices', array(&$this, 'invoices_action') );
		
		add_submenu_page( 'quotes', __('Product page',$this->plugin_name), __('Products',$this->plugin_name), 'read', 'products', array(&$this, 'products_action') );
		
		add_submenu_page( 'quotes', __('Phase Anagraphic page',$this->plugin_name), __('Phase Anagraphic',$this->plugin_name), 'read', 'phase', array(&$this, 'phase_action') );
		
		add_submenu_page( 'quotes', __('Job Gantt page',$this->plugin_name), __('Job Gantt',$this->plugin_name), 'read', 'job-gantt', array(&$this, 'gantt_action') );
		
	}
	
	public function quotes_action(){
		if( isset($_REQUEST['action']) && ($_REQUEST['action'] == 'add' || $_REQUEST['action'] == 'edit')){
			
			if(isset($_POST['submit_page'])) {
			    if ( ! isset( $_POST['quote_add_nonce_field'] ) || ! wp_verify_nonce( $_POST['quote_add_nonce_field'], 'quote_add_action' ) ) {
			    	$this->aqi_add_notice('Spiacente, il tuo nonce non ha verificato.','error');
			    } else {
			    	$model    =  new Admin_Quote_Invoices_Model();
			    	$response =  $model->add_quote();
			    	$this->aqi_add_notice($response['msg'] , $response['status']);
			    	if($_REQUEST['action'] == 'add'){
			    	$url = admin_url("admin.php?page=quotes&action=edit&id=".$response['quote_id']);
			    	print('<script>window.location.href="'.$url.'"</script>');
			    	}

			    }
			} 
			if(isset($_POST['generate_invoice'])) {
			    if ( ! isset( $_POST['quote_add_nonce_field'] ) || ! wp_verify_nonce( $_POST['quote_add_nonce_field'], 'quote_add_action' ) ) {
			    	$this->aqi_add_notice('Spiacente, il tuo nonce non ha verificato.','error');
			    } else {
			    	$model    =  new Admin_Quote_Invoices_Model();
			    	$response =  $model->generate_invoice();
			    	//print_r($response);exit;
			    	$this->aqi_add_notice($response['msg'] , $response['status']);
			    	$url = admin_url("admin.php?page=invoices&action=edit&id=".$response['invoice_id']);
			    	print('<script>window.location.href="'.$url.'"</script>');
			    }
			}    	
			
			include_once 'views/quotes/add.php';
		}else{
			include_once 'views/quotes/lists.php';
		}
	}
	public function invoices_action(){
		if( isset($_REQUEST['action']) && ($_REQUEST['action'] == 'add' || $_REQUEST['action'] == 'edit')){
			
			if(isset($_POST['submit_page'])) {
			    if ( ! isset( $_POST['invoice_add_nonce_field'] ) || ! wp_verify_nonce( $_POST['invoice_add_nonce_field'], 'invoice_add_action' ) ) {
			    	$this->aqi_add_notice('Spiacente, il tuo nonce non ha verificato.','error');
			    } else {
			    	$model    =  new Admin_Quote_Invoices_Model();
			    	$response =  $model->add_invoice();
			    	$this->aqi_add_notice($response['msg'] , $response['status']);
			    	if($_REQUEST['action'] == 'add'){
			    	$url = admin_url("admin.php?page=invoices&action=edit&id=".$response['invoice_id']);
			    	print('<script>window.location.href="'.$url.'"</script>');
			    	}
			    }
			} 
			if(isset($_POST['issue_invoice'])) {
			    if ( ! isset( $_POST['invoice_add_nonce_field'] ) || ! wp_verify_nonce( $_POST['invoice_add_nonce_field'], 'invoice_add_action' ) ) {
			    	$this->aqi_add_notice('Spiacente, il tuo nonce non ha verificato.','error');
			    } else {
			    	$model    =  new Admin_Quote_Invoices_Model();
			    	$response =  $model->issue_invoice();
			    	$this->aqi_add_notice($response['msg'] , $response['status']);
			    	$url = admin_url("admin.php?page=invoices&action=edit&id=".$response['invoice_id']);
			    	print('<script>window.location.href="'.$url.'"</script>');
			    }
			}    	
			
			include_once 'views/invoices/add.php';
		}else{
			include_once 'views/invoices/lists.php';
		}
	}
	public function products_action(){
		if( isset($_REQUEST['action']) && ($_REQUEST['action'] == 'add' || $_REQUEST['action'] == 'edit')){
			
			if(isset($_POST['submit_page'])) {
			    if ( ! isset( $_POST['product_add_nonce_field'] ) || ! wp_verify_nonce( $_POST['product_add_nonce_field'], 'product_add_action' ) ) {
			    	$this->aqi_add_notice('Spiacente, il tuo nonce non ha verificato.','error');
			    } else {
			    	$model    =  new Admin_Quote_Invoices_Model();
			    	$response =  $model->add_product();
			    	$this->aqi_add_notice($response['msg'] , $response['status']);
			    }
			}    	
			
			include_once 'views/products/add.php';
		}else{
			include_once 'views/products/lists.php';
		}
	}
	public function phase_action(){
		if( isset($_REQUEST['action']) && ($_REQUEST['action'] == 'add' || $_REQUEST['action'] == 'edit')){
			
			if(isset($_POST['submit_page'])) {
			    if ( ! isset( $_POST['phase_add_nonce_field'] ) || ! wp_verify_nonce( $_POST['phase_add_nonce_field'], 'phase_add_action' ) ) {
			    	$this->aqi_add_notice('Spiacente, il tuo nonce non ha verificato.','error');
			    } else {
			    	$model    =  new Admin_Quote_Invoices_Model();
			    	$response =  $model->add_phase();
			    	$this->aqi_add_notice($response['msg'] , $response['status']);
			    	if($_REQUEST['action'] == 'add'){
			    	$url = admin_url("admin.php?page=phase&action=edit&id=".$response['phase_id']);
			    	print('<script>window.location.href="'.$url.'"</script>');
			    	}
			    }
			}elseif(isset($_POST['delete_phase'])) {
				$model    =  new Admin_Quote_Invoices_Model();
		    	$response =  $model->delete_phase($_POST['edit_id']);
		    	$this->aqi_add_notice($response['msg'] , $response['status']);
		    	$url = admin_url("admin.php?page=job-gantt&quote=".$_POST['quote_id']);
			    print('<script>window.location.href="'.$url.'"</script>');
			}    	
			
			include_once 'views/phase/add.php';
		}else{
			include_once 'views/phase/lists.php';
		}
	}
	public function gantt_action(){
		include_once 'views/gantt/chart.php';
	}
	public function settings_action(){
		if( isset($_REQUEST['action']) && ($_REQUEST['action'] == 'add' || $_REQUEST['action'] == 'edit')){
			
			if(isset($_POST['submit_page'])) {
			    if ( ! isset( $_POST['settings_add_nonce_field'] ) || ! wp_verify_nonce( $_POST['settings_add_nonce_field'], 'settings_add_action' ) ) {
			    	$this->aqi_add_notice('Spiacente, il tuo nonce non ha verificato.','error');
			    } else {
			    	$model    =  new Admin_Quote_Invoices_Model();
			    	$response =  $model->add_settings();
			    	$this->aqi_add_notice($response['msg'] , $response['status']);
			    	if($_REQUEST['action'] == 'add'){
			    	$url = admin_url("admin.php?page=settings&action=edit&id=".$response['settings_id']);
			    	print('<script>window.location.href="'.$url.'"</script>');
			    	}
			    }
			}    	
			
			include_once 'views/settings/add.php';
		}else{
			include_once 'views/settings/lists.php';
		}
		
	}
	public function aqi_add_notice($notice, $type = 'error')
	{
		$types = array(
			'error' => 'error',
			'warning' => 'update-nag',
			'info' => 'check-column',
			'note' => 'updated',
			'none' => '',
		);
		if (!array_key_exists($type, $types))
			$type = 'none';

		$notice_data = array('class' => $types[$type], 'message' => $notice);

		$key = 'aqi_admin_notices_' . get_current_user_id();
		$notices = get_transient($key);

		if (FALSE === $notices)
			$notices = array($notice_data);

		// only add the message if it's not already there
		$found = FALSE;
		foreach ($notices as $notice) {
			if ($notice_data['message'] === $notice['message'])
				$found = TRUE;
		}
		if (!$found)
			$notices[] = $notice_data;

		set_transient($key, $notices, 3600);
	}

}
