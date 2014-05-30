<?php
/*
Template Name: _boards_settings
*/

if (!is_user_logged_in()) { wp_redirect(wp_login_url($_SERVER['REQUEST_URI'])); exit; }

if (!current_user_can('edit_posts')) { wp_redirect(home_url('/')); exit; }

get_header();

if ($_GET['i']) { 
	$board_id = intval($_GET['i']);
	$board_info = get_term_by('id', $board_id, 'board');
	if ($board_info && $board_info->parent != 0 && ($board_info->parent == get_user_meta($user_ID, '_Board Parent ID', true) || current_user_can('edit_others_posts'))) {
	?>
	<div class="container-fluid">
		<div class="row-fluid">
			<div class="span4"></div>
	
			<div class="span4 usercp-wrapper">

				<h1><?php _e('Edit', 'ipin') ?> / <?php echo $board_info->name ?></h1>
				
				<div class="error-msg"></div>
				
				<form id="add_board_form">
					

					<label><?php _e('Category', 'ipin'); ?><br />
					<?php wp_dropdown_categories(array('show_option_none' => __('Select a Category', 'ipin'), 'exclude' => of_get_option('blog_cat_id') . ',1', 'hide_empty' => 0, 'name' => 'category-id', 'orderby' => 'name', 'selected' => $board_info->description)); ?></label>

					<br />
					<input type='hidden' value='<?php echo $board_info->term_id; ?>' name='term-id' id='term-id' />
					<input type='hidden' value='edit' name='mode' id='mode' />
					<input class="btn btn-primary btn-large" type="submit" name="submit" id="submit" value="<?php _e('Save Settings', 'ipin'); ?>" /> 
					<img class="ajax-loader hide" src="<?php echo get_template_directory_uri(); ?>/img/ajax-loader-2.gif" />
				</form>
				<hr style="border-top: 1px solid #ccc" />
				<button class="btn ipin-delete-board" type="button"><?php _e('Delete Board', 'ipin') ?></button>
			</div>
	
			<div class="span4"></div>
		</div>
	
		<div id="scrolltotop"><a href="#"><i class="icon-chevron-up"></i><br /><?php _e('Top', 'ipin'); ?></a></div>
	</div>
	<?php } else { ?>
	<div class="row-fluid">			
		<div class="span12">
			<div class="bigmsg">
				<h2><?php _e('No boards found.', 'ipin'); ?></h2>
			</div>
		</div>
	</div>

<?php }
} else { ?>

<div class="container-fluid">
	<div class="row-fluid">
		<div class="span4"></div>

		<div class="span4 usercp-wrapper">
						<label><?php _e('Category', 'ipin'); ?>
				<?php wp_dropdown_categories(array('show_option_none' => __('Select a Category', 'ipin'), 'exclude' => of_get_option('blog_cat_id') . ',1', 'hide_empty' => 0, 'name' => 'category-id', 'orderby' => 'name')); ?></label>


				<br />
				<input type='hidden' value='add' name='mode' id='mode' />
				<input class="btn btn-primary btn-large " type="submit" name="submit" id="submit" value="<?php _e('Add Board', 'ipin'); ?>" /> 
				 <img class="ajax-loader hide" src="<?php echo get_template_directory_uri(); ?>/img/ajax-loader-2.gif" />
			</form>
		</div>

		<div class="span4"></div>
	</div>

	<div id="scrolltotop"><a href="#"><i class="fa fa-chevron-up"></i><br /><?php _e('Top', 'ipin'); ?></a></div>
</div>
<?php } ?>

<div class="modal hide" id="delete-board-modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-body">
		<h4><?php _e('All stories on this board will also be deleted.', 'ipin')?> <br /> <?php _e('Are you sure you want to permanently delete this board?', 'ipin'); ?></h4>
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal"><strong><?php _e('Cancel', 'ipin'); ?></strong></a>
		<a href="#" id="ipin-delete-board-confirmed" class="btn btn-danger" data-board_id="<?php echo $board_info->term_id; ?>"><strong><?php _e('Delete Board', 'ipin'); ?></strong></a> 
		<img class="ajax-loader-delete-board hide" src="<?php echo get_template_directory_uri(); ?>/img/ajax-loader-2.gif" />
	</div>
</div>

<script>
jQuery(document).ready(function($) {
	$('#board-title').focus();
});
</script>

<?php get_footer(); ?>
