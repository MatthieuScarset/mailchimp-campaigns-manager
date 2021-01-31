<?php

/**
 * Mailchimp Campaigns Manager settings page.
 *
 * @see https://codex.wordpress.org/Creating_Options_Pages#Example_.232
 */
class MailchimpCampaignsManagerRest {

  const MCM_API_ENDPOINT = 'mailchimp-campaigns-manager/v1';

  /**
   * Start up
   */
  public function __construct() {
    $this->registerRoutes();
  }

  /**
   * Create our custom REST routes.
   */
  public function registerRoutes() {
    // Receive a new Mailchimp campaign.
    register_rest_route(self::MCM_API_ENDPOINT, '/campaign/create', [
      'methods'  => 'POST',
      'callback' => [$this, 'campaignCreate'],
    ]);
  }

  /**
   * Receive a new Mailchimp campaign.
   *
   * @param WP_REST_Request $request
   * @return WP_Error|WP_REST_Response
   * @todo Configure REST user and validate API key in settings.
   */
  public function campaignCreate(WP_REST_Request $post) {
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

    // POST campaign from within WordPress.
    // BasicAuth authentication is required.
    // WordPress automatically check permission in rest_do_request().
    $request = new WP_REST_Request('POST', '/wp/v2/posts');
    $request->set_param('title', $post->get_param('title'));
    $request->set_param('status', $post->get_param('status'));
    $request->set_param('content', $post->get_param('content'));
    $request->set_param('excerpt', $post->get_param('excerpt'));
    $response = rest_do_request($request);

    return rest_ensure_response($response);
  }
}
