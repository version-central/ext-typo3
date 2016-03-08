<?php

namespace VersionCentral\Components;

use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Scheduler\AdditionalFieldProviderInterface;
use TYPO3\CMS\Scheduler\Controller\SchedulerModuleController;
use TYPO3\CMS\Scheduler\Task\AbstractTask;

require_once sprintf('%s/HttpRequest.php', __DIR__);

class FieldProvider implements AdditionalFieldProviderInterface
{
  protected $extKey = 'versioncentral';
  protected $taskKey = 'updater';

  /**
   * Gets additional fields to render in the form to add/edit a task
   *
   * @param array $taskInfo Values of the fields from the add/edit task form
   * @param \TYPO3\CMS\Scheduler\Task\AbstractTask $task The task object being edited. Null when adding a task!
   * @param \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $parentObject Reference to the scheduler backend module
   * 
   * @return array A two dimensional array, array('Identifier' => array('fieldId' => array('code' => '', 'label' => '', 'cshKey' => '', 'cshLabel' => ''))
   */
  public function getAdditionalFields(array &$taskInfo, $task, SchedulerModuleController $parentObject)
  {
    $additionalFields = array();
    $editEntry = $parentObject->CMD === 'edit';

    // --

    if (empty($taskInfo[$this->extKey][$this->taskKey]['credentials'])) {
      if ($editEntry) {
        $taskInfo[$this->extKey][$this->taskKey]['credentials'] = $task->credentials;
      } else {
        $taskInfo[$this->extKey][$this->taskKey]['credentials'] = '';
      }
    }
    $fieldIdentifier = sprintf('task_%s_%s_%s', $this->extKey, $this->taskKey, 'credentials');
    $additionalFields[$fieldIdentifier] = array(
      'code' => sprintf(
        '<input class="form-control" name="%s" value="%s" size="50" />',
        sprintf('tx_scheduler[%s][%s][%s]', $this->extKey, $this->taskKey, 'credentials'),
        $taskInfo[$this->extKey][$this->taskKey]['credentials']
      ),
      'label' => 'Credentials'
    );

    // --

    return $additionalFields;
  }

  /**
   * Validates the additional fields' values
   *
   * @param array $submittedData An array containing the data submitted by the add/edit task form
   * @param \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $parentObject Reference to the scheduler backend module
   * 
   * @return boolean TRUE if validation was ok (or selected class is not relevant), FALSE otherwise
   */
  public function validateAdditionalFields(array &$submittedData, SchedulerModuleController $parentObject)
  {
    $request = GeneralUtility::makeInstance(HttpRequest::class)
      ->setMethod(HttpRequest::METHOD_HEAD)
      ->setHeader(
        sprintf(
          'Authorization: Basic %s',
          base64_encode($submittedData[$this->extKey][$this->taskKey]['credentials'])
        )
      );

    $response = $request->send();

    if (intval($response->getStatus()/100) !== 2) {
      $submittedData[$this->extKey][$this->taskKey]['credentials'] = '';

      $messageOut = GeneralUtility::makeInstance(
        FlashMessage::class,
        'Bitte überprüfe deine Credentials und speichere diesen Task anschließend mit gültigen Credentials erneut.',
        'Credentials ungültig',
        FlashMessage::ERROR,
        FALSE
      );

      GeneralUtility::makeInstance(FlashMessageService::class)
        ->getMessageQueueByIdentifier()
        ->addMessage($messageOut);

      return false;
    }

    return true;
  }

  /**
   * Takes care of saving the additional fields' values in the task's object
   *
   * @param array $submittedData An array containing the data submitted by the add/edit task form
   * @param \TYPO3\CMS\Scheduler\Task\AbstractTask $task Reference to the scheduler backend module
   * 
   * @return void
   */
  public function saveAdditionalFields(array $submittedData, AbstractTask $task)
  {
    $task->credentials = $submittedData[$this->extKey][$this->taskKey]['credentials'];
  }
}