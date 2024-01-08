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
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">

<?php include_once( AQI_ADMIN_VIEW_PATH.'/notification.php'); ?>

	<div id="poststuff" class="">
        <div id="post-body">
          <h1 class="wp-heading-inline"><?php esc_html_e('Manage Invoices',$this->plugin_name); ?></h1>
          
          <a href="<?php echo admin_url('admin.php?page=invoices&action=add');?>" class="page-title-action"><?php esc_html_e('Add New Invoice',$this->plugin_name); ?></a>
          <hr class="wp-header-end">
            <div id="post-body-content">
             <?php
              $myListTable = new Invoice_List_Table();
		      $myListTable->prepare_items(); ?>
              <form method="get">
              	<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
              	<?php $myListTable->display(); ?>
             </form>
            </div>
        </div>
    </div>        
</div>