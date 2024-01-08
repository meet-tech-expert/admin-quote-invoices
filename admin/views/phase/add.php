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
$quote_id = '';
$head_text = "Add New Phase";
$result = false;
$gantt = false;
if( isset($_REQUEST['action']) && isset($_REQUEST['id']) && ($_REQUEST['action'] == 'edit' && $_REQUEST['id']!='') && filter_var($_REQUEST['id'], FILTER_VALIDATE_INT) ){
	
	$model    =  new Admin_Quote_Invoices_Model();
	$result   =  $model->edit_phase($_REQUEST['id']);
	if($result){
		$id = $_REQUEST['id'];
		$head_text = "Edit Phase";
		$quote_id  = $result['phase']['quote_id'];
		$gantt = TRUE;
		
	}
}
if( isset($_REQUEST['quote']) && $_REQUEST['quote'] != ''){ 
	$quote_id = $_REQUEST['quote'];
}								
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">

	<?php include_once( AQI_ADMIN_VIEW_PATH.'/notification.php'); 	?>

	<div id="poststuff" class="">
		<div id="post-body">
			<h1 class="wp-heading-inline"><?php esc_html_e($head_text,$this->plugin_name); ?> for <?php esc_html_e('Quote id',$this->plugin_name);echo ' : '.$quote_id; ?></h1>
			
			<?php if($gantt){ ?>
			<a target="_blank" href="admin.php?page=phase&action=add&quote=<?php echo $result['phase']['quote_id'];?>"><?php esc_html_e('Add More Phase for this quote',$this->plugin_name); ?></a>
			&nbsp;&nbsp;&nbsp;<a target="_blank" href="admin.php?page=job-gantt&quote=<?php echo $result['phase']['quote_id'];?>"><?php esc_html_e('View Gantt',$this->plugin_name); ?></a>
			<?php } ?>
			
			<hr class="wp-header-end">
			<div id="post-body-content">
				<form method="post" class="aqi_form" id="quote_form" action="<?php echo str_replace('%7E', '~', $_SERVER['REQUEST_URI']); ?>">
				<input type="hidden" name="edit_id" value="<?php echo $id;?>"/>
				<input type="hidden" name="quote_id" value="<?php echo $quote_id;?>"/>
					<?php
					// this prevent automated script for unwanted spam
					if(function_exists('wp_nonce_field'))
					wp_nonce_field( 'phase_add_action', 'phase_add_nonce_field' );
					?>
                     
					<table class="form-table">
                        
						<tr valign="top">
							<th scope="row">
								<label class="required"><?php esc_html_e("Phase name", $this->plugin_name); ?></label>
							</th>
							<td>
								<input type="text" name="phase_name" value="<?php echo ($result)? $result['phase']['name']:''; ?>" required="" autofocus/>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label class="required"><?php esc_html_e("Expected Start Date", $this->plugin_name); ?></label>
							</th>
							<td>
								<input type="text" name="expected_start_date" class="admin-date" value="<?php echo ($result)? $result['phase']['expected_start_date']:''; ?>" required=""/>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label class="required"><?php esc_html_e("Expected End Date", $this->plugin_name); ?></label>
							</th>
							<td>
								<input type="text" name="expected_end_date" class="admin-date" value="<?php echo ($result)? $result['phase']['expected_end_date']:''; ?>" required=""/>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label class=""><?php esc_html_e("Effective Start Date", $this->plugin_name); ?></label>
							</th>
							<td>
								<input type="text" name="effective_start_date" class="admin-date" value="<?php echo ($result && $result['phase']['effective_start_date']!='0000-00-00')? $result['phase']['effective_start_date']:''; ?>" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label class=""><?php esc_html_e("Effective End Date", $this->plugin_name); ?></label>
							</th>
							<td>
								<input type="text" name="effective_end_date" class="admin-date" value="<?php echo ($result && $result['phase']['effective_end_date']!='0000-00-00')? $result['phase']['effective_end_date']:''; ?>" onchange="validate_this();" />
							</td>
						</tr>
						
						<tr valign="top">
							<th scope="row">
								<label class=""></label>
							</th>
							<td>
								<button class="button" id="add_new_checklist" type="button"><?php esc_html_e("Add New Checklist", $this->plugin_name); ?></button>
							</td>
						</tr>
						
						</table>
						
						<table class="form-table" id="checklist">
						<?php 
						if($result){
							$data = json_decode($result['phase']['checklists'],true);
							//print_r($data);
							foreach($data as $val){
							foreach($val as $flag => $check){
						 ?>
						 <tr valign="top">
							<th scope="row">
								<label class=""></label>
							</th>
							<td>
								<input type="checkbox" class="flags" name="checkbox[]" <?php if($flag=='yes'){ echo "checked";} ?> />
								<input type="text" name="checklist[]" value="<?php echo $check; ?>" required />
								<span title="Rimuovere" class="dashicons dashicons-no remove-checklist"></span>
							</td>
						 </tr>
						 <?php }}}
						 ?>
						</table>

					<div style="clear: both;">
						<p class="submit">
							<input type="submit" name="submit_page" class="button button-primary button-large" value="<?php _e('Save') ?>" />
							<?php if($result){?>
							<input type="submit" name="delete_phase" class="button button-danger button-large" value="<?php _e('Delete') ?>" style="background: #a00;color: #fff;" onclick="return confirm('Sei sicuro di voler cancellare il record?');" />
							<?php }?>
						</p>
					</div>		
				</form>
			</div>
		</div>
	</div>        
</div>