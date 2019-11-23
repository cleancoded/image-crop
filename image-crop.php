<?php
/*
Plugin Name: Image Crop
Plugin URI: https://github.com/cleancoded/image-crop
Description: Plugin allows you to manually crop all the image sizes..
Version: 1.0
Author: cleancoded
Author URI: https://github.com/cleancoded
*/

define('mic_VERSION', '1.12');

include_once(dirname(__FILE__) . '/lib/cleancodedImageCropSettingsPage.php');

//mic - stands for Manual Image Crop

add_action('plugins_loaded', 'mic_init_plugin');

add_option('mic_make2x', 'true'); //Add option so we can persist make2x choice across sessions

/**
 * inits the plugin
 */
function mic_init_plugin() {
	// we are gonna use our plugin in the admin area only, so ends here if it's a frontend
	if (!is_admin()) return;

	include_once(dirname(__FILE__) . '/lib/cleancodedImageCrop.php');

	load_plugin_textdomain('microp', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

	$cleancodedImageCrop = cleancodedImageCrop::getInstance();
	add_action( 'admin_enqueue_scripts', array($cleancodedImageCrop, 'enqueueAssets') );
	$cleancodedImageCrop->addEditorLinks();

	//attach admin actions
	add_action('wp_ajax_mic_editor_window', 'mic_ajax_editor_window');
	add_action('wp_ajax_mic_crop_image', 'mic_ajax_crop_image');
}

/**
 * ajax call rendering the image cropping area
 */
function mic_ajax_editor_window() {
	include_once(dirname(__FILE__) . '/lib/cleancodedImageCropEditorWindow.php');
	$cleancodedImageCropEditorWindow = cleancodedImageCropEditorWindow::getInstance();
	$cleancodedImageCropEditorWindow->renderWindow();
	exit;
}

/**
 * ajax call that does the cropping job and overrides the previous image version
 */
function mic_ajax_crop_image() {
	$cleancodedImageCrop = cleancodedImageCrop::getInstance();
	$cleancodedImageCrop->cropImage();
	exit;
}


/**
 * add settings link on plugin page
 */
function mic_settings_link($links) {
	$settings_link = '<a href="options-general.php?page=Mic-setting-admin">' . __('Settings') . '</a>';
	array_unshift($links, $settings_link);
	return $links;
}

$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'mic_settings_link' );
