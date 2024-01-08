<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.upwork.com/o/profiles/users/_~01b8271e5700438133/
 * @since      1.0.0
 *
 * @package    Admin_Quote_Invoices
 * @subpackage Admin_Quote_Invoices/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Admin_Quote_Invoices
 * @subpackage Admin_Quote_Invoices/includes
 * @author     Jyoti M <jmodanwal789@gmail.com>
 */
class Admin_Quote_Invoices_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		ob_start();
		global $wpdb;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$tableQuotes               = $wpdb->prefix . 'admin_quotes';
		$tableQuoteInvoiceProducts = $wpdb->prefix . 'admin_quote_invoice_products';
		$tableaAminAttachments     = $wpdb->prefix . 'admin_attachments';
		$tableaAminProducts		   = $wpdb->prefix . 'admin_products';
		
		$tableInvoices             = $wpdb->prefix . 'admin_invoices';
		$tablePhases               = $wpdb->prefix . 'admin_phases';
		
		/* 1. 
         * CREATE Quotes TABLE
         */
        if ($wpdb->get_var("SHOW TABLES LIKE '$tableQuotes'") != $tableQuotes){
            if (!empty($wpdb->charset))
                $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
            if (!empty($wpdb->collate))
                $charset_collate .= " COLLATE $wpdb->collate";
            $sql = "CREATE TABLE IF NOT EXISTS $tableQuotes (
                  `id` 					int(11) NOT NULL AUTO_INCREMENT,
				  `serial_code` 		VARCHAR(255) NOT NULL,
				  `object`      		VARCHAR(255) NOT NULL,
				  `customer`    		int(11) NOT NULL,
				  `creation_date` 		date NOT NULL,
				  `issue_date`	 		date NOT NULL,
				  `notes`				text,
				  `private_notes`		text,
				  `accepted`			ENUM('no','yes') NOT NULL,
				  `accepted_date`		date NOT NULL,
				  `order_confirm`		ENUM('no','yes') NOT NULL,
				  `order_confirm_date`	date NOT NULL,
				  `add_date` 			TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                 PRIMARY KEY (id)) $charset_collate;";
            dbDelta($sql);
		
    	}
    	/* 2. 
         * CREATE admin attachments TABLE
         */
        if ($wpdb->get_var("SHOW TABLES LIKE '$tableQuoteInvoiceProducts'") != $tableQuoteInvoiceProducts){
            if (!empty($wpdb->charset))
                $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
            if (!empty($wpdb->collate))
                $charset_collate .= " COLLATE $wpdb->collate";
            $sql = "CREATE TABLE IF NOT EXISTS $tableQuoteInvoiceProducts (
                  `id` 					int(11) NOT NULL AUTO_INCREMENT,
				  `post_id`	    		int(11) NOT NULL COMMENT 'Quote Id,Invoice Id',
				  `post_type`			ENUM('quote','invoice') NOT NULL,
				  `name`	      		VARCHAR(255) NOT NULL,
				  `cost`	    		FLOAT(10,2) NOT NULL,
				  `qty`	  		  		int(11) NOT NULL,
				  `tax`	    			FLOAT(10,2) NOT NULL,
				  `add_date` 			TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                 PRIMARY KEY (id)) $charset_collate;";
            dbDelta($sql);
    	}
    	/* 3. 
         * CREATE Quote Invoice Products TABLE
         */
        if ($wpdb->get_var("SHOW TABLES LIKE '$tableaAminAttachments'") != $tableaAminAttachments){
            if (!empty($wpdb->charset))
                $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
            if (!empty($wpdb->collate))
                $charset_collate .= " COLLATE $wpdb->collate";
            $sql = "CREATE TABLE IF NOT EXISTS $tableaAminAttachments (
                  `id` 					int(11) NOT NULL AUTO_INCREMENT,
				  `post_id`	    		int(11) NOT NULL COMMENT 'Quote Id,Invoice Id',
				  `post_type`			ENUM('quote','invoice') NOT NULL,
				  `attach_id`	  		int(11) NOT NULL,
				  `attach_desc`      	VARCHAR(255) NOT NULL,
				  `add_date` 			TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                 PRIMARY KEY (id)) $charset_collate;";
            dbDelta($sql);
    	}
    	/* 4. 
         * CREATE Invoices TABLE
         */
        if ($wpdb->get_var("SHOW TABLES LIKE '$tableInvoices'") != $tableInvoices){
            if (!empty($wpdb->charset))
                $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
            if (!empty($wpdb->collate))
                $charset_collate .= " COLLATE $wpdb->collate";
            $sql = "CREATE TABLE IF NOT EXISTS $tableInvoices (
                  `id` 					int(11) NOT NULL AUTO_INCREMENT,
				  `serial_code` 		int(11) NOT NULL,
				  `object`      		VARCHAR(255) NOT NULL,
				  `customer`    		int(11) NOT NULL,
				  `issue_date`	 		date NOT NULL,
				  `issue_invoice`		ENUM('no','yes') NOT NULL,
				  `notes`				text,
				  `private_notes`		text,
				  `paid`				ENUM('no','yes') NOT NULL,
				  `pay_due_date`		date NOT NULL,
				  `effective_pay_date`	date NOT NULL,
				  `add_date` 			TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                 PRIMARY KEY (id)) $charset_collate;";
            dbDelta($sql);
		
    	}
    	/* 5. 
         * CREATE Products TABLE
         */
        if ($wpdb->get_var("SHOW TABLES LIKE '$tableaAminProducts'") != $tableaAminProducts){
            if (!empty($wpdb->charset))
                $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
            if (!empty($wpdb->collate))
                $charset_collate .= " COLLATE $wpdb->collate";
            $sql = "CREATE TABLE IF NOT EXISTS $tableaAminProducts (
                  `id` 					int(11) NOT NULL AUTO_INCREMENT,
				  `name`		 		VARCHAR(255) NOT NULL,
				  `code`	      		VARCHAR(255) NOT NULL,
				  `cost`	    		FLOAT(10,2) NOT NULL,
				  `add_date` 			TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                 PRIMARY KEY (id)) $charset_collate;";
            dbDelta($sql);
		
    	}
    	/* 6. 
         * CREATE Phases TABLE
         */
        if ($wpdb->get_var("SHOW TABLES LIKE '$tablePhases'") != $tablePhases){
            if (!empty($wpdb->charset))
                $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
            if (!empty($wpdb->collate))
                $charset_collate .= " COLLATE $wpdb->collate";
            $sql = "CREATE TABLE IF NOT EXISTS $tablePhases (
                  `id` 						int(11) NOT NULL AUTO_INCREMENT,
                  `quote_id`	 			int(11) NOT NULL,
				  `name`		 			VARCHAR(255) NOT NULL,
				  `expected_start_date`	    date NOT NULL,
				  `expected_end_date`	    date NOT NULL,
				  `effective_start_date`    date NOT NULL,
				  `effective_end_date`	    date NOT NULL,
				  `checklists`			    text NOT NULL,
				  `add_date` 				TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                 PRIMARY KEY (id)) $charset_collate;";
            dbDelta($sql);
		
    	}
    	
    	ob_flush();
	}

}