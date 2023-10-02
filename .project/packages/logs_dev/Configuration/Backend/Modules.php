<?php

declare(strict_types=1);

use CoStack\LogsDev\Controller\ModuleController;

return [
    'tx-logs_dev' => [
        'parent' => 'tools',
        'position' => ['after' => 'tx-logs'],
        'access' => 'user,group',
        'workspaces' => 'live',
        'path' => '/module/tx-logs-dev',
        'labels' => 'LLL:EXT:logs_dev/Resources/Private/Language/locallang.module.xlf',
        'extensionName' => 'logs_dev',
        'iconIdentifier' => 'tx-logs-dev-module',
        'controllerActions' => [
            ModuleController::class => [
                'index',
                'createLogs',
                'createDeprecations'
            ],
        ],
    ],
];
