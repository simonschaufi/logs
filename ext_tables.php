<?php

/**
 * @noinspection PhpMissingStrictTypesDeclarationInspection
 */

use CoStack\Logs\Controller\DeprecationController;
use CoStack\Logs\Controller\LogErasingController;
use CoStack\Logs\Controller\LogReadingController;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

(static function () {
    $configuration = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('logs');

    if ($configuration['moduleConfig'] !== 'disable') {
        ExtensionUtility::registerModule(
            'logs',
            $configuration['moduleConfig'],
            'logs',
            '',
            [
                LogReadingController::class => 'filter',
                LogErasingController::class => 'delete,deleteAlike',
                DeprecationController::class => 'filter,delete',
            ],
            [
                'access' => 'user,group',
                'icon' => 'EXT:logs/Resources/Public/Icons/Extension.svg',
                'labels' => 'LLL:EXT:logs/Resources/Private/Language/locallang.module.xlf',
            ]
        );
    }
})();
