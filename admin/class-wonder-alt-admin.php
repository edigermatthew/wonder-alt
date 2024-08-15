<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link https://wonderjarcreative.com
 * 
 * @since 0.1.0
 *
 * @package Wonder_Alt
 * 
 * @subpackage Wonder_Alt/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 */
class Wonder_Alt_Admin {

	/**
	 * The ID of this plugin.
	 * 
	 * @access private
	 * 
	 * @var string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @access private
	 * 
	 * @var string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wonder_Alt_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wonder_Alt_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wonder-alt-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wonder_Alt_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wonder_Alt_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wonder-alt-admin.js', array( 'jquery' ), $this->version, false );
	}

	/**
	 * Save attribute alt.
	 * 
	 * Save the alt for the attribute.
	 * 
	 * @since 1.4.0
	 * 
	 * @param int 	  $post_id The post ID.
	 * @param WP_POST $post    The post object.
	 * @param bool 	  $update  Whether this is an existing post being updated.
	 */
	public function save_attachment_alt( $post_id ) {
		$has_alt = get_post_meta( $post_id, '_wp_attachment_image_alt', true );

		if ( empty( $has_alt ) || ! $has_alt ) {
			$alt_text = $this->generate_alt_text_from_title( get_the_title( $post_id ) );

			if ( ! empty( $alt_text ) ) {
				update_post_meta( $post_id, '_wp_attachment_image_alt', $alt_text );
			}
		}

	}
	
	/**
	 * WP ajax save attachment alt.
	 * 
	 * Save the attachment in the ajax call.
	 * 
	 * @see /wp-admin/includes/ajax-actions.php#3109
	 * @see wp_ajax_save_attachment
	 * 
	 * @since 1.3.0
	 */
	public function wp_ajax_save_attachment_alt() {		
		if ( ! isset( $_REQUEST['id'] ) || ! isset( $_REQUEST['changes'] ) ) {
			wp_send_json_error();
		}
	
		$id = absint( $_REQUEST['id'] );
		if ( ! $id ) {
			wp_send_json_error();
		}
	
		check_ajax_referer( 'update-post_' . $id, 'nonce' );
	
		if ( ! current_user_can( 'edit_post', $id ) ) {
			wp_send_json_error();
		}
	
		$changes = $_REQUEST['changes'];
		$post    = get_post( $id, ARRAY_A );
	
		if ( 'attachment' !== $post['post_type'] ) {
			wp_send_json_error();
		}

		// If no alt is coming in the changes.
		if ( ! isset( $changes['alt'] ) || empty( $changes['alt'] ) ) {
			$this->update_alt_meta( $id, $post['post_title'] );
		}

		wp_send_json_success();
	}

	/**
	 * Update alt meta.
	 * 
	 * Update the meta for the image.
	 * 
	 * @since 1.4.0
	 * 
	 * @param int 	 $post_id 	 The ID of the post.
	 * @param string $post_title The title of the post.
	 */
	private function update_alt_meta( $post_id, $post_title ) {
		$has_alt = get_post_meta( $post_id, '_wp_attachment_image_alt', true );

		// Only on empty alts.
		if ( empty( $has_alt ) || false === $has_alt ) {
			$alt_text = $this->generate_alt_text_from_title( $post_title );

			if ( ! empty( $alt_text ) ) {
				update_post_meta( $post_id, '_wp_attachment_image_alt', $alt_text );
			}
		}
	}

	/**
	 * Generate alt text from title.
	 * 
	 * Utility method to generate some alt text from the provided title.
	 * 
	 * @access private
	 * 
	 * @since 1.3.0 Adding wp_slash and wp_strip_all_tags.
	 * 
	 * @param string $post_title
	 * 
	 * @return string The alt text generated.
	 */
	private function generate_alt_text_from_title( $title ) {
		$alt_text = ucwords( str_replace( '_', ' ', str_replace( '-', ' ', $title ) ) );

		if ( ! empty( $alt_text ) ) {
			return wp_slash( wp_strip_all_tags( $alt_text, true ) );
		}

		return;
	}
}