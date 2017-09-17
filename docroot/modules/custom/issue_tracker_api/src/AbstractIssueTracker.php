<?php

namespace Drupal\issue_tracker_api;


abstract class AbstractIssueTracker implements IssueTrackerInterface {

  protected $tracker_name;
  protected $api_endpoint_url;
  protected $api_key;
  protected $issue_pattern_url;

  public function __construct($tracker_name, $api_endpoint_url, $api_key, $issue_pattern_url) {
    $this->tracker_name= $tracker_name;
    // @todo handle URL ending in /
    $this->api_endpoint_url = $api_endpoint_url;
    $this->api_key = $api_key;
    $this->issue_pattern_url = $issue_pattern_url;
  }


  public function getTrackerName() {
    return $this->tracker_name;
  }


  public function getAPIEndpointURL() {
    return $this->api_endpoint_url;
  }


  public function getAPIKey() {
    return $this->api_key;
  }

  public function getIssuePatternURL() {
    return $this->issue_pattern_url;
  }
}
