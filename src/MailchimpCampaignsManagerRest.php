<?php

/**
 * Our custom Rest resource.
 *
 * @see https://codex.wordpress.org/Creating_Options_Pages#Example_.232
 */
class MailchimpCampaignsManagerRest extends WP_REST_Controller {

  const WP_REST_PREFIX = '/wp/v2/';

  /**
   * Create our custom REST routes.
   */
  public static function init() {
    $class = get_called_class();

    register_rest_route(MCM_ENDPOINT, '/' . MCM_POST_TYPE, [
      [
        // Get an existing Mailchimp campaign.
        'methods'  => WP_REST_Server::READABLE,
        'callback' => [$class, 'getCampaign'],
      ],
      [
        // Receive a new Mailchimp campaign.
        'methods'  => WP_REST_Server::EDITABLE,
        'callback' => [$class, 'createOrEditCampaign'],
      ],
    ]);
  }

  /**
   * Helper function to be call before anything else.
   *
   * BasicAuth authentication is required in call's headers.
   *
   * WordPress automatically checks permissions.
   *
   * @param WP_REST_Request $post
   * @return WP_Error|bool
   */
  private static function authenticateCall(WP_REST_Request $post) {
    $rest_user = get_option('mailchimp_campaigns_manager')['rest_user'] ?? NULL;
    if (!$rest_user) {
      return new WP_Error('missing_settings', 'Please configure the plugin in WordPress.', ['status' => 401]);
    }

    $given_rest_user = $post->get_param('rest_user');
    if ($given_rest_user !== $rest_user) {
      return new WP_Error('invalid_request', 'Invalid request', ['status' => 401]);
    }

    // Switch identity.
    wp_set_current_user(get_user_by('login', $rest_user));

    return TRUE;
  }

  /**
   * Get a Mailchimp campaign.
   *
   * @param WP_REST_Request $request
   * @return WP_Error|WP_REST_Response
   *
   * @todo Get a campaign by Mailchimp ID.
   */
  public static function getCampaign(WP_REST_Request $post) {
    // Switch identity.
    if ($error = is_wp_error(self::authenticateCall($post))) {
      return $error;
    }

    // GET campaign from within WordPress.
    $request  = new WP_REST_Request('GET', self::WP_REST_PREFIX . MCM_POST_TYPE);
    $response = rest_do_request($request);

    return rest_ensure_response($response);
  }

  /**
   * Receive a Mailchimp campaign.
   *
   * @param WP_REST_Request $request
   * @return WP_Error|WP_REST_Response
   *
   * @todo Use custom post type endpoint.
   * @todo Allow POST, PUT, PATCH.
   */
  public static function createOrEditCampaign(WP_REST_Request $post) {
    // Switch identity.
    if ($error = is_wp_error(self::authenticateCall($post))) {
      return $error;
    }

    // POST campaign from within WordPress.
    $request = new WP_REST_Request('POST', self::WP_REST_PREFIX . MCM_POST_TYPE);
    $request->set_param('title', $post->get_param('title'));
    $request->set_param('status', $post->get_param('status'));
    $request->set_param('content', $post->get_param('content'));
    $request->set_param('excerpt', $post->get_param('excerpt'));
    $response = rest_do_request($request);

    return rest_ensure_response($response);
  }
}
