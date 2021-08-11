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
 * Import and display your Mailchimp campaigns in WordPress with simple shortcodes.
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {exit;}

define('MCM_TEXT_DOMAIN', 'mailchimp_campaigns_manager');
define('MCM_ENDPOINT', 'mailchimp-campaigns-manager/v1');
define('MCM_POST_TYPE', 'campaign');
define('MCM_ROLE_ID', 'mailchimp_campaigns_manager');

$src = plugin_dir_path(__FILE__) . 'src/';
require_once $src . 'MailchimpCampaignsManager.php';
require_once $src . 'MailchimpCampaignsManagerRest.php';

register_activation_hook(__FILE__, ['MailchimpCampaignsManager', 'add_role']);

add_action('init', ['MailchimpCampaignsManager', 'register_post_type']);
add_action('init', ['MailchimpCampaignsManager', 'add_role']);

add_action('rest_api_init', ['MailchimpCampaignsManagerRest', 'init']);

if (is_admin() || (defined('WP_CLI') && WP_CLI)) {
  require_once $src . 'MailchimpCampaignsManagerSettings.php';
  add_action('init', ['MailchimpCampaignsManagerSettings', 'init']);
}
