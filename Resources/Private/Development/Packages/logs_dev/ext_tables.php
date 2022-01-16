<?php
/** @noinspection PhpFullyQualifiedNameUsageInspection */

(static function () {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'logs_dev',
        'tools',
        'logs',
        'after:LogsLogs',
        [
            \CoStack\LogsDev\Controller\ModuleController::class => 'index, create',
        ],
        [
            'access' => 'user,group',
            'icon' => 'EXT:logs_dev/Resources/Public/Icons/Extension.svg',
            'labels' => 'LLL:EXT:logs_dev/Resources/Private/Language/locallang.module.xlf',
        ]
    );
})();
