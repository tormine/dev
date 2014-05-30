<div id="sidebar-left" class="sidebar">
<?php if (is_single() && !in_category(intval(of_get_option('blog_cat_id')))) { ?>
	<div class="sidebar-left-single<?php if (!is_active_sidebar('sidebar-l')) { echo ' position-fixed'; } ?>">
	<?php
		$boards = get_the_terms($post->ID, 'board');
	
		foreach ($boards as $board) {
			$board_id = $board->term_id;
			$board_parent_id = $board->parent;
			$board_name = $board->name;
			$board_count = $board->count;
			$board_slug = $board->slug;
		}
	
		$loop_board_args = array(
			'posts_per_page' => 5,
			'tax_query' => array(
				array(
					'taxonomy' => 'board',
					'field' => 'id',
					'terms' => $board_id
				)
			)
		);
		
		$loop_board = new WP_Query($loop_board_args);
		$board_link = get_term_link($board_slug, 'board');
		?>
		<div class="board-mini">
			<h4><a href="<?php echo $board_link; ?>"><?php echo $board_name; ?></a></h4>
			<p><?php echo $board_count ?> <?php _e('pins','ipin') ?></p>
			
			<div class="board-photo-frame">
				<a href="<?php echo $board_link; ?>">
				<?php
				$count= 1;
				$post_array = array();
				while ($loop_board->have_posts()) : $loop_board->the_post();	
					if ($count == 1) {
						$imgsrc = wp_get_attachment_image_src(get_post_thumbnail_id($loop_board->ID),'medium');
						$imgsrc = $imgsrc[0];
						array_unshift($post_array, $imgsrc);
					} else {
						$imgsrc = wp_get_attachment_image_src(get_post_thumbnail_id($loop_board->ID),'thumbnail');
						$imgsrc = $imgsrc[0];
						array_unshift($post_array, $imgsrc);
					}
					$count++;
				endwhile;
				wp_reset_query();
				
				$count = 1;
		
				$post_array_final = array_fill(0, 5, '');
				
				foreach ($post_array as $post_imgsrc) {
					array_unshift($post_array_final, $post_imgsrc);
					array_pop($post_array_final);
				}
				
				foreach ($post_array_final as $post_final) {
					if ($count == 1) {
						?>
						<div class="board-main-photo-wrapper">
							<img src="<?php echo $post_final; ?>" class="board-main-photo" alt="" />
						</div>
						<?php
					} else if ($post_final !=='') {
						?>
						<div class="board-photo-wrapper">
						<img src="<?php echo $post_final; ?>" class="board-photo" alt="" />
						</div>
						<?php
					} else {
						?>
						<div class="board-photo-wrapper">
						</div>
						<?php
					}
					$count++;
				}
				?>
				</a>
				
				<?php global $user_ID; if ($post->post_author != $user_ID) { ?>
					<button class="btn follow ipin-follow<?php if ($followed = ipin_followed($board_id)) { echo ' disabled'; } ?>" data-author_id="<?php echo $post->post_author; ?>" data-board_id="<?php echo $board_id;  ?>" data-board_parent_id="<?php echo $board_parent_id; ?>" type="button"><?php if (!$followed) { _e('Follow', 'ipin'); } else { _e('Unfollow', 'ipin'); } ?></button>
				<?php } else { ?>
					<a class="btn follow" href="<?php echo home_url('/boards-settings/?i=') . $board_id; ?>"><?php _e('Edit Board', 'ipin'); ?></a>
				<?php } ?>
			</div>
		</div>
		
		<?php
		$photo_source_domain = get_post_meta($post->ID, '_Photo Source Domain', true);
		if ($photo_source_domain != '' ) {
			$loop_domain_args = array(
				'posts_per_page' => 4,
				'meta_key' => '_Photo Source Domain',
				'meta_value' => $photo_source_domain
			);
			
			$loop_domain = new WP_Query($loop_domain_args);
			?>
			<div class="board-domain">
				<p><?php _e('Also from', 'ipin'); ?> <a href="<?php echo home_url('/source/') . $photo_source_domain; ?>/"><?php echo $photo_source_domain; ?></a></p>
				<a href="<?php echo home_url('/source/') . $photo_source_domain; ?>">
				<?php
				$post_domain_array = array();
				while ($loop_domain->have_posts()) : $loop_domain->the_post();
					$imgsrc = wp_get_attachment_image_src(get_post_thumbnail_id($loop_board->ID),'thumbnail');
					$imgsrc = $imgsrc[0];
					array_unshift($post_domain_array, $imgsrc);
				endwhile;
				wp_reset_query();
		
				$post_domain_array_final = array_fill(0, 4, '');
				
				foreach ($post_domain_array as $post_imgsrc) {
					array_unshift($post_domain_array_final, $post_imgsrc);
					array_pop($post_domain_array_final);
				}
				
				foreach ($post_domain_array_final as $post_final) {
					if ($post_final !=='') {
					?>
						<div class="board-domain-wrapper">
						<img src="<?php echo $post_final; ?>" alt="" />
						</div>
					<?php
					} else {
						?>
						<div class="board-domain-wrapper">
						</div>
						<?php
					}
				}
				?>
					<div class="clearfix"></div>
				</a>
			</div>
		<?php }	?>
	</div>
	
	<div class="clearfix"></div>
<?php 
}

if (!dynamic_sidebar('sidebar-left')) :
endif ?>
</div>