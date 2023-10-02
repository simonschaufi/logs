<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'co-stack.com logs-dev',
    'description' => 'Development extension to create log entries in the development environment',
    'category' => 'misc',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'author' => 'Oliver Eglseder',
    'author_email' => 'oliver.eglseder@co-stack.com',
    'author_company' => 'co-stack.com',
    'version' => '2.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-12.4.99',
            'logs' => '*'
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
