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
  private function getAccessToken() {
    $config = $this->configFactory->get('spotify_lookup.settings');
    $clientId = $config->get('client_id');
    $clientSecret = $config->get('client_secret');

    $response = $this->httpClient->post('https://accounts.spotify.com/api/token', [
      'headers' => [
        'Authorization' => 'Basic ' . base64_encode("$clientId:$clientSecret"),
      ],
      'form_params' => [
        'grant_type' => 'client_credentials',
      ],
    ]);

    $data = json_decode($response->getBody(), TRUE);
    return $data['access_token'];
  }

  /**
   * Searches Spotify for the given query.
   */
  public function search($query, $type = 'track,album,artist') {
    $accessToken = $this->getAccessToken();
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
    $accessToken = $this->getAccessToken();
    $url = "https://api.spotify.com/v1/{$type}s/{$id}";

    $response = $this->httpClient->get($url, [
      'headers' => [
        'Authorization' => "Bearer $accessToken",
      ],
    ]);

    return json_decode($response->getBody(), TRUE);
  }
}
