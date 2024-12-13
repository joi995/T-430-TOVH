<?php
namespace Drupal\music_search\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\spotify_lookup\SpotifyLookupService;

class SongForm extends FormBase {
  protected SpotifyLookupService $spotifyService;

  public function __construct(SpotifyLookupService $spotifyService) {
    $this->spotifyService = $spotifyService;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('spotify_lookup.service'));
      $session = \Drupal::service('session');

      // Merge artist and track items stored in session (if both exist).
      $artist_items = $session->get('artist_selection_items', []);
      $track_items = $session->get('track_selection_items', []);
      $items = array_merge($artist_items, $track_items);

      return new static($items);
    }
  public function getFormId() {
    return 'music_search_song_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state): array
  {
    $form['Song'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Song Name'),
      '#required' => TRUE,
    ];
    $form['Artist'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Artist Name'),
      '#required' => TRUE,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];
    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state): void
  {
    // Get user input.
    $song = $form_state->getValue('Song');
    $artist = $form_state->getValue('Artist');

    // Build the Spotify query.
    $query = "track:$song artist:$artist";

    try {
      // Call SpotifyLookupService to search tracks.
      $results = $this->spotifyService->search($query, 'track');

      // Process the track results into items compatible with `SelectionForm`.
      $items = $this->processTracks($results);

      // Store the tracks in the session (same as for artists).
      $session = \Drupal::service('session');
      $session->set('song_selection_items', $items);

      // Redirect to SelectionForm to display the tracks.
      $form_state->setRedirect('music_search.select_items1');
    } catch (\Exception $e) {
      \Drupal::messenger()->addError($this->t('An error occurred while searching Spotify: @error', ['@error' => $e->getMessage()]));
    }
  }

  private function processTracks(array $results): array
  {
    $items = [];

    // Loop through the track items from the Spotify search results.
    foreach ($results['tracks']['items'] as $item) {
      $track = new \stdClass();
      $track->id = $item['id'];
      $track->label = $item['name']; // Track name.
      $artist_names = array_map(fn($artist) => $artist['name'], $item['artists']);
      $track->description = $this->t('By: @artists', ['@artists' => implode(', ', $artist_names)]);
      $track->value = $item['external_urls']['spotify'] ?? '#'; // Spotify URL for the track.
      $track->thumb = $item['album']['images'][0]['url'] ?? '#'; // Album image (thumbnail).
      $track->alt = $this->t('@track by @artists album cover', ['@track' => $item['name'], '@artists' => implode(', ', $artist_names)]);
      $track->url = $item['external_urls']['spotify'] ?? '#';

      $items[] = $track; // Add the formatted track data.
    }

    return $items;
  }
}
