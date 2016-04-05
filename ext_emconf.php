<?php

$EM_CONF[$_EXTKEY] = array(
  'title' => 'VersionCentral Updater',
  'description' => '...',
  'category' => 'plugin',
  'version' => '0.0.1',
  'state' => 'experimental',
  'uploadfolder' => false,
  'createDirs' => '',
  'clearcacheonload' => true,
  'author' => 'Stefan Matheis',
  'author_email' => 'support@versioncentral.com',
  'author_company' => 'k10r GmbH',
  'constraints' => array(
    'depends' => array(
      'typo3' => '6.2.0-7.9.99',
      'scheduler' => '6.2.0-7.9.99'
    ),
    'conflicts' =>  array(),
    'suggests' =>  array()
  ),
  'autoload' => array(
      'psr-4' =>
          array(
              'VersionCentral\\' => 'Classes',
          ),
  ),
);
