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
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

/**
 * Delete table from DB upon plugin deletion.
 *
 * @return void
 */
function ppr_delete_installed_tables() {
	global $wpdb;
	$tables = array( 'ppr_post_page_reactions', 'ppr_post_page_reaction_users' );
	foreach ( $tables as $table ) {
		$ppr_table = $wpdb->prefix . $table;
		$sql          = "DROP TABLE IF EXISTS $ppr_table";
		$wpdb->query( $sql );
	}
}
ppr_delete_installed_tables();
