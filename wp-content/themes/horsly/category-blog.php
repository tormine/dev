<?php get_header(); ?>

<div class="container">
	<div class="row">
		<div class="span9">
			<div class="row">
				<div id="double-left-column" class="span6 pull-right">
					<?php while (have_posts()) : the_post(); ?>
					<div id="post-<?php the_ID(); ?>" <?php post_class('post-wrapper'); ?>>
						<div class="h1-wrapper">
							<a href="<?php the_permalink(); ?>"><h1><?php the_title(); ?></h1></a>
						</div>

						<div class="post-meta-top">
							<div class="pull-right"><a href="<?php the_permalink(); ?>#comments"><?php comments_number(__('0 Comments', 'ipin'), __('1 Comment', 'ipin'), __('% Comments', 'ipin'));?></a><?php edit_post_link(__('Edit', 'ipin'), ' | '); ?></div>
							<div class="pull-left"><?php echo ipin_human_time_diff(get_post_time('U', true)) . ' / ';the_author(); ?></div>
						</div>

						<?php
						$imgsrc = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'large');
						if ($imgsrc[0] != '') {
						?>
						<div class="post-featured-photo">
							<a href="<?php the_permalink(); ?>" target="_blank"><img class="featured-thumb" src="<?php echo $imgsrc[0]; ?>" alt="<?php the_title_attribute(); ?>" /></a>
						</div>
						<?php } ?>

						<div class="post-content">
							<?php
							the_content();
							wp_link_pages( array( 'before' => '<p><strong>' . __('Pages:', 'ipin') . '</strong>', 'after' => '</p>' ) );
							?>
							
							<div class="clearfix"></div>
							
							<div class="post-meta-category-tag">
								<?php _e('Categories', 'ipin'); ?>: <?php the_category(', '); ?> 
								<?php the_tags(__('Tags', 'ipin') . ': ', ', '); ?>
							</div>
						</div>
						
					</div>
					<?php endwhile; ?>


					<?php if(function_exists('wp_pagenavi')) { ?>
						<div id="navigation" class="pagination pagination-centered">
							<?php wp_pagenavi(); ?>
						</div>
					<?php } else { ?>
						<div id="navigation-blog">
							<ul class="pager">
								<li class="previous"><?php next_posts_link(__('&laquo; Previous', 'ipin')) ?></li>
								<li class="next"><?php previous_posts_link(__('Next &raquo;', 'ipin')) ?></li>
							</ul>
						</div>
					<?php } ?>
				</div>
				
				<div id="single-right-column" class="span3">
					<?php get_sidebar('left'); ?>
				</div>
			</div>
		</div>
		
		<div class="span3">
			<?php get_sidebar('right'); ?>
		</div>
	</div>

	<div id="scrolltotop"><a href="#"><i class="icon-chevron-up"></i><br /><?php _e('Top', 'ipin'); ?></a></div>
</div>

<?php get_footer(); ?>
