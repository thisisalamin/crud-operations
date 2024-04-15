<?php
namespace Alamin\CrudOperations\Admin;

/**
 * Class Form
 *
 * Represents a form for CRUD operations.
 */
class Form {

	/**
	 * Represents the ID of the form.
	 *
	 * @var string
	 */
	private $id = '';

	/**
	 * Represents the name of the form.
	 *
	 * @var string
	 */
	private $name = '';

	/**
	 * Represents the email of the form.
	 *
	 * @var string
	 */
	private $email = '';

	/**
	 * Form constructor.
	 *
	 * Initializes the Form object and performs necessary actions based on the request.
	 */
	public function __construct() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'crud_operations';

		// Check if the action is edit and id is set.
		if ( isset( $_GET['action'] ) && 'update_data' === $_GET['action'] && isset( $_GET['id'] ) ) {

			if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_wpnonce'] ) ), 'crud_nonce' ) ) {
				wp_die( 'Invalid Request!' );
			}

			$this->id = isset( $_GET['id'] ) ? sanitize_text_field( wp_unslash( $_GET['id'] ) ) : '';

			$result = wp_cache_get( $this->id, 'crud_operations' );

			if ( false === $result ) {
				$result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}crud_operations WHERE id = %d", $this->id ) );
				wp_cache_set( $this->id, $result, 'crud_operations' );
			}

			if ( $result ) {
				$this->name  = $result->name;
				$this->email = $result->email;
			} else {
				$this->id    = isset( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';
				$this->name  = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
				$this->email = isset( $_POST['email'] ) ? sanitize_text_field( wp_unslash( $_POST['email'] ) ) : '';
			}
		}

		// Check if the action is delete and id is set.
		if ( isset( $_GET['action'] ) && 'delete' === $_GET['action'] && isset( $_GET['id'] ) ) {

			if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_wpnonce'] ) ), 'crud_nonce' ) ) {
				wp_die( 'Invalid Request!' );
			}

			$this->id = isset( $_GET['id'] ) ? sanitize_text_field( wp_unslash( $_GET['id'] ) ) : '';
			$wpdb->delete( $table_name, array( 'id' => $this->id ) );
			echo '<script>window.location.href="' . esc_url( admin_url( 'admin.php?page=crud-operations' ) ) . '";</script>';
			exit;
		}
	}

	/**
	 * Display the form.
	 */
	public function display_form() {
		?>
		<div>
			<a class="add-new-data btn btn-success" href="<?php echo esc_url( 'admin.php?page=crud-operations' ); ?>"><?php esc_html_e( 'Add New', 'db-crud-operations' ); ?></a>
			<br><br>
			<form class="form-inline" id="data-form" method="post" action="">
				<?php wp_nonce_field( 'crud_nonce' ); ?>
				<input type="hidden" class="form-control mr-3" name="id" value="<?php echo esc_attr( $this->id ); ?>">
				<input type="text" class="form-control mr-3" name="name" placeholder="Name" value="<?php echo esc_attr( $this->name ); ?>">
				<input type="text" class="form-control mr-3" name="email" placeholder="Email" value="<?php echo esc_attr( $this->email ); ?>">
				<input class="btn btn-success" type="submit" value="Submit">
			</form>
		</div>
		<?php
	}
}
