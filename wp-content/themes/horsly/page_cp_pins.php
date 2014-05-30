<?php
/*
Template Name: _pins_settings
*/

if (!is_user_logged_in()) { wp_redirect(wp_login_url($_SERVER['REQUEST_URI'])); exit; }

if (!current_user_can('edit_posts')) { wp_redirect(home_url('/')); exit; }

error_reporting(0); get_header(); global $user_ID;

if ($_GET['i']) {  //edit pin
	$pin_id = intval($_GET['i']);
	$pin_info = get_post($pin_id);
	$imgsrc = wp_get_attachment_image_src(get_post_thumbnail_id($pin_info->ID),'medium');
	$terms = get_the_terms($pin_info->ID, 'board');
	
	if ($terms) {
		foreach ($terms as $term) {
			$board_id = $term->term_id;
		}
	}
	
	if (($pin_info->post_author == $user_ID || current_user_can('edit_others_posts')) && $pin_info->post_type == 'post') {
	?>
	<div class="container-fluid">
		<div class="row-fluid">
			<div class="span4"></div>
	
			<div class="span4 usercp-wrapper usercp-pins">
				<h1><?php _e('Edit your story..', 'ipin') ?></h1>
				
				<div class="error-msg"></div>
				
				<br />
				
				<div id="pin-upload-postdata-wrapper">
					<div class="postdata-box-photo"><img id="thumbnail" src="<?php echo $imgsrc[0]; ?>" /></div>
					<form id="pin-edit-form">
                                                <?php if ( has_action('es_theme_pin_edit_input_before_title') ) { do_action('es_theme_pin_edit_input_before_title', $pin_id); } ?>
						<textarea placeholder="<?php _e('Describe your story...', 'ipin'); ?>"><?php echo $pin_info->post_title; ?></textarea>
                                                <?php if ( has_action('es_theme_pin_edit_input_after_title') ) { do_action('es_theme_pin_edit_input_after_title', $pin_id); } ?>
						<input type="text" name="source" id="source" value="<?php echo get_post_meta($pin_info->ID, '_Photo Source', true); ?>" placeholder="<?php _e('Source...', 'ipin'); ?>" />
						
						
						<?php wp_dropdown_categories(array('taxonomy' => 'board', 'parent' => get_user_meta($pin_info->post_author, '_Board Parent ID', true), 'hide_empty' => 0, 'name' => 'board', 'hierarchical' => true, 'selected' => $board_id, 'order' => 'DESC')); ?>
						<input type="text" class="board-add-new" id="board-add-new" placeholder="<?php _e('Enter new story title', 'ipin'); ?>" />
						<?php wp_dropdown_categories(array('show_option_none' => __('Category for New Board', 'ipin'), 'exclude' => of_get_option('blog_cat_id') . ',1', 'hide_empty' => 0, 'name' => 'board-add-new-category', 'orderby' => 'name')); ?>
						
						
						<div class="clearfix"></div>
                                                <?php if ( has_action('es_theme_pin_edit_input_before_pin_button') ) { do_action('es_theme_pin_edit_input_before_pin_button', $pin_id); } ?>
						<input class="btn btn-primary btn-large" type="submit" name="pinit" id="pinit" value="<?php _e('Save', 'ipin'); ?>" /> 			
						<img class="ajax-loader-add-pin hide" src="<?php echo get_template_directory_uri(); ?>/img/ajax-loader-2.gif" />
					</form>
				</div>
				<hr style="border-top: 1px solid #ccc" />
				<button class="btn ipin-delete-pin" type="button"><?php _e('Delete story', 'ipin') ?></button>
			</div>
	
			<div class="span4"></div>
		</div>
	
		<div id="scrolltotop"><a href="#"><i class="fa fa-chevron-up"></i><br /><?php _e('Top', 'ipin'); ?></a></div>

		<script>
		//placeholder for source field so that it also works on IE
		(function ($) {
			$.fn.placeHolder = function () {
				var input = $('#source');
				var text = input.attr('placeholder');
				if (text) input.val(text).css({
					color: '#a5a5a5'
				});
				input.focus(function () {
					if (input.val() === text) input.css({
						color: '#a5a5a5'
					}).selectRange(0, 0).one('keydown', function () {
						input.val("").css({
							color: 'black'
						});
					});
				});
				input.blur(function () {
					if (input.val() == "" || input.val() === text) input.val(text).css({
						color: '#a5a5a5'
					});
				});
				input.keyup(function () {
					if (input.val() == "") input.val(text).css({
						color: '#a5a5a5'
					}).selectRange(0, 0).one('keydown', function () {
						input.val("").css({
							color: 'black'
						});
					});
				});
				input.mouseup(function () {
					if (input.val() === text) input.selectRange(0, 0);
				});
			};
	
			$.fn.selectRange = function (start, end) {
				return this.each(function () {
					if (this.setSelectionRange) {
						this.setSelectionRange(start, end);
					} else if (this.createTextRange) {
						var range = this.createTextRange();
						range.collapse(true);
						range.moveEnd('character', end);
						range.moveStart('character', start);
						range.select();
					}
				});
			};

			if ($('#source').val() == '') {
				$('#source').placeHolder();
			}
		})(jQuery);
		</script>
	</div>
	<?php } else { ?>
	<div class="row-fluid">			
		<div class="span12">
			<div class="bigmsg">
				<h2><?php _e('No pins found.', 'ipin'); ?></h2>
			</div>
		</div>
	</div>

<?php }
} else if ($_GET['m'] == 'bm') {  //bookmarklet
	$imgsrc = esc_url_raw(urldecode('http' . $_GET['imgsrc']));
	$source = esc_url_raw(urldecode('http' . $_GET['source']));
	$title = esc_textarea(html_entity_decode(rawurldecode(stripslashes($_GET['title'])), ENT_QUOTES, 'UTF-8'));
	
	require_once(ABSPATH . 'wp-admin/includes/image.php');
	require_once(ABSPATH . 'wp-admin/includes/file.php');
	require_once(ABSPATH . 'wp-admin/includes/media.php');
		
	if (function_exists("curl_init")) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $imgsrc);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$image = curl_exec($ch);
		curl_close($ch);
	} elseif (ini_get("allow_url_fopen")) {
		$image = file_get_contents($imgsrc, false, $context);
	}

	if (!$image) {
		$error = 'error';
	}

	$filename = time() . substr(str_shuffle("abcde12345"), 0, 5);
	$file_array['tmp_name'] = WP_CONTENT_DIR . "/" . $filename . '.tmp';
	$filetmp = file_put_contents($file_array['tmp_name'], $image);
	
	if (!$filetmp) {
		@unlink($file_array['tmp_name']);
		$error = 'error';
	}

	$imageTypes = array (
		1, //IMAGETYPE_GIF
		2, //IMAGETYPE_JPEG
		3 //IMAGETYPE_PNG
	);

	$imageinfo = getimagesize($file_array['tmp_name']);
	$width = @$imageinfo[0];
	$height = @$imageinfo[1];
	$type = @$imageinfo[2];
	$mime = @$imageinfo ['mime'];

	if (!in_array ( $type, $imageTypes)) {
		@unlink($file_array['tmp_name']);
		$error = 'error';
	}

	if ($width <= 1 && $height <= 1) {
		@unlink($file_array['tmp_name']);
		$error = 'error';
	}

	if($mime != 'image/gif' && $mime != 'image/jpeg' && $mime != 'image/png') {
		@unlink($file_array['tmp_name']);
		$error = 'error';
	}
	
	switch($type) {
		case 1:
			$ext = '.gif';
			break;
		case 2:
			$ext = '.jpg';
			break;
		case 3:
			$ext = '.png';
			break;
	}
	$file_array['name'] = $filename . $ext;

	$attach_id = media_handle_sideload($file_array, $post_id);

	if (is_wp_error($attach_id)) {
		@unlink($file_array['tmp_name']);
		$error = 'error';
	}
		
	if ($error == 'error') {
	?>
		<div class="container-fluid">
			<div class="row-fluid">
				<div class="span4"></div>
				<div class="span4 usercp-wrapper usercp-pins">
					<div class="error-msg">
						<div class="alert">
						<strong><?php _e('Invalid image file.', 'ipin'); ?></strong>
						</div>
					</div>
				</div>
				<div class="span4"></div>
			</div>
		</div>
	<?php
	} else {
			$thumbnail = wp_get_attachment_image_src($attach_id, 'medium');
			?>
			<div class="container-fluid">
				<div class="row-fluid">
					<div class="span4"></div>
			
					<div class="span4 usercp-wrapper usercp-pins">
						<h1><?php _e('Add a story', 'ipin') ?></h1>
						
						<div class="error-msg"></div>
						
						<br />
						
						<div id="pin-upload-postdata-wrapper">
						<div class="postdata-box-photo"><img id="thumbnail" src="<?php echo $thumbnail[0]; ?>" /></div>
						<form id="pin-postdata-form">
                                                        <?php if ( has_action('es_theme_pin_insert_input_before_title') ) { do_action('es_theme_pin_insert_input_before_title'); } ?>
							<textarea placeholder="<?php _e('Describe your story...', 'ipin'); ?>"><?php echo $title; ?></textarea>
                                                        <?php if ( has_action('es_theme_pin_insert_input_after_title') ) { do_action('es_theme_pin_insert_input_after_title'); } ?>
							<?php  
							$board_parent_id = get_user_meta($user_ID, '_Board Parent ID', true);
							$board_children_count = wp_count_terms('board', array('parent' => $board_parent_id));
							if (is_array($board_children_count) || $board_children_count == 0) {
								echo '<span id="noboard">';
								wp_dropdown_categories(array('show_option_none' => __('Add a new board first...', 'ipin'), 'taxonomy' => 'board', 'parent' => $board_parent_id, 'hide_empty' => 0, 'name' => 'board', 'hierarchical' => true));
								echo '</span>';
								$noboard = 'yes';
							} else {
								wp_dropdown_categories(array('taxonomy' => 'board', 'parent' => $board_parent_id, 'hide_empty' => 0, 'name' => 'board', 'hierarchical' => true, 'orderby' => 'name')); // edited by Marveller: Pin popup board dropdown
							}
							?>
							<input type="text" class="board-add-new" id="board-add-new" placeholder="<?php _e('Enter new board title', 'ipin'); ?>" />
							<?php wp_dropdown_categories(array('show_option_none' => __('Category for new board', 'ipin'), 'exclude' => of_get_option('blog_cat_id') . ',1', 'hide_empty' => 0, 'name' => 'board-add-new-category', 'orderby' => 'name')); ?>
							<!--<a id="pin-postdata-add-new-board" class="btn btn-mini pull-right"><?php _e('Add new board...', 'ipin'); ?></a>-->
							<input type="hidden" value="<?php echo $attach_id; ?>" name="attachment-id" id="attachment-id" />
							<input type="hidden" name="photo_data_source" id="photo_data_source" value="<?php echo $source ?>" />
							<div class="clearfix"></div>
                                                        <?php if ( has_action('es_theme_pin_insert_input_before_pin_button') ) { do_action('es_theme_pin_insert_input_before_pin_button'); } ?>
							<input <?php if ($noboard == 'yes' || $title == '') { echo ' disabled="disabled"'; } ?> class="btn btn-primary btn-large" type="submit" name="pinit" id="pinit" value="<?php _e('Share', 'ipin'); ?>" /> 
							<img class="ajax-loader-add-pin hide" src="<?php echo get_template_directory_uri(); ?>/img/ajax-loader-2.gif" />
						</form>
					</div>
					</div>
			
					<div class="span4"></div>
				</div>
			
				<div id="scrolltotop"><a href="#"><i class="fa fa-chevron-up"></i><br /><?php _e('Top', 'ipin'); ?></a></div>
			</div>
		<?php
	}
} else { ?>

<div class="container-fluid">
	<div class="row-fluid">
		<div class="span4"></div>

		<div class="span4 usercp-wrapper usercp-pins">
			<h1><?php _e('Add a story', 'ipin') ?></h1>
			
			<div class="error-msg hide"></div>

			<br />

			<div id="pin-upload-from-computer-wrapper" class="hero-unit">
				<h4><?php _e('From computer', 'ipin'); ?></h4>
				<form id="pin_upload_form" method="post" action="#" enctype="multipart/form-data">
					<input type="file" name="pin_upload_file" id="pin_upload_file" accept="image/*" /> 
					<input type="hidden" value="<?php echo wp_create_nonce('upload_pin'); ?>" name="_wpnonce" />
					<input type="hidden" name="mode" id="mode" value="computer" />
					<input type="hidden" name="action" id="action" value="ipin-upload-pin" />
					<img class="ajax-loader-add-pin hide" src="<?php echo get_template_directory_uri(); ?>/img/ajax-loader-2.gif" />
				</form>
			</div>
			
			<div id="pin-upload-from-web-wrapper" class="hero-unit">
				<h4><?php _e('From web', 'ipin'); ?></h4>
				<form id="pin_upload_web_form" method="post" action="#">
					<input type="text" name="pin_upload_web" id="pin_upload_web" style="margin:0;" placeholder="http://" />
					<input type="hidden" value="<?php echo wp_create_nonce('upload_pin'); ?>" name="_wpnonce" />
					<input type="hidden" name="mode" id="mode" value="web" />
					<input type="hidden" name="action" id="action" value="ipin-upload-pin" />
					<input class="fetch-pin" type="submit" name="fetch" id="fetch" value="Fetch" />
					<img class="ajax-loader-add-pin hide" src="<?php echo get_template_directory_uri(); ?>/img/ajax-loader-2.gif" />
				</form>
			</div>
			
			<div id="bookmarklet" class="hero-unit">
				<h4><?php _e('Story bookmarklet', 'ipin'); ?></h4>
				<span class="badge badge-warning"><a onClick='javascript:return false' href="javascript:var ipinsite='<?php bloginfo('name'); ?>',ipinsiteurl='<?php echo home_url('/'); ?>';(function(){if(window.ipinit!==undefined){ipinit();}else{document.body.appendChild(document.createElement('script')).src='<?php echo get_template_directory_uri(); ?>/js/ipinit.js';}})();"><?php bloginfo('name'); ?></a></span>
				<p><small><?php _e('Be a part of the Hors.ly community and submit your favorite Hors.ly stories. Drag the orange button to your bookmarks toolbar. Then, click to share a story of an image or any articles and videos from any website. You can share anything that you love about the horse world.', 'ipin'); ?></small></p>
			</div>
			
			<div id="pin-upload-postdata-wrapper" class="hide">
				<div class="postdata-box-photo"><img id="thumbnail" /></div>
				<form id="pin-postdata-form">
					<textarea placeholder="<?php _e('Describe your story...', 'ipin'); ?>"></textarea>
					<?php  
					$board_parent_id = get_user_meta($user_ID, '_Board Parent ID', true);
					$board_children_count = wp_count_terms('board', array('parent' => $board_parent_id));
					if (is_array($board_children_count) || $board_children_count == 0) {
						echo '<span id="noboard">';
						wp_dropdown_categories(array('show_option_none' => __('Add a new board first...', 'ipin'), 'taxonomy' => 'board', 'parent' => $board_parent_id, 'hide_empty' => 0, 'name' => 'board', 'hierarchical' => true));
						echo '</span>';
					} else {
						wp_dropdown_categories(array('taxonomy' => 'board', 'parent' => $board_parent_id, 'hide_empty' => 0, 'name' => 'board', 'hierarchical' => true, 'orderby' => 'name')); // edited by Marveller: Pin popup board dropdown
					}
					?>
					<input type="text" class="board-add-new" id="board-add-new" placeholder="<?php _e('Enter new board title', 'ipin'); ?>" />
					<?php wp_dropdown_categories(array('show_option_none' => __('Category for new board', 'ipin'), 'exclude' => of_get_option('blog_cat_id') . ',1', 'hide_empty' => 0, 'name' => 'board-add-new-category', 'orderby' => 'name')); ?>
					<a id="pin-postdata-add-new-board" class="btn btn-mini pull-right"><?php _e('Add new board...', 'ipin'); ?></a>
					<input type="hidden" value="" name="attachment-id" id="attachment-id" />
						<div class="clearfix"></div>
                                                        <?php if ( has_action('es_theme_pin_insert_input_before_pin_button') ) { do_action('es_theme_pin_insert_input_before_pin_button'); } ?>
							<input <?php if ($noboard == 'yes' || $title == '') { echo ' disabled="disabled"'; } ?> class="btn btn-primary btn-large" type="submit" name="pinit" id="pinit" value="<?php _e('Share', 'ipin'); ?>" /> 
												
					
					<img class="ajax-loader-add-pin hide" src="<?php echo get_template_directory_uri(); ?>/img/ajax-loader-2.gif" />
				</form>
			</div>
		</div>

		<div class="span4"></div>
	</div>

	<div id="scrolltotop"><a href="#"><i class="fa fa-chevron-up"></i><br /><?php _e('Top', 'ipin'); ?></a></div>
</div>
<?php } ?>

<div class="modal hide" id="delete-pin-modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-body">
		<h4><?php _e('Are you sure you want to permanently delete this story?', 'ipin'); ?></h4>
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal"><strong><?php _e('Cancel', 'ipin'); ?></strong></a>
		<a href="#" id="ipin-delete-pin-confirmed" class="btn btn-danger" data-pin_id="<?php echo $pin_id; ?>" data-pin_author="<?php echo $pin_info->post_author; ?>"><strong><?php _e('Delete Story', 'ipin'); ?></strong></a> 
		<img class="ajax-loader-delete-pin hide" src="<?php echo get_template_directory_uri(); ?>/img/ajax-loader-2.gif" />
	</div>
</div>

<script>
jQuery(document).ready(function($) {
	$('#pin-edit-form textarea').focus();
});
</script>

<?php 
wp_enqueue_script('jquery-form', array('jquery'), false, true);
get_footer();
?>
