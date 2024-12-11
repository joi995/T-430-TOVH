<?php
namespace Drupal\music_search\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Controller Music Search
 */
class MusicSearchController extends ControllerBase {
  public function MusicSearch() {
    return [
      '#theme' => 'music_search',
    ];
  }
}
