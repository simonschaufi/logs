<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

return [
    'tx-logs-dev-module' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:logs_dev/Resources/Public/Icons/Extension.svg',
    ],
];
