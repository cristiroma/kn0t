<?php

namespace Drupal\staff_time_attendance\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;

class TimeEntryForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    /* @var $entity \Drupal\staff_time_attendance\Entity\TimeEntry */
    $entity = $this->entity;
    unset($form['changed_uid']);
    $u = \Drupal::currentUser();
    $user = User::load($u->id());
    $entity->setOwner($user);
    $form['uid']['#access'] = FALSE;
    $form['uid']['#value'] = $user->id();
    $form['actions']['submit']['#value'] = $this->t('Punch time!');
    return $form;
  }



  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $status = parent::save($form, $form_state);
    $entity = $this->entity;
    if ($status == SAVED_UPDATED) {
      drupal_set_message($this->t('The time entry %feed has been updated.', ['%feed' => $entity->toLink()->toString()]));
    } else {
      drupal_set_message($this->t('The time entry %feed has been added.', ['%feed' => $entity->toLink()->toString()]));
    }

    $form_state->setRedirectUrl($this->entity->toUrl('collection'));
    return $status;
  }

}