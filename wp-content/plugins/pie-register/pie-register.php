<?php
/*
Plugin Name: Pie Register
Plugin URI: http://genetechsolutions.com/pie-register.html
Description: <strong>WordPress 3.5 + ONLY.</strong> Enhance your Registration form, Custom logo, Password field, Invitation codes, Paypal, Captcha validation, Email verification and more.


Author: Genetech Solutions
Version: 2.0.6
Author URI: http://www.genetechsolutions.com/
			
CHANGELOG
See readme.txt
*/

$dir_path = dirname(__FILE__);

if(!defined('PIEREG_DB_VERSION'))
	define('PIEREG_DB_VERSION','2.0.5');

function pr_licenseKey_errors()
{
	do_action("pr_licenseKey_errors");
}
global $pie_success,$pie_error,$pie_error_msg,$pie_suucess_msg, $pagenow,$action,$profile,$errors;
require_once($dir_path.'/dash_widget.php');
require_once($dir_path.'/classes/base.php');
require_once($dir_path.'/classes/profile_admin.php');
require_once($dir_path.'/classes/profile_front.php');
require_once($dir_path.'/classes/registration_form.php');
require_once($dir_path.'/classes/edit_form.php');
require_once($dir_path.'/widget.php');

if( !class_exists('PieRegister') ){
class PieRegister extends PieReg_Base{
	//public static $instance;
	public static $pieinstance;
	var	$pie_success;
	var	$pie_error;
	var	$pie_error_msg;
	var	$pie_success_msg;	
	
	function __construct()
	{
		self::$pieinstance = $this;
		/***********************/
		parent::__construct();
		global $pagenow,$wp_version,$profile;
		
		$errors = new WP_Error();
		//LOCALIZATION
		#Place your language file in the plugin folder and name it "piereg-{language}.mo"
		#replace {language} with your language value from wp-config.php
		load_textdomain( 'piereg', ABS_PATH_TO_MO_FILE ); // OK
		load_plugin_textdomain( 'piereg', false, dirname(plugin_basename(__FILE__)) . '/lang/');
			
		
		add_action('wp_ajax_get_meta_by_field', array($this,'getMeta'));
		
		
		add_action('wp_ajax_check_username',  array($this,'unique_user' ));
		add_action('wp_ajax_nopriv_check_username',  array($this,'unique_user' ));	
		
		add_action( 'admin_init', array($this,'piereg_register_scripts') );
		#Adding Menus
		add_action( 'admin_menu',  array($this,'AddPanel') );
		
		#plugin page links
		add_filter( 'plugin_action_links' , array($this,'add_action_links'),10,2 );
		
		//Add paypal payment method
		add_action("check_payment_method_paypal", array($this, "check_payment_method_paypal"));
		
		
		//Adding "embed form" button      
		add_action('media_buttons_context', array($this, 'add_pie_form_button'));
		
		if(in_array(basename($_SERVER['PHP_SELF']), array('post.php', 'page.php', 'page-new.php', 'post-new.php'))){
			add_action('admin_footer',  array($this, 'add_pie_form_popup'));
		}
		
		#Adding Short Code Functionality
		add_shortcode( 'pie_register_login',  array($this,'showLoginForm') );	
		add_shortcode( 'pie_register_form',  array($this,'showForm') );		
		add_shortcode( 'pie_register_profile', array($this,'showProfile') );
		add_shortcode( 'pie_register_forgot_password',  array($this,'showForgotPasswordForm') );
		add_shortcode( 'pie_register_renew_account',  array($this,'show_renew_account') );		
		
		
		#Genrate Warnings
		add_action('admin_notices', array($this, 'warnings'),20);
		
		add_action( 'init', array($this,'pie_main') );	
			
		$profile = new Profile_admin();
		add_action('show_user_profile',array($profile,"edit_user_profile"));
		add_action('personal_options_update',array($profile,"updateMyProfile"));
		
		add_action('edit_user_profile',array($profile,"edit_user_profile"));
		add_action('edit_user_profile_update', array($profile,'updateProfile'));	
		
		add_action( 'widgets_init', array($this,'initPieWidget'));
		
		add_action('get_header', array($this,'add_ob_start'));
		//It will redirect the User to the home page if the curren tpage is a alternate login page
		add_filter('get_header', array($this,'checkLoginPage'));
		
		add_action('payment_validation_paypal',	array($this, 'payment_validation_paypal'));
			
		add_action("Add_payment_option",		array($this,"Add_payment_option"));
		add_action("add_payment_method_script", array($this,"add_payment_method_script"));

		add_action("add_select_payment_script",	 array($this,"add_select_payment_script"));
		add_action("get_payment_content_area",	 array($this,"get_payment_content_area"));
		
		//	$this->install_settings();
		
		add_action("show_icon_payment_gateway",	array($this,"show_icon_payment_gateway"));
		add_action("check_enable_social_site_method",	array($this,"check_enable_social_site_method_func"));
		add_action("pr_licenseKey_errors",array($this,"print_Rpr_licenseKey_errors"),30);
		
		add_filter("piereg_messages",array($this,"modify_all_notices"));
		
		
		/*update update_invitation_code form ajax*/
		add_action( 'wp_ajax_pireg_update_invitation_code', array($this,'pireg_update_invitation_code_cb_url' ));
		add_action( 'wp_ajax_nopriv_pireg_update_invitation_code', array($this,'pireg_update_invitation_code_cb_url' ));
		add_action( 'admin_enqueue_scripts' ,array($this,'pie_admin_enqueu_scripts') );
		////FRONT END SCRIPTS
		add_action('wp_enqueue_scripts',array($this,'pie_frontend_enqueu_scripts'));
		add_action('wp_head',array($this,'pie_frontend_ajaxurl'));
		
	}
	
	function piereg_register_scripts(){
		
		wp_register_script('pie_calendarcontrol_js',plugins_url("/js/CalendarControl.js",__FILE__),'jquery','2.0',false);
		wp_register_script('pie_datepicker_js',plugins_url("/js/datepicker.js",__FILE__),'jquery','2.0',false);
		wp_register_script('pie_drag_js',plugins_url("/js/drag.js",__FILE__),'jquery','2.0',false);
		wp_register_script('pie_mCustomScrollbar_js',plugins_url("/js/jquery.mCustomScrollbar.min.js",__FILE__),'jquery','2.0',false);
		wp_register_script('pie_mousewheel_js',plugins_url("/js/jquery.mousewheel.min.js",__FILE__),'jquery','2.0',false);
		wp_register_script('pie_sidr_js',plugins_url("/js/jquery.sidr.min.js",__FILE__),'jquery','2.0',false);
		wp_register_script('pie_validationEngine_js',plugins_url("/js/jquery.validationEngine-en.js",__FILE__),'jquery','2.0',false);
		wp_register_script('pie_phpjs_js',plugins_url("/js/phpjs.js",__FILE__),'jquery','2.0',false);
		wp_register_script('pie_registermain_js',plugins_url("/js/pie-register-main.js",__FILE__),'jquery','2.0',false);
		wp_register_script('pie_password_checker_js',plugins_url("/js/pie_password_checker.js",__FILE__),'jquery','2.0',false);
		wp_register_script('pie_regs_js',plugins_url("/js/pie_regs.js",__FILE__),'jquery','2.0',false);
		wp_register_script('pie_tooltip_js',plugins_url("/js/tooltip.js",__FILE__),'jquery','2.0',false);
		wp_register_script('pie_validation_js',plugins_url("/js/validation.js",__FILE__),'jquery','2.0',false);
		
		/////////////////////////////////////////////////
		wp_register_style( 'pie_jqueryui_css', plugins_url("/css/jquery-ui.css",__FILE__),false,'2.0', "all" );
		wp_register_style( 'pie_calendarcontrol_css', plugins_url("/css/CalendarControl.css",__FILE__),false,'2.0', "all" );
		wp_register_style( 'pie_front_css', plugins_url("/css/front.css",__FILE__),false,'2.0', "all" );
		//wp_register_style( 'pie_', plugins_url("/css/CalendarControl.js",__FILE__),false,'2.0', "all" );
		wp_register_style( 'pie_mCustomScrollbar_css', plugins_url("/css/jquery.mCustomScrollbar.css",__FILE__),false,'2.0', "all" );
		wp_register_style( 'pie_menu_style_css', plugins_url("/css/piereg_menu_style.css",__FILE__),false,'2.0', "all" );
		wp_register_style( 'pie_style_css', plugins_url("/css/style.css",__FILE__),false,'2.0', "all" );
		wp_register_style( 'pie_tooltip_css', plugins_url("/css/tooltip.css",__FILE__),false,'2.0', "all" );
		wp_register_style( 'pie_validation_css', plugins_url("/css/validation.css",__FILE__),false,'2.0', "all" );
	}
	function pie_frontend_enqueu_scripts(){
		wp_enqueue_style( 'pie_jqueryui_css' );
		wp_enqueue_style( 'pie_front_css' );
		wp_enqueue_style( 'pie_validation_css' );
		wp_enqueue_style( 'pie_calendarcontrol_css' );
		
		////////////////////////////////////////////
		wp_deregister_script('jquery-ui-core');
		wp_register_script('jquery-ui-core', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.0/jquery-ui.min.js',array('jquery'),'1.8.0',false);
		wp_enqueue_script( 'jquery-ui-core' );	
		wp_enqueue_script('jquery-ui-datepicker');	
		wp_enqueue_script("pie_validation_js");
		wp_enqueue_script("pie_validationEngine_js");
		wp_enqueue_script("pie_calendarcontrol_js");
		wp_enqueue_script("pie_datepicker_js" );	
		//add_action("wp_head",array($this,"addUrl"));
	}
	function pie_frontend_ajaxurl(){
		?>
		<script type="text/javascript">
        var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
        </script>
        <?php
	}
	function pie_admin_enqueu_scripts(){
		wp_enqueue_style('pie_jqueryui_css');
		wp_enqueue_style('pie_calendarcontrol_css');
		wp_enqueue_style( 'pie_mCustomScrollbar_css' );
		wp_enqueue_style( 'pie_menu_style_css' );
		wp_enqueue_style( 'pie_style_css' );
		wp_enqueue_style('pie_tooltip_css');
		wp_enqueue_style('pie_validation_css');
		
		////////////////////////////////////////////
		
		wp_enqueue_script( 'jquery-ui-core' );	
		wp_enqueue_script('pie_calendarcontrol_js');	
		wp_enqueue_script("pie_datepicker_js");
		wp_enqueue_script("pie_drag_js");
		wp_enqueue_script("pie_mCustomScrollbar_js");
		wp_enqueue_script("pie_mousewheel_js" );
		wp_enqueue_script( 'pie_sidr_js' );	
		wp_enqueue_script('pie_validationEngine_js');	
		wp_enqueue_script("pie_phpjs_js");
		wp_enqueue_script("pie_registermain_js");
		wp_enqueue_script("pie_password_checker_js");
		wp_enqueue_script("pie_regs_js" );
		wp_enqueue_script("pie_tooltip_js" );
		wp_enqueue_script("pie_validation_js" );
	}
	function modify_all_notices($notice)
	{
		$Start_notice = "";/*Write your message*/
		$End_notice = "";/*Write your message*/
		return $Start_notice.$notice.$End_notice;
	}
	
	function print_Rpr_licenseKey_errors()
	{
		return $_POST['PR_license_notice'];
	}
	function initPieWidget()
	{
		register_widget( 'Pie_Register_Widget' );
		register_widget( 'Pie_Login_Widget' );
		register_widget( 'Pie_Forgot_Widget' );	
	}
	
	
	//Plugin Menu Link
	function add_action_links( $links, $file ) 
	{
   		 if ( $file != plugin_basename( __FILE__ ))
            return $links;
		
		$links[] = '<a href="'. get_admin_url(null, 'admin.php?page=pie-general-settings') .'">General Settings</a>';   		
   		return $links;
	}
	function pie_main()
	{
		$pie_plugin_db_version = get_option('piereg_plugin_db_version');
		
		if($pie_plugin_db_version != PIEREG_DB_VERSION){
			$this->install_settings();
		}
		
		$option = get_option( 'pie_register_2' );
		/////////// Register Scripts ////////////
		$this->piereg_register_scripts();
		////////////////////////////////////////
		if(isset($_REQUEST['action']) && $_REQUEST['action'] == "check_username")
			return;
		
		global $pagenow;
		
		if($option['custom_css'] != "")
			add_action('wp_head', array($this,'addCustomCSS'));
		if( $option['tracking_code'] != "")
			add_action('wp_footer', array($this,'addCustomScripts'));
		
		// check to prevent php "notice: undefined index" msg
		$theaction ='';	
		
		if(isset($_GET['pr_preview']) && $_GET['pr_preview']==1) 
		{
			global $errors;		
			$form 		= new Registration_form();
			$success 	= '' ;					
			include_once($this->plugin_dir."/register_form_preview.php");			
			exit;			
		}
		
		if(isset($_GET['action'])) 
			$theaction = $_GET['action']; 
			
			
			
			
		if($theaction == "payment_success")
			$this->payment_success_cancel_after_register("payment=success");
			
		if($theaction == "payment_cancel")
			$this->payment_success_cancel_after_register("payment=cancel");
		
		#Save Settings
		if( isset($_POST['action']) && $_POST['action'] == 'pie_reg_update' )
		{$this->SaveSettings();}
		if(isset($_POST['Remove_license_x']) and ((int)$_POST['Remove_license_x']) != "0" and isset($_POST['Remove_license_y']))
		{$this->Remove_license_Key();}
		
		add_filter('allow_password_reset',array($this,'checkUserAllowedPassReset'),20,2);
		
		#Delete User after grace Period
		//$this->deleteUsers();
		
		#Reset Settings to default
		if( isset($_POST['piereg_default_settings']) )
		{
			$this->piereg_default_settings();
			/*$this->uninstall_settings();
			$this->install_settings();*/
		}
		
		#Admin Verify Users
		if( isset($_POST['verifyit']) )		
			$this->verifyUsers();
			
		#Admin Send Payment Link
		if( isset($_POST['paymentl']) && !(empty($option['paypal_butt_id'])) && $option['enable_paypal']==1)
			$this->PaymentLink();
		
		#Admin Resend VerificatioN Email
		if( isset($_POST['emailverifyit']) )
			$this->AdminEmailValidate() ;		
			
		#Admin Delete Unverified User
		if( isset($_POST['vdeleteit']))			
			$this->AdminDeleteUnvalidated();	
		
		//Blocking wp admin for registered users
		if(($pagenow == 'wp-login.php' && $option['block_wp_login']==1) && ($option['alternate_login']  && $theaction != 'logout'))
		{
			if($theaction=="register")
			{
				wp_redirect($this->get_redirect_url_pie(get_permalink($option['alternate_register'])));
			}
			else if($theaction=="lostpassword")
			{
				wp_redirect($this->get_redirect_url_pie(get_permalink($option['alternate_forgotpass'])));
			}
			else if($theaction=="")
			{
				wp_redirect($this->get_redirect_url_pie(get_permalink($option['alternate_login'])));
			}
			
		}
		
		//Blocking access of users to default pages if redirect is on 
		if((is_user_logged_in() && $pagenow == 'wp-login.php') && ($option['redirect_user']==1   && $theaction != 'logout'))
		{
			$this->afterLoginPage();			
		}
		//
		if(trim($pagenow) == "profile.php" && $option['block_WP_profile']==1 )
		{
			$current_user = wp_get_current_user();
			if(trim($current_user->roles[0]) == "subscriber")
			{
				//$profile_page = get_option("Profile_page_id");
				$profile_page = $option['alternate_profilepage'];
				wp_redirect($this->get_redirect_url_pie(get_permalink($profile_page)));
			}
		}
		
		//Blocking wp admin for registered users
		/*if($option['subscriber_login']==0)
			$this->block_wp_admin();*/
			
		if(isset($_POST['log']) && isset($_POST['pwd']))	
		 	$this->checkLogin();
		//else if(isset($_POST['log']) && isset($_POST['reset_pass']))
			//$this->resetPassword();	
		else if(isset($_POST['pie_submit']))	
			$this->check_register_form();
			
		else if(isset($_POST['pie_renew']))
		{
			$this->renew_account();
		}
		/*else if(isset($_POST['pie_submit_authorize']))	
		{
			do_action('check_register_form_AuthorizeDotNet');
		}*/
		
		// if the user is on the login page, then let the game begin
		if ($pagenow == 'wp-login.php' && $theaction != 'logout'){
			add_action('login_init',array($this,'pieregister_login'),1);
			//$this->pieregister_login();
		}
					
		else if($theaction=="ipn_success")			
			$this->processPostPayment();
			
		//OImport Export Section
		if(isset($_POST['pie_fields_csv']) || isset($_POST['pie_meta_csv']))
			$this->generateCSV();	
		else if(isset($_FILES['csvfile']['name']))			
			$this->importUsers();
			
		if(isset($_POST['pie_form']))
		{
			//This will make sure no one tempers the field from the client side
			$required = array("form","username","email","password","submit");
			$length   = 0;
			foreach($_POST['field'] as $field)
			{
				if(in_array($field['type'],$required))
				$length++;
			}
			if($length==sizeof($required))
			{
				$this->saveFields();
			}
		}
		
		//$this->subscriber_show_admin_bar();
		if(
			isset($_POST['invitaion_code_bulk_option']) and isset($_POST['btn_submit_invitaion_code_bulk_option']) and isset($_POST['select_invitaion_code_bulk_option']) and $_POST['invitaion_code_bulk_option'] != "" and $_POST['btn_submit_invitaion_code_bulk_option'] != ""
		  )
		{
			if(trim($_POST['invitaion_code_bulk_option']) == "delete")
			{
				$this->delete_invitation_codes($_POST['select_invitaion_code_bulk_option']);
			}
			else if(trim($_POST['invitaion_code_bulk_option']) == "active")
			{
				$this->active_or_unactive_invitation_codes($_POST['select_invitaion_code_bulk_option'],"1");
			}
			else if(trim($_POST['invitaion_code_bulk_option']) == "unactive")
			{
				$this->active_or_unactive_invitation_codes($_POST['select_invitaion_code_bulk_option'],"0");
			}
		}
		if(isset($_POST['invitation_code_per_page_items']) and $_POST['invitation_code_per_page_items'] != "")
		{
			$opt = get_option("pie_register_2");
			$val = ((int)($_POST['invitation_code_per_page_items']) != 0)? ((int)$_POST['invitation_code_per_page_items']) : "10";
			$opt['invitaion_codes_pagination_number'] = $val;
			update_option("pie_register_2",$opt);
			unset($opt);
		}
		
		/*if($option['modify_avatars'])
		{
			add_filter('get_avatar',array($this,'add_custom_avatars'),88888);
		}*/
		if($option['show_admin_bar'] == "0")
		{
			$this->subscriber_show_admin_bar();// show/hide admin bar
		}
		if($option['block_wp_login']){
			add_filter( 'login_url', array($this,'pie_login_url'),88888,1);
			add_filter( 'lostpassword_url', array($this,'pie_lostpassword_url'),88888,1);
			add_filter( 'register_url', array($this,'pie_registration_url'),88888,1);
		}
		add_filter( 'piereg_password_reset_not_allowed_text', array($this,'piereg_password_reset_not_allowed_text_function'),20,1);
		
		if(isset($_POST['import_email_template_from_version_1']) and $_POST['old_version_emport'] == "yes")
		{
			$old_options = get_option("pie_register");
			$new_options = get_option("pie_register_2");
			$new_options['user_message_email_admin_verification'] = nl2br($old_options['adminvmsg']);
			$new_options['user_message_email_email_verification'] = nl2br($old_options['emailvmsg']);
			$new_options['user_message_email_default_template'] = nl2br($old_options['msg']);
			update_option("pie_register_2",$new_options);
		}
	}
	function payment_success_cancel_after_register($query_string){
		global $wpdb;
		$option = get_option( 'pie_register_2' );
		$fields 			= maybe_unserialize(get_option("pie_fields"));
		$confirmation_type 	= $fields['submit']['confirmation'];
		
		if($confirmation_type == "page"){
			wp_safe_redirect(get_permalink($fields['submit']['page']));
			exit;
		}elseif($confirmation_type == "redirect"){
			wp_redirect($fields['submit']['redirect_url']);
			exit;
		}elseif($confirmation_type == "text" ){
			wp_safe_redirect($this->pie_modify_custom_url(get_permalink($option['alternate_login']),$query_string));
			exit;
		}
	}
	function get_redirect_url_pie($get_url){
		$get_url = trim($get_url);
		if(!$get_url) return false;
		if($_SERVER['QUERY_STRING']){
			if(strpos($get_url,"?"))
				$url = $get_url."&".$_SERVER['QUERY_STRING'];
			else
				$url = $get_url."?".$_SERVER['QUERY_STRING'];
		}
		else{
			$url = $get_url;
		}
		return $url;
	}
	function subscriber_show_admin_bar()
	{
		global $current_user;
		$current_user->caps = array_keys($current_user->caps);
		$ncaps = count($current_user->caps);
		$role = $current_user->caps[$ncaps - 1];
		if( trim(strtolower($role)) == "subscriber" )
		{
			show_admin_bar( false );
		}
		unset($current_user);
	}
	
	//"Insert Form" button to the post/page edit screen
    function add_pie_form_button($context)
	{
        $is_post_edit_page = in_array(basename($_SERVER['PHP_SELF']), array('post.php', 'page.php', 'page-new.php', 'post-new.php'));
        if(!$is_post_edit_page)
            return $context;

        
        $out = '<a href="#TB_inline?width=480&inlineId=select_pie_form" class="thickbox" id="add_pie_form" title="' . __("Add Pie Register Form", 'piereg') . '"><img src="'.get_bloginfo('url').'/wp-content/plugins/pie-register/images/form-icon.png" alt="' . __("Add Pie Register Form", 'piereg') . '" /></a>';
        return $context . $out;
    }
	function checkLoginPage()
	{
		$option 		= get_option('pie_register_2');	
		$current_page	= get_the_ID();
		if($option['block_wp_login']==1 && $option['alternate_login'] > 0 && is_user_logged_in() && $current_page == $option['alternate_login'] )
		{	
			
			$this->afterLoginPage();			
		}
	}
	function add_pie_form_popup()
	{
		 ?>
          <script type="text/javascript">
            function addForm(){
                var form_id = jQuery("#pie_forms").val();
                if(form_id == ""){
                    alert("<?php _e("Please select a form", "piereg") ?>");
                    return;
                }

               

                window.send_to_editor(form_id);
            }
        </script>
		 <div id="select_pie_form" style="display:none;">
          	<div >
                <div>
                    <div style="padding:15px 15px 0 15px;">
                        <h3 style="color:#5A5A5A!important; font-family:Georgia,Times New Roman,Times,serif!important; font-size:1.8em!important; font-weight:normal!important;"><?php _e("Select A Form", "piereg"); ?></h3>
                        <span>
                            <?php _e("Select a form below to add it to your post or page.", "piereg"); ?>
                        </span>
                    </div>
                    <div style="padding:15px 15px 0 15px;">
                        <select id="pie_forms">
                            
                            <option value="[pie_register_form]"><?php _e("Registration Form","piereg") ?></option>
                            <option value="[pie_register_login]"><?php _e("Login Form","piereg") ?></option>
                            <option value="[pie_register_forgot_password]"><?php _e("Forgot Password Form","piereg") ?></option>
                            
                        </select> <br/>
                        
                    </div>
                    
                    <div style="padding:15px;">
                        <input type="button" class="button-primary" value="Insert Form" onclick="addForm();"/>&nbsp;&nbsp;&nbsp;
                    <a class="button" style="color:#bbb;" href="#" onclick="tb_remove(); return false;"><?php _e("Cancel", "piereg"); ?></a>
                    </div>
                </div>
            </div>  
        </div>	
	<?php
    }
	function getMeta()
	{
		$meta =  get_option( 'pie_fields_meta');
		$meta = $meta[$_POST['field_type']];
		$meta = str_replace("%d%",$_POST['id'],$meta);	
		$meta .= '<input value = "'.$_POST['field_type'].'" type="hidden" class="input_fields" name="field['.$_POST['id'].'][type]" id="type_'.$_POST['id'].'">';		
		
		echo $meta;
		die();	
	}
	
	function process_login_form(){
		get_header();
		//ob_start();
		include_once("login_form.php");
		$output = pieOutputLoginForm();
		echo $output;
		//ob_end_flush();
		//include_once("login_form.php");
		get_footer();
		exit;
	}
	function checkLogin()
	{
		global $errors, $wp_session;
		$errors = new WP_Error();			
		if(empty($_POST['log']) || empty($_POST['pwd']))
		{
			$errors->add('login-error',apply_filters("piereg_Invalid_username_or_password",__('Invalid username or password.','piereg')));
		}
		else
		{
			$creds = array();
			$creds['user_login'] 	= $_POST['log'];
			$creds['user_password'] = $_POST['pwd'];
			$creds['remember'] 		= $_POST['rememberme'];
			if(isset($_POST['social_site']) and $_POST['social_site'] == "true" )
			{
				require_once( ABSPATH . WPINC . '/user.php' );
				require_once( ABSPATH . WPINC . '/pluggable.php' );
				wp_set_auth_cookie($_POST['user_id_social_site']);
				$user = get_userdata($_POST['user_id_social_site']);
			}
			else
			{
				$user = wp_signon( $creds, false );
			}
			//$this->check_user_activation();
			if ( is_wp_error($user))
			{
				$user_login_error = $user->get_error_message();
				if(strpos(strip_tags($user_login_error),'Invalid username',5) > 6)
				{
					$user_login_error = apply_filters('pie_invalid_username_password_msg_txt','<strong>'.ucwords(__("error","piereg")).'</strong>: '.__("Invalid username","piereg").'. <a href="'.$this->pie_lostpassword_url().'" title="'.__("Password Lost and Found","piereg").'">'.__("Lost your password?","piereg").'</a>');
				}else if(strpos(strip_tags($user_login_error),'password you entered',9) > 10)
				{
					$user_login_error = apply_filters('pie_invalid_user_password_msg_txt','<strong>'.ucwords(__("error","piereg")).'</strong>: '.__("The password you entered for the username","piereg").' <strong>'.$_POST['log'].'</strong> '.__("is incorrect","piereg").'. <a href="'.$this->pie_lostpassword_url().'" title="'.__("Password Lost and Found","piereg").'">'.__("Lost your password?","piereg").'</a>');
				}
				$errors->add('login-error',apply_filters("piereg_login_error",$user_login_error));
			}
			else
			{
				if($user->roles[0]=="administrator")
				{
					//wp_redirect($this->get_redirect_url_pie(admin_url()));
					wp_redirect(admin_url());
					exit;
				}
				else
				{
					$active = get_user_meta($user->ID,"active");
					if(!$this->deleteUsers($user->ID,$user->user_email,$user->user_registered)){
						
						//Delete User after grace Period
						
						if($active[0] != 1)//If not active
						{
							
							wp_logout();
							
							$errors->add('login-error',apply_filters("piereg_your_account_is_not_activated",__('Your account is not activated!.','piereg')));
						}else{
							//apply_filters('get_avatar',array($this,'custom_avatars'),$user->ID,"29");
							$this->afterLoginPage();
							exit;
						}
					}
				}
			}	
		}
			
	}
	/*function check_user_activation($user_id)1
	{
		global $wpdb;
		$result = $wpdb->get_result("SELECT `meta_key`,`meta_value` FROM `wp_usermeta` WHERE `user_id` = "$user_id" and `meta_key` = 'active'");
		//
	}*/
	//Add the Settings and User Panels
	function AddPanel()
	{ 
		$update = get_option( 'pie_register_2' );
				
		$pie_page_suffix_1 = add_object_page( "Pie Register", 'Pie Register', 10, 'pie-register',  array($this,'RegPlusEditForm'), plugins_url("/images/pr_icon.png",__FILE__) );	
		
		//add_action('admin_print_scripts-' . $pie_page_suffix_1, 'my_plugin_admin_scripts');
		
		$pie_page_suffix_2 = add_submenu_page( 'pie-register', 'Form Editor', 'Form Editor', 10, 'pie-register', array($this, 'RegPlusEditForm') );		
		
		
		$pie_page_suffix_3 = add_submenu_page( 'pie-register', 'General Settings', 'General Settings', 10, 'pie-general-settings', array($this, 'PieGeneralSettings') );		
		
		$pie_page_suffix_4 = add_submenu_page( 'pie-register', 'Payment Gateway Settings', 'Payment Gateway', 10, 'pie-gateway-settings', array($this, 'PieRegPaymentGateway') );
		
		$pie_page_suffix_5 = add_submenu_page( 'pie-register', 'Email Notification Settings', 'Admin Notifications', 10, 'pie-admin-notification', array($this, 'PieRegAdminNotification') );		
		
		$pie_page_suffix_6 = add_submenu_page( 'pie-register', 'Email Notification Settings', 'User Notifications', 10, 'pie-user-notification', array($this, 'PieRegUserNotification') );		
		
		$pie_page_suffix_7 = add_submenu_page( 'pie-register', 'Export/Import', 'Export/Import', 10, 'pie-import-export', array($this, 'PieRegImportExport'));		
		
		$pie_page_suffix_8 = add_submenu_page( 'pie-register', 'Invitation Codes', 'Invitation Codes', 10, 'pie-invitation-codes', array($this, 'PieRegInvitationCodes'));		
		
		// help page
		$pie_page_suffix_9 = add_submenu_page( 'pie-register', 'Help', 'Help', 10, 'pie-help', array($this, 'PieRegHelp'));		
		
		if( $update['verification'] == 1 || $update['verification'] == 2 )
		add_users_page( 'Unverified Users', 'Unverified Users', 10, 'unverified-users', array($this, 'Unverified') );
		
		do_action('pie_register_add_menu');
		
	}
	
	function block_wp_admin() 
	{
		if (strpos(strtolower($_SERVER['REQUEST_URI']),'/wp-admin/') !== false) 
		{
			if ( !current_user_can( 'manage_options' ) ) 
			{
				wp_redirect($this->get_redirect_url_pie(get_option('siteurl')),302);
			}
		}	
	}
	//deprecated
	
	function saveFields()
	{
		
		foreach($_POST['field'] as $k=>$fv){
			if($fv['type'] == 'html')
			$fv['html'] = htmlentities(stripslashes($fv['html']), ENT_QUOTES | ENT_IGNORE, "UTF-8");
			
			$updated_post[$k] = $fv;
		}
		
		if(!$_POST['field'])
				$_POST['field'] =  get_option( 'pie_fields_default' );
	
		do_action("pie_fields_save");
		//update_option("pie_fields",serialize($_POST['field']));
		update_option("pie_fields",serialize($updated_post));
		$options = get_option("pie_register_2");
		$options['pie_regis_set_user_role_'] = $_POST['set_user_role_'];
		update_option("pie_register_2",$options);
		//wp_redirect("admin.php?page=pie-register");
	}
	//Opening Form Editor
	function RegPlusEditForm()
	{ 		
		$data 	= $this->getCurrentFields();
		if(!is_array($data) || sizeof($data) == 0)
		{
			$data 	= get_option( 'pie_fields_default' );	
		}
		
		require_once($this->plugin_dir.'/menus/PieRegEditForm.php');		
	}
	
	function addCustomCSS()
	{
		$option = get_option( 'pie_register_2' );
			
		if($option['custom_css'] != "")
		{
			echo '<style>'.$option['custom_css'].'</style>';
		}
		
	}
	function addCustomScripts()
	{
		$option = get_option( 'pie_register_2' );
			
		if($option['tracking_code'] != "")
		{
			echo stripslashes($option['tracking_code']);
		}
	}
	function pieregister_login()
	{
		$option = get_option( 'pie_register_2' );
		if($option['allow_pr_edit_wplogin'] == 1){
			
			global $errors;
			if (isset($_REQUEST['action'])) :
				$action = $_REQUEST['action'];
			else :
				$action = 'login';
			endif;
			
			switch($action) :
				case 'lostpassword' :
				case 'retrievepassword' :
					$this->process_lostpassword();
				break;
				case 'resetpass' :
				case 'rp' :
					$this->process_getpassword();
				break;	
				case 'register':
				$this->process_register_form();		
				case 'login':
				default:
					$this->process_login_form();
				break;
			endswitch;	
			exit;
		}
		return false;
	}
	//deprecated
	/*function addUrl()	
	{
		
		?><script type="text/javascript">
var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
</script><?php
	}*/
	function process_register_form()
	{
		global $errors;
		
		$form 		= new Registration_form();
		$success 	= '' ;	
		
		//$this->pie_frontend_enqueu_scripts();		
			
		get_header();
		include_once("register_form.php");
		//Printing Success Message
		echo outputRegForm();
		get_footer();	
		
		exit;
	}
	/*public function check_enable_payment_method()// only check any payment method enable or not.
	{
		$pie_reg = get_option('pie_register_2');
		if(
			($pie_reg['enable_authorize_net'] 	== 1 and trim($pie_reg['piereg_authorize_net_api_id']) 	!= "") or
			($pie_reg['enable_2checkout'] 		== 1 and trim($pie_reg['piereg_2checkout_api_id']) 		!= "") or
			($pie_reg['enable_paypal'] 			== 1 and trim($pie_reg['paypal_butt_id'])				!= "")
		  )
		{
			return "true";
		}
		else
		{
			return "false";
		}
	}*/
	function check_register_form()
	{
		/*if(isset($_POST['select_payment_method']) and trim($_POST['select_payment_method']) != "" )
		{
			do_action('payment_validation_'.$_POST['select_payment_method']);//Validate payment method Like check_payment_method_paypal
		}*/
		global $errors, $wp_session;
		if(($this->check_enable_payment_method()) == "false")
		{
			$this->pie_save_registration();
		}
		else if(($this->check_enable_payment_method()) == "true")
		{
			if(isset($_POST['select_payment_method']) and trim($_POST['select_payment_method']) != "" and $_POST['select_payment_method'] != "select")
			{
				$this->pie_save_registration();
			}
			else{
				$_POST['error'] = __("Please select any payment method","piereg");
			}
		}
		else if(trim($wp_session['payment_error']) != "")
		{
			$_POST['error'] = __($wp_session['payment_error'],"piereg");
			$wp_session['payment_error'] = "";
			$wp_session['payment_sussess'] = "";
		}
	}
	function pie_save_registration()
	{
		add_filter('wp_mail_content_type', array($this,'set_html_content_type'));
		global $errors;
		$form 		= new Registration_form();
		$errors 	= $form->validateRegistration($errors);
		$option 	= get_option( 'pie_register_2' );
		//If Registration doesn't have errors
		
		if(sizeof($errors->errors) == 0)
		{
				
			do_action('pie_register_after_register_validate');				 
			//Inserting User
			$pass = $_POST['password'];
			$user_data = array('user_pass' => $pass,'user_login' => $_POST['username'],'user_email' => $_POST['e_mail'],'role' => get_option('default_role'));
			if(isset($_POST['url']))
			{
				$user_data["user_url"] =  $_POST['url'];	 
			}
			
			$user_id = wp_insert_user( $user_data );
			
			$form->addUser($user_id);
			$new_role = 'subscriber';
			if(isset($option['pie_regis_set_user_role_']) and trim($option['pie_regis_set_user_role_']) != "")
			{
				$new_role = strtolower($option['pie_regis_set_user_role_']);
			}
			//// update user role using wordpress function
			wp_update_user( array ('ID' => $user_id, 'role' => $new_role ) ) ;
			add_user_meta( $user_id, "is_social", "false", $unique = false );
			add_user_meta( $user_id, "social_site_name", "", $unique = false );
			$user 		= new WP_User($user_id);
			do_action('pie_register_after_register_validate',$user);
			////////////////////////////////////////////////////
			/******** Admin Notification *******/
			$message  		= $form->filterEmail($option['admin_message_email'],$user,$pass);	
			$subject		= $option['admin_subject_email'];
			$to				= $option['admin_sendto_email'];
			$from_name		= $option['admin_from_name'];
			$from_email		= $option['admin_from_email'];
			$bcc			= $option['admin_bcc_email'];
			$reply_to_email	= $option['admin_to_email'];
			
			if(!filter_var($to,FILTER_VALIDATE_EMAIL))//if not valid email address then use wordpress default admin
			{
				$to = get_option('admin_email');
			}
			
			//Headers
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			
			if(!empty($from_email) && filter_var($from_email,FILTER_VALIDATE_EMAIL))//Validating From
			$headers .= "From: ".$from_name." <".$from_email."> \r\n";
			
			if(!empty($bcc) && filter_var($bcc,FILTER_VALIDATE_EMAIL))//Validating BCC
			 $headers .= "Bcc: " . $bcc . " \r\n";
			
			if(!empty($reply_to_email) && filter_var($reply_to_email,FILTER_VALIDATE_EMAIL))//Validating Reply To
			$headers .= 'Reply-To: <'.$reply_to_email.'> \r\n';		
				

			@wp_mail($to,$subject, $message,$headers);
			////////////////////////////////////////////////////
			/*if(isset($_POST['select_payment_method']) and ($_POST['select_payment_method'] != "" or $_POST['select_payment_method'] != "select"))//Goto payment method Like check_payment_method_paypal
			{
				$_POST['user_id'] = $user_id;
				update_user_meta( $user_id, 'active', 0);
				do_action("check_payment_method_".$_POST['select_payment_method']);// function prefix check_payment_method_
			}
			else if(($this->check_enable_payment_method()) == "true" )
			{
				if( (($this->check_enable_payment_method()) == "true" and !isset($_POST['select_payment_method']) ) or
				 	( isset($_POST['select_payment_method']) and $_POST['select_payment_method'] == "" or $_POST['select_payment_method'] == "select")
				  )
				{
					$_POST['error'] = __("please select any payment method","piereg");
				}
			}
			else */
			if(!(empty($option['paypal_butt_id'])) && $option['enable_paypal']==1)
			{
				$_POST['user_id'] = $user_id;
				update_user_meta( $user_id, 'active', 0);
				do_action("check_payment_method_paypal");// function prefix check_payment_method_
			}
			else if($option['verification'] == 1 )//Admin Verification
			{
				update_user_meta( $user_id, 'active', 0);
				update_user_meta( $user_id, 'register_type', "admin_verify");
				$subject 		= $option['user_subject_email_admin_verification'];				
				$message		= $form->filterEmail($option['user_message_email_admin_verification'],$user, $pass );
				$from_name		= $option['user_from_name_admin_verification'];
				$from_email		= $option['user_from_email_admin_verification'];					
				$reply_email 	= $option['user_to_email_admin_verification'];
						
				//Headers
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			
				if(!empty($from_email) && filter_var($from_email,FILTER_VALIDATE_EMAIL))//Validating From
				$headers .= "From: ".$from_name." <".$from_email."> \r\n";
				if($reply_email){
					$headers .= "Reply-To: {$reply_email}\r\n";
					$headers .= "Return-Path: {$from_name}\r\n";
				}else{
					$headers .= "Reply-To: {$from_email}\r\n";
					$headers .= "Return-Path: {$from_email}\r\n";
				}
	
							
				wp_mail($_POST['e_mail'], $subject, $message , $header);
				$_POST['success'] = apply_filters("piereg_thank_you_for_your_registration",__("Thank you for your registration. You will be notified once the admin approves your account.",'piereg'));	
			
			}
			else if($option['verification'] == 2 )//E-Mail Link Verification
			{
				update_user_meta( $user_id, 'active', 0);
				$hash = md5( time() );
				update_user_meta( $user_id, 'hash', $hash );
				update_user_meta( $user_id, 'register_type', "email_verify");
				
				$subject 		= $option['user_subject_email_email_verification'];				
				$message		= $form->filterEmail($option['user_message_email_email_verification'],$user, $pass );
				$from_name		= $option['user_from_name_email_verification'];
				$from_email		= $option['user_from_email_email_verification'];					
				$reply_email 	= $option['user_to_email_default_template'];
						
				//Headers
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			
				if(!empty($from_email) && filter_var($from_email,FILTER_VALIDATE_EMAIL))//Validating From
				$headers .= "From: ".$from_name." <".$from_email."> \r\n";
				if($reply_email){
					$headers .= "Reply-To: {$reply_email}\r\n";
					$headers .= "Return-Path: {$from_name}\r\n";
				}else{
					$headers .= "Reply-To: {$from_email}\r\n";
					$headers .= "Return-Path: {$from_email}\r\n";
				}
	
							
				wp_mail($_POST['e_mail'], $subject, $message , $header);
				
				$_POST['success'] = apply_filters("piereg_thank_you_for_your_registration",__("Thank you for your registration. An activation link with your password has been sent to you.",'piereg'));
					
			}
			else if($option['verification'] == 0 ){
				update_user_meta( $user_id, 'active', 1);
				/************ User Notification **************/
				//update_user_meta( $user_id, 'active', 1);
				
				$subject 		= $option['user_subject_email_default_template'];				
				$message		= $form->filterEmail($option['user_message_email_default_template'],$user, $pass );
				$from_name		= $option['user_from_name_default_template'];
				$from_email		= $option['user_from_email_default_template'];					
				$reply_email 	= $option['user_to_email_default_template'];
						
				//Headers
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			
				if(!empty($from_email) && filter_var($from_email,FILTER_VALIDATE_EMAIL))//Validating From
				$headers .= "From: ".$from_name." <".$from_email."> \r\n";
				if($reply_email){
					$headers .= "Reply-To: {$reply_email}\r\n";
					$headers .= "Return-Path: {$from_name}\r\n";
				}else{
					$headers .= "Reply-To: {$from_email}\r\n";
					$headers .= "Return-Path: {$from_email}\r\n";
				}
					
						
				wp_mail($_POST['e_mail'], $subject, $message , $headers);
			}
			
			do_action('pie_register_after_register',$user);
			
			$fields 			= maybe_unserialize(get_option("pie_fields"));
			$confirmation_type 	= $fields['submit']['confirmation'];
			
			if(trim($wp_session['payment_error']) != "")
			{
				$_POST['error'] = __($wp_session['payment_error'],"piereg");
				$wp_session['payment_error'] = "";
				$wp_session['payment_sussess'] = "";
			}
			else if(trim($wp_session['payment_sussess']) != "")
			{
				$_POST['success'] = apply_filters("piereg_payment_sussess",__($wp_session['payment_sussess'],"piereg"));
				$wp_session['payment_error'] = "";
				$wp_session['payment_sussess'] = "";
			}
			else if($confirmation_type == "" || $confirmation_type== "text" )
			{
				$_POST['success']	= __($fields['submit']['message'],"piereg");
			}
			else if($confirmation_type== "page")
			{
				/*?>
                <script type="text/javascript" language="javascript">
					location.replace("<?php echo get_permalink($fields['submit']['page']); ?>");
				</script>
                <?php*/
				wp_redirect(get_permalink($fields['submit']['page']));
			}
			else if($confirmation_type == "redirect")
			{
				/*?>
                <script type="text/javascript" language="javascript">
					location.replace("<?php echo $fields['submit']['redirect_url'] ?>");
				</script>
                <?php*/
				wp_redirect($fields['submit']['redirect_url']);
			}	
		}
	}
	
	function check_payment_method_paypal()
	{
		$user_id = $_POST['user_id'];
		add_filter( 'wp_mail_content_type', array($this,'set_html_content_type' ));
		global $errors;
		$form 		= new Registration_form();
		$errors 	= $form->validateRegistration($errors);
		$option 	= get_option( 'pie_register_2' );	
		
		update_user_meta( $user_id, 'active', 0);
		$hash = md5( time() );
		update_user_meta( $user_id, 'hash', $hash );
		
		
		$subject 		= $option['user_subject_email_pending_payment'];				
		$message		= $form->filterEmail($option['user_message_email_pending_payment'],$user, $pass );
		$from_name		= $option['user_from_name_pending_payment'];
		$from_email		= $option['user_from_email_pending_payment'];
		$replay_email	= $option['user_to_email_pending_payment'];
				
		//Headers
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	
		if(!empty($from_email) && filter_var($from_email,FILTER_VALIDATE_EMAIL))//Validating From
		$headers .= "From: ".$from_name." <".$from_email."> \r\n";	
		$headers .= "Reply-To: $replay_email\r\n";
		$headers .= "Return-Path: $replay_email\r\n";
			
		wp_mail($_POST['e_mail'], $subject, $message , $header);
		
		update_user_meta( $user_id, 'register_type', "payment_verify");
		
		if($option['paypal_sandbox']=="no")
		{
			echo '<form id="paypal_form" action="https://www.paypal.com/cgi-bin/webscr" method="post">
			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="hidden" name="hosted_button_id" value="'.$option['paypal_butt_id'].'">
			<input name="custom" type="hidden" value="'.$hash.'|'.$user_id.'">
			</form>';	
		}
		else
		{
			echo '<form  id="paypal_form" action="https://sandbox.paypal.com/cgi-bin/webscr" method="post">
			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="hidden" name="hosted_button_id" value="'.$option['paypal_butt_id'].'">
			<input name="custom" type="hidden" value="'.$hash.'|'.$user_id.'">
			</form>';		
		}
		echo '<script type="text/javascript">document.getElementById("paypal_form").submit();</script>';
		die();
	}

	function process_lostpassword()
 	{
		global $errors ;
		include_once("forgot_password.php");
		get_header();	
		
		//$this->pie_frontend_enqueu_scripts();
	
		
		$output =  resetFormOutput();
		echo $output;
		get_footer();
		exit;
	}
	function process_getpassword()
	{
		global $errors ;
		$user 		= check_password_reset_key($_GET['key'], $_GET['login']);
		if ( is_wp_error($user) ) 
		{	
			wp_redirect( site_url('wp-login.php?action=lostpassword&error=invalidkey') );
			exit;
		}
		
		get_header();
		
		//$this->pie_frontend_enqueu_scripts();
		include_once("get_password.php");
		$get_form = piereg_get_passwird();
		echo $get_form;
		get_footer();
		exit;	
	}
	
	function Unverified(){
			global $wpdb;
			if( $_POST['notice'] )
				echo '<div id="message" class="updated fade"><p><strong>' . $_POST['notice'] . '.</strong></p></div>';
				
		
			$unverified = get_users(array('meta_key'=> 'active','meta_value'   => 0));			
			$piereg = get_option('pie_register_2');
			?>
<div class="wrap">
  <h2>
    <?php _e('Unverified Users', 'piereg')?>
  </h2>
  <form id="verify-filter" method="post" action="">
    <?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'piereg-unverified'); ?>
    <div class="tablenav">
      <div class="alignleft">
        <input onclick="return window.confirm('This will verify users of all types'); " value="<?php _e('Verify Checked Users','piereg');?>" name="verifyit" class="button-secondary" type="submit">
        &nbsp;
        <?php //if( !(empty($piereg['paypal_butt_id'])) && $piereg['enable_paypal']==1){ ?>
        <input value="<?php _e('Resend Pending Payment E-mail','piereg');?>" name="paymentl" class="button-secondary" type="submit">
        <?php //}  else if( $piereg['verification'] == 2 ){ ?>
         &nbsp;
        <input value="<?php _e('Resend Verification E-mail','piereg');?>" name="emailverifyit" class="button-secondary" type="submit">
        <?php //} ?>
        &nbsp;
        <input value="<?php _e('Delete','piereg');?>" name="vdeleteit" class="button-secondary delete" type="submit">
      </div>
      <br class="clear">
    </div>
    <br class="clear">
    <table class="widefat">
      <thead>
        <tr class="thead">
          <th scope="col" class="check-column"><input onclick="checkAll(document.getElementById('verify-filter'));" type="checkbox">
          </th>
          <th><?php _e('User Name','piereg');?></th>
          <th><?php _e('E-mail','piereg');?></th>
          <th><?php _e('Registration Type','piereg');?></th>
          <th><?php _e('Role','piereg');?></th>
        </tr>
      </thead>
      <tbody id="users" class="list:user user-list">
        <?php 
								foreach( $unverified as $un) {
								if( $alt ) $alt = ''; else $alt = "alternate";
								$user_object = new WP_User($un->ID);
								$roles = $user_object->roles;
								$role = array_shift($roles);
								/*
								if( $piereg['email_verify'] )
									$reg_type = get_user_meta($un->ID, 'email_verify_user',true);
								else if( $piereg['admin_verify'] )*/
									$reg_type = get_user_meta($un->ID, 'register_type');
							?>
        <tr id="user-1" class="<?php echo $alt;?>">
          <th scope="row" class="check-column"><input name="vusers[]" id="user_<?php echo $un->ID;?>" class="administrator" value="<?php echo $un->ID;?>" type="checkbox"></th>
          <td><strong><?php echo $un->user_login;?></strong></td>
          <td><a href="mailto:<?php echo $un->user_email;?>" title="<?php _e('E-mail', 'piereg'); echo ": ".$un->user_email;?>"><?php echo $un->user_email;?></a></td>
          <td><?php echo ucwords($reg_type[0]);?></td>
          <td><?php echo ucwords($role);?></td>
          
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </form>
</div>
<?php
		}
	function verifyUsers()
	{
		$valid = $_POST['vusers'];
		if($valid)
		{	
			$option = get_option('pie_register_2');
			foreach( $valid as $user_id )
			{
				if ( $user_id ) 
				{
					update_user_meta( $user_id, 'active',1);
					//$pass = wp_generate_password();
					//wp_set_password( $pass, $user_id );
					
					//Sending E-Mail to newly active user
					$user 			= new WP_User($user_id);
					$subject 		= $option['user_subject_email_admin_verification'];
					$user_email 	= $user->user_email;
					$message	= $this->filterEmail($option['user_message_email_admin_verification'],$user,$pass);
					$from_name		= $option['user_from_name_admin_verification'];
					$from_email		= $option['user_from_email_admin_verification'];
					$replay_email	= $option['user_to_email_admin_verification'];
								
					//Headers
					$headers  = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
					
					if(!empty($from_email) && filter_var($from_email,FILTER_VALIDATE_EMAIL))//Validating From
					$headers .= "From: ".$from_name." <".$from_email."> \r\n";
					$headers .= "Reply-To: $replay_email\r\n";
					$headers .= "Return-Path: $replay_email\r\n";
					
					wp_mail($user_email, $subject, $message , $header);
				}
			}
			$_POST['notice'] = __("User(s) has been activated");
		}
		else
			$_POST['notice'] = "<strong>".__('Error','piereg').":</strong>".__("Please select a user to send emails to", "piereg");
	}
	function PaymentLink()
	{
			global $wpdb;			
			$valid = $_POST['vusers'];
			add_filter( 'wp_mail_content_type', array($this,'set_html_content_type' ));
			if( is_array($valid)) 
			{
				$option = get_option('pie_register_2');
				$sent = 0;
				foreach( $valid as $user_id )
				{		
						$reg_type = get_user_meta($user_id, 'register_type');
						if($reg_type[0] != "payment_verify")
						{
							continue;	
						}
						$sent++;
						update_user_meta( $user_id, 'active', 0);
						$hash = md5( time() );
						update_user_meta( $user_id, 'hash', $hash );
						
			
						$user 			= new WP_User($user_id);
						$subject 		= $option['user_subject_email_pending_payment'];				
						$message		= $this->filterEmail($option['user_message_email_pending_payment'],$user, $pass );
						$from_name		= $option['user_from_name_pending_payment'];
						$from_email		= $option['user_from_email_pending_payment'];
						$replay_email	= $option['user_to_email_pending_payment'];
						
						//Headers
						$headers  = 'MIME-Version: 1.0' . "\r\n";
						$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
					
						if(!empty($from_email) && filter_var($from_email,FILTER_VALIDATE_EMAIL))//Validating From
						$headers .= "From: ".$from_name." <".$from_email."> \r\n";
						$headers .= "Reply-To: $replay_email\r\n";
						$headers .= "Return-Path: $replay_email\r\n";
									
						wp_mail($user->user_email, $subject, $message , $header);	
				}
				if($sent > 0)
					$_POST['notice'] = __("Payment Link Emails have been re-sent", "piereg");
				else
					$_POST['notice'] = __("Invalid User Types", "piereg");
					
			}
			else
			{
				$_POST['notice'] = "<strong>".__('Error','piereg').":</strong>".__("Please select a user to send emails to", "piereg");
			}
			
			
	}
	function AdminEmailValidate()
	{
		
			global $wpdb;			
			$valid = $_POST['vusers'];
			add_filter( 'wp_mail_content_type', array($this,'set_html_content_type' ));
			if( is_array($valid) ) {
			$option = get_option('pie_register_2');
			$sent = 0;
			foreach( $valid as $user_id )
			{
					
					$reg_type = get_user_meta($user_id, 'register_type');
					if($reg_type[0] != "email_verify")
					{
						continue;	
					}
					$sent ++;
					update_user_meta( $user_id, 'active', 0);
					$hash = md5( time() );
					update_user_meta( $user_id, 'hash', $hash );
					
		
					$user 			= new WP_User($user_id);
					
					$subject 		= $option['user_subject_email_email_verification'];				
					$message		= $this->filterEmail($option['user_message_email_email_verification'],$user, $pass );
					$from_name		= $option['user_from_name_email_verification'];
					$from_email		= $option['user_from_email_email_verification'];
					$replay_email	= $option['user_to_email_email_verification'];
							
					//Headers
					$headers  = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				
					if(!empty($from_email) && filter_var($from_email,FILTER_VALIDATE_EMAIL))//Validating From
					$headers .= "From: ".$from_name." <".$from_email."> \r\n";	
					$headers .= "Reply-To: $replay_email\r\n";
					$headers .= "Return-Path: $replay_email\r\n";
		
								
					wp_mail($user->user_email, $subject, $message , $header);	
			}
			
			if($sent > 0)
					$_POST['notice'] = __("Verification Emails have been re-sent", "piereg");
				else
					$_POST['notice'] = __("Invalid User Types", "piereg");
			}
			else
			$_POST['notice'] = "<strong>".__('Error','piereg').":</strong>".__("Please select a user to send emails to", "piereg");
			
		}
	function AdminDeleteUnvalidated()
	{
		global $wpdb;
		$piereg = get_option('pie_register_2');
		$valid = $_POST['vusers'];
		if($valid)
		{	
			include_once( ABSPATH . 'wp-admin/includes/user.php' );
			foreach( $valid as $user_id )
			{
				if ( $user_id ) 
				{
					wp_delete_user($user_id);
				}
			}
			$_POST['notice'] = __("User(s) has been deleted");
		}
	}
	function cleantext($text)
	{
		$text = str_replace(chr(13), " ", $text); //remove carriage returns
		$text = str_replace(chr(10), " ", $text);
		return $text;
	}
	function disable_magic_quotes_gpc(&$value)
	{	
		$value = stripslashes($value);
		return $value;
	}
	function PieGeneralSettings()
	{
		$option 		= get_option( 'pie_register_2' );
		
		$current_key 	= $option['support_license'];		
		$key 			=  get_option("pie_register_2_key");
		$active		 	=  get_option("pie_register_2_active");
			
		if($active != 1 || $current_key=="" || $key=="" || $key != $current_key)
		{
			$piereg = get_option("pie_register_2");
			$piereg['support_license'] = "";
			update_option("pie_register_2",$piereg);
			delete_option("pie_register_2_key");
			delete_option("pie_register_2_active");
			delete_option("pie_register_2_key");
			delete_option("pie_register_2_active");	
		}
		
		require_once($this->plugin_dir.'/menus/PieGeneralSettings.php');			
	}
	function PieRegPaymentGateway()
	{
		require_once($this->plugin_dir.'/menus/PieRegPaymentGateway.php');				
	}
	function PieRegAdminNotification()
	{
		require_once($this->plugin_dir.'/menus/PieRegAdminNotification.php');	
	}
	function PieRegUserNotification()
	{
		require_once($this->plugin_dir.'/menus/PieRegUserNotification.php');	
	}
	function PieRegCustomMessages()
	{
		require_once($this->plugin_dir.'/menus/PieRegCustomMessages.php');		
	}
	function PieRegHelp()
	{
		require_once($this->plugin_dir.'/menus/PieRegHelp.php');		
	}
	function PieRegInvitationCodes()
	{
		global $wpdb;
		$piereg 	= get_option( 'pie_register_2' );		
		$codetable	= $this->codeTable();
		
		if( isset($_POST['invi_del_id']) ) 
		{
			if($wpdb->query("DELETE FROM ".$codetable." WHERE id = ".$_POST['invi_del_id']))	
			$_POST['notice'] = "The Invitation Code has been deleted";
		}
		
		else if( isset($_POST['status_id']) ) 
		{
			if($wpdb->query("update ".$codetable." SET status = CASE WHEN status = 1 THEN  0 WHEN status = 0 THEN 1 ELSE  0 END  WHERE id = ".$_POST['status_id']))	
			$_POST['notice'] = "Status has been changed.";
		}
		
		else if( isset($_POST['piereg_codepass']) ) 
		{
			$update["codepass"] = $_POST['piereg_codepass'];
			$codespasses=explode("\n",$update["codepass"]);
			
			foreach( $codespasses as $k=>$v )
			{
				$this->InsertCode(trim($v));
			}
			$piereg['enable_invitation_codes'] = 	$_POST['enable_invitation_codes'];	
			update_option( 'pie_register_2',$piereg);	
			
			if(isset($_POST['invitation_code_usage']) && is_numeric($_POST['invitation_code_usage'])  && $_POST['invitation_code_usage'] > 0)
			{
				$piereg["invitation_code_usage"] = $_POST['invitation_code_usage'];
				update_option( 'pie_register_2',$piereg);		
			}	
		}
		require_once($this->plugin_dir.'/menus/PieRegInvitationCodes.php');		
	}
	function InsertCode($name)
	{
			if(empty($name)) return false;
			
			global $wpdb;
			$piereg=get_option( 'pie_register_2' );
			
			$codetable=$this->codeTable();
			$expiry=$piereg['codeexpiry'];
			$users = $wpdb->get_results( "SELECT * FROM $codetable WHERE `name`='{$name}'" );
			$counts = count($users);
			$wpdb->flush();
			
			if( $counts > 0 )
			{
				return true;
			}
			
			$name = mysql_real_escape_string(trim(preg_replace("/[^A-Za-z0-9_-]/", '', $name)));			
			$date=date("Y-m-d");
			$usage = $_POST['invitation_code_usage'];			
			$wpdb->query("INSERT INTO ".$codetable." (`created`,`modified`,`name`,`count`,`status`,`code_usage`)VALUES('".$date."','".$date."','".$name."','".$counts."','1','".$usage."')");
			$wpdb->flush();
			return true;
			
		}
	function generateCSV()
	{
		global $wpdb;
		$user_table 		= $wpdb->prefix . "users";
		$user_meta_table 	= $wpdb->prefix . "usermeta";
		
		
		$fields = "";
		if(sizeof($_POST['pie_fields_csv']) > 0)
		{
			$fields	=	implode(',',array_keys($_POST['pie_fields_csv']));					
		}			
		
		
		if(!isset($_POST['pie_fields_csv']) || sizeof($_POST['pie_fields_csv']) == 0)
		{
			$_POST['pie_fields_csv'] = array();
		}
		if(!isset($_POST['pie_meta_csv']) || sizeof($_POST['pie_meta_csv']) == 0)
		{
			$_POST['pie_meta_csv'] = array();
		}
			
		
		$heads	= array_merge(array("id"=>"User ID"),$_POST['pie_fields_csv'],$_POST['pie_meta_csv']);
		
		$query 	= "SELECT ID ";
		$query 	.= ($fields)?",$fields " : "";
		$query 	.= " FROM $user_table ";
		
		//$query 	= "SELECT ID,$fields FROM $user_table ";
		
		if($_POST['date_start'] != "" || $_POST['date_end'] != "")
		{
			$date_start = FALSE;
			$query .= " where ";
			if($_POST['date_start'] != "")
			{
				$query .= " user_registered >= '{$_POST['date_start']} 00:00:00' ";
				$date_start = TRUE;			
			}
			
			if($_POST['date_end'] != "")
			{
				if($date_start)
				{
					$query .= " AND ";	
				}
				$query .= " user_registered <= '{$_POST['date_end']} 23:59:59' ";			
			}		
		}		
		$query .= " order by user_login asc";
		
		$users = $wpdb->get_results($query,ARRAY_A);
		global  $wp_roles,$wpdb;
		if(sizeof($users ) > 0){
			$dfile = "pieregister_exported_users_".date("Y-m-d").".csv";
			header('Content-Type: application/csv');
			header('Content-Disposition: attachment; filename='.$dfile);
			header('Pragma: no-cache');
			echo '"'.implode('","',$heads).'"'."\r\n";
			
			foreach ($users as $user_key=>$user_value){
				$content_data = '';
				foreach($user_value as $single_user_data){
					$content_data.='"'.$single_user_data.'",'; 
				}
				if(sizeof($_POST['pie_meta_csv']) > 0){
					foreach($_POST['pie_meta_csv'] as $key=>$value){
						
						if($key == "wp_capabilities"){
							$user = get_userdata( $user_value['ID'] );
							
							 $capabilities = $user->{$wpdb->prefix . 'capabilities'};
	
							if ( !isset( $wp_roles ) )
								$wp_roles = new WP_Roles();
							$meta_value = '';
							foreach ( $wp_roles->role_names as $role => $name ):
								if ( array_key_exists( $role, $capabilities ) )
									$meta_value = $role;
							endforeach;
						}
						else{
							$meta_value = get_user_meta($user_value['ID'],$key,true);
						}
						
						$content_data.='"'.htmlentities($meta_value, ENT_QUOTES | ENT_IGNORE, "UTF-8").'"'.",";
					}
				}
				echo rtrim($content_data,',');
				echo "\r\n";
			}
			die();
		}
		else
		{
			$_POST['error_message'] = "No Record Found.";	
		}			
	}
	function csv_to_array($filename='', $delimiter=','){
		if(!file_exists($filename) || !is_readable($filename))
			return FALSE;
	
		$header = NULL;
		$data = array();
		if (($handle = fopen($filename, 'r')) !== FALSE)
		{
			while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
			{
				if(!$header)
					$header = $row;
				else
					$data[] = array_combine($header, $row);
					
				
			}
			fclose($handle);
		}
		return $data;
	}
	function importUsers()
	{
		$success_import = 0;
		$unsuccess_import = 0;
		$already_exist = 0;
		if(empty($_FILES['csvfile']['name']))
		{
			$_POST['error_message'] = apply_filters("piereg_didnt_select_file_to_import",__("You did not select a file to import users",'piereg'));
			return;	
		}
		$ext = pathinfo($_FILES['csvfile']['name'], PATHINFO_EXTENSION);
		if($ext != "csv")
		{
			$_POST['error_message'] = __("Invalid CSV file.",'piereg');	
			return;	
		}
		$current_csv_file_data = "";
		if ($_FILES['csvfile']['tmp_name']){
			$csv_data = $this->csv_to_array($_FILES['csvfile']['tmp_name']);
		}
		if(!isset($csv_data[0]) or sizeof($csv_data[0]) < 3)
		{
			 $_POST['error_message'] = __("Invalid CSV File. It must contain all the default user fields.",'piereg');
			 return;	
		}
		$table_fields = array(
							  //////////// DEFAULT FEILDS //////////
							  "User ID"=>"ID",
							  "Username"=>"user_login",
							  "Password"=>'user_pass',
							  "Nickname"=>"user_nicename",
							  "E-mail"=>"user_email",
							  "Website"=>"user_url",
							  "User Registered"=>"user_registered",
							  "Display name"=>"display_name",
							  ///////////// USER META /////////////
							  "First Name"=>"first_name",
							  "Last Name"=>"last_name",
							  "Biographical Info"=>"description",
							  "Role"=>"wp_capabilities");
		$user_csv_data = array();
		$temp_data = array();
		$user_default_data = array();
		$user_meta_key = array();
		foreach($csv_data as $arr_key=>$arr_val){
			foreach($arr_val as $head_key=>$user_data){
				switch($head_key):
					case 'User ID' :
					case 'Username' :
					case 'user_pass' :
					case 'Nickname' :
					case 'E-mail' :
					case 'Website' :
					case 'User Registered' :
					case 'Display name' :
						$user_default_data[$table_fields[$head_key]] = utf8_encode(html_entity_decode($user_data));
					break;
					case 'First Name' :
					case 'Last Name' :
					case 'Biographical Info' :
					case 'Role' :
						$user_meta_key[$table_fields[$head_key]] = utf8_encode(html_entity_decode($user_data));
					break;
				endswitch;
				$temp_data[$table_fields[$head_key]] = $user_data;
			}
			$user_csv_data[$arr_key] = $temp_data;
			$user_default_data['user_pass'] = wp_generate_password();
			
			if ( username_exists( $user_default_data['user_login'] ) ){
				if(isset($_POST['update_existing_users']) && $_POST['update_existing_users'] == "yes"){
					$user_id = wp_update_user($user_default_data);
					if(isset($user_id)){
						$this->update_user_meta_by_array($user_id,$user_meta_key);
					}
				}else{
					$already_exist++;
				}
			}else{
				if(get_user_by('ID',$user_default_data['ID'])){
					if(isset($_POST['update_existing_users']) && $_POST['update_existing_users'] == "yes"){
						$user_id = wp_update_user($user_default_data);
						if(isset($user_id)){
							$this->update_user_meta_by_array($user_id,$user_meta_key);
						}
					}else{
						$already_exist++;
					}
				}else{
					unset($user_default_data['ID']);
					$user_id = wp_insert_user($user_default_data);
					if(isset($user_id)){
						$this->update_user_meta_by_array($user_id,$user_meta_key);
					}
					$success_import++;
				}
			}
			
			unset($temp_data);
			unset($user_meta_key);
			unset($user_default_data);
		}
		
		$_POST['success_message'] = __("$success_import user(s) imported.",'piereg');
		if($unsuccess_import)
			$_POST['error_message'] = __("$unsuccess_import user(s) do not imported.",'piereg');
			
		if($already_exist){
			if(isset($_POST['update_existing_users']) && $_POST['update_existing_users'] == "yes"){
				$_POST['success_message'] .= "<br />".__("$already_exist user(s) Update.",'piereg');
			}else{
				$_POST['error_message'] = __("$already_exist user(s) already exist.",'piereg');
			}
		}
		
		
	}
	function update_user_meta_by_array($user_id,$user_meta_keys)
	{
		if(isset($user_id) and isset($user_meta_keys))
		{
			if(is_array($user_meta_keys)){
				foreach($user_meta_keys as $key=>$val){
					if($key == "wp_capabilities"){
						$wp_user_object = new WP_User($user_id);
						$wp_user_object->set_role($val);
						unset($wp_user_object);
					}else{
						update_user_meta($user_id,$key,$val);
					}
				}
			}
		}
	}
	function PieRegImportExport()
	{
		
		//wp_enqueue_script('jquery-ui-datepicker');
		//wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
		require_once($this->plugin_dir.'/menus/PieRegImportExport.php');		
	}
	
	function SaveSettings()
	{
		$update = get_option( 'pie_register_2' );
		if( isset($_POST['action']) && $_POST['action'] == 'pie_reg_update' )
		{
			if(isset($_POST['payment_gateway_page']))
			{
				$update["enable_paypal"]	= intval($_POST['enable_paypal']);
				$update["paypal_butt_id"]	= $this->disable_magic_quotes_gpc($_POST['piereg_paypal_butt_id']);
				//$update["paypal_pdt"]     = $this->disable_magic_quotes_gpc($_POST['piereg_paypal_pdt']);
				$update["paypal_sandbox"]	= $_POST['piereg_paypal_sandbox'];
			}
			else if(isset($_POST['admin_email_notification_page'])){
				
				$update['enable_admin_notifications']	= intval($_POST['enable_admin_notifications']);
				$update['admin_sendto_email']			= $_POST['admin_sendto_email'];
				$update['admin_from_name']				= $this->disable_magic_quotes_gpc(mb_convert_encoding($_POST['admin_from_name'],'HTML-ENTITIES','utf-8'));
				$update['admin_from_email']				= $_POST['admin_from_email'];
				$update['admin_to_email']				= $_POST['admin_to_email'];
				$update['admin_bcc_email']				= $_POST['admin_bcc_email'];
				$update['admin_subject_email']			= $this->disable_magic_quotes_gpc(mb_convert_encoding($_POST['admin_subject_email'],'HTML-ENTITIES','utf-8'));
				$update['admin_message_email']			= $this->disable_magic_quotes_gpc(mb_convert_encoding($_POST['admin_message_email'],'HTML-ENTITIES','utf-8'));
			
				
			}
			else if(isset($_POST['user_email_notification_page']))
			{
				
				$pie_user_email_types = get_option( 'pie_user_email_types'); 
				
				foreach ($pie_user_email_types as $val=>$type){
					
					//$update['user_sendto_email_'.$val] = $this->disable_magic_quotes_gpc(mb_convert_encoding($_POST['user_sendto_email_'.$val],'HTML-ENTITIES','utf-8'));
					$update['user_from_name_'.$val]		= $this->disable_magic_quotes_gpc(mb_convert_encoding($_POST['user_from_name_'.$val],'HTML-ENTITIES','utf-8'));
					$update['user_from_email_'.$val]	= $this->disable_magic_quotes_gpc(mb_convert_encoding($_POST['user_from_email_'.$val],'HTML-ENTITIES','utf-8'));
					$update['user_to_email_'.$val]		= $this->disable_magic_quotes_gpc(mb_convert_encoding($_POST['user_to_email_'.$val],'HTML-ENTITIES','utf-8'));
					$update['user_bcc_email_'.$val]		= $this->disable_magic_quotes_gpc(mb_convert_encoding($_POST['user_bcc_email_'.$val],'HTML-ENTITIES','utf-8'));
					$update['user_subject_email_'.$val]	= $this->disable_magic_quotes_gpc(mb_convert_encoding($_POST['user_subject_email_'.$val],'HTML-ENTITIES','utf-8'));
					$update['user_message_email_'.$val]	= $this->disable_magic_quotes_gpc(mb_convert_encoding($_POST['user_message_email_'.$val],'HTML-ENTITIES','utf-8'));	
				}
				
			}
			else if(isset($_POST['general_settings_page'])){
				/*if(!empty($_POST['support_license']) && $_POST['support_license'] != $update['support_license'])
				{
					$update['support_license'] = $this->checkLicense($_POST['support_license']);
					
					if(!empty($update['support_license']))
					$_POST['license_success'] = __('Your plugin has been registered', 'piereg');
				}*/
				
				$update['display_hints']			= intval($_POST['display_hints']);
				$update['subscriber_login']			= intval($_POST['subscriber_login']);
				$update['modify_avatars']			= intval($_POST['modify_avatars']);
				$update['show_admin_bar']			= intval($_POST['show_admin_bar']);
				$update['allow_pr_edit_wplogin']	= intval($_POST['allow_pr_edit_wplogin']);
				
				$update['block_WP_profile']			= intval($_POST['block_WP_profile']);
				
				$update['redirect_user']			= intval($_POST['redirect_user']);
				$update['block_wp_login']			= intval($_POST['block_wp_login']);
				$update['alternate_register']		= intval($_POST['alternate_register']);
				
				$update['alternate_login']			= intval($_POST['alternate_login']);
				$update['alternate_forgotpass']		= intval($_POST['alternate_forgotpass']);
				$update['alternate_profilepage']	= intval($_POST['alternate_profilepage']);
				$update['after_login']				= intval($_POST['after_login']);
				$update['outputcss']				= intval($_POST['outputcss']);
				$update['outputhtml']				= $this->disable_magic_quotes_gpc(mb_convert_encoding($_POST['outputhtml'],'HTML-ENTITIES','utf-8'));
				$update['no_conflict']				= $this->disable_magic_quotes_gpc(mb_convert_encoding($_POST['no_conflict'],'HTML-ENTITIES','utf-8'));
				$update['currency']					= $this->disable_magic_quotes_gpc(mb_convert_encoding($_POST['currency'],'HTML-ENTITIES','utf-8'));
				$update['verification']				= intval($_POST['verification']);
				$update['grace_period']				= intval($_POST['grace_period']);
				$update['captcha_publc']			= $_POST['captcha_publc'];
				$update['captcha_private']			= $_POST['captcha_private'];
				$update['custom_css']				= strip_tags($_POST['custom_css']);
				$update['tracking_code']			= $_POST['tracking_code'];
				//$update['payment_setting_amount']	= sprintf("%01.2f", $_POST['payment_setting_amount']);
				
				if(
					   (isset($_POST['support_email']) and $_POST['support_email'] != "" )and 
					   (isset($_POST['support_license']) and $_POST['support_license'] != "" ) and
					   (isset($_POST['empty_license_key']) and $_POST['empty_license_key'] == "yes" )
				   )
				{
					$error = $this->Check_LicenseKey();
					
				}else if(isset($_POST['support_email']) and isset($_POST['support_license']) ){
					$error = $this->Get_LicenseKey();
					$support_license = get_option("pie_register_2");
					$update['support_license'] = $support_license['support_license'];
				}	
			}
				update_option( 'pie_register_2', $update );
				if(trim($error) != "" )
				{
					$_POST['PR_license_notice'] = $error;
				}

				$_POST['notice'] = apply_filters("piereg_settings_saved",__('Settings Saved', 'piereg'));
			
		}
	}	
	function Get_LicenseKey()
	{
		if(
			isset($_POST['domainname']) and isset($_POST['support_email'])
		  )
		{
			$error = "";
			if(trim($_POST['domainname']) 	== ""	or	strpos($_POST['domainname'], 'localhost') !== false)
			{
				$error = apply_filters("piereg_testing_server_localhost_is_not_allowed","Testing server 'localhost' is not allowed to get license key.");
			}
			if(trim($_POST['support_email'])		!= ""	and !filter_var($_POST['support_email'] , FILTER_VALIDATE_EMAIL))
			{
				$error .= apply_filters("piereg_please_enter_valid_email","Please Enter valid email.");
			}
			
			if(trim($error) != "" )
			{
				return $error;
			}
			else
			{
				//$post_url = "http://192.168.14.3/pie/Requesthandler.ashx";
				$post_url = "http://achnawachna.com/PieRegisterService_new/requesthandler.ashx";
				$domain_name = $_POST['domainname'];
				$email = $_POST['support_email'];
				$origin = "plugin";
				$post_string_url	= "type=adddomain&domainname=".$domain_name."&chk=true&email=".$email."&origin=".$origin."";

				$post_response = "";
				if(function_exists('curl_version')){
					$request = curl_init($post_url);
					curl_setopt($request, CURLOPT_HEADER, 0);
					curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($request, CURLOPT_POSTFIELDS, $post_string_url);
					curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE);
					$post_response = curl_exec($request);
					curl_close ($request);
				}else{
					$url = $post_url."?".$post_string_url;
					$post_response = @file_get_contents($url);
				}
				
				$key = trim(strip_tags($post_response));// get license Key from http://pieregister.genetechsolutions.com/
				if(trim($key) != "" )
				{
					$key = trim(strip_tags($key));
					$piereg = get_option("pie_register_2");
					$piereg['support_license'] = trim($key);
					update_option("pie_register_2",$piereg);
					add_option("pie_register_2_key",trim($key));
					add_option("pie_register_2_active",1);
					$error = apply_filters("piereg_Your_version_has_been_registered","Success. Your version has been registered.");
					return $error ;
				}
				else
				{
					return apply_filters("piereg_Server_is_down_please_try_again_later","Server is down please try again later");
				}
			}
		}
	}
	function Check_LicenseKey()
	{
		$response = $this->Check_license_key_form_API($_POST['support_license']);
		if($response != "" and !is_array($response))
		{
			return $response;
		}else{
			$key = $response['key'];
			$key = trim(strip_tags($key));
			$piereg = get_option("pie_register_2");
			$piereg['support_license'] = trim($key);
			update_option("pie_register_2",$piereg);
			add_option("pie_register_2_key",trim($key));
			add_option("pie_register_2_active",1);
			$error = apply_filters("piereg_Your_version_has_been_registered","Success. Your version has been registered.");
			return $error ;
		}
	}
	function Remove_license_Key()
	{
		$piereg = get_option("pie_register_2");
		$piereg['support_license'] = "";
		update_option("pie_register_2",$piereg);
		delete_option("pie_register_2_key");
		delete_option("pie_register_2_active");
		$_POST['PR_license_notice'] = __('Successfully Remove License Key', 'piereg');
		
	}
	function addTextField($field,$no)
	{		
		$name 	= $this->createFieldName($field,$no);
		$id 	= $this->createFieldID($field,$no);
		
		echo '<input disabled="disabled" id="'.$id.'" name="'.$name.'" class="input_fields"  placeholder="'.$field['placeholder'].'" type="text" value="'.$field['default_value'].'" />';	
	}
	function addInvitationField($field,$no)
	{		
		$name 	= $this->createFieldName($field,$no);
		
		echo '<input type="hidden" id="default_'.$field['type'].'">';
		echo '<input disabled="disabled" id="invitation_field" name="'.$name.'" class="input_fields"  placeholder="'.$field['placeholder'].'" type="text" value="'.$field['default_value'].'" />';	
	}
	function addDefaultField($field,$no)
	{		
		$name 	= $this->createFieldName($field,$no);
		$id 	= $this->createFieldID($field,$no);
		if($field['label']=="About Yourself")
		{
			echo '<textarea  rows="5" cols="73" disabled="disabled" id="default_'.$field['field_name'].'" name="'.$name.'"></textarea>';	
		}
		else
		{
			echo '<input disabled="disabled" id="default_'.$field['field_name'].'" name="'.$name.'" class="input_fields"  placeholder="'.$field['placeholder'].'" type="text"  />';	
		}
		echo '<input type="hidden" name="field['.$field['id'].'][id]" value="'.$field['id'].'" id="id_'.$field['id'].'">';
		echo '<input type="hidden" name="field['.$field['id'].'][type]" value="default" id="type_'.$field['id'].'">';
		echo '<input type="hidden" name="field['.$field['id'].'][label]" value="'.$field['label'].'" id="label_'.$field['id'].'">';
		echo '<input type="hidden" name="field['.$field['id'].'][field_name]" value="'.$field['type'].'" id="label_'.$field['id'].'">';
		echo '<input type="hidden" id="default_'.$field['type'].'">';
	}
	function addEmail($field,$no)
	{
		$name 			= $this->createFieldName($field,$no);
		$id 			= $this->createFieldID($field,$no);
		$confirm_email = 'style="display:none;"';
		
		echo '<input disabled="disabled" id="'.$id.'" name="'.$name.'" class="input_fields"  placeholder="'.$field['placeholder'].'" type="text" value="'.$field['default_value'].'" />';
		
		if(isset($field['confirm_email']))
		{
			$confirm_email	= "";
		}
		echo '</div><div '.$confirm_email.' id="confirm_email_label_'.$no.'" class="label_position"><label>Confirm E-Mail</label></div><div class="fields_position"><div id="confirm_email_field_'.$no.'" '.$confirm_email.' class="inner_fields"><input disabled="disabled" type="text" class="input_fields" placeholder="'.$field['placeholder'].'" > </div>';	
	}
	function addPassword($field,$no)
	{		
		$name 			= $this->createFieldName($field,$no);
		$id 			= $this->createFieldID($field,$no);
		
		
		echo '<input disabled="disabled" id="'.$id.'" name="'.$name.'" class="input_fields"  placeholder="'.$field['placeholder'].'" type="text" value="" />';
		
		
		echo '</div><div id="confirm_password_label_'.$no.'" class="label_position"><label>Confirm Password</label></div><div class="fields_position"><div id="confirm_email_field_'.$no.'" '.$confirm_email.' class="inner_fields"><input disabled="disabled" type="text" class="input_fields" placeholder="'.$field['placeholder'].'" > </div>';	
	}
	
	function addUpload($field,$no)
	{
		$name 	= $this->createFieldName($field,$no);
		$id 	= $this->createFieldID($field,$no);
		
		echo '<input disabled="disabled" id="'.$id.'" name="'.$name.'" class="input_fields"  type="file"  />';	
	}
	function addProfilePicUpload($field,$no)
	{
		$name 	= $this->createFieldName($field,$no);
		$id 	= $this->createFieldID($field,$no);
		echo '<input disabled="disabled" id="'.$id.'" name="'.$name.'" class="input_fields"  type="file"  />';
	}
	
	function addAddress($field,$no)
	{
		$name 	= $this->createFieldName($field,$no);
		$id 	= $this->createFieldID($field,$no);
		
		echo '<input type="hidden" id="default_'.$field['type'].'">';
		echo '<div class="address" id="address_fields">
		  <input disabled="disabled" type="text" class="input_fields">
		  <label>Street Address</label>
		</div>
		<div class="address" id="address_address2_'.$no.'">
		  <input disabled="disabled" type="text" class="input_fields">
		  <label>Address Line 2</label>
		</div>
		<div class="address">
		  <div class="address2">
			<input disabled="disabled" type="text" class="input_fields">
			<label>City</label>
		  </div>';
		
		 $hide_state = "";
		 if($field['hide_state'])
		 {
			$hide_state 		= 'style="display:none;"';	
			$hide_usstate 		= 'style="display:none;"';	
			$hide_canstate 		= 'style="display:none;"';	 
		 } 
		 else 
		 {
			 	if($field['address_type'] == "International")
				{
					$hide_state 		= '';		
				}
				else if($field['address_type'] == "United States")
				{
					$hide_usstate 		= '';	
				}
				else if($field['address_type'] == "Canada")
				{
					$hide_canstate 		= '';	
				}
		 }
		
		
		 echo '<div class="address2 state_div_'.$no.'" id="state_'.$no.'" '.$hide_state .'>
			<input disabled="disabled" type="text" class="input_fields">
			<label>State / Province / Region</label>
		  </div>
		  <div class="address2 state_div_'.$no.'" id="state_us_'.$no.'" '.$hide_usstate .'>
			<select disabled="disabled" id="state_us_field_'.$no.'">
			  <option value="" selected="selected">'.$field['us_default_state'].'</option>
			  
			</select>
			<label>State</label>
		  </div>
		  <div class="address2 state_div_'.$no.'" id="state_canada_'.$no.'" '.$hide_canstate.'>
			<select disabled="disabled" id="state_canada_field_'.$no.'">
			  <option value="" selected="selected">'.$field['canada_default_state'].'</option>
			  
			</select>
			<label>Province</label>
		  </div>
		</div>
		<div class="address">';
		 
		
		 $hideAddress2= "";
		 if($field['hide_address2'])
		 {
			$hideAddress2 = 'style="display:none;"';	 
		 }		
		echo ' <div class="address2" '.$hideAddress2.'>
			<input disabled="disabled" type="text" class="input_fields">
			<label>Zip / Postal Code</label>
		  </div>';
		 
		 $hideCountry = "";
		 if($field['address_type'] != "International")
		 {
			$hideCountry = 'style="display:none;"';	 
		 }
		 
		  echo '<div id="address_country_'.$no.'" class="address2" '.$hideCountry.'>
					<select disabled="disabled">
			  			<option>'.$field['default_country'].'</option>
					</select>
					<label>Country</label>
		  		</div>
		</div>';	
	}
	function addTextArea($field,$no)
	{
		$name 	= $this->createFieldName($field,$no);
		$id 	= $this->createFieldID($field,$no);
			
		echo '<textarea disabled="disabled" id="'.$id.'" name="'.$name.'" rows="'.$field['rows'].'" cols="'.$field['cols'].'"   placeholder="'.$field['placeholder'].'">'.$field['default_value'].'</textarea>';		
	}
	
	function addDropdown($field,$no)
	{
		
		$name 	= $this->createFieldName($field,$no);
		$id 	= $this->createFieldID($field,$no);
		
		$multiple = "";
		if($field['type']=="multiselect")
		{
			$multiple 	= 'multiple';	
			$name		.= "[]";	
		}
		
				
		echo '<select '.$multiple.' id="'.$name.'" name="'.$name.'" disabled="disabled">';
	
		if(sizeof($field['value']) > 0)
		{
		
			for($a = 0 ; $a < sizeof($field['value']) ; $a++)
			{
				$selected = '';
				if(in_array($a,$field['selected']))
				{
					$selected = 'selected="selected"';	
				}				
				echo '<option '.$selected.' value="'.$field['value'][$a].'">'.$field['display'][$a].'</option>';	
			}		
		}	
		echo '</select>';			
	}
	function addNumberField($field,$no)
	{
		$name 	= $this->createFieldName($field,$no);
		$id 	= $this->createFieldID($field,$no);
		
		echo '<input disabled="disabled" id="'.$id.'" name="'.$name.'" class="input_fields"  placeholder="'.$field['placeholder'].'" min="'.$field['min'].'" max="'.$field['max'].'" type="number" value="'.$field['default_value'].'" />';		
	}
	function addCheckRadio($field,$no)
	{
		if(sizeof($field['value']) > 0)
		{
			echo '<div class="radio_wrap">';
			$name 	= $this->createFieldName($field,$no);
			$id 	= $this->createFieldID($field,$no);
			
				
			for($a = 0 ; $a < sizeof($field['value']) ; $a++)
			{
				$checked = '';
				if(is_array($field['selected']) && in_array($a,$field['selected']))
				{
					$checked = 'checked="checked"';	
				}				
				echo '<label>'.$field['display'][$a].'</label>';	
				echo '<input '.$checked.' type="'.$field['type'].'" '.$multiple.' name="'.$field['type'].'_'.$field['id'].'[]" class="radio_fields" disabled="disabled" >';
			}		
			echo '</div>';
		}			
	}	
	function addDate($field,$no)
	{		
		$name 	= $this->createFieldName($field,$no);
		$id 	= $this->createFieldID($field,$no);
		
		$datefield 		= 'style="display:none;"';
		$datepicker 	= 'style="display:none;"';
		$datedropdown 	= 'style="display:none;"';
		$calendar_icon 	= 'style="display:none;"';
		$calendar_url 	= 'style="display:none;"';
		
		if($field['date_type'] == "datefield")
		{
			$datefield = "";		
		}
		else if($field['date_type'] == "datepicker")
		{
			$datepicker = "";
			if($field['calendar_icon'] == "calendar")		
			{
				$calendar_icon = "";
			}
		}
		else if($field['date_type'] == "datedropdown")
		{
			$datedropdown = "";		
		}
		
		echo '<div class="time date_format_field" id="datefield_'.$no.'" '.$datefield.'>
				  <div class="time_fields" id="mm_'.$no.'">
					<input disabled="disabled" type="text" class="input_fields">
					<label>MM</label>
				  </div>
				  <div class="time_fields" id="dd_'.$no.'">
					<input disabled="disabled" type="text" class="input_fields">
					<label>DD</label>
				  </div>
				  <div class="time_fields" id="yyyy_'.$no.'">
					<input disabled="disabled" type="text" class="input_fields">
					<label>YYYY</label>
				  </div>
				</div>';
				
		echo	'<div class="time date_format_field" id="datepicker_'.$no.'" '.$datepicker.'>
				  <input disabled="disabled" type="text" class="input_fields">
				  <img src="../wp-content/plugins/pie-register/images/calendar.png" id="calendar_image_'.$no.'" '.$calendar_icon.' /> </div>';
				  
			  
		echo '<div class="time date_format_field" id="datedropdown_'.$no.'"  '.$datedropdown.'>
				  <div class="time_fields" id="month_'.$no.'">
					<select disabled="disabled">
					  <option>Month</option>
					</select>
				  </div>
				  <div class="time_fields" id="day_'.$no.'">
					<select disabled="disabled">
					  <option>Day</option>
					</select>
				  </div>
				  <div class="time_fields" id="year_'.$no.'">
					<select disabled="disabled">
					  <option>Year</option>
					</select>
				  </div>
				</div>';	
		
		
	}
	function addHTML($field,$no)
	{
		echo '<div id="field_'.$no.'" class="htmldiv" id="htmlbox_'.$no.'_div">';		
	}
	function addSectionBreak($field,$no)
	{
		echo '<div style="width:100%;float:left;border: 1px solid #aaaaaa;margin-top:25px;"></div>';	
	}
	function addPageBreak($field,$no)
	{
		echo '<img src="../wp-content/plugins/pie-register/images/pagebreak.png" />';
		
	}
	function addName($field,$no)
	{
		echo '<input type="hidden" id="default_'.$field['type'].'">';
		echo '<input disabled="disabled" type="text" class="input_fields">';
		echo '</div><div class="label_position"><label>Last Name</label></div><div class="fields_position">  <input disabled="disabled" type="text" class="input_fields">';	
	
	}
	function addTime($field,$no)
	{
		
		$name 	= $this->createFieldName($field,$no);
		$id 	= $this->createFieldID($field,$no);
		
		$format = "display:none;";		
		
		if($field['time_type']=="12")
		{
			$format = "";
		}		
		echo '<div class="time"><div class="time_fields"><input disabled="disabled" type="text" class="input_fields"><label>HH</label></div><span class="colon">:</span><div class="time_fields"><input disabled="disabled" type="text" class="input_fields"><label>MM</label></div><div id="time_format_field_'.$no.'" class="time_fields" style="'.$format.'"><select disabled><option>AM</option><option>PM</option></select></div></div>';
	
	}
	function addCaptcha($field,$no)
	{
		$name 	= $this->createFieldName($field,$no);
		$id 	= $this->createFieldID($field,$no);
			
		echo '<img id="captcha_img" src="../wp-content/plugins/pie-register/images/recatpcha.jpg" />';	
		echo '<input type="hidden" id="default_'.$field['type'].'">';	
	}
	function addList($field,$no)
	{
		if($field['cols']=="0")
		$field['cols'] = 1;
		
		$name 	= $this->createFieldName($field,$no);
		$id 	= $this->createFieldID($field,$no);
		$width  = 90 / $field['cols']; 
		
		for($a = 1 ; $a <= $field['cols'] ;$a ++)
		{
			echo '<input type="text" id="field_'.$no.'" class="input_fields" style="width:'.$width.'%;margin-right:2px;" >';
		}
		//echo '<img src="../wp-content/plugins/pie-register/images/plus.png" />';
		echo '<img src="'.plugins_url("images/plus.png",__FILE__).'" />';
	}		
	function createFieldName($field,$no)
	{
		return "field_[".$field['id']."]";		
	}
	function createFieldID($field,$no)
	{
		return "field_".$field['id'];	
	}
	function pie_retrieve_password_title()
	{
		$option = get_option( 'pie_register_2' );
		return $option['user_subject_email_email_forgotpassword'];		
	}
	function pie_retrieve_password_message($content,$key)
	{
		$activation_url =  wp_login_url("url")."?action=rp&key=".$key."&login=".$_POST['log'];
		$option 		= get_option( 'pie_register_2' );		 
		echo str_replace("%forgot_pass_link%","$activation_url",$option['user_message_email_email_forgotpassword']);		
		
	}
	function processPostPayment()
	{
		 	
			
			add_filter( 'wp_mail_content_type', array($this,'set_html_content_type' ));
			
			$return_data = explode("|",$_POST['custom']);			
			$hash 		= $return_data[0];
			$user_id 	= $return_data[1];				 
			
		
			
			if(!is_numeric($user_id ))
				return false;				
			
			$check_hash = get_usermeta( $user_id, "hash");
			
			if($check_hash != $hash)
				return false;
			
			
			
			/*$pass 		= wp_generate_password();
			$user_data 	= array('ID' => $user_id,'user_pass' => $pass);
			$user_id 	= wp_update_user( $user_data ); */
			$user 		= new WP_User($user_id);
			$option 	= get_option('pie_register_2');

			
			//mail("baqar.hassan@genetechsolutions.com","payment",$user_id." , ".$user->user_email.",".$user_email );
		
			update_user_meta( $user_id, 'active',1);
					
			//Sending E-Mail to newly active user
			
			$subject 		= $option['user_subject_email_payment_success'];
			$user_email 	= $user->user_email;
			$message		= $this->filterEmail($option['user_message_email_payment_success'],$user,$pass);
			$from_name		= $option['user_from_name_payment_success'];
			$from_email		= $option['user_from_email_payment_success'];			
			$replay_email	= $option['user_to_email_payment_success'];
						
			//Headers
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			
			if(!empty($from_email) && filter_var($from_email,FILTER_VALIDATE_EMAIL))//Validating From
			$headers .= "From: ".$from_name." <".$from_email."> \r\n";	
			$headers .= "Reply-To: $replay_email\r\n";
			$headers .= "Return-Path: $replay_email\r\n";
			
			
			
			wp_mail($user_email, $subject, $message , $header);
							
	}	
	function set_html_content_type() 
	{
		return 'text/html';
	}
	function deleteUsers($user_id = 0,$user_email = "",$user_registered = "")
	{
		$option 		= get_option( 'pie_register_2' );
		$grace			= ((int)$option['grace_period']);
		
		if(($grace != 0 and $user_id != 0) and ($user_email != "" and $user_registered != ""))
		{
			$date			= date("Y-m-d 00:00:00",strtotime("-{$grace} days"));
			/*$inactive		= get_users(array('meta_key'=> 'active','meta_value' => 0,'role'=>get_option('default_role')));
			if(sizeof($inactive) > 0)
			{
				include_once( ABSPATH . 'wp-admin/includes/user.php' );
				foreach($inactive as $user)
				{
					if($user->user_registered < $date)
					{
						//wp_delete_user( $user->ID );
					}
				}	
			}*/
			
			if($user_registered < $date)
			{
				$this->wp_mail_send($user_email,"user_perm_blocked_notice");
				global $errors,$wpdb;
				$errors = new WP_Error();
				$errors->add("Login-error",apply_filters("piereg_your_account_has_no_longer_exist",__("Your account has no longer exist.")));
				$wpdb->query("DELETE FROM `wp_usermeta` WHERE `user_id` = '".$user_id."'");
				$wpdb->query("DELETE FROM `wp_users` WHERE `ID` = '".$user_id."'");
				wp_logout();
				return true;
				//wp_delete_user($user_id);
				
			}
		}
		return false;
	}
	function unique_user()
	{
		$username 	= $_REQUEST['fieldValue'];		
		$validateId	= $_REQUEST['fieldId'];
		
		$arrayToJs = array();
		$arrayToJs[0] = $validateId;

		if(!username_exists($username ))
		{		// validate??
				$arrayToJs[1] = true;			// RETURN TRUE
				echo json_encode($arrayToJs);			// RETURN ARRAY WITH success
		}
		else
		{
			for($x=0;$x<1000000;$x++)
			{
				if($x == 990000)
				{
					$arrayToJs[1] = false;
					echo json_encode($arrayToJs);		// RETURN ARRAY WITH ERROR
				}
			}				
		}
		die();
	}
	function showForm()
	{
		global $errors;
		$option 		= get_option( 'pie_register_2' );	
		if(is_user_logged_in() && $option['redirect_user']==1 )
		{
			$this->afterLoginPage();
			return "";	
		}		
		else
		{
				
			add_filter( 'wp_mail_content_type', array($this,'set_html_content_type' ));
			//$this->pie_frontend_enqueu_scripts();			
			//ob_start();
			$output = '';
			
			if($_POST['success'] != "")
			$output .= '<p class="piereg_message">'.apply_filters('piereg_messages',__($_POST['success'],"piereg")).'</p>';
			if($_POST['error'] != "")
			$output .= '<p class="piereg_login_error">'.apply_filters('piereg_messages',__($_POST['error'],"piereg")).'</p>';
			if(sizeof($errors->errors) > 0)
			{
				foreach($errors->errors as $key=>$err)
				{
					if($key != "login-error")
						$error .= $err[0] . "<br />";	
				}
				if(!empty($error))
					$output .= '<p class="piereg_login_error">'.apply_filters('piereg_messages',__($error,"piereg")).'</p>';
			}
			
			include_once("register_form.php");
			$output .= outputRegForm();
			return $output;
			//ob_end_flush();
		}
		
	}
	function showLoginForm()
	{
		global $errors,$pagenow;
		$option 		= get_option( 'pie_register_2' );	
		
		
		if(is_user_logged_in() && $option['redirect_user']==1 )
		{
			$this->afterLoginPage();
			return "";	
		}	
		
		else
		{	
			//$this->pie_frontend_enqueu_scripts();
			//ob_start();
			include_once("login_form.php");
			$output = pieOutputLoginForm();
			return  $output;
			//ob_end_flush();
		}
	}
	function showForgotPasswordForm()
	{
		global $errors;
		$option 		= get_option( 'pie_register_2' );	
		if(is_user_logged_in() && $option['redirect_user']==1 )
		{
			$this->afterLoginPage();
			return "";	
		}	
		
		else
		{
			//$this->pie_frontend_enqueu_scripts();
			include_once("forgot_password.php");
			//$output =  resetFormOutput();
			$output =  pieResetFormOutput();
			return $output;
		}
			
	}	
	function showProfile()
	{
		global $current_user,$pie_success,$pie_error,$pie_error_msg,$pie_suucess_msg;
		if ( is_user_logged_in() ){
			
			get_currentuserinfo();
			
			if(isset($_GET['edit_user']) && $_GET['edit_user'] == "1"){
				
				$form 		= new Edit_form($current_user);
				if(isset($_POST['pie_submit_update'])  )			
				{
					$form->error = "";
					$errors = new WP_Error();
					$errors = $form->validateRegistration($errors);	
					if(sizeof($errors->errors) > 0)
					{
						foreach($errors->errors as $err)
						{
							$form->error .= $err[0] . "<br />";	
						}		  	
					}	
					else
					{
						 $user_data = array('ID' => $current_user->ID,'user_email' => $_POST['e_mail']);
						 if(isset($_POST['url']))
						 {
							$user_data["user_url"] =  $_POST['url'];	 
						 }
						 /*if($current_user->user_pass != $_POST['pwd'] && $_POST['pwd'] == $_POST['confirm_password'])
		 				 {
							$user_data["user_pass"] =  $_POST['pwd'];
						 }*/
						 if(($_POST['password'] != '') && ($_POST['password'] == $_POST['confirm_password']))
		 				 {
							$user_data["user_pass"] =  $_POST['password'];
						 }
						 
						$id = wp_update_user( $user_data );						
						$form->UpdateUser();
					}
							
				}
				
				/*$success 	= '' ;
				$error 		= '' ;*/
				$output = '';
				if($form->pie_success)
					$output .= '<div class="alert alert-success"><p class="piereg_message">'.$form->pie_success_msg.'</p></div>';
				elseif($form->error != "")
					$output .= '<div class="alert alert-danger"><p class="piereg_login_error">'.$form->error.'</p></div>';	
				//$option 	= get_option( 'pie_register_2' );
				if($_POST['success'] != "")
					$output .= '<p class="piereg_message">'.apply_filters('piereg_messages',__($_POST['success'],"piereg")).'</p>';
				if($_POST['error'] != "")
					$output .= '<p class="piereg_login_error">'.apply_filters('piereg_messages',__($_POST['error'],"piereg")).'</p>';
				require_once($this->plugin_dir."/edit_form.php");
				$output.= edit_userdata();
				return $output;
			}
			else
			{
				$profile_front = new Profile_front($current_user);		
				$profile_form_data = $profile_front->print_user_profile();
				return $profile_form_data;
			}
		}
		else
		{
			return __('Please','piereg').' <a href="'.wp_login_url().'">'. __('login','piereg').'</a> '.__('to see your profile','piereg');
		}	
	}
	function show_renew_account()
	{
		include_once("renew_account.php");
	}
	function afterLoginPage()
	{
		$this->flush_ob_end();
		$option = get_option("pie_register_2");
		if($option['after_login'] > 0)
		{
			//wp_redirect($this->get_redirect_url_pie(get_permalink($option['after_login'])));
			wp_redirect(get_permalink($option['after_login']));
		}else{
			wp_redirect(site_url());
		}	
	}
	function add_ob_start()
	{
     	ob_start();
	}
	
	function flush_ob_end()
	{
		 ob_clean();
	}
	
	function payment_validation_paypal()
	{
		global $errors, $wp_session;
		add_filter('wp_mail_content_type', array($this,'set_html_content_type'));
		
		$form 		= new Registration_form();
		$errors 	= $form->validateRegistration($errors);
		$option 	= get_option( 'pie_register_2' );	
		
		update_user_meta( $user_id, 'active', 0);
		$hash = md5( time() );
		update_user_meta( $user_id, 'hash', $hash );
		
		
		$subject 		= $option['user_subject_email_pending_payment'];				
		$message		= $form->filterEmail($option['user_message_email_pending_payment'],$user, $pass );
		$from_name		= $option['user_from_name_pending_payment'];
		$from_email		= $option['user_from_email_pending_payment'];					
		$replay_email	= $option['user_to_email_pending_payment'];	
				
		//Headers
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	
		if(!empty($from_email) && filter_var($from_email,FILTER_VALIDATE_EMAIL))//Validating From
		$headers .= "From: ".$from_name." <".$from_email."> \r\n";
		$headers .= "Reply-To: $replay_email\r\n";
		$headers .= "Return-Path: $replay_email\r\n";
					
		wp_mail($_POST['e_mail'], $subject, $message , $header);
		
		update_user_meta( $user_id, 'register_type', "payment_verify");
		
		if($option['paypal_sandbox']=="no")
		{
			echo '<form id="paypal_form" action="https://www.paypal.com/cgi-bin/webscr" method="post">
			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="hidden" name="hosted_button_id" value="'.$option['paypal_butt_id'].'">
			<input name="custom" type="hidden" value="'.$hash.'|'.$user_id.'">
			</form>';	
		}
		else
		{
			echo '<form  id="paypal_form" action="https://sandbox.paypal.com/cgi-bin/webscr" method="post">
			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="hidden" name="hosted_button_id" value="'.$option['paypal_butt_id'].'">
			<input name="custom" type="hidden" value="'.$hash.'|'.$user_id.'">
			</form>';
		}	
		echo '<script type="text/javascript">document.getElementById("paypal_form").submit();</script>';
		echo 'document.getElementById("paypal_form").submit();';
		die();
	}
	
	
	function Add_payment_option() // Only For Paypal
	{
		$check_payment = get_option("pie_register_2");
		if($check_payment["enable_2checkout"] == 1 && !(empty($check_payment['piereg_2checkout_api_id'])) )
		{
			//do_action('Add_submit_button_2checkout');
			echo '<option value="paypal" data-img="https://www.paypalobjects.com/en_US/i/logo/paypal_logo.gif">Paypal (one time subscription)</option>';
		}
	}
	function add_payment_method_script() // Only For Paypal
	{
		$check_payment = get_option("pie_register_2");
		if($check_payment["enable_paypal"] == 1 && !(empty($check_payment['paypal_butt_id'])) )
		{
			//Add jQuery for payment Method
			?>
                if(jQuery(this).val() == "paypal")
                {
                    payment = 'You are Select paypal payment method.';
                    image = '<img src="'+jQuery('option:selected',jQuery(this)).attr('data-img')+'" style="max-width: 150px;padding-top: 20px;" />';
                }
			<?php 
		}
	}
	function add_select_payment_script()
	{
		?> 
		<script type="text/javascript">
            jQuery(document).ready(function(){
                jQuery("#select_payment_method").change(function(){
                    if(jQuery(this).val() != "")
                    {
                        var payment = "", image = "";
                        <?php do_action('add_payment_method_script'); ?>
                        jQuery("#show_payment_method").html(payment);
                        jQuery("#show_payment_method_image").html(image);
                    }
                    else
                    {
                        jQuery("#show_payment_method").html("");
                        jQuery("#show_payment_method_image").html("");
                    }
                });
            });
        </script>
        <?php
	}
	function get_payment_content_area()
	{
		echo '<div id="show_payment_method_image"></div>';
		echo '<div id="show_payment_method"></div><br>';
	}
	function show_icon_payment_gateway() // for paypal
	{
		$button = get_option("pie_register_2");
		if(!(empty($button['paypal_butt_id'])) && $button['enable_paypal']==1)
	 	{
			?>
              <div class="fields_options submit_field">
                <img style="width:100%;" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" />
              </div>
            <?php
		}
	}
	function renew_account()
	{
		if(isset($_POST['select_payment_method']) and trim($_POST['select_payment_method']) != "")
		{
			//Array ( [user_name] => demo [u_pass] => 123456789 [select_payment_method] => authorizeNet [x_card_num] => 222 [x_exp_date] => 2 [pie_renew] => Renew Account )
			$creds = array();
			$creds['user_login'] 	= $_POST['user_name'];
			$creds['user_password'] = $_POST['u_pass'];
			$user = wp_signon( $creds, false );
			if($user->ID != 0 or $user->ID != "")
			{
				$user_meta = get_user_meta($user->ID);
				if($user_meta['active'][0] == 0)
				{
					if(isset($_POST['select_payment_method']) and $_POST['select_payment_method'] != "" )//Goto payment method Like check_payment_method_paypal
					{
						$_POST['user_id'] = $user->ID;
						$_POST['renew_account_msg'] = apply_filters("piereg_Renew_Account",__("Renew Account","piereg"));
						do_action("check_payment_method_".$_POST['select_payment_method']);
					}
				}
			}
			else
			{
				$_POST['error'] = apply_filters("piereg_Invalid_Username_or_Password",__("Invalid Username or Password","piereg"));
			}
		}
		else
		{
			$_POST['error'] = apply_filters("piereg_Please_Select_any_payment_method",__("Please Select any payment method","piereg"));
			wp_logout();
		}
	}
	
	function wp_mail_send($to_email = "",$key = "",$additional_msg = "",$msg = "")
	{
		global $errors;
		$errors = new WP_Error();
		if(trim($key) != "" and trim($to_email) != "" )
		{
			$email_types = get_option("pie_register_2");
			$message  		= $this->filterEmail( ($email_types['user_message_email_'.$key]."<br />".$additional_msg) ,$to_email);
			$to				= $to_email;
			$from_name		= $email_types['user_from_name_'.$key];
			$from_email		= $email_types['user_from_email_'.$key];
			$reply_to_email	= $email_types['user_to_email_'.$key];
			$subject		= $email_types['user_subject_email_'.$key];

			if(!filter_var($to,FILTER_VALIDATE_EMAIL))//if not valid email address then use wordpress default admin
			{
				$to = get_option('admin_email');
			}
			
			//Headers
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			
			if(!empty($from_email) && filter_var($from_email,FILTER_VALIDATE_EMAIL))//Validating From
				$headers .= "From: ".$from_name." <".$from_email."> \r\n";
			
			if(!empty($reply_to_email) && filter_var($reply_to_email,FILTER_VALIDATE_EMAIL))//Validating Reply To
				$headers .= 'Reply-To: <'.$reply_to_email.'> \r\n';
			
			if(!mail($to,$subject,$message,$headers))
			{
				$errors->add('check-error',apply_filters("piereg_problem_and_the_email_was_probably_not_sent",__("There was a problem and the email was probably not sent.",'piereg')));
			}
			else{
				if(trim($msg) != "")
				{
					$_POST['success'] = __($msg,"piereg");
				}
			}
		}
	}
	
	function check_enable_social_site_method_func()
	{
		if($this->check_enable_social_site_method() == "true"){
			?>
			<center><h2>OR</h2></center>
			<h3>Login From Social Websit</h3>
			<center>
			<?php do_action("get_enable_social_sites_button"); ?>
			</center>
			<?php
	  	}
	}
	function add_custom_avatars($avatar="", $id_or_email="", $size="")
	{
		/*if(is_user_logged_in())
		  {
			$current_user = wp_get_current_user();
			
			$profile_pic_array = get_user_meta($current_user->ID);
			foreach($profile_pic_array as $key=>$val)
			{
				if(strpos($key,'profile_pic') !== false)
				{
					$profile_pic = trim($val[0]);
				}
			}
			
			if(!preg_match('/(http|https):\/\/(www\.)?[\w-_\.]+\.[a-zA-Z]+\/((([\w-_\/]+)\/)?[\w-_\.]+\.(png|gif|jpg|jpeg|xpng|bmp))/',$profile_pic)){
				$profile_pic = plugin_dir_url(__FILE__).'images/userImage.png';
			}
			
			if(trim($profile_pic) != "")
			{
			  return '<img src="'.$profile_pic.'" class="avatar photo" style="max-height:64px;max-width:64px;" width="'.$size.'" height="'.$size.'" alt="'.$current_user->display_name .'" />';
			}
		  }*/
		  
	}

	/*function pie_registration_url($url)
	{
		$options = get_option("pie_register_2");
		$pie_registration_url = get_permalink($options['alternate_register']);
		return ($pie_registration_url)? $pie_registration_url : wp_registration_url();
	}
	function pie_login_url($url)
	{
		$options = get_option("pie_register_2");
		$pie_login_url = get_permalink($options['alternate_login']);
		return ($pie_login_url)? $pie_login_url : wp_login_url();
	}
	function pie_lostpassword_url($url)
	{
		$options = get_option("pie_register_2");
		$pie_lostpass_url = get_permalink($options['alternate_forgotpass']);
		return ($pie_lostpass_url)? $pie_lostpass_url : wp_lostpassword_url();
	}*/
	function delete_invitation_codes($ids="0")
	{
		global $wpdb;
		$prefix=$wpdb->prefix."pieregister_";
		$codetable=$prefix."code";
		$sql = "DELETE FROM `$codetable` WHERE `id` IN ( ".$ids." )";
		$wpdb->query($sql);
	}
	function active_or_unactive_invitation_codes($ids="0",$status="1")
	{
		global $wpdb;
		$prefix=$wpdb->prefix."pieregister_";
		$codetable=$prefix."code";
		$sql = "UPDATE `wp_pieregister_code` SET `status`= ".$status." WHERE `id` IN (".$ids.")";
		$wpdb->query($sql);
	}
	function pireg_update_invitation_code_cb_url()
	{
		global $wpdb;
		$prefix=$wpdb->prefix."pieregister_";
		$codetable=$prefix."code";
		$inv_code_id = intval($_POST['data']['id']);
		if( isset($_POST['data']))
		{
			if(trim($_POST['data']['id']) != "" and trim($_POST['data']['value']) != "" and trim($_POST['data']['type']) != "")
			{
				global $wpdb;
				$sql ="";
				if(trim($_POST['data']['type']) == "name")
				{
					$sql_res_sel = $wpdb->get_var( $wpdb->prepare( "SELECT `name` FROM `{$codetable}` WHERE `name` = %s", $_POST['data']['value']) );
					if(!$sql_res_sel)
						$sql = "UPDATE `{$codetable}` SET `name`='".mysql_real_escape_string($_POST['data']['value'])."' WHERE `id` = '{$inv_code_id}'";
					else{
						echo "duplicate";
						die();
					}
				}
				else if(trim($_POST['data']['type']) == "code_usage")
				{
					$sql = "UPDATE `{$codetable}` SET `code_usage`='".mysql_real_escape_string($_POST['data']['value'])."' WHERE `id` = ".((int)$_POST['data']['id'])."";
				}
				$result = $wpdb->query($sql);
				if($result)
				{
					echo "done";
				}
				else{
					echo "error";
				}
			}
		}
		die();
	}
	
	function piereg_ProgressBarScripts($countPageBreaks)
	{
		$output = '
			<script type="text/javascript">
			  jQuery(document).ready(function() {
				 jQuery( ".piereg_progressbar" ).progressbar({
				  value:  1 /'.$countPageBreaks.' * 100
				});  
			});
		</script>
		';
		return $output;
	}
	function checkUserAllowedPassReset($val,$userid){
		if(!$userid) return false;
		//Checkj if the user is active or not
		//if active true, or false
		$user_active_status = get_user_meta($userid,"active");
		return $user_active_status[0];
	}
	function piereg_password_reset_not_allowed_text_function($text)
	{
		return $text;
	}
}
}
if( class_exists('PieRegister') ){
	$pie_register = new PieRegister();
	if(isset($pie_register)){
		register_activation_hook( __FILE__, array(  &$pie_register, 'install_settings' ) );
		register_deactivation_hook( __FILE__, array(  &$pie_register, 'uninstall_settings' ) );
		function pie_registration_url($url=false)
		{
			return PieRegister::pie_registration_url($url);
		}
		function pie_login_url($url=false)
		{
			return PieRegister::pie_login_url($url);
		}
		function pie_lostpassword_url($url=false)
		{
			return PieRegister::pie_lostpassword_url($url);
		}
		function pie_modify_custom_url($url,$query_string=false){
			return PieRegister::pie_modify_custom_url($url,$query_string);
		}
	}
}


?>