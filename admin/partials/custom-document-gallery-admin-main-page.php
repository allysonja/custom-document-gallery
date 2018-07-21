<?php

/**
 * Provide an admin area view for the plugin.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Custom_Document_Gallery
 * @subpackage Custom_Document_Gallery/admin/partials
 */

include( plugin_dir_path( __FILE__ ) . 'classes/class-custom-document-gallery-gallery-table.php');
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php

$table = new CDG_Gallery_Table();
$table->prepare_items();
?>
<div class="wrap">

    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e('Galleries', 'mgpv_example')?>
    	<a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=gallery-set-form');?>">
    		<?php _e('Add new', 'mgpv_example')?>
    	</a>
    </h2>

    <?php $table->views() ?>
    <form id="gallery-set-filter" method="GET">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
        <?php $table->display() ?>
    </form>

</div>