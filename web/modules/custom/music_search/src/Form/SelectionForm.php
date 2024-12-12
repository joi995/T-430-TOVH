<?php
namespace Drupal\music_search\Form;

use AllowDynamicProperties;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


#[AllowDynamicProperties] class SelectionForm extends FormBase
{

  protected $items;

  public static function create(ContainerInterface $container)
  {
    $session = \Drupal::service('session');
    $items = $session->get('artist_selection_items', []); // Retrieve items from session.
    return new static($items);
  }

  public function __construct(array $items = [])
  {
    $this->items = $items;
  }

  public function getFormId()
  {
    return 'music_search_selection_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    foreach ($this->items as $item) {
      $form[$item->id] = [
        '#type' => 'container',
        'thumb' => [
          '#theme' => 'image',
          '#uri' => $item->thumb,
          '#alt' => $item->alt,
          '#attributes' => [
            'style' => 'max-width: 300px; max-height: 300px;',
          ],
        ],
        'button' => [
          '#type' => 'submit',
          '#title' => $item->label,
          '#description' => $item->description,
          '#value' => $item->label,
          '#name' => $item->id, // Store the artist's ID here.
        ],
      ];
    }

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    // Retrieve the triggering button.
    $button = $form_state->getTriggeringElement();

    // Extract the button's name to determine which artist was selected.
    $selected_item_id = $button['#name']; // The button's name corresponds to the item's ID.

    // Find the artist data in $this->items using the ID.
    $selected_item = null;
    foreach ($this->items as $item) {
      if ($item->id === $selected_item_id) {
        $selected_item = $item;
        break;
      }
    }

    if ($selected_item) {
      // Prepare the artist data for saving.
      $artist_data = [
        'name' => $selected_item->label, // Artist name.
        'description' => $selected_item->description, // Artist description.
        'website' => $selected_item->url ?? null, // Website, if available.
        'picture' => $selected_item->thumb, // Image URL.
        'artist_type' => 'Solo', // Example static data.
      ];

      // Save the artist using MusicSearchService.
      try {
        /** @var \Drupal\music_search\MusicSearchService $musicSearchService */
        $musicSearchService = \Drupal::service('music_search.service');
        $node_id = $musicSearchService->saveArtist($artist_data);

        // Inform the user that the artist was saved successfully.
        \Drupal::messenger()->addStatus($this->t('The artist "@name" was successfully saved with Node ID: @id.', [
          '@name' => $selected_item->label,
          '@id' => $node_id,
        ]));
      } catch (\Exception $e) {
        // Handle errors and notify the user.
        \Drupal::messenger()->addError($this->t('An error occurred while saving the artist: @error', [
          '@error' => $e->getMessage(),
        ]));
      }
    } else {
      // No match found, notify the user.
      \Drupal::messenger()->addError($this->t('The selected artist could not be found.'));
    }
  }
}
