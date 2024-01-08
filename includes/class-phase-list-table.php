<?php
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class Phase_List_Table extends WP_List_Table {
	
	public $plugin_name = 'admin-quote-invoices';
    function __construct(){
    global $status, $page;

        parent::__construct( array(
            'singular'  => __( 'search form', $this->plugin_name ),     //singular name of the listed records
            'plural'    => __( 'search forms', $this->plugin_name ),   //plural name of the listed records
            'ajax'      => false        //does this table support ajax?

    ) );
        //print_r($_REQUEST);
    add_action( 'admin_head', array( &$this, 'admin_header' ) ); 
    
    
    }

  function admin_header() {
   $page = ( isset($_GET['page'] ) ) ? esc_attr( $_GET['page'] ) : false;
    if( 'search-pages' != $page )
    return;
    echo '<style type="text/css">';
    echo '.wp-list-table .column-id { width: 5%; }';
    echo '.wp-list-table .column-booktitle { width: 40%; }';
    echo '.wp-list-table .column-author { width: 35%; }';
    echo '.wp-list-table .column-isbn { width: 20%;}';
    echo '</style>';
  }
  
  public static function get_records( $per_page, $page_number = 1 ) {
  		
		//echo $per_page;
		// now use $per_page to set the number of items displayed
		global $wpdb;
		$search_phase   = ( isset( $_REQUEST['filter_name'] ) ) ? $_REQUEST['filter_name'] : false;
		
		$sql = "SELECT *  FROM {$wpdb->prefix}admin_phases";
		$where = ' WHERE 1';
		
		if($search_phase){
			$where .= " AND name LIKE '%".$search_phase."%' ";
		}
		
        $sql .= $where;
		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}else{
			$sql .= ' ORDER BY add_date DESC';
		}

		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

		//echo $sql;
		$result = $wpdb->get_results( $sql, 'ARRAY_A' );
		

		return $result;
	}
	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public static function record_count() {
		global $wpdb;

		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}admin_phases";

		return $wpdb->get_var( $sql );
	}
	
	/**
	 * Delete a customer record.
	 *
	 * @param int $id customer ID
	 */
	public static function delete_record( $id ) {
		global $wpdb;

		$wpdb->delete(
			"{$wpdb->prefix}admin_phases",
			[ 'id' => $id ],
			[ '%d' ]
		);
	}

  function no_items() {
    _e( 'No records found' ,$this->plugin_name);
  }

  function column_default( $item, $column_name ) {
    switch( $column_name ) { 
        case 'name':
        case 'expected_start_date':
        case 'expected_end_date':
        case 'effective_start_date':
        case 'effective_end_date':
            return $item[ $column_name ];
        default:
            return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
    }
  }

	function get_sortable_columns() {
	  $sortable_columns = array(
	    'name' 				   => array('name',false),
	    'expected_start_date'  => array('expected_start_date',false),
	    'expected_end_date'    => array('expected_end_date',false),
	    'effective_start_date' => array('effective_start_date',false),
	    'effective_end_date'   => array('effective_end_date',false),
	  );
	  return $sortable_columns;
	}

	function get_columns(){
	        $columns = array(
	            'cb'  			      => '<input type="checkbox" />',
	            'name' 				  => __( 'Phase name', $this->plugin_name),
	            'expected_start_date' => __( 'Expected Start Date', $this->plugin_name ),
	            'expected_end_date'   => __( 'Expected End Date', $this->plugin_name),
	            'effective_start_date'=> __( 'Effective Start Date', $this->plugin_name),
	            'effective_end_date'  => __( 'Effective End Date', $this->plugin_name),
	        );
	         return $columns;
	    }

	function usort_reorder( $a, $b ) {
	  // If no sort, default to title
	  $orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'booktitle';
	  // If no order, default to asc
	  $order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'asc';
	  // Determine sort order
	  $result = strcmp( $a[$orderby], $b[$orderby] );
	  // Send final sort direction to usort
	  return ( $order === 'asc' ) ? $result : -$result;
	}

	function column_name($item){
		$delete_nonce = wp_create_nonce( 'phase_delete_record' );
	  $actions = array(
	            'edit'      => sprintf('<a href="?page=%s&action=%s&id=%s">modificare</a>','phase','edit',$item['id']),
	            'delete' => sprintf( '<a class="delete_record" href="?page=%s&action=%s&phaseId=%s&_wpnonce=%s">Elimina</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce ),
	        );

	  return sprintf('%1$s %2$s', $item['name'], $this->row_actions($actions) );
	}
	

	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = [
			'bulk-delete' => 'Delete'
		];

		return $actions;
	}

	function column_cb($item) {
	        return sprintf(
	            '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
	        );    
	    }

	function prepare_items() {
	  $columns  = $this->get_columns();
	  $hidden   = array();
	  $sortable = $this->get_sortable_columns();
	  $this->_column_headers = array( $columns, $hidden, $sortable );
	  /** Process bulk action */
	  $this->process_bulk_action();
	
	  /*$user = get_current_user_id();
	  $screen = get_current_screen();
	  $option = $screen->get_option('per_page', 'option');	 
	  $per_page = get_user_meta($user, $option, true);
	  if ( empty ( $per_page) || $per_page < 1 ) {
		 
		$per_page = $screen->get_option( 'per_page', 'default' );
		 
	  }*/
	  $per_page = 10;

	  $current_page = $this->get_pagenum();
	  $total_items = self::record_count();


	  $this->set_pagination_args( array(
	    'total_items' => $total_items,  //WE have to calculate the total number of items
	    'per_page'    => $per_page     //WE have to determine how many items to show on a page
	  ) );
	  
	  $this->items = self::get_records( $per_page, $current_page );
	}
	
	public function process_bulk_action() {


		//Detect when a bulk action is being triggered...
		if ( 'delete' === $this->current_action() ) {

			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );
			if ( ! wp_verify_nonce( $nonce, 'phase_delete_record' ) ) {
				die( 'Go get a life script kiddies' );
			}
			else {
				self::delete_record( absint( $_GET['phaseId'] ) );
				 //$this->custom_add_notice(__("Record deleted Successfully" ,$this->plugin_name),'note');
             	 wp_redirect(admin_url("admin.php?page=phase"));
			}

		}
		// If the delete bulk action is triggered
		if ( ( isset( $_GET['action'] ) && $_GET['action'] == 'bulk-delete' )
		     || ( isset( $_GET['action2'] ) && $_GET['action2'] == 'bulk-delete' )
		) {

			$delete_ids = esc_sql( $_GET['bulk-delete'] );

			// loop over the array of record IDs and delete them
			foreach ( $delete_ids as $id ) {
				self::delete_record( $id );

			}

			// $this->custom_add_notice( __("Record deleted Successfully" ,$this->plugin_name),'note');
         	 wp_redirect(admin_url("admin.php?page=phase"));
		}
	}
	function extra_tablenav( $which ) {
	    if ( $which == "top" ){
	        ?>
	        <div class="alignleft actions bulkactions">
	         <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
	       		<input type="search" id="quote-search-input" name="filter_name" value="<?php echo ( isset( $_REQUEST['filter_name'] ) ) ? $_REQUEST['filter_name'] : '';?>" placeholder="<?php esc_html_e('Filter Phase name',$this->plugin_name)?>">
			 <input type="submit" id="search-submit2" class="button" value="<?php esc_html_e('Filter')?>">		
	        </div>
	        
	        <?php
	    }
	    if ( $which == "bottom" ){
	        //The code that goes after the table is there

	    }
	}
	public function custom_add_notice($notice, $type = 'error')
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

