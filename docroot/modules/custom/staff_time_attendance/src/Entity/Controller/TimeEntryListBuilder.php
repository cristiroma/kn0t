<?php


namespace Drupal\staff_time_attendance\Entity\Controller;


use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

class TimeEntryListBuilder extends EntityListBuilder {


  /**
   * {@inheritdoc}
   *
   * We override ::render() so that we can add our own content above the table.
   * parent::render() is where EntityListBuilder creates the table using our
   * buildHeader() and buildRow() implementations.
   */
  public function render() {
    $build = [];
//    $build['description'] = [
//      '#markup' => $this->t('Content Entity Example implements a Contacts model. These contacts are fieldable entities. You can manage the fields on the <a href="@adminlink">Contacts admin page</a>.', array(
//        '@adminlink' => \Drupal::urlGenerator()
//          ->generateFromRoute('content_entity_example.contact_settings'),
//      )),
//    ];
//
    $build += parent::render();
    return $build;
  }


  /**
   * {@inheritdoc}
   *
   * Building the header and content lines for the contact list.
   *
   * Calling the parent::buildHeader() adds a column for the possible actions
   * and inserts the 'edit' and 'delete' links as defined for the entity type.
   */
  public function buildHeader() {
    $header['id'] = $this->t('Employee');
    $header['name'] = $this->t('Name');
    $header['created'] = $this->t('Date');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\staff_time_attendance\Entity\TimeEntry */
    $row['id'] = $entity->id();
    $row['name'] = $entity->uid->entity->label();
    $row['created'] = \Drupal::service('date.formatter')->format($entity->created->value, 'european_date');
    return $row + parent::buildRow($entity);
  }
}