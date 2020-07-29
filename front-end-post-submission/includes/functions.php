<?php

function wpfeps_add_jquery_libraries() {

     wp_register_script('wpfeps-jquery-validation-plugin', 'http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js', array('jquery'));

    // Enqueueing Scripts to the head section
    wp_enqueue_script('jquery');
    wp_enqueue_script('wpfeps-jquery-validation-plugin');
	
	wp_enqueue_script( 'wpfeps_ajax_js', WPFEPS_PLUGIN_URL . '/assets/js/ajax.js', array( 'jquery', 'wpfeps-jquery-validation-plugin' ), null, true );
	
	wp_localize_script( 'wpfeps_ajax_js', 'wpfeps_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
	
   }
add_action( 'wp_enqueue_scripts', 'wpfeps_add_jquery_libraries' );

/********create shortcode [wpfeps]***********/

add_shortcode('wpfeps','wpfeps_func_form');

function wpfeps_func_form()
{
	//check user login or not
	if ( is_user_logged_in() ) {
	$args = array(
   'public'   => true
	);
	$post_types = get_post_types($args);
	//print_r($post_types);
	$htmlform='<div class="row">';
	$htmlform .='<form id="create_post" name="create_post" action="create_post" method="post" enctype="multipart/form-data">';
	$htmlform .='<div class="form-group">
    <label for="title">Title</label>
    <input type="text" class="form-control" id="title" name="title">
	  </div>';
	 $htmlform .='<div class="form-group">
	<label for="posttype">Type</label>
	<select class="form-control" id="ptype" name="ptype">
      <option value="">Select Type</option>';
	if ( $post_types ) {
		foreach ( $post_types  as $post_type ) {
      $htmlform .='<option value="' . $post_type . '">' . $post_type . '</option>';
		}
	}
      
    $htmlform .='</select>
	  </div>';
	$htmlform .='<div class="form-group">
		<label for="description">Description</label>
		<textarea class="form-control" id="description" name="description" rows="4"></textarea>
	  </div>';
	 $htmlform .='<div class="form-group">
		<label for="excerpt">Excerpt</label>
		<textarea class="form-control" id="excerpt" name="excerpt" rows="2"></textarea>
	  </div>';
	
	$htmlform .='<div class="form-group">
    <label for="featured_img">featured Image</label>
    <input type="file" class="form-control-file" id="featured_img" name="featured_img">
	</div>';
	$htmlform .='<div id="status"></div>';
	$htmlform .='<div><input type="hidden" class="form-control" id="security" value="'. wp_create_nonce( "wpfeps-security") .'" name="security"></div>';
	$htmlform .='<button type="submit" class="btn btn-primary">Submit</button>';
	$htmlform .='</form>';
	$htmlform .='</div>';
	}else{
	$htmlform .='<div class="row"><div class="alert alert-danger" role="alert">
	  You are not logged! 
	</div></div>';	
	}
	return $htmlform;
}

/*******Action fornt end post********/
add_action( 'wp_ajax_wpfeps_create_post', 'wpfeps_create_post_function' );
function wpfeps_create_post_function() {
	global $wpdb;
	$authorId = get_current_user_id();
	
	//Check security
	check_ajax_referer( 'wpfeps-security', 'security' );
	
	if($_POST['title'] && $authorId){
	// Create post object
	$wpfeps_post = array(
	  'post_title'    => wp_strip_all_tags( $_POST['title'] ),
	  'post_content'  => $_POST['description'],
	  'post_excerpt'  => $_POST['excerpt'],
	  'post_type'  => $_POST['ptype'],
	  'post_author'   => $authorId
	);
	 
	// Insert the post into the database
	$wpfeps_post_id = wp_insert_post( $wpfeps_post );
	
	if($wpfeps_post_id){
		$fileUpdate = wpfeps_file_upload($_FILES['featured_img'],$wpfeps_post_id);
		if($fileUpdate==0){
			//echo 'Post submit successfull,But sorry image not update, code error';
			echo json_encode(array('status'=>true,'message'=>'Post submit successfull,But sorry image not update, code error'));
		}else{
			//echo 'Post submit successfull';
			echo json_encode(array('status'=>true,'message'=>'Post submit successfull.'));
		}
	}else{
		//echo 'Sorry data not update, code error';
		echo json_encode(array('status'=>false,'message'=>'Sorry data not update, code error.'));
		
	}
	
	}else{
		//echo 'Sorry fields are empty!';
		echo json_encode(array('status'=>false,'message'=>'Sorry fields are empty.'));
	}
	wp_die();
}

/********File upload********/
function wpfeps_file_upload($file=array(),$postId=0){	
 //check $file is empty or not
 if(!empty($file)){
 // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
 require_once( ABSPATH . 'wp-admin/includes/image.php' );

 $upload = wp_upload_bits($file['name'] , null, file_get_contents($file['tmp_name'], FILE_USE_INCLUDE_PATH));
 
 // check and return file type
 $imageFile = $upload['file'];
 $wpFileType = wp_check_filetype($imageFile, null);
 
 // Attachment attributes for file
 $attachment = array(
 'post_mime_type' => $wpFileType['type'],  // file type
 'post_title' => sanitize_file_name($imageFile),  // sanitize and use image name as file name
 'post_content' => '',  // could use the image description here as the content
 'post_status' => 'inherit'
 );
 
 // insert and return attachment id
 $attachmentId = wp_insert_attachment( $attachment, $imageFile, $postId );
 
 // insert and return attachment metadata
 $attachmentData = wp_generate_attachment_metadata( $attachmentId, $imageFile);
 
 // update and return attachment metadata
 wp_update_attachment_metadata( $attachmentId, $attachmentData );
 
 set_post_thumbnail( $postId, $attachmentId );
 
 $status = $attachmentId;
	 
 }else{
	 
	 $status = 0;
 }
 
 return $status;
}
?>