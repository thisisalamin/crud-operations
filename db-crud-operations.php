<?php

/**
 * Plugin Name:       DB CRUD Operations
 * Plugin URI:       https://github.com/thisisalamin/crud-operations
 * Description:       Handle all CRUD operations.
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Mohamed Alamin
 * Author URI:        https://example.com
 * License:           GPL v2 or later
 * License URI:       https://www.linkedin.com/in/thisismdalamin/
 * Text Domain:       db-crud-operations
 * Domain Path:       /languages
 */

require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

// Aborting if this file is called directly.

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class DB_CRUD_Operations
 */
class DB_CRUD_Operations {


	/**
	 * The constructor.
	 */
	public function __construct() {
		$this->define_constants();
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'plugins_loaded', array( $this, 'db_crud_operations_load_textdomain' ) );
		register_activation_hook( COP_FILE, array( $this, 'cop_activation' ) );
	}

	/**
	 * Initialize the plugin.
	 */
	public function init() {
		add_filter( 'plugin_action_links_' . plugin_basename( COP_FILE ), array( $this, 'settings_link' ) );
		include_once plugin_dir_path( __FILE__ ) . 'includes/Admin/AdminPage.php';
		new Alamin\CrudOperations\Admin\AdminPage();
	}

	/**
	 * Define the constants.
	 */
	private function define_constants() {
		define( 'COP_VERSION', '1.0' );
		define( 'COP_FILE', __FILE__ );
		define( 'COP_PATH', __DIR__ );
		define( 'COP_URL', plugins_url( '', COP_FILE ) );
		define( 'COP_ASSETS', COP_URL . '/assets' );
	}


	/**
	 * Load the plugin text domain for translation.
	 */
	public function db_crud_operations_load_textdomain() {
		load_plugin_textdomain( 'db-crud-operations', false, plugin_dir_path( __FILE__ ) . 'languages' );
	}


	/**
	 * The activation hook.
	 */
	public function cop_activation() {
		include_once plugin_dir_path( __FILE__ ) . 'includes/Database/Database.php';
		new Alamin\CrudOperations\Database\Database();
	}

	/**
	 * Add settings link to the plugin.
	 *
	 * @param array $links The links array.
	 *
	 * @return array
	 */
	public function settings_link( $links ) {
		$settings_link = '<a href="admin.php?page=crud-operations">' . __( 'Settings', 'db-crud-operations' ) . '</a>';
		array_push( $links, $settings_link );
		return $links;
	}
}

new DB_CRUD_Operations();
