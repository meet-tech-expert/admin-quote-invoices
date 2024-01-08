<?php
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class Quote_List_Table extends WP_List_Table {
	
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
		$search_quote   = ( isset( $_REQUEST['filter_quote'] ) ) ? $_REQUEST['filter_quote'] : false;
		$search_customer = ( isset( $_REQUEST['filter_customer'] ) ) ? $_REQUEST['filter_customer'] : false;
		$search_month = ( isset( $_REQUEST['m'] ) ) ? $_REQUEST['m'] : false;
	 	//var_dump($search);
		$sql = "SELECT t1.* , (SELECT GROUP_CONCAT(t2.attach_id) FROM {$wpdb->prefix}admin_attachments t2 WHERE t2.post_id = t1.id AND t2.post_type='quote') AS attachments  FROM {$wpdb->prefix}admin_quotes t1 ";
		$where = ' WHERE 1';
		if($search_quote){
			$where .= " AND object LIKE '%".$search_quote."%' ";
		}
		if($search_customer){
			$search_customer = (preg_match("/^".$search_customer."/", "mario"))?'1':'2';
			
			$where .= " AND customer = '".$search_customer."' ";
		}
		if($search_month){
			$where .= " AND CONCAT(YEAR( creation_date ),'',MONTH( creation_date )) = '$search_month' ";
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

		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}admin_quotes";

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
			"{$wpdb->prefix}admin_quotes",
			[ 'id' => $id ],
			[ '%d' ]
		);
		
		$wpdb->delete(
			"{$wpdb->prefix}admin_attachments",
			[ 'post_id' => $id,'post_type' => 'quote' ],
			[ '%d','%s' ]
		);
		$wpdb->delete(
			"{$wpdb->prefix}admin_quote_invoice_products",
			[ 'post_id' => $id,'post_type' => 'quote' ],
			[ '%d','%s' ]
		);
		$wpdb->delete(
			"{$wpdb->prefix}admin_phases",
			[ 'quote_id' => $id ],
			[ '%d']
		);
	}

  function no_items() {
    _e( 'No records found' ,$this->plugin_name);
  }

  function column_default( $item, $column_name ) {
    switch( $column_name ) { 
        case 'object':
        case 'serial_code':
        case 'customer':
        case 'creation_date':
        case 'accepted_date':
        case 'attachments':
        case 'gantt':
            return $item[ $column_name ];
        default:
            return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
    }
  }

	function get_sortable_columns() {
	  $sortable_columns = array(
	    'object'  		 => array('object',false),
	    'serial_code'  	 => array('serial_code',false),
	    'accepted_date'  => array('accepted_date',false),
	  );
	  return $sortable_columns;
	}

	function get_columns(){
	        $columns = array(
	            'cb'        	=> '<input type="checkbox" />',
	            'object' 		=> __( 'Object', $this->plugin_name),
	            'serial_code'   => __( 'Serial code', $this->plugin_name ),
	            'customer' 		=> __( 'Customer', $this->plugin_name),
	            'creation_date' => __( 'DataEmission', $this->plugin_name ),
	            'accepted_date' => __( 'DateAccept', $this->plugin_name ),
	            'attachments'   => __( 'Attachments', $this->plugin_name ),
	            'gantt'		    => __( 'Gantt', $this->plugin_name ),
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

	function column_object($item){
		$delete_nonce = wp_create_nonce( 'quote_delete_record' );
	  $actions = array(
	            'edit'      => sprintf('<a href="?page=%s&action=%s&id=%s">modificare</a>','quotes','edit',$item['id']),
	            'delete' => sprintf( '<a class="delete_record" href="?page=%s&action=%s&quoteId=%s&_wpnonce=%s">Elimina</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce ),
	        );

	  return sprintf('%1$s %2$s', $item['object'], $this->row_actions($actions) );
	}
	
	function column_customer($item){
		/*$author_obj = get_user_by('id', $item['customer']);
	  return '<a href="user-edit.php?user_id='.$item['customer'].'">'.$author_obj->user_login.'</a>';*/
	  $customer = array('1' => 'Mario','2'=>'David');
	  return $customer[$item['customer']];
	}
	function column_creation_date($item){
		
	  return $item['creation_date'];
	}
	function column_attachments($item){
	  $data = '';	
	  if(!is_null($item['attachments'])){
	  	$attach_arr = explode(',' ,$item['attachments']);
	  	$data =  "<ul>";
	  	foreach($attach_arr as $attach_id){
	  		$url = wp_get_attachment_url( $attach_id );
			$data .= "<li>";
			$data .= "<a target='_blank' href='".$url."'>".pathinfo($url)['filename'].'.'.pathinfo($url)['extension']."</a>";
			$data .= "</li>";
		}
		$data .= "</ul>";
	  }	
	  return $data;
	}
	function column_gantt($item){
		global $wpdb;
		$phaseTable = $wpdb->prefix.'admin_phases';
		$settings_lists = $wpdb->get_results("SELECT quote_id FROM $phaseTable WHERE quote_id = '".$item['id']."' ",ARRAY_A);
		if($wpdb->num_rows > 0){
			return sprintf('<a href="?page=%s&action=add&quote=%s">Add Phase</a>','phase',$item['id']) .' '. sprintf('<a target="_blank" href="?page=%s&quote=%s">View Gantt</a>','job-gantt',$item['id']);
		}
	    return sprintf('<a href="?page=%s&action=add&quote=%s">Add Phase</a>','phase',$item['id']);
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
			if ( ! wp_verify_nonce( $nonce, 'quote_delete_record' ) ) {
				die( 'Go get a life script kiddies' );
			}
			else {
				self::delete_record( absint( $_GET['quoteId'] ) );
				 //$this->custom_add_notice(__("Record deleted Successfully" ,$this->plugin_name),'note');
             	 wp_redirect(admin_url("admin.php?page=quotes"));
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

			 //$this->custom_add_notice( __("Record deleted Successfully" ,$this->plugin_name),'note');
			 wp_redirect(admin_url("admin.php?page=quotes"));
			
		}
	}
	function extra_tablenav( $which ) {
	    if ( $which == "top" ){
	        ?>
	        <!--<form method="get" action="">-->
	        <div class="alignleft actions bulkactions">
	         <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
	       		<input type="search" id="quote-search-input" name="filter_quote" value="<?php echo ( isset( $_REQUEST['filter_quote'] ) ) ? $_REQUEST['filter_quote'] : '';?>" placeholder="<?php esc_html_e('Filter Quote',$this->plugin_name)?>">
					
	        </div>
	        <div class="alignleft actions bulkactions">
	       		<input type="search" id="client-search-input" name="filter_customer" value="<?php echo ( isset( $_REQUEST['filter_customer'] ) ) ? $_REQUEST['filter_customer'] : '';?>" placeholder="<?php esc_html_e('Filter Customer',$this->plugin_name)?>">
			
	        </div>
	         <div class="alignleft actions bulkactions">
	        	<?php 
	        	global $wpdb, $wp_locale;
	        	$months = $wpdb->get_results("SELECT DISTINCT YEAR( creation_date ) AS year, MONTH( creation_date ) AS month FROM {$wpdb->prefix}admin_quotes ORDER BY creation_date DESC");
	        	
	        	$month_count = count( $months );

			    if ( !$month_count || ( 1 == $month_count && 0 == $months[0]->month ) )
			      return;
			      
	        	$m = isset( $_GET['m'] ) ? (int) $_GET['m'] : 0;
	        	?>
	        	<label for="filter-by-date" class="screen-reader-text"><?php _e( 'Filter by date' ); ?></label>
				<select name="m" id="filter-by-date">
				<option<?php selected( $m, 0 ); ?> value="0"><?php _e( 'All dates' ); ?></option>
				<?php
			    foreach ( $months as $arc_row ) {
			      if ( 0 == $arc_row->year )
			        continue;

			      $month = zeroise( $arc_row->month, 1 );
			      $year = $arc_row->year;

			      printf( "<option %s value='%s'>%s</option>\n",
			        selected( $m, $year . $month, false ),
			        esc_attr( $arc_row->year . $month ),
			        /* translators: 1: month name, 2: 4-digit year */
			        sprintf( __( '%1$s %2$d' ), $wp_locale->get_month( $month ), $year )
			      );
			    }
				?>
				</select>
				<input type="submit" id="search-submit2" class="button" value="<?php esc_html_e('Filter')?>">
			
	        </div>
	        <!--</form>	-->
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

