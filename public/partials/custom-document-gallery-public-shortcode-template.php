<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Custom_Document_Gallery
 * @subpackage Custom_Document_Gallery/public/partials
 */

// print($id);

?>

<div id="custom-document-gallery">
	<?php
	global $wpdb;
	$table_name = $wpdb->prefix . 'document_media';
	$query = "SELECT * FROM " . $table_name . " WHERE document_gallery_id = " . $id . " AND deleted_date IS NULL ORDER BY sort_order";
	$data = $wpdb->get_results($query, ARRAY_A);
    $medias = $data;

    foreach ($medias as $media) {
    	// print_r($media);
    ?>

    <div class="document_media">
    	<div>
    		<p>
    			<?php echo $media['name']; ?>
    		</p>
    	</div>
    	<div>
    		<a href="<?php echo $media['document_url']; ?>" target="_blank">
    			<img src="<?php echo $media['thumbnail_url']; ?>" alt="document media pdf thumbnail">
    		</a>
    	</div>
    </div>

    <?php
	}
	?>
</div>