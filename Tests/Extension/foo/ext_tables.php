<?php
/** @noinspection PhpFullyQualifiedNameUsageInspection */

(static function () {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'foo',
        'tools',
        'foo',
        '',
        [
            \CoStack\Foo\Controller\ModuleController::class => 'index, create',
        ],
        [
            'access' => 'user,group',
            'icon' => 'EXT:foo/Resources/Public/Icons/Extension.svg',
            'labels' => 'LLL:EXT:foo/Resources/Private/Language/locallang.module.xlf',
        ]
    );
})();
