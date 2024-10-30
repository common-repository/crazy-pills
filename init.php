<?php
/*
Plugin Name:    Crazy Pills
Description:    Stop the shortcode madness with Crazy Pills. Build buttons, boxes, beautiful lists, and highlight text right from your editor, with live preview.
Author:         Hassan Derakhshandeh
Version:        0.4.3
Text Domain:    crazy-pills
Domain Path:    /languages
*/

class CrazyPills {

	var $base_url;

	function __construct() {
		add_action( 'init', array( $this, 'add_buttons' ) );
		add_action( 'init', array( $this, 'i18n' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
		$this->base_url = trailingslashit( plugins_url( '', __FILE__ ) );
	}

	/**
	 * Load stylesheet for editor preview
	 *
	 * @link http://codex.wordpress.org/Plugin_API/Filter_Reference/mce_css
	 * @return string mce_css
	 * @since 0.1
	 */
	function mce_css( $mce_css ) {
		if( ! empty( $mce_css ) ) $mce_css .= ',';
		$mce_css .= $this->base_url . 'css/styles.css,' . $this->base_url . 'css/admin.css';
		return $mce_css;
	}

	function add_buttons() {
		if ( current_user_can( 'edit_posts' ) &&  current_user_can( 'edit_pages' ) ) {
			add_filter( 'mce_external_plugins', array( $this, 'mce_external_plugins' ) );
			add_filter( 'mce_buttons_2', array( $this, 'mce_buttons_2' ) );
			add_action( 'wp_enqueue_editor', array( $this, 'wp_enqueue_editor' ) );
			add_filter( 'mce_css', array( $this, 'mce_css' ) );
		}
	}

	/**
	 * Add the button to TinyMCE toolbar, second row
	 *
	 * @return array
	 */
	function mce_buttons_2( $buttons ) {
		$config = $this->get_config();
		foreach( array_keys( $config ) as $button ) {
			array_push( $buttons, $button );
		}

		return $buttons;
	}

	/**
	 * Define script file responsible for the TinyMCE button
	 * All are handled by the editor.js file
	 *
	 * @return array
	 */
	function mce_external_plugins( $plugin_array ) {
		$config = $this->get_config();
		foreach( array_keys( $config ) as $button ) {
			$plugin_array[ $button ] = $this->base_url . 'js/editor.js';
		}

		return $plugin_array;
	}

	function enqueue() {
		wp_enqueue_style( 'crazypills', $this->base_url . 'css/styles.css', array(), '0.4.3' );
	}

	/**
	 * Load assets required for TinyMCE
	 *
	 * @since 0.4.3
	 */
	function wp_enqueue_editor() {
		wp_enqueue_style( 'crazypills-admin', $this->base_url . 'css/admin.css', array( 'editor-buttons' ), '0.4.3' );

		wp_localize_script( 'editor', 'crazyPills', array(
			'config' => $this->get_config(),
		) );
	}

	public function i18n() {
		load_plugin_textdomain( 'crazy-pills', false, '/languages' );
	}

	/**
	 * Get the plugin configration file
	 *
	 * This can be overrided in themes by copying the config file to <theme>/plugins/crazy-pills/ folder
	 * and modifying it.
	 *
	 * @return array
	 * @since 0.4.3
	 */
	public function get_config() {
		if( ! $file = locate_template( array( 'plugins/crazy-pills/config.php' ) ) ) {
			$file = dirname( __FILE__ ) . '/includes/config.php';
		}
		$config = include( $file );

		return apply_filters( 'crazy_pills_config', $config );
	}
}
$crazy_pills = new CrazyPills;