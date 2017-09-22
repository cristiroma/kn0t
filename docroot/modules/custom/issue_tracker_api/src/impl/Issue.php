<?php

namespace Drupal\issue_tracker_api\impl;


use Drupal\issue_tracker_api\IssueInterface;

class Issue implements IssueInterface {

  private $trackerName;
  private $id;
  private $url;
  private $title;
  private $description;
  private $priorityName;
  private $statusName;
  private $startDate;
  private $projectName;
  private $authorName;
  private $typeName;
  private $assigneeName;

  /**
   * @param $priorityName
   *  Priority label
   *
   * @return integer
   *  Priority index 0 (normal), 1 (high) or 2 (critical)
   */
  public static function getPriorityIndex($priorityName) {
    switch(strtolower($priorityName)) {
      case 'high':
        return 1;
      case 'critical':
      case 'immediate':
        return 2;
      default:
        return 0;
    }
  }

  /**
   * @param mixed $trackerName
   */
  public function setTrackerName($trackerName) {
    $this->trackerName = $trackerName;
  }

  /**
   * @param mixed $id
   */
  public function setId($id) {
    $this->id = $id;
  }

  /**
   * @param mixed $url
   */
  public function setUrl($url) {
    $this->url = $url;
  }

  /**
   * @param mixed $title
   */
  public function setTitle($title) {
    $this->title = $title;
  }

  /**
   * @param mixed $description
   */
  public function setDescription($description) {
    $this->description = $description;
  }

  /**
   * @param mixed $priorityName
   */
  public function setPriorityName($priorityName) {
    $this->priorityName = $priorityName;
  }

  /**
   * @param mixed $statusName
   */
  public function setStatusName($statusName) {
    $this->statusName = $statusName;
  }

  /**
   * @param mixed $startDate
   */
  public function setStartDate($startDate) {
    $this->startDate = $startDate;
  }

  /**
   * @param mixed $projectName
   */
  public function setProjectName($projectName) {
    $this->projectName = $projectName;
  }

  /**
   * @param mixed $authorName
   */
  public function setAuthorName($authorName) {
    $this->authorName = $authorName;
  }

  /**
   * @param mixed $typeName
   */
  public function setTypeName($typeName) {
    $this->typeName = $typeName;
  }

  /**
   * @param mixed $type
   */
  public function setAssigneeName($assigneeName) {
    $this->assigneeName = $assigneeName;
  }


  /**
   * @return mixed
   */
  public function getTrackerName() {
    return $this->trackerName;
  }

  function getId() {
    return $this->id;
  }

  function getUrl() {
    return $this->url;
  }

  function getTitle() {
    return $this->title;
  }

  function getDescription() {
    return $this->description;
  }

  function getPriorityName() {
    return $this->priorityName;
  }

  function getStatusName() {
    return $this->statusName;
  }

  function getStartDate() {
    return $this->startDate;
  }

  function getProjectName() {
    return $this->projectName;
  }

  function getAuthorName() {
    return $this->authorName;
  }

  function getTypeName() {
    return $this->typeName;
  }

  function getAssigneeName() {
    return $this->assigneeName;
  }
}