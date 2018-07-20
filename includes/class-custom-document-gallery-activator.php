<?php

/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Custom_Document_Gallery
 * @subpackage Custom_Document_Gallery/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Custom_Document_Gallery
 * @subpackage Custom_Document_Gallery/includes
 * @author     Sonja Linton <sonjamw17@gmail.com>
 */

class Custom_Document_Gallery_Activator {

	/**
	 * Create database tables, create paths and directories for media uploads.
	 *
	 * The activation handler prepares the database tables by creating the galleries table, and the media table, which ties the media to the galleries. It also creates the new directories/folders on the paths to have a specific place to upload media to for the galleries.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		self::prepare_database();
		self::create_directories();
	}

	/**
	 * Prepare the database. Create Galleries table and Media table.
	 *
	 * Create two tables, the galleries table and the media table. The media table will use the gallery's id as a foriegn key.
	 *
	 * @since    1.0.0
	 */
	private static function prepare_database(){
		self::create_gallery_table();
		self::create_media_table();

		update_option( 'CUSTOM_DOCUMENT_GALLERY_DATABASE_VERSION', CUSTOM_DOCUMENT_GALLERY_DATABASE_VERSION );
	}

	/**
	 * Create galleries table.
	 *
	 * Create galleries table. The gallery table has the id (primary key), the name, the created date, and the updated date.
	 *
	 * @since    1.0.0
	 */
	private static function create_gallery_table() {
		global $wpdb;

		$table_name = $wpdb->prefix . 'document_galleries';

		$charset_collate = $wpdb->get_charset_collate();

		$sql = $wpdb->query("CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			name varchar(55) NOT NULL,
			created_date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
			updated_date datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;");

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta($sql);
	}

	/**
	 * Create media table.
	 *
	 * Create media table. The media table has the id (primary key), the name, the id of the document gallery it belongs to (foreign key) the created date, the updated date, the url path of the original media document, the url path of the image thumbnail of the document, and the sort order. When a gallery is deleted, all media references in this table will be deleted, as shown by the ON DELETE CASCADE of the relationship constraint.
	 *
	 * @since    1.0.0
	 */
	private static function create_media_table() {
		global $wpdb;

		$table_name = $wpdb->prefix . 'document_media';
		$galleries_table_name = $wpdb->prefix . 'document_galleries';

		$charset_collate = $wpdb->get_charset_collate();

		$sql = $wpdb->query("CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			name varchar(55) NOT NULL,
			document_gallery_id mediumint(9) NOT NULL,
			created_date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
			updated_date datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
			document_url varchar(255) NOT NULL,
			thumbnail_url varchar(255) NOT NULL,
			sort_order int NOT NULL DEFAULT 2147483647,
			PRIMARY KEY (id),
			CONSTRAINT FK_document_galleries FOREIGN KEY (document_gallery_id)
			REFERENCES $galleries_table_name(id)
			ON DELETE CASCADE
		) $charset_collate;");
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta($sql);
	}

	private static function create_directories(){
		$upload_dir = wp_upload_dir();
		$base_dir = $upload_dir["basedir"] . '/custom_document_gallery/';
		$dir = $base_dir . 'documents/';
		self::create_path($dir);
	}

	private static function create_path($path) {
	    if (is_dir($path)) return true;
	    $prev_path = substr($path, 0, strrpos($path, '/', -2) + 1 );
	    $return = self::create_path($prev_path);
	    return ($return && is_writable($prev_path)) ? mkdir($path) : false;
	}

}