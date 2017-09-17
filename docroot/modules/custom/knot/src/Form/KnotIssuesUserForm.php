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
    $form['refresh'] = [
      '#type' => 'actions',
      'refresh' => [
        '#type' => 'submit',
        '#value' => t('Refresh'),
        '#suffix' => '<div id="issues-table"></div>',
        '#ajax' => [
          'callback' => '::renderIssuesTable',
          'wrapper' => 'issues-table',
          'method' => 'replace',
          'effect' => 'fade',
        ],
      ]
    ];
    if ($issues = $this->loadIssuesCache()) {
      $form['issues-table'] = $this->buildIssuesTable($issues);
      $form['issues-table']['#weight'] = 100;
    }
    return $form;
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


  public function loadIssuesCache($useCache = TRUE) {
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
    return $ret;
  }


  public function buildIssuesTable(array $issues) {
    $ret = [
      '#type' => 'table',
      '#header' => ['System', '#', 'Title', 'Status', 'Priority'],
      '#rows' => [],
    ];
    /** @var \Drupal\issue_tracker_api\IssueInterface $issue */
    foreach($issues as $idx => $issue) {
      $row = [];
      $row[] = $issue->getTrackerName();
      $row[] = Link::fromTextAndUrl(
        $issue->getId(),
        Url::fromUri($issue->getUrl(), ['#attributes' => ['target' => '_blank']])
      );
      $row[] = Link::fromTextAndUrl(
        $issue->getTitle(),
        Url::fromUri($issue->getUrl(), ['#attributes' => ['target' => '_blank']])
      );
      $row[] = $issue->getStatusName();
      $row[] = $issue->getPriorityName();
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