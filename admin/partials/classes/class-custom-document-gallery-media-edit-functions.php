<?php

/**
 * Media editing functions.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Custom_Document_Gallery
 * @subpackage Custom_Document_Gallery/admin
 */

/**
 * Media editing functions.
 *
 * @package    Custom_Document_Gallery
 * @subpackage Custom_Document_Gallery/admin/partials/classes
 * @author     Sonja Linton <sonjamw17@gmail.com>
 */

class Custom_Document_Gallery_Media_Edit {

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
	 */
	private function load_dependancies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'classes/class-custom-document-gallery-upload-media.php';
	}

	/**
	 * Form page handler checks is there some data posted and tries to save it
	 * Also it renders basic wrapper in which we are calling meta box render
	 */
	private function page_handler() {
	    global $wpdb;
	    $table_name = $wpdb->prefix . 'document_media'; // do not forget about tables prefix

	    $message = '';
	    $notice = '';

	    // this is default $item which will be used for new records
	    $default = array(
	        'id' => 0,
	        'name' => '',
	        'document_gallery_id' => 0,
	        'thumbnail_url' => '',
	    );

	    // print_r($_REQUEST);
	    // here we are verifying does this request is post back and have correct nonce
	    if ( isset($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
	        // print_r($_REQUEST);
	        // combine our default item with request params
	        $item = shortcode_atts($default, $_REQUEST);
	        // validate data, and if all ok save item to database
	        // if id is zero insert otherwise update
	        $item_valid = $this->validate_media($item);
	        if (isset($_GET['gallery-id'])) {
	            $item['document_gallery_id'] = $_GET['gallery-id'];
	        }
	        if ($item_valid === true) {
	            if (isset($_REQUEST['save_media'])){
	                $result = $wpdb->update($table_name, $item, array('id' => $item['id']));
	                if ($result) {
	                    $message = __('Item was successfully updated', 'custom-document-gallery');
	                } else {
	                    $notice = __('There was an error while updating item', 'custom-document-gallery');
	                }
	            } else if (isset($_REQUEST['upload-thumb'])) {
	                $query = "SELECT thumbnail_url FROM " . $table_name . " WHERE id =" . $item['id'];
	                $result = $wpdb->get_results($query, ARRAY_A);
	                $item['thumbnail_url'] = $result[0]['thumbnail_url'];
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
	                $notice = __('Item not found', 'custom-document-gallery');
	            }
	        }
	    }

	    // here we adding our custom meta box
	    $this->add_meta_boxes();

	    ?>
	    <div class="wrap">
	        <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
	        <!-- <p><?php // var_dump($item); ?></p> -->
        	<h2><?php _e('Edit Media', 'custom_document_gallery')?> <a class="add-new-h2"
                                    href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=' . $this->plugin_name . '-gallery-edit&id=' . $item['document_gallery_id']);?>"><?php _e('back to edit gallery', 'custom_document_gallery')?></a>
        	</h2>

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
	                        <?php do_meta_boxes('media', 'normal', $item); ?>
	                        <input type="submit" value="<?php _e('Save', 'custom_document_gallery')?>" id="save_media" class="button-primary" name="save_media">
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
		add_meta_box('media_form_meta_box', 'Media', array( $this, 'media_form_meta_box_handler' ), 'media', 'normal', 'default');
	}

	/**
	 * This function renders our custom meta box
	 * $item is row
	 *
	 * @param $item
	 */
	public function media_form_meta_box_handler($item) {
	    ?>
	    <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
	        <tbody>
	        	<tr class="form-field">
		            <th valign="top" scope="row">
		                <label for="name"><?php _e('Name', 'custom_document_gallery')?></label>
		            </th>
		            <td>
		                <input id="name" name="name" type="text" style="width: 95%" value="<?php echo esc_attr($item['name'])?>"
		                       size="50" class="code" placeholder="<?php _e('Name of Media Item', 'custom_document_gallery')?>" required>
		            </td>
		        </tr>
		        <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
	                <tbody>
	                    <tr>
		                    <th valign="top" scope="row">
		                        <label for="thumbnail"><?php _e('Thumbnail', 'custom-document-gallery')?></label>
		                    </th>
		                    <td>
		                    	<input type="hidden" id="thumbnail_url" name="thumbnail_url" value="<?php echo $item['thumbnail_url']; ?>">
		                        <img src="<?php echo esc_attr($item['thumbnail_url']); ?>" width="80px" height= "80px" />
		                    </td>
		                    <td>
		                        <button type="button" id="thumbnail-button" onclick="uploadAlternateThumb()">Upload alternate thumbnail</button>
		                        <div id="new-thumbnail" style="display: none;">
		                            <input type="file" multiple="multiple" name="file[]">
		                            <input type="submit" name="cdg-upload-thumb" value="Upload" class="button-primary">
		                        </div>
		                    </td>
		                </tr>
	                </tbody>
	            </table>
	        </tbody>
	    </table>
	    <script type="text/javascript">
	        function uploadAlternateThumb() {
	            document.getElementById('new-thumbnail').style.display = 'block';
	            document.getElementById('thumbnail-button').style.display = 'none';
	        }
	    </script>
	    <?php
	}

	/**
	 * Simple function that validates data and retrieve bool on success
	 * and error message(s) on error
	 *
	 * @param $item
	 * @return bool|string
	 */
	private function validate_media($item) {
	    $messages = array();

	    if (empty($item['name'])) $messages[] = __('Name is required', 'custom_document_gallery');

	    if (empty($messages)) return true;
	    return implode('<br />', $messages);
	}

}