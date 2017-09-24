<?php

namespace Drupal\staff_time_attendance\Entity;


use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\user\UserInterface;

/**
 * Defines the StaffTimeAttendanceEntity entity.
 *
 * @ingroup knot
 *
 * @ContentEntityType(
 *   id = "staff_time_entry",
 *   label = @Translation("Time entry"),
 *   base_table = "staff_time_entry",
 *   fieldable = TRUE,
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid"
 *   },
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\staff_time_attendance\Entity\Controller\TimeEntryListBuilder",
 *     "views_data" = "Drupal\staff_time_attendance\Views\TimeEntryEntityViewsData",
 *     "form" = {
 *       "add" = "Drupal\staff_time_attendance\Form\TimeEntryForm",
 *       "delete" = "Drupal\staff_time_attendance\Form\TimeEntryDeleteForm",
 *     },
 *    "access" = "Drupal\staff_time_attendance\TimeEntryAccessControlHandler",
 *   },
 *   links = {
 *     "canonical" = "/time/{staff_time_entry}",
 *     "edit-form" = "/time/{staff_time_entry}/edit",
 *     "delete-form" = "/time/{staff_time_entry}/delete",
 *     "collection" = "/time/list"
 *   },
 *   admin_permission = "administer time entry system"
 * )
 */
class TimeEntry extends ContentEntityBase implements TimeEntryInterface {

  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    // Standard field, used as unique if primary index.
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setRequired(TRUE)
      ->setTranslatable(FALSE)
      ->setDescription(t('The unique ID of the entity.'))
      ->setReadOnly(TRUE);

    // Standard field, unique outside of the scope of the current project.
    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The unique UUID of the time entry entity'))
      ->setReadOnly(TRUE);

    // Owner of this time record.
    // Entity reference field, holds the reference to the user object.
    // The view shows the user name field of the user.
    // The form presents a auto complete field for the user name.
    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setStorageRequired(TRUE) // @todo still created with NOT NULL
      ->setLabel(t('User'))
      ->setRequired(TRUE)
      ->setTranslatable(FALSE)
      ->setReadOnly(TRUE)
      ->setCardinality(1)
      ->setDescription(t('The name of the owner account.'))
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'entity_reference_label',
        'weight' => -3,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ),
        'weight' => -3,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setStorageRequired(TRUE) // @todo still created with NOT NULL
      ->setSetting('required', TRUE)
      ->setRequired(TRUE)
      ->setLabel(t('Created'))
      ->setTranslatable(FALSE)
      ->setDescription(t('The time that the entity was created.'));

    // Standard field, used as unique if primary index.
    $fields['reason'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Reason'))
      ->setRequired(FALSE)
      ->setTranslatable(FALSE)
      ->setDescription(t('Time entry reason'));

    // Who edited this time record.
    // Entity reference field, holds the reference to the user object.
    // The view shows the user name field of the user.
    // The form presents a auto complete field for the user name.
    $fields['changed_uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Editor'))
      ->setDescription(t('The name of the owner account.'))
      ->setCardinality(1)
      ->setReadOnly(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'entity_reference_label',
        'weight' => -3,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ),
        'weight' => -3,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setStorageRequired(TRUE) // @todo still created with NOT NULL
      ->setLabel(t('Updated on'))
      ->setRequired(TRUE)
      ->setTranslatable(FALSE)
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }


  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('uid')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('uid')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('uid', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('uid', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getChangedTimeAcrossTranslations()  {
    $changed = $this->getUntranslated()->getChangedTime();
    foreach ($this->getTranslationLanguages(FALSE) as $language)    {
      $translation_changed = $this->getTranslation($language->getId())->getChangedTime();
      $changed = max($translation_changed, $changed);
    }
    return $changed;
  }

  /**
   * Gets the timestamp of the last entity change for the current translation.
   *
   * @return int
   *   The timestamp of the last entity save operation.
   */
  public function getChangedTime() {
    return $this->get('changed')->value;
  }

  /**
   * Sets the timestamp of the last entity change for the current translation.
   *
   * @param int $timestamp
   *   The timestamp of the last entity save operation.
   *
   * @return $this
   */
  public function setChangedTime($timestamp) {
    $this->set('changed', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   *
   * When a new entity instance is added, set the user_id entity reference to
   * the current user as the creator of the instance.
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += array(
      'uid' => \Drupal::currentUser()->id(),
      'changed_uid' => \Drupal::currentUser()->id(),
    );
  }
}