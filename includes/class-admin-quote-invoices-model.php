<?php
class Admin_Quote_Invoices_Model {

	public static $quotes_table 		= 'admin_quotes';
	public static $attachments_table 	= 'admin_attachments';
	public static $quote_products_table = 'admin_quote_invoice_products';
	public static $products_table   	= 'admin_products';
	
	public static $invoices_table 		= 'admin_invoices';
	public static $phases_table 		= 'admin_phases';
	public static $settings_table 		= 'admin_settings';
	
	public function add_quote(){
		global $wpdb;
		$quoteTable = $wpdb->prefix. self::$quotes_table;
		
		$code   		= sanitize_text_field($_POST['serial_code']);
		$object 		= sanitize_text_field($_POST['object']);
		$customer      	= sanitize_text_field($_POST['customer']);
		$creation_date  = sanitize_text_field($_POST['creation_date']);
		$issue_date     = sanitize_text_field($_POST['issue_date']);
		$notes		    = sanitize_text_field($_POST['notes']);
		$private_notes  = sanitize_text_field($_POST['private_notes']);
		$accepted       = (array_key_exists("accepted",$_POST))?'yes':'no';
		$accepted_date  = sanitize_text_field($_POST['accepted_date']);
		$order_confirm  = (array_key_exists("order_confirm",$_POST))?'yes':'no';
		$order_confirm_date  = sanitize_text_field($_POST['order_confirm_date']);
		
		$insertData = array(
			'serial_code'  			=> $code,
			'object' 				=> $object,
			'customer' 				=> $customer,
			'creation_date'     	=> $creation_date,
			'issue_date'  		   	=> $issue_date,
			'notes'  		   		=> $notes,
			'private_notes'  		=> $private_notes,
			'accepted'  			=> $accepted,
			'accepted_date'  		=> $accepted_date,
			'order_confirm'  		=> $order_confirm,
			'order_confirm_date'  	=> $order_confirm_date,
		);
		$attch_exist   =  array_key_exists("attach_id",$_POST);
		
		$product_exist =  array_key_exists("product",$_POST);
		
		$edit_id = $_POST['edit_id'];
		if($edit_id != '0'){
			
				$wpdb->update($quoteTable,$insertData ,array('id' => $edit_id));
				
				if($product_exist)$this->insert_products($edit_id , $_POST ,'quote');
				
				if($attch_exist)$this->insert_attachments($edit_id , $_POST,'quote');
				
				return array('status' => 'note','quote_id'=>$edit_id,'msg'=> 'Aggiornato con successo');
		
			
		}else{
		
			if($wpdb->insert($quoteTable,$insertData)){
				$quote_id = $wpdb->insert_id;
				
				if($product_exist)$this->insert_products($quote_id , $_POST ,'quote');
				
				if($attch_exist)$this->insert_attachments($quote_id , $_POST,'quote');
				
				return array('status' => 'note','quote_id'=>$quote_id, 'msg'=> 'Salvato con successo');
			}else{
				return array('status' => 'error','msg'=> 'Modulo di richiesta non salvato. '.$wpdb->last_error);
			}
		}
		
	}
	public function insert_products($post_id,$postData,$post_type){
		global $wpdb;
		$quoteProductTable = $wpdb->prefix. self::$quote_products_table;
		
		$this->delete($quoteProductTable ,array('post_id'=> $post_id,'post_type'=> $post_type));
		
		$n = count($postData['product']);
		for($i=0; $i < $n; $i++ ){
			$data = array(
				'post_id' 		=> $post_id,
				'post_type'		=> $post_type,
				'name'			=> sanitize_text_field($postData['product'][$i]),
				'cost'			=> $postData['cost'][$i],
				'qty'			=> $postData['qty'][$i],
				'tax'			=> $postData['tax'][$i],
			);
			
			$wpdb->insert($quoteProductTable , $data);
		}
	}
	public function insert_attachments($post_id,$postData,$post_type){
		global $wpdb;
		$attachmentsTable = $wpdb->prefix. self::$attachments_table;
		
		$this->delete($attachmentsTable ,array('post_id'=> $post_id,'post_type'=> $post_type));
		
		$n = count($postData['attach_id']);
		for($i=0; $i < $n; $i++ ){
			$data = array(
				'post_id' 		=> $post_id,
				'post_type'		=> $post_type,
				'attach_id'		=> $postData['attach_id'][$i],
				'attach_desc'	=> sanitize_text_field($postData['desc'][$i])
			);
			
			$wpdb->insert($attachmentsTable , $data);
		}
		
	}
	public function edit_quote($id){
		global $wpdb;
		$quoteTable 	   = $wpdb->prefix. self::$quotes_table;
		$attachmentsTable  = $wpdb->prefix. self::$attachments_table;
		$quoteProductTable = $wpdb->prefix. self::$quote_products_table;
	    $result = array();
	    $sql = "SELECT * FROM $quoteTable WHERE id='$id'";
	    $quotes = $wpdb->get_results( $sql, ARRAY_A );
	    if($wpdb->num_rows > 0){
	    	$result['quote'] = $quotes[0]; 
	    	
	    	$pro_sql    = "SELECT name,cost,qty,tax FROM $quoteProductTable WHERE post_id='$id' AND post_type='quote'";
	    	$pro_result = $wpdb->get_results( $pro_sql, ARRAY_A );
	    	if($wpdb->num_rows > 0){
	    		$result['products'] = $pro_result;
	    	}	
	    	
	    	$attach_sql    = "SELECT attach_id,attach_desc FROM $attachmentsTable WHERE post_id='$id' AND post_type='quote'";
	    	$att_result = $wpdb->get_results( $attach_sql, ARRAY_A );
	    	if($wpdb->num_rows > 0){
	    		$result['attachments'] = $att_result;
	    	}	
	    	
			return $result;
		}
	    return false;
	}
	public function add_invoice(){
		global $wpdb;
		$invoiceTable = $wpdb->prefix. self::$invoices_table;
		
		$code   		= sanitize_text_field($_POST['serial_code']);
		$object 		= sanitize_text_field($_POST['object']);
		$customer      	= sanitize_text_field($_POST['customer']);
		$issue_date     = sanitize_text_field($_POST['issue_date']);
		$notes		    = sanitize_text_field($_POST['notes']);
		$private_notes  = sanitize_text_field($_POST['private_notes']);
		$paid		    = (array_key_exists("paid",$_POST))?'yes':'no';
		$pay_due_date   = (array_key_exists("pay_due_date",$_POST))?sanitize_text_field($_POST['pay_due_date']):'0000-00-00';
		$effective_pay_date  = (array_key_exists("pay_due_date",$_POST))?sanitize_text_field($_POST['effective_pay_date']):'0000-00-00';
		
		$issue_invoice = (array_key_exists("issue_invoice",$_POST))? TRUE: FALSE;
		
		$issue_Invoice = 'no';
		
		if($issue_invoice){
			$res = $this->generate_serial($code ,$issue_date ,$invoiceTable);
			/*if($res['status'] == 'error'){
				return $res;
			}*/
			$code = $res;
			$issue_Invoice = 'yes';
		}
		
		$insertData = array(
			'serial_code'  			=> $code,
			'object' 				=> $object,
			'customer' 				=> $customer,
			'issue_date'  		   	=> $issue_date,
			'issue_invoice'  		=> $issue_Invoice,
			'notes'  		   		=> $notes,
			'private_notes'  		=> $private_notes,
			'paid' 		 			=> $paid,
			'pay_due_date'  		=> $pay_due_date,
			'effective_pay_date'  	=> $effective_pay_date,
		);
		$attch_exist   =  array_key_exists("attach_id",$_POST);
		
		$product_exist =  array_key_exists("product",$_POST);
		
		$edit_id = $_POST['edit_id'];
		if($edit_id != '0'){
			
				if($product_exist)$this->insert_products($edit_id , $_POST ,'invoice');
					
				if($attch_exist)$this->insert_attachments($edit_id , $_POST,'invoice');
				
				if($wpdb->update($invoiceTable,$insertData ,array('id' => $edit_id))){
					
					return array('status' => 'note','invoice_id'=>$edit_id,'msg'=> 'Aggiornato con successo');	
				}else{
					if($wpdb->last_error!='')return array('status' => 'error','invoice_id'=>$edit_id,'msg'=> 'Modulo di richiesta non salvato. '.$wpdb->last_error);
				}
				
			
		}else{
		
			if($wpdb->insert($invoiceTable,$insertData)){
				$invoice_id = $wpdb->insert_id;
				
				if($product_exist)$this->insert_products($invoice_id , $_POST ,'invoice');
				
				if($attch_exist)$this->insert_attachments($invoice_id , $_POST,'invoice');
				
				return array('status' => 'note','invoice_id'=>$invoice_id,'msg'=> 'Salvato con successo');
			}else{
				return array('status' => 'error','msg'=> 'Modulo di richiesta non salvato. '.$wpdb->last_error);
			}
		}
		
	}
	private function generate_serial($code,$issue_date,$invoiceTable){
		global $wpdb;
		$year = date("Y");
		$check = strpos($code, $year);
		//if ($check !== false) {
		   $lastStr = substr($code ,4,7);
		   //$sql = $wpdb->get_results("SELECT serial_code FROM $invoiceTable WHERE serial_code = '$code'");
		  // if($wpdb->num_rows == '0'){
		   	$serialCode = $wpdb->get_results("SELECT MAX(serial_code) AS serialCode FROM $invoiceTable WHERE issue_Invoice = 'yes'");
		   	$serialCode = $serialCode[0]->serialCode;
		   	if(is_null($serialCode)){
				$serialCode = $year.'001';
			}else{
				$serialCode = ++$serialCode;
			}
		   	return $serialCode;
		   	
		   /*}else{
		   	return array('status' => 'error','msg'=> 'Errore: questo codice seriale è già presente. per favore provane un altro');
		   }*/
		    
		/*}else{
			return array('status' => 'error','msg'=> 'Errore: il formato seriale è sbagliato');
		}*/
		
		
	}
	public function edit_invoice($id){
		global $wpdb;
		$invoiceTable 	   = $wpdb->prefix. self::$invoices_table;
		$attachmentsTable  = $wpdb->prefix. self::$attachments_table;
		$quoteProductTable = $wpdb->prefix. self::$quote_products_table;
	    $result = array();
	    $sql      = "SELECT * FROM $invoiceTable WHERE id='$id'";
	    $invoices = $wpdb->get_results( $sql, ARRAY_A );
	    if($wpdb->num_rows > 0){
	    	$result['invoice'] = $invoices[0]; 
	    	
	    	$pro_sql    = "SELECT name,cost,qty,tax FROM $quoteProductTable WHERE post_id='$id' AND post_type='invoice'";
	    	$pro_result = $wpdb->get_results( $pro_sql, ARRAY_A );
	    	if($wpdb->num_rows > 0){
	    		$result['products'] = $pro_result;
	    	}	
	    	
	    	$attach_sql    = "SELECT attach_id,attach_desc FROM $attachmentsTable WHERE post_id='$id' AND post_type='invoice'";
	    	$att_result = $wpdb->get_results( $attach_sql, ARRAY_A );
	    	if($wpdb->num_rows > 0){
	    		$result['attachments'] = $att_result;
	    	}	
	    	
			return $result;
		}
	    return false;
	}
	public function add_product(){
		global $wpdb;
		$productTable = $wpdb->prefix. self::$products_table;
		
		$name   		= sanitize_text_field($_POST['product_name']);
		$code 			= sanitize_text_field($_POST['code']);
		$cost      		= sanitize_text_field($_POST['cost']);
		
		$insertData = array(
			'name'  => $name,
			'code' 	=> $code,
			'cost' 	=> $cost,
		);
		
		$edit_id = $_POST['edit_id'];
		if($edit_id != '0'){
			
				$wpdb->update($productTable,$insertData ,array('id' => $edit_id));
		
				return array('status' => 'note','msg'=> 'Aggiornato con successo');
		
		}else{
			
			if($wpdb->insert($productTable,$insertData)){
				$product_id = $wpdb->insert_id;
				
				return array('status' => 'note','msg'=> 'Salvato con successo');
			}else{
				return array('status' => 'error','msg'=> 'Modulo di richiesta non salvato. '.$wpdb->last_error);
			}
		}
		
	}
	public function edit_product($id){
		global $wpdb;
		$productTable = $wpdb->prefix. self::$products_table;
		
	    $result = array();
	    $sql      = "SELECT * FROM $productTable WHERE id='$id'";
	    $products = $wpdb->get_results( $sql, ARRAY_A );
	    if($wpdb->num_rows > 0){
	    	$result['product'] = $products[0]; 	
	    	
			return $result;
		}
	    return false;
	}
	
	public function generate_invoice(){
		$response = $this->add_quote();
		if($response['status'] != 'error'){
			$_POST['edit_id'] = '0';
			$invoice_response = $this->add_invoice();
			//print_r($invoice_response);
			return $invoice_response;
			
		}else{
			return $response;
		}
	}
	public function issue_invoice(){
		/*echo "<pre>";
		print_r($_POST);
		echo "</pre>";exit;*/
		return $response = $this->add_invoice();
		
	}
	public function add_phase(){
		/*echo "<pre>";
		print_r($_POST);
		echo "</pre>";*/
		global $wpdb;
		$phaseTable = $wpdb->prefix. self::$phases_table;
		$checklist_data = array();
		
		$quote_id 		= sanitize_text_field($_POST['quote_id']);
		$name   		= sanitize_text_field($_POST['phase_name']);
		$ex_start_date	= sanitize_text_field($_POST['expected_start_date']);
		$ex_end_date	= sanitize_text_field($_POST['expected_end_date']);
		$ef_start_date	= sanitize_text_field($_POST['effective_start_date']);
		$ef_end_date	= sanitize_text_field($_POST['effective_end_date']);
		
		$checklist 	    = (array_key_exists("checklist",$_POST))?$_POST['checklist']:false;
		$checkbox 	    = (array_key_exists("checkbox",$_POST))?$_POST['checkbox']:array();
		
		if($checklist && !empty($checklist)){
			foreach($checklist as $key => $checkVal){
				
				$check = (array_key_exists($key , $checkbox))?'yes':'no';
				$checklist_data[] = array($check => $checkVal);
			}
		}
		/*print_r($checklist_data);
		exit;*/
		$insertData = array(
			'quote_id'  			=> $quote_id,
			'name'  				=> $name,
			'expected_start_date' 	=> $ex_start_date,
			'expected_end_date' 	=> $ex_end_date,
			'effective_start_date' 	=> $ef_start_date,
			'effective_end_date' 	=> $ef_end_date,
			'checklists'			=> json_encode($checklist_data)
		);
		
		$edit_id = $_POST['edit_id'];
		if($edit_id != '0'){
			
				$wpdb->update($phaseTable,$insertData ,array('id' => $edit_id));
		
				return array('status' => 'note','msg'=> 'Aggiornato con successo');
		
		}else{
			
			if($wpdb->insert($phaseTable,$insertData)){
				$phase_id = $wpdb->insert_id;
				
				return array('status' => 'note','phase_id'=> $phase_id,'msg'=> 'Salvato con successo');
			}else{
				return array('status' => 'error','msg'=> 'Modulo di richiesta non salvato. '.$wpdb->last_error);
			}
		}
		
	}
	public function edit_phase($id){
		global $wpdb;
		$phaseTable = $wpdb->prefix. self::$phases_table;
		
	    $result = array();
	    $sql      = "SELECT * FROM $phaseTable WHERE id='$id'";
	    $products = $wpdb->get_results( $sql, ARRAY_A );
	    if($wpdb->num_rows > 0){
	    	$result['phase'] = $products[0]; 	
	    	
			return $result;
		}
	    return false;
	}
	public function add_settings(){
		global $wpdb;
		$settingsTable = $wpdb->prefix. self::$settings_table;
		
		$quote   		= sanitize_text_field($_POST['quote_lists']);
		$products		= $_POST['prod_lists'];
		$phases   		= $_POST['phase_lists'];
		
		$insertData = array(
			'quote_id'   => $quote,
			'products' 	 => implode(',', $products),
			'phases' 	 => implode(',', $phases),
		);
		
		$edit_id = $_POST['edit_id'];
		if($edit_id != '0'){
			
				$wpdb->update($settingsTable,$insertData ,array('id' => $edit_id));
		
				return array('status' => 'note','msg'=> 'Aggiornato con successo');
		
		}else{
			
			if($wpdb->insert($settingsTable,$insertData)){
				$settings_id = $wpdb->insert_id;
				
				return array('status' => 'note','settings_id'=>$settings_id,'msg'=> 'Salvato con successo');
			}else{
				return array('status' => 'error','msg'=> 'Modulo di richiesta non salvato. '.$wpdb->last_error);
			}
		}
		
	}
	public function edit_settings($id){
		global $wpdb;
		$settingsTable = $wpdb->prefix. self::$settings_table;
		
	    $result = array();
	    $sql      = "SELECT * FROM $settingsTable WHERE id='$id'";
	    $settings = $wpdb->get_results( $sql, ARRAY_A );
	    if($wpdb->num_rows > 0){
	    	$result['settings'] = $settings[0]; 	
	    	
			return $result;
		}
	    return false;
	}
	
	public function delete_phase($phase_id){
		global $wpdb;
		$phaseTable = $wpdb->prefix. self::$phases_table;
		$wpdb->delete($phaseTable,array('id' => $phase_id));
		return array('status' => 'note','msg'=> 'Cancellato abilmente');
	}
	

}	