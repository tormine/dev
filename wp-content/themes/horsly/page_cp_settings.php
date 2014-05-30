<?php
/*
Template Name: _settings
*/

if (!is_user_logged_in()) { wp_redirect(home_url('/login/?redirect_to=' . home_url('/settings/'))); exit; }

//Let users who login thru facebook and twitter change their username
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['form_name'] == 'change_username_form') {
	global $wpdb;
	
	if ($_GET['user'] && (current_user_can('administrator') || current_user_can('editor'))) {
		$user_info = get_userdata($_GET['user']);
	} else {
		$user_info = get_userdata($user_ID);
	}
	
	$sanitized_user_login = sanitize_user( $_POST['change_username'] );
	
	// Check the username
	if ( $sanitized_user_login == '' ) {
		$username_error = __( '<strong>ERROR</strong>: Please enter a username.', 'ipin' );
	} elseif ( ! validate_username( $user_login ) ) {
		$username_error = __( '<strong>ERROR</strong>: This username is invalid because it uses illegal characters. Please enter a valid username.', 'ipin' );
		$sanitized_user_login = '';
	} elseif ( username_exists( $sanitized_user_login ) && $sanitized_user_login != $user_info->user_login ) {
		$username_error = __( '<strong>ERROR</strong>: This username is already registered. Please choose another one.', 'ipin' );
	}
	
	if (!$username_error) {
		$user_nicename = sanitize_title( $sanitized_user_login );
		$q = sprintf( "UPDATE %s SET user_login='%s', user_nicename='%s' WHERE ID=%d", $wpdb->users, $sanitized_user_login, $user_nicename, (int) $user_info->ID );
		$wpdb->query($q);
		update_usermeta( $user_info->ID, 'nickname', $sanitized_user_login );
		update_usermeta( $user_info->ID, 'ipin_changed_username', '1' );
		wp_redirect(home_url('/login/?redirect_to=' . home_url('/settings/')));
		exit;
	}
}

//Save Settings
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['form_name'] == 'save_settings_form') {
	require_once(ABSPATH . 'wp-admin/includes/image.php');
	require_once(ABSPATH . 'wp-admin/includes/file.php');
	require_once(ABSPATH . 'wp-admin/includes/media.php');
	
	if ($_GET['user'] && (current_user_can('administrator') || current_user_can('editor'))) {
		$user_info = get_userdata($_GET['user']);
		$errors = ipin_edit_user($_GET['user']);
	} else {
		$user_info = get_userdata($user_ID);
		$errors = ipin_edit_user($user_ID);
	}
	
	if ($_POST['ipin_user_location'] != $user_info->ipin_user_location)
		update_user_meta($user_info->ID, 'ipin_user_location', sanitize_text_field($_POST['ipin_user_location']));
	if ($_POST['ipin_user_facebook'] != $user_info->ipin_user_facebook)
		update_user_meta($user_info->ID, 'ipin_user_facebook', sanitize_text_field($_POST['ipin_user_facebook']));
	if ($_POST['ipin_user_twitter'] != $user_info->ipin_user_twitter)
		update_user_meta($user_info->ID, 'ipin_user_twitter', sanitize_text_field($_POST['ipin_user_twitter']));
	if ($_POST['ipin_user_pinterest'] != $user_info->ipin_user_pinterest)
		update_user_meta($user_info->ID, 'ipin_user_pinterest', sanitize_text_field($_POST['ipin_user_pinterest']));
	if ($_POST['ipin_user_googleplus'] != $user_info->ipin_user_googleplus)
		update_user_meta($user_info->ID, 'ipin_user_googleplus', sanitize_text_field($_POST['ipin_user_googleplus']));
	if ($_POST['ipin_user_notify_likes'] != $user_info->ipin_user_notify_likes)
		update_user_meta($user_info->ID, 'ipin_user_notify_likes', sanitize_text_field($_POST['ipin_user_notify_likes']));
	if ($_POST['ipin_user_notify_repins'] != $user_info->ipin_user_notify_repins)
		update_user_meta($user_info->ID, 'ipin_user_notify_repins', sanitize_text_field($_POST['ipin_user_notify_repins']));
	if ($_POST['ipin_user_notify_follows'] != $user_info->ipin_user_notify_follows)
		update_user_meta($user_info->ID, 'ipin_user_notify_follows', sanitize_text_field($_POST['ipin_user_notify_follows']));
	if ($_POST['ipin_user_notify_comments'] != $user_info->ipin_user_notify_comments)
		update_user_meta($user_info->ID, 'ipin_user_notify_comments', sanitize_text_field($_POST['ipin_user_notify_comments']));
		
	//save avatar
	$valid_image = '';
	if (!$_FILES['ipin_user_avatar']['error']) {
		foreach ($_FILES as $file => $array) {							
			$imageTypes = array (
				IMAGETYPE_GIF,
				IMAGETYPE_JPEG,
				IMAGETYPE_PNG
			);
			
			$imageinfo = getimagesize($_FILES[$file]['tmp_name']);
			$width = @$imageinfo[0];
			$height = @$imageinfo [1];
			$type = @$imageinfo [2];
			$bits = @$imageinfo ['bits'];
			$mime = @$imageinfo ['mime'];
			
			if (!in_array($type, $imageTypes)) {
				@unlink($_FILES[$file]['tmp_name']);
				$valid_image = 'no';
			}
			
			if ($width <= 1 && $height <= 1) {
				@unlink($_FILES[$file]['tmp_name']);
				$valid_image = 'no';
			}
			
			if($mime != 'image/gif' && $mime != 'image/jpeg' && $mime != 'image/png') {
				@unlink($_FILES[$file]['tmp_name']);
				$valid_image = 'no';
			}


			add_image_size('avatar48', 48, 48, true);
			$attach_id = media_handle_upload($file, $post_id, array('post_title' => 'Avatar for UserID ' . $user_info->ID ));
			
			if (is_wp_error($attach_id)) {
				@unlink($_FILES[$file]['tmp_name']);
				$valid_image = 'no';
			} else {
				$user_avatar = get_user_meta($user_info->ID, 'ipin_user_avatar', true);
				if ($user_avatar != '')
					wp_delete_attachment($user_avatar);
					
				update_user_meta($user_info->ID, 'ipin_user_avatar', $attach_id);
			}
		}   
	}

	$savesuccess = '1';
}

//function from wp-admin/includes/user.php
function ipin_edit_user( $user_id = 0 ) {
	global $wp_roles, $wpdb;
	$user = new stdClass;
	if ( $user_id ) {
		$update = true;
		$user->ID = (int) $user_id;
		$userdata = get_userdata( $user_id );
		$user->user_login = $wpdb->escape( $userdata->user_login );
	} else {
		$update = false;
	}

	if ( !$update && isset( $_POST['user_login'] ) )
		$user->user_login = sanitize_user($_POST['user_login'], true);

	$pass1 = $pass2 = '';
	if ( isset( $_POST['pass1'] ))
		$pass1 = $_POST['pass1'];
	if ( isset( $_POST['pass2'] ))
		$pass2 = $_POST['pass2'];

	if ( isset( $_POST['role'] ) && current_user_can( 'edit_users' ) ) {
		$new_role = sanitize_text_field( $_POST['role'] );
		$potential_role = isset($wp_roles->role_objects[$new_role]) ? $wp_roles->role_objects[$new_role] : false;
		// Don't let anyone with 'edit_users' (admins) edit their own role to something without it.
		// Multisite super admins can freely edit their blog roles -- they possess all caps.
		if ( ( is_multisite() && current_user_can( 'manage_sites' ) ) || $user_id != get_current_user_id() || ($potential_role && $potential_role->has_cap( 'edit_users' ) ) )
			$user->role = $new_role;

		// If the new role isn't editable by the logged-in user die with error
		$editable_roles = get_editable_roles();
		if ( ! empty( $new_role ) && empty( $editable_roles[$new_role] ) )
			wp_die(__('You can&#8217;t give users that role.', 'ipin'));
	}

	//edited: store the original email
	$original_user_email = $userdata->user_email;

	if ( isset( $_POST['email'] ))
		$user->user_email = sanitize_text_field( $_POST['email'] );

	if ( isset( $_POST['url'] ) ) {
		if ( empty ( $_POST['url'] ) || $_POST['url'] == 'http://' ) {
			$user->user_url = '';
		} else {
			$user->user_url = esc_url_raw( $_POST['url'] );
			$protocols = implode( '|', array_map( 'preg_quote', wp_allowed_protocols() ) );
			$user->user_url = preg_match('/^(' . $protocols . '):/is', $user->user_url) ? $user->user_url : 'http://'.$user->user_url;
		}
	}
	if ( isset( $_POST['first_name'] ) )
		$user->first_name = sanitize_text_field( $_POST['first_name'] );
	if ( isset( $_POST['last_name'] ) )
		$user->last_name = sanitize_text_field( $_POST['last_name'] );
	if ( isset( $_POST['nickname'] ) )
		$user->nickname = sanitize_text_field( $_POST['nickname'] );
	if ( isset( $_POST['display_name'] ) )
		$user->display_name = sanitize_text_field( $_POST['display_name'] );

	if ( isset( $_POST['description'] ) )
		$user->description = trim( $_POST['description'] );

	foreach ( _wp_get_user_contactmethods( $user ) as $method => $name ) {
		if ( isset( $_POST[$method] ))
			$user->$method = sanitize_text_field( $_POST[$method] );
	}

	if ( $update ) {
		$user->rich_editing = isset( $_POST['rich_editing'] ) && 'false' == $_POST['rich_editing'] ? 'false' : 'true';
		$user->admin_color = isset( $_POST['admin_color'] ) ? sanitize_text_field( $_POST['admin_color'] ) : 'fresh';
		$user->show_admin_bar_front = isset( $_POST['admin_bar_front'] ) ? 'true' : 'false';
	}

	$user->comment_shortcuts = isset( $_POST['comment_shortcuts'] ) && 'true' == $_POST['comment_shortcuts'] ? 'true' : '';

	$user->use_ssl = 0;
	if ( !empty($_POST['use_ssl']) )
		$user->use_ssl = 1;

	$errors = new WP_Error();

	/* checking that username has been typed */
	if ( $user->user_login == '' )
		$errors->add( 'user_login', __( '<strong>ERROR</strong>: Please enter a username.', 'ipin' ));

	/* checking the password has been typed twice */
	do_action_ref_array( 'check_passwords', array ( $user->user_login, & $pass1, & $pass2 ));

	if ( $update ) {
		if ( empty($pass1) && !empty($pass2) )
			$errors->add( 'pass', __( '<strong>ERROR</strong>: You entered your new password only once.', 'ipin' ), array( 'form-field' => 'pass1' ) );
		elseif ( !empty($pass1) && empty($pass2) )
			$errors->add( 'pass', __( '<strong>ERROR</strong>: You entered your new password only once.', 'ipin' ), array( 'form-field' => 'pass2' ) );
			
		//edited: added to check password length
		if ( !empty($pass1) && !empty($pass2) )
			if ( strlen( $pass1 ) < 6 ) {
			$errors->add('password_too_short', "<strong>ERROR</strong>: Passwords must be at least 6 characters long", 'ipin');
		}
	} else {
		if ( empty($pass1) )
			$errors->add( 'pass', __( '<strong>ERROR</strong>: Please enter your password.', 'ipin' ), array( 'form-field' => 'pass1' ) );
		elseif ( empty($pass2) )
			$errors->add( 'pass', __( '<strong>ERROR</strong>: Please enter your password twice.', 'ipin' ), array( 'form-field' => 'pass2' ) );
	}

	/* Check for "\" in password */
	if ( false !== strpos( stripslashes($pass1), "\\" ) )
		$errors->add( 'pass', __( '<strong>ERROR</strong>: Passwords may not contain the character "\\".', 'ipin' ), array( 'form-field' => 'pass1' ) );

	/* checking the password has been typed twice the same */
	if ( $pass1 != $pass2 )
		$errors->add( 'pass', __( '<strong>ERROR</strong>: Please enter the same password in the two password fields.', 'ipin' ), array( 'form-field' => 'pass1' ) );

	if ( !empty( $pass1 ) )
		$user->user_pass = $pass1;

	if ( !$update && isset( $_POST['user_login'] ) && !validate_username( $_POST['user_login'] ) )
		$errors->add( 'user_login', __( '<strong>ERROR</strong>: This username is invalid because it uses illegal characters. Please enter a valid username.', 'ipin' ));

	if ( !$update && username_exists( $user->user_login ) )
		$errors->add( 'user_login', __( '<strong>ERROR</strong>: This username is already registered. Please choose another one.', 'ipin' ));

	/* checking e-mail address */
	$verify_new_email = $user_id;
	if ( empty( $user->user_email ) ) {
		$errors->add( 'empty_email', __( '<strong>ERROR</strong>: Please enter an email address.', 'ipin' ), array( 'form-field' => 'email' ) );
	} elseif ( !is_email( $user->user_email ) ) {
		$errors->add( 'invalid_email', __( '<strong>ERROR</strong>: The email address isn&#8217;t correct.', 'ipin' ), array( 'form-field' => 'email' ) );
	} elseif ( ( $owner_id = email_exists($user->user_email) ) && ( !$update || ( $owner_id != $user->ID ) ) ) {
		$errors->add( 'email_exists', __('<strong>ERROR</strong>: This email is already registered, please choose another one.', 'ipin'), array( 'form-field' => 'email' ) );
	//edited: requires email verification if email is changed
	} elseif ($userdata->user_email != $_POST['email'] && !current_user_can('administrator') && !current_user_can('editor'))  {
		//store new email temporarily
		update_user_meta($user_id, '_new_email', $user->user_email);

		$new_email_key = wp_generate_password(20, false);
		update_user_meta($user_id, '_new_email_key', $new_email_key);
		
		$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

		$message .= __('Please click the link to verify your email:', 'ipin') . "\r\n";
		$message .= home_url('/settings/');
		$message .= sprintf('?email=verify&login=%s&key=%s', $user->user_login, $new_email_key);

		wp_mail($user->user_email, sprintf(__('[%s] Email Verification', 'ipin'), $blogname), $message);

		$user->user_email = $original_user_email;
		$verify_new_email = 'verify_new_email';
	}

	// Allow plugins to return their own errors.
	do_action_ref_array('user_profile_update_errors', array ( &$errors, $update, &$user ) );

	if ( $errors->get_error_codes() )
		return $errors;

	if ( $update ) {
		$user_id = wp_update_user( $user );
	} else {
		$user_id = wp_insert_user( $user );
		wp_new_user_notification( $user_id, isset($_POST['send_password']) ? $pass1 : '' );
	}
	
	
		return $verify_new_email;
}

//check email verification
if ($_GET['email'] == 'verify') {
	if ($_GET['user'] && (current_user_can('administrator') || current_user_can('editor')))
		$user_info = get_userdata($_GET['user']);
	else
		$user_info = get_userdata($user_ID);
		
	if ($_GET['login'] == $user_info->user_login && $_GET['key'] == $user_info->_new_email_key) {
		wp_update_user(array('ID'=> $user_info->ID, 'user_email' => get_user_meta($user_info->ID, '_new_email', true)));
		delete_user_meta($user_info->ID, '_new_email');
		delete_user_meta($user_info->ID, '_new_email_key');
		$email_verified = 'yes';
	} else {
		$email_verified = 'no';
	}
}

//retreive latest userinfo even after updating above
if ($_GET['user'] && (current_user_can('administrator') || current_user_can('editor')))
	$user_info = get_userdata($_GET['user']);
else
	$user_info = get_userdata($user_ID);

get_header();
?>

<div class="container-fluid">
	<div class="row-fluid">

		<div class="span4"></div>

		<div class="span4 usercp-wrapper">
			<?php if ($user_info->wsl_user != '') { ?>
			<div class="hide">
			<?php } ?>
				<h1><?php _e('Settings', 'ipin'); if ($_GET['user'] && (current_user_can('administrator') || current_user_can('editor'))) echo ' - ' . $user_info->user_login; ?></h1>
			<?php if ($user_info->wsl_user != '') { ?>
			</div>
			<?php } ?>
			
			<?php 
			$user_registered_timestamp = strtotime($user_info->user_registered);
			$interval = time()- $user_registered_timestamp;
			$days_since_registered = floor($interval/60/60/24);
			$days_left = 3 - $days_since_registered;
			if ($days_left > 0 && $user_info->wsl_user != '' && get_usermeta($user_info->ID, 'ipin_changed_username', 'true') != '1') {
			 ?>
			<div class="alert alert-info">				
				<form name="usernameform" id="usernameform" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
					<?php
					if ($username_error) {
						echo '<p class="text-error"><strong>';
						echo $username_error;
						echo '</strong></p>';
					}
					?>
					<strong><?php _e('Change Profile Username? (Days Left:', 'ipin'); ?> <?php echo $days_left; ?>)</strong>
					<input type="text" name="change_username" id="change_username" value="<?php echo esc_attr($user_info->user_login); ?>" />
					<input type="hidden" name="form_name" id="form_name" value="change_username_form" />
					<input type="submit" class="btn" name="username-change" id="username-change" value="<?php _e('Change', 'ipin'); ?>" />
					<p><?php _e('You will be logout upon successful changing of username. Please login again with', 'ipin'); echo ' ' . $user_info->wsl_user; ?>.</p>
				</form>
			</div>
			<?php } ?>

			<?php if (is_wp_error($errors)) { ?>
				<div class="error-msg"><div class="alert"><strong><?php echo $errors->get_error_message(); ?></strong></div></div>
			<?php } else if ($valid_image == 'no') { ?>
				<div class="error-msg"><div class="alert"><strong><?php _e('Invalid image file for avatar. Please choose a JPG/GIF/PNG file.', 'ipin'); ?></strong></div></div>
			<?php }	else if ($savesuccess == '1') { ?>
				<div class="error-msg"><div class="alert alert-success"><strong><?php _e('Settings Saved.', 'ipin'); ?></strong></div></div>
			<?php } ?>
			
			<?php if ($errors == 'verify_new_email') { ?>
				<div class="error-msg"><div class="alert"><strong><?php _e('Your email will be changed upon verification. Please check your new email for verification link.', 'ipin'); ?></strong></div></div>
			<?php } ?>
			
			<?php if ($email_verified == 'yes') { ?>
				<div class="error-msg"><div class="alert alert-success"><strong><?php _e('Your email has been verified and updated.', 'ipin'); ?></strong></div></div>
			<?php } else if ($email_verified == 'no') { ?>
				<div class="error-msg"><div class="alert"><strong><?php _e('Invalid verification key', 'ipin'); ?></strong></div></div>
			<?php } ?>

			<form name="settingsform" id="settingsform" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype="multipart/form-data">
				<?php if ($user_info->wsl_user != '') { ?>
				<div class="hide">
				<?php } ?>
                    <div class="row-fluid">
                        <div class="span4">
                            <label for="email"><?php _e('Email', 'ipin'); ?><br /></label>
                        </div>
                        <div class="span8">
                            <input type="email" name="email" id="email" value="<?php echo esc_attr($user_info->user_email); ?>" tabindex="10" />
                        </div>
                    </div>

                    <div class="row-fluid">
                        <div class="span4">
                            <label for="pass1"><?php _e('Password', 'ipin') ?></label>
                        </div>
                        <div class="span8">
                            <input type="password" name="pass1" id="pass1" class="input" size="20" value="" autocomplete="off" tabindex="20" />
                        </div>
                    </div>

                    <div class="row-fluid">
                        <div class="span4">
                            <label for="pass2"><?php _e('Confirm Password', 'ipin') ?></label>
                        </div>
                        <div class="span8">
                            <input type="password" name="pass2" id="pass2" class="input" size="20" value="" autocomplete="off" tabindex="30" />
                            <span style="font-size: smaller"><?php _e('Enter new password to change it, or leave it blank.', 'ipin'); ?></span>
                        </div>
                    </div>

                    <br />
				<?php if ($user_info->wsl_user != '') { ?>
				</div>
				<?php } ?>

                <h2><?php _e('Profile', 'ipin'); ?></h2>
                <div class="row-fluid">
                    <div class="span12" style="text-align: right; margin-bottom: 1em;">
                        <a class="btn btn-small" href="<?php if ($_GET['user']) echo get_author_posts_url($_GET['user']); else echo get_author_posts_url($user_ID);  ?>" target="_blank">See Your Public Profile</a>
                    </div>
                </div>

                <div class="row-fluid">
                    <div class="span4">
                        <label for="display_name"><?php _e('Display Name', 'ipin'); ?></label>
                    </div>
                    <div class="span8">
                        <input type="text" name="display_name" id="display_name" value="<?php echo esc_attr($user_info->display_name); ?>" tabindex="40" />
                    </div>
                </div>

                <div class="row-fluid">
                    <div class="span4">
                        <label for="about"><?php _e('About', 'ipin') ?></label>
                    </div>
                    <div class="span8">
                        <textarea name="description" id="description" tabindex="50"><?php echo $user_info->user_description; ?></textarea>
                    </div>
                </div>

                <div class="row-fluid">
                    <div class="span4">
                        <label for="ipin_user_location"><?php _e('Location', 'ipin') ?></label>
                    </div>
                    <div class="span8">
                        <input type="text" name="ipin_user_location" id="ipin_user_location" class="input" value="<?php echo esc_attr($user_info->ipin_user_location); ?>" tabindex="60" placeholder="<?php _e('city, country', 'ipin'); ?>" />
                    </div>
                </div>

                <div class="row-fluid">
                    <div class="span4">
                        <label for="website"><?php _e('Website', 'ipin') ?></label>
                    </div>
                    <div class="span8">
                        <input type="text" name="url" id="url" value="<?php echo esc_attr($user_info->user_url); ?>" class="input" tabindex="70" placeholder="<?php _e('myweb.com', 'ipin'); ?>" />
                    </div>
                </div>

                <div class="row-fluid">
                    <div class="span4">
                        <label for="ipin_user_facebook"><?php _e('Facebook Username', 'ipin') ?></label>
                    </div>
                    <div class="span8">
                        <input type="text" name="ipin_user_facebook" id="ipin_user_facebook" value="<?php echo esc_attr($user_info->ipin_user_facebook); ?>" class="input" tabindex="80" placeholder="<?php _e(' my fb', 'ipin'); ?>" />
                    </div>
                </div>

                <div class="row-fluid">
                    <div class="span4">
                        <label for="ipin_user_twitter"><?php _e('Twitter Username', 'ipin') ?></label>
                    </div>
                    <div class="span8">
				<input type="text" name="ipin_user_twitter" id="ipin_user_twitter" value="<?php echo esc_attr($user_info->ipin_user_twitter); ?>" class="input" tabindex="90" placeholder="<?php _e('my twitter', 'ipin'); ?>" />
                    </div>
                </div>

                <div class="row-fluid">
                    <div class="span4">
                        <label for="ipin_user_pinterest"><?php _e('Pinterest Username', 'ipin') ?></label>
                    </div>
                    <div class="span8">
                        <input type="text" name="ipin_user_pinterest" id="ipin_user_pinterest" value="<?php echo esc_attr($user_info->ipin_user_pinterest); ?>" class="input" tabindex="90" placeholder="<?php _e('my pinterest', 'ipin'); ?>" />
                    </div>
                </div>

                <div class="row-fluid">
                    <div class="span4">
                        <label for="ipin_user_googleplus"><?php _e('Google+ ID', 'ipin') ?></label>
                    </div>
                    <div class="span8">
                        <input type="text" name="ipin_user_googleplus" id="ipin_user_googleplus" value="<?php echo esc_attr($user_info->ipin_user_googleplus); ?>" class="input" tabindex="100"  placeholder="<?php _e('+my google or 123456789', 'ipin'); ?>" />
                    </div>
                </div>

                <div class="row-fluid">
                    <div class="span4">
                        <label for="ipin_user_avatar"><?php _e('Avatar', 'ipin') ?></label>
                    </div>
                    <div class="span8">
                        <input type="file" name="ipin_user_avatar" id="ipin_user_avatar" accept="image/*" tabindex="110" />

                        <?php if ($user_info->ipin_user_avatar != '') {
                        $imgsrc = wp_get_attachment_image_src($user_info->ipin_user_avatar,'thumbnail');
                        ?>
                        <div id="avatar-wrapper">
                            <img src="<?php echo $imgsrc[0]; ?>" alt="avatar" class="img-polaroid" />
                            <button id="avatar-delete" class="btn btn-mini btn-danger" data-id="<?php echo $user_info->ID; ?>"><i class="fa fa-remove"></i></button>
                        </div>

                        <img class="ajax-loader-delete-avatar hide" src="<?php echo get_template_directory_uri(); ?>/img/ajax-loader-2.gif" />
                        <?php } ?>

                    </div>
                </div>


				<br />
				<h2><?php _e('Email Notifications', 'ipin'); ?></h2>

                <br />
                <div class="notification-options">
                    <input type="checkbox" id="ipin_user_notify_likes" name="ipin_user_notify_likes" value="1" tabindex="120"<?php if (get_user_meta($user_info->ID, 'ipin_user_notify_likes', true) == '1') echo ' checked="checked"'; ?>>
                    <label for="ipin_user_notify_likes"><?php _e('Notify when someone likes my story', 'ipin') ?></label>

                    <br />
                    <input type="checkbox" id="ipin_user_notify_repins" name="ipin_user_notify_repins" value="1" tabindex="130"<?php if (get_user_meta($user_info->ID, 'ipin_user_notify_repins', true) == '1') echo ' checked="checked"'; ?>>
                    <label for="ipin_user_notify_repins"><?php _e('Notify me when my story was shared', 'ipin') ?></label>

                    <br />
                    <input type="checkbox" id="ipin_user_notify_follows" name="ipin_user_notify_follows" value="1" tabindex="140"<?php if (get_user_meta($user_info->ID, 'ipin_user_notify_follows', true) == '1') echo ' checked="checked"'; ?>>
                    <label for="ipin_user_notify_follows"><?php _e('Notify me when someone follows me', 'ipin') ?></label>

                    <br />
                    <input type="checkbox" id="ipin_user_notify_comments" name="ipin_user_notify_comments" value="1" tabindex="150"<?php if (get_user_meta($user_info->ID, 'ipin_user_notify_comments', true) == '1') echo ' checked="checked"'; ?>>
                    <label for="ipin_user_notify_comments"><?php _e('Notify me when someone comments on my story', 'ipin') ?></label>
                </div>

				<br />
				<input type="hidden" name="user_login" id="user_login" value="<?php echo esc_attr($user_info->user_login); ?>" />
				<input type="hidden" name="form_name" id="form_name" value="save_settings_form" />
				<input type="submit" class="btn btn-large btn-danger" name="wp-submit" id="wp-submit" value="<?php _e('Save Settings', 'ipin'); ?>" tabindex="200" />
			</form>
		</div>

		<div class="span4"></div>
	</div>

	<div id="scrolltotop"><a href="#"><i class="fa fa-chevron-up"></i><br /><?php _e('Top', 'ipin'); ?></a></div>
</div>

<?php get_footer(); ?>
