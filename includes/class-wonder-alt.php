<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link https://wonderjarcreative.com
 * 
 * @since 0.1.0
 *
 * @package Wonder_Alt
 * 
 * @subpackage Wonder_Alt/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 */
class Wonder_Alt {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 * 
	 * @access protected
	 * 
	 * @var Wonder_Alt_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 * 
	 * @access protected
	 * 
	 * @var string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 * 
	 * @access protected
	 * 
	 * @var string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 */
	public function __construct() {
		if ( defined( 'WONDER_ALT_VERSION' ) ) {
			$this->version = WONDER_ALT_VERSION;
		} else {
			$this->version = '0.1.0';
		}
		$this->plugin_name = 'wonder-alt';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		//$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wonder_Alt_Loader. Orchestrates the hooks of the plugin.
	 * - Wonder_Alt_i18n. Defines internationalization functionality.
	 * - Wonder_Alt_Admin. Defines all hooks for the admin area.
	 * - Wonder_Alt_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 * 
	 * @access private
	 */
	private function load_dependencies() {
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wonder-alt-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wonder-alt-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wonder-alt-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wonder-alt-public.php';

		$this->loader = new Wonder_Alt_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wonder_Alt_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 * 
	 * @access private
	 */
	private function set_locale() {
		$plugin_i18n = new Wonder_Alt_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 * 
	 * @access private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Wonder_Alt_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_filter( 'wp_insert_attachment_data', $plugin_admin, 'intercept_attachment', 10, 4 );
		$this->loader->add_action( 'after_attachment_inserted', $plugin_admin, 'add_alt_text_to_attachment' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 * 
	 * @access private
	 */
	private function define_public_hooks() {
		$plugin_public = new Wonder_Alt_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return string The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return Wonder_Alt_Loader Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return string The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}