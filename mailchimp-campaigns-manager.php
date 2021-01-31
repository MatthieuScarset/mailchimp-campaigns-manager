<?php

/*
 * Plugin Name: MailChimp Campaigns Manager
 * Plugin Script: mailchimp-campaigns-manager.php
 * Plugin URI:   https://mailchimp-campaigns-manager.com
 * Description: Import and display your MailChimp campaigns in WordPress with simple shortcodes.
 * Author: MatthieuScarset
 * Author URI: https://matthieuscarset.com/
 * License: GPL
 * Version: 1.0.0
 * Text Domain: mailchimp_campaigns_manager
 * Domain Path: languages/
 *
 * Import and display your MailChimp campaigns in WordPress with simple shortcodes.
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {exit;}

define('MCM_PLUGIN_ROOT_DIR', plugin_dir_path(__FILE__));

/**
 * Register admin pages.
 */
function mailchimp_campaigns_manager_init() {
  if (is_admin()) {
    // Init the setting page.
    require_once MCM_PLUGIN_ROOT_DIR . 'src/MailchimpCampaignsManagerSettings.php';
    new MailchimpCampaignsManagerSettings();
  }
}
add_action('init', 'mailchimp_campaigns_manager_init');

/**
 * Register REST API routes.
 */
function mailchimp_campaigns_manager_rest_api_init() {
  require_once MCM_PLUGIN_ROOT_DIR . 'src/MailchimpCampaignsManagerRest.php';
  new MailchimpCampaignsManagerRest();
}
add_action('rest_api_init', 'mailchimp_campaigns_manager_rest_api_init');
