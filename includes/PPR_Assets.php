<?php

/**
 * Core plugin file
 *
 * @since      1.0
 * @package    post-page-reaction
 * @author     Hamid Azad
 */

/*
 * If this file is called directly, abort.
 */
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Assets handler class
 */
class PPR_Assets
{

	/**
	 * Class constructor
	 */
	public function __construct()
	{
		add_action('wp_enqueue_scripts', array($this, 'ppr_register_assets'));
	}

	/**
	 * All available scripts
	 *
	 * @return array
	 */
	public function get_scripts()
	{
		return array(
			'wp-plugin-boilerplate-script' => array(
				'src' => PPR_ASSETS . '/js/ppr.js',
				'version' => filemtime(PPR_PATH . '/assets/js/ppr.js'),
				'deps' => array('jquery'),
			),

		);
	}

	/**
	 * All available styles
	 *
	 * @return array
	 */
	public function get_styles()
	{
		return array(
			'wp-plugin-boilerplate-style' => array(
				'src' => PPR_ASSETS . '/css/ppr.css',
				'version' => filemtime(PPR_PATH . '/assets/css/ppr.css'),
			),
			'ppr-fontawesome-style' => array(
				'src' => PPR_ASSETS . '/css/fontawesome.min.css',
				'version' => filemtime(PPR_PATH . '/assets/css/fontawesome.min.css'),
			),
			'ppr-fontawesome-all' => array(
				'src' => PPR_ASSETS . '/css/all.css',
				'version' => filemtime(PPR_PATH . '/assets/css/all.css'),
			),
		);
	}

	/**
	 * Register scripts and styles
	 *
	 * @return void
	 */
	public function ppr_register_assets()
	{
		$scripts = $this->get_scripts();
		$styles = $this->get_styles();

		foreach ($scripts as $handle => $script) {
			$deps = isset($script['deps']) ? $script['deps'] : false;
			wp_register_script($handle, $script['src'], $deps, $script['version'], true);
			wp_enqueue_script($handle);
			wp_localize_script($handle, 'pprAjax', array(
				'ajaxurl' => admin_url('admin-ajax.php'),
				'nonce' => wp_create_nonce('ppr-nonce-hamid')
			));
		}

		foreach ($styles as $handle => $style) {
			$deps = isset($style['deps']) ? $style['deps'] : false;
			wp_register_style($handle, $style['src'], $deps, $style['version']);
			wp_enqueue_style($handle);
		}
	}
}
