<?php
namespace Drupal\music_search\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;

class SongForm extends FormBase {
  public function getFormId() {
    return 'music_search_song_form';
  }
  public function buildForm(array $form, FormStateInterface $form_state) {
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
  public function submitForm(array &$form, FormStateInterface $form_state) {}
}
