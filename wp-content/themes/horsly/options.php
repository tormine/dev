<?php
function optionsframework_option_name() {
	$themename = get_option( 'stylesheet' );
	$themename = preg_replace("/\W/", "_", strtolower($themename) );

	$optionsframework_settings = get_option( 'optionsframework' );
	$optionsframework_settings['id'] = $themename;
	update_option( 'optionsframework', $optionsframework_settings );
}

add_action('admin_init', 'optionscheck_change_santiziation', 100);
  
function optionscheck_change_santiziation() {
    remove_filter('of_sanitize_textarea', 'of_sanitize_textarea');
    add_filter('of_sanitize_textarea', 'custom_sanitize_textarea');
}
  
function custom_sanitize_textarea($input) {
    return $input;
}

function optionsframework_options() {

	// Pull all the parent categories into an array	
	$options_categories = array('');
	$options_categories_obj = get_categories('hide_empty=0&exclude=1');
	foreach ($options_categories_obj as $category) {
		if ($category->category_parent == 0) {
			$options_categories[$category->cat_ID] = $category->cat_name;
		}
	}
	
	$options = array();
	
	$options[] = array(
		'name' => __('General', 'options_framework_theme'),
		'type' => 'heading');
	
	$options[] = array(
		'name' => __('Show Popular Pins Based On', 'options_framework_theme'),
		'id' => 'popularity',
		'std' => 'showall',
		'type' => 'radio',
		'options' => array('likes' => __('Likes', 'options_framework_theme'), 'repins' => __('Repins', 'options_framework_theme'), 'comments' => __('Comments', 'options_framework_theme'), 'showall' => __('Show All', 'options_framework_theme')));

	$options[] = array(
		'desc' => __('When your site is new, select "Show All" so that the homepage & popular page will not be blank. As the pins get more likes, repins or comments, select as appropriate.', 'options_framework_theme'),
		'type' => 'info');

	$options[] = array(
		'name' => __('Show Popular Pins Over Last X Days', 'options_framework_theme'),
		'desc' => __('Days', 'options_framework_theme'),
		'id' => 'popularity_duration',
		'std' => '30',
		'class' => 'mini',
		'type' => 'text');
	
	$options[] = array(
		'name' => __('Social Icon Urls', 'options_framework_theme'),
		'desc' => __('Facebook Url. Leave blank to hide facebook icon in header', 'options_framework_theme'),
		'id' => 'facebook_icon_url',
		'std' => 'http://facebook.com/#',
		'class' => 'mini',
		'type' => 'text');
		
	$options[] = array(
		'desc' => __('Twitter Url. Leave blank to hide twitter icon in header', 'options_framework_theme'),
		'id' => 'twitter_icon_url',
		'std' => 'http://twitter.com/#',
		'class' => 'mini',
		'type' => 'text');
		
	$options[] = array(
		'name' => __('Header Logo', 'options_framework_theme'),
		'desc' => __('Leave blank to use site title text.', 'options_framework_theme'),
		'id' => 'logo',
		'type' => 'upload');
		
	$options[] = array(
		'name' => __('Frontpage Comments Number', 'options_framework_theme'),
		'desc' => __('Enter 0 to hide comments on frontpage', 'options_framework_theme'),
		'id' => 'frontpage_comments_number',
		'std' => '3',
		'class' => 'mini',
		'type' => 'text');
		
	$options[] = array(
		'name' => __('Infinite Scroll', 'options_framework_theme'),
		'desc' => __('If disabled, the normal pagination links are displayed. The theme is compatible with the <a href="http://wordpress.org/extend/plugins/wp-pagenavi/">WP-PageNavi</a> plugin, but must be deactivated if you re-enable infinite scroll.', 'options_framework_theme'),
		'id' => 'infinitescroll',
		'std' => 'enable',
		'type' => 'radio',
		'options' => array('enable' => __('Enable', 'options_framework_theme'), 'disable' => __('Disable', 'options_framework_theme')));

	$options[] = array(
		'name' => __('Outgoing Email Settings', 'options_framework_theme'),
		'desc' => __('Email address', 'options_framework_theme'),
		'id' => 'outgoing_email',
		'std' => 'noreply@' . parse_url(home_url(), PHP_URL_HOST),
		'class' => 'mini',
		'type' => 'text');
		
	$options[] = array(
		'desc' => __('From whom', 'options_framework_theme'),
		'id' => 'outgoing_email_name',
		'std' => get_bloginfo('name'),
		'class' => 'mini',
		'type' => 'text');
		
	$options[] = array(
		'desc' => __('Your email "From" field. For user email notifications for likes, follows, comments etc.', 'options_framework_theme'),
		'type' => 'info');

	$options[] = array(
		'name' => __('Prune Schedule', 'options_framework_theme'),
		'desc' => __('posts every', 'options_framework_theme'),
		'id' => 'prune_postnumber',
		'std' => '5',
		'class' => 'mini',
		'type' => 'text');
		
	$options[] = array(
		'desc' => __('mins', 'options_framework_theme'),
		'id' => 'prune_duration',
		'std' => '5',
		'class' => 'mini',
		'type' => 'text');

	$options[] = array(
		'desc' => __('When a user delete a pin or a board, the posts are marked as prune for deletion later. Depending on your server load, you can adjust how often the system delete these posts.', 'options_framework_theme'),
		'type' => 'info');
		
	$options[] = array(
		'name' => __('Category For Blog', 'options_framework_theme'),
		'desc' => __('Hide blog category from the Add/Edit Board page. Leave blank if you do not need a blog yet.', 'options_framework_theme'),
		'id' => 'blog_cat_id',
		'type' => 'select',
		'options' => $options_categories);
		
	$options[] = array(
		'name' => __('Advertisement', 'options_framework_theme'),
		'type' => 'heading');

	$options[] = array(
		'name' => __('Header Advertisement', 'options_framework_theme'),
		'desc' => __('HTML / PHP / Javascript allowed.', 'options_framework_theme'),
		'id' => 'header_ad',
		'type' => 'textarea');
		
	$options[] = array(
		'name' => __('Single Post - Above Photo', 'options_framework_theme'),
		'desc' => __('Recommended Width: 520px or lower. HTML / PHP / Javascript allowed. Note: Adsense will only display on single post, not in lightbox.', 'options_framework_theme'),
		'id' => 'single_pin_above_ad',
		'type' => 'textarea');
		
	$options[] = array(
		'name' => __('Single Post - Below Photo', 'options_framework_theme'),
		'desc' => __('Recommended Width: 520px or lower. HTML / PHP / Javascript allowed. Note: Adsense will only display on single post, not in lightbox.', 'options_framework_theme'),
		'id' => 'single_pin_below_ad',
		'type' => 'textarea');

	$options[] = array(
		'name' => __('Frontpage Thumbnail Ad #1', 'options_framework_theme'),
		'desc' => __('Display before X(th) thumbnail'),
		'id' => 'frontpage1_ad',
		'std' => '1',
		'class' => 'mini',
		'type' => 'text');
		
	$options[] = array(
		'desc' => __('Recommended Width: 200px or lower. HTML / PHP / Javascript allowed.', 'options_framework_theme'),
		'id' => 'frontpage1_ad_code',
		'type' => 'textarea');
		
	$options[] = array(
		'name' => __('Frontpage Thumbnail Ad #2', 'options_framework_theme'),
		'desc' => __('Display at X(th) position'),
		'id' => 'frontpage2_ad',
		'std' => '2',
		'class' => 'mini',
		'type' => 'text');
		
	$options[] = array(
		'desc' => __('Recommended Width: 200px or lower. HTML / PHP / Javascript allowed.', 'options_framework_theme'),
		'id' => 'frontpage2_ad_code',
		'type' => 'textarea');
		
	$options[] = array(
		'name' => __('Frontpage Thumbnail Ad #3', 'options_framework_theme'),
		'desc' => __('Display at X(th) position'),
		'id' => 'frontpage3_ad',
		'std' => '3',
		'class' => 'mini',
		'type' => 'text');
		
	$options[] = array(
		'desc' => __('Recommended Width: 200px or lower. HTML / PHP / Javascript allowed.', 'options_framework_theme'),
		'id' => 'frontpage3_ad_code',
		'type' => 'textarea');
		
	$options[] = array(
		'name' => __('Frontpage Thumbnail Ad #4', 'options_framework_theme'),
		'desc' => __('Display at X(th) position'),
		'id' => 'frontpage4_ad',
		'std' => '4',
		'class' => 'mini',
		'type' => 'text');
		
	$options[] = array(
		'desc' => __('Recommended Width: 200px or lower. HTML / PHP / Javascript allowed.', 'options_framework_theme'),
		'id' => 'frontpage4_ad_code',
		'type' => 'textarea');
		
	$options[] = array(
		'name' => __('Frontpage Thumbnail Ad #5', 'options_framework_theme'),
		'desc' => __('Display at X(th) position'),
		'id' => 'frontpage5_ad',
		'std' => '5',
		'class' => 'mini',
		'type' => 'text');
		
	$options[] = array(
		'desc' => __('Recommended Width: 200px or lower. HTML / PHP / Javascript allowed.', 'options_framework_theme'),
		'id' => 'frontpage5_ad_code',
		'type' => 'textarea');
		
        if ( has_filter('es_theme_options') ) {
                $options = apply_filters('es_theme_options', $options);
        }

	return $options;
}
?>
