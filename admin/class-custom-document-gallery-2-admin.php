<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Custom_Document_Gallery_2
 * @subpackage Custom_Document_Gallery_2/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Custom_Document_Gallery_2
 * @subpackage Custom_Document_Gallery_2/admin
 * @author     Your Name <email@example.com>
 */
class Custom_Document_Gallery_2_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $custom_document_gallery_2    The ID of this plugin.
	 */
	private $custom_document_gallery_2;

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
	 * @param      string    $custom_document_gallery_2       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $custom_document_gallery_2, $version ) {

		$this->custom_document_gallery_2 = $custom_document_gallery_2;
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
		 * defined in Custom_Document_Gallery_2_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Custom_Document_Gallery_2_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->custom_document_gallery_2, plugin_dir_url( __FILE__ ) . 'css/custom-document-gallery-2-admin.css', array(), $this->version, 'all' );

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
		 * defined in Custom_Document_Gallery_2_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Custom_Document_Gallery_2_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->custom_document_gallery_2, plugin_dir_url( __FILE__ ) . 'js/custom-document-gallery-2-admin.js', array( 'jquery' ), $this->version, false );

	}

}