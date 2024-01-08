<?php

/**
* Provide a admin area view for the plugin
*
* This file is used to markup the admin-facing aspects of the plugin.
*
* @link       https://www.upwork.com/o/profiles/users/_~01b8271e5700438133/
* @since      1.0.0
*
* @package    Admin_Quote_products
* @subpackage Admin_Quote_products/admin/partials
*/
$id = '0';
$head_text = "Add New Product";
$result = false;
if( isset($_REQUEST['action']) && isset($_REQUEST['id']) && ($_REQUEST['action'] == 'edit' && $_REQUEST['id']!='') && filter_var($_REQUEST['id'], FILTER_VALIDATE_INT) ){
	
	$model    =  new Admin_Quote_Invoices_Model();
	$result   =  $model->edit_product($_REQUEST['id']);
	if($result){
		$id = $_REQUEST['id'];
		$head_text = "Edit Product";
		
	}
}
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">

	<?php include_once( AQI_ADMIN_VIEW_PATH.'/notification.php'); 	?>

	<div id="poststuff" class="">
		<div id="post-body">
			<h1 class="wp-heading-inline"><?php esc_html_e($head_text,$this->plugin_name); ?></h1>
			
			<a href="admin.php?page=<?php echo $_REQUEST['page'] ?>"><?php esc_html_e('All Products',$this->plugin_name); ?></a>
			
			<hr class="wp-header-end">
			<div id="post-body-content">
				<form method="post" class="aqi_form" id="quote_form" action="<?php echo str_replace('%7E', '~', $_SERVER['REQUEST_URI']); ?>">
				<input type="hidden" name="edit_id" value="<?php echo $id;?>"/>
					<?php
					// this prevent automated script for unwanted spam
					if(function_exists('wp_nonce_field'))
					wp_nonce_field( 'product_add_action', 'product_add_nonce_field' );
					?>
                     
					<table class="form-table">
                        
						<tr valign="top">
							<th scope="row">
								<label class="required"><?php esc_html_e("Product name", $this->plugin_name); ?></label>
							</th>
							<td>
								<input type="text" name="product_name" value="<?php echo ($result)? $result['product']['name']:''; ?>" required="" autofocus/>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label class="required"><?php esc_html_e("Code", $this->plugin_name); ?></label>
							</th>
							<td>
								<input type="text" name="code" value="<?php echo ($result)? $result['product']['code']:''; ?>" required=""/>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label class="required"><?php esc_html_e("Cost", $this->plugin_name); ?></label>
							</th>
							<td>
								<input type="number" name="cost" step="any" value="<?php echo ($result)? $result['product']['cost']:''; ?>" required=""/>
							</td>
						</tr>
						
						</table>

					<div style="clear: both;">
						<p class="submit">
							<input type="submit" name="submit_page" class="button button-primary button-large" value="<?php _e('Save') ?>" />
						</p>
					</div>		
				</form>
			</div>
		</div>
	</div>        
</div>