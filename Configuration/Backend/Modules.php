<?php


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
    'web_examples' => [
        'parent' => $configuration['moduleConfig'],
        'position' => [],
        'access' => 'user,group',
        'workspaces' => 'live',
        'path' => '/module/page/example',
        'labels' => 'LLL:EXT:logs/Resources/Private/Language/locallang.module.xlf',
        'extensionName' => 'logs',
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
