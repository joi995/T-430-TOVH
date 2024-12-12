<?php
namespace Drupal\music_search\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\spotify_lookup\SpotifyLookupService;
use Drupal\Core\Render\RendererInterface;

class AlbumForm extends FormBase {

  /**
   * Spotify lookup service.
   *
   * @var \Drupal\spotify_lookup\SpotifyLookupService
   */
  protected SpotifyLookupService $spotifyService;

  /**
   * Renderer service.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected RendererInterface $renderer;

  /**
   * Constructor.
   */
  public function __construct(SpotifyLookupService $spotifyService, RendererInterface $renderer) {
    $this->spotifyService = $spotifyService;
    $this->renderer = $renderer;
  }

  /**
   * Dependency injection container.
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('spotify_lookup.service'),
      $container->get('renderer')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'music_search_album_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['Album'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Album Name'),
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

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Extract user input.
    $album = $form_state->getValue('Album');
    $artist = $form_state->getValue('Artist');

    // Build the Spotify query.
    $query = "album:$album artist:$artist";

    try {
      // Call SpotifyLookupService to search albums.
      $results = $this->spotifyService->search($query, 'album');

      // Process and render the results.
      $render_array = $this->displayResults($results);
      $rendered_output = $this->renderer->render($render_array);

      // Display rendered output as a status message.
      \Drupal::messenger()->addStatus($rendered_output);
    } catch (\Exception $e) {
      // Handle errors gracefully and inform the user.
      \Drupal::messenger()->addError($this->t('An error occurred while searching Spotify: @error', ['@error' => $e->getMessage()]));
    }
  }

  /**
   * Helper method to process and display search results.
   *
   * @param array $results
   *   The results from the Spotify API call.
   *
   * @return array
   *   A renderable array of the results.
   */
  protected function displayResults(array $results): array {
    $items = [];

    // Extract album data from the Spotify API response.
    if (!empty($results['albums']['items'])) {
      foreach ($results['albums']['items'] as $item) {
        $album_name = $item['name'];
        $artists = array_map(fn($artist) => $artist['name'], $item['artists']);
        $url = $item['external_urls']['spotify'] ?? '#';

        // Prepare display text with album name and artists.
        $items[] = $this->t('<a href="@url" target="_blank">@name</a> by @artists', [
          '@url' => $url,
          '@name' => $album_name,
          '@artists' => implode(', ', $artists),
        ]);
      }
    } else {
      $items[] = $this->t('No albums found for your query.');
    }

    // Return the render array as an item list.
    return [
      '#theme' => 'item_list',
      '#items' => $items,
      '#title' => $this->t('Search Results'),
    ];
  }
}
