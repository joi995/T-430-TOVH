<?php
namespace Drupal\music_search\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class CreationForm extends FormBase {
  public function getFormId() {}
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['type'] = [
      $this->t('type');
  }
  public function submitForm(array &$form, FormStateInterface $form_state) {}
}
