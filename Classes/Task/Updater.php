<?php

namespace VersionCentral\Task;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Scheduler\Task\AbstractTask;

require_once sprintf('%s/Components/HttpRequest.php', dirname(__DIR__));
use VersionCentral\Components\HttpRequest;

class Updater extends AbstractTask
{
  public function execute()
  {
    global $TYPO3_LOADED_EXT;

    $sysExtensions = array_filter(
      $TYPO3_LOADED_EXT,
      function(array $extension) {
        return strtoupper($extension['type']) === 'S';
      }
    );

    /* @var $objectManager TYPO3\CMS\Extbase\Object\ObjectManager */
    $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
    /* @var $listUtility TYPO3\CMS\Extensionmanager\Utility\ListUtility */
    $listUtility = $objectManager->get('TYPO3\CMS\Extensionmanager\Utility\ListUtility');
    $allExtensions = $listUtility->getAvailableAndInstalledExtensionsWithAdditionalInformation();

    $core = $availableExtensions['core'];
    $localExtensions = array_diff_key($allExtensions, $sysExtensions);

    $localExtensions = array_filter($localExtensions, function($extension) {
      return $extension['type'] !== 'System';
    });

    $extensions = array_map(
      function(array $extension, $identifier) {
        return array(
          'identifier' => $identifier,
          'name' => $extension['title'],
          'version' => $extension['version'],
          'active' => array_key_exists('installed', $extension) && $extension['installed']
        );
      },
      array_values($localExtensions),
      array_keys($localExtensions)
    );

    $localConfiguration = include sprintf('%s/../../../../LocalConfiguration.php', __DIR__);

    $data = array(
        'application' => array(
            'identifier' => 'typo3',
            'version' => TYPO3_version
        ),
        'packages' => $extensions,
        'meta' => array(
          'name' => $localConfiguration['SYS']['sitename'],
          'url' => sprintf(
            '%s://%s/%s',
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443 ? 'https' : 'http',
            $_SERVER['HTTP_HOST'],
            ltrim(dirname($_SERVER['PHP_SELF']), '/')
          )
        )
    );

    $request = GeneralUtility::makeInstance(HttpRequest::class)
      ->setMethod(HttpRequest::METHOD_PUT)
      ->setHeader(sprintf('Authorization: Basic %s', base64_encode($this->credentials)))
      ->setHeader('Content-Type: application/json')
      ->setBody(json_encode($data));

    $response = $request->send();
    if (intval($response->getStatus()/100) !== 2) {
      throw new DomainException(
        'Ein Fehler ist bei der Übertragung an VersionCentral aufgetreten. Bitte setzen Sie sich mit uns '.
        'in Verbindung für weitere Informationen und Unterstützung.'
      );
    }

    return true;
  }
}
