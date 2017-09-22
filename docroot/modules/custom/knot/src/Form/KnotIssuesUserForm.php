<?php

namespace Drupal\knot\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\issue_tracker_api\IssueTracker\IssueManager;
use Drupal\user\Entity\User;

/**
 * Class MyModuleUserSettingsForm.
 *
 * @package Drupal\mymodule\Form
 */
class KnotIssuesUserForm extends FormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'knot_issues_user';
  }


  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $filters = [
      'tracker' => $form_state->getValue('tracker'),
      'project' => $form_state->getValue('project'),
    ];
    $form['actions'] = [
      '#type' => 'actions',
      'refresh' => [
        '#type' => 'button',
        '#value' => t('Reload'),
        '#attributes' => ['title' => $this->t('Force reload data from issue trackers')],
        '#weight' => 100,
      ],
    ];
    if ($issues = $this->loadIssuesCache($filters)) {
      $form['filters'] = $this->renderFilters($issues);
      $form['issues-table'] = $this->buildIssuesTable($issues);
      $form['issues-table']['#weight'] = 100;
      $form['actions']['submit'] = [
        '#type' => 'button',
        '#value' => $this->t('Filter'),
      ];
      $user = \Drupal::currentUser();
      $form['actions']['reset'] = [
        '#type' => 'link',
        '#title' => $this->t('Reset'),
        '#url' => Url::fromRoute(
          'knot.issue_tracker_list',
          ['user' => $user->id()]
        ),
        '#attributes' => ['class' => ['btn', 'btn-default']],
        '#weight' => 150,
      ];
    }
    return $form;
  }


  public function renderFilters($issues) {
    $ret = [];
    $trackers = [$this->t('-- Select --')];
    $projects = [$this->t('-- Select --')];
    /** @var \Drupal\issue_tracker_api\IssueInterface $issue */
    foreach ($issues as $issue) {
      $trackers[$issue->getTrackerName()] = $issue->getTrackerName();
      $projects[$issue->getProjectName()] = $issue->getProjectName();
    }

    $trackers = array_unique($trackers);
    $projects = array_unique($projects);
    $ret['tracker'] = [
      '#title' => $this->t('Tracker'),
      '#type' => 'select',
      '#options' => $trackers
    ];
    $ret['project'] = [
      '#title' => $this->t('Projects'),
      '#type' => 'select',
      '#options' => $projects
    ];
    return $ret;
  }


  public function renderIssuesTable(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $issues = $this->loadIssuesCache(FALSE);
    $table = $this->buildIssuesTable($issues);
    $response->addCommand(new ReplaceCommand(
      '#edit-issues-table',
      $table)
    );
    return $response;
  }


  public function loadIssuesCache($filters = [], $useCache = TRUE) {
    $ret = NULL;
    $cache = \Drupal::cache()->get(__CLASS__);
    if (empty($cache) || !$useCache) {
      $u = \Drupal::currentUser();
      $user = User::load($u->id());
      $ret = IssueManager::loadUserIssues($user);
      $expire = time() + (24 * 3600);
      // @todo Enhance caching
      \Drupal::cache()->set(__CLASS__, $ret, $expire);
    }
    else {
      $ret = $cache->data;
    }

    $filtered = [];
    // Apply filters
    /** @var \Drupal\issue_tracker_api\IssueInterface $issue */
    foreach($ret as $issue) {
      if (!empty($filters['tracker']) && $issue->getTrackerName() != $filters['tracker']) {
        continue;
      }
      if (!empty($filters['project']) && $issue->getProjectName() != $filters['project']) {
        continue;
      }
      $filtered[] = $issue;
    }
    return $filtered;
  }


  public function buildIssuesTable(array $issues) {
    $ret = [
      '#type' => 'table',
      '#header' => ['Tracker', 'Project', 'Status', 'Priority', '#', 'Title'],
      '#rows' => [],
    ];
    /** @var \Drupal\issue_tracker_api\IssueInterface $issue */
    foreach($issues as $idx => $issue) {
      $row = [];
      $row[] = $issue->getTrackerName();
      $row[] = $issue->getProjectName();
      $row[] = $issue->getStatusName();
      $row[] = $issue->getPriorityName();
      $row[] = Link::fromTextAndUrl(
        $issue->getId(),
        Url::fromUri($issue->getUrl(), ['#attributes' => ['target' => '_blank']])
      );
      $row[] = Link::fromTextAndUrl(
        $issue->getTitle(),
        Url::fromUri($issue->getUrl(), ['#attributes' => ['target' => '_blank']])
      );
      $ret['#rows'][] = $row;
    }
    return $ret;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {  }

}