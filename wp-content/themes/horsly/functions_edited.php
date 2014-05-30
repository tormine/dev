<?php
load_theme_textdomain('ipin', get_template_directory() . '/languages');

register_nav_menus(array('top_nav' => __('Top Navigation', 'ipin')));
register_sidebar(array('id' => 'sidebar-l', 'name' => 'sidebar-left', 'before_widget' => '', 'after_widget' => '', 'before_title' => '<h4>', 'after_title' => '</h4>'));
register_sidebar(array('id' => 'sidebar-r', 'name' => 'sidebar-right', 'before_widget' => '', 'after_widget' => '', 'before_title' => '<h4>', 'after_title' => '</h4>'));

add_theme_support('automatic-feed-links');
add_theme_support('post-thumbnails');
add_theme_support('custom-background', array('default-color' => 'f2f2f2'));
add_editor_style();

show_admin_bar(false);

if (!isset($content_width))
	$content_width = 600;   // Marveller

//Theme options
if (!function_exists( 'optionsframework_init')) {
	define('OPTIONS_FRAMEWORK_DIRECTORY', get_template_directory_uri() . '/inc/');
	require_once dirname( __FILE__ ) . '/inc/options-framework.php';
}


//Clean up wp head
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);


if ( function_exists( 'add_image_size' ) ) { 
	add_image_size( 'medium-thumb', 300, 200, true ); //(cropped)
}


//Opengraph
function ipin_opengraph() {
	if (is_single()) {
		global $post;
		setup_postdata($post);		
		$output = '<meta property="og:type" content="article" />' . "\n";
		$output .= '<meta property="og:title" content="' . esc_attr(get_the_title()) . '" />' . "\n";
		$output .= '<meta property="og:url" content="' . get_permalink() . '" />' . "\n";
		$output .= '<meta property="og:description" content="' . esc_attr(get_the_excerpt()) . '" />' . "\n";
		if (has_post_thumbnail()) {
			$imgsrc = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'large');
			$output .= '<meta property="og:image" content="' . $imgsrc[0] . '" />' . "\n";
		}
		echo $output;
	}
}
add_action( 'wp_head', 'ipin_opengraph' );


//Rewrite slug from /author/ to /user/
add_filter('init', create_function(
	'$a',
	'global $wp_rewrite;
	$wp_rewrite->author_base = "user";
    $wp_rewrite->author_structure = "/" . $wp_rewrite->author_base . "/%author%/";
	'
    )
);


//Rewrite source page template slug from /source/?domain=google.com to /source/google.com/
function add_query_vars($aVars) {
	$aVars[] = 'domain';
	return $aVars;
}
add_filter('query_vars', 'add_query_vars');

function add_rewrite_rules($aRules) {
	$aNewRules = array('source/([^/]+)/?$' => 'index.php?pagename=source&domain=$matches[1]');
	$aRules = $aNewRules + $aRules;
	return $aRules;
}
add_filter('rewrite_rules_array', 'add_rewrite_rules');


//Rewrite source page template <title> if all-in-one-seo-pack installed
function add_source_to_title($title) {
	global $wp_query;
	return __('Stories from', 'ipin') . ' ' . $wp_query->query_vars['domain'] . str_replace('Source ', ' ', $title);
}

function rewrite_source_title() {
	if (class_exists('All_in_One_SEO_Pack') && is_page('source')) {
		add_filter( 'aioseop_title_page', 'add_source_to_title');
	}
}
add_action('wp', 'rewrite_source_title'); //hook to wp early to use is_page


//Restrict /wp-admin/ to administrators
function ipin_restrict_admin() {
	if ((!defined('DOING_AJAX') || !DOING_AJAX) && !current_user_can('administrator') && !current_user_can('editor')) {
		wp_redirect(home_url());
		exit;
    }
}
add_action('admin_init', 'ipin_restrict_admin', 1);


//Redirect login page from wp-login.php to /login/
function ipin_login_url($login_url, $redirect){
	$login_url = home_url('/login/');

	if (!empty($redirect)) {
		//prevent duplicate redirect_to parameters
		$duplicate_redirect = substr_count($redirect, 'redirect_to');
		if ($duplicate_redirect >= 1) {
			$redirect = substr($redirect, 0, (strrpos($redirect, '?')));
		}
		
		$login_url = add_query_arg('redirect_to', urlencode($redirect), $login_url);
	} else {
		$login_url = add_query_arg('redirect_to', urlencode(home_url('/')), $login_url);
	}

	if ($force_reauth)
		$login_url = add_query_arg('reauth', '1', $login_url);

	return $login_url;
}
add_filter('login_url', 'ipin_login_url', 10, 2);


//Redirect login page if login failed
function ipin_login_fail($username) {
	$referrer = $_SERVER['HTTP_REFERER'];
	if (!empty($referrer) && !strstr($referrer,'wp-login') && !strstr($referrer,'wp-admin')) {
		//notify unverified users to activate their account
		$userdata = get_user_by('login', $username);
		$verify = get_user_meta($userdata->ID, '_Verify Email', true);
		//user with verified email do not have this usermeta field
		if ($verify != '') {
			$verify = '&email=unverified';
		}

		if (strpos($referrer, '&login=failed')) {
			wp_redirect($referrer . $verify);
		} else {
			wp_redirect($referrer . $verify . '&login=failed');
		}
		exit;
	}
}
add_action('wp_login_failed', 'ipin_login_fail');


//Check whether user verified their email
function ipin_verify_email($userdata) {
	$verify = get_user_meta($userdata->ID, '_Verify Email', true);
	//user with verified email do not have this usermeta field
	if ($verify != '') {
		return new WP_Error('email_unverified', $verify. __('Sorry, but your email is not verified. Please check your email for a verification link. -Hors.ly', 'ipin'));
	}
	return $userdata;
}
add_filter('wp_authenticate_user', 'ipin_verify_email', 1);


//Add user data after successful registration
function ipin_user_register($user_id) {
	//create a parent board
	$board_id = wp_insert_term (
		$user_id,
		'board'
	);
	update_user_meta($user_id, '_Board Parent ID', $board_id['term_id']);
	
		
	
	
	//set email notifications
	update_user_meta($user_id, 'ipin_user_notify_likes', '1');
	update_user_meta($user_id, 'ipin_user_notify_repins', '1');
	update_user_meta($user_id, 'ipin_user_notify_follows', '1');
	update_user_meta($user_id, 'ipin_user_notify_comments', '1');
	
	//remove url if register via WP Social Login plugin
	if (function_exists('wsl_activate')) {
		wp_update_user(array('ID' => $user_id, 'user_url' => '')) ;
	}
}
add_action('user_register', 'ipin_user_register');


//Check and add parent board upon login (in case user did not register through ipin pro theme register page
function ipin_wp_login($user_login, $user) {
	

	
	$board_parent_id = get_user_meta($user->ID, '_Board Parent ID', true);
	//create a parent board if not exists
	if ($board_parent_id == '') {
		$board_id = wp_insert_term (
			$user->ID,
			'board'
		);
		update_user_meta($user->ID, '_Board Parent ID', $board_id['term_id'] );
		
		
		//create default corresponding boards with categories for the the default categories
		$add_default_boards = array(
		
			'108' => 'Feeling Hors.ly',
			'18'=>   'News', 
			'19'=>  'Tournament', 
			'34'=>  'Horses', 
			'19'=>  'Longines GCT', 
			'19'=>  'Vienna Masters');
		
		$board_parent_id = get_user_meta($user->ID, '_Board Parent ID', true);
		foreach ($add_default_boards as $add_default_board) {
		
		$slug_unique= wp_unique_term_slug(array_values(str_replace(" ", "-", strtolower($add_default_board))) . '__ipinboard', 'board');
		wp_insert_term (array_values($add_default_board), 'board',
				array(
					'description' => key($add_default_board),
					'parent' => $board_parent_id,
					'slug' => $slug_unique,	)
			}
		
		
}
add_action('wp_login', 'ipin_wp_login', 10, 2);


//Exclude blog entries from homepage
function ipin_exclude_category($query) {
	if (!is_admin()) {
		$blog_cat_id = of_get_option('blog_cat_id');
		if (!$query->is_category($blog_cat_id)) {
			$query->set('cat', '-' . $blog_cat_id);
		}
		
		//exclude pages from search
		if ($query->is_search) {
			$query->set('post_type', 'post');	
		}
	}
	return $query;
}
add_action('pre_get_posts', 'ipin_exclude_category');


//Add boards taxonomy
function ipin_add_custom_taxonomies() {
	register_taxonomy('board', 'post', array(
		'hierarchical' => true,
		'public' => false,
		'labels' => array(
			'name' => 'Boards',
			'singular_name' => 'Board',
			'search_items' =>  'Search Boards',
			'all_items' => 'All Boards',
			'parent_item' => 'Parent Board',
			'parent_item_colon' => 'Parent Board:',
			'edit_item' => 'Edit Board',
			'update_item' => 'Update Board',
			'add_new_item' => 'Add New Board',
			'new_item_name' => 'New Board Name',
			'menu_name' => 'Boards'
		),
		'rewrite' => array(
			'slug' => 'board',
			'with_front' => false,
			'hierarchical' => true
		)
	));
}
add_action('init', 'ipin_add_custom_taxonomies', 0);

function ipin_board_permalink ($termlink, $term, $taxonomy) {
	if ($taxonomy == 'board')
		return home_url('/board/') . $term->term_id . '/';
	return $termlink;
}
add_filter('term_link', 'ipin_board_permalink', 10, 3);

function ipin_board_query($query) {
	if(isset($query->query_vars['board'])):
		if ($board = get_term_by('id', $query->query_vars['board'], 'board'))
			$query->query_vars['board'] = $board->slug;
	endif;
}
add_action('parse_query', 'ipin_board_query');


//Javascripts
function ipin_scripts() {
	global $user_login, $user_ID, $user_identity;
	
	if (!is_single()) {
		wp_enqueue_script('ipin_masonry', get_template_directory_uri() . '/js/jquery.masonry.min.js', array('jquery'), null, true);
		wp_enqueue_script('ipin_infinitescroll', get_template_directory_uri() . '/js/jquery.infinitescroll.min.js', array('jquery'), null, true);

		//for infinite scroll
		if (function_exists('wp_pagenavi')) {
			$nextSelector = '#navigation a:nth-child(3)';
		} else {
			$nextSelector = '#navigation #navigation-next a';
		}
	}
	
	if (is_singular() && comments_open() && get_option('thread_comments') && is_user_logged_in()) {
		wp_enqueue_script('comment-reply');
	}

	wp_enqueue_script('ipin_bootstrap', get_template_directory_uri() . '/js/bootstrap.min.js', array('jquery'), null, true);
	wp_enqueue_script('ipin_custom', get_template_directory_uri() . '/js/ipin.custom.js', array('jquery'), null, true);

	$translation_array = array(
		'__allitemsloaded' => __('All stories loaded', 'ipin'),
		'__addanotherpin' => __('Add another story', 'ipin'),
		'__addnewboard' => __('Add new board...', 'ipin'),
		'__boardalreadyexists' => __('That board already exists. Please try another title.', 'ipin'),
		'__errorpleasetryagain' => __('Error. Please try again.', 'ipin'),
		'__cancel' => __('Cancel', 'ipin'),
		'__close' => __('Close', 'ipin'),
		'__comment' => __('comment', 'ipin'),
		'__comments' => __('comments', 'ipin'),
		'__enternewboardtitle' => __('Enter new board title', 'ipin'),
		'__invalidimagefile' => __('Sorry but this is an invalid image file. Please choose a JPG/GIF/PNG file.', 'ipin'),
		'__like' => __('like', 'ipin'),
		'__likes' => __('likes', 'ipin'),
		'__Likes' => __('Likes', 'ipin'),
		'__loading' => __('Loading...', 'ipin'),
		'__pinit' => __('Share', 'ipin'),
		'__pinnedto' => __('Shared into', 'ipin'),
		'__pleaseenteratitle' => __('Please enter a title', 'ipin'),
		'__pleaseenterbothusernameandpassword' => __('Please enter both username and password.', 'ipin'),
		'__pleaseenterurl' => __('Please enter url', 'ipin'),
		'__repin' => __('repin', 'ipin'),
		'__repins' => __('repins', 'ipin'),
		'__Repins' => __('Shares', 'ipin'),
		'__repinnedto' => __('Reshared to', 'ipin'),
		'__seethispin' => __('See this story', 'ipin'),
		'__sorryunbaletofindanypinnableitems' => __('Sorry, we are unable to find any story items on here.. :(', 'ipin'),
		'__Video' => __('Video', 'ipin'),
		'__yourpinispendingreview' => __('Your story is pending review', 'ipin'),

		'ajaxurl' => admin_url('admin-ajax.php'),
		'avatar30' => get_avatar($user_ID, '30'),
		'avatar48' => get_avatar($user_ID, '48'),
		'blogname' => get_bloginfo('name'),
		'categories' => wp_dropdown_categories(array('show_option_none' => __('Category for new board', 'ipin'), 'exclude' => of_get_option('blog_cat_id') . ',1', 'hide_empty' => 0, 'name' => 'board-add-new-category', 'orderby' => 'name', 'echo' => 0)),
		'current_date' => date('j M Y g:ia', current_time('timestamp')),
		'home_url' => home_url(),
		'infinitescroll' => of_get_option('infinitescroll'),
		'login_url' => wp_login_url($_SERVER['REQUEST_URI']),
		'nextselector' => $nextSelector,
		'nonce' => wp_create_nonce('ajax-nonce'),
		'stylesheet_directory_uri' => get_template_directory_uri(),
		'u' => $user_ID,
		'ui' => $user_identity,
		'ul' => $user_login
	);
	
	wp_localize_script('ipin_custom', 'obj_ipin', $translation_array);
	
	wp_enqueue_script('twitter', 'http://platform.twitter.com/widgets.js', array(), null, true);
}
add_action('wp_enqueue_scripts', 'ipin_scripts');


// Make Twitter Bootstrap menu work with Wordpress Custom Menu
// From Roots Theme http://rootstheme.com
function is_element_empty($element) {
  $element = trim($element);
  return empty($element) ? false : true;
}

class Roots_Nav_Walker extends Walker_Nav_Menu {
  function check_current($classes) {
    return preg_match('/(current[-_])|active|dropdown/', $classes);
  }

  function start_lvl(&$output, $depth = 0, $args = array()) {
    $output .= "\n<ul class=\"dropdown-menu\">\n";
  }

  function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
    $item_html = '';
    parent::start_el($item_html, $item, $depth, $args);

    if ($item->is_dropdown && ($depth === 0)) {
      $item_html = str_replace('<a', '<a class="dropdown-toggle" data-toggle="dropdown" data-target="#"', $item_html);
      $item_html = str_replace('</a>', ' <b class="caret"></b></a>', $item_html);
    }
    elseif (stristr($item_html, 'li class="divider')) {
      $item_html = preg_replace('/<a[^>]*>.*?<\/a>/iU', '', $item_html);    
    }
    elseif (stristr($item_html, 'li class="nav-header')) {
      $item_html = preg_replace('/<a[^>]*>(.*)<\/a>/iU', '$1', $item_html);
    }   

    $output .= $item_html;
  }

  function display_element($element, &$children_elements, $max_depth, $depth = 0, $args, &$output) {
    $element->is_dropdown = !empty($children_elements[$element->ID]);

    if ($element->is_dropdown) {
      if ($depth === 0) {
        $element->classes[] = 'dropdown';
      } elseif ($depth === 1) {
        $element->classes[] = 'dropdown-submenu';
      }
    }

    parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
  }
}

/**
 * Remove the id="" on nav menu items
 * Return 'menu-slug' for nav menu classes
 */
function roots_nav_menu_css_class($classes, $item) {
  $slug = sanitize_title($item->title);
  $classes = preg_replace('/(current(-menu-|[-_]page[-_])(item|parent|ancestor))/', 'active', $classes);
  $classes = preg_replace('/^((menu|page)[-_\w+]+)+/', '', $classes);

  $classes[] = 'menu-' . $slug;

  $classes = array_unique($classes);

  return array_filter($classes, 'is_element_empty');
}

add_filter('nav_menu_css_class', 'roots_nav_menu_css_class', 10, 2);
add_filter('nav_menu_item_id', '__return_null');

/**
 * Clean up wp_nav_menu_args
 *
 * Remove the container
 * Use Roots_Nav_Walker() by default
 */
function roots_nav_menu_args($args = '') {
  $roots_nav_menu_args['container'] = false;

  if (!$args['items_wrap']) {
    $roots_nav_menu_args['items_wrap'] = '<ul class="%2$s">%3$s</ul>';
  }

  if (current_theme_supports('bootstrap-top-navbar')) {
    $roots_nav_menu_args['depth'] = 3;
  }

  if (!$args['walker']) {
    $roots_nav_menu_args['walker'] = new Roots_Nav_Walker();
  }

  return array_merge($args, $roots_nav_menu_args);
}

add_filter('wp_nav_menu_args', 'roots_nav_menu_args');


//Relative date modified from wp-includes/formatting.php
function ipin_human_time_diff( $from, $to = '' ) {
	if ( empty($to) )
		$to = time();
	$diff = (int) abs($to - $from);
	if ($diff <= 3600) {
		$mins = round($diff / 60);
		if ($mins <= 1) {
			$mins = 1;
		}

		if ($mins == 1) {
			$since = $mins . ' ' . __('min ago', 'ipin');
		} else {
			$since = $mins . ' ' . __('mins ago', 'ipin');
		}
	} else if (($diff <= 86400) && ($diff > 3600)) {
		$hours = round($diff / 3600);
		if ($hours <= 1) {
			$hours = 1;
		}
		
		if ($hours == 1) {
			$since = $hours . ' ' . __('hour ago', 'ipin');
		} else {
			$since = $hours . ' ' . __('hours ago', 'ipin');
		}
	} else if ($diff >= 86400 && $diff <= 31536000) {
		$days = round($diff / 86400);
		if ($days <= 1) {
			$days = 1;
		}

		if ($days == 1) {
			$since = $days . ' ' . __('day ago', 'ipin');
		} else {
			$since = $days . ' ' . __('days ago', 'ipin');
		}
	} else {
		$since = get_the_date();
	}
	return $since;
}


//Feed content for pins
function ipin_feed_content($content) {
	global $post;
	
	$boards = get_the_terms($post->ID, 'board');

	if ($boards) {
		foreach ($boards as $board) {
			$board_name = $board->name;
			$board_slug = $board->slug;
		}
		
		$board_link = get_term_link($board_slug, 'board');

		$imgsrc = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'medium');
		if ($imgsrc[0] == '') {
			$imgsrc[0] = get_template_directory_uri() . '/img/blank.gif';
		}		

		$content_before = '<p><a href="' . get_permalink($post->ID) . '"><img src="' . $imgsrc[0] . '" alt="" /></a>';
		$content_before .= '<br />' . __('Shared into', 'ipin') . ' <a href="' . $board_link . '">' . $board_name . '</a></p>';
	}
	
	return ($content_before . $content);
}

add_filter('the_excerpt_rss', 'ipin_feed_content');
add_filter('the_content_feed', 'ipin_feed_content');


//Comments
function ipin_list_comments($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment;
	?>
	<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">

		<?php $comment_author = get_user_by('id', $comment->user_id); ?>
		<div class="comment-avatar">
			<?php if ($comment_author) { ?>
			<a href="<?php echo home_url('/user/') . $comment_author->user_login; ?>/">
			<?php } ?>
				<?php echo get_avatar($comment->user_id, '48'); ?>
			<?php if ($comment_author) { ?>
			</a>
			<?php } ?>
		</div>

		<div class="pull-right"><?php comment_reply_link(array('reply_text' => __('Reply', 'ipin'), 'login_text' => __('Reply', 'ipin'), 'depth' => $depth, 'max_depth'=> $args['max_depth'])); ?></div>

		<div class="comment-content">

			<strong><span <?php comment_class(); ?>>
			<?php if ($comment_author) { ?>
			<a class="url" href="<?php echo home_url('/user/') . $comment_author->user_login; ?>/">
			<?php } ?>
				<?php echo $comment->comment_author; ?>
			<?php if ($comment_author) { ?>
			</a>
			<?php } ?>
			
			</span></strong> / <?php comment_date('j M Y g:ia'); ?> <a href="#comment-<?php comment_ID() ?>" title="<?php esc_attr_e('Comment Permalink', 'ipin'); ?>">#</a> <?php edit_comment_link('e','',''); ?>
			<?php if ($comment->comment_approved == '0') : ?>
			<br /><em><?php _e('Your comment is awaiting moderation.', 'ipin'); ?></em>
			<?php endif; ?>
	
			<?php comment_text(); ?>
		</div>
	<?php
}


//Repins
function ipin_repin() {
	$nonce = $_POST['nonce'];

	if (!wp_verify_nonce($nonce, 'ajax-nonce'))
		die();

	global $user_ID, $user_identity;
	$original_id  = $_POST['repin_post_id'];
	$duplicate = get_post($original_id, 'ARRAY_A');
	$original_post_author = $duplicate['post_author']; //store original author for use later
	$duplicate['post_author'] = $user_ID;
	$duplicate['post_title'] = sanitize_text_field($_POST['repin_title']);

	unset($duplicate['ID']);
	unset($duplicate['post_date']);
	unset($duplicate['post_date_gmt']);
	unset($duplicate['post_modified']);
	unset($duplicate['post_modified_gmt']);
	unset($duplicate['post_name']);
	unset($duplicate['guid']);
	unset($duplicate['comment_count']);

	$duplicate_id = wp_insert_post($duplicate);

	//set board
	$board_add_new = sanitize_text_field($_POST['repin_board_add_new']);
	$board_add_new_category = $_POST['repin_board_add_new_category'];
	$board_parent_id = get_user_meta($user_ID, '_Board Parent ID', true);
	if ($board_add_new !== '') {
		$board_children = get_term_children($board_parent_id, 'board');
		$found = '0';

		foreach ($board_children as $board_child) {
			$board_child_term = get_term_by('id', $board_child, 'board');
			if (stripslashes(htmlspecialchars($board_add_new, ENT_NOQUOTES, 'UTF-8')) == $board_child_term->name) {
				$found = '1';
				$found_board_id = $board_child_term->term_id;
				break;
			}
		}

		if ($found == '0') {
			$slug = wp_unique_term_slug($board_add_new . '__ipinboard', 'board'); //append __ipinboard to solve slug conflict with category and 0 in title
			if ($board_add_new_category == '-1')
				$board_add_new_category = '1';

			$new_board_id = wp_insert_term (
				$board_add_new,
				'board',
				array(
					'description' => $board_add_new_category,
					'parent' => $board_parent_id,
					'slug' => $slug
				)
			);
			
			$repin_board = $new_board_id['term_id'];
		} else {
			$repin_board = $found_board_id;
		}
	} else {
		$repin_board = $_POST['repin_board'];		
	}
	wp_set_post_terms($duplicate_id, array($repin_board), 'board');

	//set category
	$category_id = get_term_by('id', $repin_board, 'board');
	wp_set_post_terms($duplicate_id, array($category_id->description), 'category');

	//update postmeta for new post
	if ('' == $repin_of_repin = get_post_meta($original_id, '_Original Post ID', true)) { //check if is a simple repin or a repin of a repin
		add_post_meta($duplicate_id, '_Original Post ID', $original_id);
	} else {
		add_post_meta($duplicate_id, '_Original Post ID', $original_id);
		add_post_meta($duplicate_id, '_Earliest Post ID', $repin_of_repin); //the very first post/pin		
	}
	add_post_meta($duplicate_id, '_Photo Source', get_post_meta($original_id, '_Photo Source', true));
	add_post_meta($duplicate_id, '_Photo Source Domain', get_post_meta($original_id, '_Photo Source Domain', true));
	add_post_meta($duplicate_id, '_thumbnail_id', get_post_meta($original_id, '_thumbnail_id', true));

	//update postmeta for original post
	$postmeta_repin_count = get_post_meta($original_id, '_Repin Count', true);
	$postmeta_repin_post_id = get_post_meta($original_id, '_Repin Post ID');
	$repin_post_id = $postmeta_repin_post_id[0];

	if (!is_array($repin_post_id))
		$repin_post_id = array();

	array_push($repin_post_id, $duplicate_id);
	update_post_meta($original_id, '_Repin Post ID', $repin_post_id);
	update_post_meta($original_id, '_Repin Count', ++$postmeta_repin_count);

	//email author
	if (get_user_meta($original_post_author, 'ipin_user_notify_repins', true) != '' && $user_ID != $original_post_author) {
		$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
		$message = sprintf(__('%s reshared your "%s" story at %s', 'ipin'), $user_identity, html_entity_decode(get_the_title($original_id), ENT_QUOTES, 'UTF-8'), get_permalink($duplicate_id)) . "\r\n\r\n";;
		$message .= "-------------------------------------------\r\n";
		$message .= sprintf(__('To change your notification settings, visit %s', 'ipin'), home_url('/settings/'));
		wp_mail(get_the_author_meta('user_email', $original_post_author), sprintf(__('[%s] Someone reshared your story. :)', 'ipin'), $blogname), $message);
	}
	
	//add new board to followers who fully follow user
	if ($new_board_id && !is_wp_error($new_board_id)) {
		$usermeta_followers_id_allboards = get_user_meta($user_ID, '_Followers User ID All Boards');
		$followers_id_allboards = $usermeta_followers_id_allboards[0];
		
		if (!empty($followers_id_allboards)) {
			foreach ($followers_id_allboards as $followers_id_allboard) {
				$usermeta_following_board_id = get_user_meta($followers_id_allboard, '_Following Board ID');
				$following_board_id = $usermeta_following_board_id[0];
				array_unshift($following_board_id, $new_board_id['term_id']);
				update_user_meta($followers_id_allboard, '_Following Board ID', $following_board_id);
			}
		}
	}
	
	echo get_permalink($duplicate_id);

	exit;
}
add_action('wp_ajax_ipin-repin', 'ipin_repin');

function ipin_repin_board_populate() {
	global $user_ID;

	$board_parent_id = get_user_meta($user_ID, '_Board Parent ID', true);
	$board_children_count = wp_count_terms('board', array('parent' => $board_parent_id));
	
	
	if (is_array($board_children_count) || $board_children_count == 0) {
		echo '<span id="noboard">' . wp_dropdown_categories(array('echo' => 0, 'show_option_none' => __('Add a new board first...', 'ipin'), 'taxonomy' => 'board', 'parent' => $board_parent_id, 'hide_empty' => 0, 'name' => 'board', 'hierarchical' => true));
		echo '</span>';
	} else {
		echo wp_dropdown_categories(array('echo' => 0, 'taxonomy' => 'board', 'parent' => $board_parent_id, 'hide_empty' => 0, 'name' => 'board', 'hierarchical' => true, 'orderby' => 'name')); // edited by Marveller
	}
	exit;
}
add_action('wp_ajax_ipin-repin-board-populate', 'ipin_repin_board_populate');


//Likes
function ipin_like() {
	$nonce = $_POST['nonce'];

	if (!wp_verify_nonce($nonce, 'ajax-nonce'))
		die();

	global $user_ID, $user_identity;
	$post_id = $_POST['post_id'];

	if ($_POST['ipin_like'] == 'like') {
		$postmeta_count = get_post_meta($post_id, '_Likes Count', true);
		$postmeta_user_id = get_post_meta($post_id, '_Likes User ID');
		$likes_user_id = $postmeta_user_id[0];

		if (!is_array($likes_user_id))
			$likes_user_id = array();

		//update postmeta
		array_push($likes_user_id, $user_ID);
		update_post_meta($post_id, '_Likes User ID', $likes_user_id);
		update_post_meta($post_id, '_Likes Count', ++$postmeta_count);

		//update usermeta
		$usermeta_count = get_user_meta($user_ID, '_Likes Count', true);
		$usermeta_post_id = get_user_meta($user_ID, '_Likes Post ID');
		$likes_post_id = $usermeta_post_id[0];

		if (!is_array($likes_post_id))
			$likes_post_id = array();

		array_unshift($likes_post_id, $post_id);

		update_user_meta($user_ID, '_Likes Post ID', $likes_post_id);
		update_user_meta($user_ID, '_Likes Count', ++$usermeta_count);

		//email author
		if (get_user_meta($_POST['post_author'], 'ipin_user_notify_likes', true) != '') {
			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
			$message = sprintf(__('%s likes your "%s" story at %s', 'ipin'), $user_identity, html_entity_decode(get_the_title($post_id), ENT_QUOTES, 'UTF-8'), get_permalink($post_id)) . "\r\n\r\n";
			$message .= "-------------------------------------------\r\n";
			$message .= sprintf(__('To change your notification settings, visit %s', 'ipin'), home_url('/settings/'));
			wp_mail(get_the_author_meta('user_email', $_POST['post_author']), sprintf(__('[%s] Someone likes your story', 'ipin'), $blogname), $message);
		}

		echo $postmeta_count;

	} else if ($_POST['ipin_like'] == 'unlike') {
		//update postmeta
		$postmeta_count = get_post_meta($post_id, '_Likes Count', true);
		$postmeta_user_id = get_post_meta($post_id, '_Likes User ID');
		$likes_user_id = $postmeta_user_id[0];
		unset($likes_user_id[array_search($user_ID, $likes_user_id)]);
		$likes_user_id = array_values($likes_user_id);
		update_post_meta($post_id, '_Likes User ID', $likes_user_id);
		update_post_meta($post_id, '_Likes Count', --$postmeta_count);

		//update usermeta
		$usermeta_count = get_user_meta($user_ID, '_Likes Count', true);
		$usermeta_post_id = get_user_meta($user_ID, '_Likes Post ID');
		$likes_post_id = $usermeta_post_id[0];

		unset($likes_post_id[array_search($post_id, $likes_post_id)]);
		$likes_post_id = array_values($likes_post_id);

		update_user_meta($user_ID, '_Likes Post ID', $likes_post_id);
		update_user_meta($user_ID, '_Likes Count', --$usermeta_count);

		echo $postmeta_count;
	}
	
	exit;
}
add_action('wp_ajax_ipin-like', 'ipin_like');

function ipin_liked($post_id) {
	global $user_ID;
	$postmeta_user_id = get_post_meta($post_id, '_Likes User ID');
	$likes_user_id = $postmeta_user_id[0];

	if (!is_array($likes_user_id))
		$likes_user_id = array();

	if (in_array($user_ID, $likes_user_id)) {
		return true;
	}
	return false;
}


//Follows
function ipin_follow() {
	$nonce = $_POST['nonce'];

	if (!wp_verify_nonce($nonce, 'ajax-nonce'))
		die();
	
	global $user_ID, $user_identity;
	$board_parent_id = $_POST['board_parent_id'];
	$board_id = $_POST['board_id'];
	$author_id = $_POST['author_id'];

	if ($_POST['ipin_follow'] == 'follow') {
		//update usermeta following for current user
		$usermeta_following_count = get_user_meta($user_ID, '_Following Count', true);
		$usermeta_following_user_id = get_user_meta($user_ID, '_Following User ID');
		$following_user_id = $usermeta_following_user_id[0];
		$usermeta_following_board_id = get_user_meta($user_ID, '_Following Board ID');
		$following_board_id = $usermeta_following_board_id[0];

		if (!is_array($following_user_id))
			$following_user_id = array();

		if (!is_array($following_board_id))
			$following_board_id = array();

		if ($board_parent_id == '0') {
			//insert all sub-boards from author
			$author_boards = get_term_children($board_id, 'board');

			foreach ($author_boards as $author_board) {
				if (!in_array($author_board, $following_board_id)) {
					array_unshift($following_board_id, $author_board);
				}
			}

			//track followers who fully follow user to update them when user create a new board
			$usermeta_followers_id_allboards = get_user_meta($author_id, '_Followers User ID All Boards');
			$followers_id_allboards = $usermeta_followers_id_allboards[0];

			if (!is_array($followers_id_allboards))
				$followers_id_allboards = array();

			if (!in_array($user_ID, $followers_id_allboards)) {
				array_unshift($followers_id_allboards, $user_ID);
				update_user_meta($author_id, '_Followers User ID All Boards', $followers_id_allboards);
			}
		}
		array_unshift($following_board_id, $board_id);
		update_user_meta($user_ID, '_Following Board ID', $following_board_id);

		if (!in_array($author_id, $following_user_id)) {
			array_unshift($following_user_id, $author_id);
			update_user_meta($user_ID, '_Following User ID', $following_user_id);
			update_user_meta($user_ID, '_Following Count', ++$usermeta_following_count);
		}

		//update usermeta followers for author
		$usermeta_followers_count = get_user_meta($author_id, '_Followers Count', true);
		$usermeta_followers_id = get_user_meta($author_id, '_Followers User ID');
		$followers_id = $usermeta_followers_id[0];

		if (!is_array($followers_id))
			$followers_id = array();

		if (!in_array($user_ID, $followers_id)) {
			array_unshift($followers_id, $user_ID);
			update_user_meta($author_id, '_Followers User ID', $followers_id);
			update_user_meta($author_id, '_Followers Count', ++$usermeta_followers_count);
		}

		//email author
		if (get_user_meta($author_id, 'ipin_user_notify_follows', true) != '') {
			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
			$message = sprintf(__('%s is now following you. View %s\'s profile at %s', 'ipin'), $user_identity, $user_identity, get_author_posts_url($user_ID)) . "\r\n\r\n";
			$message .= "-------------------------------------------\r\n";
			$message .= sprintf(__('To change your notification settings, visit %s', 'ipin'), home_url('/settings/'));
			wp_mail(get_the_author_meta('user_email', $author_id), sprintf(__('[%s] Someone is following you', 'ipin'), $blogname), $message);
		}
	} else if ($_POST['ipin_follow'] == 'unfollow') {		
		//update usermeta following for current user
		$usermeta_following_count = get_user_meta($user_ID, '_Following Count', true);
		$usermeta_following_user_id = get_user_meta($user_ID, '_Following User ID');
		$following_user_id = $usermeta_following_user_id[0];
		$usermeta_following_board_id = get_user_meta($user_ID, '_Following Board ID');
		$following_board_id = $usermeta_following_board_id[0];

		if ($board_parent_id == '0') {
			$author_boards = get_term_children($board_id, 'board');

			//prepare to remove all boards from author
			foreach ($author_boards as $author_board) {
				if (in_array($author_board, $following_board_id)) {
					unset($following_board_id[array_search($author_board, $following_board_id)]);
					$following_board_id = array_values($following_board_id);
				}
			}

			//remove parent board as well
			unset($following_board_id[array_search($board_id, $following_board_id)]);
			$following_board_id = array_values($following_board_id);

			unset($following_user_id[array_search($author_id, $following_user_id)]);
			$following_user_id = array_values($following_user_id);

			update_user_meta($user_ID, '_Following Board ID', $following_board_id);
			update_user_meta($user_ID, '_Following User ID', $following_user_id);
			update_user_meta($user_ID, '_Following Count', --$usermeta_following_count);

			//update usermeta followers for author
			$usermeta_followers_count = get_user_meta($author_id, '_Followers Count', true);

			$usermeta_followers_id = get_user_meta($author_id, '_Followers User ID');
			$followers_id = $usermeta_followers_id[0];
			unset($followers_id[array_search($user_ID, $followers_id)]);
			$followers_id = array_values($followers_id);

			$usermeta_followers_id_allboards = get_user_meta($author_id, '_Followers User ID All Boards');
			$followers_id_allboards = $usermeta_followers_id_allboards[0];
			unset($followers_id_allboards[array_search($user_ID, $followers_id_allboards)]);
			$followers_id_allboards = array_values($followers_id_allboards);

			update_user_meta($author_id, '_Followers User ID', $followers_id);
			update_user_meta($author_id, '_Followers User ID All Boards', $followers_id_allboards);
			update_user_meta($author_id, '_Followers Count', --$usermeta_followers_count);
			
			echo 'unfollow_all';
		} else {
			unset($following_board_id[array_search($board_id, $following_board_id)]);
			$following_board_id = array_values($following_board_id);

			$author_boards = get_term_children($board_parent_id, 'board');
			$board_following_others = 'no';

			//check if current user is following other boards from author
			//if no longer following other boards, also unfollow user
			foreach ($following_board_id as $following_board) {
				if (in_array($following_board, $author_boards)) {
					$board_following_others = 'yes';
					break;
				}
			}

			if ($board_following_others == 'no') {
				//remove parent board
				unset($following_board_id[array_search($board_parent_id, $following_board_id)]);
				$following_board_id = array_values($following_board_id);

				unset($following_user_id[array_search($author_id, $following_user_id)]);
				$following_user_id = array_values($following_user_id);

				update_user_meta($user_ID, '_Following User ID', $following_user_id);
				update_user_meta($user_ID, '_Following Count', --$usermeta_following_count);

				//update usermeta followers for author
				$usermeta_followers_count = get_user_meta($author_id, '_Followers Count', true);

				$usermeta_followers_id = get_user_meta($author_id, '_Followers User ID');
				$followers_id = $usermeta_followers_id[0];
				unset($followers_id[array_search($user_ID, $followers_id)]);
				$followers_id = array_values($followers_id);

				$usermeta_followers_id_allboards = get_user_meta($author_id, '_Followers User ID All Boards');
				$followers_id_allboards = $usermeta_followers_id_allboards[0];
				unset($followers_id_allboards[array_search($user_ID, $followers_id_allboards)]);
				$followers_id_allboards = array_values($followers_id_allboards);

				update_user_meta($author_id, '_Followers User ID', $followers_id);
				update_user_meta($author_id, '_Followers User ID All Boards', $followers_id_allboards);
				update_user_meta($author_id, '_Followers Count', --$usermeta_followers_count);

				echo 'unfollow_all';
			}
			update_user_meta($user_ID, '_Following Board ID', $following_board_id);
		}
	}
	
	exit;
}
add_action('wp_ajax_ipin-follow', 'ipin_follow');

function ipin_followed($board_id) {
	global $user_ID;
	$usermeta_board_id = get_user_meta($user_ID, '_Following Board ID');
	$follow_board_id = $usermeta_board_id[0];

	if (!is_array($follow_board_id))
		$follow_board_id = array();
	
	if (in_array($board_id, $follow_board_id)) {
		return true;
	}
	return false;
}


//Ajax comments
function ipin_ajaxify_comments($comment_ID, $comment_status) {
	if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
		if ('spam' !== $comment_status) {
			if ('0' == $comment_status) {
				wp_notify_moderator($comment_ID);
			} else if ('1' == $comment_status) {
				//email author
				global $user_ID, $user_identity;
				$commentdata = get_comment($comment_ID, 'ARRAY_A');
				$postdata = get_post($commentdata['comment_post_ID'], 'ARRAY_A');
				$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);


				if (get_user_meta($postdata['post_author'], 'ipin_user_notify_comments', true) != '' && $user_ID != $postdata['post_author']) {
					$message = sprintf(__('%s commented on your "%s" story at %s', 'ipin'), $user_identity, html_entity_decode($postdata['post_title'], ENT_QUOTES, 'UTF-8'), get_permalink($postdata['ID'])) . "\r\n\r\n";
					$message .= "-------------------------------------------\r\n";
					$message .= sprintf(__('To change your notification settings, visit %s', 'ipin'), home_url('/settings/'));
					wp_mail(get_the_author_meta('user_email', $postdata['post_author']), sprintf(__('[%s] Someone commented on your story', 'ipin'), $blogname), $message);
				}
				
				$comment_author_domain = @gethostbyaddr($commentdata['comment_author_IP']);
				
				//email admin
				if (get_option('comments_notify') && $user_ID != $postdata['post_author']) {
					$admin_message  = sprintf(__('New comment on the post "%s"', 'ipin'), html_entity_decode($postdata['post_title'], ENT_QUOTES, 'UTF-8')) . "\r\n";
					$admin_message .= sprintf(__('Author : %1$s (IP: %2$s , %3$s)', 'ipin'), $commentdata['comment_author'], $commentdata['comment_author_IP'], $comment_author_domain) . "\r\n";
					$admin_message .= sprintf(__('E-mail : %s', 'ipin'), $commentdata['comment_author_email']) . "\r\n";
					$admin_message .= sprintf(__('URL    : %s', 'ipin'), $commentdata['comment_author_url']) . "\r\n";
					$admin_message .= sprintf(__('Whois  : http://whois.arin.net/rest/ip/%s', 'ipin'), $commentdata['comment_author_IP']) . "\r\n";
					$admin_message .= __('Comment: ', 'ipin') . "\r\n" . $commentdata['comment_content'] . "\r\n\r\n";
					$admin_message .= __('You can see all comments on this post here: ', 'ipin') . "\r\n";
					$admin_message .= get_permalink($postdata['ID']) . "#comments\r\n\r\n";
					$admin_message .= sprintf(__('Permalink: %s', 'ipin'), get_permalink($postdata['ID']) . '#comment-' . $comment_ID) . "\r\n";
					$admin_message .= sprintf(__('Delete it: %s', 'ipin'), admin_url("comment.php?action=delete&c=$comment_ID")) . "\r\n";
					$admin_message .= sprintf(__('Spam it: %s', 'ipin'), admin_url("comment.php?action=spam&c=$comment_ID")) . "\r\n";
					$admin_subject = sprintf(__('[%1$s] Comment: "%2$s"', 'ipin'), $blogname, html_entity_decode($postdata['post_title'], ENT_QUOTES, 'UTF-8'));
					wp_mail(get_option('admin_email'), $admin_subject, $admin_message);
				}

				echo 'success';
			}
		}
		exit;
	}
}
add_action('comment_post', 'ipin_ajaxify_comments', 20, 2);


//Clean up postmeta & usermeta when delete post
function ipin_delete_post_clean($post_id) {
	global $wpdb;

	$original_id = get_post_meta($post_id, '_Original Post ID', true);

	if ($original_id == '') { //this is an original post
		//remove instances from repinned postmeta
		$wpdb->query(
			$wpdb->prepare("UPDATE $wpdb->postmeta
						SET meta_value = 'deleted'
						WHERE meta_key = '_Original Post ID'
						AND meta_value = %s
						"
						,$post_id)
		);

		//remove instances from repinned of repinned postmeta
		$wpdb->query(
			$wpdb->prepare("UPDATE $wpdb->postmeta
						SET meta_value = 'deleted'
						WHERE meta_key = '_Earliest Post ID'
						AND meta_value = %s
						"
						,$post_id)
		);

		//remove instances from usermeta
		$postmeta_likes_user_ids = get_post_meta($post_id, '_Likes User ID');
		$likes_user_ids = $postmeta_likes_user_ids[0];

		if (is_array($likes_user_ids)) {
			foreach ($likes_user_ids as $likes_user_id) {
				$usermeta_count = get_user_meta($likes_user_id, '_Likes Count', true);
				$usermeta_post_id = get_user_meta($likes_user_id, '_Likes Post ID');
				$likes_post_id = $usermeta_post_id[0];
		
				unset($likes_post_id[array_search($post_id, $likes_post_id)]);
				$likes_post_id = array_values($likes_post_id);
		
				update_user_meta($likes_user_id, '_Likes Post ID', $likes_post_id);
				update_user_meta($likes_user_id, '_Likes Count', --$usermeta_count);
			}
		}
	} else { //this is a repinned post
		//remove instances from repinned postmeta
		$wpdb->query(
			$wpdb->prepare("UPDATE $wpdb->postmeta
						SET meta_value = 'deleted'
						WHERE meta_key = '_Original Post ID'
						AND meta_value = %s
						"
						,$post_id)
		);

		//remove instances from original postmeta
		$postmeta_repin_count = get_post_meta($original_id, '_Repin Count', true);
		$postmeta_repin_post_id = get_post_meta($original_id, '_Repin Post ID');
		$repin_post_id = $postmeta_repin_post_id[0];
		unset($repin_post_id[array_search($post_id, $repin_post_id)]);
		$repin_post_id = array_values($repin_post_id);

		update_post_meta($original_id, '_Repin Post ID', $repin_post_id);
		update_post_meta($original_id, '_Repin Count', --$postmeta_repin_count);

		//remove instances from usermeta
		$postmeta_likes_user_ids = get_post_meta($post_id, '_Likes User ID');
		$likes_user_ids = $postmeta_likes_user_ids[0];

		if (is_array($likes_user_ids)) {
			foreach ($likes_user_ids as $likes_user_id) {
				$usermeta_count = get_user_meta($likes_user_id, '_Likes Count', true);
				$usermeta_post_id = get_user_meta($likes_user_id, '_Likes Post ID');
				$likes_post_id = $usermeta_post_id[0];
		
				unset($likes_post_id[array_search($post_id, $likes_post_id)]);
				$likes_post_id = array_values($likes_post_id);
		
				update_user_meta($likes_user_id, '_Likes Post ID', $likes_post_id);
				update_user_meta($likes_user_id, '_Likes Count', --$usermeta_count);
			}
		}
	}
}
add_action('before_delete_post', 'ipin_delete_post_clean');


//Clean up usermeta & boards when delete user
function ipin_delete_user_clean($id) {
	global $wpdb;

	//user_id is name of parent board
	$board_parent_id = get_user_meta($id, '_Board Parent ID', true);
	$child_boards = get_term_children($board_parent_id, 'board');
	array_push($child_boards, $board_parent_id);

	//remove likes from postmeta
	$usermeta_likes_post_ids = get_user_meta($id, '_Likes Post ID');

	if (!empty($usermeta_likes_post_ids[0])) {
		foreach ($usermeta_likes_post_ids[0] as $likes_post_id) {
			$postmeta_likes_count = get_post_meta($likes_post_id, '_Likes Count', true);
			$postmeta_likes_user_id = get_post_meta($likes_post_id, '_Likes User ID');
			$likes_user_id = $postmeta_likes_user_id[0];
	
			unset($likes_user_id[array_search($id, $likes_user_id)]);
			$likes_user_id = array_values($likes_user_id);
	
			update_post_meta($likes_post_id, '_Likes User ID', $likes_user_id);
			update_post_meta($likes_post_id, '_Likes Count', --$postmeta_likes_count);
		}
	}

	//remove instances from followers
	$followers = get_user_meta($id, '_Followers User ID');
	
	if(!empty($followers[0])) {
		foreach ($followers[0] as $follower) {
			$usermeta_following_count = get_user_meta($follower, '_Following Count', true);
			$usermeta_following_user_id = get_user_meta($follower, '_Following User ID');
			$following_user_id = $usermeta_following_user_id[0];

			unset($following_user_id[array_search($id, $following_user_id)]);
			$following_user_id = array_values($following_user_id);

			update_user_meta($follower, '_Following User ID', $following_user_id);
			update_user_meta($follower, '_Following Count', --$usermeta_following_count);

			//delete board from followers usermeta
			foreach ($child_boards as $child_board) {
				$usermeta_following_board_id = get_user_meta($follower, '_Following Board ID');
				$following_board_id = $usermeta_following_board_id[0];
				
				unset($following_board_id[array_search($child_board, $following_board_id)]);
				$following_board_id = array_values($following_board_id);
				update_user_meta($follower, '_Following Board ID', $following_board_id);	
			}
		}
	}
	
	//remove instances from following users
	$following = get_user_meta($id, '_Following User ID');
	
	if(!empty($following[0])) {
		foreach ($following[0] as $following) {
			$usermeta_followers_count = get_user_meta($following, '_Followers Count', true);
			$usermeta_followers_user_id = get_user_meta($following, '_Followers User ID');
			$followers_user_id = $usermeta_followers_user_id[0];
			$usermeta_followers_user_id_all_boards = get_user_meta($following, '_Followers User ID All Boards');
			$followers_user_id_all_boards = $usermeta_followers_user_id_all_boards[0];

			unset($followers_user_id[array_search($id, $followers_user_id)]);
			$followers_user_id = array_values($followers_user_id);
			
			unset($followers_user_id_all_boards[array_search($id, $followers_user_id_all_boards)]);
			$followers_user_id_all_boards = array_values($followers_user_id_all_boards);

			update_user_meta($following, '_Followers User ID', $followers_user_id);
			update_user_meta($following, '_Followers Count', --$usermeta_followers_count);
			update_user_meta($following, '_Followers User ID All Boards', $followers_user_id_all_boards);
		}
	}

	//finally delete the boards
	foreach ($child_boards as $child_board) {
		wp_delete_term($child_board, 'board');
	}
	
}
add_action('delete_user', 'ipin_delete_user_clean');


//Prune posts
function ipin_add_cron_schedule($schedules) {
	$prune_duration = of_get_option('prune_duration') * 60;
	
    $schedules['ipin_prune'] = array(
        'interval' => $prune_duration,
        'display'  => 'Prune Duration'
    );
 
    return $schedules;
}
add_filter('cron_schedules', 'ipin_add_cron_schedule');

if (!wp_next_scheduled( 'ipin_cron_action' )) {
    wp_schedule_event(time(), 'ipin_prune', 'ipin_cron_action');
}
 
function ipin_cron_function() {
	global $wpdb;
	
	$prune_postnumber = of_get_option('prune_postnumber');
	
	$posts = $wpdb->get_results("
		SELECT ID FROM $wpdb->posts
		WHERE post_status = 'ipin_prune'
		LIMIT $prune_postnumber
	");
	
	if ($posts) {
		foreach ($posts as $post) {
			wp_delete_post($post->ID, true);
		}
	}
}
add_action('ipin_cron_action', 'ipin_cron_function');


//Change default email
function ipin_mail_from($email)
{
	if ('' != $outgoing_email = of_get_option('outgoing_email')) {
		return $outgoing_email;
	} else {
		return $email;
	}
}
add_filter('wp_mail_from', 'ipin_mail_from');

function ipin_mail_from_name($name)
{
	if ('' != $outgoing_email_name = of_get_option('outgoing_email_name')) {
		return $outgoing_email_name;
	} else {
		return $name;
	}
}
add_filter('wp_mail_from_name', 'ipin_mail_from_name');

// THIS HERE
//Local avatar
function ipin_local_avatar($avatar, $id_or_email, $size, $default, $alt) {
	if (!is_admin()) {
		$avatar_id = get_user_meta($id_or_email, 'ipin_user_avatar', true);
	
		if ($avatar_id != '') {
			if (intval($size) <= 48) {
				$imgsrc = wp_get_attachment_image_src($avatar_id, 'avatar48');
				return '<img alt="avatar" src="' . $imgsrc[0] . '" class="avatar" height="' . $size . '" width="' . $size . '" />';
			} else {
				$imgsrc = wp_get_attachment_image_src($avatar_id, 'thumbnail');
				return '<img alt="avatar" src="' . $imgsrc[0] . '" class="avatar" height="' . $size . '" width="' . $size . '" />';
			}
		} 
	}
	return $avatar;
}
add_filter('get_avatar', 'ipin_local_avatar', 10, 5);


//Delete avatar
function ipin_delete_avatar() {
	$nonce = $_POST['nonce'];

	if (!wp_verify_nonce($nonce, 'ajax-nonce'))
		die();

	$user_avatar = get_user_meta($_POST['id'], 'ipin_user_avatar', true);
	wp_delete_attachment($user_avatar);
	update_user_meta($_POST['id'], 'ipin_user_avatar', '');
	exit;
}
add_action('wp_ajax_ipin-delete-avatar', 'ipin_delete_avatar');


//**User Control Panel**//

//Add Board/Edit Board
function ipin_add_board() {
	$nonce = $_POST['nonce'];

	if (!wp_verify_nonce($nonce, 'ajax-nonce'))
		die();

	global $wpdb, $user_ID;
	$mode = $_POST['mode'];
	$term_id = $_POST['term_id'];
	$board_parent_id = get_user_meta($user_ID, '_Board Parent ID', true);
	$board_title  = sanitize_text_field($_POST['board_title']);
	$category_id  = $_POST['category_id'];
	
	if ($category_id == '-1')
		$category_id = '1';

	if ($mode == 'add') {
		$board_children = get_term_children($board_parent_id, 'board');
		$found = '0';

		foreach ($board_children as $board_child) {
			$board_child_term = get_term_by('id', $board_child, 'board');
			if (stripslashes(htmlspecialchars($board_title, ENT_NOQUOTES, 'UTF-8')) == $board_child_term->name) {
				$found = '1';
				break;
			}
		}
		
		if ($found == '0') {
			$slug = wp_unique_term_slug($board_title . '__ipinboard', 'board'); //append __ipinboard to solve slug conflict with category and 0 in title
			
			$new_board_id = wp_insert_term (
				$board_title,
				'board',
				array(
					'description' => $category_id,
					'parent' => $board_parent_id,
					'slug' => $slug
				)
			);
			echo get_term_link($new_board_id['term_id'], 'board');
		} else {
			echo 'error';
		}

		//add new board to followers who fully follow user
		$usermeta_followers_id_allboards = get_user_meta($user_ID, '_Followers User ID All Boards');
		$followers_id_allboards = $usermeta_followers_id_allboards[0];

		if (!empty($followers_id_allboards)) {
			foreach ($followers_id_allboards as $followers_id_allboard) {
				$usermeta_following_board_id = get_user_meta($followers_id_allboard, '_Following Board ID');
				$following_board_id = $usermeta_following_board_id[0];
				array_unshift($following_board_id, $new_board_id['term_id']);
				update_user_meta($followers_id_allboard, '_Following Board ID', $following_board_id);
			}
		}
	} else if ($mode == 'edit') {
		$board_info = get_term_by('id', $term_id, 'board', ARRAY_A);
		
		if (stripslashes(htmlspecialchars($board_title, ENT_NOQUOTES, 'UTF-8')) == $board_info['name']) {
			wp_update_term(
				$term_id,
				'board',
				array(
					'description' => $category_id
				)
			);
			echo get_term_link(intval($term_id), 'board');
		} else {
			$board_children = get_term_children($board_info['parent'], 'board');
			$found = '0';

			foreach ($board_children as $board_child) {
				$board_child_term = get_term_by('id', $board_child, 'board');
				if (stripslashes(htmlspecialchars($board_title, ENT_NOQUOTES, 'UTF-8')) == $board_child_term->name) {
					$found = '1';
					break;
				}
			}

			if ($found == '0') {
				$slug = wp_unique_term_slug($board_title . '__ipinboard', 'board'); //append __ipinboard to solve slug conflict with category and 0 in title
				wp_update_term(
					$term_id,
					'board',
					array(
						'name' => $board_title,
						'slug' => $slug,
						'description' => $category_id
					)
				);
				echo get_term_link(intval($term_id), 'board');
			} else {
				echo 'error';				
			}
		}

		//change the category of all posts in this board only if category is changed in the form
		$original_board_cat_id = get_term_by('id', $board_info['term_id'], 'board');
		if ($category_id != $original_board_cat_id) {		
			$posts = $wpdb->get_results(
				$wpdb->prepare("SELECT object_id FROM $wpdb->term_relationships
							WHERE term_taxonomy_id = %d
							"
							,intval($board_info['term_taxonomy_id']))
			);
			
			if ($posts) {
				foreach ($posts as $post) {
					wp_set_object_terms($post->object_id, array(intval($category_id)), 'category');
				}
			}
		}
	}
	exit;
}
add_action('wp_ajax_ipin-add-board', 'ipin_add_board');


//Delete board
function ipin_delete_board() {
	$nonce = $_POST['nonce'];

	if (!wp_verify_nonce($nonce, 'ajax-nonce'))
		die();

	global $wpdb;

	$board_id = $_POST['board_id'];
	$board_info = get_term_by('id', $board_id, 'board');

	//user_id is name of parent board
	$board_parent_info = get_term_by('id', $board_info->parent, 'board');
	$user_id = $board_parent_info->name;

	//get all posts in this board
	$posts = $wpdb->get_results(
		$wpdb->prepare("SELECT object_id FROM $wpdb->term_relationships
					WHERE term_taxonomy_id = %d
					"
					,intval($board_info->term_taxonomy_id))
	);

	if ($posts) {
		$post_ids = array();

		foreach ($posts as $post) {
			array_push($post_ids, $post->object_id);
		}

		$post_ids = implode(',', $post_ids);

		//set status to prune
		$wpdb->query("UPDATE $wpdb->posts
					SET post_status = 'ipin_prune'
					WHERE ID IN ($post_ids)
		");
	}

	//delete board from followers usermeta
	$followers = get_user_meta($user_id, '_Followers User ID');

	if(!empty($followers[0])) {
		foreach ($followers[0] as $follower) {
			$usermeta_following_board_id = get_user_meta($follower, '_Following Board ID');
			$following_board_id = $usermeta_following_board_id[0];

			unset($following_board_id[array_search($board_info->term_id, $following_board_id)]);
			$following_board_id = array_values($following_board_id);
			update_user_meta($follower, '_Following Board ID', $following_board_id);
		}
	}

	wp_delete_term($board_info->term_id, 'board');

	echo get_author_posts_url($user_id);
	exit;
}
add_action('wp_ajax_ipin-delete-board', 'ipin_delete_board');


//Add pin
function ipin_upload_pin(){
    check_ajax_referer('upload_pin');

	require_once(ABSPATH . 'wp-admin/includes/image.php');
	require_once(ABSPATH . 'wp-admin/includes/file.php');
	require_once(ABSPATH . 'wp-admin/includes/media.php');

	if ($_POST['mode'] == 'computer') {
		if ($_FILES) {
			foreach ($_FILES as $file => $array) {							
				$imageTypes = array (
					1, //IMAGETYPE_GIF
					2, //IMAGETYPE_JPEG
					3 //IMAGETYPE_PNG
				);

				$imageinfo = getimagesize($_FILES[$file]['tmp_name']);
				$width = @$imageinfo[0];
				$height = @$imageinfo [1];
				$type = @$imageinfo [2];
				$bits = @$imageinfo ['bits'];
				$mime = @$imageinfo ['mime'];

				if (!in_array($type, $imageTypes)) {
					@unlink($_FILES[$file]['tmp_name']);
					echo 'error';
					die();
				}

				if ($width <= 1 && $height <= 1) {
					@unlink($_FILES[$file]['tmp_name']);
					echo 'error';
					die();
				}

				if($mime != 'image/gif' && $mime != 'image/jpeg' && $mime != 'image/png') {
					@unlink($_FILES[$file]['tmp_name']);
					echo 'error';
					die();
				}

				$filename = time() . substr(str_shuffle("abcde12345"), 0, 5);

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
				$_FILES[$file]['name'] = $filename . $ext;

				$attach_id = media_handle_upload($file, $post_id);

				if (is_wp_error($attach_id)) {
					@unlink($_FILES[$file]['tmp_name']);
					echo 'error';
					die();
				}
			}   
		}
		
		$return = array();

		$thumbnail = wp_get_attachment_image_src($attach_id, 'medium');
		$return['thumbnail'] = $thumbnail[0];
		$return['id'] = $attach_id;
		echo json_encode($return);
	}

	if ($_POST['mode'] == 'web') {
		$url = esc_url_raw($_POST['pin_upload_web']);
		
		if (function_exists("curl_init")) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$image = curl_exec($ch);
			curl_close($ch);
		} elseif (ini_get("allow_url_fopen")) {
			$image = file_get_contents($url, false, $context);
		}

		if (!$image) {
			echo 'error';
			die();
		}

		$filename = time() . substr(str_shuffle("abcde12345"), 0, 5);
		$file_array['tmp_name'] = WP_CONTENT_DIR . "/" . $filename . '.tmp';
		$filetmp = file_put_contents($file_array['tmp_name'], $image);
		
		if (!$filetmp) {
			@unlink($file_array['tmp_name']);
			echo 'error';
			die();
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
			echo 'error';
			die();
		}

		if ($width <= 1 && $height <= 1) {
			@unlink($file_array['tmp_name']);
			echo 'error';
			die();
		}

		if($mime != 'image/gif' && $mime != 'image/jpeg' && $mime != 'image/png') {
			@unlink($file_array['tmp_name']);
			echo 'error';
			die();
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
			echo 'error';
			die();
		}

		$return = array();
		$thumbnail = wp_get_attachment_image_src($attach_id, 'medium');
		$return['thumbnail'] = $thumbnail[0];
		$return['id'] = $attach_id;
		echo json_encode($return);
	}
	exit;
}
add_action('wp_ajax_ipin-upload-pin', 'ipin_upload_pin');

//Remove %20 from filenames
function ipin_clean_filename($filename, $filename_raw) {
	$filename = str_replace('%20', '-', $filename);
	return $filename;
}
add_filter('sanitize_file_name', 'ipin_clean_filename', 1, 2);

//Add pin as a wp post
function ipin_postdata() {
	$nonce = $_POST['nonce'];

	if (!wp_verify_nonce($nonce, 'ajax-nonce'))
		die();

	global $user_ID;

	//get board info
	$board_add_new = sanitize_text_field($_POST['postdata_board_add_new']);
	$board_add_new_category = $_POST['postdata_board_add_new_category'];
	$board_parent_id = get_user_meta($user_ID, '_Board Parent ID', true);
	if ($board_add_new !== '') {
		$board_children = get_term_children($board_parent_id, 'board');
		$found = '0';

		foreach ($board_children as $board_child) {
			$board_child_term = get_term_by('id', $board_child, 'board');
			if (stripslashes(htmlspecialchars($board_add_new, ENT_NOQUOTES, 'UTF-8')) == $board_child_term->name) {
				$found = '1';
				$found_board_id = $board_child_term->term_id;
				break;
			}
		}

		if ($found == '0') {
			$slug = wp_unique_term_slug($board_add_new . '__ipinboard', 'board'); //append __ipinboard to solve slug conflict with category and 0 in title
			if ($board_add_new_category == '-1')
				$board_add_new_category = '1';

			$new_board_id = wp_insert_term (
				$board_add_new,
				'board',
				array(
					'description' => $board_add_new_category,
					'parent' => $board_parent_id,
					'slug' => $slug
				)
			);
			
			$postdata_board = $new_board_id['term_id'];
		} else {
			$postdata_board = $found_board_id;
		}
	} else {
		$postdata_board = $_POST['postdata_board'];		
	}

	//category ID is stored in the board description field
	$category_id = get_term_by('id', $postdata_board, 'board');

	$post_status = 'publish';
	
	if (!current_user_can('publish_posts')) {
		$post_status = 'pending';
	}

	$post_array = array(
	  'post_title'    => sanitize_text_field($_POST['postdata_title']),
	  'post_status'   => $post_status,
	  'post_category' => array($category_id->description)
	);

	$post_id = wp_insert_post($post_array);
		
	wp_set_post_terms($post_id, array($postdata_board), 'board');

	//update postmeta for new post
	if ($_POST['postdata_photo_source'] != '') {
		add_post_meta($post_id, '_Photo Source', esc_url($_POST['postdata_photo_source']));
		add_post_meta($post_id, '_Photo Source Domain', parse_url($_POST['postdata_photo_source'], PHP_URL_HOST));
	}

	$attachment_id = $_POST['postdata_attachment_id'];
	add_post_meta($post_id, '_thumbnail_id', $attachment_id);

	global $wpdb;
	$wpdb->query(
		"
		UPDATE $wpdb->posts 
		SET post_parent = $post_id
		WHERE ID = $attachment_id
		AND post_type = 'attachment'
		"
	);
	
	//add new board to followers who fully follow user
	if ($new_board_id && !is_wp_error($new_board_id)) {
		$usermeta_followers_id_allboards = get_user_meta($user_ID, '_Followers User ID All Boards');
		$followers_id_allboards = $usermeta_followers_id_allboards[0];
		
		if (!empty($followers_id_allboards)) {
			foreach ($followers_id_allboards as $followers_id_allboard) {
				$usermeta_following_board_id = get_user_meta($followers_id_allboard, '_Following Board ID');
				$following_board_id = $usermeta_following_board_id[0];
				array_unshift($following_board_id, $new_board_id['term_id']);
				update_user_meta($followers_id_allboard, '_Following Board ID', $following_board_id);
			}
		}
	}

	echo get_permalink($post_id);
	exit;
}
add_action('wp_ajax_ipin-postdata', 'ipin_postdata');


//Edit pin
function ipin_edit() {
	$nonce = $_POST['nonce'];

	if (!wp_verify_nonce($nonce, 'ajax-nonce'))
		die();

	$postinfo = get_post(intval($_POST['postdata_pid']), ARRAY_A);
	$user_id = $postinfo['post_author'];
		
	//Get board info
	$board_add_new = sanitize_text_field($_POST['postdata_board_add_new']);
	$board_add_new_category = $_POST['postdata_board_add_new_category'];
	$board_parent_id = get_user_meta($user_id, '_Board Parent ID', true);
	if ($board_add_new !== '') {
		$board_children = get_term_children($board_parent_id, 'board');
		$found = '0';

		foreach ($board_children as $board_child) {
			$board_child_term = get_term_by('id', $board_child, 'board');
			if (stripslashes(htmlspecialchars($board_add_new, ENT_NOQUOTES, 'UTF-8')) == $board_child_term->name) {
				$found = '1';
				$found_board_id = $board_child_term->term_id;
				break;
			}
		}

		if ($found == '0') {
			$slug = wp_unique_term_slug($board_add_new . '__ipinboard', 'board'); //append __ipinboard to solve slug conflict with category and 0 in title
			if ($board_add_new_category == '-1')
				$board_add_new_category = '1';

			$new_board_id = wp_insert_term (
				$board_add_new,
				'board',
				array(
					'description' => $board_add_new_category,
					'parent' => $board_parent_id,
					'slug' => $slug
				)
			);
			
			$postdata_board = $new_board_id['term_id'];
		} else {
			$postdata_board = $found_board_id;
		}
	} else {
		$postdata_board = $_POST['postdata_board'];		
	}

	//category ID is stored in the board description field
	$category_id = get_term_by( 'id', $postdata_board, 'board');

	$post_id = intval($_POST['postdata_pid']);
	$edit_post = array();
	$edit_post['ID'] = $post_id;
	$edit_post['post_title'] = sanitize_text_field($_POST['postdata_title']);
	$edit_post['post_category'] = array($category_id->description);
	$edit_post['post_name'] = '';

	wp_update_post($edit_post);
	
	wp_set_post_terms($post_id, array($postdata_board), 'board');
	
	//update postmeta for new post
	if ($_POST['postdata_source'] != '' && $_POST['postdata_source'] != 'Source...') {
		update_post_meta($post_id, '_Photo Source', esc_url($_POST['postdata_source']));
		update_post_meta($post_id, '_Photo Source Domain', parse_url(esc_url($_POST['postdata_source']), PHP_URL_HOST));
	} else {
		delete_post_meta($post_id, '_Photo Source');
		delete_post_meta($post_id, '_Photo Source Domain');
	}
	
	//add new board to followers who fully follow user
	if ($new_board_id && !is_wp_error($new_board_id)) {
		$usermeta_followers_id_allboards = get_user_meta($user_id, '_Followers User ID All Boards');
		$followers_id_allboards = $usermeta_followers_id_allboards[0];
		
		if (!empty($followers_id_allboards)) {
			foreach ($followers_id_allboards as $followers_id_allboard) {
				$usermeta_following_board_id = get_user_meta($followers_id_allboard, '_Following Board ID');
				$following_board_id = $usermeta_following_board_id[0];
				array_unshift($following_board_id, $new_board_id['term_id']);
				update_user_meta($followers_id_allboard, '_Following Board ID', $following_board_id);
			}
		}
	}
	
	echo get_permalink($post_id);
	exit;
}
add_action('wp_ajax_ipin-pin-edit', 'ipin_edit');


//Delete pin
function ipin_delete_pin() {
	$nonce = $_POST['nonce'];

	if (!wp_verify_nonce($nonce, 'ajax-nonce'))
		die();
		
	global $wpdb;
	$post_id = $_POST['pin_id'];
	$post_author = intval($_POST['pin_author']);
	
	//set status to prune
	$wpdb->query("UPDATE $wpdb->posts
				SET post_status = 'ipin_prune'
				WHERE ID = $post_id
	");

	echo get_author_posts_url($post_author) . '?view=pins';
	exit;
}
add_action('wp_ajax_ipin-delete-pin', 'ipin_delete_pin');


//Setup theme for first time
function ipin_setup() {
	$ipin_version = get_option('ipin_version');
	if (!$ipin_version) {
		//setup pages
		$page= array(
			'post_title' => __('Boards Settings', 'ipin'),
			'post_name' => 'boards-settings',
			'post_author' => 1,
			'post_status' => 'publish',
			'post_type' => 'page',
			'comment_status' => 'closed',
			'ping_status' => 'closed'
		);
		$pageid = wp_insert_post($page);
		add_post_meta($pageid, '_wp_page_template', 'page_cp_boards.php');
		
		$page = array(
			'post_title' => __('Login', 'ipin'),
			'post_name' => 'login',
			'post_author' => 1,
			'post_status' => 'publish',
			'post_type' => 'page',
			'comment_status' => 'closed',
			'ping_status' => 'closed'
		);
		$pageid = wp_insert_post($page);
		add_post_meta($pageid, '_wp_page_template', 'page_cp_login.php');
		
		$page = array(
			'post_title' => __('Lost Your Password?', 'ipin'),
			'post_name' => 'login-lpw',
			'post_author' => 1,
			'post_status' => 'publish',
			'post_type' => 'page',
			'comment_status' => 'closed',
			'ping_status' => 'closed'
		);
		$pageid = wp_insert_post($page);
		add_post_meta($pageid, '_wp_page_template', 'page_cp_login_lpw.php');
	
		$page = array(
			'post_title' => __('Story Settings', 'ipin'),
			'post_name' => 'pin-settings',
			'post_author' => 1,
			'post_status' => 'publish',
			'post_type' => 'page',
			'comment_status' => 'closed',
			'ping_status' => 'closed'
		);
		$pageid = wp_insert_post($page);
		add_post_meta($pageid, '_wp_page_template', 'page_cp_pins.php');
	
		$page = array(
			'post_title' => __('Register', 'ipin'),
			'post_name' => 'register',
			'post_author' => 1,
			'post_status' => 'publish',
			'post_type' => 'page',
			'comment_status' => 'closed',
			'ping_status' => 'closed'
		);
		$pageid = wp_insert_post($page);
		add_post_meta($pageid, '_wp_page_template', 'page_cp_register.php');
		
		$page = array(
			'post_title' => __('Settings', 'ipin'),
			'post_name' => 'settings',
			'post_author' => 1,
			'post_status' => 'publish',
			'post_type' => 'page',
			'comment_status' => 'closed',
			'ping_status' => 'closed'
		);
		$pageid = wp_insert_post($page);
		add_post_meta($pageid, '_wp_page_template', 'page_cp_settings.php');
		
		$page = array(
			'post_title' => __('Everything', 'ipin'),
			'post_name' => 'everything',
			'post_author' => 1,
			'post_status' => 'publish',
			'post_type' => 'page',
			'comment_status' => 'closed',
			'ping_status' => 'closed'
		);
		$pageid = wp_insert_post($page);
		add_post_meta($pageid, '_wp_page_template', 'page_everything.php');
		
		$page = array(
			'post_title' => __('Following', 'ipin'),
			'post_name' => 'following',
			'post_author' => 1,
			'post_status' => 'publish',
			'post_type' => 'page',
			'comment_status' => 'closed',
			'ping_status' => 'closed'
		);
		$pageid = wp_insert_post($page);
		add_post_meta($pageid, '_wp_page_template', 'page_following.php');
	
		$page = array(
			'post_title' => __('Popular', 'ipin'),
			'post_name' => 'popular',
			'post_author' => 1,
			'post_status' => 'publish',
			'post_type' => 'page',
			'comment_status' => 'closed',
			'ping_status' => 'closed'
		);
		$pageid = wp_insert_post($page);
		add_post_meta($pageid, '_wp_page_template', 'page_popular.php');
		
		$page = array(
			'post_title' => __('Source', 'ipin'),
			'post_name' => 'source',
			'post_author' => 1,
			'post_status' => 'publish',
			'post_type' => 'page',
			'comment_status' => 'closed',
			'ping_status' => 'closed'
		);
		$pageid = wp_insert_post($page);
		add_post_meta($pageid, '_wp_page_template', 'page_source.php');

		//setup top menu
		$menuname = 'Top Menu';
		$menulocation = 'top_nav';
		$menu_exists = wp_get_nav_menu_object($menuname);

		if( !$menu_exists){
			$menu_id = wp_create_nav_menu($menuname);

			wp_update_nav_menu_item($menu_id, 0, array(
				'menu-item-title' =>  __('Popular', 'ipin'),
				'menu-item-url' => home_url('/popular/'), 
				'menu-item-status' => 'publish'));
		
			wp_update_nav_menu_item($menu_id, 0, array(
				'menu-item-title' =>  __('Everything', 'ipin'),
				'menu-item-url' => home_url('/everything/'), 
				'menu-item-status' => 'publish'));
		
			wp_update_nav_menu_item($menu_id, 0, array(
				'menu-item-title' =>  __('Categories', 'ipin'),
				'menu-item-url' => '#', 
				'menu-item-status' => 'publish'));

			if(!has_nav_menu($bpmenulocation)){
				$locations = get_theme_mod('nav_menu_locations');
				$locations[$menulocation] = $menu_id;
				set_theme_mod('nav_menu_locations', $locations);
			}
		}
		
		//remove default sidebar widgets
		update_option('sidebars_widgets', array());

		//setup admin account
		$board_id = wp_insert_term ('1', 'board');
		update_user_meta(1, '_Board Parent ID', $board_id['term_id']);
		update_user_meta(1, 'ipin_user_notify_likes', '1');
		update_user_meta(1, 'ipin_user_notify_repins', '1');
		update_user_meta(1, 'ipin_user_notify_follows', '1');
		update_user_meta(1, 'ipin_user_notify_comments', '1');
	
		update_option('ipin_version', '1.0');

	}
}
add_action('admin_init', 'ipin_setup');

do_action('endlessscroll_plugin_init');


/* Endless Scroll hooks
------------------------------------------------------------------------------------ */

function endless_validate_username($valid, $username ) {
    if (preg_match("/\\s/", $username)) {
            // there are spaces
            $valid = false;
    }

    return $valid;
}
add_filter('validate_username' , 'endless_validate_username', 10, 2);

function home_categories( $query ) {
    if ( $query->is_home() && $query->is_main_query() ) {
        $query->set( 'cat', '18,19,34,108' );
    }
}
add_action( 'pre_get_posts', 'home_categories' ); 

?> 
