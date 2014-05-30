<?php get_header(); global $user_ID; ?>

<div class="container">
	<div class="row">
		<div class="span9">
			<div class="row">
				<div id="double-left-column" class="span6 pull-right">
					<?php while (have_posts()) : the_post(); ?>
					<div id="post-<?php the_ID(); ?>" <?php post_class('post-wrapper'); ?>>
						<div class="post-top-wrapper">
						
						
							<div class="pull-left">
								<a href="<?php echo home_url('/user/') . get_the_author_meta('user_login'); ?>/">
								<?php echo get_avatar(get_the_author_meta('ID'), '48'); ?>
								</a>
							</div>
							<div class="post-top-wrapper-header">
							<div class="pull-right">
								<div class="post-actionbar">
								<?php if ($post->post_author == $user_ID || current_user_can('edit_others_posts')) { ?>
										<a class="btn" href="<?php echo home_url('/story-settings/'); ?>?i=<?php the_ID(); ?>"><i class="fa fa-edit"></i><?php _e(' Edit', 'ipin'); ?></a>
									<?php if (current_user_can('administrator') || current_user_can('editor') || current_user_can('author') || !is_user_logged_in()) { ?>
										<a class="ipin-repin btn" data-post_id="<?php echo $post->ID ?>" href="#"><i class="fa fa-share"></i> <?php _e(' Share', 'ipin'); ?></a>
									<?php } ?>
									<?php if ($post->post_author != $user_ID) { ?> 
										<button class="ipin-like btn <?php if (ipin_liked($post->ID)) { echo ' disabled'; } ?>" data-post_id="<?php echo $post->ID ?>" data-post_author="<?php echo $post->post_author; ?>" type="button"><i class="fa fa-heart"></i> <?php _e('', 'ipin'); ?></button>
										
									
									<?php } ?>
									
										
										<?php if ($post->post_author != $user_ID) { ?> 
											<button class="btn follow ipin-follow<?php $data_boards = get_the_terms($post->ID, 'board'); foreach ($data_boards as $data_board) { $board_parent_id = $data_board->parent; $board_id = $data_board->term_id; } if ($followed = ipin_followed($board_parent_id)) { echo ' disabled'; } ?>" data-board_parent_id="0" data-author_id="<?php echo $post->post_author; ?>" data-board_id="<?php echo $board_parent_id; ?>" type="button"><?php if (!$followed) { _e('Follow', 'ipin'); } else { _e('Unfollow', 'ipin'); } ?></button>
										<?php } ?>
									<?php } ?>
								</div>
								
								</div>
								<a href="<?php echo home_url('/user/') . get_the_author_meta('user_login'); ?>/">
									<div class="post-top-wrapper-author"><?php echo get_the_author_meta('display_name'); ?></div>
								</a>
								<?php 
								$original_post_id = get_post_meta($post->ID, "_Original Post ID", true);
								if ($original_post_id != '' && $original_post_id != 'deleted') {
									_e('Reshared', 'ipin');

								} else {
									_e('Shared', 'ipin');
								}
								echo ' ' . ipin_human_time_diff(get_post_time('U', true));
								?>
							</div>
								

						</div>

                                                <?php if ( has_action('es_theme_single_top') ) { ?>
                                                    <div class="single-pin-top">
                                                        <?php do_action('es_theme_single_top', $post->ID); ?>
                                                    </div>
                                                <?php } ?>
						
						<?php
						$imgsrc = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');

						//for animated gif
						if (substr($imgsrc[0], -3) != 'gif' && intval($imgsrc[1]) > 520) {
							$imgsrc = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'large');
						}
						
						if ($imgsrc[0] == '') {
							$imgsrc[0] = get_template_directory_uri() . '/img/blank.gif';
						}
						?>

						<div class="post-share<?php if (!is_active_sidebar('sidebar-r')) { echo ' position-fixed'; } ?>">
							<p><iframe src="//www.facebook.com/plugins/like.php?href=<?php echo urlencode(get_permalink()); ?>&amp;send=false&amp;layout=button_count&amp;width=75&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:78px; height:21px;" allowTransparency="true"></iframe></p>

							
							<p><a id="post-embed" class="btn btn-mini"><strong>&lt;&gt; <?php _e('Embed', 'ipin'); ?></strong></a></p>
						</div>
						
						<div class="post-top-meta" >
							<div class="pull-right">
								<?php if ('' == $photo_source = get_post_meta($post->ID, "_Photo Source", true)) { ?>
								<strong><?php  _e('uploaded by <i class="fa fa-user"></i> ', 'ipin');   echo get_the_author_meta('display_name');   ?></strong>
								<?php 
								} else { 
									$photo_source_domain = parse_url($photo_source, PHP_URL_HOST);
									
									
									$photo_source_domain_parts = explode('.',$photo_source_domain);
									 
									$photo_source_domain_without_subdomain = $photo_source_domain_parts[count($photo_source_domain_parts)-2];
									
									
									_e('From:', 'ipin'); ?> 
									<?php
                                                                            if ( has_filter('es_theme_source_url') ) {
                                                                                    $photo_source = apply_filters('es_theme_source_url', $post->ID, $photo_source);
                                                                            }
									?>
									<a style="text-transform:capitalize;" href="<?php echo $photo_source; ?>" target="_blank"><?php echo $photo_source_domain_without_subdomain; ?></a>
								<?php } ?>
							</div>
							
							
							<div class="pull-left">
							
							</div>
						
						</div>
						
						
						<div class="clearfix"></div>
						
						<?php if (of_get_option('single_pin_above_ad') != '') { ?>
						<div id="single-pin-above-ad">
							<?php eval('?>' . of_get_option('single_pin_above_ad')); ?>
						</div>
						<?php } ?>
						
						<div class="post-featured-photo">
						<?php
						//if is youtube video
						if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $photo_source, $match)) {
						?>
							<embed id="video-embed" src="http://www.youtube.com/v/<?php echo $match[1]; ?>?rel=0&autoplay=1" type="application/x-shockwave-flash" width="640" height="347" allowscriptaccess="always" allowfullscreen="true"></embed>
						<?php
						//if is vimeo video
						} else if (strpos(parse_url($photo_source, PHP_URL_HOST), 'vimeo.com') !== FALSE && sscanf(parse_url($photo_source, PHP_URL_PATH), '/%d', $video_id)){
						?>
							<iframe id="video-embed" src="http://player.vimeo.com/video/<?php echo $video_id; ?>?title=0&amp;byline=0&amp;portrait=0&amp;badge=0&amp;color=ffffff&amp;autoplay=1" width="640" height="347" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
						<?php
						} else {
						?>
							<?php if ($photo_source != '') { ?>
								<a href="<?php echo $photo_source; ?>" target="_blank">
							<?php } ?>
								<img class="featured-thumb" src="<?php echo $imgsrc[0]; ?>" alt="<?php the_title_attribute(); ?>" />
							<?php if ($photo_source != '') { ?>
								</a>
							<?php } ?>
						<?php } ?>
						</div>
						
						<?php if (of_get_option('single_pin_below_ad') != '') { ?>
						<div id="single-pin-below-ad">
							<?php eval('?>' . of_get_option('single_pin_below_ad')); ?>
						</div>
						<?php } ?>

						<div class="post-content">
							<?php if (mb_strlen(get_the_title()) < 120) { ?>
								<h1 class="post-title"><?php the_title(); ?></h1>
							<?php } else { ?>
								<p class="post-title"><?php the_title(); ?></p>
							<?php } ?>
							
							
							
							
							<?php
							the_content();
							wp_link_pages( array( 'before' => '<p><strong>' . __('Pages:', 'ipin') . '</strong>', 'after' => '</p>' ) );
							?>
							
							<?php if ($original_post_id != '' && $original_post_id != 'deleted') { ?>
								<p class="post-original-author">
								<?php 
								$original_postdata = get_post($original_post_id, 'ARRAY_A');
								$original_author = get_user_by('id', $original_postdata['post_author']);
								$board = wp_get_post_terms($original_post_id, 'board', array("fields" => "all")); 
								?>
								<?php _e('Reshared from', 'ipin'); ?> 
								<a href="<?php echo get_term_link($board[0]->slug, 'board'); ?>"><?php echo $board[0]->name; ?></a> 
								<?php _e('by', 'ipin'); ?> <a href="<?php echo home_url('/user/') . $original_author->user_login; ?>/"><?php echo $original_author->display_name; ?></a> 
								</p>
							<?php }	?>
							
							<?php 
								$earliest_post_id = get_post_meta($post->ID, "_Earliest Post ID", true);
								if ($earliest_post_id != '' && $earliest_post_id != 'deleted') { ?>
								<p class="post-original-author">
								<?php 
								$earliest_postdata = get_post($earliest_post_id, 'ARRAY_A');
								$earliest_author = get_user_by('id', $earliest_postdata['post_author']);
								$earliest_board = wp_get_post_terms($earliest_post_id, 'board', array("fields" => "all")); 
								?>
								<?php _e('Originally shared into', 'ipin'); ?> 
								<a href="<?php echo get_term_link($earliest_board[0]->slug, 'board'); ?>"><?php echo $earliest_board[0]->name; ?></a> 
								<?php _e('by', 'ipin'); ?> <a href="<?php echo home_url('/user/') . $earliest_author->user_login; ?>/"><?php echo $earliest_author->display_name; ?></a>
								
								</p>
							<?php }	?>
							
							
						</div>
						
                                                <?php if ( has_action('es_theme_single_middle') ) { ?>
                                                    <div class="single-pin-middle">
                                                        <?php do_action('es_theme_single_middle', $post->ID); ?>
                                                    </div>
                                                <?php } ?>

						<div class="post-comments">
							<div class="post-comments-wrapper">
								
								<?php comments_template(); ?>
								
							</div>
						
						
							
						</div>
						
						
						
						<div class="post-board hide">
							<div class="post-board-wrapper">
								<?php if ($post->post_author != $user_ID) { ?>
								<button class="btn btn-mini pull-right follow ipin-follow<?php if ($followed = ipin_followed($board_id)) { echo ' disabled'; } ?>" data-author_id="<?php echo $post->post_author; ?>" data-board_id="<?php echo $board_id;  ?>" data-board_parent_id="<?php echo $board_parent_id; ?>" type="button"><?php if (!$followed) { _e('Follow', 'ipin'); } else { _e('Unfollow', 'ipin'); } ?></button>
								<?php } ?>
								
								<h4>
							
								<?php _e('Shared into', 'ipin') ?> <?php the_terms($post->ID, 'board', '<span>', ', ', '</span>'); ?></h4>
								<?php
								$boards = get_the_terms($post->ID, 'board');
							
								foreach ($boards as $board) {
									$board_id = $board->term_id;
									$board_name = $board->name;
									$board_count = $board->count;
									$board_slug = $board->slug;
								}
							
								$loop_board_args = array(
									'posts_per_page' => 4,
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
									<a href="<?php echo $board_link; ?>">
									<?php
									$post_array = array();
									while ($loop_board->have_posts()) : $loop_board->the_post();
										$board_imgsrc = wp_get_attachment_image_src(get_post_thumbnail_id($loop_board->ID),'medium-thumb');
										$board_imgsrc = $board_imgsrc[0];
										array_unshift($post_array, $board_imgsrc);
									endwhile;

									wp_reset_query();

									$post_array_final = array_fill(0, 4, '');
									
									foreach ($post_array as $post_imgsrc) {
										array_unshift($post_array_final, $post_imgsrc);
										array_pop($post_array_final);
									}
									
									foreach ($post_array_final as $post_final) {
										if ($post_final !=='') {
											?>
											<div class="post-board-photo">
												<img src="<?php echo $post_final; ?>" alt="" />
											</div>
											<?php
										} else {
											?>
											<div class="post-board-photo">
											</div>
											<?php
										}
									}
									?>
									</a>
							</div>
							<div class="clearfix"></div>
						</div>
						
						<?php
						if ($photo_source_domain != '' ) {
							$loop_domain_args = array(
								'posts_per_page' => 4,
								'meta_key' => '_Photo Source Domain',
								'meta_value' => $photo_source_domain
							);
							
							$loop_domain = new WP_Query($loop_domain_args);
						?>
						<div id="post-board-source" class="post-board hide" >
							<div class="post-board-wrapper">
								<h4><?php _e('Also from', 'ipin') ?> <a style="text-transform:capitalize;" href="<?php echo home_url('/source/') . $photo_source_domain; ?>/"><?php echo $photo_source_domain_without_subdomain; ?></a></h4>
									<a href="<?php echo home_url('/source/') . $photo_source_domain; ?>">
									<?php
									$post_domain_array = array();
									while ($loop_domain->have_posts()) : $loop_domain->the_post();
										$domain_imgsrc = wp_get_attachment_image_src(get_post_thumbnail_id($loop_board->ID),'medium-thumb');
										$domain_imgsrc = $domain_imgsrc[0];
										array_unshift($post_domain_array, $domain_imgsrc);
									endwhile;
									wp_reset_query();
									
									$post_domain_array_final = array_fill(0, 4, '');
									
									foreach ($post_domain_array as $post_imgsrc) {
										array_unshift($post_domain_array_final, $post_imgsrc);
										array_pop($post_domain_array_final);
									}
									
									foreach ($post_domain_array_final as $post_final) {
										if ($post_final != '') {
											?>
											<div class="post-board-photo">
												<img src="<?php echo $post_final; ?>" alt="" />
											</div>
											<?php
										} /*else {
											?>
											<div class="post-board-photo">
											</div>
											<?php
										}*/
									}
									?>
									</a>
							</div>
							<div class="clearfix"></div>
						</div>
						<?php
						}
						
						$post_likes = get_post_meta($post->ID, "_Likes User ID");
						$post_likes_count = count($post_likes[0]);
						if (!empty($post_likes[0])) {
						$post_likes[0] = array_slice($post_likes[0], -16);
						?>
						<div class="post-likes">
							<div class="post-likes-wrapper">
								<h4><?php _e('<i class="fa fa-heart fa-2x"></i>', 'ipin'); ?></h4>
								<div class="post-likes-avatar">
								<?php
								foreach ($post_likes[0] as $post_like) {
									$like_author = get_user_by('id', $post_like);
									?>
									<a id="likes-<?php echo $post_like; ?>" href="<?php echo home_url('/user/') . $like_author->user_login; ?>/" rel="tooltip" title="<?php echo $like_author->display_name; ?>">
									<?php echo get_avatar($like_author->ID, '48'); ?>
									</a>
								<?php 
								}
								if ($post_likes_count > 16) {
								?>
									<p class="more-likes"><strong>+<?php echo $post_likes_count - 16 ?></strong> <?php _e('more likes', 'ipin'); ?></p>
								<?php } ?>
								</div>
							</div>
						</div>
						<?php } ?>
						
						<?php
						$post_repins = get_post_meta($post->ID, "_Repin Post ID");
						$post_repins_count = count($post_repins[0]);
						if (!empty($post_repins[0])) {
						$post_repins[0] = array_slice($post_repins[0], -10);
						?>
						<div id="post-repins">
							<div class="post-repins-wrapper">
								<h4><?php _e('Reshares  ', 'ipin'); ?></h4><br>								<ul>
								<?php
								foreach ($post_repins[0] as $post_repin) {
									$repin_postdata = get_post($post_repin, 'ARRAY_A');
									$repin_author = get_user_by('id', $repin_postdata['post_author']);
									?>
									<li id="repins-<?php echo $post_repin; ?>">
									<a style="float:left;" class="post-repins-avatar" href="<?php echo home_url('/user/') . $repin_author->user_login; ?>/">
									
									<?php echo get_avatar($repin_author->ID, '48'); ?>
									</a> 
									<div class="post-repins-content">
									<a href="<?php echo home_url('/user/') . $repin_author->user_login; ?>/">
									<?php echo $repin_author->display_name; ?>
									</a> 
									into
									<?php 
									$board = wp_get_post_terms($post_repin, 'board', array("fields" => "all"));
									echo '<a href="' . get_term_link($board[0]->slug, 'board') . '">' . $board[0]->name . '</a></div>';
									?>
									</li>
								<?php 
								}	
								if ($post_repins_count > 10) {
								?>
									<li class="more-repins"><strong>+<?php echo $post_repins_count - 10; ?></strong> <?php _e('more reshares', 'ipin'); ?></li>
								<?php } ?>
								</ul>
							</div>
						</div>
						<?php } ?>
						
						<div id="post-embed-overlay"></div>
						
						<div class="modal hide" id="post-embed-box" tabindex="-1" role="dialog" aria-hidden="true">
							<div class="modal-header">
								<button id="post-embed-close" type="button" class="close" aria-hidden="true">x</button>
								<h3><?php _e('Embed this on your site', 'ipin'); ?></h3>
							</div>
							
							<div class="modal-footer">
								<?php $size = getimagesize($imgsrc[0]); ?>
								<input type="text" id="embed-width" value="<?php echo $size[0]; ?>" /><span class="help-inline"> <?php _e('px -Image Width', 'ipin'); ?></span>
								<input type="text" id="embed-height" value="<?php echo $size[1]; ?>" /><span class="help-inline"> <?php _e('px -Image Height', 'ipin'); ?></span>
								<textarea><div style='padding-bottom: 2px;line-height:0px;'><a href='<?php the_permalink(); ?>' target='_blank'><img src='<?php echo $imgsrc[0]; ?>' border='0' width='<?php echo $size[0]; ?>' height='<?php echo $size[1]; ?>' /></a></div><div style='float:left;padding-top:0px;padding-bottom:0px;'><p style='font-size:10px;color:#76838b;'><?php _e('Source', 'ipin'); ?>: <a style='text-decoration:underline;font-size:10px;color:#76838b;' href='<?php echo $photo_source;  ?>'><?php echo $photo_source_domain; ?></a> <?php _e('via', 'ipin'); ?> <a style='text-decoration:underline;font-size:10px;color:#76838b;' href='<?php echo home_url('/user/') . get_the_author_meta('user_login'); ?>' target='_blank'><?php echo get_the_author_meta('display_name'); ?></a> <?php _e('on', 'ipin'); ?> <a style='text-decoration:underline;color:#76838b;' href='<?php echo home_url('/'); ?>' target='_blank'><?php bloginfo('name'); ?></a></p></div></textarea>
							</div>
						</div>

                                                <?php if ( has_action('es_theme_single_bottom') ) { ?>
                                                    <div class="single-pin-bottom">
                                                        <?php do_action('es_theme_single_bottom', $post->ID); ?>
                                                    </div>
                                                <?php } ?>

					</div>
					<?php endwhile; ?>
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

	<div id="scrolltotop"><a href="#"><i class="fa fa-chevron-up"></i><br /><?php _e('Top', 'ipin'); ?></a></div>
</div>

<?php get_footer(); ?>
