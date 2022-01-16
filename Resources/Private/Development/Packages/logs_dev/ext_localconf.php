<?php
/** @noinspection PhpFullyQualifiedNameUsageInspection */

$GLOBALS['TYPO3_CONF_VARS']['LOG']['CoStack']['LogsDev']['writerConfiguration'][\TYPO3\CMS\Core\Log\LogLevel::DEBUG] = [
    \TYPO3\CMS\Core\Log\Writer\DatabaseWriter::class => [
        'logTable' => 'tx_logsdev_log',
    ],
];
