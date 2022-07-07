<?php
(static function () {
    $configuration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class
    )->get('logs');

    if (empty($configuration['moduleConfig'])) {
        $configuration['moduleConfig'] = 'tools';
    }

    if ($configuration['moduleConfig'] !== 'disable') {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
            'Logs',
            $configuration['moduleConfig'],
            'logs',
            '',
            [
                \CoStack\Logs\Controller\LogController::class => 'filter,delete,deleteAlike',
                \CoStack\Logs\Controller\DeprecationController::class => 'filter,delete'
            ],
            [
                'access' => 'user,group',
                'icon' => 'EXT:logs/Resources/Public/Icons/Extension.svg',
                'labels' => 'LLL:EXT:logs/Resources/Private/Language/locallang.module.xlf',
            ]
        );
    }
})();
