<?php

namespace Drupal\staff_time_attendance\Views;

use Drupal\views\EntityViewsData;

class TimeEntryEntityViewsData extends EntityViewsData {

  public function getViewsData() {
    $structure = parent::getViewsData();
    var_dump(array_keys($structure['staff_time_entry']['created']['sort']));
    var_dump($structure['staff_time_entry']['created']['sort']);
    //$structure['staff_time_entry']['created']['sort'] = ['id' => 'numeric'];
    return $structure;
  }
}