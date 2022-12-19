<?php

/**
 * @noinspection PhpMissingStrictTypesDeclarationInspection
 */

use Psr\Log\LogLevel;
use TYPO3\CMS\Core\Log\Writer\DatabaseWriter;

$GLOBALS['TYPO3_CONF_VARS']['LOG']['CoStack']['LogsDev']['writerConfiguration'][LogLevel::DEBUG] = [
    DatabaseWriter::class => [
        'logTable' => 'tx_logsdev_log',
    ],
];
