<?php
/**
 * Post Page Reaction 
 *
 * @package     hamid-post-page-reaction
 * @author      Hamid Azad
 * @copyright   2023 Hamid Azad
 * @license     GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name: Post Page Reaction
 * Plugin URI:  https://github.com/hamidhosenazad/post-page-reaction
 * Description: Plugin to let user react to post or page 
 * Version:     1.0.0
 * Author:      Hamid Azad
 * Author URI:  https://github.com/hamidhosenazad
 * Text Domain: hamid-post-page-reaction
 * License:     GPL v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

/*
 * If this file is called directly, abort.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/includes/Installer.php';
require_once __DIR__ . '/includes/Shortcode.php';
require_once __DIR__ . '/includes/Assets.php';

final class Hamid_Post_Page_Reaction{

    /**
	 * Class construcotr
	 */
    private function __construct() {
		$this->ppr_installer();
        $this->ppr_init_plugin();
		$this->ppr_define_constants();
	}

	/**
	 * Define the required plugin constants
	 *
	 * @return void
	 */
	public function ppr_define_constants() {
		define( 'POST_PAGE_REACTION_FILE', __FILE__ );
		define( 'POST_PAGE_REACTION_PATH', __DIR__ );
		define( 'POST_PAGE_REACTION_URL', plugin_dir_url( __FILE__ ) );
		define( 'POST_PAGE_REACTION_ASSETS', POST_PAGE_REACTION_URL . 'assets' );
	}

    /**
	 * Initializes a singleton instance
	 *
	 * @return \Hamid_Post_Page_Reaction
	 */
	public static function ppr_init() {
		static $instance = false;

		if ( ! $instance ) {
			$instance = new self();
		}

		return $instance;
	}

    /**
	 * Initialize the plugin
	 *
	 * @return void
	 */
	public function ppr_init_plugin() {
			$shortcode_init  = new Shortcode();
            add_action('init', array($shortcode_init, 'ppr_register_shortcode'));
			add_action('wp_ajax_ppr_save_reaction_data', array($shortcode_init, 'ppr_save_reaction_data'));
			add_action('wp_ajax_nopriv_ppr_save_reaction_data', array($shortcode_init, 'ppr_save_reaction_data'));

			add_action('wp_ajax_ppr_save_reaction_count_data', array($shortcode_init, 'ppr_save_reaction_count_data'));
			add_action('wp_ajax_nopriv_ppr_save_reaction_count_data', array($shortcode_init, 'ppr_save_reaction_count_data'));

			
			$assets_init = new Assets();

	}

    /**
	 * Do stuff upon plugin activation
	 *
	 * @return void
	 */
	public function ppr_installer() {
		$installer = new Installer();
        $installer->ppr_install();
	}
}

/**
 * Initializes the main plugin
 *
 * @return \Hamid_Post_Page_Reaction
 */
function Hamid_Post_Page_Reaction() {
	return Hamid_Post_Page_Reaction::ppr_init();
}

// Kick-off the plugin.
Hamid_Post_Page_Reaction();