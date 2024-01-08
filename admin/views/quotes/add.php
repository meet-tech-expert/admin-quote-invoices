<?php

/**
* Provide a admin area view for the plugin
*
* This file is used to markup the admin-facing aspects of the plugin.
*
* @link       https://www.upwork.com/o/profiles/users/_~01b8271e5700438133/
* @since      1.0.0
*
* @package    Admin_Quote_Invoices
* @subpackage Admin_Quote_Invoices/admin/partials
*/
$id = '0';
$head_text = "Add New Quote";
$result = false;
if( isset($_REQUEST['action']) && isset($_REQUEST['id']) && ($_REQUEST['action'] == 'edit' && $_REQUEST['id']!='') && filter_var($_REQUEST['id'], FILTER_VALIDATE_INT) ){
	
	$model    =  new Admin_Quote_Invoices_Model();
	$result   =  $model->edit_quote($_REQUEST['id']);
	if($result){
		$id = $_REQUEST['id'];
		$head_text = "Edit Quote";
		
	}
}
global $wpdb;
$productsTable = $wpdb->prefix.'admin_products';
$pro_lists 	   = $wpdb->get_results("SELECT id,name,cost FROM $productsTable ",ARRAY_A);
//print_r($pro_lists);
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">

	<?php include_once( AQI_ADMIN_VIEW_PATH.'/notification.php'); 	?>

	<div id="poststuff" class="">
		<div id="post-body">
			<h1 class="wp-heading-inline"><?php esc_html_e($head_text,$this->plugin_name); ?></h1>
			
			<a href="admin.php?page=<?php echo $_REQUEST['page'] ?>"><?php esc_html_e('All Quotes',$this->plugin_name); ?></a>
			
			<hr class="wp-header-end">
			<div id="post-body-content">
				<form method="post" class="aqi_form" id="quote_form" action="<?php echo str_replace('%7E', '~', $_SERVER['REQUEST_URI']); ?>">
				<input type="hidden" name="edit_id" value="<?php echo $id;?>"/>
					<?php
					// this prevent automated script for unwanted spam
					if(function_exists('wp_nonce_field'))
					wp_nonce_field( 'quote_add_action', 'quote_add_nonce_field' );
					?>
                     
					<table class="form-table">
                        
						<tr valign="top">
							<th scope="row">
								<label class="required"><?php esc_html_e("Serial code", $this->plugin_name); ?></label>
							</th>
							<td>
								<input type="text" name="serial_code" value="<?php echo ($result)? $result['quote']['serial_code']:''; ?>" required="" autofocus />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label class="required"><?php esc_html_e("Object", $this->plugin_name); ?></label>
							</th>
							<td>
								<input type="text" name="object" value="<?php echo ($result)? $result['quote']['object']:''; ?>" required=""/>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label class="required"><?php esc_html_e("Customer", $this->plugin_name); ?></label>
							</th>
							<td>
								<select name="customer" required="">
									<option value=""><?php esc_html_e("Select", $this->plugin_name); ?></option>
									<option value="1" <?php echo ($result && $result['quote']['customer']=='1')? 'selected' :''; ?>>Mario</option>
									<option value="2" <?php echo ($result && $result['quote']['customer']=='2')? 'selected' :''; ?>>David</option>
								</select>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label class="required"><?php esc_html_e("Date of creation", $this->plugin_name); ?></label>
							</th>
							<td>
								<input type="text" name="creation_date" class="admin-date" value="<?php echo ($result)? $result['quote']['creation_date']:''; ?>" required=""/>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label class=""><?php esc_html_e("Date of issue", $this->plugin_name); ?></label>
							</th>
							<td>
								<input type="text" name="issue_date" class="admin-date" value="<?php echo ($result)? $result['quote']['issue_date']:''; ?>" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label class=""><?php esc_html_e("Select Product", $this->plugin_name); ?></label>
							</th>
							<td>
								<select name="prod_lists" id="prod_lists">
									<option value=""><?php esc_html_e("Select", $this->plugin_name); ?></option>
									<?php if(!empty($pro_lists) && count($pro_lists)>0 ){
									 foreach($pro_lists as $pro_list){
									 ?>
									 <option data-cost="<?php echo $pro_list['cost']; ?>" value="1"><?php echo $pro_list['name']; ?></option>
									<?php } }?>
								</select>
								<button class="button" id="add_new_product" type="button"><?php esc_html_e("Add New Product", $this->plugin_name); ?></button>
							</td>
						</tr>
					</table>
                        
					<table border="1" class="form-table product-table">
						<thead>
							<th scope="row"><?php esc_html_e("Product name", $this->plugin_name); ?></th>
							<th scope="row"><?php esc_html_e("Cost", $this->plugin_name); ?></th>
							<th scope="row"><?php esc_html_e("Quantity", $this->plugin_name); ?></th>
							<th scope="row"><?php esc_html_e("Taxes", $this->plugin_name); ?>[%]</th>
							<th scope="row"><?php esc_html_e("Total Cost", $this->plugin_name); ?></th>
                          	
						</thead>
						<tbody id="product_body">
						  <?php if( $result && !empty($result['products']) ){
						  		foreach($result['products'] as $key => $p){
						  	 ?>
						  	<tr valign="top">
								<td><input type="text" name="product[]" value="<?php echo $p['name'];?>" required></td>
								<td><input type="number" step="any" class="pro_cost" name="cost[]" value="<?php echo $p['cost'];?>" min="1" required></td>
								<td><input type="number" step="any" class="pro_qty" name="qty[]" value="<?php echo $p['qty'];?>" min="1" required></td>
								<td><input type="number" step="any" class="pro_tax" name="tax[]" value="<?php echo $p['tax'];?>" min="1" required></td>
								<td>
								    <span class="ttl_cost">0</span>
								    
								    <span title="Rimuovere" class="dashicons dashicons-no remove-product"></span>
								</td>
							</tr>	
						  <?php } }else{ ?>
							<tr valign="top">
								<td><input type="text" name="product[]" value="" required></td>
								<td><input type="number" step="any" class="pro_cost" name="cost[]" value="" min="1" required></td>
								<td><input type="number" step="any" class="pro_qty" name="qty[]" value="" min="1" required></td>
								<td><input type="number" step="any" class="pro_tax" name="tax[]" value="" min="1" required></td>
								<td>
								    <span class="ttl_cost">0</span>
								    <span title="Rimuovere" class="dashicons dashicons-no remove-product"></span>
								</td>
							</tr>
							<?php } ?>
						</tbody>
						<tfoot>
                          	
							<th scope="row"></th>
							<th scope="row"><?php esc_html_e("Total Cost", $this->plugin_name); ?>: <span class="final_cost">0</span></th>
							<th scope="row"></th>
							<th scope="row"><?php esc_html_e("Total Taxes", $this->plugin_name); ?>: <span class="final_tax">0</span></th>
							<th scope="row"><?php esc_html_e("Total Sum", $this->plugin_name); ?>: <span class="final_sum">0</span></th>
                          	
						</tfoot>
					</table>
					
					<table class="form-table">
						<tr valign="top">
							<th scope="row">
								<label class=""><?php esc_html_e("Notes", $this->plugin_name); ?></label>
							</th>
							<td>
								<input type="text" name="notes" class="" value="<?php echo ($result)? $result['quote']['notes']:''; ?>"/>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label class=""><?php esc_html_e("Private Notes", $this->plugin_name); ?></label>
							</th>
							<td>
								<input type="text" name="private_notes" class="" value="<?php echo ($result)? $result['quote']['private_notes']:''; ?>"/>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label class=""><?php esc_html_e("Accepted", $this->plugin_name); ?></label>
							</th>
							<td>
								<input type="checkbox" name="accepted" class="" <?php echo ($result && $result['quote']['accepted']=='yes')? 'checked' :'';  ?>  value="1"/>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label class=""><?php esc_html_e("Acceptance date", $this->plugin_name); ?></label>
							</th>
							<td>
								<input type="text" name="accepted_date" class="admin-date" value="<?php echo ($result)? $result['quote']['accepted_date']:''; ?>" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label class=""><?php esc_html_e("Order Confirmation", $this->plugin_name); ?></label>
							</th>
							<td>
								<input type="checkbox" name="order_confirm" class="" <?php echo ($result && $result['quote']['order_confirm']=='yes')? 'checked' :'';  ?> value="1"/>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label class=""><?php esc_html_e("Order Confirmation date", $this->plugin_name); ?></label>
							</th>
							<td>
								<input type="text" name="order_confirm_date" class="admin-date" value="<?php echo ($result)? $result['quote']['order_confirm_date']:''; ?>" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label class=""></label>
							</th>
							<td>
								<button class="button" id="add_new_attach" type="button"><?php esc_html_e("Add New attachment", $this->plugin_name); ?></button>
							</td>
						</tr>

					</table>
					
					<div class="attach-outer-div">
						<ul class="attachments append-attach">
							<?php if( $result && isset($result['attachments']) && !empty($result['attachments']) ){
						  		foreach($result['attachments'] as $key => $at){
						  		 $url = wp_get_attachment_url( $at['attach_id'] );
						  		 if(@exif_imagetype($url)){
								 	$image_url = $url;
								 }else{
								 	$image_url = site_url('wp-includes/images/media/document.png');
								 }
						  	 ?>
						  	 <li class="attachment-li" id="li-<?php echo $at['attach_id'];?>">
							  	 <div class="attachment-preview-div">
							  	 <div class="thumbnail-div">
							  	 <img src="<?php echo $image_url;?>" alt="">
							  	 </div>
							  	 <div class="info">
							  	 <a target="_blank" title="<?php echo pathinfo($url)['filename'].'.'.pathinfo($url)['extension'];?>" href="<?php echo $url;?>" class="file_name">vista</a>
							  	 </div>
							  	 <div class="desc">
							  	 <input name="attach_id[]" type="hidden" value="<?php echo $at['attach_id'];?>" /><input placeholder="Descrizione" name="desc[]" value="<?php echo $at['attach_desc'];?>" type="text"/>
							  	 </div>
							  	 <div class="remove-div">
							  	 <span class="remove_file" data-id="<?php echo $at['attach_id'];?>" >Rimuovere</span>
							  	 </div>
							  	 </div>
						  	 </li>
						  	 
						  	 <?php }} ?>
						</ul>
					</div>


					<div style="clear: both;">
						<p class="submit">
							<input type="submit" name="submit_page" class="button button-primary button-large" value="<?php _e('Save') ?>" />
							<input type="submit" name="generate_invoice" class="button button-primary button-large" value="<?php _e('Save and Generate Invoice') ?>" />
						</p>
					</div>		
				</form>
			</div>
		</div>
	</div>        
</div>