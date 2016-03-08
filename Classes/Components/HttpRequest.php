<?php

namespace VersionCentral\Components;

use TYPO3\CMS\Core\Http\HttpRequest as BaseHttpRequest;

class HttpRequest extends BaseHttpRequest
{
  CONST API_ENDPOINT = 'https://data.versioncentral.com';

  public function __construct($url = null, $method = self::METHOD_GET, array $config = array())
  {
    parent::__construct($url ?: self::API_ENDPOINT, $method, $config);
    $this->setHeader('Accept: application/vnd.version-central-v1+json');
  }
}