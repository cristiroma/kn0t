<?php

namespace Drupal\issue_tracker_api;


interface IssueInterface {

  function getTrackerName();

  function getId();

  function getUrl();

  function getTitle();

  function getDescription();

  function getPriorityName();

  function getStatusName();

  function getStartDate();

  function getProjectName();

  function getAuthorName();

  function getTypeName();

  function getAssigneeName();
}