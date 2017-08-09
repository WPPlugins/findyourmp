<?php

/*
Plugin Name: Find Your MP
Plugin URI: 
Description: A plugin for retrieving information about United Kingdom Members of Parliament
Author: Rupert Young
Version: 1.0
Author URI: 
*/

  
define('MPS_FILE_PATH', dirname(__FILE__));
define('MPS_DIR_NAME', basename(MPS_FILE_PATH));

register_activation_hook( __FILE__, 'mps_activate' );
add_action('admin_menu', 'mpsmenus');
add_action('wp_head', 'add_mps_stylesheet');
add_action('save_post', 'mps_save_function', 100, 2);
add_filter('the_content' , 'mpsshortcode', 1);
  

/**
 * load CSS file.
 * @return void
 */
function add_mps_stylesheet() {
    $mpsStyleUrl = plugins_url('/mps/css/mps.css');
    echo '<link type="text/css" rel="stylesheet" href="' . $mpsStyleUrl . '" />' . "\n";
}

/**
 * add Admin menues for the plugin.
 * @return void
 */
 
function mpsmenus() {
    add_menu_page('MPS Administration', 'MPS', 'manage_options', MPS_DIR_NAME . '/mps_admin.php' );
    add_submenu_page(MPS_DIR_NAME.'/mps_admin.php', __('MPS Configuration'), __('Configuration'), 'manage_options' , MPS_DIR_NAME . '/mps_config.php');
}

require_once('mps_functions.php');

/**
 * used on the WP filter 'the_content'.
 * if the [mps-e-cards] pattern is found on the $content, filter the content and add the flash object or the generated image, otherwise return the $content.
 * @param string $content
 * @return string
 */
function mpsshortcode($content) {
//echo "mpsshortcode";
    if ( strpos($content, 'find-your-mp') ) {
//echo " yes ";
        global $post;
		if ( !isset($_REQUEST['postcode']) ) {
            $content = mps_default_render($post->ID, $content);
        	return $content ;
		}else if ( isset($_REQUEST['postcode']) ) {
		//echo $_REQUEST['postcode']; 
			$content = mps_get_details($_REQUEST['postcode']);
        	return $content ;
		}
		
/*        if ( !isset($_REQUEST['imageid']) && !isset($_REQUEST['confirm']) ) {
            $content = default_render($post->ID, $content);  
            return $content ;
        } else if ( isset($_REQUEST['imageid']) ) {
            return image_render($post->ID); // throw the content and show the image and form
        } else {
            return confirm_render($post->ID); // throw the content, send the email and show the 'your email has been sent' text
        }
		*/
		
    } else {
        return $content; // not our post, don't touch it
    }
}

/**
 * on plugin activation create database tables.
 * @param bool $bool
 * @return void
 */
 
 
function mps_activate() {
    global $wpdb;
    $tbl_settings = $wpdb->prefix . "mps_settings";
    echo "Activating MPS";
	
	/*
    if($wpdb->get_var("show tables like '$tbl_settings'") != $tbl_settings ) {
        $sql = "CREATE TABLE IF NOT EXISTS $tbl_settings (
        sid bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        name varchar(16) COLLATE utf8_unicode_ci NOT NULL,
        value varchar(2048) COLLATE utf8_unicode_ci NOT NULL,
        PRIMARY KEY (sid)
    );";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        $sql = 'INSERT INTO ' . $tbl_settings . ' (sid, name, value) VALUES
            (null, \'width\', \'400\'),
            (null, \'height\', \'350\'),
            (null, \'feed\', \'http://zetaprints.com/RssTemplates.aspx?0AFC70BA-F856-42D9-B707-FFF27753F0F0\'),' .
            '(null, \'message\', \'Replace with email body message\'),
            (null, \'confirmmessage\', \'Check your Inbox or Junk folder for a confirmation email. The card will be emailed as soon as you click on the link the email\'),
            (null, \'recipients\', \'Name#1 Surname, name@example.com' . "\r\n" . 'Name#2 Surname, name2@example.com' . "\r\n" . '\'),
            (null, \'requirefrom\', \'on\'),
            (null, \'validatefrom\', \'\'),
            (null, \'from\', \'Replace with your email address\'),
            (null, \'domain\', \'http://zetaprints.com/\'),
            (null, \'subject\', \'Replace with email subject\'
        );';
        $result = $wpdb->query($sql);
    }


    $tbl_emails = $wpdb->prefix . "ecards_emails";
    if($wpdb->get_var("show tables like '$tbl_emails'") != $tbl_emails ) {
        $sql = "CREATE TABLE IF NOT EXISTS $tbl_emails (
            eid bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            link varchar(64) COLLATE utf8_unicode_ci NOT NULL,
            emailfrom varchar(128) COLLATE utf8_unicode_ci NOT NULL,
            emailto varchar(128) COLLATE utf8_unicode_ci NOT NULL,
            image varchar(128) COLLATE utf8_unicode_ci NOT NULL,
            PRIMARY KEY (eid)
        );";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    $tbl_post_settings = $wpdb->prefix . "ecards_post_settings";
    if($wpdb->get_var("show tables like '$tbl_post_settings'") != $tbl_post_settings ) {
        $sql = "CREATE TABLE IF NOT EXISTS $tbl_post_settings (
            pid bigint(20) unsigned NOT NULL,
            settings varchar(4096) COLLATE utf8_unicode_ci NOT NULL,
            PRIMARY KEY (pid)
        );";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
*/
}


// v2 1.9
require('mps_settings.php');
add_action('admin_menu', 'mps_settings_box');
add_filter('admin_print_scripts', 'mpsadminHead');
add_action('wp_print_scripts', 'mpsaddscript' );
add_action('wp_ajax_mps_ajax', 'ajaxResponse');

/**
 * load javascript file.
 * @return void
 */
function mpsaddscript() {
    //wp_enqueue_script('jquery');
    wp_enqueue_script('mpsjs', plugins_url('/mps/js/mps.js'), array('jquery'), '1.0');
}


/**
 * load admin CSS file.
 * @return void
 */
function mpsadminHead () {
    echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/mps/css/mpsAdmin.css" />' . "\n";
}

/**
 * not used.
 */
 /*
function ecards_edit_function() {
    echo "ecards_edit: ";
    print_r($_POST);
    echo "ecards_edit end <br />";
}
*/

/**
 * save ecards settings for the current post.
 * @param int $arg1
 * @return void
 */
 
 
function mps_save_function($arg1, $arg2) {
/*
    global $wpdb;
    $pid = $arg1;
    $tbl_post_settings = $wpdb->prefix . "ecards_post_settings";
    $settings['width'] = $_POST['width'];
    $settings['height'] = $_POST['height'];
    $settings['feed'] = $_POST['feed'];
    $settings['message'] = addslashes($_POST['message']);
    $settings['confirmmessage'] = addslashes($_POST['confirmmessage']);
    $settings['recipients'] = $_POST['recipients'];
    $settings['requirefrom'] = $_POST['requirefrom'];
    $settings['validatefrom'] = $_POST['validatefrom'];
    $settings['from'] = $_POST['from'];
    $settings['domain'] = $_POST['domain'];
    $settings['subject'] = addslashes($_POST['subject']);
    $serialsettings = prepare_settings($settings);

    $tmp = $wpdb->get_var("SELECT settings from $tbl_post_settings WHERE pid = '$pid'");
    if ($tmp == null) {
        $query = "INSERT INTO $tbl_post_settings ( pid, settings ) VALUES ('$pid', '$serialsettings')";
        $results = $wpdb->query( $query );
    } else {
        $query = "UPDATE $tbl_post_settings SET settings='$serialsettings' WHERE pid = '$pid'" ;
        $results = $wpdb->query( $query );
    }
*/
}


/**
 * escape $settings after serializing.
 * @param array $settings
 * @return string
 */
function mps_prepare_settings($settings) {
    global $wpdb;
    return $wpdb->escape(serialize($settings));
    return serialize($settings);
}


?>
