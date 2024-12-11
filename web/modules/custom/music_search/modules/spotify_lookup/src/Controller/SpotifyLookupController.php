<?php

namespace Drupal\spotify_lookup\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\spotify_lookup\SpotifyLookupService;

class SpotifyLookupController extends ControllerBase {
  protected SpotifyLookupService $spotifyService;

  public function __construct(SpotifyLookupService $spotifyService) {
    $this->spotifyService = $spotifyService;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('spotify_lookup.service')
    );
  }
  public function testSpotify(): array {
    $response = $this->spotifyService->search('track', 'Hello');
    return [
      '#type' => 'markup',
      '#markup' => '<pre>' . print_r($response, TRUE) . '</pre>',
    ];
  }
}
