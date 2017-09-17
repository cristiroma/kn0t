<?php

namespace Drupal\issue_tracker_api\impl;

use Drupal\Core\Url;
use Drupal\issue_tracker_api\AbstractIssueTracker;
use Symfony\Component\HttpFoundation\Request;

class RedmineIssueTracker extends AbstractIssueTracker {


  public function getMyIssues() {
    $ret = [];
    // TODO: Implement getMyIssues() method.
    $url = $this->url('/issues.json', ['assigned_to_id' => 'me']);
    if ($payload = $this->call($url)) {
      $json = \GuzzleHttp\json_decode($payload, TRUE);
      foreach($json['issues'] as $arr) {
        $issue = new Issue();
        $id = $arr['id'];
        $issue->setId($id);
        $issue->setTitle($arr['subject']);
        $issue->setDescription($arr['description']);
        $issue->setPriorityName($arr['priority']['name']);
        $issue->setStatusName($arr['status']['name']);
        $issue->setStartDate($arr['start_date']); // @todo parse date
        $issue->setProjectName($arr['project']['name']);
        $issue->setAuthorName($arr['author']['name']);
        $issue->setTypeName($arr['tracker']['name']);
        $issue->setTrackerName($this->getTrackerName());
        $url = sprintf($this->issue_pattern_url, $id);
        $issue->setUrl($url);
        $ret[] = $issue;
      }
    }
    return $ret;
  }


  protected function url($endpoint, $params = []) {
    $url = sprintf(
      '%s%s?key=%s',
      $this->api_endpoint_url,
      $endpoint,
      $this->api_key
    );
    if (!empty($params)) {
      foreach($params as $k => $v) {
        $url .= sprintf('&%s=%s', urlencode($k), urlencode($v));
      }
    }
    return $url;
  }

  protected function call($url) {
    /*
    $client = \Drupal::httpClient();
    $response = $client->request('GET', $url);
    // @todo handle errors, response codes etc.
    $body = $response->getBody();
    return $body->getContents();
    */
    $content = file_get_contents($url);
    return $content;
  }

}