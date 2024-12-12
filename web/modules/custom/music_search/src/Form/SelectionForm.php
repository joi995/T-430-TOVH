<?php
namespace Drupal\music_search\Form;

use AllowDynamicProperties;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

#[AllowDynamicProperties] class SelectionForm extends FormBase {
  public function __construct($items){
    $this -> items = $items;
  }
  public function getFormId() {
    return 'music_search_selection_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    foreach ($this -> items as $item) {
      $form[$item -> id()] = [
        '#type' => 'submit',
        '#title' => $item -> label(),
        '#description' => $item -> getDescription(),
        '#value' => $item -> getValue(),
      ];
    }
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {}
}
