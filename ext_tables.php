<?php
/** @noinspection PhpFullyQualifiedNameUsageInspection */

(static function () {
    $configuration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class
    )->get('logs');

    if ($configuration['moduleConfig'] !== 'disable') {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
            'CoStack.Logs',
            $configuration['moduleConfig'],
            'logs',
            '',
            [
                'Log' => 'filter,delete,deleteAlike',
                'Deprecation' => 'filter,delete',
            ],
            [
                'access' => 'user,group',
                'icon' => 'EXT:logs/Resources/Public/Icons/Extension.svg',
                'labels' => 'LLL:EXT:logs/Resources/Private/Language/locallang.module.xlf',
            ]
        );
    }
})();
