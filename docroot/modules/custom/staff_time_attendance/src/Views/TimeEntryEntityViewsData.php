<?php

namespace Drupal\staff_time_attendance\Views;

use Drupal\views\EntityViewsData;

class TimeEntryEntityViewsData extends EntityViewsData {

  public function getViewsData() {
    $structure = parent::getViewsData();
    //$structure['staff_time_entry']['created']['sort'] = ['id' => 'numeric'];
    return $structure;
  }
}