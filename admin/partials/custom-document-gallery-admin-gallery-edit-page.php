<?php
/**
 * Create gallery edit page for the plugin.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Custom_Document_Gallery
 * @subpackage Custom_Document_Gallery/admin/partials
 */


/**
 * The class responsible for creating
 * the form of the gallery edit page and updating database information
 */

if (isset($_POST['cdg-upload-images']) || isset($_POST['cdg-upload-videos']) || isset($_POST['cdg-upload-thumb'])) {
  include (plugin_dir_path( dirname( __FILE__ ) ) . 'partials/classes/class-custom-document-gallery-upload-media.php');
  $upload_media = new Custom_Document_Gallery_Upload_Media($_REQUEST['id']);
}

require_once plugin_dir_path( dirname( __FILE__ ) ) . 'partials/classes/class-custom-document-gallery-gallery-edit-functions.php';

$gallery_edit_page = new Custom_Document_Gallery_Gallery_Edit(CUSTOM_DOCUMENT_GALLERY_PLUGIN_NAME, CUSTOM_DOCUMENT_GALLERY_VERSION);

?>
