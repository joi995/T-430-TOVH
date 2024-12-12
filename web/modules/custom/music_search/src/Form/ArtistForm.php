<?php
namespace Drupal\music_search\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;

class ArtistForm extends FormBase {
  public function getFormId() {
    return 'music_search_artist_form';
  }
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
  public function submitForm(array &$form, FormStateInterface $form_state) {}
}
