<?php

/**
 * Fired during plugin activation
 *
 * @link       http://2bytecode.com/author/tassawer
 * @since      1.0.0
 *
 * @package    Digital_Asset_Manager
 * @subpackage Digital_Asset_Manager/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Digital_Asset_Manager
 * @subpackage Digital_Asset_Manager/includes
 * @author     2ByteCode <support@2bytecode.com>
 */
class Digital_Asset_Manager_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
   		
		$table_dam_records = $wpdb->prefix . "dam_records";
		$sql = "CREATE TABLE $table_dam_records (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			srno mediumint(9) NOT NULL,
			name varchar(1024) NOT NULL,
			version varchar(55) NOT NULL,
			record_type varchar(55) NOT NULL,
			drive_url tinytext NOT NULL,
			live_url tinytext NOT NULL,
			license_key tinytext NOT NULL,
			account_credentials text NOT NULL,
			comments text NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";
		dbDelta( $sql );

		$table_dam_token = $wpdb->prefix . "dam_token";
		$sql = "CREATE TABLE $table_dam_token (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			token tinytext NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";
		dbDelta( $sql );

		$table_dam_record_with_token = $wpdb->prefix . "dam_record_with_token";
		$sql = "CREATE TABLE $table_dam_record_with_token (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			record_id mediumint(9) NOT NULL,
			token_id mediumint(9) NOT NULL,
			PRIMARY KEY  (id),
			FOREIGN KEY  (record_id) REFERENCES $table_dam_records(id),
			FOREIGN KEY  (token_id) REFERENCES $table_dam_token(id)
		) $charset_collate;";
		dbDelta( $sql );
	}

}
