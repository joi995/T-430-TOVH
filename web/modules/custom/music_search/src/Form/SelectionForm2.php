<?php
namespace Drupal\music_search\Form;

use AllowDynamicProperties;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\spotify_lookup\SpotifyLookupService;


#[AllowDynamicProperties] class SelectionForm2 extends FormBase
{

  protected $items;

  public static function create(ContainerInterface $container) {
    $session = \Drupal::service('session');
    $items = $session->get('album_selection_items', []);
    $spotifyService = $container->get('spotify_lookup.service');

    // Correct parameter order.
    return new static($spotifyService, $items);
  }

  public function __construct(SpotifyLookupService $spotifyService, array $items = []) {
    $this->spotifyService = $spotifyService;
    $this->items = $items;
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    foreach ($this->items as $item) {
      $form[$item->id] = [
        '#type' => 'container',
        '#attributes' => [
          'class' => ['item-container'],
          'style' => 'border: 1px solid #808080; margin-bottom: 10px; text-align: center;'
          ],
        'thumb' => [
          '#theme' => 'image',
          '#uri' => $item->thumb,
          '#alt' => $item->alt,
          '#attributes' => [
            'style' => 'display: block; margin: 0 auto; max-width: 300px; max-height: 300px;',
          ],
        ],
        'artist_name' => [
          '#type' => 'markup',
          '#markup' => '<p><strong>' . $this->t('Artist: @artist', ['@artist' => $item->artists]) . '</strong></p>',
        ],
        'release_date' => [
          '#type' => 'markup',
          '#markup' => '<p><strong>' . $this->t('Released: @date', ['@date' => $item->release]) . '</strong></p>',
        ],
        'button' => [
          '#type' => 'submit',
          '#title' => $item->label,
          '#description' => $item->description,
          '#value' => $item->label,
          '#name' => $item->id, // Store the artist's ID here.
          '#attributes' => [
            'style' => 'margin-top: -5px;',
          ]
        ],
      ];
    }

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Retrieve the triggering button.
    $button = $form_state->getTriggeringElement();

    // Extract the button's name to determine which album was selected.
    $selected_album_id = $button['#name']; // The button's name corresponds to the album ID.

    // Retrieve album details and associated data
    try {
      $spotify_album_data = $this->spotifyService->getDetails($selected_album_id, 'album');

      // Retrieve and save Artists from the album data.
      $artist_ids = [];
      foreach ($spotify_album_data['artists'] as $artist) {
        $artist_details = $this->spotifyService->getDetails($artist['id'], 'artist'); // Fetch artist details.

        $artist_data = [
          'name' => $artist_details['name'],
          'website' => $artist_details['external_urls']['spotify'] ?? '',
          'picture_url' => $artist_details['images'][0]['url'] ?? null,
          'artist_type' => 'Individual', // Adjust as needed based on additional details.
        ];

        $artist_node_id = \Drupal::service('music_search.service')->saveArtist($artist_data);
        $artist_ids[] = $artist_node_id;
      }

      // Retrieve and save Songs from the album data.
      $song_ids = [];
      foreach ($spotify_album_data['tracks']['items'] as $track) {
        $song_data = [
          'title' => $track['name'],
          'spotify_id' => $track['id'],
          'spotify_embedded' => $this->getSpotifyEmbedCode($track['uri']),
          'song_length' => gmdate('i:s', (int) ($track['duration_ms'] / 1000)),
          'music_category' => null, // Optional: Map to taxonomy term.
        ];

        $song_node_id = \Drupal::service('music_search.service')->saveSong($song_data);
        $song_ids[] = $song_node_id;
      }

      // Prepare the album data for saving.
      $album_data = [
        'title' => $spotify_album_data['name'],
        'artist' => $artist_ids,
        'description' => $spotify_album_data['description'] ?? '',
        'website' => $spotify_album_data['external_urls']['spotify'] ?? null,
        'picture_url' => $spotify_album_data['images'][0]['url'] ?? '',
        'songs' => $song_ids,
        'release_date' => $spotify_album_data['release_date'] ?? null,
      ];

      $musicSearchService = \Drupal::service('music_search.service');
      $album_node_id = $musicSearchService->saveAlbum($album_data);

      \Drupal::messenger()->addStatus($this->t('The Album "@name" was successfully saved with Node ID: @id.', [
        '@name' => $spotify_album_data['name'],
        '@id' => $album_node_id,
      ]));
    } catch (\Exception $e) {
      // Handle errors
      \Drupal::messenger()->addError($this->t('An error occurred while saving the album: @message', [
        '@message' => $e->getMessage(),
      ]));
    }
  }

  public function getFormId() {
    return 'music_search_selection_form_2';
  }

  protected function getSpotifyEmbedCode(string $spotifyUri): string {
    $embedUrl = str_replace(':', '/', $spotifyUri); // Convert Spotify URI to embed URL.
    return 'https://open.spotify.com/embed/' . $embedUrl;
  }

}
