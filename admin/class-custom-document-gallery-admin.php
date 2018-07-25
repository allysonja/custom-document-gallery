<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Custom_Document_Gallery
 * @subpackage Custom_Document_Gallery/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Custom_Document_Gallery
 * @subpackage Custom_Document_Gallery/admin
 * @author     Sonja Linton <sonjamw17@gmail.com>
 */
class Custom_Document_Gallery_Admin {

	/**
	 * The plugin options.
	 *
	 * @since 		1.0.0
	 * @access 		private
	 * @var 		string 			$options    The plugin options.
	 */
	private $options;

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Custom_Document_Gallery_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Custom_Document_Gallery_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/custom-document-gallery-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Custom_Document_Gallery_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Custom_Document_Gallery_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/custom-document-gallery-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Function to create admin menu pages.
	 *
	 * @since    1.0.0
	 */

	public function create_admin_menu() {
		$this->admin_main_page();
		$this->admin_gallery_edit_page();
		$this->admin_media_edit_page();
		$this->admin_settings_page();
		$this->admin_help_page();
	}

	/**
	 * Register the main plugin menu page.
	 *
	 * @since    1.0.0
	 */
	public function admin_main_page() {
	    add_menu_page(
	        'Custom Document Gallery',
	        'Document Gallery',
	        'manage_options',
	        $this->plugin_name,
	        array( $this, 'load_admin_main_page_content' ),
	        '',
	        6
	    );
	}

	/**
	 * Load the main admin menu page partial.
	 *
	 * @since    1.0.0
	 */
	public function load_admin_main_page_content() {
	    require_once plugin_dir_path( __FILE__ ). 'partials/custom-document-gallery-admin-main-page.php';
	}

	/**
	 * Register the plugin gallery edit page.
	 *
	 * @since    1.0.0
	 */
	public function admin_gallery_edit_page() {
	    add_submenu_page(
	        null,
	        'Edit Gallery',
	        'Edit Gallery',
	        'manage_options',
	        $this->plugin_name . '-gallery-edit',
	        array( $this, 'load_admin_gallery_edit_page_content' ),
	        ''
	    );
	}

	/**
	 * Load the plugin gallery edit page partial.
	 *
	 * @since    1.0.0
	 */
	public function load_admin_gallery_edit_page_content() {
	    require_once plugin_dir_path( __FILE__ ). 'partials/custom-document-gallery-admin-gallery-edit-page.php';
	}

	/**
	 * Register the plugin media edit page.
	 *
	 * @since    1.0.0
	 */
	public function admin_media_edit_page() {
	    add_submenu_page(
	        null,
	        'Edit Media',
	        'Edit Media',
	        'manage_options',
	        $this->plugin_name . '-media-edit',
	        array( $this, 'load_admin_media_edit_page_content' ),
	        ''
	    );
	}

	/**
	 * Load the plugin media edit page partial.
	 *
	 * @since    1.0.0
	 */
	public function load_admin_media_edit_page_content() {
	    require_once plugin_dir_path( __FILE__ ). 'partials/custom-document-gallery-admin-media-edit-page.php';
	}

	/**
	 * Register the plugin settings page.
	 *
	 * @since    1.0.0
	 */
	public function admin_settings_page() {
	    add_submenu_page(
	        $this->plugin_name,
	        'Settings',
	        'Settings',
	        'manage_options',
	        $this->plugin_name . '-settings',
	        array( $this, 'load_admin_settings_page_content' ),
	        ''
	    );
	}

	/**
	 * Load the plugin settings page partial.
	 *
	 * @since    1.0.0
	 */
	public function load_admin_settings_page_content() {
	    require_once plugin_dir_path( __FILE__ ). 'partials/custom-document-gallery-admin-settings-page.php';
	}

	/**
	 * Register the plugin help page.
	 *
	 * @since    1.0.0
	 */
	public function admin_help_page() {
	    add_submenu_page(
	        $this->plugin_name,
	        'Help',
	        'Help',
	        'manage_options',
	        $this->plugin_name . '-help',
	        array( $this, 'load_admin_help_page_content' ),
	        ''
	    );
	}

	/**
	 * Load the plugin help page partial.
	 *
	 * @since    1.0.0
	 */
	public function load_admin_help_page_content() {
	    require_once plugin_dir_path( __FILE__ ). 'partials/custom-document-gallery-admin-help-page.php';
	}

}