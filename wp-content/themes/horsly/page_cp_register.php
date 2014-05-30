<?php
/*
Template Name: _register
*/

if (is_user_logged_in()) { wp_redirect(home_url()); exit; }

if ( !get_option('users_can_register') ) {
	wp_redirect( home_url('/login/?registration=disabled') );
	exit();
}

$http_post = ('POST' == $_SERVER['REQUEST_METHOD']);
$user_login = '';
$user_email = '';
if ($http_post) {
	$user_login = $_POST['user_login'];
	$user_email = $_POST['user_email'];
	$errors = es_register_new_user($user_login, $user_email);
	if (!is_wp_error($errors)) {
		$redirect_to = home_url('/login/?registration=done');
		wp_safe_redirect( $redirect_to );
		exit();
	}
}

//function from wp-login.php
function es_register_new_user( $user_login, $user_email ) {
	$errors = new WP_Error();

	$sanitized_user_login = sanitize_user( $user_login );
	$user_email = apply_filters( 'user_registration_email', $user_email );

	// Check the username
	if ( $sanitized_user_login == '' ) {
		$errors->add( 'empty_username', __( '<strong>ERROR</strong>: Please enter a username.', 'ipin' ) );
	} elseif ( ! validate_username( $user_login ) ) {
		$errors->add( 'invalid_username', __( '<strong>ERROR</strong>: This username is invalid because it uses spaces or/and illegal characters. Please enter a valid username.', 'ipin' ) );
		$sanitized_user_login = '';
	} elseif ( username_exists( $sanitized_user_login ) ) {
		$errors->add( 'username_exists', __( '<strong>ERROR</strong>: This username is already registered. Please choose another one.', 'ipin' ) );
	}

	// Check the e-mail address
	if ( $user_email == '' ) {
		$errors->add( 'empty_email', __( '<strong>ERROR</strong>: Please type your e-mail address.', 'ipin' ) );
	} elseif ( ! is_email( $user_email ) ) {
		$errors->add( 'invalid_email', __( '<strong>ERROR</strong>: The email address isn&#8217;t correct.', 'ipin' ) );
		$user_email = '';
	} elseif ( email_exists( $user_email ) ) {
		$errors->add( 'email_exists', __( '<strong>ERROR</strong>: This email is already registered, please choose another one.', 'ipin' ) );
	}
	
	//edited: added to check the passwords
	if ( $_POST['pass1'] == '' ) {
		$errors = new WP_Error('password_blank', __('Password cannot be blank.', 'ipin', 'ipin'));
	}
	if ( strlen( $_POST['pass1'] ) < 6 ) {
	$errors->add('password_too_short', "<strong>ERROR</strong>: Passwords must be at least 6 characters long", 'ipin');
	}
	if ( isset($_POST['pass1']) && $_POST['pass1'] != $_POST['pass2'] ) {
		$errors = new WP_Error('password_reset_mismatch', __('The passwords do not match.', 'ipin'));
	}
	
	//edited: check if is spam user
	if (trim($_POST['anti-spam']) != date('Y') || empty($_POST['anti-spam']) || !empty( $_POST['anti-spam-2'])) {
		$errors = new WP_Error('password_reset_mismatch', __('Antispam field is incorrect.', 'ipin'));
	}

	do_action( 'register_post', $sanitized_user_login, $user_email, $errors );

	$errors = apply_filters( 'registration_errors', $errors, $sanitized_user_login, $user_email );

	if ( $errors->get_error_code() )
		return $errors;

	//$user_pass = wp_generate_password( 12, false); //edited: dun generate password
	$user_pass = trim($_POST['pass1']);
	$user_id = wp_create_user( $sanitized_user_login, $user_pass, $user_email );
	
	if ( ! $user_id ) {
		$errors->add( 'registerfail', sprintf( __( '<strong>ERROR</strong>: Couldn&#8217;t register you... please contact the <a href="mailto:%s">webmaster</a> !', 'ipin' ), get_option( 'admin_email' ) ) );
		return $errors;
	}
	
	//update_user_option( $user_id, 'default_password_nag', true, true ); //Set up the Password change nag. //edited: dun nag

	//wp_new_user_notification( $user_id, $user_pass ); //edited: dun notify
	
	//function adapted from wp-includes/pluggable/wp_new_user_notification()
	$mask_password = str_pad(substr($user_pass,-3), strlen($user_pass), '*', STR_PAD_LEFT); //edited: mask paswword

	//add user meta to verify email
	$verify_email = wp_generate_password(20, false);
	update_user_meta($user_id, '_Verify Email', $verify_email);
	
	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

	$message  = sprintf(__('Thank you for registering with %s.', 'ipin'), $blogname) . "\r\n\r\n";
	$message .= sprintf(__('Username: %s', 'ipin'), $sanitized_user_login) . "\r\n";
	$message .= sprintf(__('Password: %s', 'ipin'), $mask_password) . "\r\n\r\n";
	$message .= __('Please click the link to verify your email:', 'ipin') . "\r\n";
	$message .= wp_login_url();
	$message .= sprintf('&email=verify&login=%s&key=%s', $sanitized_user_login, $verify_email);

	wp_mail($user_email, sprintf(__('[%s] Account Registration', 'ipin'), $blogname), $message);

	return $user_id;
}

get_header();
?>

<div class="container-fluid">
	<div class="row-fluid">

		<div class="span4"></div>

		<div class="span4 usercp-wrapper">
			<h1><?php _e('Register', 'ipin') ?></h1>
			
			<?php 
			if (function_exists('wsl_activate')) {
				do_action('wordpress_social_login');
				echo '<hr style="border-top: 2px solid #e5e5e5;border-bottom: 2px solid #fafafa;" />';
			}
			?>

			<?php if (is_wp_error($errors)) {   ?>
				<div class="error-msg"><div class="alert"><strong><?php echo $errors->get_error_message(); ?></strong></div></div>
			<?php } ?>
			
			<form name="registerform" id="registerform" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
				<label><?php _e('Username', 'ipin'); ?><br />
				<input type="text" name="user_login" id="user_login" value="<?php echo esc_attr(stripslashes($user_login)); ?>" tabindex="10" /></label>

				<label><?php _e('Email', 'ipin'); ?><br />
				<input type="email" name="user_email" id="user_email" value="<?php echo esc_attr(stripslashes($user_email)); ?>" tabindex="20" /></label>

				<label for="pass1"><?php _e('Password', 'ipin') ?><br />
				<input type="password" name="pass1" id="pass1" class="input" size="20" value="" autocomplete="off" tabindex="30" /></label>

				<label for="pass2"><?php _e('Confirm Password', 'ipin') ?><br />
				<input type="password" name="pass2" id="pass2" class="input" size="20" value="" autocomplete="off" tabindex="40" /></label>

				<p class="comment-form-anti-spam" style="clear:both;">
					<label for="anti-spam">Current ye@r <span class="required">*</span>
					<input type="hidden" name="anti-spam-0" id="anti-spam-0" value="<?php echo date('Y'); ?>" />
					<input type="text" name="anti-spam" id="anti-spam" size="30" value="" /></label> 
				</p>
				
				<p class="comment-form-anti-spam-2" style="display:none;">
					<label for="anti-spam-2">Leave this field empty<span class="required">*</span>
					<input type="text" name="anti-spam-2" id="anti-spam-2" size="30" value=""/></label> 
				</p>

				<br  />
				<input type="submit" class="btn btn-large btn-primary" name="wp-submit" id="wp-submit" value="<?php _e('Register', 'ipin'); ?>" tabindex="50" />
			</form>
		</div>

		<div class="span4"></div>
	</div>

	<div id="scrolltotop"><a href="#"><i class="fa fa-chevron-up"></i><br /><?php _e('Top', 'ipin'); ?></a></div>
</div>

<script>
jQuery(document).ready(function($) {
	$('.comment-form-anti-spam, .comment-form-anti-spam-2').hide();
	var answer = $('.comment-form-anti-spam input#anti-spam-0').val();
	$('.comment-form-anti-spam input#anti-spam').val(answer);
	$('#user_login').focus();
});
</script>

<?php get_footer(); ?>
