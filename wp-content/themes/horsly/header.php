<!DOCTYPE html>
<html <?php language_attributes(); ?> prefix="og: http://ogp.me/ns#">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title><?php wp_title( '|', true, 'right' );	bloginfo( 'name' );	$site_description = get_bloginfo( 'description', 'display' ); if ($site_description && (is_home() || is_front_page())) echo " | $site_description"; ?></title>

	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	<link href="<?php echo get_template_directory_uri(); ?>/css/bootstrap.css" rel="stylesheet">
    <link href="<?php echo get_template_directory_uri(); ?>/css/font-awesome.css" rel="stylesheet">
	<link href="<?php echo get_template_directory_uri(); ?>/style.css" rel="stylesheet">

	<!--[if lt IE 9]>
		<script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	
	<!--[if IE 7]>
	  <link href="<?php echo get_template_directory_uri(); ?>/css/font-awesome-ie7.css" rel="stylesheet">
	<![endif]-->

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<noscript>
	<div class="alert alert-error text-align-center">
		<h3>You need to enable Javascript.</h3>
	</div>
	</noscript>
	
	<div id="topmenu" class="navbar navbar-fixed-top">
		<div class="navbar-inner top-nav">
            <div class="frow">

                <div class="center-span">
                    <?php $logo = of_get_option('logo'); ?>
                    <a class="top-brand<?php if ($logo != '') { echo ' logo'; } ?>" href="<?php echo esc_url( home_url( '/' ) ); ?>">
                        <?php if ($logo != '') { ?>
                        <img src="<?php echo $logo ?>" alt="logo" />
                        <?php } else {
                        bloginfo('name');
                    }
                        ?>
                    </a>
                </div>

                <div class="left-span">
                        <form class="navbar-search" method="get" id="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                                <input type="text" class="search-query" placeholder="<?php _e('Find a story..', 'ipin'); ?>" name="s" id="s" value="<?php the_search_query(); ?>">
                        </form>
                </div>

                <div class="right-span right">

                    <div id="topmenu-social-group">
                        <?php if ('' != $facebook_icon_url = of_get_option('facebook_icon_url')) { ?>
                        <a href="<?php echo $facebook_icon_url; ?>" title="<?php _e('Find us on Facebook', 'ipin'); ?>" class="topmenu-social"><i class="fa fa-facebook"></i></a>
                        <?php } ?>

                        <?php if ('' != $twitter_icon_url = of_get_option('twitter_icon_url')) { ?>
                        <a href="<?php echo $twitter_icon_url; ?>" title="<?php _e('Follow us on Twitter', 'ipin'); ?>" class="topmenu-social"><i class="fa fa-twitter"></i></a>
                        <?php } ?>

                        <a href="<?php bloginfo('rss2_url'); ?>" title="<?php _e('Subscribe to our RSS Feed', 'ipin'); ?>" class="topmenu-social"><i class="fa fa-rss"></i></a>
                    </div>

                    <ul id="menu-top-right" class="nav" style="padding-top: 8px !important;">
                        <?php if (is_user_logged_in()) { global $user_ID, $user_login; ?>
	
	                
                        <?php if (current_user_can('edit_posts')) { ?>
                            <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" data-target="#" href="#"><?php _e('Share', 'ipin'); ?> <i class="fa fa-caret-down"></i></a>
                                <ul class="dropdown-menu dropdown-menu-add">
                                    <li><a href="<?php echo home_url('/story-settings/'); ?>"><?php _e('Story', 'ipin'); ?></a></li>
                                    <li><a href="<?php echo home_url('/boards-settings/'); ?>"><?php _e('Board', 'ipin'); ?></a></li>
                                </ul>
                            </li>
                            <?php } ?>

                        <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" data-target="#" href="#"><?php if (strlen($user_login) > 12) { echo substr($user_login,0, 12) . '..'; } else { echo $user_login; } ?> <i class="icon-caret-down"></i></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo get_author_posts_url($user_ID); ?>"><?php _e('Boards', 'ipin'); ?></a></li>
                                <li><a href="<?php echo get_author_posts_url($user_ID); ?>?view=stories"><?php _e('Stories', 'ipin'); ?></a></li>
                                <li><a href="<?php echo get_author_posts_url($user_ID); ?>?view=likes"><?php _e('Likes', 'ipin'); ?></a></li>
                                <li><a href="<?php echo home_url('/settings/'); ?>"><?php _e('Settings', 'ipin'); ?></a></li>
                                <?php if (current_user_can('administrator') || current_user_can('editor')) { ?>
                                <li><a href="<?php echo home_url('/wp-admin/'); ?>"><?php _e('WP Admin', 'ipin'); ?></a></li>
                                <?php } ?>
                                <li><a href="<?php echo wp_logout_url(wp_login_url()); ?>"><?php _e('Logout', 'ipin'); ?></a></li>
                            </ul>
                        </li>
                        <?php } else { ?>
                       <!-- <li><a href="<?php echo home_url('/register/'); ?>"><?php _e('Register', 'ipin'); ?></a></li>

                        <li><a href="<?php echo wp_login_url($_SERVER['REQUEST_URI']); ?>"><?php _e('Login', 'ipin'); ?></a></li>-->
<li><?php echo jfb_output_facebook_btn(); ?></li>
                        <?php  } ?>


                    </ul>

                </div>

            </div>

		</div>
		<div class="navbar-inner bottom-nav">
			<div class="container center">
				<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
					<i class="icon-bar"></i>
					<i class="icon-bar"></i>
					<i class="icon-bar"></i>
				</a>


				<nav id="nav-main" class="nav-collapse" role="navigation">
					
					<?php 
					if (has_nav_menu('top_nav')) {
						$topmenu = wp_nav_menu(array('theme_location' => 'top_nav', 'menu_class' => 'nav', 'echo' => false));
						if (!is_user_logged_in()) {
							echo $topmenu;
						} else {
							$following_menu = '<li class="menu-following"><a href="' . home_url('/') . 'following/">' . __('Following', 'ipin') . '</a></li>';
							$pos = stripos($topmenu, '<li');
							echo substr($topmenu, 0, $pos) . $following_menu . substr($topmenu, $pos);
						}
					} else {
						echo '<ul id="menu-top" class="nav">';
						wp_list_pages('title_li=&depth=0&sort_column=menu_order' );
						echo '</ul>';
					}
					?>
				</nav>
			</div>
		</div>
	</div>
	<?php if (of_get_option('header_ad') != '' && !is_page('pins-settings')) { ?>	
	<div id="header-ad" class="container-fluid">
		<div class="row-fluid">
			<div class="span12" style=""><?php eval('?>' . of_get_option('header_ad')); ?></div>
		</div>
	</div>
	<?php } ?>
