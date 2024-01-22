<?php
/**
 * Core plugin file
 *
 * @since      1.0
 * @package    hamid-post-page-reaction
 * @author     Hamid Azad
 */

/*
 * If this file is called directly, abort.
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Installer class
 */
class PPR_Installer {

	/**
	 * Run the installer
	 *
	 * @return void
	 */
	public function ppr_install() {
		$this->ppr_create_tables();
	}

	/**
	 * Create necessary database tables
	 *
	 * @return void
	 */
	public function ppr_create_tables() {

		global $wpdb;
    
		if ( ! function_exists( 'dbDelta' ) ) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		}
		
		$charset_collate = $wpdb->get_charset_collate();

		// SQL queries for table creation
		$schema = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}ppr_post_page_reactions` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`user_id` int(11) NOT NULL,
			`post_type` varchar(100) NOT NULL,
			`post_or_page_id` int(11) NOT NULL,
			`straight_face_count` int(11) NOT NULL,
			`smiley_face_count` int(11) NOT NULL,
			`sad_face_count` int(11) NOT NULL,
			`created_at` datetime NOT NULL,
			PRIMARY KEY (`id`)
		) $charset_collate";
		
		// dbDelta to create or update tables
		dbDelta( $schema );
	}
}
