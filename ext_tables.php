<?php
/** @noinspection PhpFullyQualifiedNameUsageInspection */

(static function () {
    $configuration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class
    )->get('logs');

    if ($configuration['moduleConfig'] !== 'disable') {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
            'logs',
            $configuration['moduleConfig'],
            'logs',
            '',
            [
                \CoStack\Logs\Controller\LogReadingController::class => 'filter',
                \CoStack\Logs\Controller\LogErasingController::class => 'delete,deleteAlike',
                \CoStack\Logs\Controller\DeprecationController::class => 'filter,delete',
            ],
            [
                'access' => 'user,group',
                'icon' => 'EXT:logs/Resources/Public/Icons/Extension.svg',
                'labels' => 'LLL:EXT:logs/Resources/Private/Language/locallang.module.xlf',
            ]
        );
    }
})();
