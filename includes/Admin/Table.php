<?php

namespace Alamin\CrudOperations\Admin;

/**
 * Class Table
 *
 * Represents a table in the database.
 */
class Table {

	/**
	 * Display the table.
	 */
	public function display_table() {
		global $wpdb;

		// Try to get data from cache.
		$results = wp_cache_get( 'crud_operations' );
		if ( false === $results ) {
			$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}crud_operations" );
			wp_cache_set( 'crud_operations', $results );
		}

		?>

		<?php if ( empty( $results ) ) : ?>
			<div class="alert alert-warning" role="alert">
				<?php esc_html_e( 'No data found.', 'db-crud-operations' ); ?>
			</div>
		<?php else : ?>
		<table class="table">
			<thead class="thead-dark">
				<tr>
					<th scope="col">ID</th>
					<th scope="col">Name</th>
					<th scope="col">Email</th>
					<th scope="col">Action</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $results as $result ) : ?>
					
					<tr>
						<td><?php echo esc_html( $result->id ); ?></td>
						<td><?php echo esc_html( $result->name ); ?></td>
						<td><?php echo esc_html( $result->email ); ?></td>
						<td>
							<a class="edit-data text-success" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=crud-operations&action=update_data&id=' . $result->id ), 'crud_nonce' ) ); ?>"><?php esc_html_e( 'Edit', 'db-crud-operations' ); ?></a> /
							<a class="text-danger" id="delete_data" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=crud-operations&action=delete&id=' . $result->id ), 'crud_nonce' ) ); ?>" onclick="return confirm('Are you sure you want to delete this record?')"><?php esc_html_e( 'Delete', 'db-crud-operations' ); ?></a>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>
		<?php
	}
}
