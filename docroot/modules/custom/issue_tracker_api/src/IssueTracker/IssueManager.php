<?php

namespace Drupal\issue_tracker_api\IssueTracker;


use Drupal\issue_tracker_api\impl\RedmineIssueTracker;
use Drupal\user\Entity\User;

class IssueManager {

  public static function loadUserIssues(User $user) {
    $ret = [];
    for($i = 0; $i < count($user->field_issue_trackers); $i++) {
      $value = $user->field_issue_trackers[$i]->entity;
      $system_name = $value->field_issue_tracker_type->entity->getName();
      if(strtolower($system_name) == 'redmine') {
        $tracker_name = $value->field_name->value;
        $endpoint_url = $value->field_api_endpoint->getValue()[0]['uri'];
        $api_key = $value->field_api_key->value;
        $issue_pattern_url = $endpoint_url . '/issues/%s';
        $tracker = new RedmineIssueTracker(
          $tracker_name,
          $endpoint_url,
          $api_key,
          $issue_pattern_url
        );
        $issues = $tracker->getMyIssues();
        $ret = array_merge($ret, $issues);
      }
    }
    return $ret;
  }


}