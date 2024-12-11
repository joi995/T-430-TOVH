<?php

namespace Drupal\spotify_lookup;

use GuzzleHttp\ClientInterface;
use Drupal\Core\Config\ConfigFactoryInterface;

class SpotifyLookupService {
  protected ClientInterface $httpClient;
  protected ConfigFactoryInterface $configFactory;

  public function __construct(ClientInterface $httpClient, ConfigFactoryInterface $configFactory) {
    $this->httpClient = $httpClient;
    $this->configFactory = $configFactory;
  }

  /**
   * Gets an access token from Spotify API.
   */
  public function getToken(): string {
    $config = $this->configFactory->get('spotify_lookup.settings');

    $client_id = $config->get('client_id');
    $client_secret = $config->get('client_secret');
    $auth_url = $config->get('auth_url');

    // Validate configuration values
    if (empty($client_id) || empty($client_secret) || empty($auth_url)) {
      throw new \Exception('Spotify API credentials or auth_url are missing in configuration.');
    }

    if (!filter_var($auth_url, FILTER_VALIDATE_URL)) {
      throw new \InvalidArgumentException('The auth_url must be a valid URL.');
    }

    // Make the POST request to get the token
    try {
      $response = $this->httpClient->post($auth_url, [
        'form_params' => [
          'grant_type' => 'client_credentials',
        ],
        'headers' => [
          'Authorization' => 'Basic ' . base64_encode("$client_id:$client_secret"),
          'Content-Type' => 'application/x-www-form-urlencoded',
        ],
      ]);

      $data = json_decode($response->getBody(), true);
      return $data['access_token'] ?? '';
    } catch (\Exception $e) {
      throw new \Exception('Error acquiring Spotify token: ' . $e->getMessage());
    }
  }

  /**
   * Searches Spotify for the given query.
   */
  public function search($query, $type = 'track,album,artist') {
    $accessToken = $this->getToken();
    $response = $this->httpClient->get('https://api.spotify.com/v1/search', [
      'headers' => [
        'Authorization' => "Bearer $accessToken",
      ],
      'query' => [
        'q' => $query,
        'type' => $type,
      ],
    ]);

    return json_decode($response->getBody(), TRUE);
  }

  /**
   * Fetches detailed data by ID.
   */
  public function getDetails($id, $type) {
    $accessToken = $this->getToken();
    $url = "https://api.spotify.com/v1/{$type}s/{$id}";

    $response = $this->httpClient->get($url, [
      'headers' => [
        'Authorization' => "Bearer $accessToken",
      ],
    ]);

    return json_decode($response->getBody(), TRUE);
  }
}
