<?php
/** @noinspection PhpFullyQualifiedNameUsageInspection */

$GLOBALS['TYPO3_CONF_VARS']['LOG']['CoStack']['Foo']['writerConfiguration'][\TYPO3\CMS\Core\Log\LogLevel::DEBUG] = [
    \TYPO3\CMS\Core\Log\Writer\DatabaseWriter::class => [
        'logTable' => 'tx_foo_log',
    ],
];
