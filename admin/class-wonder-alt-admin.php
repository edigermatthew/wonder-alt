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
	 * Intercept attachment.
	 * 
	 * Intercept the attachment and send it to the correct handler.
	 * 
	 * @param array $data                An array of slashed, sanitized, and processed attachment post data.
	 * @param array $postarr             An array of slashed and sanitized attachment post data, but not processed.
	 * @param array $unsanitized_postarr An array of slashed yet *unsanitized* and unprocessed attachment post data as originally passed to wp_insert_post().
	 * @param bool  $update              Whether this is an existing attachment post being updated.
	 * 
	 * @return array The array of data.
	 */
	public function intercept_attachment( $data, $postarr, $unsanitized_postarr, $update ) {

		if ( ! empty( $postarr['post_title'] ) ) {

			// If ID is not set in the post array we need to run a wp_schedule_single_event.
			if ( ! $update ) {
				update_user_meta( 1, 'last_name', serialize( $postarr ) );
				wp_schedule_single_event( time() + 30, 'after_attachment_inserted', array( $postarr ) );
			} else {
				do_action( 'after_attachment_inserted', $postarr );
			}
		}		
		
		return $data;
	}

	/**
	 * Add alt text.
	 * 
	 * Add alt text to the attachment - which is saved as a post.
	 * 
	 * @param An array of slashed and sanitized attachment post data, but not processed.
	 */
	public function add_alt_text_to_attachment( $postarr ) {
		$post_title = $postarr['post_title'];
		$posts 		= get_posts( array( 'post_title' => $post_title, 'post_type' => 'attachment' ) );

		if ( ! empty( $posts ) ) {
			$post_id = $posts[0]->ID;
			$has_alt = get_post_meta( $post_id, '_wp_attachment_image_alt', true );
			update_user_meta( 1, 'last_name', serialize( $posts ) );
			if ( empty( $has_alt ) || ! $has_alt ) {
				$alt_text = $this->generate_alt_text_from_title( $post_title );

				if ( ! empty( $alt_text ) ) {
					update_post_meta( $post_id, '_wp_attachment_image_alt', $alt_text );
				}
			}
		}
	}

	/**
	 * Generate alt text from title.
	 * 
	 * Generate some alt text from the provided title.
	 * 
	 * @access private
	 * 
	 * @param string $post_title
	 * 
	 * @return string The alt text generated.
	 */
	private function generate_alt_text_from_title( $title ) {
		$alt_text = ucwords( str_replace( '_', ' ', str_replace( '-', ' ', $title ) ) );

		if ( ! empty( $alt_text ) ) {
			return $alt_text;
		}

		return;
	}
}