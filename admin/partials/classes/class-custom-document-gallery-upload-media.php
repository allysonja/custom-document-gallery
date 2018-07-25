<?php

/**
 * Class to upload media.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Custom_Document_Gallery
 * @subpackage Custom_Document_Gallery/admin
 */

/**
 * Class to upload media.
 *
 * @package    Custom_Document_Gallery
 * @subpackage Custom_Document_Gallery/admin/partials/classes
 * @author     Sonja Linton <sonjamw17@gmail.com>
 */

class Custom_Document_Gallery_Upload_Media {
	/**
	 * The ID of the gallery.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      int   $gallery_id    The ID of the gallery.
	 */
	protected $gallery_id;

	/**
	 * The base dir for uploads.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      int   $base_dir    The base dir for uploads.
	 */
	protected $base_dir;

	/**
	 * The base url for uploads.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      int   $base_url    The base url for uploads.
	 */
	protected $base_url;

	/**
	 * The array of files (for multi-upload).
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array   $file_ary    The array of files (for multi-upload).
	 */
	protected $file_ary;

	public function __construct($gallery_id) {
		$this->gallery_id = $gallery_id;
		$upload_dir = wp_upload_dir();
		$this->base_dir = $upload_dir["basedir"] . '/custom_document_gallery/';
		$this->base_url = $upload_dir["baseurl"] . '/custom_document_gallery/';
		$this->file_ary = $this->reArrayFiles($_FILES['file']);
		$this->upload_media($this->file_ary);

		// $this->echo_all_variables();
	}

	public function echo_all_variables() {
		var_dump($this->gallery_id);
		var_dump($this->base_dir);
		var_dump($this->file_ary);
	}

	/**
	 * Re-Array the files.
	 *
	 * Make the file_array for multi-part uploads easier to read and parse for the code to parse better.
	 *
	 * @since    1.0.0
	 */
	public function reArrayFiles(&$file_post) {

	    $file_ary = array();
	    $file_count = count($file_post['name']);
	    $file_keys = array_keys($file_post);

	    for ($i=0; $i<$file_count; $i++) {
	        foreach ($file_keys as $key) {
	            $file_ary[$i][$key] = $file_post[$key][$i];
	        }
	    }

	    return $file_ary;
	}

	public function upload_media($file_ary) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'document_media';

		foreach ($file_ary as $file) {
			$fileName = basename($file["name"]);

			$fileName = $this->resolve_duplicate($this->base_dir, $fileName);
			$targetFilePath = $this->base_dir . $fileName;
			$document_url = $this->base_url . $fileName;

			$fileType = strtolower(pathinfo($targetFilePath,PATHINFO_EXTENSION));
			if(isset($_POST["cdg-upload-files"]) && !empty($file["name"])){
			    // Allow certain file formats
			    $documentTypes = array('pdf','doc','docx');
			    if(in_array($fileType, $documentTypes)){

			        // Upload file to server
			        if(move_uploaded_file($file["tmp_name"], $targetFilePath)){

			        	$extension_pos = strrpos($fileName, '.');
						$thumbnailName = substr($fileName, 0, $extension_pos) . 'png';
						$thumbnailName = $this->resolve_duplicate($this->base_dir, $thumbnailName);

			        	$save_to = $this->base_dir . $thumbnailName;
			        	$thumbnail_url = $this->base_url . $thumbnailName;

			        	$img = new imagick($targetFilePath.'[0]');
			        	$img->setImageFormat('png');
			        	$img->writeImage($save_to);

			            // Insert image file name into database
			            $insert = $wpdb->query("INSERT into $table_name (name, created_date, document_gallery_id, document_url, thumbnail_url) VALUES ('" . $fileName . "', NOW(), '" . $this->gallery_id . "', '" . $document_url . "', '" . $thumbnail_url . "')");
			            if($insert){
			                $message = "The file " . $fileName . " has been uploaded successfully.";
			            }else{
			                $notice = "File upload failed, please try again.";
			            }
			        }else{
			            $notice = "Sorry, there was an error uploading your file.";
			        }
			    }else{
			        $notice = 'Sorry, only PDF, DOC, and DOCX files are allowed to upload.';
			    }
			}else{
			    $notice = 'Please select a file to upload.';
			}

			// Display status message
			if (!empty($notice)): ?>
		        <div id="notice" class="error"><p><?php echo $notice ?></p></div>
		        <?php endif;?>
		        <?php if (!empty($message)): ?>
		        <div id="message" class="updated"><p><?php echo $message ?></p></div>
		        <?php endif;?>
			<br>
		<?php
		}
	}

	// function resize_uploaded_image($file_name){
	// 	$original_image_path = $GLOBALS['original_dir'] . $file_name;
	// 	$resized_image_path = $GLOBALS['display_dir'] . $file_name;

	// 	list($img_width, $img_height, $type) = @getimagesize($original_image_path);
	// 	if ( !$img_width || !$img_height ) {

	//     }
	//     $max_width = 1200;   // in px
	//     $max_height = 1200;  // in px
	//     $image_quality = 60;  // in %

	//     $scale = min($max_width / $img_width, $max_height / $img_height);
	// 	@ini_set('memory_limit', '-1');
	// 	if ( !function_exists('imagecreatetruecolor') ) {
	// 	error_log('Function not found: imagecreatetruecolor');

	// 	return FALSE;
	// 	}
	// 	if($scale > 1){
	// 		$scale = 1;
	// 	}
	// 	$new_width = $img_width * $scale;
	// 	$new_height = $img_height * $scale;
	// 	$dst_x = 0;
	// 	$dst_y = 0;
	//     $new_img = @imagecreatetruecolor($new_width, $new_height);

	//     switch ( $type ) {
	//         case 2:
	//           $src_img = @imagecreatefromjpeg($original_image_path);
	//           $write_image = 'imagejpeg';
	//           // $image_quality = $image_quality;
	//           break;
	//         case 1:
	//           @imagecolortransparent($new_img, @imagecolorallocate($new_img, 0, 0, 0));
	//           $src_img = @imagecreatefromgif($original_image_path);
	//           $write_image = 'imagegif';
	//           $image_quality = NULL;
	//           break;
	//         case 3:
	//           @imagecolortransparent($new_img, @imagecolorallocate($new_img, 0, 0, 0));
	//           @imagealphablending($new_img, FALSE);
	//           @imagesavealpha($new_img, TRUE);
	//           $src_img = @imagecreatefrompng($original_image_path);
	//           $write_image = 'imagepng';
	//           $image_quality = 6;
	//           break;
	//         default:
	//           $src_img = NULL;
	//           break;
	//       }
	//       $src_img && @imagecopyresampled($new_img, $src_img, $dst_x, $dst_y, 0, 0, $new_width, $new_height, $img_width, $img_height) && $write_image($new_img, $resized_image_path, $image_quality);
	//       // Free up memory (imagedestroy does not delete files):
	//       @imagedestroy($src_img);
	//       @imagedestroy($new_img);
	//       @ini_restore('memory_limit');

	// }

	/**
	 * Resolve duplicate uploades to uploads directory.
	 *
	 * Use this function to resolve files with duplicant names in the uploads directory. For example, you can't have two files named "my_file", so this function will look for if there is already a file in the uploads folder called my_file, and will add a number to the end of it, until there isn't a duplicate file of the same name.
	 *
	 * @since    1.0.0
	 */
	private function resolve_duplicate($path, $orig_name, $i=1, $new_name=NULL){
		if($new_name){
			$file_name = $new_name;
		}else{
			$file_name = $orig_name;
		}
		if(file_exists($path . $file_name)){
			$extension_pos = strrpos($orig_name, '.');
			$new_file_name = substr($orig_name, 0, $extension_pos) . '(' . $i . ')' . substr($orig_name, $extension_pos);
			return $this->resolve_duplicate($path, $orig_name, $i+1, $new_file_name);
		}else{
			return $file_name;
		}
	}

}