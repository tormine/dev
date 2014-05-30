<?php
/*
Template Name: _source
*/
?>

<?php get_header(); global $user_ID; ?>
<div class="container-fluid">
	<?php
	if(isset($wp_query->query_vars['domain'])) {
		$source = urlencode($wp_query->query_vars['domain']);
		
									
									
									$photo_source_domain_parts = explode('.',$source);
									 
									$photo_source_domain_without_subdomain = $photo_source_domain_parts[count($photo_source_domain_parts)-2];
	} else {
		$source = '...';
	}
	?>
	
	<div class="row-fluid">
		<div class="span4"></div>

		<div class="span4 grand-title-wrapper">
			<h1><?php _e('Stories from', 'ipin') ?> <a style="text-transform:capitalize;" href="http://<?php echo $source; ?>" target="_blank"><?php echo $photo_source_domain_without_subdomain; ?></a></h1>
		</div>

		<div class="span4"></div>
	</div>
	
	<?php
	$pnum = $_GET['pnum'] ? $_GET['pnum'] : 1;
	$args = array(
		'meta_key' => '_Photo Source Domain',
		'meta_value' => $source,
		'paged' => $pnum
	);
	
	query_posts($args);
	$maxpage = $wp_query->max_num_pages;
	?>

	<div id="masonry">
		<?php $count_ad = 1; if (have_posts()) : while (have_posts()) : the_post(); ?>
		
		<?php if (of_get_option('frontpage1_ad') == $count_ad && of_get_option('frontpage1_ad_code') != '' && ($pnum == 1  || of_get_option('infinitescroll') == 'disable')) { ?>
		<div class="thumb">
			<div class="thumb-ad">				
				<?php eval('?>' . of_get_option('frontpage1_ad_code')); ?>
			</div>	 
		</div>
		<?php } ?>
		
		<?php if (of_get_option('frontpage2_ad') == $count_ad && of_get_option('frontpage2_ad_code') != '' && ($pnum == 1  || of_get_option('infinitescroll') == 'disable')) { ?>
		<div class="thumb">
			<div class="thumb-ad">				
				<?php eval('?>' . of_get_option('frontpage2_ad_code')); ?>
			</div>	 
		</div>
		<?php } ?>
		
		<?php if (of_get_option('frontpage3_ad') == $count_ad && of_get_option('frontpage3_ad_code') != '' && ($pnum == 1  || of_get_option('infinitescroll') == 'disable')) { ?>
		<div class="thumb">
			<div class="thumb-ad">				
				<?php eval('?>' . of_get_option('frontpage3_ad_code')); ?>
			</div>	 
		</div>
		<?php } ?>
		
		<?php if (of_get_option('frontpage4_ad') == $count_ad && of_get_option('frontpage4_ad_code') != '' && ($paged == 0 || $paged == 1 || of_get_option('infinitescroll') == 'disable')) { ?>
		<div class="thumb">
			<div class="thumb-ad">				
				<?php eval('?>' . of_get_option('frontpage4_ad_code')); ?>
			</div>	 
		</div>
		<?php } ?>
		
		<?php if (of_get_option('frontpage5_ad') == $count_ad && of_get_option('frontpage5_ad_code') != '' && ($paged == 0 || $paged == 1 || of_get_option('infinitescroll') == 'disable')) { ?>
		<div class="thumb">
			<div class="thumb-ad">				
				<?php eval('?>' . of_get_option('frontpage5_ad_code')); ?>
			</div>	 
		</div>
		<?php } ?>
		
<div id="post-<?php the_ID(); ?>" <?php post_class('thumb'); ?>>
    <?php if ( has_action('es_theme_thumbnail_top') ) { ?>
        <div class="masonry-pin-top">
            <?php do_action('es_theme_thumbnail_top', $post->ID); ?>
        </div>
    <?php } ?>
    <div class="brick-header">
        <div class="thumb-holder">
            <div class="masonry-actionbar">
                <?php if (current_user_can('administrator') || current_user_can('editor') || current_user_can('author') || !is_user_logged_in()) { ?>
                <a class="ipin-repin btn btn-mini" data-post_id="<?php echo $post->ID ?>" href="#"><i class="fa fa-share"></i> <?php _e('', 'ipin'); ?></a>
                <?php } ?>
                <?php if ($post->post_author != $user_ID) { ?>
                <button class="ipin-like btn btn-mini<?php if(ipin_liked($post->ID)) { echo ' disabled'; } ?>" data-post_id="<?php echo $post->ID ?>" data-post_author="<?php echo $post->post_author; ?>" type="button"><i class="fa fa-heart"></i> <?php _e('', 'ipin'); ?></button>
                <?php } else { ?>
                <a class="btn btn-mini" href="<?php echo home_url('/pins-settings/'); ?>?i=<?php the_ID(); ?>"><i class="fa fa-edit"> </i><?php _e('', 'ipin'); ?></a>
                <?php } ?>
                <a class="ipin-comment btn btn-mini" href="<?php the_permalink(); ?>/#respond" data-post_id="<?php echo $post->ID ?>"><i class="fa fa-comment-o"></i> <?php _e('', 'ipin'); ?></a>
            </div>

            <a class="featured-thumb-link" href="<?php the_permalink(); ?>">
                <?php
                //if is youtube or vimeo video
                $photo_source = get_post_meta($post->ID, "_Photo Source", true);
                if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $photo_source, $match) || (strpos(parse_url($photo_source, PHP_URL_HOST), 'vimeo.com') !== FALSE && sscanf(parse_url($photo_source, PHP_URL_PATH), '/%d', $video_id))) {
                    ?>
                    <div class="featured-thumb-video"></div>
                    <?php } ?>

                <?php
                $imgsrc = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID),'medium');
                if ($imgsrc[0] == '') {
                    $imgsrc[0] = get_template_directory_uri() . '/img/blank.gif';
                    $imgsrc[1] = 200;
                    $imgsrc[2] = 200;
                }

                //if is animated gif (may be server intensive)
                /*if (substr($imgsrc[0], -4) == '.gif') {
                    $imgsrc_full = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID),'full');

                    if(($fh = @fopen($imgsrc_full[0], 'rb'))) {
                        $frames = 0;
                        while(!feof($fh) && $frames < 2) {
                            $chunk = fread($fh, 1024 * 100); //read 100kb at a time
                            $frames += preg_match_all('#\x00\x21\xF9\x04.{4}\x00(\x2C|\x21)#s', $chunk, $matches);
                       }
                    }
                    fclose($fh);

                    if ($frames == '2') {
                        echo '<div class="featured-thumb-gif"></div>';
                    }

                } */
                ?>
                <img class="featured-thumb" src="<?php echo $imgsrc[0]; ?>" alt="<?php the_title_attribute(); ?>" style="width:<?php echo $imgsrc[1] ?>px;height:<?php echo round( (200 * $imgsrc[2]) / $imgsrc[1] ) /* edited by Marveller: $imgsrc[2] */  ?>px" />
            </a>

            <div class="post-title"><?php the_title(); ?></div>
        </div>

        <?php
        $likes_number = get_post_meta($post->ID, '_Likes Count', true);
        $repins_number = get_post_meta($post->ID, '_Repin Count', true);
        $comments_number = get_comments_number();
        ?>
        <div class="masonry-meta masonry-meta-comment-likes text-align-center">
            <?php
            if ($likes_number == '' || $likes_number == '0') {
                echo '<span id="likes-count-' . $post->ID . '" class="likes-count hide"></span>';
            } elseif ($likes_number == '1') {
                echo '<span id="likes-count-' . $post->ID . '" class="likes-count">' . __('<i class="fa fa-heart"></i>', 'ipin') . '</span>' ;
            } else {
                echo '<span id="likes-count-' . $post->ID . '" class="likes-count">' . $likes_number . ' ' . __('<i class="fa fa-heart"></i>', 'ipin') . '</span>';
            }

            if ($comments_number == '0') {
                echo '<span id="comments-count-' . $post->ID . '" class="comments-count hide"></span>';
            } elseif ($comments_number == '1') {
                echo '<span id="comments-count-' . $post->ID . '" class="comments-count"> 1 ' . __('comment', 'ipin') . '</span>';
            } else {
                echo '<span id="comments-count-' . $post->ID . '" class="comments-count">' . $comments_number . ' ' . __('comments', 'ipin') . '</span>';
            }

            if ($repins_number == '' || $repins_number == '0') {
                echo '<span id="repins-count-' . $post->ID . '" class="repins-count hide"></span>';
            } elseif ($repins_number == '1') {
                echo '<span id="repins-count-' . $post->ID . '" class="repins-count">1 ' . __('share', 'ipin') . '</span>';
            } else {
                echo '<span id="repins-count-' . $post->ID . '" class="repins-count">' . $repins_number . ' ' . __('shares', 'ipin') . '</span>';
            }
            ?>
        </div>
        
        
     

       
      

        <div class="masonry-actionbar-mobile">
            <?php if (current_user_can('administrator') || current_user_can('editor') || current_user_can('author') || !is_user_logged_in()) { ?>
            <a class="ipin-repin btn btn-small" data-post_id="<?php echo $post->ID ?>" href="#"><i class="fa fa-share"></i><?php _e('', 'ipin'); ?></a>
            <?php } ?>
            <?php if ($post->post_author != $user_ID) { ?>
            <button class="ipin-like btn btn-small<?php if(ipin_liked($post->ID)) { echo ' disabled'; } ?>" data-post_id="<?php echo $post->ID ?>" data-post_author="<?php echo $post->post_author; ?>" type="button"><i class="fa fa-heart"></i><?php _e('', 'ipin'); ?></button>
            <?php } else { ?>
            <a class="btn btn-small" href="<?php echo home_url('/pins-settings/'); ?>?i=<?php the_ID(); ?>"><i class="fa fa-edit"></i><?php _e('', 'ipin'); ?></a>
            <?php } ?>
            <a class="ipin-comment btn btn-small" href="<?php the_permalink(); ?>/#respond" data-post_id="<?php echo $post->ID ?>"><i class="fa fa-comment"></i><?php _e('', 'ipin'); ?></a>
        </div>
    </div><!-- / brick-header -->

    <?php if ( has_action('es_theme_thumbnail_middle') ) { ?>
        <div class="masonry-pin-middle">
            <?php do_action('es_theme_thumbnail_middle', $post->ID); ?>
        </div>
    <?php } ?>

    <div class="brick-footer">
        <div class="masonry-meta">
            <div class="masonry-meta-avatar"><a href="<?php echo home_url('/user/') . get_the_author_meta('user_login'); ?>/"><?php echo get_avatar(get_the_author_meta('ID') , '30'); ?></a></div>
            <div class="masonry-meta-comment">
                <span class="masonry-meta-author"><?php the_author_posts_link(); ?></span>
                <?php
                $original_post_id = get_post_meta($post->ID, "_Original Post ID", true);
                if ($original_post_id != '' && $original_post_id != 'deleted') {
                    $original_postdata = get_post($original_post_id, 'ARRAY_A');
                    $original_author = get_user_by('id', $original_postdata['post_author']);
                    ?>
                    <?php _e('via', 'ipin'); ?>
                    <a href="<?php echo home_url('/user/') . $original_author->user_login; ?>/"><strong><?php echo $original_author->display_name; ?></strong></a>
                    <?php } ?><br>
                <?php _e('shared into', 'ipin'); ?>
                <span class="masonry-meta-content"><strong><?php the_terms($post->ID, 'board'); ?></strong></span>
            </div>
        </div>

        <?php
        if ('0' != $frontpage_comments_number = of_get_option('frontpage_comments_number')) {
            ?>
            <div id="masonry-meta-comment-wrapper-<?php echo $post->ID; ?>">
                <?php
                if ($comments_number >  $frontpage_comments_number) {
                    $offset = $comments_number - $frontpage_comments_number;
                } else {
                    $offset = 0;
                }

                $args = array(
                    'number' => $frontpage_comments_number,
                    'post_id' => $post->ID,
                    'order' => 'asc',
                    'offset' => $offset,
                    'status' => 'approve'
                );
                $comments = get_comments($args);
                foreach($comments as $comment) {
                    ?>
                    <div class="masonry-meta">
                        <?php $comment_author = get_user_by('id', $comment->user_id); ?>
                        <div class="masonry-meta-avatar">
                            <?php if ($comment_author) { ?>
                            <a href="<?php echo home_url('/user/') . $comment_author->user_login; ?>/">
                            <?php } ?>
                            <?php echo get_avatar($comment->user_id, '30'); ?>
                            <?php if ($comment_author) { ?>
                            </a>
                            <?php } ?>
                        </div>
                        <div class="masonry-meta-comment">
                            <span class="masonry-meta-author">
                                <?php if ($comment_author) { ?><a href="<?php echo home_url('/user/') . $comment_author->user_login; ?>/"><?php } ?><?php echo $comment->comment_author; ?><?php if ($comment_author) { ?></a><?php } ?>
                            </span><br>
                            <span class="masonry-meta-comment-content"><?php echo $comment->comment_content; ?></span>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
            <?php
        }

        if (is_user_logged_in()) {
            ?>
            <div id="masonry-meta-commentform-<?php echo $post->ID ?>" class="masonry-meta hide">
                <div class="masonry-meta-avatar"><?php echo get_avatar($user_ID, '30'); ?></div>
                <div class="masonry-meta-comment">
                    <?php
                    $id_form = 'commentform-' . $post->ID;
                    $id_submit = 'submit-' . $post->ID;

                    comment_form(array(
                        'id_form' => $id_form,
                        'id_submit' => $id_submit,
                        'title_reply' => '',
                        'cancel_reply_link' => __('X Cancel reply', 'ipin'),
                        'comment_notes_before' => '',
                        'comment_notes_after' => '',
                        'logged_in_as' => '',
                        'label_submit' => __('Comment..', 'ipin'),
                        'comment_field' => '<textarea placeholder="' . __('Add a comment...', 'ipin') . '" id="comment" name="comment" aria-required="true"></textarea>'
                    ));
                    ?>
                </div>
            </div>
        <?php } ?>

        <?php if ( has_action('es_theme_thumbnail_bottom') ) { ?>
            <div class="masonry-pin-bottom">
                <?php do_action('es_theme_thumbnail_bottom', $post->ID); ?>
            </div>
        <?php } ?>
            
    </div><!-- / brick-footer -->
</div>
    <?php
    $count_ad++;
endwhile;
else :
    ?>
<div class="row-fluid">
    <div class="span12">
        <div class="bigmsg">
            <h2><?php _e('No stories were found :( . . .', 'ipin'); ?></h2>
        </div>
    </div>
</div>
    <?php endif; ?>
</div>

<?php if(function_exists('wp_pagenavi')) { ?>
<div id="navigation" class="pagination pagination-centered">
    <?php wp_pagenavi(); ?>
</div>
<?php } else { ?>
<div id="navigation">
    <ul class="pager">
        <li id="navigation-next"><?php next_posts_link(__('&laquo; Previous', 'ipin')) ?></li>
        <li id="navigation-previous"><?php previous_posts_link(__('Next &raquo;', 'ipin')) ?></li>
    </ul>
</div>
<?php } ?>

	<div id="scrolltotop"><a href="#"><i class="fa fa-chevron-up"></i><br /><?php _e('Top', 'ipin'); ?></a></div>
</div>

<div class="modal hide" id="post-lightbox" tabindex="-1" role="dialog" aria-hidden="true"></div>	

<?php get_footer(); ?>