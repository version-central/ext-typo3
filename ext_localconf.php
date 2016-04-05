<?php

if (!defined ('TYPO3_MODE'))
  die ('Access denied.');

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['VersionCentral\Task\Updater'] = array(
  'extension' => $_EXTKEY,
  'title' => 'VersionCentral Updater',
  'description' => 'Dieser Task übermittelt die Versionen aller installierten Plugins an VersionCentral',
  'additionalFields' => 'VersionCentral\Components\FieldProvider'
);
