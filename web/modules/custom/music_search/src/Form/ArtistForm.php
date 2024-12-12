<?php
namespace Drupal\music_search\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\spotify_lookup\SpotifyLookupService;
use Drupal\Core\Render\RendererInterface;

class ArtistForm extends FormBase {

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
    return 'music_search_artist_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
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
    // Get user input.
    $artist = $form_state->getValue('Artist');

    // Build the Spotify query.
    $query = "artist:$artist";

    try {
      // Call SpotifyLookupService to search for artists.
      $results = $this->spotifyService->search($query, 'artist');

      // Process and render the results.
      $render_array = $this->displayResults($results);
      $rendered_output = $this->renderer->render($render_array);

      // Display rendered output as a status message.
      \Drupal::messenger()->addStatus($rendered_output);
    } catch (\Exception $e) {
      // Handle any errors.
      \Drupal::messenger()->addError($this->t('An error occurred while searching Spotify: @error', ['@error' => $e->getMessage()]));
    }
  }

  /**
   * Helper method to process search results and display them.
   *
   * @param array $results
   *   The results from Spotify API.
   *
   * @return array
   *   Renderable array of the results.
   */
  protected function displayResults(array $results): array {
    $items = [];

    // Extract artist data from the Spotify API response.
    if (!empty($results['artists']['items'])) {
      foreach ($results['artists']['items'] as $item) {
        $artist_name = $item['name'];
        $popularity = $item['popularity'] ?? 'N/A';
        $url = $item['external_urls']['spotify'] ?? '#';

        // Prepare the display text.
        $items[] = $this->t('<a href="@url" target="_blank">@name</a> (Popularity: @popularity)', [
          '@url' => $url,
          '@name' => $artist_name,
          '@popularity' => $popularity,
        ]);
      }
    } else {
      $items[] = $this->t('No artists found for your query.');
    }

    // Return the render array as an item list.
    return [
      '#theme' => 'item_list',
      '#items' => $items,
      '#title' => $this->t('Search Results'),
    ];
  }
}
