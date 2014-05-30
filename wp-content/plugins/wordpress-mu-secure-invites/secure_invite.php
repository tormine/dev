<?php
/*
Plugin Name: Secure Invites
Description: Stops public signup on your Wordpress MultiSite or BuddyPress site, but allows existing users to email and invite their friend to join your blog community. This plugin is based on a plugin by kt (Gord), from http://www.ikazoku.com.
Version: 1.2.5
Author: Chris Taylor
Author URI: http://www.stillbreathing.co.uk
Plugin URI: http://www.stillbreathing.co.uk/wordpress/wordpress-mu-secure-invites/
Date: 2013-05-29
*/

function secure_invite_version() {
	return "1.2.5";
}

// when the admin menu is built
add_action('admin_menu', 'secure_invites_add_admin');

// when the admin head is built
add_action('admin_head', 'secure_invites_add_admin_js');

// when the admin dashboard is built
add_action('wp_dashboard_setup', 'secure_invite_dashboard');

// add shortcodes
// [inviteform]
add_shortcode('inviteform', 'secure_invite_form_shortcode');
// [myinviter] or [myinviter id="123"]
add_shortcode('myinviter', 'secure_invite_myinviter_shortcode');
// [bestinviters]
add_shortcode('bestinviters', 'secure_invite_bestinviters_shortcode');

// when a user is registered
add_action('user_register', 'secure_invite_user_registered');

// add the template action
add_action('init', 'secure_invite_buddypress_theme_page');

// add the check secure page actions
add_action('init', 'secure_invite_check_secure_page', 1);

// add the activation action
register_activation_hook( __FILE__, 'secure_invite_activate' );

function secure_invite_activate() {
	// check the invites table exists
	secure_invite_check_table();
	// save the default settings
	$values = array();
	$values["secure_invite_preset"] = "2";
	secure_invite_save_settings($values);
}

// add dashboard widgets
function secure_invite_dashboard() {
	if(current_user_can("edit_users")) wp_add_dashboard_widget( 'secure_invite_admin_dashboard', __( 'Invitations reports', "secure_invite"  ), 'secure_invite_admin_dashboard' );
	wp_add_dashboard_widget( 'secure_invite_user_dashboard', __( 'Invite a friend', "secure_invite"  ), 'secure_invite_user_dashboard' );
}

// add the form to a post or page using a shortcode
function secure_invite_form_shortcode($atts, $content="") {
	return secure_invite_form($success='Thanks, your invitation has been sent', $error='Sorry, your invitation could not be sent. Perhaps this email address is already registered.', true);
}

// show the details of the inviter of the current user
function secure_invite_myinviter_shortcode($atts, $content="") {
	extract(shortcode_atts(array(
		'id' => null
	), $atts));
	if ($id == null) {
		global $current_user;
		$email = $current_user->user_email;
	} else {
		$user = get_userdata($id);
		$email = $user->user_email;
	}
	if ($email) {
		global $wpdb;
		$sql = $wpdb->prepare("select u.display_name from {$wpdb->users} u inner join " . secure_invite_prefix() . "invitations i on i.user_id = u.id where i.invited_email = %s;", $email);
		return $wpdb->get_var($sql);
	}
}

function secure_invite_bestinviters_shortcode($atts, $content="") {
	$best_inviters = secure_inviters_get_best_by_points();
	if ($best_inviters && count($best_inviters) > 0) {
		$o = "<ul>\n";
		foreach($best_inviters as $best_inviter) {
			$o .= "<li>" . $best_inviter->display_name . ": " . $best_inviter->secure_invite_points . "</li>";
		}
		$o .= "</ul>";
		return $o;
	}
	return "";
}

// get the link to the admin page
function secure_invite_page_link() {
	$wpmums = "wpmu-admin";
	if (version_compare(get_bloginfo('version'), "3") >= 0)	{
		$wpmums = "ms-admin";
		if ( !defined( 'WP_ALLOW_MULTISITE' ) || !WP_ALLOW_MULTISITE) {
			$wpmums = "options-general";
		}
	}
	return $wpmums;
}

// get the base prefix
function secure_invite_prefix() {
	global $wpdb;
	if ( !empty( $wpdb->base_prefix ) ) return $wpdb->base_prefix;
	return $wpdb->prefix;
}

// administrators admin dashboard widget
function secure_invite_admin_dashboard() {
	if (current_user_can("edit_users")) {
		secure_invite_by_month(6, "");
		echo '
		<div style="padding:5px"></div>
		';
		secure_invite_by_inviter(6, "");
		if (version_compare(get_bloginfo('version'), "3") >= 0)	{
		if ( defined( 'WP_ALLOW_MULTISITE' ) && WP_ALLOW_MULTISITE) {
			$link = "ms-admin.php?page=secure_invite_list";
		} else {
			$link = "options-general.php?page=secure_invite_list";
		}
	} else {
		$link = "wpmu-admin.php?page=secure_invite_list";
	}
		echo '
		<p><a href="'.$link.'">'.__("View full reports", "secure_invite").'</a></p>
		';
	}
}

// administrators user dashboard widget
function secure_invite_user_dashboard() {
	global $current_user;
	$secure_invite_show_admin_link = stripslashes( get_site_option("secure_invite_show_admin_link", "yes") );
	if ($secure_invite_show_admin_link == "") { $secure_invite_show_admin_link = "yes"; }
	// if the user can send invites
	if (secure_invite_user_can_invite( $current_user->ID ) && ($secure_invite_show_admin_link == "yes" || current_user_can("edit_users"))) {
		secure_invite_form();
	}
}

// show the right theme page
function secure_invite_buddypress_theme_page() {
	if (defined("BP_CORE_DB_VERSION") && strpos($_SERVER["REQUEST_URI"], "/send-secure-invite") !== false) {
	
		$send_result = 0;
		// if an email has been supplied
		if (@$_POST['invite-email'] != "") {
			if (is_email($_POST['invite-email'])) {
				if (secure_invite_send()) {
					$send_result = 1;
				} else {
					$send_result = 2;
				}
			} else {
				$send_result = 3;
			}
		}
		
		// if a redirect has been supplied
		if (@$_GET["return"] != "") {
			$result = "?send_result=" . $send_result;
			if (strpos($_GET["return"], "?") !== false) {
				$result = "&send_result=" . $send_result;
			}
			$url = secure_invite_remove_querystring_var($_GET["return"]);
			header("Location: " . $url . $result);
		}
		
		get_header();
		echo '
		<div id="content">
			<div class="padder">
			<h1>' . __("Invite a friend", "secure_invite") . '</h1>
			';
			// if an invite has been sent
			if ($send_result > 0) {
				// if the invite can be sent
				if ($send_result == 1) {
					// show the success message
					echo '<div id="message" class="updated"><p>' . __("Thanks, your invitation has been sent", "secure_invite") . '</p></div>';
					secure_invite_buddypress_form(true, false);
				} else if ($send_result == 2) {
					// show the error message
					echo '<div id="message" class="error"><p>' . __("Sorry, your invitation could not be sent. Perhaps this email address is already registered. Please try again.", "secure_invite") . '</p></div>';
					secure_invite_buddypress_form(true, true);
				} else if ($send_result == 3) {
					echo '<div id="message" class="error"><p>' . __("You must supply a valid email address. Please try again.", "secure_invite") . '</p></div>';
					secure_invite_buddypress_form(true, true);
				}
			}
			echo '
			</div><!-- .padder -->
		</div><!-- #content -->
		';
		locate_template( array( 'sidebar.php' ), true );
		get_footer();
		exit();
	}
}

// add actions for Buddypress
function secure_invite_add_buddypress_theme_actions() {
	$actionlist = stripslashes( get_site_option("secure_invite_buddypress_theme_actions") );
	if ($actionlist != "") {
		if (strpos($actionlist, ",") !== false) {
			$actions = explode(",", $actionlist);
			foreach($actions as $action) {
				add_action($action, 'secure_invite_buddypress_form');
			}
		} else {
			add_action($actionlist, 'secure_invite_buddypress_form');
		}
		add_action("wp_head", "secure_invite_buddypress_head_js");
	}
}
secure_invite_add_buddypress_theme_actions();

function secure_invite_buddypress_head_js() {
	echo '
	<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery("div.secure_invite_form_wrapper").hide();
		jQuery("a.secure_invite_toggler").click(function(){
			jQuery(jQuery(this).attr("href")).toggle("normal");
			return false;
		});
	});
	</script>
	';
}

// check secure pages
function secure_invite_check_secure_page() {
	if (!headers_sent()) session_start();
	// set the invite code
	if (isset($_POST["invite_code"])) {
		$_SESSION["invite_code"] = trim($_POST["invite_code"]);
		header("Location: " . $_SERVER['REQUEST_URI']);
		exit();
	}

	// when wp-signup.php or another restricted page is requested, and open signup is disabled
	if (secure_invite_is_restricted_page() && stripslashes( get_site_option("secure_invite_open_signup", "0") ) != "1") {
		global $current_user;

		// set the signup request as not valid
		$valid = false;

		// check the email address is a valid invitation, or a valid code has been given, or the user is logged in
		if (
			(isset($_GET["email"]) && secure_invites_is_valid_email($_GET["email"]))
			||
			(isset($_POST["user_email"]) && secure_invites_is_valid_email(trim(@$_POST["user_email"])))
			||
			(isset($_SESSION["invite_code"]) && secure_invites_is_valid_code(trim(@$_SESSION["invite_code"])))
			||
			((function_exists("is_user_logged_in") && is_user_logged_in()) || (isset($current_user) && $current_user->ID != ""))
			) {
			$valid = true;
			if (isset($_GET["email"])) {
				$_POST['user_email'] = $_GET["email"];
				$_POST['signup_email'] = $_GET["email"];
			}
		}
		
		// Originally from Giovanni Gonzalez <giova@cal.berkeley.edu>
		// Check the list of invitation codes defined in the Security Settings
		$invitation_code_message = "";
		$site_invite_codes = explode("\n",get_site_option('secure_invite_invitation_codes'));
		if (is_array($site_invite_codes) && count($site_invite_codes) > 0 && $site_invite_codes[0] != "") {
			$invitation_code_message = "</p>
			<p>" . __('Or enter an invitation code below:', 'secure_invite') . '</p>
			<form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
				<p><input type="text" name="invite_code" />
				<button type="submit">' . __("Register code", "secure_invite") . '</button></p>
			</form>
			<p>' . __("Contact the site administrator if you have any problems.", "secure_invite");
			for ( $i = 1; $i <= count($site_invite_codes); $i++ ) {
				if ( trim(@$_POST['invitecode']) == trim($site_invite_codes[$i - 1]) ) {
					echo "VALID: ".$_POST['invitecode']." = ".$site_invite_codes[$i - 1];
					$valid = true;
				}	
			}
		}

		// if the signup request is not valid
		if (!$valid) {
			// show the message
			$secure_invite_no_invite_message = stripslashes( get_site_option("secure_invite_no_invite_message") );
			if ($secure_invite_no_invite_message == "") { $secure_invite_no_invite_message = secure_invite_default_setting("secure_invite_no_invite_message"); }
			// stop processing
			$args["response"] = 403;
			$args["back_link"] = true;
			wp_die($secure_invite_no_invite_message . $invitation_code_message, __("Invitation required", "secure_invite"), $args);
			exit();
		}
	}
}

// when a user is registered
function secure_invite_user_registered($user_id) {

	// get the email of the new user
	$user = get_userdata($user_id);
	$email = $user->user_email;
	
	// save the invite code, if one is set
	if ($_SESSION["invite_code"] != ""){
		update_user_meta($user_id, "secure_invite_invitation_code", $_SESSION["invite_code"]);
	}
	
	// check if this is an invited email address
	$invited = secure_invites_is_valid_email($email);
	if ($invited) {
	
		// get the id of the level 1 inviter
		$inviterid_1 = secure_invite_get_inviter_id($email);
		// increase the level 1 inviter points by 5
		$points_1 = (int)secure_invite_get_user_meta($inviterid_1, "secure_invite_points");
		update_user_meta($inviterid_1, "secure_invite_points", ($points_1+5));
		// get the inviter 1 email
		$inviter_1 = get_userdata($inviterid_1);
		$email_1 = $inviter_1->user_email;
		
		// get the id of the level 2 inviter
		$inviterid_2 = secure_invite_get_inviter_id($email_1);
		
		// if they were invited
		if ($inviterid_2 != "") {
		
			// increase the level 2 inviter points by 2
			$points_2 = (int)secure_invite_get_user_meta($inviterid_2, "secure_invite_points");
			update_user_meta($inviterid_2, "secure_invite_points", ($points_2+2));
			// get the inviter 2 email
			$inviter_2 = get_userdata($inviterid_2);
			$email_2 = $inviter_2->user_email;
			
			// get the id of the level 3 inviter
			$inviterid_3 = secure_invite_get_inviter_id($email_2);
			
			// if they were invited
			if ($inviterid_3 != "") {
			
				// increase the level 3 inviter points by 1
				$points_3 = (int)secure_invite_get_user_meta($inviterid_3, "secure_invite_points");
				update_user_meta($inviterid_3, "secure_invite_points", ($points_3+1));
				
			}
		}
	}
	return $user_id;
}

// get the inviter user id of an email address
function secure_invite_get_inviter_id($email) {
	global $wpdb;
	$sql = $wpdb->prepare("select user_id from ".secure_invite_prefix()."invitations where invited_email = '%s';", $email);
	return $wpdb->get_var($sql);
}

// check if this is a restricted page
function secure_invite_is_restricted_page() {
	$uri = $_SERVER["REQUEST_URI"];
	$secure_invite_signup_page = stripslashes( get_site_option("secure_invite_signup_page") );
	if ( $secure_invite_signup_page == "" ) { $secure_invite_signup_page = secure_invite_default_setting("secure_invite_signup_page"); }
	if ( strpos( $secure_invite_signup_page, "," ) !== false ) {
		$pages = explode( ",", $secure_invite_signup_page );
		foreach( $pages as $page ) {
			if ( strpos( $uri, $page ) !== false ) {
				return true;
			}
		}
	} else {
		if ( strpos( $uri, $secure_invite_signup_page ) !== false ) {
			return true;
		}
	}
	return false;
}

// check a code is valid
function secure_invites_is_valid_code($code) {
	$valid = false;
	$site_invite_codes = explode("\n",get_site_option('secure_invite_invitation_codes'));
	if (is_array($site_invite_codes) && count($site_invite_codes) > 0 && $site_invite_codes[0] != "") {
		for ( $i = 1; $i <= count($site_invite_codes); $i++ ) {
			if ( trim(@$code) == trim($site_invite_codes[$i - 1]) ) {
				$valid = true;
			}	
		}
	}
	return $valid;
}

// check an email address has been invited
function secure_invites_is_valid_email($email) {
	if ($email && is_email($email))
	{
		$timelimit = stripslashes( get_site_option("secure_invite_signup_time_limit", "3") );
		if ($timelimit == "")
		{
			// default time limit of 3 days
			$timelimit = 3;
		}
		global $wpdb;
		$sql = $wpdb->prepare("select count(id) from ".secure_invite_prefix()."invitations where invited_email = '%s' and UNIX_TIMESTAMP(datestamp) > %d;", $email, (time()-($timelimit*60*60*24)));
		$invites = $wpdb->get_var($sql);
		
		if ($invites == "0")
		{
			return false;
		} else {
			return true;
		}
	} else {
		return false;
	}
}

// add the admin invitation button
function secure_invites_add_admin() {
	global $current_user;
	$secure_invite_show_admin_link = stripslashes( get_site_option("secure_invite_show_admin_link", "yes") );
	if ($secure_invite_show_admin_link == "") { $secure_invite_show_admin_link = "yes"; }
	// if the user can send invites
	if (secure_invite_user_can_invite( $current_user->ID ) && ($secure_invite_show_admin_link == "yes" || current_user_can("edit_users")))
	{
		add_submenu_page('index.php', __('Invite friends', "secure_invite"), __('Invite friends', "secure_invite"), 'read', 'secure_invite', 'secure_invite_admin');
	}
	if (version_compare(get_bloginfo('version'), "3") >= 0)	{
		if ( defined( 'WP_ALLOW_MULTISITE' ) && WP_ALLOW_MULTISITE) {
			add_submenu_page('ms-admin.php', __('Invites', "secure_invite"), __('Invites', "secure_invite"), 'edit_users', 'secure_invite_list', 'secure_invite_list');
		} else {
			add_submenu_page('options-general.php', __('Invites', "secure_invite"), __('Invites', "secure_invite"), 'edit_users', 'secure_invite_list', 'secure_invite_list');
		}
	} else {
		add_submenu_page('wpmu-admin.php', __('Invites', "secure_invite"), __('Invites', "secure_invite"), 'edit_users', 'secure_invite_list', 'secure_invite_list');
	}
}

function secure_invites_add_admin_js() {
	if ( isset( $_GET["page"] ) && $_GET["page"] == "secure_invite_list" && isset( $_GET["view"] ) && $_GET["view"] == "settings" ) {
		echo '
		<script type="text/javascript">
		jQuery(document).ready(function(){
			if (!jQuery("#secure_invite_preset_5").is(":checked")) { jQuery("#secure_invites_custom_settings_form").hide(); }
			jQuery("#secure_invite_preset_1,#secure_invite_preset_2,#secure_invite_preset_3,#secure_invite_preset_4,#secure_invite_preset_6").click(function(){
				if (jQuery("#secure_invites_custom_settings_form").is(":visible")) {
					jQuery("#secure_invites_custom_settings_form").slideUp("normal");
					jQuery("#secure_invite_save_preset").removeAttr("disabled");
				}
			});
			jQuery("#secure_invite_preset_5").click(function(){
				jQuery("#secure_invites_custom_settings_form").slideDown("normal");
				jQuery("#secure_invite_save_preset").attr("disabled", true);
			});
		});
		</script>
		';
	}
}

// add the list of invitations
function secure_invite_list() {
	if (@$_GET["view"] == "") {
	
		secure_invite_list_page();
	
	} else if (@$_GET["view"] == "settings") {
	
		secure_invite_settings();
	
	} else if (@$_GET["view"] == "users") {
	
		secure_invite_users();
	
	} else if (@$_GET["view"] == "bulk") {
	
		secure_invite_bulk_invite();
	
	} else if (@$_GET["view"] == "top") {
	
		echo '
		<div class="wrap">
		';
		secure_invite_wp_plugin_standard_header( "GBP", "Secure invites", "Chris Taylor", "chris@stillbreathing.co.uk", "http://wordpress.org/extend/plugins/wordpress-mu-secure-invites/" );
		echo '
		<h2>' . __("Secure invites admin", "secure_invite") . '
		<span style="float:right">
			<a href="' . secure_invite_page_link(). '.php?page=secure_invite_list&amp;view=settings" class="button">' . __("Settings", "secure_invite") . '</a>
			<a href="' . secure_invite_page_link() . '.php?page=secure_invite_list&amp;view=bulk" class="button">' . __("Bulk invite", "secure_invite") . '</a>
			<a href="' . secure_invite_page_link() . '.php?page=secure_invite_list&amp;view=users" class="button">' . __("Special users", "secure_invite") . '</a></span>
		</h2>
		';
	
		secure_invite_by_points(100);
		
		echo '
		</div>
		';
	
	} else if (@$_GET["view"] == "debug") {
	
		global $wpdb;
	
		$version = $wpdb->get_var("SELECT VERSION();");
	
		echo '
		<div class="wrap">
		<h2>Secure invite debug</h2>
		<p>MySQL version: ' . $version . '</p>
		<p>PHP version: ' . phpversion() . '</p>
		<p>Secure Invite version: ' . secure_invite_version() . '</p>
		<p>secure_invite_preset: ' . get_site_option("secure_invite_preset") . '</p>
		<p>secure_invite_days_after_joining: ' . get_site_option("secure_invite_days_after_joining") . '</p>
		<p>secure_invite_signup_page: ' . get_site_option("secure_invite_signup_page") . '</p>
		<p>secure_invite_registration_page: ' . get_site_option("secure_invite_registration_page") . '</p>
		<p>secure_invite_no_invite_message: ' . get_site_option("secure_invite_no_invite_message") . '</p>
		<p>secure_invite_signup_time_limit: ' . get_site_option("secure_invite_signup_time_limit") . '</p>
		<p>secure_invite_default_message: ' . get_site_option("secure_invite_default_message") . '</p>
		<p>secure_invite_open_signup: ' . get_site_option("secure_invite_open_signup") . '</p>
		<p>secure_invite_invite_limit: ' . get_site_option("secure_invite_invite_limit") . '</p>
		<p>secure_invite_show_admin_link: ' . get_site_option("secure_invite_show_admin_link") . '</p>
		<p>secure_invite_invitation_codes: ' . get_site_option("secure_invite_invitation_codes") . '</p>
		<p>
		<textarea cols="60" rows="12" style="width:100%">id,user_id,invited_email,datestamp';
		$sql = "select id, user_id, invited_email, datestamp from ".secure_invite_prefix()."invitations order by id;";
		$rows = $wpdb->get_results($sql);
		if ($rows) {
			foreach($rows as $row) {
				echo "\n" . $row->id . "," . $row->user_id . "," . $row->invited_email . "," . $row->datestamp; 
			}
		}
		echo '</textarea>
		</p>
		</div>
		';
	
	}
	
	secure_invite_wp_plugin_standard_footer( "GBP", "Secure invites", "Chris Taylor", "chris@stillbreathing.co.uk", "http://wordpress.org/extend/plugins/wordpress-mu-secure-invites/" );
}

// return a default setting
function secure_invite_default_setting($name) {

	if ($name == "secure_invite_days_after_joining") { return "30"; }
	
	if ($name == "secure_invite_signup_page") { return "wp-signup.php,wp-login.php?action=register,/register,wp-register.php"; }
	
	if ($name == "secure_invite_registration_page") { 
		// for BuddyPress, use http://domain.com/register/?email=[email]
		if ( defined("BP_CORE_DB_VERSION") ) {
			return trim(get_bloginfo("wpurl"), '/') . "/register/?email=";
		// for standard WordPress use http://domain.com/wp-login?action=register&email=[email]
		} else {
			return trim(get_bloginfo("wpurl"), '/') . "/wp-login.php?action=register&email=";
		}
	}
	
	if ($name == "secure_invite_no_invite_message") { return "Sorry, you must be invited to join hors.ly's community."; }

	if ($name == "secure_invite_signup_time_limit") { return 3; }
	
	if ($name == "secure_invite_invite_limit") { return -1; }
	
	if ($name == "secure_invite_show_admin_link") { return "yes"; }

	if ($name == "secure_invite_default_message") { return "----------------------------------------------------------------------------------------
	
You have been invited to open a free account at hors.ly. To open and register, please visit

[signuplink]

Regards,

[name]

This invitation will work for the next [timeout] days. After that your invitation will expire and you will have to be invited again.

If clicking the links in this message does not work, copy and paste them into the address bar of your browser."; }

	if ($name == "secure_invite_buddypress_theme_actions") { return "bp_members_directory_member_types"; }
	
	if ($name == "secure_invite_invitation_codes") { return ""; }

}

// save the settings
function secure_invite_save_settings($values) {
	// if a preset has been chosen
	if ($values["secure_invite_preset"] != "" && $values["secure_invite_preset"] != "5")
	{
		// general settings
		$values["secure_invite_days_after_joining"] = secure_invite_default_setting("secure_invite_days_after_joining");
		$values["secure_invite_signup_page"] = secure_invite_default_setting("secure_invite_signup_page");
		$values["secure_invite_registration_page"] = secure_invite_default_setting("secure_invite_registration_page");
		$values["secure_invite_no_invite_message"] = secure_invite_default_setting("secure_invite_no_invite_message");
		$values["secure_invite_signup_time_limit"] = secure_invite_default_setting("secure_invite_signup_time_limit");
		$values["secure_invite_default_message"] = secure_invite_default_setting("secure_invite_default_message");
		$values["secure_invite_open_signup"] = "1";
		$values["secure_invite_invite_limit"] = secure_invite_default_setting("secure_invite_invite_limit");
		$values["secure_invite_show_admin_link"] = secure_invite_default_setting("secure_invite_show_admin_link");
		$values["secure_invite_invitation_codes"] = secure_invite_default_setting("secure_invite_invitation_codes");
		
		// preset 1
		if ($values["secure_invite_preset"] == "1")
		{
			$values["secure_invite_days_after_joining"] = 0;
			$values["secure_invite_invite_limit"] = -1;
		}
		// preset 2
		if ($values["secure_invite_preset"] == "2")
		{
			$values["secure_invite_days_after_joining"] = 0;
			$values["secure_invite_invite_limit"] = -1;
			$values["secure_invite_open_signup"] = "";
		}
		// preset 3
		if ($values["secure_invite_preset"] == "3")
		{
			$values["secure_invite_days_after_joining"] = 30;
			$values["secure_invite_invite_limit"] = -1;
			$values["secure_invite_open_signup"] = "";
		}
		// preset 4
		if ($values["secure_invite_preset"] == "4")
		{
			$values["secure_invite_days_after_joining"] = 30;
			$values["secure_invite_invite_limit"] = 10;
			$values["secure_invite_open_signup"] = "";
		}
		// preset 6
		if ($values["secure_invite_preset"] == "6")
		{
			$values["secure_invite_show_admin_link"] = "no";
			$values["secure_invite_open_signup"] = "";
		}
	}

	// save the settings
	update_site_option("secure_invite_preset", (int)$values["secure_invite_preset"]);
	update_site_option("secure_invite_days_after_joining", (int)$values["secure_invite_days_after_joining"]);
	update_site_option("secure_invite_signup_page", $values["secure_invite_signup_page"]);
	update_site_option("secure_invite_registration_page", $values["secure_invite_registration_page"]);
	update_site_option("secure_invite_no_invite_message", trim($values["secure_invite_no_invite_message"]));
	update_site_option("secure_invite_signup_time_limit", trim($values["secure_invite_signup_time_limit"]));
	update_site_option("secure_invite_default_message", trim($values["secure_invite_default_message"]));
	update_site_option("secure_invite_open_signup", trim($values["secure_invite_open_signup"]));
	update_site_option("secure_invite_invite_limit", trim($values["secure_invite_invite_limit"]));
	update_site_option("secure_invite_show_admin_link", trim($values["secure_invite_show_admin_link"]));
	update_site_option("secure_invite_invitation_codes", trim($values["secure_invite_invitation_codes"]));
	if (isset($values["secure_invite_buddypress_theme_actions"])) {
		$vals = implode(",", $values["secure_invite_buddypress_theme_actions"]);
		if (strpos($vals, "bp_nowhere") !== false){ $vals = "bp_nowhere"; }
		update_site_option("secure_invite_buddypress_theme_actions", $vals);
	}
}

// show the settings for secure invites
function secure_invite_settings() {
	// check the invites table exists
	secure_invite_check_table();

	if (@$_POST && is_array($_POST) && count($_POST) > 0)
	{

		secure_invite_save_settings($_POST);
		
		echo '<div id="message" class="updated fade"><p><strong>'.__('The settings have been updated', "secure_invite").'</strong></p></div>';
	}
	
	$secure_invite_preset = stripslashes( get_site_option("secure_invite_preset") );

	$secure_invite_days_after_joining = stripslashes( get_site_option("secure_invite_days_after_joining", "30") );
	if ($secure_invite_days_after_joining == "") { $secure_invite_days_after_joining = secure_invite_default_setting("secure_invite_days_after_joining"); }
	
	$secure_invite_open_signup = stripslashes( get_site_option("secure_invite_open_signup", "0") );
	$open_signup = "";
	if ($secure_invite_open_signup == "1") { $open_signup = ' selected="selected"'; }
	
	$secure_invite_signup_page = stripslashes( get_site_option("secure_invite_signup_page") );
	if ($secure_invite_signup_page == "") { $secure_invite_signup_page = secure_invite_default_setting("secure_invite_signup_page"); }
	
	$secure_invite_registration_page = stripslashes( get_site_option("secure_invite_registration_page") );
	if ($secure_invite_registration_page == "") { $secure_invite_registration_page = secure_invite_default_setting("secure_invite_registration_page"); }
	
	$secure_invite_no_invite_message = stripslashes( get_site_option("secure_invite_no_invite_message") );
	if ($secure_invite_no_invite_message == "") { $secure_invite_no_invite_message = secure_invite_default_setting("secure_invite_no_invite_message"); }
	
	$secure_invite_signup_time_limit = stripslashes( get_site_option("secure_invite_signup_time_limit", "3") );
	if ($secure_invite_signup_time_limit == "") { $secure_invite_signup_time_limit = secure_invite_default_setting("secure_invite_signup_time_limit"); }
	
	$secure_invite_invite_limit = stripslashes( get_site_option("secure_invite_invite_limit", "-1") );
	if ($secure_invite_invite_limit == "") { $secure_invite_invite_limit = secure_invite_default_setting("secure_invite_invite_limit"); }
	if ($secure_invite_invite_limit == "-1") { $secure_invite_invite_limit = ""; }
	
	$secure_invite_show_admin_link = stripslashes( get_site_option("secure_invite_show_admin_link", "yes") );
	if ($secure_invite_show_admin_link == "") { $secure_invite_show_admin_link = secure_invite_default_setting("secure_invite_show_admin_link"); }
	
	$secure_invite_default_message = stripslashes( get_site_option("secure_invite_default_message") );
	if ($secure_invite_default_message == "") { $secure_invite_default_message = secure_invite_default_setting("secure_invite_default_message"); }
	
	$secure_invite_buddypress_theme_actions = stripslashes( get_site_option("secure_invite_buddypress_theme_actions") );
	if ($secure_invite_buddypress_theme_actions == "") { $secure_invite_buddypress_theme_actions = secure_invite_default_setting("secure_invite_buddypress_theme_actions"); }
	$secure_invite_buddypress_theme_actions = $secure_invite_buddypress_theme_actions . ",";
	
	$secure_invite_invitation_codes = stripslashes( get_site_option("secure_invite_invitation_codes") );

	echo '<div class="wrap">
	<h2>' . __("Invitation settings", "secure_invite") . '	
	<span style="float:right">
		<a href="' . secure_invite_page_link() . '.php?page=secure_invite_list" class="button">' . __("Invitation list", "secure_invite") . '</a>
		<a href="' . secure_invite_page_link() . '.php?page=secure_invite_list&amp;view=bulk" class="button">' . __("Bulk invite", "secure_invite") . '</a>
		<a href="' . secure_invite_page_link() . '.php?page=secure_invite_list&amp;view=users" class="button">' . __("Special users", "secure_invite") . '</a></span>
	</h2>
	';
	do_action("secure_invite_user_can_invite");
	
	if (!secure_invite_registration_setting_ok()) {
		secure_invites_disallowed_registration_error();
	}
	
	echo '
	<h3>' . __("Settings presets", "secure_invite") . '</h3>
	<form action="' . secure_invite_page_link() . '.php?page=secure_invite_list&amp;view=settings" method="post">
	<ul>
		<li><input type="radio" name="secure_invite_preset" id="secure_invite_preset_1" value="1"';
		if ($secure_invite_preset == "1"){ echo ' checked="checked"'; }
		echo ' /> ' . __("Anyone can join with or without an invitation, and all users can invite as many people as they like", "secure_invites") . '</li>
		<li><input type="radio" name="secure_invite_preset" id="secure_invite_preset_2" value="2"';
		if ($secure_invite_preset == "2"){ echo ' checked="checked"'; }
		echo ' /> ' . __("Signup is just for invited people, and all users can invite as many people as they like", "secure_invites") . '</li>
		<li><input type="radio" name="secure_invite_preset" id="secure_invite_preset_3" value="3"';
		if ($secure_invite_preset == "3"){ echo ' checked="checked"'; }
		echo ' /> ' . __("Signup is just for invited people, and all users who have been registered for 30 days or more can invite as many people as they like", "secure_invites") . '</li>
		<li><input type="radio" name="secure_invite_preset" id="secure_invite_preset_4" value="4"';
		if ($secure_invite_preset == "4"){ echo ' checked="checked"'; }
		echo ' /> ' . __("Signup is just for invited people, and all users who have been registered for 30 days or more can invite up to 10 people", "secure_invites") . '</li>
		<li><input type="radio" name="secure_invite_preset" id="secure_invite_preset_6" value="6"';
		if ($secure_invite_preset == "6"){ echo ' checked="checked"'; }
		echo ' /> ' . __("Signup is just for invited people, and only administrators can invite people", "secure_invites") . '</li>
		<li><input type="radio" name="secure_invite_preset" id="secure_invite_preset_5" value="5"';
		if ($secure_invite_preset == "5"){ echo ' checked="checked"'; }
		echo ' /> ' . __("Use custom settings", "secure_invites") . '</li>
	</ul>
	<p><button type="submit" name="secure_invite_save_settings" id="secure_invite_save_preset" class="button-primary">' . __("Save this preset", "secure_invite") . '</button></p>
	</form>
	<div id="secure_invites_custom_settings_form">
	<h3>' . __("Custom settings", "secure_invite") . '</h3>
	<p>' . __("Use custom settings for secure invitations here.", "secure_invite") . '</p>
	<form action="' . secure_invite_page_link() . '.php?page=secure_invite_list&amp;view=settings" method="post">
	<fieldset>
	
	<p><label for="secure_invite_show_admin_link" style="float:left;width:30%;">' . __("Show admin link", "secure_invite") . '</label>
	<select name="secure_invite_show_admin_link" id="secure_invite_show_admin_link">
	<option value="yes"';
	if ($secure_invite_show_admin_link == "yes"){ echo ' selected="selected"'; }
	echo '>'.__("Yes", "secure_invite").'</option>
	<option value="no"';
	if ($secure_invite_show_admin_link == "no"){ echo ' selected="selected"'; }
	echo '>'.__("No", "secure_invite").'</option>
	</select> <span class="description">' . __("Show the invite link in the admin area for normal users", "secure_invite") . '</span></p>
	
	<h3 style="padding-top:2em">' . __( "User settings", "secure_invite" ) . '</h3>
	
	<p><label for="secure_invite_days_after_joining" style="float:left;width:30%;">' . __("Inviting lockdown (days)", "secure_invite") . '</label>
	<input type="text" name="secure_invite_days_after_joining" id="secure_invite_days_after_joining" value="'.$secure_invite_days_after_joining.'" style="width:10%" /> <span class="description">' . __("A user must have been registered for how many days before they can invite friends?", "secure_invite") . '</span></p>
	
	<p><label for="secure_invite_invite_limit" style="float:left;width:30%;">' . __("Maximum number of invites per person", "secure_invite") . '</label>
	<input type="text" name="secure_invite_invite_limit" id="secure_invite_invite_limit" value="'.$secure_invite_invite_limit.'" style="width:10%" /> <span class="description">' . __('How many invites can each user send (<a href="' . secure_invite_page_link(). '.php?page=secure_invite_list&amp;view=users">override this for particular users here</a>)? (set as blank for unlimited)', "secure_invite") . '</span></p>
	
	<h3 style="padding-top:2em">' . __( "Security settings", "secure_invite" ) . '</h3>
	
	<p><label for="secure_invite_open_signup" style="float:left;width:30%;">' . __("Open signup", "secure_invite") . '</label>
	<select name="secure_invite_open_signup" id="secure_invite_open_signup">
		<option value="0">' . __("No", "secure_invite") . '</option>
		<option value="1"' . $open_signup . '>' . __("Yes", "secure_invite"). '</option>
		</select> <span class="description">' . __("Allow anyone to sign up? This disables the security on the signup page", "secure_invite") . '</span></p>
	
	<p><label for="secure_invite_signup_page" style="float:left;width:30%;">' . __("Signup page", "secure_invite") . '</label>
	<input type="text" name="secure_invite_signup_page" id="secure_invite_signup_page" value="'.$secure_invite_signup_page.'" style="width:60%" /></p>
	<p>' . __("What is the address of the signup page? (wp-login.php?action=register is the default). You can put multiple addresses here separated by a comma (,). For example, when using Buddypress you will want to make this &quot;wp-signup.php,wp-login.php?action=register,/register,wp-register.php&quot;", "secure_invite") . '</p>
	
	<h3 style="padding-top:2em">' . __( "Signup settings", "secure_invite" ) . '</h3>
	
	<p>' . __("What address do you want the invitation emails to send people to? Please add the full URL to the registration page.", "secure_invite") . '</p>
	<p><label for="secure_invite_registration_page" style="float:left;width:30%;">' . __("Signup page", "secure_invite") . '</label>
	<input type="text" name="secure_invite_registration_page" id="secure_invite_registration_page" value="'.$secure_invite_registration_page.'" style="width:60%" /></p>
	
	<p><label for="secure_invite_signup_time_limit" style="float:left;width:30%;">' . __("Time limit for signups (days)", "secure_invite") . '</label>
	<input type="text" name="secure_invite_signup_time_limit" id="secure_invite_signup_time_limit" value="'.$secure_invite_signup_time_limit.'" style="width:10%" /> <span class="description">' . __("How many days would you like an invitation to be open for?", "secure_invite") . '</span></p>
	
	<h3 style="padding-top:2em">' . __( "Invitation codes", "secure_invite" ) . '</h3>
	<p>' . __("Allow people to sign up to your site using a special code. The code they sign up with will be stored so you can see which codes are most effective. Add one code per line.", "secure_invite") . '</p>
	<p><label for="secure_invite_invitation_codes" style="float:left;width:30%;">' . __("Invitation codes (one per line)", "secure_invite") . '</label>
	<textarea name="secure_invite_invitation_codes" id="secure_invite_invitation_codes" style="width:99%" rows="4" cols="30">'.$secure_invite_invitation_codes.'</textarea></p>
	
	<h3 style="padding-top:2em">' . __( "Message settings", "secure_invite" ) . '</h3>
	
	<p>' . __("What message do you want to show if someone tries to sign up without being invited?", "secure_invite") . '</p>
	<p><label for="secure_invite_no_invite_message" style="float:left;width:30%;">' . __("No invitation message", "secure_invite") . '</label>
	<input type="text" name="secure_invite_no_invite_message" id="secure_invite_no_invite_message" value="'.$secure_invite_no_invite_message.'" style="width:60%" /></p>
	
	<p>' . __("Enter the message you would like to appear below the users personal message in the invite email. There are four special keywords to enter which are automatically changed when the email is sent. Use these keywords:", "secure_invite") . '</p>
	<ul>
		<li><code>[sitename]</code> ' . __("where you want the name of your site to appear", "secure_invite") . '</li>
		<li><code>[signuplink]</code> ' . __("where you want the special link to the signup form to appear", "secure_invite") . '</li>
		<li><code>[name]</code> ' . __("where you want the name of the email sender to appear", "secure_invite") . '</li>
		<li><code>[timeout]</code> ' . __("where you want the number of days this invitation is valid to appear", "secure_invite") . '</li>
	</ul>
	<p><label for="secure_invite_default_message" style="float:left;width:30%;">' . __("Default message for invites", "secure_invite") . '</label>
	<textarea name="secure_invite_default_message" id="secure_invite_default_message" style="width:99%" rows="12" cols="30">'.$secure_invite_default_message.'</textarea></p>
	';
	
	if (defined("BP_CORE_DB_VERSION")) {
	echo '
	<h3 style="padding-top:2em">' . __( "BuddyPress theme settings", "secure_invite" ) . '</h3>
	<p>' . __("Where would you like the invitation form to how in your Buddypress site? The form will be hidden by default, and can be shown by clicking a button.", "secure_invite") . '</p>
	<ul>
		<!--li><input type="checkbox" name="secure_invite_buddypress_theme_actions[]" value="bp_before_members_loop"';
		if (strpos($secure_invite_buddypress_theme_actions, "bp_before_members_loop,") !== false){ echo ' checked="checked"'; }
		echo ' /> ' . __("Before any list of members", "secure_invite") . '</li-->
		<!--li><input type="checkbox" name="secure_invite_buddypress_theme_actions[]" value="bp_after_members_loop"';
		if (strpos($secure_invite_buddypress_theme_actions, "bp_after_members_loop,") !== false){ echo ' checked="checked"'; }
		echo ' /> ' . __("After any list of members", "secure_invite") . '</li-->
		<li><input type="checkbox" name="secure_invite_buddypress_theme_actions[]" value="bp_before_container"';
		if (strpos($secure_invite_buddypress_theme_actions, "bp_before_container,") !== false){ echo ' checked="checked"'; }
		echo ' /> ' . __("At the top of every page", "secure_invite") . '</li>
		<li><input type="checkbox" name="secure_invite_buddypress_theme_actions[]" value="bp_before_blog_home"';
		if (strpos($secure_invite_buddypress_theme_actions, "bp_before_blog_home,") !== false){ echo ' checked="checked"'; }
		echo ' /> ' . __("Before your site homepage", "secure_invite") . '</li>
		<li><input type="checkbox" name="secure_invite_buddypress_theme_actions[]" value="bp_after_blog_home"';
		if (strpos($secure_invite_buddypress_theme_actions, "bp_after_blog_home,") !== false){ echo ' checked="checked"'; }
		echo ' /> ' . __("After your site homepage", "secure_invite") . '</li>
		<li><input type="checkbox" name="secure_invite_buddypress_theme_actions[]" value="bp_inside_before_sidebar"';
		if (strpos($secure_invite_buddypress_theme_actions, "bp_inside_before_sidebar,") !== false){ echo ' checked="checked"'; }
		echo ' /> ' . __("At the top of the default sidebar", "secure_invite") . '</li>
		<li><input type="checkbox" name="secure_invite_buddypress_theme_actions[]" value="bp_inside_after_sidebar"';
		if (strpos($secure_invite_buddypress_theme_actions, "bp_inside_after_sidebar,") !== false){ echo ' checked="checked"'; }
		echo ' /> ' . __("At the bottom of the default sidebar", "secure_invite") . '</li>
		<li><input type="checkbox" name="secure_invite_buddypress_theme_actions[]" value="bp_nowhere"';
		if (strpos($secure_invite_buddypress_theme_actions, "bp_nowhere,") !== false){ echo ' checked="checked"'; }
		echo ' /> ' . __("Don't use automatic BuddyPress integration", "secure_invite") . '</li>
	</ul>
	';
	}
	
	echo '
	<p><button type="submit" name="secure_invite_save_settings" class="button-primary">' . __("Save these settings", "secure_invite") . '</button>
	<input type="hidden" name="secure_invite_preset" value="5" /></p>
	
	</fieldset>
	</form>
	</div>
	';
	
	echo '</div>
	';
}

// send bulk invitations
function secure_invite_send_bulk($emails, $message) {
	$emails = trim($emails);
	$emails = explode("\n", $emails);
	$sent = 0;
	$failed = array();
	$total = count($emails);
	if ($total === 0) {
		echo '<div id="message" class="updated fade"><p><strong>'.__('No email addresses were entered. Please try again.', "secure_invite").'</strong></p></div>';
	} else {
		foreach($emails as $email) {
			$email = trim($email);
			// sort out CSV
			if (strpos($email, ",") !== false) {
				$parts = explode(",", $email);
				$_POST["invite-email"] = $parts[1];
				$_POST["invite-name"] = $parts[0];
			} else {
				$_POST["invite-email"] = $email;
				$_POST["invite-name"] = $email;
			}
			$_POST['invite-personalmessage'] = $message;
			if (is_email($_POST["invite-email"])) {
				if (secure_invite_send()) {
					$sent++;
				} else {
					$failed[] = $_POST["invite-email"] . ' - Failed to send, maybe the email addresses has already been registered';
				}
			} else {
				$failed[] = $_POST["invite-email"] . ' - Not a valid email address';
			}
		}
		if ($sent === $total) {
			echo '<div id="message" class="updated fade"><p><strong>'.sprintf(__('%1$s emails were sent.', "secure_invite"), $sent).'</strong></p></div>';
		} else {
			echo '<div id="message" class="updated fade"><p><strong>'.sprintf(__('%1$s emails were sent from %2$s entered email addresses. The failed email addresses are below:', "secure_invite"), $sent, $total).'</strong></p><p><textarea name="failed_addresses" rows="10" cols="50" style="width:99%;height:6em">';
			foreach($failed as $fail) {
				echo $fail . "\r";
			}
			echo '</textarea></div>';
		}
		
	}
}

// bulk invite users
function secure_invite_bulk_invite() {

	if (@$_POST && is_array($_POST) && count($_POST) > 0) {
		secure_invite_send_bulk($_POST["invite-emails"], $_POST["invite-personalmessage"]);
	}
	
	$site_name = stripslashes( get_site_option("site_name") );
	if ( $site_name == "" ){ $site_name = stripslashes( get_option( "blogname" ) ); }

	echo '<div class="wrap">
	<h2>' . __("Bulk invite", "secure_invite") . '	
	<span style="float:right">
		<a href="' . secure_invite_page_link() . '.php?page=secure_invite_list" class="button">' . __("Invitation list", "secure_invite") . '</a>
		<a href="' . secure_invite_page_link() . '.php?page=secure_invite_list&amp;view=bulk" class="button">' . __("Bulk invite", "secure_invite") . '</a>
		<a href="' . secure_invite_page_link() . '.php?page=secure_invite_list&amp;view=users" class="button">' . __("Special users", "secure_invite") . '</a></span>
	</h2>
	';
	do_action("secure_invite_user_can_invite");
	echo '
	<form action="options-general.php?page=secure_invite_list&amp;view=bulk" method="post">
	<p>' . __("Add email addresses, one per line. You can also add names and email addresses using CSV formatting. Each line should be like this:<br /><em>Invitee name,invitee@email.com</em>", "secure_invite") . '</p>
	<p style="clear:both">
		<label for="emails" style="display:block">' . __("Emails", "secure_invite") . '</label>
		<textarea rows="10" cols="50" name="invite-emails" id="emails" style="width:99%;height:6em"></textarea>
	</p>
	<p style="clear:both">
		<label for="personalmessage" style="display:block">' . __("Your message", "secure_invite") . '</label>
		<textarea rows="10" cols="50" name="invite-personalmessage" id="personalmessage" style="width:99%;height:6em">' . sprintf(__("I've been blogging at %s and thought you might like to try it out.\n\nMy blog is at %s", "secure_invite"), $site_name, get_option('home')) . '</textarea>
	</p>
	<p class="submit" style="clear:both">
		<input type="submit" name="Submit" class="button-primary" tabindex="4" value="' . __("Send Invitations", "secure_invite") . ' &raquo;" />
		<input type="hidden" name="invite-action" value="send" />
	</p>
	</form>
	</div>
	';
}

// show the users admin page
function secure_invite_users() {
	global $wpdb;
	
	echo '<div class="wrap">
	<h2>' . __("User invitation settings", "secure_invite") . '
	<span style="float:right">
		<a href="' . secure_invite_page_link() . '.php?page=secure_invite_list" class="button">' . __("Invitation list", "secure_invite") . '</a>
		<a href="' . secure_invite_page_link() . '.php?page=secure_invite_list&amp;view=bulk" class="button">' . __("Bulk invite", "secure_invite") . '</a>
		<a href="' . secure_invite_page_link() . '.php?page=secure_invite_list&amp;view=settings" class="button">' . __("Settings", "secure_invite") . '</a></span>
	</h2>';

	if (@$_POST && is_array($_POST) && count($_POST) > 0)
	{
		
		// search users
		if ($_POST["secure_invite_search_users"] != "")
		{
		
			$q = trim($_POST["secure_invite_search_users"]);
			$sql = "select u.ID, u.user_nicename, u.display_name, u.user_email, u.user_login,
					(select count(i.invited_email)
					from " . $wpdb->base_prefix . "invitations i
					inner join " . $wpdb->users . " s on CONVERT(s.user_email using utf8) = CONVERT(i.invited_email using utf8)
					where i.user_id = u.ID) as signups,
					(select count(invited_email) from " . $wpdb->base_prefix . "invitations
					where user_id = u.ID) as invitations
					from " . $wpdb->users . " u
					where u.user_email like '%" . mysql_real_escape_string($q) . "%'
					or u.user_nicename like '%" . mysql_real_escape_string($q) . "%'
					or u.display_name like '%" . mysql_real_escape_string($q) . "%'
					or u.user_login like '%" . mysql_real_escape_string($q) . "%';";
			$users = $wpdb->get_results($sql);
			if ($users && is_array($users) && count($users) > 0)
			{
				echo '
				<h3>' . __("Choose a user", "secure_invite") . '</h3>
				<table class="widefat" cellspacing="0">
				<thead>
				<tr>
					<th>' . __("Username", "secure_invite") . '</th>
					<th>' . __("Nice name", "secure_invite") . '</th>
					<th>' . __("Display name", "secure_invite") . '</th>
					<th>' . __("Email", "secure_invite") . '</th>
					<th>' . __("Invites sent", "secure_invite") . '</th>
					<th>' . __("Resulting signups", "secure_invite") . '</th>
				</tr>
				</thead>
				<tbody>
				';
				foreach($users as $user)
				{
					echo '
					<tr>
						<td><a href="' . secure_invite_page_link() . '.php?page=secure_invite_list&amp;view=users&amp;id=' . $user->ID . '">' . $user->user_login . '</a></td>
						<td><a href="' . secure_invite_page_link() . '.php?page=secure_invite_list&amp;view=users&amp;id=' . $user->ID . '">' . $user->user_nicename . '</a></td>
						<td><a href="' . secure_invite_page_link() . '.php?page=secure_invite_list&amp;view=users&amp;id=' . $user->ID . '">' . $user->display_name . '</a></td>
						<td><a href="' . secure_invite_page_link() . '.php?page=secure_invite_list&amp;view=users&amp;id=' . $user->ID . '">' . $user->user_email . '</a></td>
						<td>' . $user->invitations . '</td>
						<td>' . $user->signups . '</td>
					</tr>
					';
				}
				echo '
				</tbody>
				</table>
				';
			} else {
				echo '
				<div class="error"><p>' . __("No users found, please try again", "secure_invite") . '</p></div>
				';
			}
		
		}
		
		// update an individual user
		if (isset($_POST["secure_invite_user_invite_limit"]))
		{
			// update the details
			$can = "no";
			if ($_POST["secure_invite_user_can_invite"] == "1") {
				$can = "yes";
			}
			update_user_meta($_GET["id"], "secure_invite_user_can_invite", $can);
			if ($_POST["secure_invite_user_invite_limit"] != "")
			{
				update_user_meta($_GET["id"], "secure_invite_user_invite_limit", "_" . $_POST["secure_invite_user_invite_limit"]);
				echo '
				<div class="updated"><p>' . __("The settings for this user have been saved", "secure_invite") . '</p></div>
				';
			} else {
				update_user_meta($_GET["id"], "secure_invite_user_invite_limit", "_-1");
				echo '
				<div class="updated"><p>' . __("The settings for this user have been saved", "secure_invite") . '</p></div>
				';
			}
		}
		
		// update all users
		if ($_POST["secure_invite_global_increase_limit"] != "")
		{
		
			$done = 0;
			$increase = (int)$_POST["secure_invite_global_increase_limit"];
			$sql = "select u.ID, m.meta_value as invites
					from " . $wpdb->users . " u
					inner join " . $wpdb->usermeta . " m on m.user_id = u.ID
					where m.meta_key = 'secure_invite_user_invite_limit';";
			$users = $wpdb->get_results($sql);
			if ($users && is_array($users) && count($users) > 0)
			{
				foreach($users as $user)
				{
					update_user_meta($user->ID, "secure_invite_user_invite_limit", "_" .((int)trim($user->invites, "_") + $increase));
					$done++;
				}
			}
			
			if ($done == 0) {
			
			echo '
			<div class="updated"><p>' . __("No users updated. Perhaps you haven't set the limit for any users yet?", "secure_invite") . '</p></div>
			';
			
			} else {
			
			echo '
			<div class="updated"><p>';
			printf(__("%d users updated", "secure_invite"), $done);
			echo '</p></div>
			';
			
			}
		
		}
		
		// if resetting
		if ($_POST["secure_invite_user_invite_limit_reset"] == "1")
		{
			delete_user_meta( $_GET["id"], "secure_invite_user_can_invite" );
			delete_user_meta( $_GET["id"], "secure_invite_user_invite_limit" );
			
			echo '
			<div class="updated"><p>' . __("The settings for this user have been saved", "secure_invite") . '</p></div>
			';
		}
		
	}
	
	// get a user
	if (@$_GET && is_array($_GET) && @$_GET["id"] != "")
	{
		
		$user = get_userdata($_GET["id"]);
		
		$can_invite = secure_invite_get_user_meta($_GET["id"], "secure_invite_user_can_invite");
		if ($can_invite == "no") {
			$can_invite = '';
		} else {
			$can_invite = ' checked="checked"';
		}
		
		$invite_limit = trim(secure_invite_get_user_meta($_GET["id"], "secure_invite_user_invite_limit"), "_");
		if ($invite_limit == "-1") { $invite_limit = ""; }
		
		$remaining_header = "";
		if ($invite_limit != "-1") {
			$remaining = "<td>" . secure_invite_user_invites_remaining($user->ID) . "</td>";
			$remaining_header = "<th>" . __("Remaining invites", "secure_invite") . "</th>";
		}
		
		// get the details for this user
		$sent = secure_invite_user_sent_invites($user->ID);
		$accepted = (int)secure_invite_user_accepted_invites($user->ID);
		$rate = 0;
		if ($sent > 0) $rate = round((($accepted / $sent) * 100), 1);
		$points = (int)secure_invite_get_user_meta($user->ID, "secure_invite_points");
		
		echo '
		<p>' . __("Username", "secure_invite") . ': ' . $user->user_login . ' (<a href="mailto:' . $user->user_email . '">' . $user->user_email . '</a>)</p>
		';
		
		echo secure_invite_user_can_invite( $user->ID, false, false );
		
		echo '
		<table class="widefat">
		<thead>
		<tr>
			<th>' . __("Invites sent", "secure_invite") . '</th>
			<th>' . __("Invites accepted", "secure_invite") . '</th>
			<th>' . __("Acceptance rate", "secure_invite") . '</th>
			' . $remaining_header . '
			<th>' . __("Invite points", "secure_invite") . '</th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td>' . $sent . '</td>
			<td>' . $accepted . '</td>
			<td>' . $rate . '%</td>
			' . $remaining . '
			<td>' . $points . '</td>
		</tr>
		</tbody>
		</table>
		<h3>' . __("Set invite settings for this user", "secure_invite") . '</h3>
		<form action="' . secure_invite_page_link() . '.php?page=secure_invite_list&amp;view=users&amp;id=' . $_GET["id"] . '" method="post">
		<fieldset>
		<p><label for="secure_invite_user_can_invite" style="float:left;width:15%;">' . __("Can invite", "secure_invite") . '</label>
		<input type="checkbox" name="secure_invite_user_can_invite" id="secure_invite_user_can_invite" value="1"' . $can_invite . ' /> <span class="description">' . __("Can this user send invitations?", "secure_invites") . '</span></p>
		<p><label for="secure_invite_user_invite_limit" style="float:left;width:15%;">' . __("Invitation limit", "secure_invite") . '</label>
		<input type="text" name="secure_invite_user_invite_limit" id="secure_invite_user_invite_limit" value="' . $invite_limit . '" style="width:10%" /> <span class="description">' . __("Number of invitations this user can send (leave blank for unlimited)", "secure_invites") . '</span></p>
		<p><label for="secure_invite_user_invite_limit_reset" style="float:left;width:15%;">' . __("Reset invitation limit", "secure_invite") . '</label>
		<input type="checkbox" name="secure_invite_user_invite_limit_reset" id="secure_invite_user_invite_limit_reset" value="1" style="width:10%" /> <span class="description">' . __("Reset number of invitations this user can send to the global default", "secure_invites") . '</span></p>
		<p><input type="submit" name="save" class="button-primary" value="' . __("Save settings for this user", "secure_invite") . '" /></p>
		</fieldset>
		</form>
		';
	
	}

	echo '
	<div style="float:left;width:45%">
	<h3>' .  __("Search users", "secure_invite") . '</h3>
	<p>' . __("Search for a user to override their invitation settings.", "secure_invite") . '</p>
	<form action="' . secure_invite_page_link() . '.php?page=secure_invite_list&amp;view=users" method="post">
	<fieldset>
	<p><label for="secure_invite_search_users" style="float:left;width:15%;">' . __("Search users", "secure_invite") . '</label>
	<input type="text" name="secure_invite_search_users" id="secure_invite_search_users" value="'.@$_POST["secure_invite_search_users"].'" style="width:40%" /> <input type="submit" name="search" class="button" value="' . __("Search users", "secure_invite") . '" /></p>
	</fieldset>
	</form>
	</div>
	
	<div style="float:right;width:45%">
	<h3>' . __("Increase invites for every user", "secure_invite") . '</h3>
	<p>' . __("Increase the number of invites for each user (only works for users you have explicitly set a limit for)", "secure_invite") . '</p>
	<form action="' . secure_invite_page_link() . '.php?page=secure_invite_list&amp;view=users" method="post">
	<fieldset>
	<p><label for="secure_invite_global_increase_limit" style="float:left;width:50%;">' . __("Increase the number of invites each user can send by:", "secure_invite") . '</label>
	<input type="text" name="secure_invite_global_increase_limit" id="secure_invite_global_increase_limit" value="" style="width:15%" /> <input type="submit" name="search" class="button" value="' . __("Increase invites", "secure_invite") . '" /></p>
	</fieldset>
	</form>
	</div>
	';
}

function secure_invite_by_month($limit = 12, $style = "float:left;width:45%") {
	global $wpdb;
	// show the number of invites per month
	$sql = "select UNIX_TIMESTAMP(i.datestamp) as date,
			count(i.invited_email) as invites,
			(select count(i2.invited_email)
			from ".$wpdb->base_prefix."invitations i2
			inner join ".$wpdb->users." u2 on CONVERT(u2.user_email USING utf8) = CONVERT(i2.invited_email using utf8)
			where year(i2.datestamp) = year(i.datestamp)
			and month(i2.datestamp) = month(i.datestamp)) as signups
			from ".$wpdb->base_prefix."invitations i
			group by month(i.datestamp)
			order by i.datestamp desc
			limit 0, " . (int)$limit . ";";
	$invites_per_month = $wpdb->get_results($sql);
	$invites_per_month_num = count($invites_per_month);	
	echo '
	<div style="'.$style.'">
	<h3>' . __("Invitations per month", "secure_invite") . '</h3>
	';
	if ($invites_per_month && $invites_per_month_num > 0)
	{
	echo '
	<table summary="'.__("Invitations per month", "secure_invite").'" class="widefat">
	<thead>
	<tr>
		<th>'.__("Month", "secure_invite").'</th>
		<th>'.__("Invites sent", "secure_invite").'</th>
		<th>'.__("Resulting signups", "secure_invite").'</th>
	</tr>
	</thead>
	<tbody>
	';
	$alt = '';
	foreach ($invites_per_month as $invite_month)
	{
		if ($alt == '') { $alt = ' class="alternate"'; } else { $alt = ''; }
		echo '
		<tr'.$alt.'>
			<td>'.__(date("F Y", $invite_month->date)).'</td>
			<td>'.__($invite_month->invites).'</td>
			<td>'.__($invite_month->signups).'</td>
		</tr>
		';
	}
	echo '
	</tbody>
	</table>
	';
	} else {
	echo '
	<p>'.__("No invitations sent yet", "secure_invite").'</p>
	';
	}
	echo '
	</div>
	';
}

function secure_invite_by_inviter($limit = 12, $style = "float:right;width:45%") {
	global $wpdb;
	// get the best inviters by signups
	$sql = "select u.id, u.user_nicename,
			count(i.invited_email) as invites,
			(select count(i2.invited_email)
			from ".$wpdb->base_prefix."invitations i2
			inner join ".$wpdb->users." u2 on CONVERT(u2.user_email USING utf8) = CONVERT(i2.invited_email USING utf8)
			where i2.user_id = i.user_id) as signups
			from ".$wpdb->base_prefix."invitations i
			inner join ".$wpdb->base_prefix."users u on u.id = i.user_id
			group by i.user_id
			order by signups desc
			limit 0, " . (int)$limit . ";";
	$best_inviters = $wpdb->get_results($sql);
	$best_inviters_num = count($best_inviters);	
	echo '
	<div style="'.$style.'">
	<h3>' . __("Best inviters by signups", "secure_invite") . '</h3>
	';
	if ($best_inviters && $best_inviters_num > 0)
	{
	echo '
	<table summary="'.__("Best inviters by signups", "secure_invite").'" class="widefat">
	<thead>
	<tr>
		<th>'.__("Name", "secure_invite").'</th>
		<th>'.__("Invites sent", "secure_invite").'</th>
		<th>'.__("Resulting signups", "secure_invite").'</th>
	</tr>
	</thead>
	<tbody>
	';
	$alt = '';
	foreach ($best_inviters as $best_inviter)
	{
		if ($alt == '') { $alt = ' class="alternate"'; } else { $alt = ''; }
		echo '
		<tr'.$alt.'>
			<td><a href="?page=secure_invite_list&view=users&id=' . $best_inviter->id . '">'.__($best_inviter->user_nicename).'</a></td>
			<td>'.__($best_inviter->invites).'</td>
			<td>'.__($best_inviter->signups).'</td>
		</tr>
		';
	}
	echo '
	</tbody>
	</table>
	';
	} else {
	echo '
	<p>'.__("No invitations sent yet", "secure_invite").'</p>
	';
	}
	echo '
	</div>
	';
}

function secure_invite_list_page() {
	global $wpdb;

	echo '
	<div class="wrap">
	';
	secure_invite_wp_plugin_standard_header( "GBP", "Secure invites", "Chris Taylor", "chris@stillbreathing.co.uk", "http://wordpress.org/extend/plugins/wordpress-mu-secure-invites/" );
	echo '
	<h2>' . __("Secure invites admin", "secure_invite") . '
	<span style="float:right">
		<a href="' . secure_invite_page_link(). '.php?page=secure_invite_list&amp;view=settings" class="button">' . __("Settings", "secure_invite") . '</a>
		<a href="' . secure_invite_page_link() . '.php?page=secure_invite_list&amp;view=bulk" class="button">' . __("Bulk invite", "secure_invite") . '</a>
		<a href="' . secure_invite_page_link() . '.php?page=secure_invite_list&amp;view=users" class="button">' . __("Special users", "secure_invite") . '</a></span>
	</h2>
	';

	// if deleting
	if ((isset($_GET["delete"]) && $_GET["delete"] != "") || (isset($_POST["delete"]) && @$_POST["delete"] != ""))
	{
		if (isset($_GET["delete"]) && $_GET["delete"] != "") {
			$sql = "delete from ".secure_invite_prefix()."invitations
					where invited_email = '" . $wpdb->escape( str_replace(" ", "+", urldecode($wpdb->escape($_GET["delete"]))) ) . "';";
			if ($wpdb->query($sql)) {
				echo '<div id="message" class="updated fade"><p><strong>' . __("The invitation for this email address has been deleted", "secure_invite") . '</strong></p></div>';
			} else {
				echo '<div id="message" class="updated fade"><p><strong>' . __("The invitation for this email address could not be deleted", "secure_invite") . '</strong></p></div>';
			}
		} else {
			$emails = str_replace(" ", "+", urldecode(implode("','", $_POST["delete"])));
			$sql = "delete from ".secure_invite_prefix()."invitations
					where invited_email in ('" . stripslashes( $wpdb->escape( $emails ) ) . "');";
			if ($wpdb->query($sql)) {
				echo '<div id="message" class="updated fade"><p><strong>' . __("The selected invitations have been deleted", "secure_invite") . '</strong></p></div>';
			} else {
				echo '<div id="message" class="updated fade"><p><strong>' . __("The selected invitations could not be deleted", "secure_invite") . '</strong></p></div>';
			}
		}
	}

	// check the invites table exists
	secure_invite_check_table();
	
	// show signups over the last 12 months
	secure_invite_by_month();
	
	// show signups by best inviter
	secure_invite_by_inviter();
	
	// show best inviters by points
	secure_invite_by_points( 6, "float:left;width:75%;" );
	
	// show best codes by signups
	secure_invite_signups_by_code( "float:right;width:20%;" );
			
	// show the invitation list
	secure_invite_invite_list();
	
	echo '
	</div>
	';
}

// show the list of invites
function secure_invite_invite_list() {

	global $wpdb;
	
	// get the page
	$page = @(int)$_GET["p"];
	if ($page == "")
	{
		$page = "1";
	}
	$start = ($page * 50) -50;
	if ($start == "") { $start = 0; }
	
	// get the invites
	$sql = $wpdb->prepare("select SQL_CALC_FOUND_ROWS i.user_id, i.invited_email, UNIX_TIMESTAMP(i.datestamp) as datestamp, u.user_nicename as inviter, l.user_nicename as signed_up
			from ".secure_invite_prefix()."invitations i
			inner join ".$wpdb->users." u on u.id = i.user_id
			left outer join ".$wpdb->users." l on CONVERT(l.user_email USING utf8) = CONVERT(i.invited_email USING utf8)
			order by i.datestamp desc
			limit %d, 50", $start);
	$invites = $wpdb->get_results($sql);

	echo '
	<h3 style="clear:both;padding-top:20px">' . __("Invitation list", "secure_invite") . '</h3>
	';
	
	$invites_num = count($invites);
	$total = $wpdb->get_var( "SELECT found_rows() AS found_rows" );
	$invites_pages = ceil($total/50);
	
	if ($invites && $invites_num > 0)
	{
		if ($invites_pages > 1)
		{
			$thisp = @$_GET["p"];
			if ($thisp == "") { $thisp = 1; }
			echo '<ul style="list-style: none;">
			';
			for ($i = 1; $i <= $invites_pages; $i++)
			{
				if ($i == $thisp)
				{
					echo '<li style="display: inline;">'.$i.'</li>
				';
				} else {
					echo '<li style="display: inline;"><a href="' . secure_invite_page_link() . '.php?page=secure_invite_list&amp;p='.$i.'">'.$i.'</a></li>
				';
				}
			}
			echo '</ul>
			';
		}
		echo '<form action="' . secure_invite_page_link() . '.php?page=secure_invite_list" method="post">
		<table summary="'.__("Invitations sent by site users", "secure_invite").'" class="widefat">
		<thead>
		<tr>
			<th>'.__("Inviter", "secure_invite").'</th>
			<th>'.__("Datestamp", "secure_invite").'</th>
			<th>'.__("Invited email", "secure_invite").'</th>
			<th>'.__("Signed up name", "secure_invite").'</th>
			<th>'.__("Delete invitation", "secure_invite").'</th>
		</tr>
		</thead>
		<tbody>
		';
		$alt = '';
		foreach ($invites as $invite)
		{
			if ($alt == '') { $alt = ' class="alternate"'; } else { $alt = ''; }
			echo '<tr'.$alt.'>
			<td><a href="?page=secure_invite_list&view=users&id=' . $invite->user_id . '">' . $invite->inviter . '</a></td>
			<td>' . date("F j, Y, g:i a", $invite->datestamp) . '</td>
			<td>' . $invite->invited_email . '</td>
			<td>' . $invite->signed_up . '</td>';
			if ($invite->signed_up == "") {
			echo '
			<td>
				<a href="' . secure_invite_page_link() . '.php?page=secure_invite_list&amp;delete='.urlencode($invite->invited_email).'">' . __("Delete", "secure_invite") . '</a>
				<input type="checkbox" name="delete[]" value="'.urlencode($invite->invited_email).'" />
			</td>
			';
			} else {
			echo '
			<td></td>
			';
			}
			echo '
			</tr>
			';
		}
		echo '
		</tbody>
		</table>
		<p><input type="submit" name="deleteall" class="button" value="' . __("Delete all checked invitations", "secure_invite") . '" /></p>
		</form>
		';
		if ($invites_pages > 1)
		{
			echo '<ul style="list-style: none;">
			';
			for ($i = 1; $i <= $invites_pages; $i++)
			{
				if ($i == $thisp)
				{
					echo '<li style="display: inline;">'.$i.'</li>
				';
				} else {
					echo '<li style="display: inline;"><a href="' . secure_invite_page_link() . '.php?page=secure_invite_list&amp;p='.$i.'">'.$i.'</a></li>
				';
				}
			}
			echo '</ul>
			';
		}
	} else {
		echo '<p>' . __("No invitations sent yet.", "secure_invite") . '</p>';
	}

}

// get the best inviters by points
function secure_inviters_get_best_by_points($limit=6) {
	global $wpdb;
	
	// get the best inviters by points
	$sql = $wpdb->prepare( "select u.user_nicename, u.display_name,
			u.ID as user_id,
			CAST(m.meta_value AS SIGNED) as secure_invite_points,
			(select count(user_id) from ".secure_invite_prefix()."invitations where user_id = u.ID) as sent,
			(select count(u2.id) from ".$wpdb->users." u2 inner join ".secure_invite_prefix()."invitations i on CONVERT(i.invited_email USING utf8) = CONVERT(u2.user_email USING utf8) where i.user_id = u.ID) as accepted
			from ".$wpdb->users." u
			inner join ".$wpdb->usermeta." m on m.user_id = u.ID and m.meta_key = 'secure_invite_points'
			order by CAST(m.meta_value AS SIGNED) desc
			limit 0, %d;", $limit );
	$best_inviters = $wpdb->get_results($sql);
	return $best_inviters;
}

// show the best inviters by points
function secure_invite_by_points( $limit = 6, $style = "" ) {

	$best_inviters = secure_inviters_get_best_by_points($limit);
	$best_inviters_num = count($best_inviters);	
	echo '
	<div style="' . $style . '">
	<h3 style="clear:both">' . __("Best inviters by points", "secure_invite") . '</h3>
	';
	if ($best_inviters && $best_inviters_num > 0)
	{
	echo '
	<table summary="'.__("Best inviters by points", "secure_invite").'" class="widefat">
	<thead>
	<tr>
		<th>'.__("Name", "secure_invite").'</th>
		<th>'.__("Invites Sent", "secure_invite").'</th>
		<th>'.__("Invites Accepted", "secure_invite").'</th>
		<th>'.__("Acceptance Rate", "secure_invite").'</th>
		<th>'.__("Invite Points", "secure_invite").'</th>
	</tr>
	</thead>
	<tbody>
	';
	$alt = '';
	foreach ($best_inviters as $best_inviter)
	{
		$rate = 0;
		if ($best_inviter->sent > 0) $rate = round((($best_inviter->accepted / $best_inviter->sent) * 100), 1);
		if ($alt == '') { $alt = ' class="alternate"'; } else { $alt = ''; }
		echo '
		<tr'.$alt.'>
			<td>'.$best_inviter->user_nicename.'</td>
			<td>'.$best_inviter->sent.'</td>
			<td>'.$best_inviter->accepted.'</td>
			<td>'.$rate.'%</td>
			<td>'.$best_inviter->secure_invite_points.'</td>
		</tr>
		';
	}
	echo '
	</tbody>
	</table>
	';
	if ( $limit < 100 ) {
	echo '
	<p><a href="?page=secure_invite_list&amp;view=top">' . __( "View top 100 inviters by points", "secure_invite" ) . '</a></p>
	';
	}
	} else {
	echo '
	<p>' . __("No inviters with points yet", "secure_invite") . '</p>
	';
	}
	echo '
	</div>
	';

}

// get the signups by code
function secure_inviters_get_signups_by_code() {
	global $wpdb;
	
	// get the best inviters by points
	$sql = "select count(u.ID) as signups,
			m.meta_value as code
			from ".$wpdb->users." u
			inner join ".$wpdb->usermeta." m on m.user_id = u.ID and m.meta_key = 'secure_invite_invitation_code'
			group by m.meta_value
			order by count(u.ID) desc;";
	$best_codes = $wpdb->get_results($sql);
	return $best_codes;
}

// show the number of signups by code
function secure_invite_signups_by_code( $style = "" ) {

	$best_codes = secure_inviters_get_signups_by_code();
	$best_codes_num = count($best_codes);	
	echo '
	<div style="' . $style . '">
	<h3 style="clear:both">' . __("Best codes by signups", "secure_invite") . '</h3>
	';
	if ($best_codes && $best_codes_num > 0)
	{
	echo '
	<table summary="'.__("Best codes by signups", "secure_invite").'" class="widefat">
	<thead>
	<tr>
		<th>'.__("Code", "secure_invite").'</th>
		<th>'.__("Signups", "secure_invite").'</th>
	</tr>
	</thead>
	<tbody>
	';
	$alt = '';
	foreach ($best_codes as $best_code)
	{
		if ($alt == '') { $alt = ' class="alternate"'; } else { $alt = ''; }
		echo '
		<tr'.$alt.'>
			<td>'.$best_code->code.'</td>
			<td>'.$best_code->signups.'</td>
		</tr>
		';
	}
	echo '
	</tbody>
	</table>
	';
	} else {
	echo '
	<p>' . __("No signups using codes yet", "secure_invite") . '</p>
	';
	}
	echo '
	</div>
	';

}

// check the invites table exists
function secure_invite_check_table() {
	global $wpdb;
	// if the invitations table does not exist
	$sql = "select count(id) from ".secure_invite_prefix()."invitations;";
	$exists = $wpdb->get_var($sql);
	if($exists == "")
	{
		require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
		// include the file with the required database manipulation functions
		// create the table
		$sql = "CREATE TABLE ".secure_invite_prefix()."invitations (
id mediumint(9) NOT NULL AUTO_INCREMENT,
user_id mediumint(9),
invited_email varchar(255),
datestamp datetime,
PRIMARY KEY  (id)
);";
		dbDelta($sql);
	}
}

// show a BuddyPress form
function secure_invite_buddypress_form($hidelink = false, $usepost = false) {
	global $current_user;
	$secure_invite_show_admin_link = stripslashes( get_site_option("secure_invite_show_admin_link", "yes") );
	if ($secure_invite_show_admin_link == "") { $secure_invite_show_admin_link = "yes"; }
	// if the user can send invites
	if (secure_invite_user_can_invite( $current_user->ID ) && ($secure_invite_show_admin_link == "yes" || current_user_can("edit_users"))) {
		$name = "";
		$email = "";
		$message = "";
		if ($usepost) {
			$name = @$_POST["name"];
			$email = @$_POST["email"];
			$message = @$_POST["personalmessage"];
		}
		$rand = rand(1, 10000);
		$hide = "_visible";
		if (!$hidelink) {
			$hide = "";
			echo '
			<div class="generic-button" style="padding:0 0 0.6em 0"><p><a href="#secure_invite_form_' . $rand  . '" title="' . __("Invite a friend", "secure_invite") . '" class="add secure_invite_toggler">' . __("Invite a friend", "secure_invite") . '</a></p></div>
			';
		}
		
		// if an invite has been sent
		$send_result = @$_GET["send_result"];
		if ($send_result > 0) {
			$hide = "";
			// if the invite can be sent
			if ($send_result == 1) {
				// show the success message
				echo '<div id="message" class="updated"><p>' . __("Thanks, your invitation has been sent", "secure_invite") . '</p></div>';
			} else if ($send_result == 2) {
				// show the error message
				echo '<div id="message" class="error"><p>' . __("Sorry, your invitation could not be sent. Perhaps this email address is already registered. Please try again.", "secure_invite") . '</p></div>';
			} else if ($send_result == 3) {
				echo '<div id="message" class="error"><p>' . __("You must supply a valid email address. Please try again.", "secure_invite") . '</p></div>';
			}
		}
		
		$url = secure_invite_remove_querystring_var($_SERVER["REQUEST_URI"]);
		echo '
		<div id="secure_invite_form_' . $rand  . '" class="secure_invite_form_wrapper' . $hide . '">
		<h3>' . __( "Invite a friend to join", "secure_invite" ) . '</h3>
		<form action="' . trim(get_bloginfo("wpurl"), '/') . '/send-secure-invite?return=' . $url . '" method="post" class="standard-form">
		<fieldset>
			<p><label for="secure_invite_name_'.$rand.'">' . __("Name of person to invite", "secure_invite") . '</label>
			<input name="invite-name" type="text" id="secure_invite_name_'.$rand.'" value="' . $name . '" /></p>
			<p><label for="secure_invite_email_'.$rand.'">' . __("Email of person to invite", "secure_invite") . '</label>
			<input name="invite-email" type="text" id="secure_invite_email_'.$rand.'" value="' . $email . '" /></p>
			<p><label for="secure_invite_personalmessage_'.$rand.'">' . __("A personal message (optional)", "secure_invite") . '</label>
			<textarea rows="10" cols="50" name="invite-personalmessage" id="secure_invite_personalmessage_'.$rand.'">' . $message . '</textarea></p>
			<p><input type="submit" id="secure_invite_send_'.$rand.'" name="submit" value="' . __("Send Invitation", "secure_invite") . '" /> ' . secure_invite_user_invites_remaining($current_user->ID) . '</p>
			';
			$nonce = wp_nonce_field( 'secure_invite_send_invite', '_wpnonce', true, false );
			$nonce = str_replace('id="_wpnonce"', 'id="_wpnonce_'.$rand.'"', $nonce);
			echo $nonce;
			echo '
		</fieldset>
		</form>
		</div>
		<div style="clear:both"></div>
		';
	}
}

// remove the send_result variable from the querystring
function secure_invite_remove_querystring_var($url) {
    $url = str_replace("?send_result=1", "", $url);
    $url = str_replace("?send_result=2", "", $url);
	$url = str_replace("?send_result=3", "", $url);
	$url = str_replace("&send_result=1", "", $url);
	$url = str_replace("&send_result=2", "", $url);
	$url = str_replace("&send_result=3", "", $url);
    return ($url);
}

// show an invitation form
function secure_invite_form($success='Thanks, your invitation has been sent', $error='Sorry, your invitation could not be sent. Perhaps this email address is already registered.', $return=false) {
	global $current_user;
	$secure_invite_show_admin_link = stripslashes( get_site_option("secure_invite_show_admin_link", "yes") );
	if ($secure_invite_show_admin_link == "") { $secure_invite_show_admin_link = "yes"; }
	// if the user can send invites
	if (secure_invite_user_can_invite( $current_user->ID ) && ($secure_invite_show_admin_link == "yes" || current_user_can("edit_users"))) {
		$r = '
		<div id="secure_invite_form">
		';
		// if an email has been supplied
		if (@$_POST['invite-email'] != "" && is_email($_POST['invite-email'])) {
			if (secure_invite_send()) {
				// show the success message
				$r .= '<div class="success"><p>' . __($success, "secure_invite") . '</p></div>';
			} else {
				// show the error message
				$r .= '<div class="error"><p>' . __($error, "secure_invite") . '</p></div>';
			}
		}
		// show the form
		$r .= '
		<form action="' . $_SERVER[ "REQUEST_URI" ] . '#secure_invite_form" method="post" class="secure_invite_form">
		<fieldset>
			<p><label for="secure_invite_name">' . __("Name of person to invite", "secure_invite") . '</label>
			<input name="invite-name" type="text" id="secure_invite_name" value="" /></p>
			<p><label for="secure_invite_email">' . __("Email of person to invite", "secure_invite") . '</label>
			<input name="invite-email" type="text" id="secure_invite_email" value="" /></p>
			<p><label for="secure_invite_personalmessage">' . __("A personal message (optional)", "secure_invite") . '</label>
			<textarea rows="10" cols="50" name="invite-personalmessage" id="secure_invite_personalmessage"></textarea></p>
			<p><label for="secure_invite_send">' . __("Send this invitation", "secure_invite") . '</label>
			<input type="submit" id="secure_invite_send" name="submit" value="' . __("Send Invitation", "secure_invite") . '" /> ' . secure_invite_user_invites_remaining($current_user->ID) . '</p>
		</fieldset>
		</form>
		</div>';
		if ($return) {
			return $r;
		} else {
			echo $r;
		}
	}
}

// check the registration setting
function secure_invite_registration_setting_ok() {
	$site_registration = stripslashes( get_site_option( "registration" ) );
	if (version_compare(get_bloginfo('version'), "3") >= 0 && (!defined( 'WP_ALLOW_MULTISITE' ) || !WP_ALLOW_MULTISITE)) {
		$site_registration = get_option( "users_can_register" );
	}
	if ($site_registration == "all" || $site_registration == "user" || $site_registration == "1") return true;
	return false;
}

// see if a user can send an invite
function secure_invite_user_can_invite($userid = "", $allow_admins = true, $return_bool = true) {
	// return true for administrators
	if ($allow_admins && current_user_can("edit_users")) { return true; }

	global $wpdb;
	
	if ($userid == "") {
		global $current_user;
		$userid = $current_user->ID;
	}

	// if the user is set
	if ($userid != "")
	{
		// if site registration is allowed
		if (secure_invite_registration_setting_ok())
		{
			// if the user has not been overridden
			if (secure_invite_get_user_meta($userid, "secure_invite_user_can_invite") != "no")
			{
				// get the date this user was registered
				$registered = $wpdb->get_var($wpdb->prepare("select UNIX_TIMESTAMP(user_registered) from ".$wpdb->users." where id=%d;", $userid));
				
				// get how many days after registration invites are locked
				$secure_invite_days_after_joining = (int)stripslashes( get_site_option("secure_invite_days_after_joining", "30") );
				if ($secure_invite_days_after_joining === "") { $secure_invite_days_after_joining = 30; }
				
				// if the user is not too new, or is a site admin
				if ($registered < (time() - ($secure_invite_days_after_joining * 24 * 60 * 60)) || $secure_invite_days_after_joining == "0" || secure_invite_is_super_admin($userid))
				{
					// get the total number of invites a user is allowed to send
					$secure_invite_invite_limit = stripslashes( get_site_option("secure_invite_invite_limit", "0") );
					if ($secure_invite_invite_limit == "") { $secure_invite_invite_limit = 0; }
					
					// get the limit for this user
					$user_limit = trim(secure_invite_get_user_meta($userid, "secure_invite_user_invite_limit"), "_");
					if ($user_limit != "-1") { $secure_invite_invite_limit = (int)$user_limit; }
					
					// get the number of invites this user has sent
					$sent = secure_invite_user_sent_invites();
				
					// if the user has sent less than their limit, or there is no limit
					if ($sent < $secure_invite_invite_limit || $secure_invite_invite_limit == "" || $secure_invite_invite_limit == -1 || $user_limit == -1)
					{
						if ($return_bool) return true;
						return secure_invites_user_allowed_message();
					} else {
						if ($return_bool) {
							add_action('admin_head', 'secure_invites_disallowed_limit');
							add_action('wp_head', 'secure_invites_disallowed_limit');
							add_action('secure_invite_user_can_invite', 'secure_invites_disallowed_limit_error');
							return false;
						} else {
							return secure_invites_disallowed_limit_error();
						}
					}
				} else {
					if ($return_bool) {
						add_action('admin_head', 'secure_invites_disallowed_new');
						add_action('wp_head', 'secure_invites_disallowed_new');
						add_action('secure_invite_user_can_invite', 'secure_invites_disallowed_new_error');
						return false;
					} else {
						return secure_invites_disallowed_new_error();
					}
				}
			} else {
				if ($return_bool) {
					add_action('admin_head', 'secure_invites_disallowed_turnedoff');
					add_action('wp_head', 'secure_invites_disallowed_turnedoff');
					add_action('secure_invite_user_can_invite', 'secure_invites_disallowed_turnedoff_error');
					return false;
				} else {
					return secure_invites_disallowed_turnedoff_error();
				}
			}
		} else {
			if ($return_bool) {
				add_action('admin_head', 'secure_invites_disallowed_registration');
				add_action('wp_head', 'secure_invites_disallowed_registration');
				add_action('secure_invite_user_can_invite', 'secure_invites_disallowed_registration_error');
				return false;
			} else {
				return secure_invites_disallowed_registration_error();
			}
		}
	} else {
		if ($return_bool) {
			add_action('admin_head', 'secure_invites_disallowed_login');
			add_action('wp_head', 'secure_invites_disallowed_login');
			add_action('secure_invite_user_can_invite', 'secure_invites_disallowed_login_error');
			return false;
		} else {
			return secure_invites_disallowed_login_error();
		}
	}
}

 // is the logged in user a site/super admin
function secure_invite_is_super_admin() {
	if (function_exists("is_super_admin")){
		return is_super_admin();
	}
	if (function_exists("is_site_admin")){
		return is_site_admin();
	}
	return false;
}

// the user is allowed to send invites
function secure_invites_user_allowed_message() {
	echo '<div id="message" class="updated"><p>You are allowed to send invites</p></div>';
}

// the reasons why people are disallowed from sending invites
function secure_invites_disallowed_limit() {
	echo '<!-- Secure Invites: User cannot send invites because they have sent their limit of invitations -->';
}
function secure_invites_disallowed_new() {
	echo '<!-- Secure Invites: User cannot send invites because they have not been registered for long enough -->';
}
function secure_invites_disallowed_registration() {
	echo '<!-- Secure Invites: User cannot send invites because site registration is not turned on -->';
}
function secure_invites_disallowed_login() {
	echo '<!-- Secure Invites: User cannot send invites because they are not logged in -->';
}
function secure_invites_disallowed_turnedoff() {
	echo '<!-- Secure Invites: User cannot send invites because their invite rights have been revoked -->';
}

function secure_invites_disallowed_limit_error() {
	echo '<div id="message" class="updated"><p>You cannot send invites because you have sent your limit of invitations</p></div>';
}
function secure_invites_disallowed_new_error() {
	echo '<div id="message" class="updated"><p>You cannot send invites because they have not been registered for long enough</p></div>';
}
function secure_invites_disallowed_registration_error() {
	echo '<div id="message" class="updated"><p>Users cannot send invites because site registration is not turned on (so anyone responding to an invitation would be unable to register). <a href="options-general.php">Visit the General Settings page</a> and allow registrations.</p></div>';
}
function secure_invites_disallowed_login_error() {
	echo '<div id="message" class="updated"><p>You cannot send invites because you are not logged in</p></div>';
}
function secure_invites_disallowed_turnedoff_error() {
	echo '<div id="message" class="updated"><p>You cannot send invites because your invite rights have been revoked</p></div>';
}

// get the number of invites this user has sent 
function secure_invite_user_sent_invites($userid = 0) {
	global $wpdb, $current_user;
	if ($userid == 0) { $userid = $current_user->ID; }
	return $wpdb->get_var($wpdb->prepare("select count(user_id) from ".secure_invite_prefix()."invitations where user_id = %d", $userid));
}

// get the number of invites this user has sent which have resulted in a non-spam, non-deleted signup
function secure_invite_user_accepted_invites( $userid = 0 ) {
	global $wpdb, $current_user;
	if ($userid == 0) { $userid = $current_user->ID; }
	return $wpdb->get_var($wpdb->prepare("select count(u.id) from ".$wpdb->users." u inner join ".secure_invite_prefix()."invitations i on CONVERT(i.invited_email USING utf8) = CONVERT(u.user_email USING utf8) where u.user_status = 0 and i.user_id = %d", $userid));
}

// show how many invites this user is allowed to send
function secure_invite_user_invites_remaining($userid) {
	$remaining = secure_invite_user_invites_remaining_num($userid);
	if ($remaining > 0)
	{
		return __("Number of invites left to send:", "secure_invite") . " " . $remaining;
	} else if($remaining == -1) {
		return __("Unlimited invites", "secure_invite");
	} else {
		return __("No invites left to send", "secure_invite");
	}
}

// show how many invites this user is allowed to send
function secure_invite_user_invites_remaining_num($userid) {
	if ($userid == "") {
		global $current_user;
		$userid = $current_user->ID;
	}
	// get the total number of invites a user is allowed to send
	$secure_invite_invite_limit = stripslashes( get_site_option("secure_invite_invite_limit", "-1") );
	if ($secure_invite_invite_limit == "") { $secure_invite_invite_limit = (int)secure_invite_default_setting("secure_invite_invite_limit"); }
	
	// get the limit for this user
	$user_limit = secure_invite_get_user_meta($userid, "secure_invite_user_invite_limit");
	if (substr($user_limit, 0, 1) == "_") { $secure_invite_invite_limit = (int)trim($user_limit, "_"); }
	
	if ($secure_invite_invite_limit == -1)
	{
		return -1;
	} else if ($secure_invite_invite_limit == 0) {
		return 0;
	} else {
		// get the number of invites sent
		$sent = secure_invite_user_sent_invites($userid);
		return ($secure_invite_invite_limit - $sent);
	}
}

// check if an email address exists, or has already been invited
function secure_invite_email_exists( $email ) {
	global $wpdb;
	if( function_exists('email_exists') ) {
		$existing_user = email_exists( trim( $email ) );
	} else {
		$sql = $wpdb->prepare( "select user_email from " . $wpdb->users . " where user_email = %s;", trim( $email ) );
		$saved_email = $wpdb->get_var( $sql );
		if ( $saved_email == trim( $email ) ) {
			$existing_user = true;
		} else {
			$existing_user = false;
		}
	}
	$sql = $wpdb->prepare("select count(invited_email) from ".secure_invite_prefix()."invitations where invited_email = %s", $email );
	$found_emails = $wpdb->get_var( $sql );
	$already_invited = false;
	if ( $found_emails > 0 ) {
		$already_invited = true;
	}
	if ( $existing_user || $already_invited ) {
		return true;
	}
	return false;
}

// send an invitation
function secure_invite_send()
{
	global $current_site, $current_user, $blog_id, $wpdb;
	// check the user can invite
	if (secure_invite_user_can_invite( $current_user->ID ))
	{
		// check this email address isn't already registered
		if ( !secure_invite_email_exists( trim($_POST['invite-email']) ) ) {
			$usernickname = $current_user->display_name;
			$to = trim($_POST['invite-email']);
			$from = $current_user->display_name . ' <' . $current_user->user_email . '>';
			$pname = trim($_POST['invite-name']);
			$site_name = stripslashes( get_site_option("site_name") );
			if ( $site_name == "" ){ $site_name = stripslashes( get_option( "blogname" ) ); }
			
			// save the invitation 
			$sql = $wpdb->prepare("insert into ".secure_invite_prefix()."invitations
		(user_id, invited_email, datestamp)
		values
		(%d, %s, now());", $current_user->ID, $to);
									$wpdb->print_error();
			$query = $wpdb->query($sql);
			$query_error = mysql_error();
			// if the invitation could be saved
			if ($query)
			{
				if(!empty($pname)) {
					$subject = $pname.', '.$usernickname.'  has invited you to join '.$site_name;
					$message = "Dear ".$pname.", ";
				}
				else {
					$subject = 'Hi there, '. $usernickname.'  has invited you to join '.$site_name;
					$message = "Hi there, ";
				}
				
				$secure_invite_signup_time_limit = (int)stripslashes( get_site_option("secure_invite_signup_time_limit") );
				if ($secure_invite_signup_time_limit == "") { $secure_invite_signup_time_limit = secure_invite_default_setting("secure_invite_signup_time_limit"); }
				
				$secure_invite_signup_page = stripslashes( get_site_option("secure_invite_signup_page") );
				if ($secure_invite_signup_page == "") { $secure_invite_signup_page = secure_invite_default_setting("secure_invite_signup_page"); }
				
				$secure_invite_registration_page = stripslashes( get_site_option("secure_invite_registration_page") );
				if ($secure_invite_registration_page == "") { 
					$secure_invite_registration_page = secure_invite_default_setting("secure_invite_registration_page"); 
				}
					
				$secure_invite_default_message = stripslashes( get_site_option("secure_invite_default_message") );
				if ($secure_invite_default_message == "") { $secure_invite_default_message = secure_invite_default_setting("secure_invite_default_message"); }

				$secure_invite_default_message = str_replace("[sitename]", $site_name, $secure_invite_default_message);
				$secure_invite_default_message = str_replace("[signuplink]", $secure_invite_registration_page . $to, $secure_invite_default_message);
				$secure_invite_default_message = str_replace("[name]", $usernickname, $secure_invite_default_message);
				$secure_invite_default_message = str_replace("[timeout]", $secure_invite_signup_time_limit, $secure_invite_default_message);
				
				$message = $message . "\n\n" . stripslashes($_POST['invite-personalmessage']) . "\n\n" . $secure_invite_default_message;
				
				$headers = 'From: '. $from . "\r\n" . 
							'Reply-To: ' . $from;
				wp_mail($to, $subject, $message, $headers);

				return true;
			} else {
				$headers = 'From: '. $from . "\r\n" . 
							'Reply-To: ' . $from;
				wp_mail(stripslashes( get_site_option("admin_email") ), "Secure invite failure for ".$from, "A user just tried to invite someone to join ".$site_name.". The following SQL query could not be completed:\n\n".$sql."\n\nThe error reported was:\n\n".$query_error."\n\nThis is an automatic email sent by the Secure Invites plugin.", $headers);
			}
		}
	}
	return false;
}

// add an invitation to the database
function secure_invite_admin() {
	global $current_site, $current_user, $blog_id, $wpdb;

	$site_name = stripslashes( get_site_option("site_name") );
	if ( $site_name == "" ){ $site_name = stripslashes( get_option( "blogname" ) ); }
	
	// check the invites table exists
	secure_invite_check_table();

	if( isset( $_POST['invite-action'] ) && $_POST['invite-action'] == "send" )
	{
		// if the email is valid
		if(is_email($_POST['invite-email']))
		{
			// try to send
			if (secure_invite_send())
			{
				echo '<div id="message" class="updated fade"><p><strong>'.__('Your invitation has been successfully sent to', "secure_invite").' '.$_POST['invite-email'].'.</strong></p></div>';
				// the invitation could not be saved, show an error
			} else {
				echo '<div id="message" class="updated fade"><p><strong>'.__('Your invitation could not be sent to', "secure_invite").' '.$_POST['invite-email'].'. '.__('Perhaps this email address is already registered. Please try again. If it fails more than twice please contact the site administrator.', "secure_invite").'</strong></p></div>';
			}
		}
		else
		{
			echo '<div id="message" class="updated fade"><p><strong>'.__('Please enter a valid email address', "secure_invite").'</strong></p></div>';
		} // end error
	} // end if action is send
	

	echo '<div class="wrap">';
  echo '<h2>' . __("Invite a friend to join", "secure_invite") . ' '.$site_name.'</h2> ';
  echo '<form method="post" action="index.php?page=secure_invite"> 
		<fieldset>
			<p> 
				<label for="name" style="float:left;width:20%;">'.__('Name', "secure_invite").'</label>
				<input name="invite-name" type="text" id="name" value="" style="float:left;width:79%;" />
			</p> 
			<p style="clear:both">
				<label for="email" style="float:left;width:20%;">'.__('Email', "secure_invite").'</label> 
				<input name="invite-email" type="text" id="email" value="" style="float:left;width:79%;" />
			</p>
			<p style="clear:both">
				<label for="personalmessage" style="display:block">' . __("Your message", "secure_invite") . '</label>
				<textarea rows="10" cols="50" name="invite-personalmessage" id="personalmessage" style="width:99%;height:6em">' . sprintf(__("I've been blogging at %s and thought you might like to try it out.\n\nMy blog is at %s", "secure_invite"), $site_name, get_option('home')) . '</textarea>
			</p>
			<p class="submit" style="clear:both">
				<input type="submit" name="Submit" class="button-primary" tabindex="4" value="' . __("Send Invitation", "secure_invite") . ' &raquo;" /> ';
				global $current_user;
				echo secure_invite_user_invites_remaining($current_user->ID);
				echo '
				<input type="hidden" name="invite-action" value="send" />
			</p>
		</fieldset>
		</form>
		</div>';
}

function secure_invite_get_user_meta( $user_id, $meta_key ) {
	if ( function_exists( 'get_user_meta' ) ) {
		return get_user_meta( $user_id, $meta_key, true );
	} else {
		return get_usermeta( $user_id, $meta_key );
	}
}

// a standard header for your plugins, offers a PayPal donate button and link to a support page
function secure_invite_wp_plugin_standard_header( $currency = "", $plugin_name = "", $author_name = "", $paypal_address = "", $bugs_page ) {
	$r = "";
	$option = get_option( $plugin_name . " header" );
	if ( ( isset( $_GET[ "header" ] ) && $_GET[ "header" ] != "" ) || ( isset ( $_GET["thankyou"] ) && $_GET["thankyou"] == "true" ) ) {
		update_option( $plugin_name . " header", "hide" );
		$option = "hide";
	}
	if ( isset( $_GET["thankyou"] ) && $_GET["thankyou"] == "true" ) {
		$r .= '<div class="updated"><p>' . __( "Thank you for donating" ) . '</p></div>';
	}
	if ( $currency != "" && $plugin_name != "" && ( !isset( $_GET[ "header" ] ) || $_GET[ "header" ] != "hide" ) && $option != "hide" )
	{
		$r .= '<div class="updated">';
		$pageURL = 'http';
		if ( isset( $_SERVER["HTTPS"] ) && $_SERVER["HTTPS"] == "on" ) { $pageURL .= "s"; }
		$pageURL .= "://";
		if ( $_SERVER["SERVER_PORT"] != "80" ) {
			$pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
		}
		if ( strpos( $pageURL, "?") === false ) {
			$pageURL .= "?";
		} else {
			$pageURL .= "&";
		}
		$pageURL = htmlspecialchars( $pageURL );
		if ( $bugs_page != "" ) {
			$r .= '<p>' . sprintf ( __( 'To report bugs please visit <a href="%s">%s</a>.' ), $bugs_page, $bugs_page ) . '</p>';
		}
		if ( $paypal_address != "" && is_email( $paypal_address ) ) {
			$r .= '
			<form id="wp_plugin_standard_header_donate_form" action="https://www.paypal.com/cgi-bin/webscr" method="post">
			<input type="hidden" name="cmd" value="_donations" />
			<input type="hidden" name="item_name" value="Donation: ' . $plugin_name . '" />
			<input type="hidden" name="business" value="' . $paypal_address . '" />
			<input type="hidden" name="no_note" value="1" />
			<input type="hidden" name="no_shipping" value="1" />
			<input type="hidden" name="rm" value="1" />
			<input type="hidden" name="currency_code" value="' . $currency . '"/>
			<input type="hidden" name="return" value="' . $pageURL . 'thankyou=true" />
			<input type="hidden" name="bn" value="PP-DonationsBF:btn_donateCC_LG.gif:NonHosted" />
			<p>';
			if ( $author_name != "" ) {
				$r .= sprintf( __( 'If you found %1$s useful please consider donating to help %2$s to continue writing free Wordpress plugins.' ), $plugin_name, $author_name );
			} else {
				$r .= sprintf( __( 'If you found %s useful please consider donating.' ), $plugin_name );
			}
			$r .= '
			<p><input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="" /></p>
			</form>
			';
		}
		$r .= '<p><a href="' . $pageURL . 'header=hide" class="button">' . __( "Hide this" ) . '</a></p>';
		$r .= '</div>';
	}
	print $r;
}
function secure_invite_wp_plugin_standard_footer( $currency = "", $plugin_name = "", $author_name = "", $paypal_address = "", $bugs_page ) {
	$r = "";
	if ( $currency != "" && $plugin_name != "" )
	{
		$pageURL = 'http';
		if ( isset( $_SERVER["HTTPS"] ) && $_SERVER["HTTPS"] == "on" ) { $pageURL .= "s"; }
		$pageURL .= "://";
		if ( $_SERVER["SERVER_PORT"] != "80" ) {
			$pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
		}
		if ( strpos( $pageURL, "?") === false ) {
			$pageURL .= "?";
		} else {
			$pageURL .= "&";
		}
		$pageURL = htmlspecialchars( $pageURL );
		$r .= '<form id="wp_plugin_standard_footer_donate_form" action="https://www.paypal.com/cgi-bin/webscr" method="post" style="clear:both;padding-top:50px;"><p>';
		if ( $paypal_address != "" && is_email( $paypal_address ) ) {
			$r .= '
			<input type="hidden" name="cmd" value="_donations" />
			<input type="hidden" name="item_name" value="Donation: ' . $plugin_name . '" />
			<input type="hidden" name="business" value="' . $paypal_address . '" />
			<input type="hidden" name="no_note" value="1" />
			<input type="hidden" name="no_shipping" value="1" />
			<input type="hidden" name="rm" value="1" />
			<input type="hidden" name="currency_code" value="' . $currency . '"/>
			<input type="hidden" name="return" value="' . $pageURL . 'thankyou=true" />
			<input type="hidden" name="bn" value="PP-DonationsBF:btn_donateCC_LG.gif:NonHosted" />
			<input type="submit" name="submit" class="button" value="' . __( "PayPal: Donate" ) . '" />
			';
		}
		if ( $bugs_page != "" ) {
			$r .= sprintf ( __( '<a href="%s">Bugs</a>' ), $bugs_page );
		}
		$r .= '</p></form>';
	}
	print $r;
}
?>