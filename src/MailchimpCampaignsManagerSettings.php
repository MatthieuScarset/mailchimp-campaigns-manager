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
   * Start up function.
   */
  public static function init() {
    $class = get_called_class();

    add_filter('plugin_action_links', [$class, 'settings_link'], 10, 2);
    add_action('admin_menu', [$class, 'add_plugin_page']);
    add_action('admin_init', [$class, 'page_init']);
  }

  /**
   * Display a direct link to settings from plugin page.
   *
   * @return array
   */
  public static function settings_link($links, $file) {
    if ($file == 'mailchimp-campaigns-manager/mailchimp-campaigns-manager.php') {
      $url     = 'options-general.php?page=mailchimp-campaigns-manager-settings';
      $links[] = '<a href="' . $url . '">' . __('Settings', 'mailchimp_campaigns_manager') . '</a>';

      $url     = 'edit.php?post_type=campaign';
      $links[] = '<a href="' . $url . '">' . __('Campaigns', 'mailchimp_campaigns_manager') . '</a>';
    }

    return $links;
  }

  /**
   * Add options page
   */
  public static function add_plugin_page() {
    $class = get_called_class();

    // This page will be under "Settings"
    add_options_page(
      'Settings',
      'Mailchimp Campaigns',
      'manage_options',
      'mailchimp-campaigns-manager-settings',
      [$class, 'create_admin_page']
    );
  }

  /**
   * Options page callback
   */
  public static function create_admin_page() {
    print '<div class="wrap">
            <h1>Mailchimp Campaigns Manager</h1>
            <form method="post" action="options.php">';

    // Display setting fields.
    settings_fields('mailchimp_campaigns_manager');
    do_settings_sections('mailchimp-campaigns-manager-settings');
    submit_button();

    print '
            </form>
        </div>';
  }

  /**
   * Register and add settings
   */
  public static function page_init() {
    $class = get_called_class();

    register_setting(
      'mailchimp_campaigns_manager',
      'mailchimp_campaigns_manager',
      [$class, 'sanitize']
    );

    add_settings_section(
      'settings',
      'Settings',
      [$class, 'print_section_info'],
      'mailchimp-campaigns-manager-settings'
    );

    add_settings_field(
      'rest_user',
      'Rest API user',
      [$class, 'rest_user_callback'],
      'mailchimp-campaigns-manager-settings',
      'settings'
    );
  }

  /**
   * Sanitize each setting field as needed
   *
   * @param array $input Contains all settings fields as array keys
   */
  public static function sanitize($input) {
    $new_input = [];
    if (isset($input['rest_user'])) {
      $new_input['rest_user'] = sanitize_text_field($input['rest_user']);
    }

    return $new_input;
  }

  /**
   * Print the Section text
   */
  public static function print_section_info() {
    print 'Configure this plugin to synchronize campaigns on this website.';
  }

  /**
   * Get the settings option array and print one of its values
   */
  public static function rest_user_callback() {
    $txt_domain    = 'mailchimp_campaigns_manager';
    $role_id       = 'mailchimp_campaigns_manager';
    $users         = get_users(['role' => $role_id]);
    $selected_user = esc_attr(
      get_option('mailchimp_campaigns_manager')['rest_user'] ?? ''
    );

    printf(
      '<select id="rest_user" name="mailchimp_campaigns_manager[rest_user]" required %s>',
      empty($users) ? 'disabled' : ''
    );

    if (!empty($users)) {
      // Empty default value.
      printf('<option value="">%s</option>', __('- Select -', $txt_domain));

      foreach ($users as $user) {
        $user_login = $user->get('user_login');

        printf(
          '<option value="%s" %s>%s</option>',
          $user_login,
          $selected_user == $user_login ? 'selected' : '',
          $user_login,
        );
      }
    }

    if (empty($users)) {
      printf('<option value="">%s</option>', __('- Empty -', $txt_domain));
    }

    print '</select>';

    if (empty($users)) {
      printf(
        '<p><a href="%s">Create at least one user</a> with <em>%s</em> role.</p>',
        'user-new.php',
        $role_id
      );
      printf('<p>Please, use a strong password.</p>', );
    }
  }
}
