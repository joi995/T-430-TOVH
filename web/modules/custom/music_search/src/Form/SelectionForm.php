<?php
namespace Drupal\music_search\Form;

use AllowDynamicProperties;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


#[AllowDynamicProperties] class SelectionForm extends FormBase {

  protected $items;

  public static function create(ContainerInterface $container) {
    $session = \Drupal::service('session');
    $items = $session->get('artist_selection_items', []); // Retrieve items from session.
    return new static($items);
  }

  public function __construct(array $items = []) {
    $this->items = $items;
  }

  public function getFormId() {
    return 'music_search_selection_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    foreach ($this->items as $item) {
      $form[$item->id] = [
        // Group each button/image in a container.
        '#type' => 'container',
        'thumb' => [
          '#theme' => 'image',
          '#uri' => $item->thumb, // The image URL or URI.
          '#alt' => $item->alt, // Fallback alt text.
          '#attributes' => [
            'style' => 'max-width: 300px; max-height: 300px;', // Optional: Resize with inline styles.
          ],
        ],
        'button' => [
          '#type' => 'submit',
          '#title' => $item->label,
          '#description' => $item->description,
          '#value' => $item->label,
        ],
      ];
    }

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {}
}
