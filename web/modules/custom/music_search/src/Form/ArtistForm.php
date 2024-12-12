<?php
namespace Drupal\music_search\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\spotify_lookup\SpotifyLookupService;
use Drupal\Core\Render\RendererInterface;
use Drupal\music_search\Form\SelectionForm;

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
    $artist = $form_state->getValue('Artist');
    $query = "artist:$artist";

    try {
      $results = $this->spotifyService->search($query, 'artist');
      $items = $this->processArtists($results);

      // Store items in the session.
      $session = \Drupal::service('session');
      $session->set('artist_selection_items', $items);

      // Redirect to SelectionForm.
      $form_state->setRedirect('music_search.select_items');
    } catch (\Exception $e) {
      \Drupal::messenger()->addError($this->t('An error occurred: @error', ['@error' => $e->getMessage()]));
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
    SelectionForm::create($items);
    return [
      '#theme' => 'item_list',
      '#items' => $items,
      '#title' => $this->t('Search Results'),
    ];
  }

  private function processArtists(mixed $results) {
    $items = [];

    foreach ($results['artists']['items'] as $item) {
      // Create an object representing each artist's data.
      $artist = new \stdClass();
      $artist->id = $item['id'];
      $artist->label = $item['name'];
      $artist->description = $this->t('Popularity: @popularity', ['@popularity' => $item['popularity'] ?? 'N/A']);
      $artist->value = $item['external_urls']['spotify'] ?? '#';
      $artist->url = $item['external_urls']['spotify'] ?? '#';
      $artist->thumb = $item['images'][0]['url'] ?? '#';

      $items[] = $artist;
    }

    return $items;
  }
}
