<?php

namespace Drupal\staff_time_attendance\Form;


use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\staff_time_attendance\Entity\TimeEntry;
use Drupal\user\Entity\User;

class TimeEntriesUserForm extends FormBase {


  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'timeentries_user_form';
  }

  /**
   * Form constructor.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The form structure.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['punch'] = [
      '#type' => 'submit',
      '#value' => $this->t('Punch!'),
      '#attributes' => ['class' => ['btn', 'btn-primary']]
    ];
    $form['entries'] = $this->buildTable();
    return $form;
  }


  public function buildTable() {
    $header = array(
      // We make it sortable by name.
      array('#'),
      array('data' => $this->t('Entry'), 'field' => 'created', 'sort' => 'desc'),
    );

    $db = \Drupal::database();
    $query = $db->select('staff_time_entry','a');
    $query->fields('a', array('created'));
    $query->condition('created', strtotime('today midnight'), '>=');

    $table_sort = $query->extend('Drupal\Core\Database\Query\TableSortExtender')
      ->orderByHeader($header);
    // Limit the rows to 20 for each page.
    $pager = $table_sort->extend('Drupal\Core\Database\Query\PagerSelectExtender')
      ->limit(50);
    $result = $pager->execute();

    // Populate the rows.
    $rows = array();
    $i = 0;

    $u = \Drupal::currentUser();
    $user = User::load($u->id());
    foreach($result as $row) {
      $rows[] = [
        ++$i,
        \Drupal::service('date.formatter')->format($row->created, 'eu_date_time', '', $user->getTimeZone())
      ];
    }

    // The table description.
    $build = [];

    // Generate the table.
    $build['table'] = [
      '#theme' => 'table',
      '#caption' => $this->t('Today'),
      '#header' => $header,
      '#rows' => $rows,
    ];

    // Finally add the pager.
    $build['pager'] = [
      '#type' => 'pager'
    ];

    return $build;
  }


  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $entry = TimeEntry::create([]);
    $entry->save();
    drupal_set_message($this->t('Time entry successfully created')) ;
  }
}