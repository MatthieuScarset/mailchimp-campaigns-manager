<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {exit;}

// Make sure we're uninstalling
if (!defined('WP_UNINSTALL_PLUGIN')) {
  return false;
}

// Delete our custom options.
delete_option('mailchimp_campaigns_manager');
