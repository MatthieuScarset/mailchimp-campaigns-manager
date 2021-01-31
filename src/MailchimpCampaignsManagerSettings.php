<?php

/**
 * Mailchimp Campaigns Manager settings page.
 *
 * @see https://codex.wordpress.org/Creating_Options_Pages#Example_.232
 */
class MailchimpCampaignsManagerSettings {

  /**
   * Holds the values to be used in the fields callbacks
   */
  private $options;

  /**
   * Start up
   */
  public function __construct() {
    add_action('admin_menu', [$this, 'add_plugin_page']);
    add_action('admin_init', [$this, 'page_init']);
  }

  /**
   * Add options page
   */
  public function add_plugin_page() {
    // This page will be under "Settings"
    add_options_page(
      'Settings',
      'Mailchimp Settings',
      'manage_options',
      'mailchimp-campaigns-manager-settings',
      [$this, 'create_admin_page']
    );
  }

  /**
   * Options page callback
   */
  public function create_admin_page() {
    // Set class property
    $this->options = get_option('mailchimp_campaigns_manager');
    ?>
        <div class="wrap">
            <h1>Mailchimp Campaigns Manager</h1>
            <form method="post" action="options.php">
            <?php

    // Display setting fields.
    settings_fields('mailchimp_campaigns_manager');
    do_settings_sections('mailchimp-campaigns-manager-settings');
    submit_button();

    ?>
            </form>
        </div>
        <?php
}

  /**
   * Register and add settings
   */
  public function page_init() {
    register_setting(
      'mailchimp_campaigns_manager',
      'mailchimp_campaigns_manager',
      [$this, 'sanitize']
    );

    add_settings_section(
      'credentials',
      'Application credentials',
      [$this, 'print_section_info'],
      'mailchimp-campaigns-manager-settings'
    );

    add_settings_field(
      'rest_user',
      'API user',
      [$this, 'rest_user_callback'],
      'mailchimp-campaigns-manager-settings',
      'credentials'
    );
  }

  /**
   * Sanitize each setting field as needed
   *
   * @param array $input Contains all settings fields as array keys
   */
  public function sanitize($input) {
    $new_input = [];
    if (isset($input['rest_user'])) {
      $new_input['rest_user'] = sanitize_text_field($input['rest_user']);
    }

    return $new_input;
  }

  /**
   * Print the Section text
   */
  public function print_section_info() {
    print 'Enter your secret credentials below:';
  }

  /**
   * Get the settings option array and print one of its values
   */
  public function rest_user_callback() {
    printf(
      '<input type="text" id="rest_user" name="mailchimp_campaigns_manager[rest_user]" value="%s" required />',
      isset($this->options['rest_user']) ? esc_attr($this->options['rest_user']) : ''
    );
  }
}
