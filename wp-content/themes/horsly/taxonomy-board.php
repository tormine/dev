<?php get_header(); global $user_ID; ?>

<div class="container-fluid">
	<div class="row-fluid">
		<div class="span4"></div>
		<div class="span4 grand-title-wrapper">
			<?php 
			$board_info = $wp_query->get_queried_object();
			$board_user = $post->post_author;
			if (!isset($board_user)) {
				$board_user = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM $wpdb->usermeta WHERE meta_key ='_Board Parent ID' AND meta_value = %s LIMIT 1", $board_info->parent));
			}
			$user_info = get_user_by('id', $board_user);
			?>
			<h1>
			<?php 
				if ($board_info->parent == 0) {
					echo __('Stories From All', 'ipin') . ' ' . $user_info->display_name . '&#39;s ' . __('Boards', 'ipin');
				} else {
					echo $board_info->name;
				}
			?>
			</h1>

			<div class="grand-title-subheader">
				<div class="pull-right">
					<?php 
					if ($board_user != $user_ID) {
					?>
					<button class="btn follow ipin-follow<?php if ($followed = ipin_followed($board_info->term_id)) { echo ' disabled'; } ?>" data-author_id="<?php echo $board_user; ?>" data-board_id="<?php echo $board_info->term_id;  ?>" data-board_parent_id="<?php echo $board_info->parent; ?>" type="button"><?php if (!$followed) { _e('Follow', 'ipin'); } else { _e('Unfollow', 'ipin'); } ?></button>
					<?php 
					} 
					if ($board_info->parent && ($board_user == $user_ID || current_user_can('edit_others_posts'))) { ?>
					<a class="btn follow" href="<?php echo home_url('/boards-settings/?i=') . $board_info->term_id; ?>"><?php _e('Edit' , 'ipin'); ?></a>
					<?php } ?>
				</div>
			
				<div class="pull-left">
					<a href="<?php echo home_url('/user/') . $user_info->user_login; ?>/"><?php echo get_avatar($user_info->ID, '32'); ?></a> 
					<a href="<?php echo home_url('/user/') . $user_info->user_login; ?>/"><?php echo $user_info->display_name; ?></a>
				</div>
				
				<div class="clearfix"></div>
			</div>

		</div>

		<div class="span4"></div>
	</div>
	
<?php 
get_template_part('index', 'masonry');
get_footer();
?>
