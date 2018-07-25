<?php

/**
 * Gallery editing functions.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Custom_Document_Gallery
 * @subpackage Custom_Document_Gallery/admin
 */

/**
 * Gallery editing functions.
 *
 * @package    Custom_Document_Gallery
 * @subpackage Custom_Document_Gallery/admin/partials/classes
 * @author     Sonja Linton <sonjamw17@gmail.com>
 */

class Custom_Document_Gallery_Gallery_Edit {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	public function __construct($plugin_name, $version) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->load_dependancies();
		$this->page_handler();
	}

	/**
	 * Load dependancies.
	 * Require Class CDG_Media_Table to render media table.
	 */
	private function load_dependancies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'classes/class-custom-document-gallery-media-table.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'classes/class-custom-document-gallery-upload-media.php';
	}

	/**
	 * Form page handler checks is there some data posted and tries to save it
	 * Also it renders basic wrapper in which we are calling meta box render
	 */
	private function page_handler() {
	    global $wpdb;
	    $table_name = $wpdb->prefix . 'document_galleries'; // do not forget about tables prefix

	    $message = '';
	    $notice = '';

	    // this is default $item which will be used for new records
	    $default = array(
	        'id' => 0,
	        'name' => '',
	    );

	    // print_r($_REQUEST);
	    // here we are verifying does this request is post back and have correct nonce
	    if ( isset($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
	        // print_r($_REQUEST);
	        // combine our default item with request params
	        $item = shortcode_atts($default, $_REQUEST);
	        // validate data, and if all ok save item to database
	        // if id is zero insert otherwise update
	        $item_valid = $this->validate_gallery($item);
	        if ($item_valid === true) {
	            if (isset($_REQUEST['save_gallery'])) {
	                if ($item['id'] == 0) {
	                    $result = $wpdb->insert($table_name, $item);
	                    $item['id'] = $wpdb->insert_id;
	                    if ($result) {
	                        $message = __('Item was successfully saved', 'custom_document_gallery');
	                    } else {
	                        $notice = __('There was an error while saving item', 'custom_document_gallery');
	                    }
	                } else {
	                    $result = $wpdb->update($table_name, $item, array('id' => $item['id']));
	                    if ($result) {
	                        $message = __('Item was successfully updated', 'custom_document_gallery');
	                    } else {
	                        $notice = __('There was an error while updating item', 'custom_document_gallery');
	                    }
	                }
	            }
	        } else {
	            // if $item_valid not true it contains error message(s)
	            $notice = $item_valid;
	        }
	    } else {
	        // if this is not post back we load item to edit or give new one to create
	        $item = $default;
	        if (isset($_REQUEST['id'])) {
	            $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $_REQUEST['id']), ARRAY_A);
	            if (!$item) {
	                $item = $default;
	                $notice = __('Item not found', 'custom_document_gallery');
	            }
	        }
	    }

	    // here we adding our custom meta box
	    $this->add_meta_boxes();

	    ?>
	    <div class="wrap">
	        <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
	        <!-- <p><?php // var_dump($item); ?></p> -->
	        <?php if($item['id'] != 0) { ?>
	        	<h2><?php _e('Edit Gallery', 'custom_document_gallery')?> <a class="add-new-h2"
	                                    href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=' . $this->plugin_name);?>"><?php _e('back to galleries list', 'custom_document_gallery')?></a>
	        	</h2>
	        <?php } else { ?>
	        	<h2><?php _e('Create New Gallery', 'custom_document_gallery')?> <a class="add-new-h2"
	                                    href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=' . $this->plugin_name);?>"><?php _e('back to galleries list', 'custom_document_gallery')?></a>
	        	</h2>
	        <?php } ?>

	        <?php if (!empty($notice)): ?>
	        <div id="notice" class="error"><p><?php echo $notice ?></p></div>
	        <?php endif;?>
	        <?php if (!empty($message)): ?>
	        <div id="message" class="updated"><p><?php echo $message ?></p></div>
	        <?php endif;?>

	        <form id="gallery_form" method="POST">
	            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
	            <?php /* NOTICE: here we storing id to determine will be item added or updated */ ?>
	            <input type="hidden" name="id" value="<?php echo $item['id'] ?>"/>

	            <div class="metabox-holder" id="poststuff">
	                <div id="post-body">
	                    <div id="post-body-content">
	                        <?php /* And here we call our custom meta box */ ?>
	                        <?php do_meta_boxes('gallery', 'normal', $item); ?>
	                        <input type="submit" value="<?php _e('Save', 'custom_document_gallery')?>" id="save_gallery" class="button-primary" name="save_gallery">
	                        <br>
	                        <br>
	                        <?php if ($item['id'] != 0) {
	                        	do_meta_boxes('gallery media', 'normal', $item['id']); 
	                        } else  { ?>
	                        	<p> You will be able to add media to this gallery once you have saved it </p>
	                        <?php } ?>
	                        <br>
	                        <br>
	                        <?php if ($item['id'] != 0) {
	                            do_meta_boxes('media upload', 'normal', $item);
	                        } ?>
	                    </div>
	                </div>
	            </div>
	        </form>
	    </div>
	    <?php
	}

	/**
	 * Add meta boxes all in one function.
	 * $item is row
	 */
	public function add_meta_boxes() {
		add_meta_box('gallery_form_meta_box', 'Gallery', array( $this, 'gallery_form_meta_box_handler' ), 'gallery', 'normal', 'default');
	    add_meta_box('gallery_media_meta_box', 'Media', array( $this, 'media_table_meta_box_handler' ), 'gallery media', 'normal', 'default');
	    add_meta_box('gallery_media_upload_meta_box', 'Upload Media', array( $this, 'media_upload_meta_box_handler' ), 'media upload', 'normal', 'default');
	}

	/**
	 * This function renders our custom meta box
	 * $item is row
	 *
	 * @param $item
	 */
	public function gallery_form_meta_box_handler($item) {
	    ?>
	    <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
	        <tbody>
	        <tr class="form-field">
	            <th valign="top" scope="row">
	                <label for="name"><?php _e('Name', 'custom_document_gallery')?></label>
	            </th>
	            <td>
	                <input id="name" name="name" type="text" style="width: 95%" value="<?php echo esc_attr($item['name'])?>"
	                       size="50" class="code" placeholder="<?php _e('Name of Gallery', 'custom_document_gallery')?>" required>
	            </td>
	        </tr>
	        </tbody>
	    </table>
	    <?php
	}

	/**
	 * This function renders our custom meta box for the media table
	 * $id is the id of the gallery
	 *
	 * @param $id
	 */
	public function media_table_meta_box_handler($id) {
	    ?>
	    <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
	        <tbody>
	        <tr class="form-field">
	            <td>
	                <?php
	                    $table = new CDG_Media_Table();
	                    $table->prepare_items();
	                ?>

	                <div class="wrap">
	                    <?php $table->views() ?>
	                    <form id="gallery-set-filter" method="GET">
	                        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
	                        <?php $table->display() ?>
	                    </form>
	                 </div>
	            </td>
	        </tr>
	        </tbody>
	    </table>
	    <?php
	}

	/**
	 * This function renders our custom meta box
	 * $item is row
	 *
	 * @param $item
	 */
	public function media_upload_meta_box_handler($item){
	    ?>
	    <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table"
	        <tbody>
	            <tr>
	                <form action="<?php CUSTOM_DOCUMENT_GALLERY_PLUGIN_NAME . '-gallery-edit'; ?>" method="post" enctype="multipart/form-data">
	                    <div id="file-upload-form">
	                        Select File(s) to Upload:
	                        <br>
	                        <input type="file" multiple="multiple" name="file[]">
	                        <br>
	                        <input type="submit" name="cdg-upload-files" value="Upload">
	                    </div>
	                </form>
	            </tr>
	        </tbody>
	    </table>
	    <?php
	}

	/**
	 * Simple function that validates data and retrieve bool on success
	 * and error message(s) on error
	 *
	 * @param $item
	 * @return bool|string
	 */
	private function validate_gallery($item) {
	    $messages = array();

	    if (empty($item['name'])) $messages[] = __('Name is required', 'custom_document_gallery');

	    if (empty($messages)) return true;
	    return implode('<br />', $messages);
	}

}