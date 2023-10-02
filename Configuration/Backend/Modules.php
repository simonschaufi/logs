<?php

declare(strict_types=1);

use CoStack\Logs\Controller\DeprecationController;
use CoStack\Logs\Controller\LogErasingController;
use CoStack\Logs\Controller\LogReadingController;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

$configuration = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('logs');

if ($configuration['moduleConfig'] === 'disable') {
    return [];
}

return [
    'tx-logs' => [
        'parent' => $configuration['moduleConfig'],
        'position' => [],
        'access' => 'user,group',
        'workspaces' => 'live',
        'path' => '/module/tx-logs',
        'labels' => 'LLL:EXT:logs/Resources/Private/Language/locallang.module.xlf',
        'extensionName' => 'logs',
        'iconIdentifier' => 'tx-logs-module',
        'controllerActions' => [
            LogReadingController::class => [
                'filter'
            ],
            LogErasingController::class => [
                'delete',
                'deleteAlike'
            ],
            DeprecationController::class => [
                'filter',
                'delete'
            ],
        ],
    ],
];
