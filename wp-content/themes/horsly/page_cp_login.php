<?php
/*
Template Name: _login
*/

if (is_user_logged_in()) { wp_redirect(home_url()); exit; }

get_header();
?>
<div class="container-fluid">
	<div class="row-fluid">

		<div class="span4"></div>

		<div class="span4 usercp-wrapper hide">
			<h1><?php _e('Login', 'ipin') ?></h1>
			
			<?php 
			if (function_exists('wsl_activate')) {
				do_action('wordpress_social_login');
				echo '<hr style="border-top: 2px solid #e5e5e5;border-bottom: 2px solid #fafafa;" />';
			}
			?>

			<?php if ($_GET['pw'] == 'reset') {   ?>
				<div class="error-msg-incorrect"><div class="alert alert-success"><strong><?php _e('Your password has been reset.', 'ipin'); ?></strong></div></div>
			<?php } else if ($_GET['registration'] == 'disabled') {   ?>
				<div class="error-msg-incorrect"><div class="alert"><strong><?php _e('User registration is currently not allowed.', 'ipin'); ?></strong></div></div>
			<?php } else if ($_GET['registration'] == 'done' ) {   ?>
				<div class="error-msg-incorrect"><div class="alert alert-success"><strong><?php _e('To activate account, please check your email for verification link.', 'ipin'); ?></strong></div></div>
			<?php } else if ($_GET['email'] == 'unverified' ) {   ?>
				<div class="error-msg-incorrect"><div class="alert"><strong><?php _e('Account not activated yet. Please check your email for verification link.', 'ipin'); ?></strong></div></div>
			<?php } else if ($_GET['email'] == 'verify') {
				$user = get_user_by('login', $_GET['login']);
				$key = get_user_meta($user->ID, '_Verify Email', true);
				if ($key == $_GET['key']) {
					delete_user_meta($user->ID, '_Verify Email', $key);
				?>
				<div class="error-msg-incorrect"><div class="alert alert-success"><strong><?php _e('Verification success. You may login now.', 'ipin'); ?></strong></div></div>
				<?php } else { ?>
				<div class="error-msg-incorrect"><div class="alert"><strong><?php _e('Invalid verification key', 'ipin'); ?></strong></div></div>
			<?php }
			} else if ($_GET['login'] == 'failed') {echo $_POST['errors']; ?>
				<div class="error-msg-incorrect"><div class="alert"><strong><?php _e('Incorrect Username or Password', 'ipin'); ?></strong></div></div>
			<?php } ?>

			<div class="error-msg-blank"></div>
			
			<form name="loginform" id="loginform" action="<?php echo site_url('/wp-login.php'); ?>" method="post">
				<label><?php _e('Username', 'ipin'); ?><br />
				<input type="text" name="log" id="log" value="" tabindex="10" /></label>

				<label><?php _e('Password', 'ipin'); ?><br />
				<input type="password" name="pwd" id="pwd" value="" tabindex="20" /></label>

				<br />
				<input type="hidden" name="rememberme" id="rememberme" value="forever" />
				<input type="hidden" name="redirect_to" id="redirect_to" value="<?php if ($_GET['redirect_to']) { echo esc_attr($_GET['redirect_to']); } else { echo esc_attr(home_url()); } ?>" />
				<input type="hidden" name="icookie" id="icookie" value="1" />
				<input type="submit" class="btn btn-large btn-primary" name="wp-submit" id="wp-submit" value="<?php _e('Login', 'ipin'); ?>" tabindex="30" />

				<br /><br />
				<p class="lostpassword">
				<a href="<?php echo home_url('/login-lpw/'); ?>"><?php _e('Lost your password?', 'ipin'); ?></a>
				</p>
			</form>
		</div>

		<div class="span4"></div>
	</div>

	<div id="scrolltotop"><a href="#"><i class="icon-chevron-up"></i><br /><?php _e('Top', 'ipin'); ?></a></div>
</div>

<script>
jQuery(document).ready(function($) {
	$('.usercp-wrapper').show();
	$('#log').focus();
});
</script>

<?php get_footer(); ?>