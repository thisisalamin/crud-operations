<?php

namespace Alamin\CrudOperations\Admin;

/**
 * Class AdminPage
 */
class AdminPage {

	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_script' ) );
		add_action( 'wp_ajax_create_data', array( $this, 'handle_data' ) );
		add_action( 'wp_ajax_update_data', array( $this, 'handle_data' ) );
		add_action( 'wp_ajax_get_data', array( $this, 'get_data' ) );
	}

	/**
	 * Get data from the database.
	 */
	public function get_data() {
		check_ajax_referer( 'crud_nonce' );

		$id = isset( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';
		if ( ! $id ) {
			wp_send_json_error( 'Invalid ID' );
		}

		global $wpdb;

		// Get data from cache.
		$data = wp_cache_get( 'crud_operations' );

		if ( false === $data ) {
			$data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}crud_operations WHERE id = %d", $id ) );
			wp_cache_set( 'crud_operations', $data );
		}

		if ( $data ) {
			wp_send_json_success( $data );
		} else {
			wp_send_json_error( __( 'Data not found', 'db-crud-operations' ) );
		}
		wp_die();
	}

	/**
	 * Handle the data received from AJAX request.
	 */
	public function handle_data() {
		check_ajax_referer( 'crud_nonce' );
		global $wpdb;
		$table_name = $wpdb->prefix . 'crud_operations';

		$id    = isset( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';
		$name  = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$email = isset( $_POST['email'] ) ? sanitize_text_field( wp_unslash( $_POST['email'] ) ) : '';

		// Get data from cache.
		$results = wp_cache_get( 'crud_operations' );
		if ( false !== $results ) {
			wp_cache_delete( 'crud_operations' );
		}

		if ( isset( $_POST['action'] ) && 'update_data' === $_POST['action'] ) {
			$result = $wpdb->update(
				$table_name,
				array(
					'name'  => $name,
					'email' => $email,
				),
				array( 'id' => $id ),
				array( '%s', '%s' ),
				array( '%d' )
			);
			if ( $result ) {
				wp_send_json_success( __( 'Data updated successfully', ' db-crud-operations ' ) );
			} else {
				wp_send_json_error( __( 'The data already exists! Please try entering new data.', ' db-crud-operations ' ) );
			}
		} else {
			$result = $wpdb->insert(
				$table_name,
				array(
					'name'  => $name,
					'email' => $email,
				),
				array( '%s', '%s' )
			);
			if ( $result ) {
				wp_send_json_success( __( 'Data inserted successfully', 'db-crud-operations' ) );
			} else {
				wp_send_json_error( __( 'Failed to insert data', 'db-crud-operations' ) );
			}
		}
		wp_die();
	}


	/**
	 * Load the script based on the provided hooks.
	 *
	 * @param string $hooks The hooks to load the script for.
	 */
	public function load_script( $hooks ) {
		if ( 'toplevel_page_crud-operations' === $hooks ) {
			$crud_nonce = wp_create_nonce( 'crud_nonce' );
			$ajax_url   = admin_url( 'admin-ajax.php' );

			wp_enqueue_script( 'cop-script', COP_ASSETS . '/js/main.js', array( 'jquery' ), '1.0', true );
			wp_enqueue_style( 'cop-style', COP_ASSETS . '/css/bootstrap.min.css', array(), '1.0', 'all' );
			wp_localize_script(
				'cop-script',
				'ajax_object',
				array(
					'cop_nonce' => $crud_nonce,
					'ajax_url'  => $ajax_url,
				)
			);
		}
	}

	/**
	 * Add menu.
	 */
	public function add_menu() {
		add_menu_page(
			'CRUD Operations',
			'CRUD Operations',
			'manage_options',
			'crud-operations',
			array( $this, 'crud_page' ),
			'dashicons-database-add',
			30
		);
	}

	/**
	 * Display the CRUD page.
	 */
	public function crud_page() {
		include_once COP_PATH . '/includes/Admin/Table.php';
		include_once COP_PATH . '/includes/Admin/Form.php';
		$table = new Table();
		$form  = new Form();

		?>
		<div class="wrap">
			<h2>CRUD Operations</h2>
			<br>
			<?php $form->display_form(); ?>
			<br>
			<?php $table->display_table(); ?>
		</div>
		<?php
	}
}
