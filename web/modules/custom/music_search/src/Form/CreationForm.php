<?php
namespace Drupal\music_search\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;

class CreationForm extends FormBase {
  public function getFormId() {
    return 'music_search_song_form';
  }
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['content'] = [
      '#type' => 'select',
      '#title' => $this->t('Type'),
      '#options' => ['album' =>$this->t('Album'), 'song'=>$this->t('Song'), 'artist'=>$this->t('Artist')],
      '#required' => TRUE,
      '#default_value' => t('Album'),
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];
    return $form;
  }
  public function submitForm(array &$form, FormStateInterface $form_state) {
    switch ($form_state->getValue('content')) {
      case 'album':
        $url = 'music_search.create_album';
        break;
      case 'song':
          $url = 'music_search.create_song';
          break;
      case 'artist':
        $url = 'music_search.create_artist';
    };
    $form_state->setRedirect($url);

  }
}
