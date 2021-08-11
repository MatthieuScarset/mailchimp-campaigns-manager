<?php

/**
 * Register our custom structure on install.
 *
 * @see mailchimp_campaigns_manager_activate()
 */
class MailchimpCampaignsManager {

  /**
   * Get list of labels for our campaign post type.
   *
   * @return array
   */
  public static function getLabels() {
    $txt_domain = MCM_TEXT_DOMAIN;
    $post_type  = MCM_POST_TYPE;

    return [
      'name'                  => _x(ucfirst($post_type), 'Post type general name', $txt_domain),
      'singular_name'         => _x($post_type, 'Post type singular name', $txt_domain),
      'menu_name'             => _x(ucfirst($post_type), 'Admin Menu text', $txt_domain),
      'name_admin_bar'        => _x(ucfirst($post_type), 'Add New on Toolbar', $txt_domain),
      'add_new'               => __('Add New', $txt_domain),
      'add_new_item'          => __('Add New ' . $post_type, $txt_domain),
      'new_item'              => __('New ' . $post_type, $txt_domain),
      'edit_item'             => __('Edit ' . $post_type, $txt_domain),
      'view_item'             => __('View ' . $post_type, $txt_domain),
      'all_items'             => __('All ' . $post_type, $txt_domain),
      'search_items'          => __('Search ' . $post_type, $txt_domain),
      'parent_item_colon'     => __('Parent ' . $post_type . ':', $txt_domain),
      'not_found'             => __('No ' . $post_type . ' found.', $txt_domain),
      'not_found_in_trash'    => __('No ' . $post_type . ' found in Trash.', $txt_domain),
      'featured_image'        => _x(ucfirst($post_type) . ' cover image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', $txt_domain),
      'set_featured_image'    => _x('Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', $txt_domain),
      'remove_featured_image' => _x('Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', $txt_domain),
      'use_featured_image'    => _x('Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', $txt_domain),
      'archives'              => _x(ucfirst($post_type) . ' archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', $txt_domain),
      'insert_into_item'      => _x('Insert into ' . $post_type, 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', $txt_domain),
      'uploaded_to_this_item' => _x('Uploaded to this ' . $post_type, 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', $txt_domain),
      'filter_items_list'     => _x('Filter ' . $post_type . ' list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', $txt_domain),
      'items_list_navigation' => _x(ucfirst($post_type) . ' list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', $txt_domain),
      'items_list'            => _x(ucfirst($post_type) . ' list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', $txt_domain),
    ];
  }

  /**
   * Get list of custom campaigns fields.
   *
   * @return array
   */
  public static function getMetaFields() {
    $txt_domain = MCM_TEXT_DOMAIN;

    return [
      'id'               => __('ID', $txt_domain),
      'type'             => __('Type', $txt_domain),
      'status'           => __('Status', $txt_domain),
      'create_time'      => __('Created on', $txt_domain),
      'send_time'        => __('Sent on', $txt_domain),
      'emails_sent'      => __('Emails sent', $txt_domain),
      'delivery_status'  => __('Delivery status', $txt_domain),
      'content_type'     => __('Content type', $txt_domain),
      'archive_url'      => __('Archive URL', $txt_domain),
      'long_archive_url' => __('Archive URL (long)', $txt_domain),
      // Content
      'plain_text'       => __('Plain text', $txt_domain),
      'content_html'     => __('HTML', $txt_domain),
      // Lists related
      'recipients'       => __('Recipients', $txt_domain),
      'list_id'          => __('List ID', $txt_domain),
      'list_name'        => __('List name', $txt_domain),
      'segment_text'     => __('Segment', $txt_domain),
      'recipient_count'  => __('Recipients', $txt_domain),
      // Extra campaign settings
      'settings'         => __('Settings', $txt_domain),
      'tracking'         => __('Tracking', $txt_domain),
      'social_card'      => __('Social card', $txt_domain),
      'report_summary'   => __('Report summary', $txt_domain),
      // Help related
      '__links'          => __('Action links', $txt_domain),
      '_edit_lock'       => __('Edit lock', $txt_domain),
      '_edit_last'       => __('Edit last', $txt_domain),
    ];
  }

  /**
   * Register our custom post type.
   *
   * @see https://developer.wordpress.org/reference/functions/register_post_type/
   */
  public static function register_post_type() {
    $post_type = MCM_POST_TYPE;
    if (post_type_exists($post_type)) {
      return;
    }

    $args = [
      'labels'                => self::getLabels(),
      'public'                => true,
      'has_archive'           => true,
      'show_ui'               => true,
      'show_in_menu'          => true,
      'query_var'             => true,
      'rewrite'               => ['slug' => $post_type],
      'capability_type'       => [$post_type, $post_type . 's'],
      'map_meta_cap'          => true,
      'hierarchical'          => false,
      'menu_icon'             => 'dashicons-email',
      'supports'              => ['title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'custom-fields'],
      'show_in_rest'          => true,
      'rest_base'             => $post_type,
      'rest_controller_class' => 'MailchimpCampaignsManagerRest',
    ];

    register_post_type($post_type, $args);

    foreach (self::getMetaFields() as $name => $description) {
      $args = [
        // 'sanitize_callback' => 'sanitize_text_field',
        // 'auth_callback' => 'authorize_my_meta_key',
        // 'single' => true,
        'type'         => 'string',
        'description'  => $description,
        'show_in_rest' => true,
      ];

      $meta_key = 'mcm_' . $name;
      register_meta($post_type, $meta_key, $args);
    }

    // Clear caches.
    flush_rewrite_rules();
  }

  /**
   * Register our custom role.
   */
  public static function add_role() {
    $role_id = MCM_ROLE_ID;
    if (self::role_exists($role_id)) {
      return;
    }

    $role_capabilities = [
      // Disable defaults.
      'read'          => FALSE,
      'edit_posts'    => FALSE,
      'delete_posts'  => FALSE,
      'publish_posts' => FALSE,
      'upload_files'  => FALSE,
      // Custom caps.
      'level_2'       => TRUE,
      'level_1'       => TRUE,
      'level_0'       => TRUE,
    ];

    add_role($role_id, 'Mailchimp Campaigns Manager', $role_capabilities);

    $post_type = 'campaign';

    $admin_capabilities = array(
      'read',
      'read_' . $post_type,
      'read_private_' . $post_type . 's',
      'edit_' . $post_type,
      'edit_' . $post_type . 's',
      'edit_others_' . $post_type . 's',
      'edit_published_' . $post_type . 's',
      'publish_' . $post_type . 's',
      'delete_others_' . $post_type . 's',
      'delete_private_' . $post_type . 's',
      'delete_published_' . $post_type . 's',
    );

    // Allow custom roles to access Campaigns menu.
    $admin_roles = get_option('mailchimp_campaigns_manager')['admin_roles'] ?? [];
    if (!in_array($admin_roles, ['administrator'])) {
      $admin_roles[] = 'administrator';
    }

    foreach ($admin_roles as $admin_role_id) {
      if ($admin_role = get_role($admin_role_id)) {
        foreach ($admin_capabilities as $admin_capability_name) {
          $admin_role->add_cap($admin_capability_name);
        }
      }
    }
  }

  /**
   * Helper function to check a role.
   *
   * @param string $role_id
   *  A given role ID.
   *
   * @return bool
   *
   * @see https://hugh.blog/2015/02/12/wordpress-check-user-role-exists/
   */
  private static function role_exists($role_id) {
    return isset($GLOBALS['wp_roles']) && $GLOBALS['wp_roles']->is_role($role_id);
  }

}
