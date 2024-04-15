<?php

namespace Alamin\CrudOperations\Database;

/**
 * Class Database
 *
 * Represents a database connection and operations.
 */
class Database {

	/**
	 * The name of the database table.
	 *
	 * @var string
	 */
	private $table_name;
	/**
	 * The version of the database.
	 *
	 * @var string
	 */
	private $cop_dbv = '1.0';

	/**
	 * Class constructor.
	 */
	public function __construct() {
		global $wpdb;
		$this->table_name = $wpdb->prefix . 'crud_operations';

		$cop_dvd = get_option( 'cop_dbv' );
		if ( $cop_dvd !== $this->cop_dbv ) {
			$this->create_table();
			update_option( 'cop_dbv', $this->cop_dbv );
		}
		$this->create_table();
	}

	/**
	 * Create the database table.
	 */
	public function create_table() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$sql             = "CREATE TABLE $this->table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(50) NOT NULL,
            email varchar(50) NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}
}
