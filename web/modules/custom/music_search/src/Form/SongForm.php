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
      $container->get('spotify_lookup.service')
    );
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

    // Call SpotifyLookupService to search.
    try {
      $results = $this->spotifyService->search($query, 'track');
      // Parse the results and display them.
      $this->displayResults($results); // Custom helper method.
    } catch (\Exception $e) {
      \Drupal::messenger()->addError($this->t('An error occurred while searching Spotify: @error', ['@error' => $e->getMessage()]));
    }
  }

  protected function displayResults(array $results) {
    // Process and display results (example: track names and artists).
    $items = [];
    foreach ($results['tracks']['items'] as $item) {
      $track = $item['name'];
      $artists = array_map(fn($artist) => $artist['name'], $item['artists']);
      $items[] = $track . ' by ' . implode(', ', $artists);
    }

    $render_array = [
      '#theme' => 'item_list',
      '#items' => $items,
      '#title' => $this->t('Search Results'),
    ];

    // Render the array using the renderer service.
    $rendered_output = \Drupal::service('renderer')->render($render_array);

    // Add the rendered output to the messenger.
    \Drupal::messenger()->addStatus($rendered_output);
  }
}
